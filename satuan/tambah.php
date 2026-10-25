<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_satuan    = $_POST['nama_satuan'];
    $status         = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO satuan (nama_satuan, status) VALUES (:nama, :status)");
    $stmt->execute([
        ':nama' => $nama_satuan, 
        ':status' => $status
    ]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Satuan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Tambah Satuan Baru</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Satuan:</label>
            <input type="text" name="nama_satuan" required placeholder="Contoh: Pcs, Box, Kg">
        </div>
        <div class="form-group">
            <label>Status:</label>
            <select name="status" required>
                <option value="1">Aktif</option>
                <option value="0">Non-Aktif</option>
            </select>
        </div>
        <button type="submit">Simpan Data</button>
        <a href="index.php" class="btn" style="background:#fff; color:#000; margin-left:10px;">Batal</a>