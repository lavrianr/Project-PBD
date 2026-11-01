<?php
$judul = 'Kartu Stok per Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$stmt_barang = $pdo->query("SELECT idbarang, nama FROM barang WHERE status = 1 ORDER BY nama");
$barang = $stmt_barang->fetchAll();
$stmt_barang->closeCursor();

$idbarang = $_GET['idbarang'] ?? '';
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$rows = [];

if ($idbarang !== '') {
    $stmt = $pdo->prepare("CALL sp_kartu_stok_barang(?, ?, ?)");
    $stmt->execute([$idbarang, $dari, $sampai]);
    $rows = $stmt->fetchAll();
    $stmt->closeCursor();
}
?>
<div class="dashboard-card">
    <form method="get">
        Barang:
        <select name="idbarang" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($barang as $b): ?>
                <option value="<?= $b['idbarang'] ?>" <?php echo $idbarang == $b['idbarang'] ? 'selected' : ''; ?>> <?= htmlspecialchars($b['nama']) ?></option>
            <?php endforeach; ?>
        </select>
        Dari: <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
        Sampai: <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
        <button type="submit">Tampilkan</button>
    </form>
</div>

<?php if ($idbarang !== ''): ?>
<table>
    <tr><th>Tanggal</th><th>Jenis</th><th>Masuk</th><th>Keluar</th><th>Stok</th></tr>
    <?php if (!$rows): ?>
        <tr><td colspan="5">Tidak ada data pada rentang tanggal ini.</td></tr>
        <?php endif; ?>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= $r['created_at'] ?></td>
        <td><?= $r['jenis_transaksi'] ?></td>
        <td><?= $r['masuk'] ?></td>
        <td><?= $r['keluar'] ?></td>
        <td><?= $r['stock'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>