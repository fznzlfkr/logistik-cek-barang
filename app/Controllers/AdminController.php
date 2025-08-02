<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin - CargoWing',
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
