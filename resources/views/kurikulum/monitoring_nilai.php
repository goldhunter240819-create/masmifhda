<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Monitoring Nilai Mapel'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
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
                        <i data-lucide="bar-chart-2" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Monitoring Nilai Mapel
                    </h1>
                    <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                        Pantau progres pengisian nilai oleh guru untuk setiap mata pelajaran.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="<?= base_url('kurikulum/syarat-pas') ?>" class="btn" style="color: white; border: 1px solid rgba(255,255,255,0.4); background: rgba(16, 185, 129, 0.35); padding: 8px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="shield-check" style="color: #6ee7b7; width: 18px; height: 18px;"></i> Kelayakan Ikut PAS (100%)
                    </a>
                    <a href="<?= base_url('kurikulum/monitoring-nilai/global') ?>" target="_blank" class="btn" style="color: white; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.18); padding: 8px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="globe" style="color: white; width: 18px; height: 18px;"></i> Detail Global
                    </a>
                </div>
            </div>
        </div>

        <div class="z-card" style="margin-bottom: 25px; padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Guru Pengampu</label>
                    <select name="guru_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Semua Guru --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $guru_id == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Kelas</label>
                    <select name="kelas_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach($kelas_list as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="z-btn z-btn-primary" style="padding: 10px 22px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; background: #0284c7; color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <?php if(!$kelas_id && !$guru_id): ?>
        <div class="z-card" style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0;">
            <div style="width: 70px; height: 70px; background: rgba(14, 165, 233, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <i data-lucide="search" style="width: 34px; height: 34px; color: #0284c7;"></i>
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">Pilih Filter</h3>
            <p style="font-size: 0.9rem; color: #64748b; margin: 0; max-width: 400px; margin: 0 auto;">Silakan pilih Guru atau Kelas dari filter di atas untuk melihat progres pengisian nilai harian mata pelajaran.</p>
        </div>
        <?php else: ?>

        <div class="z-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="list-checks" style="color: #0284c7; width: 20px;"></i> Progres Nilai Mapel
                </h3>
            </div>

            <div style="overflow-x: auto;">
                <table class="z-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="width: 50px; text-align: center; padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">No</th>
                            <th style="padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Kelas</th>
                            <th style="padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Mata Pelajaran</th>
                            <th style="padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Guru Pengampu</th>
                            <th style="padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Progres Evaluasi (Nilai Masuk)</th>
                            <th style="width: 160px; text-align: center; padding: 12px 14px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($monitoring_data)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">Tidak ada data mata pelajaran yang ditemukan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($monitoring_data as $idx => $row): 
                                $total_siswa = $row['total_siswa'];
                            ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="text-align: center; font-weight: 700; color: #64748b; padding: 12px 14px;"><?= $idx + 1 ?></td>
                                <td style="padding: 12px 14px;">
                                    <span style="display: inline-block; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 3px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 800;">
                                        <?= htmlspecialchars($row['kelas']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 700; color: #1e293b; padding: 12px 14px;"><?= htmlspecialchars($row['mapel']) ?></td>
                                <td style="padding: 12px 14px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 28px; height: 28px; background: rgba(2, 132, 199, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0284c7; font-weight: 800; font-size: 0.75rem;">
                                            <?= substr(strtoupper($row['guru'] ?: '?'), 0, 1) ?>
                                        </div>
                                        <span style="font-size: 0.9rem; font-weight: 600; color: #334155;"><?= htmlspecialchars($row['guru'] ?: 'Belum diatur') ?></span>
                                    </div>
                                </td>
                                <td style="padding: 12px 14px;">
                                    <?php if(empty($row['evaluasi'])): ?>
                                        <span style="font-size: 0.8rem; color: #ef4444; background: #fef2f2; padding: 4px 10px; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <i data-lucide="x-circle" style="width: 14px; height: 14px;"></i> Belum ada nilai
                                        </span>
                                    <?php else: ?>
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                            <?php foreach($row['evaluasi'] as $ev): 
                                                $percent = $total_siswa > 0 ? round(($ev['total_dinilai'] / $total_siswa) * 100) : 0;
                                                if ($percent > 100) $percent = 100;
                                                $isComplete = $ev['total_dinilai'] >= $total_siswa && $total_siswa > 0;
                                                $icon = $isComplete ? 'check-circle' : 'clock';
                                            ?>
                                                <div style="background: <?= $isComplete ? '#ecfdf5' : '#fffbeb' ?>; border: 1px solid <?= $isComplete ? '#a7f3d0' : '#fde68a' ?>; padding: 8px 12px; border-radius: 12px; display: inline-flex; flex-direction: column; gap: 6px; min-width: 140px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; font-weight: 800; color: <?= $isComplete ? '#047857' : '#b45309' ?>;">
                                                        <span><?= htmlspecialchars($ev['jenis']) ?></span>
                                                        <i data-lucide="<?= $icon ?>" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div style="font-size: 0.65rem; color: #64748b; line-height: 1.2; font-weight: 500; margin-top: -3px; word-break: break-word; font-style: italic;">
                                                        <?= !empty($ev['materi']) ? htmlspecialchars($ev['materi']) : '-' ?>
                                                    </div>
                                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: <?= $isComplete ? '#059669' : '#d97706' ?>; font-weight: 700;">
                                                        <span><?= $ev['total_dinilai'] ?> / <?= $total_siswa ?> Siswa</span>
                                                        <span style="background: <?= $isComplete ? '#d1fae5' : '#fef3c7' ?>; padding: 2px 6px; border-radius: 6px;"><?= $percent ?>%</span>
                                                    </div>
                                                    <div style="width: 100%; background: <?= $isComplete ? '#d1fae5' : '#fef3c7' ?>; height: 5px; border-radius: 4px; overflow: hidden; margin-top: 2px;">
                                                        <div style="height: 100%; background: <?= $isComplete ? '#10b981' : '#f59e0b' ?>; width: <?= $percent ?>%; transition: width 0.3s ease;"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle; padding: 12px 14px;">
                                    <div style="display: flex; gap: 8px; justify-content: center; flex-direction: column;">
                                        <a href="<?= base_url('kurikulum/monitoring-nilai/detail?kelas_id=' . $row['kelas_id'] . '&mapel_id=' . $row['mapel_id']) ?>" 
                                           class="z-btn" 
                                           style="background: #eff6ff; color: #2563eb; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; justify-content: center; align-items: center; gap: 5px; border: 1px solid #bfdbfe;">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat Detail
                                        </a>
                                        <a href="<?= base_url('kurikulum/monitoring-nilai/cetak?kelas_id=' . $row['kelas_id'] . '&mapel_id=' . $row['mapel_id']) ?>" 
                                           class="z-btn" target="_blank"
                                           style="background: #f0fdf4; color: #16a34a; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; text-decoration: none; display: inline-flex; justify-content: center; align-items: center; gap: 5px; border: 1px solid #bbf7d0;">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Blangko
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>

