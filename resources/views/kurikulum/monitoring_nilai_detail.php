<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Detail Monitoring Nilai - ' . htmlspecialchars($mapel_info['nama_mapel'] ?? ''); include __DIR__ . '/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
                <div>
                    <h1 class="mph-title" style="color: white; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="eye" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Detail Monitoring Nilai
                    </h1>
                    <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                        Pantau progres nilai harian siswa secara rinci per evaluasi.
                    </p>
                </div>
                <a href="<?= base_url('kurikulum/monitoring-nilai') ?>" class="btn" style="color: white; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.18); padding: 8px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="arrow-left" style="color: white; width: 18px; height: 18px;"></i> Kembali
                </a>
            </div>
        </div>

        <div class="z-card" style="padding: 24px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;">
                <!-- Kiri: Info Dasar -->
                <div style="flex: 1; min-width: 320px;">
                    <table style="width: 100%; font-size: 0.95rem; border-collapse: collapse;">
                        <tr><td style="width: 140px; font-weight: bold; color: #64748b; padding: 6px 0;">Mata Pelajaran</td><td style="padding: 6px 0; font-weight: 700; color: #0f172a;">: <?= htmlspecialchars($mapel_info['nama_mapel']) ?></td></tr>
                        <tr><td style="font-weight: bold; color: #64748b; padding: 6px 0;">Kelas</td><td style="padding: 6px 0; font-weight: 700; color: #2563eb;">: <?= htmlspecialchars($kelas['nama_kelas']) ?></td></tr>
                        <tr><td style="font-weight: bold; color: #64748b; padding: 6px 0;">Guru Pengampu</td><td style="padding: 6px 0; font-weight: 600; color: #1e293b;">: <?= htmlspecialchars($mapel_info['guru_pengampu']) ?></td></tr>
                        <tr><td style="font-weight: bold; color: #64748b; padding: 6px 0;">Wali Kelas</td><td style="padding: 6px 0; font-weight: 600; color: #1e293b;">: <?= htmlspecialchars($kelas['wali_kelas']) ?></td></tr>
                        <tr><td style="font-weight: bold; color: #64748b; padding: 6px 0;">KKM Acuan</td><td style="padding: 6px 0;"><span style="background: #ecfdf5; color: #059669; padding: 3px 8px; border-radius: 6px; font-weight: 800; font-size: 0.85rem;">KKM: <?= $kkm ?></span></td></tr>
                    </table>
                </div>

                <!-- Kanan: Materi -->
                <div style="flex: 1; min-width: 320px;">
                    <p style="font-weight: 800; margin-bottom: 10px; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="info" style="width: 16px; height: 16px; color: #0284c7;"></i> Keterangan Materi Pembelajaran:
                    </p>
                    <div style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <table class="z-table" style="width: 100%; font-size: 0.85rem; margin-bottom: 0;">
                            <thead>
                                <tr style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                                    <th style="width: 80px; padding: 8px 12px; font-weight: 800; color: #475569;">Evaluasi</th>
                                    <th style="padding: 8px 12px; font-weight: 800; color: #475569;">Materi Pembelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ph_list as $ph): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="font-weight: 800; padding: 8px 12px; color: #0284c7;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></td>
                                    <td style="padding: 8px 12px; color: #334155; <?= empty($ph_materi[$ph]) ? 'font-style: italic; color: #94a3b8;' : '' ?>">
                                        <?= !empty($ph_materi[$ph]) ? htmlspecialchars($ph_materi[$ph]) : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto; margin-top: 20px;">
                <table class="z-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                            <th style="width: 40px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #475569;">NO</th>
                            <th style="width: 100px; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #475569;">NIS</th>
                            <th style="padding: 10px; font-size: 0.75rem; font-weight: 800; color: #475569;">NAMA SISWA</th>
                            <?php foreach($ph_list as $ph): ?>
                                <th style="width: 80px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #0284c7;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></th>
                            <?php endforeach; ?>
                            <th style="width: 80px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #d97706;">PTS</th>
                            <th style="width: 80px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #16a34a;">PAS</th>
                            <th style="width: 90px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #0f172a;">RATA-RATA</th>
                            <th style="width: 90px; text-align: center; padding: 10px; font-size: 0.75rem; font-weight: 800; color: #0f172a;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswa_list)): ?>
                        <tr><td colspan="<?= count($ph_list) + 7 ?>" style="text-align: center; padding: 30px; color: #64748b;">Belum ada data siswa di kelas ini.</td></tr>
                        <?php else: ?>
                            <?php foreach($siswa_list as $idx => $s): 
                                $s_id = $s['id'];
                                $total_val = 0;
                                $count_val = 0;
                            ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="text-align: center; font-weight: 700; color: #64748b; padding: 10px;"><?= $idx + 1 ?></td>
                                <td style="font-family: monospace; font-weight: 600; padding: 10px;"><?= htmlspecialchars($s['nis'] ?: '-') ?></td>
                                <td style="font-weight: 700; color: #1e293b; padding: 10px;"><?= htmlspecialchars($s['nama']) ?></td>
                                
                                <?php foreach($ph_list as $ph): 
                                    $val = $nilai_grouped[$s_id][$ph] ?? null;
                                    if ($val !== null && is_numeric($val)) {
                                        $total_val += (float)$val;
                                        $count_val++;
                                    }
                                ?>
                                    <td style="text-align: center; padding: 10px; font-weight: 700; <?= ($val !== null && $val < $kkm) ? 'color: #dc2626; background: #fef2f2;' : 'color: #0f172a;' ?>">
                                        <?= $val !== null ? number_format((float)$val, 0) : '<span style="color:#cbd5e1;">-</span>' ?>
                                    </td>
                                <?php endforeach; ?>

                                <?php 
                                    $pts = $nilai_grouped[$s_id]['PTS'] ?? null;
                                    if ($pts !== null && is_numeric($pts)) { $total_val += (float)$pts; $count_val++; }
                                    $pas = $nilai_grouped[$s_id]['PAS'] ?? null;
                                    if ($pas !== null && is_numeric($pas)) { $total_val += (float)$pas; $count_val++; }
                                    $rata = $count_val > 0 ? round($total_val / $count_val, 1) : null;
                                    $is_tuntas = ($rata !== null && $rata >= $kkm);
                                ?>
                                <td style="text-align: center; padding: 10px; font-weight: 700; color: #d97706;">
                                    <?= $pts !== null ? number_format((float)$pts, 0) : '<span style="color:#cbd5e1;">-</span>' ?>
                                </td>
                                <td style="text-align: center; padding: 10px; font-weight: 700; color: #16a34a;">
                                    <?= $pas !== null ? number_format((float)$pas, 0) : '<span style="color:#cbd5e1;">-</span>' ?>
                                </td>
                                <td style="text-align: center; padding: 10px; font-weight: 800; color: #0f172a; background: #f8fafc;">
                                    <?= $rata !== null ? number_format($rata, 1) : '<span style="color:#cbd5e1;">-</span>' ?>
                                </td>
                                <td style="text-align: center; padding: 10px;">
                                    <?php if ($rata === null): ?>
                                        <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                    <?php elseif ($is_tuntas): ?>
                                        <span style="background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 800;">TUNTAS</span>
                                    <?php else: ?>
                                        <span style="background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 800;">REMEDIAL</span>
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
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>

