<?php
$pageTitle = 'Berkas Pribadi Guru';
$active_menu = 'pribadi';
$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$isAdminOrKamad = ($roleId === 1 || $roleId === 99 || !empty($_SESSION['is_kamad']));
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'uploaded'): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Berkas dokumen berhasil diunggah!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Berkas dokumen telah berhasil dihapus.
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">PORTOFOLIO & ARSIP GURU</div>
                        <h1><i data-lucide="file-badge"></i> Berkas Pribadi & Portofolio</h1>
                        <p>Pusat penyimpanan arsip kepegawaian (SK Mengajar, Sertifikat Pendidik, Ijazah, KTP, NUPTK) dan portofolio dewan guru.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.add('open')" class="e-btn e-btn-glass">
                            <i data-lucide="upload-cloud"></i> Upload Berkas Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url($baseRoute . '/pribadi'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    
                    <?php if ($isAdminOrKamad): ?>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="label-sm">Filter Dewan Guru</label>
                        <select name="guru_id" class="input-sm" onchange="this.form.submit()">
                            <option value="0">-- Semua Dewan Guru --</option>
                            <?php foreach($guruList as $g): ?>
                                <option value="<?php echo $g['id']; ?>" <?php echo ($filter_guru == $g['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div style="flex: 1; min-width: 180px;">
                        <label class="label-sm">Kategori Berkas</label>
                        <select name="kategori" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Kategori --</option>
                            <?php 
                            $kats = ['SK Kepegawaian', 'Sertifikat', 'Ijazah', 'Dokumen Pribadi', 'Portofolio', 'Umum'];
                            foreach($kats as $k): ?>
                                <option value="<?php echo $k; ?>" <?php echo ($filter_kategori === $k) ? 'selected' : ''; ?>>
                                    <?php echo $k; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 2; min-width: 220px;">
                        <label class="label-sm">Cari Nama Berkas / Guru</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="q" class="input-sm" placeholder="Ketik kata kunci dokumen..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 14px; background: #059669;">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                            </button>
                            <?php if (!empty($search) || !empty($filter_kategori) || $filter_guru > 0): ?>
                            <a href="<?php echo base_url($baseRoute . '/pribadi'); ?>" class="e-btn e-btn-dark" style="padding: 7px 12px;" title="Reset Filter">
                                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; font-size: 0.92rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Berkas Pribadi & Portofolio</span>
                        <span style="font-size: 0.75rem; background: #ecfdf5; color: #047857; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($berkasPribadiList); ?> Berkas
                        </span>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Dewan Guru</th>
                                <th>Kategori</th>
                                <th>Nama Berkas</th>
                                <th>Status Kepegawaian</th>
                                <th>Tgl Upload</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($berkasPribadiList)): ?>
                            <tr>
                                <td colspan="7" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada berkas pribadi ditemukan</div>
                                    <div style="font-size: 0.78rem;">Silakan upload berkas baru menggunakan tombol di atas.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($berkasPribadiList as $bp): 
                                    $bpFileUrl = base_url('public/uploads/guru_berkas/' . $bp['file_path']);
                                    $isMine = ($bp['guru_id'] == ($currentGuruId ?? 0));
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($bp['nama_guru'] ?? 'Guru'); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b;">NIP: <?php echo htmlspecialchars($bp['nip_guru'] ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #ecfdf5; color: #047857;">
                                            <?php echo htmlspecialchars($bp['kategori'] ?? 'Umum'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.84rem;"><?php echo htmlspecialchars($bp['nama_berkas']); ?></div>
                                        <div style="font-size: 0.7rem; color: #94a3b8;"><?php echo htmlspecialchars(basename($bp['file_path'])); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #f1f5f9; color: #475569;">
                                            <?php echo htmlspecialchars($bp['status_kepegawaian'] ?: 'Guru'); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.75rem; color: #64748b; white-space: nowrap;">
                                        <?php echo !empty($bp['created_at']) ? date('d M Y, H:i', strtotime($bp['created_at'])) . ' WIB' : '-'; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button type="button" onclick="openPreview('<?php echo $bpFileUrl; ?>', '<?php echo addslashes($bp['nama_berkas']); ?>')" class="e-btn e-btn-blue-light" style="padding: 5px 9px; font-size: 0.74rem;" title="Pratinjau File">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Pratinjau
                                        </button>
                                        <a href="<?php echo $bpFileUrl; ?>" target="_blank" class="e-btn e-btn-primary" style="padding: 5px 9px; font-size: 0.74rem; background: #059669;" title="Unduh File">
                                            <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <?php if ($isAdminOrKamad || $isMine): ?>
                                        <form method="POST" action="<?php echo base_url($baseRoute . '/delete-pribadi'); ?>" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas ini?')">
                                            <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 5px 9px; font-size: 0.74rem; background: #fee2e2; color: #b91c1c; border-color: #fecaca;" title="Hapus Dokumen">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
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

<!-- Modal Upload Berkas Pribadi -->
<div id="modalUploadPribadi" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="upload-cloud" style="width: 20px; height: 20px; color: #059669;"></i>
                Upload Berkas Pribadi
            </h3>
            <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url($baseRoute . '/upload-pribadi'); ?>" enctype="multipart/form-data">
            
            <?php if ($isAdminOrKamad): ?>
            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Dewan Guru Target <span style="color:#ef4444;">*</span></label>
                <select name="guru_id" class="input-sm" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($guruList as $g): ?>
                        <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Nama Berkas / Dokumen <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nama_berkas" class="input-sm" placeholder="Contoh: SK Mengajar 2026/2027 atau Sertifikat Pendidik" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Kategori Berkas</label>
                <select name="kategori" class="input-sm">
                    <option value="SK Kepegawaian">SK Kepegawaian / Mengajar</option>
                    <option value="Sertifikat">Sertifikat / Piagam Pelatihan</option>
                    <option value="Ijazah">Ijazah & Transkrip Nilai</option>
                    <option value="Dokumen Pribadi">Dokumen Pribadi (KTP, NUPTK, KK)</option>
                    <option value="Portofolio">Portofolio & Karya Ilmiah</option>
                    <option value="Umum">Lainnya / Umum</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="label-sm">File Dokumen (PDF / Word / Gambar / Excel) <span style="color:#ef4444;">*</span></label>
                <input type="file" name="berkas_file" class="input-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" required>
                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px;">Maksimal 10MB. Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #059669;">
                    <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Upload Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal PDF / Image Viewer -->
<div id="previewModal" class="modal-overlay" onclick="closePreview(event)">
    <div style="background: #ffffff; width: 92%; max-width: 950px; height: 85vh; border-radius: 20px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);">
        <div style="padding: 14px 20px; background: #0f172a; color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-text" style="width: 18px; height: 18px; color: #38bdf8;"></i>
                <span id="previewModalTitle">Pratinjau Dokumen</span>
            </div>
            <button type="button" onclick="document.getElementById('previewModal').classList.remove('open')" style="background: none; border: none; color: #cbd5e1; cursor: pointer; display: flex; align-items: center; padding: 4px; border-radius: 6px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div style="flex: 1; background: #f1f5f9;">
            <iframe id="previewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    function openPreview(url, title) {
        document.getElementById('previewModalTitle').innerText = title || 'Pratinjau Dokumen';
        document.getElementById('previewIframe').src = url;
        document.getElementById('previewModal').classList.add('open');
    }

    function closePreview(e) {
        if (e.target.id === 'previewModal') {
            document.getElementById('previewModal').classList.remove('open');
            document.getElementById('previewIframe').src = '';
        }
    }
</script>
