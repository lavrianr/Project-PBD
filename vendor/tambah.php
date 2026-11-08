<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_vendor = $_POST['nama_vendor'];
    $badan_hukum = $_POST['badan_hukum'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (:nama, :bh, :status)");
    $stmt->execute([
        ':nama' => $nama_vendor,
        ':bh' => $badan_hukum,
        ':status' => $status,
    ]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Vendor</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Tambah Vendor Baru</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Vendor:</label>
            <input type="text" name="nama" required placeholder="Contoh: PT Chihuahua">
        </div>
        <div class="form-group">
            <label>Badan Hukum</label>
            <select name="badan_hukum" required>
                <option value="Y">Ya</option>
                <option value="N">Tidak</option>
            </select>
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