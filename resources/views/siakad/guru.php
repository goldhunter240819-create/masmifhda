<?php
// data variables: $gurus, $inst, $title
?>
<style>
    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Bulk Bar Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .bulk-bar {
        display: none; position: sticky; top: 0; z-index: 100;
        background: #0369a1; color: white; padding: 0.5rem 1.25rem;
        border-radius: 14px 14px 0 0; justify-content: space-between; align-items: center;
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { transform: translateY(-100%); } to { transform: translateY(0); } }
    .bulk-info { display: flex; align-items: center; gap: 8px; font-weight: 700; }
    .count-badge { background: #fff; color: #0369a1; padding: 1px 8px; border-radius: 8px; font-size: 0.85rem; }
    .bulk-actions { display: flex; gap: 8px; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Components Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .guru-profile-cell { display: flex; align-items: center; gap: 10px; }
    .guru-avatar {
        width: 36px; height: 36px; border-radius: 10px; overflow: hidden;
        border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        position: relative; cursor: pointer; transition: 0.3s;
    }
    .guru-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-hover {
        position: absolute; inset: 0; background: rgba(0,0,0,0.4);
        display: flex; align-items: center; justify-content: center;
        color: white; opacity: 0; transition: 0.3s;
    }
    .guru-avatar:hover .avatar-hover { opacity: 1; }
    .avatar-actions button {
        background: #f1f5f9; border: none; width: 24px; height: 24px;
        border-radius: 6px; cursor: pointer; color: #475569; transition: 0.2s;
    }
    .avatar-actions button:hover { background: #e2e8f0; color: #0f172a; }

    .guru-info-main .name { font-weight: 800; color: #1e293b; font-size: 0.85rem; margin-bottom: 2px; }
    .guru-info-main .nip { font-size: 0.7rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; }
    
    .guru-contact { display: flex; flex-direction: column; gap: 2px; }
    .contact-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #64748b; }
    .contact-item i { width: 11px; height: 11px; }

    .z-action-btns { display: flex; gap: 6px; justify-content: center; }
    .action-btn {
        width: 28px; height: 28px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s;
    }
    .card-btn { background: #f0f9ff; color: #0284c7; }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Forms Modernization Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .form-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem; }
    .form-section {
        background: rgba(248, 250, 252, 0.6); backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 20px;
        padding: 1.75rem; box-shadow: inset 0 2px 4px rgba(255,255,255,0.6), 0 8px 20px rgba(0,0,0,0.03);
    }
    .form-section h4 {
        display: flex; align-items: center; gap: 10px; font-size: 1.05rem; font-weight: 800;
        color: #0f172a; margin-bottom: 1.5rem; padding-bottom: 12px;
        border-bottom: 2px dashed #e2e8f0;
    }
    .form-section h4 i { width: 20px; height: 20px; color: #0284c7; padding: 6px; background: #f0f9ff; border-radius: 8px; }

    /* Bulk Action Bar */
    .bulk-bar {
        background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 12px 20px;
        margin-bottom: 20px; display: none; align-items: center; justify-content: space-between;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .bulk-bar.active { display: flex; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .count-badge { background: #0ea5e9; color: white; padding: 2px 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem; margin-right: 8px; }
    
    .z-checkbox { width: 18px; height: 18px; accent-color: #0ea5e9; cursor: pointer; }

    /* Modal Styling Adjustments */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.25rem; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Modern Input */
    .z-input, .z-textarea {
        width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 12px;
        font-family: inherit; font-size: 0.9rem; color: #1e293b; background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }
    .z-input:hover, .z-textarea:hover { border-color: #94a3b8; }
    .z-input:focus, .z-textarea:focus { border-color: #0284c7; background: #fff; box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15); outline: none; transform: translateY(-1px); }
    .z-textarea { resize: vertical; min-height: 80px; }

    /* Radios */
    .radio-group { display: flex; gap: 24px; padding: 8px 0; }
    .radio-item { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 600; color: #334155; transition: 0.2s; }
    .radio-item:hover { color: #0284c7; }
    .radio-item input { width: 18px; height: 18px; accent-color: #0284c7; cursor: pointer; }
    
    /* Modern File Upload */
    .file-upload-box {
        border: 2px dashed #94a3b8; border-radius: 16px; padding: 2rem 1.5rem;
        text-align: center; color: #64748b; position: relative; cursor: pointer;
        transition: all 0.3s; background: rgba(255,255,255,0.5); display: flex; flex-direction: column; align-items: center; gap: 8px;
    }
    .file-upload-box i { width: 28px; height: 28px; color: #94a3b8; transition: 0.3s; }
    .file-upload-box:hover { border-color: #0284c7; background: #f0fdf4; color: #0284c7; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(5,150,105,0.1); }
    .file-upload-box:hover i { color: #0284c7; transform: scale(1.1); }
    .file-upload-box input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Export & Import Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .export-options { display: grid; gap: 12px; margin-bottom: 1.5rem; }
    .export-option {
        display: flex; align-items: center; gap: 15px; padding: 1.25rem;
        border: 2px solid #f1f5f9; border-radius: 16px; cursor: pointer; transition: 0.2s;
    }
    .export-option:hover { border-color: #0284c7; }
    .export-option input { width: 20px; height: 20px; accent-color: #0284c7; }
    .option-title { font-weight: 800; color: #1e293b; font-size: 0.95rem; }
    .option-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .import-layout { display: grid; grid-template-columns: 1fr 240px; gap: 2rem; }
    .import-textarea { height: 300px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; }
    .guide-card { background: #f8fafc; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; }
    .guide-card h5 { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #1e293b; }
    .guide-card ol { padding-left: 1.25rem; font-size: 0.8rem; color: #64748b; margin-bottom: 15px; }
    .example-box { background: #fff; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.7rem; color: #94a3b8; font-family: monospace; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Photo Viewer Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .photo-viewer .viewer-content { max-width: 90%; max-height: 90%; text-align: center; }
    .photo-viewer img { max-width: 100%; max-height: 80vh; border-radius: 24px; border: 6px solid white; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
    .viewer-name { color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 800; letter-spacing: 1px; }

    #modal-teacher-card.card-viewer { overflow-y: auto !important; display: none; align-items: flex-start !important; justify-content: center !important; padding: 2rem 1rem !important; }
    #modal-teacher-card .viewer-layout { display: flex; flex-direction: column; gap: 1.5rem; align-items: center; margin: auto; }
    .printable-area { display: flex; flex-direction: column; gap: 1.25rem; align-items: center; }
    .id-card {
        width: 85.6mm; height: 54mm; background: white; border-radius: 4mm;
        overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        position: relative; display: flex; flex-direction: column; border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .id-card.front .card-header {
        padding: 8px 15px; display: flex; align-items: center; gap: 12px;
        background: linear-gradient(135deg, #064e3b 0%, #0ea5e9 100%); color: white;
    }
    .inst-logo { height: 32px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    .inst-info .card-label { font-size: 7px; font-weight: 600; opacity: 0.8; letter-spacing: 1px; }
    .inst-info .inst-name { display: block; font-size: 12px; font-weight: 800; }
    .card-body { flex: 1; padding: 12px 18px; display: flex; gap: 15px; }
    .photo-box { width: 24mm; height: 32mm; border-radius: 6px; overflow: hidden; background: #f1f5f9; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex-shrink: 0; }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; }
    .info-table { font-size: 8.5px; border-collapse: collapse; line-height: 1.4; flex: 1; }
    .info-table td:first-child { font-weight: 700; color: #64748b; width: 14mm; }
    .info-table td.bold { font-weight: 800; color: #0f172a; }
    .signature-box { display: flex; align-items: center; gap: 8px; margin-top: auto; }
    .qr-ttd { width: 13mm; height: 13mm; border: 1px solid #e2e8f0; border-radius: 2px; }
    .sig-text { font-size: 6px; color: #475569; display: flex; flex-direction: column; }
    .sig-text .name { font-weight: 800; color: #0f172a; border-bottom: 0.5px solid #0f172a; margin-top: 4px; padding-bottom: 1px; }
    .card-footer-bar { height: 2.5mm; background: linear-gradient(90deg, #064e3b, #0ea5e9); }

    .id-card.back { padding: 5mm; align-items: center; justify-content: center; }
    .back-header { text-align: center; margin-bottom: 4mm; }
    .back-header .title { display: block; font-weight: 800; font-size: 11px; color: #1e293b; }
    .back-header .subtitle { font-size: 7px; color: #94a3b8; }
    .qr-box { background: white; padding: 2mm; border-radius: 3mm; border: 1px solid #e2e8f0; margin-bottom: 4mm; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .qr-box img { width: 25mm; height: 25mm; }
    .creds-box { width: 100%; display: flex; justify-content: space-between; border-top: 1px dashed #cbd5e1; padding-top: 4mm; }
    .cred .label { display: block; font-size: 6px; font-weight: 700; color: #94a3b8; margin-bottom: 2px; }
    .cred .value { font-weight: 800; font-size: 10px; color: #0f172a; font-family: monospace; }

    @media print {
        body * { visibility: hidden; }
        #modal-teacher-card, #modal-teacher-card * { visibility: visible; }
        #modal-teacher-card { position: absolute; left: 0; top: 0; background: white !important; display: block !important; padding: 0 !important; }
        .printable-area { display: block !important; }
        .id-card { box-shadow: none !important; margin-bottom: 10mm; page-break-inside: avoid; border: 0.5px solid #ddd !important; }
        .viewer-actions { display: none !important; }
        -webkit-print-color-adjust: exact;
    }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ DataTables Custom Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 0 1.5rem; }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 1.5rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; 
        background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #0284c7 !important; color: #fff !important; border-color: #0284c7 !important; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">DATA MASTER</div>
                    <h1><i data-lucide="users"></i> Manajemen Data Guru</h1>
                    <p>Kelola profil, jabatan, dan cetak kartu identitas guru & tendik.</p>
                </div>
                <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button class="e-btn e-btn-glass" onclick="document.getElementById('modal-export-guru').style.display='flex'">
                        <i data-lucide="printer"></i> Cetak / Export
                    </button>
                    <button class="e-btn e-btn-blue-light" onclick="document.getElementById('modal-import-guru').style.display='flex'">
                        <i data-lucide="file-up"></i> Smart-Paste
                    </button>
                    <a href="<?php echo base_url('siakad/guru/cetak-kartu?mode=horizontal'); ?>" target="_blank" class="e-btn e-btn-dark">
                        <i data-lucide="id-card"></i> Kartu (H)
                    </a>
                    <a href="<?php echo base_url('siakad/guru/cetak-kartu?mode=vertical'); ?>" target="_blank" class="e-btn e-btn-dark">
                        <i data-lucide="id-card"></i> Kartu (V)
                    </a>
                    <button class="e-btn e-btn-primary" onclick="openAddGuruModal()">
                        <i data-lucide="user-plus"></i> Tambah Guru
                    </button>
                </div>
            </div>
        </div>

        <div class="z-panel">
            <form id="form-bulk-guru" action="<?php echo base_url('siakad/guru/delete-bulk'); ?>" method="POST">
                <!-- Bulk Action Bar -->
                <div id="bulk-action-bar-guru" class="bulk-bar">
                    <div class="bulk-info">
                        <span class="count-badge" id="selected-count-guru">0</span>
                        <span>Guru terpilih</span>
                    </div>
                    <div class="bulk-actions">
                        <button type="button" onclick="confirmBulkActionGuru('print')" class="btn btn-sm z-btn-secondary">
                            <i data-lucide="printer"></i> Cetak Kartu
                        </button>
                        <button type="button" onclick="confirmBulkActionGuru('delete')" class="btn btn-sm z-btn-danger">
                            <i data-lucide="trash-2"></i> Hapus
                        </button>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table id="table-data-guru" class="">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <input type="checkbox" id="check-all-guru" class="z-checkbox">
                                </th>
                                <th style="width: 70px;">Profil</th>
                                <th style="width: 150px;">NIK</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($gurus as $g): ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="<?php echo $g['id']; ?>" class="check-guru z-checkbox" onchange="updateBulkGuru()">
                                </td>
                                <td>
                                    <div class="guru-profile-cell">
                                        <div class="guru-avatar" onclick="viewPhoto('<?php echo $g['foto']; ?>', '<?php echo addslashes($g['nama']); ?>')">
                                            <?php if($g['foto'] && file_exists(__DIR__ . "/../../../public/uploads/guru/" . $g['foto'])): ?>
                                                <img loading="lazy" id="img-guru-<?php echo $g['id']; ?>" src="<?php echo base_url('thumb.php?src=uploads/guru/' . $g['foto'] . '&w=100&h=100'); ?>" alt="Foto">
                                            <?php else: ?>
                                                <img loading="lazy" id="img-guru-<?php echo $g['id']; ?>" src="https://ui-avatars.com/api/?name=<?php echo urlencode($g['nama']); ?>&background=059669&color=fff&bold=true" alt="Avatar">
                                            <?php endif; ?>
                                            <div class="avatar-hover">
                                                <i data-lucide="maximize-2"></i>
                                            </div>
                                        </div>
                                        <div class="avatar-actions">
                                            <button type="button" onclick="quickUpload('<?php echo $g['id']; ?>', '<?php echo addslashes($g['nama']); ?>')" title="Ganti Foto">
                                                <i data-lucide="camera"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="guru-info-main" style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #475569; font-weight: 600;">
                                        <?php echo $g['nik'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="guru-info-main">
                                        <div class="name"><?php echo $g['nama']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="z-badge-outline"><?php echo $g['jabatan'] ?: 'Guru'; ?></span>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <button type="button" class="action-btn card-btn" onclick='showTeacherCard(<?php echo htmlspecialchars(json_encode($g), ENT_QUOTES, "UTF-8"); ?>)' title="Lihat Kartu">
                                            <i data-lucide="contact"></i>
                                        </button>
                                        <button type="button" class="action-btn edit-btn" onclick='editGuru(<?php echo htmlspecialchars(json_encode($g), ENT_QUOTES, "UTF-8"); ?>)' title="Edit Data">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <a href="<?php echo base_url('siakad/guru/delete/' . $g['id']); ?>" onclick="return confirm('Hapus guru ini? Akun login juga akan dihapus.')" class="action-btn delete-btn" title="Hapus">
                                            <i data-lucide="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Guru -->
<div id="modal-add-guru" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header">
            <h3 id="modal-title-guru"><i data-lucide="user-plus"></i> Tambah Guru & Tendik</h3>
            <button onclick="document.getElementById('modal-add-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form id="form-guru" action="<?php echo base_url('siakad/guru/add'); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-sections">
                    <div class="form-section">
                        <h4><i data-lucide="info"></i> Identitas Utama</h4>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" required class="z-input" placeholder="Contoh: Ahmad Rifa'i, M.Pd">
                            </div>
                            <div class="form-group">
                                <label>NIK KTP (Username & Wajib)</label>
                                <input type="text" name="nik" required class="z-input" placeholder="Masukkan 16 digit NIK">
                            </div>
                            <div class="form-group">
                                <label>NIP Guru (Opsional)</label>
                                <input type="text" name="nip" class="z-input" placeholder="Kosongkan jika tidak ada">
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="z-input" placeholder="Kota lahir">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label>Jenis Kelamin</label>
                                <div class="radio-group">
                                    <label class="radio-item">
                                        <input type="radio" name="jenis_kelamin" value="L" checked>
                                        <span>Laki-laki</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="jenis_kelamin" value="P">
                                        <span>Perempuan</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Foto Profil</label>
                                <div class="file-upload-box">
                                    <i data-lucide="image"></i>
                                    <span>Klik untuk pilih foto</span>
                                    <input type="file" name="foto" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i data-lucide="briefcase"></i> Kontak & Kepegawaian</h4>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Jabatan / Peran</label>
                                <input type="text" name="jabatan" placeholder="Contoh: Guru Matematika / Wali Kelas" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label style="display:flex; align-items:center; gap:12px; cursor:pointer; padding:12px 15px; background: linear-gradient(to right, #f0fdf4, #fff); border:1.5px solid #86efac; border-radius:12px;">
                                    <input type="checkbox" name="is_kamad" value="1" style="width:22px; height:22px; accent-color:#0284c7;">
                                    <div style="text-transform:none;">
                                        <div style="font-size:0.95rem; font-weight:800; color:#064e3b; margin-bottom:2px;"><i data-lucide="shield-check" style="width:16px;height:16px;display:inline-block;vertical-align:-3px;"></i> Akses Kepala Madrasah (Administrator)</div>
                                        <div style="font-size:0.75rem; color:#166534; font-weight:500; letter-spacing:0;">Centang ini untuk memberikan hak akses portal Admin penuh.</div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Nomor HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="z-input" placeholder="08xxxxxx">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="z-input" placeholder="guru@sekolah.id">
                            </div>
                            <div class="form-group full-width">
                                <label>Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="z-input">
                                    <option value="Honorer">Honorer</option>
                                    <option value="Sertifikasi">Sertifikasi</option>
                                    <option value="PNS">PNS</option>
                                    <option value="P3K">P3K</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>NPK</label>
                                <input type="text" name="npk" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>NPWP</label>
                                <input type="text" name="npwp" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label>No Rekening</label>
                                <input type="text" name="no_rekening" class="z-input">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4><i data-lucide="map-pin"></i> Domisili Lengkap</h4>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Alamat Lengkap (Jalan/Dusun)</label>
                                <textarea name="alamat" class="z-textarea" placeholder="Alamat domisili..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>RT</label>
                                <input type="text" name="rt" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>RW</label>
                                <input type="text" name="rw" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Desa / Kelurahan</label>
                                <input type="text" name="desa" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Kabupaten / Kota</label>
                                <input type="text" name="kota" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" class="z-input">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i data-lucide="award"></i> Sertifikasi & Profesi</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>NRG</label>
                                <input type="text" name="nrg" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>No Peserta Sertifikasi</label>
                                <input type="text" name="no_peserta_sertifikasi" class="z-input">
                            </div>
                            <div class="form-group full-width">
                                <label>No Sertifikat</label>
                                <input type="text" name="no_sertifikat" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Sertifikat</label>
                                <input type="date" name="tgl_sertifikat" class="z-input">
                            </div>
                            <div class="form-group">
                                <label>Jenjang Sertifikat</label>
                                <select name="jenjang_sertifikat" class="z-input">
                                    <option value="">- Pilih -</option>
                                    <option value="RA">RA</option>
                                    <option value="MI">MI</option>
                                    <option value="MTs">MTs</option>
                                    <option value="MA">MA</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label>Mapel Sertifikat</label>
                                <input type="text" name="mapel_sertifikat" class="z-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-add-guru').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">Simpan & Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export Guru -->
<div id="modal-export-guru" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3><i data-lucide="printer"></i> Export Data Guru</h3>
            <button onclick="document.getElementById('modal-export-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/guru/export'); ?>" method="GET" target="_blank">
                <div class="export-options">
                    <label class="export-option">
                        <input type="radio" name="mode" value="public" checked>
                        <div class="option-content">
                            <div class="option-title">Data Publik (Umum)</div>
                            <div class="option-desc">NIP, Nama, Jabatan, dan Pendidikan.</div>
                        </div>
                    </label>
                    <label class="export-option">
                        <input type="radio" name="mode" value="full">
                        <div class="option-content">
                            <div class="option-title">Data Lengkap (Internal)</div>
                            <div class="option-desc">Seluruh biodata, NIK, No HP, dan Alamat.</div>
                        </div>
                    </label>
                </div>
                <div class="z-modal-footer">
                    <button type="submit" class="z-btn z-btn-primary full-width">Buka Preview Cetak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Guru -->
<div id="modal-import-guru" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header">
            <h3><i data-lucide="file-up"></i> Import Smart-Paste</h3>
            <button onclick="document.getElementById('modal-import-guru').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/guru/import'); ?>" method="POST">
                <div class="import-layout">
                    <div class="import-main">
                        <label class="z-label">Paste Data Excel/Word di Sini</label>
                        <textarea name="paste_data" class="z-textarea import-textarea" placeholder="NIK [TAB] Nama [TAB] Jabatan [TAB] Email"></textarea>
                    </div>
                    <div class="import-sidebar">
                        <div class="guide-card">
                            <h5><i data-lucide="help-circle"></i> Panduan Kolom</h5>
                            <p>Urutan kolom harus sesuai:</p>
                            <ol>
                                <li>NIK / Username (Wajib)</li>
                                <li>Nama Lengkap (Wajib)</li>
                                <li>Jabatan</li>
                                <li>Email (Opsional)</li>
                            </ol>
                            <div class="example-box">
                                320123... &nbsp; Budi S &nbsp; Guru IPA
                            </div>
                        </div>
                    </div>
                </div>
                <div class="z-modal-footer">
                    <button type="submit" class="z-btn z-btn-primary">Proses Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lihat Foto -->
<div id="modal-view-photo" class="z-modal photo-viewer" onclick="this.style.display='none'">
    <div class="viewer-content">
        <img id="view-photo-img" src="">
        <div id="view-photo-name" class="viewer-name"></div>
    </div>
</div>

<!-- Teacher Card Modal -->
<div id="modal-teacher-card" class="z-modal card-viewer">
    <div class="viewer-layout">
        <div id="printable-teacher-card" class="printable-area">
            <!-- Front Card -->
            <div class="id-card front">
                <div class="card-header">
                    <?php if($inst && $inst['logo']): ?>
                        <img src="<?php echo base_url('uploads/logo/' . $inst['logo']); ?>" class="inst-logo">
                    <?php endif; ?>
                    <div class="inst-info">
                        <span class="card-label">KARTU IDENTITAS GURU</span>
                        <span class="inst-name"><?php echo $inst ? $inst['nama'] : 'Aula System ACADEMY'; ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="photo-box">
                        <img id="tc-photo" src="">
                    </div>
                    <div class="info-box">
                        <table class="info-table">
                            <tr><td>Nama</td><td>:</td><td id="tc-name" class="bold"></td></tr>
                            <tr><td>Jabatan</td><td>:</td><td id="tc-jabatan"></td></tr>
                            <tr><td>Tmpt Lahir</td><td>:</td><td id="tc-tempat-lahir"></td></tr>
                            <tr><td>Tgl Lahir</td><td>:</td><td id="tc-tanggal-lahir"></td></tr>
                            <tr><td>Alamat</td><td>:</td><td id="tc-alamat" class="truncate"></td></tr>
                        </table>
                        <div class="signature-box">
                            <img id="tc-ttd-qr" src="" class="qr-ttd">
                            <div class="sig-text">
                                <span class="date"><?php echo isset($inst['kota']) ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?></span>
                                <span class="title">Kepala Madrasah</span>
                                <span class="name"><?php echo isset($inst['nama_kepala']) ? $inst['nama_kepala'] : 'Kepala Sekolah'; ?></span>
                                <span class="nip">NIP. <?php echo isset($inst['nip_kepala']) ? $inst['nip_kepala'] : '-'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer-bar"></div>
            </div>

            <!-- Back Card -->
            <div class="id-card back">
                <div class="back-content">
                    <div class="back-header">
                        <span class="title">AKUN AKSES DIGITAL</span>
                        <span class="subtitle">Gunakan QR Code ini untuk verifikasi & login</span>
                    </div>
                    <div class="qr-box">
                        <img id="tc-qr" src="">
                    </div>
                </div>
            </div>
        </div>

        <div class="viewer-actions">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-teacher-card').style.display='none'">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i data-lucide="printer"></i> Cetak Kartu</button>
        </div>
    </div>
</div>

<input type="file" id="quick-auto-input-guru" accept="image/*" hidden onchange="handleAutoUpload(this)">
<input type="hidden" id="quick-auto-guru-id">

<script>
    function openAddGuruModal() {
        const modal = document.getElementById('modal-add-guru');
        document.getElementById('modal-title-guru').innerHTML = '<i data-lucide="user-plus"></i> Tambah Guru & Tendik';
        const form = document.getElementById('form-guru');
        form.action = base_url('siakad/guru/add');
        form.reset();
        form.querySelector('[name="nip"]').readOnly = false;
        const hid = form.querySelector('[name="id"]');
        if (hid) hid.remove();
        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function viewPhoto(foto, nama, isUrl = false) {
        const img = document.getElementById('view-photo-img');
        const modal = document.getElementById('modal-view-photo');
        if(isUrl) img.src = foto;
        else img.src = foto ? '/uploads/guru/' + foto : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=059669&color=fff&bold=true';
        document.getElementById('view-photo-name').innerText = nama;
        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function quickUpload(id, nama) {
        document.getElementById('quick-auto-guru-id').value = id;
        document.getElementById('quick-auto-input-guru').click();
    }

    async function handleAutoUpload(input) {
        if (!input.files || !input.files[0]) return;
        const id = document.getElementById('quick-auto-guru-id').value;
        const tableImg = document.getElementById('img-guru-' + id);
        tableImg.style.opacity = '0.5';
        const formData = new FormData();
        formData.append('id', id);
        formData.append('foto', input.files[0]);
        try {
            const response = await fetch(base_url('siakad/guru/update-foto'), { method: 'POST', body: formData });
            const res = await response.json();
            if(res.success) {
                tableImg.src = '/uploads/guru/' + res.foto + '?t=' + Date.now();
                ZNS.showSuccess('Foto profil berhasil diperbarui');
            } else ZNS.showError('Gagal: ' + res.message);
        } catch (error) { ZNS.showError('Kesalahan koneksi.'); }
        finally { tableImg.style.opacity = '1'; input.value = ''; }
    }

    function showTeacherCard(data) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = base_url('siakad/guru/cetak-kartu-bulk');
        form.target = '_blank';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = data.id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function editGuru(data) {
        const modal = document.getElementById('modal-add-guru');
        document.getElementById('modal-title-guru').innerHTML = '<i data-lucide="edit-3"></i> Edit Data Guru';
        const form = document.getElementById('form-guru');
        form.action = base_url('siakad/guru/update');
        
        form.querySelector('[name="nama"]').value = data.nama;
        form.querySelector('[name="nip"]').value = data.nip || '';
        form.querySelector('[name="nip"]').readOnly = false;
        form.querySelector('[name="nik"]').value = data.nik || '';
        form.querySelector('[name="nik"]').readOnly = false;
        form.querySelector('[name="tempat_lahir"]').value = data.tempat_lahir || '';
        form.querySelector('[name="tanggal_lahir"]').value = data.tanggal_lahir || '';
        form.querySelector(`[name="jenis_kelamin"][value="${data.jenis_kelamin || 'L'}"]`).checked = true;
        form.querySelector('[name="jabatan"]').value = data.jabatan || '';
        form.querySelector('[name="no_hp"]').value = data.no_hp || '';
        form.querySelector('[name="email"]').value = data.email || '';
        form.querySelector('[name="alamat"]').value = data.alamat || '';
        form.querySelector('[name="rt"]').value = data.rt || '';
        form.querySelector('[name="rw"]').value = data.rw || '';
        form.querySelector('[name="desa"]').value = data.desa || '';
        form.querySelector('[name="kecamatan"]').value = data.kecamatan || '';
        form.querySelector('[name="kota"]').value = data.kota || '';
        form.querySelector('[name="provinsi"]').value = data.provinsi || '';
        form.querySelector('[name="kode_pos"]').value = data.kode_pos || '';
        form.querySelector('[name="npk"]').value = data.npk || '';
        form.querySelector('[name="npwp"]').value = data.npwp || '';
        form.querySelector('[name="no_rekening"]').value = data.no_rekening || '';
        form.querySelector('[name="nrg"]').value = data.nrg || '';
        form.querySelector('[name="no_peserta_sertifikasi"]').value = data.no_peserta_sertifikasi || '';
        form.querySelector('[name="no_sertifikat"]').value = data.no_sertifikat || '';
        form.querySelector('[name="tgl_sertifikat"]').value = data.tgl_sertifikat || '';
        form.querySelector('[name="jenjang_sertifikat"]').value = data.jenjang_sertifikat || '';
        form.querySelector('[name="mapel_sertifikat"]').value = data.mapel_sertifikat || '';
        
        if (form.querySelector('[name="status_kepegawaian"]')) {
            form.querySelector('[name="status_kepegawaian"]').value = data.status_kepegawaian || 'Honorer';
        }
        
        let kamadCheck = form.querySelector('[name="is_kamad"]');
        if (kamadCheck) {
            kamadCheck.checked = (data.is_kamad == 1);
        }
        
        let hid = form.querySelector('[name="id"]');
        if(!hid) {
            hid = document.createElement('input');
            hid.type = 'hidden'; hid.name = 'id';
            form.appendChild(hid);
        }
        hid.value = data.id;

        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function updateBulkGuru() {
        const count = document.querySelectorAll('.check-guru:checked').length;
        document.getElementById('selected-count-guru').innerText = count;
        document.getElementById('bulk-action-bar-guru').style.display = count > 0 ? 'flex' : 'none';
    }

    document.getElementById('check-all-guru')?.addEventListener('change', function() {
        document.querySelectorAll('.check-guru').forEach(cb => cb.checked = this.checked);
        updateBulkGuru();
    });

    function confirmBulkActionGuru(action) {
        const form = document.getElementById('form-bulk-guru');
        if(action === 'delete') {
            ZNS.confirm('Hapus semua guru terpilih?', () => {
                form.action = base_url('siakad/guru/delete-bulk');
                form.target = '_self';
                form.submit();
            });
        } else {
            form.action = base_url('siakad/guru/cetak-kartu-bulk');
            form.target = '_blank';
            form.submit();
        }
    }

    $(document).ready(function() {
        $('#table-data-guru').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                search: "",
                searchPlaceholder: "Cari data guru...",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: { next: '<i data-lucide="chevron-right"></i>', previous: '<i data-lucide="chevron-left"></i>' }
            },
            columnDefs: [{ orderable: false, targets: [0, 1, 5] }],
            drawCallback: () => lucide.createIcons()
        });
    });
</script>


