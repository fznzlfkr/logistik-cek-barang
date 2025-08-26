<?= $this->extend('layout/templateUser'); ?>
<?= $this->section('content'); ?>
<!-- Main -->
<main class="px-6 py-6">
    <!-- Card statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <i data-feather="package" class="w-6 h-6 text-gray-600"></i>
            <div>
                <div class="text-sm text-gray-500">Barang Masuk</div>
                <div class="text-xl font-bold text-gray-800"><?= esc($totalMasuk) ?></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <i data-feather="arrow-down" class="w-6 h-6 text-gray-600"></i>
            <div>
                <div class="text-sm text-gray-500">Barang Dipakai</div>
                <div class="text-xl font-bold text-gray-800"><?= esc($totalDipakai) ?></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <i data-feather="box" class="w-6 h-6 text-gray-600"></i>
            <div>
                <div class="text-sm text-gray-500">Total Stok</div>
                <div class="text-xl font-bold text-gray-800"><?= esc($totalBarang) ?></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
            <i data-feather="alert-triangle" class="w-6 h-6 text-gray-600"></i>
            <div>
                <div class="text-sm text-gray-500">Stok Minimum</div>
                <div class="text-xl font-bold text-gray-800"><?= esc($barangMinimum) ?></div>
            </div>
        </div>
    </div>

    <!-- Riwayat table -->
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg font-semibold mb-4">Riwayat Terakhir</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-b text-gray-700 font-semibold">
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Nama Barang</th>
                        <th class="px-4 py-2">Jenis</th>
                        <th class="px-4 py-2">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporanData as $index => $laporan): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $index + 1 ?></td>
                            <td class="px-4 py-2"><?= date('d-m-Y H:i:s', strtotime($laporan['tanggal'])) ?></td>
                            <td class="px-4 py-2"><?= esc($laporan['nama_barang']) ?></td>
                            <td class="px-4 py-2"><?= esc($laporan['jenis']) ?></td>
                            <td class="px-4 py-2"><?= esc($laporan['jumlah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>