<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$page_title = 'Supervisi Administrasi';
include 'header.php';

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
$tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

// Fetch gurus and their status
$stmt = $core_db->prepare("
    SELECT g.id, g.nama, g.nip, g.foto,
           s.id as supervisi_id, s.tanggal_supervisi, s.nilai_akhir, s.penilai_nama
    FROM guru g
    LEFT JOIN kurikulum_supervisi_administrasi s 
      ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
    ORDER BY g.nama ASC
");
$stmt->execute([$tahun_ajaran_id]);
$gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Supervisi Adm.</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.location.href='aplikasi_saya.php'">
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
            if ($g['nilai_akhir'] < 70) $score_color = '#ef4444';
            elseif ($g['nilai_akhir'] < 85) $score_color = '#f59e0b';
        ?>
        <div style="margin-bottom: 15px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
            <div class="data-card" style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                
                <div style="flex: 1; overflow: hidden;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars($g['nama']) ?>
                    </div>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                        <?= empty($g['nip']) ? '-' : $g['nip'] ?>
                    </div>
                    
                    <?php if ($is_supervised): ?>
                    <div style="margin-top: 6px;">
                        <div style="display: inline-block; background: <?= $score_color ?>15; color: <?= $score_color ?>; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;">
                            <i class="fa-solid fa-check-circle" style="margin-right: 4px;"></i> Skor: <?= $g['nilai_akhir'] ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div style="margin-top: 6px;">
                        <div style="display: inline-block; background: #f1f5f9; color: #94a3b8; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                            <i class="fa-solid fa-clock" style="margin-right: 4px;"></i> Belum Dinilai
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <?php if ($is_supervised): ?>
                    <a href="wakakur_supervisi_adm_form.php?guru_id=<?= $g['id'] ?>" style="background: #fef9c3; color: #ca8a04; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; border: 1px solid #fde047; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <?php else: ?>
                    <a href="wakakur_supervisi_adm_form.php?guru_id=<?= $g['id'] ?>" style="background: #e0f2fe; color: #0284c7; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; border: 1px solid #bae6fd; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <i class="fa-solid fa-plus"></i> Nilai
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($is_supervised && !empty($g['penilai_nama'])): ?>
            <div style="background: #f8fafc; border-top: 1px dashed #e2e8f0; padding: 8px 15px; font-size: 0.75rem; color: #475569; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-user-check" style="color: #0ea5e9;"></i> 
                <span>Dinilai oleh: <strong style="color: #1e293b; font-weight: 700;"><?= htmlspecialchars($g['penilai_nama']) ?></strong></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
