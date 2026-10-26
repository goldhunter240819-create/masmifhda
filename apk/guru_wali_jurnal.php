<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil riwayat catatan bimbingan
$stmt_catatan = $core_db->prepare("
    SELECT c.*, s.nama, s.nis, s.foto, k.nama_kelas
    FROM guru_catatan_siswa c
    JOIN aula_db.siswa s ON c.siswa_id = s.id
    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id
    WHERE c.guru_id = :gid
    ORDER BY c.tanggal DESC
");
$stmt_catatan->execute(['gid' => $guru_id]);
$catatan_list = $stmt_catatan->fetchAll(PDO::FETCH_ASSOC);

// Helper Tanggal Indo
$days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

function formatTanggalIndo($date) {
    global $days_indo, $months_indo;
    $d = date('l', strtotime($date));
    $m = date('m', strtotime($date));
    return $days_indo[$d] . ', ' . date('d', strtotime($date)) . ' ' . $months_indo[$m] . ' ' . date('Y', strtotime($date));
}

$page_title = 'Jurnal Bimbingan';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Jurnal Catatan</p>
            <h1>Bimbingan Wali</h1>
        </div>
        <div class="avatar" style="color: #f59e0b; background: white;">
            <i class="fa-solid fa-handshake-angle"></i>
        </div>
    </div>
</div>

<div class="main-content">
    
    <div style="margin-top: -20px; z-index: 10; position: relative; display: flex; justify-content: center; margin-bottom: 25px;">
        <a href="guru_wali_jurnal_tambah.php" class="btn" style="background: #ffffff; color: #f59e0b; padding: 12px 25px; border-radius: 50px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Tambah Catatan
        </a>
    </div>

    <!-- Riwayat Catatan -->
    <?php if (empty($catatan_list)): ?>
        <div class="data-card" style="text-align: center; padding: 30px 20px; color: #64748b;">
            <i class="fa-regular fa-folder-open" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
            <p style="font-size: 1rem; font-weight: 600; color: #475569;">Belum Ada Jurnal</p>
            <p style="font-size: 0.85rem; margin-top: 5px;">Silakan klik Tambah Catatan untuk membuat catatan bimbingan pertama Anda.</p>
        </div>
    <?php else: ?>
        <div style="position: relative; padding-left: 15px; border-left: 2px solid #fef3c7; margin-left: 10px; margin-bottom: 30px;">
            <?php foreach ($catatan_list as $c): 
                $icon = 'fa-info';
                $color = '#3b82f6';
                $bg = '#eff6ff';
                if ($c['jenis_catatan'] == 'Apresiasi') {
                    $icon = 'fa-star';
                    $color = '#10b981';
                    $bg = '#ecfdf5';
                } elseif ($c['jenis_catatan'] == 'Evaluasi') {
                    $icon = 'fa-triangle-exclamation';
                    $color = '#ef4444';
                    $bg = '#fef2f2';
                }
            ?>
            <div style="position: relative; margin-bottom: 20px;">
                <div style="position: absolute; left: -25px; top: 0; width: 20px; height: 20px; border-radius: 50%; background: <?= $color ?>; border: 4px solid white; box-shadow: 0 0 0 1px #e2e8f0;"></div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    
                    <div style="padding: 10px 15px; background: <?= $bg ?>; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: <?= $color ?>;"><i class="fa-solid <?= $icon ?>"></i> <?= htmlspecialchars($c['jenis_catatan']) ?></span>
                        <span style="font-size: 0.75rem; color: #64748b;"><?= formatTanggalIndo($c['tanggal']) ?></span>
                    </div>

                    <div style="padding: 12px 15px; background: #fafaf9; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; justify-content: center; align-items: center; color: #94a3b8; font-size: 0.9rem;">
                            <?php if (!empty($c['foto'])): ?>
                                <img src="/public/uploads/siswa/<?= htmlspecialchars($c['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;"><?= htmlspecialchars($c['nama']) ?></div>
                            <div style="font-size: 0.7rem; color: #64748b;">Kelas <?= htmlspecialchars($c['nama_kelas']) ?></div>
                        </div>
                    </div>

                    <div style="padding: 15px; font-size: 0.9rem; color: #334155; line-height: 1.5;">
                        <?= nl2br(htmlspecialchars($c['catatan'])) ?>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer_guru.php'; ?>
