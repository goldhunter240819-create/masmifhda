<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi Guru Global - Bulan <?php echo $bulans[$bulan] ?? $bulan; ?> <?php echo $tahun; ?></title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 6mm 8mm; 
        }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 8pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            background: #f8fafc; 
        }
        
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
            padding: 8px 18px; 
            font-size: 12px; 
            font-weight: bold; 
            border-radius: 6px; 
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #0284c7; }
        
        .page-container {
            max-width: 297mm; 
            margin: 15px auto; 
            background: white; 
            padding: 6mm 10mm; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        
        .header-kop { 
            text-align: center; 
            border-bottom: 2.5px solid #000; 
            padding-bottom: 4px; 
            margin-bottom: 8px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            position: relative; 
        }
        .header-kop img { 
            width: 70px; 
            height: auto; 
            position: absolute; 
            left: 0; 
            top: 2px;
        }
        .header-text { 
            flex-grow: 1; 
            line-height: 1.25; 
        }
        .header-text h3 { margin: 0; font-size: 11pt; font-weight: normal; }
        .header-text h2 { margin: 0; font-size: 13pt; font-weight: bold; }
        .header-text p { margin: 0; font-size: 8pt; }
        
        .report-title { 
            text-align: center; 
            margin: 6px 0 2px 0; 
            font-weight: bold; 
            font-size: 11pt; 
            text-transform: uppercase; 
            text-decoration: underline; 
            letter-spacing: 0.5px;
        }
        .report-subtitle { 
            text-align: center; 
            margin-bottom: 8px; 
            font-size: 9pt; 
            font-weight: bold;
        }
        
        table.matrix-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 7.5pt; 
            margin-bottom: 8px; 
        }
        table.matrix-table th, table.matrix-table td { 
            border: 1px solid #000; 
            padding: 3px 2px; 
            text-align: center; 
            line-height: 1.15;
        }
        table.matrix-table th { 
            background-color: #f1f5f9 !important; 
            font-weight: bold; 
            -webkit-print-color-adjust: exact;
        }
        table.matrix-table td.col-nama { 
            text-align: left; 
            padding-left: 5px; 
            font-weight: 600; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }
        table.matrix-table td.col-nip { 
            text-align: center; 
            font-size: 7pt; 
            white-space: nowrap;
        }
        table.matrix-table td.col-jabatan { 
            text-align: left; 
            padding-left: 4px; 
            font-size: 7pt; 
            white-space: nowrap;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Status Badges in Print */
        .lbl-h { font-weight: bold; color: #15803d; }
        .lbl-t { font-weight: bold; color: #b45309; }
        .lbl-s { font-weight: bold; color: #1d4ed8; }
        .lbl-i { font-weight: bold; color: #7e22ce; }
        .lbl-a { font-weight: bold; color: #b91c1c; }
        .lbl-off { color: #cbd5e1; }
        .lbl-sun { background-color: #fef2f2 !important; color: #ef4444; -webkit-print-color-adjust: exact; }

        .legend-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 7.5pt;
            margin-top: 6px;
        }
        .legend-items {
            display: flex;
            gap: 12px;
            font-weight: 600;
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
            <i data-lucide="printer" style="width: 15px; height: 15px;"></i> Cetak Laporan (A4 Landscape)
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

        <div class="report-title">REKAPITULASI DETAIL KEHADIRAN GURU (BULANAN)</div>
        <div class="report-subtitle">
            Periode Bulan: <?php echo $bulans[$bulan] ?? $bulan; ?> <?php echo $tahun; ?> | Tahun Pelajaran 2026/2027
        </div>

        <table class="matrix-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20px;">No</th>
                    <th rowspan="2" style="width: 85px;">NIP / NPK</th>
                    <th rowspan="2" style="width: 150px;">Nama Guru</th>
                    <th rowspan="2" style="width: 75px;">Jabatan</th>
                    <th colspan="<?php echo count($dates_range); ?>">Tanggal Pelaksanaan (Bulan <?php echo $bulans[$bulan] ?? $bulan; ?>)</th>
                    <th colspan="6" style="width: 120px;">Rekapitulasi</th>
                </tr>
                <tr>
                    <?php foreach($dates_range as $dt): 
                        $dayOfWeek = date('w', strtotime($dt));
                        $isSun = ($dayOfWeek == 0);
                    ?>
                        <th style="width: 18px; <?php echo $isSun ? 'background:#fee2e2 !important; color:#dc2626;' : ''; ?>">
                            <?php echo date('j', strtotime($dt)); ?>
                        </th>
                    <?php endforeach; ?>
                    <th style="width: 20px; background:#dcfce7 !important; color:#15803d;">H</th>
                    <th style="width: 20px; background:#fef3c7 !important; color:#b45309;">T</th>
                    <th style="width: 20px; background:#eff6ff !important; color:#1d4ed8;">S</th>
                    <th style="width: 20px; background:#f3e8ff !important; color:#7e22ce;">I</th>
                    <th style="width: 20px; background:#fee2e2 !important; color:#b91c1c;">A</th>
                    <th style="width: 24px; background:#f1f5f9 !important;">Tot</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matrixGuru)): ?>
                <tr>
                    <td colspan="<?php echo 10 + count($dates_range); ?>" style="padding: 20px; text-align: center; color: #94a3b8;">
                        Data guru atau absensi belum tersedia untuk periode bulan ini.
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($matrixGuru as $mg): 
                        $rk = $mg['rekap'];
                        $totKehadiran = $rk['Hadir'] + $rk['Terlambat'] + $rk['Sakit'] + $rk['Izin'] + $rk['Alpa'];
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="col-nip"><?php echo htmlspecialchars($mg['nip'] ?: ($mg['npk'] ?: '-')); ?></td>
                        <td class="col-nama"><?php echo htmlspecialchars($mg['nama']); ?></td>
                        <td class="col-jabatan"><?php echo htmlspecialchars($mg['jabatan'] ?? 'Guru'); ?></td>
                        
                        <?php foreach($dates_range as $dt): 
                            $dayOfWeek = date('w', strtotime($dt));
                            $isSun = ($dayOfWeek == 0);
                            $st = $mg['harian'][$dt] ?? '';
                            
                            $label = '-';
                            $class = 'lbl-off';
                            
                            if ($isSun) {
                                $label = 'M';
                                $class = 'lbl-sun';
                            } elseif ($st === 'Hadir') {
                                $label = 'H';
                                $class = 'lbl-h';
                            } elseif ($st === 'Terlambat') {
                                $label = 'T';
                                $class = 'lbl-t';
                            } elseif ($st === 'Sakit') {
                                $label = 'S';
                                $class = 'lbl-s';
                            } elseif (in_array($st, ['Izin', 'Dinas Luar'])) {
                                $label = 'I';
                                $class = 'lbl-i';
                            } elseif ($st === 'Alpa') {
                                $label = 'A';
                                $class = 'lbl-a';
                            }
                        ?>
                            <td class="<?php echo $class; ?>" style="<?php echo $isSun ? 'background:#fef2f2;' : ''; ?>">
                                <?php echo $label; ?>
                            </td>
                        <?php endforeach; ?>

                        <td style="font-weight: bold; color: #15803d;"><?php echo $rk['Hadir']; ?></td>
                        <td style="font-weight: bold; color: #b45309;"><?php echo $rk['Terlambat']; ?></td>
                        <td style="font-weight: bold; color: #1d4ed8;"><?php echo $rk['Sakit']; ?></td>
                        <td style="font-weight: bold; color: #7e22ce;"><?php echo $rk['Izin']; ?></td>
                        <td style="font-weight: bold; color: #b91c1c;"><?php echo $rk['Alpa']; ?></td>
                        <td style="font-weight: 800; background: #f8fafc;"><?php echo $totKehadiran; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="legend-box">
            <div class="legend-items">
                <span><strong>Keterangan:</strong></span>
                <span class="lbl-h">H = Hadir</span>
                <span class="lbl-t">T = Terlambat</span>
                <span class="lbl-s">S = Sakit</span>
                <span class="lbl-i">I = Izin / Dinas Luar</span>
                <span class="lbl-a">A = Alpa (Jadwal Kosong)</span>
                <span style="color:#ef4444;">M = Hari Minggu</span>
                <span class="lbl-off">- = Libur / Tidak Ada Jam</span>
            </div>
            
            <div style="text-align: right; font-size: 7pt; color: #64748b;">
                Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?> | Sistem MASMIFHDA
            </div>
        </div>

        <table style="width: 100%; margin-top: 15px; font-size: 8.5pt;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%; text-align: center; line-height: 1.4;">
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
