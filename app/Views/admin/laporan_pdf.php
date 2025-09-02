<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Barang</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; }
    table { border-collapse: collapse; width:100%; }
    th, td { border: 1px solid #333; padding:6px; text-align:left; }
    th { background:#eee; }
    h2 { margin-bottom:8px; }
  </style>
</head>
<body>
  <h2>Laporan Barang <?= (!empty($keyword)) ? (' - Pencarian: ' . esc($keyword)) : '' ?></h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Waktu</th>
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Jenis</th>
        <th>Staff</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($riwayatData)): ?>
        <?php foreach ($riwayatData as $i => $r): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= date('d-m-Y H:i:s', strtotime($r['tanggal'])) ?></td>
            <td><?= esc($r['nama_barang']) ?></td>
            <td><?= esc($r['jumlah']) ?></td>
            <td><?= esc($r['jenis']) ?></td>
            <td><?= esc($r['nama_user']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">Tidak ada data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>