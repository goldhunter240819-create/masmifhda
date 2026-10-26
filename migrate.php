<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';

try {
    $core_db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN nilai_akhir INT NULL AFTER instrumen_json");
} catch (\Exception $ex) {}
try {
    $core_db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN penilai_nama VARCHAR(150) NULL AFTER tindak_lanjut");
} catch (\Exception $ex) {}

$stmt = $core_db->query('SELECT * FROM kurikulum_supervisi_kunjungan_kelas');
while ($row = $stmt->fetch()) {
    $mapel_id = $row['mapel_id'];
    $kelas_id = $row['kelas_id'];
    
    $nm = ''; $nk = '';
    $s_m = $siakad_db->prepare('SELECT nama_mapel FROM mata_pelajaran WHERE id=?');
    $s_m->execute([$mapel_id]); if($r=$s_m->fetch()) $nm = $r['nama_mapel'];
    
    $s_k = $siakad_db->prepare('SELECT nama_kelas FROM kelas WHERE id=?');
    $s_k->execute([$kelas_id]); if($r=$s_k->fetch()) $nk = $r['nama_kelas'];
    
    $s_i = $core_db->prepare('INSERT IGNORE INTO kurikulum_supervisi_kelas (tahun_ajaran_id, guru_id, mata_pelajaran, kelas, jam_ke, materi_pokok, tanggal_supervisi, instrumen_json, nilai_akhir, catatan, tindak_lanjut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $s_i->execute([$row['tahun_ajaran_id'], $row['guru_id'], $nm, $nk, '', '', $row['tanggal_supervisi'], $row['instrumen_json'], $row['nilai_akhir'], $row['catatan'], '']);
}
echo 'Migrated!';
