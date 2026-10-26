<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$today = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$detail_kelas = isset($_GET['detail_kelas']) ? intval($_GET['detail_kelas']) : 0;
$title = "Monitor Scan QR";

// =========================================================================
// AJAX HANDLER UNTUK POPUP
// =========================================================================
if (isset($_GET['ajax_kelas']) && isset($_GET['ajax_status'])) {
    $kelas_id = intval($_GET['ajax_kelas']);
    $status_filter = $_GET['ajax_status'];
    
    $stmt = $core_db->prepare("
        SELECT s.id as siswa_id, s.nama, s.nis, p.status as gate_status, p.jam_masuk as created_at
        FROM siswa s
        LEFT JOIN presensi p ON s.user_id = p.user_id AND p.tanggal = ? AND p.role_id = 3
        WHERE s.kelas_id = ? AND s.status = 'Aktif'
        ORDER BY s.nama ASC
    ");
    $stmt->execute([$today, $kelas_id]);
    $absen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_pusat = $siakad_db->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE tanggal = ? AND kelas_id = ?");
    $stmt_pusat->execute([$today, $kelas_id]);
    $pusat_kelas = $stmt_pusat->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $filtered_students = [];
    foreach ($absen_list as $row) {
        $st = '';
        $has_scanned_pagi = !empty($row['created_at']) && $row['created_at'] !== '00:00:00';
        
        if ($has_scanned_pagi && !empty($row['gate_status'])) {
            $st = $row['gate_status'];
        } else if (isset($pusat_kelas[$row['siswa_id']])) {
            $pst = $pusat_kelas[$row['siswa_id']];
            // Jangan ambil status 'Hadir' dari guru mapel jika belum scan QR
            if ($pst !== 'Hadir') {
                $st = $pst;
            }
        }
        
        $kategori = 'Hadir'; // Default jika ada status tak terduga
        if (empty($st) || $st == '-') {
            $kategori = 'Belum';
        } else {
            if ($st == 'Hadir') $kategori = 'Hadir';
            elseif ($st == 'Terlambat') $kategori = 'Terlambat';
            elseif ($st == 'Sakit') $kategori = 'Sakit';
            elseif ($st == 'Izin') $kategori = 'Izin';
            elseif ($st == 'Alpa' || $st == 'Alpha') $kategori = 'Alpa';
            elseif ($st == 'Bolos') $kategori = 'Alpa';
        }
        
        if ($kategori === $status_filter) {
            $filtered_students[] = [
                'nama' => $row['nama'],
                'nis' => $row['nis'],
                'waktu' => $row['created_at'] ? substr($row['created_at'], 0, 5) : '-'
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $filtered_students]);
    exit;
}

// Ambil daftar kelas
$kelasList = [];
try {
    $stmt = $core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
    $kelasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Monitor Scan QR';
include 'header.php';

// TAMPILAN DETAIL PER KELAS (JIKA DIKLIK)
// =========================================================================
if ($detail_kelas > 0) {
    $kelas_nama = "";
    foreach ($kelasList as $k) {
        if ($k['id'] == $detail_kelas) $kelas_nama = $k['nama_kelas'];
    }

    $absen_list = [];
    try {
        $stmt = $core_db->prepare("
            SELECT s.id, s.nama, s.nis, s.foto, p.status, p.jam_masuk as created_at
            FROM siswa s
            LEFT JOIN presensi p ON s.user_id = p.user_id AND p.tanggal = ? AND p.role_id = 3
            WHERE s.kelas_id = ? AND s.status = 'Aktif'
            ORDER BY s.nama ASC
        ");
        $stmt->execute([$today, $detail_kelas]);
        $absen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    ?>
    <div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
        <div class="header-top">
            <div class="greeting">
                <p style="color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; margin-bottom: 5px;">DETAIL KELAS</p>
                <h1 style="font-size: 1.4rem;">Kelas <?= htmlspecialchars($kelas_nama) ?></h1>
            </div>
            <div class="avatar" style="color: #8b5cf6; background: white;" onclick="window.location.href='wakasis_qr_absen.php?tanggal=<?= $today ?>'">
                <i class="fa-solid fa-arrow-left"></i>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 15px;">
            <div style="margin-bottom:15px; font-weight:700; color:#475569; font-size:0.9rem; margin-left: 5px; display: flex; justify-content: space-between;">
                <span>Log Scan (<?= date('d M Y', strtotime($today)) ?>)</span>
                <span style="color: #94a3b8; font-size: 0.8rem;">Total: <?= count($absen_list) ?> Siswa</span>
            </div>

            <?php if(empty($absen_list)): ?>
                <div style="text-align:center; padding:30px; background:#fff; border-radius:15px; color:#94a3b8; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-inbox" style="font-size: 40px; margin-bottom:10px; color: #cbd5e1;"></i><br>
                    Belum ada data siswa untuk kelas ini.
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php
                    // Ambil data status pusat khusus untuk kelas ini
                    $stmt_pusat = $siakad_db->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE tanggal = ? AND kelas_id = ?");
                    $stmt_pusat->execute([$today, $detail_kelas]);
                    $pusat_kelas = $stmt_pusat->fetchAll(PDO::FETCH_KEY_PAIR);
                    
                    foreach($absen_list as $row): 
                        $st = '';
                        $has_scanned_pagi = !empty($row['created_at']) && $row['created_at'] !== '00:00:00' && !empty($row['status']);
                        
                        if ($has_scanned_pagi) {
                            $st = $row['status'];
                        } else if (isset($pusat_kelas[$row['id']])) {
                            $pst = $pusat_kelas[$row['id']];
                            if ($pst !== 'Hadir') {
                                $st = $pst;
                            }
                        }
                        
                        if (empty($st) || $st == '-') {
                            $st = 'Belum Scan';
                        }
                        
                        $st_color = '#94a3b8'; $st_bg = '#f1f5f9';
                        if ($st === 'Hadir') { $st_color = '#10b981'; $st_bg = '#ecfdf5'; }
                        if ($st === 'Terlambat') { $st_color = '#f59e0b'; $st_bg = '#fffbeb'; }
                        if ($st === 'Sakit') { $st_color = '#3b82f6'; $st_bg = '#eff6ff'; }
                        if ($st === 'Izin') { $st_color = '#8b5cf6'; $st_bg = '#f5f3ff'; }
                        if ($st === 'Alpa' || $st === 'Alpha') { $st_color = '#ef4444'; $st_bg = '#fef2f2'; }
                        if ($st === 'Bolos') { $st_color = '#db2777'; $st_bg = '#fdf2f8'; }
                    ?>
                    <div style="background:#fff; border:1px solid #f1f5f9; border-radius:15px; padding:15px; box-shadow:0 2px 10px rgba(0,0,0,0.02); display:flex; align-items:center; gap:12px;">
                        <div style="flex:1;">
                            <div style="font-weight:800; color:#1e293b; font-size:0.9rem; margin-bottom:3px;"><?= htmlspecialchars($row['nama']) ?></div>
                            <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($row['nis']) ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="display:inline-block; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:800; background:<?= $st_bg ?>; color:<?= $st_color ?>;">
                                <?= $st ?>
                            </div>
                            <?php if($has_scanned && !empty($row['created_at'])): ?>
                            <div style="font-size:0.7rem; color:#94a3b8; margin-top:5px; font-weight:700;">
                                <i class="fa-regular fa-clock" style="margin-right:3px;"></i><?= substr($row['created_at'], 0, 5) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    include 'footer_guru.php';
    exit;
}

// =========================================================================
// TAMPILAN MONITORING REKAP PER KELAS (DEFAULT)
// =========================================================================

// Calculate stats for all classes
$classStats = [];

// Siapkan data pusat untuk mempercepat query
$stmt_pusat_all = $siakad_db->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE tanggal = ?");
$stmt_pusat_all->execute([$today]);
$pusat_all = $stmt_pusat_all->fetchAll(PDO::FETCH_KEY_PAIR);

foreach ($kelasList as $kelas) {
    $k_id = $kelas['id'];
    
    $stats = ['Hadir' => 0, 'Terlambat' => 0, 'Belum' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0, 'total' => 0];
    
    $stmt = $core_db->prepare("
        SELECT s.id as siswa_id, p.status as gate_status, p.jam_masuk
        FROM siswa s
        LEFT JOIN presensi p ON s.user_id = p.user_id AND p.tanggal = ? AND p.role_id = 3
        WHERE s.kelas_id = ? AND s.status = 'Aktif'
    ");
    $stmt->execute([$today, $k_id]);
    $absen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($absen_list as $row) {
        $stats['total']++;
        
        // Penentuan status: 
        // 1. Prioritas utama: Gate (scan gerbang)
        // 2. Jika tidak ada di gate, ambil dari Status Pusat (input admin / guru mapel)
        $st = '';
        $has_scanned_pagi = !empty($row['jam_masuk']) && $row['jam_masuk'] !== '00:00:00';

        if ($has_scanned_pagi && !empty($row['gate_status'])) {
            $st = $row['gate_status'];
        } else if (isset($pusat_all[$row['siswa_id']])) {
            $pst = $pusat_all[$row['siswa_id']];
            // Jangan ambil status 'Hadir' dari guru mapel jika belum scan QR
            if ($pst !== 'Hadir') {
                $st = $pst;
            }
        }

        if (empty($st) || $st == '-') {
            $stats['Belum']++;
        } else {
            if ($st == 'Hadir') $stats['Hadir']++;
            elseif ($st == 'Terlambat') $stats['Terlambat']++;
            elseif ($st == 'Sakit') $stats['Sakit']++;
            elseif ($st == 'Izin') $stats['Izin']++;
            elseif ($st == 'Alpa' || $st == 'Alpha') $stats['Alpa']++;
            elseif ($st == 'Bolos') $stats['Alpa']++; 
            else $stats['Hadir']++;
        }
    }
    
    $kelas['stats'] = $stats;
    $classStats[] = $kelas;
}
?>

<div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); position: relative; overflow: hidden;">
    <!-- Geometric Pattern Overlay ala MTS RS -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background-image: linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(60deg, #ffffff77 25%, transparent 25.5%, transparent 75%, #ffffff77 75%, #ffffff77), linear-gradient(60deg, #ffffff77 25%, transparent 25.5%, transparent 75%, #ffffff77 75%, #ffffff77); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;"></div>
    
    <div class="header-top" style="position: relative; z-index: 2;">
        <div class="greeting">
            <p style="color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; margin-bottom: 5px;">MONITORING HARIAN</p>
            <h1 style="font-size: 1.4rem;">QR Absen Siswa</h1>
        </div>
        <div class="avatar" style="color: #8b5cf6; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 20px; border-radius: 20px;">
        
        <!-- Toolbar -->
        <div style="margin-bottom: 25px;">
            <form id="filterForm" method="GET" style="display: flex; gap: 8px;">
                <div style="flex: 1; position: relative;">
                    <input type="date" name="tanggal" value="<?= htmlspecialchars($today) ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-size: 0.95rem; color: #334155; background: white; outline: none; box-sizing: border-box; font-weight: 600;">
                </div>
                <button type="submit" style="background: #8b5cf6; color: white; border: none; border-radius: 12px; padding: 0 15px; font-family: inherit; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <!-- Tombol ini kalau mau diarahkan ke cetak laporan -->
                <button type="button" onclick="window.location.href='wakasis_qr_absen.php'" style="background: #10b981; color: white; border: none; border-radius: 12px; padding: 0 15px; font-family: inherit; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-file-excel"></i> Rekap
                </button>
            </form>
        </div>

        <h3 style="margin: 0 0 15px 0; font-size: 1.1rem; color: #1e293b; font-weight: 800;">Monitoring Per Kelas</h3>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($classStats as $k): ?>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                <!-- Class Header -->
                <div style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; color: #334155; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-users" style="color: #94a3b8;"></i> Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                    </div>
                    <div style="font-size: 0.75rem; background: #f1f5f9; padding: 4px 12px; border-radius: 20px; font-weight: 800; color: #64748b;">
                        <?= $k['stats']['total'] ?> Siswa
                    </div>
                </div>
                
                <!-- Stats Grid -->
                <div style="padding: 15px; display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; text-align: center;">
                    <!-- Hadir -->
                    <div style="border: 1px solid #a7f3d0; background: #ecfdf5; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Hadir', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #059669; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Hadir'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #10b981; text-transform: uppercase; word-break: break-all;">Hadir</div>
                    </div>
                    <!-- Terlambat -->
                    <div style="border: 1px solid #fde68a; background: #fffbeb; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Terlambat', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #d97706; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Terlambat'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; word-break: break-all;">Terlambat</div>
                    </div>
                    <!-- Belum -->
                    <div style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Belum', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #475569; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Belum'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #64748b; text-transform: uppercase; word-break: break-all;">Belum</div>
                    </div>
                    <!-- Sakit -->
                    <div style="border: 1px solid #fecaca; background: #fef2f2; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Sakit', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #b91c1c; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Sakit'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #ef4444; text-transform: uppercase; word-break: break-all;">Sakit</div>
                    </div>
                    <!-- Izin -->
                    <div style="border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Izin', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Izin'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #3b82f6; text-transform: uppercase; word-break: break-all;">Izin</div>
                    </div>
                    <!-- Alpa -->
                    <div style="border: 1px solid #fca5a5; background: #fef2f2; border-radius: 8px; padding: 10px 5px; cursor: pointer; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;" onclick="showStatusModal(<?= $k['id'] ?>, 'Alpa', '<?= htmlspecialchars($k['nama_kelas']) ?>')">
                        <div style="font-size: 1.1rem; font-weight: 800; color: #991b1b; line-height: 1; margin-bottom: 4px;"><?= $k['stats']['Alpa'] ?></div>
                        <div style="font-size: 0.55rem; font-weight: 800; color: #dc2626; text-transform: uppercase; word-break: break-all;">Alpa</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Popup -->
<div id="statusModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease;">
    <div style="background:#fff; width:90%; max-width:400px; border-radius:20px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.2); transform:translateY(20px); transition:transform 0.3s ease;" id="statusModalContent">
        <div style="background:#8b5cf6; padding:15px 20px; color:white; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="margin:0; font-size:1.1rem;" id="modalTitle">Detail Siswa</h3>
                <p style="margin:0; font-size:0.8rem; opacity:0.8;" id="modalSubtitle">Memuat data...</p>
            </div>
            <button onclick="closeStatusModal()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <div style="padding:15px; max-height:60vh; overflow-y:auto;" id="modalBody">
            <div style="text-align:center; padding:20px; color:#94a3b8;">Memuat data...</div>
        </div>
    </div>
</div>

<script>
function showStatusModal(kelasId, status, namaKelas) {
    document.getElementById('statusModal').style.display = 'flex';
    document.getElementById('modalTitle').innerText = status + ' - Kelas ' + namaKelas;
    document.getElementById('modalSubtitle').innerText = 'Memuat data...';
    document.getElementById('modalBody').innerHTML = '<div style="text-align:center; padding:20px; color:#94a3b8;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><br><br>Memuat data...</div>';
    
    // Animate in
    setTimeout(() => {
        document.getElementById('statusModal').style.opacity = '1';
        document.getElementById('statusModalContent').style.transform = 'translateY(0)';
    }, 10);
    
    // Fetch data
    const url = 'wakasis_qr_absen.php?ajax_kelas=' + kelasId + '&ajax_status=' + status + '&tanggal=<?= $today ?>';
    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                document.getElementById('modalSubtitle').innerText = data.length + ' Siswa (' + status + ')';
                
                if (data.length === 0) {
                    document.getElementById('modalBody').innerHTML = '<div style="text-align:center; padding:20px; color:#94a3b8;">Tidak ada data.</div>';
                } else {
                    let html = '<div style="display:flex; flex-direction:column; gap:10px;">';
                    data.forEach((siswa, index) => {
                        html += `
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px; display:flex; gap:12px; align-items:center;">
                            <div style="width:30px; height:30px; border-radius:50%; background:#8b5cf6; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem; flex-shrink: 0;">
                                ${index+1}
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight:700; color:#1e293b; font-size:0.9rem;">${siswa.nama}</div>
                                <div style="font-size:0.75rem; color:#64748b;">${siswa.nis}</div>
                            </div>
                            ${siswa.waktu !== '-' ? `<div style="font-size:0.7rem; font-weight: 700; color:#10b981;"><i class="fa-regular fa-clock"></i> ${siswa.waktu}</div>` : `<div style="font-size:0.7rem; font-weight: 700; color:#ef4444;"><i class="fa-solid fa-circle-exclamation"></i> Belum Scan</div>`}
                        </div>`;
                    });
                    html += '</div>';
                    document.getElementById('modalBody').innerHTML = html;
                }
            } else {
                document.getElementById('modalBody').innerHTML = '<div style="text-align:center; padding:20px; color:#ef4444;">Gagal memuat data.</div>';
            }
        })
        .catch(err => {
            document.getElementById('modalBody').innerHTML = '<div style="text-align:center; padding:20px; color:#ef4444;">Terjadi kesalahan.</div>';
        });
}

function closeStatusModal() {
    document.getElementById('statusModal').style.opacity = '0';
    document.getElementById('statusModalContent').style.transform = 'translateY(20px)';
    setTimeout(() => {
        document.getElementById('statusModal').style.display = 'none';
    }, 300);
}
</script>

<?php include 'footer_guru.php'; ?>
