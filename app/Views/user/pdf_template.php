<h2 style="text-align:center;">Detail Barang</h2>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr><th>Nama Barang</th><td><?= esc($barang['nama_barang']) ?></td></tr>
    <tr><th>Jumlah</th><td><?= esc($barang['jumlah']) . ' ' . esc($barang['satuan']) ?></td></tr>
    <tr><th>Tanggal Masuk</th><td><?= esc($barang['tanggal_masuk']) ?></td></tr>
    <tr><th>Barcode</th><td><?= esc($barang['barcode']) ?></td></tr>
    <tr><th>Minimum Stok</th><td><?= esc($barang['minimum_stok']) ?></td></tr>
</table>
