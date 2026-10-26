<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_guru = $_SESSION['nama'] ?? 'Guru';

// Get active semester
$activeTahunId = 1;
try {
    $stmtTahun = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
    }
} catch (Exception $e) {}

// Data List Kelas untuk Filter
$stmt_k = $core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas");
$kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

// Top 5 Pelanggaran
$stmt_top_pelanggaran = $core_db->prepare("
    SELECT s.nama, k.nama_kelas, SUM(bp.poin) as total_poin
    FROM bk_poin bp
    JOIN siswa s ON bp.siswa_id = s.id
    JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id
    JOIN kelas k ON r.kelas_id = k.id
    JOIN bk_kategori bk ON bp.kategori_id = bk.id
    WHERE bk.tipe = 'pelanggaran' AND bp.tahun_ajaran_id = ?
    GROUP BY s.id
    ORDER BY total_poin DESC
    LIMIT 5
");
$stmt_top_pelanggaran->execute([$activeTahunId]);
$topPelanggaran = $stmt_top_pelanggaran->fetchAll(PDO::FETCH_ASSOC);

// Top 5 Prestasi
$stmt_top_prestasi = $core_db->prepare("
    SELECT s.nama, k.nama_kelas, SUM(bp.poin) as total_poin
    FROM bk_poin bp
    JOIN siswa s ON bp.siswa_id = s.id
    JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id
    JOIN kelas k ON r.kelas_id = k.id
    JOIN bk_kategori bk ON bp.kategori_id = bk.id
    WHERE bk.tipe = 'prestasi' AND bp.tahun_ajaran_id = ?
    GROUP BY s.id
    ORDER BY total_poin DESC
    LIMIT 5
");
$stmt_top_prestasi->execute([$activeTahunId]);
$topPrestasi = $stmt_top_prestasi->fetchAll(PDO::FETCH_ASSOC);

// Filter Setup
$filter_nama = $_GET['nama'] ?? '';
$filter_kelas = $_GET['kelas_id'] ?? '';

// Build Query untuk Log Lengkap
$sql_log = "
    SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, bk.nama_kategori, bk.tipe, g.nama as nama_guru
    FROM bk_poin bp
    JOIN siswa s ON bp.siswa_id = s.id
    LEFT JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id
    LEFT JOIN kelas k ON r.kelas_id = k.id
    JOIN bk_kategori bk ON bp.kategori_id = bk.id
    LEFT JOIN guru g ON bp.guru_id = g.id
    WHERE bp.tahun_ajaran_id = ?
";
$params = [$activeTahunId];

if (!empty($filter_nama)) {
    $sql_log .= " AND s.nama LIKE ?";
    $params[] = '%' . $filter_nama . '%';
}
if (!empty($filter_kelas)) {
    $sql_log .= " AND k.id = ?";
    $params[] = $filter_kelas;
}

$sql_log .= " ORDER BY bp.created_at DESC LIMIT 50"; // Batasi 50 terakhir agar tidak berat
$stmt_log = $core_db->prepare($sql_log);
$stmt_log->execute($params);
$logData = $stmt_log->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Poin Log (Kesiswaan)';
include 'header.php';
?>
<style>
    .summary-container {
        display: flex; gap: 15px; margin-top: -2.5rem; position: relative; z-index: 10;
        flex-direction: column;
    }
    @media (min-width: 768px) {
        .summary-container { flex-direction: row; }
    }
    
    .card-top {
        background: #fff;
        border-radius: 16px;
        padding: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        flex: 1;
    }
    
    .card-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .top-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .top-item:last-child { border-bottom: none; }
    
    .badge-poin-large {
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.9rem;
    }
    .badge-poin-large.pelanggaran { background: #fee2e2; color: #ef4444; }
    .badge-poin-large.prestasi { background: #d1fae5; color: #10b981; }
    
    .filter-box {
        background: #fff; border-radius: 12px; padding: 15px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .form-control {
        width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; margin-bottom: 10px;
    }
    
    .log-card {
        background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 8px;
    }
    .log-card.tipe-pelanggaran { border-left: 4px solid #ef4444; }
    .log-card.tipe-prestasi { border-left: 4px solid #10b981; }
</style>

<div class="header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding-bottom: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kesiswaan</p>
            <h1>Dashboard Poin Siswa</h1>
        </div>
        <a href="aplikasi_saya.php" class="avatar" style="color: #ef4444; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    
    <div class="summary-container">
        <!-- Top Pelanggaran -->
        <div class="card-top">
            <div class="card-title" style="color: #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Top Pelanggaran (Poin Tertinggi)</div>
            <?php if(count($topPelanggaran) > 0): ?>
                <?php foreach($topPelanggaran as $tp): ?>
                <div class="top-item">
                    <div>
                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($tp['nama']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;">Kelas <?= htmlspecialchars($tp['nama_kelas']) ?></div>
                    </div>
                    <div class="badge-poin-large pelanggaran"><?= $tp['total_poin'] ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Belum ada data pelanggaran.</div>
            <?php endif; ?>
        </div>
        
        <!-- Top Prestasi -->
        <div class="card-top">
            <div class="card-title" style="color: #10b981;"><i class="fa-solid fa-star"></i> Top Prestasi (Poin Tertinggi)</div>
            <?php if(count($topPrestasi) > 0): ?>
                <?php foreach($topPrestasi as $tp): ?>
                <div class="top-item">
                    <div>
                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($tp['nama']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;">Kelas <?= htmlspecialchars($tp['nama_kelas']) ?></div>
                    </div>
                    <div class="badge-poin-large prestasi"><?= $tp['total_poin'] ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Belum ada data prestasi.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Filter Pencarian -->
    <div class="filter-box">
        <div style="font-weight: 800; color: #1e293b; margin-bottom: 10px;"><i class="fa-solid fa-filter"></i> Cari Riwayat Poin</div>
        <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="nama" class="form-control" placeholder="Cari Nama Siswa..." value="<?= htmlspecialchars($filter_nama) ?>" style="flex: 2; margin-bottom:0;">
            <select name="kelas_id" class="form-control" style="flex: 1; margin-bottom:0;">
                <option value="">Semua Kelas</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($filter_kelas == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="background: #1e293b; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: bold;"><i class="fa-solid fa-search"></i></button>
            <?php if(!empty($filter_nama) || !empty($filter_kelas)): ?>
            <a href="wakasis_poin.php" style="background: #ef4444; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold;"><i class="fa-solid fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Log Poin Terkini -->
    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 15px; margin-top: 20px;">Riwayat Poin (50 Terakhir)</h3>
    
    <?php if(count($logData) > 0): ?>
        <?php foreach($logData as $log): ?>
        <div class="log-card tipe-<?= $log['tipe'] ?>">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 1rem;"><?= htmlspecialchars($log['nama_siswa']) ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;">Kelas <?= htmlspecialchars($log['nama_kelas']) ?> | <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($log['tanggal'])) ?></div>
                </div>
                <span class="badge-poin-large <?= $log['tipe'] ?>" style="font-size: 0.8rem;"><?= ($log['tipe'] == 'pelanggaran' ? '-' : '+') . $log['poin'] ?> Poin</span>
            </div>
            
            <div style="background: #f8fafc; padding: 8px 10px; border-radius: 6px; font-size: 0.85rem; color: #475569;">
                <div style="font-weight: 600; color: #334155; margin-bottom: 3px;"><?= htmlspecialchars($log['nama_kategori']) ?></div>
                <?php if(!empty($log['keterangan'])): ?>
                <div style="font-style: italic; margin-bottom: 5px;">"<?= htmlspecialchars($log['keterangan']) ?>"</div>
                <?php endif; ?>
                <div style="font-size: 0.7rem; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 5px; margin-top: 5px;">
                    Diinput oleh: <strong><?= htmlspecialchars($log['nama_guru']) ?></strong>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            Tidak ada riwayat poin yang sesuai dengan pencarian.
        </div>
    <?php endif; ?>
    
    <div style="height: 30px;"></div>
</div>

<?php include 'footer_guru.php'; ?>