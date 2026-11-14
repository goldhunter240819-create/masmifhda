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

    public static function ensureAbsensiGuruColumns($db_siakad) {
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

    public static function saveOrUpdateKehadiran($db_siakad, $tanggal, $guru_id, $status = 'Hadir') {
        self::ensureAbsensiGuruColumns($db_siakad);
        
        // Waktu masuk antara jam 07.00 sampai 07.30
        $waktu_masuk = self::generateRandomTime(7, 0, 7, 30);
        // Waktu pulang antara jam 13.15 sampai 13.30
        $waktu_pulang = self::generateRandomTime(13, 15, 13, 30);
        $created_at = $tanggal . ' ' . $waktu_masuk;

        $stmtCek = $db_siakad->prepare("SELECT id FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
        $stmtCek->execute([$tanggal, $guru_id]);
        $existing = $stmtCek->fetch();

        if ($existing) {
            $stmtUp = $db_siakad->prepare("UPDATE absensi_guru 
                SET status = ?, 
                    waktu_masuk = ?, waktu_keluar = ?, 
                    jam_masuk = ?, jam_pulang = ?, 
                    created_at = ? 
                WHERE id = ?");
            $stmtUp->execute([$status, $waktu_masuk, $waktu_pulang, $waktu_masuk, $waktu_pulang, $created_at, $existing['id']]);
        } else {
            $stmtIn = $db_siakad->prepare("INSERT INTO absensi_guru 
                (tanggal, guru_id, status, waktu_masuk, waktu_keluar, jam_masuk, jam_pulang, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtIn->execute([$tanggal, $guru_id, $status, $waktu_masuk, $waktu_pulang, $waktu_masuk, $waktu_pulang, $created_at]);
        }

        return [
            'guru_id' => $guru_id,
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
        self::ensureAbsensiGuruColumns($db_siakad);

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
        self::ensureAbsensiGuruColumns($db_siakad);

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
        self::ensureAbsensiGuruColumns($db_siakad);

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

    public static function rekapHarian() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

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

        $rekap = [];
        foreach ($guruList as $g) {
            $stmt = $db_siakad->prepare("SELECT status, COUNT(*) as total FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? GROUP BY status");
            $stmt->execute([$g['id'], $bulan, $tahun]);
            $res = $stmt->fetchAll();
            
            $r = ['Hadir'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0, 'Terlambat'=>0];
            foreach ($res as $row) {
                if ($row['status'] == 'Dinas Luar') {
                    $r['Izin'] += $row['total'];
                } else {
                    if (isset($r[$row['status']])) {
                        $r[$row['status']] += $row['total'];
                    } else {
                        $r[$row['status']] = $row['total'];
                    }
                }
            }
            $g['rekap'] = $r;
            $rekap[] = $g;
        }

        $title = "Rekap Bulanan Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];
        
        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_guru_v2/cetak_rekap_bulanan.php';
            exit;
        }
        
        if (isset($_GET['cetak_detail']) && $_GET['cetak_detail'] == 1) {
            // Khusus cetak detail guru
            $guru_id = $_GET['guru_id'] ?? 0;
            $stmtGuru = $db_core->prepare("SELECT id, nama, nip, npk, jabatan FROM guru WHERE id = ?");
            $stmtGuru->execute([$guru_id]);
            $guruDetail = $stmtGuru->fetch();
            
            if ($guruDetail) {
                $stmtDetail = $db_siakad->prepare("SELECT tanggal, waktu_masuk, waktu_keluar, status, keterangan FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY tanggal ASC");
                $stmtDetail->execute([$guru_id, $bulan, $tahun]);
                $detailAbsen = $stmtDetail->fetchAll();
                
                include __DIR__ . '/../../resources/views/admin/absen_guru_v2/cetak_rekap_bulanan_detail.php';
                exit;
            }
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
