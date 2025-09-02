<?= $this->extend('layout/templateAdmin') ?>
<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content p-5">
  <div class="mb-4 flex items-center gap-2">
    <form method="get" class="flex items-center gap-2">
      <input type="text" name="keyword" value="<?= esc(service('request')->getVar('keyword')) ?>"
        placeholder="Search..."
        class="px-3 py-2 border border-gray-300 rounded w-64 focus:outline-none focus:ring focus:ring-blue-200 text-sm" />

      <button type="submit"
        class="px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
        Cari
      </button>

      <?php if (service('request')->getVar('keyword') || service('request')->getVar('per_page')): ?>
        <a href="<?= current_url() ?>"
          class="px-3 py-2 bg-gray-300 text-sm rounded hover:bg-gray-400 transition">
          Reset
        </a>
      <?php endif; ?>
    </form>

    <!-- Tombol Cetak PDF (di kanan, warna biru) -->
    <a href="<?= base_url('admin/laporan/pdf') ?>?keyword=<?= urlencode($keyword ?? '') ?>&per_page=<?= urlencode($perPage ?? '') ?>"
       target="_blank"
       class="ml-auto px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
       Cetak PDF
    </a>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm bg-white rounded shadow">
      <thead class="bg-gray-200 text-gray-700 font-semibold">
        <tr>
          <th class="p-3 text-left">No</th>
          <th class="p-3 text-left">Waktu</th>
          <th class="p-3 text-left">Nama Barang</th>
          <th class="p-3 text-left">Jumlah</th>
          <th class="p-3 text-left">Jenis</th>
          <th class="p-3 text-left">Staff</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($riwayatData)): ?>
          <?php foreach ($riwayatData as $index => $riwayat): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3"><?= $index + 1 ?></td>
              <td class="p-3"><?= date('d-m-Y H:i:s', strtotime($riwayat['tanggal'])) ?></td>
              <td class="p-3"><?= $riwayat['nama_barang'] ?></td>
              <td class="p-3"><?= $riwayat['jumlah'] ?></td>
              <td class="p-3"><?= $riwayat['jenis'] ?></td>
              <td class="p-3"><?= $riwayat['nama'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="p-3 text-center">Tidak ada data riwayat.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer Pagination -->
  <div class="flex justify-between items-center mt-4 text-sm">
    <div class="flex items-center gap-2">
      <span>Rows per page</span>
      <form method="get">
        <input type="hidden" name="keyword" value="<?= esc($keyword) ?>" />
        <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 px-2 py-1 rounded">
          <option value="5" <?= ($perPage == 5) ? 'selected' : '' ?>>5</option>
          <option value="10" <?= ($perPage == 10) ? 'selected' : '' ?>>10</option>
          <option value="25" <?= ($perPage == 25) ? 'selected' : '' ?>>25</option>
        </select>
      </form>
    </div>
  </div>

<?= $this->endSection() ?>