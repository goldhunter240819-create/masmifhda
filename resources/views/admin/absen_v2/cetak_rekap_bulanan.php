<?php
// Core Data & Kop
$dbCore = \App\Core\Database::connect('core');
$kop = null;
try {
    $kop = $dbCore->query("SELECT * FROM institusi LIMIT 1")->fetch();
} catch (\Exception $e) {}

$logoSrc = ($kop && !empty($kop['logo'])) ? '/uploads/' . $kop['logo'] : '/assets/images/logo.png';
$namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
$namaMadrasah = $kop['nama'] ?? 'MAS MIFTAHUL HUDA';
$npsn = $kop['npsn'] ?? '69988776';
$nsm = $kop['nsm'] ?? '131218040003';
$alamatJalan = $kop['alamat'] ?? 'Jl. Tirtomoyo No. 083, Margorahayu';
$kecamatan = $kop['kecamatan'] ?? 'Way Tenong';
$kabupaten = $kop['kota'] ?? 'Lampung Barat';
$telepon = $kop['telepon'] ?? '081938240819';
$email = $kop['email'] ?? 'masmiftahulhuda@gmail.com';

// Current Month & Year derived from $start_date
$curr_m = (int)date('m', strtotime($start_date));
$curr_y = (int)date('Y', strtotime($start_date));

// Previous Month & Next Month dates
$prev_time = strtotime($start_date . ' -1 month');
$prev_start = date('Y-m-01', $prev_time);
$prev_end = date('Y-m-t', $prev_time);

$next_time = strtotime($start_date . ' +1 month');
$next_start = date('Y-m-01', $next_time);
$next_end = date('Y-m-t', $next_time);

$nama_bulan_arr = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$periode_label = 'Bulan ' . ($nama_bulan_arr[$curr_m] ?? date('F', strtotime($start_date))) . ' ' . $curr_y . ' (' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)) . ')';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Ringkasan Absensi Siswa - <?php echo $periode_label; ?></title>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', Arial, sans-serif; 
            font-size: 11px; 
            margin: 0; 
            padding: 0; 
            background: #f8fafc;
            color: #1e293b;
        }

        /* Toolbar Filter Langsung */
        .print-toolbar { 
            background: #ffffff; 
            color: #1e293b; 
            padding: 10px 1.5rem; 
            position: sticky; 
            top: 0; 
            z-index: 100;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
        }

        .select-filter {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
            font-size: 11px;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            cursor: pointer;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-nav:hover {
            background: #bae6fd;
            color: #0284c7;
        }

        .btn-submit {
            background: #0284c7;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-print { 
            background: #10b981; 
            color: white; 
            border: none; 
            padding: 7px 16px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 12px; 
            font-weight: 700; 
            box-shadow: 0 2px 4px rgba(16,185,129,0.25);
        }
        .btn-print:hover { background: #059669; }

        .btn-back {
            background: #64748b;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }
        .btn-back:hover { background: #475569; }

        /* Container Dokumen Cetak */
        .page-container {
            max-width: 210mm;
            margin: 15px auto;
            background: white;
            padding: 10mm 15mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* KOP SURAT MADRASAH */
        .header-kop { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            position: relative; 
            padding-bottom: 6px; 
            margin-bottom: 2px;
            border-bottom: 3px double #000;
        }
        .header-kop img { 
            position: absolute; 
            left: 5px; 
            top: 50%; 
            transform: translateY(-50%); 
            width: 65px; 
            height: auto; 
        }
        .header-text { 
            text-align: center; 
            flex-grow: 1; 
            line-height: 1.25; 
        }
        .header-text h3 { 
            margin: 0; 
            font-size: 11pt; 
            font-weight: bold; 
            color: #1e293b; 
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-text h2 { 
            margin: 2px 0; 
            font-size: 13.5pt; 
            font-weight: 800; 
            color: #0f172a; 
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-text p { 
            margin: 1px 0; 
            font-size: 8pt; 
            color: #334155; 
        }

        .report-header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .report-title {
            margin: 0;
            font-size: 12.5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-subtitle {
            margin-top: 3px;
            font-size: 9.5pt;
            color: #475569;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #334155; padding: 6px 8px; text-align: left; font-size: 10.5px; }
        table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }

        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .page-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            @page { size: portrait; margin: 10mm; }
            table th, table td { border-color: #000; }
        }
    </style>
</head>
<body>

<!-- TOOLBAR FILTER LANGSUNG (NO PRINT) -->
<div class="no-print print-toolbar">
    <div class="toolbar-left">
        <!-- Quick Nav Prev Month -->
        <a href="?start_date=<?php echo $prev_start; ?>&end_date=<?php echo $prev_end; ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak=1" class="btn-nav" title="Pindah ke Bulan Sebelumnya">
            &larr; <?php echo $nama_bulan_arr[(int)date('m', $prev_time)] ?? 'Bulan Lalu'; ?>
        </a>

        <!-- Form Filter Dropdown -->
        <form method="GET" action="" class="filter-form">
            <input type="hidden" name="cetak" value="1">
            
            <div class="filter-group">
                <label>Bulan:</label>
                <select name="bulan" class="select-filter" onchange="this.form.submit()">
                    <?php foreach($nama_bulan_arr as $m_idx => $m_name): ?>
                        <option value="<?php echo $m_idx; ?>" <?php echo $m_idx == $curr_m ? 'selected' : ''; ?>>
                            <?php echo $m_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Tahun:</label>
                <select name="tahun" class="select-filter" onchange="this.form.submit()">
                    <?php for($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y == $curr_y ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Kelas:</label>
                <select name="kelas_id" class="select-filter" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                        <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($k['nama_kelas']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">
                Terapkan
            </button>
        </form>

        <!-- Quick Nav Next Month -->
        <a href="?start_date=<?php echo $next_start; ?>&end_date=<?php echo $next_end; ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak=1" class="btn-nav" title="Pindah ke Bulan Berikutnya">
            <?php echo $nama_bulan_arr[(int)date('m', $next_time)] ?? 'Bulan Depan'; ?> &rarr;
        </a>
    </div>

    <div class="toolbar-right">
        <a href="?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak_detail=1" style="background:#f59e0b; color:white; padding:7px 14px; border-radius:6px; font-weight:700; text-decoration:none; font-size:12px;">
            Lihat Cetak Detail
        </a>
        <button onclick="window.print()" class="btn-print">
            Cetak Dokumen
        </button>
        <a href="<?php echo base_url('admin/absen_v2/rekap-bulanan') . '?start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date) . '&kelas_id=' . urlencode($kelas_id); ?>" class="btn-back">
            Kembali
        </a>
    </div>
</div>

<div class="page-container">
    <!-- KOP RESMI MADRASAH -->
    <div class="header-kop">
        <img src="<?php echo $logoSrc; ?>" alt="Logo Madrasah" onerror="this.src='/logo.png'; this.onerror=function(){this.style.display='none'};">
        <div class="header-text">
            <h3><?php echo htmlspecialchars($namaYayasan); ?></h3>
            <h2><?php echo htmlspecialchars($namaMadrasah); ?></h2>
            <p style="font-weight: 600;">NSM: <?php echo htmlspecialchars($nsm); ?> | NPSN: <?php echo htmlspecialchars($npsn); ?></p>
            <p><?php echo htmlspecialchars($alamatJalan . ', Kec. ' . $kecamatan . ', Kab. ' . $kabupaten); ?></p>
            <p>Telp: <?php echo htmlspecialchars($telepon); ?> | Email: <?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="report-header">
        <div class="report-title">REKAPITULASI ABSENSI SISWA</div>
        <div class="report-subtitle">
            Periode: <strong><?php echo htmlspecialchars($periode_label); ?></strong> &nbsp;|&nbsp; Kelas: <strong><?php echo htmlspecialchars($kelas_nama ?: 'Semua Kelas'); ?></strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">NIS</th>
                <th width="30%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="8%" class="text-center">Hadir</th>
                <th width="8%" class="text-center">Sakit</th>
                <th width="8%" class="text-center">Izin</th>
                <th width="8%" class="text-center">Alpa</th>
                <th width="8%" class="text-center">Bolos</th>
                <th width="8%" class="text-center">Telat</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($rekap)): ?>
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8; font-weight: bold;">Belum ada data absensi.</td>
            </tr>
            <?php else: ?>
            <?php $no=1; foreach($rekap as $row): ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nis']); ?></td>
                <td style="font-weight: 600;"><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                <td class="text-center" style="font-weight: 700; color: #16a34a;"><?php echo $row['rekap']['Hadir']; ?></td>
                <td class="text-center"><?php echo $row['rekap']['Sakit']; ?></td>
                <td class="text-center"><?php echo $row['rekap']['Izin']; ?></td>
                <td class="text-center" style="font-weight: 700; color: #dc2626;"><?php echo $row['rekap']['Alpa']; ?></td>
                <td class="text-center" style="font-weight: 700; color: #7f1d1d;"><?php echo $row['rekap']['Bolos']; ?></td>
                <td class="text-center"><?php echo $row['rekap']['Terlambat']; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 220px; font-size: 11px;">
            <?php
            $lastDayNum = date('t', strtotime($start_date));
            $namaBulanAkhir = $nama_bulan_arr[$curr_m] ?? '';
            $tglCetakAkhirBulan = "{$lastDayNum} {$namaBulanAkhir} {$curr_y}";
            ?>
            <p style="margin: 0;">Way Tenong, <?php echo $tglCetakAkhirBulan; ?></p>
            <p style="margin: 4px 0 60px 0;">Kepala Madrasah,</p>
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">AHMAD RIFA'I, M.Pd</p>
            <p style="margin: 2px 0 0 0;">NIP. -</p>
        </div>
    </div>
</div>

</body>
</html>
