<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Pengguna";
if(isset($_SESSION['role_id'])) {
    if($_SESSION['role_id'] == 2) $roleName = "Guru / Tendik";
    elseif($_SESSION['role_id'] == 3) $roleName = "Siswa";
    elseif($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 99) $roleName = "Administrator";
}
$profileUrl = "#";
if(isset($_SESSION['role_id'])) {
    if($_SESSION['role_id'] == 2) $profileUrl = base_url('guru/profil-guru');
    elseif($_SESSION['role_id'] == 3) $profileUrl = base_url('siswa/profil-siswa');
}
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
$userFoto = null;
if(isset($_SESSION['role_id']) && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    $role_id = $_SESSION['role_id'];
    
    try {
        // Fetch Apps
        if($role_id == 2) {
            $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
            foreach($res as $r) {
                $myApps[] = $r['app_code'];
            }
        }
        
        // Fetch Profile Photo & Status
        if($role_id == 2) { // Guru
            $data = $db_core->query("SELECT foto, is_kamad, status_kepegawaian FROM guru WHERE user_id = $uid LIMIT 1")->fetch();
            $status_kepegawaian = $data ? $data['status_kepegawaian'] : 'Honorer';
            if($data && !empty($data['foto'])) $userFoto = base_url('public/uploads/guru/') . $data['foto'];
            if($data && $data['is_kamad']) $roleName = "Kepala Madrasah";
        } elseif($role_id == 3) { // Siswa
            $data = $db_core->query("SELECT foto FROM siswa WHERE user_id = $uid LIMIT 1")->fetch();
            if($data && !empty($data['foto'])) $userFoto = base_url('public/uploads/siswa/') . $data['foto'];
        }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Terpadu - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . time()); ?>">
</head>
<body>
<div class="siakad-container">

<?php
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
if ($role_id == 2) {
    include __DIR__ . '/sidebar_multi_guru.php';
} elseif ($role_id == 3) {
    include __DIR__ . '/sidebar_multi_siswa.php';
} else {
    include __DIR__ . '/sidebar.php';
}
?>

    <!-- Ã¢â€â‚¬Ã¢â€â‚¬ MAIN AREA Ã¢â€â‚¬Ã¢â€â‚¬ -->
    <div class="z-main">

<?php
if ($role_id == 2) {
    include __DIR__ . '/header_multi_guru.php';
} elseif ($role_id == 3) {
    include __DIR__ . '/header_multi_siswa.php';
} else {
    include __DIR__ . '/header.php';
}
?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">

            <!-- Welcome Banner -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);">
                <div class="header-bg-shapes" style="position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 20px 20px; opacity: 0.15; pointer-events: none;"></div>
                <div style="position: relative; z-index: 2;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="graduation-cap" style="color: rgba(255,255,255,0.8);"></i> Selamat Datang, <?php echo $nama; ?>! 👋
                    </h1>
                    <p style="margin: 0 0 1rem; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Ini adalah Portal Terpadu Aula System. Akses semua modul — SIAKAD, Keuangan, dan Absensi — dari satu tempat.</p>
                    <div class="badge-row" style="display: flex; gap: 10px;">
                        <span class="pb" style="background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;"><i data-lucide="shield-check" style="width:14px;height:14px; margin-right:4px;"></i> Portal Resmi</span>
                        <span class="pb pb-white" style="background: white; color: #0ea5e9; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 700;"><i data-lucide="calendar" style="width:14px;height:14px; margin-right:4px;"></i> <?php echo date('l, d F Y'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="portal-stats" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="feature-card" style="display: flex; align-items: center; gap: 15px; text-align: left; padding: 1.25rem;">
                    <div class="ps-icon" style="background:#f0f9ff; color:#0ea5e9; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;"><i data-lucide="clock"></i></div>
                    <div>
                        <div class="ps-label" style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Jam Sekarang</div>
                        <div class="ps-value" style="font-size: 1.2rem; font-weight: 800; color: #0f172a;"><?php echo date('H:i'); ?></div>
                    </div>
                </div>
                <div class="feature-card" style="display: flex; align-items: center; gap: 15px; text-align: left; padding: 1.25rem;">
                    <div class="ps-icon" style="background:#eff6ff; color:#3b82f6; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;"><i data-lucide="monitor-play"></i></div>
                    <div>
                        <div class="ps-label" style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Sistem</div>
                        <div class="ps-value" style="font-size: 1.2rem; font-weight: 800; color: #3b82f6;">Online</div>
                    </div>
                </div>
                <div class="feature-card" style="display: flex; align-items: center; gap: 15px; text-align: left; padding: 1.25rem;">
                    <div class="ps-icon" style="background:#fef3c7; color:#d97706; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;"><i data-lucide="user"></i></div>
                    <div>
                        <div class="ps-label" style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Role Aktif</div>
                        <div class="ps-value" style="font-size: 1rem; font-weight: 800; color: #0f172a; <?php echo ($roleName == 'Kepala Madrasah') ? 'color:#d97706;' : ''; ?>"><?php echo $roleName; ?></div>
                    </div>
                </div>
                <div class="feature-card" style="display: flex; align-items: center; gap: 15px; text-align: left; padding: 1.25rem;">
                    <div class="ps-icon" style="background:#ecfdf5; color:#10b981; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;"><i data-lucide="calendar-days"></i></div>
                    <div>
                        <div class="ps-label" style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Hari Ini</div>
                        <div class="ps-value" style="font-size: 1rem; font-weight: 800; color: #0f172a;"><?php echo date('d M Y'); ?></div>
                    </div>
                </div>
            </div>

            <!-- App Shortcuts -->
            <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="grid-2x2"></i> Akses Cepat Aplikasi</div>
                </div>
                <div class="z-panel-body">
                    <div class="app-grid">
                        <?php if(isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 99)): ?>
                        
                        <a href="<?php echo base_url('siakad'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
                            <div class="ai"><i data-lucide="graduation-cap"></i></div>
                            <div class="an">SIAKAD</div>
                            <div class="as">Data Akademik</div>
                        </a>
                        <a href="<?php echo base_url('admin/finance'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#0284c7,#0ea5e9);">
                            <div class="ai"><i data-lucide="wallet"></i></div>
                            <div class="an">Keuangan SPP</div>
                            <div class="as">Sistem Administrasi Siswa</div>
                        </a>
                        <a href="<?php echo base_url('admin/bos'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#059669,#10b981);">
                            <div class="ai"><i data-lucide="landmark"></i></div>
                            <div class="an">Keuangan BOS</div>
                            <div class="as">Manajemen BKU & RKAS</div>
                        </a>
                        <a href="<?php echo base_url('admin/absen-v2'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
                            <div class="ai"><i data-lucide="calendar-check"></i></div>
                            <div class="an">Absen</div>
                            <div class="as">Sistem Presensi</div>
                        </a>

                        <?php elseif(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
                        
                        <a href="<?php echo base_url('siakad'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
                            <div class="ai"><i data-lucide="graduation-cap"></i></div>
                            <div class="an">SIAKAD Guru</div>
                            <div class="as">Panel Akademik</div>
                        </a>
                        
                        <a href="<?php echo base_url('absen-saya'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
                            <div class="ai"><i data-lucide="calendar-check"></i></div>
                            <div class="an">Absen</div>
                            <div class="as">Rekap Kehadiran</div>
                        </a>

                        <?php else: ?>
                        <a href="<?php echo base_url('siakad'); ?>" class="app-card featured" style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
                            <div class="ai"><i data-lucide="graduation-cap"></i></div>
                            <div class="an">SIAKAD Siswa</div>
                            <div class="as">Data Akademik</div>
                        </a>
                        <?php endif; ?>

                        <a href="<?php echo $profileUrl; ?>" class="app-card">
                            <div class="ai" style="background:#f0fdf4;color:#0284c7;"><i data-lucide="user-circle"></i></div>
                            <div class="an">Profil Saya</div>
                            <div class="as">Data Pribadi</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modul Dalam Pengembangan -->
            <div class="z-panel" style="margin-top: 1.5rem;">
                <div class="z-panel-head">
                    <div class="z-panel-title" style="color: #64748b;"><i data-lucide="rocket"></i> Segera Hadir (Dalam Pengembangan)</div>
                </div>
                <div class="z-panel-body">
                    <div class="app-grid" style="opacity: 0.9;">
                        <!-- E-Sarpras -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="boxes"></i></div>
                            <div class="an" style="color:#475569;">E-Sarpras</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Manajemen Aset</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                        <!-- E-Office -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="mail-plus"></i></div>
                            <div class="an" style="color:#475569;">E-Office</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Surat & Disposisi</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                        <!-- PPDB Online -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="users-plus"></i></div>
                            <div class="an" style="color:#475569;">PPDB Online</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Penerimaan Siswa Baru</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                        <!-- Buku Induk -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="book-open-check"></i></div>
                            <div class="an" style="color:#475569;">Buku Induk</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Digitalisasi Data</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                        <!-- Ekspor EMIS/RDM -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="database-zap"></i></div>
                            <div class="an" style="color:#475569;">Sync Data</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Integrasi EMIS/RDM</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                        <!-- CBT Bank Soal -->
                        <div class="app-card" style="background: linear-gradient(135deg,#f8fafc,#f1f5f9); border: 1px dashed #cbd5e1; cursor: not-allowed; text-align: center;">
                            <div class="ai" style="background:#e2e8f0;color:#64748b; margin: 0 auto 8px auto;"><i data-lucide="laptop-2"></i></div>
                            <div class="an" style="color:#475569;">CBT Bank Soal</div>
                            <div class="as" style="color:#94a3b8; font-size: 0.75rem;">Ujian Terintegrasi</div>
                            <div style="margin-top: 10px;"><span style="font-size: 0.6rem; background: #e2e8f0; color: #64748b; padding: 3px 8px; border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">COMING SOON</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Grid: Info + Notif -->
            <style>
                .bottom-grid {
                    display: grid;
                    grid-template-columns: 2fr 1fr;
                    gap: 1rem;
                }
                @media (max-width: 768px) {
                    .bottom-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
            <div class="bottom-grid">
                <div class="z-panel" style="margin:0;">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="clock-4"></i> Aktivitas Mendatang</div>
                    </div>
                    <div class="z-panel-body" style="padding: 1.25rem;">
                        <?php
                        $upcoming_tests = [];
                        if ($role_id == 2) {
                            try {
                                if (class_exists('\\App\\Controllers\\GhcbtPortalController')) {
                                    $assign = \App\Controllers\GhcbtPortalController::getTeacherAssignments();
                                    if ($assign && !empty($assign['mapels'])) {
                                        $db_cbt = \App\Core\Database::connect('cbt');
                                        $ph = implode(',', array_fill(0, count($assign['mapels']), '?'));
                                        $sql = "SELECT DISTINCT t.tes_id, t.tes_nama, t.tes_begin_time, t.tes_end_time 
                                                FROM cbt_tes t 
                                                JOIN cbt_tes_topik_set ts ON t.tes_id = ts.tset_tes_id 
                                                JOIN cbt_topik tf ON ts.tset_topik_id = tf.topik_id 
                                                WHERE tf.topik_nama IN ($ph) 
                                                  AND (
                                                    DATE(t.tes_begin_time) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                                                    OR DATE(t.tes_end_time) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                                                  )
                                                ORDER BY t.tes_begin_time ASC LIMIT 5";
                                        $stmt = $db_cbt->prepare($sql);
                                        $stmt->execute($assign['mapels']);
                                        $upcoming_tests = $stmt->fetchAll();
                                    }
                                }
                            } catch (\Exception $e) {}
                        }

                        if (empty($upcoming_tests)) {
                        ?>
                            <div style="text-align:center;padding:1.5rem;color:var(--z-muted);">
                                <i data-lucide="calendar-x" style="width:40px;height:40px;opacity:.25;margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;"></i>
                                <div style="font-weight:600;margin-bottom:4px;">Belum Ada Jadwal</div>
                                <div style="font-size:.82rem;">Tidak ada jadwal ujian mendatang yang berkaitan dengan Anda.</div>
                            </div>
                        <?php
                        } else {
                            foreach ($upcoming_tests as $ut) {
                                $is_ongoing = strtotime($ut['tes_begin_time']) <= time();
                        ?>
                                <div style="border-bottom: 1px solid #f1f5f9; padding: 12px 0; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;"><?php echo htmlspecialchars($ut['tes_nama']); ?></div>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                            <i data-lucide="calendar" style="width:12px;height:12px;display:inline-block;vertical-align:text-bottom;"></i>
                                            Mulai: <?php echo date('d M Y, H:i', strtotime($ut['tes_begin_time'])); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($is_ongoing): ?>
                                            <span style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.65rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; text-transform: uppercase;">Sedang Berlangsung</span>
                                        <?php else: ?>
                                            <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.65rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; text-transform: uppercase;">Mendatang</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="z-panel" style="margin:0;">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="bell"></i> Pemberitahuan</div>
                    </div>
                    <div class="z-panel-body">
                        <div class="notif-item">
                            <div class="notif-dot"></div>
                            <div>
                                <div class="notif-title">Selamat Datang!</div>
                                <div class="notif-sub">Portal Terpadu Aula System aktif dan siap digunakan.</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-dot" style="background:#f59e0b;"></div>
                            <div>
                                <div class="notif-title">Fitur Baru</div>
                                <div class="notif-sub">E-Raport dan E-Learning segera hadir.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php
if ($role_id == 2) {
    include __DIR__ . '/footer_multi_guru.php';
} elseif ($role_id == 3) {
    include __DIR__ . '/footer_multi_siswa.php';
} else {
    include __DIR__ . '/footer.php';
}
?>

<?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99, 2])): ?>
<script>
// Auto-update RDM link dari Ngrok API
function fetchRdmLink() {
    // Card ID bisa 'rdm-card' (admin) atau 'rdm-card-guru' (guru)
    var card  = document.getElementById('rdm-card') || document.getElementById('rdm-card-guru');
    var label = document.getElementById('rdm-link-status');
    var url   = document.getElementById('rdm-link-url');
    var dot   = document.getElementById('rdm-dot');

    if (!card) return;

    fetch(base_url('public/api/ngrok-status.php'))
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.status === 'online' && data.rdm) {
                url.value   = data.rdm;
                var isLocal = (data.rdm_mode === 'local');
                if (isLocal) {
                    label.textContent = 'Ã°Å¸Å¸Â  LAN Only Ã¢â‚¬â€ Akses via WiFi Sekolah';
                    label.style.color = '#fb923c';
                    dot.style.background = '#f97316';
                    dot.style.boxShadow  = '0 0 6px #f97316';
                } else {
                    label.textContent = 'Ã°Å¸Å¸Â¢ Online Ã¢â‚¬â€ ' + data.rdm.replace('https://', '');
                    label.style.color = '#4ade80';
                    dot.style.background = '#22c55e';
                    dot.style.boxShadow  = '0 0 6px #22c55e';
                }
                card.title  = data.rdm;
            } else {
                url.value   = '#';
                label.textContent = 'Ã°Å¸â€Â´ RDM tidak aktif';
                label.style.color = '#f87171';
                dot.style.background = '#ef4444';
                dot.style.boxShadow  = 'none';
            }
        })
        .catch(function() {
            if (label) {
                label.textContent = 'Ã¢Å¡Â Ã¯Â¸Â Tidak dapat terhubung';
                label.style.color = '#fbbf24';
            }
        });
}

// Jalankan saat halaman load dan setiap 30 detik
fetchRdmLink();
setInterval(fetchRdmLink, 30000);
</script>
<?php endif; ?>




