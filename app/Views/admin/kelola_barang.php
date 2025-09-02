<?= $this->extend('layout/templateAdmin') ?>
<?= $this->section('content') ?>


<!-- Main Content -->
<main class="main-content p-6 md:p-8 lg:p-10"> 
    <!-- Flash Message -->
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
            <input type="text" name="keyword" value="<?= esc(service('request')->getVar('keyword')) ?>" placeholder="Search..."
                class="px-3 py-2 border border-gray-300 rounded w-64 focus:outline-none focus:ring focus:ring-blue-200 text-sm" />
            <button type="submit" class="px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">Cari</button>
            <?php if (service('request')->getVar('keyword') || service('request')->getVar('per_page')): ?>
                <a href="<?= current_url() ?>" class="px-3 py-2 bg-gray-300 text-sm rounded hover:bg-gray-400 transition">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="table">
        <table class="min-w-full text-sm bg-white rounded shadow">
            <thead class="bg-gray-200 text-gray-700 font-semibold">
            <tr>
                <th class="p-3 text-left">No</th>
                <th class="p-3 text-left">Nama Barang</th>
                <th class="p-3 text-left">Jumlah</th>
                <th class="p-3 text-left">Satuan</th>
                <th class="p-3 text-left">Tgl Masuk</th>
                <th class="p-3 text-left">Barcode</th>
                <th class="p-3 text-left">Min Stok</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($barangList)): ?>
                <?php $no = 1; foreach ($barangList as $barang): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><?= $no++ ?></td>
                        <td class="p-3"><?= esc($barang['nama_barang']) ?></td>
                        <td class="p-3"><?= esc($barang['jumlah']) ?></td>
                        <td class="p-3"><?= esc($barang['satuan']) ?></td>
                        <td class="p-3"><?= esc($barang['tanggal_masuk']) ?></td>
                        <td class="p-3"><?= esc($barang['barcode']) ?></td>
                        <td class="p-3"><?= esc($barang['minimum_stok']) ?></td>
                        <td class="p-3 text-center space-x-2">
                            <!-- Edit -->
                            <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($barang), ENT_QUOTES, 'UTF-8') ?>)"
                                    class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition">
                                <i data-feather="edit" class="w-4 h-4"></i>
                            </button>
                            <!-- Download -->
                            <form action="<?= base_url('admin/download-barcode/' . $barang['id_barang']) ?>" method="post" class="inline">
                                <?= csrf_field() ?>
                                <button title="Download"
                                        class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <!-- Hapus -->
                            <form action="<?= base_url('admin/hapus-barang/' . $barang['id_barang']) ?>" method="post" class="form-hapus inline">
                                <?= csrf_field() ?>
                                <button title="Hapus" type="submit"
                                        class="btn-hapus inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition">
                                    <i data-feather="trash" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center py-4">Tidak ada data barang.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
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

    <!-- Modal Tambah Barang -->
<!-- Modal Tambah Barang -->
<div id="modalTambah" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 z-[9999] flex items-center justify-center">

  <!-- Konten Modal -->
  <div class="bg-white rounded-lg shadow-lg p-6 relative w-full max-w-5xl">

    <!-- Tombol Close -->
    <button type="button" onclick="closeModal('modalTambah')" 
      class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>

    <!-- Header -->
    <h2 class="text-lg font-bold mb-6">Barang Masuk</h2>

    <!-- Form -->
    <form action="<?= base_url('admin/tambah-barang') ?>" method="post">
      <?= csrf_field() ?>

      <div class="grid grid-cols-3 gap-6">
        <!-- Kolom kiri (form input) -->
        <div class="col-span-2 grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nama Barang</label>
            <input type="text" name="nama_barang" placeholder="Masukkan nama barang baru"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-gray-200" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Jumlah</label>
            <input type="number" name="jumlah" placeholder="Masukkan jumlah barang"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-gray-200" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Satuan</label>
            <input type="text" name="satuan" placeholder="Masukkan satuan barang"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-gray-200" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-gray-200" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Minimum Stok</label>
            <input type="number" name="minimum_stok" placeholder="Minimum stok"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-gray-200" required>
          </div>
        </div>

        <!-- Kolom kanan (barcode) -->
        <div class="border rounded p-4 flex flex-col items-center justify-center">
          <label class="block text-sm font-medium mb-3">Barcode (QR Code)</label>
          <div id="qrcode" class="w-32 h-32 flex items-center justify-center bg-gray-100 border mb-3"></div>
          <button type="button" onclick="generateBarcode()" 
            class="bg-gray-800 text-white px-4 py-2 rounded mb-2">Generate</button>
          <input type="text" name="barcode" id="barcodeInput"
            class="w-full border rounded px-3 py-2 text-center" readonly required>
        </div>
      </div>

      <!-- Tombol -->
      <div class="mt-6 flex justify-end space-x-3">
        <button type="submit"
          class="px-5 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">Tambah</button>
        <button type="button" onclick="closeModal('modalTambah')"
          class="px-5 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</button>
      </div>
    </form>
  </div>
</div>






    <!-- Modal Barang Keluar -->
    <div id="modalKeluar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 p-8 relative">
            <button type="button" onclick="closeModal('modalKeluar')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Barang Dipakai</h2>
            <form action="<?= base_url('admin/barang_keluar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Pilih Barang</label>
                        <select name="id_barang" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($barangList as $barang): ?>
                                <option value="<?= $barang['id_barang'] ?>"><?= esc($barang['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEdit" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 z-50 flex items-start justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 relative mt-24
                    w-[calc(100%-250px)] ml-[250px] max-w-5xl">
            
            <!-- Tombol Close -->
            <button type="button" onclick="closeModal('modalEdit')" 
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            
            <!-- Header -->
            <h2 class="text-xl font-semibold mb-4">Edit Barang</h2>
            
            <!-- Form -->
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_barang" id="edit_id_barang">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" id="edit_nama_barang" 
                            class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="edit_jumlah" 
                            class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" 
                            class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" 
                            class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Minimum Stok</label>
                        <input type="number" name="minimum_stok" id="edit_minimum_stok" 
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                </div>
                
                <!-- Tombol -->
                <div class="mt-4 flex justify-end">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>

<!-- Feather Icons -->
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script> feather.replace(); </script>

<!-- Script Modal & Alert -->
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
function openModalEdit(data) {
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('formEdit').action = "<?= base_url('admin/update-barang/') ?>" + data.id_barang;
    document.getElementById('edit_id_barang').value = data.id_barang;
    document.getElementById('edit_nama_barang').value = data.nama_barang;
    document.getElementById('edit_jumlah').value = data.jumlah;
    document.getElementById('edit_satuan').value = data.satuan;
    document.getElementById('edit_tanggal_masuk').value = data.tanggal_masuk;
    document.getElementById('edit_minimum_stok').value = data.minimum_stok;
    
}


// Auto hide alert
setTimeout(() => {
    let errorAlert = document.getElementById('errorAlert');
    let successAlert = document.getElementById('successAlert');
    if (errorAlert) errorAlert.style.display = 'none';
    if (successAlert) successAlert.style.display = 'none';
}, 3000);
</script>

<?= $this->endSection() ?>
