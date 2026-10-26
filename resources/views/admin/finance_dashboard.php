<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Dashboard Keuangan'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Dashboard Keuangan</span>
        <span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:1.5rem;">
    <div>
        <h1>Dashboard Keuangan</h1>
        <p>Ringkasan keuangan sekolah â€” <?php echo date('d F Y'); ?></p>
    </div>
    <div>
        <form method="GET" action="" style="display:flex; gap:10px; align-items:center;">
            <select name="kategori" class="form-control" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid var(--z-border); border-radius:6px; font-size:14px; min-width:180px; background:#fff; cursor:pointer;">
                <option value="">Semua Kategori</option>
                <?php if(isset($kategoriList)): foreach($kategoriList as $k): ?>
                <option value="<?php echo htmlspecialchars($k['kategori']); ?>" <?php echo (isset($selected_kategori) && $selected_kategori==$k['kategori'])?'selected':''; ?>><?php echo htmlspecialchars($k['kategori']); ?></option>
                <?php endforeach; endif; ?>
            </select>
            <?php if(!empty($selected_kategori)): ?>
                <a href="<?php echo base_url('admin/finance'); ?>" class="btn btn-secondary" style="padding:8px 12px; font-size:14px; background:#f1f5f9; color:#475569; border:none; border-radius:6px; text-decoration:none;">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="z-stats">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value">Rp <?php echo number_format($totalPemasukan,0,',','.'); ?></div><div class="z-stat-trend z-trend-up"><i data-lucide="trending-up" style="width:12px;height:12px;"></i> Tahun ini</div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value">Rp <?php echo number_format($totalPengeluaran,0,',','.'); ?></div><div class="z-stat-trend z-trend-dn">Operasional</div></div><div class="z-stat-icon zi-red"><i data-lucide="trending-down"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Saldo Bersih</div><div class="z-stat-value">Rp <?php echo number_format($saldo,0,',','.'); ?></div><div class="z-stat-trend z-trend-up">Surplus</div></div><div class="z-stat-icon zi-teal"><i data-lucide="wallet"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Tagihan Belum Bayar</div><div class="z-stat-value" style="color:var(--z-warning);"><?php echo $tagihanBelumBayar; ?></div><div class="z-stat-trend" style="color:var(--z-warning);">Siswa menunggak</div></div><div class="z-stat-icon zi-amber"><i data-lucide="alert-circle"></i></div></div>
</div>

<div style="display:grid;grid-template-columns:3fr 2fr;gap:1.25rem;flex-wrap:wrap;">
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="clock"></i>Transaksi Terbaru</div><a href="<?php echo base_url('admin/finance/laporan'); ?>" class="btn btn-outline btn-sm">Laporan</a></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Jenis</th><th>Jumlah</th></tr></thead>
        <tbody>
            <?php foreach(array_slice($transaksi,0,6) as $t): ?>
            <tr>
                <td style="font-size:.8rem;color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($t['tanggal'])); ?></td>
                <td style="font-weight:600;"><?php echo $t['keterangan']; ?></td>
                <td><span class="pill <?php echo $t['jenis']==='Pemasukan'?'pill-green':'pill-red'; ?>"><?php echo $t['jenis']; ?></span></td>
                <td class="<?php echo $t['jenis']==='Pemasukan'?'z-amount':'z-amount-red'; ?>">Rp <?php echo number_format($t['jumlah'],0,',','.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="zap"></i>Aksi Cepat</div></div>
    <div class="z-panel-body" style="display:flex;flex-direction:column;gap:8px;">
        <a href="<?php echo base_url('admin/finance/tagihan'); ?>" class="btn btn-primary"><i data-lucide="file-text" style="width:14px;height:14px;"></i>Kelola Tagihan</a>
        <a href="<?php echo base_url('admin/finance/gaji'); ?>" class="btn btn-outline"><i data-lucide="users" style="width:14px;height:14px;"></i>Data Gaji</a>
        <a href="<?php echo base_url('admin/finance/biaya'); ?>" class="btn btn-outline"><i data-lucide="receipt" style="width:14px;height:14px;"></i>Biaya Sekolah</a>
        <a href="<?php echo base_url('admin/finance/laporan'); ?>" class="btn btn-ghost"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i>Laporan Keuangan</a>
    </div>
</div>
</div>
</div>
</main></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
