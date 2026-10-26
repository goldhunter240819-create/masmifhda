<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tugas Daring - <?= htmlspecialchars($kelas_info['nama_kelas']) ?> - <?= date('d M Y', strtotime($tanggal_filter)) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header-kop {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-kop img {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .header-text {
            text-align: center;
        }
        .header-text h3 { margin: 0; font-weight: bold; }
        .header-text h2 { margin: 5px 0; font-weight: bold; letter-spacing: 1px; }
        .header-text p { margin: 2px 0; font-size: 11pt; }
        
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .tugas-item {
            margin-bottom: 30px;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        .tugas-header {
            background-color: #f0f0f0;
            padding: 8px 12px;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        .tugas-body {
            padding: 12px;
        }
        .tugas-body .judul {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .tugas-body .instruksi {
            white-space: pre-wrap;
            line-height: 1.5;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="margin-bottom: 30px; display: flex; gap: 10px; justify-content: flex-start; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; font-weight: bold; background: #0ea5e9; color: white; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2); transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print Sekarang
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; font-weight: bold; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Tutup
        </button>
    </div>

    <?php
        $dbCore = \App\Core\Database::connect('core');
        $kop = $dbCore->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch();
        $logoSrc = ($kop && $kop['logo']) ? '/assets/images/' . $kop['logo'] : '/assets/images/logo.png';
    ?>
    <div class="header-kop">
        <img src="<?php echo $logoSrc; ?>" alt="Logo" onerror="this.src='/logo.png'; this.onerror=function(){this.style.display='none'};">
        <div class="header-text">
            <h3 style="font-size: <?php echo $kop['font_yayasan'] ?? 16; ?>pt;"><?php echo htmlspecialchars($kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA'); ?></h3>
            <h2 style="font-size: <?php echo $kop['font_madrasah'] ?? 24; ?>pt;"><?php echo htmlspecialchars($kop['nama_madrasah'] ?? 'MADRASAH ALIYAH MIFTAHUL HUDA'); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu')); ?></p>
            <p>Telp: <?php echo htmlspecialchars($kop['telepon'] ?? '081938240819'); ?> | Email: <?php echo htmlspecialchars($kop['email'] ?? 'masmiftahulhuda083@gmail.com'); ?></p>
        </div>
    </div>

    <div class="report-title">LEMBAR TUGAS DARING / E-LEARNING </div>

    <div class="meta-info">
        <div>Kategori Kelas: <?= htmlspecialchars($kelas_info['nama_kelas']) ?></div>
        <div>Tanggal: <?= date('d F Y', strtotime($tanggal_filter)) ?></div>
    </div>

    <?php if (empty($tugas_list)): ?>
        <p style="text-align: center; font-style: italic;">Tidak ada tugas yang dijadwalkan untuk kelas ini pada tanggal tersebut.</p>
    <?php else: ?>
        <?php foreach ($tugas_list as $t): ?>
        <div class="tugas-item">
            <div class="tugas-header">
                Mata Pelajaran: <?= htmlspecialchars($t['nama_mapel']) ?> &nbsp;|&nbsp; Guru: <?= htmlspecialchars($t['nama_guru']) ?>
            </div>
            <div class="tugas-body">
                <div class="judul"><?= htmlspecialchars($t['judul']) ?></div>
                <?php if (!empty($t['batas_waktu'])): ?>
                <div style="font-weight: bold; color: #d32f2f; margin-bottom: 15px; font-size: 11pt;">
                    Batas Pengumpulan: <?= date('d M Y', strtotime($t['batas_waktu'])) ?>
                </div>
                <?php endif; ?>
                <div class="instruksi"><?= htmlspecialchars($t['instruksi']) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
