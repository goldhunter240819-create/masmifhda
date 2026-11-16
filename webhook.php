<?php
echo "Menerima request webhook MAS Mifhda...\n";

// 1. Bersihkan perubahan lokal tak sengaja di server agar pull tidak abort/konflik
shell_exec('git -c safe.directory="*" checkout -- . 2>&1');
shell_exec('git -c safe.directory="*" clean -fd 2>&1');

// 2. Tarik kode terbaru dari GitHub origin/main dan paksa sync ke HEAD origin/main
shell_exec('git -c safe.directory="*" fetch origin main 2>&1');
$output = shell_exec('git -c safe.directory="*" reset --hard origin/main 2>&1');
$headLog = shell_exec('git -c safe.directory="*" log -1 --oneline 2>&1');
$output .= "\nHEAD: " . trim($headLog);

// 3. Reset OPcache agar PHP langsung menyajikan kodingan terbaru tanpa restart Apache
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// Check live active mengajar
require_once __DIR__ . '/app/Core/Database.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    
    // Check difference between active mengajar and bs_paket for topik 3
    $sasPaket = $db->query("SELECT mapel_nama, kelas_nama, guru_nama FROM bs_paket WHERE topik_id = 3")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "\nSAS PAKET (ID 3) count: " . count($sasPaket) . "\n";
    
    // Check which active mengajar is not in sasPaket
    $mRows = $db->query("
        SELECT DISTINCT mp.nama_mapel, k.nama_kelas, COALESCE(g.nama, g_siakad.nama_lengkap) as guru_nama
        FROM mengajar m
        JOIN kelas k ON m.kelas_id = k.id
        JOIN mapel mp ON m.mapel_id = mp.id
        LEFT JOIN aula_db.guru g ON m.guru_id = g.id
        LEFT JOIN guru g_siakad ON m.guru_id = g_siakad.id
        WHERE m.guru_id IS NOT NULL AND m.guru_id > 0
          AND (m.tahun_ajaran_id = 2 OR m.tahun_ajaran_id IS NULL)
          AND (m.semester = 1 OR m.semester IS NULL OR m.semester = 0)
          AND mp.nama_mapel NOT IN ('Upacara', 'Senam', 'Pramuka', 'Sholat Dhuha', 'Juz Amma - Asmaul Husna', 'BTQ', 'IPAS', 'SBdP', 'Baca Tulis Qur\'an', 'Ilmu Pengetahuan Alam Sosial', 'Seni Budaya dan Prakarya')
        ORDER BY mp.nama_mapel ASC, k.nama_kelas ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "ACTIVE MENGAJAR DISTINCT (TA 2, Sem 1): " . count($mRows) . "\n";
    
    // Clean up orphan bs_paket whose topik_id is not in bs_topik
    $deletedOrphans = $db->exec("DELETE FROM bs_paket WHERE topik_id NOT IN (SELECT id FROM bs_topik) OR topik_id IS NULL OR topik_id = 0");
    $output .= "CLEANED ORPHAN BS_PAKET: " . $deletedOrphans . " rows deleted\n";
    
    // Find which rows in mRows are not in sasPaket
    $diff = [];
    foreach ($mRows as $mr) {
        $found = false;
        foreach ($sasPaket as $sp) {
            if ($sp['mapel_nama'] === $mr['nama_mapel'] && $sp['kelas_nama'] === $mr['nama_kelas']) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $diff[] = $mr;
        }
    }
    $output .= "DIFF ROWS (" . count($diff) . "): " . json_encode($diff) . "\n";


} catch (\Exception $e) {
    $output .= "\nError querying bs_paket: " . $e->getMessage() . "\n";
}



// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
