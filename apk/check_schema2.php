<?php
require_once 'koneksi.php';
try {
    $res = $siakad_db->query('DESCRIBE absensi_siswa');
    echo "absensi_siswa columns:\n";
    foreach($res->fetchAll(PDO::FETCH_COLUMN) as $col) echo "  - $col\n";
} catch(Exception $e) { echo "Error absensi_siswa: " . $e->getMessage() . "\n"; }
try {
    $res = $siakad_db->query('DESCRIBE jurnal_guru');
    echo "jurnal_guru columns:\n";
    foreach($res->fetchAll(PDO::FETCH_COLUMN) as $col) echo "  - $col\n";
} catch(Exception $e) { echo "Error jurnal_guru: " . $e->getMessage() . "\n"; }
