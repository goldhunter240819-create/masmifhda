<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$current_label = 'Waka Kurikulum';
?>
<?php include __DIR__ . '/_zamrud_head.php'; ?>
<style>
.mph-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.mph-subtitle {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}
.e-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.5rem 1rem;
    background: var(--z-primary);
    color: white;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.e-btn:hover {
    background: var(--z-primary-h);
    transform: translateY(-1px);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.status-badge.belum {
    background: #fef2f2;
    color: #ef4444;
}
.status-badge.sudah {
    background: #ecfdf5;
    color: #10b981;
}
</style>
</head>
<body style="background: var(--z-bg); margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-title">Supervisi Kelas</div>
                <div class="z-header-right">
                    <div style="text-align: right; line-height: 1.2;">
                        <div style="font-weight: 700; font-size: 0.9rem; color: var(--z-text);"><?php echo htmlspecialchars($adminNama); ?></div>
                        <div style="font-size: 0.75rem; color: var(--z-muted);"><?php echo $current_label; ?></div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-content">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="monitor-play" style="color: rgba(255,255,255,0.8);"></i> Supervisi Kelas
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Instrumen penilaian pedagogik, kepribadian, dan sosial guru di dalam kelas (Pendahuluan, Inti, Penutup).</p>
                    </div>
                </div>

                <div class="z-card">
                    <div class="z-table-wrap">
                        <table class="z-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 50px;">NO</th>
                                    <th>NAMA GURU</th>
                                    <th style="text-align: center;">STATUS</th>
                                    <th>SUPERVISOR</th>
                                    <th style="text-align: center;">TGL SUPERVISI</th>
                                    <th style="text-align: right; padding-right: 20px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($guru_list)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px; color: var(--z-muted);">Tidak ada data guru.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($guru_list as $g): ?>
                                    <tr>
                                        <td style="text-align: center; color: var(--z-muted); font-size: 0.85rem;"><?= $no++ ?></td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--z-text);"><?= htmlspecialchars($g['nama']) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--z-muted);"><?= htmlspecialchars($g['nip'] ?: '-') ?></div>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if ($g['supervisi_id']): ?>
                                                <div class="status-badge sudah">
                                                    <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Sudah
                                                </div>
                                            <?php else: ?>
                                                <div class="status-badge belum">
                                                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Belum
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($g['supervisi_id'] && !empty($g['penilai_nama'])): ?>
                                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--z-text);">
                                                    <i data-lucide="user-check" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                                                    <?= htmlspecialchars($g['penilai_nama']) ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: var(--z-muted); font-size: 0.85rem;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center; color: var(--z-muted); font-size: 0.85rem;">
                                            <?= $g['tanggal_supervisi'] ? date('d/m/Y', strtotime($g['tanggal_supervisi'])) : '-' ?>
                                        </td>
                                        <td style="text-align: right; padding-right: 20px; white-space: nowrap;">
                                            <?php if ($g['supervisi_id']): ?>
                                                <a href="<?= base_url('admin/kurikulum/supervisi-kelas/cetak?guru_id=' . $g['id']) ?>" target="_blank" class="e-btn e-btn-ghost" style="margin-right: 5px;" title="Cetak Hasil">
                                                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('admin/kurikulum/supervisi-kelas/evaluasi?guru_id=' . $g['id']) ?>" class="e-btn">
                                                <i data-lucide="clipboard-edit" style="width: 14px; height: 14px;"></i> Evaluasi
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
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
