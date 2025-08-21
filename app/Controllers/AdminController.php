<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
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
        $this->adminModel = new AdminModel;
        $this->userModel = new userModel;
        $this->barangModel = new BarangModel;
        $this->laporanModel = new LaporanModel;
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
            'laporan'           => $laporan, // parsing ke view
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
}
