<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

$myApps = [];
if (isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = " . (int)$_SESSION['user_id'])->fetchAll();
        foreach ($res as $r) { $myApps[] = $r['app_code']; }
    } catch (\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekap Nilai Harian - MASMIFHDA SYSTEM</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .nh-rekap-banner {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 60%, #38bdf8 100%);
            border-radius: 18px;
            padding: 1.5rem 2rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 12px 26px -6px rgba(14, 165, 233, 0.35);
            position: relative;
            overflow: hidden;
        }
        .nh-rekap-banner .decor { position: absolute; right: -20px; top: -25px; opacity: 0.12; pointer-events: none; }
        .nh-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.04); margin-bottom: 1.5rem; }
        .nh-table { width: 100%; border-collapse: collapse; }
        .nh-table th { padding: 10px 12px; background: #f8fafc; font-size: 0.72rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.6px; border-bottom: 1px solid #e2e8f0; }
        .nh-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; vertical-align: middle; color: #334155; }
        .nh-table tr:hover td { background: #f8fbff; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <div class="nh-rekap-banner">
                <div class="decor"><i data-lucide="clipboard-list" style="width:180px;height:180px;"></i></div>
                <div style="position:relative; z-index:1;">
                    <div style="font-size:0.72rem; font-weight:800; letter-spacing:1px; text-transform:uppercase; color:#e0f2fe; margin-bottom:4px;">PORTAL GURU</div>
                    <h2 style="margin:0 0 4px 0; font-size:1.35rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="eye" style="width:22px;height:22px;"></i> Detail Monitoring Nilai
                    </h2>
                    <p style="margin:0; font-size:0.88rem; color:#e0f2fe;">Pantau progres nilai harian, PTS, PAS, dan nilai akhir siswa secara menyeluruh.</p>
                </div>
                <div style="display:flex; gap:10px; position:relative; z-index:1;">
                    <a href="<?php echo base_url('guru/nilai_harian/cetak_rekap?kelas_id=' . (int)$kelas_id . '&mapel_id=' . (int)$mapel_id); ?>" target="_blank" style="background:#fff; color:#0284c7; padding:9px 16px; border-radius:9px; font-weight:700; font-size:0.84rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Rekap
                    </a>
                    <a href="<?php echo base_url('guru/nilai_harian'); ?>" style="background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.35); padding:9px 16px; border-radius:9px; font-weight:700; font-size:0.84rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="nh-card" style="padding: 1.5rem; display: flex; gap: 2rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 280px;">
                    <table style="width: 100%; border: none;">
                        <tr><td style="padding: 6px 0; width: 140px; color: #64748b; font-size: 0.85rem;">Mata Pelajaran</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($mapel); ?></td></tr>
                        <tr><td style="padding: 6px 0; color: #64748b; font-size: 0.85rem;">Kelas</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($kelas); ?></td></tr>
                        <tr><td style="padding: 6px 0; color: #64748b; font-size: 0.85rem;">Guru Pengampu</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($guru); ?></td></tr>
                        <tr><td style="padding: 6px 0; color: #64748b; font-size: 0.85rem;">Wali Kelas</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($wali_kelas); ?></td></tr>
                        <tr><td style="padding: 6px 0; color: #64748b; font-size: 0.85rem;">Tahun Ajaran</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($tahun_ajaran); ?></td></tr>
                        <tr><td style="padding: 6px 0; color: #64748b; font-size: 0.85rem;">Semester</td><td style="padding: 6px 0; font-weight: 700; color:#0f172a;">: <?php echo htmlspecialchars($semester); ?></td></tr>
                    </table>
                </div>
                <div style="flex: 1; min-width: 280px;">
                    <div style="color: #0284c7; font-weight: 800; font-size: 0.9rem; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="info" style="width: 16px; height: 16px;"></i> Keterangan Materi:
                    </div>
                    <table class="nh-table" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Evaluasi</th>
                                <th>Materi Pembelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evaluasi_list)): ?>
                                <tr><td colspan="2" style="text-align:center; color:#64748b;">Belum ada penilaian harian.</td></tr>
                            <?php else: foreach ($evaluasi_list as $idx => $ev): ?>
                                <tr>
                                    <td><strong style="color: #0f172a;">PH<?php echo $idx + 1; ?></strong></td>
                                    <td><?php echo htmlspecialchars($ev['judul_materi']); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="nh-card">
                <div style="overflow-x: auto;">
                    <table class="nh-table" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 50px; text-align: center; vertical-align: middle;">NO</th>
                                <th rowspan="2" style="width: 100px; vertical-align: middle;">NIS</th>
                                <th rowspan="2" style="vertical-align: middle;">NAMA LENGKAP SISWA</th>
                                <?php if (count($evaluasi_list) > 0): ?>
                                    <th colspan="<?php echo count($evaluasi_list); ?>" style="text-align: center; border-left: 1px solid #e2e8f0;">PENILAIAN HARIAN (PH)</th>
                                <?php endif; ?>
                                <th colspan="2" style="text-align: center; border-left: 1px solid #e2e8f0;">EVALUASI SUMATIF</th>
                                <th rowspan="2" style="width: 110px; text-align: center; border-left: 1px solid #e2e8f0; vertical-align: middle;">NILAI RAPOR<br><span style="font-size: 0.7rem; color:#64748b;">(KKM: <?php echo $kkm; ?>)</span></th>
                            </tr>
                            <tr>
                                <?php foreach ($evaluasi_list as $idx => $ev): ?>
                                    <th style="text-align: center; border-left: 1px solid #e2e8f0;">PH<?php echo $idx + 1; ?></th>
                                <?php endforeach; ?>
                                <th style="text-align: center; border-left: 1px solid #e2e8f0; width: 65px;">PTS</th>
                                <th style="text-align: center; width: 65px;">PAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($siswa_list as $s): ?>
                            <tr>
                                <td align="center" style="color:#64748b; font-weight:700;"><?php echo $no++; ?></td>
                                <td style="font-family:monospace; color:#475569;"><?php echo htmlspecialchars($s['nis'] ?? '-'); ?></td>
                                <td><strong style="color: #0f172a; text-transform: uppercase; font-size: 0.82rem;"><?php echo htmlspecialchars($s['nama']); ?></strong></td>
                                <?php foreach ($evaluasi_list as $ev):
                                    $val = $s['nilai_harian'][$ev['id']];
                                    $color = (is_numeric($val) && $val < $kkm) ? 'color: #ef4444;' : 'color: #0f172a;';
                                ?>
                                    <td align="center" style="font-weight: 700; border-left: 1px solid #f1f5f9; <?php echo $color; ?>">
                                        <?php echo htmlspecialchars((string)$val); ?>
                                    </td>
                                <?php endforeach;
                                    $val_pts = $s['nilai_pts'];
                                    $color_pts = (is_numeric($val_pts) && $val_pts < $kkm) ? 'color: #ef4444;' : 'color: #0f172a;';
                                    $val_pas = $s['nilai_pas'];
                                    $color_pas = (is_numeric($val_pas) && $val_pas < $kkm) ? 'color: #ef4444;' : 'color: #0f172a;';
                                ?>
                                <td align="center" style="font-weight: 700; border-left: 1px solid #f1f5f9; <?php echo $color_pts; ?>">
                                    <?php echo htmlspecialchars((string)$val_pts); ?>
                                </td>
                                <td align="center" style="font-weight: 700; <?php echo $color_pas; ?>">
                                    <?php echo htmlspecialchars((string)$val_pas); ?>
                                </td>
                                <td align="center" style="font-weight: 800; border-left: 1px solid #f1f5f9; <?php echo ($s['nilai_rapor'] < $kkm) ? 'color: #ef4444;' : 'color: #0284c7;'; ?>">
                                    <?php echo htmlspecialchars((string)$s['nilai_rapor']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
