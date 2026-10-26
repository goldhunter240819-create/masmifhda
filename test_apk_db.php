<?php
session_start();
$_SESSION['guru_id'] = 15;
$_SESSION['user_id'] = 1;
$_GET['kelas_id'] = 4;
$_GET['mapel_id'] = 1;
$_GET['bulan'] = '2026-07';

require 'apk/koneksi.php';

try {
    $guru_id = 15;
    $filter_bulan = '2026-07';
    $filter_kelas = 4;
    $filter_mapel = 1;
    $query = "
        SELECT j.*, k.nama_kelas, mp.nama_mapel
        FROM jurnal_guru j 
        JOIN aula_db.kelas k ON k.id = j.kelas_id 
        JOIN mata_pelajaran mp ON mp.id = j.mapel_id
        WHERE j.guru_id = ? AND DATE_FORMAT(j.tanggal, '%Y-%m') = ?
        AND j.kelas_id = ? AND j.mapel_id = ?
        ORDER BY j.tanggal DESC, j.id DESC
    ";
    $params = [$guru_id, $filter_bulan, $filter_kelas, $filter_mapel];
    $stmt_jurnal = $siakad_db->prepare($query);
    $stmt_jurnal->execute($params);
    $jurnals = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($jurnals) . " journals.\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
