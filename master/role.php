<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $nama = trim($_POST['nama_role'] ?? '');

    if ($aksi === 'tambah') {
        if ($nama === '') {
            set_flash('error', 'Nama role wajib diisi.');
        } else {
            $cek = $pdo->prepare("SELECT COUNT(*) FROM `role` WHERE nama_role = ?");
            $cek->execute([$nama]);
            if ($cek->fetchColumn() > 0) {
                set_flash('error', 'Nama role sudah terdaftar.');
            } else {
                $pdo->prepare("INSERT INTO `role` (nama_role) VALUES (?)")->execute([$nama]);
                set_flash('success', 'Role berhasil ditambahkan.');
            }
        }
    } elseif ($aksi === 'edit') {
        $id = (int)($_POST['idrole'] ?? 0);
        if ($nama === '') {
            set_flash('error', 'Nama role wajib diisi.');
        } else {
            $pdo->prepare("UPDATE `role` SET nama_role = ? WHERE idrole = ?")->execute([$nama, $id]);
            set_flash('success', 'Role berhasil diperbarui.');
        }
    } elseif ($aksi === 'hapus') {
        $id = (int)($_POST['idrole'] ?? 0);
        $cek = $pdo->prepare("SELECT COUNT(*) FROM `user` WHERE idrole = ?");
        $cek->execute([$id]);
        if ($cek->fetchColumn() > 0) {
            set_flash('error', 'Role tidak bisa dihapus karena masih digunakan oleh user.');
        } else {
            $pdo->prepare("DELETE FROM `role` WHERE idrole = ?")->execute([$id]);
            set_flash('success', 'Role berhasil dihapus.');
        }
    }
    header('Location: role.php');
    exit;
}

$editData = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM `role` WHERE idrole = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$rows = $pdo->query("SELECT * FROM v_role ORDER BY idrole ASC")->fetchAll();
$pageTitle = 'Manajemen Role';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900">Manajemen Role</h1>
  <p class="text-sm text-slate-500">Daftar hak akses pengguna dalam sistem.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit Role' : 'Tambah Role Baru' ?></h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
      <?php if ($editData): ?>
        <input type="hidden" name="idrole" value="<?= (int)$editData['idrole'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Role</label>
        <input type="text" name="nama_role" required value="<?= e($editData['nama_role'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">Simpan</button>
        <?php if ($editData): ?>
          <a href="role.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">ID Role</th>
          <th class="px-4 py-3">Nama Role</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 text-slate-500">#<?= (int)$r['idrole'] ?></td>
            <td class="px-4 py-3 font-medium"><?= e($r['nama_role']) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="?edit=<?= (int)$r['idrole'] ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
              <form method="POST" class="inline" onsubmit="return confirm('Hapus role ini?')">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="idrole" value="<?= (int)$r['idrole'] ?>">
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>