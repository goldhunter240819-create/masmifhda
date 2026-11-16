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

// Check live mengajar
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    $mRows = $db->query("
        SELECT m.id, m.kelas_id, k.nama_kelas, m.mapel_id, mp.nama_mapel, m.guru_id, g.nama as guru_nama, m.tahun_ajaran_id, m.semester
        FROM mengajar m
        LEFT JOIN kelas k ON m.kelas_id = k.id
        LEFT JOIN mapel mp ON m.mapel_id = mp.id
        LEFT JOIN aula_db.guru g ON m.guru_id = g.id
        WHERE m.guru_id IS NOT NULL AND m.guru_id > 0
        ORDER BY k.nama_kelas ASC, mp.nama_mapel ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $output .= "\nLIVE MENGAJAR ASSIGNMENTS Count: " . count($mRows) . "\n";
    foreach ($mRows as $m) {
        $output .= "Kelas: {$m['nama_kelas']} (ID {$m['kelas_id']}) | Mapel: {$m['nama_mapel']} (ID {$m['mapel_id']}) | Guru: {$m['guru_nama']} (ID {$m['guru_id']}) | TA: {$m['tahun_ajaran_id']} | Sem: {$m['semester']}\n";
    }
} catch (\Exception $e) {
    $output .= "\nError querying mengajar: " . $e->getMessage() . "\n";
}


// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
