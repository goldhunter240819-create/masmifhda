<?php

namespace App\Controllers;

use App\Core\Database;

class ActivationController {
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['activation_code']);
            $current_domain = strtolower(trim($_SERVER['HTTP_HOST']));
            
            // Verify Algorithmic Key
            $salt = "GH-SSS-SULTAN-2026";
            $expected_hash = md5($current_domain . $salt);
            $expected_key = 'GHSSS-' . strtoupper(substr($expected_hash, 0, 4) . '-' . substr($expected_hash, 4, 4) . '-' . substr($expected_hash, 8, 4));

            if ($code === $expected_key) {
                $db = Database::connect('core');
                // Upsert: hanya update/insert untuk domain ini, tidak hapus domain lain
                $stmt = $db->prepare("INSERT INTO licenses (activation_code, status, domain) VALUES (?, 'aktif', ?)
                    ON DUPLICATE KEY UPDATE status='aktif', activation_code=VALUES(activation_code)");
                $stmt->execute([$code, $current_domain]);
                // Jika tidak ada kolom unique domain, fallback: hapus domain ini saja lalu insert
                if ($db->query("SELECT COUNT(*) FROM licenses WHERE domain = '" . addslashes($current_domain) . "'")->fetchColumn() == 0) {
                    $stmt2 = $db->prepare("INSERT INTO licenses (activation_code, status, domain) VALUES (?, 'aktif', ?)");
                    $stmt2->execute([$code, $current_domain]);
                }
                
                redirect('/');
                exit;
            } else {
                $error = "AUTH_ERROR: Invalid Activation Sequence for this Domain/IP.";
            }
        }

        $title = "SECURITY_GATE - GH-SSS";
        ob_start();
        ?>
        <div class="activation-gate" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #0a0a0a; display: flex; align-items: center; justify-content: center; z-index: 10000; overflow: hidden; font-family: 'Courier New', Courier, monospace;">
            <!-- Matrix-like Background -->
            <canvas id="matrix-canvas" style="position: absolute; top: 0; left: 0; opacity: 0.2;"></canvas>

            <div class="card fade-up" style="max-width: 450px; background: rgba(20, 20, 20, 0.9); border: 1px solid #008f11; backdrop-filter: blur(20px); color: #00ff41; padding: 3rem; text-align: center; box-shadow: 0 0 50px rgba(0, 143, 17, 0.2);">
                <div style="margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; border: 2px solid #00ff41; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; animation: rotateGate 4s linear infinite;">
                        <i data-lucide="shield-lock" style="width: 40px; height: 40px;"></i>
                    </div>
                </div>

                <h1 style="font-size: 1.5rem; letter-spacing: 3px; margin-bottom: 1rem;">SYSTEM_LOCKED</h1>
                <p style="font-size: 0.8rem; color: #00ff41; opacity: 0.7; line-height: 1.6; margin-bottom: 2rem;">GH-SSS Smart School System Pro. Silakan masukkan kode lisensi untuk melakukan autentikasi sistem pada domain ini.</p>

                <form method="POST">
                    <input type="text" name="activation_code" placeholder="ENTER_LICENSE_KEY" style="width: 100%; background: transparent; border: 1px solid #008f11; padding: 1rem; color: #00ff41; text-align: center; font-family: inherit; font-size: 1.1rem; border-radius: 8px; margin-bottom: 1.5rem; outline: none; box-shadow: inset 0 0 10px rgba(0, 143, 17, 0.1);">
                    
                    <?php if (isset($error)): ?>
                        <div style="color: #ff3d00; font-size: 0.8rem; margin-bottom: 1.5rem; border-left: 2px solid #ff3d00; padding-left: 10px; text-align: left;">
                            > <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn" style="width: 100%; background: #008f11; color: #000; font-weight: 800; padding: 1rem; justify-content: center; border-radius: 8px;">
                        DECRYPT_SYSTEM <i data-lucide="key-round"></i>
                    </button>
                </form>

                <p style="margin-top: 2rem; font-size: 0.7rem; color: #00ff41; opacity: 0.5;">DOMAIN_IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> | STATUS: UNAUTHORIZED</p>
            </div>
        </div>

        <style>
            @keyframes rotateGate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        </style>

        <script>
            // Matrix Effect
            const canvas = document.getElementById('matrix-canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const characters = '01ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const fontSize = 16;
            const columns = canvas.width / fontSize;
            const drops = Array(Math.floor(columns)).fill(1);

            function draw() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#008f11';
                ctx.font = fontSize + 'px monospace';

                for (let i = 0; i < drops.length; i++) {
                    const text = characters.charAt(Math.floor(Math.random() * characters.length));
                    ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                    if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
                    drops[i]++;
                }
            }
            setInterval(draw, 33);
            lucide.createIcons();
        </script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }
}
