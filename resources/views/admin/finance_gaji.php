<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Data Gaji'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Data Gaji</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Data Gaji Karyawan</h1><p>Kelola penggajian guru dan staf sekolah</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Data</button>
</div>
<?php
$total=count($gaji); $sudah=0; $belum=0; $totalGaji=0;
foreach($gaji as $g){ if($g['status']==='Sudah Dibayar') $sudah++; else $belum++; $totalGaji+=$g['gaji_pokok']; }
?>
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Karyawan</div><div class="z-stat-value"><?php echo $total; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Sudah Dibayar</div><div class="z-stat-value" style="color:#16a34a;"><?php echo $sudah; ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="check-circle"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Penggajian</div><div class="z-stat-value" style="font-size:1.05rem;">Rp <?php echo number_format($totalGaji,0,',','.'); ?></div></div><div class="z-stat-icon zi-purple"><i data-lucide="wallet"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="users"></i>Daftar Gaji</div></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Karyawan</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Total</th><th>Bulan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($gaji as $g): ?>
            <tr>
                <td><div class="z-uc"><div class="z-uc-av"><?php echo strtoupper(substr($g['nama'],0,2)); ?></div><div class="z-uc-name"><?php echo $g['nama']; ?></div></div></td>
                <td style="font-size:.82rem;"><?php echo $g['jabatan']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($g['gaji_pokok'],0,',','.'); ?></td>
                <td style="font-size:.82rem;">Rp <?php echo number_format($g['tunjangan'],0,',','.'); ?></td>
                <td class="z-amount">Rp <?php echo number_format($g['gaji_pokok']+$g['tunjangan'],0,',','.'); ?></td>
                <td style="font-size:.81rem;color:var(--z-muted);"><?php echo $g['bulan']; ?></td>
                <td><span class="pill <?php echo $g['status']==='Sudah Dibayar'?'pill-green':'pill-amber'; ?>"><?php echo $g['status']; ?></span></td>
                <td>
                    <?php if($g['status']!=='Sudah Dibayar'): ?>
                        <form action="<?php echo base_url('admin/finance/gaji/bayar'); ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="nip" value="<?php echo $g['nip']; ?>">
                            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Konfirmasi pembayaran gaji untuk karyawan ini?');">Bayar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div></main></div>

<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal">
    <div class="z-modal-title"><i data-lucide="users"></i>Tambah Data Gaji</div>
    <form onsubmit="event.preventDefault();alert('Simpan segera hadir!');">
        <div class="z-form-group"><label class="z-label">Nama Karyawan</label><input class="z-field" type="text" placeholder="Nama lengkap" required></div>
        <div class="z-grid2"><div class="z-form-group"><label class="z-label">Jabatan</label><input class="z-field" type="text" placeholder="Jabatan"></div><div class="z-form-group"><label class="z-label">Gaji Pokok</label><input class="z-field" type="number" placeholder="0" required></div></div>
        <div class="z-grid2"><div class="z-form-group"><label class="z-label">Tunjangan</label><input class="z-field" type="number" placeholder="0"></div><div class="z-form-group"><label class="z-label">Bulan</label><input class="z-field" type="month" value="<?php echo date('Y-m'); ?>"></div></div>
        <div class="z-modal-foot"><button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
