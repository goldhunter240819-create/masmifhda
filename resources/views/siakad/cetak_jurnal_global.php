<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal Global</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
        }
        .btn-print { background: #16a34a; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .filter-bar { display: flex; align-items: center; gap: 10px; background: #fff; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex-wrap: wrap; }
        .filter-bar select, .filter-bar input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 12px; font-family: inherit; }
        .filter-bar button { padding: 9px 15px; font-size: 12px; font-family: inherit; }
        .btn-filter { background: #0ea5e9; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="no-print filter-bar">
    <form id="filterForm" method="GET" action="" style="display: flex; gap: 10px; align-items: center; width: 100%; margin: 0; flex-wrap: wrap;">
        <input type="hidden" name="cetak" value="1">
        
        <label style="display: bold; margin-right: 5px;">Opsi Cetak:</label>
        <select name="opsi_cetak" style="width: 150px;" onchange="resetAndSubmit(this.value)">
            <option value="">-- Pilih Opsi --</option>
            <option value="buku_jurnal_kelas" <?php echo $filter_opsi_cetak == 'buku_jurnal_kelas' ? 'selected' : ''; ?>>Buku Jurnal Kelas</option>
            <option value="jurnal_pribadi" <?php echo $filter_opsi_cetak == 'jurnal_pribadi' ? 'selected' : ''; ?>>Jurnal Pribadi</option>
        </select>

        <?php if (!empty($filter_opsi_cetak)): ?>
            <input type="date" name="tanggal_mulai" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>">
            <span style="font-weight: bold;">s/d</span>
            <input type="date" name="tanggal_sampai" value="<?php echo htmlspecialchars($filter_tanggal_sampai); ?>">

            <select name="kelas_id" style="width: 120px;">
                <option value="0">-- Semua Kelas --</option>
                <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $filter_kelas == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="guru_id" style="width: 120px;">
                <option value="0">-- Semua Guru --</option>
                <?php foreach($guruList as $g): ?>
                    <option value="<?php echo $g['id']; ?>" <?php echo (isset($filter_guru) ? $filter_guru : 0) == $g['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($g['nama']); ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($filter_opsi_cetak == 'jurnal_pribadi'): ?>
                <?php $filter_mapel = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0; ?>
                <select name="mapel_id" style="width: 120px;">
                    <option value="0">-- Semua Mapel --</option>
                    <?php foreach($mapelList as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo $filter_mapel == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit" class="btn-filter">Terapkan Filter</button>
            <button type="button" class="btn-print" onclick="window.print()">Cetak Halaman (Ctrl+P)</button>
        <?php endif; ?>
    </form>
</div>
<script>
function resetAndSubmit(val) {
    if (val !== "") {
        window.location.href = '?cetak=1&opsi_cetak=' + val;
    } else {
        window.location.href = '?cetak=1';
    }
}
</script>

<div class="header" style="border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; display: <?php echo empty($filter_opsi_cetak) ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; position: relative;">
    <!-- Logo Sekolah -->
    <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" style="width: 80px; position: absolute; left: 10px;">
    
    <!-- Teks Kop -->
    <div style="text-align: center; width: 100%;">
        <h3 style="margin: 0; font-size: 16px; font-family: 'Times New Roman', Times, serif; font-weight: normal; text-transform: uppercase;">YAYASAN PONDOK PESANTREN MIFTAHUL HUDA</h3>
        <h2 style="margin: 0; font-size: 22px; font-weight: bold; font-family: 'Times New Roman', Times, serif; text-transform: uppercase;"><?php echo isset($inst['nama']) ? htmlspecialchars($inst['nama']) : 'MADRASAH ALIYAH SWASTA MIFTAHUL HUDA'; ?></h2>
        <p style="margin: 3px 0 0; font-size: 12px; font-family: 'Times New Roman', Times, serif;">
            NSM: <?php echo isset($inst['nsm']) ? htmlspecialchars($inst['nsm']) : '-'; ?> | 
            NPSN: <?php echo isset($inst['npsn']) ? htmlspecialchars($inst['npsn']) : '-'; ?>
        </p>
        <p style="margin: 2px 0 0; font-size: 12px; font-family: 'Times New Roman', Times, serif;">
            Alamat: <?php echo isset($inst['alamat']) ? htmlspecialchars($inst['alamat']) : ''; ?><?php echo isset($inst['kecamatan']) ? ', Kec. '.htmlspecialchars($inst['kecamatan']) : ''; ?><?php echo isset($inst['kota']) ? ', '.htmlspecialchars($inst['kota']) : ''; ?>
        </p>
    </div>
</div>

<?php if (!empty($filter_opsi_cetak)): ?>
<div style="text-align: center; margin-bottom: 20px;">
    <h4 style="font-family: 'Times New Roman', Times, serif; font-weight: bold; font-size: 16px; margin: 0; text-decoration: underline;">
        <?php echo $filter_opsi_cetak == 'jurnal_pribadi' ? 'BUKU JURNAL PRIBADI' : 'BUKU JURNAL KELAS'; ?>
    </h4>
</div>

<div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 10px 0; margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; font-family: 'Times New Roman', Times, serif;">
    <table style="border: none; width: 48%; margin: 0;">
        <tr>
            <td style="border: none; padding: 2px 0; width: 35%;">Periode Tanggal</td>
            <td style="border: none; padding: 2px 0; width: 5%;">:</td>
            <td style="border: none; padding: 2px 0; width: 60%;"><?php echo date('d-m-Y', strtotime($filter_tanggal_mulai)); ?> s/d <?php echo date('d-m-Y', strtotime($filter_tanggal_sampai)); ?></td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Jenis Cetakan</td>
            <td style="border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;"><?php echo $filter_opsi_cetak == 'jurnal_pribadi' ? 'Jurnal Pribadi' : 'Jurnal Kelas'; ?></td>
        </tr>
    </table>
    <table style="border: none; width: 48%; margin: 0;">
        <tr>
            <td style="border: none; padding: 2px 0; width: 35%;">Kelas/Mapel</td>
            <td style="border: none; padding: 2px 0; width: 5%;">:</td>
            <td style="border: none; padding: 2px 0; width: 60%;">
                <?php 
                $nama_mapel_cetak = 'Semua Mapel';
                if (isset($filter_mapel) && $filter_mapel > 0 && isset($mapelList)) {
                    foreach($mapelList as $m) {
                        if($m['id'] == $filter_mapel) {
                            $nama_mapel_cetak = $m['nama_mapel'];
                            break;
                        }
                    }
                }
                echo htmlspecialchars($nama_kelas_cetak) . ' / ' . htmlspecialchars($nama_mapel_cetak); 
                ?>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Guru</td>
            <td style="border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;"><?php echo (isset($filter_guru) && $filter_guru > 0 && isset($guruMap[$filter_guru])) ? htmlspecialchars($guruMap[$filter_guru]) : 'Semua Guru'; ?></td>
        </tr>
    </table>
</div>

<?php
// Kelompokkan berdasarkan tanggal
$grouped = [];
foreach($jurnal_global as $j) {
    $tgl = $j['tanggal'];
    if(!isset($grouped[$tgl])) $grouped[$tgl] = [];
    $grouped[$tgl][] = $j;
}

$hari_indo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
?>

<?php if(empty($jurnal_global)): ?>
    <div class="text-center" style="margin-top: 50px;">Tidak ada data jurnal.</div>
<?php else: ?>

    <?php if ($filter_opsi_cetak == 'jurnal_pribadi'): ?>
        <!-- FORMAT JURNAL PRIBADI: SINGLE TABLE -->
        <table style="margin-top: 0; margin-bottom: 25px; font-family: 'Times New Roman', Times, serif;">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center;">No</th>
                    <th width="20%">Hari/Tanggal</th>
                    <th width="50%">Materi Pembahasan</th>
                    <th width="15%" style="text-align: center;">Presensi</th>
                    <th width="10%" style="text-align: center;">Ket</th>
                </tr>
            </thead>
            <tbody>
            <?php $no=1; foreach($jurnal_global as $j): 
                $tgl = $j['tanggal'];
                $hari_inggris = date('l', strtotime($tgl));
                $nama_hari = $hari_indo[$hari_inggris];
            ?>
            <tr <?php if(!empty($j['is_pulang_awal'])) echo 'style="background: linear-gradient(to right, #eff6ff, #dbeafe); box-shadow: inset 4px 0 0 0 #3b82f6;" title="Agenda Pulang Dipercepat / Rapat"'; else if(!empty($j['is_duplicate'])) echo 'style="background-color: #ffedd5;" title="Jurnal Ganda pada Mapel dan Tanggal yang sama"'; ?>>
                <td class="text-center"><?php echo $no++; ?></td>
                
                <?php if(!empty($j['is_libur']) || !empty($j['is_pulang_awal'])): 
                    $bg = !empty($j['is_libur']) ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
                ?>
                    <td>
                        <b><?php echo $nama_hari; ?></b><br>
                        <?php echo date('d-m-Y', strtotime($tgl)); ?>
                    </td>
                    <td colspan="3" class="text-center" style="font-weight:bold; color:#ffffff; background: <?php echo $bg; ?>; padding:10px;">
                        <?php echo htmlspecialchars($j['materi']); ?>
                    </td>
                <?php else: ?>
                    <td>
                        <b><?php echo $nama_hari; ?></b><br>
                        <?php echo date('d-m-Y', strtotime($tgl)); ?>
                    </td>
                    <td>
                        <?php if(empty(trim($j['materi']))): ?>
                            <span style="color: red; font-style: italic;">Belum mengisi jurnal mengajar</span>
                        <?php else: ?>
                            <?php 
                                $materi_print = htmlspecialchars($j['materi']);
                                if (strpos($materi_print, 'Pembelajaran Daring:') !== false) {
                                    $materi_print = str_replace('Pembelajaran Daring:', '<span style="background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe; font-weight: bold; font-size: 11px;">💻 Pembelajaran Daring</span><br>', $materi_print);
                                }
                                // Badge Inval
                                $materi_print = preg_replace('/\(Inval oleh:\s*(.*?)\)/i', '<span style="background: #fdf4ff; color: #a21caf; padding: 2px 6px; border-radius: 4px; border: 1px solid #f5d0fe; font-weight: bold; font-size: 11px;">🤝 Inval oleh: $1</span>', $materi_print);
                                $materi_print = preg_replace('/Tugas Inval:/i', '<span style="background: #fef2f2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecaca; font-weight: bold; font-size: 11px;">📋 Tugas Inval</span><br>', $materi_print);
                                
                                echo nl2br($materi_print);
                            ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="font-size: 11px;">
                        <?php
                        $ad = $j['absen_detail'];
                        $text_absen = [];
                        if($ad['hadir'] > 0) $text_absen[] = "H:" . $ad['hadir'];
                        if($ad['sakit'] > 0) $text_absen[] = "S:" . $ad['sakit'];
                        if($ad['izin'] > 0) $text_absen[] = "I:" . $ad['izin'];
                        if($ad['alpa'] > 0) $text_absen[] = "A:" . $ad['alpa'];
                        echo empty($text_absen) ? "-" : implode(", ", $text_absen);
                        ?>
                    </td>
                    <td class="text-center">
                        <?php echo !empty($j['keterangan']) ? htmlspecialchars($j['keterangan']) : '-'; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php else: ?>
        <!-- FORMAT BUKU JURNAL KELAS: DIKELOMPOKKAN PER HARI -->
        <?php foreach($grouped as $tgl => $items): 
            $hari_inggris = date('l', strtotime($tgl));
            $nama_hari = $hari_indo[$hari_inggris];
        ?>
        <div style="page-break-inside: avoid; break-inside: avoid;">
            <div style="font-weight: bold; margin-bottom: 5px; font-family: 'Times New Roman', Times, serif;">Hari/Tanggal: <?php echo $nama_hari; ?>, <?php echo date('d-m-Y', strtotime($tgl)); ?></div>
            <table style="margin-top: 0; margin-bottom: 25px; font-family: 'Times New Roman', Times, serif;">
                <thead>
                    <tr>
                        <th width="5%" style="text-align: center;">No</th>
                        <th width="20%">Mata Pelajaran</th>
                        <th width="35%">Materi Pembahasan</th>
                        <th width="20%">Nama Guru</th>
                        <th width="15%" style="text-align: center;">Presensi</th>
                        <th width="5%" style="text-align: center;">Ket</th>
                    </tr>
                </thead>
            <tbody>
            <?php $no=1; foreach($items as $j): ?>
            <tr <?php if(!empty($j['is_pulang_awal'])) echo 'style="background: linear-gradient(to right, #eff6ff, #dbeafe); box-shadow: inset 4px 0 0 0 #3b82f6;" title="Agenda Pulang Dipercepat / Rapat"'; else if(!empty($j['is_duplicate'])) echo 'style="background-color: #ffedd5;" title="Jurnal Ganda pada Mapel dan Tanggal yang sama"'; ?>>
                <td class="text-center"><?php echo $no++; ?></td>
                <?php if(!empty($j['is_libur']) || !empty($j['is_pulang_awal'])): 
                    $bg = !empty($j['is_libur']) ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
                ?>
                      <td colspan="5" class="text-center" style="font-weight:bold; color:#ffffff; background: <?php echo $bg; ?>; padding:10px;">
                          <?php echo htmlspecialchars($j['materi']); ?>
                      </td>
                  <?php else: ?>
                <td>
                    <?php echo htmlspecialchars($j['nama_mapel']); ?><br>
                    <span style="font-size: 10px; color: #666;">(Kelas: <?php echo htmlspecialchars($j['nama_kelas']); ?>)</span>
                </td>
                <td>
                    <?php if(empty(trim($j['materi']))): ?>
                        <span style="color: red; font-style: italic;">Belum mengisi jurnal mengajar</span>
                    <?php else: ?>
                        <?php 
                            $materi_print = htmlspecialchars($j['materi']);
                            if (strpos($materi_print, 'Pembelajaran Daring:') !== false) {
                                $materi_print = str_replace('Pembelajaran Daring:', '<span style="background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe; font-weight: bold; font-size: 11px;">💻 Pembelajaran Daring</span><br>', $materi_print);
                            }
                            // Badge Inval
                            $materi_print = preg_replace('/\(Inval oleh:\s*(.*?)\)/i', '<span style="background: #fdf4ff; color: #a21caf; padding: 2px 6px; border-radius: 4px; border: 1px solid #f5d0fe; font-weight: bold; font-size: 11px;">🤝 Inval oleh: $1</span>', $materi_print);
                            $materi_print = preg_replace('/Tugas Inval:/i', '<span style="background: #fef2f2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecaca; font-weight: bold; font-size: 11px;">📋 Tugas Inval</span><br>', $materi_print);
                            
                            echo nl2br($materi_print);
                        ?>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($j['nama_guru']); ?></td>
                <td class="text-center" style="font-size: 11px;">
                    <?php
                    $ad = $j['absen_detail'];
                    $text_absen = [];
                    if($ad['hadir'] > 0) $text_absen[] = "H:" . $ad['hadir'];
                    if($ad['sakit'] > 0) $text_absen[] = "S:" . $ad['sakit'];
                    if($ad['izin'] > 0) $text_absen[] = "I:" . $ad['izin'];
                    if($ad['alpa'] > 0) $text_absen[] = "A:" . $ad['alpa'];
                    echo empty($text_absen) ? "-" : implode(", ", $text_absen);
                    ?>
                </td>
                <td class="text-center">
                    <?php echo !empty($j['keterangan']) ? htmlspecialchars($j['keterangan']) : '-'; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>
<?php endif; ?>

</body>
</html>
