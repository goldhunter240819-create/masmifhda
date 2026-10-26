<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guru');
$initial = strtoupper(substr($nama, 0, 1));
$userFoto = $guru['foto'] ?? null;

$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}

$status_kepegawaian = $guru['status_kepegawaian'] ?? 'Honorer';
$myTasks = [];

$totalMaster = count($master_berkas);
$percentUploaded = $totalMaster > 0 ? round(($totalUploaded / $totalMaster) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Portal Guru GH-SSS</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <script src="/public/assets/js/lucide.min.js"></script>
    <link rel="stylesheet" href="/public/assets/css/zamrud.css?v=<?php echo date('YmdH'); ?>">
    <link rel="stylesheet" href="/public/assets/css/multi_dashboard.css?v=<?php echo date('YmdH'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem !important; }

        /* Banner Header */
        .elite-page-header {
            position: relative; padding: 2rem 2.5rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
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
        .elite-page-header .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .elite-page-header .header-text-premium h1 { font-size: 1.75rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff; }
        .elite-page-header .header-text-premium p { font-size: 0.9rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem; margin-bottom: 0; max-width: 680px; font-weight: 400; line-height: 1.5; }
        .header-tag-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px); }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #1e3a8a; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #0f172a; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* Stat Grid */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 14px 18px; border-radius: 16px; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); background: #fff; border: 1px solid #e2e8f0;
        }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); flex-shrink: 0; }
        .stat-box.purple { background: linear-gradient(135deg, #faf5ff, #ffffff); border-color: #e9d5ff; color: #6b21a8; }
        .stat-box.purple .stat-icon { color: #8b5cf6; }
        .stat-box.emerald { background: linear-gradient(135deg, #ecfdf5, #ffffff); border-color: #a7f3d0; color: #1e40af; }
        .stat-box.emerald .stat-icon { color: #0284c7; }
        .stat-box.green { background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0; color: #14532d; }
        .stat-box.green .stat-icon { color: #38bdf8; }
        .stat-box.yellow { background: linear-gradient(135deg, #fffbeb, #ffffff); border-color: #fde68a; color: #92400e; }
        .stat-box.yellow .stat-icon { color: #f59e0b; }
        .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.72rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Filter Pills */
        .nav-filter-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
        .filter-pill-btn {
            background: #ffffff; border: 1px solid #e2e8f0; color: #475569; padding: 7px 14px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px;
        }
        .filter-pill-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
        .filter-pill-btn.active {
            background: #1e3a8a !important; color: #ffffff !important; border-color: #1e3a8a !important;
        }
        .filter-pill-btn.active .badge-num { background: rgba(255,255,255,0.25); color: #fff; }
        .badge-num { background: #f1f5f9; color: #475569; font-size: 0.7rem; padding: 2px 7px; border-radius: 999px; }

        .category-filter-wrap {
            display: flex; gap: 8px; flex-wrap: wrap; padding: 12px 16px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 20px;
        }
        .cat-filter-btn {
            background: #ffffff; border: 1px solid #cbd5e1; color: #334155; padding: 6px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: 0.2s;
        }
        .cat-filter-btn:hover { background: #f1f5f9; }
        .cat-filter-btn.active { background: #0f172a !important; color: #ffffff !important; border-color: #0f172a !important; }
        .cat-num { font-size: 0.7rem; opacity: 0.75; margin-left: 4px; }

        /* Glass Card & Table */
        .glass-card {
            background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 22px; margin-bottom: 24px;
        }
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom th {
            background: #f8fafc; padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;
        }
        .table-custom td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; vertical-align: top; }
        .table-custom tr:hover td { background-color: #fbfcfe; }

        .cat-badge {
            font-size: 0.68rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 4px;
        }
        .cat-sk { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .cat-notulen { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .cat-program { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .cat-laporan { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .cat-digital { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
        .cat-sarpras { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .cat-umum { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .btn-upload-direct {
            background: #0284c7; color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; border: none; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: 0.2s;
        }
        .btn-upload-direct:hover { background: #1e3a8a; }

        .file-pill {
            background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px 10px; border-radius: 8px; margin-bottom: 5px; font-size: 0.78rem; display: flex; justify-content: space-between; align-items: center; gap: 8px;
        }
        .ind-chip {
            background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin: 2px;
        }

        /* Auto Digital Box */
        .digital-box-ready {
            background: linear-gradient(135deg, #f0fdf4, #ffffff); border: 1px solid #86efac; border-radius: 10px; padding: 10px 14px; margin-top: 8px;
        }

        /* Modal Custom */
        .modal-custom {
            display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px);
        }
        .modal-custom.show { display: flex; align-items: center; justify-content: center; }
        .modal-dialog-custom {
            background: #ffffff; border-radius: 20px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: modalIn 0.2s ease-out;
        }
        @keyframes modalIn {
            from { transform: translateY(-20px) scale(0.97); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <!-- Elite Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill"><i data-lucide="folder-open" style="width:13px;height:13px;"></i> MASTER BUKTI FISIK • PORTAL GURU</div>
                        <h1><i data-lucide="archive"></i> Daftar Berkas Master PKKM</h1>
                        <p>Pusat koleksi berkas master. Satu dokumen yang diunggah otomatis terdistribusi ke seluruh butir indikator PKKM yang memerlukannya.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2 flex-wrap">
                        <button type="button" class="e-btn e-btn-secondary" onclick="openKoleksiBuktiModal()">
                            <i data-lucide="sparkles" style="width:16px;height:16px;color:#fde047;"></i> Link Bukti Kita (13)
                        </button>
                        <a href="/guru/pkkm/instrumen" class="e-btn e-btn-primary">
                            <i data-lucide="clipboard-check"></i> Buka Instrumen PKKM
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stat Summary Cards -->
            <div class="stat-grid">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="files" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalMaster; ?></div>
                        <div class="stat-label">Total Dokumen Master</div>
                    </div>
                </div>
                <div class="stat-box emerald">
                    <div class="stat-icon"><i data-lucide="sparkles" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalDigitalReady; ?></div>
                        <div class="stat-label">Bukti Digital Siap</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="check-circle" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalUploaded; ?> (<?php echo $percentUploaded; ?>%)</div>
                        <div class="stat-label">Sudah Ada File</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="clock" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalPending; ?></div>
                        <div class="stat-label">Belum Ada File</div>
                    </div>
                </div>
            </div>

            <!-- Banner Isolasi Berkas Tugas -->
            <?php if(!empty($myAssignments)): ?>
                <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-white border rounded-4 shadow-sm flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" style="font-size:0.8rem; padding:6px 12px; border-radius:8px;">
                            <i data-lucide="lock" style="width:13px;height:13px;vertical-align:text-bottom;"></i> Berkas Terisolasi
                        </span>
                        <span style="font-weight:700; color:#0f172a; font-size:0.88rem;">
                            Menampilkan Berkas Master Khusus Terkait: 
                            <?php foreach($myAssignments as $idx => $ma): ?>
                                <strong>Komponen <?php echo $ma['komponen_urutan']; ?> (<?php echo htmlspecialchars($ma['nama_komponen']); ?>)</strong><?php echo $idx < count($myAssignments)-1 ? ', ' : ''; ?>
                            <?php endforeach; ?>
                        </span>
                    </div>
                    <span class="badge bg-light text-muted border" style="font-size:0.75rem;">
                        <i data-lucide="shield-check" style="width:12px;height:12px;vertical-align:text-bottom;"></i> Berkas komponen lain disembunyikan
                    </span>
                </div>
            <?php endif; ?>

            <!-- Glass Card Table -->
            <div class="glass-card">
                <!-- Nav Filter Pills Bar -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="nav-filter-pills">
                        <button type="button" class="filter-pill-btn active" data-filter="all">
                            Semua Berkas Tugas <span class="badge-num"><?php echo $totalMaster; ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="uploaded">
                            <i data-lucide="check-circle" style="width:13px;height:13px;"></i> Sudah Diunggah <span class="badge-num"><?php echo $totalUploaded; ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="pending">
                            <i data-lucide="clock" style="width:13px;height:13px;"></i> Belum Ada File <span class="badge-num"><?php echo $totalPending; ?></span>
                        </button>
                        <button type="button" class="filter-pill-btn" data-filter="digital_ready" style="border-color:#86efac; color:#1e40af;">
                            <i data-lucide="sparkles" style="width:13px;height:13px;color:#0284c7;"></i> Bukti Digital Siap <span class="badge-num" style="background:#dcfce7; color:#166534;"><?php echo $totalDigitalReady; ?></span>
                        </button>
                    </div>
                </div>

                <!-- Category Filter Pills Bar -->
                <div class="category-filter-wrap">
                    <button type="button" class="cat-filter-btn active" data-category="all">
                        Semua Kategori <span class="cat-num"><?php echo $totalMaster; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="sk">
                        📜 SK & Kebijakan <span class="cat-num"><?php echo $categoryCounts['sk'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="notulen">
                        📝 Berita Acara & Notulen <span class="cat-num"><?php echo $categoryCounts['notulen'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="program">
                        📅 Program & Rencana <span class="cat-num"><?php echo $categoryCounts['program'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="laporan">
                        📈 Laporan & Prestasi <span class="cat-num"><?php echo $categoryCounts['laporan'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="digital">
                        💻 Bukti Digital & TIK <span class="cat-num"><?php echo $categoryCounts['digital'] ?? 0; ?></span>
                    </button>
                    <button type="button" class="cat-filter-btn" data-category="sarpras">
                        🏢 Sarpras & Lingkungan <span class="cat-num"><?php echo $categoryCounts['sarpras'] ?? 0; ?></span>
                    </button>
                </div>

                <!-- Table Master Berkas -->
                <div class="table-responsive">
                    <table class="table-custom" id="masterTable">
                        <thead>
                            <tr>
                                <th style="width:5%;">No</th>
                                <th style="width:42%;">Nama Dokumen & Deskripsi Fisik</th>
                                <th style="width:25%;">Status & File Terunggah</th>
                                <th style="width:28%; text-align:right;">Aksi & Bukti Auto-Generate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($master_berkas as $no => $mb): 
                                $catClass = 'cat-' . ($mb['kategori'] ?? 'umum');
                                $catLabel = $mb['kategori_label'] ?? 'DOKUMEN UMUM';
                                $hasFiles = !empty($mb['files']);
                                $hasDigital = !empty($mb['bukti_digital']);
                            ?>
                            <tr class="master-row" 
                                data-category="<?php echo $mb['kategori'] ?? 'umum'; ?>"
                                data-status="<?php echo $hasFiles ? 'uploaded' : 'pending'; ?>"
                                data-digital="<?php echo $hasDigital ? '1' : '0'; ?>">
                                
                                <td style="font-weight:700; color:#94a3b8;"><?php echo $no + 1; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="cat-badge <?php echo $catClass; ?>"><?php echo $catLabel; ?></span>
                                    </div>
                                    <div style="font-weight:800; color:#0f172a; font-size:0.95rem; line-height:1.3; margin-bottom:4px;">
                                        <?php echo htmlspecialchars($mb['judul_tampilan'] ?? $mb['nama_berkas']); ?>
                                    </div>
                                    <?php if(!empty($mb['deskripsi'])): ?>
                                        <div style="font-size:0.8rem; color:#334155; background:#f8fafc; padding:6px 10px; border-radius:6px; border-left:3px solid #cbd5e1; margin-bottom:6px;">
                                            <?php echo htmlspecialchars($mb['deskripsi']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Indikator Tertaut -->
                                    <?php if(!empty($mb['indikators_terkait'])): ?>
                                        <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                            <span style="font-size:0.7rem; color:#64748b; font-weight:700;">Tertaut:</span>
                                            <?php foreach(array_slice($mb['indikators_terkait'], 0, 4) as $it): ?>
                                                <span class="ind-chip" title="<?php echo htmlspecialchars($it['komponen']); ?>">
                                                    <i data-lucide="corner-down-right" style="width:10px;height:10px;"></i> <?php echo $it['kode']; ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if(count($mb['indikators_terkait']) > 4): ?>
                                                <span class="badge bg-light text-secondary border" style="font-size:0.68rem;">+<?php echo count($mb['indikators_terkait']) - 4; ?> lagi</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Auto Digital Box -->
                                    <?php if($hasDigital): ?>
                                        <div class="digital-box-ready">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div style="font-size:0.78rem; font-weight:700; color:#1e40af;">
                                                    <i data-lucide="sparkles" style="width:14px;height:14px;color:#38bdf8;vertical-align:text-bottom;"></i>
                                                    Tersedia Bukti PDF Resmi Auto-Generate (Kop + TTD Kamad)!
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <a href="<?php echo $mb['bukti_digital']['url']; ?>" target="_blank" class="btn btn-sm btn-outline-success p-1 px-2" style="font-size:0.72rem; border-radius:6px;">
                                                        <i data-lucide="printer" style="width:12px;height:12px;"></i> Preview / Cetak
                                                    </a>
                                                    <button type="button" onclick="attachDigital('<?php echo $mb['id']; ?>', '<?php echo $mb['bukti_digital']['url']; ?>', '<?php echo htmlspecialchars(addslashes($mb['bukti_digital']['title'] ?? 'Bukti Digital Resmi')); ?>')" class="btn btn-sm btn-success p-1 px-2" style="font-size:0.72rem; border-radius:6px; background:#0284c7; border-color:#0284c7;">
                                                        <i data-lucide="check" style="width:12px;height:12px;"></i> Pasang 1-Klik
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($hasFiles): ?>
                                        <?php foreach($mb['files'] as $f): ?>
                                            <div class="file-pill">
                                                <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:170px;" title="<?php echo htmlspecialchars($f['nama_file_asli']); ?>">
                                                    <i data-lucide="<?php echo $f['tipe'] === 'link' ? 'link' : 'file-text'; ?>" style="width:13px;height:13px;color:#0284c7;vertical-align:middle;"></i>
                                                    <a href="<?php echo $f['url']; ?>" target="_blank" style="text-decoration:none; color:#1e293b; font-weight:600;">
                                                        <?php echo htmlspecialchars($f['nama_file_asli']); ?>
                                                    </a>
                                                </div>
                                                <form method="POST" action="/guru/pkkm/master-berkas/delete" onsubmit="return confirm('Hapus file ini?');" style="margin:0;">
                                                    <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge bg-light text-danger border border-danger-subtle" style="font-size:0.75rem;">
                                            <i data-lucide="clock" style="width:11px;height:11px;vertical-align:text-bottom;"></i> Belum ada file
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <button type="button" onclick="openUploadModal(<?php echo $mb['id']; ?>, '<?php echo htmlspecialchars(addslashes($panduan['judul_lengkap'] ?? $mb['nama_berkas'])); ?>')" class="btn-upload-direct">
                                        <i data-lucide="upload" style="width:13px;height:13px;"></i> Upload
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Upload Berkas Master -->
<div id="uploadModal" class="modal-custom">
    <div class="modal-dialog-custom">
        <form method="POST" action="/guru/pkkm/master-berkas/upload" enctype="multipart/form-data" style="margin:0;">
            <input type="hidden" name="master_berkas_id" id="modal_master_id">
            
            <div style="padding:18px 24px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg, #f0fdf4, #ffffff);">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px; height:34px; border-radius:10px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="upload-cloud" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <h5 style="margin:0; font-weight:800; font-size:1.05rem; color:#0f172a;">Upload Dokumen Master</h5>
                        <div style="font-size:0.75rem; color:#64748b;" id="modalDokumenPreview">Nama Dokumen</div>
                    </div>
                </div>
                <button type="button" onclick="closeUploadModal()" style="border:none; background:transparent; cursor:pointer; color:#94a3b8;">
                    <i data-lucide="x" style="width:20px;height:20px;"></i>
                </button>
            </div>

            <div style="padding:22px;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">Pilih Berkas Dokumen</label>
                    <input type="file" name="file_upload[]" multiple class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required style="border-radius:10px;">
                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:5px;">
                        • Bisa pilih banyak file atau foto (foto otomatis digabung menjadi 1 Album PDF).<br>
                        • Format didukung: PDF, Word, Excel, Gambar.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">Nama / Keterangan Berkas (Opsional)</label>
                    <input type="text" name="nama_file" class="form-control" placeholder="Contoh: Berita Acara Rapat Lengkap TTD" style="border-radius:10px;">
                </div>
            </div>

            <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="closeUploadModal()" class="btn btn-light" style="border-radius:10px; font-weight:700; font-size:0.85rem;">Batal</button>
                <button type="submit" class="btn btn-success" style="border-radius:10px; font-weight:700; font-size:0.85rem; background:#1e3a8a; border-color:#1e3a8a;">
                    <i data-lucide="check" style="width:16px;height:16px;vertical-align:text-bottom;"></i> Upload Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hidden Attach Digital -->
<form id="attachDigitalForm" method="POST" action="/guru/pkkm/master-berkas/attach-digital" style="display:none;">
    <input type="hidden" name="id" id="digital_master_id">
    <input type="hidden" name="url" id="digital_url">
    <input type="hidden" name="nama_file" id="digital_nama_file">
</form>

<!-- Include Modal Koleksi Bukti Digital -->
<?php include __DIR__ . '/../../admin/_pkkm_koleksi_modal.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let masterTable = null;

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'uploaded') {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Dokumen master berhasil diunggah.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'deleted') {
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'File dokumen telah dihapus.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'digital_attached') {
            Swal.fire({ icon: 'success', title: 'Terpasang!', text: 'Bukti fisik resmi auto-generate berhasil dipasang ke master berkas.', timer: 2000, showConfirmButton: false });
        }

        // Init DataTable
        masterTable = $('#masterTable').DataTable({
            language: {
                search: "Cari Dokumen:",
                lengthMenu: "Tampilkan _MENU_ berkas",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ berkas",
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            },
            pageLength: 25,
            ordering: false
        });

        // Setup filter handlers
        setupFilters();
    });

    let currentStatusFilter = 'all';
    let currentCategoryFilter = 'all';

    function setupFilters() {
        // Status & Task filter pills
        document.querySelectorAll('.filter-pill-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentStatusFilter = this.getAttribute('data-filter');
                applyCustomFilters();
            });
        });

        // Category filter buttons
        document.querySelectorAll('.cat-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentCategoryFilter = this.getAttribute('data-category');
                applyCustomFilters();
            });
        });

        // Custom filtering logic for DataTables
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const row = masterTable.row(dataIndex).node();
            if (!row) return true;

            const rowCategory = row.getAttribute('data-category');
            const rowStatus = row.getAttribute('data-status');
            const rowDigital = row.getAttribute('data-digital');
            const rowMyTask = row.getAttribute('data-mytask');

            // Category match
            if (currentCategoryFilter !== 'all' && rowCategory !== currentCategoryFilter) {
                return false;
            }

            // Status match
            if (currentStatusFilter === 'uploaded' && rowStatus !== 'uploaded') {
                return false;
            } else if (currentStatusFilter === 'pending' && rowStatus !== 'pending') {
                return false;
            } else if (currentStatusFilter === 'digital_ready' && rowDigital !== '1') {
                return false;
            } else if (currentStatusFilter === 'my_task' && rowMyTask !== '1') {
                return false;
            }

            return true;
        });

        applyCustomFilters();
    }

    function applyCustomFilters() {
        if (masterTable) {
            masterTable.draw();
            if (typeof lucide !== 'undefined') {
                setTimeout(() => lucide.createIcons(), 50);
            }
        }
    }

    function openUploadModal(masterId, namaBerkas) {
        document.getElementById('modal_master_id').value = masterId;
        document.getElementById('modalDokumenPreview').textContent = namaBerkas;
        document.getElementById('uploadModal').classList.add('show');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('show');
    }

    function attachDigital(id, url, nama) {
        Swal.fire({
            title: 'Pasang Bukti Resmi 1-Klik?',
            text: 'Dokumen auto-generate resmi dengan Kop Madrasah dan TTD Kamad akan otomatis dipasang ke berkas ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            confirmButtonText: 'Ya, Pasang!',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById('digital_master_id').value = id;
                document.getElementById('digital_url').value = url;
                document.getElementById('digital_nama_file').value = nama;
                document.getElementById('attachDigitalForm').submit();
            }
        });
    }

    function openKoleksiBuktiModal() {
        const modal = document.getElementById('koleksiBuktiModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
</script>

</body>
</html>
