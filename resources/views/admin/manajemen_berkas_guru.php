<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title_page ?? 'Monitoring Berkas Guru') ?> — Aula System</title>
    <link rel="icon" type="image/png" href="<?= \App\Core\Branding::getInstitusiFavicon() ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/plus-jakarta-sans.css') ?>">
    <script src="<?= base_url('public/assets/js/lucide.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/zamrud.css?v=' . date('YmdH')) ?>">
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --primary-light: #f0f9ff;
            --violet-500: #8b5cf6;
            --violet-600: #7c3aed;
            --violet-50: #f5f3ff;
            --slate-800: #1e293b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .page-wrap {
            max-width: 1300px;
            margin: 0 auto;
            padding: 1.5rem 1rem 3rem;
        }

        /* Top Bar */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .top-nav a.btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #475569;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .top-nav a.btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Hero Header */
        .header-hero {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4338ca 100%);
            border-radius: 18px;
            padding: 2rem 2.2rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(67, 56, 202, 0.25);
            margin-bottom: 1.75rem;
        }
        .header-hero .bg-circle {
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(165, 180, 252, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            top: -50px;
            right: -30px;
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #c7d2fe;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .hero-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hero-desc {
            margin: 0;
            color: #e0e7ff;
            font-size: 0.92rem;
            max-width: 650px;
            line-height: 1.5;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.04);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-num {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 4px;
        }
        .kpi-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tabs Nav */
        .tabs-header {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e2e8f0;
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            width: fit-content;
        }
        .tab-nav-btn {
            padding: 8px 18px;
            border-radius: 9px;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .tab-nav-btn.active {
            background: #ffffff;
            color: #4338ca;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        /* Filter Box */
        .filter-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-select, .filter-input {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: inherit;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
        }
        .filter-select:focus, .filter-input:focus {
            border-color: #6366f1;
            background: #fff;
        }

        /* Table Card */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            margin-bottom: 2rem;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        .custom-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
        }
        .custom-table tr:hover td {
            background: #faf5ff;
        }

        /* Badges */
        .badge-cat {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-modul { background: #e0f2fe; color: #0284c7; }
        .badge-atp   { background: #dcfce7; color: #15803d; }
        .badge-prota { background: #fef3c7; color: #b45309; }
        .badge-kktp  { background: #ede9fe; color: #6d28d9; }
        .badge-umum  { background: #f1f5f9; color: #475569; }

        /* Action Buttons */
        .btn-view-doc {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: #ede9fe;
            color: #6d28d9;
            border-radius: 7px;
            font-weight: 700;
            font-size: 0.78rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-view-doc:hover {
            background: #ddd6fe;
            color: #5b21b6;
        }

        /* Modal Preview */
        .preview-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .preview-modal.active { display: flex; }
        .preview-modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 900px;
            height: 85vh;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .preview-modal-header {
            padding: 12px 20px;
            background: #1e1b4b;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- Top Nav -->
    <div class="top-nav">
        <a href="<?= base_url('portal') ?>" class="btn-back">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali ke Portal
        </a>
        <div style="display:flex; gap:8px;">
            <a href="<?= base_url('admin/pkkm/cetak-bukti/perangkat-guru') ?>" target="_blank" class="btn-back" style="background:#eff6ff; color:#2563eb; border-color:#bfdbfe;">
                <i data-lucide="printer" style="width:16px;height:16px;"></i> Display Audit PKKM
            </a>
            <a href="<?= base_url('admin/kurikulum/supervisi-administrasi') ?>" class="btn-back" style="background:#f0fdf4; color:#16a34a; border-color:#bbf7d0;">
                <i data-lucide="check-circle" style="width:16px;height:16px;"></i> Supervisi Administrasi
            </a>
        </div>
    </div>

    <!-- Hero Header -->
    <div class="header-hero">
        <div class="bg-circle"></div>
        <div class="hero-badge">
            <i data-lucide="folder-check" style="width:12px;height:12px;"></i> Pusat Monitoring Guru
        </div>
        <h1 class="hero-title">
            <i data-lucide="folder-kanban" style="width:28px;height:28px;color:#a5b4fc;"></i>
            Manajemen Berkas Guru
        </h1>
        <p class="hero-desc">
            Monitoring kelengkapan berkas pribadi (SK, Sertifikat, Portofolio) dan berkas perangkat pembelajaran (Modul Ajar/RPP, ATP, Prota, Promes, KKTP) seluruh dewan guru.
        </p>
    </div>

    <!-- KPI Summary -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
                <i data-lucide="users" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="kpi-num"><?= $totalGuru ?></div>
                <div class="kpi-label">Dewan Guru</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f5f3ff; color:#7c3aed;">
                <i data-lucide="book-open-check" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="kpi-num"><?= $totalPerangkat ?></div>
                <div class="kpi-label">Perangkat Mengajar</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
                <i data-lucide="file-badge" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="kpi-num"><?= $totalBerkasPribadi ?></div>
                <div class="kpi-label">Berkas Pribadi</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
                <i data-lucide="activity" style="width:24px;height:24px;"></i>
            </div>
            <div>
                <div class="kpi-num"><?= $persenKelengkapan ?>%</div>
                <div class="kpi-label">Partisipasi Guru (<?= $guruAktifCount ?>/<?= $totalGuru ?>)</div>
            </div>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="tabs-header">
        <a href="?tab=perangkat<?= $filter_guru ? '&guru_id='.$filter_guru : '' ?>" class="tab-nav-btn <?= $filter_tab === 'perangkat' ? 'active' : '' ?>">
            <i data-lucide="book-marked" style="width:16px;height:16px;"></i>
            Perangkat Pembelajaran Guru (<?= count($perangkatList) ?>)
        </a>
        <a href="?tab=pribadi<?= $filter_guru ? '&guru_id='.$filter_guru : '' ?>" class="tab-nav-btn <?= $filter_tab === 'pribadi' ? 'active' : '' ?>">
            <i data-lucide="user-check" style="width:16px;height:16px;"></i>
            Berkas Pribadi & Portofolio (<?= count($berkasPribadiList) ?>)
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="filter-panel">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($filter_tab) ?>">
        <div style="font-weight:700; font-size:0.8rem; color:#64748b; display:flex; align-items:center; gap:5px;">
            <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter:
        </div>
        <select name="guru_id" class="filter-select" onchange="this.form.submit()">
            <option value="0">-- Semua Guru --</option>
            <?php foreach($guruList as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $filter_guru == $g['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if($filter_tab === 'perangkat'): ?>
        <select name="kategori" class="filter-select" onchange="this.form.submit()">
            <option value="">-- Semua Kategori Perangkat --</option>
            <option value="Modul" <?= $filter_kategori == 'Modul' ? 'selected' : '' ?>>Modul Ajar / RPP</option>
            <option value="ATP" <?= $filter_kategori == 'ATP' ? 'selected' : '' ?>>Alur Tujuan Pembelajaran (ATP) / Silabus</option>
            <option value="Prota" <?= $filter_kategori == 'Prota' ? 'selected' : '' ?>>Program Tahunan (Prota) / Promes</option>
            <option value="KKTP" <?= $filter_kategori == 'KKTP' ? 'selected' : '' ?>>KKTP / Capaian</option>
        </select>
        <?php endif; ?>

        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama berkas / mapel..." class="filter-input" style="min-width:200px;">
        <button type="submit" class="btn-back" style="background:#4338ca; color:#fff; border-color:#4338ca; cursor:pointer;">
            <i data-lucide="search" style="width:14px;height:14px;"></i> Cari
        </button>
        <?php if($filter_guru || !empty($filter_kategori) || !empty($search)): ?>
            <a href="?tab=<?= htmlspecialchars($filter_tab) ?>" class="btn-back" style="color:#ef4444;">Reset</a>
        <?php endif; ?>
    </form>

    <!-- CONTENT TAB 1: PERANGKAT PEMBELAJARAN GURU -->
    <?php if($filter_tab === 'perangkat'): ?>
    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Nama Guru</th>
                    <th>Mata Pelajaran & Kelas</th>
                    <th>Kategori Perangkat</th>
                    <th>Judul & Dokumen</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($perangkatList)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:3rem; color:#94a3b8;">
                        <i data-lucide="file-question" style="width:40px;height:40px;margin-bottom:8px;opacity:0.5;"></i>
                        <div>Tidak ditemukan berkas perangkat pembelajaran yang cocok dengan filter.</div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($perangkatList as $p): 
                        $kat = $p['kategori'] ?? 'Perangkat';
                        $badgeClass = 'badge-umum';
                        if (stripos($kat, 'Modul') !== false || stripos($kat, 'RPP') !== false) $badgeClass = 'badge-modul';
                        elseif (stripos($kat, 'ATP') !== false || stripos($kat, 'Silabus') !== false) $badgeClass = 'badge-atp';
                        elseif (stripos($kat, 'Prota') !== false || stripos($kat, 'Promes') !== false) $badgeClass = 'badge-prota';
                        elseif (stripos($kat, 'KKTP') !== false || stripos($kat, 'Capaian') !== false) $badgeClass = 'badge-kktp';
                        
                        $fileUrl = '';
                        if (!empty($p['file_path'])) {
                            if (strpos($p['file_path'], 'http') === 0) {
                                $fileUrl = $p['file_path'];
                            } elseif (strpos($p['file_path'], '/public/') === 0) {
                                $fileUrl = $p['file_path'];
                            } else {
                                $fileUrl = base_url('public/uploads/guru_perangkat/' . $p['file_path']);
                            }
                        }
                    ?>
                    <tr>
                        <td style="color:#94a3b8; font-weight:600;"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($p['nama_guru'] ?: 'Guru') ?></div>
                            <div style="font-size:0.75rem; color:#64748b;">NIP: <?= htmlspecialchars($p['nip_guru'] ?: '-') ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600; color:#334155;"><?= htmlspecialchars($p['mapel_nama'] ?: 'Mata Pelajaran') ?></div>
                            <div style="font-size:0.75rem; color:#64748b;">Kelas: <?= htmlspecialchars($p['kelas_nama'] ?: '-') ?></div>
                        </td>
                        <td>
                            <span class="badge-cat <?= $badgeClass ?>"><?= htmlspecialchars($kat) ?></span>
                        </td>
                        <td>
                            <div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($p['nama_berkas'] ?: 'Dokumen Perangkat') ?></div>
                            <div style="font-size:0.72rem; color:#94a3b8; margin-top:2px;">
                                <i data-lucide="clock" style="width:11px;height:11px;vertical-align:-1px;"></i>
                                <?= !empty($p['created_at']) ? date('d M Y, H:i', strtotime($p['created_at'])) : '-' ?>
                            </div>
                        </td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:0.75rem; font-weight:700; background:#f0fdf4; color:#16a34a;">
                                <i data-lucide="check-circle" style="width:12px;height:12px;"></i> <?= htmlspecialchars($p['status_review'] ?: 'Tersedia') ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <?php if(!empty($fileUrl)): ?>
                                <button type="button" class="btn-view-doc" onclick="openPreview('<?= htmlspecialchars($fileUrl) ?>', '<?= htmlspecialchars(addslashes($p['nama_berkas'])) ?>')">
                                    <i data-lucide="eye" style="width:13px;height:13px;"></i> Lihat File
                                </button>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8;">Tidak ada file</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- CONTENT TAB 2: BERKAS PRIBADI GURU -->
    <?php if($filter_tab === 'pribadi'): ?>
    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Nama Guru</th>
                    <th>Kategori / Dokumen</th>
                    <th>Nama Berkas Portofolio</th>
                    <th>Status Kepegawaian</th>
                    <th>Waktu Upload</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($berkasPribadiList)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:3rem; color:#94a3b8;">
                        <i data-lucide="folder-x" style="width:40px;height:40px;margin-bottom:8px;opacity:0.5;"></i>
                        <div>Belum ada arsip berkas pribadi guru yang diunggah.</div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($berkasPribadiList as $bp): 
                        $bpFileUrl = base_url('public/uploads/guru_berkas/' . $bp['file_path']);
                    ?>
                    <tr>
                        <td style="color:#94a3b8; font-weight:600;"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($bp['nama_guru'] ?: 'Guru') ?></div>
                            <div style="font-size:0.75rem; color:#64748b;">NIP: <?= htmlspecialchars($bp['nip_guru'] ?: '-') ?></div>
                        </td>
                        <td>
                            <span class="badge-cat badge-umum"><?= htmlspecialchars($bp['kategori'] ?? 'Portofolio') ?></span>
                        </td>
                        <td>
                            <div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($bp['nama_berkas']) ?></div>
                            <div style="font-size:0.72rem; color:#64748b;"><?= htmlspecialchars($bp['file_path']) ?></div>
                        </td>
                        <td>
                            <span style="font-size:0.78rem; font-weight:700; padding:3px 8px; border-radius:6px; background:#f1f5f9; color:#475569;">
                                <?= htmlspecialchars($bp['status_kepegawaian'] ?: 'Guru') ?>
                            </span>
                        </td>
                        <td style="color:#64748b; font-size:0.8rem;">
                            <?= date('d M Y, H:i', strtotime($bp['created_at'])) ?> WIB
                        </td>
                        <td style="text-align:right;">
                            <a href="<?= $bpFileUrl ?>" target="_blank" class="btn-view-doc">
                                <i data-lucide="external-link" style="width:13px;height:13px;"></i> Buka / Unduh
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- Modal PDF Viewer -->
<div id="previewModal" class="preview-modal" onclick="closePreview(event)">
    <div class="preview-modal-box">
        <div class="preview-modal-header">
            <div style="font-weight:700; font-size:0.95rem; display:flex; align-items:center; gap:8px;">
                <i data-lucide="file-text" style="width:16px;height:16px;color:#a5b4fc;"></i>
                <span id="previewModalTitle">Pratinjau Dokumen</span>
            </div>
            <button type="button" onclick="document.getElementById('previewModal').classList.remove('active')" style="background:none; border:none; color:#fff; cursor:pointer; font-size:1.2rem;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div style="flex:1; background:#f1f5f9;">
            <iframe id="previewIframe" src="" style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function openPreview(url, title) {
        document.getElementById('previewModalTitle').innerText = title || 'Pratinjau Dokumen';
        document.getElementById('previewIframe').src = url;
        document.getElementById('previewModal').classList.add('active');
    }

    function closePreview(e) {
        if (e.target.id === 'previewModal') {
            document.getElementById('previewModal').classList.remove('active');
            document.getElementById('previewIframe').src = '';
        }
    }
</script>

</body>
</html>

