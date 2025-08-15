<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\LaporanModel;
use CodeIgniter\HTTP\ResponseInterface;
use Endroid\QrCode\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

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

        // Ambil data barang dari database
        $barangList = $this->barangModel->findAll();

        $data = [
            'title' => 'Kelola Barang User - CargoWing',
            'user' => $user,
            'barangList' => $barangList
        ];
        return view('user/kelola_barang', $data);
    }

    public function tambahBarang()
    {
        $dataUser = session()->get('id_user');
        $user = $this->userModel->find($dataUser);

        $data = [
            'title' => 'Tambah Barang User - CargoWing',
            'user' => $user
        ];
        return view('user/tambah_barang', $data);
    }
    public function simpanBarang()
    {

            $data = [
                'nama_barang'   => $this->request->getPost('nama_barang'),
                'jumlah'        => $this->request->getPost('jumlah'),
                'satuan'        => $this->request->getPost('satuan'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
                'barcode'       => $this->request->getPost('barcode'),
                'minimum_stok'  => $this->request->getPost('minimum_stok'),
            ];

            // Validasi sederhana (pastikan semua field terisi)
            foreach ($data as $key => $value) {
                if ($value === null || $value === '') {
                    return redirect()->back()->withInput()->with('error', 'Field ' . $key . ' wajib diisi.');
                }
            }

            // Simpan data
            if ($this->barangModel->insert($data)) {
                return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
            } else {
                $error = $this->barangModel->errors();
                return redirect()->back()->withInput()->with('error', 'Gagal menambah barang. ' . json_encode($error));
            }
    }

    public function editBarang($id)
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'nama_barang'   => $this->request->getPost('nama_barang'),
                'jumlah'        => $this->request->getPost('jumlah'),
                'satuan'        => $this->request->getPost('satuan'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
                'barcode'       => $this->request->getPost('barcode'),
                'minimum_stok'  => $this->request->getPost('minimum_stok'),
            ];
            $this->barangModel->update($id, $data);
            return redirect()->back()->with('success', 'Barang berhasil diupdate.');
        }
        return redirect()->back()->with('error', 'Gagal update barang.');
    }
    public function updateBarang($id_barang)
{
    $data = [
        'nama_barang'   => $this->request->getPost('nama_barang'),
        'jumlah'        => $this->request->getPost('jumlah'),
        'satuan'        => $this->request->getPost('satuan'),
        'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        'barcode'       => $this->request->getPost('barcode'),
        'minimum_stok'  => $this->request->getPost('minimum_stok'),
    ];

    // Validasi sederhana (pastikan semua field terisi)
    foreach ($data as $key => $value) {
        if ($value === null || $value === '') {
            return redirect()->back()->withInput()->with('error', 'Field ' . $key . ' wajib diisi.');
        }
    }

    // Update data
    if ($this->barangModel->update($id_barang, $data)) {
        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    } else {
        $error = $this->barangModel->errors();
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui barang. ' . json_encode($error));
    }
}


    public function hapusBarang($id)
    {
        $this->barangModel->delete($id);
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

public function scanBarcode($barcode)
{
    // Ambil data barang berdasarkan barcode
    $barang = $this->barangModel->where('barcode', $barcode)->first();

    if (!$barang) {
        return redirect()->back()->with('error', 'Barang tidak ditemukan.');
    }

    // Buat HTML untuk PDF
    $html = view('barang_pdf', ['barang' => $barang]);

    // Inisialisasi Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Nama file PDF
    $fileName = 'barang_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.pdf';

    // Outputkan PDF untuk download
    $dompdf->stream($fileName, ["Attachment" => true]);
}

public function downloadBarcode($id)
{
    $barang = $this->barangModel->find($id);
    if (!$barang) {
        return redirect()->back()->with('error', 'Barang tidak ditemukan.');
    }

    // QR mengarah ke fungsi generate PDF
    $urlPDF = base_url('barang/pdf/' . $barang['barcode']);

    $fileName = 'barcode_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.png';

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($urlPDF)
        ->size(300)
        ->margin(10)
        ->build();

    return $this->response
        ->setHeader('Content-Type', 'image/png')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->setBody($result->getString());
}

public function pdf($barcode)
{
    $barang = $this->barangModel->where('barcode', $barcode)->first();
    if (!$barang) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barang tidak ditemukan');
    }

    // Buat HTML untuk PDF
    $html = view('barang/pdf_template', ['barang' => $barang]);

    // Setup Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = 'Detail_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $barang['nama_barang']) . '.pdf';
    $dompdf->stream($fileName, ["Attachment" => true]);
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
