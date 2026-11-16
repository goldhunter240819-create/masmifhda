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

// Check live active mengajar
require_once __DIR__ . '/app/Core/Database.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    
    // Check bs_topik and bs_paket
    $topiks = $db->query("SELECT id, jenis, nama_topik, status FROM bs_topik")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "\nLIVE TOPIK: " . json_encode($topiks) . "\n";
    
    $groupPaket = $db->query("SELECT topik_id, COUNT(*) as cnt FROM bs_paket GROUP BY topik_id")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "LIVE BS_PAKET by topik_id: " . json_encode($groupPaket) . "\n";
    
    $orphans = $db->query("SELECT id, topik_id, mapel_nama, kelas_nama FROM bs_paket WHERE topik_id NOT IN (SELECT id FROM bs_topik) OR topik_id IS NULL OR topik_id = 0")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "LIVE ORPHANS (" . count($orphans) . "): " . json_encode($orphans) . "\n";
} catch (\Exception $e) {
    $output .= "\nError querying bs_paket: " . $e->getMessage() . "\n";
}



// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
