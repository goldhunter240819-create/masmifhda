<?php
require 'apk/koneksi.php';
try {
    $hari_ini = 'Senin';
    $core_db_name = $core_db->query("SELECT database()")->fetchColumn();
    $stmt = $siakad_db->prepare("SELECT j.jam_mulai, j.jam_selesai, m.nama_mapel, k.nama_kelas, g.nama as nama_guru FROM jadwal_pelajaran j LEFT JOIN {$core_db_name}.mapel m ON j.mata_pelajaran_id = m.id LEFT JOIN {$core_db_name}.kelas k ON j.kelas_id = k.id LEFT JOIN {$core_db_name}.guru g ON j.guru_id = g.id WHERE j.hari = ? ORDER BY j.jam_mulai ASC, k.tingkat ASC, k.nama_kelas ASC");
    $stmt->execute([$hari_ini]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Found: '.count($res) . "\n";
    if (count($res) > 0) print_r($res[0]);
} catch (Exception $e) { echo $e->getMessage() . "\n"; }
