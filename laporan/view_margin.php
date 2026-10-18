<?php
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_margin");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View User</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Laporan Margin Penjualan</h2>
    <p><a href="../index.php" class="btn">&laquo; Kembali ke Menu Utama</a></p>

    <table>
        <thead>
            <tr>
                <th>ID Margin</th>
                <th>Persen</th>
                <th>Tanggal Dibuat</th>
                <th>Dibuat oleh</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['idmargin_penjualan'] ?></td>
                    <td><?= $row['persen'] ?>%</td>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= htmlspecialchars($row['dibuat_oleh'] ?? '-'); ?></td>
                    <td><?= $row['status'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="5" style="text-align: center;">Data kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>