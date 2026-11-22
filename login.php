<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['user'])) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT u.iduser, u.username, u.password, u.idrole, r.nama_role
        FROM `user` u
        JOIN `role` r ON r.idrole = u.idrole
        WHERE u.username = ?
    ");
    $stmt->execute([$username]);
    $u = $stmt->fetch();

    if ($u && ($password === $u['password'] || password_verify($password, $u['password']))) {
        $_SESSION['user'] = [
            'iduser'    => $u['iduser'],
            'username'  => $u['username'],
            'idrole'    => $u['idrole'],
            'nama_role' => $u['nama_role'],
        ];
        header('Location: ' . base_url('dashboard.php'));
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Sistem Inventori & Kasir</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
    <div class="mb-6">
      <div class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Proyek Pemrograman Basis Data</div>
      <h1 class="text-2xl font-bold text-slate-900 mt-1">Masuk ke Sistem</h1>
      <p class="text-sm text-slate-500 mt-1">Gunakan akun Super Admin atau Kasir untuk melanjutkan.</p>
    </div>

    <?php if ($error): ?>
      <div class="mb-4 px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
        <input type="text" name="username" required autofocus placeholder="Contoh: superadmin / kasir01"
               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-slate-900">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input type="password" name="password" required placeholder="••••••••"
               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-slate-900">
      </div>
      <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg text-sm transition">
        Masuk
      </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1">
      <div><strong>Super Admin:</strong> <code>superadmin</code> / <code>password123</code></div>
      <div><strong>Kasir:</strong> <code>kasir01</code> / <code>password123</code></div>
    </div>
  </div>
</body>
</html>