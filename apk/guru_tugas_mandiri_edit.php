<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$tugas_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['update_tugas'])) {
    $id_kelas = $_POST['id_kelas'];
    $id_mapel = $_POST['id_mapel'];
    $tanggal = $_POST['tanggal'];
    $judul = $_POST['judul'];
    $instruksi = $_POST['instruksi'];
    $batas_waktu = !empty($_POST['batas_waktu']) ? $_POST['batas_waktu'] : null;

    try {
        $stmt_up = $siakad_db->prepare("UPDATE tugas_daring SET kelas_id=?, mata_pelajaran_id=?, tanggal=?, batas_waktu=?, judul=?, instruksi=? WHERE id=? AND guru_id=?");
        $stmt_up->execute([$id_kelas, $id_mapel, $tanggal, $batas_waktu, $judul, $instruksi, $tugas_id, $guru_id]);
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#f3f6f9; font-family:sans-serif;'>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Mantap!',
                    text: 'Tugas berhasil diperbarui.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    backdrop: `rgba(0,0,0,0.4)`
                }).then(() => {
                    window.location.href = 'guru_tugas_mandiri.php';
                });
            });
        </script></body></html>";
        exit;
    } catch (Exception $e) {
        $error_msg = "Gagal memperbarui tugas: " . $e->getMessage();
    }
}

// Ambil data tugas
try {
    $stmt_tugas = $siakad_db->prepare("SELECT * FROM tugas_daring WHERE id = ? AND guru_id = ?");
    $stmt_tugas->execute([$tugas_id, $guru_id]);
    $tugas_data = $stmt_tugas->fetch(PDO::FETCH_ASSOC);
    if (!$tugas_data) {
        die("Data tugas tidak ditemukan atau Anda tidak memiliki akses.");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Ambil tahun ajaran aktif
$activeTahunId = 1;
$activeSemester = 1;
try {
    $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
        $activeSemester = $thn['semester'];
    }
} catch (Exception $e) {}

// Ambil data Kelas yang diajar
$kelas_list = [];
$mapel_list = [];
try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas, k.tingkat
        FROM (
            SELECT kelas_id FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT kelas_id FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN aula_db.kelas k ON k.id = combined.kelas_id
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $kelas_list = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("
        SELECT DISTINCT mp.id, mp.nama_mapel
        FROM (
            SELECT mapel_id as mapel FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN mata_pelajaran mp ON mp.id = combined.mapel
        ORDER BY mp.nama_mapel
    ");
    $stmt_m->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $mapel_list = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Edit Tugas Daring';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
    <div class="header-top">
        <a href="guru_tugas_mandiri.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting">
            <p>Penugasan Siswa</p>
            <h1>Edit Tugas</h1>
        </div>
        <div class="avatar" style="color: #ef4444;">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title">Edit Detail Tugas</h3>
        
        <?php if (isset($success_msg)): ?>
            <div style="background: #ecfdf5; color: #10b981; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #a7f3d0;">
                <i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> <?= $success_msg ?>
                <div style="margin-top: 8px;">
                    <a href="guru_tugas_mandiri.php" style="color: #059669; font-weight: bold; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tugas</a>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div style="background: #fef2f2; color: #ef4444; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 5px;"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Pilih Kelas</label>
                <select name="id_kelas" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($tugas_data['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Mata Pelajaran</label>
                <select name="id_mapel" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach ($mapel_list as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($tugas_data['mata_pelajaran_id'] == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars(isset($m['nama_mapel']) ? $m['nama_mapel'] : 'Unknown Mapel') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Tanggal Tugas</label>
                    <input type="date" name="tanggal" required value="<?= htmlspecialchars($tugas_data['tanggal']) ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Batas Waktu Pengumpulan</label>
                    <input type="date" name="batas_waktu" value="<?= !empty($tugas_data['batas_waktu']) ? date('Y-m-d', strtotime($tugas_data['batas_waktu'])) : '' ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Judul Tugas</label>
                <input type="text" name="judul" required value="<?= htmlspecialchars($tugas_data['judul']) ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Instruksi Tugas</label>
                <textarea name="instruksi" rows="5" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box; resize: vertical;"><?= htmlspecialchars($tugas_data['instruksi']) ?></textarea>
            </div>

            <button type="submit" name="update_tugas" style="width: 100%; padding: 14px; background: #0ea5e9; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
