<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>

<main class="px-6 py-8">
  <h1 class="text-xl font-bold mb-4">Riwayat</h1>

  <!-- Search -->
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
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($riwayatData)): ?>
          <?php foreach ($riwayatData as $index => $riwayat): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3"><?= $index + 1 ?></td>
              <td class="p-3"><?= date('d-m-Y H:i:s', strtotime($riwayat['tanggal'] . ' +7 hours')) ?></td>
              <td class="p-3"><?= $riwayat['nama_barang'] ?></td>
              <td class="p-3"><?= $riwayat['jumlah'] ?></td>
              <td class="p-3"><?= $riwayat['jenis'] ?></td>
              <td class="p-3"><?= $riwayat['nama'] ?></td>
              <td class="p-3 text-center space-x-1">
                <a href="<?= base_url('barang/edit/' . $riwayat['id_laporan']); ?>" title="Edit">
                  <i data-feather="edit" class="w-4 h-4 text-gray-600"></i>
                </a>
                <a href="<?= base_url('barang/download/' . $riwayat['id_laporan']); ?>" title="Download">
                  <i data-feather="download" class="w-4 h-4 text-gray-600"></i>
                </a>
                <a href="<?= base_url('barang/download/' . $riwayat['id_laporan']); ?>" title="Download">
                  <i data-feather="download" class="w-4 h-4 text-gray-600"></i>
                </a>
              </td>
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
    <!-- Rows per page -->
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

    <!-- Pagination Links -->
    <div class="flex items-center justify-center gap-2 mt-4">
      <?php if ($pager): ?>
        <div class="flex items-center space-x-1">
          <?= $pager->simpleLinks('riwayat', 'tailwind_pagination') ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?= $this->endSection() ?>