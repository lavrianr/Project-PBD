<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idvendor = (int)($_POST['vendor_idvendor'] ?? 0);
    $iduser   = (int)$_SESSION['user']['iduser'];

    $cek = $pdo->prepare("SELECT COUNT(*) FROM vendor WHERE idvendor = ? AND status = 'A'");
    $cek->execute([$idvendor]);
    if ($cek->fetchColumn() == 0) {
        set_flash('error', 'Pilih vendor yang valid dan berstatus Aktif.');
        header('Location: index.php');
        exit;
    }

    $st = $pdo->prepare("INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai) VALUES (NOW(), ?, 'P', ?, 0, 0, 0)");
    $st->execute([$iduser, $idvendor]);
    $idpengadaan = (int)$pdo->lastInsertId();

    set_flash('success', 'Header pengadaan #' . $idpengadaan . ' dibuat. Silakan masukkan daftar barang.');
    header('Location: detail.php?id=' . $idpengadaan);
    exit;
}

$vendors    = $pdo->query("SELECT * FROM v_vendor WHERE status = 'Aktif' ORDER BY nama_vendor ASC")->fetchAll();
$rows       = $pdo->query("SELECT * FROM v_pengadaan ORDER BY idpengadaan DESC")->fetchAll();
$pageTitle  = 'Pengadaan Barang';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900">Pengadaan Barang</h1>
  <p class="text-sm text-slate-500">Langkah 1: Pilih vendor untuk membuat nomor nota pengadaan baru.</p>
</div>

<div class="bg-white p-5 rounded-xl border border-slate-200 mb-6">
  <form method="POST" class="flex flex-wrap items-end gap-4">
    <div class="flex-1 min-w-[240px]">
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Pilih Vendor Tujuan</label>
      <select name="vendor_idvendor" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        <option value="">-- Pilih Vendor Aktif --</option>
        <?php foreach ($vendors as $v): ?>
          <option value="<?= (int)$v['idvendor'] ?>"><?= e($v['nama_vendor']) ?> (<?= e($v['badan_hukum']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
      Buat Pengadaan Baru &rarr;
    </button>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
  <table class="w-full text-sm text-left">
    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
      <tr>
        <th class="px-4 py-3">No. Pengadaan</th>
        <th class="px-4 py-3">Waktu</th>
        <th class="px-4 py-3">Vendor</th>
        <th class="px-4 py-3">Admin</th>
        <th class="px-4 py-3">Total Nilai</th>
        <th class="px-4 py-3">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada data pengadaan.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-bold">#<?= (int)$r['idpengadaan'] ?></td>
          <td class="px-4 py-3 text-slate-500"><?= e($r['timestamp']) ?></td>
          <td class="px-4 py-3 font-medium"><?= e($r['vendor']) ?></td>
          <td class="px-4 py-3 text-slate-600"><?= e($r['user']) ?></td>
          <td class="px-4 py-3 font-semibold"><?= rupiah($r['total_nilai']) ?></td>
          <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
          <td class="px-4 py-3 text-right">
            <a href="detail.php?id=<?= (int)$r['idpengadaan'] ?>" class="text-xs font-semibold text-slate-900 hover:underline">
              <?= $r['status'] === 'Diproses' ? 'Isi / Kelola Item' : 'Lihat Detail' ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>