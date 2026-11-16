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

// Check live bs_paket
require_once __DIR__ . '/app/Core/Database.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    $pkts = $db->query("SELECT id, mapel_nama, kelas_nama, guru_nama FROM bs_paket WHERE topik_id = 3 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "\nLIVE BS_PAKET (Topik 3) Count: " . count($pkts) . "\n";
    foreach ($pkts as $p) {
        $output .= "ID: {$p['id']} | Mapel: {$p['mapel_nama']} | Kelas: {$p['kelas_nama']} | Guru: {$p['guru_nama']}\n";
    }
} catch (\Exception $e) {
    $output .= "\nError querying bs_paket: " . $e->getMessage() . "\n";
}

// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
