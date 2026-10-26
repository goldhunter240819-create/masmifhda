<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Rekomendasi Kuasa PIP - <?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA'] ?? ''); ?></title>
    <style>
        @page { size: A4; margin: 0; }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.15;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
            font-size: 10.5pt;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 5mm 15mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }
        
        /* Kop Surat Sama */
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
            bottom: -3px;
            left: 0;
            right: 0;
            border-bottom: 1px solid #000;
        }
        .kop-logo { width: 90px; height: 90px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 2px 0 0; font-size: 9pt; }
        
        .title-box {
            text-align: center;
            margin-bottom: 10px;
        }
        .title {
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .nomor {
            font-size: 10.5pt;
        }
        
        .content {
            font-size: 10.5pt;
            text-align: justify;
        }
        
        .indent { padding-left: 20px; margin-bottom: 3px; margin-top: 2px; }
        
        table.form-table {
            width: 100%;
            margin-top: 2px;
            margin-bottom: 3px;
            font-size: 10.5pt;
        }
        table.form-table td { padding: 2px 0; vertical-align: top; }
        table.form-table td:nth-child(1) { width: 170px; }
        table.form-table td:nth-child(2) { width: 15px; }
        
        .signature-section {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 250px;
            text-align: left;
            font-size: 10.5pt;
        }
        
        .signature-space { height: 65px; display: flex; align-items: flex-end; position: relative;}
        
        .btn-print {
            position: fixed; top: 20px; right: 20px; background: #0ea5e9; color: white;
            border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;
            font-family: inherit; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex; align-items: center; gap: 8px; z-index: 1000;
        }
        .btn-print:hover { background: #0284c7; }
        
        @media print {
            body { background: white; padding: 0; }
            .page { box-shadow: none; width: 100%; min-height: auto; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Cetak Surat
    </button>

    <div class="page">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <?php 
                $baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if ($baseUri === '/') $baseUri = '';
                $logo = isset($institusi['logo']) && $institusi['logo'] ? $baseUri . '/uploads/logo/' . $institusi['logo'] : $baseUri . '/assets/images/logo.png';
            ?>
            <img src="<?php echo $logo; ?>" class="kop-logo" alt="Logo" onerror="this.src='<?php echo $baseUri; ?>/assets/images/logo.png'">
            <div class="kop-text">
                <h1><?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH'] ?? 'MI MIFTAHUL HUDA MATHLA\'UL ANWAR'); ?></h1>
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
            <div class="title">SURAT KETERANGAN DAN REKOMENDASI</div>
            <?php 
                $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                $bulanRomawi = $romawi[(int)date('m')];
            ?>
            <div class="nomor">Nomor: MA.h/PP.05/YPP-MH-MA/0208/90/<?php echo $bulanRomawi; ?>/<?php echo date('Y'); ?></div>
        </div>
        
        <div class="content">
            <p style="margin-bottom: 2px;">Yang bertanda tangan di bawah ini:</p>
            
            <table class="form-table indent">
                <tr>
                    <td style="width: 170px;"><strong>Nama</strong></td>
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
                    <td><strong>Nama Lembaga</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($institusi['nama'] ?? 'MI Miftahul Huda Mathla\'ul Anwar'); ?></td>
                </tr>
            </table>

            <p style="margin-bottom: 2px;">Dengan ini menerangkan bahwa:</p>
            
            <table class="form-table indent">
                <tr>
                    <td style="width: 170px;"><strong>Nama Peserta Didik</strong></td>
                    <td style="width: 15px;">:</td>
                    <td><strong><?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA'] ?? ''); ?></strong></td>
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
                    <td><strong>Kelas</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($student['KELAS'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>:</td>
                    <td style="text-transform: uppercase;"><?php echo htmlspecialchars($student['ALAMAT'] ?? '-'); ?></td>
                </tr>
            </table>

            <p style="margin-bottom: 2px;">Adalah benar <strong>peserta didik aktif</strong> pada <strong><?php echo htmlspecialchars($institusi['nama'] ?? 'MI Miftahul Huda Mathla\'ul Anwar'); ?></strong> dan merupakan <strong>penerima Program Indonesia Pintar (PIP)</strong>.</p>
            
            <p style="margin-bottom: 2px;">Berdasarkan kondisi yang diketahui oleh pihak madrasah, orang tua peserta didik <strong>masih hidup namun berdomisili/berada di luar daerah sehingga tidak dapat hadir secara langsung</strong> dalam pengurusan administrasi pencairan dana Program Indonesia Pintar (PIP).</p>
            
            <p style="margin-bottom: 2px;">Selama menjalani pendidikan di madrasah, peserta didik tersebut <strong>tinggal bersama kakak kandungnya</strong>, yaitu:</p>
            
            <table class="form-table indent">
                <tr>
                    <td style="width: 170px;"><strong>Nama</strong></td>
                    <td style="width: 15px;">:</td>
                    <td><?php echo htmlspecialchars(strtoupper($kuasa_nama)); ?></td>
                </tr>
                <tr>
                    <td><strong>NIK</strong></td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($kuasa_nik); ?></td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>:</td>
                    <td style="text-transform: uppercase;"><?php echo htmlspecialchars($kuasa_alamat); ?></td>
                </tr>
            </table>
            
            <p style="margin-bottom: 2px;">Yang bersangkutan selama ini mendampingi, mengasuh, serta memenuhi kebutuhan sehari-hari peserta didik, termasuk keperluan pendidikan.</p>
            
            <p style="margin-bottom: 2px;">Sehubungan dengan hal tersebut, pihak madrasah <strong>merekomendasikan</strong> agar saudara/i tersebut dapat <strong>mewakili peserta didik dalam proses pengurusan administrasi Program Indonesia Pintar (PIP)</strong> pada <strong>PT Bank Rakyat Indonesia (Persero) Tbk.</strong>, sepanjang memenuhi persyaratan dan ketentuan yang berlaku.</p>

            <p style="margin-bottom: 2px;">Surat keterangan ini <strong>bukan merupakan pengalihan hak kepemilikan dana PIP</strong>, melainkan hanya sebagai bentuk keterangan dan rekomendasi dari madrasah mengenai kondisi peserta didik yang sebenarnya.</p>
            
            <p style="margin-bottom: 2px;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
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
                    <!-- Tanda tangan dan stempel basah -->
                </div>
                
                <strong style="position: relative; z-index: 3; text-decoration: underline;"><?php echo htmlspecialchars($institusi['nama_kepala'] ?? 'Hi. NURKOTIM, S.Pd.I., M.M'); ?></strong><br>
                <span style="position: relative; z-index: 3;">NIP. <?php echo htmlspecialchars($institusi['nip_kepala'] ?? '-'); ?></span>
            </div>
        </div>
    </div>

</body>
</html>
