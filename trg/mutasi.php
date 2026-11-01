<?php
$judul = 'Mutasi Stok';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$pesan = ''; $tipe = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idbarang = (int)$_POST['idbarang'];
    $jenis = $_POST['jenis_transaksi'] === 'K' ? 'K' : 'M';
    $jumlah = (int)$_POST['jumlah'];
    $masuk = $jenis === 'M' ? $jumlah : 0;
    $keluar = $jenis === 'K' ? $jumlah : 0;
    $idtrans = (int)$_POST['idtransaksi'];

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, idtransaksi, idbarang)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$jenis, $masuk, $keluar, $idtrans, $idbarang]);
        $stmt->closeCursor();
        $pesan = 'Mutasi stok berhasil dicatat.';
        $tipe = 'ok';
    } catch (PDOException $e) {
        $pesan = 'Gagal melakukan mutasi stok: ' . $e->getMessage();
        $tipe = 'err';
    }
}

$stmt_barang = $pdo->query("SELECT idbarang, nama, fn_stok_barang(idbarang) AS stok FROM barang WHERE status = 1 ORDER BY nama");
$barang = $stmt_barang->fetchAll();
$stmt_barang->closeCursor();

$stmt_terakhir = $pdo->query(
    "SELECT k.idkartu_stok, b.nama, k.jenis_transaksi, k.masuk, k.keluar, k.stock, k.created_at
    FROM kartu_stok k JOIN barang b ON b.idbarang = k.idbarang
    ORDER BY k.idkartu_stok DESC LIMIT 10"
);
$terakhir = $stmt_terakhir->fetchAll();
$stmt_terakhir->closeCursor();
?>
<?php if ($pesan): ?>
    <p style="margin-bottom:16px;color:<?= $tipe === 'err' ? '#8e995d' : '#8e995d' ?>"><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<div class="dashboard-card">
    <form method="post">
       <p>Barang<br>
       <select name="idbarang" required>
         <?php foreach ($barang as $b): ?>
            <option value="<?= $b['idbarang'] ?>"><?= htmlspecialchars($b['nama']) ?> (stok: <?= $b['stok'] ?>)</option>
            <?php endforeach; ?>
       </select></p>
    <br>
    <p>Jenis<br>
    <select name="jenis_transaksi">
        <option value="M">Masuk (M)</option>
        <option value="K">Keluar (K)</option>
    </select></p>
    <br>
    <p>Jumlah<br>
    <input type="number" name="jumlah" min="1" required></p>
    <br>
    <button type="submit">Simpan Mutasi</button>
    </form>
</div>

<table>
    <tr><th>ID</th><th>Barang</th><th>Jenis</th><th>Masuk</th><th>Keluar</th><th>Stok</th><th>Waktu</th></tr>
    <?php foreach ($terakhir as $t): ?>
        <tr>
            <td><?= $t['idkartu_stok'] ?></td>
            <td><?= htmlspecialchars($t['nama']) ?></td>
            <td><?= $t['jenis_transaksi'] === 'M' ? 'Masuk' : 'Keluar' ?></td>
            <td><?= $t['masuk'] ?></td>
            <td><?= $t['keluar'] ?></td>
            <td><?= $t['stock'] ?></td>
            <td><?= $t['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>