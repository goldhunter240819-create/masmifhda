<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Identitas Guru | SIAKAD</title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        :root {
            --primary: #1e293b;
            --accent: #0369a1;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --border: #e2e8f0;
            --bg-card: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        @media print {
            @page { 
                size: A4; 
                margin: 10mm; 
            }
            body { 
                background: white !important; 
                margin: 0; 
                padding: 0; 
            }
            .no-print { display: none !important; }
            .container { 
                padding: 0 !important; 
                gap: 5mm !important; 
                max-width: none !important;
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                width: 100% !important;
            }
            .card { 
                break-inside: avoid; 
                border: 0.5pt solid #ccc !important;
                box-shadow: none !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            width: 100%;
            max-width: 900px;
        }

        /* Ukuran KTP Standar ISO/IEC 7810 ID-1: 85.6mm x 53.98mm */
        .card {
            width: 85.6mm;
            height: 54mm;
            background: radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.1) 0%, transparent 60%), radial-gradient(circle at 0% 100%, rgba(2, 132, 199, 0.05) 0%, transparent 50%), var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 4mm;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .card.vertical {
            width: 54mm;
            height: 85.6mm;
        }

        /* Watermark Background Logo */
        .card::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 160px;
            height: 160px;
            background-image: url('/uploads/logo/<?php echo isset($inst['logo']) ? $inst['logo'] : ''; ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            transform: translate(-50%, -50%);
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }

        .card-header, .card-footer {
            color: white;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .card-header { 
            height: 12.5mm;
            padding: 0 4mm;
            box-sizing: border-box;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.25' d='M0,128L48,144C96,160,192,192,288,197.3C384,203,480,181,576,149.3C672,117,768,75,864,64C960,53,1056,75,1152,106.7C1248,139,1344,181,1392,202.7L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E"), linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            background-size: cover;
            background-position: center;
            border-bottom: 1.5px solid #075985;
        }

        .card-footer { 
            background: linear-gradient(135deg, #064e3b 0%, #0c4a6e 100%);
            border-top: 1.5px solid #38bdf8; 
            margin-top: auto; 
            padding: 6px 12px; 
        }

        .card-header img {
            height: 28px;
            margin-right: 12px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
            flex-shrink: 0;
        }

        .header-title { line-height: 1.2; flex: 1; min-width: 0; }
        .header-title h1 {
            margin: 0;
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        .header-title h2 {
            margin: 0;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-body {
            display: flex;
            padding: 10px 15px;
            position: relative;
            z-index: 2;
            flex: 1;
            gap: 15px;
            align-items: stretch;
        }

        .photo-container {
            width: 22.5mm;
            flex-shrink: 0;
            border: 2px solid #fff;
            border-radius: 6px;
            overflow: hidden;
            background: #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            height: 30mm;
            align-self: flex-start;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-row { margin-bottom: 4px; }
        .info-label {
            font-size: 8px; font-weight: 600; color: #64748b; text-transform: uppercase;
        }
        .info-value {
            font-size: 11px; font-weight: 800; color: #0f172a; line-height: 1.2;
        }

        .footer-qr {
            width: 32px; height: 32px; padding: 2px; background: white; border-radius: 4px; margin-right: 10px; flex-shrink: 0;
        }

        .footer-text h3 { margin: 0; font-size: 8px; font-weight: 700; color: #7dd3fc; }
        .footer-text p { margin: 0; font-size: 8.5px; font-family: 'JetBrains Mono', monospace; font-weight: 700; }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(6, 95, 70, 0.4);
            z-index: 1000;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .print-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(6, 95, 70, 0.6); }

        /* Sisi Belakang CSS */
        .card-back {
            align-items: center;
            justify-content: center;
            padding: 4mm;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; bottom: 30px; right: 30px; z-index: 1000; display: flex; gap: 10px; flex-wrap: wrap; max-width: 500px; justify-content: flex-end;">
        <?php 
            $isThumb = isset($_GET['thumb']) && $_GET['thumb'] == '1'; 
            $isVertical = isset($_GET['layout']) && $_GET['layout'] == 'vertical';
        ?>
        <button class="print-btn" style="position: static; background: #8b5cf6;" onclick="window.location.href='?<?php echo $isThumb ? 'thumb=1&' : ''; ?>layout=<?php echo $isVertical ? 'horizontal' : 'vertical'; ?>'"><i data-lucide="layout" style="width: 20px; height: 20px;"></i> Versi <?php echo $isVertical ? 'Horizontal' : 'Vertikal'; ?></button>
        <button class="print-btn" style="position: static; background: <?php echo $isThumb ? '#f59e0b' : '#64748b'; ?>;" onclick="window.location.href='?<?php echo $isVertical ? 'layout=vertical&' : ''; ?><?php echo $isThumb ? '' : 'thumb=1'; ?>'"><i data-lucide="image" style="width: 20px; height: 20px;"></i> Versi <?php echo $isThumb ? 'Original' : 'Thumbnail'; ?></button>
        <button class="print-btn" style="position: static;" onclick="window.print()"><i data-lucide="printer" style="width: 20px; height: 20px;"></i> Cetak Kartu</button>
    </div>

    <div class="container">
        <?php 
            $bulanIndo = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            foreach($gurus as $g): 
            $foto = $g['foto'] ? '/uploads/guru/' . $g['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=065f46&color=fff';
            $tglCetak = date('j') . ' ' . $bulanIndo[(int)date('m')] . ' ' . date('Y');
            $kotaCetak = isset($inst['kota']) && $inst['kota'] ? $inst['kota'] : 'Jakarta';
            $namaKepala = isset($inst['nama_kepala']) && $inst['nama_kepala'] ? $inst['nama_kepala'] : 'Kepala Sekolah';
            $instName = $inst ? $inst['nama'] : 'Aula System ACADEMY';
        ?>
        <?php
            $useThumb = isset($_GET['thumb']) && $_GET['thumb'] == '1';
            if ($useThumb) {
                $fotoURL = $g['foto'] ? base_url('thumb.php?src=uploads/guru/' . $g['foto'] . '&w=300&h=300') : 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=065f46&color=fff';
            } else {
                $fotoURL = $g['foto'] ? base_url('uploads/guru/' . $g['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($g['nama']) . '&background=065f46&color=fff';
            }
            $qrDataTTD = "DOKUMEN SAH\nDitandatangani oleh:\nNama: {$namaKepala}\nJabatan: Kepala Madrasah\nLokasi: {$kotaCetak}\nTanggal: {$tglCetak}";
            $qrUrlFront = 'https://quickchart.io/qr?size=150&margin=1&text=' . urlencode($qrDataTTD);

            $qrDataGuru = "TOKEN:" . $g['user_id'] . ":" . substr(md5($g['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
            $qrUrlBack = 'https://quickchart.io/qr?size=250&margin=1&text=' . urlencode($qrDataGuru);
        ?>

        <?php if($isVertical): ?>
        <!-- SISI DEPAN VERTIKAL -->
        <div class="card vertical">
            <div class="card-header" style="flex-direction: column; height: 18mm; text-align: center; padding: 2mm; justify-content: center;">
                <?php if($inst && $inst['logo']): ?>
                    <img src="<?php echo base_url('uploads/logo/' . (isset($inst['logo']) ? $inst['logo'] : '')); ?>" alt="Logo" style="margin: 0 0 2px 0; height: 20px;">
                <?php endif; ?>
                <div class="header-title">
                    <h1 style="font-size: 8px;">KARTU IDENTITAS GURU</h1>
                    <h2 style="font-size: 7px;"><?php echo $instName; ?></h2>
                </div>
            </div>
            <div style="flex: 1; padding: 2mm 4mm; display: flex; flex-direction: column; align-items: center; justify-content: space-between; position: relative; z-index: 2;">
                <div style="display: flex; flex-direction: column; align-items: center; width: 100%; margin-top: 2mm;">
                    <!-- FOTO -->
                    <div style="width: 27mm; height: 36mm; border: 1px solid #cbd5e1; border-radius: 1.5mm; overflow: hidden; padding: 1mm; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 3mm;">
                        <img src="<?php echo $fotoURL; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5mm;">
                    </div>
                    <!-- INFO -->
                    <div style="width: 100%; text-align: center;">
                        <div style="font-weight: 900; color: #0f172a; font-size: 11px; line-height: 1.3;"><?php echo $g['nama']; ?></div>
                        <div style="font-weight: 800; color: #0ea5e9; font-size: 8px; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $g['jabatan'] ?: '-'; ?></div>
                    </div>
                </div>

                <!-- TTD & QR -->
                <div style="width: 100%; display: flex; justify-content: space-between; align-items: flex-end; padding-top: 2mm; border-top: 0.5px solid #e2e8f0; margin-top: auto;">
                    <div style="text-align: left;">
                        <div style="font-size: 4px; color: #475569; line-height: 1.4;"><?php echo $kotaCetak; ?>, <?php echo $tglCetak; ?><br>Mengetahui, Kepala Madrasah</div>
                        <div style="font-size: 4.5px; font-weight: 800; color: #0f172a; margin-top: 3mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo $namaKepala; ?></div>
                        <div style="font-size: 4px; color: #475569; margin-top: 0.5mm;">NIP. <?php echo isset($inst['nip_kepala']) && $inst['nip_kepala'] ? $inst['nip_kepala'] : '-'; ?></div>
                    </div>
                    <img src="<?php echo $qrUrlFront; ?>" style="width: 10mm; height: 10mm; border: 0.5px solid #e2e8f0; padding: 1px; border-radius: 1mm;">
                </div>
            </div>
            <div style="height: 1.5mm; background: linear-gradient(135deg, #064e3b 0%, #0ea5e9 100%); width: 100%; position: relative; z-index: 2;"></div>
        </div>

        <!-- SISI BELAKANG VERTIKAL -->
        <div class="card vertical" style="display: flex; flex-direction: column; padding: 4mm; align-items: center; justify-content: center; text-align: center;">
            <?php if($inst && $inst['logo']): ?>
            <div style="position: absolute; top: 50%; left: 50%; width: 100px; height: 100px; background-image: url('/uploads/logo/<?php echo isset($inst['logo']) ? $inst['logo'] : ''; ?>'); background-repeat: no-repeat; background-position: center; background-size: contain; transform: translate(-50%, -50%); opacity: 0.1; z-index: 0; pointer-events: none;"></div>
            <?php endif; ?>
            
            <div style="width: 100%; margin-bottom: 3mm;">
                <div style="font-weight: 800; font-size: 10px; letter-spacing: 1px; color: #1e293b;">AKUN AKSES DIGITAL</div>
                <div style="font-size: 6.5px; color: #64748b; margin-top: 1mm;">Gunakan QR Code ini untuk akses verifikasi dan login</div>
            </div>
            <div style="background: white; padding: 2mm; border-radius: 2mm; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px rgba(0,0,0,0.05); z-index: 2; margin-bottom: 5mm;">
                <img src="<?php echo $qrUrlBack; ?>" style="width: 28mm; height: 28mm; display: block;">
            </div>
            
            <div style="text-align: left; width: 100%; font-size: 6px; color: #475569; z-index: 2;">
                <div style="margin-bottom: 1.5mm; font-weight: 700; color: #1e293b;">PENTING:</div>
                <ul style="padding-left: 3.5mm; margin: 0; line-height: 1.5;">
                    <li>Kartu ini adalah identitas resmi guru & tenaga kependidikan.</li>
                    <li>QR Code bersifat rahasia, jangan disebarkan untuk mencegah penyalahgunaan.</li>
                    <li>Jika kartu hilang atau rusak, segera laporkan ke pihak administrasi madrasah.</li>
                </ul>
            </div>
        </div>

        <?php else: ?>

        <!-- SISI DEPAN HORIZONTAL -->
        <div class="card">
            <div class="card-header">
                <?php if($inst && $inst['logo']): ?>
                    <img src="<?php echo base_url('uploads/logo/' . (isset($inst['logo']) ? $inst['logo'] : '')); ?>" alt="Logo">
                <?php endif; ?>
                <div class="header-title">
                    <h1>KARTU IDENTITAS GURU</h1>
                    <h2><?php echo $instName; ?></h2>
                </div>
            </div>
            
            <div style="flex: 1; padding: 2mm 4mm; display: flex; gap: 3mm; position: relative; z-index: 2;">
                <!-- KIRI: Foto besar -->
                <div style="width: 22.5mm; flex-shrink: 0; border: 1px solid #cbd5e1; border-radius: 1mm; overflow: hidden; padding: 0.8mm; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); height: 30mm; align-self: flex-start;">
                    <img src="<?php echo $fotoURL; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5mm;">
                </div>
                <!-- KANAN: Data atas, QR TTD bawah -->
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <table style="width: 100%; font-size: 8px; border-collapse: collapse; line-height: 1.35;">
                        <tr>
                            <td style="font-weight: 700; width: 14mm; color: #475569; vertical-align: top;">Nama</td>
                            <td style="width: 1.5mm; vertical-align: top;">:</td>
                            <td style="font-weight: 800; color: #0f172a; font-size: 9px; vertical-align: top; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 35mm; display: inline-block;"><?php echo $g['nama']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #475569; vertical-align: top;">Jabatan</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo $g['jabatan'] ?: '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #475569; vertical-align: top;">Tmpt Lahir</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo isset($g['tempat_lahir']) && $g['tempat_lahir'] ? $g['tempat_lahir'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #475569; vertical-align: top;">Tgl Lahir</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php 
                                if(isset($g['tanggal_lahir']) && $g['tanggal_lahir'] && $g['tanggal_lahir'] != '0000-00-00') {
                                    $p = explode('-', $g['tanggal_lahir']);
                                    echo (count($p) == 3) ? $p[2] . ' ' . $bulanIndo[(int)$p[1]] . ' ' . $p[0] : $g['tanggal_lahir'];
                                } else {
                                    echo '-';
                                }
                            ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #475569; vertical-align: top;">Alamat</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="font-weight: 700; color: #1e293b; vertical-align: top; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 35mm; display: inline-block;"><?php echo isset($g['alamat']) && $g['alamat'] ? $g['alamat'] : '-'; ?></td>
                        </tr>
                    </table>
                    
                    <!-- QR TTD + Pengesahan -->
                    <div style="display: flex; align-items: flex-end; gap: 2mm; margin-bottom: 2mm;">
                        <img src="<?php echo $qrUrlFront; ?>" style="width: 12mm; height: 12mm; flex-shrink: 0; border: 0.5px solid #e2e8f0; padding: 1px; border-radius: 1mm;">
                        <div style="text-align: left;">
                            <div style="font-size: 4.5px; color: #475569; line-height: 1.4;"><?php echo $kotaCetak; ?>, <?php echo $tglCetak; ?><br>Mengetahui, Kepala Madrasah</div>
                            <div style="font-size: 5px; font-weight: 800; color: #0f172a; margin-top: 2.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo $namaKepala; ?></div>
                            <div style="font-size: 4.5px; color: #475569; margin-top: 0.5mm;">NIP. <?php echo isset($inst['nip_kepala']) && $inst['nip_kepala'] ? $inst['nip_kepala'] : '-'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height: 1.5mm; background: linear-gradient(135deg, #064e3b 0%, #0ea5e9 100%); width: 100%; position: relative; z-index: 2;"></div>
        </div>

        <!-- SISI BELAKANG HORIZONTAL -->
        <div class="card" style="display: flex; flex-direction: column; padding: 4mm; align-items: center; justify-content: center;">
            <!-- Watermark -->
            <?php if($inst && $inst['logo']): ?>
            <div style="position: absolute; top: 50%; left: 50%; width: 120px; height: 120px; background-image: url('/uploads/logo/<?php echo isset($inst['logo']) ? $inst['logo'] : ''; ?>'); background-repeat: no-repeat; background-position: center; background-size: contain; transform: translate(-50%, -50%); opacity: 0.1; z-index: 0; pointer-events: none;"></div>
            <?php endif; ?>
            <div style="width: 100%; text-align: center; margin-bottom: 3mm;">
                <div style="font-weight: 800; font-size: 11px; letter-spacing: 1px; color: #1e293b;">AKUN AKSES DIGITAL</div>
                <div style="font-size: 7px; color: #64748b; margin-top: 1mm;">Gunakan QR Code ini untuk akses verifikasi dan login</div>
            </div>
            <div style="background: white; padding: 2mm; border-radius: 2mm; margin-bottom: 3mm; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px rgba(0,0,0,0.05); z-index: 2;">
                <img src="<?php echo $qrUrlBack; ?>" style="width: 25mm; height: 25mm; display: block;">
            </div>

        </div>

        <?php endif; ?>

        <?php endforeach; ?>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>


