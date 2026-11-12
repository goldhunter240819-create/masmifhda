<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Program Supervisi - <?= htmlspecialchars($program['judul']) ?></title>
    <!-- Google Fonts for Luxury Academic & Certificate Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm;
        }
        @page :first {
            margin: 8mm 8mm;
        }
        
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 25px 0 60px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .document-wrapper {
            max-width: 210mm;
            margin: 0 auto;
        }

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
            border: 3.5px solid #065f46; /* Deep Emerald */
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
            border: 1.5px solid #c59b27; /* Rich Gold */
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
        .cover-header {
            width: 100%;
            margin-top: 2px;
        }
        .header-super {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 10.5pt;
            font-weight: 700;
            letter-spacing: 2.5px;
            color: #065f46;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .header-doc-type {
            font-family: 'Cinzel', serif;
            font-size: 8.5pt;
            font-weight: 600;
            letter-spacing: 3px;
            color: #c59b27;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .header-main-title {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 25pt;
            font-weight: 900;
            letter-spacing: 2px;
            color: #0f172a;
            margin: 0 0 3px 0;
            line-height: 1.15;
            text-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }
        .header-sub-title {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 14.5pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
        }

        /* Top Line Ornament Divider */
        .ornament-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 78%;
            margin: 10px auto 4px;
            gap: 8px;
        }
        .orn-line-left, .orn-line-right {
            flex: 1;
            height: 1.5px;
            position: relative;
        }
        .orn-line-left {
            background: linear-gradient(90deg, transparent, #065f46 60%, #c59b27 100%);
        }
        .orn-line-right {
            background: linear-gradient(90deg, #c59b27, #065f46 40%, transparent 100%);
        }
        .orn-line-left::after, .orn-line-right::after {
            content: '';
            position: absolute;
            top: -2px;
            left: 0;
            right: 0;
            height: 0.8px;
            background: inherit;
            opacity: 0.4;
        }
        .orn-center-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Central Logo Area */
        .cover-logo-area {
            margin: 6px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo-crest-ring {
            position: relative;
            padding: 8px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(197, 155, 39, 0.45), 0 0 0 5px rgba(6, 95, 70, 0.15), 0 8px 25px rgba(0,0,0,0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .cover-logo {
            width: 145px;
            height: auto;
            max-height: 155px;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 3px 6px rgba(0,0,0,0.1));
        }

        /* Document Title Plaque */
        .title-plaque-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 4px 0 8px;
        }
        .title-plaque-box {
            position: relative;
            background: linear-gradient(135deg, rgba(6,95,70,0.03), rgba(197,155,39,0.06), rgba(6,95,70,0.03));
            border: 1.5px solid #065f46;
            outline: 1px solid #c59b27;
            outline-offset: 3px;
            border-radius: 4px;
            padding: 9px 24px;
            width: 90%;
            box-shadow: 0 2px 10px rgba(6,95,70,0.05);
        }
        .plaque-corner {
            position: absolute;
            font-size: 11px;
            color: #c59b27;
            line-height: 1;
        }
        .plaque-tl { top: -6px; left: -6px; }
        .plaque-tr { top: -6px; right: -6px; }
        .plaque-bl { bottom: -6px; left: -6px; }
        .plaque-br { bottom: -6px; right: -6px; }
        .plaque-badge {
            font-family: 'Cinzel', serif;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 2px;
            color: #065f46;
            margin-bottom: 2px;
        }
        .plaque-title-text {
            font-family: 'Times New Roman', serif;
            font-size: 14.5pt;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #0f172a;
            line-height: 1.3;
        }

        /* Madrasah Identity Block */
        .cover-madrasah-block {
            width: 100%;
        }
        .madrasah-main-name {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 21pt;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .madrasah-pills {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8.5pt;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .pill-sep {
            color: #c59b27;
            font-size: 11pt;
        }
        .status-active {
            color: #065f46;
            font-weight: 700;
        }
        .academic-ribbon {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #065f46;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 9.5pt;
            font-weight: 700;
            letter-spacing: 0.8px;
            padding: 4px 18px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(6,95,70,0.08);
        }

        /* Bottom Section & Author */
        .cover-footer-block {
            width: 100%;
        }
        .ornament-divider-sm {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 65%;
            margin: 6px auto 10px;
            gap: 8px;
        }
        .orn-star-icon {
            font-size: 8.5pt;
            color: #c59b27;
            letter-spacing: 3px;
        }
        .author-wrapper {
            margin-bottom: 8px;
        }
        .author-label {
            font-family: 'Cinzel', serif;
            font-size: 8.5pt;
            font-weight: 700;
            letter-spacing: 2px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .author-headline {
            font-family: 'Times New Roman', serif;
            font-size: 13.5pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.4px;
        }
        .institution-contact-footer {
            font-family: 'Times New Roman', serif;
            font-size: 9.5pt;
            color: #475569;
            line-height: 1.45;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            width: 85%;
            margin: 0 auto;
        }
        .footer-yayasan {
            font-weight: bold;
            font-size: 9.5pt;
            color: #065f46;
            letter-spacing: 0.8px;
            margin-bottom: 1px;
            text-transform: uppercase;
        }
        .footer-alamat {
            margin-bottom: 2px;
        }
        .footer-contact-row {
            font-size: 9pt;
            color: #64748b;
        }
        .contact-sep {
            color: #c59b27;
            margin: 0 4px;
        }

        /* ══════════════════════════════════════════════════════
           INNER DOCUMENT PAGES STYLING
           ══════════════════════════════════════════════════════ */
        .page-content-wrapper {
            background: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
            border-radius: 4px;
            padding: 22mm 18mm;
            margin-bottom: 30px;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .kop-logo {
            width: 80px;
            height: auto;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 0;
            font-size: 11pt;
        }
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-decoration: underline;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .content-box {
            margin-bottom: 20px;
            text-align: justify;
        }
        .content-box p {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .signature {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 300px;
            text-align: center;
        }

        /* Floating Modern Print Button */
        .btn-print {
            position: fixed;
            bottom: 25px;
            right: 25px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            z-index: 9999;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(5, 150, 105, 0.45);
            background: linear-gradient(135deg, #10b981, #059669);
        }

        /* PRINT MEDIA OVERRIDES */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .document-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
            }
            .cover-page {
                width: 100% !important;
                height: 278mm !important;
                max-height: 278mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 4px !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
            }
            .page-content-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <button class="no-print btn-print" onclick="window.print()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Cetak Dokumen
    </button>

    <div class="document-wrapper">
        <!-- ══════════════════════════════════════════════════════
             COVER PAGE DENGAN FRAME, ORNAMEN GARIS & CORNER BORDER
             ══════════════════════════════════════════════════════ -->
        <div class="cover-page">
            <div class="cover-frame-outer">
                <!-- 4 SVG Corner Ornaments (Mirrored via CSS) -->
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
                    <!-- Top Header Section -->
                    <div class="cover-header">
                        <div class="header-super">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                        <div class="header-doc-type">DOKUMEN INDUK PROGRAM MADRASAH</div>
                        <h1 class="header-main-title">PROGRAM SUPERVISI</h1>
                        <h2 class="header-sub-title">AKADEMIK DAN MANAJERIAL</h2>
                        
                        <!-- Ornamen Garis Mewah -->
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

                    <!-- Center Logo Area with Emblem Halo -->
                    <div class="cover-logo-area">
                        <div class="logo-crest-ring">
                            <?php if(!empty($institusi['logo'])): ?>
                                <img src="/public/uploads/logo/<?= htmlspecialchars($institusi['logo']) ?>" class="cover-logo" alt="Logo">
                            <?php else: ?>
                                <img src="/public/uploads/logo/logo_1778151980.png" class="cover-logo" alt="Logo">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Madrasah Name & Credentials -->
                    <div class="cover-madrasah-block">
                        <h2 class="madrasah-main-name"><?= htmlspecialchars($institusi['nama_institusi'] ?? 'MAS MIFTAHUL HUDA') ?></h2>
                        <div class="madrasah-pills">
                            <span>NSM: <?= htmlspecialchars($institusi['nsm'] ?? '111218040083') ?></span>
                            <span class="pill-sep">&bull;</span>
                            <span>NPSN: <?= htmlspecialchars($institusi['npsn'] ?? '60706240') ?></span>
                            <span class="pill-sep">&bull;</span>
                            <span class="status-active">TERAKREDITASI</span>
                        </div>

                        <!-- Document Title Plaque / Cartouche -->
                        <div class="title-plaque-wrap">
                            <div class="title-plaque-box">
                                <div class="plaque-corner plaque-tl">❖</div>
                                <div class="plaque-corner plaque-tr">❖</div>
                                <div class="plaque-corner plaque-bl">❖</div>
                                <div class="plaque-corner plaque-br">❖</div>
                                <div class="plaque-badge">FOKUS KEGIATAN & PEDOMAN KERJA</div>
                                <div class="plaque-title-text"><?= htmlspecialchars($program['judul']) ?></div>
                            </div>
                        </div>

                        <div class="academic-ribbon">
                            <span>TAHUN AJARAN <?= htmlspecialchars($program['ta_nama'] ?? '2026/2027') ?> &bull; SEMESTER <?= strtoupper(htmlspecialchars($program['semester'] ?? 'GANJIL')) ?></span>
                        </div>
                    </div>

                    <!-- Bottom Section: Authors & Institution Contact -->
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
    <div style="page-break-after: always; padding: 10px 0;">
        <h3 style="text-align:center; margin-bottom: 40px; font-size: 16pt; font-weight: bold;">LEMBAR PENGESAHAN</h3>
        <p style="text-align:justify; line-height: 2;">
            Dokumen <strong>Program Supervisi Akademik dan Manajerial</strong> Tahun Pelajaran <strong><?= htmlspecialchars($program['ta_nama'] ?? '') ?></strong> Semester <strong><?= strtoupper(htmlspecialchars($program['semester'])) ?></strong> di <strong><?= htmlspecialchars($institusi['nama_institusi'] ?? 'Madrasah') ?></strong> ini telah diperiksa, disetujui, dan disahkan penggunaannya sebagai pedoman pelaksanaan supervisi pendidikan di lingkungan madrasah.
        </p>
        
        <table style="width: 100%; margin-top: 50px; border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">Ketua Panitia,</p>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">
                        <?php
                        $tglProgramObj = strtotime($program['created_at'] ?? 'now');
                        $bulanIndoFull = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        $tglProgramIndo = date('d', $tglProgramObj) . ' ' . $bulanIndoFull[(int)date('n', $tglProgramObj)] . ' ' . date('Y', $tglProgramObj);
                        ?>
                        <?= htmlspecialchars($institusi['kabupaten'] ?? $institusi['kota'] ?? 'Lampung Barat') ?>, <?= $tglProgramIndo ?><br>
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

    <!-- DAFTAR ISI (STATIC/MANUAL STYLE) -->
    <div style="page-break-after: always; padding: 10px 0;">
        <h3 style="text-align:center; margin-bottom: 30px; font-size: 16pt; font-weight: bold;">DAFTAR ISI</h3>
        <table style="width: 80%; margin: 0 auto; border: none; line-height: 2;">
            <tr style="border: none;"><td style="border: none;">Halaman Judul</td><td style="border: none; text-align: right;">i</td></tr>
            <tr style="border: none;"><td style="border: none;">Lembar Pengesahan</td><td style="border: none; text-align: right;">ii</td></tr>
            <tr style="border: none;"><td style="border: none;">Kata Pengantar</td><td style="border: none; text-align: right;">iii</td></tr>
            <tr style="border: none;"><td style="border: none;">Daftar Isi</td><td style="border: none; text-align: right;">iv</td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB I PENDAHULUAN</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB II PROFIL MADRASAH</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB III PROGRAM SUPERVISI</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB IV ORGANISASI DAN PELAKSANA SUPERVISI</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB V JADWAL SUPERVISI</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB VI INSTRUMEN SUPERVISI</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB VII PENGOLAHAN DAN ANALISIS HASIL</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB VIII TINDAK LANJUT</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB IX MONITORING DAN EVALUASI</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>BAB X PELAPORAN</strong></td></tr>
            <tr style="border: none;"><td style="border: none; padding-top: 15px;" colspan="2"><strong>LAMPIRAN</strong></td></tr>
        </table>
    </div>

    <div class="content-box">
        <div style="page-break-after:always;">
<h3 style="text-align:center;">KATA PENGANTAR</h3>
<p style="text-align:justify;">Puji syukur kehadirat Allah SWT atas rahmat dan hidayah-Nya, sehingga Program Supervisi Madrasah ini dapat diselesaikan. Program ini disusun sebagai acuan terarah bagi Kepala Madrasah dan Tim Supervisi dalam melakukan pembinaan, pengawasan, dan evaluasi berkelanjutan demi meningkatkan mutu pendidikan, kompetensi pendidik, serta pelayanan pembelajaran yang berpusat pada peserta didik di era modern. Kami mengucapkan terima kasih kepada seluruh pihak (dewan guru, komite madrasah, dan pengawas) yang telah berkontribusi memberikan masukan berharga.</p>
</div>

<h3 style="text-align:center;">BAB I<br>PENDAHULUAN</h3>
<h4>A. Latar Belakang</h4>
<p style="text-align:justify; margin-bottom:10px;">Pendidikan merupakan fondasi utama dalam membangun peradaban bangsa. Dalam konteks pendidikan madrasah, upaya peningkatan mutu lulusan sangat bergantung pada kualitas proses pembelajaran yang diselenggarakan di dalam kelas. Guru sebagai ujung tombak pendidikan memegang peranan krusial dalam mentransformasikan ilmu pengetahuan, nilai-nilai moral, dan keterampilan kepada peserta didik. Namun, seiring dengan dinamika perubahan kurikulum, perkembangan teknologi informasi, serta pergeseran paradigma pendidikan menuju era Society 5.0, guru dituntut untuk terus beradaptasi dan berinovasi secara berkelanjutan.</p>
<p style="text-align:justify; margin-bottom:10px;">Kenyataan di lapangan seringkali menunjukkan bahwa pendidik menghadapi berbagai kendala manajerial maupun pedagogik dalam mengimplementasikan kurikulum yang berlaku. Tantangan seperti manajemen waktu di kelas, penyusunan instrumen asesmen yang valid (HOTS), hingga kurangnya penguasaan model pembelajaran abad ke-21 (seperti <em>Deep Learning</em>, <em>Problem Based Learning</em>, dan pemanfaatan media digital) menuntut adanya pembinaan yang sistematis. Selain itu, dimensi psikologis seperti perlunya pendekatan welas asih atau Kurikulum Berbasis Cinta (KBC) menjadi semakin penting untuk mengatasi masalah karakter dan motivasi belajar peserta didik pasca-pandemi.</p>
<p style="text-align:justify;">Oleh karena itu, kegiatan supervisi pendidikan tidak lagi dipandang sekadar sebagai "inspeksi" atau pengawasan yang bersifat top-down dan mencari-cari kesalahan. Supervisi akademik dan manajerial madrasah kini telah bertransformasi menjadi layanan profesional yang bersifat kolaboratif, humanis, dan berorientasi pada pengembangan diri guru. Kepala Madrasah selaku supervisor berfungsi sebagai fasilitator, mentor, sekaligus <em>coach</em> yang memandu guru menemukan solusi atas permasalahannya sendiri. Melalui Program Supervisi ini, diharapkan seluruh komponen madrasah dapat bergerak sinergis mewujudkan iklim akademik yang prestatif, religius, dan siap menyongsong masa depan yang gemilang.</p>

<h4>B. Dasar Hukum</h4>
<p style="text-align:justify; margin-bottom:5px;">Pelaksanaan supervisi ini berlandaskan pada serangkaian peraturan perundang-undangan dan kebijakan strategis pemerintah di bidang pendidikan, antara lain:</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li>Undang-Undang Republik Indonesia Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
<li>Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen.</li>
<li>Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan (SNP) sebagaimana telah diubah dengan PP Nomor 4 Tahun 2022.</li>
<li>Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi (Permendikbudristek) Nomor 16 Tahun 2022 tentang Standar Proses pada PAUD, Jenjang Pendidikan Dasar, dan Pendidikan Menengah.</li>
<li>Keputusan Menteri Agama (KMA) Republik Indonesia Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
<li>Peraturan Menteri Agama Nomor 58 Tahun 2017 tentang Kepala Madrasah yang menegaskan tugas kepala madrasah sebagai supervisor.</li>
<li>Rencana Kerja Menengah Madrasah (RKMM) dan Rencana Kerja Tahunan (RKT) Madrasah Tahun Pelajaran berjalan.</li>
</ol>

<h4>C. Tujuan Supervisi</h4>
<p style="text-align:justify; margin-bottom:5px;">Tujuan umum dari pelaksanaan program supervisi akademik dan manajerial di lingkungan madrasah ini adalah sebagai berikut:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Peningkatan Mutu Pembelajaran:</strong> Membantu guru dalam menyusun skenario pembelajaran yang berpusat pada siswa (student-centered), inovatif, dan menyenangkan, sehingga berdampak langsung pada peningkatan prestasi akademik dan non-akademik siswa.</li>
<li><strong>Pengembangan Profesionalisme:</strong> Mendorong pertumbuhan profesional guru secara berkelanjutan (Continuous Professional Development) melalui identifikasi kelemahan mengajar yang kemudian ditindaklanjuti dengan pelatihan, <em>In-House Training (IHT)</em>, atau <em>Peer-Coaching</em>.</li>
<li><strong>Penjaminan Standar Kualitas:</strong> Memastikan terpenuhinya kelengkapan dan keabsahan dokumen administrasi pembelajaran seperti Modul Ajar, Silabus, ATP, Program Tahunan (Prota), dan Program Semester (Promes) sesuai dengan standar yang ditetapkan pemerintah.</li>
<li><strong>Pembinaan Karakter & Empati:</strong> Memastikan implementasi Kurikulum Berbasis Cinta (KBC) dan pembentukan Profil Pelajar Rahmatan Lil 'Alamin berjalan efektif melalui interaksi kelas yang sehat dan beradab.</li>
</ul>

<h4>D. Prinsip - Prinsip Supervisi</h4>
<p style="text-align:justify; margin-bottom:5px;">Dalam pelaksanaannya, Kepala Madrasah dan Tim Supervisi berpegang teguh pada prinsip-prinsip etis dan profesional berikut:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Kolaboratif dan Humanis:</strong> Menciptakan relasi kesejawatan (kemitraan) dengan guru; tidak bersifat otokratis atau menjatuhkan martabat pendidik.</li>
<li><strong>Objektif dan Berbasis Data:</strong> Pengambilan kesimpulan dan nilai didasarkan pada instrumen rubrik yang terukur, observasi faktual di kelas, dan bukti dokumen fisik pendukung, bukan asumsi.</li>
<li><strong>Konstruktif (Membangun):</strong> Berfokus pada perbaikan ke depan. Segala temuan negatif akan dibahas dalam ruang diskusi yang aman untuk mencari akar permasalahan (root cause).</li>
<li><strong>Berkelanjutan (Continuous):</strong> Pelaksanaan dilakukan secara berkesinambungan dan terpola dalam satu siklus yang tidak terputus sepanjang tahun ajaran (Supervisi &rarr; Temuan &rarr; Tindak Lanjut &rarr; Evaluasi).</li>
<li><strong>Transparan:</strong> Jadwal, kriteria instrumen penilaian, dan hasil evaluasi disampaikan secara terbuka kepada guru yang disupervisi sebelum dan sesudah pelaksanaan.</li>
</ul>

<h4>E. Sasaran</h4>
<p style="text-align:justify; margin-bottom:15px;">Program supervisi ini bersifat menyeluruh (holistik) dan mengikat bagi seluruh civitas akademika madrasah. Sasaran utamanya meliputi:<br>
1. Seluruh <strong>Guru Kelas</strong> (Wali Kelas) dari kelas rendah hingga kelas tinggi.<br>
2. Seluruh <strong>Guru Mata Pelajaran</strong> (termasuk mapel PAI, Bahasa Arab, Pendidikan Jasmani, dan Muatan Lokal).<br>
3. Seluruh <strong>Tenaga Kependidikan</strong> (Staf Tata Usaha, Pustakawan, Petugas Laboratorium, dll) terkait dengan layanan administrasi penunjang KBM.</p>

<h4>F. Ruang Lingkup</h4>
<p style="text-align:justify; margin-bottom:15px;">Agar pelaksanaan supervisi terarah, efektif, dan memberikan dampak langsung terhadap peningkatan kualitas proses pembelajaran serta akreditasi/PKKM madrasah, ruang lingkup program supervisi difokuskan pada <strong>2 (dua) Pilar Utama</strong>: (1) <strong>Supervisi Kelas (Observasi Pembelajaran Guru)</strong> yang mencakup 4 aspek penilaian proses KBM (Persiapan Mengajar, Pendahuluan, Inti Pembelajaran, dan Penutup); serta (2) <strong>Supervisi Tenaga Pendidik & Tenaga Kependidikan (Administrasi & Layanan Tendik)</strong> yang mencakup kelengkapan 13+ perangkat ajar guru dan akuntabilitas tata kelola administrasi madrasah (Tata Usaha, Perpustakaan, Sarana Prasarana, dan Operator Data). Seluruh lingkup ini dipantau secara terpadu melalui sistem informasi madrasah.</p>

<h4>G. Target Capaian Supervisi</h4>
<p style="text-align:justify; margin-bottom:5px;">Keberhasilan program ini diukur melalui Indikator Kinerja Utama (IKU) madrasah sebagai berikut:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Kuantitatif Pendidik:</strong> 100% (seluruh) tenaga pendidik mendapatkan layanan observasi kelas (supervisi akademik) dan verifikasi administrasi ajar minimal 2 (dua) kali dalam satu tahun ajaran, yakni pada siklus Semester Ganjil dan Semester Genap.</li>
<li><strong>Kuantitatif Tendik:</strong> 100% tenaga kependidikan (staf TU, pustakawan, sarpras, operator) mendapatkan supervisi tata kelola administrasi minimal 1 (satu) kali setiap semester.</li>
<li><strong>Kualitatif Pedagogik:</strong> Minimal 85% dari total guru yang disupervisi meraih predikat akhir minimal <strong>"BAIK" (Skor &gt; 80)</strong> pada 4 aspek standar observasi kelas.</li>
<li><strong>Tindak Lanjut & Pembinaan:</strong> 100% guru dan tendik yang memerlukan peningkatan kompetensi menerima program Rencana Tindak Lanjut (RTL) atau supervisi klinis/pendampingan KKG selambat-lambatnya 14 hari pasca supervisi.</li>
</ul>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center; margin-bottom:25px;">BAB II<br>PROFIL MADRASAH</h3>

<h4 style="margin-top:20px; margin-bottom:10px;">A. Identitas Madrasah</h4>
<p style="text-align:justify; margin-bottom:12px;">Secara kelembagaan dan perizinan resmi operasional, <?= htmlspecialchars($institusi['nama'] ?? $institusi['nama_institusi'] ?? 'MAS Miftahul Huda') ?> merupakan satuan pendidikan formal tingkat dasar berciri khas Islam yang bernaung di bawah Kementerian Agama Republik Indonesia, dengan rincian identitas madrasah sebagai berikut:</p>

<table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:10pt;">
    <tr>
        <td style="width:32%; padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Nama Madrasah</td>
        <td style="width:68%; padding:6px 10px; border:1px solid #cbd5e1;"><?= htmlspecialchars($institusi['nama'] ?? $institusi['nama_institusi'] ?? 'MI Miftahul Huda Margorahayu') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Nomor Statistik Madrasah (NSM)</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;"><?= htmlspecialchars($institusi['nsm'] ?? '111218040045') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">NPSN</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;"><?= htmlspecialchars($institusi['npsn'] ?? '60706240') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Status Akreditasi</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;">Terakreditasi B (Badan Akreditasi Nasional Sekolah/Madrasah)</td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Alamat Lengkap</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;"><?= htmlspecialchars($institusi['alamat'] ?? 'Jl. Tirtomoyo No. 083') ?>, Margorahayu, Kec. Way Tenong, Kab. Lampung Barat, Lampung (Kode Pos 34884)</td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Kepala Madrasah</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;"><?= htmlspecialchars($kamad['nama'] ?? 'Drs. Hamim Hasyim') ?></td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Lembaga Penyelenggara</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;">Yayasan Pondok Pesantren Miftahul Huda Mathla'ul Anwar</td>
    </tr>
    <tr>
        <td style="padding:6px 10px; font-weight:bold; border:1px solid #cbd5e1; background:#f8fafc;">Layanan Digital & Portal</td>
        <td style="padding:6px 10px; border:1px solid #cbd5e1;">Website: mismifhda.sch.id &bull; Email: mimiftahulhuda083@gmail.com</td>
    </tr>
</table>

<h4 style="margin-top:20px; margin-bottom:10px;">B. Visi, Misi, dan Tujuan Madrasah</h4>
<p style="text-align:justify; margin-bottom:10px;">Visi dan misi madrasah dirumuskan secara partisipatif dan berkala ditinjau bersama dewan pendidik dan komite madrasah sebagai acuan filosofis dan operasional:</p>

<div style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:15px;">
    <strong style="color:#166534; font-size:10.5pt;">Visi Resmi Madrasah:</strong>
    <p style="margin:6px 0 0 0; font-style:italic; font-weight:bold; color:#14532d; font-size:11pt; line-height:1.6;">
        "Bertaqwa, Berakhlakul Karimah, Berprestasi, dan Berwawasan Lingkungan Hidup."
    </p>
</div>

<p style="font-weight:bold; margin-bottom:6px;">Misi Strategis Madrasah:</p>
<ol style="text-align:justify; margin-top:0; margin-bottom:15px; line-height:1.8;">
    <li><strong>Pembentukan Karakter & Ketakwaan:</strong> Membentuk karakter peserta didik yang taat beribadah dan bertaqwa kepada Allah SWT melalui pembiasaan Sholat Dhuha harian, Dzuhur berjamaah, hafalan doa harian, dan program Tahfidz Al-Qur'an Juz 30.</li>
    <li><strong>Pendidikan Akhlakul Karimah:</strong> Mewujudkan proses pendidikan yang menghasilkan lulusan berakhlakul karimah melalui pembudayaan 5S (Senyum, Salam, Sapa, Sopan, Santun) serta kajian adab santri.</li>
    <li><strong>Prestasi Akademik & Non-Akademik:</strong> Mewujudkan proses pendidikan bermutu tinggi yang melahirkan peserta didik berprestasi di bidang sains (Kompetisi Sains Madrasah/KSM), keagamaan (MTQ/Hadroh), olahraga (Pencak Silat), dan kepramukaan.</li>
    <li><strong>Pembelajaran Inovatif & Digital (Smart Madrasah):</strong> Menyelenggarakan proses KBM inovatif, inspiratif, dan berdiferensiasi berbasis teknologi modern melalui Computer Based Test (CBT), asesmen ANBK terpadu, modul ajar interaktif, dan SIAKAD digital.</li>
    <li><strong>Penilaian Akuntabel & Transparan:</strong> Menerapkan sistem penilaian capaian belajar yang akuntabel, transparan, dan terintegrasi melalui aplikasi Rapor Digital Madrasah (RDM) Kementerian Agama.</li>
    <li><strong>Lingkungan Madrasah yang Asri & Nyaman (Eco-Madrasah):</strong> Mewujudkan tata kelola lingkungan madrasah yang hijau, bersih, sehat, indah, asri, dan aman sebagai sarana belajar yang ramah anak.</li>
</ol>

<p style="font-weight:bold; margin-bottom:6px;">Tujuan Kelembagaan Madrasah:</p>
<ul style="text-align:justify; margin-top:0; margin-bottom:20px; line-height:1.8;">
    <li>Menyiapkan peserta didik menjadi insan beriman, bertaqwa, dan berakhlak mulia yang memiliki kemandirian ibadah harian.</li>
    <li>Meningkatkan kompetensi dasar literasi membaca, literasi numerasi, dan sains agar seluruh lulusan siap melanjutkan ke jenjang MTs/SMP unggulan.</li>
    <li>Mengembangkan minat, bakat, serta potensi unik anak melalui bimbingan kepribadian berlandaskan Kurikulum Berbasis Cinta (KBC).</li>
    <li>Mewujudkan madrasah digital terpercaya dengan tata kelola manajemen yang transparan, modern, dan akuntabel.</li>
</ul>

<h4 style="margin-top:20px; margin-bottom:10px;">C. Kondisi Guru, Tenaga Kependidikan, dan Peserta Didik</h4>
<p style="text-align:justify; margin-bottom:10px;">Guna memastikan penyelenggaraan pendidikan berlangsung prima, madrasah didukung oleh sumber daya pendidik dan tenaga kependidikan yang berdedikasi tinggi serta jumlah peserta didik yang aktif:</p>
<ol style="text-align:justify; margin-top:0; margin-bottom:15px; line-height:1.8;">
    <li><strong>Tenaga Pendidik (Dewan Guru):</strong> Didukung oleh <?= (int)($countGuru ?? 12) ?> orang guru berkualifikasi akademik Sarjana (S-1) dan Magister (S-2) linier di bidang Pendidikan Guru Madrasah Aliyah (PGMI), Pendidikan Agama Islam (PAI), dan kependidikan umum, yang menguasai implementasi Kurikulum Merdeka dan Kurikulum Berbasis Cinta (KBC). Seluruh guru aktif mengikuti forum Komunitas Belajar (Kombel) KKG dan pelatihan MOOC PINTAR Kemenag.</li>
    <li><strong>Tenaga Kependidikan (Tendik):</strong> Didukung tenaga tata usaha, operator pangkalan data EMIS/Simpatika, pengelola perpustakaan madrasah, dan petugas sarana prasarana yang melayani administrasi pendidikan madrasah secara terpadu.</li>
    <li><strong>Peserta Didik:</strong> Tersebar dalam 6 rombongan belajar (Kelas I s.d. VI) dengan total <?= (int)($countSiswa ?? 140) ?> siswa aktif yang memiliki keberagaman bakat minat serta antusiasme belajar tinggi dalam lingkungan belajar ramah anak dan religius.</li>
</ol>

<h4 style="margin-top:20px; margin-bottom:10px;">D. Sarana Prasarana & Kondisi Pembelajaran</h4>
<p style="text-align:justify; margin-bottom:15px; line-height:1.8;">Pelaksanaan KBM di <?= htmlspecialchars($institusi['nama'] ?? 'MAS Miftahul Huda') ?> didukung oleh sarana prasarana yang representatif dan terus dikembangkan, meliputi gedung madrasah milik sendiri, ruang kelas belajar berfasilitas pojok baca literasi dan media peraga edukatif, laboratorium komputer multimedia terhubung internet untuk pelaksanaan ANBK/CBT, masjid/musholla sarana ibadah dhuha dan dzuhur berjamaah, perpustakaan madrasah, lapangan olahraga serbaguna, sanitasi ramah anak yang bersih, serta lingkungan asri yang menunjang kenyamanan belajar siswa.</p>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB III<br>PROGRAM SUPERVISI (2 PILAR FOKUS UTAMA)</h3>
<p style="text-align:justify; margin-bottom:15px;">Guna menjawab kebutuhan peningkatan mutu pendidikan madrasah secara terarah, realistis, dan berbobot tinggi, program supervisi difokuskan pada 2 (dua) pilar utama yang fundamental bagi keberhasilan madrasah:</p>

<h4>A. PILAR I: SUPERVISI KELAS (AKADEMIK / PROSES PEMBELAJARAN GURU)</h4>
<p style="text-align:justify; margin-bottom:10px;">Supervisi kelas merupakan inti kegiatan pengawasan akademik. Kepala Madrasah atau Tim Supervisor melakukan observasi langsung tatap muka di dalam kelas untuk memantau dinamika Kegiatan Belajar Mengajar (KBM) yang otentik. Penilaian observasi kelas menerapkan 4 (empat) aspek standar:</p>
<ol style="text-align:justify; margin-bottom:15px;">
    <li><strong>Persiapan Mengajar (6 Indikator):</strong> Keselarasan Modul Ajar / RPP dengan Capaian Pembelajaran (CP) dan Alur Tujuan Pembelajaran (ATP), kesiapan media dan sumber belajar, kelengkapan LKPD, instrumen asesmen formatif awal, serta bahan pengayaan/remedial.</li>
    <li><strong>Kegiatan Pendahuluan (5 Indikator):</strong> Pembiasaan doa dan salam, pengkondisian kesiapan mental dan fisik siswa, pemberian motivasi dan apersepsi kontekstual, penyampaian tujuan pembelajaran, serta penjelasan skema asesmen.</li>
    <li><strong>Kegiatan Inti Pembelajaran (11 Indikator):</strong> Penguasaan konsep materi secara mendalam, penerapan model pembelajaran aktif yang berpusat pada siswa (student-centered), fasilitasi pembelajaran berdiferensiasi, pemantik keterampilan berpikir kritis (HOTS), pemanfaatan media edukatif/TIK interaktif, pengelolaan interaksi dan disiplin positif kelas, serta asesmen formatif berkala selama pembelajaran berlangsung.</li>
    <li><strong>Kegiatan Penutup (3 Indikator):</strong> Refleksi bersama siswa mengenai proses dan kebermaknaan materi yang dipelajari, perumusan kesimpulan bersama dan evaluasi hasil belajar, penguatan pendidikan karakter Islami, serta penyampaian tindak lanjut penugasan terstruktur.</li>
</ol>

<h4>B. PILAR II: SUPERVISI TENAGA PENDIDIK & TENAGA KEPENDIDIKAN (ADMINISTRASI & LAYANAN TENDIK)</h4>
<p style="text-align:justify; margin-bottom:10px;">Pilar kedua memastikan ketertiban tata kelola dokumen perencanaan ajar pendidik dan akuntabilitas manajerial tenaga kependidikan pendukung madrasah:</p>
<ol style="text-align:justify; margin-bottom:15px;">
    <li><strong>Supervisi Administrasi Perangkat Pembelajaran Guru:</strong> Verifikasi sistematis kelengkapan 13+ dokumen administrasi wajib guru, meliputi Kalender Pendidikan, Program Tahunan (Prota), Program Semester (Promes), CP & ATP/Silabus, Modul Ajar/RPP, Kriteria Ketercapaian Tujuan Pembelajaran (KKTP), Buku Nilai dan Asesmen Siswa, Jurnal Mengajar Harian, Daftar Hadir Siswa, dan Lembar Penilaian Kinerja Guru.</li>
    <li><strong>Supervisi Tenaga Administrasi / Tata Usaha (TU):</strong> Evaluasi tertib administrasi persuratan (buku agenda surat masuk/keluar, format penomoran surat kedinasan), kearsipan dokumen kepegawaian, serta pengelolaan data madrasah pada pangkalan data EMIS Kemenag dan Simpatika.</li>
    <li><strong>Supervisi Pengelolaan Perpustakaan Madrasah:</strong> Pemeriksaan ketertiban buku induk inventaris perpustakaan, klasifikasi koleksi buku teks pelajaran dan buku pengayaan, sistem pencatatan peminjaman dan pengembalian, serta optimalisasi pojok literasi madrasah.</li>
    <li><strong>Supervisi Sarana Prasarana & Aset:</strong> Pemantauan buku inventaris barang milik madrasah, kartu inventaris ruangan (KIR), pemeliharaan kebersihan dan keamanan fasilitas belajar, serta pengusulan perawatan sarpras secara berkala.</li>
</ol>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB IV<br>ORGANISASI DAN PELAKSANA SUPERVISI</h3>
<p style="text-align:justify; margin-bottom:15px;">Pelaksanaan program supervisi akademik dan manajerial yang komprehensif tidak mungkin berjalan efektif tanpa adanya struktur organisasi yang jelas dan pembagian tugas pokok serta fungsi (Tupoksi) yang terstruktur. Oleh karena itu, madrasah membentuk Tim Pelaksana Supervisi yang dikoordinasikan secara hierarkis dan fungsional.</p>

<h4>A. Struktur Organisasi Supervisi</h4>
<p style="text-align:justify; margin-bottom:10px;">Struktur pelaksana supervisi di madrasah dibentuk berdasarkan Surat Keputusan (SK) Kepala Madrasah pada awal tahun ajaran. Secara manajerial, alur pertanggungjawabannya adalah sebagai berikut:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0; line-height:1.6;">
<li><strong>1. Penanggung Jawab:</strong> <?= htmlspecialchars($penanggung_jawab['nama'] ?? $kamad['nama'] ?? 'Drs. Hamim Hasyim') ?> (<?= htmlspecialchars($penanggung_jawab['jabatan'] ?? 'Kepala Madrasah') ?>)</li>
<li><strong>2. Ketua Panitia:</strong> <?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? 'M AZIZ SULAIMAN, S.Pd') ?> (<?= htmlspecialchars($ketua_panitia['jabatan'] ?? 'Waka Kurikulum') ?>)</li>
<li><strong>3. Sekretaris:</strong> <?= htmlspecialchars($sekretaris['nama'] ?? 'ZULI ASTUTIK, S.Pd.I') ?> (<?= htmlspecialchars($sekretaris['jabatan'] ?? 'Guru') ?>)</li>
<li><strong>4. Dewan Supervisor / Observer:</strong> <?= !empty($supervisor['nama']) ? htmlspecialchars($supervisor['nama']) : 'Dewan Guru Senior & Observer KBM' ?></li>
<li><strong>Pihak Tersupervisi:</strong> Seluruh Guru Kelas, Guru Mata Pelajaran, dan Tenaga Kependidikan.</li>
</ul>

<h4>B. Tugas Pokok dan Fungsi (Tupoksi) Kepala Madrasah</h4>
<p style="text-align:justify; margin-bottom:5px;">Sebagai edukator, manajer, administrator, dan supervisor (EMAS), Kepala Madrasah memiliki tugas mutlak:</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li>Menyusun, mensahkan, dan menyosialisasikan Program Supervisi Tahunan kepada seluruh dewan guru.</li>
<li>Menerbitkan Surat Keputusan (SK) pendelegasian wewenang kepada Tim Supervisi (Wakil Kepala dan Guru Senior) untuk membantu proses observasi.</li>
<li>Melaksanakan observasi kelas secara langsung secara acak (sampling) atau khusus pada guru-guru prioritas.</li>
<li>Menyelenggarakan pertemuan pasca-observasi (sesi <em>coaching</em>) untuk menganalisis hasil rapor supervisi bersama guru yang bersangkutan.</li>
<li>Merumuskan Rencana Tindak Lanjut (RTL) seperti merekomendasikan pelatihan (IHT) atau pengadaan fasilitas penunjang KBM berdasarkan temuan supervisi.</li>
<li>Melaporkan hasil keseluruhan supervisi kepada Pengawas Madrasah dan Yayasan (jika ada) sebagai bahan evaluasi kinerja tahunan.</li>
</ol>

<h4>C. Tugas Pokok dan Fungsi Tim Supervisi (Observer)</h4>
<p style="text-align:justify; margin-bottom:5px;">Mengingat rasio jumlah guru yang tidak sebanding jika disupervisi sendiri oleh Kepala Madrasah, Tim Supervisi yang ditunjuk memiliki peran strategis:</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li>Menyepakati jadwal observasi dan jenis modul instrumen yang akan dinilai bersama guru yang bersangkutan.</li>
<li>Melakukan pemeriksaan dokumen administrasi (Prota, Promes, Modul Ajar, ATP) secara teliti sebelum jadwal masuk kelas.</li>
<li>Melakukan pengamatan faktual di dalam kelas (KBM) tanpa mengintervensi atau memotong penjelasan guru saat sedang mengajar.</li>
<li>Mengisi instrumen rubrik penilaian secara objektif, jujur, dan berbasis bukti melalui platform <em>Dashboard Supervisi SIAKAD</em>.</li>
<li>Mencatat <em>anecdotal record</em> (catatan lapangan) mengenai kejadian spesifik, kekuatan, dan kelemahan guru yang tidak tercakup dalam rubrik angka.</li>
<li>Menyerahkan hasil observasi secara *real-time* kepada Kepala Madrasah untuk ditindaklanjuti.</li>
</ol>

<h4>D. Peran dan Kode Etik Guru (Pihak Tersupervisi)</h4>
<p style="text-align:justify; margin-bottom:5px;">Guru sebagai subjek utama dalam program ini diharapkan memposisikan supervisi sebagai momentum pengembangan karir (bukan beban). Guru memiliki kewajiban:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Kesiapan Dokumen:</strong> Menyiapkan dan melengkapi seluruh perangkat pembelajaran (Modul Ajar, LKPD, Instrumen Asesmen) selambat-lambatnya H-3 sebelum jadwal observasi.</li>
<li><strong>Autentisitas Kinerja:</strong> Mengajar secara wajar, natural, dan otentik sesuai keseharian, tanpa melakukan "rekayasa" KBM atau mengkondisikan siswa secara berlebihan hanya demi penilaian sesaat.</li>
<li><strong>Keterbukaan Pikiran (Open-Minded):</strong> Menerima kritik, saran, dan <em>feedback</em> dari supervisor dengan lapang dada sebagai bahan refleksi diri.</li>
<li><strong>Self-Assessment (Refleksi):</strong> Mengisi instrumen refleksi diri pasca-observasi untuk mengevaluasi secara mandiri hal-hal apa saja yang sudah berjalan baik dan yang masih perlu diperbaiki di pertemuan berikutnya.</li>
</ul>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB V<br>JADWAL DAN MEKANISME PELAKSANAAN SUPERVISI</h3>
<p style="text-align:justify; margin-bottom:15px;">Keberhasilan program supervisi sangat bergantung pada kedisiplinan waktu dan kepatuhan terhadap jadwal yang telah disepakati bersama. Di era digital ini, madrasah menggunakan sistem otomasi melalui <em>Sistem Informasi Akademik (SIAKAD)</em> untuk menghindari benturan jadwal mengajar dan memastikan seluruh guru tersupervisi secara adil dan merata.</p>

<h4>A. Skema Penjadwalan Berbasis Aplikasi</h4>
<p style="text-align:justify; margin-bottom:15px;">Jadwal supervisi tidak lagi disusun secara konvensional. Sistem akan melakukan <em>mapping</em> secara otomatis berdasarkan jam kosong (JTM) supervisor dan menyesuaikannya dengan jam mengajar guru target. Guru akan menerima notifikasi jadwal supervisi (yang memuat Tanggal, Jam Ke-, Kelas, Mata Pelajaran, dan Nama Supervisor) paling lambat 1 (satu) minggu sebelum hari-H pelaksaaan. Hal ini memberikan keleluasaan bagi guru untuk mempersiapkan mental dan kelengkapan administrasi sebaik mungkin.</p>

<h4>B. Jenis dan Sifat Pelaksanaan Supervisi</h4>
<p style="text-align:justify; margin-bottom:5px;">Berdasarkan urgensinya, mekanisme pelaksanaan supervisi di madrasah ini dibagi menjadi dua jenis utama:</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Supervisi Reguler (Terjadwal):</strong> Dilaksanakan sesuai dengan <em>timeline</em> kalender akademik madrasah. Fokus supervisi pada tahap ini bersifat menyeluruh (general), mencakup 12 modul pengawasan yang dinilai menggunakan rubrik baku.</li>
<li><strong>Supervisi Klinis (Insidental):</strong> Merupakan supervisi lanjutan yang bersifat khusus dan personal. Supervisi ini tidak dijadwalkan secara masif, melainkan diperuntukkan bagi guru-guru spesifik yang teridentifikasi mengalami masalah pedagogik akut (misalnya siswa sering mengeluh bosan, nilai ujian kelas sangat rendah, atau guru terindikasi mengalami <em>burnout</em>). Sifat pelaksanaannya sangat tertutup (confidential) dan lebih mengedepankan aspek psikologis (konseling) daripada administratif.</li>
</ol>

<h4>C. Timeline Matriks Pelaksanaan Tahunan</h4>
<p style="text-align:justify; margin-bottom:10px;">Agar 2 pilar utama supervisi dapat dilaksanakan secara optimal tanpa membebani guru secara berlebihan, fokus pelaksanaan dibagi menjadi 2 (dua) termin utama berdasarkan Semester:</p>
<table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 11pt; margin-bottom:15px;">
<thead>
<tr style="background:#f0f0f0;">
<th style="border: 1px solid #000; padding: 8px; text-align: center; width: 15%;">Semester</th>
<th style="border: 1px solid #000; padding: 8px; text-align: center; width: 55%;">Fokus Pilar & Aspek Supervisi</th>
<th style="border: 1px solid #000; padding: 8px; text-align: center; width: 30%;">Waktu Pelaksanaan</th>
</tr>
</thead>
<tbody>
<tr>
<td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight:bold;">GANJIL</td>
<td style="border: 1px solid #000; padding: 8px; text-align: justify;">
<strong>1. Supervisi Tenaga Pendidik (Administrasi):</strong> Verifikasi 13+ kelengkapan perangkat ajar (CP, ATP, Modul Ajar/RPP, Prota, Promes, KKTP, Buku Nilai).<br>
<strong>2. Supervisi Tenaga Kependidikan:</strong> Pengawasan layanan Tata Usaha, Persuratan, Perpustakaan, Sarpras, dan Operator.<br>
<strong>3. Observasi Kelas Siklus 1:</strong> Pengamatan 4 aspek KBM tatap muka di kelas.
</td>
<td style="border: 1px solid #000; padding: 8px; text-align: center;">Bulan Agustus <br> s.d. <br> Bulan Oktober</td>
</tr>
<tr>
<td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight:bold;">GENAP</td>
<td style="border: 1px solid #000; padding: 8px; text-align: justify;">
<strong>1. Observasi Kelas Siklus 2:</strong> Penguatan KBM berpusat pada siswa, pembelajaran berdiferensiasi, HOTS, & asesmen formatif.<br>
<strong>2. Supervisi Administrasi Tendik Lanjutan:</strong> Evaluasi ketertiban kearsipan, buku induk perpustakaan, & inventarisasi aset.<br>
<strong>3. Rencana Tindak Lanjut (RTL):</strong> Supervisi klinis, pembinaan KKG, dan diseminasi praktik baik.
</td>
<td style="border: 1px solid #000; padding: 8px; text-align: center;">Bulan Februari <br> s.d. <br> Bulan April</td>
</tr>
</tbody>
</table>

<h4>D. Format Matriks Penugasan Individu</h4>
<p style="text-align:justify; margin-bottom:10px;">Format detail <em>(by name by class)</em> mengenai jadwal setiap individu guru tidak dicantumkan pada bab ini karena bersifat fluktuatif menyesuaikan kalender pendidikan. Rincian tabel yang memuat nama guru, jam observasi, dan nama supervisor dapat dilihat secara terperinci pada <strong>LAMPIRAN 2</strong> dalam dokumen ini, atau dapat diunduh secara *real-time* oleh guru melalui portal <em>Dashboard SIAKAD</em> masing-masing.</p>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB VI<br>INSTRUMEN DAN SKEMA PENILAIAN</h3>
<p style="text-align:justify; margin-bottom:15px;">Instrumen supervisi merupakan alat ukur utama yang menentukan objektivitas dan validitas hasil pengawasan. Di MI Miftahul Huda, instrumen tidak lagi berupa lembar ceklis (Ya/Tidak) yang kaku, melainkan menggunakan <strong>Rubrik Penilaian Deskriptif</strong> berbasis skala interval (Skala Likert). Seluruh proses pengisian instrumen dilakukan secara digital/paperless melalui sistem informasi madrasah.</p>

<h4>A. Skala dan Kriteria Rubrik Penilaian</h4>
<p style="text-align:justify; margin-bottom:5px;">Setiap butir indikator dalam instrumen dievaluasi dengan rentang skor 1 hingga 4, yang masing-masing skor memiliki deskripsi kualitatif yang jelas:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Skor 4 (Sangat Baik / Mahir):</strong> Guru menunjukkan kompetensi secara konsisten, kreatif, terstruktur, dan bisa menjadi percontohan (role-model) bagi rekan sejawat.</li>
<li><strong>Skor 3 (Baik / Cakap):</strong> Guru menunjukkan kompetensi sesuai dengan standar yang diharapkan secara umum, namun belum memunculkan inovasi.</li>
<li><strong>Skor 2 (Cukup / Dasar):</strong> Guru baru menunjukkan sebagian kecil kompetensi atau pelaksanaannya belum konsisten di sepanjang KBM.</li>
<li><strong>Skor 1 (Kurang / Belum Terlihat):</strong> Guru sama sekali tidak memunculkan indikator kinerja yang dinilai selama masa observasi maupun pemeriksaan dokumen.</li>
</ul>

<h4>B. Klasifikasi Instrumen Berdasarkan 2 Pilar Fokus</h4>
<p style="text-align:justify; margin-bottom:10px;">Madrasah telah merumuskan 2 paket instrumen standar yang memiliki bobot dan fokus penilaian (Key Performance Indicators) masing-masing, yaitu:</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Paket Instrumen Supervisi Kelas (4 Aspek - 25 Indikator):</strong>
    <ul style="margin-top:4px;">
        <li><em>Persiapan Mengajar (6 Indikator):</em> Validitas modul ajar/RPP, keselarasan CP & ATP, kesiapan media dan LKPD.</li>
        <li><em>Kegiatan Pendahuluan (5 Indikator):</em> Apersepsi, motivasi belajar, pengkondisian ruang dan kesiapan mental siswa, penyampaian tujuan pembelajaran.</li>
        <li><em>Kegiatan Inti Pembelajaran (11 Indikator):</em> Penguasaan materi pokok, pendekatan aktif berpusat pada murid, diferensiasi belajar, HOTS, pemanfaatan TIK, dinamika kelas, dan asesmen formatif.</li>
        <li><em>Kegiatan Penutup (3 Indikator):</em> Refleksi bersama, evaluasi simpulan esensial, penguatan karakter Islami, dan arahan tugas terstruktur.</li>
    </ul>
</li>
<li style="margin-top:10px;"><strong>Paket Instrumen Supervisi Tenaga Pendidik & Tenaga Kependidikan:</strong>
    <ul style="margin-top:4px;">
        <li><em>Instrumen Administrasi Pembelajaran Guru:</em> Ceklis 13+ komponen perangkat ajar (Prota, Promes, CP, ATP, Modul Ajar, KKTP, Buku Nilai, Jurnal, Absensi).</li>
        <li><em>Instrumen Layanan Tata Usaha & Kearsipan:</em> Buku agenda persuratan masuk/keluar, buku ekspedisi, tata kelola dokumen guru dan lembaga.</li>
        <li><em>Instrumen Pengelolaan Perpustakaan Madrasah:</em> Buku induk perpustakaan, klasifikasi koleksi, kartu peminjaman, dan pojok baca madrasah.</li>
        <li><em>Instrumen Pengelolaan Sarana Prasarana & Aset:</em> Buku inventaris barang madrasah, kartu inventaris ruangan (KIR), dan pemeliharaan fasilitas.</li>
    </ul>
</li>
</ol>

<h4>C. Digitalisasi dan Otentikasi Instrumen</h4>
<p style="text-align:justify; margin-bottom:15px;">Semua instrumen di atas sudah tidak menggunakan kertas secara masif (Go-Green/Paperless). Supervisor mengakses <em>E-Rubric</em> melalui perangkat *tablet* atau *smartphone* di dalam kelas. Setelah observasi selesai, sistem akan secara otomatis mengkalkulasi skor akhir (0-100). Bukti fisik (foto/video observasi) dapat langsung diunggah, dan dokumen diotentikasi menggunakan tanda tangan digital/sistem persetujuan dari kedua belah pihak (Supervisor dan Guru yang disupervisi) untuk menjamin transparansi absolut.</p>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB VII<br>PENGOLAHAN DAN ANALISIS HASIL SUPERVISI</h3>
<p style="text-align:justify; margin-bottom:15px;">Tahap krusial pasca-observasi adalah pengolahan data. Agar hasil supervisi tidak hanya berakhir sebagai tumpukan arsip, madrasah menerapkan sistem pengolahan dan analisis data <em>(Data Analytics)</em> secara terpusat untuk memetakan rapor kinerja guru secara objektif, komprehensif, dan *real-time*.</p>

<h4>A. Mekanisme Komputasi dan Konversi Skor</h4>
<p style="text-align:justify; margin-bottom:10px;">Nilai mentah (raw score) yang didapatkan dari akumulasi Skala Likert (1-4) pada setiap instrumen akan dikonversi ke dalam skala seratus (0-100). Rumus komputasi yang digunakan adalah:</p>
<div style="background:#f8fafc; padding:15px; border:1px solid #cbd5e1; text-align:center; margin-bottom:15px; font-weight:bold; font-family:monospace; font-size:14px;">
NILAI AKHIR = (Total Skor Perolehan / Total Skor Maksimal) &times; 100
</div>
<p style="text-align:justify; margin-bottom:15px;">Hasil akhir ini akan diproses secara otomatis oleh sistem SIAKAD menjadi sebuah *Dashboard Analytics* yang menampilkan grafik radar (Spider Web Chart) kompetensi guru, sehingga profil keunggulan dan kelemahan dapat terlihat secara visual.</p>

<h4>B. Predikat dan Kategorisasi Kinerja</h4>
<p style="text-align:justify; margin-bottom:5px;">Skor Akhir (0-100) tersebut kemudian diklasifikasikan ke dalam 4 (empat) predikat kinerja yang menentukan arah pembinaan selanjutnya:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>91 - 100 [AMAT BAIK / A]:</strong> Guru melampaui standar nasional. <em>Tindak lanjut:</em> Diberikan apresiasi (reward), dijadikan <em>Role-Model</em> atau Mentor Sejawat, serta direkomendasikan untuk promosi kepangkatan/jabatan.</li>
<li><strong>81 - 90 [BAIK / B]:</strong> Guru memenuhi standar kelayakan secara umum. <em>Tindak lanjut:</em> Dipertahankan kinerjanya dan didorong untuk melakukan inovasi lebih lanjut melalui kelompok kerja guru (KKG).</li>
<li><strong>71 - 80 [CUKUP / C]:</strong> Guru menunjukkan performa di bawah ekspektasi pada beberapa modul krusial. <em>Tindak lanjut:</em> Wajib menyusun komitmen perbaikan mutu dan mengikuti pembinaan mandiri dengan tenggat waktu.</li>
<li><strong>&lt; 71 [KURANG / D]:</strong> Guru mengalami kendala pedagogik/manajerial yang serius. <em>Tindak lanjut:</em> Wajib menerima Pendampingan Khusus (Coaching Clinic) intensif oleh Kepala Madrasah, dan dikenakan Supervisi Ulang <em>(Re-Supervision)</em> maksimal 14 hari kerja.</li>
</ul>

<h4>C. Analisis Diagnostik (Kekuatan dan Kelemahan)</h4>
<p style="text-align:justify; margin-bottom:15px;">Selain angka matematis, Kepala Madrasah dibantu sistem akan melakukan Analisis Diagnostik terhadap <em>Anecdotal Record</em> (catatan kualitatif). Analisis ini bertujuan untuk memetakan dua hal:<br>
1. <strong>Identifikasi Kekuatan (Strengths):</strong> Praktik baik <em>(Best Practice)</em> apa yang secara unik berhasil diterapkan oleh guru di kelas.<br>
2. <strong>Identifikasi Kelemahan (Weaknesses):</strong> Menentukan akar masalah <em>(Root Cause)</em> mengapa suatu indikator gagal dicapai. Misalnya, nilai evaluasi siswa rendah mungkin bukan karena soalnya sulit, tetapi karena guru belum terbiasa menerapkan metode <em>Deep Learning</em>.</p>

<h4>D. Rekapitulasi Kinerja Tingkat Madrasah</h4>
<p style="text-align:justify; margin-bottom:15px;">Pada akhir semester, seluruh nilai individu guru akan diagregasi menjadi <strong>Profil Mutu Madrasah</strong>. Jika analisis menunjukkan bahwa rata-rata nilai "Pemanfaatan Teknologi" seluruh guru berada di kategori "Cukup", maka Kepala Madrasah menjadikan hal tersebut sebagai dasar Rencana Kerja Anggaran Madrasah (RKAM) tahun berikutnya, misalnya dengan mengadakan IHT (In-House Training) Literasi Digital secara masal.</p>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB VIII<br>TINDAK LANJUT HASIL SUPERVISI</h3>
<p style="text-align:justify; margin-bottom:15px;">Pelaksanaan supervisi akademik dan manajerial pada hakikatnya tidak bermakna jika tidak diiringi dengan program tindak lanjut yang nyata, terukur, dan berdampak langsung pada perbaikan kualitas pembelajaran. Di MI Miftahul Huda, hasil pengolahan data pada BAB VII (yang telah dikonversi menjadi kategori Amat Baik, Baik, Cukup, dan Kurang) secara otomatis menjadi pijakan (baseline data) dalam perumusan Rencana Tindak Lanjut (RTL). Konsep tindak lanjut di madrasah ini tidak mengedepankan pendekatan punitif (pemberian sanksi yang menjatuhkan mental), melainkan mengadopsi paradigma andragogi (pembelajaran orang dewasa) yang berpusat pada perbaikan berkelanjutan <em>(Continuous Quality Improvement)</em>.</p>

<h4>A. Siklus dan Paradigma Tindak Lanjut</h4>
<p style="text-align:justify; margin-bottom:10px;">Alur pembinaan kinerja guru di madrasah diatur menggunakan siklus *coaching* sistematis yang wajib dilalui tanpa terkecuali. Tahapan tersebut dirumuskan sebagai berikut:</p>
<div style="background:#f0f0f0; padding:15px; border-left:5px solid #3b82f6; text-align:center; margin-bottom:20px; font-weight:bold; letter-spacing:1px; font-size:13px;">
SUPERVISI &rarr; DIAGNOSIS TEMUAN &rarr; REFLEKSI DIRI &rarr; COACHING CLINIC &rarr; RENCANA PERBAIKAN &rarr; SUPERVISI ULANG
</div>
<p style="text-align:justify; margin-bottom:15px;">Paradigma yang dibangun adalah <em>Growth Mindset</em> (Pola Pikir Bertumbuh). Guru yang mendapatkan predikat rendah didorong untuk memandang evaluasi sebagai "cermin" profesionalisme, bukan sebagai "vonis" kegagalan. Oleh karena itu, tindak lanjut dirancang secara spesifik, berjangka waktu yang jelas, dan dipantau menggunakan *Coaching Log* (Buku Jurnal Pendampingan Guru).</p>

<h4>B. Matriks dan Program Tindak Lanjut Berdasarkan Klasifikasi Kinerja</h4>
<p style="text-align:justify; margin-bottom:10px;">Program tindak lanjut tidak bersifat "pukul rata" (one size fits all), melainkan sangat personal (diferensiasi pembinaan) menyesuaikan dengan level kompetensi masing-masing guru paska-observasi. Berikut adalah matriks intervensi yang diberlakukan:</p>

<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Bagi Guru Berpredikat "AMAT BAIK" (Skor 91-100)</strong><br>
Guru pada level ini telah mencapai tahap <em>Mastery</em>. Tindak lanjut yang diberikan bersifat apresiatif dan pemberdayaan <em>(Empowerment)</em>:<br>
- Diberikan sertifikat penghargaan internal atau *reward* kinerja tahunan dari Kepala Madrasah/Yayasan.<br>
- Diangkat menjadi <em>Peer-Coach</em> (Tutor Sebaya) bagi guru lain yang membutuhkan bimbingan.<br>
- Direkomendasikan untuk mewakili madrasah dalam ajang Guru Berprestasi tingkat Kabupaten/Provinsi.<br>
- Dilibatkan secara aktif sebagai Tim Pengembang Kurikulum Madrasah (TPKM) pada tahun ajaran berikutnya.
</li>
<li style="margin-top:10px;"><strong>Bagi Guru Berpredikat "BAIK" (Skor 81-90)</strong><br>
Guru pada level ini memenuhi standar kompetensi lulusan, namun belum mencapai tingkat inovasi maksimal. Intervensi yang diberikan bersifat pengayaan <em>(Enrichment)</em>:<br>
- Diminta mempertahankan praktik baik yang sudah ada.<br>
- Didelegasikan untuk mengikuti <em>In-House Training (IHT)</em>, *Workshop*, atau KKG guna memicu lahirnya ide-ide inovatif (seperti mendesain media interaktif atau mencoba <em>Deep Learning</em>).<br>
- Diberi target untuk menciptakan minimal 1 (satu) Penelitian Tindakan Kelas (PTK) setiap semester sebagai syarat perbaikan mutu berkelanjutan.
</li>
<li style="margin-top:10px;"><strong>Bagi Guru Berpredikat "CUKUP" (Skor 71-80)</strong><br>
Guru pada level ini masih memiliki celah (gap) kompetensi yang cukup signifikan, biasanya terkait dengan miskonsepsi penerapan kurikulum merdeka atau kurangnya manajemen waktu di kelas. Intervensi yang diberikan bersifat perbaikan <em>(Corrective)</em>:<br>
- Pemanggilan oleh Wakil Kepala Bidang Kurikulum untuk menyelaraskan RPP dan membedah kelemahan di instrumen.<br>
- Penugasan <em>Peer-Mentoring</em>: Guru diwajibkan melakukan observasi silang (masuk ke kelas guru model yang berpredikat Amat Baik) untuk belajar secara langsung *best-practice* di lapangan.<br>
- Diwajibkan menyusun Rencana Tindak Lanjut (RTL) tertulis yang berisi komitmen perbaikan dalam 30 hari ke depan.
</li>
<li style="margin-top:10px;"><strong>Bagi Guru Berpredikat "KURANG" (Skor &lt; 71)</strong><br>
Guru pada level ini memerlukan penanganan yang masif dan intensif <em>(Clinical Intervention)</em>. Tahapan pembinaan yang dilakukan sangat ketat:<br>
- <strong>Coaching Clinic:</strong> Pemanggilan khusus 4 mata secara tertutup oleh Kepala Madrasah. Tujuannya bukan untuk memarahi, melainkan membedah akar masalah secara psikologis dan pedagogis (misalnya: apakah guru mengalami kelelahan mental/burnout, beban ganda, atau ketidaktahuan teknologi).<br>
- <strong>Pakta Integritas Mutu:</strong> Penandatanganan komitmen untuk mengikuti bimbingan intensif penyusunan perangkat ajar selama 1 minggu penuh di bawah pengawasan waka kurikulum.<br>
- <strong>Supervisi Ulang (Re-Supervision):</strong> Wajib dilaksanakan selambat-lambatnya 14 (empat belas) hari kerja setelah sesi *coaching*.
</li>
</ol>

<h4>C. Prosedur Pelaksanaan Supervisi Ulang (Re-Supervision)</h4>
<p style="text-align:justify; margin-bottom:15px;">Supervisi ulang merupakan *stress-test* kedua untuk membuktikan apakah proses *coaching* berhasil membawa dampak perubahan atau tidak. Prosedurnya adalah sebagai berikut:<br>
1. Guru menyusun ulang Modul Ajar dan instrumen asesmen yang direvisi sesuai masukan supervisor.<br>
2. Supervisor (seringkali langsung oleh Kepala Madrasah sendiri) melakukan observasi kelas kedua.<br>
3. Apabila pada observasi kedua guru tersebut menunjukkan peningkatan nilai menuju kategori minimal "Cukup", maka masa pendampingan khusus dinyatakan selesai.<br>
4. Apabila hasil observasi kedua guru tetap berada pada level "Kurang", maka Kepala Madrasah akan menerbitkan Surat Peringatan (SP 1) Pembinaan Pegawai, dan kasus ini akan diteruskan ke tingkat Yayasan/Kementerian Agama untuk ditinjau kelayakannya sebagai pendidik di madrasah tersebut.</p>


<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB IX<br>MONITORING DAN EVALUASI PROGRAM SUPERVISI</h3>
<p style="text-align:justify; margin-bottom:15px;">Program Supervisi Akademik dan Manajerial merupakan sebuah sistem yang harus dijaga kualitas dan integritasnya. Jika supervisor melakukan kelalaian atau sistem SIAKAD mengalami anomali, maka hasil *rating* guru akan menjadi bias dan tidak valid. Oleh karena itu, Kepala Madrasah dibantu oleh Pengawas Madrasah (sebagai eksternal auditor) melaksanakan program Monitoring dan Evaluasi (Monev) secara komprehensif pada akhir semester.</p>

<h4>A. Objek dan Dimensi Evaluasi Program</h4>
<p style="text-align:justify; margin-bottom:5px;">Monev program supervisi tidak menilai guru, melainkan menilai sistem dan para penilai itu sendiri. Dimensi yang dievaluasi mencakup:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Evaluasi Kepatuhan Jadwal (Schedule Compliance):</strong> Membandingkan data matriks rencana supervisi (planned) di BAB V dengan realisasi aktual di lapangan. Apakah ada guru yang terlewat disupervisi? Apakah supervisor sering menunda jadwal karena alasan kesibukan pribadi?</li>
<li><strong>Evaluasi Validitas Instrumen (Instrument Reliability):</strong> Mengkaji efektivitas 12 Modul Instrumen yang digunakan. Apakah rubrik terlalu rumit untuk dipahami? Apakah terdapat bias indikator yang merugikan guru tertentu? Temuan ini akan menjadi dasar revisi butir instrumen di tahun ajaran berikutnya.</li>
<li><strong>Evaluasi Integritas Supervisor (Observer Objectivity):</strong> Menganalisis *bell-curve* atau distribusi nilai yang diberikan oleh setiap tim penilai. Jika terdeteksi seorang supervisor secara tidak wajar memberikan nilai "Amat Baik" (Skor 100) kepada seluruh guru binaannya tanpa catatan anekdotal (Halo Effect), maka Kepala Madrasah berhak membatalkan hasil tersebut dan menegur keras supervisor yang bersangkutan demi menjaga *Marwah* objektivitas pendidikan.</li>
<li><strong>Evaluasi Ketercapaian Target (KPI Achievement):</strong> Mengukur IKU (Indikator Kinerja Utama) madrasah. Misalnya, jika target awal adalah 85% guru mendapat predikat "Baik", namun realisasinya hanya 60%, maka Kepala Madrasah harus mencari akar penyebab kegagalan makro tersebut.</li>
</ul>

<h4>B. Instrumen Evaluasi dan Pelaporan Monev</h4>
<p style="text-align:justify; margin-bottom:15px;">Pelaksanaan Monev diiringi dengan penyebaran angket anonim (Survey Kepuasan) kepada seluruh dewan guru untuk menilai kinerja para supervisor. Pertanyaannya meliputi kenyamanan saat diobservasi, kejelasan umpan balik yang diberikan, dan relevansi *coaching* terhadap kebutuhan mengajar mereka. Seluruh hasil temuan Monev dirangkum dalam satu Dokumen Rapat Kerja (Raker) Semesteran yang dipimpin langsung oleh Kepala Madrasah.</p>


<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">BAB X<br>PELAPORAN DAN PENGARSIPAN</h3>
<p style="text-align:justify; margin-bottom:15px;">Pelaporan merupakan muara dari seluruh siklus program supervisi akademik dan manajerial. Laporan disusun bukan semata-mata sebagai formalitas birokrasi, melainkan sebagai dokumen *legal-standing* yang merekam jejak mutu pendidikan, bahan dasar penyusunan Rencana Kerja Anggaran Madrasah (RKAM) tahun berikutnya, serta jaminan transparansi akuntabilitas publik.</p>

<h4>A. Format dan Substansi Laporan Akhir</h4>
<p style="text-align:justify; margin-bottom:5px;">Laporan Akhir Supervisi dicetak secara utuh di akhir tahun ajaran (atau akhir semester) yang mencakup esensi berikut:</p>
<ul style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Buku Induk Program:</strong> Yakni dokumen ini (Mulai dari Halaman Cover hingga BAB X).</li>
<li><strong>Data Rekapitulasi Analytics:</strong> <em>Print-out</em> langsung dari SIAKAD yang menampilkan distribusi *rating* guru (Diagram Lingkaran), pemetaan kompetensi terkuat dan terlemah madrasah (Diagram Batang), dan rata-rata skor per bidang studi.</li>
<li><strong>Dokumen Tindak Lanjut (RTL):</strong> Bukti fisik pelaksanaan *Coaching Clinic*, absen IHT, dan hasil Supervisi Ulang.</li>
<li><strong>Lampiran Otentik:</strong> Berupa berkas instrumen kosong, SK tim penilai, daftar hadir sosialisasi, hingga cetakan lembar observasi yang telah ditandatangani secara digital (E-Sign) oleh kedua belah pihak, lengkap dengan *time-stamp* pelaksanaan.</li>
</ul>

<h4>B. Distribusi Eksternal dan Internal</h4>
<p style="text-align:justify; margin-bottom:5px;">Buku laporan ini diekspor ke dalam format PDF dan dicetak *hardcopy* dengan jilid *hard-cover* untuk didistribusikan kepada pemangku kepentingan (Stakeholders):</p>
<ol style="text-align:justify; margin-bottom:15px; margin-top:0;">
<li><strong>Pengawas Madrasah (Kementerian Agama Kabupaten/Kota):</strong> Sebagai portofolio wajib bagi Kepala Madrasah saat menghadapi Penilaian Kinerja Kepala Madrasah (PKKM) tahunan dan 4-tahunan. Pengawas akan memverifikasi keselarasan antara program yang direncanakan dengan bukti pelaksanaan di lapangan.</li>
<li><strong>Badan Akreditasi Nasional (BAN-S/M):</strong> Menjadi dokumen pamungkas (Master Document) pada pengisian instrumen akreditasi, khususnya terkait Standar Pendidik dan Tenaga Kependidikan, serta Standar Proses.</li>
<li><strong>Yayasan Penyelenggara Pendidikan (Bagi Madrasah Swasta):</strong> Digunakan sebagai parameter strategis dalam penentuan anggaran peningkatan SDM (Pelatihan, Bimtek), pemberian insentif berprestasi, maupun keputusan rotasi dan mutasi kepegawaian.</li>
<li><strong>Arsip Sistem Penjaminan Mutu Internal (SPMI):</strong> Disimpan secara permanen (baik fisik di Ruang Kepala Madrasah maupun digital di *Cloud* SIAKAD) sebagai bank data penjaminan mutu madrasah lintas generasi.</li>
</ol>

<p style="text-align:justify; margin-bottom:15px; font-weight:bold; text-align:center; margin-top:60px;">
--- BUKU PROGRAM SUPERVISI SELESAI ---
</p>

<div style="page-break-before:always;"></div>
<h3 style="text-align:center;">LAMPIRAN - LAMPIRAN DOKUMEN</h3>
<p style="text-align:justify; margin-bottom:15px;">Dokumen fisik dan digital pendukung yang melekat dan tidak terpisahkan dari Buku Program Supervisi ini dapat diakses pada bundel lampiran map terpisah atau diunduh secara digital melalui *Dashboard Supervisi SIAKAD*, yang secara berurutan terdiri dari:</p>
<ul style="text-align:justify; line-height:1.8;">
<li><strong>Lampiran 1:</strong> Salinan Surat Keputusan (SK) Kepala Madrasah tentang Susunan Tim Pelaksana Supervisi Akademik dan Manajerial Tahun Ajaran Berjalan.</li>
<li><strong>Lampiran 2:</strong> Matriks Jadwal Lengkap Supervisi (*By Name By Class*) Semester Ganjil & Genap.</li>
<li><strong>Lampiran 3:</strong> Blangko Cetak Kosong & Rubrik Kriteria Indikator Lengkap (12 Modul Supervisi).</li>
<li><strong>Lampiran 4:</strong> Data Agregat / Rekapitulasi Analytics Hasil Akhir Supervisi Tingkat Madrasah.</li>
<li><strong>Lampiran 5:</strong> Berita Acara, Notulensi, dan Daftar Hadir Rapat Sosialisasi Program Supervisi ke Dewan Guru.</li>
<li><strong>Lampiran 6:</strong> Jurnal Pendampingan Pribadi (*Coaching Log*) dan Lembar Catatan Anekdotal Guru.</li>
<li><strong>Lampiran 7:</strong> Format Baku Rencana Tindak Lanjut (RTL) Pasca-Observasi (Untuk Predikat Cukup/Kurang).</li>
<li><strong>Lampiran 8:</strong> Dokumentasi Visual berupa kolase Foto dan Tautan Video (URL) Kegiatan Observasi Kelas & Sesi Wawancara Konseling.</li>
</ul>
    </div> <!-- END OF .content-box -->
    </div> <!-- END OF .page-content-wrapper -->
    </div> <!-- END OF .document-wrapper -->

    <!-- The signature block at the end is removed since it's now in the Lembar Pengesahan -->
</body>
</html>
