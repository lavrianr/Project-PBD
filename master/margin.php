<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi   = $_POST['aksi'] ?? '';
    $iduser = (int)$_SESSION['user']['iduser'];

    if ($aksi === 'simpan') {
        $id     = (int)($_POST['idmargin_penjualan'] ?? 0);
        $persen = (float)($_POST['persen'] ?? -1);
        $status = (int)($_POST['status'] ?? 0);

        if ($persen < 0) {
            set_flash('error', 'Persentase margin tidak boleh kurang dari 0%.');
        } elseif ($id > 0 && $status === 0) {
            $cekAktif = $pdo->prepare("SELECT COUNT(*) FROM margin_penjualan WHERE status = 1 AND idmargin_penjualan <> ?");
            $cekAktif->execute([$id]);
            if ($cekAktif->fetchColumn() == 0) {
                set_flash('error', 'Minimal harus ada 1 margin yang aktif. Aktifkan margin lain terlebih dahulu.');
                header('Location: margin.php');
                exit;
            }
            $pdo->prepare("CALL sp_simpan_margin(?, ?, ?, ?)")->execute([$id, $persen, $status, $iduser]);
            set_flash('success', 'Data margin berhasil disimpan.');
        } else {
            $pdo->prepare("CALL sp_simpan_margin(?, ?, ?, ?)")->execute([$id, $persen, $status, $iduser]);
            set_flash('success', 'Data margin berhasil disimpan.');
        }
    } elseif ($aksi === 'aktifkan') {
        $id = (int)($_POST['idmargin_penjualan'] ?? 0);
        $st = $pdo->prepare("SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = ?");
        $st->execute([$id]);
        $persen = (float)$st->fetchColumn();
        $pdo->prepare("CALL sp_simpan_margin(?, ?, 1, ?)")->execute([$id, $persen, $iduser]);
        set_flash('success', 'Margin berhasil diaktifkan (margin lain otomatis dinonaktifkan).');
    }
    header('Location: margin.php');
    exit;
}

$filter   = $_GET['filter'] ?? 'semua';
$whereSql = ($filter === 'aktif') ? "WHERE status = 'Aktif'" : '';
$rows     = $pdo->query("SELECT * FROM v_margin_penjualan $whereSql ORDER BY status ASC, idmargin_penjualan ASC")->fetchAll();

$editData = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$pageTitle = 'Margin Penjualan';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Margin Penjualan</h1>
    <p class="text-sm text-slate-500">Hanya satu margin yang dapat aktif dalam satu waktu.</p>
  </div>
  <div class="flex gap-2 text-xs font-semibold">
    <a href="?filter=semua" class="px-3 py-2 rounded-lg border <?= $filter === 'semua' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Semua Data</a>
    <a href="?filter=aktif" class="px-3 py-2 rounded-lg border <?= $filter === 'aktif' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600' ?>">Aktif Saja</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit Margin' : 'Tambah Margin Baru' ?></h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="aksi" value="simpan">
      <input type="hidden" name="idmargin_penjualan" value="<?= (int)($editData['idmargin_penjualan'] ?? 0) ?>">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Persentase Margin (%)</label>
        <input type="number" step="0.1" min="0" name="persen" required value="<?= e($editData['persen'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
        <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <option value="1" <?= (($editData['status'] ?? 1) == 1) ? 'selected' : '' ?>>Aktif (Otomatis nonaktifkan yang lain)</option>
          <option value="0" <?= (($editData['status'] ?? 1) == 0) ? 'selected' : '' ?>>Non-Aktif</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
        <?php if ($editData): ?>
          <a href="margin.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">Persen Margin</th>
          <th class="px-4 py-3">Diatur Oleh</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 font-bold text-slate-900"><?= (float)$r['persen'] ?>%</td>
            <td class="px-4 py-3 text-slate-600"><?= e($r['user']) ?></td>
            <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="?edit=<?= (int)$r['idmargin_penjualan'] ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
              <?php if ($r['status'] === 'Non-Aktif'): ?>
                <form method="POST" class="inline">
                  <input type="hidden" name="aksi" value="aktifkan">
                  <input type="hidden" name="idmargin_penjualan" value="<?= (int)$r['idmargin_penjualan'] ?>">
                  <button type="submit" class="text-xs font-semibold text-emerald-600 hover:underline">Jadikan Aktif</button>
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