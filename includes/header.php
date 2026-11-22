<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';
require_login();
$flash = get_flash();
$curr  = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Inventori & Kasir') ?> — Proyek PBD</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #0F172A; }
  </style>
</head>
<body class="min-h-screen flex">
  <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0">
    <div>
      <div class="px-6 py-5 border-b border-slate-100">
        <div class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Sistem POS & Gudang</div>
        <div class="text-lg font-bold text-slate-900 mt-0.5">Proyek PBD</div>
      </div>

      <nav class="p-4 space-y-1 text-sm">
        <a href="<?= base_url('dashboard.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'dashboard.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
          Dashboard
        </a>

        <div class="pt-4 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Transaksi</div>
        <?php if (is_super_admin()): ?>
          <a href="<?= base_url('pengadaan/index.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, '/pengadaan/') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Pengadaan Barang
          </a>
          <a href="<?= base_url('penerimaan/index.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, '/penerimaan/') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Penerimaan Barang
          </a>
        <?php endif; ?>
        <a href="<?= base_url('penjualan/index.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, '/penjualan/') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
          Penjualan (Kasir)
        </a>
        <a href="<?= base_url('kartu_stok/index.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, '/kartu_stok/') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
          Kartu Stok
        </a>

        <div class="pt-4 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Data Master</div>
        <a href="<?= base_url('master/barang.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'barang.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
          Data Barang <?= !is_super_admin() ? '(Lihat)' : '' ?>
        </a>
        <?php if (is_super_admin()): ?>
          <a href="<?= base_url('master/satuan.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'satuan.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Data Satuan
          </a>
          <a href="<?= base_url('master/vendor.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'vendor.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Data Vendor
          </a>
          <a href="<?= base_url('master/margin.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'margin.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Margin Penjualan
          </a>
          <a href="<?= base_url('master/user.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'user.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Manajemen User
          </a>
          <a href="<?= base_url('master/role.php') ?>" class="block px-3 py-2 rounded-lg font-medium <?= str_contains($curr, 'role.php') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Manajemen Role
          </a>
        <?php endif; ?>
      </nav>
    </div>

    <div class="p-4 border-t border-slate-100">
      <div class="px-3 py-2 bg-slate-50 rounded-lg mb-2">
        <div class="text-sm font-semibold text-slate-900"><?= e($_SESSION['user']['username']) ?></div>
        <div class="text-xs text-slate-500"><?= e($_SESSION['user']['nama_role']) ?></div>
      </div>
      <a href="<?= base_url('logout.php') ?>" class="block text-center px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200">
        Keluar Sesi
      </a>
    </div>
  </aside>

  <main class="flex-1 p-8 max-w-7xl mx-auto">
    <?php if ($flash): ?>
      <div class="mb-6 px-4 py-3 rounded-lg border text-sm font-medium <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' ?>">
        <?= e($flash['msg']) ?>
      </div>
    <?php endif; ?>