<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil kelas_id siswa saat ini
$stmt_s = $core_db->prepare("SELECT kelas_id FROM siswa WHERE id = ?");
$stmt_s->execute([$siswa_id]);
$siswa = $stmt_s->fetch(PDO::FETCH_ASSOC);
$kelas_id = $siswa['kelas_id'];

$hari_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tanggal_filter)) {
    $tanggal_filter = date('Y-m-d');
}
$hari_ini = $hari_indo[date('w', strtotime($tanggal_filter))];

$is_daring = false;
$kalender_ket = '';
try {
    $stmt_cal = $siakad_db->prepare("SELECT tipe, keterangan FROM kalender_akademik WHERE :tgl BETWEEN tanggal_mulai AND IF(tanggal_selesai IS NULL OR tanggal_selesai = '0000-00-00', tanggal_mulai, tanggal_selesai) LIMIT 1");
    $stmt_cal->execute([':tgl' => $tanggal_filter]);
    $cal = $stmt_cal->fetch(PDO::FETCH_ASSOC);
    if ($cal) {
        if ($cal['tipe'] == 'daring') {
            $is_daring = true;
        } else {
            $kalender_ket = $cal['keterangan'];
        }
    }
} catch (Exception $e) {}

$jadwal = [];
if (!empty($kelas_id) && $is_daring) {
    try {
        $core_db_name = $core_db->query("SELECT database()")->fetchColumn();
        
        $activeTahunId = 1;
        $activeSemester = 1;
        $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        if ($thn = $stmtTahun->fetch()) {
            $activeTahunId = $thn['id'];
            $activeSemester = $thn['semester'];
        }

        $query = "
            SELECT j.id as jadwal_id, j.jam_mulai, j.jam_selesai, j.mata_pelajaran_id as mapel_id, 
                   m.nama_mapel, g.nama as nama_guru,
                   td.id as tugas_id, td.judul, td.instruksi, td.batas_waktu
            FROM jadwal_pelajaran j 
            LEFT JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
            LEFT JOIN {$core_db_name}.guru g ON j.guru_id = g.id 
            LEFT JOIN tugas_daring td ON td.guru_id = j.guru_id 
                                      AND td.kelas_id = j.kelas_id 
                                      AND td.mata_pelajaran_id = j.mata_pelajaran_id 
                                      AND td.tanggal = :tanggal
            WHERE j.hari = :hari AND j.tahun_ajaran_id = :tahun_ajaran_id AND j.semester = :semester
            AND j.kelas_id = :kelas_id
            ORDER BY j.jam_mulai ASC
        ";
        
        $params = [
            ':hari' => $hari_ini, 
            ':tanggal' => $tanggal_filter,
            ':tahun_ajaran_id' => $activeTahunId,
            ':semester' => $activeSemester,
            ':kelas_id' => $kelas_id
        ];
        
        $stmt = $siakad_db->prepare($query);
        $stmt->execute($params);
        $raw_jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($raw_jadwal as $jdwl) {
            $group_key = $jdwl['mapel_id'] . '_' . $jdwl['nama_guru'];
            if (!isset($grouped[$group_key])) {
                $grouped[$group_key] = $jdwl;
            } else {
                if (strtotime($jdwl['jam_selesai']) > strtotime($grouped[$group_key]['jam_selesai'])) {
                    $grouped[$group_key]['jam_selesai'] = $jdwl['jam_selesai'];
                }
            }
        }
        $jadwal = array_values($grouped);
    } catch (Exception $e) {}
}

// Helper parsing URL
function parseUrlsInText($text) {
    // Escape HTML first
    $text = htmlspecialchars($text);
    // Find URLs and convert to links/buttons
    $pattern = '/(https?:\/\/[^\s]+)/';
    $replacement = '<div style="margin: 15px 0;"><a href="$1" target="_blank" style="background: #3b82f6; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(59,130,246,0.3);"><i class="fa-solid fa-link"></i> Buka Tautan</a></div>';
    return preg_replace($pattern, $replacement, $text);
}

$page_title = 'Tugas Daring / Mandiri';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
    <div class="header-top">
        <a href="profil.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting">
            <p>Daftar Penugasan</p>
            <h1>Tugas Daring</h1>
        </div>
        <div class="avatar" style="color: #ef4444;">
            <i class="fa-solid fa-laptop-file"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title">Tugas untuk Kelas Anda</h3>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Daftar tugas yang diberikan oleh bapak/ibu guru untuk dikerjakan dari rumah.</p>
        
        <div style="margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>" onchange="this.form.submit()" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #334155; background: white; outline: none; cursor: pointer;">
                <div style="background: #ef4444; color: white; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;">
                    <i class="fa-regular fa-calendar"></i>
                </div>
            </form>
        </div>

        <?php if (!$is_daring): ?>
            <div style="text-align: center; padding: 40px 20px; color: #94a3b8; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc;">
                <i class="fa-solid fa-users-viewfinder" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <p style="margin: 0; font-size: 0.95rem; font-weight: 600; color: #64748b;">Bukan Hari Pembelajaran Daring (PJJ)</p>
                <p style="margin: 5px 0 0 0; font-size: 0.8rem;">
                    <?= $kalender_ket ? htmlspecialchars($kalender_ket) : 'Pembelajaran dilaksanakan secara tatap muka di sekolah.' ?>
                </p>
            </div>
        <?php elseif (empty($jadwal)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #94a3b8; border: 2px dashed #cbd5e1; border-radius: 12px;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <p style="margin: 0; font-size: 0.9rem;">Tidak ada jadwal pelajaran pada hari ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($jadwal as $j): ?>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); position: relative; overflow: hidden;">
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: <?= $j['tugas_id'] ? '#10b981' : '#cbd5e1' ?>;"></div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 3px;"><?= htmlspecialchars($j['nama_guru']) ?></div>
                            <div style="font-size: 1.05rem; font-weight: bold; color: #1e293b;"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; color: white; background: #64748b; padding: 3px 8px; border-radius: 20px; font-weight: 600; display: inline-block;">
                                <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($j['tugas_id']): ?>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px; margin-top: 10px;">
                            <div style="font-size: 0.9rem; font-weight: bold; color: #166534; margin-bottom: 5px;"><?= htmlspecialchars($j['judul']) ?></div>
                            
                            <?php if (!empty($j['batas_waktu'])): ?>
                                <div style="font-size: 0.75rem; color: #15803d; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                                    <i class="fa-regular fa-clock"></i> Batas Kumpul: <?= date('d M Y', strtotime($j['batas_waktu'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button onclick="toggleInstruksi(<?= $j['tugas_id'] ?>)" style="margin-top: 10px; background: #10b981; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; width: 100%; justify-content: center; transition: all 0.2s;">
                            <i class="fa-solid fa-eye"></i> Lihat Instruksi & Materi
                        </button>

                        <div id="instruksi-<?= $j['tugas_id'] ?>" style="display: none; font-size: 0.85rem; color: #334155; line-height: 1.6; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-top: 10px;">
                            <?= parseUrlsInText($j['instruksi']) ?>
                        </div>
                    <?php else: ?>
                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 10px; margin-top: 10px; text-align: center;">
                            <div style="font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <i class="fa-solid fa-circle-info"></i> Guru belum memberikan instruksi/materi
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script>
function toggleInstruksi(id) {
    var el = document.getElementById('instruksi-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>

<?php include 'footer.php'; ?>
