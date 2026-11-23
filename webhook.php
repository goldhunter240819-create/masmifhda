<?php
echo "Menerima request webhook MAS Mifhda...\n";

// 0. Bersihkan file lock dan proses git yang menggantung di Windows Server
@shell_exec('taskkill /F /IM git.exe 2>&1');
@unlink(__DIR__ . '/.git/index.lock');
@unlink(__DIR__ . '/.git/shallow.lock');
@unlink(__DIR__ . '/.git/refs/heads/main.lock');

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

// 3.5 Bersihkan & Migrasikan Bank Soal ke Basis Tingkat
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
if (file_exists(__DIR__ . '/app/Core/AcademicYear.php')) {
    require_once __DIR__ . '/app/Core/AcademicYear.php';
}
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Controllers/BankSoalController.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    \App\Controllers\BankSoalController::ensureTables($db);
    $activeTopikIds = $db->query("SELECT id FROM bs_topik WHERE status = 'Aktif'")->fetchAll(PDO::FETCH_COLUMN);
    $bsCtrl = new \App\Controllers\BankSoalController();
    foreach ($activeTopikIds as $actId) {
        $bsCtrl->syncFromPengampu((int)$actId, true);
    }
    $output .= "\nBank Soal synced to Tingkat successfully.\n";
} catch (\Exception $e) {
    $output .= "\nBank Soal migration: " . $e->getMessage() . "\n";
}

// 3.6 Update Brand Names & Tab Titles & Kepala Madrasah di Database
try {
    $dbCore = \App\Core\Database::connect('core');
    $dbCore->exec("UPDATE app_modules SET app_name = 'MA Miftahul Huda', tab_title = 'MA Miftahul Huda' WHERE app_code = 'portal' OR app_name LIKE '%Aula%' OR tab_title LIKE '%Aula%'");
    $dbCore->exec("UPDATE institusi SET nama_kepala = 'AHMAD RIFA\'I, M.Pd' WHERE nama_kepala LIKE '%Hamim%' OR nama_kepala LIKE '%Ulfah%' OR nama_kepala = '' OR nama_kepala IS NULL");
    $output .= "\nApp modules branding & Kamad updated to MA Miftahul Huda / AHMAD RIFA'I, M.Pd successfully.\n";
} catch (\Exception $e) {
    $output .= "\nBranding update migration error: " . $e->getMessage() . "\n";
}

// 3.7 Ensure kurikulum_supervisi_administrasi columns
try {
    $dbCore = \App\Core\Database::connect('core');
    $colsToAdd = [
        'keterangan' => 'TEXT NULL',
        'pegawai_id' => 'INT NULL',
        'guru_id' => 'INT NULL',
        'supervisor_id' => 'INT NULL',
        'penilai_id' => 'INT NULL',
        'tanggal_supervisi' => 'DATE NULL',
        'penilai_nama' => 'VARCHAR(150) NULL',
        'nilai_akhir' => 'FLOAT NULL'
    ];
    foreach ($colsToAdd as $c => $t) {
        try { $dbCore->exec("ALTER TABLE `kurikulum_supervisi_administrasi` ADD COLUMN `$c` $t"); } catch (\Throwable $e) {}
    }
    $output .= "\nEnsured all columns in kurikulum_supervisi_administrasi.\n";
} catch (\Exception $e) {}

// 3.8 Sinkronisasi Perangkat Pembelajaran Guru ke SIAKAD & PKKM
try {
    $dbS = \App\Core\Database::connect('siakad');
    $dbC = \App\Core\Database::connect('core');

    // Ensure columns in perangkat_pembelajaran
    $colsPP = $dbS->query("SHOW COLUMNS FROM `perangkat_pembelajaran`")->fetchAll(PDO::FETCH_COLUMN);
    $ppColsToAdd = [
        'guru_perangkat_id' => 'INT NULL',
        'mapel_nama' => 'VARCHAR(150) NULL',
        'kelas_nama' => 'VARCHAR(100) NULL'
    ];
    foreach ($ppColsToAdd as $c => $t) {
        if (!in_array($c, $colsPP)) {
            try { $dbS->exec("ALTER TABLE `perangkat_pembelajaran` ADD COLUMN `$c` $t"); } catch (\Throwable $e) {}
        }
    }
    try { $dbS->exec("ALTER TABLE `perangkat_pembelajaran` MODIFY COLUMN `mapel_id` INT NULL DEFAULT NULL"); } catch (\Throwable $e) {}
    try { $dbS->exec("ALTER TABLE `perangkat_pembelajaran` MODIFY COLUMN `kelas_id` INT NULL DEFAULT NULL"); } catch (\Throwable $e) {}
    try { $dbS->exec("ALTER TABLE `perangkat_pembelajaran` ADD INDEX `idx_gp_id` (`guru_perangkat_id`)"); } catch (\Throwable $e) {}

    // Jalankan sync
    $hasGP = $dbC->query("SHOW TABLES LIKE 'guru_perangkat'")->fetchColumn();
    if ($hasGP) {
        require_once __DIR__ . '/app/Controllers/KurikulumController.php';
        $synced = \App\Controllers\KurikulumController::syncFromGuruPerangkat($dbS, $dbC);
        $totalPP = (int)$dbS->query("SELECT COUNT(*) FROM `perangkat_pembelajaran`")->fetchColumn();
        $output .= "\nSynced perangkat_pembelajaran from guru_perangkat: $synced processed. Total in Siakad now: $totalPP.\n";
    }
} catch (\Throwable $e) {
    $output .= "\nPerangkat migration error: " . $e->getMessage() . "\n";
}

// 3.9 Generate Otomatis 20 Soal per Mapel untuk SEMUA Mapel di Topik SIMULASI NEW CBT
try {
    if (file_exists(__DIR__ . '/app/Services/CbtAllSubjects20Generator.php')) {
        require_once __DIR__ . '/app/Services/CbtAllSubjects20Generator.php';
        $inserted20 = \App\Services\CbtAllSubjects20Generator::generateAll(10);
        $output .= "\nCBT All Subjects (20 Soal per Mapel) generated: {$inserted20} items.\n";
    }
} catch (\Throwable $e) {
    $output .= "\nCBT 20 questions generator error: " . $e->getMessage() . "\n";
}

// 3.10 Seed 34 Peserta Khusus / Kelas Jauh (Kelas X, XI, XII)
try {
    if (file_exists(__DIR__ . '/app/Services/CbtPesertaKhususSeeder.php')) {
        require_once __DIR__ . '/app/Services/CbtPesertaKhususSeeder.php';
        $resKj = \App\Services\CbtPesertaKhususSeeder::seed();
        $output .= "\nPeserta Kelas Jauh seeded: {$resKj['total']} siswa ({$resKj['inserted']} baru, {$resKj['updated']} diupdate).\n";
    }
} catch (\Throwable $e) {
    $output .= "\nPeserta Kelas Jauh seed error: " . $e->getMessage() . "\n";
}

// 3.11 Ensure Tabel CBT & Bersihkan data absensi simulasi lama yang nyangkut
try {
    $dbCore = \App\Core\Database::connect('core');
    require_once __DIR__ . '/app/Controllers/CbtController.php';
    \App\Controllers\CbtController::ensureTables($dbCore);

    // Pastikan kolom topik_id ada di cbt_jam_slot dan cbt_denah_peserta
    try { $dbCore->exec("ALTER TABLE cbt_jam_slot ADD COLUMN topik_id INT DEFAULT NULL AFTER id, ADD INDEX (topik_id)"); } catch (\Throwable $e) {}
    try { $dbCore->exec("ALTER TABLE cbt_denah_peserta ADD COLUMN topik_id INT DEFAULT NULL AFTER ruang_id, ADD INDEX (topik_id)"); } catch (\Throwable $e) {}
    try { $dbCore->exec("ALTER TABLE cbt_absensi MODIFY COLUMN siswa_id INT NULL DEFAULT NULL"); } catch (\Throwable $e) {}
    try { $dbCore->exec("ALTER TABLE cbt_peserta ADD COLUMN jumlah_tidak_jawab INT DEFAULT 0 AFTER jumlah_salah"); } catch (\Throwable $e) {}

    // Pastikan tabel cbt_ruang_pengawas sudah dibuat
    $dbCore->exec("
        CREATE TABLE IF NOT EXISTS cbt_ruang_pengawas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topik_id INT NOT NULL,
            ruang_id INT NOT NULL,
            sesi_label VARCHAR(50) NOT NULL,
            jam_slot VARCHAR(50) NULL,
            guru_id INT NULL,
            guru_nama VARCHAR(150) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_topik_ruang_sesi (topik_id, ruang_id, sesi_label),
            INDEX idx_topik (topik_id),
            INDEX idx_ruang (ruang_id),
            INDEX idx_guru (guru_nama)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Sinkronkan durasi default 60 menit
    try {
        $dbCore->exec("UPDATE cbt_pengaturan SET default_durasi_menit = 60 WHERE default_durasi_menit = 90 OR default_durasi_menit IS NULL");
        $dbCore->exec("UPDATE cbt_ujian SET durasi_menit = 60 WHERE durasi_menit = 90 OR durasi_menit IS NULL OR durasi_menit = 0");
    } catch (\Throwable $e) {}

    // Isi otomatis absensi siswa hari ini untuk seluruh mapel sesuai jadwal dan rentang jam sesi (Anti-Blokir Siswa Ujian)
    try {
        if (!class_exists('\App\Controllers\CbtController')) {
            require_once __DIR__ . '/app/Controllers/CbtController.php';
        }
        $resBulk = \App\Controllers\CbtController::bulkPopulateAbsensiHariIni();
        $output .= "\nAuto-populated today's attendance: " . ($resBulk['inserted'] ?? 0) . " entries created (" . ($resBulk['message'] ?? '') . ").\n";
        if (date('Y-m-d') !== '2026-11-23') {
            $resBulk2 = \App\Controllers\CbtController::bulkPopulateAbsensiHariIni('2026-11-23');
            $output .= "Populated 2026-11-23: " . ($resBulk2['inserted'] ?? 0) . " entries.\n";
        }
    } catch (\Throwable $e) {
        $output .= "\nAuto-populate attendance error: " . $e->getMessage() . "\n";
    }

    // Seragamkan seluruh nama ujian di cbt_ujian agar selalu konsisten dengan format [Nama Topik] - [Mapel] ([Kelas])
    $dbCore->exec("
        UPDATE cbt_ujian u
        INNER JOIN bs_topik t ON u.topik_id = t.id
        SET u.nama_ujian = CONCAT(t.nama_topik, ' - ', u.mapel_nama, ' (', u.kelas_nama, ')')
        WHERE (u.nama_ujian LIKE 'Simulasi %' OR u.nama_ujian NOT LIKE CONCAT(t.nama_topik, '%'))
          AND u.topik_id IS NOT NULL AND u.topik_id > 0
    ");

    // Pastikan siswa reguler seperti Faiz (id 414) memiliki kursi di Ruang 01 jika belum diatur
    try {
        $cekFaiz = (int)$dbCore->query("SELECT COUNT(*) FROM cbt_denah_peserta WHERE siswa_id = 414")->fetchColumn();
        if ($cekFaiz === 0) {
            $slotFaiz = (int)$dbCore->query("SELECT id FROM cbt_denah_peserta WHERE ruang_id = 1 AND (siswa_id IS NULL OR siswa_id = 0) ORDER BY nomor_meja ASC LIMIT 1")->fetchColumn();
            if ($slotFaiz > 0) {
                $dbCore->exec("
                    UPDATE cbt_denah_peserta 
                    SET siswa_id = 414, nis = '414', nama_siswa = 'M. Faiz Al Majid', kelas_id = 3, kelas_nama = 'XII F.2', tingkat = 12
                    WHERE id = {$slotFaiz}
                ");
            }
        }
    } catch (\Throwable $e) {}

    // Sinkronkan jadwal topik aktif
    $tpRow = $dbCore->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($tpRow) {
        $dbSiakad = \App\Core\Database::connect('siakad');
        \App\Controllers\CbtController::syncJadwalWithUjian($dbCore, $dbSiakad, (int)$tpRow['id']);
    }
    
    // Bersihkan duplikasi topik SAS ID 5 (satukan ke ID 3)
    try {
        $dbCore->exec("UPDATE cbt_ujian SET topik_id = 3 WHERE topik_id = 5");
        $dbCore->exec("UPDATE bs_paket SET topik_id = 3 WHERE topik_id = 5");
        $dbCore->exec("UPDATE bs_topik SET status = 'Arsip' WHERE id = 5");
        $dbSiakad = \App\Core\Database::connect('siakad');
        if ($dbSiakad) {
            $dbSiakad->exec("UPDATE bs_topik SET status = 'Arsip' WHERE id = 5");
            $dbSiakad->exec("UPDATE bs_paket SET topik_id = 3 WHERE topik_id = 5");
        }
        $output .= "\nDuplicate topik ID 5 consolidated to ID 3 and archived successfully.\n";
    } catch (\Throwable $e) {}

    $output .= "\nCBT Tables ensured, simulation attendance cleaned, and exam names synchronized.\n";
} catch (\Throwable $e) {
    $output .= "\nCBT Tables ensure error: " . $e->getMessage() . "\n";
}

// 3.12 Build Standalone CBT Package Terbaru untuk Distribusi Server Madrasah
try {
    if (file_exists(__DIR__ . '/tools/build_cbt_standalone.php')) {
        require_once __DIR__ . '/tools/build_cbt_standalone.php';
        if (function_exists('build_cbt_standalone_package')) {
            $bRes = build_cbt_standalone_package();
            if ($bRes) {
                $output .= "\nStandalone CBT Package built successfully: " . round($bRes['size'] / 1024, 2) . " KB (" . $bRes['files'] . " files).\n";
            }
        }
    }
} catch (\Throwable $e) {
    $output .= "\nStandalone CBT Package build error: " . $e->getMessage() . "\n";
}

// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
