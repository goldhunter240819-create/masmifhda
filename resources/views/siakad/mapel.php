<?php
// data variables: $mapel, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">DATA MASTER</div>
                    <h1><i data-lucide="book-open"></i> Mata Pelajaran</h1>
                    <p>Kelola kurikulum dan daftar mata pelajaran aktif.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="addMapelModal()">
                        <i data-lucide="plus-circle"></i> Tambah Mapel
                    </button>
                </div>
            </div>
        </div>

        <div class="z-panel">
            <div class="z-table-wrap">
                <table class="">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th class="text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($mapel)): ?>
                            <tr><td colspan="3" class="text-center" style="padding: 3rem; color: #94a3b8;">Belum ada data mata pelajaran.</td></tr>
                        <?php else: ?>
                            <?php foreach($mapel as $m): ?>
                            <tr>
                                <td>
                                    <span class="z-badge-code"><?php echo $m['kode_mapel']; ?></span>
                                </td>
                                <td>
                                    <div class="mapel-name"><?php echo $m['nama_mapel']; ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;"><i data-lucide="tag" style="width: 12px; height: 12px; display: inline-block; vertical-align: text-bottom;"></i> <?php echo $m['kategori'] ?: 'Umum'; ?></div>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <button type="button" class="action-btn edit-btn" onclick='editMapel(<?php echo htmlspecialchars(json_encode($m), ENT_QUOTES, "UTF-8"); ?>)' title="Edit Mapel">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <a href="<?php echo base_url('siakad/mapel/delete/' . $m['id']); ?>" onclick="return confirm('Hapus mata pelajaran ini?')" class="action-btn delete-btn" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mapel -->
<div id="modal-mapel" class="z-modal">
    <div class="z-modal-content">
        <div class="z-modal-header">
            <h3 id="mapel-modal-title"><i data-lucide="book-open"></i> Tambah Mata Pelajaran</h3>
            <button onclick="document.getElementById('modal-mapel').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form id="mapel-form" action="<?php echo base_url('siakad/mapel/add'); ?>" method="POST">
                <input type="hidden" name="id" id="mapel-id">
                <div class="form-group">
                    <label>Kode Mata Pelajaran</label>
                    <input type="text" name="kode_mapel" id="mapel-kode" required class="z-input" placeholder="Contoh: MP-001">
                </div>
                <div class="form-group">
                    <label>Nama Mata Pelajaran</label>
                    <input type="text" name="nama_mapel" id="mapel-nama" required class="z-input" placeholder="Contoh: Matematika Dasar">
                </div>
                <div class="form-group">
                    <label>Kategori Mata Pelajaran</label>
                    <select name="kategori" id="mapel-kategori" class="z-input" required>
                        <option value="Umum">Umum (Utama)</option>
                        <option value="Muatan Lokal">Muatan Lokal</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                        <option value="Pembiasaan/Tambahan">Pembiasaan/Tambahan</option>
                    </select>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-mapel').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Layout Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    
    
    
    

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Table Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    
    
    
    
    

    .z-badge-code {
        background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px;
        font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700;
        border: 1px solid #e2e8f0;
    }
    .mapel-name { font-weight: 700; color: #1e293b; font-size: 0.9rem; }

    .z-action-btns { display: flex; gap: 8px; justify-content: center; }
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s;
    }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Form Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; margin-bottom: 6px; }
    .z-input {
        width: 100%; padding: 0.65rem 0.95rem; border: 1.5px solid #e2e8f0;
        border-radius: 10px; font-size: 0.88rem; transition: 0.2s;
    }
    .z-input:focus { border-color: #0ea5e9; outline: none; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
</style>

<script>
    function addMapelModal() {
        document.getElementById('mapel-modal-title').innerHTML = '<i data-lucide="book-open"></i> Tambah Mata Pelajaran';
        document.getElementById('mapel-form').action = base_url('siakad/mapel/add');
        document.getElementById('mapel-id').value = '';
        document.getElementById('mapel-kode').value = '';
        document.getElementById('mapel-nama').value = '';
        document.getElementById('mapel-kategori').value = 'Umum';
        document.getElementById('modal-mapel').style.display = 'flex';
        lucide.createIcons();
    }

    function editMapel(data) {
        document.getElementById('mapel-modal-title').innerHTML = '<i data-lucide="edit-3"></i> Edit Mata Pelajaran';
        document.getElementById('mapel-form').action = base_url('siakad/mapel/update');
        document.getElementById('mapel-id').value = data.id;
        document.getElementById('mapel-kode').value = data.kode_mapel;
        document.getElementById('mapel-nama').value = data.nama_mapel;
        document.getElementById('mapel-kategori').value = data.kategori || 'Umum';
        document.getElementById('modal-mapel').style.display = 'flex';
        lucide.createIcons();
    }
    lucide.createIcons();
</script>

