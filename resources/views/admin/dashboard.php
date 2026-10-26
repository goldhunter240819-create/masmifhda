<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . base_url('login'));
    exit;
}
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['guru_nama']) ? $_SESSION['guru_nama'] : 'Admin');
$adminInitial = strtoupper(substr($adminNama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../_zamrud_head.php'; ?>
    <style>
        .pkkm-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .pkkm-stat-card {
            background: #fff;
            border-radius: var(--z-radius, 12px);
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--z-border, #e2e8f0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pkkm-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(14, 165, 233, 0.12);
            border-color: #38bdf8;
        }
        .pkkm-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pkkm-stat-info .num {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .pkkm-stat-info .lbl {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .pkkm-stat-info .sub {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .komponen-card {
            background: #fff;
            border-radius: var(--z-radius, 12px);
            border: 1px solid var(--z-border, #e2e8f0);
            padding: 1.5rem;
            transition: 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .komponen-card:hover {
            border-color: #0ea5e9;
            box-shadow: 0 6px 14px rgba(14, 165, 233, 0.08);
        }
        .p-bar-bg {
            height: 8px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
            margin: 0.75rem 0 0.5rem;
        }
        .p-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #0284c7, #38bdf8);
            transition: width 0.5s ease-in-out;
        }

        .quick-nav-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
            border-radius: var(--z-radius, 12px);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        .quick-nav-card:hover {
            transform: translateY(-3px);
            border-color: #38bdf8;
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.15);
        }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/../_zamrud_sidebar.php'; ?>

        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">PKKM Madrasah</span>
                    <span class="z-badge" style="background:#eff6ff; color:#0284c7; border:1px solid #bfdbfe;">Akreditasi & Mutu</span>
                </div>
                <div class="z-header-right" style="display:flex; align-items:center; gap:12px;">
                    <a href="<?php echo base_url('portal'); ?>" class="z-btn z-btn-secondary" style="display:inline-flex; align-items:center; gap:6px; font-size:0.8rem; padding:0.4rem 0.85rem; border-radius:8px; text-decoration:none; border:1px solid #cbd5e1; color:#475569; background:#fff;">
                        <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                    </a>
                    <div class="z-header-user">
                        <div class="z-user-info">
                            <div class="name"><?php echo htmlspecialchars($adminNama); ?></div>
                            <div class="role"><?php echo !empty($_SESSION['is_kamad']) ? 'Kepala Madrasah' : 'Tim Penilai PKKM'; ?></div>
                        </div>
                        <div class="z-avatar"><?php echo $adminInitial; ?></div>
                    </div>
                </div>
            </header>

            <div class="z-scroll">
                <!-- HERO BANNER -->
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0284c7 100%); padding: 2rem 2.5rem; border-radius: var(--z-radius, 16px); margin-bottom: 2rem; color: white; box-shadow: 0 10px 25px -5px rgba(30,58,138,0.3);">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="award" style="color: rgba(255,255,255,0.85);"></i> Penilaian Kinerja Kepala Madrasah (PKKM)
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.92); font-size: 0.95rem;">
                            Monitoring evaluasi 4 Komponen Utama, pemenuhan instrumen indikator, dan repositori dokumen bukti fisik terpadu.
                        </p>
                    </div>
                </div>

                <!-- STATS GRID -->
                <div class="pkkm-stat-grid">
                    <div class="pkkm-stat-card">
                        <div class="pkkm-stat-icon" style="background:#eff6ff; color:#0284c7;">
                            <i data-lucide="layers" style="width:26px; height:26px;"></i>
                        </div>
                        <div class="pkkm-stat-info">
                            <div class="lbl">Komponen Utama</div>
                            <div class="num"><?php echo count($komponen); ?></div>
                            <div class="sub"><?php echo $totalSubKomponen; ?> Sub-Komponen</div>
                        </div>
                    </div>

                    <div class="pkkm-stat-card">
                        <div class="pkkm-stat-icon" style="background:#f0f9ff; color:#0284c7;">
                            <i data-lucide="file-check-2" style="width:26px; height:26px;"></i>
                        </div>
                        <div class="pkkm-stat-info">
                            <div class="lbl">Instrumen Indikator</div>
                            <div class="num"><?php echo $totalIndikator; ?></div>
                            <div class="sub"><?php echo $totalNilaiTerisi; ?> Indikator Terisi Nilai</div>
                        </div>
                    </div>

                    <div class="pkkm-stat-card">
                        <div class="pkkm-stat-icon" style="background:#fef3c7; color:#d97706;">
                            <i data-lucide="star" style="width:26px; height:26px;"></i>
                        </div>
                        <div class="pkkm-stat-info">
                            <div class="lbl">Rata-rata Skor</div>
                            <div class="num"><?php echo $avgSkor > 0 ? $avgSkor : '-'; ?> <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ 4.00</span></div>
                            <div class="sub"><?php echo $avgSkor >= 3.5 ? 'Predikat Amat Baik' : ($avgSkor >= 2.75 ? 'Predikat Baik' : 'Skor Mandiri'); ?></div>
                        </div>
                    </div>

                    <div class="pkkm-stat-card">
                        <div class="pkkm-stat-icon" style="background:#fdf4ff; color:#a855f7;">
                            <i data-lucide="folder-check" style="width:26px; height:26px;"></i>
                        </div>
                        <div class="pkkm-stat-info">
                            <div class="lbl">Dokumen Bukti</div>
                            <div class="num"><?php echo $totalBerkasUploaded; ?> <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ <?php echo $totalMasterBerkas; ?></span></div>
                            <div class="sub"><?php echo $totalMasterBerkas > 0 ? round(($totalBerkasUploaded / $totalMasterBerkas) * 100) : 0; ?>% Berkas Terkumpul</div>
                        </div>
                    </div>
                </div>

                <!-- QUICK NAVIGATION TO CORE MENUS -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.25rem; margin-bottom:2rem;">
                    <a href="<?php echo base_url('admin/pkkm/instrumen'); ?>" class="quick-nav-card">
                        <div style="width:54px; height:54px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#0284c7; flex-shrink:0;">
                            <i data-lucide="list-checks" style="width:28px;height:28px;"></i>
                        </div>
                        <div>
                            <div style="font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:4px;">Instrumen Penilaian &rarr;</div>
                            <p style="font-size:0.85rem; color:#64748b; margin:0; line-height:1.4;">
                                Input & verifikasi skor penilaian <?php echo $totalIndikator; ?> butir indikator kinerja madrasah beserta kriteria bukti fisiknya.
                            </p>
                        </div>
                    </a>

                    <a href="<?php echo base_url('admin/pkkm/dokumen'); ?>" class="quick-nav-card">
                        <div style="width:54px; height:54px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#2563eb; flex-shrink:0;">
                            <i data-lucide="folder-open" style="width:28px;height:28px;"></i>
                        </div>
                        <div>
                            <div style="font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:4px;">Dokumen Bukti Fisik &rarr;</div>
                            <p style="font-size:0.85rem; color:#64748b; margin:0; line-height:1.4;">
                                Kelola 243 daftar master dokumen SK, KTSP, RKAM, laporan EDM, dan file pendukung penilaian.
                            </p>
                        </div>
                    </a>
                </div>

                <!-- PROGRESS PER KOMPONEN -->
                <div class="z-card" style="padding:1.5rem; margin-bottom:2rem; background:#fff; border-radius:var(--z-radius, 12px); border:1px solid var(--z-border, #e2e8f0);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:10px;">
                        <div>
                            <h2 style="font-size:1.2rem; font-weight:800; color:#0f172a; margin:0 0 4px 0;">Capaian per Komponen Standar</h2>
                            <p style="font-size:0.85rem; color:#64748b; margin:0;">Rekap kelengkapan instrumen dan rata-rata skor per unsur kinerja</p>
                        </div>
                        <a href="<?php echo base_url('admin/pkkm/instrumen'); ?>" class="z-btn z-btn-primary" style="font-size:0.85rem; padding:0.45rem 1rem; border-radius:8px; text-decoration:none; background:#0ea5e9; color:#fff; font-weight:700;">
                            Buka Form Instrumen
                        </a>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:1.25rem;">
                        <?php foreach($komponen as $k): 
                            $stats = $komponenStats[$k['id']] ?? ['total_indikator'=>0, 'indikator_terisi'=>0, 'avg_skor'=>0, 'percentage'=>0];
                        ?>
                        <div class="komponen-card">
                            <div>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <span style="font-size:0.75rem; font-weight:800; color:#0ea5e9; text-transform:uppercase; letter-spacing:0.5px;">Komponen <?php echo $k['urutan']; ?></span>
                                    <span style="font-size:0.8rem; font-weight:700; color:#0284c7;"><?php echo $stats['percentage']; ?>% Selesai</span>
                                </div>
                                <h3 style="font-size:1rem; font-weight:700; color:#1e293b; margin:0 0 0.5rem 0; line-height:1.4;">
                                    <?php echo htmlspecialchars($k['nama_komponen']); ?>
                                </h3>
                                <div class="p-bar-bg">
                                    <div class="p-bar-fill" style="width: <?php echo $stats['percentage']; ?>%;"></div>
                                </div>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1rem; padding-top:0.75rem; border-top:1px dashed #e2e8f0; font-size:0.8rem; color:#64748b;">
                                <div>Terisi: <strong><?php echo $stats['indikator_terisi']; ?> / <?php echo $stats['total_indikator']; ?></strong></div>
                                <div>Skor: <strong><?php echo $stats['avg_skor'] > 0 ? $stats['avg_skor'] : '-'; ?></strong></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- BERKAS TERAKHIR DIUNGGAH -->
                <div class="z-card" style="padding:1.5rem; background:#fff; border-radius:var(--z-radius, 12px); border:1px solid var(--z-border, #e2e8f0);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                        <h2 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin:0; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="clock" style="width:20px;height:20px;color:#0ea5e9;"></i> Bukti Fisik Terbaru
                        </h2>
                        <a href="<?php echo base_url('admin/pkkm/dokumen'); ?>" style="font-size:0.85rem; color:#0ea5e9; font-weight:700; text-decoration:none;">Kelola Semua Dokumen &rarr;</a>
                    </div>

                    <?php if (empty($recentFiles)): ?>
                    <div style="text-align:center; padding:3rem 1rem; color:#94a3b8;">
                        <i data-lucide="file-question" style="width:42px;height:42px;margin-bottom:0.75rem;opacity:0.6;"></i>
                        <p style="margin:0; font-size:0.9rem;">Belum ada dokumen bukti fisik yang diunggah.</p>
                        <a href="<?php echo base_url('admin/pkkm/dokumen'); ?>" class="z-btn z-btn-primary" style="display:inline-block; margin-top:1rem; font-size:0.8rem; padding:0.4rem 1rem; border-radius:8px; text-decoration:none; background:#0ea5e9; color:#fff;">Upload Dokumen Sekarang</a>
                    </div>
                    <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; text-align:left; color:#64748b;">
                                    <th style="padding:0.75rem 1rem;">Nama Dokumen Bukti</th>
                                    <th style="padding:0.75rem 1rem;">Tipe</th>
                                    <th style="padding:0.75rem 1rem;">Waktu Upload</th>
                                    <th style="padding:0.75rem 1rem; text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recentFiles as $rf): ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:0.85rem 1rem; font-weight:600; color:#1e293b;">
                                        <?php echo htmlspecialchars($rf['nama_berkas']); ?>
                                    </td>
                                    <td style="padding:0.85rem 1rem;">
                                        <span class="z-badge" style="font-size:0.75rem; padding:2px 8px; border-radius:6px; <?php echo $rf['tipe']==='link'?'background:#fef3c7;color:#b45309;':'background:#eff6ff;color:#1d4ed8;'; ?>">
                                            <?php echo strtoupper($rf['tipe']); ?>
                                        </span>
                                    </td>
                                    <td style="padding:0.85rem 1rem; color:#64748b; font-size:0.8rem;">
                                        <?php echo date('d M Y, H:i', strtotime($rf['created_at'])); ?>
                                    </td>
                                    <td style="padding:0.85rem 1rem; text-align:right;">
                                        <a href="<?php echo $rf['tipe']==='link'?htmlspecialchars($rf['url']):base_url($rf['url']); ?>" target="_blank" class="z-btn z-btn-secondary" style="font-size:0.75rem; padding:0.35rem 0.75rem; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; border:1px solid #cbd5e1; color:#0284c7;">
                                            <i data-lucide="external-link" style="width:12px;height:12px;"></i> Buka
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
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        function zToggleSidebar() {
            var s = document.getElementById('zSidebar');
            var o = document.getElementById('zOverlay');
            if (s) s.classList.toggle('open');
            if (o) o.classList.toggle('open');
        }
    </script>
</body>
</html>
