<?php
/**
 * MAS MIFHDA Mobile APK - Dedicated Download Center & Streamer
 */

$apkPath = __DIR__ . '/masmifhda.apk';
$apkFilename = 'MAS_MIFHDA_Mobile.apk';
$apkSize = file_exists($apkPath) ? filesize($apkPath) : 12173414;
$apkSizeFormatted = number_format($apkSize / (1024 * 1024), 1) . ' MB';

// Direct file stream request
if ((isset($_GET['action']) && $_GET['action'] === 'file') || isset($_GET['direct'])) {
    if (!file_exists($apkPath)) {
        http_response_code(404);
        die("File APK belum tersedia di server. Hubungi administrator madrasah.");
    }

    // Disable caching for latest apk download
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="' . $apkFilename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $apkSize);

    // Clean output buffer to prevent corrupted apk downloads
    if (ob_get_level()) {
        ob_end_clean();
    }
    flush();
    readfile($apkPath);
    exit;
}

$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$downloadDirectUrl = $currentUrl . '/masmifhda.apk';
$downloadPageUrl = $currentUrl . '/download.php';
$streamUrl = $currentUrl . '/download.php?action=file';
$isAuto = isset($_GET['auto']) && $_GET['auto'] == '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Download Aplikasi Mobile MAS MIFHDA - Resmi Android (.APK)</title>
    <link rel="icon" type="image/png" href="/public/assets/images/logo.png">
    <link rel="apple-touch-icon" href="/public/assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($isAuto): ?>
    <meta http-equiv="refresh" content="2;url=<?= htmlspecialchars($streamUrl) ?>">
    <?php endif; ?>
    <style>
        :root {
            --brand-primary: #059669;
            --brand-primary-dark: #047857;
            --brand-accent: #10b981;
            --brand-teal: #0d9488;
            --brand-navy: #0f172a;
            --brand-slate: #1e293b;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: linear-gradient(135deg, #020617 0%, #0f172a 50%, #064e3b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Effect */
        body::before {
            content: '';
            position: fixed;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(5, 150, 105, 0.05) 50%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
            filter: blur(40px);
        }

        .container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        /* Header Card */
        .glass-card {
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 32px 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(16, 185, 129, 0.15);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #3b82f6, #06b6d4);
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
        }

        /* App Icon Mockup */
        .app-icon-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto 18px;
            position: relative;
        }

        .app-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #065f46 0%, #047857 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(52, 211, 153, 0.3);
            position: relative;
            overflow: hidden;
        }

        .app-icon img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.25));
        }

        .android-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            background: #22c55e;
            color: #ffffff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border: 2px solid #0f172a;
        }

        h1.app-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        p.app-subtitle {
            font-size: 0.88rem;
            color: #94a3b8;
            line-height: 1.45;
            margin-bottom: 22px;
        }

        /* Spec Badges Grid */
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 12px 10px;
            margin-bottom: 24px;
        }

        .spec-item {
            text-align: center;
        }

        .spec-label {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .spec-value {
            font-size: 0.88rem;
            color: #f1f5f9;
            font-weight: 700;
        }

        /* Main Download Button */
        .btn-download-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 800;
            padding: 16px 20px;
            border-radius: 18px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4), inset 0 1px 2px rgba(255, 255, 255, 0.3);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-download-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.5);
        }

        .btn-download-primary:active {
            transform: translateY(1px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-download-primary i {
            font-size: 1.3rem;
            animation: bounceDown 1.8s infinite;
        }

        @keyframes bounceDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(4px); }
        }

        /* Secondary Action Buttons */
        .action-row {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .btn-secondary {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 11px 12px;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Installation Guide Box */
        .guide-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            padding: 22px 20px;
            margin-top: 20px;
            text-align: left;
        }

        .guide-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .guide-title i {
            color: #38bdf8;
        }

        .step-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.82rem;
            color: #cbd5e1;
            line-height: 1.5;
        }

        .step-num {
            width: 24px;
            height: 24px;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 800;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-item b {
            color: #ffffff;
        }

        /* Security Assurance */
        .security-badge {
            margin-top: 18px;
            padding: 10px 14px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px dashed rgba(16, 185, 129, 0.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.74rem;
            color: #6ee7b7;
            text-align: center;
        }

        /* Toast Alert */
        #toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #10b981;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 0.75rem;
            color: #64748b;
        }

        .footer-note a {
            color: #38bdf8;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="glass-card">
            
            <div class="verified-badge">
                <i class="fa-solid fa-shield-halved"></i> APLIKASI RESMI TERVERIFIKASI
            </div>

            <div class="app-icon-wrap">
                <div class="app-icon">
                    <img src="/public/assets/images/logo.png" alt="Logo MAS MIFHDA" onerror="this.src='/public/logo_kemenag.png'">
                </div>
                <div class="android-badge">
                    <i class="fa-brands fa-android"></i>
                </div>
            </div>

            <h1 class="app-title">MAS MIFHDA Mobile</h1>
            <p class="app-subtitle">Aplikasi Android Resmi CBT Online, Ujian Terpadu & SIAKAD MA Miftahul Huda</p>

            <div class="specs-grid">
                <div class="spec-item">
                    <div class="spec-label">Format</div>
                    <div class="spec-value">.APK</div>
                </div>
                <div class="spec-item">
                    <div class="spec-label">Ukuran</div>
                    <div class="spec-value"><?= htmlspecialchars($apkSizeFormatted) ?></div>
                </div>
                <div class="spec-item">
                    <div class="spec-label">Android</div>
                    <div class="spec-value">5.0+</div>
                </div>
            </div>

            <!-- Primary Download Button -->
            <a href="<?= htmlspecialchars($streamUrl) ?>" class="btn-download-primary" id="btn-download-action">
                <i class="fa-solid fa-download"></i>
                <span>Download APK Sekarang</span>
            </a>

            <!-- Secondary Actions -->
            <div class="action-row">
                <button type="button" class="btn-secondary" onclick="copyDownloadLink()">
                    <i class="fa-solid fa-copy"></i> Salin Link
                </button>
                <a href="/apk/cbt.php" class="btn-secondary">
                    <i class="fa-solid fa-laptop-code"></i> Buka CBT Web
                </a>
            </div>

            <div class="security-badge">
                <i class="fa-solid fa-circle-check"></i> Bebas Virus & Terlindungi dari Pemblokiran
            </div>

            <!-- Installation Instructions -->
            <div class="guide-card">
                <div class="guide-title">
                    <i class="fa-solid fa-circle-question"></i> Cara Memasang di Android:
                </div>
                <div class="step-list">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div>Klik tombol hijau <b>Download APK Sekarang</b> di atas dan tunggu proses selesai.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div>Buka bar notifikasi atau folder <b>Download</b> di ponsel Anda, lalu ketuk file <b>MAS_MIFHDA_Mobile.apk</b>.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div>Jika muncul peringatan keamanan, pilih <b>Tetap Instal / Install Anyway</b> atau aktifkan izin <i>"Sumber Tidak Dikenal"</i> di pengaturan Chrome/Browser.</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="footer-note">
            &copy; <?= date('Y') ?> MA Miftahul Huda. All rights reserved.<br>
            Butuh bantuan? <a href="https://wa.me/?text=Halo%20Admin%20MAS%20MIFHDA%2C%20saya%20butuh%20bantuan%20terkait%20Aplikasi%20Mobile%20CBT." target="_blank"><i class="fa-brands fa-whatsapp"></i> Hubungi Admin</a>
        </div>
    </div>

    <div id="toast">
        <i class="fa-solid fa-check"></i> Link download berhasil disalin!
    </div>

    <script>
        function copyDownloadLink() {
            const url = '<?= htmlspecialchars($downloadPageUrl) ?>';
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(showToast);
            } else {
                const ta = document.createElement("textarea");
                ta.value = url;
                ta.style.position = "fixed";
                ta.style.left = "-999999px";
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                    showToast();
                } catch (err) {}
                document.body.removeChild(ta);
            }
        }

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        <?php if ($isAuto): ?>
        // Auto feedback
        const btn = document.getElementById('btn-download-action');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mengunduh Otomatis...</span>';
        }
        <?php endif; ?>
    </script>
</body>
</html>
