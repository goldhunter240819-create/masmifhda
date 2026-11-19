<?php
// GHTech Analytics Tracking for CBT Activity
$_ghtech_key = "4a0fef55431a38a8ee6317c512178502";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}

require_once 'koneksi.php';
require_once __DIR__ . '/apk_version_guard.php';
check_apk_version_guard();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

// Track active student username to GHTech Developer Monitor
$_ghtech_user = $_SESSION['siswa_nama'] ?? ($_SESSION['nama'] ?? '');
if (!empty($_ghtech_user)) {
    $ctx = stream_context_create(['http' => ['timeout' => 1]]);
    @file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_ghtech_user), false, $ctx);
}

$siswa_id = (int)$_SESSION['siswa_id'];
$is_cbt_only = !empty($_SESSION['is_cbt_only']);

if ($is_cbt_only) {
    $stmt = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = :id AND status = 'Aktif' LIMIT 1");
    $stmt->execute(['id' => $siswa_id]);
    $cbtStudent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cbtStudent) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    $siswa = [
        'id' => $cbtStudent['id'],
        'nama' => $cbtStudent['nama_siswa'],
        'nis' => $cbtStudent['no_peserta'],
        'nik' => $cbtStudent['nis'] ?? '',
        'nama_kelas' => $cbtStudent['kelas_nama'],
        'tingkat' => $cbtStudent['tingkat'],
        'status' => 'Kelas Jauh',
        'foto' => $cbtStudent['foto'] ?? ''
    ];

    $tingkat_kelas = $cbtStudent['tingkat'];
    if (stripos($tingkat_kelas, 'XII') !== false || stripos($tingkat_kelas, '12') !== false) {
        $tingkat_kelas = 'Kelas XII';
        $short_tingkat = 'XII';
    } elseif (stripos($tingkat_kelas, 'XI') !== false || stripos($tingkat_kelas, '11') !== false) {
        $tingkat_kelas = 'Kelas XI';
        $short_tingkat = 'XI';
    } else {
        $tingkat_kelas = 'Kelas X';
        $short_tingkat = 'X';
    }
} else {
    // Ambil data siswa
    $stmt = $core_db->prepare("SELECT s.*, COALESCE(k.nama_kelas, 'Umum') as nama_kelas 
                               FROM siswa s 
                               LEFT JOIN kelas k ON s.kelas_id = k.id 
                               WHERE s.id = :id LIMIT 1");
    $stmt->execute(['id' => $siswa_id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$siswa) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Tentukan tingkat kelas
    $tingkat_kelas = 'Kelas X';
    $short_tingkat = 'X';
    if (stripos($siswa['nama_kelas'], 'XII') !== false || stripos($siswa['nama_kelas'], '12') !== false) {
        $tingkat_kelas = 'Kelas XII';
        $short_tingkat = 'XII';
    } elseif (stripos($siswa['nama_kelas'], 'XI') !== false || stripos($siswa['nama_kelas'], '11') !== false) {
        $tingkat_kelas = 'Kelas XI';
        $short_tingkat = 'XI';
    }
}

// Inisialisasi info tempat duduk (akan ditentukan setelah evaluasi topik aktif/terdekat)
$seatingInfo = null;

// 1. Jalankan Auto-Sync Matriks Jadwal ke CBT Ujian
try {
    if (class_exists('\App\Controllers\CbtController')) {
        \App\Controllers\CbtController::syncJadwalWithUjian($core_db, $siakad_db);
    }
} catch (\Exception $e) {}

// 2. Ambil Pengaturan CBT Terpusat
$cbtSettings = [];
try {
    $stmtSt = $core_db->query("SELECT setting_key, setting_value FROM cbt_pengaturan");
    if ($stmtSt) {
        while ($stRow = $stmtSt->fetch(PDO::FETCH_ASSOC)) {
            $cbtSettings[$stRow['setting_key']] = $stRow['setting_value'];
        }
    }
} catch (\Exception $e) {}

$lockSchedule = ($cbtSettings['lock_by_schedule_time'] ?? '1') === '1';
$requireAttendance = ($cbtSettings['require_cbt_attendance'] ?? '0') === '1';
$showScoreToStudent = ($cbtSettings['show_score_to_student'] ?? '1') === '1';
$maxTabViolations = (int)($cbtSettings['max_tab_violations'] ?? 3);
$tokenMasaBerlaku = (int)($cbtSettings['token_refresh_interval'] ?? 120);
if ($tokenMasaBerlaku <= 0) $tokenMasaBerlaku = 120;

// 3. Ambil Info Topik Terdekat yang Aktif
$nearestTopik = null;
try {
    $stmtTp = $core_db->query("
        SELECT * FROM bs_topik 
        WHERE status = 'Aktif' 
        ORDER BY 
            CASE 
                WHEN tgl_mulai >= CURDATE() THEN 0 
                WHEN (tgl_mulai IS NOT NULL AND tgl_mulai != '' AND tgl_selesai >= CURDATE()) THEN 1 
                ELSE 2 
            END ASC,
            ABS(DATEDIFF(COALESCE(NULLIF(tgl_mulai, ''), CURDATE()), CURDATE())) ASC,
            id DESC
        LIMIT 1
    ");
    if ($stmtTp) {
        $nearestTopik = $stmtTp->fetch(PDO::FETCH_ASSOC);
    }
    if (!$nearestTopik && isset($siakad_db)) {
        $stmtTpS = $siakad_db->query("
            SELECT * FROM bs_topik 
            WHERE status = 'Aktif' 
            ORDER BY 
                CASE 
                    WHEN tgl_mulai >= CURDATE() THEN 0 
                    WHEN (tgl_mulai IS NOT NULL AND tgl_mulai != '' AND tgl_selesai >= CURDATE()) THEN 1 
                    ELSE 2 
                END ASC,
                ABS(DATEDIFF(COALESCE(NULLIF(tgl_mulai, ''), CURDATE()), CURDATE())) ASC,
                id DESC
            LIMIT 1
        ");
        if ($stmtTpS) {
            $nearestTopik = $stmtTpS->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (\Exception $e) {}
$nearestTopikId = (int)($nearestTopik['id'] ?? 0);
$topikAktif = $nearestTopik;

// 4. Daftar Ujian CBT yang Aktif untuk Siswa
$ujianListRaw = [];
try {
    $allowedKelas = ['Semua Kelas', '', $siswa['nama_kelas'], $tingkat_kelas, $short_tingkat];
    if ($short_tingkat === 'XII') {
        $allowedKelas[] = 'Kelas 12';
        $allowedKelas[] = '12';
    } elseif ($short_tingkat === 'XI') {
        $allowedKelas[] = 'Kelas 11';
        $allowedKelas[] = '11';
    } else {
        $allowedKelas[] = 'Kelas 10';
        $allowedKelas[] = '10';
    }

    try {
        $numTingkat = ($short_tingkat === 'XII') ? 12 : (($short_tingkat === 'XI') ? 11 : 10);
        $stmtK = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE tingkat = ?");
        $stmtK->execute([$numTingkat]);
        $rombelNames = $stmtK->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($rombelNames)) {
            $allowedKelas = array_merge($allowedKelas, $rombelNames);
        }
    } catch (\Exception $e) {}

    $allowedKelas = array_values(array_unique(array_filter($allowedKelas)));
    $inPlaceholders = implode(',', array_fill(0, count($allowedKelas), '?'));

    $sqlUjian = "
        SELECT u.*,
               t.nama_topik,
               COALESCE((SELECT COUNT(*) FROM cbt_soal s WHERE s.ujian_id = u.id), 0) as total_soal,
               p.id as peserta_id,
               p.status_pengerjaan,
               p.nilai_akhir,
               p.waktu_mulai,
               p.waktu_selesai
        FROM cbt_ujian u
        LEFT JOIN cbt_peserta p ON p.ujian_id = u.id AND (p.siswa_id = ? OR (p.nis = ? AND ? != ''))
        LEFT JOIN bs_topik t ON u.topik_id = t.id
        WHERE u.status = 'Aktif'
          AND (u.topik_id IS NULL OR u.topik_id = 0 OR (t.id IS NOT NULL AND t.status = 'Aktif'))
          AND (u.kelas_nama IS NULL OR u.kelas_nama = '' OR u.kelas_nama IN ($inPlaceholders))
        ORDER BY 
            CASE WHEN u.topik_id = {$nearestTopikId} THEN 0 ELSE 1 END ASC,
            (total_soal > 0) DESC, 
            u.id DESC
    ";
    $params = array_merge([$siswa_id, (string)($siswa['nis'] ?? ''), (string)($siswa['nis'] ?? '')], $allowedKelas);
    $stmtUjian = $core_db->prepare($sqlUjian);
    $stmtUjian->execute($params);
    $ujianListRaw = $stmtUjian->fetchAll(PDO::FETCH_ASSOC);

    // Deduplikasi Ujian per Mapel & Topik: Prioritaskan topik terdekat
    $seenMapelKeys = [];
    $dedupedList = [];
    foreach ($ujianListRaw as $uRaw) {
        $mRaw = strtolower(trim($uRaw['mapel_nama'] ?? ''));
        if (strpos($mRaw, 'arab') !== false) {
            $mKeyNorm = 'bahasa arab';
        } elseif (strpos($mRaw, 'biologi') !== false) {
            $mKeyNorm = 'biologi';
        } elseif (strpos($mRaw, 'matematika') !== false) {
            $mKeyNorm = 'matematika';
        } else {
            $mKeyNorm = $mRaw;
        }

        $mKey = ($uRaw['topik_id'] ?? 0) . '_' . $mKeyNorm;
        if (isset($seenMapelKeys[$mKey])) {
            continue; // Lewati duplikat mapel yang sama dalam satu topik
        }
        $seenMapelKeys[$mKey] = true;
        $dedupedList[] = $uRaw;
    }
    $ujianListRaw = $dedupedList;
} catch (\Exception $e) {}

// Filter & Validasi Paket Ujian Tersedia (Sembunyikan yang sudah selesai)
$ujianTersedia = [];
$allEvaluatedUjian = []; // Menyimpan semua ujian untuk evaluasi status asesmen hari ini
$hariIndo = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
$curHari = $hariIndo[date('l')] ?? '';
$todayDate = date('Y-m-d');
$nowTs = time();
$dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

// Ambil jadwal lengkap siswa untuk Topik Terdekat (Untuk Modal Pop-up Jadwal Ujian)
$jadwalSiswaList = [];
try {
    $sqlJadwalSiswa = "
        SELECT g.*, 
               u.durasi_menit, u.kkm,
               t.nama_topik, t.tahun_ajaran as topik_ta
        FROM cbt_jadwal_grid g
        LEFT JOIN bs_topik t ON g.topik_id = t.id
        LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
        WHERE (g.kelas_nama = :kelas OR g.kelas_nama = 'Semua Kelas')
    ";
    if ($nearestTopikId > 0) {
        $sqlJadwalSiswa .= " AND (g.topik_id = :topik_id OR g.topik_id IS NULL OR g.topik_id = 0)";
    }
    $sqlJadwalSiswa .= " ORDER BY COALESCE(g.tanggal, '2099-12-31') ASC, g.jam_slot ASC, g.id ASC";

    $stmtJadwalSiswa = $core_db->prepare($sqlJadwalSiswa);
    $paramsJadwal = [
        ':kelas' => $siswa['nama_kelas']
    ];
    if ($nearestTopikId > 0) {
        $paramsJadwal[':topik_id'] = $nearestTopikId;
    }
    $stmtJadwalSiswa->execute($paramsJadwal);
    $rawGrids = $stmtJadwalSiswa->fetchAll(PDO::FETCH_ASSOC);

    // Ambil mapping slot sesi untuk penentuan label sesi & jam
    $slotRef = [];
    try {
        $stmtSlots = $core_db->prepare("SELECT * FROM cbt_jam_slot WHERE (topik_id = :tid OR topik_id IS NULL OR topik_id = 0) ORDER BY urutan ASC");
        $stmtSlots->execute([':tid' => $nearestTopikId]);
        $slotRef = $stmtSlots->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}

    $processedGrids = [];
    $seenGridKeys = [];
    foreach ($rawGrids as $rg) {
        $slotStr = trim($rg['jam_slot'] ?? '');
        $parts = explode('-', $slotStr);
        $startStr = trim($parts[0] ?? '');
        $endStr = trim($parts[1] ?? '');

        // Cari slot sesi yang cocok
        $matchedSlot = null;
        foreach ($slotRef as $sl) {
            $slStart = substr(trim($sl['jam_mulai']), 0, 5);
            $slEnd = substr(trim($sl['jam_selesai']), 0, 5);
            if ($startStr === $slStart || $slotStr === $sl['label_sesi'] || $slotStr === ($slStart . ' - ' . $slEnd)) {
                $matchedSlot = $sl;
                break;
            }
        }

        // Jika slot istirahat, jangan jadikan sebagai jadwal ujian
        if ($matchedSlot && !empty($matchedSlot['is_istirahat'])) {
            continue;
        }

        $labelSesi = $matchedSlot['label_sesi'] ?? '';
        if (empty($labelSesi)) {
            if ($startStr === '08:00') $labelSesi = 'Sesi 1';
            elseif ($startStr === '09:00') $labelSesi = 'Sesi 2';
            elseif ($startStr === '10:10') $labelSesi = 'Sesi 3';
            else $labelSesi = $slotStr ?: 'Sesi Ujian';
        }

        $tglStr = $rg['tanggal'] ?? '';
        $mNorm = strtolower(trim($rg['mapel_nama'] ?? ''));
        $dedupKey = $tglStr . '_' . $startStr . '_' . $mNorm;
        if (isset($seenGridKeys[$dedupKey])) {
            continue;
        }
        $seenGridKeys[$dedupKey] = true;

        $rg['label_sesi'] = $labelSesi;
        $rg['jam_mulai'] = $matchedSlot ? $matchedSlot['jam_mulai'] : ($startStr ? ($startStr . ':00') : '');
        $rg['jam_selesai'] = $matchedSlot ? $matchedSlot['jam_selesai'] : ($endStr ? ($endStr . ':00') : '');
        $processedGrids[] = $rg;
    }
    $jadwalSiswaList = $processedGrids;
} catch (\Exception $e) {}

// Ambil Data Absensi Siswa Hari Ini jika Wajib Absensi Aktif
$absenHariIniMap = [];
if ($requireAttendance) {
    try {
        $stmtAbs = $core_db->prepare("
            SELECT ujian_id, mapel_nama, status, waktu_scan 
            FROM cbt_absensi 
            WHERE (siswa_id = :sid OR (nis = :nis AND :nis != '')) 
              AND (tanggal = :today OR DATE(waktu_scan) = :today2)
              AND status IN ('Hadir', 'Terlambat')
        ");
        $stmtAbs->execute([
            ':sid' => $siswa_id,
            ':nis' => (string)($siswa['nis'] ?? ''),
            ':today' => $todayDate,
            ':today2' => $todayDate
        ]);
        while ($abRow = $stmtAbs->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($abRow['ujian_id'])) {
                $absenHariIniMap['uid_' . $abRow['ujian_id']] = $abRow;
            }
            $mKey = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $abRow['mapel_nama'] ?? '')));
            if ($mKey) {
                $absenHariIniMap['mapel_' . $mKey] = $abRow;
            }
        }
    } catch (\Exception $e) {}
}

foreach ($ujianListRaw as $u) {
    // 0. Seragamkan format nama ujian mengikuti topik aktif: [Topik] - [Mapel] ([Kelas/Tingkat])
    if (!empty($u['nama_topik'])) {
        $curTopikName = trim($u['nama_topik']);
        if (stripos($u['nama_ujian'], 'Simulasi ') === 0 || stripos($u['nama_ujian'], $curTopikName) === false) {
            $klsDisplay = !empty($u['kelas_nama']) && $u['kelas_nama'] !== 'Semua Kelas' ? $u['kelas_nama'] : ($siswa['nama_kelas'] ?? $tingkat_kelas);
            $u['nama_ujian'] = $curTopikName . ' - ' . $u['mapel_nama'] . ' (' . $klsDisplay . ')';
        }
    }
    // 1. Jamin Token Spesifik per Mapel & Kelas
    if (empty($u['token'])) {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $u['mapel_nama'] ?: 'CBT'), 0, 3));
        if (strlen($prefix) < 3) $prefix = str_pad($prefix, 3, 'X');
        $u['token'] = $prefix . rand(100, 999);
        try {
            $core_db->prepare("UPDATE cbt_ujian SET token = ? WHERE id = ?")->execute([$u['token'], $u['id']]);
            $core_db->prepare("INSERT INTO cbt_token (token, ujian_id, masa_berlaku_menit, status, created_at) VALUES (?, ?, ?, 'Aktif', NOW())")->execute([$u['token'], $u['id'], $tokenMasaBerlaku]);
        } catch (\Exception $e) {}
    }

    // 2. Evaluasi Aturan Jadwal (Strict Schedule vs Mode Susulan)
    $canTake = true;
    $scheduleState = 'open'; // 'open', 'early', 'expired'
    $scheduleNotice = '';
    $jadwalSlotLabel = '';
    $isHariIni = false;

    if ($lockSchedule) {
        try {
            // 1. Cek apakah ada jadwal spesifik untuk HARI INI
            $stmtToday = $core_db->prepare("
                SELECT g.*, s.jam_mulai, s.jam_selesai, s.label_sesi 
                FROM cbt_jadwal_grid g 
                LEFT JOIN cbt_jam_slot s ON (
                    (s.topik_id = g.topik_id OR s.topik_id IS NULL OR g.topik_id IS NULL)
                    AND (
                        g.jam_slot = s.label_sesi 
                        OR g.jam_slot = s.urutan
                        OR g.jam_slot = CONCAT(SUBSTRING(s.jam_mulai, 1, 5), ' - ', SUBSTRING(s.jam_selesai, 1, 5))
                        OR g.jam_slot LIKE CONCAT('%', SUBSTRING(s.jam_mulai, 1, 5), '%')
                    )
                )
                WHERE (g.tanggal = :today OR (g.tanggal IS NULL AND g.hari = :curHari))
                  AND (
                      g.ujian_id = :uid 
                      OR (
                          (g.mapel_nama = :mapel OR :mapel LIKE CONCAT('%', g.mapel_nama, '%') OR g.mapel_nama LIKE CONCAT('%', :mapel, '%'))
                          AND (
                              g.kelas_nama = :kelas 
                              OR g.kelas_nama = :tingkat 
                              OR g.kelas_nama = :short_tingkat
                              OR g.kelas_nama = 'Semua Kelas' 
                              OR :kelas LIKE CONCAT('%', g.kelas_nama, '%')
                              OR g.kelas_nama LIKE CONCAT('%', :short_tingkat, '%')
                          )
                      )
                  )
                ORDER BY (g.ujian_id = :uid2) DESC, (g.kelas_nama = :kelas2) DESC, g.id DESC LIMIT 1
            ");
            $stmtToday->execute([
                ':today' => $todayDate,
                ':curHari' => $curHari,
                ':uid' => $u['id'],
                ':mapel' => $u['mapel_nama'],
                ':kelas' => $siswa['nama_kelas'],
                ':tingkat' => $tingkat_kelas,
                ':short_tingkat' => $short_tingkat,
                ':uid2' => $u['id'],
                ':kelas2' => $siswa['nama_kelas']
            ]);
            $jGrid = $stmtToday->fetch(PDO::FETCH_ASSOC);

            if ($jGrid) {
                // Terjadwal HARI INI!
                $isHariIni = true;
                $slotLabel = $jGrid['label_sesi'] ?: $jGrid['jam_slot'];
                
                $jMulaiStr = !empty($jGrid['jam_mulai']) ? $jGrid['jam_mulai'] : '';
                $jSelesaiStr = !empty($jGrid['jam_selesai']) ? $jGrid['jam_selesai'] : '';

                if ((empty($jMulaiStr) || empty($jSelesaiStr)) && !empty($jGrid['jam_slot'])) {
                    if (preg_match('/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/', $jGrid['jam_slot'], $mTimes)) {
                        $jMulaiStr = $mTimes[1];
                        $jSelesaiStr = $mTimes[2];
                    }
                }

                $timeRange = (!empty($jMulaiStr) && !empty($jSelesaiStr)) ? $jMulaiStr . ' - ' . $jSelesaiStr . ' WIB' : '';
                $jadwalSlotLabel = trim($slotLabel . ' ' . ($timeRange ? "($timeRange)" : ''));

                $startTs = !empty($jMulaiStr) ? strtotime($todayDate . ' ' . $jMulaiStr) : 0;
                $endTs = !empty($jSelesaiStr) ? strtotime($todayDate . ' ' . $jSelesaiStr) : 0;

                if ($startTs && $nowTs < $startTs) {
                    // Jam mulai belum tiba
                    $scheduleState = 'early';
                    $canTake = false;
                    $scheduleNotice = 'Sesi dibuka pukul ' . $jMulaiStr . ' WIB';
                } elseif ($endTs && $nowTs > $endTs) {
                    // Jam selesai sudah terlewat!
                    $scheduleState = 'expired';
                    $canTake = false;
                    $scheduleNotice = 'Sesi telah berakhir pada pukul ' . $jSelesaiStr . ' WIB';
                } else {
                    // Jam sedang berlangsung
                    $scheduleState = 'open';
                    $canTake = true;
                    $scheduleNotice = 'Sesi Sedang Berlangsung (' . ($timeRange ?: 'Jam Ujian') . ')';
                }
            } else {
                // Tidak ada jadwal untuk HARI INI. Cek apakah ada jadwal di hari lain di cbt_jadwal_grid
                $uTopikId = (int)($u['topik_id'] ?? 0);
                $stmtOther = $core_db->prepare("
                    SELECT g.* 
                    FROM cbt_jadwal_grid g 
                    WHERE g.ujian_id = :uid 
                       OR (
                           (g.mapel_nama = :mapel OR :mapel LIKE CONCAT('%', g.mapel_nama, '%') OR g.mapel_nama LIKE CONCAT('%', :mapel, '%'))
                           AND (:tid <= 0 OR g.topik_id = :tid2 OR g.topik_id IS NULL OR g.topik_id = 0)
                           AND (
                               g.kelas_nama = :kelas 
                               OR g.kelas_nama = :tingkat 
                               OR g.kelas_nama = :short_tingkat
                               OR g.kelas_nama = 'Semua Kelas' 
                               OR :kelas LIKE CONCAT('%', g.kelas_nama, '%')
                               OR g.kelas_nama LIKE CONCAT('%', :short_tingkat, '%')
                           )
                       )
                    ORDER BY (g.ujian_id = :uid2) DESC, CASE WHEN g.tanggal >= CURDATE() THEN 0 ELSE 1 END ASC, g.tanggal ASC, g.jam_slot ASC, g.id ASC LIMIT 1
                ");
                $stmtOther->execute([
                    ':uid' => $u['id'],
                    ':mapel' => $u['mapel_nama'],
                    ':tid' => $uTopikId,
                    ':tid2' => $uTopikId,
                    ':kelas' => $siswa['nama_kelas'],
                    ':tingkat' => $tingkat_kelas,
                    ':short_tingkat' => $short_tingkat,
                    ':uid2' => $u['id']
                ]);
                $otherGrid = $stmtOther->fetch(PDO::FETCH_ASSOC);

                if ($otherGrid) {
                    $isHariIni = false;
                    $canTake = false;
                    $otherDayName = $otherGrid['hari'] ?: (!empty($otherGrid['tanggal']) ? date('l', strtotime($otherGrid['tanggal'])) : '');
                    $otherDayIndo = $hariIndo[$otherDayName] ?? $otherDayName;
                    $curDayIdx = $dayOrder[$curHari] ?? 0;
                    $targetDayIdx = $dayOrder[$otherDayIndo] ?? 0;

                    $jamSlotStr = trim($otherGrid['jam_slot'] ?? '');
                    $jamMulaiParsed = '';
                    if (strpos($jamSlotStr, '-') !== false) {
                        $parts = explode('-', $jamSlotStr);
                        $jamMulaiParsed = trim($parts[0]);
                    } else {
                        $jamMulaiParsed = $jamSlotStr;
                    }

                    $u['upcoming_date'] = $otherGrid['tanggal'] ?? '';
                    $u['upcoming_time'] = $jamMulaiParsed;
                    $u['grid_topik_id'] = $otherGrid['topik_id'] ?? 0;

                    $jamDisplay = !empty($jamMulaiParsed) ? ' (' . $jamMulaiParsed . ' WIB)' : '';
                    if (!empty($otherGrid['tanggal'])) {
                        if ($todayDate < $otherGrid['tanggal']) {
                            $scheduleState = 'early';
                            $scheduleNotice = 'Jadwal: ' . date('d M Y', strtotime($otherGrid['tanggal'])) . $jamDisplay;
                        } else {
                            $scheduleState = 'expired';
                            $scheduleNotice = 'Jadwal tanggal ' . date('d M Y', strtotime($otherGrid['tanggal'])) . ' telah terlewat';
                        }
                    } elseif ($targetDayIdx > 0 && $curDayIdx > 0) {
                        if ($curDayIdx < $targetDayIdx) {
                            $scheduleState = 'early';
                            $scheduleNotice = 'Jadwal: Hari ' . $otherDayIndo . $jamDisplay;
                        } else {
                            $scheduleState = 'expired';
                            $scheduleNotice = 'Jadwal Hari ' . $otherDayIndo . ' telah terlewat';
                        }
                    } else {
                        $scheduleState = 'early';
                        $scheduleNotice = 'Jadwal: Hari ' . $otherDayIndo . $jamDisplay;
                    }
                    $jadwalSlotLabel = 'Hari ' . $otherDayIndo;
                } else {
                    // Benar-benar tidak dijadwalkan hari ini (seperti Bahasa Arab)
                    $isHariIni = false;
                    $scheduleState = 'unscheduled';
                    $canTake = false;
                    $scheduleNotice = 'Tidak Ada Jadwal Hari Ini';
                    $jadwalSlotLabel = 'Belum Dijadwalkan';
                }
            }
        } catch (\Exception $e) {}
    } else {
        $isHariIni = true;
        $scheduleState = 'open';
        $canTake = true;
        $scheduleNotice = 'Mode Ujian Susulan (Terbuka)';
    }

    // Evaluasi Verifikasi Absensi 1 Pintu (Jika Diwajibkan di Pengaturan CBT)
    $hasAbsen = true;
    $absenInfo = null;
    if ($requireAttendance) {
        $hasAbsen = false;
        $mKeyNorm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $u['mapel_nama'] ?? '')));
        if (!empty($u['id']) && isset($absenHariIniMap['uid_' . $u['id']])) {
            $hasAbsen = true;
            $absenInfo = $absenHariIniMap['uid_' . $u['id']];
        } elseif ($mKeyNorm && isset($absenHariIniMap['mapel_' . $mKeyNorm])) {
            $hasAbsen = true;
            $absenInfo = $absenHariIniMap['mapel_' . $mKeyNorm];
        }
    }
    $u['has_absen'] = $hasAbsen;
    $u['absen_info'] = $absenInfo;

    $u['is_hari_ini'] = $isHariIni;
    $u['can_take'] = $canTake;
    $u['schedule_state'] = $scheduleState;
    $u['schedule_notice'] = $scheduleNotice;
    $u['jadwal_slot_label'] = $jadwalSlotLabel;
    $u['upcoming_date'] = $otherGrid['tanggal'] ?? '';
    $u['upcoming_time'] = $otherGrid['jam_mulai'] ?? '';
    $u['grid_topik_id'] = $otherGrid['topik_id'] ?? 0;

    $allEvaluatedUjian[] = $u;
}

// 4a. Kelompokkan Paket Ujian Tersedia (Belum Selesai)
$ujianHariIni = [];
$ujianLainnya = [];

foreach ($allEvaluatedUjian as $u) {
    if ($u['status_pengerjaan'] === 'Selesai') {
        continue;
    }
    if ($lockSchedule) {
        if (!empty($u['is_hari_ini'])) {
            $ujianHariIni[] = $u;
        } else {
            $ujianLainnya[] = $u;
        }
    } else {
        $ujianHariIni[] = $u;
    }
}

// Urutkan paket ujian hari ini agar sesi yang aktif/siap selalu di atas, disusul yang akan datang, dan yang expired di bawah
usort($ujianHariIni, function($a, $b) {
    $rank = function($x) {
        if (in_array($x['status_pengerjaan'] ?? '', ['Mengerjakan', 'Sedang Mengerjakan'])) return 1;
        if (!empty($x['can_take'])) return 2;
        if (($x['schedule_state'] ?? '') === 'early') return 3;
        if (($x['schedule_state'] ?? '') === 'expired') return 4;
        return 5;
    };
    return $rank($a) <=> $rank($b);
});

$ujianTersedia = array_merge($ujianHariIni, $ujianLainnya);

// 4b. Tentukan Asesmen yang Sedang Berlangsung Saat Ini
$activeOngoingUjian = null;
$upcomingUjian = null;
$completedTodayUjian = null;

// Prioritas 1: Yang sedang dikerjakan saat ini
foreach ($allEvaluatedUjian as $uItem) {
    if (in_array($uItem['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan'])) {
        $activeOngoingUjian = $uItem;
        break;
    }
}

// Prioritas 2: Yang aktif hari ini dan siap dikerjakan
if (!$activeOngoingUjian) {
    foreach ($allEvaluatedUjian as $uItem) {
        if ($uItem['status_pengerjaan'] !== 'Selesai' && $uItem['can_take']) {
            $activeOngoingUjian = $uItem;
            break;
        }
    }
}

// Prioritas 3: Jadwal hari ini tapi jam mulai belum tiba
if (!$activeOngoingUjian) {
    foreach ($allEvaluatedUjian as $uItem) {
        if (!empty($uItem['is_hari_ini']) && $uItem['schedule_state'] === 'early') {
            $upcomingUjian = $uItem;
            break;
        }
    }
}

// Prioritas 4: Jika jadwal hari ini sudah diselesaikan siswa
if (!$activeOngoingUjian && !$upcomingUjian) {
    foreach ($allEvaluatedUjian as $uItem) {
        if (!empty($uItem['is_hari_ini']) && $uItem['status_pengerjaan'] === 'Selesai') {
            $completedTodayUjian = $uItem;
            break;
        }
    }
}

// Prioritas 5: Cari jadwal mendatang di hari berikutnya (Prioritaskan topik terdekat & tanggal terawal)
if (!$activeOngoingUjian && !$upcomingUjian && !$completedTodayUjian) {
    $upcomingCandidates = [];
    $nearestTopikId = (int)($nearestTopik['id'] ?? 0);

    foreach ($allEvaluatedUjian as $uItem) {
        if ($uItem['status_pengerjaan'] !== 'Selesai' && $uItem['schedule_state'] === 'early') {
            $upcomingCandidates[] = $uItem;
        }
    }

    if (!empty($upcomingCandidates)) {
        usort($upcomingCandidates, function($a, $b) use ($nearestTopikId) {
            $tA = (int)($a['topik_id'] ?: ($a['grid_topik_id'] ?? 0));
            $tB = (int)($b['topik_id'] ?: ($b['grid_topik_id'] ?? 0));
            $isTopikA = ($nearestTopikId > 0 && $tA === $nearestTopikId) ? 0 : 1;
            $isTopikB = ($nearestTopikId > 0 && $tB === $nearestTopikId) ? 0 : 1;
            if ($isTopikA !== $isTopikB) {
                return $isTopikA <=> $isTopikB;
            }

            $dateA = !empty($a['upcoming_date']) ? $a['upcoming_date'] : '9999-12-31';
            $dateB = !empty($b['upcoming_date']) ? $b['upcoming_date'] : '9999-12-31';
            if ($dateA !== $dateB) {
                return strcmp($dateA, $dateB);
            }

            $timeA = !empty($a['upcoming_time']) ? $a['upcoming_time'] : '00:00';
            $timeB = !empty($b['upcoming_time']) ? $b['upcoming_time'] : '00:00';
            return strcmp($timeA, $timeB);
        });

        $upcomingUjian = $upcomingCandidates[0];
    }
}

// Ambil informasi Ruang & Nomor Tempat Duduk Ujian (Khusus Siswa Reguler Siakad)
// Menyesuaikan jadwal topik ruang yang sedang berlangsung, atau paling dekat jika belum berlangsung
if (!$is_cbt_only && !empty($siswa['id'])) {
    $targetTopikId = 0;
    if (!empty($activeOngoingUjian['topik_id'])) {
        $targetTopikId = (int)$activeOngoingUjian['topik_id'];
    } elseif (!empty($upcomingUjian['topik_id'])) {
        $targetTopikId = (int)$upcomingUjian['topik_id'];
    } elseif (!empty($nearestTopikId)) {
        $targetTopikId = $nearestTopikId;
    }

    try {
        $stmtSeat = $core_db->prepare("
            SELECT dp.nomor_meja, dp.posisi_baris, dp.posisi_kolom, dp.posisi_sisi, dp.topik_id,
                   r.nama_ruang, r.kode_ruang, r.lokasi
            FROM cbt_denah_peserta dp
            JOIN cbt_ruang r ON dp.ruang_id = r.id
            WHERE dp.siswa_id = :sid OR (dp.nis = :nis AND :nis != '')
            ORDER BY 
                CASE WHEN :target_tid > 0 AND dp.topik_id = :target_tid2 THEN 0 ELSE 1 END ASC,
                dp.id DESC
            LIMIT 1
        ");
        $stmtSeat->execute([
            ':sid' => $siswa['id'],
            ':nis' => (string)($siswa['nis'] ?? ''),
            ':target_tid' => $targetTopikId,
            ':target_tid2' => $targetTopikId
        ]);
        $seatingInfo = $stmtSeat->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

// 5. Riwayat Ujian yang Sudah Selesai
$riwayatUjian = [];
try {
    $stmtRiw = $core_db->prepare("
        SELECT p.*, u.nama_ujian, u.mapel_nama, u.kkm, u.durasi_menit
        FROM cbt_peserta p
        JOIN cbt_ujian u ON p.ujian_id = u.id
        WHERE (p.siswa_id = :sid OR (p.nis = :nis AND :nis != ''))
          AND p.status_pengerjaan = 'Selesai'
        ORDER BY p.waktu_selesai DESC
    ");
    $stmtRiw->execute([
        'sid' => $siswa_id,
        'nis' => (string)($siswa['nis'] ?? '')
    ]);
    $riwayatUjian = $stmtRiw->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

$page_title = 'CBT Ujian Siswa';
include 'header.php';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Header Pro */
    .cbt-header-pro {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0284c7 100%);
        padding: 72px 20px 42px;
        color: white;
        border-bottom-left-radius: 28px;
        border-bottom-right-radius: 28px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
        position: relative;
    }
    .cbt-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .cbt-brand-sub {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #93c5fd;
        margin: 0 0 2px 0;
    }
    .cbt-brand-title {
        font-size: 1.35rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.3px;
    }

    /* Student Profile Card (Clean - Only Name & Class) */
    .cbt-user-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 14px 16px;
        margin: -32px 16px 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 10;
    }
    .cbt-avatar {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        overflow: hidden;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #2563eb;
        font-size: 1.4rem;
        border: 2px solid #bfdbfe;
    }
    .cbt-user-meta {
        flex: 1;
        min-width: 0;
    }
    .cbt-user-name {
        font-weight: 900;
        font-size: 1rem;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }
    .cbt-user-badges {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .cbt-badge-class {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .cbt-badge-kj {
        background: #fdf2f8;
        color: #db2777;
        border: 1px solid #fbcfe8;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .cbt-btn-logout {
        background: #fee2e2;
        color: #dc2626;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        flex-shrink: 0;
        transition: 0.2s;
    }
    .cbt-btn-logout:hover {
        background: #fecaca;
    }

    /* Action bar & Top Tata Tertib Button */
    .cbt-actions-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 0 16px;
        margin-bottom: 16px;
    }
    .btn-tatatertib-top {
        background: #ffffff;
        color: #0f172a;
        border: 1.5px solid #cbd5e1;
        padding: 9px 14px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s;
        flex: 1;
    }
    .btn-tatatertib-top:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .badge-sched-mode {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 6px 10px;
        border-radius: 10px;
        white-space: nowrap;
    }
    .badge-sched-mode.mode-normal {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-sched-mode.mode-susulan {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Banner CBT Info */
    .cbt-banner-pro {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 18px;
        padding: 16px 18px;
        color: white;
        margin: 0 16px 18px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cbt-banner-pro h3 {
        margin: 0 0 4px 0;
        font-size: 0.95rem;
        font-weight: 900;
        color: #38bdf8;
    }
    .cbt-banner-pro p {
        margin: 0;
        font-size: 0.76rem;
        color: #94a3b8;
        line-height: 1.4;
    }

    /* Exam Cards */
    .cbt-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        margin: 0 16px 14px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cbt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
    }
    .btn-ujian {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.88rem;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s;
        border: none;
        box-sizing: border-box;
    }
    .btn-ujian-start {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .btn-ujian-continue {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    .btn-ujian-locked {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        cursor: not-allowed;
    }
    .btn-ujian-expired {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        cursor: not-allowed;
    }

    /* Modal Popup Modern */
    .cbt-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 16px 16px 85px 16px;
    }
    .cbt-modal-box {
        background: #ffffff;
        border-radius: 22px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: calc(85vh - 75px);
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
    }
    @keyframes popInModal {
        0% { transform: scale(0.92); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="cbt-header-pro">
    <div class="cbt-topbar">
        <div>
            <div class="cbt-brand-sub">PORTAL EVALUASI AKADEMIK</div>
            <h1 class="cbt-brand-title">CBT MASMIFHDA</h1>
        </div>
        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding: 0 0 90px 0;">

    <!-- Card Identitas Siswa: HANYA NAMA & KELAS -->
    <div class="cbt-user-card">
        <div class="cbt-avatar">
            <?php if (!empty($siswa['foto'])): ?>
                <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user-graduate\'></i>';">
            <?php else: ?>
                <i class="fa-solid fa-user-graduate"></i>
            <?php endif; ?>
        </div>
        <div class="cbt-user-meta">
            <div class="cbt-user-name">
                <?= htmlspecialchars($siswa['nama']) ?>
            </div>
            <div class="cbt-user-badges">
                <span class="cbt-badge-class">
                    <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($siswa['nama_kelas']) ?>
                </span>
                <?php if (!$is_cbt_only && !empty($seatingInfo)): ?>
                    <span class="cbt-badge-room" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Ruang Ujian">
                        <i class="fa-solid fa-door-open" style="color: #16a34a;"></i> <?= htmlspecialchars($seatingInfo['nama_ruang']) ?>
                    </span>
                    <span class="cbt-badge-seat" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Nomor Tempat Duduk / Meja">
                        <i class="fa-solid fa-chair" style="color: #d97706;"></i> Meja <?= htmlspecialchars(str_pad($seatingInfo['nomor_meja'], 2, '0', STR_PAD_LEFT)) ?>
                    </span>
                <?php endif; ?>
                <?php if ($is_cbt_only): ?>
                    <span class="cbt-badge-kj">Peserta Khusus CBT</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($is_cbt_only): ?>
            <button type="button" onclick="konfirmasiKeluarCbt()" class="cbt-btn-logout" title="Keluar Akun">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        <?php endif; ?>
    </div>

    <!-- Action Bar: Tombol Tata Tertib, Jadwal Ujian & Mode Jadwal -->
    <div class="cbt-actions-bar" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <button type="button" onclick="bukaModalTataTertib()" class="btn-tatatertib-top">
            <i class="fa-solid fa-file-shield" style="color: #2563eb; font-size: 0.95rem;"></i>
            <span>Tata Tertib & Larangan Ujian</span>
        </button>
        <button type="button" onclick="bukaModalJadwalSiswa()" class="btn-tatatertib-top" style="border-color: #cbd5e1; background: #ffffff;">
            <i class="fa-solid fa-calendar-days" style="color: #059669; font-size: 0.95rem;"></i>
            <span>Jadwal Ujian</span>
        </button>
        <?php if ($lockSchedule): ?>
            <span class="badge-sched-mode mode-normal">
                <i class="fa-solid fa-calendar-check"></i> Sesuai Jadwal
            </span>
        <?php else: ?>
            <span class="badge-sched-mode mode-susulan">
                <i class="fa-solid fa-clock-rotate-left"></i> Mode Susulan
            </span>
        <?php endif; ?>
    </div>

    <!-- Banner Status Asesmen Berlangsung -->
    <?php if ($activeOngoingUjian): ?>
        <div class="cbt-banner-pro" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); border: 1px solid rgba(56, 189, 248, 0.3);">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; flex-wrap: wrap;">
                    <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.35); padding: 2px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                        <span style="width: 6px; height: 6px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 6px #22c55e;"></span>
                        ASESMEN BERLANGSUNG
                    </span>
                    <?php if (!empty($activeOngoingUjian['jadwal_slot_label'])): ?>
                        <span style="font-size: 0.7rem; color: #93c5fd; font-weight: 700;">
                            <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($activeOngoingUjian['jadwal_slot_label']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 900; color: #38bdf8; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($activeOngoingUjian['nama_ujian'] ?: $activeOngoingUjian['mapel_nama']) ?>
                </h3>
                <p style="color: #cbd5e1; font-size: 0.76rem;">
                    <?php if (in_array($activeOngoingUjian['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan'])): ?>
                        Sesi sedang berjalan. Silakan lanjutkan pengerjaan di bawah.
                    <?php elseif ($requireAttendance && empty($activeOngoingUjian['has_absen'])): ?>
                        Sesi sedang berlangsung. Silakan lakukan scan absensi ke pengawas ujian terlebih dahulu.
                    <?php else: ?>
                        Sesi telah dibuka sesuai jadwal. Silakan klik "Mulai Kerjakan Ujian" di bawah.
                    <?php endif; ?>
                </p>
            </div>
            <div style="font-size: 2.2rem; opacity: 0.4; margin-left: 14px; color: #38bdf8; flex-shrink: 0;">
                <i class="fa-solid fa-circle-play"></i>
            </div>
        </div>
    <?php elseif ($completedTodayUjian): ?>
        <div class="cbt-banner-pro" style="background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%); border: 1px solid rgba(16, 185, 129, 0.35);">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; flex-wrap: wrap;">
                    <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.35); padding: 2px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-circle-check"></i>
                        ASESMEN HARI INI
                    </span>
                    <span style="font-size: 0.7rem; color: #a7f3d0; font-weight: 700;">
                        Selesai Dikerjakan
                    </span>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 900; color: #6ee7b7; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($completedTodayUjian['nama_ujian'] ?: $completedTodayUjian['mapel_nama']) ?>
                </h3>
                <p style="color: #cbd5e1; font-size: 0.76rem;">
                    Anda telah menyelesaikan asesmen untuk jadwal hari ini. Lembar jawaban telah tersimpan rapi.
                </p>
            </div>
            <div style="font-size: 2.2rem; opacity: 0.4; margin-left: 14px; color: #34d399; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    <?php elseif ($upcomingUjian): ?>
        <div class="cbt-banner-pro" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(245, 158, 11, 0.25);">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 5px;">
                    <span style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); padding: 2px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-hourglass-half" style="font-size: 0.65rem;"></i>
                        JADWAL MENDATANG
                    </span>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 900; color: #fde047; margin: 0 0 3px 0;">
                    <?= htmlspecialchars($upcomingUjian['nama_ujian'] ?: $upcomingUjian['mapel_nama']) ?>
                </h3>
                <p style="color: #94a3b8; font-size: 0.76rem;">
                    <?= htmlspecialchars($upcomingUjian['schedule_notice']) ?>
                </p>
            </div>
            <div style="font-size: 2rem; opacity: 0.3; margin-left: 14px; color: #fbbf24; flex-shrink: 0;">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    <?php else: ?>
        <div class="cbt-banner-pro" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 5px;">
                    <span style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.25); padding: 2px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-circle-pause" style="font-size: 0.65rem;"></i>
                        ASESMEN BERLANGSUNG
                    </span>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 900; color: #94a3b8; margin: 0 0 3px 0;">
                    Tidak Ada
                </h3>
                <p style="color: #64748b; font-size: 0.76rem;">
                    Tidak ada sesi asesmen aktif saat ini sesuai jadwal.
                </p>
            </div>
            <div style="font-size: 2rem; opacity: 0.25; margin-left: 14px; color: #94a3b8; flex-shrink: 0;">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
    <?php endif; ?>

    <!-- Section 1: Daftar Asesmen Terjadwal Hari Ini -->
    <div style="padding: 0 16px 10px; display: flex; align-items: center; justify-content: space-between;">
        <h3 style="margin: 0; font-size: 0.95rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-calendar-day" style="color: #2563eb;"></i> Paket Ujian Hari Ini
        </h3>
        <span style="font-size: 0.72rem; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; font-weight: 800; padding: 2px 9px; border-radius: 20px;">
            <?= count($ujianHariIni) ?> Mapel Terjadwal
        </span>
    </div>

    <?php if (empty($ujianHariIni)): ?>
        <div style="background: white; border-radius: 16px; padding: 25px 20px; text-align: center; border: 1px dashed #cbd5e1; margin: 0 16px 20px;">
            <div style="width: 55px; height: 55px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h4 style="margin: 0 0 4px 0; font-size: 0.95rem; color: #1e293b; font-weight: 800;">Tidak Ada Jadwal Asesmen Hari Ini</h4>
            <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4;">
                Tidak ada paket ujian yang dijadwalkan untuk kelas <b><?= htmlspecialchars($siswa['nama_kelas']) ?></b> pada hari ini.
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($ujianHariIni as $u): ?>
            <?php
            $isMengerjakan = in_array($u['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan']);
            $durasi = (int)($u['durasi_menit'] ?? 90);
            $totalSoal = (int)($u['total_soal'] ?? 0);
            $kkm = (int)($u['kkm'] ?? 75);
            $tokenUjian = $u['token'] ?? '';
            ?>
            <div class="cbt-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-book-bookmark"></i> <?= htmlspecialchars($u['kode_ujian']) ?>
                        </div>
                        <h4 style="margin: 2px 0 0; font-size: 0.98rem; color: #0f172a; font-weight: 800; line-height: 1.35;">
                            <?= htmlspecialchars($u['nama_ujian']) ?>
                        </h4>
                    </div>
                    <?php if ($isMengerjakan): ?>
                        <span style="background: #fffbeb; color: #d97706; font-size: 0.68rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; border: 1px solid #fde68a; white-space: nowrap;">
                            ⏳ Mengerjakan
                        </span>
                    <?php elseif ($u['can_take']): ?>
                        <span style="background: #ecfdf5; color: #059669; font-size: 0.68rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; border: 1px solid #a7f3d0; white-space: nowrap;">
                            🟢 Siap
                        </span>
                    <?php elseif ($u['schedule_state'] === 'early'): ?>
                        <span style="background: #f1f5f9; color: #475569; font-size: 0.68rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; border: 1px solid #cbd5e1; white-space: nowrap;">
                            🔒 Belum Waktu
                        </span>
                    <?php else: ?>
                        <span style="background: #fee2e2; color: #dc2626; font-size: 0.68rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; border: 1px solid #fecaca; white-space: nowrap;">
                            ⛔ Sesi Terlewat
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Info Grid -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; background: #f8fafc; border-radius: 10px; padding: 8px 10px; margin-bottom: 12px; text-align: center; border: 1px solid #f1f5f9;">
                    <div>
                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 600;">Mata Pelajaran</div>
                        <div style="font-size: 0.78rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($u['mapel_nama']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 600;">Alokasi Waktu</div>
                        <div style="font-size: 0.78rem; font-weight: 800; color: #1e293b;"><?= $durasi ?> Menit</div>
                    </div>
                    <div>
                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 600;">Jumlah Soal</div>
                        <div style="font-size: 0.78rem; font-weight: 800; color: #1e293b;"><?= $totalSoal > 0 ? $totalSoal . ' Butir' : 'Tersusun' ?></div>
                    </div>
                </div>

                <?php if (!empty($u['jadwal_slot_label'])): ?>
                    <div style="font-size: 0.72rem; color: #475569; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-clock" style="color: #0284c7;"></i> Sesi Jadwal: <b><?= htmlspecialchars($u['jadwal_slot_label']) ?></b>
                    </div>
                <?php endif; ?>

                <!-- Notice Status Sesi -->
                <?php if ($u['schedule_state'] === 'expired'): ?>
                    <div style="font-size: 0.73rem; color: #b91c1c; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; background: #fef2f2; padding: 7px 10px; border-radius: 8px; border: 1px solid #fee2e2;">
                        <i class="fa-solid fa-clock-rotate-left"></i> <?= htmlspecialchars($u['schedule_notice'] ?: 'Waktu sesi ujian ini telah berakhir.') ?>
                    </div>
                <?php elseif ($u['schedule_state'] === 'early'): ?>
                    <div style="font-size: 0.73rem; color: #1e40af; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; background: #eff6ff; padding: 7px 10px; border-radius: 8px; border: 1px solid #dbeafe;">
                        <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($u['schedule_notice'] ?: 'Sesi ujian belum dibuka.') ?>
                    </div>
                <?php endif; ?>

                <!-- Action Button -->
                <?php if ($isMengerjakan): ?>
                    <a href="/cbt/kerjakan?ujian_id=<?= (int)$u['id'] ?>" class="btn-ujian btn-ujian-continue">
                        <i class="fa-solid fa-play"></i> Lanjutkan Pengerjaan Ujian
                    </a>
                <?php elseif ($u['can_take']): ?>
                    <?php if ($requireAttendance && empty($u['has_absen'])): ?>
                        <div style="font-size: 0.73rem; color: #b91c1c; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; background: #fff1f2; padding: 7px 10px; border-radius: 8px; border: 1px solid #fecdd3;">
                            <i class="fa-solid fa-triangle-exclamation" style="color: #e11d48;"></i> Anda belum absen. Silakan scan barcode kartu Anda ke pengawas ujian.
                        </div>
                        <button type="button" class="btn-ujian btn-ujian-disabled" style="background: #94a3b8; color: #ffffff; cursor: not-allowed; border: none; box-shadow: none;" disabled title="Silakan lakukan absensi terlebih dahulu ke pengawas ruang">
                            <i class="fa-solid fa-qrcode"></i> Silakan Absensi Terlebih Dahulu
                        </button>
                    <?php else: ?>
                        <?php if ($requireAttendance && !empty($u['absen_info'])): ?>
                            <div style="font-size: 0.72rem; color: #059669; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; background: #ecfdf5; padding: 6px 10px; border-radius: 8px; border: 1px solid #a7f3d0;">
                                <i class="fa-solid fa-circle-check"></i> Absensi Terverifikasi (Pukul <?= date('H:i', strtotime($u['absen_info']['waktu_scan'])) ?> WIB)
                            </div>
                        <?php endif; ?>
                        <button type="button" class="btn-ujian btn-ujian-start" onclick="konfirmasiMulaiUjian(<?= (int)$u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nama_ujian'])) ?>', '<?= htmlspecialchars(addslashes($u['mapel_nama'])) ?>', <?= $durasi ?>, <?= $totalSoal ?>, '<?= htmlspecialchars(addslashes($tokenUjian)) ?>', <?= $kkm ?>)">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Mulai Kerjakan Ujian
                        </button>
                    <?php endif; ?>
                <?php elseif ($u['schedule_state'] === 'early'): ?>
                    <div class="btn-ujian btn-ujian-locked">
                        <i class="fa-solid fa-lock"></i> <?= htmlspecialchars($u['schedule_notice'] ?: 'Belum Masuk Waktu Ujian') ?>
                    </div>
                <?php else: ?>
                    <div class="btn-ujian btn-ujian-expired">
                        <i class="fa-solid fa-ban"></i> Sesi Ujian Telah Berakhir
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Section 2: Mata Pelajaran Lain (Disembunyikan agar tampilan APK siswa bersih dan fokus pada jadwal hari ini) -->

    <!-- Riwayat Hasil Ujian CBT -->
    <?php if (!empty($riwayatUjian)): ?>
        <div style="padding: 10px 16px 8px; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; font-size: 0.95rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #10b981;"></i> Riwayat Hasil Ujian CBT
            </h3>
            <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;"><?= count($riwayatUjian) ?> Selesai</span>
        </div>

        <div style="padding: 0 16px;">
            <?php foreach ($riwayatUjian as $r): ?>
                <?php
                $isTuntas = ((float)$r['nilai_akhir'] >= (float)($r['kkm'] ?? 75));
                ?>
                <div style="background: white; border-radius: 14px; padding: 12px 14px; margin-bottom: 10px; border-left: 4px solid <?= $isTuntas ? '#10b981' : '#ef4444' ?>; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                    <div style="min-width: 0; flex: 1; padding-right: 10px;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($r['nama_ujian']) ?></div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px;">
                            <span><?= htmlspecialchars($r['mapel_nama']) ?></span> • 
                            <span><?= date('d M Y, H:i', strtotime($r['waktu_selesai'])) ?></span>
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <?php if ($showScoreToStudent): ?>
                            <div style="font-size: 1.15rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>; line-height: 1;">
                                <?= number_format((float)$r['nilai_akhir'], 1) ?>
                            </div>
                            <span style="font-size: 0.65rem; font-weight: 800; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                                <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                            </span>
                        <?php else: ?>
                            <div style="font-size: 0.85rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>;">
                                <?= $isTuntas ? '✓ TUNTAS' : '✕ REMEDIAL' ?>
                            </div>
                            <span style="font-size: 0.65rem; color: #64748b; font-weight: 700;">
                                KKM: <?= (int)($r['kkm'] ?? 75) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- MODAL: INFORMASI JADWAL UJIAN SISWA -->
    <div id="modalJadwalSiswa" class="cbt-modal-backdrop" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px 16px 85px 16px;">
        <div class="cbt-modal-box" style="background: white; border-radius: 20px; max-width: 500px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; animation: slideUp 0.25s ease-out; max-height: calc(85vh - 75px); margin-bottom: 40px; display: flex; flex-direction: column;">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(37, 99, 235, 0.2); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.98rem; font-weight: 900;">Jadwal Pelaksanaan Ujian</h3>
                        <p style="margin: 2px 0 0; font-size: 0.72rem; color: #94a3b8;">
                            <?= htmlspecialchars($nearestTopik['nama_topik'] ?? 'Asesmen CBT') ?> • Kelas <?= htmlspecialchars($siswa['nama_kelas']) ?>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalJadwalSiswa()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>

            <!-- Body -->
            <div style="padding: 16px 20px; overflow-y: auto; flex: 1;">
                <?php if (empty($jadwalSiswaList)): ?>
                    <div style="text-align: center; padding: 30px 10px; color: #94a3b8;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <div style="font-weight: 800; font-size: 0.92rem; color: #475569;">Belum Ada Jadwal Khusus</div>
                        <div style="font-size: 0.76rem; margin-top: 4px;">Jadwal ujian untuk kelas Anda akan muncul begitu dirilis oleh panitia.</div>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php 
                        $curGroupDate = '';
                        foreach ($jadwalSiswaList as $js): 
                            $tglStr = !empty($js['tanggal']) ? date('d M Y', strtotime($js['tanggal'])) : ($js['hari'] ?? 'Hari Ujian');
                            $isToday = (!empty($js['tanggal']) && $js['tanggal'] === $todayDate) || ($js['hari'] === $curHari);
                        ?>
                            <?php if ($tglStr !== $curGroupDate): $curGroupDate = $tglStr; ?>
                                <div style="font-size: 0.74rem; font-weight: 800; color: #0369a1; background: #f0f9ff; border: 1px solid #bae6fd; padding: 5px 12px; border-radius: 8px; margin-top: 6px; display: flex; align-items: center; justify-content: space-between;">
                                    <span><i class="fa-solid fa-calendar-day"></i> <?= htmlspecialchars($tglStr) ?></span>
                                    <?php if ($isToday): ?>
                                        <span style="background: #0284c7; color: white; font-size: 0.65rem; padding: 1px 6px; border-radius: 4px;">HARI INI</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 11px 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                    <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">
                                        <?= htmlspecialchars($js['mapel_nama']) ?>
                                    </div>
                                    <span style="font-size: 0.68rem; font-weight: 800; background: #eff6ff; color: #2563eb; padding: 2px 7px; border-radius: 6px; border: 1px solid #bfdbfe; font-family: monospace;">
                                        <?= htmlspecialchars($js['label_sesi'] ?: ($js['jam_slot'] ?: 'Sesi Ujian')) ?>
                                    </span>
                                </div>
                                <div style="display: flex; gap: 10px; font-size: 0.72rem; color: #64748b; font-weight: 600; flex-wrap: wrap;">
                                    <?php if (!empty($js['jam_mulai'])): ?>
                                        <span><i class="fa-solid fa-clock"></i> <?= substr($js['jam_mulai'], 0, 5) ?> - <?= substr($js['jam_selesai'], 0, 5) ?> WIB</span>
                                    <?php endif; ?>
                                    <span><i class="fa-solid fa-stopwatch"></i> <?= (int)($js['durasi_menit'] ?: 60) ?> Menit</span>
                                    <span><i class="fa-solid fa-bullseye"></i> KKM: <?= (int)($js['kkm'] ?: 75) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right;">
                <button type="button" onclick="tutupModalJadwalSiswa()" style="background: #2563eb; color: white; border: none; border-radius: 10px; font-weight: 800; font-size: 0.82rem; padding: 8px 18px; cursor: pointer;">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 1: TATA TERTIB & LARANGAN UJIAN CBT -->
    <div id="modalTataTertib" class="cbt-modal-backdrop">
        <div class="cbt-modal-box">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 18px 20px; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-shield" style="font-size: 1.2rem; color: #93c5fd;"></i>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800;">Tata Tertib Ujian CBT</h3>
                </div>
                <button type="button" onclick="tutupModalTataTertib()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 0.9rem;">
                    ✕
                </button>
            </div>
            <div style="padding: 18px 20px; overflow-y: auto; font-size: 0.82rem; color: #334155; line-height: 1.6;">
                <div style="margin-bottom: 14px;">
                    <b style="color: #1e3a8a; display: block; margin-bottom: 4px; font-size: 0.88rem;">1. Ketentuan Umum & Kesiapan</b>
                    <ul style="margin: 0; padding-left: 18px;">
                        <li>Peserta wajib hadir dan mengaktifkan lembar ujian tepat waktu sesuai jadwal sesi.</li>
                        <li>Pastikan daya baterai gawai Anda minimal 50% dan memiliki koneksi internet yang lancar.</li>
                    </ul>
                </div>
                <div style="margin-bottom: 14px;">
                    <b style="color: #dc2626; display: block; margin-bottom: 4px; font-size: 0.88rem;">2. Larangan Keras & Sistem Anti-Cheat</b>
                    <ul style="margin: 0; padding-left: 18px; color: #991b1b;">
                        <li><b>Dilarang keluar aplikasi atau berpindah tab browser</b> selama ujian berlangsung. Toleransi sistem maksimal <b><?= $maxTabViolations ?> kali</b> sebelum lembar jawaban Anda <b>dikumpulkan paksa (Auto-Submit)</b>!</li>
                        <li>Dilarang menekan tombol kembali (back button). Menekan tombol kembali akan memicu alarm bahaya.</li>
                        <li>Dilarang mengambil tangkapan layar (screenshot), merekam layar, atau menyalin (copy-paste) soal ujian.</li>
                        <li>Dilarang membuka mesin pencari, bantuan AI, ataupun membuka catatan dalam bentuk apapun.</li>
                    </ul>
                </div>
                <div style="margin-bottom: 14px;">
                    <b style="color: #1e3a8a; display: block; margin-bottom: 4px; font-size: 0.88rem;">3. Mekanisme Pengerjaan & Waktu</b>
                    <ul style="margin: 0; padding-left: 18px;">
                        <li>Jawaban tersimpan otomatis setiap kali opsi dipilih.</li>
                        <li>Hitungan waktu berjalan mundur di server. Jika waktu habis, jawaban otomatis tersimpan.</li>
                    </ul>
                </div>
                <div>
                    <b style="color: #1e3a8a; display: block; margin-bottom: 4px; font-size: 0.88rem;">4. Kelulusan & Remedial</b>
                    <p style="margin: 0;">
                        Peserta yang memperoleh perolehan nilai di bawah standar KKM wajib mengikuti program remedial sesuai instruksi guru mata pelajaran.
                    </p>
                </div>
            </div>
            <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; text-align: right;">
                <button type="button" onclick="tutupModalTataTertib()" style="padding: 9px 20px; border-radius: 10px; background: #2563eb; color: white; border: none; font-weight: 800; font-size: 0.82rem; cursor: pointer;">
                    Saya Paham & Patuh
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: KONFIRMASI MULAI UJIAN + TOKEN INPUT -->
    <div id="modalStartExam" class="cbt-modal-backdrop">
        <div class="cbt-modal-box">
            <!-- Modal Header -->
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 20px 20px 16px; text-align: center; color: #ffffff;">
                <div style="width: 52px; height: 52px; margin: 0 auto 10px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 2px solid rgba(255, 255, 255, 0.3);">
                    🚀
                </div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 900; letter-spacing: -0.3px;">Konfirmasi Pelaksanaan Ujian</h3>
                <p style="margin: 3px 0 0; font-size: 0.76rem; color: #bfdbfe;">Masukkan token ujian untuk membuka lembar soal</p>
            </div>

            <!-- Modal Body -->
            <div style="padding: 16px 20px; overflow-y: auto;">
                <!-- Info Paket Ujian -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;">
                    <div id="modalExamTitle" style="font-size: 0.95rem; font-weight: 900; color: #0f172a; line-height: 1.35; margin-bottom: 6px;">-</div>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap; font-size: 0.72rem; font-weight: 700;">
                        <span id="modalExamMapel" style="background: #eff6ff; color: #2563eb; padding: 3px 8px; border-radius: 6px; border: 1px solid #dbeafe;">-</span>
                        <span id="modalExamDurasi" style="background: #f0fdf4; color: #16a34a; padding: 3px 8px; border-radius: 6px; border: 1px solid #dcfce7;">-</span>
                        <span id="modalExamSoal" style="background: #faf5ff; color: #7c3aed; padding: 3px 8px; border-radius: 6px; border: 1px solid #f3e8ff;">-</span>
                    </div>
                </div>

                <!-- Box Token Ujian Resmi -->
                <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border-radius: 16px; padding: 14px 16px; margin-bottom: 14px; text-align: center; box-shadow: 0 4px 15px rgba(15, 23, 42, 0.18); border: 1.5px solid #334155;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">KODE TOKEN UJIAN RESMI</div>
                    <div id="modalExamToken" style="font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; font-size: 1.95rem; font-weight: 900; letter-spacing: 6px; color: #38bdf8; margin: 6px 0 2px; text-shadow: 0 2px 10px rgba(56, 189, 248, 0.35); font-variant-numeric: slashed-zero;">
                        ------
                    </div>
                    <div id="tokenCharHint" style="display: none; font-size: 0.72rem; font-weight: 800; color: #facc15; margin: 4px auto 2px; background: rgba(234, 179, 8, 0.15); padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(234, 179, 8, 0.3);">
                        <i class="fa-solid fa-circle-info"></i> Memakai Huruf <b>O</b> (Bukan angka Nol 0)
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">Token ini berlaku khusus untuk mata pelajaran ini.</div>
                </div>

                <!-- Form Input Token Siswa -->
                <div style="margin-bottom: 12px;">
                    <label for="inputTokenUjian" style="display: block; font-size: 0.78rem; font-weight: 800; color: #334155; margin-bottom: 6px;">
                        Ketikkan Kode Token Ujian di Bawah:
                    </label>
                    <input type="text" id="inputTokenUjian" placeholder="Contoh: MAT267" maxlength="10" autocomplete="off" style="width: 100%; box-sizing: border-box; padding: 13px; border-radius: 14px; border: 2px solid #cbd5e1; text-align: center; text-transform: uppercase; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 1.35rem; font-weight: 900; letter-spacing: 6px; color: #0f172a; outline: none; transition: border-color 0.2s; font-variant-numeric: slashed-zero;">
                    <div id="tokenErrorMsg" style="display: none; color: #dc2626; font-size: 0.72rem; font-weight: 800; margin-top: 4px; text-align: center;">
                        <i class="fa-solid fa-circle-exclamation"></i> Token tidak cocok! Silakan cek kembali.
                    </div>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 8px 10px; font-size: 0.72rem; color: #92400e; line-height: 1.4; display: flex; gap: 6px; align-items: flex-start;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.85rem; margin-top: 1px; flex-shrink: 0; color: #d97706;"></i>
                    <div>
                        <b>Peringatan:</b> Waktu akan langsung dihitung mundur setelah Anda menekan tombol Mulai.
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div style="padding: 12px 20px 18px; display: flex; gap: 10px;">
                <button type="button" onclick="tutupModalMulai()" style="flex: 1; padding: 11px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 800; font-size: 0.84rem; cursor: pointer;">
                    Batal
                </button>
                <button type="button" onclick="verifikasiDanMulaiUjian()" style="flex: 1.4; padding: 11px; border-radius: 10px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 900; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
                    <span>Mulai Ujian</span> <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
let curUjianId = 0;
let curExpectedToken = '';

function bukaModalJadwalSiswa() {
    document.getElementById('modalJadwalSiswa').style.display = 'flex';
}
function tutupModalJadwalSiswa() {
    document.getElementById('modalJadwalSiswa').style.display = 'none';
}

function bukaModalTataTertib() {
    document.getElementById('modalTataTertib').style.display = 'flex';
}
function tutupModalTataTertib() {
    document.getElementById('modalTataTertib').style.display = 'none';
}

function konfirmasiMulaiUjian(ujianId, namaUjian, mapelNama, durasi, totalSoal, tokenResmi, kkm) {
    curUjianId = ujianId;
    curExpectedToken = (tokenResmi || '').trim().toUpperCase();

    document.getElementById('modalExamTitle').textContent = namaUjian;
    document.getElementById('modalExamMapel').textContent = mapelNama || 'CBT Online';
    document.getElementById('modalExamDurasi').textContent = durasi + ' Menit';
    document.getElementById('modalExamSoal').textContent = (totalSoal > 0 ? totalSoal + ' Soal' : '50 Soal');
    document.getElementById('modalExamToken').textContent = curExpectedToken || '------';
    
    // Hint cerdas untuk membedakan Huruf O vs Angka 0
    const hintEl = document.getElementById('tokenCharHint');
    if (hintEl) {
        if (curExpectedToken.indexOf('O') !== -1) {
            hintEl.innerHTML = '<i class="fa-solid fa-circle-info"></i> Memakai <b>HURUF O</b> (Bukan angka nol 0)';
            hintEl.style.display = 'inline-block';
        } else if (curExpectedToken.indexOf('0') !== -1) {
            hintEl.innerHTML = '<i class="fa-solid fa-circle-info"></i> Memakai <b>ANGKA 0 (NOL)</b>';
            hintEl.style.display = 'inline-block';
        } else {
            hintEl.style.display = 'none';
        }
    }

    const inp = document.getElementById('inputTokenUjian');
    inp.value = '';
    inp.style.borderColor = '#cbd5e1';
    document.getElementById('tokenErrorMsg').style.display = 'none';

    document.getElementById('modalStartExam').style.display = 'flex';
    setTimeout(() => inp.focus(), 250);
}

function tutupModalMulai() {
    document.getElementById('modalStartExam').style.display = 'none';
}

function verifikasiDanMulaiUjian() {
    const inp = document.getElementById('inputTokenUjian');
    const entered = inp.value.trim().toUpperCase();

    if (!entered) {
        inp.style.borderColor = '#dc2626';
        inp.focus();
        document.getElementById('tokenErrorMsg').textContent = 'Ketikkan token ujian terlebih dahulu!';
        document.getElementById('tokenErrorMsg').style.display = 'block';
        return;
    }

    // Toleransi Cerdas O dan 0: Siswa tidak akan gagal hanya karena salah mengetik huruf O / angka 0
    const normEntered = entered.replace(/0/g, 'O');
    const normExpected = curExpectedToken.replace(/0/g, 'O');

    if (normEntered !== normExpected) {
        inp.style.borderColor = '#dc2626';
        inp.focus();
        document.getElementById('tokenErrorMsg').textContent = 'Token tidak cocok! Silakan cek kembali.';
        document.getElementById('tokenErrorMsg').style.display = 'block';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Token Tidak Sesuai!',
                text: 'Kode token yang Anda masukkan salah. Periksa kembali kode token resmi di atas.',
                confirmButtonColor: '#ef4444'
            });
        }
        return;
    }

    // Token cocok -> Redirect ke kerjakan ujian dengan token resmi yang valid!
    window.location.href = '/cbt/kerjakan?ujian_id=' + curUjianId + '&token=' + encodeURIComponent(curExpectedToken);
}

// Enter key submit on input token
document.getElementById('inputTokenUjian')?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        verifikasiDanMulaiUjian();
    }
});

// Heartbeat ping to Developer Monitor setiap 45 detik agar status siswa tetap Online
setInterval(function() {
    fetch('ping_tracker.php').catch(function(){});
}, 45000);
</script>

<?php include 'footer.php'; ?>
