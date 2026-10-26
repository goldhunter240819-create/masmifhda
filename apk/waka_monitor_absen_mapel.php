<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$today = $_GET['tgl'] ?? date('Y-m-d');
$title = "Absen Mapel Siswa";

// Ambil data absensi permapel hari ini
$raw_data = [];
try {
    $sql = "
        SELECT a.siswa_id, a.status, m.nama_mapel, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, p.jam_masuk, p.jam_pulang
        FROM " . $siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
        JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
        JOIN " . $siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
        LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
        LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
        LEFT JOIN " . $core_db->query("SELECT database()")->fetchColumn() . ".presensi p ON p.user_id = s.user_id AND p.tanggal = a.tanggal
        WHERE a.tanggal = ? AND s.status = 'Aktif'
        ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC, j.id ASC
    ";
    $stmt = $siakad_db->prepare($sql);
    $stmt->execute([$today]);
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Organisasi data
$kelasData = [];
$studentData = [];

foreach ($raw_data as $r) {
    $k_id = $r['kelas_id'];
    $s_id = $r['siswa_id'];
    
    if (!isset($kelasData[$k_id])) {
        // Ambil total siswa kelas ini
        try {
            $tot = $core_db->prepare("SELECT COUNT(*) FROM riwayat_kelas_siswa r JOIN siswa s ON r.siswa_id = s.id WHERE r.kelas_id = ? AND s.status='Aktif'");
            $tot->execute([$k_id]);
            $total_siswa = $tot->fetchColumn();
        } catch (Exception $e) {
            $total_siswa = 0;
        }
        
        $kelasData[$k_id] = [
            'id' => $k_id,
            'nama' => $r['nama_kelas'],
            'tingkat' => $r['tingkat'],
            'total' => $total_siswa,
            'hadir' => 0,
            'bolos' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'students' => []
        ];
    }
    
    if (!isset($studentData[$k_id][$s_id])) {
        $studentData[$k_id][$s_id] = [
            'siswa_id' => $s_id,
            'nama' => $r['nama'],
            'mapel_dict' => [],
            'masuk' => empty($r['jam_masuk']) || $r['jam_masuk'] == '00:00:00' ? '-' : $r['jam_masuk'],
            'pulang' => empty($r['jam_pulang']) || $r['jam_pulang'] == '00:00:00' ? '-' : $r['jam_pulang'],
            'final_status' => '-'
        ];
    }
    
    // Tambahkan mapel (deduplikasi per hari)
    $mapel_name = $r['nama_mapel'] ?? 'Mapel';
    $st = $r['status'];
    if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
    
    $studentData[$k_id][$s_id]['mapel_dict'][$mapel_name] = $st;
}

// Tentukan final status untuk masing-masing siswa dan agregasi ke kelas
foreach ($studentData as $k_id => $students) {
    foreach ($students as $s_id => $s_data) {
        $statuses = array_values($s_data['mapel_dict']);
        $masuk = $s_data['masuk'];
        $pulang = $s_data['pulang'];
        
        $has_hadir = in_array('Hadir', $statuses);
        $has_alpha = in_array('Alpha', $statuses);
        $has_bolos = in_array('Bolos', $statuses);
        
        $status_pusat = '-';
        if (!empty($statuses)) {
            $isBolos = false;
            $isAlpa = false;
            
            $is_today = ($today == date('Y-m-d'));
            $is_past_15 = (date('H') >= 15);
            $is_past_pulang = (!$is_today || $is_past_15);

            if ($has_hadir) {
                if ($masuk == '-') {
                    // Masuk gerbang belum scan, tapi mapel hadir = Bolos
                    $isBolos = true;
                } elseif ($has_alpha) {
                    // Masuk gerbang sudah, tapi ada mapel alpha (mapel lain hadir) = Bolos
                    $isBolos = true;
                } elseif ($pulang == '-' && $is_past_pulang) {
                    // Masuk gerbang sudah, mapel hadir semua, tapi belum scan pulang dan sudah lewat jam 15:00 = Bolos
                    $isBolos = true;
                }
                // Jika belum jam 15:00 dan belum scan pulang, status tetap akan menjadi Hadir
            } else {
                // Tidak ada mapel yang hadir sama sekali
                if ($has_alpha) {
                    $isAlpa = true;
                    // Jika sudah scan masuk gerbang tapi alpha di semua mapel = Bolos (kabur setelah masuk gerbang)
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
                // Jika pure alpha
                $status_pusat = 'Alpha';
                // Tapi kalau sudah masuk gerbang, jadi bolos
                if ($masuk != '-') $status_pusat = 'Bolos';
            }
        }
        
        $s_data['final_status'] = $status_pusat;
        
        $mapelArr = [];
        foreach ($s_data['mapel_dict'] as $m_name => $m_st) {
            $mapelArr[] = ['mapel' => $m_name, 'status' => $m_st];
        }
        $s_data['mapels'] = $mapelArr;
        
        $kelasData[$k_id]['students'][] = $s_data;
        
        if ($status_pusat === 'Hadir') $kelasData[$k_id]['hadir']++;
        elseif ($status_pusat === 'Bolos') $kelasData[$k_id]['bolos']++;
        elseif ($status_pusat === 'Sakit') $kelasData[$k_id]['sakit']++;
        elseif ($status_pusat === 'Izin') $kelasData[$k_id]['izin']++;
        elseif ($status_pusat === 'Alpha') $kelasData[$k_id]['alpha']++;
    }
}

$page_title = 'Monitor Absen Mapel Siswa';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="color: white; font-size: 1.2rem; margin: 0; flex: 1; text-align: center;">Absen Mapel Siswa</h1>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: 10px; margin-bottom: 20px;">
        <!-- Form Filter Tanggal -->
        <form action="" method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            <input type="date" name="tgl" value="<?= htmlspecialchars($today) ?>" style="flex: 1 1 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
            <div style="display: flex; gap: 10px; width: 100%;">
                <button type="submit" style="flex: 1; justify-content: center; background: #8b5cf6; color: #fff; border: none; padding: 12px 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-filter" style="width: 16px; height: 16px;"></i> Filter
                </button>
                <a href="waka_monitor_absen_mapel_rekap.php" style="flex: 1; justify-content: center; background: #10b981; color: #fff; text-decoration: none; padding: 12px 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-calendar-check" style="width: 16px; height: 16px;"></i> Rekap
                </a>
            </div>
        </form>

        <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 15px 0;">Monitoring Per Kelas (Jurnal)</h3>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php if(empty($kelasData)): ?>
                <div style="text-align: center; padding: 30px; color: #94a3b8; font-size: 0.9rem; font-weight: 600; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    Belum ada guru yang mengisi absensi/jurnal mapel pada tanggal ini.
                </div>
            <?php else: ?>
                <?php foreach($kelasData as $k_id => $k): ?>
                <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                    <div style="padding: 12px 15px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-users" style="color: #64748b; width: 16px;"></i> Kelas <?= htmlspecialchars($k['nama']) ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; background: #e2e8f0; padding: 3px 8px; border-radius: 8px;">
                                <?= $k['total'] ?> Siswa
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 15px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px;">
                        <!-- Hadir -->
                        <div onclick="showModal(<?= $k_id ?>, 'Hadir', 'Hadir')" style="cursor: pointer; text-align: center; background: #ecfdf5; padding: 6px 2px; border-radius: 8px; border: 1px solid #a7f3d0; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;">
                            <div style="font-size: 1rem; font-weight: 800; color: #059669; line-height: 1;"><?= $k['hadir'] ?></div>
                            <div style="font-size: 0.55rem; font-weight: 700; color: #047857; margin-top: 4px; word-break: break-all;">Hadir</div>
                        </div>
                        <!-- Sakit -->
                        <div onclick="showModal(<?= $k_id ?>, 'Sakit', 'Sakit')" style="cursor: pointer; text-align: center; background: #fff1f2; padding: 6px 2px; border-radius: 8px; border: 1px solid #fecdd3; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;">
                            <div style="font-size: 1rem; font-weight: 800; color: #e11d48; line-height: 1;"><?= $k['sakit'] ?></div>
                            <div style="font-size: 0.55rem; font-weight: 700; color: #be123c; margin-top: 4px; word-break: break-all;">Sakit</div>
                        </div>
                        <!-- Izin -->
                        <div onclick="showModal(<?= $k_id ?>, 'Izin', 'Izin')" style="cursor: pointer; text-align: center; background: #e0f2fe; padding: 6px 2px; border-radius: 8px; border: 1px solid #bae6fd; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;">
                            <div style="font-size: 1rem; font-weight: 800; color: #0284c7; line-height: 1;"><?= $k['izin'] ?></div>
                            <div style="font-size: 0.55rem; font-weight: 700; color: #0369a1; margin-top: 4px; word-break: break-all;">Izin</div>
                        </div>
                        <!-- Alpha -->
                        <div onclick="showModal(<?= $k_id ?>, 'Alpha', 'Alpha')" style="cursor: pointer; text-align: center; background: #fef2f2; padding: 6px 2px; border-radius: 8px; border: 1px solid #fca5a5; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;">
                            <div style="font-size: 1rem; font-weight: 800; color: #b91c1c; line-height: 1;"><?= $k['alpha'] ?></div>
                            <div style="font-size: 0.55rem; font-weight: 700; color: #991b1b; margin-top: 4px; word-break: break-all;">Alpha</div>
                        </div>
                        <!-- Bolos -->
                        <div onclick="showModal(<?= $k_id ?>, 'Bolos', 'Bolos')" style="cursor: pointer; text-align: center; background: #fffbeb; padding: 6px 2px; border-radius: 8px; border: 1px solid #fde68a; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; box-sizing: border-box;">
                            <div style="font-size: 1rem; font-weight: 800; color: #d97706; line-height: 1;"><?= $k['bolos'] ?></div>
                            <div style="font-size: 0.55rem; font-weight: 700; color: #b45309; margin-top: 4px; word-break: break-all;">Bolos</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="studentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10000; align-items: center; justify-content: center; padding: 20px 20px 90px 20px; box-sizing: border-box; backdrop-filter: blur(3px);">
    <div style="background: #fff; width: 100%; max-width: 400px; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div id="modalTitle" style="font-weight: 800; color: #1e293b; font-size: 1.05rem;">Daftar Siswa</div>
            <button onclick="closeModal()" style="background: #e2e8f0; border: none; color: #64748b; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.2rem; font-weight: bold;">&times;</button>
        </div>
        <div id="modalContent" style="padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
            <!-- content goes here -->
        </div>
    </div>
</div>

<script>
var kelasData = <?= json_encode($kelasData) ?>;

function showModal(k_id, statusFilter, title) {
    var k = kelasData[k_id];
    var students = k.students;
    
    document.getElementById('modalTitle').innerText = 'Kelas ' + k.nama + ' - ' + title;
    var content = '';
    
    for (var i = 0; i < students.length; i++) {
        var s = students[i];
        
        var match = false;
        var st = s.final_status;
        if (statusFilter === 'Hadir' && st === 'Hadir') match = true;
        if (statusFilter === 'Bolos' && st === 'Bolos') match = true;
        if (statusFilter === 'Sakit' && st === 'Sakit') match = true;
        if (statusFilter === 'Izin' && st === 'Izin') match = true;
        if (statusFilter === 'Alpha' && st === 'Alpha') match = true;
        
        if (match) {
            var bgColor = '#f8fafc'; var color = '#64748b';
            if (st === 'Hadir') { bgColor = '#ecfdf5'; color = '#059669'; }
            else if (st === 'Bolos') { bgColor = '#fffbeb'; color = '#d97706'; }
            else if (st === 'Sakit') { bgColor = '#fff1f2'; color = '#dc2626'; }
            else if (st === 'Izin') { bgColor = '#e0f2fe'; color = '#0284c7'; }
            else if (st === 'Alpha') { bgColor = '#fef2f2'; color = '#b91c1c'; }
            
            var mapelHtml = '';
            if (s.mapels && s.mapels.length > 0) {
                mapelHtml += '<div style="margin-top: 8px; display: flex; flex-direction: column; gap: 4px; border-top: 1px dashed rgba(0,0,0,0.1); padding-top: 8px;">';
                
                var masukStr = s.masuk !== '-' ? s.masuk.substring(0, 5) : 'Belum Scan';
                var masukColor = s.masuk !== '-' ? '#059669' : '#dc2626';
                mapelHtml += `
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem;">
                        <span style="color: #475569; font-weight: 700;"><i class="fa-solid fa-arrow-right-to-bracket" style="width:14px; margin-right:3px;"></i> Masuk Gerbang</span>
                        <span style="font-weight: 800; color: ${masukColor};">${masukStr}</span>
                    </div>
                `;
                
                for(var j=0; j<s.mapels.length; j++) {
                    var m = s.mapels[j];
                    var mColor = '#64748b';
                    var mStatus = m.status;
                    if(mStatus === 'Bolos' || mStatus === 'Alpa') mStatus = 'Alpha';
                    
                    if(mStatus == 'Hadir') mColor = '#059669';
                    else if(mStatus == 'Sakit') mColor = '#dc2626';
                    else if(mStatus == 'Izin') mColor = '#0284c7';
                    else if(mStatus == 'Alpha') mColor = '#b91c1c';
                    
                    mapelHtml += `
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem;">
                            <span style="color: #475569; padding-left: 17px;">${m.mapel}</span>
                            <span style="font-weight: 700; color: ${mColor};">${mStatus}</span>
                        </div>
                    `;
                }
                
                var pulangStr = s.pulang !== '-' ? s.pulang.substring(0, 5) : 'Belum Scan';
                var pulangColor = s.pulang !== '-' ? '#059669' : '#dc2626';
                mapelHtml += `
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem;">
                        <span style="color: #475569; font-weight: 700;"><i class="fa-solid fa-arrow-right-from-bracket" style="width:14px; margin-right:3px;"></i> Keluar Gerbang</span>
                        <span style="font-weight: 800; color: ${pulangColor};">${pulangStr}</span>
                    </div>
                `;
                
                mapelHtml += '</div>';
            }
            
            var waktuScan = s.masuk !== '-' ? '<span style="font-size: 0.7rem; color: #10b981; font-weight: 700; margin-left: 10px;"><i class="fa-regular fa-clock"></i> ' + s.masuk.substring(0, 5) + '</span>' : '<span style="font-size: 0.7rem; color: #ef4444; font-weight: 700; margin-left: 10px;"><i class="fa-solid fa-circle-exclamation"></i> Belum Scan</span>';
            
            content += `
                <div style="background: ${bgColor}; padding: 12px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem; display: inline-block;">${s.nama}</div>
                            ${waktuScan}
                        </div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: ${color}; background: #fff; padding: 2px 6px; border-radius: 6px; border: 1px solid ${color};">${st}</div>
                    </div>
                    ${mapelHtml}
                </div>
            `;
        }
    }
    
    if (content === '') {
        content = '<div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 0.85rem;">Tidak ada siswa dengan status ini.</div>';
    }
    
    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('studentModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('studentModal').style.display = 'none';
}
</script>

<?php include 'footer_guru.php'; ?>
