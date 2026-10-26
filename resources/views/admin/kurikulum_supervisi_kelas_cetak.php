<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
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
            margin-bottom: 10px;
        }
        .info-table td {
            vertical-align: top;
            padding: 1px 5px;
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
            margin-bottom: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10.5pt;
        }
        .data-table tr {
            page-break-inside: avoid;
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
            padding: 10px;
            margin-bottom: 15px;
            font-size: 10.5pt;
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
            @page {
                size: auto;
                margin: 15mm; /* Margin sedikit dilebarkan agar rapi */
            }
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
            <?php if (isset($kop_surat) && !empty($kop_surat)): ?>
                <img src="<?= $kop_surat ?>" alt="Kop Surat" style="width: 100%; max-height: 150px; object-fit: contain;">
            <?php else: ?>
                <?php 
                    $kop_data = isset($kop) && is_array($kop) ? $kop : [];
                    $logoSrc = !empty($kop_data['logo']) ? '/assets/images/' . $kop_data['logo'] : '/assets/images/logo.png';
                ?>
                <img src="<?= $logoSrc ?>" alt="Logo" style="width: 85px; height: auto; position: absolute; left: 0;" onerror="this.src='/logo.png'; this.onerror=null;">
                <div style="flex-grow: 1; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.2;">
                    <?php 
                        $font_y = (int)($kop_data['font_yayasan'] ?? 16);
                        $font_m = (int)($kop_data['font_madrasah'] ?? 24);
                        // Dikecilkan sedikit
                        $font_y = $font_y > 12 ? $font_y - 2 : $font_y;
                        $font_m = $font_m > 16 ? $font_m - 4 : $font_m;
                    ?>
                    <div style="margin: 0; font-size: <?= $font_y ?>pt; font-weight: bold;"><?= htmlspecialchars($kop_data['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA') ?></div>
                    <div style="margin: 0; font-size: <?= $font_m ?>pt; font-weight: bold;"><?= htmlspecialchars($kop_data['nama_madrasah'] ?? 'MADRASAH ALIYAH SWASTA MIFTAHUL HUDA') ?></div>
                    <div style="margin: 0; font-size: 9pt;"><?= nl2br(htmlspecialchars($kop_data['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu, Tambak Jaya, Way Tenong, Lampung Barat')) ?></div>
                    <div style="margin: 0; font-size: 10pt;">
                        Telp: <?= htmlspecialchars($kop_data['telepon'] ?? '081938240819') ?> | 
                        Email: <?= htmlspecialchars($kop_data['email'] ?? 'masmiftahulhuda083@gmail.com') ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline;">SUPERVISI PELAKSANAAN PEMBELAJARAN</div>
            <div style="font-size: 11pt; margin-top: 4px;">Tahun Ajaran: <?= $institusi['tahun_ajaran_nama'] ?? 'Saat Ini' ?></div>
        </div>

        <table class="info-table">
            <tr>
                <td>Nama Guru</td>
                <td>:</td>
                <td><strong><?= htmlspecialchars($guru['nama']) ?></strong></td>
            </tr>
            <tr>
                <td>Mata Pelajaran</td>
                <td>:</td>
                <td><?= htmlspecialchars($supervisi['mata_pelajaran'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Kelas / Jam Ke-</td>
                <td>:</td>
                <td><?= htmlspecialchars($supervisi['kelas'] ?? '-') ?> / <?= htmlspecialchars($supervisi['jam_ke'] ?? '-') ?></td>
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
                        <th style="width: 30px;">No</th>
                        <th>Aspek yang Diamati</th>
                        <th style="width: 50px;">Nilai</th>
                        <th style="width: 90px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $items = [
                        '1' => '<b>Kegiatan apersepsi</b><br>a. Menyebutkan tujuan pembelajaran<br>b. Memberikan contoh-contoh dalam kehidupan sehari-hari<br>c. Manfaat belajar materi yang disampaikan. (KBC)',
                        '2' => '<b>Memahami</b><br>Guru memberikan stimulus untuk membangkitkan rasa ingin tahu peserta didik dan mulai mengidentifikasi topik yang akan dieksplorasi (Berkesadaran dan Bermakna)/KBC',
                        '3' => '<b>Memahami</b><br>Peserta didik mengidentifikasi dan merumuskan pertanyaan yang akan dijawab dalam proses inkuiri dan guru membimbing peserta didik untuk menyusun hipotesis atau dugaan awal.<br>(Berkesadaran dan Bermakna) / KBC',
                        '4' => '<b>Mengaplikasi</b><br>Peserta didik mengumpulkan informasi dari berbagai sumber, seperti eksperimen, observasi, wawancara, atau literatur dan mencari pola atau hubungan antar konsep.<br>(Bermakna, Mengembirakan)',
                        '5' => '<b>Mengaplikasi</b><br>Peserta didik mengorganisasi dan menafsirkan data yang telah dikumpulkan, menggunakan berbagai teknik analisis.<br>(Bermakna, Mengembirakan) / KBC',
                        '6' => '<b>Mengaplikasi</b><br>Peserta didik menyusun kesimpulan berdasarkan bukti dan hasil analisis, serta menjelaskan temuan dan menghubungkannya dengan konsep atau teori yang relevan.<br>(Bermakna, Mengembirakan)',
                        '7' => '<b>Mengaplikasi</b><br>Peserta didik menyampaikan hasil temuannya melalui presentasi, laporan tertulis, atau diskusi kelas.<br>(Bermakna, Mengembirakan) / KBC',
                        '8' => '<b>Mengaplikasi</b><br>Melakukan refleksi terhadap proses pembelajaran: apa yang telah dipelajari, kesulitan yang dihadapi, dan bagaimana meningkatkan pemahaman lebih lanjut, serta guru memberikan umpan balik konstruktif untuk memperkaya pengalaman belajar peserta didik.<br>(Berkesadaran, Bermakna)',
                        '9' => '<b>Mengaplikasi</b><br>Peserta didik menerapkan hasil pembelajaran dalam konteks nyata atau proyek lanjutan dan mendorong eksplorasi lebih lanjut untuk memperdalam pemahaman konsep.<br>(Berkesadaran, Bermakna)'
                    ];
                    
                    $no = 1;
                    foreach ($items as $key => $label):
                        $val = $supervisi['instrumen'][$key] ?? '';
                        
                        $display_val = '-';
                        if ($val == '4') $display_val = 'Sangat Baik';
                        elseif ($val == '3') $display_val = 'Baik';
                        elseif ($val == '2') $display_val = 'Cukup';
                        elseif ($val == '1') $display_val = 'Kurang';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="left"><?= $label ?></td>
                        <td style="font-weight: bold; font-size: 12pt;"><?= $val ?: '-' ?></td>
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
