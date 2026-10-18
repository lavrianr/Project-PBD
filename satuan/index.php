<?php
require_once '../config/database.php';

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM satuan WHERE id_satuan = :id");
    $stmt->execute([':id' => $id]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM satuan ORDER BY idsatuan DESC");
$data_satuan = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Satuan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Data Satuan Barang</h2>
    <p>
        <a href="tambah.php" class="btn">+ Tambah Satuan</a>
        <a href="../index.php" class="btn" style="background:#fff; color:#000;">&laquo; Menu Utama</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Satuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_satuan as $row): ?>
                <tr>
                    <td><?= $row['idsatuan'] ?></td>
                    <td><?= htmlspecialchars($row['nama_satuan']); ?></td>
                    <td><?= $row['status'] == 1 ? 'Aktif' : 'Non-Aktif' ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['idsatuan'] ?>">Edit</a> |
                        <a href="index.php?hapus=<?= $row['idsatuan'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data_satuan)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Belum ada data tersedia.</td>
                </tr>
                <?php endif; ?>
        </tbody>
    </table>
</body>
</html>