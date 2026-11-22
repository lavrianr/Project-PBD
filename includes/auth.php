<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function base_url(string $path = ''): string {
    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '/projectpbd';
    return $base . '/' . ltrim($path, '/'); 
}

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

function is_super_admin(): bool {
    return isset($_SESSION['user']['nama_role']) && $_SESSION['user']['nama_role'] === 'Super Admin';
}

function require_super_admin(): void {
    require_login();
    if (!is_super_admin()) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Akses ditolak. Halaman ini khusus untuk Super Admin.'];
        header('Location: ' . base_url('dashboard.php'));
        exit;
    }
}