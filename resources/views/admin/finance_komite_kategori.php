<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Master Kategori Tagihan'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Master Kategori</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Master Kategori Tagihan</h1><p>Kelola daftar kategori laporan keuangan (misal: Seragam, Komite, Buku)</p></div>
    <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Kategori</button>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div style="background: <?php echo $_GET['msg']=='deleted' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $_GET['msg']=='deleted' ? '#b91c1c' : '#15803d'; ?>; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid <?php echo $_GET['msg']=='deleted' ? '#fca5a5' : '#bbf7d0'; ?>;">
        <?php if($_GET['msg'] == 'saved'): ?>Berhasil menyimpan data kategori!
        <?php elseif($_GET['msg'] == 'deleted'): ?>Kategori berhasil dihapus!
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="z-panel" style="max-width: 800px;">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="list"></i>Daftar Kategori</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th style="width:50px;text-align:center;">No</th><th>Nama Kategori</th><th>Petugas (Penanggung Jawab)</th><th style="width:150px;text-align:center;">Aksi</th></tr></thead>
        <tbody>
            <?php $no=1; foreach($kategoriList as $k): ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td style="font-weight:600;"><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                <td style="color:#64748b;"><?php echo $k['petugas_nama'] ? htmlspecialchars($k['petugas_nama']) : '<span style="font-style:italic;font-size:0.8rem;">Belum diset</span>'; ?></td>
                <td style="text-align:center;">
                    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <div style="display:flex; gap:5px; justify-content:center;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="openEditModal('<?php echo $k['id']; ?>', '<?php echo htmlspecialchars($k['nama_kategori'], ENT_QUOTES); ?>', '<?php echo $k['guru_id']; ?>')">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $k['id']; ?>', '<?php echo htmlspecialchars($k['nama_kategori'], ENT_QUOTES); ?>')">Hapus</button>
                        </div>
                    <?php else: ?>
                        <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($kategoriList)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada master kategori.</td></tr>
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
        <div class="z-modal-head"><div class="z-modal-title" id="mTitle">Tambah Kategori</div><button type="button" class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></button></div>
        <form method="POST" action="<?php echo base_url('admin/finance/komite/kategori/save'); ?>">
            <div class="z-modal-body">
                <input type="hidden" name="id" id="f_id" value="0">
                <div class="z-form-group">
                    <label class="z-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="f_nama" class="z-field" required placeholder="Contoh: Seragam, Buku, SPP, Komite">
                </div>
                <div class="z-form-group" style="margin-top:15px;">
                    <label class="z-label">Pilih Petugas (Penanggung Jawab)</label>
                    <select name="guru_id" id="f_guru" class="z-field">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?> <?php if(!empty($g['nip'])) echo '('.htmlspecialchars($g['nip']).')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete -->
<div class="z-modal-ov" id="dOv" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="z-modal" style="max-width:400px; text-align:center;">
        <div class="z-modal-body" style="padding: 30px 20px;">
            <div style="width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <i data-lucide="trash-2" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 style="margin-bottom: 10px; font-size: 1.2rem; color: #1e293b;">Hapus Kategori?</h3>
            <p>Menghapus <strong id="dName"></strong></p>
            <p style="color:var(--z-danger);font-size:12px;margin-top:5px;">Perhatian: Tagihan atau transaksi yang menggunakan kategori ini namanya akan tetap ada, tapi referensi master kategorinya hilang.</p>
        </div>
        <div class="z-modal-foot" style="justify-content:center; background:#f8fafc;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('dOv').classList.remove('open')">Batal</button>
            <a href="#" id="dLink" class="btn btn-danger">Ya, Hapus</a>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
function zToggleSidebar() { document.getElementById('zSidebar').classList.toggle('open'); document.getElementById('zOverlay').classList.toggle('open'); }
function resetModal() {
    document.getElementById('mTitle').innerText = 'Tambah Kategori';
    document.getElementById('f_id').value = '0';
    document.getElementById('f_nama').value = '';
    document.getElementById('f_guru').value = '';
}
function openEditModal(id, nama, guru_id) {
    document.getElementById('mTitle').innerText = 'Edit Kategori';
    document.getElementById('f_id').value = id;
    document.getElementById('f_nama').value = nama;
    document.getElementById('f_guru').value = guru_id || '';
    document.getElementById('mOv').classList.add('open');
}
function openDeleteModal(id, nama) {
    document.getElementById('dName').innerText = nama;
    document.getElementById('dLink').href = '<?php echo base_url('admin/finance/komite/kategori/delete/'); ?>' + id;
    document.getElementById('dOv').classList.add('open');
}
<?php if(isset($_GET['msg'])): ?>
window.history.replaceState(null, null, window.location.pathname);
<?php endif; ?>
</script>
</body></html>
