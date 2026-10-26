<?php
require 'koneksi.php';
try { 
    $sql = "SELECT a.siswa_id, a.status, m.nama_mapel, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, asis.jam_masuk 
            FROM " . $siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a 
            JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id 
            JOIN " . $siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id 
            LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id 
            LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id 
            LEFT JOIN " . $siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_siswa asis ON asis.siswa_id = s.id AND asis.tanggal = a.tanggal 
            WHERE a.tanggal = '2026-08-27' AND s.status = 'Aktif'"; 
    $stmt = $siakad_db->prepare($sql); 
    $stmt->execute(); 
    var_dump(count($stmt->fetchAll())); 
} catch(Exception $e) { 
    echo $e->getMessage(); 
}
