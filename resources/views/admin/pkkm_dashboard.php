<?php
$active_module = 'pkkm';
$active_page = 'overview';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page ?? 'Dashboard PKKM'; include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem !important; }

        /* Elite Banner Header */
        .elite-page-header {
            position: relative; padding: 2.2rem 2.5rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f766e 100%);
            border-radius: 20px; overflow: hidden; color: #ffffff;
            box-shadow: 0 12px 30px -8px rgba(6, 78, 59, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .elite-page-header .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.18) 0%, transparent 40%),
                              radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.12) 0%, transparent 40%);
            z-index: 1; pointer-events: none;
        }
        .elite-page-header .header-content-inner {
            position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;
        }
        .elite-page-header .header-text-premium h1 {
            font-size: 1.85rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff;
            letter-spacing: -0.5px;
        }
        .elite-page-header .header-text-premium p {
            font-size: 0.92rem; color: rgba(255,255,255,0.88); margin-top: 0.4rem; margin-bottom: 0; max-width: 680px; font-weight: 400; line-height: 1.55;
        }
        .header-tag-pill {
            display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.3); color: #fff; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px);
        }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #047857; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #064e3b; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* Stat Grid (4 Glass Cards) */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 18px 22px; border-radius: 16px; display: flex; align-items: flex-start; justify-content: space-between;
            background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); position: relative; overflow: hidden;
        }
        .stat-box:hover {
            transform: translateY(-3px); box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }
        .stat-box.purple { background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%); border-color: #e9d5ff; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-color: #bfdbfe; }
        .stat-box.green { background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border-color: #a7f3d0; }
        .stat-box.yellow { background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%); border-color: #fde68a; }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); flex-shrink: 0;
        }
        .stat-box.purple .stat-icon { background: #f3e8ff; color: #7c3aed; }
        .stat-box.blue .stat-icon { background: #dbeafe; color: #2563eb; }
        .stat-box.green .stat-icon { background: #d1fae5; color: #059669; }
        .stat-box.yellow .stat-icon { background: #fef3c7; color: #d97706; }

        .stat-val-row { display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap; }
        .stat-value { font-size: 1.85rem; font-weight: 800; line-height: 1.1; color: #0f172a; }
        .stat-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 6px; }
        .stat-desc { font-size: 0.8rem; color: #64748b; margin-top: 6px; font-weight: 500; }
        
        .kpi-pill {
            padding: 3px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;
        }
        .kpi-pill.emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .kpi-pill.amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .kpi-pill.blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .kpi-pill.purple { background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }

        /* Dual Trackers Bento Grid */
        .dual-trackers-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 18px; margin-bottom: 24px;
        }
        .tracker-card {
            background: #ffffff; border-radius: 18px; padding: 22px 26px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;
        }
        .tracker-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 10px;
        }
        .tracker-title-box h3 {
            font-size: 1.12rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;
        }
        .tracker-title-box p {
            font-size: 0.82rem; color: #64748b; margin: 3px 0 0 0;
        }
        .tracker-pct-badge {
            font-size: 1.35rem; font-weight: 900; line-height: 1;
        }
        
        .progress-track-wrapper {
            width: 100%; height: 16px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin: 14px 0 12px 0;
            position: relative;
        }
        .progress-track-fill {
            height: 100%; border-radius: 999px; transition: width 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .fill-emerald { background: linear-gradient(90deg, #10b981, #059669); }
        .fill-purple { background: linear-gradient(90deg, #8b5cf6, #d946ef); }

        .tracker-meta-row {
            display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #475569; font-weight: 600;
        }

        /* Table Card */
        .table-card-zamrud {
            background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;
        }
        .table-card-header {
            padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
            background: #ffffff;
        }
        .table-card-header h3 {
            font-size: 1.18rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;
        }
        .table-card-header p {
            font-size: 0.82rem; color: #64748b; margin: 3px 0 0 0;
        }

        .pkkm-table {
            width: 100%; border-collapse: collapse; font-size: 0.86rem;
        }
        .pkkm-table th {
            background: #f8fafc; color: #475569; font-weight: 700; padding: 14px 18px;
            text-align: left; border-bottom: 1.5px solid #e2e8f0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .pkkm-table td {
            padding: 16px 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .pkkm-table tbody tr:hover {
            background: #f8fafc;
        }

        .comp-code-badge {
            width: 32px; height: 32px; border-radius: 10px; background: #ecfdf5; color: #059669;
            font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; border: 1px solid #a7f3d0;
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px;
            font-size: 0.76rem; font-weight: 800; letter-spacing: 0.2px; white-space: nowrap;
        }
        .badge-status.lengkap {
            background: #dcfce7; color: #15803d; border: 1px solid #86efac;
        }
        .badge-status.proses {
            background: #fef3c7; color: #b45309; border: 1px solid #fde68a;
        }
        .badge-status.kosong {
            background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;
        }

        /* Mini progress bar inside table */
        .mini-prog-wrap {
            width: 100px; height: 7px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-top: 5px;
        }
        .mini-prog-fill {
            height: 100%; border-radius: 999px;
        }

        /* Action buttons */
        .btn-tbl-action {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 10px;
            background: #f1f5f9; color: #0f172a; font-weight: 700; font-size: 0.78rem; text-decoration: none;
            border: 1px solid #e2e8f0; transition: all 0.2s;
        }
        .btn-tbl-action:hover {
            background: #059669; color: #ffffff; border-color: #059669; transform: translateY(-1px);
        }

        /* Info Alert Box */
        .info-callout {
            background: #f0fdf4; border: 1px dashed #86efac; border-radius: 14px; padding: 16px 20px;
            display: flex; align-items: flex-start; gap: 14px; font-size: 0.84rem; color: #166534; line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include __DIR__ . '/_zamrud_sidebar.php'; ?>
    
    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__.'/../siakad/_siakad_header.php'; ?>

        <div class="z-scroll">
            <div class="container-fluid px-4 pt-3">
                
                <!-- 1. ELITE PAGE HEADER BANNER -->
                <div class="elite-page-header">
                    <div class="header-bg-shapes"></div>
                    <div class="header-content-inner">
                        <div class="header-text-premium">
                            <span class="header-tag-pill">
                                <i data-lucide="sparkles" style="width:13px;height:13px;"></i> Aplikasi PKKM • TP 2026/2027
                            </span>
                            <h1>
                                <i data-lucide="layout-dashboard" style="width:28px;height:28px;"></i>
                                Dashboard & Kelengkapan PKKM
                            </h1>
                            <p>
                                Monitoring komprehensif keterpenuhan berkas per butir indikator, pelacakan dokumen master, dan simulasi skor penilaian kinerja kepala madrasah.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="/admin/pkkm/instrumen" class="e-btn e-btn-primary">
                                <i data-lucide="clipboard-list" style="width:16px;height:16px;"></i> Instrumen Penilaian
                            </a>
                            <a href="/admin/pkkm/master-berkas" class="e-btn e-btn-secondary">
                                <i data-lucide="folder-open" style="width:16px;height:16px;"></i> Master Berkas
                            </a>
                            <a href="/admin/pkkm/bukti-digital" class="e-btn e-btn-secondary">
                                <i data-lucide="shield-check" style="width:16px;height:16px;"></i> Bukti Digital
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. STAT KPI GRID (4 MODERN GLASS CARDS) -->
                <div class="stat-grid">
                    
                    <!-- Card 1: Komponen -->
                    <div class="stat-box purple">
                        <div>
                            <div class="stat-label">Standar Komponen</div>
                            <div class="stat-val-row">
                                <span class="stat-value"><?php echo $totalKomponen; ?></span>
                                <span class="kpi-pill purple">4 Pilar Kemenag</span>
                            </div>
                            <div class="stat-desc">Usaha, Manajerial, Wirausaha, Supervisi</div>
                        </div>
                        <div class="stat-icon">
                            <i data-lucide="layers" style="width:22px;height:22px;"></i>
                        </div>
                    </div>

                    <!-- Card 2: Indikator Terpenuhi (Akurat) -->
                    <div class="stat-box green">
                        <div>
                            <div class="stat-label">Indikator Terpenuhi</div>
                            <div class="stat-val-row">
                                <span class="stat-value"><?php echo $totalIndikatorTerpenuhi; ?></span>
                                <span style="font-size:1.1rem; color:#64748b; font-weight:700;">/ <?php echo $totalIndikator; ?></span>
                                <span class="kpi-pill emerald"><?php echo $persenKelengkapanGlobal; ?>%</span>
                            </div>
                            <div class="stat-desc">
                                <?php if($totalIndikatorBelum > 0): ?>
                                    <strong style="color:#d97706;"><?php echo $totalIndikatorBelum; ?></strong> indikator belum ada berkas
                                <?php else: ?>
                                    <strong style="color:#059669;">100% Seluruh Indikator Terpenuhi!</strong>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i data-lucide="check-circle-2" style="width:22px;height:22px;"></i>
                        </div>
                    </div>

                    <!-- Card 3: Total Berkas File -->
                    <div class="stat-box blue">
                        <div>
                            <div class="stat-label">Total Berkas Diunggah</div>
                            <div class="stat-val-row">
                                <span class="stat-value"><?php echo $totalBerkas; ?></span>
                                <span class="kpi-pill blue">File & Tautan</span>
                            </div>
                            <div class="stat-desc">Termasuk berkas manual & arsip master</div>
                        </div>
                        <div class="stat-icon">
                            <i data-lucide="folder-check" style="width:22px;height:22px;"></i>
                        </div>
                    </div>

                    <!-- Card 4: Simulasi Skor & Predikat -->
                    <div class="stat-box yellow">
                        <div>
                            <div class="stat-label">Simulasi Skor (1–4)</div>
                            <div class="stat-val-row">
                                <span class="stat-value"><?php echo $skorTotal; ?></span>
                                <span style="font-size:1rem; color:#64748b; font-weight:700;">/ <?php echo $skorMax; ?></span>
                                <span class="kpi-pill amber"><?php echo $kategori; ?></span>
                            </div>
                            <div class="stat-desc">Capaian skor simulasi <?php echo $persentase; ?>%</div>
                        </div>
                        <div class="stat-icon">
                            <i data-lucide="award" style="width:22px;height:22px;"></i>
                        </div>
                    </div>

                </div>

                <!-- 3. DUAL TRACKERS BENTO GRID -->
                <div class="dual-trackers-grid">
                    
                    <!-- Tracker 1: Kelengkapan Bukti Fisik Riil -->
                    <div class="tracker-card">
                        <div>
                            <div class="tracker-header">
                                <div class="tracker-title-box">
                                    <h3><i data-lucide="check-square" style="width:20px;height:20px;color:#059669;"></i> Kelengkapan Butir Indikator</h3>
                                    <p>Proporsi butir indikator yang telah memiliki minimal 1 berkas bukti fisik terlampir.</p>
                                </div>
                                <div class="tracker-pct-badge" style="color:#059669;">
                                    <?php echo $persenKelengkapanGlobal; ?>%
                                </div>
                            </div>
                            <div class="progress-track-wrapper">
                                <div class="progress-track-fill fill-emerald" style="width: <?php echo $persenKelengkapanGlobal; ?>%;"></div>
                            </div>
                        </div>
                        <div class="tracker-meta-row">
                            <span style="color:#059669;">✓ <?php echo $totalIndikatorTerpenuhi; ?> Indikator Terisi</span>
                            <span style="color:#d97706;">⏳ <?php echo $totalIndikatorBelum; ?> Indikator Kosong</span>
                            <span style="color:#64748b;">Total: <?php echo $totalIndikator; ?> Item</span>
                        </div>
                    </div>

                    <!-- Tracker 2: Progress Skor Simulasi -->
                    <div class="tracker-card">
                        <div>
                            <div class="tracker-header">
                                <div class="tracker-title-box">
                                    <h3><i data-lucide="trending-up" style="width:20px;height:20px;color:#7c3aed;"></i> Capaian Skor Simulasi Penilaian</h3>
                                    <p>Persentase perolehan nilai berdasarkan skor 1-4 yang telah diinputkan pada instrumen.</p>
                                </div>
                                <div class="tracker-pct-badge" style="color:#7c3aed;">
                                    <?php echo $persentase; ?>%
                                </div>
                            </div>
                            <div class="progress-track-wrapper">
                                <div class="progress-track-fill fill-purple" style="width: <?php echo $persentase; ?>%;"></div>
                            </div>
                        </div>
                        <div class="tracker-meta-row">
                            <span style="color:#7c3aed;">Skor Saat Ini: <?php echo $skorTotal; ?></span>
                            <span style="color:#64748b;">Maksimal: <?php echo $skorMax; ?> Poin</span>
                            <span style="color:#b45309;">Predikat: <?php echo $kategori; ?></span>
                        </div>
                    </div>

                </div>

                <!-- 4. TABEL STATUS KELENGKAPAN PER KOMPONEN -->
                <div class="table-card-zamrud">
                    <div class="table-card-header">
                        <div>
                            <h3><i data-lucide="layers" style="width:20px;height:20px;color:#059669;"></i> Status Kelengkapan per Komponen PKKM</h3>
                            <p>Status dihitung berdasarkan keterisian seluruh butir indikator di masing-masing komponen.</p>
                        </div>
                        <div style="font-size:0.8rem; color:#64748b;">
                            <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:5px 10px; font-weight:700;">
                                <i data-lucide="check" style="width:12px;height:12px;display:inline;"></i> Logika Per-Indikator Aktif
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="pkkm-table">
                            <thead>
                                <tr>
                                    <th width="5%" style="text-align:center;">No</th>
                                    <th width="35%">Komponen Penilaian</th>
                                    <th width="22%">Indikator Terpenuhi</th>
                                    <th width="15%" style="text-align:center;">Berkas Terlampir</th>
                                    <th width="13%" style="text-align:center;">Status Akurat</th>
                                    <th width="10%" style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($komponenStats as $idx => $k): 
                                    $progColor = $k['status_class'] === 'lengkap' ? '#10b981' : ($k['status_class'] === 'proses' ? '#f59e0b' : '#cbd5e1');
                                ?>
                                <tr>
                                    <td style="text-align:center; font-weight:700; color:#64748b;">
                                        <?php echo $idx + 1; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="comp-code-badge">
                                                <?php echo htmlspecialchars($k['urutan'] ?? ($idx + 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:#0f172a; font-size:0.92rem;">
                                                    <?php echo htmlspecialchars($k['nama_komponen']); ?>
                                                </div>
                                                <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                                                    Total <?php echo $k['jml_indikator']; ?> Butir Kriteria
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; justify-content:space-between; font-size:0.82rem; font-weight:700; color:#334155;">
                                            <span><?php echo $k['indikator_terpenuhi']; ?> / <?php echo $k['jml_indikator']; ?> Item</span>
                                            <span style="color:<?php echo $progColor; ?>;"><?php echo $k['persen_kelengkapan']; ?>%</span>
                                        </div>
                                        <div class="mini-prog-wrap" style="width:100%;">
                                            <div class="mini-prog-fill" style="width:<?php echo $k['persen_kelengkapan']; ?>%; background:<?php echo $progColor; ?>;"></div>
                                        </div>
                                        <div style="font-size:0.72rem; color:#64748b; margin-top:3px;">
                                            <?php if($k['indikator_sisa'] > 0): ?>
                                                <span>Sisa <?php echo $k['indikator_sisa']; ?> indikator kosong</span>
                                            <?php else: ?>
                                                <span style="color:#059669; font-weight:700;">✓ Seluruh indikator terisi</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="font-weight:800; font-size:1rem; color:#0f172a;">
                                            <?php echo $k['jml_berkas']; ?> File
                                        </div>
                                        <?php if($k['jml_berkas'] > $k['indikator_terpenuhi']): ?>
                                            <div style="font-size:0.7rem; color:#64748b;" title="Beberapa indikator memiliki lebih dari 1 file bukti fisik">
                                                (ada >1 berkas/item)
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if ($k['status_class'] === 'lengkap'): ?>
                                            <span class="badge-status lengkap">
                                                <i data-lucide="check-check" style="width:14px;height:14px;"></i> Lengkap
                                            </span>
                                        <?php elseif ($k['status_class'] === 'proses'): ?>
                                            <span class="badge-status proses">
                                                <i data-lucide="clock" style="width:13px;height:13px;"></i> Proses (<?php echo $k['indikator_terpenuhi']; ?>/<?php echo $k['jml_indikator']; ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status kosong">
                                                <i data-lucide="alert-circle" style="width:13px;height:13px;"></i> Belum Ada
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="/admin/pkkm/instrumen" class="btn-tbl-action" title="Buka instrumen komponen ini">
                                            <span>Buka</span>
                                            <i data-lucide="arrow-right" style="width:12px;height:12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. CALLOUT EDUKATIF (MENGAPA BERKAS > INDIKATOR) -->
                <div class="info-callout">
                    <div style="width:36px; height:36px; border-radius:10px; background:#dcfce7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i data-lucide="lightbulb" style="width:20px;height:20px;color:#059669;"></i>
                    </div>
                    <div>
                        <div style="font-weight:800; font-size:0.9rem; color:#064e3b; margin-bottom:3px;">
                            💡 Mengapa Jumlah Berkas Bisa Lebih Banyak dari Jumlah Indikator?
                        </div>
                        <div style="color:#166534; line-height:1.5;">
                            Dalam instrumen PKKM, satu butir indikator seringkali memiliki beberapa dokumen persyaratan fisik (misalnya bukti a, b, dan c). Sistem kami kini menghitung kelengkapan secara <strong>adil dan akurat</strong> berdasarkan pemenuhan setiap butir indikator (komponen baru berstatus <em>Lengkap</em> jika seluruh indikator telah memiliki berkas), sehingga jumlah berkas yang banyak pada satu indikator tidak akan membuat komponen dianggap lengkap sebelum seluruh indikator lainnya terpenuhi.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
function zToggleSidebar(){
    document.getElementById('zSidebar').classList.toggle('open');
    document.getElementById('zOverlay').classList.toggle('open');
}
lucide.createIcons();
</script>
</body>
</html>
