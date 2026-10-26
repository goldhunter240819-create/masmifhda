<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        @media print {
            @page { margin: 2cm; size: A4; }
            body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .surat-container {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 2cm;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .header img.logo-kiri {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }
        .header img.logo-kanan {
            position: absolute;
            right: 0;
            top: 0;
            width: 80px;
        }
        .header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            font-style: italic;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        td {
            padding: 4px;
            vertical-align: top;
        }
        td.label { width: 35%; }
        td.colon { width: 2%; text-align: center; }
        td.value { width: 63%; }
        
        .sub-label {
            padding-left: 20px;
        }
        
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .footer-left {
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }
        
        .foto-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .footer-right {
            text-align: left;
            width: 250px;
        }
        
        .ttd-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="surat-container">
    <div class="header">
        <!-- Assuming Kemenag logo and School logo -->
        <img src="<?php echo base_url('logo_kemenag.png'); ?>" alt="Kemenag" class="logo-kiri" onerror="this.style.display='none'">
        <img src="<?php echo base_url('uploads/logo/' . ($institusi['logo'] ?? '')); ?>" alt="Logo" class="logo-kanan" onerror="this.style.display='none'">
        
        <h3>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h3>
        <h1><?php echo strtoupper(htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH'])); ?></h1>
        <p><?php echo htmlspecialchars($institusi['alamat'] ?? ''); ?>, Kec. <?php echo htmlspecialchars($institusi['kecamatan'] ?? ''); ?>, <?php echo htmlspecialchars($institusi['kota'] ?? ''); ?></p>
    </div>

    <div class="title">IDENTITAS PESERTA DIDIK</div>

    <table>
        <?php $s = $student['SIAKAD'] ?? []; ?>
        <tr>
            <td class="label">1. Nama Peserta Didik</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nama'] ?? $student['NAMA_SISWA'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">2. NIS</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nis'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">3. NISN</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nisn'] ?? $student['NISN'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">4. Tempat Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="value">
                <?php 
                    $tempat = ucwords(strtolower($s['tempat_lahir'] ?? $student['LAHIR_TEMPAT'] ?? ''));
                    $date = strtotime($s['tanggal_lahir'] ?? $student['LAHIR_TANGGAL'] ?? time());
                    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    $tgl = date('d', $date) . ' ' . $months[(int)date('m', $date)] . ' ' . date('Y', $date);
                    echo htmlspecialchars($tempat) . ', ' . $tgl;
                ?>
            </td>
        </tr>
        <tr>
            <td class="label">5. Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['jenis_kelamin'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">6. Agama</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['agama'] ?? 'Islam'); ?></td>
        </tr>
        <tr>
            <td class="label">7. Status dalam Keluarga</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['status_keluarga'] ?? 'Anak Kandung'); ?></td>
        </tr>
        <tr>
            <td class="label">8. Anak ke</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['anak_ke'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">9. Alamat Peserta Didik</td>
            <td class="colon">:</td>
            <td class="value">
                <?php
                    $alamatArr = [];
                    if (!empty($s['alamat'])) $alamatArr[] = $s['alamat'];
                    if (!empty($s['rt'])) $alamatArr[] = "RT " . $s['rt'];
                    if (!empty($s['rw'])) $alamatArr[] = "RW " . $s['rw'];
                    if (!empty($s['desa'])) $alamatArr[] = "Desa/Kel " . $s['desa'];
                    if (!empty($s['kecamatan'])) $alamatArr[] = "Kec. " . $s['kecamatan'];
                    if (!empty($s['kota'])) $alamatArr[] = $s['kota'];
                    echo htmlspecialchars(implode(', ', $alamatArr));
                ?>
            </td>
        </tr>
        <tr>
            <td class="label">10. Nomor Telepon Rumah/HP</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['no_hp_ortu'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">11. Sekolah Asal</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['sekolah_asal'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">12. Diterima di sekolah ini</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">a. Di kelas</div></td>
            <td class="colon">:</td>
            <td class="value">X</td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">b. Pada tanggal</div></td>
            <td class="colon">:</td>
            <td class="value">
                <?php
                    $k = trim(strtolower($s['nama_kelas'] ?? $student['KELAS'] ?? ''));
                    $tgl_masuk = '';
                    
                    if (strpos($k, '12') !== false || preg_match('/\bxii\b/', $k)) {
                        $tgl_masuk = '15 Juli 2024';
                    } elseif (strpos($k, '11') !== false || preg_match('/\bxi\b/', $k)) {
                        $tgl_masuk = '17 Juli 2025';
                    } elseif (strpos($k, '10') !== false || preg_match('/\bx\b/', $k)) {
                        $tgl_masuk = '13 Juli 2026';
                    }
                    
                    echo $tgl_masuk;
                ?>
            </td>
        </tr>
        <tr>
            <td class="label">13. Nama Orang Tua</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">a. Ayah</div></td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nama_ayah'] ?? $student['NAMA_AYAH'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">b. Ibu</div></td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nama_ibu'] ?? $student['NAMA_IBU'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">14. Alamat Orang Tua</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars(implode(', ', $alamatArr)); ?></td>
        </tr>
        <tr>
            <td class="label">15. Pekerjaan Orang Tua</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">a. Ayah</div></td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['pekerjaan_ayah'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">b. Ibu</div></td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['pekerjaan_ibu'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label">16. Nama Wali Siswa</td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['nama_wali'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">Pekerjaan Wali</div></td>
            <td class="colon">:</td>
            <td class="value"><?php echo htmlspecialchars($s['pekerjaan_wali'] ?? ''); ?></td>
        </tr>
        <tr>
            <td class="label"><div class="sub-label">Alamat Wali Siswa</div></td>
            <td class="colon">:</td>
            <td class="value">
                <?php 
                    if (!empty($s['nama_wali'])) {
                        echo htmlspecialchars(implode(', ', $alamatArr));
                    }
                ?>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div class="footer-left">
            <?php 
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'apk.masmifhda.sch.id';
                $verifyLink = $protocol . "://" . $host . base_url('verifikasi/pip?id=' . urlencode($student['NIK']));
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($verifyLink);
            ?>
            <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 80px; height: 80px;">
            <div class="foto-box">
                <?php if (!empty($s['foto']) && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto'])): ?>
                    <img src="<?php echo base_url('uploads/siswa/' . $s['foto']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    Foto<br>3x4
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-right">
            <?php
                $kota = $institusi['kota'] ?? 'Kotabumi';
                $kota = ucwords(strtolower(str_replace(['KABUPATEN ', 'KAB. ', 'KOTA '], '', $kota)));
                if ($tgl_masuk != '') {
                    $today = $tgl_masuk;
                } else {
                    $tgl_konfirmasi = isset($student['confirmed_at']) ? strtotime($student['confirmed_at']) : time();
                    $today = date('d', $tgl_konfirmasi) . ' ' . $months[(int)date('m', $tgl_konfirmasi)] . ' ' . date('Y', $tgl_konfirmasi);
                }
            ?>
            <p style="margin: 0;"><?php echo htmlspecialchars($kota); ?>, <?php echo $today; ?></p>
            <p style="margin: 0;">Kepala Madrasah</p>
            
            <div style="position: relative; height: 70px; display: flex; align-items: flex-end; justify-content: flex-start;">
                <img src="<?php echo base_url('stempelma.png'); ?>" alt="Stempel" style="position: absolute; width: 140px; top: -50px; left: -30px; z-index: 1; opacity: 0.9;">
                <img src="<?php echo base_url('ttdkamad.png'); ?>" alt="Tanda Tangan" style="position: absolute; width: 220px; top: -90px; left: -10px; z-index: 2;">
            </div>
            
            <div class="ttd-name" style="position: relative; z-index: 3; margin-top: 10px;">
                <strong><?php echo htmlspecialchars($institusi['nama_kepala'] ?? '........................................'); ?></strong><br>
                NIP.
            </div>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
};
document.addEventListener('contextmenu', event => event.preventDefault()); 
document.onkeydown = function(e) {
    if(e.keyCode == 123) return false;
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) return false;
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) return false;
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) return false;
    if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false;
};
</script>

</body>
</html>
