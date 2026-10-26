<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;

if (!$siswa_id) {
    echo "<script>alert('Siswa tidak ditemukan.'); window.location.href='guru_wali.php';</script>";
    exit;
}

// Cek apakah siswa ini adalah anak asuh dari guru ini
$stmt_siswa = $core_db->prepare("
    SELECT s.*, k.nama_kelas 
    FROM guru_wali_siswa gws
    JOIN aula_db.siswa s ON gws.siswa_id = s.id 
    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
    WHERE gws.guru_id = :gid AND s.id = :sid
");
$stmt_siswa->execute(['gid' => $guru_id, 'sid' => $siswa_id]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "<script>alert('Siswa ini bukan anak asuh Anda.'); window.location.href='guru_wali.php';</script>";
    exit;
}

// Ambil profil
$page_title = 'Detail Anak Asuh';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Profil Siswa</p>
            <h1>Detail Anak Asuh</h1>
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
                confirmButtonColor: '#ec4899'
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
        <p style="margin: 0; color: #64748b; font-size: 0.85rem;">NIS: <?= htmlspecialchars($siswa['nis']) ?> &bull; Kelas <?= htmlspecialchars($siswa['nama_kelas']) ?></p>
        
        <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
            <a href="chat_guru.php?siswa_id=<?= $siswa['id'] ?>" class="btn" style="background: #10b981; color: white; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; font-size: 0.95rem; display: flex; justify-content: center; align-items: center; gap: 8px;">
                <i class="fa-brands fa-whatsapp" style="font-size: 1.2rem;"></i> Chat Orang Tua
            </a>
            <a href="guru_wali_akademik.php?siswa_id=<?= $siswa['id'] ?>" class="btn" style="background: #0ea5e9; color: white; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; font-size: 0.95rem; display: flex; justify-content: center; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-line" style="font-size: 1.2rem;"></i> Pantau Akademik & Absensi
            </a>
        </div>
    </div>

    <!-- Biodata Lengkap -->
    <h3 class="section-title" style="margin-top: 15px;"><i class="fa-solid fa-address-card" style="color: #ec4899; margin-right: 5px;"></i> Biodata Lengkap</h3>
    
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        
        <!-- Tab: Pribadi -->
        <div style="padding: 12px 15px; background: #fdf2f8; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #be185d; font-size: 0.9rem;">
            <i class="fa-solid fa-user-tag"></i> Data Pribadi
        </div>
        <div style="padding: 15px;">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">NISN</div>
                <div style="color: #1e293b;"><?= !empty($siswa['nisn']) ? htmlspecialchars($siswa['nisn']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">NIK</div>
                <div style="color: #1e293b;"><?= !empty($siswa['nik']) ? htmlspecialchars($siswa['nik']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">TTL</div>
                <div style="color: #1e293b;"><?= htmlspecialchars($siswa['tempat_lahir'] ?? '-') ?>, <?= !empty($siswa['tanggal_lahir']) ? date('d-m-Y', strtotime($siswa['tanggal_lahir'])) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">Gender</div>
                <div style="color: #1e293b;"><?= ($siswa['jenis_kelamin'] == 'L') ? 'Laki-laki' : (($siswa['jenis_kelamin'] == 'P') ? 'Perempuan' : '-') ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">Agama</div>
                <div style="color: #1e293b;"><?= !empty($siswa['agama']) ? htmlspecialchars($siswa['agama']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">Asal Sekolah</div>
                <div style="color: #1e293b;"><?= !empty($siswa['sekolah_asal']) ? htmlspecialchars($siswa['sekolah_asal']) : '-' ?></div>
            </div>
        </div>

        <!-- Tab: Alamat -->
        <div style="padding: 12px 15px; background: #fdf2f8; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #be185d; font-size: 0.9rem;">
            <i class="fa-solid fa-map-location-dot"></i> Data Alamat
        </div>
        <div style="padding: 15px;">
            <div style="display: flex; flex-direction: column; gap: 5px; font-size: 0.85rem;">
                <div style="color: #1e293b; font-weight: 500;">
                    <?= !empty($siswa['alamat']) ? nl2br(htmlspecialchars($siswa['alamat'])) : '-' ?>
                </div>
                <div style="color: #64748b; margin-top: 5px;">
                    RT/RW: <?= htmlspecialchars($siswa['rt'] ?? '-') ?>/<?= htmlspecialchars($siswa['rw'] ?? '-') ?><br>
                    Desa: <?= htmlspecialchars($siswa['desa'] ?? '-') ?><br>
                    Kecamatan: <?= htmlspecialchars($siswa['kecamatan'] ?? '-') ?><br>
                    Kota: <?= htmlspecialchars($siswa['kota'] ?? '-') ?><br>
                    Provinsi: <?= htmlspecialchars($siswa['provinsi'] ?? '-') ?> (<?= htmlspecialchars($siswa['kode_pos'] ?? '-') ?>)
                </div>
            </div>
        </div>

        <!-- Tab: Orang Tua -->
        <div style="padding: 12px 15px; background: #fdf2f8; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #be185d; font-size: 0.9rem;">
            <i class="fa-solid fa-people-roof"></i> Data Orang Tua
        </div>
        <div style="padding: 15px;">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">Nama Ayah</div>
                <div style="color: #1e293b;"><?= !empty($siswa['nama_ayah']) ? htmlspecialchars($siswa['nama_ayah']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">Pekerjaan Ayah</div>
                <div style="color: #1e293b;"><?= !empty($siswa['pekerjaan_ayah']) ? htmlspecialchars($siswa['pekerjaan_ayah']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 15px; font-size: 0.85rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px;">
                <div style="color: #64748b; font-weight: 600;">Nama Ibu</div>
                <div style="color: #1e293b;"><?= !empty($siswa['nama_ibu']) ? htmlspecialchars($siswa['nama_ibu']) : '-' ?></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px; font-size: 0.85rem;">
                <div style="color: #64748b; font-weight: 600;">No HP Ortu</div>
                <div style="color: #1e293b; font-weight: 700; color: #10b981;"><?= !empty($siswa['no_hp_ortu']) ? htmlspecialchars($siswa['no_hp_ortu']) : '-' ?></div>
            </div>
        </div>
    </div>

    <a href="guru_wali.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px; margin-top: 15px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Anak Asuh
    </a>

</div>

<?php include 'footer_guru.php'; ?>
