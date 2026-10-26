<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses tabungan
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'tabungan' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
$has_access = (bool)$stmt_app->fetch();
if (!$has_access) {
    try {
        $stmt_kta = $core_db->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = :uid LIMIT 1");
        $stmt_kta->execute(['uid' => $user_id]);
        if ($stmt_kta->fetch()) $has_access = true;
    } catch(Exception $e) {}
}
if (!$has_access) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Daftar Rekening Siswa';
include 'header.php';

// Ambil daftar semua siswa beserta tabungan mereka
$all_siswa_tabungan = [];
try {
    $stmt_all = $core_db->query("
        SELECT s.id, s.nama, s.nis, COALESCE(k.nama_kelas, '-') as nama_kelas,
               COALESCE(t.saldo, 0) as saldo
        FROM siswa s 
        LEFT JOIN kelas k ON s.kelas_id = k.id 
        LEFT JOIN keuangan_tabungan t ON s.id = t.siswa_id 
        WHERE s.status = 'Aktif'
        ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC
    ");
    $all_siswa_tabungan = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil daftar kelas yang unik untuk filter
$kelas_list = [];
foreach($all_siswa_tabungan as $st) {
    if (!in_array($st['nama_kelas'], $kelas_list) && $st['nama_kelas'] !== '-') {
        $kelas_list[] = $st['nama_kelas'];
    }
}
sort($kelas_list);

?>

<div class="header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Keuangan</p>
            <h1>Daftar Rekening</h1>
        </div>
        <div class="avatar" style="color: #3b82f6; background: white;">
            <i class="fa-solid fa-list"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            Semua Siswa Aktif
            <span style="font-size: 0.8rem; background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 20px; font-weight: 600;"><?= count($all_siswa_tabungan) ?> Siswa</span>
        </h3>
        
        <div style="margin-bottom: 15px; display: flex; flex-direction: column; gap: 10px;">
            <select id="filterKelas" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; box-sizing: border-box; font-weight: bold; color: #334155;" onchange="filterList()">
                <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                <?php foreach($kelas_list as $kls): ?>
                    <option value="<?= htmlspecialchars(strtolower($kls)) ?>"><?= htmlspecialchars($kls) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" id="filterInput" placeholder="Cari nama siswa..." style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; box-sizing: border-box;" onkeyup="filterList()">
        </div>

        <div id="listContainer">
            <?php if(empty($all_siswa_tabungan)): ?>
                <div style="text-align:center; padding: 20px; color: #64748b; font-size: 0.9rem;">Belum ada data siswa aktif.</div>
            <?php else: ?>
                <div id="messagePilihKelas" style="text-align:center; padding: 30px 20px; color: #64748b; font-size: 0.95rem; border: 2px dashed #e2e8f0; border-radius: 8px; background: #f8fafc;">
                    <i class="fa-solid fa-hand-pointer" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                    Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa.
                </div>
                
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach($all_siswa_tabungan as $st): ?>
                        <div class="siswa-item" data-nama="<?= strtolower(htmlspecialchars($st['nama'])) ?>" data-kelas="<?= strtolower(htmlspecialchars($st['nama_kelas'])) ?>" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: none; justify-content: space-between; align-items: center; background: white;">
                            <div style="flex:1;">
                                <div style="font-weight:bold; font-size:0.95rem; color:#1e293b;"><?= htmlspecialchars($st['nama']) ?></div>
                                <div style="font-size:0.75rem; color:#64748b; margin-top:3px;">NIS: <?= htmlspecialchars($st['nis'] ?: '-') ?> | Kelas: <?= htmlspecialchars($st['nama_kelas']) ?></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 800; color: <?= $st['saldo'] > 0 ? '#10b981' : '#94a3b8' ?>; font-size: 0.95rem;">Rp <?= number_format($st['saldo'], 0, ',', '.') ?></div>
                                <a href="admin_tabungan.php?cari=<?= urlencode($st['nama']) ?>" style="display:inline-block; background:#f1f5f9; color:#3b82f6; border:1px solid #cbd5e1; padding:4px 10px; border-radius:6px; font-size:0.75rem; font-weight:bold; cursor:pointer; margin-top: 5px; text-decoration:none;"><i class="fa-solid fa-folder-open"></i> Buka</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <a href="admin_tabungan.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px; margin-top: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Kasir
    </a>
</div>

<script>
function filterList() {
    let input = document.getElementById('filterInput').value.toLowerCase();
    let kelasFilter = document.getElementById('filterKelas').value;
    let items = document.getElementsByClassName('siswa-item');
    let messageEl = document.getElementById('messagePilihKelas');
    
    if (kelasFilter === "") {
        // Sembunyikan semua jika kelas belum dipilih
        for (let i = 0; i < items.length; i++) {
            items[i].style.display = 'none';
        }
        if(messageEl) messageEl.style.display = 'block';
        return;
    }
    
    if(messageEl) messageEl.style.display = 'none';

    for (let i = 0; i < items.length; i++) {
        let nama = items[i].getAttribute('data-nama');
        let kelas = items[i].getAttribute('data-kelas');
        
        let matchKelas = (kelas === kelasFilter);
        let matchNama = nama.includes(input);
        
        if (matchKelas && matchNama) {
            items[i].style.display = 'flex';
        } else {
            items[i].style.display = 'none';
        }
    }
}
</script>

<?php include 'footer_guru.php'; ?>
