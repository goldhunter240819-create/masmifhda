<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$page_title = 'Supervisi Kunjungan Kelas';
include 'header.php';

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
$tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

// Fetch gurus and their status
$stmt = $core_db->prepare("
    SELECT g.id, g.nama, g.nip, g.foto,
           s.id as supervisi_id, s.tanggal_supervisi, s.nilai_akhir, s.penilai_nama
    FROM guru g
    LEFT JOIN kurikulum_supervisi_kelas s 
      ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
    ORDER BY g.nama ASC
");
$stmt->execute([$tahun_ajaran_id]);
$gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Supervisi Kelas</h1>
        </div>
        <div class="avatar" style="color: #d97706; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div style="margin-top: -2.5rem; position: relative; z-index: 10;">
        <?php foreach($gurus as $g): 
            $is_supervised = !empty($g['supervisi_id']);
            // Determine avatar URL using siakad folder approach
            if (!empty($g['foto'])) {
                $foto_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $g['foto'];
            } else {
                $foto_url = 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=f1f5f9&color=475569';
            }
            
            $score_color = '#10b981';
            if ($g['nilai_akhir'] <= 70) $score_color = '#ef4444';
            elseif ($g['nilai_akhir'] <= 80) $score_color = '#f59e0b';
        ?>
        <div class="data-card" style="margin-bottom: 15px; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onclick="window.location.href='wakakur_supervisi_kunjungan_form.php?guru_id=<?= $g['id'] ?>'">
            <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
            
            <div style="flex: 1; overflow: hidden;">
                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($g['nama']) ?>
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                    <i class="fa-solid fa-user-check"></i> Penilai: <?= empty($g['penilai_nama']) ? '-' : htmlspecialchars($g['penilai_nama']) ?>
                </div>
                
                <?php if ($is_supervised): ?>
                <div style="margin-top: 6px; display: inline-block; background: <?= $score_color ?>15; color: <?= $score_color ?>; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">
                    <i class="fa-solid fa-check-circle" style="margin-right: 4px;"></i> Skor: <?= number_format($g['nilai_akhir'], 1) ?>
                </div>
                <?php else: ?>
                <div style="margin-top: 6px; display: inline-block; background: #f1f5f9; color: #94a3b8; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                    <i class="fa-solid fa-clock" style="margin-right: 4px;"></i> Belum Dinilai
                </div>
                <?php endif; ?>
            </div>
            
            <div style="color: #cbd5e1;">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
