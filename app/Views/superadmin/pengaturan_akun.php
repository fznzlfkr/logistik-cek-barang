<?= $this->extend('layout/TemplateSuperAdmin') ?>

<?= $this->section('content') ?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>Pengaturan Akun</h1>
                <p>Kelola informasi akun Administrator Anda di sini.</p>
            </div>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="user-avatar">JD</div>
                    <a href="<?= base_url('admin/pengaturan-akun') ?>" class="a-info">
                        <div class="user-info">
                            <h6><?= esc($superAdmin['nama']) ?></h6>
                            <p>Administrator</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-area">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Informasi Akun -->
            <div class="content-card info">
                <div class="card-header">
                    <h2 class="card-title">Informasi Akun</h2>
                </div>
                <!-- Form Profil -->
                <form id="formProfil" action="<?= base_url('admin/profil/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="nama" value="<?= esc($superAdmin['nama']) ?>"
                            class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?= esc($superAdmin['email']) ?>"
                            class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Simpan</button>
                        <button type="reset" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Batal</button>
                    </div>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Ganti Password</h2>
                </div>
                <form action="<?= base_url('admin/profil/ganti-password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <input type="password" name="password_lama" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password_baru" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Ubah</button>
                        <button type="reset" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>