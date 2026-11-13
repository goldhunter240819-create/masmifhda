<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Global Monitoring Nilai</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= \App\Core\Helper::url('/uploads/logo/' . ($institusi['logo'] ?? '')) ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --border: #e2e8f0;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header-title h1 { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .header-title p { color: #bfdbfe; font-size: 14px; }
        
        .btn-print {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.4);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-print:hover {
            background: rgba(255,255,255,0.3);
        }

        .section {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
        }

        .section-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary-dark);
            border-bottom: 2px solid var(--border);
            padding-bottom: 10px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            border: 1px solid var(--border);
            padding: 8px;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
        }
        
        .th-ph { background-color: #fef08a !important; color: #854d0e; }
        .th-pts { background-color: #fed7aa !important; color: #9a3412; }
        .th-pas { background-color: #fecaca !important; color: #991b1b; }
        .th-rapor { background-color: #bfdbfe !important; color: #1e40af; }
        .th-mapel {
            background-color: #e2e8f0;
            color: var(--text-main);
            border-left: 3px solid #94a3b8;
            border-right: 3px solid #94a3b8;
        }
        
        .td-mapel-border-left { border-left: 3px solid #94a3b8 !important; }
        .td-mapel-border-right { border-right: 3px solid #94a3b8 !important; }
        
        .td-text { text-align: left; }
        .text-red { color: var(--danger); font-weight: bold; }

        tr:nth-child(even) { background-color: #f8fafc; }
        tr:hover { background-color: #f1f5f9; }
        
        .sticky-col {
            position: sticky;
            z-index: 10;
        }
        th.sticky-col {
            z-index: 20;
            background-color: #f1f5f9;
        }
        td.sticky-col {
            background-color: var(--bg-card);
        }
        tr:nth-child(even) td.sticky-col { background-color: #f8fafc; }
        tr:hover td.sticky-col { background-color: #f1f5f9; }
        
        td.sticky-nama { background-color: #f0fdf4 !important; color: #166534; }
        tr:nth-child(even) td.sticky-nama { background-color: #dcfce7 !important; }
        tr:hover td.sticky-nama { background-color: #bbf7d0 !important; }
        
        .bg-kosong { background-color: #fee2e2 !important; color: #ef4444 !important; font-weight: bold; }
        
        .sticky-no { left: 0; width: 40px; min-width: 40px; max-width: 40px; }
        .sticky-nis { left: 40px; width: 100px; min-width: 100px; max-width: 100px; }
        .sticky-nama { 
            left: 140px; 
            width: 250px; min-width: 250px; max-width: 250px; 
            border-right: 2px solid #94a3b8 !important; 
            white-space: normal !important; 
            word-break: break-word;
        }
        
        @media print {
            body { background: white; padding: 0; }
            .header { border-radius: 0; box-shadow: none; margin-bottom: 20px; }
            .btn-print { display: none; }
            .section { box-shadow: none; border: none; padding: 0; margin-bottom: 40px; page-break-after: always; }
            .section:last-child { page-break-after: auto; }
            th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }
            .th-mapel { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">
            <h1><i class="fa-solid fa-globe"></i> Detail Global Monitoring Nilai</h1>
            <p>Tahun Ajaran <?= htmlspecialchars($activeYear['name'] ?? '-') ?> | Semester <?= htmlspecialchars($semester) ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="/kurikulum/syarat-pas" target="_blank" class="btn-print" style="background: rgba(16, 185, 129, 0.9); border: 1px solid rgba(255, 255, 255, 0.4);">
                <i class="fa-solid fa-shield-halved"></i> Kelayakan Peserta PAS
            </a>
            <button class="btn-print" onclick="window.close()" style="background: rgba(239, 68, 68, 0.9); border: 1px solid rgba(255, 255, 255, 0.4);">
                <i class="fa-solid fa-xmark"></i> Tutup Tab
            </button>
        </div>
    </div>

    <?php 
    $bobotHarian = (float)($institusi['bobot_harian'] ?? 50) / 100;
    $bobotPas = (float)($institusi['bobot_pas'] ?? 50) / 100;

    foreach ($kelas_list as $kls): 
        $kls_id = $kls['id'];
        $tingkat = (int) substr($kls['nama_kelas'], 0, 1);
        if (!in_array($tingkat, [7, 8, 9])) $tingkat = 7;
        
        $mapels = $mapel_kelas[$kls_id] ?? [];
        if (empty($mapels)) continue; // Skip jika tidak ada mapel

        $siswas = $siswa_kelas[$kls_id] ?? [];
        if (empty($siswas)) continue; // Skip jika tidak ada siswa
    ?>
    <div class="section">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Kelas: <?= htmlspecialchars($kls['nama_kelas']) ?></span>
            <a href="/kurikulum/syarat-pas?kelas_id=<?= $kls_id ?>" target="_blank" style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 600;">
                <i class="fa-solid fa-shield-halved"></i> Verifikasi Syarat PAS Kelas Ini &rarr;
            </a>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-col sticky-no">No</th>
                        <th rowspan="2" class="sticky-col sticky-nis">NIS</th>
                        <th rowspan="2" class="sticky-col sticky-nama">Nama Siswa</th>
                        <?php foreach ($mapels as $mp): ?>
                            <?php 
                                $max_ph = $ph_counts[$kls_id . '_' . $mp['id']] ?? 1; 
                                $colspan = $max_ph + 3; // PH + PTS + PAS + RAPOR
                            ?>
                            <th colspan="<?= $colspan ?>" class="th-mapel"><?= htmlspecialchars($mp['nama']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($mapels as $mp): ?>
                            <?php $max_ph = $ph_counts[$kls_id . '_' . $mp['id']] ?? 1; ?>
                            <?php for ($i = 1; $i <= $max_ph; $i++): ?>
                                <th class="th-ph <?= ($i === 1) ? 'td-mapel-border-left' : '' ?>">PH <?= $i ?></th>
                            <?php endfor; ?>
                            <th class="th-pts">PTS</th>
                            <th class="th-pas">PAS</th>
                            <th class="th-rapor td-mapel-border-right">RAPOR</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($siswas as $siswa): ?>
                    <tr>
                        <td class="sticky-col sticky-no"><?= $no++ ?></td>
                        <td class="sticky-col sticky-nis"><?= htmlspecialchars($siswa['nis']) ?></td>
                        <td class="td-text sticky-col sticky-nama"><strong><?= htmlspecialchars($siswa['nama']) ?></strong></td>
                        
                        <?php foreach ($mapels as $mp): ?>
                            <?php 
                                $max_ph = $ph_counts[$kls_id . '_' . $mp['id']] ?? 1; 
                                $kkm = $kkm_map[$mp['id'] . '_' . $tingkat] ?? 70;
                                
                                $sum_ph = 0;
                                $count_ph = 0;
                            ?>
                            
                            <!-- LOOPING PH -->
                            <?php for ($i = 1; $i <= $max_ph; $i++): ?>
                                <?php 
                                    $val_ph = $nilai_map[$siswa['id']][$mp['id']]["PH $i"] ?? ''; 
                                    if ($val_ph !== '') {
                                        $sum_ph += (float)$val_ph;
                                        $count_ph++;
                                    }
                                    
                                    $is_kosong = ($val_ph === '') ? 'bg-kosong' : '';
                                    $is_red = ($val_ph !== '' && (float)$val_ph < (float)$kkm) ? 'text-red' : '';
                                    $border = ($i === 1) ? 'td-mapel-border-left' : '';
                                ?>
                                <td class="<?= $border ?> <?= $is_kosong ?> <?= $is_red ?>"><?= $val_ph !== '' ? $val_ph : '-' ?></td>
                            <?php endfor; ?>
                            
                            <!-- PTS -->
                            <?php 
                                $val_pts = $nilai_map[$siswa['id']][$mp['id']]["PTS"] ?? ''; 
                                $is_kosong = ($val_pts === '') ? 'bg-kosong' : '';
                                $is_pts_red = ($val_pts !== '' && (float)$val_pts < (float)$kkm) ? 'text-red' : '';
                            ?>
                            <td class="<?= $is_kosong ?> <?= $is_pts_red ?>"><?= $val_pts !== '' ? $val_pts : '-' ?></td>
                            
                            <!-- PAS -->
                            <?php 
                                $val_pas = $nilai_map[$siswa['id']][$mp['id']]["PAS"] ?? ''; 
                                $is_kosong = ($val_pas === '') ? 'bg-kosong' : '';
                                $is_pas_red = ($val_pas !== '' && (float)$val_pas < (float)$kkm) ? 'text-red' : '';
                            ?>
                            <td class="<?= $is_kosong ?> <?= $is_pas_red ?>"><?= $val_pas !== '' ? $val_pas : '-' ?></td>
                            
                            <!-- RAPOR -->
                            <?php 
                                $rapor = '';
                                $count_komponen = $max_ph + 1; // PH(s) + 1 PTS
                                if ($count_ph > 0 || $val_pts !== '' || $val_pas !== '') {
                                    if ($val_pts !== '') {
                                        $sum_ph += (float)$val_pts;
                                    }
                                    $rata_rata = $sum_ph / $count_komponen;
                                    $pas_num = $val_pas !== '' ? (float)$val_pas : 0;
                                    $rapor = round(($rata_rata * $bobotHarian) + ($pas_num * $bobotPas));
                                }
                                $is_kosong = ($rapor === '') ? 'bg-kosong' : '';
                                $is_rapor_red = ($rapor !== '' && (float)$rapor < (float)$kkm) ? 'text-red' : '';
                            ?>
                            <td class="td-mapel-border-right <?= $is_kosong ?> <?= $is_rapor_red ?>"><strong><?= $rapor !== '' ? $rapor : '-' ?></strong></td>
                            
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>

</body>
</html>
