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
$routes->group('superadmin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::indexSuperAdmin');
});

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashAdmin');
});

$routes->group('user', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'UserController::index');
    $routes->get('kelola_barang', 'UserController::kelolaBarang');
    $routes->get('tambah_barang', 'UserController::tambahBarang');
    $routes->post('simpan_barang', 'UserController::simpanBarang');
    $routes->post('edit_barang/(:num)', 'UserController::editBarang/$1');
    $routes->post('update_barang/(:num)', 'UserController::updateBarang/$1');
    $routes->post('update_barang/(:num)', 'UserController::updateBarang/$1');
    $routes->post('hapus_barang/(:num)', 'UserController::hapusBarang/$1');
    $routes->post('download_barcode/(:num)', 'UserController::downloadBarcode/$1');
    $routes->get('barang/pdf/(:any)', 'UserController::pdf/$1');
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->get('barang_masuk', 'UserController::barangMasuk');
    $routes->post('barang_masuk/save', 'UserController::simpanBarangMasuk');
    $routes->get('barang_keluar', 'UserController::barangKeluar');
    $routes->post('barang_keluar/save', 'UserController::saveBarangKeluar');
    $routes->get('profil', 'UserController::profil');
    $routes->post('profil/update', 'UserController::update');
    $routes->post('profil/ganti-password', 'UserController::gantiPassword');
    $routes->post('logout', 'UserController::logout');
    $routes->post('hapus-riwayat/(:num)', 'UserController::hapusRiwayat/$1');
    $routes->post('edit-riwayat/(:num)', 'UserController::editRiwayat/$1');
    $routes->post('print-riwayat/(:num)', 'UserController::printRiwayat/$1');
});
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

$routes->group('superadmin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'SuperAdminController::dashSuperAdmin');
});

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashAdmin');
});

$routes->group('user', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'UserController::index');
    $routes->get('kelola_barang', 'UserController::kelolaBarang');
    $routes->get('tambah_barang', 'UserController::tambahBarang');
    $routes->post('simpan_barang', 'UserController::simpanBarang');
    $routes->post('edit_barang/(:num)', 'UserController::editBarang/$1');
    $routes->post('update_barang/(:num)', 'UserController::updateBarang/$1');
    $routes->post('update_barang/(:num)', 'UserController::updateBarang/$1');
    $routes->post('hapus_barang/(:num)', 'UserController::hapusBarang/$1');
    $routes->post('download_barcode/(:num)', 'UserController::downloadBarcode/$1');
    $routes->get('barang/pdf/(:any)', 'UserController::pdf/$1');
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->get('barang_masuk', 'UserController::barangMasuk');
    $routes->post('barang_masuk/save', 'UserController::simpanBarangMasuk');
    $routes->get('barang_keluar', 'UserController::barangKeluar');
    $routes->post('barang_keluar/save', 'UserController::saveBarangKeluar');
    $routes->get('profil', 'UserController::profil');
    $routes->post('profil/update', 'UserController::update');
    $routes->post('profil/ganti-password', 'UserController::gantiPassword');
    $routes->post('logout', 'UserController::logout');
    $routes->post('hapus-riwayat/(:num)', 'UserController::hapusRiwayat/$1');
    $routes->post('edit-riwayat/(:num)', 'UserController::editRiwayat/$1');
    $routes->post('print-riwayat/(:num)', 'UserController::printRiwayat/$1');
});
