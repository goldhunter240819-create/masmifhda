<?php
require_once 'apk/koneksi.php';

$guru_id = 12; // DIDI HARTONO, S.Pd.I
$hari_indo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];

// Ambil semua jurnal Pak Didi
$stmt_jurnal = $siakad_db->query("SELECT j.id, j.tanggal, j.kelas_id, j.mapel_id, m.nama_mapel 
    FROM jurnal_guru j 
    LEFT JOIN mapel m ON j.mapel_id = m.id 
    WHERE j.guru_id = $guru_id ORDER BY j.tanggal");
$jurnals = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua jadwal Pak Didi
$stmt_jadwal = $siakad_db->query("SELECT j.hari, j.kelas_id, j.mata_pelajaran_id, m.nama_mapel 
    FROM jadwal_pelajaran j 
    LEFT JOIN mapel m ON j.mata_pelajaran_id = m.id 
    WHERE j.guru_id = $guru_id");
$jadwals = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

$mistakes = [];

foreach($jurnals as $jd) {
    $hari_inggris = date('l', strtotime($jd['tanggal']));
    $hari = $hari_indo[$hari_inggris];
    
    // Cari apakah mapel_id yang diisi ada di jadwal hari ini dan kelas ini
    $found_schedule = false;
    $scheduled_mapels = [];
    
    foreach($jadwals as $jdw) {
        if ($jdw['hari'] == $hari && $jdw['kelas_id'] == $jd['kelas_id']) {
            $scheduled_mapels[] = $jdw;
            if ($jdw['mata_pelajaran_id'] == $jd['mapel_id']) {
                $found_schedule = true;
            }
        }
    }
    
    if (!$found_schedule) {
        $mistakes[] = [
            'jurnal_id' => $jd['id'],
            'tanggal' => $jd['tanggal'],
            'hari' => $hari,
            'kelas_id' => $jd['kelas_id'],
            'mapel_diisi' => $jd['nama_mapel'],
            'mapel_id_diisi' => $jd['mapel_id'],
            'jadwal_seharusnya' => $scheduled_mapels
        ];
    }
}

echo "Mistakes found: " . count($mistakes) . "\n";
print_r($mistakes);
?>
