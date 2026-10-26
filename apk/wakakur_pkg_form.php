<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$pkg_id = $_GET['pkg_id'] ?? 0;
$kompetensi_id = $_GET['kompetensi'] ?? 1;

if (!$pkg_id) die("PKG ID tidak valid.");

try {
    // Load data guru
    $stmt1 = $core_db->prepare("
        SELECT p.*, g.nama as nama_guru, g.nip 
        FROM kurikulum_pkg_guru p 
        JOIN guru g ON p.guru_id = g.id 
        WHERE p.id = ?
    ");
    $stmt1->execute([$pkg_id]);
    $pkg = $stmt1->fetch(PDO::FETCH_ASSOC);
    
    if (!$pkg) die("Data PKG tidak ditemukan.");
    
    // Load saved scores for this competency
    $stmt2 = $core_db->prepare("SELECT skor_json FROM kurikulum_pkg_nilai WHERE pkg_id = ? AND kompetensi_ke = ?");
    $stmt2->execute([$pkg_id, $kompetensi_id]);
    $saved_nilai = $stmt2->fetchColumn();
    $answers = $saved_nilai ? json_decode($saved_nilai, true) : [];
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Load instrumen from JSON
$json_path = 'pkg_instrumen.json';
$instrumen_data = json_decode(file_get_contents($json_path), true);

$current_kompetensi = null;
foreach ($instrumen_data as $k) {
    if ($k['id'] == $kompetensi_id) {
        $current_kompetensi = $k;
        break;
    }
}

if (!$current_kompetensi) die("Kompetensi tidak ditemukan.");

$total_kompetensi = count($instrumen_data);
$is_last = ($kompetensi_id == $total_kompetensi);

$page_title = 'Form PKG - Kompetensi ' . $kompetensi_id;
include 'header.php';
?>

<style>
    .score-radio { display: flex; gap: 8px; width: 100%; margin-top: 10px; }
    .score-radio input[type="radio"] { display: none; }
    .score-radio label { 
        flex: 1; padding: 10px 0; text-align: center;
        border: 2px solid #cbd5e1; border-radius: 8px; cursor: pointer; color: #64748b; font-weight: 700; font-size: 1.1rem;
        background: white; transition: all 0.2s ease;
    }
    .score-radio label:hover { border-color: #94a3b8; }
    
    /* Dynamic Colors: 0=Red, 1=Yellow, 2=Green */
    .score-radio input[value="0"]:checked + label { background: #ef4444; color: white; border-color: #ef4444; }
    .score-radio input[value="1"]:checked + label { background: #eab308; color: white; border-color: #eab308; }
    .score-radio input[value="2"]:checked + label { background: #22c55e; color: white; border-color: #22c55e; }
    
    .komp-badge { background: white; color: #ca8a04; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 800; display: inline-block; margin-bottom: 5px; }
</style>

<div class="header" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%); padding-bottom: 4rem;">
    <div class="header-content">
        <div class="greeting">
            <div class="komp-badge">Kompetensi <?= $kompetensi_id ?> dari <?= $total_kompetensi ?></div>
            <h2 style="color: white; font-size: 1.1rem; line-height: 1.4; margin: 0; padding-right: 20px;"><?= htmlspecialchars($current_kompetensi['nama']) ?></h2>
        </div>
        <div class="avatar" style="color: #ca8a04; background: white;" onclick="window.location.href='wakakur_pkg.php'">
            <i class="fa-solid fa-times"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div style="margin-top: -2.5rem; position: relative; z-index: 10; background: #fffbeb; border: 1px solid #fde047; padding: 12px 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
        <i class="fa-solid fa-user-tie" style="font-size: 1.5rem; color: #ca8a04;"></i>
        <div>
            <div style="font-size: 0.75rem; color: #a16207; font-weight: 700; text-transform: uppercase;">Menilai Guru:</div>
            <div style="font-weight: 800; color: #854d0e; font-size: 1rem;"><?= htmlspecialchars($pkg['nama_guru']) ?></div>
        </div>
    </div>

    <form id="formPkg" action="api_wakakur_pkg_save.php" method="POST">
        <input type="hidden" name="pkg_id" value="<?= $pkg_id ?>">
        <input type="hidden" name="kompetensi_ke" value="<?= $kompetensi_id ?>">
        <input type="hidden" name="is_last" value="<?= $is_last ? 1 : 0 ?>">
        
        <div class="data-card" style="padding: 20px;">
            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; font-size: 0.8rem; color: #475569; margin-bottom: 20px; text-align: center; border: 1px solid #e2e8f0;">
                <b style="color: #0f172a;">Keterangan Skor:</b><br>
                0 = Tidak terpenuhi<br>
                1 = Terpenuhi sebagian<br>
                2 = Seluruhnya terpenuhi
            </div>
            
            <?php foreach($current_kompetensi['indikator'] as $ind): ?>
                <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 20px;">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="width: 24px; height: 24px; background: #eab308; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                            <?= $ind['no'] ?>
                        </div>
                        <div style="font-size: 0.95rem; color: #334155; line-height: 1.5; font-weight: 500;">
                            <?= htmlspecialchars($ind['teks']) ?>
                        </div>
                    </div>
                    
                    <div class="score-radio">
                        <?php $val = $answers[$ind['no']] ?? ''; ?>
                        
                        <input type="radio" name="instrumen[<?= $ind['no'] ?>]" id="i_<?= $ind['no'] ?>_0" value="0" <?= ($val === 0 || $val === '0') ? 'checked' : '' ?> required>
                        <label for="i_<?= $ind['no'] ?>_0">0</label>
                        
                        <input type="radio" name="instrumen[<?= $ind['no'] ?>]" id="i_<?= $ind['no'] ?>_1" value="1" <?= ($val === 1 || $val === '1') ? 'checked' : '' ?>>
                        <label for="i_<?= $ind['no'] ?>_1">1</label>
                        
                        <input type="radio" name="instrumen[<?= $ind['no'] ?>]" id="i_<?= $ind['no'] ?>_2" value="2" <?= ($val === 2 || $val === '2') ? 'checked' : '' ?>>
                        <label for="i_<?= $ind['no'] ?>_2">2</label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <?php if($kompetensi_id > 1): ?>
                    <button type="button" onclick="window.location.href='wakakur_pkg_form.php?pkg_id=<?= $pkg_id ?>&kompetensi=<?= $kompetensi_id - 1 ?>'" style="flex: 1; background: #f1f5f9; color: #475569; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
                        <i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Prev
                    </button>
                <?php endif; ?>
                
                <button type="submit" id="btnNext" style="flex: 2; background: <?= $is_last ? '#10b981' : '#eab308' ?>; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
                    <?php if($is_last): ?>
                        Selesai & Rekap <i class="fa-solid fa-check" style="margin-left: 5px;"></i>
                    <?php else: ?>
                        Simpan & Lanjut <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                    <?php endif; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Prevent multiple submissions
document.getElementById('formPkg').addEventListener('submit', function() {
    let btn = document.getElementById('btnNext');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';
});
</script>

<?php include 'footer_guru.php'; ?>
