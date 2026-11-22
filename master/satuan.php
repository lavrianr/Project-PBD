<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi   = $_POST['aksi'] ?? '';
    $nama   = trim($_POST['nama_satuan'] ?? '');
    $status = (int)($_POST['status'] ?? 1);

    if ($aksi === 'tambah') {
        if ($nama === '') {
            set_flash('error', 'Nama satuan wajib diisi.');
        } else {
            $pdo->prepare("INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)")->execute([$nama, $status]);
            set_flash('success', 'Satuan berhasil ditambahkan.');
        }
    } elseif ($aksi === 'edit') {
        $id = (int)($_POST['idsatuan'] ?? 0);
        if ($nama === '') {
            set_flash('error', 'Nama satuan wajib diisi.');
        } else {
            $pdo->prepare("UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?")->execute([$nama, $status, $id]);
            set_flash('success', 'Satuan berhasil diperbarui.');
        }
    } elseif ($aksi === 'nonaktif') {
        $id = (int)($_POST['idsatuan'] ?? 0);
        $pdo->prepare("UPDATE satuan SET status = 0 WHERE idsatuan = ?")->execute([$id]);
        set_flash('success', 'Satuan berhasil dinonaktifkan.');
    }
    header('Location: satuan.php');
    exit;
}

$filter   = $_GET['filter'] ?? 'aktif';
$whereSql = ($filter === 'semua') ? '' : "WHERE status = 'Aktif'";
$rows     = $pdo->query("SELECT * FROM v_satuan $whereSql ORDER BY idsatuan ASC")->fetchAll();

$editData = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM satuan WHERE idsatuan = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$pageTitle = 'Data Satuan';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Data Satuan</h1>
    <p class="text-sm text-slate-500">Unit satuan untuk master barang.</p>
  </div>
  <div class="flex gap-2 text-xs font-semibold">
    <a href="?filter=aktif" class="px-3 py-2 rounded-lg border <?= $filter === 'aktif' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Aktif Saja</a>
    <a href="?filter=semua" class="px-3 py-2 rounded-lg border <?= $filter === 'semua' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Semua Data</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit Satuan' : 'Tambah Satuan Baru' ?></h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
      <?php if ($editData): ?>
        <input type="hidden" name="idsatuan" value="<?= (int)$editData['idsatuan'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Satuan</label>
        <input type="text" name="nama_satuan" required value="<?= e($editData['nama_satuan'] ?? '') ?>"
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
          <a href="satuan.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Nama Satuan</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 text-slate-500">#<?= (int)$r['idsatuan'] ?></td>
            <td class="px-4 py-3 font-medium"><?= e($r['nama_satuan']) ?></td>
            <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="?edit=<?= (int)$r['idsatuan'] ?>&filter=<?= e($filter) ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
              <?php if ($r['status'] === 'Aktif'): ?>
                <form method="POST" class="inline" onsubmit="return confirm('Nonaktifkan satuan ini?')">
                  <input type="hidden" name="aksi" value="nonaktif">
                  <input type="hidden" name="idsatuan" value="<?= (int)$r['idsatuan'] ?>">
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