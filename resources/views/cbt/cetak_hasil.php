<?php
$bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglCetak = date('d') . ' ' . ($bulanIndo[(int)date('n')] ?? date('F')) . ' ' . date('Y');

// Identitas Resmi Institusi dari Database
$dbCore = isset($db) ? $db : (class_exists('\App\Core\Database') ? \App\Core\Database::connect('core') : null);
$inst = null;
if ($dbCore) {
    try {
        $inst = $dbCore->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {}
}

$namaYayasan = !empty($inst['nama_yayasan']) ? $inst['nama_yayasan'] : "YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA'UL ANWAR";
$namaInstitusi = !empty($inst['nama']) ? $inst['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
$nsm = !empty($inst['nsm']) ? $inst['nsm'] : '131218040003';
$npsn = !empty($inst['npsn']) ? $inst['npsn'] : '69988776';
$alamatJalan = !empty($inst['alamat']) ? $inst['alamat'] : 'Jl. Tirtomoyo No. 083, Margorahayu';
$desa = !empty($inst['desa']) ? $inst['desa'] : 'Tambak Jaya';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';
$kabupaten = !empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat';
$provinsi = !empty($inst['provinsi']) ? $inst['provinsi'] : 'Lampung';
$kodepos = !empty($inst['kodepos']) ? $inst['kodepos'] : '34884';
$telepon = !empty($inst['telepon']) ? $inst['telepon'] : '081938240819';
$email = !empty($inst['email']) ? $inst['email'] : 'masmiftahulhuda@gmail.com';
$website = !empty($inst['website']) ? $inst['website'] : 'masmifhda.sch.id';

$namaKepala = !empty($inst['nama_kepala']) ? $inst['nama_kepala'] : 'AHMAD RIFA\'I, M.Pd';
$nipKepala = !empty($inst['nip_kepala']) ? $inst['nip_kepala'] : '-';

$logoUrl = function_exists('base_url') ? base_url('public/uploads/logo/logo.png') : '/public/uploads/logo/logo.png';
$faviconUrl = $logoUrl;
if (class_exists('\App\Core\Branding')) {
    try {
        $logoUrl = \App\Core\Branding::getInstitusiLogo();
        $faviconUrl = \App\Core\Branding::getInstitusiFavicon();
    } catch (\Throwable $e) {}
}
$kemenagLogo = function_exists('base_url') ? base_url('public/logo_kemenag.png') : '/public/logo_kemenag.png';

// Parameter Filter Aktif
$selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : (isset($selectedTopikId) ? (int)$selectedTopikId : 0);
if ($selectedTopikId == 5) $selectedTopikId = 3;
$selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : (isset($selectedMapel) ? trim($selectedMapel) : '');
$selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : (isset($selectedKelas) ? trim($selectedKelas) : '');
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : (isset($sort) ? trim($sort) : 'nilai_desc');

// Default Array & Stats Fallbacks
$topikList = $topikList ?? [];
$mapelList = $mapelList ?? [];
$kelasList = $kelasList ?? [];
$totalSelesai = $totalSelesai ?? 0;
$rataRata = $rataRata ?? 0;
$persenTuntas = $persenTuntas ?? 0;
$tuntasCount = $tuntasCount ?? 0;
$maxNilai = $maxNilai ?? 0;
$minNilai = $minNilai ?? 0;

// Siapkan Data untuk Dropdown Filter di Toolbar
if (empty($topikList) && $dbCore) {
    try {
        $rawT = $dbCore->query("SELECT id, nama_topik FROM bs_topik WHERE status = 'Aktif' ORDER BY (id = 3) DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $seen = [];
        $topikList = [];
        foreach ($rawT as $rt) {
            $n = trim($rt['nama_topik']);
            if (!isset($seen[$n])) {
                $seen[$n] = true;
                $topikList[] = $rt;
            }
        }
    } catch (\Exception $e) {}
}
if (empty($mapelList) && $dbCore) {
    try {
        if ($selectedTopikId > 0) {
            $tpJudul = $dbCore->query("SELECT nama_topik FROM bs_topik WHERE id = " . (int)$selectedTopikId)->fetchColumn();
            $stmtM = $dbCore->prepare("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE (topik_id = ? OR (? != '' AND nama_ujian LIKE ?)) AND mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC");
            $stmtM->execute([$selectedTopikId, !empty($tpJudul) ? $tpJudul : '', !empty($tpJudul) ? "%{$tpJudul}%" : "%"]);
            $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
        }
        if (empty($mapelList)) {
            $mapelList = $dbCore->query("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (\Exception $e) {}
}
if (empty($kelasList) && $dbCore) {
    try {
        $k1 = $dbCore->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != ''")->fetchAll(PDO::FETCH_COLUMN);
        $k2 = $dbCore->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
        $k3 = $dbCore->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
        $merged = array_unique(array_filter(array_merge($k1, $k2, $k3)));
        $merged = array_diff($merged, ['Kelas X', 'Kelas XI', 'Kelas XII', 'Semua Kelas']);
        usort($merged, function($a, $b) { return strnatcasecmp($a, $b); });
        $kelasList = array_values($merged);
    } catch (\Exception $e) {}
}

$namaUjian = !empty($currentUjian['nama_ujian']) ? $currentUjian['nama_ujian'] : 'Asesmen CBT Siswa';
$mapelNama = !empty($currentUjian['mapel_nama']) ? $currentUjian['mapel_nama'] : (!empty($paket['mapel_nama']) ? $paket['mapel_nama'] : 'Semua Mapel');
$topikNama = !empty($currentUjian['topik_nama']) ? $currentUjian['topik_nama'] : (!empty($topik['nama_topik']) ? $topik['nama_topik'] : 'Simulasi CBT');
$kelasLabel = !empty($selectedKelas) ? $selectedKelas : (!empty($currentUjian['kelas_nama']) ? $currentUjian['kelas_nama'] : 'Semua Kelas');
$kkmNilai = (int)($currentUjian['kkm'] ?? ($kkmUjian ?? 75));
$durasiMenit = (int)($currentUjian['durasi_menit'] ?? 90);
$guruNama = !empty($currentUjian['guru_nama']) ? $currentUjian['guru_nama'] : (!empty($_SESSION['guru_nama']) ? $_SESSION['guru_nama'] : 'Proktor CBT');

// Sort label description
$sortLabels = [
    'nilai_desc' => 'Nilai Tertinggi ke Terendah (Rank)',
    'nilai_asc'  => 'Nilai Terendah ke Tertinggi',
    'nama_asc'   => 'Nama Siswa (A - Z)',
    'nama_desc'  => 'Nama Siswa (Z - A)',
    'nis_asc'    => 'Nomor Peserta / NIS (Urut)',
    'waktu_desc' => 'Waktu Selesai Ujian'
];
$activeSortLabel = $sortLabels[$sort ?? 'nilai_desc'] ?? 'Nilai Tertinggi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai CBT - <?= htmlspecialchars($mapelNama . ' (' . $kelasLabel . ')') ?></title>
    <link rel="icon" type="image/png" href="<?= $faviconUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/public/assets/js/jspdf.umd.min.js"></script>
    <script src="/public/assets/js/jspdf.plugin.autotable.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background: #f1f5f9;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
            padding: 20px;
        }

        /* Controls Bar (Layar Saja) */
        .no-print-bar {
            max-width: 950px;
            margin: 0 auto 20px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #cbd5e1;
        }
        .btn-ctrl {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
        }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover { background: #e2e8f0; }

        /* Lembar Dokumen Kertas A4 */
        .paper-sheet {
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            padding: 25mm 20mm 20mm;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            min-height: 297mm;
            position: relative;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-title-box {
            flex: 1;
            text-align: center;
            padding: 0 15px;
        }
        .kop-title-box h4 {
            font-size: 0.88rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .kop-title-box h2 {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .kop-title-box p {
            font-size: 0.72rem;
            color: #475569;
            line-height: 1.35;
        }

        /* Judul Dokumen */
        .doc-title-box {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title-box h3 {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .doc-title-box p {
            font-size: 0.78rem;
            color: #475569;
            font-weight: 700;
        }

        /* Metadata Info Grid */
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-lbl {
            width: 18%;
            color: #475569;
            font-weight: 700;
        }
        .meta-sep {
            width: 2%;
            font-weight: 800;
            color: #0f172a;
        }
        .meta-val {
            width: 30%;
            font-weight: 800;
            color: #0f172a;
        }

        /* Summary Stats Badge Box */
        .summary-stats-box {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 18px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-item-box {
            border-right: 1px solid #e2e8f0;
            padding: 2px 4px;
        }
        .stat-item-box:last-child { border-right: none; }
        .stat-item-num {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-item-lbl {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Tabel Data Nilai Siswa */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 0.78rem;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #0f172a;
            padding: 6px 7px;
            vertical-align: middle;
        }
        .nilai-table th {
            background: #e2e8f0;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .nilai-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .center-col { text-align: center; }
        .right-col { text-align: right; }
        .badge-status-print {
            font-weight: 800;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .print-tuntas {
            color: #15803d;
        }
        .print-remedial {
            color: #b91c1c;
        }
        .print-proses {
            color: #b45309;
        }
        .badge-kj-tag {
            background: #fdf2f8;
            color: #db2777;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 4px;
            border: 1px solid #fbcfe8;
            margin-left: 4px;
        }

        /* Kolom Tanda Tangan */
        .sign-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 0.82rem;
        }
        .sign-col {
            text-align: center;
            width: 250px;
        }
        .sign-space {
            height: 70px;
        }
        .sign-name {
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }
        .sign-nip {
            font-size: 0.75rem;
            color: #475569;
            margin-top: 2px;
        }

        /* Print Media Query */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .paper-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
            .nilai-table th {
                background: #e2e8f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .summary-stats-box {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

<!-- Control Bar di Layar dengan Filter Lengkap -->
<div class="no-print-bar" style="max-width: 950px; margin: 0 auto 15px; background: #ffffff; padding: 14px 18px; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 12px;">
    <!-- Baris 1: Filter Dropdown (Topik, Mapel, Kelas, Urutan) -->
    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back" style="padding: 8px 14px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>

        <!-- 1. Topik Ujian -->
        <div style="flex: 1.4; min-width: 170px;">
            <label style="font-size: 0.68rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 3px; text-transform: uppercase;">
                <i class="fa-solid fa-layer-group" style="color: #059669;"></i> Topik Ujian
            </label>
            <select id="selTopikCetak" onchange="applyCetakFilter(true)" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.82rem; font-weight: 700; color: #0f172a; background: #f8fafc; outline: none; cursor: pointer;">
                <option value="0">-- Semua Topik Ujian --</option>
                <?php foreach ($topikList as $tp): ?>
                    <option value="<?= (int)$tp['id'] ?>" <?= ($selectedTopikId == $tp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tp['nama_topik']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 2. Mata Pelajaran -->
        <div style="flex: 1.2; min-width: 150px;">
            <label style="font-size: 0.68rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 3px; text-transform: uppercase;">
                <i class="fa-solid fa-book-open" style="color: #059669;"></i> Mata Pelajaran
            </label>
            <select id="selMapelCetak" onchange="applyCetakFilter(false)" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.82rem; font-weight: 700; color: #0f172a; background: #f8fafc; outline: none; cursor: pointer;">
                <option value="">-- Semua Mata Pelajaran --</option>
                <?php foreach ($mapelList as $mp): ?>
                    <option value="<?= htmlspecialchars($mp) ?>" <?= ($selectedMapel === $mp) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mp) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 3. Filter Kelas -->
        <div style="flex: 1; min-width: 130px;">
            <label style="font-size: 0.68rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 3px; text-transform: uppercase;">
                <i class="fa-solid fa-graduation-cap" style="color: #059669;"></i> Filter Kelas
            </label>
            <select id="selKelasCetak" onchange="applyCetakFilter(false)" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.82rem; font-weight: 700; color: #0f172a; background: #f8fafc; outline: none; cursor: pointer;">
                <option value="">-- Semua Kelas --</option>
                <?php foreach ($kelasList as $kls): ?>
                    <option value="<?= htmlspecialchars($kls) ?>" <?= ($selectedKelas === $kls) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kls) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 4. Urutan Data Siswa -->
        <div style="flex: 1.2; min-width: 160px;">
            <label style="font-size: 0.68rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 3px; text-transform: uppercase;">
                <i class="fa-solid fa-arrow-down-wide-short" style="color: #059669;"></i> Urutan Siswa
            </label>
            <select id="selSortCetak" onchange="applyCetakFilter(false)" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.82rem; font-weight: 700; color: #0f172a; background: #f8fafc; outline: none; cursor: pointer;">
                <option value="nilai_desc" <?= (($sort ?? 'nilai_desc') === 'nilai_desc') ? 'selected' : '' ?>>Nilai Tertinggi (Rank)</option>
                <option value="nilai_asc" <?= (($sort ?? '') === 'nilai_asc') ? 'selected' : '' ?>>Nilai Terendah</option>
                <option value="nama_asc" <?= (($sort ?? '') === 'nama_asc') ? 'selected' : '' ?>>Alphabet Nama (A - Z)</option>
                <option value="nama_desc" <?= (($sort ?? '') === 'nama_desc') ? 'selected' : '' ?>>Alphabet Nama (Z - A)</option>
            </select>
        </div>
    </div>

    <!-- Baris 2: Status Terdata & Tombol Download/Cetak -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">
            Urutan: <b style="color: #0f172a;"><?= htmlspecialchars($activeSortLabel) ?></b> • Siswa: <b style="color: #059669;"><?= count($pesertaList) ?> Terdata</b>
        </span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" onclick="downloadPdfDirect()" id="btnDirectDownload" class="btn-ctrl" style="background: #059669; color: white; display: flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(5, 150, 105, 0.3); font-size: 0.82rem; padding: 8px 14px;">
                <i class="fa-solid fa-file-pdf"></i> Unduh File PDF
            </button>
            <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn-ctrl" style="background: #10b981; color: white; text-decoration: none; display: flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3); font-size: 0.82rem; padding: 8px 14px;">
                <i class="fa-solid fa-file-excel"></i> Unduh Excel (.xls)
            </a>
            <button type="button" onclick="window.print()" class="btn-ctrl btn-print" style="font-size: 0.82rem; padding: 8px 14px;">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (A4)
            </button>
        </div>
    </div>
</div>

<!-- Lembar Cetak Kertas -->
<div class="paper-sheet">

    <!-- Kop Surat Resmi Madrasah -->
    <div class="kop-header">
        <img src="<?= $logoUrl ?>" class="kop-logo" onerror="this.src='/public/uploads/logo/logo.png'" alt="Logo Madrasah">
        <div class="kop-title-box">
            <h4><?= htmlspecialchars($namaYayasan) ?></h4>
            <h2><?= htmlspecialchars($namaInstitusi) ?></h2>
            <p style="font-weight: 700; color: #0f172a; margin: 2px 0;">STATUS TERAKREDITASI B &bull; NSM: <?= htmlspecialchars($nsm) ?> &bull; NPSN: <?= htmlspecialchars($npsn) ?></p>
            <p><?= htmlspecialchars($alamatJalan) ?>, <?= htmlspecialchars($desa) ?>, Kec. <?= htmlspecialchars($kecamatan) ?>, Kab. <?= htmlspecialchars($kabupaten) ?>, <?= htmlspecialchars($provinsi) ?> KP. <?= htmlspecialchars($kodepos) ?></p>
            <p>Website: <?= htmlspecialchars($website) ?> &bull; Email: <?= htmlspecialchars($email) ?> &bull; Telp: <?= htmlspecialchars($telepon) ?></p>
        </div>
        <img src="<?= $kemenagLogo ?>" class="kop-logo" onerror="this.style.visibility='hidden'" alt="Logo Kemenag">
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title-box">
        <h3>REKAPITULASI HASIL ASESMEN CBT & DAFTAR NILAI SISWA</h3>
        <p>TAHUN AJARAN 2026/2027</p>
    </div>

    <!-- Metadata Informasi -->
    <table class="meta-table">
        <tr>
            <td class="meta-lbl">Mata Pelajaran</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($mapelNama) ?></td>

            <td class="meta-lbl">Kelas / Rombel</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($kelasLabel) ?></td>
        </tr>
        <tr>
            <td class="meta-lbl">Topik Asesmen</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($topikNama) ?></td>

            <td class="meta-lbl">Standar KKM</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $kkmNilai ?> Poin</td>
        </tr>
        <tr>
            <td class="meta-lbl">Alokasi Waktu</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $durasiMenit ?> Menit</td>

            <td class="meta-lbl">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $tglCetak ?></td>
        </tr>
    </table>

    <!-- Ringkasan Statistik -->
    <div class="summary-stats-box">
        <div class="stat-item-box">
            <div class="stat-item-num"><?= count($pesertaList) ?></div>
            <div class="stat-item-lbl">Total Siswa</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num"><?= $totalSelesai ?></div>
            <div class="stat-item-lbl">Selesai</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num"><?= number_format($rataRata, 1) ?></div>
            <div class="stat-item-lbl">Rata-Rata</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #15803d;"><?= number_format($maxNilai ?? ($maxScore ?? 0), 1) ?></div>
            <div class="stat-item-lbl">Tertinggi</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #b91c1c;"><?= number_format($minNilai ?? ($minScore ?? 0), 1) ?></div>
            <div class="stat-item-lbl">Terendah</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #2563eb;"><?= $persenTuntas ?>%</div>
            <div class="stat-item-lbl">Tuntas (<?= $tuntasCount ?>)</div>
        </div>
    </div>

    <!-- Tabel Daftar Nilai Siswa -->
    <table class="nilai-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">No Peserta / NIS</th>
                <th style="width: 28%;">Nama Lengkap Siswa</th>
                <th style="width: 12%;">Kelas</th>
                <th style="width: 7%;">Benar</th>
                <th style="width: 7%;">Salah</th>
                <th style="width: 7%;">Kosong</th>
                <th style="width: 9%;">Nilai Akhir</th>
                <th style="width: 12%;">Ketuntasan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="9" class="center-col" style="padding: 20px; color: #64748b; font-style: italic;">
                        Tidak ada data hasil ujian yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pesertaList as $idx => $p): ?>
                    <?php
                    $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
                    $isTerkunci = in_array($p['status_pengerjaan'] ?? '', ['Terkunci', 'Pelanggaran']);
                    $nilai = (float)($p['nilai_akhir'] ?? 0);
                    $isTuntas = ($nilai >= $kkmNilai);
                    $isKJ = (stripos($p['kelas_nama'] ?? '', 'Jauh') !== false || stripos($p['nama_siswa'] ?? '', 'Kelas Jauh') !== false);

                    $benar = (int)($p['jumlah_benar'] ?? 0);
                    $salah = (int)($p['jumlah_salah'] ?? 0);
                    $tidakJawab = (int)($p['jumlah_tidak_jawab'] ?? 0);
                    $totalSoal = (int)($p['total_soal_ujian'] ?? 0);
                    if ($totalSoal <= 0) $totalSoal = max(20, $benar + $salah);
                    if ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
                        $tidakJawab = $totalSoal - ($benar + $salah);
                    }
                    ?>
                    <tr>
                        <td class="center-col"><?= $idx + 1 ?></td>
                        <td class="center-col font-mono" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                            <?= htmlspecialchars($p['nis'] ?: '-') ?>
                        </td>
                        <td style="font-weight: 700; color: #0f172a;">
                            <?= htmlspecialchars($p['nama_siswa']) ?>
                            <?php if ($isKJ): ?>
                                <span class="badge-kj-tag">Kelas Jauh</span>
                            <?php endif; ?>
                        </td>
                        <td class="center-col"><?= htmlspecialchars($p['kelas_nama'] ?: '-') ?></td>
                        <td class="center-col" style="color: #15803d; font-weight: 700;"><?= $isSelesai ? $benar : '-' ?></td>
                        <td class="center-col" style="color: #b91c1c; font-weight: 700;"><?= $isSelesai ? $salah : '-' ?></td>
                        <td class="center-col" style="color: #64748b; font-weight: 700;"><?= $isSelesai ? $tidakJawab : '-' ?></td>
                        <td class="center-col" style="font-weight: 900; font-size: 0.88rem; color: <?= $isSelesai ? ($isTuntas ? '#15803d' : '#b91c1c') : '#64748b' ?>;">
                            <?= number_format($nilai, 1) ?>
                        </td>
                        <td class="center-col">
                            <?php if ($isSelesai): ?>
                                <span class="badge-status-print <?= $isTuntas ? 'print-tuntas' : 'print-remedial' ?>">
                                    <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                                </span>
                            <?php elseif ($isTerkunci): ?>
                                <span class="badge-status-print print-remedial">TERKUNCI</span>
                            <?php else: ?>
                                <span class="badge-status-print print-proses">PROSES</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi -->
    <div class="sign-wrapper">
        <div class="sign-col">
            <div>Mengetahui,</div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($namaKepala) ?></div>
            <div class="sign-nip">NIP. <?= htmlspecialchars($nipKepala) ?></div>
        </div>

        <div class="sign-col">
            <div><?= htmlspecialchars($kotaCetak ?? 'Lampung Barat') ?>, <?= $tglCetak ?></div>
            <div style="font-weight: 700;">Guru Pengampu / Proktor CBT</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($guruNama) ?></div>
            <div class="sign-nip">NIP. -</div>
        </div>
    </div>

</div>

<script>
function applyCetakFilter(isTopikChange) {
    const topik = document.getElementById('selTopikCetak') ? document.getElementById('selTopikCetak').value : '0';
    const mapel = (isTopikChange || !document.getElementById('selMapelCetak')) ? '' : document.getElementById('selMapelCetak').value;
    const kelas = document.getElementById('selKelasCetak') ? document.getElementById('selKelasCetak').value : '';
    const sort = document.getElementById('selSortCetak') ? document.getElementById('selSortCetak').value : 'nilai_desc';

    const url = new URL(window.location.href);
    if (topik && topik !== '0') {
        url.searchParams.set('topik_id', topik);
    } else {
        url.searchParams.delete('topik_id');
    }

    if (isTopikChange) {
        url.searchParams.delete('mapel');
    } else if (mapel) {
        url.searchParams.set('mapel', mapel);
    } else {
        url.searchParams.delete('mapel');
    }

    if (kelas) {
        url.searchParams.set('kelas', kelas);
    } else {
        url.searchParams.delete('kelas');
    }

    url.searchParams.set('sort', sort);
    url.searchParams.delete('ujian_id');
    url.searchParams.delete('paket_id');
    url.searchParams.delete('download');
    url.searchParams.delete('autoprint');
    url.searchParams.delete('export');

    window.location.href = url.toString();
}

function downloadPdfDirect() {
    const btn = document.getElementById('btnDirectDownload');
    if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan PDF...';

    if (typeof window.jspdf === 'undefined') {
        window.print();
        if (btn) btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Unduh File PDF';
        return;
    }

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        // Header / Kop Dokumen
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9.5);
        doc.text('<?= addslashes($namaYayasan) ?>', 105, 12, { align: 'center' });
        doc.setFontSize(13);
        doc.text('<?= addslashes($namaInstitusi) ?>', 105, 17, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7.5);
        doc.text('STATUS TERAKREDITASI B • NSM: <?= addslashes($nsm) ?> • NPSN: <?= addslashes($npsn) ?>', 105, 21.5, { align: 'center' });
        doc.text('<?= addslashes($alamatJalan) ?>, <?= addslashes($desa) ?>, Kec. <?= addslashes($kecamatan) ?>, Kab. <?= addslashes($kabupaten) ?>, <?= addslashes($provinsi) ?>', 105, 25.5, { align: 'center' });

        // Garis Pembatas Kop
        doc.setLineWidth(0.5);
        doc.line(14, 28, 196, 28);
        doc.setLineWidth(0.2);
        doc.line(14, 29, 196, 29);

        // Metadata Ujian
        doc.setFontSize(8.5);
        doc.setFont('helvetica', 'bold');
        doc.text('Mata Pelajaran', 14, 34);
        doc.setFont('helvetica', 'normal');
        doc.text(': <?= addslashes($mapelNama) ?>', 42, 34);

        doc.setFont('helvetica', 'bold');
        doc.text('Kelas / Rombel', 14, 39);
        doc.setFont('helvetica', 'normal');
        doc.text(': <?= addslashes($kelasLabel) ?>', 42, 39);

        doc.setFont('helvetica', 'bold');
        doc.text('Topik Ujian', 115, 34);
        doc.setFont('helvetica', 'normal');
        doc.text(': <?= addslashes($topikNama) ?>', 138, 34);

        doc.setFont('helvetica', 'bold');
        doc.text('KKM / Rata-rata', 115, 39);
        doc.setFont('helvetica', 'normal');
        doc.text(': <?= $kkmNilai ?> / <?= number_format($rataRata, 1) ?>', 138, 39);

        // Render Tabel secara Otomatis dari elemen .nilai-table
        doc.autoTable({
            html: '.nilai-table',
            startY: 44,
            theme: 'grid',
            styles: {
                font: 'helvetica',
                fontSize: 8,
                cellPadding: 2,
                lineColor: [203, 213, 225],
                lineWidth: 0.1,
                valign: 'middle'
            },
            headStyles: {
                fillColor: [5, 150, 105],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 10 },
                1: { halign: 'center', cellWidth: 26 },
                2: { cellWidth: 50 },
                3: { halign: 'center', cellWidth: 24 },
                4: { halign: 'center', cellWidth: 14 },
                5: { halign: 'center', cellWidth: 14 },
                6: { halign: 'center', cellWidth: 14 },
                7: { halign: 'center', cellWidth: 18, fontStyle: 'bold' },
                8: { halign: 'center', cellWidth: 22, fontStyle: 'bold' }
            },
            didDrawPage: function (data) {
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(7.5);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor(148, 163, 184);
                doc.text('Dicetak dari CBT MAS Miftahul Huda', 14, 290);
                doc.text('Halaman ' + data.pageNumber + ' dari ' + pageCount, 196, 290, { align: 'right' });
            }
        });

        // Trigger Direct Download File PDF
        const safeTitle = '<?= addslashes(preg_replace('/[^A-Za-z0-9_-]/', '_', "Rekap_Nilai_{$mapelNama}_{$kelasLabel}")) ?>';
        doc.save(safeTitle + '.pdf');

        if (btn) btn.innerHTML = '<i class="fa-solid fa-check"></i> PDF Terunduh!';
        setTimeout(() => {
            if (btn) btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Unduh File PDF';
        }, 2500);
    } catch (e) {
        console.error('jsPDF error:', e);
        window.print();
        if (btn) btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Unduh File PDF';
    }
}

window.addEventListener('load', function() {
    <?php if (!empty($_GET['download'])): ?>
    setTimeout(() => {
        downloadPdfDirect();
    }, 400);
    <?php elseif (!empty($_GET['autoprint'])): ?>
    setTimeout(() => {
        window.print();
    }, 300);
    <?php endif; ?>
});
</script>

</body>
</html>
