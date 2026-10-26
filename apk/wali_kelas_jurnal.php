<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek Wali Kelas
$stmt_wk = $core_db->prepare("SELECT gt.id, gt.keterangan as nama_kelas, k.id as kelas_id FROM guru_tugas gt JOIN kelas k ON (gt.keterangan = CONCAT('Kelas ', k.nama_kelas) OR gt.keterangan = k.nama_kelas) WHERE gt.guru_id = :gid AND gt.tugas = 'Wali Kelas' LIMIT 1");
$stmt_wk->execute(['gid' => $guru_id]);
$wali_kelas = $stmt_wk->fetch(PDO::FETCH_ASSOC);

if (!$wali_kelas) {
    header("Location: akses_ditolak.php");
    exit;
}

$kelas_id = $wali_kelas['kelas_id'] ?? 0;
$nama_kelas_wk = $wali_kelas['nama_kelas'];

// Ambil filter bulan
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Helper Tanggal Indo
$days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

function formatTanggalIndo($date) {
    global $days_indo, $months_indo;
    $d = date('l', strtotime($date));
    $m = date('m', strtotime($date));
    return $days_indo[$d] . ', ' . date('d', strtotime($date)) . ' ' . $months_indo[$m] . ' ' . date('Y', strtotime($date));
}

// Ambil tahun ajaran aktif
$stmt_ta = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
$active_ta = $stmt_ta->fetch(PDO::FETCH_ASSOC);
$ta_id = $active_ta ? $active_ta['id'] : 0;

// 1. Ambil Jadwal Pelajaran Kelas Ini (hanya tahun ajaran aktif)
$stmt_jadwal = $siakad_db->prepare("
    SELECT jp.hari, jp.jam_mulai, jp.jam_selesai, m.nama_mapel, g.nama as nama_guru, jp.mata_pelajaran_id, jp.guru_id
    FROM jadwal_pelajaran jp
    JOIN mata_pelajaran m ON m.id = jp.mata_pelajaran_id
    LEFT JOIN aula_db.guru g ON g.id = jp.guru_id
    WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ?
    ORDER BY FIELD(jp.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jp.jam_mulai
");
$stmt_jadwal->execute([$kelas_id, $ta_id]);
$jadwal_list = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

$jadwal_per_hari = [];
foreach ($jadwal_list as $jdwl) {
    // Filter out Pembiasaan, Ekstrakurikuler, Senam, Upacara, dll
    $nama_mapel = strtolower($jdwl['nama_mapel']);
    $exclude_words = ['pembiasaan', 'ekstra', 'senam', 'upacara', 'sholat', 'pramuka', 'juz amma', 'asmaul husna'];
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

    $hari = $jdwl['hari'];
    $mapel_id = $jdwl['mata_pelajaran_id'];
    
    // Group per hari per mapel (jadikan 1 row saja dari jam mulai s/d jam selesai)
    if (!isset($jadwal_per_hari[$hari][$mapel_id])) {
        $jadwal_per_hari[$hari][$mapel_id] = $jdwl;
    } else {
        if (strtotime($jdwl['jam_selesai']) > strtotime($jadwal_per_hari[$hari][$mapel_id]['jam_selesai'])) {
            $jadwal_per_hari[$hari][$mapel_id]['jam_selesai'] = $jdwl['jam_selesai'];
        }
    }
}

// 2. Ambil Jurnal Guru untuk bulan ini
$stmt_jurnal = $siakad_db->prepare("
    SELECT jg.id, jg.tanggal, jg.mapel_id, jg.materi, jg.keterangan, 
           g.nama as nama_guru_jurnal, m.nama_mapel
    FROM jurnal_guru jg
    JOIN aula_db.guru g ON g.id = jg.guru_id
    JOIN mata_pelajaran m ON m.id = jg.mapel_id
    WHERE jg.kelas_id = ? AND DATE_FORMAT(jg.tanggal, '%Y-%m') = ?
");
$stmt_jurnal->execute([$kelas_id, $bulan]);
$jurnals_raw = $stmt_jurnal->fetchAll(PDO::FETCH_ASSOC);

$jurnal_map = [];
foreach($jurnals_raw as $jr) {
    // Kita simpan berdasarkan tanggal dan mapel_id
    $jurnal_map[$jr['tanggal']][$jr['mapel_id']] = $jr;
}

// 3. Bangun Rekap Berdasarkan Hari di Bulan Ini (Hanya dari hari ini ke belakang)
$start_date = $bulan . '-01';
$end_date = date('Y-m-t', strtotime($start_date));
if ($bulan == date('Y-m')) {
    $end_date = date('Y-m-d'); // limit to today if current month
}

$current = strtotime($end_date);
$start = strtotime($start_date);
$final_display = [];

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

while ($current >= $start) {
    $tgl = date('Y-m-d', $current);
    $hari_inggris = date('l', $current);
    $hari_indo = $days_indo[$hari_inggris];
    
    if ($hari_indo != 'Minggu') {
        if (isset($jadwal_per_hari[$hari_indo])) {
            $day_data = [
                'tanggal' => $tgl,
                'hari_indo' => $hari_indo,
                'is_special' => isset($special_dates[$tgl]) ? $special_dates[$tgl] : false,
                'items' => []
            ];
            
            foreach ($jadwal_per_hari[$hari_indo] as $jdwl) {
                $mapel_id = $jdwl['mata_pelajaran_id'];
                $jurnal = $jurnal_map[$tgl][$mapel_id] ?? null;
                
                // Ambil absensi jika ada jurnal
                $hadir = 0; $sakit = 0; $izin = 0; $alpha = 0;
                if ($jurnal) {
                    try {
                        $stmt_absen = $siakad_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
                        $stmt_absen->execute([$jurnal['id']]);
                        $counts = $stmt_absen->fetchAll(PDO::FETCH_KEY_PAIR);
                        $hadir = $counts['Hadir'] ?? 0;
                        $sakit = $counts['Sakit'] ?? 0;
                        $izin = $counts['Izin'] ?? 0;
                        $alpha = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0);
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
    }
    
    $current -= 86400; // mundur 1 hari
}

$page_title = 'Monitoring Jurnal Kelas';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Buku Wali Kelas</p>
            <h1>Monitoring Jurnal</h1>
        </div>
        <div class="avatar" style="color: #d97706; background: white;">
            <i class="fa-solid fa-book-journal-whills"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 class="section-title" style="margin: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: #d97706; margin-right: 5px;"></i> Jurnal Kelas <?= htmlspecialchars($nama_kelas_wk) ?></h3>
            <form method="GET" style="margin: 0;">
                <input type="month" name="bulan" value="<?= $bulan ?>" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; outline: none;">
            </form>
        </div>
        <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 15px;">Membandingkan Jadwal Pelajaran vs Realisasi Pengisian Jurnal oleh Guru Mata Pelajaran.</p>
        
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-top: 15px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 8px;"><i class="fa-solid fa-print"></i> Cetak Jurnal Kelas</div>
            <form action="/siakad/guru/wali-kelas/jurnal/cetak" target="_blank" method="GET" style="margin: 0;">
                <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.7rem; color: #64748b; margin-bottom: 3px;">Dari Tanggal</label>
                        <input type="date" name="start" value="<?= $bulan ?>-01" required style="width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.7rem; color: #64748b; margin-bottom: 3px;">Sampai Tanggal</label>
                        <input type="date" name="end" value="<?= date('Y-m-t', strtotime($bulan . '-01')) ?>" required style="width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem;">
                    </div>
                </div>
                <button type="submit" style="width: 100%; background: #b45309; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> Cetak Jurnal
                </button>
            </form>
        </div>
    </div>

    <?php if (empty($final_display)): ?>
        <div style="text-align: center; padding: 20px; color: #64748b;">
            <i class="fa-regular fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
            <p style="font-size: 0.85rem;">Belum ada data jadwal/jurnal untuk bulan ini.</p>
        </div>
    <?php else: ?>
        <div style="position: relative; padding-left: 15px; border-left: 2px solid #e2e8f0; margin-left: 10px; margin-bottom: 30px; margin-top: 20px;">
            <?php foreach ($final_display as $idx => $day): ?>
                
                <div style="margin-left: -25px; margin-bottom: 15px; margin-top: <?= $idx === 0 ? '0' : '25px' ?>;">
                    <span style="font-size: 0.8rem; font-weight: 800; color: white; background: #b45309; padding: 4px 12px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> <?= formatTanggalIndo($day['tanggal']) ?>
                    </span>
                </div>

                <div style="position: relative; margin-bottom: 15px;">
                    <div style="position: absolute; left: -21px; top: 15px; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; border: 2px solid white; box-shadow: 0 0 0 1px #f59e0b;"></div>
                    <div style="background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        
                        <?php if ($day['is_special'] && $day['is_special']['is_full_day']): ?>
                            <div style="display: flex; align-items: center; gap: 12px; background: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fef2f2' : '#eff6ff' ?>; padding: 12px; border-radius: 12px; border: 1px solid <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fecaca' : '#bfdbfe' ?>; margin-bottom: 5px;">
                                <div style="background: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#fee2e2' : '#dbeafe' ?>; color: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#dc2626' : '#2563eb' ?>; padding: 8px; border-radius: 8px;">
                                    <i class="fa-solid <?= strtolower($day['is_special']['kategori']) === 'libur' ? 'fa-calendar-xmark' : 'fa-calendar-check' ?>"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; color: <?= strtolower($day['is_special']['kategori']) === 'libur' ? '#991b1b' : '#1e40af' ?>; font-size: 0.95rem; text-transform: uppercase;"><?= htmlspecialchars($day['is_special']['kegiatan'] ?: $day['is_special']['kategori']) ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Kewajiban Jurnal Guru ditiadakan</div>
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
                            <div style="margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                                            <?= htmlspecialchars($jadwal['nama_mapel']) ?>
                                            <?php if ($is_excused): ?>
                                                <i class="fa-solid fa-circle-minus" style="color: #d97706; font-size: 0.9rem;" title="Ditiadakan"></i>
                                            <?php elseif ($is_filled): ?>
                                                <i class="fa-solid fa-circle-check" style="color: #16a34a; font-size: 0.9rem;" title="Sudah Diisi"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-circle-xmark" style="color: #dc2626; font-size: 0.9rem;" title="Belum Diisi"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                                            <i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>
                                            &nbsp;|&nbsp; <i class="fa-solid fa-user-tie" style="margin-right: 3px;"></i> 
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
                                        <div style="background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                            Belum Isi Jurnal
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($is_excused): ?>
                                    <div style="background: #fffbeb; border: 1px dashed #fcd34d; border-radius: 8px; padding: 10px; margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                        <div style="background: #fef3c7; color: #d97706; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-circle-info" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: #b45309; font-size: 0.8rem; text-transform: uppercase;"><?= htmlspecialchars($day['is_special']['kegiatan'] ?: $day['is_special']['kategori']) ?></div>
                                            <div style="font-size: 0.7rem; color: #92400e; margin-top: 2px;">Kewajiban Jurnal & Absen ditiadakan</div>
                                        </div>
                                    </div>
                                <?php elseif ($is_filled): ?>
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-top: 10px;">
                                        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 3px; font-weight: 700;">Materi / Topik:</div>
                                        <p style="margin: 0; font-size: 0.85rem; color: #1e293b; line-height: 1.5; white-space: pre-wrap; font-weight: 500;"><?= htmlspecialchars($jurnal['materi']) ?></p>
                                        
                                        <?php if (!empty($jurnal['keterangan'])): ?>
                                            <div style="background: #fef3c7; padding: 6px 8px; border-radius: 6px; border-left: 3px solid #f59e0b; margin-top: 8px;">
                                                <div style="font-size: 0.7rem; color: #b45309; margin-bottom: 2px; font-weight: 700;"><i class="fa-solid fa-note-sticky" style="margin-right:4px;"></i>Catatan:</div>
                                                <div style="font-size: 0.75rem; color: #78350f; font-style: italic;"><?= htmlspecialchars($jurnal['keterangan']) ?></div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div style="margin-top: 10px; border-top: 1px solid #cbd5e1; padding-top: 8px; display: flex; gap: 5px; flex-wrap: wrap;">
                                            <span style="font-size:0.7rem; color:#475569; font-weight:700; margin-right: 5px; margin-top: 2px;">Absen:</span>
                                            <span style="background:white; border: 1px solid #dcfce7; color:#16a34a; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">H: <?= $absen['hadir'] ?></span>
                                            <span style="background:white; border: 1px solid #fef3c7; color:#d97706; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">S: <?= $absen['sakit'] ?></span>
                                            <span style="background:white; border: 1px solid #dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">I: <?= $absen['izin'] ?></span>
                                            <span style="background:white; border: 1px solid #fee2e2; color:#dc2626; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">A: <?= $absen['alpha'] ?></span>
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

    <a href="wali_kelas.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Wali Kelas
    </a>
</div>

<?php include 'footer_guru.php'; ?>
