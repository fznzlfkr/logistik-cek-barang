<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ProfilController extends BaseController
{
    public function profil()
    {
         $data = [
            'title' => 'profil - CargoWing',
        ];
        return view('user/profil', $data);
    }

    public function update()
    {
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        // Ambil data dari form
        $nama   = $this->request->getPost('nama');
        $email  = $this->request->getPost('email');
        $no_hp  = $this->request->getPost('no_hp');
        $foto   = $this->request->getFile('foto');

        // Validasi sederhana
        if (empty($nama) || empty($email) || empty($no_hp)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $dataUpdate = [
            'nama'  => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ];

        // Handle upload foto jika ada
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move('uploads', $newName);
            $dataUpdate['foto'] = $newName;
            session()->set('foto', $newName);
        }

        // Update ke database
        $userModel->update($userId, $dataUpdate);

        // Update session
        session()->set([
            'nama'  => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function gantiPassword()
    {
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasi   = $this->request->getPost('konfirmasi_password');

        $user = $userModel->find($userId);

        if (!$user || !password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('errorp', 'Password lama salah.');
        }
        if ($passwordBaru !== $konfirmasi) {
            return redirect()->back()->with('errorp', 'Konfirmasi password tidak sama.');
        }
        if (strlen($passwordBaru) < 8) {
            return redirect()->back()->with('errorp', 'Password baru minimal 8 karakter.');
        }

        $userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('successp', 'Password berhasil diubah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
