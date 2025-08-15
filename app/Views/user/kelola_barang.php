<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>

<main class="px-6 py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Kelola Barang</h1>
        <button type="button" onclick="openModalTambah()" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">Tambah</button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <form method="get" action="">
            <input type="text" name="q" placeholder="Search..." class="px-3 py-2 border border-gray-300 rounded w-64 focus:outline-none focus:ring focus:ring-blue-200 text-sm" value="<?= esc($_GET['q'] ?? '') ?>" />
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
                    <?php $no = 1;

                    foreach ($barangList as $barang): ?>
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
                                <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($barang), ENT_QUOTES, 'UTF-8') ?>)" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                <!-- Download -->
                                <form action="<?= base_url('user/download_barcode/' . $barang['id_barang']) ?>" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <button title="Download" class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                <!-- Hapus -->
                                <form action="<?= base_url('user/hapus_barang/' . $barang['id_barang']) ?>" method="post" class="inline" onsubmit="return confirm('Yakin hapus barang ini?')">
                                    <?= csrf_field() ?>
                                    <button title="Hapus" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition">
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
    </div>

    <!-- Modal update Tambah -->
    <div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 p-6 relative">
            <button type="button" onclick="closeModal('modalTambah')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Tambah Barang</h2>
            <form id="formTambahBarang" action="<?= base_url('user/simpan_barang') ?>" method="post" enctype="multipart/form-data" class="grid grid-cols-3 gap-6">
                <?= csrf_field() ?>
                <!-- Form Kiri -->
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Nama barang</label>
                        <input type="text" name="nama_barang" id="inputNamaBarang" placeholder="Masukkan nama barang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Jumlah</label>
                        <input type="number" name="jumlah" id="inputJumlah" placeholder="Masukkan jumlah barang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Satuan</label>
                        <input type="text" name="satuan" id="inputSatuan" placeholder="Masukkan satuan barang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="inputTanggalMasuk" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Minimum Stok</label>
                        <input type="number" name="minimum_stok" id="inputMinimumStok" placeholder="Minimum stok" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <!-- Form Barcode -->
                <div class="col-span-1 flex flex-col items-center justify-center border rounded p-4">
                    <label class="block text-sm mb-2">Barcode (QR Code)</label>
                    <div id="qrcode" class="w-32 h-32 flex items-center justify-center bg-gray-100 border mb-3"></div>
                    <button type="button" onclick="generateBarcode()" class="bg-gray-800 text-white px-4 py-2 rounded mb-2">Generate</button>
                    <input type="text" name="barcode" id="barcodeInput" class="w-full border rounded px-3 py-2 text-center" readonly required>
                </div>
                <!-- Tombol -->
                <div class="col-span-3 flex justify-end gap-2 mt-4">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Tambah</button>
                    <button type="button" onclick="closeModal('modalTambah')" class="bg-gray-300 text-black px-4 py-2 rounded">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-3/4 p-6 relative">
            <button type="button" onclick="closeModal('modalEdit')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Barang</h2>
            <form id="formEditBarang" action="" method="post" enctype="multipart/form-data" class="grid grid-cols-3 gap-6">
                <?= csrf_field() ?>
                <input type="hidden" name="id_barang" id="editIdBarang">
                <!-- Form Kiri -->
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Nama barang</label>
                        <input type="text" name="nama_barang" id="editNamaBarang" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Jumlah</label>
                        <input type="number" name="jumlah" id="editJumlah" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Satuan</label>
                        <input type="text" name="satuan" id="editSatuan" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="editTanggalMasuk" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Minimum Stok</label>
                        <input type="number" name="minimum_stok" id="editMinimumStok" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <!-- Form Barcode -->
                <div class="col-span-1 flex flex-col items-center justify-center border rounded p-4">
                    <label class="block text-sm mb-2">Barcode (QR Code)</label>
                    <div id="editQrcode" class="w-32 h-32 flex items-center justify-center bg-gray-100 border mb-3"></div>
                    <input type="text" name="barcode" id="editBarcode" class="w-full border rounded px-3 py-2 text-center" readonly required>
                </div>
                <!-- Tombol -->
                <div class="col-span-3 flex justify-end gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <button type="button" onclick="closeModal('modalEdit')" class="bg-gray-300 text-black px-4 py-2 rounded">Batal</button>
                </div>
            </form>
        </div>
    </div>

</main>

<!-- SCRIPT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function openModalTambah() {
        document.getElementById('modalTambah').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
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
        new QRCode(document.getElementById('editQrcode'), {
            text: barang.barcode,
            width: 120,
            height: 120
        });
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
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
        new QRCode(document.getElementById('qrcode'), {
            text: barcode,
            width: 120,
            height: 120
        });
    }
</script>

<?= $this->endSection() ?>