<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Laporan Keuangan Kas'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Laporan Kas</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row" style="align-items:flex-start;">
    <div>
        <h1>Laporan Rekapitulasi Kas</h1>
        <p>Ringkasan keuangan kas dari seluruh transaksi siswa</p>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
        <form method="GET" action="" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
            <?php if (empty($is_restricted)): ?>
            <select name="petugas_id" class="form-control" style="padding:8px; border:1px solid var(--z-border); border-radius:6px; font-size:14px; min-width:150px;">
                <option value="">Semua Petugas</option>
                <?php if(isset($petugasList)): foreach($petugasList as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo (isset($_GET['petugas_id']) && $_GET['petugas_id']==$p['id'])?'selected':''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endforeach; endif; ?>
            </select>
            <?php endif; ?>
            <select name="kategori" class="form-control" style="padding:8px; border:1px solid var(--z-border); border-radius:6px; font-size:14px; min-width:150px;">
                <option value="">Semua Kategori</option>
                <?php if(isset($kategoriList)): foreach($kategoriList as $k): ?>
                <option value="<?php echo htmlspecialchars($k['kategori']); ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori']==$k['kategori'])?'selected':''; ?>><?php echo htmlspecialchars($k['kategori']); ?></option>
                <?php endforeach; endif; ?>
            </select>
            <input type="date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>" style="padding:8px; border:1px solid var(--z-border); border-radius:6px; font-size:14px;">
            <span style="color:var(--z-muted); font-size:14px;">s.d</span>
            <input type="date" name="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>" style="padding:8px; border:1px solid var(--z-border); border-radius:6px; font-size:14px;">
            <button type="submit" class="btn btn-primary" style="padding:8px 15px;"><i data-lucide="filter" style="width:14px;height:14px;"></i> Filter</button>
            <?php if(!empty($_GET['start_date']) || !empty($_GET['kategori']) || !empty($_GET['petugas_id'])): ?>
                <a href="?" class="btn btn-secondary" style="padding:8px 15px; background:#f1f5f9; color:#475569; border:none; border-radius:6px; text-decoration:none;">Reset</a>
            <?php endif; ?>
        </form>
        <a href="<?php echo base_url('admin/finance/komite/laporan/cetak?start_date=' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '&end_date=' . (isset($_GET['end_date']) ? $_GET['end_date'] : '') . '&kategori=' . (isset($_GET['kategori']) ? urlencode($_GET['kategori']) : '') . '&petugas_id=' . (isset($_GET['petugas_id']) ? $_GET['petugas_id'] : '')); ?>" target="_blank" class="btn btn-primary" style="background:#16a34a; border-color:#16a34a; text-decoration:none;"><i data-lucide="printer" style="width:14px;height:14px;"></i> Cetak Detail Laporan</a>
    </div>
</div>

<?php
$lunas_jml = 0; $lunas_total = 0;
$menyicil_jml = 0; $menyicil_total = 0;
$belum_jml = 0; $belum_total = 0;
foreach($tagihanStats as $ts) {
    if($ts['status'] === 'Lunas')       { $lunas_jml = $ts['jml']; $lunas_total = $ts['total']; }
    elseif($ts['status'] === 'Menyicil')   { $menyicil_jml = $ts['jml']; $menyicil_total = $ts['total']; }
    elseif($ts['status'] === 'Belum Bayar') { $belum_jml = $ts['jml']; $belum_total = $ts['total']; }
}
$totalTagihan = $lunas_jml + $menyicil_jml + $belum_jml;
?>

<!-- STATS UTAMA -->
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pemasukan</div><div class="z-stat-value" style="color:#16a34a;">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="trending-up"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Pengeluaran</div><div class="z-stat-value" style="color:var(--z-danger);">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="trending-down"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Saldo Kas</div><div class="z-stat-value" style="color:#2563eb;">Rp <?php echo number_format($saldo,0,',','.'); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="wallet"></i></div></div>
</div>

<!-- STATUS TAGIHAN -->
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Tagihan Lunas</div><div class="z-stat-value"><?php echo $lunas_jml; ?> <small style="font-size:.7rem;font-weight:400;">tagihan</small></div><div style="font-size:.8rem;color:var(--z-muted);">Rp <?php echo number_format($lunas_total,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="check-circle"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Tagihan Menyicil</div><div class="z-stat-value" style="color:#2563eb;"><?php echo $menyicil_jml; ?> <small style="font-size:.7rem;font-weight:400;">tagihan</small></div><div style="font-size:.8rem;color:var(--z-muted);">Rp <?php echo number_format($menyicil_total,0,',','.'); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="loader"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Belum Bayar</div><div class="z-stat-value" style="color:var(--z-danger);"><?php echo $belum_jml; ?> <small style="font-size:.7rem;font-weight:400;">tagihan</small></div><div style="font-size:.8rem;color:var(--z-muted);">Rp <?php echo number_format($belum_total,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="alert-circle"></i></div></div>
</div>

<!-- RINGKASAN KATEGORI -->
<div class="z-panel" style="margin-bottom: 20px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="pie-chart"></i>Ringkasan Pemasukan per Kategori</div></div>
    <div style="padding: 20px;">
        <?php if(empty($kategoriSummary)): ?>
            <p style="color:var(--z-muted); text-align:center;">Tidak ada data pemasukan pada periode ini.</p>
        <?php else: ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:15px;">
            <?php foreach($kategoriSummary as $kat => $tot): ?>
            <div style="background:#f8fafc; border:1px solid var(--z-border); border-radius:8px; padding:15px; display:flex; flex-direction:column; gap:5px;">
                <div style="font-size:0.85rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;"><?php echo htmlspecialchars($kat); ?></div>
                <div style="font-size:1.1rem; font-weight:700; color:#16a34a;">Rp <?php echo number_format($tot,0,',','.'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- TREND BULANAN -->
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="bar-chart-2"></i>Tren Pemasukan Bulanan (6 Bulan Terakhir)</div></div>
    <div style="padding: 20px;">
        <?php if(empty($bulanan)): ?>
            <p style="color:var(--z-muted); text-align:center;">Belum ada data pemasukan bulanan.</p>
        <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead><tr style="background:#f8fafc;"><th style="padding:8px 12px; text-align:left; font-size:.82rem; color:var(--z-muted);">Bulan</th><th style="padding:8px 12px; text-align:right; font-size:.82rem; color:var(--z-muted);">Pemasukan</th><th style="padding:8px 12px;">Grafik</th></tr></thead>
            <tbody>
            <?php 
            $maxBulanan = max(array_column($bulanan, 'total'));
            foreach($bulanan as $b): 
                $pct = $maxBulanan > 0 ? ($b['total'] / $maxBulanan * 100) : 0;
            ?>
            <tr style="border-top:1px solid var(--z-border);">
                <td style="padding:10px 12px; font-size:.85rem; font-weight:600;"><?php echo date('F Y', strtotime($b['bln'].'-01')); ?></td>
                <td style="padding:10px 12px; text-align:right; font-size:.85rem; color:#16a34a; font-weight:700;">Rp <?php echo number_format($b['total'],0,',','.'); ?></td>
                <td style="padding:10px 12px; min-width:200px;">
                    <div style="background:#e2e8f0; border-radius:4px; height:8px;">
                        <div style="background:#16a34a; height:8px; border-radius:4px; width:<?php echo $pct; ?>%;"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

</div></main></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();

document.addEventListener('DOMContentLoaded', function() {
    const petugasSelect = document.querySelector('select[name="petugas_id"]');
    const kategoriSelect = document.querySelector('select[name="kategori"]');
    
    if (petugasSelect && kategoriSelect) {
        const kategoriMapping = <?php echo json_encode($kategoriMapping ?? []); ?>;
        const originalOptions = Array.from(kategoriSelect.options);
        
        function filterCategories() {
            const selectedPetugasId = petugasSelect.value;
            const currentSelectedValue = "<?php echo isset($_GET['kategori']) ? htmlspecialchars($_GET['kategori']) : ''; ?>";
            
            kategoriSelect.innerHTML = '';
            kategoriSelect.appendChild(originalOptions[0]); // "Semua Kategori"
            
            originalOptions.slice(1).forEach(option => {
                const catName = option.value;
                const mapping = kategoriMapping.find(m => m.kategori === catName && m.user_id.toString() === selectedPetugasId);
                
                if (!selectedPetugasId || mapping) {
                    kategoriSelect.appendChild(option);
                }
            });
            
            if (Array.from(kategoriSelect.options).some(opt => opt.value === currentSelectedValue)) {
                kategoriSelect.value = currentSelectedValue;
            } else {
                kategoriSelect.value = '';
            }
        }
        
        petugasSelect.addEventListener('change', filterCategories);
        filterCategories();
    }
});
</script>
</body></html>
