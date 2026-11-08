<?php
require_once __DIR__ . '/../config/database.php';
$judul = $judul ?? 'Project PBD';
$beranda = $beranda ?? false;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <h2><?= htmlspecialchars($judul) ?></h2>
    <?php if ($beranda): ?>
        <p style="margin-bottom: 20px;">
            <a href="<?= BASE_URL ?>/pengadaan/index.php" class="btn">Pengadaan</a>
            <a href="<?= BASE_URL ?>/penerimaan/index.php" class="btn">Penerimaan</a>
            <a href="<?= BASE_URL ?>/penjualan/index.php" class="btn">Penjualan</a>
        </p>
    <?php endif; ?>