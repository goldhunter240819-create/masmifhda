<?php

// Data Master Instrumen PKKM Terbaru (42 Indikator sesuai PDF Petunjuk Pengisian Penilaian Kinerja Tahun 1, 2, dan 3 Kepala Madrasah)

$komponen = [
    ['id' => 1, 'nama' => 'Usaha Pengembangan Madrasah', 'urutan' => 1],
    ['id' => 2, 'nama' => 'Pelaksanaan Tugas Manajerial', 'urutan' => 2],
    ['id' => 3, 'nama' => 'Pengembangan Kewirausahaan', 'urutan' => 3],
    ['id' => 4, 'nama' => 'Supervisi Kepada Guru dan Tenaga Kependidikan', 'urutan' => 4],
];

$sub_komponen = [
    // Komponen 1 (6 Sub)
    ['id' => 1, 'komp_id' => 1, 'nama' => 'Mengembangkan Madrasah sesuai dengan kebutuhan', 'urutan' => '1.1'],
    ['id' => 2, 'komp_id' => 1, 'nama' => 'Mengelola perubahan dan pengembangan Madrasah menuju organisasi pembelajar yang efektif', 'urutan' => '1.2'],
    ['id' => 3, 'komp_id' => 1, 'nama' => 'Mengelola hubungan Madrasah, orangtua dan masyarakat dalam rangka pencarian dukungan ide, sumber belajar, dan pembiayaan', 'urutan' => '1.3'],
    ['id' => 4, 'komp_id' => 1, 'nama' => 'Mengelola proses pencapaian 8 SNP sesuai dengan arah dan tujuan pendidikan nasional', 'urutan' => '1.4'],
    ['id' => 5, 'komp_id' => 1, 'nama' => 'Memanfaatkan kemajuan teknologi informasi bagi peningkatan pembelajaran dan manajemen Madrasah', 'urutan' => '1.5'],
    ['id' => 6, 'komp_id' => 1, 'nama' => 'Mengelola system informasi Madrasah dalam mendukung penyusunan program dan pengambilan keputusan', 'urutan' => '1.6'],

    // Komponen 2 (10 Sub)
    ['id' => 7, 'komp_id' => 2, 'nama' => 'Menyusun perencanaan Madrasah untuk berbagai tingkatan perencanaan', 'urutan' => '2.1'],
    ['id' => 8, 'komp_id' => 2, 'nama' => 'Memimpin Madrasah dalam rangka pendayagunaan sumber daya Madrasah secara optimal', 'urutan' => '2.2'],
    ['id' => 9, 'komp_id' => 2, 'nama' => 'Menciptakan budaya dan iklim Madrasah yang kondusif dan inovatif bagi pembelajaran peserta didik', 'urutan' => '2.3'],
    ['id' => 10, 'komp_id' => 2, 'nama' => 'Mengelola guru dan staf dalam rangka pendayagunaan sumber daya manusia secara optimal', 'urutan' => '2.4'],
    ['id' => 11, 'komp_id' => 2, 'nama' => 'Mengelola sarana dan prasarana Madrasah dalam rangka pendayagunaan secara optimal', 'urutan' => '2.5'],
    ['id' => 12, 'komp_id' => 2, 'nama' => 'Mengelola peserta didik dalam rangka penerimaan peserta didik baru, dan penempatan serta pengembangan kapasitas peserta didik', 'urutan' => '2.6'],
    ['id' => 13, 'komp_id' => 2, 'nama' => 'Mengelola pengembangan kurikulum dan kegiatan pembelajaran sesuai dengan arah dan tujuan pendidikan nasional', 'urutan' => '2.7'],
    ['id' => 14, 'komp_id' => 2, 'nama' => 'Mengelola keuangan Madrasah sesuai prinsip pengelolaan yang akuntabel, transparan, dan efisien', 'urutan' => '2.8'],
    ['id' => 15, 'komp_id' => 2, 'nama' => 'Mengelola ketatausahaan Madrasah dalam mendukung pencapaian tujuan Madrasah', 'urutan' => '2.9'],
    ['id' => 16, 'komp_id' => 2, 'nama' => 'Melakukan monitoring, evaluasi, dan pelaporan pelaksanaan program kegiatan dengan prosedur yang tepat, serta merencanakan tindak lanjutnya', 'urutan' => '2.10'],

    // Komponen 3 (2 Sub)
    ['id' => 17, 'komp_id' => 3, 'nama' => 'Menciptakan inovasi yang bermanfaat dan tepat bagi pengembangan Madrasah', 'urutan' => '3.1'],
    ['id' => 18, 'komp_id' => 3, 'nama' => 'Memiliki motivasi yang kuat untuk sukses dalam melaksanakan tugas pokok dan fungsinya sebagai pemimpin', 'urutan' => '3.2'],

    // Komponen 4 (3 Sub)
    ['id' => 19, 'komp_id' => 4, 'nama' => 'Menyusun program supervisi dalam rangka peningkatan profesionalisme guru dan tenaga kependidikan', 'urutan' => '4.1'],
    ['id' => 20, 'komp_id' => 4, 'nama' => 'Melaksanakan supervisi terhadap guru dan tenaga kependidikan dengan menggunakan pendekatan dan teknik supervisi yang tepat', 'urutan' => '4.2'],
    ['id' => 21, 'komp_id' => 4, 'nama' => 'Menindak lanjuti kegiatan supervisi dalam rangka peningkatan profesionalisme guru dan tenaga kependidikan', 'urutan' => '4.3'],
];

$indikator = [
    // --- KOMPONEN 1 (14 Indikator) ---
    // Sub 1.1
    [
        'komp_id' => 1, 'sub_id' => 1, 'urutan' => 1,
        'indikator' => 'Mampu mengembangkan struktur organisasi yang sesuai dengan kebutuhan program',
        'kriteria' => 'Dokumen struktur organisasi Madrasah meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Bagan / struktur organisasi\n- b. Notulen rapat penyusunan struktur organisasi\n- c. Dokumen sosialisasi ke warga Madrasah\n- d. Daftar hadir rapat penyusunan struktur organisasi\n- e. Dokumen penetapan / pengesahan susunan organisasi"
    ],
    [
        'komp_id' => 1, 'sub_id' => 1, 'urutan' => 2,
        'indikator' => 'Mampu menempatkan personalia yang sesuai dengan kebutuhan',
        'kriteria' => 'Dokumen uraian tugas personalia pada struktur organisasi Madrasah',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Dokumen uraian tugas personalia pada struktur organisasi Madrasah\n- b. SK Pembagian tugas dan fungsi masing-masing personalia"
    ],

    // Sub 1.2
    [
        'komp_id' => 1, 'sub_id' => 2, 'urutan' => 1,
        'indikator' => 'Mampu mengembangkan program baru untuk meningkatkan pencapaian target yang lebih tinggi',
        'kriteria' => 'Program pengembangan Madrasah berorientasi pada pencapaian indikator keunggulan khas satuan pendidikan, dan kerja sama tim, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Visi, misi dan tujuan Madrasah\n- b. Program unggulan Madrasah\n- c. Data alumni"
    ],
    [
        'komp_id' => 1, 'sub_id' => 2, 'urutan' => 2,
        'indikator' => 'Mampu mengembangkan potensi dan meningkatkan prestasi Madrasah',
        'kriteria' => 'Dokumen bukti perkembangan potensi dan prestasi peserta didik, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Data prestasi Madrasah 2 tahun terakhir\n- b. Program ekstrakurikuler\n- c. Prestasi non akademik (pembiasaan-pembiasaan positif)"
    ],

    // Sub 1.3
    [
        'komp_id' => 1, 'sub_id' => 3, 'urutan' => 1,
        'indikator' => 'Merencanakan kerjasama dengan orang tua, masyarakat, Pemerintah dan Swasta',
        'kriteria' => 'Data kerjasama dengan komite Madrasah, masyarakat, pemerintah dan swasta, berupa:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. SK Komite Madrasah\n- b. Dokumen MOU dengan Komite/Pemerintah/Dunia Usaha/Pendidikan\n- c. Laporan Program kegiatan dengan Komite/Pemerintah/Dunia Usaha/Pendidikan\n- d. Dokumentasi Kegiatan"
    ],
    [
        'komp_id' => 1, 'sub_id' => 3, 'urutan' => 2,
        'indikator' => 'Melakukan pendekatan-pendekatan dalam rangka mendapatkan dukungan dari lembaga pemerintah, swasta, dan Masyarakat',
        'kriteria' => 'Terdapat dokumen Pelaksanaan sosialisasi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Program Kerja\n- b. Undangan, notulen dan daftar hadir"
    ],
    [
        'komp_id' => 1, 'sub_id' => 3, 'urutan' => 3,
        'indikator' => 'Memanfaatkan dan memelihara hubungan kerjasama dengan lembaga pemerintah, swasta, dan Masyarakat',
        'kriteria' => 'Data hasil kerjasama meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Laporan kegiatan\n- b. Foto-foto kegiatan"
    ],

    // Sub 1.4
    [
        'komp_id' => 1, 'sub_id' => 4, 'urutan' => 1,
        'indikator' => 'Mengaplikasikan pengembangan kurikulum yang mengacu kepada standar isi',
        'kriteria' => 'Dokumen analisis standar isi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen Kurikulum Madrasah\n- b. Dokumen hasil sosialisasi kurikulum (Undangan, dafdir, notulen, dokumentasi)"
    ],
    [
        'komp_id' => 1, 'sub_id' => 4, 'urutan' => 2,
        'indikator' => 'Mengaplikasikan pengembangan proses pembelajaran yang mengacu kepada standar proses',
        'kriteria' => 'Dokumen analisis standar proses, memiliki perangkat pembelajaran meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Program Tahunan/Semester / RPE\n- b. RPP / Modul\n- c. Pembagian jam mengajar/Jadwal\n- d. Media pembelajaran\n- e. Buku Panduan / Bahan Ajar\n- f. Data layanan anak berkebutuhan khusus\n- g. Penilaian"
    ],
    [
        'komp_id' => 1, 'sub_id' => 4, 'urutan' => 3,
        'indikator' => 'Memanfaatkan teknologi informasi dan komunikasi dalam pembelajaran, baik sebagai sumber belajar maupun sebagai alat / media pembelajaran',
        'kriteria' => 'Adanya bukti pemanfaatan teknologi informasi dan komunikasi sebagai sumber belajar dan media pembelajaran meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Ketersediaan computer/Laptop\n- b. Jaringan internet\n- c. Pemanfaatan medsos (WA, FB, Instagram, Tiktok dll)\n- d. Alamat email Madrasah\n- e. Pemanfaatan TIK untuk pembelajaran"
    ],
    [
        'komp_id' => 1, 'sub_id' => 4, 'urutan' => 4,
        'indikator' => 'Memanfaatkan teknologi informasi dan komunikasi dalam menjalin kerjasama dengan pihak lain',
        'kriteria' => 'Adanya bukti pemanfaatan teknologi informasi dan komunikasi dalam menjalin kerja sama dengan pihak lain',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. SK dan deskripsi tugas operator Madrasah"
    ],

    // Sub 1.5
    [
        'komp_id' => 1, 'sub_id' => 5, 'urutan' => 1,
        'indikator' => 'Mampu mengembangkan sistem administrasi pengelolaan secara efektif dengan dukungan penerapan teknologi informasi dan komunikasi',
        'kriteria' => 'Terdapat penerapan TIK dalam pengelolaan administrasi Madrasah, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Laporan absensi elektronik\n- b. Dokumen lain pemanfaatan TIK untuk administrasi Madrasah"
    ],
    [
        'komp_id' => 1, 'sub_id' => 5, 'urutan' => 2,
        'indikator' => 'Mengelola administrasi pembelajaran secara efektif dengan dukungan penerapan teknologi informasi dan komunikasi',
        'kriteria' => 'Model penerapan TIK dalam pengelolaan administrasi kurikulum dan pembelajaran dalam:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Penyusunan RPP/Modul berbasis TIK\n- b. Pemanfaatan TIK untuk pembelajaran"
    ],

    // Sub 1.6
    [
        'komp_id' => 1, 'sub_id' => 6, 'urutan' => 1,
        'indikator' => 'Memanfaatkan teknologi informasi dan komunikasi dalam manajemen Madrasah',
        'kriteria' => 'Adanya bukti pemanfaatan teknologi informasi dan komunikasi dalam manajemen Madrasah, berupa:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Jaringan internet\n- b. Webset / Email Madrasah"
    ],

    // --- KOMPONEN 2 (21 Indikator) ---
    // Sub 2.1
    [
        'komp_id' => 2, 'sub_id' => 7, 'urutan' => 1,
        'indikator' => 'Mampu mengembangkan RKM, RKTM dengan program lainnya berdasarkan data hasil evaluasi dalam pemenuhan 8 SNP',
        'kriteria' => 'Dokumen perencanaan Madrasah berbasis data evaluasi pemenuhan 8 SNP:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Dokumen Evaluasi Diri (EDM)\n- b. Raport Pendidikan\n- c. Dokumen RKM dan RKTM berbasis Data\n- d. Program evaluasi dan Monitoring"
    ],
    [
        'komp_id' => 2, 'sub_id' => 7, 'urutan' => 2,
        'indikator' => 'Mampu menyosialisasikan visi misi Madrasah sebagai arah pengembangan program RKM, RKTM dan program lainnya',
        'kriteria' => 'Dokumen sosialisasi visi misi dan tujuan lembaga:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen 1 KTSP\n- b. Dokumen hasil Sosialisasi Visi Misi dan Tujuan Lembaga\n- c. Berita acara, daftar hadir kegiatan dan dokumentasi"
    ],

    // Sub 2.2
    [
        'komp_id' => 2, 'sub_id' => 8, 'urutan' => 1,
        'indikator' => 'Mampu melaksanakan peraturan sesuai dengan ketentuan yang berlaku',
        'kriteria' => 'Menjadi contoh dan mengarahkan guru, staf administrasi, dan peserta didik melaksanakan peraturan:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen regulasi/aturan\n- b. SOP administrasi Kepala\n- c. Notulen rapat / briefing\n- d. Pengarahan kegiatan"
    ],
    [
        'komp_id' => 2, 'sub_id' => 8, 'urutan' => 2,
        'indikator' => 'Mampu menunjukkan kedisiplinan sebagai insan pembelajaran',
        'kriteria' => 'Rajin membaca dan pendengar yang baik, mengekspresikan pikiran secara tertulis, dibuktikan dengan adanya:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Data Koleksi buku referensi / ilmu pengetahuan\n- b. Karya tulis yang diterbitkan atau tidak\n- c. Menyusun panduan pembelajaran secara mandiri"
    ],

    // Sub 2.3
    [
        'komp_id' => 2, 'sub_id' => 9, 'urutan' => 1,
        'indikator' => 'Mampu menjadi contoh dan berbudaya mutu yang kompetitif dalam mendorong peningkatan prestasi akademik dan non akademik',
        'kriteria' => 'Pengembangan Inovasi Pembelajaran yang kreatif dan inovatif, dibuktikan dengan adanya:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen prestasi kepala"
    ],
    [
        'komp_id' => 2, 'sub_id' => 9, 'urutan' => 2,
        'indikator' => 'Mampu melengkapi sarana dan prasarana untuk menciptakan suasana belajar kondusif dan aman bagi peserta didik',
        'kriteria' => 'Dokumentasi Kondisi real lingkungan Madrasah:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Suasana lingkungan yang asri, bersih, rindang, aman, dan menyenangkan bagi peserta didik"
    ],
    [
        'komp_id' => 2, 'sub_id' => 9, 'urutan' => 3,
        'indikator' => 'Mampu memfasilitasi kegiatan-kegiatan lomba di bidang akademik dan non akademik bagi peserta didik',
        'kriteria' => 'Dokumen penyelenggaraan dan keikutsertaan kompetisi peserta didik:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen penyelenggaraan kegiatan kompetisi yang dimulai dari tingkat Madrasah\n- b. Daftar perolehan piagam penghargaan dan piala kejuaraan"
    ],

    // Sub 2.4
    [
        'komp_id' => 2, 'sub_id' => 10, 'urutan' => 1,
        'indikator' => 'Mampu menyusun perencanaan pengembangan pendidik dan tenaga kependidikan',
        'kriteria' => 'Dokumen program pembinaan pendidik dan tenaga kependidikan di Madrasah, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Program kerja peningkatan kompetensi Pendidik dan Tendik\n- b. Laporan kegiatan workshop, pelatihan kompetensi guru\n- c. Dokumentasi (foto / video) kegiatan workshop, pelatihan guru"
    ],
    [
        'komp_id' => 2, 'sub_id' => 10, 'urutan' => 2,
        'indikator' => 'Mampu melakukan pembinaan berkala untuk guru dan staf',
        'kriteria' => 'Laporan pelaksanaan pembinaan guru berkala:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen laporan pelaksanaan hasil pengembangan SDM Guru guru"
    ],
    [
        'komp_id' => 2, 'sub_id' => 10, 'urutan' => 3,
        'indikator' => 'Memantau dan menilai penerapan hasil pelatihan',
        'kriteria' => 'Dokumen laporan pelaksanaan evaluasi pelatihan:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen laporan pelaksanaan pelatihan"
    ],

    // Sub 2.5
    [
        'komp_id' => 2, 'sub_id' => 11, 'urutan' => 1,
        'indikator' => 'Mampu mengelola fasilitas sarana prasarana, (perabot, gedung, bangunan, lahan, meja, kursi, lemari, laboratorium, dll)',
        'kriteria' => 'Data fasilitas sarana prasarana, perabot, dan sarana dikelola dengan baik, dalam bentuk:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Buku inventaris\n- b. Buku pemeliharaan sarana dan prasarana"
    ],
    [
        'komp_id' => 2, 'sub_id' => 11, 'urutan' => 2,
        'indikator' => 'Mampu mengelola perpustakaan Madrasah',
        'kriteria' => 'Data perpustakaan dikelola dengan baik, dibuktikan dengan adanya:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Buku induk perpustakaan\n- b. Buku / data / grafik layanan perpustakaan"
    ],

    // Sub 2.6
    [
        'komp_id' => 2, 'sub_id' => 12, 'urutan' => 1,
        'indikator' => 'Menyusun perencanaan penerimaan, pengelolaan, dan data tumbuh kembang peserta didik',
        'kriteria' => 'Dokumen perencanaan penerimaan dan pengelolaan peserta didik baru:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen buku penerimaan peserta didik\n- b. Brosur, SOP Penerimaan Peserta Didik baru"
    ],
    [
        'komp_id' => 2, 'sub_id' => 12, 'urutan' => 2,
        'indikator' => 'Memiliki program pengembangan potensi diri dan prestasi peserta didik',
        'kriteria' => 'Program pengembangan minat dan bakat peserta didik:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Program pengembangan minat dan bakat peserta didik (Kegiatan Extrakurikuler)\n- b. Buku kehadiran Guru/pembina Extrakurikuler"
    ],

    // Sub 2.7
    [
        'komp_id' => 2, 'sub_id' => 13, 'urutan' => 1,
        'indikator' => 'Mampu mengarahkan secara efektif dalam menerapkan prinsip-prinsip pengembangan kurikulum yang berlaku melalui Rapat Koordinasi dan Kegiatan Pembinaan dengan Pengawas',
        'kriteria' => 'Dokumen penerapan prinsip pengembangan kurikulum yang berlaku:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen kurikulum yang berlaku\n- b. SK Tim Pengembang Kurikulum\n- c. Dokumen penyusunan dokumen kurikulum (daftar hadir, berita acara, notulen)"
    ],
    [
        'komp_id' => 2, 'sub_id' => 13, 'urutan' => 2,
        'indikator' => 'Memfasilitasi efektivitas tim kerja guru dalam rangka meningkatkan mutu pembelajaran',
        'kriteria' => 'Bukti pelaksanaan kerja sama guru pada tingkat satuan pendidikan, antar satuan Pendidikan dll:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Program KKG\n- b. Laporan pelaksanaan KKG\n- c. Dokumentasi kegiatan (foto / video)"
    ],

    // Sub 2.8
    [
        'komp_id' => 2, 'sub_id' => 14, 'urutan' => 1,
        'indikator' => 'Mampu merencanakan kebutuhan keuangan Madrasah sesuai dengan rencana',
        'kriteria' => 'Dokumen perencanaan kebutuhan keuangan Madrasah sesuai rencana:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. RKJM dan RKTM\n- b. Rencana anggaran"
    ],
    [
        'komp_id' => 2, 'sub_id' => 14, 'urutan' => 2,
        'indikator' => 'Mampu membuat laporan dan evaluasi pengelolaan keuangan',
        'kriteria' => 'Laporan dan evaluasi pengelolaan keuangan:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. LPJ (Laporan Pertanggungjawaban)"
    ],

    // Sub 2.9
    [
        'komp_id' => 2, 'sub_id' => 15, 'urutan' => 1,
        'indikator' => 'Mampu mengelola administrasi surat masuk dan surat keluar sesuai dengan pedoman persuratan yang berlaku',
        'kriteria' => 'Pengelolaan administrasi surat masuk dan surat keluar sesuai aturan:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen dan Wawancara\n- a. Adanya bukti dokumen surat masuk dan keluar"
    ],
    [
        'komp_id' => 2, 'sub_id' => 15, 'urutan' => 2,
        'indikator' => 'Mampu mengelola administrasi yang meliputi administrasi akademik, kesiswaan, sarana prasarana, keuangan',
        'kriteria' => 'Adanya bukti dokumen administrasi, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen akademik\n- b. Dokumen kesiswaan\n- c. Dokumen sarana prasarana"
    ],

    // Sub 2.10
    [
        'komp_id' => 2, 'sub_id' => 16, 'urutan' => 1,
        'indikator' => 'Melakukan monitoring, evaluasi, dan pelaporan pelaksanaan program kegiatan dengan prosedur yang tepat, serta merencanakan tindak lanjutnya',
        'kriteria' => 'Dokumen program monitoring, evaluasi dan tindak lanjut:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Dokumen program monitoring, evaluasi dan laporan program tahunan\n- b. Dokumen EDM"
    ],

    // --- KOMPONEN 3 (4 Indikator) ---
    // Sub 3.1
    [
        'komp_id' => 3, 'sub_id' => 17, 'urutan' => 1,
        'indikator' => 'Memahami dan menghayati arti dan tujuan perubahan (inovasi)',
        'kriteria' => 'Adanya bukti perubahan yang lebih baik dari tahun ke tahun, meliputi:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara\n- a. Program pengembangan inovasi dari tahun ke tahun\n- b. Profil Madrasah dua tahun terakhir"
    ],
    [
        'komp_id' => 3, 'sub_id' => 17, 'urutan' => 2,
        'indikator' => 'Menumbuhkan iklim yang mendorong inovasi',
        'kriteria' => 'Adanya bukti inovasi pengembangan Madrasah yang sudah dilakukan meliputi 8 SNP:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Dokumen inovasi pengembangan Madrasah yang sudah dilakukan meliputi 8 SNP"
    ],

    // Sub 3.2
    [
        'komp_id' => 3, 'sub_id' => 18, 'urutan' => 1,
        'indikator' => 'Bersedia belajar dari orang lain',
        'kriteria' => 'Adanya kemauan belajar dari orang lain:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. KKM, komite, lembaga pendidikan lain\n- b. Dokumentasi kegiatan"
    ],
    [
        'komp_id' => 3, 'sub_id' => 18, 'urutan' => 2,
        'indikator' => 'Ingin melakukan yang terbaik',
        'kriteria' => 'Adanya bukti keinginan melakukan yang terbaik:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Dokumentasi pelaksanaan kegiatan\n- b. Keikut-sertaan dalam lomba-lomba pembelajaran / pendidikan maupun manajemen prestasi dalam lomba guru, tenaga kependidikan, dan kepala"
    ],

    // --- KOMPONEN 4 (3 Indikator) ---
    // Sub 4.1
    [
        'komp_id' => 4, 'sub_id' => 19, 'urutan' => 1,
        'indikator' => 'Tersusunnya program supervisi terhadap Guru dan Tenaga Kependidikan (GTK)',
        'kriteria' => 'Program Supervisi GTK meliputi unsur: perencanaan, pelaksanaan, penilaian/pelaporan:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. SK Tim Supervisor dan lampirannya"
    ],

    // Sub 4.2
    [
        'komp_id' => 4, 'sub_id' => 20, 'urutan' => 1,
        'indikator' => 'Melaksanakan kegiatan supervisi terhadap Guru dan Tenaga Kependidikan (GTK)',
        'kriteria' => 'Pelaksanaan kegiatan supervisi terhadap Guru dan Tenaga Kependidikan (GTK):',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Wawancara, Observasi\n- a. Instrumen hasil supervisi GTK\n- b. Laporan hasil supervisi GTK\n- c. Catatan rekomendasi hasil supervisi\n- d. Rencana tindak lanjut hasil supervisi"
    ],

    // Sub 4.3
    [
        'komp_id' => 4, 'sub_id' => 21, 'urutan' => 1,
        'indikator' => 'Melaksanakan tindaklanjut hasil supervise GTK',
        'kriteria' => 'Laporan pelaksanaan tindak lanjut supervisi GTK:',
        'bukti_fisik' => "Penggalian Data: Telaah dokumen, Observasi, Wawancara\n- a. Laporan pelaksanaan tindak lanjut supervise GTK"
    ],
];

echo "Total Komponen: " . count($komponen) . "\n";
echo "Total Sub-Komponen: " . count($sub_komponen) . "\n";
echo "Total Indikator: " . count($indikator) . "\n";

// Generate SQL
$sql = "-- =========================================================================\n";
$sql .= "-- SINKRONISASI INSTRUMEN PKKM TERBARU (42 INDIKATOR SESUAI JUKNIS TAHUNAN)\n";
$sql .= "-- =========================================================================\n\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "TRUNCATE TABLE pkkm_nilai;\n";
$sql .= "TRUNCATE TABLE pkkm_berkas;\n";
$sql .= "TRUNCATE TABLE pkkm_indikator;\n";
$sql .= "TRUNCATE TABLE pkkm_sub_komponen;\n";
$sql .= "TRUNCATE TABLE pkkm_komponen;\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

// Komponen
$sql .= "-- 1. KOMPONEN\n";
$sql .= "INSERT INTO `pkkm_komponen` (`id`, `nama_komponen`, `urutan`) VALUES\n";
$komp_vals = [];
foreach ($komponen as $k) {
    $komp_vals[] = "({$k['id']}, '" . addslashes($k['nama']) . "', {$k['urutan']})";
}
$sql .= implode(",\n", $komp_vals) . ";\n\n";

// Sub-Komponen
$sql .= "-- 2. SUB-KOMPONEN\n";
$sql .= "INSERT INTO `pkkm_sub_komponen` (`id`, `komponen_id`, `nama_sub`, `urutan`) VALUES\n";
$sub_vals = [];
foreach ($sub_komponen as $s) {
    $sub_vals[] = "({$s['id']}, {$s['komp_id']}, '" . addslashes($s['nama']) . "', '{$s['urutan']}')";
}
$sql .= implode(",\n", $sub_vals) . ";\n\n";

// Indikator
$sql .= "-- 3. INDIKATOR (42 ITEM)\n";
$sql .= "INSERT INTO `pkkm_indikator` (`id`, `komponen_id`, `sub_komponen_id`, `indikator`, `kriteria`, `bukti_fisik`, `urutan`) VALUES\n";
$ind_vals = [];
$id = 1;
foreach ($indikator as $ind) {
    $ind_vals[] = "($id, {$ind['komp_id']}, {$ind['sub_id']}, '" . addslashes($ind['indikator']) . "', '" . addslashes($ind['kriteria']) . "', '" . addslashes($ind['bukti_fisik']) . "', {$ind['urutan']})";
    $id++;
}
$sql .= implode(",\n", $ind_vals) . ";\n";

file_put_contents(__DIR__ . '/sync_pkkm_instrumen_2026.sql', $sql);
echo "File scripts/sync_pkkm_instrumen_2026.sql berhasil dibuat!\n";
