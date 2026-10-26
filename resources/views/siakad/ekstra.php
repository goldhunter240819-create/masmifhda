<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">DATA MASTER</div>
                    <h1><i data-lucide="award"></i> Data Ekstrakurikuler</h1>
                    <p>Kelola data kegiatan ekstrakurikuler sekolah beserta pembina dan jumlah anggotanya secara terpusat.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="addEkstraModal()">
                        <i data-lucide="plus-circle"></i> Tambah Ekstrakurikuler
                    </button>
                </div>
            </div>
        </div>

        <div class="z-panel">
            <div class="z-panel-head">
                <div class="z-panel-title"><i data-lucide="list"></i> Daftar Kegiatan Ekstrakurikuler</div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Ekstrakurikuler</th>
                            <th>Pembina / Pelatih</th>
                            <th style="width: 150px; text-align: center;">Total Anggota</th>
                            <th>Tahun Ajaran</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($ekstras)): ?>
                            <tr><td colspan="6" align="center" style="padding: 40px; color: #94a3b8;">Belum ada data kegiatan ekstrakurikuler.</td></tr>
                        <?php else: ?>
                            <?php $no=1; foreach($ekstras as $e): ?>
                            <tr>
                                <td align="center"><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($e['nama_ekstra']); ?></strong></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div class="pembina-avatar"><i data-lucide="user" style="width:14px;height:14px;"></i></div>
                                        <span><?php echo htmlspecialchars($e['pembina_nama']); ?></span>
                                    </div>
                                </td>
                                <td align="center">
                                    <span class="z-badge-count">
                                        <i data-lucide="users" style="width:12px;height:12px;margin-right:4px;"></i>
                                        <?php echo $e['total_anggota']; ?> Siswa
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($e['tahun_ajaran']); ?></td>
                                <td align="center">
                                    <div class="z-action-btns">
                                        <button type="button" class="action-btn edit-btn" onclick='editEkstra(<?php echo htmlspecialchars(json_encode($e), ENT_QUOTES, "UTF-8"); ?>)' title="Edit">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <a href="<?php echo base_url('siakad/ekstra/delete/' . $e['id']); ?>" class="action-btn delete-btn" onclick="return confirm('Hapus kegiatan ekstrakurikuler ini? Ini juga akan menghapus data program kerjanya.')" title="Hapus">
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

<!-- Modal Tambah/Edit Ekstra -->
<div id="ekstraModal" class="z-modal">
    <div class="z-modal-content" style="max-width: 500px;">
        <div class="z-modal-head">
            <h3 id="modalTitle"><i data-lucide="plus-circle"></i> Tambah Ekstrakurikuler</h3>
            <button class="z-modal-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('siakad/ekstra/save'); ?>" method="POST" id="ekstraForm">
            <input type="hidden" name="id" id="ekstra-id">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label>Nama Ekstrakurikuler</label>
                    <input type="text" name="nama_ekstra" id="ekstra-nama-input" class="z-field" placeholder="Ketik nama ekstrakurikuler..." required>
                </div>
                
                <div class="z-form-group">
                    <label>Guru Pembina / Pelatih</label>
                    <select name="guru_id" id="ekstra-guru-id" class="z-field" required>
                        <option value="">-- Pilih Guru Pembina --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<style>
    .pembina-avatar { width: 24px; height: 24px; border-radius: 50%; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; }
    .z-badge-count { background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; }
    
    .z-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
    .z-modal.open { display: flex; }
    .z-modal-content { background: white; border-radius: 20px; width: 90%; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: slideUp 0.3s ease; }
    .z-modal-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .z-modal-head h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .z-modal-close { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; transition: 0.2s; }
    .z-modal-close:hover { background: #f1f5f9; color: #0f172a; }
    .z-modal-body { padding: 1.5rem; }
    .z-modal-foot { padding: 1.25rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: right; display: flex; justify-content: flex-end; gap: 10px; }
    
    .z-form-group { margin-bottom: 1.25rem; }
    .z-form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px; }
    .z-field { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; outline: none; transition: 0.2s; }
    .z-field:focus { border-color: #0ea5e9; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
    
    .z-action-btns { display: flex; gap: 8px; justify-content: center; }
    .action-btn { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.2s; }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }
    
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<script>
    function addEkstraModal() {
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="plus-circle"></i> Tambah Ekstrakurikuler';
        document.getElementById('ekstra-id').value = '';
        
        document.getElementById('ekstra-nama-input').value = '';
        document.getElementById('ekstra-guru-id').value = '';
        
        document.getElementById('ekstraModal').classList.add('open');
        lucide.createIcons();
    }
    
    function editEkstra(data) {
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit-3"></i> Edit Ekstrakurikuler';
        document.getElementById('ekstra-id').value = data.id;
        
        document.getElementById('ekstra-nama-input').value = data.nama_ekstra;
        document.getElementById('ekstra-guru-id').value = data.guru_id;
        
        document.getElementById('ekstraModal').classList.add('open');
        lucide.createIcons();
    }
    
    function closeModal() {
        document.getElementById('ekstraModal').classList.remove('open');
    }
    
    lucide.createIcons();
</script>

