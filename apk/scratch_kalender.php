<?php
require 'c:\xampp\htdocs\masmifhda\apk\koneksi.php';

// Ambil semua siswa aktif
$stmt = $core_db->query("SELECT id, kelas_id FROM siswa WHERE status = 'Aktif'");
$siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tgls = ['2026-08-17', '2026-08-18'];

foreach($siswa_list as $s) {
    foreach($tgls as $t) {
        $cek = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
        $cek->execute([$s['id'], $t]);
        
        if ($row = $cek->fetch()) {
            $upd = $siakad_db->prepare("UPDATE absensi_siswa SET status = 'Hadir' WHERE id = ?");
            $upd->execute([$row['id']]);
        } else {
            $ins = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, 'Hadir')");
            $ins->execute([$s['id'], $s['kelas_id'], $t]);
        }
    }
}
echo "All active students marked as Hadir for 17 and 18 Aug 2026.\n";
