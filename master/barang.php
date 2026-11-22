<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_super_admin();
    $aksi     = $_POST['aksi'] ?? '';
    $nama     = trim($_POST['nama'] ?? '');
    $jenis    = $_POST['jenis'] ?? 'M';
    $idsatuan = (int)($_POST['idsatuan'] ?? 0);
    $harga    = (int)($_POST['harga'] ?? 0);
    $status   = (int)($_POST['status'] ?? 1);

    if ($aksi === 'tambah') {
        if ($nama === '' || $idsatuan <= 0 || $harga < 0) {
            set_flash('error', 'Data barang tidak lengkap atau harga tidak valid.');
        } else {
            $pdo->prepare("INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES (?, ?, ?, ?, ?)")
                ->execute([$jenis, $nama, $idsatuan, $status, $harga]);
            set_flash('success', 'Barang baru berhasil ditambahkan.');
        }
    } elseif ($aksi === 'edit') {
        $id = (int)($_POST['idbarang'] ?? 0);
        if ($nama === '' || $idsatuan <= 0 || $harga < 0) {
            set_flash('error', 'Data barang tidak lengkap atau harga tidak valid.');
        } else {
            $pdo->prepare("UPDATE barang SET jenis = ?, nama = ?, idsatuan = ?, status = ?, harga = ? WHERE idbarang = ?")
                ->execute([$jenis, $nama, $idsatuan, $status, $harga, $id]);
            set_flash('success', 'Data barang berhasil diperbarui.');
        }
    } elseif ($aksi === 'nonaktif') {
        $id = (int)($_POST['idbarang'] ?? 0);
        $pdo->prepare("UPDATE barang SET status = 0 WHERE idbarang = ?")->execute([$id]);
        set_flash('success', 'Barang berhasil dinonaktifkan.');
    }
    header('Location: barang.php');
    exit;
}

$filter   = $_GET['filter'] ?? 'aktif';
$whereSql = ($filter === 'semua') ? '' : "WHERE status = 'Aktif'";
$rows     = $pdo->query("SELECT *, fn_stok_terakhir(idbarang) AS stok FROM v_barang $whereSql ORDER BY idbarang ASC")->fetchAll();
$satuans  = $pdo->query("SELECT * FROM satuan WHERE status = 1 ORDER BY nama_satuan ASC")->fetchAll();

$editData = null;
if (is_super_admin() && !empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM barang WHERE idbarang = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$pageTitle = 'Data Barang';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Data Barang</h1>
    <p class="text-sm text-slate-500">Daftar master barang, harga pokok, dan stok terkini.</p>
  </div>
  <div class="flex gap-2 text-xs font-semibold">
    <a href="?filter=aktif" class="px-3 py-2 rounded-lg border <?= $filter === 'aktif' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Aktif Saja</a>
    <a href="?filter=semua" class="px-3 py-2 rounded-lg border <?= $filter === 'semua' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Semua Data</a>
  </div>
</div>

<div class="grid grid-cols-1 <?= is_super_admin() ? 'lg:grid-cols-3' : '' ?> gap-6">
  <?php if (is_super_admin()): ?>
    <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
      <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit Barang' : 'Tambah Barang Baru' ?></h2>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
        <?php if ($editData): ?>
          <input type="hidden" name="idbarang" value="<?= (int)$editData['idbarang'] ?>">
        <?php endif; ?>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang</label>
          <input type="text" name="nama" required value="<?= e($editData['nama'] ?? '') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Kategori / Jenis</label>
          <select name="jenis" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="M" <?= (($editData['jenis'] ?? 'M') === 'M') ? 'selected' : '' ?>>Makanan</option>
            <option value="N" <?= (($editData['jenis'] ?? '') === 'N') ? 'selected' : '' ?>>Non-Makanan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
          <select name="idsatuan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <?php foreach ($satuans as $s): ?>
              <option value="<?= (int)$s['idsatuan'] ?>" <?= (($editData['idsatuan'] ?? 0) == $s['idsatuan']) ? 'selected' : '' ?>>
                <?= e($s['nama_satuan']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Harga Beli / Pokok (Rp)</label>
          <input type="number" name="harga" min="0" required value="<?= e($editData['harga'] ?? 0) ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="1" <?= (($editData['status'] ?? 1) == 1) ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= (($editData['status'] ?? 1) == 0) ? 'selected' : '' ?>>Non-Aktif</option>
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
          <?php if ($editData): ?>
            <a href="barang.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <div class="<?= is_super_admin() ? 'lg:col-span-2' : '' ?> bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Nama Barang</th>
          <th class="px-4 py-3">Jenis</th>
          <th class="px-4 py-3">Harga Pokok</th>
          <th class="px-4 py-3">Stok</th>
          <th class="px-4 py-3">Status</th>
          <?php if (is_super_admin()): ?>
            <th class="px-4 py-3 text-right">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 font-medium"><?= e($r['nama']) ?> <span class="text-xs text-slate-400">(<?= e($r['satuan']) ?>)</span></td>
            <td class="px-4 py-3 text-slate-600"><?= e($r['jenis']) ?></td>
            <td class="px-4 py-3 font-medium"><?= rupiah($r['harga']) ?></td>
            <td class="px-4 py-3 font-semibold <?= $r['stok'] <= 5 ? 'text-rose-600' : 'text-slate-900' ?>"><?= (int)$r['stok'] ?></td>
            <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
            <?php if (is_super_admin()): ?>
              <td class="px-4 py-3 text-right space-x-2">
                <a href="?edit=<?= (int)$r['idbarang'] ?>&filter=<?= e($filter) ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
                <?php if ($r['status'] === 'Aktif'): ?>
                  <form method="POST" class="inline" onsubmit="return confirm('Nonaktifkan barang ini?')">
                    <input type="hidden" name="aksi" value="nonaktif">
                    <input type="hidden" name="idbarang" value="<?= (int)$r['idbarang'] ?>">
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Nonaktifkan</button>
                  </form>
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>