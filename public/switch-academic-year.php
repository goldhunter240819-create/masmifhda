<?php
// /public/switch-academic-year.php
session_name("AULA_SYSTEM_PORTAL");
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        $_SESSION['selected_tahun_ajaran_id'] = $id;
    } else {
        // Reset to global active year
        unset($_SESSION['selected_tahun_ajaran_id']);
    }
}

// Redirect back to the respective dashboard to avoid getting stuck on filtered pages
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/portal';

if (strpos($referer, '/admin/finance') !== false) {
    $redirect = '/admin/finance';
} elseif (strpos($referer, '/admin/siakad') !== false) {
    $redirect = '/admin/siakad/dashboard';
} elseif (strpos($referer, '/admin/absen_v2') !== false) {
    $redirect = '/admin/absen_v2';
} else {
    $redirect = '/portal';
}

header("Location: " . $redirect);
exit;
