<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $adminModel;
    protected $session;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->adminModel = new AdminModel();
        $this->session    = session();
        helper(['form']);
    }

    public function index()
    {
        return view('auth/login', ['title' => 'Login - CargoWing']);
    }

    public function register()
    {
        return view('auth/register', ['title' => 'Register - CargoWing']);
    }

    public function loginProcess()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cek ke tabel admin terlebih dahulu
        $admin = $this->adminModel->where('email', $email)->first();

        if ($admin && password_verify($password, $admin['password'])) {
            $this->session->set([
                'id_admin'  => $admin['id_admin'],
                'nama'      => $admin['nama'],
                'email'     => $admin['email'],
                'role'      => $admin['role'],
                'logged_in' => true
            ]);

            if ($admin['role'] === 'Super Admin') {
                return redirect()->to('superadmin/dashboard');
            } else {
                return redirect()->to('admin/dashboard');
            }
        }

        // Jika tidak ditemukan di admin, cek tabel users
        $user = $this->userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $this->session->set([
                'id_user'   => $user['id_user'],
                'nama'      => $user['nama'],
                'email'     => $user['email'],
                'no_hp'     => $user['no_hp'],
                'logged_in' => true,
                // 'role'      => 'User' // Tambahan role bantu
            ]);

            return redirect()->to('user/dashboard');
        }

        return redirect()->back()->withInput()->with('error', '<div style="color:red;">Email atau password salah.</div>');
    }

    public function registerProcess()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama'     => 'required|min_length[4]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'no_hp'    => 'required|numeric|min_length[10]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->userModel->insert([
            'nama'     => $this->request->getPost('nama'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'no_hp'    => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
