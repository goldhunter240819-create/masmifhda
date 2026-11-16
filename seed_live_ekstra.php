<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/vendor/autoload.php';

echo "=== DIAGNOSTIK SERVER LIVE ===\n";
echo "Git log:\n";
echo shell_exec('git -c safe.directory="*" log -1 --oneline 2>&1') . "\n";

echo "Git pull:\n";
echo shell_exec('git -c safe.directory="*" pull origin main 2>&1') . "\n";

echo "Running CbtController::ensureTables()...\n";
try {
    $db = \App\Core\Database::connect('core');
    \App\Controllers\CbtController::ensureTables($db);
    echo "ensureTables SUCCESS!\n";

    $topiks = $db->query("SELECT id, jenis, nama_topik, tgl_mulai, tgl_selesai, status FROM bs_topik ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    print_r($topiks);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
