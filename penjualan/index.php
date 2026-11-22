<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iduser = (int)$_SESSION['user']['iduser'];

    $stMargin = $pdo->query("SELECT idmargin_penjualan FROM margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan DESC LIMIT 1");
    $idMargin = $stMargin->fetchColumn();

    if (!$idMargin) {
        set_flash('error', 'Belum ada margin penjualan yang aktif. Hubungi Super Admin untuk mengaktifkan margin.');
        header('Location: index.php');
        exit;
    }

    $st = $pdo->prepare("INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan) VALUES (NOW(), 0, 0, 0, ?, ?)");
    $st->execute([$iduser, (int)$idMargin]);
    $idpenjualan = (int)$pdo->lastInsertId();

    set_flash('success', 'Nota penjualan #' . $idpenjualan . ' siap. Silakan input barang belanjaan.');
    header('Location: detail.php?id=' . $idpenjualan);
    exit;
}

$marginAktif = $pdo->query("SELECT persen FROM v_margin_penjualan WHERE status = 'Aktif' LIMIT 1")->fetchColumn();
$rows = $pdo->query("SELECT * FROM v_penjualan ORDER BY idpenjualan DESC")->fetchAll();
$pageTitle = 'Penjualan (Kasir)';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Transaksi Penjualan</h1>
    <p class="text-sm text-slate-500">Margin penjualan aktif saat ini: <strong><?= (float)$marginAktif ?>%</strong></p>
  </div>
  <form method="POST">
    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
      + Transaksi Penjualan Baru
    </button>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
  <table class="w-full text-sm text-left">
    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
      <tr>
        <th class="px-4 py-3">No. Nota</th>
        <th class="px-4 py-3">Waktu</th>
        <th class="px-4 py-3">Kasir</th>
        <th class="px-4 py-3">Margin</th>
        <th class="px-4 py-3">Total Bayar</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($rows)): ?>
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada transaksi penjualan.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-bold">#<?= (int)$r['idpenjualan'] ?></td>
          <td class="px-4 py-3 text-slate-500"><?= e($r['created_at']) ?></td>
          <td class="px-4 py-3"><?= e($r['user']) ?></td>
          <td class="px-4 py-3"><?= (float)$r['margin_penjualan'] ?>%</td>
          <td class="px-4 py-3 font-bold text-emerald-700"><?= rupiah($r['total_nilai']) ?></td>
          <td class="px-4 py-3 text-right">
            <a href="detail.php?id=<?= (int)$r['idpenjualan'] ?>" class="text-xs font-semibold text-slate-900 hover:underline">Buka Nota</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>