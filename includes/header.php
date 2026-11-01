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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<div class="container">
    <h2><?= htmlspecialchars($judul) ?></h2>
    <?php if (!$beranda): ?>
        <a href="<?= BASE_URL ?>/index.php">Menu Utama</a>
    <?php endif; ?>