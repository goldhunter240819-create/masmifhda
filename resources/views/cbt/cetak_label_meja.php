<?php
$namaInstitusi = 'MADRASAH ALIYAH MIFTAHUL HUDA';
$logoUrl = function_exists('base_url') ? base_url('public/uploads/logo/logo.png') : '/public/uploads/logo/logo.png';
$faviconUrl = $logoUrl;
if (class_exists('\App\Core\Branding')) {
    try { 
        $logoUrl = \App\Core\Branding::getInstitusiLogo(); 
        $faviconUrl = \App\Core\Branding::getInstitusiFavicon();
    } catch (\Throwable $e) {}
}

$topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$allRuang = $allRuang ?? [];
$isAllRooms = !empty($isAllRooms);
$roomsData = $roomsData ?? [];

// Jika bukan cetak semua ruangan, bungkus $ruang dan $mejaList ke dalam format standar
if (!$isAllRooms) {
    $roomsData = [
        [
            'ruang' => $ruang,
            'mejaList' => $mejaList ?? []
        ]
    ];
}

$totalSemuaLabel = 0;
foreach ($roomsData as $rd) {
    $totalSemuaLabel += count($rd['mejaList']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Meja Peserta <?= $isAllRooms ? 'Semua Ruangan' : htmlspecialchars($ruang['nama_ruang']) ?> - CBT</title>
    <link rel="icon" type="image/png" href="<?= $faviconUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800;900&family=JetBrains+Mono:wght@800;900&display=swap" rel="stylesheet">
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

        /* Top Control Bar (Screen Only) */
        .no-print-bar { 
            max-width: 900px; 
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

        /* Lembar A4 Preview & Print Wrapper */
        .page-sheet {
            max-width: 900px;
            margin: 0 auto 30px;
            background: #ffffff;
            padding: 12mm 10mm;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .room-header-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 2px solid #0f172a;
        }

        /* Grid Layout: 2 Kolom Kotak Presisi */
        .label-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 16px; 
            max-width: 780px;
            margin: 0 auto;
        }

        /* KARTU MEJA BENTUK KOTAK (SQUARE 1:1) */
        .meja-card { 
            background: #ffffff; 
            border: 2px dashed #0f172a; 
            border-radius: 16px; 
            padding: 14px 16px; 
            page-break-inside: avoid; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            aspect-ratio: 1 / 1;
            min-height: 310px;
            position: relative; 
            box-sizing: border-box;
            background-clip: padding-box;
        }
        .meja-card::before {
            content: '✂';
            position: absolute;
            top: -10px;
            right: 18px;
            background: #ffffff;
            padding: 0 4px;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Bagian Atas Kartu */
        .card-top { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            border-bottom: 1.5px solid #0f172a; 
            padding-bottom: 8px; 
            margin-bottom: 8px; 
            gap: 8px;
        }
        .card-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            flex: 1;
        }
        .card-logo { 
            width: 32px; 
            height: 32px; 
            object-fit: contain; 
            flex-shrink: 0;
        }
        .card-inst { 
            font-size: 0.72rem; 
            font-weight: 900; 
            color: #0f172a; 
            text-transform: uppercase; 
            letter-spacing: 0.3px;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-subinst {
            font-size: 0.58rem;
            font-weight: 800;
            color: #0284c7;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* NAMA RUANGAN BESAR & MENONJOL */
        .card-room-badge-large {
            font-size: 0.88rem;
            font-weight: 900;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #93c5fd;
            padding: 4px 10px;
            border-radius: 8px;
            white-space: nowrap;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.3px;
        }

        /* Bagian Tengah: Susunan Kotak Nomor Meja Besar & Siswa */
        .card-middle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 6px 0;
            padding: 6px 0;
        }
        .middle-left-info {
            flex: 1;
            min-width: 0;
        }

        /* KOTAK NOMOR MEJA BESAR (Star Feature) */
        .big-meja-box {
            width: 86px;
            height: 82px;
            background: #0f172a;
            border: 2px solid #0f172a;
            color: #ffffff;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }
        .big-meja-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3.5px;
            background: #38bdf8;
        }
        .big-meja-label {
            font-size: 0.6rem;
            font-weight: 900;
            color: #94a3b8;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            line-height: 1;
            margin-top: 2px;
        }
        .big-meja-num {
            font-family: 'Plus Jakarta Sans', 'JetBrains Mono', sans-serif;
            font-size: 2.7rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -1.5px;
            color: #ffffff;
            margin-top: 2px;
        }

        /* Detail Identitas Siswa */
        .siswa-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            margin-top: auto;
            margin-bottom: 6px;
        }
        .siswa-label {
            font-size: 0.58rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .siswa-nama { 
            font-size: 1.05rem; 
            font-weight: 900; 
            color: #0f172a; 
            line-height: 1.25; 
            margin-bottom: 6px; 
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        .siswa-meta { 
            display: flex; 
            gap: 6px; 
            flex-wrap: wrap; 
            align-items: center; 
        }
        .meta-pill {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .pill-nis {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            font-family: 'JetBrains Mono', monospace;
        }
        .pill-kelas {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        /* Bagian Bawah Kartu */
        .card-footer { 
            border-top: 1px dashed #cbd5e1; 
            padding-top: 6px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            font-size: 0.62rem; 
            color: #64748b; 
            font-weight: 800; 
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Print Specific Styles */
        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
                margin: 0 !important; 
            }
            .no-print-bar { 
                display: none !important; 
            }
            .page-sheet { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important; 
                margin: 0 0 20mm 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                page-break-after: always;
                break-after: page;
            }
            .room-header-title {
                display: none !important;
            }
            .label-grid { 
                max-width: 100% !important;
                gap: 10mm !important; 
            }
            .meja-card { 
                border: 2px dashed #000000 !important; 
                box-shadow: none !important; 
                aspect-ratio: 1 / 1 !important;
                height: 88mm !important;
                min-height: 88mm !important;
                max-height: 88mm !important;
                width: 88mm !important;
                padding: 10px 12px !important;
                page-break-inside: avoid !important;
            }
            .big-meja-box { 
                background: #000000 !important; 
                border-color: #000000 !important;
                color: #ffffff !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .big-meja-num {
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .card-room-badge-large {
                background: #eff6ff !important;
                color: #1d4ed8 !important;
                border-color: #93c5fd !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .pill-kelas {
                background: #eff6ff !important;
                color: #1d4ed8 !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            @page { 
                size: A4 portrait; 
                margin: 8mm 12mm; 
            }
        }
    </style>
</head>
<body>

<!-- Bar Kontrol Atas (Layar Saja) -->
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
                        <?= htmlspecialchars($ar['nama_ruang']) ?> (<?= (int)$ar['total_peserta'] ?> Label Meja)
                    </option>
                <?php endforeach; ?>
                <option value="all" <?= ($isAllRooms) ? 'selected' : '' ?>>
                    ⭐ Cetak Semua Ruangan (<?= $totalSemuaLabel ?> Label Sekaligus)
                </option>
            </select>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak Label Meja A4
        </button>
    </div>
</div>

<!-- Daftar Lembar Label per Ruangan -->
<?php foreach ($roomsData as $index => $rd): ?>
    <?php 
    $rInfo = $rd['ruang'];
    $mList = $rd['mejaList'];
    ?>
    <div class="page-sheet">
        <?php if ($isAllRooms): ?>
            <div class="room-header-title">
                <span style="font-size: 1rem; font-weight: 900; color: #0f172a;">
                    <i class="fa-solid fa-door-open" style="color: #2563eb;"></i> <?= htmlspecialchars($rInfo['nama_ruang']) ?> (<?= count($mList) ?> Meja)
                </span>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 700;">Halaman Ruang <?= $index + 1 ?> dari <?= count($roomsData) ?></span>
            </div>
        <?php endif; ?>

        <?php if (empty($mList)): ?>
            <div style="text-align: center; padding: 50px 20px; color: #94a3b8; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-chair" style="font-size: 2rem; margin-bottom: 10px; color: #cbd5e1; display: block;"></i>
                Belum ada siswa yang ditempatkan di kursi <b><?= htmlspecialchars($rInfo['nama_ruang']) ?></b>.<br>Silakan tata siswa di menu Denah Tempat Duduk.
            </div>
        <?php else: ?>
            <div class="label-grid">
                <?php foreach ($mList as $m): ?>
                    <div class="meja-card">
                        <!-- Top Bar: Logo & Nama Ruang BESAR -->
                        <div class="card-top">
                            <div class="card-brand">
                                <img src="<?= $logoUrl ?>" class="card-logo" onerror="this.src='/public/uploads/logo/logo.png'">
                                <div style="min-width: 0;">
                                    <div class="card-inst"><?= htmlspecialchars($namaInstitusi) ?></div>
                                    <div class="card-subinst">KARTU MEJA CBT</div>
                                </div>
                            </div>
                            <!-- NAMA RUANGAN BESAR -->
                            <div class="card-room-badge-large">
                                <i class="fa-solid fa-door-open" style="font-size: 0.8rem;"></i>
                                <?= htmlspecialchars($rInfo['nama_ruang']) ?>
                            </div>
                        </div>

                        <!-- Card Middle: Nomor Meja Besar + Sesi Info -->
                        <div class="card-middle">
                            <div class="middle-left-info">
                                <div style="font-size: 0.7rem; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 3px;">
                                    MEJA UJIAN RESMI
                                </div>
                                <div style="font-size: 0.65rem; color: #64748b; line-height: 1.3;">
                                    Silakan duduk sesuai nomor meja yang tertera pada kartu ini.
                                </div>
                            </div>

                            <!-- KOTAK NOMOR MEJA BESAR -->
                            <div class="big-meja-box">
                                <span class="big-meja-label">MEJA</span>
                                <span class="big-meja-num"><?= str_pad($m['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </div>

                        <!-- Identitas Peserta Ujian -->
                        <div class="siswa-section">
                            <div class="siswa-label">Peserta Ujian</div>
                            <div class="siswa-nama" title="<?= htmlspecialchars($m['nama_siswa']) ?>">
                                <?= htmlspecialchars($m['nama_siswa']) ?>
                            </div>
                            <div class="siswa-meta">
                                <span class="meta-pill pill-nis">
                                    NIS: <b><?= htmlspecialchars($m['nis'] ?: '-') ?></b>
                                </span>
                                <span class="meta-pill pill-kelas">
                                    Kelas: <b><?= htmlspecialchars($m['kelas_nama'] ?: 'X') ?></b>
                                </span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <span>ASESMEN CBT 2026/2027</span>
                            <span>✂ TEMPEL PADA MEJA</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
