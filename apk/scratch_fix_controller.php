<?php
$file = 'c:\xampp\htdocs\masmifhda\app\Controllers\AbsenV2Controller.php';
$content = file_get_contents($file);

$search = "                        // Guru belum mengisi jurnal
    public static function autoGenerateAlpaSiswa";

$replace = <<<EOD
                        // Guru belum mengisi jurnal
                        \$timeline['mapels'][] = '-';
                    } else {
                        // Fallback: Jika jurnal mapel diisi oleh guru, namun absensi_permapel siswa ini belum/tidak tercatat
                        \$stmtPusatCek = \$db_siakad->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                        \$stmtPusatCek->execute([\$s['id'], \$tanggal]);
                        \$pusatCek = \$stmtPusatCek->fetch();
                        
                        if (\$pusatCek && !empty(\$pusatCek['status'])) {
                            \$timeline['mapels'][] = \$pusatCek['status'];
                        } else {
                            \$timeline['mapels'][] = 'Hadir';
                        }
                    }
                }
            }
            
            // 3. Sholat
            \$stmtSholat = \$db_core->prepare("SELECT status, jam_scan FROM absensi_sholat WHERE siswa_id = ? AND tanggal = ?");
            \$stmtSholat->execute([\$s['id'], \$tanggal]);
            \$sholat = \$stmtSholat->fetch();
            \$timeline['sholat'] = \$sholat ? 'Hadir ('.\$sholat['jam_scan'].')' : '-';
            
            // 4. Gerbang Pulang
            \$timeline['gate_pulang'] = \$presensi && \$presensi['jam_pulang'] ? \$presensi['jam_pulang'] : '-';
            
            // 5. Status Akhir Pusat
            \$stmtPusat = \$db_siakad->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
            \$stmtPusat->execute([\$s['id'], \$tanggal]);
            \$pusat = \$stmtPusat->fetch();
            \$status_akhir = \$pusat ? \$pusat['status'] : '-';

            // Override logika: Jika tidak Sakit/Izin, cek kelengkapan
            if (!in_array(\$status_akhir, ['Sakit', 'Izin', 'S', 'I'])) {
                \$isBolos = false;
                
                // Jika tidak scan masuk atau pulang -> Bolos
                if (\$timeline['gate_masuk'] == '-') \$isBolos = true;
                if (\$timeline['gate_pulang'] == '-') \$isBolos = true;
                
                // Jika ada mapel yang bolong/alpa -> Bolos
                foreach (\$timeline['mapels'] as \$m) {
                    if (\$m == '-' || in_array(\$m, ['Alpa', 'Bolos', 'A', 'B'])) {
                        \$isBolos = true;
                    }
                }
                
                if (\$isBolos) {
                    \$status_akhir = 'Bolos';
                }
            }

            \$timeline['status_akhir'] = \$status_akhir;

            \$s['timeline'] = \$timeline;
            \$rekap[] = \$s;
        }

        \$title = "Rekap Detail Absensi";
        \$initial = substr(\$_SESSION['nama'], 0, 1);
        \$nama = \$_SESSION['nama'];

        if (isset(\$_GET['cetak']) && \$_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_v2/cetak_rekap_detail.php';
            exit;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/rekap_detail.php';
        \$content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function autoGenerateAlpaSiswa
EOD;

// Fix exact line endings for search string to match
$search = "                        // Guru belum mengisi jurnal\r\n    public static function autoGenerateAlpaSiswa";
if (strpos($content, $search) === false) {
    $search = "                        // Guru belum mengisi jurnal\n    public static function autoGenerateAlpaSiswa";
}
if (strpos($content, $search) === false) {
    echo "SEARCH STRING NOT FOUND\n";
    $pos = strpos($content, "autoGenerateAlpaSiswa");
    echo "autoGenerateAlpaSiswa is at $pos\n";
} else {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "FIXED SUCCESSFULLY!\n";
}
