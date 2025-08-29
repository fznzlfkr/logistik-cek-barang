<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use CodeIgniter\HTTP\ResponseInterface;
use Endroid\QrCode\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class UserController extends BaseController
{
    protected $userModel,
        $barangModel,
        $laporanModel;

    public function __construct()
    {
        $this->userModel = new UserModel;
        $this->barangModel = new BarangModel;
        $this->laporanModel = new LaporanModel;
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
            'totalMasuk' => $totalMasuk,
            'totalDipakai' => $totalDipakai,
            'totalBarang' => $totalBarang,
            'barangMinimum' => $barangMinimum,
            'laporanData' => $laporanData,
            'user' => $user
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
            ->select('id_barang, nama_barang, jumlah')
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Kelola Barang User - CargoWing',
            'user'       => $user,
            'keyword'    => $keyword,
            'perPage'    => $perPage,
            'barangList' => $barangList,
            'uniqueBarang' => $uniqueBarang,
            'pager'      => $this->barangModel->pager // Pastikan pager diambil dari model
        ];

        return view('user/kelola_barang', $data);
    }


    public function tambahBarang()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Tambah Barang User - CargoWing',
            'user' => $user
        ];
        return view('user/tambah_barang', $data);
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

        // Validasi sederhana (pastikan semua field terisi)
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
        // Ambil data barang berdasarkan barcode
        $barang = $this->barangModel->where('barcode', $barcode)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Buat HTML untuk PDF
        $html = view('barang_pdf', ['barang' => $barang]);

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nama file PDF
        $fileName = 'barang_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.pdf';

        // Outputkan PDF untuk download
        $dompdf->stream($fileName, ["Attachment" => true]);
    }

    public function downloadBarcode($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // QR mengarah ke fungsi generate PDF
        $urlPDF = base_url('barang/pdf/' . $barang['barcode']);

        $fileName = 'barcode_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.png';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($urlPDF)
            ->size(300)
            ->margin(10)
            ->build();

        return $this->response
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($result->getString());
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
            'user'         => $user,
            'riwayatData'  => $riwayatData,
            'pager'        => $this->laporanModel->pager,
            'perPage'      => $perPage,
            'keyword'      => $keyword,
            'uniqueBarang' => $uniqueBarang // kirim ke view
        ];

        return view('user/riwayat', $data);
    }

    // Proses input barang masuk
    public function simpanBarangMasuk()
    {
        // Ambil data dari form
        $data = [
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'jumlah'        => $this->request->getPost('jumlah'),
            'satuan'        => $this->request->getPost('satuan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'barcode'       => $this->request->getPost('barcode'),
            'minimum_stok'  => $this->request->getPost('minimum_stok'),
        ];

        // --- Validasi field kosong ---
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return redirect()->back()->with('error', ucfirst(str_replace('_', ' ', $key)) . ' wajib diisi');
            }
        }

        // --- Cek duplikasi nama_barang ---
        $cekBarang = $this->barangModel->where('nama_barang', $data['nama_barang'])->first();
        if ($cekBarang) {
            return redirect()->back()->with('error', 'Barang sudah terdaftar');
        }

        // --- Simpan data ---
        $this->barangModel->insert($data);

        return redirect()->to('user/kelola_barang')->with('success', 'Barang berhasil disimpan');
    }

    public function saveBarangKeluar()
    {
        $dataUser = session()->get('id_user');
        $idBarang = $this->request->getPost('id_barang');
        $jumlah   = (int) $this->request->getPost('jumlah');
        $tanggal  = $this->request->getPost('tanggal');
        $ket      = $this->request->getPost('keterangan');

        // Ambil waktu sekarang (Jakarta)
        $now = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $jam = $now->format('H:i:s');

        // Gabungkan jadi datetime
        $tanggalKeluar = $tanggal . ' ' . $jam; // contoh: 2025-08-21 14:32:10

        // Ambil stok barang dulu
        $barang = $this->barangModel->find($idBarang);

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan!');
        }

        // Cek apakah stok cukup
        if ($barang['jumlah'] < $jumlah) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // Simpan ke tabel laporan
        $this->laporanModel->save([
            'id_barang'  => $idBarang,
            'jumlah'     => $jumlah,
            'jenis'      => 'Dipakai',
            'tanggal'    => $tanggalKeluar,
            'id_user'    => $dataUser,
            'keterangan' => $ket,
        ]);

        // Update stok barang (dikurangi)
        $this->barangModel->update($idBarang, [
            'jumlah' => $barang['jumlah'] - $jumlah
        ]);

        return redirect()->to('/user/riwayat')
            ->with('success', 'Barang keluar berhasil dicatat & stok terupdate!');
    }

    public function hapusRiwayat($idLaporan)
    {
        // Pastikan ID ada
        if (!$idLaporan) {
            return redirect()->back()->with('error', 'ID laporan tidak valid.');
        }

        // Cek apakah datanya ada
        $laporan = $this->laporanModel->find($idLaporan);
        if (!$laporan) {
            return redirect()->back()->with('error', 'Data laporan tidak ditemukan.');
        }

        // Hapus data
        if ($this->laporanModel->delete($idLaporan)) {
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
            'id_user'   => session()->get('id_user')
        ];

        // Cek apakah ada perubahan data
        if ($existingData && $newData == $existingData) {
            return redirect()->to(base_url('user/riwayat'))
                ->with('error', 'Tidak ada yang diperbarui.');
        }

        // Update data laporan
        $this->laporanModel->update($idLaporan, $newData);

        // ✅ Tambahkan log aktivitas
        logAktivitas("Mengedit laporan ID: {$idLaporan}, Barang: {$namaBarang}, Jumlah: {$newData['jumlah']}, Jenis: {$newData['jenis']}");

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
            'title' => 'profil - CargoWing',
            'user' => $user
        ];
        return view('user/profil', $data);
    }

    public function update()
    {
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        // Ambil data dari form
        $nama   = $this->request->getPost('nama');
        $email  = $this->request->getPost('email');
        $no_hp  = $this->request->getPost('no_hp');
        $foto   = $this->request->getFile('foto');

        // Validasi sederhana
        if (empty($nama) || empty($email) || empty($no_hp)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $dataUpdate = [
            'nama'  => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ];

        // Handle upload foto jika ada
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move('uploads', $newName);
            $dataUpdate['foto'] = $newName;
            session()->set('foto', $newName);
        }

        // Update ke database
        $userModel->update($userId, $dataUpdate);

        // Update session
        session()->set([
            'nama'  => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
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

        return redirect()->back()->with('successp', 'Password berhasil diubah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
