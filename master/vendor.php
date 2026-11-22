<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi   = $_POST['aksi'] ?? '';
    $nama   = trim($_POST['nama_vendor'] ?? '');
    $bh     = $_POST['badan_hukum'] ?? 'Y';
    $status = $_POST['status'] ?? 'A';

    if ($aksi === 'tambah') {
        if ($nama === '') {
            set_flash('error', 'Nama vendor wajib diisi.');
        } else {
            $pdo->prepare("INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)")
                ->execute([$nama, $bh, $status]);
            set_flash('success', 'Vendor berhasil ditambahkan.');
        }
    } elseif ($aksi === 'edit') {
        $id = (int)($_POST['idvendor'] ?? 0);
        if ($nama === '') {
            set_flash('error', 'Nama vendor wajib diisi.');
        } else {
            $pdo->prepare("UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?")
                ->execute([$nama, $bh, $status, $id]);
            set_flash('success', 'Data vendor berhasil diperbarui.');
        }
    } elseif ($aksi === 'nonaktif') {
        $id = (int)($_POST['idvendor'] ?? 0);
        $pdo->prepare("UPDATE vendor SET status = 'N' WHERE idvendor = ?")->execute([$id]);
        set_flash('success', 'Vendor berhasil dinonaktifkan.');
    }
    header('Location: vendor.php');
    exit;
}

$filter   = $_GET['filter'] ?? 'aktif';
$whereSql = ($filter === 'semua') ? '' : "WHERE status = 'Aktif'";
$rows     = $pdo->query("SELECT * FROM v_vendor $whereSql ORDER BY idvendor ASC")->fetchAll();

$editData = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM vendor WHERE idvendor = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$pageTitle = 'Data Vendor';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Data Vendor</h1>
    <p class="text-sm text-slate-500">Daftar supplier pengadaan barang.</p>
  </div>
  <div class="flex gap-2 text-xs font-semibold">
    <a href="?filter=aktif" class="px-3 py-2 rounded-lg border <?= $filter === 'aktif' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Aktif Saja</a>
    <a href="?filter=semua" class="px-3 py-2 rounded-lg border <?= $filter === 'semua' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Semua Data</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit Vendor' : 'Tambah Vendor Baru' ?></h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
      <?php if ($editData): ?>
        <input type="hidden" name="idvendor" value="<?= (int)$editData['idvendor'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Vendor</label>
        <input type="text" name="nama_vendor" required value="<?= e($editData['nama_vendor'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Badan Hukum</label>
        <select name="badan_hukum" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <option value="Y" <?= (($editData['badan_hukum'] ?? 'Y') === 'Y') ? 'selected' : '' ?>>Berbadan Hukum (PT / CV)</option>
          <option value="N" <?= (($editData['badan_hukum'] ?? '') === 'N') ? 'selected' : '' ?>>Non-Badan Hukum (UD / Toko)</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
        <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <option value="A" <?= (($editData['status'] ?? 'A') === 'A') ? 'selected' : '' ?>>Aktif</option>
          <option value="N" <?= (($editData['status'] ?? '') === 'N') ? 'selected' : '' ?>>Non-Aktif</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
        <?php if ($editData): ?>
          <a href="vendor.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Nama Vendor</th>
          <th class="px-4 py-3">Badan Hukum</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 font-medium"><?= e($r['nama_vendor']) ?></td>
            <td class="px-4 py-3 text-slate-600"><?= e($r['badan_hukum']) ?></td>
            <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="?edit=<?= (int)$r['idvendor'] ?>&filter=<?= e($filter) ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
              <?php if ($r['status'] === 'Aktif'): ?>
                <form method="POST" class="inline" onsubmit="return confirm('Nonaktifkan vendor ini?')">
                  <input type="hidden" name="aksi" value="nonaktif">
                  <input type="hidden" name="idvendor" value="<?= (int)$r['idvendor'] ?>">
                  <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Nonaktifkan</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>