<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\LogAktivitasModel;

class AdminController extends BaseController
{
    protected $adminModel,
        $userModel,
        $barangModel,
        $laporanModel,
        $LogAktivitasModel;

    public function __construct()
    {
        $this->adminModel  = new AdminModel();
        $this->userModel   = new UserModel();
        $this->barangModel = new BarangModel();
        $this->laporanModel = new LaporanModel();
        $this->LogAktivitasModel = new LogAktivitasModel();
        helper(['form', 'url']);
    }

    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $diff . ' detik yang lalu';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' menit yang lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam yang lalu';
        } else {
            return floor($diff / 86400) . ' hari yang lalu';
        }
    }

    public function dashAdmin()
    {
        $dataAdmin = session()->get('id_admin');
        $admin = $this->adminModel->find($dataAdmin);

        // Hitung total staff
        $totalStaff = $this->userModel->countAllResults();

        // Hitung total barang unik
        $totalBarang = $this->barangModel
            ->select('COUNT(DISTINCT nama_barang) as total')
            ->first()['total'] ?? 0;

        // Barang hampir habis
        $barangHampirHabis = $this->barangModel
            ->where('jumlah <= minimum_stok')
            ->countAllResults();

        // Ambil riwayat laporan dengan join
        $laporan = $this->laporanModel
            ->select('laporan.tanggal, barang.nama_barang, laporan.jumlah, laporan.jenis, users.nama as staff')
            ->join('barang', 'barang.id_barang = laporan.id_barang')
            ->join('users', 'users.id_user = laporan.id_user')
            ->orderBy('laporan.tanggal', 'DESC')
            ->findAll(10);

        $logsUser = $this->LogAktivitasModel->getLogsByUser();

        foreach ($logsUser as &$log) {
            $log['waktu_ago'] = $this->timeAgo($log['created_at']);
        }

        $data = [
            'title'             => 'Dashboard Admin - CargoWing',
            'currentPage'       => 'dashboard',
            'judul'             => 'Dashboard',
            'subJudul'          => 'Selamat datang di dashboard',
            'admin'             => $admin,
            'totalStaff'        => $totalStaff,
            'totalBarang'       => $totalBarang,
            'barangHampirHabis' => $barangHampirHabis,
            'laporan'           => $laporan,
            'logsUser'         => $logsUser, // parsing ke view
        ];

        return view('admin/dashboard', $data);
    }

    public function indexSuperAdmin()
    {
        $data = [
            'title' => 'Dashboard Super Admin - CargoWing',
        ];
        return view('superadmin/dashboard', $data);
    }

    public function profil()
    {
        $dataAdmin = session()->get('id_admin');
        $admin = $this->adminModel->find($dataAdmin);

        $data = [
            'title'       => 'Profil Admin - CargoWing',
            'currentPage' => 'profil',
            'judul'       => 'Profil',
            'subJudul'    => 'Pengaturan Akun',
            'admin'       => $admin,
        ];

        return view('admin/profil', $data);
    }

    public function updateProfil()
    {
        $adminId = session()->get('id_admin');
        $admin   = $this->adminModel->find($adminId);

        if (!$admin) {
            return redirect()->back()->with('error', 'Data admin tidak ditemukan!');
        }

        $nama  = $this->request->getPost('nama');
        $email = $this->request->getPost('email');

        $dataUpdate = [
            'nama'  => $nama,
            'email' => $email,
        ];

        // Cek perubahan (pakai data lama dari DB sebagai identitas pelaku)
        $pelaku = $admin['nama'];

        if ($admin['nama'] !== $nama && $admin['email'] === $email) {
            logAktivitas("$pelaku mengganti nama dari '{$admin['nama']}' menjadi '{$nama}'");
        } elseif ($admin['nama'] === $nama && $admin['email'] !== $email) {
            logAktivitas("$pelaku mengganti email dari '{$admin['email']}' menjadi '{$email}'");
        } elseif ($admin['nama'] !== $nama && $admin['email'] !== $email) {
            logAktivitas("$pelaku mengganti nama dari '{$admin['nama']}' menjadi '{$nama}', dan mengganti email dari '{$admin['email']}' menjadi '{$email}'");
        }

        // Update data
        $this->adminModel->update($adminId, $dataUpdate);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function kelolaBarang()
    {
         $dataAdmin = session()->get('id_admin');
        $admin     = $this->adminModel->find($dataAdmin);

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
            'title'      => 'Kelola Barang Admin - CargoWing',
            'currentPage' => 'kelolabarang',
            'judul'      => 'Kelola Barang',
            'subJudul'   => 'Manajemen Data Barang',
            'admin'       => $admin,
            'keyword'    => $keyword,
            'perPage'    => $perPage,
            'barangList' => $barangList,
            'uniqueBarang' => $uniqueBarang,
            'pager'      => $this->barangModel->pager // Pastikan pager diambil dari model
        ];

        return view('admin/kelola_barang', $data);
    }

    public function updateBarang($id){
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan!');
        }

        $nama         = $this->request->getPost('nama_barang');
        $jumlah       = $this->request->getPost('jumlah');
        $satuan       = $this->request->getPost('satuan');
        $minimumStok  = $this->request->getPost('minimum_stok');
        $barcodeInput = $this->request->getPost('barcode');

        // Siapkan data update tanpa mengubah barcode jika input barcode kosong
        $dataUpdate = [
            'nama_barang'  => $nama,
            'jumlah'       => $jumlah,
            'satuan'       => $satuan,
            'minimum_stok' => $minimumStok,
        ];

        // Hanya set barcode jika ada nilai yang valid dari form (tidak kosong)
        if ($barcodeInput !== null && trim($barcodeInput) !== '') {
            $dataUpdate['barcode'] = $barcodeInput;
        }

        $this->barangModel->update($id, $dataUpdate);

        return redirect()->back()->with('success', 'Data barang berhasil diperbarui!');
    }
        public function hapusBarang($id)
    {
        $this->barangModel->delete($id);
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
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

    public function tambahBarang()
    {
        $namaBarang = $this->request->getPost('nama_barang');
        $jumlah     = $this->request->getPost('jumlah');
        $satuan     = $this->request->getPost('satuan');
        $minimumStok = $this->request->getPost('minimum_stok');
        $barcode    = $this->request->getPost('barcode');

        // Validasi input
        if (empty($namaBarang) || empty($jumlah) || empty($satuan) || empty($minimumStok)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi!');
        }

        // Simpan data barang
        $this->barangModel->save([
            'nama_barang'  => $namaBarang,
            'jumlah'       => $jumlah,
            'satuan'       => $satuan,
            'minimum_stok' => $minimumStok,
            'barcode'      => $barcode,
        ]);

        // Log aktivitas
        $adminId = session()->get('id_admin');
        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
    }

    public function kelolaStaff()
{
    $dataAdmin = session()->get('id_admin');
    $admin     = $this->adminModel->find($dataAdmin);

    $keyword   = $this->request->getVar('keyword');
    $perPage   = $this->request->getVar('per_page') ?? 10;

    // Mulai query ke tabel users
    $userQuery = $this->userModel;

    if ($keyword) {
        $userQuery = $userQuery->like('nama', $keyword)
                               ->orLike('email', $keyword)
                               ->orLike('no_hp', $keyword);
    }

    // Gunakan paginate untuk pagination
    $staffList = $userQuery->orderBy('id_user', 'ASC')
                           ->paginate($perPage, 'number');

    $data = [
        'title'       => 'Kelola Staff - CargoWing',
        'currentPage' => 'kelolaStaff',
        'judul'       => 'Kelola Staff',
        'subJudul'    => 'Manajemen Data Staff',
        'admin'       => $admin,
        'staffList'   => $staffList,
        'keyword'     => $keyword, 
        'perPage'     => $perPage,
        'pager'       => $userQuery->pager, // pakai pager dari query terakhir
    ];

    return view('admin/kelola_staff', $data);
}


    public function tambahStaff()
    {
        $nama     = $this->request->getPost('nama');
        $email    = $this->request->getPost('email');
        $noHp     = $this->request->getPost('no_hp');
        $password = $this->request->getPost('password');

        // Validasi input
        if (empty($nama) || empty($email) || empty($noHp) || empty($password)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi!');
        }
        // Cek email sudah terdaftar
        $existingUser = $this->userModel->where('email', $email)->first();
        if ($existingUser) {
            return redirect()->back()->with('error', 'Email sudah terdaftar!');
        }
        // Cek nomor HP sudah terdaftar
        $existingUser = $this->userModel->where('no_hp', $noHp)->first();
        if ($existingUser) {
            return redirect()->back()->with('error', 'Nomor HP sudah terdaftar!');
        }
        // Simpan data staff
        $this->userModel->insert([
            'nama'     => $nama,
            'email'    => $email,
            'no_hp'    => $noHp,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'staff',
        ]);
        return redirect()->back()->with('success', 'Staff berhasil ditambahkan!');
    }
    public function editStaff($id)
    {
        $staff = $this->userModel->find($id);
        if (!$staff) {
            return redirect()->back()->with('error', 'Data staff tidak ditemukan!');
        }
        $data = [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];
        $this->userModel->update($id, $data);
        return redirect()->back()->with('success', 'Data staff berhasil diperbarui!');
    }

    public function hapusStaff($id)
    {
        $this->userModel->delete($id);
        return redirect()->back()->with('success', 'Staff berhasil dihapus.');
    }

    public function laporanBarang(){
        $dataAdmin = session()->get('id_admin');
        $admin     = $this->adminModel->find($dataAdmin);
        $keyword   = $this->request->getVar('keyword');
        $perPage  = $this->request->getVar('per_page') ?? 10;
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
            'title'       => 'Laporan Barang - CargoWing',
            'currentPage' => 'laporan',
            'judul'       => 'Laporan Barang',
            'subJudul'    => 'Riwayat Laporan Barang',
            'admin'       => $admin,
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'riwayatData' => $riwayatData,
            'uniqueBarang' => $uniqueBarang,
            'pager'       => $this->laporanModel->pager // Pastikan pager diambil dari model
            
        ];
        return view('admin/laporan', $data);
    }



public function cetakLaporanPDF()
{
    $keyword = $this->request->getGet('keyword');

    // koneksi DB
    $db = \Config\Database::connect();
    $builder = $db->table('laporan l')
        ->select('l.*, b.nama_barang as nama_barang, u.nama as nama_user')
        ->join('barang b', 'b.id_barang = l.id_barang', 'left')
        ->join('users u', 'u.id_user = l.id_user', 'left');

    if (!empty($keyword)) {
        $builder->groupStart()
            ->like('b.nama_barang', $keyword)
            ->orLike('u.nama', $keyword)
            ->orLike('l.jenis', $keyword)
            ->groupEnd();
    }

    $riwayatData = $builder->orderBy('l.tanggal', 'DESC')->get()->getResultArray();

    // Hitung total
    $totalMasuk = 0;
    $totaldipakai = 0;
    foreach ($riwayatData as $row) {
        if (strtolower($row['jenis']) === 'masuk') {
            $totalMasuk += $row['jumlah'];
        } elseif (strtolower($row['jenis']) === 'dipakai') {
            $totaldipakai += $row['jumlah'];
        }
    }

    // Tentukan kesimpulan
    if ($totalMasuk > $totaldipakai) {
        $kesimpulan = 'Stok bertambah (' . ($totalMasuk - $totaldipakai) . ')';
    } elseif ($totalMasuk < $totaldipakai) {
        $kesimpulan = 'Stok berkurang (' . ($totaldipakai - $totalMasuk) . ')';
    } else {
        $kesimpulan = 'Stok seimbang';
    }

    // render view pdf
    $html = view('admin/laporan_pdf', [
        'riwayatData' => $riwayatData,
        'keyword'     => $keyword,
        'totalMasuk'  => $totalMasuk,
        'totaldipakai' => $totaldipakai,
        'kesimpulan'  => $kesimpulan,
    ]);

    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = 'laporan_'.date('Ymd_His').'.pdf';

    $pdfOutput = $dompdf->output();

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->setBody($pdfOutput);
}

public function cetakLaporanExcel()
{
    $keyword = $this->request->getGet('keyword');

    // Ambil data riwayat
    $riwayatData = $this->laporanModel->getRiwayatData($keyword);

    // Buat Spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul laporan
    $sheet->setCellValue('A1', 'LAPORAN RIWAYAT BARANG');
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    // Header tabel
    $sheet->setCellValue('A3', 'No');
    $sheet->setCellValue('B3', 'Waktu');
    $sheet->setCellValue('C3', 'Nama Barang');
    $sheet->setCellValue('D3', 'Jumlah');
    $sheet->setCellValue('E3', 'Jenis');
    $sheet->setCellValue('F3', 'Staff');

    // Styling header
    $sheet->getStyle('A3:F3')->getFont()->setBold(true);
    $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');

    // Lebar kolom otomatis
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Isi data
    $row = 4;
    $no = 1;
    $totalMasuk = 0;
    $totaldipakai = 0;

    foreach ($riwayatData as $data) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, date('d-m-Y H:i:s', strtotime($data['tanggal'])));
        $sheet->setCellValue('C' . $row, $data['nama_barang']);
        $sheet->setCellValue('D' . $row, $data['jumlah']);
        $sheet->setCellValue('E' . $row, $data['jenis']);
        $sheet->setCellValue('F' . $row, $data['nama']);

        // Hitung total
        if (strtolower($data['jenis']) === 'masuk') {
            $totalMasuk += $data['jumlah'];
        } elseif (strtolower($data['jenis']) === 'dipakai') {
            $totaldipakai += $data['jumlah'];
        }

        $row++;
    }

    // Tambahkan total & kesimpulan
    $row += 2;
    $sheet->setCellValue('E' . $row, 'Total Barang Masuk:');
    $sheet->setCellValue('F' . $row, $totalMasuk);

    $row++;
    $sheet->setCellValue('E' . $row, 'Total Barang dipakai:');
    $sheet->setCellValue('F' . $row, $totaldipakai);

    $row += 2;
    $sheet->setCellValue('E' . $row, 'Kesimpulan:');
    if ($totalMasuk > $totaldipakai) {
        $sheet->setCellValue('F' . $row, 'Stok bertambah (' . ($totalMasuk - $totaldipakai) . ')');
    } elseif ($totalMasuk < $totaldipakai) {
        $sheet->setCellValue('F' . $row, 'Stok berkurang (' . ($totaldipakai - $totalMasuk) . ')');
    } else {
        $sheet->setCellValue('F' . $row, 'Stok seimbang');
    }

    // Output Excel
    $fileName = 'Laporan_Riwayat_' . date('Ymd_His') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"{$fileName}\"");
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}




    public function gantiPassword()
    {
        $adminId = session()->get('id_admin');
        $admin   = $this->adminModel->find($adminId);

        if (!$admin) {
            return redirect()->back()->with('errorp', 'Data admin tidak ditemukan!');
        }

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasi   = $this->request->getPost('konfirmasi_password');

        if (!password_verify($passwordLama, $admin['password'])) {
            return redirect()->back()->with('errorp', 'Password lama salah!');
        }

        if ($passwordBaru !== $konfirmasi) {
            return redirect()->back()->with('errorp', 'Konfirmasi password tidak cocok!');
        }

        $this->adminModel->update($adminId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('successp', 'Password berhasil diubah!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'))->with('success', 'Anda telah berhasil logout.');
    }
}
