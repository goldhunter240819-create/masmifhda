<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil daftar anak asuh untuk dropdown
$stmt_siswa = $core_db->prepare("
    SELECT s.id, s.nama, k.nama_kelas 
    FROM guru_wali_siswa gws
    JOIN aula_db.siswa s ON gws.siswa_id = s.id 
    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
    WHERE gws.guru_id = :gid AND s.status = 'Aktif'
    ORDER BY s.nama ASC
");
$stmt_siswa->execute(['gid' => $guru_id]);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

if (empty($siswa_list)) {
    echo "<script>alert('Anda tidak memiliki anak asuh.'); window.location.href='guru_wali_jurnal.php';</script>";
    exit;
}

// Proses form tambah catatan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah_catatan') {
    $siswa_id = intval($_POST['siswa_id']);
    $jenis_catatan = $_POST['jenis_catatan'];
    $catatan = trim($_POST['catatan']);
    
    // Validasi apakah siswa_id benar anak asuhnya
    $valid_siswa = false;
    foreach ($siswa_list as $s) {
        if ($s['id'] == $siswa_id) {
            $valid_siswa = true;
            break;
        }
    }
    
    if ($valid_siswa && !empty($catatan)) {
        $stmt_ins = $core_db->prepare("INSERT INTO guru_catatan_siswa (guru_id, siswa_id, jenis_catatan, catatan) VALUES (?, ?, ?, ?)");
        $stmt_ins->execute([$guru_id, $siswa_id, $jenis_catatan, $catatan]);
        echo "<script>alert('Catatan bimbingan berhasil disimpan!'); window.location.href='guru_wali_jurnal.php';</script>";
        exit;
    } else {
        $error_msg = "Data tidak valid atau catatan kosong!";
    }
}

$page_title = 'Tambah Jurnal';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Jurnal Bimbingan</p>
            <h1>Tambah Catatan</h1>
        </div>
    </div>
</div>

<div class="main-content">

    <?php if (isset($error_msg)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= addslashes($error_msg) ?>',
                confirmButtonColor: '#ef4444'
            });
        </script>
    <?php endif; ?>

    <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-top: -20px; position: relative; z-index: 10;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="tambah_catatan">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Pilih Anak Asuh</label>
                <select name="siswa_id" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #1e293b; outline: none; background: #f8fafc;" required>
                    <option value="" disabled selected>-- Pilih Siswa --</option>
                    <?php foreach ($siswa_list as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (<?= htmlspecialchars($s['nama_kelas']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Jenis Bimbingan</label>
                <select name="jenis_catatan" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #1e293b; outline: none; background: #f8fafc;" required>
                    <option value="Info">Info Pendampingan</option>
                    <option value="Apresiasi">Apresiasi / Prestasi</option>
                    <option value="Evaluasi">Evaluasi / Masalah</option>
                </select>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Catatan (Rahasia)</label>
                <textarea name="catatan" rows="4" placeholder="Tuliskan hasil konseling atau pemantauan anak asuh..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #1e293b; outline: none; background: #f8fafc; resize: none;" required></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 6px rgba(245,158,11,0.3);">
                Simpan Jurnal
            </button>
        </form>
    </div>

    <a href="guru_wali_jurnal.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-top: 20px; margin-bottom: 20px;">
        Batal
    </a>

</div>

<?php include 'footer_guru.php'; ?>
