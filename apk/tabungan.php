<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil tabungan
$tabungan = null;
try {
    $stmt_tb = $core_db->prepare("SELECT * FROM keuangan_tabungan WHERE siswa_id = :siswa_id");
    $stmt_tb->execute(['siswa_id' => $siswa_id]);
    $tabungan = $stmt_tb->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Ambil riwayat mutasi jika ada tabungan
$riwayat_mutasi = [];
if ($tabungan) {
    try {
        $stmt_ri = $core_db->prepare("SELECT * FROM keuangan_tabungan_mutasi WHERE siswa_id = :siswa_id ORDER BY tanggal DESC LIMIT 15");
        $stmt_ri->execute(['siswa_id' => $siswa_id]);
        $riwayat_mutasi = $stmt_ri->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$page_title = 'Tabungan';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <div class="greeting">
            <p>Rekapitulasi Keuangan</p>
            <h1>Tabungan Siswa</h1>
        </div>
        <div class="avatar" style="color: #10b981;">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if ($tabungan): ?>
        <div class="data-card" style="margin-top: -2.5rem; z-index: 10; border-top: 4px solid #10b981;">
            <div style="text-align: center; margin-bottom: 10px;">
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Nomor Rekening</div>
                <div style="font-weight: 700; color: #334155; font-family: monospace; letter-spacing: 1px;"><?= htmlspecialchars(!empty($tabungan['no_rekening']) ? $tabungan['no_rekening'] : ('TBG-' . str_pad($siswa_id, 5, '0', STR_PAD_LEFT))) ?></div>
            </div>
            <div style="height: 1px; background: #e2e8f0; margin: 10px 0;"></div>
            <div style="text-align: center;">
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 5px;">Saldo Tabungan</div>
                <div class="value-green" style="font-size: 1.8rem; font-weight: 800;"><?= formatRupiah($tabungan['saldo']) ?></div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 1.5rem;">Riwayat Mutasi Terakhir</h3>
        <div class="data-card" style="padding: 10px 1.5rem;">
            <?php if (count($riwayat_mutasi) > 0): ?>
                <?php foreach ($riwayat_mutasi as $r): 
                    $is_setor = $r['jenis_mutasi'] == 'Setor';
                ?>
                    <div class="list-item">
                        <div class="list-icon" style="background: <?= $is_setor ? '#ecfdf5' : '#fff1f2' ?>; color: <?= $is_setor ? '#10b981' : '#f43f5e' ?>;">
                            <i class="fa-solid <?= $is_setor ? 'fa-arrow-down-to-line' : 'fa-arrow-up-from-line' ?>"></i>
                        </div>
                        <div class="list-content">
                            <div class="list-title"><?= htmlspecialchars($r['jenis_mutasi']) ?> Tunai</div>
                            <div class="list-sub"><?= date('d M Y, H:i', strtotime($r['tanggal'])) ?></div>
                            <?php if($r['keterangan']): ?>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;"><i class="fa-solid fa-note-sticky"></i> <?= htmlspecialchars($r['keterangan']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="list-amount <?= $is_setor ? 'value-green' : 'value-red' ?>">
                            <?= $is_setor ? '+' : '-' ?> <?= formatRupiah($r['jumlah']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem; padding: 15px;">Belum ada riwayat mutasi tabungan.</div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="data-card" style="margin-top: -2.5rem; z-index: 10; text-align: center; padding: 40px 20px;">
            <i class="fa-solid fa-lock" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
            <h2 style="font-size: 1.3rem; color: #334155; margin-bottom: 8px;">Belum Buka Rekening</h2>
            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Putra/putri Anda belum terdaftar dalam program Bank Mini Madrasah (Tabungan Siswa).</p>
            
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; text-align: left;">
                <div style="font-weight: 700; color: #475569; font-size: 0.85rem; margin-bottom: 8px;"><i class="fa-solid fa-circle-info" style="color: #3b82f6;"></i> Syarat & Ketentuan:</div>
                <ul style="font-size: 0.8rem; color: #64748b; padding-left: 20px; line-height: 1.6; margin: 0;">
                    <li>Hubungi wali kelas atau pihak Tata Usaha (Kasir) untuk melakukan pendaftaran.</li>
                    <li>Membawa buku tabungan yang akan diterbitkan oleh sekolah.</li>
                    <li>Setoran dan penarikan hanya dilayani pada jam operasional sekolah.</li>
                    <li>Siswa diharapkan aktif menabung untuk melatih kebiasaan berhemat sejak dini.</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
