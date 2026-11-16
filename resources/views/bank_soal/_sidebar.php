<?php
/**
 * Sidebar Bank Soal - Terintegrasi dengan CBT Online & Portal Guru
 */
$active_menu = 'bank_soal';
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 2) {
    include __DIR__ . '/../portal/sidebar_multi_guru.php';
} else {
    include __DIR__ . '/../cbt/sidebar.php';
}

