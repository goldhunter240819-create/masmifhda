<?php
// data variables: $kelas, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="z-page-head-row">
            <div>
                <h1>Data Kelas & Rombel</h1>
                <p>Kelola pembagian tingkat dan rombongan belajar sekolah.</p>
            </div>
            <button class="btn btn-primary" onclick="addKelasModal()">
                <i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Tambah Kelas
            </button>
        </div>

        <div class="z-panel">
            <div class="z-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 150px;">Tingkat</th>
                            <th>Nama Kelas / Rombel</th>
                            <th>Wali Kelas</th>
                            <th style="text-align: right; width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($kelas)): ?>
                            <tr><td colspan="3" style="text-align:center; padding:3rem; color:#94a3b8;">Belum ada data kelas.</td></tr>
                        <?php else: ?>
                            <?php 
                            $lastTingkat = null;
                            foreach($kelas as $k): 
                            ?>
                            <tr class="<?php echo ($k['tingkat'] != $lastTingkat) ? 'tingkat-border' : ''; ?>">
                                <td>
                                    <?php if($k['tingkat'] != $lastTingkat): ?>
                                        <span class="pill" style="background:#0369a1; color:white;">TINGKAT <?php echo $k['tingkat']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight:700; color:#1e293b; font-size:1.1rem;"><?php echo $k['nama_kelas']; ?></div>
                                </td>
                                <td>
                                    <?php 
                                    $wk = isset($wali_map['Kelas ' . $k['nama_kelas']]) ? $wali_map['Kelas ' . $k['nama_kelas']] : (isset($wali_map[$k['nama_kelas']]) ? $wali_map[$k['nama_kelas']] : null); 
                                    if($wk): ?>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div style="width:28px; height:28px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold; color:#475569; overflow:hidden;">
                                                <?php if(!empty($wk['guru_foto']) && file_exists(__DIR__ . '/../../../public/uploads/guru/' . $wk['guru_foto'])): ?>
                                                    <img src="<?php echo base_url('uploads/guru/' . $wk['guru_foto']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Foto">
                                                <?php else: ?>
                                                    <?php echo substr($wk['guru_nama'], 0, 2); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:#0f172a; font-size:0.9rem;"><?php echo $wk['guru_nama']; ?></div>
                                                <div style="font-size:0.75rem; color:#64748b;">Wali Kelas</div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-style:italic; font-size:0.85rem;">Belum diset</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                                        <button type="button" style="padding: 6px 10px; border-radius: 8px; background: #f0fdf4; color: #16a34a; border: none; cursor: pointer;" onclick='setWaliKelas("Kelas <?php echo $k['nama_kelas']; ?>", "<?php echo $wk ? $wk['guru_id'] : ''; ?>")' title="Set Wali Kelas">
                                            <i data-lucide="user-check" style="width:16px;height:16px;"></i>
                                        </button>
                                        <button type="button" style="padding: 6px 10px; border-radius: 8px; background: #eff6ff; color: #2563eb; border: none; cursor: pointer;" onclick='editKelas(<?php echo htmlspecialchars(json_encode($k), ENT_QUOTES, "UTF-8"); ?>, "<?php echo $wk ? $wk['guru_id'] : ''; ?>")' title="Edit Kelas">
                                            <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                                        </button>
                                        <a href="<?php echo base_url('siakad/kelas/delete/' . $k['id']); ?>" onclick="return confirm('Hapus kelas ini?')" style="padding: 6px 10px; border-radius: 8px; background: #fff1f2; color: #e11d48; border: none; cursor: pointer; text-decoration: none;" title="Hapus">
                                            <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            $lastTingkat = $k['tingkat'];
                            endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelas -->
<div id="modal-kelas" class="z-modal">
    <div class="z-modal-content small" style="max-width: 450px;">
        <div class="z-modal-header">
            <div class="z-modal-title" id="kelas-modal-title" style="font-weight:800; display:flex; align-items:center; gap:8px;"><i data-lucide="layers"></i> Tambah Kelas</div>
            <button type="button" onclick="document.getElementById('modal-kelas').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form id="kelas-form" action="<?php echo base_url('siakad/kelas/add'); ?>" method="POST">
                <input type="hidden" name="id" id="kelas-id">
                <div class="z-form-group" style="margin-bottom: 1.25rem;">
                    <label class="z-label" style="display:block; margin-bottom:6px; font-weight:700;">Tingkat Pendidikan</label>
                    <select name="tingkat" id="kelas-tingkat" required class="z-field" style="width:100%; padding:6px 10px; border-radius:8px;">
                        <option value="">Pilih Tingkat</option>
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?php echo $i; ?>">Tingkat <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="z-form-group" style="margin-bottom: 1.25rem;">
                    <label class="z-label" style="display:block; margin-bottom:6px; font-weight:700;">Nama Rombongan Belajar</label>
                    <input type="text" name="nama_kelas" id="kelas-nama" required class="z-field" style="width:100%; padding:6px 10px; border-radius:8px;" placeholder="Contoh: 10-IPA-1 atau X-A">
                </div>
                <div class="z-form-group" style="margin-bottom: 1.25rem;">
                    <label class="z-label" style="display:block; margin-bottom:6px; font-weight:700;">Pilih Wali Kelas <span style="font-size:0.7rem; font-weight:normal; color:#64748b;">(Opsional)</span></label>
                    <select name="wali_kelas_id" id="kelas-wali-id" class="z-field" style="width:100%; padding:6px 10px; border-radius:8px;">
                        <option value="">-- Kosongkan Wali Kelas --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-modal-footer" style="margin-top: 1.5rem;">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-kelas').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Wali Kelas -->
<div id="modal-wali" class="z-modal">
    <div class="z-modal-content small" style="max-width: 450px;">
        <div class="z-modal-header">
            <div class="z-modal-title" style="font-weight:800; display:flex; align-items:center; gap:8px;"><i data-lucide="user-check"></i> Set Wali Kelas</div>
            <button type="button" onclick="document.getElementById('modal-wali').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/kelas/assign-wali'); ?>" method="POST">
                <div class="z-form-group" style="margin-bottom: 1.25rem;">
                    <label class="z-label" style="display:block; margin-bottom:6px; font-weight:700;">Nama Kelas / Rombel</label>
                    <input type="text" name="nama_kelas" id="wali-kelas-nama" readonly class="z-field" style="width:100%; padding:6px 10px; border-radius:8px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; cursor:not-allowed;">
                </div>
                <div class="z-form-group" style="margin-bottom: 1.25rem;">
                    <label class="z-label" style="display:block; margin-bottom:6px; font-weight:700;">Pilih Guru (Wali Kelas)</label>
                    <select name="guru_id" id="wali-guru-id" class="z-field" style="width:100%; padding:6px 10px; border-radius:8px;">
                        <option value="">-- Kosongkan Wali Kelas --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-modal-footer" style="margin-top: 1.5rem;">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-wali').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">SIMPAN WALI KELAS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .z-main {
        margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
        flex: 1; background: var(--z-bg); min-height: 100vh;
        display: flex; flex-direction: column;
        padding: var(--z-gap) var(--z-gap) var(--z-gap) 0;
    }
    .tingkat-border td { border-top: 2px solid #f1f5f9; }
    
    @media (max-width: 768px) {
        .z-main { margin-left: 0; padding: 1.25rem; }
        .z-page-head-row { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<script>
    function addKelasModal() {
        document.getElementById('kelas-modal-title').innerHTML = '<i data-lucide="layers"></i> Tambah Kelas';
        document.getElementById('kelas-form').action = base_url('siakad/kelas/add');
        document.getElementById('kelas-id').value = '';
        document.getElementById('kelas-tingkat').value = '';
        document.getElementById('kelas-nama').value = '';
        document.getElementById('kelas-wali-id').value = '';
        document.getElementById('modal-kelas').style.display = 'flex';
        lucide.createIcons();
    }

    function editKelas(data, currentWaliId = '') {
        document.getElementById('kelas-modal-title').innerHTML = '<i data-lucide="edit-3"></i> Edit Kelas';
        document.getElementById('kelas-form').action = base_url('siakad/kelas/update');
        
        let modal = document.getElementById('modal-kelas');
        document.getElementById('kelas-id').value = data.id;
        document.getElementById('kelas-tingkat').value = data.tingkat;
        document.getElementById('kelas-nama').value = data.nama_kelas;
        document.getElementById('kelas-wali-id').value = currentWaliId;
        
        let hid = document.getElementById('kelas-id');
        if(!hid) {
            hid = document.createElement('input');
            hid.type = 'hidden';
            hid.id = 'kelas-id';
            hid.name = 'id';
            document.getElementById('kelas-form').appendChild(hid);
            hid.value = data.id;
        }

        modal.style.display = 'flex';
        lucide.createIcons();
    }
    
    function setWaliKelas(namaKelas, guruId) {
        document.getElementById('wali-kelas-nama').value = namaKelas;
        document.getElementById('wali-guru-id').value = guruId || '';
        document.getElementById('modal-wali').style.display = 'flex';
        lucide.createIcons();
    }
    
    lucide.createIcons();
</script>

