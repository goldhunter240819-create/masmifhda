<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;">ABSEN GURU V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Harian</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>

                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-user-info" style="text-align: right;">
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo isset($nama) ? $nama : 'Administrator'; ?></div>
                        <div class="role" style="font-size:0.7rem;color:#64748b;">Administrator</div>
                    </div>
                    <?php $usr_initial = isset($initial) && !empty($initial) ? $initial : (isset($nama) ? strtoupper(substr($nama,0,2)) : 'AD'); ?>
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;background:linear-gradient(135deg, #0ea5e9, #0284c7);box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $usr_initial; ?></div>
                </div>
                
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- Elite Header Banner -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%); padding: 2rem 2.5rem; border-radius: 16px; margin-bottom: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(2,132,199,0.3);">
                <div class="header-bg-shapes" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.2) 0%, transparent 45%), radial-gradient(circle at 15% 85%, rgba(0, 0, 0, 0.15) 0%, transparent 40%); z-index: 1;"></div>
                <div style="position: absolute; inset: 0; opacity: 0.12; pointer-events: none; background: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'white\' /%3E%3C/svg%3E'); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div>
                        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.25); padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:700; margin-bottom:0.6rem; letter-spacing:0.5px;">
                            <i data-lucide="calendar" style="width:13px; height:13px;"></i>
                            <span><?= htmlspecialchars($hari ?? 'Hari Aktif') ?>, <?= date('d F Y', strtotime($tanggal)) ?></span>
                        </div>
                        <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="calendar-check" style="color: #38bdf8;"></i> Rekap Harian Absensi Guru
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.9rem; max-width: 650px;">
                            Kelola kehadiran guru harian: Guru sertifikasi hadir setiap hari kerja &amp; guru non-sertifikasi hadir otomatis sesuai jadwal jam mengajar.
                        </p>
                    </div>

                    <!-- Mini Rules Pill -->
                    <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; padding: 12px 16px; font-size: 0.8rem; line-height: 1.45; max-width: 300px;">
                        <div style="font-weight: 800; color: #fef08a; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <i data-lucide="clock" style="width:14px; height:14px;"></i> Standar Waktu Kehadiran:
                        </div>
                        <div style="color: #f0fdf4;">• Masuk: <strong>07.00 - 07.30</strong> (Acak)</div>
                        <div style="color: #eff6ff;">• Pulang: <strong>13.15 - 13.30</strong> (Acak)</div>
                    </div>
                </div>
            </div>

            <!-- Flash Alert Notification -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'sertifikasi_success'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 5px solid #22c55e; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #16a34a; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #166534; font-weight: 600;">
                            <strong>Alhamdulillah Berhasil!</strong> Sebanyak <strong><?= intval($_GET['count'] ?? 0) ?> guru sertifikasi</strong> telah berhasil dihadirkan untuk tanggal <strong><?= htmlspecialchars($tanggal) ?></strong> (Jam Masuk 07.00 - 07.30, Pulang 13.15 - 13.30).
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'jam_success'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 5px solid #10b981; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #059669; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #065f46; font-weight: 600;">
                            <strong>Alhamdulillah Berhasil!</strong> Sebanyak <strong><?= intval($_GET['count'] ?? 0) ?> guru</strong> yang memiliki jam mengajar hari <strong><?= htmlspecialchars($_GET['hari'] ?? ($hari ?? '')) ?></strong> telah berhasil dihadirkan untuk tanggal <strong><?= htmlspecialchars($tanggal) ?></strong> (Jam Masuk 07.00 - 07.30, Pulang 13.15 - 13.30).
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'all_success'): ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 5px solid #3b82f6; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <i data-lucide="sparkles" style="width: 22px; height: 22px; color: #2563eb; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #1e40af; font-weight: 600;">
                            <strong>Alhamdulillah Berhasil!</strong> Sebanyak <strong><?= intval($_GET['count'] ?? 0) ?> guru</strong> (Semua Guru Sertifikasi + Guru Ada Jam Mengajar Hari <?= htmlspecialchars($_GET['hari'] ?? ($hari ?? '')) ?>) telah berhasil dihadirkan untuk tanggal <strong><?= htmlspecialchars($tanggal) ?></strong>.
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'auto_alpa_success'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 5px solid #ef4444; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <i data-lucide="alert-octagon" style="width: 22px; height: 22px; color: #dc2626; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #991b1b; font-weight: 600;">
                            <strong>Auto-Alpa Berhasil Diproses!</strong> Sebanyak <strong><?= intval($_GET['count'] ?? 0) ?> guru</strong> yang memiliki jadwal hadir tetapi tidak ada absensi telah ditandai sebagai <strong>ALPA</strong>. (Guru yang tidak ada jam mengajar tetap aman).
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'libur'): ?>
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-left: 5px solid #e11d48; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="calendar-x" style="width: 22px; height: 22px; color: #e11d48; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #9f1239; font-weight: 600;">
                            <strong>Hari Libur / Agenda Madrasah:</strong> Tanggal <strong><?= htmlspecialchars(date('d/m/Y', strtotime($tanggal))) ?></strong> tercatat sebagai <strong><?= htmlspecialchars($_GET['libur_nama'] ?? 'Hari Libur Kalender') ?></strong>. Presensi dan Alpa tidak diberlakukan.
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'minggu_libur'): ?>
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #d97706; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #92400e; font-weight: 600;">
                            <strong>Perhatian:</strong> Tanggal yang dipilih jatuh pada hari <strong>Minggu</strong> (Libur KBM). Tidak ada jadwal mengajar reguler.
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'no_schedule'): ?>
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #d97706; flex-shrink: 0;"></i>
                        <div style="font-size: 0.88rem; color: #92400e; font-weight: 600;">
                            Tidak ditemukan jadwal mengajar guru pada hari <strong><?= htmlspecialchars($hari ?? '') ?></strong> di database.
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Summary KPI Badges -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="user-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Hadir</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $stats['hadir'] ?? 0 ?> <span style="font-size: 0.75rem; color: #10b981; font-weight: 700;">Guru</span></div>
                    </div>
                </div>

                <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="award"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Guru Sertifikasi</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $stats['sertifikasi'] ?? 0 ?> <span style="font-size: 0.75rem; color: #d97706; font-weight: 700;">(Tiap Hari)</span></div>
                    </div>
                </div>

                <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="book-open"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Ada Jam Hari Ini</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $stats['ada_jam'] ?? 0 ?> <span style="font-size: 0.75rem; color: #3b82f6; font-weight: 700;">Guru</span></div>
                    </div>
                </div>

                <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="clock-alert"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Terlambat / Izin</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= ($stats['terlambat'] ?? 0) + ($stats['izin'] ?? 0) ?> <span style="font-size: 0.75rem; color: #ef4444; font-weight: 700;">Guru</span></div>
                    </div>
                </div>
            </div>

            <!-- Main Card Container -->
            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                
                <!-- Action Controls Toolbar -->
                <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px; margin-bottom:1.5rem; padding-bottom:1.25rem; border-bottom:1px solid #f1f5f9;">
                    <?php
                    $prev_tanggal = date('Y-m-d', strtotime($tanggal . ' -1 day'));
                    $next_tanggal = date('Y-m-d', strtotime($tanggal . ' +1 day'));
                    $today_tanggal = date('Y-m-d');
                    ?>
                    <!-- Filter Date Form dengan Tombol Geser Tanggal -->
                    <div style="display:flex; align-items:center; gap:6px;">
                        <!-- Tombol Geser Mundur (Kemarin) -->
                        <a href="/admin/absen_guru_v2/rekap-harian?tanggal=<?= $prev_tanggal ?>" style="width:38px; height:38px; border-radius:10px; border:1.5px solid #cbd5e1; background:#ffffff; color:#475569; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; transition:0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.05);" title="Geser ke Kemarin (<?= date('d/m/Y', strtotime($prev_tanggal)) ?>)">
                            <i data-lucide="chevron-left" style="width:18px; height:18px;"></i>
                        </a>

                        <form method="GET" action="/admin/absen_guru_v2/rekap-harian" style="display:flex; align-items:center; gap:6px; margin:0;">
                            <div style="position:relative; display:flex; align-items:center;">
                                <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" onchange="this.form.submit()" style="padding:9px 12px; border:1.5px solid #cbd5e1; border-radius:10px; outline:none; font-family:inherit; font-size:0.88rem; font-weight:600; color:#1e293b; cursor:pointer;">
                            </div>
                            <button type="submit" style="padding:9px 16px; background:#0ea5e9; color:white; border:none; border-radius:10px; font-weight:700; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; gap:5px; transition:0.2s;">
                                <i data-lucide="filter" style="width:14px; height:14px;"></i> Filter
                            </button>
                        </form>

                        <!-- Tombol Geser Maju (Besok) -->
                        <a href="/admin/absen_guru_v2/rekap-harian?tanggal=<?= $next_tanggal ?>" style="width:38px; height:38px; border-radius:10px; border:1.5px solid #cbd5e1; background:#ffffff; color:#475569; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; transition:0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.05);" title="Geser ke Besok (<?= date('d/m/Y', strtotime($next_tanggal)) ?>)">
                            <i data-lucide="chevron-right" style="width:18px; height:18px;"></i>
                        </a>

                        <?php if ($tanggal !== $today_tanggal): ?>
                        <a href="/admin/absen_guru_v2/rekap-harian?tanggal=<?= $today_tanggal ?>" style="padding:7px 12px; border-radius:10px; border:1px solid #bfdbfe; background:#eff6ff; color:#2563eb; font-size:0.78rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" title="Kembali ke Tanggal Hari Ini">
                            Hari Ini
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Fast Attendance Generation Buttons -->
                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                        <!-- Tombol Harian 1: Hadirkan Guru Sertifikasi -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-sertifikasi" onsubmit="return confirmAction(event, 'sertifikasi', 'Hadirkan Semua Guru Sertifikasi (Hadir Setiap Hari)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-sertif" style="padding:9px 13px; background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(245,158,11,0.25); transition:0.2s;" title="Hadirkan semua guru sertifikasi hari ini">
                                <i data-lucide="award" style="width:15px; height:15px;"></i> Guru Sertifikasi
                            </button>
                        </form>

                        <!-- Tombol Harian 2: Hadirkan Guru Ada Jam -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-jam" onsubmit="return confirmAction(event, 'jam', 'Hadirkan Guru Yang Ada Jam Hari Ini (<?= htmlspecialchars($hari) ?>)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-jam" style="padding:9px 13px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(16,185,129,0.25); transition:0.2s;" title="Hadirkan guru yang ada jam hari ini">
                                <i data-lucide="calendar-check" style="width:15px; height:15px;"></i> Guru Ada Jam (<?= htmlspecialchars($hari) ?>)
                            </button>
                        </form>

                        <!-- Tombol Harian 3: Hadirkan Semua Otomatis -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-semua" onsubmit="return confirmAction(event, 'semua', 'Hadirkan Semua Guru (Sertifikasi + Yang Ada Jam Hari Ini)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-all" style="padding:9px 13px; background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(59,130,246,0.25); transition:0.2s;" title="Hadirkan semua otomatis (Sertif + Ada Jam)">
                                <i data-lucide="sparkles" style="width:15px; height:15px;"></i> Semua Otomatis
                            </button>
                        </form>

                        <!-- Tombol Harian 4: Auto Alpa yang Kosong -->
                        <form method="POST" action="/admin/absen_guru_v2/auto-alpa" onsubmit="return confirmAction(event, 'alpa', 'Tandai status ALPA untuk guru yang terjadwal hadir hari ini (Sertifikasi & Ada Jam) tetapi belum absen?\n\n*Catatan: Guru yang TIDAK ADA JAM mengajar di hari ini TETAP TIDAK DIALPA.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" style="padding:9px 13px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(239,68,68,0.25); transition:0.2s;" title="Tandai Alpa otomatis untuk guru yang terjadwal hadir tapi belum absen hari ini">
                                <i data-lucide="user-x" style="width:15px; height:15px;"></i> Auto Alpa
                            </button>
                        </form>

                        <div style="height:24px; width:1px; background:#cbd5e1; margin:0 3px;"></div>

                        <!-- Tombol Bulanan: Hadirkan 1 Bulan Khusus Sertifikasi -->
                        <button type="button" onclick="generateBulanDirect('sertifikasi', 'Khusus Semua Guru Sertifikasi')" style="padding:9px 14px; background:linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(245,158,11,0.25); transition:0.2s;" title="Generate 1 Bulan Penuh untuk Guru Sertifikasi Saja">
                            <i data-lucide="award" style="width:15px; height:15px;"></i> 1 Bulan Sertifikasi
                        </button>

                        <!-- Tombol Bulanan: Hadirkan 1 Bulan Semua Guru Penuh -->
                        <button type="button" onclick="generateBulanDirect('semua', 'Semua Dewan Guru Madrasah')" style="padding:9px 14px; background:linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(99,102,241,0.25); transition:0.2s;" title="Generate 1 Bulan Penuh untuk Semua Dewan Guru">
                            <i data-lucide="users" style="width:15px; height:15px;"></i> 1 Bulan Semua Guru
                        </button>

                        <!-- Tombol Bulanan: Opsi Modal Generate 1 Bulan -->
                        <button type="button" onclick="openModalGenerateBulan('<?= date('m', strtotime($tanggal)) ?>', '<?= date('Y', strtotime($tanggal)) ?>')" style="padding:9px 14px; background:linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(139,92,246,0.25); transition:0.2s;" title="Pilihan mode kombinasi, jam, atau opsi alpa 1 bulan">
                            <i data-lucide="calendar-days" style="width:15px; height:15px;"></i> Opsi Bulan (<?= date('M Y', strtotime($tanggal)) ?>)
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:850px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:50px;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:170px;">NIP / NUPTK</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Nama Guru &amp; Kategori</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:150px;">Jam Masuk</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:150px;">Jam Pulang</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:130px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($absensi)): ?>
                            <tr>
                                <td colspan="6" style="padding:36px 20px; text-align:center; color:#94a3b8;">
                                    <div style="width:48px; height:48px; border-radius:50%; background:#f1f5f9; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; margin-bottom:8px;">
                                        <i data-lucide="clipboard-x" style="width:24px; height:24px;"></i>
                                    </div>
                                    <div style="font-size:0.95rem; font-weight:700; color:#475569;">Belum ada data absensi untuk tanggal ini.</div>
                                    <div style="font-size:0.8rem; color:#94a3b8; margin-top:4px;">Gunakan tombol di atas untuk menghadirkan guru sertifikasi atau guru yang memiliki jam mengajar hari ini.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $no = 1; 
                                foreach($absensi as $a): 
                                    // Deteksi waktu masuk & keluar
                                    $jamMasuk = !empty($a['waktu_masuk']) ? $a['waktu_masuk'] : (!empty($a['jam_masuk']) ? $a['jam_masuk'] : (!empty($a['created_at']) ? date('H:i:s', strtotime($a['created_at'])) : '-'));
                                    $jamPulang = !empty($a['waktu_keluar']) ? $a['waktu_keluar'] : (!empty($a['jam_pulang']) ? $a['jam_pulang'] : '-');
                                    
                                    // Status sertifikasi
                                    $isSertif = false;
                                    if (!empty($a['status_kepegawaian']) && stripos($a['status_kepegawaian'], 'sertifikasi') !== false) {
                                        $isSertif = true;
                                    } elseif (!empty($a['nrg']) && trim($a['nrg']) !== '-' && trim($a['nrg']) !== '') {
                                        $isSertif = true;
                                    } elseif (!empty($a['no_sertifikat']) && trim($a['no_sertifikat']) !== '-' && trim($a['no_sertifikat']) !== '') {
                                        $isSertif = true;
                                    }
                                    
                                    $hasJam = in_array(intval($a['guru_id']), $guruJamIds ?? []);
                                ?>
                                <tr style="border-bottom:1px solid #f1f5f9; transition:0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:14px 16px; color:#64748b; font-size:0.85rem; font-weight:600;"><?php echo $no++; ?></td>
                                    <td style="padding:14px 16px; font-size:0.85rem; color:#475569;">
                                        <div style="font-family:monospace; font-weight:600; color:#334155;"><?php echo htmlspecialchars($a['nip'] ?: '-'); ?></div>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <div style="font-weight:700; color:#0f172a; font-size:0.9rem;"><?php echo htmlspecialchars($a['nama'] ?? 'Guru (ID: '.$a['guru_id'].')'); ?></div>
                                        <div style="display:flex; align-items:center; gap:6px; margin-top:4px;">
                                            <?php if ($isSertif): ?>
                                                <span style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; padding:2px 8px; border-radius:100px; font-size:0.7rem; font-weight:800; display:inline-flex; align-items:center; gap:3px;">
                                                    <i data-lucide="award" style="width:11px; height:11px;"></i> Sertifikasi (Tiap Hari)
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($hasJam): ?>
                                                <span style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:2px 8px; border-radius:100px; font-size:0.7rem; font-weight:800; display:inline-flex; align-items:center; gap:3px;">
                                                    <i data-lucide="book-open" style="width:11px; height:11px;"></i> Ada Jam <?= htmlspecialchars($hari ?? '') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="padding:14px 16px; font-size:0.85rem;">
                                        <div style="display:inline-flex; align-items:center; gap:6px; font-weight:700; color:#0f172a; background:#f8fafc; border:1px solid #e2e8f0; padding:4px 10px; border-radius:8px;">
                                            <i data-lucide="log-in" style="width:13px; height:13px; color:#10b981;"></i>
                                            <span><?= htmlspecialchars($jamMasuk) ?></span>
                                        </div>
                                    </td>
                                    <td style="padding:14px 16px; font-size:0.85rem;">
                                        <?php if ($jamPulang !== '-' && !empty($jamPulang)): ?>
                                            <div style="display:inline-flex; align-items:center; gap:6px; font-weight:700; color:#0f172a; background:#f8fafc; border:1px solid #e2e8f0; padding:4px 10px; border-radius:8px;">
                                                <i data-lucide="log-out" style="width:13px; height:13px; color:#3b82f6;"></i>
                                                <span><?= htmlspecialchars($jamPulang) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span style="color:#94a3b8; font-weight:600;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        <?php if ($a['status'] == 'Hadir'): ?>
                                            <span style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#22c55e;"></span> Hadir
                                            </span>
                                        <?php elseif ($a['status'] == 'Terlambat'): ?>
                                            <span style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
                                                <span style="width:6px; height:6px; border-radius:50%; background:#f59e0b;"></span> Terlambat
                                            </span>
                                        <?php elseif (in_array($a['status'], ['Izin', 'Dinas Luar'])): ?>
                                            <span style="background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:800;">
                                                <?php echo htmlspecialchars($a['status']); ?>
                                            </span>
                                        <?php elseif ($a['status'] == 'Sakit'): ?>
                                            <span style="background:#ffedd5; color:#c2410c; border:1px solid #fed7aa; padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:800;">
                                                Sakit
                                            </span>
                                        <?php else: ?>
                                            <span style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:4px 12px; border-radius:100px; font-size:0.75rem; font-weight:800;">
                                                <?php echo htmlspecialchars($a['status'] ?: 'Alpa'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate 1 Bulan Penuh -->
<div id="modalGenBulan" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:540px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); overflow:hidden; animation:zoomIn 0.2s ease;">
        <div style="padding:18px 24px; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:white; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="sparkles" style="width:20px; height:20px;"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-size:1.1rem; font-weight:800;">Generate Hadir 1 Bulan Penuh</h3>
                    <p style="margin:0; font-size:0.78rem; opacity:0.9;">Otomatisasi kehadiran guru bulanan madrasah</p>
                </div>
            </div>
            <button type="button" onclick="closeModalGenerateBulan()" style="background:none; border:none; color:white; font-size:1.4rem; cursor:pointer; line-height:1;">&times;</button>
        </div>

        <form method="POST" action="/admin/absen_guru_v2/generate-bulan" style="padding:22px 24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:5px;">Bulan Target</label>
                    <select name="bulan" id="modalBulan" style="width:100%; padding:9px 12px; border:1.5px solid #cbd5e1; border-radius:10px; font-weight:600; font-size:0.88rem; outline:none;">
                        <?php foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $v): ?>
                            <option value="<?= $k ?>" <?= $k === date('m', strtotime($tanggal)) ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:5px;">Tahun</label>
                    <select name="tahun" id="modalTahun" style="width:100%; padding:9px 12px; border:1.5px solid #cbd5e1; border-radius:10px; font-weight:600; font-size:0.88rem; outline:none;">
                        <?php for($y = date('Y')-1; $y <= date('Y')+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $y == date('Y', strtotime($tanggal)) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:8px;">Pilih Mode Generate Kehadiran:</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #6366f1; background:#f5f3ff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="kombinasi" checked style="margin-top:3px; accent-color:#6366f1;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#4338ca;">⚡ Kombinasi Otomatis (Rekomendasi Madrasah)</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Guru sertifikasi hadir setiap hari kerja (Senin-Sabtu), guru non-sertifikasi hadir saat ada jam mengajar.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="sertifikasi" style="margin-top:3px; accent-color:#f59e0b;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#b45309;">🏆 Khusus Semua Guru Sertifikasi</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Hanya guru bersertifikasi yang dihadirkan setiap hari kerja sebulan penuh. Guru lain tidak disentuh.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="jam" style="margin-top:3px; accent-color:#10b981;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#065f46;">📅 Guru Sesuai Jam Mengajar</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Guru dihadirkan HANYA pada hari di mana mereka memiliki jadwal jam mengajar di hari tersebut.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="semua" style="margin-top:3px; accent-color:#0ea5e9;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#0369a1;">👥 Semua Guru Madrasah (Penuh)</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Seluruh guru madrasah dihadirkan setiap hari kerja aktif (Senin-Sabtu) sebulan penuh.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; margin-bottom:18px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.8rem; font-weight:700; color:#334155;">
                    <input type="checkbox" name="isi_alpa" value="1" style="accent-color:#ef4444; width:16px; height:16px;">
                    <span>Tandai ALPA untuk guru yang terjadwal tapi kosong pada tanggal yang sudah lewat</span>
                </label>
                <div style="font-size:0.73rem; color:#64748b; margin-left:24px; margin-top:2px;">
                    *Guru yang TIDAK ADA JAM mengajar tetap aman (tidak dialpakan).
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModalGenerateBulan()" style="padding:9px 16px; background:#f1f5f9; color:#475569; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 20px; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(99,102,241,0.3);">
                    <i data-lucide="sparkles" style="width:14px; height:14px;"></i> Proses Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }

    function confirmAction(e, type, message) {
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        return true;
    }

    function openModalGenerateBulan(bln, thn) {
        if (bln) document.getElementById('modalBulan').value = bln;
        if (thn) document.getElementById('modalTahun').value = thn;
        document.getElementById('modalGenBulan').style.display = 'flex';
        lucide.createIcons();
    }

    function closeModalGenerateBulan() {
        document.getElementById('modalGenBulan').style.display = 'none';
    }

    function generateBulanDirect(mode, modeLabel) {
        var b = '<?= date('m', strtotime($tanggal)) ?>';
        var y = '<?= date('Y', strtotime($tanggal)) ?>';
        var namaBulan = '<?= date('M Y', strtotime($tanggal)) ?>';
        
        var msg = 'Konfirmasi Generate Kehadiran 1 Bulan Penuh (' + namaBulan + '):\n\n' +
                  'Target: ' + modeLabel + '\n' +
                  'Standar Waktu: Masuk 07:00-07:30 (acak), Pulang 13:15-13:30 (acak)\n' +
                  'Hari Minggu dan Kalender Libur otomatis dilewati.\n\n' +
                  'Lanjutkan proses otomatisasi kehadiran?';
                  
        if (confirm(msg)) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/absen_guru_v2/generate-bulan';
            
            var inB = document.createElement('input'); inB.type = 'hidden'; inB.name = 'bulan'; inB.value = b; form.appendChild(inB);
            var inY = document.createElement('input'); inY.type = 'hidden'; inY.name = 'tahun'; inY.value = y; form.appendChild(inY);
            var inM = document.createElement('input'); inM.type = 'hidden'; inM.name = 'mode'; inM.value = mode; form.appendChild(inM);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
