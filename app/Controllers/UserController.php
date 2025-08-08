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
