<?php
$files = [
    'c:/xampp/htdocs/masmifhda/resources/views/siakad/jurnal_global.php',
    'c:/xampp/htdocs/masmifhda/resources/views/siakad/cetak_jurnal_global.php',
    'c:/xampp/htdocs/masmifhda/resources/views/admin/kurikulum_monitoring_jurnal.php'
];

$pattern = '/<tr <\?php if\(!empty\(\$j\[\'is_duplicate\'\]\)\) echo \'style="background-color: #ffedd5;"(?: title="Jurnal Ganda pada Mapel dan Tanggal yang sama")?\'; \?>>/';
$replacement = '<tr <?php if(!empty($j[\'is_pulang_awal\'])) echo \'style="background: linear-gradient(to right, #eff6ff, #dbeafe); box-shadow: inset 4px 0 0 0 #3b82f6;" title="Agenda Pulang Dipercepat / Rapat"\'; else if(!empty($j[\'is_duplicate\'])) echo \'style="background-color: #ffedd5;" title="Jurnal Ganda pada Mapel dan Tanggal yang sama"\'; ?>>';

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
