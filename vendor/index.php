e<?php
require_once '../config/database.php';

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM vendor WHERE idvendor = :id");
    $stmt->execute([':id' => $id]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM vendor ORDER BY idvendor DESC");
$data_vendor = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Vendor</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <p>
        <a href="tambah.php" class="btn">+ Tambah Vendor</a>
        <a href="../index.php" class="btn" style="background:#fff; color:#000;">Menu Utama</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Vendor</th>
                <th>Badan Hukum</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_vendor as $row): ?>
                <tr>
                    <td><?= $row['idvendor'] ?></td>
                    <td><?= htmlspecialchars($row['nama_vendor']); ?></td>
                    <td><?= $row['badan_hukum'] == 'Y' ? 'Ya' : 'Tidak'; ?></td>
                    <td><?= $row['status'] == 1 ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['idbarang'] ?>">Edit</a> |
                        <a href="index.php?hapus=<?= $row['idbarang'] ?>" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data_vendor)): ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada data tersedia.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>