<?php
require 'koneksi.php';

// 1. Ubah yang status='' (kosong/invalid ENUM) menjadi 'Alpa'
$stmtUpdate = $siakad_db->query("UPDATE absensi_permapel SET status = 'Alpa' WHERE status = ''");
$affected = $stmtUpdate->rowCount();
echo "Berhasil update {$affected} baris menjadi Alpa di absensi_permapel.\n";

// 2. Ambil daftar tanggal dan siswa yang terkena update
$stmtAffected = $siakad_db->query("SELECT DISTINCT siswa_id, tanggal FROM absensi_permapel WHERE status = 'Alpa'");
$affectedRows = $stmtAffected->fetchAll(PDO::FETCH_ASSOC);

// 3. Recalculate absensi_siswa
$stmt_cek_pusat = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
$stmt_insert_pusat = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
$stmt_update_pusat = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
$stmt_semua_absen = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");

// We need kelas_id for insert if it doesn't exist, we can get it from absensi_permapel
$stmt_get_kelas = $siakad_db->prepare("SELECT kelas_id FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ? LIMIT 1");

$updated_pusat = 0;

foreach ($affectedRows as $row) {
    $s_id = $row['siswa_id'];
    $tanggal = $row['tanggal'];
    
    $stmt_semua_absen->execute([$s_id, $tanggal]);
    $semua_status = $stmt_semua_absen->fetchAll(PDO::FETCH_COLUMN);
    
    $ada_hadir = in_array('Hadir', $semua_status);
    $ada_alpa = in_array('Alpa', $semua_status) || in_array('Alpha', $semua_status);
    
    if ($ada_hadir && $ada_alpa) {
        $status_final = 'Bolos';
    } elseif ($ada_alpa && !$ada_hadir) {
        $status_final = 'Alpa';
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
        $updated_pusat++;
    } else {
        $stmt_get_kelas->execute([$s_id, $tanggal]);
        $k_id_row = $stmt_get_kelas->fetch();
        if ($k_id_row) {
            $stmt_insert_pusat->execute([$s_id, $k_id_row['kelas_id'], $tanggal, $status_final]);
            $updated_pusat++;
        }
    }
}

echo "Berhasil memperbarui / sinkronisasi ulang status pusat untuk {$updated_pusat} data.\n";
