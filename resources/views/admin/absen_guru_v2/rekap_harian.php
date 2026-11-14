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
                    <!-- Filter Date Form -->
                    <form method="GET" action="/admin/absen_guru_v2/rekap-harian" style="display:flex; align-items:center; gap:8px;">
                        <div style="position:relative; display:flex; align-items:center;">
                            <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" style="padding:9px 14px; border:1.5px solid #cbd5e1; border-radius:10px; outline:none; font-family:inherit; font-size:0.88rem; font-weight:600; color:#1e293b;">
                        </div>
                        <button type="submit" style="padding:9px 18px; background:#0ea5e9; color:white; border:none; border-radius:10px; font-weight:700; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; gap:6px; transition:0.2s;">
                            <i data-lucide="filter" style="width:14px; height:14px;"></i> Filter
                        </button>
                    </form>

                    <!-- Fast Attendance Generation Buttons -->
                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                        <!-- Tombol 1: Hadirkan Guru Sertifikasi -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-sertifikasi" onsubmit="return confirmAction(event, 'sertifikasi', 'Hadirkan Semua Guru Sertifikasi (Hadir Setiap Hari)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-sertif" style="padding:9px 16px; background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:flex; align-items:center; gap:7px; box-shadow:0 3px 8px rgba(245,158,11,0.25); transition:0.2s;">
                                <i data-lucide="award" style="width:15px; height:15px;"></i> Hadirkan Semua Guru Sertifikasi
                            </button>
                        </form>

                        <!-- Tombol 2: Hadirkan Guru Ada Jam -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-jam" onsubmit="return confirmAction(event, 'jam', 'Hadirkan Guru Yang Ada Jam Hari Ini (<?= htmlspecialchars($hari) ?>)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-jam" style="padding:9px 16px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:flex; align-items:center; gap:7px; box-shadow:0 3px 8px rgba(16,185,129,0.25); transition:0.2s;">
                                <i data-lucide="calendar-check" style="width:15px; height:15px;"></i> Hadirkan Guru Ada Jam (<?= htmlspecialchars($hari) ?>)
                            </button>
                        </form>

                        <!-- Tombol 3: Hadirkan Semua (Sertif + Jam) -->
                        <form method="POST" action="/admin/absen_guru_v2/hadirkan-semua" onsubmit="return confirmAction(event, 'semua', 'Hadirkan Semua Guru (Sertifikasi + Yang Ada Jam Hari Ini)? Waktu masuk akan diacak antara 07:00-07:30 dan pulang 13:15-13:30.')">
                            <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                            <button type="submit" class="btn-hadirkan-all" style="padding:9px 16px; background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:flex; align-items:center; gap:7px; box-shadow:0 3px 8px rgba(59,130,246,0.25); transition:0.2s;">
                                <i data-lucide="sparkles" style="width:15px; height:15px;"></i> Hadirkan Semua Otomatis
                            </button>
                        </form>

                        <!-- Tombol 4: Generate Hadir 1 Bulan Penuh -->
                        <form method="POST" action="/admin/absen_guru_v2/generate-bulan" onsubmit="return confirmAction(event, 'bulan', 'Generate seluruh kehadiran otomatis untuk 1 BULAN PENUH (Bulan <?= date('F Y', strtotime($tanggal)) ?>)? Guru sertifikasi hadir tiap hari kerja, guru non-sertifikasi hadir saat ada jam mengajar.')">
                            <input type="hidden" name="bulan" value="<?= date('m', strtotime($tanggal)) ?>">
                            <input type="hidden" name="tahun" value="<?= date('Y', strtotime($tanggal)) ?>">
                            <button type="submit" class="btn-hadirkan-bulan" style="padding:9px 16px; background:linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:flex; align-items:center; gap:7px; box-shadow:0 3px 8px rgba(139,92,246,0.25); transition:0.2s;" title="Sekali klik untuk meng-generate kehadiran seluruh guru selama 1 bulan penuh">
                                <i data-lucide="calendar-days" style="width:15px; height:15px;"></i> Generate 1 Bulan (<?= date('M Y', strtotime($tanggal)) ?>)
                            </button>
                        </form>
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
</script>
