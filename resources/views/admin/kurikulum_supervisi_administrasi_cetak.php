<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        @page {
            size: A4;
            margin: 20mm;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .header-logo {
            width: 80px;
            height: auto;
        }
        .header-text {
            flex: 1;
        }
        .header-text h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header-text p {
            margin: 2px 0 0 0;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 5px;
        }
        .info-table td:first-child {
            width: 150px;
        }
        .info-table td:nth-child(2) {
            width: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .data-table th {
            font-weight: bold;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact;
        }
        .data-table td.left {
            text-align: left;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 30px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body {
                background: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0ea5e9; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px;">
            🖨️ Cetak Dokumen
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="container">
        <!-- KOP SURAT dari Pengaturan SIAKAD -->
        <div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; position: relative;">
            <?php 
                $logoSrc = ($kop && !empty($kop['logo'])) ? '/assets/images/' . $kop['logo'] : '/assets/images/logo.png';
            ?>
            <img src="<?= $logoSrc ?>" alt="Logo" style="width: 85px; height: auto; position: absolute; left: 0;" onerror="this.src='/logo.png'; this.onerror=null;">
            <div style="flex-grow: 1; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3;">
                <div style="margin: 0; font-size: <?= htmlspecialchars($kop['font_yayasan'] ?? '16') ?>pt; font-weight: bold;"><?= htmlspecialchars($kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA') ?></div>
                <div style="margin: 0; font-size: <?= htmlspecialchars($kop['font_madrasah'] ?? '24') ?>pt; font-weight: bold;"><?= htmlspecialchars($kop['nama_madrasah'] ?? 'MADRASAH ALIYAH SWASTA MIFTAHUL HUDA') ?></div>
                <div style="margin: 0; font-size: 10pt;"><?= nl2br(htmlspecialchars($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu, Tambak Jaya, Way Tenong, Lampung Barat')) ?></div>
                <div style="margin: 0; font-size: 10pt;">
                    Telp: <?= htmlspecialchars($kop['telepon'] ?? '081938240819') ?> | 
                    Email: <?= htmlspecialchars($kop['email'] ?? 'masmiftahulhuda083@gmail.com') ?>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline;">SUPERVISI ADMINISTRASI GURU</div>
            <div style="font-size: 11pt; margin-top: 4px;">Tahun Ajaran: <?= $institusi['tahun_ajaran_nama'] ?? 'Saat Ini' ?></div>
        </div>

        <table class="info-table">
            <tr>
                <td>Nama Guru</td>
                <td>:</td>
                <td><strong><?= htmlspecialchars($guru['nama']) ?></strong></td>
            </tr>
            <tr>
                <td>Tanggal Supervisi</td>
                <td>:</td>
                <td><?= $supervisi ? date('d F Y', strtotime($supervisi['tanggal_supervisi'])) : '-' ?></td>
            </tr>
        </table>

        <?php if (!$supervisi): ?>
            <div style="text-align: center; padding: 40px; border: 1px dashed #ccc;">
                <h2>Belum Ada Data Supervisi</h2>
                <p>Guru ini belum dinilai administrasi mengajarnya.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Komponen Administrasi</th>
                        <th style="width: 150px;">Ketersediaan/Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $items = [
                        'cp' => 'Capaian Pembelajaran (CP)',
                        'atp' => 'Alur Tujuan Pembelajaran (ATP)',
                        'modul' => 'Modul Ajar / RPP',
                        'kktp' => 'KKTP / KKM',
                        'prota' => 'Program Tahunan (Prota)',
                        'promes' => 'Program Semester (Promes)',
                        'absen' => 'Buku Daftar Hadir Siswa',
                        'jurnal' => 'Buku Jurnal Mengajar',
                        'daftar_nilai' => 'Buku Daftar Nilai'
                    ];
                    
                    $no = 1;
                    foreach ($items as $key => $label):
                        $val = $supervisi['instrumen'][$key] ?? '';
                        
                        // Fallback untuk data dari web lama
                        if (empty($val)) {
                            if ($key == 'cp' || $key == 'atp') $val = $supervisi['instrumen']['silabus'] ?? '';
                            elseif ($key == 'modul') $val = $supervisi['instrumen']['rpp'] ?? '';
                            elseif ($key == 'kktp') $val = $supervisi['instrumen']['kkm'] ?? '';
                            elseif ($key == 'daftar_nilai') $val = $supervisi['instrumen']['nilai'] ?? '';
                        }
                        
                        $display_val = '-';
                        if ($val == '4' || $val === 'Ada') $display_val = 'Sangat Lengkap';
                        elseif ($val == '3') $display_val = 'Cukup Lengkap';
                        elseif ($val == '2' || $val === 'Tidak Lengkap') $display_val = 'Kurang Lengkap';
                        elseif ($val == '1' || $val == '0' || $val === 'Tidak Ada') $display_val = 'Tidak Ada';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="left"><?= $label ?></td>
                        <td><?= $display_val ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-box">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 150px; font-weight: bold; vertical-align: top;">Nilai Akhir</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td style="font-size: 16pt; font-weight: bold; color: <?= $supervisi['nilai_akhir'] >= 75 ? '#16a34a' : '#dc2626' ?>;">
                            <?= $supervisi['nilai_akhir'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; vertical-align: top; padding-top: 10px;">Catatan Penilai</td>
                        <td style="vertical-align: top; padding-top: 10px;">:</td>
                        <td style="padding-top: 10px;"><?= nl2br(htmlspecialchars($supervisi['catatan'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; vertical-align: top; padding-top: 10px;">Tindak Lanjut</td>
                        <td style="vertical-align: top; padding-top: 10px;">:</td>
                        <td style="padding-top: 10px;"><?= nl2br(htmlspecialchars($supervisi['tindak_lanjut'] ?? '-')) ?></td>
                    </tr>
                </table>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <p>Guru yang Disupervisi,</p>
                    <div class="signature-space"></div>
                    <div class="signature-name"><?= htmlspecialchars($guru['nama']) ?></div>
                </div>
                
                <div class="signature-box">
                    <p>Supervisor / Penilai,</p>
                    <div class="signature-space"></div>
                    <div class="signature-name"><?= htmlspecialchars($supervisi['penilai_nama'] ?? 'Admin') ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
