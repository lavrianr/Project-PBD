<?php
require_once '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM satuan WHERE idsatuan = :id");
$stmt->execute([':id' => $id]);
$satuan = $stmt->fetch();

if (!$satuan) {
    die("Data tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_satuan    = $_POST['nama_satuan'];
    $status         = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE satuan SET nama_satuan = :nama, status = :status WHERE idsatuan = :id");
    $stmt->execute([
        ':nama' => $nama_satuan,
        ':status' => $status,
        ':id' => $id
    ]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Satuan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Edit Satuan Barang</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Satuan:</label>
            <input type="text" name="nama_satuan" value="<?= htmlspecialchars($satuan['nama_satuan']); ?>" required>
        </div>
        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="1" <?= $satuan['status'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                <option value="0" <?= $satuan['status'] == 0 ? 'selected' : ''; ?>>Non-Aktif</option>
            </select>
        </div>
        <button type="submit">Update Data</button>
        <a href="index.php" class="btn" style="background:#fff; color:#000; margin-left:10px;">Batal</a>
    </form>
</body>
</html>