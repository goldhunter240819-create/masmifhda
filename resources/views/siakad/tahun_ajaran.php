<?php
// data variables: $years, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">KONFIGURASI AKADEMIK</div>
                    <h1><i data-lucide="calendar"></i> Manajemen Tahun Ajaran</h1>
                    <p>Kelola daftar tahun pelajaran aktif dan semester madrasah.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="document.getElementById('modal-add-tahun').style.display='flex'">
                        <i data-lucide="plus"></i> Tambah Tahun Ajaran
                    </button>
                </div>
            </div>
        </div>

        <div class="z-panel">
            <div class="z-table-wrap">
                <table class="">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($years as $y): ?>
                        <tr>
                            <td>
                                <div class="year-name"><?php echo $y['name']; ?></div>
                            </td>
                            <td class="text-center">
                                <?php if($y['is_active']): ?>
                                    <span class="z-badge-success">SEDANG DIGUNAKAN</span>
                                <?php else: ?>
                                    <span class="z-badge-muted">ARSIP</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="z-action-btns justify-center">
                                    <?php if(!$y['is_active']): ?>
                                        <a href="<?php echo base_url('siakad/tahun-ajaran/activate/' . $y['id']); ?>" class="btn z-btn-emerald-light z-btn-sm">
                                            <i data-lucide="check-circle"></i> Aktifkan
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.8rem; font-weight: 700;">Status Aktif</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="modal-add-tahun" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3 class="z-modal-title"><i data-lucide="plus-circle"></i> Tambah Tahun Ajaran</h3>
            <button type="button" onclick="document.getElementById('modal-add-tahun').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/tahun-ajaran/add'); ?>" method="POST">
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="z-label">Nama Tahun Ajaran</label>
                    <input type="text" name="name" placeholder="Contoh: 2024/2025" required class="z-field">
                    <p class="help-text">Gunakan format YYYY/YYYY (contoh: 2025/2026)</p>
                </div>
                <div class="z-modal-footer" style="padding: 1rem 0 0 0; background: none; border-top: none;">
                    <button type="submit" class="btn btn-primary z-btn-full">SIMPAN DATA <i data-lucide="save"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* â”€â”€â”€ Layout â”€â”€â”€ */
    
    
    
    

    /* â”€â”€â”€ Table â”€â”€â”€ */
    
    
    
    
    

    .year-name { font-weight: 800; color: #1e293b; font-size: 1.1rem; }

    .z-badge-success { background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 100px; font-size: 0.7rem; font-weight: 800; }
    .z-badge-muted { background: #f1f5f9; color: #64748b; padding: 6px 14px; border-radius: 100px; font-size: 0.7rem; font-weight: 800; }

    /* â”€â”€â”€ Modal â”€â”€â”€ */
    .help-text { font-size: 0.75rem; color: #94a3b8; margin-top: 6px; font-style: italic; }
    .modal-footer-btns { display: flex; gap: 10px; }
    .z-btn-full { width: 100%; justify-content: center; }
</style>

<script>lucide.createIcons();</script>
