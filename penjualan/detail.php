<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT * FROM v_penjualan WHERE idpenjualan = ?");
$st->execute([$id]);
$header = $st->fetch();

if (!$header) {
    set_flash('error', 'Nota penjualan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idbarang = (int)($_POST['idbarang'] ?? 0);
    $jumlah   = (int)($_POST['jumlah'] ?? 0);

    $stStok = $pdo->prepare("SELECT fn_stok_terakhir(idbarang) FROM v_barang WHERE idbarang = ? AND status = 'Aktif'");
    $stStok->execute([$idbarang]);
    $stokTersedia = $stStok->fetchColumn();

    if ($stokTersedia === false) {
        set_flash('error', 'Barang tidak ditemukan atau berstatus Non-Aktif.');
    } elseif ($jumlah < 1) {
        set_flash('error', 'Jumlah beli minimal 1.');
    } elseif ($jumlah > (int)$stokTersedia) {
        set_flash('error', "Stok tidak mencukupi! Stok tersedia saat ini hanya $stokTersedia unit.");
    } else {
        $pdo->prepare("CALL sp_tambah_detail_penjualan(?, ?, ?)")->execute([$id, $idbarang, $jumlah]);
        set_flash('success', 'Barang ditambahkan ke keranjang nota dan stok otomatis berkurang.');
    }
    header("Location: detail.php?id=$id");
    exit;
}

$items = $pdo->prepare("SELECT * FROM v_detail_penjualan WHERE idpenjualan = ? ORDER BY iddetail_penjualan ASC");
$items->execute([$id]);
$details = $items->fetchAll();

$stMargin = $pdo->prepare("SELECT idmargin_penjualan FROM penjualan WHERE idpenjualan = ?");
$stMargin->execute([$id]);
$idMargin = (int)$stMargin->fetchColumn();

$stBrg = $pdo->prepare("SELECT idbarang, nama, satuan, fn_stok_terakhir(idbarang) AS stok, fn_harga_jual(idbarang, ?) AS harga_jual FROM v_barang WHERE status = 'Aktif' ORDER BY nama ASC");
$stBrg->execute([$idMargin]);
$barangs = $stBrg->fetchAll();

$pageTitle = "Kasir Nota #$id";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Daftar Penjualan</a>
    <h1 class="text-2xl font-bold text-slate-900 mt-1">Nota Penjualan #<?= $id ?></h1>
    <p class="text-sm text-slate-500">Kasir: <strong><?= e($header['user']) ?></strong> &bull; Margin: <strong><?= (float)$header['margin_penjualan'] ?>%</strong></p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4">Input Barang Terjual</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Barang</label>
        <select name="idbarang" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <option value="">-- Pilih Barang --</option>
          <?php foreach ($barangs as $b): ?>
            <option value="<?= (int)$b['idbarang'] ?>" <?= $b['stok'] <= 0 ? 'disabled' : '' ?>>
              <?= e($b['nama']) ?> — <?= rupiah($b['harga_jual']) ?> (Stok: <?= (int)$b['stok'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Beli</label>
        <input type="number" name="jumlah" min="1" value="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <button type="submit" class="w-full py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
        + Masukkan ke Nota
      </button>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col justify-between">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Nama Barang</th>
          <th class="px-4 py-3">Harga Jual</th>
          <th class="px-4 py-3">Qty</th>
          <th class="px-4 py-3 text-right">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($details)): ?>
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada item di nota penjualan ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($details as $d): ?>
          <tr>
            <td class="px-4 py-3 font-medium"><?= e($d['barang']) ?></td>
            <td class="px-4 py-3"><?= rupiah($d['harga_satuan']) ?></td>
            <td class="px-4 py-3 font-semibold"><?= (int)$d['jumlah'] ?></td>
            <td class="px-4 py-3 text-right font-semibold"><?= rupiah($d['subtotal']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="bg-slate-50 p-5 border-t border-slate-200 flex justify-end">
      <div class="w-full max-w-xs space-y-1.5 text-sm">
        <div class="flex justify-between text-slate-600"><span>Subtotal</span><span><?= rupiah($header['subtotal_nilai']) ?></span></div>
        <div class="flex justify-between text-slate-600"><span>PPN (11%)</span><span><?= rupiah($header['ppn']) ?></span></div>
        <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-200">
          <span>Total Tagihan</span><span><?= rupiah($header['total_nilai']) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>