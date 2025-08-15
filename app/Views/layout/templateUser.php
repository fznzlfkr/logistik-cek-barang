<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - CargoWing</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('../assets/css/user.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="../assets/img/logo.jpg" alt="Logo CargoWing" class="w-8 h-8 rounded-full">
            <span class="text-xl font-semibold">CargoWing</span>
        </div>
        <nav class="space-x-6 text-sm font-medium">
            <a href="<?= base_url('/user/dashboard') ?>" class="text-gray-700 hover:text-blue-600">Beranda</a>
            <a href="<?= base_url('/user/kelola_barang') ?>" class="text-gray-700 hover:text-blue-600">Kelola Barang</a>
            <a href="<?= base_url('/user/riwayat') ?>" class="text-gray-700 hover:text-blue-600">Riwayat</a>
            <a href="<?= base_url('/user/profil') ?>" class="text-gray-700 hover:text-blue-600">Profil</a>
        </nav>
        <a href="<?= base_url('user/profil') ?>">
            <div class="flex items-center gap-3">
            <div class="text-right text-sm">
                <div class="font-semibold text-gray-800"><?= esc($user['nama']) ?></div>
                <div class="text-gray-500 text-xs">Staff Gudang</div>
            </div>
            <img src="../assets/img/logo.jpg" alt="User" class="w-8 h-8 rounded-full">
        </div>
        </a>
    </header>

    <!-- Konten Dinamis -->
    <?= $this->renderSection('content') ?>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="<?= base_url('../assets/js/user.js') ?>"></script>
    <script>
        feather.replace();
    </script>
</body>

</html>