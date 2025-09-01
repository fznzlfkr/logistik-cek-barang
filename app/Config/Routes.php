<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/register', 'AuthController::register');
$routes->post('/loginProcess', 'AuthController::loginProcess');
$routes->post('/registerProcess', 'AuthController::registerProcess');
$routes->get('/logout', 'AuthController::logout');
// File: app/Config/Routes.php

$routes->group('superadmin', ['filter' => 'auth:Super Admin'], function ($routes) {
    $routes->get('dashboard', 'SuperAdminController::dashSuperAdmin');
});

$routes->group('admin', ['filter' => 'auth:Admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashAdmin');
    $routes->get('kelola-barang', 'AdminController::kelolaBarang');
    $routes->post('tambah_barang', 'AdminController::tambahBarang');
    $routes->get('laporan-barang', 'AdminController::laporanBarang');
    $routes->get('kelola-staff', 'AdminController::kelolaStaff');
    $routes->post('tambah-staff', 'AdminController::tambahStaff');
    $routes->post('edit-staff/(:num)', 'AdminController::editStaff/$1');
    $routes->post('hapus-staff/(:num)', 'AdminController::hapusStaff/$1');
    $routes->post('update-barang/(:num)', 'AdminController::updateBarang/$1');
    $routes->post('hapus-barang/(:num)', 'AdminController::hapusBarang/$1');
    $routes->post('download-barcode/(:num)', 'AdminController::downloadBarcode/$1');
    $routes->get('pengaturan-akun', 'AdminController::profil');
    $routes->post('profil/update', 'AdminController::updateProfil');
    $routes->post('profil/ganti-password', 'AdminController::gantiPassword');

    // route baru untuk cetak laporan PDF
    $routes->get('laporan/pdf', 'AdminController::cetakLaporan');
});

$routes->group('user', ['filter' => 'auth:User'], function ($routes) {
    $routes->get('dashboard', 'UserController::index');
    $routes->get('kelola_barang', 'UserController::kelolaBarang');
    $routes->get('tambah_barang', 'UserController::tambahBarang');
    $routes->post('update_barang/(:num)', 'UserController::updateBarang/$1');
    $routes->post('hapus_barang/(:num)', 'UserController::hapusBarang/$1');
    $routes->post('download_barcode/(:num)', 'UserController::downloadBarcode/$1');
    $routes->get('barang/pdf/(:any)', 'UserController::pdf/$1');
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->post('barang_masuk/save', 'UserController::simpanBarangMasuk');
    $routes->post('barang_keluar/save', 'UserController::saveBarangKeluar');
    $routes->get('profil', 'UserController::profil');
    $routes->post('profil/update', 'UserController::update');
    $routes->post('profil/ganti-password', 'UserController::gantiPassword');
    $routes->post('logout', 'UserController::logout');
    $routes->post('hapus-riwayat/(:num)', 'UserController::hapusRiwayat/$1');
    $routes->post('edit-riwayat/(:num)', 'UserController::editRiwayat/$1');
    $routes->post('print-riwayat/(:num)', 'UserController::printRiwayat/$1');
});
