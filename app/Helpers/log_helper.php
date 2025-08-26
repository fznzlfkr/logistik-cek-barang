<?php

use Config\Database;
use Config\Services;

if (!function_exists('logAktivitas')) {
    /**
     * Catat log aktivitas user/admin
     *
     * @param string $aktivitas Deskripsi aktivitas
     */
    function logAktivitas(string $aktivitas)
    {
        $db = Database::connect();
        $request = Services::request();

        $data = [
            'id_user'   => session()->get('id_user'),
            'role'      => session()->get('role'),
            'aktivitas' => $aktivitas,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('log_aktivitas')->insert($data);
    }
}
