<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps
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
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .j-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); display: flex; gap: 20px; align-items: flex-start; }
        .j-date { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; text-align: center; min-width: 80px; }
        .j-date-d { font-size: 1.8rem; font-weight: 800; color: #8b5cf6; line-height: 1; margin-bottom: 4px; }
        .j-date-m { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .j-content { flex: 1; }
        .j-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; }
        .j-desc { font-size: 0.9rem; color: #475569; margin: 0 0 15px 0; line-height: 1.6; }
        .j-stats { display: flex; gap: 15px; font-size: 0.8rem; font-weight: 600; flex-wrap: wrap; }
        .s-badge { padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; }
        .s-h { background: #f0f9ff; color: #0ea5e9; }
        .s-s { background: #eff6ff; color: #3b82f6; }
        .s-i { background: #fefce8; color: #eab308; }
        .s-a { background: #fef2f2; color: #ef4444; }
        .j-photo { width: 120px; height: 80px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; }
        .j-actions { display: flex; gap: 8px; flex-direction: column; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra'); ?>" class="z-btn-icon" style="background:white; border:1px solid #e2e8f0;"><i data-lucide="arrow-left"></i></a>
                    <div>
                        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;">Jurnal & Absensi</h2>
                        <p style="margin:0; font-size:0.85rem; color:#64748b;">Ekstrakurikuler: <?php echo htmlspecialchars($nama_ekstra); ?></p>
                    </div>
                </div>
                <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal/form?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" class="z-btn" style="background:linear-gradient(135deg, #8b5cf6, #7c3aed); color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; text-decoration:none;"><i data-lucide="plus" style="width:16px;height:16px;vertical-align:-3px;margin-right:5px;"></i> Tambah Pertemuan</a>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="background:#f0f9ff; border:1px solid #0ea5e9; color:#0284c7; padding:15px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                    <i data-lucide="check-circle"></i>
                    <div><strong>Berhasil!</strong> Data jurnal dan absensi berhasil diperbarui.</div>
                </div>
            <?php endif; ?>

            <?php if(empty($jurnals)): ?>
                <div class="z-panel" style="text-align:center; padding:4rem 2rem; color:#64748b;">
                    <i data-lucide="book-open" style="width:48px; height:48px; margin-bottom:1rem; opacity:0.3; display:inline-block;"></i>
                    <h3 style="color:#0f172a; margin:0 0 10px 0;">Belum Ada Jurnal</h3>
                    <p style="margin:0;">Catat riwayat pertemuan latihan dan absensi di sini.</p>
                </div>
            <?php else: ?>
                <?php foreach($jurnals as $j): 
                    $timestamp = strtotime($j['tanggal']);
                ?>
                <div class="j-card">
                    <div class="j-date">
                        <div class="j-date-d"><?php echo date('d', $timestamp); ?></div>
                        <div class="j-date-m"><?php echo date('M Y', $timestamp); ?></div>
                    </div>
                    
                    <div class="j-content">
                        <h3 class="j-title">Materi: <?php echo htmlspecialchars($j['materi']); ?></h3>
                        <div class="j-stats">
                            <span class="s-badge s-h"><i data-lucide="check-circle" style="width:14px;height:14px;"></i> <?php echo $j['hadir']; ?> Hadir</span>
                            <span class="s-badge s-s"><i data-lucide="activity" style="width:14px;height:14px;"></i> <?php echo $j['sakit']; ?> Sakit</span>
                            <span class="s-badge s-i"><i data-lucide="info" style="width:14px;height:14px;"></i> <?php echo $j['izin']; ?> Izin</span>
                            <span class="s-badge s-a"><i data-lucide="x-circle" style="width:14px;height:14px;"></i> <?php echo $j['alpa']; ?> Alpa</span>
                        </div>
                    </div>
                    
                    <?php if($j['dokumentasi']): ?>
                        <a href="<?php echo base_url('public/uploads/ekstra_jurnal/<?php echo $j['dokumentasi']; ?>'); ?>" target="_blank">
                            <img src="<?php echo base_url('public/uploads/ekstra_jurnal/<?php echo $j['dokumentasi']; ?>'); ?>" class="j-photo" alt="Dokumentasi">
                        </a>
                    <?php else: ?>
                        <div class="j-photo" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:0.75rem; text-align:center; padding:10px;">
                            <div><i data-lucide="image-off" style="width:24px;height:24px;margin-bottom:5px;display:block;margin:auto;"></i> No Photo</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="j-actions">
                        <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal/form/<?php echo $j['id']; ?>?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" class="z-btn-icon" style="background:#f8fafc; color:#3b82f6; border:1px solid #e2e8f0;" title="Edit Jurnal"><i data-lucide="edit-2"></i></a>
                        <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal/delete/<?php echo $j['id']; ?>?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" onclick="return confirm('Yakin ingin menghapus jurnal dan seluruh data absensi pada pertemuan ini?');" class="z-btn-icon" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca;" title="Hapus Jurnal"><i data-lucide="trash-2"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>
<script>lucide.createIcons();</script>
</body>
</html>


