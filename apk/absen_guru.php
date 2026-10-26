<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$current_month = date('Y-m');

// Rekap Kehadiran Bulan Ini
$rekap_bulan_ini = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'T' => 0];
try {
    $stmt_rab = $siakad_db->prepare("SELECT status, COUNT(*) as total FROM absensi_guru WHERE guru_id = :id AND tanggal LIKE :bln GROUP BY status");
    $stmt_rab->execute(['id' => $guru_id, 'bln' => $current_month . '%']);
    $r_abs = $stmt_rab->fetchAll(PDO::FETCH_ASSOC);
    foreach($r_abs as $r) {
        $stat = $r['status'];
        if (in_array(strtolower($stat), ['hadir', 'sakit', 'izin', 'alpa', 'terlambat'])) {
            $kode = substr(ucfirst(strtolower($stat)), 0, 1);
        } else {
            $kode = $stat;
        }
        if (!isset($rekap_bulan_ini[$kode])) $rekap_bulan_ini[$kode] = 0;
        $rekap_bulan_ini[$kode] += $r['total'];
    }
} catch (PDOException $e) {}

// Log Absen Terakhir (maksimal 30 untuk riwayat)
$log_terakhir = [];
$log_dates = [];
try {
    $stmt_log = $siakad_db->prepare("SELECT * FROM absensi_guru WHERE guru_id = :id ORDER BY tanggal DESC LIMIT 30");
    $stmt_log->execute(['id' => $guru_id]);
    $log_terakhir = $stmt_log->fetchAll(PDO::FETCH_ASSOC);
    foreach ($log_terakhir as $log) {
        $log_dates[] = $log['tanggal'];
    }
} catch (PDOException $e) {}

// Detail absensi per mapel untuk tanggal-tanggal di log
$detail_permapel = [];
if (!empty($log_dates)) {
    try {
        $inQuery = implode(',', array_fill(0, count($log_dates), '?'));
        $stmt_det = $siakad_db->prepare("
            SELECT a.tanggal, a.status, m.nama_mapel, g.nama as nama_guru 
            FROM absensi_permapel a 
            LEFT JOIN mata_pelajaran m ON a.mapel_id = m.id 
            LEFT JOIN aula_db.guru g ON a.guru_id = g.id
            WHERE a.guru_id = ? AND a.tanggal IN ($inQuery)
        ");
        $params = array_merge([$guru_id], $log_dates);
        $stmt_det->execute($params);
        $res_det = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res_det as $row) {
            $detail_permapel[$row['tanggal']][] = $row;
        }
    } catch (PDOException $e) {}
}

// Rekap Bulanan
$rekap_bulanan = [];
try {
    $stmt_rb = $siakad_db->prepare("
        SELECT DATE_FORMAT(tanggal, '%Y-%m') as bulan, status, COUNT(*) as total 
        FROM absensi_guru 
        WHERE guru_id = :id 
        GROUP BY bulan, status 
        ORDER BY bulan DESC 
        LIMIT 60
    ");
    $stmt_rb->execute(['id' => $guru_id]);
    $res_rb = $stmt_rb->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res_rb as $row) {
        $b = $row['bulan'];
        $stat = strtolower($row['status']);
        $kode = in_array($stat, ['hadir', 'sakit', 'izin', 'alpa', 'terlambat']) ? substr(ucfirst($stat), 0, 1) : $row['status'];
        if (!isset($rekap_bulanan[$b])) {
            $rekap_bulanan[$b] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'T' => 0];
        }
        if (!isset($rekap_bulanan[$b][$kode])) $rekap_bulanan[$b][$kode] = 0;
        $rekap_bulanan[$b][$kode] += $row['total'];
    }
} catch (PDOException $e) {}

$page_title = 'Absensi';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding-bottom: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Monitoring Kehadiran</p>
            <h1>Data Absensi Guru</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9;">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -3rem; z-index: 10; margin-bottom: 15px; padding: 15px;">
        <a href="izin_guru.php" class="btn-primary" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; text-decoration: none; padding: 14px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
            <i class="fa-solid fa-file-signature"></i> Ajukan Izin / Cuti
        </a>
    </div>

    <div class="data-card" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 10px; text-align: center;">Rekap Bulan Ini</h3>
        <div style="display: flex; justify-content: space-around; text-align: center;">
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);"><?= $rekap_bulan_ini['H'] ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">Hadir</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning);"><?= $rekap_bulan_ini['S'] + $rekap_bulan_ini['I'] ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">Sakit/Izin</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--danger);"><?= $rekap_bulan_ini['A'] ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">Alpa</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #7f1d1d;"><?= $rekap_bulan_ini['T'] ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700;">Terlambat</div>
            </div>
        </div>
    </div>

    <h3 class="section-title" style="margin-top: 1.5rem;">Riwayat Presensi</h3>
    <div class="data-card" style="padding: 10px 1.5rem;">
        <?php if (count($log_terakhir) > 0): ?>
            <?php foreach ($log_terakhir as $index => $a): 
                $stat = $a['status'] ?: 'H';
                if (in_array(strtolower($stat), ['hadir', 'sakit', 'izin', 'alpa', 'terlambat'])) {
                    $statText = ucfirst(strtolower($stat));
                    $statKode = substr($statText, 0, 1);
                } else {
                    $statText = ($stat=='H'?'Hadir':($stat=='S'?'Sakit':($stat=='I'?'Izin':($stat=='B'?'Terlambat':'Alpa'))));
                    $statKode = $stat;
                }
                $colorCls = $statKode=='H'?'value-green':($statKode=='A' || $statKode=='T'?'value-red':'value-orange');
                $iconCls = $statKode=='H'?'fa-arrow-right-to-bracket':'fa-notes-medical';
                
                $hiddenStyle = $index >= 5 ? 'display: none;' : '';
                $hiddenClass = $index >= 5 ? 'riwayat-hidden' : '';
            ?>
                <div class="list-item <?= $hiddenClass ?>" style="flex-wrap: wrap; <?= $hiddenStyle ?>">
                    <div style="display: flex; width: 100%; align-items: center;">
                        <div class="list-icon"><i class="fa-solid <?= $iconCls ?>"></i></div>
                        <div class="list-content">
                            <div class="list-title"><?= htmlspecialchars($a['tanggal']) ?></div>
                            <div class="list-sub">Jam Masuk: <?= htmlspecialchars(isset($a['created_at']) ? date('H:i', strtotime($a['created_at'])) : '-') ?></div>
                        </div>
                        <div class="list-amount <?= $colorCls ?>" style="font-size: 0.8rem; display: flex; align-items: center;">
                            <?= htmlspecialchars($statText) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($log_terakhir) > 5): ?>
                <div id="btn-show-more" style="text-align: center; margin-top: 15px; padding-bottom: 5px;">
                    <button type="button" onclick="showMoreRiwayat()" style="background: transparent; border: 1px solid var(--primary); color: var(--primary); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; cursor: pointer;">
                        Tampilkan Lebih Banyak
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem; padding: 15px;">Belum ada log absen.</div>
        <?php endif; ?>
    </div>

    <?php if (count($rekap_bulanan) > 0): ?>
    <h3 class="section-title" style="margin-top: 1.5rem;">Rekap Per Bulan</h3>
    <div class="data-card" style="padding: 10px 1.5rem; margin-bottom: 2rem;">
        <?php foreach ($rekap_bulanan as $bln => $rkp): 
            $blnArr = explode('-', $bln);
            $namaBln = date("F", mktime(0, 0, 0, (int)$blnArr[1], 1)) . ' ' . $blnArr[0];
        ?>
            <div style="border-bottom: 1px solid var(--border-color); padding: 10px 0; margin-bottom: 5px;">
                <div style="font-weight: 600; margin-bottom: 8px; color: var(--text-color);"><?= htmlspecialchars($namaBln) ?></div>
                <div style="display: flex; justify-content: space-around; text-align: center; font-size: 0.8rem;">
                    <div><span style="color: var(--primary); font-weight: 700; font-size: 1.1rem;"><?= $rkp['H'] ?></span><br><span style="color: var(--text-muted); font-size: 0.7rem;">Hadir</span></div>
                    <div><span style="color: var(--warning); font-weight: 700; font-size: 1.1rem;"><?= $rkp['S']+$rkp['I'] ?></span><br><span style="color: var(--text-muted); font-size: 0.7rem;">Sakit/Izin</span></div>
                    <div><span style="color: var(--danger); font-weight: 700; font-size: 1.1rem;"><?= $rkp['A'] ?></span><br><span style="color: var(--text-muted); font-size: 0.7rem;">Alpa</span></div>
                    <div><span style="color: #7f1d1d; font-weight: 700; font-size: 1.1rem;"><?= $rkp['T'] ?></span><br><span style="color: var(--text-muted); font-size: 0.7rem;">Terlambat</span></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <script>
    function toggleDetail(id) {
        const el = document.getElementById(id);
        const isOpening = (el.style.display === 'none' || el.style.display === '');
        
        // Tutup semua dropdown detail-mapel
        const allDetails = document.querySelectorAll('.detail-mapel');
        allDetails.forEach(detail => {
            detail.style.display = 'none';
        });

        // Jika sebelumnya tertutup, maka buka
        if (isOpening) {
            el.style.display = 'block';
        }
    }
    
    function showMoreRiwayat() {
        const hiddens = document.querySelectorAll('.riwayat-hidden');
        hiddens.forEach(el => {
            el.style.display = ''; 
            el.classList.remove('riwayat-hidden');
        });
        document.getElementById('btn-show-more').style.display = 'none';
    }
    </script>
</div>

<?php include 'footer_guru.php'; ?>
