<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi Guru Detail - <?php echo htmlspecialchars($guruDetail['nama'] ?? 'Guru'); ?></title>
    <style>
        @page { size: A4 portrait; margin: 6mm 8mm; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 9.5pt; 
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
            background: #0ea5e9; 
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
        .btn-print:hover { background: #0284c7; }
        
        /* A4 Page Container Styling */
        .page-container {
            max-width: 210mm; 
            margin: 15px auto; 
            background: white; 
            padding: 6mm 10mm; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        
        .header-kop { 
            text-align: center; 
            border-bottom: 2.5px solid #000; 
            padding-bottom: 5px; 
            margin-bottom: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            position: relative; 
        }
        .header-kop img { 
            width: 75px; 
            height: auto; 
            position: absolute; 
            left: 0; 
            top: 2px;
        }
        .header-text { 
            flex-grow: 1; 
            line-height: 1.25; 
        }
        .header-text h3 { margin: 0; font-size: 12pt; font-weight: normal; }
        .header-text h2 { margin: 0; font-size: 14pt; font-weight: bold; }
        .header-text p { margin: 0; font-size: 8.5pt; }
        
        .report-title { 
            text-align: center; 
            margin: 8px 0 2px 0; 
            font-weight: bold; 
            font-size: 11pt; 
            text-transform: uppercase; 
            text-decoration: underline; 
            letter-spacing: 0.5px;
        }
        .report-subtitle { 
            text-align: center; 
            margin-bottom: 10px; 
            font-size: 9.5pt; 
            font-weight: bold;
        }
        
        .guru-info { 
            width: 100%; 
            margin-bottom: 10px; 
            border-collapse: collapse; 
            font-size: 9.5pt; 
        }
        .guru-info td { padding: 2px 4px; vertical-align: top; }
        .guru-info td:first-child { width: 120px; font-weight: 600; }
        .guru-info td:nth-child(2) { width: 10px; }
        
        .table-data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
            font-size: 8.5pt; 
        }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 3.5px 5px; 
        }
        .table-data th { 
            text-align: center; 
            font-weight: bold; 
            background-color: #f1f5f9 !important; 
            -webkit-print-color-adjust: exact;
        }
        .table-data td.text-center { text-align: center; }

        .summary-box {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 8.5pt;
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 15px;
            -webkit-print-color-adjust: exact;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; -webkit-print-color-adjust: exact; }
            .page-container { margin: 0; padding: 0; box-shadow: none; max-width: none; width: 100%; }
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <div class="print-toolbar no-print">
        <button class="btn-print" onclick="window.print()">
            <i data-lucide="printer" style="width: 15px; height: 15px;"></i> Cetak Laporan Detail Guru
        </button>
    </div>

    <div class="page-container">
        <?php
            $dbCore = \App\Core\Database::connect('core');
            $kop = null;
            try {
                $kop = $dbCore->query("SELECT * FROM institusi LIMIT 1")->fetch();
            } catch (\Exception $e) {}
            if (!$kop) {
                try {
                    $kop = $dbCore->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch();
                } catch (\Exception $e) {}
            }

            $logoSrc = ($kop && !empty($kop['logo'])) ? '/uploads/' . $kop['logo'] : '/assets/images/logo.png';
            $namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
            $namaMadrasah = $kop['nama'] ?? ($kop['nama_madrasah'] ?? 'MA MIFTAHUL HUDA MATHLA\'UL ANWAR');
            $alamatJalan = $kop['alamat'] ?? ($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo No. 083, Margorahayu');
            $telepon = $kop['telepon'] ?? '081938240819';
            $email = $kop['email'] ?? 'masmiftahulhuda083@gmail.com';

            $kamad = $dbCore->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 OR jabatan LIKE '%Kepala%' LIMIT 1")->fetch();
            $kamadNama = $kamad['nama'] ?? 'AHMAD RIFA\'I, M.Pd';
            $kamadNip = $kamad['nip'] ?? '-';

            $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
            $namaBulan = $bulans[$bulan] ?? $bulan;
        ?>
        <div class="header-kop">
            <img src="<?php echo $logoSrc; ?>" alt="Logo" onerror="this.src='/logo.png'; this.onerror=function(){this.style.display='none'};">
            <div class="header-text">
                <h3 style="font-size: <?php echo $kop['font_yayasan'] ?? 12; ?>pt;"><?php echo htmlspecialchars($namaYayasan); ?></h3>
                <h2 style="font-size: <?php echo $kop['font_madrasah'] ?? 15; ?>pt;"><?php echo htmlspecialchars($namaMadrasah); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($alamatJalan)); ?></p>
                <p>Telp: <?php echo htmlspecialchars($telepon); ?> | Email: <?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>

        <div class="report-title">LAPORAN REKAPITULASI DETAIL KEHADIRAN GURU</div>
        <div class="report-subtitle">Periode Bulan: <?php echo $namaBulan; ?> <?php echo $tahun; ?></div>

        <table class="guru-info">
            <tr>
                <td>Nama Guru</td>
                <td>:</td>
                <td style="font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($guruDetail['nama']); ?></td>
            </tr>
            <tr>
                <td>NIP / NPK</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($guruDetail['nip'] ?: ($guruDetail['npk'] ?: '-')); ?></td>
            </tr>
            <tr>
                <td>Jabatan / Tugas</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($guruDetail['jabatan'] ?: 'Guru'); ?></td>
            </tr>
            <tr>
                <td>Status Kepegawaian</td>
                <td>:</td>
                <td>
                    <?php 
                        $stat = $guruDetail['status_kepegawaian'] ?? 'Honorer';
                        if (!empty($guruDetail['nrg']) || stripos($stat, 'sertifikasi') !== false) {
                            echo htmlspecialchars($stat) . ' (Sertifikasi - Hadir Setiap Hari)';
                        } else {
                            echo htmlspecialchars($stat) . ' (Hadir Sesuai Jadwal KBM)';
                        }
                    ?>
                </td>
            </tr>
        </table>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 14%;">Hari</th>
                    <th style="width: 15%;">Jam Masuk</th>
                    <th style="width: 15%;">Jam Pulang</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 23%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $absenData = [];
                foreach ($detailAbsen as $a) {
                    $absenData[$a['tanggal']] = $a;
                }

                $haris = ['Sunday'=>'Minggu', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
                
                $totHadir = 0;
                $totTerlambat = 0;
                $totSakit = 0;
                $totIzin = 0;
                $totAlpa = 0;
                
                $rowNo = 1;
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                    $tglDisplay = sprintf('%02d/%02d/%04d', $d, $bulan, $tahun);
                    $namaHari = $haris[date('l', strtotime($tglStr))];
                    $isSun = (date('w', strtotime($tglStr)) == 0);
                    
                    if ($isSun) {
                        continue; // Skip hari Minggu agar ringkas 1 halaman penuh
                    }
                    
                    $isHadir = isset($absenData[$tglStr]);
                    
                    if ($isHadir) {
                        $row = $absenData[$tglStr];
                        $st = $row['status'] ?: 'Hadir';
                        
                        $masuk = !empty($row['waktu_masuk']) ? substr($row['waktu_masuk'], 0, 5) : (!empty($row['jam_masuk']) ? substr($row['jam_masuk'], 0, 5) : '-');
                        $pulang = !empty($row['waktu_keluar']) ? substr($row['waktu_keluar'], 0, 5) : (!empty($row['jam_pulang']) ? substr($row['jam_pulang'], 0, 5) : '-');
                        $ket = $row['keterangan'] ?: '-';

                        if ($st === 'Hadir') $totHadir++;
                        elseif ($st === 'Terlambat') $totTerlambat++;
                        elseif ($st === 'Sakit') $totSakit++;
                        elseif (in_array($st, ['Izin', 'Dinas Luar'])) $totIzin++;
                        elseif ($st === 'Alpa') $totAlpa++;
                    } else {
                        $st = 'Tidak Ada Jam';
                        $masuk = '-';
                        $pulang = '-';
                        $ket = '-';
                    }
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $rowNo++; ?></td>
                        <td class="text-center"><?php echo $tglDisplay; ?></td>
                        <td><?php echo $namaHari; ?></td>
                        <td class="text-center"><?php echo $masuk; ?></td>
                        <td class="text-center"><?php echo $pulang; ?></td>
                        <td class="text-center">
                            <?php if ($st === 'Hadir'): ?>
                                <strong style="color: #15803d;">Hadir</strong>
                            <?php elseif ($st === 'Terlambat'): ?>
                                <strong style="color: #b45309;">Terlambat</strong>
                            <?php elseif ($st === 'Sakit'): ?>
                                <strong style="color: #1d4ed8;">Sakit</strong>
                            <?php elseif ($st === 'Izin'): ?>
                                <strong style="color: #7e22ce;">Izin</strong>
                            <?php elseif ($st === 'Alpa'): ?>
                                <strong style="color: #b91c1c;">Alpa</strong>
                            <?php else: ?>
                                <span style="color: #94a3b8;"><?php echo htmlspecialchars($st); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo htmlspecialchars($ket); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Summary Box -->
        <div class="summary-box">
            <span>Rekapitulasi Kehadiran:</span>
            <span style="color:#15803d;">Hadir: <?php echo $totHadir; ?> Hari</span>
            <span style="color:#b45309;">Terlambat: <?php echo $totTerlambat; ?> Hari</span>
            <span style="color:#1d4ed8;">Sakit: <?php echo $totSakit; ?> Hari</span>
            <span style="color:#7e22ce;">Izin: <?php echo $totIzin; ?> Hari</span>
            <span style="color:#b91c1c;">Alpa: <?php echo $totAlpa; ?> Hari</span>
            <span>Total KBM: <?php echo ($totHadir + $totTerlambat + $totSakit + $totIzin + $totAlpa); ?> Hari</span>
        </div>
        
        <table style="width: 100%; margin-top: 15px; font-size: 9.5pt;">
            <tr>
                <td style="width: 50%; text-align: center; line-height: 1.4;">
                    Guru yang Bersangkutan,<br><br><br><br>
                    <strong><u><?php echo htmlspecialchars($guruDetail['nama']); ?></u></strong><br>
                    NIP/NPK: <?php echo htmlspecialchars($guruDetail['nip'] ?: ($guruDetail['npk'] ?: '-')); ?>
                </td>
                <td style="width: 50%; text-align: center; line-height: 1.4;">
                    Mengetahui,<br>
                    Kepala Madrasah<br><br><br><br>
                    <strong><u><?php echo htmlspecialchars($kamadNama); ?></u></strong><br>
                    NIP: <?php echo htmlspecialchars($kamadNip); ?>
                </td>
            </tr>
        </table>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
