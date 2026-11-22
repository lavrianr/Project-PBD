<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

header('Location: ' . base_url(!empty($_SESSION['user']) ? 'dashboard.php' : 'login.php'));
exit;