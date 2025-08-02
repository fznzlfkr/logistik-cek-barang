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
}
