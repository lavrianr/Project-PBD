<?php
$judul = 'Project PBD';
$beranda = true;
require_once __DIR__ . '/includes/header.php';
?>
<div class="dashboard-card">
    <h2>Master Data</h2>
    <a class="menu-item" href="master/view.php?t=barang">Data Barang</a>
    <a class="menu-item" href="master/view.php?t=vendor">Data Vendor</a>
    <a class="menu-item" href="master/view.php?t=role">Data Role</a>
    <a class="menu-item" href="master/view.php?t=user">Data User</a>
    <a class="menu-item" href="master/view.php?t=margin">Data Margin Penjualan</a>
    <a class="menu-item" href="satuan/">Data Satuan</a>
</div>

<div class=""dashboard-card">
    <h2>Transaksi</h2>
    <a class="menu-item" href="sp/laporan_stok.php">Laporan Stok</a>
    <a class="menu-item" href="sp/kartu_stok.php">Kartu Stok per Barang</a>
    <a class="menu-item" href="fn/cek.php">Stok dan Harga Jual</a>
    <a class="menu-item" href="trg/mutasi.php">Mutasi Stok</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>