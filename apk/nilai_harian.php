<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$logged_in_guru_id = $_SESSION['guru_id'];
$guru_id = $logged_in_guru_id;
$user_id = $_SESSION['user_id'] ?? 0;

$is_kamad = false;
$is_wakakur = false;

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

$page_title = 'Nilai Harian';
include 'header.php';

// Ambil tahun ajaran aktif dari institusi
$institusi = [];
try {
    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $institusi = $stmt_inst->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
$semester = $institusi['semester'] == 'Genap' ? 2 : 1;

// Ambil kelas yang diampu
$kelasList = [];
try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas 
        FROM jadwal_pelajaran j 
        JOIN kelas k ON j.kelas_id = k.id 
        WHERE j.guru_id = ? AND j.tahun_ajaran_id = ?
        ORDER BY k.nama_kelas ASC
    ");
    $stmt_k->execute([$guru_id, $tahun_ajaran_id]);
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil riwayat nilai
$historyListRaw = [];
try {
    $stmt_h = $core_db->prepare("
        SELECT n.id, n.kelas_id, n.mapel_id, n.jenis_evaluasi, n.judul_materi, n.tanggal,
               COUNT(d.id) as jumlah_siswa,
               SUM(CASE WHEN d.nilai IS NOT NULL THEN 1 ELSE 0 END) as ikut_siswa
        FROM nilai_harian n
        LEFT JOIN nilai_harian_detail d ON n.id = d.nilai_harian_id
        WHERE n.guru_id = ? AND n.tahun_ajaran_id = ? AND n.semester = ?
        GROUP BY n.id
        ORDER BY n.created_at DESC
    ");
    $stmt_h->execute([$guru_id, $tahun_ajaran_id, $semester]);
    $historyListRaw = $stmt_h->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Helper untuk fetch mapel dan kelas nama di history
$kelasMap = [];
$mapelMap = [];
try {
    $k_res = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(PDO::FETCH_ASSOC);
    foreach($k_res as $k) $kelasMap[$k['id']] = $k['nama_kelas'];
    
    $m_res = $siakad_db->query("SELECT id, nama_mapel FROM mata_pelajaran")->fetchAll(PDO::FETCH_ASSOC);
    foreach($m_res as $m) $mapelMap[$m['id']] = $m['nama_mapel'];
} catch(Exception $e) {}

// Fetch detail & siswa untuk accordion
$kelas_ids = array_unique(array_column($historyListRaw, 'kelas_id'));
$history_ids = array_column($historyListRaw, 'id');

$siswaMap = [];
$detailMap = [];

try {
    // Get tahun ajaran name from core_db using tahun_ajaran_id
    $ta_name = ''; 
    try {
        $stmt_ta = $core_db->prepare("SELECT name FROM tahun_ajaran WHERE id = ?");
        $stmt_ta->execute([$tahun_ajaran_id]);
        if ($res_ta = $stmt_ta->fetch(PDO::FETCH_ASSOC)) {
            $ta_name = $res_ta['name'];
        }
    } catch(Exception $e) {}

    if (!empty($kelas_ids)) {
        $in_kelas = str_repeat('?,', count($kelas_ids) - 1) . '?';
        $params = array_values($kelas_ids);
        if ($ta_name !== '') {
            $params[] = $ta_name;
            $stmt_s = $core_db->prepare("SELECT id, nama as nama_lengkap, kelas_id FROM siswa WHERE kelas_id IN ($in_kelas) AND tahun_ajaran = ? AND status = 'Aktif' ORDER BY nama ASC");
        } else {
            $stmt_s = $core_db->prepare("SELECT id, nama as nama_lengkap, kelas_id FROM siswa WHERE kelas_id IN ($in_kelas) AND status = 'Aktif' ORDER BY nama ASC");
        }
        $stmt_s->execute($params);
        $siswaRes = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
        foreach($siswaRes as $s) {
            $siswaMap[$s['kelas_id']][] = $s;
        }
    }
    
    if (!empty($history_ids)) {
        $in_hist = str_repeat('?,', count($history_ids) - 1) . '?';
        $stmt_d = $core_db->prepare("SELECT nilai_harian_id, siswa_id, nilai FROM nilai_harian_detail WHERE nilai_harian_id IN ($in_hist)");
        $stmt_d->execute(array_values($history_ids));
        $detailRes = $stmt_d->fetchAll(PDO::FETCH_ASSOC);
        foreach($detailRes as $d) {
            $detailMap[$d['nilai_harian_id']][$d['siswa_id']] = $d['nilai'];
        }
    }
} catch (Exception $e) {}

// Grouping untuk UI
$historyGrouped = [];
foreach($historyListRaw as $h) {
    $nama_kelas = $kelasMap[$h['kelas_id']] ?? 'Unknown';
    $nama_mapel = $mapelMap[$h['mapel_id']] ?? 'Unknown';
    
    $tingkat = 'Lainnya';
    if (preg_match('/^([IVX]+|\d+)/i', $nama_kelas, $matches)) {
        $tingkat = strtoupper($matches[1]);
    }
    
    $historyGrouped[$tingkat][$nama_mapel][] = $h;
}


?>
<style>
    .tabs {
        display: flex;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .tab {
        flex: 1;
        text-align: center;
        padding: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.2s ease;
        cursor: pointer;
        color: #64748b;
    }
    .tab.active {
        background: white;
        color: #0ea5e9;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border-radius: 8px;
    }
    .history-card {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }
    .input-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
        background: #f8fafc;
        box-sizing: border-box;
        outline: none;
    }
    .input-group select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }
    .accordion-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        cursor: pointer;
        padding: 15px;
    }
    .accordion-body {
        display: none;
        border-top: 1px solid #f1f5f9;
        padding: 15px;
    }
    .accordion-icon {
        transition: transform 0.2s;
        color: #94a3b8;
    }
    .accordion-icon.open {
        transform: rotate(180deg);
    }
    .student-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.8rem;
    }
    .student-row:last-child {
        border-bottom: none;
    }
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>MENU GURU</p>
            <h1>Nilai Harian</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;">
            <i class="fa-solid fa-file-pen"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(isset($_SESSION['msg'])): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: #065f46; font-size: 0.9rem; font-weight: 600;"><?= $_SESSION['msg'] ?></p>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div class="tabs">
            <div class="tab active" id="tabInput" onclick="switchTab('input')">Input Baru</div>
            <div class="tab" id="tabRiwayat" onclick="switchTab('riwayat')">Riwayat Nilai</div>
        </div>
        
        <!-- TAB INPUT BARU -->
        <div id="contentInput">
            <p style="font-size: 0.85rem; color: #64748b; margin-top: 0; margin-bottom: 20px;">
                Pilih parameter di bawah ini untuk memulai input nilai. Sistem akan otomatis menentukan urutan Penilaian Harian (PH).
            </p>
            
            <form action="nilai_harian_input.php" method="GET">
                <div class="input-group" style="margin-bottom: 15px;">
                    <label>Pilih Kelas</label>
                    <select name="kelas_id" id="kelas_id" required onchange="loadMapel()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="input-group" style="margin-bottom: 15px;">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" required disabled onchange="loadEvaluasi()">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                    </select>
                </div>
                
                <div class="input-group" style="margin-bottom: 25px;">
                    <label>Jenis Evaluasi</label>
                    <select name="jenis_evaluasi" id="jenis_evaluasi" required disabled onchange="checkLanjut()">
                        <option value="">-- Pilih Jenis Evaluasi --</option>
                    </select>
                </div>
                
                <button type="submit" class="btn" id="btnLanjut" disabled style="width: 100%; padding: 14px; background: #0ea5e9; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer;">
                    Lanjutkan Input Nilai <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                </button>
            </form>
        </div>
        
        <!-- TAB RIWAYAT -->
        <div id="contentRiwayat" style="display: none;">
            <?php if(empty($historyGrouped)): ?>
                <div style="text-align: center; padding: 30px 10px;">
                    <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="color: #64748b; font-size: 0.9rem;">Belum ada riwayat nilai harian.</p>
                </div>
            <?php else: ?>
                <?php foreach($historyGrouped as $tingkat => $mapels): ?>
                    <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 10px; margin-top: 15px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">
                        <?= htmlspecialchars($tingkat) ?>
                    </div>
                    
                    <?php foreach($mapels as $mapel => $items): ?>
                        <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 10px; margin-top: 10px; display: flex; align-items: center; gap: 6px; text-transform: uppercase;">
                            <i class="fa-solid fa-book-open"></i> <?= htmlspecialchars($mapel) ?>
                        </div>
                        
                        <?php foreach($items as $h): ?>
                            <?php 
                                $kelas_id = $h['kelas_id'];
                                $total_siswa = count($siswaMap[$kelas_id] ?? []);
                                $ikut_siswa = $h['ikut_siswa'] ?? 0;
                                $persentase = $total_siswa > 0 ? round(($ikut_siswa / $total_siswa) * 100) : 0;
                                $color_percent = $persentase == 100 ? '#16a34a' : ($persentase >= 50 ? '#d97706' : '#ef4444');
                            ?>
                            <div class="history-card" style="display: block; padding: 0;">
                                <div class="accordion-header" onclick="toggleAccordion(this)">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem; margin-bottom: 4px;">
                                            <?= htmlspecialchars($h['jenis_evaluasi']) ?>
                                        </div>
                                        <?php if(!empty($h['judul_materi'])): ?>
                                        <div style="font-size: 0.8rem; color: #475569; margin-bottom: 4px; line-height: 1.3;">
                                            <?= htmlspecialchars($h['judul_materi']) ?>
                                        </div>
                                        <?php endif; ?>
                                        <div style="font-size: 0.75rem; font-weight: 600; color: <?= $color_percent ?>;">
                                            <?= $persentase ?>% Ikut (<?= $ikut_siswa ?> dari <?= $total_siswa ?> Siswa)
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-chevron-down accordion-icon" style="margin-top: 4px;"></i>
                                </div>
                                
                                <div class="accordion-body">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">
                                        <span>Nama Siswa</span>
                                        <span>Nilai</span>
                                    </div>
                                    
                                    <div class="hide-scrollbar" style="max-height: 250px; overflow-y: auto; margin-bottom: 15px; padding-right: 5px;">
                                        <?php if(isset($siswaMap[$kelas_id]) && !empty($siswaMap[$kelas_id])): ?>
                                            <?php foreach($siswaMap[$kelas_id] as $s): ?>
                                                <?php 
                                                    $nilai = $detailMap[$h['id']][$s['id']] ?? null; 
                                                    $nilai_color = ($nilai !== null && $nilai < 70) ? '#ef4444' : '#16a34a';
                                                ?>
                                                <div class="student-row">
                                                    <span style="color: #334155; font-weight: 500;"><?= htmlspecialchars($s['nama_lengkap']) ?></span>
                                                    <span style="font-weight: 700; color: <?= $nilai_color ?>;"><?= $nilai !== null ? $nilai : '-' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div style="text-align: center; color: #94a3b8; font-size: 0.8rem;">Data siswa tidak ditemukan</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="nilai_harian_input.php?edit_id=<?= $h['id'] ?>" style="flex: 1; text-align: center; padding: 8px; background: #f0f9ff; color: #0ea5e9; border: 1px solid #bae6fd; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                            <i class="fa-solid fa-pen"></i> Edit Nilai
                                        </a>
                                        <button onclick="confirmDelete(<?= $h['id'] ?>)" style="flex: 1; padding: 8px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tabInput').classList.remove('active');
    document.getElementById('tabRiwayat').classList.remove('active');
    document.getElementById('contentInput').style.display = 'none';
    document.getElementById('contentRiwayat').style.display = 'none';

    if(tab === 'input') {
        document.getElementById('tabInput').classList.add('active');
        document.getElementById('contentInput').style.display = 'block';
    } else {
        document.getElementById('tabRiwayat').classList.add('active');
        document.getElementById('contentRiwayat').style.display = 'block';
    }
}

function loadMapel() {
    const kelasId = document.getElementById('kelas_id').value;
    const mapelSelect = document.getElementById('mapel_id');
    const evalSelect = document.getElementById('jenis_evaluasi');
    const btnLanjut = document.getElementById('btnLanjut');
    
    mapelSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
    mapelSelect.disabled = true;
    evalSelect.innerHTML = '<option value="">-- Pilih Jenis Evaluasi --</option>';
    evalSelect.disabled = true;
    btnLanjut.disabled = true;
    
    if(!kelasId) return;
    
    mapelSelect.innerHTML = '<option value="">Memuat...</option>';
    
    // Gunakan api_get_jadwal.php atau buat api khusus. Kita asumsikan api_get_siswa_kelas.php bisa disesuaikan atau kita buat api khusus.
    // Karena kita tidak yakin ada api_get_jadwal.php, mari kita arahkan ke api_get_mapel_guru.php (kita akan buat ini)
    fetch('api_nilai_harian.php?action=mapel&kelas_id=' + kelasId)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">-- Pilih Mata Pelajaran --</option>';
            data.forEach(m => {
                html += `<option value="${m.id}">${m.nama_mapel}</option>`;
            });
            mapelSelect.innerHTML = html;
            mapelSelect.disabled = false;
        })
        .catch(err => {
            mapelSelect.innerHTML = '<option value="">Error memuat data</option>';
        });
}

function loadEvaluasi() {
    const kelasId = document.getElementById('kelas_id').value;
    const mapelId = document.getElementById('mapel_id').value;
    const evalSelect = document.getElementById('jenis_evaluasi');
    const btnLanjut = document.getElementById('btnLanjut');
    
    evalSelect.innerHTML = '<option value="">-- Pilih Jenis Evaluasi --</option>';
    evalSelect.disabled = true;
    btnLanjut.disabled = true;
    
    if(!mapelId) return;
    
    evalSelect.innerHTML = '<option value="">Memuat...</option>';
    
    fetch('api_nilai_harian.php?action=evaluasi&kelas_id=' + kelasId + '&mapel_id=' + mapelId)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">-- Pilih Jenis Evaluasi --</option>';
            data.forEach(e => {
                html += `<option value="${e.value}">${e.label}</option>`;
            });
            evalSelect.innerHTML = html;
            evalSelect.disabled = false;
        })
        .catch(err => {
            evalSelect.innerHTML = '<option value="">Error memuat data</option>';
        });
}

function checkLanjut() {
    const evalSelect = document.getElementById('jenis_evaluasi');
    const btnLanjut = document.getElementById('btnLanjut');
    btnLanjut.disabled = !evalSelect.value;
}

function confirmDelete(id) {
    if(confirm('Apakah Anda yakin ingin menghapus riwayat nilai harian ini beserta seluruh nilai siswanya?')) {
        window.location.href = 'nilai_harian_delete.php?id=' + id;
    }
}
function toggleAccordion(headerEl) {
    const bodyEl = headerEl.nextElementSibling;
    const iconEl = headerEl.querySelector('.accordion-icon');
    
    if (bodyEl.style.display === 'block') {
        bodyEl.style.display = 'none';
        iconEl.classList.remove('open');
    } else {
        bodyEl.style.display = 'block';
        iconEl.classList.add('open');
    }
}
</script>

<?php include 'footer_guru.php'; ?>
