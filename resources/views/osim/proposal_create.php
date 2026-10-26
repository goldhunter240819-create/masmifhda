<?php include __DIR__ . '/_layout_top.php'; ?>

<style>
    /* A4 Paper CSS */
    .a4-wrapper {
        background: #e2e8f0;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 40px;
        border-radius: 20px;
        margin-top: 30px;
    }
    .a4-paper {
        background: white;
        width: 210mm;
        min-height: 297mm;
        /* Margin Skripsi: Atas 3cm, Kanan 3cm, Bawah 3cm, Kiri 4cm */
        padding: 30mm 30mm 30mm 40mm;
        box-sizing: border-box;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        font-family: 'Times New Roman', Times, serif;
        color: #000;
        line-height: 1.5;
        position: relative;
    }
    .a4-page-number {
        position: absolute;
        bottom: 20mm;
        right: 30mm;
        font-size: 12pt;
    }
    .a4-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .a4-header h2 { margin: 0; font-size: 16pt; font-weight: bold; }
    .a4-header h1 { margin: 5px 0; font-size: 20pt; font-weight: bold; }
    .a4-content h3 { font-size: 14pt; margin-top: 10px; margin-bottom: 15px; text-transform: uppercase; font-weight: bold; text-align: center; }
    .a4-content h4 { font-size: 12pt; margin-top: 12px; margin-bottom: 6px; font-weight: bold; }
    
    /* Tipografi Skripsi (Paragraf menjorok ke dalam) */
    .a4-content p { 
        font-size: 12pt; 
        text-align: justify; 
        text-indent: 1.27cm; /* Indentasi 1 tab standar skripsi */
        margin-bottom: 8px; 
    }
    
    /* Pengecualian indentasi untuk teks khusus */
    .a4-content p.no-indent { text-indent: 0; }
    .a4-content.daftar-isi p { 
        text-indent: 0; 
        text-align: left !important; 
        display: flex;
        align-items: flex-end;
        margin-bottom: 8px;
    }
    .a4-content.daftar-isi .dots {
        flex-grow: 1;
        border-bottom: 2px dotted #000;
        margin: 0 10px;
        position: relative;
        top: -4px;
    }
    .a4-content p.center-text { text-align: center; text-indent: 0; }
    
    .a4-content ul, .a4-content ol { 
        font-size: 12pt; 
        text-align: justify; 
        margin-bottom: 10px; 
        margin-left: 1.27cm; 
        padding-left: 15px; 
    }
    .a4-content li { margin-bottom: 5px; }
    .a4-content table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10.5pt; margin-top: 5px; line-height: 1.2; }
    .a4-content table, .a4-content th, .a4-content td { border: 1px solid #000; padding: 3px 5px; }
    .a4-content th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
    .quote-box { margin: 15px 1.27cm; padding: 10px 15px; border-left: 4px solid #000; font-style: italic; background: #f9f9f9; text-indent: 0 !important; }
    
    .form-section {
        background: #fff;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    }
    
    @media print {
        body { background: white; }
        .form-section, .editor-tabs, .e-btn { display: none !important; }
        .a4-wrapper { 
            display: block !important;
            background: transparent !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            box-shadow: none !important; 
        }
        .a4-paper { 
            box-shadow: none !important; 
            margin: 0 !important; 
            padding: 30mm 30mm 30mm 40mm !important;
            width: 100% !important;
            min-height: 275mm !important;
            page-break-after: always;
        }
        @page { margin: 0; }
    }
</style>

<div class="form-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 1.4rem; color: #1e293b;"><i data-lucide="edit-3" style="width: 24px; height: 24px; vertical-align: middle; color: #8b5cf6;"></i> Editor E-Proposal (Live Preview)</h2>
        <div>
            <a href="<?= base_url('osim/proposal') ?>" class="e-btn" style="background: #f1f5f9; color: #64748b; margin-right: 10px;"><i data-lucide="arrow-left"></i> Kembali</a>
            <button id="btnSimpanDraft" class="e-btn" style="background: #8b5cf6; color: #fff; border: none;"><i data-lucide="save"></i> Simpan Draft</button>
            <button class="e-btn" style="background: #10b981; color: #fff; border: none;" onclick="window.print()"><i data-lucide="printer"></i> Cetak PDF</button>
        </div>
    </div>

    <style>
        .editor-tabs { display: flex; border-bottom: 1px solid #cbd5e1; margin-bottom: 20px; overflow-x: auto; }
        .editor-tab { padding: 12px 20px; cursor: pointer; font-weight: 600; color: #64748b; border-bottom: 3px solid transparent; white-space: nowrap; }
        .editor-tab.active { color: #0d9488; border-bottom-color: #0d9488; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .dynamic-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .dynamic-row input { padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        .btn-remove { background: #ef4444; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; }
        .btn-add { background: #0ea5e9; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 10px; display: inline-flex; align-items: center; gap: 5px; }
    </style>

    <div class="form-section">
        <div class="editor-tabs">
            <div class="editor-tab active" data-tab="tab-info"><i data-lucide="info" style="width:16px; vertical-align:text-bottom;"></i> Info Dasar</div>
            <div class="editor-tab" data-tab="tab-panitia"><i data-lucide="users" style="width:16px; vertical-align:text-bottom;"></i> Kepanitiaan</div>
            <div class="editor-tab" data-tab="tab-jadwal"><i data-lucide="calendar" style="width:16px; vertical-align:text-bottom;"></i> Jadwal Kegiatan</div>
            <div class="editor-tab" data-tab="tab-rab"><i data-lucide="calculator" style="width:16px; vertical-align:text-bottom;"></i> R.A.B</div>
        </div>

        <!-- TAB INFO -->
        <div id="tab-info" class="tab-content active">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Judul Kegiatan</label>
                    <input type="text" id="inputJudul" value="<?= $type == 'matamuda' ? 'MASA TA\'ARUF SISWA MADRASAH (MATAMUDA)' : '' ?>" placeholder="Misal: LATIHAN DASAR KEPEMIMPINAN (LDK)" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Tema Kegiatan</label>
                    <input type="text" id="inputTema" value="<?= $type == 'matamuda' ? 'Membentuk Generasi Rabbani yang Cerdas, Beradab, dan Berwawasan Global' : '' ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Waktu & Tempat</label>
                    <input type="text" id="inputWaktu" value="<?= $type == 'matamuda' ? 'Senin - Rabu, 13 - 15 Juli 2026 di MAS Miftahul Huda' : '' ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Ketua Panitia Pelaksana</label>
                    <select id="inputPanitia" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
                        <option>-- Pilih dari Pengurus OSIM --</option>
                        <option <?= $type == 'matamuda' ? 'selected' : '' ?>>Perdi Aprizal</option>
                        <option <?= $type != 'matamuda' ? 'selected' : '' ?>>Rizky Maulana (NIS: 12345)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TAB PANITIA -->
        <div id="tab-panitia" class="tab-content">
            <div id="container-seksi">
                <?php if ($type == 'matamuda') { ?>
                <div class="dynamic-row">
                    <input type="text" class="input-seksi-nama" placeholder="Nama Seksi (Misal: Seksi Acara)" value="Seksi Acara (Koord: M. Ghaza Al Ghifari)" style="flex:1;" oninput="renderPanitia()">
                    <input type="text" class="input-seksi-anggota" placeholder="Anggota (Pemisah koma, misal: Budi, Caca)" value="Ridho, Tri Lestari, Eren" style="flex:2;" oninput="renderPanitia()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
                </div>
                <div class="dynamic-row">
                    <input type="text" class="input-seksi-nama" placeholder="Nama Seksi (Misal: Seksi Acara)" value="Seksi Perlengkapan (Koord: Ari Santsos)" style="flex:1;" oninput="renderPanitia()">
                    <input type="text" class="input-seksi-anggota" placeholder="Anggota (Pemisah koma, misal: Budi, Caca)" value="Rio Apriyanto, Ayatul Kisna, Naela Afifatun Nisa" style="flex:2;" oninput="renderPanitia()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
                </div>
                <div class="dynamic-row">
                    <input type="text" class="input-seksi-nama" placeholder="Nama Seksi (Misal: Seksi Acara)" value="Seksi Konsumsi (Koord: Subhanal Hawa Aulia)" style="flex:1;" oninput="renderPanitia()">
                    <input type="text" class="input-seksi-anggota" placeholder="Anggota (Pemisah koma, misal: Budi, Caca)" value="M. Gilang Ramadan, Dimas Aditiyansah, Najwa" style="flex:2;" oninput="renderPanitia()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
                </div>
                <div class="dynamic-row">
                    <input type="text" class="input-seksi-nama" placeholder="Nama Seksi (Misal: Seksi Acara)" value="Seksi Dokumentasi" style="flex:1;" oninput="renderPanitia()">
                    <input type="text" class="input-seksi-anggota" placeholder="Anggota (Pemisah koma, misal: Budi, Caca)" value="M Faiz Amajid" style="flex:2;" oninput="renderPanitia()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
                </div>
                <?php } else { ?>
                <div class="dynamic-row">
                    <input type="text" class="input-seksi-nama" placeholder="Nama Seksi (Misal: Seksi Acara)" value="Seksi Acara" style="flex:1;">
                    <input type="text" class="input-seksi-anggota" placeholder="Anggota (Pemisah koma, misal: Budi, Caca)" value="Andi, Siti, Rahman" style="flex:2;">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
                </div>
                <?php } ?>
            </div>
            <button class="btn-add" onclick="addSeksiRow()"><i data-lucide="plus"></i> Tambah Seksi</button>
        </div>

        <!-- TAB JADWAL -->
        <div id="tab-jadwal" class="tab-content">
            <div id="container-jadwal">
                <?php
                $jadwal_matamuda = [
                    ['Selasa, 14 Juli 2026', '07.30 - 09.30', 'Pembukaan MATAMUDA (Lapangan)'],
                    ['Selasa, 14 Juli 2026', '09.30 - 10.00', 'Perkenalan Panitia'],
                    ['Selasa, 14 Juli 2026', '10.00 - 10.15', 'Istirahat'],
                    ['Selasa, 14 Juli 2026', '10.15 - 11.00', 'Materi 1 (Pengenalan Lingkungan Madrasah)'],
                    ['Selasa, 14 Juli 2026', '11.00 - 12.00', 'Panitia'],
                    ['Selasa, 14 Juli 2026', '12.00 - 12.30', 'ISHOMA'],
                    ['Selasa, 14 Juli 2026', '12.30 - 13.00', 'Persiapan Hari ke-2 (Panitia)'],

                    ['Rabu, 15 Juli 2026', '07.30 - 08.00', 'Persiapan Panitia'],
                    ['Rabu, 15 Juli 2026', '08.00 - 09.00', 'Materi 2 (Penguatan dan Motivasi terhadap Siswa)'],
                    ['Rabu, 15 Juli 2026', '09.00 - 10.00', 'Panitia'],
                    ['Rabu, 15 Juli 2026', '10.00 - 10.15', 'Istirahat'],
                    ['Rabu, 15 Juli 2026', '10.15 - 11.15', 'Materi 3 (KBM dan Pengenalan Kurikulum)'],
                    ['Rabu, 15 Juli 2026', '11.15 - 12.00', 'Panitia'],
                    ['Rabu, 15 Juli 2026', '12.00 - 12.30', 'ISHOMA'],
                    ['Rabu, 15 Juli 2026', '12.30 - 13.00', 'Persiapan Hari ke-3 (Panitia)'],

                    ['Kamis, 16 Juli 2026', '07.30 - 08.00', 'Persiapan Panitia'],
                    ['Kamis, 16 Juli 2026', '08.00 - 09.00', 'Materi 4 (Akhlaqul Karimah)'],
                    ['Kamis, 16 Juli 2026', '09.00 - 10.00', 'Panitia'],
                    ['Kamis, 16 Juli 2026', '10.00 - 10.15', 'Istirahat'],
                    ['Kamis, 16 Juli 2026', '10.15 - 11.15', 'Materi 5 (Pengenalan Ekstrakurikuler)'],
                    ['Kamis, 16 Juli 2026', '11.15 - 12.00', 'Panitia'],
                    ['Kamis, 16 Juli 2026', '12.00 - 12.30', 'ISHOMA'],
                    ['Kamis, 16 Juli 2026', '12.30 - 13.00', 'Penutupan MATAMUDA'],
                ];
                
                if ($type == 'matamuda') {
                    foreach($jadwal_matamuda as $j) {
                        echo '<div class="dynamic-row">
                            <input type="text" class="input-jadwal-hari" placeholder="Hari/Tanggal" value="'.$j[0].'" style="flex:1;" oninput="renderJadwal()">
                            <input type="text" class="input-jadwal-jam" placeholder="Jam" value="'.$j[1].'" style="flex:1;" oninput="renderJadwal()">
                            <input type="text" class="input-jadwal-kegiatan" placeholder="Kegiatan" value="'.$j[2].'" style="flex:2;" oninput="renderJadwal()">
                            <button class="btn-remove" onclick="this.parentElement.remove(); renderJadwal();">&times;</button>
                        </div>';
                    }
                } else {
                ?>
                <div class="dynamic-row">
                    <input type="text" class="input-jadwal-hari" placeholder="Hari/Tanggal" value="Senin, 13 Juli 2026" style="flex:1;" oninput="renderJadwal()">
                    <input type="text" class="input-jadwal-jam" placeholder="Jam (Misal: 07.00 - 08.00)" value="07.00 - 08.00" style="flex:1;" oninput="renderJadwal()">
                    <input type="text" class="input-jadwal-kegiatan" placeholder="Kegiatan (Misal: Upacara Pembukaan)" value="Upacara Pembukaan" style="flex:2;" oninput="renderJadwal()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderJadwal();">&times;</button>
                </div>
                <?php } ?>
            </div>
            <button class="btn-add" onclick="addJadwalRow()"><i data-lucide="plus"></i> Tambah Jadwal</button>
        </div>

        <!-- TAB RAB -->
        <div id="tab-rab" class="tab-content">
            <div id="container-rab">
                <div class="dynamic-row">
                    <input type="text" class="input-rab-uraian" placeholder="Uraian (Misal: Konsumsi Peserta)" value="Konsumsi Peserta 100 org x 3 hari" style="flex:2;">
                    <input type="number" class="input-rab-vol" placeholder="Vol (Misal: 300)" value="300" style="width: 80px;" oninput="renderRAB()">
                    <input type="text" class="input-rab-satuan" placeholder="Satuan (Misal: Kotak)" value="Kotak" style="width: 100px;">
                    <input type="number" class="input-rab-harga" placeholder="Harga Sat (Misal: 15000)" value="15000" style="flex:1;" oninput="renderRAB()">
                    <button class="btn-remove" onclick="this.parentElement.remove(); renderRAB();">&times;</button>
                </div>
            </div>
            <button class="btn-add" onclick="addRABRow()"><i data-lucide="plus"></i> Tambah Anggaran</button>
        </div>
    </div>
</div>

<!-- Kertas A4 Live Preview -->
<div class="a4-wrapper" id="printableArea">
    
    <!-- HALAMAN 1: COVER -->
    <div class="a4-paper">
        <div class="a4-header" style="border: none; padding-top: 50px;">
            <div style="display: flex; justify-content: center; margin-bottom: 40px;">
                <img src="<?= base_url('public/uploads/logo/logo_1781854511.png') ?>" width="120" alt="Madrasah" onerror="this.src='https://via.placeholder.com/120'">
            </div>
            <h2 style="font-size: 18pt; margin-bottom: 10px;">PROPOSAL KEGIATAN</h2>
            <h1 class="val-judul" style="font-size: 24pt; margin-bottom: 20px;"><?= $type == 'matamuda' ? 'MASA TA\'ARUF SISWA MADRASAH<br>(MATAMUDA)' : 'JUDUL KEGIATAN' ?></h1>
            <p style="font-size: 14pt; font-style: italic;">Tema:<br>"<span class="val-tema"><?= $type == 'matamuda' ? 'Membentuk Generasi Rabbani yang Cerdas, Beradab, dan Berwawasan Global' : 'TEMA KEGIATAN' ?></span>"</p>
            <div style="margin-top: 80px; margin-bottom: 80px;">
                <p style="font-size: 14pt; font-weight: bold;">TAHUN PELAJARAN <?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?></p>
            </div>
            <h3 style="font-size: 16pt; margin-bottom: 5px;">ORGANISASI SISWA INTRA MADRASAH (OSIM)</h3>
            <h3 style="font-size: 18pt; font-weight: bold; margin-top: 0; margin-bottom: 10px;"><?= htmlspecialchars(strtoupper($settings['nama_madrasah'] ?? 'MAS MIFTAHUL HUDA')) ?></h3>
            <p style="font-size: 12pt;">Jl. Tirtomoyo, No. 083, Margorahayu, Tambak Jaya, Way Tenong, Lampung Barat</p>
        </div>
    </div>

    <!-- HALAMAN 2: PENGESAHAN -->
    <div class="a4-paper">
        <div class="a4-page-number">2</div>
        <div class="a4-content">
            <h3 style="text-align: center; margin-bottom: 30px; font-weight: bold; font-size: 16pt;">LEMBAR PENGESAHAN</h3>
            <p>
                Proposal Kegiatan <strong class="val-judul-text"><?= $type == 'matamuda' ? 'MASA TA\'ARUF SISWA MADRASAH (MATAMUDA)' : 'JUDUL KEGIATAN' ?></strong> Tahun Pelajaran <?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?> ini telah disetujui dan disahkan pelaksanaannya pada <span class="val-waktu"><?= $type == 'matamuda' ? 'Senin - Rabu, 13 - 15 Juli 2026 di MAS Miftahul Huda' : 'Waktu dan Tempat Pelaksanaan' ?></span>.
            </p>
            <table style="width: 100%; text-align: center; border: none; margin-bottom: 20px; font-size: 12pt; margin-top: 20px;">
                <tr>
                    <td style="width: 50%; padding-bottom: 30px; border:none;">Ketua Panitia<br><br><br><strong><u class="val-panitia"><?= $type == 'matamuda' ? 'Perdi Aprizal' : 'Nama Ketua Panitia' ?></u></strong></td>
                    <td style="width: 50%; padding-bottom: 30px; border:none;">Ketua OSIM<br><br><br><strong><u><?= htmlspecialchars($settings['ketua_osim_nama'] ?? 'Nama Ketua OSIM') ?></u></strong><br>NIS. <?= htmlspecialchars($settings['ketua_osim_nis'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 30px; border:none;">Pembina OSIM<br><br><br><strong><u><?= htmlspecialchars($settings['pembina_nama'] ?? 'Nama Pembina') ?></u></strong><br>NIP. <?= htmlspecialchars($settings['pembina_nip'] ?? '-') ?></td>
                    <td style="padding-bottom: 30px; border:none;">Waka Kesiswaan<br><br><br><strong><u><?= htmlspecialchars($settings['wakasis_nama'] ?? 'Nama Waka Kesiswaan') ?></u></strong><br>NIP. <?= htmlspecialchars($settings['wakasis_nip'] ?? '-') ?></td>
                </tr>
            </table>
            <div style="text-align: center; font-size: 12pt;">
                Mengetahui/Menyetujui,<br>Kepala Madrasah<br><br><br>
                <strong><u><?= htmlspecialchars($settings['kamad_nama'] ?? 'Nama Kepala Madrasah') ?></u></strong><br>
                NIP. <?= htmlspecialchars($settings['kamad_nip'] ?? '-') ?>
            </div>
        </div>
    </div>

    <!-- HALAMAN 3: KATA PENGANTAR -->
    <div class="a4-paper">
        <div class="a4-page-number">3</div>
        <div class="a4-content">
            <h3 style="text-align: center;">KATA PENGANTAR</h3>
            <div class="quote-box">"Maha Suci Engkau, tidak ada yang kami ketahui selain dari apa yang telah Engkau ajarkan kepada kami." (QS. Al-Baqarah: 32)</div>
            <p>Puji syukur senantiasa kami panjatkan kehadirat Allah SWT yang telah melimpahkan rahmat, taufik, serta hidayah-Nya, sehingga kami selaku Pengurus Organisasi Siswa Intra Madrasah (OSIM) dapat merampungkan penyusunan Proposal Kegiatan <span class="val-judul-text">MATAMUDA</span> Tahun Pelajaran <?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?>.</p>
            <p>Shalawat serta salam semoga senantiasa tercurahkan kepada junjungan kita, Nabi Muhammad SAW, yang telah membawa risalah Islam sebagai rahmatan lil 'alamin dan menjadi teladan utama dalam segala aspek kehidupan.</p>
            <p>Proposal ini disusun sebagai landasan berpijak dan pedoman pelaksanaan kegiatan bagi peserta didik baru. Kegiatan ini bukan sekadar rutinitas penyambutan tahunan, melainkan gerbang awal bagi siswa baru untuk mengenal ekosistem madrasah yang Islami, disiplin, dan berprestasi.</p>
            <p>Kami menyadari bahwa penyusunan proposal dan pelaksanaan kegiatan ini kelak tidak akan terwujud tanpa dukungan, bimbingan, dan kerja sama dari berbagai pihak. Oleh karena itu, kami menyampaikan terima kasih yang sebesar-besarnya kepada Bapak Kepala Madrasah, Bapak/Ibu Dewan Guru, Waka Kesiswaan, Pembina OSIM, serta seluruh rekan panitia yang telah berkontribusi.</p>
            <br><br>
            <div style="text-align: right;">
                <?= htmlspecialchars($settings['kota_madrasah'] ?? 'Nama Kota') ?>, Juli 2026<br><br><br><br>
                <strong>Panitia Pelaksana</strong>
            </div>
        </div>
    </div>

    <!-- HALAMAN 4: DAFTAR ISI -->
    <div class="a4-paper">
        <div class="a4-page-number">4</div>
        <div class="a4-content daftar-isi">
            <h3 style="text-align: center; margin-bottom: 30px;">DAFTAR ISI</h3>
            <p><span><strong>HALAMAN PENGESAHAN</strong></span><span class="dots"></span><span>2</span></p>
            <p><span><strong>KATA PENGANTAR</strong></span><span class="dots"></span><span>3</span></p>
            <p><span><strong>DAFTAR ISI</strong></span><span class="dots"></span><span>4</span></p>
            <p><span><strong>BAB I PENDAHULUAN</strong></span><span class="dots"></span><span>5</span></p>
            <p><span style="padding-left: 20px;">A. Latar Belakang</span><span class="dots"></span><span>5</span></p>
            <p><span style="padding-left: 20px;">B. Dasar Hukum</span><span class="dots"></span><span>6</span></p>
            <p><span style="padding-left: 20px;">C. Filosofi MATAMUDA</span><span class="dots"></span><span>6</span></p>
            <p><span><strong>BAB II MAKSUD, TUJUAN & SASARAN</strong></span><span class="dots"></span><span>7</span></p>
            <p><span><strong>BAB III PROFIL MADRASAH</strong></span><span class="dots"></span><span>8</span></p>
            <p><span><strong>BAB IV TEMA & KONSEP MATAMUDA</strong></span><span class="dots"></span><span>9</span></p>
            <p><span><strong>BAB V MATERI & JADWAL KEGIATAN</strong></span><span class="dots"></span><span>10</span></p>
            <p><span><strong>BAB VI SUSUNAN PANITIA & TATA TERTIB</strong></span><span class="dots"></span><span>13</span></p>
            <p><span><strong>BAB VII ANGGARAN & EVALUASI</strong></span><span class="dots"></span><span>16</span></p>
            <p><span><strong>BAB VIII PENUTUP</strong></span><span class="dots"></span><span>18</span></p>
            <p><span><strong>LAMPIRAN</strong></span><span class="dots"></span><span>19</span></p>
        </div>
    </div>

    <!-- HALAMAN 5: BAB I -->
    <div class="a4-paper">
        <div class="a4-page-number">5</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB I<br>PENDAHULUAN</h3>
            <div class="quote-box">"Sesungguhnya Allah tidak merubah keadaan sesuatu kaum sehingga mereka merubah keadaan yang ada pada diri mereka sendiri." (QS. Ar-Ra'd: 11)</div>
            <h4>A. Latar Belakang</h4>
            <p>Peralihan status dari siswa Taman Kanak-Kanak/Raudhatul Athfal (TK/RA) menjadi siswa Madrasah Ibtidaiyah (MI) merupakan fase transisi psikologis dan sosial yang sangat krusial. Lingkungan baru, teman baru, guru baru, serta sistem pembelajaran yang lebih terstruktur menuntut peserta didik baru untuk mampu beradaptasi dengan cepat. Di sinilah letak pentingnya program <span class="val-judul-text">MATAMUDA</span>.</p>
            <p>Kementerian Agama senantiasa menekankan bahwa pendidikan madrasah tidak hanya berfokus pada transfer pengetahuan (kognitif), melainkan juga pembentukan akhlak (afektif). Oleh karena itu, orientasi siswa baru tidak boleh lagi diwarnai oleh praktik-praktik perploncoan, kekerasan fisik, maupun tekanan mental yang kontraproduktif. MATAMUDA hadir sebagai paradigma baru: orientasi yang edukatif, ramah anak, dan mengedepankan nilai-nilai keislaman.</p>
            <p>Kami merancang kegiatan ini dengan pendekatan yang komprehensif. Kami menyadari bahwa madrasah adalah "rumah kedua" bagi siswa. Melalui program ini, siswa akan dikenalkan dengan lingkungan fisik madrasah, budaya akademik, pembiasaan ibadah, hingga etika pergaulan yang dilandasi nilai-nilai moderasi beragama dan anti-perundungan (anti-bullying). Dengan demikian, diharapkan benih-benih kecintaan terhadap madrasah mulai tumbuh pada hari-hari pertama mereka belajar.</p>
        </div>
    </div>

    <!-- HALAMAN 6: Lanjutan BAB I -->
    <div class="a4-paper">
        <div class="a4-page-number">6</div>
        <div class="a4-content">
            <h4>B. Dasar Hukum</h4>
            <p>Pelaksanaan kegiatan MATAMUDA ini berlandaskan pada peraturan dan perundang-undangan sebagai berikut:</p>
            <ol>
                <li>Undang-Undang RI Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
                <li>Peraturan Menteri Agama RI Nomor 90 Tahun 2013 tentang Penyelenggaraan Pendidikan Madrasah.</li>
                <li>Peraturan Menteri Pendidikan dan Kebudayaan RI Nomor 18 Tahun 2016 tentang Pengenalan Lingkungan Sekolah Bagi Siswa Baru.</li>
                <li>Keputusan Dirjen Pendis Kemenag RI terkait Panduan MATSAMA Tahun Pelajaran berjalan.</li>
                <li>Program Kerja OSIM Masa Bakti <?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?>.</li>
            </ol>
            <h4>C. Filosofi MATAMUDA</h4>
            <p>Kata <strong>MATAMUDA</strong> merupakan akronim dari <em>Masa Ta'aruf Siswa Madrasah</em>, namun secara filosofis bermakna bahwa peserta didik baru adalah tunas-tunas <strong>muda</strong> yang menjadi <strong>mata</strong> air harapan masa depan umat dan bangsa. Filosofi ini dijabarkan ke dalam tiga pilar utama kegiatan:</p>
            <ol>
                <li><strong>Ta'aruf (Mengenal):</strong> Proses saling mengenal antar siswa, mengenal dewan guru, dan mengenal lingkungan fisik serta fasilitas madrasah.</li>
                <li><strong>Tafahum (Memahami):</strong> Proses memahami aturan, tata tertib, sistem e-learning, dan budaya-budaya baik yang berlaku di madrasah.</li>
                <li><strong>Ta'awun (Tolong-menolong/Sinergi):</strong> Penanaman sikap gotong royong, empati, dan kerja sama sejak dini melalui berbagai games edukatif.</li>
            </ol>
        </div>
    </div>

    <!-- HALAMAN 7: BAB II -->
    <div class="a4-paper">
        <div class="a4-page-number">7</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB II<br>MAKSUD, TUJUAN & SASARAN</h3>
            <h4>A. Maksud dan Tujuan</h4>
            <p>Kegiatan ini dimaksudkan sebagai jembatan adaptasi yang ramah, aman, dan menyenangkan bagi peserta didik baru. Adapun tujuan spesifik dari kegiatan ini adalah:</p>
            <ol>
                <li>Menggali potensi awal, minat, dan bakat peserta didik baru untuk pembinaan kegiatan ekstrakurikuler lebih lanjut.</li>
                <li>Membantu peserta didik baru beradaptasi dengan lingkungan madrasah dan sekitarnya, meliputi aspek keamanan, fasilitas umum, dan sarana prasarana belajar.</li>
                <li>Menumbuhkan motivasi, semangat, dan cara belajar efektif sebagai siswa madrasah.</li>
                <li>Menanamkan nilai-nilai religius, kejujuran, kemandirian, sikap saling menghargai, kedisiplinan, hidup bersih dan sehat.</li>
                <li>Mengkampanyekan lingkungan madrasah yang bebas dari kekerasan fisik, verbal, maupun perundungan (zero bullying).</li>
            </ol>
            <h4>B. Sasaran dan Manfaat</h4>
            <p><strong>Sasaran:</strong> Seluruh peserta didik baru Tahun Pelajaran <?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?>.</p>
            <p><strong>Manfaat Kegiatan:</strong></p>
            <ul>
                <li><strong>Bagi Peserta Didik Baru:</strong> Mendapatkan rasa aman, nyaman, dan percaya diri pada hari-hari pertama bersekolah, serta mengetahui hak dan kewajibannya.</li>
                <li><strong>Bagi Orang Tua:</strong> Memiliki keyakinan dan ketenangan menitipkan putra-putrinya di lingkungan madrasah yang positif.</li>
                <li><strong>Bagi Madrasah:</strong> Memperoleh gambaran awal mengenai profil psikologis dan akademis siswa baru, serta membangun citra madrasah yang ramah anak dan profesional.</li>
            </ul>
        </div>
    </div>

    <!-- HALAMAN 8: BAB III -->
    <div class="a4-paper">
        <div class="a4-page-number">8</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB III<br>PROFIL SINGKAT MADRASAH</h3>
            <div class="quote-box">"Orang-orang yang beriman dan berhijrah serta berjihad di jalan Allah dengan harta, benda dan diri mereka, adalah lebih tinggi derajatnya di sisi Allah." (QS. At-Taubah: 20)</div>
            <h4>1. Sejarah Singkat</h4>
            <p>Madrasah ini didirikan oleh para tokoh ulama setempat dengan semangat <em>Tafaqquh Fiddin</em>. Berawal dari madrasah diniyah kecil, madrasah ini terus berkembang secara institusional dan infrastruktur, hingga kini mengadopsi sistem digital terpadu untuk administrasi dan pembelajarannya.</p>
            <h4>2. Visi</h4>
            <p>"Terwujudnya Madrasah Unggul yang Berakhlakul Karimah, Berprestasi Mandiri, dan Berwawasan Global yang Dilandasi Nilai-Nilai Islam."</p>
            <h4>3. Misi</h4>
            <ul>
                <li>Menyelenggarakan pendidikan dasar Islam yang komprehensif.</li>
                <li>Menumbuhkembangkan budaya literasi dan pemanfaatan teknologi digital dalam pembelajaran.</li>
                <li>Membentuk karakter santri yang beradab, toleran, dan anti-kekerasan.</li>
            </ul>
            <h4>4. Motto</h4>
            <p><strong>"Cerdas Menginspirasi, Berakhlak Membumi."</strong></p>
            <h4>5. Budaya Madrasah (Materi Wajib MATAMUDA)</h4>
            <ul>
                <li><strong>Budaya 5S:</strong> Senyum, Salam, Sapa, Sopan, Santun.</li>
                <li><strong>Spiritual Pagi:</strong> Shalat Dhuha berjamaah dan Tadarus Al-Qur'an.</li>
                <li><strong>Literasi Pagi:</strong> Membaca buku non-pelajaran 15 menit.</li>
                <li><strong>Jumat Bersih & Berkah:</strong> Kerja bakti dan infaq rutin.</li>
                <li><strong>Disiplin Waktu:</strong> Hadir 10 menit sebelum bel, absensi QR Code.</li>
            </ul>
        </div>
    </div>

    <!-- HALAMAN 9: BAB IV -->
    <div class="a4-paper">
        <div class="a4-page-number">9</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB IV<br>TEMA & KONSEP KEGIATAN</h3>
            <h4>A. Nilai-Nilai MATAMUDA</h4>
            <p>Agar kegiatan ini tidak sekadar event seremonial, panitia mematok 8 Core Values (Nilai Utama) yang disisipkan dalam setiap materi dan games:</p>
            <ul>
                <li><strong>Religius:</strong> Ketaatan ibadah wajib dan sunnah.</li>
                <li><strong>Disiplin:</strong> Menghargai waktu dan tata tertib.</li>
                <li><strong>Tanggung Jawab:</strong> Berani mengakui kesalahan dan memperbaiki diri.</li>
                <li><strong>Jujur:</strong> Terbuka dan tidak curang.</li>
                <li><strong>Peduli:</strong> Empati terhadap teman yang kesulitan.</li>
                <li><strong>Gotong Royong:</strong> Kerja sama tim.</li>
                <li><strong>Integritas:</strong> Kesesuaian ucapan dan perbuatan.</li>
                <li><strong>Cinta Lingkungan:</strong> Membuang sampah pada tempatnya.</li>
            </ul>
            <h4>B. Tema, Tagline & Atribut</h4>
            <p><strong>TEMA KEGIATAN:</strong></p>
            <p class="val-tema" style="font-weight: bold; font-style: italic;">Membentuk Generasi Rabbani yang Cerdas, Beradab, dan Berwawasan Global</p>
            <p><strong>SUBTEMA:</strong></p>
            <p>"Menyemai Karakter Moderat dan Anti-Bullying Sejak Dini"</p>
            <p><strong>TAGLINE:</strong></p>
            <p>#MataMudaBerakhlak | #MadrasahHebatBermartabat</p>
            <p><strong>DRESSCODE PESERTA:</strong></p>
            <ul>
                <li><strong>Hari Ke-1 (Senin):</strong> Seragam Merah Putih / Bebas Rapi. Name tag panitia.</li>
                <li><strong>Hari Ke-2 (Selasa):</strong> Pakaian Olahraga.</li>
                <li><strong>Hari Ke-3 (Rabu):</strong> Seragam Pramuka.</li>
            </ul>
            <p><em>(Catatan: Panitia dilarang memberikan instruksi pemakaian atribut aneh yang membebani orang tua).</em></p>
        </div>
    </div>

    <!-- HALAMAN 10: BAB V -->
    <div class="a4-paper">
        <div class="a4-page-number">10</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB V<br>MATERI & JADWAL KEGIATAN</h3>
            <h4>A. Kurikulum Materi MATAMUDA</h4>
            <p>MATAMUDA diformulasikan dengan kurikulum materi esensial berikut:</p>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Materi</th>
                        <th style="width: 65%;">Cakupan & Pemateri</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Kemadrasahan</td><td>Sejarah, visi misi, fasilitas madrasah. (Kepala Madrasah)</td></tr>
                    <tr><td>2</td><td>Tata Tertib</td><td>Budaya 5S, adab, disiplin waktu. (Waka Kesiswaan)</td></tr>
                    <tr><td>3</td><td>Moderasi Beragama</td><td>Toleransi, menghargai perbedaan. (Guru PAI)</td></tr>
                    <tr><td>4</td><td>Anti Bullying</td><td>Bentuk bullying, bahaya, cara lapor. (Guru BK)</td></tr>
                    <tr><td>5</td><td>Literasi Digital</td><td>Cara login e-learning, portal siswa. (Tim IT)</td></tr>
                    <tr><td>6</td><td>Ekstrakurikuler</td><td>Demo Ekskul Pramuka, Futsal, dll. (Pengurus OSIM)</td></tr>
                    <tr><td>7</td><td>P5RA</td><td>Pengenalan Kurikulum Merdeka. (Waka Kurikulum)</td></tr>
                    <tr><td>8</td><td>Kepemimpinan</td><td>Dinamika kelompok, kekompakan tim. (Panitia OSIM)</td></tr>
                </tbody>
            </table>

        </div>
    </div>

    <!-- HALAMAN 11: Lanjutan BAB V -->
    <div class="a4-paper">
        <div class="a4-page-number">11</div>
        <div class="a4-content">
            <h4>B. Jadwal Kegiatan MATAMUDA</h4>
            <p><strong>Waktu Pelaksanaan:</strong> <span class="val-waktu">Selasa - Kamis, 14 - 16 Juli 2026 di MAS Miftahul Huda</span>.</p>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Hari/Tanggal</th>
                        <th style="width: 17%;">Waktu</th>
                        <th style="width: 45%;">Kegiatan</th>
                        <th style="width: 23%;">Tempat / PJ</th>
                    </tr>
                </thead>
                <tbody id="val-jadwal-list">
                    <tr>
                        <td rowspan="7"><strong>Selasa,<br>14 Juli 2026</strong></td>
                        <td style="white-space: nowrap;">07.30-09.30</td><td>Pembukaan MATAMUDA</td><td>Lapangan</td>
                    </tr>
                    <tr><td style="white-space: nowrap;">09.30-10.00</td><td>Perkenalan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">10.00-10.15</td><td>Istirahat</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">10.15-11.00</td><td>Materi 1 (Pengenalan Lingkungan Madrasah)</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">11.00-12.00</td><td>Kegiatan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">12.00-12.30</td><td>ISHOMA</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">12.30-13.00</td><td>Persiapan Hari ke-2</td><td>Panitia</td></tr>
                    <tr>
                        <td rowspan="8"><strong>Rabu,<br>15 Juli 2026</strong></td>
                        <td style="white-space: nowrap;">07.30-08.00</td><td>Persiapan Panitia</td><td>Panitia</td>
                    </tr>
                    <tr><td style="white-space: nowrap;">08.00-09.00</td><td>Materi 2 (Penguatan & Motivasi)</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">09.00-10.00</td><td>Kegiatan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">10.00-10.15</td><td>Istirahat</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">10.15-11.15</td><td>Materi 3 (KBM & Kurikulum)</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">11.15-12.00</td><td>Kegiatan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">12.00-12.30</td><td>ISHOMA</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">12.30-13.00</td><td>Persiapan Hari ke-3</td><td>Panitia</td></tr>
                    <tr>
                        <td rowspan="8"><strong>Kamis,<br>16 Juli 2026</strong></td>
                        <td style="white-space: nowrap;">07.30-08.00</td><td>Persiapan Panitia</td><td>Panitia</td>
                    </tr>
                    <tr><td style="white-space: nowrap;">08.00-09.00</td><td>Materi 4 (Akhlaqul Karimah)</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">09.00-10.00</td><td>Kegiatan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">10.00-10.15</td><td>Istirahat</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">10.15-11.15</td><td>Materi 5 (Pengenalan Ekstrakurikuler)</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">11.15-12.00</td><td>Kegiatan Panitia</td><td>Panitia</td></tr>
                    <tr><td style="white-space: nowrap;">12.00-12.30</td><td>ISHOMA</td><td>-</td></tr>
                    <tr><td style="white-space: nowrap;">12.30-13.00</td><td>Penutupan MATAMUDA</td><td>Panitia</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- HALAMAN 12: BAB VI -->
    <div class="a4-paper">
        <div class="a4-page-number">12</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB VI<br>SUSUNAN PANITIA & TATA TERTIB</h3>
            <h4>A. Susunan Panitia</h4>
            <p><strong>Pelindung:</strong> Kepala Madrasah<br>
            <strong>Penasihat:</strong> Waka Kesiswaan<br>
            <strong>Penanggung Jawab:</strong> Pembina OSIM</p>
            <p><strong>Panitia Pelaksana (OSIM):</strong></p>
            <ul>
                <li><strong>Ketua Panitia:</strong> <span class="val-panitia"><?= $type == 'matamuda' ? 'Perdi Aprizal' : 'Nama Ketua Panitia' ?></span></li>
                <li><strong>Wakil Ketua:</strong> <?= $type == 'matamuda' ? 'Rizqi Aria Saputra' : 'Nama Wakil Ketua' ?></li>
                <li><strong>Sekretaris:</strong> <?= $type == 'matamuda' ? 'Raisya Mutiara Ayu' : htmlspecialchars($settings['sekretaris_osim_nama'] ?? 'Sekretaris OSIM') ?></li>
                <li><strong>Bendahara:</strong> <?= $type == 'matamuda' ? 'Elis Setia Agista' : htmlspecialchars($settings['bendahara_osim_nama'] ?? 'Bendahara OSIM') ?></li>
            </ul>
            <p><strong>Seksi-seksi:</strong></p>
            <ul id="val-panitia-list">
                <li><strong>Seksi Acara:</strong> Mengatur rundown, menghubungi pemateri, memimpin ice breaking.</li>
                <li><strong>Seksi Kesekretariatan:</strong> Presensi, name tag, sertifikat/piagam, dokumen LPJ.</li>
                <li><strong>Seksi Perlengkapan:</strong> Sound system, proyektor, spanduk, tata ruang.</li>
                <li><strong>Seksi Konsumsi:</strong> Air minum pemateri, snack peserta.</li>
                <li><strong>Seksi Pubdok:</strong> Foto, video, posting sosmed.</li>
                <li><strong>Seksi Keamanan & Korlap:</strong> Ketertiban peserta, mobilitas, pendamping kelompok.</li>
            </ul>
        </div>
    </div>

    <!-- HALAMAN 13: Tata Tertib Peserta -->
    <div class="a4-paper">
        <div class="a4-page-number">13</div>
        <div class="a4-content">
            <h4>B. Tata Tertib Peserta</h4>
            <p>Untuk menjamin kelancaran MATAMUDA dan menanamkan sikap <strong>Disiplin</strong> serta <strong>Tanggung Jawab</strong>, seluruh peserta diwajibkan mematuhi tata tertib berikut:</p>
            <p><strong>Kewajiban Peserta:</strong></p>
            <ol>
                <li>Datang tepat waktu (selambat-lambatnya 10 menit sebelum acara dimulai).</li>
                <li>Mengenakan pakaian sesuai dengan ketentuan hari yang berlaku, bersih, rapi.</li>
                <li>Wajib memakai Name Tag selama kegiatan berlangsung.</li>
                <li>Membawa bekal air minum dan makanan ringan sehat.</li>
                <li>Meminta izin kepada panitia pendamping apabila hendak ke toilet.</li>
                <li>Menjaga kebersihan lingkungan (membuang sampah di tempat yang telah disediakan).</li>
                <li>Menghormati dewan guru, panitia, dan sesama teman (mengucapkan salam).</li>
                <li>Mengikuti seluruh rangkaian kegiatan dari awal hingga akhir dengan antusias.</li>
            </ol>
            <p><strong>Larangan Peserta:</strong></p>
            <ol>
                <li>Tidak diperkenankan membawa barang berharga berlebihan.</li>
                <li>Tidak diperkenankan membawa HP/Gadget kecuali ada instruksi panitia.</li>
                <li>Dilarang keras membawa senjata tajam atau mainan berbahaya.</li>
                <li>Dilarang mengucapkan kata-kata kotor, mengejek, atau melakukan tindakan perundungan (bullying).</li>
            </ol>
            <p><em>(Pelanggaran terhadap tata tertib akan diberikan pembinaan edukatif, bukan hukuman fisik).</em></p>
        </div>
    </div>

    <!-- HALAMAN 14: Tata Tertib Panitia -->
    <div class="a4-paper">
        <div class="a4-page-number">14</div>
        <div class="a4-content">
            <h4>C. Tata Tertib Panitia</h4>
            <p>Profesionalisme panitia (Pengurus OSIM) adalah kunci keberhasilan acara. Panitia harus menjadi <em>role model</em> (teladan) bagi adik-adik kelasnya. Panitia WAJIB mematuhi:</p>
            <ol>
                <li><strong>Ramah & Senyum:</strong> Wajib menerapkan budaya 5S dalam melayani peserta. Dilarang menunjukkan wajah galak.</li>
                <li><strong>Teladan Disiplin:</strong> Hadir 30 menit lebih awal dari peserta.</li>
                <li><strong>Pakaian Rapi & Profesional:</strong> Mengenakan seragam pengurus OSIM atau Jas Almamater lengkap.</li>
                <li><strong>Anti Perploncoan:</strong> Dilarang keras melakukan perploncoan, pembentakan, kekerasan verbal, maupun kekerasan fisik sekecil apa pun.</li>
                <li><strong>No Hukuman Fisik:</strong> Apabila ada peserta yang melanggar, sanksi dialihkan ke sanksi edukatif (misal: hafalan surat pendek, menyanyi lagu nasional, atau membersihkan sampah). Dilarang menghukum push up/squat jump.</li>
                <li><strong>Bahas yang Sopan:</strong> Menggunakan bahasa Indonesia yang baik dan sopan. Dilarang berkata kasar.</li>
                <li><strong>Koordinasi:</strong> Jika terjadi insiden medis, segera laporkan kepada Pembina OSIM atau tim medis (PMR). Dilarang mengambil keputusan medis sepihak.</li>
            </ol>
            <div class="quote-box">"Panitia yang terbukti melanggar tata tertib ini, khususnya poin kekerasan/bullying, akan dicopot dari kepanitiaan dan diproses sesuai kode etik OSIM."</div>
        </div>
    </div>

    <!-- HALAMAN 15: BAB VII -->
    <div class="a4-paper">
        <div class="a4-page-number">15</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB VII<br>ANGGARAN & EVALUASI</h3>
            <h4>A. Rencana Anggaran Biaya (RAB)</h4>
            <p>Sumber dana kegiatan bersumber dari Dana BOS dan Kas OSIM.</p>
            <table>
                <thead>
                    <tr>
                        <th>Uraian Kebutuhan</th>
                        <th>Volume</th>
                        <th>Harga Satuan (Rp)</th>
                        <th>Jumlah Total (Rp)</th>
                    </tr>
                </thead>
                <tbody id="val-rab-list">
                    <tr><td><strong>A. Kesekretariatan</strong></td><td></td><td></td><td></td></tr>
                    <tr><td>Penggandaan Proposal & LPJ</td><td>3 bendel</td><td>20.000</td><td>60.000</td></tr>
                    <tr><td>Name Tag Peserta & Panitia</td><td>150 pcs</td><td>3.000</td><td>450.000</td></tr>
                    <tr><td>Sertifikat Peserta Terbaik</td><td>10 lbr</td><td>5.000</td><td>50.000</td></tr>
                    <tr><td>ATK (Spidol, Kertas, Lem)</td><td>1 paket</td><td>100.000</td><td>100.000</td></tr>
                    <tr><td><strong>B. Perlengkapan</strong></td><td></td><td></td><td></td></tr>
                    <tr><td>Spanduk MATAMUDA (4x1m)</td><td>1 buah</td><td>120.000</td><td>120.000</td></tr>
                    <tr><td>Sewa Sound System</td><td>1 paket</td><td>150.000</td><td>150.000</td></tr>
                    <tr><td>Properti Games Edukatif</td><td>1 paket</td><td>200.000</td><td>200.000</td></tr>
                    <tr><td><strong>C. Konsumsi & Medis</strong></td><td></td><td></td><td></td></tr>
                    <tr><td>Air Mineral Dus</td><td>10 dus</td><td>35.000</td><td>350.000</td></tr>
                    <tr><td>Snack Pemateri (3 Hari)</td><td>30 kotak</td><td>10.000</td><td>300.000</td></tr>
                    <tr><td>Snack Panitia (3 Hari)</td><td>90 kotak</td><td>8.000</td><td>720.000</td></tr>
                    <tr><td>P3K & Obat-obatan Dasar</td><td>1 paket</td><td>100.000</td><td>100.000</td></tr>
                    <tr><td colspan="3" style="text-align: right;"><strong>TOTAL</strong></td><td><strong id="val-rab-total">Rp 2.600.000</strong></td></tr>
                </tbody>
            </table>
            <p><em id="val-rab-terbilang">Terbilang: Dua Juta Enam Ratus Ribu Rupiah</em></p>
        </div>
    </div>

    <!-- HALAMAN 16: Lanjutan BAB VII -->
    <div class="a4-paper">
        <div class="a4-page-number">16</div>
        <div class="a4-content">
            <h4>B. Indikator Keberhasilan & Evaluasi</h4>
            <p>Sebagai bentuk pertanggungjawaban profesional, keberhasilan acara ini diukur dari indikator output dan outcome berikut:</p>
            <p><strong>1. Indikator Kuantitatif:</strong></p>
            <ul>
                <li><strong>Tingkat Kehadiran:</strong> Minimal 95% peserta didik baru hadir penuh selama 3 hari.</li>
                <li><strong>Kepatuhan Tata Tertib:</strong> 90% peserta mematuhi tata tertib tanpa pelanggaran berat.</li>
                <li><strong>Ketepatan Waktu:</strong> Acara berjalan sesuai rundown dengan toleransi keterlambatan maksimal 15 menit per sesi.</li>
            </ul>
            <p><strong>2. Indikator Kualitatif:</strong></p>
            <ul>
                <li><strong>Adaptasi:</strong> Peserta mampu menghafal nama wali kelas, letak toilet, letak masjid, dan ruang kelasnya pada hari terakhir.</li>
                <li><strong>Kedisiplinan:</strong> Peserta mampu berbaris dengan rapi dan mengembalikan sampah ke tempatnya.</li>
                <li><strong>Sinergi & Kerjasama:</strong> Terlihatnya interaksi positif antar peserta dari berbagai asal sekolah yang berbeda.</li>
                <li><strong>Zero Bullying:</strong> Tidak ada laporan tindakan perundungan atau tangisan ketakutan akibat tekanan dari panitia.</li>
                <li><strong>Kepuasan:</strong> Umpan balik positif dari orang tua wali murid yang merasa aman menitipkan anaknya.</li>
            </ul>
            <p><strong>Mekanisme Evaluasi:</strong></p>
            <ul>
                <li><strong>Briefing Pagi:</strong> Dilakukan panitia setiap pukul 06.30 untuk mengecek kesiapan.</li>
                <li><strong>Evaluasi Sore:</strong> Dilakukan setiap pukul 11.45 untuk membahas kendala hari itu dan solusi esok hari.</li>
                <li><strong>LPJ Resmi:</strong> Disusun maksimal 1 minggu setelah acara selesai oleh Sekretaris dan Bendahara Panitia.</li>
            </ul>
        </div>
    </div>

    <!-- HALAMAN 17: BAB VIII PENUTUP -->
    <div class="a4-paper">
        <div class="a4-page-number">17</div>
        <div class="a4-content">
            <h3 style="text-align: center;">BAB VIII<br>PENUTUP</h3>
            <p>Masa Ta'aruf Siswa Madrasah (<span class="val-judul-text">MATAMUDA</span>) adalah gerbang pertama dalam melukiskan kesan mendalam pada memori peserta didik baru. Kesan pertama yang baik, ramah, dan bernuansa Islami akan menjadi pondasi kuat bagi motivasi belajar mereka selama menempuh pendidikan 6 tahun ke depan.</p>
            <p>Proposal ini disusun tidak hanya sebagai pemenuhan syarat administratif, melainkan sebagai <em>grand design</em> agar setiap langkah, materi, dan anggaran yang dikeluarkan terukur serta tepat sasaran. Kami meyakini bahwa kolaborasi sinergis antara Kepala Madrasah, Dewan Guru, dan Pengurus OSIM adalah kunci kesuksesan terwujudnya kegiatan yang edukatif dan <em>zero bullying</em>.</p>
            <p>Akhir kata, kami memohon dukungan moril, materil, dan spiritual dari seluruh elemen madrasah. Semoga Allah SWT, Tuhan Yang Maha Esa, senantiasa meridhai niat baik kita, memberikan kelancaran dalam pelaksanaan, serta mencatat dedikasi ini sebagai amal jariyah di jalan pendidikan.</p>
            <p class="center-text" style="margin-top: 30px; font-style: italic;">Amin ya Rabbal 'Alamin.</p>
            
            <div style="text-align: center; border-top: 1px solid #ddd; margin-top: 100px; padding-top: 30px; font-style: italic; color: #555; font-size: 14pt;">
                "Pendidikan yang baik tidak hanya mengisi pikiran dengan pengetahuan, tetapi juga melembutkan hati dengan akhlak mulia."
            </div>
        </div>
    </div>

    <!-- HALAMAN 18: LAMPIRAN A -->
    <div class="a4-paper">
        <div class="a4-page-number">18</div>
        <div class="a4-content">
            <h3 style="text-align: center;">LAMPIRAN A<br>DENAH & PEMBAGIAN KELOMPOK</h3>
            <h4>1. Layout Denah Kegiatan Utama</h4>
            <p><em>(Dalam dokumen fisik, panitia menempelkan denah lokasi 2D di sini)</em></p>
            <ul>
                <li><strong>Zona A (Aula):</strong> Pemaparan materi terpusat.</li>
                <li><strong>Zona B (Lapangan):</strong> Upacara, Senam, dan Games Dinamika Kelompok.</li>
                <li><strong>Zona C (Masjid):</strong> Shalat Dhuha dan Materi Keagamaan.</li>
                <li><strong>Titik Kumpul Evakuasi:</strong> Teras depan ruang guru.</li>
            </ul>
            <h4>2. Skema Pembagian Kelompok Peserta</h4>
            <p>Untuk memudahkan pendampingan, peserta dibagi menjadi 6 kelompok kecil (masing-masing 20 anak), dengan nama kelompok diambil dari pahlawan Islam:</p>
            <ul>
                <li>Kelompok 1: <strong>Abu Bakar</strong> (Pendamping: Kak Aris & Kak Salma)</li>
                <li>Kelompok 2: <strong>Umar bin Khattab</strong> (Pendamping: Kak Doni & Kak Dinda)</li>
                <li>Kelompok 3: <strong>Utsman bin Affan</strong> (Pendamping: Kak Yoga & Kak Rini)</li>
                <li>Kelompok 4: <strong>Ali bin Abi Thalib</strong> (Pendamping: Kak Dimas & Kak Fitri)</li>
                <li>Kelompok 5: <strong>Khadijah</strong> (Pendamping: Kak Bayu & Kak Nisa)</li>
                <li>Kelompok 6: <strong>Aisyah</strong> (Pendamping: Kak Iqbal & Kak Laila)</li>
            </ul>
        </div>
    </div>

    <!-- HALAMAN 19: LAMPIRAN B DEKLARASI -->
    <div class="a4-paper">
        <div class="a4-page-number">19</div>
        <div class="a4-content">
            <h3 style="text-align: center;">LAMPIRAN B<br>TEKS DEKLARASI SANTRI BARU</h3>
            <p style="text-align: center; font-style: italic;">(Akan dibacakan secara serentak oleh seluruh peserta pada Upacara Penutupan MATAMUDA)</p>
            
            <div style="border: 2px solid #000; padding: 20px; margin-top: 20px; text-align: justify; line-height: 1.6;">
                <h2 style="text-align: center; margin-top: 0; font-weight: bold;">DEKLARASI SANTRI<br><?= htmlspecialchars(strtoupper($settings['nama_madrasah'] ?? 'MAS MIFTAHUL HUDA')) ?></h2>
                <p style="text-align: center; font-weight: bold;">Bismillahirrahmannirrahim.</p>
                <p>Kami, Murid Baru <?= htmlspecialchars($settings['nama_madrasah'] ?? 'MAS Miftahul Huda') ?>, dengan kesadaran dan ketulusan hati, berjanji dan berkomitmen:</p>
                <ol style="margin-left: 20px;">
                    <li style="margin-bottom: 8px;"><strong>Menjaga Keimanan:</strong> Senantiasa menjalankan ibadah, mendirikan shalat, dan rajin mengaji.</li>
                    <li style="margin-bottom: 8px;"><strong>Menghormati Guru:</strong> Taat, patuh, dan menjaga adab yang baik kepada Bapak/Ibu Guru.</li>
                    <li style="margin-bottom: 8px;"><strong>Menyayangi Sesama:</strong> Menjadi teman yang baik, menolong yang kesulitan, dan menolak segala bentuk perundungan (bullying).</li>
                    <li style="margin-bottom: 8px;"><strong>Disiplin & Tanggung Jawab:</strong> Hadir tepat waktu, menjaga kebersihan, dan merawat fasilitas madrasah.</li>
                    <li style="margin-bottom: 8px;"><strong>Berprestasi:</strong> Belajar dengan sungguh-sungguh untuk meraih cita-cita dan membanggakan orang tua, bangsa, dan agama.</li>
                </ol>
                <p style="text-align: center; margin-top: 20px;">Semoga Allah SWT senantiasa memberikan kekuatan dan kemudahan kepada kami.</p>
            </div>
        </div>
    </div>

    <!-- HALAMAN 20: PENUTUP -->
    <div class="a4-paper">
        <div class="a4-page-number">20</div>
        <div class="a4-content" style="display: flex; flex-direction: column; justify-content: center; height: 100%; align-items: center; text-align: center;">
            <h1 style="font-size: 36pt; font-weight: bold; margin-bottom: 20px;" class="val-judul">MATAMUDA</h1>
            <h2 style="font-size: 20pt; margin-bottom: 50px;">TAHUN <?= date('Y') ?></h2>
            
            <div style="max-width: 80%; padding: 40px; border: 4px double #000; font-style: italic; font-size: 16pt; line-height: 1.8;">
                "Bukan sekadar kegiatan pengenalan, tetapi langkah pertama membentuk generasi madrasah yang beriman, berilmu, berkarakter, dan siap menghadapi masa depan."
            </div>
            
            <div style="margin-top: 100px;">
                <img src="<?= base_url('public/uploads/logo/logo_1781854511.png') ?>" width="80" alt="Logo" onerror="this.style.display='none'">
                <p style="margin-top: 20px; font-weight: bold; font-size: 14pt;">OSIM <?= htmlspecialchars(strtoupper($settings['nama_madrasah'] ?? 'MAS MIFTAHUL HUDA')) ?></p>
            </div>
        </div>
    </div>

</div>

<script>
    // Live Binding JavaScript for all elements with specific classes
    document.getElementById('inputJudul').addEventListener('input', function() {
        let val = this.value.toUpperCase();
        let valText = this.value.toUpperCase();
        
        if(val.includes('MATAMUDA')) {
            val = val.replace('MATAMUDA', '<br>(MATAMUDA)');
        }
        
        document.querySelectorAll('.val-judul').forEach(el => el.innerHTML = val || 'JUDUL KEGIATAN');
        document.querySelectorAll('.val-judul-text').forEach(el => el.innerText = valText || 'JUDUL KEGIATAN');
    });

    document.getElementById('inputTema').addEventListener('input', function() {
        let val = this.value || 'TEMA KEGIATAN';
        document.querySelectorAll('.val-tema').forEach(el => el.innerText = val);
    });

    document.getElementById('inputWaktu').addEventListener('input', function() {
        let val = this.value || 'Waktu dan Tempat Pelaksanaan';
        document.querySelectorAll('.val-waktu').forEach(el => el.innerText = val);
    });

    document.getElementById('inputPanitia').addEventListener('change', function() {
        let val = this.value || 'Nama Ketua Panitia';
        document.querySelectorAll('.val-panitia').forEach(el => el.innerText = val);
    });

    // === NEW LOGIC: Tabs & Dynamic Binding ===
    
    // Tabs Logic
    document.querySelectorAll('.editor-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.editor-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    // Format Rupiah & Terbilang
    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }
    
    const angkaToTerbilang = (angka) => {
        const bilangan = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if(angka < 12) return bilangan[angka];
        if(angka < 20) return angkaToTerbilang(angka-10) + ' Belas';
        if(angka < 100) return angkaToTerbilang(Math.floor(angka/10)) + ' Puluh ' + (angka%10 > 0 ? angkaToTerbilang(angka%10) : '');
        if(angka < 200) return 'Seratus ' + (angka-100 > 0 ? angkaToTerbilang(angka-100) : '');
        if(angka < 1000) return angkaToTerbilang(Math.floor(angka/100)) + ' Ratus ' + (angka%100 > 0 ? angkaToTerbilang(angka%100) : '');
        if(angka < 2000) return 'Seribu ' + (angka-1000 > 0 ? angkaToTerbilang(angka-1000) : '');
        if(angka < 1000000) return angkaToTerbilang(Math.floor(angka/1000)) + ' Ribu ' + (angka%1000 > 0 ? angkaToTerbilang(angka%1000) : '');
        if(angka < 1000000000) return angkaToTerbilang(Math.floor(angka/1000000)) + ' Juta ' + (angka%1000000 > 0 ? angkaToTerbilang(angka%1000000) : '');
        return '';
    };

    // Panitia Logic
    window.addSeksiRow = function() {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = `
            <input type="text" class="input-seksi-nama" placeholder="Nama Seksi" style="flex:1;" oninput="renderPanitia()">
            <input type="text" class="input-seksi-anggota" placeholder="Anggota" style="flex:2;" oninput="renderPanitia()">
            <button class="btn-remove" onclick="this.parentElement.remove(); renderPanitia();">&times;</button>
        `;
        document.getElementById('container-seksi').appendChild(div);
    };

    window.renderPanitia = function() {
        let html = '';
        document.querySelectorAll('#container-seksi .dynamic-row').forEach(row => {
            const nama = row.querySelector('.input-seksi-nama').value;
            const anggotaStr = row.querySelector('.input-seksi-anggota').value;
            if(nama) {
                let anggotas = anggotaStr.split(',').map((v, i) => `${i+1}. ${v.trim()}`).filter(v => v !== `${i+1}. `).join(' | ');
                html += `<li><strong>${nama}:</strong> ${anggotas}</li>`;
            }
        });
        document.getElementById('val-panitia-list').innerHTML = html || '<li><em>Belum ada seksi yang ditambahkan.</em></li>';
    };
    
    document.querySelectorAll('.input-seksi-nama, .input-seksi-anggota').forEach(el => el.addEventListener('input', renderPanitia));

    // Jadwal Logic
    window.addJadwalRow = function() {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = `
            <input type="text" class="input-jadwal-hari" placeholder="Hari/Tanggal" style="flex:1;" oninput="renderJadwal()">
            <input type="text" class="input-jadwal-jam" placeholder="Jam" style="flex:1;" oninput="renderJadwal()">
            <input type="text" class="input-jadwal-kegiatan" placeholder="Kegiatan" style="flex:2;" oninput="renderJadwal()">
            <button class="btn-remove" onclick="this.parentElement.remove(); renderJadwal();">&times;</button>
        `;
        document.getElementById('container-jadwal').appendChild(div);
    };

    window.renderJadwal = function() {
        let html = '';
        document.querySelectorAll('#container-jadwal .dynamic-row').forEach(row => {
            const hari = row.querySelector('.input-jadwal-hari').value;
            const jam = row.querySelector('.input-jadwal-jam').value;
            const kegiatan = row.querySelector('.input-jadwal-kegiatan').value;
            if(hari || jam || kegiatan) {
                html += `<tr><td>${hari}</td><td>${jam}</td><td>${kegiatan}</td></tr>`;
            }
        });
        document.getElementById('val-jadwal-list').innerHTML = html || '<tr><td colspan="3" style="text-align:center">Belum ada jadwal</td></tr>';
    };

    document.querySelectorAll('.input-jadwal-hari, .input-jadwal-jam, .input-jadwal-kegiatan').forEach(el => el.addEventListener('input', renderJadwal));

    // RAB Logic
    window.addRABRow = function() {
        const div = document.createElement('div');
        div.className = 'dynamic-row';
        div.innerHTML = `
            <input type="text" class="input-rab-uraian" placeholder="Uraian" style="flex:2;" oninput="renderRAB()">
            <input type="number" class="input-rab-vol" placeholder="Vol" style="width: 80px;" oninput="renderRAB()">
            <input type="text" class="input-rab-satuan" placeholder="Satuan" style="width: 100px;" oninput="renderRAB()">
            <input type="number" class="input-rab-harga" placeholder="Harga Sat" style="flex:1;" oninput="renderRAB()">
            <button class="btn-remove" onclick="this.parentElement.remove(); renderRAB();">&times;</button>
        `;
        document.getElementById('container-rab').appendChild(div);
    };

    window.renderRAB = function() {
        let html = '';
        let totalAll = 0;
        document.querySelectorAll('#container-rab .dynamic-row').forEach(row => {
            const uraian = row.querySelector('.input-rab-uraian').value;
            const vol = parseFloat(row.querySelector('.input-rab-vol').value) || 0;
            const satuan = row.querySelector('.input-rab-satuan').value;
            const harga = parseFloat(row.querySelector('.input-rab-harga').value) || 0;
            
            if(uraian) {
                const totalRow = vol * harga;
                totalAll += totalRow;
                html += `<tr><td>${uraian}</td><td style="text-align:center;">${vol} ${satuan}</td><td>${formatRupiah(harga)}</td><td>${formatRupiah(totalRow)}</td></tr>`;
            }
        });
        document.getElementById('val-rab-list').innerHTML = html || '<tr><td colspan="4" style="text-align:center">Belum ada rincian anggaran</td></tr>';
        document.getElementById('val-rab-total').innerText = formatRupiah(totalAll);
        
        let textTerbilang = angkaToTerbilang(totalAll).trim() + " Rupiah";
        if(totalAll === 0) textTerbilang = "Nol Rupiah";
        document.getElementById('val-rab-terbilang').innerHTML = `Terbilang: <em>${textTerbilang}</em>`;
    };

    document.querySelectorAll('.input-rab-uraian, .input-rab-satuan').forEach(el => el.addEventListener('input', renderRAB));

    // Initialize default rendering on load
    setTimeout(() => {
        renderPanitia();
        renderJadwal();
        renderRAB();
    }, 100);

    // === NEW LOGIC: Save Draft to Database ===
    document.getElementById('btnSimpanDraft').addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="lucide-loader"></i> Menyimpan...';
        btn.disabled = true;

        try {
            // Gather Panitia
            let panitia = [];
            document.querySelectorAll('#container-seksi .dynamic-row').forEach(row => {
                const nama = row.querySelector('.input-seksi-nama').value;
                const anggota = row.querySelector('.input-seksi-anggota').value;
                if(nama) panitia.push({ nama, anggota });
            });

            // Gather Jadwal
            let jadwal = [];
            document.querySelectorAll('#container-jadwal .dynamic-row').forEach(row => {
                const hari = row.querySelector('.input-jadwal-hari').value;
                const jam = row.querySelector('.input-jadwal-jam').value;
                const kegiatan = row.querySelector('.input-jadwal-kegiatan').value;
                if(hari || jam || kegiatan) jadwal.push({ hari, jam, kegiatan });
            });

            // Gather RAB
            let rab = [];
            document.querySelectorAll('#container-rab .dynamic-row').forEach(row => {
                const uraian = row.querySelector('.input-rab-uraian').value;
                const vol = row.querySelector('.input-rab-vol').value;
                const satuan = row.querySelector('.input-rab-satuan').value;
                const harga = row.querySelector('.input-rab-harga').value;
                if(uraian) rab.push({ uraian, vol, satuan, harga });
            });

            // Create Form Data
            const formData = new URLSearchParams();
            formData.append('judul', document.getElementById('inputJudul').value);
            formData.append('tujuan', document.getElementById('inputTema').value); // Using Tema for Tujuan temporarily
            formData.append('waktu_tempat', document.getElementById('inputWaktu').value);
            formData.append('panitia_json', JSON.stringify(panitia));
            formData.append('jadwal_json', JSON.stringify(jadwal));
            formData.append('rab_json', JSON.stringify(rab));
            formData.append('status', 'draft');

            const response = await fetch('<?= base_url('osim/proposal/simpan') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '<?= base_url('osim/proposal') ?>';
                });
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
