<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$title = "Rekap Absen Mapel";
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$filter_kelas_id = $_GET['kelas_id'] ?? '';

// Ambil list kelas untuk dropdown
$kelasList = [];
try {
    $kelasList = $core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

$rekap_list = [];

if (!empty($filter_kelas_id)) {
    // Ambil semua absensi mapel untuk kelas terpilih di rentang tanggal
    $sql = "
        SELECT a.siswa_id, a.tanggal, a.status, s.nama, k.nama_kelas, m.nama_mapel, p.jam_masuk, p.jam_pulang
        FROM " . $siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
        JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
        JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
        JOIN " . $siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
        LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
        LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".presensi p ON p.user_id = s.user_id AND p.tanggal = a.tanggal
        WHERE a.tanggal >= ? AND a.tanggal <= ? AND s.kelas_id = ? AND s.status = 'Aktif'
        ORDER BY a.tanggal ASC, s.nama ASC, j.id ASC
    ";
    
    try {
        $stmt = $siakad_db->prepare($sql);
        $stmt->execute([$start_date, $end_date, $filter_kelas_id]);
        $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $temp = [];
        foreach ($raw_data as $r) {
            $sid = $r['siswa_id'];
            $tgl = $r['tanggal'];
            
            if (!isset($temp[$sid])) {
                $temp[$sid] = [
                    'siswa_id' => $sid,
                    'nama' => $r['nama'],
                    'nama_kelas' => $r['nama_kelas'],
                    'dates' => []
                ];
            }
            
            if (!isset($temp[$sid]['dates'][$tgl])) {
                $temp[$sid]['dates'][$tgl] = [
                    'mapel_dict' => [],
                    'masuk' => empty($r['jam_masuk']) ? '-' : $r['jam_masuk'],
                    'pulang' => empty($r['jam_pulang']) ? '-' : $r['jam_pulang']
                ];
            }
            
            $st = $r['status'];
            if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
            
            $temp[$sid]['dates'][$tgl]['mapel_dict'][$r['nama_mapel'] ?? 'Mapel'] = $st;
        }
        
        foreach ($temp as $sid => $sData) {
            $rekap_list[$sid] = [
                'siswa_id' => $sid,
                'nama' => $sData['nama'],
                'nama_kelas' => $sData['nama_kelas'],
                'hadir' => 0,
                'bolos' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'total_hari' => 0
            ];
            
            foreach ($sData['dates'] as $tgl => $dData) {
                $rekap_list[$sid]['total_hari']++;
                
                $statuses = array_values($dData['mapel_dict']);
                $masuk = $dData['masuk'];
                $pulang = $dData['pulang'];
                
                $status_pusat = '-';
                $has_hadir = in_array('Hadir', $statuses);
                $has_alpha = in_array('Alpha', $statuses);
                $has_bolos = in_array('Bolos', $statuses);
                
                if (!empty($statuses)) {
                    $isBolos = false;
                    $isAlpa = false;
                    
                    $is_today = ($tgl == date('Y-m-d'));
                    $is_past_15 = (date('H') >= 15);
                    $is_past_pulang = (!$is_today || $is_past_15);

                    if ($has_hadir) {
                        if ($masuk == '-') {
                            $isBolos = true;
                        } elseif ($has_alpha) {
                            $isBolos = true;
                        } elseif ($pulang == '-' && $is_past_pulang) {
                            $isBolos = true;
                        }
                    } else {
                        if ($has_alpha) {
                            $isAlpa = true;
                            if ($masuk != '-') {
                                $isBolos = true;
                                $isAlpa = false;
                            }
                        }
                    }
                    
                    if ($isBolos) {
                        $status_pusat = 'Bolos';
                    } elseif ($isAlpa) {
                        $status_pusat = 'Alpha';
                    } else {
                        $status_pusat = 'Hadir';
                    }
                    
                    // PRIORITAS MUTLAK Izin/Sakit/Alpa: Jika semua mapel seragam tanpa Hadir, abaikan scan!
                    if (in_array('Sakit', $statuses) && !$has_hadir && !$has_alpha && !$has_bolos) {
                        $status_pusat = 'Sakit';
                    } elseif (in_array('Izin', $statuses) && !$has_hadir && !$has_alpha && !$has_bolos) {
                        $status_pusat = 'Izin';
                    } elseif (in_array('Alpha', $statuses) && !$has_hadir && !in_array('Izin', $statuses) && !in_array('Sakit', $statuses) && !$has_bolos) {
                        $status_pusat = 'Alpha';
                        if ($masuk != '-') $status_pusat = 'Bolos';
                    }
                }
                
                if ($status_pusat === 'Hadir') $rekap_list[$sid]['hadir']++;
                elseif ($status_pusat === 'Bolos') $rekap_list[$sid]['bolos']++;
                elseif ($status_pusat === 'Sakit') $rekap_list[$sid]['sakit']++;
                elseif ($status_pusat === 'Izin') $rekap_list[$sid]['izin']++;
                elseif ($status_pusat === 'Alpha') $rekap_list[$sid]['alpha']++;
            }
        }
    } catch (Exception $e) {}
}

$page_title = 'Rekap Absen Mapel';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="header-top">
        <a href="waka_monitor_absen_mapel.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="color: white; font-size: 1.2rem; margin: 0; flex: 1; text-align: center;">Rekap Absensi Jurnal</h1>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <!-- Filter Form -->
    <div class="data-card" style="margin-top: 10px; z-index: 10; margin-bottom: 20px;">
        <form action="" method="GET">
            <h3 class="section-title" style="margin-top:0;">Filter Rekap</h3>

            <div style="display:grid; gap:12px; margin-bottom:15px;">
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Kelas</label>
                    <select name="kelas_id" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kelas_id==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Dari</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Sampai</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                    </div>
                </div>
            </div>
            <button type="submit" style="width:100%; padding:12px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:bold; font-size:0.95rem; cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Rekap
            </button>
        </form>
    </div>

    <?php if (!empty($filter_kelas_id)): ?>
    <!-- Rekap Cards -->
    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom: 30px;">
        <?php if (empty($rekap_list)): ?>
            <div style="text-align:center; padding:30px; background:white; border-radius:12px; color:#94a3b8;">
                <i class="fa-solid fa-inbox" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                Belum ada data absensi untuk filter ini.
            </div>
        <?php else: ?>
            <?php 
                // Sort by Hadir percentage descending
                uasort($rekap_list, function($a, $b) {
                    $pctA = $a['total_hari'] > 0 ? ($a['hadir'] / $a['total_hari']) : 0;
                    $pctB = $b['total_hari'] > 0 ? ($b['hadir'] / $b['total_hari']) : 0;
                    return $pctB <=> $pctA;
                });
                
                $no = 1;
                foreach($rekap_list as $r):
                $total = $r['total_hari'];
                $pct = $total > 0 ? round(($r['hadir'] / $total) * 100) : 100;
                $barColor = $pct >= 75 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            ?>
            <div style="background:white; border-radius:12px; padding:14px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                    <div>
                        <div style="font-weight:700; color:#1e293b; font-size:0.95rem;"><?= $no++ ?>. <?= htmlspecialchars($r['nama']) ?></div>
                        <div style="font-size:0.75rem; color:#94a3b8;">Kelas <?= htmlspecialchars($r['nama_kelas']) ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:1.2rem; font-weight:800; color:<?= $barColor ?>;"><?= $pct ?>%</div>
                        <div style="font-size:0.7rem; color:#94a3b8;">Kehadiran</div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div style="background:#f1f5f9; border-radius:10px; height:6px; margin-bottom:10px; overflow:hidden;">
                    <div style="height:100%; width:<?= $pct ?>%; background:<?= $barColor ?>; border-radius:10px; transition:width 0.5s;"></div>
                </div>
                <!-- Status badges -->
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <span style="background:#ecfdf5; color:#059669; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">✓ Hadir: <?= $r['hadir'] ?></span>
                    <span style="background:#fff1f2; color:#e11d48; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">⚕ Sakit: <?= $r['sakit'] ?></span>
                    <span style="background:#e0f2fe; color:#0284c7; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">📝 Izin: <?= $r['izin'] ?></span>
                    <span style="background:#fef2f2; color:#dc2626; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">✗ Alpha: <?= $r['alpha'] ?></span>
                    <span style="background:#fffbeb; color:#d97706; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">🏃 Bolos: <?= $r['bolos'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer_guru.php'; ?>
