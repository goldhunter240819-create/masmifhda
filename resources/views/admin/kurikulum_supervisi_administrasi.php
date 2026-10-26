<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        .z-btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            font-size: 12px !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .z-btn-primary:hover { 
            filter: brightness(1.1); 
            box-shadow: 0 4px 6px -1px rgba(14,165,233,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Supervisi Administrasi</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Waka Kurikulum</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="folder-check" style="color: rgba(255,255,255,0.8);"></i> Supervisi Administrasi Guru
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola instrumen kelengkapan perangkat mengajar guru (Silabus, RPP, Prota, Promes, dll).</p>
                    </div>
                </div>

                <div class="z-card" style="margin-top: 2rem;">
                    <div class="z-table-wrap">
                        <table class="z-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Nama Guru</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Nilai</th>
                                    <th style="text-align: center;">Tgl Supervisi</th>
                                    <th style="text-align: right; padding-right: 20px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($guru_list)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada data guru.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($guru_list as $g): ?>
                                    <tr>
                                        <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($g['nama']) ?></td>
                                        <td>
                                            <?php if ($g['supervisi_id']): ?>
                                                <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0fdf4; color: #16a34a; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                                        <i data-lucide="check-circle-2" style="width: 14px; height: 14px;"></i> Selesai
                                                    </span>
                                                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Oleh: <?= htmlspecialchars($g['penilai_nama'] ?? 'Admin') ?></div>
                                                </div>
                                            <?php else: ?>
                                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef2f2; color: #dc2626; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Belum
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if ($g['supervisi_id']): ?>
                                                <span style="font-weight: 700; color: <?= $g['nilai_akhir'] >= 75 ? '#16a34a' : '#f59e0b' ?>; font-size: 1.1rem;"><?= $g['nilai_akhir'] ?></span>
                                            <?php else: ?>
                                                <span style="color: #94a3b8;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center; color: #475569;">
                                            <?= $g['tanggal_supervisi'] ? date('d/m/Y', strtotime($g['tanggal_supervisi'])) : '-' ?>
                                        </td>
                                        <td style="text-align: right; padding-right: 20px;">
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <a href="<?= base_url('admin/kurikulum/supervisi-administrasi/evaluasi?guru_id=' . $g['id']) ?>" class="z-btn-primary">
                                                    <i data-lucide="clipboard-edit" style="width: 14px; height: 14px;"></i> Evaluasi
                                                </a>
                                                <?php if ($g['supervisi_id']): ?>
                                                <a href="<?= base_url('admin/kurikulum/supervisi-administrasi/cetak?guru_id=' . $g['id']) ?>" class="z-btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);" target="_blank">
                                                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak
                                                </a>
                                                <?php endif; ?>
                                            </div>
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
        function zToggleSidebar() {
            document.querySelector('.z-sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
