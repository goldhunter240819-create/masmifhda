<?php
echo "Menerima request webhook MAS Mifhda...\n";

// 1. Bersihkan perubahan lokal tak sengaja di server agar pull tidak abort/konflik
shell_exec('git -c safe.directory="*" checkout -- . 2>&1');
shell_exec('git -c safe.directory="*" clean -fd 2>&1');

// 2. Tarik kode terbaru dari GitHub origin/main dan paksa sync ke HEAD origin/main
shell_exec('git -c safe.directory="*" fetch origin main 2>&1');
$output = shell_exec('git -c safe.directory="*" reset --hard origin/main 2>&1');
$headLog = shell_exec('git -c safe.directory="*" log -1 --oneline 2>&1');
$output .= "\nHEAD: " . trim($headLog);

// 3. Reset OPcache agar PHP langsung menyajikan kodingan terbaru tanpa restart Apache
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// 3.5 Bersihkan & Migrasikan Bank Soal ke Basis Tingkat
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Controllers/BankSoalController.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    \App\Controllers\BankSoalController::ensureTables($db);
    $activeTopikIds = $db->query("SELECT id FROM bs_topik WHERE status = 'Aktif'")->fetchAll(PDO::FETCH_COLUMN);
    $bsCtrl = new \App\Controllers\BankSoalController();
    foreach ($activeTopikIds as $actId) {
        $bsCtrl->syncFromPengampu((int)$actId, true);
    }
    $output .= "\nBank Soal synced to Tingkat successfully.\n";
} catch (\Exception $e) {
    $output .= "\nBank Soal migration: " . $e->getMessage() . "\n";
}



// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
