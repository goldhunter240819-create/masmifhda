<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div style="flex: 1; padding: 1.5rem;">
        <?php include __DIR__ . '/_siakad_header.php'; ?>
        
        <div class="z-page-head-row">
            <div>
                <h1><i data-lucide="users"></i> Daftar Hadir Rapat</h1>
                <p>Cetak daftar hadir rapat atau pertemuan dengan format tanda tangan standar.</p>
            </div>
        </div>

        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem; max-width: 800px; margin: 0 auto;">
            <form id="form-rapat" action="<?php echo base_url('admin/siakad/lain-lain/rapat/cetak'); ?>" method="POST" target="_blank">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Judul Rapat <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: RAPAT SOSIALISASI PROGRAM SEKOLAH" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Hari / Tanggal <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tanggal" required placeholder="Contoh: Sabtu, 16 Juni 2026" value="<?php echo date('d F Y'); ?>" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Filter Kelas <span style="color: #ef4444;">*</span></label>
                        <select name="kelas_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; background: #fff; cursor: pointer;">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="all">Semua Kelas</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?php echo $k['id']; ?>">KELAS <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Agenda Rapat (Opsional)</label>
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 10px;">Setiap baris baru akan otomatis diberi nomor.</p>
                    <textarea name="agenda" rows="5" placeholder="1. Pembukaan&#10;2. Sambutan Kepala Madrasah&#10;3. Inti Rapat&#10;4. Penutup" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <button type="button" onclick="updatePreview()" class="z-btn z-btn-primary" style="padding: 10px 20px; border-radius: 8px; font-size: 0.95rem; background: #0ea5e9;">
                        <i data-lucide="eye" style="width: 18px; height: 18px; margin-right: 8px;"></i> Lihat Preview
                    </button>
                    <button type="submit" class="z-btn z-btn-primary" style="padding: 10px 20px; border-radius: 8px; font-size: 0.95rem; margin-left: 10px;">
                        <i data-lucide="printer" style="width: 18px; height: 18px; margin-right: 8px;"></i> Buka di Tab Baru
                    </button>
                </div>

            </form>
        </div>

        <!-- LIVE PREVIEW CONTAINER -->
        <div id="preview-container" style="display: none; margin-top: 2rem; background: #e2e8f0; padding: 20px; border-radius: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin: 0; font-size: 1rem; color: #475569;"><i data-lucide="monitor" style="width:16px;height:16px;"></i> Live View (Ukuran Kertas F4)</h3>
            </div>
            <!-- Iframe to hold the generated print view -->
            <iframe name="preview_frame" id="preview_frame" style="width: 100%; height: 800px; background: white; border: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);"></iframe>
        </div>

    </div>
</div>
<script>
    document.querySelectorAll('input[type="text"], textarea, select').forEach(el => {
        el.addEventListener('focus', function() { this.style.borderColor = '#0ea5e9'; });
        el.addEventListener('blur', function() { this.style.borderColor = '#cbd5e1'; });
        // Auto update preview on change
        el.addEventListener('change', function() {
            if (document.getElementById('preview-container').style.display !== 'none') {
                updatePreview();
            }
        });
    });

    function updatePreview() {
        document.getElementById('preview-container').style.display = 'block';
        const form = document.getElementById('form-rapat');
        const oldTarget = form.target;
        form.target = 'preview_frame';
        form.submit();
        form.target = oldTarget; // Restore target
    }
</script>

