<?php
require_once '../config/database.php';

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM barang WHERE idbarang = :id");
    $stmt->execute([':id' => $id]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT b.*, s.nama_satuan 
                    FROM barang b 
                    LEFT JOIN satuan s ON b.idsatuan = s.idsatuan 
                    ORDER BY b.idbarang DESC");
$data_barang = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Barang</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Data Barang</h2>
    <p>
        <a href="tambah.php" class="btn">+ Tambah Barang</a>
        <a href="../index.php" class="btn" style="background:#fff; color:#000;">Menu Utama</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Satuan</th>
                <th>Harga Dasar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_barang as $row): ?>
                <tr>
                    <td><?= $row['idbarang'] ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= htmlspecialchars($row['jenis']); ?></td>
                    <td><?= htmlspecialchars($row['nama_satuan'] ?? '-'); ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?= $row['status'] == 1 ? 'Aktif' : 'Non-Aktif'; ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['idbarang'] ?>">Edit</a> |
                        <a href="index.php?hapus=<?= $row['idbarang'] ?>" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data_barang)): ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada data tersedia.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>