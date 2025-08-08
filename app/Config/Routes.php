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
$routes->group('superadmin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'AdminController::indexSuperAdmin');
});

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'AdminController::index');
});


$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'UserController::index');
    $routes->get('kelola_barang', 'UserController::kelolaBarang');
    $routes->get('tambah_barang', 'UserController::tambahBarang');
    $routes->post('simpan_barang', 'UserController::simpanBarang');
    $routes->post('edit_barang/(:num)', 'UserController::editBarang/$1');
    $routes->post('hapus_barang/(:num)', 'UserController::hapusBarang/$1');
    $routes->get('download_barcode/(:num)', 'UserController::downloadBarcode/$1');
    $routes->get('riwayat', 'UserController::riwayat');
    $routes->get('profil', 'UserController::profil');
    $routes->post('profil/update', 'UserController::update');
    $routes->post('profil/ganti-password', 'UserController::gantiPassword');
    $routes->post('logout', 'UserController::logout');
});
