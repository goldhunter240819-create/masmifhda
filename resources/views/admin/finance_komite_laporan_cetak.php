<?php
function tgl_indo($tanggal){
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Detail Laporan Kas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 80px;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .filter-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            -webkit-print-color-adjust: exact;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            @page { margin: 1cm; }
            body { font-size: 11px; }
            th { background-color: #eee !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="kop-surat">
        <?php if(!empty($inst['logo'])): ?>
            <img src="<?php echo base_url('public/uploads/logo/' . htmlspecialchars($inst['logo'])); ?>" class="kop-logo" alt="Logo">
        <?php else: ?>
            <div class="kop-logo"></div>
        <?php endif; ?>
        <div class="kop-text">
            <h1><?php echo isset($inst['nama']) && !empty($inst['nama']) ? htmlspecialchars($inst['nama']) : 'INSTITUSI PENDIDIKAN'; ?></h1>
            <p>
                <?php 
                $alamat = [];
                if(!empty($inst['alamat'])) $alamat[] = $inst['alamat'];
                if(!empty($inst['desa'])) $alamat[] = 'Desa/Kel. ' . $inst['desa'];
                if(!empty($inst['kecamatan'])) $alamat[] = 'Kec. ' . $inst['kecamatan'];
                if(!empty($inst['kota'])) $alamat[] = $inst['kota'];
                echo implode(', ', $alamat);
                ?>
                <br>
                <?php
                $kontak = [];
                if(!empty($inst['telepon'])) $kontak[] = 'Telp: ' . $inst['telepon'];
                if(!empty($inst['email'])) $kontak[] = 'Email: ' . $inst['email'];
                if(!empty($kontak)) echo implode(' | ', $kontak);
                ?>
            </p>
        </div>
    </div>

    <div class="report-title">LAPORAN RINCIAN KAS <?php echo htmlspecialchars($title_suffix); ?></div>
    
    <div class="filter-info">
        <?php if(!empty($start_date) && !empty($end_date)): ?>
            Periode: <?php echo tgl_indo($start_date); ?> s.d <?php echo tgl_indo($end_date); ?><br>
        <?php else: ?>
            Periode: Semua Waktu (Hingga <?php echo tgl_indo(date('Y-m-d')); ?>)<br>
        <?php endif; ?>
    </div>

    <div class="summary-box">
        <div>Total Pemasukan: Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></div>
        <div>Total Pengeluaran: Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></div>
        <div>Total Saldo Kas: Rp <?php echo number_format($saldo, 0, ',', '.'); ?></div>
    </div>

    <div class="section-title">A. Rincian Pemasukan Kas</div>
    <?php if(empty($detailPemasukan)): ?>
        <p style="text-align:center; font-style:italic;">Tidak ada data pemasukan pada periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 100px;">Kategori</th>
                    <th style="width: 100px;">Petugas</th>
                    <th class="text-right" style="width: 120px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($detailPemasukan as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo tgl_indo($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['petugas']); ?></td>
                    <td class="text-right"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PEMASUKAN</th>
                    <th class="text-right">Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="section-title" style="page-break-before: auto;">B. Rincian Pengeluaran Kas</div>
    <?php if(empty($detailPengeluaran)): ?>
        <p style="text-align:center; font-style:italic;">Tidak ada data pengeluaran pada periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 100px;">Kategori</th>
                    <th style="width: 100px;">Petugas</th>
                    <th class="text-right" style="width: 120px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($detailPengeluaran as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><?php echo tgl_indo($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['petugas']); ?></td>
                    <td class="text-right"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PENGELUARAN</th>
                    <th class="text-right">Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="signatures">
        <div class="signature-box">
            <p>Kepala Madrasah</p>
            <p class="signature-name">( <?php echo !empty($inst['nama_kepala']) ? htmlspecialchars($inst['nama_kepala']) : '_________________________'; ?> )</p>
        </div>
        <div class="signature-box">
            <p><?php echo isset($inst['kota']) ? $inst['kota'] : 'Kota/Kab'; ?>, <?php echo tgl_indo(date('Y-m-d')); ?><br>Bendahara <?php echo !empty($title_suffix) ? htmlspecialchars($title_suffix) : 'Komite'; ?></p>
            <p class="signature-name">( <?php echo !empty($bendahara_nama) ? htmlspecialchars($bendahara_nama) : '_________________________'; ?> )</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
