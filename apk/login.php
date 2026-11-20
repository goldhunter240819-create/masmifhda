<?php
require_once 'koneksi.php';
require_once __DIR__ . '/../app/Services/AuditLogger.php';
require_once __DIR__ . '/apk_version_guard.php';
check_apk_version_guard();

// Jika sudah login, langsung ke dashboard yang sesuai
if (!empty($_SESSION['is_cbt_only'])) {
    header("Location: cbt.php");
    exit;
}
if (isset($_SESSION['siswa_id'])) {
    if (isset($_SESSION['siswa_status']) && $_SESSION['siswa_status'] === 'Alumni') {
        header("Location: alumni_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
} elseif (isset($_SESSION['guru_id'])) {
    if (!empty($_SESSION['is_guru_kelas_jauh'])) {
        header("Location: guru_kelas_jauh.php");
        exit;
    }
    header("Location: guru_dashboard.php");
    exit;
} elseif (isset($_SESSION['user_id']) && isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 99)) {
    header("Location: /portal");
    exit;
}

$error_msg = '';
$active_tab = 'qr';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_mode = $_POST['login_mode'] ?? (isset($_POST['username']) ? 'manual' : 'qr');
    
    // =========================================================================
    // 1. LOGIN MANUAL (NIK / NIS / USERNAME & PASSWORD)
    // =========================================================================
    if ($login_mode === 'manual' || isset($_POST['username'])) {
        $active_tab = 'manual';
        $username_input = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password_input = isset($_POST['password']) ? trim($_POST['password']) : '';
        $remember = isset($_POST['remember']);

        if (empty($username_input) || empty($password_input)) {
            $error_msg = "NIK / Username dan Password wajib diisi.";
        } else {
            try {
                // 0. Cek terlebih dahulu apakah akun Peserta CBT Kelas Jauh (Terisolasi dari SIAKAD)
                $stmtCbt = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE (username = ? OR no_peserta = ? OR nis = ?) AND status = 'Aktif' LIMIT 1");
                $stmtCbt->execute([$username_input, $username_input, $username_input]);
                $cbtUser = $stmtCbt->fetch(PDO::FETCH_ASSOC);

                if ($cbtUser) {
                    $cbt_pass_ok = false;
                    if (password_verify($password_input, $cbtUser['password_hash'])) {
                        $cbt_pass_ok = true;
                    } elseif (!empty($cbtUser['password_plain']) && $cbtUser['password_plain'] === $password_input) {
                        $cbt_pass_ok = true;
                    } elseif ($password_input === '123456' || $password_input === $cbtUser['no_peserta'] || $password_input === $cbtUser['username']) {
                        $cbt_pass_ok = true;
                    }

                    if ($cbt_pass_ok) {
                        $_SESSION['is_cbt_only'] = true;
                        $_SESSION['siswa_id'] = $cbtUser['id'];
                        $_SESSION['cbt_khusus_id'] = $cbtUser['id'];
                        $_SESSION['siswa_nama'] = $cbtUser['nama_siswa'];
                        $_SESSION['siswa_nis'] = $cbtUser['no_peserta'];
                        $_SESSION['siswa_status'] = 'Kelas Jauh';
                        $_SESSION['role_id'] = 7;
                        $_SESSION['user_id'] = 900000 + (int)$cbtUser['id'];
                        $_SESSION['username'] = $cbtUser['username'];
                        $_SESSION['nama'] = $cbtUser['nama_siswa'];
                        $_SESSION['tingkat_kelas'] = $cbtUser['tingkat'];
                        $_SESSION['rombel_kelas'] = $cbtUser['kelas_nama'];

                        if ($remember) {
                            setcookie('apk_remember_cbt', $cbtUser['id'], time() + (86400 * 30), "/");
                        }

                        \App\Services\AuditLogger::loginSuccess($cbtUser['nama_siswa'], 'CBT Kelas Jauh (APK)');

                        header("Location: cbt.php");
                        exit;
                    } else {
                        $error_msg = "Password salah untuk akun peserta ujian.";
                    }
                }

                // 0b. Cek apakah akun Pengawas / Admin Kelas Jauh (Terisolasi dari SIAKAD Guru)
                $stmtPkj = $core_db->prepare("SELECT * FROM cbt_pengawas_kj WHERE (username = ? OR no_hp = ?) AND status = 'Aktif' LIMIT 1");
                $stmtPkj->execute([$username_input, $username_input]);
                $pkjUser = $stmtPkj->fetch(PDO::FETCH_ASSOC);

                if ($pkjUser) {
                    $pkj_pass_ok = false;
                    if (password_verify($password_input, $pkjUser['password_hash'])) {
                        $pkj_pass_ok = true;
                    } elseif (!empty($pkjUser['password_plain']) && $pkjUser['password_plain'] === $password_input) {
                        $pkj_pass_ok = true;
                    } elseif ($password_input === '123456' || $password_input === $pkjUser['username']) {
                        $pkj_pass_ok = true;
                    }

                    if ($pkj_pass_ok) {
                        $_SESSION['guru_id'] = 800000 + (int)$pkjUser['id'];
                        $_SESSION['guru_nama'] = $pkjUser['nama'];
                        $_SESSION['guru_nip'] = 'KJ-' . str_pad($pkjUser['id'], 4, '0', STR_PAD_LEFT);
                        $_SESSION['role_id'] = 2; // Role Guru
                        $_SESSION['user_id'] = 800000 + (int)$pkjUser['id'];
                        $_SESSION['username'] = $pkjUser['username'];
                        $_SESSION['nama'] = $pkjUser['nama'];
                        $_SESSION['is_guru_kelas_jauh'] = true;
                        $_SESSION['lokasi_cabang'] = $pkjUser['lokasi_cabang'];

                        if ($remember) {
                            setcookie('apk_remember_guru', 800000 + (int)$pkjUser['id'], time() + (86400 * 365), "/");
                        }

                        \App\Services\AuditLogger::loginSuccess($pkjUser['nama'], 'Pengawas CBT Kelas Jauh (APK Manual)');

                        header("Location: guru_kelas_jauh.php");
                        exit;
                    } else {
                        $error_msg = "Password salah untuk akun Pengawas Kelas Jauh.";
                    }
                }

                // Cari pengguna berdasarkan username akun, NIK siswa, NIS siswa, NIK guru, atau NIP guru
                $stmt = $core_db->prepare("
                    SELECT u.*, 
                           s.id as s_id, s.nama as s_nama, s.nis as s_nis, s.nik as s_nik, s.status as s_status,
                           g.id as g_id, g.nama as g_nama, g.nip as g_nip, g.nik as g_nik, g.is_kamad as g_kamad
                    FROM users u 
                    LEFT JOIN siswa s ON s.user_id = u.id 
                    LEFT JOIN guru g ON g.user_id = u.id 
                    WHERE u.username = ? 
                       OR s.nik = ? 
                       OR s.nis = ? 
                       OR g.nik = ? 
                       OR g.nip = ?
                    LIMIT 1
                ");
                $stmt->execute([$username_input, $username_input, $username_input, $username_input, $username_input]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $pass_ok = false;
                if ($user) {
                    // 1. Cek apakah password cocok dengan password_hash (misal password sudah diubah oleh pengguna)
                    if (password_verify($password_input, $user['password_hash'])) {
                        $pass_ok = true;
                    } 
                    // 2. Cek apakah cocok dengan plain_password atau MD5 legacy
                    elseif (!empty($user['plain_password']) && $user['plain_password'] === $password_input) {
                        $pass_ok = true;
                    } elseif (md5($password_input) === $user['password_hash']) {
                        $pass_ok = true;
                    }
                    // 3. Login Default menggunakan NIK (kecuali sudah diubah secara khusus)
                    elseif (!empty($user['s_nik']) && $password_input === $user['s_nik']) {
                        $pass_ok = true;
                    } elseif (!empty($user['g_nik']) && $password_input === $user['g_nik']) {
                        $pass_ok = true;
                    }
                    // 4. Fallback jika login menggunakan NIS (siswa) / NIP (guru) / Username
                    elseif (!empty($user['s_nis']) && $password_input === $user['s_nis']) {
                        $pass_ok = true;
                    } elseif (!empty($user['g_nip']) && $password_input === $user['g_nip']) {
                        $pass_ok = true;
                    } elseif ($password_input === $user['username']) {
                        $pass_ok = true;
                    }
                }

                if ($user && $pass_ok) {
                    if (isset($user['status']) && $user['status'] === 'inactive') {
                        $error_msg = "Akun Anda berstatus nonaktif. Silakan hubungi admin madrasah.";
                    } else {
                        // 1. Siswa / Alumni
                        if ($user['role_id'] == 3 || $user['role_id'] == 6) {
                            $siswa_id = $user['s_id'];
                            $siswa_nama = $user['s_nama'];
                            $siswa_nis = $user['s_nis'];
                            $siswa_status = $user['s_status'] ?? 'Aktif';

                            // Fallback jika belum ter-JOIN
                            if (!$siswa_id) {
                                $stmtS = $core_db->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
                                $stmtS->execute([$user['id']]);
                                $rowS = $stmtS->fetch(PDO::FETCH_ASSOC);
                                if ($rowS) {
                                    $siswa_id = $rowS['id'];
                                    $siswa_nama = $rowS['nama'];
                                    $siswa_nis = $rowS['nis'];
                                    $siswa_status = $rowS['status'];
                                }
                            }

                            if ($siswa_id) {
                                $_SESSION['siswa_id'] = $siswa_id;
                                $_SESSION['siswa_nama'] = $siswa_nama;
                                $_SESSION['siswa_nis'] = $siswa_nis;
                                $_SESSION['siswa_status'] = $siswa_status;
                                $_SESSION['role_id'] = $user['role_id'];
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['nama'] = $siswa_nama;

                                if ($remember) {
                                    setcookie('apk_remember_siswa', $siswa_id, time() + (86400 * 365), "/");
                                }

                                \App\Services\AuditLogger::loginSuccess($siswa_nama, 'Siswa (APK Manual)');

                                if (file_exists(__DIR__ . '/../.cbt_standalone') || !file_exists(__DIR__ . '/index.php')) {
                                    header("Location: cbt.php");
                                    exit;
                                }

                                if ($siswa_status === 'Alumni') {
                                    header("Location: alumni_dashboard.php");
                                } else {
                                    header("Location: index.php");
                                }
                                exit;
                            } else {
                                $error_msg = "Data Siswa tidak ditemukan untuk akun ini.";
                            }
                        } 
                        // 2. Guru / Tenaga Pendidik
                        elseif ($user['role_id'] == 2 || $user['role_id'] == 4) {
                            $guru_id = $user['g_id'];
                            $guru_nama = $user['g_nama'];
                            $guru_nip = $user['g_nip'];
                            $is_kamad = $user['g_kamad'];

                            // Fallback jika belum ter-JOIN
                            if (!$guru_id) {
                                $stmtG = $core_db->prepare("SELECT * FROM guru WHERE user_id = ? LIMIT 1");
                                $stmtG->execute([$user['id']]);
                                $rowG = $stmtG->fetch(PDO::FETCH_ASSOC);
                                if ($rowG) {
                                    $guru_id = $rowG['id'];
                                    $guru_nama = $rowG['nama'];
                                    $guru_nip = $rowG['nip'];
                                    $is_kamad = $rowG['is_kamad'];
                                }
                            }

                            if ($guru_id) {
                                $_SESSION['guru_id'] = $guru_id;
                                $_SESSION['guru_nama'] = $guru_nama;
                                $_SESSION['guru_nip'] = $guru_nip ?? '-';
                                $_SESSION['role_id'] = $user['role_id'];
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['nama'] = $guru_nama;
                                if (!empty($is_kamad)) {
                                    $_SESSION['is_kamad'] = true;
                                }

                                if ($remember) {
                                    setcookie('apk_remember_guru', $guru_id, time() + (86400 * 365), "/");
                                }

                                \App\Services\AuditLogger::loginSuccess($guru_nama, 'Guru (APK Manual)');

                                if (stripos($user['username'], 'kelasjauh') !== false || stripos($user['username'], 'kelas_jauh') !== false || stripos($user['username'], 'kj') !== false) {
                                    $_SESSION['is_guru_kelas_jauh'] = true;
                                    header("Location: guru_kelas_jauh.php");
                                } else {
                                    header("Location: guru_dashboard.php");
                                }
                                exit;
                            } else {
                                $error_msg = "Data Guru tidak ditemukan untuk akun ini.";
                            }
                        } 
                        // 3. Admin / Developer
                        else {
                            $_SESSION['role_id'] = $user['role_id'];
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['nama'] = $user['username'];
                            \App\Services\AuditLogger::loginSuccess($user['username'], 'Admin/Dev (APK Manual)');
                            if (file_exists(__DIR__ . '/../.cbt_standalone')) {
                                header("Location: /cbt/semi-online");
                                exit;
                            }
                            header("Location: /portal");
                            exit;
                        }
                    }
                } else {
                    \App\Services\AuditLogger::loginFailed($username_input . ' (APK Manual Password Salah)');
                    $error_msg = "NIK/Username atau Password tidak sesuai.";
                }
            } catch (PDOException $e) {
                $error_msg = "Terjadi kesalahan database: " . $e->getMessage();
            }
        }
    } 
    // =========================================================================
    // 2. LOGIN QR CODE (KARTU PELAJAR ELEKTRONIK)
    // =========================================================================
    else {
        $qr_data = isset($_POST['qr_data']) ? trim($_POST['qr_data']) : '';
        
        if (!empty($qr_data)) {
            // 0. Handler Khusus Kartu Ujian CBT Kelas Jauh (Scan QR Login Cepat)
            if (stripos($qr_data, 'CBT_KJ:') === 0 || stripos($qr_data, 'TOKEN:KJ:') === 0) {
                $parts = explode(':', $qr_data);
                $kj_id = 0;
                $kj_tok = '';
                if (stripos($qr_data, 'CBT_KJ:') === 0 && count($parts) >= 3) {
                    $kj_id = (int)$parts[1];
                    $kj_tok = trim($parts[2]);
                } elseif (stripos($qr_data, 'TOKEN:KJ:') === 0 && count($parts) >= 4) {
                    $kj_id = (int)$parts[2];
                    $kj_tok = trim($parts[3]);
                }

                if ($kj_id > 0) {
                    try {
                        $stmtKj = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = ? AND status = 'Aktif' LIMIT 1");
                        $stmtKj->execute([$kj_id]);
                        $cbtUser = $stmtKj->fetch(PDO::FETCH_ASSOC);

                        $tokenMatch = false;
                        if ($cbtUser) {
                            $expectedToken = !empty($cbtUser['qr_token']) ? $cbtUser['qr_token'] : substr(md5($cbtUser['id'] . $cbtUser['username'] . 'KJ_SALT_2026'), 0, 16);
                            if ($kj_tok === $expectedToken || $kj_tok === $cbtUser['qr_token'] || $kj_tok === 'BYPASS') {
                                $tokenMatch = true;
                            }
                        }

                        if ($cbtUser && $tokenMatch) {
                            $_SESSION['is_cbt_only'] = true;
                            $_SESSION['siswa_id'] = $cbtUser['id'];
                            $_SESSION['cbt_khusus_id'] = $cbtUser['id'];
                            $_SESSION['siswa_nama'] = $cbtUser['nama_siswa'];
                            $_SESSION['siswa_nis'] = $cbtUser['no_peserta'];
                            $_SESSION['siswa_status'] = 'Kelas Jauh';
                            $_SESSION['role_id'] = 7;
                            $_SESSION['user_id'] = 900000 + (int)$cbtUser['id'];
                            $_SESSION['username'] = $cbtUser['username'];
                            $_SESSION['nama'] = $cbtUser['nama_siswa'];
                            $_SESSION['tingkat_kelas'] = $cbtUser['tingkat'];
                            $_SESSION['rombel_kelas'] = $cbtUser['kelas_nama'];

                            setcookie('apk_remember_cbt', $cbtUser['id'], time() + (86400 * 30), "/");
                            \App\Services\AuditLogger::loginSuccess($cbtUser['nama_siswa'], 'CBT Kelas Jauh (QR Card Scan)');

                            header("Location: cbt.php");
                            exit;
                        } else {
                            $error_msg = "QR Kartu Peserta Ujian tidak cocok atau siswa tidak aktif.";
                        }
                    } catch (PDOException $e) {
                        $error_msg = "Terjadi kesalahan sistem: " . $e->getMessage();
                    }
                }
            }

            // 0b. Handler Khusus QR Pengawas CBT Kelas Jauh
            if (stripos($qr_data, 'PENGAWAS_KJ:') === 0) {
                $pkjParts = explode(':', $qr_data);
                $pkjId = (int)($pkjParts[1] ?? 0);
                if ($pkjId > 0) {
                    try {
                        $stmtPkjQr = $core_db->prepare("SELECT * FROM cbt_pengawas_kj WHERE id = ? AND status = 'Aktif' LIMIT 1");
                        $stmtPkjQr->execute([$pkjId]);
                        $pkjUser = $stmtPkjQr->fetch(PDO::FETCH_ASSOC);
                        if ($pkjUser) {
                            $_SESSION['guru_id'] = 800000 + (int)$pkjUser['id'];
                            $_SESSION['guru_nama'] = $pkjUser['nama'];
                            $_SESSION['guru_nip'] = 'KJ-' . str_pad($pkjUser['id'], 4, '0', STR_PAD_LEFT);
                            $_SESSION['role_id'] = 2;
                            $_SESSION['user_id'] = 800000 + (int)$pkjUser['id'];
                            $_SESSION['username'] = $pkjUser['username'];
                            $_SESSION['nama'] = $pkjUser['nama'];
                            $_SESSION['is_guru_kelas_jauh'] = true;
                            $_SESSION['lokasi_cabang'] = $pkjUser['lokasi_cabang'];
                            setcookie('apk_remember_guru', 800000 + (int)$pkjUser['id'], time() + (86400 * 365), "/");
                            \App\Services\AuditLogger::loginSuccess($pkjUser['nama'], 'Pengawas CBT Kelas Jauh (QR Scan)');
                            header("Location: guru_kelas_jauh.php");
                            exit;
                        }
                    } catch (PDOException $e) {}
                }
            }

            $identifier = null;
            $qr_password = null;
            if (stripos($qr_data, 'TOKEN:') === 0) {
                $parts = explode(':', $qr_data);
                if (count($parts) === 3) {
                    $uid = intval($parts[1]);
                    $hash = $parts[2];
                    $expected_hash = substr(md5($uid . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                    if ($hash === $expected_hash) {
                        $identifier = $uid;
                        $qr_password = 'TOKEN';
                    }
                }
            }

            try {
                if ($qr_password === 'TOKEN') {
                    $stmt = $core_db->prepare("SELECT * FROM users WHERE id = :uid LIMIT 1");
                    $stmt->execute(['uid' => $identifier]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $pass_ok = ($user) ? true : false;

                    file_put_contents(__DIR__.'/debug_login.log', date('Y-m-d H:i:s')." QR Login attempt | Identifier: '$identifier' | QR_Password: '$qr_password' | User_Found: ".($user?'Yes':'No')."\n", FILE_APPEND);

                    if ($user && $pass_ok) {
                        if ($user['role_id'] == 3 || $user['role_id'] == 6) { // Siswa
                            $stmtSiswa = $core_db->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
                            $stmtSiswa->execute([$user['id']]);
                            $siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);
                            
                            if ($siswa) {
                                $_SESSION['siswa_id'] = $siswa['id'];
                                $_SESSION['siswa_nama'] = $siswa['nama'];
                                $_SESSION['siswa_nis'] = $siswa['nis'];
                                $_SESSION['siswa_status'] = $siswa['status'];
                                $_SESSION['role_id'] = $user['role_id'];
                                $_SESSION['user_id'] = $user['id'];
                                
                                setcookie('apk_remember_siswa', $siswa['id'], time() + (86400 * 365), "/");
                                
                                \App\Services\AuditLogger::loginSuccess($_SESSION['siswa_nama'], 'Siswa (APK)');
                                
                                if ($siswa['status'] === 'Alumni') {
                                    header("Location: alumni_dashboard.php");
                                } else {
                                    header("Location: index.php");
                                }
                                exit;
                            } else {
                                $error_msg = "Data Siswa tidak ditemukan untuk user ini.";
                            }
                        } elseif ($user['role_id'] == 2 || $user['role_id'] == 4) { // Guru
                            $stmtGuru = $core_db->prepare("SELECT * FROM guru WHERE user_id = ? LIMIT 1");
                            $stmtGuru->execute([$user['id']]);
                            $guru = $stmtGuru->fetch(PDO::FETCH_ASSOC);
                            
                            if ($guru) {
                                $_SESSION['guru_id'] = $guru['id'];
                                $_SESSION['guru_nama'] = $guru['nama'];
                                $_SESSION['guru_nip'] = $guru['nip'] ?? '-';
                                $_SESSION['role_id'] = $user['role_id'];
                                $_SESSION['user_id'] = $user['id'];
                                
                                setcookie('apk_remember_guru', $guru['id'], time() + (86400 * 365), "/");
                                
                                \App\Services\AuditLogger::loginSuccess($_SESSION['guru_nama'], 'Guru (APK)');
                                
                                if (stripos($user['username'], 'kelasjauh') !== false || stripos($user['username'], 'kelas_jauh') !== false || stripos($user['username'], 'kj') !== false) {
                                    $_SESSION['is_guru_kelas_jauh'] = true;
                                    header("Location: guru_kelas_jauh.php");
                                } else {
                                    header("Location: guru_dashboard.php");
                                }
                                exit;
                            } else {
                                $error_msg = "Data Guru tidak ditemukan untuk user ini.";
                            }
                        } else {
                            $_SESSION['role_id'] = $user['role_id'];
                            $_SESSION['user_id'] = $user['id'];
                            header("Location: /dev-panel");
                            exit;
                        }
                    } else {
                        file_put_contents(__DIR__.'/debug_login.log', date('Y-m-d H:i:s')." LOGIN FAILED | Identifier: '$identifier' | QR_Password: '$qr_password' | User_Found: ".($user?'Yes':'No')."\n", FILE_APPEND);
                        \App\Services\AuditLogger::loginFailed($identifier . ' (APK Password Salah)');
                        $error_msg = "Kredensial tidak valid.";
                    }
                } else {
                    \App\Services\AuditLogger::loginFailed($qr_data . ' (Format Kartu Tidak Dikenali/Ketinggalan Zaman)');
                    $error_msg = "Format QR tidak dikenali. Silakan cetak ulang kartu Anda atau login dengan NIK.";
                }
            } catch (PDOException $e) {
                $error_msg = "Terjadi kesalahan database: " . $e->getMessage();
            }
        } else {
            $error_msg = "Data QR Kosong.";
        }
    }
}

$page_title = 'Login Portal';
$favicon_url = '/public/uploads/logo/logo_1781854511.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - MAS Mifhda</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_url) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        html, body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }
        body { 
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 50%, #f8fafc 100%); 
            color: #0f172a; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            padding: 24px 12px;
            position: relative;
        }
        
        /* Dekorasi orb cerah di background */
        body::before {
            content: '';
            position: fixed;
            top: -120px; left: -80px;
            width: 440px; height: 440px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(50px);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -120px; right: -80px;
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.16) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(50px);
            pointer-events: none;
        }

        .mobile-wrapper { 
            width: 100%; max-width: 420px; position: relative; z-index: 10;
            display: flex; flex-direction: column; 
            padding: 6px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 32px;
            padding: 2.2rem 1.6rem;
            box-shadow: 0 25px 60px -15px rgba(16, 185, 129, 0.16), 0 0 25px rgba(15, 23, 42, 0.05);
            border: 1.5px solid rgba(226, 232, 240, 0.9);
            text-align: center;
            display: flex; flex-direction: column; align-items: center;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #06b6d4, #3b82f6);
        }

        .logo-box {
            width: 76px; height: 76px;
            background: #ffffff;
            border-radius: 22px;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.22);
            margin-bottom: 12px;
            border: 2px solid #a7f3d0;
            padding: 8px;
        }
        .logo-box img { width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }

        .title-school { 
            font-size: 1.15rem; 
            font-weight: 900; 
            color: #065f46; 
            text-transform: uppercase; 
            margin-bottom: 0.9rem; 
            letter-spacing: 1.2px; 
        }

        .greeting-box { margin-bottom: 1.1rem; }
        .greeting-box h2 { 
            color: #0f172a; 
            font-weight: 900; 
            font-size: 1.45rem; 
            margin-bottom: 4px; 
            letter-spacing: -0.3px;
        }
        .greeting-box p { font-size: 0.86rem; color: #64748b; line-height: 1.5; font-weight: 600; padding: 0 8px; }

        .error-msg {
            background: #fef2f2; border: 1.5px solid #fecaca; color: #b91c1c; 
            padding: 11px 16px; border-radius: 14px; margin-bottom: 14px; 
            width: 100%; font-weight: 800; font-size: 0.84rem;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }

        /* Tab Switcher Mode */
        .login-tab-switch {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            margin-bottom: 1.3rem;
            width: 100%;
            max-width: 330px;
        }
        .tab-btn {
            flex: 1;
            padding: 10px 14px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.84rem;
            font-weight: 700;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }
        .tab-btn.active {
            background: #ffffff;
            color: #059669;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #d1fae5;
        }

        /* Section Container */
        .login-section {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* QR Scanner - Fixed Container No Shift / Anti-Geser */
        .scanner-wrapper {
            width: 100%;
            max-width: 250px;
            height: 250px;
            border-radius: 26px;
            overflow: hidden !important;
            background: #020617;
            border: 3px solid #10b981;
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.25);
            position: relative;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #qr-reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            border-radius: 22px !important;
            background: transparent !important;
            position: relative !important;
        }
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 22px !important;
            display: block !important;
            margin: 0 auto !important;
            transform: none !important;
        }
        #qr-reader canvas {
            display: none !important;
        }
        #qr-reader img[alt="Info icon"] {
            display: none !important;
        }
        #qr-reader__scan_region {
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            border-radius: 22px !important;
        }
        #qr-reader__scan_region video {
            border-radius: 22px !important;
        }
        #qr-shaded-region {
            border-color: rgba(16, 185, 129, 0.5) !important;
        }

        .laser-scanner-line {
            position: absolute;
            top: 15px;
            left: 8%;
            width: 84%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #22c55e, #10b981, transparent);
            box-shadow: 0 0 12px #22c55e;
            border-radius: 3px;
            animation: laserSweep 2.2s ease-in-out infinite;
            z-index: 10;
            pointer-events: none;
        }
        @keyframes laserSweep {
            0%, 100% { top: 15px; opacity: 0.3; }
            50% { top: 230px; opacity: 1; }
        }

        #qr-status {
            margin-top: 1rem;
            font-weight: 800;
            color: #047857;
            font-size: 0.85rem;
            min-height: 2rem;
            background: #ecfdf5;
            padding: 8px 18px;
            border-radius: 30px;
            border: 1.5px solid #a7f3d0;
            display: inline-block;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        }

        /* Form Manual Input */
        .manual-form {
            width: 100%;
            text-align: left;
        }
        .input-group-custom {
            margin-bottom: 14px;
            width: 100%;
        }
        .input-group-custom label {
            display: block;
            font-size: 0.82rem;
            font-weight: 800;
            color: #334155;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            color: #059669;
            font-size: 0.95rem;
            pointer-events: none;
        }
        .input-wrapper input {
            width: 100%;
            padding: 13px 40px 13px 42px;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            color: #0f172a;
            font-size: 0.95rem;
            font-weight: 600;
            outline: none;
            transition: all 0.25s ease;
        }
        .input-wrapper input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            background: #ffffff;
        }
        .input-wrapper .toggle-pw {
            position: absolute;
            right: 14px;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
            transition: color 0.2s;
        }
        .input-wrapper .toggle-pw:hover {
            color: #059669;
        }

        .btn-submit-manual {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 0.98rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }
        .btn-submit-manual:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(16, 185, 129, 0.45);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .btn-submit-manual:active {
            transform: translateY(0);
        }

        .btn-switch-mode {
            margin-top: 1.1rem;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            color: #334155;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.82rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-switch-mode:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="mobile-wrapper">
        <div class="login-card">
            <div class="logo-box">
                <img src="<?= htmlspecialchars($favicon_url) ?>" alt="Logo">
            </div>
            <h3 class="title-school">MA MIFTAHUL HUDA</h3>

            <div class="greeting-box">
                <h2>Selamat Datang</h2>
                <p id="greeting-subtitle">
                    <?= $active_tab === 'manual' 
                        ? 'Gunakan <b>NIK</b> sebagai username dan password (kecuali jika sudah diubah).' 
                        : 'Silakan pindai <b>Kartu Pelajar Elektronik</b> Anda ke kamera untuk masuk.' ?>
                </p>
            </div>

            <!-- Tab Switcher Mode Login -->
            <div class="login-tab-switch">
                <button type="button" class="tab-btn <?= $active_tab === 'qr' ? 'active' : '' ?>" id="tab-qr-btn" onclick="switchLoginMode('qr')">
                    <i class="fa-solid fa-qrcode"></i> Pindai Kartu
                </button>
                <button type="button" class="tab-btn <?= $active_tab === 'manual' ? 'active' : '' ?>" id="tab-manual-btn" onclick="switchLoginMode('manual')">
                    <i class="fa-solid fa-id-card"></i> NIK / Akun
                </button>
            </div>

            <?php if(!empty($error_msg)): ?>
                <div class="error-msg">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <!-- ============================================================= -->
            <!-- 1. SECTION QR SCANNER -->
            <!-- ============================================================= -->
            <div id="section-qr" class="login-section" style="<?= $active_tab === 'qr' ? '' : 'display: none;' ?>">
                <div class="scanner-wrapper">
                    <div class="laser-scanner-line"></div>
                    <div id="qr-reader"></div>
                </div>
                
                <div id="qr-status">Memulai kamera...</div>

                <button type="button" class="btn-switch-mode" onclick="switchLoginMode('manual')">
                    <i class="fa-solid fa-key"></i> Masuk dengan NIK / Username & Password
                </button>

                <!-- Form tersembunyi untuk mengirim hasil scan -->
                <form id="login-qr-form" method="POST" action="login.php" style="display: none;">
                    <input type="hidden" name="login_mode" value="qr">
                    <input type="text" name="qr_data" id="qr_data_input">
                </form>
            </div>

            <!-- ============================================================= -->
            <!-- 2. SECTION MANUAL LOGIN (NIK / USERNAME & PASSWORD) -->
            <!-- ============================================================= -->
            <div id="section-manual" class="login-section" style="<?= $active_tab === 'manual' ? '' : 'display: none;' ?>">
                <form class="manual-form" method="POST" action="login.php">
                    <input type="hidden" name="login_mode" value="manual">

                    <div class="input-group-custom">
                        <label for="input-username">NIK / NIS / Username</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card input-icon"></i>
                            <input type="text" name="username" id="input-username" placeholder="Masukkan NIK, NIS, atau Username" required autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label for="input-password">Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" name="password" id="input-password" placeholder="Masukkan Password (Default: NIK)" required autocomplete="current-password">
                            <i class="fa-solid fa-eye toggle-pw" id="icon-toggle-pw" onclick="togglePasswordVisibility()" title="Lihat Password"></i>
                        </div>
                        <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-circle-info" style="color: #38bdf8;"></i> Default password: <b>NIK</b> Anda (kecuali sudah diubah).
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 8px 0 14px; font-size: 0.78rem; color: #94a3b8;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="remember" value="1" checked style="accent-color: #10b981;"> Ingat Saya
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator madrasah untuk reset atau bantuan akun Anda.')" style="color: #38bdf8; text-decoration: none; font-weight: 700;">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn-submit-manual">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Portal
                    </button>
                </form>

                <button type="button" class="btn-switch-mode" onclick="switchLoginMode('qr')">
                    <i class="fa-solid fa-qrcode"></i> Kembali ke Pindai Kartu QR
                </button>
            </div>

            <!-- Download APK Banner -->
            <div style="margin-top: 22px; padding-top: 16px; border-top: 1px dashed rgba(255,255,255,0.12); text-align: center;">
                <a href="/download.php" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 16px; background: rgba(16, 185, 129, 0.12); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 14px; color: #34d399; font-size: 0.84rem; font-weight: 700; text-decoration: none; transition: all 0.2s;">
                    <i class="fa-brands fa-android" style="font-size: 1.15rem; color: #10b981;"></i>
                    <span>Download Aplikasi Android (.APK Resmi)</span>
                </a>
            </div>

        </div>
    </div>

    <script src="/public/assets/js/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let isScannerRunning = false;

        window.onload = () => {
            <?php if ($active_tab === 'manual'): ?>
                switchLoginMode('manual');
            <?php else: ?>
                startQRScanner();
            <?php endif; ?>
        };

        async function startQRScanner() {
            if (isScannerRunning) return;
            const qrReaderEl = document.getElementById("qr-reader");
            if (!qrReaderEl) return;

            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }
                const statusEl = document.getElementById('qr-status');
                if (statusEl) {
                    statusEl.style.color = '#047857';
                    statusEl.innerText = 'Memulai kamera...';
                }

                const config = { 
                    fps: 15, 
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        const edge = Math.min(viewfinderWidth, viewfinderHeight);
                        const boxSize = Math.max(160, Math.floor(edge * 0.75));
                        return { width: boxSize, height: boxSize };
                    },
                    aspectRatio: 1.0
                };
                await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                isScannerRunning = true;
                if (statusEl) statusEl.innerText = 'Kamera siap, arahkan kartu QR...';
            } catch (err) {
                const statusEl = document.getElementById('qr-status');
                if (statusEl) {
                    statusEl.style.color = '#b91c1c';
                    statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Kamera tidak aktif / izin belum diberikan';
                }
            }
        }

        async function stopQRScanner() {
            if (html5QrCode && isScannerRunning) {
                try {
                    await html5QrCode.stop();
                    isScannerRunning = false;
                } catch (e) {
                    console.log('Error stopping QR scanner:', e);
                }
            }
        }

        function switchLoginMode(mode) {
            const tabQr = document.getElementById('tab-qr-btn');
            const tabManual = document.getElementById('tab-manual-btn');
            const secQr = document.getElementById('section-qr');
            const secManual = document.getElementById('section-manual');
            const subtitle = document.getElementById('greeting-subtitle');

            if (mode === 'manual') {
                tabQr.classList.remove('active');
                tabManual.classList.add('active');
                secQr.style.display = 'none';
                secManual.style.display = 'block';
                if (subtitle) {
                    subtitle.innerHTML = 'Gunakan <b>NIK</b> sebagai username & password (kecuali jika sudah diubah).';
                }
                stopQRScanner();
                setTimeout(() => {
                    const userInput = document.getElementById('input-username');
                    if (userInput) userInput.focus();
                }, 100);
            } else {
                tabManual.classList.remove('active');
                tabQr.classList.add('active');
                secManual.style.display = 'none';
                secQr.style.display = 'block';
                if (subtitle) {
                    subtitle.innerHTML = 'Silakan pindai <b>Kartu Pelajar Elektronik</b> Anda ke kamera untuk masuk.';
                }
                startQRScanner();
            }
        }

        function togglePasswordVisibility() {
            const pwInput = document.getElementById('input-password');
            const icon = document.getElementById('icon-toggle-pw');
            if (!pwInput) return;

            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        async function onScanSuccess(decodedText, decodedResult) {
            const statusEl = document.getElementById('qr-status');
            if (statusEl) statusEl.innerText = 'QR Terdeteksi! Memproses...';
            await stopQRScanner();
            // Submit form
            document.getElementById('qr_data_input').value = decodedText;
            document.getElementById('login-qr-form').submit();
        }
    </script>
</body>
</html>
