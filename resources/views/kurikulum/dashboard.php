<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Dashboard Kurikulum'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .stat-card-kurikulum {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card-kurikulum:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-sub {
            font-size: 1.05rem;
            color: #94a3b8;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- HEADER DASHBOARD KURIKULUM -->
        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2); position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1 class="mph-title" style="color: #ffffff; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="layout-dashboard" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Dashboard Kurikulum
                    </h1>
                    <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                        Pantau pelaksanaan KBM, kelengkapan administrasi guru, dan instrumen penilaian.
                    </p>
                </div>
                <div>
                    <span style="background: rgba(255,255,255,0.2); color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.3);">
                        MODUL UTAMA
                    </span>
                </div>
            </div>
        </div>

        <!-- 3 STAT CARDS -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- CARD 1: TOTAL GURU -->
            <div class="stat-card-kurikulum" onclick="location.href='<?= base_url('kurikulum/supervisi/administrasi') ?>'">
                <div class="stat-icon-wrap" style="background: #eff6ff; color: #3b82f6;">
                    <i data-lucide="users" style="width: 26px; height: 26px;"></i>
                </div>
                <div>
                    <div class="stat-label">Total Guru</div>
                    <div class="stat-value"><?= number_format($statTotalGuru) ?></div>
                </div>
            </div>

            <!-- CARD 2: SUPERVISI ADMINISTRASI -->
            <div class="stat-card-kurikulum" onclick="location.href='<?= base_url('kurikulum/supervisi/administrasi') ?>'">
                <div class="stat-icon-wrap" style="background: #f0fdf4; color: #22c55e;">
                    <i data-lucide="folder-check" style="width: 26px; height: 26px;"></i>
                </div>
                <div>
                    <div class="stat-label">Supervisi Administrasi</div>
                    <div class="stat-value">
                        <?= number_format($statAdminLengkap) ?> 
                        <span class="stat-sub">/ <?= number_format($statTotalGuru) ?></span>
                    </div>
                </div>
            </div>

            <!-- CARD 3: SUPERVISI KELAS -->
            <div class="stat-card-kurikulum" onclick="location.href='<?= base_url('kurikulum/supervisi/kelas') ?>'">
                <div class="stat-icon-wrap" style="background: #fef2f2; color: #ef4444;">
                    <i data-lucide="monitor-play" style="width: 26px; height: 26px;"></i>
                </div>
                <div>
                    <div class="stat-label">Supervisi Kelas</div>
                    <div class="stat-value">
                        <?= number_format($statSupervisiKelas) ?> 
                        <span class="stat-sub">/ <?= number_format($statTotalGuru) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT SUPERVISI KELAS -->
        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
            <div class="z-panel-head" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <div class="z-panel-title" style="font-weight: 800; font-size: 1.05rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="clock" style="color: #0ea5e9; width: 18px; height: 18px;"></i> Supervisi Kelas Terbaru
                </div>
                <a href="<?= base_url('kurikulum/supervisi/kelas') ?>" class="z-btn" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                    Lihat Semua
                </a>
            </div>
            <div class="z-panel-body" style="padding: 0;">
                <div class="z-table-wrap">
                    <table class="z-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Tanggal</th>
                                <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Nama Guru</th>
                                <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Mata Pelajaran</th>
                                <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSupervisi)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2.5rem; color: #64748b; font-weight: 500;">
                                    Belum ada data supervisi kelas.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($recentSupervisi as $rs): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 18px;">
                                        <div style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                            <?= date('d M Y', strtotime($rs['tanggal_supervisi'])) ?>
                                        </div>
                                    </td>
                                    <td style="padding: 12px 18px; font-weight: 700; color: #1e293b;">
                                        <?= htmlspecialchars($rs['nama_guru']) ?>
                                    </td>
                                    <td style="padding: 12px 18px; color: #475569; font-weight: 500;">
                                        <?= htmlspecialchars($rs['mata_pelajaran']) ?>
                                    </td>
                                    <td style="padding: 12px 18px;">
                                        <span style="display: inline-block; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                            <?= htmlspecialchars($rs['kelas']) ?>
                                        </span>
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
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
