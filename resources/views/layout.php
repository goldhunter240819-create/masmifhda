<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Aula System'; ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo isset($site_favicon) ? $site_favicon : base_url('public/assets/images/favicon.png'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <style>
        :root {
            --primary-rgb: <?php 
                $tc = isset($theme_color) ? $theme_color : '0ea5e9';
                if(strpos($tc, '#') === false) $tc = '#' . $tc;
                list($r, $g, $b) = sscanf($tc, "#%02x%02x%02x");
                echo "$r, $g, $b";
            ?>;
            --z-primary: <?php echo $tc; ?>;
            --primary-gradient: linear-gradient(135deg, <?php echo $tc; ?> 0%, rgba(<?php echo "$r, $g, $b"; ?>, 0.8) 100%);
        }
    </style>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css?v=<?php echo time(); ?>'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/all.min.css'); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body > .container {
            flex: 1;
            width: 100%;
        }

        /* PREMIUM NOTIFICATION SYSTEM (ZNS) */
        #zns-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .zns-toast {
            pointer-events: auto;
            min-width: 300px;
            padding: 16px 20px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .zns-toast.active { transform: translateX(0); }
        .zns-toast.success { border-left: 5px solid #0ea5e9; }
        .zns-toast.error { border-left: 5px solid #ef4444; }
        .zns-icon { width: 24px; height: 24px; }
        .zns-success .zns-icon { color: #0ea5e9; }
        .zns-error .zns-icon { color: #ef4444; }
        .zns-msg { font-weight: 600; color: #1e293b; font-size: 0.95rem; }

        /* MODAL CONFIRM */
        #zns-confirm-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .zns-confirm-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9);
            transition: 0.3s;
        }
        #zns-confirm-overlay.active { display: flex; }
        #zns-confirm-overlay.active .zns-confirm-card { transform: scale(1); }
        .zns-confirm-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
        .zns-confirm-msg { color: #64748b; margin-bottom: 25px; line-height: 1.5; }
        .zns-confirm-btns { display: flex; gap: 10px; justify-content: center; }
        .zns-btn { padding: 10px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; transition: 0.2s; }
        .zns-btn-cancel { background: #f1f5f9; color: #64748b; }
        .zns-btn-confirm { background: #0ea5e9; color: white; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2); }
        .zns-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }

        /* GLOBAL RESPONSIVE IMAGES */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <?php if (strpos($_SERVER['REQUEST_URI'], 'siakad') !== false): ?>
    <style>
        body {
            font-size: 13.5px !important;
        }
        h1 { font-size: 1.8rem !important; }
        h2 { font-size: 1.5rem !important; }
        h3 { font-size: 1.25rem !important; }
        p, span, td, th, a, input, select, textarea, button {
            font-size: 13px !important;
        }
        /* Make table paddings slightly tighter for a compact, minimalist vibe */
        th, td {
            padding: 8px 12px !important;
        }
        /* Form controls compact */
        .form-control, .input-sm {
            padding: 8px 12px !important;
            font-size: 13px !important;
            border-radius: 10px !important;
        }
        /* Action buttons slightly smaller */
        .btn, .action-btn {
            padding: 6px 14px !important;
            font-size: 13px !important;
            border-radius: 8px !important;
        }
        /* Sidebar items slightly more compact */
        .sidebar-menu li a, .menu-item, .menu-header {
            padding: 8px 12px !important;
            font-size: 13px !important;
            border-radius: 10px !important;
        }
        .submenu-item {
            font-size: 12px !important;
            padding: 4px 0 !important;
        }
    </style>
    <?php endif; ?>

</head>
<body>
    <?php 
        $uri = $_SERVER['REQUEST_URI'];
        
        // Logika pemetaan folder aplikasi berdasarkan URL
        $app_folder = 'main';
        if (strpos($uri, 'cbt') !== false) {
            $app_folder = 'cbt';
        } elseif (strpos($uri, 'siakad') !== false) {
            $app_folder = 'siakad';
        } elseif (strpos($uri, 'dev-panel') !== false) {
            $app_folder = 'developer';
        }

        // Path ke komponen khusus aplikasi
        $custom_header = __DIR__ . '/' . $app_folder . '/header.php';
        $custom_footer = __DIR__ . '/' . $app_folder . '/footer.php';

        // Tampilkan header
        if ($app_folder !== 'developer' && !isset($hide_header_footer)) {
            if (file_exists($custom_header) && $app_folder !== 'main') {
                include $custom_header;
            } else {
                include __DIR__ . '/header.php';
            }
        }
    ?>

    <div class="container fade-up">
        <?php echo $content; ?>
    </div>

    <?php 
        // Tampilkan footer
        if ($app_folder !== 'developer' && !isset($hide_header_footer)) {
            if (file_exists($custom_footer) && $app_folder !== 'main') {
                include $custom_footer;
            } else {
                include __DIR__ . '/footer.php';
            }
        }
    ?>

    <!-- Zenith Notification Containers -->
    <div id="zns-container"></div>
    <div id="zns-confirm-overlay">
        <div class="zns-confirm-card">
            <div id="zns-confirm-icon" style="margin-bottom: 15px; color: #0ea5e9;">
                <i data-lucide="help-circle" style="width: 48px; height: 48px;"></i>
            </div>
            <div class="zns-confirm-title" id="zns-confirm-title">Konfirmasi</div>
            <div class="zns-confirm-msg" id="zns-confirm-msg">Apakah Anda yakin?</div>
            <div class="zns-confirm-btns">
                <button class="zns-btn zns-btn-cancel" onclick="ZNS.closeConfirm(false)">Batal</button>
                <button class="zns-btn zns-btn-confirm" id="zns-confirm-btn-ok" onclick="ZNS.closeConfirm(true)">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Zenith Notification System Logic
        const ZNS = {
            confirmCallback: null,
            showSuccess(msg) { this.createToast(msg, 'success', 'check-circle'); },
            showError(msg) { this.createToast(msg, 'error', 'alert-circle'); },
            createToast(msg, type, icon) {
                const id = 'zns-' + Date.now();
                const html = `
                    <div id="${id}" class="zns-toast ${type} zns-${type}">
                        <i data-lucide="${icon}" class="zns-icon"></i>
                        <span class="zns-msg">${msg}</span>
                    </div>
                `;
                document.getElementById('zns-container').insertAdjacentHTML('beforeend', html);
                lucide.createIcons();
                setTimeout(() => document.getElementById(id).classList.add('active'), 10);
                setTimeout(() => {
                    const el = document.getElementById(id);
                    if(el) {
                        el.classList.remove('active');
                        setTimeout(() => el.remove(), 500);
                    }
                }, 4000);
            },
            alert(msg, title = 'Informasi', callback = null) {
                document.getElementById('zns-confirm-msg').innerText = msg;
                document.getElementById('zns-confirm-title').innerText = title;
                document.getElementById('zns-confirm-btn-ok').innerText = 'OK';
                document.querySelector('.zns-btn-cancel').style.display = 'none'; // Hide cancel for alert
                this.confirmCallback = callback;
                document.getElementById('zns-confirm-overlay').classList.add('active');
            },
            confirm(msg, callback, title = 'Konfirmasi', btnText = 'Ya, Lanjutkan') {
                document.getElementById('zns-confirm-msg').innerText = msg;
                document.getElementById('zns-confirm-title').innerText = title;
                document.getElementById('zns-confirm-btn-ok').innerText = btnText;
                document.querySelector('.zns-btn-cancel').style.display = 'inline-block';
                this.confirmCallback = callback;
                document.getElementById('zns-confirm-overlay').classList.add('active');
            },
            closeConfirm(result) {
                document.getElementById('zns-confirm-overlay').classList.remove('active');
                if (result && this.confirmCallback) this.confirmCallback();
                this.confirmCallback = null;
            }
        };

        // Override standard functions if needed (optional)
        // window.alert = (msg) => ZNS.showSuccess(msg);
    </script>
    </script>
</body>
</html>


