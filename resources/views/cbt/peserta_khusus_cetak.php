<?php
$bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglCetakDefault = date('d') . ' ' . ($bulanIndo[(int)date('n')] ?? date('F')) . ' ' . date('Y');
$tglCetak = !empty($_GET['tgl_cetak']) ? htmlspecialchars($_GET['tgl_cetak']) : $tglCetakDefault;
$kotaCetak = !empty($_GET['kota_cetak']) ? htmlspecialchars($_GET['kota_cetak']) : (!empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat');

$namaKepala = 'Drs. Hamim Hasyim';
if (!empty($inst['nama_kepala']) && stripos($inst['nama_kepala'], 'Ulfah') === false) {
    $namaKepala = $inst['nama_kepala'];
}
$nipKepala = !empty($inst['nip_kepala']) ? $inst['nip_kepala'] : '-';
$namaInstitusi = !empty($inst['nama']) ? $inst['nama'] : (!empty($inst['nama_institusi']) ? $inst['nama_institusi'] : 'MAS MIFTAHUL HUDA');
$logoUrl = \App\Core\Branding::getInstitusiLogo();
$faviconUrl = \App\Core\Branding::getInstitusiFavicon();

$sisi = isset($_GET['sisi']) ? $_GET['sisi'] : 'kedua'; // 'kedua' (lipat), 'berdampingan', 'depan', 'belakang'
$tingkat = isset($_GET['tingkat']) ? $_GET['tingkat'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <title>Kartu Peserta CBT Online - <?= htmlspecialchars($namaInstitusi) ?></title>
    
    <link rel="icon" type="image/png" href="<?= $faviconUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <script src="<?= base_url('public/assets/js/qrcode.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/lucide.min.js') ?>"></script>

    <style>
        :root {
            color-scheme: light;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            margin: 0;
            padding: 20px;
            background: #f1f5f9 !important;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
        }

        /* Top Action Bar (Non-Print) */
        .print-controls {
            max-width: 1050px;
            margin: 0 auto 20px;
            background: #ffffff;
            padding: 16px 22px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            border: 1px solid #e2e8f0;
        }
        .ctrl-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .ctrl-label {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ctrl-input {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.85rem;
            color: #0f172a;
            outline: none;
            background: white;
        }
        .btn-ctrl {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.86rem;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-print {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.45);
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

        /* Container Kartu */
        .card-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 6mm;
            max-width: 1050px;
            margin: 0 auto;
        }

        /* Wrapper Sepasang Kartu (Depan + Belakang) */
        .id-card-wrapper {
            display: flex;
            <?php if ($sisi === 'berdampingan'): ?>
                flex-direction: row;
                gap: 4mm;
            <?php else: ?>
                flex-direction: column;
                gap: 0;
            <?php endif; ?>
            page-break-inside: avoid;
            align-items: center;
        }

        <?php if ($sisi === 'kedua'): ?>
        /* Sisi belakang diputar 180 derajat agar saat dilipat ke belakang posisinya tegak lurus sempurna */
        .id-card-wrapper > .id-card-body:nth-child(2) {
            transform: rotate(180deg);
        }
        <?php endif; ?>

        /* Standar ISO ID-1 Card (85.6mm x 54mm) */
        .id-card-body {
            box-sizing: border-box;
            width: 85.6mm;
            height: 54mm;
            border-radius: 3mm;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            position: relative;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.12) 0%, transparent 60%), radial-gradient(circle at 0% 100%, rgba(2, 132, 199, 0.06) 0%, transparent 50%), #ffffff;
            flex-shrink: 0;
            page-break-inside: avoid;
        }

        /* Header Bar Sisi Depan */
        .card-header-bar {
            height: 12.5mm;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.25' d='M0,128L48,144C96,160,192,192,288,197.3C384,203,480,181,576,149.3C672,117,768,75,864,64C960,53,1056,75,1152,106.7C1248,139,1344,181,1392,202.7L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E"), linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            padding: 0 4mm;
            color: white;
            border-bottom: 1.5px solid #0369a1;
            flex-shrink: 0;
            z-index: 2;
            position: relative;
        }
        .header-logo {
            width: 10mm;
            height: 10mm;
            object-fit: contain;
            margin-right: 3mm;
            flex-shrink: 0;
            background: white;
            border-radius: 50%;
            padding: 1px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        /* Foto Siswa 3x4 Pro */
        .student-photo-frame {
            width: 22mm;
            flex-shrink: 0;
            border: 1px solid #cbd5e1;
            border-radius: 1mm;
            overflow: hidden;
            padding: 0.8mm;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
            align-self: stretch;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .student-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5mm;
        }

        /* Sisi Belakang */
        .card-back-body {
            background: #ffffff;
            position: relative;
            overflow: hidden;
            color: #0f172a;
            padding: 3mm 4mm;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 3mm;
        }
        .back-orb-1 {
            position: absolute;
            top: -10mm;
            right: -10mm;
            width: 45mm;
            height: 45mm;
            background: linear-gradient(135deg, rgba(14,165,233,0.18) 0%, rgba(14,165,233,0) 100%);
            border-radius: 50%;
            z-index: 1;
        }
        .back-orb-2 {
            position: absolute;
            bottom: -15mm;
            left: -15mm;
            width: 55mm;
            height: 55mm;
            background: linear-gradient(45deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0) 100%);
            border-radius: 50%;
            z-index: 1;
        }

        /* Tech QR Corner Box */
        .tech-qr-box {
            background: white;
            padding: 2mm;
            border-radius: 4px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            position: relative;
            display: inline-block;
        }
        .corner-tl { position: absolute; top: -1px; left: -1px; width: 5px; height: 5px; border-top: 2px solid #0284c7; border-left: 2px solid #0284c7; }
        .corner-tr { position: absolute; top: -1px; right: -1px; width: 5px; height: 5px; border-top: 2px solid #0284c7; border-right: 2px solid #0284c7; }
        .corner-bl { position: absolute; bottom: -1px; left: -1px; width: 5px; height: 5px; border-bottom: 2px solid #0284c7; border-left: 2px solid #0284c7; }
        .corner-br { position: absolute; bottom: -1px; right: -1px; width: 5px; height: 5px; border-bottom: 2px solid #0284c7; border-right: 2px solid #0284c7; }

        /* Media Print */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-controls {
                display: none !important;
            }
            .card-container {
                gap: 0 !important;
            }
            .id-card-wrapper {
                box-shadow: none !important;
                margin: 0 !important;
            }
            .id-card-body {
                box-shadow: none !important;
                border: 0.1mm solid #cbd5e1 !important; /* Batas potong kartu halus */
            }
            @page {
                size: A4 landscape;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>

    <script>
    function updateConfig() {
        var sisi = document.getElementById('f_sisi').value;
        var kota = document.getElementById('f_kota').value;
        var tgl = document.getElementById('f_tgl').value;
        var tingkat = document.getElementById('f_tingkat').value;

        var url = window.location.pathname + '?sisi=' + encodeURIComponent(sisi) + 
                  '&kota_cetak=' + encodeURIComponent(kota) + 
                  '&tgl_cetak=' + encodeURIComponent(tgl);
        if (tingkat) {
            url += '&tingkat=' + encodeURIComponent(tingkat);
        }
        window.location.href = url;
    }
    </script>

    <!-- Top Action Bar -->
    <div class="print-controls">
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div class="ctrl-group">
                <label class="ctrl-label">Filter Tingkat</label>
                <select id="f_tingkat" onchange="updateConfig()" class="ctrl-input">
                    <option value="">Semua Tingkat</option>
                    <option value="Kelas X" <?= $tingkat === 'Kelas X' ? 'selected' : '' ?>>Kelas X</option>
                    <option value="Kelas XI" <?= $tingkat === 'Kelas XI' ? 'selected' : '' ?>>Kelas XI</option>
                    <option value="Kelas XII" <?= $tingkat === 'Kelas XII' ? 'selected' : '' ?>>Kelas XII</option>
                </select>
            </div>

            <div class="ctrl-group">
                <label class="ctrl-label">Pilihan Sisi</label>
                <select id="f_sisi" onchange="updateConfig()" class="ctrl-input">
                    <option value="kedua" <?= $sisi === 'kedua' ? 'selected' : '' ?>>Kedua Sisi (Lipat Atas-Bawah)</option>
                    <option value="berdampingan" <?= $sisi === 'berdampingan' ? 'selected' : '' ?>>Berdampingan (Kiri-Kanan)</option>
                    <option value="depan" <?= $sisi === 'depan' ? 'selected' : '' ?>>Depan Saja</option>
                    <option value="belakang" <?= $sisi === 'belakang' ? 'selected' : '' ?>>Belakang Saja</option>
                </select>
            </div>

            <div class="ctrl-group">
                <label class="ctrl-label">Kota Titimangsa</label>
                <input type="text" id="f_kota" value="<?= htmlspecialchars($kotaCetak) ?>" onchange="updateConfig()" class="ctrl-input" style="width: 130px;">
            </div>

            <div class="ctrl-group">
                <label class="ctrl-label">Tanggal Cetak</label>
                <input type="text" id="f_tgl" value="<?= htmlspecialchars($tglCetak) ?>" onchange="updateConfig()" class="ctrl-input" style="width: 150px;">
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="/cbt/peserta-khusus" class="btn-ctrl btn-back">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn-ctrl btn-print">
                🖨️ Cetak Kartu (Print)
            </button>
        </div>
    </div>

    <!-- Container Cetak Kartu Peserta -->
    <div class="card-container">
        <?php foreach ($pesertaList as $p): ?>
            <?php
            $fotoUrl = '';
            if (!empty($p['foto'])) {
                $fotoUrl = '/public/uploads/siswa/' . $p['foto'];
            }

            // QR TTD Digital Pengesahan Kepala Madrasah
            $qrTtdText = "KARTU PESERTA CBT RESMI - MAS MIFTAHUL HUDA\nTelah disahkan oleh Kepala Madrasah: {$namaKepala} di {$kotaCetak} pada {$tglCetak}.\nNomor Peserta: {$p['no_peserta']}\nNama: {$p['nama_siswa']}";

            // QR Absen / Login Siswa via APK Camera Scan
            $qrLoginText = 'CBT_KJ:' . $p['id'] . ':' . ($p['qr_token'] ?: 'TOKEN');
            ?>

            <div class="id-card-wrapper">
                <!-- ================= SISI DEPAN ================= -->
                <?php if ($sisi !== 'belakang'): ?>
                <div class="id-card-body">
                    <!-- Header Bar -->
                    <div class="card-header-bar">
                        <img src="<?= $logoUrl ?>" alt="Logo" class="header-logo">
                        <div style="text-align: left; line-height: 1.2; flex: 1; min-width: 0;">
                            <div style="font-weight: 800; font-size: 10px; letter-spacing: 1px; white-space: nowrap;">KARTU PESERTA CBT ONLINE</div>
                            <div style="font-weight: 700; font-size: 8.5px; opacity: 0.95; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($namaInstitusi) ?></div>
                        </div>
                    </div>

                    <!-- Body Depan: Foto Kiri, Data & QR TTD Kanan -->
                    <div style="flex: 1; padding: 2mm 4mm; display: flex; gap: 3mm; position: relative; z-index: 2;">
                        <!-- Foto Siswa 3x4 Frame -->
                        <div class="student-photo-frame">
                            <?php if (!empty($fotoUrl)): ?>
                                <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto" onerror="this.onerror=null; this.parentNode.innerHTML='<span style=\'font-size:1.6rem;\'>🎓</span>';">
                            <?php else: ?>
                                <span style="font-size: 1.6rem; color: #94a3b8;">🎓</span>
                            <?php endif; ?>
                        </div>

                        <!-- Data & Pengesahan -->
                        <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                            <table style="width: 100%; font-size: 7.5px; border-collapse: collapse; line-height: 1.5;">
                                <tr>
                                    <td style="font-weight: 700; width: 17mm; color: #475569; vertical-align: top;">No. Peserta</td>
                                    <td style="width: 2mm; vertical-align: top;">:</td>
                                    <td style="vertical-align: top;">
                                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 0.5px 4px; border-radius: 2px; font-size: 7.5px; border: 0.5px solid #bae6fd;">
                                            <?= htmlspecialchars($p['no_peserta']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Nama Siswa</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="vertical-align: top; max-width: 38mm; overflow: hidden; white-space: nowrap;">
                                        <div style="font-weight: 800; color: #0f172a; font-size: 8px; overflow: hidden; text-overflow: ellipsis;">
                                            <?= htmlspecialchars($p['nama_siswa']) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Kelas / Rombel</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="font-weight: 700; color: #1e293b; vertical-align: top;">
                                        <?= htmlspecialchars($p['tingkat']) ?> (<?= htmlspecialchars($p['kelas_nama']) ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Lokasi Ujian</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="font-weight: 700; color: #1e293b; vertical-align: top;">
                                        <?= htmlspecialchars($p['lokasi_cabang'] ?: 'Filial / Kelas Jauh') ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- QR TTD + Titimangsa Kamad -->
                            <div style="display: flex; align-items: flex-end; gap: 2.5mm; border-top: 0.5px dashed #cbd5e1; padding-top: 1mm;">
                                <div id="qr-ttd-<?= $p['id'] ?>" style="width: 12mm; height: 12mm; flex-shrink: 0;"></div>
                                <script>
                                    new QRCode(document.getElementById("qr-ttd-<?= $p['id'] ?>"), {
                                        text: <?= json_encode($qrTtdText) ?>,
                                        width: 60,
                                        height: 60,
                                        correctLevel : QRCode.CorrectLevel.L
                                    });
                                    setTimeout(() => {
                                        let qrEl = document.getElementById("qr-ttd-<?= $p['id'] ?>");
                                        if (qrEl) {
                                            let img = qrEl.querySelector('img');
                                            if (img) img.style.width = '100%';
                                            let canvas = qrEl.querySelector('canvas');
                                            if (canvas) canvas.style.display = 'none';
                                        }
                                    }, 200);
                                </script>

                                <div style="text-align: left; line-height: 1.25;">
                                    <div style="font-size: 5px; color: #475569;">
                                        <?= htmlspecialchars($kotaCetak) ?>, <?= htmlspecialchars($tglCetak) ?><br>
                                        Mengetahui, Kepala Madrasah
                                    </div>
                                    <div style="font-size: 5.8px; font-weight: 800; color: #0f172a; margin-top: 1.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.3px;">
                                        <?= htmlspecialchars($namaKepala) ?>
                                    </div>
                                    <div style="font-size: 5px; color: #475569; margin-top: 0.3mm;">
                                        NIP. <?= htmlspecialchars($nipKepala) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Accent Line -->
                    <div style="height: 1.5mm; background: #0284c7; width: 100%; flex-shrink: 0; position: relative; z-index: 2;"></div>
                </div>
                <?php endif; ?>

                <!-- ================= SISI BELAKANG ================= -->
                <?php if ($sisi !== 'depan'): ?>
                <div class="id-card-body card-back-body">
                    <!-- Geometric Ornaments -->
                    <div class="back-orb-1"></div>
                    <div class="back-orb-2"></div>
                    <div style="position: absolute; top: 50%; right: 4mm; transform: translateY(-50%) rotate(45deg); width: 18mm; height: 18mm; border: 1.5px solid rgba(2,132,199,0.12); z-index: 1;"></div>

                    <!-- Watermark Logo -->
                    <img src="<?= $logoUrl ?>" alt="Logo Watermark" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 25mm; height: 25mm; object-fit: contain; opacity: 0.04; z-index: 1; filter: grayscale(100%);">

                    <!-- Kiri: Tech QR Code Scanner -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 2; width: 30mm; flex-shrink: 0;">
                        <div class="tech-qr-box">
                            <div class="corner-tl"></div>
                            <div class="corner-tr"></div>
                            <div class="corner-bl"></div>
                            <div class="corner-br"></div>

                            <div id="qr-login-<?= $p['id'] ?>" style="width: 24mm; height: 24mm;"></div>
                            <script>
                                new QRCode(document.getElementById("qr-login-<?= $p['id'] ?>"), {
                                    text: <?= json_encode($qrLoginText) ?>,
                                    width: 100,
                                    height: 100,
                                    correctLevel: QRCode.CorrectLevel.M,
                                    colorDark: "#0f172a",
                                    colorLight: "#ffffff"
                                });
                                setTimeout(() => {
                                    let qrEl = document.getElementById("qr-login-<?= $p['id'] ?>");
                                    if (qrEl) {
                                        let img = qrEl.querySelector('img');
                                        if (img) img.style.width = '100%';
                                        let canvas = qrEl.querySelector('canvas');
                                        if (canvas) canvas.style.display = 'none';
                                    }
                                }, 200);
                            </script>
                        </div>
                        <div style="font-weight: 800; font-size: 5.5px; color: #0284c7; margin-top: 1.5mm; letter-spacing: 0.5px; text-transform: uppercase;">
                            Scan Kamera APK Siswa
                        </div>
                    </div>

                    <!-- Kanan: Dark Badge, Credential Box & Rules -->
                    <div style="flex: 1; z-index: 2; display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-width: 0;">
                        <div>
                            <div style="display: inline-block; background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%); color: white; padding: 1mm 3mm; border-radius: 3px; font-weight: 900; font-size: 7.5px; letter-spacing: 1px; border-left: 2.5px solid #0284c7; box-shadow: 0 2px 6px rgba(15,23,42,0.15);">
                                KARTU PESERTA CBT ONLINE
                            </div>
                            <div style="font-size: 6px; color: #475569; margin-top: 1mm; font-weight: 700; line-height: 1.3;">
                                PETUNJUK MASUK & KREDENSIAL CADANGAN
                            </div>
                        </div>

                        <!-- Credential Box -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 1.5mm 2.5mm; font-size: 6.2px; line-height: 1.4; color: #1e293b; margin: 1mm 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>User: <b style="font-family: 'JetBrains Mono', monospace; color: #0f172a;"><?= htmlspecialchars($p['username']) ?></b></span>
                                <span>Pass: <b style="font-family: 'JetBrains Mono', monospace; color: #0f172a;"><?= htmlspecialchars($p['password_plain'] ?: '123456') ?></b></span>
                            </div>
                            <div style="margin-top: 0.5mm; font-size: 5.5px; color: #64748b; font-family: 'JetBrains Mono', monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                TOKEN: <?= htmlspecialchars($p['qr_token'] ?: 'KJ-' . $p['id']) ?>
                            </div>
                        </div>

                        <!-- Brief CBT Rules -->
                        <div style="font-size: 5.2px; color: #475569; line-height: 1.35;">
                            <div>1. Arahkan kamera login APK ke QR code samping untuk masuk cepat.</div>
                            <div>2. Dilarang berpindah aplikasi selama ujian (maks. 3x toleransi).</div>
                            <div>3. Seluruh jawaban tersimpan otomatis ke cloud CBT server.</div>
                        </div>

                        <!-- Return Notice -->
                        <div style="font-size: 4.8px; color: #64748b; border-top: 0.5px dashed #cbd5e1; padding-top: 0.8mm; line-height: 1.25;">
                            Bila menemukan kartu ini, mohon mengembalikan ke:<br>
                            <b style="color: #0f172a; font-size: 5.5px;"><?= htmlspecialchars($namaInstitusi) ?></b>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
