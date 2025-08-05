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
    }

    public function index()
    {
        $data = [
            'title' => 'Masuk'
        ];
        return view('auth/login', $data);
    }

    public function register()
    {
        $data = [
            'title' => 'Daftar'
        ];
        return view('auth/register', $data);
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

        return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
    }

    public function registerProcess()
    {
        $data = $this->request->getPost();
        $validation = \Config\Services::validation();

        // Validasi dasar
        $validation->setRules([
            'nama'     => 'required',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
            'no_hp'    => 'required|numeric|min_length[10]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali.');
        }

        // Cek apakah email sudah digunakan
        if ($this->userModel->where('email', $data['email'])->first()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar! Silakan gunakan email lain.');
        }

        // Cek apakah nomor HP sudah digunakan
        if ($this->userModel->where('no_hp', $data['no_hp'])->first()) {
            return redirect()->back()->withInput()->with('error', 'Nomor HP sudah terdaftar!');
        }

        // Simpan data jika valid
        $this->userModel->insert([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'no_hp'    => $data['no_hp'],
        ]);

        return redirect()->to('/')->with('success', 'Registrasi berhasil! Silakan login.');
    }



    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
