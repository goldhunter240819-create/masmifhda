<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;

if (!$siswa_id) {
    echo "<script>alert('Siswa tidak ditemukan.'); window.location.href='wali_kelas.php';</script>";
    exit;
}

// Cek Wali Kelas dan apakah siswa ada di kelas ini
$stmt_wk = $core_db->prepare("SELECT id, keterangan as nama_kelas FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
$stmt_wk->execute(['gid' => $guru_id]);
$wali_kelas = $stmt_wk->fetch(PDO::FETCH_ASSOC);

if (!$wali_kelas) {
    header("Location: akses_ditolak.php");
    exit;
}

// Ambil info siswa
$stmt_siswa = $core_db->prepare("
    SELECT s.*, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    JOIN guru_tugas gt ON (gt.keterangan = CONCAT('Kelas ', k.nama_kelas) OR gt.keterangan = k.nama_kelas) 
    WHERE gt.guru_id = :gid AND gt.tugas = 'Wali Kelas' AND s.id = :sid
");
$stmt_siswa->execute(['gid' => $guru_id, 'sid' => $siswa_id]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "<script>alert('Siswa tidak berada di kelas Anda.'); window.location.href='wali_kelas.php';</script>";
    exit;
}

// Proses form tambah catatan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah_catatan') {
    $jenis_catatan = $_POST['jenis_catatan'];
    $catatan = trim($_POST['catatan']);
    
    if (!empty($catatan)) {
        $stmt_ins = $core_db->prepare("INSERT INTO guru_catatan_siswa (guru_id, siswa_id, jenis_catatan, catatan) VALUES (?, ?, ?, ?)");
        $stmt_ins->execute([$guru_id, $siswa_id, $jenis_catatan, $catatan]);
        $success_msg = "Catatan berhasil ditambahkan!";
    } else {
        $error_msg = "Catatan tidak boleh kosong!";
    }
}

// Ambil riwayat catatan
$stmt_catatan = $core_db->prepare("SELECT * FROM guru_catatan_siswa WHERE siswa_id = ? ORDER BY tanggal DESC");
$stmt_catatan->execute([$siswa_id]);
$catatan_list = $stmt_catatan->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Detail Siswa';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Jurnal & Chat</p>
            <h1>Detail Siswa</h1>
        </div>
    </div>
</div>

<div class="main-content">
    
    <?php if (isset($success_msg)): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= addslashes($success_msg) ?>',
                confirmButtonColor: '#8b5cf6'
            });
        </script>
    <?php endif; ?>
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

    <!-- Profil Siswa -->
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; text-align: center; position: relative; overflow: visible;">
        <div style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin: -50px auto 10px; background: #e2e8f0; overflow: hidden; display: flex; justify-content: center; align-items: center; color: #94a3b8; font-size: 2.5rem;">
            <?php if (!empty($siswa['foto'])): ?>
                <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
            <?php else: ?>
                <i class="fa-solid fa-user"></i>
            <?php endif; ?>
        </div>
        <h2 style="margin: 0 0 5px 0; font-size: 1.2rem; color: #1e293b;"><?= htmlspecialchars($siswa['nama']) ?></h2>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem;">NIS: <?= htmlspecialchars($siswa['nis']) ?> • Kelas <?= htmlspecialchars($siswa['nama_kelas']) ?></p>
        
        <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
            <a href="chat_guru.php?siswa_id=<?= $siswa['id'] ?>" class="btn" style="background: #10b981; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i> Chat Orang Tua
            </a>
        </div>
    </div>

    <!-- Jurnal Perkembangan Anak -->
    <h3 class="section-title"><i class="fa-solid fa-book-open" style="color: #3b82f6; margin-right: 5px;"></i> Jurnal Perkembangan Anak</h3>
    
    <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="tambah_catatan">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Jenis Catatan</label>
                <select name="jenis_catatan" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #1e293b; outline: none; background: #f8fafc;" required>
                    <option value="Info">ℹ️ Informasi / Pemberitahuan</option>
                    <option value="Apresiasi">⭐ Apresiasi / Prestasi</option>
                    <option value="Evaluasi">📈 Evaluasi / Bimbingan</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Catatan Guru</label>
                <textarea name="catatan" rows="3" placeholder="Tuliskan perkembangan, apresiasi, atau hal yang perlu dievaluasi..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #1e293b; outline: none; background: #f8fafc; resize: none;" required></textarea>
            </div>
            <button type="submit" style="width: 100%; background: #8b5cf6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer;">
                Simpan Catatan
            </button>
        </form>
    </div>

    <!-- Riwayat Catatan -->
    <?php if (empty($catatan_list)): ?>
        <div style="text-align: center; padding: 20px; color: #64748b;">
            <i class="fa-regular fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
            <p style="font-size: 0.85rem;">Belum ada riwayat catatan untuk siswa ini.</p>
        </div>
    <?php else: ?>
        <div style="position: relative; padding-left: 15px; border-left: 2px solid #e2e8f0; margin-left: 10px; margin-bottom: 30px;">
            <?php foreach ($catatan_list as $c): 
                $icon = 'fa-info';
                $color = '#3b82f6';
                if ($c['jenis_catatan'] == 'Apresiasi') { $icon = 'fa-star'; $color = '#f59e0b'; }
                if ($c['jenis_catatan'] == 'Evaluasi') { $icon = 'fa-arrow-trend-up'; $color = '#ef4444'; }
            ?>
                <div style="position: relative; margin-bottom: 20px;">
                    <div style="position: absolute; left: -24px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: <?= $color ?>; border: 3px solid white; box-shadow: 0 0 0 1px <?= $color ?>;"></div>
                    <div style="background: white; border-radius: 12px; padding: 12px 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: <?= $color ?>; background: <?= $color ?>20; padding: 2px 8px; border-radius: 20px;">
                                <i class="fa-solid <?= $icon ?>"></i> <?= $c['jenis_catatan'] ?>
                            </span>
                            <span style="font-size: 0.7rem; color: #94a3b8;"><i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($c['tanggal'])) ?></span>
                        </div>
                        <p style="margin: 0; font-size: 0.9rem; color: #475569; line-height: 1.5; white-space: pre-wrap;"><?= htmlspecialchars($c['catatan']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="wali_kelas.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Siswa
    </a>

</div>

<?php include 'footer_guru.php'; ?>
