<?= $this->extend('layout/templateAdmin') ?>
<?= $this->section('content') ?>

<!-- Header (tanpa gap luar, full width) -->
<div class="header">
  <div class="header-content flex justify-around">
    <div class="header-title">
      <h1 class="text-2xl font-bold text-gray-900">Kelola Barang</h1>
      <p class="text-gray-600">Selamat datang kembali, <?= esc($admin['nama']) ?>!</p>
    </div>
    <div class="header-actions">
      <?php
        $nama = trim($admin['nama']);
        $parts = explode(" ", $nama);
        if (count($parts) >= 2) {
            $avatar = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        } else {
            $avatar = strtoupper(substr($nama, 0, 2));
        }
      ?>
      <div class="user-profile flex items-center gap-3">
        <div class="user-avatar bg-gray-800 text-white rounded-full w-10 h-10 flex items-center justify-center font-semibold">
          <?= $avatar ?>
        </div>
        <a href="<?= base_url('admin/pengaturan-akun') ?>" class="a-info">
          <div class="user-info">
            <h6 class="font-semibold"><?= esc($admin['nama']) ?></h6>
            <p class="text-sm text-gray-500"><?= esc($admin['role']) ?></p>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<main class="main-content p-6 md:p-8 lg:p-10"> 
    <!-- Kelola Barang -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Data Barang</h1>
        <div class="flex space-x-2">
            <button type="button" onclick="openModal('modalTambah')" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">Barang Masuk</button>
            <button type="button" onclick="openModal('modalKeluar')" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">Barang Dipakai</button>
        </div>
    </div>

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
    <div class="overflow-x-auto">
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
                            <form action="<?= base_url('admin/download_barcode/' . $barang['id_barang']) ?>" method="post" class="inline">
                                <?= csrf_field() ?>
                                <button title="Download"
                                        class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <!-- Hapus -->
                            <form action="<?= base_url('admin/hapus_barang/' . $barang['id_barang']) ?>" method="post" class="form-hapus inline">
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
    <div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 p-8 relative">
            <button type="button" onclick="closeModal('modalTambah')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Barang Masuk</h2>
            <form action="<?= base_url('admin/tambah_barang') ?>" method="post">
                <?= csrf_field() ?>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Minimum Stok</label>
                        <input type="number" name="minimum_stok" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
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
    <div id="modalEdit" class="hidden fixed inset-0 bg-gray-600 bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 p-8 relative">
            <button type="button" onclick="closeModal('modalEdit')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Barang</h2>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_barang" id="edit_id_barang">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" id="edit_nama_barang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="edit_jumlah" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label>Minimum Stok</label>
                        <input type="number" name="minimum_stok" id="edit_minimum_stok" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update</button>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Feather Icons -->
<script src="https://unpkg.com/feather-icons"></script>
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
    document.getElementById('formEdit').action = "<?= base_url('admin/edit_barang/') ?>" + data.id_barang;
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
