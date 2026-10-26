<?php
// GHTech Analytics Tracking
$_ghtech_key = "4a0fef55431a38a8ee6317c512178502";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}
// Track IP langsung
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip));
// Update username setelah session aktif
register_shutdown_function(function() {
    global $_ghtech_key, $_ghtech_ip;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_u = "";
        if (isset($_SESSION["guru_nama"])) { $_u = $_SESSION["guru_nama"]; }
        elseif (isset($_SESSION["siswa_nama"])) { $_u = $_SESSION["siswa_nama"]; }
        elseif (isset($_SESSION["nama"])) { $_u = $_SESSION["nama"]; }
        if (!empty($_u)) {
            @file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_u));
        }
    }
});
?>
<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil data siswa
$stmt = $core_db->prepare("SELECT * FROM siswa WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika alumni, alihkan ke dashboard alumni
if ($siswa['status'] === 'Alumni') {
    header("Location: alumni_dashboard.php");
    exit;
}

// Nama Kelas
$nama_kelas = "-";
if (!empty($siswa['kelas_id'])) {
    try {
        $stmt_k = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = :kid");
        $stmt_k->execute(['kid' => $siswa['kelas_id']]);
        $k = $stmt_k->fetch();
        if ($k) $nama_kelas = $k['nama_kelas'];
    } catch(PDOException $e){}
}

// Keuangan Singkat
$stmt_t = $core_db->prepare("SELECT t.*, (SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as jumlah_terbayar FROM keuangan_tagihan t WHERE t.siswa_id = :siswa_id");
$stmt_t->execute(['siswa_id' => $siswa_id]);
$tagihans = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

$total_tunggakan = 0;
$lunas_bulan_ini = false;
$current_month = date('Y-m');

foreach ($tagihans as $t) {
    if ($t['status'] != 'Lunas') {
        $total_tunggakan += ($t['jumlah_tagihan'] - $t['jumlah_terbayar']);
    } else {
        if (strpos($t['jatuh_tempo'], $current_month) === 0) {
            $lunas_bulan_ini = true;
        }
    }
}

// Absensi Hari Ini
$absen_hari_ini = ['status' => 'Belum Absen', 'jam' => '-', 'kode' => '-'];
try {
    $today = date('Y-m-d');
    $stmt_a = $siakad_db->prepare("SELECT * FROM absensi_siswa WHERE siswa_id = :id AND tanggal = :tgl LIMIT 1");
    $stmt_a->execute(['id' => $siswa_id, 'tgl' => $today]);
    $absen = $stmt_a->fetch(PDO::FETCH_ASSOC);
    if ($absen) {
        $stat = $absen['status'];
        if (in_array(strtolower($stat), ['hadir', 'sakit', 'izin', 'alpa', 'bolos'])) {
            $absen_hari_ini['status'] = ucfirst(strtolower($stat));
            $absen_hari_ini['kode'] = substr(ucfirst(strtolower($stat)), 0, 1);
        } else {
            $absen_hari_ini['kode'] = $stat;
            $absen_hari_ini['status'] = ($stat=='H'?'Hadir':($stat=='S'?'Sakit':($stat=='I'?'Izin':($stat=='B'?'Bolos':'Alpa'))));
        }
        $absen_hari_ini['jam'] = isset($absen['created_at']) ? date('H:i', strtotime($absen['created_at'])) : '-';
    }
} catch (Exception $e) {}

// Cek Pesan Belum Terbaca (Chat Wali Kelas)
$unread_chat = 0;
try {
    $stmt_unread = $core_db->prepare("SELECT COUNT(*) FROM pesan_chat WHERE penerima_role = 'siswa' AND penerima_id = :sid AND is_read = 0");
    $stmt_unread->execute(['sid' => $siswa_id]);
    $unread_chat = $stmt_unread->fetchColumn();
} catch (Exception $e) {}

// Tabungan
$tabungan = null;
try {
    $stmt_tab = $core_db->prepare("SELECT * FROM keuangan_tabungan WHERE siswa_id = :id LIMIT 1");
    $stmt_tab->execute(['id' => $siswa_id]);
    $tabungan = $stmt_tab->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Pengumuman
$pengumumans = [];
try {
    $stmt_p = $core_db->prepare("SELECT * FROM pengumuman WHERE tanggal_berakhir IS NULL OR tanggal_berakhir >= CURDATE() ORDER BY tanggal_dibuat DESC LIMIT 5");
    $stmt_p->execute();
    $pengumumans = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Jadwal Pelajaran
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
        SELECT jp.*, m.nama_mapel, k.nama_kelas, g.nama as nama_guru
        FROM jadwal_pelajaran jp
        JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
        JOIN aula_db.kelas k ON jp.kelas_id = k.id
        JOIN aula_db.guru g ON jp.guru_id = g.id
        WHERE jp.kelas_id = :kelas_id AND jp.hari = :hari AND jp.tahun_ajaran_id = :ta_id AND jp.semester = :semester
        ORDER BY jp.jam_mulai ASC
    ");
    $stmt_jadwal->execute([
        'kelas_id' => $siswa['kelas_id'],
        'hari' => $hari_ini,
        'ta_id' => $ta_id,
        'semester' => (string)$sem_num
    ]);
    $jadwal_hari_ini = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

    $stmt_jadwal->execute([
        'kelas_id' => $siswa['kelas_id'],
        'hari' => $besok,
        'ta_id' => $ta_id,
        'semester' => (string)$sem_num
    ]);
    $jadwal_besok = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Beranda';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <div class="greeting" style="padding-right: 15px; flex: 1;">
            <p style="margin-bottom: 2px;">Selamat Datang, Wali Murid</p>
            <h1 style="margin: 0; font-size: 20px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal; word-break: break-word;"><?= htmlspecialchars($siswa['nama']) ?></h1>
        </div>
        <div class="avatar" style="overflow: hidden;">
            <?php if(!empty($siswa['foto'])): ?>
                <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user-tie\'></i>';">
            <?php else: ?>
                <i class="fa-solid fa-user-tie"></i>
            <?php endif; ?>
        </div>
    </div>
    <div style="background: rgba(255,255,255,0.15); padding: 10px 15px; border-radius: 12px; font-size: 0.85rem; display: flex; align-items: center; gap: 10px; backdrop-filter: blur(5px);">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Akses Data Terenkripsi Aman</span>
    </div>
</div>

<div class="main-content">

    <!-- Animasi Liburan Dihapus -->

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

    <h3 class="section-title">Ringkasan Informasi</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
        <!-- Card Keuangan -->
        <a href="tagihan.php" style="background: #fff; border-radius: 16px; padding: 15px; text-decoration: none; color: inherit; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 1px solid #f1f5f9; transition: 0.2s;">
            <div style="background: #e0f2fe; color: #0284c7; width: 45px; height: 45px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.2rem; margin-bottom: 12px;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Keuangan</div>
            <?php if ($total_tunggakan <= 0): ?>
                <div style="font-size: 1rem; font-weight: 900; color: #10b981;">Aman / Lunas</div>
            <?php else: ?>
                <div style="font-size: 1rem; font-weight: 900; color: #ef4444;"><?= formatRupiah($total_tunggakan) ?></div>
            <?php endif; ?>
        </a>

        <!-- Card Absensi -->
        <a href="absen.php" style="background: #fff; border-radius: 16px; padding: 15px; text-decoration: none; color: inherit; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 1px solid #f1f5f9; transition: 0.2s;">
            <div style="background: #ecfdf5; color: #10b981; width: 45px; height: 45px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.2rem; margin-bottom: 12px;">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Absen Hari Ini</div>
            <div style="font-size: 1rem; font-weight: 900; color: <?= ($absen_hari_ini['kode']=='H' ? '#10b981' : ($absen_hari_ini['kode']=='-' ? '#f59e0b' : '#ef4444')) ?>;">
                <?= htmlspecialchars($absen_hari_ini['status']) ?>
            </div>
            <?php if($absen_hari_ini['jam'] !== '-'): ?>
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px; font-weight: 600;"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($absen_hari_ini['jam']) ?></div>
            <?php endif; ?>
        </a>
    </div>

    <!-- ================= JADWAL BUTTON ================= -->
    <button onclick="document.getElementById('modal-jadwal').style.display='flex'" style="width: 100%; padding: 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 25px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fa-solid fa-calendar-alt"></i> Lihat Jadwal Pelajaran
    </button>

    <!-- Modal Jadwal -->
    <div id="modal-jadwal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter: blur(3px);">
        <div style="background:white; border-radius:12px; width:100%; max-width:500px; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden;">
            <div style="padding:15px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:1.1rem; color:#0f172a;"><i class="fa-solid fa-calendar-day" style="color: #3b82f6; margin-right: 5px;"></i> Jadwal Pelajaran</h3>
                <button onclick="document.getElementById('modal-jadwal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">&times;</button>
            </div>
            <div style="padding:15px 20px; overflow-y:auto; flex:1; background: #f8fafc;">
                
                <h4 style="margin-top:0; margin-bottom: 10px; font-size: 0.95rem; color: #3b82f6;">Hari Ini (<?= $hari_ini ?>)</h4>
                <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <?php if (empty($jadwal_hari_ini)): ?>
                        <div style="text-align: center; padding: 15px 10px; color: #64748b;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"></i>
                            <p style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tidak ada jadwal pelajaran hari ini.</p>
                            <p style="font-size: 0.8rem; color: #64748b; margin-top: 3px;">Waktunya istirahat atau belajar mandiri! 😊</p>
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
                                <div style="background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: bold; padding: 5px 10px; border-radius: 30px; border: 1px solid #bfdbfe; text-align: right;">
                                    <i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($j['nama_guru']) ?>
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
                            <p style="font-weight: 600; font-size: 0.9rem; color: #475569;">Tidak ada jadwal pelajaran besok.</p>
                            <p style="font-size: 0.8rem; color: #64748b; margin-top: 3px;">Manfaatkan waktu luang! 😊</p>
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
                                <div style="background: #ecfdf5; color: #10b981; font-size: 0.75rem; font-weight: bold; padding: 5px 10px; border-radius: 30px; border: 1px solid #a7f3d0; text-align: right;">
                                    <i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($j['nama_guru']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- ================= BERITA SEKOLAH TERBARU ================= -->
    <h3 class="section-title" style="margin-top: 15px;"><i class="fa-solid fa-newspaper" style="color: #f59e0b; margin-right: 5px;"></i> Berita Sekolah Terbaru</h3>
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
    // Ambil Berita Terbaru dari Website Utama via API Lokal
    fetch('api_berita_sekolah.php')
        .then(response => response.json())
        .then(res => {
            const container = document.getElementById('berita-container');
            if (res.status === 'success') {
                let html = '';
                res.data.forEach(b => {
                    html += `
                    <div onclick="window.open('${b.link}', '_blank')" style="background: #ffffff; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; cursor: pointer; display: flex; gap: 15px; padding: 12px; transition: 0.2s;">
                        <div style="width: 100px; height: 100px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: #f1f5f9;">
                            <img src="${b.image}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/public/assets/img/placeholder.png';">
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                            <div style="font-size: 0.7rem; color: #10b981; font-weight: bold; margin-bottom: 4px;">
                                <i class="fa-regular fa-calendar"></i> ${b.date}
                            </div>
                            <h4 style="margin: 0 0 6px 0; font-size: 0.95rem; color: #1e293b; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${b.title}</h4>
                            <p style="margin: 0; font-size: 0.75rem; color: #64748b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${b.desc}</p>
                        </div>
                    </div>`;
                });
                // Tambahkan tombol lihat semua berita
                html += `
                <div style="text-align: center; margin-top: 5px;">
                    <button onclick="window.open('https://masmifhda.sch.id/posts/news/', '_blank')" style="background: #f1f5f9; color: #3b82f6; border: 1px solid #bfdbfe; padding: 10px 20px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: 0.2s;">
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
            document.getElementById('berita-container').innerHTML = `<div style="background: #ffffff; border-radius: 12px; padding: 15px; text-align: center; color: #94a3b8; font-size: 0.85rem;"><i class="fa-solid fa-wifi"></i> Gagal terhubung ke server berita.</div>`;
        });

    // Debug alert untuk komandan
    // alert("Memeriksa koneksi Android... Status: " + typeof Android);
    
    if (typeof Android !== "undefined" && typeof Android.setOneSignalUser === "function") {
        Android.setOneSignalUser("siswa_<?= addslashes($siswa_id) ?>");
        // alert("Berhasil mengirim ID Siswa ke aplikasi Android!");
    } else {
        // alert("Peringatan: Aplikasi Android belum mengenali fitur OneSignal (JavascriptInterface belum terpasang atau APK belum di-rebuild).");
    }
</script>

<?php include 'footer.php'; ?>
