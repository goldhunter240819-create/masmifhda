<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Ambil tahun ajaran aktif
$institusi = [];
try {
    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $institusi = $stmt_inst->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
$semester = $institusi['semester'] == 'Genap' ? 2 : 1;

// Mode Edit atau Insert
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
$mapel_id = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
$jenis_evaluasi = isset($_GET['jenis_evaluasi']) ? $_GET['jenis_evaluasi'] : '';

$materi = '';
$existingNilai = [];

if ($edit_id > 0) {
    // Mode Edit
    $stmt = $core_db->prepare("SELECT * FROM nilai_harian WHERE id = ? AND guru_id = ?");
    $stmt->execute([$edit_id, $guru_id]);
    $header = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$header) {
        $_SESSION['msg'] = "Data tidak ditemukan atau Anda tidak memiliki akses.";
        header("Location: nilai_harian.php");
        exit;
    }
    
    $kelas_id = $header['kelas_id'];
    $mapel_id = $header['mapel_id'];
    $jenis_evaluasi = $header['jenis_evaluasi'];
    $materi = $header['judul_materi'];
    
    // Ambil detail nilai
    $stmt_d = $core_db->prepare("SELECT siswa_id, nilai FROM nilai_harian_detail WHERE nilai_harian_id = ?");
    $stmt_d->execute([$edit_id]);
    $details = $stmt_d->fetchAll(PDO::FETCH_ASSOC);
    foreach ($details as $d) {
        $existingNilai[$d['siswa_id']] = $d['nilai'];
    }
} else {
    // Mode Insert Baru, pastikan parameter lengkap
    if ($kelas_id <= 0 || $mapel_id <= 0 || empty($jenis_evaluasi)) {
        header("Location: nilai_harian.php");
        exit;
    }
}

// Info Kelas & Mapel
$nama_kelas = '';
$nama_mapel = '';
try {
    $stmt_k = $siakad_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
    $stmt_k->execute([$kelas_id]);
    $nama_kelas = $stmt_k->fetchColumn() ?: 'Unknown';
    
    $stmt_m = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
    $stmt_m->execute([$mapel_id]);
    $nama_mapel = $stmt_m->fetchColumn() ?: 'Unknown';
} catch (Exception $e) {}

// Daftar Siswa
$siswaList = [];
try {
    $stmt_s = $siakad_db->prepare("SELECT id, nis, nama_lengkap as nama FROM siswa WHERE kelas_id = ? AND status = 'aktif' ORDER BY nama ASC");
    $stmt_s->execute([$kelas_id]);
    $siswaList = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$jumlah_siswa = count($siswaList);

// PROSES SIMPAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_materi = trim($_POST['materi'] ?? '');
    $post_nilai = $_POST['nilai'] ?? [];
    
    if (!empty($post_materi)) {
        try {
            $core_db->beginTransaction();
            
            $tanggal = date('Y-m-d');
            
            if ($edit_id > 0) {
                // UPDATE HEADER
                $stmt_upd = $core_db->prepare("UPDATE nilai_harian SET judul_materi = ?, tanggal = ? WHERE id = ?");
                $stmt_upd->execute([$post_materi, $tanggal, $edit_id]);
                
                // HAPUS DETAIL LAMA
                $stmt_del = $core_db->prepare("DELETE FROM nilai_harian_detail WHERE nilai_harian_id = ?");
                $stmt_del->execute([$edit_id]);
                
                $nh_id = $edit_id;
            } else {
                // INSERT HEADER BARU
                $stmt_ins = $core_db->prepare("
                    INSERT INTO nilai_harian (guru_id, kelas_id, mapel_id, tahun_ajaran_id, semester, jenis_evaluasi, judul_materi, tanggal)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt_ins->execute([$guru_id, $kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi, $post_materi, $tanggal]);
                $nh_id = $core_db->lastInsertId();
            }
            
            // INSERT DETAIL BARU
            if (!empty($post_nilai) && is_array($post_nilai)) {
                $stmt_dtl = $core_db->prepare("INSERT INTO nilai_harian_detail (nilai_harian_id, siswa_id, nilai) VALUES (?, ?, ?)");
                foreach ($post_nilai as $s_id => $val) {
                    $v = trim($val);
                    if ($v !== '') { // hanya simpan yang diisi
                        $stmt_dtl->execute([$nh_id, $s_id, floatval($v)]);
                    }
                }
            }
            
            $core_db->commit();
            $_SESSION['msg'] = 'Berhasil ' . ($edit_id > 0 ? 'memperbarui' : 'menyimpan') . ' nilai harian!';
            header("Location: nilai_harian.php");
            exit;
        } catch (Exception $e) {
            $core_db->rollBack();
            $error_msg = "Gagal menyimpan: " . $e->getMessage();
        }
    } else {
        $error_msg = "Materi / Topik pembelajaran wajib diisi.";
    }
}

$page_title = 'Input Nilai ' . htmlspecialchars($jenis_evaluasi);
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <a href="nilai_harian.php" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 50%;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="margin-left: 15px;">
            <p style="text-transform: uppercase;">INPUT NILai</p>
            <h1><?= htmlspecialchars($jenis_evaluasi) ?></h1>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($error_msg)): ?>
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: #b91c1c; font-size: 0.9rem; font-weight: 600;"><?= htmlspecialchars($error_msg) ?></p>
        </div>
    <?php endif; ?>

    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 20px;">
        <!-- Banner Info Mapel -->
        <div style="background: #0f172a; border-radius: 16px; padding: 20px; color: white; margin-bottom: 25px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <p style="margin: 0 0 5px 0; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">MATA PELAJARAN</p>
            <h2 style="margin: 0 0 15px 0; font-size: 1.4rem; font-weight: 800; color: white;"><?= htmlspecialchars($nama_mapel) ?></h2>
            
            <div style="display: flex; gap: 20px; font-size: 0.85rem;">
                <div>
                    <div style="color: #64748b; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; margin-bottom: 2px;">Kelas</div>
                    <div style="font-weight: 700;"><?= htmlspecialchars($nama_kelas) ?></div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; margin-bottom: 2px;">Evaluasi</div>
                    <div style="font-weight: 700;"><?= htmlspecialchars($jenis_evaluasi) ?></div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; margin-bottom: 2px;">Jumlah</div>
                    <div style="font-weight: 700;"><?= $jumlah_siswa ?> Siswa</div>
                </div>
            </div>
        </div>

        <form action="" method="POST">
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Materi / Topik Pembelajaran <span style="color: #ef4444;">(Wajib)</span></label>
                <textarea name="materi" rows="2" required placeholder="Contoh: Bab 1 - Bilangan Bulat..." style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 0.95rem; resize: vertical; box-sizing: border-box; outline: none; transition: border-color 0.2s;"><?= htmlspecialchars($materi) ?></textarea>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px;">
                <?php if(empty($siswaList)): ?>
                    <div style="text-align: center; padding: 20px; color: #64748b; font-size: 0.9rem; border: 1px dashed #cbd5e1; border-radius: 12px;">Tidak ada data siswa aktif di kelas ini.</div>
                <?php else: ?>
                    <?php foreach($siswaList as $s): ?>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 15px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="flex: 1; padding-right: 15px;">
                                <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 3px;"><?= htmlspecialchars($s['nama']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b; font-family: monospace;">NIS: <?= htmlspecialchars($s['nis'] ?: '-') ?></div>
                            </div>
                            <div style="width: 70px;">
                                <?php $val = isset($existingNilai[$s['id']]) ? floatval($existingNilai[$s['id']]) : ''; ?>
                                <input type="number" step="0.01" min="0" max="100" name="nilai[<?= $s['id'] ?>]" value="<?= $val ?>" placeholder="-" style="width: 100%; padding: 10px 5px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 1.1rem; font-weight: 800; text-align: center; color: #0f172a; box-sizing: border-box; outline: none;" onfocus="this.style.borderColor='#0ea5e9'" onblur="this.style.borderColor='#e2e8f0'">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if(!empty($siswaList)): ?>
                <button type="submit" style="width: 100%; padding: 16px; background: #0ea5e9; color: white; border: none; border-radius: 12px; font-size: 1.05rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25); margin-bottom: 20px;">
                    <i class="fa-solid fa-save"></i> <?= $edit_id > 0 ? 'Simpan Perubahan' : 'Simpan Nilai' ?>
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
