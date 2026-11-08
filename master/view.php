<?php
$daftar = [
    'barang' => ['Data Barang', 'v_barang', 'barang', 'ID'],
    'vendor' => ['Data Vendor', 'v_vendor', 'vendor', 'ID'],
    'role'   => ['Data Role', 'v_role', 'role', 'ID'],
    'user'   => ['Data User', 'v_user', 'user', 'ID'],
    'margin' => ['Data Margin Penjualan', 'v_margin', 'margin', 'ID'],
    'satuan' => ['Data Satuan', 'v_satuan', 'satuan', 'ID'],
];

$t = $_GET['t'] ?? 'barang';
if (!isset($daftar[$t])) { $t = 'barang'; }
[$judul, $view, $folder_tabel, $pk_view] = $daftar[$t];

ob_start();
require_once __DIR__ . '/../includes/header.php';
$header_content = ob_get_clean();

$header_content = preg_replace('/<a[^>]*>Menu Utama<\/a>/i', '', $header_content);
echo $header_content;

$rows = [];
$error = '';
try {
    $stmt = $pdo->query("SELECT * FROM $view");
    if ($stmt) {
        $rows = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = 'Gagal mengambil data: ' . $e->getMessage();
}

$kolom = [];
if ($rows) {
    foreach (array_keys($rows[0]) as $k) {
        if (stripos($k, 'password') === false) {
            $kolom[] = $k;
        }
    }
}
?>

<div style="text-align: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; margin-bottom: 15px; color: #3b5028;"><?= htmlspecialchars($judul) ?></h2>
    <div style="display: flex; justify-content: center; gap: 12px; align-items: center;">
        <a href="../<?= $folder_tabel ?>/tambah.php" class="btn">+ Tambah Data</a>
        <a href="../index.php" class="btn" style="background:#fff; color:#000;">Menu Utama</a>
    </div>
</div>

<?php if ($error): ?>
    <p class="pesan pesan-err"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
    <?php if (!$rows): ?>
        <tr><td colspan="7" style="text-align: center;">Belum ada data.</td></tr>
    <?php else: ?>
    <thead>
        <tr>
            <?php foreach ($kolom as $k): ?>
                <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $k))) ?></th>
            <?php endforeach; ?>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
            <?php 
                $id_val = $r[$pk_view] ?? $r[strtolower($pk_view)] ?? reset($r); 
            ?>
            <tr>
                <?php foreach ($kolom as $k): ?>
                    <td><?= htmlspecialchars((string)$r[$k]) ?></td>
                <?php endforeach; ?>
                <td>
                    <a href="../<?= $folder_tabel ?>/edit.php?id=<?= $id_val ?>">Edit</a> | 
                    <a href="../<?= $folder_tabel ?>/index.php?hapus=<?= $id_val ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <?php endif; ?>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>