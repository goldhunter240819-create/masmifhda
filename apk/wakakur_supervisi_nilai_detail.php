<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
if (!$guru_id) {
    header("Location: wakakur_supervisi_nilai.php");
    exit;
}

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
$inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
$tahun_ajaran_id = $inst['tahun_ajaran_id'] ?? 1;
$semester = $inst['semester'] ?? 'Ganjil';

// Get guru info
$stmtGuru = $core_db->prepare("SELECT id, nama, nip, foto FROM guru WHERE id = ?");
$stmtGuru->execute([$guru_id]);
$guru = $stmtGuru->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    header("Location: wakakur_supervisi_nilai.php");
    exit;
}

$foto_url = !empty($guru['foto']) ? (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $guru['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($guru['nama']) . '&background=f1f5f9&color=475569';

// Get schedules for this teacher
$query = "SELECT DISTINCT j.mata_pelajaran_id as mapel_id, j.kelas_id, m.nama_mapel, k.nama_kelas 
          FROM aula_siakad_db.jadwal_pelajaran j 
          JOIN mapel m ON m.id = j.mata_pelajaran_id 
          JOIN kelas k ON k.id = j.kelas_id
          WHERE j.guru_id = ?";
$stmt = $core_db->prepare($query);
$stmt->execute([$guru_id]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students per class
$kelas_ids = array_unique(array_column($schedules, 'kelas_id'));
$siswa_counts = [];
if (!empty($kelas_ids)) {
    $in = str_repeat('?,', count($kelas_ids) - 1) . '?';
    $stmt_siswa = $core_db->prepare("SELECT kelas_id, COUNT(*) as total FROM riwayat_kelas_siswa WHERE kelas_id IN ($in) GROUP BY kelas_id");
    $stmt_siswa->execute($kelas_ids);
    $siswa_counts = $stmt_siswa->fetchAll(PDO::FETCH_KEY_PAIR);
}

// Get all evaluations
$stmt_nilai = $core_db->prepare("SELECT n.mapel_id, n.kelas_id, n.jenis_evaluasi, COUNT(DISTINCT d.siswa_id) as total_dinilai, MAX(n.judul_materi) as materi
    FROM nilai_harian n
    JOIN nilai_harian_detail d ON d.nilai_harian_id = n.id
    WHERE n.tahun_ajaran_id = ? AND n.semester = ? AND n.guru_id = ? AND d.nilai IS NOT NULL
    GROUP BY n.mapel_id, n.kelas_id, n.jenis_evaluasi");
$stmt_nilai->execute([$tahun_ajaran_id, $semester, $guru_id]);
$nilais = $stmt_nilai->fetchAll(PDO::FETCH_ASSOC);

$eval_grouped = [];
foreach ($nilais as $n) {
    $key = $n['kelas_id'] . '_' . $n['mapel_id'];
    $eval_grouped[$key][] = $n;
}

// Get existing supervision status
$stmtSup = $core_db->prepare("SELECT kelas_id, mapel_id, jenis_evaluasi, ada_kisi, ada_analisis, ada_remedial FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ? AND guru_id = ?");
$stmtSup->execute([$tahun_ajaran_id, $semester, $guru_id]);
$sups = $stmtSup->fetchAll(PDO::FETCH_ASSOC);

$sup_grouped = [];
foreach ($sups as $s) {
    $key = $s['kelas_id'] . '_' . $s['mapel_id'] . '_' . $s['jenis_evaluasi'];
    $sup_grouped[$key] = $s;
}

$page_title = 'Detail Evaluasi';
include 'header.php';
?>
<div class="header" style="background: linear-gradient(135deg, #c026d3 0%, #86198f 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Jadwal & Evaluasi</p>
            <h1 style="font-size: 1.1rem;">Supervisi Nilai</h1>
        </div>
        <div class="avatar" style="color: #c026d3; background: white;" onclick="window.location.href='wakakur_supervisi_nilai.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        <div>
            <div style="font-weight: 800; color: #1e293b;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size: 0.8rem; color: #64748b;">NIP: <?= empty($guru['nip']) ? '-' : $guru['nip'] ?></div>
        </div>
    </div>

    <?php if (empty($schedules)): ?>
    <div style="text-align: center; padding: 30px 20px; color: #94a3b8; background: white; border-radius: 12px;">
        <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
        <p style="margin: 0;">Tidak ada jadwal mengajar ditemukan.</p>
    </div>
    <?php endif; ?>

    <?php foreach($schedules as $s): 
        $key = $s['kelas_id'] . '_' . $s['mapel_id'];
        $evals = $eval_grouped[$key] ?? [];
        $total_siswa = $siswa_counts[$s['kelas_id']] ?? 0;
    ?>
    <div class="data-card" style="background: white; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden;">
        <div style="padding: 15px; background: #fdf4ff; border-bottom: 1px solid #fce7f3; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 800; color: #86198f; font-size: 0.95rem;"><?= htmlspecialchars($s['nama_mapel']) ?></div>
                <div style="font-size: 0.8rem; color: #a21caf; font-weight: 600; margin-top: 2px;">Kelas <?= htmlspecialchars($s['nama_kelas']) ?> &bull; <?= $total_siswa ?> Siswa</div>
            </div>
        </div>
        
        <div style="padding: 15px;">
            <?php if (empty($evals)): ?>
                <div style="font-size: 0.8rem; color: #94a3b8; font-style: italic; text-align: center; padding: 10px 0;">Belum ada nilai diinput.</div>
            <?php else: ?>
                <?php foreach($evals as $ev): 
                    $sup_key = $s['kelas_id'] . '_' . $s['mapel_id'] . '_' . $ev['jenis_evaluasi'];
                    $sup = $sup_grouped[$sup_key] ?? null;
                    
                    $pct = $total_siswa > 0 ? round(($ev['total_dinilai'] / $total_siswa) * 100) : 0;
                    $color = $pct >= 100 ? '#10b981' : ($pct > 0 ? '#f59e0b' : '#ef4444');
                    
                    $is_supervised = !empty($sup);
                    $all_checked = $is_supervised && $sup['ada_kisi'] && $sup['ada_analisis'] && $sup['ada_remedial'];
                    $sup_color = $all_checked ? '#10b981' : ($is_supervised ? '#f59e0b' : '#94a3b8');
                ?>
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 0.85rem; color: #334155;"><?= htmlspecialchars($ev['jenis_evaluasi']) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Materi: <?= htmlspecialchars($ev['materi']) ?></div>
                        </div>
                        <div style="text-align: right; margin-left: 10px;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: <?= $color ?>;"><?= $ev['total_dinilai'] ?>/<?= $total_siswa ?></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                        <div style="flex: 1; max-width: 120px; background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; width: <?= $pct ?>%; background: <?= $color ?>;"></div>
                        </div>
                        <button type="button" onclick="window.location.href='wakakur_supervisi_nilai_form.php?guru_id=<?= $guru_id ?>&kelas_id=<?= $s['kelas_id'] ?>&mapel_id=<?= $s['mapel_id'] ?>&jenis=<?= urlencode($ev['jenis_evaluasi']) ?>'" style="background: transparent; border: 1px solid <?= $sup_color ?>; color: <?= $sup_color ?>; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                            <i class="fa-solid <?= $is_supervised ? 'fa-check-double' : 'fa-clipboard-check' ?>"></i>
                            <?= $is_supervised ? 'Telah Disupervisi' : 'Supervisi' ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'footer_guru.php'; ?>
