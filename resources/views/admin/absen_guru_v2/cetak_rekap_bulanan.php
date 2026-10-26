<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Rekap Bulanan Guru</title>
    <style>
        @page { size: A4 portrait; margin: 5mm; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 10pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            background: #f8fafc; 
        }
        
        /* Print Toolbar Styling */
        .print-toolbar { 
            background: #ffffff; 
            color: #1e293b; 
            padding: 10px; 
            text-align: center; 
            position: sticky; 
            top: 0; 
            z-index: 100;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .btn-print { 
            background: #3b82f6; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            font-size: 12px; 
            font-weight: bold; 
            border-radius: 5px; 
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-print:hover { background: #2563eb; }
        
        /* A4 Page Container Styling */
        .page-container {
            max-width: 210mm; 
            margin: 20px auto; 
            background: white; 
            padding: 5mm; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        
        .header-kop { text-align: center; border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; position: relative; }
        /* Adjusted logo position */
        .header-kop img { width: 85px; height: auto; position: absolute; left: 0; }
        .header-text { flex-grow: 1; line-height: 1.3; }
        .header-text h3 { margin: 0; font-size: 12pt; }
        .header-text h2 { margin: 0; font-size: 14pt; font-weight: bold; }
        .header-text p { margin: 0; font-size: 9pt; }
        
        .report-title { text-align: center; margin: 10px 0; font-weight: bold; font-size: 11pt; text-transform: uppercase; text-decoration: underline; }
        .report-subtitle { text-align: center; margin-top: -5px; margin-bottom: 10px; font-size: 10pt; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 4px; }
        .table-data th { text-align: center; font-weight: bold; background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }
        .table-data td.text-center { text-align: center; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; -webkit-print-color-adjust: exact; }
            .page-container { margin: 0; padding: 0; box-shadow: none; max-width: none; min-height: 0; }
        }
    </style>
    <!-- Include Lucide Icons for the print button -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <div class="print-toolbar no-print">
        <button class="btn-print" onclick="window.print()">
            <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Laporan
        </button>
    </div>

    <div class="page-container">
        <?php
            $dbCore = \App\Core\Database::connect('core');
            $kop = $dbCore->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch();
            $logoSrc = ($kop && $kop['logo']) ? '/assets/images/' . $kop['logo'] : '/assets/images/logo.png';
        ?>
        <div class="header-kop">
            <img src="<?php echo $logoSrc; ?>" alt="Logo" onerror="this.src='/logo.png'; this.onerror=function(){this.style.display='none'};">
            <div class="header-text">
                <h3 style="font-size: <?php echo $kop['font_yayasan'] ?? 16; ?>pt;"><?php echo htmlspecialchars($kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA'); ?></h3>
                <h2 style="font-size: <?php echo $kop['font_madrasah'] ?? 24; ?>pt;"><?php echo htmlspecialchars($kop['nama_madrasah'] ?? 'MADRASAH ALIYAH SUNAN AMPEL'); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu')); ?></p>
                <p>Telp: <?php echo htmlspecialchars($kop['telepon'] ?? '081938240819'); ?> | Email: <?php echo htmlspecialchars($kop['email'] ?? 'masmiftahulhuda083@gmail.com'); ?></p>
            </div>
        </div>

        <div class="report-title">LAPORAN REKAPITULASI ABSENSI GURU</div>
        <?php 
            $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
            $namaBulan = $bulans[$bulan] ?? '';
        ?>
        <div class="report-subtitle">Bulan: <?php echo $namaBulan; ?> <?php echo $tahun; ?></div>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Guru</th>
                    <th style="width: 20%;">Jabatan</th>
                    <th style="width: 8%;">Hadir</th>
                    <th style="width: 8%;">Telat</th>
                    <th style="width: 8%;">Sakit</th>
                    <th style="width: 8%;">Izin</th>
                    <th style="width: 8%;">Alpa</th>
                    <th style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rekap)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding: 20px;">Data Guru tidak ditemukan.</td>
                </tr>
                <?php else: ?>
                    <?php $no=1; foreach($rekap as $r): 
                        $hadir = $r['rekap']['Hadir'] ?? 0;
                        $terlambat = $r['rekap']['Terlambat'] ?? 0;
                        $sakit = $r['rekap']['Sakit'] ?? 0;
                        $izin = $r['rekap']['Izin'] ?? 0;
                        $alpa = $r['rekap']['Alpa'] ?? 0;
                        $total = $hadir + $terlambat + $sakit + $izin + $alpa;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($r['nama']); ?></td>
                        <td><?php echo htmlspecialchars($r['jabatan'] ?? 'Guru'); ?></td>
                        <td class="text-center"><?php echo $hadir; ?></td>
                        <td class="text-center"><?php echo $terlambat; ?></td>
                        <td class="text-center"><?php echo $sakit; ?></td>
                        <td class="text-center"><?php echo $izin; ?></td>
                        <td class="text-center"><?php echo $alpa; ?></td>
                        <td class="text-center" style="font-weight:bold;"><?php echo $total; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <table style="width: 100%; margin-top: 30px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: center;">
                    Mengetahui,<br>
                    Kepala Madrasah<br><br><br><br><br>
                    <b>(.............................................)</b>
                </td>
            </tr>
        </table>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
