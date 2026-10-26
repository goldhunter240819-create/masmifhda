<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Biaya Sekolah'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Biaya Sekolah</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Komponen Biaya Sekolah</h1><p>Kelola jenis dan nominal biaya resmi sekolah</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Biaya</button>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="receipt"></i>Daftar Komponen Biaya</div></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Jenis Biaya</th><th>Nominal</th><th>Periode</th><th>Berlaku Untuk</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($biaya_list as $b): ?>
            <tr>
                <td style="font-weight:700;"><?php echo $b['nama']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($b['nominal'],0,',','.'); ?></td>
                <td><span class="pill pill-blue"><?php echo $b['periode']; ?></span></td>
                <td style="font-size:.82rem;"><?php echo $b['berlaku_untuk']; ?></td>
                <td><span class="pill <?php echo $b['status']==='Aktif'?'pill-green':'pill-gray'; ?>"><?php echo $b['status']; ?></span></td>
                <td>
                    <div style="display:flex;gap:5px;">
                        <button class="btn btn-outline btn-sm" onclick="alert('Edit belum tersedia!')"><i data-lucide="pencil" style="width:12px;height:12px;"></i></button>
                        <a href="<?php echo base_url('admin/finance/biaya/delete/' . $b['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus komponen biaya ini?');"><i data-lucide="trash-2" style="width:12px;height:12px;"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div></main></div>

<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal">
    <div class="z-modal-title"><i data-lucide="receipt"></i>Tambah Komponen Biaya</div>
    <form action="<?php echo base_url('admin/finance/biaya/save'); ?>" method="POST">
        <div class="z-form-group"><label class="z-label">Jenis Biaya</label><input class="z-field" type="text" name="nama_biaya" placeholder="Nama komponen biaya" required></div>
        <div class="z-grid2"><div class="z-form-group"><label class="z-label">Nominal (Rp)</label><input class="z-field" type="number" name="nominal" placeholder="0" required></div><div class="z-form-group"><label class="z-label">Periode</label><select class="z-field" name="periode"><option value="Bulanan">Bulanan</option><option value="Semester">Semester</option><option value="Tahunan">Tahunan</option><option value="Sekali Bayar">Sekali</option></select></div></div>
        <div class="z-form-group"><label class="z-label">Berlaku Untuk</label><input class="z-field" type="text" name="berlaku_untuk" placeholder="Semua siswa / Kelas X..."></div>
        <div class="z-modal-foot"><button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div>
<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
