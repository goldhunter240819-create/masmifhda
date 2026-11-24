<?php
$topikInfo = $topikInfo ?? [];
$paketInfo = $paketInfo ?? [];
$soals = $soals ?? [];
$breakdown = $breakdown ?? [];
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Sekincau, Lampung Barat'];

// Logo resmi kop surat
$kopLogoUrl = '/assets/images/logo.png';
if (!empty($kop['logo'])) {
    $kopLogoUrl = function_exists('base_url') ? base_url('assets/images/' . $kop['logo']) : ('/assets/images/' . $kop['logo']);
} elseif (class_exists('\App\Core\Branding')) {
    try { 
        $kopLogoUrl = \App\Core\Branding::getInstitusiLogo(); 
    } catch (\Throwable $e) {}
}

$namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA';
$namaMadrasah = $kop['nama_madrasah'] ?? 'MADRASAH ALIYAH MIFTAHUL HUDA';
$alamatJalan = $kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu, Kec. Sekincau, Kab. Lampung Barat';
$telepon = $kop['telepon'] ?? '';
$email = $kop['email'] ?? '';
$website = $kop['website'] ?? 'masmifhda.sch.id';

$mapelNama = $paketInfo['mapel_nama'] ?? 'Mata Pelajaran';
$kelasNama = $paketInfo['kelas_nama'] ?? 'Kelas X';
$kelasSasaran = $paketInfo['kelas_sasaran'] ?? '';
$guruNama = $paketInfo['guru_nama'] ?? 'Guru Pengampu';
$durasiMenit = (int)($paketInfo['durasi_menit'] ?? 90);
$kkm = (int)($paketInfo['kkm'] ?? 75);

$topikNama = !empty($topikInfo['nama_topik']) ? trim($topikInfo['nama_topik']) : 'ASESMEN MADRASAH';
$tahunAjaran = !empty($topikInfo['tahun_ajaran']) ? trim($topikInfo['tahun_ajaran']) : '2026/2027';
$semester = !empty($topikInfo['semester']) ? trim($topikInfo['semester']) : 'Ganjil';

$tanggalCetak = date('Y-m-d');
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglFormatIndo = date('j', strtotime($tanggalCetak)) . ' ' . $bulanIndo[(int)date('n', strtotime($tanggalCetak))] . ' ' . date('Y', strtotime($tanggalCetak));

function renderKontenTeks($str) {
    if (empty($str)) return '';
    if ($str !== strip_tags($str)) {
        return $str;
    }
    return nl2br(htmlspecialchars($str));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naskah Soal & Kunci: <?= htmlspecialchars($mapelNama) ?> (<?= htmlspecialchars($kelasNama) ?>) - MAS MIFTAHUL HUDA</title>
    <link rel="icon" type="image/png" href="<?= $kopLogoUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: #0f172a; 
            font-family: 'Plus Jakarta Sans', Arial, sans-serif; 
            color: #0f172a; 
            padding: 20px; 
            min-height: 100vh;
        }

        /* Top Action Bar (Screen Only) */
        .no-print-bar { 
            max-width: 900px; 
            margin: 0 auto 20px; 
            background: #ffffff; 
            padding: 14px 20px; 
            border-radius: 14px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 15px; 
            flex-wrap: wrap;
            border: 1px solid #cbd5e1; 
        }
        .btn-ctrl { 
            padding: 9px 18px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 800; 
            text-decoration: none; 
            cursor: pointer; 
            border: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.2s;
        }
        .btn-print { 
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.45);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-back:hover {
            background: #e2e8f0;
        }
        .toggle-opt {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            user-select: none;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .toggle-opt input {
            accent-color: #0284c7;
            cursor: pointer;
        }

        /* Lembar Kertas A4 */
        .paper-sheet { 
            background: #ffffff; 
            width: 100%; 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 35px 45px; 
            border-radius: 6px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            color: #000000;
        }

        /* Kop Surat */
        .kop-wrapper {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
            gap: 16px;
        }
        .kop-logo {
            width: 76px;
            height: 76px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-kemenag {
            font-size: 10.5pt;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .kop-yayasan {
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .kop-madrasah {
            font-size: 14pt;
            font-weight: 900;
            color: #000;
            margin: 2px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #222;
            line-height: 1.3;
        }
        .kop-kontak {
            font-size: 8pt;
            color: #333;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title h2 {
            font-size: 12.5pt;
            font-weight: 900;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9.5pt;
            font-weight: 700;
            margin-top: 3px;
            color: #111;
        }

        /* Metadata Tabel */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 16px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .meta-table td {
            padding: 5px 6px;
            vertical-align: top;
        }

        /* Box Petunjuk */
        .petunjuk-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 8.2pt;
            margin-bottom: 16px;
            line-height: 1.45;
        }
        .petunjuk-box strong {
            color: #0f172a;
        }

        /* Daftar Soal */
        .soal-item {
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px dashed #cbd5e1;
            page-break-inside: avoid;
        }
        .soal-item:last-child {
            border-bottom: none;
        }
        .soal-header {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 9.5pt;
            font-weight: 700;
            color: #000;
        }
        .soal-nomor {
            font-weight: 900;
            min-width: 26px;
            color: #000;
        }
        .soal-teks {
            flex: 1;
            line-height: 1.5;
            font-weight: 500;
        }
        .soal-teks img {
            max-width: 100%;
            height: auto;
            margin: 6px 0;
            border-radius: 4px;
        }
        .soal-teks p {
            margin-bottom: 6px;
        }

        /* Opsi Jawaban */
        .opsi-list {
            margin-left: 28px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 8px;
        }
        .opsi-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 9pt;
            line-height: 1.4;
            padding: 3px 6px;
            border-radius: 4px;
        }
        .opsi-item.is-kunci {
            background: #ecfdf5;
            border-left: 3px solid #059669;
            font-weight: 700;
        }
        .opsi-huruf {
            font-weight: 800;
            min-width: 18px;
            color: #000;
        }
        .opsi-teks {
            flex: 1;
        }
        .badge-kunci-inline {
            background: #059669;
            color: white;
            font-size: 7pt;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            margin-left: 8px;
            display: inline-block;
        }

        /* Box Kunci Jawaban */
        .kunci-box {
            margin-left: 28px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 3px solid #d97706;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 8.5pt;
            margin-top: 6px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .kunci-box strong {
            color: #92400e;
        }
        .kunci-val {
            font-weight: 900;
            color: #b45309;
            background: #fef3c7;
            padding: 1px 7px;
            border-radius: 4px;
            font-size: 9pt;
        }

        /* Matriks Kunci Jawaban di Akhir Dokumen */
        .matriks-section {
            margin-top: 25px;
            padding-top: 16px;
            border-top: 2px solid #000;
            page-break-inside: avoid;
        }
        .matriks-title {
            text-align: center;
            font-size: 10.5pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .matriks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
            text-align: center;
        }
        .matriks-table th {
            background: #f1f5f9;
            border: 1px solid #000;
            padding: 5px;
            font-weight: 800;
        }
        .matriks-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            font-weight: 700;
        }
        .matriks-key-val {
            color: #059669;
            font-weight: 900;
            font-size: 9pt;
        }

        /* Signatures */
        .ttd-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .ttd-box {
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 65px;
        }
        .ttd-name {
            font-weight: 800;
            text-decoration: underline;
        }

        /* PRINT STYLES */
        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
            }
            .no-print-bar { 
                display: none !important; 
            }
            .paper-sheet { 
                box-shadow: none !important; 
                padding: 15mm 15mm !important; 
                max-width: 100% !important; 
            }
            @page { 
                size: A4; 
                margin: 10mm; 
            }
            .hide-kunci .kunci-box {
                display: none !important;
            }
            .hide-kunci .badge-kunci-inline {
                display: none !important;
            }
            .hide-kunci .opsi-item.is-kunci {
                background: transparent !important;
                border-left: none !important;
                font-weight: normal !important;
            }
            .hide-matriks {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Control Bar (Layar Saja) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <a href="javascript:history.back()" class="btn-ctrl btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            
            <label class="toggle-opt">
                <input type="checkbox" id="checkTampilKunci" checked onchange="toggleKunciDisplay()">
                <span>Tampilkan Kunci Jawaban di Soal</span>
            </label>

            <label class="toggle-opt">
                <input type="checkbox" id="checkTampilMatriks" checked onchange="toggleMatriksDisplay()">
                <span>Sertakan Lembar Kunci Jawaban</span>
            </label>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (A4)
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Kertas A4 -->
    <div class="paper-sheet" id="printContent">
        
        <!-- KOP SURAT RESMI -->
        <div class="kop-wrapper">
            <img src="<?= $kopLogoUrl ?>" alt="Logo" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <div class="kop-kemenag">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="kop-yayasan"><?= htmlspecialchars($namaYayasan) ?></div>
                <div class="kop-madrasah"><?= htmlspecialchars($namaMadrasah) ?></div>
                <div class="kop-alamat"><?= htmlspecialchars($alamatJalan) ?></div>
                <div class="kop-kontak">
                    <?php if(!empty($telepon)): ?>Telp: <?= htmlspecialchars($telepon) ?> | <?php endif; ?>
                    <?php if(!empty($email)): ?>Email: <?= htmlspecialchars($email) ?> | <?php endif; ?>
                    Website: <?= htmlspecialchars($website) ?>
                </div>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h2>NASKAH SOAL & KUNCI JAWABAN</h2>
            <p><?= htmlspecialchars($topikNama) ?> TAHUN AJARAN <?= htmlspecialchars($tahunAjaran) ?> (SEMESTER <?= strtoupper(htmlspecialchars($semester)) ?>)</p>
        </div>

        <!-- METADATA ASESMEN -->
        <table class="meta-table">
            <tr>
                <td style="width: 17%; font-weight: 700;">Mata Pelajaran</td>
                <td style="width: 3%;">:</td>
                <td style="width: 35%; font-weight: 800;"><?= htmlspecialchars($mapelNama) ?></td>
                <td style="width: 17%; font-weight: 700;">Tingkat / Kelas</td>
                <td style="width: 3%;">:</td>
                <td style="width: 25%; font-weight: 800;">
                    <?= htmlspecialchars($kelasNama) ?>
                    <?php if (!empty($kelasSasaran)): ?>
                        <span style="font-weight: 600; color: #475569;">(<?= htmlspecialchars($kelasSasaran) ?>)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 700;">Guru Pengampu</td>
                <td>:</td>
                <td><?= htmlspecialchars($guruNama) ?></td>
                <td style="font-weight: 700;">Alokasi Waktu</td>
                <td>:</td>
                <td><?= $durasiMenit ?> Menit</td>
            </tr>
            <tr>
                <td style="font-weight: 700;">Jumlah Butir Soal</td>
                <td>:</td>
                <td>
                    <strong><?= count($soals) ?> Butir</strong>
                    <?php if (!empty($breakdown['sec_pg'])): ?>(<?= $breakdown['sec_pg'] ?> PG)<?php endif; ?>
                    <?php if (!empty($breakdown['sec_pgk'])): ?>(<?= $breakdown['sec_pgk'] ?> PGK)<?php endif; ?>
                    <?php if (!empty($breakdown['sec_essay'])): ?>(<?= $breakdown['sec_essay'] ?> Essay)<?php endif; ?>
                </td>
                <td style="font-weight: 700;">Standar KKM</td>
                <td>:</td>
                <td><?= $kkm ?> Poin</td>
            </tr>
        </table>

        <!-- PETUNJUK UMUM -->
        <div class="petunjuk-box">
            <strong>PETUNJUK UMUM:</strong>
            <ol style="margin-left: 18px; margin-top: 3px;">
                <li>Bacalah basmalah dan periksa butir soal dengan teliti sebelum menjawab.</li>
                <li>Dahulukan menjawab butir soal yang dianggap paling mudah.</li>
                <li>Dokumen ini merupakan arsip naskah soal resmi CBT yang telah diverifikasi dan dikunci oleh panitia asesmen madrasah.</li>
            </ol>
        </div>

        <!-- DAFTAR BUTIR SOAL -->
        <div class="daftar-soal-wrapper">
            <?php if (empty($soals)): ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8; font-style: italic;">
                    Belum ada butir soal pada paket mata pelajaran ini.
                </div>
            <?php else: ?>
                <?php $no = 1; foreach ($soals as $s): 
                    $tipe = $s['tipe_soal'] ?: 'sec_pg';
                    $kunci = trim((string)($s['kunci_jawaban'] ?? ''));
                ?>
                    <div class="soal-item">
                        <div class="soal-header">
                            <span class="soal-nomor"><?= $no++ ?>.</span>
                            <div class="soal-teks">
                                <?= renderKontenTeks($s['pertanyaan']) ?>
                            </div>
                        </div>

                        <!-- Opsi Jawaban (Jika Pilihan Ganda / PG Kompleks) -->
                        <?php if ($tipe === 'sec_pg' || $tipe === 'sec_pgk'): ?>
                            <div class="opsi-list">
                                <?php 
                                $opsiList = ['A', 'B', 'C', 'D', 'E'];
                                foreach ($opsiList as $o): 
                                    $fName = 'opsi_' . strtolower($o);
                                    if (empty($s[$fName])) continue;
                                    
                                    // Cek apakah opsi ini adalah kunci
                                    $isKunci = false;
                                    if ($tipe === 'sec_pg') {
                                        $isKunci = (strtoupper($kunci) === $o);
                                    } elseif ($tipe === 'sec_pgk') {
                                        $kArr = array_map('trim', explode(',', strtoupper($kunci)));
                                        $isKunci = in_array($o, $kArr);
                                    }
                                ?>
                                    <div class="opsi-item <?= $isKunci ? 'is-kunci' : '' ?>">
                                        <span class="opsi-huruf"><?= $o ?>.</span>
                                        <span class="opsi-teks"><?= renderKontenTeks($s[$fName]) ?></span>
                                        <?php if ($isKunci): ?>
                                            <span class="badge-kunci-inline"><i class="fa-solid fa-check"></i> KUNCI</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($tipe === 'sec_jodoh'): ?>
                            <?php 
                            $kData = json_decode($s['konten_tambahan'] ?? '{}', true) ?: [];
                            $pasangan = $kData['pasangan'] ?? [];
                            $allRespon = array_values(array_unique(array_column($pasangan, 'respon')));
                            if (count($allRespon) > 1) {
                                // Acak urutan pilihan respon agar tidak segaris lurus
                                $seedC = ((int)($s['id'] ?? 1) * 31) + 17;
                                srand($seedC);
                                shuffle($allRespon);
                                srand(); // reset seed
                            }
                            ?>
                            <?php if (!empty($pasangan)): ?>
                                <table style="width: 100%; border-collapse: collapse; margin: 8px 0 12px 0; font-size: 9pt;">
                                    <thead>
                                        <tr style="background: #f1f5f9;">
                                            <th style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 48%; text-align: left;">Kolom Pernyataan</th>
                                            <th style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 48%; text-align: left;">Pilihan Jawaban (Acak)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $maxRow = max(count($pasangan), count($allRespon));
                                        for ($r = 0; $r < $maxRow; $r++):
                                            $pItem = $pasangan[$r] ?? null;
                                            $respItem = $allRespon[$r] ?? null;
                                        ?>
                                            <tr>
                                                <td style="border: 1px solid #e2e8f0; padding: 6px 10px; vertical-align: top;">
                                                    <?php if ($pItem): ?>
                                                        <b style="color: #1e293b;">[<?= $r + 1 ?>]</b> <?= renderKontenTeks($pItem['premis']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="border: 1px solid #e2e8f0; padding: 6px 10px; vertical-align: top;">
                                                    <?php if ($respItem): ?>
                                                        <b style="color: #0369a1;">[<?= chr(65 + $r) ?>]</b> <?= renderKontenTeks($respItem) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php elseif ($tipe === 'sec_bs'): ?>
                            <?php 
                            $kData = json_decode($s['konten_tambahan'] ?? '{}', true) ?: [];
                            $butir = $kData['butir'] ?? [];
                            ?>
                            <?php if (!empty($butir)): ?>
                                <table style="width: 100%; border-collapse: collapse; margin: 8px 0 12px 0; font-size: 9pt;">
                                    <thead>
                                        <tr style="background: #f1f5f9;">
                                            <th style="border: 1px solid #cbd5e1; padding: 6px 10px; text-align: left;">Pernyataan</th>
                                            <th style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 70px; text-align: center;">Benar</th>
                                            <th style="border: 1px solid #cbd5e1; padding: 6px 10px; width: 70px; text-align: center;">Salah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($butir as $bIdx => $b): ?>
                                            <tr>
                                                <td style="border: 1px solid #e2e8f0; padding: 6px 10px;"><?= ($bIdx + 1) ?>. <?= renderKontenTeks($b['pernyataan'] ?? '') ?></td>
                                                <td style="border: 1px solid #e2e8f0; text-align: center; color: #64748b;">[ &nbsp; ]</td>
                                                <td style="border: 1px solid #e2e8f0; text-align: center; color: #64748b;">[ &nbsp; ]</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Box Kunci Jawaban Lengkap -->
                        <div class="kunci-box">
                            <strong>KUNCI JAWABAN:</strong> 
                            <span class="kunci-val"><?= htmlspecialchars($kunci ?: '-') ?></span>
                            <span style="color: #64748b; font-size: 8pt;">• Tipe: <?= strtoupper(str_replace('sec_', '', $tipe)) ?></span>
                            <span style="color: #64748b; font-size: 8pt;">• Bobot Skor: <?= (float)($s['skor_maksimal'] ?? 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- MATRIKS KUNCI JAWABAN LENGKAP (LEMBAR REKAP KUNCI DI AKHIR) -->
        <?php if (!empty($soals)): ?>
            <div class="matriks-section" id="matriksKunciSection">
                <div class="matriks-title">LEMBAR REKAPITULASI KUNCI JAWABAN (MATRIKS)</div>
                <table class="matriks-table">
                    <tbody>
                        <?php 
                        $totalSoal = count($soals);
                        $colsPerRow = 10;
                        for ($i = 0; $i < $totalSoal; $i += $colsPerRow): 
                        ?>
                            <!-- Header Baris Nomor -->
                            <tr>
                                <?php for ($j = $i; $j < $i + $colsPerRow; $j++): ?>
                                    <th><?= ($j < $totalSoal) ? ($j + 1) : '-' ?></th>
                                <?php endfor; ?>
                            </tr>
                            <!-- Baris Jawaban Kunci -->
                            <tr>
                                <?php for ($j = $i; $j < $i + $colsPerRow; $j++): ?>
                                    <td>
                                        <?php if ($j < $totalSoal): ?>
                                            <span class="matriks-key-val"><?= htmlspecialchars($soals[$j]['kunci_jawaban'] ?: '-') ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- TANDA TANGAN PENGESAHAN -->
        <div class="ttd-wrapper">
            <div class="ttd-box">
                <div>Mengetahui,</div>
                <div>Kepala Madrasah</div>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($inst['nama_kepala']) ?></div>
                <div>NIP. <?= htmlspecialchars($inst['nip_kepala']) ?></div>
            </div>

            <div class="ttd-box">
                <div><?= htmlspecialchars($inst['kota']) ?>, <?= $tglFormatIndo ?></div>
                <div>Guru Mata Pelajaran</div>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($guruNama) ?></div>
                <div>NIP. -</div>
            </div>
        </div>

    </div>

    <script>
        function toggleKunciDisplay() {
            var chk = document.getElementById('checkTampilKunci');
            var content = document.getElementById('printContent');
            if (chk.checked) {
                content.classList.remove('hide-kunci');
            } else {
                content.classList.add('hide-kunci');
            }
        }

        function toggleMatriksDisplay() {
            var chk = document.getElementById('checkTampilMatriks');
            var matriks = document.getElementById('matriksKunciSection');
            if (matriks) {
                if (chk.checked) {
                    matriks.classList.remove('hide-matriks');
                } else {
                    matriks.classList.add('hide-matriks');
                }
            }
        }
    </script>
</body>
</html>
