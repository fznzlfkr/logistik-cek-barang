<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>
    <main class="px-6 py-8">
    <h1 class="text-xl font-bold mb-4">Riwayat</h1>

    <!-- Search -->
    <div class="mb-4">
      <input type="text" placeholder="Search..." class="px-3 py-2 border border-gray-300 rounded w-64 focus:outline-none focus:ring focus:ring-blue-200 text-sm" />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm bg-white rounded shadow">
        <thead class="bg-gray-200 text-gray-700 font-semibold">
          <tr>
            <th class="p-3 text-center"><input type="checkbox" /></th>
            <th class="p-3 text-left">No</th>
            <th class="p-3 text-left">Waktu</th>
            <th class="p-3 text-left">Jumlah</th>
            <th class="p-3 text-left">Jenis</th>
            <th class="p-3 text-left">Staff</th>
            <th class="p-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 1; $i <= 10; $i++): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3 text-center"><input type="checkbox" /></td>
            <td class="p-3"><?= $i ?></td>
            <td class="p-3">2025-08-0<?= $i ?></td>
            <td class="p-3"><?= rand(1, 100) ?></td>
            <td class="p-3"><?= $i % 2 == 0 ? 'Masuk' : 'Keluar' ?></td>
            <td class="p-3">Staff <?= $i ?></td>
            <td class="p-3 text-center space-x-2">
              <button title="Edit"><i data-feather="edit" class="w-4 h-4 text-gray-600"></i></button>
              <button title="Download"><i data-feather="download" class="w-4 h-4 text-gray-600"></i></button>
              <button title="Lainnya"><i data-feather="more-vertical" class="w-4 h-4 text-gray-600"></i></button>
            </td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

    <!-- Footer Table -->
    <div class="flex justify-between items-center mt-4 text-sm">
      <div class="flex items-center gap-2">
        <span>Rows per page</span>
        <select class="border border-gray-300 px-2 py-1 rounded">
          <option>3</option>
          <option>10</option>
          <option>25</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <span>1 - 10 of 406</span>
        <button><i data-feather="chevrons-left" class="w-4 h-4"></i></button>
        <button><i data-feather="chevron-left" class="w-4 h-4"></i></button>
        <button><i data-feather="chevron-right" class="w-4 h-4"></i></button>
        <button><i data-feather="chevrons-right" class="w-4 h-4"></i></button>
      </div>
    </div>
  </main>
<?= $this->endSection() ?>