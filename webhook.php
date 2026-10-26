<?php
echo "Menerima request webhook MAS Mifhda...\n";

// Menggunakan git -c safe.directory="*" untuk mengakali error izin user Windows (Apache vs User)
$output = shell_exec('git -c safe.directory="*" pull origin main 2>&1');

// Catat ke log biar bisa dicek kalau gagal
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
file_put_contents('webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
