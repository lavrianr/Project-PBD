<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();

$filterNama = trim($_GET['barang'] ?? '');
$barangs = $pdo->query("SELECT nama FROM barang ORDER BY nama ASC")->fetchAll();

if ($filterNama !== '') {
    $st = $pdo->prepare("SELECT * FROM v_kartu_stok WHERE barang = ? ORDER BY idkartu_stok DESC");
    $st->execute([$filterNama]);
    $rows = $st->fetchAll();
} else {
    $rows = $pdo->query("SELECT * FROM v_kartu_stok ORDER BY idkartu_stok DESC LIMIT 100")->fetchAll();
}

$pageTitle = 'Kartu Stok';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Kartu Stok</h1>
    <p class="text-sm text-slate-500">Riwayat mutasi keluar-masuk barang otomatis dari trigger transaksi.</p>
  </div>
  <form method="GET" class="flex items-center gap-2">
    <select name="barang" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm">
      <option value="">Semua Barang</option>
      <?php foreach ($barangs as $b): ?>
        <option value="<?= e($b['nama']) ?>" <?= $filterNama === $b['nama'] ? 'selected' : '' ?>>
          <?= e($b['nama']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
  <table class="w-full text-sm text-left">
    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
      <tr>
        <th class="px-4 py-3">Waktu</th>
        <th class="px-4 py-3">Nama Barang</th>
        <th class="px-4 py-3">Jenis Mutasi</th>
        <th class="px-4 py-3">Masuk</th>
        <th class="px-4 py-3">Keluar</th>
        <th class="px-4 py-3">Sisa Stok</th>
        <th class="px-4 py-3">Ref. ID</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada riwayat mutasi stok.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 text-slate-500"><?= e($r['created_at']) ?></td>
          <td class="px-4 py-3 font-medium"><?= e($r['barang']) ?></td>
          <td class="px-4 py-3"><?= badge_status($r['jenis_transaksi']) ?></td>
          <td class="px-4 py-3 text-emerald-600 font-semibold"><?= $r['masuk'] > 0 ? '+' . (int)$r['masuk'] : '-' ?></td>
          <td class="px-4 py-3 text-rose-600 font-semibold"><?= $r['keluar'] > 0 ? '-' . (int)$r['keluar'] : '-' ?></td>
          <td class="px-4 py-3 font-bold text-slate-900"><?= (int)$r['stock'] ?></td>
          <td class="px-4 py-3 text-slate-500">#<?= (int)$r['idtransaksi'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>