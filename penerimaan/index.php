<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpengadaan = (int)($_POST['idpengadaan'] ?? 0);
    $iduser = (int)$_SESSION['user']['iduser'];

    $cek =$pdo->prepare("SELECT COUNT(*) FROM pengadaan WHERE idpengadaan = ? AND status = 'P'");
    $cek->execute([$idpengadaan]);
    if ($cek->fetchColumn() == 0) {
        set_flash('error', 'Pilih nomor pengadaan yang masih berstatus Diproses.');
        header('Location: index.php');
        exit;
    }

    $st =$pdo->prepare("INSERT INTO penerimaan (created_at, status, idpengadaan, iduser) VALUES (NOW(), 'P', ?, ?)");
    $st->execute([$idpengadaan,$iduser]);
    $idpenerimaan = (int)$pdo->lastInsertId();

    set_flash('success', 'Header penerimaan #' . $idpenerimaan . ' dibuat. Silakan input barang yang diterima.');
    header('Location: detail.php?id=' . $idpenerimaan);
    exit;
}

$listPengadaan =$pdo->query("
    SELECT p.idpengadaan, p.`timestamp`, p.vendor, p.total_nilai
    FROM v_pengadaan p
    WHERE p.status = 'Diproses'
      AND EXISTS (SELECT 1 FROM detail_pengadaan d WHERE d.idpengadaan = p.idpengadaan)
    ORDER BY p.idpengadaan DESC
")->fetchAll();

$rows =$pdo->query("
    SELECT pn.*, pg.vendor
    FROM v_penerimaan pn
    JOIN v_pengadaan pg ON pg.idpengadaan = pn.idpengadaan
    ORDER BY pn.idpenerimaan DESC
")->fetchAll();

$pageTitle = 'Penerimaan Barang';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900">Penerimaan Barang</h1>
  <p class="text-sm text-slate-500">Langkah 1: Pilih nomor pengadaan yang barangnya datang dari vendor.</p>
</div>

<div class="bg-white p-5 rounded-xl border border-slate-200 mb-6">
  <form method="POST" class="flex flex-wrap items-end gap-4">
    <div class="flex-1 min-w-[260px]">
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Pilih Pengadaan (Status Diproses)</label>
      <select name="idpengadaan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
        <option value="">-- Pilih Nomor Pengadaan --</option>
        <?php foreach ($listPengadaan as$pg): ?>
          <option value="<?= (int)$pg['idpengadaan'] ?>">
            Pengadaan #<?= (int)$pg['idpengadaan'] ?> — <?= e($pg['vendor']) ?> (<?= rupiah($pg['total_nilai']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
      Buat Penerimaan Baru &rarr;
    </button>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
  <table class="w-full text-sm text-left">
    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
      <tr>
        <th class="px-4 py-3">No. Terima</th>
        <th class="px-4 py-3">Ref. Pengadaan</th>
        <th class="px-4 py-3">Vendor</th>
        <th class="px-4 py-3">Penerima</th>
        <th class="px-4 py-3">Waktu</th>
        <th class="px-4 py-3">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada data penerimaan.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as$r): ?>
        <tr>
          <td class="px-4 py-3 font-bold">#<?= (int)$r['idpenerimaan'] ?></td>
          <td class="px-4 py-3 text-slate-600">Pengadaan #<?= (int)$r['idpengadaan'] ?></td>
          <td class="px-4 py-3 font-medium"><?= e($r['vendor']) ?></td>
          <td class="px-4 py-3 text-slate-600"><?= e($r['user']) ?></td>
          <td class="px-4 py-3 text-slate-500"><?= e($r['created_at']) ?></td>
          <td class="px-4 py-3"><?= badge_status($r['status']) ?></td>
          <td class="px-4 py-3 text-right">
            <a href="detail.php?id=<?= (int)$r['idpenerimaan'] ?>" class="text-xs font-semibold text-slate-900 hover:underline">
              <?= $r['status'] !== 'Diterima Utuh' ? 'Input Barang Terima' : 'Lihat Detail' ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>