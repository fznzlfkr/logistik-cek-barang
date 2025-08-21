<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>

<main class="px-6 py-8">
  <h1 class="text-xl font-bold mb-4">Riwayat</h1>
  <?php if (session()->getFlashdata('error')): ?>
    <div id="errorAlert" class="error-message">
      <?= session()->getFlashdata('error'); ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div id="successAlert" class="success-message">
      <?= session()->getFlashdata('success'); ?>
    </div>
  <?php endif; ?>

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
              <td class="p-3"><?= date('d-m-Y H:i:s', strtotime($riwayat['tanggal'])) ?></td>
              <td class="p-3"><?= $riwayat['nama_barang'] ?></td>
              <td class="p-3"><?= $riwayat['jumlah'] ?></td>
              <td class="p-3"><?= $riwayat['jenis'] ?></td>
              <td class="p-3"><?= $riwayat['nama'] ?></td>
              <td class="p-3 text-center space-x-1">
                <form action="<?= base_url('user/edit-laporan/' . $riwayat['id_laporan']) ?>" method="post" class="inline">
                  <?= csrf_field() ?>
                  <button title="Edit" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition">
                    <i data-feather="edit" class="w-4 h-4"></i>
                  </button>
                </form>
                <button type="button" data-id="<?= $riwayat['id_laporan'] ?>" class="btn-print inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded transition" title="Print">
                  <i data-feather="printer" class="w-4 h-4"></i>
                </button>
                <form action="<?= base_url('user/hapus-riwayat/' . $riwayat['id_laporan']) ?>"
                  method="post"
                  class="form-hapus inline">
                  <?= csrf_field() ?>
                  <button type="submit"
                    class="btn-hapus inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition"
                    title="Hapus">
                    <i data-feather="trash" class="w-4 h-4"></i>
                  </button>
                </form>
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

    <div class="flex items-center justify-center gap-2 mt-4">
      <?php if ($pager): ?>
        <div class="flex items-center space-x-1">
          <?= $pager->simpleLinks('riwayat', 'tailwind_pagination') ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<!-- Modal for Edit -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded shadow-lg p-6 w-96">
    <h2 class="text-lg font-bold mb-4">Edit Laporan</h2>
    <form id="editForm" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id_laporan" id="editIdLaporan">
      <div class="mb-4">
        <label for="editNamaBarang" class="block text-sm font-medium">Nama Barang</label>
        <select name="nama_barang" id="editNamaBarang" class="w-full px-3 py-2 border rounded" required>
          <option value="" disabled>Pilih Barang</option>
          <?php foreach ($uniqueBarang as $barang): ?>
            <option value="<?= esc($barang['nama_barang']) ?>">
              <?= esc($barang['nama_barang']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-4">
        <label for="editJumlah" class="block text-sm font-medium">Jumlah</label>
        <input type="number" name="jumlah" id="editJumlah" class="w-full px-3 py-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label for="editJenis" class="block text-sm font-medium">Jenis</label>
        <select name="jenis" id="editJenis" class="w-full px-3 py-2 border rounded" required>
          <option value="Masuk">Masuk</option>
          <option value="Dipakai">Dipakai</option>
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" id="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal for Print -->
<div id="printModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-6 w-96">
    <h2 class="text-lg font-bold mb-4">Pilih Format Print</h2>
    <form id="printForm" method="post" class="space-y-4">
      <?= csrf_field() ?>
      <input type="hidden" name="id_laporan" id="printIdLaporan">

      <div>
        <label class="block text-sm font-medium mb-2">Pilih format</label>
        <div class="grid grid-cols-2 gap-4">
          <label class="format-card cursor-pointer border rounded-lg p-4 flex flex-col items-center justify-center transition hover:border-green-500">
            <input type="radio" name="format" value="excel" class="hidden formatOption">
            <img src="../assets/img/excel.png" alt="Excel" class="mb-2 opacity-70">
            <span class="text-sm font-medium">Excel</span>
          </label>
          <label class="format-card cursor-pointer border rounded-lg p-4 flex flex-col items-center justify-center transition hover:border-green-500">
            <input type="radio" name="format" value="pdf" class="hidden formatOption">
            <img src="../assets/img/pdf.png" alt="PDF" class="mb-2 opacity-70">
            <span class="text-sm font-medium">PDF</span>
          </label>
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" id="closePrintModal"
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
        <button type="submit" id="btnPrint" disabled
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">Print</button>
      </div>
    </form>
  </div>
</div>

<style>
  /* Efek highlight saat dipilih */
  .format-card input:checked+img,
  .format-card input:checked+img+span {
    opacity: 1;
  }

  .format-card input:checked+img {
    filter: drop-shadow(0 0 5px #22c55e);
  }

  .format-card:has(input:checked) {
    border-color: #22c55e;
    background-color: #f0fdf4;
  }
</style>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Edit Modal
    const editButtons = document.querySelectorAll('button[title="Edit"]');
    const editModal = document.getElementById('editModal');
    const closeModal = document.getElementById('closeModal');
    const editForm = document.getElementById('editForm');
    const editIdLaporan = document.getElementById('editIdLaporan');
    const editNamaBarang = document.getElementById('editNamaBarang');
    const editJumlah = document.getElementById('editJumlah');
    const editJenis = document.getElementById('editJenis');

    editButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const idLaporan = button.closest('form').action.split('/').pop();
        const row = button.closest('tr');
        const namaBarang = row.querySelector('td:nth-child(3)').textContent.trim();
        const jumlah = row.querySelector('td:nth-child(4)').textContent.trim();
        const jenis = row.querySelector('td:nth-child(5)').textContent.trim();

        editIdLaporan.value = idLaporan;
        editNamaBarang.value = namaBarang;
        editJumlah.value = jumlah;
        editJenis.value = jenis;

        editForm.action = `<?= base_url('user/edit-riwayat') ?>/${idLaporan}`;
        editModal.classList.remove('hidden');
      });
    });

    closeModal.addEventListener('click', () => {
      editModal.classList.add('hidden');
    });

    // Print Modal
    const printButtons = document.querySelectorAll('.btn-print');
    const printModal = document.getElementById('printModal');
    const closePrintModal = document.getElementById('closePrintModal');
    const printForm = document.getElementById('printForm');
    const printIdLaporan = document.getElementById('printIdLaporan');
    const btnPrint = document.getElementById('btnPrint');
    const formatOptions = document.querySelectorAll('.formatOption');

    printButtons.forEach(button => {
      button.addEventListener('click', () => {
        const idLaporan = button.getAttribute('data-id');
        printIdLaporan.value = idLaporan;
        btnPrint.disabled = true;
        formatOptions.forEach(opt => opt.checked = false);
        printForm.action = `<?= base_url('user/print-riwayat') ?>/${idLaporan}`;
        printModal.classList.remove('hidden');
      });
    });

    formatOptions.forEach(option => {
      option.addEventListener('change', () => {
        btnPrint.disabled = !document.querySelector('.formatOption:checked');
      });
    });

    closePrintModal.addEventListener('click', () => {
      printModal.classList.add('hidden');
    });
  });
</script>

<?= $this->endSection() ?>