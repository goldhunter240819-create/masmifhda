<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $page_title ?? 'Instrumen Penilaian PKKM'; include __DIR__ . '/../_zamrud_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 4rem !important; }

        /* ELITE BLUE SULTAN BANNER */
        .elite-page-header {
            position: relative; padding: 2rem 2.2rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0284c7 100%);
            border-radius: 20px; overflow: hidden; color: #ffffff;
            box-shadow: 0 12px 30px -8px rgba(30, 58, 138, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            font-size: 1.7rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff;
        }
        .elite-page-header .header-text-premium p {
            font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem; margin-bottom: 0; max-width: 680px; font-weight: 400; line-height: 1.5;
        }
        .header-tag-pill {
            display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px);
        }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px;
            border-radius: 12px; font-weight: 700; font-size: 0.84rem; cursor: pointer;
            border: none; transition: all 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #0284c7; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #1e3a8a; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* STAT SUMMARY CARDS */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 16px 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); background: #fff; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }
        .stat-box.purple { background: linear-gradient(135deg, #faf5ff, #ffffff); border-color: #e9d5ff; color: #6b21a8; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff, #ffffff); border-color: #bfdbfe; color: #1e3a8a; }
        .stat-box.green { background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0; color: #14532d; }
        .stat-box.yellow { background: linear-gradient(135deg, #fefce8, #ffffff); border-color: #fef08a; color: #713f12; }
        .stat-icon { width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0; }
        .stat-box.purple .stat-icon { color: #8b5cf6; }
        .stat-box.blue .stat-icon { color: #3b82f6; }
        .stat-box.green .stat-icon { color: #38bdf8; }
        .stat-box.yellow .stat-icon { color: #f59e0b; }
        .stat-value { font-size: 1.65rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.74rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* PKKM CARD CONTAINER */
        .pkkm-card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        
        /* NAV PILLS FOR COMPONENTS */
        .nav-pills-wrap { border-bottom: 2px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 20px; }
        .nav-pills .nav-link {
            color: #475569; background: #f8fafc; font-weight: 700; font-size: 0.86rem; border-radius: 12px; padding: 10px 18px; border: 1px solid #e2e8f0; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px;
        }
        .nav-pills .nav-link:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #0284c7, #1d4ed8) !important; color: #ffffff !important; border-color: #1d4ed8 !important; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3) !important;
        }
        .badge-count { background: #e2e8f0; color: #475569; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 999px; }
        .nav-pills .nav-link.active .badge-count { background: rgba(255, 255, 255, 0.25) !important; color: #fff !important; }

        /* SUB-KOMPONEN BOX */
        .sub-komponen-box { border: 1px solid #e2e8f0; border-radius: 14px; margin-bottom: 22px; padding: 18px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .sub-komponen-header {
            font-weight: 800; color: #0f172a; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1; font-size: 0.95rem; line-height: 1.4;
        }
        .sub-komponen-header .badge-num {
            background: #0284c7; color: #ffffff; padding: 3px 9px; border-radius: 6px; margin-right: 8px; font-size: 0.76rem; font-weight: 800; letter-spacing: 0.5px;
        }

        /* TABLE & ROWS */
        .pkkm-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 0; }
        .pkkm-table thead th {
            background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.6px; padding: 10px 14px; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;
        }
        .indicator-header-row td {
            background: #f8fafc !important; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #e2e8f0; padding: 10px 14px;
        }
        .badge-ind-code {
            background: #0284c7; color: #ffffff; font-size: 0.78rem; font-weight: 800; padding: 3px 9px; border-radius: 7px; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
        }
        .ind-title-text { color: #0f172a; font-size: 0.92rem; font-weight: 700; line-height: 1.4; }
        .indicator-meta-pill {
            display: inline-flex; align-items: center; gap: 5px; background: #ffffff; color: #475569; padding: 3px 9px; border-radius: 6px; font-size: 0.74rem; font-weight: 600; border: 1px solid #e2e8f0;
        }
        .badge-system-sync {
            display: inline-flex; align-items: center; gap: 5px; background: #f0f9ff; color: #0284c7; padding: 3px 9px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; border: 1px solid #bae6fd; text-decoration: none; transition: 0.2s;
        }
        .badge-system-sync:hover { background: #e0f2fe; color: #0369a1; }

        .btn-banner-upload {
            background: #ffffff; color: #0284c7; border: 1px solid #93c5fd; padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.2s;
        }
        .btn-banner-upload:hover { background: #eff6ff; color: #1e40af; border-color: #0284c7; }

        .indicator-data-row td {
            vertical-align: top; padding: 14px 14px; background: #ffffff; border-bottom: 1px solid #f1f5f9;
        }
        .kriteria-content { font-size: 0.84rem; color: #334155; line-height: 1.55; font-weight: 500; }

        /* DROPZONE & FILE CHIPS */
        .empty-file-dropzone {
            border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 12px 10px; text-align: center; cursor: pointer; background: #f8fafc; transition: all 0.2s;
        }
        .empty-file-dropzone:hover { background: #f0f9ff; border-color: #38bdf8; }
        .empty-file-icon { color: #94a3b8; margin-bottom: 3px; }
        .empty-file-title { font-size: 0.78rem; font-weight: 700; color: #64748b; }
        .empty-file-hint { font-size: 0.72rem; color: #0284c7; font-weight: 700; }

        .file-list-group { display: flex; flex-direction: column; gap: 6px; }
        .berkas-item-modern {
            font-size: 0.78rem; background: #f8fafc; padding: 6px 9px; border-radius: 7px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; gap: 6px;
        }
        .berkas-item-link {
            color: #0f172a; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; flex: 1; min-width: 0;
        }
        .berkas-item-link:hover { color: #0284c7; text-decoration: underline; }
        .badge-auto { background: #e0f2fe; color: #0369a1; font-size: 0.68rem; font-weight: 800; padding: 2px 6px; border-radius: 5px; flex-shrink: 0; }
        .berkas-del-btn { background: none; border: none; color: #ef4444; padding: 0; cursor: pointer; display: flex; align-items: center; }
        .berkas-del-btn:hover { color: #b91c1c; }
        .btn-add-more-file {
            background: none; border: 1px dashed #cbd5e1; color: #0284c7; font-size: 0.74rem; font-weight: 700; border-radius: 6px; padding: 4px 8px; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;
        }
        .btn-add-more-file:hover { background: #eff6ff; border-color: #93c5fd; }

        /* SKOR SELECTOR */
        .select-skor {
            border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-weight: 800; cursor: pointer; background: #fff; font-size: 0.88rem; color: #334155; transition: all 0.2s; min-width: 60px; text-align: center;
        }
        .select-skor:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15); }
        .select-skor.score-filled { background: #f0f9ff; border-color: #38bdf8; color: #0369a1; font-weight: 800; }
        .skor-status-text { font-size: 0.68rem; color: #0284c7; font-weight: 700; height: 14px; margin-top: 2px; }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include __DIR__ . '/../_zamrud_sidebar.php'; ?>
    
    <main class="z-main">
        <?php $page_badge = 'PKKM Madrasah'; include __DIR__ . '/../../siakad/_siakad_header.php'; ?>

        <div class="z-scroll">
            
            <!-- ELITE SULTAN BANNER GLOBAL -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill">
                            <i data-lucide="award" style="width:13px;height:13px;"></i> JUKNIS PKKM TERBARU • TAHUN 1, 2, 3
                        </div>
                        <h1><i data-lucide="clipboard-check"></i> Instrumen Penilaian PKKM</h1>
                        <p>Kelola 42 indikator penilaian kinerja kepala madrasah, sinkronkan berkas bukti fisik terintegrasi, dan catat skor visitasi resmi.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2 flex-wrap">
                        <a href="<?php echo base_url('admin/pkkm/instrumen/cetak'); ?>" target="_blank" class="e-btn e-btn-primary" title="Cetak Format Resmi A4 Landscape Standar Asesor">
                            <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Lembar A4
                        </a>
                        <a href="<?php echo base_url('admin/pkkm/dokumen/sync'); ?>" class="e-btn e-btn-secondary" title="Sinkronkan bukti fisik otomatis dari modul sistem yang aktif">
                            <i data-lucide="refresh-cw" style="width:16px;height:16px;"></i> Auto-Sync Sistem
                        </a>
                        <button type="button" class="e-btn e-btn-secondary" data-bs-toggle="modal" data-bs-target="#modalIdentitas">
                            <i data-lucide="sliders" style="width:16px;height:16px;"></i> Identitas
                        </button>
                    </div>
                </div>
            </div>

            <!-- STAT SUMMARY CARDS -->
            <div class="stat-grid">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="layers" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalKomponen ?? 4; ?></div>
                        <div class="stat-label">Komponen Utama</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="folder-tree" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalSubKomponen ?? 21; ?></div>
                        <div class="stat-label">Sub-Komponen</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="list-checks" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalIndikator ?? 42; ?></div>
                        <div class="stat-label">Total Indikator</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="award" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value">
                            <?php echo $skorTotal ?? 0; ?> 
                            <span style="font-size:0.95rem; opacity:0.6; font-weight:600;">/ <?php echo $skorMax ?? 168; ?></span>
                        </div>
                        <div class="stat-label">Skor Riil (<?php echo $kategori ?? 'Cukup'; ?>)</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" style="background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; border-radius:12px;" role="alert">
                    <i data-lucide="check-circle" style="width:20px;height:20px;flex-shrink:0;"></i>
                    <div style="font-size:0.88rem; font-weight:600;"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="margin-left:auto;"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca; border-radius:12px;" role="alert">
                    <i data-lucide="alert-circle" style="width:20px;height:20px;flex-shrink:0;"></i>
                    <div style="font-size:0.88rem; font-weight:600;"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="margin-left:auto;"></button>
                </div>
            <?php endif; ?>

            <!-- MAIN CONTAINER WITH TABS -->
            <div class="pkkm-card">
                <?php if (empty($data)): ?>
                    <div class="text-center py-5" style="color:#94a3b8;">
                        <i data-lucide="folder-open" style="width:48px;height:48px; margin-bottom:10px;"></i>
                        <p>Belum ada data komponen instrumen PKKM.</p>
                    </div>
                <?php else: ?>
                    
                    <!-- Horizontal Navigation Tabs per Komponen -->
                    <div class="nav-pills-wrap">
                        <ul class="nav nav-pills" id="pkkmKomponenTabs" role="tablist" style="gap:10px;">
                            <?php $tabIdx = 0; foreach ($data as $kId => $komp): 
                                $countInd = 0;
                                foreach ($komp['sub_komponens'] as $s) { 
                                    $countInd += count($s['indikators']); 
                                }
                                $countInd += count($komp['indikators']);
                            ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $tabIdx === 0 ? 'active' : ''; ?>" id="tab-komp-<?php echo $kId; ?>" data-bs-toggle="pill" data-bs-target="#pane-komp-<?php echo $kId; ?>" type="button" role="tab">
                                        <span>Komponen <?php echo htmlspecialchars($komp['urutan']); ?></span>
                                        <span class="badge-count"><?php echo $countInd; ?> Indikator</span>
                                    </button>
                                </li>
                            <?php $tabIdx++; endforeach; ?>
                        </ul>
                    </div>

                    <!-- Tab Panes Content -->
                    <div class="tab-content" id="pkkmKomponenTabsContent">
                        <?php $tabIdx = 0; foreach ($data as $kId => $komp): ?>
                            <div class="tab-pane fade <?php echo $tabIdx === 0 ? 'show active' : ''; ?>" id="pane-komp-<?php echo $kId; ?>" role="tabpanel" tabindex="0">
                                
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:18px; padding:12px 16px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="background:#0284c7; color:#fff; padding:4px 10px; border-radius:8px; font-size:0.85rem; font-weight:800;"><?php echo htmlspecialchars($komp['urutan']); ?></span>
                                        <span style="font-weight:800; color:#0f172a; font-size:1.05rem;"><?php echo htmlspecialchars($komp['nama_komponen']); ?></span>
                                    </div>
                                    <span style="font-size:0.82rem; color:#0284c7; font-weight:700;">
                                        <?php echo count($komp['sub_komponens']); ?> Sub-Komponen
                                    </span>
                                </div>

                                <?php foreach ($komp['sub_komponens'] as $sub): ?>
                                    <div class="sub-komponen-box">
                                        <div class="sub-komponen-header">
                                            <div>
                                                <span class="badge-num"><?php echo htmlspecialchars($sub['urutan']); ?></span>
                                                <span><?php echo htmlspecialchars($sub['nama_sub']); ?></span>
                                            </div>
                                            <span style="font-size:0.75rem; color:#64748b; font-weight:700;">
                                                <?php echo count($sub['indikators']); ?> Indikator
                                            </span>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="pkkm-table">
                                                <thead>
                                                    <tr>
                                                        <th width="32%">Kriteria Penilaian</th>
                                                        <th width="38%">Bukti Fisik (Dokumen & Bukti Riil)</th>
                                                        <th width="20%">File Terlampir</th>
                                                        <th width="10%" class="text-center">Skor (1-4)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($sub['indikators'])): ?>
                                                        <tr><td colspan="4" class="text-center" style="color:#94a3b8; padding:20px;">Belum ada indikator pada sub-komponen ini.</td></tr>
                                                    <?php else: foreach ($sub['indikators'] as $ind): ?>
                                                        <?php include __DIR__ . '/pkkm_ind_row.php'; ?>
                                                    <?php endforeach; endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (!empty($komp['indikators'])): ?>
                                    <div class="sub-komponen-box">
                                        <div class="sub-komponen-header">
                                            <div>
                                                <span class="badge-num"><?php echo htmlspecialchars($komp['urutan']); ?></span>
                                                <span>Indikator Langsung Komponen</span>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="pkkm-table">
                                                <thead>
                                                    <tr>
                                                        <th width="32%">Kriteria Penilaian</th>
                                                        <th width="38%">Bukti Fisik (Dokumen & Bukti Riil)</th>
                                                        <th width="20%">File Terlampir</th>
                                                        <th width="10%" class="text-center">Skor (1-4)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($komp['indikators'] as $ind): ?>
                                                        <?php include __DIR__ . '/pkkm_ind_row.php'; ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php $tabIdx++; endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- Modal Upload Berkas Langsung ke Indikator -->
<div class="modal fade" id="modalBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:none; box-shadow:0 20px 40px rgba(0,0,0,0.15); overflow:hidden;">
            <form action="<?php echo base_url('admin/pkkm/berkas/save'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="indikator_id" id="inp_berkas_indikator_id" value="0">
                
                <div class="modal-header" style="background:linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0284c7 100%); color:#fff; padding:18px 24px;">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalBerkasTitle" style="font-size:1.1rem;">
                            Unggah Berkas Bukti Fisik
                        </h5>
                        <small id="modalBerkasSub" style="color:rgba(255,255,255,0.85); font-size:0.78rem;">
                            Lampirkan dokumen fisik atau tautan pendukung untuk indikator ini.
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="padding:22px 24px; background:#fff;">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.84rem; color:#334155;">Tipe Bukti</label>
                        <select name="tipe" id="tipe_lampiran" class="form-select" onchange="toggleTipeLampiran()" style="border-radius:10px; font-weight:600;">
                            <option value="file">File Dokumen (PDF, Word, Excel, Gambar)</option>
                            <option value="link">Tautan Eksternal / Google Drive / Modul</option>
                        </select>
                    </div>

                    <div id="wrap_file" class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.84rem; color:#334155;">Pilih File Bukti</label>
                        <input type="file" name="file" class="form-control" style="border-radius:10px;">
                        <small class="text-muted" style="font-size:0.75rem;">Maksimal 25MB. Format: PDF, DOCX, XLSX, JPG, PNG, ZIP.</small>
                    </div>

                    <div id="wrap_link" class="mb-3" style="display:none;">
                        <label class="form-label fw-bold" style="font-size:0.84rem; color:#334155;">URL / Link Tautan</label>
                        <input type="text" name="url" class="form-control" placeholder="https://drive.google.com/... atau tautan sistem" style="border-radius:10px;">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size:0.84rem; color:#334155;">Judul / Nama Keterangan File</label>
                        <input type="text" name="nama_file" id="inp_berkas_nama_file" class="form-control" placeholder="Contoh: SK Tim Pengembang Madrasah 2026" style="border-radius:10px;">
                        <small class="text-muted" style="font-size:0.75rem;">Biarkan kosong jika ingin memakai nama asli file.</small>
                    </div>
                </div>

                <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 24px;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius:10px; font-size:0.85rem;">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="background:#0284c7; border:none; border-radius:10px; font-size:0.85rem; padding:8px 20px;">
                        Simpan Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Identitas Penilai & Kamad -->
<div class="modal fade" id="modalIdentitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:18px; border:none; box-shadow:0 20px 40px rgba(0,0,0,0.15); overflow:hidden;">
            <form action="<?php echo base_url('admin/pkkm/identitas/save'); ?>" method="POST">
                <div class="modal-header" style="background:linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0284c7 100%); color:#fff; padding:18px 24px;">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" style="font-size:1.1rem;">
                            Pengaturan Identitas Penilaian PKKM
                        </h5>
                        <small style="color:rgba(255,255,255,0.85); font-size:0.78rem;">
                            Data kepala madrasah, penilai, dan institusi untuk lembar cetak resmi.
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="padding:22px 24px; background:#fff;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Nama Kepala Madrasah</label>
                            <input type="text" name="nama_kamad" class="form-control" value="<?php echo htmlspecialchars($identitas['nama_kamad']); ?>" required style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">NIP Kepala Madrasah</label>
                            <input type="text" name="nip_kamad" class="form-control" value="<?php echo htmlspecialchars($identitas['nip_kamad']); ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Nama Madrasah</label>
                            <input type="text" name="nama_madrasah" class="form-control" value="<?php echo htmlspecialchars($identitas['nama_madrasah']); ?>" required style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Alamat Madrasah</label>
                            <input type="text" name="alamat_madrasah" class="form-control" value="<?php echo htmlspecialchars($identitas['alamat_madrasah']); ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Nama Penilai / Pengawas</label>
                            <input type="text" name="nama_penilai" class="form-control" value="<?php echo htmlspecialchars($identitas['nama_penilai']); ?>" required style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">NIP Penilai</label>
                            <input type="text" name="nip_penilai" class="form-control" value="<?php echo htmlspecialchars($identitas['nip_penilai']); ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Jabatan Penilai</label>
                            <input type="text" name="jabatan_penilai" class="form-control" value="<?php echo htmlspecialchars($identitas['jabatan_penilai']); ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Tanggal Penilaian</label>
                            <input type="date" name="tgl_penilaian" class="form-control" value="<?php echo htmlspecialchars($identitas['tgl_penilaian']); ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold" style="font-size:0.82rem; color:#334155;">Tahun Penilaian</label>
                            <input type="text" name="tahun_penilaian" class="form-control" value="<?php echo htmlspecialchars($identitas['tahun_penilaian']); ?>" style="border-radius:8px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 24px;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius:10px; font-size:0.85rem;">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="background:#0284c7; border:none; border-radius:10px; font-size:0.85rem; padding:8px 20px;">
                        Simpan Identitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function zToggleSidebar(){
        document.getElementById('zSidebar').classList.toggle('open');
        document.getElementById('zOverlay').classList.toggle('open');
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function openModalBerkas(indikatorId, indicatorTitle = '') {
        document.getElementById('inp_berkas_indikator_id').value = indikatorId;
        document.getElementById('inp_berkas_nama_file').value = '';
        document.getElementById('tipe_lampiran').value = 'file';
        toggleTipeLampiran();
        if (indicatorTitle) {
            document.getElementById('modalBerkasSub').innerText = indicatorTitle;
        }
        new bootstrap.Modal(document.getElementById('modalBerkas')).show();
    }

    function toggleTipeLampiran() {
        var tipe = document.getElementById('tipe_lampiran').value;
        if (tipe === 'file') {
            document.getElementById('wrap_file').style.display = 'block';
            document.getElementById('wrap_link').style.display = 'none';
        } else {
            document.getElementById('wrap_file').style.display = 'none';
            document.getElementById('wrap_link').style.display = 'block';
        }
    }

    function saveSkor(indikatorId, skorVal) {
        var el = document.getElementById('select_skor_' + indikatorId);
        var statusEl = document.getElementById('skor_status_' + indikatorId);
        
        if (statusEl) {
            statusEl.innerText = 'Menyimpan...';
            statusEl.style.color = '#3b82f6';
        }

        if (parseInt(skorVal) > 0) {
            el.classList.add('score-filled');
        } else {
            el.classList.remove('score-filled');
        }

        $.ajax({
            url: '<?php echo base_url("admin/pkkm/nilai/save"); ?>',
            type: 'POST',
            data: { indikator_id: indikatorId, skor: skorVal },
            dataType: 'json',
            success: function(res) {
                if (statusEl) {
                    statusEl.innerText = 'Tersimpan ✓';
                    statusEl.style.color = '#38bdf8';
                    setTimeout(function() { statusEl.innerText = ''; }, 2000);
                }
            },
            error: function() {
                if (statusEl) {
                    statusEl.innerText = 'Gagal!';
                    statusEl.style.color = '#ef4444';
                }
            }
        });
    }
</script>

<?php include __DIR__ . '/_pkkm_panduan_modal.php'; ?>
</body>
</html>
