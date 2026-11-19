<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class BiologiKelasXII {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): Kerja Enzim & Faktor yang Mempengaruhinya
        // ══════════════════════════════════════════════════════════════════
        $prompt1 = "Grafik biokimia kinetika aktivitas enzim pengaruh suhu dan pH. Menampilkan kurva lonceng suhu optimum (37°C) dan denaturasi protein pada suhu di atas 60°C. Sisi kanan menampilkan model Induced Fit sisi aktif enzim mengikat molekul substrat serta pengaruh inhibitor kompetitif yang memblokir sisi aktif. Gaya ilustrasi ilmiah laboratorium biokimia modern.";
        $svg1 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <line x1="25" y1="75" x2="140" y2="75" stroke="#64748b" stroke-width="1.5"/>
            <line x1="25" y1="75" x2="25" y2="15" stroke="#64748b" stroke-width="1.5"/>
            <path d="M 28 75 Q 75 10 135 75" fill="none" stroke="#2563eb" stroke-width="2.5"/>
            <circle cx="82" cy="22" r="3" fill="#dc2626"/>
            <text x="82" y="14" font-size="7.5" font-weight="bold" fill="#dc2626" text-anchor="middle">Suhu Opt (37&deg;C)</text>
            <text x="25" y="10" font-size="7" fill="#64748b">Laju</text>
            <text x="140" y="84" font-size="7" fill="#64748b">Suhu</text>
            <rect x="160" y="15" width="100" height="60" rx="8" fill="#f8fafc" stroke="#475569" stroke-width="1.5"/>
            <text x="210" y="34" font-size="8.5" font-weight="bold" fill="#0f172a" text-anchor="middle">Sisi Aktif Enzim</text>
            <text x="210" y="50" font-size="7.5" fill="#2563eb" text-anchor="middle">Induced Fit Model</text>
            <text x="210" y="65" font-size="7" fill="#dc2626" text-anchor="middle">Inhibitor Kompetitif</text>
        </svg>';
        $story1 = 'Enzim merupakan <u>biokatalisator organik</u> berbahan protein yang mempercepat laju reaksi kimia metabolisme dengan cara <strong>menurunkan energi aktivasi (Ea)</strong> tanpa ikut berubah menjadi produk. Enzim bekerja secara spesifik terhadap substrat tertentu (model gembok-anak kunci / *Lock and Key* serta kecocokan terinduksi / *Induced Fit*). Faktor yang mempengaruhi kerja enzim meliputi suhu, derajat keasaman (pH), konsentrasi enzim/substrat, serta keberadaan <u>inhibitor kompetitif</u> (menyerupai substrat dan merebut sisi aktif) maupun <u>inhibitor non-kompetitif</u> (menempel pada sisi alosterik).';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Kinetika Enzim dan Faktor Pengaruh Metabolisme', $prompt1, $svg1, $story1, '1. PILIHAN GANDA', 'Mengapa laju reaksi enzimatik <u>menurun drastis hingga berhenti</u> ketika suhu dinaikkan melampaui suhu optimumnya (di atas 60°C)?'),
            'opsi_a' => 'Molekul substrat membeku dan tidak dapat bergerak',
            'opsi_b' => 'Enzim mengalami denaturasi kerusakan struktur tiga dimensi proteinnya',
            'opsi_c' => 'Energi aktivasi reaksi meningkat secara berlebihan',
            'opsi_d' => 'Terbentuknya inhibitor kompetitif alami dari substrat',
            'opsi_e' => 'Enzim berubah menjadi molekul koenzim anorganik',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Molekul substrat membeku dan tidak dapat bergerak',
                'B' => 'Enzim mengalami denaturasi kerusakan struktur tiga dimensi proteinnya',
                'C' => 'Energi aktivasi reaksi meningkat secara berlebihan',
                'D' => 'Terbentuknya inhibitor kompetitif alami dari substrat',
                'E' => 'Enzim berubah menjadi molekul koenzim anorganik'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Kinetika Enzim dan Faktor Pengaruh Metabolisme', '', '', $story1, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai sifat-sifat umum katalitik dari enzim? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Enzim menurunkan energi aktivasi yang diperlukan untuk memulai suatu reaksi biokimia.',
            'opsi_b' => 'Struktur enzim tidak ikut habis terkonsumsi dan dapat digunakan kembali setelah produk terlepas.',
            'opsi_c' => 'Enzim bekerja secara spesifik hanya pada substrat yang memiliki konformasi komplementer.',
            'opsi_d' => 'Enzim dapat mengubah arah kesetimbangan akhir suatu reaksi endoterm menjadi eksoterm.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Enzim menurunkan energi aktivasi yang diperlukan untuk memulai suatu reaksi biokimia.', 'is_kunci' => true],
                    ['teks' => 'Struktur enzim tidak ikut habis terkonsumsi dan dapat digunakan kembali setelah produk terlepas.', 'is_kunci' => true],
                    ['teks' => 'Enzim bekerja secara spesifik hanya pada substrat yang memiliki konformasi komplementer.', 'is_kunci' => true],
                    ['teks' => 'Enzim dapat mengubah arah kesetimbangan akhir suatu reaksi endoterm menjadi eksoterm.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Kinetika Enzim dan Faktor Pengaruh Metabolisme', '', '', $story1, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari mekanisme kerja inhibitor enzim berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Inhibitor kompetitif dapat diatasi efek penghambatannya dengan meningkatkan konsentrasi molekul substrat.', 'kunci' => 'Benar'],
                    ['teks' => 'Inhibitor non-kompetitif berikatan langsung pada sisi aktif enzim sehingga merusak bentuk substrat.', 'kunci' => 'Salah'],
                    ['teks' => 'Bagian protein dari suatu enzim majemuk (holoenzim) disebut dengan istilah apoenzim.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Kinetika Enzim dan Faktor Pengaruh Metabolisme', '', '', $story1, '4. MENJODOHKAN', 'Pasangkan komponen pembentuk enzim majemuk (holoenzim) dengan deskripsinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Apoenzim', 'respon' => 'Komponen protein enzim yang bersifat termolabil (sensitif panas)'],
                ['premis' => 'Koenzim', 'respon' => 'Gugus non-protein organik (seperti vitamin B, NAD+, FAD)'],
                ['premis' => 'Kofaktor', 'respon' => 'Gugus non-protein anorganik berupa ion-ion logam (Fe2+, Mg2+)'],
                ['premis' => 'Sisi Alosterik', 'respon' => 'Situs selain sisi aktif tempat melekatnya inhibitor non-kompetitif']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Apoenzim', 'respon' => 'Komponen protein enzim yang bersifat termolabil (sensitif panas)'],
                    ['premis' => 'Koenzim', 'respon' => 'Gugus non-protein organik (seperti vitamin B, NAD+, FAD)'],
                    ['premis' => 'Kofaktor', 'respon' => 'Gugus non-protein anorganik berupa ion-ion logam (Fe2+, Mg2+)'],
                    ['premis' => 'Sisi Alosterik', 'respon' => 'Situs selain sisi aktif tempat melekatnya inhibitor non-kompetitif']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Kinetika Enzim dan Faktor Pengaruh Metabolisme', '', '', $story1, '5. ISIAN SINGKAT', 'Zat penghambat yang memiliki struktur mirip dengan substrat dan bersaing untuk menempati sisi aktif enzim dinamakan inhibitor...'),
            'kunci_jawaban' => 'Kompetitif',
            'konten_tambahan' => ['rubrik' => 'Kompetitif (atau Inhibitor Kompetitif)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): Katabolisme Karbohidrat: Respirasi Aerob
        // ══════════════════════════════════════════════════════════════════
        $prompt2 = "Diagram alur biokimia 4 tahapan respirasi seluler aerobik lengkap di dalam sel manusia. Menampilkan Glikolisis di sitoplasma (Glukosa -> 2 Asam Piruvat + 2 NADH + 2 ATP), Dekarboksilasi Oksidatif di membran luar mitokondria, Siklus Krebs di matriks mitokondria (menghasilkan 2 ATP, 6 NADH, 2 FADH2, dan CO2), serta Rantai Transpor Elektron di krista mitokondria dengan enzim ATP sintase menghasilkan 28 ATP dan H2O. Vektor ilmiah metabolomik beresolusi tinggi.";
        $svg2 = '<svg width="280" height="95" viewBox="0 0 280 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="10" width="60" height="75" rx="6" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="35" y="28" font-size="8" font-weight="bold" fill="#b45309" text-anchor="middle">1. Glikolisis</text>
            <text x="35" y="44" font-size="7" fill="#78350f" text-anchor="middle">Sitosol</text>
            <text x="35" y="60" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">2 ATP, 2 NADH</text>
            <rect x="72" y="10" width="60" height="75" rx="6" fill="#fed7aa" stroke="#ea580c" stroke-width="1.5"/>
            <text x="102" y="28" font-size="8" font-weight="bold" fill="#c2410c" text-anchor="middle">2. Dekarb. Oks.</text>
            <text x="102" y="44" font-size="7" fill="#7c2d12" text-anchor="middle">Mitokondria</text>
            <text x="102" y="60" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">2 Asetil KoA</text>
            <rect x="139" y="10" width="65" height="75" rx="6" fill="#eff6ff" stroke="#2563eb" stroke-width="1.5"/>
            <text x="171" y="28" font-size="8" font-weight="bold" fill="#1d4ed8" text-anchor="middle">3. Siklus Krebs</text>
            <text x="171" y="44" font-size="7" fill="#1e40af" text-anchor="middle">Matriks</text>
            <text x="171" y="60" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">2 ATP, 6 NADH</text>
            <rect x="210" y="10" width="65" height="75" rx="6" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="242" y="28" font-size="8" font-weight="bold" fill="#047857" text-anchor="middle">4. STE</text>
            <text x="242" y="44" font-size="7" fill="#065f46" text-anchor="middle">Krista</text>
            <text x="242" y="60" font-size="7.5" fill="#16a34a" font-weight="bold" text-anchor="middle">28-34 ATP</text>
        </svg>';
        $story2 = 'Respirasi aerobik merupakan proses katabolisme penguraian 1 molekul glukosa (C6H12O6) menggunakan gas oksigen (O2) menjadi CO2, H2O, dan energi ATP. Proses ini terdiri dari 4 tahapan berurutan: (1) <strong>Glikolisis</strong> di sitosol (pemecahan glukosa menjadi 2 asam piruvat, 2 NADH, 2 ATP); (2) <strong>Dekarboksilasi Oksidatif</strong> piruvat menjadi 2 Asetil Ko-A, 2 CO2, 2 NADH; (3) <strong>Siklus Asam Sitrat (Krebs)</strong> di matriks mitokondria menghasilkan 2 ATP, 6 NADH, 2 FADH2, 4 CO2; dan (4) <strong>Sistem Transpor Elektron (STE)</strong> di membran dalam (krista) mitokondria dengan O2 bertindak sebagai akseptor elektron terakhir membentuk H2O.';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Katabolisme Seluler: Jalur Respirasi Aerob', $prompt2, $svg2, $story2, '1. PILIHAN GANDA', 'Molekul yang bertindak sebagai <u>akseptor elektron dan proton hidrogen terakhir</u> pada rantai transpor elektron respirasi aerobik adalah...'),
            'opsi_a' => 'Asam Piruvat',
            'opsi_b' => 'Gas Oksigen (O2)',
            'opsi_c' => 'Koenzim NAD+',
            'opsi_d' => 'Gas Karbon Dioksida (CO2)',
            'opsi_e' => 'Sitokrom c oksidase',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Asam Piruvat',
                'B' => 'Gas Oksigen (O2)',
                'C' => 'Koenzim NAD+',
                'D' => 'Gas Karbon Dioksida (CO2)',
                'E' => 'Sitokrom c oksidase'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Katabolisme Seluler: Jalur Respirasi Aerob', '', '', $story2, '2. PG KOMPLEKS (CENTANG)', 'Produk-produk manakah di bawah ini yang dihasilkan secara langsung dari <strong>Siklus Krebs</strong> untuk setiap penguraian 1 molekul glukosa? (Pilihan benar lebih dari satu)'),
            'opsi_a' => '2 molekul ATP (atau GTP)',
            'opsi_b' => '6 molekul NADH',
            'opsi_c' => '2 molekul FADH2',
            'opsi_d' => '2 molekul Asam Laktat',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => '2 molekul ATP (atau GTP)', 'is_kunci' => true],
                    ['teks' => '6 molekul NADH', 'is_kunci' => true],
                    ['teks' => '2 molekul FADH2', 'is_kunci' => true],
                    ['teks' => '2 molekul Asam Laktat', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Katabolisme Seluler: Jalur Respirasi Aerob', '', '', $story2, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari konversi energi dalam respirasi aerob berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Setiap 1 molekul NADH yang memasuki sistem transpor elektron setara dengan pembentukan 3 molekul ATP (atau 2.5 ATP).', 'kunci' => 'Benar'],
                    ['teks' => 'Glikolisis membutuhkan gas oksigen murni agar dapat memecah glukosa menjadi asam piruvat.', 'kunci' => 'Salah'],
                    ['teks' => 'Fermentasi asam laktat terjadi pada otot saat pasokan oksigen tidak mencukupi, hanya menghasilkan 2 ATP per glukosa.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Katabolisme Seluler: Jalur Respirasi Aerob', '', '', $story2, '4. MENJODOHKAN', 'Pasangkan tahapan respirasi seluler dengan lokasi spesifik tempat terjadinya di dalam sel:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Glikolisis', 'respon' => 'Sitosol / Sitoplasma'],
                ['premis' => 'Dekarboksilasi Oksidatif', 'respon' => 'Matriks / Membran Luar Mitokondria'],
                ['premis' => 'Siklus Krebs', 'respon' => 'Matriks Mitokondria'],
                ['premis' => 'Rantai Transpor Elektron', 'respon' => 'Membran Dalam (Krista) Mitokondria']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Glikolisis', 'respon' => 'Sitosol / Sitoplasma'],
                    ['premis' => 'Dekarboksilasi Oksidatif', 'respon' => 'Matriks / Membran Luar Mitokondria'],
                    ['premis' => 'Siklus Krebs', 'respon' => 'Matriks Mitokondria'],
                    ['premis' => 'Rantai Transpor Elektron', 'respon' => 'Membran Dalam (Krista) Mitokondria']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Katabolisme Seluler: Jalur Respirasi Aerob', '', '', $story2, '5. ISIAN SINGKAT', 'Enzim kompleks saluran proton pada krista mitokondria yang memanfaatkan gradien kemiosmosis H+ untuk mensintesis ATP dinamakan...'),
            'kunci_jawaban' => 'ATP Sintase',
            'konten_tambahan' => ['rubrik' => 'ATP Sintase']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): Anabolisme: Fotosintesis Reaksi Terang & Gelap
        // ══════════════════════════════════════════════════════════════════
        $prompt3 = "Infografis biokimia fotosintesis tumbuhan hijau. Sisi kiri menampilkan Reaksi Terang di membran tilakoid dengan Fotosistem II (P680) dan Fotosistem I (P700), fotolisis air H2O menghasilkan gas O2, serta pembentukan ATP dan NADPH. Sisi kanan menampilkan Reaksi Gelap (Siklus Calvin) di stroma dengan fiksasi CO2 oleh enzim RuBisCO dan sintesis glukosa. Diagram ilmiah botani modern.";
        $svg3 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="115" height="74" rx="8" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="72" y="26" font-size="8.5" font-weight="bold" fill="#047857" text-anchor="middle">REAKSI TERANG</text>
            <text x="72" y="42" font-size="7.5" fill="#065f46" text-anchor="middle">Membran Tilakoid</text>
            <text x="72" y="56" font-size="7.5" fill="#15803d" text-anchor="middle">H2O &rarr; O2 (Fotolisis)</text>
            <text x="72" y="70" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">Hasil: ATP + NADPH</text>
            <rect x="140" y="8" width="115" height="74" rx="8" fill="#eff6ff" stroke="#2563eb" stroke-width="1.5"/>
            <text x="197" y="26" font-size="8.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle">SIKLUS CALVIN</text>
            <text x="197" y="42" font-size="7.5" fill="#1e40af" text-anchor="middle">Stroma Kloroplas</text>
            <text x="197" y="56" font-size="7.5" fill="#2563eb" text-anchor="middle">Fiksasi CO2 (RuBisCO)</text>
            <text x="197" y="70" font-size="7.5" fill="#16a34a" font-weight="bold" text-anchor="middle">Hasil: Glukosa (PGAL)</text>
        </svg>';
        $story3 = 'Fotosintesis merupakan anabolisme penyusunan molekul organik glukosa dari CO2 dan air menggunakan energi cahaya matahari. Terbagi menjadi 2 reaksi: (1) <u>Reaksi Terang (Fotofosforilasi)</u> di tilakoid/grana yang membutuhkan cahaya untuk fotolisis pemecahan air (H2O &rarr; 2H+ + 2e- + &frac12; O2) menghasilkan gas O2 serta energi kimia <strong>ATP dan NADPH</strong>; dan (2) <u>Reaksi Gelap (Siklus Calvin)</u> di stroma yang tidak membutuhkan cahaya langsung, menggunakan ATP dan NADPH untuk mereduksi CO2 yang diikat oleh RuBP dengan katalis enzim <em>RuBisCO</em> membentuk glukosa.';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Fotobiologi Fotosintesis: Reaksi Terang dan Siklus Calvin', $prompt3, $svg3, $story3, '1. PILIHAN GANDA', 'Gas <u>oksigen (O2)</u> yang dilepaskan tumbuhan ke atmosfer selama proses fotosintesis berasal dari peristiwa...'),
            'opsi_a' => 'Fiksasi molekul gas karbon dioksida (CO2)',
            'opsi_b' => 'Fotolisis penguraian molekul air (H2O) pada fotosistem II',
            'opsi_c' => 'Regenerasi senyawa ribulosa bifosfat (RuBP)',
            'opsi_d' => 'Reduksi fosfogliserat (PGA) menjadi PGAL',
            'opsi_e' => 'Aliran elektron siklik pada fotosistem I',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Fiksasi molekul gas karbon dioksida (CO2)',
                'B' => 'Fotolisis penguraian molekul air (H2O) pada fotosistem II',
                'C' => 'Regenerasi senyawa ribulosa bifosfat (RuBP)',
                'D' => 'Reduksi fosfogliserat (PGA) menjadi PGAL',
                'E' => 'Aliran elektron siklik pada fotosistem I'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Fotobiologi Fotosintesis: Reaksi Terang dan Siklus Calvin', '', '', $story3, '2. PG KOMPLEKS (CENTANG)', 'Zat-zat apa sajakah yang merupakan <strong>produk hasil reaksi terang</strong> yang langsung digunakan sebagai bahan bakar dalam Siklus Calvin? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Molekul penyimpan energi ATP',
            'opsi_b' => 'Koenzim pereduksi NADPH',
            'opsi_c' => 'Gas oksigen (O2) bebas',
            'opsi_d' => 'Gas karbon monoksida (CO)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Molekul penyimpan energi ATP', 'is_kunci' => true],
                    ['teks' => 'Koenzim pereduksi NADPH', 'is_kunci' => true],
                    ['teks' => 'Gas oksigen (O2) bebas', 'is_kunci' => false],
                    ['teks' => 'Gas karbon monoksida (CO)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B']
            ]
        ];

        // No 13: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Fotobiologi Fotosintesis: Reaksi Terang dan Siklus Calvin', '', '', $story3, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari tahapan Siklus Calvin berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Enzim RuBisCO mengkatalisis pengikatan CO2 oleh senyawa berkarbon-5 Ribulosa Bifosfat (RuBP).', 'kunci' => 'Benar'],
                    ['teks' => 'Siklus Calvin berlangsung di dalam grana kloroplas dan menghasilkan gas O2.', 'kunci' => 'Salah'],
                    ['teks' => 'Senyawa fosfogliseraldehida (PGAL / G3P) merupakan prekursor langsung pembentukan glukosa dan amilum.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Fotobiologi Fotosintesis: Reaksi Terang dan Siklus Calvin', '', '', $story3, '4. MENJODOHKAN', 'Pasangkan 3 tahapan utama Siklus Calvin dengan peristiwanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Tahap Karboksilasi / Fiksasi', 'respon' => 'Pengikatan molekul CO2 oleh RuBP membentuk 3-PGA'],
                ['premis' => 'Tahap Reduksi', 'respon' => 'Pengubahan PGA menjadi PGAL menggunakan elektron NADPH dan ATP'],
                ['premis' => 'Tahap Regenerasi', 'respon' => 'Penyusunan kembali molekul PGAL menjadi RuBP penerima CO2'],
                ['premis' => 'Reaksi Terang', 'respon' => 'Eksitasi klorofil dan fotofosforilasi di membran tilakoid']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Tahap Karboksilasi / Fiksasi', 'respon' => 'Pengikatan molekul CO2 oleh RuBP membentuk 3-PGA'],
                    ['premis' => 'Tahap Reduksi', 'respon' => 'Pengubahan PGA menjadi PGAL menggunakan elektron NADPH dan ATP'],
                    ['premis' => 'Tahap Regenerasi', 'respon' => 'Penyusunan kembali molekul PGAL menjadi RuBP penerima CO2'],
                    ['premis' => 'Reaksi Terang', 'respon' => 'Eksitasi klorofil dan fotofosforilasi di membran tilakoid']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Fotobiologi Fotosintesis: Reaksi Terang dan Siklus Calvin', '', '', $story3, '5. ISIAN SINGKAT', 'Cairan kental menyerupai matriks di dalam kloroplas yang menjadi tempat berlangsungnya Siklus Calvin dinamakan...'),
            'kunci_jawaban' => 'Stroma',
            'konten_tambahan' => ['rubrik' => 'Stroma']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): Struktur DNA & Dogma Sentral Genetika
        // ══════════════════════════════════════════════════════════════════
        $prompt4 = "Diagram molekuler heliks ganda DNA (Double Helix Watson-Crick). Menampilkan rantai polinukleotida antiparalel 5'-3' dan 3'-5', rangka gula deoksiribosa dan gugus fosfat, pasangan basa nitrogen komplementer Adenin berikatan 2 hidrogen dengan Timin (A=T), Guanin berikatan 3 hidrogen dengan Sitosin (G≡C). Ilustrasi sains genetika molekuler modern.";
        $svg4 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <line x1="40" y1="10" x2="40" y2="80" stroke="#0284c7" stroke-width="3"/>
            <line x1="120" y1="10" x2="120" y2="80" stroke="#0284c7" stroke-width="3"/>
            <line x1="40" y1="25" x2="120" y2="25" stroke="#ef4444" stroke-width="2"/>
            <text x="80" y="21" font-size="7.5" font-weight="bold" fill="#dc2626" text-anchor="middle">A = T (2 Ikatan H)</text>
            <line x1="40" y1="50" x2="120" y2="50" stroke="#10b981" stroke-width="2.5"/>
            <text x="80" y="46" font-size="7.5" font-weight="bold" fill="#047857" text-anchor="middle">G &equiv; C (3 Ikatan H)</text>
            <line x1="40" y1="72" x2="120" y2="72" stroke="#ef4444" stroke-width="2"/>
            <rect x="145" y="15" width="115" height="60" rx="8" fill="#f8fafc" stroke="#64748b" stroke-width="1.5"/>
            <text x="202" y="34" font-size="8.5" font-weight="bold" fill="#0f172a" text-anchor="middle">Nukleotida</text>
            <text x="202" y="48" font-size="7.5" fill="#64748b" text-anchor="middle">&bull; Gula Deoksiribosa</text>
            <text x="202" y="60" font-size="7.5" fill="#64748b" text-anchor="middle">&bull; Gugus Fosfat</text>
            <text x="202" y="70" font-size="7.5" fill="#0284c7" text-anchor="middle">&bull; Basa Nitrogen</text>
        </svg>';
        $story4 = 'Asam deoksiribonukleat (DNA) menyimpan materi genetik dalam bentuk rantai ganda heliks berpilin (<em>double helix</em>) yang bersifat antiparalel. Satu unit monomer nukleotida tersusun atas: <u>gugus fosfat</u>, <u>gula pentosa deoksiribosa</u>, dan <u>basa nitrogen</u>. Basa nitrogen terbagi menjadi golongan Purin (Adenin [A] dan Guanin [G]) serta Pirimidin (Timin [T] dan Sitosin [C]). Berdasarkan Aturan Chargaff, persentase Adenin selalu sama dengan Timin (A=T dengan 2 ikatan hidrogen), dan persentase Guanin selalu sama dengan Sitosin (G&equiv;C dengan 3 ikatan hidrogen).';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Struktur Molekuler DNA dan Aturan Chargaff', $prompt4, $svg4, $story4, '1. PILIHAN GANDA', 'Jika dalam suatu molekul DNA beruntai ganda teranalisis memiliki kandungan basa <u>Guanin (G) sebesar 30%</u>, berapakah persentase kandungan basa <u>Timin (T)</u> pada molekul DNA tersebut?'),
            'opsi_a' => '30%',
            'opsi_b' => '60%',
            'opsi_c' => '20%',
            'opsi_d' => '40%',
            'opsi_e' => '10%',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => '30%',
                'B' => '60%',
                'C' => '20%',
                'D' => '40%',
                'E' => '10%'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Struktur Molekuler DNA dan Aturan Chargaff', '', '', $story4, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>membedakan molekul DNA dan RNA</strong> secara tepat? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Gula pentosa pada DNA adalah deoksiribosa, sedangkan pada RNA adalah ribosa.',
            'opsi_b' => 'Basa pirimidin pada DNA terdiri atas Timin dan Sitosin, sedangkan pada RNA Timin digantikan oleh Urasil.',
            'opsi_c' => 'DNA beruntai ganda heliks panjang (double helix), sedangkan RNA umumnya beruntai tunggal pendek (single strand).',
            'opsi_d' => 'Kadar RNA di dalam sel selalu bersifat konstan permanen tidak terpengaruh sintesis protein.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Gula pentosa pada DNA adalah deoksiribosa, sedangkan pada RNA adalah ribosa.', 'is_kunci' => true],
                    ['teks' => 'Basa pirimidin pada DNA terdiri atas Timin dan Sitosin, sedangkan pada RNA Timin digantikan oleh Urasil.', 'is_kunci' => true],
                    ['teks' => 'DNA beruntai ganda heliks panjang (double helix), sedangkan RNA umumnya beruntai tunggal pendek (single strand).', 'is_kunci' => true],
                    ['teks' => 'Kadar RNA di dalam sel selalu bersifat konstan permanen tidak terpengaruh sintesis protein.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Struktur Molekuler DNA dan Aturan Chargaff', '', '', $story4, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari struktur asam nukleat berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pasangan basa Guanin dan Sitosin dihubungkan oleh 3 ikatan hidrogen sehingga lebih kuat daripada ikatan Adenin-Timin.', 'kunci' => 'Benar'],
                    ['teks' => 'Satu unit nukleosida terdiri dari gugus gula, basa nitrogen, dan gugus asam fosfat.', 'kunci' => 'Salah'],
                    ['teks' => 'Replikasi DNA mengikuti model semikonservatif di mana setiap untai lama bertindak sebagai cetakan bagi untai baru.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Struktur Molekuler DNA dan Aturan Chargaff', '', '', $story4, '4. MENJODOHKAN', 'Pasangkan jenis enzim replikasi DNA dengan peranan fungsionalnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Helikase', 'respon' => 'Membuka ikatan hidrogen heliks ganda DNA'],
                ['premis' => 'DNA Polimerase', 'respon' => 'Merangkai rantai nukleotida baru dari ujung 5\' ke 3\''],
                ['premis' => 'Ligase', 'respon' => 'Menyambungkan fragmen Okazaki pada untai lambat (lagging strand)'],
                ['premis' => 'Primase', 'respon' => 'Mensintesis primer RNA pendek sebagai titik awal replikasi']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Helikase', 'respon' => 'Membuka ikatan hidrogen heliks ganda DNA'],
                    ['premis' => 'DNA Polimerase', 'respon' => 'Merangkai rantai nukleotida baru dari ujung 5\' ke 3\''],
                    ['premis' => 'Ligase', 'respon' => 'Menyambungkan fragmen Okazaki pada untai lambat (lagging strand)'],
                    ['premis' => 'Primase', 'respon' => 'Mensintesis primer RNA pendek sebagai titik awal replikasi']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Struktur Molekuler DNA dan Aturan Chargaff', '', '', $story4, '5. ISIAN SINGKAT', 'Model replikasi DNA di mana dua pita heliks ganda DNA induk terpisah dan masing-masing membentuk satu pita komplementer baru disebut model replikasi...'),
            'kunci_jawaban' => 'Semikonservatif',
            'konten_tambahan' => ['rubrik' => 'Semikonservatif']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): Sintesis Protein: Transkripsi & Translasi
        // ══════════════════════════════════════════════════════════════════
        $prompt5 = "Diagram dogma sentral biologi molekuler sintesis protein di dalam sel eukariotik. Menampilkan Transkripsi di dalam nukleus (DNA sense dan antisense dicetak enzim RNA polimerase menjadi mRNA/RNA duta), keluarnya mRNA melalui pori nukleus menuju sitoplasma, serta Translasi di ribosom dengan tRNA membawa antikodon dan asam amino spesifik membentuk rantai polipeptida. Desain grafis biomedis modern.";
        $svg5 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="10" width="110" height="70" rx="8" fill="#fdf4ff" stroke="#a855f7" stroke-width="1.5"/>
            <text x="70" y="28" font-size="9" font-weight="bold" fill="#7e22ce" text-anchor="middle">TRANSKRIPSI</text>
            <text x="70" y="46" font-size="7.5" fill="#6b21a8" text-anchor="middle">Di dalam Nukleus</text>
            <text x="70" y="64" font-size="7.5" fill="#dc2626" text-anchor="middle">DNA &rarr; mRNA (Kodon)</text>
            <path d="M 128 45 L 148 45" stroke="#64748b" stroke-width="2"/>
            <rect x="150" y="10" width="110" height="70" rx="8" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="205" y="28" font-size="9" font-weight="bold" fill="#047857" text-anchor="middle">TRANSLASI</text>
            <text x="205" y="46" font-size="7.5" fill="#065f46" text-anchor="middle">Di Ribosom Sitosol</text>
            <text x="205" y="64" font-size="7.5" fill="#16a34a" text-anchor="middle">mRNA &rarr; Polipeptida</text>
        </svg>';
        $story5 = 'Dogma sentral biologi molekuler menyatakan aliran informasi genetik berlangsung dari DNA &rarr; RNA &rarr; Protein. <strong>Transkripsi</strong> terjadi di dalam nukleus di mana enzim RNA polimerase membaca untai DNA template (antisense 3\' &rarr; 5\') untuk mencetak <u>mRNA / kodon (5\' &rarr; 3\')</u>. Selanjutnya mRNA keluar menuju ribosom untuk proses <strong>Translasi</strong>. Pada translasi, tRNA yang membawa <u>antikodon</u> membaca kodon pada mRNA dan menerjemahkannya menjadi urutan asam amino spesifik. Kodon AUG bertindak sebagai *start codon* (metionin), sedangkan UAA, UAG, dan UGA bertindak sebagai *stop codon*.';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Mekanisme Sintesis Protein: Transkripsi dan Translasi', $prompt5, $svg5, $story5, '1. PILIHAN GANDA', 'Jika urutan basa nitrogen pada rantai DNA antisense (template) adalah <strong>3\'- TAC - GCT - CGA - ACT - 5\'</strong>, maka urutan kodon pada mRNA hasil transkripsi adalah...'),
            'opsi_a' => '5\'- ATG - CGA - GCT - TGA - 3\'',
            'opsi_b' => '5\'- AUG - CGA - GCU - UGA - 3\'',
            'opsi_c' => '5\'- UAC - GCU - CGA - ACU - 3\'',
            'opsi_d' => '5\'- AUC - GCA - GCU - ACU - 3\'',
            'opsi_e' => '5\'- TAC - GCT - CGA - ACT - 3\'',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => '5\'- ATG - CGA - GCT - TGA - 3\'',
                'B' => '5\'- AUG - CGA - GCU - UGA - 3\'',
                'C' => '5\'- UAC - GCU - CGA - ACU - 3\'',
                'D' => '5\'- AUC - GCA - GCU - ACU - 3\'',
                'E' => '5\'- TAC - GCT - CGA - ACT - 3\''
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Mekanisme Sintesis Protein: Transkripsi dan Translasi', '', '', $story5, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai peranan jenis-jenis RNA dalam sintesis protein? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'mRNA (messenger RNA) bertugas membawa pesan kode genetik dari DNA nukleus ke ribosom.',
            'opsi_b' => 'tRNA (transfer RNA) membawa asam amino bebas yang sesuai dengan kodon menuju ribosom.',
            'opsi_c' => 'rRNA (ribosomal RNA) menyusun struktur organel ribosom dan mengkatalisis pembentukan ikatan peptida.',
            'opsi_d' => 'tRNA bertindak sebagai template cetakan replikasi DNA di dalam nukleus.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'mRNA (messenger RNA) bertugas membawa pesan kode genetik dari DNA nukleus ke ribosom.', 'is_kunci' => true],
                    ['teks' => 'tRNA (transfer RNA) membawa asam amino bebas yang sesuai dengan kodon menuju ribosom.', 'is_kunci' => true],
                    ['teks' => 'rRNA (ribosomal RNA) menyusun struktur organel ribosom dan mengkatalisis pembentukan ikatan peptida.', 'is_kunci' => true],
                    ['teks' => 'tRNA bertindak sebagai template cetakan replikasi DNA di dalam nukleus.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Mekanisme Sintesis Protein: Transkripsi dan Translasi', '', '', $story5, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari kode genetik sintesis protein berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kodon AUG berfungsi ganda sebagai kodon awal (start codon) sekaligus mengkode asam amino metionin.', 'kunci' => 'Benar'],
                    ['teks' => 'Kodon stop (UAA, UAG, UGA) mengkode asam amino penutup khusus pada ujung rantai polipeptida.', 'kunci' => 'Salah'],
                    ['teks' => 'Ikatan yang menghubungkan gugus karboksil satu asam amino dengan gugus amina asam amino berikutnya adalah ikatan peptida.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Mekanisme Sintesis Protein: Transkripsi dan Translasi', '', '', $story5, '4. MENJODOHKAN', 'Pasangkan istilah genetika molekuler dengan penjelasannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Kodon', 'respon' => 'Urutan triplet 3 basa nitrogen pada molekul mRNA'],
                ['premis' => 'Antikodon', 'respon' => 'Urutan triplet 3 basa pada tRNA yang komplementer dengan mRNA'],
                ['premis' => 'Sense / Coding Strand', 'respon' => 'Untai DNA yang memiliki urutan basa identik dengan mRNA (kecuali T diganti U)'],
                ['premis' => 'Polisom', 'respon' => 'Kumpulan beberapa ribosom yang membaca satu molekul mRNA sekaligus']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Kodon', 'respon' => 'Urutan triplet 3 basa nitrogen pada molekul mRNA'],
                    ['premis' => 'Antikodon', 'respon' => 'Urutan triplet 3 basa pada tRNA yang komplementer dengan mRNA'],
                    ['premis' => 'Sense / Coding Strand', 'respon' => 'Untai DNA yang memiliki urutan basa identik dengan mRNA (kecuali T diganti U)'],
                    ['premis' => 'Polisom', 'respon' => 'Kumpulan beberapa ribosom yang membaca satu molekul mRNA sekaligus']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Mekanisme Sintesis Protein: Transkripsi dan Translasi', '', '', $story5, '5. ISIAN SINGKAT', 'Kodon inisiasi triplet basa nitrogen yang menandai dimulainya proses translasi sintesis protein di ribosom adalah...'),
            'kunci_jawaban' => 'AUG',
            'konten_tambahan' => ['rubrik' => 'AUG']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): Pembelahan Sel: Mitosis vs Meiosis
        // ══════════════════════════════════════════════════════════════════
        $prompt6 = "Diagram sitogenetika komparasi pembelahan sel Mitosis dan Meiosis. Menampilkan tahapan Profase, Metafase (kromosom berjajar di lempeng ekuator), Anafase (pemisahan kromatid saudara ke kutub berlawanan), dan Telofase. Detail crossing over (pindah silang) pada kiasma kromatid non-saudara selama Profase I Meiosis. Ilustrasi visual seluler resolusi tinggi.";
        $svg6 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="115" height="74" rx="8" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="72" y="26" font-size="8.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle">MITOSIS</text>
            <text x="72" y="42" font-size="7.5" fill="#1e40af" text-anchor="middle">Sel Somatik Tubuh</text>
            <text x="72" y="56" font-size="7.5" fill="#1e40af" text-anchor="middle">1x Pembelahan</text>
            <text x="72" y="70" font-size="7.5" fill="#2563eb" font-weight="bold" text-anchor="middle">Hasil: 2 Sel Diploid (2n)</text>
            <rect x="140" y="8" width="115" height="74" rx="8" fill="#fef2f2" stroke="#ef4444" stroke-width="1.5"/>
            <text x="197" y="26" font-size="8.5" font-weight="bold" fill="#b91c1c" text-anchor="middle">MEIOSIS</text>
            <text x="197" y="42" font-size="7.5" fill="#7f1d1d" text-anchor="middle">Sel Gamet (Kelamin)</text>
            <text x="197" y="56" font-size="7.5" fill="#7f1d1d" text-anchor="middle">2x Pembelahan (I &amp; II)</text>
            <text x="197" y="70" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">Hasil: 4 Sel Haploid (n)</text>
        </svg>';
        $story6 = 'Pembelahan sel terbagi menjadi mitosis dan meiosis. <u>Mitosis</u> terjadi pada sel-sel somatis (tubuh) untuk pertumbuhan dan regenerasi jaringan yang rusak, menghasilkan 2 sel anakan yang identik secara genetik dengan sel induk (diploid, 2n). Sebaliknya, <u>Meiosis</u> merupakan pembelahan reduksi yang terjadi pada organ reproduksi untuk pembentukan gamet (gametogenesis), menghasilkan 4 sel anakan dengan jumlah kromosom separuh dari induk (haploid, n). Pada tahap <strong>Profase I Meiosis</strong>, terjadi peristiwa <em>crossing over</em> (pindah silang) pada kiasma yang menciptakan rekombinasi variasi genetik.';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Sitologi Pembelahan Sel: Mitosis dan Meiosis', $prompt6, $svg6, $story6, '1. PILIHAN GANDA', 'Fase pembelahan sel di mana kromosom berjajar rapi dalam satu baris pada <u>bidang pembelahan (lempeng ekuator)</u> dengan sentromer terikat benang spindel adalah...'),
            'opsi_a' => 'Profase',
            'opsi_b' => 'Metafase',
            'opsi_c' => 'Anafase',
            'opsi_d' => 'Telofase',
            'opsi_e' => 'Interfase',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Profase',
                'B' => 'Metafase',
                'C' => 'Anafase',
                'D' => 'Telofase',
                'E' => 'Interfase'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Sitologi Pembelahan Sel: Mitosis dan Meiosis', '', '', $story6, '2. PG KOMPLEKS (CENTANG)', 'Karakteristik manakah di bawah ini yang merupakan ciri khusus dari <strong>Pembelahan Meiosis</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Terjadi reduksi jumlah kromosom dari diploid (2n) menjadi haploid (n).',
            'opsi_b' => 'Terjadi peristiwa pindah silang (crossing over) antara kromosom homolog pada fase pakiten profase I.',
            'opsi_c' => 'Menghasilkan 4 sel anakan yang memiliki kombinasi genetik bervariasi.',
            'opsi_d' => 'Berfungsi untuk menutup luka dan menambah tinggi batang tanaman.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Terjadi reduksi jumlah kromosom dari diploid (2n) menjadi haploid (n).', 'is_kunci' => true],
                    ['teks' => 'Terjadi peristiwa pindah silang (crossing over) antara kromosom homolog pada fase pakiten profase I.', 'is_kunci' => true],
                    ['teks' => 'Menghasilkan 4 sel anakan yang memiliki kombinasi genetik bervariasi.', 'is_kunci' => true],
                    ['teks' => 'Berfungsi untuk menutup luka dan menambah tinggi batang tanaman.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Sitologi Pembelahan Sel: Mitosis dan Meiosis', '', '', $story6, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari gametogenesis pada manusia berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Spermatogenesis pada pria menghasilkan 4 sel spermatozoa fungsional yang fertil dari satu spermatosit primer.', 'kunci' => 'Benar'],
                    ['teks' => 'Oogenesis pada wanita menghasilkan 4 sel ovum fungsional matang yang siap dibuahi.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada oogenesis terbentuk 1 sel ovum fungsional dan 3 badan polar (polosit) yang kemudian mengalami degenerasi.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Sitologi Pembelahan Sel: Mitosis dan Meiosis', '', '', $story6, '4. MENJODOHKAN', 'Pasangkan fase pembelahan sel dengan peristiwa khas yang terjadi di dalamnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Profase', 'respon' => 'Kromatin memadat menjadi kromosom dan membran inti mulai menghilang'],
                ['premis' => 'Metafase', 'respon' => 'Kromosom berderet di bidang ekuatorial pembelahan'],
                ['premis' => 'Anafase', 'respon' => 'Kromatid saudara terpisah dan bergerak menuju ke kutub berlawanan'],
                ['premis' => 'Telofase', 'respon' => 'Membran inti terbentuk kembali dan terjadi sitokinesis pembagian sitoplasma']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Profase', 'respon' => 'Kromatin memadat menjadi kromosom dan membran inti mulai menghilang'],
                    ['premis' => 'Metafase', 'respon' => 'Kromosom berderet di bidang ekuatorial pembelahan'],
                    ['premis' => 'Anafase', 'respon' => 'Kromatid saudara terpisah dan bergerak menuju ke kutub berlawanan'],
                    ['premis' => 'Telofase', 'respon' => 'Membran inti terbentuk kembali dan terjadi sitokinesis pembagian sitoplasma']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Sitologi Pembelahan Sel: Mitosis dan Meiosis', '', '', $story6, '5. ISIAN SINGKAT', 'Titik persilangan atau perlekatan antara kromatid non-saudara tempat terjadinya pertukaran segmen gen (pindah silang) pada Profase I Meiosis disebut...'),
            'kunci_jawaban' => 'Kiasma',
            'konten_tambahan' => ['rubrik' => 'Kiasma (atau Kiasmata)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): Pola Pewarisan Sifat Hukum Mendel
        // ══════════════════════════════════════════════════════════════════
        $prompt7 = "Diagram persilangan genetika Mendel dihibrid kacang ercis (Pisum sativum) sifat bentuk biji (Bulat B vs Keriput b) dan warna biji (Kuning K vs Hijau k). Papan Catur Punnett 16 kotak persilangan F1 heterozigot BbKk x BbKk dengan perbandingan fenotipe 9 Bulat Kuning : 3 Bulat Hijau : 3 Keriput Kuning : 1 Keriput Hijau. Infografis genetika klasik elegan.";
        $svg7 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="115" height="74" rx="8" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="72" y="26" font-size="8.5" font-weight="bold" fill="#b45309" text-anchor="middle">HUKUM I MENDEL</text>
            <text x="72" y="44" font-size="7.5" fill="#78350f" text-anchor="middle">Segregasi Bebas</text>
            <text x="72" y="58" font-size="7.5" fill="#78350f" text-anchor="middle">Persilangan Monohibrid</text>
            <text x="72" y="72" font-size="8" fill="#d97706" font-weight="bold" text-anchor="middle">Rasio F2 = 3 : 1</text>
            <rect x="140" y="8" width="115" height="74" rx="8" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="197" y="26" font-size="8.5" font-weight="bold" fill="#047857" text-anchor="middle">HUKUM II MENDEL</text>
            <text x="197" y="44" font-size="7.5" fill="#065f46" text-anchor="middle">Asortasi Bebas</text>
            <text x="197" y="58" font-size="7.5" fill="#065f46" text-anchor="middle">Persilangan Dihibrid</text>
            <text x="197" y="72" font-size="8" fill="#059669" font-weight="bold" text-anchor="middle">Rasio F2 = 9:3:3:1</text>
        </svg>';
        $story7 = 'Gregor Mendel merumuskan dua hukum dasar hereditas: (1) <u>Hukum Segregasi Bebas (Hukum I Mendel)</u> yang menyatakan pasangan alel memisah secara bebas pada saat pembentukan gamet (terbukti pada persilangan monohibrid dengan rasio fenotip F2 = 3 dominan : 1 resesif); dan (2) <u>Hukum Asortasi Bebas (Hukum II Mendel)</u> yang menyatakan alel dari lokus yang berbeda berpasangan secara bebas dalam pembentukan gamet (terbukti pada persilangan dihibrid heterozigot BbKk x BbKk yang menghasilkan rasio fenotip F2 = <strong>9 : 3 : 3 : 1</strong>).';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Pola Pewarisan Sifat Hukum Mendel', $prompt7, $svg7, $story7, '1. PILIHAN GANDA', 'Pada persilangan dihibrid tanaman kacang ercis biji bulat kuning (BbKk) yang disilangkan dengan sesamanya, dari total 160 butir biji keturunan F2 yang dipanen, berapakah perkiraan jumlah biji yang berfenotip <u>keriput hijau</u>?'),
            'opsi_a' => '90 butir',
            'opsi_b' => '30 butir',
            'opsi_c' => '10 butir',
            'opsi_d' => '16 butir',
            'opsi_e' => '40 butir',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => '90 butir',
                'B' => '30 butir',
                'C' => '10 butir',
                'D' => '16 butir',
                'E' => '40 butir'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Pola Pewarisan Sifat Hukum Mendel', '', '', $story7, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai macam gamet yang dibentuk oleh suatu individu menurut Hukum Asortasi Mendel? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Individu bergenotipe AaBb dapat membentuk 4 macam gamet yang berbeda, yaitu AB, Ab, aB, dan ab.',
            'opsi_b' => 'Jumlah macam gamet dapat dihitung menggunakan rumus 2^n, dengan n adalah jumlah pasangan alel yang heterozigot.',
            'opsi_c' => 'Individu homozigot dominan AABB hanya mampu membentuk 1 macam gamet saja, yaitu AB.',
            'opsi_d' => 'Individu AaBbCc yang heterozigot pada 3 lokus gen akan membentuk 16 macam gamet berbeda.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Individu bergenotipe AaBb dapat membentuk 4 macam gamet yang berbeda, yaitu AB, Ab, aB, dan ab.', 'is_kunci' => true],
                    ['teks' => 'Jumlah macam gamet dapat dihitung menggunakan rumus 2^n, dengan n adalah jumlah pasangan alel yang heterozigot.', 'is_kunci' => true],
                    ['teks' => 'Individu homozigot dominan AABB hanya mampu membentuk 1 macam gamet saja, yaitu AB.', 'is_kunci' => true],
                    ['teks' => 'Individu AaBbCc yang heterozigot pada 3 lokus gen akan membentuk 16 macam gamet berbeda.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Pola Pewarisan Sifat Hukum Mendel', '', '', $story7, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari teknik persilangan genetika berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Uji silang (Testcross) adalah menyilangkan individu berfenotip dominan yang belum diketahui genotipenya dengan individu homozigot resesif.', 'kunci' => 'Benar'],
                    ['teks' => 'Jika hasil testcross menghasilkan keturunan dengan fenotip 100% dominan, maka induk yang diuji pasti bergenotipe heterozigot.', 'kunci' => 'Salah'],
                    ['teks' => 'Backcross adalah perkawinan silang antara individu F1 dengan salah satu induknya (baik induk homozigot dominan maupun resesif).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Pola Pewarisan Sifat Hukum Mendel', '', '', $story7, '4. MENJODOHKAN', 'Pasangkan istilah dasar genetika dengan pengertiannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Genotipe', 'respon' => 'Komposisi susunan genetik penyusun sifat yang tidak tampak dari luar'],
                ['premis' => 'Fenotipe', 'respon' => 'Karakteristik atau sifat morfologis yang dapat diamati secara fisik'],
                ['premis' => 'Alel', 'respon' => 'Pasangan versi alternatif dari suatu gen pada lokus yang bersesuaian'],
                ['premis' => 'Heterozigot', 'respon' => 'Kondisi individu memiliki sepasang alel yang berbeda (misal Aa atau Bb)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Genotipe', 'respon' => 'Komposisi susunan genetik penyusun sifat yang tidak tampak dari luar'],
                    ['premis' => 'Fenotipe', 'respon' => 'Karakteristik atau sifat morfologis yang dapat diamati secara fisik'],
                    ['premis' => 'Alel', 'respon' => 'Pasangan versi alternatif dari suatu gen pada lokus yang bersesuaian'],
                    ['premis' => 'Heterozigot', 'respon' => 'Kondisi individu memiliki sepasang alel yang berbeda (misal Aa atau Bb)']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Pola Pewarisan Sifat Hukum Mendel', '', '', $story7, '5. ISIAN SINGKAT', 'Teknik perkawinan silang antara suatu individu berfenotip dominan dengan individu homozigot resesif untuk menguji kemurnian genotipenya dinamakan persilangan...'),
            'kunci_jawaban' => 'Testcross',
            'konten_tambahan' => ['rubrik' => 'Testcross (atau Uji Silang)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): Hereditas Manusia: Pautan Seks & Pedigree
        // ══════════════════════════════════════════════════════════════════
        $prompt8 = "Diagram silsilah keluarga (Pedigree Chart) pewarisan kelainan genetik terpaut kromosom X resesif (Hemofilia dan Buta Warna). Menampilkan simbol kotak (laki-laki), lingkaran (perempuan), simbol terisi hitam penuh (penderita) dan setengah hitam (wanita karier pembawa sifat heterozigot). Bagan pohon keluarga ilmiah medis beresolusi tinggi.";
        $svg8 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="35" y="15" width="22" height="22" fill="#38bdf8" stroke="#0284c7" stroke-width="1.5"/>
            <text x="46" y="29" font-size="7.5" font-weight="bold" fill="#0f172a" text-anchor="middle">XY</text>
            <line x1="57" y1="26" x2="85" y2="26" stroke="#64748b" stroke-width="1.5"/>
            <circle cx="96" cy="26" r="11" fill="#fbcfe8" stroke="#db2777" stroke-width="1.5"/>
            <text x="96" y="29" font-size="7" font-weight="bold" fill="#831843" text-anchor="middle">XhX</text>
            <line x1="71" y1="26" x2="71" y2="52" stroke="#64748b" stroke-width="1.5"/>
            <line x1="45" y1="52" x2="95" y2="52" stroke="#64748b" stroke-width="1.5"/>
            <rect x="35" y="55" width="20" height="20" fill="#f87171" stroke="#dc2626" stroke-width="1.5"/>
            <text x="45" y="68" font-size="7" font-weight="bold" fill="#ffffff" text-anchor="middle">XhY</text>
            <circle cx="85" cy="65" r="10" fill="#fbcfe8" stroke="#db2777" stroke-width="1.5"/>
            <text x="145" y="38" font-size="8.5" font-weight="bold" fill="#0f172a">Pautan Kromosom X</text>
            <text x="145" y="52" font-size="7.5" fill="#dc2626">Hemofilia (Xh)</text>
            <text x="145" y="65" font-size="7.5" fill="#0284c7">Buta Warna (Xcb)</text>
        </svg>';
        $story8 = 'Pewarisan sifat pada manusia mencakup gen yang terpaut autosom (kromosom tubuh) dan terpaut gonosom (kromosom seks X dan Y). Penyakit kelainan darah sukar membeku (<strong>Hemofilia</strong>) dan ketidakmampuan membedakan spektrum warna merah-hijau (<strong>Buta Warna</strong>) dikendalikan oleh alel resesif yang <u>terpaut pada kromosom X</u>. Karena laki-laki hanya memiliki satu kromosom X (XY / hemizigot), keberadaan satu alel resesif (XhY atau XcbY) sudah cukup memunculkan penyakit, sedangkan wanita (XX) dapat berstatus normal pembawa sifat (karier, XHXh) tanpa mengalami gejala.';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Hereditas Manusia dan Penyakit Terpaut Seks', $prompt8, $svg8, $story8, '1. PILIHAN GANDA', 'Seorang wanita karier hemofilia (XHXh) menikah dengan seorang laki-laki normal (XHY). Berapakah peluang persentase kemungkinan lahirnya <u>anak laki-laki yang menderita hemofilia</u> dari seluruh total kemungkinan anak mereka?'),
            'opsi_a' => '0%',
            'opsi_b' => '25%',
            'opsi_c' => '50%',
            'opsi_d' => '75%',
            'opsi_e' => '100%',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => '0%',
                'B' => '25%',
                'C' => '50%',
                'D' => '75%',
                'E' => '100%'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Hereditas Manusia dan Penyakit Terpaut Seks', '', '', $story8, '2. PG KOMPLEKS (CENTANG)', 'Penyakit atau kelainan genetik manakah berikut ini yang <strong>diwariskan melalui gen autosom</strong> (tidak terpaut jenis kelamin)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Albinisme (Albino / ketiadaan pigmen melanin)',
            'opsi_b' => 'Talasemia (gangguan sintesis rantai hemoglobin darah)',
            'opsi_c' => 'Anemia Sel Sabit (Sickle Cell Anemia)',
            'opsi_d' => 'Hemofilia (gangguan faktor pembekuan darah VIII/IX)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Albinisme (Albino / ketiadaan pigmen melanin)', 'is_kunci' => true],
                    ['teks' => 'Talasemia (gangguan sintesis rantai hemoglobin darah)', 'is_kunci' => true],
                    ['teks' => 'Anemia Sel Sabit (Sickle Cell Anemia)', 'is_kunci' => true],
                    ['teks' => 'Hemofilia (gangguan faktor pembekuan darah VIII/IX)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Hereditas Manusia dan Penyakit Terpaut Seks', '', '', $story8, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pewarisan golongan darah sistem ABO berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pasangan suami istri bergolongan darah A heterozigot (IAIO) dan B heterozigot (IBIO) dapat memiliki anak dengan seluruh 4 variasi golongan darah (A, B, AB, dan O).', 'kunci' => 'Benar'],
                    ['teks' => 'Seorang ayah bergolongan darah AB dapat memiliki anak kandung bergolongan darah O.', 'kunci' => 'Salah'],
                    ['teks' => 'Pewarisan sifat buta warna pada anak laki-laki selalu diturunkan dari kromosom X pihak ibu (criss-cross inheritance).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Hereditas Manusia dan Penyakit Terpaut Seks', '', '', $story8, '4. MENJODOHKAN', 'Pasangkan kelainan genetika manusia dengan pola penurunannya yang tepat:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Hemofilia', 'respon' => 'Terpaut kromosom X bersifat resesif'],
                ['premis' => 'Hipertrikosis (Telinga Berambut)', 'respon' => 'Terpaut kromosom Y (Holandrik)'],
                ['premis' => 'Albino', 'respon' => 'Autosom resesif pada gen tirosinase'],
                ['premis' => 'Polidaktili (Jari Berlebih)', 'respon' => 'Autosom dominan']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Hemofilia', 'respon' => 'Terpaut kromosom X bersifat resesif'],
                    ['premis' => 'Hipertrikosis (Telinga Berambut)', 'respon' => 'Terpaut kromosom Y (Holandrik)'],
                    ['premis' => 'Albino', 'respon' => 'Autosom resesif pada gen tirosinase'],
                    ['premis' => 'Polidaktili (Jari Berlebih)', 'respon' => 'Autosom dominan']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Hereditas Manusia dan Penyakit Terpaut Seks', '', '', $story8, '5. ISIAN SINGKAT', 'Pola pewarisan sifat pada pautan seks di mana sifat dari ayah diwariskan kepada anak perempuan untuk kemudian diteruskan ke cucu laki-laki disebut pewarisan silang atau...'),
            'kunci_jawaban' => 'Criss-cross inheritance',
            'konten_tambahan' => ['rubrik' => 'Criss-cross inheritance (atau Pewarisan Menyilang)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): Mutasi Gen & Aneuploidi Kromosom
        // ══════════════════════════════════════════════════════════════════
        $prompt9 = "Diagram sitogenetika mutasi kromosom manusia (Aneuploidi). Menampilkan kariotipe kromosom sindrom Down dengan trisomi pada pasangan kromosom nomor 21 (47, XX+21 atau 47, XY+21) akibat peristiwa gagal berpisah (nondisjunction) saat meiosis. Sisi kanan menampilkan aberasi struktur kromosom (delesi, duplikasi, translokasi timbal balik). Ilustrasi ilmiah sitogenetika resolusi tinggi.";
        $svg9 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="115" height="74" rx="8" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="72" y="26" font-size="8.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle">SINDROM DOWN</text>
            <text x="72" y="44" font-size="7.5" fill="#1e40af" text-anchor="middle">Trisomi Autosom 21</text>
            <text x="72" y="58" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">Kariotipe: 47, XY +21</text>
            <text x="72" y="70" font-size="6.5" fill="#64748b" text-anchor="middle">Nondisjunction Meiosis</text>
            <rect x="140" y="8" width="115" height="74" rx="8" fill="#fdf4ff" stroke="#a855f7" stroke-width="1.5"/>
            <text x="197" y="26" font-size="8.5" font-weight="bold" fill="#7e22ce" text-anchor="middle">SINDROM KLINEFELTER</text>
            <text x="197" y="44" font-size="7.5" fill="#6b21a8" text-anchor="middle">Trisomi Gonosom Seks</text>
            <text x="197" y="58" font-size="7.5" fill="#dc2626" font-weight="bold" text-anchor="middle">Kariotipe: 47, XXY</text>
            <text x="197" y="70" font-size="6.5" fill="#64748b" text-anchor="middle">Laki-laki Berpayudara</text>
        </svg>';
        $story9 = 'Mutasi adalah perubahan spontan ataupun terinduksi pada materi genetik (DNA atau kromosom) yang dapat diwariskan bila terjadi pada sel germinal. Mutasi gen (titik) meliputi substitusi basa (transisi / transversi) serta mutasi pergeseran kerangka baca (<em>frameshift mutation</em>) akibat delesi atau insersi basa nitrogen. Sedangkan mutasi kromosom melibatkan aberasi struktur (delesi, duplikasi, inversi, translokasi) maupun perubahan jumlah kromosom (aneuploidi) yang dipicu peristiwa <strong>gagal berpisah (nondisjunction)</strong> benang spindel saat anafase meiosis, seperti Sindrom Down (Trisomi 21), Sindrom Turner (45, XO), dan Sindrom Klinefelter (47, XXY).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Mutasi Genetik dan Aneuploidi Kromosom Manusia', $prompt9, $svg9, $story9, '1. PILIHAN GANDA', 'Seorang individu penderita <u>Sindrom Turner</u> memiliki kelainan jumlah kromosom akibat monosomi gonosom dengan formula kariotipe...'),
            'opsi_a' => '47, XX +21',
            'opsi_b' => '47, XXY',
            'opsi_c' => '45, XO',
            'opsi_d' => '47, XYY',
            'opsi_e' => '47, XXX',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => '47, XX +21',
                'B' => '47, XXY',
                'C' => '45, XO',
                'D' => '47, XYY',
                'E' => '47, XXX'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Mutasi Genetik dan Aneuploidi Kromosom Manusia', '', '', $story9, '2. PG KOMPLEKS (CENTANG)', 'Manakah di bawah ini yang tergolong agen-agen penyebab mutasi (<strong>Mutagen</strong>) yang sering memicu kerusakan rantai DNA? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Sinar radiasi pengion (sinar X, sinar gamma, radiasi kosmik)',
            'opsi_b' => 'Radiasi ultraviolet (UV) sinar matahari yang memicu pembentukan dimer timin',
            'opsi_c' => 'Zat kimia karsinogenik seperti tar rokok, benzena, dan gas mustard',
            'opsi_d' => 'Air murni (akuades) yang diminum dalam kondisi dingin',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Sinar radiasi pengion (sinar X, sinar gamma, radiasi kosmik)', 'is_kunci' => true],
                    ['teks' => 'Radiasi ultraviolet (UV) sinar matahari yang memicu pembentukan dimer timin', 'is_kunci' => true],
                    ['teks' => 'Zat kimia karsinogenik seperti tar rokok, benzena, dan gas mustard', 'is_kunci' => true],
                    ['teks' => 'Air murni (akuades) yang diminum dalam kondisi dingin', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Mutasi Genetik dan Aneuploidi Kromosom Manusia', '', '', $story9, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari jenis mutasi titik pada gen berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Mutasi diam (silent mutation) adalah perubahan basa nitrogen yang tidak mengubah jenis asam amino yang dikode.', 'kunci' => 'Benar'],
                    ['teks' => 'Mutasi salah arti (missense mutation) selalu mengubah kodon asam amino normal menjadi kodon stop terminasi.', 'kunci' => 'Salah'],
                    ['teks' => 'Mutasi tanpa arti (nonsense mutation) mengubah kodon pengkode asam amino menjadi salah satu dari kodon stop prematurely.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Mutasi Genetik dan Aneuploidi Kromosom Manusia', '', '', $story9, '4. MENJODOHKAN', 'Pasangkan aberasi struktur kromosom dengan definisinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Delesi Kromosom', 'respon' => 'Hilangnya patahan segmen materi genetik pada lengan kromosom'],
                ['premis' => 'Duplikasi', 'respon' => 'Penggandaan sebagian ruas kromosom sehingga gen hadir ganda'],
                ['premis' => 'Inversi', 'respon' => 'Patahan kromosom tersambung kembali dengan urutan terbalik 180 derajat'],
                ['premis' => 'Translokasi Resiprok', 'respon' => 'Pertukaran segmen kromosom antara dua kromosom non-homolog']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Delesi Kromosom', 'respon' => 'Hilangnya patahan segmen materi genetik pada lengan kromosom'],
                    ['premis' => 'Duplikasi', 'respon' => 'Penggandaan sebagian ruas kromosom sehingga gen hadir ganda'],
                    ['premis' => 'Inversi', 'respon' => 'Patahan kromosom tersambung kembali dengan urutan terbalik 180 derajat'],
                    ['premis' => 'Translokasi Resiprok', 'respon' => 'Pertukaran segmen kromosom antara dua kromosom non-homolog']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Mutasi Genetik dan Aneuploidi Kromosom Manusia', '', '', $story9, '5. ISIAN SINGKAT', 'Kegagalan pasangan kromosom homolog atau kromatid saudara untuk berpisah ke kutub berlawanan pada saat pembelahan meiosis disebut peristiwa...'),
            'kunci_jawaban' => 'Nondisjunction',
            'konten_tambahan' => ['rubrik' => 'Nondisjunction (atau Gagal Berpisah)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): Bioteknologi Modern: Rekayasa Genetika
        // ══════════════════════════════════════════════════════════════════
        $prompt10 = "Diagram alur bioteknologi rekayasa genetika DNA Rekombinan untuk produksi insulin manusia skala industri. Menampilkan plasmid bakteri Escherichia coli dipotong enzim restriksi endonuklease, penyisipan gen insulin manusia dengan perekat enzim DNA ligase membentuk plasmid rekombinan, transformasi ke sel inang bakteri, dan bioreaktor fermentasi penghasil hormon insulin murni. Vektor bioteknologi presisi tinggi.";
        $svg10 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="45" cy="45" r="28" fill="none" stroke="#2563eb" stroke-width="3"/>
            <text x="45" y="48" font-size="8" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Plasmid Bakteri</text>
            <path d="M 80 45 L 115 45" stroke="#64748b" stroke-width="2" marker-end="url(#arr)"/>
            <text x="98" y="38" font-size="7" fill="#dc2626" font-weight="bold">Restriksi</text>
            <rect x="120" y="20" width="60" height="50" rx="8" fill="#fdf4ff" stroke="#a855f7" stroke-width="1.5"/>
            <text x="150" y="42" font-size="7.5" font-weight="bold" fill="#7e22ce" text-anchor="middle">+ Gen Insulin</text>
            <text x="150" y="55" font-size="7" fill="#6b21a8" text-anchor="middle">(Enzim Ligase)</text>
            <path d="M 185 45 L 210 45" stroke="#64748b" stroke-width="2"/>
            <rect x="215" y="20" width="50" height="50" rx="8" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="240" y="42" font-size="8" font-weight="bold" fill="#047857" text-anchor="middle">Insulin</text>
            <text x="240" y="55" font-size="7" fill="#065f46" text-anchor="middle">Rekombinan</text>
        </svg>';
        $story10 = 'Bioteknologi modern memanfaatkan teknik rekayasa genetika pada tingkat molekuler untuk menghasilkan produk yang bermanfaat bagi manusia. Salah satu teknik utamanya adalah <u>DNA Rekombinan</u>, yaitu menggabungkan fragmen gen fungsional target (misalnya gen penyandi hormon insulin manusia) ke dalam vektor pembawa berupa <strong>plasmid sirkuler bakteri</strong>. Pemotongan rantai DNA pada urutan basa pengenal spesifik (palindrom) dilakukan oleh <em>enzim restriksi endonuklease</em>, sedangkan penyambungan ujung DNA dibantu oleh <em>enzim DNA ligase</em>.';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Bioteknologi Modern: Rekayasa DNA Rekombinan', $prompt10, $svg10, $story10, '1. PILIHAN GANDA', 'Enzim yang berperan sebagai "gunting biologi" untuk <u>memotong untai ganda DNA</u> pada urutan nukleotida spesifik dalam teknik kloning molekuler adalah...'),
            'opsi_a' => 'DNA Ligase',
            'opsi_b' => 'Restriksi Endonuklease',
            'opsi_c' => 'DNA Polimerase',
            'opsi_d' => 'Enzim Amilase',
            'opsi_e' => 'RNA Transkriptase Balik',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'DNA Ligase',
                'B' => 'Restriksi Endonuklease',
                'C' => 'DNA Polimerase',
                'D' => 'Enzim Amilase',
                'E' => 'RNA Transkriptase Balik'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Bioteknologi Modern: Rekayasa DNA Rekombinan', '', '', $story10, '2. PG KOMPLEKS (CENTANG)', 'Penerapan bioteknologi modern manakah yang memanfaatkan <strong>teknik fusi protoplas (hibridoma)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Produksi antibodi monoklonal spesifik untuk terapi kanker.',
            'opsi_b' => 'Peleburan sel limfosit B dengan sel mieloma (kanker) yang mampu membelah terus menerus.',
            'opsi_c' => 'Pembuatan tanaman hibrida unggul antar-spesies yang sulit disilangkan secara generatif.',
            'opsi_d' => 'Fermentasi tradisional singkong menjadi tape menggunakan ragi.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Produksi antibodi monoklonal spesifik untuk terapi kanker.', 'is_kunci' => true],
                    ['teks' => 'Peleburan sel limfosit B dengan sel mieloma (kanker) yang mampu membelah terus menerus.', 'is_kunci' => true],
                    ['teks' => 'Pembuatan tanaman hibrida unggul antar-spesies yang sulit disilangkan secara generatif.', 'is_kunci' => true],
                    ['teks' => 'Fermentasi tradisional singkong menjadi tape menggunakan ragi.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Bioteknologi Modern: Rekayasa DNA Rekombinan', '', '', $story10, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari prinsip kultur jaringan tumbuhan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kultur jaringan didasarkan pada sifat totipotensi sel tumbuhan, yaitu kemampuan satu sel untuk tumbuh menjadi individu utuh.', 'kunci' => 'Benar'],
                    ['teks' => 'Tanaman hasil kultur jaringan memiliki sifat genetik yang bervariasi berbeda total dari tanaman induk asalnya.', 'kunci' => 'Salah'],
                    ['teks' => 'Kultur jaringan memungkinkan perbanyakan bibit tanaman unggul bebas virus dalam jumlah banyak dalam waktu singkat.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Bioteknologi Modern: Rekayasa DNA Rekombinan', '', '', $story10, '4. MENJODOHKAN', 'Pasangkan produk bioteknologi modern dengan mikroorganisme yang dimanfaatkannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Hormon Insulin Rekombinan', 'respon' => 'Escherichia coli dengan plasmid gen insulin manusia'],
                ['premis' => 'Biopestisida Tanaman Jagung Bt', 'respon' => 'Bacillus thuringiensis penghasil kristal protein endotoksin'],
                ['premis' => 'Kultur Jaringan Tanaman', 'respon' => 'Eksplan sel meristematik pada media agar nutrisi steril'],
                ['premis' => 'Vektor Pembawa Gen Tanaman', 'respon' => 'Agrobacterium tumefaciens dengan Ti-plasmid']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Hormon Insulin Rekombinan', 'respon' => 'Escherichia coli dengan plasmid gen insulin manusia'],
                    ['premis' => 'Biopestisida Tanaman Jagung Bt', 'respon' => 'Bacillus thuringiensis penghasil kristal protein endotoksin'],
                    ['premis' => 'Kultur Jaringan Tanaman', 'respon' => 'Eksplan sel meristematik pada media agar nutrisi steril'],
                    ['premis' => 'Vektor Pembawa Gen Tanaman', 'respon' => 'Agrobacterium tumefaciens dengan Ti-plasmid']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Bioteknologi Modern: Rekayasa DNA Rekombinan', '', '', $story10, '5. ISIAN SINGKAT', 'Enzim yang bertindak sebagai "lem biologi" untuk menyambungkan kembali ikatan fosfodiester antara fragmen DNA target dengan vektor plasmid adalah enzim DNA...'),
            'kunci_jawaban' => 'Ligase',
            'konten_tambahan' => ['rubrik' => 'Ligase (atau DNA Ligase)']
        ];

        return $list;
    }
}
