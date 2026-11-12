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

$nomor_sk = $config['nomor_sk'] ?? 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026';
$tanggal_sk = $config['tanggal_sk'] ?? '2026-07-15';
$tahun_ajaran = $config['tahun_ajaran'] ?? '2026/2027';
$semester = $config['semester'] ?? 'Ganjil';

$kepala_nama = $config['kamad_nama'] ?? 'Drs. Hamim Hasyim';
$kepala_nip = $config['kamad_nip'] ?? '-';
$pengawas_nama = $config['pengawas_nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I';
$pengawas_nip = $config['pengawas_nip'] ?? '196805141994031002';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_obj = strtotime($tanggal_sk);
$tgl_sk_formatted = date('j', $tgl_obj) . ' ' . $bulanIndo[(int)date('n', $tgl_obj)] . ' ' . date('Y', $tgl_obj);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK Penetapan Tim Supervisi - <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($brand['favicon']) ?>">
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2cm 2cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .no-print {
            display: block;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
        .sk-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .sk-title {
            text-align: center;
            margin-bottom: 24px;
        }
        .sk-title h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .sk-title .nomor {
            font-size: 11pt;
            margin-top: 4px;
        }
        .sk-title .tentang {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .sk-table-konsiderans {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .sk-table-konsiderans td {
            vertical-align: top;
            padding: 3px 0;
        }
        .sk-table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 10.5pt;
        }
        .sk-table-data th, .sk-table-data td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .sk-table-data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div style="position:fixed; top:20px; right:20px; z-index:9999;" class="no-print">
        <button onclick="window.print()" style="background:#059669; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.15);">
            Cetak Naskah SK A4
        </button>
    </div>

    <div style="max-width: 210mm; margin: 0 auto;">
        
        <!-- KOP SURAT MADRASAH -->
        <div class="sk-kop">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 85px; vertical-align: middle; text-align: center;">
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="width: 75px; height: 75px; object-fit: contain;">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase;"><?= htmlspecialchars($nama_yayasan) ?></div>
                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($sekolah_nama) ?></div>
                        <div style="font-size: 9.5pt; font-style: italic; margin-top: 2px;">NSM: 111218040083 &bull; NPSN: 60706240 &bull; Akreditasi B (Baik)</div>
                        <div style="font-size: 9pt; margin-top: 2px;"><?= htmlspecialchars($alamatLengkap) ?></div>
                        <div style="font-size: 9pt; color: #333;">Website: <?= htmlspecialchars($webUrl) ?> &bull; Email: <?= htmlspecialchars($emailAddr) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- JUDUL SK -->
        <div class="sk-title">
            <h1>KEPUTUSAN KEPALA MADRASAH IBTIDAIYAH MIFTAHUL HUDA</h1>
            <div class="nomor">Nomor: <?= htmlspecialchars($nomor_sk) ?></div>
            <div style="margin-top: 10px; font-size: 11pt;">TENTANG</div>
            <div class="tentang">
                PENETAPAN TIM SUPERVISI PEMBELAJARAN (AKADEMIK GURU)<br>
                DAN PENILAI INTERNAL PENJAMINAN MUTU PENDIDIKAN<br>
                TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>
            </div>
            <div style="margin-top: 12px; font-weight: bold;">DENGAN RAHMAT TUHAN YANG MAHA ESA<br>KEPALA MADRASAH IBTIDAIYAH MIFTAHUL HUDA,</div>
        </div>

        <!-- KONSIDERANS -->
        <table class="sk-table-konsiderans">
            <tr>
                <td style="width: 18%; font-weight: bold;">Menimbang</td>
                <td style="width: 3%; text-align: center;">:</td>
                <td style="width: 79%;">
                    <ol style="margin: 0; padding-left: 20px; text-align: justify;">
                        <li style="margin-bottom: 5px;">bahwa untuk meningkatkan mutu proses Kegiatan Belajar Mengajar (KBM) dan profesionalitas pendidik dalam implementasi Kurikulum Merdeka di <?= htmlspecialchars($sekolah_nama) ?>, perlu diselenggarakan supervisi pembelajaran yang terencana dan berkesinambungan;</li>
                        <li style="margin-bottom: 5px;">bahwa untuk menjamin kelancaran, objektivitas, dan validitas hasil pengawasan akademik serta pelaksanaan Penilaian Kinerja Kepala Madrasah (PKKM), perlu dibentuk Tim Supervisi Pembelajaran yang didelegasikan wewenang;</li>
                        <li>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan b, perlu menetapkan Keputusan Kepala Madrasah tentang Tim Supervisi Pembelajaran Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 8px;">Mengingat</td>
                <td style="text-align: center; padding-top: 8px;">:</td>
                <td style="padding-top: 8px;">
                    <ol style="margin: 0; padding-left: 20px; text-align: justify;">
                        <li style="margin-bottom: 4px;">Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                        <li style="margin-bottom: 4px;">Peraturan Menteri Agama Nomor 58 Tahun 2017 tentang Kepala Madrasah;</li>
                        <li style="margin-bottom: 4px;">Keputusan Menteri Agama Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah;</li>
                        <li style="margin-bottom: 4px;">Keputusan Direktur Jenderal Pendidikan Islam tentang Petunjuk Teknis Supervisi Pembelajaran pada Madrasah;</li>
                        <li>Rencana Kerja dan Anggaran Madrasah (RKAM) <?= htmlspecialchars($sekolah_nama) ?> Tahun Anggaran 2026.</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 8px;">Memperhatikan</td>
                <td style="text-align: center; padding-top: 8px;">:</td>
                <td style="padding-top: 8px; text-align: justify;">
                    Hasil Rapat Dewan Guru dan Tenaga Kependidikan <?= htmlspecialchars($sekolah_nama) ?> tentang Pembagian Tugas Pokok dan Pembentukan Tim Supervisi Penjaminan Mutu Madrasah.
                </td>
            </tr>
        </table>

        <!-- PAGE BREAK: MEMUTUSKAN DI HALAMAN KEDUA -->
        <div style="page-break-before: always; break-before: page; text-align: center; font-weight: bold; padding-top: 20px; margin-bottom: 15px; font-size: 11pt; letter-spacing: 1px;">MEMUTUSKAN:</div>

        <table class="sk-table-konsiderans">
            <tr>
                <td style="width: 18%; font-weight: bold;">Menetapkan</td>
                <td style="width: 3%; text-align: center;">:</td>
                <td style="width: 79%; font-weight: bold; text-align: justify;">
                    KEPUTUSAN KEPALA MADRASAH TENTANG PENETAPAN TIM SUPERVISI PEMBELAJARAN (AKADEMIK GURU) <?= htmlspecialchars($sekolah_nama) ?> TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 6px;">KESATU</td>
                <td style="text-align: center; padding-top: 6px;">:</td>
                <td style="padding-top: 6px; text-align: justify;">
                    Menetapkan Susunan Tim Supervisi Pembelajaran sebagaimana tercantum dalam Lampiran yang merupakan bagian tidak terpisahkan dari Keputusan ini.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 6px;">KEDUA</td>
                <td style="text-align: center; padding-top: 6px;">:</td>
                <td style="padding-top: 6px; text-align: justify;">
                    Tim Supervisi sebagaimana dimaksud Diktum KESATU bertugas menyusun instrumen, melaksanakan observasi langsung proses KBM di kelas, memeriksa dokumen administrasi perangkat ajar, menyusun rapor evaluasi, serta merumuskan Rencana Tindak Lanjut (RTL).
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 6px;">KETIGA</td>
                <td style="text-align: center; padding-top: 6px;">:</td>
                <td style="padding-top: 6px; text-align: justify;">
                    Segala biaya yang timbul akibat diterbitkannya Keputusan ini dibebankan pada pos anggaran pendapatan dan belanja madrasah yang sesuai.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-top: 6px;">KEEMPAT</td>
                <td style="text-align: center; padding-top: 6px;">:</td>
                <td style="padding-top: 6px; text-align: justify;">
                    Keputusan ini mulai berlaku pada tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan akan diperbaiki sebagaimana mestinya.
                </td>
            </tr>
        </table>

        <!-- TANDA TANGAN KEPALA MADRASAH -->
        <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
            <div style="text-align: left; width: 45%;">
                Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
                Pada tanggal : <?= $tgl_sk_formatted ?><br><br>
                <strong>KEPALA MADRASAH,</strong><br><br><br><br>
                <strong><u><?= htmlspecialchars($kepala_nama) ?></u></strong>
                <?php if(!empty($kepala_nip) && $kepala_nip !== '-'): ?>
                <br>NIP. <?= htmlspecialchars($kepala_nip) ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 25px; font-size: 9.5pt; color: #334155;">
            <strong>Tembusan disampaikan Kepada Yth:</strong>
            <ol style="margin: 2px 0 0 0; padding-left: 18px;">
                <li>Kepala Kantor Kementerian Agama Kabupaten Lampung Barat</li>
                <li>Pengawas Madrasah Pembina Kemenag Kab. Lampung Barat</li>
                <li>Ketua Yayasan PP Miftahul Huda Mathla'ul Anwar</li>
                <li>Yang bersangkutan untuk dilaksanakan sebagaimana mestinya</li>
                <li>Arsip / Dokumen Bukti Fisik PKKM</li>
            </ol>
        </div>

        <!-- PAGE BREAK UNTUK LAMPIRAN SUSUNAN TIM -->
        <div style="page-break-before: always; margin-top: 30px;">
            <div style="font-size: 9.5pt; margin-bottom: 15px; text-align: right;">
                <strong>LAMPIRAN KEPUTUSAN KEPALA MADRASAH</strong><br>
                Nomor : <?= htmlspecialchars($nomor_sk) ?><br>
                Tanggal : <?= $tgl_sk_formatted ?><br>
                Tentang : Susunan Tim Supervisi Pembelajaran TP <?= htmlspecialchars($tahun_ajaran) ?>
            </div>

            <div style="text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; margin-bottom: 20px;">
                SUSUNAN TIM SUPERVISI PEMBELAJARAN DAN OBSERVER KBM<br>
                <?= htmlspecialchars($sekolah_nama) ?><br>
                TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>
            </div>

            <table class="sk-table-data">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 32%;">Nama Personel / Pegawai</th>
                        <th style="width: 25%;">Peran / Jabatan Tim</th>
                        <th style="width: 38%;">Fokus Bidang Tugas Pengawasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($timSupervisi)): ?>
                    <tr><td colspan="4" align="center">Belum ada anggota tim terdaftar</td></tr>
                    <?php else: ?>
                    <?php $no = 1; foreach($timSupervisi as $ts): 
                        $fokusItem = trim($ts['tugas_fokus'] ?? '');
                        if (empty($fokusItem)) {
                            $p = strtolower(trim($ts['peran_tim'] ?? ''));
                            if (strpos($p, 'penanggung') !== false || strpos($p, 'utama') !== false) {
                                $fokusItem = 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM';
                            } elseif (strpos($p, 'ketua') !== false) {
                                $fokusItem = 'Koordinasi Jadwal Supervisi, Verifikasi Administrasi Guru & Analisis Hasil Observasi';
                            } elseif (strpos($p, 'sekretaris') !== false) {
                                $fokusItem = 'Notulensi Coaching, Administrasi Instrumen & Rekapitulasi Laporan RTL';
                            } elseif (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false) {
                                $fokusItem = 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis';
                            } else {
                                $fokusItem = 'Observasi Pembelajaran Kelas & Pendampingan Perangkat Ajar';
                            }
                        }
                    ?>
                    <tr>
                        <td align="center"><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($ts['nama']) ?></strong><br>
                            <span style="font-size: 8.5pt; color: #475569;"><?= htmlspecialchars($ts['jabatan_madrasah'] ?? 'Guru') ?></span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($ts['peran_tim']) ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars($fokusItem) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 35px; display: flex; justify-content: flex-end;">
                <div style="text-align: left; width: 45%;">
                    <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_sk_formatted ?><br>
                    <strong>KEPALA MADRASAH,</strong><br><br><br><br>
                    <strong><u><?= htmlspecialchars($kepala_nama) ?></u></strong>
                    <?php if(!empty($kepala_nip) && $kepala_nip !== '-'): ?>
                    <br>NIP. <?= htmlspecialchars($kepala_nip) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
