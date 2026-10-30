<?php
$pageTitle = 'Berkas Perangkat Pembelajaran';
$active_menu = 'perangkat';
$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$isAdminOrKamad = ($roleId === 1 || $roleId === 99 || !empty($_SESSION['is_kamad']));
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">SUPERVISI & KBM</div>
                        <h1><i data-lucide="book-marked"></i> Berkas Perangkat Pembelajaran</h1>
                        <p>Monitoring dan validasi kelengkapan perangkat ajar digital (Modul Ajar / RPP, ATP, Prota, Promes, KKTP, Silabus) seluruh dewan guru.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php if (!$isAdminOrKamad): ?>
                        <a href="<?php echo base_url('guru/perangkat'); ?>" class="e-btn e-btn-glass">
                            <i data-lucide="plus-circle"></i> Input Perangkat Saya
                        </a>
                        <?php else: ?>
                        <a href="<?php echo base_url('admin/pkkm/cetak-bukti/perangkat-guru'); ?>" target="_blank" class="e-btn e-btn-glass">
                            <i data-lucide="printer"></i> Cetak Bukti Supervisi
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url($baseRoute . '/perangkat'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    
                    <?php if ($isAdminOrKamad): ?>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="label-sm">Filter Guru</label>
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
                        <label class="label-sm">Kategori Perangkat</label>
                        <select name="kategori" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Kategori --</option>
                            <?php 
                            $kats = ['Modul Ajar', 'RPP', 'Alur Tujuan Pembelajaran', 'Capaian Pembelajaran', 'Program Tahunan', 'Program Semester', 'KKTP', 'Silabus', 'Bahan Ajar'];
                            foreach($kats as $k): ?>
                                <option value="<?php echo $k; ?>" <?php echo ($filter_kategori === $k) ? 'selected' : ''; ?>>
                                    <?php echo $k; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 150px;">
                        <label class="label-sm">Status Verifikasi</label>
                        <select name="status" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Status --</option>
                            <option value="Disetujui" <?php echo ($filter_status === 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="Menunggu" <?php echo ($filter_status === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="Perlu Revisi" <?php echo ($filter_status === 'Perlu Revisi') ? 'selected' : ''; ?>>Perlu Revisi</option>
                        </select>
                    </div>

                    <div style="flex: 2; min-width: 220px;">
                        <label class="label-sm">Cari Judul / Mapel</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="q" class="input-sm" placeholder="Ketik kata kunci..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 14px;">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                            </button>
                            <?php if (!empty($search) || !empty($filter_kategori) || !empty($filter_status) || $filter_guru > 0): ?>
                            <a href="<?php echo base_url($baseRoute . '/perangkat'); ?>" class="e-btn e-btn-dark" style="padding: 7px 12px;" title="Reset Filter">
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
                        <span>Daftar Perangkat Pembelajaran</span>
                        <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($perangkatList); ?> Dokumen
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
                                <th>Mata Pelajaran & Kelas</th>
                                <th>Nama Dokumen</th>
                                <th>Status Review</th>
                                <th>Tgl Upload</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($perangkatList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Tidak ada berkas perangkat ditemukan</div>
                                    <div style="font-size: 0.78rem;">Coba sesuaikan filter atau kata kunci pencarian Anda.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($perangkatList as $p): 
                                    $fUrl = (strpos($p['file_path'], 'http') === 0) ? $p['file_path'] : base_url('public/uploads/guru_perangkat/' . $p['file_path']);
                                    $st = $p['status_review'] ?? 'Menunggu';
                                    $stColor = ($st === 'Disetujui') ? '#15803d' : (($st === 'Perlu Revisi') ? '#b91c1c' : '#b45309');
                                    $stBg = ($st === 'Disetujui') ? '#dcfce7' : (($st === 'Perlu Revisi') ? '#fee2e2' : '#fef3c7');
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($p['nama_guru'] ?? 'Guru'); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b;">NIP: <?php echo htmlspecialchars($p['nip_guru'] ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #e0f2fe; color: #0284c7;">
                                            <?php echo htmlspecialchars($p['kategori'] ?? 'Modul Ajar'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.82rem;">
                                            <?php echo htmlspecialchars($p['mapel_nama'] ?? $p['nama_mapel'] ?? '-'); ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            Kelas: <?php echo htmlspecialchars($p['kelas_nama'] ?? $p['nama_kelas'] ?? '-'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.84rem;"><?php echo htmlspecialchars($p['nama_berkas']); ?></div>
                                        <div style="font-size: 0.7rem; color: #94a3b8;"><?php echo htmlspecialchars(basename($p['file_path'])); ?></div>
                                        <?php if (!empty($p['catatan_review'])): ?>
                                        <div style="margin-top: 4px; font-size: 0.72rem; color: #b91c1c; background: #fff1f2; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                            Catatan: <?php echo htmlspecialchars($p['catatan_review']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 6px; background: <?php echo $stBg; ?>; color: <?php echo $stColor; ?>;">
                                            <?php echo htmlspecialchars($st); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.75rem; color: #64748b; white-space: nowrap;">
                                        <?php echo !empty($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '-'; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button type="button" onclick="openPreview('<?php echo $fUrl; ?>', '<?php echo addslashes($p['nama_berkas']); ?>')" class="e-btn e-btn-blue-light" style="padding: 5px 9px; font-size: 0.74rem;" title="Pratinjau File">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Pratinjau
                                        </button>
                                        <a href="<?php echo $fUrl; ?>" target="_blank" class="e-btn e-btn-primary" style="padding: 5px 9px; font-size: 0.74rem;" title="Unduh File">
                                            <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <?php if ($isAdminOrKamad && ($p['source'] ?? '') !== 'siakad'): ?>
                                        <button type="button" onclick="openReviewModal(<?php echo $p['id']; ?>, '<?php echo addslashes($p['nama_berkas']); ?>', '<?php echo addslashes($st); ?>', '<?php echo addslashes($p['catatan_review'] ?? ''); ?>')" class="e-btn e-btn-dark" style="padding: 5px 9px; font-size: 0.74rem;" title="Verifikasi / Beri Catatan">
                                            <i data-lucide="check-square" style="width: 14px; height: 14px;"></i>
                                        </button>
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

<!-- Modal Review Status (Admin/Kamad) -->
<?php if ($isAdminOrKamad): ?>
<div id="reviewModal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 480px; border-radius: 18px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #0284c7;"></i>
                Review Perangkat Guru
            </h3>
            <button type="button" onclick="document.getElementById('reviewModal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('admin/manajemen-berkas/update-status'); ?>">
            <input type="hidden" name="id" id="review_doc_id" value="">

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Nama Berkas</label>
                <div id="review_doc_title" style="font-weight: 700; color: #1e293b; font-size: 0.88rem; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">-</div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Status Verifikasi</label>
                <select name="status_review" id="review_status_select" class="input-sm" required>
                    <option value="Disetujui">Disetujui (Sesuai Standar)</option>
                    <option value="Perlu Revisi">Perlu Revisi (Ada Catatan)</option>
                    <option value="Menunggu">Menunggu Review</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="label-sm">Catatan Review (Opsional)</label>
                <textarea name="catatan_review" id="review_notes" class="input-sm z-textarea" placeholder="Tuliskan catatan perbaikan bila ada..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('reviewModal').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary">
                    Simpan Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

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

    function openReviewModal(id, title, status, notes) {
        document.getElementById('review_doc_id').value = id;
        document.getElementById('review_doc_title').innerText = title;
        document.getElementById('review_status_select').value = status;
        document.getElementById('review_notes').value = notes || '';
        document.getElementById('reviewModal').classList.add('open');
    }
</script>
