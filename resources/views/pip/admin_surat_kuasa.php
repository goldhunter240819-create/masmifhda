<style>
    .pip-container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; width: 100%; }
    
    .z-panel {
        background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .panel-header {
        padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
    }
    .panel-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    
    .btn-action {
        display: inline-flex; align-items: center; gap: 6px;
        background: #0ea5e9; color: white; padding: 6px 12px;
        border-radius: 8px; text-decoration: none; font-size: 0.75rem;
        font-weight: 700; transition: 0.2s; border: none; cursor: pointer;
    }
    .btn-action:hover { background: #0284c7; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); transform: translateY(-1px); }

    /* Modal Styles */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center;
        z-index: 1000; padding: 20px;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white; border-radius: 16px; width: 100%; max-width: 500px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;
    }
    .modal-header {
        padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h4 { margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; }
    .modal-close { background: none; border: none; cursor: pointer; color: #64748b; padding: 5px; }
    .modal-body { padding: 1.5rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.85rem; color: #475569; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px;
        font-family: inherit; font-size: 0.9rem; box-sizing: border-box;
    }
    .modal-footer {
        padding: 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;
        background: #f8fafc;
    }
    .btn-secondary { background: #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
    .btn-primary { background: #0ea5e9; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>

    <div class="z-main">
        <header class="z-header">
            <div class="z-header-left" style="display: flex; align-items: center; gap: 15px;">
                <button onclick="zToggleSidebar()" class="z-hamburger" style="display: none; background: transparent; border: none; cursor: pointer; color: #1e293b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb">
                    <a href="/" class="parent" style="text-decoration:none;">Portal Admin</a>
                    <i data-lucide="chevron-right" class="sep"></i>
                    <span class="current">Surat Kuasa</span>
                </div>
            </div>
            <div class="z-header-right">
                <div class="header-status-pill">
                    <div class="dot pulse"></div>
                    <span>System Online</span>
                </div>
                <div class="z-header-user" style="display: flex; align-items: center; gap: 10px;">
                    <div class="z-avatar-premium"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name"><?php echo $nama; ?></div>
                        <div class="role">Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="pip-container">
            <div class="z-panel">
                <div class="panel-header">
                    <h3><i data-lucide="file-key-2"></i> Cetak Surat Kuasa / Rekomendasi PIP</h3>
                </div>
                <div class="z-table-wrap" style="padding: 1.5rem;">
                    <div class="z-filter" style="margin-bottom:15px;">
                        <input type="text" id="searchInput" class="z-input" placeholder="Cari NISN atau Nama Siswa..." style="width:100%; max-width:300px;">
                    </div>
                    <table class="z-table" id="dataTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Nama Ibu Kandung</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pipData)): ?>
                            <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 3rem;">Data PIP kosong.</td></tr>
                            <?php else: ?>
                                <?php foreach($pipData as $index => $c): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($c['NISN'] ?? '-'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($c['NAMA_SISWA'] ?? $c['NAMA'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($c['KELAS'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($c['NAMA_IBU_KANDUNG'] ?? '-'); ?></td>
                                    <td style="text-align: right;">
                                        <button type="button" onclick="openModal('<?php echo htmlspecialchars($c['NIK']); ?>', '<?php echo htmlspecialchars(addslashes($c['NAMA_SISWA'] ?? $c['NAMA'] ?? '')); ?>')" class="btn-action">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Form Cetak
                                        </button>
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

<!-- Modal Form Kuasa -->
<div class="modal-overlay" id="kuasaModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Data Penerima Kuasa / Wali</h4>
            <button type="button" class="modal-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 0; margin-bottom: 1.5rem;">
                Masukkan data pihak yang diberikan rekomendasi untuk mewakili siswa <strong id="modalStudentName"></strong> dalam pencairan PIP.
            </p>
            <input type="hidden" id="studentNik">
            <div class="form-group">
                <label>Nama Lengkap (Penerima Kuasa)</label>
                <input type="text" id="kuasaNama" placeholder="Contoh: Budi Santoso" required>
            </div>
            <div class="form-group">
                <label>NIK (Penerima Kuasa)</label>
                <input type="text" id="kuasaNik" placeholder="Contoh: 180XXXXXXXXXXXXX" required>
            </div>
            <div class="form-group">
                <label>Alamat (Penerima Kuasa)</label>
                <textarea id="kuasaAlamat" rows="3" placeholder="Contoh: Desa Tambak Jaya, Kec. Way Tenong..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
            <button type="button" class="btn-primary" onclick="cetakSurat()">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Surat
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    // Simple search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#dataTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let col1 = rows[i].cells[1].textContent.toUpperCase();
            let col2 = rows[i].cells[2].textContent.toUpperCase();
            if (col1.indexOf(filter) > -1 || col2.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });

    // Modal Logic
    function openModal(nik, nama) {
        document.getElementById('studentNik').value = nik;
        document.getElementById('modalStudentName').textContent = nama;
        document.getElementById('kuasaNama').value = '';
        document.getElementById('kuasaNik').value = '';
        document.getElementById('kuasaAlamat').value = '';
        document.getElementById('kuasaModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('kuasaModal').classList.remove('active');
    }

    function cetakSurat() {
        const nik = document.getElementById('studentNik').value;
        const kuasaNama = document.getElementById('kuasaNama').value;
        const kuasaNik = document.getElementById('kuasaNik').value;
        const kuasaAlamat = document.getElementById('kuasaAlamat').value;

        if(!kuasaNama || !kuasaNik || !kuasaAlamat) {
            alert('Harap lengkapi semua data penerima kuasa!');
            return;
        }

        const url = "<?php echo base_url('admin/pip/cetak-kuasa'); ?>?id=" + encodeURIComponent(nik) + 
                    "&kuasa_nama=" + encodeURIComponent(kuasaNama) + 
                    "&kuasa_nik=" + encodeURIComponent(kuasaNik) + 
                    "&kuasa_alamat=" + encodeURIComponent(kuasaAlamat);
        
        window.open(url, '_blank');
        closeModal();
    }
</script>
