<style>
    body {
        background: #f1f5f9;
        margin: 0;
        padding: 20px;
    }
    .print-toolbar {
        background: #fff;
        padding: 15px 20px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    .surat-container {
        background: #fff;
        width: 210mm; /* A4 width */
        min-height: 297mm; /* A4 height */
        margin: 0 auto;
        padding: 20mm;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        box-sizing: border-box;
    }
    .btn-print {
        background: #0ea5e9;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }
    .btn-print:hover { background: #0284c7; }
    .btn-back {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-back:hover { background: #e2e8f0; color: #1e293b; }
    
    @media print {
        body { padding: 0; background: #fff; }
        .print-toolbar { display: none; }
        .surat-container {
            width: 100%; margin: 0; padding: 0;
            box-shadow: none; min-height: auto;
            zoom: 0.95; /* Scale down slightly to ensure it fits on one page */
        }
        @page { size: A4; margin: 1cm; } /* Margin narrow (1 cm) */
    }

    /* KOP SURAT */
    .kop-surat {
        display: flex;
        align-items: center;
        border-bottom: 3px solid #000;
        padding-bottom: 10px;
        margin-bottom: 3px;
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
    .kop-text h2 { margin: 5px 0 0; font-size: 16px; font-weight: normal; }
    .kop-text p { margin: 5px 0 0; font-size: 10.5px; }

    /* ISI SURAT */
    .surat-title {
        text-align: center;
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .surat-title h3 {
        margin: 0;
        font-size: 18px;
        text-decoration: underline;
    }
    .surat-title p {
        margin: 5px 0 0;
        font-size: 14px;
    }

    .surat-body {
        font-size: 16px;
        line-height: 1.6;
        color: #000;
    }
    .surat-table {
        width: 100%;
        margin: 20px 0;
    }
    .surat-table td {
        padding: 5px 0;
        vertical-align: top;
    }
    .surat-table td:first-child { width: 200px; }
    .surat-table td:nth-child(2) { width: 20px; }

    .ttd-section {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .ttd-box {
        text-align: center;
        width: 300px;
    }
    .ttd-box p { margin: 0 0 5px; }
    .ttd-box .ttd-name {
        margin-top: 80px;
        font-weight: bold;
        text-decoration: underline;
    }

    /* LAMPIRAN */
    .lampiran-container {
        page-break-before: always;
    }
    .lampiran-title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .table-lampiran {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .table-lampiran th, .table-lampiran td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }
    .table-lampiran th {
        background: #f1f5f9;
        font-weight: bold;
        text-align: center;
    }
    .table-lampiran td.center {
        text-align: center;
    }
    .lampiran-container .ttd-section {
        margin-top: 80px; /* Tambahan jarak dari tabel ke blok TTD */
    }
</style>

<div class="print-toolbar">
    <a href="#" onclick="window.location.href = window.location.pathname.replace(base_url('pip/surat'), base_url('pip')); return false;" class="btn-back">
        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
    </a>
    <button onclick="window.print()" class="btn-print">
        <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak / Download Surat
    </button>
</div>

<div class="surat-container">
    <div class="kop-surat">
        <?php 
            $baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
            if ($baseUri === '/') $baseUri = '';
            $logo = isset($institusi['logo']) && $institusi['logo'] ? $baseUri . '/uploads/logo/' . $institusi['logo'] : $baseUri . '/assets/images/logo.png';
        ?>
        <img src="<?php echo $logo; ?>" class="kop-logo" alt="Logo" onerror="this.src='<?php echo $baseUri; ?>/assets/images/logo.png'">
        <div class="kop-text">
            <h1><?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH']); ?></h1>
            <p><?php echo htmlspecialchars($institusi['alamat'] ?? ''); ?>, Desa <?php echo htmlspecialchars($institusi['desa'] ?? ''); ?>, Kec. <?php echo htmlspecialchars($institusi['kecamatan'] ?? ''); ?>, <?php echo htmlspecialchars($institusi['kota'] ?? ''); ?>, Prov. <?php echo htmlspecialchars($institusi['provinsi'] ?? ''); ?> - <?php echo htmlspecialchars($institusi['kodepos'] ?? ''); ?></p>
            <p>Email: <?php echo htmlspecialchars($institusi['email'] ?? '-'); ?> | Website: masmifhda.sch.id</p>
        </div>
    </div>

    <div class="surat-title">
        <h3>SURAT KETERANGAN PENERIMA PIP</h3>
        <?php
            $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
            $romanMonth = $romanMonths[(int)date('m')];
            $year = date('Y');
            $nomor_surat = "MA.h/PP.05/YPP-MH-MA/0208/87/{$romanMonth}/{$year}";
        ?>
        <p>Nomor: <?php echo $nomor_surat; ?></p>
    </div>

    <div class="surat-body">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table class="surat-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($institusi['nama_kepala'] ?? '.......................................................'); ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Kepala Madrasah</td>
            </tr>
            <tr>
                <td>Nama Lembaga</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH']); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($institusi['alamat'] ?? ''); ?>, Kec. <?php echo htmlspecialchars($institusi['kecamatan'] ?? ''); ?>, <?php echo htmlspecialchars($institusi['kota'] ?? ''); ?></td>
            </tr>
        </table>

        <p>Dengan ini menerangkan bahwa:</p>
        <table class="surat-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong><?php echo htmlspecialchars($student['NAMA_SISWA']); ?></strong></td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($student['NISN']); ?></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td><?php echo htmlspecialchars(ucwords(strtolower($student['LAHIR_TEMPAT']))); ?>, <?php 
                    $date = strtotime($student['LAHIR_TANGGAL']);
                    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    echo date('d', $date) . ' ' . $months[(int)date('m', $date)] . ' ' . date('Y', $date);
                ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($student['KELAS']); ?></td>
            </tr>
            <tr>
                <td>Nama Ibu</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($student['NAMA_IBU']); ?></td>
            </tr>
            <tr>
                <td>No. Rekening</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($student['NOMOR_REKENING'] ?? '-'); ?> <?php if(!empty($student['NAMA_BANK'])) echo '(' . htmlspecialchars($student['NAMA_BANK']) . ')'; ?></td>
            </tr>
        </table>

        <p>Adalah benar peserta didik pada <?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH']); ?> dan <strong>terdaftar sebagai penerima Program Indonesia Pintar (PIP)</strong> pada tahun <?php echo date('Y'); ?>.</p>

        <p>Surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="ttd-section">
            <div style="display: flex; align-items: flex-end; gap: 10px; max-width: 60%; margin-bottom: 5px;">
                <?php 
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'] ?? 'apk.masmifhda.sch.id';
                    $verifyLink = $protocol . "://" . $host . base_url('pip/surat?token=' . urlencode($_GET['token']));
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($verifyLink);
                ?>
                <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 70px; height: 70px; padding: 2px; border: 1px solid #ccc;">
                <div style="font-size: 7.5pt; color: #333; line-height: 1.2;">
                    <strong>Dokumen Sah & Tervalidasi Sistem</strong><br>
                    Scan QR Code verifikasi dokumen digital.<br>
                    <i>ID Registrasi: <?php echo htmlspecialchars($student['NIK']); ?></i>
                </div>
            </div>

            <div class="ttd-box">
                <?php
                    $kota = $institusi['kota'] ?? 'Kotabumi';
                    $kota = ucwords(strtolower(str_replace(['KABUPATEN ', 'KAB. ', 'KOTA '], '', $kota)));
                    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    $tgl_konfirmasi = isset($student['confirmed_at']) ? strtotime($student['confirmed_at']) : time();
                    $today = date('d', $tgl_konfirmasi) . ' ' . $months[(int)date('m', $tgl_konfirmasi)] . ' ' . date('Y', $tgl_konfirmasi);
                ?>
                <p><?php echo htmlspecialchars($kota); ?>, <?php echo $today; ?></p>
                <p>Kepala Madrasah</p>
                <div style="position: relative; height: 70px; display: flex; align-items: flex-end; justify-content: center;">
                    <img src="<?php echo base_url('stempelma.png'); ?>" alt="Stempel" style="position: absolute; width: 140px; top: -50px; left: 5px; z-index: 1; opacity: 0.9;">
                    <img src="<?php echo base_url('ttdkamad.png'); ?>" alt="Tanda Tangan" style="position: absolute; width: 220px; top: -90px; left: 50%; transform: translateX(-35%); z-index: 2;">
                </div>
                <div class="ttd-name" style="position: relative; z-index: 3; margin-top: 10px;"><strong><?php echo htmlspecialchars($institusi['nama_kepala'] ?? '........................................'); ?></strong></div>
            </div>
        </div>
    </div>
</div>

<!-- LAMPIRAN (Halaman 2 dst) -->
<div class="surat-container lampiran-container">
    <div class="lampiran-title">
        DAFTAR PENERIMA PROGRAM INDONESIA PINTAR (PIP)<br>
        <?php echo htmlspecialchars($institusi['nama'] ?? $student['NAMA_MADRASAH']); ?><br>
        TAHUN <?php echo date('Y'); ?>
    </div>
    
    <table class="table-lampiran">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Nama Siswa</th>
                <th style="width: 20%;">NISN</th>
                <th style="width: 20%;">NIK</th>
                <th style="width: 25%;">No. Rekening</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($data) && is_array($data)) {
                $no = 1;
                foreach ($data as $item) {
                    echo '<tr>';
                    echo '<td class="center">' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($item['NAMA_SISWA'] ?? '-') . '</td>';
                    echo '<td class="center">' . htmlspecialchars($item['NISN'] ?? '-') . '</td>';
                    echo '<td class="center">' . htmlspecialchars($item['NIK'] ?? '-') . '</td>';
                    echo '<td class="center">' . htmlspecialchars($item['NOMOR_REKENING'] ?? '-') . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="center">Data tidak tersedia</td></tr>';
            }
            ?>
        </tbody>
    </table>
    
    <div class="ttd-section">
        <div style="display: flex; align-items: flex-end; gap: 10px; max-width: 60%; margin-bottom: 5px;">
            <?php 
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'apk.masmifhda.sch.id';
                $komparasiLink = $protocol . "://" . $host . base_url('pip/komparasi');
                $qrUrlLampiran = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($komparasiLink);
            ?>
            <img src="<?php echo $qrUrlLampiran; ?>" alt="QR Code Lampiran" style="width: 70px; height: 70px; padding: 2px; border: 1px solid #ccc;">
            <div style="font-size: 7.5pt; color: #333; line-height: 1.2;">
                <strong>Daftar Penerima & Komparasi Data PIP</strong><br>
                Scan QR Code untuk melihat komparasi.<br>
                <i>Sistem Informasi Akademik</i>
            </div>
        </div>

        <div class="ttd-box">
            <p><?php echo htmlspecialchars($kota); ?>, <?php echo $today; ?></p>
            <p>Kepala Madrasah</p>
            <div style="position: relative; height: 70px; display: flex; align-items: flex-end; justify-content: center;">
                <img src="<?php echo base_url('stempelma.png'); ?>" alt="Stempel" style="position: absolute; width: 140px; top: -50px; left: 5px; z-index: 1; opacity: 0.9;">
                <img src="<?php echo base_url('ttdkamad.png'); ?>" alt="Tanda Tangan" style="position: absolute; width: 220px; top: -90px; left: 50%; transform: translateX(-35%); z-index: 2;">
            </div>
            <div class="ttd-name" style="position: relative; z-index: 3; margin-top: 10px;"><strong><?php echo htmlspecialchars($institusi['nama_kepala'] ?? '........................................'); ?></strong></div>
        </div>
    </div>
</div>

<script>
// --- Auto Cetak saat halaman dibuka ---
window.onload = function() {
    window.print();
};

// --- Anti Inspeksi (Mencegah Klik Kanan & Developer Tools) ---
document.addEventListener('contextmenu', event => event.preventDefault()); // Block right click
document.onkeydown = function(e) {
    if (e.keyCode == 123) return false; // F12
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) return false; // Ctrl+Shift+I
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) return false; // Ctrl+Shift+C
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) return false; // Ctrl+Shift+J
    if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false; // Ctrl+U
}
</script>
