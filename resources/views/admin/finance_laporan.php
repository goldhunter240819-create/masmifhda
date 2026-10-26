<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Laporan Keuangan'; include __DIR__.'/_zamrud_head.php'; ?>
<style>
.bar-row{display:flex;align-items:center;gap:12px;margin-bottom:10px;}
.bar-lbl{min-width:90px;font-size:.8rem;font-weight:700;}
.bar-track{flex:1;height:9px;background:#f1f5f9;border-radius:100px;overflow:hidden;}
.bar-fill{height:100%;border-radius:100px;background:linear-gradient(90deg,#0284c7,#34d399);}
.bar-val{min-width:80px;text-align:right;font-size:.8rem;font-weight:700;color:var(--z-primary);}
</style>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Laporan Keuangan</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Laporan Keuangan</h1><p>Rekapitulasi pemasukan dan pengeluaran sekolah</p></div>
    <button class="btn btn-outline" onclick="window.print()"><i data-lucide="printer" style="width:14px;height:14px;"></i>Cetak</button>
</div>
<div class="z-stats">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value">Rp <?php echo number_format($totalPemasukan,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value">Rp <?php echo number_format($totalPengeluaran,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="trending-down"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Saldo Bersih</div><div class="z-stat-value" style="color:var(--z-primary);">Rp <?php echo number_format($saldo,0,',','.'); ?></div></div><div class="z-stat-icon zi-teal"><i data-lucide="wallet"></i></div></div>
</div>
<?php
$max = 1;
foreach($per_bulan as $b){ if($b['pemasukan']>$max) $max=$b['pemasukan']; }
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="bar-chart-2"></i>Pemasukan per Bulan</div></div>
    <div class="z-panel-body">
        <?php foreach($per_bulan as $b): ?>
        <div class="bar-row">
            <div class="bar-lbl"><?php echo $b['bulan']; ?></div>
            <div class="bar-track"><div class="bar-fill" style="width:<?php echo round($b['pemasukan']/$max*100); ?>%;"></div></div>
            <div class="bar-val">Rp <?php echo number_format($b['pemasukan']/1000000,1); ?>jt</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="list"></i>Rekap Bulanan</div></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Saldo</th></tr></thead>
        <tbody>
            <?php foreach($per_bulan as $b): $s=$b['pemasukan']-$b['pengeluaran']; ?>
            <tr>
                <td style="font-weight:700;"><?php echo $b['bulan']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($b['pemasukan'],0,',','.'); ?></td>
                <td class="z-amount-red">Rp <?php echo number_format($b['pengeluaran'],0,',','.'); ?></td>
                <td style="font-weight:800;color:<?php echo $s>=0?'var(--z-primary)':'var(--z-danger)'; ?>">Rp <?php echo number_format($s,0,',','.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div>
</div></main></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>

