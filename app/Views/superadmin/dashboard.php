<?= $this->extend('layout/TemplateSuperAdmin') ?>

<?= $this->section('content') ?>
<!-- Main Content -->
<div class="main-content">
  <!-- Header -->
  <div class="header">
    <div class="header-content">
      <div class="header-title">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <?= esc($superAdmin['nama']) ?>! Berikut aktivitas hari ini.</p>
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

    <!-- Stats Cards -->
    <div class="stats-grid">

      <!-- Total Admin Aktif -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Total Admin</p>
          <div class="stat-icon">
            <i class="fas fa-user-shield"></i>
          </div>
        </div>
        <h2 class="stat-value"><?= esc($superAdmin ? 1 : 0) ?></h2>
      </div>

      <!-- Barang Hampir Habis -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Total Admin Aktif</p>
          <div class="stat-icon">
            <i class="fas fa-user-check"></i>
          </div>
        </div>
        <h2 class="stat-value"></h2>
      </div>

      <!-- Barang di Gudang -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Barang di Gudang</p>
          <div class="stat-icon">
            <i class="fas fa-boxes"></i>
          </div>
        </div>
        <h2 class="stat-value"><?= esc($totalBarang) ?></h2>
      </div>

      <!-- Total Staff Gudang -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Total Staff Gudang</p>
          <div class="stat-icon">
            <i class="fas fa-user-friends"></i>
          </div>
        </div>
        <h2 class="stat-value"><?= esc($totalStaff) ?></h2>
      </div>

    </div>

    <!-- Content Grid -->
    <div class="content-grid">

      <!-- Recent Activity -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">Aktivitas Terbaru</h3>
          <a href="#" class="card-action">View All</a>
        </div>
        <ul class="activity-list">
          <li class="activity-item">
            <div class="activity-avatar">JS</div>
            <div class="activity-content">
              <h6>John Smith placed an order</h6>
              <p>Dell XPS Laptop - $1,299</p>
            </div>
            <div class="activity-time">2 min ago</div>
          </li>
          <li class="activity-item">
            <div class="activity-avatar">JD</div>
            <div class="activity-content">
              <h6>Jane Doe updated profile</h6>
              <p>Changed shipping address</p>
            </div>
            <div class="activity-time">15 min ago</div>
          </li>
          <li class="activity-item">
            <div class="activity-avatar">MJ</div>
            <div class="activity-content">
              <h6>Mike Johnson cancelled order</h6>
              <p>Samsung Galaxy S23 - $799</p>
            </div>
            <div class="activity-time">1 hour ago</div>
          </li>
          <li class="activity-item">
            <div class="activity-avatar">SW</div>
            <div class="activity-content">
              <h6>Sarah Wilson registered</h6>
              <p>New user account created</p>
            </div>
            <div class="activity-time">2 hours ago</div>
          </li>
          <li class="activity-item">
            <div class="activity-avatar">RB</div>
            <div class="activity-content">
              <h6>Robert Brown left review</h6>
              <p>5 stars for iPhone 14 Pro</p>
            </div>
            <div class="activity-time">3 hours ago</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>