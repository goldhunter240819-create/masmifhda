<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class ArabKelasX {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): التَّعَارُفُ وَالتَّحِيَّاتُ (Perkenalan & Sapaan)
        // ══════════════════════════════════════════════════════════════════
        $ar1 = 'أَحْمَدُ: السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَاتُهُ.<br>
خَالِدٌ: وَعَلَيْكُمُ السَّلَامُ وَرَحْمَةُ اللهِ وَبَرَكَاتُهُ.<br>
أَحْمَدُ: أَهْلًا وَسَهْلًا يَا أَخِي، اسْمِي أَحْمَدُ، أَنَا طَالِبٌ جَدِيْدٌ فِي المَدْرَسَةِ الثَّانَوِيَّةِ الإِسْلَامِيَّةِ "مِفْتَاحُ الهُدَى". وَمَا اسْمُكَ؟<br>
خَالِدٌ: أَهْلًا بِكَ يَا أَحْمَدُ، اسْمِي خَالِدٌ، وَأَنَا طَالِبٌ فِي الفَصْلِ العَاشِرِ أَيْضًا.<br>
أَحْمَدُ: مِنْ أَيْنَ أَنْتَ يَا خَالِدُ؟<br>
خَالِدٌ: أَنَا قَادِمٌ مِنْ مَدِيْنَةِ بَانْدُونج، وَأَسْكُنُ الآنَ فِي السَّكَنِ المَدْرَسِيِّ. صَبَاحُ الخَيْرِ!<br>
أَحْمَدُ: صَبَاحُ النُّوْرِ وَالسُّرُوْرِ، فَرِحْتُ بِلِقَائِكَ كَثِيْرًا.';
        $indo1 = 'Teks percakapan ta\'aruf antara Ahmad dan Khalid yang baru bertemu di lingkungan MAS Miftahul Huda. Mereka saling menanyakan nama, tingkatan kelas (Kelas X), serta tempat tinggal di asrama madrasah.';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّعَارُفُ وَالتَّحِيَّاتُ', 'Ungkapan Sapaan & Perkenalan Diri', $ar1, $indo1, '1. PILIHAN GANDA', 'بِنَاءً عَلَى الحِوَارِ السَّابِقِ، أَيْنَ يَدْرُسُ أَحْمَدُ وَخَالِدٌ؟ (Berdasarkan hiwar di atas, di manakah Ahmad dan Khalid bersekolah?)'),
            'opsi_a' => 'فِي الجَامِعَةِ الإِسْلَامِيَّةِ الحُكُوْمِيَّةِ',
            'opsi_b' => 'فِي المَدْرَسَةِ الثَّانَوِيَّةِ الإِسْلَامِيَّةِ "مِفْتَاحُ الهُدَى"',
            'opsi_c' => 'فِي المَدْرَسَةِ الِابْتِدَائِيَّةِ المَرْكَزِيَّةِ',
            'opsi_d' => 'فِي مَعْهَدِ البُحُوْثِ العِلْمِيَّةِ',
            'opsi_e' => 'فِي المَسْجِدِ الكَبِيْرِ بِبَانْدُونج',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'فِي الجَامِعَةِ الإِسْلَامِيَّةِ الحُكُوْمِيَّةِ',
                'B' => 'فِي المَدْرَسَةِ الثَّانَوِيَّةِ الإِسْلَامِيَّةِ "مِفْتَاحُ الهُدَى"',
                'C' => 'فِي المَدْرَسَةِ الِابْتِدَائِيَّةِ المَرْكَزِيَّةِ',
                'D' => 'فِي مَعْهَدِ البُحُوْثِ العِلْمِيَّةِ',
                'E' => 'فِي المَسْجِدِ الكَبِيْرِ بِبَانْدُونج'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّعَارُفُ وَالتَّحِيَّاتُ', 'Ungkapan Sapaan & Perkenalan Diri', $ar1, $indo1, '2. PG KOMPLEKS (CENTANG)', 'Pilihlah pernyataan yang <strong>BENAR</strong> mengenai informasi teks maupun kaidah dhomir (kata ganti) di atas! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'أَحْمَدُ وَخَالِدٌ كِلَاهُمَا طَالِبَانِ فِي الفَصْلِ العَاشِرِ (Keduanya adalah siswa kelas sepuluh).',
            'opsi_b' => 'خَالِدٌ يَسْكُنُ فِي السَّكَنِ المَدْرَسِيِّ (Khalid bertempat tinggal di asrama madrasah).',
            'opsi_c' => 'الضَّمِيْرُ "أَنَا" فِي الحِوَارِ يَدُلُّ عَلَى المُتَكَلِّمِ المُفْرَدِ (Dhomir "أَنَا" bermakna pembicara tunggal/orang pertama).',
            'opsi_d' => 'خَالِدٌ قَادِمٌ مِنْ مَدِيْنَةِ سُوْرَابَايَا (Khalid berasal dari kota Surabaya).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'أَحْمَدُ وَخَالِدٌ كِلَاهُمَا طَالِبَانِ فِي الفَصْلِ العَاشِرِ (Keduanya adalah siswa kelas sepuluh).', 'is_kunci' => true],
                    ['teks' => 'خَالِدٌ يَسْكُنُ فِي السَّكَنِ المَدْرَسِيِّ (Khalid bertempat tinggal di asrama madrasah).', 'is_kunci' => true],
                    ['teks' => 'الضَّمِيْرُ "أَنَا" فِي الحِوَارِ يَدُلُّ عَلَى المُتَكَلِّمِ المُفْرَدِ (Dhomir "أَنَا" bermakna pembicara tunggal/orang pertama).', 'is_kunci' => true],
                    ['teks' => 'خَالِدٌ قَادِمٌ مِنْ مَدِيْنَةِ سُوْرَابَايَا (Khalid berasal dari kota Surabaya).', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّعَارُفُ وَالتَّحِيَّاتُ', 'Ungkapan Sapaan & Perkenalan Diri', $ar1, $indo1, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pernyataan ungkapan sapaan bahasa Arab (التَّحِيَّاتُ اليَوْمِيَّةُ) berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Jawaban yang tepat untuk sapaan "صَبَاحُ الخَيْرِ" adalah "صَبَاحُ النُّوْرِ".', 'kunci' => 'Benar'],
                    ['teks' => 'Jawaban resmi untuk ucapan selamat datang "أَهْلًا وَسَهْلًا" adalah "مَعَ السَّلَامَةِ".', 'kunci' => 'Salah'],
                    ['teks' => 'Ungkapan "فَرِحْتُ بِلِقَائِكَ" digunakan untuk mengungkapkan rasa senang atas perjumpaan dengan sahabat baru.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّعَارُفُ وَالتَّحِيَّاتُ', 'Ungkapan Sapaan & Perkenalan Diri', $ar1, $indo1, '4. MENJODOHKAN', 'Pasangkan ungkapan sapaan bahasa Arab di sebelah kiri dengan jawaban balasannya yang tepat:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'السَّلَامُ عَلَيْكُمْ', 'respon' => 'وَعَلَيْكُمُ السَّلَامُ'],
                ['premis' => 'أَهْلًا وَسَهْلًا', 'respon' => 'أَهْلًا بِكَ'],
                ['premis' => 'مَسَاءُ الخَيْرِ', 'respon' => 'مَسَاءُ النُّوْرِ'],
                ['premis' => 'شُكْرًا جَزِيْلًا', 'respon' => 'عَفْوًا']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'السَّلَامُ عَلَيْكُمْ', 'respon' => 'وَعَلَيْكُمُ السَّلَامُ'],
                    ['premis' => 'أَهْلًا وَسَهْلًا', 'respon' => 'أَهْلًا بِكَ'],
                    ['premis' => 'مَسَاءُ الخَيْرِ', 'respon' => 'مَسَاءُ النُّوْرِ'],
                    ['premis' => 'شُكْرًا جَزِيْلًا', 'respon' => 'عَفْوًا']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّعَارُفُ وَالتَّحِيَّاتُ', 'Ungkapan Sapaan & Perkenalan Diri', $ar1, $indo1, '5. ISIAN SINGKAT', 'Kata ganti terpisah (Dhomir Munfashil) untuk orang pertama jamak yang bermakna "Kami / Kita" dalam bahasa Arab adalah... (Tuliskan teks Arabnya berharakat atau huruf latinnya)'),
            'kunci_jawaban' => 'نَحْنُ',
            'konten_tambahan' => ['rubrik' => 'نَحْنُ (atau Nahnu)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): الأُسْرَةُ وَالبَيْتُ (Keluarga & Rumah)
        // ══════════════════════════════════════════════════════════════════
        $ar2 = 'هَذِهِ أُسْرَةُ فَرْحَانَ. هُوَ يَعِيْشُ مَعَ أُسْرَتِهِ فِي بَيْتٍ جَمِيْلٍ وَنَظِيْفٍ.<br>
أَبُوْهُ اسْمُهُ السَّيِّدُ لُقْمَانُ، هُوَ مُدَرِّسٌ مَاهِرٌ فِي المَدْرَسَةِ، وَأُمُّهُ اسْمُهَا السَّيِّدَةُ سَلْمَى، هِيَ طَبِيْبَةٌ تَعْمَلُ فِي المُسْتَشْفَى.<br>
لِفَرْحَانَ أَخٌ كَبِيْرٌ اسْمُهُ حَسَنٌ، هُوَ طَالِبٌ فِي الجَامِعَةِ، وَأُخْتٌ صَغِيْرَةٌ اسْمُهَا عَائِشَةُ تَدْرُسُ فِي المَدْرَسَةِ الِابْتِدَائِيَّةِ.<br>
فِي بَيْتِ فَرْحَانَ غُرَفٌ كَثِيْرَةٌ: غُرْفَةُ الجُلُوْسِ، وَغُرْفَةُ النَّوْمِ، وَغُرْفَةُ المَكْتَبِ، وَالمَطْبَخُ، وَالحَمَّامُ. كُلُّ مَسَاءٍ، تَجْتَمِعُ الأُسْرَةُ فِي غُرْفَةِ الجُلُوْسِ يَتَحَادَثُوْنَ بِمَوَدَّةٍ وَسُرُوْرٍ.';
        $indo2 = 'Teks deskripsi mengenai keluarga Farhan yang harmonis, profesi kedua orang tuanya (Ayah seorang guru dan Ibu seorang dokter), saudara-saudaranya, serta ruangan-ruangan di rumahnya.';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الأُسْرَةُ وَالبَيْتُ', 'Kehidupan Keluarga & Profesi', $ar2, $indo2, '1. PILIHAN GANDA', 'مَا مِهْنَةُ وَالِدَةِ فَرْحَانَ (السَّيِّدَةِ سَلْمَى)؟ (Apakah profesi dari ibunda Farhan?)'),
            'opsi_a' => 'مُدَرِّسَةٌ فِي المَدْرَسَةِ',
            'opsi_b' => 'طَبِيْبَةٌ فِي المُسْتَشْفَى',
            'opsi_c' => 'مُوَظَّفَةٌ فِي البَنْكِ',
            'opsi_d' => 'تَاجِرَةٌ فِي السُّوْقِ',
            'opsi_e' => 'مُهَنْدِسَةٌ فِي المَصْنَعِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'مُدَرِّسَةٌ فِي المَدْرَسَةِ',
                'B' => 'طَبِيْبَةٌ فِي المُسْتَشْفَى',
                'C' => 'مُوَظَّفَةٌ فِي البَنْكِ',
                'D' => 'تَاجِرَةٌ فِي السُّوْقِ',
                'E' => 'مُهَنْدِسَةٌ فِي المَصْنَعِ'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الأُسْرَةُ وَالبَيْتُ', 'Kehidupan Keluarga & Profesi', $ar2, $indo2, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>SESUAI</strong> dengan teks bacaan di atas? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'السَّيِّدُ لُقْمَانُ يُعَلِّمُ التَّلَامِيْذَ فِي المَدْرَسَةِ (Pak Luqman mengajar para siswa di madrasah).',
            'opsi_b' => 'حَسَنٌ هُوَ الأَخُ الأَكْبَرُ لِفَرْحَانَ وَهُوَ طَالِبٌ جَامِعِيٌّ (Hasan adalah kakak laki-laki Farhan yang seorang mahasiswa).',
            'opsi_c' => 'تَجْتَمِعُ الأُسْرَةُ كُلَّ مَسَاءٍ فِي المَطْبَخِ (Keluarga berkumpul setiap malam di dapur).',
            'opsi_d' => 'فِي بَيْتِ فَرْحَانَ غُرْفَةُ جُلُوْسٍ وَغُرْفَةُ نَوْمٍ وَمَطْبَخٌ (Di rumah Farhan terdapat ruang tamu, kamar tidur, dan dapur).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'السَّيِّدُ لُقْمَانُ يُعَلِّمُ التَّلَامِيْذَ فِي المَدْرَسَةِ (Pak Luqman mengajar para siswa di madrasah).', 'is_kunci' => true],
                    ['teks' => 'حَسَنٌ هُوَ الأَخُ الأَكْبَرُ لِفَرْحَانَ وَهُوَ طَالِبٌ جَامِعِيٌّ (Hasan adalah kakak laki-laki Farhan yang seorang mahasiswa).', 'is_kunci' => true],
                    ['teks' => 'تَجْتَمِعُ الأُسْرَةُ كُلَّ مَسَاءٍ فِي المَطْبَخِ (Keluarga berkumpul setiap malam di dapur).', 'is_kunci' => false],
                    ['teks' => 'فِي بَيْتِ فَرْحَانَ غُرْفَةُ جُلُوْسٍ وَغُرْفَةُ نَوْمٍ وَمَطْبَخٌ (Di rumah Farhan terdapat ruang tamu, kamar tidur, dan dapur).', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الأُسْرَةُ وَالبَيْتُ', 'Kehidupan Keluarga & Profesi', $ar2, $indo2, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah Isim Isyarah (Kata Tunjuk) berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kata tunjuk "هَذَا" digunakan untuk menunjuk kata benda berjenis Mudzakkar jarak dekat.', 'kunci' => 'Benar'],
                    ['teks' => 'Kalimat "هَذَا مَدْرَسَةٌ كَبِيْرَةٌ" merupakan kalimat yang benar secara kaidah tata bahasa Arab.', 'kunci' => 'Salah'],
                    ['teks' => 'Kata tunjuk "تِلْكَ" digunakan untuk menunjuk Isim Muannats berjarak jauh.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الأُسْرَةُ وَالبَيْتُ', 'Kehidupan Keluarga & Profesi', $ar2, $indo2, '4. MENJODOHKAN', 'Pasangkan kosa kata ruangan rumah dalam bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'غُرْفَةُ الجُلُوْسِ', 'respon' => 'Ruang Tamu'],
                ['premis' => 'غُرْفَةُ النَّوْمِ', 'respon' => 'Kamar Tidur'],
                ['premis' => 'المَطْبَخُ', 'respon' => 'Dapur'],
                ['premis' => 'الحَمَّامُ', 'respon' => 'Kamar Mandi']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'غُرْفَةُ الجُلُوْسِ', 'respon' => 'Ruang Tamu'],
                    ['premis' => 'غُرْفَةُ النَّوْمِ', 'respon' => 'Kamar Tidur'],
                    ['premis' => 'المَطْبَخُ', 'respon' => 'Dapur'],
                    ['premis' => 'الحَمَّامُ', 'respon' => 'Kamar Mandi']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الأُسْرَةُ وَالبَيْتُ', 'Kehidupan Keluarga & Profesi', $ar2, $indo2, '5. ISIAN SINGKAT', 'Lengkapilah kalimat berikut dengan kata ganti bersambung (Dhomir Muttashil) untuk kepemilikan dia perempuan: "فَاطِمَةُ فِي الغُرْفَةِ، وَكِتَابُـ... عَلَى المَكْتَبِ" (Fatimah di kamar, dan bukunya di atas meja). Huruf yang tepat adalah...'),
            'kunci_jawaban' => 'ـهَا',
            'konten_tambahan' => ['rubrik' => 'ـهَا (atau haa / ها)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): المَدْرَسَةُ وَمَرَافِقُهَا (Fasilitas Madrasah)
        // ══════════════════════════════════════════════════════════════════
        $ar3 = 'مَدْرَسَتِي هِيَ المَدْرَسَةُ الثَّانَوِيَّةُ الإِسْلَامِيَّةُ "مِفْتَاحُ الهُدَى". هِيَ مَدْرَسَةٌ كَبِيْرَةٌ، وَاسِعَةٌ، وَمُنَظَّمَةٌ.<br>
فِي المَدْرَسَةِ مَرَافِقُ عَامَّةٌ كَثِيْرَةٌ، مِنْهَا: الفُصُوْلُ الدِّرَاسِيَّةُ، وَالمَكْتَبَةُ الشَّامِلَةُ، وَمَعْمَلُ اللُّغَاتِ، وَمَعْمَلُ العُلُوْمِ الطَّبِيْعِيَّةِ (البَيُولُوجِيَا وَالكِيْمِيَاءِ)، وَالمَسْجِدُ الجَامِعُ، وَالمَيْدَانُ الرِّيَاضِيُّ.<br>
يَقَعُ المَسْجِدُ أَمَامَ المَيْدَانِ، وَالمَكْتَبَةُ بَيْنَ الإِدَارَةِ وَمَعْمَلِ الحَاسُوْبِ. يَجْلِسُ التَّلَامِيْذُ عَلَى الكَرَاسِيِّ أَمَامَ المَكَاتِبِ لِيَسْتَمِعُوْا إِلَى شَرْحِ الأُسْتَاذِ بِاهْتِمَامٍ بَالِغٍ.';
        $indo3 = 'Teks deskripsi mengenai sarana dan prasarana di lingkungan MAS Miftahul Huda, letak geografis antar-ruangan menggunakan kata depan (Huruf Jar) dan keterangan tempat (Zharaf Makan).';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'المَدْرَسَةُ وَمَرَافِقُهَا', 'Sarana Prasarana Madrasah & Huruf Jar', $ar3, $indo3, '1. PILIHAN GANDA', 'أَيْنَ يَقَعُ المَسْجِدُ فِي مَدْرَسَةِ مِفْتَاحِ الهُدَى؟ (Di manakah letak masjid di MAS Miftahul Huda?)'),
            'opsi_a' => 'وَرَاءَ الفُصُوْلِ',
            'opsi_b' => 'أَمَامَ المَيْدَانِ الرِّيَاضِيِّ',
            'opsi_c' => 'تَحْتَ المَكْتَبَةِ',
            'opsi_d' => 'فَوْقَ مَعْمَلِ اللُّغَاتِ',
            'opsi_e' => 'بِجَانِبِ المَقْصَفِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'وَرَاءَ الفُصُوْلِ',
                'B' => 'أَمَامَ المَيْدَانِ الرِّيَاضِيِّ',
                'C' => 'تَحْتَ المَكْتَبَةِ',
                'D' => 'فَوْقَ مَعْمَلِ اللُّغَاتِ',
                'E' => 'بِجَانِبِ المَقْصَفِ'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'المَدْرَسَةُ وَمَرَافِقُهَا', 'Sarana Prasarana Madrasah & Huruf Jar', $ar3, $indo3, '2. PG KOMPLEKS (CENTANG)', 'Huruf manakah di bawah ini yang tergolong <strong>Huruf Jar</strong> (حُرُوْفُ الجَرِّ) yang menyebabkan kata setelahnya berharakat majrur (kasrah)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'فِي (Di dalam)',
            'opsi_b' => 'عَلَى (Di atas menempel)',
            'opsi_c' => 'إِلَى (Ke / Menuju)',
            'opsi_d' => 'ثُمَّ (Kemudian)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'فِي (Di dalam)', 'is_kunci' => true],
                    ['teks' => 'عَلَى (Di atas menempel)', 'is_kunci' => true],
                    ['teks' => 'إِلَى (Ke / Menuju)', 'is_kunci' => true],
                    ['teks' => 'ثُمَّ (Kemudian)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 13: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'المَدْرَسَةُ وَمَرَافِقُهَا', 'Sarana Prasarana Madrasah & Huruf Jar', $ar3, $indo3, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari kaidah zharaf makan (keterangan posisi) berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kata "أَمَامَ" bermakna "di depan", sedangkan "وَرَاءَ" bermakna "di belakang".', 'kunci' => 'Benar'],
                    ['teks' => 'Kata "تَحْتَ" bermakna "di atas", semakna dengan kata "فَوْقَ".', 'kunci' => 'Salah'],
                    ['teks' => 'Kata "بَيْنَ" digunakan untuk menerangkan letak sesuatu di antara dua benda.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'المَدْرَسَةُ وَمَرَافِقُهَا', 'Sarana Prasarana Madrasah & Huruf Jar', $ar3, $indo3, '4. MENJODOHKAN', 'Pasangkan nama fasilitas madrasah dengan terjemahan bahasa Indonesianya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'مَعْمَلُ اللُّغَاتِ', 'respon' => 'Laboratorium Bahasa'],
                ['premis' => 'المَكْتَبَةُ الشَّامِلَةُ', 'respon' => 'Perpustakaan Lengkap'],
                ['premis' => 'المَيْدَانُ الرِّيَاضِيُّ', 'respon' => 'Lapangan Olahraga'],
                ['premis' => 'الإِدَارَةُ', 'respon' => 'Kantor Tata Usaha / Administrasi']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'مَعْمَلُ اللُّغَاتِ', 'respon' => 'Laboratorium Bahasa'],
                    ['premis' => 'المَكْتَبَةُ الشَّامِلَةُ', 'respon' => 'Perpustakaan Lengkap'],
                    ['premis' => 'المَيْدَانُ الرِّيَاضِيُّ', 'respon' => 'Lapangan Olahraga'],
                    ['premis' => 'الإِدَارَةُ', 'respon' => 'Kantor Tata Usaha / Administrasi']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'المَدْرَسَةُ وَمَرَافِقُهَا', 'Sarana Prasarana Madrasah & Huruf Jar', $ar3, $indo3, '5. ISIAN SINGKAT', 'Lengkapilah kalimat berikut dengan huruf jar yang tepat: "يَذْهَبُ الطَّالِبُ ... المَدْرَسَةِ صَبَاحًا بَاكِرًا" (Siswa itu pergi ... madrasah pagi-pagi sekali).'),
            'kunci_jawaban' => 'إِلَى',
            'konten_tambahan' => ['rubrik' => 'إِلَى (atau Ila)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): الحَيَاةُ اليَوْمِيَّةُ (Keseharian Siswa)
        // ══════════════════════════════════════════════════════════════════
        $ar4 = 'يَسْتَيْقِظُ عُمَرُ مِنَ النَّوْمِ فِي السَّاعَةِ الرَّابِعَةِ صَبَاحًا. يَتَوَضَّأُ ثُمَّ يُصَلِّي صَلَاةَ الصُّبْحِ جَمَاعَةً فِي مَسْجِدِ المَعْهَدِ.<br>
بَعْدَ الصَّلَاةِ، يَقْرَأُ القُرْآنَ الكَرِيْمَ وَيَسْتَذْكِرُ الدُّرُوْسَ. فِي السَّاعَةِ السَّادِسَةِ، يَسْتَحِمُّ عُمَرُ وَيَرْتَدِي الزِّيَّ المَدْرَسِيَّ، ثُمَّ يَتَنَاوَلُ طَعَامَ الفُطُوْرِ مَعَ أَصْدِقَائِهِ.<br>
يَبْدَأُ الدَّرْسُ الأَوَّلُ فِي السَّاعَةِ السَّابِعَةِ صَبَاحًا، وَيَنْتَهِي الدَّوَامُ فِي السَّاعَةِ الثَّانِيَةِ وَالنِّصْفِ بَعْدَ الظُّهْرِ. يُحِبُّ عُمَرُ أَنْ يَقْضِيَ وَقْتَ فَرَاغِهِ بِمُمَارَسَةِ الرِّيَاضَةِ أَوْ قِرَاءَةِ الكُتُبِ النَّافِعَةِ.';
        $indo4 = 'Teks narasi mengenai rutinitas harian Umar dari bangun pagi pukul 04.00, sholat subuh berjamaah, sarapan, belajar di kelas hingga istirahat sore.';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'الحَيَاةُ اليَوْمِيَّةُ', 'Aktivitas Harian & Jam (السَّاعَةُ)', $ar4, $indo4, '1. PILIHAN GANDA', 'فِي أَيِّ سَاعَةٍ يَبْدَأُ الدَّرْسُ الأَوَّلُ فِي مَدْرَسَةِ عُمَرَ؟ (Pada jam berapakah pelajaran pertama dimulai?)'),
            'opsi_a' => 'فِي السَّاعَةِ السَّادِسَةِ صَبَاحًا',
            'opsi_b' => 'فِي السَّاعَةِ السَّابِعَةِ صَبَاحًا',
            'opsi_c' => 'فِي السَّاعَةِ الثَّامِنَةِ صَبَاحًا',
            'opsi_d' => 'فِي السَّاعَةِ الرَّابِعَةِ صَبَاحًا',
            'opsi_e' => 'فِي السَّاعَةِ السَّادِسَةِ وَالنِّصْفِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'فِي السَّاعَةِ السَّادِسَةِ صَبَاحًا',
                'B' => 'فِي السَّاعَةِ السَّابِعَةِ صَبَاحًا',
                'C' => 'فِي السَّاعَةِ الثَّامِنَةِ صَبَاحًا',
                'D' => 'فِي السَّاعَةِ الرَّابِعَةِ صَبَاحًا',
                'E' => 'فِي السَّاعَةِ السَّادِسَةِ وَالنِّصْفِ'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'الحَيَاةُ اليَوْمِيَّةُ', 'Aktivitas Harian & Jam (السَّاعَةُ)', $ar4, $indo4, '2. PG KOMPLEKS (CENTANG)', 'Aktivitas apa sajakah yang dilakukan Umar <strong>sebelum berangkat belajar</strong> ke madrasah? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'يُصَلِّي صَلَاةَ الصُّبْحِ جَمَاعَةً (Sholat subuh berjamaah).',
            'opsi_b' => 'يَقْرَأُ القُرْآنَ الكَرِيْمَ وَيَسْتَذْكِرُ الدُّرُوْسَ (Membaca Al-Qur\'an dan mengulang pelajaran).',
            'opsi_c' => 'يَتَنَاوَلُ طَعَامَ الفُطُوْرِ مَعَ أَصْدِقَائِهِ (Makan sarapan pagi bersama teman-temannya).',
            'opsi_d' => 'يُسَافِرُ إِلَى مَدِيْنَةٍ أُخْرَى (Bepergian ke kota lain).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'يُصَلِّي صَلَاةَ الصُّبْحِ جَمَاعَةً (Sholat subuh berjamaah).', 'is_kunci' => true],
                    ['teks' => 'يَقْرَأُ القُرْآنَ الكَرِيْمَ وَيَسْتَذْكِرُ الدُّرُوْسَ (Membaca Al-Qur\'an dan mengulang pelajaran).', 'is_kunci' => true],
                    ['teks' => 'يَتَنَاوَلُ طَعَامَ الفُطُوْرِ مَعَ أَصْدِقَائِهِ (Makan sarapan pagi bersama teman-temannya).', 'is_kunci' => true],
                    ['teks' => 'يُسَافِرُ إِلَى مَدِيْنَةٍ أُخْرَى (Bepergian ke kota lain).', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'الحَيَاةُ اليَوْمِيَّةُ', 'Aktivitas Harian & Jam (السَّاعَةُ)', $ar4, $indo4, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kosa kata waktu (السَّاعَةُ) dalam bahasa Arab berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Ungkapan "السَّاعَةُ السَّادِسَةُ وَالنِّصْفُ" bermakna pukul enam lewat tiga puluh menit (06.30).', 'kunci' => 'Benar'],
                    ['teks' => 'Ungkapan "إِلَّا الرُّبْعَ" dalam menyatakan waktu bermakna "lebih lima belas menit".', 'kunci' => 'Salah'],
                    ['teks' => 'Kata "مَسَاءً" digunakan untuk menyatakan keterangan waktu pada sore atau malam hari.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'الحَيَاةُ اليَوْمِيَّةُ', 'Aktivitas Harian & Jam (السَّاعَةُ)', $ar4, $indo4, '4. MENJODOHKAN', 'Pasangkan kata kerja harian (Fi\'il Mudhari\') dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'يَسْتَيْقِظُ', 'respon' => 'Dia bangun tidur'],
                ['premis' => 'يَتَنَاوَلُ الفُطُوْرَ', 'respon' => 'Dia makan sarapan pagi'],
                ['premis' => 'يَرْتَدِي الزِّيَّ', 'respon' => 'Dia memakai seragam sekolah'],
                ['premis' => 'يَرْجِعُ إِلَى البَيْتِ', 'respon' => 'Dia pulang ke rumah']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'يَسْتَيْقِظُ', 'respon' => 'Dia bangun tidur'],
                    ['premis' => 'يَتَنَاوَلُ الفُطُوْرَ', 'respon' => 'Dia makan sarapan pagi'],
                    ['premis' => 'يَرْتَدِي الزِّيَّ', 'respon' => 'Dia memakai seragam sekolah'],
                    ['premis' => 'يَرْجِعُ إِلَى البَيْتِ', 'respon' => 'Dia pulang ke rumah']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'الحَيَاةُ اليَوْمِيَّةُ', 'Aktivitas Harian & Jam (السَّاعَةُ)', $ar4, $indo4, '5. ISIAN SINGKAT', 'Bentuk jamak (Jamak Taksir) dari kata tunggal "كِتَابٌ" (buku) dalam bahasa Arab adalah...'),
            'kunci_jawaban' => 'كُتُبٌ',
            'konten_tambahan' => ['rubrik' => 'كُتُبٌ (atau Kutub / كتب)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): الهِوَايَةُ وَالأَنْشِطَةُ (Hobi & Aktivitas)
        // ══════════════════════════════════════════════════════════════════
        $ar5 = 'لِكُلِّ إِنْسَانٍ هِوَايَةٌ يُحِبُّ مُمَارَسَتَهَا فِي أَوْقَاتِ الفَرَاغِ.<br>
سَلْمَانُ يُحِبُّ <u>قِرَاءَةَ الكُتُبِ الدِّيْنِيَّةِ وَالعِلْمِيَّةِ</u>، وَهُوَ يَزُوْرُ المَكْتَبَةَ كُلَّ أُسْبُوْعٍ.<br>
أَمَّا صَدِيْقُهُ فَيْصَلُ فَهِوَايَتُهُ <u>كُرَةُ القَدَمِ</u>، هُوَ يَلْعَبُ مَعَ فَرِيْقِ المَدْرَسَةِ كُلَّ يَوْمِ جُمُعَةٍ.<br>
وَأَمَّا زَيْنَبُ فَهِيَ تَهْوَى <u>الخَطَّ العَرَبِيَّ وَالرَّسْمَ</u>، وَقَدْ فَازَتْ بِالجَائِزَةِ الأُوْلَى فِي مُسَابَقَةِ الخَطِّ عَلَى مُسْتَوَى المِنْطَقَةِ. الهِوَايَةُ النَّافِعَةُ تُنَمِّي العَقْلَ وَتَبْعَثُ النَّشَاطَ فِي الجِسْمِ.';
        $indo5 = 'Teks mengenai keragaman hobi siswa: membaca buku, sepak bola, seni kaligrafi Arab, dan manfaat hobi positif bagi kebugaran tubuh serta ketajaman berpikir.';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'الهِوَايَةُ وَالأَنْشِطَةُ الطُّلَّابِيَّةُ', 'Ragam Hobi & Jumlah Ismiyyah', $ar5, $indo5, '1. PILIHAN GANDA', 'مَا هِيَ هِوَايَةُ زَيْنَبَ الَّتِي فَازَتْ فِيْهَا بِالجَائِزَةِ الأُوْلَى؟ (Apakah hobi Zainab yang membawanya meraih juara pertama?)'),
            'opsi_a' => 'السِّبَاحَةُ وَالجَرْيُ',
            'opsi_b' => 'الخَطُّ العَرَبِيُّ وَالرَّسْمُ',
            'opsi_c' => 'كُرَةُ السَّلَّةِ',
            'opsi_d' => 'الطَّبْخُ فِي المَطْبَخِ',
            'opsi_e' => 'قِرَاءَةُ الرِّوَايَاتِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'السِّبَاحَةُ وَالجَرْيُ',
                'B' => 'الخَطُّ العَرَبِيُّ وَالرَّسْمُ',
                'C' => 'كُرَةُ السَّلَّةِ',
                'D' => 'الطَّبْخُ فِي المَطْبَخِ',
                'E' => 'قِرَاءَةُ الرِّوَايَاتِ'
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'الهِوَايَةُ وَالأَنْشِطَةُ الطُّلَّابِيَّةُ', 'Ragam Hobi & Jumlah Ismiyyah', $ar5, $indo5, '2. PG KOMPLEKS (CENTANG)', 'Manakah kalimat-kalimat di bawah ini yang merupakan susunan <strong>Jumlah Ismiyyah</strong> (diawali oleh kata benda / Isim)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'سَلْمَانُ طَالِبٌ نَشِيْطٌ فِي المَدْرَسَةِ.',
            'opsi_b' => 'القِرَاءَةُ هِوَايَةٌ نَافِعَةٌ لِلعَقْلِ.',
            'opsi_c' => 'يَلْعَبُ فَيْصَلٌ كُرَةَ القَدَمِ فِي المَلْعَبِ.',
            'opsi_d' => 'المَدْرَسَةُ نَظِيْفَةٌ وَجَمِيْلَةٌ جِدًّا.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, D',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'سَلْمَانُ طَالِبٌ نَشِيْطٌ فِي المَدْرَسَةِ.', 'is_kunci' => true],
                    ['teks' => 'القِرَاءَةُ هِوَايَةٌ نَافِعَةٌ لِلعَقْلِ.', 'is_kunci' => true],
                    ['teks' => 'يَلْعَبُ فَيْصَلٌ كُرَةَ القَدَمِ فِي المَلْعَبِ.', 'is_kunci' => false],
                    ['teks' => 'المَدْرَسَةُ نَظِيْفَةٌ وَجَمِيْلَةٌ جِدًّا.', 'is_kunci' => true]
                ],
                'kunci_array' => ['A', 'B', 'D']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'الهِوَايَةُ وَالأَنْشِطَةُ الطُّلَّابِيَّةُ', 'Ragam Hobi & Jumlah Ismiyyah', $ar5, $indo5, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah Mubtada\' dan Khabar berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Mubtada\' dan Khabar pada kondisi dasar selalu berstatus I\'rab Rofa\' (marfu\').', 'kunci' => 'Benar'],
                    ['teks' => 'Jika Mubtada\' berjenis Muannats, maka Khabar harus tetap berjenis Mudzakkar.', 'kunci' => 'Salah'],
                    ['teks' => 'Kalimat "الطَّالِبَاتُ مَاهِرَاتٌ" benar karena kesesuaian jenis Muannats dan bilangan Jamak.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'الهِوَايَةُ وَالأَنْشِطَةُ الطُّلَّابِيَّةُ', 'Ragam Hobi & Jumlah Ismiyyah', $ar5, $indo5, '4. MENJODOHKAN', 'Pasangkan istilah cabang hobi dalam bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'كُرَةُ القَدَمِ', 'respon' => 'Sepak Bola'],
                ['premis' => 'الخَطُّ العَرَبِيُّ', 'respon' => 'Seni Kaligrafi Arab'],
                ['premis' => 'السِّبَاحَةُ', 'respon' => 'Berenang'],
                ['premis' => 'التَّصْوِيْرُ', 'respon' => 'Fotografi / Memotret']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'كُرَةُ القَدَمِ', 'respon' => 'Sepak Bola'],
                    ['premis' => 'الخَطُّ العَرَبِيُّ', 'respon' => 'Seni Kaligrafi Arab'],
                    ['premis' => 'السِّبَاحَةُ', 'respon' => 'Berenang'],
                    ['premis' => 'التَّصْوِيْرُ', 'respon' => 'Fotografi / Memotret']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'الهِوَايَةُ وَالأَنْشِطَةُ الطُّلَّابِيَّةُ', 'Ragam Hobi & Jumlah Ismiyyah', $ar5, $indo5, '5. ISIAN SINGKAT', 'Isim yang terletak di awal kalimat dalam Jumlah Ismiyyah dan berfungsi sebagai subjek kalimat dinamakan...'),
            'kunci_jawaban' => 'Mubtada',
            'konten_tambahan' => ['rubrik' => 'Mubtada (atau المُبْتَدَأُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): فِي المَكْتَبَةِ (Di Perpustakaan)
        // ══════════════════════════════════════════════════════════════════
        $ar6 = 'المَكْتَبَةُ المَدْرَسِيَّةُ مَكَانٌ هَادِئٌ وَمُنَظَّمٌ لِمُطَالَعَةِ الكُتُبِ وَالبُحُوْثِ.<br>
دَخَلَ زَيْدٌ المَكْتَبَةَ وَقَابَلَ أَمِيْنَ المَكْتَبَةِ السَّيِّدَ عَبْدَ اللهِ. سَأَلَ زَيْدٌ عَنْ <u>كِتَابِ التَّارِيْخِ الإِسْلَامِيِّ</u> لِيَقْرَأَهُ فِي وَقْتِ الِاسْتِرَاحَةِ.<br>
قَالَ أَمِيْنُ المَكْتَبَةِ: "الكُتُبُ الدِّيْنِيَّةُ عَلَى الرَّفِّ الأَوَّلِ، وَكُتُبُ اللُّغَةِ العَرَبِيَّةِ عَلَى الرَّفِّ الثَّانِي، وَكُتُبُ العُلُوْمِ عَلَى الرَّفِّ الثَّالِثِ".<br>
اسْتَعَارَ زَيْدٌ كِتَابَيْنِ مُدَّةَ أُسْبُوْعٍ وَاحِدٍ بِاسْتِخْدَامِ <u>بِطَاقَةِ المَكْتَبَةِ</u>.';
        $indo6 = 'Teks mengenai tata cara meminjam buku di perpustakaan madrasah, klasifikasi buku pada rak, dan penggunaan kartu anggota perpustakaan.';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'فِي المَكْتَبَةِ المَدْرَسِيَّةِ', 'Aktivitas Membaca & Susunan Idhafah', $ar6, $indo6, '1. PILIHAN GANDA', 'كَمْ كِتَابًا اسْتَعَارَ زَيْدٌ مِنْ أَمِيْنِ المَكْتَبَةِ؟ (Berapa bukukah yang dipinjam oleh Zaid?)'),
            'opsi_a' => 'كِتَابًا وَاحِدًا',
            'opsi_b' => 'كِتَابَيْنِ اثْنَيْنِ',
            'opsi_c' => 'ثَلَاثَةَ كُتُبٍ',
            'opsi_d' => 'أَرْبَعَةَ كُتُبٍ',
            'opsi_e' => 'خَمْسَةَ كُتُبٍ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'كِتَابًا وَاحِدًا',
                'B' => 'كِتَابَيْنِ اثْنَيْنِ',
                'C' => 'ثَلَاثَةَ كُتُبٍ',
                'D' => 'أَرْبَعَةَ كُتُبٍ',
                'E' => 'خَمْسَةَ كُتُبٍ'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'فِي المَكْتَبَةِ المَدْرَسِيَّةِ', 'Aktivitas Membaca & Susunan Idhafah', $ar6, $indo6, '2. PG KOMPLEKS (CENTANG)', 'Frasa manakah berikut ini yang merupakan susunan <strong>Idhafah</strong> (Mudhaf dan Mudhaf Ilaih)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'كِتَابُ التَّارِيْخِ (Buku sejarah)',
            'opsi_b' => 'أَمِيْنُ المَكْتَبَةِ (Petugas perpustakaan)',
            'opsi_c' => 'بِطَاقَةُ المَكْتَبَةِ (Kartu perpustakaan)',
            'opsi_d' => 'كِتَابٌ جَدِيْدٌ (Buku baru)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'كِتَابُ التَّارِيْخِ (Buku sejarah)', 'is_kunci' => true],
                    ['teks' => 'أَمِيْنُ المَكْتَبَةِ (Petugas perpustakaan)', 'is_kunci' => true],
                    ['teks' => 'بِطَاقَةُ المَكْتَبَةِ (Kartu perpustakaan)', 'is_kunci' => true],
                    ['teks' => 'كِتَابٌ جَدِيْدٌ (Buku baru)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'فِي المَكْتَبَةِ المَدْرَسِيَّةِ', 'Aktivitas Membaca & Susunan Idhafah', $ar6, $indo6, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah susunan Idhafah berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kata pertama (Mudhaf) tidak boleh memiliki alif lam (ال) dan tidak boleh bertanwin.', 'kunci' => 'Benar'],
                    ['teks' => 'Kata kedua (Mudhaf Ilaih) selalu berstatus I\'rab Rofa\' (marfu\' dhammah).', 'kunci' => 'Salah'],
                    ['teks' => 'Mudhaf Ilaih selalu berstatus I\'rab Jar / Khafadh (majrur dengan kasrah).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'فِي المَكْتَبَةِ المَدْرَسِيَّةِ', 'Aktivitas Membaca & Susunan Idhafah', $ar6, $indo6, '4. MENJODOHKAN', 'Pasangkan kosa kata perpustakaan dengan maknanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'أَمِيْنُ المَكْتَبَةِ', 'respon' => 'Pustakawan / Petugas Perpustakaan'],
                ['premis' => 'اسْتَعَارَ - يَسْتَعِيْرُ', 'respon' => 'Meminjam buku'],
                ['premis' => 'رَفٌّ - رُفُوْفٌ', 'respon' => 'Rak tempat meletakkan buku'],
                ['premis' => 'فِهْرِسٌ', 'respon' => 'Katalog / Daftar Indeks Buku']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'أَمِيْنُ المَكْتَبَةِ', 'respon' => 'Pustakawan / Petugas Perpustakaan'],
                    ['premis' => 'اسْتَعَارَ - يَسْتَعِيْرُ', 'respon' => 'Meminjam buku'],
                    ['premis' => 'رَفٌّ - رُفُوْفٌ', 'respon' => 'Rak tempat meletakkan buku'],
                    ['premis' => 'فِهْرِسٌ', 'respon' => 'Katalog / Daftar Indeks Buku']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'فِي المَكْتَبَةِ المَدْرَسِيَّةِ', 'Aktivitas Membaca & Susunan Idhafah', $ar6, $indo6, '5. ISIAN SINGKAT', 'Unsur kata kedua dalam susunan Idhafah yang berfungsi menerangkan kata pertama dinamakan...'),
            'kunci_jawaban' => 'Mudhaf Ilaih',
            'konten_tambahan' => ['rubrik' => 'Mudhaf Ilaih (atau المُضَافُ إِلَيْهِ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): فِي المَقْصَفِ (Di Kantin & Makanan Halal)
        // ══════════════════════════════════════════════════════════════════
        $ar7 = 'فِي وَقْتِ الِاسْتِرَاحَةِ، يَذْهَبُ التَّلَامِيْذُ إِلَى مَقْصَفِ المَدْرَسَةِ لِتَنَاوُلِ المَأْكُوْلَاتِ الخَفِيْفَةِ وَالمَشْرُوْبَاتِ الطَّازَجَةِ.<br>
فِي المَقْصَفِ أَطْعِمَةٌ حَلَالٌ وَطَيِّبَةٌ، مِثْلُ: الرُّزِّ المَقْلِيِّ، وَالخُبْزِ، وَالفَوَاكِهِ، وَالعَصِيْرِ، وَالشَّايِ.<br>
اشْتَرَى يُوْسُفُ <u>ثَلَاثَةَ أَقْدَاحٍ</u> مِنَ الشَّايِ وَ<u>خَمْسَ خُبْزَاتٍ</u> لَهُ وَلِزُمَلَائِهِ. دَفَعَ يُوْسُفُ ثَمَنَهَا لِلْبَائِعِ بِمَالٍ حَلَالٍ.<br>
يَتَأَدَّبُ التَّلَامِيْذُ بِآدَابِ الأَكْلِ الإِسْلَامِيَّةِ: يَغْسِلُوْنَ أَيْدِيَهُمْ، وَيُسَمُّوْنَ اللهَ تَعَالَى (بِسْمِ اللهِ)، وَيَأْكُلُوْنَ بِاليَمِيْنِ وَهُمْ جَالِسُوْنَ.';
        $indo7 = 'Teks mengenai adab makan di kantin madrasah, menu makanan halal dan sehat, serta transaksi hitungan belanjaan (Adad Ma\'dud).';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'فِي المَقْصَفِ المَدْرَسِيِّ', 'Makanan Halal & Adad Ma\'dud (الْعَدَدُ وَالْمَعْدُوْدُ)', $ar7, $indo7, '1. PILIHAN GANDA', 'كَيْفَ يَأْكُلُ التَّلَامِيْذُ طَعَامَهُمْ فِي المَقْصَفِ وِفْقًا لِلآدَابِ الإِسْلَامِيَّةِ؟ (Bagaimanakah adab makan para siswa sesuai sunnah?)'),
            'opsi_a' => 'يَأْكُلُوْنَ بِالشِّمَالِ وَهُمْ وَاقِفُوْنَ',
            'opsi_b' => 'يَأْكُلُوْنَ بِاليَمِيْنِ وَهُمْ جَالِسُوْنَ بَعْدَ التَّسْمِيَةِ',
            'opsi_c' => 'يَأْكُلُوْنَ مُسْرِعِيْنَ بِدُوْنِ غَسْلِ الأَيْدِي',
            'opsi_d' => 'يَأْكُلُوْنَ وَهُمْ يَرْكُضُوْنَ فِي المَيْدَانِ',
            'opsi_e' => 'يَتْرُكُوْنَ بَقَايَا الطَّعَامِ عَلَى الأَرْضِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'يَأْكُلُوْنَ بِالشِّمَالِ وَهُمْ وَاقِفُوْنَ',
                'B' => 'يَأْكُلُوْنَ بِاليَمِيْنِ وَهُمْ جَالِسُوْنَ بَعْدَ التَّسْمِيَةِ',
                'C' => 'يَأْكُلُوْنَ مُسْرِعِيْنَ بِدُوْنِ غَسْلِ الأَيْدِي',
                'D' => 'يَأْكُلُوْنَ وَهُمْ يَرْكُضُوْنَ فِي المَيْدَانِ',
                'E' => 'يَتْرُكُوْنَ بَقَايَا الطَّعَامِ عَلَى الأَرْضِ'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'فِي المَقْصَفِ المَدْرَسِيِّ', 'Makanan Halal & Adad Ma\'dud (الْعَدَدُ وَالْمَعْدُوْدُ)', $ar7, $indo7, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai kaidah hitungan bilangan (Adad Ma\'dud) 3 sampai 10? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Bentuk jenis kelamin (Mudzakkar/Muannats) bilangan (Adad) harus berlawanan dengan jenis benda yang dihitung (Ma\'dud).',
            'opsi_b' => 'Benda yang dihitung (Ma\'dud) harus berbentuk Jamak dan berharakat Majrur Kasrah.',
            'opsi_c' => 'Jika bendanya Mudzakkar (misal: قَلَمٌ), maka bilangannya harus memakai ta marbuthah (misal: ثَلَاثَةُ أَقْلَامٍ).',
            'opsi_d' => 'Bilangan 3 sampai 10 selalu mengikuti jenis kelamin bendanya secara sama persis.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Bentuk jenis kelamin (Mudzakkar/Muannats) bilangan (Adad) harus berlawanan dengan jenis benda yang dihitung (Ma\'dud).', 'is_kunci' => true],
                    ['teks' => 'Benda yang dihitung (Ma\'dud) harus berbentuk Jamak dan berharakat Majrur Kasrah.', 'is_kunci' => true],
                    ['teks' => 'Jika bendanya Mudzakkar (misal: قَلَمٌ), maka bilangannya harus memakai ta marbuthah (misal: ثَلَاثَةُ أَقْلَامٍ).', 'is_kunci' => true],
                    ['teks' => 'Bilangan 3 sampai 10 selalu mengikuti jenis kelamin bendanya secara sama persis.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'فِي المَقْصَفِ المَدْرَسِيِّ', 'Makanan Halal & Adad Ma\'dud (الْعَدَدُ وَالْمَعْدُوْدُ)', $ar7, $indo7, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari kaidah hitungan angka berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Untuk bilangan 1 dan 2 (وَاحِدٌ / اثْنَانِ), sifat bilangan mengikuti ma\'dud-nya baik dalam jenis maupun harakat.', 'kunci' => 'Benar'],
                    ['teks' => 'Kalimat "عِنْدِي أَرْبَعُ كُتُبٍ" benar secara kaidah tata bahasa Arab.', 'kunci' => 'Salah'],
                    ['teks' => 'Kalimat yang benar adalah "عِنْدِي أَرْبَعَةُ كُتُبٍ" karena kata كِتَابٌ berjenis Mudzakkar.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'فِي المَقْصَفِ المَدْرَسِيِّ', 'Makanan Halal & Adad Ma\'dud (الْعَدَدُ وَالْمَعْدُوْدُ)', $ar7, $indo7, '4. MENJODOHKAN', 'Pasangkan nama hidangan makanan/minuman dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'الرُّزُّ المَقْلِيُّ', 'respon' => 'Nasi Goreng'],
                ['premis' => 'عَصِيْرُ البُرْتُقَالِ', 'respon' => 'Jus Jeruk Segar'],
                ['premis' => 'حَلِيْبٌ طَازَجٌ', 'respon' => 'Susu Segar'],
                ['premis' => 'شَايٌ حَارٌّ', 'respon' => 'Teh Hangat']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'الرُّزُّ المَقْلِيُّ', 'respon' => 'Nasi Goreng'],
                    ['premis' => 'عَصِيْرُ البُرْتُقَالِ', 'respon' => 'Jus Jeruk Segar'],
                    ['premis' => 'حَلِيْبٌ طَازَجٌ', 'respon' => 'Susu Segar'],
                    ['premis' => 'شَايٌ حَارٌّ', 'respon' => 'Teh Hangat']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'فِي المَقْصَفِ المَدْرَسِيِّ', 'Makanan Halal & Adad Ma\'dud (الْعَدَدُ وَالْمَعْدُوْدُ)', $ar7, $indo7, '5. ISIAN SINGKAT', 'Tulislah angka hitungan "5 pulpen" dalam bahasa Arab yang benar: "خَمْسَةُ ... " (Isilah titik-titik dengan bentuk jamak dari qolamun).'),
            'kunci_jawaban' => 'أَقْلَامٍ',
            'konten_tambahan' => ['rubrik' => 'أَقْلَامٍ (atau Aqlam / اقلام)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): المِهْنَةُ وَالحَيَاةُ (Profesi & Karakteristik)
        // ══════════════════════════════════════════════════════════════════
        $ar8 = 'تَتَنَوَّعُ المِهَنُ وَالأَعْمَالُ الَّتِي يَقُوْمُ بِهَا النَّاسُ لِخِدْمَةِ المُجْتَمَعِ.<br>
المُدَرِّسُ يُعَلِّمُ التَّلَامِيْذَ العُلُوْمَ النَّافِعَةَ فِي المَدْرَسَةِ. وَالطَّبِيْبُ يُعَالِجُ المَرْضَى بِإِخْلَاصٍ فِي المُسْتَشْفَى.<br>
وَالمُهَنْدِسُ النَّاجِحُ يُخَطِّطُ لِبِنَاءِ البُيُوْتِ وَالشَّوَارِعِ الحَدِيْثَةِ. وَالفَلَّاحُ المَاهِرُ يَزْرَعُ الرُّزَّ وَالخَضْرَاوَاتِ فِي المَزْرَعَةِ الوَاسِعَةِ.<br>
كُلُّ مِهْنَةٍ شَرِيْفَةٍ نَافِعَةٍ تَنَالُ الأَجْرَ وَالثَّوَابَ عِنْدَ اللهِ إِذَا كَانَتْ خَالِصَةً لِوَجْهِهِ تَعَالَى.';
        $indo8 = 'Teks mengenai ragam profesi yang mulia (Guru, Dokter, Insinyur, Petani), kontribusinya bagi masyarakat, dan penerapan kaidah Na\'at Man\'ut (Kata Sifat).';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المِهْنَةُ وَالحَيَاةُ الاجْتِمَاعِيَّةُ', 'Profesi & Kaidah Na\'at Man\'ut (النَّعْتُ وَالْمَنْعُوْتُ)', $ar8, $indo8, '1. PILIHAN GANDA', 'مَنْ هُوَ الَّذِي يَعْمَلُ فِي المَزْرَعَةِ وَيَزْرَعُ الرُّزَّ وَالخَضْرَاوَاتِ؟ (Siapakah yang bekerja di sawah dan menanam padi serta sayur?)'),
            'opsi_a' => 'المُهَنْدِسُ',
            'opsi_b' => 'الفَلَّاحُ',
            'opsi_c' => 'الشُّرْطِيُّ',
            'opsi_d' => 'الطَّبِيْبُ',
            'opsi_e' => 'التَّاجِرُ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'المُهَنْدِسُ',
                'B' => 'الفَلَّاحُ',
                'C' => 'الشُّرْطِيُّ',
                'D' => 'الطَّبِيْبُ',
                'E' => 'التَّاجِرُ'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المِهْنَةُ وَالحَيَاةُ الاجْتِمَاعِيَّةُ', 'Profesi & Kaidah Na\'at Man\'ut (النَّعْتُ وَالْمَنْعُوْتُ)', $ar8, $indo8, '2. PG KOMPLEKS (CENTANG)', 'Dalam aspek apa sajakah <strong>Na\'at (Kata Sifat) harus mengikuti Man\'ut (Kata yang Disifati)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Dalam I\'rab (Rofa\', Nashob, atau Jar).',
            'opsi_b' => 'Dalam jenis kelamin (Mudzakkar atau Muannats).',
            'opsi_c' => 'Dalam kejelasan (Ma\'rifat atau Nakirah).',
            'opsi_d' => 'Dalam kepemilikan huruf jar saja.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Dalam I\'rab (Rofa\', Nashob, atau Jar).', 'is_kunci' => true],
                    ['teks' => 'Dalam jenis kelamin (Mudzakkar atau Muannats).', 'is_kunci' => true],
                    ['teks' => 'Dalam kejelasan (Ma\'rifat atau Nakirah).', 'is_kunci' => true],
                    ['teks' => 'Dalam kepemilikan huruf jar saja.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المِهْنَةُ وَالحَيَاةُ الاجْتِمَاعِيَّةُ', 'Profesi & Kaidah Na\'at Man\'ut (النَّعْتُ وَالْمَنْعُوْتُ)', $ar8, $indo8, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari kaidah Na\'at Man\'ut pada kalimat berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Frasa "مَدْرَسَةٌ كَبِيْرَةٌ" benar karena kedua kata sama-sama berjenis Muannats dan Nakirah.', 'kunci' => 'Benar'],
                    ['teks' => 'Frasa "الطَّالِبُ المَاهِرَةُ" sudah tepat menurut kaidah bahasa Arab.', 'kunci' => 'Salah'],
                    ['teks' => 'Frasa "المَسْجِدُ الجَمِيْلُ" benar karena keduanya sama-sama memiliki alif lam Ma\'rifat.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المِهْنَةُ وَالحَيَاةُ الاجْتِمَاعِيَّةُ', 'Profesi & Kaidah Na\'at Man\'ut (النَّعْتُ وَالْمَنْعُوْتُ)', $ar8, $indo8, '4. MENJODOHKAN', 'Pasangkan nama profesi dalam bahasa Arab dengan tempat kerjanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'المُدَرِّسُ', 'respon' => 'المَدْرَسَةُ'],
                ['premis' => 'الطَّبِيْبُ', 'respon' => 'المُسْتَشْفَى'],
                ['premis' => 'الفَلَّاحُ', 'respon' => 'المَزْرَعَةُ'],
                ['premis' => 'التَّاجِرُ', 'respon' => 'السُّوْقُ']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'المُدَرِّسُ', 'respon' => 'المَدْرَسَةُ'],
                    ['premis' => 'الطَّبِيْبُ', 'respon' => 'المُسْتَشْفَى'],
                    ['premis' => 'الفَلَّاحُ', 'respon' => 'المَزْرَعَةُ'],
                    ['premis' => 'التَّاجِرُ', 'respon' => 'السُّوْقُ']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المِهْنَةُ وَالحَيَاةُ الاجْتِمَاعِيَّةُ', 'Profesi & Kaidah Na\'at Man\'ut (النَّعْتُ وَالْمَنْعُوْتُ)', $ar8, $indo8, '5. ISIAN SINGKAT', 'Kata sifat dalam bahasa Arab yang mengikuti kata benda sebelumnya dalam 4 perkara dinamakan...'),
            'kunci_jawaban' => 'Na\'at',
            'konten_tambahan' => ['rubrik' => 'Na\'at (atau النَّعْتُ / Sifat)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): عِيَادَةُ المَرِيْضِ (Menjenguk Teman Sakit)
        // ══════════════════════════════════════════════════════════════════
        $ar9 = 'عَلِمَ طَلْحَةُ أَنَّ صَدِيْقَهُ إِبْرَاهِيْمَ مَرِيْضٌ فِي بَيْتِهِ بِسَبَبِ الحُمَّى.<br>
فِي المَسَاءِ، <u>ذَهَبَ</u> طَلْحَةُ مَعَ زُمَلَائِهِ لِعِيَادَتِهِ. <u>دَخَلَ</u> الأَصْدِقَاءُ غُرْفَةَ إِبْرَاهِيْمَ وَسَلَّمُوْا عَلَيْهِ بِأَدَبٍ.<br>
<u>قَدَّمَ</u> طَلْحَةُ بَعْضَ الفَوَاكِهِ الطَّازَجَةِ، وَ<u>دَعَا</u> لَهُ بِالشِّفَاءِ العَاجِلِ قَائِلًا: "لَا بَأْسَ طَهُوْرٌ إِنْ شَاءَ اللهُ، أَسْأَلُ اللهَ العَظِيْمَ رَبَّ العَرْشِ العَظِيْمِ أَنْ يَشْفِيَكَ".<br>
فَرِحَ إِبْرَاهِيْمُ بِزِيَارَتِهِمْ وَشَكَرَهُمْ عَلَى كَرَمِ أَخْلَاقِهِمْ.';
        $indo9 = 'Teks mengenai adab mulia menjenguk sahabat yang sakit demam di rumahnya, doa kesembuhan yang diajarkan Rasulullah SAW, serta penggunaan Fi\'il Madhi (Kata Kerja Lampau).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'عِيَادَةُ المَرِيْضِ وَآدَابُهَا', 'Adab Menjenguk Sahabat & Fi\'il Madhi (الفِعْلُ الْمَاضِي)', $ar9, $indo9, '1. PILIHAN GANDA', 'مَاذَا دَعَا طَلْحَةُ لِصَدِيْقِهِ المَرِيْضِ إِبْرَاهِيْمَ؟ (Apakah doa yang diucapkan Thalhah untuk Ibrahim?)'),
            'opsi_a' => 'جَزَاكَ اللهُ خَيْرًا',
            'opsi_b' => 'لَا بَأْسَ طَهُوْرٌ إِنْ شَاءَ اللهُ، أَسْأَلُ اللهَ العَظِيْمَ أَنْ يَشْفِيَكَ',
            'opsi_c' => 'فِي أَمَانِ اللهِ وَحِفْظِهِ',
            'opsi_d' => 'بَارَكَ اللهُ لَكَ فِي أَهْلِكَ',
            'opsi_e' => 'مَبْرُوْكٌ عَلَيْكَ الفَوْزُ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'جَزَاكَ اللهُ خَيْرًا',
                'B' => 'لَا بَأْسَ طَهُوْرٌ إِنْ شَاءَ اللهُ، أَسْأَلُ اللهَ العَظِيْمَ أَنْ يَشْفِيَكَ',
                'C' => 'فِي أَمَانِ اللهِ وَحِفْظِهِ',
                'D' => 'بَارَكَ اللهُ لَكَ فِي أَهْلِكَ',
                'E' => 'مَبْرُوْكٌ عَلَيْكَ الفَوْزُ'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'عِيَادَةُ المَرِيْضِ وَآدَابُهَا', 'Adab Menjenguk Sahabat & Fi\'il Madhi (الفِعْلُ الْمَاضِي)', $ar9, $indo9, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang merupakan <strong>Fi\'il Madhi (Kata Kerja Bentuk Lampau)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'ذَهَبَ (Dia telah pergi)',
            'opsi_b' => 'دَخَلَ (Dia telah masuk)',
            'opsi_c' => 'قَدَّمَ (Dia telah memberikan)',
            'opsi_d' => 'يَكْتُبُ (Dia sedang menulis)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'ذَهَبَ (Dia telah pergi)', 'is_kunci' => true],
                    ['teks' => 'دَخَلَ (Dia telah masuk)', 'is_kunci' => true],
                    ['teks' => 'قَدَّمَ (Dia telah memberikan)', 'is_kunci' => true],
                    ['teks' => 'يَكْتُبُ (Dia sedang menulis)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'عِيَادَةُ المَرِيْضِ وَآدَابُهَا', 'Adab Menjenguk Sahabat & Fi\'il Madhi (الفِعْلُ الْمَاضِي)', $ar9, $indo9, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari perubahan tasrif Fi\'il Madhi berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Fi\'il Madhi "ذَهَبَ" jika pelakunya adalah dhomir "أَنَا" (saya) berubah menjadi "ذَهَبْتُ".', 'kunci' => 'Benar'],
                    ['teks' => 'Fi\'il Madhi "كَتَبَ" jika pelakunya dhomir "هِيَ" berubah menjadi "كَتَبُوْا".', 'kunci' => 'Salah'],
                    ['teks' => 'Jika pelakunya adalah dhomir "نَحْنُ", maka bentuknya menjadi "ذَهَبْنَا".', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'عِيَادَةُ المَرِيْضِ وَآدَابُهَا', 'Adab Menjenguk Sahabat & Fi\'il Madhi (الفِعْلُ الْمَاضِي)', $ar9, $indo9, '4. MENJODOHKAN', 'Pasangkan Fi\'il Madhi dengan dhomir (subjek pelaku) yang sesuai:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'ذَهَبَ', 'respon' => 'هُوَ (Dia seorang laki-laki)'],
                ['premis' => 'ذَهَبَتْ', 'respon' => 'هِيَ (Dia seorang perempuan)'],
                ['premis' => 'ذَهَبْتَ', 'respon' => 'أَنْتَ (Kamu seorang laki-laki)'],
                ['premis' => 'ذَهَبْتُ', 'respon' => 'أَنَا (Saya)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'ذَهَبَ', 'respon' => 'هُوَ (Dia seorang laki-laki)'],
                    ['premis' => 'ذَهَبَتْ', 'respon' => 'هِيَ (Dia seorang perempuan)'],
                    ['premis' => 'ذَهَبْتَ', 'respon' => 'أَنْتَ (Kamu seorang laki-laki)'],
                    ['premis' => 'ذَهَبْتُ', 'respon' => 'أَنَا (Saya)']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'عِيَادَةُ المَرِيْضِ وَآدَابُهَا', 'Adab Menjenguk Sahabat & Fi\'il Madhi (الفِعْلُ الْمَاضِي)', $ar9, $indo9, '5. ISIAN SINGKAT', 'Ubahlah kata kerja lampau "قَرَأَ" untuk pelaku dhomir saya (أَنَا): "أَنَا ... القُرْآنَ الكَرِيْمَ".'),
            'kunci_jawaban' => 'قَرَأْتُ',
            'konten_tambahan' => ['rubrik' => 'قَرَأْتُ (atau Qara\'tu / قرأت)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ (Jejak Islam)
        // ══════════════════════════════════════════════════════════════════
        $ar10 = 'قَامَ طُلَّابُ مَدْرَسَةِ "مِفْتَاحُ الهُدَى" بِرِحْلَةٍ عِلْمِيَّةٍ لِزِيَارَةِ <u>المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ</u>.<br>
زَارَ الطُّلَّابُ المَسْجِدَ التَّارِيْخِيَّ القَدِيْمَ الَّذِي بَنَاهُ الأَوْلِيَاءُ فِي القَرْنِ السَّادِسَ عَشَرَ المِيْلَادِيِّ. شَاهَدُوْا فَنَّ العِمَارَةِ الإِسْلَامِيَّةِ الرَّائِعَةِ الَّتِي تَمْزُجُ بَيْنَ الثَّقَافَةِ العَرَبِيَّةِ وَالثَّقَافَةِ المَحَلِّيَّةِ.<br>
وَفِي المَتْحَفِ، رَأَى الطُّلَّابُ <u>مَخْطُوْطَاتٍ قَدِيْمَةً</u> مَكْتُوْبَةً بِالخَطِّ الجَمِيْلِ، وَآثَارًا إِسْلَامِيَّةً نَفِيْسَةً تَدُلُّ عَلَى عِزَّةِ الحَضَارَةِ الإِسْلَامِيَّةِ وَنَشْرِ رِسَالَةِ السَّلَامِ فِي رُبُوْعِ النُّوْسَانْتَارَا.';
        $indo10 = 'Teks mengenai rihlah ilmiah para siswa MAS Miftahul Huda mengunjungi peninggalan bersejarah Islam, arsitektur masjid kuno para wali, serta manuskrip berharga di museum.';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ', 'Peninggalan Islam & Jamak Taksir', $ar10, $indo10, '1. PILIHAN GANDA', 'مَاذَا شَاهَدَ الطُّلَّابُ فِي المَتْحَفِ الإِسْلَامِيِّ؟ (Apakah yang disaksikan para siswa di museum Islam?)'),
            'opsi_a' => 'سَيَّارَاتٍ سَرِيْعَةً حَدِيْثَةً',
            'opsi_b' => 'مَخْطُوْطَاتٍ قَدِيْمَةً وَآثَارًا إِسْلَامِيَّةً نَفِيْسَةً',
            'opsi_c' => 'أَسْمَاكًا نَادِرَةً فِي الحَوْضِ',
            'opsi_d' => 'أَجْهِزَةً إِلِكْتِرُونِيَّةً مُتَطَوِّرَةً',
            'opsi_e' => 'أَلْعَابًا رِيَاضِيَّةً مُتَنَوِّعَةً',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'سَيَّارَاتٍ سَرِيْعَةً حَدِيْثَةً',
                'B' => 'مَخْطُوْطَاتٍ قَدِيْمَةً وَآثَارًا إِسْلَامِيَّةً نَفِيْسَةً',
                'C' => 'أَسْمَاكًا نَادِرَةً فِي الحَوْضِ',
                'D' => 'أَجْهِزَةً إِلِكْتِرُونِيَّةً مُتَطَوِّرَةً',
                'E' => 'أَلْعَابًا رِيَاضِيَّةً مُتَنَوِّعَةً'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ', 'Peninggalan Islam & Jamak Taksir', $ar10, $indo10, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang merupakan bentuk <strong>Jamak Taksir</strong> (jamak yang berubah dari pola mufradnya)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'طُلَّابٌ (Bentuk jamak dari طَالِبٌ)',
            'opsi_b' => 'عُلَمَاءُ (Bentuk jamak dari عَالِمٌ)',
            'opsi_c' => 'مَسَاجِدُ (Bentuk jamak dari مَسْجِدٌ)',
            'opsi_d' => 'مُدَرِّسُوْنَ (Jamak Mudzakkar Salim)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'طُلَّابٌ (Bentuk jamak dari طَالِبٌ)', 'is_kunci' => true],
                    ['teks' => 'عُلَمَاءُ (Bentuk jamak dari عَالِمٌ)', 'is_kunci' => true],
                    ['teks' => 'مَسَاجِدُ (Bentuk jamak dari مَسْجِدٌ)', 'is_kunci' => true],
                    ['teks' => 'مُدَرِّسُوْنَ (Jamak Mudzakkar Salim)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ', 'Peninggalan Islam & Jamak Taksir', $ar10, $indo10, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari pembagian Isim ditinjau dari jumlah bilangannya:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Isim Mutsanna (Tasniyah) menunjukkan arti dua dengan menambahkan akhiran alif-nun (ـَانِ) atau ya-nun (ـَيْنِ).', 'kunci' => 'Benar'],
                    ['teks' => 'Jamak Muannats Salim dibentuk dengan menambahkan huruf wawu dan nun (ـُوْنَ) di akhir kata.', 'kunci' => 'Salah'],
                    ['teks' => 'Jamak Muannats Salim diakhiri dengan huruf alif dan ta maftuhah (ـَاتٌ) seperti مُسْلِمَاتٌ.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ', 'Peninggalan Islam & Jamak Taksir', $ar10, $indo10, '4. MENJODOHKAN', 'Pasangkan kata tunggal (Mufrad) dengan bentuk Jamak Taksirnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'مَسْجِدٌ', 'respon' => 'مَسَاجِدُ'],
                ['premis' => 'عَالِمٌ', 'respon' => 'عُلَمَاءُ'],
                ['premis' => 'بَابٌ', 'respon' => 'أَبْوَابٌ'],
                ['premis' => 'وَلَدٌ', 'respon' => 'أَوْلَادٌ']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'مَسْجِدٌ', 'respon' => 'مَسَاجِدُ'],
                    ['premis' => 'عَالِمٌ', 'respon' => 'عُلَمَاءُ'],
                    ['premis' => 'بَابٌ', 'respon' => 'أَبْوَابٌ'],
                    ['premis' => 'وَلَدٌ', 'respon' => 'أَوْلَادٌ']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'زِيَارَةُ المَعَالِمِ الإِسْلَامِيَّةِ التَّارِيْخِيَّةِ', 'Peninggalan Islam & Jamak Taksir', $ar10, $indo10, '5. ISIAN SINGKAT', 'Bentuk kata benda yang menunjukkan jumlah dua (Dual) dalam tata bahasa Arab disebut Isim...'),
            'kunci_jawaban' => 'Mutsanna',
            'konten_tambahan' => ['rubrik' => 'Mutsanna (atau Tasniyah / الْمُثَنَّى)']
        ];

        return $list;
    }
}
