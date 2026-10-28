<style>
    /* â”€â”€â”€ Premium Header â”€â”€â”€ */
    .z-header {
        height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8);
        backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 100;
    }
    .header-breadcrumb { display: flex; align-items: center; gap: 10px; }
    .header-breadcrumb .parent { font-weight: 800; color: #0ea5e9; letter-spacing: 1px; font-size: 0.8rem; }
    .header-breadcrumb .sep { width: 14px; color: #cbd5e1; }
    .header-breadcrumb .current { font-weight: 700; color: #1e293b; }

    .z-header-right { display: flex; align-items: center; gap: 2rem; }
    .header-status-pill { 
        display: flex; align-items: center; gap: 8px; background: #f0fdf4;
        padding: 6px 14px; border-radius: 100px; border: 1px solid #dcfce7;
        font-size: 0.75rem; font-weight: 700; color: #15803d;
    }
    .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; }
    .dot.pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }

    .z-avatar-premium {
        width: 36px; height: 36px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-weight: 800; box-shadow: 0 4px 12px rgba(14,165,233,0.2);
    }

    /* ─── Hero Banner ─── */
    .welcome-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        margin: 1rem 2rem; border-radius: 20px; padding: 2rem 2.5rem; position: relative;
        overflow: hidden; color: white; display: flex; justify-content: space-between; align-items: center;
    }
    .welcome-hero::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(255,255,255,0.15) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(255,255,255,0.1) 0%, transparent 40%),
            url("data:image/svg+xml,%3Csvg width='24' height='24' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='white' fill-opacity='0.15'/%3E%3C/svg%3E");
        z-index: 1;
    }
    .hero-bg-icon { position: absolute; right: -30px; bottom: -30px; font-size: 120px; opacity: 0.05; transform: rotate(-15deg); z-index: 1; pointer-events: none; }
    .hero-bg-icon i, .hero-bg-icon svg { width: 120px !important; height: 120px !important; }
    .hero-content { position: relative; z-index: 2; max-width: 550px; }
    .hero-badge { background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 100px; font-weight: 800; font-size: 0.7rem; display: inline-block; margin-bottom: .8rem; border: 1px solid rgba(255,255,255,0.3); }
    .hero-content h1 { font-size: 1.8rem; font-weight: 800; margin: 0 0 .5rem; letter-spacing: -0.5px; line-height: 1.15; }
    .hero-content p { font-size: .92rem; opacity: 0.7; line-height: 1.5; margin-bottom: 1.2rem; }
    .hero-actions { display: flex; gap: .75rem; }
    .hero-btn { text-decoration: none; padding: .6rem 1.25rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; font-size: .85rem !important; transition: 0.3s; }
    .hero-btn i, .hero-btn svg { width: 16px !important; height: 16px !important; }
    .hero-btn.primary { background: #0ea5e9; color: white; box-shadow: 0 10px 20px rgba(14,165,233,0.2); }
    .hero-btn.secondary { background: rgba(255,255,255,0.1); color: white; backdrop-filter: blur(10px); }
    .hero-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(0,0,0,0.2); }

    .hero-stats-mini { display: flex; gap: 1.5rem; z-index: 2; }
    .mini-stat { text-align: right; border-right: 2px solid rgba(255,255,255,0.1); padding-right: 1.5rem; }
    .mini-stat:last-child { border: none; padding: 0; }
    .mini-stat .val { font-size: 1.5rem; font-weight: 800; color: white; }
    .mini-stat .lab { font-size: 0.7rem; font-weight: 700; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px; }

    /* ─── Stats Cards ─── */
    .z-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 0 2rem 1.5rem; }
    .stat-card { background: white; padding: 1.2rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem; border: 1px solid #f1f5f9; position: relative; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .stat-icon i, .stat-icon svg, .stat-icon .lucide { width: 20px !important; height: 20px !important; max-width: 20px !important; max-height: 20px !important; }
    .stat-info .label { font-size: 0.72rem; font-weight: 700; color: #64748b; margin-bottom: 2px; }
    .stat-info .value { font-size: 1.25rem; font-weight: 800; color: #0f172a; }
    .stat-info .sub { font-size: 0.68rem; color: #94a3b8; }

    .green .stat-icon { background: #f0fdf4; color: #0ea5e9; }
    .blue .stat-icon { background: #eff6ff; color: #3b82f6; }
    .purple .stat-icon { background: #faf5ff; color: #a855f7; }
    .sky .stat-icon { background: #f0f9ff; color: #0284c7; }
    .amber .stat-icon { background: #fffbeb; color: #f59e0b; }

    .stat-chart-mini { position: absolute; right: 1.2rem; bottom: 1.2rem; display: flex; gap: 3px; align-items: flex-end; height: 16px; }
    .stat-chart-mini .bar { width: 3px; background: #0ea5e9; border-radius: 2px; opacity: 0.3; }

    /* ─── Dashboard Content ─── */
    .z-dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem; padding: 0 2rem 2.5rem; }
    .z-panel-modern { background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid #f1f5f9; }
    .panel-header { margin-bottom: 1.2rem; }
    .panel-header h3 { margin: 0; font-size: .95rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 6px; }
    .panel-header h3 i, .panel-header h3 svg { width: 16px !important; height: 16px !important; }
    .panel-header p { margin: 2px 0 0; font-size: 0.8rem; color: #94a3b8; }

    .quick-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; }
    .quick-item {
        text-decoration: none; padding: 1rem .8rem; border-radius: 14px; border: 1px solid #f1f5f9;
        display: flex; flex-direction: column; align-items: center; gap: 8px; transition: 0.2s;
    }
    .quick-item span { font-weight: 700; font-size: .78rem !important; color: #475569; }
    .quick-item:hover { background: #f8fafc; border-color: #e2e8f0; transform: scale(1.02); }
    .quick-item.special { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; }
    .quick-item.special span { color: white; }

    .icon-box { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .icon-box i, .icon-box svg, .icon-box .lucide { width: 18px !important; height: 18px !important; max-width: 18px !important; max-height: 18px !important; }
    .s-green { background: #f0fdf4; color: #0ea5e9; }
    .s-blue { background: #eff6ff; color: #3b82f6; }
    .s-amber { background: #fffbeb; color: #f59e0b; }
    .s-purple { background: #faf5ff; color: #a855f7; }
    .s-sky { background: #f0f9ff; color: #0284c7; }
    .s-white { background: rgba(255,255,255,0.2); color: white; }

    .activity-list { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.25rem; }
    .activity-item { display: flex; gap: .75rem; align-items: flex-start; }
    .act-icon { width: 34px; height: 34px; border-radius: 50%; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .act-icon i, .act-icon svg, .act-icon .lucide { width: 16px !important; height: 16px !important; max-width: 16px !important; max-height: 16px !important; }
    .act-text { display: flex; flex-direction: column; }
    .act-text strong { font-size: .82rem !important; color: #1e293b; }
    .act-text span { font-size: 0.7rem !important; color: #94a3b8; }
    .view-all { text-decoration: none; font-size: 0.8rem !important; font-weight: 800; color: #0ea5e9; display: flex; align-items: center; gap: 6px; }
</style>
<?php
// data variables: $totalSiswa, $totalGuru, $totalKelas, $totalMapel, $nama, $initial, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb">
                    <span class="parent">SIAKAD</span>
                    <i data-lucide="chevron-right" class="sep"></i>
                    <span class="current">Dashboard</span>
                </div>
            </div>
            <div class="z-header-right">
                <div class="header-status-pill">
                    <div class="dot pulse"></div>
                    <span>System Online</span>
                </div>
                <!-- PORTAL HUB BUTTON -->
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>
                <div class="z-header-user" onclick="toggleUserDropdown()">
                    <div class="z-avatar-premium"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name"><?php echo $nama; ?></div>
                        <div class="role">Super Administrator</div>
                    </div>
                </div>
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll">
            <!-- Welcome Hero -->
            <div class="welcome-hero">
                <div class="hero-content">
                    <div class="hero-badge">SIAKAD MA MIFTAHUL HUDA v2.0</div>
                    <h1>Selamat Datang, <?php echo $nama; ?>! <span class="wave">👋</span></h1>
                    <p>Fokus Akademik. Kelola data siswa, rombongan belajar, presensi, hingga penilaian secara efisien dalam satu pintu.</p>
                </div>
                <div class="hero-stats-mini">
                    <div class="mini-stat">
                        <div class="val"><?php echo date('H:i'); ?></div>
                        <div class="lab">Waktu Server</div>
                    </div>
                    <div class="mini-stat">
                        <div class="val"><?php echo date('d'); ?></div>
                        <div class="lab"><?php echo date('M Y'); ?></div>
                    </div>
                </div>
                <div class="hero-bg-icon">
                    <i data-lucide="graduation-cap"></i>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="z-stats-grid">
                <div class="stat-card green">
                    <div class="stat-icon"><i data-lucide="users"></i></div>
                    <div class="stat-info">
                        <div class="label">Peserta Didik</div>
                        <div class="value"><?php echo number_format($totalSiswa); ?></div>
                        <div class="sub">Siswa Terdaftar</div>
                    </div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-icon"><i data-lucide="user-check"></i></div>
                    <div class="stat-info">
                        <div class="label">Tenaga Pendidik</div>
                        <div class="value"><?php echo number_format($totalGuru); ?></div>
                        <div class="sub">Guru & Staf</div>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"><i data-lucide="layers"></i></div>
                    <div class="stat-info">
                        <div class="label">Rombongan Belajar</div>
                        <div class="value"><?php echo number_format($totalKelas); ?></div>
                        <div class="sub">Kelas Aktif</div>
                    </div>
                </div>
                <div class="stat-card sky">
                    <div class="stat-icon" style="background:#f0f9ff;color:#0284c7;"><i data-lucide="check-square"></i></div>
                    <div class="stat-info">
                        <div class="label">Presensi Hari Ini</div>
                        <div class="value"><?php echo $absensiPersen; ?>%</div>
                        <div class="sub"><?php echo $hadir; ?> Siswa Hadir</div>
                    </div>
                    <div class="stat-chart-mini">
                        <div class="bar" style="height: <?php echo $absensiPersen; ?>%; opacity:1;"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Panels -->
            <div class="z-dashboard-grid">
                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="zap"></i> Menu Navigasi Cepat</h3>
                        <p>Akses fitur akademik utama</p>
                    </div>
                    <div class="quick-grid">
                        <a href="<?php echo base_url('siakad/siswa'); ?>" class="quick-item">
                            <div class="icon-box s-green"><i data-lucide="users"></i></div>
                            <span>Data Siswa</span>
                        </a>
                        <a href="<?php echo base_url('siakad/guru'); ?>" class="quick-item">
                            <div class="icon-box s-blue"><i data-lucide="user-plus"></i></div>
                            <span>Data Guru</span>
                        </a>
                        <a href="<?php echo base_url('siakad/nilai'); ?>" class="quick-item">
                            <div class="icon-box s-amber"><i data-lucide="edit-3"></i></div>
                            <span>Input Nilai</span>
                        </a>
                        <a href="<?php echo base_url('siakad/raport'); ?>" class="quick-item">
                            <div class="icon-box s-purple"><i data-lucide="file-text"></i></div>
                            <span>Cetak Raport</span>
                        </a>
                        <a href="<?php echo base_url('siakad/kelas'); ?>" class="quick-item">
                            <div class="icon-box s-sky"><i data-lucide="layout"></i></div>
                            <span>Data Kelas</span>
                        </a>
                        <a href="<?php echo base_url('exam'); ?>" class="quick-item special">
                            <div class="icon-box s-white"><i data-lucide="monitor"></i></div>
                            <span>Portal CBT</span>
                        </a>
                        <a href="<?php echo base_url('admin/pip/dashboard'); ?>" class="quick-item">
                            <div class="icon-box s-green" style="background:#f0fdf4; color:#10b981;"><i data-lucide="award"></i></div>
                            <span>Data PIP</span>
                        </a>
                    </div>
                </div>

                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="activity"></i> Aktivitas Akademik</h3>
                        <p>Pembaruan data siswa & presensi terbaru</p>
                    </div>
                    <div class="activity-list">
                        <?php if(empty($activities)): ?>
                            <div style="text-align:center; padding:20px; color:#94a3b8;">Belum ada aktivitas akademik terekam.</div>
                        <?php else: ?>
                            <?php foreach($activities as $act): ?>
                            <div class="activity-item">
                                <div class="act-icon"><i data-lucide="<?php echo $act['icon']; ?>"></i></div>
                                <div class="act-text">
                                    <strong><?php echo $act['text']; ?></strong>
                                    <span><?php echo $act['sub']; ?> &bull; <?php 
                                        $time = strtotime($act['time']);
                                        $diff = time() - $time;
                                        if($diff < 60) echo "Baru saja";
                                        else if($diff < 3600) echo floor($diff/60) . " mnt yang lalu";
                                        else if($diff < 86400) echo floor($diff/3600) . " jam yang lalu";
                                        else echo date('d/m/Y H:i', $time);
                                    ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>lucide.createIcons();</script>


