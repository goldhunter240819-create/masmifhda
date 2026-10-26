<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Akses Bendahara'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Akses Bendahara</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">

<div class="z-page-head-row">
    <div>
        <h1>Akses & Bendahara</h1>
        <p>Kelola hak akses menu aplikasi untuk masing-masing petugas bendahara.</p>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div style="background: <?php echo $_GET['msg']=='deleted' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $_GET['msg']=='deleted' ? '#b91c1c' : '#15803d'; ?>; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid <?php echo $_GET['msg']=='deleted' ? '#fca5a5' : '#bbf7d0'; ?>;">
        <?php if($_GET['msg'] == 'saved'): ?>Pengaturan akses berhasil disimpan!
        <?php elseif($_GET['msg'] == 'deleted'): ?>Pengaturan akses berhasil dihapus!
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="shield-check"></i> Daftar Hak Akses</div></div>
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Petugas / Bendahara</th>
                    <th>Kategori Tanggung Jawab</th>
                    <th>Akses Menu</th>
                    <th style="text-align: center;">Hapus Data</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($aksesList) > 0): ?>
                    <?php foreach ($aksesList as $a): ?>
                        <tr>
                            <td>
                                <strong style="color: #0f172a;"><?= htmlspecialchars($a['nama']) ?></strong><br>
                                <span style="font-size: 0.75rem; color: #64748b;">NIP: <?= htmlspecialchars($a['nip']) ?></span>
                            </td>
                            <td>
                                <?php if(empty($a['kategori_tanggung_jawab'])): ?>
                                    <span class="pill pill-gray">Tidak Ada Kategori</span>
                                <?php else: ?>
                                    <?php 
                                    $cats = explode(', ', $a['kategori_tanggung_jawab']);
                                    foreach($cats as $cat): 
                                    ?>
                                        <span class="pill pill-green" style="margin:2px; display:inline-block;"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(empty($a['akses_menu_arr'])): ?>
                                    <span class="pill pill-gray">Tidak Ada Akses Menu</span>
                                <?php else: ?>
                                    <?php foreach($a['akses_menu_arr'] as $m): ?>
                                        <span class="pill pill-blue" style="margin:2px; display:inline-block;"><?= htmlspecialchars($availableMenus[$m] ?? $m) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <label class="switch">
                                    <input type="checkbox" onchange="toggleDeleteAkses(<?= $a['user_id'] ?>, this.checked)" <?= ($a['can_delete'] == 1) ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <div style="display:flex;gap:5px;">
                                    <button class="btn btn-outline btn-sm btn-edit-akses" 
                                            data-id="<?= $a['id'] ?>" 
                                            data-guru-id="<?= $a['guru_id'] ?>" 
                                            data-nama="<?= htmlspecialchars($a['nama'], ENT_QUOTES) ?>" 
                                            data-menu="<?= htmlspecialchars($a['akses_menu'], ENT_QUOTES) ?>" 
                                            title="Edit Akses Menu">
                                        <i data-lucide="edit" style="width:12px;height:12px;"></i> Edit Menu
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">Belum ada petugas yang memiliki hak akses. Hubungkan Guru ke Kategori di Master Kategori terlebih dahulu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</main>
</div>

<style>
/* Switch Toggle CSS */
.switch {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 20px;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}
input:checked + .slider {
  background-color: #10b981;
}
input:focus + .slider {
  box-shadow: 0 0 1px #10b981;
}
input:checked + .slider:before {
  transform: translateX(16px);
}
.slider.round {
  border-radius: 20px;
}
.slider.round:before {
  border-radius: 50%;
}
</style>

<!-- Modal Akses (Zamrud Style) -->
<div class="z-modal-ov" id="aksesModal" onclick="if(event.target===this) hideModal('aksesModal')">
    <div class="z-modal" style="max-width: 500px;">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="shield"></i> Pengaturan Akses Menu Petugas</div>
            <button class="z-modal-close" onclick="hideModal('aksesModal')"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('admin/finance/akses/save'); ?>" method="POST" style="display:flex;flex-direction:column;flex:1;min-height:0;">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label class="z-label">Petugas / Bendahara</label>
                    <input type="hidden" name="guru_id" id="formGuruId">
                    <input type="text" id="formGuruNama" class="z-field" readonly style="background:#f1f5f9; cursor:not-allowed; font-weight:600; color:#0f172a;">
                </div>
                
                <div class="z-form-group" style="margin-top:20px;">
                    <label class="z-label" style="color:#3b82f6;">Akses Menu Aplikasi</label>
                    <p style="font-size:0.75rem; color:#64748b; margin-bottom:10px;">Pilih menu yang BISA dilihat/diakses oleh petugas ini.</p>
                    <div style="display:flex; flex-wrap:wrap; gap:10px; background:#f8fafc; padding:15px; border-radius:12px; border:1px solid #e2e8f0;">
                        <?php foreach($availableMenus as $k => $v): ?>
                            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-size:0.85rem; color:#0f172a; width:calc(50% - 10px);">
                                <input type="checkbox" name="akses_menu[]" value="<?= htmlspecialchars($k) ?>" class="chk-menu" style="width:16px; height:16px; accent-color:#3b82f6;"> 
                                <?= htmlspecialchars($v) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="hideModal('aksesModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
function showModal(id) { document.getElementById(id).classList.add('open'); }
function hideModal(id) { document.getElementById(id).classList.remove('open'); }

function toggleDeleteAkses(userId, isChecked) {
    if(!userId) {
        alert('User ID tidak ditemukan. Pastikan guru ini sudah memiliki akun pengguna.');
        location.reload();
        return;
    }
    var formData = new FormData();
    formData.append('user_id', userId);
    formData.append('val', isChecked ? 1 : 0);
    
    fetch('<?= base_url("admin/finance/akses/toggle-delete") ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(!data.success) {
            alert('Gagal merubah hak akses hapus data.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan.');
        location.reload();
    });
}

// Bind click event to Edit Menu buttons
document.querySelectorAll('.btn-edit-akses').forEach(btn => {
    btn.addEventListener('click', function() {
        let guru_id = this.getAttribute('data-guru-id');
        let nama_guru = this.getAttribute('data-nama');
        let menu_json = this.getAttribute('data-menu');
        
        document.getElementById('formGuruId').value = guru_id;
        document.getElementById('formGuruNama').value = nama_guru;
        
        // Reset all checkboxes
        document.querySelectorAll('.chk-menu').forEach(c => c.checked = false);
        
        try {
            let menus = JSON.parse(menu_json || '[]');
            document.querySelectorAll('.chk-menu').forEach(c => {
                if(menus.includes(c.value)) c.checked = true;
            });
        } catch(e) {}
        
        showModal('aksesModal');
    });
});

lucide.createIcons();
</script>
</body></html>
