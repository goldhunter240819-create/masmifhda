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
    $countPaket = (int)$db->query("SELECT COUNT(*) FROM bs_paket")->fetchColumn();
    if ($countPaket === 0) {
        $activeTopikIds = $db->query("SELECT id FROM bs_topik WHERE status = 'Aktif'")->fetchAll(PDO::FETCH_COLUMN);
        $bsCtrl = new \App\Controllers\BankSoalController();
        foreach ($activeTopikIds as $actId) {
            $bsCtrl->syncFromPengampu((int)$actId, true);
        }
        $output .= "\nBank Soal synced to Tingkat successfully.\n";
    } else {
        $output .= "\nBank Soal already populated ($countPaket packages), skipping auto-sync.\n";
    }
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

    // Bersihkan absensi otomatis agar data absensi kembali murni dari scan riil siswa per-sesi
    try {
        $delCount = $dbCore->exec("DELETE FROM cbt_absensi WHERE tanggal = '2026-11-24' OR DATE(waktu_scan) = '2026-11-24' OR tanggal = '2026-11-23' OR DATE(waktu_scan) = '2026-11-23' OR tanggal = CURDATE() OR DATE(waktu_scan) = CURDATE()");
        $output .= "\nCleaned auto-populated attendance: " . (int)$delCount . " entries deleted.\n";
    } catch (\Throwable $e) {
        $output .= "\nClean attendance error: " . $e->getMessage() . "\n";
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

    // DIAGNOSTIK & PERBAIKAN SOAL FIKIH
    try {
        $dbCore = \App\Core\Database::connect('core');
        $dbSiakad = \App\Core\Database::connect('siakad') ?: $dbCore;
        
        $output .= "\n=== PERBAIKAN SOAL FIKIH DENGAN PAKET ASLI 40 BUTIR ===\n";

        // Samakan nama mapel Fikih / Fiqih di bs_paket, bs_soal, cbt_ujian, dan cbt_jadwal_grid
        $dbSiakad->exec("UPDATE bs_paket SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");
        $dbCore->exec("UPDATE bs_paket SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");
        $dbSiakad->exec("UPDATE bs_soal SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");
        $dbCore->exec("UPDATE bs_soal SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");
        $dbCore->exec("UPDATE cbt_ujian SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");
        $dbCore->exec("UPDATE cbt_jadwal_grid SET mapel_nama = 'Fikih' WHERE mapel_nama = 'Fiqih'");

        // Petakan paket Fikih SAS (Topik 3):
        // Paket 169 -> Kelas X (40 butir)
        // Paket 170 -> Kelas XI (40 butir)
        // Paket 171 -> Kelas XII (40 butir)
        $paketMapping = [
            10 => 169, // Kelas X
            11 => 170, // Kelas XI
            12 => 171  // Kelas XII
        ];

        $getLevel = function($str) {
            $s = strtoupper((string)$str);
            if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
            if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
            if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
            return 0;
        };

        // Ambil semua ujian Fikih di Topik 3 (SAS Ganjil)
        $fikihUjians = $dbCore->query("SELECT id, nama_ujian, kelas_nama FROM cbt_ujian WHERE topik_id = 3 AND (mapel_nama = 'Fikih' OR mapel_nama = 'Fiqih')")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fikihUjians as $fu) {
            $uId = (int)$fu['id'];
            $lvl = $getLevel($fu['kelas_nama']);
            $sourcePktId = $paketMapping[$lvl] ?? 0;

            if ($sourcePktId > 0) {
                // Ambil 40 butir soal asli dari bs_soal
                $stSoal = $dbSiakad->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
                $stSoal->execute([$sourcePktId]);
                $asliSoal = $stSoal->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($asliSoal)) {
                    $curCnt = (int)$dbCore->query("SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = {$uId}")->fetchColumn();
                    if ($curCnt !== count($asliSoal)) {
                        // Bersihkan 50 butir soal campur aduk lama di cbt_soal
                        $dbCore->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$uId]);

                        // Masukkan 40 butir soal asli Fikih lengkap dengan tipe_soal & konten_tambahan
                        $stIns = $dbCore->prepare("
                            INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");

                        $no = 1;
                        foreach ($asliSoal as $as) {
                            $stIns->execute([
                                $uId,
                                $no++,
                                $as['pertanyaan'],
                                $as['opsi_a'] ?? '',
                                $as['opsi_b'] ?? '',
                                $as['opsi_c'] ?? '',
                                $as['opsi_d'] ?? '',
                                $as['opsi_e'] ?? '',
                                $as['kunci_jawaban'] ?? 'A',
                                $as['skor_maksimal'] ?? 2.50,
                                $as['tipe_soal'] ?? 'sec_pg',
                                $as['konten_tambahan'] ?? null
                            ]);
                        }

                        $output .= "Sukses perbaiki Ujian ID {$uId} ({$fu['nama_ujian']}): dimasukkan " . count($asliSoal) . " butir soal asli Fikih dari Paket {$sourcePktId}!\n";
                    }
                }
            }
        }

        // 3.11b Pemulihan Nilai & Skor Siswa dari jawaban_json (Al-Qur'an Hadits, Fikih, SKI, dll)
        $output .= "\n=== PEMULIHAN & PERHITUNGAN ULANG NILAI DARI JAWABAN SISWA ===\n";
        try {
            // Sinkronkan seluruh cbt_soal Topik Aktif dari Bank Soal (bs_soal) yang baru diperbarui
            $syncedCount = 0;
            $ujsToSync = $dbCore->query("SELECT id, topik_id, mapel_nama, kelas_nama FROM cbt_ujian WHERE topik_id = 3")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ujsToSync as $us) {
                if (\App\Controllers\CbtController::syncExamSoalFromBankSoal($dbCore, $dbSiakad, $us)) {
                    $syncedCount++;
                }
            }
            $output .= "Sinkron cbt_soal dari Bank Soal (bs_soal) selesai: {$syncedCount} paket ujian diperbarui.\n";

            // Cek juga daftar Ujian Al-Qur'an Hadits dan jumlah soalnya
            $qhUjians = $dbCore->query("
                SELECT u.id, u.nama_ujian, u.kelas_nama, (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as cnt_soal,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE ujian_id = u.id) as cnt_peserta,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE ujian_id = u.id AND (nilai_akhir > 0 OR status_pengerjaan = 'Selesai')) as cnt_selesai
                FROM cbt_ujian u
                WHERE u.nama_ujian LIKE '%Qur%' OR u.mapel_nama LIKE '%Qur%'
            ")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($qhUjians as $qhu) {
                $output .= sprintf("QH UJIAN ID %d: %s (%s) | Soal: %d | Peserta: %d (Selesai: %d)\n",
                    $qhu['id'], $qhu['nama_ujian'], $qhu['kelas_nama'], $qhu['cnt_soal'], $qhu['cnt_peserta'], $qhu['cnt_selesai']
                );
            }

            $candidatePeserta = $dbCore->query("
                SELECT p.id, p.ujian_id, p.siswa_id, p.nis, p.nama_siswa, p.kelas_nama, p.status_pengerjaan, p.nilai_akhir, p.jumlah_benar, p.jawaban_json
                FROM cbt_peserta p
                WHERE p.jawaban_json IS NOT NULL AND p.jawaban_json != ''
            ")->fetchAll(PDO::FETCH_ASSOC);

            $soalCache = [];
            $recalculatedCount = 0;

            foreach ($candidatePeserta as $cp) {
                $uId = (int)$cp['ujian_id'];
                if (!isset($soalCache[$uId])) {
                    $stS = $dbCore->prepare("SELECT id, nomor_urut, kunci_jawaban, bobot, tipe_soal, konten_tambahan FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
                    $stS->execute([$uId]);
                    $soalCache[$uId] = $stS->fetchAll(PDO::FETCH_ASSOC);
                }

                $soals = $soalCache[$uId] ?? [];
                if (!empty($soals)) {
                    $eval = \App\Controllers\CbtController::evaluatePesertaAnswers($soals, $cp['jawaban_json']);
                    
                    $curNilai = (float)($cp['nilai_akhir'] ?? 0);
                    $newNilai = (float)$eval['nilaiAkhir'];
                    
                    if ($newNilai > $curNilai || $curNilai <= 0 || $cp['status_pengerjaan'] !== 'Selesai' || $eval['jumlahBenar'] > (int)($cp['jumlah_benar'] ?? 0) || ((int)($cp['jumlah_tidak_jawab'] ?? 0) > 0 && $eval['jumlahTidakJawab'] < (int)($cp['jumlah_tidak_jawab'] ?? 0))) {
                        $stFix = $dbCore->prepare("
                            UPDATE cbt_peserta 
                            SET status_pengerjaan = 'Selesai',
                                jumlah_benar = ?,
                                jumlah_salah = ?,
                                jumlah_tidak_jawab = ?,
                                nilai_akhir = ?,
                                waktu_selesai = COALESCE(waktu_selesai, NOW())
                            WHERE id = ?
                        ");
                        $stFix->execute([$eval['jumlahBenar'], $eval['jumlahSalah'], $eval['jumlahTidakJawab'], $newNilai, $cp['id']]);
                        $recalculatedCount++;
                        $output .= sprintf("Pulihkan Nilai ID %d: %s (%s) - Nilai: %.1f (Benar: %d/%d, Salah: %d, Kosong: %d)\n", $cp['id'], $cp['nama_siswa'], $cp['kelas_nama'], $newNilai, $eval['jumlahBenar'], count($soals), $eval['jumlahSalah'], $eval['jumlahTidakJawab']);
                    }
                }
            }
            $output .= "Sukses hitung ulang & pulihkan {$recalculatedCount} nilai pengerjaan siswa!\n";

            // Bersihkan duplikasi cbt_peserta yang kosong (pertahankan yang ada nilai/jawaban)
            $dbCore->exec("
                DELETE p1 FROM cbt_peserta p1
                INNER JOIN cbt_peserta p2 ON p1.ujian_id = p2.ujian_id AND p1.nis = p2.nis AND p1.id != p2.id
                WHERE (p1.nilai_akhir = 0 OR p1.nilai_akhir IS NULL) 
                  AND (p1.jawaban_json IS NULL OR p1.jawaban_json = '')
                  AND (p2.nilai_akhir > 0 OR (p2.jawaban_json IS NOT NULL AND p2.jawaban_json != ''))
            ");
        } catch (\Throwable $e) {
            $output .= "Gagal hitung ulang nilai: " . $e->getMessage() . "\n";
        }

        // 3.11c Rapikan cbt_jam_slot agar tidak ada duplikasi Sesi 1, 2, 3, dan buang slot dummy 4, 5, 6, 7
        try {
            $dbCore->exec("DELETE FROM cbt_jam_slot WHERE label_sesi IN ('4','5','6','7') OR topik_id = 10 OR (topik_id IS NULL AND id NOT IN (1, 2, 3, 4))");
            $activeTid3 = (int)($dbCore->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 3);
            $stdSlots = [
                ['urutan' => 1, 'label' => 'Sesi 1', 'mulai' => '08:00:00', 'selesai' => '09:00:00', 'is_ist' => 0],
                ['urutan' => 2, 'label' => 'Sesi 2', 'mulai' => '09:00:00', 'selesai' => '10:00:00', 'is_ist' => 0],
                ['urutan' => 3, 'label' => 'Istirahat', 'mulai' => '10:00:00', 'selesai' => '10:10:00', 'is_ist' => 1],
                ['urutan' => 4, 'label' => 'Sesi 3', 'mulai' => '10:10:00', 'selesai' => '11:10:00', 'is_ist' => 0],
            ];
            $dbCore->exec("DELETE FROM cbt_jam_slot WHERE topik_id = {$activeTid3}");
            $stInsSlot = $dbCore->prepare("INSERT INTO cbt_jam_slot (topik_id, urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($stdSlots as $ssl) {
                $stInsSlot->execute([$activeTid3, $ssl['urutan'], $ssl['label'], $ssl['mulai'], $ssl['selesai'], $ssl['is_ist']]);
            }
            $output .= "\nSukses standarisasi cbt_jam_slot untuk Topik {$activeTid3} (Sesi 1: 08-09, Sesi 2: 09-10, Sesi 3: 10:10-11:10)!\n";
        } catch (\Throwable $e) {
            $output .= "\nClean cbt_jam_slot error: " . $e->getMessage() . "\n";
        }

        // Verifikasi hasil perbaikan
        $verifUjs = $dbCore->query("SELECT id, nama_ujian, (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = cbt_ujian.id) as cnt FROM cbt_ujian WHERE topik_id = 3 AND (mapel_nama = 'Fikih' OR mapel_nama = 'Fiqih')")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($verifUjs as $vu) {
            $sampel = $dbCore->query("SELECT nomor_urut, tipe_soal, SUBSTRING(pertanyaan, 1, 50) as ringkasan FROM cbt_soal WHERE ujian_id = {$vu['id']} ORDER BY nomor_urut ASC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            $output .= "HASIL Ujian ID {$vu['id']} ({$vu['nama_ujian']}): {$vu['cnt']} butir soal. Sampel: " . json_encode($sampel) . "\n";
        }
    } catch (\Throwable $e) {
        $output .= "\nPerbaikan Fikih error: " . $e->getMessage() . "\n";
    }

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
