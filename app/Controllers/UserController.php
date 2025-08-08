<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel,
        $barangModel,
        $laporanModel;

    public function __construct()
    {
        $this->userModel = new UserModel;
        $this->barangModel = new BarangModel;
        $this->laporanModel = new LaporanModel;
    }

    public function index()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $totalMasuk = $this->laporanModel->where('jenis', 'Masuk')->countAllResults();
        $totalDipakai = $this->laporanModel->where('jenis', 'Dipakai')->countAllResults();
        $totalBarang = $this->barangModel->selectSum('jumlah')->get()->getRow()->jumlah;
        $barangMinimum = $this->barangModel->where('jumlah <= minimum_stok')->countAllResults();

        $laporanData = $this->laporanModel
            ->select('laporan.tanggal, barang.nama_barang, laporan.jenis, laporan.jumlah')
            ->join('barang', 'barang.id_barang = laporan.id_barang')
            ->orderBy('laporan.tanggal', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Dashboard User - CargoWing',
            'totalMasuk' => $totalMasuk,
            'totalDipakai' => $totalDipakai,
            'totalBarang' => $totalBarang,
            'barangMinimum' => $barangMinimum,
            'laporanData' => $laporanData,
            'user' => $user
        ];

        return view('user/dashboard', $data);
    }

    public function kelolaBarang()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Kelola Barang User - CargoWing',
            'user' => $user
        ];
        return view('user/kelola_barang', $data);
    }

    public function riwayat()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Riiwayat - CargoWing',
            'user' => $user
        ];
        return view('user/riwayat', $data);
    }

    public function profil()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'profil - CargoWing',
            'user' => $user
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
