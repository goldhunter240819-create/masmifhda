<?php
// data variables: $siswa, $tahun, $nilai, $total, $inst
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport - <?php echo $siswa['nama']; ?></title>
    <style>
        @import url(base_url('public/assets/css/plus-jakarta-sans.css'));
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding: 40px; 
            color: #1e293b; 
            line-height: 1.5; 
            background: #f1f5f9;
        }
        
        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-print {
            background: #0ea5e9;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Header Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo { width: 80px; height: 80px; object-fit: contain; }
        .header-text { flex: 1; text-align: center; }
        .header-text h1 { margin: 0; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; }
        .header-text h2 { margin: 5px 0; font-size: 16px; color: #0ea5e9; }
        .header-text p { margin: 0; font-size: 11px; color: #64748b; }

        .doc-title { text-align: center; margin-bottom: 30px; }
        .doc-title h3 { margin: 0; font-size: 18px; text-decoration: underline; text-underline-offset: 4px; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Info Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .label { width: 140px; font-weight: 600; color: #64748b; }
        .colon { width: 15px; }
        .value { font-weight: 700; color: #0f172a; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Main Table Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .main-table th {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 12px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
        }
        .main-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            font-size: 13px;
            text-align: center;
        }
        .text-left { text-align: left !important; }
        .font-bold { font-weight: 800; }
        .bg-gray { background: #f8fafc; }

        /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Footer Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
        .footer-sigs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            margin-top: 50px;
            font-size: 13px;
        }
        .sig-box { text-align: center; }
        .sig-space { height: 80px; }
        .sig-name { font-weight: 800; text-decoration: underline; }

        @media print {
            body { background: white; padding: 0; }
            .page { box-shadow: none; margin: 0; padding: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            CETAK RAPORT
        </button>
    </div>

    <div class="page">
        <div class="header">
            <?php if($inst && $inst['logo']): ?>
                <img src="<?php echo base_url('uploads/logo/' . $inst['logo']); ?>" class="logo">
            <?php endif; ?>
            <div class="header-text">
                <h1><?php echo $inst ? $inst['nama'] : 'Aula System SMART SCHOOL'; ?></h1>
                <h2>LAPORAN HASIL BELAJAR SISWA</h2>
                <p><?php echo $inst ? $inst['alamat'] : 'Jl. Pendidikan No. 123, Indonesia'; ?></p>
            </div>
        </div>

        <div class="doc-title">
            <h3>HASIL PENCAPAIAN KOMPETENSI PESERTA DIDIK</h3>
        </div>

        <div class="info-grid">
            <table class="info-table">
                <tr><td class="label">Nama Peserta Didik</td><td class="colon">:</td><td class="value"><?php echo $siswa['nama']; ?></td></tr>
                <tr><td class="label">NIS / NISN</td><td class="colon">:</td><td class="value"><?php echo $siswa['nis']; ?> / <?php echo $siswa['nisn'] ?: '-'; ?></td></tr>
            </table>
            <table class="info-table">
                <tr><td class="label">Kelas</td><td class="colon">:</td><td class="value"><?php echo $siswa['nama_kelas']; ?></td></tr>
                <tr><td class="label">Tahun Ajaran</td><td class="colon">:</td><td class="value"><?php echo $tahun['name']; ?></td></tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th class="text-left">Mata Pelajaran</th>
                    <th width="70">Tugas</th>
                    <th width="70">UTS</th>
                    <th width="70">UAS</th>
                    <th width="100">Nilai Akhir</th>
                    <th width="80">Predikat</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $sum = 0;
                foreach($nilai as $n): 
                    $sum += $n['nilai_akhir'];
                    $predikat = 'C';
                    if($n['nilai_akhir'] >= 85) $predikat = 'A';
                    elseif($n['nilai_akhir'] >= 75) $predikat = 'B';
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td class="text-left font-bold"><?php echo $n['nama_mapel']; ?></td>
                    <td><?php echo $n['nilai_tugas']; ?></td>
                    <td><?php echo $n['nilai_uts']; ?></td>
                    <td><?php echo $n['nilai_uas']; ?></td>
                    <td class="font-bold"><?php echo number_format($n['nilai_akhir'], 1); ?></td>
                    <td><?php echo $predikat; ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($nilai)): ?>
                    <tr><td colspan="7" style="padding: 40px; color: #94a3b8;">Belum ada data nilai untuk periode ini.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray">
                    <td colspan="5" class="text-left font-bold" style="text-align: right !important; padding-right: 20px;">RATA-RATA NILAI</td>
                    <td class="font-bold" style="color: #0ea5e9; font-size: 15px;"><?php echo count($nilai) > 0 ? round($sum / count($nilai), 2) : '0'; ?></td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-sigs">
            <div class="sig-box">
                <p>Orang Tua / Wali Murid,</p>
                <div class="sig-space"></div>
                <p class="sig-name">( ........................................ )</p>
            </div>
            <div class="sig-box">
                <p><?php echo $inst ? $inst['kota'] : 'Indonesia'; ?>, <?php echo date('d F Y'); ?></p>
                <p>Kepala Sekolah,</p>
                <div class="sig-space"></div>
                <p class="sig-name"><?php echo $inst ? $inst['nama_kepala'] : 'Administrator'; ?></p>
                <p style="font-size: 11px;">NIP. <?php echo $inst ? $inst['nip_kepala'] : '-'; ?></p>
            </div>
        </div>
    </div>
</body>
</html>


