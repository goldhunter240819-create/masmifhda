<?php
require 'koneksi.php';

$tanggal = '2026-07-23';

// Ambil semua siswa yang punya absen di tanggal ini
$stmt = $siakad_db->prepare("SELECT DISTINCT siswa_id FROM absensi_permapel WHERE tanggal = ?");
$stmt->execute([$tanggal]);
$siswa_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt_semua_absen = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");
$stmt_cek_pusat = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
$stmt_update_pusat = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");

$updated = 0;
foreach ($siswa_ids as $s_id) {
    $stmt_semua_absen->execute([$s_id, $tanggal]);
    $semua_status = $stmt_semua_absen->fetchAll(PDO::FETCH_COLUMN);
    
    $ada_hadir = in_array('Hadir', $semua_status);
    $ada_alpa = in_array('Alpa', $semua_status) || in_array('Alpha', $semua_status);
    
    // Logika Penentuan Status Pusat BARU
    if ($ada_hadir && $ada_alpa) {
        $status_final = 'Bolos';
    } elseif ($ada_alpa && !$ada_hadir) {
        $status_final = 'Alpa';
    } elseif ($ada_hadir) {
        $status_final = 'Hadir';
    } elseif (in_array('Sakit', $semua_status)) {
        $status_final = 'Sakit';
    } elseif (in_array('Izin', $semua_status)) {
        $status_final = 'Izin';
    } else {
        $status_final = 'Hadir';
    }
    
    $stmt_cek_pusat->execute([$s_id, $tanggal]);
    if ($stmt_cek_pusat->fetch()) {
        $stmt_update_pusat->execute([$status_final, $s_id, $tanggal]);
        $updated++;
    }
}

echo "Berhasil mengupdate {$updated} status siswa sesuai logika baru.\n";
