<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Detail Absensi - <?php echo date('d M Y', strtotime($tanggal)); ?></title>
    <?php 
    if (!function_exists('singkatMapel')) {
        function singkatMapel($nama) {
            $exceptions = ['TIK', 'PJOK', 'PKN', 'IPA', 'IPS'];
            if (in_array(strtoupper($nama), $exceptions)) return strtoupper($nama);
            $words = explode(' ', trim($nama));
            if (count($words) > 1) {
                $singkat = '';
                foreach($words as $w) {
                    if (strlen($w) > 0) $singkat .= strtoupper(substr($w,0,1));
                }
                return $singkat;
            }
            return strlen($nama) > 5 ? substr($nama, 0, 3) . '.' : $nama;
        }
    }

    if (!function_exists('singkatStatus')) {
        function singkatStatus($status) {
            if (empty($status) || $status == '-') return '-';
            if (strtolower($status) == 'terlambat') return 'T';
            return strtoupper(substr($status, 0, 1));
        }
    }
    ?>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 11px; margin: 0; padding: 10px; line-height: 1.2; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; font-size: 14px; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th, table td { border: 1px solid #000; padding: 2px 4px; text-align: center; vertical-align: middle; }
        table th { background-color: #f1f5f9; font-weight: bold; }
        .text-left { text-align: left !important; }
        .text-center { text-align: center; }
        @media print {
            @page { 
                size: 330mm 215mm; /* Kertas F4 / Folio Landscape */
                margin: 5mm; /* Margin Sempit */
            }
            body { padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        .btn-print { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
        .badge { padding: 3px 6px; border-radius: 3px; font-weight: bold; display: inline-block; }
        .bg-hadir { color: #15803d; }
        .bg-alpa { background-color: #ef4444; color: white; }
        .bg-sakit { background-color: #eab308; color: white; }
        .bg-izin { background-color: #3b82f6; color: white; }
        .bg-bolos { background-color: #991b1b; color: white; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: right;">
    <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
</div>

<div class="header">
    <h2>REKAP DETAIL JEJAK ABSENSI SISWA</h2>
    <p>Tanggal: <?php echo date('d F Y', strtotime($tanggal)); ?> | Kelas: <?php echo htmlspecialchars($kelas_nama); ?></p>
</div>

<?php 
$hari_ini_rekap = date('w', strtotime($tanggal));
$show_sholat = !in_array($hari_ini_rekap, [5, 6]); 
?>
<table>
    <thead>
        <tr>
            <th rowspan="2" style="width: 1%; white-space: nowrap;">No</th>
            <th rowspan="2" class="text-left" style="width: 25%; white-space: nowrap;">Nama Siswa</th>
            <th colspan="<?php echo count($jadwal) + ($show_sholat ? 4 : 3); ?>">TIMELINE KEHADIRAN</th>
        </tr>
        <tr>
            <th>Masuk</th>
            <?php foreach($jadwal as $i => $j): ?>
                <th style="min-width:40px;">
                    <span title="<?php echo htmlspecialchars($j['nama_mapel']); ?>"><?php echo htmlspecialchars($j['kode_mapel']); ?></span><br>
                    <span style="font-weight:normal; font-size:9px;">
                        <?php 
                        echo substr($j['jam_mulai'],0,5); 
                        if (!empty($j['jam_selesai']) && substr($j['jam_selesai'],0,5) != substr($j['jam_mulai'],0,5)) {
                            echo '-' . substr($j['jam_selesai'],0,5);
                        }
                        ?>
                    </span>
                </th>
            <?php endforeach; ?>
            <?php if($show_sholat): ?><th>Sholat</th><?php endif; ?>
            <th>Pulang</th>
            <th>Status Pusat</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rekap)): ?>
        <tr>
            <td colspan="<?php echo count($jadwal) + ($show_sholat ? 6 : 5); ?>" class="text-center">Data siswa tidak ditemukan.</td>
        </tr>
        <?php else: ?>
            <?php $no=1; foreach($rekap as $r): ?>
            <tr>
                <td style="width: 1%; white-space: nowrap;"><?php echo $no++; ?></td>
                <td class="text-left" style="white-space: nowrap; font-weight: bold;"><?php echo htmlspecialchars($r['nama']); ?></td>
                
                <td><?php echo $r['timeline']['gate_masuk']; ?></td>
                
                <?php foreach($r['timeline']['mapels'] as $m): ?>
                <td class="text-center">
                    <?php 
                        $cls = 'bg-hadir';
                        if($m == 'Alpa') $cls = 'bg-alpa badge';
                        elseif($m == 'Sakit') $cls = 'bg-sakit badge';
                        elseif($m == 'Izin') $cls = 'bg-izin badge';
                    ?>
                    <span class="<?php echo $cls; ?>"><?php echo singkatStatus($m); ?></span>
                </td>
                <?php endforeach; ?>
                
                <?php if($show_sholat): ?><td><?php echo $r['timeline']['sholat']; ?></td><?php endif; ?>
                
                <td><?php echo $r['timeline']['gate_pulang']; ?></td>
                
                <!-- Status Pusat -->
                <td style="font-weight:bold;
                    <?php 
                        if(in_array($r['timeline']['status_akhir'], ['Hadir','Terlambat'])) echo 'color:#15803d; background:#f0fdf4;';
                        elseif(in_array($r['timeline']['status_akhir'], ['Alpa','Bolos'])) echo 'color:#ffffff; background:#ef4444;';
                        elseif($r['timeline']['status_akhir'] == 'Sakit') echo 'color:#ffffff; background:#eab308;';
                        elseif($r['timeline']['status_akhir'] == 'Izin') echo 'color:#ffffff; background:#3b82f6;';
                        else echo 'color:#94a3b8;';
                    ?>
                ">
                    <?php echo singkatStatus($r['timeline']['status_akhir']); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
