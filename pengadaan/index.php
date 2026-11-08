<?php
$judul = 'Daftar Pengadaan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("SELECT * FROM v_pengadaan ORDER BY idpengadaan DESC");
$data_pengadaan = $stmt->fetchAll();
$stmt->closeCursor();
?>

<h2>Daftar Pengadaan Barang</h2>
<p>
    <a href="tambah.php" class="btn">+ Buat Pengadaan Baru</a>
    <a href="../index.php" class="btn" style="background: #fff; color: #000;">Menu Utama</a>
</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Vendor</th>
            <th>User</th>
            <th>Subtotal</th>
            <th>PPN</th>
            <th>Total Nilai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_pengadaan as $row): ?>
            <tr>
                <td>#<?= $row['idpengadaan'] ?></td>
                <td><?= $row['tanggal'] ?></td>
                <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
                <td><?= htmlspecialchars($row['pembuat']) ?></td>
                <td>Rp <?= number_format($row['subtotal_nilai'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($row['ppn'], 0, ',', '.') ?></td>
                <td><strong>Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></strong></td>
                <td><?= $row['status_teks'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($data_pengadaan)): ?>
            <tr><td colspan="8" style="text-align: center;">Belum ada transaksi pengadaan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>