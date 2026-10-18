<?php
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_role");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Role</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Laporan Data Role</h2>
    <p><a href="../index.php" class="btn">&laquo; Kembali ke Menu Utama</a></p>

    <table>
        <thead>
            <tr>
                <th>ID Role</th>
                <th>Nama Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['idrole'] ?></td>
                    <td><?= htmlspecialchars($row['nama_role']); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="2" style="text-align: center;">Data kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>