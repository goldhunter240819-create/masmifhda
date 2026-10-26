<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = $_GET['guru_id'] ?? 0;
if (!$guru_id) die("Guru ID tidak valid.");

try {
    // Ambil data guru yang dinilai
    $stmt1 = $core_db->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt1->execute([$guru_id]);
    $guru = $stmt1->fetch(PDO::FETCH_ASSOC);
    if (!$guru) die("Guru tidak ditemukan.");
    
    // Ambil daftar semua guru untuk pilihan Penilai
    $semua_guru = $core_db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Cek apakah sudah ada draft/data PKG
    $tahun_sekarang = date('Y');
    $stmt2 = $core_db->prepare("SELECT * FROM kurikulum_pkg_guru WHERE guru_id = ? AND tahun = ?");
    $stmt2->execute([$guru_id, $tahun_sekarang]);
    $pkg = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    if (!$pkg) {
        $pkg = [
            'id' => 0,
            'periode_penilaian' => "2 Januari $tahun_sekarang s.d 30 Juni $tahun_sekarang",
            'tahun' => $tahun_sekarang,
            'penilai_id' => 0,
            'tanggal_penilaian' => date('Y-m-d')
        ];
    }
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

$page_title = 'Identitas PKG';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);">
    <div class="header-content">
        <div class="greeting">
            <p>PKG (Penilaian Kinerja Guru)</p>
            <h1>Identitas Penilaian</h1>
        </div>
        <div class="avatar" style="color: #ca8a04; background: white;" onclick="window.location.href='wakakur_pkg.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <form action="api_wakakur_pkg_identitas_save.php" method="POST" id="formIdentitas">
        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">
        <input type="hidden" name="pkg_id" value="<?= $pkg['id'] ?>">
        
        <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 20px;">
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                Data Guru (Yang Dinilai)
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">Nama Guru</label>
                <input type="text" value="<?= htmlspecialchars($guru['nama']) ?>" readonly style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f1f5f9; color: #64748b; font-weight: 600;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">NIP / NUPTK</label>
                <input type="text" value="<?= htmlspecialchars($guru['nip'] ?? '-') ?>" readonly style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f1f5f9; color: #64748b;">
            </div>
            
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 15px; margin-top: 25px; font-size: 1.1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                Data Penilaian
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">Penilai (Asesor)</label>
                <select name="penilai_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; background: white;">
                    <option value="">-- Pilih Penilai --</option>
                    <?php foreach($semua_guru as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= ($pkg['penilai_id'] == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">Periode Penilaian</label>
                <input type="text" name="periode_penilaian" value="<?= htmlspecialchars($pkg['periode_penilaian']) ?>" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">Tanggal Penilaian</label>
                <input type="date" name="tanggal_penilaian" value="<?= htmlspecialchars($pkg['tanggal_penilaian']) ?>" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem;">Tahun Penilaian</label>
                <input type="text" name="tahun" value="<?= htmlspecialchars($pkg['tahun']) ?>" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
            
            <button type="submit" style="width: 100%; background: #eab308; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 1rem; margin-top: 15px; cursor: pointer;">
                Lanjutkan ke Instrumen <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </div>
    </form>
</div>

<?php include 'footer_guru.php'; ?>
