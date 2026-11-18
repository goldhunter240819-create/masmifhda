<?php
session_name("AULA_SYSTEM_PORTAL");
if (session_status() === PHP_SESSION_NONE) {
    session_save_path(__DIR__ . '/../storage/sessions');
    ini_set('session.gc_maxlifetime', 31536000);
    ini_set('session.cookie_lifetime', 31536000);
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $core_db = new PDO("mysql:host=$host;dbname=aula_db;charset=utf8mb4", $user, $pass);
    $core_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $core_db->exec("SET time_zone = '+07:00'");
    
    $siakad_db = new PDO("mysql:host=$host;dbname=aula_siakad_db;charset=utf8mb4", $user, $pass);
    $siakad_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $siakad_db->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Auto-Login mechanism (Remember Me / Sesi Awet)
if (!isset($_SESSION['siswa_id']) && isset($_COOKIE['apk_remember_cbt'])) {
    $cbt_id = $_COOKIE['apk_remember_cbt'];
    try {
        $stmt_c = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = :id AND status = 'Aktif' LIMIT 1");
        $stmt_c->execute(['id' => $cbt_id]);
        $c_cookie = $stmt_c->fetch(PDO::FETCH_ASSOC);
        if ($c_cookie) {
            $_SESSION['is_cbt_only'] = true;
            $_SESSION['siswa_id'] = $c_cookie['id'];
            $_SESSION['cbt_khusus_id'] = $c_cookie['id'];
            $_SESSION['siswa_nama'] = $c_cookie['nama_siswa'];
            $_SESSION['siswa_nis'] = $c_cookie['no_peserta'];
            $_SESSION['siswa_status'] = 'Kelas Jauh';
            $_SESSION['role_id'] = 7;
            $_SESSION['user_id'] = 900000 + (int)$c_cookie['id'];
            $_SESSION['username'] = $c_cookie['username'];
            $_SESSION['nama'] = $c_cookie['nama_siswa'];
            $_SESSION['tingkat_kelas'] = $c_cookie['tingkat'];
            $_SESSION['rombel_kelas'] = $c_cookie['kelas_nama'];
        } else {
            setcookie('apk_remember_cbt', '', time() - 3600, "/");
        }
    } catch (Exception $e) {}
}

if (!isset($_SESSION['siswa_id']) && isset($_COOKIE['apk_remember_siswa'])) {
    $cookie_id = $_COOKIE['apk_remember_siswa'];
    try {
        $stmt_c = $core_db->prepare("SELECT id, nama, nis, status FROM siswa WHERE id = :id LIMIT 1");
        $stmt_c->execute(['id' => $cookie_id]);
        $s_cookie = $stmt_c->fetch(PDO::FETCH_ASSOC);
        if ($s_cookie) {
            $_SESSION['siswa_id'] = $s_cookie['id'];
            $_SESSION['siswa_nama'] = $s_cookie['nama'];
            $_SESSION['siswa_nis'] = $s_cookie['nis'];
            $_SESSION['siswa_status'] = $s_cookie['status'];
        } else {
            // Jika siswa tidak valid di DB, hapus cookie
            setcookie('apk_remember_siswa', '', time() - 3600, "/");
        }
    } catch (Exception $e) {}
}

// Fungsi bantu untuk format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Global user_id assurance untuk Guru
if (isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    try {
        $stmt_u = $core_db->prepare("SELECT user_id FROM guru WHERE id = :id LIMIT 1");
        $stmt_u->execute(['id' => $_SESSION['guru_id']]);
        $g_u = $stmt_u->fetch(PDO::FETCH_ASSOC);
        if ($g_u) {
            $_SESSION['user_id'] = $g_u['user_id'];
        }
    } catch(Exception $e) {}
}

// Global Activity Tracker untuk APK (Live User Monitor integration)
if (isset($_SESSION['user_id'])) {
    $now = time();
    if (!isset($_SESSION['last_activity_ping']) || ($now - $_SESSION['last_activity_ping'] > 60)) {
        try {
            $core_db->query("UPDATE users SET last_activity = NOW() WHERE id = " . intval($_SESSION['user_id']));
            $_SESSION['last_activity_ping'] = $now;
        } catch (\Exception $e) {}
    }
} else {
    // Siswa auto-login
    if (isset($_SESSION['siswa_id'])) {
        try {
            $stmt_u = $core_db->prepare("SELECT user_id FROM siswa WHERE id = :id LIMIT 1");
            $stmt_u->execute(['id' => $_SESSION['siswa_id']]);
            $s_u = $stmt_u->fetch(PDO::FETCH_ASSOC);
            if ($s_u && $s_u['user_id']) {
                $_SESSION['user_id'] = $s_u['user_id'];
                $core_db->query("UPDATE users SET last_activity = NOW() WHERE id = " . intval($_SESSION['user_id']));
                $_SESSION['last_activity_ping'] = time();
            }
        } catch(Exception $e) {}
    }
}

// Global Visitor Tracker untuk APK
try {
    $visitorIp = 'UNKNOWN';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $visitorIp = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $visitorIp = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $visitorIp = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $visitorIp = $_SERVER['REMOTE_ADDR'];
    }

    if ($visitorIp !== 'UNKNOWN') {
        $stmt_vis = $core_db->prepare("INSERT IGNORE INTO visitor_logs (ip_address, visit_date) VALUES (?, CURDATE())");
        $stmt_vis->execute([$visitorIp]);
    }
} catch (\Exception $e) {}

// ============================================================================
// FUNGSI GLOBAL NOTIFIKASI ONESIGNAL
// ============================================================================
function sendOneSignalNotification($target_user_id, $title, $message, $data = null) {
    $settings_file = __DIR__ . '/apk_settings.json';
    if (!file_exists($settings_file)) return false;
    $settings = json_decode(file_get_contents($settings_file), true);
    
    $app_id = $settings['onesignal_app_id'] ?? '';
    $api_key = $settings['onesignal_rest_key'] ?? '';
    
    if (empty($app_id) || empty($api_key)) return false;
    
    $content = [
        "en" => $message,
        "id" => $message
    ];
    $headings = [
        "en" => $title,
        "id" => $title
    ];
    
    $fields = [
        'app_id' => $app_id,
        'headings' => $headings,
        'contents' => $content,
        // Jika target_user_id diset, kirim private. Jika kosong, kirim ke semua.
        'target_channel' => 'push'
    ];
    if ($data !== null) {
        $fields['data'] = $data;
    }
    
    if (!empty($target_user_id)) {
        // Kirim spesifik ke user tertentu (Private Notif)
        $target_array = is_array($target_user_id) ? $target_user_id : [$target_user_id];
        $fields['include_aliases'] = [
            'external_id' => $target_array
        ];
    } else {
        // Broadcast ke semua user (Global Notif)
        $fields['included_segments'] = ['Total Subscriptions'];
    }
    
    $fields = json_encode($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// ============================================================================
// FUNGSI SINKRONISASI STATUS PUSAT (GABUNGAN GATE & KELAS)
// ============================================================================
function sinkronisasi_status_pusat($siswa_id, $tanggal, $core_db, $siakad_db) {
    // 1. Cek Presensi (Gate Masuk)
    $stmt_gate = $core_db->prepare("SELECT jam_masuk, jam_pulang, status FROM presensi WHERE user_id = (SELECT user_id FROM siswa WHERE id = ?) AND tanggal = ? AND role_id = 3");
    $stmt_gate->execute([$siswa_id, $tanggal]);
    $gate = $stmt_gate->fetch(PDO::FETCH_ASSOC);
    
    $is_scan_masuk = ($gate && !empty($gate['jam_masuk']));
    
    // 2. Cek Absensi Per Mapel (Jurnal Guru)
    $stmt_mapel = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");
    $stmt_mapel->execute([$siswa_id, $tanggal]);
    $semua_status = $stmt_mapel->fetchAll(PDO::FETCH_COLUMN);
    
    $ada_hadir = in_array('Hadir', $semua_status);
    $ada_alpa = in_array('Alpa', $semua_status) || in_array('Alpha', $semua_status);
    
    $has_bolos = in_array('Bolos', $semua_status);
    
    // PRIORITAS MUTLAK: Jika semua mapel seragam tanpa Hadir, abaikan scan!
    if (in_array('Sakit', $semua_status) && !$ada_hadir && !$ada_alpa && !$has_bolos) {
        $status_final = 'Sakit';
    } elseif (in_array('Izin', $semua_status) && !$ada_hadir && !$ada_alpa && !$has_bolos) {
        $status_final = 'Izin';
    } elseif ($ada_alpa && !$ada_hadir && !in_array('Izin', $semua_status) && !in_array('Sakit', $semua_status) && !$has_bolos) {
        $status_final = 'Alpa';
    } else {
        if ($is_scan_masuk || $ada_hadir) {
            if ($ada_alpa || $has_bolos) {
                $status_final = 'Bolos'; // Berada di sekolah (via scan atau kelas) tapi cabut kelas lain
            } else {
                $status_final = 'Hadir'; // Termasuk kalau scan-nya 'Terlambat', pusatnya tetap 'Hadir'
            }
        } else {
            if (in_array('Sakit', $semua_status)) {
                $status_final = 'Sakit';
            } elseif (in_array('Izin', $semua_status)) {
                $status_final = 'Izin';
            } else {
                $status_final = 'Alpa';
            }
        }
    }
    
    // 3. Update tabel absensi_siswa
    $stmt_s = $core_db->prepare("SELECT kelas_id FROM siswa WHERE id = ?");
    $stmt_s->execute([$siswa_id]);
    $siswa = $stmt_s->fetch(PDO::FETCH_ASSOC);
    $kelas_id = $siswa ? $siswa['kelas_id'] : 0;
    
    $stmt_cek_pusat = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
    $stmt_cek_pusat->execute([$siswa_id, $tanggal]);
    if ($stmt_cek_pusat->fetch()) {
        $stmt_upd = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
        $stmt_upd->execute([$status_final, $siswa_id, $tanggal]);
    } else {
        $stmt_ins = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
        $stmt_ins->execute([$siswa_id, $kelas_id, $tanggal, $status_final]);
    }
}

// ============================================================================
// FUNGSI CEK HAK AKSES SUPERVISOR / TIM SUPERVISI
// ============================================================================
function cek_akses_supervisor($guru_id, $core_db) {
    if (!$guru_id) return false;

    // 0. Cek jika role Admin / Developer
    if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 99)) {
        return true;
    }

    try {
        // 1. Cek jika Kepala Madrasah (is_kamad = 1)
        $stmt = $core_db->prepare("SELECT is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetchColumn() == 1) return true;

        // 2. Cek jika Waka Kurikulum di guru_tugas
        $stmt = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetch()) return true;

        // 3. Cek apakah terdaftar di Tim Supervisi (kurikulum_supervisi_tim)
        $stmt = $core_db->prepare("SELECT id FROM kurikulum_supervisi_tim WHERE guru_id = ? LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetch()) return true;

        // Cek juga berdasarkan nama guru di kurikulum_supervisi_tim
        $stmt = $core_db->prepare("SELECT t.id FROM kurikulum_supervisi_tim t JOIN guru g ON g.id = ? WHERE t.nama = g.nama LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetch()) return true;

        // 4. Cek apakah ada izin spesifik di apk_akses_guru
        $stmt = $core_db->prepare("SELECT id FROM apk_akses_guru WHERE guru_id = ? AND app_code IN ('supervisi_kunjungan', 'supervisi_kelas', 'val_ekinerja', 'mon_penilaian') LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetch()) return true;

        // 5. Cek apakah ditugaskan sebagai supervisor di tabel supervisi kelas/adm
        $stmt = $core_db->prepare("SELECT id FROM kurikulum_supervisi_kelas WHERE supervisor_id = ? LIMIT 1");
        $stmt->execute([$guru_id]);
        if ($stmt->fetch()) return true;

        $stmt = $core_db->prepare("SELECT id FROM kurikulum_supervisi_administrasi WHERE supervisor_id = ? OR penilai_id = ? LIMIT 1");
        $stmt->execute([$guru_id, $guru_id]);
        if ($stmt->fetch()) return true;
    } catch (\Exception $e) {
        error_log("cek_akses_supervisor error: " . $e->getMessage());
    }

    return false;
}
?>
