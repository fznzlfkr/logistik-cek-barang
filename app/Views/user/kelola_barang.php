<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>

<main class="px-8 py-10 max-w-full mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Kelola Barang</h1>
        <div class="flex space-x-3">
            <button type="button" onclick="openModal('modalTambah')" class="bg-gray-800 text-white px-6 py-3 rounded hover:bg-gray-700 text-base">Barang Masuk</button>
            <button type="button" onclick="openModal('modalKeluar')" class="bg-gray-800 text-white px-6 py-3 rounded hover:bg-gray-700 text-base">Barang Dipakai</button>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if (session()->getFlashdata('error')): ?>
        <div id="errorAlert" class="error-message mb-6">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div id="successAlert" class="success-message mb-6">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="mb-6 flex items-center gap-3">
        <form method="get" class="flex items-center gap-3">
            <input type="text" name="keyword" value="<?= esc(service('request')->getVar('keyword')) ?>" placeholder="Search..."
                class="px-4 py-3 border border-gray-300 rounded w-80 focus:outline-none focus:ring focus:ring-blue-200 text-base" />
            <button type="submit" class="px-4 py-3 bg-blue-500 text-white text-base rounded hover:bg-blue-600 transition">Cari</button>
            <?php if (service('request')->getVar('keyword') || service('request')->getVar('per_page')): ?>
                <a href="<?= current_url() ?>" class="px-4 py-3 bg-gray-300 text-base rounded hover:bg-gray-400 transition">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full text-base bg-white rounded shadow-lg">
            <thead class="bg-gray-200 text-gray-700 font-semibold">
            <tr>
                <th class="p-4 text-left">No</th>
                <th class="p-4 text-left">Nama Barang</th>
                <th class="p-4 text-left">Jumlah</th>
                <th class="p-4 text-left">Satuan</th>
                <th class="p-4 text-left">Tgl Masuk</th>
                <th class="p-4 text-left">Barcode</th>
                <th class="p-4 text-left">Min Stok</th>
                <th class="p-4 text-center">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($barangList)): ?>
                <?php $no = 1; foreach ($barangList as $barang): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-4"><?= $no++ ?></td>
                        <td class="p-4"><?= esc($barang['nama_barang']) ?></td>
                        <td class="p-4"><?= esc($barang['jumlah']) ?></td>
                        <td class="p-4"><?= esc($barang['satuan']) ?></td>
                        <td class="p-4"><?= esc($barang['tanggal_masuk']) ?></td>
                        <td class="p-4"><?= esc($barang['barcode']) ?></td>
                        <td class="p-4"><?= esc($barang['minimum_stok']) ?></td>
                        <td class="p-4 text-center space-x-2">
                            <!-- Edit -->
                            <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($barang), ENT_QUOTES, 'UTF-8') ?>)"
                                    class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition">
                                <i data-feather="edit" class="w-5 h-5"></i>
                            </button>
                            <!-- Download -->
                            <form action="<?= base_url('user/download_barcode/' . $barang['id_barang']) ?>" method="post" class="inline">
                                <?= csrf_field() ?>
                                <button title="Download"
                                        class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                    <i data-feather="download" class="w-5 h-5"></i>
                                </button>
                            </form>
                            <!-- Hapus -->
                            <form action="<?= base_url('user/hapus_barang/' . $barang['id_barang']) ?>" method="post" class="form-hapus inline">
                                <?= csrf_field() ?>
                                <button title="Hapus" type="submit"
                                        class="btn-hapus inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition">
                                    <i data-feather="trash" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center py-6">Tidak ada data barang.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Pagination -->
    <div class="flex justify-between items-center mt-6 text-base">
        <div class="flex items-center gap-3">
            <span>Rows per page</span>
            <form method="get">
                <input type="hidden" name="keyword" value="<?= esc($keyword) ?>" />
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 px-3 py-2 rounded">
                    <option value="5" <?= ($perPage == 5) ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= ($perPage == 10) ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($perPage == 25) ? 'selected' : '' ?>>25</option>
                </select>
            </form>
        </div>
        <div class="flex items-center justify-center gap-3 mt-6">
            <?php if ($pager): ?>
                <div class="flex items-center space-x-2">
                    <?= $pager->simpleLinks('number', 'tailwind_pagination') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Modal Tambah (Barang Masuk) -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-4/5 max-w-6xl p-8 relative">
        <button type="button" onclick="closeModal('modalTambah')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-6">Barang Masuk</h2>

        <!-- Box Pesan -->
        <div id="msgBox" class="hidden mb-6 p-4 rounded text-base"></div>

        <form id="formTambahBarang" action="<?= base_url('user/barang_masuk/save') ?>" method="post" enctype="multipart/form-data" class="grid grid-cols-3 gap-8">
            <?= csrf_field() ?>
            <!-- Form Kiri -->
            <div class="col-span-2 grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-base mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="inputNamaBarang" placeholder="Masukkan nama barang baru" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Jumlah</label>
                    <input type="number" name="jumlah" id="inputJumlah" placeholder="Masukkan jumlah barang" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Satuan</label>
                    <input type="text" name="satuan" id="inputSatuan" placeholder="Masukkan satuan barang" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="inputTanggalMasuk" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Minimum Stok</label>
                    <input type="number" name="minimum_stok" id="inputMinimumStok" placeholder="Minimum stok" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
            </div>
            <!-- Form Barcode -->
            <div class="col-span-1 flex flex-col items-center justify-center border rounded p-6">
                <label class="block text-base mb-3">Barcode (QR Code)</label>
                <div id="qrcode" class="w-40 h-40 flex items-center justify-center bg-gray-100 border mb-4"></div>
                <button type="button" onclick="generateBarcode()" class="bg-gray-800 text-white px-6 py-3 rounded mb-3 text-base">Generate</button>
                <input type="text" name="barcode" id="barcodeInput" class="w-full border rounded px-4 py-3 text-center text-base" readonly required>
            </div>
            <!-- Tombol -->
            <div class="col-span-3 flex justify-end gap-3 mt-6">
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded text-base">Tambah</button>
                <button type="button" onclick="closeModal('modalTambah')" class="bg-gray-300 text-black px-6 py-3 rounded text-base">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Barang Keluar -->
<div id="modalKeluar" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-4/5 max-w-4xl p-8 relative">
        <button type="button" onclick="closeModal('modalKeluar')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-6">Barang Dipakai</h2>
        <form id="formBarangKeluar" action="<?= base_url('user/barang_keluar/save') ?>" method="post" class="grid grid-cols-2 gap-8">
            <?= csrf_field() ?>
            <!-- Pilih Barang -->
            <div>
                <label for="id_barang" class="block text-base font-medium mb-2">Pilih Barang</label>
                <select name="id_barang" id="id_barang" class="w-full border rounded px-4 py-3 text-base" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($uniqueBarang as $barang): ?>
                        <option value="<?= $barang['id_barang'] ?>">
                            <?= esc($barang['nama_barang']) ?> (Stok: <?= esc($barang['jumlah']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Jumlah Keluar -->
            <div>
                <label for="jumlah" class="block text-base font-medium mb-2">Jumlah Dipakai</label>
                <input type="number" name="jumlah" id="jumlah" placeholder="Masukkan jumlah Dipakai" class="w-full border rounded px-4 py-3 text-base" min="1" required>
            </div>
            <!-- Tanggal Keluar -->
            <div>
                <label for="tanggal" class="block text-base font-medium mb-2">Tanggal Dipakai</label>
                <input type="date" name="tanggal" id="tanggal" class="w-full border rounded px-4 py-3 text-base" required>
            </div>
            <!-- Tombol -->
            <div class="col-span-2 flex justify-end gap-3 mt-6">
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded hover:bg-gray-900 text-base">Simpan</button>
                <button type="button" onclick="closeModal('modalKeluar')" class="bg-gray-300 text-black px-6 py-3 rounded hover:bg-gray-400 text-base">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-4/5 max-w-6xl p-8 relative">
        <button type="button" onclick="closeModal('modalEdit')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-6">Edit Barang</h2>
        <form id="formEditBarang" action="" method="post" enctype="multipart/form-data" class="grid grid-cols-3 gap-8">
            <?= csrf_field() ?>
            <input type="hidden" name="id_barang" id="editIdBarang">
            <!-- Form Kiri -->
            <div class="col-span-2 grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-base mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="editNamaBarang" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Jumlah</label>
                    <input type="number" name="jumlah" id="editJumlah" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Satuan</label>
                    <input type="text" name="satuan" id="editSatuan" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="editTanggalMasuk" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
                <div>
                    <label class="block text-base mb-2">Minimum Stok</label>
                    <input type="number" name="minimum_stok" id="editMinimumStok" class="w-full border rounded px-4 py-3 text-base" required>
                </div>
            </div>
            <!-- Form Barcode -->
            <div class="col-span-1 flex flex-col items-center justify-center border rounded p-6">
                <label class="block text-base mb-3">Barcode (QR Code)</label>
                <div id="editQrcode" class="w-40 h-40 flex items-center justify-center bg-gray-100 border mb-4"></div>
                <input type="text" name="barcode" id="editBarcode" class="w-full border rounded px-4 py-3 text-center text-base" readonly required>
            </div>
            <!-- Tombol -->
            <div class="col-span-3 flex justify-end gap-3 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded text-base">Update</button>
                <button type="button" onclick="closeModal('modalEdit')" class="bg-gray-300 text-black px-6 py-3 rounded text-base">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    function openModalEdit(barang) {
        document.getElementById('modalEdit').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('formEditBarang').action = "<?= base_url('user/update_barang') ?>/" + barang.id_barang;
        document.getElementById('editIdBarang').value = barang.id_barang;
        document.getElementById('editNamaBarang').value = barang.nama_barang;
        document.getElementById('editJumlah').value = barang.jumlah;
        document.getElementById('editSatuan').value = barang.satuan;
        document.getElementById('editTanggalMasuk').value = barang.tanggal_masuk;
        document.getElementById('editMinimumStok').value = barang.minimum_stok;
        document.getElementById('editBarcode').value = barang.barcode;
        document.getElementById('editQrcode').innerHTML = '';
        new QRCode(document.getElementById('editQrcode'), { text: barang.barcode, width: 120, height: 120 });
    }
    function generateBarcode() {
        const nama = document.getElementById('inputNamaBarang').value;
        if (!nama) {
            alert('Isi nama barang terlebih dahulu!');
            return;
        }
        const prefix = nama.substring(0, 3).toUpperCase();
        const rand = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
        const barcode = `BC${prefix}${rand}`;
        document.getElementById('barcodeInput').value = barcode;
        document.getElementById('qrcode').innerHTML = '';
        new QRCode(document.getElementById('qrcode'), { text: barcode, width: 120, height: 120 });
    }
    $("#formTambahBarang").on("submit", function(e) {
    e.preventDefault();

    let nama_barang = $("#inputNamaBarang").val();
    let barcode     = $("#barcodeInput").val();
    let form        = $(this);
    let msgBox      = $("#msgBox");

    $.post("<?= base_url('user/barang_masuk/cekBarang') ?>", 
        { nama_barang, barcode }, 
        function(res) {
            if (res.status === "error") {
                msgBox
                    .removeClass("hidden bg-green-100 text-green-700 border-green-400")
                    .addClass("bg-red-100 text-red-700 border border-red-400")
                    .text(res.message)
                    .show();
            } else {
                msgBox.hide();
                form.off("submit").submit(); // lanjut submit form
            }
        }, "json"
    );
});
</script>

<?= $this->endSection() ?>