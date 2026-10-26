<?php
require_once 'apk/koneksi.php';

$hari_indo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];

// Ambil semua jurnal
$stmt_jurnal = $siakad_db->query("SELECT j.id, j.tanggal, j.kelas_id, j.mapel_id, j.guru_id, m.nama_mapel 
    FROM jurnal_guru j 
    LEFT JOIN mapel m ON j.mapel_id = m.id 
    ORDER BY j.tanggal");
$jurnals = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua jadwal
$stmt_jadwal = $siakad_db->query("SELECT j.hari, j.kelas_id, j.guru_id, j.mata_pelajaran_id, m.nama_mapel 
    FROM jadwal_pelajaran j 
    LEFT JOIN mapel m ON j.mata_pelajaran_id = m.id");
$jadwals = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

$mistakes = [];

foreach($jurnals as $jd) {
    $hari_inggris = date('l', strtotime($jd['tanggal']));
    $hari = $hari_indo[$hari_inggris];
    
    // Cari apakah mapel_id yang diisi ada di jadwal hari ini, kelas ini, guru ini
    $found_schedule = false;
    $scheduled_mapels = [];
    
    foreach($jadwals as $jdw) {
        if ($jdw['hari'] == $hari && $jdw['kelas_id'] == $jd['kelas_id'] && $jdw['guru_id'] == $jd['guru_id']) {
            $scheduled_mapels[$jdw['mata_pelajaran_id']] = $jdw['nama_mapel'];
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
            'guru_id' => $jd['guru_id'],
            'kelas_id' => $jd['kelas_id'],
            'mapel_diisi' => $jd['nama_mapel'],
            'mapel_id_diisi' => $jd['mapel_id'],
            'jadwal_seharusnya' => empty($scheduled_mapels) ? "Tidak ada jadwal" : implode(", ", array_values($scheduled_mapels)),
            'jadwal_ids' => array_keys($scheduled_mapels)
        ];
    }
}

// Filter hanya mistake yang "jelas" (ada 1 mapel terjadwal, tapi dia ngisi mapel lain, dan kita bisa auto-fix)
$auto_fixable = [];
foreach($mistakes as $m) {
    if (count($m['jadwal_ids']) == 1) { // Hanya ada 1 mapel di hari itu, jadi pasti itu maksudnya
        $m['fix_to'] = $m['jadwal_ids'][0];
        $auto_fixable[] = $m;
    }
}

$summary = [];
foreach($mistakes as $m) {
    $summary[$m['guru_id']][] = $m;
}

foreach($summary as $guru => $m_list) {
    echo "\n=== Guru ID $guru (" . count($m_list) . " mistakes) ===\n";
    foreach($m_list as $m) {
        echo "- {$m['tanggal']} ({$m['hari']}) di kelas {$m['kelas_id']}: Ngisi '{$m['mapel_diisi']}' (ID: {$m['mapel_id_diisi']}) -> Jadwal aslinya: {$m['jadwal_seharusnya']}\n";
    }
}

echo "\nTotal mistakes: " . count($mistakes) . "\n";
echo "Auto fixable: " . count($auto_fixable) . "\n";

file_put_contents('mistakes.json', json_encode($auto_fixable, JSON_PRETTY_PRINT));
?>
