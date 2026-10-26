<?php

namespace App\Services;

class PkkmPanduanService {

    /**
     * Master Kamus Panduan & Saran Bukti Fisik PKKM Konkret
     */
    public static function getRules(): array {
        return [
            // === TIK & TEKNOLOGI ===
            'internet' => [
                'keywords' => ['internet', 'wifi', 'jaringan'],
                'kategori' => 'Foto Dokumentasi & Struk',
                'badge_color' => 'blue',
                'icon' => 'wifi',
                'format' => 'Foto JPG/PNG atau PDF Struk',
                'contoh' => [
                    'Foto fisik router / modem Wi-Fi atau switch hub di ruang kantor/guru/lab.',
                    'Screenshot hasil uji kecepatan internet (Speedtest by Ookla) yang menampilkan nama provider & kecepatan.',
                    'Struk / kwitansi / bukti transfer pembayaran langganan internet bulanan (Indihome, Biznet, Iconnet, dll).'
                ],
                'tips' => 'Pengawas ingin memastikan madrasah memiliki koneksi internet aktif yang memadai untuk pembelajaran, UN/CBT, dan sinkronisasi EMIS/RDM.'
            ],
            'website_email' => [
                'keywords' => ['webset', 'website', 'email', 'e-mail', 'web sekolah', 'medsos'],
                'kategori' => 'Screenshot Digital & Tautan',
                'badge_color' => 'purple',
                'icon' => 'globe',
                'format' => 'Screenshot JPG/PNG atau Link URL',
                'contoh' => [
                    'Screenshot halaman beranda (Homepage) website resmi madrasah.',
                    'Screenshot dashboard admin website / cPanel / hosting domain madrasah.',
                    'Screenshot inbox email resmi madrasah (@masmifhda.sch.id atau email kedinasan).',
                    'Screenshot akun media sosial resmi madrasah (Facebook/Instagram/YouTube/TikTok) yang memuat berita kegiatan.'
                ],
                'tips' => 'Pastikan website memuat informasi profil madrasah, visi misi, dan berita kegiatan terbaru. Tautan link bisa dilampirkan langsung.'
            ],
            'komputer' => [
                'keywords' => ['computer', 'komputer', 'laptop'],
                'kategori' => 'Foto Inventaris & Dokumen',
                'badge_color' => 'emerald',
                'icon' => 'monitor',
                'format' => 'Foto JPG/PNG atau Dokumen Inventaris',
                'contoh' => [
                    'Foto ruang laboratorium komputer / perangkat laptop madrasah.',
                    'Foto saat perangkat komputer/laptop digunakan oleh siswa atau guru.',
                    'Daftar rekapitulasi inventaris komputer/laptop madrasah beserta status kelaikannya.'
                ],
                'tips' => 'Tampilkan foto laboratorium komputer yang tertata rapi beserta nomor unit PC/laptop.'
            ],
            'pemanfaatan_tik' => [
                'keywords' => ['pemanfaatan tik', 'modul berbasis tik', 'rpp berbasis tik', 'aplikasi'],
                'kategori' => 'Screenshot Aplikasi & RPP',
                'badge_color' => 'blue',
                'icon' => 'smartphone',
                'format' => 'PDF RPP atau Screenshot Aplikasi',
                'contoh' => [
                    'Screenshot penggunaan aplikasi madrasah (e-Learning, CBT Online, Google Classroom, Quizizz, Wordwall).',
                    'Modul ajar / RPP yang memuat integrasi media TIK (link video YouTube, slide Canva/PPT).',
                    'Foto saat guru mengajar menggunakan LCD proyektor atau TV interaktif di kelas.'
                ],
                'tips' => 'Tunjukkan bagaimana TIK benar-benar diterapkan dalam kegiatan belajar mengajar sehari-hari.'
            ],

            // === ORGANISASI & KELEMBAGAAN ===
            'struktur_organisasi' => [
                'keywords' => ['bagan', 'struktur organisasi', 'susunan organisasi', 'uraian tugas'],
                'kategori' => 'Foto Fisik & Dokumen SK',
                'badge_color' => 'emerald',
                'icon' => 'network',
                'format' => 'Foto JPG/PNG & Dokumen SK PDF',
                'contoh' => [
                    'Foto bagan struktur organisasi yang terpasang di dinding kantor Kepala Madrasah / ruang guru.',
                    'File bagan digital struktur organisasi yang ditandatangani Kamad.',
                    'SK Kepala Madrasah tentang Penetapan Struktur Organisasi & Rincian Tugas (Tupoksi) masing-masing personalia.',
                    'Dokumen notulen rapat penyusunan struktur organisasi beserta daftar hadir guru/tendik.'
                ],
                'tips' => 'Pengawas akan mencocokkan bagan di dinding dengan SK dan pembagian tugas riil guru/staf.'
            ],
            'sk_tugas' => [
                'keywords' => ['sk pembagian tugas', 'pembagian jam mengajar', 'jadwal'],
                'kategori' => 'Dokumen Resmi (SK & Jadwal)',
                'badge_color' => 'blue',
                'icon' => 'file-check',
                'format' => 'Dokumen PDF Lengkap',
                'contoh' => [
                    'SK Pembagian Tugas Guru dalam Proses Belajar Mengajar tahun ajaran aktif.',
                    'Lampiran rincian jam mengajar masing-masing guru per minggu.',
                    'Tabel jadwal pelajaran lengkap tiap rombel (kelas 1 s.d 6).',
                    'Foto jadwal pelajaran yang terpajang di papan pengumuman ruang guru.'
                ],
                'tips' => 'Pastikan SK sudah ditandatangani Kamad dan dibubuhi stempel resmi madrasah.'
            ],
            'komite_mou' => [
                'keywords' => ['komite', 'mou', 'kerja sama', 'mitra', 'dunia usaha'],
                'kategori' => 'Dokumen MoU & Foto Kegiatan',
                'badge_color' => 'purple',
                'icon' => 'handshake',
                'format' => 'Dokumen PDF MoU & Foto JPG',
                'contoh' => [
                    'Naskah MoU (Nota Kesepahaman) tertulis dengan Puskesmas, Kepolisian/Polsek, Bank, Dunia Usaha, atau Lembaga Pendidikan lain.',
                    'SK Kepengurusan Komite Madrasah masa bakti berjalan.',
                    'Notulen rapat dan daftar hadir pertemuan rutin komite madrasah bersama orang tua siswa.',
                    'Foto-foto kegiatan kerja sama (misal: penyuluhan kesehatan oleh Puskesmas, imunisasi, kegiatan parenting).'
                ],
                'tips' => 'MoU harus masih berlaku (aktif) pada tahun penilaian berjalan.'
            ],
            'visi_misi' => [
                'keywords' => ['visi, misi', 'visi misi', 'tujuan madrasah', 'unggulan'],
                'kategori' => 'Foto Terpajang & Notulen Sosialisasi',
                'badge_color' => 'emerald',
                'icon' => 'target',
                'format' => 'Foto JPG & Notulen PDF',
                'contoh' => [
                    'Foto banner/papan Visi, Misi, dan Tujuan Madrasah yang terpajang di lobi madrasah atau ruang kantor.',
                    'Berita acara, notulen, dan daftar hadir rapat perumusan/peninjauan visi misi madrasah.',
                    'Dokumen / brosur sosialisasi visi misi kepada wali murid dan masyarakat.'
                ],
                'tips' => 'Pastikan visi misi selaras dengan kurikulum dan program madrasah yang sedang dijalankan.'
            ],

            // === KURIKULUM & PEMBELAJARAN ===
            'kurikulum' => [
                'keywords' => ['kurikulum', 'ktsp', 'kom', 'kurikulum madrasah', 'tim pengembang kurikulum'],
                'kategori' => 'Dokumen Kurikulum Resmi',
                'badge_color' => 'blue',
                'icon' => 'book-open',
                'format' => 'Dokumen PDF Lengkap',
                'contoh' => [
                    'Buku Dokumen Kurikulum Madrasah (KOM / Kurikulum Merdeka atau Dokumen 1 KTSP) tahun ajaran aktif.',
                    'Lembar Pengesahan Kurikulum yang sudah ditandatangani Kamad dan disahkan oleh Kepala Seksi Pendidikan Madrasah (Kemenag Kab/Kota).',
                    'SK Tim Pengembang Kurikulum (TPK) Madrasah.',
                    'Notulen rapat, berita acara, dan daftar hadir kegiatan penyusunan kurikulum bersama narasumber/pengawas.'
                ],
                'tips' => 'Lembar pengesahan dari Kantor Kemenag adalah bukti paling krusial yang dicari pengawas.'
            ],
            'rpp_modul' => [
                'keywords' => ['rpp', 'modul', 'perangkat pembelajaran', 'panduan pembelajaran', 'bahan ajar'],
                'kategori' => 'Dokumen Perangkat Guru',
                'badge_color' => 'emerald',
                'icon' => 'file-text',
                'format' => 'Contoh PDF RPP/Modul',
                'contoh' => [
                    'Contoh Modul Ajar / RPP lengkap beberapa mata pelajaran (Tematik, PAI, Matematika).',
                    'Alur Tujuan Pembelajaran (ATP) / Silabus pembelajaran.',
                    'Program Tahunan (Prota) dan Program Semester (Promes).',
                    'Foto buku bahan ajar / lembar kerja peserta didik (LKPD) yang digunakan siswa.'
                ],
                'tips' => 'Cukup lampirkan sampel modul ajar 2-3 mata pelajaran yang ditandatangani guru dan diketahui Kepala Madrasah.'
            ],
            'kkg_mgmp' => [
                'keywords' => ['kkg', 'mgmp', 'pelatihan guru', 'workshop', 'pengembangan sdm'],
                'kategori' => 'Laporan Kegiatan & Sertifikat',
                'badge_color' => 'purple',
                'icon' => 'users',
                'format' => 'Laporan PDF & Foto JPG',
                'contoh' => [
                    'Surat tugas keikutsertaan guru dalam kegiatan KKG / MGMP / Pelatihan mandiri di PINTAR Kemenag.',
                    'Laporan pelaksanaan atau resume materi KKG tingkat kecamatan/kabupaten.',
                    'Sertifikat pelatihan / workshop peningkatan kompetensi guru.',
                    'Foto-foto saat guru mengikuti kegiatan workshop atau diskusi KKG.'
                ],
                'tips' => 'Tunjukkan sertifikat pelatihan terbaru guru baik daring (PINTAR/Kemdikbud) maupun luring.'
            ],
            'penilaian' => [
                'keywords' => ['penilaian', 'kisi-kisi', 'soal', 'analisis butir soal', 'raport', 'pas', 'pat'],
                'kategori' => 'Dokumen Instrumen Evaluasi',
                'badge_color' => 'amber',
                'icon' => 'clipboard-check',
                'format' => 'PDF Kisi-kisi & Sampel Raport',
                'contoh' => [
                    'Kisi-kisi soal Asesmen Sumatif / Penilaian Akhir Semester (PAS / PAT).',
                    'Naskah soal ulangan/asesmen beserta kunci jawaban dan rubrik penilaian.',
                    'Dokumen analisis hasil belajar siswa & program remedial / pengayaan.',
                    'Sampel lembar laporan hasil belajar (Buku Raport Siswa) yang sudah dibagikan.'
                ],
                'tips' => 'Lampirkan buku leger nilai atau cetak rekap nilai dari RDM (Rapor Digital Madrasah).'
            ],

            // === PERENCANAAN, EDM & KEUANGAN ===
            'edm_rkjm' => [
                'keywords' => ['edm', 'evaluasi diri', 'rkm', 'rktm', 'rkjm', 'raport pendidikan', 'rencana anggaran', 'keuangan'],
                'kategori' => 'Dokumen EDM & e-RKAM',
                'badge_color' => 'emerald',
                'icon' => 'bar-chart-2',
                'format' => 'Dokumen PDF Resmi',
                'contoh' => [
                    'Laporan Evaluasi Diri Madrasah (EDM) resmi hasil cetak dari aplikasi e-RKAM Kemenag.',
                    'Dokumen Rencana Kerja Jangka Menengah (RKJM 4 tahunan) dan Rencana Kerja Tahunan (RKT/RKM).',
                    'Dokumen Rencana Kegiatan dan Anggaran Madrasah (RKAM) berbasis EDM.',
                    'Unduhan hasil Raport Pendidikan Madrasah (Asesmen Nasional).'
                ],
                'tips' => 'Dokumen EDM dan RKAM harus sinkron satu sama lain sebagai wujud perencanaan berbasis data.'
            ],
            'lpj_keuangan' => [
                'keywords' => ['lpj', 'laporan keuangan', 'pertanggungjawaban', 'buku kas'],
                'kategori' => 'Laporan Pertanggungjawaban (LPJ)',
                'badge_color' => 'amber',
                'icon' => 'file-spreadsheet',
                'format' => 'Dokumen Sampel LPJ / BKU',
                'contoh' => [
                    'Laporan Pertanggungjawaban (LPJ) Dana BOS / BOP semester berjalan.',
                    'Buku Kas Umum (BKU), Buku Pembantu Kas, dan Buku Bank.',
                    'Berita acara rekonsiliasi BOS dengan Kemenag kabupaten/kota.',
                    'Papan informasi transparansi penggunaan dana BOS di madrasah.'
                ],
                'tips' => 'Foto papan transparansi dana BOS di dinding madrasah sangat disukai pengawas.'
            ],

            // === SARANA PRASARANA & LINGKUNGAN ===
            'sarpras_inventaris' => [
                'keywords' => ['inventaris', 'pemeliharaan sarana', 'buku inventaris', 'sarana prasarana'],
                'kategori' => 'Buku Catatan & Foto Fasilitas',
                'badge_color' => 'blue',
                'icon' => 'box',
                'format' => 'Buku PDF & Foto Sarpras',
                'contoh' => [
                    'Buku Induk Inventaris Barang / Aset Madrasah.',
                    'Buku Catatan Pemeliharaan / Perbaikan Sarana (servis AC/kipas, perbaikan meja kursi, instalasi listrik).',
                    'Foto papan denah tata ruang madrasah.',
                    'Foto kondisi ruang kelas, toilet siswa, ruang UKS, dan tempat ibadah (mushola).'
                ],
                'tips' => 'Buku pemeliharaan sarana harus memuat tanggal perbaikan, jenis sarana, dan estimasi biaya.'
            ],
            'perpustakaan' => [
                'keywords' => ['perpustakaan', 'buku induk perpustakaan', 'koleksi buku', 'grafik layanan perpustakaan'],
                'kategori' => 'Foto Ruang & Buku Pengunjung',
                'badge_color' => 'purple',
                'icon' => 'library',
                'format' => 'Foto JPG & Dokumen Buku',
                'contoh' => [
                    'Foto ruang perpustakaan madrasah dengan rak buku tertata rapi.',
                    'Buku induk katalog perpustakaan / daftar koleksi buku referensi dan buku teks.',
                    'Buku register kunjungan perpustakaan harian siswa dan guru.',
                    'Foto sudut baca (pojok baca) di dalam ruang kelas.'
                ],
                'tips' => 'Foto siswa yang sedang asyik membaca di pojok baca atau perpustakaan adalah nilai plus.'
            ],
            'lingkungan_asri' => [
                'keywords' => ['lingkungan', 'asri', 'rindang', 'bersih', 'aman', 'menyenangkan'],
                'kategori' => 'Foto Kondisi Lapangan',
                'badge_color' => 'emerald',
                'icon' => 'trees',
                'format' => 'Foto Landscape JPG/PNG',
                'contoh' => [
                    'Foto halaman madrasah yang bersih, asri dengan tanaman/pepohonan terawat.',
                    'Foto fasilitas tempat pembuangan sampah terpilah (organik & anorganik).',
                    'Foto fasilitas wastafel cuci tangan dengan sabun air mengalir di depan kelas.',
                    'Foto pagar pengaman madrasah yang menjamin keamanan peserta didik.'
                ],
                'tips' => 'Ambil foto saat cuaca cerah di pagi hari agar pemandangan madrasah tampak segar dan asri.'
            ],

            // === KESISWAAN, PPDB & PRESTASI ===
            'ppdb' => [
                'keywords' => ['ppdb', 'penerimaan peserta didik', 'brosur', 'buku penerimaan'],
                'kategori' => 'Brosur & Buku PPDB',
                'badge_color' => 'blue',
                'icon' => 'user-plus',
                'format' => 'Foto Brosur & Rekap PDF',
                'contoh' => [
                    'Brosur / pamflet / poster digital Penerimaan Peserta Didik Baru (PPDB).',
                    'SOP atau Panduan Alur Pendaftaran Peserta Didik Baru.',
                    'Buku Induk Registrasi Pendaftaran Siswa Baru tahun ajaran aktif.',
                    'Foto spanduk PPDB yang terpasang di depan gerbang madrasah.'
                ],
                'tips' => 'Lampirkan juga SK Panitia PPDB madrasah beserta formulir pendaftaran siswa.'
            ],
            'ekstrakurikuler' => [
                'keywords' => ['ekstrakurikuler', 'extrakurikuler', 'minat dan bakat', 'pembina'],
                'kategori' => 'Program & Foto Kegiatan',
                'badge_color' => 'purple',
                'icon' => 'activity',
                'format' => 'Program PDF & Foto JPG',
                'contoh' => [
                    'Buku Program Kerja Ekstrakurikuler (Pramuka, Drumband, Pencak Silat, Hadroh, Tahfidz, Futsal, dll).',
                    'Buku daftar hadir guru pembina ekstrakurikuler dan daftar hadir peserta didik.',
                    'Foto-foto dokumentasi aktifitas latihan ekstrakurikuler di madrasah.',
                    'SK Pembina Ekstrakurikuler yang ditetapkan oleh Kepala Madrasah.'
                ],
                'tips' => 'Tampilkan foto anak-anak berseragam pramuka atau atribut ekskul saat sedang latihan.'
            ],
            'prestasi_kejuaraan' => [
                'keywords' => ['prestasi', 'piagam', 'piala', 'lomba', 'kompetisi', 'kejuaraan'],
                'kategori' => 'Foto Piala & Piagam',
                'badge_color' => 'amber',
                'icon' => 'trophy',
                'format' => 'Foto JPG Piala / Scan Piagam PDF',
                'contoh' => [
                    'Foto lemari etalase piala kejuaraan / trofi perlombaan siswa/guru.',
                    'Scan piagam penghargaan atau sertifikat juara lomba (KSM, AKSIOMA, Porseni, lomba tingkat kecamatan/kabupaten/nasional).',
                    'Buku rekapitulasi data prestasi akademik & non-akademik 2 tahun terakhir.',
                    'Foto siswa saat menerima piala/penghargaan di atas panggung atau saat upacara bendera.'
                ],
                'tips' => 'Beri nomor urut dan nama kejuaraan pada scan sertifikat agar mudah diperiksa pengawas.'
            ],

            // === SUPERVISI KEPALA MADRASAH ===
            'supervisi_gtk' => [
                'keywords' => ['supervisi', 'supervisor', 'instrumen supervisi', 'tindak lanjut'],
                'kategori' => 'Dokumen Supervisi Akademik',
                'badge_color' => 'blue',
                'icon' => 'check-square',
                'format' => 'Dokumen PDF Lengkap',
                'contoh' => [
                    'SK Tim Supervisor Pembelajaran yang ditetapkan oleh Kepala Madrasah.',
                    'Jadwal pelaksanaan supervisi akademik guru dan tenaga kependidikan semester ganjil & genap.',
                    'Instrumen lembar penilaian supervisi yang sudah diisi skor dan catatan oleh Kepala Madrasah/Supervisor.',
                    'Catatan rekomendasi dan Laporan Tindak Lanjut hasil supervisi guru (pembinaan / penugasan pelatihan).',
                    'Foto dokumentasi saat Kepala Madrasah melakukan observasi mengajar guru di dalam kelas.'
                ],
                'tips' => 'Pengawas akan sangat teliti melihat tindak lanjut supervisi: setelah disupervisi, apa perbaikan yang diberikan Kamad kepada guru?'
            ],

            // === KETATAUSAHAAN & SURAT MENYURAT ===
            'persuratan_tu' => [
                'keywords' => ['surat masuk', 'surat keluar', 'persuratan', 'administrasi', 'absensi'],
                'kategori' => 'Buku Agenda & Absensi',
                'badge_color' => 'emerald',
                'icon' => 'mail',
                'format' => 'Foto Buku & Cetak Rekap PDF',
                'contoh' => [
                    'Foto Buku Agenda Surat Masuk dan Buku Agenda Surat Keluar fisik.',
                    'File lembar disposisi Kepala Madrasah untuk surat dinas masuk.',
                    'Rekap absensi kehadiran guru dan pegawai (cetak mesin fingerprint atau absensi digital/PushSimpatika).',
                    'Buku tamu dinas madrasah yang memuat catatan kunjungan pengawas / tamu dinas.'
                ],
                'tips' => 'Pastikan nomor surat dan stempel pada buku ekspedisi/agenda tertata rapi.'
            ],
            // === KEUANGAN & ANGGARAN BOS ===
            'keuangan_bos' => [
                'keywords' => ['keuangan', 'anggaran', 'bos', 'rkam', 'lpj', 'bku', 'dana bos', 'rkjm', 'rktm', 'pembiayaan'],
                'kategori' => 'Dokumen RKAM & Papan BOS',
                'badge_color' => 'emerald',
                'icon' => 'wallet',
                'format' => 'Dokumen PDF / Papan Transparansi',
                'contoh' => [
                    'Papan Transparansi Penggunaan Dana BOS & RKAM Tahun Berjalan (Infografis A4/Plang Madrasah).',
                    'Buku Rencana Kerja dan Anggaran Madrasah (e-RKAM) yang telah disetujui Kantor Kemenag.',
                    'Laporan Pertanggungjawaban (LPJ) Realisasi Dana BOS Tahap I dan Tahap II beserta BKU.',
                    'Bukti Setor Pajak (SSP / Cetak NTPN Bank Persepsi) untuk PPh 21, 22, 23 & PPN.'
                ],
                'tips' => 'Pengawas akan mengecek keterbukaan informasi publik (papan transparansi) dan ketepatan SPJ pajak serta sinkronisasi e-RKAM.'
            ],
        ];
    }

    /**
     * Cari panduan bukti fisik berdasarkan nama dokumen
     */
    public static function find(string $namaDokumen): array {
        $clean = mb_strtolower(trim($namaDokumen));
        $rules = self::getRules();

        foreach ($rules as $key => $rule) {
            foreach ($rule['keywords'] as $kw) {
                if (stripos($clean, $kw) !== false) {
                    return [
                        'found' => true,
                        'key' => $key,
                        'nama_dokumen' => $namaDokumen,
                        'kategori' => $rule['kategori'],
                        'badge_color' => $rule['badge_color'],
                        'icon' => $rule['icon'],
                        'format' => $rule['format'],
                        'contoh' => $rule['contoh'],
                        'tips' => $rule['tips']
                    ];
                }
            }
        }

        // Fallback cerdas jika tidak ada kata kunci yang cocok persis
        $isFoto = preg_match('/foto|dokumentasi|suasana|kondisi/i', $clean);
        return [
            'found' => false,
            'key' => 'general',
            'nama_dokumen' => $namaDokumen,
            'kategori' => $isFoto ? 'Foto Dokumentasi' : 'Dokumen / Berkas Resmi',
            'badge_color' => $isFoto ? 'emerald' : 'blue',
            'icon' => $isFoto ? 'camera' : 'file-text',
            'format' => $isFoto ? 'Foto JPG/PNG' : 'Dokumen PDF / Tautan',
            'contoh' => [
                'Dokumen resmi yang ditandatangani oleh Kepala Madrasah dan dibubuhi stempel.',
                'Foto dokumentasi kegiatan atau fasilitas pendukung terkait di madrasah.',
                'Notulen atau berita acara kegiatan yang mencakup daftar hadir dan foto kegiatan.'
            ],
            'tips' => 'Unggah berkas dalam format PDF yang jelas atau foto beresolusi baik agar mudah dibaca oleh pengawas.'
        ];
    }

    /**
     * Ekspor seluruh aturan dalam format JSON untuk digunakan langsung di antarmuka JavaScript
     */
    public static function getJsonRules(): string {
        return json_encode(self::getRules(), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
    }

    /**
     * Memberikan metadata detail, judul kontekstual, deskripsi konkret, kategori,
     * dan koneksi bukti digital siap cetak untuk setiap berkas master PKKM.
     */
    public static function getMasterBerkasMetadata(string $nama, array $matchedIndicators = []): array {
        $clean = mb_strtolower(trim($nama));
        
        $judul = $nama;
        $deskripsi = 'Dokumen resmi bukti fisik pelaksanaan program dan manajerial madrasah.';
        $kategori = 'umum';
        $buktiDigital = null;

        // === SPECIFIC MATCHING & OVERRIDES FOR AMBIGUOUS/GENERIC NAMES ===
        if (stripos($clean, 'berita acara, daftar hadir kegiatan dan dokumentasi') !== false) {
            $judul = 'Berita Acara, Daftar Hadir & Dokumentasi — Sosialisasi Visi Misi';
            $deskripsi = 'Notulen perumusan, daftar hadir komite/guru/tokoh, dan foto sosialisasi visi-misi madrasah.';
            $kategori = 'notulen';
            $buktiDigital = [
                'slug' => 'visi-misi',
                'title' => 'Bukti Visi Misi & Sosialisasi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/visi-misi'
            ];
        } elseif (stripos($clean, 'notulen rapat / briefing') !== false) {
            $judul = 'Notulen Rapat & Briefing — Pembinaan Disiplin GTK';
            $deskripsi = 'Catatan pengarahan berkala Kepala Madrasah terkait tata tertib, jam kerja, etika dan kedisiplinan guru.';
            $kategori = 'notulen';
        } elseif (stripos($clean, 'undangan, notulen dan daftar hadir') !== false) {
            $judul = 'Undangan, Notulen & Daftar Hadir — Kemitraan Komite / Lembaga Mitra';
            $deskripsi = 'Dokumen pertemuan koordinasi dengan orang tua, komite, atau instansi mitra pendidikan/pemerintah.';
            $kategori = 'notulen';
        } elseif (stripos($clean, 'dokumen penyusunan dokumen kurikulum') !== false) {
            $judul = 'Berita Acara, Daftar Hadir & Notulen — Tim Pengembang Kurikulum (KOSP/KTSP)';
            $deskripsi = 'Rapat kerja Tim Pengembang Kurikulum (TPK) bersama pengawas madrasah, dewan guru, dan komite.';
            $kategori = 'notulen';
        } elseif (stripos($clean, 'dokumen hasil sosialisasi kurikulum') !== false) {
            $judul = 'Dokumen Sosialisasi Kurikulum — Undangan, Daftar Hadir, Notulen & Foto';
            $deskripsi = 'Sosialisasi struktur dan muatan kurikulum madrasah kepada seluruh pendidik dan perwakilan orang tua murid.';
            $kategori = 'notulen';
        } elseif (stripos($clean, 'notulen rapat penyusunan struktur organisasi') !== false) {
            $judul = 'Notulen Rapat — Pembentukan Susunan Struktur Organisasi';
            $deskripsi = 'Notulensi musyawarah penetapan bagan struktur dan uraian tugas masing-masing personalia madrasah.';
            $kategori = 'notulen';
            $buktiDigital = [
                'slug' => 'struktur-organisasi',
                'title' => 'Bukti Struktur Organisasi Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi'
            ];
        } elseif (stripos($clean, 'daftar hadir rapat penyusunan struktur organisasi') !== false) {
            $judul = 'Daftar Hadir — Rapat Penyusunan Struktur Organisasi';
            $deskripsi = 'Presensi kehadiran dewan guru dan staf dalam rapat penetapan susunan organisasi madrasah.';
            $kategori = 'notulen';
            $buktiDigital = [
                'slug' => 'struktur-organisasi',
                'title' => 'Bukti Struktur Organisasi Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi'
            ];
        } elseif (stripos($clean, 'dokumen penetapan / pengesahan susunan organisasi') !== false) {
            $judul = 'SK Penetapan & Pengesahan Susunan Organisasi Madrasah';
            $deskripsi = 'Surat Keputusan Kepala Madrasah tentang pengesahan struktur organisasi dan bagan tata kelola.';
            $kategori = 'sk';
            $buktiDigital = [
                'slug' => 'struktur-organisasi',
                'title' => 'Bukti Struktur Organisasi Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi'
            ];
        } elseif (stripos($clean, 'bagan / struktur organisasi') !== false) {
            $judul = 'Bagan Struktur Organisasi & Tupoksi Personalia Madrasah';
            $deskripsi = 'Bagan struktur hierarki madrasah lengkap dengan nama pemegang jabatan dan uraian tugas pokok.';
            $kategori = 'sarpras';
            $buktiDigital = [
                'slug' => 'struktur-organisasi',
                'title' => 'Bukti Struktur Organisasi Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi'
            ];
        } elseif (stripos($clean, 'uraian tugas personalia') !== false) {
            $judul = 'Dokumen Uraian Tugas (Job Description) Personalia Madrasah';
            $deskripsi = 'Rincian wewenang dan tanggung jawab masing-masing jabatan (Kamad, Waka, Bendahara, Guru, Tendik).';
            $kategori = 'sk';
            $buktiDigital = [
                'slug' => 'struktur-organisasi',
                'title' => 'Bukti Struktur Organisasi Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi'
            ];
        } elseif (stripos($clean, 'visi') !== false && (stripos($clean, 'misi') !== false || stripos($clean, 'tujuan') !== false)) {
            $judul = 'Dokumen Resmi Visi, Misi, Tujuan & Sasaran Mutu Madrasah';
            $deskripsi = 'Naskah resmi SK penetapan, berita acara perumusan, publikasi plang kelembagaan, dan pemenuhan ketercapaian visi misi.';
            $kategori = 'program';
            $buktiDigital = [
                'slug' => 'visi-misi',
                'title' => 'Monitoring & Verifikasi Visi Misi (Pengawas & PKKM)',
                'url' => '/admin/pkkm/cetak-bukti/visi-misi'
            ];
        } elseif (stripos($clean, 'laporan kegiatan workshop, pelatihan kompetensi guru') !== false) {
            $judul = 'Laporan Pelaksanaan Workshop / Pelatihan Peningkatan Kompetensi Guru';
            $deskripsi = 'Laporan kegiatan in-house training, workshop Kurikulum Merdeka, atau bimtek kompetensi GTK.';
            $kategori = 'laporan';
        } elseif (stripos($clean, 'laporan kegiatan') !== false) {
            $judul = 'Laporan Pelaksanaan Kegiatan Kerjasama & Kemitraan Eksternal';
            $deskripsi = 'Laporan kegiatan kemitraan bersama instansi pemerintah, puskesmas, masyarakat, atau swasta.';
            $kategori = 'laporan';
        } elseif (preg_match('/^dokumentasi kegiatan/i', $clean) || stripos($clean, 'foto-foto kegiatan') !== false) {
            $judul = 'Dokumentasi & Foto Kegiatan Madrasah (Kerjasama, KKG, & Pembiasaan)';
            $deskripsi = 'Kumpulan foto dan video dokumentasi kegiatan kelembagaan, pertemuan guru, atau kemitraan komite.';
            $deskripsi = 'Kumpulan foto dan video dokumentasi kegiatan kelembagaan, pertemuan guru, atau kemitraan komite.';
            $kategori = 'laporan';
        } elseif (stripos($clean, 'catatan rekomendasi hasil supervisi') !== false) {
            $judul = 'Catatan Rekomendasi & Saran Pembinaan Hasil Supervisi GTK';
            $deskripsi = 'Lembar catatan khusus dari kepala madrasah untuk perbaikan proses pembelajaran guru.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Monitoring Rekomendasi Supervisi GTK',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=rekomendasi'
            ];
        } elseif (stripos($clean, 'rencana tindak lanjut hasil supervisi') !== false) {
            $judul = 'Rencana Tindak Lanjut (RTL) Hasil Supervisi Guru';
            $deskripsi = 'Matriks program tindak lanjut berupa bimbingan klinis, pelatihan teman sejawat, atau workshop.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Monitoring RTL & Realisasi Pembinaan Guru',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=rtl'
            ];
        } elseif (stripos($clean, 'laporan hasil supervisi') !== false) {
            $judul = 'Laporan Pelaksanaan Supervisi Guru dan Tenaga Kependidikan';
            $deskripsi = 'Laporan rekapitulasi penilaian administrasi, RPP/Modul, dan observasi pembelajaran di kelas.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Laporan Rekapitulasi Nilai Supervisi GTK',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=laporan'
            ];
        } elseif ((stripos($clean, 'instrumen') !== false || stripos($clean, 'instrument') !== false) && (stripos($clean, 'supervisi') !== false || stripos($clean, 'supervise') !== false)) {
            $judul = 'Instrumen & Lembar Penilaian Observasi Supervisi Guru';
            $deskripsi = 'Format instrumen telaah modul ajar/RPP dan lembar pengamatan proses pembelajaran di kelas.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Instrumen Hasil Observasi KBM Per Guru',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=instrumen'
            ];
        } elseif (stripos($clean, 'sk tim supervisor') !== false || (stripos($clean, 'tim supervisor') !== false && stripos($clean, 'sk') !== false)) {
            $judul = 'SK Tim Supervisor & Jadwal Pelaksanaan Supervisi GTK';
            $deskripsi = 'Surat Keputusan penunjukan tim supervisi pembelajaran beserta jadwal visitasi kelas.';
            $kategori = 'sk';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Bukti Program & Supervisi GTK (Portal)',
                'url' => '/admin/pkkm/monitoring-supervisi'
            ];
        } elseif (stripos($clean, 'laporan pelaksanaan tindak lanjut supervise') !== false || stripos($clean, 'tindak lanjut supervise') !== false || stripos($clean, 'tindak lanjut supervisi') !== false) {
            $judul = 'Laporan Realisasi Tindak Lanjut Hasil Supervisi Guru';
            $deskripsi = 'Bukti pelaksanaan perbaikan kinerja mengajar dan tindak lanjut pasca supervisi akademik.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Monitoring Realisasi RTL Supervisi Guru',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=rtl'
            ];
        } elseif (stripos($clean, 'dokumen supervise') !== false || stripos($clean, 'program pengawasan pembelajaran') !== false || stripos($clean, 'supervisi pembelajaran') !== false) {
            $judul = 'Program Kerja & Laporan Supervisi Pembelajaran Guru';
            $deskripsi = 'Dokumen program pengawasan pembelajaran, visitasi kelas, dan evaluasi hasil supervisi akademik.';
            $kategori = 'program';
            $buktiDigital = [
                'slug' => 'supervisi-guru',
                'title' => 'Portal Monitoring Supervisi GTK',
                'url' => '/admin/pkkm/monitoring-supervisi?tab=laporan'
            ];
        } elseif (stripos($clean, 'pembagian jam mengajar') !== false || stripos($clean, 'jadwal') !== false || stripos($clean, 'kalender') !== false) {
            $judul = (stripos($clean, 'kalender') !== false)
                ? 'Kalender Pendidikan Madrasah & Jadwal Rencana Kegiatan Tahunan'
                : 'Jadwal Pelajaran & Pembagian Beban Jam Mengajar Guru';
            $deskripsi = 'Matriks jadwal pelajaran kelas 1-6 dan pembagian ekuivalensi beban kerja tatap muka guru.';
            $kategori = 'program';
            $buktiDigital = [
                'slug' => 'jadwal-kegiatan',
                'title' => 'Bukti Jadwal & Kalender Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/jadwal-kegiatan'
            ];
        } elseif (stripos($clean, 'penilaian') === 0 && strlen($clean) < 15) {
            $judul = 'Instrumen & Rekapitulasi Penilaian Hasil Belajar Siswa';
            $deskripsi = 'Buku nilai harian, asesmen sumatif lingkup materi, asesmen akhir semester, dan analisis butir soal.';
            $kategori = 'program';
        } elseif (stripos($clean, 'pengarahan kegiatan') !== false) {
            $judul = 'Dokumen Pengarahan & Briefing Pimpinan Madrasah';
            $deskripsi = 'Lembar arahan atau poin pokok briefing pimpinan saat apel atau rapat dinas mingguan.';
            $kategori = 'notulen';
        } elseif (stripos($clean, 'suasana lingkungan yang asri') !== false) {
            $judul = 'Dokumentasi Lingkungan Madrasah Asri, Bersih, Rindang & Sehat';
            $deskripsi = 'Foto taman sekolah, area hijau, tempat pembuangan sampah terpilah, sanitasi, dan drainase sehat.';
            $kategori = 'sarpras';
            $buktiDigital = [
                'slug' => 'lingkungan-sehat',
                'title' => 'Bukti Lingkungan Sehat Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/lingkungan-sehat'
            ];
        } elseif (stripos($clean, 'webset') !== false || (stripos($clean, 'email') !== false && stripos($clean, 'alamat') !== false)) {
            $judul = 'Dokumen Domain & Email Dinas Madrasah (masmifhda.sch.id)';
            $deskripsi = 'Screenshot website resmi madrasah, panel domain, dan akun email dinas lembaga @masmifhda.sch.id.';
            $kategori = 'digital';
            $buktiDigital = [
                'slug' => 'website-email',
                'title' => 'Bukti Website & Email Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/website-email'
            ];
        } elseif (stripos($clean, 'jaringan internet') !== false) {
            $judul = 'Dokumen Jaringan Internet & Uji Kecepatan WiFi Madrasah';
            $deskripsi = 'Struk langganan ISP, screenshot Speedtest Ookla, dan foto titik akses WiFi di lingkungan madrasah.';
            $kategori = 'digital';
            $buktiDigital = [
                'slug' => 'jaringan-internet',
                'title' => 'Bukti Jaringan Internet Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/jaringan-internet'
            ];
        } elseif (stripos($clean, 'absensi elektronik') !== false) {
            $judul = 'Laporan Rekapitulasi Absensi Elektronik / Fingerprint GTK';
            $deskripsi = 'Laporan cetak rekap kehadiran digital mesin fingerprint/aplikasi absensi guru dan staf madrasah.';
            $kategori = 'digital';
            $buktiDigital = [
                'slug' => 'absensi-elektronik',
                'title' => 'Bukti Absensi Elektronik Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/absensi-elektronik'
            ];
        } elseif (stripos($clean, 'computer') !== false || stripos($clean, 'laptop') !== false) {
            $judul = 'Buku Inventaris & Ketersediaan Komputer / Laptop Madrasah';
            $deskripsi = 'Daftar inventaris spesifikasi perangkat PC laboratorium dan laptop operasional madrasah.';
            $kategori = 'digital';
            $buktiDigital = [
                'slug' => 'inventaris-tik',
                'title' => 'Bukti Inventaris TIK Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/inventaris-tik'
            ];
        } elseif (stripos($clean, 'perpustakaan') !== false) {
            $judul = (stripos($clean, 'induk') !== false) 
                ? 'Buku Induk Registrasi Koleksi Buku Perpustakaan' 
                : 'Buku & Grafik Statistik Layanan Pengunjung / Peminjaman Perpustakaan';
            $deskripsi = 'Daftar koleksi buku ber-ISBN atau catatan grafik pengunjung harian dan sirkulasi peminjaman buku.';
            $kategori = 'sarpras';
            $buktiDigital = [
                'slug' => 'perpustakaan',
                'title' => 'Bukti Perpustakaan Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/perpustakaan'
            ];
        } elseif (stripos($clean, 'ekstrakurikuler') !== false) {
            $judul = (stripos($clean, 'buku kehadiran') !== false)
                ? 'Buku Presensi & Daftar Hadir Kegiatan Ekstrakurikuler'
                : 'Program Kerja & Jadwal Pembinaan Ekstrakurikuler Madrasah';
            $deskripsi = 'Program latihan rutin ekstrakurikuler (Pramuka, Drumband, Tahfidz, Futsal, Tari) dan absensi siswa.';
            $kategori = 'program';
            $buktiDigital = [
                'slug' => 'ekstrakurikuler',
                'title' => 'Bukti Ekstrakurikuler Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/ekstrakurikuler'
            ];
        } elseif (stripos($clean, 'piagam penghargaan') !== false || stripos($clean, 'prestasi') !== false) {
            $judul = (stripos($clean, 'kepala') !== false)
                ? 'Dokumen Prestasi & Piagam Penghargaan Kepala Madrasah'
                : 'Rekapitulasi Prestasi Lomba Siswa & Koleksi Piagam / Piala';
            $deskripsi = 'Daftar kejuaraan lomba akademik dan non-akademik tingkat kecamatan, kabupaten/kota, atau provinsi.';
            $kategori = 'laporan';
            $buktiDigital = [
                'slug' => 'prestasi-siswa',
                'title' => 'Bukti Prestasi Siswa Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/prestasi-siswa'
            ];
        } elseif (stripos($clean, 'wirausaha') !== false || stripos($clean, 'kewirausahaan') !== false || stripos($clean, 'mou') !== false) {
            $judul = (stripos($clean, 'mou') !== false)
                ? 'Naskah Perjanjian Kerjasama (MoU) Kemitraan Eksternal & DUDI'
                : 'Program Pengembangan Kewirausahaan & Unit Usaha Produktif Madrasah';
            $deskripsi = 'Naskah MoU kemitraan atau program unit usaha madrasah (Koperasi siswa, kantin sehat, hidroponik).';
            $kategori = (stripos($clean, 'mou') !== false) ? 'sk' : 'program';
            $buktiDigital = [
                'slug' => 'kewirausahaan',
                'title' => 'Bukti Kewirausahaan Resmi (PDF)',
                'url' => '/admin/pkkm/cetak-bukti/kewirausahaan'
            ];
        } elseif (stripos($clean, 'perangkat pembelajaran') !== false || stripos($clean, 'rpp') !== false || stripos($clean, 'silabus') !== false) {
            $judul = (stripos($clean, 'silabus') !== false)
                ? 'Dokumen Pengembangan Silabus & Alur Tujuan Pembelajaran (ATP)'
                : ((stripos($clean, 'rpp') !== false) 
                    ? 'Rencana Pelaksanaan Pembelajaran (RPP) & Modul Ajar Terpadu'
                    : 'Dokumen Lengkap Perangkat Pembelajaran Guru Madrasah');
            $deskripsi = 'Portal monitoring & arsip digital 404 dokumen perangkat ajar (RPP, Modul Ajar, Silabus/ATP, Prota, Promes, KKTP) seluruh dewan guru.';
            $kategori = 'program';
            $buktiDigital = [
                'slug' => 'perangkat-guru',
                'title' => 'Monitoring Perangkat Guru (View Only)',
                'url' => '/admin/pkkm/cetak-bukti/perangkat-guru'
            ];
        }

        // === FALLBACK CATEGORIZATION BY KEYWORD IF STILL GENERAL ===
        if ($kategori === 'umum') {
            if (preg_match('/^sk|surat tugas|penetapan|pengesahan|sop|regulasi|uraian tugas|mou/i', $clean)) {
                $kategori = 'sk';
                $deskripsi = 'Surat Keputusan atau regulasi resmi yang disahkan oleh Kepala Madrasah.';
            } elseif (preg_match('/berita acara|notulen|daftar hadir|undangan|sosialisasi/i', $clean)) {
                $kategori = 'notulen';
                $deskripsi = 'Notulen, presensi kehadiran, atau berita acara musyawarah madrasah.';
            } elseif (preg_match('/program|rencana|rkm|rktm|kurikulum|rpp|modul|kalender|pembagian jam|bahan ajar|buku panduan|ktsp/i', $clean)) {
                $kategori = 'program';
                $deskripsi = 'Dokumen perencanaan operasional atau kurikulum dan rencana pembelajaran madrasah.';
            } elseif (preg_match('/laporan|evaluasi|monitoring|lpj|supervisi|raport|prestasi|rekomendasi|edm|alumni|karya tulis|inovasi/i', $clean)) {
                $kategori = 'laporan';
                $deskripsi = 'Laporan pertanggungjawaban, evaluasi capaian mutu, atau bukti prestasi.';
            } elseif (preg_match('/internet|website|email|tik|aplikasi|medsos|komputer|laptop|elektronik/i', $clean)) {
                $kategori = 'digital';
                $deskripsi = 'Bukti pemanfaatan teknologi informasi dan komunikasi di madrasah.';
            } elseif (preg_match('/buku induk|inventaris|sarana|gedung|ruang|lingkungan|surat masuk|buku penerimaan|kesiswaan|referensi/i', $clean)) {
                $kategori = 'sarpras';
                $deskripsi = 'Dokumen pengelolaan sarana prasarana, fasilitas atau administrasi umum madrasah.';
            }
        }

        $kategoriLabels = [
            'sk' => ['label' => 'SK & Kebijakan', 'badge' => 'badge-sk', 'icon' => 'file-badge'],
            'notulen' => ['label' => 'Berita Acara & Notulen', 'badge' => 'badge-notulen', 'icon' => 'clipboard-check'],
            'program' => ['label' => 'Program & Rencana Kerja', 'badge' => 'badge-program', 'icon' => 'calendar-range'],
            'laporan' => ['label' => 'Laporan, Evaluasi & Prestasi', 'badge' => 'badge-laporan', 'icon' => 'trending-up'],
            'digital' => ['label' => 'Bukti Digital & TIK', 'badge' => 'badge-digital', 'icon' => 'cpu'],
            'sarpras' => ['label' => 'Sarpras & Lingkungan', 'badge' => 'badge-sarpras', 'icon' => 'building-2'],
            'umum' => ['label' => 'Dokumen Kelengkapan', 'badge' => 'badge-umum', 'icon' => 'file-text']
        ];

        $katInfo = $kategoriLabels[$kategori] ?? $kategoriLabels['umum'];

        return [
            'nama_asli' => $nama,
            'judul_tampilan' => $judul,
            'deskripsi' => $deskripsi,
            'kategori' => $kategori,
            'kategori_label' => $katInfo['label'],
            'kategori_badge' => $katInfo['badge'],
            'kategori_icon' => $katInfo['icon'],
            'bukti_digital' => $buktiDigital
        ];
    }
}

