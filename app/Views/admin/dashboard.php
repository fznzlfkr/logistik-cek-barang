<?= $this->extend('layout/TemplateAdmin') ?>

<?= $this->section('content') ?>
<!-- Main Content -->
<div class="main-content">


  <!-- Content Area -->
  <div class="content-area">

    <!-- Stats Cards -->
    <div class="stats-grid">

      <!-- Total Admin Aktif -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Total Admin Aktif</p>
          <div class="stat-icon">
            <i class="fas fa-user-shield"></i>
          </div>
        </div>
        <h2 class="stat-value"><?= esc($admin ? 1 : 0) ?></h2>
      </div>

      <!-- Barang Hampir Habis -->
      <div class="stat-card">
        <div class="stat-header">
          <p class="stat-title">Barang Hampir Habis</p>
          <div class="stat-icon">
            <i class="fas fa-box-open"></i>
          </div>
        </div>
        <h2 class="stat-value"><?= esc($barangHampirHabis) ?></h2>
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
      <!-- Recent Orders -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">Laporan Terbaru</h3>
          <a href="<?= base_url('admin/laporan') ?>" class="card-action">View All</a>
        </div>
        <div class="table-container">
          <table class="modern-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Jenis</th>
                <th>Staff</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($laporan)): ?>
                <?php foreach ($laporan as $row): ?>
                  <tr>
                    <td><?= date('d M Y H:i', strtotime($row['tanggal'])) ?></td>
                    <td><?= esc($row['nama_barang']) ?></td>
                    <td><?= esc($row['jumlah']) ?></td>
                    <td>
                      <?php if ($row['jenis'] == 'Masuk'): ?>
                        <span class="status-badge status-active">Masuk</span>
                      <?php else: ?>
                        <span class="status-badge status-pending">Dipakai</span>
                      <?php endif; ?>
                    </td>
                    <td><?= esc($row['staff']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">Belum ada laporan</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>


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