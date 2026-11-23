<?php
$pageTitle = 'Berita Acara & Kehadiran Ruang CBT';
$active_menu = 'berita_acara_ruang';

$topikList = $topikList ?? [];
$topikId = (int)($topikId ?? 0);
$topik = $topik ?? [];
$allRuang = $allRuang ?? [];
$ruangId = (int)($ruangId ?? 0);
$ruang = $ruang ?? [];
$tanggal = $tanggal ?? date('Y-m-d');
$sesiLabel = $sesiLabel ?? 'Sesi 1';
$pesertaList = $pesertaList ?? [];
$tidakHadirList = $tidakHadirList ?? [];
$mapelNama = $mapelNama ?? 'Asesmen CBT';
$jamSesi = $jamSesi ?? '08:00 - 09:00 WIB';
$pengawasNama = $pengawasNama ?? 'Bapak / Ibu Pengawas';
$pengawasNip = $pengawasNip ?? '-';
$totalTerdaftar = $totalTerdaftar ?? count($pesertaList);
$totalHadir = $totalHadir ?? 0;
$totalTidakHadir = $totalTidakHadir ?? 0;
$persenHadir = $totalTerdaftar > 0 ? round(($totalHadir / $totalTerdaftar) * 100) : 0;

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$curDayIndo = $hariIndo[date('w', strtotime($tanggal))];
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglFormatIndo = $curDayIndo . ', ' . date('j', strtotime($tanggal)) . ' ' . $bulanIndo[(int)date('n', strtotime($tanggal))] . ' ' . date('Y', strtotime($tanggal));

$topikNama = !empty($topik['nama_topik']) ? trim($topik['nama_topik']) : 'ASESMEN CBT';
$topikTA = !empty($topik['tahun_ajaran']) ? trim($topik['tahun_ajaran']) : '2026/2027';

// Base URL helper
$bu = function($path = '') {
    return function_exists('base_url') ? base_url($path) : '/' . ltrim($path, '/');
};
?>
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/all.min.css'); ?>">
<style>
    .ba-hero-header {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);
        margin-bottom: 1.5rem;
    }
    .ba-hero-header::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .ba-header-inner {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.25rem;
    }
    .ba-hero-title h1 {
        font-size: 1.55rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        margin-bottom: 4px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ba-hero-title p {
        font-size: 0.88rem;
        color: #e0f2fe;
        margin: 0;
        max-width: 600px;
        line-height: 1.4;
    }
    .ba-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 30px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .ba-action-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .btn-act {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }
    .btn-act:hover {
        transform: translateY(-2px);
    }
    .btn-act-ba {
        background: #ffffff;
        color: #0369a1;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .btn-act-ba:hover {
        background: #f0f9ff;
        color: #0284c7;
        box-shadow: 0 6px 20px rgba(0,0,0,0.18);
    }
    .btn-act-hadir {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }
    .btn-act-hadir:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
    }
    .btn-act-mapel {
        background: rgba(255,255,255,0.15);
        color: #ffffff;
        border: 1px solid rgba(255,255,255,0.35);
        backdrop-filter: blur(8px);
    }
    .btn-act-mapel:hover {
        background: rgba(255,255,255,0.25);
        color: #ffffff;
    }

    /* Filter Card */
    .ba-filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        align-items: flex-end;
    }
    .filter-group label {
        display: block;
        font-size: 0.76rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .filter-control {
        width: 100%;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.2s;
    }
    .filter-control:focus {
        border-color: #0284c7;
        background: #ffffff;
    }
    .btn-filter-apply {
        background: #0284c7;
        color: #fff;
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 42px;
        transition: background 0.2s;
    }
    .btn-filter-apply:hover {
        background: #0369a1;
    }

    /* KPI Grid */
    .kpi-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .kpi-val {
        font-size: 1.65rem;
        font-weight: 900;
        line-height: 1.1;
        color: #0f172a;
    }
    .kpi-lbl {
        font-size: 0.74rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Content Box */
    .ba-box-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .ba-box-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #fafafa;
    }
    .ba-box-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ba-box-title .icon-cube {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .ba-box-title h2 {
        font-size: 1.05rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }
    .ba-box-title p {
        font-size: 0.78rem;
        color: #64748b;
        margin: 2px 0 0 0;
    }
    .ba-box-body {
        padding: 1.5rem;
    }

    /* Detail Grid */
    .info-params-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 14px;
        background: #f8fafc;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }
    .param-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .param-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .param-value {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }

    /* Table */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }
    .table-modern td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    .badge-scan-time {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 4px 10px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 0.8rem;
        font-weight: 800;
    }
    .badge-scan-none {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
    }
    .badge-status-hadir {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 800;
        background: #22c55e;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-status-absen {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 800;
        background: #ef4444;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Hero Header Banner -->
            <div class="ba-hero-header">
                <div class="ba-header-inner">
                    <div class="ba-hero-title">
                        <div class="ba-badge-pill">
                            <i class="fa-solid fa-file-signature"></i> DOKUMEN ASESMEN CBT RESMI
                        </div>
                        <h1>
                            <i class="fa-solid fa-file-lines"></i>
                            Berita Acara & Kehadiran Ruang CBT
                        </h1>
                        <p>Pusat kendali dan dokumentasi resmi Berita Acara Ujian serta Daftar Hadir Peserta berbasis jam scan presensi kartu siswa persesi terverifikasi.</p>
                    </div>

                    <!-- 3 Print Action Buttons -->
                    <div class="ba-action-btns">
                        <!-- 1. Cetak Berita Acara (A4) -->
                        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-ba?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" 
                           target="_blank" 
                           class="btn-act btn-act-ba"
                           title="Cetak Lembar Resmi Berita Acara Pelaksanaan Ujian (A4)">
                            <i class="fa-solid fa-print"></i>
                            <span>Cetak Berita Acara (A4)</span>
                        </a>

                        <!-- 2. Cetak Daftar Hadir Ruang (A4) -->
                        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-hadir-ruang?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" 
                           target="_blank" 
                           class="btn-act btn-act-hadir"
                           title="Cetak Lembar Resmi Daftar Hadir Siswa Ruangan dengan Waktu Scan Kartu (A4)">
                            <i class="fa-solid fa-clipboard-user"></i>
                            <span>Cetak Daftar Hadir Ruang (A4)</span>
                        </a>

                        <!-- 3. Cetak Daftar Hadir Mapel/Kelas (A4) -->
                        <a href="<?= $bu('cbt/hasil/daftar-hadir-kelas?topik_id=' . $topikId) ?>" 
                           target="_blank" 
                           class="btn-act btn-act-mapel"
                           title="Buka Cetak Daftar Hadir Siswa Per-Kelas Per-Mata Pelajaran Untuk Guru Mapel">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Cetak Hadir Mapel/Kelas</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Controls Bar -->
            <div class="ba-filter-card">
                <form method="GET" action="<?= $bu('cbt/ruang/berita-acara') ?>" class="filter-grid">
                    <div class="filter-group">
                        <label><i class="fa-solid fa-layer-group"></i> Topik Asesmen</label>
                        <select name="topik_id" class="filter-control">
                            <?php foreach ($topikList as $tp): ?>
                                <option value="<?= $tp['id'] ?>" <?= ((int)$topikId === (int)$tp['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tp['nama_topik']) ?> (<?= htmlspecialchars($tp['tahun_ajaran']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fa-solid fa-door-open"></i> Ruang Ujian</label>
                        <select name="ruang_id" class="filter-control">
                            <?php foreach ($allRuang as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= ((int)$ruangId === (int)$r['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nama_ruang']) ?> (Kapasitas: <?= (int)$r['kapasitas'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fa-solid fa-calendar-days"></i> Tanggal Ujian</label>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" class="filter-control">
                    </div>

                    <div class="filter-group">
                        <label><i class="fa-solid fa-clock"></i> Sesi Ujian</label>
                        <select name="sesi" class="filter-control">
                            <?php 
                            $sesiOptions = ['Sesi 1', 'Sesi 2', 'Sesi 3', 'Sesi 4'];
                            foreach ($sesiOptions as $opt): 
                            ?>
                                <option value="<?= $opt ?>" <?= ($sesiLabel === $opt) ? 'selected' : '' ?>>
                                    <?= $opt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group" style="display: flex; gap: 8px;">
                        <button type="submit" class="btn-filter-apply" style="flex: 1;">
                            <i class="fa-solid fa-filter"></i>
                            <span>Terapkan Filter</span>
                        </button>
                        <a href="<?= $bu('cbt/ruang/berita-acara') ?>" class="btn-filter-apply" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- KPI Summary Cards -->
            <div class="kpi-stat-grid">
                <!-- Card 1: Total Terdaftar -->
                <div class="kpi-card">
                    <div class="kpi-icon-box" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="kpi-val"><?= $totalTerdaftar ?></div>
                        <div class="kpi-lbl">Total Siswa Terdaftar</div>
                    </div>
                </div>

                <!-- Card 2: Hadir (Scan) -->
                <div class="kpi-card">
                    <div class="kpi-icon-box" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="kpi-val" style="color: #16a34a;"><?= $totalHadir ?> <span style="font-size: 0.95rem; font-weight: 700; color: #64748b;">(<?= $persenHadir ?>%)</span></div>
                        <div class="kpi-lbl">Hadir (Terscan Presensi)</div>
                    </div>
                </div>

                <!-- Card 3: Tidak Hadir -->
                <div class="kpi-card">
                    <div class="kpi-icon-box" style="background: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <div class="kpi-val" style="color: #dc2626;"><?= $totalTidakHadir ?></div>
                        <div class="kpi-lbl">Tidak Hadir / Belum Scan</div>
                    </div>
                </div>

                <!-- Card 4: Pengawas Bertugas -->
                <div class="kpi-card">
                    <div class="kpi-icon-box" style="background: #f3e8ff; color: #9333ea;">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div class="kpi-val" style="font-size: 1.05rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" title="<?= htmlspecialchars($pengawasNama) ?>">
                            <?= htmlspecialchars($pengawasNama) ?>
                        </div>
                        <div class="kpi-lbl">Pengawas Ruang Bertugas</div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: RINGKASAN BERITA ACARA RUANGAN -->
            <div class="ba-box-card">
                <div class="ba-box-header">
                    <div class="ba-box-title">
                        <div class="icon-cube" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <h2>1. Ringkasan Berita Acara Pelaksanaan Ujian Ruang</h2>
                            <p>Data resmi pelaksanaan asesmen pada ruang dan sesi terpilih</p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-ba?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" 
                           target="_blank" 
                           class="btn-act btn-act-ba" style="background: #0284c7; color: #fff;">
                            <i class="fa-solid fa-print"></i>
                            <span>Cetak Dokumen Berita Acara (A4)</span>
                        </a>
                    </div>
                </div>
                <div class="ba-box-body">
                    <div class="info-params-grid">
                        <div class="param-item">
                            <span class="param-label">Ruang Ujian:</span>
                            <span class="param-value"><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang 01') ?></span>
                        </div>
                        <div class="param-item">
                            <span class="param-label">Hari & Tanggal:</span>
                            <span class="param-value"><?= htmlspecialchars($tglFormatIndo) ?></span>
                        </div>
                        <div class="param-item">
                            <span class="param-label">Sesi & Jam Ujian:</span>
                            <span class="param-value"><?= htmlspecialchars($sesiLabel) ?> (<?= htmlspecialchars($jamSesi) ?>)</span>
                        </div>
                        <div class="param-item">
                            <span class="param-label">Mata Pelajaran:</span>
                            <span class="param-value" style="color: #0284c7;"><?= htmlspecialchars($mapelNama) ?></span>
                        </div>
                        <div class="param-item">
                            <span class="param-label">Pengawas Bertugas:</span>
                            <span class="param-value"><?= htmlspecialchars($pengawasNama) ?> (NIP: <?= htmlspecialchars($pengawasNip) ?>)</span>
                        </div>
                        <div class="param-item">
                            <span class="param-label">Topik Asesmen:</span>
                            <span class="param-value"><?= htmlspecialchars($topikNama) ?> (<?= htmlspecialchars($topikTA) ?>)</span>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border-left: 4px solid #0284c7; padding: 14px 18px; border-radius: 0 12px 12px 0; margin-bottom: 1rem;">
                        <div style="font-weight: 800; color: #0f172a; margin-bottom: 4px; font-size: 0.88rem;">Pernyataan Berita Acara:</div>
                        <div style="font-size: 0.84rem; color: #475569; line-height: 1.5;">
                            Pada hari <strong><?= $curDayIndo ?></strong>, tanggal <strong><?= htmlspecialchars(date('j', strtotime($tanggal)) . ' ' . $bulanIndo[(int)date('n', strtotime($tanggal))] . ' ' . date('Y', strtotime($tanggal))) ?></strong>, telah diselenggarakan asesmen <strong><?= htmlspecialchars($topikNama) ?></strong> untuk mata pelajaran <strong><?= htmlspecialchars($mapelNama) ?></strong> pada <strong><?= htmlspecialchars($sesiLabel) ?><?php if (!empty($jamSesi)): ?> (<?= htmlspecialchars($jamSesi) ?>)<?php endif; ?></strong> bertempat di <strong><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian') ?></strong>.
                            Jumlah peserta yang terdaftar sebanyak <strong><?= $totalTerdaftar ?> orang</strong>, hadir mengikuti ujian sebanyak <strong><?= $totalHadir ?> orang</strong>, dan tidak hadir sebanyak <strong><?= $totalTidakHadir ?> orang</strong>.
                        </div>
                    </div>

                    <?php if (!empty($tidakHadirList)): ?>
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 12px 16px; margin-top: 1rem;">
                        <div style="font-weight: 800; color: #9f1239; font-size: 0.82rem; margin-bottom: 6px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Peserta Tidak Hadir (<?= count($tidakHadirList) ?> Siswa):
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php foreach ($tidakHadirList as $th): ?>
                                <span style="background: #ffffff; border: 1px solid #fecdd3; color: #9f1239; padding: 3px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700;">
                                    <?= htmlspecialchars($th['nama_siswa']) ?> (<?= htmlspecialchars($th['kelas_nama'] ?: '-') ?>) - Meja <?= (int)$th['nomor_meja'] ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- BAGIAN 2: TABEL DAFTAR HADIR RUANGAN (JAM SCAN KARTU SISWA) -->
            <div class="ba-box-card">
                <div class="ba-box-header">
                    <div class="ba-box-title">
                        <div class="icon-cube" style="background: #dcfce7; color: #16a34a;">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <div>
                            <h2>2. Daftar Hadir Peserta Ruangan (Waktu Presensi Scan Kartu Siswa)</h2>
                            <p>Data jam scan kartu per-sesi terekam otomatis secara digital tanpa perlu tanda tangan pulpen manual</p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-hadir-ruang?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" 
                           target="_blank" 
                           class="btn-act btn-act-hadir">
                            <i class="fa-solid fa-print"></i>
                            <span>Cetak Daftar Hadir Ruang (A4)</span>
                        </a>
                    </div>
                </div>
                <div class="ba-box-body" style="padding: 0;">
                    <div style="overflow-x: auto;">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">No</th>
                                    <th style="width: 75px; text-align: center;">No Meja</th>
                                    <th style="width: 120px;">No. Peserta</th>
                                    <th style="width: 100px;">NISN / NIS</th>
                                    <th>Nama Lengkap Siswa</th>
                                    <th style="width: 85px; text-align: center;">Kelas</th>
                                    <th style="min-width: 170px;">Waktu Scan Kartu (Presensi)</th>
                                    <th style="width: 110px; text-align: center;">Status Hadir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pesertaList)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: #94a3b8; font-style: italic;">
                                            <i class="fa-solid fa-users-slash" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            Belum ada peserta yang ditempatkan di ruangan ini. Atur denah tempat duduk di menu Ruang & Tempat Duduk.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pesertaList as $idx => $ps): ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: 800; color: #64748b;"><?= $idx + 1 ?></td>
                                            <td style="text-align: center; font-weight: 900; color: #0284c7;">
                                                <span style="background: #e0f2fe; padding: 3px 8px; border-radius: 6px;">
                                                    <?= htmlspecialchars($ps['nomor_meja'] ?: '-') ?>
                                                </span>
                                            </td>
                                            <td style="font-family: monospace; font-weight: 800; color: #334155;">
                                                <?= htmlspecialchars($ps['no_peserta'] ?? ($ps['nis'] ?? '-')) ?>
                                            </td>
                                            <td style="font-family: monospace; font-size: 0.8rem; color: #64748b;">
                                                <?= htmlspecialchars($ps['nisn'] ?: ($ps['nis'] ?: '-')) ?>
                                            </td>
                                            <td>
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?= htmlspecialchars($ps['nama_siswa']) ?></div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-weight: 700; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 0.78rem;">
                                                    <?= htmlspecialchars($ps['kelas_nama'] ?: '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($ps['is_hadir']): ?>
                                                    <div class="badge-scan-time">
                                                        <i class="fa-solid fa-clock-check" style="color: #16a34a;"></i>
                                                        <span><?= htmlspecialchars($ps['waktu_scan']) ?></span>
                                                    </div>
                                                    <span style="font-size: 0.72rem; color: #64748b; margin-left: 6px;"><?= htmlspecialchars($ps['metode_scan']) ?></span>
                                                <?php else: ?>
                                                    <div class="badge-scan-none">
                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                        <span>Belum Scan Kartu</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($ps['is_hadir']): ?>
                                                    <span class="badge-status-hadir">HADIR</span>
                                                <?php else: ?>
                                                    <span class="badge-status-absen">TIDAK HADIR</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
