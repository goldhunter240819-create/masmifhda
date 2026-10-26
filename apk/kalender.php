<?php
require_once 'koneksi.php';

$is_logged_in = isset($_SESSION['siswa_id']) || isset($_SESSION['guru_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$page_title = 'Kalender Akademik';
include 'header.php';

$agendas = [];
try {
    $stmt = $siakad_db->query("SELECT * FROM kalender_akademik WHERE tanggal_mulai >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) ORDER BY tanggal_mulai ASC");
    $kalender_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($kalender_data as $row) {
        $warna = '#3b82f6'; // Default blue
        $jenis = ucfirst(str_replace('_', ' ', $row['tipe']));
        
        switch(strtolower($row['tipe'])) {
            case 'libur':
                $warna = '#ef4444'; // Red
                break;
            case 'ujian':
            case 'pts':
            case 'pas':
                $warna = '#f59e0b'; // Orange
                break;
            case 'kegiatan':
                $warna = '#10b981'; // Green
                break;
            case 'pulang_awal':
                $warna = '#8b5cf6'; // Purple
                break;
        }

        // Jika agenda berlangsung lebih dari 1 hari, tampilkan rentang
        $judul = $row['keterangan'];
        if ($row['tanggal_mulai'] !== $row['tanggal_selesai']) {
            $judul .= ' (' . date('d/m/Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . ')';
        }

        $agendas[] = [
            'tanggal' => $row['tanggal_mulai'],
            'judul' => $judul,
            'jenis' => $jenis,
            'warna' => $warna
        ];
    }
} catch (PDOException $e) {
    // Gunakan data kosong jika terjadi error
}

?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <a href="<?= isset($_SESSION['guru_id']) ? 'profil_guru.php' : 'profil.php' ?>" style="color: white; font-size: 1.2rem; padding: 5px;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 25px;">
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">Tahun Ajaran 2026/2027</p>
            <h1 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Kalender Akademik</h1>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="text-align: center; margin-bottom: 20px;">
            <i class="fa-regular fa-calendar-days" style="font-size: 3rem; color: #0ea5e9; margin-bottom: 10px;"></i>
            <h2 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Agenda Penting Sekolah</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 5px 0 0 0;">Catat tanggal-tanggal penting di bawah ini agar tidak terlewat.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($agendas as $agenda): ?>
            <div style="display: flex; align-items: stretch; background: white; border-radius: 12px; overflow: hidden; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="width: 80px; background: <?= $agenda['warna'] ?>; color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 10px;">
                    <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;"><?= date('M', strtotime($agenda['tanggal'])) ?></span>
                    <span style="font-size: 1.8rem; font-weight: 800; line-height: 1;"><?= date('d', strtotime($agenda['tanggal'])) ?></span>
                    <span style="font-size: 0.7rem; font-weight: 500; opacity: 0.8; margin-top: 2px;"><?= date('Y', strtotime($agenda['tanggal'])) ?></span>
                </div>
                <div style="flex: 1; padding: 12px 15px; display: flex; flex-direction: column; justify-content: center;">
                    <span style="font-size: 0.7rem; font-weight: 700; color: <?= $agenda['warna'] ?>; background: <?= $agenda['warna'] ?>15; padding: 2px 8px; border-radius: 20px; align-self: flex-start; margin-bottom: 5px;"><?= $agenda['jenis'] ?></span>
                    <h3 style="margin: 0; font-size: 0.95rem; color: #1e293b; line-height: 1.3;"><?= htmlspecialchars($agenda['judul']) ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php 
if (isset($_SESSION['guru_id'])) {
    include 'footer_guru.php'; 
} else {
    include 'footer.php';
}
?>
