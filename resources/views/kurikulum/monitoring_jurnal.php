<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Monitoring Jurnal Mengajar'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);">
            <div>
                <h1 class="mph-title" style="color: white; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                    <i data-lucide="book-check" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Monitoring Jurnal Mengajar
                </h1>
                <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                    Memastikan guru disiplin mengisi absensi kelas dan materi setelah mengajar.
                </p>
            </div>
        </div>

        <!-- Filter Box -->
        <div style="background: #fff; padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <form action="" method="GET" style="display: flex; align-items: flex-end; gap: 15px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 140px;">
                    <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Dari Tanggal:</label>
                    <input type="date" name="tanggal_mulai" class="z-input" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>" style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 10px;">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Sampai Tanggal:</label>
                    <input type="date" name="tanggal_akhir" class="z-input" value="<?php echo htmlspecialchars($filter_tanggal_akhir); ?>" style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 10px;">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Kelas:</label>
                    <select name="kelas_id" class="z-input" style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 10px;">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $filter_kelas == $k['id'] ? 'selected' : ''; ?>>
                                Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 160px;">
                    <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Guru:</label>
                    <select name="guru_id" class="z-input" style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 10px;">
                        <option value="">Semua Guru</option>
                        <?php foreach($guruList as $g): ?>
                            <option value="<?php echo $g['id']; ?>" <?php echo (isset($filter_guru) && $filter_guru == $g['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 160px;">
                    <label style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 6px;">Filter Mapel:</label>
                    <select name="mapel_id" class="z-input" style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 10px;">
                        <option value="">Semua Mapel</option>
                        <?php foreach($mapelList as $m): ?>
                            <option value="<?php echo $m['id']; ?>" <?php echo (isset($filter_mapel) && $filter_mapel == $m['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['nama_mapel']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="height: 38px; padding: 0 20px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; color: white; background: #0284c7; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3); display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="filter" style="width:16px; height:16px;"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
            <div class="z-table-wrap">
                <table class="z-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="width: 110px; padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Tanggal</th>
                            <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Guru & Pelajaran</th>
                            <th style="padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Materi Pembelajaran</th>
                            <th style="width: 260px; padding: 12px 18px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Statistik Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jurnalList)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color: #64748b; padding: 2.5rem; font-weight: 500;">Belum ada log jurnal mengajar guru.</td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach($jurnalList as $j): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="font-family: monospace; font-weight: 700; vertical-align: top; padding: 1rem 18px; color: #1e293b;">
                                <?php echo date('d/m/Y', strtotime($j['tanggal'])); ?>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 3px;">
                                    <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                    <span><?php echo !empty($j['created_at']) ? date('H:i', strtotime($j['created_at'])) . ' WIB' : '-'; ?></span>
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 1rem 18px;">
                                <div style="font-weight: 800; color: #0284c7; margin-bottom: 4px;"><?php echo htmlspecialchars($j['nama_guru']); ?></div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($j['nama_mapel']); ?></div>
                                <div style="margin-top: 6px;">
                                    <span style="display: inline-block; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">
                                        Kelas <?php echo htmlspecialchars($j['nama_kelas']); ?>
                                    </span>
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 1rem 18px; font-size: 0.85rem; color: #334155;">
                                <div style="line-height: 1.5; font-weight: 600;"><?php echo nl2br(htmlspecialchars($j['materi'])); ?></div>
                                <?php if (!empty($j['keterangan'])): ?>
                                    <div style="background: #fef3c7; padding: 8px 10px; border-radius: 6px; margin-top: 8px; border-left: 3px solid #f59e0b; font-size: 0.8rem; color: #78350f; font-style: italic;">
                                        <?php echo nl2br(htmlspecialchars($j['keterangan'])); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: top; padding: 1rem 18px;">
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <span style="background: #dcfce7; color: #16a34a; padding: 3px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;" title="Hadir">H: <?php echo $j['hadir']; ?></span>
                                    <span style="background: #fef3c7; color: #d97706; padding: 3px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;" title="Sakit">S: <?php echo $j['sakit']; ?></span>
                                    <span style="background: #dbeafe; color: #1d4ed8; padding: 3px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;" title="Izin">I: <?php echo $j['izin']; ?></span>
                                    <span style="background: #fee2e2; color: #dc2626; padding: 3px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;" title="Alpha">A: <?php echo $j['alpha']; ?></span>
                                </div>
                                <?php if (!empty($j['absent_list'])): ?>
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; font-size: 0.75rem;">
                                        <div style="color: #64748b; font-weight: 700; margin-bottom: 4px; font-size: 0.7rem;">Tidak Hadir:</div>
                                        <ul style="margin: 0; padding-left: 15px; color: #334155;">
                                            <?php foreach ($j['absent_list'] as $ab): 
                                                $col = $ab['status'] == 'Sakit' ? '#d97706' : ($ab['status'] == 'Izin' ? '#1d4ed8' : '#dc2626');
                                            ?>
                                                <li style="margin-bottom: 2px;">
                                                    <b><?php echo htmlspecialchars($ab['nama']); ?></b> 
                                                    <span style="color: <?php echo $col; ?>; font-weight: 700; font-size: 0.7rem;">(<?php echo in_array($ab['status'], ['Alpha', 'Bolos']) ? 'Alpa' : $ab['status']; ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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

