<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi - <?php echo $periode_nama; ?></title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table th { background-color: #f1f5f9; font-weight: bold; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        .btn-print { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: right;">
    <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
</div>

<div class="header">
    <h2>REKAP ABSENSI SISWA</h2>
    <p>Periode: <?php echo $periode_nama; ?> | Kelas: <?php echo $kelas_nama ?: 'Semua Kelas'; ?></p>
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
            <td colspan="10" class="text-center">Belum ada data absensi.</td>
        </tr>
        <?php else: ?>
        <?php $no=1; foreach($rekap as $row): ?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['nis']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
            <td class="text-center"><?php echo $row['rekap']['Hadir']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Sakit']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Izin']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Alpa']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Bolos']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Terlambat']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
// Perhitungan Persentase untuk Ranking
$rekap_ranking = $rekap;
foreach ($rekap_ranking as &$s) {
    $hadir = $s['rekap']['Hadir'] + $s['rekap']['Terlambat'];
    $sakit = $s['rekap']['Sakit'];
    $izin = $s['rekap']['Izin'];
    $alpa = $s['rekap']['Alpa'];
    $bolos = $s['rekap']['Bolos'];
    
    $total_rekam = $hadir + $sakit + $izin + $alpa + $bolos;
    $s['persentase'] = $total_rekam > 0 ? round(($hadir / $total_rekam) * 100, 1) : 0;
}
unset($s);

// Sorting Ranking
usort($rekap_ranking, function($a, $b) {
    if ($b['persentase'] == $a['persentase']) {
        return strcmp($a['avg_jam_masuk'], $b['avg_jam_masuk']); // Paling pagi di atas
    }
    return $b['persentase'] <=> $a['persentase'];
});
?>

<div style="page-break-before: always; margin-top: 40px; padding-top: 20px;">
    <div class="header">
        <h2>LAMPIRAN: PERINGKAT KERAJINAN SISWA</h2>
        <p>Berdasarkan Persentase Kehadiran & Rata-rata Jam Masuk</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">Rank</th>
                <th width="15%">NIS</th>
                <th width="35%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%" class="text-center">Rata-rata Masuk</th>
                <th width="15%" class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($rekap_ranking)): ?>
            <tr>
                <td colspan="6" class="text-center">Belum ada data absensi.</td>
            </tr>
            <?php else: ?>
            <?php $no=1; foreach($rekap_ranking as $row): ?>
            <tr>
                <td class="text-center" style="font-weight: bold; font-size: 14px;"><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nis']); ?></td>
                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                <td class="text-center"><?php echo $row['avg_jam_masuk'] == '23:59:59' ? '-' : substr($row['avg_jam_masuk'], 0, 5); ?></td>
                <td class="text-center" style="font-weight: bold; font-size: 14px;"><?php echo $row['persentase']; ?>%</td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
