<?php
use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) ? $institusi['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
$nama_yayasan = !empty($institusi['nama_yayasan']) ? $institusi['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/' . $institusi['logo'] : Branding::getInstitusiLogo();

$alamatParts = [];
if (!empty($institusi['alamat'])) $alamatParts[] = $institusi['alamat'];
if (!empty($institusi['desa'])) $alamatParts[] = 'Ds. ' . $institusi['desa'];
if (!empty($institusi['kecamatan'])) $alamatParts[] = 'Kec. ' . $institusi['kecamatan'];
if (!empty($institusi['kota'])) $alamatParts[] = 'Kota ' . $institusi['kota'];
if (!empty($institusi['provinsi'])) $alamatParts[] = 'Prov. ' . $institusi['provinsi'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung';
}

$webUrl = !empty($institusi['website']) ? $institusi['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($institusi['email']) ? $institusi['email'] : 'masmiftahulhuda@gmail.com';
$kecamatan = !empty($institusi['kecamatan']) ? $institusi['kecamatan'] : 'Way Tenong';

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$bulanIndoSingkat = [
    1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
];

$tgl_hari_ini = time();
$tgl_cetak_indo = date('j', $tgl_hari_ini) . ' ' . $bulanIndo[(int)date('n', $tgl_hari_ini)] . ' ' . date('Y', $tgl_hari_ini);

$ketua_nama = $ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? 'M AZIZ SULAIMAN, S.Pd';
$kamad_nama = $kamad['nama'] ?? 'Drs. Hamim Hasyim';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Supervisi Pembelajaran - <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($brand['favicon']) ?>">
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm 1.2cm 2cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.35;
            color: #000;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .sheet {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 20px 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .no-print {
            display: block;
        }
        @media print {
            .no-print { display: none !important; }
            body { 
                background: #fff; 
                padding: 0; 
                margin: 0;
            }
            .sheet {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title h1 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .doc-title .sub-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 3px;
            text-transform: uppercase;
        }
        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10pt;
        }
        .jadwal-table th, .jadwal-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .jadwal-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5pt;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div style="position:fixed; top:20px; right:20px; z-index:9999;" class="no-print">
        <button onclick="window.print()" style="background:#059669; color:#fff; border:none; padding:10px 22px; border-radius:8px; font-weight:bold; font-size:14px; cursor:pointer; box-shadow:0 4px 12px rgba(5,150,105,0.3); display:inline-flex; align-items:center; gap:8px;">
            <svg style="width:16px;height:16px;fill:currentColor;" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Cetak Jadwal (A4 1 Lembar)
        </button>
    </div>

    <div class="sheet">
        <!-- KOP SURAT MADRASAH -->
        <table class="kop-table">
            <tr>
                <td style="width: 75px; text-align: center;">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="width: 65px; height: 65px; object-fit: contain;">
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;"><?= htmlspecialchars($nama_yayasan) ?></div>
                    <div style="font-size: 13.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($sekolah_nama) ?></div>
                    <div style="font-size: 8.5pt; font-style: italic; margin-top: 1px;">NSM: 111218040083 &bull; NPSN: 60706240 &bull; Akreditasi B (Baik)</div>
                    <div style="font-size: 8pt; margin-top: 1px;"><?= htmlspecialchars($alamatLengkap) ?></div>
                    <div style="font-size: 8pt; color: #333;">Website: <?= htmlspecialchars($webUrl) ?> &bull; Email: <?= htmlspecialchars($emailAddr) ?></div>
                </td>
            </tr>
        </table>

        <!-- JUDUL JADWAL -->
        <div class="doc-title">
            <h1>JADWAL PELAKSANAAN SUPERVISI PEMBELAJARAN (KELAS)</h1>
            <div class="sub-title">SEMESTER <?= strtoupper(htmlspecialchars($semester)) ?> TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
        </div>

        <!-- TABEL JADWAL -->
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 125px;">Hari / Tanggal</th>
                    <th style="width: 65px;">Waktu</th>
                    <th>Nama Guru Disupervisi</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 50px;">Kelas</th>
                    <th>Supervisor / Observer</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($daftar_supervisi)): ?>
                <tr>
                    <td colspan="7" align="center" style="padding: 15px;">Belum ada jadwal pelaksanaan supervisi yang ditetapkan.</td>
                </tr>
                <?php else: ?>
                    <?php 
                    $no = 1;
                    foreach($daftar_supervisi as $row): 
                        $tgl_item = strtotime($row['tanggal']);
                        $hari_item = $hariIndo[(int)date('w', $tgl_item)] ?? '-';
                        $tgl_formatted = $hari_item . ', ' . date('d', $tgl_item) . ' ' . ($bulanIndoSingkat[(int)date('n', $tgl_item)] ?? date('M', $tgl_item)) . ' ' . date('Y', $tgl_item);
                        $jam_ke = !empty($row['jam_ke']) ? $row['jam_ke'] : 'Jam KBM';
                    ?>
                    <tr>
                        <td align="center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($tgl_formatted) ?></td>
                        <td align="center" style="font-size: 9pt;"><?= htmlspecialchars($jam_ke) ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_guru']) ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_mapel']) ?></td>
                        <td align="center"><?= htmlspecialchars($row['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($row['nama_supervisor'] ?: $kamad_nama) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- CATATAN / KETENTUAN SINGKAT -->
        <div style="font-size: 8.5pt; color: #334155; margin-bottom: 15px; border-left: 3px solid #059669; padding-left: 8px;">
            <strong>Catatan Pelaksanaan:</strong>
            <ol style="margin: 2px 0 0 0; padding-left: 14px;">
                <li>Guru yang bersangkutan menyiapkan Perangkat Ajar (Modul Ajar/RPP, Media, Bahan Ajar, dan LKPD) sebelum jadwal observasi.</li>
                <li>Pengamatan difokuskan pada kegiatan pendahuluan, inti pembelajaran berdiferensiasi, serta asesmen autentik (KKTP).</li>
            </ol>
        </div>

        <!-- TANDA TANGAN (BEBAS NIP) -->
        <table class="signatures-table">
            <tr>
                <td>
                    <p style="margin: 0; line-height: 1.4;">Ketua Panitia Supervisi,</p>
                    <div style="height: 55px;"></div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 10.5pt;">
                        <?= htmlspecialchars($ketua_nama) ?>
                    </p>
                </td>
                <td>
                    <p style="margin: 0; line-height: 1.4;">
                        <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_cetak_indo ?><br>
                        Kepala Madrasah (Penanggung Jawab),
                    </p>
                    <div style="height: 55px;"></div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 10.5pt;">
                        <?= htmlspecialchars($kamad_nama) ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

