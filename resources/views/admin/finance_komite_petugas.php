<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Master Petugas'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Master Petugas</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Master Petugas Keuangan</h1><p>Daftar guru yang memiliki wewenang untuk mencatat tagihan dan pembayaran</p></div>
    <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Petugas</button>
    </div>
</div>
<div class="z-panel" style="max-width: 800px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="users"></i>Daftar Petugas Aktif</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th style="width:50px;text-align:center;">No</th><th>Nama Guru</th><th>NIP</th><th style="width:150px;text-align:center;">Aksi</th></tr></thead>
        <tbody>
            <?php $no=1; foreach($petugasList as $p): ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td style="font-weight:600;"><?php echo htmlspecialchars($p['nama']); ?></td>
                <td style="color:var(--z-muted);"><?php echo htmlspecialchars($p['nip']) ?: '-'; ?></td>
                <td style="text-align:center;">
                    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <div style="display:flex; gap:5px; justify-content:center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $p['id']; ?>', '<?php echo htmlspecialchars($p['nama'], ENT_QUOTES); ?>')">Hapus Akses</button>
                        </div>
                    <?php else: ?>
                        <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($petugasList)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada master petugas yang ditambahkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div>
</main>
</div>

<!-- Modal Form -->
<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="z-modal">
        <div class="z-modal-head"><div class="z-modal-title" id="mTitle">Tambah Petugas</div><button type="button" class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></button></div>
        <form method="POST" action="<?php echo base_url('admin/finance/komite/petugas/save'); ?>">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label class="z-label">Pilih Guru</label>
                    <select name="guru_id" id="f_guru" class="z-field" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?> <?php if(!empty($g['nip'])) echo '('.htmlspecialchars($g['nip']).')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="margin-top:15px; font-size:0.8rem; color:var(--z-muted); background:#f8fafc; padding:10px; border-radius:8px; border:1px solid var(--z-border);">
                    <i data-lucide="info" style="width:14px;height:14px; vertical-align:middle; margin-bottom:2px; color:#3b82f6;"></i>
                    Guru yang ditambahkan akan mendapatkan akses penuh ke menu Pemasukan, Pengeluaran, Tagihan, dan Laporan.
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Petugas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete -->
<div class="z-modal-ov" id="dOv" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="z-modal" style="max-width:400px; text-align:center;">
        <div class="z-modal-body" style="padding: 30px 20px;">
            <div style="width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <i data-lucide="user-minus" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 style="margin-bottom: 10px; font-size: 1.2rem; color: #1e293b;">Hapus Akses Petugas?</h3>
            <p>Mencabut hak akses bendahara dari <strong id="dName"></strong></p>
            <p style="color:var(--z-danger);font-size:12px;margin-top:5px;">Petugas ini tidak akan bisa lagi mencatat pembayaran tagihan atau pengeluaran kas.</p>
        </div>
        <div class="z-modal-foot" style="justify-content:center; background:#f8fafc;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('dOv').classList.remove('open')">Batal</button>
            <a href="#" id="dLink" class="btn btn-danger">Ya, Hapus Akses</a>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
function zToggleSidebar() { document.getElementById('zSidebar').classList.toggle('open'); document.getElementById('zOverlay').classList.toggle('open'); }
function resetModal() {
    document.getElementById('f_guru').value = '';
}
function openDeleteModal(id, nama) {
    document.getElementById('dName').innerText = nama;
    document.getElementById('dLink').href = '<?php echo base_url('admin/finance/komite/petugas/delete/'); ?>' + id;
    document.getElementById('dOv').classList.add('open');
}
<?php if(isset($_GET['msg']) && $_GET['msg']=='saved'): ?>
alert('Berhasil menambah petugas!');
window.history.replaceState(null, null, window.location.pathname);
<?php endif; ?>
<?php if(isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
alert('Akses petugas telah dicabut!');
window.history.replaceState(null, null, window.location.pathname);
<?php endif; ?>
</script>
</body></html>
