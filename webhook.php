<?php
echo "Menerima request webhook MAS Mifhda...\n";

// 1. Bersihkan perubahan lokal tak sengaja di server agar pull tidak abort/konflik
shell_exec('git -c safe.directory="*" checkout -- . 2>&1');
shell_exec('git -c safe.directory="*" clean -fd 2>&1');

// 2. Tarik kode terbaru dari GitHub origin/main
$output = shell_exec('git -c safe.directory="*" pull origin main 2>&1');

// 3. Reset OPcache agar PHP langsung menyajikan kodingan terbaru tanpa restart Apache
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
