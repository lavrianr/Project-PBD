<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT pn.*, pg.vendor FROM v_penerimaan pn JOIN v_pengadaan pg ON pg.idpengadaan = pn.idpengadaan WHERE pn.idpenerimaan = ?");
$st->execute([$id]);
$header = $st->fetch();

if (!$header) {
    set_flash('error', 'Data penerimaan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$idpengadaan = (int)$header['idpengadaan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $header['status'] !== 'Diterima Utuh') {
    $idbarang = (int)($_POST['idbarang'] ?? 0);
    $jumlah   = (int)($_POST['jumlah_terima'] ?? 0);
    $harga    = (int)($_POST['harga_satuan_terima'] ?? -1);

    $stSisa = $pdo->prepare("SELECT fn_sisa_terima(?, ?)");
    $stSisa->execute([$idpengadaan, $idbarang]);
    $sisa = (int)$stSisa->fetchColumn();

    if ($idbarang <= 0 || $sisa <= 0) {
        set_flash('error', 'Barang tidak valid atau sudah diterima lunas seluruhnya.');
    } elseif ($jumlah < 1) {
        set_flash('error', 'Jumlah terima minimal 1.');
    } elseif ($jumlah > $sisa) {
        set_flash('error', "Jumlah terima melebihi sisa pesanan! Sisa yang belum diterima hanya $sisa unit.");
    } elseif ($harga < 0) {
        set_flash('error', 'Harga satuan terima tidak valid.');
    } else {
        $pdo->prepare("CALL sp_tambah_detail_penerimaan(?, ?, ?, ?)")
            ->execute([$id, $idbarang, $jumlah, $harga]);
        set_flash('success', 'Barang diterima, stok kartu stok dan harga master telah diperbarui.');
    }
    header("Location: detail.php?id=$id");
    exit;
}

$stSisaList = $pdo->prepare("
    SELECT dp.idbarang, b.nama AS nama_barang, s.nama_satuan, dp.harga_satuan,
           dp.jumlah AS jumlah_pesan,
           dp.jumlah - fn_sisa_terima(dp.idpengadaan, dp.idbarang) AS jumlah_diterima,
           fn_sisa_terima(dp.idpengadaan, dp.idbarang) AS sisa
    FROM detail_pengadaan dp
    JOIN barang b ON b.idbarang = dp.idbarang
    JOIN satuan s ON s.idsatuan = b.idsatuan
    WHERE dp.idpengadaan = ?
    ORDER BY b.nama
");
$stSisaList->execute([$idpengadaan]);
$daftarPesanan = $stSisaList->fetchAll();

$items = $pdo->prepare("SELECT * FROM v_detail_penerimaan WHERE idpenerimaan = ? ORDER BY iddetail_penerimaan ASC");
$items->execute([$id]);
$details = $items->fetchAll();

$pageTitle = "Detail Penerimaan #$id";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Daftar Penerimaan</a>
    <h1 class="text-2xl font-bold text-slate-900 mt-1">Penerimaan #<?= $id ?> (Ref. Pengadaan #<?= $idpengadaan ?>)</h1>
    <p class="text-sm text-slate-500">Vendor: <strong><?= e($header['vendor']) ?></strong></p>
  </div>
  <div><?= badge_status($header['status']) ?></div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
  <h2 class="text-sm font-bold text-slate-900 mb-3">Status Kelengkapan Item Pengadaan #<?= $idpengadaan ?></h2>
  <div class="overflow-x-auto">
    <table class="w-full text-xs text-left">
      <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
        <tr>
          <th class="px-3 py-2">Barang</th>
          <th class="px-3 py-2">Harga Pesan</th>
          <th class="px-3 py-2">Dipesan</th>
          <th class="px-3 py-2">Sudah Diterima</th>
          <th class="px-3 py-2">Sisa Belum Diterima</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($daftarPesanan as $p): ?>
          <tr>
            <td class="px-3 py-2 font-medium"><?= e($p['nama_barang']) ?> (<?= e($p['nama_satuan']) ?>)</td>
            <td class="px-3 py-2"><?= rupiah($p['harga_satuan']) ?></td>
            <td class="px-3 py-2"><?= (int)$p['jumlah_pesan'] ?></td>
            <td class="px-3 py-2 text-emerald-700 font-semibold"><?= (int)$p['jumlah_diterima'] ?></td>
            <td class="px-3 py-2 font-bold <?= $p['sisa'] > 0 ? 'text-amber-600' : 'text-slate-400' ?>"><?= (int)$p['sisa'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <?php if ($header['status'] !== 'Diterima Utuh'): ?>
    <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
      <h2 class="text-base font-bold mb-4">Input Barang Diterima</h2>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Barang dari Pengadaan</label>
          <select name="idbarang" id="selBarang" required onchange="isiHargaDefault(this)" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="" data-harga="0">-- Pilih Barang --</option>
            <?php foreach ($daftarPesanan as $p): ?>
              <?php if ((int)$p['sisa'] > 0): ?>
                <option value="<?= (int)$p['idbarang'] ?>" data-harga="<?= (int)$p['harga_satuan'] ?>">
                  <?= e($p['nama_barang']) ?> (Sisa: <?= (int)$p['sisa'] ?> <?= e($p['nama_satuan']) ?>)
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Diterima Sekarang</label>
          <input type="number" name="jumlah_terima" min="1" value="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Harga Satuan Terima (Bisa Edit)</label>
          <input type="number" name="harga_satuan_terima" id="inpHarga" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <p class="text-xs text-slate-400 mt-1">Jika harga berubah, master barang akan terupdate otomatis.</p>
        </div>
        <button type="submit" class="w-full py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
          Simpan Penerimaan Item
        </button>
      </form>
    </div>
  <?php endif; ?>

  <div class="<?= $header['status'] !== 'Diterima Utuh' ? 'lg:col-span-2' : 'lg:col-span-3' ?> bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Barang Diterima</th>
          <th class="px-4 py-3">Qty Terima</th>
          <th class="px-4 py-3">Harga Satuan</th>
          <th class="px-4 py-3">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($details)): ?>
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada barang yang dicatat pada penerimaan ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($details as $d): ?>
          <tr>
            <td class="px-4 py-3 font-medium"><?= e($d['barang']) ?></td>
            <td class="px-4 py-3 font-semibold"><?= (int)$d['jumlah_terima'] ?></td>
            <td class="px-4 py-3"><?= rupiah($d['harga_satuan_terima']) ?></td>
            <td class="px-4 py-3 font-semibold"><?= rupiah($d['sub_total_terima']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function isiHargaDefault(sel) {
  const opt = sel.options[sel.selectedIndex];
  document.getElementById('inpHarga').value = opt ? (opt.getAttribute('data-harga') || 0) : 0;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>