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
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_data = isset($_POST['qr_data']) ? trim($_POST['qr_data']) : '';
    
    if (!empty($qr_data)) {
        // Coba ekstrak identifier dari berbagai format QR kartu pelajar
        $identifier = null;
        
        // Format Baru: Cek TOKEN (Lebih Aman)
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
        
        // Format lama dihapus sesuai instruksi

        try {
            if ($qr_password === 'TOKEN') {
                // Login via Token
                $stmt = $core_db->prepare("SELECT * FROM users WHERE id = :uid LIMIT 1");
                $stmt->execute(['uid' => $identifier]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $pass_ok = false;
                if ($user) {
                    $pass_ok = true;
                }

                // Debug log
                file_put_contents(__DIR__.'/debug_login.log', date('Y-m-d H:i:s')." QR Login attempt | Identifier: '$identifier' | QR_Password: '$qr_password' | User_Found: ".($user?'Yes':'No')."\n", FILE_APPEND);

                if ($user && $pass_ok) {
                    // Cek Role ID
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
                        // Admin / Developer
                        $_SESSION['role_id'] = $user['role_id'];
                        $_SESSION['user_id'] = $user['id'];
                        header("Location: /dev-panel");
                        exit;
                    }
                } else {
                    file_put_contents(__DIR__.'/debug_login.log', date('Y-m-d H:i:s')." LOGIN FAILED | Identifier: '$identifier' | QR_Password: '$qr_password' | User_Found: ".($user?'Yes':'No')." | PassOK: ".($pass_ok?'Yes':'No')."\n", FILE_APPEND);
                    \App\Services\AuditLogger::loginFailed($identifier . ' (APK Password Salah)');
                    $error_msg = "Kredensial tidak valid.";
                }
            } else {
                // Semua format lama telah dihapus sesuai instruksi
                \App\Services\AuditLogger::loginFailed($qr_data . ' (Format Kartu Tidak Dikenali/Ketinggalan Zaman)');
                $error_msg = "Format QR tidak dikenali. Silakan cetak ulang kartu Anda.";
            }
        } catch (PDOException $e) {
            $error_msg = "Terjadi kesalahan database: " . $e->getMessage();
        }
    } else {
        $error_msg = "Data QR Kosong.";
    }
}

$page_title = 'Login Portal';

// Fetch favicon
$favicon_url = '/public/uploads/logo/logo_1781854511.png'; // MA Logo
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MAS Mifhda</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_url) ?>">
    <!-- Menggunakan font Quicksand yang lebih ramah/bulat -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Quicksand', sans-serif; }
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: fixed; /* Mencegah pull-to-refresh dan overscroll di mobile */
            touch-action: none; /* Mencegah semua interaksi sentuh default seperti scroll */
        }
        body { 
            /* Gradien dark modern premium */
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%); 
            color: #f8fafc; 
            display: flex; 
            justify-content: center; 
            align-items: center;
        }
        
        /* Dekorasi orb glowing melengkung di background */
        body::before {
            content: '';
            position: absolute;
            top: -150px; left: -100px;
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(50px);
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -150px; right: -150px;
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
        }

        .mobile-wrapper { 
            width: 100%; max-width: 420px; position: relative; z-index: 10;
            display: flex; flex-direction: column; 
            padding: 20px;
        }
        
        .login-card {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 35px;
            padding: 2rem 1.2rem;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.05);
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

        .title-school { font-size: 1.1rem; font-weight: 800; color: #f8fafc; text-transform: uppercase; margin-bottom: 1.2rem; letter-spacing: 1.5px; opacity: 0.95; }

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
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; 
            padding: 10px; border-radius: 12px; margin-bottom: 12px; 
            width: 100%; font-weight: 600; font-size: 0.8rem;
            backdrop-filter: blur(10px);
        }

        .scanner-wrapper {
            width: 100%; max-width: 220px;
            border-radius: 25px; overflow: hidden;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(52, 211, 153, 0.3);
            box-shadow: 0 15px 40px rgba(52, 211, 153, 0.15);
            position: relative;
        }
        
        /* Memberikan rounded pada video canvas */
        #qr-reader video, #qr-reader canvas { border-radius: 26px; }

        #qr-status {
            margin-top: 1rem; font-weight: 700; color: #34d399; font-size: 0.9rem;
            min-height: 2rem; background: rgba(52, 211, 153, 0.1); padding: 8px 20px; border-radius: 30px;
            border: 1px solid rgba(52, 211, 153, 0.2);
            display: inline-block; animation: pulse 2.5s infinite; letter-spacing: 0.5px;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 0 0 12px rgba(52, 211, 153, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
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
                <p>Silakan pindai <b>Kartu Pelajar Elektronik</b> Anda ke kamera untuk masuk.</p>
            </div>

            <?php if(!empty($error_msg)): ?>
                <div class="error-msg">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div class="scanner-wrapper">
                <div id="qr-reader"></div>
            </div>
            
            <div id="qr-status">Memulai kamera...</div>

            <!-- Form tersembunyi untuk mengirim hasil scan -->
            <form id="login-form" method="POST" action="login.php" style="display: none;">
                <input type="text" name="qr_data" id="qr_data_input">
            </form>
        </div>
    </div>

    <script src="/public/assets/js/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode;

        window.onload = () => {
            startQRScanner();
        };

        async function startQRScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            try {
                await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                document.getElementById('qr-status').innerText = 'Siap memindai...';
            } catch (err) {
                document.getElementById('qr-status').style.color = '#fca5a5';
                document.getElementById('qr-status').innerHTML = 'Gagal mengakses kamera: ' + err;
            }
        }

        async function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('qr-status').innerText = 'QR Terdeteksi! Memproses...';
            if (html5QrCode) {
                await html5QrCode.stop().catch(() => {});
            }
            // Submit form
            document.getElementById('qr_data_input').value = decodedText;
            document.getElementById('login-form').submit();
        }
    </script>
</body>
</html>
