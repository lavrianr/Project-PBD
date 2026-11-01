<?php
$judul = 'Laporan Stok';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("CALL sp_laporan_stok()");
$rows = $stmt->fetchAll();
$stmt->closeCursor();
?>

<table>
    <tr><th>ID</th><th>Nama</th><th>Satuan</th><th>Stok</th><th>Harga</th><th>Harga Jual</th></tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= $r['idbarang'] ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['nama_satuan']) ?></td>
            <td><?= $r['stok'] ?></td>
            <td><?= number_format($r['harga']) ?></td>
            <td><?= number_format($r['harga_jual']) ?></td>
        </tr>
        <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>