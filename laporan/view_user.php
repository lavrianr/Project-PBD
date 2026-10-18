<?php
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_user");
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
    <h2>Laporan Data User</h2>
    <p><a href="../index.php" class="btn">&laquo; Kembali ke Menu Utama</a></p>

    <table>
        <thead>
            <tr>
                <th>ID User</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['iduser'] ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['idrole']); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="3" style="text-align: center;">Data kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>