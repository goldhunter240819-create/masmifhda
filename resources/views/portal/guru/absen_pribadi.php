<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Riwayat Kehadiran'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <!-- â”€â”€ MAIN AREA â”€â”€ -->
    <div class="z-main">

<?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <div style="margin-bottom: 2rem;">
                <h2 style="margin: 0;">Riwayat Kehadiran Pribadi</h2>
                <p style="color: var(--text-muted); margin-top: 5px;">Rekapitulasi kehadiran Anda saat mengajar.</p>
            </div>

<div class="z-panel" style="background: transparent; box-shadow: none; padding: 0;">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #0ea5e9;">
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;"><?= $rekap['Hadir'] ?> <span style="font-size:1rem; font-weight:normal; color:#64748b;">kali</span></div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><i data-lucide="check-circle-2" style="color:#0ea5e9; width:16px; display:inline-block; vertical-align:text-bottom;"></i> Hadir</div>
        </div>
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #f59e0b;">
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;"><?= $rekap['Sakit'] ?> <span style="font-size:1rem; font-weight:normal; color:#64748b;">kali</span></div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><i data-lucide="thermometer" style="color:#f59e0b; width:16px; display:inline-block; vertical-align:text-bottom;"></i> Sakit</div>
        </div>
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #3b82f6;">
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;"><?= $rekap['Izin'] ?> <span style="font-size:1rem; font-weight:normal; color:#64748b;">kali</span></div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><i data-lucide="mail" style="color:#3b82f6; width:16px; display:inline-block; vertical-align:text-bottom;"></i> Izin</div>
        </div>
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #ef4444;">
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;"><?= $rekap['Alpa'] ?> <span style="font-size:1rem; font-weight:normal; color:#64748b;">kali</span></div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><i data-lucide="x-circle" style="color:#ef4444; width:16px; display:inline-block; vertical-align:text-bottom;"></i> Alpa</div>
        </div>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="calendar-clock"></i> Riwayat Kehadiran Saya</div>
    </div>
    <div class="z-panel-body">
        
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 8px; font-size: 0.9rem; color: #1e40af; margin-bottom: 25px;">
            <i data-lucide="info" style="width: 18px; display: inline-block; vertical-align: middle; margin-right: 5px;"></i>
            Status "Hadir" akan terisi secara otomatis ketika Anda menyimpan <strong>Jurnal Mengajar</strong> di kelas pertama pada hari tersebut. Halaman ini hanya menampilkan riwayat dari catatan kehadiran Anda.
        </div>

        <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="" style="display:flex; gap:10px;">
                <select name="m" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px;">
                    <?php
                    $months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                    for($i=1; $i<=12; $i++):
                        $m_val = str_pad($i, 2, "0", STR_PAD_LEFT);
                        $sel = ($m_val == $filter_month) ? 'selected' : '';
                    ?>
                    <option value="<?= $m_val ?>" <?= $sel ?>><?= $months[$i-1] ?></option>
                    <?php endfor; ?>
                </select>
                <select name="y" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px;">
                    <?php
                    $y_now = date('Y');
                    for($i=$y_now; $i>=$y_now-2; $i--):
                        $sel = ($i == $filter_year) ? 'selected' : '';
                    ?>
                    <option value="<?= $i ?>" <?= $sel ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="z-btn z-btn-dark"><i data-lucide="filter"></i> Filter</button>
            </form>
        </div>

        <div class="z-table-responsive">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu Pencatatan</th>
                        <th>Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($riwayat_absen)): ?>
                    <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:20px;">Belum ada riwayat kehadiran bulan ini.</td></tr>
                    <?php else: foreach($riwayat_absen as $a): ?>
                    <tr>
                        <td style="font-weight: 700; color: #1e293b;"><i data-lucide="calendar" style="width:14px; color:#64748b; margin-right:5px;"></i> <?= date('d M Y', strtotime($a['tanggal'])) ?></td>
                        <td style="font-family: monospace; font-size: 1.1rem; color: #334155;"><?= htmlspecialchars($a['waktu_masuk'] ?: '-') ?></td>
                        <td>
                            <?php 
                            if($a['status'] == 'Hadir') echo '<span style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Hadir</span>';
                            elseif($a['status'] == 'Sakit') echo '<span style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Sakit</span>';
                            elseif($a['status'] == 'Izin') echo '<span style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Izin</span>';
                            elseif($a['status'] == 'Alpa') echo '<span style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Alpa</span>';
                            else echo '<span style="background:#f1f5f9; color:#475569; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; text-transform:uppercase;">'.$a['status'].'</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php include __DIR__ . '/../footer_multi_guru.php'; ?>

