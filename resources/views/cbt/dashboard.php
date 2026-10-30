<?php
$pageTitle = 'Dashboard CBT';
$active_menu = 'dashboard';
$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
?>
<style>
    .welcome-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        border-radius: 20px;
        padding: 2.2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 15px 35px -10px rgba(79, 70, 229, 0.4);
        margin-bottom: 2rem;
    }
    .welcome-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(255,255,255,0.18) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(255,255,255,0.12) 0%, transparent 40%),
            url("data:image/svg+xml,%3Csvg width='24' height='24' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='white' fill-opacity='0.15'/%3E%3C/svg%3E");
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }
    .hero-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 4px 14px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.72rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0.8rem;
        border: 1px solid rgba(255,255,255,0.3);
        letter-spacing: 0.5px;
        backdrop-filter: blur(8px);
    }
    .hero-content h1 {
        font-size: 1.85rem;
        font-weight: 900;
        margin: 0 0 0.5rem;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    .hero-content p {
        font-size: 0.92rem;
        opacity: 0.9;
        line-height: 1.55;
        margin: 0 0 1.3rem;
    }
    .hero-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .hero-btn {
        text-decoration: none;
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.84rem;
        transition: 0.25s;
    }
    .hero-btn.primary {
        background: #ffffff;
        color: #4f46e5;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .hero-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.18);
        background: #f8fafc;
    }
    .hero-btn.secondary {
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }
    .hero-btn.secondary:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.22);
    }

    .hero-stats-mini {
        display: flex;
        gap: 1.5rem;
        z-index: 2;
        flex-shrink: 0;
    }
    .mini-stat {
        text-align: right;
        border-right: 2px solid rgba(255,255,255,0.18);
        padding-right: 1.5rem;
    }
    .mini-stat:last-child {
        border: none;
        padding-right: 0;
    }
    .mini-stat .val {
        font-size: 1.65rem;
        font-weight: 900;
        color: white;
        line-height: 1.1;
    }
    .mini-stat .lab {
        font-size: 0.7rem;
        font-weight: 700;
        opacity: 0.75;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-top: 4px;
    }

    .hero-bg-icon {
        position: absolute;
        right: -25px;
        bottom: -25px;
        opacity: 0.08;
        transform: rotate(-12deg);
        z-index: 1;
        pointer-events: none;
    }
    .hero-bg-icon i, .hero-bg-icon svg {
        width: 170px !important;
        height: 170px !important;
        color: white;
    }

    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        20%, 60% { transform: rotate(14deg); }
        40%, 80% { transform: rotate(-14deg); }
    }
    .wave {
        display: inline-block;
        animation: wave 2s infinite;
        transform-origin: 70% 70%;
    }

    @media (max-width: 900px) {
        .welcome-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 1.75rem;
        }
        .hero-stats-mini {
            width: 100%;
            justify-content: flex-start;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 1.25rem;
        }
        .mini-stat {
            text-align: left;
        }
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">
            
            <!-- Welcome Hero -->
            <div class="welcome-hero">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i data-lucide="monitor-check" style="width: 14px; height: 14px;"></i> CBT ENGINE ONLINE v2.5
                    </div>
                    <h1>Selamat Datang di CBT Online! <span class="wave">👋</span></h1>
                    <p>
                        Sistem pelaksanaan ujian berbasis komputer terintegrasi. Buat jadwal, susun soal, rilis token ujian, pantau pengerjaan siswa, hingga analisis butir soal secara real-time.
                    </p>
                    <div class="hero-actions">
                        <a href="<?php echo base_url('cbt/jadwal'); ?>" class="hero-btn primary">
                            <i data-lucide="calendar" style="width: 16px; height: 16px;"></i> Jadwal Ujian
                        </a>
                        <a href="<?php echo base_url('cbt/ujian/tambah'); ?>" class="hero-btn secondary">
                            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i> Buat Ujian Baru
                        </a>
                        <a href="<?php echo base_url('cbt/token'); ?>" class="hero-btn secondary">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i> Token Aktif: <?php echo htmlspecialchars($tokenAktif['token'] ?? 'OFF'); ?>
                        </a>
                    </div>
                </div>

                <!-- Right Side Mini Stats -->
                <div class="hero-stats-mini">
                    <div class="mini-stat">
                        <div class="val"><?php echo date('H:i'); ?></div>
                        <div class="lab">Waktu Server</div>
                    </div>
                    <div class="mini-stat">
                        <div class="val"><?php echo date('d M'); ?></div>
                        <div class="lab"><?php echo date('Y'); ?></div>
                    </div>
                    <div class="mini-stat">
                        <div class="val" style="color: #a5b4fc;"><?php echo $totalPesertaSelesai; ?></div>
                        <div class="lab">Siswa Selesai</div>
                    </div>
                </div>

                <!-- Floating Giant Watermark Icon -->
                <div class="hero-bg-icon">
                    <i data-lucide="monitor-check"></i>
                </div>
            </div>

            <!-- 4 Stat KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <!-- Total Ujian -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="clipboard-list" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalUjian; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Paket Ujian Dibuat</div>
                    </div>
                </div>

                <!-- Total Soal -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="file-question" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalSoal; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Total Butir Soal</div>
                    </div>
                </div>

                <!-- Peserta Mengerjakan / Selesai -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="users" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalPeserta; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Peserta Terdata</div>
                    </div>
                </div>

                <!-- Token Aktif -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="key" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #b45309; line-height: 1; font-family: monospace; letter-spacing: 2px;">
                            <?php echo htmlspecialchars($tokenAktif['token'] ?? 'TIDAK AKTIF'); ?>
                        </div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Token Saat Ini</div>
                    </div>
                </div>
            </div>

            <!-- Two Column Section -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                
                <!-- Jadwal Ujian Aktif -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.95rem; color: #1e293b;">
                            <i data-lucide="calendar-check" style="width: 18px; height: 18px; color: #4f46e5;"></i>
                            Jadwal Ujian Aktif & Terjadwal
                        </div>
                        <a href="<?php echo base_url('cbt/jadwal'); ?>" style="font-size: 0.8rem; font-weight: 700; color: #4f46e5; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            Kelola Jadwal <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                        </a>
                    </div>
                    <div style="padding: 0;">
                        <?php if (empty($ujianList)): ?>
                            <div style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                <i data-lucide="calendar-x" style="width: 40px; height: 40px; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.88rem; font-weight: 700; color: #475569;">Belum ada jadwal ujian aktif</div>
                                <div style="font-size: 0.78rem;">Buat paket ujian baru dan jadwalkan sesi ujian.</div>
                            </div>
                        <?php else: ?>
                            <div class="z-table-wrap">
                                <table class="z-table">
                                    <thead>
                                        <tr>
                                            <th>Ujian / Mapel</th>
                                            <th>Kelas</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $topUjian = array_slice($ujianList, 0, 5);
                                        foreach($topUjian as $u): 
                                            $st = $u['status'] ?? 'Draft';
                                            $stBg = ($st === 'Aktif') ? '#dcfce7' : (($st === 'Selesai') ? '#f1f5f9' : '#fef3c7');
                                            $stCol = ($st === 'Aktif') ? '#15803d' : (($st === 'Selesai') ? '#475569' : '#b45309');
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($u['nama_ujian']); ?></div>
                                                <div style="font-size: 0.72rem; color: #64748b;"><?php echo htmlspecialchars($u['mapel_nama']); ?> &bull; Kode: <?php echo htmlspecialchars($u['kode_ujian']); ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.74rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 6px;">
                                                    <?php echo htmlspecialchars($u['kelas_nama'] ?: 'Semua Kelas'); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8rem; color: #475569; font-weight: 600;">
                                                <?php echo $u['durasi_menit']; ?> Menit
                                            </td>
                                            <td>
                                                <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: <?php echo $stBg; ?>; color: <?php echo $stCol; ?>;">
                                                    <?php echo htmlspecialchars($st); ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <a href="<?php echo base_url('cbt/soal?ujian_id=' . $u['id']); ?>" class="e-btn e-btn-blue-light" style="padding: 4px 8px; font-size: 0.72rem;" title="Kelola Soal">
                                                    <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Soal
                                                </a>
                                                <a href="<?php echo base_url('cbt/hasil?ujian_id=' . $u['id']); ?>" class="e-btn e-btn-primary" style="padding: 4px 8px; font-size: 0.72rem; background: #4f46e5;" title="Hasil Siswa">
                                                    <i data-lucide="award" style="width: 13px; height: 13px;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hasil Peserta Terbaru -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.95rem; color: #1e293b;">
                            <i data-lucide="user-check" style="width: 18px; height: 18px; color: #059669;"></i>
                            Peserta Selesai Terbaru
                        </div>
                        <a href="<?php echo base_url('cbt/hasil'); ?>" style="font-size: 0.8rem; font-weight: 700; color: #059669; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Semua <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                        </a>
                    </div>
                    <div style="padding: 0;">
                        <?php if (empty($pesertaTerbaru)): ?>
                            <div style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                <i data-lucide="user-x" style="width: 40px; height: 40px; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.88rem; font-weight: 700; color: #475569;">Belum ada riwayat pengerjaan ujian</div>
                                <div style="font-size: 0.78rem;">Siswa yang menyelesaikan ujian akan tampil di sini secara real-time.</div>
                            </div>
                        <?php else: ?>
                            <div class="z-table-wrap">
                                <table class="z-table">
                                    <thead>
                                        <tr>
                                            <th>Siswa</th>
                                            <th>Ujian</th>
                                            <th>Skor / Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pesertaTerbaru as $p): 
                                            $isTuntas = ($p['nilai_akhir'] >= 75);
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.85rem;"><?php echo htmlspecialchars($p['nama_siswa']); ?></div>
                                                <div style="font-size: 0.72rem; color: #64748b;">Kelas: <?php echo htmlspecialchars($p['kelas_nama'] ?: '-'); ?></div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #334155; font-size: 0.8rem;"><?php echo htmlspecialchars($p['nama_ujian'] ?? 'Ujian'); ?></div>
                                                <div style="font-size: 0.7rem; color: #94a3b8;">B: <?php echo $p['jumlah_benar']; ?> &bull; S: <?php echo $p['jumlah_salah']; ?></div>
                                            </td>
                                            <td>
                                                <div style="font-size: 1.15rem; font-weight: 900; color: <?php echo $isTuntas ? '#15803d' : '#b91c1c'; ?>;">
                                                    <?php echo number_format($p['nilai_akhir'], 1); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; <?php echo $isTuntas ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;'; ?>">
                                                    <?php echo $isTuntas ? 'Tuntas' : 'Remedial'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Quick Action Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="<?php echo base_url('cbt/jadwal'); ?>" class="card" style="padding: 1.25rem; text-decoration: none; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; transition: 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="calendar"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;">Jadwal Ujian</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Atur waktu & sesi</div>
                    </div>
                </a>

                <a href="<?php echo base_url('cbt/ujian/tambah'); ?>" class="card" style="padding: 1.25rem; text-decoration: none; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; transition: 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #fdf4ff; color: #c026d3; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="plus-circle"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;">Tambah Ujian</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Buat paket & soal</div>
                    </div>
                </a>

                <a href="<?php echo base_url('cbt/token'); ?>" class="card" style="padding: 1.25rem; text-decoration: none; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; transition: 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="key-round"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;">Token Ujian</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Generate token baru</div>
                    </div>
                </a>

                <a href="<?php echo base_url('cbt/hasil'); ?>" class="card" style="padding: 1.25rem; text-decoration: none; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; transition: 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="award"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;">Hasil & Rekap</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Nilai siswa & ekspor</div>
                    </div>
                </a>

                <a href="<?php echo base_url('cbt/analisis'); ?>" class="card" style="padding: 1.25rem; text-decoration: none; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; transition: 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="bar-chart-2"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;">Analisis Butir</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Tingkat kesukaran soal</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

