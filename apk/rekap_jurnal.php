<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$logged_in_guru_id = $_SESSION['guru_id'];
$guru_id = $logged_in_guru_id;

$is_kamad = false;
$is_wakakur = false;

// Cek hak akses
$stmt_akses = $core_db->prepare("SELECT is_kamad FROM guru WHERE id = :id LIMIT 1");
$stmt_akses->execute(['id' => $logged_in_guru_id]);
$logged_in_guru = $stmt_akses->fetch(PDO::FETCH_ASSOC);

if ($logged_in_guru && $logged_in_guru['is_kamad'] == 1) {
    $is_kamad = true;
} else {
    $stmt_w = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
    $stmt_w->execute([$logged_in_guru_id]);
    if ($stmt_w->fetch()) {
        $is_wakakur = true;
    }
}

if (isset($_GET['guru_id']) && ($is_kamad || $is_wakakur)) {
    $guru_id = (int)$_GET['guru_id'];
}

// Ambil data guru yang akan dilihat rekapnya
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$msg_sukses = '';
$msg_error = '';

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    // Cek kepemilikan atau sinkronisasi dengan guru pengganti
    $stmt_cek = $siakad_db->prepare("SELECT id, tanggal, kelas_id, mapel_id, guru_id FROM jurnal_guru WHERE id = ?");
    $stmt_cek->execute([$del_id]);
    if ($row_j = $stmt_cek->fetch(PDO::FETCH_ASSOC)) {
        $tanggal_j = $row_j['tanggal'];
        
        // Ambil daftar siswa_id yang ada absen mapelnya di jurnal ini
        $stmt_s = $siakad_db->prepare("SELECT DISTINCT siswa_id FROM absensi_permapel WHERE jurnal_id = ?");
        $stmt_s->execute([$del_id]);
        $siswa_ids = $stmt_s->fetchAll(PDO::FETCH_COLUMN);
        
        $siakad_db->beginTransaction();
        try {
            // Hapus absensi permapel
            $siakad_db->prepare("DELETE FROM absensi_permapel WHERE jurnal_id = ?")->execute([$del_id]);
            
            // Hapus jurnal
            $siakad_db->prepare("DELETE FROM jurnal_guru WHERE id = ?")->execute([$del_id]);
            
            // Recalculate pusat
            if (!empty($siswa_ids)) {
                $stmt_cek_pusat = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmt_update_pusat = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                $stmt_del_pusat = $siakad_db->prepare("DELETE FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmt_semua_absen = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");
                
                foreach ($siswa_ids as $s_id) {
                    $stmt_semua_absen->execute([$s_id, $tanggal_j]);
                    $semua_status = $stmt_semua_absen->fetchAll(PDO::FETCH_COLUMN);
                    
                    if (empty($semua_status)) {
                        $stmt_del_pusat->execute([$s_id, $tanggal_j]);
                    } else {
                        $ada_hadir = in_array('Hadir', $semua_status);
                        $ada_alpa = in_array('Alpa', $semua_status) || in_array('Alpha', $semua_status);
                        
                        if ($ada_hadir && $ada_alpa) {
                            $status_final = 'Bolos';
                        } elseif ($ada_alpa && !$ada_hadir) {
                            $status_final = 'Alpa';
                        } elseif ($ada_hadir) {
                            $status_final = 'Hadir';
                        } elseif (in_array('Sakit', $semua_status)) {
                            $status_final = 'Sakit';
                        } elseif (in_array('Izin', $semua_status)) {
                            $status_final = 'Izin';
                        } else {
                            $status_final = 'Hadir';
                        }
                        
                        $stmt_cek_pusat->execute([$s_id, $tanggal_j]);
                        if ($stmt_cek_pusat->fetch()) {
                            $stmt_update_pusat->execute([$status_final, $s_id, $tanggal_j]);
                        }
                    }
                }
            }
            $siakad_db->commit();
            $msg_sukses = "Jurnal beserta absensinya berhasil dihapus dan direkap ulang.";
        } catch (Exception $e) {
            $siakad_db->rollBack();
            $msg_error = "Gagal menghapus jurnal: " . $e->getMessage();
        }
    } else {
        $msg_error = "Jurnal tidak ditemukan atau Anda tidak memiliki akses.";
    }
}

// Ambil tahun ajaran & semester aktif (untuk filter kelas mengajar)
$ta_id = 1; $ta_sem = 1;
try {
    $ta_q = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetch();
    if($ta_q) { $ta_id = $ta_q['id']; $ta_sem = $ta_q['semester']; }
} catch (Exception $e) {}

// Ambil daftar kelas dan mapel yang diajar
$kelasList = [];
$mapelList = [];
$mengajarData = [];
try {
    $stmt_k = $siakad_db->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM mengajar m JOIN aula_db.kelas k ON k.id = m.kelas_id WHERE m.guru_id = ? ORDER BY k.tingkat, k.nama_kelas");
    $stmt_k->execute([$guru_id]);
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("SELECT DISTINCT mp.id, mp.nama_mapel FROM mengajar m JOIN mata_pelajaran mp ON mp.id = m.mapel_id WHERE m.guru_id = ? ORDER BY mp.nama_mapel");
    $stmt_m->execute([$guru_id]);
    $mapelList = $stmt_m->fetchAll(PDO::FETCH_ASSOC);

    $stmt_mg = $siakad_db->prepare("SELECT kelas_id, mapel_id as mapel_id, mp.nama_mapel FROM mengajar m JOIN mata_pelajaran mp ON mp.id = m.mapel_id WHERE m.guru_id = ?");
    $stmt_mg->execute([$guru_id]);
    $mengajarData = $stmt_mg->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    try {
        $stmt_k = $siakad_db->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM jadwal_pelajaran jp JOIN aula_db.kelas k ON k.id = jp.kelas_id WHERE jp.guru_id = ? ORDER BY k.tingkat, k.nama_kelas");
        $stmt_k->execute([$guru_id]);
        $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

        $stmt_m = $siakad_db->prepare("SELECT DISTINCT mp.id, mp.nama_mapel FROM jadwal_pelajaran jp JOIN mata_pelajaran mp ON mp.id = jp.mata_pelajaran_id WHERE jp.guru_id = ? ORDER BY mp.nama_mapel");
        $stmt_m->execute([$guru_id]);
        $mapelList = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt_mg = $siakad_db->prepare("SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel FROM jadwal_pelajaran jp JOIN mata_pelajaran mp ON mp.id = jp.mata_pelajaran_id WHERE jp.guru_id = ?");
        $stmt_mg->execute([$guru_id]);
        $mengajarData = $stmt_mg->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e2) {}
}

// Filter Options
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$message = '';
$status = '';

// AUTO-SYNC TUGAS DARING KE JURNAL UNTUK HARI DARING
try {
    $stmt_sync = $siakad_db->prepare("
        SELECT t.id, t.kelas_id, t.mata_pelajaran_id as mapel_id, t.tanggal, t.judul, t.instruksi 
        FROM tugas_daring t
        JOIN kalender_akademik k ON k.tipe = 'daring' AND t.tanggal BETWEEN k.tanggal_mulai AND k.tanggal_selesai
        WHERE t.guru_id = ?
    ");
    $stmt_sync->execute([$guru_id]);
    $tugas_list = $stmt_sync->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tugas_list as $tg) {
        $stmt_c = $siakad_db->prepare("SELECT id FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal = ?");
        $stmt_c->execute([$guru_id, $tg['kelas_id'], $tg['mapel_id'], $tg['tanggal']]);
        if (!$stmt_c->fetch()) {
            $materi = "Pembelajaran Daring: " . $tg['judul'];
            $stmt_i = $siakad_db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, 'Di-generate otomatis dari E-Learning')");
            $stmt_i->execute([$guru_id, $tg['kelas_id'], $tg['mapel_id'], $tg['tanggal'], $materi]);
        }
    }
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
    $stmt_semua_jadwal->execute([$guru_id, $ta_id]);
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

$filter_kelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
$filter_mapel = isset($_GET['mapel_id']) ? $_GET['mapel_id'] : '';

try {
    if (empty($filter_kelas) || empty($filter_mapel)) {
        $jurnals = [];
    } else {
        $query = "
            SELECT j.*, k.nama_kelas, mp.nama_mapel
            FROM jurnal_guru j 
            JOIN aula_db.kelas k ON k.id = j.kelas_id 
            JOIN mata_pelajaran mp ON mp.id = j.mapel_id
            WHERE DATE_FORMAT(j.tanggal, '%Y-%m') = ?
            AND j.kelas_id = ? AND j.mapel_id = ?
            ORDER BY j.tanggal DESC, j.id DESC
        ";
        $params = [$filter_bulan, $filter_kelas, $filter_mapel];
    
        $stmt_jurnal = $siakad_db->prepare($query);
        $stmt_jurnal->execute($params);
        $jurnals = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Siapkan array absensi
    $absen_data = [];
    if (count($jurnals) > 0) {
        $jurnal_ids = array_column($jurnals, 'id');
        $inQuery = implode(',', array_fill(0, count($jurnal_ids), '?'));
        
        $stmt_absen = $siakad_db->prepare("
            SELECT jurnal_id, status, COUNT(id) as total
            FROM absensi_permapel
            WHERE jurnal_id IN ($inQuery)
            GROUP BY jurnal_id, status
        ");
        $stmt_absen->execute($jurnal_ids);
        $rekap_absen = $stmt_absen->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rekap_absen as $ra) {
            $absen_data[$ra['jurnal_id']]['rekap'][$ra['status']] = $ra['total'];
        }
        
        $stmt_absen_detail = $siakad_db->prepare("
            SELECT ap.jurnal_id, ap.siswa_id, ap.status, ap.keterangan 
            FROM absensi_permapel ap
            WHERE ap.jurnal_id IN ($inQuery) AND ap.status != 'Hadir'
        ");
        $stmt_absen_detail->execute($jurnal_ids);
        $detail_absen = $stmt_absen_detail->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($detail_absen) > 0) {
            $siswa_ids = array_values(array_unique(array_column($detail_absen, 'siswa_id')));
            $inSiswa = implode(',', array_fill(0, count($siswa_ids), '?'));
            $stmt_siswa = $core_db->prepare("SELECT id, nama FROM siswa WHERE id IN ($inSiswa)");
            $stmt_siswa->execute($siswa_ids);
            $nama_siswa = [];
            while($row = $stmt_siswa->fetch(PDO::FETCH_ASSOC)) {
                $nama_siswa[$row['id']] = $row['nama'];
            }
            
            foreach ($detail_absen as $da) {
                $da['nama'] = isset($nama_siswa[$da['siswa_id']]) ? $nama_siswa[$da['siswa_id']] : 'Siswa Tdk Ditemukan';
                $absen_data[$da['jurnal_id']]['detail'][] = $da;
            }
        }
    }
} catch (Exception $e) {
    $jurnals = [];
    $absen_data = [];
}

$page_title = 'Rekap Jurnal Mengajar';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);">
    <div class="header-top">
        <a href="profil_guru.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="text-align: center; flex: 1;">
            <h1 style="margin: 0; font-size: 1.2rem;">Rekap Jurnal</h1>
        </div>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($msg_sukses)): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: #065f46; font-size: 0.9rem; font-weight: 600;"><?= $msg_sukses ?></p>
        </div>
    <?php endif; ?>
    <?php if(!empty($msg_error)): ?>
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: #b91c1c; font-size: 0.9rem; font-weight: 600;"><?= $msg_error ?></p>
        </div>
    <?php endif; ?>
    <div class="data-card" style="margin-top: -2rem; z-index: 10;">
        
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

        <form method="GET" action="" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
            <div style="font-size: 0.85rem; font-weight: 600; color: #475569;">Filter Jurnal</div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;" onchange="updateMapelDropdown()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $filter_kelas==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="mapel_id" id="mapel_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;" data-selected="<?= htmlspecialchars($filter_mapel) ?>">
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($mapelList as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $filter_mapel==$m['id']?'selected':'' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <input type="month" name="bulan" value="<?= htmlspecialchars($filter_bulan) ?>" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit;">
                <button type="submit" style="background: #4f46e5; color: white; border: none; padding: 0 15px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-search"></i> Tampilkan
                </button>
            </div>
        </form>
        
        <?php if(empty($filter_kelas) || empty($filter_mapel)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-filter" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
                <h3 style="margin: 0 0 5px 0; color: #475569; font-size: 1.1rem;">Pilih Filter</h3>
                <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Silakan pilih kelas dan mata pelajaran terlebih dahulu untuk melihat rekap jurnal.</p>
            </div>
        <?php else: 
            // ==========================================
            // LOGIKA PENCARIAN TANGGUNGAN JURNAL (MISSING)
            // ==========================================
            $tanggungan = [];
            $hari_libur = [];
            $sudah_isi = array_column($jurnals, 'tanggal');
            
            try {
                // 1. Ambil jadwal hari untuk mapel dan kelas ini
                $stmt_hari = $siakad_db->prepare("SELECT DISTINCT hari, jam_mulai FROM jadwal_pelajaran WHERE guru_id = ? AND kelas_id = ? AND mata_pelajaran_id = ? AND tahun_ajaran_id = ? ORDER BY jam_mulai ASC");
                $stmt_hari->execute([$guru_id, $filter_kelas, $filter_mapel, $ta_id]);
                $jadwal_raw = $stmt_hari->fetchAll(PDO::FETCH_ASSOC);
                
                $jadwal_hari = [];
                foreach($jadwal_raw as $jdwl) {
                    if (!isset($jadwal_hari[$jdwl['hari']])) {
                        $jadwal_hari[$jdwl['hari']] = $jdwl['jam_mulai'];
                    }
                }
                
                $hari_map = [
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu',
                    7 => 'Minggu'
                ];
                
                if (!empty($jadwal_hari)) {
                    // 2. Ambil data kalender pendidikan untuk bulan ini
                    $start_date = $filter_bulan . '-01';
                    $end_of_month = date('Y-m-t', strtotime($start_date));
                    $today = date('Y-m-d');
                    $end_date = ($end_of_month > $today) ? $today : $end_of_month;
                    
                    $stmt_cal = $siakad_db->prepare("SELECT tanggal_mulai, tanggal_selesai, keterangan, tipe, jam_mulai_kosong FROM kalender_akademik WHERE tanggal_mulai <= ? AND (tanggal_selesai >= ? OR tanggal_selesai IS NULL OR tanggal_selesai = '0000-00-00')");
                    $stmt_cal->execute([$end_date, $start_date]);
                    $kalender_data = $stmt_cal->fetchAll(PDO::FETCH_ASSOC);
                    
                    $kalender_dates = [];
                    foreach($kalender_data as $cal) {
                        $t_start = strtotime($cal['tanggal_mulai']);
                        $t_end = (!empty($cal['tanggal_selesai']) && $cal['tanggal_selesai'] != '0000-00-00') ? strtotime($cal['tanggal_selesai']) : $t_start;
                        for($t = $t_start; $t <= $t_end; $t += 86400) {
                            $kalender_dates[date('Y-m-d', $t)] = [
                                'keterangan' => $cal['keterangan'],
                                'tipe' => $cal['tipe'],
                                'jam_mulai_kosong' => $cal['jam_mulai_kosong'],
                                'is_full_day' => (empty($cal['jam_mulai_kosong']) || $cal['jam_mulai_kosong'] === '00:00:00')
                            ];
                        }
                    }
                    
                    $current_date = strtotime($start_date);
                    $end_time = strtotime($end_date);
                    
                    $excused_dates = [];
                    
                    while ($current_date <= $end_time) {
                        $day_of_week = date('N', $current_date);
                        $nama_hari = $hari_map[$day_of_week];
                        $date_str = date('Y-m-d', $current_date);
                        
                        // Jika hari ini termasuk dalam jadwal mengajar
                        if (array_key_exists($nama_hari, $jadwal_hari)) {
                            $is_filled = in_array($date_str, $sudah_isi);
                            $is_excused = false;
                            $keterangan_excuse = '';

                            if (isset($kalender_dates[$date_str])) {
                                $cal_info = $kalender_dates[$date_str];
                                if ($cal_info['tipe'] != 'daring') {
                                    if ($cal_info['is_full_day']) {
                                        $is_excused = true;
                                        $keterangan_excuse = $cal_info['keterangan'];
                                    } else {
                                        $jadwal_start = $jadwal_hari[$nama_hari];
                                        if ($jadwal_start >= $cal_info['jam_mulai_kosong']) {
                                            $is_excused = true;
                                            $keterangan_excuse = $cal_info['keterangan'] . ' (Ditiadakan)';
                                        }
                                    }
                                }
                            }
                            
                            if ($is_excused) {
                                $hari_libur[] = ['tanggal' => $date_str, 'keterangan' => $keterangan_excuse];
                                $excused_dates[] = $date_str;
                            } else {
                                if (!$is_filled) {
                                    $tanggungan[] = $date_str;
                                }
                            }
                        }
                        $current_date = strtotime("+1 day", $current_date);
                    }
                    
                    // Sembunyikan (timpa) jurnal yang terlanjur diisi di hari yang ditiadakan
                    if (!empty($excused_dates)) {
                        $filtered_jurnals = [];
                        foreach($jurnals as $j) {
                            if (!in_array($j['tanggal'], $excused_dates)) {
                                $filtered_jurnals[] = $j;
                            }
                        }
                        $jurnals = $filtered_jurnals;
                    }
                }
            } catch (Exception $e) {}
        ?>
            <!-- TAMPILAN TANGGUNGAN LAMA DIHAPUS AGAR TIDAK DOUBLE -->
        <?php 
            // Gabungkan jurnals dan hari libur untuk list
            $merged_list = [];
            foreach($jurnals as $j) {
                $j['type'] = 'jurnal';
                $merged_list[] = $j;
            }
            foreach($hari_libur as $hl) {
                $hl['type'] = 'libur';
                $merged_list[] = $hl;
            }
            
            // Sort by date DESC
            usort($merged_list, function($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });
        
            if(empty($merged_list)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
                <h3 style="margin: 0 0 5px 0; color: #475569; font-size: 1.1rem;">Belum ada data</h3>
                <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Tidak ada jurnal maupun kegiatan untuk filter yang dipilih.</p>
            </div>
        <?php else: 
            // Hitung jurnal ganda per tanggal
            $tanggal_counts = [];
            foreach($merged_list as $j) {
                if ($j['type'] == 'jurnal') {
                    $tgl = $j['tanggal'];
                    if(!isset($tanggal_counts[$tgl])) $tanggal_counts[$tgl] = 0;
                    $tanggal_counts[$tgl]++;
                }
            }
        ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($merged_list as $j): ?>
                    <?php if ($j['type'] == 'libur'): ?>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; padding: 15px;">
                            <div style="font-size: 2rem; color: #22c55e; margin-right: 15px;">
                                <i class="fa-solid fa-umbrella-beach"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: #166534; font-size: 0.95rem;"><?= htmlspecialchars($j['keterangan']) ?></div>
                                <div style="font-size: 0.75rem; font-weight: 600; color: #059669; margin-top: 5px;">
                                    <i class="fa-solid fa-calendar-check"></i> <?= date('d M Y', strtotime($j['tanggal'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                    <?php 
                        $jid = $j['id'];
                        $is_duplicate = ($tanggal_counts[$j['tanggal']] > 1);
                        $hadir = isset($absen_data[$jid]['rekap']['Hadir']) ? $absen_data[$jid]['rekap']['Hadir'] : 0;
                        $sakit = isset($absen_data[$jid]['rekap']['Sakit']) ? $absen_data[$jid]['rekap']['Sakit'] : 0;
                        $izin = isset($absen_data[$jid]['rekap']['Izin']) ? $absen_data[$jid]['rekap']['Izin'] : 0;
                        $alpa = isset($absen_data[$jid]['rekap']['Alpa']) ? $absen_data[$jid]['rekap']['Alpa'] : 
                                (isset($absen_data[$jid]['rekap']['Alpha']) ? $absen_data[$jid]['rekap']['Alpha'] : 0);
                        
                        $detail_absen = isset($absen_data[$jid]['detail']) ? $absen_data[$jid]['detail'] : [];
                        
                        $materi_text = $j['materi'];
                        $is_daring = false;
                        if (strpos($materi_text, 'Pembelajaran Daring:') !== false) {
                            $is_daring = true;
                            // Jika masih ada sisa teks instruksi lama, potong aja
                            $parts = explode("\n\nInstruksi:", $materi_text);
                            $materi_display = trim(str_replace('Pembelajaran Daring:', '', $parts[0]));
                        } else {
                            $materi_display = $materi_text;
                        }
                    ?>
                    <div style="background: <?= $is_duplicate ? '#fff7ed' : 'white' ?>; border: <?= $is_duplicate ? '2px solid #f97316' : '1px solid #e2e8f0' ?>; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); <?= $is_duplicate ? 'position:relative;' : '' ?>">
                        <?php if($is_duplicate): ?>
                            <div style="position:absolute; top:12px; right:110px; background:#f97316; color:white; font-size:0.65rem; font-weight:bold; padding:2px 6px; border-radius:4px; z-index:5;">JURNAL GANDA</div>
                        <?php endif; ?>
                        <div style="background: <?= $is_duplicate ? '#ffedd5' : '#eef2ff' ?>; padding: 12px 15px; border-bottom: <?= $is_duplicate ? '1px solid #fed7aa' : '1px solid #e2e8f0' ?>; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 700; color: #4338ca; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <?= htmlspecialchars($j['nama_mapel']) ?>
                                <?php if($is_daring): ?>
                                    <span style="background: #8b5cf6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold;"><i class="fa-solid fa-laptop"></i> PJJ / Daring</span>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; background: white; padding: 3px 8px; border-radius: 20px; border: 1px solid #cbd5e1;">
                                    <?= date('d M Y', strtotime($j['tanggal'])) ?>
                                </div>
                                <a href="edit_jurnal.php?id=<?= $jid ?>" style="color: #f59e0b; font-size: 1rem;"><i class="fa-solid fa-edit"></i></a>
                                <a href="#" onclick="confirmDelete('?delete=<?= $jid ?>&bulan=<?= urlencode($filter_bulan) ?>&kelas_id=<?= urlencode($filter_kelas) ?>&mapel_id=<?= urlencode($filter_mapel) ?>'); return false;" style="color: #ef4444; font-size: 1rem;"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                        <div style="padding: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <div style="font-size: 0.8rem; color: #64748b;">
                                    <i class="fa-solid fa-landmark"></i> Kelas: <span style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($j['nama_kelas']) ?></span>
                                </div>
                            </div>
                            <div style="font-size: 0.85rem; color: #334155; margin-bottom: 15px; line-height: 1.5;">
                                <?php if ($is_daring): ?>
                                    <div style="background: #eff6ff; border: 1px dashed #3b82f6; color: #1d4ed8; padding: 10px; border-radius: 8px; margin-bottom: 5px; font-weight: bold;">
                                        <i class="fa-solid fa-laptop-file"></i> Pembelajaran Daring:<br>
                                        <span style="font-weight: normal; color: #334155;"><?= nl2br(htmlspecialchars($materi_display)) ?></span>
                                    </div>
                                <?php else: ?>
                                    <b>Materi:</b><br><?= nl2br(htmlspecialchars($materi_display)) ?>
                                <?php endif; ?>
                            </div>
                            
                            <div style="background: #f8fafc; border-radius: 8px; padding: 10px; margin-bottom: <?= count($detail_absen)>0?'15px':'0' ?>;">
                                <div style="font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Ringkasan Kehadiran</div>
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="text-align: center;"><div style="font-size: 1.1rem; font-weight: bold; color: #10b981;"><?= $hadir ?></div><div style="font-size: 0.65rem; color: #64748b;">Hadir</div></div>
                                    <div style="text-align: center;"><div style="font-size: 1.1rem; font-weight: bold; color: #eab308;"><?= $sakit ?></div><div style="font-size: 0.65rem; color: #64748b;">Sakit</div></div>
                                    <div style="text-align: center;"><div style="font-size: 1.1rem; font-weight: bold; color: #3b82f6;"><?= $izin ?></div><div style="font-size: 0.65rem; color: #64748b;">Izin</div></div>
                                    <div style="text-align: center;"><div style="font-size: 1.1rem; font-weight: bold; color: #ef4444;"><?= $alpa ?></div><div style="font-size: 0.65rem; color: #64748b;">Alpa</div></div>
                                </div>
                            </div>
                            
                            <?php if(count($detail_absen) > 0): ?>
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Siswa Tidak Hadir:</div>
                                <ul style="margin: 0; padding-left: 20px; font-size: 0.8rem; color: #334155; line-height: 1.6;">
                                    <?php foreach($detail_absen as $da): ?>
                                    <li>
                                        <b><?= htmlspecialchars($da['nama']) ?></b> - 
                                        <span style="font-weight:600; color: <?= $da['status']=='Sakit'?'#eab308':($da['status']=='Izin'?'#3b82f6':'#ef4444') ?>;"><?= $da['status'] ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const mengajarData = <?= json_encode($mengajarData ?? []) ?>;

function updateMapelDropdown() {
    var kelas_id = document.getElementById('kelas_id').value;
    var mapelSelect = document.getElementById('mapel_id');
    var currentVal = mapelSelect.dataset.selected || mapelSelect.value;
    
    mapelSelect.innerHTML = '<option value="">-- Semua Mapel --</option>';
    if (!kelas_id) {
        // Tampilkan semua mapel unik
        var uniqueMapels = [];
        var mapelIds = new Set();
        mengajarData.forEach(function(m) {
            if (!mapelIds.has(m.mapel_id)) {
                mapelIds.add(m.mapel_id);
                uniqueMapels.push(m);
            }
        });
        uniqueMapels.forEach(function(m) {
            var opt = document.createElement('option');
            opt.value = m.mapel_id;
            opt.textContent = m.nama_mapel;
            if (currentVal && m.mapel_id == currentVal) {
                opt.selected = true;
            }
            mapelSelect.appendChild(opt);
        });
        return;
    }
    
    var mapels = mengajarData.filter(function(m) {
        return m.kelas_id == kelas_id;
    });
    
    var uniqueMapels = [];
    var mapelIds = new Set();
    mapels.forEach(function(m) {
        if (!mapelIds.has(m.mapel_id)) {
            mapelIds.add(m.mapel_id);
            uniqueMapels.push(m);
        }
    });
    
    uniqueMapels.forEach(function(m) {
        var opt = document.createElement('option');
        opt.value = m.mapel_id;
        opt.textContent = m.nama_mapel;
        if (currentVal && m.mapel_id == currentVal) {
            opt.selected = true;
        }
        mapelSelect.appendChild(opt);
    });
    
    mapelSelect.dataset.selected = ''; 
}

document.addEventListener('DOMContentLoaded', function() {
    if(mengajarData.length > 0) {
        updateMapelDropdown();
    }
});

function confirmDelete(url) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Jurnal?',
            text: "Jurnal beserta seluruh data absensinya akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: `rgba(0,0,0,0.4)`
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    } else {
        if (confirm('Yakin ingin menghapus jurnal ini beserta data absensinya?')) {
            window.location.href = url;
        }
    }
}
</script>

<?php include 'footer_guru.php'; ?>
