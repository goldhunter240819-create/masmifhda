<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan PIP - <?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }
        
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
            position: relative;
        }
        .kop-surat::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            border-bottom: 1px solid #000;
        }
        .kop-logo { width: 90px; height: 90px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 { margin: 0; font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 5px 0 0; font-size: 10.5px; }
        
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        
        .content {
            font-size: 12pt;
            text-align: justify;
        }
        
        .indent { padding-left: 20px; }
        
        table.form-table {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        table.form-table td { padding: 5px 0; vertical-align: top; }
        table.form-table td:nth-child(1) { width: 180px; }
        table.form-table td:nth-child(2) { width: 15px; }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 250px;
            text-align: center;
            font-size: 12pt;
        }
        
        .signature-space { height: 100px; display: flex; align-items: flex-end; justify-content: center; position: relative;}
        .materai-box {
            border: 1px dashed #999;
            width: 80px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #666;
            margin: 0 auto;
            position: absolute;
            left: 20px;
            bottom: 10px;
        }
        
        .footer-note {
            margin-top: 50px;
            font-size: 10pt;
            font-style: italic;
            color: #555;
        }

        .btn-print {
            position: fixed; top: 20px; right: 20px; background: #0ea5e9; color: white;
            border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-family: sans-serif;
            cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;
        }

        @media print {
            body { background: white; padding: 0; }
            .page { width: 100%; height: auto; box-shadow: none; padding: 0; margin: 0; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">Cetak Surat Pernyataan</button>

    <div class="page">
        <div class="kop-surat">
            <?php 
                $baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if ($baseUri === '/') $baseUri = '';
                $logo = isset($institusi['logo']) && $institusi['logo'] ? $baseUri . '/uploads/logo/' . $institusi['logo'] : $baseUri . '/assets/images/logo.png';
            ?>
            <img src="<?php echo $logo; ?>" class="kop-logo" alt="Logo" onerror="this.src='<?php echo $baseUri; ?>/assets/images/logo.png'">
            <div class="kop-text">
                <h1><?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH'] ?? 'MAS MIFTAHUL HUDA'); ?></h1>
                <p><?php 
                    $alamat = !empty($institusi['alamat']) ? htmlspecialchars($institusi['alamat']) : 'Jalan Simpang Mutar Alam Lama';
                    $desa = !empty($institusi['desa']) ? ', Desa ' . htmlspecialchars($institusi['desa']) : ', Desa Mutar Alam';
                    $kec = !empty($institusi['kecamatan']) ? ', Kec. ' . htmlspecialchars($institusi['kecamatan']) : ', Kec. Way Tenong';
                    $kota = !empty($institusi['kota']) ? ', ' . htmlspecialchars($institusi['kota']) : ', Kab. Lampung Barat';
                    $prov = !empty($institusi['provinsi']) ? ', Prov. ' . htmlspecialchars($institusi['provinsi']) : ', Prov. Lampung';
                    $kodepos = !empty($institusi['kodepos']) ? ' - ' . htmlspecialchars($institusi['kodepos']) : ' - 34864';
                    echo $alamat . $desa . $kec . $kota . $prov . $kodepos;
                ?></p>
                <p>Email: <?php echo !empty($institusi['email']) ? htmlspecialchars($institusi['email']) : 'masmiftahulhuda.wt@gmail.com'; ?> | Website: masmifhda.sch.id</p>
            </div>
        </div>
        
        <div class="title">SURAT PERNYATAAN PERSETUJUAN ORANG TUA/WALI</div>
        
        <div class="content">
            <p>Yang bertanda tangan di bawah ini selaku Orang Tua / Wali Murid dari:</p>
            
            <table class="form-table indent">
                <tr>
                    <td>Nama Lengkap Siswa</td>
                    <td>:</td>
                    <td><strong><?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA']); ?></strong></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['NISN']); ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['KELAS'] ?? '-'); ?></td>
                </tr>
            </table>

            <p>Menyatakan dengan sesungguhnya, dalam keadaan sadar dan tanpa paksaan dari pihak manapun, bahwa kami <strong>menyetujui</strong> pemotongan / penggunaan sebagian dana Bantuan Program Indonesia Pintar (PIP) Tahun Anggaran 2026 yang diterima oleh anak kami tersebut di atas.</p>
            
            <p>Adapun dana yang dipotong tersebut diperuntukkan khusus bagi penyelesaian administrasi madrasah berupa pembayaran iuran Komite Madrasah, Field Trip (bila ada), dan/atau keperluan penunjang akademik lainnya sesuai dengan kesepakatan dan ketentuan yang berlaku di lingkungan MA Miftahul Huda.</p>
            
            <p>Demikian surat pernyataan persetujuan ini kami buat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signature-section">
            <div class="signature-box" style="position: relative;">
                <br>
                Kepala Madrasah,
                <div class="signature-space">
                    <img src="<?php echo base_url('stempelma.png'); ?>" alt="Stempel" style="position: absolute; width: 140px; top: -50px; left: 5px; z-index: 1; opacity: 0.9;">
                    <img src="<?php echo base_url('ttdkamad.png'); ?>" alt="Tanda Tangan" style="position: absolute; width: 220px; top: -90px; left: 50%; transform: translateX(-30%); z-index: 2;">
                </div>
                <strong style="position: relative; z-index: 3;"><?php echo htmlspecialchars($institusi['nama_kepala'] ?? 'Hi. NURKOTIM, S.Pd.I., M.M'); ?></strong>
            </div>
            
            <div class="signature-box">
                Lampung Barat, <?php 
                    $tgl_konfirmasi = isset($student['confirmed_at']) ? strtotime($student['confirmed_at']) : time();
                    $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                    echo date('d', $tgl_konfirmasi) . ' ' . $bulan[(int)date('m', $tgl_konfirmasi)] . ' ' . date('Y', $tgl_konfirmasi);
                ?><br>
                Orang Tua / Wali,
                <div class="signature-space">
                    <div class="materai-box">Materai<br>10.000</div>
                </div>
                <strong>(<?php echo isset($student['NAMA_AYAH']) && !empty(trim($student['NAMA_AYAH'])) ? htmlspecialchars($student['NAMA_AYAH']) : '......................................................'; ?>)</strong>
            </div>
        </div>

        <div class="footer-note" style="display: flex; align-items: flex-end; gap: 10px; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px;">
            <?php 
                // Bangun URL absolut lengkap dengan https dan domain
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'apk.masmifhda.sch.id';
                $verifyLink = $protocol . "://" . $host . base_url('verifikasi/pip?id=' . urlencode($student['NIK']));
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($verifyLink);
            ?>
            <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 70px; height: 70px; padding: 2px;">
            <div style="font-size: 8pt; color: #333; line-height: 1.3;">
                <strong>Dokumen Sah & Tervalidasi Sistem</strong><br>
                Scan QR Code di samping untuk verifikasi keaslian dokumen ini secara digital.<br>
                <i>ID Registrasi: <?php echo htmlspecialchars($student['NIK']); ?></i>
            </div>
        </div>
    </div>

</body>
</html>
