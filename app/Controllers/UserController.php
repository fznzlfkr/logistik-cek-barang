<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use CodeIgniter\HTTP\ResponseInterface;

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
        $user = $this->userModel->find($dataUser);

        // Ambil data barang dari database
        $barangList = $this->barangModel->findAll();

        $data = [
            'title' => 'Kelola Barang User - CargoWing',
            'user' => $user,
            'barangList' => $barangList
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

        // Validasi sederhana (pastikan semua field terisi)
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                return redirect()->back()->withInput()->with('error', 'Field ' . $key . ' wajib diisi.');
            }
        }

        // Simpan data
        if ($this->barangModel->insert($data)) {
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
        } else {
            $error = $this->barangModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah barang. ' . json_encode($error));
        }
    }

    public function editBarang($id)
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'nama_barang'   => $this->request->getPost('nama_barang'),
                'jumlah'        => $this->request->getPost('jumlah'),
                'satuan'        => $this->request->getPost('satuan'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
                'barcode'       => $this->request->getPost('barcode'),
                'minimum_stok'  => $this->request->getPost('minimum_stok'),
            ];
            $this->barangModel->update($id, $data);
            return redirect()->back()->with('success', 'Barang berhasil diupdate.');
        }
        return redirect()->back()->with('error', 'Gagal update barang.');
    }

    public function hapusBarang($id)
    {
        $this->barangModel->delete($id);
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

    public function downloadBarcode($id)
    {
        // Dummy: Anda bisa generate barcode dan download di sini
        // Sementara redirect saja
        return redirect()->back()->with('success', 'Barcode berhasil diunduh (dummy).');
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
            ->orderBy('laporan.tanggal', 'DESC')
            ->paginate($perPage, 'riwayat');

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
            'tanggal'   => $this->request->getPost('tanggal') ?? date('Y-m-d H:i:s'),
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
