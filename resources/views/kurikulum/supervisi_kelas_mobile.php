<?php
// Validasi session (karena dari controller public)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['guru_id'])) {
    header("Location: /apk/login.php");
    exit;
}

function tgl_indo($tanggal){
    $bulan = array (
        1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return (int)$pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$page_title = 'Supervisi Kelas';
$favicon_url = '/public/assets/images/favicon.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="icon" href="<?= htmlspecialchars($favicon_url) ?>" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html { background: #e2e8f0; display: flex; justify-content: center; }
        body { width: 100%; max-width: 480px; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; padding-bottom: 80px; box-shadow: 0 0 25px rgba(0,0,0,0.1); min-height: 100vh; position: relative; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 3.5rem 1.5rem 2rem; color: white; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; position: relative; overflow: hidden; }
        .header-top { display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 2; }
        .greeting p { margin: 0; font-size: 0.9rem; opacity: 0.9; }
        .greeting h1 { margin: 5px 0 0; font-size: 1.5rem; font-weight: 800; }
        .avatar { width: 45px; height: 45px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        
        .container { padding: 1.5rem; margin-top: -30px; position: relative; z-index: 3; }
        
        .card { background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px dashed #e2e8f0; gap: 10px; }
        .guru-info h3 { margin: 0; font-size: 1.1rem; color: #0f172a; line-height: 1.3; }
        .guru-info p { margin: 5px 0 0; font-size: 0.85rem; color: #64748b; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
        .status-jadwal { background: #fffbeb; color: #d97706; }
        .status-selesai { background: #f0fdf4; color: #16a34a; }
        
        .detail-row { display: flex; margin-bottom: 8px; font-size: 0.9rem; }
        .detail-row i { width: 20px; color: #94a3b8; margin-top: 3px; }
        .detail-text { flex: 1; color: #475569; }
        .detail-text strong { color: #1e293b; }
        
        .btn-action { display: block; width: 100%; box-sizing: border-box; padding: 12px; background: #f1f5f9; color: #0f172a; border-radius: 12px; text-align: center; text-decoration: none; font-weight: 700; margin-top: 15px; }
        
        /* Nav Bawah Standar APK */
        .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; background: white; display: flex; justify-content: space-around; padding: 1rem 1rem 1.8rem; box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.05); border-top-left-radius: 25px; border-top-right-radius: 25px; z-index: 50; padding-bottom: calc(1.8rem + env(safe-area-inset-bottom)); }
    </style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div class="greeting">
            <p>Menu Koordinator / Admin</p>
            <h1>Supervisi Kelas</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-users-viewfinder"></i>
        </div>
    </div>
</div>

<div class="container">
    <?php if(empty($jadwal_supervisi)): ?>
        <div style="text-align: center; padding: 3rem 0; color: #94a3b8;">
            <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>Belum ada jadwal supervisi.</p>
        </div>
    <?php else: ?>
        <?php foreach($jadwal_supervisi as $j): ?>
            <?php 
                $is_selesai = $j['status'] === 'Dievaluasi';
                $status_class = $is_selesai ? 'status-selesai' : 'status-jadwal';
                $status_text = $is_selesai ? 'Selesai' : 'Terjadwal';
            ?>
            <div class="card">
                <div class="card-header">
                    <div class="guru-info">
                        <h3><?= htmlspecialchars($j['nama_guru']) ?></h3>
                        <p><?= htmlspecialchars($j['nama_mapel']) ?></p>
                    </div>
                    <div class="status-badge <?= $status_class ?>"><?= $status_text ?></div>
                </div>
                
                <div class="detail-row">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <div class="detail-text">Kelas: <strong><?= htmlspecialchars($j['nama_kelas']) ?></strong></div>
                </div>
                <div class="detail-row">
                    <i class="fa-regular fa-calendar"></i>
                    <div class="detail-text">Tanggal: <strong><?= tgl_indo($j['tanggal']) ?></strong></div>
                </div>
                <div class="detail-row">
                    <i class="fa-solid fa-user-tie"></i>
                    <div class="detail-text">Supervisor: <strong><?= htmlspecialchars($j['nama_supervisor']) ?></strong></div>
                </div>
                
                <?php if($is_selesai): ?>
                <div class="detail-row" style="margin-top:10px; background:#f8fafc; padding:10px; border-radius:10px;">
                    <i class="fa-solid fa-star" style="color:#eab308;"></i>
                    <div class="detail-text">Nilai: <strong style="font-size:1.1rem; color:#16a34a;"><?= htmlspecialchars($j['nilai']) ?></strong></div>
                </div>
                <?php else: ?>
                <a href="/kurikulum/supervisi/kelas/evaluasi/mobile?id=<?= $j['id'] ?>" class="btn-action" style="background:#10b981; color:white; margin-top: 10px;"><i class="fa-solid fa-clipboard-check"></i> Mulai Evaluasi</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php 
// Fallback if not initialized in controller
if(!isset($core_db) && class_exists('\App\Core\Database')) {
    $core_db = \App\Core\Database::connect('core');
}
$current_page = 'aplikasi_saya.php'; // Highlight the "Aplikasi" menu
include __DIR__ . '/../../../apk/footer_guru.php'; 
?>

</body>
</html>
