<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_ekstra = $_GET['ekstra'] ?? '';
$tahun_ajaran = $_GET['ta'] ?? '';

if (empty($nama_ekstra) || empty($tahun_ajaran)) {
    echo "<script>alert('Data tidak valid.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

// Cek hak akses pembina
$stmt = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = :ekstra AND tahun_ajaran = :ta");
$stmt->execute(['gid' => $guru_id, 'ekstra' => $nama_ekstra, 'ta' => $tahun_ajaran]);
if (!$stmt->fetch()) {
    echo "<script>alert('Akses ditolak.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

$db_siakad = $siakad_db;

// Ambil semester aktif
$semester = '1';
try {
    $stmt_peng = $db_siakad->query("SELECT semester_aktif FROM pengaturan LIMIT 1");
    $peng = $stmt_peng->fetch(PDO::FETCH_ASSOC);
    if ($peng) {
        $sem_name = strtolower(trim($peng['semester_aktif']));
        $semester = ($sem_name === 'genap' || $sem_name === '2') ? '2' : '1';
    }
} catch(Exception $e) {}

// Pastikan tabel ekstra_nilai ada
try {
    $db_siakad->exec("
        CREATE TABLE IF NOT EXISTS `ekstra_nilai` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nama_ekstra` varchar(150) NOT NULL,
            `tahun_ajaran` varchar(20) NOT NULL,
            `semester` varchar(10) NOT NULL,
            `siswa_id` int(11) NOT NULL,
            `predikat` varchar(2) DEFAULT NULL,
            `deskripsi` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_nilai_ekstra` (`nama_ekstra`,`tahun_ajaran`,`semester`,`siswa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch(Exception $e) {}

// Save Handle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $predikats = $_POST['predikat'] ?? [];
    $deskripsis = $_POST['deskripsi'] ?? [];
    
    $db_siakad->beginTransaction();
    try {
        $ins = $db_siakad->prepare("
            INSERT INTO ekstra_nilai (nama_ekstra, tahun_ajaran, semester, siswa_id, predikat, deskripsi)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE predikat = VALUES(predikat), deskripsi = VALUES(deskripsi)
        ");
        
        foreach ($predikats as $s_id => $pred) {
            $desc = $deskripsis[$s_id] ?? '';
            // Hanya simpan jika ada nilainya
            if (!empty($pred)) {
                $ins->execute([$nama_ekstra, $tahun_ajaran, $semester, $s_id, $pred, $desc]);
            } else {
                // Hapus jika predikat dikosongkan
                $del = $db_siakad->prepare("DELETE FROM ekstra_nilai WHERE nama_ekstra=? AND tahun_ajaran=? AND semester=? AND siswa_id=?");
                $del->execute([$nama_ekstra, $tahun_ajaran, $semester, $s_id]);
            }
        }
        $db_siakad->commit();
        $msg = "Nilai berhasil disimpan!";
    } catch (\Exception $e) {
        $db_siakad->rollBack();
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Load Data
// Ambil anggota beserta nilai yang sudah tersimpan
$anggotaList = $db_siakad->query("SELECT ea.siswa_id, s.nama, k.nama_kelas, n.predikat, n.deskripsi
                                FROM ekstra_anggota ea 
                                JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                LEFT JOIN ekstra_nilai n ON (ea.siswa_id = n.siswa_id AND n.nama_ekstra = '{$nama_ekstra}' AND n.tahun_ajaran = '{$tahun_ajaran}' AND n.semester = '{$semester}')
                                WHERE ea.nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                AND ea.tahun_ajaran = ".$db_siakad->quote($tahun_ajaran)."
                                ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Nilai Ekstra';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Penilaian Semester <?= $semester == '1' ? 'Ganjil' : 'Genap' ?></p>
            <h1><?= htmlspecialchars($nama_ekstra) ?></h1>
        </div>
        <a href="ekstra_pembina.php" style="color: white; font-size: 1.5rem; text-decoration: none;">
            <i class="fa-solid fa-circle-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    <?php if (isset($msg)): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #bbf7d0; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #fecaca; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="data-card" style="margin-top: <?= isset($msg) || isset($error) ? '0' : '-2.5rem' ?>; z-index: 10; margin-bottom: 30px;">
        <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-star" style="color: #10b981; margin-right: 5px;"></i> Form Penilaian Anggota</h3>
        
        <?php if (empty($anggotaList)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b; font-size: 0.85rem;">
                Belum ada anggota. Silakan tambah anggota terlebih dahulu dari menu Anggota.
            </div>
        <?php else: ?>
            <form method="POST">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($anggotaList as $idx => $anggota): 
                        $s_id = $anggota['siswa_id'];
                    ?>
                        <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px;">
                            <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;"><?= $idx + 1 ?>. <?= htmlspecialchars($anggota['nama']) ?></div>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px; margin-bottom: 8px;"><i class="fa-solid fa-door-open" style="margin-right: 3px;"></i> Kelas <?= htmlspecialchars($anggota['nama_kelas']) ?></div>
                            
                            <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                                <div style="flex: 1;">
                                    <label style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 3px; display: block;">Predikat:</label>
                                    <select name="predikat[<?= $s_id ?>]" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: #f8fafc; font-weight: 600;">
                                        <option value="">- Belum Dinilai -</option>
                                        <option value="A" <?= ($anggota['predikat'] == 'A') ? 'selected' : '' ?>>A (Sangat Baik)</option>
                                        <option value="B" <?= ($anggota['predikat'] == 'B') ? 'selected' : '' ?>>B (Baik)</option>
                                        <option value="C" <?= ($anggota['predikat'] == 'C') ? 'selected' : '' ?>>C (Cukup)</option>
                                        <option value="D" <?= ($anggota['predikat'] == 'D') ? 'selected' : '' ?>>D (Kurang)</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 3px; display: block;">Deskripsi / Catatan:</label>
                                <textarea name="deskripsi[<?= $s_id ?>]" rows="2" placeholder="Contoh: Sangat aktif dan disiplin..." style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem; font-family: inherit; resize: vertical;"><?= htmlspecialchars($anggota['deskripsi'] ?? '') ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="ekstra_pembina.php" style="flex: 1; text-align: center; padding: 12px; background: #f1f5f9; color: #475569; border-radius: 8px; font-weight: 700; text-decoration: none;">Kembali</a>
                    <button type="submit" style="flex: 2; border: none; background: #10b981; color: white; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Penilaian
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
