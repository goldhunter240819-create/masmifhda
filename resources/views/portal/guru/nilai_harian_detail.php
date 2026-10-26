<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps (dibutuhkan sidebar)
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Nilai Harian - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .info-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; }
        .info-item .lbl { font-size: 0.72rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .info-item .val { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .nh-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .nh-card-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .nh-card-head h3 { margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .nh-table { width: 100%; border-collapse: collapse; }
        .nh-table th { padding: 10px 16px; background: #f8fafc; font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px solid #f1f5f9; }
        .nh-table td { padding: 12px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.85rem; vertical-align: middle; color: #334155; }
        .nh-table tr:last-child td { border-bottom: none; }
        .nh-table tr:hover td { background: #fafffe; }
        .nilai-pass { font-weight: 800; font-size: 1.05rem; color: #0284c7; }
        .nilai-fail { font-weight: 800; font-size: 1.05rem; color: #dc2626; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <!-- Back + Title -->
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
                <a href="<?php echo base_url('guru/nilai_harian'); ?>" style="width:38px;height:38px;background:white;border:1px solid #e2e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;" title="Kembali">
                    <i data-lucide="arrow-left" style="width:18px;height:18px;"></i>
                </a>
                <div>
                    <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;">Detail Nilai Harian</h2>
                    <p style="margin:0; font-size:0.85rem; color:#64748b;">Materi: <strong><?= htmlspecialchars($header['judul_materi']) ?></strong></p>
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-card">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="lbl">Tanggal</div>
                        <div class="val"><?= date('d F Y', strtotime($header['tanggal'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Kelas</div>
                        <div class="val"><?= htmlspecialchars($header['nama_kelas']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Mata Pelajaran</div>
                        <div class="val"><?= htmlspecialchars($header['nama_mapel']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Table Siswa -->
            <div class="nh-card">
                <div class="nh-card-head">
                    <h3><i data-lucide="users" style="width:18px;height:18px;color:#0ea5e9;"></i> Daftar Nilai Siswa</h3>
                </div>
                <div style="overflow-x:auto;">
                    <table class="nh-table">
                        <thead>
                            <tr>
                                <th style="width:50px;text-align:center;">No</th>
                                <th style="width:120px;">NIS</th>
                                <th>Nama Siswa</th>
                                <th style="width:130px;text-align:center;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($details)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:2rem; color:#64748b;">Tidak ada rincian data siswa.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($details as $d): ?>
                                <tr>
                                    <td style="text-align:center; color:#94a3b8;"><?= $no++ ?></td>
                                    <td style="font-family:monospace; color:#475569;"><?= htmlspecialchars($d['nis'] ?? '-') ?></td>
                                    <td style="font-weight:600;"><?= htmlspecialchars($d['nama']) ?></td>
                                    <td style="text-align:center;">
                                        <span class="<?= ($d['nilai'] >= 75) ? 'nilai-pass' : 'nilai-fail' ?>">
                                            <?= floatval($d['nilai']) ?>
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
<script>lucide.createIcons();</script>
</body>
</html>


