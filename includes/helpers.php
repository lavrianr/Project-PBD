<?php
function e($val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

function rupiah($angka): string {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function set_flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function badge_status(string $label): string {
    $green = ['Aktif', 'Selesai', 'Diterima Utuh', 'Stok Masuk'];
    $amber = ['Diproses', 'Diterima Sebagian'];
    $red = ['Non-Aktif', 'Stok Keluar', 'Belum Ada Item'];

    $class = 'bg-slate-100 text-slate-700 border-slate-200';
    if (in_array($label, $green, true)) {
        $class = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    } elseif (in_array($label, $amber, true)) {
        $class = 'bg-amber-50 text-amber-700 border-amber-200';
    } elseif (in_array($label, $red, true)) {
        $class = 'bg-rose-50 text-rose-700 border-rose-200';
    }
    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $class\">" . e($label) . "</span>";
}