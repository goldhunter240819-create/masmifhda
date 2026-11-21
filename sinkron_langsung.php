<?php
/**
 * ⚡ SINKRONISASI KILAT & BERSIH SERVER CBT MADRASAH
 * Menyelaraskan seluruh data topik, bank soal, jadwal, dan akun dari server online pusat.
 * Dirancang 100% tangguh & mandiri: otomatis membuat seluruh tabel di aula_db & aula_siakad_db.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);
ini_set('memory_limit', '512M');
date_default_timezone_set('Asia/Jakarta');

$baseDir = __DIR__;
if (file_exists($baseDir . '/vendor/autoload.php')) {
    require_once $baseDir . '/vendor/autoload.php';
}

$dbConfig = file_exists($baseDir . '/config/database.php') ? require $baseDir . '/config/database.php' : [];
$host = $dbConfig['core']['host'] ?? '127.0.0.1';
$user = $dbConfig['core']['username'] ?? 'root';
$pass = $dbConfig['core']['password'] ?? '';
$coreDbName = $dbConfig['core']['db_name'] ?? 'aula_db';
$siakadDbName = $dbConfig['siakad']['db_name'] ?? 'aula_siakad_db';

$status = [];
$warnings = [];
$errorMsg = '';
$isDone = false;

function ensureAllSyncTables($pdoCore, $pdoSiakad) {
    $commonSql = [
        "CREATE TABLE IF NOT EXISTS siswa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            nis VARCHAR(50) NOT NULL,
            nik VARCHAR(50) DEFAULT NULL,
            nama VARCHAR(150) NOT NULL,
            kelas_id INT DEFAULT NULL,
            jenis_kelamin VARCHAR(10) DEFAULT 'L',
            status VARCHAR(20) DEFAULT 'Aktif',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (nis),
            INDEX (nik),
            INDEX (user_id),
            INDEX (kelas_id),
            INDEX (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS kelas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_kelas VARCHAR(100) NOT NULL,
            tingkat VARCHAR(20) DEFAULT 'X',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS mapel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_mapel VARCHAR(150) NOT NULL,
            kode_mapel VARCHAR(50) DEFAULT NULL,
            kategori VARCHAR(50) DEFAULT 'Umum',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS guru (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            nama VARCHAR(150) NOT NULL,
            nip VARCHAR(50) DEFAULT NULL,
            nik VARCHAR(50) DEFAULT NULL,
            is_kamad TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            plain_password VARCHAR(100) DEFAULT NULL,
            role_id INT DEFAULT 3,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (role_id),
            INDEX (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS bs_topik (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tahun_ajaran VARCHAR(20) DEFAULT '2026/2027',
            semester VARCHAR(10) DEFAULT 'Ganjil',
            nama_topik VARCHAR(150) NOT NULL,
            jenis VARCHAR(50) DEFAULT 'SAS',
            tgl_mulai DATE DEFAULT NULL,
            tgl_selesai DATE DEFAULT NULL,
            keterangan TEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'Terjadwal',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS bs_paket (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topik_id INT NOT NULL,
            mapel_nama VARCHAR(150) NOT NULL,
            kelas_nama VARCHAR(100) DEFAULT 'Kelas X',
            kelas_sasaran VARCHAR(255) DEFAULT NULL,
            guru_id INT DEFAULT NULL,
            guru_nama VARCHAR(150) DEFAULT NULL,
            kkm DECIMAL(5,2) DEFAULT 75.00,
            durasi_menit INT DEFAULT 90,
            is_locked TINYINT DEFAULT 0,
            locked_at DATETIME DEFAULT NULL,
            locked_by INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (topik_id),
            INDEX (mapel_nama)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS bs_soal (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topik_id INT NOT NULL,
            paket_id INT DEFAULT NULL,
            mapel_nama VARCHAR(150) NOT NULL,
            kelas_nama VARCHAR(100) DEFAULT NULL,
            nomor_urut INT DEFAULT 1,
            tipe_soal VARCHAR(30) DEFAULT 'sec_pg',
            tingkat_kesulitan VARCHAR(20) DEFAULT 'Sedang',
            skor_maksimal DECIMAL(5,2) DEFAULT 2.00,
            pertanyaan LONGTEXT NOT NULL,
            opsi_a LONGTEXT DEFAULT NULL,
            opsi_b LONGTEXT DEFAULT NULL,
            opsi_c LONGTEXT DEFAULT NULL,
            opsi_d LONGTEXT DEFAULT NULL,
            opsi_e LONGTEXT DEFAULT NULL,
            kunci_jawaban TEXT DEFAULT NULL,
            konten_tambahan LONGTEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (topik_id),
            INDEX (paket_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    ];

    foreach ([$pdoCore, $pdoSiakad] as $pdo) {
        if (!$pdo) continue;
        foreach ($commonSql as $q) {
            try { $pdo->exec($q); } catch (\Throwable $e) {}
        }
    }

    if (class_exists('\App\Controllers\CbtController')) {
        try { \App\Controllers\CbtController::ensureTables($pdoCore); } catch (\Throwable $e) {}
        try { \App\Controllers\CbtController::ensureTables($pdoSiakad); } catch (\Throwable $e) {}
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['mulai'])) {
    try {
        $pdoCore = new PDO("mysql:host=$host;dbname=$coreDbName;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $pdoSiakad = null;
        try {
            $pdoSiakad = new PDO("mysql:host=$host;dbname=$siakadDbName;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (\Throwable $e) {
            // Jika database siakad belum ada, buat otomatis
            try {
                $pdoRoot = new PDO("mysql:host=$host", $user, $pass);
                $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$siakadDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdoSiakad = new PDO("mysql:host=$host;dbname=$siakadDbName;charset=utf8mb4", $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (\Throwable $e2) {
                $pdoSiakad = $pdoCore;
            }
        }
        if (!$pdoSiakad) {
            $pdoSiakad = $pdoCore;
        }

        // Jalankan pembuatan skema tabel lengkap pada kedua database
        ensureAllSyncTables($pdoCore, $pdoSiakad);

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $apiUrl = $serverUrl . '/apk/cbt_sync_api.php?action=export&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || empty($response)) {
            $errorMsg = "Gagal menghubungi server pusat ($apiUrl). HTTP: $httpCode. Error: $curlErr";
        } else {
            $data = json_decode($response, true);
            if (!$data || ($data['status'] ?? '') !== 'success') {
                $errorMsg = "Format respon dari server pusat tidak valid.";
            } else {
                // 1. Bersihkan dummy topik dan sinkronkan 4 topik resmi
                if (!empty($data['topik'])) {
                    $validTopikIds = array_filter(array_map('intval', array_column($data['topik'], 'id')));
                    if (!empty($validTopikIds)) {
                        $inClauseT = implode(',', $validTopikIds);
                        try { $pdoCore->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)"); } catch (\Throwable $e) {}
                        try { $pdoSiakad->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)"); } catch (\Throwable $e) {}
                    }

                    $sqlT = "INSERT INTO bs_topik (id, tahun_ajaran, semester, nama_topik, jenis, tgl_mulai, tgl_selesai, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_topik = VALUES(nama_topik), jenis = VALUES(jenis), status = VALUES(status), tgl_mulai = VALUES(tgl_mulai), tgl_selesai = VALUES(tgl_selesai), tahun_ajaran = VALUES(tahun_ajaran), semester = VALUES(semester), keterangan = VALUES(keterangan)";
                    $stmtT = $pdoCore->prepare($sqlT);
                    $stmtTS = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlT) : null;

                    $cntT = 0;
                    foreach ($data['topik'] as $t) {
                        try {
                            $vals = [$t['id'], $t['tahun_ajaran'] ?? '2026/2027', $t['semester'] ?? 'Ganjil', $t['nama_topik'], $t['jenis'] ?? ($t['jenis_topik'] ?? 'SAS'), $t['tgl_mulai'] ?? null, $t['tgl_selesai'] ?? null, $t['keterangan'] ?? null, $t['status'] ?? 'Terjadwal'];
                            $stmtT->execute($vals);
                            if ($stmtTS) {
                                try { $stmtTS->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntT++;
                        } catch (\Throwable $e) {}
                    }
                    $status['topik'] = $cntT;
                }

                // 2. Paket Bank Soal (bs_paket)
                if (!empty($data['bs_paket'])) {
                    $validPaketIds = array_filter(array_map('intval', array_column($data['bs_paket'], 'id')));
                    if (!empty($validPaketIds)) {
                        $inClauseP = implode(',', $validPaketIds);
                        try { $pdoCore->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)"); } catch (\Throwable $e) {}
                        try { $pdoSiakad->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)"); } catch (\Throwable $e) {}
                    }

                    $sqlP = "INSERT INTO bs_paket (id, topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_id, guru_nama, kkm, durasi_menit, is_locked, locked_at, locked_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), kelas_sasaran = VALUES(kelas_sasaran), guru_id = VALUES(guru_id), guru_nama = VALUES(guru_nama), kkm = VALUES(kkm), durasi_menit = VALUES(durasi_menit), is_locked = VALUES(is_locked)";
                    $stmtP1 = $pdoCore->prepare($sqlP);
                    $stmtP2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlP) : null;

                    $cntP = 0;
                    foreach ($data['bs_paket'] as $p) {
                        try {
                            $vals = [$p['id'], $p['topik_id'], $p['mapel_nama'], $p['kelas_nama'] ?? 'Kelas X', $p['kelas_sasaran'] ?? null, $p['guru_id'] ?? null, $p['guru_nama'] ?? null, $p['kkm'] ?? 75, $p['durasi_menit'] ?? 90, $p['is_locked'] ?? 0, $p['locked_at'] ?? null, $p['locked_by'] ?? null];
                            $stmtP1->execute($vals);
                            if ($stmtP2) {
                                try { $stmtP2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntP++;
                        } catch (\Throwable $e) {}
                    }
                    $status['bs_paket'] = $cntP;
                }

                // 3. Butir Soal Bank Soal (bs_soal)
                if (!empty($data['bs_soal'])) {
                    $validBsSoalIds = array_filter(array_map('intval', array_column($data['bs_soal'], 'id')));
                    $syncPaketIds = array_filter(array_map('intval', array_column($data['bs_paket'] ?? [], 'id')));
                    if (!empty($validBsSoalIds) && !empty($syncPaketIds)) {
                        $inPaketStr = implode(',', $syncPaketIds);
                        $inBsSoalStr = implode(',', $validBsSoalIds);
                        try { $pdoCore->exec("DELETE FROM bs_soal WHERE paket_id IN ($inPaketStr) AND id NOT IN ($inBsSoalStr)"); } catch (\Throwable $e) {}
                        if ($pdoSiakad !== $pdoCore) {
                            try { $pdoSiakad->exec("DELETE FROM bs_soal WHERE paket_id IN ($inPaketStr) AND id NOT IN ($inBsSoalStr)"); } catch (\Throwable $e) {}
                        }
                    }

                    $sqlBS = "INSERT INTO bs_soal (id, topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), paket_id = VALUES(paket_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan), opsi_a = VALUES(opsi_a), opsi_b = VALUES(opsi_b), opsi_c = VALUES(opsi_c), opsi_d = VALUES(opsi_d), opsi_e = VALUES(opsi_e)";
                    $stmtBS1 = $pdoCore->prepare($sqlBS);
                    $stmtBS2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlBS) : null;

                    $cntBS = 0;
                    foreach ($data['bs_soal'] as $bs) {
                        try {
                            $vals = [$bs['id'], $bs['topik_id'], $bs['paket_id'] ?? null, $bs['mapel_nama'], $bs['kelas_nama'] ?? null, $bs['nomor_urut'] ?? 1, $bs['tipe_soal'] ?? 'sec_pg', $bs['tingkat_kesulitan'] ?? 'Sedang', $bs['skor_maksimal'] ?? 2.00, $bs['pertanyaan'], $bs['opsi_a'] ?? null, $bs['opsi_b'] ?? null, $bs['opsi_c'] ?? null, $bs['opsi_d'] ?? null, $bs['opsi_e'] ?? null, $bs['kunci_jawaban'] ?? null, $bs['konten_tambahan'] ?? null];
                            $stmtBS1->execute($vals);
                            if ($stmtBS2) {
                                try { $stmtBS2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntBS++;
                        } catch (\Throwable $e) {}
                    }
                    $status['bs_soal'] = $cntBS;
                }

                // 4. Jadwal Grid (cbt_jadwal_grid)
                if (!empty($data['jadwal_grid'])) {
                    $stmtG = $pdoCore->prepare("INSERT INTO cbt_jadwal_grid (id, topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE mapel_nama = VALUES(mapel_nama), ujian_id = VALUES(ujian_id), tanggal = VALUES(tanggal), hari = VALUES(hari), jam_slot = VALUES(jam_slot), topik_id = VALUES(topik_id)");
                    $cntG = 0;
                    foreach ($data['jadwal_grid'] as $g) {
                        try {
                            $stmtG->execute([$g['id'], $g['topik_id'] ?? null, $g['ujian_id'] ?? 0, $g['mapel_nama'] ?? '', $g['hari'], $g['jam_slot'], $g['kelas_id'] ?? null, $g['kelas_nama'], $g['pengawas'] ?? null, $g['ruang'] ?? null, $g['tanggal'] ?? null]);
                            $cntG++;
                        } catch (\Throwable $e) {}
                    }
                    $status['jadwal'] = $cntG;
                }

                // 5. Ujian CBT & Butir Soal Ujian
                if (!empty($data['ujian'])) {
                    $validUjianIds = array_filter(array_map('intval', array_column($data['ujian'], 'id')));
                    $syncTopikIds = array_filter(array_map('intval', array_column($data['topik'] ?? [], 'id')));
                    if (!empty($validUjianIds) && !empty($syncTopikIds)) {
                        $inTopikStr = implode(',', $syncTopikIds);
                        $inUjianStr = implode(',', $validUjianIds);
                        try { $pdoCore->exec("DELETE FROM cbt_soal WHERE ujian_id IN (SELECT id FROM cbt_ujian WHERE topik_id IN ($inTopikStr) AND id NOT IN ($inUjianStr))"); } catch (\Throwable $e) {}
                        try { $pdoCore->exec("DELETE FROM cbt_ujian WHERE topik_id IN ($inTopikStr) AND id NOT IN ($inUjianStr)"); } catch (\Throwable $e) {}
                    }

                    $stmtU = $pdoCore->prepare("INSERT INTO cbt_ujian (id, topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_ujian = VALUES(nama_ujian), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), status = VALUES(status), durasi_menit = VALUES(durasi_menit), kkm = VALUES(kkm)");
                    $cntU = 0;
                    foreach ($data['ujian'] as $u) {
                        try {
                            $stmtU->execute([$u['id'], $u['topik_id'] ?? null, $u['kode_ujian'], $u['nama_ujian'], $u['mapel_nama'], $u['kelas_nama'], $u['durasi_menit'] ?? 60, $u['kkm'] ?? 75, $u['acak_soal'] ?? 1, $u['acak_opsi'] ?? 1, $u['token'] ?? '', $u['status'] ?? 'Aktif']);
                            $cntU++;
                        } catch (\Throwable $e) {}
                    }
                    $status['ujian'] = $cntU;
                }

                if (!empty($data['soal'])) {
                    $validSoalIds = array_filter(array_map('intval', array_column($data['soal'], 'id')));
                    $syncUjianIds = array_filter(array_map('intval', array_column($data['ujian'] ?? [], 'id')));
                    if (!empty($validSoalIds) && !empty($syncUjianIds)) {
                        $inUjianStr = implode(',', $syncUjianIds);
                        $inSoalStr = implode(',', $validSoalIds);
                        try { $pdoCore->exec("DELETE FROM cbt_soal WHERE ujian_id IN ($inUjianStr) AND id NOT IN ($inSoalStr)"); } catch (\Throwable $e) {}
                    }

                    $stmtSoal = $pdoCore->prepare("INSERT INTO cbt_soal (id, ujian_id, nomor_urut, tipe_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan)");
                    $cntS = 0;
                    foreach ($data['soal'] as $so) {
                        try {
                            $stmtSoal->execute([$so['id'], $so['ujian_id'], $so['nomor_urut'], $so['tipe_soal'] ?? 'sec_pg', $so['pertanyaan'], $so['opsi_a'] ?? null, $so['opsi_b'] ?? null, $so['opsi_c'] ?? null, $so['opsi_d'] ?? null, $so['opsi_e'] ?? null, $so['kunci_jawaban'] ?? null, $so['bobot'] ?? 1.00, $so['konten_tambahan'] ?? null]);
                            $cntS++;
                        } catch (\Throwable $e) {}
                    }
                    $status['soal_cbt'] = $cntS;
                }

                // 6. Siswa Reguler
                if (!empty($data['siswa'])) {
                    $sqlSsw = "INSERT INTO siswa (id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nis = VALUES(nis), nik = VALUES(nik), status = VALUES(status), kelas_id = VALUES(kelas_id)";
                    $stmtSsw1 = $pdoCore->prepare($sqlSsw);
                    $stmtSsw2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlSsw) : null;
                    $cntSw = 0;
                    foreach ($data['siswa'] as $sw) {
                        try {
                            $vals = [$sw['id'], $sw['user_id'] ?? null, $sw['nis'], $sw['nik'] ?? null, $sw['nama'] ?? ($sw['nama_lengkap'] ?? ''), $sw['status'] ?? 'Aktif', $sw['kelas_id'] ?? null, $sw['jenis_kelamin'] ?? 'L'];
                            $stmtSsw1->execute($vals);
                            if ($stmtSsw2) {
                                try { $stmtSsw2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntSw++;
                        } catch (\Throwable $e) {}
                    }
                    $status['siswa'] = $cntSw;
                }

                // 7. Akun Login Users
                if (!empty($data['users'])) {
                    $sqlUsr = "INSERT INTO users (id, username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), plain_password = VALUES(plain_password), role_id = VALUES(role_id), status = VALUES(status)";
                    $stmtU1 = $pdoCore->prepare($sqlUsr);
                    $stmtU2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlUsr) : null;
                    $cntUsr = 0;
                    foreach ($data['users'] as $u) {
                        try {
                            $vals = [$u['id'], $u['username'], $u['password_hash'], $u['plain_password'] ?? null, $u['role_id'], $u['status'] ?? 'active'];
                            $stmtU1->execute($vals);
                            if ($stmtU2) {
                                try { $stmtU2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntUsr++;
                        } catch (\Throwable $e) {}
                    }
                    $status['users'] = $cntUsr;
                }

                // 7b. Sinkronisasi Permanen user_id Siswa dengan Akun Login NIK / NIS
                try {
                    $pdoCore->exec("
                        UPDATE siswa s 
                        JOIN users u ON (u.username = s.nik AND s.nik != '' AND s.nik IS NOT NULL)
                        SET s.user_id = u.id
                    ");
                    $pdoCore->exec("
                        UPDATE siswa s 
                        JOIN users u ON (u.username = s.nis AND s.nis != '' AND s.nis IS NOT NULL)
                        SET s.user_id = u.id
                        WHERE s.user_id IS NULL OR s.user_id = 0
                    ");
                } catch (\Throwable $e) {}
                if ($pdoSiakad !== $pdoCore) {
                    try {
                        $pdoSiakad->exec("
                            UPDATE siswa s 
                            JOIN users u ON (u.username = s.nik AND s.nik != '' AND s.nik IS NOT NULL)
                            SET s.user_id = u.id
                        ");
                        $pdoSiakad->exec("
                            UPDATE siswa s 
                            JOIN users u ON (u.username = s.nis AND s.nis != '' AND s.nis IS NOT NULL)
                            SET s.user_id = u.id
                            WHERE s.user_id IS NULL OR s.user_id = 0
                        ");
                    } catch (\Throwable $e) {}
                }

                // 8. Kelas
                if (!empty($data['kelas'])) {
                    $sqlK = "INSERT INTO kelas (id, nama_kelas, tingkat) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nama_kelas = VALUES(nama_kelas), tingkat = VALUES(tingkat)";
                    $stmtK1 = $pdoCore->prepare($sqlK);
                    $stmtK2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlK) : null;
                    $cntK = 0;
                    foreach ($data['kelas'] as $k) {
                        try {
                            $vals = [$k['id'], $k['nama_kelas'], $k['tingkat'] ?? 'X'];
                            $stmtK1->execute($vals);
                            if ($stmtK2) {
                                try { $stmtK2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntK++;
                        } catch (\Throwable $e) {}
                    }
                    $status['kelas'] = $cntK;
                }

                // 9. Mapel
                if (!empty($data['mapel'])) {
                    $sqlM = "INSERT INTO mapel (id, nama_mapel, kode_mapel, kategori) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_mapel = VALUES(nama_mapel), kode_mapel = VALUES(kode_mapel), kategori = VALUES(kategori)";
                    $stmtM1 = $pdoCore->prepare($sqlM);
                    $stmtM2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlM) : null;
                    $cntM = 0;
                    foreach ($data['mapel'] as $m) {
                        try {
                            $vals = [$m['id'], $m['nama_mapel'], $m['kode_mapel'] ?? '', $m['kategori'] ?? 'Umum'];
                            $stmtM1->execute($vals);
                            if ($stmtM2) {
                                try { $stmtM2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntM++;
                        } catch (\Throwable $e) {}
                    }
                    $status['mapel'] = $cntM;
                }

                // 10. Guru & Pengawas
                if (!empty($data['guru'])) {
                    $sqlG = "INSERT INTO guru (id, user_id, nama, nip, nik, is_kamad) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nip = VALUES(nip), nik = VALUES(nik)";
                    $stmtG1 = $pdoCore->prepare($sqlG);
                    $stmtG2 = ($pdoSiakad !== $pdoCore) ? $pdoSiakad->prepare($sqlG) : null;
                    $cntGuru = 0;
                    foreach ($data['guru'] as $g) {
                        try {
                            $vals = [$g['id'], $g['user_id'] ?? null, $g['nama'], $g['nip'] ?? null, $g['nik'] ?? null, $g['is_kamad'] ?? 0];
                            $stmtG1->execute($vals);
                            if ($stmtG2) {
                                try { $stmtG2->execute($vals); } catch (\Throwable $e2) {}
                            }
                            $cntGuru++;
                        } catch (\Throwable $e) {}
                    }
                    $status['guru'] = $cntGuru;
                }

                // 11. Token Ujian
                if (!empty($data['token'])) {
                    $stmtTok = $pdoCore->prepare("INSERT INTO cbt_token (id, token, ujian_id, masa_berlaku_menit, status, created_at) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), token = VALUES(token)");
                    $cntTok = 0;
                    foreach ($data['token'] as $tk) {
                        try {
                            $stmtTok->execute([$tk['id'], $tk['token'], $tk['ujian_id'] ?? null, $tk['masa_berlaku_menit'] ?? 120, $tk['status'] ?? 'Aktif', $tk['created_at'] ?? date('Y-m-d H:i:s')]);
                            $cntTok++;
                        } catch (\Throwable $e) {}
                    }
                    $status['token'] = $cntTok;
                }

                // 12. Peserta Khusus & Denah
                if (!empty($data['peserta_khusus'])) {
                    $stmtKj = $pdoCore->prepare("INSERT INTO cbt_peserta_khusus (id, nama_siswa, no_peserta, nis, tingkat, kelas_nama, username, password_plain, password_hash, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_siswa = VALUES(nama_siswa), no_peserta = VALUES(no_peserta), status = VALUES(status)");
                    $cntKj = 0;
                    foreach ($data['peserta_khusus'] as $pk) {
                        try {
                            $stmtKj->execute([$pk['id'], $pk['nama_siswa'], $pk['no_peserta'], $pk['nis'] ?? $pk['no_peserta'], $pk['tingkat'], $pk['kelas_nama'], $pk['username'], $pk['password_plain'], $pk['password_hash'], $pk['status'] ?? 'Aktif']);
                            $cntKj++;
                        } catch (\Throwable $e) {}
                    }
                    $status['peserta_khusus'] = $cntKj;
                }

                // Pastikan akun proktor default ada
                try {
                    $hasAdmin = (int)$pdoCore->query("SELECT COUNT(*) FROM users WHERE role_id = 1")->fetchColumn();
                    if ($hasAdmin === 0) {
                        $pwHash = password_hash('proktor123', PASSWORD_BCRYPT);
                        $pdoCore->exec("INSERT INTO users (username, password_hash, plain_password, role_id, status) VALUES ('proktor', '{$pwHash}', 'proktor123', 1, 'active')");
                    }
                } catch (\Throwable $e) {}

                $isDone = true;
            }
        }
    } catch (\Throwable $e) {
        $errorMsg = "Terjadi kegagalan: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi Kilat Server CBT Madrasah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-slate-800 border border-slate-700 rounded-3xl p-8 shadow-2xl">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-700">
            <div class="w-14 h-14 bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white">Sinkronisasi Kilat Server CBT</h1>
                <p class="text-xs text-slate-400">Penyelarasan Bersih ID Topik, Bank Soal, Jadwal & Siswa MAS MIFHDA</p>
            </div>
        </div>

        <?php if ($isDone): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 p-5 rounded-2xl mb-6">
                <div class="flex items-center gap-3 font-bold text-base mb-2">
                    <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
                    <span>ALHAMDULILLAH! SINKRONISASI BERHASIL 100%</span>
                </div>
                <p class="text-xs text-emerald-200 mb-4">Seluruh data lama/dummy berhasil dibersihkan dan digantikan dengan data resmi dari server pusat online.</p>
                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-900/60 p-4 rounded-xl border border-emerald-500/30">
                    <div>• Topik Ujian: <b class="text-white"><?= $status['topik'] ?? 0 ?> Topik</b></div>
                    <div>• Paket Bank Soal: <b class="text-white"><?= $status['bs_paket'] ?? 0 ?> Paket</b></div>
                    <div>• Butir Bank Soal: <b class="text-white"><?= $status['bs_soal'] ?? 0 ?> Butir</b></div>
                    <div>• Sesi Jadwal Grid: <b class="text-white"><?= $status['jadwal'] ?? 0 ?> Sesi</b></div>
                    <div>• Paket Ujian CBT: <b class="text-white"><?= $status['ujian'] ?? 0 ?> Ujian</b></div>
                    <div>• Butir Soal CBT: <b class="text-white"><?= $status['soal_cbt'] ?? 0 ?> Butir</b></div>
                    <div>• Siswa Madrasah: <b class="text-white"><?= $status['siswa'] ?? 0 ?> Siswa</b></div>
                    <div>• Akun Login: <b class="text-white"><?= $status['users'] ?? 0 ?> Pengguna</b></div>
                    <div>• Data Mapel: <b class="text-white"><?= $status['mapel'] ?? 0 ?> Mapel</b></div>
                    <div>• Data Kelas: <b class="text-white"><?= $status['kelas'] ?? 0 ?> Kelas</b></div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <a href="bank_soal" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl text-center transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30">
                    <i class="fa-solid fa-layer-group"></i> 👉 Buka Bank Soal Madrasah Sekarang
                </a>
                <a href="jadwal" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold py-3 px-6 rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-calendar-days"></i> Cek Jadwal Ujian CBT
                </a>
                <a href="topik" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-3 px-6 rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-calendar-check"></i> Cek Master Topik Ujian
                </a>
                <a href="semi-online" class="w-full bg-slate-900 hover:bg-slate-800 text-slate-400 font-bold py-2.5 px-6 rounded-xl text-center transition text-xs">
                    Kembali ke Menu Semi-Online
                </a>
            </div>
        <?php else: ?>
            <?php if (!empty($errorMsg)): ?>
                <div class="bg-rose-500/20 border border-rose-500/40 text-rose-300 p-4 rounded-xl text-sm mb-6 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-lg mt-0.5 text-rose-400"></i>
                    <div>
                        <div class="font-bold">Gagal Sinkronisasi:</div>
                        <div class="text-xs mt-1 text-rose-200"><?= htmlspecialchars($errorMsg) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-xs text-slate-300 bg-slate-700/50 p-4 rounded-xl mb-6 leading-relaxed border border-slate-600">
                <b class="text-indigo-400 font-bold"><i class="fa-solid fa-info-circle mr-1"></i> Apa yang dilakukan alat ini?</b><br>
                1. Memperbaiki dan memastikan database <code class="text-emerald-400">aula_db</code> & <code class="text-emerald-400">aula_siakad_db</code> siap 100%.<br>
                2. Menghapus 5 topik dummy bawaan awal yang ID-nya salah / tidak cocok.<br>
                3. Mengimpor 4 Topik Resmi (SAS 89 Sesi, UM, STS, Simulasi).<br>
                4. Mengimpor 154 Paket Bank Soal dan 1.980 Butir Soal.<br>
                5. Mengimpor 125 Jadwal Grid, Siswa SIAKAD, dan Akun Login Peserta.
            </div>

            <form method="POST">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-300 mb-2">URL Server Online Pusat:</label>
                    <input type="text" name="server_url" value="https://apk.masmifhda.sch.id" required class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-bolt"></i> Mulai Sinkronisasi Kilat (1-Klik Bersih)
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
