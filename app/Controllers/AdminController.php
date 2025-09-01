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

class AdminController extends BaseController
{
    protected $adminModel,
        $userModel,
        $barangModel,
        $laporanModel;

    public function __construct()
    {
        $this->adminModel  = new AdminModel();
        $this->userModel   = new UserModel();
        $this->barangModel = new BarangModel();
        $this->laporanModel = new LaporanModel();
        helper(['form', 'url']);
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

        $data = [
            'title'             => 'Dashboard Admin - CargoWing',
            'currentPage'       => 'Dashboard',
            'admin'             => $admin,
            'totalStaff'        => $totalStaff,
            'totalBarang'       => $totalBarang,
            'barangHampirHabis' => $barangHampirHabis,
            'laporan'           => $laporan,
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
            'currentPage' => 'Profil',
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
            'currentPage' => 'KelolaBarang',
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
        'currentPage' => 'KelolaStaff',
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
            'currentPage' => 'Laporan',
            'admin'       => $admin,
            'keyword'     => $keyword,
            'perPage'     => $perPage,
            'riwayatData' => $riwayatData,
            'uniqueBarang' => $uniqueBarang,
            'pager'       => $this->laporanModel->pager // Pastikan pager diambil dari model
            
        ];
        return view('admin/laporan', $data);
    }



public function cetakLaporan()
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

    // render view pdf
    $html = view('admin/laporan_pdf', [
        'riwayatData' => $riwayatData,
        'keyword'     => $keyword
    ]);

    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait'); // perbaiki penulisan orientation
    $dompdf->render();

    $fileName = 'laporan_'.date('Ymd_His').'.pdf';

    // Generate PDF binary and force download via response headers
    $pdfOutput = $dompdf->output();

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->setBody($pdfOutput);
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
