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

    <!-- Tombol Cetak Laporan (buka modal) -->
    <button type="button" 
            onclick="openPrintModal()" 
            class="ml-auto px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
      Cetak Laporan
    </button>
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
            <div class="flex items-center justify-center gap-2 mt-4">
                <?php if ($pager): ?>
                    <div class="flex items-center space-x-1">
                        <?= $pager->simpleLinks('number', 'tailwind_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
</div>


<!-- Modal Memilih Format Cetak -->
<div id="printModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-6 w-96">
    <h2 class="text-lg font-bold mb-4">Pilih Format Print</h2>
    <form id="printForm" method="get" action="" class="space-y-4">
      <?= csrf_field() ?>
      <input type="hidden" name="keyword" value="<?= esc($keyword) ?>">
      <input type="hidden" name="per_page" value="<?= esc($perPage) ?>">

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
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
          Print
        </button>
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

<!-- Script Modal -->
<script>
  function openPrintModal() {
    document.getElementById("printModal").classList.remove("hidden");
  }

  document.addEventListener("DOMContentLoaded", function () {
    const printModal = document.getElementById("printModal");
    const closePrintModal = document.getElementById("closePrintModal");
    const formatOptions = document.querySelectorAll(".formatOption");
    const btnPrint = document.getElementById("btnPrint");
    const printForm = document.getElementById("printForm");

    // Tutup modal
    closePrintModal.addEventListener("click", () => {
      printModal.classList.add("hidden");
      formatOptions.forEach(opt => opt.checked = false);
      btnPrint.disabled = true;
    });

    // Ubah action sesuai pilihan format
    formatOptions.forEach(option => {
      option.addEventListener("change", () => {
        if (option.value === "excel") {
          printForm.action = "<?= base_url('admin/laporan/excel') ?>";
        } else if (option.value === "pdf") {
          printForm.action = "<?= base_url('admin/laporan/pdf') ?>";
        }
        btnPrint.disabled = false;
      });
    });
  });
</script>

<?= $this->endSection() ?>
