<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch active accesses for this guru
$akses = [];
try {
    $stmt = $core_db->prepare("SELECT app_code FROM apk_akses_guru WHERE guru_id = ?");
    $stmt->execute([$_SESSION['guru_id']]);
    $akses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Cek Wali Kelas
    $stmt_wk = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
    $stmt_wk->execute(['gid' => $_SESSION['guru_id']]);
    if ($stmt_wk->fetch()) {
        $akses[] = 'chat_siswa';
    }

    // Cek Guru Wali (Mentor)
    $stmt_gwm = $core_db->prepare("SELECT COUNT(*) FROM guru_wali_siswa WHERE guru_id = ?");
    $stmt_gwm->execute([$_SESSION['guru_id']]);
    if ($stmt_gwm->fetchColumn() > 0) {
        $akses[] = 'chat_mentor';
    }
} catch (Exception $e) {}

// Akses default untuk semua guru
$akses[] = 'absen_pribadi';
$akses[] = 'nilai_harian';
$akses[] = 'tugas_daring';
$akses[] = 'perpus_digital';

// Hitung izin pending untuk badge (hanya jika punya akses admin_izin)
$pending_izin_count = 0;
if (in_array('admin_izin', $akses)) {
    try {
        $stmt_pending = $siakad_db->query("SELECT COUNT(*) FROM ajuan_izin_guru WHERE status = 'Pending'");
        $pending_izin_count = (int)$stmt_pending->fetchColumn();
    } catch (Exception $e) {}
}

// Definisi aplikasi (Harus sama dengan yang di pengaturan Admin)
$apk_apps = [
    'Akademik & Pribadi' => [
        'absen_pribadi' => ['icon' => 'fa-calendar-check', 'name' => 'Rekap Absen', 'color' => '#4f46e5', 'bg' => '#e0e7ff', 'url' => 'absen_guru.php'],
        'nilai_harian' => ['icon' => 'fa-file-pen', 'name' => 'Nilai Harian', 'color' => '#d97706', 'bg' => '#fffbeb', 'url' => 'nilai_harian.php'],
        'tugas_daring' => ['icon' => 'fa-laptop-file', 'name' => 'Tugas Daring', 'color' => '#ef4444', 'bg' => '#fef2f2', 'url' => 'guru_tugas_mandiri.php'],
        'perpus_digital' => ['icon' => 'fa-book-open-reader', 'name' => 'E-Library', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'url' => 'https://e-library.masmifhda.sch.id/']
    ],
    'Keuangan & Tabungan' => [
        'kasir_tabungan' => ['icon' => 'fa-wallet', 'name' => 'Kasir Tab.', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'url' => 'admin_tabungan.php'],
        'kasir_pembayaran' => ['icon' => 'fa-calculator', 'name' => 'Kasir', 'color' => '#8b5cf6', 'bg' => '#f3e8ff', 'url' => 'admin_kasir.php'],
        'bku_bendahara' => ['icon' => 'fa-hand-holding-dollar', 'name' => 'BKU Bend.', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'admin_bendahara.php'],
        'bendahara_bos' => ['icon' => 'fa-landmark', 'name' => 'Bend. BOS', 'color' => '#f43f5e', 'bg' => '#ffe4e6', 'url' => 'bendahara_bos.php']
    ],
    'Absensi & Perizinan' => [
        'scan_siswa' => ['icon' => 'fa-qrcode', 'name' => 'Scan Siswa', 'color' => '#0ea5e9', 'bg' => '#e0f2fe', 'url' => 'scanner_siswa.php'],
        'scan_guru' => ['icon' => 'fa-expand', 'name' => 'Scan Guru', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'url' => 'scanner_guru.php'],
        'admin_izin' => ['icon' => 'fa-clipboard-check', 'name' => 'Persetujuan Izin', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'url' => 'admin_izin.php']
    ],
    'Kepala Madrasah' => [
        'mon_keuangan' => ['icon' => 'fa-chart-line', 'name' => 'Mon. Keuangan', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'url' => 'monitoring_keuangan.php'],
        'mon_bos' => ['icon' => 'fa-chart-pie', 'name' => 'Monitor BOS', 'color' => '#6366f1', 'bg' => '#eef2ff', 'url' => 'monitoring_bos.php'],
        'qr_absen_guru' => ['icon' => 'fa-qrcode', 'name' => 'QR Absen Guru', 'color' => '#4f46e5', 'bg' => '#e0e7ff', 'url' => 'kamad_qr_absen.php'],
        'kurikulum_pkg' => ['icon' => 'fa-award', 'name' => 'Penilaian PKG', 'color' => '#eab308', 'bg' => '#fefce8', 'url' => 'wakakur_pkg.php']
    ],
    'Waka Kurikulum' => [
        'wakakur_jadwal' => ['icon' => 'fa-calendar-check', 'name' => 'Jadwal KBM', 'color' => '#d946ef', 'bg' => '#fdf4ff', 'url' => 'wakakur_jadwal_kbm.php'],
        'mon_jurnal' => ['icon' => 'fa-book-open', 'name' => 'Monitor Jurnal', 'color' => '#0ea5e9', 'bg' => '#e0f2fe', 'url' => 'wakakur_monitor_jurnal.php'],
        'val_ekinerja' => ['icon' => 'fa-clipboard-check', 'name' => 'Supervisi Adm.', 'color' => '#d946ef', 'bg' => '#fdf4ff', 'url' => 'wakakur_supervisi_adm.php'],
        'supervisi_kunjungan' => ['icon' => 'fa-chalkboard-user', 'name' => 'Kunjungan Kelas', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'url' => 'wakakur_supervisi_kunjungan.php'],
        'supervisi_kelas' => ['icon' => 'fa-person-chalkboard', 'name' => 'Supervisi Kelas', 'color' => '#d97706', 'bg' => '#fffbeb', 'url' => 'wakakur_supervisi_kelas.php'],
        'mon_penilaian' => ['icon' => 'fa-clipboard-list', 'name' => 'Supervisi Nilai', 'color' => '#c026d3', 'bg' => '#fdf4ff', 'url' => 'wakakur_supervisi_nilai.php'],
        'mon_elearning' => ['icon' => 'fa-laptop-file', 'name' => 'Mon. E-Learning', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'wakakur_monitor_elearning.php']
    ],
    'Waka Kesiswaan' => [
        'wakasis_poin' => ['icon' => 'fa-clipboard-list', 'name' => 'Poin Log', 'color' => '#ef4444', 'bg' => '#fef2f2', 'url' => 'wakasis_poin.php'],
        'wakasis_kehadiran' => ['icon' => 'fa-chart-pie', 'name' => 'Kehadiran', 'color' => '#4338ca', 'bg' => '#e0e7ff', 'url' => 'wakasis_kehadiran.php'],
        'absen_qr' => ['icon' => 'fa-qrcode', 'name' => 'QR Absen Siswa', 'color' => '#9333ea', 'bg' => '#f3e8ff', 'url' => 'wakasis_qr_absen.php'],
        'absen_mapel' => ['icon' => 'fa-book', 'name' => 'Absen Mapel', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'waka_monitor_absen_mapel.php']
    ],
    'Bimbingan Konseling' => [
        'bk_jurnal' => ['icon' => 'fa-book-open', 'name' => 'Jurnal BK', 'color' => '#6366f1', 'bg' => '#eef2ff', 'url' => 'bk_jurnal.php'],
        'bk_poin' => ['icon' => 'fa-triangle-exclamation', 'name' => 'Poin Disiplin', 'color' => '#ef4444', 'bg' => '#fef2f2', 'url' => 'bk_poin.php']
    ],
    'Wali Kelas' => [
        'wali_siswa' => ['icon' => 'fa-users', 'name' => 'Data Siswa', 'color' => '#6366f1', 'bg' => '#eef2ff', 'url' => 'wali_kelas_siswa.php'],
        'wali_jurnal' => ['icon' => 'fa-clipboard-list', 'name' => 'Jurnal Kelas', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'url' => 'wali_kelas_jurnal.php'],
        'wali_absen' => ['icon' => 'fa-user-check', 'name' => 'Rekap Absen', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'wali_kelas_absen.php'],
        'wali_buku' => ['icon' => 'fa-list-check', 'name' => 'Buku Kerja', 'color' => '#0ea5e9', 'bg' => '#e0f2fe', 'url' => 'wali_kelas_program.php'],
        'wali_poin' => ['icon' => 'fa-circle-exclamation', 'name' => 'Log Disiplin', 'color' => '#ef4444', 'bg' => '#fef2f2', 'url' => 'dalam_pengembangan.php'],
        'chat_siswa' => ['icon' => 'fa-comment-dots', 'name' => 'Chat Siswa', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'chat_list_guru.php']
    ],
    'Pembina Ekstra' => [
        'ekstra_daftar' => ['icon' => 'fa-list-check', 'name' => 'Daftar Ekstra', 'color' => '#10b981', 'bg' => '#ecfdf5', 'url' => 'ekstra_pembina.php']
    ],
    'Guru Wali (Mentor)' => [
        'wali_asuh_siswa' => ['icon' => 'fa-users', 'name' => 'Daftar Anak Asuh', 'color' => '#ec4899', 'bg' => '#fdf2f8', 'url' => 'guru_wali.php?action=daftar'],
        'wali_asuh_pantau' => ['icon' => 'fa-chart-line', 'name' => 'Pantau Akademik', 'color' => '#06b6d4', 'bg' => '#ecfeff', 'url' => 'guru_wali.php?action=pantau'],
        'wali_asuh_jurnal' => ['icon' => 'fa-handshake-angle', 'name' => 'Catatan Bimbingan', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'url' => 'guru_wali.php?action=catatan'],
        'chat_mentor' => ['icon' => 'fa-comment-dots', 'name' => 'Chat Anak Asuh', 'color' => '#ec4899', 'bg' => '#fdf2f8', 'url' => 'chat_list_mentor.php']
    ]
];

$page_title = 'Aplikasi Saya';
include 'header.php';
?>

<style>
    @keyframes pulse-badge {
        0%   { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.5); }
        70%  { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(239,68,68,0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Delegasi</p>
            <h1>Aplikasi Saya</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px;">Daftar Aplikasi</h3>
        
        <?php 
        $hasAnyApp = false;
        foreach ($apk_apps as $catName => $apps): 
            // Check if any app in this category is accessible
            $activeApps = [];
            foreach ($apps as $code => $app) {
                if (in_array($code, $akses)) {
                    $activeApps[$code] = $app;
                }
            }

            if (count($activeApps) > 0):
                $hasAnyApp = true;
        ?>
            <div style="margin-bottom: 24px;">
                <h4 style="margin: 0 0 12px 0; font-size: 0.85rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">
                    <?= $catName ?>
                </h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                    <?php foreach ($activeApps as $code => $app): 
                            // Cek apakah ada badge untuk app ini
                            $badge_count = 0;
                            if ($code === 'admin_izin') $badge_count = $pending_izin_count;
                        ?>
                        <a href="<?= htmlspecialchars($app['url']) ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px; position: relative;">
                            <div style="position: relative; width: 45px; height: 45px;">
                                <div style="width: 45px; height: 45px; border-radius: 14px; background: <?= $app['bg'] ?>; color: <?= $app['color'] ?>; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fa-solid <?= $app['icon'] ?>"></i>
                                </div>
                                <?php if ($code === 'chat_siswa' || $code === 'chat_mentor'): ?>
                                <div class="fab-badge chat-badge-indicator" style="display: none; position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 100px; min-width: 18px; height: 18px; font-size: 0.65rem; font-weight: 800; align-items: center; justify-content: center; padding: 0 4px; border: 2px solid white; box-shadow: 0 2px 4px rgba(239,68,68,0.4); animation: pulse-badge 1.5s infinite; z-index: 10;">0</div>
                                <?php elseif ($badge_count > 0): ?>
                                <div style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 100px; min-width: 18px; height: 18px; font-size: 0.65rem; font-weight: 800; display: flex; align-items: center; justify-content: center; padding: 0 4px; border: 2px solid white; box-shadow: 0 2px 4px rgba(239,68,68,0.4); animation: pulse-badge 1.5s infinite;">
                                    <?= $badge_count > 99 ? '99+' : $badge_count ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;"><?= $app['name'] ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php 
            endif;
        endforeach; 
        ?>

        <?php if (!$hasAnyApp): ?>
            <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                <i class="fa-solid fa-folder-open" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <p style="margin: 0; font-size: 0.9rem;">Tidak ada aplikasi yang didelegasikan ke akun Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
