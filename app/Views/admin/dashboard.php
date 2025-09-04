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
          <a href="<?= base_url('/log-aktivitas-user') ?>" class="card-action">View All</a>
        </div>

        <ul class="activity-list">
          <?php if (!empty($logsUser)): ?>
            <?php foreach ($logsUser as $log): ?>
              <li class="activity-item">
                <div class="activity-avatar">
                  <?= strtoupper(substr($log['nama_user'] ?? 'NA', 0, 2)); ?>
                </div>
                <div class="activity-content">
                  <h6><?= $log['nama_user']; ?></h6>
                  <p><?= $log['aktivitas']; ?></p>
                </div>
                <div class="activity-time"><?= $log['waktu_ago']; ?></div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="activity-item">
              <div class="activity-content">
                <p class="text-muted">Belum ada aktivitas.</p>
              </div>
            </li>
          <?php endif; ?>
        </ul>

      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>