<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Perangkat Pembelajaran'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.15rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
        }
        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .badge-jenis {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-modul { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-atp   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-prota { background: #fdf4ff; color: #9333ea; border: 1px solid #f0abfc; }
        .badge-kktp  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-default { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-disetujui { background: #dcfce7; color: #15803d; }
        .status-menunggu  { background: #fef3c7; color: #b45309; }
        .status-revisi    { background: #fee2e2; color: #b91c1c; }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.85rem;
            align-items: end;
        }
        .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.35rem;
            display: block;
        }
        .filter-input {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.85rem;
            background: #f8fafc;
            color: #1e293b;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }
        .filter-input:focus {
            background: #fff;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .modal-box {
            background: #ffffff;
            border-radius: 14px;
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }
        .btn-action-view { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .btn-action-view:hover { background: #dbeafe; color: #1d4ed8; }
        .btn-action-status { background: #f8fafc; color: #475569; border-color: #cbd5e1; }
        .btn-action-status:hover { background: #e2e8f0; color: #1e293b; }
        .btn-action-delete { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
        .btn-action-delete:hover { background: #ffe4e6; color: #be123c; }

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .pagination-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .page-link-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            background: #fff;
            border: 1px solid #cbd5e1;
            transition: all 0.15s;
        }
        .page-link-item:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #1e293b;
        }
        .page-link-item.active {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(16,185,129,0.3);
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- HEADER UTAMA -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">MANAJEMEN BERKAS GURU</div>
                    <h1><i data-lucide="book-open"></i> Perangkat Pembelajaran</h1>
                    <p>Sinkronisasi dan monitoring berkas RPP, Modul Ajar, ATP, CP, Prota, Promes, & KKTP guru.</p>
                </div>
                <div class="header-actions-premium" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <a href="/admin/pkkm/cetak-bukti/perangkat-guru" target="_blank" class="e-btn" style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.35); color:#fff; backdrop-filter:blur(10px); text-decoration:none;" title="Buka Halaman Monitoring Perangkat Pembelajaran untuk Pengawas & Asesor PKKM">
                        <i data-lucide="eye" style="width:14px;height:14px;"></i> Halaman Pengawas PKKM (View-Only)
                    </a>
                    <?php if($is_pkkm_linked): ?>
                        <form action="/kurikulum/berkas/perangkat/lepas-link-pkkm" method="POST" style="margin:0;" onsubmit="return confirm('Lepaskan tautan halaman monitoring ini dari Master Berkas PKKM?');">
                            <button type="submit" class="e-btn" style="background:rgba(239, 68, 68, 0.22); border:1px solid rgba(239, 68, 68, 0.45); color:#fff; backdrop-filter:blur(10px);" title="Lepaskan link dari PKKM">
                                <i data-lucide="unlink" style="width:14px;height:14px;"></i> Lepas Link PKKM
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="/kurikulum/berkas/perangkat/pasang-link-pkkm" method="POST" style="margin:0;" onsubmit="return confirm('Lampirkan link halaman monitoring ini ke Master Berkas PKKM (RPP, Modul, Silabus)?');">
                            <button type="submit" class="e-btn" style="background:rgba(16,185,129,0.3); border:1px solid rgba(16,185,129,0.5); color:#fff; backdrop-filter:blur(10px);" title="Lampirkan link resmi ini ke Master Berkas PKKM">
                                <i data-lucide="link" style="width:14px;height:14px;"></i> Pasang Link ke PKKM
                            </button>
                        </form>
                    <?php endif; ?>
                    <button class="e-btn e-btn-primary" onclick="openUploadModal()">
                        <i data-lucide="upload-cloud"></i> Unggah Perangkat
                    </button>
                </div>
            </div>
        </div>

        <!-- NOTIFIKASI FEEDBACK -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] === 'link_attached'): ?>
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-left:4px solid #10b981; color:#065f46; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="link-2" style="color:#10b981; width:22px; height:22px; flex-shrink:0;"></i>
                    <div>
                        <strong>Tautan Berhasil Dilampirkan ke PKKM!</strong>
                        <span>Halaman Display Monitoring Perangkat Guru kini telah resmi terpasang pada Master Berkas PKKM (RPP, Modul Ajar, Silabus). Pengawas dapat langsung memeriksa berkas view-only secara praktis.</span>
                    </div>
                </div>
            <?php elseif($_GET['msg'] === 'link_detached'): ?>
                <div style="background:#fff1f2; border:1px solid #fecdd3; border-left:4px solid #e11d48; color:#9f1239; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="unlink" style="color:#e11d48; width:22px; height:22px; flex-shrink:0;"></i>
                    <span>Tautan halaman monitoring berhasil dilepas dari Master Berkas PKKM.</span>
                </div>
            <?php elseif($_GET['msg'] === 'unsynced_pkkm'): ?>
                <div style="background:#fff1f2; border:1px solid #fecdd3; border-left:4px solid #e11d48; color:#9f1239; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="unlink" style="color:#e11d48; width:22px; height:22px; flex-shrink:0;"></i>
                    <div>
                        <strong>Sinkronisasi Berhasil Dibatalkan!</strong>
                        <span>Sebanyak <?= (int)($_GET['count'] ?? 0) ?> tautan berkas perangkat guru telah dilepas dari Bukti Fisik PKKM. Berkas asli di portal Kurikulum tetap tersimpan dengan aman.</span>
                    </div>
                </div>


            <?php elseif($_GET['msg'] === 'uploaded'): ?>
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-left:4px solid #10b981; color:#065f46; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="check-circle" style="color:#10b981; width:22px; height:22px; flex-shrink:0;"></i>
                    <span>Dokumen perangkat pembelajaran berhasil diunggah dan disimpan ke server!</span>
                </div>
            <?php elseif($_GET['msg'] === 'status_updated'): ?>
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-left:4px solid #10b981; color:#065f46; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="check-circle" style="color:#10b981; width:22px; height:22px; flex-shrink:0;"></i>
                    <span>Status validasi dokumen berhasil diperbarui!</span>
                </div>
            <?php elseif($_GET['msg'] === 'deleted'): ?>
                <div style="background:#fff1f2; border:1px solid #fecdd3; border-left:4px solid #e11d48; color:#9f1239; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="trash-2" style="color:#e11d48; width:22px; height:22px; flex-shrink:0;"></i>
                    <span>Dokumen perangkat pembelajaran berhasil dihapus.</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($_GET['err'])): ?>
            <div style="background:#fef2f2; border:1px solid #fecaca; border-left:4px solid #ef4444; color:#991b1b; padding:1rem 1.25rem; border-radius:10px; margin-top:1.5rem; display:flex; align-items:center; gap:10px;">
                <i data-lucide="alert-triangle" style="color:#ef4444; width:22px; height:22px; flex-shrink:0;"></i>
                <span><?= htmlspecialchars($_GET['err']) ?></span>
            </div>
        <?php endif; ?>

        <!-- KPI STATISTIK -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
                    <i data-lucide="files"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">Total Perangkat</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#0f172a;"><?= number_format($stats['total']) ?></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
                    <i data-lucide="book-marked"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">Modul Ajar / RPP</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#2563eb;"><?= number_format($stats['modul_rpp']) ?></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#f0fdf4; color:#16a34a;">
                    <i data-lucide="git-branch"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">ATP & Silabus</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#16a34a;"><?= number_format($stats['atp_silabus']) ?></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
                    <i data-lucide="calendar"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">Prota & Promes</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#9333ea;"><?= number_format($stats['prota_promes']) ?></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
                    <i data-lucide="award"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">KKTP & CP</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#d97706;"><?= number_format($stats['kktp_cp']) ?></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#f0fdfa; color:#0d9488;">
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">Guru Mengunggah</div>
                    <div style="font-size:1.4rem; font-weight:700; color:#0d9488;"><?= number_format($stats['guru_count']) ?> Guru</div>
                </div>
            </div>
        </div>

        <!-- PANEL FILTER & PENCARIAN -->
        <div class="z-panel" style="margin-bottom: 1.5rem;">
            <div class="z-panel-body" style="padding: 1.25rem;">
                <form method="GET" action="/kurikulum/berkas/perangkat">
                    <div class="filter-grid">
                        <div>
                            <label class="filter-label"><i data-lucide="user" style="width:12px;height:12px;vertical-align:-1px;"></i> Filter Guru</label>
                            <select name="guru_id" class="filter-input">
                                <option value="0">-- Semua Guru --</option>
                                <?php foreach($guru_list as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= ($filter_guru == $g['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="filter-label"><i data-lucide="folder" style="width:12px;height:12px;vertical-align:-1px;"></i> Kategori Dokumen</label>
                            <select name="jenis" class="filter-input">
                                <option value="">-- Semua Kategori --</option>
                                <?php foreach($jenis_list as $jl): ?>
                                    <option value="<?= htmlspecialchars($jl) ?>" <?= ($filter_jenis === $jl) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($jl) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="filter-label"><i data-lucide="layers" style="width:12px;height:12px;vertical-align:-1px;"></i> Kelas</label>
                            <select name="kelas_id" class="filter-input">
                                <option value="0">-- Semua Kelas --</option>
                                <?php foreach($kelas_list as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= ($filter_kelas == $k['id']) ? 'selected' : '' ?>>
                                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="filter-label"><i data-lucide="book" style="width:12px;height:12px;vertical-align:-1px;"></i> Mata Pelajaran</label>
                            <select name="mapel_id" class="filter-input">
                                <option value="0">-- Semua Mapel --</option>
                                <?php foreach($mapel_list as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ($filter_mapel == $m['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nama_mapel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="filter-label"><i data-lucide="check-square" style="width:12px;height:12px;vertical-align:-1px;"></i> Status</label>
                            <select name="status" class="filter-input">
                                <option value="">-- Semua Status --</option>
                                <option value="Disetujui" <?= ($filter_status === 'Disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                <option value="Menunggu" <?= ($filter_status === 'Menunggu') ? 'selected' : '' ?>>Menunggu</option>
                                <option value="Revisi" <?= ($filter_status === 'Revisi') ? 'selected' : '' ?>>Revisi</option>
                            </select>
                        </div>

                        <div>
                            <label class="filter-label"><i data-lucide="search" style="width:12px;height:12px;vertical-align:-1px;"></i> Kata Kunci</label>
                            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="filter-input" placeholder="Judul, berkas, mapel...">
                        </div>

                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="z-btn z-btn-primary" style="flex:1; padding:0.55rem 1rem; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                                <i data-lucide="filter" style="width:15px;height:15px;"></i> Filter
                            </button>
                            <?php if($filter_guru || $filter_jenis || $filter_kelas || $filter_mapel || $filter_status || $q): ?>
                                <a href="/kurikulum/berkas/perangkat" class="z-btn z-btn-dark" style="padding:0.55rem 0.85rem; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filter">
                                    <i data-lucide="x" style="width:15px;height:15px;"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- DAFTAR PERANGKAT PEMBELAJARAN -->
        <div class="z-panel">
            <div class="z-panel-head" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Daftar Perangkat Pembelajaran Guru
                </div>
                <div style="font-size:0.85rem; color:#64748b;">
                    Menampilkan <strong><?= count($perangkat_list) ?></strong> dari <strong><?= number_format($total_filtered) ?></strong> berkas
                </div>
            </div>
            
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Perangkat & File</th>
                            <th>Guru Pengunggah</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th style="text-align: center;">Status</th>
                            <th>Waktu Unggah</th>
                            <th style="width: 150px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($perangkat_list)): ?>
                            <tr>
                                <td colspan="8" align="center" style="padding: 3rem 1rem;">
                                    <div style="color: var(--z-muted);">
                                        <i data-lucide="inbox" style="width:52px;height:52px;opacity:0.3;margin-bottom:1rem; stroke-width:1.5;"></i>
                                        <h4 style="margin:0 0 6px 0; color:#475569;">Tidak Ada Dokumen</h4>
                                        <p style="margin:0; font-size:0.9rem; color:#94a3b8;">Tidak ditemukan perangkat pembelajaran yang sesuai dengan kriteria filter.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = $offset + 1;
                            foreach($perangkat_list as $row): 
                                $jenis = $row['jenis_perangkat'] ?? 'Lainnya';
                                $badgeClass = 'badge-default';
                                if (stripos($jenis, 'modul') !== false || stripos($jenis, 'rpp') !== false) $badgeClass = 'badge-modul';
                                elseif (stripos($jenis, 'atp') !== false || stripos($jenis, 'silabus') !== false) $badgeClass = 'badge-atp';
                                elseif (stripos($jenis, 'prota') !== false || stripos($jenis, 'promes') !== false) $badgeClass = 'badge-prota';
                                elseif (stripos($jenis, 'kktp') !== false || stripos($jenis, 'capaian') !== false) $badgeClass = 'badge-kktp';
                                
                                $status = $row['status'] ?: 'Menunggu';
                                $statusClass = 'status-menunggu';
                                $statusIcon  = 'clock';
                                if ($status === 'Disetujui') { $statusClass = 'status-disetujui'; $statusIcon = 'check-circle'; }
                                elseif ($status === 'Revisi') { $statusClass = 'status-revisi'; $statusIcon = 'alert-triangle'; }
                            ?>
                            <tr>
                                <td align="center" style="color:#64748b; font-weight:600;"><?= $no++ ?></td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:4px;">
                                        <div style="font-weight:700; color:#0f172a; font-size:0.95rem;">
                                            <?= htmlspecialchars($row['judul'] ?: $row['nama_file']) ?>
                                        </div>
                                        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                            <span class="badge-jenis <?= $badgeClass ?>">
                                                <?= htmlspecialchars($jenis) ?>
                                            </span>
                                            <span style="font-size:0.75rem; color:#64748b; display:inline-flex; align-items:center; gap:3px;">
                                                <i data-lucide="file-text" style="width:12px;height:12px; color:#ef4444;"></i>
                                                <?= htmlspecialchars($row['nama_file']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:34px; height:34px; border-radius:50%; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; flex-shrink:0;">
                                            <?= strtoupper(substr($row['nama_guru'] ?? 'G', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:#1e293b; font-size:0.9rem; line-height:1.2;">
                                                <?= htmlspecialchars($row['nama_guru'] ?? 'Guru') ?>
                                            </div>
                                            <div style="font-size:0.75rem; color:#94a3b8;">
                                                NIP: <?= htmlspecialchars($row['nip_guru'] ?: '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:600; color:#334155; font-size:0.88rem;">
                                        <?= htmlspecialchars($row['nama_mapel'] ?? '-') ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="display:inline-block; padding:3px 8px; border-radius:6px; background:#f1f5f9; font-weight:700; font-size:0.8rem; color:#475569;">
                                        Kelas <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                                    </span>
                                </td>
                                <td align="center">
                                    <span class="badge-status <?= $statusClass ?>">
                                        <i data-lucide="<?= $statusIcon ?>" style="width:13px;height:13px;"></i>
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                    <?php if(!empty($row['komentar_kamad'])): ?>
                                        <div style="font-size:0.72rem; color:#64748b; margin-top:4px; max-width:140px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="<?= htmlspecialchars($row['komentar_kamad']) ?>">
                                            "<?= htmlspecialchars($row['komentar_kamad']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.82rem; color:#64748b; white-space:nowrap;">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span style="font-size:0.72rem; color:#94a3b8;"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</span>
                                </td>
                                <td align="center">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                        <!-- Tombol Buka File -->
                                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="btn-action btn-action-view" title="Buka / Unduh File">
                                            <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                        </a>

                                        <!-- Tombol Validasi Status -->
                                        <button type="button" class="btn-action btn-action-status" title="Validasi / Ubah Status" onclick="openStatusModal(<?= htmlspecialchars(json_encode([
                                            'id' => $row['id'],
                                            'judul' => $row['judul'] ?: $row['nama_file'],
                                            'nama_guru' => $row['nama_guru'],
                                            'status' => $row['status'] ?: 'Menunggu',
                                            'komentar' => $row['komentar_kamad'] ?? ''
                                        ])) ?>)">
                                            <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="/kurikulum/berkas/perangkat/delete" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-action btn-action-delete" title="Hapus Dokumen">
                                                <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if($total_pages > 1): ?>
                <?php
                // Build base pagination query string
                $queryArgs = $_GET;
                unset($queryArgs['page']);
                $baseQuery = http_build_query($queryArgs);
                $basePrefix = $baseQuery ? "?{$baseQuery}&page=" : "?page=";
                ?>
                <div class="pagination-container">
                    <div style="font-size:0.85rem; color:#64748b;">
                        Halaman <strong><?= $page ?></strong> dari <strong><?= $total_pages ?></strong>
                    </div>
                    <div class="pagination-links">
                        <?php if($page > 1): ?>
                            <a href="<?= $basePrefix . ($page - 1) ?>" class="page-link-item" title="Sebelumnya">
                                <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                            </a>
                        <?php endif; ?>

                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage   = min($total_pages, $page + 2);
                        if ($startPage > 1) {
                            echo '<a href="' . $basePrefix . '1" class="page-link-item">1</a>';
                            if ($startPage > 2) echo '<span style="padding:0 4px; color:#94a3b8;">...</span>';
                        }
                        for($p = $startPage; $p <= $endPage; $p++):
                        ?>
                            <a href="<?= $basePrefix . $p ?>" class="page-link-item <?= ($p == $page) ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php 
                        if ($endPage < $total_pages) {
                            if ($endPage < $total_pages - 1) echo '<span style="padding:0 4px; color:#94a3b8;">...</span>';
                            echo '<a href="' . $basePrefix . $total_pages . '" class="page-link-item">' . $total_pages . '</a>';
                        }
                        ?>

                        <?php if($page < $total_pages): ?>
                            <a href="<?= $basePrefix . ($page + 1) ?>" class="page-link-item" title="Selanjutnya">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- MODAL UNGGAH PERANGKAT -->
<div id="modalUpload" class="modal-backdrop">
    <div class="modal-box">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 style="margin:0; font-size:1.15rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;">
                <i data-lucide="upload-cloud" style="color:#10b981;"></i> Unggah Perangkat Pembelajaran
            </h3>
            <button onclick="closeUploadModal()" style="background:none; border:none; cursor:pointer; font-size:1.4rem; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form action="/kurikulum/berkas/perangkat/upload" method="POST" enctype="multipart/form-data">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1.1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Pilih Guru Pengajar <span style="color:#ef4444;">*</span></label>
                    <select name="guru_id" class="filter-input" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_lengkap']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Kelas</label>
                        <select name="kelas_id" class="filter-input">
                            <option value="0">-- Pilih Kelas --</option>
                            <?php foreach($kelas_list as $k): ?>
                                <option value="<?= $k['id'] ?>">Kelas <?= htmlspecialchars($k['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Mata Pelajaran</label>
                        <select name="mapel_id" class="filter-input">
                            <option value="0">-- Pilih Mapel --</option>
                            <?php foreach($mapel_list as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Kategori / Jenis Perangkat <span style="color:#ef4444;">*</span></label>
                    <select name="jenis_perangkat" class="filter-input" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Modul Ajar">Modul Ajar / RPP</option>
                        <option value="Alur Tujuan Pembelajaran (ATP)">Alur Tujuan Pembelajaran (ATP)</option>
                        <option value="Capaian Pembelajaran (CP)">Capaian Pembelajaran (CP)</option>
                        <option value="Program Tahunan (Prota)">Program Tahunan (Prota)</option>
                        <option value="Program Semester (Promes)">Program Semester (Promes)</option>
                        <option value="KKTP">KKTP</option>
                        <option value="Silabus / ATP">Silabus / ATP</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Judul / Nama Perangkat <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="judul" class="filter-input" placeholder="Contoh: Modul Ajar Matematika Bab 1" required>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Status Validasi Awal</label>
                    <select name="status" class="filter-input">
                        <option value="Disetujui" selected>Disetujui (Langsung Sah)</option>
                        <option value="Menunggu">Menunggu Verifikasi</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">File Dokumen (PDF/Docx, Max 10MB) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="file_perangkat" accept=".pdf,.doc,.docx,.zip,.rar" class="filter-input" style="padding:0.45rem; background:#fff;" required>
                </div>
            </div>
            <div style="padding:1rem 1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:0.75rem; background:#f8fafc; border-bottom-left-radius:14px; border-bottom-right-radius:14px;">
                <button type="button" onclick="closeUploadModal()" style="padding:0.6rem 1.25rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.6rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.25);">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL VALIDASI / UBAH STATUS -->
<div id="modalStatus" class="modal-backdrop">
    <div class="modal-box" style="max-width:480px;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 style="margin:0; font-size:1.15rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;">
                <i data-lucide="check-square" style="color:#10b981;"></i> Validasi Perangkat
            </h3>
            <button onclick="closeStatusModal()" style="background:none; border:none; cursor:pointer; font-size:1.4rem; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form action="/kurikulum/berkas/perangkat/status" method="POST">
            <input type="hidden" name="id" id="statusId">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1.1rem;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.85rem;">
                    <div style="font-size:0.78rem; color:#64748b; font-weight:600;">DOKUMEN</div>
                    <div id="statusJudul" style="font-weight:700; color:#0f172a; margin-top:2px;">-</div>
                    <div id="statusGuru" style="font-size:0.82rem; color:#475569; margin-top:2px;">-</div>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Status Validasi <span style="color:#ef4444;">*</span></label>
                    <select name="status" id="statusSelect" class="filter-input" required>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Revisi">Revisi (Perlu Perbaikan)</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600; font-size:0.85rem; color:#475569;">Catatan / Feedback Kurikulum</label>
                    <textarea name="komentar_kamad" id="statusKomentar" class="filter-input" style="min-height:90px; resize:vertical;" placeholder="Tuliskan catatan perbaikan jika ada..."></textarea>
                </div>
            </div>
            <div style="padding:1rem 1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:0.75rem; background:#f8fafc; border-bottom-left-radius:14px; border-bottom-right-radius:14px;">
                <button type="button" onclick="closeStatusModal()" style="padding:0.6rem 1.25rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.6rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.25);">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadModal() {
        document.getElementById('modalUpload').style.display = 'flex';
    }
    function closeUploadModal() {
        document.getElementById('modalUpload').style.display = 'none';
    }

    function openStatusModal(data) {
        document.getElementById('statusId').value = data.id;
        document.getElementById('statusJudul').innerText = data.judul;
        document.getElementById('statusGuru').innerText = 'Guru: ' + (data.nama_guru || '-');
        document.getElementById('statusSelect').value = data.status || 'Disetujui';
        document.getElementById('statusKomentar').value = data.komentar || '';
        document.getElementById('modalStatus').style.display = 'flex';
    }
    function closeStatusModal() {
        document.getElementById('modalStatus').style.display = 'none';
    }

    // Tutup modal jika klik backdrop
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
