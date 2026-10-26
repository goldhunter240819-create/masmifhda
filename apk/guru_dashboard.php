<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil data guru
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user_id = $guru['user_id'];

// Cek Absensi Hari Ini
$stmt_abs = $siakad_db->prepare("SELECT * FROM absensi_guru WHERE guru_id = :gid AND tanggal = CURDATE() LIMIT 1");
$stmt_abs->execute(['gid' => $guru_id]);
$absensi_hari_ini = $stmt_abs->fetch(PDO::FETCH_ASSOC);

// Cek Jurnal Mengajar Bulan Ini
$jml_jurnal = 0;
try {
    $stmt_jurnal = $siakad_db->prepare("SELECT COUNT(*) FROM jurnal_guru WHERE guru_id = :gid AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
    $stmt_jurnal->execute(['gid' => $guru_id]);
    $jml_jurnal = $stmt_jurnal->fetchColumn();
} catch (Exception $e) {}

// Cek Kehadiran Bulan Ini
$jml_hadir = 0;
try {
    $stmt_hadir = $siakad_db->prepare("SELECT COUNT(*) FROM absensi_guru WHERE guru_id = :gid AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) AND status IN ('Hadir', 'Terlambat')");
    $stmt_hadir->execute(['gid' => $guru_id]);
    $jml_hadir = $stmt_hadir->fetchColumn();
} catch (Exception $e) {}

// Cek Delegasi Akses Admin
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid");
$stmt_app->execute(['uid' => $user_id]);
$delegated_apps = $stmt_app->fetchAll(PDO::FETCH_COLUMN);

// Cek Wali Kelas
$is_wali_kelas = false;
try {
    $stmt_wk = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
    $stmt_wk->execute(['gid' => $guru_id]);
    if ($stmt_wk->fetch()) {
        $is_wali_kelas = true;
    }
} catch (Exception $e) {}

$app_details = [
    'keuangan' => ['title' => 'Admin Pembayaran', 'icon' => 'fa-wallet', 'url' => '/keuangan', 'color' => '#10b981'],
    'siakad' => ['title' => 'Admin SIAKAD', 'icon' => 'fa-graduation-cap', 'url' => '/siakad', 'color' => '#3b82f6'],
    'cbt' => ['title' => 'Admin CBT', 'icon' => 'fa-laptop-code', 'url' => '/cbt', 'color' => '#8b5cf6'],
    'absensi' => ['title' => 'Admin Absensi', 'icon' => 'fa-user-clock', 'url' => '/absensi', 'color' => '#f59e0b'],
    'eraport' => ['title' => 'Admin E-Raport', 'icon' => 'fa-book', 'url' => '/eraport', 'color' => '#06b6d4'],
    'elearning' => ['title' => 'Admin E-Learning', 'icon' => 'fa-chalkboard-user', 'url' => '/elearning', 'color' => '#ec4899'],
    'persuratan' => ['title' => 'Admin Surat', 'icon' => 'fa-envelope-open-text', 'url' => '/persuratan', 'color' => '#eab308'],
    'inventaris' => ['title' => 'Admin Inventaris', 'icon' => 'fa-boxes-stacked', 'url' => '/inventaris', 'color' => '#64748b'],
    'perpustakaan' => ['title' => 'Admin Perpus', 'icon' => 'fa-book-open-reader', 'url' => '/perpus', 'color' => '#14b8a6'],
    'bos' => ['title' => 'Keuangan BOS', 'icon' => 'fa-landmark', 'url' => 'admin_bos.php', 'color' => '#059669']
];

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$page_title = 'Portal Guru';

// Pengumuman
$pengumumans = [];
try {
    $stmt_p = $core_db->prepare("SELECT * FROM pengumuman WHERE tanggal_berakhir IS NULL OR tanggal_berakhir >= CURDATE() ORDER BY tanggal_dibuat DESC LIMIT 5");
    $stmt_p->execute();
    $pengumumans = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// --- KONDISI KHUSUS KAMAD & GURU BIASA ---
$is_kamad = (isset($guru['is_kamad']) && $guru['is_kamad'] == 1);

$totalPemasukan = 0;
$totalPengeluaran = 0;
$saldo = 0;
$tagihanBelumBayar = 0;
$jadwal_hari_ini = [];
$jadwal_besok = [];
$hari_ini = '';
$besok = '';

try {
        $days_ind = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $hari_ini = $days_ind[date('w')];
        $besok = $days_ind[date('w', strtotime('+1 day'))];
        
        // Ambil tahun ajaran aktif
        $ta_id = 1;
        $stmt_peng = $siakad_db->query("SELECT tahun_ajaran_aktif, semester_aktif FROM pengaturan LIMIT 1");
        $peng = $stmt_peng->fetch(PDO::FETCH_ASSOC);
        if ($peng) {
            $ta_name = $peng['tahun_ajaran_aktif'];
            $sem_name = strtolower(trim($peng['semester_aktif']));
            $sem_num = ($sem_name === 'genap' || $sem_name === '2') ? 2 : 1;
            
            $stmt_ta = $core_db->prepare("SELECT id FROM tahun_ajaran WHERE name = ? AND semester = ? LIMIT 1");
            $stmt_ta->execute([$ta_name, $sem_num]);
            $ta_row = $stmt_ta->fetch(PDO::FETCH_ASSOC);
            if ($ta_row) {
                $ta_id = $ta_row['id'];
            }
        }

        $stmt_jadwal = $siakad_db->prepare("
            SELECT jp.*, m.nama_mapel, k.nama_kelas 
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
            JOIN aula_db.kelas k ON jp.kelas_id = k.id
            WHERE jp.guru_id = :guru_id AND jp.hari = :hari AND jp.tahun_ajaran_id = :ta_id AND jp.semester = :semester
            ORDER BY jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([
            'guru_id' => $guru_id,
            'hari' => $hari_ini,
            'ta_id' => $ta_id,
            'semester' => (string)$sem_num
        ]);
        $jadwal_hari_ini = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt_jadwal->execute([
            'guru_id' => $guru_id,
            'hari' => $besok,
            'ta_id' => $ta_id,
            'semester' => (string)$sem_num
        ]);
        $jadwal_besok = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting" style="padding-right: 15px; flex: 1;">
            <p style="margin-bottom: 2px;">Selamat Datang, Guru</p>
            <h1 style="margin: 0; font-size: 20px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal; word-break: break-word;"><?= htmlspecialchars($guru['nama']) ?></h1>
        </div>
        <div class="avatar" style="overflow: hidden; color: #0ea5e9;">
            <?php 
                $foto_src = '';
                if (!empty($guru['foto'])) {
                    if (file_exists(__DIR__ . '/../public/uploads/guru/' . $guru['foto'])) {
                        $foto_src = '/public/uploads/guru/' . $guru['foto'];
                    } elseif (file_exists(__DIR__ . '/../public/images/guru/' . $guru['foto'])) {
                        $foto_src = '/public/images/guru/' . $guru['foto'];
                    }
                }
                
                if ($foto_src) {
                    echo '<img src="'.htmlspecialchars($foto_src).'" style="width:100%; height:100%; object-fit:cover;" alt="Foto">';
                } else {
                    echo '<i class="fa-solid fa-chalkboard-user"></i>';
                }
            ?>
        </div>
    </div>
    <div style="background: rgba(255,255,255,0.15); padding: 10px 15px; border-radius: 12px; font-size: 0.85rem; display: flex; align-items: center; justify-content: space-between; backdrop-filter: blur(5px);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Akses Data Terenkripsi Aman</span>
        </div>
        <div style="font-weight:bold;"><?= date('d M Y') ?></div>
    </div>
</div>

<div class="main-content">
    <!-- Animasi Liburan Dihapus -->

    <!-- 1. Mading Pengumuman (Paling Atas) -->
    <?php if (count($pengumumans) > 0): ?>
    <h3 class="section-title"><i class="fa-solid fa-bullhorn"></i> Mading Pengumuman</h3>
    <div style="margin-bottom: 25px;">
        <?php foreach ($pengumumans as $p): ?>
        <div style="background: #ffffff; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #0ea5e9;">
            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 5px; font-weight: bold;">
                <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($p['tanggal_dibuat'])) ?>
            </div>
            <h4 style="margin: 0 0 8px 0; font-size: 1rem; color: #1e293b;"><?= htmlspecialchars($p['judul']) ?></h4>
            <p style="margin: 0; font-size: 0.9rem; color: #475569; line-height: 1.5; white-space: pre-wrap;"><?= htmlspecialchars($p['pesan']) ?></p>
            <?php if (!empty($p['url'])): ?>
            <div style="margin-top: 10px;">
                <a href="<?= htmlspecialchars($p['url']) ?>" style="display: inline-block; padding: 6px 12px; background: #f1f5f9; color: #0ea5e9; border-radius: 6px; font-size: 0.8rem; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-link"></i> Buka Tautan</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h3 class="section-title" style="margin-bottom: 10px; font-size: 0.95rem;">Ringkasan Aktivitas</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 20px;">
        
        <!-- Absensi Hari Ini -->
        <?php 
        $absen_icon = $absensi_hari_ini ? 'fa-check-circle' : 'fa-circle-xmark';
        $absen_bg = $absensi_hari_ini ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
        $absen_text = $absensi_hari_ini ? 'Sudah' : 'Belum';
        $text_color = '#ffffff';
        $subtext_color = 'rgba(255,255,255,0.85)';
        $border_color = 'rgba(255,255,255,0.3)';
        ?>
        <div style="grid-row: span 2; background: <?= $absen_bg ?>; border-radius: 12px; padding: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 12px; flex: 1;">
                <div style="font-size: 2.2rem; color: <?= $text_color ?>;"><i class="fa-solid <?= $absen_icon ?>"></i></div>
                <div style="text-align: left;">
                    <div style="font-size: 1.3rem; font-weight: 800; color: <?= $text_color ?>; line-height: 1;"><?= $absen_text ?></div>
                    <div style="font-size: 0.8rem; color: <?= $subtext_color ?>; margin-top: 4px; font-weight: 600;">Absen Hari Ini</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed <?= $border_color ?>; padding-top: 8px;">
                <div style="display: flex; gap: 5px; align-items: baseline;">
                    <span style="font-size: 0.65rem; color: <?= $subtext_color ?>; font-weight: 600;">Masuk:</span>
                    <span style="font-size: 0.9rem; font-weight: 800; color: <?= $text_color ?>;"><?= $absensi_hari_ini && $absensi_hari_ini['waktu_masuk'] ? substr($absensi_hari_ini['waktu_masuk'], 0, 5) : '--:--' ?></span>
                </div>
                <div style="display: flex; gap: 5px; align-items: baseline;">
                    <span style="font-size: 0.65rem; color: <?= $subtext_color ?>; font-weight: 600;">Pulang:</span>
                    <span style="font-size: 0.9rem; font-weight: 800; color: <?= $text_color ?>;"><?= $absensi_hari_ini && $absensi_hari_ini['waktu_keluar'] ? substr($absensi_hari_ini['waktu_keluar'], 0, 5) : '--:--' ?></span>
                </div>
            </div>
        </div>

        <!-- Total Hadir Bulan Ini -->
        <div style="background: #ffffff; border-radius: 12px; padding: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 10px; border-left: 3px solid #0ea5e9;">
            <div style="font-size: 1.1rem; color: #0ea5e9; background: #e0f2fe; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fa-solid fa-user-check"></i></div>
            <div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $jml_hadir ?> <span style="font-size:0.6rem; font-weight:normal; color: #64748b;">hari</span></div>
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 600; margin-top: 2px;">Hadir Bulan Ini</div>
            </div>
        </div>

        <!-- Jurnal Mengajar Bulan Ini -->
        <div style="background: #ffffff; border-radius: 12px; padding: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 10px; border-left: 3px solid #3b82f6;">
            <div style="font-size: 1.1rem; color: #3b82f6; background: #dbeafe; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fa-solid fa-book-open"></i></div>
            <div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $jml_jurnal ?> <span style="font-size:0.6rem; font-weight:normal; color: #64748b;">kali</span></div>
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 600; margin-top: 2px;">Isi Jurnal</div>
            </div>
        </div>

    </div>



    <!-- ================= JADWAL BUTTON ================= -->
    <button onclick="document.getElementById('modal-jadwal').style.display='flex'" style="width: 100%; padding: 15px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 25px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fa-solid fa-calendar-alt"></i> Lihat Jadwal Mengajar
    </button>

    <!-- Modal Jadwal -->
    <div id="modal-jadwal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter: blur(3px);">
        <div style="background:white; border-radius:12px; width:100%; max-width:500px; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden;">
            <div style="padding:15px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:1.1rem; color:#0f172a;"><i class="fa-solid fa-calendar-day" style="color: #3b82f6; margin-right: 5px;"></i> Jadwal Mengajar</h3>
                <button onclick="document.getElementById('modal-jadwal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">&times;</button>
            </div>
            <div style="padding:15px 20px; overflow-y:auto; flex:1; background: #f8fafc;">
                
                <h4 style="margin-top:0; margin-bottom: 10px; font-size: 0.95rem; color: #3b82f6;">Hari Ini (<?= $hari_ini ?>)</h4>
                <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <?php if (empty($jadwal_hari_ini)): ?>
                        <div style="text-align: center; padding: 15px 10px; color: #64748b;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"></i>
                            <p style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tidak ada jadwal mengajar hari ini.</p>
                            <p style="font-size: 0.8rem; color: #64748b; margin-top: 3px;">Selamat beristirahat & berkumpul dengan keluarga! 😊</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jadwal_hari_ini as $j): ?>
                            <div style="border-bottom: 1px solid #f1f5f9; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                <div>
                                    <div style="font-weight: bold; color: #1e293b; font-size: 0.9rem;"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                        <i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                                    </div>
                                </div>
                                <div style="background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: bold; padding: 5px 10px; border-radius: 30px; border: 1px solid #bfdbfe;">
                                    Kelas <?= htmlspecialchars($j['nama_kelas']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h4 style="margin-top:0; margin-bottom: 10px; font-size: 0.95rem; color: #10b981;">Besok (<?= $besok ?>)</h4>
                <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 10px;">
                    <?php if (empty($jadwal_besok)): ?>
                        <div style="text-align: center; padding: 15px 10px; color: #64748b;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"></i>
                            <p style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tidak ada jadwal mengajar besok.</p>
                            <p style="font-size: 0.8rem; color: #64748b; margin-top: 3px;">Waktunya untuk istirahat! 😊</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jadwal_besok as $j): ?>
                            <div style="border-bottom: 1px solid #f1f5f9; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                <div>
                                    <div style="font-weight: bold; color: #1e293b; font-size: 0.9rem;"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                        <i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                                    </div>
                                </div>
                                <div style="background: #ecfdf5; color: #10b981; font-size: 0.75rem; font-weight: bold; padding: 5px 10px; border-radius: 30px; border: 1px solid #a7f3d0;">
                                    Kelas <?= htmlspecialchars($j['nama_kelas']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



    <!-- ================= POSTINGAN WEBSITE MADRASAH ================= -->
    <h3 class="section-title" style="margin-top: 15px;"><i class="fa-solid fa-newspaper" style="color: #f59e0b; margin-right: 5px;"></i> Berita Website Madrasah</h3>
    <div id="berita-container" style="margin-bottom: 25px;">
        <div style="text-align: center; padding: 20px; color: #94a3b8;">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 10px;"></i>
            <p style="font-size: 0.85rem; margin:0;">Memuat berita terbaru...</p>
        </div>
    </div>

    <!-- ================= POSTINGAN MEDIA SOSIAL ================= -->
    <h3 class="section-title" style="margin-top: 15px;"><i class="fa-solid fa-hashtag" style="color: #ec4899; margin-right: 5px;"></i> Media Sosial Madrasah</h3>
    <div style="margin-bottom: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        
        <!-- Tombol Instagram -->
        <button onclick="window.open('https://www.instagram.com/scc_miftahul_huda/', '_blank')" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(220, 39, 67, 0.2);">
            <i class="fa-brands fa-instagram" style="font-size: 1.2rem;"></i> Instagram
        </button>

        <!-- Tombol TikTok -->
        <button onclick="window.open('https://www.tiktok.com/@scc_miftahul_huda', '_blank')" style="background: #000000; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);">
            <i class="fa-brands fa-tiktok" style="font-size: 1.2rem;"></i> TikTok
        </button>

        <!-- Tombol Facebook -->
        <button onclick="window.open('https://www.facebook.com/profile.php?id=61582891674145', '_blank')" style="background: #1877f2; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(24, 119, 242, 0.2);">
            <i class="fa-brands fa-facebook" style="font-size: 1.2rem;"></i> Facebook
        </button>
        
        <!-- Tombol YouTube -->
        <button onclick="window.open('https://www.youtube.com/@SCC_MASMIFHDA', '_blank')" style="background: #ff0000; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(255, 0, 0, 0.2);">
            <i class="fa-brands fa-youtube" style="font-size: 1.2rem;"></i> YouTube
        </button>

        <!-- Tombol Threads -->
        <button onclick="window.open('https://www.threads.com/@scc_miftahul_huda?xmt=AQF0BauGbKJzCb_erSt2JkpZDlXaAwIMljJ2PxceuISmSGc', '_blank')" style="grid-column: span 2; background: #000000; color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);">
            <i class="fa-solid fa-at" style="font-size: 1.2rem;"></i> Threads
        </button>

    </div>

</div>

<script>
    // Ambil Postingan Terbaru dari Website Utama via API Lokal (dengan timeout)
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 detik timeout

    fetch('api_berita_sekolah.php', { signal: controller.signal })
        .then(response => response.json())
        .then(res => {
            clearTimeout(timeoutId);
            const container = document.getElementById('berita-container');
            if (res.status === 'success') {
                let html = '';
                res.data.forEach(b => {
                    const catColors = {
                        'News': { bg: '#dbeafe', text: '#2563eb' },
                        'Prestasi': { bg: '#fef3c7', text: '#d97706' },
                        'Pengumuman': { bg: '#d1fae5', text: '#059669' }
                    };
                    const cat = catColors[b.category] || { bg: '#f1f5f9', text: '#64748b' };
                    const catLabel = b.category || '';
                    const dateLabel = b.date ? `<i class="fa-regular fa-calendar"></i> ${b.date}` : '';

                    html += `
                    <div onclick="window.open('${b.link}', '_blank')" style="background: #ffffff; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; cursor: pointer; display: flex; gap: 15px; padding: 12px; transition: 0.2s;">
                        <div style="width: 100px; height: 100px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: #f1f5f9;">
                            <img src="${b.image}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/public/assets/img/placeholder.png';">
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px; flex-wrap: wrap;">
                                ${catLabel ? `<span style="font-size: 0.6rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: ${cat.bg}; color: ${cat.text};">${catLabel}</span>` : ''}
                                ${dateLabel ? `<span style="font-size: 0.65rem; color: #10b981; font-weight: bold;">${dateLabel}</span>` : ''}
                            </div>
                            <h4 style="margin: 0 0 6px 0; font-size: 0.95rem; color: #1e293b; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${b.title}</h4>
                            <p style="margin: 0; font-size: 0.75rem; color: #64748b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${b.desc}</p>
                        </div>
                    </div>`;
                });
                html += `
                <div style="text-align: center; margin-top: 5px;">
                    <button onclick="window.open('https://masmifhda.sch.id/posts', '_blank')" style="background: #f1f5f9; color: #3b82f6; border: 1px solid #bfdbfe; padding: 10px 20px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: 0.2s;">
                        Tampilkan Semua Berita <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                    </button>
                </div>
                `;
                container.innerHTML = html;
            } else {
                container.innerHTML = `<div style="background: #ffffff; border-radius: 12px; padding: 15px; text-align: center; color: #94a3b8; font-size: 0.85rem;"><i class="fa-solid fa-info-circle"></i> ${res.message}</div>`;
            }
        })
        .catch(err => {
            clearTimeout(timeoutId);
            document.getElementById('berita-container').innerHTML = `<div style="background: #ffffff; border-radius: 12px; padding: 15px; text-align: center; color: #94a3b8; font-size: 0.85rem;"><i class="fa-solid fa-wifi"></i> Gagal memuat berita. Coba refresh halaman.</div>`;
        });    if (typeof Android !== "undefined" && typeof Android.setOneSignalUser === "function") {
        Android.setOneSignalUser("guru_<?= addslashes($guru_id) ?>");
    }
</script>

<?php include 'footer_guru.php'; ?>
