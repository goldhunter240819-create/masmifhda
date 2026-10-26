<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Dashboard Dana BOS'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Dana BOS</span><span class="z-badge">Admin BOS</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Admin BOS</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div>
        <h1>Manajemen Dana BOS</h1>
        <p>Bantuan Operasional Sekolah â€” pencatatan pemasukan dan pengeluaran dana pemerintah</p>
    </div>
</div>

<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Pemasukan</div>
            <div class="z-stat-value" style="color:#16a34a;">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></div>
        </div>
        <div class="z-stat-icon zi-green"><i data-lucide="arrow-down-left"></i></div>
    </div>
    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Pengeluaran</div>
            <div class="z-stat-value" style="color:var(--z-danger);">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></div>
        </div>
        <div class="z-stat-icon zi-red"><i data-lucide="arrow-up-right"></i></div>
    </div>
    <div class="z-stat">
        <div class="z-stat-left">
            <div class="z-stat-label">Saldo BOS</div>
            <div class="z-stat-value" style="color:var(--z-primary);">Rp <?php echo number_format($saldo,0,',','.'); ?></div>
        </div>
        <div class="z-stat-icon zi-blue"><i data-lucide="wallet"></i></div>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="history"></i>Transaksi Terbaru</div>
        <div style="display:flex;gap:8px;">
            <a href="<?php echo base_url('admin/bos/pemasukan'); ?>" class="btn btn-sm btn-ghost"><i data-lucide="arrow-down-left" style="width:12px;height:12px;"></i> Pemasukan</a>
            <a href="<?php echo base_url('admin/bos/pengeluaran'); ?>" class="btn btn-sm btn-ghost"><i data-lucide="arrow-up-right" style="width:12px;height:12px;"></i> Pengeluaran</a>
        </div>
    </div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Jenis</th><th>Kategori</th><th>Jumlah</th></tr></thead>
        <tbody>
            <?php foreach($recentTx as $tx): ?>
            <tr>
                <td style="font-size:.82rem;color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($tx['tanggal_transaksi'])); ?></td>
                <td style="font-weight:600;"><?php echo htmlspecialchars($tx['keterangan']); ?></td>
                <td>
                    <?php if($tx['jenis']==='Pemasukan'): ?>
                        <span class="pill pill-green"><i data-lucide="arrow-down-left" style="width:10px;height:10px;"></i> Masuk</span>
                    <?php else: ?>
                        <span class="pill pill-red"><i data-lucide="arrow-up-right" style="width:10px;height:10px;"></i> Keluar</span>
                    <?php endif; ?>
                </td>
                <td><span class="pill pill-blue"><?php echo htmlspecialchars($tx['kategori']); ?></span></td>
                <td class="<?php echo $tx['jenis']==='Pemasukan' ? 'z-amount' : 'z-amount-red'; ?>">
                    <?php echo $tx['jenis']==='Pemasukan' ? '+' : '-'; ?> Rp <?php echo number_format($tx['jumlah'],0,',','.'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recentTx)): ?>
            <tr><td colspan="5" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada transaksi.</td></tr>
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

