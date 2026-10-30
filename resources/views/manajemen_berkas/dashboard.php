<?php
$pageTitle = 'Dashboard Monitoring';
$active_menu = 'dashboard';
$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
?>
<style>
    /* ─── Hero Banner (SIAKAD Style) ─── */
    .welcome-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 20px;
        padding: 2.2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 15px 35px -10px rgba(2, 132, 199, 0.4);
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
        color: #0284c7;
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

    /* Mini stats in banner right side */
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
                        <i data-lucide="folder-check" style="width: 14px; height: 14px;"></i> PUSAT DOKUMEN & SUPERVISI GURU v2.0
                    </div>
                    <h1>Selamat Datang, <?php echo htmlspecialchars($nama); ?>! <span class="wave">👋</span></h1>
                    <p>
                        Pusat digitalisasi dokumen madrasah. Kelola, monitor, dan verifikasi berkas pribadi dewan guru serta perangkat pembelajaran (Modul Ajar, ATP, Prota, Promes) secara efisien dalam satu pintu.
                    </p>
                    <div class="hero-actions">
                        <a href="<?php echo base_url($baseRoute . '/perangkat'); ?>" class="hero-btn primary">
                            <i data-lucide="book-marked" style="width: 16px; height: 16px;"></i> Perangkat Pembelajaran
                        </a>
                        <a href="<?php echo base_url($baseRoute . '/pribadi'); ?>" class="hero-btn secondary">
                            <i data-lucide="file-badge" style="width: 16px; height: 16px;"></i> Berkas Pribadi
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
                        <div class="val"><?php echo date('d'); ?></div>
                        <div class="lab"><?php echo date('M Y'); ?></div>
                    </div>
                    <div class="mini-stat">
                        <div class="val" style="color: #67e8f9;"><?php echo $persenKelengkapan; ?>%</div>
                        <div class="lab">Partisipasi</div>
                    </div>
                </div>

                <!-- Floating Giant Watermark Icon -->
                <div class="hero-bg-icon">
                    <i data-lucide="folder-kanban"></i>
                </div>
            </div>

            <!-- 4 Stat KPI Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <!-- Total Guru -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="users" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalGuru; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Total Dewan Guru</div>
                    </div>
                </div>

                <!-- Perangkat Pembelajaran -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="book-open-check" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalPerangkat; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Perangkat Mengajar</div>
                    </div>
                </div>

                <!-- Berkas Pribadi -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="file-badge" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalBerkasPribadi; ?></div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Berkas Pribadi Guru</div>
                    </div>
                </div>

                <!-- Partisipasi -->
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="pie-chart" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $persenKelengkapan; ?>%</div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-top: 4px;">Partisipasi Guru (<?php echo $guruAktifCount; ?>/<?php echo $totalGuru; ?>)</div>
                    </div>
                </div>
            </div>

            <!-- Two Column Overview Tables -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                
                <!-- Section 1: Perangkat Pembelajaran Terbaru -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.95rem; color: #1e293b;">
                            <i data-lucide="book-marked" style="width: 18px; height: 18px; color: #0284c7;"></i>
                            Perangkat Pembelajaran Terbaru
                        </div>
                        <a href="<?php echo base_url($baseRoute . '/perangkat'); ?>" style="font-size: 0.8rem; font-weight: 700; color: #0284c7; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Semua <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                        </a>
                    </div>
                    <div style="padding: 0;">
                        <?php if (empty($perangkatList)): ?>
                            <div style="padding: 2.5rem 1.5rem; text-align: center; color: #94a3b8;">
                                <i data-lucide="folder-x" style="width: 40px; height: 40px; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.85rem; font-weight: 600;">Belum ada perangkat pembelajaran yang diunggah.</div>
                            </div>
                        <?php else: ?>
                            <div class="z-table-wrap">
                                <table class="z-table">
                                    <thead>
                                        <tr>
                                            <th>Guru</th>
                                            <th>Kategori / Mapel</th>
                                            <th>Judul Berkas</th>
                                            <th>Status</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $topPerangkat = array_slice($perangkatList, 0, 5);
                                        foreach($topPerangkat as $p): 
                                            $fUrl = (strpos($p['file_path'], 'http') === 0) ? $p['file_path'] : base_url('public/uploads/guru_perangkat/' . $p['file_path']);
                                            $st = $p['status_review'] ?? 'Menunggu';
                                            $stClass = ($st === 'Disetujui') ? 'background:#dcfce7;color:#15803d;' : (($st === 'Perlu Revisi') ? 'background:#fee2e2;color:#b91c1c;' : 'background:#fef3c7;color:#b45309;');
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($p['nama_guru'] ?? 'Guru'); ?></div>
                                                <div style="font-size: 0.72rem; color: #64748b;"><?php echo htmlspecialchars($p['nip_guru'] ?: '-'); ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #e0f2fe; color: #0369a1;">
                                                    <?php echo htmlspecialchars($p['kategori'] ?? 'Modul Ajar'); ?>
                                                </span>
                                                <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                                                    <?php echo htmlspecialchars(($p['mapel_nama'] ?? $p['nama_mapel'] ?? '-') . ' - ' . ($p['kelas_nama'] ?? $p['nama_kelas'] ?? '-')); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #334155; font-size: 0.8rem;"><?php echo htmlspecialchars($p['nama_berkas']); ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; <?php echo $stClass; ?>">
                                                    <?php echo htmlspecialchars($st); ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <button type="button" onclick="openPreview('<?php echo $fUrl; ?>', '<?php echo addslashes($p['nama_berkas']); ?>')" class="e-btn e-btn-blue-light" style="padding: 4px 8px; font-size: 0.72rem;" title="Pratinjau">
                                                    <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                                </button>
                                                <a href="<?php echo $fUrl; ?>" target="_blank" class="e-btn e-btn-primary" style="padding: 4px 8px; font-size: 0.72rem;" title="Buka / Unduh">
                                                    <i data-lucide="download" style="width: 13px; height: 13px;"></i>
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

                <!-- Section 2: Berkas Pribadi Terbaru -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.95rem; color: #1e293b;">
                            <i data-lucide="file-badge" style="width: 18px; height: 18px; color: #059669;"></i>
                            Berkas Pribadi & Portofolio Terbaru
                        </div>
                        <a href="<?php echo base_url($baseRoute . '/pribadi'); ?>" style="font-size: 0.8rem; font-weight: 700; color: #059669; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Semua <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                        </a>
                    </div>
                    <div style="padding: 0;">
                        <?php if (empty($berkasPribadiList)): ?>
                            <div style="padding: 2.5rem 1.5rem; text-align: center; color: #94a3b8;">
                                <i data-lucide="folder-x" style="width: 40px; height: 40px; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.85rem; font-weight: 600;">Belum ada berkas pribadi yang diunggah.</div>
                            </div>
                        <?php else: ?>
                            <div class="z-table-wrap">
                                <table class="z-table">
                                    <thead>
                                        <tr>
                                            <th>Guru</th>
                                            <th>Kategori</th>
                                            <th>Nama Dokumen</th>
                                            <th>Tanggal</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $topPribadi = array_slice($berkasPribadiList, 0, 5);
                                        foreach($topPribadi as $bp): 
                                            $bpFileUrl = base_url('public/uploads/guru_berkas/' . $bp['file_path']);
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($bp['nama_guru'] ?? 'Guru'); ?></div>
                                                <div style="font-size: 0.72rem; color: #64748b;"><?php echo htmlspecialchars($bp['status_kepegawaian'] ?: 'Guru'); ?></div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #ecfdf5; color: #047857;">
                                                    <?php echo htmlspecialchars($bp['kategori'] ?? 'Umum'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #334155; font-size: 0.8rem;"><?php echo htmlspecialchars($bp['nama_berkas']); ?></div>
                                            </td>
                                            <td style="font-size: 0.75rem; color: #64748b; white-space: nowrap;">
                                                <?php echo date('d M Y', strtotime($bp['created_at'])); ?>
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <button type="button" onclick="openPreview('<?php echo $bpFileUrl; ?>', '<?php echo addslashes($bp['nama_berkas']); ?>')" class="e-btn e-btn-blue-light" style="padding: 4px 8px; font-size: 0.72rem;" title="Pratinjau">
                                                    <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                                </button>
                                                <a href="<?php echo $bpFileUrl; ?>" target="_blank" class="e-btn e-btn-primary" style="padding: 4px 8px; font-size: 0.72rem;" title="Unduh">
                                                    <i data-lucide="download" style="width: 13px; height: 13px;"></i>
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

            </div>

        </div>
    </div>
</div>

<!-- Modal PDF / Image Viewer -->
<div id="previewModal" class="modal-overlay" onclick="closePreview(event)">
    <div style="background: #ffffff; width: 92%; max-width: 950px; height: 85vh; border-radius: 20px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2);">
        <div style="padding: 14px 20px; background: #0f172a; color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-text" style="width: 18px; height: 18px; color: #38bdf8;"></i>
                <span id="previewModalTitle">Pratinjau Dokumen</span>
            </div>
            <button type="button" onclick="document.getElementById('previewModal').classList.remove('open')" style="background: none; border: none; color: #cbd5e1; cursor: pointer; display: flex; align-items: center; padding: 4px; border-radius: 6px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div style="flex: 1; background: #f1f5f9;">
            <iframe id="previewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    function openPreview(url, title) {
        document.getElementById('previewModalTitle').innerText = title || 'Pratinjau Dokumen';
        document.getElementById('previewIframe').src = url;
        document.getElementById('previewModal').classList.add('open');
    }

    function closePreview(e) {
        if (e.target.id === 'previewModal') {
            document.getElementById('previewModal').classList.remove('open');
            document.getElementById('previewIframe').src = '';
        }
    }
</script>
