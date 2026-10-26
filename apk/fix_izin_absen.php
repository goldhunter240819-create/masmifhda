<?php
require_once 'koneksi.php';

$stmt = $siakad_db->query("SELECT id, guru_id, tanggal_izin, keterangan FROM ajuan_izin_guru WHERE status = 'Disetujui'");
$ajuans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach($ajuans as $a) {
    $guru_id = $a['guru_id'];
    $tgl = $a['tanggal_izin'];
    $ket = $a['keterangan'];

    $status_absen = 'Izin';
    if (stripos($ket, 'Sakit') !== false) {
        $status_absen = 'Sakit';
    } elseif (stripos($ket, 'Dinas') !== false) {
        $status_absen = 'Dinas Luar';
    } elseif (stripos($ket, 'Alpa') !== false) {
        $status_absen = 'Alpa';
    }

    $cek = $siakad_db->prepare("SELECT id FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
    $cek->execute([$guru_id, $tgl]);
    
    if (!$cek->fetch()) {
        $ins = $siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, status, created_at) VALUES (?, ?, ?, NOW())");
        $ins->execute([$guru_id, $tgl, $status_absen]);
        $count++;
    } else {
        $upd = $siakad_db->prepare("UPDATE absensi_guru SET status = ? WHERE guru_id = ? AND tanggal = ?");
        $upd->execute([$status_absen, $guru_id, $tgl]);
        $count++;
    }
}
echo "Fixed $count records.";
?>
