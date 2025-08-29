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

        // ================================
        // Ambil log aktivitas terbaru
        // ================================
        $db = \Config\Database::connect();
        $builder = $db->table('log_aktivitas');
        $builder->select('log_aktivitas.*, admin.nama as nama_admin');
        $builder->join('admin', 'admin.id_admin = log_aktivitas.id_admin', 'left');
        $builder->orderBy('log_aktivitas.created_at', 'DESC');
        $builder->limit(10); // ambil 10 terbaru
        $logs = $builder->get()->getResultArray();

        // Tambahkan field "waktu_ago"
        foreach ($logs as &$log) {
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
            'logs'              => $logs, // parsing ke view
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
}
