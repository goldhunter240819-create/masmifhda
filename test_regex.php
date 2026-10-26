<?php
$files = [
    'c:/xampp/htdocs/masmifhda/app/Controllers/SiakadJurnalGlobalController.php',
    'c:/xampp/htdocs/masmifhda/app/Controllers/KurikulumMonitoringJurnalController.php'
];

$pattern = '/\}\s*else if \(\$kal_tipe == \'kegiatan\'\) \{\s*foreach\(\$jadwal_by_hari\[\$hari_id\] as \$jdw\) \{\s*\$key = \$tgl_str\.\'_\'\.\$jdw\[\'kelas_id\'\]\.\'_\'\.\$jdw\[\'mata_pelajaran_id\'\]\.\'_\'\.\$jdw\[\'guru_id\'\];\s*if \(!isset\(\$added_keys\[\$key\]\)\) \{\s*\$jurnal_global\[\] = \[\s*\'id\' => 0,\s*\'guru_id\' => \$jdw\[\'guru_id\'\],\s*\'kelas_id\' => \$jdw\[\'kelas_id\'\],\s*\'mapel_id\' => \$jdw\[\'mata_pelajaran_id\'\],\s*\'tanggal\' => \$tgl_str,\s*\'jam_mulai\' => \$jdw\[\'jam_mulai\'\],\s*\'materi\' => \'Kegiatan: \' \. \$kal_info,\s*\'keterangan\' => \'\',\s*\'created_at\' => \$tgl_str\.\' 00:00:00\',\s*\'nama_guru\' => isset\(\$guruMap\[\$jdw\[\'guru_id\'\]\]\) \? \$guruMap\[\$jdw\[\'guru_id\'\]\] : \'Guru ID \'\.\$jdw\[\'guru_id\'\],\s*\'nama_kelas\' => isset\(\$kelasMap\[\$jdw\[\'kelas_id\'\]\]\) \? \$kelasMap\[\$jdw\[\'kelas_id\'\]\] : \'Kelas ID \'\.\$jdw\[\'kelas_id\'\],\s*\'nama_mapel\' => isset\(\$mapelMap\[\$jdw\[\'mata_pelajaran_id\'\]\]\) \? \$mapelMap\[\$jdw\[\'mata_pelajaran_id\'\]\] : \'Mapel ID \'\.\$jdw\[\'mata_pelajaran_id\'\],\s*\'absen_detail\' => \[\'hadir\'=>0,\'izin\'=>0,\'sakit\'=>0,\'alpa\'=>0,\'total\'=>0\],\s*\'is_kegiatan\' => true\s*\];\s*\$added_keys\[\$key\] = true;\s*\}\s*\}\s*\}/s';

$replacement = '} else if ($kal_tipe == \'kegiatan\') {
                    $key_libur = $tgl_str.\'_kegiatan\';
                    if (!isset($added_keys[$key_libur])) {
                        $jurnal_global[] = [
                            \'id\' => 0,
                            \'guru_id\' => 0,
                            \'kelas_id\' => 0,
                            \'mapel_id\' => 0,
                            \'tanggal\' => $tgl_str,
                            \'jam_mulai\' => \'00:00:00\',
                            \'materi\' => \'Kegiatan: \' . $kal_info,
                            \'keterangan\' => \'\',
                            \'created_at\' => $tgl_str.\' 00:00:00\',
                            \'nama_guru\' => \'-\',
                            \'nama_kelas\' => \'-\',
                            \'nama_mapel\' => \'-\',
                            \'absen_detail\' => [\'hadir\'=>0,\'izin\'=>0,\'sakit\'=>0,\'alpa\'=>0,\'total\'=>0],
                            \'is_libur\' => true
                        ];
                        $added_keys[$key_libur] = true;
                    }
                }';

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Replaced in $file\n";
        } else {
            echo "No change in $file\n";
        }
    }
}
