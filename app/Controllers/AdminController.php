<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;

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
            'currentPage'       => 'dashboard',
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
            'currentPage' => 'profil',
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
            'currentPage' => 'kelolaBarang',
            'admin'       => $admin,
            'keyword'    => $keyword,
            'perPage'    => $perPage,
            'barangList' => $barangList,
            'uniqueBarang' => $uniqueBarang,
            'pager'      => $this->barangModel->pager // Pastikan pager diambil dari model
        ];

        return view('admin/kelola_barang', $data);
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
