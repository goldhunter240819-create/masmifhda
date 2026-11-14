<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;

class AbsenGuruV2Controller {
    
    public static function dashboard() {
        redirect('admin/absen_v2');
        exit;
    }

    public static function scanner() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); exit; }
        
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_guru = 1");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) { redirect('/login'); exit; }
        }
        
        $title = "Scanner Absensi QR Guru V2";
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/scanner.php';
    }

    public static function processScan() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_guru = 1");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                exit;
            }
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $raw_qr_text = isset($input['nis']) ? trim($input['nis']) : ''; // we can still use the 'nis' field from JS
        $nip = $raw_qr_text;

        // Ekstrak NIP jika formatnya adalah teks multi-baris dari Cetak Kartu:
        if (preg_match('/NIP(?:\/NUPTK)?:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $nip = trim($matches[1]);
        }
        
        $username = '';
        if (preg_match('/USERNAME:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $username = trim($matches[1]);
        }
        
        // Ekstrak TOKEN jika format login baru: TOKEN:ID:HASH
        $token_id = null;
        if (stripos($raw_qr_text, 'TOKEN:') === 0) {
            $parts = explode(':', $raw_qr_text);
            if (count($parts) === 3) {
                $uid = intval($parts[1]);
                $hash = $parts[2];
                $expected_hash = substr(md5($uid . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                if ($hash === $expected_hash) {
                    $token_id = $uid; // Ini adalah user_id
                }
            }
        }

        file_put_contents(__DIR__ . '/../../scanner_guru_debug.log', "[" . date('Y-m-d H:i:s') . "] Raw Input: " . $raw_qr_text . "\n", FILE_APPEND);

        if (empty($nip) && empty($username) && empty($token_id)) {
            $msg = 'QR Code tidak terbaca atau format tidak dikenali.';
            echo json_encode(['success' => false, 'message' => $msg]);
            exit;
        }

        try {
            $db_core = Database::connect('core');
            $db_siakad = Database::connect('siakad');
            
            $guru = false;

            // 1. Cari berdasarkan Token ID (user_id)
            if ($token_id) {
                $stmt = $db_core->prepare("SELECT id, nip, nama FROM guru WHERE user_id = ?");
                $stmt->execute([$token_id]);
                $guru = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            // 2. Cari berdasarkan Username
            if (!$guru && !empty($username)) {
                $stmt_user = $db_core->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_user->execute([$username]);
                $user = $stmt_user->fetch(\PDO::FETCH_ASSOC);
                
                if ($user) {
                    $stmt_guru = $db_core->prepare("SELECT id, nip, nama FROM guru WHERE user_id = ?");
                    $stmt_guru->execute([$user['id']]);
                    $guru = $stmt_guru->fetch(\PDO::FETCH_ASSOC);
                }
            }

            // 3. Cari berdasarkan NIP
            if (!$guru && !empty($nip)) {
                $stmt = $db_core->prepare("SELECT id, nip, nama FROM guru WHERE nip = ?");
                $stmt->execute([$nip]);
                $guru = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            if (!$guru) {
                $msg = 'Data guru tidak ditemukan di database.';
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            
            $today = date('Y-m-d');
            
            // Cek Kalender Akademik (Libur)
            $cekLibur = $db_siakad->query("SELECT tipe, keterangan FROM kalender_akademik WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' AND tipe = 'libur' LIMIT 1")->fetch();
            if ($cekLibur) {
                $msg = 'Hari ini Libur: ' . $cekLibur['keterangan'] . '. Tidak perlu scan absensi.';
                file_put_contents(__DIR__ . '/../../scanner_guru_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed: " . $msg . "\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }

            // 4. Insert/Update Kehadiran di siakad_db.absensi_guru
            $guru_id = $guru['id'];
            $waktu_sekarang = date('H:i:s');
            
            // Cek apakah sudah absen hari ini
            $stmtCek = $db_siakad->prepare("SELECT id, status, waktu_masuk FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
            $stmtCek->execute([$today, $guru_id]);
            $absenAda = $stmtCek->fetch();

            // Setup DB table for guru absensi if not full
            $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guru_id INT NOT NULL,
                tanggal DATE NOT NULL,
                waktu_masuk TIME NULL,
                waktu_keluar TIME NULL,
                status VARCHAR(50) DEFAULT 'Hadir',
                keterangan TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            if ($absenAda) {
                if ($absenAda['status'] == 'Hadir') {
                    // Update waktu pulang jika jam sudah melewatinya
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Sudah absen sebelumnya.',
                        'data' => [
                            'nama' => $guru['nama'],
                            'kelas' => 'Guru',
                            'status' => 'Sudah Hadir'
                        ]
                    ]);
                    exit;
                } else {
                    $stmtUpdate = $db_siakad->prepare("UPDATE absensi_guru SET status = 'Hadir', waktu_masuk = ? WHERE id = ?");
                    $stmtUpdate->execute([$waktu_sekarang, $absenAda['id']]);
                }
            } else {
                $stmtInsert = $db_siakad->prepare("INSERT INTO absensi_guru (tanggal, guru_id, waktu_masuk, status, created_at) VALUES (?, ?, ?, 'Hadir', NOW())");
                $stmtInsert->execute([$today, $guru_id, $waktu_sekarang]);
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Berhasil!',
                'data' => [
                    'nama' => $guru['nama'],
                    'kelas' => 'Guru',
                    'status' => 'Hadir'
                ]
            ]);

        } catch (\PDOException $e) {
            $msg = 'Database Error: ' . $e->getMessage();
            echo json_encode(['success' => false, 'message' => $msg]);
        }
    }

    public static function generateRandomTime($start_h, $start_m, $end_h, $end_m) {
        $start_sec = ($start_h * 3600) + ($start_m * 60);
        $end_sec   = ($end_h * 3600) + ($end_m * 60);
        $rand_sec  = mt_rand($start_sec, $end_sec);
        
        $h = floor($rand_sec / 3600);
        $m = floor(($rand_sec % 3600) / 60);
        $s = $rand_sec % 60;
        
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private static $columnsEnsured = false;

    public static function ensureAbsensiGuruColumns($db_siakad) {
        if (self::$columnsEnsured) return;
        self::$columnsEnsured = true;

        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guru_id INT NOT NULL,
            tanggal DATE NOT NULL,
            waktu_masuk TIME NULL,
            waktu_keluar TIME NULL,
            jam_masuk TIME NULL,
            jam_pulang TIME NULL,
            status VARCHAR(50) DEFAULT 'Hadir',
            keterangan TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        try {
            $cols = $db_siakad->query("SHOW COLUMNS FROM absensi_guru")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('waktu_masuk', $cols)) {
                $db_siakad->exec("ALTER TABLE absensi_guru ADD COLUMN waktu_masuk TIME NULL AFTER status");
            }
            if (!in_array('waktu_keluar', $cols)) {
                $db_siakad->exec("ALTER TABLE absensi_guru ADD COLUMN waktu_keluar TIME NULL AFTER waktu_masuk");
            }
            if (!in_array('jam_masuk', $cols)) {
                $db_siakad->exec("ALTER TABLE absensi_guru ADD COLUMN jam_masuk TIME NULL AFTER waktu_keluar");
            }
            if (!in_array('jam_pulang', $cols)) {
                $db_siakad->exec("ALTER TABLE absensi_guru ADD COLUMN jam_pulang TIME NULL AFTER jam_masuk");
            }
        } catch (\Exception $e) {}
    }

    public static function getSertifikasiGuruIds($db_core) {
        $stmt = $db_core->query("
            SELECT id FROM guru 
            WHERE LOWER(TRIM(status_kepegawaian)) LIKE '%sertifikasi%'
               OR (nrg IS NOT NULL AND TRIM(nrg) != '' AND nrg != '-')
               OR (no_sertifikat IS NOT NULL AND TRIM(no_sertifikat) != '' AND no_sertifikat != '-')
               OR (no_peserta_sertifikasi IS NOT NULL AND TRIM(no_peserta_sertifikasi) != '' AND no_peserta_sertifikasi != '-')
        ");
        $raw = $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN) : [];
        return array_map('intval', $raw);
    }

    public static function getJadwalPerHari($db_siakad) {
        $jadwal = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        foreach ($hariList as $h) {
            $stmtJam = $db_siakad->prepare("SELECT DISTINCT guru_id FROM jadwal_pelajaran WHERE LOWER(hari) = LOWER(?)");
            $stmtJam->execute([$h]);
            $rawIds = $stmtJam->fetchAll(\PDO::FETCH_COLUMN);
            $jadwal[$h] = array_filter(array_map('intval', $rawIds), function($id) { return $id > 0; });
        }
        return $jadwal;
    }

    public static function getKalenderHolidaysMap($db_siakad, $startDate, $endDate) {
        $holidayMap = [];

        // 1. Ambil dari kalender_pendidikan (aula_siakad_db)
        try {
            $stmt = $db_siakad->query("SELECT tanggal_mulai, tanggal_selesai, keterangan, jenis 
                FROM kalender_pendidikan 
                WHERE (COALESCE(NULLIF(tanggal_selesai, ''), tanggal_mulai) >= '$startDate')
                  AND tanggal_mulai <= '$endDate'
                ORDER BY tanggal_mulai ASC");
            if ($stmt) {
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $cur = strtotime($r['tanggal_mulai']);
                    $end = (!empty($r['tanggal_selesai']) && $r['tanggal_selesai'] !== '0000-00-00') ? strtotime($r['tanggal_selesai']) : $cur;
                    if ($end < $cur) { $end = $cur; }

                    $isHoliday = ($r['jenis'] === 'libur' 
                        || $r['jenis'] === 'kegiatan_bebas_jurnal'
                        || stripos($r['keterangan'], 'libur') !== false 
                        || stripos($r['keterangan'], 'cuti') !== false
                        || stripos($r['keterangan'], 'hari raya') !== false
                        || stripos($r['keterangan'], 'peringatan') !== false
                        || stripos($r['keterangan'], 'kegiatan') !== false
                        || stripos($r['keterangan'], 'hut') !== false);

                    if ($isHoliday) {
                        $label = trim(strtoupper($r['keterangan']));
                        $safety = 0;
                        while ($cur <= $end && $safety++ < 90) {
                            $dStr = date('Y-m-d', $cur);
                            if ($dStr >= $startDate && $dStr <= $endDate) {
                                if (!isset($holidayMap[$dStr]) || $r['jenis'] === 'libur') {
                                    $holidayMap[$dStr] = [
                                        'type' => 'libur',
                                        'label' => $label ?: 'HARI LIBUR'
                                    ];
                                }
                            }
                            $cur = strtotime('+1 day', $cur);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}

        // 2. Set HARI MINGGU & pastikan 17 Agustus otomatis ada jika di bulan Agustus
        $cur = strtotime($startDate);
        $end = strtotime($endDate);
        while ($cur <= $end) {
            $dStr = date('Y-m-d', $cur);
            $w = date('w', $cur);
            $m_d = date('m-d', $cur);

            if ($m_d === '08-17' && !isset($holidayMap[$dStr])) {
                $holidayMap[$dStr] = [
                    'type' => 'libur',
                    'label' => 'HUT KEMERDEKAAN RI'
                ];
            }

            if ($w == 0) {
                $existing = $holidayMap[$dStr]['label'] ?? '';
                $holidayMap[$dStr] = [
                    'type' => 'minggu',
                    'label' => 'HARI MINGGU' . (!empty($existing) && $existing !== 'HARI MINGGU' ? " ({$existing})" : '')
                ];
            }
            $cur = strtotime('+1 day', $cur);
        }

        return $holidayMap;
    }

    public static function getKalenderLiburDates($db_siakad, $startDate = null, $endDate = null) {
        if (!$startDate) $startDate = date('Y-01-01');
        if (!$endDate) $endDate = date('Y-12-31');
        $map = self::getKalenderHolidaysMap($db_siakad, $startDate, $endDate);
        $liburDates = [];
        foreach ($map as $d => $info) {
            $liburDates[$d] = true;
        }
        return $liburDates;
    }

    public static function saveOrUpdateKehadiran($db_siakad, $tanggal, $guru_id, $status = 'Hadir', $keterangan = null) {
        self::ensureAbsensiGuruColumns($db_siakad);
        
        if ($status === 'Alpa') {
            $waktu_masuk = null;
            $waktu_pulang = null;
            $created_at = $tanggal . ' 07:00:00';
            if (!$keterangan) $keterangan = 'Alpa (Tanpa Keterangan)';
        } else {
            // Waktu masuk antara jam 07.00 sampai 07.30
            $waktu_masuk = self::generateRandomTime(7, 0, 7, 30);
            // Waktu pulang antara jam 13.15 sampai 13.30
            $waktu_pulang = self::generateRandomTime(13, 15, 13, 30);
            $created_at = $tanggal . ' ' . $waktu_masuk;
        }

        $stmtCek = $db_siakad->prepare("SELECT id FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
        $stmtCek->execute([$tanggal, $guru_id]);
        $existing = $stmtCek->fetch();

        if ($existing) {
            $stmtUp = $db_siakad->prepare("UPDATE absensi_guru 
                SET status = ?, 
                    waktu_masuk = ?, waktu_keluar = ?, 
                    jam_masuk = ?, jam_pulang = ?, 
                    keterangan = COALESCE(?, keterangan),
                    created_at = ? 
                WHERE id = ?");
            $stmtUp->execute([$status, $waktu_masuk, $waktu_pulang, $waktu_masuk, $waktu_pulang, $keterangan, $created_at, $existing['id']]);
        } else {
            $stmtIn = $db_siakad->prepare("INSERT INTO absensi_guru 
                (tanggal, guru_id, status, waktu_masuk, waktu_keluar, jam_masuk, jam_pulang, keterangan, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtIn->execute([$tanggal, $guru_id, $status, $waktu_masuk, $waktu_pulang, $waktu_masuk, $waktu_pulang, $keterangan, $created_at]);
        }

        return [
            'guru_id' => $guru_id,
            'status' => $status,
            'waktu_masuk' => $waktu_masuk,
            'waktu_pulang' => $waktu_pulang
        ];
    }

    public static function hadirkanSertifikasi() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/login');
            exit;
        }

        $tanggal = $_REQUEST['tanggal'] ?? date('Y-m-d');
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        self::ensureAbsensiGuruColumns($db_siakad);

        // Cek jika mode tanggal tunggal dan tanggal tersebut adalah hari libur/Minggu
        if (!empty($tanggal)) {
            $holMap = self::getKalenderHolidaysMap($db_siakad, $tanggal, $tanggal);
            if (isset($holMap[$tanggal])) {
                $lbl = $holMap[$tanggal]['label'];
                $msg = "Tanggal " . date('d/m/Y', strtotime($tanggal)) . " adalah {$lbl}. Tidak ada Alpa di hari libur/kegiatan.";
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=libur&libur_nama=" . urlencode($lbl));
                exit;
            }
        }

        // Cari semua guru yang berstatus sertifikasi secara dinamis
        $stmt = $db_core->query("
            SELECT id, nama, nip, status_kepegawaian, nrg, no_sertifikat 
            FROM guru 
            WHERE LOWER(TRIM(status_kepegawaian)) LIKE '%sertifikasi%'
               OR (nrg IS NOT NULL AND TRIM(nrg) != '' AND nrg != '-')
               OR (no_sertifikat IS NOT NULL AND TRIM(no_sertifikat) != '' AND no_sertifikat != '-')
               OR (no_peserta_sertifikasi IS NOT NULL AND TRIM(no_peserta_sertifikasi) != '' AND no_peserta_sertifikasi != '-')
        ");
        $gurus = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

        $processed = [];
        foreach ($gurus as $g) {
            $res = self::saveOrUpdateKehadiran($db_siakad, $tanggal, $g['id'], 'Hadir');
            $res['nama'] = $g['nama'];
            $processed[] = $res;
        }

        $count = count($processed);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menghadirkan {$count} guru sertifikasi pada tanggal {$tanggal}.",
                'count' => $count,
                'data' => $processed
            ]);
            exit;
        }

        redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=sertifikasi_success&count={$count}");
        exit;
    }

    public static function hadirkanAdaJam() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/login');
            exit;
        }

        $tanggal = $_REQUEST['tanggal'] ?? date('Y-m-d');
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        self::ensureAbsensiGuruColumns($db_siakad);

        // Cek jika mode tanggal tunggal dan tanggal tersebut adalah hari libur/Minggu
        if (!empty($tanggal)) {
            $holMap = self::getKalenderHolidaysMap($db_siakad, $tanggal, $tanggal);
            if (isset($holMap[$tanggal])) {
                $lbl = $holMap[$tanggal]['label'];
                $msg = "Tanggal " . date('d/m/Y', strtotime($tanggal)) . " adalah {$lbl}. Tidak ada Alpa di hari libur/kegiatan.";
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=libur&libur_nama=" . urlencode($lbl));
                exit;
            }
        }

        $dayNum = date('w', strtotime($tanggal));
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $hari = $hariMap[$dayNum] ?? '';

        if ($hari === 'Minggu') {
            $msg = "Hari ini adalah hari Minggu (Libur KBM). Tidak ada jadwal mengajar reguler.";
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=minggu_libur");
            exit;
        }

        $stmtJam = $db_siakad->prepare("SELECT DISTINCT guru_id FROM jadwal_pelajaran WHERE LOWER(hari) = LOWER(?)");
        $stmtJam->execute([$hari]);
        $rawIds = $stmtJam->fetchAll(\PDO::FETCH_COLUMN);
        $guruIds = array_filter(array_map('intval', $rawIds), function($id) { return $id > 0; });

        if (empty($guruIds)) {
            $msg = "Tidak ditemukan guru yang memiliki jadwal mengajar pada hari {$hari}.";
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=no_schedule");
            exit;
        }

        $in = implode(',', $guruIds);
        $gurus = $db_core->query("SELECT id, nama FROM guru WHERE id IN ($in)")->fetchAll(\PDO::FETCH_ASSOC);

        $processed = [];
        foreach ($gurus as $g) {
            $res = self::saveOrUpdateKehadiran($db_siakad, $tanggal, $g['id'], 'Hadir');
            $res['nama'] = $g['nama'];
            $processed[] = $res;
        }

        $count = count($processed);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menghadirkan {$count} guru yang memiliki jadwal jam mengajar pada hari {$hari} ({$tanggal}).",
                'count' => $count,
                'data' => $processed
            ]);
            exit;
        }

        redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=jam_success&count={$count}&hari={$hari}");
        exit;
    }

    public static function hadirkanSemuaOtomatis() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/login');
            exit;
        }

        $tanggal = $_REQUEST['tanggal'] ?? date('Y-m-d');
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        self::ensureAbsensiGuruColumns($db_siakad);

        // Cek jika mode tanggal tunggal dan tanggal tersebut adalah hari libur/Minggu
        if (!empty($tanggal)) {
            $holMap = self::getKalenderHolidaysMap($db_siakad, $tanggal, $tanggal);
            if (isset($holMap[$tanggal])) {
                $lbl = $holMap[$tanggal]['label'];
                $msg = "Tanggal " . date('d/m/Y', strtotime($tanggal)) . " adalah {$lbl}. Tidak ada Alpa di hari libur/kegiatan.";
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=libur&libur_nama=" . urlencode($lbl));
                exit;
            }
        }

        $dayNum = date('w', strtotime($tanggal));
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $hari = $hariMap[$dayNum] ?? '';

        // 1. Ambil guru sertifikasi (hadir setiap hari aktif)
        $stmtSertif = $db_core->query("
            SELECT id, nama, nip, status_kepegawaian, nrg, no_sertifikat 
            FROM guru 
            WHERE LOWER(TRIM(status_kepegawaian)) LIKE '%sertifikasi%'
               OR (nrg IS NOT NULL AND TRIM(nrg) != '' AND nrg != '-')
               OR (no_sertifikat IS NOT NULL AND TRIM(no_sertifikat) != '' AND no_sertifikat != '-')
               OR (no_peserta_sertifikasi IS NOT NULL AND TRIM(no_peserta_sertifikasi) != '' AND no_peserta_sertifikasi != '-')
        ");
        $guruSertif = $stmtSertif ? $stmtSertif->fetchAll(\PDO::FETCH_ASSOC) : [];
        $sertifIds = array_column($guruSertif, 'id');

        // 2. Ambil guru yang ada jam di hari $hari
        $guruJamIds = [];
        if ($hari !== 'Minggu') {
            $stmtJam = $db_siakad->prepare("SELECT DISTINCT guru_id FROM jadwal_pelajaran WHERE LOWER(hari) = LOWER(?)");
            $stmtJam->execute([$hari]);
            $rawIds = $stmtJam->fetchAll(\PDO::FETCH_COLUMN);
            $guruJamIds = array_filter(array_map('intval', $rawIds), function($id) { return $id > 0; });
        }

        // Gabungkan kedua kelompok guru
        $targetIds = array_values(array_unique(array_merge($sertifIds, $guruJamIds)));

        if (empty($targetIds)) {
            $msg = "Tidak ada guru sertifikasi atau guru berjadwal yang ditemukan.";
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=no_target");
            exit;
        }

        $in = implode(',', $targetIds);
        $gurus = $db_core->query("SELECT id, nama FROM guru WHERE id IN ($in)")->fetchAll(\PDO::FETCH_ASSOC);

        $processed = [];
        foreach ($gurus as $g) {
            $res = self::saveOrUpdateKehadiran($db_siakad, $tanggal, $g['id'], 'Hadir');
            $res['nama'] = $g['nama'];
            $res['is_sertif'] = in_array($g['id'], $sertifIds);
            $res['has_jam'] = in_array($g['id'], $guruJamIds);
            $processed[] = $res;
        }

        $count = count($processed);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menghadirkan {$count} guru (Semua Sertifikasi + Guru Ada Jam Hari {$hari}) pada tanggal {$tanggal}.",
                'count' => $count,
                'data' => $processed
            ]);
            exit;
        }

        redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=all_success&count={$count}&hari={$hari}");
        exit;
    }

    public static function generateBulan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/login');
            exit;
        }

        $bulan = sprintf('%02d', intval($_REQUEST['bulan'] ?? date('m')));
        $tahun = intval($_REQUEST['tahun'] ?? date('Y'));
        $mode = $_REQUEST['mode'] ?? 'kombinasi'; // 'kombinasi', 'sertifikasi', 'jam', 'semua'
        $isiAlpa = !empty($_REQUEST['isi_alpa']) ? true : false;

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        self::ensureAbsensiGuruColumns($db_siakad);

        // 3. Ambil pemetaan jadwal guru per hari & libur
        $jadwalPerHari = self::getJadwalPerHari($db_siakad);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($bulan), intval($tahun));
        $firstDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $lastDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $daysInMonth);
        $holidayMap = self::getKalenderHolidaysMap($db_siakad, $firstDate, $lastDate);

        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $today = date('Y-m-d');

        $totalHadirGenerated = 0;
        $totalAlpaGenerated = 0;
        $totalHariAktif = 0;

        // Otomatis bersihkan data absensi di masa depan (> hari ini) jika ada yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");

        $db_siakad->beginTransaction();
        try {
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);

                // JANGAN GENERATE TANGGAL YANG BELUM DILEWATI (Masa Depan)
                if ($tanggal > $today) {
                    continue;
                }

                $dayNum = date('w', strtotime($tanggal));
                $hari = $hariMap[$dayNum] ?? '';

                // Skip hari Minggu (Libur KBM) & Libur Kalender
                if ($hari === 'Minggu' || isset($holidayMap[$tanggal])) {
                    continue;
                }

                $totalHariAktif++;
                $guruJamHariIni = $jadwalPerHari[$hari] ?? [];

                // Tentukan target guru yang dihadirkan sesuai mode yang dipilih
                if ($mode === 'sertifikasi') {
                    $targetGuruIds = $sertifIds;
                } elseif ($mode === 'jam') {
                    $targetGuruIds = $guruJamHariIni;
                } elseif ($mode === 'semua') {
                    $targetGuruIds = $allGuruIds;
                } else { // kombinasi (default)
                    $targetGuruIds = array_values(array_unique(array_merge($sertifIds, $guruJamHariIni)));
                }

                foreach ($targetGuruIds as $gid) {
                    self::saveOrUpdateKehadiran($db_siakad, $tanggal, $gid, 'Hadir');
                    $totalHadirGenerated++;
                }

                // Jika opsi isi_alpa dicentang: guru yang terjadwal tapi tidak dihadirkan, ditandai Alpa
                if ($isiAlpa && $tanggal <= $today) {
                    $guruJadwalHariIni = array_values(array_unique(array_merge($sertifIds, $guruJamHariIni)));
                    $guruKosong = array_diff($guruJadwalHariIni, $targetGuruIds);
                    foreach ($guruKosong as $gid) {
                        self::saveOrUpdateKehadiran($db_siakad, $tanggal, $gid, 'Alpa', 'Alpa Otomatis (Tanpa Keterangan)');
                        $totalAlpaGenerated++;
                    }
                }
            }
            if ($db_siakad->inTransaction()) {
                $db_siakad->commit();
            }
        } catch (\Exception $e) {
            if ($db_siakad->inTransaction()) {
                $db_siakad->rollBack();
            }
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal generate: ' . $e->getMessage()]);
                exit;
            }
            redirect("/admin/absen_guru_v2/rekap-bulanan?bulan={$bulan}&tahun={$tahun}&msg=error&err=" . urlencode($e->getMessage()));
            exit;
        }

        $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
        $namaBulan = $bulans[$bulan] ?? $bulan;

        $modeNames = [
            'sertifikasi' => 'Khusus Semua Guru Sertifikasi',
            'jam' => 'Khusus Guru Sesuai Jam Mengajar',
            'semua' => 'Semua Dewan Guru Madrasah',
            'kombinasi' => 'Kombinasi (Sertifikasi Tiap Hari + Non-Sertif Ada Jam)'
        ];
        $namaMode = $modeNames[$mode] ?? 'Kombinasi Otomatis';

        $msgText = "Berhasil men-generate absensi 1 bulan ($namaBulan $tahun) mode [$namaMode]: $totalHadirGenerated data kehadiran ($totalHariAktif hari aktif).";
        if ($totalAlpaGenerated > 0) {
            $msgText .= " Serta $totalAlpaGenerated status Alpa untuk guru berjadwal yang kosong.";
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msgText,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'mode' => $mode,
                'total_hadir' => $totalHadirGenerated,
                'total_alpa' => $totalAlpaGenerated,
                'hari_aktif' => $totalHariAktif
            ]);
            exit;
        }

        redirect("/admin/absen_guru_v2/rekap-bulanan?bulan={$bulan}&tahun={$tahun}&msg=generate_success&total={$totalHadirGenerated}&hari={$totalHariAktif}&mode=" . urlencode($mode));
        exit;
    }

    public static function autoAlpaKosong() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/login');
            exit;
        }

        $tanggal = $_REQUEST['tanggal'] ?? null;
        $bulan = isset($_REQUEST['bulan']) ? sprintf('%02d', intval($_REQUEST['bulan'])) : null;
        $tahun = isset($_REQUEST['tahun']) ? intval($_REQUEST['tahun']) : null;

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        self::ensureAbsensiGuruColumns($db_siakad);

        // Cek jika mode tanggal tunggal dan tanggal tersebut adalah hari libur/Minggu
        if (!empty($tanggal)) {
            $holMap = self::getKalenderHolidaysMap($db_siakad, $tanggal, $tanggal);
            if (isset($holMap[$tanggal])) {
                $lbl = $holMap[$tanggal]['label'];
                $msg = "Tanggal " . date('d/m/Y', strtotime($tanggal)) . " adalah {$lbl}. Tidak ada Alpa di hari libur/kegiatan.";
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tanggal}&msg=libur&libur_nama=" . urlencode($lbl));
                exit;
            }
        }

        $sertifIds = self::getSertifikasiGuruIds($db_core);
        $jadwalPerHari = self::getJadwalPerHari($db_siakad);
        $liburDates = self::getKalenderLiburDates($db_siakad);
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

        $targetDates = [];
        $today = date('Y-m-d');
        if (!empty($tanggal)) {
            $targetDates[] = $tanggal;
        } elseif (!empty($bulan) && !empty($tahun)) {
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($bulan), intval($tahun));
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                if ($tgl <= $today) {
                    $targetDates[] = $tgl;
                }
            }
        } else {
            $targetDates[] = $today;
        }

        $totalAlpa = 0;
        $db_siakad->beginTransaction();
        try {
            foreach ($targetDates as $tgl) {
                $dayNum = date('w', strtotime($tgl));
                $hari = $hariMap[$dayNum] ?? '';
                if ($hari === 'Minggu' || isset($liburDates[$tgl])) {
                    continue; // Skip libur
                }

                $guruJam = $jadwalPerHari[$hari] ?? [];
                // Guru yang seharusnya hadir pada tanggal ini:
                // 1. Guru Sertifikasi (wajib hadir setiap hari kerja)
                // 2. Guru yang punya jam mengajar di hari ini
                $wajibHadirIds = array_values(array_unique(array_merge($sertifIds, $guruJam)));

                // Ambil guru yang SUDAH absen di tanggal ini
                $stmtExisting = $db_siakad->prepare("SELECT guru_id FROM absensi_guru WHERE tanggal = ?");
                $stmtExisting->execute([$tgl]);
                $alreadyAbsenIds = array_map('intval', $stmtExisting->fetchAll(\PDO::FETCH_COLUMN));

                // Cari guru yang wajib hadir tapi KOSONG (belum ada record absen)
                $kosongIds = array_diff($wajibHadirIds, $alreadyAbsenIds);

                foreach ($kosongIds as $gid) {
                    self::saveOrUpdateKehadiran($db_siakad, $tgl, $gid, 'Alpa', 'Alpa Otomatis (Tanpa Keterangan)');
                    $totalAlpa++;
                }
            }

            if ($db_siakad->inTransaction()) {
                $db_siakad->commit();
            }
        } catch (\Exception $e) {
            if ($db_siakad->inTransaction()) {
                $db_siakad->rollBack();
            }
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal auto-alpa: ' . $e->getMessage()]);
                exit;
            }
            redirect("/admin/absen_guru_v2/rekap-harian?msg=error&err=" . urlencode($e->getMessage()));
            exit;
        }

        $msg = "Berhasil menandai {$totalAlpa} status ALPA otomatis untuk guru yang terjadwal hadir tetapi belum ada absensi.";
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $msg, 'total_alpa' => $totalAlpa]);
            exit;
        }

        if (!empty($bulan)) {
            redirect("/admin/absen_guru_v2/rekap-bulanan?bulan={$bulan}&tahun={$tahun}&msg=auto_alpa_success&count={$totalAlpa}");
        } else {
            $tglRedirect = !empty($tanggal) ? $tanggal : date('Y-m-d');
            redirect("/admin/absen_guru_v2/rekap-harian?tanggal={$tglRedirect}&msg=auto_alpa_success&count={$totalAlpa}");
        }
        exit;
    }

    public static function rekapHarian() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");

        self::ensureAbsensiGuruColumns($db_siakad);

        $core_db = 'aula_db';
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $core_db = $config['core']['db_name'] ?? 'aula_db';
        } catch (\Exception $e) {}

        $dayNum = date('w', strtotime($tanggal));
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $hari = $hariMap[$dayNum] ?? '';

        // Guru Sertifikasi (Hadir Tiap Hari)
        $stmtSertif = $db_core->query("
            SELECT id, nama, nip, status_kepegawaian, nrg, no_sertifikat 
            FROM guru 
            WHERE LOWER(TRIM(status_kepegawaian)) LIKE '%sertifikasi%'
               OR (nrg IS NOT NULL AND TRIM(nrg) != '' AND nrg != '-')
               OR (no_sertifikat IS NOT NULL AND TRIM(no_sertifikat) != '' AND no_sertifikat != '-')
               OR (no_peserta_sertifikasi IS NOT NULL AND TRIM(no_peserta_sertifikasi) != '' AND no_peserta_sertifikasi != '-')
            ORDER BY nama ASC
        ");
        $guruSertifList = $stmtSertif ? $stmtSertif->fetchAll(\PDO::FETCH_ASSOC) : [];
        $guruSertifIds = array_column($guruSertifList, 'id');

        // Guru Ada Jam pada hari $hari
        $guruJamIds = [];
        if ($hari !== 'Minggu') {
            $stmtJam = $db_siakad->prepare("SELECT DISTINCT guru_id FROM jadwal_pelajaran WHERE LOWER(hari) = LOWER(?)");
            $stmtJam->execute([$hari]);
            $rawIds = $stmtJam->fetchAll(\PDO::FETCH_COLUMN);
            $guruJamIds = array_filter(array_map('intval', $rawIds), function($id) { return $id > 0; });
        }

        $sql = "SELECT a.*, g.nama, g.nip, g.status_kepegawaian, g.nrg, g.no_sertifikat, g.jabatan 
                FROM absensi_guru a 
                LEFT JOIN {$core_db}.guru g ON a.guru_id = g.id 
                WHERE a.tanggal = :tanggal 
                ORDER BY g.nama ASC";
        
        $stmt = $db_siakad->prepare($sql);
        $stmt->execute([':tanggal' => $tanggal]);
        $absensi = $stmt->fetchAll();

        // Rekap ringkas stats
        $stats = [
            'total_guru' => count($db_core->query("SELECT id FROM guru")->fetchAll()),
            'sertifikasi' => count($guruSertifIds),
            'ada_jam' => count($guruJamIds),
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];
        foreach ($absensi as $ab) {
            $st = $ab['status'] ?? 'Hadir';
            if ($st === 'Hadir') $stats['hadir']++;
            elseif ($st === 'Terlambat') $stats['terlambat']++;
            elseif (in_array($st, ['Izin', 'Dinas Luar'])) $stats['izin']++;
            elseif ($st === 'Sakit') $stats['sakit']++;
            else $stats['alpa']++;
        }

        $title = "Rekap Harian Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/rekap_harian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function rekapBulanan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $jabatan_filter = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';
        $guru_filter = isset($_GET['guru']) ? $_GET['guru'] : '';

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");
        // Bersihkan juga absensi di hari Minggu jika sempat ter-generate
        try {
            $db_siakad->exec("DELETE FROM absensi_guru WHERE DAYOFWEEK(tanggal) = 1");
        } catch (\Throwable $e) {}

        $where = ["1=1"];
        $params = [];
        if (!empty($jabatan_filter)) {
            $where[] = "jabatan = ?";
            $params[] = $jabatan_filter;
        }
        if (!empty($guru_filter)) {
            $where[] = "id = ?";
            $params[] = $guru_filter;
        }
        $whereSql = implode(" AND ", $where);

        $guruList = $db_core->prepare("SELECT id, nama, nip, npk, jabatan FROM guru WHERE $whereSql ORDER BY nama ASC");
        $guruList->execute($params);
        $guruList = $guruList->fetchAll();
        
        $jabatanList = $db_core->query("SELECT DISTINCT jabatan FROM guru WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $allGuru = $db_core->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
        $firstDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $lastDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $daysInMonth);
        $holidayMap = self::getKalenderHolidaysMap($db_siakad, $firstDate, $lastDate);

        // Hapus juga data absensi pada tanggal-tanggal libur pasti
        try {
            $liburPasti = array_keys(array_filter($holidayMap, function($h) { return $h['type'] === 'libur'; }));
            if (!empty($liburPasti)) {
                $inLib = "'" . implode("','", $liburPasti) . "'";
                $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal IN ($inLib)");
            }
        } catch (\Throwable $e) {}

        $dates_range = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dates_range[] = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
        }

        $sertifIds = self::getSertifikasiGuruIds($db_core);
        $sertifMap = array_flip($sertifIds);
        $jadwalPerHari = self::getJadwalPerHari($db_siakad);
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $today = date('Y-m-d');

        $rekap = [];
        foreach ($guruList as $g) {
            $stmtDetail = $db_siakad->prepare("SELECT tanggal, status FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
            $stmtDetail->execute([$g['id'], $bulan, $tahun]);
            $harianMap = $stmtDetail->fetchAll(\PDO::FETCH_KEY_PAIR); // tanggal => status
            
            $r = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
            $isSertif = isset($sertifMap[$g['id']]);

            foreach ($dates_range as $dt) {
                // JANGAN HITUNG HADIR ATAU ALPA PADA HARI MINGGU ATAU HARI LIBUR
                if (isset($holidayMap[$dt])) {
                    continue;
                }

                $st = $harianMap[$dt] ?? null;
                $dayOfWeek = date('w', strtotime($dt));
                $namaHari = $hariMap[$dayOfWeek] ?? '';

                if ($st) {
                    if ($st === 'Dinas Luar') $r['Izin']++;
                    elseif (isset($r[$st])) $r[$st]++;
                    else $r['Hadir']++;
                } else {
                    // Jika kosong dan tanggal hari kerja yang sudah berjalan (<= today)
                    if ($dt <= $today) {
                        $hasJam = in_array($g['id'], $jadwalPerHari[$namaHari] ?? []);
                        // Auto-Alpa jika guru sertifikasi (wajib tiap hari) ATAU guru ada jam mengajar di hari ini
                        if ($isSertif || $hasJam) {
                            $r['Alpa']++;
                            $harianMap[$dt] = 'Alpa'; // Tampilkan 'A' di matriks harian
                        }
                    }
                }
            }

            $g['harian'] = $harianMap;
            $g['rekap'] = $r;
            $g['is_sertif'] = $isSertif;
            $rekap[] = $g;
        }

        $title = "Rekap Bulanan Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];
        
        $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

        // 1. Cetak Ringkas Laporan Bulanan (Portrait)
        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_guru_v2/cetak_rekap_bulanan.php';
            exit;
        }
        
        // 2. Cetak Detail 1 Guru (Individual Portrait)
        if (isset($_GET['cetak_detail']) && $_GET['cetak_detail'] == 1 && !empty($_GET['guru_id'])) {
            $guru_id = intval($_GET['guru_id']);
            $stmtGuru = $db_core->prepare("SELECT id, nama, nip, npk, jabatan, status_kepegawaian, nrg, no_sertifikat FROM guru WHERE id = ?");
            $stmtGuru->execute([$guru_id]);
            $guruDetail = $stmtGuru->fetch();
            
            if ($guruDetail) {
                $stmtDetail = $db_siakad->prepare("SELECT tanggal, waktu_masuk, waktu_keluar, jam_masuk, jam_pulang, status, keterangan FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY tanggal ASC");
                $stmtDetail->execute([$guru_id, $bulan, $tahun]);
                $detailAbsen = $stmtDetail->fetchAll();
                
                include __DIR__ . '/../../resources/views/admin/absen_guru_v2/cetak_rekap_bulanan_detail.php';
                exit;
            }
        }

        // 3. Cetak Detail 1 Bulan Global Matriks Tanggal 1 s.d. 31 (Landscape kayak Siswa)
        if ((isset($_GET['cetak_global']) && $_GET['cetak_global'] == 1) || (isset($_GET['cetak_detail']) && $_GET['cetak_detail'] == 1 && empty($_GET['guru_id']))) {
            $matrixGuru = $rekap;
            include __DIR__ . '/../../resources/views/admin/absen_guru_v2/cetak_rekap_bulanan_global.php';
            exit;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/rekap_bulanan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function inputManual() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db_core   = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');

        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, guru_id INT NOT NULL,
            status VARCHAR(50) DEFAULT 'Hadir',
            waktu_masuk TIME NULL, waktu_keluar TIME NULL, keterangan VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        $guruList = $db_core->query("SELECT id, nama, jabatan FROM guru ORDER BY nama ASC")->fetchAll();
        $tanggal  = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

        // Ambil data absen yang sudah ada untuk tanggal ini, di-index by guru_id
        $existingAbsenByGuruId = [];
        $stmtEx = $db_siakad->prepare("SELECT * FROM absensi_guru WHERE tanggal = ?");
        $stmtEx->execute([$tanggal]);
        foreach ($stmtEx->fetchAll() as $row) {
            $existingAbsenByGuruId[$row['guru_id']] = $row;
        }

        $msg     = isset($_GET['msg']) ? $_GET['msg'] : null;
        $title   = "Input Manual Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/input_manual.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function saveInputManual() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db_siakad = \App\Core\Database::connect('siakad');
        $tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
        $absenData = $_POST['absen'] ?? [];

        foreach ($absenData as $guru_id => $data) {
            $guru_id   = intval($guru_id);
            $status    = $data['status']     ?? 'Hadir';
            $jam_masuk = !empty($data['waktu_masuk'])  ? $data['waktu_masuk']  : null;
            $jam_pulang= !empty($data['waktu_keluar']) ? $data['waktu_keluar'] : null;
            $keterangan= $data['keterangan'] ?? null;

            $cek = $db_siakad->prepare("SELECT id FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
            $cek->execute([$tanggal, $guru_id]);
            $existing = $cek->fetch();

            if ($existing) {
                $db_siakad->prepare("UPDATE absensi_guru SET status = ?, waktu_masuk = ?, waktu_keluar = ?, keterangan = ? WHERE id = ?")
                          ->execute([$status, $jam_masuk, $jam_pulang, $keterangan, $existing['id']]);
            } else {
                if ($jam_masuk !== null || $jam_pulang !== null || !empty($keterangan) || $status !== 'Hadir') {
                    $db_siakad->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, waktu_masuk, waktu_keluar, keterangan, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())")
                              ->execute([$tanggal, $guru_id, $status, $jam_masuk, $jam_pulang, $keterangan]);
                }
            }
        }

        header('Location: /admin/absen_guru_v2/input-manual?tanggal=' . $tanggal . '&msg=success');
        exit;
    }

    public static function ketidakhadiran() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Otomatis bersihkan data absensi di masa depan (> hari ini) yang terlanjur ter-generate
        $db_siakad->exec("DELETE FROM absensi_guru WHERE tanggal > CURDATE()");

        $sql = "SELECT a.*, g.nama, g.nip 
                FROM absensi_guru a 
                JOIN aula_db.guru g ON a.guru_id = g.id 
                WHERE a.tanggal = ? AND a.status IN ('Sakit', 'Izin', 'Alpa')
                ORDER BY g.nama ASC";
        
        $stmt = $db_siakad->prepare($sql);
        $stmt->execute([$tanggal]);
        $absensi = $stmt->fetchAll();
        
        $title = "Data Ketidakhadiran Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/ketidakhadiran.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function pengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $db = Database::connect('core');
        
        $stmt = $db->prepare("SELECT kunci, nilai FROM absensi_pengaturan_guru");
        $stmt->execute();
        $pengaturan = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        // Default values
        if (!isset($pengaturan['jam_masuk'])) $pengaturan['jam_masuk'] = '07:00:00';
        if (!isset($pengaturan['batas_terlambat'])) $pengaturan['batas_terlambat'] = '07:30:00';
        if (!isset($pengaturan['jam_pulang'])) $pengaturan['jam_pulang'] = '14:30:00';
        if (!isset($pengaturan['jam_pulang_jumat'])) $pengaturan['jam_pulang_jumat'] = '11:00:00';
        if (!isset($pengaturan['jam_pulang_sabtu'])) $pengaturan['jam_pulang_sabtu'] = '12:00:00';
        if (!isset($pengaturan['teks_suara_masuk'])) $pengaturan['teks_suara_masuk'] = 'Halo [nama], Anda berhasil absen masuk';
        if (!isset($pengaturan['teks_suara_pulang'])) $pengaturan['teks_suara_pulang'] = 'Terima kasih [nama], hati-hati di jalan';
        if (!isset($pengaturan['teks_suara_sholat'])) $pengaturan['teks_suara_sholat'] = 'Halo [nama], Anda berhasil absen sholat';
        if (!isset($pengaturan['teks_suara_gagal'])) $pengaturan['teks_suara_gagal'] = 'Maaf [nama], Anda sudah absen sebelumnya';

        $title = "Konfigurasi Jam Absen Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_guru_v2/pengaturan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function savePengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = Database::connect('core');
        
        $jam_masuk = $_POST['jam_masuk'] ?? '';
        $batas_terlambat = $_POST['batas_terlambat'] ?? '';
        $jam_pulang = $_POST['jam_pulang'] ?? '';
        $jam_pulang_jumat = $_POST['jam_pulang_jumat'] ?? '';
        $jam_pulang_sabtu = $_POST['jam_pulang_sabtu'] ?? '';

        try {
            $stmt = $db->prepare("INSERT INTO absensi_pengaturan_guru (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
            $stmt->execute(['jam_masuk', $jam_masuk]);
            $stmt->execute(['batas_terlambat', $batas_terlambat]);
            $stmt->execute(['jam_pulang', $jam_pulang]);
            $stmt->execute(['jam_pulang_jumat', $jam_pulang_jumat]);
            $stmt->execute(['jam_pulang_sabtu', $jam_pulang_sabtu]);
            
            if (isset($_POST['teks_suara_masuk'])) $stmt->execute(['teks_suara_masuk', $_POST['teks_suara_masuk']]);
            if (isset($_POST['teks_suara_pulang'])) $stmt->execute(['teks_suara_pulang', $_POST['teks_suara_pulang']]);
            if (isset($_POST['teks_suara_sholat'])) $stmt->execute(['teks_suara_sholat', $_POST['teks_suara_sholat']]);
            if (isset($_POST['teks_suara_gagal'])) $stmt->execute(['teks_suara_gagal', $_POST['teks_suara_gagal']]);

            echo json_encode(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
