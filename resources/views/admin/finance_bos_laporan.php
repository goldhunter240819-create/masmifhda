<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Laporan Keuangan BOS'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Laporan BOS</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Laporan Rekapitulasi BOS</h1><p>Unduh laporan bulanan/tahunan dana BOS</p></div>
</div>

<div class="z-panel" style="max-width: 600px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="printer"></i>Cetak Laporan BOS</div></div>
    <div class="z-panel-body">
        <form onsubmit="event.preventDefault();alert('Fitur cetak laporan sedang dalam pengembangan.');">
            <div class="z-form-group">
                <label class="z-label">Periode Bulan</label>
                <input class="z-field" type="month" value="<?php echo date('Y-m'); ?>" required>
            </div>
            <div class="z-form-group">
                <label class="z-label">Format Laporan</label>
                <select class="z-field">
                    <option value="pdf">PDF Dokumen</option>
                    <option value="excel">Microsoft Excel (.xlsx)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; justify-content: center;"><i data-lucide="download"></i> Unduh Laporan</button>
        </form>
    </div>
</div>
</div></main></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
