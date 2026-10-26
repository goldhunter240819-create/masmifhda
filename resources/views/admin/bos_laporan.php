<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Laporan Dana BOS'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Laporan BOS</span><span class="z-badge">Admin BOS</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Admin BOS</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Laporan Rekapitulasi BOS</h1><p>Unduh laporan bulanan/tahunan dana BOS</p></div>
    <button onclick="window.print()" class="btn btn-primary"><i data-lucide="printer" style="width:14px;height:14px;"></i>Cetak Laporan</button>
</div>

<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value" style="color:#16a34a;">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="arrow-down-left"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value" style="color:var(--z-danger);">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="arrow-up-right"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Saldo BOS</div><div class="z-stat-value" style="color:var(--z-primary);">Rp <?php echo number_format($saldo,0,',','.'); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="wallet"></i></div></div>
</div>

<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="printer"></i>Rekap Bulanan (12 Bulan Terakhir)</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo Bulan Ini</th></tr></thead>
        <tbody>
            <?php foreach($bulanan as $b):
                $selisih = $b['masuk'] - $b['keluar'];
            ?>
            <tr>
                <td style="font-weight:700;"><?php echo date('F Y', strtotime($b['bln'].'-01')); ?></td>
                <td class="z-amount">Rp <?php echo number_format($b['masuk'],0,',','.'); ?></td>
                <td class="z-amount-red">Rp <?php echo number_format($b['keluar'],0,',','.'); ?></td>
                <td style="font-weight:700;color:<?php echo $selisih >= 0 ? '#16a34a' : 'var(--z-danger)'; ?>">
                    Rp <?php echo number_format($selisih,0,',','.'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($bulanan)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada data.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div></main></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>

