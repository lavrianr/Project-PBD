<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi     = $_POST['aksi'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $idrole   = (int)($_POST['idrole'] ?? 0);

    if ($aksi === 'tambah') {
        if ($username === '' || $password === '' || $idrole <= 0) {
            set_flash('error', 'Semua field wajib diisi.');
        } else {
            $cek = $pdo->prepare("SELECT COUNT(*) FROM `user` WHERE username = ?");
            $cek->execute([$username]);
            if ($cek->fetchColumn() > 0) {
                set_flash('error', 'Username sudah digunakan.');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO `user` (username, password, idrole) VALUES (?, ?, ?)")
                    ->execute([$username, $hash, $idrole]);
                set_flash('success', 'User baru berhasil ditambahkan.');
            }
        }
    } elseif ($aksi === 'edit') {
        $id = (int)($_POST['iduser'] ?? 0);
        if ($username === '' || $idrole <= 0) {
            set_flash('error', 'Username dan role wajib diisi.');
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE `user` SET username = ?, password = ?, idrole = ? WHERE iduser = ?")
                    ->execute([$username, $hash, $idrole, $id]);
            } else {
                $pdo->prepare("UPDATE `user` SET username = ?, idrole = ? WHERE iduser = ?")
                    ->execute([$username, $idrole, $id]);
            }
            set_flash('success', 'Data user berhasil diperbarui.');
        }
    } elseif ($aksi === 'hapus') {
        $id = (int)($_POST['iduser'] ?? 0);
        if ($id === (int)$_SESSION['user']['iduser']) {
            set_flash('error', 'Tidak dapat menghapus akun yang sedang login.');
        } else {
            $cek1 = $pdo->prepare("SELECT COUNT(*) FROM pengadaan WHERE user_iduser = ?");
            $cek1->execute([$id]);
            $cek2 = $pdo->prepare("SELECT COUNT(*) FROM penjualan WHERE iduser = ?");
            $cek2->execute([$id]);
            if ($cek1->fetchColumn() > 0 || $cek2->fetchColumn() > 0) {
                set_flash('error', 'User tidak bisa dihapus karena memiliki riwayat transaksi.');
            } else {
                $pdo->prepare("DELETE FROM `user` WHERE iduser = ?")->execute([$id]);
                set_flash('success', 'User berhasil dihapus.');
            }
        }
    }
    header('Location: user.php');
    exit;
}

$editData = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT iduser, username, idrole FROM `user` WHERE iduser = ?");
    $st->execute([(int)$_GET['edit']]);
    $editData = $st->fetch();
}

$roles = $pdo->query("SELECT * FROM `role` ORDER BY idrole ASC")->fetchAll();
$rows  = $pdo->query("SELECT * FROM v_user ORDER BY iduser ASC")->fetchAll();
$pageTitle = 'Manajemen User';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-slate-900">Manajemen User</h1>
  <p class="text-sm text-slate-500">Kelola akun pengguna dan peran akses.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white p-6 rounded-xl border border-slate-200 h-fit">
    <h2 class="text-base font-bold mb-4"><?= $editData ? 'Edit User' : 'Tambah User Baru' ?></h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
      <?php if ($editData): ?>
        <input type="hidden" name="iduser" value="<?= (int)$editData['iduser'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
        <input type="text" name="username" required value="<?= e($editData['username'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Password <?= $editData ? '<span class="text-xs text-slate-400">(kosongkan jika tetap)</span>' : '' ?>
        </label>
        <input type="password" name="password" <?= $editData ? '' : 'required' ?>
               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
        <select name="idrole" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
          <?php foreach ($roles as $rl): ?>
            <option value="<?= (int)$rl['idrole'] ?>" <?= (($editData['idrole'] ?? 0) == $rl['idrole']) ? 'selected' : '' ?>>
              <?= e($rl['nama_role']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
        <?php if ($editData): ?>
          <a href="user.php" class="px-4 py-2 border border-slate-300 text-sm rounded-lg">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Username</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="px-4 py-3 text-slate-500">#<?= (int)$r['iduser'] ?></td>
            <td class="px-4 py-3 font-medium"><?= e($r['username']) ?></td>
            <td class="px-4 py-3"><?= e($r['role']) ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="?edit=<?= (int)$r['iduser'] ?>" class="text-xs font-semibold text-slate-700 hover:underline">Edit</a>
              <form method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="iduser" value="<?= (int)$r['iduser'] ?>">
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