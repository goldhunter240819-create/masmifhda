<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = $_GET['guru_id'] ?? 0;
if (!$guru_id) die("Guru ID tidak valid.");

$tahun_sekarang = date('Y');

try {
    // Ambil data PKG dan Identitas Guru
    $stmt1 = $core_db->prepare("
        SELECT p.*, g.nama as nama_guru, g.nip, penilai.nama as nama_penilai
        FROM kurikulum_pkg_guru p 
        JOIN guru g ON p.guru_id = g.id 
        LEFT JOIN guru penilai ON p.penilai_id = penilai.id
        WHERE p.guru_id = ? AND p.tahun = ?
    ");
    $stmt1->execute([$guru_id, $tahun_sekarang]);
    $pkg = $stmt1->fetch(PDO::FETCH_ASSOC);
    
    if (!$pkg) die("Data PKG tidak ditemukan.");
    
    // Ambil nilai per kompetensi
    $stmt2 = $core_db->prepare("SELECT kompetensi_ke, nilai_konversi FROM kurikulum_pkg_nilai WHERE pkg_id = ? ORDER BY kompetensi_ke ASC");
    $stmt2->execute([$pkg['id']]);
    $nilai = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    $nilai_map = [];
    foreach ($nilai as $n) {
        $nilai_map[$n['kompetensi_ke']] = $n['nilai_konversi'];
    }
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

$page_title = 'Rekap PKG';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);">
    <div class="header-content">
        <div class="greeting">
            <p>PKG (Penilaian Kinerja Guru)</p>
            <h1>Rekapitulasi PKG</h1>
        </div>
        <div class="avatar" style="color: #ca8a04; background: white;" onclick="window.location.href='wakakur_pkg.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 20px;">
        <div style="font-weight: 700; color: #1e293b; margin-bottom: 5px; font-size: 1.1rem;">Guru Dinilai: <?= htmlspecialchars($pkg['nama_guru']) ?></div>
        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">Penilai: <?= htmlspecialchars($pkg['nama_penilai']) ?> | Periode: <?= htmlspecialchars($pkg['periode_penilaian']) ?></div>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem;">
            <thead>
                <tr style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 10px; text-align: center; color: #475569;">No</th>
                    <th style="padding: 10px; text-align: left; color: #475569;">Kompetensi</th>
                    <th style="padding: 10px; text-align: center; color: #475569;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php for($i=1; $i<=14; $i++): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px; text-align: center; color: #64748b;"><?= $i ?></td>
                        <td style="padding: 10px; color: #334155; font-weight: 500;">Kompetensi <?= $i ?></td>
                        <td style="padding: 10px; text-align: center; font-weight: 700; color: #0f172a;"><?= $nilai_map[$i] ?? '-' ?></td>
                    </tr>
                <?php endfor; ?>
                <tr style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                    <td colspan="2" style="padding: 15px 10px; text-align: right; font-weight: 700; color: #0f172a;">Total (Max 56)</td>
                    <td style="padding: 15px 10px; text-align: center; font-weight: 800; font-size: 1.1rem; color: #15803d;"><?= $pkg['total_skor_14_kompetensi'] ?></td>
                </tr>
            </tbody>
        </table>
        
        <div style="background: #fefce8; border: 1px solid #fde047; padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; font-weight: 700; color: #a16207; margin-bottom: 5px;">NILAI AKHIR PKG (SKALA 100)</div>
            <div style="font-size: 2.5rem; font-weight: 800; color: #ca8a04;"><?= number_format($pkg['nilai_akhir_pkg'], 2) ?></div>
            <div style="font-size: 1.1rem; font-weight: 700; color: #854d0e; margin-top: 5px;">Sebutan: <?= $pkg['sebutan'] ?></div>
        </div>
        
        <?php if ($pkg['status_selesai'] != 'Selesai'): ?>
            <button onclick="window.location.href='wakakur_pkg_identitas.php?guru_id=<?= $guru_id ?>'" style="width: 100%; background: #eab308; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 1rem; margin-top: 20px; cursor: pointer;">
                Lanjutkan Penilaian
            </button>
        <?php else: ?>
            <button onclick="alert('Fitur Cetak PDF belum tersedia')" style="width: 100%; background: #f1f5f9; color: #475569; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 1rem; margin-top: 20px; cursor: pointer;">
                <i class="fa-solid fa-print" style="margin-right: 5px;"></i> Cetak Rekapitulasi
            </button>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
