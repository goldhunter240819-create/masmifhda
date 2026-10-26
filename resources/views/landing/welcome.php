<?php
// Load changelog data
$changelogPath = __DIR__ . '/../../../config/changelog.json';
$changelog = json_decode(file_get_contents($changelogPath), true);
$latestVersion = null;
foreach ($changelog['versions'] as $v) {
    if ($v['is_latest']) { $latestVersion = $v; break; }
}
if (!$latestVersion) $latestVersion = $changelog['versions'][0];

// Branding (safe fallback if not set from caller)
if (!isset($portal_title))   $portal_title   = $changelog['system_name'];
if (!isset($portal_version)) $portal_version = $latestVersion['version'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($portal_title); ?> Ã¢â‚¬â€ Selamat Datang</title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars(isset($portal_favicon) ? $portal_favicon : base_url('public/assets/images/favicon.png')); ?>">
    <meta name="description" content="Aula System System Ã¢â‚¬â€ Ekosistem digital terintegrasi untuk manajemen sekolah modern.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #06090f;
            --surface:  rgba(15, 23, 42, 0.7);
            --border:   rgba(255, 255, 255, 0.07);
            --primary:  #0ea5e9;
            --primary2: #34d399;
            --text:     #e2e8f0;
            --muted:    #64748b;
            --card-bg:  rgba(17, 24, 39, 0.6);
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ BACKGROUND Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .bg-canvas {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(16,185,129,0.18) 0%, transparent 70%),
                        radial-gradient(ellipse 60% 40% at 100% 80%, rgba(59,130,246,0.08) 0%, transparent 60%),
                        #06090f;
        }
        .stars { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .star {
            position: absolute; border-radius: 50%;
            background: #fff; animation: twinkle linear infinite;
        }

        @keyframes twinkle {
            0%,100% { opacity: 0.1; transform: scale(1); }
            50%      { opacity: 0.7; transform: scale(1.3); }
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-18px); }
        }
        @keyframes fadeUp {
            from { opacity:0; transform: translateY(30px); }
            to   { opacity:1; transform: translateY(0); }
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
            70%  { box-shadow: 0 0 0 14px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        @keyframes gradShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes scanline {
            0%   { transform: translateY(-100%); }
            100% { transform: translateY(100vh); }
        }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ NAVBAR Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            height: 68px;
            background: rgba(6, 9, 15, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 16px rgba(16,185,129,0.35);
        }
        .nav-logo-icon i { width: 20px; height: 20px; color: white; }
        .nav-logo-text { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: -0.3px; }
        .nav-logo-text span { color: var(--primary); }

        .nav-links { display: flex; align-items: center; gap: 2rem; }
        .nav-links a {
            font-size: 0.88rem; font-weight: 600;
            color: var(--muted); text-decoration: none;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--text); }

        .nav-cta {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .btn-ghost {
            padding: 0.5rem 1.25rem; border-radius: 8px;
            font-size: 0.88rem; font-weight: 700;
            color: var(--text); text-decoration: none;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .btn-ghost:hover { border-color: var(--primary); color: var(--primary); background: rgba(16,185,129,0.07); }

        .btn-primary {
            padding: 0.5rem 1.25rem; border-radius: 8px;
            font-size: 0.88rem; font-weight: 700;
            color: #fff; text-decoration: none;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            box-shadow: 0 0 16px rgba(16,185,129,0.25);
            transition: all 0.3s;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(16,185,129,0.4);
        }
        .btn-primary i { width: 15px; height: 15px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ MAIN LAYOUT Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        main { position: relative; z-index: 1; padding-top: 68px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ HERO Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .hero {
            min-height: calc(100vh - 68px);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            padding: 4rem 1.5rem 6rem;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.25);
            padding: 6px 16px; border-radius: 100px;
            font-size: 0.8rem; font-weight: 700; color: var(--primary);
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease backwards;
        }
        .hero-badge .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--primary);
            animation: pulse-ring 2s infinite;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900; line-height: 1.1;
            color: #fff; margin-bottom: 1.5rem;
            animation: fadeUp 0.6s 0.1s ease backwards;
        }
        .hero-title .grad {
            background: linear-gradient(135deg, #34d399, #0ea5e9, #0284c7, #34d399);
            background-size: 300% 300%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: gradShift 4s ease infinite;
        }

        .hero-sub {
            font-size: 1.1rem; color: var(--muted);
            max-width: 560px; line-height: 1.7;
            margin: 0 auto 2.5rem;
            animation: fadeUp 0.6s 0.2s ease backwards;
        }

        .hero-actions {
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: center;
            animation: fadeUp 0.6s 0.3s ease backwards;
        }
        .hero-btn {
            padding: 0.85rem 2rem; border-radius: 12px;
            font-size: 1rem; font-weight: 700;
            text-decoration: none; display: flex; align-items: center; gap: 8px;
            transition: all 0.3s;
        }
        .hero-btn i { width: 18px; height: 18px; }
        .hero-btn-primary {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #fff;
            box-shadow: 0 4px 20px rgba(16,185,129,0.3);
        }
        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(16,185,129,0.5);
        }
        .hero-btn-outline {
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text);
            backdrop-filter: blur(10px);
        }
        .hero-btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(16,185,129,0.08);
        }

        /* Stats bar */
        .hero-stats {
            display: flex; gap: 3rem; margin-top: 4rem; flex-wrap: wrap; justify-content: center;
            animation: fadeUp 0.6s 0.4s ease backwards;
        }
        .stat-item { text-align: center; }
        .stat-num { font-size: 2rem; font-weight: 900; color: #fff; }
        .stat-num span { color: var(--primary); }
        .stat-label { font-size: 0.78rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ MODULES SHOWCASE Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .section { padding: 5rem 1.5rem; max-width: 1100px; margin: 0 auto; }
        .section-tag {
            font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 2px; color: var(--primary); margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 800;
            color: #fff; line-height: 1.2; margin-bottom: 0.75rem;
        }
        .section-sub { font-size: 1rem; color: var(--muted); max-width: 520px; line-height: 1.65; }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem; margin-top: 3rem;
        }
        .module-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem;
            backdrop-filter: blur(12px);
            transition: all 0.3s;
            position: relative; overflow: hidden;
            animation: fadeUp 0.5s ease backwards;
        }
        .module-card::before {
            content: '';
            position: absolute; inset: 0; border-radius: 16px;
            background: linear-gradient(135deg, var(--accent-color, #0ea5e9), transparent);
            opacity: 0; transition: opacity 0.3s;
        }
        .module-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.15); }
        .module-card:hover::before { opacity: 0.05; }

        .module-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }
        .module-icon i { width: 22px; height: 22px; }
        .module-name { font-size: 0.95rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .module-desc { font-size: 0.78rem; color: var(--muted); line-height: 1.5; }
        .module-lock {
            position: absolute; top: 12px; right: 12px;
            width: 24px; height: 24px; border-radius: 6px;
            background: rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted);
        }
        .module-lock i { width: 12px; height: 12px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ WHAT'S NEW SECTION Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .updates-section {
            padding: 5rem 1.5rem;
            background: rgba(15, 23, 42, 0.3);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .updates-inner { max-width: 1100px; margin: 0 auto; }

        .version-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3);
            padding: 5px 14px; border-radius: 100px;
            font-size: 0.8rem; font-weight: 800; color: var(--primary);
            margin-bottom: 0.75rem;
        }

        .latest-update-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px; padding: 2.5rem;
            backdrop-filter: blur(16px);
            position: relative; overflow: hidden;
            margin-top: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .latest-update-card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .update-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .update-version-label {
            font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--primary); margin-bottom: 6px;
        }
        .update-title-main { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .update-summary { font-size: 0.9rem; color: var(--muted); line-height: 1.6; max-width: 500px; }

        .update-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
        .meta-chip {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.78rem; font-weight: 600; color: var(--muted);
        }
        .meta-chip i { width: 14px; height: 14px; }

        .changes-list { display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 2rem; }
        .change-item {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 0.88rem; color: #cbd5e1; line-height: 1.5;
        }
        .change-pill {
            flex-shrink: 0; padding: 2px 8px; border-radius: 100px;
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
            margin-top: 2px;
        }
        .pill-new    { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .pill-fix    { background: rgba(251,191,36,0.1);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .pill-update { background: rgba(99,102,241,0.12); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }

        .update-cta {
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
            padding-top: 2rem; border-top: 1px solid var(--border);
        }
        .btn-see-updates {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.85rem 2rem; border-radius: 12px;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #fff; font-size: 0.95rem; font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(16,185,129,0.3);
            transition: all 0.3s;
        }
        .btn-see-updates:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(16,185,129,0.45); }
        .btn-see-updates i { width: 18px; height: 18px; }
        .btn-ghost-updates {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.85rem 1.5rem; border-radius: 12px;
            background: transparent; border: 1px solid var(--border);
            color: var(--muted); font-size: 0.88rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-ghost-updates:hover { border-color: rgba(255,255,255,0.2); color: var(--text); }
        .btn-ghost-updates i { width: 16px; height: 16px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ FOOTER Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        footer {
            position: relative; z-index: 1;
            padding: 2.5rem 2rem;
            text-align: center;
            border-top: 1px solid var(--border);
        }
        .footer-logo { font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .footer-logo span { color: var(--primary); }
        .footer-note { font-size: 0.78rem; color: var(--muted); }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ RESPONSIVE Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        @media (max-width: 768px) {
            nav { padding: 0 1rem; }
            .nav-links { display: none; }
            .hero { padding: 3rem 1rem 4rem; }
            .hero-stats { gap: 2rem; }
            .modules-grid { grid-template-columns: repeat(2, 1fr); }
            .update-header { flex-direction: column; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-canvas"></div>
<div class="stars" id="stars"></div>

<!-- Navbar -->
<nav>
    <a href="<?php echo base_url(''); ?>" class="nav-logo">
        <div class="nav-logo-icon"><i data-lucide="graduation-cap"></i></div>
        <span class="nav-logo-text">GH<span>-SSS</span></span>
    </a>
    <div class="nav-links">
        <a href="#modules">Modul</a>
        <a href="#updates">Update</a>
        <a href="<?php echo base_url('updates'); ?>">Changelog</a>
    </div>
    <div class="nav-cta">
        <a href="<?php echo base_url('updates'); ?>" class="btn-ghost">Lihat Update</a>
        <a href="<?php echo base_url('login'); ?>" class="btn-primary">
            <i data-lucide="log-in"></i> Masuk
        </a>
    </div>
</nav>

<!-- Main -->
<main>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-badge">
            <span class="dot"></span>
            Versi <?php echo htmlspecialchars($latestVersion['version']); ?> Tersedia
        </div>

        <h1 class="hero-title">
            Selamat Datang di<br>
            <span class="grad">Aula System</span><br>
            System
        </h1>

        <p class="hero-sub">
            Ekosistem digital terintegrasi untuk manajemen akademik, keuangan, evaluasi, absensi, dan perpustakaan Ã¢â‚¬â€ semua dalam satu platform.
        </p>

        <div class="hero-actions">
            <a href="<?php echo base_url('login'); ?>" class="hero-btn hero-btn-primary">
                <i data-lucide="log-in"></i> Masuk ke Portal
            </a>
            <a href="<?php echo base_url('updates'); ?>" class="hero-btn hero-btn-outline">
                <i data-lucide="sparkles"></i> Lihat Update Terbaru
            </a>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-num">8<span>+</span></div>
                <div class="stat-label">Modul Sistem</div>
            </div>
            <div class="stat-item">
                <div class="stat-num"><span>v</span><?php echo htmlspecialchars($latestVersion['version']); ?></div>
                <div class="stat-label">Versi Terbaru</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">24<span>/7</span></div>
                <div class="stat-label">Akses Online</div>
            </div>
        </div>
    </section>

    <!-- MODULES -->
    <section class="section" id="modules">
        <div class="section-tag">Fitur Platform</div>
        <h2 class="section-title">Semua yang Anda<br>butuhkan, dalam satu sistem</h2>
        <p class="section-sub">Login untuk mengakses seluruh modul yang tersedia sesuai peran Anda.</p>

        <div class="modules-grid">
            <?php
            $modules = [
                ['name'=>'SIAKAD',             'desc'=>'Manajemen akademik & data siswa',    'icon'=>'graduation-cap', 'color'=>'#0ea5e9', 'bg'=>'rgba(16,185,129,0.12)'],
                ['name'=>'GHCBT System',        'desc'=>'Ujian online dengan analitik CBT',   'icon'=>'monitor-play',   'color'=>'#8b5cf6', 'bg'=>'rgba(139,92,246,0.12)'],
                ['name'=>'Keuangan',            'desc'=>'SPP, laporan & monitoring tunggakan','icon'=>'credit-card',    'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.12)'],
                ['name'=>'Tabungan Siswa',      'desc'=>'Setoran & Penarikan Tabungan Siswa', 'icon'=>'wallet',     'color'=>'#0284c7', 'bg'=>'rgba(16,185,129,0.12)'],
                ['name'=>'Absensi QR V2',       'desc'=>'Presensi dengan QR Code dinamis',   'icon'=>'qr-code',        'color'=>'#14b8a6', 'bg'=>'rgba(20,184,166,0.12)'],
                ['name'=>'Persuratan',          'desc'=>'Surat masuk, keluar & disposisi',   'icon'=>'mail',           'color'=>'#3b82f6', 'bg'=>'rgba(59,130,246,0.12)'],
                ['name'=>'Inventaris',          'desc'=>'Pencatatan aset sekolah',            'icon'=>'package',        'color'=>'#a855f7', 'bg'=>'rgba(168,85,247,0.12)'],
                ['name'=>'Perpustakaan',        'desc'=>'Katalog & peminjaman digital',       'icon'=>'library',        'color'=>'#0d9488', 'bg'=>'rgba(13,148,136,0.12)'],
                ['name'=>'E-Learning',          'desc'=>'Materi, tugas & forum diskusi',      'icon'=>'monitor',        'color'=>'#ef4444', 'bg'=>'rgba(239,68,68,0.12)'],
                ['name'=>'Piagam Digital',      'desc'=>'Cetak sertifikat massal + QR',       'icon'=>'award',          'color'=>'#eab308', 'bg'=>'rgba(234,179,8,0.12)'],
            ];
            foreach ($modules as $i => $mod):
            ?>
            <div class="module-card" style="animation-delay: <?php echo $i * 0.05; ?>s; --accent-color: <?php echo $mod['color']; ?>">
                <div class="module-lock"><i data-lucide="lock"></i></div>
                <div class="module-icon" style="background: <?php echo $mod['bg']; ?>; color: <?php echo $mod['color']; ?>">
                    <i data-lucide="<?php echo $mod['icon']; ?>"></i>
                </div>
                <div class="module-name"><?php echo $mod['name']; ?></div>
                <div class="module-desc"><?php echo $mod['desc']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- WHAT'S NEW -->
    <section class="updates-section" id="updates">
        <div class="updates-inner">
            <div class="section-tag">Pembaruan Sistem</div>
            <h2 class="section-title">Apa yang Baru?</h2>
            <p class="section-sub">Pembaruan terbaru yang telah dirilis untuk Aula System System.</p>

            <div class="latest-update-card">
                <div class="update-header">
                    <div>
                        <div class="update-version-label">Versi <?php echo htmlspecialchars($latestVersion['version']); ?> Ã¢â‚¬â€ <?php echo date('d M Y', strtotime($latestVersion['release_date'])); ?></div>
                        <div class="update-title-main"><?php echo htmlspecialchars($latestVersion['title']); ?></div>
                        <p class="update-summary"><?php echo htmlspecialchars($latestVersion['summary']); ?></p>
                    </div>
                    <div class="update-meta">
                        <div class="meta-chip"><i data-lucide="calendar"></i> <?php echo date('d M Y', strtotime($latestVersion['release_date'])); ?></div>
                        <div class="meta-chip"><i data-lucide="hard-drive"></i> <?php echo $latestVersion['file_size']; ?></div>
                        <div class="meta-chip"><i data-lucide="code-2"></i> PHP <?php echo $latestVersion['min_php']; ?>+</div>
                    </div>
                </div>

                <div class="changes-list">
                    <?php foreach (array_slice($latestVersion['changes'], 0, 5) as $c): ?>
                    <div class="change-item">
                        <span class="change-pill pill-<?php echo $c['type']; ?>"><?php echo $c['type']; ?></span>
                        <span><?php echo htmlspecialchars($c['text']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($latestVersion['changes']) > 5): ?>
                    <div class="change-item" style="color: var(--muted); font-size:0.82rem; padding-left: 2px;">
                        + <?php echo count($latestVersion['changes']) - 5; ?> perubahan lainnya...
                    </div>
                    <?php endif; ?>
                </div>

                <div class="update-cta">
                    <a href="<?php echo base_url('updates'); ?>" class="btn-see-updates">
                        <i data-lucide="sparkles"></i>
                        Lihat Semua Update & Panduan
                    </a>
                    <a href="<?php echo base_url('login'); ?>" class="btn-ghost-updates">
                        <i data-lucide="log-in"></i>
                        Sudah punya akun? Masuk
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Footer -->
<footer>
    <div class="footer-logo">GH<span>Smart</span>SchoolSystem</div>
    <div class="footer-note">Ã‚Â© <?php echo date('Y'); ?> Aula System Platform Ã‚Â· Dikembangkan oleh Tim Developer GH</div>
</footer>

<script>
// Generate stars
(function() {
    const container = document.getElementById('stars');
    for (let i = 0; i < 120; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2.5 + 0.5;
        s.style.cssText = `
            width: ${size}px; height: ${size}px;
            top: ${Math.random()*100}vh;
            left: ${Math.random()*100}vw;
            animation-duration: ${Math.random()*4+3}s;
            animation-delay: ${Math.random()*5}s;
            opacity: ${Math.random()*0.5+0.05};
        `;
        container.appendChild(s);
    }
})();

// Lucide icons
lucide.createIcons();
</script>
</body>
</html>


