<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class BiologiKelasX {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): Keanekaragaman Hayati (Biodiversitas) Indonesia
        // ══════════════════════════════════════════════════════════════════
        $prompt1 = "Infografis keanekaragaman hayati Indonesia beresolusi 4K dengan garis Wallace dan Weber membagi wilayah Oriental (Gajah Sumatera, Harimau), Peralihan (Anoa, Babirusa, Komodo), dan Australis (Cendrawasih, Kanguru Pohon). Latar belakang hutan hujan tropis berkabut, diagram vektor ilmiah jernih.";
        $svg1 = '<svg width="300" height="95" viewBox="0 0 300 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="8" width="90" height="78" rx="8" fill="#ecfdf5" stroke="#10b981" stroke-width="2"/>
            <text x="50" y="28" font-size="10" font-weight="bold" fill="#047857" text-anchor="middle">ZONA ORIENTAL</text>
            <text x="50" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Gajah, Harimau</text>
            <text x="50" y="68" font-size="9" fill="#059669" text-anchor="middle">Sumatera & Jawa</text>
            <line x1="100" y1="12" x2="100" y2="84" stroke="#ef4444" stroke-width="2" stroke-dasharray="4"/>
            <text x="100" y="92" font-size="7.5" fill="#dc2626" text-anchor="middle">Garis Wallace</text>
            <rect x="105" y="8" width="90" height="78" rx="8" fill="#eff6ff" stroke="#3b82f6" stroke-width="2"/>
            <text x="150" y="28" font-size="10" font-weight="bold" fill="#1d4ed8" text-anchor="middle">PERALIHAN</text>
            <text x="150" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Anoa, Komodo</text>
            <text x="150" y="68" font-size="9" fill="#2563eb" text-anchor="middle">Sulawesi & NTT</text>
            <line x1="200" y1="12" x2="200" y2="84" stroke="#ef4444" stroke-width="2" stroke-dasharray="4"/>
            <text x="200" y="92" font-size="7.5" fill="#dc2626" text-anchor="middle">Garis Weber</text>
            <rect x="205" y="8" width="90" height="78" rx="8" fill="#fdf4ff" stroke="#a855f7" stroke-width="2"/>
            <text x="250" y="28" font-size="10" font-weight="bold" fill="#7e22ce" text-anchor="middle">AUSTRALIS</text>
            <text x="250" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Cendrawasih</text>
            <text x="250" y="68" font-size="9" fill="#9333ea" text-anchor="middle">Papua & Maluku</text>
        </svg>';
        $story1 = 'Indonesia dikenal sebagai salah satu negara <em>megabiodiversitas</em> terbesar di dunia. Keanekaragaman hayati mencakup tiga tingkatan: <u>keanekaragaman gen</u> (variasi genetik dalam satu spesies, contohnya berbagai varietas buah mangga), <u>keanekaragaman jenis/spesies</u> (perbedaan antar spesies dalam satu famili, contohnya harimau, singa, dan kucing), serta <u>keanekaragaman ekosistem</u>. Secara biogeografi, fauna Indonesia terbagi menjadi tiga zona oleh Garis Wallace dan Garis Weber.';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Keanekaragaman Hayati dan Biogeografi Indonesia', $prompt1, $svg1, $story1, '1. PILIHAN GANDA', 'Berdasarkan teks dan diagram pembagian biogeografi di atas, manakah kelompok fauna berikut yang termasuk satwa khas <u>zona peralihan</u> di Indonesia?'),
            'opsi_a' => 'Gajah Sumatera, Badak Bercula Satu, dan Orangutan',
            'opsi_b' => 'Anoa, Babi Rusa, dan Komodo',
            'opsi_c' => 'Burung Cendrawasih, Kasuari, dan Kanguru Pohon',
            'opsi_d' => 'Bekantan, Harimau Jawa, dan Tapir',
            'opsi_e' => 'Walabi, Burung Kakaktua Raja, dan Kus-kus',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Gajah Sumatera, Badak Bercula Satu, dan Orangutan',
                'B' => 'Anoa, Babi Rusa, dan Komodo',
                'C' => 'Burung Cendrawasih, Kasuari, dan Kanguru Pohon',
                'D' => 'Bekantan, Harimau Jawa, dan Tapir',
                'E' => 'Walabi, Burung Kakaktua Raja, dan Kus-kus'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Keanekaragaman Hayati dan Biogeografi Indonesia', '', '', $story1, '2. PG KOMPLEKS (CENTANG)', 'Pilihlah seluruh pernyataan yang <strong>BENAR</strong> mengenai tingkatan keanekaragaman hayati berikut ini! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Perbedaan rasa, aroma, dan bentuk buah pada mangga arumanis, manalagi, dan golek merupakan contoh keanekaragaman tingkat gen.',
            'opsi_b' => 'Pohon kelapa, aren, pinang, dan kelapa sawit yang berada dalam famili Arecaceae merupakan contoh keanekaragaman tingkat spesies.',
            'opsi_c' => 'Garis Wallace memisahkan tipe fauna Australis dengan tipe fauna Peralihan.',
            'opsi_d' => 'Ekosistem padang rumput, hutan hujan tropis, dan taiga mencerminkan keanekaragaman hayati tingkat ekosistem.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Perbedaan rasa, aroma, dan bentuk buah pada mangga arumanis, manalagi, dan golek merupakan contoh keanekaragaman tingkat gen.', 'is_kunci' => true],
                    ['teks' => 'Pohon kelapa, aren, pinang, dan kelapa sawit yang berada dalam famili Arecaceae merupakan contoh keanekaragaman tingkat spesies.', 'is_kunci' => true],
                    ['teks' => 'Garis Wallace memisahkan tipe fauna Australis dengan tipe fauna Peralihan.', 'is_kunci' => false],
                    ['teks' => 'Ekosistem padang rumput, hutan hujan tropis, dan taiga mencerminkan keanekaragaman hayati tingkat ekosistem.', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Keanekaragaman Hayati dan Biogeografi Indonesia', '', '', $story1, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari masing-masing pernyataan perihal karakteristik biogeografi Indonesia:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Fauna zona Oriental didominasi oleh mamalia berukuran besar dan berbagai primata.', 'kunci' => 'Benar'],
                    ['teks' => 'Fauna tipe Australis memiliki banyak spesies kera dan tidak memiliki hewan berkantong.', 'kunci' => 'Salah'],
                    ['teks' => 'Garis Weber berada di sebelah timur Sulawesi, membatasi zona Peralihan dengan wilayah Australis.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Keanekaragaman Hayati dan Biogeografi Indonesia', '', '', $story1, '4. MENJODOHKAN', 'Pasangkan nama fauna endemik di sebelah kiri dengan zona persebarannya yang tepat di sebelah kanan:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Orangutan & Harimau', 'respon' => 'Zona Asiatis (Oriental)'],
                ['premis' => 'Anoa & Burung Maleo', 'respon' => 'Zona Peralihan'],
                ['premis' => 'Burung Cendrawasih', 'respon' => 'Zona Australis'],
                ['premis' => 'Komodo', 'respon' => 'Kepulauan Nusa Tenggara']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Orangutan & Harimau', 'respon' => 'Zona Asiatis (Oriental)'],
                    ['premis' => 'Anoa & Burung Maleo', 'respon' => 'Zona Peralihan'],
                    ['premis' => 'Burung Cendrawasih', 'respon' => 'Zona Australis'],
                    ['premis' => 'Komodo', 'respon' => 'Kepulauan Nusa Tenggara']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Keanekaragaman Hayati dan Biogeografi Indonesia', '', '', $story1, '5. ISIAN SINGKAT', 'Spesies hewan atau tumbuhan yang keberadaannya secara alami hanya terbatas pada satu wilayah geografis tertentu dan tidak dijumpai di wilayah lain disebut organisme...'),
            'kunci_jawaban' => 'Endemik',
            'konten_tambahan' => ['rubrik' => 'Endemik (atau Spesies Endemik)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): Struktur & Replikasi Virus (Bakteriofag)
        // ══════════════════════════════════════════════════════════════════
        $prompt2 = "Ilustrasi medis 3D struktur mikroskopis virus Bakteriofag T4. Menampilkan kepala ikosahedral berisi materi genetik DNA asam nukleat, leher, selubung ekor silindris heliks, lempeng dasar, dan serabut ekor menempel pada membran sel bakteri E. coli. Label huruf A, B, C, D jelas, pencahayaan laboratorium bioteknologi fluoresen.";
        $svg2 = '<svg width="260" height="100" viewBox="0 0 260 100" xmlns="http://www.w3.org/2000/svg">
            <polygon points="130,5 155,20 155,42 130,55 105,42 105,20" fill="#e0e7ff" stroke="#4f46e5" stroke-width="2"/>
            <text x="130" y="32" font-size="9" font-weight="bold" fill="#3730a3" text-anchor="middle">DNA / Kapsid [A]</text>
            <rect x="127" y="55" width="6" height="8" fill="#64748b"/>
            <rect x="124" y="63" width="12" height="22" fill="#c7d2fe" stroke="#4338ca" stroke-width="1.5"/>
            <text x="160" y="76" font-size="8" fill="#4338ca">Selubung Ekor [B]</text>
            <line x1="124" y1="85" x2="95" y2="98" stroke="#1e1b4b" stroke-width="2"/>
            <line x1="136" y1="85" x2="165" y2="98" stroke="#1e1b4b" stroke-width="2"/>
            <line x1="130" y1="85" x2="130" y2="98" stroke="#1e1b4b" stroke-width="2"/>
            <text x="195" y="94" font-size="8" font-weight="bold" fill="#ef4444">Serabut Ekor [C]</text>
        </svg>';
        $story2 = 'Virus merupakan entitas biologis aseluler (bukan berupa sel) yang hanya dapat bereproduksi di dalam sel inang hidup (parasit obligat intraseluler). Partikel virus lengkap (virion) tersusun atas <u>asam nukleat</u> (DNA atau RNA, tidak pernah keduanya sekaligus) yang diselubungi mantel protein pelindung bernama <u>kapsid</u>. Pada virus bakteriofag, terdapat <u>serabut ekor</u> yang berfungsi mengenali reseptor spesifik pada dinding sel inang.';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Struktur dan Siklus Replikasi Virus', $prompt2, $svg2, $story2, '1. PILIHAN GANDA', 'Bagian bakteriofag yang bertugas <u>menginjeksikan materi genetik</u> ke dalam sitoplasma sel inang setelah proses adsorpsi adalah...'),
            'opsi_a' => 'Kepala Ikosahedral',
            'opsi_b' => 'Kapsomer Kapsid',
            'opsi_c' => 'Selubung Ekor (Sheath)',
            'opsi_d' => 'Glikoprotein Spike',
            'opsi_e' => 'Dinding Sel',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Kepala Ikosahedral',
                'B' => 'Kapsomer Kapsid',
                'C' => 'Selubung Ekor (Sheath)',
                'D' => 'Glikoprotein Spike',
                'E' => 'Dinding Sel'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Struktur dan Siklus Replikasi Virus', '', '', $story2, '2. PG KOMPLEKS (CENTANG)', 'Manakah di antara pernyataan berikut yang merupakan perbedaan antara <strong>Siklus Litik</strong> dan <strong>Siklus Lisogenik</strong> pada virus? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Pada siklus litik, sel inang mengalami kematian/lisis pada tahap akhir perbanyakan virus.',
            'opsi_b' => 'Pada siklus lisogenik, DNA virus berintegrasi dengan DNA sel inang membentuk profag tanpa merusak sel inang seketika.',
            'opsi_c' => 'Siklus lisogenik membutuhkan waktu jauh lebih singkat dibandingkan dengan siklus litik.',
            'opsi_d' => 'Profag pada siklus lisogenik dapat bertransisi menjadi siklus litik apabila dipicu oleh radiasi atau zat kimia tertentu.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Pada siklus litik, sel inang mengalami kematian/lisis pada tahap akhir perbanyakan virus.', 'is_kunci' => true],
                    ['teks' => 'Pada siklus lisogenik, DNA virus berintegrasi dengan DNA sel inang membentuk profag tanpa merusak sel inang seketika.', 'is_kunci' => true],
                    ['teks' => 'Siklus lisogenik membutuhkan waktu jauh lebih singkat dibandingkan dengan siklus litik.', 'is_kunci' => false],
                    ['teks' => 'Profag pada siklus lisogenik dapat bertransisi menjadi siklus litik apabila dipicu oleh radiasi atau zat kimia tertentu.', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Struktur dan Siklus Replikasi Virus', '', '', $story2, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari sifat-sifat biologis virus berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Virus dapat dikristalkan ketika berada di luar sel organisme inang tanpa kehilangan sifat infeksinya.', 'kunci' => 'Benar'],
                    ['teks' => 'Antibiotik sangat efektif digunakan untuk membasmi segala jenis infeksi virus influenza.', 'kunci' => 'Salah'],
                    ['teks' => 'Virus tidak memiliki organel sel seperti ribosom dan mitokondria, sehingga bergantung penuh pada metabolisme sel inang.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Struktur dan Siklus Replikasi Virus', '', '', $story2, '4. MENJODOHKAN', 'Pasangkan tahapan reproduksi virus pada siklus litik dengan penjelasan peristiwanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Adsorpsi', 'respon' => 'Penempelan virus pada reseptor spesifik inang'],
                ['premis' => 'Penetrasi', 'respon' => 'Penyuntikan asam nukleat ke dalam sitoplasma inang'],
                ['premis' => 'Sintesis / Replikasi', 'respon' => 'Penggandaan materi genetik dan pembentukan protein virus'],
                ['premis' => 'Lisis', 'respon' => 'Pecahnya membran sel inang dan pelepasan virion baru']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Adsorpsi', 'respon' => 'Penempelan virus pada reseptor spesifik inang'],
                    ['premis' => 'Penetrasi', 'respon' => 'Penyuntikan asam nukleat ke dalam sitoplasma inang'],
                    ['premis' => 'Sintesis / Replikasi', 'respon' => 'Penggandaan materi genetik dan pembentukan protein virus'],
                    ['premis' => 'Lisis', 'respon' => 'Pecahnya membran sel inang dan pelepasan virion baru']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Struktur dan Siklus Replikasi Virus', '', '', $story2, '5. ISIAN SINGKAT', 'Lapisan selubung pelindung luar pada virus yang tersusun atas subunit-subunit molekul protein dinamakan...'),
            'kunci_jawaban' => 'Kapsid',
            'konten_tambahan' => ['rubrik' => 'Kapsid (atau Mantel Protein Kapsid)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): Bakteriologi & Bioteknologi Konvensional
        // ══════════════════════════════════════════════════════════════════
        $prompt3 = "Ilustrasi cawan petri mikrobiologi di laboratorium bercahaya LED biru, menampilkan koloni bakteri berbentuk basil Lactobacillus bulgaricus dan bintil akar legum berisi bakteri fiksasi nitrogen Rhizobium. Struktur mikroskopis dinding sel bakteri dengan lapisan peptidoglikan terperinci, gaya infografis biofarmasi.";
        $svg3 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="45" r="35" fill="#f8fafc" stroke="#0ea5e9" stroke-width="2"/>
            <circle cx="50" cy="45" r="30" fill="#f0f9ff"/>
            <ellipse cx="45" cy="40" rx="6" ry="12" fill="#0284c7"/>
            <ellipse cx="58" cy="48" rx="6" ry="12" fill="#0284c7"/>
            <text x="50" y="87" font-size="8.5" font-weight="bold" fill="#0369a1" text-anchor="middle">Gram Positif (Tebal)</text>
            <path d="M 100 45 L 140 45" stroke="#94a3b8" stroke-width="2" marker-end="url(#arr)"/>
            <rect x="155" y="15" width="105" height="60" rx="8" fill="#fef2f2" stroke="#ef4444" stroke-width="2"/>
            <text x="207" y="35" font-size="9" font-weight="bold" fill="#b91c1c" text-anchor="middle">Gram Negatif</text>
            <text x="207" y="52" font-size="8" fill="#7f1d1d" text-anchor="middle">Peptidoglikan Tipis</text>
            <text x="207" y="65" font-size="7.5" fill="#dc2626" text-anchor="middle">+ Membran Luar Lipopolisakarida</text>
        </svg>';
        $story3 = 'Bakteri (Eubacteria) merupakan mikroorganisme prokariotik uniseluler. Dinding sel bakteri tersusun dari polimer <u>peptidoglikan</u>. Berdasarkan ketebalan lapisan peptidoglikan pada pewarnaan Gram, bakteri dibedakan menjadi <strong>Gram Positif</strong> (warna ungu kristal violet tertahan karena lapisan tebal) dan <strong>Gram Negatif</strong> (warna merah safranin karena lapisan tipis dengan membran luar lipid). Banyak spesies bakteri yang menguntungkan bagi manusia dalam bidang pangan dan pertanian.';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur Bakteri dan Peranannya dalam Kehidupan', $prompt3, $svg3, $story3, '1. PILIHAN GANDA', 'Spesies bakteri yang hidup bersimbiosis mutualisme pada bintil akar tanaman kacang-kacangan (Leguminosae) dan mampu <u>mengikat nitrogen bebas (N2)</u> dari udara adalah...'),
            'opsi_a' => 'Lactobacillus casei',
            'opsi_b' => 'Rhizobium leguminosarum',
            'opsi_c' => 'Acetobacter xylinum',
            'opsi_d' => 'Streptococcus lactis',
            'opsi_e' => 'Treponema pallidum',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Lactobacillus casei',
                'B' => 'Rhizobium leguminosarum',
                'C' => 'Acetobacter xylinum',
                'D' => 'Streptococcus lactis',
                'E' => 'Treponema pallidum'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur Bakteri dan Peranannya dalam Kehidupan', '', '', $story3, '2. PG KOMPLEKS (CENTANG)', 'Centanglah nama-nama bakteri di bawah ini yang berperan penting dalam proses <strong>fermentasi bioteknologi pangan</strong>! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Lactobacillus bulgaricus dalam pembuatan yogurt',
            'opsi_b' => 'Acetobacter xylinum dalam produksi nata de coco',
            'opsi_c' => 'Mycobacterium tuberculosis dalam produksi keju cheddar',
            'opsi_d' => 'Streptococcus thermophilus dalam pembuatan olahan susu fermentasi',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Lactobacillus bulgaricus dalam pembuatan yogurt', 'is_kunci' => true],
                    ['teks' => 'Acetobacter xylinum dalam produksi nata de coco', 'is_kunci' => true],
                    ['teks' => 'Mycobacterium tuberculosis dalam produksi keju cheddar', 'is_kunci' => false],
                    ['teks' => 'Streptococcus thermophilus dalam pembuatan olahan susu fermentasi', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 13: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur Bakteri dan Peranannya dalam Kehidupan', '', '', $story3, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari pernyataan mengenai sitologi bakteri berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Benar', 'Salah']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Bakteri tergolong prokariotik karena materi genetiknya (DNA sirkuler) tidak dibungkus oleh membran inti (nukleus).', 'kunci' => 'Benar'],
                    ['teks' => 'Endospora adalah struktur pertahanan diri bakteri ketika menghadapi kondisi lingkungan yang ekstrem atau tidak menguntungkan.', 'kunci' => 'Benar'],
                    ['teks' => 'Bakteri Gram negatif memiliki lapisan peptidoglikan yang jauh lebih tebal daripada bakteri Gram positif.', 'kunci' => 'Salah']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur Bakteri dan Peranannya dalam Kehidupan', '', '', $story3, '4. MENJODOHKAN', 'Pasangkan nama mikroorganisme bakteri dengan produk atau peranan biologisnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Acetobacter xylinum', 'respon' => 'Pembuatan Nata de Coco'],
                ['premis' => 'Lactobacillus bulgaricus', 'respon' => 'Pembuatan Minuman Yogurt'],
                ['premis' => 'Nitrosomonas & Nitrobacter', 'respon' => 'Bakteri Nitrifikasi Tanah'],
                ['premis' => 'Clostridium tetani', 'respon' => 'Bakteri Patogen Penyebab Tetanus']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Acetobacter xylinum', 'respon' => 'Pembuatan Nata de Coco'],
                    ['premis' => 'Lactobacillus bulgaricus', 'respon' => 'Pembuatan Minuman Yogurt'],
                    ['premis' => 'Nitrosomonas & Nitrobacter', 'respon' => 'Bakteri Nitrifikasi Tanah'],
                    ['premis' => 'Clostridium tetani', 'respon' => 'Pembuatan Minuman Yogurt']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur Bakteri dan Peranannya dalam Kehidupan', '', '', $story3, '5. ISIAN SINGKAT', 'Lapisan gelatin pelindung di sebelah luar dinding sel bakteri yang berfungsi melindungi sel dari kekeringan dan serangan sistem imun inang adalah...'),
            'kunci_jawaban' => 'Kapsul',
            'konten_tambahan' => ['rubrik' => 'Kapsul (atau Glikokaliks)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): Rantai Makanan & Aliran Energi Ekosistem
        // ══════════════════════════════════════════════════════════════════
        $prompt4 = "Diagram infografis piramida energi ekosistem hutan padang rumput tropis bertingkat empat: Tingkat Trofik I (Produsen: Rumput hijau - 10.000 kkal), Trofik II (Konsumen Primer: Belalang - 1.000 kkal), Trofik III (Konsumen Sekunder: Katak - 100 kkal), Trofik IV (Konsumen Tersier: Elang - 10 kkal). Panah energi panas keluar 90% pada setiap tingkat, gaya buku teks biologi modern berlatar putih.";
        $svg4 = '<svg width="280" height="95" viewBox="0 0 280 95" xmlns="http://www.w3.org/2000/svg">
            <polygon points="140,5 110,25 170,25" fill="#fef08a" stroke="#ca8a04" stroke-width="1.5"/>
            <text x="140" y="20" font-size="8" font-weight="bold" fill="#854d0e" text-anchor="middle">Elang (10 kkal)</text>
            <polygon points="110,27 80,48 200,48 170,27" fill="#fed7aa" stroke="#ea580c" stroke-width="1.5"/>
            <text x="140" y="42" font-size="8.5" font-weight="bold" fill="#9a3412" text-anchor="middle">Katak / Konsumen II (100 kkal)</text>
            <polygon points="80,50 50,71 230,71 200,50" fill="#bbf7d0" stroke="#16a34a" stroke-width="1.5"/>
            <text x="140" y="64" font-size="9" font-weight="bold" fill="#166534" text-anchor="middle">Belalang / Konsumen I (1.000 kkal)</text>
            <polygon points="50,73 20,93 260,93 230,73" fill="#86efac" stroke="#15803d" stroke-width="1.5"/>
            <text x="140" y="86" font-size="9.5" font-weight="bold" fill="#14532d" text-anchor="middle">Produsen / Rumput Hijau (10.000 kkal)</text>
        </svg>';
        $story4 = 'Di dalam suatu ekosistem terjadi perpindahan energi dan materi melalui peristiwa makan dan dimakan yang membentuk <u>rantai makanan</u> dan jaring-jaring makanan. Berdasarkan <u>Hukum Aliran Energi 10%</u> (Lindeman), hanya sekitar <strong>10%</strong> dari total energi pada suatu tingkat trofik yang berhasil ditransfer ke tingkat trofik berikutnya. Sisanya (sekitar 90%) hilang ke lingkungan sebagai energi panas melalui proses respirasi seluler, aktivitas gerak, dan sisa metabolisme (ekskresi).';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Aliran Energi dan Piramida Ekologi', $prompt4, $svg4, $story4, '1. PILIHAN GANDA', 'Jika energi kimia yang dihasilkan oleh rumput (produsen) adalah <strong>20.000 kkal</strong>, berapakah perkiraan energi yang tersedia bagi konsumen tersier (puncak) sesuai hukum transfer energi 10%?'),
            'opsi_a' => '2.000 kkal',
            'opsi_b' => '200 kkal',
            'opsi_c' => '20 kkal',
            'opsi_d' => '2 kkal',
            'opsi_e' => '0,2 kkal',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => '2.000 kkal',
                'B' => '200 kkal',
                'C' => '20 kkal',
                'D' => '2 kkal',
                'E' => '0,2 kkal'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Aliran Energi dan Piramida Ekologi', '', '', $story4, '2. PG KOMPLEKS (CENTANG)', 'Faktor apa sajakah yang menyebabkan <strong>berkurangnya energi</strong> pada setiap perpindahan trofik dalam piramida energi? (Pilihlah semua yang benar)'),
            'opsi_a' => 'Sebagian besar energi terlepas ke biosfer dalam bentuk panas selama proses respirasi seluler.',
            'opsi_b' => 'Tidak semua bagian tubuh organisme mangsa dapat dicerna dan diserap oleh pemangsa (misal tulang, bulu, cangkang).',
            'opsi_c' => 'Sebagian energi terbuang bersama zat sisa metabolisme (feses dan urine).',
            'opsi_d' => 'Konsumen tersier mampu menyerap kembali energi panas matahari secara langsung.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Sebagian besar energi terlepas ke biosfer dalam bentuk panas selama proses respirasi seluler.', 'is_kunci' => true],
                    ['teks' => 'Tidak semua bagian tubuh organisme mangsa dapat dicerna dan diserap oleh pemangsa (misal tulang, bulu, cangkang).', 'is_kunci' => true],
                    ['teks' => 'Sebagian energi terbuang bersama zat sisa metabolisme (feses dan urine).', 'is_kunci' => true],
                    ['teks' => 'Konsumen tersier mampu menyerap kembali energi panas matahari secara langsung.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Aliran Energi dan Piramida Ekologi', '', '', $story4, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan perihal piramida ekologi berikut ini:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Piramida energi tidak pernah terbalik karena energi selalu berkurang pada setiap kenaikan tingkat trofik.', 'kunci' => 'Benar'],
                    ['teks' => 'Piramida jumlah selalu berbentuk tegak lurus pada ekosistem pohon parasit.', 'kunci' => 'Salah'],
                    ['teks' => 'Organisme detritivor dan dekomposer berperan mengembalikan nutrisi anorganik ke tanah untuk diserap produsen.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Aliran Energi dan Piramida Ekologi', '', '', $story4, '4. MENJODOHKAN', 'Pasangkan peranan fungsional organisme dalam ekosistem dengan contoh konkretnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Produsen Autotrof', 'respon' => 'Fitoplankton & Tanaman Padi'],
                ['premis' => 'Konsumen Primer (Herbivora)', 'respon' => 'Belalang & Kelinci'],
                ['premis' => 'Dekomposer (Pengurai)', 'respon' => 'Jamur Saprofit & Bakteri Pembusuk'],
                ['premis' => 'Detritivor', 'respon' => 'Cacing Tanah & Luwing']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Produsen Autotrof', 'respon' => 'Fitoplankton & Tanaman Padi'],
                    ['premis' => 'Konsumen Primer (Herbivora)', 'respon' => 'Belalang & Kelinci'],
                    ['premis' => 'Dekomposer (Pengurai)', 'respon' => 'Jamur Saprofit & Bakteri Pembusuk'],
                    ['premis' => 'Detritivor', 'respon' => 'Cacing Tanah & Luwing']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Aliran Energi dan Piramida Ekologi', '', '', $story4, '5. ISIAN SINGKAT', 'Menurut hukum Lindeman, persentase rata-rata energi kimia yang berhasil diteruskan dari satu tingkat trofik ke tingkat trofik di atasnya adalah sebesar... (Tuliskan angkanya saja)'),
            'kunci_jawaban' => '10%',
            'konten_tambahan' => ['rubrik' => '10% (atau 10 persen)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): Siklus Nitrogen & Siklus Karbon
        // ══════════════════════════════════════════════════════════════════
        $prompt5 = "Diagram ilmiah siklus biogeokimia nitrogen di alam. Menampilkan nitrogen bebas N2 di atmosfer, proses fiksasi nitrogen oleh Rhizobium, amonifikasi pengurai menjadi NH4+, proses nitrifikasi oleh Nitrosomonas menjadi NO2- dan Nitrobacter menjadi NO3-, asimilasi oleh tanaman hijau, serta denitrifikasi oleh Pseudomonas mengembalikan N2 ke udara. Vektor bersih beresolusi tinggi.";
        $svg5 = '<svg width="280" height="95" viewBox="0 0 280 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="90" y="5" width="100" height="22" rx="6" fill="#e0f2fe" stroke="#0284c7" stroke-width="1.5"/>
            <text x="140" y="19" font-size="9.5" font-weight="bold" fill="#0369a1" text-anchor="middle">N2 di Atmosfer (78%)</text>
            <path d="M 90 20 L 40 45" stroke="#0284c7" stroke-width="1.5" marker-end="url(#arr)"/>
            <text x="40" y="32" font-size="7.5" fill="#0284c7">Fiksasi</text>
            <rect x="10" y="45" width="70" height="35" rx="6" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="45" y="60" font-size="8.5" font-weight="bold" fill="#b45309" text-anchor="middle">Amonia (NH4+)</text>
            <text x="45" y="73" font-size="7" fill="#78350f" text-anchor="middle">Amonifikasi</text>
            <path d="M 85 62 L 115 62" stroke="#d97706" stroke-width="1.5"/>
            <rect x="120" y="45" width="65" height="35" rx="6" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="152" y="60" font-size="8.5" font-weight="bold" fill="#047857" text-anchor="middle">Nitrit (NO2-)</text>
            <text x="152" y="73" font-size="7" fill="#065f46" text-anchor="middle">Nitrosomonas</text>
            <path d="M 190 62 L 210 62" stroke="#059669" stroke-width="1.5"/>
            <rect x="215" y="45" width="60" height="35" rx="6" fill="#fdf4ff" stroke="#7e22ce" stroke-width="1.5"/>
            <text x="245" y="60" font-size="8.5" font-weight="bold" fill="#6b21a8" text-anchor="middle">Nitrat (NO3-)</text>
            <text x="245" y="73" font-size="7" fill="#581c87" text-anchor="middle">Nitrobacter</text>
        </svg>';
        $story5 = 'Gas nitrogen (N2) menyusun sekitar 78% atmosfer bumi, namun sebagian besar tumbuhan tidak dapat langsung memanfaatkannya dalam bentuk gas bebas. Melalui <u>daur nitrogen</u>, gas N2 mengalami <strong>fiksasi</strong> menjadi amonium/amonia (NH4+/NH3). Selanjutnya berlangsung <strong>nitrifikasi</strong> melalui dua tahapan: nitritasi oleh bakteri <em>Nitrosomonas</em> menghasilkan nitrit (NO2-), lalu nitratasi oleh bakteri <em>Nitrobacter</em> menghasilkan nitrat (NO3-) yang siap diserap akar tanaman.';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Daur Biogeokimia: Siklus Nitrogen di Biosfer', $prompt5, $svg5, $story5, '1. PILIHAN GANDA', 'Bakteri tanah yang berperan dalam mengoksidasi senyawa <u>nitrit (NO2-) menjadi nitrat (NO3-)</u> dalam proses nitratasi adalah...'),
            'opsi_a' => 'Nitrosomonas',
            'opsi_b' => 'Nitrococcus',
            'opsi_c' => 'Nitrobacter',
            'opsi_d' => 'Pseudomonas denitrificans',
            'opsi_e' => 'Azotobacter',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Nitrosomonas',
                'B' => 'Nitrococcus',
                'C' => 'Nitrobacter',
                'D' => 'Pseudomonas denitrificans',
                'E' => 'Azotobacter'
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Daur Biogeokimia: Siklus Nitrogen di Biosfer', '', '', $story5, '2. PG KOMPLEKS (CENTANG)', 'Aktivitas alami maupun antropogenik manakah yang mengembalikan gas <strong>karbon dioksida (CO2)</strong> ke atmosfer bumi? (Pilihlah semua jawaban benar)'),
            'opsi_a' => 'Respirasi seluler seluruh makhluk hidup aerob.',
            'opsi_b' => 'Pembakaran bahan bakar fosil pada kendaraan bermotor dan pabrik industri.',
            'opsi_c' => 'Dekomposisi jasad organik yang mati oleh mikroorganisme pengurai.',
            'opsi_d' => 'Reaksi terang pada proses fotosintesis kloroplas tumbuhan hijau.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Respirasi seluler seluruh makhluk hidup aerob.', 'is_kunci' => true],
                    ['teks' => 'Pembakaran bahan bakar fosil pada kendaraan bermotor dan pabrik industri.', 'is_kunci' => true],
                    ['teks' => 'Dekomposisi jasad organik yang mati oleh mikroorganisme pengurai.', 'is_kunci' => true],
                    ['teks' => 'Reaksi terang pada proses fotosintesis kloroplas tumbuhan hijau.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Daur Biogeokimia: Siklus Nitrogen di Biosfer', '', '', $story5, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari tahapan siklus biogeokimia berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Bakteri Pseudomonas denitrificans mengubah kembali ion nitrat menjadi gas nitrogen (N2) yang dilepas ke atmosfer.', 'kunci' => 'Benar'],
                    ['teks' => 'Bentuk nitrogen yang paling mudah dan utama diserap oleh sistem perakaran tumbuhan adalah gas N2 secara difusi langsung.', 'kunci' => 'Salah'],
                    ['teks' => 'Petir/kilat di atmosfer dapat memecah ikatan gas N2 sehingga bersenyawa dengan oksigen membentuk nitrat melalui air hujan.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Daur Biogeokimia: Siklus Nitrogen di Biosfer', '', '', $story5, '4. MENJODOHKAN', 'Pasangkan istilah proses dalam daur nitrogen dengan pengertian yang tepat:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Fiksasi Nitrogen', 'respon' => 'Pengikatan gas N2 bebas di udara menjadi senyawa amonia'],
                ['premis' => 'Nitrifikasi', 'respon' => 'Oksidasi amonia menjadi ion nitrit dan nitrat'],
                ['premis' => 'Asimilasi', 'respon' => 'Pemanfaatan nitrat oleh tumbuhan untuk sintesis asam amino'],
                ['premis' => 'Denitrifikasi', 'respon' => 'Pelepasan kembali nitrogen bebas ke atmosfer']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Fiksasi Nitrogen', 'respon' => 'Pengikatan gas N2 bebas di udara menjadi senyawa amonia'],
                    ['premis' => 'Nitrifikasi', 'respon' => 'Oksidasi amonia menjadi ion nitrit dan nitrat'],
                    ['premis' => 'Asimilasi', 'respon' => 'Pemanfaatan nitrat oleh tumbuhan untuk sintesis asam amino'],
                    ['premis' => 'Denitrifikasi', 'respon' => 'Pelepasan kembali nitrogen bebas ke atmosfer']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Daur Biogeokimia: Siklus Nitrogen di Biosfer', '', '', $story5, '5. ISIAN SINGKAT', 'Gas rumah kaca yang terlibat langsung dalam siklus karbon dan menjadi pemicu utama kenaikan suhu rata-rata global adalah...'),
            'kunci_jawaban' => 'Karbon Dioksida',
            'konten_tambahan' => ['rubrik' => 'Karbon Dioksida (atau CO2)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): Kingdom Fungi & Simbiosis Liken
        // ══════════════════════════════════════════════════════════════════
        $prompt6 = "Ilustrasi anatomi jamur Basidiomycota (jamur merang) di hutan lembap. Struktur tudung (pileus), bilah berbasidium penghasil basidiospora (lamela), cincin (annulus), tangkai (stipe), dan miselium hifa berseptum di dalam tanah. Sisi kanan menampilkan lumut kerak (liken) pada kulit pohon sebagai indikator udara bersih.";
        $svg6 = '<svg width="250" height="90" viewBox="0 0 250 90" xmlns="http://www.w3.org/2000/svg">
            <path d="M 80,40 Q 125,5 170,40 Z" fill="#fed7aa" stroke="#c2410c" stroke-width="2"/>
            <text x="125" y="32" font-size="8.5" font-weight="bold" fill="#9a3412" text-anchor="middle">Tudung (Pileus)</text>
            <rect x="118" y="40" width="14" height="42" fill="#ffedd5" stroke="#c2410c" stroke-width="1.5"/>
            <text x="125" y="65" font-size="7.5" fill="#7c2d12" text-anchor="middle">Stipe</text>
            <line x1="85" y1="42" x2="165" y2="42" stroke="#ea580c" stroke-width="2" stroke-dasharray="2"/>
            <text x="125" y="87" font-size="8" font-weight="bold" fill="#047857" text-anchor="middle">Hifa &amp; Miselium</text>
        </svg>';
        $story6 = 'Jamur (Fungi) merupakan organisme eukariotik heterotrof yang dinding selnya tersusun atas <u>zat kitin</u>. Jamur tidak berklorofil, memperoleh nutrisi secara saprofit atau parasit dengan melepaskan enzim hidrolitik ekstraseluler. Tubuh jamur tersusun atas benang-benang halus bernama <strong>hifa</strong> yang berkumpul membentuk <strong>miselium</strong>. Simbiosis mutualisme antara jamur dan alga fotosintetik membentuk <u>Lichenes (lumut kerak)</u> yang sangat sensitif terhadap polutan sulfur dioksida.';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Karakteristik Kingdom Fungi dan Simbiosis Liken', $prompt6, $svg6, $story6, '1. PILIHAN GANDA', 'Polimer penyusun utama <u>dinding sel jamur</u> yang juga ditemukan pada eksoskeleton hewan Arthropoda adalah...'),
            'opsi_a' => 'Selulosa',
            'opsi_b' => 'Peptidoglikan',
            'opsi_c' => 'Kitin',
            'opsi_d' => 'Lignin',
            'opsi_e' => 'Pektin',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Selulosa',
                'B' => 'Peptidoglikan',
                'C' => 'Kitin',
                'D' => 'Lignin',
                'E' => 'Pektin'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Karakteristik Kingdom Fungi dan Simbiosis Liken', '', '', $story6, '2. PG KOMPLEKS (CENTANG)', 'Manakah peranan yang <strong>BENAR</strong> dari simbiosis mutualisme jamur pada <strong>Mikoriza</strong> (akar tanaman) dan <strong>Lichenes</strong>? (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Mikoriza membantu memperluas bidang penyerapan air dan mineral fosfor bagi perakaran tanaman.',
            'opsi_b' => 'Tanaman inang menyediakan senyawa organik hasil fotosintesis bagi kelangsungan hidup jamur mikoriza.',
            'opsi_c' => 'Lichenes mampu hidup di batuan tandus dan bertindak sebagai vegetasi perintis (suksesi primer).',
            'opsi_d' => 'Jamur pada lichenes mampu berfotosintesis mandiri tanpa bantuan komponen alga mikro.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Mikoriza membantu memperluas bidang penyerapan air dan mineral fosfor bagi perakaran tanaman.', 'is_kunci' => true],
                    ['teks' => 'Tanaman inang menyediakan senyawa organik hasil fotosintesis bagi kelangsungan hidup jamur mikoriza.', 'is_kunci' => true],
                    ['teks' => 'Lichenes mampu hidup di batuan tandus dan bertindak sebagai vegetasi perintis (suksesi primer).', 'is_kunci' => true],
                    ['teks' => 'Jamur pada lichenes mampu berfotosintesis mandiri tanpa bantuan komponen alga mikro.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Karakteristik Kingdom Fungi dan Simbiosis Liken', '', '', $story6, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari sifat biologis Fungi berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Jamur menyimpan kelebihan cadangan makanan dalam bentuk glikogen, bukan berupa amilum.', 'kunci' => 'Benar'],
                    ['teks' => 'Ragi roti (Saccharomyces cerevisiae) berkembang biak secara seksual dengan basidiospora.', 'kunci' => 'Salah'],
                    ['teks' => 'Keberadaan lumut kerak (Lichenes) yang melimpah menandakan udara di kawasan tersebut bebas dari polusi gas SO2.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Karakteristik Kingdom Fungi dan Simbiosis Liken', '', '', $story6, '4. MENJODOHKAN', 'Pasangkan divisi Fungi dengan jenis spora generatif (seksual) yang dihasilkannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Zygomycota (Tempe)', 'respon' => 'Zigospora berdinding tebal'],
                ['premis' => 'Ascomycota (Ragi)', 'respon' => 'Askospora di dalam askus'],
                ['premis' => 'Basidiomycota (Jamur Merang)', 'respon' => 'Basidiospora di atas basidium'],
                ['premis' => 'Deuteromycota', 'respon' => 'Belum diketahui reproduksi seksualnya']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Zygomycota (Tempe)', 'respon' => 'Zigospora berdinding tebal'],
                    ['premis' => 'Ascomycota (Ragi)', 'respon' => 'Askospora di dalam askus'],
                    ['premis' => 'Basidiomycota (Jamur Merang)', 'respon' => 'Basidiospora di atas basidium'],
                    ['premis' => 'Deuteromycota', 'respon' => 'Belum diketahui reproduksi seksualnya']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Karakteristik Kingdom Fungi dan Simbiosis Liken', '', '', $story6, '5. ISIAN SINGKAT', 'Kumpulan atau anyaman dari benang-benang halus (hifa) yang menyusun tubuh vegetatif jamur dinamakan...'),
            'kunci_jawaban' => 'Miselium',
            'konten_tambahan' => ['rubrik' => 'Miselium']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): Eutrofikasi & Pencemaran Lingkungan Air
        // ══════════════════════════════════════════════════════════════════
        $prompt7 = "Ilustrasi danau air tawar mengalami eutrofikasi parah. Permukaan air danau tertutup alga hijau pekat tebal (algal bloom), ikan-ikan mati terapung kehabisan oksigen (hipoksia), dan limpasan pupuk fosfat nitrat dari areal pertanian masuk ke badan air. Diagram ilmiah lingkungan detail dan dramatis.";
        $svg7 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="8" width="80" height="74" rx="8" fill="#ecfdf5" stroke="#10b981" stroke-width="1.5"/>
            <text x="50" y="28" font-size="9" font-weight="bold" fill="#047857" text-anchor="middle">Limpasan Pupuk</text>
            <text x="50" y="46" font-size="8" fill="#065f46" text-anchor="middle">Fosfat &amp; Nitrat</text>
            <text x="50" y="65" font-size="7.5" fill="#059669" text-anchor="middle">Erosi Pertanian</text>
            <path d="M 92 45 L 115 45" stroke="#047857" stroke-width="2"/>
            <rect x="118" y="8" width="75" height="74" rx="8" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="155" y="28" font-size="9" font-weight="bold" fill="#b45309" text-anchor="middle">Algal Bloom</text>
            <text x="155" y="46" font-size="8" fill="#78350f" text-anchor="middle">Ledakan Alga</text>
            <text x="155" y="65" font-size="7.5" fill="#d97706" text-anchor="middle">Cahaya Tertutup</text>
            <path d="M 195 45 L 215 45" stroke="#b45309" stroke-width="2"/>
            <rect x="218" y="8" width="48" height="74" rx="8" fill="#fef2f2" stroke="#ef4444" stroke-width="1.5"/>
            <text x="242" y="28" font-size="9" font-weight="bold" fill="#b91c1c" text-anchor="middle">Anoksia</text>
            <text x="242" y="46" font-size="7.5" fill="#7f1d1d" text-anchor="middle">DO Turun</text>
            <text x="242" y="65" font-size="7.5" fill="#dc2626" text-anchor="middle">Ikan Mati</text>
        </svg>';
        $story7 = 'Penggunaan pupuk kimia pertanian (NPK) yang berlebihan menyebabkan sisa-sisa nutrien fosfat dan nitrat hanyut ke perairan danau atau sungai melalui aliran air limpasan (run-off). Kondisi perairan yang terlalu kaya nutrien ini memicu fenomena <u>eutrofikasi</u>, ditandai ledakan populasi alga dan eceng gondok (<em>algal bloom</em>). Ketika alga mati dalam jumlah masif, bakteri dekomposer menguraikannya dengan menghabiskan cadangan oksigen terlarut (<strong>DO - Dissolved Oxygen</strong>), memicu kondisi anoksia yang menyebabkan kematian massal biota air.';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Eutrofikasi dan Pencemaran Ekosistem Perairan', $prompt7, $svg7, $story7, '1. PILIHAN GANDA', 'Pemicu utama terjadinya fenomena ledakan populasi alga (<em>blooming algae</em>) pada danau yang mengalami eutrofikasi adalah penumpukan senyawa...'),
            'opsi_a' => 'Logam berat timbal (Pb) dan merkuri (Hg)',
            'opsi_b' => 'Nutrien fosfat dan nitrat dari sisa pupuk atau detergen',
            'opsi_c' => 'Gas klorofluorokarbon (CFC) terlarut',
            'opsi_d' => 'Minyak bumi mentah dan tumpahan aspal',
            'opsi_e' => 'Karbon monoksida (CO) hasil industri',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Logam berat timbal (Pb) dan merkuri (Hg)',
                'B' => 'Nutrien fosfat dan nitrat dari sisa pupuk atau detergen',
                'C' => 'Gas klorofluorokarbon (CFC) terlarut',
                'D' => 'Minyak bumi mentah dan tumpahan aspal',
                'E' => 'Karbon monoksida (CO) hasil industri'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Eutrofikasi dan Pencemaran Ekosistem Perairan', '', '', $story7, '2. PG KOMPLEKS (CENTANG)', 'Dampak ekologis yang timbul akibat fenomena <strong>algal bloom</strong> yang menutupi permukaan perairan adalah: (Pilihlah semua jawaban benar)'),
            'opsi_a' => 'Penetrasi sinar matahari ke lapisan perairan yang lebih dalam terhalang sehingga fotosintesis tumbuhan air terhenti.',
            'opsi_b' => 'Kadar Biological Oxygen Demand (BOD) melonjak tajam karena tingginya aktivitas bakteri pengurai.',
            'opsi_c' => 'Konsentrasi oksigen terlarut (Dissolved Oxygen / DO) di dalam air menurun drastis.',
            'opsi_d' => 'Populasi ikan predator dan karnivora air bertambah secara berlipat ganda.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Penetrasi sinar matahari ke lapisan perairan yang lebih dalam terhalang sehingga fotosintesis tumbuhan air terhenti.', 'is_kunci' => true],
                    ['teks' => 'Kadar Biological Oxygen Demand (BOD) melonjak tajam karena tingginya aktivitas bakteri pengurai.', 'is_kunci' => true],
                    ['teks' => 'Konsentrasi oksigen terlarut (Dissolved Oxygen / DO) di dalam air menurun drastis.', 'is_kunci' => true],
                    ['teks' => 'Populasi ikan predator dan karnivora air bertambah secara berlipat ganda.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Eutrofikasi dan Pencemaran Ekosistem Perairan', '', '', $story7, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari parameter pencemaran lingkungan perairan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Benar', 'Salah']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Semakin tinggi nilai BOD suatu badan air, semakin buruk kualitas perairan tersebut akibat tingginya beban polutan organik.', 'kunci' => 'Benar'],
                    ['teks' => 'Pestisida non-biodegradable (seperti DDT) mengalami biomagnifikasi konsentrasi tertinggi pada konsumen puncak rantai makanan.', 'kunci' => 'Benar'],
                    ['teks' => 'Peningkatan suhu air akibat limbah air pendingin pabrik (thermal pollution) justru meningkatkan kadar oksigen terlarut.', 'kunci' => 'Salah']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Eutrofikasi dan Pencemaran Ekosistem Perairan', '', '', $story7, '4. MENJODOHKAN', 'Pasangkan parameter uji kualitas air dengan definisi operasionalnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'DO (Dissolved Oxygen)', 'respon' => 'Kadar oksigen terlarut yang tersedia bagi respirasi organisme air'],
                ['premis' => 'BOD (Biological Oxygen Demand)', 'respon' => 'Jumlah oksigen yang dibutuhkan mikroba untuk mengurai bahan organik'],
                ['premis' => 'COD (Chemical Oxygen Demand)', 'respon' => 'Kebutuhan oksigen untuk mengoksidasi senyawa kimiawi secara total'],
                ['premis' => 'Eutrofikasi', 'respon' => 'Penyuburan perairan secara berlebihan oleh unsur hara fosfat dan nitrat']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'DO (Dissolved Oxygen)', 'respon' => 'Kadar oksigen terlarut yang tersedia bagi respirasi organisme air'],
                    ['premis' => 'BOD (Biological Oxygen Demand)', 'respon' => 'Jumlah oksigen yang dibutuhkan mikroba untuk mengurai bahan organik'],
                    ['premis' => 'COD (Chemical Oxygen Demand)', 'respon' => 'Kebutuhan oksigen untuk mengoksidasi senyawa kimiawi secara total'],
                    ['premis' => 'Eutrofikasi', 'respon' => 'Penyuburan perairan secara berlebihan oleh unsur hara fosfat dan nitrat']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Eutrofikasi dan Pencemaran Ekosistem Perairan', '', '', $story7, '5. ISIAN SINGKAT', 'Proses pelipatgandaan akumulasi zat kimia beracun yang sulit terurai (seperti pestisida DDT atau merkuri) pada setiap tingkatan trofik rantai makanan dinamakan...'),
            'kunci_jawaban' => 'Biomagnifikasi',
            'konten_tambahan' => ['rubrik' => 'Biomagnifikasi (atau Bioakumulasi Trofik)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): Plantae: Lumut vs Tumbuhan Paku
        // ══════════════════════════════════════════════════════════════════
        $prompt8 = "Infografis komparasi metagenesis Lumut (Bryophyta) dan Tumbuhan Paku (Pteridophyta). Menampilkan fase dominan gametofit haploid pada lumut dengan anteridium/arkegonium dan fase dominan sporofit diploid pada tumbuhan paku dengan sorus berisikan sporangium pada helaian daun paku. Desain ilmiah botani resolusi tinggi.";
        $svg8 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="10" width="110" height="70" rx="8" fill="#f0fdf4" stroke="#16a34a" stroke-width="1.5"/>
            <text x="70" y="28" font-size="9" font-weight="bold" fill="#15803d" text-anchor="middle">LUMUT (Bryophyta)</text>
            <text x="70" y="48" font-size="8" fill="#166534" text-anchor="middle">Dominan: Gametofit (n)</text>
            <text x="70" y="66" font-size="7.5" fill="#14532d" text-anchor="middle">Atracheophyta (Non-Vaskular)</text>
            <rect x="145" y="10" width="110" height="70" rx="8" fill="#eff6ff" stroke="#2563eb" stroke-width="1.5"/>
            <text x="200" y="28" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">PAKU (Pteridophyta)</text>
            <text x="200" y="48" font-size="8" fill="#1e40af" text-anchor="middle">Dominan: Sporofit (2n)</text>
            <text x="200" y="66" font-size="7.5" fill="#1e3a8a" text-anchor="middle">Tracheophyta (Xilem/Floem)</text>
        </svg>';
        $story8 = 'Kingdom Plantae dibagi menjadi tumbuhan tidak berpembuluh (Bryophyta/Lumut) dan tumbuhan berpembuluh (Tracheophyta yang meliputi Pteridophyta/Paku dan Spermatophyta/Biji). Lumut dan paku sama-sama mengalami pergiliran keturunan (<strong>metagenesis</strong>) antara generasi seksual menghasilkan gamet (gametofit, n) dan generasi aseksual menghasilkan spora (sporofit, 2n). Pada lumut, fase dominan adalah <u>gametofit</u>, sedangkan pada tumbuhan paku fase dominan yang hidup bebas adalah <u>sporofit</u>.';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Metagenesis dan Klasifikasi Kingdom Plantae', $prompt8, $svg8, $story8, '1. PILIHAN GANDA', 'Fase hidup tumbuhan paku yang berdaun hijau, memiliki pembuluh angkut (xilem dan floem), dan berfungsi menghasilkan spora adalah generasi...'),
            'opsi_a' => 'Gametofit diploid (2n)',
            'opsi_b' => 'Gametofit haploid (n)',
            'opsi_c' => 'Protonema haploid (n)',
            'opsi_d' => 'Sporofit diploid (2n)',
            'opsi_e' => 'Protalium haploid (n)',
            'kunci_jawaban' => 'D',
            'konten_tambahan' => ['kunci' => 'D', 'opsi' => [
                'A' => 'Gametofit diploid (2n)',
                'B' => 'Gametofit haploid (n)',
                'C' => 'Protonema haploid (n)',
                'D' => 'Sporofit diploid (2n)',
                'E' => 'Protalium haploid (n)'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Metagenesis dan Klasifikasi Kingdom Plantae', '', '', $story8, '2. PG KOMPLEKS (CENTANG)', 'Manakah ciri-ciri tumbuhan paku (Pteridophyta) yang <strong>membedakannya</strong> dari tumbuhan lumut (Bryophyta)? (Pilihlah semua jawaban benar)'),
            'opsi_a' => 'Sudah memiliki berkas pembuluh angkut sejati (xilem dan floem).',
            'opsi_b' => 'Memiliki akar, batang, dan daun sejati (tumbuhan kormus).',
            'opsi_c' => 'Daun mudanya memiliki ciri khas menggulung (circinate vernation).',
            'opsi_d' => 'Membutuhkan air sebagai medium fertilisasi sperma berflagela menuju ovum.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Sudah memiliki berkas pembuluh angkut sejati (xilem dan floem).', 'is_kunci' => true],
                    ['teks' => 'Memiliki akar, batang, dan daun sejati (tumbuhan kormus).', 'is_kunci' => true],
                    ['teks' => 'Daun mudanya memiliki ciri khas menggulung (circinate vernation).', 'is_kunci' => true],
                    ['teks' => 'Membutuhkan air sebagai medium fertilisasi sperma berflagela menuju ovum.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Metagenesis dan Klasifikasi Kingdom Plantae', '', '', $story8, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan perihal metagenesis tumbuhan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Anteridium merupakan gametangium jantan yang menghasilkan sel sperma berflagela.', 'kunci' => 'Benar'],
                    ['teks' => 'Spora tumbuhan lumut yang berkecambah akan langsung tumbuh menjadi protalium.', 'kunci' => 'Salah'],
                    ['teks' => 'Arkegonium merupakan organ reproduksi betina penghasil sel ovum pada lumut dan paku.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Metagenesis dan Klasifikasi Kingdom Plantae', '', '', $story8, '4. MENJODOHKAN', 'Pasangkan nama tanaman dengan kelompok taksonominya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Marchantia polymorpha (Lumut Hati)', 'respon' => 'Bryophyta (Non-Vaskular)'],
                ['premis' => 'Adiantum cuneatum (Suplir)', 'respon' => 'Pteridophyta (Tumbuhan Paku)'],
                ['premis' => 'Pinus merkusii (Tusam)', 'respon' => 'Gymnospermae (Biji Terbuka)'],
                ['premis' => 'Oryza sativa (Padi)', 'respon' => 'Angiospermae Monokotil']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Marchantia polymorpha (Lumut Hati)', 'respon' => 'Bryophyta (Non-Vaskular)'],
                    ['premis' => 'Adiantum cuneatum (Suplir)', 'respon' => 'Pteridophyta (Tumbuhan Paku)'],
                    ['premis' => 'Pinus merkusii (Tusam)', 'respon' => 'Gymnospermae (Biji Terbuka)'],
                    ['premis' => 'Oryza sativa (Padi)', 'respon' => 'Angiospermae Monokotil']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Metagenesis dan Klasifikasi Kingdom Plantae', '', '', $story8, '5. ISIAN SINGKAT', 'Daun pada tumbuhan paku yang khusus berfungsi menghasilkan spora (daun fertil) disebut daun...'),
            'kunci_jawaban' => 'Sporofil',
            'konten_tambahan' => ['rubrik' => 'Sporofil']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): Animalia: Invertebrata & Sistem Tubuh
        // ══════════════════════════════════════════════════════════════════
        $prompt9 = "Ilustrasi anatomi komparatif filum Animalia invertebrata. Menampilkan Porifera dengan koanosit berflagel, Coelenterata berknidoblas, cacing pipih Platyhelminthes aselomata, Arthropoda dengan eksoskeleton kitin dan kaki beruas, serta Echinodermata (bintang laut) dengan sistem vaskular air ambulakral. Diagram infografis zoologi resolusi 4K.";
        $svg9 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="45" cy="45" r="30" fill="#fdf4ff" stroke="#a855f7" stroke-width="1.5"/>
            <text x="45" y="42" font-size="8.5" font-weight="bold" fill="#7e22ce" text-anchor="middle">Aselomata</text>
            <text x="45" y="56" font-size="7" fill="#6b21a8" text-anchor="middle">Platyhelminthes</text>
            <circle cx="135" cy="45" r="30" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="135" y="42" font-size="8.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Pseudoselomata</text>
            <text x="135" y="56" font-size="7" fill="#1e40af" text-anchor="middle">Nematoda</text>
            <circle cx="225" cy="45" r="30" fill="#ecfdf5" stroke="#10b981" stroke-width="1.5"/>
            <text x="225" y="42" font-size="8.5" font-weight="bold" fill="#047857" text-anchor="middle">Selomata Sejati</text>
            <text x="225" y="56" font-size="7" fill="#065f46" text-anchor="middle">Annelida &amp; Chordata</text>
        </svg>';
        $story9 = 'Kingdom Animalia beranggotakan organisme eukariotik multiseluler tanpa dinding sel. Klasifikasi hewan didasarkan pada simetri tubuh (radial vs bilateral), lapisan jaringan embrional (diploblastik vs triploblastik), serta rongga tubuh (<em>selom</em>). Hewan triploblastik dibagi menjadi <strong>aselomata</strong> (tidak berongga tubuh semu maupun sejati, misal cacing pipih), <strong>pseudoselomata</strong> (rongga semu, misal cacing gilig Nematoda), dan <strong>selomata sejati</strong> (misal Annelida, Mollusca, Arthropoda, dan Chordata).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Karakteristik Lapisan Embrional dan Klasifikasi Animalia', $prompt9, $svg9, $story9, '1. PILIHAN GANDA', 'Filum hewan laut yang memiliki rangka dalam berduri dari zat kapur dan bergerak menggunakan <u>sistem ambulakral (kaki tabung)</u> adalah...'),
            'opsi_a' => 'Porifera',
            'opsi_b' => 'Coelenterata / Cnidaria',
            'opsi_c' => 'Mollusca',
            'opsi_d' => 'Echinodermata',
            'opsi_e' => 'Annelida',
            'kunci_jawaban' => 'D',
            'konten_tambahan' => ['kunci' => 'D', 'opsi' => [
                'A' => 'Porifera',
                'B' => 'Coelenterata / Cnidaria',
                'C' => 'Mollusca',
                'D' => 'Echinodermata',
                'E' => 'Annelida'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Karakteristik Lapisan Embrional dan Klasifikasi Animalia', '', '', $story9, '2. PG KOMPLEKS (CENTANG)', 'Karakteristik anatomis manakah yang dimiliki oleh kelompok hewan kelas <strong>Insecta (Serangga)</strong> dalam filum Arthropoda? (Pilihlah semua jawaban benar)'),
            'opsi_a' => 'Tubuh terbagi menjadi 3 bagian tegas: kepala (kaput), dada (toraks), dan perut (abdomen).',
            'opsi_b' => 'Memiliki 3 pasang kaki jalan (heksapoda) yang semuanya melekat pada bagian toraks.',
            'opsi_c' => 'Bernapas menggunakan sistem pembuluh trakea dengan stigma / spirakel.',
            'opsi_d' => 'Memiliki 4 pasang kaki jalan dan sepasang kelisera tanpa antena.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Tubuh terbagi menjadi 3 bagian tegas: kepala (kaput), dada (toraks), dan perut (abdomen).', 'is_kunci' => true],
                    ['teks' => 'Memiliki 3 pasang kaki jalan (heksapoda) yang semuanya melekat pada bagian toraks.', 'is_kunci' => true],
                    ['teks' => 'Bernapas menggunakan sistem pembuluh trakea dengan stigma / spirakel.', 'is_kunci' => true],
                    ['teks' => 'Memiliki 4 pasang kaki jalan dan sepasang kelisera tanpa antena.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Karakteristik Lapisan Embrional dan Klasifikasi Animalia', '', '', $story9, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari sifat fisiologis hewan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Cacing gelang (Annelida) memiliki sistem peredaran darah tertutup dengan pembuluh darah dan lengkung aorta.', 'kunci' => 'Benar'],
                    ['teks' => 'Planaria (cacing pipih) tergolong hewan selomata sejati dengan sistem peredaran darah terbuka.', 'kunci' => 'Salah'],
                    ['teks' => 'Sel koanosit berflagela pada Porifera berfungsi menciptakan aliran air dan menangkap partikel makanan.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Karakteristik Lapisan Embrional dan Klasifikasi Animalia', '', '', $story9, '4. MENJODOHKAN', 'Pasangkan kelas vertebrata dengan ciri adaptasi khususnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Pisces (Ikan)', 'respon' => 'Jantung beruang 2 dan bernapas dengan insang'],
                ['premis' => 'Amphibia (Katak)', 'respon' => 'Mengalami metamorfosis dan bernapas kulit/paru-paru'],
                ['premis' => 'Aves (Burung)', 'respon' => 'Memiliki pundi-pundi hawa (sakus pneumatikus)'],
                ['premis' => 'Mammalia', 'respon' => 'Memiliki kelenjar susu (glandula mammae) dan berambut']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Pisces (Ikan)', 'respon' => 'Jantung beruang 2 dan bernapas dengan insang'],
                    ['premis' => 'Amphibia (Katak)', 'respon' => 'Mengalami metamorfosis dan bernapas kulit/paru-paru'],
                    ['premis' => 'Aves (Burung)', 'respon' => 'Memiliki pundi-pundi hawa (sakus pneumatikus)'],
                    ['premis' => 'Mammalia', 'respon' => 'Memiliki kelenjar susu (glandula mammae) dan berambut']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Karakteristik Lapisan Embrional dan Klasifikasi Animalia', '', '', $story9, '5. ISIAN SINGKAT', 'Hewan yang suhu tubuhnya selalu berfluktuasi mengikuti suhu lingkungan sekitarnya (hewan berdarah dingin) disebut hewan...'),
            'kunci_jawaban' => 'Poikiloterm',
            'konten_tambahan' => ['rubrik' => 'Poikiloterm']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): Konservasi Hayati In-Situ & Ex-Situ
        // ══════════════════════════════════════════════════════════════════
        $prompt10 = "Ilustrasi penangkaran konservasi satwa liar Indonesia. Sisi kiri memperlihatkan suaka margasatwa in-situ Taman Nasional Way Kambas untuk gajah Sumatera di habitat alami hutan primer. Sisi kanan memperlihatkan penangkaran ex-situ kebun raya dan taman safari dengan dokter hewan merawat satwa langka. Vektor edukasi konservasi modern.";
        $svg10 = '<svg width="280" height="90" viewBox="0 0 280 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="10" width="115" height="70" rx="8" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="72" y="28" font-size="9" font-weight="bold" fill="#047857" text-anchor="middle">KONSERVASI IN-SITU</text>
            <text x="72" y="46" font-size="8" fill="#065f46" text-anchor="middle">Di Habitat Alami Asli</text>
            <text x="72" y="65" font-size="7.5" fill="#059669" text-anchor="middle">TN Ujung Kulon, Way Kambas</text>
            <rect x="150" y="10" width="115" height="70" rx="8" fill="#eff6ff" stroke="#2563eb" stroke-width="1.5"/>
            <text x="207" y="28" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">KONSERVASI EX-SITU</text>
            <text x="207" y="46" font-size="8" fill="#1e40af" text-anchor="middle">Di Luar Habitat Alami</text>
            <text x="207" y="65" font-size="7.5" fill="#1e3a8a" text-anchor="middle">Kebun Raya Bogor, Taman Safari</text>
        </svg>';
        $story10 = 'Laju deforestasi dan perburuan liar mengancam kepunahan spesies flora dan fauna endemik Indonesia. Untuk melindungi keanekaragaman hayati, dilakukan dua strategi pelestarian: <u>Konservasi In-Situ</u> (pelestarian makhluk hidup di dalam habitat aslinya, seperti Taman Nasional, Cagar Alam, dan Suaka Margasatwa) serta <u>Konservasi Ex-Situ</u> (pelestarian yang dilakukan dengan memindahkan organisme langka ke luar habitat alaminya, seperti Kebun Binatang, Taman Safari, Kebun Raya, dan bank gen/sperma).';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Upaya Konservasi Keanekaragaman Hayati', $prompt10, $svg10, $story10, '1. PILIHAN GANDA', 'Manakah di antara kawasan konservasi berikut ini yang merupakan contoh pelaksanaan <u>konservasi ex-situ</u>?'),
            'opsi_a' => 'Taman Nasional Ujung Kulon di Banten',
            'opsi_b' => 'Taman Nasional Way Kambas di Lampung',
            'opsi_c' => 'Kebun Raya Bogor dan Taman Safari Cisarua',
            'opsi_d' => 'Cagar Alam Sibolangit di Sumatera Utara',
            'opsi_e' => 'Taman Nasional Tanjung Puting di Kalimantan Barat',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Taman Nasional Ujung Kulon di Banten',
                'B' => 'Taman Nasional Way Kambas di Lampung',
                'C' => 'Kebun Raya Bogor dan Taman Safari Cisarua',
                'D' => 'Cagar Alam Sibolangit di Sumatera Utara',
                'E' => 'Taman Nasional Tanjung Puting di Kalimantan Barat'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Upaya Konservasi Keanekaragaman Hayati', '', '', $story10, '2. PG KOMPLEKS (CENTANG)', 'Tindakan nyata manakah yang mendukung pelestarian keanekaragaman hayati secara <strong>berkelanjutan (sustainable)</strong>? (Pilihlah semua jawaban benar)'),
            'opsi_a' => 'Penerapan sistem tebang pilih dan reboisasi pada hutan produksi.',
            'opsi_b' => 'Pembuatan koridor satwa liar guna mencegah isolasi genetik akibat fragmentasi hutan.',
            'opsi_c' => 'Perburuan satwa langka musiman untuk keperluan cinderamata pariwisata.',
            'opsi_d' => 'Penetapan zona inti yang steril dari eksploitasi manusia di taman nasional.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Penerapan sistem tebang pilih dan reboisasi pada hutan produksi.', 'is_kunci' => true],
                    ['teks' => 'Pembuatan koridor satwa liar guna mencegah isolasi genetik akibat fragmentasi hutan.', 'is_kunci' => true],
                    ['teks' => 'Perburuan satwa langka musiman untuk keperluan cinderamata pariwisata.', 'is_kunci' => false],
                    ['teks' => 'Penetapan zona inti yang steril dari eksploitasi manusia di taman nasional.', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Upaya Konservasi Keanekaragaman Hayati', '', '', $story10, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kebijakan konservasi hayati berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Suaka margasatwa difokuskan untuk perlindungan hewan tertentu yang terancam punah beserta habitatnya.', 'kunci' => 'Benar'],
                    ['teks' => 'Konservasi in-situ dilakukan dengan mengambil satwa langka dari hutan untuk dipelihara di kebun binatang pribadi.', 'kunci' => 'Salah'],
                    ['teks' => 'Pembangunan bank plasma nutfah (seed bank) merupakan salah satu bentuk konservasi ex-situ untuk flora langka.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Upaya Konservasi Keanekaragaman Hayati', '', '', $story10, '4. MENJODOHKAN', 'Pasangkan kawasan konservasi in-situ di Indonesia dengan satwa langka prioritas yang dilindungi di dalamnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Taman Nasional Ujung Kulon', 'respon' => 'Badak Jawa Bercula Satu'],
                ['premis' => 'Taman Nasional Way Kambas', 'respon' => 'Pusat Konservasi Gajah Sumatera'],
                ['premis' => 'Taman Nasional Tanjung Puting', 'respon' => 'Rehabilitasi Orangutan'],
                ['premis' => 'Taman Nasional Komodo', 'respon' => 'Kadal Raksasa Komodo']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Taman Nasional Ujung Kulon', 'respon' => 'Badak Jawa Bercula Satu'],
                    ['premis' => 'Taman Nasional Way Kambas', 'respon' => 'Pusat Konservasi Gajah Sumatera'],
                    ['premis' => 'Taman Nasional Tanjung Puting', 'respon' => 'Rehabilitasi Orangutan'],
                    ['premis' => 'Taman Nasional Komodo', 'respon' => 'Kadal Raksasa Komodo']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Upaya Konservasi Keanekaragaman Hayati', '', '', $story10, '5. ISIAN SINGKAT', 'Metode pelestarian flora dan fauna langka yang dilakukan di luar habitat aslinya (misalnya di taman safari, herbarium, atau kebun botani) dinamakan konservasi...'),
            'kunci_jawaban' => 'Ex-situ',
            'konten_tambahan' => ['rubrik' => 'Ex-situ (atau Eksitu)']
        ];

        return $list;
    }
}
