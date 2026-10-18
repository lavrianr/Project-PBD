<?php
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_vendor");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Vendor</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Laporan Data Vendor</h2>
    <p><a href="../index.php" class="btn">&laquo; Kembali ke Menu Utama</a></p>

    <table>
        <thead>
            <tr>
                <th>ID Vendor</th>
                <th>Nama Vendor</th>
                <th>Badan Hukum</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['idvendor'] ?></td>
                    <td><?= htmlspecialchars($row['nama_vendor']); ?></td>
                    <td><?= $row['badan_hukum'] ?></td>
                    <td><?= $row['status'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="4" style="text-align: center;">Data kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>