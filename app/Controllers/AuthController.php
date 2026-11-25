<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuditLogger;

class AuthController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['role_id'] ?? 0;
            if ($role == 5) {
                redirect('/pengawas/pkkm');
            } elseif ($role == 2) {
                redirect('/guru/dashboard');
            } elseif ($role == 3) {
                redirect('/siswa/dashboard');
            } elseif ($role == 99) {
                redirect('/dev-panel');
            } else {
                redirect('/');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $db = Database::connect('core');
            $dbSiakad = null;
            try { $dbSiakad = Database::connect('siakad'); } catch (\Throwable $e) {}

            $loginInput = trim($username);
            $stmt = $db->prepare("
                SELECT u.*, 
                       s.id as s_id, s.nama as s_nama, s.nis as s_nis, s.nik as s_nik,
                       g.id as g_id, g.nama as g_nama, g.nip as g_nip, g.nik as g_nik, g.is_kamad as g_kamad
                FROM users u 
                LEFT JOIN siswa s ON (s.user_id = u.id OR (s.nik = u.username AND s.nik != '' AND s.nik IS NOT NULL) OR (s.nis = u.username AND s.nis != '' AND s.nis IS NOT NULL))
                LEFT JOIN guru g ON (g.user_id = u.id OR (g.nik = u.username AND g.nik != '' AND g.nik IS NOT NULL) OR (g.nip = u.username AND g.nip != '' AND g.nip IS NOT NULL))
                WHERE u.username = ? 
                   OR s.nik = ? 
                   OR s.nis = ? 
                   OR g.nik = ? 
                   OR g.nip = ?
                LIMIT 1
            ");
            $stmt->execute([$loginInput, $loginInput, $loginInput, $loginInput, $loginInput]);
            $user = $stmt->fetch();

            $pass_ok = false;
            if (!$user) {
                // Cek langsung ke data siswa jika belum memiliki akun user terpisah (Login via NIS atau NIK)
                $stmtDirectS = $db->prepare("SELECT * FROM siswa WHERE (nik = ? AND nik != '' AND nik IS NOT NULL) OR (nis = ? AND nis != '' AND nis IS NOT NULL) LIMIT 1");
                $stmtDirectS->execute([$loginInput, $loginInput]);
                $directS = $stmtDirectS->fetch();
                if (!$directS && $dbSiakad) {
                    try {
                        $stmtDirectS2 = $dbSiakad->prepare("SELECT * FROM siswa WHERE (nik = ? AND nik != '' AND nik IS NOT NULL) OR (nis = ? AND nis != '' AND nis IS NOT NULL) LIMIT 1");
                        $stmtDirectS2->execute([$loginInput, $loginInput]);
                        $directS = $stmtDirectS2->fetch();
                    } catch (\Throwable $e) {}
                }

                if ($directS) {
                    $allowedPass = array_filter([$directS['nik'] ?? null, $directS['nis'] ?? null, '123456']);
                    if (in_array($password, $allowedPass)) {
                        $uname = !empty($directS['nik']) ? $directS['nik'] : $directS['nis'];
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        try {
                            $stmtMakeU = $db->prepare("INSERT INTO users (username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, 3, 'active') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), plain_password = VALUES(plain_password)");
                            $stmtMakeU->execute([$uname, $hash, $password]);
                            $newUid = (int)$db->lastInsertId();
                            if (!$newUid) {
                                $newUid = (int)$db->query("SELECT id FROM users WHERE username = " . $db->quote($uname))->fetchColumn();
                            }
                            if ($newUid) {
                                $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$newUid, $directS['id']]);
                                if ($dbSiakad) {
                                    try { $dbSiakad->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$newUid, $directS['id']]); } catch (\Throwable $e) {}
                                }
                            }
                        } catch (\Throwable $e) {}

                        $user = [
                            'id' => $newUid ?? (100000 + (int)$directS['id']),
                            'username' => $uname,
                            'password_hash' => $hash,
                            'plain_password' => $password,
                            'role_id' => 3,
                            'status' => 'active',
                            's_id' => $directS['id'],
                            's_nama' => $directS['nama'],
                            's_nis' => $directS['nis'],
                            's_nik' => $directS['nik']
                        ];
                        $pass_ok = true;
                    }
                }
            }

            if ($user) {
                // Pastikan s_id, s_nis, dan s_nik terisi jika akun ini adalah akun siswa
                if (empty($user['s_id']) && ($user['role_id'] == 3 || empty($user['role_id']))) {
                    $stmtResolvS = $db->prepare("
                        SELECT * FROM siswa 
                        WHERE (nik = ? AND nik != '' AND nik IS NOT NULL) 
                           OR (nis = ? AND nis != '' AND nis IS NOT NULL)
                           OR (nik = ? AND nik != '' AND nik IS NOT NULL) 
                           OR (nis = ? AND nis != '' AND nis IS NOT NULL)
                           OR (user_id = ? AND ? > 0)
                        LIMIT 1
                    ");
                    $stmtResolvS->execute([$loginInput, $loginInput, $user['username'], $user['username'], $user['id'], $user['id']]);
                    $foundS = $stmtResolvS->fetch();
                    if (!$foundS && $dbSiakad) {
                        try {
                            $stmtResolvS2 = $dbSiakad->prepare("
                                SELECT * FROM siswa 
                                WHERE (nik = ? AND nik != '' AND nik IS NOT NULL) 
                                   OR (nis = ? AND nis != '' AND nis IS NOT NULL)
                                   OR (nik = ? AND nik != '' AND nik IS NOT NULL) 
                                   OR (nis = ? AND nis != '' AND nis IS NOT NULL)
                                   OR (user_id = ? AND ? > 0)
                                LIMIT 1
                            ");
                            $stmtResolvS2->execute([$loginInput, $loginInput, $user['username'], $user['username'], $user['id'], $user['id']]);
                            $foundS = $stmtResolvS2->fetch();
                        } catch (\Throwable $e) {}
                    }
                    if ($foundS) {
                        $user['s_id'] = $foundS['id'];
                        $user['s_nama'] = $foundS['nama'];
                        $user['s_nis'] = $foundS['nis'];
                        $user['s_nik'] = $foundS['nik'];
                        try {
                            $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$user['id'], $foundS['id']]);
                            if ($dbSiakad) {
                                try { $dbSiakad->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$user['id'], $foundS['id']]); } catch (\Throwable $e) {}
                            }
                        } catch (\Throwable $e) {}
                    }
                }

                // 1. Cek apakah password cocok dengan hash (misal sudah diganti oleh pengguna)
                if (password_verify($password, $user['password_hash'])) {
                    $pass_ok = true;
                } 
                // 2. Cek plain password / MD5 legacy
                elseif (!empty($user['plain_password']) && $user['plain_password'] === $password) {
                    $pass_ok = true;
                } elseif (md5($password) === $user['password_hash']) {
                    $pass_ok = true;
                }
                // 3. Default Password = NIK Siswa
                elseif (!empty($user['s_nik']) && $password === $user['s_nik']) {
                    $pass_ok = true;
                } 
                // 4. Default Password = NIS Siswa
                elseif (!empty($user['s_nis']) && $password === $user['s_nis']) {
                    $pass_ok = true;
                }
                // 5. Default Password Siswa '123456'
                elseif ($user['role_id'] == 3 && $password === '123456') {
                    $pass_ok = true;
                }
                // 6. Username akun
                elseif ($password === $user['username']) {
                    $pass_ok = true;
                }
                // 7. Guru NIK / NIP
                elseif (!empty($user['g_nik']) && $password === $user['g_nik']) {
                    $pass_ok = true;
                } elseif (!empty($user['g_nip']) && $password === $user['g_nip']) {
                    $pass_ok = true;
                }
            }

            if ($user && $pass_ok) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                
                // Fetch Full Name based on role
                $nama = $user['username'];
                if ($user['role_id'] == 3) { // Siswa
                    $nama = $user['s_nama'] ?? $user['username'];
                    $_SESSION['siswa_id'] = $user['s_id'] ?? null;
                    $_SESSION['siswa_nama'] = $user['s_nama'] ?? '';
                    $_SESSION['siswa_nis'] = $user['s_nis'] ?? '';
                } elseif ($user['role_id'] == 2) { // Guru
                    $nama = $user['g_nama'] ?? $user['username'];
                    $_SESSION['guru_id'] = $user['g_id'] ?? null;
                    $_SESSION['guru_nama'] = $user['g_nama'] ?? '';
                    $_SESSION['guru_nip'] = $user['g_nip'] ?? '-';
                    if (!empty($user['g_kamad']) && $user['g_kamad'] == 1) {
                        $_SESSION['is_kamad'] = true;
                    }
                } elseif ($user['role_id'] == 5) { // Pengawas PKKM
                    $p = $db->query("SELECT nama, peran FROM pkkm_pengawas WHERE user_id = " . (int)$user['id'])->fetch();
                    if ($p) {
                        $nama = $p['nama'];
                        $_SESSION['peran_pengawas'] = $p['peran'];
                    }
                }
                $_SESSION['nama'] = $nama;

                // â”€â”€ Audit Log â”€â”€
                $roleMap = [1 => 'Admin', 2 => 'Guru', 3 => 'Siswa', 5 => 'Pengawas', 99 => 'Dev'];
                $roleName = $roleMap[$user['role_id']] ?? 'Unknown';
                AuditLogger::loginSuccess($user['username'], $roleName);

                if ($user['role_id'] == 99) {
                    redirect('/dev-panel');
                } elseif ($user['role_id'] == 5) {
                    redirect('/pengawas/pkkm');
                } elseif ($user['role_id'] == 2 || $user['role_id'] == 4) {
                    redirect('/guru/dashboard');
                } elseif ($user['role_id'] == 7) {
                    redirect('/apk/cbt.php');
                } elseif ($user['role_id'] == 6) {
                    redirect('/alumni/dashboard');
                } elseif ($user['role_id'] == 3 || !empty($_SESSION['siswa_id'])) {
                    redirect('/siswa/dashboard');
                } elseif ($user['role_id'] == 1) {
                    redirect('/portal');
                } else {
                    redirect('/login');
                }
                exit;
            } else {
                // â”€â”€ Audit Log: gagal login â”€â”€
                AuditLogger::loginFailed($username);
                $error = "Username atau Password salah!";
            }
        }

        $title = "Login - MA Miftahul Huda";
        ob_start();
        $instLogo = \App\Core\Branding::getInstitusiLogo();
        ?>
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
            <div class="card fade-up" style="width: 100%; max-width: 400px; padding: 3rem;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="margin: 0 auto 1.5rem; display: flex; justify-content: center;">
                        <img src="<?php echo $instLogo; ?>" alt="Logo" style="height: 80px; object-fit: contain;">
                    </div>
                    <h2 style="margin: 0; color: var(--primary-blue);">Portal Login</h2>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Masukkan kredensial untuk akses sistem.</p>
                </div>

                <form method="POST">
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600;">Username</label>
                        <input type="text" name="username" required style="width:100%; padding: 12px; border: 1px solid #eee; border-radius: 12px; background: #f9f9f9;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600;">Password</label>
                        <input type="password" name="password" required style="width:100%; padding: 12px; border: 1px solid #eee; border-radius: 12px; background: #f9f9f9;">
                    </div>

                    <?php if (isset($error)): ?>
                        <div style="color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1.5rem; text-align: center;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                        MASUK <i data-lucide="log-in"></i>
                    </button>

                    <div style="margin: 1.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <div style="flex: 1; height: 1px; background: #eee;"></div>
                        <div style="font-size: 0.75rem; color: #999; font-weight: 700;">ATAU</div>
                        <div style="flex: 1; height: 1px; background: #eee;"></div>
                    </div>

                    <button type="button" onclick="startQRLogin()" class="btn" style="width: 100%; justify-content: center; padding: 1rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 800;">
                        LOGIN DENGAN QR <i data-lucide="qr-code"></i>
                    </button>
                </form>

                <div style="margin-top: 2rem; text-align: center; font-size: 0.8rem; color: #999;">
                    Lupa password? Hubungi Administrator.
                </div>

                <div style="margin-top: 1rem; text-align: center;">
                    <a href="/download.php" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; color: #059669; text-decoration: none; background: #ecfdf5; padding: 6px 14px; border-radius: 20px; border: 1px solid #a7f3d0;">
                        <i data-lucide="smartphone" style="width: 14px; height: 14px;"></i> Download Aplikasi Android (.APK)
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal QR Scanner -->
        <div id="modal-qr-login" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); z-index: 3000; align-items: center; justify-content: center;">
            <div class="card" style="width: 100%; max-width: 500px; padding: 2rem; text-align: center; position: relative;">
                <button onclick="stopQRLogin()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #999; cursor: pointer;"><i data-lucide="x"></i></button>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; color: var(--primary-blue);">Scan Kartu Pelajar</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Arahkan QR Code pada kartu ke kamera.</p>
                </div>

                <div id="qr-reader" style="width: 100%; border-radius: 12px; overflow: hidden; background: #000; border: 4px solid #f1f5f9;"></div>
                
                <div id="qr-status" style="margin-top: 1rem; font-weight: 700; color: var(--primary-blue); min-height: 1.5rem;"></div>

                <div style="margin-top: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 12px; font-size: 0.8rem; color: #64748b; text-align: left;">
                    <i data-lucide="info" style="width: 14px; display: inline-block; vertical-align: middle;"></i> <b>Tips:</b> Pastikan pencahayaan cukup dan QR Code terlihat jelas di area kamera.
                </div>
            </div>
        </div>

        <script src="/public/assets/js/html5-qrcode.min.js"></script>
        <script>
            let html5QrCode;

            async function startQRLogin() {
                document.getElementById('modal-qr-login').style.display = 'flex';
                document.getElementById('qr-status').innerText = 'Memulai kamera...';
                
                html5QrCode = new Html5Qrcode("qr-reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                try {
                    await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                    document.getElementById('qr-status').innerText = 'Siap memindai...';
                } catch (err) {
                    document.getElementById('qr-status').style.color = '#dc2626';
                    document.getElementById('qr-status').innerText = 'Gagal mengakses kamera: ' + err;
                }
                lucide.createIcons();
            }

            async function onScanSuccess(decodedText, decodedResult) {
                document.getElementById('qr-status').innerText = 'QR Terdeteksi! Memverifikasi...';
                await html5QrCode.stop();
                
                // Kirim data ke backend
                const formData = new FormData();
                formData.append('qr_data', decodedText);

                try {
                    const response = await fetch('/login/qr', {
                        method: 'POST',
                        body: formData
                    });
                    const res = await response.json();

                    if(res.success) {
                        document.getElementById('qr-status').innerText = 'Login Berhasil! Mengalihkan...';
                        location.href = res.redirect;
                    } else {
                        document.getElementById('qr-status').style.color = '#dc2626';
                        document.getElementById('qr-status').innerText = 'Gagal: ' + res.message;
                        console.log('Debug QR Data:', res.debug_data);
                        
                        // Tampilkan data yang terbaca agar user bisa lapor
                        const debugEl = document.createElement('div');
                        debugEl.style.fontSize = '0.7rem';
                        debugEl.style.marginTop = '10px';
                        debugEl.style.color = '#666';
                        debugEl.innerText = 'Data Terbaca: ' + res.debug_data;
                        document.getElementById('qr-status').appendChild(debugEl);

                        // Restart scanner after 4 seconds on failure
                        setTimeout(startQRLogin, 4000);
                    }
                } catch (error) {
                    alert('Kesalahan koneksi ke server.');
                    stopQRLogin();
                }
            }

            function stopQRLogin() {
                if (html5QrCode) {
                    html5QrCode.stop().catch(() => {});
                }
                document.getElementById('modal-qr-login').style.display = 'none';
            }

            lucide.createIcons();
        </script>
        <?php
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function loginQR() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $qr_data = isset($_POST['qr_data']) ? $_POST['qr_data'] : '';
            
            $identifier = '';
            $role_detected = '';

            // 1. Cek format baru dengan TOKEN (Lebih Aman)
            if (stripos($qr_data, 'TOKEN:') === 0) {
                $parts = explode(':', $qr_data);
                if (count($parts) === 3) {
                    $uid = intval($parts[1]);
                    $hash = $parts[2];
                    $expected_hash = substr(md5($uid . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                    if ($hash === $expected_hash) {
                        $identifier = $uid;
                        $role_detected = 'token';
                    }
                }
            } 
            // 2. Format lama dihapus sesuai instruksi

            if ($identifier !== '') {
                $db = Database::connect('core');
                $user = null;

                if ($role_detected === 'token') {
                    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$identifier]);
                    $temp_user = $stmt->fetch();
                    
                    if ($temp_user) {
                        $user = $temp_user;
                        // Fetch nama tambahan
                        if ($user['role_id'] == 3) {
                            $s = $db->query("SELECT nama FROM siswa WHERE user_id = " . $user['id'])->fetch();
                            $user['nama'] = $s ? $s['nama'] : $user['username'];
                        } elseif ($user['role_id'] == 2) {
                            $g = $db->query("SELECT nama FROM guru WHERE user_id = " . $user['id'])->fetch();
                            $user['nama'] = $g ? $g['nama'] : $user['username'];
                        }
                    }
                }

                if ($user) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['nama'] = isset($user['nama']) ? $user['nama'] : $user['username'];
                    if ($role_detected === 'guru') {
                        $g = $db->query("SELECT is_kamad FROM guru WHERE user_id = " . $user['id'])->fetch();
                        if ($g && $g['is_kamad'] == 1) {
                            $_SESSION['is_kamad'] = true;
                        }
                    }

                    // Cek apakah siswa ini alumni
                    $is_alumni = false;
                    if ($role_detected === 'siswa') {
                        $s_status = $db->query("SELECT status FROM siswa WHERE user_id = " . intval($user['id']))->fetch();
                        if ($s_status && $s_status['status'] === 'Alumni') {
                            $is_alumni = true;
                            $_SESSION['siswa_status'] = 'Alumni';
                        }
                    }

                    // â”€â”€ Audit Log QR â”€â”€
                    AuditLogger::loginQr($identifier, true);

                    $redir = '/login';
                    if ($user['role_id'] == 99) $redir = '/dev-panel';
                    elseif ($user['role_id'] == 5) $redir = '/pengawas/pkkm';
                    elseif ($user['role_id'] == 2 || $user['role_id'] == 4) $redir = '/guru/dashboard';
                    elseif ($user['role_id'] == 7) $redir = '/apk/cbt.php';
                    elseif ($user['role_id'] == 6) $redir = '/alumni/dashboard';
                    elseif ($user['role_id'] == 3 || !empty($_SESSION['siswa_id'])) {
                        $redir = $is_alumni ? '/alumni/dashboard' : '/siswa';
                    } elseif ($user['role_id'] == 1) {
                        $redir = '/portal';
                    }

                    echo json_encode([
                        'success' => true, 
                        'redirect' => $redir
                    ]);
                    exit;
                }
            }
            
            // â”€â”€ Audit Log QR gagal â”€â”€
            AuditLogger::loginQr($qr_data, false);

            echo json_encode([
                'success' => false, 
                'message' => 'Kartu tidak dikenali atau akun tidak aktif.',
                'debug_data' => $qr_data,
                'debug_matches' => $matches ?? []
            ]);
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // â”€â”€ Audit Log â”€â”€
        $username = $_SESSION['username'] ?? 'unknown';
        AuditLogger::logout($username);
        session_destroy();
        redirect('/login');
        exit;
    }

    public function switchRole() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || empty($_SESSION['is_kamad'])) {
            redirect('/');
            exit;
        }

        $to = isset($_GET['to']) ? $_GET['to'] : '';
        if ($to === 'admin') {
            $_SESSION['role_id'] = 1; // Kamad acts as Admin
            redirect('/portal');
            exit;
        } elseif ($to === 'guru') {
            $_SESSION['role_id'] = 2; // Return to Guru
            redirect('/guru/dashboard');
            exit;
        }
        
        redirect('/');
        exit;
    }
}

