<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Peserta CBT Premium - MAS Miftahul Huda</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 20px;
        }
        .no-print-bar {
            max-width: 1000px;
            margin: 0 auto 20px;
            background: #ffffff;
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .btn-action {
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.85rem;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-print {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Container Print Sheet */
        .print-sheet {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        /* Card Container (Front & Back Pair) */
        .card-pair {
            display: flex;
            flex-direction: column;
            gap: 12px;
            page-break-inside: avoid;
            margin-bottom: 10px;
        }

        /* Single ID Card Dimension (Approx standard printable exam badge) */
        .id-card {
            width: 100%;
            height: 250px;
            border-radius: 16px;
            background: #ffffff;
            border: 1.5px solid #0284c7;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 12px 14px;
        }

        /* Watermark */
        .id-card::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 170px;
            height: 170px;
            background-image: url('<?php echo \App\Core\Branding::getInstitusiLogo(); ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.05;
            pointer-events: none;
            z-index: 1;
        }

        /* Card Header / Kop */
        .card-header-kop {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 6px;
            position: relative;
            z-index: 2;
        }
        .kop-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-text h4 {
            font-size: 0.85rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.2px;
        }
        .kop-text p {
            font-size: 0.62rem;
            color: #0284c7;
            font-weight: 800;
            margin: 1px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Front Content */
        .front-body {
            display: flex;
            gap: 12px;
            align-items: center;
            margin: 6px 0;
            position: relative;
            z-index: 2;
        }
        .student-photo-box {
            width: 70px;
            height: 92px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #f8fafc;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .student-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.72rem;
        }
        .student-info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            width: 72px;
        }
        .info-val {
            color: #0f172a;
            font-weight: 700;
        }
        .badge-nopes {
            font-family: 'JetBrains Mono', monospace;
            background: #e0f2fe;
            color: #0369a1;
            padding: 1px 6px;
            border-radius: 5px;
            font-weight: 800;
            font-size: 0.78rem;
            border: 1px solid #bae6fd;
            display: inline-block;
        }

        /* Front Footer / Signature */
        .front-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 0.62rem;
            color: #64748b;
            position: relative;
            z-index: 2;
            border-top: 1px dashed #e2e8f0;
            padding-top: 4px;
        }
        .sig-block {
            text-align: right;
            line-height: 1.25;
        }

        /* BACK SIDE STYLES */
        .card-back {
            border-color: #059669;
        }
        .card-back .card-header-kop {
            border-bottom-color: #059669;
        }
        .card-back .kop-text p {
            color: #059669;
        }
        .back-body {
            display: flex;
            gap: 12px;
            align-items: center;
            position: relative;
            z-index: 2;
            margin: 6px 0;
        }
        .qr-code-box {
            width: 90px;
            text-align: center;
            flex-shrink: 0;
        }
        .qr-code-img {
            width: 76px;
            height: 76px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: white;
        }
        .qr-code-sub {
            font-size: 0.58rem;
            color: #059669;
            font-weight: 800;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .back-instructions {
            flex: 1;
            font-size: 0.65rem;
            color: #334155;
            line-height: 1.45;
        }
        .back-instructions ol {
            margin: 0;
            padding-left: 14px;
        }
        .cred-badge-box {
            background: #f0fdf4;
            border: 1px dashed #86efac;
            border-radius: 8px;
            padding: 4px 8px;
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 0.68rem;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Side Badge Tag */
        .side-tag {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 0.6rem;
            font-weight: 900;
            padding: 2px 7px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .side-tag-front {
            background: #e0f2fe;
            color: #0284c7;
        }
        .side-tag-back {
            background: #dcfce7;
            color: #15803d;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .print-sheet {
                max-width: 100%;
                gap: 15px;
            }
            .id-card {
                box-shadow: none;
                border-color: #0f172a;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi Non-Print -->
    <div class="no-print-bar">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 900; color: #0f172a; margin: 0;">Kartu Peserta CBT Kelas Jauh (2 Sisi: Depan & Belakang)</h2>
            <p style="font-size: 0.8rem; color: #64748b; margin: 2px 0 0 0;">
                Dilengkapi Foto 3x4 Siswa, QR Code Login Cepat APK, Kredensial Cadangan, dan Tata Tertib CBT.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="/cbt/peserta-khusus" class="btn-action btn-back">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Kartu Sekarang (Print)
            </button>
        </div>
    </div>

    <!-- Sheet Cetak Kartu -->
    <div class="print-sheet">
        <?php foreach ($pesertaList as $p): ?>
            <?php
            $qrPayload = 'CBT_KJ:' . $p['id'] . ':' . ($p['qr_token'] ?: 'TOKEN');
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=4&data=' . urlencode($qrPayload);
            ?>
            <div class="card-pair">
                <!-- SISI DEPAN -->
                <div class="id-card">
                    <span class="side-tag side-tag-front">Sisi Depan</span>
                    
                    <div class="card-header-kop">
                        <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" class="kop-logo">
                        <div class="kop-text">
                            <h4>MAS MIFTAHUL HUDA</h4>
                            <p>KARTU PESERTA CBT ONLINE (KELAS JAUH)</p>
                        </div>
                    </div>

                    <div class="front-body">
                        <div class="student-photo-box">
                            <?php if (!empty($p['foto'])): ?>
                                <img src="/public/uploads/siswa/<?php echo htmlspecialchars($p['foto']); ?>" alt="Foto" onerror="this.onerror=null; this.parentNode.innerHTML='<span style=\'font-size:1.8rem;\'>🎓</span>';">
                            <?php else: ?>
                                <span style="font-size: 1.8rem;">🎓</span>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <table class="student-info-table">
                                <tr>
                                    <td class="info-label">No. Peserta</td>
                                    <td style="width: 8px;">:</td>
                                    <td class="info-val">
                                        <span class="badge-nopes"><?php echo htmlspecialchars($p['no_peserta']); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="info-label">Nama Siswa</td>
                                    <td>:</td>
                                    <td class="info-val"><?php echo htmlspecialchars($p['nama_siswa']); ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Kelas / Rombel</td>
                                    <td>:</td>
                                    <td class="info-val"><?php echo htmlspecialchars($p['tingkat']); ?> (<?php echo htmlspecialchars($p['kelas_nama']); ?>)</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Lokasi Ujian</td>
                                    <td>:</td>
                                    <td class="info-val"><?php echo htmlspecialchars($p['lokasi_cabang'] ?: 'Filial / Kelas Jauh'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="front-footer">
                        <div>
                            Tahun Ajaran <b>2026/2027 (Ganjil)</b>
                        </div>
                        <div class="sig-block">
                            Lampung Barat, <?php echo date('d F Y'); ?><br>
                            <b>Kepala Madrasah</b><br><br>
                            <b><u><?php echo htmlspecialchars($inst['kepala_madrasah'] ?? 'ULFAH, S.Pd.I'); ?></u></b>
                        </div>
                    </div>
                </div>

                <!-- SISI BELAKANG -->
                <div class="id-card card-back">
                    <span class="side-tag side-tag-back">Sisi Belakang</span>

                    <div class="card-header-kop">
                        <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" class="kop-logo">
                        <div class="kop-text">
                            <h4>AKSES CEPAT & TATA TERTIB CBT</h4>
                            <p>PETUNJUK MASUK & KREDENSIAL CADANGAN</p>
                        </div>
                    </div>

                    <div class="back-body">
                        <div class="qr-code-box">
                            <img src="<?php echo $qrUrl; ?>" alt="QR Login" class="qr-code-img">
                            <div class="qr-code-sub">Scan Kamera APK</div>
                        </div>
                        <div class="back-instructions">
                            <ol>
                                <li>Arahkan pemindai kamera login APK Siswa ke QR code di samping untuk masuk cepat tanpa ketik password.</li>
                                <li>Dilarang berpindah tab / aplikasi browser saat ujian berlangsung (toleransi maks 3x sebelum auto-submit).</li>
                                <li>Jika terjadi kendala login atau putus koneksi, gunakan kredensial darurat di bawah ini:</li>
                            </ol>
                            <div class="cred-badge-box">
                                <div>User: <b><?php echo htmlspecialchars($p['username']); ?></b></div>
                                <div>Pass: <b><?php echo htmlspecialchars($p['password_plain'] ?: '123456'); ?></b></div>
                            </div>
                        </div>
                    </div>

                    <div class="front-footer" style="color: #059669; border-top-color: #bbf7d0;">
                        <div>
                            Portal CBT: <b>apk.masmifhda.sch.id</b>
                        </div>
                        <div style="font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 0.62rem;">
                            TOKEN: <?php echo htmlspecialchars($p['qr_token'] ?: 'KJ-' . $p['id']); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
