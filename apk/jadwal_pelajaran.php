<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil data siswa
$stmt = $core_db->prepare("SELECT kelas_id FROM siswa WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa || empty($siswa['kelas_id'])) {
    // Siswa belum punya kelas
    $jadwal_per_hari = [];
} else {
    // Ambil tahun ajaran aktif
    $ta_id = 1;
    $sem_num = 1;
    $stmt_peng = $siakad_db->query("SELECT tahun_ajaran_aktif, semester_aktif FROM pengaturan LIMIT 1");
    $peng = $stmt_peng->fetch(PDO::FETCH_ASSOC);
    if ($peng) {
        $ta_name = $peng['tahun_ajaran_aktif'];
        $sem_name = strtolower(trim($peng['semester_aktif']));
        $sem_num = ($sem_name === 'genap' || $sem_name === '2') ? 2 : 1;
        
        $stmt_ta = $core_db->prepare("SELECT id FROM tahun_ajaran WHERE name = ? AND semester = ? LIMIT 1");
        $stmt_ta->execute([$ta_name, $sem_num]);
        $ta_row = $stmt_ta->fetch(PDO::FETCH_ASSOC);
        if ($ta_row) {
            $ta_id = $ta_row['id'];
        }
    }

    $stmt_jadwal = $siakad_db->prepare("
        SELECT jp.*, m.nama_mapel, g.nama as nama_guru
        FROM jadwal_pelajaran jp
        JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
        JOIN aula_db.guru g ON jp.guru_id = g.id
        WHERE jp.kelas_id = :kelas_id AND jp.tahun_ajaran_id = :ta_id AND jp.semester = :semester
        ORDER BY jp.jam_mulai ASC
    ");
    $stmt_jadwal->execute([
        'kelas_id' => $siswa['kelas_id'],
        'ta_id' => $ta_id,
        'semester' => (string)$sem_num
    ]);
    $semua_jadwal = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

    // Grouping by hari
    $jadwal_per_hari = [
        'Senin' => [],
        'Selasa' => [],
        'Rabu' => [],
        'Kamis' => [],
        'Jumat' => [],
        'Sabtu' => [],
        'Minggu' => []
    ];

    foreach ($semua_jadwal as $j) {
        $hari = ucfirst(strtolower($j['hari']));
        if (isset($jadwal_per_hari[$hari])) {
            // Gabungkan jika mapel dan guru sama berurutan
            $count = count($jadwal_per_hari[$hari]);
            if ($count > 0) {
                $last_index = $count - 1;
                $last_j = $jadwal_per_hari[$hari][$last_index];
                if ($last_j['mata_pelajaran_id'] == $j['mata_pelajaran_id'] && $last_j['guru_id'] == $j['guru_id']) {
                    // Update jam selesainya saja
                    $jadwal_per_hari[$hari][$last_index]['jam_selesai'] = $j['jam_selesai'];
                    continue;
                }
            }
            $jadwal_per_hari[$hari][] = $j;
        }
    }
}

$page_title = 'Jadwal Pelajaran';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="header-top">
        <a href="index.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting">
            <p>Agenda Kelas</p>
            <h1>Jadwal Pelajaran</h1>
        </div>
        <div class="avatar" style="color: #10b981;">
            <i class="fa-solid fa-calendar-alt"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title">Jadwal Sepekan</h3>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Berikut adalah jadwal pelajaran Anda untuk pekan ini.</p>

        <?php 
        $ada_jadwal = false;
        foreach ($jadwal_per_hari as $hari => $jadwals): 
            if (empty($jadwals)) continue;
            $ada_jadwal = true;
            
            // Highlight hari ini
            $hari_ini_str = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')];
            $is_today = ($hari === $hari_ini_str);
        ?>
            <div style="margin-bottom: 25px;">
                <h4 style="margin-top:0; margin-bottom: 10px; font-size: 1rem; color: <?= $is_today ? '#10b981' : '#3b82f6' ?>; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid <?= $is_today ? 'fa-calendar-day' : 'fa-calendar' ?>"></i>
                    <?= $hari ?>
                    <?php if($is_today): ?>
                        <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.65rem; font-weight: bold; margin-left: auto;">Hari Ini</span>
                    <?php endif; ?>
                </h4>
                
                <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); <?= $is_today ? 'border: 2px solid #a7f3d0;' : 'border: 1px solid #e2e8f0;' ?>">
                    <?php foreach ($jadwals as $j): ?>
                        <div style="border-bottom: 1px solid #f1f5f9; padding: 12px 0; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 5px;">
                                    <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                                </div>
                            </div>
                            <div style="background: #f8fafc; color: #475569; font-size: 0.75rem; font-weight: 600; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: right; max-width: 45%;">
                                <i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($j['nama_guru']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$ada_jadwal): ?>
            <div style="text-align: center; padding: 40px 20px; color: #94a3b8; border: 2px dashed #cbd5e1; border-radius: 12px;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <p style="margin: 0; font-size: 0.95rem; font-weight: 600; color: #475569;">Belum Ada Jadwal</p>
                <p style="margin: 5px 0 0 0; font-size: 0.8rem;">Jadwal pelajaran untuk kelas Anda belum tersedia atau semester belum diatur.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
