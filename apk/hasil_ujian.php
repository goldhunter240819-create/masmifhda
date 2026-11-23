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
$role_id = (int)($_SESSION['role_id'] ?? 0);
$isAdmin = ($role_id == 1 || $role_id == 99);

$db = $siakad_db ?: $core_db;

// 1. Ambil info paket jika paket_id disertakan dari link Bank Soal
$paket_id = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;
$ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
$paket = null;
if ($paket_id > 0) {
    try {
        $stmtP = $db->prepare("SELECT * FROM bs_paket WHERE id = ?");
        $stmtP->execute([$paket_id]);
        $paket = $stmtP->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

// 2. Ambil seluruh Mapel yang diampu oleh Guru ini (Isolasi Akun Guru)
$guruMapelList = [];
if ($guru_id > 0) {
    // Dari siakad.mengajar
    if (!empty($siakad_db)) {
        try {
            $stmtM = $siakad_db->prepare("
                SELECT DISTINCT mp.nama_mapel 
                FROM mengajar m
                JOIN mapel mp ON m.mapel_id = mp.id
                WHERE m.guru_id = ? AND mp.nama_mapel IS NOT NULL AND mp.nama_mapel != ''
                ORDER BY mp.nama_mapel ASC
            ");
            $stmtM->execute([$guru_id]);
            foreach ($stmtM->fetchAll(PDO::FETCH_COLUMN) as $m) {
                $m = trim($m);
                if (!empty($m) && !in_array($m, $guruMapelList)) {
                    $guruMapelList[] = $m;
                }
            }
        } catch (\Exception $e) {}
    }

    // Dari bs_paket
    try {
        $stmtP = $core_db->prepare("
            SELECT DISTINCT mapel_nama 
            FROM bs_paket 
            WHERE (guru_id = ? OR (? != '' AND guru_nama = ?) OR (? != '' AND guru_nama LIKE ?))
              AND mapel_nama IS NOT NULL AND mapel_nama != ''
            ORDER BY mapel_nama ASC
        ");
        $stmtP->execute([$guru_id, $guru_nama, $guru_nama, $guru_nama, "%{$guru_nama}%"]);
        foreach ($stmtP->fetchAll(PDO::FETCH_COLUMN) as $m) {
            $m = trim($m);
            if (!empty($m) && !in_array($m, $guruMapelList)) {
                $guruMapelList[] = $m;
            }
        }
    } catch (\Exception $e) {}
}

if ($paket && !empty($paket['mapel_nama'])) {
    $pMapel = trim($paket['mapel_nama']);
    if (!in_array($pMapel, $guruMapelList)) {
        $guruMapelList[] = $pMapel;
    }
}

// Parameter filter
$selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
if ($selectedTopikId == 5) $selectedTopikId = 3;
$selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
$selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'nilai_desc';

// Jika datang dari link paket soal tapi belum ada parameter URL
if ($paket) {
    if ($selectedTopikId === 0 && !empty($paket['topik_id'])) {
        $selectedTopikId = (int)$paket['topik_id'];
        if ($selectedTopikId == 5) $selectedTopikId = 3;
    }
    if (empty($selectedMapel) && !empty($paket['mapel_nama'])) {
        $selectedMapel = trim($paket['mapel_nama']);
    }
}

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

// 3. Ambil Data Topik Ujian (HANYA TOPIK YANG AKTIF)
$topikList = [];
try {
    $rawTopik = $core_db->query("SELECT id, nama_topik FROM bs_topik WHERE status = 'Aktif' ORDER BY (id = 3) DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $seenNames = [];
    foreach ($rawTopik as $rt) {
        $normName = trim($rt['nama_topik']);
        if (!isset($seenNames[$normName])) {
            $seenNames[$normName] = true;
            $topikList[] = $rt;
        }
    }
} catch (\Exception $e) {}

// Default ke topik aktif pertama jika belum dipilih
if ($selectedTopikId === 0 && !empty($topikList)) {
    $selectedTopikId = (int)$topikList[0]['id'];
}

// Ambil Nama Topik yang Aktif
$activeTopikNama = '';
foreach ($topikList as $tp) {
    if ((int)$tp['id'] === $selectedTopikId) {
        $activeTopikNama = trim($tp['nama_topik']);
        break;
    }
}

// 4. Daftar Mata Pelajaran (ISOLASI GURU: HANYA MAPEL YANG DIAMPU)
$mapelList = [];
if (!$isAdmin && !empty($guruMapelList)) {
    // Guru HANYA melihat mata pelajaran miliknya sendiri
    $mapelList = $guruMapelList;

    // Pastikan selectedMapel terkunci pada salah satu mapel yang diampu
    $foundMapel = false;
    foreach ($mapelList as $mp) {
        if (strcasecmp($selectedMapel, $mp) === 0) {
            $foundMapel = true;
            $selectedMapel = $mp;
            break;
        }
    }
    // Jika tidak sesuai atau belum milih, kunci ke mapel pertama guru
    if (!$foundMapel) {
        $selectedMapel = $mapelList[0];
    }
} else {
    // Fallback Admin: Ambil semua mapel dari cbt_ujian
    try {
        if ($selectedTopikId > 0) {
            $stmtM = $core_db->prepare("
                SELECT DISTINCT mapel_nama 
                FROM cbt_ujian 
                WHERE (topik_id = ? OR (? != '' AND nama_ujian LIKE ?)) 
                  AND mapel_nama IS NOT NULL AND mapel_nama != '' 
                ORDER BY mapel_nama ASC
            ");
            $stmtM->execute([$selectedTopikId, $activeTopikNama, "%{$activeTopikNama}%"]);
            $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
        }
        if (empty($mapelList)) {
            $mapelList = $core_db->query("
                SELECT DISTINCT mapel_nama 
                FROM cbt_ujian 
                WHERE mapel_nama IS NOT NULL AND mapel_nama != '' 
                ORDER BY mapel_nama ASC
            ")->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (\Exception $e) {}

    if (empty($selectedMapel) && !empty($mapelList) && !isset($_GET['mapel'])) {
        $selectedMapel = $mapelList[0];
    }
}

// 5. Ambil Daftar Kelas (Diprioritaskan sesuai kelas dari mapel yang dipilih)
$kelasList = [];
try {
    if (!empty($selectedMapel)) {
        $stmtK = $core_db->prepare("
            SELECT DISTINCT p.kelas_nama 
            FROM cbt_peserta p
            JOIN cbt_ujian u ON p.ujian_id = u.id
            WHERE (u.mapel_nama = ? OR u.mapel_nama LIKE ? OR u.nama_ujian LIKE ?)
              AND p.kelas_nama IS NOT NULL AND p.kelas_nama != ''
            ORDER BY p.kelas_nama ASC
        ");
        $stmtK->execute([$selectedMapel, "%{$selectedMapel}%", "%{$selectedMapel}%"]);
        $kelasList = $stmtK->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (\Exception $e) {}

// Fallback jika belum ada peserta yang ujian pada mapel ini
if (empty($kelasList)) {
    try {
        $k1 = $core_db->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != ''")->fetchAll(PDO::FETCH_COLUMN);
        $k2 = $core_db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
        $k3 = $core_db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
        $mergedKls = array_unique(array_filter(array_merge($k1, $k2, $k3)));
        $mergedKls = array_diff($mergedKls, ['Kelas X', 'Kelas XI', 'Kelas XII', 'Semua Kelas']);
        usort($mergedKls, function($a, $b) { return strnatcasecmp($a, $b); });
        $kelasList = array_values($mergedKls);
    } catch (\Exception $e) {}
} else {
    usort($kelasList, function($a, $b) { return strnatcasecmp($a, $b); });
}

// 6. Query Daftar Peserta & Nilai CBT (100% Terisolasi per Mapel Guru)
$sql = "SELECT p.*, u.nama_ujian, u.kode_ujian, u.mapel_nama as ujian_mapel, u.kkm as kkm_ujian, u.durasi_menit,
               (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
        FROM cbt_peserta p
        LEFT JOIN cbt_ujian u ON p.ujian_id = u.id
        WHERE 1=1";
$params = [];

if ($selectedTopikId > 0) {
    if (!empty($activeTopikNama)) {
        $sql .= " AND (u.topik_id = :topik_id OR u.nama_ujian LIKE :topik_like)";
        $params[':topik_id'] = $selectedTopikId;
        $params[':topik_like'] = "%{$activeTopikNama}%";
    } else {
        $sql .= " AND (u.topik_id = :topik_id)";
        $params[':topik_id'] = $selectedTopikId;
    }
}

if (!empty($selectedMapel)) {
    $sql .= " AND (u.mapel_nama = :mapel OR u.mapel_nama LIKE :mapel_like OR u.nama_ujian LIKE :mapel_like2)";
    $params[':mapel'] = $selectedMapel;
    $params[':mapel_like'] = "%{$selectedMapel}%";
    $params[':mapel_like2'] = "%{$selectedMapel}%";
} elseif (!$isAdmin && !empty($guruMapelList)) {
    // Jika tidak spesifik, batasi HANYA pada mapel-mapel guru ini
    $inPlaceholders = [];
    foreach ($guruMapelList as $idx => $gm) {
        $key = ":gm_" . $idx;
        $inPlaceholders[] = $key;
        $params[$key] = $gm;
    }
    $sql .= " AND u.mapel_nama IN (" . implode(',', $inPlaceholders) . ")";
}

if (!empty($selectedKelas)) {
    $sql .= " AND (p.kelas_nama = :kelas OR p.kelas_nama LIKE :kelas_like)";
    $params[':kelas'] = $selectedKelas;
    $params[':kelas_like'] = "%{$selectedKelas}%";
}

switch ($sort) {
    case 'nilai_asc':
        $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir ASC, p.nama_siswa ASC";
        break;
    case 'nama_asc':
        $sql .= " ORDER BY p.nama_siswa ASC, p.nilai_akhir DESC";
        break;
    case 'nama_desc':
        $sql .= " ORDER BY p.nama_siswa DESC, p.nilai_akhir DESC";
        break;
    case 'nilai_desc':
    default:
        $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
        break;
}

$pesertaList = [];
try {
    $stmtP = $core_db->prepare($sql);
    $stmtP->execute($params);
    $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

// KKM Ujian
$kkm = !empty($pesertaList) ? (int)($pesertaList[0]['kkm_ujian'] ?? 75) : (int)($paket['kkm'] ?? 75);

// 7. Kalkulasi Statistik & Metrik KPI
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

$page_title = 'Hasil Ujian: ' . (!empty($selectedMapel) ? $selectedMapel : 'CBT');
include 'header.php';
?>

<!-- SweetAlert2 (Local Offline) -->
<script src="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/js/sweetalert2.all.min.js"></script>

<style>
    /* Header Gradient Modern */
    .hasil-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
        padding: 68px 18px 30px;
        color: white;
        border-bottom-left-radius: 26px;
        border-bottom-right-radius: 26px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4);
        position: relative;
    }
    .hasil-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .btn-back-round {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: 0.2s;
    }
    .btn-back-round:active {
        transform: scale(0.95);
        background: rgba(255, 255, 255, 0.25);
    }
    .hasil-badge-top {
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
        border: 1px solid rgba(110, 231, 183, 0.35);
        padding: 4px 12px;
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
        margin: -20px 16px 14px;
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

    /* Student Card */
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
        <a href="cetak_hasil.php?topik_id=<?= $selectedTopikId ?>&mapel=<?= urlencode($selectedMapel) ?>&kelas=<?= urlencode($selectedKelas) ?>&sort=<?= urlencode($sort) ?>" target="_blank" class="btn-back-round" title="Cetak Rekap Nilai Resmi Kemenag A4">
            <i class="fa-solid fa-print"></i>
        </a>
    </div>

    <div>
        <h2 style="margin: 0 0 4px; font-size: 1.35rem; font-weight: 900; letter-spacing: -0.3px;">
            <?= htmlspecialchars(!empty($selectedMapel) ? $selectedMapel : 'Rekap Nilai Siswa') ?>
        </h2>
        <div style="font-size: 0.78rem; color: #bae6fd; font-weight: 600; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars(!empty($selectedKelas) ? $selectedKelas : 'Semua Kelas') ?></span>
            <span>&bull;</span>
            <span><i class="fa-solid fa-bullseye"></i> KKM: <b><?= $kkm ?></b></span>
            <?php if (!empty($activeTopikNama)): ?>
                <span>&bull;</span>
                <span><i class="fa-solid fa-folder"></i> <?= htmlspecialchars($activeTopikNama) ?></span>
            <?php endif; ?>
        </div>
    </div>
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

<!-- Form Filter Bertingkat: 1. Topik -> 2. Mapel -> 3. Kelas -> 4. Urutan -->
<div style="padding: 0 16px 12px;">
    <form id="formFilterHasil" method="GET" action="hasil_ujian.php" style="background: white; border-radius: 16px; padding: 14px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        <?php if ($paket_id > 0): ?>
            <input type="hidden" name="paket_id" value="<?= $paket_id ?>">
        <?php endif; ?>

        <!-- 1. Topik Ujian (Aktif) -->
        <div>
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                <i class="fa-solid fa-layer-group" style="color: #2563eb;"></i> 1. Topik Ujian (Aktif):
            </label>
            <select name="topik_id" id="selTopik" onchange="applyFilter(true)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                <option value="0">-- Semua Topik Ujian --</option>
                <?php foreach ($topikList as $tp): ?>
                    <option value="<?= (int)$tp['id'] ?>" <?= ($selectedTopikId == $tp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tp['nama_topik']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 2. Mata Pelajaran (Terisolasi Mapel Guru) -->
        <div>
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                <i class="fa-solid fa-book-open" style="color: #2563eb;"></i> 2. Mata Pelajaran <?= (!$isAdmin && !empty($guruMapelList)) ? '(Yang Diampu)' : '' ?>:
            </label>
            <select name="mapel" id="selMapel" onchange="applyFilter(false)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                <?php if ($isAdmin || empty($guruMapelList)): ?>
                    <option value="">-- Semua Mata Pelajaran --</option>
                <?php elseif (count($mapelList) > 1): ?>
                    <option value="">-- Semua Mapel yang Diampu --</option>
                <?php endif; ?>
                <?php foreach ($mapelList as $mp): ?>
                    <option value="<?= htmlspecialchars($mp) ?>" <?= ($selectedMapel === $mp) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mp) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 3. Filter Kelas -->
        <div>
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                <i class="fa-solid fa-graduation-cap" style="color: #2563eb;"></i> 3. Filter Kelas:
            </label>
            <select name="kelas" id="selKelas" onchange="applyFilter(false)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                <option value="">-- Semua Kelas --</option>
                <?php foreach ($kelasList as $kls): ?>
                    <option value="<?= htmlspecialchars($kls) ?>" <?= ($selectedKelas === $kls) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kls) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 4. Urutan Data Siswa -->
        <div>
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                <i class="fa-solid fa-arrow-down-wide-short" style="color: #2563eb;"></i> 4. Urutan Data Siswa:
            </label>
            <div style="display: flex; gap: 8px;">
                <select name="sort" id="selSort" onchange="applyFilter(false)" style="flex: 1; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <option value="nilai_desc" <?= ($sort === 'nilai_desc') ? 'selected' : '' ?>>Nilai Tertinggi (Rank 1 ➔ Terendah)</option>
                    <option value="nilai_asc" <?= ($sort === 'nilai_asc') ? 'selected' : '' ?>>Nilai Terendah ➔ Tertinggi</option>
                    <option value="nama_asc" <?= ($sort === 'nama_asc') ? 'selected' : '' ?>>Alphabet Nama Siswa (A ➔ Z)</option>
                    <option value="nama_desc" <?= ($sort === 'nama_desc') ? 'selected' : '' ?>>Alphabet Nama Siswa (Z ➔ A)</option>
                </select>
                <button type="button" onclick="resetFilterHasil()" style="padding: 10px 14px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 10px; display: <?= ($selectedTopikId > 0 || !empty($selectedKelas) || $sort !== 'nilai_desc') ? 'flex' : 'none' ?>; align-items: center; justify-content: center; cursor: pointer;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tombol Cepat Unduh Excel & Cetak Dokumen Resmi -->
<div style="padding: 0 16px 14px; display: flex; gap: 8px;">
    <a href="cetak_hasil.php?export=excel&topik_id=<?= $selectedTopikId ?>&mapel=<?= urlencode($selectedMapel) ?>&kelas=<?= urlencode($selectedKelas) ?>&sort=<?= urlencode($sort) ?>" style="flex: 1; box-sizing: border-box; padding: 12px 14px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
        <i class="fa-solid fa-file-excel" style="font-size: 1.1rem;"></i> Unduh Excel (.xls)
    </a>
    <a href="cetak_hasil.php?topik_id=<?= $selectedTopikId ?>&mapel=<?= urlencode($selectedMapel) ?>&kelas=<?= urlencode($selectedKelas) ?>&sort=<?= urlencode($sort) ?>" target="_blank" style="flex: 1; box-sizing: border-box; padding: 12px 14px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
        <i class="fa-solid fa-print" style="font-size: 1.1rem;"></i> Cetak / Simpan PDF
    </a>
</div>

<!-- Judul Daftar Nilai Siswa -->
<div style="display: flex; justify-content: space-between; align-items: center; padding: 0 16px 12px;">
    <h3 style="margin: 0; font-size: 0.95rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-list-check" style="color: #2563eb;"></i> Daftar Nilai Siswa
    </h3>
    <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; background: #f1f5f9; padding: 3px 8px; border-radius: 20px;">
        <?= count($pesertaList) ?> Siswa Terdata
    </span>
</div>

<?php if (empty($pesertaList)): ?>
    <div style="background: white; border-radius: 16px; padding: 35px 20px; text-align: center; border: 1.5px dashed #cbd5e1; margin: 0 16px 20px;">
        <div style="width: 55px; height: 55px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px;">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <h4 style="margin: 0 0 4px 0; font-size: 0.95rem; color: #1e293b; font-weight: 800;">Belum Ada Hasil Ujian Terdata</h4>
        <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4;">
            Belum ada data siswa untuk kombinasi topik, mata pelajaran, atau kelas yang dipilih.<br>Silakan pilih kelas lain di atas.
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
                                <?php $isKJ = (stripos($p['kelas_nama'] ?? '', 'jauh') !== false); ?>
                                <span style="<?= $isKJ ? 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;' : 'background: #eff6ff; color: #1d4ed8;' ?> padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                                    <?= htmlspecialchars($p['kelas_nama'] ?: 'Umum') ?>
                                    <?= $isKJ ? ' &bull; KJ' : '' ?>
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

<script>
function applyFilter(isTopikChange) {
    const fTopik = document.getElementById('selTopik') ? document.getElementById('selTopik').value : '0';
    const fMapel = (isTopikChange || !document.getElementById('selMapel')) ? '' : document.getElementById('selMapel').value;
    const fKelas = document.getElementById('selKelas') ? document.getElementById('selKelas').value : '';
    const fSort = document.getElementById('selSort') ? document.getElementById('selSort').value : 'nilai_desc';

    const url = new URL(window.location.href);
    if (fTopik && fTopik !== '0') {
        url.searchParams.set('topik_id', fTopik);
    } else {
        url.searchParams.delete('topik_id');
    }

    if (isTopikChange) {
        url.searchParams.delete('mapel');
    } else if (fMapel) {
        url.searchParams.set('mapel', fMapel);
    } else {
        url.searchParams.delete('mapel');
    }

    if (fKelas) {
        url.searchParams.set('kelas', fKelas);
    } else {
        url.searchParams.delete('kelas');
    }

    url.searchParams.set('sort', fSort);
    url.searchParams.delete('paket_id');
    url.searchParams.delete('ujian_id');
    url.searchParams.delete('q');

    window.location.href = url.toString();
}

function resetFilterHasil() {
    window.location.href = 'hasil_ujian.php';
}
</script>

<?php include 'footer.php'; ?>
