<?php
$topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$allRuang = $allRuang ?? [];
$isAllRooms = !empty($isAllRooms);
$roomsData = $roomsData ?? [];
$kop = $kop ?? [];

// Logo resmi kop surat dari SIAKAD
$kopLogoUrl = '/assets/images/logo.png';
if (!empty($kop['logo'])) {
    $kopLogoUrl = function_exists('base_url') ? base_url('assets/images/' . $kop['logo']) : ('/assets/images/' . $kop['logo']);
} elseif (class_exists('\App\Core\Branding')) {
    try { 
        $kopLogoUrl = \App\Core\Branding::getInstitusiLogo(); 
    } catch (\Throwable $e) {}
}

$namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA';
$namaMadrasah = $kop['nama_madrasah'] ?? 'MADRASAH ALIYAH MIFTAHUL HUDA';
$alamatJalan = $kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu';
$telepon = $kop['telepon'] ?? '';
$email = $kop['email'] ?? '';
$website = $kop['website'] ?? '';

$topikNama = !empty($topik['nama_topik']) ? trim($topik['nama_topik']) : 'SIMULASI NEW CBT';
$topikTA = !empty($topik['tahun_ajaran']) ? trim($topik['tahun_ajaran']) : '2026/2027';

// Jika bukan cetak semua ruangan, bungkus $ruang dan $mejaList ke dalam format array standar
if (!$isAllRooms) {
    $roomsData = [
        [
            'ruang' => $ruang,
            'mejaList' => $mejaList ?? []
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Ruang Ujian <?= $isAllRooms ? 'Semua Ruangan' : htmlspecialchars($ruang['nama_ruang']) ?> - CBT</title>
    <link rel="icon" type="image/png" href="<?= $kopLogoUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: #0f172a; 
            font-family: 'Plus Jakarta Sans', Arial, sans-serif; 
            color: #0f172a; 
            padding: 20px; 
            min-height: 100vh;
        }

        /* Top Bar Control (Screen Only) */
        .no-print-bar { 
            max-width: 960px; 
            margin: 0 auto 20px; 
            background: #ffffff; 
            padding: 12px 20px; 
            border-radius: 14px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 15px; 
            flex-wrap: wrap;
            border: 1px solid #cbd5e1; 
        }
        .btn-ctrl { 
            padding: 9px 16px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 800; 
            text-decoration: none; 
            cursor: pointer; 
            border: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.2s;
        }
        .btn-print { 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-print:hover { 
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-back:hover { 
            background: #e2e8f0; 
            color: #0f172a;
        }
        .select-filter-ruang {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.85rem;
            color: #0f172a;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .select-filter-ruang:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Lembar Kertas A4 */
        .paper-sheet { 
            max-width: 960px; 
            margin: 0 auto 30px; 
            background: white; 
            padding: 16mm 14mm 18mm; 
            border-radius: 6px; 
            box-shadow: 0 10px 35px rgba(0,0,0,0.3); 
            border: 1px solid #e2e8f0; 
        }

        /* KOP SURAT RESMI IDENTIK SIAKAD */
        .kop-wrapper-siakad {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 3.5px double #000000;
        }
        .kop-logo-img {
            width: 82px;
            height: auto;
            max-height: 85px;
            object-fit: contain;
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
        }
        .kop-content-center {
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            line-height: 1.25;
            padding: 0 85px;
            width: 100%;
        }
        .kop-yayasan {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .kop-madrasah {
            font-size: 19pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .kop-alamat {
            font-size: 9.5pt;
            line-height: 1.3;
        }
        .kop-kontak {
            font-size: 9.5pt;
            margin-top: 1px;
        }

        /* Judul Dokumen */
        .doc-title-box { 
            text-align: center; 
            margin-bottom: 14px; 
        }
        .doc-title-box h3 { 
            font-size: 1.05rem; 
            font-weight: 900; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-bottom: 2px; 
        }
        .doc-title-box p {
            font-size: 0.75rem;
            font-weight: 800;
            color: #0284c7;
            letter-spacing: 0.5px;
        }

        /* Metadata Tabel Ruangan (Pengawas Ruang Dihapus) */
        .meta-table { 
            width: 100%; 
            margin-bottom: 14px; 
            font-size: 0.8rem; 
            border-collapse: collapse; 
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .meta-table td { 
            padding: 6px 12px; 
        }

        /* Banner Papan Tulis Depan */
        .papan-tulis-banner { 
            background: #064e3b; 
            color: #ffffff; 
            border: 2px solid #022c22; 
            border-radius: 8px; 
            text-align: center; 
            padding: 9px 12px; 
            font-weight: 900; 
            font-size: 0.85rem; 
            letter-spacing: 1.5px; 
            margin-bottom: 20px; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Denah Meja & Kursi */
        .denah-grid { 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
            margin-bottom: 22px; 
        }
        .denah-row { 
            display: flex; 
            justify-content: center; 
            gap: 10px; 
            flex-wrap: nowrap; 
        }
        .meja-blok { 
            display: flex; 
            gap: 4px; 
            border: 1.5px solid #0f172a; 
            padding: 4px; 
            border-radius: 8px; 
            background: #f8fafc; 
        }
        .kursi-box { 
            width: 122px; 
            height: 72px; 
            border: 1.5px solid #0f172a; 
            border-radius: 8px; 
            padding: 5px 7px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            background: #ffffff; 
            box-sizing: border-box;
        }
        .kursi-head { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .kursi-num { 
            font-size: 0.68rem; 
            font-weight: 900; 
            background: #0f172a; 
            color: #ffffff; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-family: 'JetBrains Mono', monospace; 
        }
        .kursi-side { 
            font-size: 0.6rem; 
            font-weight: 800; 
            color: #64748b; 
        }
        .kursi-nama { 
            font-weight: 900; 
            font-size: 0.72rem; 
            color: #0f172a; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            line-height: 1.2; 
            margin: 2px 0; 
        }
        .kursi-kelas { 
            font-size: 0.65rem; 
            font-weight: 800; 
            color: #1d4ed8; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .kursi-kosong { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 0.68rem; 
            color: #94a3b8; 
            font-weight: 700;
            font-style: italic; 
        }

        .back-door-indicator {
            text-align: center; 
            font-size: 0.72rem; 
            font-weight: 800; 
            color: #64748b; 
            border-top: 1.5px dashed #cbd5e1; 
            padding-top: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        @media print {
            body { 
                background: #ffffff !important; 
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
                page-break-after: always;
                break-after: page;
            }
            .papan-tulis-banner { 
                background: #064e3b !important; 
                color: #ffffff !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .kursi-num { 
                background: #0f172a !important; 
                color: #ffffff !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            @page { 
                size: A4 portrait; 
                margin: 10mm 12mm; 
            }
        }
    </style>
</head>
<body>

<!-- Top Bar Kontrol (Layar Saja) -->
<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>

        <!-- Filter Ruangan Cepat (Bisa Pilih Langsung Tanpa Bolak-Balik) -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <label for="filterRuang" style="font-size: 0.82rem; font-weight: 800; color: #475569; display: flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-door-open" style="color: #2563eb;"></i> Filter Ruangan:
            </label>
            <select id="filterRuang" onchange="gantiRuangan(this.value)" class="select-filter-ruang">
                <?php foreach ($allRuang as $ar): ?>
                    <option value="<?= $ar['id'] ?>" <?= (!$isAllRooms && (int)($ruang['id'] ?? 0) === (int)$ar['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ar['nama_ruang']) ?> (<?= (int)$ar['total_peserta'] ?> Peserta)
                    </option>
                <?php endforeach; ?>
                <option value="all" <?= ($isAllRooms) ? 'selected' : '' ?>>
                    ⭐ Cetak Semua Ruangan Sekaligus
                </option>
            </select>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak Dokumen Denah A4
        </button>
    </div>
</div>

<!-- Render Halaman Denah per Ruangan -->
<?php foreach ($roomsData as $rIndex => $rItem): ?>
    <?php
    $curR = $rItem['ruang'];
    $curMejaList = $rItem['mejaList'];

    $tipeMeja = (int)($curR['tipe_meja'] ?? 1);
    $samping = (int)($curR['baris_samping'] ?? 0);
    $belakang = (int)($curR['baris_belakang'] ?? 0);
    if ($samping <= 0 || $belakang <= 0) {
        $polaParts = explode('x', strtolower($curR['kode_pola'] ?? ''));
        if (count($polaParts) === 3) {
            $tipeMeja = (int)trim($polaParts[0]) ?: 1;
            $samping = (int)trim($polaParts[1]) ?: 6;
            $belakang = (int)trim($polaParts[2]) ?: 5;
        } else {
            $tipeMeja = $tipeMeja ?: 1;
            $samping = $samping ?: 6;
            $belakang = $belakang ?: 5;
        }
    }

    $mejaByBaris = [];
    foreach ($curMejaList as $m) {
        $b = (int)$m['posisi_baris'];
        if (!isset($mejaByBaris[$b])) $mejaByBaris[$b] = [];
        $mejaByBaris[$b][] = $m;
    }

    $pesertaTerisi = count(array_filter($curMejaList, fn($x) => !empty($x['siswa_id'])));
    ?>
    <div class="paper-sheet">
        <!-- KOP SURAT RESMI IDENTIK SIAKAD (pengaturan_kop_surat) -->
        <div class="kop-wrapper-siakad">
            <img src="<?= $kopLogoUrl ?>" class="kop-logo-img" onerror="this.src='/public/uploads/logo/logo.png'; this.onerror=function(){this.src='/logo.png';};" alt="Logo Madrasah">
            <div class="kop-content-center">
                <div class="kop-yayasan"><?= htmlspecialchars($namaYayasan) ?></div>
                <div class="kop-madrasah"><?= htmlspecialchars($namaMadrasah) ?></div>
                <div class="kop-alamat"><?= nl2br(htmlspecialchars($alamatJalan)) ?></div>
                <div class="kop-kontak">
                    <?php if (!empty($telepon)): ?><span>Telp: <?= htmlspecialchars($telepon) ?></span><?php endif; ?>
                    <?php if (!empty($telepon) && !empty($email)): ?><span> &bull; </span><?php endif; ?>
                    <?php if (!empty($email)): ?><span>Email: <?= htmlspecialchars($email) ?></span><?php endif; ?>
                    <?php if (!empty($website)): ?><span> &bull; Web: <?= htmlspecialchars($website) ?></span><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title-box">
            <h3>DENAH TEMPAT DUDUK (<?= htmlspecialchars($topikNama) ?>)</h3>
            <p>TAHUN AJARAN <?= htmlspecialchars($topikTA) ?></p>
        </div>

        <!-- METADATA RUANGAN (PENGAWAS RUANG SUDAH DIHAPUS) -->
        <table class="meta-table">
            <tr>
                <td style="width: 14%; font-weight: 700; color: #475569;">Ruang Ujian</td>
                <td style="width: 2%;">:</td>
                <td style="width: 34%; font-weight: 800; color: #0f172a;"><?= htmlspecialchars($curR['nama_ruang'] . ' (' . $curR['kode_ruang'] . ')') ?></td>
                <td style="width: 16%; font-weight: 700; color: #475569;">Format Meja</td>
                <td style="width: 2%;">:</td>
                <td style="width: 32%; font-weight: 800; font-family: monospace; color: #0f172a;"><?= htmlspecialchars($curR['kode_pola']) ?> (Kapasitas <?= $curR['kapasitas'] ?>)</td>
            </tr>
            <tr>
                <td style="font-weight: 700; color: #475569;">Lokasi Gedung</td>
                <td>:</td>
                <td style="font-weight: 800; color: #0f172a;"><?= htmlspecialchars($curR['lokasi'] ?: 'Madrasah Aliyah Miftahul Huda') ?></td>
                <td style="font-weight: 700; color: #475569;">Peserta Terisi</td>
                <td>:</td>
                <td style="font-weight: 800; color: #059669;"><?= $pesertaTerisi ?> Siswa Terdaftar</td>
            </tr>
        </table>

        <!-- PAPAN TULIS & POSISI DEPAN -->
        <div class="papan-tulis-banner">
            <span>📋</span>
            <span>PAPAN TULIS & MEJA PENGAWAS (BAGIAN DEPAN RUANGAN)</span>
        </div>

        <!-- DENAH GRID MEJA -->
        <div class="denah-grid">
            <?php for ($b = 1; $b <= $belakang; $b++): ?>
                <div class="denah-row">
                    <?php
                    $mRow = $mejaByBaris[$b] ?? [];
                    $mejaByKolom = [];
                    foreach ($mRow as $mr) {
                        $k = (int)$mr['posisi_kolom'];
                        if (!isset($mejaByKolom[$k])) $mejaByKolom[$k] = [];
                        $mejaByKolom[$k][] = $mr;
                    }
                    ?>
                    <?php for ($k = 1; $k <= $samping; $k++): ?>
                        <?php if ($tipeMeja === 1): ?>
                            <?php
                            $seat = ($mejaByKolom[$k] ?? [])[0] ?? null;
                            if (!$seat) continue;
                            $hasSiswa = !empty($seat['siswa_id']);
                            ?>
                            <div class="kursi-box">
                                <div class="kursi-head">
                                    <span class="kursi-num">#<?= str_pad($seat['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <?php if ($hasSiswa): ?>
                                    <div class="kursi-nama" title="<?= htmlspecialchars($seat['nama_siswa']) ?>"><?= htmlspecialchars($seat['nama_siswa']) ?></div>
                                    <div class="kursi-kelas"><?= htmlspecialchars($seat['kelas_nama'] ?: 'X') ?> &bull; <?= htmlspecialchars($seat['nis'] ?: '-') ?></div>
                                <?php else: ?>
                                    <div class="kursi-kosong">Kosong</div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php $kMejas = $mejaByKolom[$k] ?? []; ?>
                            <div class="meja-blok">
                                <?php foreach ($kMejas as $seat): ?>
                                    <?php $hasSiswa = !empty($seat['siswa_id']); ?>
                                    <div class="kursi-box">
                                        <div class="kursi-head">
                                            <span class="kursi-num">#<?= str_pad($seat['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></span>
                                            <span class="kursi-side"><?= ($seat['posisi_sisi'] === 'L') ? 'Kiri' : 'Kanan' ?></span>
                                        </div>
                                        <?php if ($hasSiswa): ?>
                                            <div class="kursi-nama" title="<?= htmlspecialchars($seat['nama_siswa']) ?>"><?= htmlspecialchars($seat['nama_siswa']) ?></div>
                                            <div class="kursi-kelas"><?= htmlspecialchars($seat['kelas_nama'] ?: 'X') ?> &bull; <?= htmlspecialchars($seat['nis'] ?: '-') ?></div>
                                        <?php else: ?>
                                            <div class="kursi-kosong">Kosong</div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endfor; ?>
        </div>

        <!-- BAGIAN BELAKANG RUANGAN (TTD SUDAH DIHAPUS SESUAI PERMINTAAN) -->
        <div class="back-door-indicator">
            🚪 PINTU / BAGIAN BELAKANG RUANGAN
        </div>
    </div>
<?php endforeach; ?>

<script>
function gantiRuangan(ruangId) {
    var topikId = <?= $topikId ?>;
    var url = '?ruang_id=' + encodeURIComponent(ruangId);
    if (topikId > 0) {
        url += '&topik_id=' + topikId;
    }
    window.location.href = url;
}
</script>

</body>
</html>
