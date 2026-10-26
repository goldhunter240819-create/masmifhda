<?php
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs((float)$angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
        }
        return $terbilang;
    }
}
// Resolve the authorized bendahara name dynamically
$resolvedBendahara = '';
$resolvedNip = '';
$dbConnection = isset($db) ? $db : (isset($core_db) ? $core_db : null);
if ($dbConnection && isset($p['jenis_pembayaran'])) {
    try {
        // 1. Ambil kategori pembayaran
        $stmtKat = $dbConnection->prepare("SELECT kategori FROM keuangan_komite_jenis WHERE nama_tagihan = ? LIMIT 1");
        $stmtKat->execute([$p['jenis_pembayaran']]);
        $katRow = $stmtKat->fetch(PDO::FETCH_ASSOC);
        if ($katRow) {
            $kategori = $katRow['kategori'];
            // 2. Ambil guru penanggung jawab kategori tersebut
            $stmtGuru = $dbConnection->prepare("
                SELECT g.nama, g.nip 
                FROM keuangan_komite_kategori kk
                JOIN guru g ON kk.guru_id = g.id
                WHERE kk.nama_kategori = ? LIMIT 1
            ");
            $stmtGuru->execute([$kategori]);
            $guruRow = $stmtGuru->fetch(PDO::FETCH_ASSOC);
            if ($guruRow) {
                $resolvedBendahara = $guruRow['nama'];
                $resolvedNip = $guruRow['nip'] ?: '';
            }
        }

        // Fallback ke akses pembayaran jika tidak ditemukan lewat kategori
        if (empty($resolvedBendahara)) {
            $aksesQuery = $dbConnection->query("
                SELECT a.akses_pembayaran, g.nama, g.nip 
                FROM keuangan_akses a 
                JOIN guru g ON a.guru_id = g.id
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($aksesQuery as $ak) {
                $arr = json_decode($ak['akses_pembayaran'], true) ?: [];
                foreach ($arr as $item) {
                    if (strcasecmp(trim($item), trim($p['jenis_pembayaran'])) === 0) {
                        $resolvedBendahara = $ak['nama'];
                        $resolvedNip = $ak['nip'] ?: '';
                        break 2;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Fallback silently if query fails
    }
}

if (!empty($resolvedBendahara)) {
    $adminNama = $resolvedBendahara;
} elseif (empty($adminNama)) {
    $adminNama = 'Bendahara Madrasah';
}

function formatTanggalIndonesia($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    $tgl = date('d', $timestamp);
    $bln = (int)date('m', $timestamp);
    $thn = date('Y', $timestamp);
    
    return $tgl . ' ' . $bulanIndo[$bln] . ' ' . $thn;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - <?php echo htmlspecialchars($p['nama']); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700;800&display=swap');

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 30px;
        }

        /* Container of receipt */
        .receipt-container {
            width: 880px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .receipt-paper-wrapper {
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-sizing: border-box;
            display: block;
            width: 100%;
        }

        /* The actual printable physical receipt sheet */
        .receipt-paper {
            width: 100%;
            height: 380px;
            border: 3px double #0f172a;
            border-radius: 6px;
            box-sizing: border-box;
            display: flex;
            position: relative;
            background: #fafcfc;
            background-image: linear-gradient(135deg, rgba(230, 242, 245, 0.25) 0%, rgba(255, 255, 255, 0.5) 100%);
            overflow: hidden;
        }

        /* Main Receipt */
        .receipt-main {
            flex: 1;
            padding: 25px 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Header & Kop */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }
        .receipt-school {
            display: flex;
            align-items: center;
            max-width: 75%;
        }
        .receipt-logo {
            width: 55px;
            height: 55px;
            margin-right: 15px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .receipt-school-details {
            line-height: 1.3;
        }
        .receipt-school-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.2px;
        }
        .receipt-school-addr {
            font-size: 9px;
            margin: 3px 0 0 0;
            color: #475569;
        }
        .receipt-meta {
            text-align: right;
            font-size: 12px;
        }
        .receipt-no {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 15px;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            background: #fff;
            border-radius: 4px;
        }

        /* Title */
        .receipt-title-container {
            text-align: center;
            margin: 12px 0;
        }
        .receipt-title {
            font-family: 'Georgia', serif;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px solid #0f172a;
            display: inline-block;
            padding: 0 20px 2px 20px;
            margin: 0;
            color: #0f172a;
        }

        /* Receipt Rows */
        .receipt-body {
            margin: 15px 0 20px 0;
        }
        .receipt-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 12px;
        }
        .receipt-label {
            width: 170px;
            font-weight: 700;
            font-size: 12px;
            color: #334155;
            flex-shrink: 0;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .receipt-colon {
            width: 15px;
            font-weight: bold;
            flex-shrink: 0;
            color: #0f172a;
        }
        .receipt-value-container {
            flex: 1;
            border-bottom: 1.5px dotted #64748b;
            padding-bottom: 1px;
            min-height: 18px;
            display: flex;
            align-items: flex-end;
        }
        .receipt-value {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 15px;
            color: #0f172a;
            line-height: 1.2;
        }

        /* Footer Section */
        .receipt-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            position: relative;
        }
        .receipt-amount-box {
            border: 3px double #0f172a;
            padding: 10px 22px;
            font-size: 20px;
            font-weight: 800;
            background: #ffffff;
            font-family: 'Courier New', Courier, monospace;
            color: #0f172a;
            box-shadow: 3px 3px 0px rgba(15, 23, 42, 0.15);
            display: inline-block;
            z-index: 2;
        }

        .receipt-signatures {
            display: flex;
            gap: 40px;
            font-size: 11px;
            z-index: 2;
        }
        .receipt-sig-col {
            text-align: center;
            width: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 95px;
        }
        .receipt-sig-title {
            margin: 0;
            color: #475569;
            font-weight: 600;
        }
        .receipt-sig-name {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        /* Stamp Lunas styling - Placed in the spacious bottom-middle area */
        .receipt-stamp {
            position: absolute;
            bottom: 15px;
            left: 280px;
            transform: rotate(-10deg);
            border: 3px solid rgba(22, 163, 74, 0.65);
            color: rgba(22, 163, 74, 0.65);
            font-weight: 800;
            font-size: 26px;
            padding: 8px 24px;
            text-transform: uppercase;
            border-radius: 8px;
            letter-spacing: 5px;
            pointer-events: none;
            background-color: rgba(250, 252, 252, 0.85);
            box-shadow: 0 0 12px rgba(22, 163, 74, 0.05);
            font-family: 'Impact', 'Arial Black', sans-serif;
            z-index: 10;
            transition: all 0.3s ease;
        }
        .receipt-stamp.belum-lunas {
            border-color: rgba(37, 99, 235, 0.65);
            color: rgba(37, 99, 235, 0.65);
            letter-spacing: 3px;
        }

        /* Action Buttons styling */
        .action-buttons {
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3), 0 4px 6px -4px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2), 0 2px 4px -2px rgba(14, 165, 233, 0.2);
            transition: all 0.2s ease;
        }
        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3), 0 4px 6px -4px rgba(14, 165, 233, 0.3);
            background: linear-gradient(135deg, #0284c7, #075985);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            cursor: pointer;
            background: #ffffff;
            color: #4b5563;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: #f9fafb;
            color: #1f2937;
            border-color: #9ca3af;
        }

        /* Cut Line Divider */
        .cut-line {
            margin: 35px 0;
            border-top: 2px dashed #cbd5e1;
            position: relative;
            text-align: center;
        }
        .cut-line::after {
            content: 'âœ‚ POTONG DI SINI âœ‚';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            padding: 0 15px;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        /* Supplementary Info section */
        .receipt-supplementary {
            padding: 0 10px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
        }
        .info-title {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-title svg {
            color: #3b82f6;
            flex-shrink: 0;
        }

        /* Table styling */
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        }
        .rekap-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: left;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .rekap-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            color: #475569;
        }

        /* Printing logic */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0.5cm;
            }
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                border: none;
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }
            .receipt-paper-wrapper {
                padding: 0 !important;
                background: none !important;
            }
            .receipt-paper {
                border: 2px solid #000000;
                background: #ffffff !important;
                background-image: none !important;
                box-shadow: none;
                page-break-inside: avoid;
                width: 880px !important;
                height: 380px !important;
                transform: scale(0.82) !important; /* Scale down to fit A4 width */
                transform-origin: top left !important;
                margin: 0 !important;
            }
            .receipt-value-container {
                border-bottom: 1.5px dotted #000000 !important;
            }
            .receipt-amount-box {
                border: 3px double #000000 !important;
                box-shadow: none;
            }
            .receipt-stamp {
                background: none !important;
                box-shadow: none;
            }
            .cut-line {
                border-top: 2px dashed #000000 !important;
            }
            .info-card {
                border: 1px solid #000000 !important;
                background: none !important;
                box-shadow: none;
            }
            .rekap-table th {
                background: none !important;
                border: 1px solid #000000 !important;
            }
            .rekap-table td {
                border: 1px solid #000000 !important;
            }
        }
        
        /* Responsive/Mobile/Modal styling */
        @media (max-width: 900px) {
            body {
                padding: 10px;
                background-color: transparent;
                overflow-x: hidden;
            }
            .receipt-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px !important;
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
                overflow: hidden !important;
                margin: 0 auto !important;
                box-sizing: border-box !important;
            }
            .receipt-paper-wrapper {
                padding: 0 !important;
                background: none !important;
            }
            .receipt-paper {
                width: 834px !important; /* Fixed natural layout width */
                flex-shrink: 0 !important;
                transform-origin: top left;
                box-sizing: border-box !important;
            }
        }
    </style>
</head>
<body>

<?php if (!isset($_GET['modal'])): ?>
<div class="no-print action-buttons">
    <button onclick="window.print()" class="btn-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v5"/><rect width="12" height="8" x="6" y="14" rx="1" ry="1"/></svg>
        CETAK SEKARANG
    </button>
        <button onclick="downloadKwitansi()" class="btn-download">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            UNDUH PDF
        </button>
    <button onclick="window.history.back()" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        KEMBALI
    </button>
</div>
<?php if (!empty($is_apk)): ?>
    <div class="no-print" style="text-align: center; margin-bottom: 16px; max-width: 880px; margin-left: auto; margin-right: auto; box-sizing: border-box; padding: 0 10px;">
        <button onclick="downloadKwitansi()" class="btn-download" style="width: 100%; max-width: 400px; font-size: 15px; padding: 13px 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            UNDUH KWITANSI (PDF)
        </button>
    </div>
<?php endif; ?>
<?php endif; ?>

<div class="receipt-container">
    <div class="receipt-paper-wrapper">
        <div class="receipt-paper">
        <!-- Main Receipt -->
        <div class="receipt-main">
            <!-- Stamp Lunas overlay - repositioned to avoid covering main text -->
            <div class="receipt-stamp <?php echo (strtolower($p['status_tagihan']) === 'lunas' || empty($p['status_tagihan'])) ? '' : 'belum-lunas'; ?>">
                <?php echo !empty($p['status_tagihan']) ? strtoupper($p['status_tagihan']) : 'LUNAS'; ?>
            </div>

            <!-- Header -->
            <div class="receipt-header">
                <div class="receipt-school">
                    <?php if(!empty($institusi['logo']) && file_exists(__DIR__.'/../../../public/uploads/logo/'.$institusi['logo'])): ?>
                        <img class="receipt-logo" src="<?php echo base_url('public/uploads/logo/' . $institusi['logo']); ?>">
                    <?php else: ?>
                        <div class="receipt-logo" style="background: #eee; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: bold; border: 1px solid #ddd; border-radius: 4px;">LOGO</div>
                    <?php endif; ?>
                    <div class="receipt-school-details">
                        <h1 class="receipt-school-name"><?php echo isset($institusi['nama']) ? strtoupper($institusi['nama']) : 'MI MIFTAHUL HUDA MATHLA\'UL ANWAR'; ?></h1>
                        <p class="receipt-school-addr">
                            <?php echo isset($institusi['alamat']) ? $institusi['alamat'] : ''; ?>
                            <?php echo isset($institusi['desa']) ? ', '.$institusi['desa'] : ''; ?>
                            <?php echo isset($institusi['kecamatan']) ? ', Kec. '.$institusi['kecamatan'] : ''; ?>
                            <?php echo isset($institusi['kota']) ? ', '.$institusi['kota'] : ''; ?>
                            <?php echo isset($institusi['kodepos']) ? ' '.$institusi['kodepos'] : ''; ?><br>
                            Telp: <?php echo isset($institusi['telepon']) ? $institusi['telepon'] : '-'; ?> | Email: <?php echo isset($institusi['email']) ? $institusi['email'] : '-'; ?>
                        </p>
                    </div>
                </div>
                <div class="receipt-meta">
                    <div class="receipt-no">No: KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>

            <!-- Title -->
            <div class="receipt-title-container">
                <h2 class="receipt-title">Kwitansi Pembayaran</h2>
            </div>

            <!-- Fields -->
            <div class="receipt-body">
                <div class="receipt-row">
                    <span class="receipt-label">Telah terima dari</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value"><?php echo $p['nama']; ?> (NIS: <?php echo $p['nis']; ?>) / Kelas: <?php echo $p['kelas']; ?></span>
                    </div>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Uang Sejumlah</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value" style="font-style: italic; color: #1e3a8a; font-weight: bold;"><?php echo ucwords(terbilang($p['jumlah'])); ?> Rupiah</span>
                    </div>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Untuk Pembayaran</span>
                    <span class="receipt-colon">:</span>
                    <div class="receipt-value-container">
                        <span class="receipt-value"><?php echo $p['jenis_pembayaran']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer of Receipt (Rp and Signatures) -->
            <div class="receipt-footer">
                <div class="receipt-amount-box">
                    Rp <?php echo number_format($p['jumlah'], 0, ',', '.'); ?>,-
                </div>
                
                <div class="receipt-signatures" style="display: flex; align-items: center; gap: 15px; text-align: left;">
                    <!-- QR Code (Kiri) -->
                    <div style="flex-shrink: 0;">
                        <?php 
                        $salt = "M1ft4hulHud4_S3cur3";
                        $token = substr(md5($p['id'] . $salt), 0, 10);
                        
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                        $host = $_SERVER['HTTP_HOST'];
                        $verify_url = $protocol . $host . "/apk/verifikasi_kwitansi.php?id=" . $p['id'] . "&token=" . $token;
                        
                        $qrData = rawurlencode($verify_url);
                        ?>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=<?php echo $qrData; ?>" style="width: 85px; height: 85px; border: 1.5px solid #0f172a; padding: 3px; border-radius: 6px; background: #fff; display: block;" alt="QR Verification">
                    </div>
                    <!-- Signature Text (Kanan) -->
                    <div style="display: flex; flex-direction: column; justify-content: space-between; height: 55px; text-align: left;">
                        <div>
                            <p style="margin: 0; font-size: 11px; font-weight: 700; color: #475569; line-height: 1.2;">Bendahara Madrasah,</p>
                            <span style="font-size: 9px; font-weight: normal; color: #64748b;"><?php echo formatTanggalIndonesia($p['tanggal_bayar']); ?></span>
                        </div>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; color: #0f172a;">
                            <strong><?php echo htmlspecialchars($adminNama); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="<?php echo base_url('public/assets/js/html2canvas.min.js'); ?>"></script>
<script>
function downloadKwitansi() {
    const btn = document.querySelector('.btn-download') || (window.parent ? window.parent.document.getElementById('btnDownload') : null);
    const originalBtnHTML = btn ? btn.innerHTML : '';
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    if (isMobile) {
        // Konfirmasi download di mobile APK
        Swal.fire({
            title: 'Unduh Kwitansi?',
            text: "Kwitansi akan disimpan sebagai gambar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Unduh',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesDownload(btn, originalBtnHTML, isMobile);
            }
        });
        return;
    }
    
    prosesDownload(btn, originalBtnHTML, isMobile);
}

function prosesDownload(btn, originalBtnHTML, isMobile) {
    if (isMobile) {
        // Tunjukkan status loading pada tombol
        if (btn) {
            btn.disabled = true;
            if (btn.id === 'btnDownload') {
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
            } else {
                btn.innerHTML = 'Sedang Mengunduh...';
            }
        }
    }

    const paper = document.querySelector('.receipt-paper-wrapper');
    
    // Gunakan html2canvas untuk menangkap tampilan kwitansi sebagai gambar PNG
    html2canvas(paper, {
        scale: 2, // HD rendering
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const dataUrl = canvas.toDataURL('image/png');
        
        if (isMobile) {
            // Kirim gambar ke server via AJAX POST untuk disimpan sebagai file fisik (.png)
            const formData = new FormData();
            formData.append('id', '<?php echo $p['id']; ?>');
            formData.append('image', dataUrl);

            fetch('/apk/save_kwitansi.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Kembalikan tombol ke kondisi semula
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHTML;
                }

                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Unduhan Berhasil!',
                        text: 'Klik OK untuk membuka gambar kwitansi, lalu sentuh dan tahan (tekan lama) pada gambar untuk menyimpannya ke Galeri HP Anda.',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        // Buka gambar kwitansi di halaman penuh
                        window.location.href = data.url;
                    });
                } else {
                    Swal.fire('Gagal!', "Gagal mengunduh kwitansi: " + data.message, 'error');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHTML;
                }
                console.error("AJAX Error:", err);
                Swal.fire('Error!', "Terjadi kesalahan jaringan saat menyimpan kwitansi.", 'error');
            });
        } else {
            // Normal instant download untuk browser PC/Laptop
            const link = document.createElement('a');
            link.download = 'Kwitansi_KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?>_<?php echo addslashes($p['nama']); ?>.png';
            link.href = dataUrl;
            link.click();
            
            // Success alert for PC
            Swal.fire({
                title: 'Berhasil!',
                text: 'Kwitansi telah berhasil diunduh ke komputer Anda.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }).catch(err => {
        if (isMobile && btn) {
            btn.disabled = false;
            btn.innerHTML = originalBtnHTML;
        }
        console.error("Gagal men-generate kwitansi:", err);
        Swal.fire('Gagal!', "Gagal men-generate gambar kwitansi. Error: " + err.message, 'error');
    });
}
</script>

<?php
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
        }
        return $terbilang;
    }
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function downloadKwitansi() {
    var btn = document.querySelector('.btn-download');
    var originalContent = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = 'Menyiapkan PDF...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
    }

    var paper = document.querySelector('.receipt-paper-wrapper');
    
    // Reset transform sementara agar hasil tangkap layar tidak terdistorsi
    var receiptPaper = document.querySelector('.receipt-paper');
    var oldTransform = '';
    if (receiptPaper) {
        oldTransform = receiptPaper.style.transform;
        receiptPaper.style.transform = 'none';
    }

    html2canvas(paper, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff',
        width: paper.scrollWidth,
        height: paper.scrollHeight
    }).then(function(canvas) {
        // Kembalikan transform
        if (receiptPaper) receiptPaper.style.transform = oldTransform;
        
        // Hitung ukuran PDF sesuai proporsi kwitansi (dalam mm)
        var imgWidth = canvas.width;
        var imgHeight = canvas.height;
        var pdfWidth = 210; // A4 width in mm
        var pdfHeight = (imgHeight * pdfWidth) / imgWidth;
        
        // Buat PDF dengan ukuran pas sesuai kwitansi
        var { jsPDF } = window.jspdf;
        var pdf = new jsPDF({
            orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
            unit: 'mm',
            format: [pdfWidth, pdfHeight]
        });
        
        var imgData = canvas.toDataURL('image/jpeg', 0.95);
        pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
        
        // Convert PDF ke base64 (tanpa prefix)
        var pdfBase64 = pdf.output('datauristring').split(',')[1];
        var filename = 'Kwitansi_KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?>_<?php echo preg_replace('/[^a-zA-Z0-9 ]/', '', $p['nama']); ?>.pdf';
        
        if (btn) btn.innerHTML = 'Mengirim ke server...';

        // Buat URL absolut
        var baseOrigin = window.location.origin;
        var saveUrl = baseOrigin + '/apk/api_save_temp_image.php';

        // Kirim PDF ke server untuk disimpan sebagai file sementara
        fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ base64: pdfBase64, filename: filename, type: 'pdf' })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status === 'success') {
                // Redirect langsung ke file PDF statis
                window.location.href = baseOrigin + '/public/downloads/temp/' + encodeURIComponent(res.filename);
            } else {
                alert('Gagal menyiapkan file: ' + (res.message || 'Unknown error'));
            }
            if (btn) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        })
        .catch(function(e) {
            alert('Gagal terhubung ke server. Pastikan koneksi internet Anda stabil.');
            if (btn) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    }).catch(function(err) {
        // Kembalikan transform
        if (receiptPaper) receiptPaper.style.transform = oldTransform;
        alert('Gagal men-generate PDF kwitansi. Coba lagi.');
        if (btn) {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    });
}

let rotationAngle = 0; // Fixed to normal landscape
let zoomFactor = 1.0;

window.setZoomAndRotation = function(zoom, rotation) {
    zoomFactor = zoom;
    rotationAngle = rotation;
    adjustMobileScale();
}

function adjustMobileScale() {
    const paper = document.querySelector('.receipt-paper');
    if (!paper) return;
    const container = document.querySelector('.receipt-container');
    if (!container) return;
    
    if (window.innerWidth > 900 && rotationAngle === 0 && zoomFactor === 1.0) {
        paper.style.transform = 'none';
        paper.style.marginLeft = '0';
        container.style.height = 'auto';
        return;
    }
    
    const W = 834; // Lock layout original width
    const H = 380; // Lock layout original height
    
    // Width of container minus its padding (10px left + 10px right = 20px)
    const containerWidth = container.clientWidth - 20;
    
    // Calculate base scale depending on rotation orientation
    let baseScale = 1.0;
    if (rotationAngle === 90 || rotationAngle === 270) {
        baseScale = containerWidth / H;
    } else {
        baseScale = containerWidth / W;
    }
    
    // Cap base scale on desktop viewport to avoid huge sizes
    if (window.innerWidth > 900 && baseScale > 1.0) {
        baseScale = 1.0;
    }
    
    const finalScale = baseScale * zoomFactor;
    paper.style.transformOrigin = 'top left';
    
    let visualWidth = 0;
    let visualHeight = 0;
    let transformStr = '';
    let offset = 0;
    
    if (rotationAngle === 0) {
        visualWidth = W * finalScale;
        visualHeight = H * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translateX(${offset}px) rotate(0deg) scale(${finalScale})`;
    } else if (rotationAngle === 90) {
        visualWidth = H * finalScale;
        visualHeight = W * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translateX(${(H * finalScale) + offset}px) rotate(90deg) scale(${finalScale})`;
    } else if (rotationAngle === 180) {
        visualWidth = W * finalScale;
        visualHeight = H * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translate(${(W * finalScale) + offset}px, ${H * finalScale}px) rotate(180deg) scale(${finalScale})`;
    } else if (rotationAngle === 270) {
        visualWidth = H * finalScale;
        visualHeight = W * finalScale;
        offset = (containerWidth - visualWidth) / 2;
        if (offset < 0) offset = 0;
        transformStr = `translate(${offset}px, ${W * finalScale}px) rotate(270deg) scale(${finalScale})`;
    }
    
    paper.style.transform = transformStr;
    paper.style.marginLeft = '0';
    
    // Adjust container height to fit the visual scaled height
    container.style.height = (visualHeight + 20) + 'px';
}

window.addEventListener('resize', adjustMobileScale);
window.addEventListener('load', adjustMobileScale);
document.addEventListener('DOMContentLoaded', adjustMobileScale);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    adjustMobileScale();
}
</script>
</body>
</html>

