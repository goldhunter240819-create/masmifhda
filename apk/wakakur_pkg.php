<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$page_title = 'Penilaian PKG';
include 'header.php';
$guru_id_session = $_SESSION['user_id'] ?? 0;

try {
    // Ambil daftar guru (kecuali admin/kamad jika perlu, tapi kita tampilkan semua)
    $gurus = $core_db->query("SELECT id, nama, nip, jabatan, is_kamad, foto FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Ambil status PKG per guru
    $supervisi_data = $core_db->query("SELECT guru_id, nilai_akhir_pkg, sebutan, status_selesai FROM kurikulum_pkg_guru")->fetchAll(PDO::FETCH_ASSOC);
    $status_map = [];
    foreach ($supervisi_data as $s) {
        $status_map[$s['guru_id']] = [
            'nilai' => $s['nilai_akhir_pkg'],
            'sebutan' => $s['sebutan'],
            'status' => $s['status_selesai']
        ];
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

?>
<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Penilaian PKG</h1>
        </div>
        <div class="avatar" style="color: #d97706; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div style="margin-top: -2.5rem; position: relative; z-index: 10;">
        <?php foreach($gurus as $g): 
            $stat = $status_map[$g['id']] ?? null;
            
            // Determine avatar URL using siakad folder approach
            if (!empty($g['foto'])) {
                $foto_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $g['foto'];
            } else {
                $foto_url = 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=f1f5f9&color=475569';
            }
            
            if($stat) {
                $is_done = ($stat['status'] == 'Selesai');
                $score_color = $is_done ? '#10b981' : '#f59e0b';
                $badge_text = $is_done ? "Skor: ".number_format($stat['nilai'],1)." (".$stat['sebutan'].")" : "Draft (Proses)";
                $badge_icon = $is_done ? 'fa-check-circle' : 'fa-hourglass-half';
                $target_url = $is_done ? "wakakur_pkg_rekap.php?guru_id={$g['id']}" : "wakakur_pkg_identitas.php?guru_id={$g['id']}";
            } else {
                $is_done = false;
                $score_color = '#94a3b8';
                $badge_text = 'Belum Dinilai';
                $badge_icon = 'fa-clock';
                $target_url = "wakakur_pkg_identitas.php?guru_id={$g['id']}";
            }
        ?>
        <div class="data-card" style="margin-bottom: 15px; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onclick="window.location.href='<?= $target_url ?>'">
            <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
            
            <div style="flex: 1; overflow: hidden;">
                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($g['nama']) ?>
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                    <?= empty($g['nip']) ? '-' : $g['nip'] ?>
                </div>
                
                <div style="margin-top: 6px; display: inline-block; background: <?= $score_color ?>15; color: <?= $score_color ?>; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">
                    <i class="fa-solid <?= $badge_icon ?>" style="margin-right: 4px;"></i> <?= $badge_text ?>
                </div>
            </div>
            
            <div style="color: #cbd5e1;">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
