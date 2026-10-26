<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$real_guru_id = $_SESSION['guru_id'];
$guru_id = $real_guru_id;

$is_inval = isset($_REQUEST['inval']) && $_REQUEST['inval'] == 1;
$target_guru_id = isset($_REQUEST['target_guru_id']) ? intval($_REQUEST['target_guru_id']) : 0;

if ($is_inval && $target_guru_id > 0) {
    $guru_id = $target_guru_id;
}

// AJAX endpoint untuk auto-jurnal daring
if (isset($_GET['action']) && $_GET['action'] == 'cek_daring') {
    $tgl = $_GET['tgl'] ?? '';
    $kls = (int)($_GET['kls'] ?? 0);
    $mpl = (int)($_GET['mpl'] ?? 0);
    
    $stmt_k = $siakad_db->prepare("SELECT id FROM kalender_akademik WHERE tipe = 'daring' AND ? BETWEEN tanggal_mulai AND tanggal_selesai LIMIT 1");
    $stmt_k->execute([$tgl]);
    $is_daring = $stmt_k->fetch() ? true : false;
    
    $materi_daring = '';
    if ($is_daring) {
        $stmt_tg = $siakad_db->prepare("SELECT judul, instruksi FROM tugas_daring WHERE guru_id = ? AND kelas_id = ? AND mata_pelajaran_id = ? AND tanggal = ? LIMIT 1");
        $stmt_tg->execute([$guru_id, $kls, $mpl, $tgl]);
        if ($tugas = $stmt_tg->fetch()) {
            $materi_daring = "Pembelajaran Daring: " . $tugas['judul'] . "\n\nInstruksi:\n" . $tugas['instruksi'];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['is_daring' => $is_daring, 'materi' => $materi_daring]);
    exit;
}

// Ambil data guru (target/absent teacher jika inval)
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Ambil data guru asli (substitute)
$stmt_real = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt_real->execute(['id' => $real_guru_id]);
$real_guru = $stmt_real->fetch(PDO::FETCH_ASSOC);
$target_guru_nama = $is_inval ? $guru['nama'] : '';

$message = '';
$status = '';

$pre_kelas = isset($_GET['pre_kelas']) ? $_GET['pre_kelas'] : '';
$pre_mapel = isset($_GET['pre_mapel']) ? $_GET['pre_mapel'] : '';
$pre_tanggal = isset($_GET['pre_tanggal']) ? $_GET['pre_tanggal'] : date('Y-m-d');
$pre_tugas = isset($_GET['tugas']) ? $_GET['tugas'] : '';

if ($is_inval && !empty($pre_tugas)) {
    $pre_tugas = "(Inval oleh: " . $real_guru['nama'] . ")\n\nTugas Inval:\n" . $pre_tugas;
} elseif ($is_inval) {
    $pre_tugas = "(Inval oleh: " . $real_guru['nama'] . ")";
}

// Proses POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas_id = intval($_POST['kelas_id']);
    $mapel_id = intval($_POST['mapel_id']);
    $tanggal = $_POST['tanggal'];
    $materi = $_POST['materi'];
    $keterangan = $_POST['keterangan'];
    
    // Validasi input
    if ($kelas_id > 0 && $mapel_id > 0 && !empty($tanggal) && !empty($materi)) {
        // Ambil tahun ajaran aktif
        $activeTahunId = 1;
        $activeSemester = 1;
        try {
            $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            if ($thn = $stmtTahun->fetch()) {
                $activeTahunId = $thn['id'];
                $activeSemester = $thn['semester'];
            }
        } catch (Exception $e) {}

        // Cek apakah guru boleh mengajar kelas+mapel ini (berdasarkan jadwal pelajaran ATAU mengajar)
        $cek_jadwal = $siakad_db->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND kelas_id = ? AND mata_pelajaran_id = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        $cek_jadwal->execute([$guru_id, $kelas_id, $mapel_id, $activeTahunId, $activeSemester]);
        
        $cek_mengajar = $siakad_db->prepare("SELECT id FROM mengajar WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        $cek_mengajar->execute([$guru_id, $kelas_id, $mapel_id, $activeTahunId, $activeSemester]);
        
        if (!$cek_jadwal->fetch() && !$cek_mengajar->fetch()) {
            $message = 'Akses ditolak: Anda tidak memiliki jadwal atau tugas untuk kelas/mapel tersebut di SIAKAD pada semester aktif.';
            $status = 'error';
        } else {
        try {
            $siakad_db->beginTransaction();
            
            // 1. Insert Jurnal
            $stmt_jurnal = $siakad_db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_jurnal->execute([$guru_id, $kelas_id, $mapel_id, $tanggal, $materi, $keterangan]);
            $jurnal_id = $siakad_db->lastInsertId();
            
            // 2. Insert Absensi Siswa Per Mapel (ke tabel TERPISAH, bukan absensi_siswa)
            if (isset($_POST['absensi']) && is_array($_POST['absensi'])) {
                $stmt_absen = $siakad_db->prepare("
                    INSERT INTO absensi_permapel 
                        (jurnal_id, guru_id, siswa_id, kelas_id, mapel_id, tanggal, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = VALUES(status)
                ");
                
                // Persiapan Integrasi Absen Pusat & Notifikasi
                $stmt_cek_pusat = $siakad_db->prepare("SELECT id, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmt_insert_pusat = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
                $stmt_update_pusat = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                
                $stmt_semua_absen = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");
                
                $stmt_mapel_name = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
                $stmt_mapel_name->execute([$mapel_id]);
                $mapel_info = $stmt_mapel_name->fetch();
                $nama_mapel = $mapel_info ? $mapel_info['nama_mapel'] : 'Mata Pelajaran';
                
                $stmt_siswa_info = $core_db->prepare("SELECT nama, user_id FROM siswa WHERE id = ?");
                
                require_once __DIR__ . '/../vendor/autoload.php';
                
                foreach ($_POST['absensi'] as $siswa_id_absen => $status_absen) {
                    $s_id = intval($siswa_id_absen);
                    
                    // ATURAN MUTLAK: Cek pusat, jika Izin/Sakit/Alpa maka override status guru
                    $stmt_cek_pusat->execute([$s_id, $tanggal]);
                    $pusat_row = $stmt_cek_pusat->fetch();
                    if ($pusat_row && in_array($pusat_row['status'], ['Izin', 'Sakit', 'Alpa', 'Alpha', 'A'])) {
                        $status_absen = $pusat_row['status'];
                    }
                    
                    // Simpan absen mapel
                    $stmt_absen->execute([$jurnal_id, $guru_id, $s_id, $kelas_id, $mapel_id, $tanggal, $status_absen]);
                    
                    // Logika Penentuan Status Pusat via fungsi sinkronisasi (Kombinasi dengan Gate Scan)
                    sinkronisasi_status_pusat($s_id, $tanggal, $core_db, $siakad_db);
                    
                    // Kirim Notifikasi OneSignal jika absen = Sakit/Izin/Alpa
                    if (in_array($status_absen, ['Sakit', 'Izin', 'Alpa'])) {
                        $stmt_siswa_info->execute([$s_id]);
                        $siswa = $stmt_siswa_info->fetch();
                        
                        if ($siswa && $siswa['user_id']) {
                            $judul = "Info Absensi Mapel";
                            $status_teks = strtoupper($status_absen);
                            $pesan = "Yth. Wali dari {$siswa['nama']}, ananda dinyatakan {$status_teks} pada mata pelajaran {$nama_mapel} hari ini.";
                            
                            try {
                                \App\Services\OneSignalService::sendNotification($judul, $pesan, null, $siswa['user_id']);
                            } catch (\Exception $e) {}
                        }
                    }
                }
            }
            
            $siakad_db->commit();
            $message = 'Jurnal dan absensi berhasil disimpan!';
            $status = 'success';
        } catch (PDOException $e) {
            $siakad_db->rollBack();
            $message = 'Gagal menyimpan data: ' . $e->getMessage();
            $status = 'error';
        }
        } // end else (guru berhak)
    } else {
        $message = 'Harap isi semua kolom wajib!';
        $status = 'warning';
    }
}

// Ambil tahun ajaran aktif
$activeTahunId = 1;
$activeSemester = 1;
try {
    $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
        $activeSemester = $thn['semester'];
    }
} catch (Exception $e) {}

// Ambil kelas & mapel yang diampu guru ini (Gabungan dari tabel mengajar & jadwal_pelajaran)
$kelasList = [];
$mapelList = [];
try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas, k.tingkat
        FROM (
            SELECT kelas_id FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT kelas_id FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN aula_db.kelas k ON k.id = combined.kelas_id
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("
        SELECT DISTINCT mp.id, mp.nama_mapel
        FROM (
            SELECT mapel_id as mapel FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN mata_pelajaran mp ON mp.id = combined.mapel
        ORDER BY mp.nama_mapel
    ");
    $stmt_m->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $mapelList = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ==========================================
// DETEKSI JADWAL KOSONG GLOBAL 
// ==========================================
$global_tanggungan = [];
try {
    $hari_map = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'];
    
    $current_month = (int)date('m');
    $current_year = date('Y');
    if ($current_month >= 7) {
        $start_date = $current_year . '-07-01';
    } else {
        $start_date = $current_year . '-01-01';
    }
    $today = date('Y-m-d');
    
    $stmt_semua_jadwal = $siakad_db->prepare("
        SELECT jp.kelas_id, k.nama_kelas, jp.mata_pelajaran_id as mapel_id, mp.nama_mapel, jp.hari, MIN(jp.jam_mulai) as jam_mulai 
        FROM jadwal_pelajaran jp 
        JOIN aula_db.kelas k ON k.id = jp.kelas_id 
        JOIN mata_pelajaran mp ON mp.id = jp.mata_pelajaran_id 
        WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ?
        GROUP BY jp.hari, jp.kelas_id, jp.mata_pelajaran_id, k.nama_kelas, mp.nama_mapel
    ");
    $stmt_semua_jadwal->execute([$guru_id, $activeTahunId]);
    $all_jadwal = $stmt_semua_jadwal->fetchAll(PDO::FETCH_ASSOC);
    
    $jadwal_by_hari = [];
    foreach($all_jadwal as $j) {
        $jadwal_by_hari[$j['hari']][] = $j;
    }
    
    $stmt_cal = $siakad_db->prepare("SELECT tanggal_mulai, tanggal_selesai, keterangan, tipe, jam_mulai_kosong FROM kalender_akademik WHERE tanggal_mulai <= ? AND (tanggal_selesai >= ? OR tanggal_selesai IS NULL OR tanggal_selesai = '0000-00-00')");
    $stmt_cal->execute([$today, $start_date]);
    $kalender_data = $stmt_cal->fetchAll(PDO::FETCH_ASSOC);
    
    $kalender_dates = [];
    foreach($kalender_data as $cal) {
        $t_start = strtotime($cal['tanggal_mulai']);
        $t_end = (!empty($cal['tanggal_selesai']) && $cal['tanggal_selesai'] != '0000-00-00') ? strtotime($cal['tanggal_selesai']) : $t_start;
        for($t = $t_start; $t <= $t_end; $t += 86400) {
            $kalender_dates[date('Y-m-d', $t)] = [
                'tipe' => $cal['tipe'],
                'is_full_day' => (empty($cal['jam_mulai_kosong']) || $cal['jam_mulai_kosong'] === '00:00:00'),
                'jam_mulai_kosong' => $cal['jam_mulai_kosong']
            ];
        }
    }
    
    $stmt_all_jurnal = $siakad_db->prepare("SELECT tanggal, kelas_id, mapel_id FROM jurnal_guru WHERE tanggal >= ? AND tanggal <= ?");
    $stmt_all_jurnal->execute([$start_date, $today]);
    $all_jurnals = $stmt_all_jurnal->fetchAll(PDO::FETCH_ASSOC);
    $jurnal_map = [];
    foreach($all_jurnals as $aj) {
        $jurnal_map[$aj['tanggal'].'_'.$aj['kelas_id'].'_'.$aj['mapel_id']] = true;
    }
    
    $current_t = strtotime($start_date);
    $end_t = strtotime($today);
    while ($current_t <= $end_t) {
        $date_str = date('Y-m-d', $current_t);
        $nama_hari = $hari_map[date('N', $current_t)];
        
        if (isset($jadwal_by_hari[$nama_hari])) {
            foreach ($jadwal_by_hari[$nama_hari] as $jdwl) {
                $is_excused = false;
                if (isset($kalender_dates[$date_str])) {
                    $cal_info = $kalender_dates[$date_str];
                    if ($cal_info['tipe'] != 'daring') {
                        if ($cal_info['is_full_day']) {
                            $is_excused = true;
                        } else {
                            if ($jdwl['jam_mulai'] >= $cal_info['jam_mulai_kosong']) {
                                $is_excused = true;
                            }
                        }
                    }
                }
                if (!$is_excused) {
                    $key = $date_str.'_'.$jdwl['kelas_id'].'_'.$jdwl['mapel_id'];
                    if (!isset($jurnal_map[$key])) {
                        $global_tanggungan[] = [
                            'tanggal' => $date_str,
                            'kelas_id' => $jdwl['kelas_id'],
                            'nama_kelas' => $jdwl['nama_kelas'],
                            'mapel_id' => $jdwl['mapel_id'],
                            'nama_mapel' => $jdwl['nama_mapel']
                        ];
                    }
                }
            }
        }
        $current_t = strtotime('+1 day', $current_t);
    }
    usort($global_tanggungan, function($a, $b) {
        return strtotime($b['tanggal']) - strtotime($a['tanggal']);
    });
} catch (Exception $eg) {}

$page_title = 'Jurnal & Absensi';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Aktivitas Belajar</p>
            <h1>Jurnal Mengajar & Absensi</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9;">
            <i class="fa-solid fa-book-open"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $status ?>',
                title: '<?= $status == "success" ? "Berhasil!" : "Perhatian!" ?>',
                text: '<?= addslashes($message) ?>',
                confirmButtonColor: '#0ea5e9'
            });
        });
        </script>
    <?php endif; ?>

    <div class="data-card" <?= empty($message) ? 'style="margin-top: -2.5rem; z-index: 10;"' : '' ?>>
        
        <?php if ($is_inval): ?>
        <div style="background: #f0f9ff; border: 1px dashed #0ea5e9; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-person-chalkboard"></i>
                </div>
                <div>
                    <h4 style="margin: 0; color: #0369a1; font-size: 0.95rem;">Mode Jurnal Inval</h4>
                    <p style="margin: 0; font-size: 0.8rem; color: #0284c7;">Menggantikan: <b><?= htmlspecialchars($target_guru_nama) ?></b></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- GLOBAL TANGGUNGAN -->
        <?php if(!empty($global_tanggungan)): ?>
        <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 12px; margin-bottom: 20px; padding: 12px 15px; box-shadow: 0 2px 4px rgba(244,63,94,0.05); display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="document.getElementById('modalTanggungan').style.display='flex'">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-bell" style="color: #f43f5e; font-size: 1.2rem;"></i>
                <div>
                    <h4 style="margin: 0; color: #9f1239; font-size: 0.95rem;">Ada <?= count($global_tanggungan) ?> Jurnal Kosong</h4>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right" style="color: #f43f5e;"></i>
        </div>

        <!-- MODAL TANGGUNGAN -->
        <div id="modalTanggungan" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; padding: 20px;">
            <div style="background: white; border-radius: 16px; width: 100%; max-width: 400px; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden;">
                <div style="padding: 15px 20px; background: #fdf2f8; border-bottom: 1px solid #fce7f3; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: #9f1239; font-size: 1.1rem;">Jurnal Belum Diisi (Semester Ini)</h3>
                    <button onclick="document.getElementById('modalTanggungan').style.display='none'" style="background: none; border: none; font-size: 1.2rem; color: #be123c; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div style="padding: 15px 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach($global_tanggungan as $gt): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: white; border: 1px solid #fce7f3; padding: 10px 15px; border-radius: 8px;">
                        <div style="display: flex; flex-direction: column; gap: 3px;">
                            <span style="font-weight: 700; color: #e11d48; font-size: 0.9rem;">
                                <i class="fa-regular fa-calendar-xmark" style="margin-right: 5px;"></i> <?= date('d M Y', strtotime($gt['tanggal'])) ?>
                            </span>
                            <span style="font-size: 0.8rem; color: #881337; font-weight: 600;"><?= htmlspecialchars($gt['nama_kelas'] . ' - ' . $gt['nama_mapel']) ?></span>
                        </div>
                        <a href="jurnal_mengajar.php?pre_tanggal=<?= $gt['tanggal'] ?>&pre_kelas=<?= $gt['kelas_id'] ?>&pre_mapel=<?= $gt['mapel_id'] ?>" style="background: #f43f5e; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; text-decoration: none; white-space: nowrap;">
                            <i class="fa-solid fa-pen-to-square"></i> Isi
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <a href="rekap_jurnal.php" style="flex: 1; background: #eef2ff; color: #4f46e5; text-decoration: none; padding: 12px; border-radius: 10px; text-align: center; font-weight: 600; font-size: 0.8rem; border: 1px solid #c7d2fe; display: flex; flex-direction: column; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(79,70,229,0.05);">
                <i class="fa-solid fa-book-journal-whills" style="font-size: 1.3rem;"></i>
                Rekap Jurnal
            </a>
            <a href="rekap_absen_mapel.php" style="flex: 1; background: #f5f3ff; color: #7c3aed; text-decoration: none; padding: 12px; border-radius: 10px; text-align: center; font-weight: 600; font-size: 0.8rem; border: 1px solid #ddd6fe; display: flex; flex-direction: column; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(124,58,237,0.05);">
                <i class="fa-solid fa-chart-bar" style="font-size: 1.3rem;"></i>
                Rekap Absen
            </a>
        </div>

        <div id="pjj-warning" style="display: none; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <div style="color: #f59e0b; font-size: 1.5rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 style="margin: 0 0 5px 0; color: #b45309; font-size: 0.95rem;">Hari Pembelajaran Daring (PJJ)</h4>
                    <p style="margin: 0 0 10px 0; font-size: 0.85rem; color: #78350f; line-height: 1.4;">
                        Jurnal mengajar pada hari PJJ akan dibuat <b>secara otomatis</b> oleh sistem. Silakan berikan tugas melalui menu E-Learning.
                    </p>
                    <a href="guru_tugas_mandiri.php" style="display: inline-block; background: #f59e0b; color: white; text-decoration: none; padding: 8px 15px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;">
                        <i class="fa-solid fa-laptop-file"></i> Ke Menu E-Learning
                    </a>
                </div>
            </div>
        </div>

        <form action="" method="POST" id="formJurnal" onsubmit="return handleFormSubmit()">
            <?php if ($is_inval): ?>
            <input type="hidden" name="inval" value="1">
            <input type="hidden" name="target_guru_id" value="<?= $target_guru_id ?>">
            <?php endif; ?>
            
            <h3 class="section-title" style="margin-top: 0;">Langkah 1: Informasi Jurnal</h3>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Tanggal <span style="color:red">*</span></label>
                <?php $is_pre_tanggal = !empty($_GET['pre_tanggal']); ?>
                <input type="date" name="tanggal" required value="<?= htmlspecialchars($pre_tanggal) ?>" <?= $is_pre_tanggal ? 'readonly tabindex="-1"' : '' ?> style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: <?= $is_pre_tanggal ? '#e2e8f0' : '#f8fafc' ?>; <?= $is_pre_tanggal ? 'pointer-events: none;' : '' ?>" onchange="updateDropdowns();">
                <label style="display:flex; align-items:center; gap:8px; margin-top:10px; font-size:0.85rem; color:#64748b; cursor:pointer;">
                    <input type="checkbox" id="showAll" onchange="updateDropdowns()" style="accent-color:#0ea5e9; width:16px; height:16px;"> 
                    Tampilkan semua kelas & mapel (Mode Jam Tambahan / Pengganti)
                </label>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Kelas <span style="color:red">*</span></label>
                <?php $is_pre_kelas = !empty($_GET['pre_kelas']); ?>
                <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: <?= $is_pre_kelas ? '#e2e8f0' : '#f8fafc' ?>; <?= $is_pre_kelas ? 'pointer-events: none;' : '' ?>" <?= $is_pre_kelas ? 'tabindex="-1"' : '' ?> onchange="loadSiswa(); updateDropdowns();">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($pre_kelas == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Mata Pelajaran <span style="color:red">*</span></label>
                <?php $is_pre_mapel = !empty($_GET['pre_mapel']); ?>
                <select name="mapel_id" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: <?= $is_pre_mapel ? '#e2e8f0' : '#f8fafc' ?>; <?= $is_pre_mapel ? 'pointer-events: none;' : '' ?>" <?= $is_pre_mapel ? 'tabindex="-1"' : '' ?> onchange="checkDaring()">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach($mapelList as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($pre_mapel == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Topik / Materi Pembelajaran <span style="color:red">*</span></label>
                <textarea name="materi" required rows="3" placeholder="Contoh: Bab 1. Pengenalan Huruf Hijaiyah" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; font-family: inherit;"><?= htmlspecialchars($pre_tugas) ?></textarea>
            </div>
            
            <div class="input-group" style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Catatan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Opsional" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; font-family: inherit;"></textarea>
            </div>
            
            <div id="wrapAbsensi" style="display: none;">
                <h3 class="section-title">Langkah 2: Absensi Siswa</h3>
                <div style="background: #eff6ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 0.8rem; color: #1e40af; border-left: 3px solid #3b82f6;">
                    <i class="fa-solid fa-circle-info"></i> Seluruh siswa secara default ditandai <b>Hadir</b>. Ubah status hanya bagi yang berhalangan.
                </div>
                
                <div id="loadingSiswa" style="text-align: center; padding: 20px; display: none;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #0ea5e9;"></i>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 10px;">Memuat daftar siswa...</p>
                </div>
                
                <div id="listSiswa" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px;">
                    <!-- Diisi lewat JS -->
                </div>
                
                <button type="submit" id="btnSubmit" class="btn" style="width: 100%; padding: 14px; background: #0ea5e9; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; margin-bottom: 15px; cursor: pointer;">
                    <i class="fa-solid fa-save"></i> <span>Simpan Jurnal & Absensi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadSiswa() {
    var kelas_id = document.getElementById('kelas_id').value;
    var wrap = document.getElementById('wrapAbsensi');
    var loading = document.getElementById('loadingSiswa');
    var list = document.getElementById('listSiswa');
    
    if (!kelas_id) {
        wrap.style.display = 'none';
        return;
    }
    
    wrap.style.display = 'block';
    list.innerHTML = '';
    loading.style.display = 'block';
    
    fetch('api_get_siswa_kelas.php?kelas_id=' + kelas_id)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            if(data.error) {
                list.innerHTML = '<div style="color:red; font-size:0.85rem;">Error: '+data.error+'</div>';
                return;
            }
            if(data.length === 0) {
                list.innerHTML = '<div style="text-align:center; color:#94a3b8; font-size:0.85rem; padding: 20px;">Tidak ada siswa di kelas ini.</div>';
                return;
            }
            
            data.forEach(function(s, index) {
                var no = index + 1;
                var html = `
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                    <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;">${no}. ${s.nama}</div>
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Hadir" checked> <span style="font-size:0.8rem; font-weight:600; color:#10b981;">Hadir</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Sakit"> <span style="font-size:0.8rem; font-weight:600; color:#eab308;">Sakit</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Izin"> <span style="font-size:0.8rem; font-weight:600; color:#3b82f6;">Izin</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Alpa"> <span style="font-size:0.8rem; font-weight:600; color:#ef4444;">Alpa</span>
                        </label>
                    </div>
                </div>
                `;
                list.innerHTML += html;
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            list.innerHTML = '<div style="color:red; font-size:0.85rem;">Error koneksi jaringan.</div>';
        });
}

function handleFormSubmit() {
    var btn = document.getElementById('btnSubmit');
    if(btn) {
        btn.disabled = true;
        btn.style.background = '#94a3b8';
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Sedang menyimpan...</span>';
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    var kelas_id = document.getElementById('kelas_id').value;
    if (kelas_id) {
        loadSiswa();
    }
    updateDropdowns();
});

var jadwalGuru = <?= json_encode(isset($all_jadwal) ? $all_jadwal : []) ?>;

function getHariIndo(dateStr) {
    var d = new Date(dateStr);
    var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return days[d.getDay()];
}

function updateDropdowns() {
    var tanggal = document.querySelector('input[name="tanggal"]').value;
    var kelasSelect = document.getElementById('kelas_id');
    var mapelSelect = document.querySelector('select[name="mapel_id"]');
    var showAll = document.getElementById('showAll').checked;
    
    if (!tanggal) return;
    var hari = getHariIndo(tanggal);
    
    // 1. Filter Kelas
    var scheduledKelas = [];
    jadwalGuru.forEach(function(j) {
        if (j.hari === hari) {
            scheduledKelas.push(j.kelas_id.toString());
        }
    });
    
    var kOptions = kelasSelect.options;
    var isKelasValid = false;
    for (var i = 0; i < kOptions.length; i++) {
        var opt = kOptions[i];
        if (opt.value === "") continue;
        
        if (showAll || scheduledKelas.includes(opt.value)) {
            opt.style.display = '';
            opt.disabled = false;
            opt.hidden = false;
            if (kelasSelect.value === opt.value) isKelasValid = true;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
            opt.hidden = true;
        }
    }
    
    if (!isKelasValid && kelasSelect.value !== "") {
        kelasSelect.value = "";
        loadSiswa();
    }
    
    // 2. Filter Mapel
    var kelas_id = kelasSelect.value;
    var scheduledMapels = [];
    if (kelas_id) {
        jadwalGuru.forEach(function(j) {
            if (j.hari === hari && j.kelas_id == kelas_id) {
                scheduledMapels.push(j.mapel_id.toString());
            }
        });
    }
    
    var mOptions = mapelSelect.options;
    var firstScheduledIndex = -1;
    var isMapelValid = false;
    
    for (var i = 0; i < mOptions.length; i++) {
        var opt = mOptions[i];
        if (opt.value === "") continue;
        
        var baseText = opt.text.replace(' (Terjadwal Hari Ini)', '');
        
        if (scheduledMapels.includes(opt.value)) {
            opt.text = baseText + ' (Terjadwal Hari Ini)';
            opt.style.fontWeight = 'bold';
            opt.style.color = '#0ea5e9';
            opt.style.display = '';
            opt.disabled = false;
            opt.hidden = false;
            if (firstScheduledIndex === -1) firstScheduledIndex = i;
            if (mapelSelect.value === opt.value) isMapelValid = true;
        } else {
            opt.text = baseText;
            opt.style.fontWeight = 'normal';
            opt.style.color = 'initial';
            if (showAll) {
                opt.style.display = '';
                opt.disabled = false;
                opt.hidden = false;
                if (mapelSelect.value === opt.value) isMapelValid = true;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
                opt.hidden = true;
            }
        }
    }
    
    if (!isMapelValid && mapelSelect.value !== "") {
        mapelSelect.value = "";
    }
    
    if (firstScheduledIndex !== -1 && mapelSelect.value === "") {
        mapelSelect.selectedIndex = firstScheduledIndex;
    }
    
    checkDaring();
}

function checkDaring() {
    var kls = document.getElementById('kelas_id').value || 0;
    var mpl = document.querySelector('select[name="mapel_id"]').value || 0;
    var tgl = document.querySelector('input[name="tanggal"]').value;
    
    if (tgl) {
        fetch('?action=cek_daring&kls=' + kls + '&mpl=' + mpl + '&tgl=' + tgl)
        .then(response => response.json())
        .then(data => {
            var formContent = document.getElementById('formJurnal');
            var pjjWarning = document.getElementById('pjj-warning');
            
            if (data.is_daring) {
                // Sembunyikan form dan tampilkan peringatan PJJ
                formContent.style.display = 'none';
                pjjWarning.style.display = 'block';
            } else {
                // Tampilkan form kembali jika bukan PJJ
                formContent.style.display = 'block';
                pjjWarning.style.display = 'none';
                
                var materi = document.querySelector('textarea[name="materi"]');
                if (data.materi && materi.value.trim() === '') {
                    materi.value = data.materi;
                    materi.style.transition = "background-color 0.5s";
                    materi.style.backgroundColor = "#e0e7ff";
                    setTimeout(() => { materi.style.backgroundColor = "#f8fafc"; }, 1500);
                }
            }
        }).catch(err => console.log('Error check daring:', err));
    }
}
</script>

<?php include 'footer_guru.php'; ?>
