<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$page_title = 'Supervisi Nilai';
include 'header.php';

// Fetch gurus
$stmt = $core_db->prepare("SELECT id, nama, nip, foto FROM guru ORDER BY nama ASC");
$stmt->execute();
$gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="header" style="background: linear-gradient(135deg, #c026d3 0%, #86198f 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Supervisi Nilai</h1>
        </div>
        <div class="avatar" style="color: #c026d3; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div style="margin-top: -2.5rem; position: relative; z-index: 10;">
        <?php foreach($gurus as $g): 
            if (!empty($g['foto'])) {
                $foto_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $g['foto'];
            } else {
                $foto_url = 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=f1f5f9&color=475569';
            }
        ?>
        <div class="data-card" style="margin-bottom: 15px; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onclick="window.location.href='wakakur_supervisi_nilai_detail.php?guru_id=<?= $g['id'] ?>'">
            <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
            
            <div style="flex: 1; overflow: hidden;">
                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($g['nama']) ?>
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                    <?= empty($g['nip']) ? '-' : $g['nip'] ?>
                </div>
                
                <div style="margin-top: 6px; display: inline-block; background: #fdf4ff; color: #c026d3; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">
                    <i class="fa-solid fa-folder-open" style="margin-right: 4px;"></i> Lihat Jadwal & Evaluasi
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
