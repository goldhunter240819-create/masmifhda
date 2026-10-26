<?php
// Load changelog data
$changelogPath = __DIR__ . '/../../../config/changelog.json';
$changelog = json_decode(file_get_contents($changelogPath), true);
$versions  = $changelog['versions'];
$steps     = $changelog['install_steps'];
$latest    = null;
foreach ($versions as $v) { if ($v['is_latest']) { $latest = $v; break; } }
if (!$latest) $latest = $versions[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update & Changelog â€” Aula System System</title>
    <meta name="description" content="Riwayat perubahan dan panduan update Aula System System.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #06090f;
            --card-bg: rgba(17, 24, 39, 0.65);
            --border:  rgba(255, 255, 255, 0.07);
            --primary: #0ea5e9;
            --text:    #e2e8f0;
            --muted:   #64748b;
        }

        html { scroll-behavior: smooth; }

        body {
            background: radial-gradient(ellipse 80% 40% at 50% 0%, rgba(16,185,129,0.14) 0%, transparent 70%), #06090f;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes gradShift {
            0%,100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        /* â”€â”€â”€ NAV â”€â”€â”€ */
        nav {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; height: 68px;
            background: rgba(6,9,15,0.9); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px; text-decoration: none;
        }
        .nav-logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 16px rgba(16,185,129,0.3);
        }
        .nav-logo-icon i { width: 18px; height: 18px; color: white; }
        .nav-logo-text { font-size: 1.05rem; font-weight: 800; color: #fff; }
        .nav-logo-text span { color: var(--primary); }
        .nav-right { display: flex; align-items: center; gap: 0.75rem; }
        .btn-back {
            display: flex; align-items: center; gap: 6px;
            padding: 0.45rem 1rem; border-radius: 8px;
            font-size: 0.85rem; font-weight: 600; color: var(--muted);
            text-decoration: none; border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .btn-back:hover { color: var(--text); border-color: rgba(255,255,255,0.15); }
        .btn-back i { width: 14px; height: 14px; }
        .btn-login {
            display: flex; align-items: center; gap: 6px;
            padding: 0.45rem 1.1rem; border-radius: 8px;
            font-size: 0.85rem; font-weight: 700; color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            transition: all 0.2s;
        }
        .btn-login:hover { opacity: 0.88; }
        .btn-login i { width: 14px; height: 14px; }

        /* â”€â”€â”€ PAGE HERO â”€â”€â”€ */
        .page-hero {
            max-width: 800px; margin: 0 auto;
            padding: 4rem 1.5rem 2rem;
            text-align: center;
            animation: fadeUp 0.5s ease backwards;
        }
        .page-tag {
            display: inline-block;
            font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 2px; color: var(--primary); margin-bottom: 1rem;
        }
        .page-title {
            font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; color: #fff;
            line-height: 1.15; margin-bottom: 1rem;
        }
        .page-title span {
            background: linear-gradient(135deg, #34d399, #0ea5e9, #0284c7, #34d399);
            background-size: 300%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: gradShift 4s ease infinite;
        }
        .page-sub { font-size: 1rem; color: var(--muted); line-height: 1.7; }

        /* â”€â”€â”€ DOWNLOAD HERO CARD â”€â”€â”€ */
        .download-wrap { max-width: 900px; margin: 0 auto; padding: 0 1.5rem 3rem; }

        .download-card {
            background: var(--card-bg);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 24px; padding: 2.5rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.06);
            position: relative; overflow: hidden;
            animation: fadeUp 0.5s 0.1s ease backwards;
        }
        .download-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .dl-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 2rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .dl-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);
            padding: 4px 12px; border-radius: 100px;
            font-size: 0.72rem; font-weight: 800; color: var(--primary);
            margin-bottom: 8px;
        }
        .dl-badge .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--primary);
        }
        .dl-version { font-size: 1.6rem; font-weight: 900; color: #fff; margin-bottom: 4px; }
        .dl-date { font-size: 0.82rem; color: var(--muted); }

        .dl-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem; }
        .dl-meta-item {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.8rem; font-weight: 600; color: var(--muted);
        }
        .dl-meta-item i { width: 14px; height: 14px; color: #475569; }

        .dl-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 1rem 2rem; border-radius: 14px;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #fff; font-size: 1rem; font-weight: 800;
            text-decoration: none;
            box-shadow: 0 6px 24px rgba(16,185,129,0.35);
            transition: all 0.3s; white-space: nowrap;
            border: none; cursor: pointer;
        }
        .dl-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(16,185,129,0.5); }
        .dl-btn i { width: 20px; height: 20px; }
        .dl-btn-note { font-size: 0.72rem; font-weight: 500; opacity: 0.8; }

        .dl-changes { display: flex; flex-direction: column; gap: 0.55rem; }
        .change-item {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 0.88rem; color: #cbd5e1; line-height: 1.5;
        }
        .change-pill {
            flex-shrink: 0; padding: 2px 8px; border-radius: 100px;
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
            margin-top: 2px;
        }
        .pill-new    { background: rgba(16,185,129,0.15);  color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .pill-fix    { background: rgba(251,191,36,0.1);   color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .pill-update { background: rgba(99,102,241,0.12);  color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }

        /* â”€â”€â”€ LAYOUT 2-COL â”€â”€â”€ */
        .content-grid {
            max-width: 1100px; margin: 0 auto; padding: 0 1.5rem 5rem;
            display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;
        }

        /* â”€â”€â”€ INSTALL GUIDE â”€â”€â”€ */
        .guide-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 20px; padding: 2rem; backdrop-filter: blur(14px);
            animation: fadeUp 0.5s 0.2s ease backwards;
        }
        .guide-header { margin-bottom: 1.75rem; }
        .guide-title {
            font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 4px;
        }
        .guide-sub { font-size: 0.82rem; color: var(--muted); }

        .steps-list { display: flex; flex-direction: column; gap: 0; }
        .step-item {
            display: flex; gap: 1rem;
            position: relative; padding-bottom: 1.75rem;
        }
        .step-item:last-child { padding-bottom: 0; }
        .step-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 19px; top: 40px; bottom: 0;
            width: 2px; background: var(--border);
        }
        .step-num {
            flex-shrink: 0; width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 800; color: #fff;
            position: relative; z-index: 1;
        }
        .step-body { flex: 1; padding-top: 6px; }
        .step-title { font-size: 0.92rem; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .step-desc  { font-size: 0.8rem; color: var(--muted); line-height: 1.6; }

        .step-note {
            margin-top: 1.5rem; padding: 1rem 1.25rem;
            background: rgba(251,191,36,0.07); border: 1px solid rgba(251,191,36,0.2);
            border-radius: 10px; font-size: 0.8rem; color: #fbbf24; line-height: 1.6;
        }
        .step-note strong { display: block; margin-bottom: 2px; font-weight: 800; }

        /* â”€â”€â”€ SIDEBAR VERSIONS â”€â”€â”€ */
        .sidebar { display: flex; flex-direction: column; gap: 1rem; }

        .versions-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(14px);
            animation: fadeUp 0.5s 0.3s ease backwards;
        }
        .versions-title { font-size: 0.88rem; font-weight: 800; color: #fff; margin-bottom: 1rem; }

        .version-list { display: flex; flex-direction: column; gap: 0.6rem; }
        .v-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; border-radius: 10px; cursor: pointer;
            border: 1px solid transparent; transition: all 0.2s;
            text-decoration: none;
        }
        .v-item:hover { background: rgba(255,255,255,0.04); border-color: var(--border); }
        .v-item.active { background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.2); }

        .v-left { display: flex; align-items: center; gap: 8px; }
        .v-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .v-dot.latest { background: var(--primary); box-shadow: 0 0 8px var(--primary); }
        .v-dot.old    { background: #334155; }
        .v-num { font-size: 0.85rem; font-weight: 700; color: #fff; }
        .v-date { font-size: 0.72rem; color: var(--muted); }
        .v-badge-latest {
            font-size: 0.65rem; font-weight: 800; color: var(--primary);
            background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
            padding: 2px 7px; border-radius: 100px;
        }

        /* Req card */
        .req-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(14px);
            animation: fadeUp 0.5s 0.4s ease backwards;
        }
        .req-title { font-size: 0.88rem; font-weight: 800; color: #fff; margin-bottom: 1rem; }
        .req-list { display: flex; flex-direction: column; gap: 0.6rem; }
        .req-item {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.8rem; color: #cbd5e1;
        }
        .req-item i { width: 14px; height: 14px; color: var(--primary); flex-shrink: 0; }

        /* â”€â”€â”€ HISTORY â”€â”€â”€ */
        .history-section { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem 5rem; }
        .history-title { font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem; }

        .history-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        .history-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem; backdrop-filter: blur(12px);
            transition: all 0.2s;
        }
        .history-card:hover { border-color: rgba(255,255,255,0.12); transform: translateY(-2px); }
        .h-version { font-size: 0.72rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .h-title { font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .h-date { font-size: 0.75rem; color: var(--muted); margin-bottom: 1rem; }
        .h-changes { display: flex; flex-direction: column; gap: 4px; }
        .h-change { font-size: 0.78rem; color: #94a3b8; display: flex; align-items: flex-start; gap: 6px; }
        .h-dot { flex-shrink: 0; width: 4px; height: 4px; border-radius: 50%; background: #475569; margin-top: 7px; }

        /* â”€â”€â”€ FOOTER â”€â”€â”€ */
        footer {
            border-top: 1px solid var(--border);
            padding: 2rem; text-align: center;
        }
        .footer-logo { font-size: 0.95rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .footer-logo span { color: var(--primary); }
        .footer-note { font-size: 0.75rem; color: var(--muted); }

        @media (max-width: 900px) {
            .content-grid { grid-template-columns: 1fr; }
            .sidebar { order: -1; }
        }
        @media (max-width: 600px) {
            nav { padding: 0 1rem; }
            .dl-top { flex-direction: column; }
            .history-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <a href="<?php echo base_url(''); ?>" class="nav-logo">
        <div class="nav-logo-icon"><i data-lucide="graduation-cap"></i></div>
        <span class="nav-logo-text">GH<span>-SSS</span></span>
    </a>
    <div class="nav-right">
        <a href="<?php echo base_url(''); ?>" class="btn-back"><i data-lucide="arrow-left"></i> Beranda</a>
        <a href="<?php echo base_url('login'); ?>" class="btn-login"><i data-lucide="log-in"></i> Masuk</a>
    </div>
</nav>

<!-- Page Hero -->
<div class="page-hero">
    <div class="page-tag">Riwayat Pembaruan</div>
    <h1 class="page-title">Changelog & <span>Update</span><br>Aula System System</h1>
    <p class="page-sub">Unduh versi terbaru dan ikuti panduan instalasi untuk memperbarui sistem Anda dengan aman.</p>
</div>

<!-- Download Card -->
<div class="download-wrap">
    <div class="download-card">
        <div class="dl-top">
            <div>
                <div class="dl-badge"><span class="dot"></span> Rilis Terbaru</div>
                <div class="dl-version">v<?php echo htmlspecialchars($latest['version']); ?> â€” <?php echo htmlspecialchars($latest['title']); ?></div>
                <div class="dl-date">Dirilis <?php echo date('d F Y', strtotime($latest['release_date'])); ?></div>
                <div class="dl-meta">
                    <div class="dl-meta-item"><i data-lucide="hard-drive"></i> <?php echo $latest['file_size']; ?></div>
                    <div class="dl-meta-item"><i data-lucide="code-2"></i> PHP <?php echo $latest['min_php']; ?>+</div>
                    <div class="dl-meta-item"><i data-lucide="database"></i> MySQL <?php echo $latest['min_mysql']; ?>+</div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
                <a href="<?php echo $changelog['download_base'] . htmlspecialchars($latest['download_file']); ?>" class="dl-btn" download>
                    <i data-lucide="download-cloud"></i>
                    <div>
                        <div>Download v<?php echo htmlspecialchars($latest['version']); ?></div>
                        <div class="dl-btn-note"><?php echo $latest['file_size']; ?> Â· .zip</div>
                    </div>
                </a>
            </div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:1.5rem;">
            <div style="font-size:0.78rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:1rem;">Perubahan di Versi Ini</div>
            <div class="dl-changes">
                <?php foreach ($latest['changes'] as $c): ?>
                <div class="change-item">
                    <span class="change-pill pill-<?php echo $c['type']; ?>"><?php echo $c['type']; ?></span>
                    <span><?php echo htmlspecialchars($c['text']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- 2-Col: Guide + Sidebar -->
<div class="content-grid">

    <!-- INSTALL GUIDE -->
    <div class="guide-card">
        <div class="guide-header">
            <div class="guide-title">ðŸ“¦ Panduan Update Sistem</div>
            <div class="guide-sub">Ikuti langkah-langkah berikut secara berurutan untuk memperbarui Aula System dengan aman.</div>
        </div>

        <div class="steps-list">
            <?php foreach ($steps as $step): ?>
            <div class="step-item">
                <div class="step-num" style="background: <?php echo $step['color']; ?>22; border: 2px solid <?php echo $step['color']; ?>55; color: <?php echo $step['color']; ?>">
                    <?php echo $step['step']; ?>
                </div>
                <div class="step-body">
                    <div class="step-title"><?php echo htmlspecialchars($step['title']); ?></div>
                    <div class="step-desc"><?php echo htmlspecialchars($step['desc']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="step-note">
            <strong>âš ï¸ Penting Sebelum Update</strong>
            Selalu lakukan backup database dan file sistem terlebih dahulu. Jika terjadi error setelah update, Anda bisa memulihkan data dari backup. Hubungi developer jika memerlukan bantuan.
        </div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <!-- Version list -->
        <div class="versions-card">
            <div class="versions-title">Semua Versi</div>
            <div class="version-list">
                <?php foreach ($versions as $v): ?>
                <a href="#v<?php echo str_replace('.', '-', $v['version']); ?>" class="v-item <?php echo $v['is_latest'] ? 'active' : ''; ?>">
                    <div class="v-left">
                        <div class="v-dot <?php echo $v['is_latest'] ? 'latest' : 'old'; ?>"></div>
                        <div>
                            <div class="v-num">v<?php echo htmlspecialchars($v['version']); ?></div>
                            <div class="v-date"><?php echo date('d M Y', strtotime($v['release_date'])); ?></div>
                        </div>
                    </div>
                    <?php if ($v['is_latest']): ?>
                    <span class="v-badge-latest">Terbaru</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sys Requirements -->
        <div class="req-card">
            <div class="req-title">Persyaratan Sistem</div>
            <div class="req-list">
                <div class="req-item"><i data-lucide="check-circle-2"></i> PHP <?php echo $latest['min_php']; ?>+ (Rekomendasi 8.x)</div>
                <div class="req-item"><i data-lucide="check-circle-2"></i> MySQL / MariaDB <?php echo $latest['min_mysql']; ?>+</div>
                <div class="req-item"><i data-lucide="check-circle-2"></i> Apache 2.4+ dengan mod_rewrite</div>
                <div class="req-item"><i data-lucide="check-circle-2"></i> Ekstensi: mysqli, gd, zip, mbstring</div>
                <div class="req-item"><i data-lucide="check-circle-2"></i> Minimal RAM: 512 MB</div>
                <div class="req-item"><i data-lucide="check-circle-2"></i> Storage: 1 GB+ (untuk upload)</div>
            </div>
        </div>
    </div>
</div>

<!-- History -->
<div class="history-section">
    <div class="history-title">Riwayat Semua Versi</div>
    <div class="history-grid">
        <?php foreach ($versions as $v): if ($v['is_latest']) continue; ?>
        <div class="history-card" id="v<?php echo str_replace('.', '-', $v['version']); ?>">
            <div class="h-version">v<?php echo htmlspecialchars($v['version']); ?></div>
            <div class="h-title"><?php echo htmlspecialchars($v['title']); ?></div>
            <div class="h-date"><i data-lucide="calendar" style="width:11px;height:11px;display:inline;"></i> <?php echo date('d F Y', strtotime($v['release_date'])); ?></div>
            <div class="h-changes">
                <?php foreach (array_slice($v['changes'], 0, 4) as $c): ?>
                <div class="h-change"><span class="h-dot"></span><?php echo htmlspecialchars($c['text']); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-logo">GH<span>Smart</span>SchoolSystem</div>
    <div class="footer-note">Â© <?php echo date('Y'); ?> Aula System Platform Â· Tim Developer GH</div>
</footer>

<script>lucide.createIcons();</script>
</body>
</html>


