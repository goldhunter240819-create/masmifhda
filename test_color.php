<?php
$files = [
    'c:/xampp/htdocs/masmifhda/resources/views/siakad/jurnal_global.php',
    'c:/xampp/htdocs/masmifhda/resources/views/siakad/cetak_jurnal_global.php',
    'c:/xampp/htdocs/masmifhda/resources/views/admin/kurikulum_monitoring_jurnal.php'
];

$pattern = '/color:#b91c1c;\s*background:#fef2f2;/';
$replacement = 'color:#ffffff; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); border:none;';

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Replaced in $file\n";
        } else {
            echo "No change in $file\n";
        }
    }
}
