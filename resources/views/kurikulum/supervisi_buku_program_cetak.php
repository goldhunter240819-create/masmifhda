<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Program Supervisi <?= htmlspecialchars($ta) ?></title>
    <!-- Google Fonts for Luxury Academic & Certificate Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 20mm 15mm; }
        @page :first { margin: 8mm 8mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; background: #e2e8f0; margin: 0; padding: 25px 0 60px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .document-wrapper { max-width: 210mm; margin: 0 auto; }
        .page-break { page-break-before: always; }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        /* ══════════════════════════════════════════════════════
           LUXURY ACADEMIC COVER WITH ORNAMENTAL FRAME & BORDERS
           ══════════════════════════════════════════════════════ */
        .cover-page {
            width: 100%;
            height: 280mm;
            max-height: 280mm;
            page-break-after: always;
            page-break-inside: avoid;
            background: #ffffff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
            border-radius: 4px;
            margin-bottom: 30px;
            padding: 8px;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Outer Frame Border */
        .cover-frame-outer {
            width: 100%;
            height: 100%;
            border: 3.5px solid #065f46;
            padding: 5px;
            position: relative;
            box-sizing: border-box;
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }

        /* Inner Frame Border */
        .cover-frame-inner {
            width: 100%;
            height: 100%;
            border: 1.5px solid #c59b27;
            position: relative;
            box-sizing: border-box;
            padding: 22px 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: radial-gradient(circle at 50% 32%, rgba(6, 95, 70, 0.025) 0%, rgba(255,255,255,0) 75%);
        }

        /* SVG Corner Ornaments */
        .corner-ornament {
            position: absolute;
            width: 58px;
            height: 58px;
            z-index: 10;
            pointer-events: none;
        }
        .corner-tl { top: -2px; left: -2px; }
        .corner-tr { top: -2px; right: -2px; transform: scaleX(-1); }
        .corner-bl { bottom: -2px; left: -2px; transform: scaleY(-1); }
        .corner-br { bottom: -2px; right: -2px; transform: scale(-1, -1); }

        /* Header Section */
        .cover-header { width: 100%; margin-top: 2px; }
        .header-super { font-family: 'Cinzel', 'Times New Roman', serif; font-size: 10.5pt; font-weight: 700; letter-spacing: 2.5px; color: #065f46; margin-bottom: 2px; text-transform: uppercase; }
        .header-doc-type { font-family: 'Cinzel', serif; font-size: 8.5pt; font-weight: 600; letter-spacing: 3px; color: #c59b27; margin-bottom: 6px; text-transform: uppercase; }
        .header-main-title { font-family: 'Cinzel', 'Times New Roman', serif; font-size: 25pt; font-weight: 900; letter-spacing: 2px; color: #0f172a; margin: 0 0 3px 0; line-height: 1.15; }
        .header-sub-title { font-family: 'Cinzel', 'Times New Roman', serif; font-size: 14.5pt; font-weight: 700; letter-spacing: 1.5px; color: #065f46; margin: 0; text-transform: uppercase; }

        /* Top Line Ornament Divider */
        .ornament-divider { display: flex; align-items: center; justify-content: center; width: 78%; margin: 10px auto 4px; gap: 8px; }
        .orn-line-left, .orn-line-right { flex: 1; height: 1.5px; position: relative; }
        .orn-line-left { background: linear-gradient(90deg, transparent, #065f46 60%, #c59b27 100%); }
        .orn-line-right { background: linear-gradient(90deg, #c59b27, #065f46 40%, transparent 100%); }
        .orn-line-left::after, .orn-line-right::after { content: ''; position: absolute; top: -2px; left: 0; right: 0; height: 0.8px; background: inherit; opacity: 0.4; }
        .orn-center-icon { display: inline-flex; align-items: center; justify-content: center; }

        /* Central Logo Area */
        .cover-logo-area { margin: 6px 0; display: flex; justify-content: center; align-items: center; }
        .logo-crest-ring { position: relative; padding: 8px; border-radius: 50%; background: #ffffff; box-shadow: 0 0 0 2px rgba(197, 155, 39, 0.45), 0 0 0 5px rgba(6, 95, 70, 0.15), 0 8px 25px rgba(0,0,0,0.08); display: inline-flex; align-items: center; justify-content: center; }
        .cover-logo { width: 145px; height: auto; max-height: 155px; object-fit: contain; display: block; filter: drop-shadow(0 3px 6px rgba(0,0,0,0.1)); }

        /* Document Title Plaque */
        .title-plaque-wrap { width: 100%; display: flex; justify-content: center; margin: 4px 0 8px; }
        .title-plaque-box { position: relative; background: linear-gradient(135deg, rgba(6,95,70,0.03), rgba(197,155,39,0.06), rgba(6,95,70,0.03)); border: 1.5px solid #065f46; outline: 1px solid #c59b27; outline-offset: 3px; border-radius: 4px; padding: 9px 24px; width: 90%; box-shadow: 0 2px 10px rgba(6,95,70,0.05); }
        .plaque-corner { position: absolute; font-size: 11px; color: #c59b27; line-height: 1; }
        .plaque-tl { top: -6px; left: -6px; }
        .plaque-tr { top: -6px; right: -6px; }
        .plaque-bl { bottom: -6px; left: -6px; }
        .plaque-br { bottom: -6px; right: -6px; }
        .plaque-badge { font-family: 'Cinzel', serif; font-size: 8pt; font-weight: 700; letter-spacing: 2px; color: #065f46; margin-bottom: 2px; }
        .plaque-title-text { font-family: 'Times New Roman', serif; font-size: 14.5pt; font-weight: bold; letter-spacing: 0.6px; text-transform: uppercase; color: #0f172a; line-height: 1.3; }

        /* Madrasah Identity Block */
        .cover-madrasah-block { width: 100%; }
        .madrasah-main-name { font-family: 'Cinzel', 'Times New Roman', serif; font-size: 21pt; font-weight: 800; letter-spacing: 1.5px; color: #0f172a; margin: 0 0 4px 0; text-transform: uppercase; }
        .madrasah-pills { display: flex; align-items: center; justify-content: center; gap: 10px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 8.5pt; font-weight: 600; color: #475569; letter-spacing: 0.5px; margin-bottom: 8px; }
        .pill-sep { color: #c59b27; font-size: 11pt; }
        .status-active { color: #065f46; font-weight: 700; }
        .academic-ribbon { display: inline-block; background: #f0fdf4; border: 1px solid #86efac; color: #065f46; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 9.5pt; font-weight: 700; letter-spacing: 0.8px; padding: 4px 18px; border-radius: 20px; box-shadow: 0 1px 3px rgba(6,95,70,0.08); }

        /* Bottom Section & Author */
        .cover-footer-block { width: 100%; }
        .ornament-divider-sm { display: flex; align-items: center; justify-content: center; width: 65%; margin: 6px auto 10px; gap: 8px; }
        .orn-star-icon { font-size: 8.5pt; color: #c59b27; letter-spacing: 3px; }
        .author-wrapper { margin-bottom: 8px; }
        .author-label { font-family: 'Cinzel', serif; font-size: 8.5pt; font-weight: 700; letter-spacing: 2px; color: #64748b; margin-bottom: 2px; }
        .author-headline { font-family: 'Times New Roman', serif; font-size: 13.5pt; font-weight: bold; color: #0f172a; letter-spacing: 0.4px; }
        .institution-contact-footer { font-family: 'Times New Roman', serif; font-size: 9.5pt; color: #475569; line-height: 1.45; border-top: 1px solid #e2e8f0; padding-top: 8px; width: 85%; margin: 0 auto; }
        .footer-yayasan { font-weight: bold; font-size: 9.5pt; color: #065f46; letter-spacing: 0.8px; margin-bottom: 1px; text-transform: uppercase; }
        .footer-alamat { margin-bottom: 2px; }
        .footer-contact-row { font-size: 9pt; color: #64748b; }
        .contact-sep { color: #c59b27; margin: 0 4px; }

        .page-content-wrapper { background: #fff; box-shadow: 0 12px 35px rgba(0,0,0,0.12); border-radius: 4px; padding: 22mm 18mm; margin-bottom: 30px; }
        
        .chapter-title { text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 20px; text-transform: uppercase; }
        .section-title { font-weight: bold; font-size: 14pt; margin-top: 20px; margin-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; text-align: center; }
        
        .signature-area { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-box { width: 45%; text-align: center; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 80px; }
        
        /* Floating Modern Print Button */
        .btn-print { position: fixed; bottom: 25px; right: 25px; padding: 12px 24px; background: linear-gradient(135deg, #059669, #047857); color: #fff; border: none; border-radius: 50px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35); display: flex; align-items: center; gap: 8px; transition: all 0.2s ease; z-index: 9999; }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(5, 150, 105, 0.45); background: linear-gradient(135deg, #10b981, #059669); }

        @media print {
            body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
            .document-wrapper { max-width: 100% !important; margin: 0 !important; }
            .cover-page { width: 100% !important; height: 278mm !important; max-height: 278mm !important; box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; padding: 4px !important; page-break-after: always !important; page-break-inside: avoid !important; }
            .page-content-wrapper { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; margin: 0 !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <button class="no-print btn-print" onclick="window.print()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Cetak Buku Program
    </button>

    <div class="document-wrapper">
        <!-- ══════════════════════════════════════════════════════
             COVER PAGE DENGAN FRAME, ORNAMEN GARIS & CORNER BORDER
             ══════════════════════════════════════════════════════ -->
        <div class="cover-page">
            <div class="cover-frame-outer">
                <!-- 4 SVG Corner Ornaments -->
                <div class="corner-ornament corner-tl">
                    <svg viewBox="0 0 70 70" width="58" height="58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 0,24 L 0,6 Q 0,0 6,0 L 24,0" stroke="#065f46" stroke-width="3" fill="none"/>
                        <path d="M 30,0 L 70,0" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 0,30 L 0,70" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 6,32 L 6,10 Q 6,6 10,6 L 32,6" stroke="#c59b27" stroke-width="1.2" fill="none"/>
                        <path d="M 38,6 L 70,6" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <path d="M 6,38 L 6,70" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <circle cx="17" cy="17" r="4.5" fill="#065f46"/>
                        <circle cx="17" cy="17" r="2.2" fill="#c59b27"/>
                        <path d="M 17,8 L 19,15 L 26,17 L 19,19 L 17,26 L 15,19 L 8,17 L 15,15 Z" fill="#c59b27"/>
                    </svg>
                </div>
                <div class="corner-ornament corner-tr">
                    <svg viewBox="0 0 70 70" width="58" height="58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 0,24 L 0,6 Q 0,0 6,0 L 24,0" stroke="#065f46" stroke-width="3" fill="none"/>
                        <path d="M 30,0 L 70,0" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 0,30 L 0,70" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 6,32 L 6,10 Q 6,6 10,6 L 32,6" stroke="#c59b27" stroke-width="1.2" fill="none"/>
                        <path d="M 38,6 L 70,6" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <path d="M 6,38 L 6,70" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <circle cx="17" cy="17" r="4.5" fill="#065f46"/>
                        <circle cx="17" cy="17" r="2.2" fill="#c59b27"/>
                        <path d="M 17,8 L 19,15 L 26,17 L 19,19 L 17,26 L 15,19 L 8,17 L 15,15 Z" fill="#c59b27"/>
                    </svg>
                </div>
                <div class="corner-ornament corner-bl">
                    <svg viewBox="0 0 70 70" width="58" height="58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 0,24 L 0,6 Q 0,0 6,0 L 24,0" stroke="#065f46" stroke-width="3" fill="none"/>
                        <path d="M 30,0 L 70,0" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 0,30 L 0,70" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 6,32 L 6,10 Q 6,6 10,6 L 32,6" stroke="#c59b27" stroke-width="1.2" fill="none"/>
                        <path d="M 38,6 L 70,6" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <path d="M 6,38 L 6,70" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <circle cx="17" cy="17" r="4.5" fill="#065f46"/>
                        <circle cx="17" cy="17" r="2.2" fill="#c59b27"/>
                        <path d="M 17,8 L 19,15 L 26,17 L 19,19 L 17,26 L 15,19 L 8,17 L 15,15 Z" fill="#c59b27"/>
                    </svg>
                </div>
                <div class="corner-ornament corner-br">
                    <svg viewBox="0 0 70 70" width="58" height="58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 0,24 L 0,6 Q 0,0 6,0 L 24,0" stroke="#065f46" stroke-width="3" fill="none"/>
                        <path d="M 30,0 L 70,0" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 0,30 L 0,70" stroke="#065f46" stroke-width="2.5" fill="none"/>
                        <path d="M 6,32 L 6,10 Q 6,6 10,6 L 32,6" stroke="#c59b27" stroke-width="1.2" fill="none"/>
                        <path d="M 38,6 L 70,6" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <path d="M 6,38 L 6,70" stroke="#c59b27" stroke-width="1" fill="none"/>
                        <circle cx="17" cy="17" r="4.5" fill="#065f46"/>
                        <circle cx="17" cy="17" r="2.2" fill="#c59b27"/>
                        <path d="M 17,8 L 19,15 L 26,17 L 19,19 L 17,26 L 15,19 L 8,17 L 15,15 Z" fill="#c59b27"/>
                    </svg>
                </div>

                <!-- Inner Frame with Content -->
                <div class="cover-frame-inner">
                    <div class="cover-header">
                        <div class="header-super">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                        <div class="header-doc-type">BUKU INDUK PROGRAM MADRASAH</div>
                        <h1 class="header-main-title">PROGRAM SUPERVISI</h1>
                        <h2 class="header-sub-title">AKADEMIK DAN MANAJERIAL</h2>
                        
                        <div class="ornament-divider">
                            <span class="orn-line-left"></span>
                            <span class="orn-center-icon">
                                <svg width="60" height="18" viewBox="0 0 60 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M 30,1 L 34,9 L 30,17 L 26,9 Z" fill="#c59b27"/>
                                    <circle cx="30" cy="9" r="2.2" fill="#065f46"/>
                                    <circle cx="18" cy="9" r="2.5" fill="#065f46"/>
                                    <circle cx="42" cy="9" r="2.5" fill="#065f46"/>
                                    <line x1="2" y1="9" x2="12" y2="9" stroke="#065f46" stroke-width="1.5"/>
                                    <line x1="48" y1="9" x2="58" y2="9" stroke="#065f46" stroke-width="1.5"/>
                                </svg>
                            </span>
                            <span class="orn-line-right"></span>
                        </div>
                    </div>

                    <div class="cover-logo-area">
                        <div class="logo-crest-ring">
                            <?php if(!empty($institusi['logo'])): ?>
                                <img src="/public/uploads/logo/<?= htmlspecialchars($institusi['logo']) ?>" class="cover-logo" alt="Logo">
                            <?php else: ?>
                                <img src="/public/uploads/logo/logo_1778151980.png" class="cover-logo" alt="Logo">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="cover-madrasah-block">
                        <h2 class="madrasah-main-name"><?= htmlspecialchars($institusi['nama_institusi'] ?? $institusi['nama'] ?? 'MAS MIFTAHUL HUDA') ?></h2>
                        <div class="madrasah-pills">
                            <span>NSM: <?= htmlspecialchars($institusi['nsm'] ?? '111218040083') ?></span>
                            <span class="pill-sep">&bull;</span>
                            <span>NPSN: <?= htmlspecialchars($institusi['npsn'] ?? '60706240') ?></span>
                            <span class="pill-sep">&bull;</span>
                            <span class="status-active">TERAKREDITASI</span>
                        </div>

                        <div class="title-plaque-wrap">
                            <div class="title-plaque-box">
                                <div class="plaque-corner plaque-tl">❖</div>
                                <div class="plaque-corner plaque-tr">❖</div>
                                <div class="plaque-corner plaque-bl">❖</div>
                                <div class="plaque-corner plaque-br">❖</div>
                                <div class="plaque-badge">PEDOMAN & STANDAR OPERASIONAL</div>
                                <div class="plaque-title-text">BUKU PROGRAM SUPERVISI TAHUNAN</div>
                            </div>
                        </div>

                        <div class="academic-ribbon">
                            <span>TAHUN PELAJARAN <?= htmlspecialchars($ta) ?></span>
                        </div>
                    </div>

                    <div class="cover-footer-block">
                        <div class="ornament-divider-sm">
                            <span class="orn-line-left"></span>
                            <span class="orn-star-icon">❖ &bull; ❖</span>
                            <span class="orn-line-right"></span>
                        </div>

                        <div class="author-wrapper">
                            <div class="author-label">DISUSUN OLEH:</div>
                            <div class="author-headline">Kepala Madrasah & Tim Penjaminan Mutu Kurikulum</div>
                        </div>

                        <div class="institution-contact-footer">
                            <div class="footer-yayasan">YAYASAN PONDOK PESANTREN MIFTAHUL HUDA</div>
                            <div class="footer-alamat"><?= htmlspecialchars($institusi['alamat'] ?? 'Jl. Tirtomoyo No. 083, Margorahayu') ?></div>
                            <div class="footer-contact-row">
                                <span>Email: <?= htmlspecialchars($institusi['email'] ?? 'masmiftahulhuda@gmail.com') ?></span>
                                <span class="contact-sep">&bull;</span>
                                <span>Telp/WhatsApp: <?= htmlspecialchars($institusi['telepon'] ?? '081938240819') ?></span>
                            </div>
                        </div>
                    </div>
                </div> <!-- END .cover-frame-inner -->
            </div> <!-- END .cover-frame-outer -->
        </div> <!-- END OF COVER PAGE -->

        <div class="page-content-wrapper">

    <!-- LEMBAR PENGESAHAN -->
    <div class="page-break">
        <div class="chapter-title">LEMBAR PENGESAHAN</div>
        <p class="text-justify" style="margin-top: 50px; line-height: 2;">
            Dokumen Program Supervisi Akademik dan Manajerial <strong><?= htmlspecialchars($institusi['nama'] ?? 'MAS Miftahul Huda') ?></strong> Tahun Pelajaran <strong><?= htmlspecialchars($ta) ?></strong> ini telah diperiksa, disetujui, dan disahkan penggunaannya sebagai pedoman pelaksanaan supervisi pendidikan di lingkungan madrasah.
        </p>
        
        <table style="width: 100%; margin-top: 60px; border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">Ketua Panitia,</p>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">
                        <?= htmlspecialchars($institusi['kabupaten'] ?? $institusi['kota'] ?? 'Lampung Barat') ?>, 15 Juli <?= substr($ta, 0, 4) ?><br>
                        Kepala Madrasah (Penanggung Jawab),
                    </p>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 11pt;">
                        <?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? 'M AZIZ SULAIMAN, S.Pd') ?>
                    </p>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 11pt;">
                        <?= htmlspecialchars($kamad['nama'] ?? 'Drs. Hamim Hasyim') ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- KATA PENGANTAR -->
    <div class="page-break">
        <div class="chapter-title">KATA PENGANTAR</div>
        <p class="text-justify" style="margin-top: 30px;">
            Puji syukur kehadirat Allah SWT atas rahmat dan hidayah-Nya, sehingga Program Supervisi <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?> Tahun Pelajaran <?= htmlspecialchars($ta) ?> ini dapat diselesaikan.
        </p>
        <p class="text-justify">
            Program ini disusun sebagai acuan terarah bagi Kepala Madrasah dan Tim Supervisi dalam melakukan pembinaan, pengawasan, dan evaluasi berkelanjutan demi meningkatkan mutu pendidikan, kompetensi pendidik, serta pelayanan pembelajaran yang berpusat pada peserta didik di era modern.
        </p>
        <p class="text-justify">
            Kami mengucapkan terima kasih kepada seluruh pihak (dewan guru, komite madrasah, dan pengawas) yang telah berkontribusi memberikan masukan berharga dalam penyusunan program komprehensif ini. Harapan kami, pelaksanaan supervisi ini bukan sekadar rutinitas administratif, melainkan menjadi ruang kolaborasi dan <em>coaching</em> untuk inovasi pembelajaran.
        </p>
        
        <div style="text-align: right; margin-top: 50px;">
            <p><?= htmlspecialchars($institusi['kabupaten'] ?? 'Kota/Kab') ?>, 15 Juli <?= substr($ta, 0, 4) ?></p>
            <p>Penyusun,</p>
            <br><br><br>
            <p class="bold">Tim Kurikulum Madrasah</p>
        </div>
    </div>

    <!-- DAFTAR ISI -->
    <div class="page-break">
        <div class="chapter-title">DAFTAR ISI</div>
        <table style="border:none; width: 80%; margin: 0 auto; line-height: 2;">
            <tr style="border:none;"><td style="border:none;">Halaman Judul</td><td style="border:none; text-align:right;">i</td></tr>
            <tr style="border:none;"><td style="border:none;">Lembar Pengesahan</td><td style="border:none; text-align:right;">ii</td></tr>
            <tr style="border:none;"><td style="border:none;">Kata Pengantar</td><td style="border:none; text-align:right;">iii</td></tr>
            <tr style="border:none;"><td style="border:none;">Daftar Isi</td><td style="border:none; text-align:right;">iv</td></tr>
            <tr style="border:none;"><td style="border:none; padding-top:15px;" colspan="2"><strong>BAB I : PENDAHULUAN</strong></td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;A. Latar Belakang</td><td style="border:none; text-align:right;">1</td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;B. Dasar Hukum</td><td style="border:none; text-align:right;">2</td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;C. Tujuan & Sasaran</td><td style="border:none; text-align:right;">2</td></tr>
            <tr style="border:none;"><td style="border:none; padding-top:15px;" colspan="2"><strong>BAB II : PROFIL MADRASAH</strong></td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;A. Visi & Misi</td><td style="border:none; text-align:right;">4</td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;B. Kondisi Pendidik (Guru)</td><td style="border:none; text-align:right;">5</td></tr>
            <tr style="border:none;"><td style="border:none; padding-top:15px;" colspan="2"><strong>BAB III : DESAIN PROGRAM SUPERVISI</strong></td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;A. Ruang Lingkup (2 Pilar Utama: Supervisi Kelas & Tenaga Pendidik)</td><td style="border:none; text-align:right;">7</td></tr>
            <tr style="border:none;"><td style="border:none;">&nbsp;&nbsp;&nbsp;B. Pendekatan dan Pelaksana Supervisi</td><td style="border:none; text-align:right;">9</td></tr>
            <tr style="border:none;"><td style="border:none; padding-top:15px;" colspan="2"><strong>BAB IV : PELAKSANAAN DAN TINDAK LANJUT</strong></td></tr>
            <tr style="border:none;"><td style="border:none; padding-top:15px;" colspan="2"><strong>BAB V : PENUTUP</strong></td></tr>
        </table>
    </div>

    <!-- BAB I -->
    <div class="page-break">
        <div class="chapter-title">BAB I<br>PENDAHULUAN</div>
        <div class="section-title">A. Latar Belakang</div>
        <p class="text-justify">
            Supervisi pendidikan bukan sekadar kegiatan inspeksi atau penilaian sepihak, melainkan upaya sistematis dan humanis untuk membantu guru dan tenaga kependidikan mengembangkan profesionalismenya. Di era implementasi Kurikulum Merdeka madrasah, fokus pembinaan diletakkan pada mutu pelaksanaan pembelajaran di kelas yang berpusat pada murid serta tertib administrasi tenaga pendidik dan kependidikan.
        </p>
        <p class="text-justify">
            Oleh karena itu, <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?> menyusun program supervisi yang terfokus, realistis, dan berbobot mutu tinggi. Program ini difokuskan pada 2 (dua) pilar utama yang fundamental, yakni <strong>Supervisi Kelas (Observasi Pembelajaran Guru)</strong> dan <strong>Supervisi Tenaga Pendidik serta Tenaga Kependidikan (Administrasi & Pelayanan Madrasah)</strong>.
        </p>
        
        <div class="section-title">B. Dasar Hukum</div>
        <ol class="text-justify">
            <li>Undang-Undang No. 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
            <li>Peraturan Pemerintah No. 57 Tahun 2021 tentang Standar Nasional Pendidikan sebagaimana diubah dengan PP No. 4 Tahun 2022.</li>
            <li>Peraturan Menteri Agama (PMA) No. 58 Tahun 2017 tentang Kepala Madrasah.</li>
            <li>Keputusan Menteri Agama (KMA) No. 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
            <li>Rencana Kerja Tahunan (RKT) <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?>.</li>
        </ol>

        <div class="section-title">C. Tujuan dan Target</div>
        <p class="text-justify">
            <strong>Tujuan:</strong> Meningkatkan kompetensi pedagogik guru dalam mengelola pembelajaran di kelas, memastikan tertib administrasi perangkat ajar pendidik, serta mengoptimalkan layanan tata kelola tenaga kependidikan demi terwujudnya madrasah yang bermutu dan akuntabel.
        </p>
        <p class="text-justify">
            <strong>Target Capaian:</strong> 100% guru tersupervisi kelas dan administrasi minimal 2 (dua) kali dalam satu tahun ajaran (Semester Ganjil dan Genap), serta 100% tenaga kependidikan mendapatkan supervisi tata kelola manajerial secara teratur.
        </p>
    </div>

    <!-- BAB II -->
    <div class="page-break">
        <div class="chapter-title">BAB II<br>PROFIL MADRASAH</div>
        <div class="section-title">A. Identitas dan Visi Misi</div>
        <table style="width:100%; border:none;">
            <tr style="border:none;"><td style="border:none; width:150px; font-weight:bold;">Nama Madrasah</td><td style="border:none;">: <?= htmlspecialchars($institusi['nama'] ?? '-') ?></td></tr>
            <tr style="border:none;"><td style="border:none; font-weight:bold;">Alamat Lengkap</td><td style="border:none;">: <?= htmlspecialchars($institusi['alamat'] ?? '-') ?>, <?= htmlspecialchars($institusi['desa'] ?? '') ?>, <?= htmlspecialchars($institusi['kecamatan'] ?? '') ?></td></tr>
            <tr style="border:none;"><td style="border:none; font-weight:bold;">Kepala Madrasah</td><td style="border:none;">: <?= htmlspecialchars($institusi['kepala_institusi'] ?? '-') ?></td></tr>
        </table>
        
        <p class="bold" style="margin-top:20px;">Visi Madrasah:</p>
        <p class="text-justify"><em>"Menjadi madrasah unggulan yang berprestasi, berakhlakul karimah, dan berwawasan global di era digital."</em></p>
        
        <div class="section-title">B. Kondisi Tenaga Pendidik</div>
        <p class="text-justify">Sasaran dari program supervisi ini adalah seluruh tenaga pendidik (guru) yang aktif mengajar di <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?>. Berikut adalah daftar guru yang menjadi sasaran supervisi:</p>
        
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Nama Guru</th>
                    <th>NIP / NUPTK</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($gurus as $g): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($g['nama']) ?></td>
                    <td><?= htmlspecialchars($g['nip'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- BAB III -->
    <div class="page-break">
        <div class="chapter-title">BAB III<br>DESAIN PROGRAM SUPERVISI PRIORITAS</div>
        <div class="section-title">A. Ruang Lingkup (2 Pilar Utama Supervisi)</div>
        <p class="text-justify">Agar pelaksanaan supervisi terarah, efektif, dan memberikan dampak nyata terhadap peningkatan kualitas pembelajaran dan akreditasi/PKKM madrasah, ruang lingkup program supervisi difokuskan pada 2 (dua) pilar utama:</p>
        
        <div style="margin-left:15px; margin-bottom:15px;">
            <p class="text-justify" style="margin-bottom:8px;"><strong>1. PILAR I: SUPERVISI KELAS (AKADEMIK / PROSES PEMBELAJARAN GURU)</strong></p>
            <p class="text-justify" style="margin-top:0;">Merupakan observasi langsung pelaksanaan Kegiatan Belajar Mengajar (KBM) guru di ruang kelas dengan menggunakan instrumen standar yang memuat 4 (empat) aspek inti penilaian:</p>
            <ol type="a" class="text-justify" style="margin-top:0;">
                <li><strong>Persiapan Mengajar (6 Indikator):</strong> Pemeriksaan kesesuaian Capaian Pembelajaran (CP), Alur Tujuan Pembelajaran (ATP), Modul Ajar/RPP, kesiapan media dan sumber belajar kontekstual, kelengkapan LKPD, serta instrumen asesmen formatif awal dan bahan remedial/pengayaan.</li>
                <li><strong>Kegiatan Pendahuluan (5 Indikator):</strong> Pengkondisian kesiapan psikologis dan fisik siswa, pembiasaan religius (doa), apersepsi kontekstual, motivasi belajar, dan penyampaian alur tujuan kegiatan pembelajaran.</li>
                <li><strong>Kegiatan Inti Pembelajaran (11 Indikator):</strong> Penguasaan materi pembelajaran yang mendalam, penerapan model pembelajaran aktif dan bermakna (student-centered), fasilitasi pembelajaran berdiferensiasi, penanaman keterampilan berpikir kritis (HOTS), pemanfaatan media/TIK interaktif, pengelolaan dinamika kelas, dan asesmen formatif berkala selama KBM.</li>
                <li><strong>Kegiatan Penutup (3 Indikator):</strong> Pelaksanaan refleksi bersama antara guru dan siswa mengenai proses serta simpulan materi pokok yang dipelajari, asesmen hasil belajar, penguatan nilai karakter akhlakul karimah, dan pemberian arahan tindak lanjut penugasan.</li>
            </ol>

            <p class="text-justify" style="margin-bottom:8px; margin-top:15px;"><strong>2. PILAR II: SUPERVISI TENAGA PENDIDIK & TENAGA KEPENDIDIKAN (ADMINISTRASI & LAYANAN TENDIK)</strong></p>
            <p class="text-justify" style="margin-top:0;">Merupakan pemantauan dan pengujian terhadap kelayakan dokumen perencanaan kurikulum guru serta akuntabilitas tata kelola layanan administrasi tenaga kependidikan madrasah:</p>
            <ol type="a" class="text-justify" style="margin-top:0;">
                <li><strong>Supervisi Administrasi Perangkat Pembelajaran Guru:</strong> Verifikasi kelengkapan 13+ komponen perangkat ajar pendidik (Kalender Pendidikan Madrasah, Alur Tujuan Pembelajaran/Silabus, Modul Ajar/RPP, Program Tahunan, Program Semester, Kriteria Ketercapaian Tujuan Pembelajaran/KKTP, Buku Nilai Siswa, Jurnal Harian Guru, Daftar Hadir Siswa, dan Lembar Penilaian Kinerja).</li>
                <li><strong>Supervisi Tenaga Kependidikan (Tendik & Tata Usaha):</strong> Evaluasi akuntabilitas pengelolaan surat masuk/keluar, buku agenda dan pengarsipan digital, pengelolaan data kelembagaan madrasah pada EMIS dan Simpatika Kemenag.</li>
                <li><strong>Supervisi Pengelolaan Perpustakaan Madrasah:</strong> Evaluasi ketertiban buku induk perpustakaan, klasifikasi koleksi buku pelajaran dan bacaan, administrasi peminjaman, serta penyediaan pojok literasi madrasah.</li>
                <li><strong>Supervisi Sarana Prasarana & Aset:</strong> Pemeriksaan buku inventaris barang madrasah, kartu inventaris ruangan (KIR), pemeliharaan kebersihan dan kelayakan sarana KBM.</li>
            </ol>
        </div>

        <div class="section-title">B. Pendekatan dan Pelaksana</div>
        <p class="text-justify">Pendekatan yang digunakan adalah <strong>Coaching dan Kolaborasi Kesejawatan</strong>. Hasil supervisi tidak digunakan untuk menghakimi, melainkan sebagai bahan refleksi bersama guna merumuskan Rencana Tindak Lanjut (RTL) yang terukur. Pelaksana utama adalah Penanggung Jawab (<strong><?= htmlspecialchars($penanggung_jawab['nama'] ?? $kamad['nama']) ?></strong>), didampingi Ketua Panitia (<strong><?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama']) ?></strong>), Sekretaris (<strong><?= htmlspecialchars($sekretaris['nama'] ?? 'ZULI ASTUTIK, S.Pd.I') ?></strong>), serta Dewan Supervisor yang ditetapkan secara resmi melalui SK Kepala Madrasah.</p>
    </div>

    <!-- BAB IV & V -->
    <div class="page-break">
        <div class="chapter-title">BAB IV<br>PELAKSANAAN DAN TINDAK LANJUT</div>
        <p class="text-justify">Jadwal pelaksanaan supervisi diatur secara dinamis melalui Sistem Informasi Akademik (SIAKAD). Instrumen pengumpulan data dilakukan secara digital, sehingga hasil evaluasi dapat langsung diketahui oleh guru yang bersangkutan secara <em>real-time</em> (Live Scoring).</p>
        <p class="text-justify">Guru dengan nilai kategori <strong>Kurang</strong> akan diberikan pendampingan khusus (In-House Training), sementara guru dengan kategori <strong>Amat Baik</strong> akan didorong menjadi <em>peer-coach</em> bagi rekan sejawatnya.</p>
        
        <br><br>
        <div class="chapter-title" style="margin-top: 50px;">BAB V<br>PENUTUP</div>
        <p class="text-justify">Demikian Program Supervisi Pendidikan <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?> Tahun Pelajaran <?= htmlspecialchars($ta) ?> ini disusun. Komitmen, kerja sama, dan pikiran yang terbuka dari seluruh pendidik sangat diharapkan agar program ini berjalan dengan sukses dan memberikan dampak nyata terhadap peningkatan kualitas lulusan.</p>
        
        <div class="signature-area" style="margin-top: 60px;">
            <div class="signature-box" style="width: 100%;">
                <p><?= htmlspecialchars($institusi['kabupaten'] ?? 'Lampung Barat') ?>, 15 Juli <?= substr($ta, 0, 4) ?><br>Kepala Madrasah (Penanggung Jawab)</p>
                <div class="signature-name" style="text-decoration:underline; font-weight:bold;"><?= htmlspecialchars($kamad['nama'] ?? 'Drs. Hamim Hasyim') ?></div>
                <?php if(!empty($kamad['nip']) && $kamad['nip'] !== '-'): ?>
                <p>NIP. <?= htmlspecialchars($kamad['nip']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div> <!-- END OF .page-content-wrapper -->
    </div> <!-- END OF .document-wrapper -->
</body>
</html>
