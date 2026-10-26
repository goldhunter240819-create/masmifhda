<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page ?? 'Daftar Berkas PKKM'; include (file_exists(__DIR__ . '/_zamrud_head.php') ? __DIR__ . '/_zamrud_head.php' : __DIR__ . '/../_zamrud_head.php'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Zamrud UI Integration Overrides */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem !important; }
        
        /* Minimalist Elite Page Header */
        .elite-page-header {
            position: relative; padding: 1.1rem 1.6rem; margin-bottom: 1.1rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border-radius: 14px; overflow: hidden; color: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(30, 58, 138, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .elite-page-header .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.12) 0%, transparent 40%),
                              radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.1) 0%, transparent 40%);
            z-index: 1; pointer-events: none;
        }
        .elite-page-header .header-content-inner {
            position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;
        }
        .elite-page-header .header-text-premium h1 {
            font-size: 1.25rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; color: #fff;
        }
        .elite-page-header .header-text-premium p {
            font-size: 0.8rem; color: rgba(255,255,255,0.85); margin-top: 0.2rem; margin-bottom: 0; max-width: 620px; font-weight: 400; line-height: 1.4;
        }
        .header-tag-pill {
            display: inline-flex; align-items: center; gap: 5px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 4px; backdrop-filter: blur(4px);
        }

        /* Action Buttons on Header */
        .e-btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
            border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-1px); }
        .e-btn-primary { background: #ffffff; color: #1e3a8a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .e-btn-primary:hover { background: #f8fafc; color: #0f172a; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* Minimalist Stat Summary */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; margin-bottom: 14px;
        }
        .stat-box {
            padding: 9px 13px; border-radius: 10px; display: flex; align-items: center; gap: 10px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); background: #fff; border: 1px solid #e2e8f0; transition: transform 0.15s, box-shadow 0.15s;
        }
        .stat-box:hover {
            transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        .stat-box blue { background: #faf5ff; border-color: #f3e8ff; color: #6b21a8; }
        .stat-box.blue { background: #eff6ff; border-color: #dbeafe; color: #1e3a8a; }
        .stat-box.green { background: #f0f9ff; border-color: #e0f2fe; color: #0c4a6e; }
        .stat-box.yellow { background: #fefce8; border-color: #fef9c3; color: #713f12; }
        .stat-box.emerald { background: #f0f9ff; border-color: #e0f2fe; color: #1e40af; }
        .stat-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.85); box-shadow: 0 1px 3px rgba(0,0,0,0.03); flex-shrink: 0; }
        .stat-icon i, .stat-icon svg { width: 16px !important; height: 16px !important; }
        .stat-box blue .stat-icon { color: #8b5cf6; }
        .stat-box.blue .stat-icon { color: #3b82f6; }
        .stat-box.green .stat-icon { color: #38bdf8; }
        .stat-box.yellow .stat-icon { color: #f59e0b; }
        .stat-box.emerald .stat-icon { color: #0284c7; }
        .stat-value { font-size: 1.25rem; font-weight: 800; line-height: 1.1; margin-bottom: 1px; }
        .stat-label { font-size: 0.68rem; font-weight: 600; color: #64748b; white-space: nowrap; }

        /* Minimalist Filter Pills */
        .nav-filter-pills { display: flex; gap: 5px; flex-wrap: wrap; }
        .filter-pill-btn {
            border: 1px solid #e2e8f0; background: #ffffff; color: #475569;
            padding: 5px 11px; border-radius: 8px; font-weight: 600; font-size: 0.74rem;
            display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.15s;
        }
        .filter-pill-btn:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .filter-pill-btn.active {
            background: #0284c7; color: #ffffff; border-color: #1e3a8a;
            box-shadow: 0 2px 6px rgba(56, 189, 248, 0.2);
        }
        .filter-pill-btn .badge-num {
            background: #f1f5f9; color: #475569; font-size: 0.68rem; font-weight: 700;
            padding: 1px 5px; border-radius: 999px;
        }
        .filter-pill-btn.active .badge-num {
            background: rgba(255, 255, 255, 0.25); color: #ffffff;
        }

        /* Minimalist Category Filter Bar */
        .category-filter-wrap {
            display: flex; gap: 5px; flex-wrap: wrap; padding: 7px 10px; background: #f8fafc;
            border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 8px; margin-bottom: 10px;
        }
        .cat-filter-btn {
            display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px;
            border-radius: 6px; font-size: 0.7rem; font-weight: 600; border: 1px solid #e2e8f0;
            background: #ffffff; color: #475569; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;
        }
        .cat-filter-btn:hover {
            background: #f1f5f9; border-color: #cbd5e1; color: #0f172a;
        }
        .cat-filter-btn.active {
            background: #0f172a; color: #ffffff; border-color: #0f172a;
        }
        .cat-filter-btn .cat-num {
            padding: 0 4px; border-radius: 999px; background: #f1f5f9; font-size: 0.64rem; color: #475569; font-weight: 700;
        }
        .cat-filter-btn.active .cat-num {
            background: rgba(255,255,255,0.22); color: #ffffff;
        }

        /* Glass Card & Modern Table */
        .glass-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            padding: 16px;
        }
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 3px;
            margin-top: 4px;
        }
        .modern-table thead th {
            background: transparent;
            padding: 6px 10px;
            font-weight: 700;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            border-bottom: 1px solid #e2e8f0;
        }
        .modern-table tbody tr {
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.015);
            transition: all 0.15s ease;
            outline: 1px solid #f1f5f9;
            border-radius: 8px;
        }
        .modern-table tbody tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            outline: 1px solid #cbd5e1;
        }
        .modern-table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
            border: none;
            color: #334155;
            font-size: 0.78rem;
        }
        .modern-table tbody td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        .modern-table tbody td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        
        .num-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; border-radius: 6px;
            background: #f1f5f9; color: #475569; font-weight: 700; font-size: 0.72rem;
        }
        .doc-title {
            font-weight: 700; color: #0f172a; font-size: 0.84rem; line-height: 1.3;
        }
        .evidence-card-badge {
            display: inline-flex; align-items: center; gap: 3px;
            background: #f0f9ff; color: #1e3a8a; border: 1px solid #bae6fd;
            border-radius: 4px; padding: 1px 6px; font-size: 0.62rem; font-weight: 700;
            cursor: pointer; transition: all 0.15s ease;
        }
        .evidence-card-badge:hover {
            background: #0284c7; color: #ffffff; border-color: #0284c7; transform: translateY(-1px);
        }
        .badge-cat-pill {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 0.62rem; font-weight: 700; padding: 1px 6px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 0.3px; vertical-align: middle;
        }
        .cat-sk { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
        .cat-notulen { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .cat-program { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .cat-laporan { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .cat-digital { background: #f0f9ff; color: #0f766e; border: 1px solid #99f6e4; }
        .cat-sarpras { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .cat-umum { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        .doc-sub-desc {
            font-size: 0.71rem; color: #64748b; line-height: 1.35; margin-top: 2px; margin-bottom: 2px;
        }
        .ind-badges-wrap {
            display: flex; flex-wrap: wrap; gap: 4px; align-items: center; margin-top: 3px;
        }
        .ind-pill-interactive {
            display: inline-flex; align-items: center; gap: 3px;
            background: #f8fafc; color: #334155; border: 1px solid #cbd5e1;
            font-size: 0.65rem; font-weight: 600; padding: 1px 5px; border-radius: 4px;
            transition: all 0.15s ease; cursor: pointer; text-decoration: none;
        }
        .ind-pill-interactive:hover {
            background: #f0f9ff; color: #1e3a8a; border-color: #7dd3fc;
        }
        .unmatched-pill {
            display: inline-flex; align-items: center; gap: 3px;
            background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
            font-size: 0.65rem; font-weight: 600; padding: 1px 5px; border-radius: 4px; margin-top: 2px;
        }
        .badge-pending {
            display: inline-flex; align-items: center; gap: 4px;
            background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
            padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.7rem;
        }

        /* Slim Digital Proof Card */
        .digital-proof-card {
            margin-top: 4px; padding: 4px 8px; border-radius: 6px;
            background: #eff6ff; border: 1px solid #bfdbfe;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
        }
        .digital-proof-title {
            font-size: 0.7rem; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 4px;
        }
        .btn-digital-preview {
            display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px;
            border-radius: 5px; background: #ffffff; color: #1e3a8a; border: 1px solid #93c5fd;
            font-size: 0.66rem; font-weight: 600; text-decoration: none;
        }
        .btn-digital-preview:hover {
            background: #0284c7; color: #ffffff; border-color: #0284c7;
        }
        .btn-digital-attach {
            display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px;
            border-radius: 5px; background: #0284c7; color: #ffffff; border: none;
            font-size: 0.66rem; font-weight: 700; cursor: pointer;
        }
        .btn-digital-attach:hover {
            background: #1e3a8a;
        }

        /* File Chips */
        .file-list-wrap { display: flex; flex-direction: column; gap: 4px; }
        .file-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f8fafc; padding: 3px 7px; border-radius: 6px;
            border: 1px solid #e2e8f0; max-width: 240px; transition: all 0.15s;
        }
        .file-chip:hover {
            border-color: #cbd5e1; background: #ffffff;
        }
        .file-chip-link {
            display: inline-flex; align-items: center; gap: 5px;
            color: #1d4ed8; font-weight: 600; font-size: 0.73rem; text-decoration: none; overflow: hidden;
        }
        .file-chip-icon {
            width: 20px; height: 20px; border-radius: 4px; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .file-chip-icon i, .file-chip-icon svg { width: 11px !important; height: 11px !important; }
        .icon-file { background: #eff6ff; color: #2563eb; }
        .icon-link { background: #f0f9ff; color: #0284c7; }
        .file-chip-name {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;
        }
        .file-chip-del {
            background: transparent; border: none; color: #94a3b8; padding: 2px;
            border-radius: 3px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .file-chip-del:hover { background: #fee2e2; color: #ef4444; }

        .btn-upload-more {
            background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1;
            padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.73rem;
            display: inline-flex; align-items: center; gap: 4px; cursor: pointer; transition: all 0.15s; text-decoration: none;
        }
        .btn-upload-more:hover {
            background: #0284c7; color: #ffffff; border-color: #0284c7;
        }

        /* Modern DataTables Inputs */
        div.dataTables_wrapper { font-family: 'Plus Jakarta Sans', sans-serif !important; font-size: 0.78rem; }
        div.dataTables_wrapper div.dataTables_length select {
            border-radius: 8px; padding: 4px 28px 4px 8px; background-color: #f8fafc; border-color: #cbd5e1; font-weight: 600; font-size: 0.78rem;
        }
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 8px; padding: 5px 12px; background-color: #f8fafc; border: 1px solid #cbd5e1; width: 200px; font-weight: 500; font-size: 0.78rem;
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            outline: none; border-color: #38bdf8; background-color: #ffffff; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }
        div.dataTables_wrapper div.dataTables_info { padding-top: 12px; color: #64748b; font-size: 0.78rem; font-weight: 600; }
        div.dataTables_wrapper .page-item.active .page-link {
            background-color: #0284c7 !important; border-color: #0284c7 !important; border-radius: 6px; font-weight: 700; color: #fff !important; font-size: 0.75rem;
        }
        div.dataTables_wrapper .page-link {
            color: #475569; border: 1px solid transparent; margin: 0 2px; border-radius: 6px; padding: 4px 10px; font-weight: 600; font-size: 0.75rem; transition: all 0.15s ease;
        }
        div.dataTables_wrapper .page-link:hover { background-color: #f1f5f9; color: #0284c7; }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include (file_exists(__DIR__ . '/_zamrud_sidebar.php') ? __DIR__ . '/_zamrud_sidebar.php' : __DIR__ . '/../_zamrud_sidebar.php'); ?>
    
    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include (file_exists(__DIR__ . '/../siakad/_siakad_header.php') ? __DIR__ . '/../siakad/_siakad_header.php' : __DIR__ . '/../../siakad/_siakad_header.php'); ?>

        <div class="z-scroll">
            
            <!-- MINIMALIST HEADER BANNER -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill">
                            <i data-lucide="folder-open" style="width:12px;height:12px;"></i> MASTER BUKTI FISIK • PKKM TAHUNAN
                        </div>
                        <h1><i data-lucide="archive" style="width:20px;height:20px;"></i> Daftar Berkas PKKM</h1>
                        <p>Pusat dokumen master terdistribusi otomatis ke 42 indikator penilaian kinerja.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2 flex-wrap">
                        <button type="button" class="e-btn e-btn-secondary" onclick="openKoleksiBuktiModal()">
                            <i data-lucide="sparkles" style="width:14px;height:14px;color:#fde047;"></i> Link Bukti (13)
                        </button>
                        <button type="button" class="e-btn e-btn-secondary" onclick="confirmSyncMasterBerkas()">
                            <i data-lucide="refresh-cw" style="width:14px;height:14px;"></i> Sinkronkan
                        </button>
                        <button type="button" class="e-btn e-btn-primary" onclick="openModalTambahBerkas()">
                            <i data-lucide="plus-circle" style="width:14px;height:14px;"></i> + Tambah Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- MINIMALIST STAT SUMMARY TILES -->
            <div class="stat-grid">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="files"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalMaster ?? count($master_berkas); ?></div>
                        <div class="stat-label">Total Berkas</div>
                    </div>
                </div>
                <div class="stat-box emerald">
                    <div class="stat-icon"><i data-lucide="sparkles"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalDigitalReady ?? 0; ?></div>
                        <div class="stat-label">Bukti Digital Siap</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="check-circle-2"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalUploaded ?? 0; ?> <span style="font-size:0.75rem; font-weight:600; opacity:0.8;">(<?= $percentUploaded ?? 0; ?>%)</span></div>
                        <div class="stat-label">Sudah Ada File</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="alert-circle"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalPending ?? 0; ?></div>
                        <div class="stat-label">Belum Ada File</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="paperclip"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalFileCount ?? 0; ?></div>
                        <div class="stat-label">Total Terlampir</div>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD WITH TABLE -->
            <div class="glass-card">
                <!-- Filter Status & Quick Actions -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="nav-filter-pills">
                        <button type="button" class="filter-pill-btn active" data-filter="all">
                            Semua <span class="badge-num"><?= $totalMaster ?? count($master_berkas); ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="uploaded">
                            <i data-lucide="check-circle" style="width:12px;height:12px;"></i> Terisi <span class="badge-num"><?= $totalUploaded ?? 0; ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="pending">
                            <i data-lucide="clock" style="width:12px;height:12px;"></i> Belum Ada <span class="badge-num"><?= $totalPending ?? 0; ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="digital_ready" style="border-color:#93c5fd; color:#1e40af;">
                            <i data-lucide="sparkles" style="width:12px;height:12px;color:#0284c7;"></i> Digital Siap <span class="badge-num" style="background:#dbeafe; color:#1e40af;"><?= $totalDigitalReady ?? 0; ?></span>
                        </button>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" onclick="openKoleksiBuktiModal()" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 fw-bold" style="border-radius:8px; padding:5px 10px; font-size:0.74rem; background:#eff6ff; border-color:#93c5fd; color:#1d4ed8;">
                            <i data-lucide="sparkles" style="width:12px;height:12px;color:#0284c7;"></i> Link Bukti (13)
                        </button>
                        <a href="/admin/pkkm/instrumen" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 fw-bold" style="border-radius:8px; padding:5px 10px; font-size:0.74rem;">
                            <i data-lucide="clipboard-list" style="width:12px;height:12px;"></i> Instrumen PKKM
                        </a>
                    </div>
                </div>

                <!-- Category Filter Pills Bar (Compact & Sleek) -->
                <div class="category-filter-wrap">
                    <button type="button" class="cat-filter-btn active" data-category="all">
                        Semua <span class="cat-num"><?= $totalMaster ?? count($master_berkas); ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="sk">
                        📜 SK & Regulasi <span class="cat-num"><?= $categoryCounts['sk'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="notulen">
                        📝 Notulen & BA <span class="cat-num"><?= $categoryCounts['notulen'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="program">
                        📅 Program Kerja <span class="cat-num"><?= $categoryCounts['program'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="laporan">
                        📈 Laporan Mutu <span class="cat-num"><?= $categoryCounts['laporan'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="digital">
                        💻 Bukti TIK <span class="cat-num"><?= $categoryCounts['digital'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="sarpras">
                        🏢 Sarpras <span class="cat-num"><?= $categoryCounts['sarpras'] ?? 0; ?></span>
                    </button>
                    <?php if(!empty($categoryCounts['umum'])): ?>
                        <button type="button" class="cat-filter-btn" data-category="umum">
                            📁 Lainnya <span class="cat-num"><?= $categoryCounts['umum']; ?></span>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table id="tableBerkas" class="modern-table">
                        <thead>
                            <tr>
                                <th style="width:36px; text-align:center;">No</th>
                                <th>Nama Dokumen Master & Penjelasan</th>
                                <th style="width:240px;">Status & File Terunggah</th>
                                <th style="width:90px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($master_berkas as $mb): 
                                $hasFiles = !empty($mb['files']);
                                $statusType = $hasFiles ? 'uploaded' : 'pending';
                                $matchedCount = $mb['matched_count'] ?? 0;
                                $isDigitalReady = !empty($mb['bukti_digital']);
                            ?>
                                <tr data-status="<?= $statusType; ?>" data-category="<?= htmlspecialchars($mb['kategori'] ?? 'umum'); ?>" data-digital="<?= $isDigitalReady ? 'yes' : 'no'; ?>">
                                    <td style="text-align:center;">
                                        <span class="num-badge"><?= $no++; ?></span>
                                    </td>
                                    <td>
                                        <!-- Header line: Kategori + Judul + Panduan Button -->
                                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                            <span class="badge-cat-pill cat-<?= htmlspecialchars($mb['kategori'] ?? 'umum'); ?>">
                                                <?= htmlspecialchars($mb['kategori_label'] ?? 'Dokumen'); ?>
                                            </span>
                                            <span class="doc-title">
                                                <?= htmlspecialchars($mb['judul_tampilan'] ?: $mb['nama_berkas']); ?>
                                            </span>
                                            <span class="evidence-card-badge" onclick="showPanduanBukti('<?= htmlspecialchars(addslashes($mb['nama_berkas'])); ?>', null, <?= $mb['id']; ?>)" role="button" title="Petunjuk & Checklist" style="cursor:pointer; padding:1px 5px; font-size:0.62rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                                <span>Panduan</span>
                                            </span>
                                        </div>

                                        <!-- Deskripsi Bantuan & Poin Juknis -->
                                        <div class="doc-sub-desc">
                                            <?= htmlspecialchars($mb['deskripsi'] ?? 'Dokumen resmi bukti fisik pelaksanaan program madrasah.'); ?>
                                            <?php if(!empty($mb['judul_tampilan']) && $mb['judul_tampilan'] !== $mb['nama_berkas']): ?>
                                                <span style="color:#94a3b8; font-size:0.67rem; margin-left:4px;">
                                                    &bull; Juknis: <i><?= htmlspecialchars($mb['nama_berkas']); ?></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Badge Interaktif Indikator Terkait -->
                                        <?php if(!empty($mb['matched_indicators'])): ?>
                                            <div class="ind-badges-wrap">
                                                <span style="font-size:0.66rem; font-weight:700; color:#64748b;">
                                                    <i data-lucide="link-2" style="width:10px;height:10px;display:inline-block;vertical-align:middle;"></i> Tertaut:
                                                </span>
                                                <?php foreach($mb['matched_indicators'] as $mind): ?>
                                                    <a href="/admin/pkkm/instrumen#ind-<?= $mind['id']; ?>" 
                                                       target="_blank"
                                                       class="ind-pill-interactive" 
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= htmlspecialchars($mind['komponen'] . ($mind['sub'] ? ' &rsaquo; ' . $mind['sub'] : '') . ' : ' . $mind['nama']); ?>">
                                                        <i data-lucide="bookmark-check" style="width:9px;height:9px;color:#0284c7;"></i>
                                                        <span><?= htmlspecialchars($mind['kode']); ?></span>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="unmatched-pill">
                                                <i data-lucide="info" style="width:10px;height:10px;"></i> Dokumen Tambahan
                                            </span>
                                        <?php endif; ?>

                                        <!-- Kotak Bukti Digital Siap Pakai (Jika Tersedia) -->
                                        <?php if(!empty($mb['bukti_digital'])): ?>
                                            <div class="digital-proof-card">
                                                <span class="digital-proof-title">
                                                    <i data-lucide="sparkles" style="width:12px;height:12px;color:#0284c7;"></i>
                                                    Tersedia Bukti PDF Resmi Auto-Generate (Kop + TTD Kamad)
                                                </span>
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="<?= $mb['bukti_digital']['url']; ?>" target="_blank" class="btn-digital-preview">
                                                        <i data-lucide="file-text" style="width:10px;height:10px;"></i> Preview
                                                    </a>
                                                    <?php 
                                                    $isAttached = false;
                                                    foreach($mb['files'] as $mf) {
                                                        if($mf['url'] === $mb['bukti_digital']['url']) {
                                                            $isAttached = true;
                                                            break;
                                                        }
                                                    }
                                                    if(!$isAttached):
                                                    ?>
                                                        <form action="/admin/pkkm/master-berkas/attach-digital" method="POST" class="m-0">
                                                            <input type="hidden" name="id" value="<?= $mb['id']; ?>">
                                                            <input type="hidden" name="url" value="<?= $mb['bukti_digital']['url']; ?>">
                                                            <input type="hidden" name="nama_file" value="<?= htmlspecialchars($mb['bukti_digital']['title']); ?>">
                                                            <button type="submit" class="btn-digital-attach">
                                                                <i data-lucide="plus-circle" style="width:10px;height:10px;"></i> Pasang 1-Klik
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-success" style="font-size:0.62rem; font-weight:700; padding:2px 6px; border-radius:4px; display:inline-flex; align-items:center; gap:2px;">
                                                            <i data-lucide="check" style="width:9px;height:9px;"></i> Terpasang
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($hasFiles): ?>
                                            <div class="file-list-wrap">
                                                <?php foreach($mb['files'] as $f): ?>
                                                    <div class="file-chip">
                                                        <a href="<?= htmlspecialchars($f['url']); ?>" target="_blank" class="file-chip-link" title="<?= htmlspecialchars($f['nama_file_asli'] ?: 'Lihat Berkas'); ?>">
                                                            <div class="file-chip-icon <?= $f['tipe'] == 'link' ? 'icon-link' : 'icon-file'; ?>">
                                                                <i data-lucide="<?= $f['tipe'] == 'link' ? 'external-link' : 'file-text'; ?>" style="width:11px;height:11px;"></i>
                                                            </div>
                                                            <span class="file-chip-name"><?= htmlspecialchars($f['nama_file_asli'] ?: 'Lihat Berkas'); ?></span>
                                                        </a>
                                                        <form action="/admin/pkkm/master-berkas/delete" method="POST" class="delete-berkas-form m-0">
                                                            <input type="hidden" name="id" value="<?= $f['id']; ?>">
                                                            <button type="submit" class="file-chip-del" title="Hapus Berkas Ini">
                                                                <i data-lucide="trash-2" style="width:11px;height:11px;"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge-pending">
                                                <i data-lucide="clock" style="width:11px;height:11px;"></i> Belum ada file
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <button type="button" class="<?= $hasFiles ? 'btn-upload-more' : 'btn-upload-more'; ?>" style="<?= !$hasFiles ? 'background:#0284c7; color:#fff; border-color:#0284c7;' : ''; ?>" onclick="openModalUpload(<?= $mb['id']; ?>, '<?= htmlspecialchars(addslashes($mb['nama_berkas'])); ?>')">
                                            <i data-lucide="<?= $hasFiles ? 'plus' : 'upload-cloud'; ?>" style="width:12px;height:12px;"></i> <?= $hasFiles ? 'Tambah' : 'Upload'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal Upload Berkas -->
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 40px rgba(0,0,0,0.15); overflow:hidden;">
            <form action="/admin/pkkm/master-berkas/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="inp_mb_id">
                <div class="modal-header" style="background:linear-gradient(135deg, #0f172a, #1e3a8a); color:white; padding:18px 24px;">
                    <div>
                        <span class="badge" style="background:rgba(255,255,255,0.2); font-size:0.7rem; font-weight:700; margin-bottom:4px; letter-spacing:0.5px;">UNGGAH BUKTI FISIK MASTER</span>
                        <h5 class="modal-title fw-bold" style="font-size:1.05rem; margin:0;" id="lbl_nama_berkas">Unggah Berkas</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div id="box_panduan_modal_upload" style="display:none;"></div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem; color:#334155;">Tipe Unggahan</label>
                        <select name="tipe" id="inp_mb_tipe" class="form-select" onchange="toggleTipeMB()" style="border-radius:10px; padding:10px 14px; font-weight:600;">
                            <option value="file">📁 File Langsung (PDF / Word / Excel / Gambar)</option>
                            <option value="link">🔗 Tautan Eksternal (Google Drive / Cloud / Web)</option>
                        </select>
                    </div>
                    
                    <div id="mb_file_input" class="form-group mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem; color:#334155;">Pilih File (Bisa pilih beberapa file sekaligus)</label>
                        <input type="file" name="file_upload[]" multiple="multiple" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" style="border-radius:10px; padding:8px 12px;">
                        <small class="text-muted" style="font-size:0.75rem; margin-top:4px; display:block;">Format didukung: PDF, Word, Excel, JPG, PNG (Maks 10MB per file).</small>
                    </div>

                    <div id="mb_link_input" class="form-group mb-3" style="display:none;">
                        <label class="form-label fw-bold" style="font-size:0.85rem; color:#334155;">URL / Link Google Drive</label>
                        <input type="url" name="url" class="form-control" placeholder="https://drive.google.com/..." style="border-radius:10px; padding:10px 14px;">
                        <small class="text-muted" style="font-size:0.75rem; margin-top:4px; display:block;">Pastikan akses link sudah diset "Siapa saja yang memiliki link dapat melihat".</small>
                    </div>
                    
                    <div class="form-group mb-2">
                        <label class="form-label fw-bold" style="font-size:0.85rem; color:#334155;">Label / Keterangan File (Opsional)</label>
                        <input type="text" name="nama_file" class="form-control" placeholder="Contoh: SK Tim Pengembang Kurikulum TP 2026/2027" style="border-radius:10px; padding:10px 14px;">
                        <small class="text-muted" style="font-size:0.75rem; margin-top:4px; display:block;">Kosongkan jika ingin memakai nama asli file.</small>
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 24px;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius:8px; padding:8px 16px;">Batal</button>
                    <button type="submit" class="btn-premium" style="padding:8px 20px;">
                        <i data-lucide="upload-cloud" style="width:16px;height:16px;"></i> Mulai Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Dokumen Master -->
<div class="modal fade" id="modalTambahBerkas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 40px rgba(0,0,0,0.15); overflow:hidden;">
            <form action="/admin/pkkm/master-berkas/create" method="POST">
                <div class="modal-header" style="background:linear-gradient(135deg, #0f172a, #1e3a8a); color:white; padding:18px 24px;">
                    <h5 class="modal-title fw-bold" style="font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="plus-circle" style="width:20px;height:20px;"></i> Tambah Dokumen Master Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem; color:#334155;">Nama Dokumen / Bukti Fisik <span class="text-danger">*</span></label>
                        <input type="text" name="nama_berkas" class="form-control" placeholder="Contoh: Dokumen Program Inovasi Madrasah" required style="border-radius:10px; padding:10px 14px; font-weight:600;">
                        <small class="text-muted" style="font-size:0.75rem; margin-top:6px; display:block;">
                            Nama dokumen ini akan dicocokkan dengan kriteria bukti fisik pada instrumen PKKM agar otomatis tertaut.
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 24px;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius:8px; padding:8px 16px;">Batal</button>
                    <button type="submit" class="btn-premium" style="padding:8px 20px;">
                        <i data-lucide="check" style="width:16px;height:16px;"></i> Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var currentFilter = 'all';
var currentCategory = 'all';

$(document).ready(function() {
    // Custom filter for DataTables (Status & Category)
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var row = table.row(dataIndex).node();
        var status = $(row).attr('data-status');
        var cat = $(row).attr('data-category');
        var isDigital = $(row).attr('data-digital');

        var statusMatch = true;
        if (currentFilter === 'uploaded') {
            statusMatch = (status === 'uploaded');
        } else if (currentFilter === 'pending') {
            statusMatch = (status === 'pending');
        } else if (currentFilter === 'digital_ready') {
            statusMatch = (isDigital === 'yes');
        }

        var catMatch = (currentCategory === 'all' || cat === currentCategory);

        return statusMatch && catMatch;
    });

    var table = $('#tableBerkas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },
        "pageLength": 10,
        "ordering": false
    });
    
    // Inisialisasi Tooltip Bootstrap
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    initTooltips();

    // Re-initialize lucide icons & tooltips on every redraw
    table.on('draw', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        initTooltips();
    });

    // Status Filter Pills click handler
    $('.filter-pill-btn').on('click', function() {
        $('.filter-pill-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        table.draw();
    });

    // Category Filter Pills click handler
    $('.cat-filter-btn').on('click', function() {
        $('.cat-filter-btn').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('category');
        table.draw();
    });
    
    // Custom Delete Confirmation with SweetAlert2
    $('#tableBerkas tbody').on('submit', '.delete-berkas-form', function(e) {
        e.preventDefault();
        var form = this;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus File Ini?',
                text: "Semua indikator yang otomatis tertaut ke file ini akan kehilangan tautan berkasnya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('Hapus file ini? Indikator terkait akan kehilangan tautan ke file ini.')) {
                form.submit();
            }
        }
    });

    // SweetAlert2 Alerts on page load
    <?php if(isset($_GET['msg'])): ?>
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            });
            
            <?php if($_GET['msg'] == 'synced'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Sinkronisasi Berhasil!',
                    html: 'Daftar Master Berkas telah disinkronkan dengan <b>42 Indikator</b> instrumen terbaru.<br><span class="badge bg-success mt-2" style="font-size:0.8rem; padding:6px 12px;">+<?= (int)($_GET['ins'] ?? 0); ?> Berkas Baru</span> <?php if(!empty($_GET['del'])): ?><span class="badge bg-secondary mt-2" style="font-size:0.8rem; padding:6px 12px;">-<?= (int)$_GET['del']; ?> Berkas Usang Dibersihkan</span><?php endif; ?>',
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: 'Alhamdulillah, Siap!',
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                });
            <?php elseif($_GET['msg'] == 'created'): ?>
                Toast.fire({ icon: 'success', title: 'Dokumen master baru berhasil ditambahkan.' });
            <?php elseif($_GET['msg'] == 'digital_attached'): ?>
                Toast.fire({ icon: 'success', title: 'Bukti fisik resmi PDF berhasil dipasang & tertaut ke indikator!' });
            <?php elseif($_GET['msg'] == 'success'): ?>
                Toast.fire({ icon: 'success', title: 'Berkas berhasil diunggah & otomatis tertaut.' });
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                Toast.fire({ icon: 'success', title: 'Berkas berhasil dihapus.' });
            <?php else: ?>
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat memproses data.' });
            <?php endif; ?>
            
            window.history.replaceState(null, null, window.location.pathname);
        }
    <?php endif; ?>
});

function openModalUpload(id, nama_berkas) {
    document.getElementById('inp_mb_id').value = id;
    document.getElementById('lbl_nama_berkas').innerText = nama_berkas;

    if (typeof renderGuideBoxInUploadModal === 'function') {
        renderGuideBoxInUploadModal(nama_berkas, 'box_panduan_modal_upload');
    }

    new bootstrap.Modal(document.getElementById('modalUpload')).show();
}

function openModalTambahBerkas() {
    new bootstrap.Modal(document.getElementById('modalTambahBerkas')).show();
}

function toggleTipeMB() {
    let tipe = document.getElementById('inp_mb_tipe').value;
    if (tipe === 'link') {
        document.getElementById('mb_file_input').style.display = 'none';
        document.getElementById('mb_link_input').style.display = 'block';
    } else {
        document.getElementById('mb_file_input').style.display = 'block';
        document.getElementById('mb_link_input').style.display = 'none';
    }
}

function confirmSyncMasterBerkas() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Sinkronkan Master Berkas?',
            html: 'Sistem akan mengekstrak seluruh bukti fisik dari <b>42 Indikator PKKM terbaru</b> dan membersihkan dokumen usang yang tidak terpakai.<br><small class="text-muted">Tenang, berkas yang sudah pernah Anda upload tidak akan hilang!</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b',
            confirmButtonText: '⚡ Ya, Sinkronkan Sekarang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Sinkronisasi...',
                    html: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        window.location.href = '/admin/pkkm/master-berkas/sync';
                    }
                });
            }
        });
    } else {
        if (confirm('Sinkronkan Master Berkas dengan 42 Indikator terbaru?')) {
            window.location.href = '/admin/pkkm/master-berkas/sync';
        }
    }
}
</script>

<?php include (file_exists(__DIR__ . '/_pkkm_panduan_modal.php') ? __DIR__ . '/_pkkm_panduan_modal.php' : __DIR__ . '/pkkm/_pkkm_panduan_modal.php'); ?>
<?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
