<?php
require_once 'koneksi.php';

// Auth check: Guru, Admin, atau Staf
if (!isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = (int)($_SESSION['guru_id'] ?? 0);
$user_id = (int)($_SESSION['user_id'] ?? 0);
$guru_nama = $_SESSION['guru_nama'] ?? ($_SESSION['nama'] ?? '');

$db = $siakad_db ?: $core_db;

// Parameter filter
$paket_id = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;
$ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
$selected_kelas = trim($_GET['kelas'] ?? '');
$search = trim($_GET['q'] ?? '');

// Action Buka Kunci Siswa (Reset Pelanggaran / Izinkan Lanjut)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'unlock') {
    $pesertaIdToUnlock = (int)($_POST['peserta_id'] ?? 0);
    if ($pesertaIdToUnlock > 0) {
        try {
            $stmtUn = $core_db->prepare("
                UPDATE cbt_peserta 
                SET status_pengerjaan = 'Mengerjakan', 
                    violation_count = 0, 
                    reset_count = reset_count + 1 
                WHERE id = ?
            ");
            $stmtUn->execute([$pesertaIdToUnlock]);
            $_SESSION['flash_msg'] = 'Sesi ujian siswa berhasil dibuka kembali!';
        } catch (\Exception $e) {
            $_SESSION['flash_err'] = 'Gagal membuka kunci: ' . $e->getMessage();
        }
    }
    header("Location: hasil_ujian.php?" . http_build_query($_GET));
    exit;
}

// 1. Ambil info paket jika paket_id disertakan
$paket = null;
$topik = null;
if ($paket_id > 0) {
    try {
        $stmtP = $db->prepare("SELECT * FROM bs_paket WHERE id = ?");
        $stmtP->execute([$paket_id]);
        $paket = $stmtP->fetch(PDO::FETCH_ASSOC);

        if ($paket && !empty($paket['topik_id'])) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([(int)$paket['topik_id']]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }
    } catch (\Exception $e) {}
}

// 2. Daftar seluruh paket soal milik guru / topik untuk selector dropdown
$allPaketList = [];
try {
    if ($guru_id > 0) {
        $stmtAll = $db->prepare("
            SELECT p.id, p.mapel_nama, p.kelas_nama, p.kelas_sasaran, t.nama_topik, t.jenis
            FROM bs_paket p
            LEFT JOIN bs_topik t ON p.topik_id = t.id
            WHERE (p.guru_id = ? OR p.guru_nama = ? OR p.guru_nama LIKE ?)
            ORDER BY t.id DESC, p.kelas_nama ASC, p.mapel_nama ASC
        ");
        $stmtAll->execute([$guru_id, $guru_nama, '%' . $guru_nama . '%']);
        $allPaketList = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmtAll = $db->query("
            SELECT p.id, p.mapel_nama, p.kelas_nama, p.kelas_sasaran, t.nama_topik, t.jenis
            FROM bs_paket p
            LEFT JOIN bs_topik t ON p.topik_id = t.id
            ORDER BY t.id DESC, p.kelas_nama ASC, p.mapel_nama ASC LIMIT 50
        ");
        $allPaketList = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (\Exception $e) {}

// Jika belum ada paket_id terpilih tapi ada daftar paket, gunakan paket pertama
if (!$paket && $paket_id === 0 && $ujian_id === 0 && !empty($allPaketList)) {
    $paket_id = (int)$allPaketList[0]['id'];
    try {
        $stmtP = $db->prepare("SELECT * FROM bs_paket WHERE id = ?");
        $stmtP->execute([$paket_id]);
        $paket = $stmtP->fetch(PDO::FETCH_ASSOC);
        if ($paket && !empty($paket['topik_id'])) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([(int)$paket['topik_id']]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }
    } catch (\Exception $e) {}
}

// 3. Cari ujian_id yang berelasi di cbt_ujian
$matchedUjianIds = [];
$cbtUjianList = [];

if ($ujian_id > 0) {
    $matchedUjianIds[] = $ujian_id;
    try {
        $stmtU = $core_db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
        $stmtU->execute([$ujian_id]);
        $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($uRow) {
            $cbtUjianList[] = $uRow;
            if (!$paket) {
                $paket = [
                    'mapel_nama' => $uRow['mapel_nama'],
                    'kelas_nama' => $uRow['kelas_nama'],
                    'kelas_sasaran' => $uRow['kelas_nama'],
                    'kkm' => $uRow['kkm'] ?? 75,
                    'durasi_menit' => $uRow['durasi_menit'] ?? 90
                ];
            }
        }
    } catch (\Exception $e) {}
} elseif ($paket) {
    try {
        $mMapel = trim($paket['mapel_nama'] ?? '');
        $mKelas = trim($paket['kelas_nama'] ?? '');
        $mSasaran = trim($paket['kelas_sasaran'] ?? '');
        $mTopik = (int)($paket['topik_id'] ?? 0);

        $stmtFind = $core_db->prepare("
            SELECT * FROM cbt_ujian 
            WHERE (topik_id = :topik OR :topik = 0)
              AND (
                  mapel_nama = :mapel 
                  OR mapel_nama LIKE CONCAT('%', :mapel2, '%') 
                  OR :mapel3 LIKE CONCAT('%', mapel_nama, '%')
              )
              AND (
                  kelas_nama = :kelas 
                  OR :sasaran LIKE CONCAT('%', kelas_nama, '%')
                  OR kelas_nama LIKE CONCAT('%', :kelas2, '%')
                  OR kelas_nama = 'Semua Kelas'
                  OR kelas_nama IS NULL
                  OR kelas_nama = ''
              )
            ORDER BY (kelas_nama = :kelas3) DESC, id DESC
        ");
        $stmtFind->execute([
            ':topik' => $mTopik,
            ':mapel' => $mMapel,
            ':mapel2' => $mMapel,
            ':mapel3' => $mMapel,
            ':kelas' => $mKelas,
            ':sasaran' => $mSasaran,
            ':kelas2' => $mKelas,
            ':kelas3' => $mKelas
        ]);
        $cbtUjianList = $stmtFind->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cbtUjianList as $cu) {
            $matchedUjianIds[] = (int)$cu['id'];
        }
    } catch (\Exception $e) {}
}

$matchedUjianIds = array_unique(array_filter($matchedUjianIds));

// 4. Ambil Daftar Peserta Ujian dari cbt_peserta
$pesertaList = [];
$kelasList = [];
$kkm = (int)($paket['kkm'] ?? 75);

if (!empty($matchedUjianIds)) {
    try {
        $inIds = implode(',', $matchedUjianIds);
        
        // Ambil daftar kelas unik untuk filter tabs
        $stmtKls = $core_db->query("
            SELECT DISTINCT kelas_nama 
            FROM cbt_peserta 
            WHERE ujian_id IN ($inIds) AND kelas_nama IS NOT NULL AND kelas_nama != ''
            ORDER BY kelas_nama ASC
        ");
        $kelasList = $stmtKls->fetchAll(PDO::FETCH_COLUMN);

        $sqlPeserta = "
            SELECT p.*, u.nama_ujian, u.kode_ujian, u.mapel_nama as ujian_mapel, u.kkm as kkm_ujian
            FROM cbt_peserta p
            JOIN cbt_ujian u ON p.ujian_id = u.id
            WHERE p.ujian_id IN ($inIds)
        ";
        $paramsP = [];

        if (!empty($selected_kelas)) {
            $sqlPeserta .= " AND p.kelas_nama = :kelas";
            $paramsP[':kelas'] = $selected_kelas;
        }

        if (!empty($search)) {
            $sqlPeserta .= " AND (p.nama_siswa LIKE :q OR p.nis LIKE :q)";
            $paramsP[':q'] = "%{$search}%";
        }

        $sqlPeserta .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
        $stmtPeserta = $core_db->prepare($sqlPeserta);
        $stmtPeserta->execute($paramsP);
        $pesertaList = $stmtPeserta->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

// 5. Kalkulasi Ringkasan Nilai & Statistik
$totalPeserta = count($pesertaList);
$selesaiList = array_filter($pesertaList, fn($p) => $p['status_pengerjaan'] === 'Selesai');
$totalSelesai = count($selesaiList);
$totalMengerjakan = count(array_filter($pesertaList, fn($p) => in_array($p['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan'])));
$totalTerkunci = count(array_filter($pesertaList, fn($p) => in_array($p['status_pengerjaan'], ['Terkunci', 'Pelanggaran'])));

$nilaiArr = array_map(fn($p) => (float)$p['nilai_akhir'], $selesaiList);
$avgNilai = !empty($nilaiArr) ? (array_sum($nilaiArr) / count($nilaiArr)) : 0;
$maxNilai = !empty($nilaiArr) ? max($nilaiArr) : 0;
$minNilai = !empty($nilaiArr) ? min($nilaiArr) : 0;

$tuntasCount = 0;
foreach ($selesaiList as $sp) {
    if ((float)$sp['nilai_akhir'] >= $kkm) {
        $tuntasCount++;
    }
}
$tuntasPercent = $totalSelesai > 0 ? round(($tuntasCount / $totalSelesai) * 100, 1) : 0;

$page_title = 'Hasil Ujian: ' . ($paket['mapel_nama'] ?? 'CBT');
include 'header.php';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Header Gradient Modern */
    .hasil-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
        padding: 68px 18px 36px;
        color: white;
        border-bottom-left-radius: 26px;
        border-bottom-right-radius: 26px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4);
        position: relative;
    }
    .hasil-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .btn-back-round {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: 0.2s;
    }
    .btn-back-round:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    .hasil-badge-top {
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.4);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* KPI Summary Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin: -24px 16px 16px;
        position: relative;
        z-index: 10;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .kpi-val {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
    }
    .kpi-lbl {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 700;
        margin-top: 3px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Card Student */
    .student-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px 16px;
        margin: 0 16px 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: transform 0.15s;
    }
    .student-card:active {
        transform: scale(0.99);
    }
    .score-badge {
        font-size: 1.15rem;
        font-weight: 900;
        padding: 4px 10px;
        border-radius: 10px;
        text-align: center;
        line-height: 1;
        min-width: 62px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .score-tuntas {
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #a7f3d0;
    }
    .score-remedial {
        background: #fef2f2;
        color: #dc2626;
        border: 1.5px solid #fecaca;
    }
    .score-proses {
        background: #fffbeb;
        color: #d97706;
        border: 1.5px solid #fde68a;
    }

    /* Filter pills */
    .filter-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 0 16px 14px;
        scrollbar-width: none;
    }
    .filter-pills::-webkit-scrollbar { display: none; }
    .pill-item {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 20px;
        white-space: nowrap;
        text-decoration: none;
        transition: 0.2s;
    }
    .pill-item.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
</style>

<!-- Header View Hasil Ujian -->
<div class="hasil-header">
    <div class="hasil-topbar">
        <a href="bank_soal.php" class="btn-back-round" title="Kembali ke Bank Soal">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="hasil-badge-top">
            <i class="fa-solid fa-square-poll-vertical"></i> Evaluasi CBT Mobile
        </div>
        <button type="button" onclick="window.print()" class="btn-back-round" title="Cetak Rekap Nilai" style="border: none; cursor: pointer;">
            <i class="fa-solid fa-print"></i>
        </button>
    </div>

    <div>
        <h2 style="margin: 0 0 4px; font-size: 1.35rem; font-weight: 900; letter-spacing: -0.3px;">
            <?= htmlspecialchars($paket['mapel_nama'] ?? 'Mata Pelajaran') ?>
        </h2>
        <div style="font-size: 0.78rem; color: #bae6fd; font-weight: 600; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($paket['kelas_nama'] ?? 'Semua Tingkat') ?></span>
            <span>&bull;</span>
            <span><i class="fa-solid fa-bullseye"></i> KKM: <b><?= $kkm ?></b></span>
            <?php if (!empty($topik['nama_topik'])): ?>
                <span>&bull;</span>
                <span><i class="fa-solid fa-folder"></i> <?= htmlspecialchars($topik['nama_topik']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dropdown Selector Ganti Paket Soal -->
    <?php if (!empty($allPaketList)): ?>
        <div style="margin-top: 14px;">
            <form method="GET" action="hasil_ujian.php">
                <select name="paket_id" onchange="this.form.submit()" style="width: 100%; background: rgba(255, 255, 255, 0.12); color: white; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 12px; padding: 8px 12px; font-size: 0.78rem; font-weight: 700; outline: none;">
                    <?php foreach ($allPaketList as $ap): ?>
                        <option value="<?= $ap['id'] ?>" <?= ((int)$ap['id'] === $paket_id) ? 'selected' : '' ?> style="color: #0f172a; background: white;">
                            <?= htmlspecialchars($ap['mapel_nama'] . ' - ' . $ap['kelas_nama'] . ' (' . ($ap['nama_topik'] ?: 'Topik') . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- 4 KPI Summary Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background: #eff6ff; color: #2563eb;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="kpi-val"><?= $totalPeserta ?></div>
            <div class="kpi-lbl">Total Siswa</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($avgNilai, 1) ?></div>
            <div class="kpi-lbl">Rata-Rata</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: #fdf4ff; color: #a855f7;">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($maxNilai, 1) ?></div>
            <div class="kpi-lbl">Skor Tertinggi</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background: #f0fdf4; color: #16a34a;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div class="kpi-val"><?= $tuntasPercent ?>%</div>
            <div class="kpi-lbl">Ketuntasan (<?= $tuntasCount ?>/<?= $totalSelesai ?>)</div>
        </div>
    </div>
</div>

<!-- Search & Form Filter -->
<div style="padding: 0 16px 10px;">
    <form method="GET" action="hasil_ujian.php" style="display: flex; gap: 8px;">
        <input type="hidden" name="paket_id" value="<?= $paket_id ?>">
        <input type="hidden" name="kelas" value="<?= htmlspecialchars($selected_kelas) ?>">
        <div style="position: relative; flex: 1;">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama atau NIS siswa..." style="width: 100%; box-sizing: border-box; background: white; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 10px 14px 10px 38px; font-size: 0.82rem; font-weight: 600; outline: none;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
        </div>
        <button type="submit" style="background: #2563eb; color: white; border: none; border-radius: 12px; padding: 0 16px; font-weight: 800; font-size: 0.82rem; cursor: pointer;">
            Cari
        </button>
    </form>
</div>

<!-- Filter Pills Kelas -->
<?php if (!empty($kelasList)): ?>
    <div class="filter-pills">
        <a href="hasil_ujian.php?paket_id=<?= $paket_id ?>&q=<?= urlencode($search) ?>" class="pill-item <?= empty($selected_kelas) ? 'active' : '' ?>">
            Semua Kelas (<?= $totalPeserta ?>)
        </a>
        <?php foreach ($kelasList as $kName): ?>
            <?php
            $cntKls = count(array_filter($pesertaList, fn($p) => $p['kelas_nama'] === $kName));
            ?>
            <a href="hasil_ujian.php?paket_id=<?= $paket_id ?>&kelas=<?= urlencode($kName) ?>&q=<?= urlencode($search) ?>" class="pill-item <?= ($selected_kelas === $kName) ? 'active' : '' ?>">
                <?= htmlspecialchars($kName) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Section List Nilai Peserta -->
<div style="padding: 4px 16px 8px; display: flex; align-items: center; justify-content: space-between;">
    <h3 style="margin: 0; font-size: 0.95rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-list-check" style="color: #2563eb;"></i> Daftar Nilai Siswa
    </h3>
    <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
        <?= count($pesertaList) ?> Peserta
    </span>
</div>

<?php if (empty($pesertaList)): ?>
    <div style="background: white; border-radius: 16px; padding: 35px 20px; text-align: center; border: 1.5px dashed #cbd5e1; margin: 0 16px 20px;">
        <div style="width: 55px; height: 55px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px;">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <h4 style="margin: 0 0 4px 0; font-size: 0.95rem; color: #1e293b; font-weight: 800;">Belum Ada Hasil Ujian Terdata</h4>
        <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4;">
            Belum ada siswa yang menyelesaikan atau mengerjakan ujian untuk mata pelajaran ini.
        </p>
    </div>
<?php else: ?>
    <div style="padding-bottom: 20px;">
        <?php foreach ($pesertaList as $idx => $p): ?>
            <?php
            $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
            $isTerkunci = in_array($p['status_pengerjaan'], ['Terkunci', 'Pelanggaran']);
            $isMengerjakan = in_array($p['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan']);
            $nilai = (float)($p['nilai_akhir'] ?? 0);
            $isTuntas = ($nilai >= $kkm);

            $initial = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $p['nama_siswa'] ?: 'S'), 0, 2));
            ?>
            <div class="student-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Avatar & Info Siswa -->
                    <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; padding-right: 10px;">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: <?= $isSelesai ? ($isTuntas ? '#ecfdf5' : '#fef2f2') : '#f8fafc' ?>; color: <?= $isSelesai ? ($isTuntas ? '#059669' : '#dc2626') : '#475569' ?>; border: 1.5px solid <?= $isSelesai ? ($isTuntas ? '#a7f3d0' : '#fecaca') : '#e2e8f0' ?>; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.85rem; flex-shrink: 0;">
                            <?= $initial ?>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($p['nama_siswa']) ?>
                            </div>
                            <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span>NIS: <b><?= htmlspecialchars($p['nis'] ?: '-') ?></b></span>
                                <span>&bull;</span>
                                <span style="background: #eff6ff; color: #1d4ed8; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                                    <?= htmlspecialchars($p['kelas_nama'] ?: 'Umum') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Nilai / Status -->
                    <div>
                        <?php if ($isSelesai): ?>
                            <div class="score-badge <?= $isTuntas ? 'score-tuntas' : 'score-remedial' ?>">
                                <span><?= number_format($nilai, 1) ?></span>
                                <span style="font-size: 0.55rem; font-weight: 800; text-transform: uppercase; margin-top: 2px;">
                                    <?= $isTuntas ? 'Tuntas' : 'Remedial' ?>
                                </span>
                            </div>
                        <?php elseif ($isTerkunci): ?>
                            <div class="score-badge score-remedial" style="font-size: 0.82rem; padding: 6px 8px;">
                                <i class="fa-solid fa-lock" style="font-size: 1rem; margin-bottom: 2px;"></i>
                                <span style="font-size: 0.55rem; font-weight: 800;">Terkunci</span>
                            </div>
                        <?php else: ?>
                            <div class="score-badge score-proses" style="font-size: 0.82rem; padding: 6px 8px;">
                                <i class="fa-solid fa-hourglass-half" style="font-size: 1rem; margin-bottom: 2px;"></i>
                                <span style="font-size: 0.55rem; font-weight: 800;">Sedang Ujian</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Detail Rincian Pengerjaan -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #e2e8f0; font-size: 0.72rem; color: #64748b;">
                    <div style="display: flex; gap: 10px;">
                        <span><i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Benar: <b><?= (int)($p['jumlah_benar'] ?? 0) ?></b></span>
                        <span><i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i> Salah: <b><?= (int)($p['jumlah_salah'] ?? 0) ?></b></span>
                    </div>

                    <div>
                        <?php if (!empty($p['waktu_selesai'])): ?>
                            <span><i class="fa-solid fa-clock"></i> Selesai <?= date('H:i', strtotime($p['waktu_selesai'])) ?> WIB</span>
                        <?php elseif (!empty($p['waktu_mulai'])): ?>
                            <span><i class="fa-solid fa-play"></i> Mulai <?= date('H:i', strtotime($p['waktu_mulai'])) ?> WIB</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tombol Buka Kunci Siswa jika status terkunci -->
                <?php if ($isTerkunci): ?>
                    <div style="margin-top: 10px;">
                        <form method="POST" action="hasil_ujian.php?<?= http_build_query($_GET) ?>">
                            <input type="hidden" name="action" value="unlock">
                            <input type="hidden" name="peserta_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" style="width: 100%; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 8px; padding: 8px 12px; font-weight: 800; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <i class="fa-solid fa-lock-open"></i> Buka Kunci Siswa (Izinkan Lanjutkan Ujian)
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
