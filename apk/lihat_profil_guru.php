<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil data guru lengkap
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$page_title = 'Detail Profil';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding-bottom: 2rem;">
    <div class="header-top">
        <a href="profil_guru.php" style="color: white; font-size: 1.2rem; text-decoration: none; padding: 5px;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="font-size: 1.1rem; margin: 0; color: white; font-weight: 800;">Detail Profil</h1>
        <div style="width: 24px;"></div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 80px; height: 80px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #0ea5e9; margin: 0 auto 15px; overflow: hidden; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <?php if(!empty($guru['foto'])): ?>
                    <img src="/public/uploads/guru/<?= htmlspecialchars($guru['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-chalkboard-user\'></i>';">
                <?php else: ?>
                    <i class="fa-solid fa-chalkboard-user"></i>
                <?php endif; ?>
            </div>
            <h2 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 5px;"><?= htmlspecialchars($guru['nama']) ?></h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= htmlspecialchars($guru['jabatan']) ?></div>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <a href="edit_profil_guru.php" class="btn-primary" style="flex: 1; background: #f59e0b; color: white; padding: 10px; font-size: 0.9rem;"><i class="fa-solid fa-pen"></i> Edit Profil</a>
            <a href="ubah_password_guru.php" class="btn-primary" style="flex: 1; background: #ef4444; color: white; padding: 10px; font-size: 0.9rem;"><i class="fa-solid fa-key"></i> Ubah Password</a>
        </div>

        <h3 class="section-title" style="font-size: 0.95rem; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Data Pokok</h3>
        <div class="data-row">
            <span class="data-label">Nama Lengkap</span>
            <span class="data-value"><?= htmlspecialchars($guru['nama'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">NIP</span>
            <span class="data-value"><?= htmlspecialchars($guru['nip'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">NIK</span>
            <span class="data-value"><?= htmlspecialchars($guru['nik'] ?: '-') ?></span>
        </div>

        <h3 class="section-title" style="font-size: 0.95rem; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Data Pribadi</h3>
        <div class="data-row">
            <span class="data-label">Tempat Lahir</span>
            <span class="data-value"><?= htmlspecialchars($guru['tempat_lahir'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tanggal Lahir</span>
            <span class="data-value">
                <?php
                if (!empty($guru['tanggal_lahir'])) {
                    $bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $time = strtotime($guru['tanggal_lahir']);
                    echo htmlspecialchars(date('d', $time) . ' ' . $bulan_indo[(int)date('m', $time)] . ' ' . date('Y', $time));
                } else {
                    echo '-';
                }
                ?>
            </span>
        </div>
        <div class="data-row">
            <span class="data-label">Jenis Kelamin</span>
            <span class="data-value"><?= htmlspecialchars($guru['jenis_kelamin'] === 'L' ? 'Laki-Laki' : ($guru['jenis_kelamin'] === 'P' ? 'Perempuan' : '-')) ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">No. HP / WA</span>
            <span class="data-value"><?= htmlspecialchars($guru['no_hp'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Email</span>
            <span class="data-value" style="font-size: 0.8rem;"><?= htmlspecialchars($guru['email'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label" style="min-width: 80px;">Alamat</span>
            <span class="data-value" style="font-size: 0.8rem; text-align: right;">
                <?= htmlspecialchars($guru['alamat'] ?: '-') ?><br>
                <?php if(!empty($guru['desa']) || !empty($guru['kecamatan']) || !empty($guru['kota']) || !empty($guru['provinsi'])): ?>
                    <span style="color: #94a3b8; font-size: 0.7rem;">
                        RT <?= htmlspecialchars($guru['rt'] ?: '-') ?> RW <?= htmlspecialchars($guru['rw'] ?: '-') ?>,
                        <?= htmlspecialchars($guru['desa'] ?: '-') ?>, <?= htmlspecialchars($guru['kecamatan'] ?: '-') ?>,<br>
                        <?= htmlspecialchars($guru['kota'] ?: '-') ?>, <?= htmlspecialchars($guru['provinsi'] ?: '-') ?> <?= htmlspecialchars($guru['kode_pos'] ?: '') ?>
                    </span>
                <?php endif; ?>
            </span>
        </div>

        <h3 class="section-title" style="font-size: 0.95rem; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Data Kepegawaian & Rekening</h3>
        <div class="data-row">
            <span class="data-label">Status Pegawai</span>
            <span class="data-value"><?= htmlspecialchars($guru['status_kepegawaian'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">NPK</span>
            <span class="data-value"><?= htmlspecialchars($guru['npk'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">NPWP</span>
            <span class="data-value"><?= htmlspecialchars($guru['npwp'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">No. Rekening</span>
            <span class="data-value"><?= htmlspecialchars($guru['no_rekening'] ?: '-') ?></span>
        </div>

        <h3 class="section-title" style="font-size: 0.95rem; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Data Sertifikasi</h3>
        <div class="data-row">
            <span class="data-label">NRG</span>
            <span class="data-value"><?= htmlspecialchars($guru['nrg'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">No Peserta</span>
            <span class="data-value"><?= htmlspecialchars($guru['no_peserta_sertifikasi'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">No Sertifikat</span>
            <span class="data-value"><?= htmlspecialchars($guru['no_sertifikat'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tanggal TTD</span>
            <span class="data-value">
                <?php
                if (!empty($guru['tgl_sertifikat'])) {
                    $time = strtotime($guru['tgl_sertifikat']);
                    echo htmlspecialchars(date('d', $time) . ' ' . $bulan_indo[(int)date('m', $time)] . ' ' . date('Y', $time));
                } else {
                    echo '-';
                }
                ?>
            </span>
        </div>
        <div class="data-row">
            <span class="data-label">Jenjang</span>
            <span class="data-value"><?= htmlspecialchars($guru['jenjang_sertifikat'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Mata Pelajaran</span>
            <span class="data-value"><?= htmlspecialchars($guru['mapel_sertifikat'] ?: '-') ?></span>
        </div>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
