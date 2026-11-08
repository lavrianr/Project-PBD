<?php
require_once '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM barang WHERE idbarang = :id");
$stmt->execute([':id' => $id]);
$barang = $stmt->fetch();

if (!$barang) {
    die("Data barang tidak ditemukan.");
}

$stmt_satuan = $pdo->query("SELECT * FROM satuan WHERE status = 1 ORDER BY nama_satuan ASC");
$satuan = $stmt_satuan->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis    = $_POST['jenis'];
    $nama     = $_POST['nama'];
    $idsatuan = $_POST['idsatuan'];
    $harga    = $_POST['harga'];
    $status   = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE barang SET jenis = :jenis, nama = :nama, idsatuan = :idsatuan, status = :status, harga = :harga WHERE idbarang = :id");
    $stmt->execute([
        ':jenis'    => $jenis,
        ':nama'     => $nama,
        ':idsatuan' => $idsatuan,
        ':status'   => $status,
        ':harga'    => $harga,
        ':id'       => $id
    ]);

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Edit Data Barang</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($barang['nama']); ?>" required>
        </div>
        <div class="form-group">
            <label>Jenis Barang:</label>
            <select name="jenis">
                <option value="Makanan" <?= $barang['jenis'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                <option value="Non-Makanan" <?= $barang['jenis'] == 'Non-Makanan' ? 'selected' : ''; ?>>Non-Makanan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Satuan:</label>
            <select name="idsatuan" required>
                <?php foreach ($satuan as $s): ?>
                    <option value="<?= $s['idsatuan'] ?>" <?= $barang['idsatuan'] == $s['idsatuan'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($s['nama_satuan']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Harga Dasar (Rp):</label>
            <input type="number" name="harga" value="<?= $barang['harga']; ?>" required>
        </div>
        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="1" <?= $barang['status'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                <option value="0" <?= $barang['status'] == 0 ? 'selected' : ''; ?>>Non-Aktif</option>
            </select>
        </div>
        <button type="submit">Update Data</button>
        <a href="index.php" class="btn" style="background:#fff; color:#000; margin-left:10px;">Batal</a>
    </form>
</body>
</html>