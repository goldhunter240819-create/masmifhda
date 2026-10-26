<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Program Wali Kelas - <?php echo htmlspecialchars($kelas['nama_kelas']); ?></title>
    <link rel="icon" href="<?php echo htmlspecialchars(\App\Core\Branding::getInstitusiFavicon()); ?>" type="image/x-icon">
    <style>
        /* CSS Print A4 Setup */
        @page { size: A4; margin: 2cm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 100%;
            height: 100vh;
            page-break-after: always;
            box-sizing: border-box;
            position: relative;
        }
        .page:last-child {
            page-break-after: auto;
        }
        h1, h2, h3 { text-align: center; font-weight: bold; margin: 0; }
        h1 { font-size: 16pt; margin-bottom: 20px; }
        h2 { font-size: 14pt; margin-bottom: 15px; }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .cover-title {
            margin-top: 150px;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 1.5;
        }
        .cover-logo {
            display: block;
            margin: 80px auto;
            width: 200px;
            height: auto;
        }
        .cover-bottom {
            position: absolute;
            bottom: 100px;
            width: 100%;
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
        }
        
        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.tabel-data th, table.tabel-data td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.tabel-data th {
            text-align: center;
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .no-print { display: none; }
        @media screen {
            body { background: #e2e8f0; padding: 40px 0; }
            .page {
                background: white;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 20px auto;
                padding: 2cm;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                height: auto;
            }
            .no-print {
                display: block;
                position: fixed;
                top: 20px;
                right: 20px;
                background: #1e293b;
                color: #fff;
                padding: 15px 25px;
                border-radius: 8px;
                z-index: 1000;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            }
            .btn-print {
                background: #3b82f6; color: white; border: none;
                padding: 10px 20px; font-size: 16px; border-radius: 5px;
                cursor: pointer; display: block; width: 100%; margin-top: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <h3 style="margin-bottom:10px; font-size:16px;">Preview Program Kerja</h3>
        <p style="font-size:13px; margin-top:0; color:#cbd5e1;">Pastikan pengaturan Margins: Default <br>dan centang Background Graphics.</p>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Save PDF</button>
    </div>

    <!-- COVER -->
    <div class="page">
        <div class="cover-title">
            PROGRAM KERJA<br>WALI KELAS
        </div>
        <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" class="cover-logo" alt="Logo">
        <div class="cover-bottom">
            <?php echo strtoupper($institusi['nama_institusi'] ?? 'MI MIFTAHUL HUDA'); ?><br>
            TAHUN PELAJARAN <?php echo htmlspecialchars($tahun_ajaran['name']); ?>
        </div>
    </div>

    <!-- KATA PENGANTAR -->
    <div class="page">
        <h2>KATA PENGANTAR</h2>
        <p class="text-justify">Segala puji bagi Allah SWT dengan sebenar-benarnya pujian, selanjutnya salam dan sejahtera atas junjungan kita Nabi Muhammad SAW, beserta keluarganya, sahabatnya, pengikutnya hingga akhir zaman.</p>
        <p class="text-justify">Rasa syukur yang tiada terhingga karena atas limpahan rahmat, taufiq dan hidayah-Nya, saya dapat menyusun dan menyelesaikan program kerja Wali Kelas <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'MI Miftahul Huda'); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran['name']); ?> ini sebagai mana yang diharapkan.</p>
        <p class="text-justify">Program Kerja Wali kelas ini disusun sebagai pedoman atau pegangan wali kelas untuk mengelola kelas secara baik dan terarah guna meningkatkan dan menunjang keberhasilan siswa dalam menempuh pendidikan di madrasah. Wali kelas sebagai orang tua siswa di madrasah yang diberi tugas untuk mengawasi, membimbing, memotivasi dan mengarahkan dan menyelesaikan masalah siswa agar selalu memiliki tanggung jawab untuk belajar, dengan adanya program kerja ini diharapkan kelas menjadi tempat yang nyaman, dan kondusif untuk berlangsungnya proses pembelajaran.</p>
        <p class="text-justify">Saya menyadari, bahwa program kerja ini masih sangat jauh dari sempurna karena keterbatasan kemampuan dan pengalaman yang saya miliki. Oleh karena itu, besar harapan saya agar diberikan saran, kritik dan masukan apapun dari semua pihak demi kesempurnaan program kerja ini pada tahun-tahun yang akan datang.</p>
        <p class="text-justify">Akhirul kalam hanya kehadirat Allah SWT semata tempat bergantung dan menyerahkan diri, semoga program kerja ini dapat berjalan lancar sehingga dapat bermanfaat untuk kemajuan pendidikan khususnya <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'Madrasah ibtidaiyah miftahul huda'); ?>.</p>
    </div>

    <!-- DAFTAR ISI -->
    <div class="page">
        <h2>DAFTAR ISI</h2>
        <table style="width: 100%; font-size: 12pt; line-height:2;">
            <tr><td>KATA PENGANTAR</td></tr>
            <tr><td>DAFTAR ISI</td></tr>
            <tr><td><strong>BAB I. PENDAHULUAN</strong></td></tr>
            <tr><td style="padding-left:20px;">A. DASAR</td></tr>
            <tr><td style="padding-left:20px;">B. MAKSUD DAN TUJUAN</td></tr>
            <tr><td><strong>BAB II. PENGORGANISASIAN KELAS</strong></td></tr>
            <tr><td style="padding-left:20px;">A. SUSUNAN ORGANISASI</td></tr>
            <tr><td style="padding-left:20px;">B. STRUKTUR ORGANISASI KELAS <?php echo strtoupper($kelas['tingkat']); ?></td></tr>
            <tr><td style="padding-left:20px;">C. RINCIAN TUGAS</td></tr>
            <tr><td><strong>BAB III. PROGRAM PENGELOLAAN KELAS</strong></td></tr>
            <tr><td style="padding-left:20px;">A. PROGRAM WALI KELAS</td></tr>
            <tr><td style="padding-left:20px;">B. KEGIATAN PEMBELAJARAN KELAS <?php echo strtoupper($kelas['tingkat']); ?></td></tr>
            <tr><td><strong>BAB IV. PENUTUP</strong></td></tr>
            <tr><td><strong>LAMPIRAN-LAMPIRAN :</strong></td></tr>
            <tr><td style="padding-left:20px;">1. Tugas Pokok Wali Kelas</td></tr>
            <tr><td style="padding-left:20px;">2. Jadwal Mata Pelajaran Kelas <?php echo strtoupper($kelas['tingkat']); ?></td></tr>
            <tr><td style="padding-left:20px;">3. Jadwal Piket Kelas</td></tr>
            <tr><td style="padding-left:20px;">4. Daftar nama siswa kelas <?php echo strtoupper($kelas['tingkat']); ?></td></tr>
        </table>
    </div>

    <!-- BAB I -->
    <div class="page">
        <h2>BAB I<br>PENDAHULUAN</h2>
        <p class="text-justify">Madrasah adalah satu lembaga tempat berlangsungnya usaha yang dilakukan secara sadar oleh manusia dewasa (guru) dalam upaya membentuk pertumbuhan jasmani dan rohani anak didik sebagai bangsa di masa yang akan datang. Dalam pelaksanaan kegiatan pendidikan sangat diperlukan suatu kondisi (iklim) yang sehat, kondusif, harmonis dan serasi antara siswa, guru, kepala madrasah, orang tua (wali) siswa dan seluruh aparat (stakeholder) pendidikan lainnya.</p>
        <p class="text-justify">Undang-Undang Republik Indonesia Nomor 20 tahun 2003 tentang Sistem Pendidikan Nasional (Sisdiknas) menetapkan bahwa tujuan Pendidikan Nasional adalah untuk berkembangnya potensi peserta didik agar menjadi manusia yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa dan berbudi pekerti luhur, berakhlak mulia, sehat, berilmu, cakap, kreatif, mandiri dan menjadi warga Negara yang demokratis serta bertanggung jawab.</p>
        <p class="text-justify">Untuk mewujudkan tujuan di atas maka peranan Kepala sekolah memiliki nilai yang sangat strategis, sebab secara langsung maupun tidak langsung, Wali Kelas sebagai perencana (planning) mempunyai tugas dan tanggung jawab yang sangat besar, khususnya dalam upaya peningkatan hasil belajar siswa. Tanggung jawab wali kelas yang diberi tugas untuk membimbing dan memotivasi siswa agar dapat belajar dengan hasil yang baik dan maksimal.</p>
        <p class="text-justify">Mengacu pada berbagai hal di atas perlu kiranya disusun Program Kerja Wali kelas di <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'MI Miftahul Huda'); ?>, sebagai acuan dari adanya rencana dan seluruh kegiatan yang berkaitan dengan kelas sehingga guru sebagai pengajar dapat meningkatkan kinerjanya secara optimal. Program ini disusun sebagai acuan pedoman kegiatan di kelas selama tahun pelajaran <?php echo htmlspecialchars($tahun_ajaran['name']); ?>.</p>

        <br>
        <p><strong>A. DASAR</strong></p>
        <ol>
            <li>Bahwa dalam memperlancar pelaksanaan proses belajar mengajar di <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'MI Miftahul Huda'); ?></li>
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang sistem Pendidikan Nasional</li>
            <li>Edaran Kalender Pendidikan Bagi Satuan Pendidikan Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran['name']); ?></li>
        </ol>

        <p><strong>B. MAKSUD DAN TUJUAN</strong></p>
        <p>Maksud dan tujuan dari disusunnya Program Kerja Wali Kelas tahun pelajaran <?php echo htmlspecialchars($tahun_ajaran['name']); ?> ini adalah sebagai:</p>
        <ol>
            <li>Sarana untuk merencanakan program penataan kelas <?php echo strtoupper($kelas['tingkat']); ?></li>
            <li>Alat untuk membina dan mendidik siswa kelas <?php echo strtoupper($kelas['tingkat']); ?></li>
            <li>Untuk mengevaluasi keadaan kelas <?php echo strtoupper($kelas['tingkat']); ?></li>
            <li>Sarana untuk menanamkan nilai-nilai keagamaan dan kebangsaan</li>
            <li>Bahan evaluasi tentang tupoksi wali kelas</li>
        </ol>
    </div>

    <!-- BAB II -->
    <div class="page">
        <h2>BAB II<br>PENGORGANISASIAN KELAS</h2>
        <p><strong>A. SUSUNAN ORGANISASI</strong></p>
        <p class="text-justify">Dalam usaha untuk mencapai tujuan yang direncanakan dalam Program Kerja Wali Kelas <?php echo strtoupper($kelas['tingkat']); ?> <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'MI Miftahul Huda'); ?> tahun pelajaran <?php echo htmlspecialchars($tahun_ajaran['name']); ?> wali kelas selaku pembina dan pembimbing bersama siswa membentuk kepengurusan organisasi kelas, yang terdiri atas kepengurusan dan struktur organisasi.</p>
        <p>Adapun pengurus organisasi sebagai berikut :</p>
        <table style="width: 100%; margin-left:20px; margin-bottom: 20px;">
            <tr><td style="width: 30%;">Kepala Madrasah</td><td>: <?php echo htmlspecialchars($kepsek['nama'] ?? ''); ?></td></tr>
            <tr><td>Wali Kelas</td><td>: <?php echo htmlspecialchars($guru['nama']); ?></td></tr>
            <tr><td>Ketua Kelas</td><td>: <?php echo htmlspecialchars($struktur['ketua']); ?></td></tr>
            <tr><td>Sekretaris</td><td>: <?php echo htmlspecialchars($struktur['sekretaris']); ?></td></tr>
            <tr><td>Bendahara</td><td>: <?php echo htmlspecialchars($struktur['bendahara']); ?></td></tr>
        </table>

        <p><strong>B. STRUKTUR ORGANISASI KELAS <?php echo strtoupper($kelas['tingkat']); ?></strong></p>
        <div style="text-align: center; margin: 30px 0;">
            <p>Kepala Madrasah<br><strong><?php echo htmlspecialchars($kepsek['nama'] ?? ''); ?></strong></p>
            <p>|</p>
            <p>Wali Kelas<br><strong><?php echo htmlspecialchars($guru['nama']); ?></strong></p>
            <p>|</p>
            <p>Ketua Kelas<br><strong><?php echo htmlspecialchars($struktur['ketua']); ?></strong></p>
            <p>|</p>
            <div style="display:flex; justify-content:center; gap:50px;">
                <p>Sekretaris<br><strong><?php echo htmlspecialchars($struktur['sekretaris']); ?></strong></p>
                <p>Bendahara<br><strong><?php echo htmlspecialchars($struktur['bendahara']); ?></strong></p>
            </div>
            <p>|</p>
            <p>Anggota<br><strong>Seluruh Siswa Kelas <?php echo strtoupper($kelas['tingkat']); ?></strong></p>
        </div>

        <p><strong>C. RINCIAN TUGAS</strong></p>
        <p class="text-justify">Untuk dapat melaksanakan tugas dalam kerja di kelas dengan baik, sehingga pelaksanaan program dapat tercapai maka dibutuhkan rincian tugas kerja sebagai pedoman.</p>
        <ol>
            <li><strong>Wali Kelas</strong>
                <ul style="list-style-type: lower-alpha;">
                    <li>Melaksanakan tanggung jawab wakil dari sekolah;</li>
                    <li>Mengelola kelas;</li>
                    <li>Menyelenggarakan administrasi kelas yang meliputi: denah tempat duduk siswa jadwal pelajaran, daftar piket kelas, jurnal kelas, dan tata tertib kelas;</li>
                    <li>Memantau kemajuan dan prestasi akademik siswa;</li>
                    <li>Mengetahui identitas dan kepribadian anak didik;</li>
                    <li>Memberikan motivasi, inovasi pada siswa kelas;</li>
                </ul>
            </li>
        </ol>
    </div>

    <!-- BAB III & IV -->
    <div class="page">
        <h2>BAB III<br>PROGRAM PENGELOLAAN KELAS</h2>
        <p><strong>A. PROGRAM WALI KELAS</strong></p>
        <p class="text-justify">Program Kerja Wali Kelas <?php echo strtoupper($kelas['tingkat']); ?> agar dapat berjalan dengan baik diperlukan perencanaan dan pelaksanaan pengelolaan secara baik dan professional. Maka diperlukan rencana kerja yang terorganisir.</p>
        
        <br>
        <h2>BAB IV<br>PENUTUP</h2>
        <p class="text-justify">Penyusunan program kerja wali kelas haruslah berdasarkan kondisi dan keadaan sesuai petunjuk pelaksanaan kurikulum, sehingga dapat membantu kegiatan dan proses dalam rangka meningkatkan mutu belajar. Ruangan kelas yang baik, bersih dan tertata rapi akan memungkinkan hasil belajar yang maksimal.</p>
        <p class="text-justify">Demikian uraian Program kerja wali kelas dapat kami paparkan. Pada akhirnya kami selaku wali kelas berharap semoga program kerja ini bermanfaat dan dapat dilaksanakan secara efektif.</p>
        <br><br>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    Mengetahui:<br>
                    Kepala <?php echo htmlspecialchars($institusi['nama_institusi'] ?? 'MI Miftahul Huda'); ?><br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepsek['nama'] ?? ''); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($kepsek['nip'] ?? '-'); ?>
                </td>
                <td style="width: 50%; text-align: center;">
                    <br>
                    Wali Kelas <?php echo htmlspecialchars($kelas['nama_kelas']); ?><br><br><br><br>
                    <strong><?php echo htmlspecialchars($guru['nama']); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($guru['nip'] ?? '-'); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- LAMPIRAN -->
    <div class="page">
        <h2 style="text-align: left;">LAMPIRAN I: TUGAS POKOK WALI KELAS</h2>
        <ol>
            <li>Mengelola kelas</li>
            <li>Mengetahui identitas dan kepribadian anak didik</li>
            <li>Mengetahui tingkat kemampuan, status sosial/ekonomi anak didik</li>
            <li>Merekapitulasi kehadiran siswa</li>
            <li>Pengisian daftar kumpulan nilai siswa (legger)</li>
            <li>Pembuatan catatan khusus tentang siswa</li>
            <li>Pencatatan mutasi siswa</li>
            <li>Pengisian buku laporan hasil belajar siswa</li>
            <li>Pembagian buku laporan hasil belajar siswa</li>
        </ol>

        <h2 style="text-align: left; margin-top:50px;">LAMPIRAN II: JADWAL MATA PELAJARAN KELAS <?php echo strtoupper($kelas['tingkat']); ?></h2>
        <table class="tabel-data">
            <tr>
                <th style="width: 15%;">Hari</th>
                <th style="width: 20%;">Jam</th>
                <th style="width: 35%;">Mata Pelajaran</th>
                <th style="width: 30%;">Guru Pengampu</th>
            </tr>
            <?php 
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            foreach($days as $day): 
                if(!empty($jadwal_mapel[$day])):
                    $rowspan = count($jadwal_mapel[$day]);
                    $first = true;
                    foreach($jadwal_mapel[$day] as $j):
            ?>
            <tr>
                <?php if($first): ?>
                <td rowspan="<?php echo $rowspan; ?>" style="text-align: center; vertical-align: middle;"><strong><?php echo $day; ?></strong></td>
                <?php $first = false; endif; ?>
                <td style="text-align: center;"><?php echo substr($j['jam_mulai'],0,5); ?> - <?php echo substr($j['jam_selesai'],0,5); ?></td>
                <td><?php echo htmlspecialchars($j['nama_mapel']); ?></td>
                <td><?php echo htmlspecialchars($j['nama_guru']); ?></td>
            </tr>
            <?php 
                    endforeach;
                else: 
            ?>
            <tr>
                <td style="text-align: center;"><strong><?php echo $day; ?></strong></td>
                <td colspan="3" style="text-align: center; color: #999;">- Belum ada jadwal -</td>
            </tr>
            <?php 
                endif; 
            endforeach; 
            ?>
        </table>

        <h2 style="text-align: left; margin-top:50px;">LAMPIRAN III: JADWAL PIKET KELAS</h2>
        <table class="tabel-data">
            <tr>
                <th style="width: 33%;">Senin</th>
                <th style="width: 33%;">Selasa</th>
                <th style="width: 34%;">Rabu</th>
            </tr>
            <tr>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Senin']??'') ? implode("\n", $jadwal_piket['Senin']) : ($jadwal_piket['Senin']??''))); ?></td>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Selasa']??'') ? implode("\n", $jadwal_piket['Selasa']) : ($jadwal_piket['Selasa']??''))); ?></td>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Rabu']??'') ? implode("\n", $jadwal_piket['Rabu']) : ($jadwal_piket['Rabu']??''))); ?></td>
            </tr>
            <tr>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
            </tr>
            <tr>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Kamis']??'') ? implode("\n", $jadwal_piket['Kamis']) : ($jadwal_piket['Kamis']??''))); ?></td>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Jumat']??'') ? implode("\n", $jadwal_piket['Jumat']) : ($jadwal_piket['Jumat']??''))); ?></td>
                <td><?php echo nl2br(htmlspecialchars(is_array($jadwal_piket['Sabtu']??'') ? implode("\n", $jadwal_piket['Sabtu']) : ($jadwal_piket['Sabtu']??''))); ?></td>
            </tr>
        </table>
    </div>

    <!-- DATA SISWA -->
    <div class="page">
        <h2>LAMPIRAN III: DATA SISWA KELAS <?php echo strtoupper($kelas['tingkat']); ?></h2>
        <table class="tabel-data" style="font-size: 10pt;">
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIS/NISN</th>
                <th style="width: 35%;">Nama Lengkap</th>
                <th style="width: 10%;">L/P</th>
                <th style="width: 35%;">Tempat, Tgl Lahir</th>
            </tr>
            <?php $no=1; foreach($siswas as $s): ?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($s['nis'].' / '.$s['nisn']); ?></td>
                <td><?php echo htmlspecialchars($s['nama']); ?></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($s['jenis_kelamin']); ?></td>
                <td><?php echo htmlspecialchars($s['tempat_lahir'].', '.date('d/m/Y', strtotime($s['tanggal_lahir']))); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
