<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_admin',
        'role',
        'aktivitas',
        'ip_address',
        'user_agent'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Ambil log aktivitas admin
     */
    public function getLogsByAdmin($limit = 10)
    {
        return $this->select('log_aktivitas.*, admin.nama as nama_admin')
            ->join('admin', 'admin.id_admin = log_aktivitas.id_admin', 'left')
            ->where('log_aktivitas.role', 'Admin')
            ->orderBy('log_aktivitas.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ambil log aktivitas user (staff gudang)
     */
    public function getLogsByUser($limit = 10)
    {
        return $this->select('log_aktivitas.*, users.nama as nama_user')
            ->join('users', 'users.id_user = log_aktivitas.id_user', 'left')
            ->where('log_aktivitas.role', 'User')
            ->orderBy('log_aktivitas.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Simpan log aktivitas (untuk admin atau user)
     */
    public function addLog($data)
    {
        return $this->insert([
            'id_user'    => $data['id_user'] ?? null,
            'id_admin'   => $data['id_admin'] ?? null,
            'role'       => $data['role'],
            'aktivitas'  => $data['aktivitas'],
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent(),
        ]);
    }
}
