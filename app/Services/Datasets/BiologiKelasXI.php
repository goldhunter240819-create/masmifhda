<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class BiologiKelasXI {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): Membran Sel & Transpor Membran
        // ══════════════════════════════════════════════════════════════════
        $prompt1 = "Ilustrasi 3D mikroskopis model mosaik cair (Fluid Mosaic Model) membran sel fosfolipid bilayer. Memperlihatkan kepala fosfat hidrofilik, ekor asam lemak hidrofobik, protein integral transmembran, protein perifer di permukaan, kolesterol, glikolipid, dan rantai karbohidrat glikoprotein. Molekul air berosmosis melalui saluran akuaporin. Gaya visual ilmiah laboratorium biofisika modern.";
        $svg1 = '<svg width="280" height="95" viewBox="0 0 280 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="8" width="270" height="80" rx="10" fill="#f8fafc" stroke="#64748b" stroke-width="1.5"/>
            <circle cx="30" cy="30" r="7" fill="#38bdf8"/> <line x1="30" y1="37" x2="30" y2="55" stroke="#f59e0b" stroke-width="2"/>
            <circle cx="50" cy="30" r="7" fill="#38bdf8"/> <line x1="50" y1="37" x2="50" y2="55" stroke="#f59e0b" stroke-width="2"/>
            <rect x="80" y="20" width="30" height="55" rx="6" fill="#818cf8" stroke="#4f46e5" stroke-width="1.5"/>
            <text x="95" y="52" font-size="7.5" fill="#ffffff" font-weight="bold" text-anchor="middle">Protein Int.</text>
            <circle cx="130" cy="30" r="7" fill="#38bdf8"/> <line x1="130" y1="37" x2="130" y2="55" stroke="#f59e0b" stroke-width="2"/>
            <circle cx="150" cy="30" r="7" fill="#38bdf8"/> <line x1="150" y1="37" x2="150" y2="55" stroke="#f59e0b" stroke-width="2"/>
            <circle cx="30" cy="65" r="7" fill="#38bdf8"/> <circle cx="50" cy="65" r="7" fill="#38bdf8"/>
            <circle cx="130" cy="65" r="7" fill="#38bdf8"/> <circle cx="150" cy="65" r="7" fill="#38bdf8"/>
            <text x="215" y="42" font-size="9" font-weight="bold" fill="#0284c7">Fosfolipid Bilayer</text>
            <text x="215" y="60" font-size="8" fill="#64748b">Kepala Hidrofilik +</text>
            <text x="215" y="72" font-size="8" fill="#d97706">Ekor Hidrofobik</text>
        </svg>';
        $story1 = 'Membran plasma memiliki sifat <u>semipermeabel / selektif permeabel</u> yang berfungsi mengatur lalu lintas zat masuk dan keluar sel. Berdasarkan model mosaik cair (Singer & Nicolson), membran tersusun atas <strong>lapisan ganda fosfolipid (bilayer)</strong> dengan kepala hidrofilik (suka air) menghadap keluar/ke dalam sitoplasma dan ekor asam lemak hidrofobik (menolak air) di bagian tengah. Transpor melintasi membran dibedakan menjadi <u>transpor pasif</u> (difusi dan osmosis, tanpa energi ATP) serta <u>transpor aktif</u> (membutuhkan energi ATP dan protein pompa, misalnya pompa natrium-kalium).';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Struktur Membran Plasma dan Mekanisme Transpor Sel', $prompt1, $svg1, $story1, '1. PILIHAN GANDA', 'Jika sel darah merah (eritrosit) dimasukkan ke dalam <u>larutan hipotonik</u> (seperti air murni/akuades), peristiwa biologis yang akan terjadi adalah...'),
            'opsi_a' => 'Krenasi, sel mengerut karena air keluar dari sel',
            'opsi_b' => 'Plasmolisis, membran sel terlepas dari dinding sel',
            'opsi_c' => 'Hemolisis, sel menggembung dan akhirnya pecah/lisis',
            'opsi_d' => 'Sel tetap normal karena memiliki dinding sel kaku',
            'opsi_e' => 'Transpor aktif natrium berhenti seketika',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Krenasi, sel mengerut karena air keluar dari sel',
                'B' => 'Plasmolisis, membran sel terlepas dari dinding sel',
                'C' => 'Hemolisis, sel menggembung dan akhirnya pecah/lisis',
                'D' => 'Sel tetap normal karena memiliki dinding sel kaku',
                'E' => 'Transpor aktif natrium berhenti seketika'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Struktur Membran Plasma dan Mekanisme Transpor Sel', '', '', $story1, '2. PG KOMPLEKS (CENTANG)', 'Manakah di antara pernyataan berikut yang tergolong mekanisme <strong>Transpor Pasif</strong> melintasi membran sel? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Perpindahan molekul gas O2 dan CO2 dari konsentrasi tinggi ke konsentrasi rendah secara difusi sederhana.',
            'opsi_b' => 'Perpindahan molekul air melintasi membran selektif permeabel melalui proses osmosis.',
            'opsi_c' => 'Difusi terfasilitasi molekul glukosa menggunakan bantuan protein transpor saluran/carrier.',
            'opsi_d' => 'Pemompaan ion Na+ keluar sel dan ion K+ ke dalam sel melawan gradien konsentrasi.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Perpindahan molekul gas O2 dan CO2 dari konsentrasi tinggi ke konsentrasi rendah secara difusi sederhana.', 'is_kunci' => true],
                    ['teks' => 'Perpindahan molekul air melintasi membran selektif permeabel melalui proses osmosis.', 'is_kunci' => true],
                    ['teks' => 'Difusi terfasilitasi molekul glukosa menggunakan bantuan protein transpor saluran/carrier.', 'is_kunci' => true],
                    ['teks' => 'Pemompaan ion Na+ keluar sel dan ion K+ ke dalam sel melawan gradien konsentrasi.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Struktur Membran Plasma dan Mekanisme Transpor Sel', '', '', $story1, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan perihal transpor seluler berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Bagian kepala fosfat pada membran sel bersifat polar hidrofilik sehingga dapat berinteraksi dengan molekul air.', 'kunci' => 'Benar'],
                    ['teks' => 'Transpor aktif memindahkan zat searah dengan gradien konsentrasi tanpa memerlukan molekul ATP.', 'kunci' => 'Salah'],
                    ['teks' => 'Plasmolisis terjadi ketika sel tumbuhan berada dalam larutan hipertonik sehingga sitoplasma dan vakuola kehilangan air.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Struktur Membran Plasma dan Mekanisme Transpor Sel', '', '', $story1, '4. MENJODOHKAN', 'Pasangkan istilah transpor membran sel dengan penjelasannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Osmosis', 'respon' => 'Difusi pelarut air melalui membran selektif permeabel'],
                ['premis' => 'Eksositosis', 'respon' => 'Pengeluaran makromolekul keluar sel melalui vesikel sekretori'],
                ['premis' => 'Fagositosis', 'respon' => 'Proses sel memakan partikel padat besar (seluler)'],
                ['premis' => 'Pompa Na-K', 'respon' => 'Transpor aktif mengeluarkan 3 Na+ dan memasukkan 2 K+']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Osmosis', 'respon' => 'Difusi pelarut air melalui membran selektif permeabel'],
                    ['premis' => 'Eksositosis', 'respon' => 'Pengeluaran makromolekul keluar sel melalui vesikel sekretori'],
                    ['premis' => 'Fagositosis', 'respon' => 'Proses sel memakan partikel padat besar (seluler)'],
                    ['premis' => 'Pompa Na-K', 'respon' => 'Transpor aktif mengeluarkan 3 Na+ dan memasukkan 2 K+']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(1, '1 - 5', 'Struktur Membran Plasma dan Mekanisme Transpor Sel', '', '', $story1, '5. ISIAN SINGKAT', 'Peristiwa lepasnya membran plasma dari dinding sel tumbuhan akibat keluarnya air saat sel berada di lingkungan hipertonik disebut...'),
            'kunci_jawaban' => 'Plasmolisis',
            'konten_tambahan' => ['rubrik' => 'Plasmolisis']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): Organel Sel Tumbuhan vs Sel Hewan
        // ══════════════════════════════════════════════════════════════════
        $prompt2 = "Ilustrasi anatomi ultrastruktur komparasi sel tumbuhan dan sel hewan beresolusi tinggi. Menampilkan organel sel tumbuhan: kloroplas hijau dengan grana tilakoid, vakuola sentral besar, dan dinding selulosa tebal. Pada sel hewan memperlihatkan sentriol, lisosom hidrolitik, serta mitokondria bermembran ganda dengan krista berlekuk. Diagram medis biologi sel modern.";
        $svg2 = '<svg width="270" height="95" viewBox="0 0 270 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="8" width="115" height="80" rx="8" fill="#f0fdf4" stroke="#16a34a" stroke-width="2"/>
            <text x="67" y="26" font-size="9" font-weight="bold" fill="#15803d" text-anchor="middle">SEL TUMBUHAN</text>
            <text x="67" y="44" font-size="8" fill="#166534" text-anchor="middle">&bull; Dinding Sel (Selulosa)</text>
            <text x="67" y="58" font-size="8" fill="#166534" text-anchor="middle">&bull; Kloroplas (Fotosintesis)</text>
            <text x="67" y="72" font-size="8" fill="#166534" text-anchor="middle">&bull; Vakuola Sentral Besar</text>
            <rect x="145" y="8" width="115" height="80" rx="8" fill="#eff6ff" stroke="#2563eb" stroke-width="2"/>
            <text x="202" y="26" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">SEL HEWAN</text>
            <text x="202" y="44" font-size="8" fill="#1e40af" text-anchor="middle">&bull; Sentriol / Sentrosom</text>
            <text x="202" y="58" font-size="8" fill="#1e40af" text-anchor="middle">&bull; Lisosom (Enzim Hidrolitik)</text>
            <text x="202" y="72" font-size="8" fill="#1e40af" text-anchor="middle">&bull; Vakuola Kecil / Tidak Ada</text>
        </svg>';
        $story2 = 'Meskipun sel tumbuhan dan sel hewan sama-sama tergolong eukariotik dengan organel dasar seperti nukleus, ribosom, retikulum endoplasma (RE), badan Golgi, dan mitokondria, keduanya memiliki perbedaan spesifik. Sel tumbuhan memiliki <u>dinding sel</u> dari selulosa untuk kekuatan mekanik, <u>kloroplas</u> tempat fotosintesis berlangsung, dan <u>vakuola sentral</u> besar penyimpan turgiditas. Sebaliknya, sel hewan memiliki <u>sentriol</u> yang berperan dalam pembelahan sel (pembentukan spindel) serta <u>lisosom</u> untuk pencernaan intraseluler (autofagi & autolisis).';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Ultrastruktur Organel Sel Eukariotik', $prompt2, $svg2, $story2, '1. PILIHAN GANDA', 'Organel sel yang memiliki membran ganda berlekuk-lekuk (krista) dan berfungsi sebagai pusat respirasi seluler penghasil energi utama (ATP) adalah...'),
            'opsi_a' => 'Ribosom',
            'opsi_b' => 'Badan Golgi',
            'opsi_c' => 'Mitokondria',
            'opsi_d' => 'Retikulum Endoplasma Kasar',
            'opsi_e' => 'Peroksisom',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Ribosom',
                'B' => 'Badan Golgi',
                'C' => 'Mitokondria',
                'D' => 'Retikulum Endoplasma Kasar',
                'E' => 'Peroksisom'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Ultrastruktur Organel Sel Eukariotik', '', '', $story2, '2. PG KOMPLEKS (CENTANG)', 'Organel atau struktur sel manakah berikut ini yang <strong>hanya ditemukan pada sel tumbuhan</strong> dan tidak dimiliki oleh sel hewan? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Kloroplas (Plastida)',
            'opsi_b' => 'Dinding sel dari bahan selulosa dan hemiselulosa',
            'opsi_c' => 'Lisosom yang mengandung enzim hidrolitik aktif',
            'opsi_d' => 'Vakuola sentral permanen berukuran besar',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Kloroplas (Plastida)', 'is_kunci' => true],
                    ['teks' => 'Dinding sel dari bahan selulosa dan hemiselulosa', 'is_kunci' => true],
                    ['teks' => 'Lisosom yang mengandung enzim hidrolitik aktif', 'is_kunci' => false],
                    ['teks' => 'Vakuola sentral permanen berukuran besar', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Ultrastruktur Organel Sel Eukariotik', '', '', $story2, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari fungsi organel sel berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Retikulum Endoplasma Kasar (RE Kasar) ditempeli banyak ribosom dan berperan dalam sintesis protein transpor.', 'kunci' => 'Benar'],
                    ['teks' => 'Retikulum Endoplasma Halus (RE Halus) berfungsi utama mensintesis asam nukleat DNA.', 'kunci' => 'Salah'],
                    ['teks' => 'Badan Golgi berfungsi memodifikasi, mengemas, dan menyortir molekul makromolekul untuk sekresi keluar sel.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Ultrastruktur Organel Sel Eukariotik', '', '', $story2, '4. MENJODOHKAN', 'Pasangkan nama organel sel dengan fungsi spesifiknya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Ribosom', 'respon' => 'Tempat sintesis protein (translasi)'],
                ['premis' => 'Lisosom', 'respon' => 'Pencernaan intraseluler dan autofagi'],
                ['premis' => 'Peroksisom', 'respon' => 'Menguraikan hidrogen peroksida (H2O2) dengan enzim katalase'],
                ['premis' => 'Sentriol', 'respon' => 'Membentuk benang spindel saat pembelahan sel']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Ribosom', 'respon' => 'Tempat sintesis protein (translasi)'],
                    ['premis' => 'Lisosom', 'respon' => 'Pencernaan intraseluler dan autofagi'],
                    ['premis' => 'Peroksisom', 'respon' => 'Menguraikan hidrogen peroksida (H2O2) dengan enzim katalase'],
                    ['premis' => 'Sentriol', 'respon' => 'Membentuk benang spindel saat pembelahan sel']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(2, '6 - 10', 'Ultrastruktur Organel Sel Eukariotik', '', '', $story2, '5. ISIAN SINGKAT', 'Tumpukan kantung tilakoid bermembran di dalam kloroplas yang menjadi tempat berlangsungnya reaksi terang fotosintesis dinamakan...'),
            'kunci_jawaban' => 'Grana',
            'konten_tambahan' => ['rubrik' => 'Grana (atau Granum)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): Jaringan Tumbuhan & Berkas Pengangkut
        // ══════════════════════════════════════════════════════════════════
        $prompt3 = "Diagram irisan melintang batang tumbuhan dikotil vs monokotil di bawah mikroskop optik. Menampilkan lapisan epidermis berlilin kutikula, korteks parenkim, lingkaran kambium vaskular silindris, floem di sebelah luar, xilem di sebelah dalam dengan trakea berongga besar, serta empulur di pusat batang dikotil. Vektor ilustrasi anatomi tumbuhan presisi tinggi.";
        $svg3 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="65" cy="45" r="38" fill="#f8fafc" stroke="#16a34a" stroke-width="2"/>
            <circle cx="65" cy="45" r="24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-dasharray="3"/>
            <text x="65" y="48" font-size="8.5" font-weight="bold" fill="#0f172a" text-anchor="middle">DIKOTIL</text>
            <text x="65" y="60" font-size="7" fill="#dc2626" text-anchor="middle">Kambium Teratur</text>
            <circle cx="195" cy="45" r="38" fill="#f8fafc" stroke="#0284c7" stroke-width="2"/>
            <circle cx="180" cy="35" r="4" fill="#0284c7"/> <circle cx="210" cy="35" r="4" fill="#0284c7"/>
            <circle cx="195" cy="55" r="4" fill="#0284c7"/> <circle cx="175" cy="55" r="4" fill="#0284c7"/>
            <text x="195" y="75" font-size="8" font-weight="bold" fill="#0369a1" text-anchor="middle">MONOKOTIL</text>
            <text x="195" y="85" font-size="6.5" fill="#64748b" text-anchor="middle">Berkas Tersebar</text>
        </svg>';
        $story3 = 'Jaringan tumbuhan terbagi menjadi jaringan meristem (sel embrionik aktif membelah) dan jaringan permanen dewasa (epidermis, parenkim, kolenkim, sklerenkim, dan jaringan pengangkut vaskular). Pengangkutan air dan mineral dari akar ke daun dilakukan oleh <u>jaringan xilem</u> (pembuluh kayu) melalui daya isap daun, kapilaritas batang, dan tekanan akar. Sementara pengangkutan translokasi hasil fotosintesis dari daun ke seluruh tubuh tumbuhan dipikul oleh <u>jaringan floem</u> (pembuluh tapis). Pada batang dikotil terdapat <strong>kambium vaskular</strong> yang memungkinkan pertumbuhan sekunder melebar.';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur dan Fungsi Jaringan Tumbuhan', $prompt3, $svg3, $story3, '1. PILIHAN GANDA', 'Jaringan penguat pada tumbuhan yang tersusun atas sel-sel mati dengan dinding sel sekunder yang mengalami penebalan merata oleh <u>zat lignin (kayu)</u> adalah...'),
            'opsi_a' => 'Jaringan Kolenkim',
            'opsi_b' => 'Jaringan Sklerenkim',
            'opsi_c' => 'Jaringan Parenkim Palisade',
            'opsi_d' => 'Jaringan Kambium Felogen',
            'opsi_e' => 'Jaringan Epidermis',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Jaringan Kolenkim',
                'B' => 'Jaringan Sklerenkim',
                'C' => 'Jaringan Parenkim Palisade',
                'D' => 'Jaringan Kambium Felogen',
                'E' => 'Jaringan Epidermis'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur dan Fungsi Jaringan Tumbuhan', '', '', $story3, '2. PG KOMPLEKS (CENTANG)', 'Faktor fisiologis apa sajakah yang mendukung berlangsungnya <strong>pengangkutan air dan garam mineral</strong> melalui xilem dari akar menuju daun pohon yang tinggi? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Daya isap daun akibat peristiwa transpirasi uap air di stomata.',
            'opsi_b' => 'Daya kapilaritas pembuluh xilem karena sifat adhesi dan kohesi molekul air.',
            'opsi_c' => 'Tekanan akar akibat penyerapan air secara osmosis oleh rambut akar.',
            'opsi_d' => 'Daya dorong aliran translokasi floem dari sel-sel pengiring.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Daya isap daun akibat peristiwa transpirasi uap air di stomata.', 'is_kunci' => true],
                    ['teks' => 'Daya kapilaritas pembuluh xilem karena sifat adhesi dan kohesi molekul air.', 'is_kunci' => true],
                    ['teks' => 'Tekanan akar akibat penyerapan air secara osmosis oleh rambut akar.', 'is_kunci' => true],
                    ['teks' => 'Daya dorong aliran translokasi floem dari sel-sel pengiring.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 13: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur dan Fungsi Jaringan Tumbuhan', '', '', $story3, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan perihal anatomi organ tumbuhan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pada daun tumbuhan dikotil, proses fotosintesis utama berlangsung pada jaringan parenkim palisade (tiang) yang kaya kloroplas.', 'kunci' => 'Benar'],
                    ['teks' => 'Batang tumbuhan monokotil mengalami pertumbuhan sekunder membesar karena memiliki kambium vaskular tersusun melingkar.', 'kunci' => 'Salah'],
                    ['teks' => 'Stomata pada epidermis daun dibatasi oleh sepasang sel penjaga (guard cell) yang mengatur buka tutup pori.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur dan Fungsi Jaringan Tumbuhan', '', '', $story3, '4. MENJODOHKAN', 'Pasangkan tipe jaringan tumbuhan dengan karakteristik utamanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Meristem Apikal', 'respon' => 'Terletak di ujung akar & pucuk batang untuk pertambahan panjang'],
                ['premis' => 'Parenkim Spons (Bunga Karang)', 'respon' => 'Berongga udara luas dan membantu pertukaran gas di daun'],
                ['premis' => 'Kolenkim', 'respon' => 'Penguat organ muda dengan penebalan selulosa di sudut sel'],
                ['premis' => 'Xilem', 'respon' => 'Tersusun atas unsur trakeal untuk mengangkut air dan mineral']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Meristem Apikal', 'respon' => 'Terletak di ujung akar & pucuk batang untuk pertambahan panjang'],
                    ['premis' => 'Parenkim Spons (Bunga Karang)', 'respon' => 'Berongga udara luas dan membantu pertukaran gas di daun'],
                    ['premis' => 'Kolenkim', 'respon' => 'Penguat organ muda dengan penebalan selulosa di sudut sel'],
                    ['premis' => 'Xilem', 'respon' => 'Tersusun atas unsur trakeal untuk mengangkut air dan mineral']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(3, '11 - 15', 'Struktur dan Fungsi Jaringan Tumbuhan', '', '', $story3, '5. ISIAN SINGKAT', 'Jaringan meristem sekunder pada tumbuhan berkayu yang aktivitas pembelahannya ke arah dalam membentuk xilem sekunder dan ke arah luar membentuk floem sekunder dinamakan...'),
            'kunci_jawaban' => 'Kambium Vaskular',
            'konten_tambahan' => ['rubrik' => 'Kambium Vaskular (atau Kambium)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): Sistem Peredaran Darah & Jantung Manusia
        // ══════════════════════════════════════════════════════════════════
        $prompt4 = "Diagram medis kardiologi anatomi jantung manusia empat ruang beresolusi tinggi. Menampilkan atrium dexter, ventrikel dexter, atrium sinister, ventrikel sinister dengan dinding miokardium tebal, katup trikuspidalis, katup bikuspidalis (mitral), arteri pulmonalis, vena pulmonalis, dan aorta pembawa darah kaya O2 ke seluruh tubuh. Alur peredaran darah ganda tertutup terperinci.";
        $svg4 = '<svg width="270" height="95" viewBox="0 0 270 95" xmlns="http://www.w3.org/2000/svg">
            <rect x="25" y="10" width="105" height="75" rx="8" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="77" y="28" font-size="8.5" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Sisi Kanan (Dekat Vena)</text>
            <text x="77" y="44" font-size="8" fill="#1e40af" text-anchor="middle">Atrium Kanan &amp; Bilik Kanan</text>
            <text x="77" y="62" font-size="7.5" fill="#dc2626" text-anchor="middle">Darah Kaya CO2</text>
            <text x="77" y="74" font-size="7" fill="#64748b" text-anchor="middle">Dipompa ke Paru-paru</text>
            <rect x="140" y="10" width="105" height="75" rx="8" fill="#fef2f2" stroke="#ef4444" stroke-width="1.5"/>
            <text x="192" y="28" font-size="8.5" font-weight="bold" fill="#b91c1c" text-anchor="middle">Sisi Kiri (Aorta)</text>
            <text x="192" y="44" font-size="8" fill="#991b1b" text-anchor="middle">Atrium Kiri &amp; Bilik Kiri</text>
            <text x="192" y="62" font-size="7.5" fill="#16a34a" text-anchor="middle">Darah Kaya O2</text>
            <text x="192" y="74" font-size="7" fill="#64748b" text-anchor="middle">Dipompa ke Seluruh Tubuh</text>
        </svg>';
        $story4 = 'Sistem kardiovaskular manusia merupakan peredaran darah tertutup dan ganda (melewati jantung dua kali dalam satu putaran peredaran lengkap). Jantung terdiri atas empat ruang: <u>atrium kanan</u> (menerima darah kaya CO2 dari vena kava), <u>ventrikel kanan</u> (memompa darah ke paru-paru lewat arteri pulmonalis), <u>atrium kiri</u> (menerima darah kaya O2 dari vena pulmonalis), dan <u>ventrikel kiri</u> dengan dinding miokardium paling tebal (memompa darah kaya O2 ke seluruh tubuh melalui pembuluh aorta).';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Anatomi Jantung dan Fisiologi Sirkulasi Darah Manusia', $prompt4, $svg4, $story4, '1. PILIHAN GANDA', 'Ruang jantung yang memiliki <u>dinding otot paling tebal</u> karena harus menghasilkan tekanan hidrostatik tinggi untuk memompa darah ke seluruh jaringan tubuh adalah...'),
            'opsi_a' => 'Atrium Dexter (Serambi Kanan)',
            'opsi_b' => 'Ventrikel Dexter (Bilik Kanan)',
            'opsi_c' => 'Atrium Sinister (Serambi Kiri)',
            'opsi_d' => 'Ventrikel Sinister (Bilik Kiri)',
            'opsi_e' => 'Septum Atrioventrikularis',
            'kunci_jawaban' => 'D',
            'konten_tambahan' => ['kunci' => 'D', 'opsi' => [
                'A' => 'Atrium Dexter (Serambi Kanan)',
                'B' => 'Ventrikel Dexter (Bilik Kanan)',
                'C' => 'Atrium Sinister (Serambi Kiri)',
                'D' => 'Ventrikel Sinister (Bilik Kiri)',
                'E' => 'Septum Atrioventrikularis'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Anatomi Jantung dan Fisiologi Sirkulasi Darah Manusia', '', '', $story4, '2. PG KOMPLEKS (CENTANG)', 'Pilihlah pernyataan yang <strong>BENAR</strong> mengenai perbedaan antara pembuluh Arteri (Nadi) dan Vena (Balik)! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Arteri memiliki dinding yang tebal, elastis, dan letaknya cenderung tersembunyi di dalam jaringan otot.',
            'opsi_b' => 'Vena memiliki katup-katup di sepanjang pembuluh untuk mencegah aliran balik darah akibat gravitasi.',
            'opsi_c' => 'Denyut arteri dapat diraba dengan jelas di pergelangan tangan dan leher mengikuti irama sistol jantung.',
            'opsi_d' => 'Semua pembuluh vena tanpa terkecuali selalu mengangkut darah yang kaya akan karbon dioksida.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Arteri memiliki dinding yang tebal, elastis, dan letaknya cenderung tersembunyi di dalam jaringan otot.', 'is_kunci' => true],
                    ['teks' => 'Vena memiliki katup-katup di sepanjang pembuluh untuk mencegah aliran balik darah akibat gravitasi.', 'is_kunci' => true],
                    ['teks' => 'Denyut arteri dapat diraba dengan jelas di pergelangan tangan dan leher mengikuti irama sistol jantung.', 'is_kunci' => true],
                    ['teks' => 'Semua pembuluh vena tanpa terkecuali selalu mengangkut darah yang kaya akan karbon dioksida.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Anatomi Jantung dan Fisiologi Sirkulasi Darah Manusia', '', '', $story4, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan seputar sirkulasi darah berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Vena pulmonalis mengalirkan darah yang kaya akan oksigen dari kapiler paru-paru menuju ke serambi kiri jantung.', 'kunci' => 'Benar'],
                    ['teks' => 'Fase diastol terjadi saat ventrikel jantung berkontraksi kuat menyemburkan darah ke dalam aorta.', 'kunci' => 'Salah'],
                    ['teks' => 'Katup bikuspidalis (mitral) terletak di antara atrium kiri dan ventrikel kiri jantung.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Anatomi Jantung dan Fisiologi Sirkulasi Darah Manusia', '', '', $story4, '4. MENJODOHKAN', 'Pasangkan kelainan/gangguan sistem sirkulasi dengan penyebab medisnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Aterosklerosis', 'respon' => 'Penyempitan lumen pembuluh darah akibat endapan plak lemak kolesterol'],
                ['premis' => 'Arteriosklerosis', 'respon' => 'Pengerasan dinding arteri akibat penumpukan senyawa zat kapur'],
                ['premis' => 'Hipertensi', 'respon' => 'Kondisi tekanan darah sistol/diastol melebihi ambang 140/90 mmHg'],
                ['premis' => 'Varises', 'respon' => 'Pelebaran pembuluh vena superfisial terutama pada area betis/kaki']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Aterosklerosis', 'respon' => 'Penyempitan lumen pembuluh darah akibat endapan plak lemak kolesterol'],
                    ['premis' => 'Arteriosklerosis', 'respon' => 'Pengerasan dinding arteri akibat penumpukan senyawa zat kapur'],
                    ['premis' => 'Hipertensi', 'respon' => 'Kondisi tekanan darah sistol/diastol melebihi ambang 140/90 mmHg'],
                    ['premis' => 'Varises', 'respon' => 'Pelebaran pembuluh vena superfisial terutama pada area betis/kaki']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(4, '16 - 20', 'Anatomi Jantung dan Fisiologi Sirkulasi Darah Manusia', '', '', $story4, '5. ISIAN SINGKAT', 'Pembuluh nadi terbesar yang keluar langsung dari ventrikel kiri jantung untuk mendistribusikan darah bersih beroksigen ke seluruh tubuh dinamakan pembuluh...'),
            'kunci_jawaban' => 'Aorta',
            'konten_tambahan' => ['rubrik' => 'Aorta']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): Komponen Darah & Golongan Darah ABO
        // ══════════════════════════════════════════════════════════════════
        $prompt5 = "Ilustrasi hematologi uji golongan darah sistem ABO dan Rhesus pada piring kaca uji (well plate). Menampilkan reaksi aglutinasi gumpalan sel darah merah (eritrosit) saat ditetesi serum anti-A, serum anti-B, dan serum anti-D (Rhesus). Gaya foto makro laboratorium patologi klinis modern.";
        $svg5 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="8" width="55" height="74" rx="6" fill="#fef2f2" stroke="#ef4444" stroke-width="1.5"/>
            <text x="37" y="24" font-size="9" font-weight="bold" fill="#b91c1c" text-anchor="middle">Gol. A</text>
            <text x="37" y="44" font-size="7.5" fill="#7f1d1d" text-anchor="middle">Aglutinogen A</text>
            <text x="37" y="60" font-size="7.5" fill="#dc2626" text-anchor="middle">Aglutinin &beta;</text>
            <rect x="75" y="8" width="55" height="74" rx="6" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="102" y="24" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Gol. B</text>
            <text x="102" y="44" font-size="7.5" fill="#1e40af" text-anchor="middle">Aglutinogen B</text>
            <text x="102" y="60" font-size="7.5" fill="#2563eb" text-anchor="middle">Aglutinin &alpha;</text>
            <rect x="140" y="8" width="55" height="74" rx="6" fill="#fdf4ff" stroke="#a855f7" stroke-width="1.5"/>
            <text x="167" y="24" font-size="9" font-weight="bold" fill="#7e22ce" text-anchor="middle">Gol. AB</text>
            <text x="167" y="44" font-size="7.5" fill="#6b21a8" text-anchor="middle">Aglutinogen A,B</text>
            <text x="167" y="60" font-size="7.5" fill="#9333ea" text-anchor="middle">Tanpa Aglutinin</text>
            <rect x="205" y="8" width="55" height="74" rx="6" fill="#f8fafc" stroke="#64748b" stroke-width="1.5"/>
            <text x="232" y="24" font-size="9" font-weight="bold" fill="#334155" text-anchor="middle">Gol. O</text>
            <text x="232" y="44" font-size="7.5" fill="#475569" text-anchor="middle">Tanpa Aglutinogen</text>
            <text x="232" y="60" font-size="7.5" fill="#64748b" text-anchor="middle">Aglutinin &alpha;,&beta;</text>
        </svg>';
        $story5 = 'Darah tersusun dari plasma darah (55%) dan elemen seluler (45%) yang mencakup eritrosit (sel darah merah pembawa O2 berhemoglobin), leukosit (sel darah putih sistem imun), dan trombosit (keping darah untuk pembekuan darah). Berdasarkan sistem ABO (Landsteiner), golongan darah ditentukan oleh ada tidaknya <u>aglutinogen (antigen)</u> pada permukaan eritrosit dan <u>aglutinin (antibodi)</u> di dalam plasma darah. Aglutinasi (penggumpalan darah) akan terjadi jika aglutinogen bertemu dengan aglutinin yang bersesuaian (misal antigen A bertemu antibodi anti-A).';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Komponen Darah dan Uji Golongan Darah ABO', $prompt5, $svg5, $story5, '1. PILIHAN GANDA', 'Dalam suatu uji sampel darah pasien di laboratorium, diperoleh hasil bahwa darah tersebut <u>menggumpal saat ditetesi serum anti-A</u>, namun <u>tidak menggumpal saat ditetesi serum anti-B</u>. Apakah golongan darah pasien tersebut?'),
            'opsi_a' => 'Golongan Darah A',
            'opsi_b' => 'Golongan Darah B',
            'opsi_c' => 'Golongan Darah AB',
            'opsi_d' => 'Golongan Darah O',
            'opsi_e' => 'Golongan Darah Rhesus Negatif',
            'kunci_jawaban' => 'A',
            'konten_tambahan' => ['kunci' => 'A', 'opsi' => [
                'A' => 'Golongan Darah A',
                'B' => 'Golongan Darah B',
                'C' => 'Golongan Darah AB',
                'D' => 'Golongan Darah O',
                'E' => 'Golongan Darah Rhesus Negatif'
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Komponen Darah dan Uji Golongan Darah ABO', '', '', $story5, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai proses mekanisme pembekuan darah (koagulasi)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Trombosit yang pecah saat luka melepaskan enzim trombokinase (tromboplastin).',
            'opsi_b' => 'Enzim trombokinase bersama ion kalsium (Ca2+) dan vitamin K mengubah protrombin menjadi trombin aktif.',
            'opsi_c' => 'Trombin mengubah protein fibrinogen terlarut menjadi jaring-jaring benang fibrin yang menjerat sel darah.',
            'opsi_d' => 'Hemoglobin dalam eritrosit bertugas mengikat benang fibrin agar luka cepat tertutup.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Trombosit yang pecah saat luka melepaskan enzim trombokinase (tromboplastin).', 'is_kunci' => true],
                    ['teks' => 'Enzim trombokinase bersama ion kalsium (Ca2+) dan vitamin K mengubah protrombin menjadi trombin aktif.', 'is_kunci' => true],
                    ['teks' => 'Trombin mengubah protein fibrinogen terlarut menjadi jaring-jaring benang fibrin yang menjerat sel darah.', 'is_kunci' => true],
                    ['teks' => 'Hemoglobin dalam eritrosit bertugas mengikat benang fibrin agar luka cepat tertutup.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Komponen Darah dan Uji Golongan Darah ABO', '', '', $story5, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari prinsip transfusi darah berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Individu bergolongan darah O disebut donor universal karena sel darah merahnya tidak memiliki aglutinogen A maupun B.', 'kunci' => 'Benar'],
                    ['teks' => 'Individu bergolongan darah AB disebut resipien universal karena di dalam plasmanya memiliki aglutinin anti-A dan anti-B.', 'kunci' => 'Salah'],
                    ['teks' => 'Eritroblastosis fetalis dapat terjadi jika ibu bergolongan Rhesus negatif mengandung janin kedua yang bergolongan Rhesus positif.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Komponen Darah dan Uji Golongan Darah ABO', '', '', $story5, '4. MENJODOHKAN', 'Pasangkan jenis sel darah dengan fungsi fisiologis utamanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Eritrosit', 'respon' => 'Mengangkut gas O2 dan sebagian CO2 melalui hemoglobin'],
                ['premis' => 'Limfosit B', 'respon' => 'Menghasilkan antibodi spesifik untuk imunitas humoral'],
                ['premis' => 'Neutrofil', 'respon' => 'Sel fagosit garis pertama yang memakan bakteri patogen'],
                ['premis' => 'Trombosit', 'respon' => 'Membantu proses hemostasis dan pembekuan darah']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Eritrosit', 'respon' => 'Mengangkut gas O2 dan sebagian CO2 melalui hemoglobin'],
                    ['premis' => 'Limfosit B', 'respon' => 'Menghasilkan antibodi spesifik untuk imunitas humoral'],
                    ['premis' => 'Neutrofil', 'respon' => 'Sel fagosit garis pertama yang memakan bakteri patogen'],
                    ['premis' => 'Trombosit', 'respon' => 'Membantu proses hemostasis dan pembekuan darah']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(5, '21 - 25', 'Komponen Darah dan Uji Golongan Darah ABO', '', '', $story5, '5. ISIAN SINGKAT', 'Protein pembawa oksigen yang mengandung ion besi (Fe) dan memberi warna merah khas pada sel darah merah manusia dinamakan...'),
            'kunci_jawaban' => 'Hemoglobin',
            'konten_tambahan' => ['rubrik' => 'Hemoglobin (atau Hb)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): Sistem Pencernaan & Enzim Metabolisme
        // ══════════════════════════════════════════════════════════════════
        $prompt6 = "Diagram anatomi sistem pencernaan manusia dari rongga mulut, esofagus, lambung (sekresi asam klorida HCl dan pepsinogen), hati dan kantung empedu, pankreas (lipase, amilase, tripsinogen), hingga usus halus dengan vili absorpsi zat gizi mikroskopis. Infografis gastrointestinal medis resolusi tinggi.";
        $svg6 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="8" width="70" height="74" rx="8" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="50" y="26" font-size="9" font-weight="bold" fill="#b45309" text-anchor="middle">LAMBUNG</text>
            <text x="50" y="44" font-size="7.5" fill="#78350f" text-anchor="middle">&bull; Asam HCl</text>
            <text x="50" y="58" font-size="7.5" fill="#78350f" text-anchor="middle">&bull; Enzim Pepsin</text>
            <text x="50" y="72" font-size="7.5" fill="#78350f" text-anchor="middle">&bull; Enzim Renin</text>
            <rect x="100" y="8" width="70" height="74" rx="8" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
            <text x="135" y="26" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">PANKREAS</text>
            <text x="135" y="44" font-size="7.5" fill="#1e40af" text-anchor="middle">&bull; Amilase</text>
            <text x="135" y="58" font-size="7.5" fill="#1e40af" text-anchor="middle">&bull; Tripsin</text>
            <text x="135" y="72" font-size="7.5" fill="#1e40af" text-anchor="middle">&bull; Lipase</text>
            <rect x="185" y="8" width="70" height="74" rx="8" fill="#ecfdf5" stroke="#10b981" stroke-width="1.5"/>
            <text x="220" y="26" font-size="9" font-weight="bold" fill="#047857" text-anchor="middle">USUS HALUS</text>
            <text x="220" y="44" font-size="7.5" fill="#065f46" text-anchor="middle">&bull; Vili Absorpsi</text>
            <text x="220" y="58" font-size="7.5" fill="#065f46" text-anchor="middle">&bull; Sukrase/Maltase</text>
            <text x="220" y="72" font-size="7.5" fill="#065f46" text-anchor="middle">&bull; Peptidase</text>
        </svg>';
        $story6 = 'Pencernaan makanan pada manusia berlangsung secara mekanik dan kimiawi. Di lambung, getah lambung yang mengandung <u>asam klorida (HCl)</u> menciptakan suasana asam (pH 1-2) untuk membunuh bakteri patogen dan mengaktifkan pepsinogen menjadi enzim <u>pepsin</u> (pemecah protein menjadi pepton). Di usus dua belas jari (duodenum), kimus dinetralkan oleh bikarbonat pankreas, lalu dicerna lebih lanjut oleh enzim tripsin, amilase pankreas, dan lipase sebelum diserap oleh mikrovili pada dinding usus halus (jejunum dan ileum).';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Fisiologi Sistem Pencernaan dan Enzim Tubuh', $prompt6, $svg6, $story6, '1. PILIHAN GANDA', 'Enzim pencernaan yang disekresikan oleh pankreas dan berfungsi menghidrolisis molekul lemak netral menjadi <u>asam lemak dan gliserol</u> adalah...'),
            'opsi_a' => 'Tripsin',
            'opsi_b' => 'Pepsin',
            'opsi_c' => 'Lipase',
            'opsi_d' => 'Amilase',
            'opsi_e' => 'Enterokinase',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Tripsin',
                'B' => 'Pepsin',
                'C' => 'Lipase',
                'D' => 'Amilase',
                'E' => 'Enterokinase'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Fisiologi Sistem Pencernaan dan Enzim Tubuh', '', '', $story6, '2. PG KOMPLEKS (CENTANG)', 'Fungsi biologis manakah yang diperankan oleh cairan getah empedu yang dihasilkan oleh organ hati (hepar)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Mengemulsikan lemak makanan menjadi butiran-butiran halus (misel) agar mudah dicerna lipase.',
            'opsi_b' => 'Memberi warna khas kuning kecokelatan pada feses dan urine melalui pigmen bilirubin/biliverdin.',
            'opsi_c' => 'Mencerna karbohidrat kompleks menjadi glukosa sederhana di dalam lambung.',
            'opsi_d' => 'Membantu penyerapan vitamin-vitamin yang larut dalam lemak (A, D, E, K).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Mengemulsikan lemak makanan menjadi butiran-butiran halus (misel) agar mudah dicerna lipase.', 'is_kunci' => true],
                    ['teks' => 'Memberi warna khas kuning kecokelatan pada feses dan urine melalui pigmen bilirubin/biliverdin.', 'is_kunci' => true],
                    ['teks' => 'Mencerna karbohidrat kompleks menjadi glukosa sederhana di dalam lambung.', 'is_kunci' => false],
                    ['teks' => 'Membantu penyerapan vitamin-vitamin yang larut dalam lemak (A, D, E, K).', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Fisiologi Sistem Pencernaan dan Enzim Tubuh', '', '', $story6, '3. TABEL BENAR / SALAH', 'Tentukan status Benar atau Salah dari proses pencernaan manusia berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Enzim ptialin (amilase ludah) bekerja optimal pada pH netral untuk memecah amilum menjadi maltosa.', 'kunci' => 'Benar'],
                    ['teks' => 'Penyerapan sari-sari makanan seperti glukosa dan asam amino paling banyak terjadi di lambung.', 'kunci' => 'Salah'],
                    ['teks' => 'Usus besar (kolon) berfungsi mereabsorpsi kelebihan air dan membusukkan sisa makanan dibantu bakteri E. coli.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Fisiologi Sistem Pencernaan dan Enzim Tubuh', '', '', $story6, '4. MENJODOHKAN', 'Pasangkan organ pencernaan dengan getah/enzim spesifik yang disekresikannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Lambung (Gaster)', 'respon' => 'Pepsin, Renin, dan Asam Klorida (HCl)'],
                ['premis' => 'Kelenjar Parotis (Mulut)', 'respon' => 'Enzim Ptialin (Amilase Saliva)'],
                ['premis' => 'Pankreas', 'respon' => 'Tripsin, Lipase Pankreas, dan Natrium Bikarbonat'],
                ['premis' => 'Hati (Hepar)', 'respon' => 'Cairan Empedu untuk Emulsifikasi Lemak']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Lambung (Gaster)', 'respon' => 'Pepsin, Renin, dan Asam Klorida (HCl)'],
                    ['premis' => 'Kelenjar Parotis (Mulut)', 'respon' => 'Enzim Ptialin (Amilase Saliva)'],
                    ['premis' => 'Pankreas', 'respon' => 'Tripsin, Lipase Pankreas, dan Natrium Bikarbonat'],
                    ['premis' => 'Hati (Hepar)', 'respon' => 'Cairan Empedu untuk Emulsifikasi Lemak']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(6, '26 - 30', 'Fisiologi Sistem Pencernaan dan Enzim Tubuh', '', '', $story6, '5. ISIAN SINGKAT', 'Lipatan-lipatan jonjot mikroskopis pada dinding epitel usus halus yang berfungsi memperluas bidang penyerapan sari makanan disebut...'),
            'kunci_jawaban' => 'Vili',
            'konten_tambahan' => ['rubrik' => 'Vili (atau Mikrovili)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): Sistem Respirasi & Alveolus Paru-paru
        // ══════════════════════════════════════════════════════════════════
        $prompt7 = "Ilustrasi anatomi sistem pernapasan manusia. Detail mikroskopis bronkiolus terminal bermuara pada kantung udara alveolus yang dikelilingi jaring-jaring pembuluh kapiler darah paru. Menampilkan difusi gas oksigen O2 dari rongga alveoli ke sel darah merah dan karbon dioksida CO2 keluar ke rongga alveolus. Vektor visual medis pulmonologi modern.";
        $svg7 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="85" cy="45" r="34" fill="#eff6ff" stroke="#3b82f6" stroke-width="2"/>
            <text x="85" y="40" font-size="9" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Rongga Alveolus</text>
            <text x="85" y="55" font-size="8" fill="#1e40af" text-anchor="middle">pO2 Tinggi</text>
            <path d="M 125 40 L 155 40" stroke="#ef4444" stroke-width="2" marker-end="url(#arr)"/>
            <text x="140" y="32" font-size="7.5" fill="#dc2626" font-weight="bold">O2</text>
            <path d="M 155 52 L 125 52" stroke="#3b82f6" stroke-width="2"/>
            <text x="140" y="64" font-size="7.5" fill="#2563eb" font-weight="bold">CO2</text>
            <rect x="165" y="15" width="90" height="60" rx="8" fill="#fef2f2" stroke="#ef4444" stroke-width="2"/>
            <text x="210" y="38" font-size="8.5" font-weight="bold" fill="#b91c1c" text-anchor="middle">Kapiler Darah</text>
            <text x="210" y="55" font-size="7.5" fill="#7f1d1d" text-anchor="middle">Difusi Eritrosit</text>
        </svg>';
        $story7 = 'Pernapasan manusia melibatkan inspirasi (menghirup udara) dan ekspirasi (menghembuskan udara). Di paru-paru terdapat jutaan kantung udara elastis bernama <u>alveolus</u> dengan dinding epitel pipih selapis yang sangat tipis dan lembap. Pertukaran gas antara rongga alveolus dan kapiler darah terjadi murni melalui <strong>proses difusi sederhana</strong> yang didorong oleh perbedaan tekanan parsial gas (pO2 dan pCO2). Oksigen akan diikat oleh hemoglobin membentuk <em>oksihemoglobin</em>, sedangkan CO2 diangkut terutama dalam bentuk ion bikarbonat (HCO3-) di plasma darah.';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Mekanisme Pertukaran Gas pada Sistem Respirasi', $prompt7, $svg7, $story7, '1. PILIHAN GANDA', 'Sebagian besar (sekitar 70%) gas karbon dioksida hasil metabolisme seluler diangkut oleh darah menuju paru-paru dalam bentuk...'),
            'opsi_a' => 'Gas bebas terlarut dalam cairan sitoplasma',
            'opsi_b' => 'Ion bikarbonat (HCO3-) di dalam plasma darah',
            'opsi_c' => 'Senyawa karbominohemoglobin dalam eritrosit',
            'opsi_d' => 'Kristal asam urat pada kapiler',
            'opsi_e' => 'Molekul glukosa tereduksi',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Gas bebas terlarut dalam cairan sitoplasma',
                'B' => 'Ion bikarbonat (HCO3-) di dalam plasma darah',
                'C' => 'Senyawa karbominohemoglobin dalam eritrosit',
                'D' => 'Kristal asam urat pada kapiler',
                'E' => 'Molekul glukosa tereduksi'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Mekanisme Pertukaran Gas pada Sistem Respirasi', '', '', $story7, '2. PG KOMPLEKS (CENTANG)', 'Peristiwa mekanik manakah yang terjadi pada saat berlangsungnya fase <strong>Inspirasi Pernapasan Perut</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Otot diafragma mengalami kontraksi sehingga bentuknya menjadi mendatar.',
            'opsi_b' => 'Volume rongga dada membesar dan kapasitas paru-paru mengembang.',
            'opsi_c' => 'Tekanan udara di dalam paru-paru menurun menjadi lebih rendah daripada tekanan atmosfer.',
            'opsi_d' => 'Otot antartulang rusuk dalam (internal) berkontraksi menekan tulang dada ke bawah.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Otot diafragma mengalami kontraksi sehingga bentuknya menjadi mendatar.', 'is_kunci' => true],
                    ['teks' => 'Volume rongga dada membesar dan kapasitas paru-paru mengembang.', 'is_kunci' => true],
                    ['teks' => 'Tekanan udara di dalam paru-paru menurun menjadi lebih rendah daripada tekanan atmosfer.', 'is_kunci' => true],
                    ['teks' => 'Otot antartulang rusuk dalam (internal) berkontraksi menekan tulang dada ke bawah.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Mekanisme Pertukaran Gas pada Sistem Respirasi', '', '', $story7, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kelainan sistem pernapasan berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Emfisema adalah kelainan berupa robeknya atau hilangnya elastisitas dinding alveolus paru-paru.', 'kunci' => 'Benar'],
                    ['teks' => 'Asma disebabkan oleh infeksi bakteri Mycobacterium yang membentuk bintil pada dinding alveoli.', 'kunci' => 'Salah'],
                    ['teks' => 'Gas karbon monoksida (CO) berbahaya karena memiliki afinitas ikatan dengan hemoglobin jauh lebih kuat dibanding O2.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Mekanisme Pertukaran Gas pada Sistem Respirasi', '', '', $story7, '4. MENJODOHKAN', 'Pasangkan volume udara pernapasan dengan besaran kapasitas normalnya pada manusia dewasa:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Udara Tidal', 'respon' => 'Volume udara pernapasan biasa (sekitar 500 mL)'],
                ['premis' => 'Udara Komplementer (Cadangan Inspirasi)', 'respon' => 'Udara yang dapat dihirup maksimal setelah inspirasi biasa (1.500 mL)'],
                ['premis' => 'Udara Suplementer (Cadangan Ekspirasi)', 'respon' => 'Udara yang dapat dihembuskan maksimal setelah ekspirasi biasa (1.500 mL)'],
                ['premis' => 'Udara Residu', 'respon' => 'Udara sisa yang selalu tinggal di paru-paru (sekitar 1.000 mL)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Udara Tidal', 'respon' => 'Volume udara pernapasan biasa (sekitar 500 mL)'],
                    ['premis' => 'Udara Komplementer (Cadangan Inspirasi)', 'respon' => 'Udara yang dapat dihirup maksimal setelah inspirasi biasa (1.500 mL)'],
                    ['premis' => 'Udara Suplementer (Cadangan Ekspirasi)', 'respon' => 'Udara yang dapat dihembuskan maksimal setelah ekspirasi biasa (1.500 mL)'],
                    ['premis' => 'Udara Residu', 'respon' => 'Udara sisa yang selalu tinggal di paru-paru (sekitar 1.000 mL)']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(7, '31 - 35', 'Mekanisme Pertukaran Gas pada Sistem Respirasi', '', '', $story7, '5. ISIAN SINGKAT', 'Unit fungsional terkecil berbentuk kantung udara anggur pada paru-paru yang menjadi tempat pertukaran gas O2 dan CO2 adalah...'),
            'kunci_jawaban' => 'Alveolus',
            'konten_tambahan' => ['rubrik' => 'Alveolus (atau Alveoli)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): Sistem Ekskresi: Nefron Ginjal & Urine
        // ══════════════════════════════════════════════════════════════════
        $prompt8 = "Diagram rinci struktur mikroskopis satu unit Nefron Ginjal manusia. Menampilkan Glomerulus di dalam Kapsula Bowman (filtrasi menghasilkan urine primer), Tubulus Kontortus Proksimal (reabsorpsi glukosa dan asam amino menghasilkan urine sekunder), Lengkung Henle berliku, Tubulus Kontortus Distal (augmentasi ion H+ dan K+), dan Tubulus Kolektivus penampung urine sesungguhnya. Gambar anatomi nefrologi resolusi tinggi.";
        $svg8 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="45" r="24" fill="#fee2e2" stroke="#dc2626" stroke-width="2"/>
            <text x="40" y="42" font-size="8.5" font-weight="bold" fill="#991b1b" text-anchor="middle">Glomerulus</text>
            <text x="40" y="55" font-size="7" fill="#b91c1c" text-anchor="middle">1. FILTRASI</text>
            <path d="M 65 45 L 85 45" stroke="#64748b" stroke-width="2"/>
            <rect x="90" y="20" width="80" height="50" rx="6" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="130" y="40" font-size="8.5" font-weight="bold" fill="#b45309" text-anchor="middle">Tubulus Proksimal</text>
            <text x="130" y="55" font-size="7" fill="#78350f" text-anchor="middle">2. REABSORPSI</text>
            <path d="M 175 45 L 195 45" stroke="#64748b" stroke-width="2"/>
            <rect x="200" y="20" width="65" height="50" rx="6" fill="#ecfdf5" stroke="#059669" stroke-width="1.5"/>
            <text x="232" y="40" font-size="8.5" font-weight="bold" fill="#047857" text-anchor="middle">Tubulus Distal</text>
            <text x="232" y="55" font-size="7" fill="#065f46" text-anchor="middle">3. AUGMENTASI</text>
        </svg>';
        $story8 = 'Ginjal menyaring darah dan membuang limbah metabolik nitrogen (urea, asam urat, amonia) melalui unit fungsional yang disebut <u>nefron</u>. Pembentukan urine terdiri atas tiga tahap berurutan: (1) <strong>Filtrasi</strong> darah di glomerulus menghasilkan <em>urine primer</em> (bebas sel darah dan protein besar); (2) <strong>Reabsorpsi</strong> zat-zat berguna (seperti glukosa, asam amino, air, dan ion Na+) di tubulus kontortus proksimal menghasilkan <em>urine sekunder</em>; dan (3) <strong>Augmentasi</strong> penambahan zat sisa berlebih di tubulus kontortus distal menghasilkan <em>urine sesungguhnya</em>.';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Struktur Nefron Ginjal dan Tahapan Pembentukan Urine', $prompt8, $svg8, $story8, '1. PILIHAN GANDA', 'Jika hasil uji laboratorium menunjukkan bahwa urine seseorang mengandung <u>molekul albumin/protein darah</u>, kerusakan patologis kemungkinan besar terjadi pada bagian...'),
            'opsi_a' => 'Glomerulus',
            'opsi_b' => 'Lengkung Henle',
            'opsi_c' => 'Tubulus Kontortus Distal',
            'opsi_d' => 'Tubulus Kolektivus',
            'opsi_e' => 'Ureter',
            'kunci_jawaban' => 'A',
            'konten_tambahan' => ['kunci' => 'A', 'opsi' => [
                'A' => 'Glomerulus',
                'B' => 'Lengkung Henle',
                'C' => 'Tubulus Kontortus Distal',
                'D' => 'Tubulus Kolektivus',
                'E' => 'Ureter'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Struktur Nefron Ginjal dan Tahapan Pembentukan Urine', '', '', $story8, '2. PG KOMPLEKS (CENTANG)', 'Zat-zat terlarut manakah di bawah ini yang secara normal diserap kembali seluruhnya (100%) pada tahap <strong>Reabsorpsi</strong> di tubulus kontortus proksimal? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Molekul glukosa',
            'opsi_b' => 'Molekul asam amino',
            'opsi_c' => 'Sebagian besar air dan ion natrium (Na+)',
            'opsi_d' => 'Zat sisa metabolit racun urea dan kreatinin',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Molekul glukosa', 'is_kunci' => true],
                    ['teks' => 'Molekul asam amino', 'is_kunci' => true],
                    ['teks' => 'Sebagian besar air dan ion natrium (Na+)', 'is_kunci' => true],
                    ['teks' => 'Zat sisa metabolit racun urea dan kreatinin', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Struktur Nefron Ginjal dan Tahapan Pembentukan Urine', '', '', $story8, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pengaturan hormonal ginjal berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Hormon ADH (Antidiuretik) disekresikan oleh hipofisis posterior untuk meningkatkan reabsorpsi air di tubulus ginjal saat tubuh kekurangan cairan.', 'kunci' => 'Benar'],
                    ['teks' => 'Kekurangan hormon ADH menyebabkan penyakit Diabetes Melitus yang ditandai dengan kadar gula darah tinggi.', 'kunci' => 'Salah'],
                    ['teks' => 'Gagal ginjal kronis dapat diatasi dengan terapi hemodialisis (cuci darah) atau transplantasi ginjal.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Struktur Nefron Ginjal dan Tahapan Pembentukan Urine', '', '', $story8, '4. MENJODOHKAN', 'Pasangkan kelainan ginjal dengan ciri patologisnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Albuminuria', 'respon' => 'Kerusakan glomerulus sehingga protein darah lolos ke dalam urine'],
                ['premis' => 'Glikosuria (Diabetes Melitus)', 'respon' => 'Tingginya kadar glukosa dalam urine akibat defisiensi hormon insulin'],
                ['premis' => 'Diabetes Insipidus', 'respon' => 'Produksi urine sangat banyak dan encer akibat kekurangan hormon ADH'],
                ['premis' => 'Urolitiasis (Batu Ginjal)', 'respon' => 'Endapan kristal kalsium oksalat atau asam urat di saluran kemih']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Albuminuria', 'respon' => 'Kerusakan glomerulus sehingga protein darah lolos ke dalam urine'],
                    ['premis' => 'Glikosuria (Diabetes Melitus)', 'respon' => 'Tingginya kadar glukosa dalam urine akibat defisiensi hormon insulin'],
                    ['premis' => 'Diabetes Insipidus', 'respon' => 'Produksi urine sangat banyak dan encer akibat kekurangan hormon ADH'],
                    ['premis' => 'Urolitiasis (Batu Ginjal)', 'respon' => 'Endapan kristal kalsium oksalat atau asam urat di saluran kemih']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'Struktur Nefron Ginjal dan Tahapan Pembentukan Urine', '', '', $story8, '5. ISIAN SINGKAT', 'Cairan hasil proses filtrasi darah di glomerulus yang belum mengandung sel darah namun masih mengandung glukosa dan asam amino disebut urine...'),
            'kunci_jawaban' => 'Primer',
            'konten_tambahan' => ['rubrik' => 'Primer (atau Urine Primer)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): Sistem Saraf: Neuron & Sinapsis
        // ══════════════════════════════════════════════════════════════════
        $prompt9 = "Ilustrasi anatomi neurobiologi satu sel saraf (Neuron multipolar) beresolusi tinggi. Menampilkan dendrit bercabang penangkap impuls, badan sel (soma) dengan nukleus, akson panjang (neurit) berbalut selubung mielin penghantar konduksi saltatori, celah Nodus Ranvier, dan tombol terminal akson sinapsis berisi vesikel neurotransmitter asetilkolin. Latar belakang gelap biologi nano bercahaya impuls listrik.";
        $svg9 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <polygon points="10,45 35,25 35,65" fill="#fef08a" stroke="#ca8a04" stroke-width="1.5"/>
            <text x="25" y="48" font-size="7.5" font-weight="bold" fill="#854d0e" text-anchor="middle">Dendrit</text>
            <circle cx="55" cy="45" r="16" fill="#fef3c7" stroke="#d97706" stroke-width="1.5"/>
            <text x="55" y="48" font-size="7" font-weight="bold" fill="#78350f" text-anchor="middle">Soma</text>
            <rect x="75" y="38" width="30" height="14" rx="4" fill="#fed7aa" stroke="#ea580c"/>
            <rect x="110" y="38" width="30" height="14" rx="4" fill="#fed7aa" stroke="#ea580c"/>
            <rect x="145" y="38" width="30" height="14" rx="4" fill="#fed7aa" stroke="#ea580c"/>
            <text x="125" y="48" font-size="7" fill="#c2410c" font-weight="bold" text-anchor="middle">Mielin</text>
            <circle cx="195" cy="45" r="12" fill="#eff6ff" stroke="#2563eb" stroke-width="1.5"/>
            <text x="195" y="48" font-size="7" font-weight="bold" fill="#1d4ed8" text-anchor="middle">Sinaps</text>
            <text x="235" y="48" font-size="8" fill="#047857" font-weight="bold">Impuls &rarr;</text>
        </svg>';
        $story9 = 'Sistem saraf mengoordinasikan respon tubuh terhadap stimulus lingkungan melalui transmisi impuls elektrokimia. Sel saraf (<u>neuron</u>) tersusun atas <strong>dendrit</strong> (menerima impuls dari reseptor), <strong>badan sel</strong>, dan <strong>akson / neurit</strong> (meneruskan impuls menjauhi badan sel). Akson diselubungi oleh selubung mielin yang dibentuk oleh sel Schwann dengan celah tanpa mielin bernama <u>Nodus Ranvier</u> yang mempercepat konduksi impuls secara loncatan (saltatori). Komunikasi antar-neuron pada celah sinapsis diperantarai oleh molekul kimia <u>neurotransmitter</u> (misalnya asetilkolin).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Struktur Neuron dan Transmisi Impuls pada Sinapsis', $prompt9, $svg9, $story9, '1. PILIHAN GANDA', 'Bagian akson yang tidak terselubungi oleh selubung mielin dan berfungsi untuk <u>mempercepat perambatan impuls</u> saraf secara konduksi saltatori dinamakan...'),
            'opsi_a' => 'Sel Schwann',
            'opsi_b' => 'Nodus Ranvier',
            'opsi_c' => 'Badan Nissl',
            'opsi_d' => 'Neurofibril',
            'opsi_e' => 'Dendrit Terminal',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'Sel Schwann',
                'B' => 'Nodus Ranvier',
                'C' => 'Badan Nissl',
                'D' => 'Neurofibril',
                'E' => 'Dendrit Terminal'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Struktur Neuron dan Transmisi Impuls pada Sinapsis', '', '', $story9, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai perbedaan alur mekanisme gerak sadar dan gerak refleks? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Gerak sadar melibatkan pusat pengolahan informasi di korteks otak besar (serebrum).',
            'opsi_b' => 'Gerak refleks berlangsung spontan melalui lengkung refleks yang berpusat di sumsum tulang belakang (medula spinalis).',
            'opsi_c' => 'Gerak sadar tidak memerlukan adanya reseptor sensori penerima rangsang.',
            'opsi_d' => 'Gerak refleks bertujuan melindungi tubuh dari bahaya fisik mendadak secara cepat tanpa menunggu analisis sadar otak.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Gerak sadar melibatkan pusat pengolahan informasi di korteks otak besar (serebrum).', 'is_kunci' => true],
                    ['teks' => 'Gerak refleks berlangsung spontan melalui lengkung refleks yang berpusat di sumsum tulang belakang (medula spinalis).', 'is_kunci' => true],
                    ['teks' => 'Gerak sadar tidak memerlukan adanya reseptor sensori penerima rangsang.', 'is_kunci' => false],
                    ['teks' => 'Gerak refleks bertujuan melindungi tubuh dari bahaya fisik mendadak secara cepat tanpa menunggu analisis sadar otak.', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Struktur Neuron dan Transmisi Impuls pada Sinapsis', '', '', $story9, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari perambatan impuls elektrokimia saraf berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Saat neuron dalam keadaan istirahat (polarisasi), muatan di luar membran bermuatan positif sedangkan bagian dalam bermuatan negatif.', 'kunci' => 'Benar'],
                    ['teks' => 'Depolarisasi membran terjadi akibat keluarnya ion kalium (K+) dalam jumlah masif dari dalam sel saraf.', 'kunci' => 'Salah'],
                    ['teks' => 'Penyeberangan impuls melintasi celah sinapsis dibantu oleh neurotransmitter yang dilepaskan secara eksositosis.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Struktur Neuron dan Transmisi Impuls pada Sinapsis', '', '', $story9, '4. MENJODOHKAN', 'Pasangkan divisi sistem saraf otonom dengan efek fisiologis yang ditimbulkannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Saraf Simpatik', 'respon' => 'Mempercepat denyut jantung dan memperlebar pupil (Fight or Flight)'],
                ['premis' => 'Saraf Parasimpatik', 'respon' => 'Memperlambat denyut jantung dan memicu motilitas cerna (Rest and Digest)'],
                ['premis' => 'Serebelum (Otak Kecil)', 'respon' => 'Pusat koordinasi keseimbangan posisi tubuh dan gerak motorik halus'],
                ['premis' => 'Medula Oblongata', 'respon' => 'Pusat pengatur refleks otonom denyut jantung, pernapasan, dan batuk']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Saraf Simpatik', 'respon' => 'Mempercepat denyut jantung dan memperlebar pupil (Fight or Flight)'],
                    ['premis' => 'Saraf Parasimpatik', 'respon' => 'Memperlambat denyut jantung dan memicu motilitas cerna (Rest and Digest)'],
                    ['premis' => 'Serebelum (Otak Kecil)', 'respon' => 'Pusat koordinasi keseimbangan posisi tubuh dan gerak motorik halus'],
                    ['premis' => 'Medula Oblongata', 'respon' => 'Pusat pengatur refleks otonom denyut jantung, pernapasan, dan batuk']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'Struktur Neuron dan Transmisi Impuls pada Sinapsis', '', '', $story9, '5. ISIAN SINGKAT', 'Titik temu fungsional penghubung antara ujung akson suatu neuron dengan dendrit atau badan sel neuron lainnya dinamakan...'),
            'kunci_jawaban' => 'Sinapsis',
            'konten_tambahan' => ['rubrik' => 'Sinapsis (atau Sinaps)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): Sistem Imun & Mekanisme Kerja Antibodi
        // ══════════════════════════════════════════════════════════════════
        $prompt10 = "Diagram imunologi struktur molekul Antibodi berbentuk huruf Y (Imunoglobulin monomer). Menampilkan rantai berat (heavy chain), rantai ringan (light chain), daerah konstan (Fc), dan daerah variabel (Fab antigen-binding site) yang mengikat epitop spesifik antigen patogen virus secara pas. Sisi kanan menampilkan respon sel memori pada grafik vaksinasi booster. Gaya infografis biomedis beresolusi 4K.";
        $svg10 = '<svg width="270" height="90" viewBox="0 0 270 90" xmlns="http://www.w3.org/2000/svg">
            <path d="M 90 20 L 115 45 L 115 80" stroke="#4f46e5" stroke-width="4" fill="none"/>
            <path d="M 140 20 L 115 45" stroke="#4f46e5" stroke-width="4" fill="none"/>
            <path d="M 80 25 L 100 45" stroke="#06b6d4" stroke-width="3" fill="none"/>
            <path d="M 150 25 L 130 45" stroke="#06b6d4" stroke-width="3" fill="none"/>
            <circle cx="85" cy="18" r="5" fill="#ef4444"/> <circle cx="145" cy="18" r="5" fill="#ef4444"/>
            <text x="85" y="12" font-size="7" fill="#dc2626" font-weight="bold">Antigen</text>
            <text x="145" y="12" font-size="7" fill="#dc2626" font-weight="bold">Antigen</text>
            <text x="175" y="55" font-size="9" font-weight="bold" fill="#312e81">Antibodi (Ig)</text>
            <text x="175" y="70" font-size="7.5" fill="#4338ca">Fab Spesifik</text>
        </svg>';
        $story10 = 'Sistem kekebalan tubuh melindungi organisme dari serangan antigen patogen (bakteri, virus, toksin). Sistem imun terbagi menjadi <u>imunitas bawaan non-spesifik</u> (kulit, air mata, fagosit makrofag) dan <u>imunitas adaptif spesifik</u> yang dipimpin oleh <strong>Limfosit B</strong> (imunitas humoral penghasil antibodi) dan <strong>Limfosit T</strong> (imunitas seluler perantara sel). Melalui vaksinasi, antigen yang dilemahkan/dimatikan disuntikkan ke dalam tubuh untuk memicu terbentuknya <u>sel memori</u> tanpa menyebabkan penyakit parah.';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Fisiologi Sistem Imunitas dan Mekanisme Antibodi', $prompt10, $svg10, $story10, '1. PILIHAN GANDA', 'Jenis sel limfosit yang berdiferensiasi menjadi sel plasma penghasil <u>molekul antibodi terlarut (imunoglobulin)</u> adalah...'),
            'opsi_a' => 'Limfosit T Sitotoksik (T-Killer)',
            'opsi_b' => 'Limfosit T Helper (CD4+)',
            'opsi_c' => 'Limfosit B (Sel B)',
            'opsi_d' => 'Sel Natural Killer (NK Cell)',
            'opsi_e' => 'Monosit Makrofag',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'Limfosit T Sitotoksik (T-Killer)',
                'B' => 'Limfosit T Helper (CD4+)',
                'C' => 'Limfosit B (Sel B)',
                'D' => 'Sel Natural Killer (NK Cell)',
                'E' => 'Monosit Makrofag'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Fisiologi Sistem Imunitas dan Mekanisme Antibodi', '', '', $story10, '2. PG KOMPLEKS (CENTANG)', 'Mekanisme biologis manakah yang digunakan oleh <strong>antibodi</strong> dalam menonaktifkan antigen patogen? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Netralisasi, memblokir sisi aktif toksin atau situs penempelan virus pada sel inang.',
            'opsi_b' => 'Aglutinasi, menggumpalkan partikel patogen agar mudah difagositosis oleh makrofag.',
            'opsi_c' => 'Presipitasi, mengendapkan molekul antigen yang terlarut dalam cairan tubuh.',
            'opsi_d' => 'Fagositosis intraseluler langsung menggunakan vakuola kontraktil antibodi.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Netralisasi, memblokir sisi aktif toksin atau situs penempelan virus pada sel inang.', 'is_kunci' => true],
                    ['teks' => 'Aglutinasi, menggumpalkan partikel patogen agar mudah difagositosis oleh makrofag.', 'is_kunci' => true],
                    ['teks' => 'Presipitasi, mengendapkan molekul antigen yang terlarut dalam cairan tubuh.', 'is_kunci' => true],
                    ['teks' => 'Fagositosis intraseluler langsung menggunakan vakuola kontraktil antibodi.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Fisiologi Sistem Imunitas dan Mekanisme Antibodi', '', '', $story10, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari prinsip imunisasi dan kekebalan tubuh berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pemberian ASI kolostrum ibu kepada bayinya merupakan contoh perolehan kekebalan pasif alami (antibodi maternal IgA).', 'kunci' => 'Benar'],
                    ['teks' => 'Vaksinasi menghasilkan kekebalan pasif buatan yang bertahan singkat beberapa hari saja.', 'kunci' => 'Salah'],
                    ['teks' => 'Respon imun sekunder berlangsung jauh lebih cepat dan menghasilkan titer antibodi jauh lebih tinggi karena adanya sel memori.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Fisiologi Sistem Imunitas dan Mekanisme Antibodi', '', '', $story10, '4. MENJODOHKAN', 'Pasangkan kelas imunoglobulin (antibodi) dengan karakteristik dominannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'IgG', 'respon' => 'Antibodi paling melimpah di darah dan mampu menembus plasenta ibu ke janin'],
                ['premis' => 'IgA', 'respon' => 'Ditemukan melimpah dalam sekresi cairan tubuh (ASI, saliva, air mata)'],
                ['premis' => 'IgM', 'respon' => 'Berbentuk pentamer dan pertama kali disekresikan pada respon imun primer'],
                ['premis' => 'IgE', 'respon' => 'Terlibat langsung dalam reaksi alergi (hipersensitivitas) dan melawan parasit cacing']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'IgG', 'respon' => 'Antibodi paling melimpah di darah dan mampu menembus plasenta ibu ke janin'],
                    ['premis' => 'IgA', 'respon' => 'Ditemukan melimpah dalam sekresi cairan tubuh (ASI, saliva, air mata)'],
                    ['premis' => 'IgM', 'respon' => 'Berbentuk pentamer dan pertama kali disekresikan pada respon imun primer'],
                    ['premis' => 'IgE', 'respon' => 'Terlibat langsung dalam reaksi alergi (hipersensitivitas) dan melawan parasit cacing']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'Fisiologi Sistem Imunitas dan Mekanisme Antibodi', '', '', $story10, '5. ISIAN SINGKAT', 'Bagian molekul antigen asing spesifik yang dikenali dan diikat secara pas oleh situs pengikatan antigen pada antibodi dinamakan...'),
            'kunci_jawaban' => 'Epitop',
            'konten_tambahan' => ['rubrik' => 'Epitop (atau Determinan Antigen)']
        ];

        return $list;
    }
}
