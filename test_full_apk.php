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
    
    // Siapkan array absensi
    $absen_data = [];
    if (count($jurnals) > 0) {
        $jurnal_ids = array_column($jurnals, 'id');
        $inQuery = implode(',', array_fill(0, count($jurnal_ids), '?'));
        
        $stmt_absen = $siakad_db->prepare("
            SELECT jurnal_id, status, COUNT(id) as total
            FROM absensi_permapel
            WHERE jurnal_id IN ($inQuery)
            GROUP BY jurnal_id, status
        ");
        $stmt_absen->execute($jurnal_ids);
        $rekap_absen = $stmt_absen->fetchAll(PDO::FETCH_ASSOC);
        echo "Absen executed.\n";
        
        foreach ($rekap_absen as $ra) {
            $absen_data[$ra['jurnal_id']]['rekap'][$ra['status']] = $ra['total'];
        }
        
        $stmt_absen_detail = $siakad_db->prepare("
            SELECT ap.jurnal_id, ap.siswa_id, ap.status, ap.keterangan 
            FROM absensi_permapel ap
            WHERE ap.jurnal_id IN ($inQuery) AND ap.status != 'Hadir'
        ");
        $stmt_absen_detail->execute($jurnal_ids);
        $detail_absen = $stmt_absen_detail->fetchAll(PDO::FETCH_ASSOC);
        echo "Detail absen executed.\n";
        
        if (count($detail_absen) > 0) {
            $siswa_ids = array_unique(array_column($detail_absen, 'siswa_id'));
            $inSiswa = implode(',', array_fill(0, count($siswa_ids), '?'));
            $stmt_siswa = $core_db->prepare("SELECT id, nama FROM siswa WHERE id IN ($inSiswa)");
            $stmt_siswa->execute($siswa_ids);
            echo "Siswa executed.\n";
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
