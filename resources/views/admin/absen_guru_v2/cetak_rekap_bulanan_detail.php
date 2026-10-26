<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Bulanan Guru Detail</title>
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
        
        .guru-info { width: 100%; margin-bottom: 10px; border-collapse: collapse; font-size: 10pt; }
        .guru-info td { padding: 2px; vertical-align: top; }
        .guru-info td:first-child { width: 100px; }
        .guru-info td:nth-child(2) { width: 10px; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 4px; }
        .table-data th { text-align: center; font-weight: bold; }
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
            <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Dokumen
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

        <table class="guru-info">
            <tr>
                <td>Nama Guru</td>
                <td>:</td>
                <td style="font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($guruDetail['nama']); ?></td>
            </tr>
            <tr>
                <td>NPK</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($guruDetail['npk'] ?: ($guruDetail['nip'] ?: '-')); ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($guruDetail['jabatan'] ?: 'Guru'); ?></td>
            </tr>
        </table>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Hari</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 15%;">Jam Masuk</th>
                    <th style="width: 15%;">Jam Pulang</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Buat array data absen dari $detailAbsen berdasarkan tanggal
                $absenData = [];
                foreach ($detailAbsen as $a) {
                    $absenData[$a['tanggal']] = $a;
                }

                $haris = ['Sunday'=>'Minggu', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
                
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
                
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                    $tglDisplay = sprintf('%02d-%02d-%04d', $d, $bulan, $tahun);
                    $namaHari = $haris[date('l', strtotime($tglStr))];
                    
                    // Skip Minggu
                    if (date('w', strtotime($tglStr)) == 0) continue;
                    
                    $isHadir = isset($absenData[$tglStr]);
                    
                    if ($isHadir) {
                        $row = $absenData[$tglStr];
                        $status = $row['status'] ?: 'Hadir';
                        $masuk = $row['waktu_masuk'] ? substr($row['waktu_masuk'], 0, 5) : '-';
                        $pulang = $row['waktu_keluar'] ? substr($row['waktu_keluar'], 0, 5) : '-';
                        $ket = $row['keterangan'] ?: '-';
                    } else {
                        $status = 'Tidak Ada Jam';
                        $masuk = '-';
                        $pulang = '-';
                        $ket = '-';
                    }
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $d; ?></td>
                        <td class="text-center"><?php echo $tglDisplay; ?></td>
                        <td><?php echo $namaHari; ?></td>
                        <td class="text-center" style="<?php echo $status != 'Hadir' && $status != 'Tidak Ada Jam' ? 'font-weight:bold;' : ''; ?>"><?php echo $status; ?></td>
                        <td class="text-center"><?php echo $masuk; ?></td>
                        <td class="text-center"><?php echo $pulang; ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($ket); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        
        <table style="width: 100%; margin-top: 30px;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%; text-align: center;">
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
