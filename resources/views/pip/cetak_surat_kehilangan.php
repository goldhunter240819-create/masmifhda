<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kehilangan Buku PIP - <?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.2;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 15mm;
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
            padding-bottom: 5px;
            margin-bottom: 10px;
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
        
        .title-box {
            text-align: center;
            margin-bottom: 10px;
        }
        .title {
            font-weight: bold;
            font-size: 16pt;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .nomor {
            font-size: 12pt;
        }
        
        .content {
            font-size: 12pt;
            text-align: justify;
        }
        
        .indent { padding-left: 20px; margin-bottom: 5px; margin-top: 3px; }
        
        table.form-table {
            width: 100%;
            margin-top: 3px;
            margin-bottom: 5px;
            font-size: 12pt;
        }
        table.form-table td { padding: 4px 0; vertical-align: top; }
        table.form-table td:nth-child(1) { width: 220px; }
        table.form-table td:nth-child(2) { width: 15px; }
        
        .signature-section {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        
        .signature-box {
            width: 300px;
            text-align: left;
            font-size: 12pt;
        }
        
        .signature-space { height: 75px; display: flex; align-items: flex-end; position: relative;}
        
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

    <button class="btn-print" onclick="window.print()">Cetak Surat Kehilangan</button>

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
                    $desa = !empty($institusi['desa']) ? ', Desa ' . htmlspecialchars($institusi['desa']) : ', Desa Tambak Jaya';
                    $kec = !empty($institusi['kecamatan']) ? ', Kec. ' . htmlspecialchars($institusi['kecamatan']) : ', Kec. Way Tenong';
                    $kota = !empty($institusi['kota']) ? ', ' . htmlspecialchars($institusi['kota']) : ', Kab. Lampung Barat';
                    $prov = !empty($institusi['provinsi']) ? ', Prov. ' . htmlspecialchars($institusi['provinsi']) : ', Prov. Lampung';
                    $kodepos = !empty($institusi['kodepos']) ? ' - ' . htmlspecialchars($institusi['kodepos']) : ' - 34884';
                    echo $alamat . $desa . $kec . $kota . $prov . $kodepos;
                ?></p>
                <p>Email: <?php echo !empty($institusi['email']) ? htmlspecialchars($institusi['email']) : 'masmiftahulhuda.wt@gmail.com'; ?> | Website: masmifhda.sch.id</p>
            </div>
        </div>
        
        <div class="title-box">
            <div class="title">SURAT KETERANGAN</div>
            <?php 
                $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                $bulanRomawi = $romawi[(int)date('m')];
            ?>
            <div class="nomor">Nomor: MA.h/PP.05/YPP-MH-MA/0208/89/<?php echo $bulanRomawi; ?>/<?php echo date('Y'); ?></div>
        </div>
        
        <div class="content">
            <p>Yang bertanda tangan di bawah ini:</p>
            
            <table class="form-table indent" style="margin-bottom: 10px; margin-top: 5px;">
                <tr>
                    <td style="width: 150px;"><strong>Nama</strong></td>
                    <td style="width: 15px;">:</td>
                    <td><?php echo htmlspecialchars($institusi['nama_kepala'] ?? 'Hi. NURKOTIM, S.Pd.I., M.M'); ?></td>
                </tr>
                <tr>
                    <td><strong>NIP</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($institusi['nip_kepala'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Jabatan</strong></td>
                    <td>:</td>
                    <td>Kepala Madrasah</td>
                </tr>
                <tr>
                    <td><strong>Nama Madrasah</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($institusi['nama'] ?? 'MI Miftahul Huda Mathla\'ul Anwar'); ?></td>
                </tr>
            </table>

            <p>Dengan ini menerangkan bahwa:</p>
            
            <table class="form-table indent" style="margin-top: 5px;">
                <tr>
                    <td><strong>Nama Peserta Didik</strong></td>
                    <td>:</td>
                    <td><strong><?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA']); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>NIS</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['NIS'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>NISN</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['NISN'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Tempat, Tanggal Lahir</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['TEMPAT_LAHIR'] ?? '-'); ?>, <?php echo htmlspecialchars($student['TANGGAL_LAHIR'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Jenis Kelamin</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['JENIS_KELAMIN'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Kelas</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['KELAS'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Nama Orang Tua/Wali</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['NAMA_AYAH'] ?? $student['NAMA_IBU_KANDUNG'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['ALAMAT'] ?? $student['ALAMAT_JALAN'] ?? '-'); ?></td>
                </tr>
            </table>

            <p style="margin-bottom: 3px;">Adalah benar <strong>peserta didik aktif</strong> pada <strong><?php echo htmlspecialchars($institusi['nama'] ?? 'MI Miftahul Huda Mathla\'ul Anwar'); ?></strong> serta merupakan <strong>penerima Program Indonesia Pintar (PIP)</strong> melalui <strong>PT Bank Rakyat Indonesia (Persero) Tbk.</strong></p>
            
            <p style="margin-bottom: 3px;">Berdasarkan laporan dari peserta didik beserta orang tua/wali, <strong>buku rekening tabungan Program Indonesia Pintar (PIP) milik yang bersangkutan telah hilang</strong>, sehingga diperlukan penggantian buku rekening sesuai dengan ketentuan yang berlaku pada <strong>PT Bank Rakyat Indonesia (Persero) Tbk.</strong></p>
            
            <p style="margin-bottom: 3px;">Apabila diperlukan, berikut data rekening yang dimiliki peserta didik:</p>
            
            <table class="form-table indent">
                <tr>
                    <td><strong>Nomor Rekening</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['NOMOR_REKENING'] ?? '........................................'); ?></td>
                </tr>
            </table>
            
            <p style="margin-bottom: 3px;">Surat keterangan ini diterbitkan sebagai <strong>salah satu persyaratan administrasi dalam pengurusan penggantian buku rekening tabungan PIP yang hilang</strong> pada <strong>PT Bank Rakyat Indonesia (Persero) Tbk.</strong></p>
            
            <p style="margin-bottom: 3px;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.</p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <?php 
                    $desa_kop = !empty($institusi['desa']) ? htmlspecialchars($institusi['desa']) : 'Tambak Jaya';
                    $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                    $tanggal = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
                ?>
                <?php echo $desa_kop; ?>, <?php echo $tanggal; ?><br><br>
                Mengetahui,<br>
                <strong>Kepala MA Miftahul Huda</strong>
                
                <div class="signature-space">
                    <img src="<?php echo base_url('stempelma.png'); ?>" alt="Stempel" style="position: absolute; width: 140px; top: -10px; left: -80px; z-index: 1; opacity: 0.9;">
                    <img src="<?php echo base_url('ttdkamad.png'); ?>" alt="Tanda Tangan" style="position: absolute; width: 220px; top: -80px; left: -10px; z-index: 2;">
                </div>
                
                <strong style="position: relative; z-index: 3; text-decoration: underline;"><?php echo htmlspecialchars($institusi['nama_kepala'] ?? 'Hi. NURKOTIM, S.Pd.I., M.M'); ?></strong><br>
                <span style="position: relative; z-index: 3;">NIP. <?php echo htmlspecialchars($institusi['nip_kepala'] ?? '-'); ?></span>
            </div>
        </div>
    </div>

</body>
</html>
