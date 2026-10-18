<?php
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_barang");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Satuan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Laporan Data Barang</h2>
    <p><a href="../index.php" class="btn">&laquo; Kembali ke Menu Utama</a></p>

    <table>
        <thead>
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['idbarang'] ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= $row['jenis'] ?></td>
                    <td><?= htmlspecialchars($row['idsatuan']); ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= $row['status'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="6" style="text-align: center;">Data kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>