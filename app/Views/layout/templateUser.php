<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <title>User Dashboard - CargoWing</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
        padding-top: 80px;
    }

    .navbar-custom {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        height: 80px;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    .navbar-link {
        font-size: 1.1rem;
        font-weight: 500;
        transition: all 0.3s;
    }

    /* ini buat yang aktif */
    .navbar-link.active {
        color: #60a5fa; /* biru muda */
        padding-bottom: 4px;
        font-weight: 600; /* lebih tebal */
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/user.css') ?>">

</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar Biru Besar -->
    <header class="navbar-custom">
        <div class="flex items-center gap-3">
            <img src="../assets/img/logo.jpg" alt="Logo CargoWing" class="w-10 h-10 rounded-full border-2 border-white">
            <span class="text-2xl font-semibold text-white">CargoWing</span>
        </div>
        <nav class="space-x-8">
            <a href="<?= base_url('/user/dashboard') ?>" class="navbar-link text-white hover:text-blue-300 <?= (strtolower($currentPage) === 'dashboard') ? 'active' : '' ?>" >Beranda</a>
            <a href="<?= base_url('/user/kelola_barang') ?>" class="navbar-link text-white hover:text-blue-300 <?= (strtolower($currentPage) === 'kelolabarang') ? 'active' : '' ?>">Kelola Barang</a>
            <a href="<?= base_url('/user/riwayat') ?>" class="navbar-link text-white hover:text-blue-300 <?= (strtolower($currentPage) === 'riwayat') ? 'active' : '' ?> ">Riwayat</a>
            <a href="<?= base_url('/user/profil') ?>" class="navbar-link text-white hover:text-blue-300 <?= (strtolower($currentPage) === 'profil') ? 'active' : '' ?> ">Profil</a>
        </nav>
        <a href="<?= base_url('user/profil') ?>">
    <div class="flex items-center gap-4">
        <div class="text-right text-base">
            <div class="font-semibold text-white"><?= esc($user['nama']) ?></div>
            <div class="text-blue-200 text-sm">Staff Gudang</div>
        </div>

        <?php
        $nama = trim($user['nama']);
        $parts = explode(' ', $nama);

        if (count($parts) >= 2) {
            // ambil huruf depan dari 2 kata
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        } else {
            // ambil 2 huruf pertama dari 1 kata
            $initials = strtoupper(substr($parts[0], 0, 2));
        }
        ?>
        
        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center bg-blue-500 text-white font-bold">
            <?= $initials ?>
        </div>
    </div>
</a>

    </header>

    <!-- Konten Dinamis -->
    <main class="p-6">
        <?= $this->renderSection('content') ?>
    </main>

    <script>
        feather.replace();
    </script>
    <script src="<?= base_url('/assets/js/user.js') ?>"></script>
</body>

</html>
