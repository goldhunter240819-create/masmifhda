<?php
require_once 'koneksi.php';
require_once __DIR__ . '/../app/Services/AuditLogger.php';

// Jika sudah login, langsung ke dashboard yang sesuai
if (isset($_SESSION['siswa_id'])) {
    if (isset($_SESSION['siswa_status']) && $_SESSION['siswa_status'] === 'Alumni') {
        header("Location: alumni_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
} elseif (isset($_SESSION['guru_id'])) {
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

                                header("Location: guru_dashboard.php");
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
                                
                                header("Location: guru_dashboard.php");
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
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Quicksand', sans-serif; }
        html, body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }
        body { 
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%); 
            color: #f8fafc; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            padding: 20px 10px;
            position: relative;
        }
        
        /* Dekorasi orb glowing melengkung di background */
        body::before {
            content: '';
            position: fixed;
            top: -150px; left: -100px;
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(50px);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -150px; right: -150px;
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
            pointer-events: none;
        }

        .mobile-wrapper { 
            width: 100%; max-width: 420px; position: relative; z-index: 10;
            display: flex; flex-direction: column; 
            padding: 10px;
        }
        
        .login-card {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 35px;
            padding: 2rem 1.4rem;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            display: flex; flex-direction: column; align-items: center;
        }

        .logo-box {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%); 
            border-radius: 20px;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 15px 25px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px;
        }
        .logo-box img { width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.4)); }

        .title-school { font-size: 1.1rem; font-weight: 800; color: #f8fafc; text-transform: uppercase; margin-bottom: 1.1rem; letter-spacing: 1.5px; opacity: 0.95; }

        .greeting-box { margin-bottom: 1rem; }
        .greeting-box h2 { 
            color: transparent; 
            background: linear-gradient(90deg, #34d399, #38bdf8); 
            -webkit-background-clip: text; 
            background-clip: text; 
            font-weight: 800; 
            font-size: 1.4rem; 
            margin-bottom: 4px; 
        }
        .greeting-box p { font-size: 0.85rem; color: #94a3b8; line-height: 1.5; font-weight: 500; padding: 0 10px; }

        .error-msg {
            background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #fca5a5; 
            padding: 10px 14px; border-radius: 14px; margin-bottom: 14px; 
            width: 100%; font-weight: 700; font-size: 0.82rem;
            backdrop-filter: blur(10px);
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
            background: rgba(0, 0, 0, 0.4);
            padding: 4px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 1.3rem;
            width: 100%;
            max-width: 330px;
        }
        .tab-btn {
            flex: 1;
            padding: 9px 12px;
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 0.82rem;
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
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.25) 0%, rgba(56, 189, 248, 0.25) 100%);
            color: #f8fafc;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(52, 211, 153, 0.45);
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

        /* QR Scanner */
        .scanner-wrapper {
            width: 100%; max-width: 220px;
            border-radius: 25px; overflow: hidden;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(52, 211, 153, 0.3);
            box-shadow: 0 15px 40px rgba(52, 211, 153, 0.15);
            position: relative;
        }
        #qr-reader video, #qr-reader canvas { border-radius: 26px; }

        #qr-status {
            margin-top: 1rem; font-weight: 700; color: #34d399; font-size: 0.85rem;
            min-height: 2rem; background: rgba(52, 211, 153, 0.1); padding: 8px 18px; border-radius: 30px;
            border: 1px solid rgba(52, 211, 153, 0.2);
            display: inline-block; animation: pulse 2.5s infinite; letter-spacing: 0.5px;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 0 0 12px rgba(52, 211, 153, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
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
            font-size: 0.8rem;
            font-weight: 700;
            color: #94a3b8;
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
            color: #38bdf8;
            font-size: 0.95rem;
            pointer-events: none;
        }
        .input-wrapper input {
            width: 100%;
            padding: 12px 40px 12px 42px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #f8fafc;
            font-size: 0.9rem;
            font-weight: 600;
            outline: none;
            transition: all 0.25s ease;
        }
        .input-wrapper input:focus {
            border-color: #34d399;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }
        .input-wrapper .toggle-pw {
            position: absolute;
            right: 14px;
            color: #64748b;
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
            transition: color 0.2s;
        }
        .input-wrapper .toggle-pw:hover {
            color: #38bdf8;
        }

        .btn-submit-manual {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981 0%, #0284c7 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.4);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }
        .btn-submit-manual:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -4px rgba(56, 189, 248, 0.5);
            background: linear-gradient(135deg, #059669 0%, #0369a1 100%);
        }
        .btn-submit-manual:active {
            transform: translateY(0);
        }

        .btn-switch-mode {
            margin-top: 1.1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 9px 18px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-switch-mode:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.3);
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
                    statusEl.style.color = '#34d399';
                    statusEl.innerText = 'Memulai kamera...';
                }

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                isScannerRunning = true;
                if (statusEl) statusEl.innerText = 'Siap memindai...';
            } catch (err) {
                const statusEl = document.getElementById('qr-status');
                if (statusEl) {
                    statusEl.style.color = '#fca5a5';
                    statusEl.innerHTML = 'Kamera tidak aktif: ' + err;
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
