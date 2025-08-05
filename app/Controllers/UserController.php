<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard User - CargoWing',
        ];
        return view('user/dashboard', $data);
    }
    public function kelolaBarang()
    {
        $data = [
            'title' => 'Kelola Barang User - CargoWing',
        ];
        return view('user/kelola_barang', $data);
    }
    public function riwayat()
    {
        $data = [
            'title' => 'Riiwayat - CargoWing',
        ];
        return view('user/riwayat', $data);
    }
    public function profil()
    {
        $data = [
            'title' => 'profil - CargoWing',
        ];
        return view('user/profil', $data);
    }
}
