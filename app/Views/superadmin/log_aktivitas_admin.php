<?= $this->extend('layout/TemplateSuperAdmin') ?>

<?= $this->section('content') ?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>Log Aktivitas Admin</h1>
                <p>Riwayat aktivitas semua Admin pada sistem.</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari...">
                </div>
                <div class="user-profile">
                    <div class="user-avatar">JD</div>
                    <a href="<?= base_url('admin/pengaturan-akun') ?>" class="a-info">
                        <div class="user-info">
                            <h6> <?= esc($superAdmin['nama']) ?></h6>
                            <p>Administrator</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">

        <!-- Content Grid -->
        <div class="content-grid">

            <!-- Recent Activity -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Admin Terbaru</h3>
                    <a href="#" class="card-action">View All</a>
                </div>

                <ul class="activity-list">
                    <!-- Dummy Data -->
                    <li class="activity-item">
                        <div class="activity-avatar">FA</div>
                        <div class="activity-content">
                            <h6>Faiz</h6>
                            <p>Login berhasil sebagai Admin</p>
                        </div>
                        <div class="activity-time">2 menit lalu</div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-avatar">AD</div>
                        <div class="activity-content">
                            <h6>Andi</h6>
                            <p>Menambahkan data barang baru (Laptop ASUS)</p>
                        </div>
                        <div class="activity-time">10 menit lalu</div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-avatar">RS</div>
                        <div class="activity-content">
                            <h6>Rosa</h6>
                            <p>Menghapus data staff #7</p>
                        </div>
                        <div class="activity-time">1 jam lalu</div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-avatar">MI</div>
                        <div class="activity-content">
                            <h6>Mira</h6>
                            <p>Logout dari sistem</p>
                        </div>
                        <div class="activity-time">2 jam lalu</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>