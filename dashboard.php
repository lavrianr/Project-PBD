<?php
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$jmlBarang    = $pdo->query("SELECT COUNT(*) FROM v_barang WHERE status = 'Aktif'")->fetchColumn();
$jmlPengadaan = $pdo->query("SELECT COUNT(*) FROM v_pengadaan WHERE status = 'Diproses'")->fetchColumn();
$jmlPenjualan = $pdo->query("SELECT COUNT(*) FROM v_penjualan")->fetchColumn();
$marginAktif  = $pdo->query("SELECT persen FROM v_margin_penjualan WHERE status = 'Aktif' LIMIT 1")->fetchColumn();
?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900">Selamat Datang, <?= e($_SESSION['user']['username']) ?></h1>
  <p class="text-sm text-slate-500">Anda masuk sebagai <strong><?= e($_SESSION['user']['nama_role']) ?></strong>.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
  <div class="bg-white p-5 rounded-xl border border-slate-200">
    <div class="text-xs font-medium text-slate-500">Barang Aktif</div>
    <div class="text-2xl font-bold text-slate-900 mt-1"><?= (int)$jmlBarang ?> Item</div>
  </div>
  <div class="bg-white p-5 rounded-xl border border-slate-200">
    <div class="text-xs font-medium text-slate-500">Pengadaan Diproses</div>
    <div class="text-2xl font-bold text-amber-600 mt-1"><?= (int)$jmlPengadaan ?> Transaksi</div>
  </div>
  <div class="bg-white p-5 rounded-xl border border-slate-200">
    <div class="text-xs font-medium text-slate-500">Total Penjualan</div>
    <div class="text-2xl font-bold text-emerald-600 mt-1"><?= (int)$jmlPenjualan ?> Transaksi</div>
  </div>
  <div class="bg-white p-5 rounded-xl border border-slate-200">
    <div class="text-xs font-medium text-slate-500">Margin Jual Aktif</div>
    <div class="text-2xl font-bold text-slate-900 mt-1"><?= (float)$marginAktif ?>%</div>
  </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-6">
  <h2 class="text-base font-bold text-slate-900 mb-3">Akses Cepat</h2>
  <div class="flex flex-wrap gap-3 text-sm">
    <a href="<?= base_url('penjualan/index.php') ?>" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-medium hover:bg-slate-800">+ Transaksi Penjualan</a>
    <?php if (is_super_admin()): ?>
      <a href="<?= base_url('pengadaan/index.php') ?>" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50">+ Pengadaan Baru</a>
      <a href="<?= base_url('penerimaan/index.php') ?>" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50">+ Penerimaan Barang</a>
    <?php endif; ?>
    <a href="<?= base_url('kartu_stok/index.php') ?>" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50">Lihat Kartu Stok</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>