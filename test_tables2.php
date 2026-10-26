<?php
require_once 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';

$stmt = $siakad_db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Tables matching 'izin' or 'ajuan' or 'tugas' or 'jadwal':\n";
foreach ($tables as $t) {
    if (strpos($t, 'izin') !== false || strpos($t, 'ajuan') !== false || strpos($t, 'tugas') !== false || strpos($t, 'jadwal') !== false || strpos($t, 'guru') !== false) {
        echo "- $t\n";
    }
}
