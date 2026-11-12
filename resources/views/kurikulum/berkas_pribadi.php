<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Berkas Pribadi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
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
                    <div class="header-badge">MANAJEMEN BERKAS</div>
                    <h1><i data-lucide="users"></i> Daftar Berkas Pribadi</h1>
                    <p>Pilih guru untuk melihat dan mengelola dokumen kepegawaian atau sertifikasinya.</p>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="users"></i> Daftar Guru
                </div>
            </div>
            
            <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                <?php if(empty($guru_list)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #94a3b8;">
                        <i data-lucide="users-2" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                        <p>Belum ada data guru aktif.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($guru_list as $g): ?>
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #f8fafc;">
                                <div style="width: 50px; height: 50px; background: #eff6ff; color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                                    <?= strtoupper(substr($g['nama_lengkap'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h3 style="margin: 0; font-size: 1rem; color: #1e293b; font-weight: 700; line-height: 1.3;"><?= htmlspecialchars($g['nama_lengkap']) ?></h3>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                                        <i data-lucide="folder" style="width:12px;height:12px;"></i> Berkas Pegawai
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 1rem; background: #f8fafc; margin-top: auto;">
                                <a href="?guru_id=<?= $g['id'] ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 0.6rem; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; color: #334155; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;">
                                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i> Lihat Berkas
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- AKHIR KONTEN ANDA -->

    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
    
    // Hover effects via JS for better encapsulation
    document.querySelectorAll('a[href^="?guru_id="]').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.background = '#f1f5f9';
            this.style.borderColor = '#94a3b8';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.background = '#fff';
            this.style.borderColor = '#cbd5e1';
        });
    });
</script>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
