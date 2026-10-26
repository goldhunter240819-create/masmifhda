<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil kelas & mapel yang diampu
$kelasList = [];
$mengajarData = [];

$ta_q = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetch();
$ta_id = $ta_q ? $ta_q['id'] : 1;
$ta_sem = $ta_q ? $ta_q['semester'] : 1;

try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas 
        FROM mengajar m JOIN aula_db.kelas k ON k.id = m.kelas_id
        WHERE m.guru_id = ? AND m.tahun_ajaran_id = ? AND m.semester = ?
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$guru_id, $ta_id, $ta_sem]);
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("
        SELECT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
        FROM mengajar m JOIN mapel mp ON mp.id = m.mapel_id
        WHERE m.guru_id = ? AND m.tahun_ajaran_id = ? AND m.semester = ?
        ORDER BY mp.nama_mapel
    ");
    $stmt_m->execute([$guru_id, $ta_id, $ta_sem]);
    $mengajarData = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    try {
        $stmt_k = $siakad_db->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM jadwal_pelajaran jp JOIN aula_db.kelas k ON k.id = jp.kelas_id WHERE jp.guru_id = ? ORDER BY k.tingkat, k.nama_kelas");
        $stmt_k->execute([$guru_id]);
        $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

        $stmt_m = $siakad_db->prepare("SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel FROM jadwal_pelajaran jp JOIN mapel mp ON mp.id = jp.mata_pelajaran_id WHERE jp.guru_id = ? ORDER BY mp.nama_mapel");
        $stmt_m->execute([$guru_id]);
        $mengajarData = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e2) {}
}

// Filter params
$filter_kelas  = isset($_GET['kelas_id'])  ? intval($_GET['kelas_id'])  : 0;
$filter_mapel  = isset($_GET['mapel_id'])  ? intval($_GET['mapel_id'])  : 0;
$filter_dari   = isset($_GET['dari'])      ? $_GET['dari']              : date('Y-m-01');
$filter_sampai = isset($_GET['sampai'])    ? $_GET['sampai']            : date('Y-m-d');

// Ambil rekap
$rekap = [];
$nama_kelas = '';
$nama_mapel = '';
$totalPertemuan = 0;

if ($filter_kelas && $filter_mapel) {
    try {
        // Nama kelas & mapel
        $r = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $r->execute([$filter_kelas]); $row = $r->fetch(); $nama_kelas = $row ? $row['nama_kelas'] : '';
        $r = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
        $r->execute([$filter_mapel]); $row = $r->fetch(); $nama_mapel = $row ? $row['nama_mapel'] : '';

        // Total pertemuan (jurnal dalam rentang)
        $r = $siakad_db->prepare("SELECT COUNT(*) FROM jurnal_guru WHERE kelas_id=? AND mapel_id=? AND tanggal BETWEEN ? AND ?");
        $r->execute([$filter_kelas, $filter_mapel, $filter_dari, $filter_sampai]);
        $totalPertemuan = $r->fetchColumn();

        // Ambil daftar siswa di kelas tersebut dari core DB
        $stmt_siswa = $core_db->prepare("
            SELECT s.id, s.nama, s.nis 
            FROM riwayat_kelas_siswa r 
            JOIN siswa s ON r.siswa_id = s.id 
            WHERE r.kelas_id = ? AND r.tahun_ajaran_id = ? AND s.status = 'Aktif'
            ORDER BY s.nama
        ");
        $stmt_siswa->execute([$filter_kelas, $ta_id]);
        $siswaList = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);
        
        // Ambil rekap dari siakad DB
        $stmt_absen = $siakad_db->prepare("
            SELECT 
                siswa_id,
                SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) AS hadir,
                SUM(CASE WHEN status='Izin'  THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN status='Alpha' OR status='Alpa' THEN 1 ELSE 0 END) AS alpha,
                COUNT(id) AS total_tercatat
            FROM absensi_permapel 
            WHERE mapel_id = ? AND kelas_id = ? AND tanggal BETWEEN ? AND ?
            GROUP BY siswa_id
        ");
        $stmt_absen->execute([$filter_mapel, $filter_kelas, $filter_dari, $filter_sampai]);
        $absenData = $stmt_absen->fetchAll(PDO::FETCH_ASSOC);
        
        // Gabungkan data
        $absenMap = [];
        foreach ($absenData as $ad) {
            $absenMap[$ad['siswa_id']] = $ad;
        }
        
        $rekap = [];
        foreach ($siswaList as $s) {
            $sid = $s['id'];
            $rData = isset($absenMap[$sid]) ? $absenMap[$sid] : ['hadir'=>0, 'izin'=>0, 'sakit'=>0, 'alpha'=>0, 'total_tercatat'=>0];
            $rekap[] = [
                'id' => $s['id'],
                'nama' => $s['nama'],
                'nis' => $s['nis'],
                'hadir' => $rData['hadir'],
                'izin' => $rData['izin'],
                'sakit' => $rData['sakit'],
                'alpha' => $rData['alpha'],
                'total_tercatat' => $rData['total_tercatat']
            ];
        }
    } catch (Exception $e) {
        $rekap = [];
    }
}

$page_title = 'Rekap Absen Mapel';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Laporan Kehadiran</p>
            <h1>Rekap Absen Per Mapel</h1>
        </div>
        <div class="avatar" style="color: #7c3aed;">
            <i class="fa-solid fa-chart-bar"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <!-- Filter Form -->
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <form action="" method="GET">
            <h3 class="section-title" style="margin-top:0;">Filter Rekap</h3>

            <div style="display:grid; gap:12px; margin-bottom:15px;">
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Kelas</label>
                    <select name="kelas_id" id="kelas_id" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;" onchange="onKelasChange()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kelas==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;" data-selected="<?= htmlspecialchars($filter_mapel) ?>">
                        <option value="">-- Pilih Mapel --</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Dari</label>
                        <input type="date" name="dari" value="<?= $filter_dari ?>" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; font-weight:600; color:#475569; font-size:0.85rem;">Sampai</label>
                        <input type="date" name="sampai" value="<?= $filter_sampai ?>" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                    </div>
                </div>
            </div>
            <button type="submit" style="width:100%; padding:12px; background:#7c3aed; color:white; border:none; border-radius:8px; font-weight:bold; font-size:0.95rem; cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Rekap
            </button>
        </form>
    </div>

    <?php if ($filter_kelas && $filter_mapel): ?>
    <!-- Info Header -->
    <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9); border-radius:12px; padding:15px; margin-top:15px; color:white;">
        <div style="font-size:0.8rem; opacity:0.8;">Mata Pelajaran</div>
        <div style="font-size:1.1rem; font-weight:700;"><?= htmlspecialchars($nama_mapel) ?></div>
        <div style="font-size:0.8rem; opacity:0.8; margin-top:4px;">Kelas <?= htmlspecialchars($nama_kelas) ?> &bull; <?= date('d M Y', strtotime($filter_dari)) ?> – <?= date('d M Y', strtotime($filter_sampai)) ?></div>
        <div style="margin-top:8px; background:rgba(255,255,255,0.15); display:inline-block; padding:4px 12px; border-radius:20px; font-size:0.8rem; font-weight:700;">
            <?= $totalPertemuan ?> Pertemuan
        </div>
    </div>

    <!-- Rekap Cards -->
    <div style="margin-top:15px; display:flex; flex-direction:column; gap:10px;">
        <?php if (empty($rekap)): ?>
            <div style="text-align:center; padding:30px; background:white; border-radius:12px; color:#94a3b8;">
                <i class="fa-solid fa-inbox" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                Belum ada data absensi untuk filter ini.
            </div>
        <?php else: ?>
            <?php foreach($rekap as $no => $r):
                $hadir = intval($r['hadir']);
                $alpha = intval($r['alpha']);
                $sakit = intval($r['sakit']);
                $izin  = intval($r['izin']);
                $pct   = $totalPertemuan > 0 ? round(($hadir / $totalPertemuan) * 100) : 100;
                $barColor = $pct >= 75 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            ?>
            <div style="background:white; border-radius:12px; padding:14px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                    <div>
                        <div style="font-weight:700; color:#1e293b; font-size:0.95rem;"><?= ($no+1) ?>. <?= htmlspecialchars($r['nama']) ?></div>
                        <div style="font-size:0.75rem; color:#94a3b8;"><?= htmlspecialchars($r['nis'] ?? '') ?></div>
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
                    <span style="background:#dcfce7; color:#16a34a; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">✓ Hadir: <?= $hadir ?></span>
                    <span style="background:#fef3c7; color:#d97706; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">⚕ Sakit: <?= $sakit ?></span>
                    <span style="background:#dbeafe; color:#1d4ed8; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">📝 Izin: <?= $izin ?></span>
                    <span style="background:#fee2e2; color:#dc2626; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">✗ Alpha: <?= $alpha ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const mengajarData = <?= json_encode($mengajarData ?? []) ?>;

function onKelasChange() {
    updateMapelDropdown();
}

function updateMapelDropdown(initialValue = null) {
    var kelas_id = document.getElementById('kelas_id').value;
    var mapelSelect = document.getElementById('mapel_id');
    var currentVal = initialValue || mapelSelect.dataset.selected || mapelSelect.value;
    
    mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
    if (!kelas_id) return;
    
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
    
    mapelSelect.dataset.selected = ''; // clear sesudah dipakai
}

document.addEventListener('DOMContentLoaded', function() {
    updateMapelDropdown();
});
</script>

<?php include 'footer_guru.php'; ?>
