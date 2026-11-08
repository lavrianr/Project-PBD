<?php
require_once '../config/database.php';

$stmt_satuan = $pdo->query("SELECT * FROM satuan WHERE status = 1 ORDER BY nama_satuan ASC");
$satuan = $stmt_satuan->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $idsatuan = $_POST['idsatuan'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES (:jenis, :nama, :idsatuan, :status, :harga)");
    $stmt->execute([
        ':jenis' => $jenis,
        ':nama' => $nama,
        ':idsatuan' => $idsatuan,
        ':status' => $status,
        ':harga' => $harga
    ]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Tambah Barang Baru</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama" required placeholder="Contoh: Pensil 2B">
        </div>
        <div class="form-group">
            <label>Jenis Barang</label>
            <select name="jenis" required>
                <option value="Makanan">Makanan</option>
                <option value="Non-Makanan">Non-Makanan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Satuan:</label>
            <select name="idsatuan" required>
                <option value="">-- Pilih Satuan --</option>
                <?php foreach ($satuan as $s): ?>
                    <option value="<?= $s['idsatuan'] ?>"><?=  htmlspecialchars($s['nama_satuan']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Harga Dasar (Rp):</label>
            <input type="number" name="harga" required min="0">
        </div>
        <div class="form-group">
            <label>Status:</label>
            <select name="status" required>
                <option value="1">Aktif</option>
                <option value="0">Non-Aktif</option>
            </select>
        </div>
        <button type="submit">Simpan Data</button>
        <a href="index.php" class="btn" style="background: #fff; color:#000; margin-left:10px;">Batal</a>
    </form>
</body>
</html>