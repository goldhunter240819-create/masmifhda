<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

// Check all presensi on 2026-11-21
$presToday = $core->query("SELECT * FROM presensi WHERE tanggal = '2026-11-21' ORDER BY jam_masuk ASC")->fetchAll(PDO::FETCH_ASSOC);

// Also check audit_log or similar
$audit = [];
try {
    $audit = $core->query("SELECT * FROM audit_logs WHERE created_at LIKE '2026-11-21%' OR action LIKE '%394%' ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {}

// Also check who printed the card with TOKEN:394:...
// Wait, is there a table `kartu` or `cetak` or `login_log`?
$loginLogs = [];
if (file_exists(__DIR__ . '/../apk/debug_login.log')) {
    $lines = file(__DIR__ . '/../apk/debug_login.log');
    $loginLogs = array_slice($lines, -15);
}

echo json_encode([
    'presToday' => $presToday,
    'audit' => $audit,
    'loginLogs' => $loginLogs
], JSON_PRETTY_PRINT);
