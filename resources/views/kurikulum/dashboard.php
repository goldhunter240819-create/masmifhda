<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Dashboard Kurikulum'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- MULAI KONTEN ANDA DI SINI -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">KURIKULUM</div>
                    <h1><i data-lucide="layout-dashboard"></i> Dashboard Kurikulum</h1>
                    <p>Ringkasan manajemen berkas dan supervisi akademik.</p>
                </div>
            </div>
        </div>
        
        <div class="z-grid2" style="gap: 1.5rem; margin-top: 1.5rem;">
            <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title">
                        <i data-lucide="folder"></i> Status Berkas
                    </div>
                </div>
                <div class="z-panel-body">
                    <p style="color: var(--z-muted);">Belum ada data berkas yang diunggah.</p>
                </div>
            </div>
            
            <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title">
                        <i data-lucide="eye"></i> Jadwal Supervisi
                    </div>
                </div>
                <div class="z-panel-body">
                    <p style="color: var(--z-muted);">Belum ada jadwal supervisi terdekat.</p>
                </div>
            </div>
        </div>
        <!-- AKHIR KONTEN ANDA -->

    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
