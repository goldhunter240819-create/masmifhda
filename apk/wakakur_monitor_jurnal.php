<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Any teacher with the menu access can view this page

// Fetch all active classes
$classes = [];
try {
    $stmt_classes = $core_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
    $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : ($classes[0]['id'] ?? 0);
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

$final_display = [];
$selected_class_name = "";

if ($kelas_id) {
    // Find selected class name
    foreach ($classes as $c) {
        if ($c['id'] == $kelas_id) {
            $selected_class_name = $c['nama_kelas'];
            break;
        }
    }

    // Ambil tahun ajaran aktif
    $stmt_ta = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    $active_ta = $stmt_ta->fetch(PDO::FETCH_ASSOC);
    $ta_id = $active_ta ? $active_ta['id'] : 0;

    // 1. Ambil Jadwal Pelajaran Kelas Ini (hanya tahun ajaran aktif)
    $jadwal_list = [];
    try {
        $stmt_jadwal = $siakad_db->prepare("
            SELECT jp.hari, jp.jam_mulai, jp.jam_selesai, m.nama_mapel, g.nama as nama_guru, jp.mata_pelajaran_id as mapel_id, jp.guru_id
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON m.id = jp.mata_pelajaran_id
            LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".guru g ON g.id = jp.guru_id
            WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ?
            ORDER BY FIELD(jp.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jp.jam_mulai
        ");
        $stmt_jadwal->execute([$kelas_id, $ta_id]);
        $jadwal_list = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    $jadwal_per_hari = [];
    foreach ($jadwal_list as $jdwl) {
        $nama_mapel = strtolower($jdwl['nama_mapel']);
        $exclude_words = ['pembiasaan', 'ekstra', 'senam', 'upacara', 'sholat', 'pramuka', 'juz amma', 'asmaul husna', 'istirahat'];
        $is_excluded = false;
        foreach ($exclude_words as $word) {
            if (strpos($nama_mapel, $word) !== false) {
                $is_excluded = true;
                break;
            }
        }
        
        if ($is_excluded) {
            continue;
        }

        $hari = ucfirst(strtolower(trim($jdwl['hari'])));
        if ($hari === 'Ahad') {
            $hari = 'Minggu';
        }
        $mapel_id = $jdwl['mapel_id'];
        
        if (!isset($jadwal_per_hari[$hari][$mapel_id])) {
            $jadwal_per_hari[$hari][$mapel_id] = $jdwl;
        } else {
            if (strtotime($jdwl['jam_selesai']) > strtotime($jadwal_per_hari[$hari][$mapel_id]['jam_selesai'])) {
                $jadwal_per_hari[$hari][$mapel_id]['jam_selesai'] = $jdwl['jam_selesai'];
            }
        }
    }

    // 2. Ambil Jurnal Guru untuk bulan ini
    $jurnals_raw = [];
    try {
        $stmt_jurnal = $siakad_db->prepare("
            SELECT jg.id, jg.tanggal, jg.mapel_id, jg.materi, jg.keterangan as hambatan, 
                   g.nama as nama_guru_jurnal, m.nama_mapel
            FROM jurnal_guru jg
            JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".guru g ON g.id = jg.guru_id
            JOIN mata_pelajaran m ON m.id = jg.mapel_id
            WHERE jg.kelas_id = ? AND DATE_FORMAT(jg.tanggal, '%Y-%m') = ?
        ");
        $stmt_jurnal->execute([$kelas_id, $bulan]);
        $jurnals_raw = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    $jurnal_map = [];
    foreach($jurnals_raw as $jr) {
        $jurnal_map[$jr['tanggal']][$jr['mapel_id']] = $jr;
    }

    // 3. Bangun Rekap Berdasarkan Hari di Bulan Ini
    $start_date = $bulan . '-01';
    $end_date = date('Y-m-t', strtotime($start_date));
    if ($bulan == date('Y-m')) {
        $end_date = date('Y-m-d');
    }

    // Fetch dates that are holidays or kegiatans
    $libur_periods = [];
    try {
        $stmt_libur_dates = $siakad_db->prepare("SELECT tanggal_mulai, tanggal_selesai, tipe as kategori, keterangan as kegiatan, jam_mulai_kosong FROM kalender_akademik WHERE tipe IN ('libur', 'kegiatan', 'ujian', 'pts', 'pas', 'pulang_awal') AND tanggal_selesai >= ? AND tanggal_mulai <= ?");
        $stmt_libur_dates->execute([$start_date, $end_date]);
        $libur_periods = $stmt_libur_dates->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
    
    $special_dates = [];
    foreach ($libur_periods as $lp) {
        $c = strtotime($lp['tanggal_mulai']);
        $e = strtotime($lp['tanggal_selesai']);
        while ($c <= $e) {
            $dt = date('Y-m-d', $c);
            $special_dates[$dt] = [
                'kategori' => $lp['kategori'],
                'kegiatan' => $lp['kegiatan'],
                'jam_mulai_kosong' => $lp['jam_mulai_kosong'],
                'is_full_day' => empty($lp['jam_mulai_kosong']) || $lp['jam_mulai_kosong'] === '00:00:00'
            ];
            $c = strtotime('+1 day', $c);
        }
    }

    $current = strtotime($end_date);
    $start = strtotime($start_date);

    while ($current >= $start) {
        $tgl = date('Y-m-d', $current);
        $hari_inggris = date('l', $current);
        $hari_indo = $days_indo[$hari_inggris];
        if (isset($jadwal_per_hari[$hari_indo])) {
            $day_data = [
                'tanggal' => $tgl,
                'hari_indo' => $hari_indo,
                'is_special' => isset($special_dates[$tgl]) ? $special_dates[$tgl] : false,
                'items' => []
            ];
            
            foreach ($jadwal_per_hari[$hari_indo] as $jdwl) {
                $mapel_id = $jdwl['mapel_id'];
                $jurnal = $jurnal_map[$tgl][$mapel_id] ?? null;
                
                $hadir = 0; $sakit = 0; $izin = 0; $alpha = 0;
                if ($jurnal) {
                    try {
                        $stmt_absen = $siakad_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
                        $stmt_absen->execute([$jurnal['id']]);
                        $counts = $stmt_absen->fetchAll(PDO::FETCH_KEY_PAIR);
                        $hadir = $counts['Hadir'] ?? 0;
                        $sakit = $counts['Sakit'] ?? 0;
                        $izin = $counts['Izin'] ?? 0;
                        $alpha = $counts['Alpha'] ?? 0;
                    } catch (Exception $e) {}
                }
                
                $day_data['items'][] = [
                    'jadwal' => $jdwl,
                    'jurnal' => $jurnal,
                    'absen' => [
                        'hadir' => $hadir,
                        'sakit' => $sakit,
                        'izin' => $izin,
                        'alpha' => $alpha
                    ]
                ];
            }
            $final_display[] = $day_data;
        }
        $current = strtotime('-1 day', $current);
    }
}

$page_title = 'Monitor Jurnal';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Monitor Jurnal</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 15px;">
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="kelas_id" onchange="this.form.submit()" style="flex: 1; padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; outline: none; font-weight: 600; color: #334155;">
                <?php foreach($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $kelas_id == $c['id'] ? 'selected' : '' ?>>Kelas <?= htmlspecialchars($c['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; outline: none; font-weight: 600; color: #334155;">
        </form>
    </div>
    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0; margin-bottom: 15px; padding-left: 5px;">Membandingkan Jadwal Pelajaran vs Realisasi Pengisian Jurnal oleh Guru Mata Pelajaran.</p>

    <?php if (empty($final_display)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <div style="width: 64px; height: 64px; background: #fffbeb; color: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                <i data-lucide="calendar-x" style="width: 32px; height: 32px;"></i>
            </div>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada data jadwal/jurnal untuk bulan ini.</p>
        </div>
    <?php else: ?>
        <div style="position: relative; padding-left: 15px; border-left: 2px solid #e2e8f0; margin-left: 5px; margin-bottom: 30px; margin-top: 20px;">
            <?php foreach ($final_display as $idx => $day): 
                $fmt_date = date('d', strtotime($day['tanggal'])) . ' ' . $months_indo[date('m', strtotime($day['tanggal']))] . ' ' . date('Y', strtotime($day['tanggal']));
            ?>
                
                <div style="margin-left: -25px; margin-bottom: 15px; margin-top: <?= $idx === 0 ? '0' : '25px' ?>;">
                    <span style="font-size: 0.75rem; font-weight: 800; color: white; background: #d97706; padding: 6px 12px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i> <?= $day['hari_indo'] . ', ' . $fmt_date ?>
                    </span>
                </div>

                <div style="position: relative; margin-bottom: 15px;">
                    <div style="position: absolute; left: -21px; top: 15px; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; border: 2px solid white; box-shadow: 0 0 0 1px #f59e0b;"></div>
                    <div style="background: white; border-radius: 16px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        
                        <?php if ($day['is_special'] && $day['is_special']['is_full_day']): ?>
                            <div style="display: flex; align-items: center; gap: 12px; background: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fef2f2' : '#eff6ff' ?>; padding: 12px; border-radius: 12px; border: 1px solid <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fecaca' : '#bfdbfe' ?>; margin-bottom: 5px;">
                                <div style="background: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fee2e2' : '#dbeafe' ?>; color: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#dc2626' : '#2563eb' ?>; padding: 8px; border-radius: 8px;">
                                    <i data-lucide="<?= strtolower($day['is_special']['kategori']) === 'libur' ? 'calendar-off' : 'calendar-heart' ?>"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; color: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#991b1b' : '#1e40af' ?>; font-size: 0.95rem; text-transform: uppercase;"><?= htmlspecialchars($day['is_special']['kegiatan'] ?: $day['is_special']['kategori']) ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Kewajiban Jurnal Guru & Absen Mapel ditiadakan</div>
                                </div>
                            </div>
                        <?php else: ?>
                            
                        <?php foreach ($day['items'] as $itemIndex => $item): 
                            $jadwal = $item['jadwal'];
                            $jurnal = $item['jurnal'];
                            $absen = $item['absen'];
                            $is_filled = $jurnal !== null;
                            
                            $is_excused = false;
                            if ($day['is_special']) {
                                if (!empty($day['is_special']['jam_mulai_kosong']) && $day['is_special']['jam_mulai_kosong'] !== '00:00:00') {
                                    if ($jadwal['jam_mulai'] >= $day['is_special']['jam_mulai_kosong']) {
                                        $is_excused = true;
                                    }
                                } else {
                                    $is_excused = true;
                                }
                            }
                        ?>
                            <div style="<?= $itemIndex > 0 ? 'margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;' : '' ?>">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                                            <?= htmlspecialchars($jadwal['nama_mapel']) ?>
                                            <?php if ($is_excused): ?>
                                                <i data-lucide="minus-circle" style="color: #d97706; width: 14px; height: 14px;"></i>
                                            <?php elseif ($is_filled): ?>
                                                <i data-lucide="check-circle" style="color: #10b981; width: 14px; height: 14px;"></i>
                                            <?php else: ?>
                                                <i data-lucide="x-circle" style="color: #ef4444; width: 14px; height: 14px;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i> <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?>
                                            &nbsp;|&nbsp; <i data-lucide="user" style="width: 12px; height: 12px;"></i> 
                                            <?php 
                                            $tampil_guru = $jadwal['nama_guru'];
                                            if ($is_filled && !empty($jurnal['nama_guru_jurnal'])) {
                                                $tampil_guru = $jurnal['nama_guru_jurnal'];
                                            }
                                            echo htmlspecialchars($tampil_guru ?? '-');
                                            ?>
                                        </div>
                                    </div>
                                    <?php if (!$is_filled && !$is_excused): ?>
                                        <div style="background: #fef2f2; color: #ef4444; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">
                                            Belum Isi Jurnal
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($is_excused): ?>
                                    <div style="background: #fffbeb; border: 1px dashed #fcd34d; border-radius: 12px; padding: 10px; margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                        <div style="background: #fef3c7; color: #d97706; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: #b45309; font-size: 0.8rem; text-transform: uppercase;"><?= htmlspecialchars($day['is_special']['kegiatan'] ?: $day['is_special']['kategori']) ?></div>
                                            <div style="font-size: 0.7rem; color: #92400e; margin-top: 2px;">Kewajiban Jurnal & Absen ditiadakan</div>
                                        </div>
                                    </div>
                                <?php elseif ($is_filled): ?>
                                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; margin-top: 12px;">
                                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px; font-weight: 700;">Materi / Topik:</div>
                                            <p style="margin: 0; font-size: 0.85rem; color: #1e293b; line-height: 1.5; white-space: pre-wrap; font-weight: 500;"><?= htmlspecialchars($jurnal['materi']) ?></p>
                                            
                                            <?php if (!empty($jurnal['hambatan'])): ?>
                                                <div style="background: #fffbeb; padding: 8px 10px; border-radius: 8px; border-left: 3px solid #f59e0b; margin-top: 10px;">
                                                    <div style="font-size: 0.7rem; color: #d97706; margin-bottom: 2px; font-weight: 700;">Hambatan:</div>
                                                    <div style="font-size: 0.75rem; color: #92400e; font-style: italic;"><?= htmlspecialchars($jurnal['hambatan']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div style="margin-top: 12px; border-top: 1px solid #cbd5e1; padding-top: 10px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                                <span style="font-size:0.7rem; color:#475569; font-weight:700; margin-right: 4px;">Absen:</span>
                                                <span style="background:white; border: 1px solid #86efac; color:#16a34a; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">H: <?= $absen['hadir'] ?></span>
                                                <span style="background:white; border: 1px solid #fde047; color:#d97706; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">S: <?= $absen['sakit'] ?></span>
                                                <span style="background:white; border: 1px solid #93c5fd; color:#2563eb; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">I: <?= $absen['izin'] ?></span>
                                                <span style="background:white; border: 1px solid #fca5a5; color:#dc2626; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:700;">A: <?= $absen['alpha'] ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer_guru.php'; ?>
