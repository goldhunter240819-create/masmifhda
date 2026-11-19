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

$sisi = isset($_GET['sisi']) ? $_GET['sisi'] : 'kedua'; // 'kedua', 'depan', 'belakang'
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <title>Kartu Identitas Pengawas & Proktor CBT - <?= htmlspecialchars($namaInstitusi) ?></title>
    
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
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(5,150,105,0.3);
        }
        .btn-print:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .btn-back {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Container Kartu */
        .card-container {
            max-width: 1050px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }

        /* Wrapper 1 Peserta */
        .id-card-wrapper {
            display: flex;
            gap: 10px;
            page-break-inside: avoid;
            margin-bottom: 6px;
        }

        /* Dimensi Kartu Standar ISO 85.6mm x 53.98mm */
        .id-card-body {
            width: 85.6mm;
            height: 53.98mm;
            border-radius: 4.2mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Sisi Depan Header */
        .card-header-bar {
            background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
            color: #ffffff;
            padding: 2.2mm 3.5mm;
            display: flex;
            align-items: center;
            gap: 2.5mm;
            position: relative;
            z-index: 2;
            border-bottom: 2px solid #34d399;
        }
        .header-logo {
            width: 7.8mm;
            height: 7.8mm;
            object-fit: contain;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
            background: white;
            border-radius: 50%;
            padding: 0.8px;
        }

        /* Foto Pengawas Frame */
        .pengawas-photo-frame {
            width: 20mm;
            height: 25mm;
            border-radius: 2.5mm;
            border: 1.5px solid #059669;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ecfdf5;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            position: relative;
        }
        .pengawas-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Sisi Belakang */
        .card-back-body {
            background: #ffffff;
            padding: 3mm 4mm;
            display: flex;
            align-items: center;
            position: relative;
            border: 1px solid #cbd5e1;
            gap: 3.5mm;
        }
        .back-orb-1 {
            position: absolute;
            top: -12mm;
            right: -12mm;
            width: 32mm;
            height: 32mm;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
        }
        .back-orb-2 {
            position: absolute;
            bottom: -15mm;
            left: -15mm;
            width: 36mm;
            height: 36mm;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5,150,105,0.08) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
        }

        /* Kotak QR Code Tech Scanner */
        .tech-qr-box {
            background: #ffffff;
            border: 1.5px solid #059669;
            border-radius: 4px;
            padding: 1.2mm;
            position: relative;
            box-shadow: 0 2px 8px rgba(5,150,105,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .corner-tl, .corner-tr, .corner-bl, .corner-br {
            position: absolute;
            width: 3mm;
            height: 3mm;
            border: 1.5px solid #059669;
        }
        .corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
        .corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
        .corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .print-controls {
                display: none !important;
            }
            .card-container {
                gap: 5mm;
                margin: 0;
            }
            .id-card-wrapper {
                margin-bottom: 5mm;
            }
            .id-card-body {
                box-shadow: none !important;
                border: 0.5px solid #94a3b8 !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 6mm;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Bar -->
    <div class="print-controls">
        <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
            <div class="ctrl-group">
                <span class="ctrl-label">Pilihan Sisi Kartu</span>
                <select id="selSisi" class="ctrl-input" onchange="filterSisi(this.value)">
                    <option value="kedua" <?= $sisi === 'kedua' ? 'selected' : '' ?>>Kedua Sisi (Berdampingan / Lipat)</option>
                    <option value="depan" <?= $sisi === 'depan' ? 'selected' : '' ?>>Hanya Sisi Depan (Identitas)</option>
                    <option value="belakang" <?= $sisi === 'belakang' ? 'selected' : '' ?>>Hanya Sisi Belakang (QR Login)</option>
                </select>
            </div>
            <div class="ctrl-group">
                <span class="ctrl-label">Kota Pengesahan</span>
                <input type="text" id="inpKota" class="ctrl-input" value="<?= htmlspecialchars($kotaCetak) ?>" style="width: 130px;">
            </div>
            <div class="ctrl-group">
                <span class="ctrl-label">Tanggal Cetak</span>
                <input type="text" id="inpTgl" class="ctrl-input" value="<?= htmlspecialchars($tglCetak) ?>" style="width: 140px;">
            </div>
            <button type="button" onclick="terapkanPengaturan()" class="btn-ctrl btn-back">
                🔄 Terapkan
            </button>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="/cbt/peserta_khusus?tab=pengawas" class="btn-ctrl btn-back">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn-ctrl btn-print">
                🖨️ Cetak Kartu Pengawas (Print)
            </button>
        </div>
    </div>

    <!-- Container Cetak Kartu Pengawas -->
    <div class="card-container">
        <?php foreach ($pengawasList as $pkj): ?>
            <?php
            // QR TTD Digital Pengesahan Kepala Madrasah
            $qrTtdText = "KARTU PENGAWAS CBT RESMI - MAS MIFTAHUL HUDA\nTelah disahkan oleh Kepala Madrasah: {$namaKepala} di {$kotaCetak} pada {$tglCetak}.\nID Pengawas: KJ-" . str_pad($pkj['id'], 4, '0', STR_PAD_LEFT) . "\nNama: {$pkj['nama']}\nLokasi: " . ($pkj['lokasi_cabang'] ?: 'Cabang Kelas Jauh');

            // QR Login Pengawas via APK Camera Scan
            $qrLoginText = 'PENGAWAS_KJ:' . $pkj['id'] . ':' . $pkj['username'];
            ?>

            <div class="id-card-wrapper">
                <!-- ================= SISI DEPAN ================= -->
                <?php if ($sisi !== 'belakang'): ?>
                <div class="id-card-body">
                    <!-- Header Bar -->
                    <div class="card-header-bar">
                        <img src="<?= $logoUrl ?>" alt="Logo" class="header-logo">
                        <div style="text-align: left; line-height: 1.2; flex: 1; min-width: 0;">
                            <div style="font-weight: 800; font-size: 9.5px; letter-spacing: 0.8px; white-space: nowrap;">KARTU PENGAWAS & PROKTOR CBT</div>
                            <div style="font-weight: 700; font-size: 8px; opacity: 0.95; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($namaInstitusi) ?></div>
                        </div>
                    </div>

                    <!-- Body Depan: Foto Kiri, Data & QR TTD Kanan -->
                    <div style="flex: 1; padding: 2mm 3.8mm; display: flex; gap: 3mm; position: relative; z-index: 2;">
                        <!-- Foto Pengawas Frame -->
                        <div class="pengawas-photo-frame">
                            <div style="text-align: center; color: #059669;">
                                <div style="font-size: 1.5rem; line-height: 1;">🛡️</div>
                                <div style="font-size: 5.5px; font-weight: 900; margin-top: 1.5mm; letter-spacing: 0.5px; text-transform: uppercase;">PROKTOR</div>
                            </div>
                        </div>

                        <!-- Data & Pengesahan -->
                        <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                            <table style="width: 100%; font-size: 7.2px; border-collapse: collapse; line-height: 1.45;">
                                <tr>
                                    <td style="font-weight: 700; width: 17mm; color: #475569; vertical-align: top;">ID Pengawas</td>
                                    <td style="width: 2mm; vertical-align: top;">:</td>
                                    <td style="vertical-align: top;">
                                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #059669; background: #ecfdf5; padding: 0.5px 4px; border-radius: 2px; font-size: 7.2px; border: 0.5px solid #a7f3d0;">
                                            KJ-<?= str_pad($pkj['id'], 4, '0', STR_PAD_LEFT) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Nama Lengkap</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="vertical-align: top; max-width: 38mm; overflow: hidden; white-space: nowrap;">
                                        <div style="font-weight: 800; color: #0f172a; font-size: 7.8px; overflow: hidden; text-overflow: ellipsis;">
                                            <?= htmlspecialchars($pkj['nama']) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Jabatan / Role</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="font-weight: 800; color: #059669; vertical-align: top;">
                                        Pengawas / Proktor CBT
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #475569; vertical-align: top;">Lokasi Tugas</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td style="font-weight: 700; color: #1e293b; vertical-align: top;">
                                        <?= htmlspecialchars($pkj['lokasi_cabang'] ?: 'Cabang Kelas Jauh') ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- QR TTD + Titimangsa Kamad -->
                            <div style="display: flex; align-items: flex-end; gap: 2.5mm; border-top: 0.5px dashed #cbd5e1; padding-top: 1mm;">
                                <div id="qr-ttd-<?= $pkj['id'] ?>" style="width: 11.5mm; height: 11.5mm; flex-shrink: 0;"></div>
                                <script>
                                    new QRCode(document.getElementById("qr-ttd-<?= $pkj['id'] ?>"), {
                                        text: <?= json_encode($qrTtdText) ?>,
                                        width: 60,
                                        height: 60,
                                        correctLevel : QRCode.CorrectLevel.L
                                    });
                                    setTimeout(() => {
                                        let qrEl = document.getElementById("qr-ttd-<?= $pkj['id'] ?>");
                                        if (qrEl) {
                                            let img = qrEl.querySelector('img');
                                            if (img) img.style.width = '100%';
                                            let canvas = qrEl.querySelector('canvas');
                                            if (canvas) canvas.style.display = 'none';
                                        }
                                    }, 200);
                                </script>
                                <div style="font-size: 5.5px; line-height: 1.25; color: #475569;">
                                    <div><?= htmlspecialchars($kotaCetak) ?>, <?= htmlspecialchars($tglCetak) ?></div>
                                    <div style="font-weight: 700; color: #1e293b;">Kepala Madrasah,</div>
                                    <div style="font-weight: 800; color: #0f172a; margin-top: 0.8mm; font-size: 5.8px;"><?= htmlspecialchars($namaKepala) ?></div>
                                    <div>NIP. <?= htmlspecialchars($nipKepala) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Strip -->
                    <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.2mm 4mm; display: flex; justify-content: space-between; align-items: center; font-size: 5.2px; font-weight: 700; color: #64748b; z-index: 2;">
                        <span>OTORISASI: PORTAL PENGAWAS CBT</span>
                        <span>TA 2026/2027 &bull; STATUS: AKTIF</span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ================= SISI BELAKANG ================= -->
                <?php if ($sisi !== 'depan'): ?>
                <div class="id-card-body card-back-body">
                    <!-- Geometric Ornaments -->
                    <div class="back-orb-1"></div>
                    <div class="back-orb-2"></div>

                    <!-- Watermark Logo -->
                    <img src="<?= $logoUrl ?>" alt="Logo Watermark" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 25mm; height: 25mm; object-fit: contain; opacity: 0.04; z-index: 1; filter: grayscale(100%);">

                    <!-- Kiri: Tech QR Code Scanner -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 2; width: 30mm; flex-shrink: 0;">
                        <div class="tech-qr-box">
                            <div class="corner-tl"></div>
                            <div class="corner-tr"></div>
                            <div class="corner-bl"></div>
                            <div class="corner-br"></div>

                            <div id="qr-login-<?= $pkj['id'] ?>" style="width: 23mm; height: 23mm;"></div>
                            <script>
                                new QRCode(document.getElementById("qr-login-<?= $pkj['id'] ?>"), {
                                    text: <?= json_encode($qrLoginText) ?>,
                                    width: 100,
                                    height: 100,
                                    correctLevel: QRCode.CorrectLevel.M,
                                    colorDark: "#064e3b",
                                    colorLight: "#ffffff"
                                });
                                setTimeout(() => {
                                    let qrEl = document.getElementById("qr-login-<?= $pkj['id'] ?>");
                                    if (qrEl) {
                                        let img = qrEl.querySelector('img');
                                        if (img) img.style.width = '100%';
                                        let canvas = qrEl.querySelector('canvas');
                                        if (canvas) canvas.style.display = 'none';
                                    }
                                }, 200);
                            </script>
                        </div>
                        <div style="font-weight: 800; font-size: 5.5px; color: #059669; margin-top: 1.5mm; letter-spacing: 0.5px; text-transform: uppercase;">
                            Scan Kamera Login APK
                        </div>
                    </div>

                    <!-- Kanan: Dark Badge, Credential Box & Rules -->
                    <div style="flex: 1; z-index: 2; display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-width: 0;">
                        <div>
                            <div style="display: inline-block; background: linear-gradient(90deg, #064e3b 0%, #065f46 100%); color: white; padding: 1mm 3mm; border-radius: 3px; font-weight: 900; font-size: 7.2px; letter-spacing: 0.8px; border-left: 2.5px solid #10b981; box-shadow: 0 2px 6px rgba(6,78,59,0.15);">
                                KARTU PENGAWAS CBT CABANG
                            </div>
                            <div style="font-size: 5.8px; color: #475569; margin-top: 0.8mm; font-weight: 700; line-height: 1.3;">
                                OTORISASI AKSES & KREDENSIAL LOGIN
                            </div>
                        </div>

                        <!-- Credential Box -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 1.5mm 2.5mm; font-size: 6.2px; line-height: 1.4; color: #1e293b; margin: 0.8mm 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>User: <b style="font-family: 'JetBrains Mono', monospace; color: #065f46;"><?= htmlspecialchars($pkj['username']) ?></b></span>
                                <span>Pass: <b style="font-family: 'JetBrains Mono', monospace; color: #065f46;"><?= htmlspecialchars($pkj['password_plain'] ?: '123456') ?></b></span>
                            </div>
                            <div style="margin-top: 0.5mm; font-size: 5.5px; color: #059669; font-family: 'JetBrains Mono', monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                KODE QR: PENGAWAS_KJ:<?= $pkj['id'] ?>
                            </div>
                        </div>

                        <!-- Rules List -->
                        <div style="font-size: 5.2px; color: #475569; line-height: 1.35;">
                            <div>1. Arahkan kamera login APK Guru ke QR code samping untuk masuk cepat.</div>
                            <div>2. Berwenang menjalankan scanner absensi ruang kelas jauh & rekap nilai.</div>
                            <div>3. Berwenang membuka kunci pengerjaan jika siswa mengalami kendala.</div>
                        </div>

                        <!-- Disclaimer / Return Notice -->
                        <div style="font-size: 4.8px; color: #64748b; border-top: 0.5px dashed #cbd5e1; padding-top: 0.8mm; line-height: 1.25;">
                            Kartu tanda pengenal resmi proktor & pengawas CBT.<br>
                            <b style="color: #0f172a; font-size: 5.5px;"><?= htmlspecialchars($namaInstitusi) ?></b>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function filterSisi(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('sisi', val);
            window.location.href = url.toString();
        }

        function terapkanPengaturan() {
            const sisi = document.getElementById('selSisi').value;
            const kota = document.getElementById('inpKota').value.trim();
            const tgl = document.getElementById('inpTgl').value.trim();

            const url = new URL(window.location.href);
            url.searchParams.set('sisi', sisi);
            if (kota) url.searchParams.set('kota_cetak', kota);
            if (tgl) url.searchParams.set('tgl_cetak', tgl);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>