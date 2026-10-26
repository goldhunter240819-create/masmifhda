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
    <title>Rekap Nilai Harian - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .nh-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .nh-card-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; }
        .nh-card-head h3 { margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .nh-table { width: 100%; border-collapse: collapse; }
        .nh-table th { padding: 10px 16px; background: #f8fafc; font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px solid #f1f5f9; white-space: nowrap; }
        .nh-table td { padding: 12px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.85rem; vertical-align: middle; color: #334155; }
        .nh-table tr:last-child td { border-bottom: none; }
        .nh-table tr:hover td { background: #fafffe; }
        .nilai-badge { font-weight: 800; font-size: 1rem; color: #0284c7; }
        .siswa-badge { background: #eff6ff; color: #1d4ed8; padding: 3px 10px; border-radius: 50px; font-weight: 700; font-size: 0.8rem; }
        .btn-detail { padding: 5px 12px; font-size: 0.78rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; }
        .btn-hapus { padding: 5px 10px; font-size: 0.78rem; background: #fef2f2; color: #dc2626; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: #64748b; }
        .empty-state i { display: block; margin: 0 auto 1rem; color: #cbd5e1; }
        .empty-state h3 { color: #0f172a; margin: 0 0 8px 0; }
        .empty-state p { margin: 0; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <!-- Page Header -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:white;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="bar-chart-2" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;">Rekap Nilai Harian</h2>
                        <p style="margin:0; font-size:0.85rem; color:#64748b;">Riwayat pengisian nilai harian, ulangan, dan tugas siswa.</p>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background:#f0f9ff; border:1px solid #0ea5e9; color:#0284c7; padding:14px 16px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
                <span>Data nilai harian berhasil dihapus.</span>
            </div>
            <?php endif; ?>

            <div class="nh-card">
                <div class="nh-card-head">
                    <h3><i data-lucide="history" style="width:18px;height:18px;color:#0ea5e9;"></i> Riwayat Input Nilai</h3>
                </div>
                <div style="overflow-x:auto;">
                    <table class="nh-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Nama Materi / Ujian</th>
                                <th style="text-align:center;">Siswa Dinilai</th>
                                <th style="text-align:center;">Rata-rata</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($riwayat)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i data-lucide="inbox" style="width:48px;height:48px;"></i>
                                        <h3>Belum Ada Data Nilai Harian</h3>
                                        <p>Gunakan Aplikasi Mobile (APK) Guru untuk menginput nilai harian siswa dengan mudah.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($riwayat as $r): ?>
                                <tr>
                                    <td style="white-space:nowrap;"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                                    <td style="font-weight:700;"><?= htmlspecialchars($r['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_mapel']) ?></td>
                                    <td><?= htmlspecialchars($r['judul_materi']) ?></td>
                                    <td style="text-align:center;"><span class="siswa-badge"><?= $r['jumlah_siswa'] ?> Siswa</span></td>
                                    <td style="text-align:center;" class="nilai-badge"><?= number_format($r['rata_rata'], 1) ?></td>
                                    <td style="text-align:center; white-space:nowrap;">
                                        <a href="<?php echo base_url('guru/nilai_harian/detail/' . $r['id']); ?>" class="btn-detail">
                                            <i data-lucide="eye" style="width:13px;height:13px;"></i> Detail
                                        </a>
                                        <a href="<?php echo base_url('guru/nilai_harian/delete/' . $r['id']); ?>" onclick="return confirm('Yakin ingin menghapus data nilai ini beserta seluruh rincian siswanya?');" class="btn-hapus" style="margin-left:5px;">
                                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
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
    </div>
</div>
<script>lucide.createIcons();</script>
</body>
</html>


