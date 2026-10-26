<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Pengeluaran Dana'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Pengeluaran</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Pengeluaran</h1><p>Pencatatan dana keluar dari kas sekolah</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pengeluaran</button>
</div>
<?php
$total_keluar = 0;
foreach($pengeluaran as $p){ $total_keluar += $p['jumlah']; }
?>
<div class="z-stats" style="grid-template-columns:repeat(2,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Transaksi</div><div class="z-stat-value"><?php echo count($pengeluaran); ?></div></div><div class="z-stat-icon zi-amber"><i data-lucide="arrow-up-right"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Dana Keluar</div><div class="z-stat-value" style="color:var(--z-danger);">Rp <?php echo number_format($total_keluar,0,',','.'); ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="credit-card"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="arrow-up-right"></i>Daftar Pengeluaran</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Bukti</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($pengeluaran as $p): ?>
            <tr>
                <td style="font-size:.82rem; color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($p['tanggal'])); ?></td>
                <td style="font-weight:600;"><?php echo $p['keterangan']; ?></td>
                <td><span class="pill pill-purple"><?php echo $p['kategori']; ?></span></td>
                <td><a href="#" style="color:var(--z-info);text-decoration:underline;font-size:0.8rem;"><i data-lucide="paperclip" style="width:12px;height:12px;"></i> <?php echo $p['bukti']; ?></a></td>
                <td class="z-amount-red">Rp <?php echo number_format($p['jumlah'],0,',','.'); ?></td>
                <td>
                    <a href="<?php echo base_url('admin/finance/komite/transaksi/delete/' . $p['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengeluaran ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pengeluaran)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada data pengeluaran.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div></main></div>

<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal">
    <div class="z-modal-head">
        <div class="z-modal-title"><i data-lucide="plus-circle"></i>Tambah Pengeluaran</div>
        <button type="button" class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></button>
    </div>
    <form action="<?php echo base_url('admin/finance/komite/transaksi/save'); ?>" method="POST">
        <div class="z-modal-body">
        <input type="hidden" name="jenis" value="Pengeluaran">
        <div class="z-form-group"><label class="z-label">Keterangan Pengeluaran</label><input class="z-field" type="text" name="keterangan" placeholder="Contoh: Subsidi Pembangunan Masjid" required></div>
        <div class="z-grid2"><div class="z-form-group"><label class="z-label">Tanggal Keluar</label><input class="z-field" type="date" name="tanggal_transaksi" required value="<?php echo date('Y-m-d'); ?>"></div><div class="z-form-group"><label class="z-label">Jumlah (Rp)</label><input class="z-field" type="number" name="jumlah" placeholder="0" required></div></div>
        <div class="z-grid2">
            <div class="z-form-group">
                <label class="z-label">Kategori Keuangan (Uang Keluar Dari)</label>
                <select class="z-field" name="kategori" required>
                    <?php foreach($kategoriList as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>"><?php echo htmlspecialchars($kat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="z-form-group">
                <label class="z-label">Jenis Alokasi Pengeluaran</label>
                <select class="z-field" name="sumber_dana">
                    <option value="Beasiswa">Beasiswa</option>
                    <option value="Sarpras">Sarpras</option>
                    <option value="Kegiatan Siswa">Kegiatan Siswa</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
        </div>
        <div class="z-form-group"><label class="z-label">Upload Bukti</label><input class="z-field" type="file" accept=".pdf,image/*"></div>
        </div>
        <div class="z-modal-foot"><button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button><button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:13px;height:13px;"></i>Simpan</button></div>
    </form>
</div></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
