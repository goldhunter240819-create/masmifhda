<style>
    /* Menggunakan tema Zamrud (Green) untuk modul Absensi */
    .z-avatar-premium { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .header-breadcrumb .parent { color: #0ea5e9; }
    
    .welcome-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }
    .welcome-hero::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(255,255,255,0.15) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(255,255,255,0.1) 0%, transparent 40%),
            url("data:image/svg+xml,%3Csvg width='24' height='24' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='white' fill-opacity='0.15'/%3E%3C/svg%3E");
        z-index: 1;
    }
    .hero-badge { background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.3); }
    .hero-btn.primary { background: white; color: #0ea5e9; box-shadow: 0 10px 20px rgba(14,165,233,0.2); }
    .hero-bg-icon { color: white; opacity: 0.05; z-index: 1; }
    
    .teal .stat-icon { background: #f0fdf4; color: #0ea5e9; }
    
    /* Progress Circle */
    .progress-circle {
        width: 80px; height: 80px; border-radius: 50%;
        background: conic-gradient(#0ea5e9 calc(var(--p) * 1%), #e2e8f0 0);
        display: flex; align-items: center; justify-content: center; position: relative;
    }
    .progress-circle::after {
        content: ''; width: 68px; height: 68px; border-radius: 50%;
        background: white; position: absolute;
    }
    .progress-val { position: relative; z-index: 1; font-weight: 800; font-size: 1.2rem; color: #0f172a; }
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
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;">ABSEN GURU V2</span>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Dashboard Central</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:2rem;">
                <div class="header-status-pill" style="display:flex;align-items:center;gap:8px;background:#f0fdf4;padding:6px 14px;border-radius:100px;border:1px solid #dcfce7;font-size:0.75rem;font-weight:700;color:#15803d;">
                    <div class="dot pulse" style="width:8px;height:8px;background:#22c55e;border-radius:50%;"></div>
                    <span>Scanner Online</span>
                </div>
                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(16,185,129,0.2);"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name" style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                        <div class="role" style="font-size:0.75rem;color:#64748b;">Super Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="z-scroll">
            <!-- Welcome Hero -->
            <div class="welcome-hero" style="margin: 1.5rem 1rem; border-radius: 20px; padding: 2rem 2.5rem; position: relative; overflow: hidden; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div class="hero-content" style="position:relative; z-index:2; max-width:550px;">
                    <div class="hero-badge" style="padding:4px 12px; border-radius:100px; font-weight:800; font-size:0.7rem; display:inline-block; margin-bottom:.8rem; border-width:1px; border-style:solid;">ABSENSI ENGINE V2</div>
                    <h1 style="font-size:1.8rem; font-weight:800; margin:0 0 .5rem; letter-spacing:-0.5px; line-height:1.15;">Pusat Kontrol Absensi <span class="wave">ðŸŽ¯</span></h1>
                    <p style="font-size:.92rem; opacity:0.8; line-height:1.5; margin-bottom:1.5rem;">Pantau kehadiran Guru secara real-time. Modul V2 hadir dengan deteksi QR yang lebih cepat dan sinkronisasi otomatis ke dashboard Wali Kelas SIAKAD.</p>
                    <div class="hero-actions" style="display:flex; gap:.75rem;">
                        <!-- Scanner button removed since scanning is done via mobile -->
                    </div>
                </div>
                <div class="hero-bg-icon" style="position:absolute; right:-20px; bottom:-20px; font-size:140px; transform:rotate(-10deg);">
                    <i data-lucide="qr-code" style="width:180px;height:180px;"></i>
                </div>
            </div>

            <!-- Stats Grid -->
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; padding:0 1rem 1.5rem;">
                <div class="stat-card" style="background:white; padding:1.2rem; border-radius:16px; display:flex; align-items:center; gap:1rem; border:1px solid #f1f5f9;">
                    <div class="stat-icon teal" style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f0fdf4;color:#0ea5e9;">
                        <i data-lucide="users"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;font-weight:700;color:#64748b;margin-bottom:2px;">Total Guru (Sistem)</div>
                        <div style="font-size:1.25rem;font-weight:800;color:#0f172a;"><?php echo number_format($totalGuru); ?></div>
                    </div>
                </div>
                
                <div class="stat-card" style="background:white; padding:1.2rem; border-radius:16px; display:flex; align-items:center; gap:1rem; border:1px solid #f1f5f9;">
                    <div class="stat-icon" style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f0f9ff;color:#0ea5e9;">
                        <i data-lucide="user-check"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;font-weight:700;color:#64748b;margin-bottom:2px;">Hadir Hari Ini</div>
                        <div style="font-size:1.25rem;font-weight:800;color:#0f172a;"><?php echo number_format($hadir); ?></div>
                    </div>
                </div>

                <div class="stat-card" style="background:white; padding:1.2rem; border-radius:16px; display:flex; align-items:center; gap:1rem; border:1px solid #f1f5f9;">
                    <div class="stat-icon" style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#eff6ff;color:#3b82f6;">
                        <i data-lucide="thermometer"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;font-weight:700;color:#64748b;margin-bottom:2px;">Sakit / Izin</div>
                        <div style="font-size:1.25rem;font-weight:800;color:#0f172a;"><?php echo number_format($sakit + $izin); ?></div>
                    </div>
                </div>

                <div class="stat-card" style="background:white; padding:1.2rem; border-radius:16px; display:flex; align-items:center; gap:1rem; border:1px solid #f1f5f9;">
                    <div class="stat-icon" style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#fef2f2;color:#ef4444;">
                        <i data-lucide="user-x"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;font-weight:700;color:#64748b;margin-bottom:2px;">Alpa (Tanpa Ket.)</div>
                        <div style="font-size:1.25rem;font-weight:800;color:#0f172a;"><?php echo number_format($alpa); ?></div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:1.25rem; padding:0 2rem 2.5rem;">
                
                <!-- Quick Actions Panel -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9;">
                    <div style="margin-bottom:1.5rem;">
                        <h3 style="margin:0; font-size:.95rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="zap" style="color:#0ea5e9;"></i> Fitur Cepat Absensi</h3>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:1rem;">
                        <a href="<?php echo base_url('absen_guru_v2/scanner'); ?>" target="_blank" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#f0fdf4';this.style.borderColor='#bbf7d0'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#0ea5e9;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="focus"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Mesin Scanner</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Jalankan kamera untuk proses scan otomatis.</div>
                            </div>
                        </a>
                        
                        <a href="<?php echo base_url('admin/absen_guru_v2/rekap-harian'); ?>" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#eff6ff';this.style.borderColor='#dbeafe'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#3b82f6;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="calendar-check"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Rekap Harian</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Lihat detail siapa saja yang absen hari ini.</div>
                            </div>
                        </a>
                        
                        <a href="<?php echo base_url('admin/siakad/guru/cetak-kartu'); ?>" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#fef3c7';this.style.borderColor='#fde68a'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#d97706;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="printer"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Cetak ID Card QR</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Generate kartu guru dengan sistem barcode V2.</div>
                            </div>
                        </a>

                        <a href="<?php echo base_url('admin/absen_guru_v2/pengaturan'); ?>" style="text-decoration:none; display:flex; gap:15px; padding:1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; transition:0.2s;" onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#e5e7eb'" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'">
                            <div style="width:48px;height:48px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;color:#475569;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);flex-shrink:0;">
                                <i data-lucide="settings"></i>
                            </div>
                            <div>
                                <div style="font-size:0.9rem;font-weight:800;color:#0f172a;margin-bottom:4px;">Pengaturan Jam</div>
                                <div style="font-size:0.75rem;color:#64748b;line-height:1.4;">Tentukan batas terlambat & jam pulang.</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Log / Live Stream Panel -->
                <div style="background:white; border-radius:20px; padding:1.5rem; border:1px solid #f1f5f9;">
                    <div style="margin-bottom:1.5rem;">
                        <h3 style="margin:0; font-size:.95rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="activity" style="color:#3b82f6;"></i> Log Aktivitas Terakhir</h3>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <?php foreach($activities as $act): ?>
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#f8fafc;color:#0ea5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i data-lucide="<?php echo $act['icon']; ?>" style="width:16px;height:16px;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:700;color:#0f172a;"><?php echo $act['text']; ?></div>
                                <div style="font-size:0.75rem;color:#64748b;margin-top:2px;"><?php echo $act['sub']; ?> &bull; Baru saja</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div style="margin-top:2rem; text-align:center; padding:1.5rem; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px;">
                            <i data-lucide="scan" style="width:32px;height:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                            <div style="font-size:0.8rem;font-weight:700;color:#64748b;">Aktivitas scan real-time akan muncul di sini saat scanner dijalankan.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

