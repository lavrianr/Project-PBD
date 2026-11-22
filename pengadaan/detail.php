<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT * FROM v_pengadaan WHERE idpengadaan = ?");
$st->execute([$id]);
$header = $st->fetch();

if (!$header) {
    set_flash('error', 'Data pengadaan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $header['status'] === 'Diproses') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah_item') {
        $idbarang = (int)($_POST['idbarang'] ?? 0);
        $jumlah = (int)($_POST['jumlah'] ?? 0);

        $cekBarang = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE idbarang = ? AND status = 1");
        $cekBarang->execute([$idbarang]);

        $cekDup = $pdo->prepare("SELECT COUNT(*) FROM detail_pengadaan WHERE idpengadaan = ? AND idbarang = ?");
        $cekDup->execute([$id, $idbarang]);

        if ($cekBarang->fetchColumn() == 0) {
            set_flash('error', 'Barang tidak valid atau Non-Aktif.');
        } elseif ($jumlah < 1) {
            set_flash('error', 'Jumlah pesanan minimal 1.');
        } elseif ($cekDup->fetchColumn() > 0) {
            set_flash('error', 'Barang tersebut sudah ada di daftar.');
        } else {
            $pdo->prepare("CALL sp_tambah_detail_pengadaan(?, ?, ?)")->execute([$id, $idbarang, $jumlah]);
            set_flash('success', 'Item berhasil ditambahkan ke nota pengadaan.');
        }
    } elseif ($aksi === 'hapus_item') {
        $iddetail = (int)($_POST['iddetail_pengadaan'] ?? 0);
        $cekTerima = $pdo->prepare("SELECT COUNT(*) FROM penerimaan WHERE idpengadaan = ?");
        $cekTerima->execute([$id]);
        if ($cekTerima->fetchColumn() > 0) {
            set_flash('error', 'Item tidak dapat dihapus karena pengadaan ini sudah memiliki data penerimaan.');
        } else {
            $pdo->prepare("DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = ?")->execute([$iddetail]);
            $stSub = $pdo->prepare("SELECT COALESCE(SUM(sub_total), 0) FROM detail_pengadaan WHERE idpengadaan = ?");
            $stSub->execute([$id]);
            $sub = (int)$stSub->fetchColumn();
            $ppn = (int)round($sub * 0.11);
            $pdo->prepare("UPDATE pengadaan SET subtotal_nilai = ?, ppn = ?, total_nilai = ? WHERE idpengadaan = ?")
                ->execute([$sub, $ppn, $sub + $ppn, $id]);
            set_flash('success', 'Baris item berhasil dihapus.');
        }
    }
    header("Location: detail.php?id=$id");
    exit;
}

$items = $pdo->prepare("SELECT * FROM v_detail_pengadaan WHERE idpengadaan = ? ORDER BY iddetail_pengadaan ASC");
$items->execute([$id]);
$details = $items->fetchAll();

$barangs = $pdo->query("SELECT * FROM v_barang WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll();
$pageTitle = "Detail Pengadaan $id";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Daftar Pengadaan</a>
    <h1 class="text-2xl font-bold text-slate-900 mt-1">Nota Pengadaan #<?= $id ?></h1>
    <p class="text-sm text-slate-500">Vendor: <strong><?= e($header['vendor']) ?></strong> &bull; Waktu: <?= e($header['timestamp']) ?></p>
  </div>
  <div><?= badge_status($header['status']) ?></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <?php if ($header['status'] === 'Diproses'): ?>
    <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
      <h2 class="text-base font-bold mb-4">Tambah Barang ke Nota</h2>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="aksi" value="tambah_item">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Barang</label>
          <select name="idbarang" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="">-- Pilih Barang --</option>
            <?php foreach ($barangs as $b): ?>
              <option value="<?= (int)$b['idbarang'] ?>">
                <?= e($b['nama']) ?> (<?= rupiah($b['harga']) ?> / <?= e($b['satuan']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Pesan</label>
          <input type="number" name="jumlah" min="1" value="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        </div>
        <button type="submit" class="w-full py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
          + Tambah ke Daftar
        </button>
      </form>
    </div>
  <?php endif; ?>

  <div class="<?= $header['status'] === 'Diproses' ? 'lg:col-span-2' : 'lg:col-span-3' ?> bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col justify-between">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Barang</th>
          <th class="px-4 py-3">Harga Satuan</th>
          <th class="px-4 py-3">Qty</th>
          <th class="px-4 py-3">Subtotal</th>
          <?php if ($header['status'] === 'Diproses'): ?>
            <th class="px-4 py-3 text-right">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($details)): ?>
          <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada barang di nota pengadaan ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($details as $d): ?>
          <tr>
            <td class="px-4 py-3 font-medium"><?= e($d['barang']) ?></td>
            <td class="px-4 py-3"><?= rupiah($d['harga_satuan']) ?></td>
            <td class="px-4 py-3 font-semibold"><?= (int)$d['jumlah'] ?></td>
            <td class="px-4 py-3 font-semibold"><?= rupiah($d['sub_total']) ?></td>
            <?php if ($header['status'] === 'Diproses'): ?>
              <td class="px-4 py-3 text-right">
                <form method="POST" class="inline">
                  <input type="hidden" name="aksi" value="hapus_item">
                  <input type="hidden" name="iddetail_pengadaan" value="<?= (int)$d['iddetail_pengadaan'] ?>">
                  <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                </form>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="bg-slate-50 p-5 border-t border-slate-200 flex justify-end">
      <div class="w-full max-w-xs space-y-1.5 text-sm">
        <div class="flex justify-between text-slate-600"><span>Subtotal</span><span><?= rupiah($header['subtotal_nilai']) ?></span></div>
        <div class="flex justify-between text-slate-600"><span>PPN (11%)</span><span><?= rupiah($header['ppn']) ?></span></div>
        <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-200">
          <span>Grand Total</span><span><?= rupiah($header['total_nilai']) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>