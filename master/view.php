<?php
$daftar = [
    'barang' => ['Data Barang', 'v_barang'],
    'vendor' => ['Data Vendor', 'v_vendor'],
    'role' => ['Data Role', 'v_role'],
    'user' => ['Data User', 'v_user'],
    'margin' => ['Data Margin Penjualan', 'v_margin'],
    'satuan' => ['Data Satuan', 'v_satuan'],
];

$t = $_GET['t'] ?? 'barang';
if (!isset($daftar[$t])) { $t = 'barang'; }
[$judul, $view] = $daftar[$t];

require_once __DIR__ . '/../includes/header.php';

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
<?php if ($error): ?>
    <p class="pesan pesan-err"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
    <?php if (!$rows): ?>
        <tr><td colspan="5">Belum ada data.</td></tr>
    <?php else: ?>
    <tr>
        <?php foreach ($kolom as $k): ?>
            <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $k))) ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($rows as $r): ?>
        <tr>
            <?php foreach ($kolom as $k): ?>
                <td><?= htmlspecialchars((string)$r[$k]) ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>