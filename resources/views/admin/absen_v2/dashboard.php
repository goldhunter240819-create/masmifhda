<style>
    /* Menggunakan tema Zamrud standar (Blue) */
    .z-avatar-premium { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .header-breadcrumb .parent { color: #0ea5e9; }
    
    .absen-dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        padding: 0 1rem 1.5rem;
    }
    
    /* ─── Hero Banner ─── */
    .welcome-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        margin: 0 1rem 1.5rem; border-radius: 20px; padding: 2rem 2.5rem; position: relative;
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
    .hero-bg-icon { position: absolute; right: -30px; bottom: -30px; font-size: 120px; opacity: 0.05; transform: rotate(-15deg); z-index: 1; }
    .hero-content { position: relative; z-index: 2; max-width: 550px; }
    .hero-badge { background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 100px; font-weight: 800; font-size: 0.7rem; display: inline-block; margin-bottom: .8rem; border: 1px solid rgba(255,255,255,0.3); }
    .hero-content h1 { font-size: 1.8rem; font-weight: 800; margin: 0 0 .5rem; letter-spacing: -0.5px; line-height: 1.15; }
    .hero-content p { font-size: .92rem; opacity: 0.7; line-height: 1.5; margin-bottom: 0; }
    
    .hero-stats-mini { display: flex; gap: 1.5rem; z-index: 2; }
    .mini-stat { text-align: right; border-right: 2px solid rgba(255,255,255,0.1); padding-right: 1.5rem; }
    .mini-stat:last-child { border: none; padding: 0; }
    .mini-stat .val { font-size: 1.5rem; font-weight: 800; color: white; }
    .mini-stat .lab { font-size: 0.7rem; font-weight: 700; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px; }
    
    .stat-main-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .stat-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 1.05rem;
        color: #0ea5e9;
        margin-bottom: 2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-card-title i {
        color: #0ea5e9;
    }

    .stat-card-title.guru {
        color: #3b82f6;
    }
    
    .stat-card-title.guru i {
        color: #3b82f6;
    }

    /* Donut Chart CSS */
    .donut-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .donut-chart {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .donut-chart::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: #ffffff;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .donut-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        text-align: center;
        width: 100%;
    }
    
    .donut-number {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .donut-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 1px;
    }

    /* Stat Grid */
    .stat-grid-6 {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.75rem;
    }

    .stat-box {
        padding: 0.75rem 0.5rem;
        border-radius: 12px;
        text-align: center;
        border: 1px solid transparent;
    }
    
    .stat-box .box-num {
        font-size: 1.25rem;
        font-weight: 900;
        margin-bottom: 3px;
    }
    
    .stat-box .box-lbl {
        font-size: 0.6rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Colors */
    .box-hadir { background: #f0fdf4; border-color: #dcfce7; }
    .box-hadir .box-num { color: #10b981; }
    
    .box-belum { background: #fef9c3; border-color: #fef08a; }
    .box-belum .box-num { color: #eab308; }
    
    .box-sakit { background: #eff6ff; border-color: #dbeafe; }
    .box-sakit .box-num { color: #3b82f6; }
    
    .box-izin { background: #faf5ff; border-color: #f3e8ff; }
    .box-izin .box-num { color: #a855f7; }
    
    .box-alpa { background: #fef2f2; border-color: #fee2e2; }
    .box-alpa .box-num { color: #ef4444; }
    
    .box-bolos { background: #f8fafc; border-color: #f1f5f9; }
    .box-bolos .box-num { color: #64748b; }

    /* Bottom Section */
    .bottom-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        padding: 0 1rem 2rem;
    }
    
    .section-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.5rem 2rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }
    
    .section-title i {
        color: #10b981;
    }
    .section-title.blue i { color: #3b82f6; }

    @media (max-width: 1024px) {
        .absen-dashboard-grid, .bottom-section { grid-template-columns: 1fr; }
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN V2</span>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Dashboard Central</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
                <div class="header-status-pill" style="display:flex;align-items:center;gap:8px;background:#f0fdf4;padding:6px 14px;border-radius:100px;border:1px solid #dcfce7;font-size:0.75rem;font-weight:700;color:#15803d;">
                    <div class="dot pulse" style="width:8px;height:8px;background:#22c55e;border-radius:50%;"></div>
                    <span style="white-space: nowrap;">Scanner Online</span>
                </div>
                
                <!-- PORTAL HUB BUTTON -->
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>

                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-user-info" style="text-align: right;">
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo $nama; ?></div>
                        <div class="role" style="font-size:0.7rem;color:#64748b;">Administrator</div>
                    </div>
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(16,185,129,0.2);"><?php echo $initial; ?></div>
                </div>
                
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding-top: 1.5rem;">
            
            <!-- Welcome Hero -->
            <div class="welcome-hero">
                <div class="hero-bg-icon">
                    <i data-lucide="qr-code"></i>
                </div>
                <div class="hero-content">
                    <div class="hero-badge">ABSENSI DIGITAL v2.0</div>
                    <h1>Selamat Datang, <?php echo $nama; ?>! <span class="wave">👋</span></h1>
                    <p>Pantau rekap kehadiran siswa dan guru secara real-time. Kelola presensi dan jam kerja dengan mudah dan efisien.</p>
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
            </div>

            <div class="absen-dashboard-grid">
                <?php
                function getConicGradient($hadir, $belum, $sakit, $izin, $alpa, $bolos, $total) {
                    if ($total == 0) return "#f1f5f9 100%";
                    
                    $p_hadir = ($hadir / $total) * 100;
                    $p_belum = ($belum / $total) * 100;
                    $p_sakit = ($sakit / $total) * 100;
                    $p_izin  = ($izin / $total) * 100;
                    $p_alpa  = ($alpa / $total) * 100;
                    $p_bolos = ($bolos / $total) * 100;

                    $curr = 0;
                    $segments = [];
                    
                    if ($p_hadir > 0) { $segments[] = "#10b981 $curr% " . ($curr + $p_hadir) . "%"; $curr += $p_hadir; }
                    if ($p_belum > 0) { $segments[] = "#eab308 $curr% " . ($curr + $p_belum) . "%"; $curr += $p_belum; }
                    if ($p_sakit > 0) { $segments[] = "#3b82f6 $curr% " . ($curr + $p_sakit) . "%"; $curr += $p_sakit; }
                    if ($p_izin > 0)  { $segments[] = "#a855f7 $curr% " . ($curr + $p_izin) . "%";  $curr += $p_izin;  }
                    if ($p_alpa > 0)  { $segments[] = "#ef4444 $curr% " . ($curr + $p_alpa) . "%";  $curr += $p_alpa;  }
                    if ($p_bolos > 0) { $segments[] = "#64748b $curr% " . ($curr + $p_bolos) . "%"; $curr += $p_bolos; }

                    return empty($segments) ? "#f1f5f9 100%" : implode(", ", $segments);
                }
                ?>
                <!-- KARTU SISWA -->
                <div class="stat-main-card">
                    <div class="stat-card-title">
                        <i data-lucide="pie-chart"></i> STATISTIK KEHADIRAN SISWA
                    </div>
                    
                    <div class="donut-wrapper">
                        <?php 
                        $belum_absen_siswa = $totalSiswa - $totalAbsen; 
                        if ($belum_absen_siswa < 0) $belum_absen_siswa = 0;
                        $bgSiswa = getConicGradient($hadir, $belum_absen_siswa, $sakit, $izin, $alpa, $bolos, $totalSiswa);
                        ?>
                        <div class="donut-chart" style="background: conic-gradient(<?php echo $bgSiswa; ?>);"></div>
                        <div class="donut-content">
                            <div class="donut-number"><?php echo number_format($totalSiswa); ?></div>
                            <div class="donut-label">TOTAL SISWA</div>
                        </div>
                    </div>
                    
                    <div class="stat-grid-6">
                        <div class="stat-box box-hadir">
                            <div class="box-num"><?php echo number_format($hadir); ?></div>
                            <div class="box-lbl">HADIR</div>
                        </div>
                        <div class="stat-box box-belum">
                            <div class="box-num"><?php echo number_format($belum_absen_siswa); ?></div>
                            <div class="box-lbl">BELUM ABSEN</div>
                        </div>
                        <div class="stat-box box-sakit">
                            <div class="box-num"><?php echo number_format($sakit); ?></div>
                            <div class="box-lbl">SAKIT</div>
                        </div>
                        <div class="stat-box box-izin">
                            <div class="box-num"><?php echo number_format($izin); ?></div>
                            <div class="box-lbl">IZIN</div>
                        </div>
                        <div class="stat-box box-alpa">
                            <div class="box-num"><?php echo number_format($alpa); ?></div>
                            <div class="box-lbl">ALPA</div>
                        </div>
                        <div class="stat-box box-bolos">
                            <div class="box-num"><?php echo number_format($bolos); ?></div>
                            <div class="box-lbl">BOLOS</div>
                        </div>
                    </div>
                </div>
                
                <!-- KARTU GURU -->
                <div class="stat-main-card">
                    <div class="stat-card-title guru">
                        <i data-lucide="pie-chart"></i> STATISTIK KEHADIRAN GURU
                    </div>
                    
                    <div class="donut-wrapper">
                        <?php 
                        $belum_absen_guru = $totalGuru - $totalAbsen_guru; 
                        if ($belum_absen_guru < 0) $belum_absen_guru = 0;
                        $bgGuru = getConicGradient($hadir_guru, $belum_absen_guru, $sakit_guru, $izin_guru, $alpa_guru, $bolos_guru, $totalGuru);
                        ?>
                        <div class="donut-chart" style="background: conic-gradient(<?php echo $bgGuru; ?>);"></div>
                        <div class="donut-content">
                            <div class="donut-number"><?php echo number_format($totalGuru); ?></div>
                            <div class="donut-label">TOTAL GURU</div>
                        </div>
                    </div>
                    
                    <div class="stat-grid-6">
                        <div class="stat-box box-hadir">
                            <div class="box-num"><?php echo number_format($hadir_guru); ?></div>
                            <div class="box-lbl">HADIR</div>
                        </div>
                        <div class="stat-box box-belum">
                            <div class="box-num"><?php echo number_format($belum_absen_guru); ?></div>
                            <div class="box-lbl">BELUM ABSEN</div>
                        </div>
                        <div class="stat-box box-sakit">
                            <div class="box-num"><?php echo number_format($sakit_guru); ?></div>
                            <div class="box-lbl">SAKIT</div>
                        </div>
                        <div class="stat-box box-izin">
                            <div class="box-num"><?php echo number_format($izin_guru); ?></div>
                            <div class="box-lbl">IZIN</div>
                        </div>
                        <div class="stat-box box-alpa">
                            <div class="box-num"><?php echo number_format($alpa_guru); ?></div>
                            <div class="box-lbl">ALPA</div>
                        </div>
                        <div class="stat-box box-bolos">
                            <div class="box-num"><?php echo number_format($bolos_guru); ?></div>
                            <div class="box-lbl">BOLOS</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-section">
                <!-- Fitur Cepat Absensi -->
                <div class="section-card">
                    <div class="section-title">
                        <i data-lucide="zap"></i> Fitur Cepat Absensi
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <a href="<?php echo base_url('admin/absen_v2/rekap-harian'); ?>" style="display:flex; align-items:center; gap:15px; padding:15px; border-radius:12px; border:1px solid #f1f5f9; text-decoration:none; color:#1e293b; transition:0.2s;">
                            <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;color:#10b981;display:flex;align-items:center;justify-content:center;">
                                <i data-lucide="calendar-check"></i>
                            </div>
                            <div>
                                <div style="font-weight:800; font-size:0.95rem;">Rekap Kehadiran Harian</div>
                                <div style="font-size:0.8rem; color:#64748b;">Lihat detail siapa saja yang hadir hari ini.</div>
                            </div>
                        </a>
                        <a href="<?php echo base_url('admin/absen_v2/ketidakhadiran'); ?>" style="display:flex; align-items:center; gap:15px; padding:15px; border-radius:12px; border:1px solid #f1f5f9; text-decoration:none; color:#1e293b; transition:0.2s;">
                            <div style="width:40px;height:40px;border-radius:10px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div>
                                <div style="font-weight:800; font-size:0.95rem;">Data Ketidakhadiran</div>
                                <div style="font-size:0.8rem; color:#64748b;">Daftar siswa dan guru yang tidak hadir (Sakit/Izin/Alpa).</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Log Aktivitas Terakhir -->
                <div class="section-card">
                    <div class="section-title blue">
                        <i data-lucide="activity"></i> Log Aktivitas Terakhir
                    </div>
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <?php if (empty($activities)): ?>
                            <div style="text-align:center; color:#94a3b8; font-size:0.85rem; padding: 2rem 0;">Belum ada aktivitas hari ini.</div>
                        <?php else: ?>
                            <?php foreach ($activities as $act): ?>
                            <div style="display:flex; align-items:flex-start; gap:15px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i data-lucide="<?php echo $act['icon']; ?>" style="width:18px;height:18px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:800; font-size:0.9rem; color:#1e293b; margin-bottom:2px;"><?php echo $act['text']; ?></div>
                                    <div style="font-size:0.8rem; color:#64748b;"><?php echo $act['sub']; ?> &bull; <?php echo date('H:i', strtotime($act['time'])); ?></div>
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
