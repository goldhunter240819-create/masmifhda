<?php
/**
 * MAS MIFHDA - Force Update Version Guard
 * Memastikan semua pengguna APK wajib memakai versi terbaru (ala CoC / Mobile Banking).
 */

function check_apk_version_guard() {
    $settings_file = __DIR__ . '/apk_settings.json';
    if (!file_exists($settings_file)) return;
    
    $settings = json_decode(file_get_contents($settings_file), true);
    if (!$settings || empty($settings['force_update'])) return;

    $required_version = $settings['min_apk_version'] ?? '26.2.2.1';
    $download_url = $settings['download_url'] ?? 'https://apk.masmifhda.sch.id/download.php';
    $direct_apk_url = 'https://apk.masmifhda.sch.id/masmifhda.apk';
    
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Jangan blokir halaman download atau webhook atau aset statis
    $current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if (in_array($current_script, ['download.php', 'webhook.php', 'apk_download_temp.php'])) {
        return;
    }

    // Deteksi apakah request berasal dari Android WebView / APK
    $is_masmifhda_app = (stripos($ua, 'MasMifhdaApp/') !== false);
    $is_android_webview = (stripos($ua, '; wv') !== false) || 
                          (stripos($ua, 'Version/4.0') !== false && stripos($ua, 'Chrome') !== false && stripos($ua, 'Android') !== false);
    
    if ($is_masmifhda_app || $is_android_webview) {
        $client_version = null;
        if (preg_match('/MasMifhdaApp\/([0-9.]+)/i', $ua, $matches)) {
            $client_version = $matches[1];
        }
        
        $needs_update = false;
        if (!$is_masmifhda_app) {
            // Ini APK lama (belum memiliki tag MasMifhdaApp)
            $needs_update = true;
            $client_version = 'Versi Lama (Kedaluwarsa)';
        } elseif ($client_version !== null && version_compare($client_version, $required_version, '<')) {
            // Versi di bawah batas minimum
            $needs_update = true;
        }
        
        if ($needs_update) {
            render_force_update_screen($client_version, $required_version, $download_url, $direct_apk_url);
            exit;
        }
    }
}

function render_force_update_screen($current_ver, $required_ver, $download_url, $direct_apk_url) {
    if (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code(426); // 426 Upgrade Required
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>Pembaruan Aplikasi Wajib - MAS MIFHDA</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --brand-primary: #059669;
                --brand-teal: #0d9488;
                --brand-accent: #10b981;
                --text-dark: #0f172a;
                --text-muted: #64748b;
            }
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: 'Plus Jakarta Sans', sans-serif;
                -webkit-tap-highlight-color: transparent;
            }
            body {
                background: linear-gradient(135deg, #020617 0%, #0f172a 60%, #064e3b 100%);
                color: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px 16px;
                position: relative;
                overflow-x: hidden;
            }
            .bg-glow {
                position: absolute;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
                border-radius: 50%;
                z-index: 0;
                pointer-events: none;
            }
            .update-card {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 420px;
                background: rgba(15, 23, 42, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 28px;
                padding: 32px 24px;
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 40px rgba(16, 185, 129, 0.15);
                text-align: center;
            }
            .icon-wrapper {
                position: relative;
                width: 90px;
                height: 90px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .icon-bg {
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                border-radius: 26px;
                transform: rotate(-6deg);
                box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
                animation: pulseGlow 3s ease-in-out infinite alternate;
            }
            @keyframes pulseGlow {
                0% { transform: rotate(-6deg) scale(0.98); }
                100% { transform: rotate(0deg) scale(1.04); }
            }
            .icon-wrapper i {
                position: relative;
                z-index: 2;
                font-size: 42px;
                color: #fff;
            }
            .badge-force {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                background: rgba(239, 68, 68, 0.18);
                border: 1px solid rgba(239, 68, 68, 0.35);
                border-radius: 100px;
                color: #fca5a5;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.8px;
                text-transform: uppercase;
                margin-bottom: 12px;
            }
            .badge-force .dot {
                width: 7px;
                height: 7px;
                background: #ef4444;
                border-radius: 50%;
                animation: blink 1.2s infinite;
            }
            @keyframes blink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.3; }
            }
            h1 {
                font-size: 1.55rem;
                font-weight: 900;
                line-height: 1.25;
                margin-bottom: 8px;
                background: linear-gradient(to right, #ffffff, #6ee7b7);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            p.desc {
                color: #94a3b8;
                font-size: 0.88rem;
                line-height: 1.55;
                margin-bottom: 22px;
            }
            .ver-comparison {
                display: flex;
                align-items: center;
                justify-content: space-around;
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 16px;
                padding: 12px 14px;
                margin-bottom: 22px;
            }
            .ver-box {
                text-align: center;
            }
            .ver-label {
                font-size: 0.7rem;
                text-transform: uppercase;
                color: #64748b;
                font-weight: 700;
                margin-bottom: 3px;
            }
            .ver-value {
                font-size: 0.95rem;
                font-weight: 800;
            }
            .ver-value.old { color: #f87171; }
            .ver-value.new { color: #34d399; }
            .ver-arrow {
                color: #64748b;
                font-size: 1rem;
            }
            .btn-update {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                width: 100%;
                padding: 15px 20px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: #fff;
                font-size: 1rem;
                font-weight: 800;
                border-radius: 16px;
                text-decoration: none;
                box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
                transition: transform 0.2s, box-shadow 0.2s;
                margin-bottom: 12px;
            }
            .btn-update:active {
                transform: scale(0.98);
            }
            .btn-direct {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 12px 16px;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.15);
                color: #e2e8f0;
                font-size: 0.86rem;
                font-weight: 700;
                border-radius: 14px;
                text-decoration: none;
                transition: background 0.2s;
                margin-bottom: 20px;
            }
            .btn-direct:hover {
                background: rgba(255, 255, 255, 0.15);
            }
            .guide-box {
                text-align: left;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 14px;
                padding: 14px;
                border: 1px dashed rgba(255, 255, 255, 0.1);
            }
            .guide-box h4 {
                font-size: 0.78rem;
                text-transform: uppercase;
                color: #38bdf8;
                font-weight: 800;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .guide-box ol {
                padding-left: 18px;
                color: #94a3b8;
                font-size: 0.78rem;
                line-height: 1.5;
            }
            .guide-box ol li {
                margin-bottom: 4px;
            }
            .guide-box .note {
                margin-top: 8px;
                color: #f59e0b;
                font-size: 0.72rem;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="bg-glow"></div>
        <div class="update-card">
            <div class="badge-force">
                <span class="dot"></span>
                Pembaruan Aplikasi Wajib
            </div>
            <div class="icon-wrapper">
                <div class="icon-bg"></div>
                <i class="fa-solid fa-cloud-arrow-down"></i>
            </div>
            <h1>Update Aplikasi</h1>
            <p class="desc">
                Versi aplikasi yang Anda gunakan sudah tidak didukung. Untuk menjaga keamanan ujian (Anti-Cheat) serta kelancaran fitur terbaru, silakan pasang versi resmi <b>v<?= htmlspecialchars($required_ver) ?></b>.
            </p>

            <div class="ver-comparison">
                <div class="ver-box">
                    <div class="ver-label">Versi Anda</div>
                    <div class="ver-value old"><?= htmlspecialchars($current_ver) ?></div>
                </div>
                <div class="ver-arrow">➔</div>
                <div class="ver-box">
                    <div class="ver-label">Versi Wajib</div>
                    <div class="ver-value new">v<?= htmlspecialchars($required_ver) ?></div>
                </div>
            </div>

            <a href="<?= htmlspecialchars($download_url) ?>" 
               onclick="if (typeof Android !== 'undefined' && typeof Android.startInAppUpdate === 'function') { Android.startInAppUpdate('https://apk.masmifhda.sch.id/download.php?action=file'); return false; } else if (typeof AndroidOneSignal !== 'undefined' && typeof AndroidOneSignal.startInAppUpdate === 'function') { AndroidOneSignal.startInAppUpdate('https://apk.masmifhda.sch.id/download.php?action=file'); return false; }"
               class="btn-update">
                <i class="fa-solid fa-download"></i> Unduh & Pasang Otomatis (.APK)
            </a>

            <a href="<?= htmlspecialchars($direct_apk_url) ?>" class="btn-direct">
                <i class="fa-solid fa-bolt"></i> Unduh Langsung (Server Madrasah)
            </a>

            <div class="guide-box">
                <h4><i class="fa-solid fa-circle-info"></i> Petunjuk Singkat:</h4>
                <ol>
                    <li>Klik tombol <b>Unduh Pembaruan</b> di atas.</li>
                    <li>Setelah unduhan selesai, klik file <code>MAS_MIFHDA_v<?= htmlspecialchars($required_ver) ?>.apk</code> untuk menginstal.</li>
                </ol>
                <div class="note">
                    * Jika muncul peringatan bentrok dengan versi lama di HP, hapus (uninstall) aplikasi lama terlebih dahulu, lalu instal kembali.
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
