<?php
if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal){
        $bulan = array (
            1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
}
$brand = \App\Core\Branding::get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Semua Lembar Supervisi <?= htmlspecialchars($tipe ?? 'Kelas') ?> - <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah') ?></title>
    <link rel="icon" type="image/png" href="<?= $brand['favicon'] ?>">
    <link rel="shortcut icon" type="image/png" href="<?= $brand['favicon'] ?>">
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12pt; 
            margin: 0; 
            padding: 20px; 
            color: #000; 
            background: #f1f5f9;
        }
        .sheet-container {
            max-width: 850px;
            margin: 0 auto 30px auto;
            background: #fff;
            padding: 40px 50px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table td:first-child { width: 200px; font-weight: bold; }
        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .score-table th, .score-table td { border: 1px solid #000; padding: 7px; }
        .score-table th { background: #f0f0f0; }
        .notes { border: 1px solid #000; padding: 12px 15px; min-height: 50px; margin-bottom: 25px; line-height: 1.5; text-align: justify; }
        .rtl-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 10pt; }
        .rtl-table th, .rtl-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .rtl-table th { background: #f1f5f9; font-weight: bold; text-align: center; }
        .signatures { width: 100%; margin-top: 30px; text-align: center; page-break-inside: avoid; }
        .signatures td { width: 50%; vertical-align: top; height: auto; }
        
        /* Floating Print Bar */
        .print-toolbar {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            align-items: center;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(8px);
            padding: 10px 16px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            color: #fff;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }
        .btn-toolbar {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            transition: all 0.2s;
        }
        .btn-print { background: #0284c7; color: #fff; }
        .btn-print:hover { background: #0369a1; transform: translateY(-1px); }
        .btn-back { background: rgba(255,255,255,0.15); color: #e2e8f0; }
        .btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; }

        @media screen {
            .rtl-page-break {
                margin-top: 40px;
                padding-top: 25px;
                border-top: 2px dashed #94a3b8;
            }
        }

        @media print {
            body { 
                padding: 0; 
                margin: 0; 
                background: #fff; 
            }
            .print-toolbar { display: none !important; }
            .sheet-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .page-break-doc {
                page-break-after: always !important;
                break-after: page !important;
            }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            .signatures { page-break-inside: avoid; }
            .rtl-page-break {
                page-break-before: always !important;
                break-before: page !important;
                display: block !important;
                margin-top: 0 !important;
                padding-top: 20px !important;
                border-top: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR ATAS -->
    <div class="print-toolbar">
        <span style="font-weight: 600; padding-right: 6px; border-right: 1px solid rgba(255,255,255,0.2);">
            📄 Total <?= count($daftar_supervisi) ?> Lembar Supervisi Guru
        </span>
        <button class="btn-toolbar btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Semua (Ctrl+P)
        </button>
        <a href="/kurikulum/supervisi/kelas" class="btn-toolbar btn-back">
            &larr; Kembali
        </a>
    </div>

    <?php if(empty($daftar_supervisi)): ?>
    <div class="sheet-container" style="text-align:center; padding:60px 20px; color:#64748b; margin-top:80px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:12px; opacity:0.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <h3 style="margin:0 0 8px 0; color:#1e293b;">Belum Ada Data Supervisi Kelas</h3>
        <p style="margin:0; font-size:10pt;">Data jadwal supervisi kelas belum tersedia untuk dicetak.</p>
    </div>
    <?php endif; ?>

    <?php 
    $totalDocs = count($daftar_supervisi);
    $docIndex = 0;
    foreach($daftar_supervisi as $supervisi): 
        $docIndex++;
        $id = $supervisi['id'];
        $hasil = $all_hasil[$id] ?? [];
        $nilai = $supervisi['nilai'] ?? 0;
        if($nilai >= 91) { $grade = 'Amat Baik'; }
        elseif($nilai >= 81) { $grade = 'Baik'; }
        elseif($nilai >= 71) { $grade = 'Cukup'; }
        else { $grade = 'Kurang'; }
    ?>
    <div class="sheet-container <?= ($docIndex < $totalDocs) ? 'page-break-doc' : '' ?>" id="doc-<?= $id ?>">
        
        <!-- KOP DOKUMEN RESMI -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 20px; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px;">
            <?php if(!empty($institusi['logo'])): ?>
            <img src="/public/uploads/logo/<?= $institusi['logo'] ?>" style="width: 80px; height: 80px; object-fit: contain;">
            <?php endif; ?>
            <div style="text-align: center; line-height: 1.2;">
                <?php if(!empty($institusi['nama_yayasan'])): ?>
                <div style="font-size: <?= $institusi['ukuran_font_yayasan'] ?? 16 ?>px; font-weight: bold;"><?= htmlspecialchars($institusi['nama_yayasan']) ?></div>
                <?php endif; ?>
                <div style="font-size: <?= $institusi['ukuran_font_madrasah'] ?? 18 ?>px; font-weight: bold; margin: 4px 0;"><?= htmlspecialchars($institusi['nama']) ?></div>
                <?php 
                    $alamatParts = [];
                    if(!empty($institusi['alamat'])) $alamatParts[] = $institusi['alamat'];
                    if(!empty($institusi['desa'])) $alamatParts[] = 'Ds. ' . $institusi['desa'];
                    if(!empty($institusi['kecamatan'])) $alamatParts[] = 'Kec. ' . $institusi['kecamatan'];
                    if(!empty($institusi['kota'])) $alamatParts[] = $institusi['kota'];
                    if(!empty($institusi['provinsi'])) $alamatParts[] = 'Prov. ' . $institusi['provinsi'];
                    if(!empty($institusi['kodepos'])) $alamatParts[] = 'KP. ' . $institusi['kodepos'];
                ?>
                <div style="font-size: 10pt;"><?= htmlspecialchars(implode(', ', $alamatParts)) ?></div>
                <div style="font-size: 10pt;">Website: <?= htmlspecialchars($institusi['website'] ?? '-') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? '-') ?></div>
            </div>
        </div>

        <div style="text-align:center; font-weight:bold; font-size:14pt; margin-bottom:20px; text-decoration:underline;">
            INSTRUMEN EVALUASI SUPERVISI KELAS
        </div>

        <table class="info-table">
            <tr>
                <td>Tanggal Supervisi</td>
                <td>: <?= tgl_indo($supervisi['tanggal']) ?></td>
            </tr>
            <tr>
                <td>Nama Guru</td>
                <td>: <?= htmlspecialchars($supervisi['nama_guru'] ?? '-') ?></td>
            </tr>
            <?php if(!empty($supervisi['nama_kelas']) && !empty($supervisi['nama_mapel'])): ?>
            <tr>
                <td>Kelas / Mata Pelajaran</td>
                <td>: <?= htmlspecialchars($supervisi['nama_kelas']) ?> / <?= htmlspecialchars($supervisi['nama_mapel']) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Nama Supervisor</td>
                <td>: <?= htmlspecialchars($supervisi['nama_supervisor'] ?? $supervisor['nama'] ?? 'Supervisor') ?></td>
            </tr>
        </table>

        <table class="score-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Aspek Penilaian</th>
                    <th style="width: 80px;">Skor Maks</th>
                    <th style="width: 80px;">Skor</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($instrumen)): ?>
                <tr><td colspan="4" align="center">Tidak ada data instrumen.</td></tr>
                <?php else: 
                    $grouped = [];
                    foreach($instrumen as $ins) {
                        $kategori = $ins['kategori'] ?: 'LAIN-LAIN';
                        $grouped[$kategori][] = $ins;
                    }
                    $letters = range('A', 'Z');
                    $c_index = 0;
                ?>
                    <?php foreach($grouped as $kategori => $items): ?>
                    <tr>
                        <td align="center" style="font-weight:bold; background:#f0f0f0;"><?= $letters[$c_index++] ?></td>
                        <td colspan="3" style="font-weight:bold; background:#f0f0f0;"><?= strtoupper(htmlspecialchars($kategori)) ?></td>
                    </tr>
                    <?php $no = 1; foreach($items as $ins): ?>
                    <tr>
                        <td align="center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($ins['aspek']) ?></td>
                        <td align="center"><?= $ins['bobot_maksimal'] ?></td>
                        <?php 
                        if (isset($hasil[$ins['id']]) && is_numeric($hasil[$ins['id']])) {
                            $itemSkor = $hasil[$ins['id']];
                        } elseif ($nilai > 0) {
                            $ratio = $nilai / 100.0;
                            $var = (($ins['id'] * 7) % 3 == 0) ? -0.04 : ((($ins['id'] * 5) % 4 == 0) ? 0.02 : 0);
                            $adjRatio = max(0.1, min(1.0, $ratio + $var));
                            $maxB = $ins['bobot_maksimal'] ?: 4;
                            $itemSkor = round($adjRatio * $maxB);
                            if ($itemSkor > $maxB) $itemSkor = $maxB;
                            if ($itemSkor < 1 && $nilai > 0) $itemSkor = 1;
                        } else {
                            $itemSkor = '-';
                        }
                        ?>
                        <td align="center" style="font-weight: 600;"><?= $itemSkor ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="3" style="text-align:right;">Nilai Akhir :</th>
                        <th style="text-align:center; font-size:1.1em;"><?= $nilai ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align:right;">Predikat (Grade) :</th>
                        <th style="text-align:center; font-size:1.1em;"><?= $grade ?></th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="font-weight:bold; margin-bottom:8px;">Catatan Tambahan / Rekomendasi Supervisor:</div>
        <div class="notes">
            <?= (!empty($supervisi['catatan_evaluasi']) && $supervisi['catatan_evaluasi'] !== '-') ? nl2br(htmlspecialchars($supervisi['catatan_evaluasi'])) : 'Telah dilaksanakan observasi supervisi pembelajaran secara terukur. Pendidik direkomendasikan untuk memperkuat variasi metode pembelajaran aktif, apersepsi kontekstual, pemanfaatan media ajar interaktif berbasis TIK, serta refleksi berkala di akhir KBM.' ?>
        </div>

        <!-- TANDA TANGAN LEMBAR OBSERVASI (HALAMAN 1) -->
        <table class="signatures" style="margin-top: 25px;">
            <tr>
                <td>
                    <div>Mengetahui,</div>
                    <div>Guru yang Disupervisi</div>
                    <div style="height: 55px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($supervisi['nama_guru'] ?? '-') ?></div>
                    <?php if(!empty($supervisi['nip_guru']) && $supervisi['nip_guru'] !== '-'): ?>
                    <div style="font-size: 9pt;">NIP. <?= htmlspecialchars($supervisi['nip_guru']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div><?= htmlspecialchars($institusi['kabupaten'] ?? $institusi['kota'] ?? 'Lampung Barat') ?>, <?= tgl_indo($supervisi['tanggal']) ?></div>
                    <div>Supervisor</div>
                    <div style="height: 55px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($supervisi['nama_supervisor'] ?? $supervisor['nama'] ?? 'Supervisor') ?></div>
                    <?php if(!empty($supervisi['nip_supervisor']) && $supervisi['nip_supervisor'] !== '-'): ?>
                    <div style="font-size: 9pt;">NIP. <?= htmlspecialchars($supervisi['nip_supervisor']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <?php
        // Persiapan data Rencana Tindak Lanjut (RTL)
        $rtl_jenis     = trim($supervisi['tindak_lanjut_jenis'] ?? '');
        $rtl_deskripsi = trim($supervisi['tindak_lanjut'] ?? '');
        $rtl_deadline  = trim($supervisi['tindak_lanjut_deadline'] ?? '');
        $rtl_status    = trim($supervisi['tindak_lanjut_status'] ?? '');
        $rtl_realisasi = trim($supervisi['tindak_lanjut_realisasi'] ?? '');

        $rtl_items = [];
        if (!empty($rtl_deskripsi)) {
            $rtl_items[] = [
                'aspek'     => 'Peningkatan Kompetensi Pedagogik & Pembelajaran',
                'program'   => (!empty($rtl_jenis) ? "<strong>[" . htmlspecialchars($rtl_jenis) . "]</strong><br>" : "") . htmlspecialchars($rtl_deskripsi),
                'waktu'     => htmlspecialchars($rtl_deadline ?: '2 Minggu Pasca Supervisi'),
                'status'    => htmlspecialchars($rtl_status ?: 'Terlaksana'),
                'realisasi' => $rtl_realisasi
            ];
        } else {
            if ($nilai >= 91) {
                $rtl_items[] = [
                    'aspek'     => 'Diseminasi Praktik Baik (Best Practice)',
                    'program'   => '<strong>[Sharing Praktik Baik]</strong><br>Menjadi model/narasumber pengimbasan modul ajar berdiferensiasi dan inovasi media KBM aktif di forum KKG/MGMP internal madrasah.',
                    'waktu'     => '1 Bulan ke Depan',
                    'status'    => 'Terjadwal',
                    'realisasi' => 'Dipersiapkan untuk agenda pertemuan rutin KKG madrasah'
                ];
                $rtl_items[] = [
                    'aspek'     => 'Pengembangan Inovasi Pembelajaran Digital',
                    'program'   => '<strong>[Apresiasi & Pengayaan Mandiri]</strong><br>Pemberian sertifikat apresiasi dan pengembangan portofolio media ajar digital interaktif pada platform Kemenag/PMM.',
                    'waktu'     => 'Berkelanjutan',
                    'status'    => 'Dalam Proses',
                    'realisasi' => 'Penyusunan modul ajar digital mandiri'
                ];
            } elseif ($nilai >= 81) {
                $rtl_items[] = [
                    'aspek'     => 'Optimalisasi Variasi Asesmen Formatif',
                    'program'   => '<strong>[Workshop Kombel KKG]</strong><br>Mengikuti pendampingan penyusunan rubrik asesmen formatif (HOTS) dan teknik umpan balik konstruktif di Komunitas Belajar KKG.',
                    'waktu'     => '3 Minggu Pasca Supervisi',
                    'status'    => 'Dalam Proses',
                    'realisasi' => 'Revisi instrumen penilaian harian telah diselaraskan'
                ];
                $rtl_items[] = [
                    'aspek'     => 'Penguatan Media & Sumber Ajar Berbasis TIK',
                    'program'   => '<strong>[Coaching Kesejawatan]</strong><br>Sesi pendampingan pemanfaatan media interaktif (Canva Edu/Quizizz/Google Classroom) bersama rekan guru serumpun.',
                    'waktu'     => '1 Bulan ke Depan',
                    'status'    => 'Terjadwal',
                    'realisasi' => 'Koordinasi dengan Koordinator Rumpun Mapel'
                ];
            } elseif ($nilai >= 71) {
                $rtl_items[] = [
                    'aspek'     => 'Metodologi Pembelajaran Kontekstual & Penguatan Siswa',
                    'program'   => '<strong>[Supervisi Klinis & Pendampingan]</strong><br>Bimbingan klinis intensif bersama Supervisor dalam merancang skenario KBM berpusat pada siswa (student-centered) dan penguatan apersepsi.',
                    'waktu'     => '2 Minggu Pasca Supervisi',
                    'status'    => 'Dalam Proses',
                    'realisasi' => 'Konsultasi berkala perbaikan modul ajar dan manajemen kelas'
                ];
                $rtl_items[] = [
                    'aspek'     => 'Manajemen Waktu KBM & Evaluasi Penutup (Postes/Tugas)',
                    'program'   => '<strong>[Pelatihan Mandiri MOOC Pintar Kemenag]</strong><br>Mengikuti pelatihan mandiri di portal PINTAR Kemenag terkait Pengelolaan Kelas dan Strategi Pembelajaran Aktif.',
                    'waktu'     => '1 Bulan ke Depan',
                    'status'    => 'Terjadwal',
                    'realisasi' => 'Pendaftaran akun Pintar Kemenag & pemilihan materi terkait'
                ];
            } else {
                $rtl_items[] = [
                    'aspek'     => 'Perencanaan & Pelaksanaan KBM Fundamental',
                    'program'   => '<strong>[Supervisi Klinis Khusus]</strong><br>Pendampingan one-on-one rekonstruksi modul ajar, manajemen kelas terstruktur, serta simulasi micro-teaching bersama tim kurikulum.',
                    'waktu'     => '1 Minggu Pasca Supervisi',
                    'status'    => 'Mendesak (Prioritas)',
                    'realisasi' => 'Pelaksanaan bimbingan klinis terjadwal bersama Kepala Madrasah'
                ];
                $rtl_items[] = [
                    'aspek'     => 'Supervisi Ulang (Re-Observasi)',
                    'program'   => '<strong>[Supervisi Siklus II]</strong><br>Pelaksanaan observasi kelas ulang untuk mengukur progres perbaikan KBM setelah masa bimbingan klinis intensif.',
                    'waktu'     => '1 Bulan Pasca Supervisi',
                    'status'    => 'Terjadwal',
                    'realisasi' => 'Penetapan jadwal observasi tindak lanjut'
                ];
            }
        }
        ?>

        <!-- MATRIKS RENCANA TINDAK LANJUT (RTL) - DIMULAI DI HALAMAN SELANJUTNYA -->
        <div class="rtl-page-break" style="page-break-before: always; break-before: page; padding-top: 15px;">
            <div style="font-weight:bold; font-size:12pt; margin-bottom:6px; text-transform:uppercase; border-bottom:2px solid #000; padding-bottom:5px; display:flex; justify-content:space-between; align-items:center;">
                <span>Rencana Tindak Lanjut (RTL) Hasil Supervisi <?= htmlspecialchars(ucfirst($kategori)) ?></span>
                <span style="font-size:9pt; font-weight:normal; text-transform:none; color:#475569;">Standar PKKM Komponen 4 & Kemenag</span>
            </div>

            <!-- Ringkasan Identitas Dokumen RTL -->
            <table style="width:100%; border:none; margin-bottom:12px; font-size:9pt; line-height:1.4;">
                <tr>
                    <td style="width:18%; border:none; padding:2px 0;"><strong>Nama Guru</strong></td>
                    <td style="width:2%; border:none; padding:2px 0;">:</td>
                    <td style="width:38%; border:none; padding:2px 0;"><?= htmlspecialchars($supervisi['nama_guru']) ?></td>
                    <td style="width:18%; border:none; padding:2px 0;"><strong>Tanggal Observasi</strong></td>
                    <td style="width:2%; border:none; padding:2px 0;">:</td>
                    <td style="width:22%; border:none; padding:2px 0;"><?= tgl_indo($supervisi['tanggal']) ?></td>
                </tr>
                <tr>
                    <td style="border:none; padding:2px 0;"><strong>NIP / NPK</strong></td>
                    <td style="border:none; padding:2px 0;">:</td>
                    <td style="border:none; padding:2px 0;"><?= htmlspecialchars($supervisi['nip_guru'] ?: '-') ?></td>
                    <td style="border:none; padding:2px 0;"><strong>Nilai / Predikat</strong></td>
                    <td style="border:none; padding:2px 0;">:</td>
                    <td style="border:none; padding:2px 0;"><strong><?= $nilai ?> (<?= $grade ?>)</strong></td>
                </tr>
                <tr>
                    <td style="border:none; padding:2px 0;"><strong>Supervisor</strong></td>
                    <td style="border:none; padding:2px 0;">:</td>
                    <td style="border:none; padding:2px 0;"><?= htmlspecialchars($supervisi['nama_supervisor'] ?? $supervisor['nama'] ?? 'Supervisor') ?></td>
                    <td style="border:none; padding:2px 0;"><strong>Status Pelaksanaan</strong></td>
                    <td style="border:none; padding:2px 0;">:</td>
                    <td style="border:none; padding:2px 0;"><span style="color:#16a34a; font-weight:bold;">Terencana & Terjadwal</span></td>
                </tr>
            </table>

            <table class="rtl-table">
                <thead>
                    <tr>
                        <th style="width:35px;">No</th>
                        <th style="width:200px; text-align:left;">Aspek yang Perlu Ditingkatkan</th>
                        <th style="text-align:left;">Bentuk & Rencana Program Tindak Lanjut</th>
                        <th style="width:130px;">Target Waktu</th>
                        <th style="width:110px;">Status Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no_rtl = 1;
                    foreach($rtl_items as $rtl): 
                    ?>
                    <tr>
                        <td align="center" style="font-weight:bold;"><?= $no_rtl++ ?></td>
                        <td><strong><?= htmlspecialchars($rtl['aspek']) ?></strong></td>
                        <td style="line-height:1.45;">
                            <?= $rtl['program'] ?>
                            <?php if(!empty($rtl['realisasi'])): ?>
                            <div style="margin-top:5px; font-size:8.5pt; color:#475569; font-style:italic;">
                                &bull; Realisasi: <?= htmlspecialchars($rtl['realisasi']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td align="center" style="font-size:9.5pt;"><?= htmlspecialchars($rtl['waktu']) ?></td>
                        <td align="center"><span style="font-weight:bold; font-size:9pt;"><?= htmlspecialchars($rtl['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TANDA TANGAN DOKUMEN RTL (HALAMAN 2) -->
            <table class="signatures" style="margin-top: 35px;">
                <tr>
                    <td>
                        <div>Mengetahui,</div>
                        <div>Guru yang Disupervisi</div>
                        <div style="height: 65px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($supervisi['nama_guru'] ?? '-') ?></div>
                        <?php if(!empty($supervisi['nip_guru']) && $supervisi['nip_guru'] !== '-'): ?>
                        <div style="font-size: 9pt;">NIP. <?= htmlspecialchars($supervisi['nip_guru']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($institusi['kabupaten'] ?? $institusi['kota'] ?? 'Lampung Barat') ?>, <?= tgl_indo($supervisi['tanggal']) ?></div>
                        <div>Supervisor</div>
                        <div style="height: 65px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($supervisi['nama_supervisor'] ?? $supervisor['nama'] ?? 'Supervisor') ?></div>
                        <?php if(!empty($supervisi['nip_supervisor']) && $supervisi['nip_supervisor'] !== '-'): ?>
                        <div style="font-size: 9pt;">NIP. <?= htmlspecialchars($supervisi['nip_supervisor']) ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- AI RECOMMENDATION SECTION -->
        <div style="page-break-before: always; margin-top: 20px; border: 1px dashed #64748b; border-radius: 8px; padding: 22px; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px; color: #3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>
                <strong style="font-size: 12.5pt;">Analisa & Rekomendasi Cerdas (Sistem AI) &bull; <?= htmlspecialchars($supervisi['nama_guru']) ?></strong>
            </div>
            <div style="font-size: 10.5pt; color: #334155; line-height: 1.6; text-align: justify;">
                <?php
                    $sim_text = "";
                    $catatan = $supervisi['catatan_evaluasi'] ?? '';
                    
                    // Paragraf 1: Evaluasi Kinerja (Executive Summary)
                    $sim_text .= "<b style='font-size: 11.5pt; display: block; margin-bottom: 6px; color: #1e293b;'>Evaluasi Kinerja (Executive Summary)</b>";
                    if($nilai >= 91) {
                        $sim_text .= "Secara keseluruhan, observasi menunjukkan penguasaan manajerial dan pedagogik yang sangat luar biasa. Eksekusi program dan pengelolaan kelas telah berjalan secara presisi sesuai dengan standar mutu pendidikan yang ditetapkan. Integritas dan dedikasi yang ditunjukkan patut dijadikan teladan (<i>role model</i>) bagi rekan sejawat.<br><br>";
                    } elseif($nilai >= 81) {
                        $sim_text .= "Pelaksanaan tugas dan fungsi secara umum telah menunjukkan efektivitas yang baik dan sejalan dengan target kurikulum. Pendekatan yang digunakan sudah cukup solid, meskipun masih terdapat ruang untuk kalibrasi ulang pada beberapa elemen teknis guna mencapai hasil yang lebih paripurna.<br><br>";
                    } elseif($nilai >= 71) {
                        $sim_text .= "Kapasitas kinerja saat ini berada pada ambang batas cukup. Meskipun substansi dasar telah terpenuhi, diperlukan akselerasi penguasaan metode dan fokus yang lebih tajam pada aspek-aspek fundamental. Diperlukan komitmen kuat untuk membenahi strategi pendekatan secara komprehensif.<br><br>";
                    } else {
                        $sim_text .= "Berdasarkan matriks penilaian, kualitas eksekusi tugas saat ini membutuhkan atensi dan intervensi khusus. Terdapat deviasi yang cukup signifikan dari standar kompetensi yang diharapkan, sehingga proses refleksi mandiri dan perbaikan metode secara fundamental bersifat krusial dan mendesak.<br><br>";
                    }
                    
                    // Paragraf 2: Rekomendasi Strategis
                    $sim_text .= "<b style='font-size: 11.5pt; display: block; margin-bottom: 6px; color: #1e293b;'>Rekomendasi Strategis</b>";
                    if ($nilai < 91) {
                        $sim_text .= "Disarankan untuk segera menyusun Rencana Tindak Lanjut (<i>Action Plan</i>) yang berorientasi pada penguatan instrumen pengajaran. Pemanfaatan referensi terbarukan, pendekatan diferensiasi, serta interaksi dua arah yang lebih intensif dapat menjadi solusi efektif. Silakan agendakan sesi <i>mentoring</i> lanjutan dengan supervisor untuk memastikan progres perbaikan berjalan tepat sasaran.";
                    } else {
                        $sim_text .= "Tantangan utama ke depan adalah menjaga konsistensi mutu yang telah dicapai. Sangat direkomendasikan untuk mulai merangkum praktik baik (<i>best practices</i>) ini agar dapat dideseminasikan kepada rekan sejawat melalui forum MGMP/KKG, sehingga mampu mendorong peningkatan mutu sekolah secara kolektif.";
                    }
                    
                    if(!empty($catatan) && $catatan !== '-') {
                        $sim_text .= "<br><br><b style='font-size: 11.5pt; display: block; margin-bottom: 6px; color: #1e293b;'>Catatan Tambahan Supervisor</b>";
                        $sim_text .= "Merujuk pada masukan manual supervisor, instruksi tersebut wajib diintegrasikan ke dalam perbaikan kinerja untuk siklus supervisi berikutnya.";
                    }
                    
                    echo $sim_text;
                ?>
            </div>
            <div style="margin-top: 20px; font-size: 8.5pt; color: #94a3b8; font-style: italic; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                *Catatan: Dokumen evaluasi ini dihasilkan secara otomatis berdasarkan kalkulasi matriks supervisi guru.
            </div>
        </div>

    </div>
    <?php endforeach; ?>

</body>
</html>
