<?php
$judul = 'Stok dan Harga Jual';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query(
    "SELECT idbarang, nama, harga,
            fn_stok_barang(idbarang) AS stok,
            fn_harga_jual(idbarang) AS harga_jual
    FROM barang WHERE status = 1 ORDER BY nama"
);
$rows = $stmt->fetchAll();
$stmt->closeCursor();
?>
<table>
    <tr><th>ID</th><th>Nama</th><th>Harga Dasar</th><th>Stok</th><th>Harga Jual</th></tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= $r['idbarang'] ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= number_format($r['harga']) ?></td>
            <td><?= $r['stok'] ?></td>
            <td><?= number_format($r['harga_jual']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>