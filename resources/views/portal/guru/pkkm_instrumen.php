<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guru');
$initial = strtoupper(substr($nama, 0, 1));
$userFoto = $guru['foto'] ?? null;

// get myApps
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

        /* Assignment Card */
        .my-assignment-banner {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 1px solid #a7f3d0; border-radius: 16px; padding: 18px 24px;
            margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
        }
        .assignment-icon {
            width: 48px; height: 48px; border-radius: 14px; background: #38bdf8; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .assignment-title { font-size: 1rem; font-weight: 800; color: #1e40af; margin-bottom: 4px; }
        .assignment-desc { font-size: 0.85rem; color: #1e3a8a; margin: 0; }
        .assignment-chip {
            background: #ffffff; border: 1px solid #6ee7b7; color: #1e40af; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; margin-top: 6px;
        }

        /* Stat Grid */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 16px 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); background: #fff; border: 1px solid #e2e8f0;
        }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); flex-shrink: 0; }
        .stat-box.green { background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0; color: #14532d; }
        .stat-box.green .stat-icon { color: #38bdf8; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff, #ffffff); border-color: #bfdbfe; color: #1e3a8a; }
        .stat-box.blue .stat-icon { color: #3b82f6; }
        .stat-box.amber { background: linear-gradient(135deg, #fffbeb, #ffffff); border-color: #fde68a; color: #92400e; }
        .stat-box.amber .stat-icon { color: #f59e0b; }
        .stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.75rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Komponen Filter Navigation */
        .nav-pills .nav-link {
            color: #475569; background: #fff; font-weight: 700; font-size: 0.85rem; border-radius: 12px; padding: 10px 18px; border: 1px solid #e2e8f0; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px;
        }
        .nav-pills .nav-link:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #0284c7, #1e3a8a) !important; color: #ffffff !important; border-color: #1e3a8a !important; box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25) !important;
        }

        /* Card Komponen */
        .pkkm-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .komponen-header {
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%); border-left: 5px solid #0284c7; padding: 14px 18px; font-weight: 800; color: #0f172a; display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-radius: 0 10px 10px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;
        }
        .sub-komponen-box { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; padding: 18px; background: #fff; }
        .sub-komponen-header {
            font-weight: 700; color: #1e293b; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1; font-size: 0.95rem;
        }
        .badge-num { background: #0284c7; color: #ffffff; padding: 3px 8px; border-radius: 6px; margin-right: 8px; font-size: 0.75rem; font-weight: 700; }

        /* Table Indikator */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom th {
            background: #f8fafc; padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;
        }
        .table-custom td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; vertical-align: top; }
        .table-custom tr:hover td { background-color: #fbfcfe; }

        .btn-upload {
            background: #ecfdf5; color: #0284c7; border: 1px solid #6ee7b7; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: 0.2s;
        }
        .btn-upload:hover { background: #d1fae5; color: #1e3a8a; }
        .berkas-item {
            font-size: 0.78rem; background: #f8fafc; padding: 6px 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; border: 1px solid #e2e8f0;
        }
        .badge-master { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; }

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
                        <div class="header-tag-pill"><i data-lucide="award" style="width:13px;height:13px;"></i> PKKM TAHUN 2026/2027 • PORTAL GURU</div>
                        <h1><i data-lucide="clipboard-check"></i> Instrumen Penilaian PKKM</h1>
                        <p>Akses butir instrumen, pedoman bukti fisik juknis madrasah, dan unggah berkas fisik pendukung sesuai pembagian komponen tim Anda.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2">
                        <a href="/guru/pkkm/master-berkas" class="e-btn e-btn-primary">
                            <i data-lucide="folder-kanban"></i> Buka Daftar Berkas Master
                        </a>
                    </div>
                </div>
            </div>

            <!-- Assignment Info Banner -->
            <?php if(!empty($myAssignments)): ?>
                <div class="my-assignment-banner">
                    <div class="d-flex align-items-center gap-3">
                        <div class="assignment-icon">
                            <i data-lucide="shield-check" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div class="assignment-title">Halo, Bapak/Ibu <?php echo htmlspecialchars($guru['nama']); ?>!</div>
                            <div class="assignment-desc">Anda tercatat sebagai Tim Penanggung Jawab Komponen PKKM:</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach($myAssignments as $assign): ?>
                                    <div class="assignment-chip">
                                        <i data-lucide="tag" style="width:13px;height:13px;color:#0284c7;"></i>
                                        <strong>Komponen <?php echo $assign['komponen_urutan']; ?></strong>: <?php echo htmlspecialchars($assign['nama_komponen']); ?>
                                        <span class="badge bg-success ms-1" style="font-size:0.7rem;"><?php echo htmlspecialchars($assign['peran']); ?></span>
                                        <?php if(!empty($assign['tugas_keterangan'])): ?>
                                            <span style="font-size:0.75rem; color:#475569;">(<?php echo htmlspecialchars($assign['tugas_keterangan']); ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info d-flex align-items-center gap-3" style="border-radius:14px; background:#eff6ff; border-color:#bfdbfe; color:#1e40af;">
                    <i data-lucide="info" style="width:24px;height:24px;flex-shrink:0;"></i>
                    <div style="font-size:0.85rem;">
                        <strong>Informasi Tim:</strong> Anda dapat meninjau seluruh butir instrumen PKKM dan berkolaborasi mengunggah bukti fisik yang diperlukan. Admin dapat menetapkan penugasan komponen spesifik melalui menu Tim Penanggung Jawab.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Mini Stat Grid -->
            <div class="stat-grid">
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="layers" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalKomponen; ?></div>
                        <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Komponen PKKM' : 'Komponen Tugas Anda'; ?></div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="check-square" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalIndikator; ?></div>
                        <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Butir Indikator' : 'Indikator Komponen Anda'; ?></div>
                    </div>
                </div>
                <div class="stat-box amber">
                    <div class="stat-icon"><i data-lucide="folder-check" style="width:22px;height:22px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalBerkas; ?></div>
                        <div class="stat-label"><?php echo (!empty($isSupervisor)) ? 'Total Berkas Siap' : 'Berkas Siap Komponen Anda'; ?></div>
                    </div>
                </div>
            </div>

            <!-- Header Info & Isolasi Komponen -->
            <?php if(empty($data)): ?>
                <div class="card p-5 text-center my-4 border-0 shadow-sm rounded-4" style="background:#fff;">
                    <i data-lucide="shield-alert" style="width:50px;height:50px;color:#f59e0b;margin:0 auto 15px auto;"></i>
                    <h4 style="font-weight:800; color:#0f172a;">Belum Ada Komponen Yang Ditugaskan</h4>
                    <p style="color:#64748b; max-width:480px; margin:0 auto; font-size:0.9rem;">
                        Akun Anda belum tercatat pada pembagian tim komponen PKKM. Hubungi Administrator untuk menetapkan komponen yang menjadi tanggung jawab Anda.
                    </p>
                </div>
            <?php elseif(count($data) === 1): 
                $singleKomp = reset($data);
            ?>
                <div class="d-flex align-items-center justify-content-between mb-4 p-3 bg-white border rounded-4 shadow-sm flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" style="font-size:0.82rem; padding:7px 12px; border-radius:8px;">
                            <i data-lucide="shield-check" style="width:14px;height:14px;vertical-align:text-bottom;"></i> Komponen Khusus Tugas Anda
                        </span>
                        <span style="font-weight:800; color:#0f172a; font-size:0.95rem;">
                            Fokus: Komponen <?php echo $singleKomp['urutan']; ?> — <?php echo htmlspecialchars($singleKomp['nama_komponen']); ?>
                        </span>
                    </div>
                    <span class="badge bg-light text-muted border" style="font-size:0.75rem;">
                        <i data-lucide="lock" style="width:12px;height:12px;vertical-align:text-bottom;"></i> Instrumen komponen lain disembunyikan
                    </span>
                </div>
            <?php else: ?>
                <!-- Filter Komponen Pills (Jika Guru ditugaskan > 1 komponen atau Supervisor) -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <ul class="nav nav-pills gap-2" id="komponenTabs">
                        <li class="nav-item">
                            <button class="nav-link active" onclick="filterKomponenView('all')">
                                <i data-lucide="grid" style="width:15px;height:15px;"></i> Semua Tugas Saya (<?php echo count($data); ?> Komponen)
                            </button>
                        </li>
                        <?php foreach ($data as $komp): ?>
                            <li class="nav-item">
                                <button class="nav-link" onclick="filterKomponenView('komp-<?php echo $komp['id']; ?>')">
                                    Komponen <?php echo $komp['urutan']; ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Daftar Komponen -->
            <?php foreach ($data as $komp): 
                $isMyKomponen = in_array((int)$komp['id'], $myKomponenIds);
            ?>
            <div class="pkkm-card komponen-section" id="komp-section-<?php echo $komp['id']; ?>" data-komponen-id="<?php echo $komp['id']; ?>" data-is-my="<?php echo $isMyKomponen ? '1' : '0'; ?>">
                <div class="komponen-header">
                    <div>
                        <span class="badge bg-success me-2" style="font-size:0.75rem;">Komponen <?php echo $komp['urutan']; ?></span>
                        <span style="font-size:1.05rem;"><?php echo htmlspecialchars($komp['nama_komponen']); ?></span>
                        <?php if($isMyKomponen): ?>
                            <span class="badge bg-primary ms-2" style="font-size:0.7rem;"><i data-lucide="star" style="width:11px;height:11px;vertical-align:text-bottom;"></i> Tugas Anda</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php 
                $renderIndikatorTable = function($indikatorList) {
                    if (empty($indikatorList)) return;
                ?>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 35%;">Indikator & Bukti Fisik Juknis</th>
                                    <th style="width: 35%;">Arsip Berkas Digital Terunggah</th>
                                    <th style="width: 15%; text-align: center;">Skor Penilaian</th>
                                    <th style="width: 10%; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($indikatorList as $ind): ?>
                                <tr>
                                    <td class="text-center font-weight-bold" style="font-weight:700; color:#64748b;"><?php echo $ind['urutan']; ?></td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($ind['indikator']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #475569; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border-left: 3px solid #0284c7;">
                                            <strong style="color:#1e40af;"><i data-lucide="file-text" style="width:13px;height:13px;vertical-align:text-bottom;"></i> Bukti Fisik Juknis:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($ind['bukti_fisik'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($ind['berkas'])): ?>
                                            <?php foreach ($ind['berkas'] as $b): ?>
                                                <div class="berkas-item">
                                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;" title="<?php echo htmlspecialchars($b['nama_file']); ?>">
                                                        <i data-lucide="<?php echo $b['tipe'] === 'link' ? 'link' : 'file-text'; ?>" style="width: 13px; height: 13px; margin-right: 4px; color:#0284c7; vertical-align:middle;"></i>
                                                        <a href="<?php echo $b['url']; ?>" target="_blank" style="text-decoration:none; color:#1e293b; font-weight:600;">
                                                            <?php echo htmlspecialchars($b['nama_file']); ?>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <?php if(!empty($b['is_master'])): ?>
                                                            <span class="badge-master">Master</span>
                                                        <?php else: ?>
                                                            <form method="POST" action="/guru/pkkm/berkas/delete" onsubmit="return confirm('Hapus berkas ini?');" style="margin:0;">
                                                                <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i data-lucide="trash-2" style="width: 13px; height: 13px;"></i></button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.8rem; font-style: italic;">Belum ada berkas fisik terunggah</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <?php if(!empty($ind['skor']) && $ind['skor'] > 0): ?>
                                            <span class="badge bg-success" style="font-size:0.85rem; padding:6px 12px; border-radius:8px;">
                                                Skor: <?php echo $ind['skor']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border" style="font-size:0.75rem; padding:5px 10px; border-radius:8px;">
                                                Belum Dinilai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            <button onclick="openUploadModal(<?php echo $ind['id']; ?>, '<?php echo htmlspecialchars(addslashes($ind['indikator'])); ?>')" class="btn-upload">
                                                <i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload
                                            </button>
                                            <?php if(stripos($ind['bukti_fisik'], 'website') !== false || stripos($ind['bukti_fisik'], 'web') !== false): ?>
                                                <button onclick="attachWebLink(<?php echo $ind['id']; ?>)" class="btn btn-sm btn-outline-success p-1 px-2" style="font-size:0.7rem; border-radius:6px;" title="Pasang Web Madrasah">
                                                    <i data-lucide="globe" style="width:11px;height:11px;"></i> Pasang Web
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php }; ?>

                <!-- Sub-Komponen Loop -->
                <?php if (!empty($komp['sub_komponens'])): ?>
                    <?php foreach ($komp['sub_komponens'] as $sub): ?>
                        <div class="sub-komponen-box">
                            <div class="sub-komponen-header">
                                <div>
                                    <span class="badge-num"><?php echo $sub['urutan']; ?></span>
                                    <span><?php echo htmlspecialchars($sub['nama_sub']); ?></span>
                                </div>
                            </div>
                            <?php $renderIndikatorTable($sub['indikators']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($komp['indikators'])): ?>
                    <div class="sub-komponen-box mt-3">
                        <div class="sub-komponen-header">
                            <div><span class="badge-num"><?php echo $komp['urutan']; ?></span> Indikator Umum</div>
                        </div>
                        <?php $renderIndikatorTable($komp['indikators']); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<!-- Modal Upload Berkas -->
<div id="uploadModal" class="modal-custom">
    <div class="modal-dialog-custom">
        <form method="POST" action="/guru/pkkm/berkas/save" enctype="multipart/form-data" style="margin:0;">
            <input type="hidden" name="indikator_id" id="modal_indikator_id">
            
            <div style="padding:18px 24px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg, #f0fdf4, #ffffff);">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px; height:34px; border-radius:10px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="upload-cloud" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <h5 style="margin:0; font-weight:800; font-size:1.05rem; color:#0f172a;">Upload Bukti Fisik PKKM</h5>
                        <div style="font-size:0.75rem; color:#64748b;" id="modalIndikatorPreview">Indikator</div>
                    </div>
                </div>
                <button type="button" onclick="closeUploadModal()" style="border:none; background:transparent; cursor:pointer; color:#94a3b8;">
                    <i data-lucide="x" style="width:20px;height:20px;"></i>
                </button>
            </div>

            <div style="padding:22px;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">Tipe Berkas</label>
                    <select name="tipe" id="tipe_berkas" class="form-select" onchange="toggleTipeInput()" style="border-radius:10px;">
                        <option value="file">File Dokumen / Foto Kegiatan</option>
                        <option value="link">Tautan Eksternal (Google Drive / Website)</option>
                    </select>
                </div>

                <div id="input_file_container" class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">Pilih File / Foto Bukti Fisik</label>
                    <input type="file" name="file_upload[]" multiple class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="border-radius:10px;">
                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:5px;">
                        • Bisa pilih banyak foto sekaligus (otomatis digabung menjadi 1 Album PDF resmi).<br>
                        • Format didukung: PDF, Word, Excel, Gambar (JPG, PNG).
                    </div>
                </div>

                <div id="input_link_container" class="mb-3" style="display: none;">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">URL / Link Bukti</label>
                    <input type="url" name="url" class="form-control" placeholder="https://..." style="border-radius:10px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem;">Keterangan / Nama File Dokumen (Opsional)</label>
                    <input type="text" name="nama_file" class="form-control" placeholder="Contoh: Berita Acara Sosialisasi Visi Misi" style="border-radius:10px;">
                </div>
            </div>

            <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="closeUploadModal()" class="btn btn-light" style="border-radius:10px; font-weight:700; font-size:0.85rem;">Batal</button>
                <button type="submit" class="btn btn-success" style="border-radius:10px; font-weight:700; font-size:0.85rem; background:#1e3a8a; border-color:#1e3a8a;">
                    <i data-lucide="check" style="width:16px;height:16px;vertical-align:text-bottom;"></i> Simpan Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Pasang Web Madrasah -->
<form id="attachWebForm" method="POST" action="/guru/pkkm/bukti-attach-website" style="display:none;">
    <input type="hidden" name="indikator_id" id="web_indikator_id">
    <input type="hidden" name="url" value="https://mismifhda.sch.id">
    <input type="hidden" name="nama_file" value="Portal Resmi Madrasah (mismifhda.sch.id)">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'saved') {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Bukti fisik berhasil diunggah.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'deleted') {
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Berkas bukti fisik telah dihapus.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.get('msg') === 'website_attached') {
            Swal.fire({ icon: 'success', title: 'Terpasang!', text: 'Tautan resmi website madrasah berhasil dipasang.', timer: 2000, showConfirmButton: false });
        }

    });

    function filterKomponenView(filter, btn = null) {
        const navLinks = document.querySelectorAll('#komponenTabs .nav-link');
        navLinks.forEach(link => link.classList.remove('active'));
        if (btn) {
            btn.classList.add('active');
        } else if (typeof event !== 'undefined' && event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }

        const cards = document.querySelectorAll('.komponen-section');
        cards.forEach(card => {
            const kompId = card.getAttribute('data-komponen-id');
            if (filter === 'all' || filter === 'komp-' + kompId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openUploadModal(indikatorId, indikatorText) {
        document.getElementById('modal_indikator_id').value = indikatorId;
        document.getElementById('modalIndikatorPreview').textContent = indikatorText.length > 50 ? indikatorText.substring(0, 50) + '...' : indikatorText;
        document.getElementById('uploadModal').classList.add('show');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('show');
    }

    function toggleTipeInput() {
        const tipe = document.getElementById('tipe_berkas').value;
        if (tipe === 'file') {
            document.getElementById('input_file_container').style.display = 'block';
            document.getElementById('input_link_container').style.display = 'none';
        } else {
            document.getElementById('input_file_container').style.display = 'none';
            document.getElementById('input_link_container').style.display = 'block';
        }
    }

    function attachWebLink(indikatorId) {
        Swal.fire({
            title: 'Pasang Tautan Website?',
            text: 'Tautan portal resmi https://mismifhda.sch.id akan otomatis ditautkan ke butir indikator ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            confirmButtonText: 'Ya, Pasang 1-Klik',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById('web_indikator_id').value = indikatorId;
                document.getElementById('attachWebForm').submit();
            }
        });
    }
</script>

</body>
</html>
