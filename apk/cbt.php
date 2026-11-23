<?php
// GHTech Analytics Tracking for CBT Activity
$_ghtech_key = "4a0fef55431a38a8ee6317c512178502";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}

require_once 'koneksi.php';
// require_once __DIR__ . '/apk_version_guard.php';
// check_apk_version_guard();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

$host = $_SERVER['HTTP_HOST'] ?? '';
$is_online_server = (strpos($host, 'apk.masmifhda.sch.id') !== false);
$is_standalone_env = !$is_online_server && (
    (defined('CBT_STANDALONE_MODE') && CBT_STANDALONE_MODE)
    || file_exists(__DIR__ . '/../.cbt_standalone')
    || (strpos($host, '192.168.') === 0 || strpos($host, '10.') === 0 || strpos($host, '127.0.0.1') === 0 || strpos($host, 'localhost') === 0)
    || !file_exists(__DIR__ . '/guru_dashboard.php')
);
$activeTab = $_GET['tab'] ?? 'beranda';

// Proteksi: Siswa reguler hanya diizinkan ujian offline di Laboratorium Komputer Madrasah
$is_kelas_jauh = $is_cbt_only || (isset($siswa['nama_kelas']) && stripos($siswa['nama_kelas'], 'jauh') !== false);
if ($is_online_server && !$is_kelas_jauh) {
    header("Location: aplikasi_siswa.php?msg=offline_only");
    exit;
}

// Self Check-in / Verifikasi Presensi Mandiri
if (isset($_GET['self_checkin'])) {
    $nowStr = date('Y-m-d H:i:s');
    $todayStr = date('Y-m-d');
    $nisSw = $is_cbt_only ? ($cbtStudent['no_peserta'] ?? '') : ($siswa['nis'] ?? '');
    $namaSw = $is_cbt_only ? ($cbtStudent['nama_siswa'] ?? '') : ($siswa['nama'] ?? '');
    $klsSw = $is_cbt_only ? ($cbtStudent['kelas_nama'] ?? '') : ($siswa['nama_kelas'] ?? '');
    $swId = $is_cbt_only ? null : $siswa_id;
    try {
        $stmtChkExist = $core_db->prepare("SELECT id FROM cbt_absensi WHERE tanggal = ? AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?)) LIMIT 1");
        $stmtChkExist->execute([$todayStr, $nisSw, $swId ?: -1]);
        $existingAid = $stmtChkExist->fetchColumn();
        if ($existingAid) {
            $core_db->prepare("UPDATE cbt_absensi SET waktu_scan = ?, status = 'Hadir', metode_scan = 'SELF_CHECKIN' WHERE id = ?")->execute([$nowStr, $existingAid]);
        } else {
            $core_db->prepare("
                INSERT INTO cbt_absensi (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama)
                VALUES (?, ?, ?, ?, ?, ?, 'Semua Mapel', 'Sesi 1', ?, 'Hadir', 'SELF_CHECKIN', 'Mandiri / Proktor', 'Ruang Ujian')
            ")->execute([$todayStr, $swId, $nisSw, $namaSw, $klsSw, $is_cbt_only ? 'khusus' : 'reguler', $nowStr]);
        }
    } catch (\Throwable $e) {}
    header("Location: cbt.php");
    exit;
}

// Inisialisasi info tempat duduk (akan ditentukan setelah evaluasi topik aktif/terdekat)
$seatingInfo = null;

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

// 3. Ambil Info Topik Terdekat yang Aktif / Sedang Berlangsung
$nearestTopik = null;
try {
    $stmtTp = $core_db->query("
        SELECT t.*, 
               COALESCE(g.grid_count, 0) as grid_count,
               COALESCE(g.today_count, 0) as today_count
        FROM bs_topik t
        LEFT JOIN (
            SELECT topik_id, 
                   COUNT(*) as grid_count,
                   SUM(CASE WHEN tanggal = CURDATE() THEN 1 ELSE 0 END) as today_count
            FROM cbt_jadwal_grid 
            GROUP BY topik_id
        ) g ON t.id = g.topik_id
        WHERE t.status = 'Aktif' 
        ORDER BY 
            CASE 
                -- 0: Topik yang memiliki jadwal ujian hari ini di cbt_jadwal_grid
                WHEN COALESCE(g.today_count, 0) > 0 THEN 0
                -- 1: Topik yang saat ini sedang berlangsung rentang tanggalnya (tgl_mulai <= hari ini <= tgl_selesai)
                WHEN (t.tgl_mulai IS NOT NULL AND t.tgl_mulai != '' AND t.tgl_mulai <= CURDATE() 
                      AND (t.tgl_selesai IS NULL OR t.tgl_selesai = '' OR t.tgl_selesai >= CURDATE())) THEN 1
                -- 2: Topik yang memiliki entri grid jadwal tersimpan
                WHEN COALESCE(g.grid_count, 0) > 0 THEN 2
                -- 3: Topik mendatang terdekat (tgl_mulai >= CURDATE)
                WHEN (t.tgl_mulai IS NOT NULL AND t.tgl_mulai != '' AND t.tgl_mulai >= CURDATE()) THEN 3
                ELSE 4
            END ASC,
            ABS(DATEDIFF(COALESCE(NULLIF(t.tgl_mulai, ''), CURDATE()), CURDATE())) ASC,
            t.id DESC
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
                    WHEN (tgl_mulai IS NOT NULL AND tgl_mulai != '' AND tgl_mulai <= CURDATE() 
                          AND (tgl_selesai IS NULL OR tgl_selesai = '' OR tgl_selesai >= CURDATE())) THEN 0
                    WHEN (tgl_mulai IS NOT NULL AND tgl_mulai != '' AND tgl_mulai >= CURDATE()) THEN 1 
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
        $allowedKelas[] = 'Kelas XII';
        $allowedKelas[] = 'XII F.1';
        $allowedKelas[] = 'XII F.2';
        $allowedKelas[] = 'XIIF.1';
        $allowedKelas[] = 'XIIF.2';
        $allowedKelas[] = 'XII - Kelas Jauh';
    } elseif ($short_tingkat === 'XI') {
        $allowedKelas[] = 'Kelas 11';
        $allowedKelas[] = '11';
        $allowedKelas[] = 'Kelas XI';
        $allowedKelas[] = 'XI F.1';
        $allowedKelas[] = 'XI F.2';
        $allowedKelas[] = 'XIF.1';
        $allowedKelas[] = 'XIF.2';
        $allowedKelas[] = 'XI - Kelas Jauh';
    } else {
        $allowedKelas[] = 'Kelas 10';
        $allowedKelas[] = '10';
        $allowedKelas[] = 'Kelas X';
        $allowedKelas[] = 'X E.1';
        $allowedKelas[] = 'X E.2';
        $allowedKelas[] = 'XE.1';
        $allowedKelas[] = 'XE.2';
        $allowedKelas[] = 'X E';
        $allowedKelas[] = 'XE';
        $allowedKelas[] = 'X - Kelas Jauh';
    }

    try {
        $numTingkat = ($short_tingkat === 'XII') ? 12 : (($short_tingkat === 'XI') ? 11 : 10);
        $stmtK = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE tingkat = ? OR tingkat = ?");
        $stmtK->execute([$numTingkat, $short_tingkat]);
        $rombelNames = $stmtK->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($rombelNames)) {
            $allowedKelas = array_merge($allowedKelas, $rombelNames);
        }
    } catch (\Exception $e) {}

    // Ambil juga kelas-kelas unik dari cbt_ujian & cbt_jadwal_grid yang beririsan dengan tingkat ini
    try {
        $stmtAllU = $core_db->query("SELECT DISTINCT kelas_nama FROM cbt_ujian WHERE kelas_nama IS NOT NULL AND kelas_nama != '' UNION SELECT DISTINCT kelas_nama FROM cbt_jadwal_grid WHERE kelas_nama IS NOT NULL AND kelas_nama != ''");
        if ($stmtAllU) {
            $allUKelas = $stmtAllU->fetchAll(PDO::FETCH_COLUMN);
            foreach ($allUKelas as $uk) {
                if ($short_tingkat === 'XII' && (stripos($uk, 'XII') !== false || stripos($uk, '12') !== false)) {
                    $allowedKelas[] = $uk;
                } elseif ($short_tingkat === 'XI' && ((stripos($uk, 'XI') !== false && stripos($uk, 'XII') === false) || stripos($uk, '11') !== false)) {
                    $allowedKelas[] = $uk;
                } elseif ($short_tingkat === 'X' && stripos($uk, 'XI') === false && stripos($uk, 'XII') === false && (stripos($uk, 'X') !== false || stripos($uk, '10') !== false)) {
                    $allowedKelas[] = $uk;
                }
            }
        }
    } catch (\Exception $e) {}

    $allowedKelas = array_values(array_unique(array_filter($allowedKelas)));
    $inPlaceholders = implode(',', array_fill(0, count($allowedKelas), '?'));

    // Khusus Siswa CBT Kelas Jauh (is_cbt_only):
    // Jangan match ke p.siswa_id karena ID cbt_peserta_khusus BUKAN id tabel siswa reguler!
    // Matching siswa kelas jauh menggunakan no_peserta, nis, atau nama_siswa.
    if ($is_cbt_only) {
        $noPesertaKj = (string)($cbtStudent['no_peserta'] ?? '');
        $nisKj = (string)($cbtStudent['nis'] ?? '');
        $namaKj = (string)($cbtStudent['nama_siswa'] ?? '');
        $joinPesertaSql = "(p.siswa_id IS NULL AND (p.nis = ? OR (p.nis = ? AND ? != '') OR p.nama_siswa = ?))";
        $joinPesertaParams = [$noPesertaKj, $nisKj, $nisKj, $namaKj];
    } else {
        $joinPesertaSql = "(p.siswa_id = ? OR (p.nis = ? AND ? != ''))";
        $joinPesertaParams = [$siswa_id, (string)($siswa['nis'] ?? ''), (string)($siswa['nis'] ?? '')];
    }

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
        LEFT JOIN cbt_peserta p ON p.ujian_id = u.id AND {$joinPesertaSql}
        LEFT JOIN bs_topik t ON u.topik_id = t.id
        WHERE u.status = 'Aktif'
          AND (u.topik_id IS NULL OR u.topik_id = 0 OR (t.id IS NOT NULL AND t.status = 'Aktif'))
          AND (u.kelas_nama IS NULL OR u.kelas_nama = '' OR u.kelas_nama IN ($inPlaceholders))
        ORDER BY 
            CASE WHEN u.topik_id = {$nearestTopikId} THEN 0 ELSE 1 END ASC,
            CASE 
                WHEN u.kelas_nama = ? THEN 0
                WHEN u.kelas_nama IN (?, ?, 'Semua Kelas', '') OR u.kelas_nama IS NULL THEN 1
                ELSE 2
            END ASC,
            (total_soal > 0) DESC, 
            u.id DESC
    ";
    // Urutan parameter PDO HARUS persis sesuai posisi '?' di dalam query SQL:
    // 1. $joinPesertaParams (di LEFT JOIN cbt_peserta)
    // 2. $allowedKelas (di WHERE u.kelas_nama IN ($inPlaceholders))
    // 3. ORDER BY placeholders (u.kelas_nama = ?, u.kelas_nama IN (?, ?))
    $params = array_merge(
        $joinPesertaParams,
        $allowedKelas,
        [
            (string)($siswa['nama_kelas'] ?? ''),
            (string)$tingkat_kelas,
            (string)$short_tingkat
        ]
    );
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

        $mKey = $mKeyNorm;
        if (isset($seenMapelKeys[$mKey])) {
            continue; // Lewati duplikat mapel yang sama
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
        WHERE (
            g.kelas_nama = :kelas 
            OR g.kelas_nama = :tingkat 
            OR g.kelas_nama = 'Semua Kelas'
            OR g.kelas_nama IS NULL 
            OR g.kelas_nama = ''
            OR :kelas LIKE CONCAT('%', g.kelas_nama, '%')
            OR (
                (:short_tingkat = 'XII' AND (g.kelas_nama LIKE 'XII%' OR g.kelas_nama LIKE 'Kelas 12%' OR g.kelas_nama LIKE 'Kelas XII%'))
                OR (:short_tingkat = 'XI' AND ((g.kelas_nama LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 11%' OR g.kelas_nama LIKE 'Kelas XI%'))
                OR (:short_tingkat = 'X' AND ((g.kelas_nama LIKE 'X%' AND g.kelas_nama NOT LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 10%' OR g.kelas_nama LIKE 'Kelas X%'))
            )
        )
    ";
    if ($nearestTopikId > 0) {
        $sqlJadwalSiswa .= " AND (g.topik_id = :topik_id OR g.topik_id IS NULL OR g.topik_id = 0)";
    }
    $sqlJadwalSiswa .= " ORDER BY COALESCE(g.tanggal, '2099-12-31') ASC, FIELD(g.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') ASC, g.jam_slot ASC, g.id ASC";

    $stmtJadwalSiswa = $core_db->prepare($sqlJadwalSiswa);
    $paramsJadwal = [
        ':kelas' => $siswa['nama_kelas'],
        ':tingkat' => $tingkat_kelas,
        ':short_tingkat' => $short_tingkat
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

        // Jika slot istirahat atau nama mapel kosong, jangan jadikan sebagai jadwal ujian
        if (($matchedSlot && !empty($matchedSlot['is_istirahat'])) || stripos($rg['mapel_nama'] ?? '', 'istirahat') !== false || empty(trim($rg['mapel_nama'] ?? ''))) {
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

// Ambil Data Absensi Siswa Hari Ini jika Wajib Absensi Aktif (Disimpan Per-Mata Pelajaran & Sesi)
$absenHariIniMap = [];
$absenHariIniList = [];
if ($requireAttendance) {
    try {
        if ($is_cbt_only) {
            $stmtAbs = $core_db->prepare("
                SELECT ujian_id, mapel_nama, status, waktu_scan 
                FROM cbt_absensi 
                WHERE (nis = :no_peserta OR (nis = :nis AND :nis != '') OR nama_siswa = :nama_kj) 
                  AND (tanggal = :today OR DATE(waktu_scan) = :today2)
                  AND status IN ('Hadir', 'Terlambat')
            ");
            $stmtAbs->execute([
                ':no_peserta' => (string)($cbtStudent['no_peserta'] ?? ''),
                ':nis' => (string)($cbtStudent['nis'] ?? ''),
                ':nama_kj' => (string)($cbtStudent['nama_siswa'] ?? ''),
                ':today' => $todayDate,
                ':today2' => $todayDate
            ]);
        } else {
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
        }
        while ($abRow = $stmtAbs->fetch(PDO::FETCH_ASSOC)) {
            $absenHariIniList[] = $abRow;
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
            $klsDisplay = ($u['kelas_nama'] === $siswa['nama_kelas'] || in_array($u['kelas_nama'], [$tingkat_kelas, $short_tingkat])) 
                ? $u['kelas_nama'] 
                : ($siswa['nama_kelas'] ?? $tingkat_kelas);
            $u['nama_ujian'] = $curTopikName . ' - ' . $u['mapel_nama'] . ' (' . $klsDisplay . ')';
        }
    }
    // 0a. Normalisasi Kode Ujian jika ada tertukar prefix
    if (stripos($u['mapel_nama'], 'Matematika') !== false && strpos($u['kode_ujian'], 'UJN-BAH') === 0) {
        $newCode = 'UJN-MAT-' . substr($u['kode_ujian'], 8);
        try { $core_db->prepare("UPDATE cbt_ujian SET kode_ujian = ? WHERE id = ?")->execute([$newCode, $u['id']]); } catch (\Throwable $e) {}
        $u['kode_ujian'] = $newCode;
    } elseif (stripos($u['mapel_nama'], 'Arab') !== false && strpos($u['kode_ujian'], 'UJN-MAT') === 0) {
        $newCode = 'UJN-ARA-' . substr($u['kode_ujian'], 8);
        try { $core_db->prepare("UPDATE cbt_ujian SET kode_ujian = ? WHERE id = ?")->execute([$newCode, $u['id']]); } catch (\Throwable $e) {}
        $u['kode_ujian'] = $newCode;
    }

    // 0b. Auto-Sync Butir Soal: Jamin butir soal cbt_soal selaras 100% dengan Bank Soal (bs_soal)
    try {
        $getLevel = function($str) {
            $s = strtoupper((string)$str);
            if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
            if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
            if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
            return 0;
        };
        $uLvl = $getLevel($u['kelas_nama'] ?: $tingkat_kelas);

        $cleanMapel = trim($u['mapel_nama']);
        $uTopik = (int)($u['topik_id'] ?: $nearestTopikId);
        $dbsToCheck = (!empty($siakad_db) && $siakad_db !== $core_db) ? [$siakad_db, $core_db] : [$core_db];
        $freshBs = [];
        
        // 1. Prioritaskan pencarian paket spesifik di bs_paket untuk jenjang kelas siswa ini
        foreach ($dbsToCheck as $targetDb) {
            $stmtAllPaket = $targetDb->prepare("
                SELECT id, kelas_nama, kelas_sasaran FROM bs_paket 
                WHERE (topik_id = ? OR ? = 0)
                  AND (mapel_nama = ? OR mapel_nama LIKE ?)
                ORDER BY id ASC
            ");
            $stmtAllPaket->execute([$uTopik, $uTopik, $cleanMapel, '%' . $cleanMapel . '%']);
            $allPkts = $stmtAllPaket->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allPkts as $pkt) {
                $pLvl = $getLevel($pkt['kelas_nama'] . ' ' . ($pkt['kelas_sasaran'] ?? ''));
                if ($pLvl === $uLvl || $uLvl === 0) {
                    $stmtBS = $targetDb->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
                    $stmtBS->execute([$pkt['id']]);
                    $rows = $stmtBS->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($rows)) {
                        $freshBs = $rows;
                        break 2;
                    }
                }
            }
        }

        // 2. Fallback jika tidak lewat bs_paket: ambil dari bs_soal dengan filter level yang sama persis
        if (empty($freshBs)) {
            foreach ($dbsToCheck as $targetDb) {
                $stmtAllSoal = $targetDb->prepare("
                    SELECT * FROM bs_soal 
                    WHERE (topik_id = ? OR ? = 0)
                      AND (mapel_nama = ? OR mapel_nama LIKE ?)
                    ORDER BY nomor_urut ASC, id ASC
                ");
                $stmtAllSoal->execute([$uTopik, $uTopik, $cleanMapel, '%' . $cleanMapel . '%']);
                $allSoals = $stmtAllSoal->fetchAll(PDO::FETCH_ASSOC);
                $matched = [];
                foreach ($allSoals as $sRow) {
                    $sLvl = $getLevel($sRow['kelas_nama']);
                    if ($sLvl === $uLvl || $uLvl === 0) {
                        $matched[] = $sRow;
                    }
                }
                if (!empty($matched)) {
                    $freshBs = $matched;
                    break;
                }
            }
        }

        if (!empty($freshBs)) {
            $bsCount = count($freshBs);
            if ((int)$u['total_soal'] === 0 || (int)$u['total_soal'] !== $bsCount) {
                $core_db->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$u['id']]);
                $stmtInsSoal = $core_db->prepare("
                    INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $noS = 1;
                foreach ($freshBs as $fb) {
                    $stmtInsSoal->execute([
                        $u['id'],
                        $noS++,
                        $fb['pertanyaan'],
                        $fb['opsi_a'] ?? '',
                        $fb['opsi_b'] ?? '',
                        $fb['opsi_c'] ?? '',
                        $fb['opsi_d'] ?? '',
                        $fb['opsi_e'] ?? '',
                        $fb['kunci_jawaban'] ?? 'A',
                        $fb['skor_maksimal'] ?? 10.00,
                        $fb['tipe_soal'] ?? 'sec_pg',
                        $fb['konten_tambahan'] ?? null
                    ]);
                }
                $u['total_soal'] = $bsCount;
            }
        }
    } catch (\Throwable $e) {}

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
    $jMulaiStr = '';
    $jSelesaiStr = '';
    $isHariIni = false;

    $otherGrid = null;
    $uTopikId = (int)($u['topik_id'] ?? 0);
    $uMapel = trim($u['mapel_nama'] ?? '');

    try {
        // 1. Cek apakah ada jadwal spesifik untuk HARI INI yang SESUAI MAPEL & KELAS SISWA
        $stmtToday = $core_db->prepare("
            SELECT g.*, s.jam_mulai, s.jam_selesai, s.label_sesi 
            FROM cbt_jadwal_grid g 
            LEFT JOIN cbt_jam_slot s ON (
                g.jam_slot = s.label_sesi 
                OR g.jam_slot = CAST(s.urutan AS CHAR)
                OR g.jam_slot = CONCAT(SUBSTRING(s.jam_mulai, 1, 5), ' - ', SUBSTRING(s.jam_selesai, 1, 5))
                OR g.jam_slot LIKE CONCAT(SUBSTRING(s.jam_mulai, 1, 5), ' - %')
                OR g.jam_slot LIKE CONCAT(SUBSTRING(s.jam_mulai, 1, 5), '%')
            )
            WHERE (g.tanggal = :today OR (g.tanggal IS NULL AND (g.hari = :curHari OR g.hari LIKE CONCAT(:curHari2, '%'))))
              AND (:tid <= 0 OR g.topik_id = :tid2 OR g.topik_id IS NULL OR g.topik_id = 0)
              AND (
                  (g.mapel_nama = :mapel OR :mapel LIKE CONCAT('%', g.mapel_nama, '%') OR g.mapel_nama LIKE CONCAT('%', :mapel, '%'))
                  OR (g.ujian_id = :uid AND (g.mapel_nama IS NULL OR g.mapel_nama = '' OR g.mapel_nama = :mapel2))
              )
              AND (
                  g.kelas_nama = :kelas 
                  OR g.kelas_nama = :tingkat 
                  OR g.kelas_nama = 'Semua Kelas' 
                  OR g.kelas_nama = ''
                  OR g.kelas_nama IS NULL
                  OR :kelas LIKE CONCAT('%', g.kelas_nama, '%')
                  OR (
                      (:short_tingkat = 'XII' AND (g.kelas_nama LIKE 'XII%' OR g.kelas_nama LIKE 'Kelas 12%' OR g.kelas_nama LIKE 'Kelas XII%'))
                      OR (:short_tingkat = 'XI' AND ((g.kelas_nama LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 11%' OR g.kelas_nama LIKE 'Kelas XI%'))
                      OR (:short_tingkat = 'X' AND ((g.kelas_nama LIKE 'X%' AND g.kelas_nama NOT LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 10%' OR g.kelas_nama LIKE 'Kelas X%'))
                  )
              )
            ORDER BY (g.mapel_nama = :mapel3) DESC, (g.ujian_id = :uid2) DESC, (g.kelas_nama = :kelas2) DESC, (g.kelas_nama = :tingkat2) DESC, g.id ASC LIMIT 1
        ");
        $stmtToday->execute([
            ':today' => $todayDate,
            ':curHari' => $curHari,
            ':curHari2' => $curHari,
            ':tid' => $uTopikId,
            ':tid2' => $uTopikId,
            ':mapel' => $uMapel,
            ':uid' => $u['id'],
            ':mapel2' => $uMapel,
            ':kelas' => $siswa['nama_kelas'],
            ':tingkat' => $tingkat_kelas,
            ':short_tingkat' => $short_tingkat,
            ':mapel3' => $uMapel,
            ':uid2' => $u['id'],
            ':kelas2' => $siswa['nama_kelas'],
            ':tingkat2' => $tingkat_kelas
        ]);
        $jGrid = $stmtToday->fetch(PDO::FETCH_ASSOC);

            if ($jGrid) {
                // Terjadwal HARI INI!
                $isHariIni = true;
                $rawSlot = trim($jGrid['jam_slot'] ?? '');
                
                $parsedStart = '';
                $parsedEnd = '';
                if (preg_match('/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/', $rawSlot, $mTimes)) {
                    $parsedStart = $mTimes[1];
                    $parsedEnd = $mTimes[2];
                }

                $jMulaiStr = $parsedStart ?: (!empty($jGrid['jam_mulai']) ? substr(trim($jGrid['jam_mulai']), 0, 5) : '');
                $jSelesaiStr = $parsedEnd ?: (!empty($jGrid['jam_selesai']) ? substr(trim($jGrid['jam_selesai']), 0, 5) : '');

                // Tentukan Label Sesi yang tepat & akurat
                $slotLabel = '';
                if ($jMulaiStr === '08:00') {
                    $slotLabel = 'Sesi 1';
                } elseif ($jMulaiStr === '09:00') {
                    $slotLabel = 'Sesi 2';
                } elseif ($jMulaiStr === '10:10') {
                    $slotLabel = 'Sesi 3';
                } elseif (!empty($jGrid['label_sesi']) && stripos($jGrid['label_sesi'], 'sesi') !== false) {
                    $slotLabel = $jGrid['label_sesi'];
                } elseif (!empty($rawSlot)) {
                    $slotLabel = $rawSlot;
                } else {
                    $slotLabel = 'Sesi Ujian';
                }

                $timeRange = (!empty($jMulaiStr) && !empty($jSelesaiStr)) ? $jMulaiStr . ' - ' . $jSelesaiStr . ' WIB' : '';
                $jadwalSlotLabel = trim($slotLabel . ' ' . ($timeRange ? "($timeRange)" : ''));

                $startTs = !empty($jMulaiStr) ? strtotime($todayDate . ' ' . $jMulaiStr) : 0;
                $endTs = !empty($jSelesaiStr) ? strtotime($todayDate . ' ' . $jSelesaiStr) : 0;

                if ($lockSchedule) {
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
                    // Mode Bebas Jam Hari Ini
                    $scheduleState = 'open';
                    $canTake = true;
                    $scheduleNotice = 'Sesi Hari Ini Terbuka (' . ($timeRange ?: 'Bebas Jam') . ')';
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
                               OR g.kelas_nama = 'Semua Kelas' 
                               OR g.kelas_nama = ''
                               OR g.kelas_nama IS NULL
                               OR :kelas LIKE CONCAT('%', g.kelas_nama, '%')
                               OR (
                                   (:short_tingkat = 'XII' AND (g.kelas_nama LIKE 'XII%' OR g.kelas_nama LIKE 'Kelas 12%' OR g.kelas_nama LIKE 'Kelas XII%'))
                                   OR (:short_tingkat = 'XI' AND ((g.kelas_nama LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 11%' OR g.kelas_nama LIKE 'Kelas XI%'))
                                   OR (:short_tingkat = 'X' AND ((g.kelas_nama LIKE 'X%' AND g.kelas_nama NOT LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 10%' OR g.kelas_nama LIKE 'Kelas X%'))
                               )
                           )
                       )
                    ORDER BY (g.ujian_id = :uid2) DESC, (g.kelas_nama = :kelas2) DESC, CASE WHEN g.tanggal >= CURDATE() THEN 0 ELSE 1 END ASC, g.tanggal ASC, g.jam_slot ASC, g.id ASC LIMIT 1
                ");
                $stmtOther->execute([
                    ':uid' => $u['id'],
                    ':mapel' => $u['mapel_nama'],
                    ':tid' => $uTopikId,
                    ':tid2' => $uTopikId,
                    ':kelas' => $siswa['nama_kelas'],
                    ':tingkat' => $tingkat_kelas,
                    ':short_tingkat' => $short_tingkat,
                    ':uid2' => $u['id'],
                    ':kelas2' => $siswa['nama_kelas']
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
                    // Benar-benar tidak dijadwalkan hari ini
                    $isHariIni = false;
                    $scheduleState = 'unscheduled';
                    $jadwalSlotLabel = 'Belum Dijadwalkan';
                    if ($lockSchedule) {
                        $canTake = false;
                        $scheduleNotice = 'Tidak Ada Jadwal Hari Ini';
                    } else {
                        $canTake = true;
                        $scheduleNotice = 'Mode Susulan Terbuka';
                    }
                }
            }
        } catch (\Exception $e) {}

    // Evaluasi Verifikasi Absensi 1 Pintu (Per-Mata Pelajaran & Sesi)
    $hasAbsen = true;
    $absenInfo = null;
    if ($requireAttendance) {
        $hasAbsen = false;
        $uId = !empty($u['id']) ? (int)$u['id'] : 0;
        $uMapelClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $u['mapel_nama'] ?? '')));

        if ($uId > 0 && isset($absenHariIniMap['uid_' . $uId])) {
            $hasAbsen = true;
            $absenInfo = $absenHariIniMap['uid_' . $uId];
        } elseif (!empty($uMapelClean) && isset($absenHariIniMap['mapel_' . $uMapelClean])) {
            $hasAbsen = true;
            $absenInfo = $absenHariIniMap['mapel_' . $uMapelClean];
        } elseif (!empty($uMapelClean) && !empty($absenHariIniList)) {
            // Cek pencocokan parsial nama mapel spesifik
            foreach ($absenHariIniList as $ab) {
                $abMapelClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $ab['mapel_nama'] ?? '')));
                if (!empty($abMapelClean) && (strpos($uMapelClean, $abMapelClean) !== false || strpos($abMapelClean, $uMapelClean) !== false)) {
                    $hasAbsen = true;
                    $absenInfo = $ab;
                    break;
                }
            }
        }
        // Catatan: Absensi bersifat KETAT per mapel/sesi. Sekali scan hanya berlaku untuk 1 mapel sesi tersebut!
    }
    $u['has_absen'] = $hasAbsen;
    $u['absen_info'] = $absenInfo;

    $u['is_hari_ini'] = $isHariIni;
    $u['can_take'] = $canTake;
    $u['schedule_state'] = $scheduleState;
    $u['schedule_notice'] = $scheduleNotice;
    $u['jadwal_slot_label'] = $jadwalSlotLabel;
    $u['jam_mulai'] = $jMulaiStr;
    $u['jam_selesai'] = $jSelesaiStr;
    $u['upcoming_date'] = $otherGrid['tanggal'] ?? '';
    $u['upcoming_time'] = $otherGrid['jam_mulai'] ?? '';
    $u['grid_topik_id'] = $otherGrid['topik_id'] ?? 0;

    $allEvaluatedUjian[] = $u;
}

// 4a. Kelompokkan Paket Ujian Tersedia (Belum Selesai)
$ujianHariIni = [];
$ujianTerlewatHariIni = [];
$ujianLainnya = [];

foreach ($allEvaluatedUjian as $u) {
    if ($u['status_pengerjaan'] === 'Selesai') {
        continue;
    }
    if (!empty($u['is_hari_ini'])) {
        // Jika pembatasan jadwal aktif ($lockSchedule) dan sesi telah berakhir/terlewat,
        // sembunyikan dari Beranda dan pindahkan ke tab Riwayat.
        // Jika mode susulan diaktifkan ($lockSchedule == false), ujian tetap muncul di Beranda dan dapat dikerjakan.
        if ($lockSchedule && ($u['schedule_state'] ?? '') === 'expired') {
            $ujianTerlewatHariIni[] = $u;
        } else {
            $ujianHariIni[] = $u;
        }
    } else {
        $ujianLainnya[] = $u;
    }
}

// Urutkan paket ujian hari ini agar sesi yang aktif/siap selalu di atas, disusul yang akan datang (terawal di atas), dan yang expired di bawah
usort($ujianHariIni, function($a, $b) {
    $rank = function($x) {
        if (in_array($x['status_pengerjaan'] ?? '', ['Mengerjakan', 'Sedang Mengerjakan'])) return 1;
        if (!empty($x['can_take'])) return 2;
        if (($x['schedule_state'] ?? '') === 'early') return 3;
        if (($x['schedule_state'] ?? '') === 'expired') return 4;
        return 5;
    };
    $rA = $rank($a);
    $rB = $rank($b);
    if ($rA !== $rB) return $rA <=> $rB;

    $tA = !empty($a['jam_mulai']) ? $a['jam_mulai'] : '99:99';
    $tB = !empty($b['jam_mulai']) ? $b['jam_mulai'] : '99:99';
    return strcmp($tA, $tB);
});

$ujianTersedia = array_merge($ujianHariIni, $ujianLainnya);

// 4b. Tentukan Asesmen yang Sedang Berlangsung Saat Ini
$activeOngoingUjian = null;
$upcomingUjian = null;
$completedTodayUjian = null;

// Prioritas 1: Yang sedang dikerjakan saat ini
foreach ($ujianHariIni as $uItem) {
    if (in_array($uItem['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan'])) {
        $activeOngoingUjian = $uItem;
        break;
    }
}
if (!$activeOngoingUjian) {
    foreach ($ujianLainnya as $uItem) {
        if (in_array($uItem['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan'])) {
            $activeOngoingUjian = $uItem;
            break;
        }
    }
}

// Prioritas 2: Yang aktif hari ini dan siap dikerjakan
if (!$activeOngoingUjian) {
    foreach ($ujianHariIni as $uItem) {
        if ($uItem['status_pengerjaan'] !== 'Selesai' && $uItem['can_take']) {
            $activeOngoingUjian = $uItem;
            break;
        }
    }
}

// Prioritas 3: Jadwal hari ini tapi jam mulai belum tiba (pilih yang paling awal jam mulainya)
if (!$activeOngoingUjian) {
    $earlyToday = [];
    foreach ($ujianHariIni as $uItem) {
        if (!empty($uItem['is_hari_ini']) && $uItem['schedule_state'] === 'early') {
            $earlyToday[] = $uItem;
        }
    }
    if (!empty($earlyToday)) {
        usort($earlyToday, function($a, $b) {
            $tA = !empty($a['jam_mulai']) ? $a['jam_mulai'] : '99:99';
            $tB = !empty($b['jam_mulai']) ? $b['jam_mulai'] : '99:99';
            return strcmp($tA, $tB);
        });
        $upcomingUjian = $earlyToday[0];
    }
}

// Prioritas 4: Jika jadwal hari ini sudah diselesaikan siswa
if (!$activeOngoingUjian && !$upcomingUjian) {
    foreach ($ujianHariIni as $uItem) {
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
    if ($is_cbt_only) {
        $stmtRiw = $core_db->prepare("
            SELECT p.*, u.nama_ujian, u.mapel_nama, u.kkm, u.durasi_menit
            FROM cbt_peserta p
            JOIN cbt_ujian u ON p.ujian_id = u.id
            WHERE (p.siswa_id IS NULL OR p.siswa_id = 0 OR p.siswa_id = :sid) 
              AND (p.nis = :no_peserta OR (p.nis = :nis AND :nis != '') OR p.nama_siswa = :nama_kj)
              AND p.status_pengerjaan = 'Selesai'
            ORDER BY p.waktu_selesai DESC
        ");
        $stmtRiw->execute([
            ':sid' => $siswa_id,
            ':no_peserta' => (string)($cbtStudent['no_peserta'] ?? ''),
            ':nis' => (string)($cbtStudent['nis'] ?? ''),
            ':nama_kj' => (string)($cbtStudent['nama_siswa'] ?? '')
        ]);
    } else {
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
    }
    $riwayatUjian = $stmtRiw->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

$page_title = 'CBT Ujian Siswa';
include 'header.php';
?>

<!-- SweetAlert2 (Local Offline) -->
<script src="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/js/sweetalert2.all.min.js"></script>

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
        align-items: stretch;
        justify-content: space-between;
        gap: 8px;
        padding: 0 16px;
        margin-bottom: 16px;
    }
    .btn-tatatertib-top {
        background: #ffffff;
        color: #0f172a;
        border: 1.5px solid #cbd5e1;
        padding: 8px 6px;
        border-radius: 12px;
        font-size: 0.74rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s;
        flex: 1;
        text-align: center;
        text-decoration: none;
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
        padding: 12px;
    }
    .cbt-modal-box {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 94vh;
        margin-bottom: 0;
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
        <?php if ($is_cbt_only || $is_standalone_env): ?>
            <button type="button" onclick="konfirmasiKeluarCbt()" class="cbt-btn-logout" title="Keluar Akun">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        <?php endif; ?>
    </div>

    <!-- Action Bar: Tombol Tata Tertib, Jadwal Ujian & Histori Pengerjaan -->
    <div class="cbt-actions-bar" style="display: flex; gap: 8px; flex-wrap: nowrap; align-items: stretch;">
        <button type="button" onclick="bukaModalTataTertib()" class="btn-tatatertib-top">
            <i class="fa-solid fa-clipboard-list" style="color: #2563eb; font-size: 0.95rem;"></i>
            <span>Tata Tertib</span>
        </button>
        <button type="button" onclick="bukaModalJadwalSiswa()" class="btn-tatatertib-top" style="border-color: #cbd5e1; background: #ffffff;">
            <i class="fa-solid fa-calendar-days" style="color: #059669; font-size: 0.95rem;"></i>
            <span>Jadwal Ujian</span>
        </button>
        <button type="button" onclick="bukaModalRiwayatUjian()" class="btn-tatatertib-top" style="border-color: #cbd5e1; background: #ffffff;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #7c3aed; font-size: 0.95rem;"></i>
            <span>Histori Pengerjaan</span>
        </button>
        <?php if ($is_standalone_env): ?>
            <a href="cbt.php" class="btn-tatatertib-top" style="<?= $activeTab !== 'riwayat' ? 'background: #2563eb; color: #ffffff; border-color: #2563eb;' : 'background: #ffffff; color: #475569; border-color: #cbd5e1;' ?> text-decoration: none;">
                <i class="fa-solid fa-layer-group"></i>
                <span>Ujian Aktif</span>
            </a>
            <a href="cbt.php?tab=riwayat" class="btn-tatatertib-top" style="<?= $activeTab === 'riwayat' ? 'background: #2563eb; color: #ffffff; border-color: #2563eb;' : 'background: #ffffff; color: #475569; border-color: #cbd5e1;' ?> text-decoration: none;">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat (<?= count($riwayatUjian) + count($ujianTerlewatHariIni) ?>)</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($activeTab === 'riwayat'): ?>
        <!-- Tampilan Khusus Tab Riwayat Pengerjaan Ujian -->
        <div style="padding: 12px 16px 20px;">
            <div style="background: white; border-radius: 16px; padding: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 15px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-clock-rotate-left" style="color: #2563eb;"></i> Riwayat & Status Ujian
                        </h3>
                        <p style="margin: 3px 0 0 0; font-size: 0.72rem; color: #64748b;">
                            Daftar ujian yang telah dikerjakan serta sesi jadwal yang telah terlewat.
                        </p>
                    </div>
                </div>

                <!-- Filter Tombol / Tabs -->
                <div style="display: flex; gap: 8px; margin-top: 14px; overflow-x: auto; padding-bottom: 2px;">
                    <button type="button" id="btn-filter-all" onclick="setFilterRiwayat('all')" class="btn-filter-riwayat" style="padding: 6px 14px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; cursor: pointer; border: 1px solid #2563eb; background: #2563eb; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <span>Semua</span>
                        <span style="font-size: 0.68rem; padding: 1px 6px; border-radius: 10px; background: rgba(255,255,255,0.25);"><?= count($riwayatUjian) + count($ujianTerlewatHariIni) ?></span>
                    </button>
                    <button type="button" id="btn-filter-selesai" onclick="setFilterRiwayat('selesai')" class="btn-filter-riwayat" style="padding: 6px 14px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                        <span>Sudah Mengerjakan</span>
                        <span style="font-size: 0.68rem; padding: 1px 6px; border-radius: 10px; background: #e2e8f0; color: #334155;"><?= count($riwayatUjian) ?></span>
                    </button>
                    <?php if (count($ujianTerlewatHariIni) > 0): ?>
                    <button type="button" id="btn-filter-terlewat" onclick="setFilterRiwayat('terlewat')" class="btn-filter-riwayat" style="padding: 6px 14px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <i class="fa-solid fa-clock" style="color: #ef4444;"></i>
                        <span>Sesi Terlewat</span>
                        <span style="font-size: 0.68rem; padding: 1px 6px; border-radius: 10px; background: #fee2e2; color: #b91c1c;"><?= count($ujianTerlewatHariIni) ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($riwayatUjian) || !empty($ujianTerlewatHariIni)): ?>
                <!-- Daftar Ujian yang Telah Selesai Dikerjakan -->
                <?php foreach ($riwayatUjian as $r): ?>
                    <?php
                    $isTuntas = ((float)$r['nilai_akhir'] >= (float)($r['kkm'] ?? 75));
                    ?>
                    <div class="item-riwayat filter-selesai" style="background: white; border-radius: 14px; padding: 14px; margin-bottom: 10px; border-left: 4px solid <?= $isTuntas ? '#10b981' : '#ef4444' ?>; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; transition: all 0.2s;">
                        <div style="min-width: 0; flex: 1; padding-right: 10px;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                <span style="font-size: 0.65rem; font-weight: 800; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1px 8px; border-radius: 12px;">SUDAH MENGERJAKAN</span>
                            </div>
                            <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($r['nama_ujian']) ?></div>
                            <div style="font-size: 0.74rem; color: #64748b; margin-top: 3px;">
                                <span style="font-weight: 600; color: #334155;"><?= htmlspecialchars($r['mapel_nama']) ?></span> • 
                                <span><?= date('d M Y, H:i', strtotime($r['waktu_selesai'])) ?> WIB</span>
                            </div>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <?php if ($showScoreToStudent): ?>
                                <div style="font-size: 1.2rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>; line-height: 1;">
                                    <?= number_format((float)$r['nilai_akhir'], 1) ?>
                                </div>
                                <span style="font-size: 0.65rem; font-weight: 800; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                                    <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                                </span>
                            <?php else: ?>
                                <div style="font-size: 0.88rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>;">
                                    <?= $isTuntas ? '✓ TUNTAS' : '✕ REMEDIAL' ?>
                                </div>
                                <span style="font-size: 0.65rem; color: #64748b; font-weight: 700;">
                                    KKM: <?= (int)($r['kkm'] ?? 75) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Daftar Ujian Hari Ini yang Waktu Sesinya Telah Berakhir (Terlewat) -->
                <?php foreach ($ujianTerlewatHariIni as $uPass): ?>
                    <div class="item-riwayat filter-terlewat" style="background: #fff; border-radius: 14px; padding: 14px; margin-bottom: 10px; border-left: 4px solid #f97316; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #fed7aa; border-right: 1px solid #fed7aa; border-bottom: 1px solid #fed7aa; transition: all 0.2s;">
                        <div style="min-width: 0; flex: 1; padding-right: 10px;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                <span style="font-size: 0.65rem; font-weight: 800; color: #c2410c; background: #fff7ed; border: 1px solid #fdba74; padding: 1px 8px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-clock-rotate-left"></i> SESI TERLEWAT
                                </span>
                                <?php if (!empty($uPass['jadwal_slot_label'])): ?>
                                    <span style="font-size: 0.68rem; color: #64748b; font-weight: 600;">
                                        <?= htmlspecialchars($uPass['jadwal_slot_label']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($uPass['nama_ujian'] ?: $uPass['mapel_nama']) ?></div>
                            <div style="font-size: 0.73rem; color: #9a3412; margin-top: 4px; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Waktu sesi berakhir pukul <?= htmlspecialchars($uPass['jam_selesai'] ?: '') ?> WIB. Hubungi proktor jika dijadwalkan Susulan.</span>
                            </div>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <span style="display: inline-block; font-size: 0.7rem; font-weight: 800; color: #9a3412; background: #ffedd5; padding: 4px 10px; border-radius: 8px;">
                                Terkunci
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div style="text-align: center; padding: 45px 20px; background: white; border-radius: 16px; border: 1.5px dashed #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 60px; height: 60px; background: #f1f5f9; color: #94a3b8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem; margin: 0 auto 12px;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-weight: 800; font-size: 0.95rem; color: #334155;">Belum Ada Riwayat Ujian</h4>
                    <p style="margin: 0 0 16px 0; font-size: 0.78rem; color: #64748b; line-height: 1.5; max-width: 320px; margin-inline: auto;">
                        Ujian yang telah Anda selesaikan atau sesi jadwal yang telah lewat akan tercatat di halaman ini.
                    </p>
                    <a href="cbt.php" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; background: #2563eb; color: white; border-radius: 10px; font-weight: 700; font-size: 0.8rem; text-decoration: none;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda Ujian
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>

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
        <?php
        $todayJadwal = null;
        foreach ($jadwalSiswaList as $js) {
            if (!empty($js['tanggal']) && $js['tanggal'] === $todayDate) {
                $todayJadwal = $js;
                break;
            } elseif (empty($js['tanggal']) && !empty($js['hari']) && stripos($js['hari'], $curHari) === 0) {
                $todayJadwal = $js;
                break;
            }
        }
        $firstJadwal = $todayJadwal ?: (!empty($jadwalSiswaList) ? $jadwalSiswaList[0] : null);
        ?>
        <div class="cbt-banner-pro" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(56, 189, 248, 0.25);">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 5px;">
                    <span style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); padding: 2px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-calendar-days" style="font-size: 0.65rem;"></i>
                        JADWAL UJIAN RESMI
                    </span>
                    <?php if ($firstJadwal): ?>
                        <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">
                            <?= htmlspecialchars(($firstJadwal['hari'] ?? '') . (!empty($firstJadwal['tanggal']) ? ' (' . date('d M Y', strtotime($firstJadwal['tanggal'])) . ')' : '')) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 900; color: #38bdf8; margin: 0 0 3px 0;">
                    <?= $firstJadwal ? htmlspecialchars($firstJadwal['mapel_nama'] . ' - ' . ($firstJadwal['label_sesi'] ?: $firstJadwal['jam_slot'])) : 'Tidak Ada Ujian Aktif Saat Ini' ?>
                </h3>
                <p style="color: #cbd5e1; font-size: 0.76rem;">
                    <?= $firstJadwal ? ('Waktu: ' . ($firstJadwal['jam_mulai'] ? substr($firstJadwal['jam_mulai'], 0, 5) : '08:00') . ' - ' . ($firstJadwal['jam_selesai'] ? substr($firstJadwal['jam_selesai'], 0, 5) : '10:00') . ' WIB') : 'Silakan klik tombol "Jadwal Ujian" di atas untuk melihat rincian agenda pelaksanaan.' ?>
                </p>
            </div>
            <div style="font-size: 2rem; opacity: 0.45; margin-left: 14px; color: #38bdf8; flex-shrink: 0; cursor: pointer;" onclick="bukaModalJadwalSiswa()" title="Buka Jadwal Lengkap">
                <i class="fa-solid fa-calendar-days"></i>
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
                <?php
                $cbtKerjakanBase = (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/masmifhda/') !== false) ? '/masmifhda/cbt/kerjakan' : '/cbt/kerjakan';
                ?>
                <?php if ($isMengerjakan): ?>
                    <a href="<?= $cbtKerjakanBase ?>?ujian_id=<?= (int)$u['id'] ?>" class="btn-ujian btn-ujian-continue">
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

    <?php endif; ?> <!-- /end activeTab !== riwayat -->

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
                        $indoDaysMap = [
                            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                        ];
                        foreach ($jadwalSiswaList as $js): 
                            $hariName = !empty($js['hari']) ? trim($js['hari']) : (!empty($js['tanggal']) ? ($indoDaysMap[date('l', strtotime($js['tanggal']))] ?? '') : '');
                            $tglFmt = !empty($js['tanggal']) ? date('d M Y', strtotime($js['tanggal'])) : '';
                            
                            $headerGroup = '';
                            if ($hariName && $tglFmt) {
                                $headerGroup = $hariName . ' (' . $tglFmt . ')';
                            } elseif ($hariName) {
                                $headerGroup = $hariName;
                            } elseif ($tglFmt) {
                                $headerGroup = $tglFmt;
                            } else {
                                $headerGroup = 'Jadwal Ujian';
                            }

                            $isToday = (!empty($js['tanggal']) && $js['tanggal'] === $todayDate) || ($js['hari'] === $curHari);
                        ?>
                            <?php if ($headerGroup !== $curGroupDate): $curGroupDate = $headerGroup; ?>
                                <div style="font-size: 0.8rem; font-weight: 800; color: #0284c7; background: #e0f2fe; border: 1.5px solid #7dd3fc; padding: 7px 14px; border-radius: 10px; margin-top: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                    <span style="display: flex; align-items: center; gap: 7px;">
                                        <i class="fa-solid fa-calendar-day" style="color: #0284c7;"></i> 
                                        <span><?= htmlspecialchars($headerGroup) ?></span>
                                    </span>
                                    <?php if ($isToday): ?>
                                        <span style="background: #0284c7; color: white; font-size: 0.65rem; font-weight: 900; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.5px; box-shadow: 0 1px 3px rgba(2,132,199,0.4);">
                                            HARI INI
                                        </span>
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

    <!-- MODAL: HISTORI PENGERJAAN UJIAN SISWA -->
    <div id="modalRiwayatSiswa" class="cbt-modal-backdrop" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px 16px 85px 16px;">
        <div class="cbt-modal-box" style="background: white; border-radius: 20px; max-width: 520px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; animation: slideUp 0.25s ease-out; max-height: calc(85vh - 75px); margin-bottom: 40px; display: flex; flex-direction: column;">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(168, 85, 247, 0.2); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; border: 1px solid rgba(192, 132, 252, 0.3);">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.98rem; font-weight: 900;">Histori Pengerjaan Ujian</h3>
                        <p style="margin: 2px 0 0; font-size: 0.72rem; color: #cbd5e1;">
                            Daftar ujian yang telah Anda selesaikan
                        </p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalRiwayatUjian()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>

            <!-- Body -->
            <div style="padding: 16px 20px; overflow-y: auto; flex: 1;">
                <?php if (empty($riwayatUjian)): ?>
                    <div style="text-align: center; padding: 35px 10px; color: #94a3b8;">
                        <i class="fa-solid fa-clock-rotate-left" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <div style="font-weight: 800; font-size: 0.92rem; color: #475569;">Belum Ada Histori Pengerjaan</div>
                        <div style="font-size: 0.76rem; margin-top: 4px;">Ujian yang telah selesai dikerjakan akan otomatis tercatat di sini.</div>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <?php foreach ($riwayatUjian as $r): 
                            $isTuntas = ((float)($r['nilai_akhir'] ?? 0) >= (float)($r['kkm'] ?? 75));
                        ?>
                            <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 13px 15px; border-left: 4px solid <?= $isTuntas ? '#10b981' : '#ef4444' ?>; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                    <div style="min-width: 0; flex: 1;">
                                        <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; line-height: 1.3;">
                                            <?= htmlspecialchars($r['nama_ujian']) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                                            <span style="font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 7px; border-radius: 6px; border: 1px solid #bfdbfe; font-size: 0.7rem;">
                                                <?= htmlspecialchars($r['mapel_nama']) ?>
                                            </span>
                                            <span>• <?= !empty($r['waktu_selesai']) ? date('d M Y, H:i', strtotime($r['waktu_selesai'])) . ' WIB' : 'Selesai' ?></span>
                                        </div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <?php if ($showScoreToStudent): ?>
                                            <div style="font-size: 1.25rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>; line-height: 1;">
                                                <?= number_format((float)($r['nilai_akhir'] ?? 0), 1) ?>
                                            </div>
                                            <span style="display: inline-block; margin-top: 3px; font-size: 0.65rem; font-weight: 800; padding: 1px 6px; border-radius: 4px; background: <?= $isTuntas ? '#ecfdf5' : '#fef2f2' ?>; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>; border: 1px solid <?= $isTuntas ? '#a7f3d0' : '#fecaca' ?>;">
                                                <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="font-size: 0.68rem; font-weight: 800; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 6px;">
                                                SELESAI
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #f1f5f9; display: flex; gap: 12px; font-size: 0.72rem; color: #64748b; font-weight: 600;">
                                    <span><i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Benar: <b style="color: #334155;"><?= (int)($r['jml_benar'] ?? 0) ?></b></span>
                                    <span><i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i> Salah: <b style="color: #334155;"><?= (int)($r['jml_salah'] ?? 0) ?></b></span>
                                    <span><i class="fa-solid fa-circle-minus" style="color: #94a3b8;"></i> Kosong: <b style="color: #334155;"><?= (int)($r['jml_kosong'] ?? 0) ?></b></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right;">
                <button type="button" onclick="tutupModalRiwayatUjian()" style="background: #2563eb; color: white; border: none; border-radius: 10px; font-weight: 800; font-size: 0.82rem; padding: 8px 18px; cursor: pointer;">
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
                    <i class="fa-solid fa-clipboard-list" style="font-size: 1.2rem; color: #93c5fd;"></i>
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
    <div id="modalStartExam" class="cbt-modal-backdrop" style="align-items: center; justify-content: center; overflow-y: auto;">
        <div class="cbt-modal-box" style="max-height: 90vh; max-height: 90dvh; max-width: 395px; border-radius: 20px; display: flex; flex-direction: column;">
            <!-- Modal Header Compact -->
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; color: #ffffff; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; background: rgba(255, 255, 255, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                        🚀
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.94rem; font-weight: 800; line-height: 1.2;">Mulai Ujian CBT</h3>
                        <p style="margin: 2px 0 0; font-size: 0.7rem; color: #bfdbfe;">Masukkan token ujian untuk membuka soal</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalMulai()" style="background: rgba(255,255,255,0.18); border: none; color: white; width: 26px; height: 26px; border-radius: 6px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>

            <!-- Modal Body Scrollable -->
            <div style="padding: 12px 16px; overflow-y: auto; flex: 1;">
                <!-- Info Paket Ujian Ringkas -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; margin-bottom: 8px;">
                    <div id="modalExamTitle" style="font-size: 0.82rem; font-weight: 800; color: #0f172a; line-height: 1.3; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">-</div>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap; font-size: 0.68rem; font-weight: 700;">
                        <span id="modalExamMapel" style="background: #eff6ff; color: #2563eb; padding: 2px 6px; border-radius: 5px; border: 1px solid #dbeafe;">-</span>
                        <span id="modalExamDurasi" style="background: #f0fdf4; color: #16a34a; padding: 2px 6px; border-radius: 5px; border: 1px solid #dcfce7;">-</span>
                        <span id="modalExamSoal" style="background: #faf5ff; color: #7c3aed; padding: 2px 6px; border-radius: 5px; border: 1px solid #f3e8ff;">-</span>
                    </div>
                </div>

                <!-- Box Token Ujian Resmi (Bisa diklik untuk auto-salin/isi!) -->
                <div onclick="autoFillToken()" title="Klik untuk otomatis mengisi token" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border-radius: 12px; padding: 7px 12px; margin-bottom: 8px; text-align: center; cursor: pointer; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15); border: 1.5px solid #334155;">
                    <div style="font-size: 0.64rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                        KODE TOKEN UJIAN RESMI <span style="font-size: 0.6rem; color: #38bdf8; font-weight: 600; text-transform: none;">(Klik untuk isi)</span>
                    </div>
                    <div id="modalExamToken" style="font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 1.5rem; font-weight: 900; letter-spacing: 4px; color: #38bdf8; margin: 1px 0; text-shadow: 0 2px 8px rgba(56, 189, 248, 0.35); font-variant-numeric: slashed-zero;">
                        ------
                    </div>
                    <div id="tokenCharHint" style="display: none; font-size: 0.65rem; font-weight: 800; color: #facc15; margin: 2px auto 0; background: rgba(234, 179, 8, 0.15); padding: 1px 6px; border-radius: 20px; border: 1px solid rgba(234, 179, 8, 0.3);">
                        <i class="fa-solid fa-circle-info"></i> Memakai Huruf <b>O</b> (Bukan Nol 0)
                    </div>
                </div>

                <!-- Form Input Token Siswa -->
                <div style="margin-bottom: 6px;">
                    <label for="inputTokenUjian" style="display: block; font-size: 0.72rem; font-weight: 800; color: #334155; margin-bottom: 3px;">
                        Ketikkan / Konfirmasi Token di Bawah:
                    </label>
                    <input type="text" id="inputTokenUjian" placeholder="Ketik Token" maxlength="10" autocomplete="off" style="width: 100%; box-sizing: border-box; padding: 8px; border-radius: 8px; border: 2px solid #cbd5e1; text-align: center; text-transform: uppercase; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 1.1rem; font-weight: 900; letter-spacing: 4px; color: #0f172a; outline: none; transition: border-color 0.2s; font-variant-numeric: slashed-zero;">
                    <div id="tokenErrorMsg" style="display: none; color: #dc2626; font-size: 0.68rem; font-weight: 800; margin-top: 2px; text-align: center;">
                        <i class="fa-solid fa-circle-exclamation"></i> Token tidak cocok! Silakan cek kembali.
                    </div>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 5px 8px; font-size: 0.66rem; color: #92400e; line-height: 1.3; display: flex; gap: 5px; align-items: flex-start;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.72rem; margin-top: 1px; flex-shrink: 0; color: #d97706;"></i>
                    <div>
                        <b>Perhatian:</b> Waktu ujian langsung dihitung mundur begitu tombol Mulai ditekan.
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons (Never shrinks) -->
            <div style="padding: 9px 16px 12px; display: flex; gap: 8px; border-top: 1px solid #f1f5f9; flex-shrink: 0; background: #ffffff;">
                <button type="button" onclick="tutupModalMulai()" style="flex: 1; padding: 9px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 800; font-size: 0.78rem; cursor: pointer;">
                    Batal
                </button>
                <button type="button" onclick="verifikasiDanMulaiUjian()" style="flex: 1.3; padding: 9px; border-radius: 8px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 900; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 5px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);">
                    <span>Mulai Ujian</span> <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
let curUjianId = 0;
let curExpectedToken = '';

function autoFillToken() {
    if (curExpectedToken) {
        const inp = document.getElementById('inputTokenUjian');
        if (inp) {
            inp.value = curExpectedToken.trim();
            inp.focus();
        }
    }
}

function bukaModalJadwalSiswa() {
    document.getElementById('modalJadwalSiswa').style.display = 'flex';
}
function tutupModalJadwalSiswa() {
    document.getElementById('modalJadwalSiswa').style.display = 'none';
}

function bukaModalRiwayatUjian() {
    var modal = document.getElementById('modalRiwayatSiswa');
    if (modal) modal.style.display = 'flex';
}
function tutupModalRiwayatUjian() {
    var modal = document.getElementById('modalRiwayatSiswa');
    if (modal) modal.style.display = 'none';
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
    document.getElementById('modalExamSoal').textContent = (totalSoal > 0 ? totalSoal + ' Soal' : '10 Soal');
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
    inp.value = curExpectedToken; // Otomatis isi token resmi agar tidak terpotong oleh keyboard di HP!
    inp.style.borderColor = '#22c55e';
    document.getElementById('tokenErrorMsg').style.display = 'none';

    document.getElementById('modalStartExam').style.display = 'flex';
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
    let curPath = window.location.pathname;
    let cbtPrefix = '/cbt';
    if (curPath.indexOf('/masmifhda/') !== -1) {
        cbtPrefix = '/masmifhda/cbt';
    } else if (curPath.indexOf('/cbt/') !== -1) {
        cbtPrefix = curPath.substring(0, curPath.indexOf('/cbt/') + 4);
    }
    window.location.href = cbtPrefix + '/kerjakan?ujian_id=' + curUjianId + '&token=' + encodeURIComponent(curExpectedToken);
}

// Enter key submit on input token
document.getElementById('inputTokenUjian')?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        verifikasiDanMulaiUjian();
    }
});

// Konfirmasi Keluar / Logout Akun CBT
function konfirmasiKeluarCbt() {
    Swal.fire({
        title: 'Keluar dari Akun?',
        text: 'Apakah Anda yakin ingin logout dari aplikasi CBT?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}

// Filter Tab Riwayat (Semua / Sudah Mengerjakan / Sesi Terlewat)
function setFilterRiwayat(type) {
    var btns = document.querySelectorAll('.btn-filter-riwayat');
    btns.forEach(function(b) {
        b.style.background = '#f8fafc';
        b.style.color = '#475569';
        b.style.borderColor = '#e2e8f0';
    });
    var activeBtn = document.getElementById('btn-filter-' + type);
    if (activeBtn) {
        activeBtn.style.background = '#2563eb';
        activeBtn.style.color = '#ffffff';
        activeBtn.style.borderColor = '#2563eb';
    }
    
    var items = document.querySelectorAll('.item-riwayat');
    items.forEach(function(item) {
        if (type === 'all') {
            item.style.display = 'flex';
        } else if (type === 'selesai') {
            item.style.display = item.classList.contains('filter-selesai') ? 'flex' : 'none';
        } else if (type === 'terlewat') {
            item.style.display = item.classList.contains('filter-terlewat') ? 'flex' : 'none';
        }
    });
}

// Heartbeat ping to Developer Monitor setiap 45 detik agar status siswa tetap Online
setInterval(function() {
    fetch('ping_tracker.php').catch(function(){});
}, 45000);
</script>

<?php include 'footer.php'; ?>
