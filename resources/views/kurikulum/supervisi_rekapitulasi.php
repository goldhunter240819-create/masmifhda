<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Rekapitulasi Supervisi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
    <style>
        .progress-bar { height: 8px; border-radius: 4px; background: #e2e8f0; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; }
        .stat-card { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1rem; }
        
        @media print {
            .z-sidebar, .z-header, .header-actions-premium { display: none !important; }
            .z-main { margin-left: 0 !important; padding: 0 !important; }
            .z-wrap { display: block !important; }
            body { background: #fff !important; }
            .z-panel { box-shadow: none !important; border: 1px solid #000 !important; }
            .progress-bar { border: 1px solid #000; }
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">ANALYTICS</div>
                    <h1><i data-lucide="pie-chart"></i> Rekapitulasi Terpadu</h1>
                    <p>Laporan progress pelaksanaan seluruh instrumen supervisi tahun ajaran ini.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="window.print()">
                        <i data-lucide="printer"></i> Cetak Laporan
                    </button>
                    <a href="/kurikulum/supervisi" class="e-btn e-btn-outline" style="background:#fff;">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-top:1.5rem;">
            <div class="stat-card">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="users" style="width:24px; height:24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">Total Guru Aktif</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b;"><?= $totalGuru ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: #f0fdf4; color: #10b981; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="check-circle" style="width:24px; height:24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">Target Instrumen</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">11 Modul</div>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="bar-chart-2"></i> Progress Pelaksanaan per Kategori
                </div>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php foreach($rekap as $r): ?>
                    <div style="border:1px solid #e2e8f0; padding:1.25rem; border-radius:12px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                            <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($r['kategori']) ?></div>
                            <div style="font-weight:700; color:var(--z-primary);"><?= $r['persen'] ?>%</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill <?= $r['color'] ?>" style="width: <?= $r['persen'] ?>%;"></div>
                        </div>
                        <div style="font-size:0.8rem; color:#64748b; margin-top:0.5rem; text-align:right;">
                            <?= $r['total'] ?> / <?= $totalGuru ?> Guru Disupervisi
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="users"></i> Rekapitulasi per Guru
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th style="text-align:center;">Modul Selesai</th>
                            <th style="width: 250px;">Progress Kelengkapan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($guruDetail as $gd): ?>
                        <tr>
                            <td align="center"><?= $no++ ?></td>
                            <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($gd['nama']) ?></td>
                            <td><?= htmlspecialchars($gd['nip'] ?? '-') ?></td>
                            <td align="center">
                                <span style="font-weight:bold; color:<?= $gd['completed'] == $gd['total_wajib'] ? '#10b981' : '#f59e0b' ?>;">
                                    <?= $gd['completed'] ?> / <?= $gd['total_wajib'] ?>
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div class="progress-bar" style="flex:1;">
                                        <div class="progress-fill <?= $gd['persen'] >= 100 ? 'bg-success' : ($gd['persen'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" style="width: <?= $gd['persen'] ?>%;"></div>
                                    </div>
                                    <div style="font-size:0.85rem; font-weight:600; min-width:40px; text-align:right;"><?= $gd['persen'] ?>%</div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
