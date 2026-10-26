<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Rekomendasi - <?php echo htmlspecialchars($data_surat['nama_guru']); ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .page-a4 {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            box-sizing: border-box;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .kop-surat {
            border-bottom: 4px double #000;
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
        .kop-text h2 { margin: 0; font-size: 14pt; font-weight: normal; }
        .kop-text h1 { margin: 0; font-size: 18pt; font-weight: bold; }
        .kop-text p { margin: 2px 0 0 0; font-size: 10pt; }
        
        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-surat h3 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
        }
        .judul-surat p {
            margin: 5px 0 0 0;
            font-size: 12pt;
        }

        .isi-surat {
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
        }
        
        .tabel-biodata {
            margin-top: 15px;
            margin-bottom: 15px;
            margin-left: 20px;
        }
        .tabel-biodata td {
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }
        .tabel-biodata td:first-child { width: 150px; }

        .ttd-box {
            margin-top: 50px;
            width: 300px;
            float: right;
        }
        .ttd-box p { margin: 0 0 5px 0; }
        .ttd-space { height: 80px; }

        .tembusan {
            margin-top: 100px;
            font-size: 11pt;
            clear: both;
        }

        @media print {
            body { background: #fff; padding: 0; display: block; }
            .page-a4 { box-shadow: none; margin: 0; width: 100%; height: auto; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page-a4">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" class="kop-logo" alt="Logo">
            <div class="kop-text">
                <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h2>
                <h1><?php echo strtoupper(htmlspecialchars($data_surat['nama_sekolah'])); ?></h1>
                <p><?php echo htmlspecialchars($data_surat['alamat_sekolah']); ?></p>
            </div>
            <div style="width:80px;"></div> <!-- Spacer -->
        </div>

        <!-- JUDUL SURAT -->
        <div class="judul-surat">
            <h3><?php echo strtoupper(str_replace('_', ' ', $jenis_surat)); ?></h3>
            <p>Nomor : ..............................</p>
        </div>

        <!-- ISI SURAT DINAMIS DARI TEMPLATE -->
        <div class="isi-surat">
            <?php echo $content_surat; ?>
        </div>

    </div>
</body>
</html>
