<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Berkas Pribadi - ' . htmlspecialchars($guru_nama); include __DIR__.'/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- MULAI KONTEN ANDA DI SINI -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">MANAJEMEN BERKAS GURU</div>
                    <h1><i data-lucide="user"></i> Berkas: <?= htmlspecialchars($guru_nama) ?></h1>
                    <p>Kelola dokumen dan berkas pribadi terkait kepegawaian dan sertifikasi milik guru ini.</p>
                </div>
                <div class="header-actions-premium">
                    <a href="/kurikulum/berkas/pribadi" class="e-btn" style="background: rgba(255,255,255,0.2); color: #fff; text-decoration: none; margin-right: 10px;">
                        <i data-lucide="arrow-left"></i> Kembali
                    </a>
                    <button class="e-btn e-btn-primary" onclick="document.getElementById('modalUpload').style.display='flex'">
                        <i data-lucide="upload-cloud"></i> Unggah Berkas
                    </button>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Daftar Berkas
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Berkas</th>
                            <th>Kategori</th>
                            <th>Tanggal Diunggah</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($berkas_list)): ?>
                        <tr>
                            <td colspan="5" align="center" style="padding: 2rem;">
                                <div style="color: var(--z-muted);">
                                    <i data-lucide="inbox" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada berkas pribadi yang diunggah.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php $no=1; foreach($berkas_list as $b): ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><strong style="color: #0f172a;"><?= htmlspecialchars($b['nama_berkas']) ?></strong></td>
                                <td><span style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; color: #475569;"><?= htmlspecialchars($b['kategori'] ?? 'Lainnya') ?></span></td>
                                <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                                <td align="center">
                                    <a href="/public/uploads/guru_berkas/<?= htmlspecialchars($b['file_path']) ?>" target="_blank" style="color: #3b82f6; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; background: #eff6ff; border-radius: 6px;">
                                        <i data-lucide="download" style="width: 14px; height: 14px;"></i> Buka File
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- AKHIR KONTEN ANDA -->

    </div>
</div>

<!-- Modal Unggah Berkas Pribadi -->
<div id="modalUpload" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="upload-cloud" style="color:#10b981;"></i> Unggah Berkas</h3>
            <button onclick="document.getElementById('modalUpload').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <!-- Note: Script upload to be implemented by user based on their setup -->
        <form action="/kurikulum/berkas/pribadi/upload?guru_id=<?= $guru_id ?>" method="POST" enctype="multipart/form-data">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Nama Berkas</label>
                    <input type="text" name="nama_berkas" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Contoh: SK Mengajar 2026" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Keterangan</label>
                    <textarea name="keterangan" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; min-height:80px; font-family:inherit;" placeholder="Tuliskan keterangan singkat..."></textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">File Berkas (PDF/JPG/PNG)</label>
                    <input type="file" name="file_berkas" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" required>
                </div>
            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <button type="button" onclick="document.getElementById('modalUpload').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
