<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;

class SuperAdminController extends BaseController
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

    public function dashSuperAdmin()
    {
        $dataSuperAdmin = session()->get('id_admin');
        $superAdmin = $this->adminModel->find($dataSuperAdmin);

        $totalAdmin = $this->adminModel
            ->where('role', 'Admin')
            ->countAllResults();

        $totalAdminAktif = $this->adminModel
            ->where('role', 'Admin')
            ->where('aktif', true)
            ->countAllResults();

        $totalBarang = $this->barangModel
            ->select('COUNT(DISTINCT nama_barang) as total')
            ->first()['total'] ?? 0;

        $totalStaff = $this->userModel->countAllResults();

        $data = [
            'title'             => 'Dashboard Admin - CargoWing',
            'currentPage'       => 'dashboard',
            'superAdmin'        => $superAdmin,
            'totalAdmin'        => $totalAdmin,
            'totalAdminAktif'   => $totalAdminAktif,
            'totalStaff'        => $totalStaff,
            'totalBarang'       => $totalBarang,
        ];
        return view('superAdmin/dashboard', $data);
    }
}
