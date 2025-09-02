<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use App\Models\LogAktivitasModel;

class SuperAdminController extends BaseController
{
    protected $adminModel,
        $userModel,
        $barangModel,
        $laporanModel,
        $logAktivitasModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel;
        $this->userModel = new userModel;
        $this->barangModel = new BarangModel;
        $this->laporanModel = new LaporanModel;
        $this->logAktivitasModel = new LogAktivitasModel;
    }

    public function dashSuperAdmin()
    {
        $dataSuperAdmin = session()->get('id_admin');
        $superAdmin = $this->adminModel->find($dataSuperAdmin);

        // Hitung total admin dan staff
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

        $logsAdmin = $this->logAktivitasModel->getLogsByAdmin();

        foreach ($logsAdmin as &$log) {
            $log['waktu_ago'] = $this->timeAgo($log['created_at']);
        }

        $data = [
            'title'             => 'Dashboard Admin - CargoWing',
            'currentPage'       => 'dashboard',
            'superAdmin'        => $superAdmin,
            'totalAdmin'        => $totalAdmin,
            'totalAdminAktif'   => $totalAdminAktif,
            'totalStaff'        => $totalStaff,
            'totalBarang'       => $totalBarang,
            'logsAdmin'         => $logsAdmin, // parsing ke view
        ];

        return view('superAdmin/dashboard', $data);
    }

    /**
     * Konversi created_at menjadi format "x menit/jam yang lalu"
     */
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

    public function kelolaAdmin()
    {
        // $data = [
        //     'title'         => 'Log Aktivitas Admin',
        //     'currentPage'   => 'log-aktivitas-admin'
        // ];
        // return view('superadmin/', $data);

        echo "progress kelola";
    }

    public function logAktivitasAdmin()
    {
        $dataSuperAdmin = session()->get('id_admin');
        $superAdmin = $this->adminModel->find($dataSuperAdmin);

        $data = [
            'title'         => 'Log Aktivitas Admin',
            'currentPage'   => 'log-aktivitas-admin',
            'superAdmin'    => $superAdmin
        ];
        return view('superadmin/log_aktivitas_admin', $data);
    }

    public function pengaturanAkun()
    {
        $dataSuperAdmin = session()->get('id_admin');
        $superAdmin = $this->adminModel->find($dataSuperAdmin);

        $data = [
            'title'         => 'Pengaturan Akun',
            'currentPage'   => 'pengaturan-akun',
            'superAdmin'    => $superAdmin
        ];
        return view('superadmin/pengaturan_akun', $data);
    }
}
