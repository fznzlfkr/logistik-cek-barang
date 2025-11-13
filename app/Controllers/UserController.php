<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use App\Models\NotifikasiModel;
use CodeIgniter\HTTP\ResponseInterface;
use Endroid\QrCode\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\LogAktivitasModel;

class UserController extends BaseController
{
    protected $userModel,
        $barangModel,
        $laporanModel,
        $logAktivitasModel,
        $notifikasiModel;

    public function __construct()
    {
        $this->userModel = new UserModel;
        $this->barangModel = new BarangModel;
        $this->laporanModel = new LaporanModel;
        $this->logAktivitasModel = new LogAktivitasModel;
        $this->notifikasiModel = new NotifikasiModel;
    }

    public function index()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $totalMasuk = $this->laporanModel->where('jenis', 'Masuk')->countAllResults();
        $totalDipakai = $this->laporanModel->where('jenis', 'Dipakai')->countAllResults();
        $totalBarang = $this->barangModel->selectSum('jumlah')->get()->getRow()->jumlah;
        $barangMinimum = $this->barangModel->where('jumlah <= minimum_stok')->countAllResults();

        $laporanData = $this->laporanModel
            ->select('laporan.tanggal, barang.nama_barang, laporan.jenis, laporan.jumlah')
            ->join('barang', 'barang.id_barang = laporan.id_barang')
            ->orderBy('laporan.tanggal', 'DESC')
            ->limit(10)
            ->findAll();
        $data = [
            'title' => 'Dashboard User - CargoWing',
            'currentPage' => 'dashboard',
            'totalMasuk' => $totalMasuk,
            'totalDipakai' => $totalDipakai,
            'totalBarang' => $totalBarang,
            'barangMinimum' => $barangMinimum,
            'laporanData' => $laporanData,
            'user' => $user,
            'notif' => $this->notifikasiModel->getUnreadNotif(5)
        ];

        return view('user/dashboard', $data);
    }

    public function kelolaBarang()
    {
        $dataUser = session()->get('id_user');
        $user     = $this->userModel->find($dataUser);

        // Ambil jumlah per halaman (default 10)
        $perPage  = $this->request->getVar('per_page') ?? 10;
        // Ambil keyword pencarian
        $keyword  = $this->request->getVar('keyword');

        // Query ke tabel barang
        $barangQuery = $this->barangModel;

        if (!empty($keyword)) {
            $barangQuery = $barangQuery->groupStart()
                ->like('nama_barang', $keyword)
                ->orLike('jumlah', $keyword)
                ->orLike('satuan', $keyword)
                ->orLike('barcode', $keyword)
                ->groupEnd();
        }

        // Ambil data barang dengan pagination
        $barangList = $barangQuery
            ->orderBy('barang.id_barang', 'ASC')->orderBy('nama_barang', 'ASC')
            ->paginate($perPage, 'number');

        $uniqueBarang = $this->barangModel
            ->select('id_barang, nama_barang, jumlah, satuan')
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Kelola Barang User - CargoWing',
            'currentPage' => 'kelolabarang',
            'user'       => $user,
            'keyword'    => $keyword,
            'perPage'    => $perPage,
            'barangList' => $barangList,
            'uniqueBarang' => $uniqueBarang,
            'pager'      => $this->barangModel->pager, // Pastikan pager diambil dari model
            'notif' => $this->notifikasiModel->getUnreadNotif(5)
        ];

        return view('user/kelola_barang', $data);
    }

    public function simpanBarang()
    {
        $data = [
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'jumlah'        => $this->request->getPost('jumlah'),
            'satuan'        => $this->request->getPost('satuan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'barcode'       => $this->request->getPost('barcode'),
            'minimum_stok'  => $this->request->getPost('minimum_stok'),
        ];

        $this->barangModel->insert($data);
        return redirect()->back()->with('success', 'Barang berhasil disimpan!');
    }


    public function updateBarang($id_barang)
    {
        $data = [
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'jumlah'        => $this->request->getPost('jumlah'),
            'satuan'        => $this->request->getPost('satuan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'barcode'       => $this->request->getPost('barcode'),
            'minimum_stok'  => $this->request->getPost('minimum_stok'),
        ];

        // Validasi sederhana
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                return redirect()->back()->withInput()->with('error', 'Field ' . $key . ' wajib diisi.');
            }
        }

        // Ambil data lama dari DB
        $barangLama = $this->barangModel->find($id_barang);
        if (!$barangLama) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Cek apakah ada perubahan
        $tidakBerubah = true;
        foreach ($data as $key => $value) {
            if ($barangLama[$key] != $value) {
                $tidakBerubah = false;
                break;
            }
        }

        if ($tidakBerubah) {
            return redirect()->back()->with('error', 'Tidak ada perubahan data.');
        }

        // Update data
        if ($this->barangModel->update($id_barang, $data)) {
            // ✅ Tambahin notif stok minimum di sini
            if ($data['jumlah'] <= $data['minimum_stok']) {
                $this->notifikasiModel->save([
                    'pesan'  => "Stok {$data['nama_barang']} tersisa {$data['jumlah']} (minimum: {$data['minimum_stok']})⚠️",
                    'status' => 'unread'
                ]);
            }

            return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
        } else {
            $error = $this->barangModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui barang. ' . json_encode($error));
        }
    }

    public function hapusBarang($id)
    {
        $this->barangModel->delete($id);
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

    public function scanBarcode($barcode)
    {
        // Cari barang berdasarkan barcode
        $barang = $this->barangModel->where('barcode', $barcode)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Tampilkan halaman informasi barang
        return view('user/informasi_barang', [
            'barang' => $barang
        ]);
    }


    public function informasiBarang($barcode)
    {
        // ambil barang berdasarkan barcode
        $barang = $this->barangModel->where('barcode', $barcode)->first();
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang dengan barcode tersebut tidak ditemukan.');
        }

        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Informasi Barang - CargoWing',
            'currentPage' => 'kelolabarang',
            'user' => $user,
            'barang' => $barang
        ];

        return view('informasi_barang', $data);
    }

    public function downloadBarcode($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Data untuk QR mengarah ke fungsi generate PDF
        $urlPDF = base_url('barang/info/' . $barang['barcode']);
        $fileName = 'barcode_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.png';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($urlPDF)
            ->size(300)
            ->margin(10)
            ->build();

        // Ambil isi biner dari QR
        $imageData = $result->getString();

        // Simpan sementara ke file tmp
        $tempFile = WRITEPATH . 'uploads/' . $fileName;
        file_put_contents($tempFile, $imageData);

        // Kembalikan file untuk diunduh
        return $this->response->download($tempFile, null)->setFileName($fileName);
    }

    public function previewSuratJalan($id)
    {
        $barang = $this->barangModel->find($id);

        if (!$barang || empty($barang['surat_jalan'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Surat jalan tidak ditemukan');
        }

        $filePath = FCPATH . 'uploads/surat_jalan/' . $barang['surat_jalan'];

        if (!is_file($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File surat jalan tidak ditemukan');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Determine MIME type
        $mime = 'application/octet-stream';
        switch ($ext) {
            case 'pdf':
                $mime = 'application/pdf';
                break;
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'webp':
                $mime = 'image/webp';
                break;
            default:
                if (function_exists('mime_content_type')) {
                    $detected = @mime_content_type($filePath);
                    if ($detected) {
                        $mime = $detected;
                    }
                }
                break;
        }

        $contents = file_get_contents($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody($contents);
    }

    public function pdf($barcode)
    {
        $barang = $this->barangModel->where('barcode', $barcode)->first();
        if (!$barang) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barang tidak ditemukan');
        }

        // Buat HTML untuk PDF
        $html = view('barang/pdf_template', ['barang' => $barang]);

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'Detail_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.pdf';
        $dompdf->stream($fileName, ["Attachment" => true]);
    }

    public function riwayat()
    {
        $dataUser = session()->get('id_user');
        $user     = $this->userModel->find($dataUser);

        // Ambil jumlah per halaman (default 10)
        $perPage  = $this->request->getVar('per_page') ?? 10;
        // Ambil keyword pencarian
        $keyword  = $this->request->getVar('keyword');

        // Query dasar untuk riwayat
        $riwayatQuery = $this->laporanModel
            ->select('laporan.tanggal, laporan.jumlah, laporan.jenis, users.nama, barang.nama_barang, laporan.id_laporan')
            ->join('users', 'users.id_user = laporan.id_user')
            ->join('barang', 'barang.id_barang = laporan.id_barang');

        // Filter pencarian kalau ada keyword
        if (!empty($keyword)) {
            $riwayatQuery->groupStart()
                ->like('barang.nama_barang', $keyword)
                ->orLike('users.nama', $keyword)
                ->orLike('laporan.jenis', $keyword)
                ->groupEnd();
        }

        // Ambil data riwayat dengan pagination
        $riwayatData = $riwayatQuery
            ->orderBy('laporan.tanggal', 'ASC')
            ->paginate($perPage, 'number');

        // Ambil semua nama barang unik langsung dari tabel barang
        $uniqueBarang = $this->barangModel
            ->select('nama_barang')
            ->distinct()
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        $data = [
            'title'        => 'Riwayat - CargoWing',
            'currentPage'  => 'riwayat',
            'user'         => $user,
            'riwayatData'  => $riwayatData,
            'pager'        => $this->laporanModel->pager,
            'perPage'      => $perPage,
            'keyword'      => $keyword,
            'uniqueBarang' => $uniqueBarang,
            'notif' => $this->notifikasiModel->getUnreadNotif(5)
        ];

        return view('user/riwayat', $data);
    }

    // Proses input barang masuk
    public function simpanBarangMasuk()
    {
        $db = \Config\Database::connect();

        $data = [
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'jumlah'        => $this->request->getPost('jumlah'),
            'satuan'        => $this->request->getPost('satuan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'barcode'       => $this->request->getPost('barcode'),
            'minimum_stok'  => $this->request->getPost('minimum_stok'),
        ];

        // Validasi field kosong
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return redirect()->back()->with('error', ucfirst(str_replace('_', ' ', $key)) . ' wajib diisi');
            }
        }

        // Ambil dan proses file upload (surat jalan & gambar barang) - referensi dari simpanBarangMasukExisting
        $suratJalanFile   = $this->request->getFile('surat_jalan');
        $gambarBarangFile = $this->request->getFile('gambar_barang');

        $suratJalanName = null;
        $gambarBarangName = null;

        if ($suratJalanFile && $suratJalanFile->isValid() && !$suratJalanFile->hasMoved()) {
            $suratJalanName = $suratJalanFile->getRandomName();
            $suratJalanFile->move(FCPATH . 'uploads/surat_jalan', $suratJalanName);
        }

        if ($gambarBarangFile && $gambarBarangFile->isValid() && !$gambarBarangFile->hasMoved()) {
            $gambarBarangName = $gambarBarangFile->getRandomName();
            $gambarBarangFile->move(FCPATH . 'uploads/gambar_barang', $gambarBarangName);
        }

        // Tambahkan nama file ke data jika ada
        if ($suratJalanName) {
            $data['surat_jalan'] = $suratJalanName;
        }
        if ($gambarBarangName) {
            $data['gambar'] = $gambarBarangName;
        }

        // Simpan barang + catat riwayat dalam transaction
        $db->transStart();

        $insertId = $this->barangModel->insert($data);
        if (!$insertId) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyimpan barang.');
        }

        // Siapkan data riwayat (laporan)
        $nowTime = date('H:i:s');
        $tanggalFull = $data['tanggal_masuk'] . ' ' . $nowTime;

        $riwayat = [
            'id_barang'  => $insertId,
            'jumlah'     => $data['jumlah'],
            'jenis'      => 'Masuk',
            'tanggal'    => $tanggalFull,
            'id_user'    => session()->get('id_user') ?? null,
            'keterangan' => 'Barang masuk (penambahan awal)'
        ];

        $this->laporanModel->insert($riwayat);

        // ✅ Tambahkan log aktivitas
        logAktivitas("Menambahkan barang baru: {$data['nama_barang']} (Jumlah: {$data['jumlah']} {$data['satuan']})");
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi. Coba lagi.');
        }

        return redirect()->to('user/kelola_barang')->with('success', 'Barang berhasil disimpan dan dicatat di riwayat');
    }

    public function simpanBarangMasukExisting()
    {
        $db = \Config\Database::connect();

        $idBarang = $this->request->getPost('id_barang');
        $jumlahMasuk = $this->request->getPost('jumlah');
        $tanggalMasuk = $this->request->getPost('tanggal_masuk');
        $suratJalanFile = $this->request->getFile('surat_jalan');
        $gambarBarangFile = $this->request->getFile('gambar_barang');

        $suratJalanName = null;
        $gambarBarangName = null;

        if ($suratJalanFile && $suratJalanFile->isValid() && !$suratJalanFile->hasMoved()) {
            $suratJalanName = $suratJalanFile->getRandomName();
            $suratJalanFile->move(FCPATH . 'uploads/surat_jalan', $suratJalanName);
        }

        if ($gambarBarangFile && $gambarBarangFile->isValid() && !$gambarBarangFile->hasMoved()) {
            $gambarBarangName = $gambarBarangFile->getRandomName();
            $gambarBarangFile->move(FCPATH . 'uploads/gambar_barang', $gambarBarangName);
        }

        // validasi input
        if (empty($idBarang) || empty($jumlahMasuk) || empty($tanggalMasuk)) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Ambil data barang yang sudah ada
        $barang = $this->barangModel->find($idBarang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
        }

        // Mulai transaksi
        $db->transStart();

        // Update stok barang (tambahkan jumlah masuk ke stok yang ada)
        $stokBaru = $barang['jumlah'] + $jumlahMasuk;
        $updateData = [
            'jumlah' => $stokBaru,
            'tanggal_masuk' => $tanggalMasuk
        ];

        if ($suratJalanName) $updateData['surat_jalan'] = $suratJalanName;
        if ($gambarBarangName) $updateData['gambar'] = $gambarBarangName;

        $this->barangModel->update($idBarang, $updateData);

        // Catat di riwayat (laporan)
        $nowTime = date('H:i:s');
        $tanggalFull = $tanggalMasuk . ' ' . $nowTime;

        $riwayat = [
            'id_barang'  => $idBarang,
            'jumlah'     => $jumlahMasuk,
            'jenis'      => 'Masuk',
            'tanggal'    => $tanggalFull,
            'id_user'    => session()->get('id_user') ?? null,
            'keterangan' => 'Penambahan stok barang'
        ];

        $this->laporanModel->insert($riwayat);

        // Log aktivitas
        logAktivitas("Menambahkan stok barang: {$barang['nama_barang']} (Jumlah: +{$jumlahMasuk} {$barang['satuan']}, Stok sekarang: {$stokBaru})");

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi. Coba lagi.');
        }

        return redirect()->to('user/kelola_barang')->with('success', 'Stok barang berhasil ditambahkan');
    }

    public function saveBarangKeluar()
    {
        $dataUser = session()->get('id_user');
        $idBarang = $this->request->getPost('id_barang');
        $jumlah   = $this->request->getPost('jumlah');
        $tanggal  = $this->request->getPost('tanggal');
        $ket      = $this->request->getPost('keterangan');

        // Validasi sederhana (wajib diisi)
        if (empty($idBarang) || empty($jumlah) || empty($tanggal)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        // Ambil waktu sekarang (Jakarta)
        $now = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $jam = $now->format('H:i:s');

        // Gabungkan jadi datetime
        $tanggalKeluar = $tanggal . ' ' . $jam;

        // Ambil stok barang dulu
        $barang = $this->barangModel->find($idBarang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Cek apakah stok cukup
        if ($barang['jumlah'] < $jumlah) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak mencukupi!');
        }

        // Simpan ke tabel laporan
        $simpanLaporan = $this->laporanModel->save([
            'id_barang'  => $idBarang,
            'jumlah'     => $jumlah,
            'jenis'      => 'Dipakai',
            'tanggal'    => $tanggalKeluar,
            'id_user'    => $dataUser,
            'keterangan' => $ket,
        ]);

        if (!$simpanLaporan) {
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat barang keluar.');
        }

        // Update stok barang (dikurangi)
        $stokBaru = $barang['jumlah'] - $jumlah;
        $updateBarang = $this->barangModel->update($idBarang, [
            'jumlah' => $stokBaru
        ]);

        if (!$updateBarang) {
            $error = $this->barangModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui stok barang. ' . json_encode($error));
        }

        // ✅ Tambahin notif stok minimum
        if ($stokBaru <= $barang['minimum_stok']) {
            $this->notifikasiModel->save([
                'pesan'  => "Stok {$barang['nama_barang']} tersisa {$stokBaru} (minimum: {$barang['minimum_stok']})⚠️",
                'status' => 'unread'
            ]);
        }

        // ✅ Tambahkan log aktivitas
        logAktivitas("Mengeluarkan barang: {$barang['nama_barang']} (Jumlah: {$jumlah} {$barang['satuan']})");

        return redirect()->to('/user/riwayat')
            ->with('success', 'Barang keluar berhasil dicatat & stok terupdate!');
    }

    public function hapusRiwayat($idLaporan)
    {
        // Pastikan ID ada
        if (!$idLaporan) {
            return redirect()->back()->with('error', 'ID laporan tidak valid.');
        }

        // Ambil laporan dengan join ke tabel barang
        $laporan = $this->laporanModel
            ->select('laporan.*, barang.nama_barang, barang.satuan')
            ->join('barang', 'barang.id_barang = laporan.id_barang', 'left')
            ->where('laporan.id_laporan', $idLaporan)
            ->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Data laporan tidak ditemukan.');
        }

        // Hapus data
        if ($this->laporanModel->delete($idLaporan)) {
            logAktivitas(" Menghapus riwayat laporan: {$laporan['nama_barang']} (Jumlah: {$laporan['jumlah']} {$laporan['satuan']})");
            return redirect()->back()->with('success', 'Riwayat berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus riwayat.');
    }


    public function editRiwayat($idLaporan)
    {
        // Ambil nama barang dari input
        $namaBarang = $this->request->getPost('nama_barang');

        // Cari ID barang dari tabel barang
        $barang = $this->barangModel
            ->where('nama_barang', $namaBarang)
            ->first();

        if (!$barang) {
            return redirect()->to(base_url('user/riwayat'))
                ->with('error', 'Barang tidak ditemukan.');
        }

        // Ambil data laporan yang ada di database
        $existingData = $this->laporanModel->find($idLaporan);

        // Data baru yang akan diupdate
        $newData = [
            'tanggal'   => $this->request->getPost('tanggal')
                ?? date('Y-m-d H:i:s', strtotime('+7 hours')),
            'jumlah'    => $this->request->getPost('jumlah'),
            'jenis'     => $this->request->getPost('jenis'),
            'id_barang' => $barang['id_barang'],
            'id_user'   => session()->get('id_user'),
            'nama_user' => session()->get('nama'),
        ];

        // Cek apakah ada perubahan data
        if ($existingData && $newData == $existingData) {
            return redirect()->to(base_url('user/riwayat'))
                ->with('error', 'Tidak ada yang diperbarui.');
        }

        // Update data laporan
        $this->laporanModel->update($idLaporan, $newData);

        // ✅ Tambahkan log aktivitas
        logAktivitas("{$newData['nama_user']} Mengedit riwayat laporan. Barang: {$namaBarang}, Jumlah: {$newData['jumlah']}, Jenis: {$newData['jenis']}");

        return redirect()->to(base_url('user/riwayat'))
            ->with('success', 'Data riwayat berhasil diperbarui.');
    }

    public function printRiwayat($id_laporan)
    {
        $format = $this->request->getPost('format');

        if (!$format) {
            return redirect()->back()->with('error', 'Pilih format print terlebih dahulu.');
        }

        // Ambil data laporan berdasarkan ID
        $riwayatQuery = $this->laporanModel
            ->select('laporan.tanggal, laporan.jumlah, laporan.jenis, users.nama, barang.nama_barang, laporan.id_laporan')
            ->join('users', 'users.id_user = laporan.id_user')
            ->join('barang', 'barang.id_barang = laporan.id_barang');

        $riwayatQuery = $this->laporanModel->find($id_laporan);

        if (!$riwayatQuery) {
            return redirect()->back()->with('error', 'Data laporan tidak ditemukan.');
        }

        // Print Excel
        if ($format === 'excel') {
            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'ID Laporan');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'Nama Barang');
            $sheet->setCellValue('D1', 'Jumlah');
            $sheet->setCellValue('E1', 'Jenis');
            $sheet->setCellValue('F1', 'Staff');

            // Data
            $sheet->setCellValue('A2', $riwayatQuery['id_laporan']);
            $sheet->setCellValue('B2', date('d-m-Y H:i:s', strtotime($riwayatQuery['tanggal'] . ' +7 hours')));
            $sheet->setCellValue('C2', $riwayatQuery['nama_barang']);
            $sheet->setCellValue('D2', $riwayatQuery['jumlah']);
            $sheet->setCellValue('E2', $riwayatQuery['jenis']);
            $sheet->setCellValue('F2', $riwayatQuery['nama']);

            // Output file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'laporan_' . $riwayatQuery['id_laporan'] . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;
        }

        // Print PDF
        if ($format === 'pdf') {
            // Load Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $html = view('user/pdf_template', ['laporan' => $riwayatQuery]);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('laporan_' . $riwayatQuery['id_laporan'] . '.pdf', ["Attachment" => true]);
            exit;
        }

        return redirect()->back()->with('error', 'Format tidak valid.');
    }

    public function profil()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Profil User - CargoWing',
            'user' => $user,
            'currentPage' => 'profil',
            'notif' => $this->notifikasiModel->getUnreadNotif(5)
        ];
        return view('user/profil', $data);
    }

    public function update()
    {
        $userId = session()->get('id_user');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data user tidak ditemukan!');
        }

        $nama  = $this->request->getPost('nama');
        $email = $this->request->getPost('email');

        $dataUpdate = [
            'nama'  => $nama,
            'email' => $email,
        ];

        // Cek perubahan (pakai data lama dari DB sebagai identitas pelaku)
        $pelaku = $user['nama'];

        if ($user['nama'] !== $nama && $user['email'] === $email) {
            logAktivitas("$pelaku mengganti nama dari '{$user['nama']}' menjadi '{$nama}'");
        } elseif ($user['nama'] === $nama && $user['email'] !== $email) {
            logAktivitas("$pelaku mengganti email dari '{$user['email']}' menjadi '{$email}'");
        } elseif ($user['nama'] !== $nama && $user['email'] !== $email) {
            logAktivitas("$pelaku mengganti nama dari '{$user['nama']}' menjadi '{$nama}', dan mengganti email dari '{$user['email']}' menjadi '{$email}'");
        }

        // Update data
        $this->userModel->update($userId, $dataUpdate);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function gantiPassword()
    {
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasi   = $this->request->getPost('konfirmasi_password');

        $user = $userModel->find($userId);

        if (!$user || !password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('errorp', 'Password lama salah.');
        }
        if ($passwordBaru !== $konfirmasi) {
            return redirect()->back()->with('errorp', 'Konfirmasi password tidak sama.');
        }
        if (strlen($passwordBaru) < 8) {
            return redirect()->back()->with('errorp', 'Password baru minimal 8 karakter.');
        }

        $userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);
        logAktivitas("{$user['nama']} mengganti password akun.");

        return redirect()->back()->with('successp', 'Password berhasil diubah.');
    }

    public function readNotif($id)
    {
        $this->notifikasiModel->markAsRead($id);
        return redirect()->back();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
