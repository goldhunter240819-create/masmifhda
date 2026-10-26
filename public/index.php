<?php
// GHTech Analytics Tracking
$_ghtech_key = "4a0fef55431a38a8ee6317c512178502";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}
// Track IP langsung
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip));
// Update username setelah session aktif
register_shutdown_function(function() {
    global $_ghtech_key, $_ghtech_ip;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_u = "";
        if (isset($_SESSION["guru_nama"])) { $_u = $_SESSION["guru_nama"]; }
        elseif (isset($_SESSION["siswa_nama"])) { $_u = $_SESSION["siswa_nama"]; }
        elseif (isset($_SESSION["nama"])) { $_u = $_SESSION["nama"]; }
        if (!empty($_u)) {
            @file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_u));
        }
    }
});
?>
<?php

date_default_timezone_set('Asia/Jakarta');
session_name("AULA_SYSTEM_PORTAL");

require_once __DIR__ . '/../vendor/autoload.php';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $base = str_replace('\\', '/', $base);
        $base = preg_replace('#/public.*$#', '', $base);
        if ($base === '/' || $base === '\\') $base = '';
        $path = ltrim($path, '/');
        return $base . '/' . $path;
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        header('Location: ' . base_url($path));
        exit;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    // Perpanjang masa aktif sesi menjadi 1 Hari (86400 detik)
    session_save_path(__DIR__ . '/../storage/sessions');
    ini_set('session.gc_maxlifetime', 86400);
    ini_set('session.cookie_lifetime', 86400);
    session_start();
}

use App\Core\Router;
use App\Middleware\LicenseMiddleware;
use App\Middleware\AuthMiddleware;

// Security Guards
LicenseMiddleware::handle();
AuthMiddleware::handle();

// Global Activity Tracker (Debounced 60 detik)
if (isset($_SESSION['user_id'])) {
    $now = time();
    if (!isset($_SESSION['last_activity_ping']) || ($now - $_SESSION['last_activity_ping'] > 60)) {
        try {
            $db_track = \App\Core\Database::connect('core');
            $db_track->query("UPDATE users SET last_activity = NOW() WHERE id = " . intval($_SESSION['user_id']));
            $_SESSION['last_activity_ping'] = $now;
        } catch (\Exception $e) {}
    }
}

// Global Visitor Tracker (Simple DB logger)
try {
    $db_track = \App\Core\Database::connect('core');
    $db_track->exec("CREATE TABLE IF NOT EXISTS visitor_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        visit_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY(ip_address, visit_date)
    )");
    
    // Fallback if IP is empty, don't insert to avoid issues
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
        $stmt_vis = $db_track->prepare("INSERT IGNORE INTO visitor_logs (ip_address, visit_date) VALUES (?, CURDATE())");
        $stmt_vis->execute([$visitorIp]);
    }
} catch (\Exception $e) {}

$router = new Router();

// --- GLOBAL BRANDING FETCH ---
$db_core = \App\Core\Database::connect('core');
$portal_mod = $db_core->query("SELECT * FROM app_modules WHERE app_code = 'portal'")->fetch();
$portal_name = ($portal_mod && isset($portal_mod['app_name'])) ? $portal_mod['app_name'] : "Portal GH-SSS";
$portal_version = ($portal_mod && isset($portal_mod['app_version'])) ? $portal_mod['app_version'] : "1.0";
$portal_theme = ($portal_mod && isset($portal_mod['theme_color'])) ? $portal_mod['theme_color'] : "0ea5e9";

// Ambil logo institusi sebagai sumber logo/favicon default
$_inst_logo    = \App\Core\Branding::getInstitusiLogo();
$_inst_favicon = \App\Core\Branding::getInstitusiFavicon();

$portal_logo    = ($portal_mod && isset($portal_mod['logo_path']) && $portal_mod['logo_path'])
    ? base_url('public/assets/images/') . $portal_mod['logo_path']
    : $_inst_logo;
$portal_favicon = ($portal_mod && isset($portal_mod['favicon_path']) && $portal_mod['favicon_path'])
    ? base_url('public/assets/images/') . $portal_mod['favicon_path']
    : $_inst_favicon;
$portal_title = ($portal_mod && isset($portal_mod['tab_title'])) ? $portal_mod['tab_title'] : "GH-SSS Platform";


// Define routes
// Alias /portal -> redirect ke homepage
$router->add('GET', '/hacker', function() {
    include __DIR__ . '/hacker.php';
    exit;
});

// Route /ping untuk Antilogout
$router->add('GET', '/ping', function() {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'time' => time()]);
    exit;
});

// Admin Website (Landing Page)

$router->add('GET', '/perpustakaan', function() {
    include __DIR__ . '/perpustakaan.php';
    exit;
});

$router->add('GET', '/portal', function() use ($portal_title, $portal_logo, $portal_favicon, $portal_version, $portal_theme) {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    $is_logged_in = isset($_SESSION['user_id']);

    // Ã¢â€â‚¬Ã¢â€â‚¬ GUEST: redirect to login instead of welcome page Ã¢â€â‚¬Ã¢â€â‚¬
    if (!$is_logged_in) {
        redirect('/login');
        exit;
    }

    if ($is_logged_in && $role == 2) {
        redirect('/guru/dashboard');
        exit;
    }
    if ($is_logged_in && $role == 3) {
        redirect('/siswa/dashboard');
        exit;
    }

    $title = $portal_title;
    $site_logo = $portal_logo;
    $site_favicon = $portal_favicon;
    $site_version = $portal_version;
    $theme_color = $portal_theme;

    // --- LIVE STATS ---
    $stats = array('surat'=>0, 'aset'=>0, 'buku'=>0, 'tugas'=>0);
    if ($is_logged_in && in_array($role, array(1, 99))) {
        $db = \App\Core\Database::connect('core');
        $rs = $db->query('SELECT COUNT(*) as c FROM persuratan_masuk')->fetch(); $stats['surat'] = $rs ? $rs['c'] : 0;
        $ra = $db->query('SELECT COUNT(*) as c FROM inv_aset')->fetch(); $stats['aset'] = $ra ? $ra['c'] : 0;
        $rb = $db->query('SELECT COUNT(*) as c FROM perpus_buku')->fetch(); $stats['buku'] = $rb ? $rb['c'] : 0;
        $rt = $db->query("SELECT COUNT(*) as c FROM elearning_tugas WHERE status = 'Aktif'")->fetch(); $stats['tugas'] = $rt ? $rt['c'] : 0;
    }

    ob_start();
    ?>
    <style>
        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ PORTAL REDESIGN Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .portal-wrap {
            min-height: calc(100vh - 140px);
            padding: 1rem 0 3rem;
        }

        /* Hero */
        .portal-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2.5rem 1rem 2rem;
            margin-bottom: 0.5rem;
            position: relative;
        }
        .portal-greeting {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg,rgba(15,23,42,0.08),rgba(14,165,233,0.08));
            border: 1px solid rgba(14,165,233,0.2);
            padding: 6px 18px;
            border-radius: 100px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #0284c7;
            margin-bottom: 1.2rem;
            letter-spacing: 0.3px;
        }
        .portal-greeting span.dot {
            width: 7px; height: 7px;
            background: #0ea5e9;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(14,165,233,0.25);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%,100% { box-shadow: 0 0 0 3px rgba(14,165,233,0.25); }
            50%      { box-shadow: 0 0 0 6px rgba(14,165,233,0.1); }
        }
        .portal-hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 0.75rem;
            color: #0f172a;
        }
        .portal-hero h1 .grad {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 60%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .portal-hero p {
            font-size: 1rem;
            color: #6b7280;
            max-width: 500px;
            line-height: 1.65;
            margin: 0;
        }

        /* Section Label */
        .section-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 2rem 0 1rem;
        }
        .section-label span {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
        }
        .section-label::before, .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        .section-label.green span { color: #0284c7; }
        .section-label.green::before, .section-label.green::after { background: #e0f2fe; }

        /* Module Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
        }

        /* Module Card - Premium */
        .mod-card {
            position: relative;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 1.75rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            animation: fadeUp 0.6s cubic-bezier(0.4,0,0.2,1) backwards;
        }
        .mod-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .mod-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: transparent;
        }
        .mod-card.disabled { cursor: default; opacity: 0.85; }
        .mod-card.disabled:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        /* Card color themes */
        .mod-card:hover.theme-blue  { border-color: #7dd3fc; box-shadow: 0 20px 40px rgba(15,23,42,0.12); }
        .mod-card:hover.theme-violet { border-color: #c4b5fd; box-shadow: 0 20px 40px rgba(124,58,237,0.1); }
        .mod-card:hover.theme-blue   { border-color: #93c5fd; box-shadow: 0 20px 40px rgba(37,99,235,0.1); }
        .mod-card:hover.theme-amber  { border-color: #fcd34d; box-shadow: 0 20px 40px rgba(245,158,11,0.1); }
        .mod-card:hover.theme-rose   { border-color: #fca5a5; box-shadow: 0 20px 40px rgba(239,68,68,0.1); }
        .mod-card:hover.theme-purple { border-color: #d8b4fe; box-shadow: 0 20px 40px rgba(147,51,234,0.1); }
        .mod-card:hover.theme-blue   { border-color: #5eead4; box-shadow: 0 20px 40px rgba(13,148,136,0.1); }

        /* Icon */
        .mod-icon-wrap {
            width: 54px; height: 54px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            flex-shrink: 0;
        }
        .mod-icon-wrap i { width: 26px; height: 26px; }
        .mod-card:hover .mod-icon-wrap { transform: scale(1.15) rotate(-5deg); }

        .icon-blue  { background: #e0f2fe; color: #0284c7; }
        .icon-violet { background: #ede9fe; color: #7c3aed; }
        .icon-blue   { background: #eff6ff; color: #2563eb; }
        .icon-amber  { background: #fffbeb; color: #d97706; }
        .icon-rose   { background: #fff1f2; color: #e11d48; }
        .icon-purple { background: #faf5ff; color: #9333ea; }
        .icon-teal   { background: #f0fdfa; color: #0d9488; }

        /* Card content */
        .mod-body { flex: 1; }
        .mod-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 5px;
            letter-spacing: -0.2px;
        }
        .mod-desc {
            font-size: 0.82rem;
            color: #6b7280;
            line-height: 1.55;
            margin: 0;
        }

        /* Arrow */
        .mod-arrow {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 32px; height: 32px;
            border-radius: 10px;
            background: #f3f4f6;
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af;
            transition: all 0.3s;
        }
        .mod-card:hover .mod-arrow {
            background: #0284c7;
            color: #fff;
            transform: translate(2px, -2px);
        }
        .mod-card.disabled .mod-arrow { display: none; }

        /* Badge */
        .mod-badge {
            position: absolute;
            top: 14px; right: 14px;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 9px;
            border-radius: 100px;
        }
        .badge-soon { background: #fef9c3; color: #a16207; border: 1px solid #fde68a; }
        .badge-new  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-live { background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc; }

        /* Sub-link inside card */
        .mod-sublink {
            margin-top: 6px;
            padding-top: 10px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #7c3aed;
            text-decoration: none;
        }
        .mod-sublink:hover { color: #5b21b6; }

        /* Stagger animations */
        .d1 { animation-delay: 0.05s; }
        .d2 { animation-delay: 0.10s; }
        .d3 { animation-delay: 0.15s; }
        .d4 { animation-delay: 0.20s; }
        .d5 { animation-delay: 0.25s; }
        .d6 { animation-delay: 0.30s; }
        .d7 { animation-delay: 0.35s; }
        .d8 { animation-delay: 0.40s; }
        .d9 { animation-delay: 0.45s; }
        .d10 { animation-delay: 0.50s; }
        .d11 { animation-delay: 0.55s; }
        .d12 { animation-delay: 0.60s; }

        @media (max-width: 640px) {
            .modules-grid { grid-template-columns: 1fr; }
            .portal-hero h1 { font-size: 1.75rem; }
            .mod-card { padding: 1.4rem; }
        }
        @media (min-width: 641px) and (max-width: 1024px) {
            .modules-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    <?php
    $hour = (int) date('H');
    if ($hour < 11)      $greet = 'Selamat Pagi';
    elseif ($hour < 15)  $greet = 'Selamat Siang';
    elseif ($hour < 18)  $greet = 'Selamat Sore';
    else                 $greet = 'Selamat Malam';

    $userName = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';
    $isAdmin  = isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], array(1, 99));
    $isFinanceAdmin = $isAdmin;
    if (!$isFinanceAdmin && isset($_SESSION['user_id'])) {
        try {
            $dbP = \App\Core\Database::connect();
            $cekFA = $dbP->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'keuangan'");
            $cekFA->execute([$_SESSION['user_id']]);
            if ($cekFA->fetch()) {
                $isFinanceAdmin = true;
            } else {
                $cekFB = $dbP->prepare("SELECT ka.id FROM keuangan_akses ka JOIN guru g ON ka.guru_id = g.id WHERE g.user_id = ?");
                $cekFB->execute([$_SESSION['user_id']]);
                if ($cekFB->fetch()) $isFinanceAdmin = true;
            }
        } catch (\Exception $e) {}
    }
    $financeUrl = $isFinanceAdmin ? base_url('admin/finance') : base_url('keuangan');

    $isTabunganAdmin = $isAdmin;
    if (!$isTabunganAdmin && isset($_SESSION['user_id'])) {
        try {
            $dbP = \App\Core\Database::connect();
            $cekTA = $dbP->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = ?");
            $cekTA->execute([$_SESSION['user_id']]);
            if ($cekTA->fetch()) {
                $isTabunganAdmin = true;
            } else {
                $cekTA2 = $dbP->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'");
                $cekTA2->execute([$_SESSION['user_id']]);
                if ($cekTA2->fetch()) $isTabunganAdmin = true;
            }
        } catch (\Exception $e) {}
    }
    $tabunganUrl = $isTabunganAdmin ? base_url('admin/tabungan') : base_url('tabungan');

    // Portal Card Manager Settings
    $configFile = __DIR__ . '/../config/portal_cards.json';
    $portalCardsConfig = [];
    if (file_exists($configFile)) {
        $portalCardsConfig = json_decode(file_get_contents($configFile), true);
    }
    $userRoleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;

    $isCardVisible = function($cardKey) use ($portalCardsConfig, $userRoleId, $isTabunganAdmin, $isFinanceAdmin) {
        if ($userRoleId === 99) return true;
        if ($cardKey === 'tabungan' && $userRoleId === 2 && $isTabunganAdmin) return true;
        if ($cardKey === 'tabungan' && $userRoleId === 2 && !$isTabunganAdmin) return false;
        if ($cardKey === 'keuangan' && $userRoleId === 2 && $isFinanceAdmin) return true;
        if ($cardKey === 'keuangan' && $userRoleId === 2 && !$isFinanceAdmin) return false;
        if ($cardKey === 'pkkm' && !empty($_SESSION['is_kamad'])) return true;

        $card = $portalCardsConfig['cards'][$cardKey] ?? null;
        if (!$card) {
            return true;
        }
        if (!$card['enabled']) {
            return false;
        }
        return in_array($userRoleId, $card['roles']);
    };
    ?>
    <div class="portal-wrap fade-up">

        <!-- Ã¢â€ â‚¬Ã¢â€ â‚¬ HERO Ã¢â€ â‚¬Ã¢â€ â‚¬ -->
        <div class="portal-hero">
            <div class="portal-greeting">
                <span class="dot"></span>
                <?php echo $greet; ?>, <?php echo htmlspecialchars($userName); ?>!
            </div>
            <h1>MASMIFHDA<br><span class="grad">Aula System</span></h1>
            <p>Ekosistem digital terintegrasi untuk manajemen akademik, keuangan, dan absensi.</p>
        </div>

        <!-- Ã¢â€ â‚¬Ã¢â€ â‚¬ MODUL UTAMA Ã¢â€ â‚¬Ã¢â€ â‚¬ -->
        <div class="section-label green"><span>Modul Utama</span></div>
        <div class="modules-grid">

            <!-- SIAKAD -->
            <?php if ($isCardVisible('siakad')): 
                $siakadUrl = base_url('siakad');
                if ($isAdmin) {
                    $siakadUrl = base_url('admin/siakad/dashboard');
                }
            ?>
            <div class="mod-card theme-blue d1" onclick="location.href='<?php echo $siakadUrl; ?>'">
                <span class="mod-badge badge-live">Live</span>
                <div class="mod-icon-wrap icon-blue"><i data-lucide="graduation-cap"></i></div>
                <div class="mod-body">
                    <p class="mod-title">SIAKAD</p>
                    <p class="mod-desc">Manajemen akademik, data siswa, nilai, dan kurikulum digital.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- PIP MODULE -->
            <div class="mod-card theme-sky d2" onclick="location.href='<?php echo base_url('admin/pip/dashboard'); ?>'">
                <span class="mod-badge badge-live">Live</span>
                <div class="mod-icon-wrap icon-sky" style="background:#f0f9ff; color:#0284c7;"><i data-lucide="award"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Data PIP</p>
                    <p class="mod-desc">Pusat informasi penerima PIP dan tracking pencetakan surat.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>

            <!-- OSIM MODULE -->
            <?php if ($isAdmin): ?>
            <div class="mod-card theme-teal d3" onclick="location.href='<?php echo base_url('osim'); ?>'">
                <span class="mod-badge badge-new">New</span>
                <div class="mod-icon-wrap icon-teal"><i data-lucide="users"></i></div>
                <div class="mod-body">
                    <p class="mod-title">OSIM</p>
                    <p class="mod-desc">Aplikasi pengelolaan OSIM, pembuatan proposal, dll.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>

            <!-- PERPUSTAKAAN DIGITAL -->
            <div class="mod-card theme-purple d3" onclick="location.href='<?php echo base_url('admin/perpus'); ?>'">
                <span class="mod-badge badge-new">New</span>
                <div class="mod-icon-wrap icon-purple"><i data-lucide="book-open"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Perpustakaan</p>
                    <p class="mod-desc">Manajemen koleksi buku E-Library untuk dibaca via APK.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>
            
            <!-- KEUANGAN -->
            <?php if ($isCardVisible('keuangan')): ?>
            <div class="mod-card theme-amber d4" onclick="location.href='<?php echo $financeUrl; ?>'">
                <span class="mod-badge badge-live">Live</span>
                <div class="mod-icon-wrap icon-amber"><i data-lucide="credit-card"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Keuangan</p>
                    <p class="mod-desc">Pembayaran SPP, laporan finansial, dan monitoring tunggakan.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- BOS -->
            <?php if ($isCardVisible('bos')): ?>
            <div class="mod-card theme-rose d4" onclick="location.href='<?php echo base_url('admin/bos'); ?>'">
                <span class="mod-badge badge-live">Live</span>
                <div class="mod-icon-wrap icon-teal" style="background:#f0fdf4; color:#16a34a;"><i data-lucide="landmark"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Dana BOS</p>
                    <p class="mod-desc">Sistem pengelolaan Dana BOS, BKU, pajak, dan pelaporan.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- TABUNGAN -->
            <?php if ($isCardVisible('tabungan')): ?>
            <div class="mod-card theme-rose d5" onclick="location.href='<?php echo $tabunganUrl; ?>'">
                <span class="mod-badge badge-new">New</span>
                <div class="mod-icon-wrap icon-rose" style="background:#fff1f2; color:#e11d48;"><i data-lucide="wallet"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Tabungan Siswa</p>
                    <p class="mod-desc"><?php echo $isTabunganAdmin ? 'Manajemen simpanan siswa, tarik/setor tunai, dan riwayat mutasi.' : 'Lihat saldo tabungan digital dan riwayat transaksi menabung Anda.'; ?></p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- WAKA KURIKULUM -->
            <?php if ($isAdmin): ?>
            <div class="mod-card theme-teal d4" onclick="location.href='<?php echo base_url('admin/kurikulum'); ?>'">
                <span class="mod-badge badge-new" style="background: #e0f2fe; color: #0284c7; border-color: #7dd3fc;">Baru</span>
                <div class="mod-icon-wrap icon-sky" style="background:#f0f9ff; color:#0284c7;"><i data-lucide="book-open-check"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Waka Kurikulum</p>
                    <p class="mod-desc">Supervisi, administrasi, jurnal, dan jadwal KBM.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- BIMBINGAN KONSELING -->
            <?php if ($isAdmin): ?>
            <div class="mod-card theme-emerald d5" onclick="location.href='<?php echo base_url('bk'); ?>'">
                <span class="mod-badge badge-new" style="background: #d1fae5; color: #059669; border-color: #6ee7b7;">New</span>
                <div class="mod-icon-wrap icon-emerald" style="background:#ecfdf5; color:#059669;"><i data-lucide="shield-half"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Bimbingan Konseling</p>
                    <p class="mod-desc">E-BK & Kedisiplinan Siswa</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- PUSAT ABSENSI QR V2 -->
            <?php if ($isCardVisible('absensi')): ?>
            <div class="mod-card theme-blue d6" onclick="window.open('<?php echo base_url('admin/absen_v2'); ?>', '_self')">
                <span class="mod-badge badge-new">New</span>
                <div class="mod-icon-wrap icon-teal"><i data-lucide="scan-line"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Pusat Absensi QR</p>
                    <p class="mod-desc">Sistem scanner QR Code terpusat untuk presensi Siswa dan kehadiran Guru.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- PENGATURAN APK -->
            <?php if ($isAdmin): ?>
            <div class="mod-card theme-rose d8" onclick="location.href='<?php echo base_url('admin/apk-settings'); ?>'">
                <span class="mod-badge badge-live" style="background: #fdf4ff; color: #d946ef; border: 1px solid #f0abfc;">App</span>
                <div class="mod-icon-wrap icon-rose" style="background:#fdf4ff; color:#d946ef;"><i data-lucide="smartphone"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Pengaturan APK</p>
                    <p class="mod-desc">Layout & Tema Aplikasi Mobile</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

            <!-- PKKM -->
            <?php if ($isCardVisible('pkkm')): ?>
            <div class="mod-card theme-sky d8" onclick="location.href='<?php echo base_url('admin/pkkm'); ?>'">
                <span class="mod-badge badge-new" style="background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc;">Baru</span>
                <div class="mod-icon-wrap icon-sky" style="background:#f0f9ff; color:#0284c7;"><i data-lucide="award"></i></div>
                <div class="mod-body">
                    <p class="mod-title">PKKM</p>
                    <p class="mod-desc">Penilaian Kinerja Kepala Madrasah, instrumen penilaian, dan berkas bukti fisik.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>


            

            

            

            

            

            

            

            

                        <!-- E-Sarpras -->
            <div class="mod-card theme-purple d5" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul E-Sarpras sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-purple" style="background:#faf5ff; color:#9333ea;"><i data-lucide="boxes"></i></div>
                <div class="mod-body">
                    <p class="mod-title">E-Sarpras</p>
                    <p class="mod-desc">Manajemen aset dan inventaris madrasah.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- E-Office -->
            <div class="mod-card theme-blue d6" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul E-Office sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-blue" style="background:#eff6ff; color:#2563eb;"><i data-lucide="mail-plus"></i></div>
                <div class="mod-body">
                    <p class="mod-title">E-Office</p>
                    <p class="mod-desc">Buku agenda surat & disposisi digital.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- PPDB Online -->
            <div class="mod-card theme-teal d7" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul PPDB Online sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-teal" style="background:#f0fdf4; color:#16a34a;"><i data-lucide="users-plus"></i></div>
                <div class="mod-body">
                    <p class="mod-title">PPDB Online</p>
                    <p class="mod-desc">Penerimaan peserta didik baru terintegrasi.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- Buku Induk -->
            <div class="mod-card theme-amber d8" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul Buku Induk sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-amber" style="background:#fffbeb; color:#d97706;"><i data-lucide="book-open-check"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Buku Induk</p>
                    <p class="mod-desc">Digitalisasi data alumni & buku besar.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- Sync EMIS/RDM -->
            <div class="mod-card theme-emerald d9" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul Integrasi Kemenag sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-emerald" style="background:#ecfdf5; color:#059669;"><i data-lucide="database-zap"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Sync Data Kemenag</p>
                    <p class="mod-desc">Integrasi ekspor otomatis ke EMIS & RDM.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- Bank Soal -->
            <div class="mod-card theme-rose d10" onclick="location.href='<?php echo base_url('bank_soal'); ?>'">
                <span class="mod-badge badge-new" style="background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc;">Live</span>
                <div class="mod-icon-wrap icon-rose" style="background:#fff1f2; color:#e11d48;"><i data-lucide="database"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Bank Soal</p>
                    <p class="mod-desc">Manajemen repositori soal terpusat untuk guru.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- CBT Online -->
            <div class="mod-card theme-indigo d11" onclick="Swal.fire({title: 'Segera Hadir!', text: 'Modul CBT Online sedang dalam tahap pengembangan.', icon: 'info', confirmButtonText: 'Oke', confirmButtonColor: '#3b82f6'})">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dev</span>
                <div class="mod-icon-wrap icon-indigo" style="background:#e0e7ff; color:#4f46e5;"><i data-lucide="monitor-check"></i></div>
                <div class="mod-body">
                    <p class="mod-title">CBT Online</p>
                    <p class="mod-desc">Pelaksanaan ujian dengan sistem anti-cheat.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px; color:#cbd5e1;"></i></div>
            </div>

            <!-- Template UI -->
            <?php if ($isAdmin): ?>
            <div class="mod-card theme-slate d12" onclick="location.href='<?php echo base_url('template'); ?>'">
                <span class="mod-badge badge-new" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">Code</span>
                <div class="mod-icon-wrap" style="background:#f1f5f9; color:#475569;"><i data-lucide="code-2"></i></div>
                <div class="mod-body">
                    <p class="mod-title">Zamrud Template</p>
                    <p class="mod-desc">Kerangka dasar (boilerplate) konsisten untuk membuat halaman modul baru.</p>
                </div>
                <div class="mod-arrow"><i data-lucide="arrow-up-right" style="width:15px;height:15px;"></i></div>
            </div>
            <?php endif; ?>

        </div>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    $content = ob_get_clean();
    include __DIR__ . '/../resources/views/layout.php';

    // Script untuk auto-detect RDM link (hanya admin/user yang bisa melihat card RDM)
    if ($isCardVisible('rdm')): ?>
    <script>
    var rdmUrl = null;
    function openRdm() {
        if (rdmUrl) { window.open(rdmUrl, '_blank'); }
        else { alert('RDM belum terdeteksi. Pastikan Ngrok dan RDM sedang berjalan.'); }
    }
    (function fetchRdm() {
        fetch('<?php echo base_url('public/api/ngrok-status.php'); ?>')
            .then(r => r.json())
            .then(data => {
                var badge  = document.getElementById('rdm-portal-badge');
                var status = document.getElementById('rdm-portal-status');
                var dot    = document.getElementById('rdm-portal-dot');
                var arrow  = document.querySelector('#rdm-portal-card .mod-arrow');
                if (data.status === 'online' && data.rdm) {
                    rdmUrl = data.rdm;
                    var isLocal = (data.rdm_mode === 'local');
                    if (isLocal) {
                        // RDM nyala tapi hanya di LAN
                        if (badge)  { badge.textContent = 'LAN Only'; badge.style.cssText = 'background:#1c1917;color:#fb923c;border-color:#78350f;'; }
                        if (status) { status.innerHTML = '<i data-lucide="wifi" style="width:12px;height:12px;"></i> Aktif \u2014 akses via jaringan sekolah'; }
                        if (dot)    { dot.style.background = '#f97316'; dot.style.boxShadow = '0 0 8px #f97316'; }
                    } else {
                        // RDM online public via ngrok
                        if (badge)  { badge.textContent = 'Online'; badge.style.cssText = 'background:#064e3b;color:#7dd3fc;border-color:#0369a1;'; }
                        if (status) { status.innerHTML = '<i data-lucide="link" style="width:12px;height:12px;"></i> ' + data.rdm.replace('https://',''); }
                        if (dot)    { dot.style.background = '#22c55e'; dot.style.boxShadow = '0 0 8px #22c55e'; }
                        if (arrow)  { arrow.style.background = '#0f4c28'; arrow.style.color = '#7dd3fc'; }
                    }
                    lucide.createIcons();
                } else {
                    rdmUrl = null;
                    if (badge)  { badge.textContent = 'Offline'; badge.style.cssText = 'background:#2d1a1a;color:#f87171;border-color:#7f1d1d;'; }
                    if (status) { status.innerHTML = '<i data-lucide="wifi-off" style="width:12px;height:12px;"></i> RDM tidak aktif'; }
                    if (dot)    { dot.style.background = '#ef4444'; }
                    lucide.createIcons();
                }
            }).catch(() => {});
        setTimeout(fetchRdm, 30000);
    })();
    </script>
    <?php endif;
});

// / -> redirect ke /portal (untuk kasus akses via router, bukan file langsung)
$router->add('GET', '/', function() {
    redirect('/portal');
    exit;
});

// Public: Changelog & Updates page
$router->add('GET', '/updates', function() {
    include __DIR__ . '/../resources/views/landing/updates.php';
    exit;
});
$router->add('GET', '/changelog', function() {
    redirect('/updates');
    exit;
});

// --- ROUTES ---
$router->add('GET', '/switch-role', [\App\Controllers\AuthController::class, 'switchRole']);

$router->add('GET', '/admin/apk-settings', function() {
    include __DIR__ . '/../resources/views/apk_settings/dashboard.php';
    exit;
});
$router->add('GET', '/admin/apk-settings/aplikasi', function() {
    include __DIR__ . '/../resources/views/apk_settings/aplikasi.php';
    exit;
});
$router->add('GET', '/admin/apk-settings/fitur', function() {
    include __DIR__ . '/../resources/views/apk_settings/fitur.php';
    exit;
});
$router->add('POST', '/admin/apk-settings/fitur/save', function() {
    header('Content-Type: application/json');
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $settings_file = __DIR__ . '/../apk/apk_settings.json';
    if (file_exists($settings_file)) {
        $settings = json_decode(file_get_contents($settings_file), true);
        if (isset($data['elearning_enabled'])) {
            $settings['elearning_enabled'] = $data['elearning_enabled'] ? true : false;
            file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
});
$router->add('POST', '/admin/apk-settings/aplikasi/save-akses', function() {
    header('Content-Type: application/json');
    $guru_id = $_POST['guru_id'] ?? 0;
    $app_code = $_POST['app_code'] ?? '';
    $is_active = $_POST['is_active'] ?? 0;

    if (!$guru_id || !$app_code) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    try {
        $db = \App\Core\Database::connect('core');
        if ($is_active) {
            $stmt = $db->prepare("INSERT IGNORE INTO apk_akses_guru (guru_id, app_code) VALUES (?, ?)");
            $stmt->execute([$guru_id, $app_code]);
        } else {
            $stmt = $db->prepare("DELETE FROM apk_akses_guru WHERE guru_id = ? AND app_code = ?");
            $stmt->execute([$guru_id, $app_code]);
        }
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
});

$router->add('GET', '/dev-panel', [\App\Controllers\DeveloperController::class, 'index']);
$router->add('POST', '/dev-panel', [\App\Controllers\DeveloperController::class, 'index']);
$router->add('GET', '/dev-panel/stats', [\App\Controllers\DeveloperController::class, 'dashboardStats']);
$router->add('GET', '/dev-panel/monitor', [\App\Controllers\DeveloperController::class, 'monitor']);
$router->add('POST', '/dev-panel/monitor', [\App\Controllers\DeveloperController::class, 'monitor']);
$router->add('GET', '/dev-panel/branding', [\App\Controllers\DeveloperController::class, 'branding']);
$router->add('POST', '/dev-panel/branding/save', [\App\Controllers\DeveloperController::class, 'saveBranding']);
$router->add('GET', '/dev-panel/users', [\App\Controllers\DeveloperController::class, 'users']);
$router->add('POST', '/dev-panel/users/save', [\App\Controllers\DeveloperController::class, 'saveUser']);
$router->add('GET', '/dev-panel/delegation', [\App\Controllers\DeveloperController::class, 'appDelegation']);
$router->add('POST', '/dev-panel/delegation/save', [\App\Controllers\DeveloperController::class, 'saveDelegation']);
$router->add('GET', '/dev-panel/delegation/delete/{id}', [\App\Controllers\DeveloperController::class, 'deleteDelegation']);
$router->add('GET', '/dev-panel/access', [\App\Controllers\DeveloperController::class, 'access']);
$router->add('POST', '/dev-panel/access/save', [\App\Controllers\DeveloperController::class, 'saveAccess']);
$router->add('GET', '/dev-panel/licenses', [\App\Controllers\DeveloperController::class, 'licenses']);
$router->add('POST', '/dev-panel/licenses/save', [\App\Controllers\DeveloperController::class, 'saveLicense']);

// Dev Panel: Portal Card Manager
$router->add('GET', '/dev-panel/portal-cards',       [\App\Controllers\DeveloperController::class, 'portalCards']);
$router->add('POST', '/dev-panel/portal-cards/save',  [\App\Controllers\DeveloperController::class, 'savePortalCards']);

// Dev Panel: MOU
$router->add('GET', '/dev-panel/mou',       [\App\Controllers\DeveloperController::class, 'mou']);
$router->add('POST', '/dev-panel/mou-save', [\App\Controllers\DeveloperController::class, 'mouSave']);
$router->add('GET', '/dev-panel/mou-print', [\App\Controllers\DeveloperController::class, 'mouPrint']);


// Dev Panel: Audit Log API
$router->add('GET', '/dev-panel/audit-log',       [\App\Controllers\DeveloperController::class, 'auditLogApi']);
$router->add('POST', '/dev-panel/audit-log/clear',  [\App\Controllers\DeveloperController::class, 'auditLogClear']);
$router->add('GET', '/dev-panel/devices',       [\App\Controllers\DeveloperController::class, 'devicesApi']);
$router->add('GET', '/dev-panel/live-monitor',  [\App\Controllers\DeveloperController::class, 'liveMonitorApi']);

// Dev Panel: APK Devices Manager
$router->add('GET', '/dev-panel/apk-devices',      [\App\Controllers\DeveloperController::class, 'apkDevices']);
$router->add('GET', '/dev-panel/apk-devices/api',  [\App\Controllers\DeveloperController::class, 'apkDevicesApi']);

// Dev Panel: APK Emulator Preview
$router->add('GET', '/dev-panel/apk-preview',      [\App\Controllers\DeveloperController::class, 'apkPreview']);

// Dev Panel: Tunnel
$router->add('POST', '/dev-panel/tunnel/control',      [\App\Controllers\DeveloperController::class, 'tunnelControl']);
$router->add('POST', '/dev-panel/tunnel/auto-fetch',   [\App\Controllers\DeveloperController::class, 'tunnelAutoFetch']);
$router->add('POST', '/dev-panel/tunnel/save-custom',  [\App\Controllers\DeveloperController::class, 'tunnelSaveCustom']);

$router->add('GET', '/setup', [\App\Controllers\DeveloperController::class, 'setup']);
$router->add('POST', '/setup', [\App\Controllers\DeveloperController::class, 'setup']);
$router->add('GET', '/activate', [\App\Controllers\ActivationController::class, 'index']);
$router->add('POST', '/activate', [\App\Controllers\ActivationController::class, 'index']);

// OSIM MODULE
$router->add('GET', '/osim', [\App\Controllers\OsimController::class, 'dashboard']);
$router->add('GET', '/osim/pengaturan', [\App\Controllers\OsimController::class, 'pengaturan']);
$router->add('POST', '/osim/pengaturan/simpan', [\App\Controllers\OsimController::class, 'simpanPengaturan']);
$router->add('GET', '/osim/pengurus', [\App\Controllers\OsimController::class, 'pengurus']);
$router->add('POST', '/osim/pengurus/simpan', [\App\Controllers\OsimController::class, 'simpanPengurus']);
$router->add('GET', '/osim/pengurus/hapus', [\App\Controllers\OsimController::class, 'hapusPengurus']);
$router->add('GET', '/osim/proker', [\App\Controllers\OsimController::class, 'proker']);
$router->add('POST', '/osim/proker/simpan', [\App\Controllers\OsimController::class, 'simpanProker']);
$router->add('GET', '/osim/proker/hapus', [\App\Controllers\OsimController::class, 'hapusProker']);
$router->add('GET', '/osim/proposal', [\App\Controllers\OsimController::class, 'proposal']);
$router->add('GET', '/osim/proposal/templates', [\App\Controllers\OsimController::class, 'proposalTemplates']);
$router->add('GET', '/osim/proposal/create', [\App\Controllers\OsimController::class, 'proposalCreate']);
$router->add('POST', '/osim/proposal/simpan', [\App\Controllers\OsimController::class, 'simpanProposal']);
$router->add('GET', '/osim/proposal/hapus', [\App\Controllers\OsimController::class, 'hapusProposal']);

// OSIM E-VOTING
$router->add('GET', '/osim/evoting', [\App\Controllers\OsimController::class, 'evotingDashboard']);
$router->add('GET', '/osim/evoting/set', [\App\Controllers\OsimController::class, 'evotingSetStatus']);
$router->add('GET', '/osim/evoting/reset', [\App\Controllers\OsimController::class, 'evotingReset']);
$router->add('GET', '/osim/evoting/kandidat', [\App\Controllers\OsimController::class, 'evotingKandidat']);
$router->add('POST', '/osim/evoting/kandidat/simpan', [\App\Controllers\OsimController::class, 'simpanKandidat']);
$router->add('GET', '/osim/evoting/kandidat/hapus', [\App\Controllers\OsimController::class, 'hapusKandidat']);
$router->add('POST', '/osim/evoting/kandidat/toggle', [\App\Controllers\OsimController::class, 'toggleKandidat']);

// PERPUSTAKAAN MODULE
$router->add('GET', '/admin/perpus', [\App\Controllers\PerpusController::class, 'dashboardUtama']);
$router->add('GET', '/admin/perpus/ebook', [\App\Controllers\PerpusController::class, 'dashboardEbook']);

$router->add('GET', '/admin/perpus/kategori', [\App\Controllers\PerpusController::class, 'kategori']);
$router->add('POST', '/admin/perpus/kategori', [\App\Controllers\PerpusController::class, 'simpanKategori']);
$router->add('GET', '/admin/perpus/kategori/hapus/{id}', [\App\Controllers\PerpusController::class, 'hapusKategori']);
$router->add('GET', '/admin/perpus/tambah', [\App\Controllers\PerpusController::class, 'tambah']);
$router->add('POST', '/admin/perpus/tambah', [\App\Controllers\PerpusController::class, 'simpanBuku']);
$router->add('GET', '/admin/perpus/edit/{id}', [\App\Controllers\PerpusController::class, 'editBuku']);
$router->add('POST', '/admin/perpus/edit/{id}', [\App\Controllers\PerpusController::class, 'updateBuku']);
$router->add('GET', '/admin/perpus/hapus/{id}', [\App\Controllers\PerpusController::class, 'hapusBuku']);

// Phase 1 Master Data Perpus Fisik
$router->add('GET', '/admin/perpus/fisik', [\App\Controllers\PerpusController::class, 'bukuFisik']);
$router->add('GET', '/admin/perpus/rak', [\App\Controllers\PerpusController::class, 'rak']);
$router->add('GET', '/admin/perpus/penulis-penerbit', [\App\Controllers\PerpusController::class, 'penulisPenerbit']);

// Phase 2 Sirkulasi Perpus
$router->add('GET', '/admin/perpus/sirkulasi', [\App\Controllers\PerpusController::class, 'sirkulasi']);
$router->add('GET', '/admin/perpus/sirkulasi/pinjam', [\App\Controllers\PerpusController::class, 'sirkulasiPinjam']);

// Phase 3 Kunjungan Perpus
$router->add('GET', '/admin/perpus/kunjungan', [\App\Controllers\PerpusController::class, 'kunjungan']);
$router->add('GET', '/admin/perpus/kunjungan/scan', [\App\Controllers\PerpusController::class, 'kunjunganScan']);

// PIP MODULE
$router->add('GET', '/pip', [\App\Controllers\PipController::class, 'index']);
$router->add('GET', '/pip/komparasi', [\App\Controllers\PipController::class, 'komparasi']);
$router->add('POST', '/pip/check', [\App\Controllers\PipController::class, 'apiCheck']);
$router->add('GET', '/pip/surat', [\App\Controllers\PipController::class, 'surat']);
$router->add('GET', '/pip/rapor', [\App\Controllers\PipController::class, 'rapor']);
$router->add('POST', '/pip/confirm', [\App\Controllers\PipController::class, 'confirm']);
$router->add('GET', '/verifikasi/pip', [\App\Controllers\PipController::class, 'verifyDoc']);
$router->add('GET', '/admin/pip/dashboard', [\App\Controllers\AdminPipController::class, 'dashboard']);
$router->add('GET', '/admin/pip/konfirmasi', [\App\Controllers\AdminPipController::class, 'konfirmasi']);
$router->add('GET', '/admin/pip/cetak-pernyataan', [\App\Controllers\AdminPipController::class, 'cetakPernyataan']);
$router->add('GET', '/admin/pip/kehilangan', [\App\Controllers\AdminPipController::class, 'kehilangan']);
$router->add('GET', '/admin/pip/cetak-kehilangan', [\App\Controllers\AdminPipController::class, 'cetakKehilangan']);
$router->add('GET', '/admin/pip/kuasa', [\App\Controllers\AdminPipController::class, 'kuasa']);
$router->add('GET', '/admin/pip/cetak-kuasa', [\App\Controllers\AdminPipController::class, 'cetakKuasa']);

// TABUNGAN MODULE
$router->add('GET', '/tabungan', [\App\Controllers\PortalTabunganController::class, 'index']);
$router->add('GET', '/admin/tabungan', [\App\Controllers\AdminTabunganController::class, 'overview']);
$router->add('GET', '/admin/tabungan/kasir', [\App\Controllers\AdminTabunganController::class, 'kasir']);
$router->add('POST', '/admin/tabungan/kasir', [\App\Controllers\AdminTabunganController::class, 'kasir']);
$router->add('GET', '/admin/tabungan/siswa', [\App\Controllers\AdminTabunganController::class, 'siswa']);
$router->add('POST', '/admin/tabungan/siswa', [\App\Controllers\AdminTabunganController::class, 'siswa']);
$router->add('GET', '/admin/tabungan/mutasi', [\App\Controllers\AdminTabunganController::class, 'mutasi']);
$router->add('POST', '/admin/tabungan/mutasi/delete', [\App\Controllers\AdminTabunganController::class, 'deleteMutasi']);
$router->add('GET', '/admin/tabungan/akses', [\App\Controllers\AdminTabunganController::class, 'akses']);
$router->add('POST', '/admin/tabungan/akses', [\App\Controllers\AdminTabunganController::class, 'akses']);

// BOS MODULE
$router->add('GET', '/admin/bos', [\App\Controllers\AdminBosController::class, 'overview']);
$router->add('GET', '/admin/bos/rkas', [\App\Controllers\AdminBosController::class, 'rkas']);
$router->add('POST', '/admin/bos/rkas/save', [\App\Controllers\AdminBosController::class, 'saveRkas']);
$router->add('GET', '/admin/bos/rkas/delete/{id}', [\App\Controllers\AdminBosController::class, 'deleteRkas']);
$router->add('GET', '/admin/bos/bku', [\App\Controllers\AdminBosController::class, 'bku']);
$router->add('POST', '/admin/bos/bku/save', [\App\Controllers\AdminBosController::class, 'saveBku']);
$router->add('GET', '/admin/bos/bku/delete/{id}', [\App\Controllers\AdminBosController::class, 'deleteBku']);
$router->add('GET', '/admin/bos/pemasukan', [\App\Controllers\AdminBosController::class, 'pemasukan']);
$router->add('GET', '/admin/bos/pengeluaran', [\App\Controllers\AdminBosController::class, 'pengeluaran']);
$router->add('GET', '/admin/bos/bank', [\App\Controllers\AdminBosController::class, 'bank']);
$router->add('GET', '/admin/bos/tunai', [\App\Controllers\AdminBosController::class, 'tunai']);
$router->add('GET', '/admin/bos/pajak', [\App\Controllers\AdminBosController::class, 'pajak']);
$router->add('GET', '/admin/bos/laporan', [\App\Controllers\AdminBosController::class, 'laporan']);
$router->add('GET', '/admin/bos/akses', [\App\Controllers\AdminBosController::class, 'akses']);
$router->add('POST', '/admin/bos/akses/save', [\App\Controllers\AdminBosController::class, 'akses']);

// CBT REDIRECTS (TOTAL REPLACEMENT)
$router->add('GET', '/exam', function() {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $role     = isset($_SESSION['role_id'])  ? $_SESSION['role_id']  : 3;
    if ($role == 1 || $role == 2 || $role == 99) {
        redirect(base_url('GHCBT/index.php/gateway/manager?u=') . urlencode($username));
    } else {
        redirect(base_url('GHCBT/index.php/gateway?u=') . urlencode($username) . '&t=' . time());
    }
    exit;
});

$siakadRoutes = [
    // Lain-lain
    ['GET', '/siakad/lain-lain/rapat', [\App\Controllers\SiakadLainLainController::class, 'rapat']],
    ['POST', '/siakad/lain-lain/rapat/cetak', [\App\Controllers\SiakadLainLainController::class, 'cetakRapat']],
    ['GET', '/siakad/lain-lain/broadcast', [\App\Controllers\SiakadLainLainController::class, 'broadcast']],
    ['POST', '/siakad/lain-lain/broadcast/send', [\App\Controllers\SiakadLainLainController::class, 'sendBroadcast']],
    ['POST', '/siakad/lain-lain/broadcast/delete', [\App\Controllers\SiakadLainLainController::class, 'deleteBroadcast']],
    ['GET', '/siakad', [\App\Controllers\SiakadDashboardController::class, 'index']],
    ['GET', '/siakad/tahun-ajaran', [\App\Controllers\SiakadInstitusiController::class, 'tahunAjaran']],
    ['POST', '/siakad/tahun-ajaran/add', [\App\Controllers\SiakadInstitusiController::class, 'addTahunAjaran']],
    ['GET', '/siakad/tahun-ajaran/activate/{id}', [\App\Controllers\SiakadInstitusiController::class, 'activateTahunAjaran']],
    ['GET', '/siakad/institusi', [\App\Controllers\SiakadInstitusiController::class, 'institusi']],
    ['POST', '/siakad/institusi/update', [\App\Controllers\SiakadInstitusiController::class, 'updateInstitusi']],
    ['GET', '/siakad/kelas', [\App\Controllers\SiakadKelasController::class, 'kelas']],
    ['POST', '/siakad/kelas/add', [\App\Controllers\SiakadKelasController::class, 'addKelas']],
    ['POST', '/siakad/kelas/update', [\App\Controllers\SiakadKelasController::class, 'updateKelas']],
    ['POST', '/siakad/kelas/assign-wali', [\App\Controllers\SiakadKelasController::class, 'assignWaliKelas']],
    ['GET', '/siakad/kelas/delete/{id}', [\App\Controllers\SiakadKelasController::class, 'deleteKelas']],
    ['GET', '/siakad/mapel', [\App\Controllers\SiakadMapelController::class, 'mapel']],
    ['POST', '/siakad/mapel/add', [\App\Controllers\SiakadMapelController::class, 'addMapel']],
    ['POST', '/siakad/mapel/update', [\App\Controllers\SiakadMapelController::class, 'updateMapel']],
    ['GET', '/siakad/mapel/delete/{id}', [\App\Controllers\SiakadMapelController::class, 'deleteMapel']],
    ['GET', '/siakad/guru', [\App\Controllers\SiakadGuruController::class, 'guru']],
    ['GET', '/siakad/guru/mengajar', [\App\Controllers\SiakadGuruController::class, 'mengajar']],
    ['GET', '/siakad/guru/tugas', [\App\Controllers\SiakadGuruController::class, 'tugasTambahan']],
    ['POST', '/siakad/guru/tugas/save', [\App\Controllers\SiakadGuruController::class, 'saveTugasTambahan']],
    ['GET', '/siakad/guru/tugas/delete/{id}', [\App\Controllers\SiakadGuruController::class, 'deleteTugasTambahan']],
    ['POST', '/siakad/guru/mengajar/update', [\App\Controllers\SiakadGuruController::class, 'updateMengajar']],
    ['POST', '/siakad/guru/add', [\App\Controllers\SiakadGuruController::class, 'addGuru']],
    ['POST', '/siakad/guru/update', [\App\Controllers\SiakadGuruController::class, 'updateGuru']],
    ['POST', '/siakad/guru/update-foto', [\App\Controllers\SiakadGuruController::class, 'updateFotoGuru']],
    ['GET', '/siakad/guru/delete/{id}', [\App\Controllers\SiakadGuruController::class, 'deleteGuru']],
    ['POST', '/siakad/guru/delete-bulk', [\App\Controllers\SiakadGuruController::class, 'deleteGuruBulk']],
    ['POST', '/siakad/guru/cetak-kartu-bulk', [\App\Controllers\SiakadGuruController::class, 'cetakKartuBulk']],
    ['GET', '/siakad/guru/cetak-kartu', [\App\Controllers\SiakadGuruController::class, 'cetakKartuMassal']],
    ['GET', '/siakad/guru/export', [\App\Controllers\SiakadGuruController::class, 'exportGuru']],
    ['POST', '/siakad/guru/import', [\App\Controllers\SiakadGuruController::class, 'importGuru']],
    ['GET', '/siakad/siswa', [\App\Controllers\SiakadSiswaController::class, 'siswa']],
    ['GET', '/siakad/siswa/cetak-kartu', [\App\Controllers\SiakadSiswaController::class, 'cetakKartuMassal']],
    ['POST', '/siakad/siswa/cetak-kartu-massal', [\App\Controllers\SiakadSiswaController::class, 'cetakKartuMassal']],
    ['GET', '/siakad/siswa/naik-kelas', [\App\Controllers\SiakadSiswaController::class, 'naikKelas']],
    ['GET', '/siakad/siswa/alumni', [\App\Controllers\SiakadSiswaController::class, 'alumni']],
    ['POST', '/siakad/siswa/upload-dokumen-alumni', [\App\Controllers\SiakadSiswaController::class, 'uploadDokumenAlumni']],
    ['POST', '/siakad/siswa/proses-naik', [\App\Controllers\SiakadSiswaController::class, 'prosesNaik']],
    ['POST', '/siakad/siswa/urungkan-naik', [\App\Controllers\SiakadSiswaController::class, 'urungkanNaik']],
    ['POST', '/siakad/siswa/add', [\App\Controllers\SiakadSiswaController::class, 'addSiswa']],
    ['POST', '/siakad/siswa/import', [\App\Controllers\SiakadSiswaController::class, 'importSiswa']],
    ['POST', '/siakad/siswa/update-bulk', [\App\Controllers\SiakadSiswaController::class, 'updateSiswaBulk']],
    ['GET', '/siakad/siswa/tambah', [\App\Controllers\SiakadSiswaController::class, 'formTambahSiswa']],
    ['GET', '/siakad/guru-wali', [\App\Controllers\SiakadGuruWaliController::class, 'index']],
    ['POST', '/siakad/guru-wali/assign', [\App\Controllers\SiakadGuruWaliController::class, 'assign']],
    ['POST', '/siakad/guru-wali/unassign', [\App\Controllers\SiakadGuruWaliController::class, 'unassign']],
    ['GET', '/siakad/jurnal-global', [\App\Controllers\SiakadJurnalGlobalController::class, 'index']],
    ['GET', '/siakad/siswa/edit/{id}', [\App\Controllers\SiakadSiswaController::class, 'editSiswa']],
    ['GET', '/siakad/siswa/export', [\App\Controllers\SiakadSiswaController::class, 'exportSiswa']],
    ['POST', '/siakad/siswa/update', [\App\Controllers\SiakadSiswaController::class, 'updateSiswa']],
    ['POST', '/siakad/siswa/update-foto', [\App\Controllers\SiakadSiswaController::class, 'updateFotoSiswa']],
    ['GET', '/siakad/siswa/delete/{id}', [\App\Controllers\SiakadSiswaController::class, 'deleteSiswa']],
    ['POST', '/siakad/siswa/delete-bulk', [\App\Controllers\SiakadSiswaController::class, 'deleteSiswaBulk']],
    ['POST', '/siakad/siswa/reset-password-bulk', [\App\Controllers\SiakadSiswaController::class, 'resetPasswordBulk']],
    ['GET', '/siakad/nilai', [\App\Controllers\SiakadAkademikController::class, 'nilai']],
    ['GET', '/siakad/nilai/input', [\App\Controllers\SiakadAkademikController::class, 'inputNilai']],
    ['POST', '/siakad/nilai/save', [\App\Controllers\SiakadAkademikController::class, 'saveNilai']],
    ['GET', '/siakad/nilai/template', [\App\Controllers\SiakadAkademikController::class, 'templateNilaiFilter']],
    ['GET', '/siakad/nilai/template/cetak', [\App\Controllers\SiakadAkademikController::class, 'cetakTemplateNilai']],
    ['GET', '/siakad/raport', [\App\Controllers\SiakadAkademikController::class, 'raport']],
    ['GET', '/siakad/raport/cetak/{siswa_id}/{tahun_id}', [\App\Controllers\SiakadAkademikController::class, 'cetakRaport']],
    ['GET', '/siakad/jadwal', [\App\Controllers\AdminSiakadJadwalController::class, 'index']],
    ['GET', '/siakad/jadwal/edit/{id}', [\App\Controllers\AdminSiakadJadwalController::class, 'edit']],
    ['POST', '/siakad/jadwal/save/{id}', [\App\Controllers\AdminSiakadJadwalController::class, 'save']],
    ['POST', '/siakad/jadwal/save_inline/{id}', [\App\Controllers\AdminSiakadJadwalController::class, 'saveInline']],
    ['GET', '/siakad/jadwal/delete/{id}', [\App\Controllers\AdminSiakadJadwalController::class, 'delete']],
    ['POST', '/siakad/jadwal/import/{id}', [\App\Controllers\AdminSiakadJadwalController::class, 'import']],
    ['GET', '/siakad/ekstra', [\App\Controllers\SiakadEkstraController::class, 'index']],
    ['POST', '/siakad/ekstra/save', [\App\Controllers\SiakadEkstraController::class, 'save']],
    ['GET', '/siakad/ekstra/delete/{id}', [\App\Controllers\SiakadEkstraController::class, 'delete']],
    ['GET', '/siakad/kalender', [\App\Controllers\SiakadAkademikController::class, 'kalender']],
    ['POST', '/siakad/kalender/save', [\App\Controllers\SiakadAkademikController::class, 'saveKalender']],
    ['GET', '/siakad/kalender/delete/{id}', [\App\Controllers\SiakadAkademikController::class, 'deleteKalender']],
    ['GET', '/siakad/tugas-daring', [\App\Controllers\SiakadTugasController::class, 'index']],
    ['GET', '/siakad/tugas-daring/cetak', [\App\Controllers\SiakadTugasController::class, 'cetak']],
];

foreach ($siakadRoutes as $r) {
    $router->add($r[0], $r[1], $r[2]);
    if ($r[1] === '/siakad') {
        $router->add($r[0], '/admin/siakad/dashboard', $r[2]);
    } else {
        $router->add($r[0], str_replace('/siakad', '/admin/siakad', $r[1]), $r[2]);
    }
}
$router->add('GET', '/guru/dashboard', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    $is_logged_in = isset($_SESSION['user_id']);
    if (!$is_logged_in || $role != 2) {
        redirect('/login');
        exit;
    }
    $db = \App\Core\Database::connect('core');
    $user_id = $_SESSION['user_id'];
    $data = $db->query("SELECT nip as identitas, foto FROM guru WHERE user_id = $user_id")->fetch();
    $identitas = isset($data['identitas']) ? $data['identitas'] : '';
    $foto = isset($data['foto']) ? $data['foto'] : '';
    $fotoPath = "/uploads/guru/";
    include __DIR__ . '/../resources/views/portal/multi_dashboard.php';
});

$router->add('GET', '/guru', function() {
    redirect('/guru/dashboard');
    exit;
});

$router->add('GET', '/siswa/dashboard', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    $is_logged_in = isset($_SESSION['user_id']);
    if (!$is_logged_in || $role != 3) {
        redirect('/login');
        exit;
    }
    $db = \App\Core\Database::connect('core');
    $user_id = $_SESSION['user_id'];
    $data = $db->query("SELECT nis as identitas, foto FROM siswa WHERE user_id = $user_id")->fetch();
    $identitas = isset($data['identitas']) ? $data['identitas'] : '';
    $foto = isset($data['foto']) ? $data['foto'] : '';
    $fotoPath = "/uploads/siswa/";
    include __DIR__ . '/../resources/views/portal/multi_dashboard.php';
});

$router->add('GET', '/siswa', function() {
    redirect('/siswa/dashboard');
    exit;
});

$router->add('GET', '/alumni', function() {
    redirect('/alumni/dashboard');
    exit;
});

$router->add('GET', '/profil-guru', [\App\Controllers\ProfilGuruController::class, 'index']);
$router->add('POST', '/profil-guru/update', [\App\Controllers\ProfilGuruController::class, 'update']);
$router->add('GET', '/profil-siswa', [\App\Controllers\ProfilSiswaController::class, 'index']);
$router->add('POST', '/profil-siswa/update', [\App\Controllers\ProfilSiswaController::class, 'update']);

// ==========================================
// 6. PORTAL GURU ROUTES
// ==========================================
$router->add('GET', '/guru/profil-guru', [\App\Controllers\ProfilGuruController::class, 'index']);
$router->add('POST', '/guru/profil-guru/update', [\App\Controllers\ProfilGuruController::class, 'update']);

// Jurnal Mengajar & Absensi Pribadi
$router->add('GET', '/guru/jurnal', [\App\Controllers\PortalGuruPribadiController::class, 'jurnal']);
$router->add('POST', '/guru/jurnal/save', [\App\Controllers\PortalGuruPribadiController::class, 'saveJurnal']);
$router->add('GET', '/guru/jurnal/delete/{id}', [\App\Controllers\PortalGuruPribadiController::class, 'deleteJurnal']);
$router->add('GET', '/guru/perangkat', [\App\Controllers\PortalGuruPribadiController::class, 'perangkat']);
$router->add('GET', '/guru/perangkat/detail', [\App\Controllers\PortalGuruPribadiController::class, 'perangkatDetail']);
$router->add('POST', '/guru/perangkat/upload', [\App\Controllers\PortalGuruPribadiController::class, 'uploadPerangkat']);
$router->add('GET', '/guru/perangkat/delete/{id}', [\App\Controllers\PortalGuruPribadiController::class, 'deletePerangkat']);
$router->add('POST', '/guru/perangkat/copy', [\App\Controllers\PortalGuruPribadiController::class, 'copyPerangkat']);
$router->add('GET', '/guru/absen', [\App\Controllers\PortalGuruPribadiController::class, 'absen']);
$router->add('GET', '/api/guru/siswa-kelas', [\App\Controllers\PortalGuruPribadiController::class, 'getSiswaKelas']);

// Tugas Tambahan - Pemberkasan

// Nilai Harian
$router->add('GET', '/guru/nilai_harian', [\App\Controllers\PortalGuruNilaiHarianController::class, 'index']);
$router->add('POST', '/guru/nilai_harian', [\App\Controllers\PortalGuruNilaiHarianController::class, 'index']); // For form submissions or filters
$router->add('POST', '/guru/nilai_harian/save', [\App\Controllers\PortalGuruNilaiHarianController::class, 'save']);
$router->add('GET', '/guru/nilai_harian/delete/{id}', [\App\Controllers\PortalGuruNilaiHarianController::class, 'delete']);
$router->add('GET', '/guru/nilai_harian/detail/{id}', [\App\Controllers\PortalGuruNilaiHarianController::class, 'detail']);

// Tugas Tambahan - Pembina Ekstra
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra', [\App\Controllers\PortalGuruEkstraController::class, 'index']);
$router->add('GET', '/siswa/profil-siswa', [\App\Controllers\ProfilSiswaController::class, 'index']);
$router->add('POST', '/siswa/profil-siswa/update', [\App\Controllers\ProfilSiswaController::class, 'update']);


// Guru GHCBT Routes

// Guru Berkas Ujian Routes

// Tugas Tambahan Guru
$router->add('GET', '/guru/tugas_tambahan/wali_kelas', [\App\Controllers\PortalGuruTugasController::class, 'waliKelas']);
$router->add('GET', '/guru/tugas_tambahan/wali_kelas/detail', [\App\Controllers\PortalGuruTugasController::class, 'detailWaliKelas']);
$router->add('GET', '/guru/tugas_tambahan/wali_kelas/absensi', [\App\Controllers\PortalGuruTugasController::class, 'absensiWaliKelas']);
$router->add('POST', '/guru/tugas_tambahan/wali_kelas/absensi', [\App\Controllers\PortalGuruTugasController::class, 'absensiWaliKelas']);

// Pembina Ekstrakurikuler
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra', [\App\Controllers\PortalGuruTugasController::class, 'pembinaEkstra']);

// Waka Kurikulum
$router->add('GET', '/guru/tugas_tambahan/waka_kurikulum', [\App\Controllers\PortalGuruTugasController::class, 'wakaKurikulum']);
$router->add('GET', '/guru/tugas_tambahan/waka_kurikulum/cetak', [\App\Controllers\PortalGuruTugasController::class, 'cetakProgramWakaKurikulum']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/pengaturan', [\App\Controllers\PortalGuruEkstraController::class, 'pengaturan']);
$router->add('POST', '/guru/tugas_tambahan/pembina_ekstra/pengaturan/save', [\App\Controllers\PortalGuruEkstraController::class, 'savePengaturan']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/cetak', [\App\Controllers\PortalGuruEkstraController::class, 'cetakProgram']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/anggota', [\App\Controllers\PortalGuruEkstraController::class, 'anggota']);
$router->add('POST', '/guru/tugas_tambahan/pembina_ekstra/anggota/add', [\App\Controllers\PortalGuruEkstraController::class, 'addAnggota']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/anggota/delete/{id}', [\App\Controllers\PortalGuruEkstraController::class, 'deleteAnggota']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/jurnal', [\App\Controllers\PortalGuruEkstraController::class, 'jurnal']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/jurnal/form', [\App\Controllers\PortalGuruEkstraController::class, 'formJurnal']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/jurnal/form/{id}', [\App\Controllers\PortalGuruEkstraController::class, 'formJurnal']);
$router->add('POST', '/guru/tugas_tambahan/pembina_ekstra/jurnal/save', [\App\Controllers\PortalGuruEkstraController::class, 'saveJurnal']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/jurnal/delete/{id}', [\App\Controllers\PortalGuruEkstraController::class, 'deleteJurnal']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/absen', [\App\Controllers\PortalGuruEkstraController::class, 'absen']);
$router->add('POST', '/guru/tugas_tambahan/pembina_ekstra/absen/save', [\App\Controllers\PortalGuruEkstraController::class, 'saveAbsen']);
$router->add('GET', '/guru/tugas_tambahan/pembina_ekstra/nilai', [\App\Controllers\PortalGuruEkstraController::class, 'nilai']);
$router->add('POST', '/guru/tugas_tambahan/pembina_ekstra/nilai/save', [\App\Controllers\PortalGuruEkstraController::class, 'saveNilai']);

// --- Absen QR V2 Module ---
$router->add('GET', '/admin/absen_v2', [\App\Controllers\AbsenV2Controller::class, 'dashboard']);
$router->add('GET', '/absen_v2/scanner', [\App\Controllers\AbsenV2Controller::class, 'scanner']);
$router->add('POST', '/api/absen_v2/scan', [\App\Controllers\AbsenV2Controller::class, 'processScan']);
$router->add('GET', '/admin/absen_v2/akses-scanner', [\App\Controllers\AbsenV2Controller::class, 'aksesScanner']);
$router->add('POST', '/admin/absen_v2/akses-scanner/save', [\App\Controllers\AbsenV2Controller::class, 'saveAksesScanner']);
$router->add('GET', '/absen_v2/scanner_sholat', [\App\Controllers\AbsenV2Controller::class, 'scannerSholat']);
$router->add('POST', '/api/absen_v2/scan_sholat', [\App\Controllers\AbsenV2Controller::class, 'processScanSholat']);

$router->add('GET', '/admin/absen_v2/rekap-harian', [\App\Controllers\AbsenV2Controller::class, 'rekapHarian']);
$router->add('GET', '/admin/absen_v2/rekap-detail', [\App\Controllers\AbsenV2Controller::class, 'rekapDetail']);
$router->add('GET', '/admin/absen_v2/rekap-bulanan', [\App\Controllers\AbsenV2Controller::class, 'rekapBulanan']);
$router->add('GET', '/admin/absen_v2/ketidakhadiran', [\App\Controllers\AbsenV2Controller::class, 'ketidakhadiran']);
$router->add('GET', '/admin/absen_v2/pengaturan', [\App\Controllers\AbsenV2Controller::class, 'pengaturan']);
$router->add('POST', '/admin/absen_v2/pengaturan/save', [\App\Controllers\AbsenV2Controller::class, 'savePengaturan']);

// --- Absen Guru V2 Module ---
$router->add('GET', '/admin/absen_guru_v2', [\App\Controllers\AbsenGuruV2Controller::class, 'dashboard']);
$router->add('GET', '/absen_guru_v2/scanner', [\App\Controllers\AbsenGuruV2Controller::class, 'scanner']);
$router->add('POST', '/api/absen_guru_v2/scan', [\App\Controllers\AbsenGuruV2Controller::class, 'processScan']);

$router->add('GET', '/admin/absen_guru_v2/rekap-harian', [\App\Controllers\AbsenGuruV2Controller::class, 'rekapHarian']);
$router->add('GET', '/admin/absen_guru_v2/rekap-bulanan', [\App\Controllers\AbsenGuruV2Controller::class, 'rekapBulanan']);
$router->add('GET', '/admin/absen_guru_v2/ketidakhadiran', [\App\Controllers\AbsenGuruV2Controller::class, 'ketidakhadiran']);
$router->add('GET', '/admin/absen_guru_v2/input-manual', [\App\Controllers\AbsenGuruV2Controller::class, 'inputManual']);
$router->add('POST', '/admin/absen_guru_v2/save-input-manual', [\App\Controllers\AbsenGuruV2Controller::class, 'saveInputManual']);
$router->add('GET', '/admin/absen_guru_v2/pengaturan', [\App\Controllers\AbsenGuruV2Controller::class, 'pengaturan']);
$router->add('POST', '/admin/absen_guru_v2/pengaturan/save', [\App\Controllers\AbsenGuruV2Controller::class, 'savePengaturan']);

// API: data institusi untuk kop surat print
$router->add('GET', '/api/institusi-info', function() {
    $db = \App\Core\Database::connect('core');
    $inst = $db->query("SELECT nama, logo, alamat, desa, kecamatan, kota, provinsi, kodepos, telepon, email FROM institusi LIMIT 1")->fetch();
    header('Content-Type: application/json');
    echo json_encode($inst ?: (object)[]);
});


$router->add('GET', '/portal/export-absen', function() {
    include __DIR__ . '/../resources/views/portal/export_absen_print.php';
});
$router->add('GET', '/absen-saya', function() {
    $db = \App\Core\Database::connect('core');
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role_id'];
    $foto = '';
    if ($role == 2) {
        $data = $db->query("SELECT nip as identitas, foto FROM guru WHERE user_id = $user_id")->fetch();
        $identitas = isset($data['identitas']) ? $data['identitas'] : '';
        $foto = isset($data['foto']) ? $data['foto'] : '';
    } elseif ($role == 3) {
        $data = $db->query("SELECT nis as identitas, foto FROM siswa WHERE user_id = $user_id")->fetch();
        $identitas = isset($data['identitas']) ? $data['identitas'] : '';
        $foto = isset($data['foto']) ? $data['foto'] : '';
    }
    include __DIR__ . '/../resources/views/portal/absen_saya.php';
});
$router->add('GET', '/keuangan', [\App\Controllers\PortalKeuanganController::class, 'index']);
$router->add('GET', '/admin/finance', [\App\Controllers\AdminFinanceController::class, 'overview']);
$router->add('GET', '/admin/finance/tagihan', [\App\Controllers\AdminFinanceController::class, 'tagihan']);
$router->add('GET', '/admin/finance/gaji', [\App\Controllers\AdminFinanceController::class, 'gaji']);
$router->add('GET', '/admin/finance/laporan', [\App\Controllers\AdminFinanceController::class, 'laporan']);
$router->add('GET', '/admin/finance/biaya', [\App\Controllers\AdminFinanceController::class, 'biaya']);
$router->add('GET', '/admin/finance/akses', [\App\Controllers\AdminFinanceController::class, 'aksesBendahara']);
$router->add('POST', '/admin/finance/akses/save', [\App\Controllers\AdminFinanceController::class, 'saveAksesBendahara']);
$router->add('GET', '/admin/finance/akses/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteAksesBendahara']);
$router->add('POST', '/admin/finance/akses/toggle-delete', [\App\Controllers\AdminFinanceController::class, 'toggleDeleteAkses']);

// Inventaris Routes

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin BOS (Dana BOS Ã¢â‚¬â€ Modul Terpisah dari Keuangan) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Finance KOMITE Routes
$router->add('GET', '/admin/finance/komite/pemasukan', [\App\Controllers\AdminFinanceController::class, 'komitePemasukan']);
$router->add('GET', '/admin/finance/komite/pengeluaran', [\App\Controllers\AdminFinanceController::class, 'komitePengeluaran']);
$router->add('GET', '/admin/finance/komite/kategori', [\App\Controllers\AdminFinanceController::class, 'komiteKategori']);
$router->add('GET', '/admin/finance/komite/jenis-tagihan', [\App\Controllers\AdminFinanceController::class, 'komiteJenisTagihan']);
$router->add('GET', '/admin/finance/komite/pembayaran-siswa', [\App\Controllers\AdminFinanceController::class, 'komitePembayaranSiswa']);
$router->add('GET', '/admin/finance/komite/laporan', [\App\Controllers\AdminFinanceController::class, 'komiteLaporan']);
$router->add('GET', '/admin/finance/komite/laporan/cetak', [\App\Controllers\AdminFinanceController::class, 'cetakLaporan']);

// Tabungan Siswa Routes

// Finance POST Routes
$router->add('POST', '/admin/finance/biaya/save', [\App\Controllers\AdminFinanceController::class, 'saveBiaya']);
$router->add('GET', '/admin/finance/biaya/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteBiaya']);
$router->add('GET', '/admin/finance/kwitansi/{id}', [\App\Controllers\AdminFinanceController::class, 'cetakKwitansi']);
$router->add('GET', '/kwitansi/verifikasi/{id}', [\App\Controllers\AdminFinanceController::class, 'verifikasiKwitansiPublic']);
$router->add('GET', '/admin/finance/cetak-tagihan/{id}', [\App\Controllers\AdminFinanceController::class, 'cetakSuratTagihan']);
$router->add('GET', '/admin/finance/cetak-tagihan-siswa/{siswa_id}', [\App\Controllers\AdminFinanceController::class, 'cetakSuratTagihanSiswa']);
$router->add('GET', '/verify-invoice', [\App\Controllers\AdminFinanceController::class, 'verifikasiTagihanPublic']);
$router->add('POST', '/admin/finance/tagihan/bayar', [\App\Controllers\AdminFinanceController::class, 'bayarTagihan']);
$router->add('POST', '/admin/finance/tagihan/update', [\App\Controllers\AdminFinanceController::class, 'updateTagihan']);
$router->add('POST', '/admin/finance/gaji/bayar', [\App\Controllers\AdminFinanceController::class, 'bayarGaji']);
$router->add('POST', '/admin/finance/komite/kategori/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteKategori']);
$router->add('GET', '/admin/finance/komite/kategori/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteKategori']);
$router->add('POST', '/admin/finance/komite/jenis-tagihan/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteJenisTagihan']);
$router->add('GET', '/admin/finance/komite/jenis-tagihan/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteJenisTagihan']);
$router->add('POST', '/admin/finance/komite/pembayaran-siswa/save', [\App\Controllers\AdminFinanceController::class, 'saveKomitePembayaran']);
$router->add('POST', '/admin/finance/komite/pembayaran-siswa/delete', [\App\Controllers\AdminFinanceController::class, 'deleteKomitePembayaran']);
$router->add('POST', '/admin/finance/komite/transaksi/save', [\App\Controllers\AdminFinanceController::class, 'saveKomiteTransaksi']);
$router->add('GET', '/admin/finance/komite/transaksi/delete/{id}', [\App\Controllers\AdminFinanceController::class, 'deleteKomiteTransaksi']);

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin Persuratan Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ SK Kamad (Generator Surat Keputusan) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Surat Dinas Umum (Tugas, Keterangan, dll) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Admin Pemberkasan Template

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin Inventaris Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin Perpustakaan (V2) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Action Routes

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin E-Learning Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Student E-Learning Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Rekap Soal GHCBT Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin Ujian Madrasah Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Admin Piagam Digital Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Ã°Å¸Â¤â€“ Admin AI Generator Soal Ã°Å¸Â¤â€“

// Bank Soal & Paket Ujian (Phase 2)



$router->add('GET', '/dev-panel/tunnels',                 [\App\Controllers\DeveloperController::class, 'tunnels']);
$router->add('POST', '/dev-panel/tunnels/control',         [\App\Controllers\DeveloperController::class, 'tunnelControl']);
$router->add('POST', '/dev-panel/tunnels/save',            [\App\Controllers\DeveloperController::class, 'tunnelSave']);
$router->add('POST', '/dev-panel/tunnels/delete',          [\App\Controllers\DeveloperController::class, 'tunnelDelete']);
$router->add('POST', '/dev-panel/tunnels/toggle-autostart',[\App\Controllers\DeveloperController::class, 'tunnelToggleAutoStart']);
$router->add('POST', '/dev-panel/tunnels/setup-autostart', [\App\Controllers\DeveloperController::class, 'tunnelSetupAutoStart']);

$router->add('GET', '/login', [\App\Controllers\AuthController::class, 'login']);
$router->add('POST', '/login', [\App\Controllers\AuthController::class, 'login']);
$router->add('POST', '/login/qr', [\App\Controllers\AuthController::class, 'loginQR']);
$router->add('GET', '/logout', [\App\Controllers\AuthController::class, 'logout']);

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Piagam Digital Module Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
// Redirect semua /piagam/* langsung ke file PHP di folder piagam/
$router->add('GET', '/piagam', function() {
    redirect('/piagam/index.php');
    exit;
});
$router->add('GET', '/piagam/{path}', function() {
    redirect('/piagam/index.php');
    exit;
});

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ SPMB (Penerimaan Siswa Baru) Module Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// Admin SPMB

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Rute Program Wali Kelas Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
$router->add('GET', '/siakad/guru/wali-kelas/pengaturan', [\App\Controllers\SiakadWaliKelasController::class, 'pengaturan']);
$router->add('POST', '/siakad/guru/wali-kelas/pengaturan/save', [\App\Controllers\SiakadWaliKelasController::class, 'savePengaturan']);
$router->add('GET', '/siakad/guru/wali-kelas/program/cetak', [\App\Controllers\SiakadWaliKelasController::class, 'cetakProgram']);

// Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Pengaturan Template SK Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬

// --- Pengaturan Template SK -------------------------------------------------

// --- Kepala Madrasah (Kamad) Routes -----------------------------------------
$router->add('GET', '/guru/kamad/dashboard', [\App\Controllers\PortalGuruKamadController::class, 'dashboard']);
$router->add('GET', '/guru/kamad/program', [\App\Controllers\PortalGuruKamadController::class, 'program']);
$router->add('GET', '/guru/kamad/program/cetak', [\App\Controllers\PortalGuruKamadController::class, 'cetakProgram']);
$router->add('GET', '/guru/kamad/monitoring/jurnal', [\App\Controllers\PortalGuruKamadController::class, 'monitoringJurnal']);
$router->add('GET', '/guru/kamad/monitoring/nilai', [\App\Controllers\PortalGuruKamadController::class, 'monitoringNilai']);




// --- Bimbingan Konseling ----------------------------------------------------
$router->add('GET', '/bk', [\App\Controllers\BkController::class, 'index']);
$router->add('GET', '/bk/kategori', [\App\Controllers\BkController::class, 'kategori']);
$router->add('POST', '/bk/kategori/save', [\App\Controllers\BkController::class, 'saveKategori']);
$router->add('GET', '/bk/kategori/delete/{id}', [\App\Controllers\BkController::class, 'deleteKategori']);
$router->add('GET', '/bk/pelanggaran', [\App\Controllers\BkController::class, 'pelanggaran']);
$router->add('POST', '/bk/pelanggaran/save', [\App\Controllers\BkController::class, 'savePelanggaran']);
$router->add('GET', '/bk/pelanggaran/delete/{id}', [\App\Controllers\BkController::class, 'deletePelanggaran']);


$router->add('GET', '/admin/kurikulum', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    include __DIR__ . '/../resources/views/admin/kurikulum_dashboard.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/dalam-pengembangan', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    include __DIR__ . '/../resources/views/admin/dalam_pengembangan.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/monitoring-jadwal', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    include __DIR__ . '/../resources/views/admin/kurikulum_monitoring_jadwal.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/monitoring-jurnal', [\App\Controllers\KurikulumMonitoringJurnalController::class, 'index']);
$router->add('GET', '/admin/kurikulum/pengaturan-kkm', [\App\Controllers\KurikulumMonitoringKKMController::class, 'index']);
$router->add('POST', '/admin/kurikulum/pengaturan-kkm/save', [\App\Controllers\KurikulumMonitoringKKMController::class, 'save']);
$router->add('GET', '/admin/kurikulum/api/guru-mengajar', function() {
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    $db_core = \App\Core\Database::connect('core');
    $db_siakad = \App\Core\Database::connect('siakad');
    
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

    $stmt_kelas = $db_siakad->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM jadwal_pelajaran j JOIN kelas k ON j.kelas_id = k.id WHERE j.guru_id = ? AND j.tahun_ajaran_id = ? ORDER BY k.tingkat ASC, k.nama_kelas ASC");
    $stmt_kelas->execute([$guru_id, $tahun_ajaran_id]);
    $kelas = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

    $stmt_mapel = $db_siakad->prepare("SELECT DISTINCT m.id, m.nama_mapel FROM jadwal_pelajaran j JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id WHERE j.guru_id = ? AND j.tahun_ajaran_id = ? ORDER BY m.nama_mapel ASC");
    $stmt_mapel->execute([$guru_id, $tahun_ajaran_id]);
    $mapel = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);
    if(empty($mapel)) { // fallback if column is mapel_id
        $stmt_mapel = $db_siakad->prepare("SELECT DISTINCT m.id, m.nama_mapel FROM jadwal_pelajaran j JOIN mapel m ON j.mapel_id = m.id WHERE j.guru_id = ? AND j.tahun_ajaran_id = ? ORDER BY m.nama_mapel ASC");
        $stmt_mapel->execute([$guru_id, $tahun_ajaran_id]);
        $mapel = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);
    }

    header('Content-Type: application/json');
    echo json_encode(['kelas' => $kelas, 'mapel' => $mapel]);
    exit;
});

$router->add('GET', '/admin/kurikulum/api/jenis-evaluasi', function() {
    $kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
    $mapel_id = isset($_GET['mapel_id']) ? (int)$_GET['mapel_id'] : 0;
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

    $evaluasi = ['PTS', 'PAS'];
    $uh_list = [];

    if ($kelas_id && $mapel_id) {
        $stmt = $db_core->prepare("SELECT DISTINCT jenis_evaluasi FROM nilai_harian WHERE tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ?");
        $stmt->execute([$tahun_ajaran_id, $kelas_id, $mapel_id]);
        $raw = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        foreach ($raw as $val) {
            if (stripos($val, 'UH') !== false || stripos($val, 'PH') !== false) {
                $uh_list[] = $val;
            }
        }
    }
    
    sort($uh_list);
    $evaluasi = array_merge($uh_list, $evaluasi);
    
    header('Content-Type: application/json');
    echo json_encode(['evaluasi' => $evaluasi]);
    exit;
});

$router->add('GET', '/admin/kurikulum/rekap-ketuntasan', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $db_siakad = \App\Core\Database::connect('siakad');
    
    // Fetch Guru
    $stmtGuru = $db_core->query("SELECT id, nama FROM guru ORDER BY nama ASC");
    $guru_list = $stmtGuru->fetchAll(\PDO::FETCH_ASSOC);
    
    // Fetch Kelas
    $stmtKelas = $db_siakad->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
    $kelas_list = $stmtKelas->fetchAll(\PDO::FETCH_ASSOC);
    
    // Fetch Mapel
    $stmtMapel = $db_siakad->query("SELECT id, nama_mapel, kkm_kelas_10, kkm_kelas_11, kkm_kelas_12 FROM mata_pelajaran ORDER BY nama_mapel ASC");
    $mapel_list = $stmtMapel->fetchAll(\PDO::FETCH_ASSOC);

    // Filters
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    $kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
    $mapel_id = isset($_GET['mapel_id']) ? (int)$_GET['mapel_id'] : 0;
    $jenis_evaluasi = isset($_GET['jenis_evaluasi']) ? $_GET['jenis_evaluasi'] : '';

    // Check if all filters are set to show data
    $is_filtered = ($guru_id > 0 && $kelas_id > 0 && $mapel_id > 0 && $jenis_evaluasi !== '');

    $nilai_data = [];
    $kkm_nilai = null;
    $summary = ['total' => 0, 'tuntas' => 0, 'remedial' => 0];

    if ($is_filtered) {
        // Ambil tahun ajaran aktif dari institusi
        $stmt_inst = $db_core->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
        $institusi = $stmt_inst->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
        $semester = $institusi['semester'] == 'Genap' ? 2 : 1;

        // Cari KKM untuk mapel ini di kelas ini
        $tingkat = 10;
        foreach ($kelas_list as $k) {
            if ($k['id'] == $kelas_id) {
                // Konversi tingkat (misal "X", "XI", "XII" atau "10", "11", "12")
                $t = trim($k['tingkat']);
                if ($t === 'XI' || $t == '11') $tingkat = 11;
                else if ($t === 'XII' || $t == '12') $tingkat = 12;
                break;
            }
        }
        
        foreach ($mapel_list as $m) {
            if ($m['id'] == $mapel_id) {
                if ($tingkat == 10) $kkm_nilai = $m['kkm_kelas_10'];
                else if ($tingkat == 11) $kkm_nilai = $m['kkm_kelas_11'];
                else if ($tingkat == 12) $kkm_nilai = $m['kkm_kelas_12'];
                break;
            }
        }

        // Ambil daftar siswa aktif di kelas ini
        $stmt_siswa = $db_siakad->prepare("SELECT id, nis, nama_lengkap as nama FROM siswa WHERE kelas_id = ? AND status = 'aktif' ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswa_list = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil header nilai harian
        $stmt_nh = $db_core->prepare("SELECT id FROM nilai_harian WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
        $stmt_nh->execute([$guru_id, $kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);
        $nh = $stmt_nh->fetch(\PDO::FETCH_ASSOC);

        $nilai_map = [];
        if ($nh) {
            // Ambil detail nilai
            $stmt_dtl = $db_core->prepare("SELECT siswa_id, nilai FROM nilai_harian_detail WHERE nilai_harian_id = ?");
            $stmt_dtl->execute([$nh['id']]);
            $raw_nilai = $stmt_dtl->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($raw_nilai as $rn) {
                $nilai_map[$rn['siswa_id']] = $rn['nilai'];
            }
        }

        foreach ($siswa_list as $s) {
            $nilai_siswa = isset($nilai_map[$s['id']]) ? floatval($nilai_map[$s['id']]) : null;
            $is_tuntas = false;
            
            if ($nilai_siswa !== null) {
                $summary['total']++;
                if ($kkm_nilai !== null && $kkm_nilai > 0) {
                    if ($nilai_siswa >= $kkm_nilai) {
                        $is_tuntas = true;
                        $summary['tuntas']++;
                    } else {
                        $summary['remedial']++;
                    }
                }
            }

            $nilai_data[] = [
                'nis' => $s['nis'],
                'nama' => $s['nama'],
                'nilai' => $nilai_siswa,
                'is_tuntas' => ($kkm_nilai !== null && $kkm_nilai > 0) ? $is_tuntas : null
            ];
        }
    }

    $page_title = "Rekap Ketuntasan Nilai";
    $active_page = 'rekap_ketuntasan';
    
    include __DIR__ . '/../resources/views/admin/kurikulum_rekap_ketuntasan.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-administrasi', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_list = [];
    if ($tahun_ajaran_id) {
        $stmt = $db_core->prepare("
            SELECT g.id, g.nama, g.nip,
                   s.id as supervisi_id, s.tanggal_supervisi, s.nilai_akhir, s.penilai_nama
            FROM guru g
            LEFT JOIN kurikulum_supervisi_administrasi s 
              ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
            ORDER BY g.nama ASC
        ");
        $stmt->execute([$tahun_ajaran_id]);
        $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    $page_title = "Supervisi Administrasi";
    $active_page = 'supervisi_administrasi';
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_administrasi.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-administrasi/evaluasi', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    if (!$guru_id) {
        redirect('/admin/kurikulum/supervisi-administrasi');
        exit;
    }

    $stmtGuru = $db_core->prepare("SELECT * FROM guru WHERE id = ?");
    $stmtGuru->execute([$guru_id]);
    $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

    if (!$guru) {
        redirect('/admin/kurikulum/supervisi-administrasi');
        exit;
    }

    $supervisi = null;
    if ($tahun_ajaran_id) {
        $stmtSup = $db_core->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
        if ($supervisi) {
            $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
        }
    }

    $page_title = "Form Supervisi Administrasi";
    $active_page = 'supervisi_administrasi';
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_administrasi_evaluasi.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-administrasi/cetak', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    if (!$guru_id) {
        die('Guru ID tidak valid');
    }
    
    $db_core = \App\Core\Database::connect('core');
    
    $stmt_kop = $db_core->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
    $kop = $stmt_kop->fetch(\PDO::FETCH_ASSOC);

    $stmt_inst = $db_core->query("SELECT * FROM institusi LIMIT 1");
    $institusi = $stmt_inst->fetch(\PDO::FETCH_ASSOC);
    $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
    
    // Ambil nama tahun ajaran
    $stmt_ta = $db_core->prepare("SELECT name, semester FROM tahun_ajaran WHERE id = ?");
    $stmt_ta->execute([$tahun_ajaran_id]);
    if ($ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC)) {
        $smt = ($ta['semester'] == 1) ? 'Ganjil' : 'Genap';
        $institusi['tahun_ajaran_nama'] = $ta['name'] . ' (' . $smt . ')';
    }
    
    $stmtGuru = $db_core->prepare("SELECT * FROM guru WHERE id = ?");
    $stmtGuru->execute([$guru_id]);
    $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);
    
    if (!$guru) {
        die('Guru tidak ditemukan');
    }
    
    $stmtSup = $db_core->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
    $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
    $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
    
    if ($supervisi) {
        $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
    }
    
    $page_title = "Cetak Supervisi Administrasi - " . htmlspecialchars($guru['nama']);
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_administrasi_cetak.php';
    exit;
});

$router->add('POST', '/admin/kurikulum/supervisi-administrasi/save', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_id = $_POST['guru_id'] ?? null;
    $tanggal_supervisi = $_POST['tanggal_supervisi'] ?? date('Y-m-d');
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    
    $instrumen = [
        'cp' => $_POST['instrumen']['cp'] ?? '0',
        'atp' => $_POST['instrumen']['atp'] ?? '0',
        'modul' => $_POST['instrumen']['modul'] ?? '0',
        'kktp' => $_POST['instrumen']['kktp'] ?? '0',
        'prota' => $_POST['instrumen']['prota'] ?? '0',
        'promes' => $_POST['instrumen']['promes'] ?? '0',
        'absen' => $_POST['instrumen']['absen'] ?? '0',
        'jurnal' => $_POST['instrumen']['jurnal'] ?? '0',
        'daftar_nilai' => $_POST['instrumen']['daftar_nilai'] ?? '0'
    ];
    
    $total_items = 9;
    $score = 0;
    foreach ($instrumen as $val) {
        $score += (int)$val;
    }
    $nilai_akhir = round(($score / ($total_items * 4)) * 100);
    $instrumen_json = json_encode($instrumen);

    $penilai_nama = $_SESSION['nama'] ?? 'Admin';

    try {
        $stmt = $db_core->prepare("
            INSERT INTO kurikulum_supervisi_administrasi 
            (tahun_ajaran_id, guru_id, tanggal_supervisi, instrumen_json, nilai_akhir, catatan, tindak_lanjut, penilai_nama) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            tanggal_supervisi = VALUES(tanggal_supervisi),
            instrumen_json = VALUES(instrumen_json),
            nilai_akhir = VALUES(nilai_akhir),
            catatan = VALUES(catatan),
            tindak_lanjut = VALUES(tindak_lanjut),
            penilai_nama = VALUES(penilai_nama)
        ");
        $stmt->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $instrumen_json, $nilai_akhir, $catatan, $tindak_lanjut, $penilai_nama]);
        
        echo json_encode(['success' => true, 'message' => 'Data supervisi berhasil disimpan']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
    }
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-kelas', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_list = [];
    if ($tahun_ajaran_id) {
        $stmt = $db_core->prepare("
            SELECT g.id, g.nama, g.nip,
                   s.id as supervisi_id, s.tanggal_supervisi, s.penilai_nama
            FROM guru g
            LEFT JOIN kurikulum_supervisi_kelas s 
              ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
            ORDER BY g.nama ASC
        ");
        $stmt->execute([$tahun_ajaran_id]);
        $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    $page_title = "Supervisi Kelas";
    $active_page = 'supervisi_kelas';
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_kelas.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-kelas/evaluasi', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    if (!$guru_id) {
        redirect('/admin/kurikulum/supervisi-kelas');
        exit;
    }

    $stmtGuru = $db_core->prepare("SELECT * FROM guru WHERE id = ?");
    $stmtGuru->execute([$guru_id]);
    $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

    if (!$guru) {
        redirect('/admin/kurikulum/supervisi-kelas');
        exit;
    }

    $supervisi = null;
    if ($tahun_ajaran_id) {
        $stmtSup = $db_core->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
        if ($supervisi) {
            $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
        }
    }

    $page_title = "Form Supervisi Kelas";
    $active_page = 'supervisi_kelas';
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_kelas_evaluasi.php';
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-kelas/cetak', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT * FROM institusi LIMIT 1");
    $institusi = $stmt_inst->fetch(\PDO::FETCH_ASSOC);
    $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
    
    // Ambil nama tahun ajaran
    $stmt_ta = $db_core->prepare("SELECT name, semester FROM tahun_ajaran WHERE id = ?");
    $stmt_ta->execute([$tahun_ajaran_id]);
    if ($ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC)) {
        $smt = ($ta['semester'] == 1) ? 'Ganjil' : 'Genap';
        $institusi['tahun_ajaran_nama'] = $ta['name'] . ' (' . $smt . ')';
    }
    
    $guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
    if (!$guru_id) {
        redirect('/admin/kurikulum/supervisi-kelas');
        exit;
    }

    $stmtGuru = $db_core->prepare("SELECT * FROM guru WHERE id = ?");
    $stmtGuru->execute([$guru_id]);
    $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

    if (!$guru) {
        redirect('/admin/kurikulum/supervisi-kelas');
        exit;
    }
    
    // Ambil Kop Surat
    $kop_surat = null;
    $kop = null;
    try {
        $stmt_kop = $db_core->query("SELECT * FROM pengaturan_kop_surat ORDER BY id DESC LIMIT 1");
        $kop = $stmt_kop->fetch(\PDO::FETCH_ASSOC);
        if ($kop && !empty($kop['kop_surat_base64'])) {
            $kop_surat = $kop['kop_surat_base64'];
        }
    } catch (\Exception $e) { }

    $supervisi = null;
    if ($tahun_ajaran_id) {
        $stmtSup = $db_core->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
        if ($supervisi) {
            $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
        }
    }

    $page_title = "Cetak Supervisi Kelas - " . htmlspecialchars($guru['nama']);
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_kelas_cetak.php';
    exit;
});

$router->add('POST', '/admin/kurikulum/supervisi-kelas/save', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_id = $_POST['guru_id'] ?? null;
    $tanggal_supervisi = $_POST['tanggal_supervisi'] ?? date('Y-m-d');
    $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $jam_ke = $_POST['jam_ke'] ?? '';
    $materi_pokok = $_POST['materi_pokok'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    
    $instrumen = $_POST['instrumen'] ?? [];
    $instrumen_json = json_encode($instrumen);

    // Hitung total skor (9 item * 4 = 36 max)
    $score = 0;
    foreach ($instrumen as $val) {
        $score += (int)$val;
    }
    $total_items = 9; // Dari APK 9 poin
    $nilai_akhir = round(($score / ($total_items * 4)) * 100);

    // Ambil nama penilai dari session
    $penilai_nama = $_SESSION['nama'] ?? 'Tim Supervisi';

    try {
        // Cek dan tambahkan kolom jika belum ada
        try {
            $db_core->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN nilai_akhir INT NULL AFTER instrumen_json");
            $db_core->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN penilai_nama VARCHAR(150) NULL AFTER tindak_lanjut");
        } catch (\Exception $ex) {
            // Kolom mungkin sudah ada
        }

        $stmt = $db_core->prepare("
            INSERT INTO kurikulum_supervisi_kelas 
            (tahun_ajaran_id, guru_id, tanggal_supervisi, mata_pelajaran, kelas, jam_ke, materi_pokok, instrumen_json, nilai_akhir, catatan, tindak_lanjut, penilai_nama) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            tanggal_supervisi = VALUES(tanggal_supervisi),
            mata_pelajaran = VALUES(mata_pelajaran),
            kelas = VALUES(kelas),
            jam_ke = VALUES(jam_ke),
            materi_pokok = VALUES(materi_pokok),
            instrumen_json = VALUES(instrumen_json),
            nilai_akhir = VALUES(nilai_akhir),
            catatan = VALUES(catatan),
            tindak_lanjut = VALUES(tindak_lanjut),
            penilai_nama = VALUES(penilai_nama)
        ");
        $stmt->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, $instrumen_json, $nilai_akhir, $catatan, $tindak_lanjut, $penilai_nama]);
        
        echo json_encode(['success' => true, 'message' => 'Data supervisi kelas berhasil disimpan']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
    }
    exit;
});

$router->add('GET', '/admin/kurikulum/supervisi-penilaian', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        redirect('/login');
        exit;
    }
    
    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $inst = $stmt_inst->fetch(\PDO::FETCH_ASSOC);
    $tahun_ajaran_id = $inst['tahun_ajaran_id'] ?? 1;
    $semester = $inst['semester'] ?? 'Ganjil';
    
    $kelas_id = $_GET['kelas_id'] ?? null;
    $guru_id = $_GET['guru_id'] ?? null;

    $kelas_list = $db_core->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
    $guru_list = $db_core->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

    $monitoring_data = [];

    if ($kelas_id || $guru_id) {
        $query = "SELECT DISTINCT j.mata_pelajaran_id as mapel_id, j.guru_id, j.kelas_id, m.nama_mapel, g.nama as nama_guru, k.nama_kelas 
                  FROM aula_siakad_db.jadwal_pelajaran j 
                  JOIN mapel m ON m.id = j.mata_pelajaran_id 
                  JOIN kelas k ON k.id = j.kelas_id
                  LEFT JOIN guru g ON g.id = j.guru_id WHERE 1=1 ";
        
        $params = [];
        if ($kelas_id) {
            $query .= " AND j.kelas_id = ?";
            $params[] = $kelas_id;
        }
        if ($guru_id) {
            $query .= " AND j.guru_id = ?";
            $params[] = $guru_id;
        }
        $query .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC, m.nama_mapel ASC";

        $stmt_jadwal = $db_core->prepare($query);
        $stmt_jadwal->execute($params);
        $jadwals = $stmt_jadwal->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch total siswa for all relevant classes
        $kelas_totals = [];
        $stmt_siswa = $db_core->prepare("SELECT kelas_id, COUNT(*) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
        $stmt_siswa->execute();
        foreach ($stmt_siswa->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $kelas_totals[$row['kelas_id']] = $row['total'];
        }

        // Get all nilai_harian for relevant TA, semester
        $stmt_nilai = $db_core->prepare("SELECT n.mapel_id, n.kelas_id, n.jenis_evaluasi, COUNT(DISTINCT d.siswa_id) as total_dinilai, MAX(n.judul_materi) as materi
            FROM nilai_harian n
            JOIN nilai_harian_detail d ON d.nilai_harian_id = n.id
            WHERE n.tahun_ajaran_id = ? AND n.semester = ? AND d.nilai IS NOT NULL
            GROUP BY n.mapel_id, n.kelas_id, n.jenis_evaluasi");
        $stmt_nilai->execute([$tahun_ajaran_id, $semester]);
        $nilais = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);

        // Get supervision data
        $stmt_sup = $db_core->prepare("SELECT * FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ?");
        $stmt_sup->execute([$tahun_ajaran_id, $semester]);
        
        $supervisi_indexed = [];
        foreach ($stmt_sup->fetchAll(\PDO::FETCH_ASSOC) as $sup) {
            $key = $sup['kelas_id'] . '_' . $sup['mapel_id'] . '_' . $sup['jenis_evaluasi'];
            $supervisi_indexed[$key] = $sup;
        }

        foreach ($jadwals as $j) {
            $k_id = $j['kelas_id'];
            $m_id = $j['mapel_id'];
            $total_siswa = $kelas_totals[$k_id] ?? 0;
            
            $evaluasi = [];
            foreach ($nilais as $n) {
                if ($n['kelas_id'] == $k_id && $n['mapel_id'] == $m_id) {
                    $key = $k_id . '_' . $m_id . '_' . $n['jenis_evaluasi'];
                    $n['supervisi'] = $supervisi_indexed[$key] ?? null;
                    $n['persentase'] = $total_siswa > 0 ? min(100, round(($n['total_dinilai'] / $total_siswa) * 100)) : 0;
                    $evaluasi[] = $n;
                }
            }

            if (!empty($evaluasi)) {
                $j['evaluasi'] = $evaluasi;
                $j['total_siswa'] = $total_siswa;
                $monitoring_data[] = $j;
            }
        }
    }

    $page_title = "Supervisi Penilaian";
    $active_page = 'supervisi_penilaian';
    include __DIR__ . '/../resources/views/admin/kurikulum_supervisi_penilaian.php';
    exit;
});

$router->add('POST', '/admin/kurikulum/supervisi-penilaian/save', function() {
    $role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    if (!isset($_SESSION['user_id']) || !in_array($role, [1, 99])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $db_core = \App\Core\Database::connect('core');
    $stmt_inst = $db_core->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $inst = $stmt_inst->fetch(\PDO::FETCH_ASSOC);
    $tahun_ajaran_id = $inst['tahun_ajaran_id'] ?? 1;
    $semester = $inst['semester'] ?? 'Ganjil';
    
    $guru_id = $_POST['guru_id'] ?? 0;
    $kelas_id = $_POST['kelas_id'] ?? 0;
    $mapel_id = $_POST['mapel_id'] ?? 0;
    $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
    
    $ada_kisi = isset($_POST['ada_kisi']) ? 1 : 0;
    $ada_analisis = isset($_POST['ada_analisis']) ? 1 : 0;
    $ada_remedial = isset($_POST['ada_remedial']) ? 1 : 0;
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    try {
        $stmt = $db_core->prepare("
            INSERT INTO kurikulum_supervisi_nilai 
            (tahun_ajaran_id, semester, guru_id, kelas_id, mapel_id, jenis_evaluasi, ada_kisi, ada_analisis, ada_remedial, catatan, tindak_lanjut, disupervisi_oleh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            ada_kisi = VALUES(ada_kisi),
            ada_analisis = VALUES(ada_analisis),
            ada_remedial = VALUES(ada_remedial),
            catatan = VALUES(catatan),
            tindak_lanjut = VALUES(tindak_lanjut),
            disupervisi_oleh = VALUES(disupervisi_oleh)
        ");
        $stmt->execute([
            $tahun_ajaran_id, $semester, $guru_id, $kelas_id, $mapel_id, $jenis_evaluasi,
            $ada_kisi, $ada_analisis, $ada_remedial, $catatan, $tindak_lanjut, $user_id
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Data supervisi berhasil disimpan']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
});
$router->add('GET', '/admin/siakad/kop-surat', ['\App\Controllers\KopSuratController', 'index']);
$router->add('POST', '/admin/siakad/kop-surat/update', ['\App\Controllers\KopSuratController', 'update']);

// Bank Soal Routes
$router->add('GET', '/bank_soal', function() {
    include __DIR__ . '/../resources/views/bank_soal/dashboard.php';
    exit;
});
$router->add('GET', '/bank_soal/topik', ['\App\Controllers\BankSoalController', 'indexTopik']);
$router->add('POST', '/bank_soal/topik/simpan', ['\App\Controllers\BankSoalController', 'simpanTopik']);
$router->add('GET', '/bank_soal/topik/hapus', ['\App\Controllers\BankSoalController', 'hapusTopik']);

$router->add('GET', '/bank_soal/kelola_soal', ['\App\Controllers\BankSoalController', 'kelolaSoal']);
$router->add('GET', '/bank_soal/daftar_soal', ['\App\Controllers\BankSoalController', 'daftarSoal']);

$router->add('GET', '/bank_soal/tambah_soal', ['\App\Controllers\BankSoalController', 'tambahSoal']);
$router->add('POST', '/bank_soal/tambah_soal', ['\App\Controllers\BankSoalController', 'simpanSoal']);
$router->add('GET', '/bank_soal/hapus_soal', ['\App\Controllers\BankSoalController', 'hapusSoal']);

// Template UI Routes
$router->add('GET', '/template', function() {
    include __DIR__ . '/../resources/views/template/dashboard.php';
    exit;
});
$router->add('GET', '/template/standar', function() {
    include __DIR__ . '/../resources/views/template/menu_standar.php';
    exit;
});
$router->add('GET', '/template/pengaturan', function() {
    include __DIR__ . '/../resources/views/template/pengaturan.php';
    exit;
});
$router->add('GET', '/template/catatan', function() {
    include __DIR__ . '/../resources/views/template/catatan.php';
    exit;
});

// PKKM MODULE
$router->add('GET', '/admin/pkkm', [\App\Controllers\AdminPkkmController::class, 'dashboard']);
$router->add('GET', '/admin/pkkm/dashboard', [\App\Controllers\AdminPkkmController::class, 'dashboard']);
$router->add('GET', '/admin/pkkm/instrumen', [\App\Controllers\AdminPkkmController::class, 'instrumen']);
$router->add('POST', '/admin/pkkm/komponen/save', [\App\Controllers\AdminPkkmController::class, 'saveKomponen']);
$router->add('POST', '/admin/pkkm/komponen/delete', [\App\Controllers\AdminPkkmController::class, 'deleteKomponen']);
$router->add('POST', '/admin/pkkm/sub-komponen/save', [\App\Controllers\AdminPkkmController::class, 'saveSubKomponen']);
$router->add('POST', '/admin/pkkm/sub-komponen/delete', [\App\Controllers\AdminPkkmController::class, 'deleteSubKomponen']);
$router->add('POST', '/admin/pkkm/indikator/save', [\App\Controllers\AdminPkkmController::class, 'saveIndikator']);
$router->add('POST', '/admin/pkkm/indikator/delete', [\App\Controllers\AdminPkkmController::class, 'deleteIndikator']);
$router->add('POST', '/admin/pkkm/berkas/save', [\App\Controllers\AdminPkkmController::class, 'saveBerkas']);
$router->add('POST', '/admin/pkkm/berkas/delete', [\App\Controllers\AdminPkkmController::class, 'deleteBerkas']);
$router->add('POST', '/admin/pkkm/nilai/save', [\App\Controllers\AdminPkkmController::class, 'saveNilai']);
$router->add('POST', '/admin/pkkm/sync-instrumen', [\App\Controllers\AdminPkkmController::class, 'syncLatestInstrumen']);
$router->add('GET', '/admin/pkkm/sync-instrumen', [\App\Controllers\AdminPkkmController::class, 'syncLatestInstrumen']);

// PKKM Master Berkas / Dokumen
$router->add('GET', '/admin/pkkm/master-berkas', [\App\Controllers\AdminPkkmController::class, 'daftarBerkas']);
$router->add('GET', '/admin/pkkm/dokumen', [\App\Controllers\AdminPkkmController::class, 'daftarBerkas']);
$router->add('GET', '/admin/pkkm/master-berkas/sync', [\App\Controllers\AdminPkkmController::class, 'syncMasterBerkas']);
$router->add('POST', '/admin/pkkm/master-berkas/sync', [\App\Controllers\AdminPkkmController::class, 'syncMasterBerkas']);
$router->add('POST', '/admin/pkkm/master-berkas/create', [\App\Controllers\AdminPkkmController::class, 'createMasterBerkas']);
$router->add('POST', '/admin/pkkm/master-berkas/upload', [\App\Controllers\AdminPkkmController::class, 'uploadMasterBerkas']);
$router->add('POST', '/admin/pkkm/master-berkas/attach-digital', [\App\Controllers\AdminPkkmController::class, 'attachDigitalToMaster']);
$router->add('POST', '/admin/pkkm/master-berkas/delete', [\App\Controllers\AdminPkkmController::class, 'deleteMasterBerkas']);

// Legacy compatibility for dokumen
$router->add('POST', '/admin/pkkm/dokumen/upload', [\App\Controllers\AdminPkkmController::class, 'uploadDokumen']);
$router->add('POST', '/admin/pkkm/dokumen/delete', [\App\Controllers\AdminPkkmController::class, 'deleteDokumen']);
$router->add('POST', '/admin/pkkm/dokumen/sync', [\App\Controllers\AdminPkkmController::class, 'syncSistem']);
$router->add('GET', '/admin/pkkm/dokumen/sync', [\App\Controllers\AdminPkkmController::class, 'syncSistem']);
$router->add('POST', '/admin/pkkm/identitas/save', [\App\Controllers\AdminPkkmController::class, 'saveIdentitas']);
$router->add('GET', '/admin/pkkm/instrumen/cetak', [\App\Controllers\AdminPkkmController::class, 'cetakInstrumen']);

// Bukti Fisik Visual Digital (Semua 13 Generator A4)
$router->add('GET', '/admin/pkkm/cetak-bukti/website-email', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiWebsiteEmail']);
$router->add('GET', '/admin/pkkm/cetak-bukti/jaringan-internet', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiJaringanInternet']);
$router->add('GET', '/admin/pkkm/cetak-bukti/absensi-elektronik', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiAbsensiElektronik']);
$router->add('GET', '/admin/pkkm/cetak-bukti/struktur-organisasi', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiStrukturOrganisasi']);
$router->add('GET', '/admin/pkkm/cetak-bukti/inventaris-tik', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiInventarisTik']);
$router->add('GET', '/admin/pkkm/cetak-bukti/jadwal-kegiatan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiJadwalKegiatan']);
$router->add('GET', '/admin/pkkm/cetak-bukti/visi-misi', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiVisiMisi']);
$router->add('GET', '/admin/pkkm/cetak-bukti/perpustakaan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiPerpustakaan']);
$router->add('GET', '/admin/pkkm/cetak-bukti/lingkungan-sehat', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiLingkunganSehat']);
$router->add('GET', '/admin/pkkm/cetak-bukti/supervisi-guru', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiSupervisiGuru']);
$router->add('GET', '/admin/pkkm/cetak-bukti/ekstrakurikuler', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiEkstrakurikuler']);
$router->add('GET', '/admin/pkkm/cetak-bukti/prestasi-siswa', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiPrestasiSiswa']);
$router->add('GET', '/admin/pkkm/cetak-bukti/kewirausahaan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiKewirausahaan']);
$router->add('POST', '/admin/pkkm/bukti-attach-website', [\App\Controllers\AdminPkkmController::class, 'attachWebsiteLink']);
$router->add('POST', '/admin/pkkm/upload-website-screenshot', [\App\Controllers\AdminPkkmController::class, 'uploadWebsiteScreenshot']);

// PKKM Manajemen Tim Penanggung Jawab Komponen
$router->add('GET', '/admin/pkkm/tim', [\App\Controllers\AdminPkkmController::class, 'tim']);
$router->add('POST', '/admin/pkkm/tim/save', [\App\Controllers\AdminPkkmController::class, 'saveTim']);
$router->add('POST', '/admin/pkkm/tim/delete', [\App\Controllers\AdminPkkmController::class, 'deleteTim']);

// PKKM Manajemen Akun Pengawas
$router->add('GET', '/admin/pkkm/pengawas', [\App\Controllers\AdminPkkmController::class, 'daftarPengawas']);
$router->add('POST', '/admin/pkkm/pengawas/save', [\App\Controllers\AdminPkkmController::class, 'savePengawas']);
$router->add('POST', '/admin/pkkm/pengawas/delete', [\App\Controllers\AdminPkkmController::class, 'deletePengawas']);
$router->add('GET', '/admin/pkkm/pengawas/generate-default', [\App\Controllers\AdminPkkmController::class, 'generateDefaultPengawas']);

// PKKM Portal Asesor / Pengawas
$router->add('GET', '/pengawas/pkkm', [\App\Controllers\PengawasPkkmController::class, 'index']);
$router->add('POST', '/pengawas/pkkm/nilai', [\App\Controllers\PengawasPkkmController::class, 'saveNilai']);
$router->add('POST', '/pengawas/pkkm/reset-nilai', [\App\Controllers\PengawasPkkmController::class, 'resetNilai']);
$router->add('GET', '/pengawas/pkkm/reset-nilai', [\App\Controllers\PengawasPkkmController::class, 'resetNilai']);
$router->add('POST', '/pengawas/pkkm/simpan-ttd', [\App\Controllers\PengawasPkkmController::class, 'saveTtd']);
$router->add('GET', '/pengawas/pkkm/cetak-rekap', [\App\Controllers\PengawasPkkmController::class, 'cetakRekap']);

// Akses Cetak Bukti Fisik PKKM untuk Asesor / Pengawas
$router->add('GET', '/pengawas/pkkm/cetak-bukti/website-email', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiWebsiteEmail']);
$router->add('POST', '/pengawas/pkkm/upload-website-screenshot', [\App\Controllers\AdminPkkmController::class, 'uploadWebsiteScreenshot']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/jaringan-internet', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiJaringanInternet']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/absensi-elektronik', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiAbsensiElektronik']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/struktur-organisasi', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiStrukturOrganisasi']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/inventaris-tik', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiInventarisTik']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/jadwal-kegiatan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiJadwalKegiatan']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/visi-misi', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiVisiMisi']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/perpustakaan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiPerpustakaan']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/lingkungan-sehat', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiLingkunganSehat']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/supervisi-guru', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiSupervisiGuru']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/ekstrakurikuler', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiEkstrakurikuler']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/prestasi-siswa', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiPrestasiSiswa']);
$router->add('GET', '/pengawas/pkkm/cetak-bukti/kewirausahaan', [\App\Controllers\AdminPkkmController::class, 'cetakBuktiKewirausahaan']);

// Portal Guru - PKKM (Penilaian Kinerja Kepala Madrasah)
$router->add('GET', '/guru/pkkm/instrumen', [\App\Controllers\PortalGuruPkkmController::class, 'instrumen']);
$router->add('GET', '/guru/pkkm/master-berkas', [\App\Controllers\PortalGuruPkkmController::class, 'masterBerkas']);
$router->add('POST', '/guru/pkkm/berkas/save', [\App\Controllers\PortalGuruPkkmController::class, 'saveBerkas']);
$router->add('POST', '/guru/pkkm/berkas/delete', [\App\Controllers\PortalGuruPkkmController::class, 'deleteBerkas']);
$router->add('POST', '/guru/pkkm/master-berkas/upload', [\App\Controllers\PortalGuruPkkmController::class, 'uploadMasterBerkas']);
$router->add('POST', '/guru/pkkm/master-berkas/delete', [\App\Controllers\PortalGuruPkkmController::class, 'deleteMasterBerkas']);
$router->add('POST', '/guru/pkkm/master-berkas/attach-digital', [\App\Controllers\PortalGuruPkkmController::class, 'attachDigitalToMaster']);
$router->add('POST', '/guru/pkkm/bukti-attach-website', [\App\Controllers\PortalGuruPkkmController::class, 'attachWebsiteLink']);

$router->run();


// TESTING 


// NO OP








