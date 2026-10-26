<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir Rapat - <?php echo htmlspecialchars($judul); ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; padding: 40px; color: #000; font-size: 11pt; }
        .kop-surat { display: flex; align-items: center; border-bottom: 4px double #000; padding-bottom: 15px; margin-bottom: 25px; }
        .kop-surat img { width: 90px; height: 90px; object-fit: contain; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h1 { margin: 0; font-size: 0.9rem; text-transform: uppercase; }
        .kop-text h2 { margin: 5px 0; font-size: 1rem; }
        .kop-text p { margin: 2px 0; font-size: 0.75rem; font-style: italic; }
        
        .rapat-header { text-align: center; margin-bottom: 25px; }
        .rapat-header h3 { text-transform: uppercase; margin: 0 0 5px 0; font-size: 1.2rem; text-decoration: underline; }
        
        .info-rapat { margin-bottom: 20px; width: 100%; }
        .info-rapat td { padding: 4px; vertical-align: top; border: none; }
        
        table.tabel-hadir { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.tabel-hadir th, table.tabel-hadir td { border: 1px solid #000; padding: 4px 6px; }
        table.tabel-hadir th { background: #f4f4f4; text-transform: uppercase; font-size: 0.85rem; }
        table.tabel-hadir td { height: 26px; font-size: 0.85rem; }
        
        .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd { text-align: center; width: 250px; }
        
        @media print {
            @page { 
                size: 215.9mm 330.2mm; /* F4 Size */
                margin: 1cm; /* Margin sempit */
            }
            .no-print { display: none !important; }
        }
        
        .floating-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #0ea5e9;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-family: sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            transition: transform 0.2s;
        }
        .floating-print:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    
    <button class="no-print floating-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Cetak Sekarang (F4)
    </button>
    
    <div class="kop-surat">
        <?php if(!empty($inst['logo'])): ?>
            <img src="<?php echo base_url('public/uploads/logo/' . $inst['logo']); ?>" alt="Logo">
        <?php else: ?>
            <div style="width: 90px;"></div>
        <?php endif; ?>
        <div class="kop-text">
            <h1>YAYASAN PENDIDIKAN MIFTAHUL HUDA MATHLA'UL ANWAR</h1>
            <h2>MI MIFTAHUL HUDA MATHLA'UL ANWAR</h2>
            <p><?php echo htmlspecialchars($inst['alamat'] ?? ''); ?>, Kec. <?php echo htmlspecialchars($inst['kecamatan'] ?? ''); ?>, <?php echo htmlspecialchars($inst['kota'] ?? ''); ?> <?php echo htmlspecialchars($inst['kodepos'] ?? ''); ?></p>
            <p>Email: <?php echo htmlspecialchars($inst['email'] ?? ''); ?> | Website: <?php echo htmlspecialchars($inst['website'] ?? ''); ?></p>
        </div>
        <div style="width: 90px;"></div> <!-- Spacer -->
    </div>

    <div class="rapat-header">
        <h3><?php echo htmlspecialchars($judul); ?></h3>
    </div>

    <table class="info-rapat">
        <tr>
            <td style="width: 120px; font-weight: bold;">Hari / Tanggal</td>
            <td style="width: 10px;">:</td>
            <td><?php echo htmlspecialchars($tanggal); ?></td>
        </tr>
        <?php if ($kelas_info): ?>
        <tr>
            <td style="font-weight: bold;">Peserta Kelas</td>
            <td>:</td>
            <td>KELAS <?php echo htmlspecialchars($kelas_info['nama_kelas']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($agenda_lines)): ?>
        <tr>
            <td style="font-weight: bold;">Agenda</td>
            <td>:</td>
            <td>
                <?php foreach($agenda_lines as $idx => $line): ?>
                    <div style="margin-bottom: 3px;"><?php echo ($idx + 1) . '. ' . htmlspecialchars($line); ?></div>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>

    <table class="tabel-hadir">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 180px;">Nama Siswa</th>
                <th>Nama Wali Siswa</th>
                <th colspan="2" style="width: 160px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = count($siswas);
            for ($i = 0; $i < $total; $i++): 
                $s = $siswas[$i];
                $is_odd = ($i % 2 === 0); 
                $has_next = isset($siswas[$i+1]);
            ?>
            <tr>
                <td align="center"><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($s['nama']); ?></td>
                <td></td> <!-- Kosong untuk Nama Wali -->
                
                <?php if ($is_odd): ?>
                    <td rowspan="2" valign="top" style="padding-top: 6px; font-size: 0.85rem; width: 80px;">
                        <?php echo $i + 1; ?>.
                    </td>
                    <td rowspan="2" valign="bottom" style="padding-bottom: 6px; font-size: 0.85rem; width: 80px;">
                        <?php if ($has_next): ?>
                            <?php echo $i + 2; ?>.
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
            
            <?php if ($total > 0 && $total % 2 !== 0): ?>
                <!-- Dummy row to complete the rowspan of the last student -->
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endif; ?>
            
            <?php if ($total === 0): ?>
                <tr>
                    <td colspan="5" align="center" style="padding: 20px;">Tidak ada data siswa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p><?php echo htmlspecialchars($inst['kota'] ?? 'Kota'); ?>, <?php echo htmlspecialchars($tanggal); ?></p>
            <p>Kepala Madrasah,</p>
            <?php 
                $qr_data = urlencode("Ditandatangani secara digital oleh: " . ($inst['nama_kepala'] ?? 'Kepala Madrasah'));
            ?>
            <div style="margin: 10px 0;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?php echo $qr_data; ?>" alt="QR Code" style="width: 80px; height: 80px;">
            </div>
            <p style="margin-top: 5px; font-size: 0.9rem;"><b><?php echo htmlspecialchars($inst['nama_kepala'] ?? '...........................'); ?></b></p>
            <?php if(!empty($inst['nip_kepala'])): ?>
                <p style="margin: 2px 0; font-size: 0.9rem;">NIP. <?php echo htmlspecialchars($inst['nip_kepala']); ?></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

