<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Monitoring Jadwal Pelajaran'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .schedule-wrapper {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
        .schedule-wrapper::-webkit-scrollbar { height: 10px; }
        .schedule-wrapper::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .schedule-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f8fafc; }
        
        .master-schedule-table {
            width: max-content;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
        }
        .master-schedule-table th, .master-schedule-table td {
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            vertical-align: middle;
        }
        .master-schedule-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 0 #e2e8f0;
        }
        .kelas-header {
            width: 170px;
            min-width: 170px;
            max-width: 170px;
            background: #f1f5f9 !important;
        }
        .kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; }

        .sticky-col-1, .sticky-col-2 {
            position: sticky;
            left: 0;
            background: #f8fafc;
            z-index: 20;
        }
        .sticky-col-2 { left: 40px; border-right: 2px solid #cbd5e1 !important; }
        
        .master-schedule-table thead .sticky-col-1, 
        .master-schedule-table thead .sticky-col-2 {
            z-index: 30;
        }

        .cell-empty {
            background: #fff;
            text-align: center;
            color: #cbd5e1;
        }
        .cell-filled {
            background: #f0fdf4;
            border-left: 3px solid #10b981;
            transition: 0.2s;
            cursor: default;
            width: 170px;
            max-width: 170px;
            overflow: hidden;
        }
        .cell-filled:hover {
            background: #dcfce7;
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            z-index: 5;
            position: relative;
        }
        
        .mapel-name {
            display: block;
            font-weight: 800;
            color: #0f172a;
            font-size: 0.75rem;
            line-height: 1.2;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .guru-name {
            display: block;
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hari-Senin .cell-filled { background: #f0f9ff; border-left-color: #3b82f6; }
        .hari-Selasa .cell-filled { background: #fdf4ff; border-left-color: #d946ef; }
        .hari-Rabu .cell-filled { background: #fffbeb; border-left-color: #f59e0b; }
        .hari-Kamis .cell-filled { background: #f0fdf4; border-left-color: #10b981; }
        .hari-Jumat .cell-filled { background: #ecfeff; border-left-color: #06b6d4; }
        .hari-Sabtu .cell-filled { background: #fff1f2; border-left-color: #f43f5e; }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1 class="mph-title" style="color: white; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="calendar" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Monitoring Jadwal Pelajaran
                    </h1>
                    <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                        Pantau alokasi jadwal pelajaran harian per rombel kelas MA.
                    </p>
                </div>
            </div>
        </div>

        <?php foreach($hari_list as $h): ?>
        <div style="background: #0284c7; padding: 12px 20px; border-radius: 12px 12px 0 0; color: white; font-weight: 800; font-size: 1.05rem; text-transform: uppercase; margin-top: 1.75rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="calendar" style="width: 18px; height: 18px;"></i> HARI <?php echo strtoupper($h); ?>
        </div>
        <div class="schedule-wrapper hari-<?php echo $h; ?>" style="border-radius: 0 0 12px 12px; margin-bottom: 0;">
            <table class="master-schedule-table">
                <thead>
                    <tr>
                        <th class="sticky-col-1" style="width:40px;">NO</th>
                        <th class="sticky-col-2" style="width:120px;">WAKTU</th>
                        <?php foreach($kelas as $k): ?>
                            <th class="kelas-header">
                                <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $found_jam = false;
                    foreach($jam_list as $jam): 
                        $has_any = false;
                        foreach($kelas as $k) {
                            if (!empty($jadwal_master[$h][$jam][$k['id']])) {
                                $has_any = true;
                                break;
                            }
                        }
                        if (!$has_any) continue;
                        $found_jam = true;
                    ?>
                    <tr>
                        <td class="sticky-col-1" style="text-align:center; font-weight:700; color:#64748b;"><?php echo $no++; ?></td>
                        <td class="sticky-col-2" style="text-align:center; font-weight:700; font-size:0.75rem; color:#0f172a; white-space:nowrap;">
                            <?php echo $jam; ?>
                        </td>
                        <?php foreach($kelas as $k): 
                            $item = $jadwal_master[$h][$jam][$k['id']] ?? null;
                        ?>
                            <?php if ($item): ?>
                                <td class="cell-filled" title="<?php echo htmlspecialchars($item['nama_mapel'] . ' - ' . ($item['nama_guru'] ?? '')); ?>">
                                    <span class="mapel-name"><?php echo htmlspecialchars($item['nama_mapel']); ?></span>
                                    <span class="guru-name"><?php echo htmlspecialchars($item['nama_guru'] ?? '-'); ?></span>
                                </td>
                            <?php else: ?>
                                <td class="cell-empty">-</td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (!$found_jam): ?>
                    <tr>
                        <td colspan="<?php echo count($kelas) + 2; ?>" style="text-align:center; padding: 2.5rem; color:#94a3b8; font-weight:600;">
                            Tidak ada jadwal pelajaran di hari <?php echo $h; ?>.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>

    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>

