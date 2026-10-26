<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">MANAJEMEN GURU</div>
                    <h1><i data-lucide="award"></i> Tugas Tambahan Guru</h1>
                    <p>Kelola penugasan khusus guru seperti Wali Kelas, Pembina Ekstrakurikuler, dan tugas struktural lainnya secara terpusat.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="openModal()">
                        <i data-lucide="plus-circle"></i> Tambah Tugas
                    </button>
                </div>
            </div>
        </div>

        <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="list"></i> Daftar Penugasan Guru</div>
                </div>
                <div style="padding: 0; overflow-x: auto;">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Guru</th>
                                <th>Jenis Tugas</th>
                                <th>Keterangan / Satker</th>
                                <th>Tahun Ajaran</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($tasks)): ?>
                                <tr><td colspan="6" align="center" style="padding: 40px; color: #94a3b8;">Belum ada data penugasan.</td></tr>
                            <?php else: ?>
                                <?php $no=1; foreach($tasks as $t): ?>
                                <tr>
                                    <td align="center"><?php echo $no++; ?></td>
                                    <td><strong><?php echo $t['nama']; ?></strong></td>
                                    <td><span class="z-badge-siakad"><?php echo $t['tugas']; ?></span></td>
                                    <td><?php echo $t['keterangan'] ?: '-'; ?></td>
                                    <td><?php echo $t['tahun_ajaran'] ?: '-'; ?></td>
                                    <td align="center">
                                        <a href="<?php echo base_url('siakad/guru/tugas/delete/' . $t['id']); ?>" class="z-btn-icon z-danger" onclick="return confirm('Hapus penugasan ini?')" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </a>
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
</div>

<!-- Modal Tambah Tugas -->
<div id="taskModal" class="z-modal">
    <div class="z-modal-content" style="max-width: 500px;">
        <div class="z-modal-head">
            <h3><i data-lucide="plus-circle"></i> Tambah Tugas Tambahan</h3>
            <button class="z-modal-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('siakad/guru/tugas/save'); ?>" method="POST">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label>Pilih Guru</label>
                    <select name="guru_id" class="z-field" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo $g['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label>Jenis Tugas</label>
                    <select name="tugas" class="z-field" required onchange="handleTugasChange(this.value)">
                        <option value="">-- Pilih Tugas --</option>
                        <option value="Kepala Laboratorium">Kepala Laboratorium</option>
                        <option value="Kepala Perpustakaan">Kepala Perpustakaan</option>
                        <option value="Waka Kurikulum">Waka Kurikulum</option>
                        <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                        <option value="Waka Sarpras">Waka Sarpras</option>
                        <option value="Waka Humas">Waka Humas</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div class="z-form-group" id="ketGroup">
                    <label id="ketLabel">Keterangan / Satuan Kerja</label>
                    <input type="text" name="keterangan" id="ketInput" class="z-field" placeholder="Contoh: Pramuka / Lab Komputer">
                    <select id="ketSelect" class="z-field" style="display: none;">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="Kelas <?php echo $k['nama_kelas']; ?>">Kelas <?php echo $k['nama_kelas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="ketSelectEkstra" class="z-field" style="display: none;" onchange="handleEkstraChange(this.value)">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <option value="Pramuka">Pramuka</option>
                        <option value="Bola Voli Mini">Bola Voli Mini</option>
                        <option value="Drumband">Drumband</option>
                        <option value="UKS">UKS</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div class="z-form-group">
                    <label>Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="z-field" required>
                        <?php 
                        $unique_tahuns = array_unique(array_column($tahun_ajarans, 'name'));
                        foreach($unique_tahuns as $t_name): ?>
                            <option value="<?php echo $t_name; ?>" <?php echo $t_name == $active_year_name ? 'selected' : ''; ?>>
                                <?php echo $t_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Tugas</button>
            </div>
        </form>
    </div>
</div>

<style>
    .z-badge-siakad { background: #f0f9ff; color: #0284c7; padding: 3px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #bbf7d0; }
    .z-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
    .z-modal.open { display: flex; }
    .z-modal-content { background: white; border-radius: 20px; width: 90%; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: slideUp 0.3s ease; }
    .z-modal-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .z-modal-head h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .z-modal-head .close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; }
    .z-modal-body { padding: 1.5rem; }
    .z-modal-foot { padding: 1.25rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: right; display: flex; justify-content: flex-end; gap: 10px; }
    .z-form-group { margin-bottom: 1.25rem; }
    .z-form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px; }
    .z-field { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; outline: none; transition: 0.2s; }
    .z-field:focus { border-color: #0ea5e9; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
    
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<script>
    function openModal() { document.getElementById('taskModal').classList.add('open'); }
    function closeModal() { document.getElementById('taskModal').classList.remove('open'); }
    
    function handleTugasChange(val) {
        const input = document.getElementById('ketInput');
        const select = document.getElementById('ketSelect');
        const selectEkstra = document.getElementById('ketSelectEkstra');
        const label = document.getElementById('ketLabel');
        
        // Reset display and form attributes
        input.style.display = 'none';
        input.removeAttribute('name');
        input.required = false;
        select.style.display = 'none';
        select.removeAttribute('name');
        select.required = false;
        selectEkstra.style.display = 'none';
        selectEkstra.removeAttribute('name');
        selectEkstra.required = false;
        
        if (val === 'Wali Kelas') {
            label.innerText = 'Pilih Kelas';
            select.style.display = 'block';
            select.setAttribute('name', 'keterangan');
            select.required = true;
        } else if (val === 'Pembina Ekstrakurikuler') {
            label.innerText = 'Pilih Ekstrakurikuler';
            selectEkstra.style.display = 'block';
            selectEkstra.setAttribute('name', 'keterangan');
            selectEkstra.required = true;
            selectEkstra.value = "";
        } else {
            label.innerText = 'Keterangan / Lokasi';
            input.style.display = 'block';
            input.setAttribute('name', 'keterangan');
            input.required = true;
            input.placeholder = 'Contoh: Lab Komputer / Perpustakaan Pusat';
            input.value = "";
        }
    }
    
    function handleEkstraChange(val) {
        const input = document.getElementById('ketInput');
        const selectEkstra = document.getElementById('ketSelectEkstra');
        
        if (val === 'Lainnya') {
            selectEkstra.removeAttribute('name');
            input.style.display = 'block';
            input.setAttribute('name', 'keterangan');
            input.placeholder = 'Masukkan nama ekstrakurikuler kustom';
            input.value = '';
            input.required = true;
            input.focus();
        } else {
            input.style.display = 'none';
            input.removeAttribute('name');
            input.required = false;
            selectEkstra.setAttribute('name', 'keterangan');
        }
    }
    
    lucide.createIcons();
</script>

