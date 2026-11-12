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
if (!empty($institusi['kota'])) $alamatParts[] = $institusi['kota'];
if (!empty($institusi['provinsi'])) $alamatParts[] = 'Prov. ' . $institusi['provinsi'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung';
}

$kepala_nama = $penanggung_jawab['nama'] ?? $kamad['nama'] ?? 'Drs. Hamim Hasyim';
$kepala_nip = (!empty($penanggung_jawab['nip']) && $penanggung_jawab['nip'] !== '-') ? $penanggung_jawab['nip'] : ((!empty($kamad['nip']) && $kamad['nip'] !== '-') ? $kamad['nip'] : '');

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_raw = !empty($_GET['tanggal']) ? $_GET['tanggal'] : (!empty($config['tanggal_laporan']) ? $config['tanggal_laporan'] : date('Y-m-d'));
$tgl_obj = strtotime($tgl_raw);
$tgl_cetak = date('j', $tgl_obj) . ' ' . $bulanIndo[(int)date('n', $tgl_obj)] . ' ' . date('Y', $tgl_obj);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Matriks Tindak Lanjut Supervisi Guru - <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($brand['favicon']) ?>">
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .no-print {
            background: #1e293b;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-family: system-ui, sans-serif;
        }
        .btn-print {
            background: #059669;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            text-align: center;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h1 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 0;
            font-size: 9pt;
            font-style: italic;
        }
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 4px;
            text-decoration: underline;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 18px;
        }
        table.rtl-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.rtl-table th, table.rtl-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.rtl-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
            font-size: 10.5pt;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <div>
        <strong>Dokumen Resmi PKKM Komponen 4.3</strong> &mdash; Matriks Rencana Tindak Lanjut (RTL) Hasil Supervisi Guru
    </div>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print()" class="btn-print">&#128424; Cetak / Simpan PDF</button>
        <button onclick="window.close()" style="background:#475569; color:#fff; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;">Tutup</button>
    </div>
</div>

<div class="kop-surat">
    <?php if(!empty($institusi['logo'])): ?>
    <img src="<?= htmlspecialchars($logoUrl) ?>" class="kop-logo" alt="Logo">
    <?php endif; ?>
    <div class="kop-text">
        <h2><?= htmlspecialchars($nama_yayasan) ?></h2>
        <h1><?= htmlspecialchars($sekolah_nama) ?></h1>
        <p><?= htmlspecialchars($alamatLengkap) ?> | Website: <?= htmlspecialchars($institusi['website'] ?? 'masmifhda.sch.id') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? 'masmiftahulhuda@gmail.com') ?></p>
    </div>
</div>

<div class="doc-title">MATRIKS RENCANA TINDAK LANJUT (RTL) HASIL SUPERVISI GURU</div>
<div class="doc-subtitle">TAHUN AJARAN 2026/2027 &mdash; SEMESTER GANJIL</div>

<table class="rtl-table">
    <thead>
        <tr>
            <th style="width:30px;">No</th>
            <th style="width:140px;">Nama Guru & NIP</th>
            <th style="width:110px;">Mapel / Kelas</th>
            <th style="width:65px;">Nilai & Predikat</th>
            <th>Temuan Masalah / Catatan KBM</th>
            <th>Bentuk Program Tindak Lanjut</th>
            <th style="width:90px;">Target Waktu</th>
            <th style="width:110px;">Supervisor</th>
            <th style="width:95px;">Status Realisasi</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($daftar_supervisi)): ?>
        <tr>
            <td colspan="9" align="center" style="padding: 20px;">Belum ada data hasil supervisi yang tercatat.</td>
        </tr>
        <?php else: 
            $no = 1;
            foreach($daftar_supervisi as $row):
                $nilai = (float)($row['nilai'] ?? 0);
                if($nilai >= 91) $predikat = 'Amat Baik';
                elseif($nilai >= 81) $predikat = 'Baik';
                elseif($nilai >= 71) $predikat = 'Cukup';
                else $predikat = 'Kurang';

                $jenis = $row['tindak_lanjut_jenis'] ?? '';
                $uraian = $row['tindak_lanjut'] ?? '';
                if(empty($uraian)) {
                    if($nilai < 70) {
                        $jenis = 'Supervisi Klinis & MOOC Pintar';
                        $uraian = 'Bimbingan klinis intensif perencanaan modul ajar dan pelatihan MOOC PINTAR Kemenag.';
                    } elseif($nilai < 80) {
                        $jenis = 'Workshop Kombel KKG';
                        $uraian = 'Pendampingan variasi media pembelajaran interaktif berbasis TIK di Komunitas Belajar KKG.';
                    } else {
                        $jenis = 'Sharing Praktik Baik';
                        $uraian = 'Penguatan pembelajaran berdiferensiasi dan menjadi model sharing praktik baik guru sejawat.';
                    }
                }
                $status = $row['tindak_lanjut_status'] ?? 'Terlaksana';
                if ($status === 'Belum Terlaksana' && $nilai > 0) $status = 'Dalam Proses';
        ?>
        <tr>
            <td align="center"><?= $no++ ?></td>
            <td>
                <strong><?= htmlspecialchars($row['nama_guru']) ?></strong><br>
                <span style="font-size:8.5pt;">NIP: <?= htmlspecialchars($row['guru_nip'] ?: '-') ?></span>
            </td>
            <td><?= htmlspecialchars($row['nama_mapel']) ?><br><small>Kelas <?= htmlspecialchars($row['nama_kelas']) ?></small></td>
            <td align="center">
                <strong><?= $nilai > 0 ? $nilai : '-' ?></strong><br>
                <small>(<?= $predikat ?>)</small>
            </td>
            <td><?= !empty($row['catatan_evaluasi']) ? htmlspecialchars($row['catatan_evaluasi']) : 'Perlu penguatan strategi pembelajaran aktif dan media ajar interaktif' ?></td>
            <td>
                <?php if(!empty($jenis)): ?><strong>[<?= htmlspecialchars($jenis) ?>]</strong><br><?php endif; ?>
                <?= htmlspecialchars($uraian) ?>
                <?php if(!empty($row['tindak_lanjut_realisasi'])): ?>
                <br><small><em>Realisasi: <?= htmlspecialchars($row['tindak_lanjut_realisasi']) ?></em></small>
                <?php endif; ?>
            </td>
            <td align="center"><?= htmlspecialchars($row['tindak_lanjut_deadline'] ?: 'Oktober 2026') ?></td>
            <td><?= htmlspecialchars($row['nama_supervisor']) ?></td>
            <td align="center"><strong><?= htmlspecialchars($status) ?></strong></td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td style="width: 33%; vertical-align: top;">
            Ketua Panitia,<br><br><br><br>
            <strong><u><?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? '-') ?></u></strong>
        </td>
        <?php if(!empty($sekretaris['nama'])): ?>
        <td style="width: 33%; vertical-align: top;">
            Sekretaris Panitia,<br><br><br><br>
            <strong><u><?= htmlspecialchars($sekretaris['nama']) ?></u></strong>
        </td>
        <?php endif; ?>
        <td style="width: 34%; vertical-align: top;">
            <?= htmlspecialchars($institusi['kecamatan'] ?? 'Way Tenong') ?>, <?= $tgl_cetak ?><br>
            Mengetahui,<br>
            Penanggung Jawab (Kepala Madrasah)<br><br><br><br>
            <strong><u><?= htmlspecialchars($penanggung_jawab['nama'] ?? $kepala_nama) ?></u></strong>
        </td>
    </tr>
</table>

</body>
</html>
