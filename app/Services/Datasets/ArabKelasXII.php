<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class ArabKelasXII {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): الحَضَارَةُ الإِسْلَامِيَّةُ (Era Keemasan & Nashob)
        // ══════════════════════════════════════════════════════════════════
        $ar1 = 'بَلَغَتِ الحَضَارَةُ الإِسْلَامِيَّةُ ذِرْوَةَ مَجْدِهَا فِي العَصْرِ العَبَّاسِيِّ الذَّهَبِيِّ، حَيْثُ أَنْشَأَ الخَلِيْفَةُ هَارُوْنُ الرَّشِيْدُ وَالمَأْمُوْنُ "بَيْتَ الحِكْمَةِ" فِي بَغْدَادَ.<br>
كَانَ العُلَمَاءُ وَالمُتَرْجِمُوْنَ يَعْمَلُوْنَ لَيْلَ نَهَارَ <u>لِيَنْقُلُوْا (كَيْ يَنْقُلُوْا)</u> كُنُوْزَ المَعْرِفَةِ مِنْ حَضَارَاتِ اليُوْنَانِ وَالفُرْسِ وَالهِنْدِ إِلَى اللُّغَةِ العَرَبِيَّةِ.<br>
قَالَ الخَلِيْفَةُ لِطُلَّابِ العِلْمِ: "<u>لَنْ تَبْلُغُوْا المَجْدَ</u> حَتَّى تَبْذُلُوْا الجُهْدَ، وَ<u>عَلَيْكُمْ أَنْ تَتَعَلَّمُوْا</u> العُلُوْمَ النَّافِعَةَ لِتَكُوْنُوْا مَنَارَةً لِلْعَالَمِ".<br>
أَصْبَحَتْ بَغْدَادُ وَقُرْطُبَةُ قِبْلَةً لِطُلَّابِ المَعْرِفَةِ مِنْ أَنْحَاءِ الأَرْضِ، مِمَّا أَثْبَتَ أَنَّ الإِسْلَامَ رَاعِي العِلْمِ وَالتَّقَدُّمِ.';
        $indo1 = 'Teks mengenai kejayaan peradaban Islam era Abbasiyah (Baitul Hikmah Baghdad), penerjemahan ilmu pengetahuan, serta penggunaan Adawatun Nashbi (أَدَوَاتُ النَّصْبِ: أَنْ، لَنْ، كَيْ، حَتَّى، لَامُ التَّعْلِيْلِ) pada Fi\'il Mudhari\'.';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'الحَضَارَةُ الإِسْلَامِيَّةُ فِي العَصْرِ العَبَّاسِيِّ', 'Keemasan Peradaban & Adawatun Nashbi (أَدَوَاتُ النَّصْبِ)', $ar1, $indo1, '1. PILIHAN GANDA', 'مَا اسْمُ المَرْكَزِ العِلْمِيِّ وَالتَّرْجَمِيِّ الشَّهِيْرِ الَّذِي أُنْشِئَ فِي بَغْدَادَ؟ (Apakah nama pusat keilmuan dan penerjemahan terkenal di Baghdad?)'),
            'opsi_a' => 'دَارُ النَّدْوَةِ',
            'opsi_b' => 'بَيْتُ الحِكْمَةِ',
            'opsi_c' => 'مَجْلِسُ الشُّوْرَى',
            'opsi_d' => 'المَدْرَسَةُ النِّظَامِيَّةُ',
            'opsi_e' => 'مَكْتَبَةُ الإِسْكَنْدَرِيَّةِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'دَارُ النَّدْوَةِ',
                'B' => 'بَيْتُ الحِكْمَةِ',
                'C' => 'مَجْلِسُ الشُّوْرَى',
                'D' => 'المَدْرَسَةُ النِّظَامِيَّةُ',
                'E' => 'مَكْتَبَةُ الإِسْكَنْدَرِيَّةِ'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'الحَضَارَةُ الإِسْلَامِيَّةُ فِي العَصْرِ العَبَّاسِيِّ', 'Keemasan Peradaban & Adawatun Nashbi (أَدَوَاتُ النَّصْبِ)', $ar1, $indo1, '2. PG KOMPLEKS (CENTANG)', 'Huruf-huruf manakah di bawah ini yang tergolong <strong>Adawatun Nashbi (Piranti Penashob Fi\'il Mudhari\')</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'أَنْ (Bahwasanya)',
            'opsi_b' => 'لَنْ (Tidak akan pernah)',
            'opsi_c' => 'كَيْ / حَتَّى / لَامُ التَّعْلِيْلِ (Supaya / Hingga)',
            'opsi_d' => 'لَمْ (Adawatul Jazmi penegas masa lalu)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'أَنْ (Bahwasanya)', 'is_kunci' => true],
                    ['teks' => 'لَنْ (Tidak akan pernah)', 'is_kunci' => true],
                    ['teks' => 'كَيْ / حَتَّى / لَامُ التَّعْلِيْلِ (Supaya / Hingga)', 'is_kunci' => true],
                    ['teks' => 'لَمْ (Adawatul Jazmi penegas masa lalu)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'الحَضَارَةُ الإِسْلَامِيَّةُ فِي العَصْرِ العَبَّاسِيِّ', 'Keemasan Peradaban & Adawatun Nashbi (أَدَوَاتُ النَّصْبِ)', $ar1, $indo1, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari tanda I\'rab nashob pada Fi\'il Mudhari\' berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Tanda nashob asal pada Fi\'il Mudhari\' shahih akhir yang didahului adawatun nashbi adalah harakat Fathah (contoh: أَنْ يَذْهَبَ).', 'kunci' => 'Benar'],
                    ['teks' => 'Fi\'il Mudhari\' yang didahului huruf "لَنْ" harakat akhirnya selalu berubah menjadi Sukun.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada Af\'alul Khamsah (أَمْثِلَةُ الخَمْسَةِ), tanda nashob-nya adalah terbuangnya huruf Nun (حَذْفُ النُّوْنِ), contoh: لَنْ تَفْعَلُوْا.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'الحَضَارَةُ الإِسْلَامِيَّةُ فِي العَصْرِ العَبَّاسِيِّ', 'Keemasan Peradaban & Adawatun Nashbi (أَدَوَاتُ النَّصْبِ)', $ar1, $indo1, '4. MENJODOHKAN', 'Pasangkan huruf penashob Fi\'il Mudhari\' dengan faedah maknanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'لَنْ', 'respon' => 'Menafikan perbuatan di masa yang akan datang dengan tegas (tidak akan)'],
                ['premis' => 'كَيْ / لِـ', 'respon' => 'Menjelaskan alasan atau sebab (supaya / agar)'],
                ['premis' => 'حَتَّى', 'respon' => 'Menunjukkan batas akhir tujuan (sehingga / hingga)'],
                ['premis' => 'إِذَنْ', 'respon' => 'Sebagai jawaban pembalasan perkataan (kalau begitu)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'لَنْ', 'respon' => 'Menafikan perbuatan di masa yang akan datang dengan tegas (tidak akan)'],
                    ['premis' => 'كَيْ / لِـ', 'respon' => 'Menjelaskan alasan atau sebab (supaya / agar)'],
                    ['premis' => 'حَتَّى', 'respon' => 'Menunjukkan batas akhir tujuan (sehingga / hingga)'],
                    ['premis' => 'إِذَنْ', 'respon' => 'Sebagai jawaban pembalasan perkataan (kalau begitu)']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'الحَضَارَةُ الإِسْلَامِيَّةُ فِي العَصْرِ العَبَّاسِيِّ', 'Keemasan Peradaban & Adawatun Nashbi (أَدَوَاتُ النَّصْبِ)', $ar1, $indo1, '5. ISIAN SINGKAT', 'Lengkapilah harakat akhir fi\'il mudhari\' berikut setelah kemasukan huruf lan: "لَنْ يَنْجَحَ الكَسْلَانُ" (Tidak akan pernah sukses orang yang pemalas). Harakat huruf ha (ح) adalah...'),
            'kunci_jawaban' => 'Fathah',
            'konten_tambahan' => ['rubrik' => 'Fathah (atau Fathah / فَتْحَة)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): دَوْرُ الشَّبَابِ (Peran Pemuda & Jazm)
        // ══════════════════════════════════════════════════════════════════
        $ar2 = 'الشَّبَابُ هُمْ عِمَادُ الأُمَّةِ وَسِرُّ نَهْضَتِهَا، وَعَلَى سَوَاعِدِهِمْ يُبْنَى مُسْتَقْبَلُ الوَطَنِ.<br>
سَجَّلَ التَّارِيْخُ الإِسْلَامِيُّ مَوَاقِفَ بُطُوْلِيَّةً لِشَبَابٍ قَادُوْا الأُمَّةَ، مِثْلُ أُسَامَةَ بْنِ زَيْدٍ الَّذِي قَادَ الجَيْشَ وَعُمْرُهُ سَبْعَةَ عَشَرَ عَامًا، وَمُحَمَّدٍ الفَاتِحِ الَّذِي فَتَحَ القُسْطَنْطِيْنِيَّةَ فِي عُمْرِ الحَادِي وَالعِشْرِيْنَ.<br>
يُوَجِّهُ النَّاصِحُوْنَ نِدَاءَهُمْ لِلْجِيْلِ الصَّاعِدِ: "<u>لَا تُضَيِّعُوْا أَوْقَاتَكُمْ</u> فِيمَا لَا يَنْفَعُ، وَ<u>لَمْ يَنَلْ أَحَدٌ المَجْدَ</u> بِالكَسَلِ، وَ<u>لْتَتَّقُوا اللهَ</u> فِي سِرِّكُمْ وَعَلَانِيَتِكُمْ".<br>
إِذَا صَلَحَ الشَّبَابُ صَلَحَتِ الأُمَّةُ كُلُّهَا، وَإِذَا فَسَدُوْا ضَاعَ الحَاضِرُ وَالمُسْتَقْبَلُ.';
        $indo2 = 'Teks mengenai peranan sentral pemuda dalam kebangkitan bangsa, teladan kepemimpinan Usamah bin Zaid dan Muhammad Al-Fatih, serta kaidah Adawatul Jazmi (أَدَوَاتُ الجَزْمِ: لَمْ، لَمَّا، لَامُ الأَمْرِ، لَا النَّاهِيَةُ).';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'دَوْرُ الشَّبَابِ فِي بِنَاءِ الأُمَّةِ', 'Kepemimpinan Pemuda & Adawatul Jazmi (أَدَوَاتُ الْجَزْمِ)', $ar2, $indo2, '1. PILIHAN GANDA', 'كَمْ كَانَ عُمْرُ مُحَمَّدٍ الفَاتِحِ حِيْنَ فَتَحَ مَدِيْنَةَ القُسْطَنْطِيْنِيَّةِ؟ (Berapakah usia Muhammad Al-Fatih ketika menaklukkan Konstantinopel?)'),
            'opsi_a' => 'سَبْعَةَ عَشَرَ عَامًا',
            'opsi_b' => 'الحَادِي وَالعِشْرِيْنَ عَامًا (21 tahun)',
            'opsi_c' => 'ثَلَاثِيْنَ عَامًا',
            'opsi_d' => 'أَرْبَعِيْنَ عَامًا',
            'opsi_e' => 'خَمْسَةَ عَشَرَ عَامًا',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'سَبْعَةَ عَشَرَ عَامًا',
                'B' => 'الحَادِي وَالعِشْرِيْنَ عَامًا (21 tahun)',
                'C' => 'ثَلَاثِيْنَ عَامًا',
                'D' => 'أَرْبَعِيْنَ عَامًا',
                'E' => 'خَمْسَةَ عَشَرَ عَامًا'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'دَوْرُ الشَّبَابِ فِي بِنَاءِ الأُمَّةِ', 'Kepemimpinan Pemuda & Adawatul Jazmi (أَدَوَاتُ الْجَزْمِ)', $ar2, $indo2, '2. PG KOMPLEKS (CENTANG)', 'Huruf-huruf manakah di bawah ini yang bertugas <strong>menjazmkan satu Fi\'il Mudhari\' (أَدَوَاتُ جَزْمِ الفِعْلِ الوَاحِدِ)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'لَمْ (Tidak / belum di masa lalu)',
            'opsi_b' => 'لَا النَّاهِيَةُ (Janganlah / larangan)',
            'opsi_c' => 'لَامُ الأَمْرِ (Hendaklah / perintah)',
            'opsi_d' => 'لَنْ (Huruf penashob)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'لَمْ (Tidak / belum di masa lalu)', 'is_kunci' => true],
                    ['teks' => 'لَا النَّاهِيَةُ (Janganlah / larangan)', 'is_kunci' => true],
                    ['teks' => 'لَامُ الأَمْرِ (Hendaklah / perintah)', 'is_kunci' => true],
                    ['teks' => 'لَنْ (Huruf penashob)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'دَوْرُ الشَّبَابِ فِي بِنَاءِ الأُمَّةِ', 'Kepemimpinan Pemuda & Adawatul Jazmi (أَدَوَاتُ الْجَزْمِ)', $ar2, $indo2, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari tanda-tanda I\'rab Jazm pada Fi\'il Mudhari\' berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Tanda jazm asal untuk Fi\'il Mudhari\' shahih akhir adalah tanda Sukun (contoh: لَمْ يَكْتُبْ).', 'kunci' => 'Benar'],
                    ['teks' => 'Fi\'il Mu\'tal akhir (berakhiran alif, wawu, ya) ketika dijazmkan tetap mempertahankan huruf illatnya.', 'kunci' => 'Salah'],
                    ['teks' => 'Tanda jazm untuk Fi\'il Mu\'tal akhir adalah membuang huruf illat (حَذْفُ حَرْفِ العِلَّةِ), contoh: لَا تَدْعُ.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'دَوْرُ الشَّبَابِ فِي بِنَاءِ الأُمَّةِ', 'Kepemimpinan Pemuda & Adawatul Jazmi (أَدَوَاتُ الْجَزْمِ)', $ar2, $indo2, '4. MENJODOHKAN', 'Pasangkan huruf penjadzim dengan fungsi maknanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'لَمْ', 'respon' => 'Menafikan perbuatan di waktu lampau (belum/tidak pernah)'],
                ['premis' => 'لَمَّا', 'respon' => 'Menafikan perbuatan sampai saat berbicara (belum selesai)'],
                ['premis' => 'لَامُ الأَمْرِ (لِـ)', 'respon' => 'Menuntut dilakukannya perbuatan (hendaklah)'],
                ['premis' => 'لَا النَّاهِيَةُ', 'respon' => 'Menuntut ditinggalkannya perbuatan (janganlah)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'لَمْ', 'respon' => 'Menafikan perbuatan di waktu lampau (belum/tidak pernah)'],
                    ['premis' => 'لَمَّا', 'respon' => 'Menafikan perbuatan sampai saat berbicara (belum selesai)'],
                    ['premis' => 'لَامُ الأَمْرِ (لِـ)', 'respon' => 'Menuntut dilakukannya perbuatan (hendaklah)'],
                    ['premis' => 'لَا النَّاهِيَةُ', 'respon' => 'Menuntut ditinggalkannya perbuatan (janganlah)']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'دَوْرُ الشَّبَابِ فِي بِنَاءِ الأُمَّةِ', 'Kepemimpinan Pemuda & Adawatul Jazmi (أَدَوَاتُ الْجَزْمِ)', $ar2, $indo2, '5. ISIAN SINGKAT', 'Tanda jazm pada fi\'il af\'alul khamsah pada kalimat "لَا تَحْزَنُوْا إِنَّ اللهَ مَعَنَا" adalah dengan membuang huruf...'),
            'kunci_jawaban' => 'Nun',
            'konten_tambahan' => ['rubrik' => 'Nun (atau Hadzfun Nun / حَذْفُ النُّوْنِ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): الأَعْيَادُ الإِسْلَامِيَّةُ (Hari Raya & Maf'ul Li Ajlih)
        // ══════════════════════════════════════════════════════════════════
        $ar3 = 'يَفْرَحُ المُسْلِمُوْنَ فِي أَنْحَاءِ العَالَمِ بِحُلُوْلِ عِيْدِ الفِطْرِ وَعِيْدِ الأَضْحَى المُبَارَكَيْنِ.<br>
فِي عِيْدِ الفِطْرِ، يُخْرِجُ الأَغْنِيَاءُ زَكَاةَ الفِطْرِ <u>شُكْرًا للهِ تَعَالَى</u> عَلَى تَوْفِيْقِهِ لِصِيَامِ رَمَضَانَ، وَ<u>مُوَاسَاةً لِلْفُقَرَاءِ</u> كَيْ يَبْتَهِجُوْا بِيَوْمِ العِيْدِ.<br>
وَفِي عِيْدِ الأَضْحَى، يُضَحِّي المُسْلِمُوْنَ بِالأَنْعَامِ <u>ابْتِغَاءَ مَرْضَاةِ اللهِ</u> وَاتِّبَاعًا لِسُنَّةِ أَبِيْنَا إِبْرَاهِيْمَ عَلَيْهِ السَّلَامُ.<br>
يَتَبَادَلُ النَّاسُ التَّهَانِي وَالتَّزَاوُرَ <u>تَوْطِيْدًا لِأَوَاصِرِ الأُخُوَّةِ</u> وَالمَحَبَّةِ بَيْنَ الأَهْلِ وَالجِيْرَانِ.';
        $indo3 = 'Teks mengenai perayaan Hari Raya Idul Fitri dan Idul Adha, zakat fitrah dan ibadah qurban, serta penerapan kaidah Maf\'ul Li Ajlih (المَفْعُوْلُ لِأَجْلِهِ: keterangan alasan/tujuan dilakukannya pekerjaan).';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'الأَعْيَادُ وَالمُنَاسَبَاتُ الدِّيْنِيَّةُ', 'Hari Raya Islam & Maf\'ul Li Ajlih (الْمَفْعُوْلُ لِأَجْلِهِ)', $ar3, $indo3, '1. PILIHAN GANDA', 'لِمَاذَا يُخْرِجُ المُسْلِمُوْنَ زَكَاةَ الفِطْرِ فِي نِهَايَةِ شَهْرِ رَمَضَانَ؟ (Mengapa kaum muslimin menunaikan zakat fitrah?)'),
            'opsi_a' => 'شُكْرًا للهِ تَعَالَى وَمُوَاسَاةً لِلْفُقَرَاءِ',
            'opsi_b' => 'رِيَاءً وَسُمْعَةً أَمَامَ النَّاسِ',
            'opsi_c' => 'خَوْفًا مِنَ العُقُوْبَاتِ القَانُوْنِيَّةِ الدُّنْيَوِيَّةِ',
            'opsi_d' => 'لِتَجْمِيْعِ الأَمْوَالِ فِي البُنُوْكِ',
            'opsi_e' => 'بِدُوْنِ قَصْدٍ أَوْ هَدَفٍ',
            'kunci_jawaban' => 'A',
            'konten_tambahan' => ['kunci' => 'A', 'opsi' => [
                'A' => 'شُكْرًا للهِ تَعَالَى وَمُوَاسَاةً لِلْفُقَرَاءِ',
                'B' => 'رِيَاءً وَسُمْعَةً أَمَامَ النَّاسِ',
                'C' => 'خَوْفًا مِنَ العُقُوْبَاتِ القَانُوْنِيَّةِ الدُّنْيَوِيَّةِ',
                'D' => 'لِتَجْمِيْعِ الأَمْوَالِ فِي البُنُوْكِ',
                'E' => 'بِدُوْنِ قَصْدٍ أَوْ هَدَفٍ'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'الأَعْيَادُ وَالمُنَاسَبَاتُ الدِّيْنِيَّةُ', 'Hari Raya Islam & Maf\'ul Li Ajlih (الْمَفْعُوْلُ لِأَجْلِهِ)', $ar3, $indo3, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai karakteristik gramatikal <strong>Maf\'ul Li Ajlih (المَفْعُوْلُ لِأَجْلِهِ)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Merupakan isim masdar qolbi (perasaan hati/alasan) yang berstatus Manshub (fathah).',
            'opsi_b' => 'Menjawab pertanyaan "Mengapa (لِمَاذَا)" perbuatan tersebut dilakukan oleh si pelaku.',
            'opsi_c' => 'Bersekutu dengan perbuatan dalam hal waktu dan pelaku (Fa\'il).',
            'opsi_d' => 'Menjelaskan tempat atau waktu terjadinya perbuatan.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Merupakan isim masdar qolbi (perasaan hati/alasan) yang berstatus Manshub (fathah).', 'is_kunci' => true],
                    ['teks' => 'Menjawab pertanyaan "Mengapa (لِمَاذَا)" perbuatan tersebut dilakukan oleh si pelaku.', 'is_kunci' => true],
                    ['teks' => 'Bersekutu dengan perbuatan dalam hal waktu dan pelaku (Fa\'il).', 'is_kunci' => true],
                    ['teks' => 'Menjelaskan tempat atau waktu terjadinya perbuatan.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'الأَعْيَادُ وَالمُنَاسَبَاتُ الدِّيْنِيَّةُ', 'Hari Raya Islam & Maf\'ul Li Ajlih (الْمَفْعُوْلُ لِأَجْلِهِ)', $ar3, $indo3, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari analisis kedudukan Maf\'ul Li Ajlih berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pada kalimat "قُمْتُ إِجْلَالًا لِلأُسْتَاذِ", kata "إِجْلَالًا" berkedudukan sebagai Maf\'ul Li Ajlih manshub.', 'kunci' => 'Benar'],
                    ['teks' => 'Maf\'ul Li Ajlih selalu didahului oleh huruf jar min (مِنْ) secara wajib.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada kalimat "يُضَحِّي المُسْلِمُوْنَ ابْتِغَاءَ مَرْضَاةِ اللهِ", kata "ابْتِغَاءَ" adalah Maf\'ul Li Ajlih berbentuk mudhaf.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'الأَعْيَادُ وَالمُنَاسَبَاتُ الدِّيْنِيَّةُ', 'Hari Raya Islam & Maf\'ul Li Ajlih (الْمَفْعُوْلُ لِأَجْلِهِ)', $ar3, $indo3, '4. MENJODOHKAN', 'Pasangkan kalimat ber-Maf\'ul Li Ajlih dengan makna tujuannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'سَافَرْتُ رَغْبَةً فِي العِلْمِ', 'respon' => 'Karena kecintaan / hasrat mendalam pada ilmu'],
                ['premis' => 'صَامَ إِيْمَانًا وَاحْتِسَابًا', 'respon' => 'Karena keimanan dan mengharap pahala ridha Allah'],
                ['premis' => 'تَصَدَّقَ خَوْفًا مِنَ النَّارِ', 'respon' => 'Karena rasa takut akan siksa api neraka'],
                ['premis' => 'وَقَفَ تَعْظِيْمًا لِلوَالِدَيْنِ', 'respon' => 'Sebagai bentuk penghormatan mulia kepada kedua orang tua']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'سَافَرْتُ رَغْبَةً فِي العِلْمِ', 'respon' => 'Karena kecintaan / hasrat mendalam pada ilmu'],
                    ['premis' => 'صَامَ إِيْمَانًا وَاحْتِسَابًا', 'respon' => 'Karena keimanan dan mengharap pahala ridha Allah'],
                    ['premis' => 'تَصَدَّقَ خَوْفًا مِنَ النَّارِ', 'respon' => 'Karena rasa takut akan siksa api neraka'],
                    ['premis' => 'وَقَفَ تَعْظِيْمًا لِلوَالِدَيْنِ', 'respon' => 'Sebagai bentuk penghormatan mulia kepada kedua orang tua']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'الأَعْيَادُ وَالمُنَاسَبَاتُ الدِّيْنِيَّةُ', 'Hari Raya Islam & Maf\'ul Li Ajlih (الْمَفْعُوْلُ لِأَجْلِهِ)', $ar3, $indo3, '5. ISIAN SINGKAT', 'Isim masdar manshub yang berfungsi menjelaskan alasan / motif dilakukannya suatu perbuatan dinamakan Maf\'ul...'),
            'kunci_jawaban' => 'Li Ajlih',
            'konten_tambahan' => ['rubrik' => 'Li Ajlih (atau Maf\'ul Li Ajlih / لِأَجْلِهِ / Maf\'ul Lah)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا (Lingkungan & Hal)
        // ══════════════════════════════════════════════════════════════════
        $ar4 = 'أَمَرَنَا الإِسْلَامُ بِالمُحَافَظَةِ عَلَى البِيْئَةِ، وَنَهَانَا عَنِ الإِفْسَادِ فِي الأَرْضِ بَعْدَ إِصْلَاحِهَا.<br>
قَالَ رَسُوْلُ اللهِ ﷺ: "مَا مِنْ مُسْلِمٍ يَغْرِسُ غَرْسًا أَوْ يَزْرَعُ زَرْعًا فَيَأْكُلُ مِنْهُ طَيْرٌ أَوْ إِنْسَانٌ أَوْ بَهِيْمَةٌ إِلَّا كَانَ لَهُ بِهِ صَدَقَةٌ".<br>
خَرَجَ طُلَّابُ المَدْرَسَةِ فِي يَوْمِ البِيْئَةِ <u>مُتَحَمِّسِيْنَ</u> لِغَرْسِ الأَشْجَارِ، وَعَمِلُوْا طَوَالَ النَّهَارِ <u>مُبْتَسِمِيْنَ وَمُتَعَاوِنِيْنَ</u>.<br>
رَجَعَ الطُّلَّابُ إِلَى بُيُوْتِهِمْ <u>مَسْرُوْرِيْنَ</u> بِمَا قَدَّمُوْهُ مِنْ خِدْمَةٍ جَلِيْلَةٍ لِحِمَايَةِ الطَّبِيْعَةِ مِنَ التَّلَوُّثِ.';
        $indo4 = 'Teks mengenai pelestarian lingkungan hidup dan reboisasi dalam perspektif hadits Nabi, kepedulian terhadap kelestarian alam, serta penggunaan Al-Hal (الحَالُ: penjelas keadaan subjek/objek saat berlangsungnya perbuatan).';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا', 'Kelestarian Alam & Kaidah Al-Hal (الْحَالُ)', $ar4, $indo4, '1. PILIHAN GANDA', 'كَيْفَ كَانَتْ حَالُ الطُّلَّابِ حِيْنَ رَجَعُوْا إِلَى بُيُوْتِهِمْ بَعْدَ غَرْسِ الأَشْجَارِ؟ (Bagaimanakah keadaan siswa saat pulang ke rumah?)'),
            'opsi_a' => 'رَجَعُوْا حَزِيْنِيْنَ وَخَائِفِيْنَ',
            'opsi_b' => 'رَجَعُوْا مَسْرُوْرِيْنَ بِمَا قَدَّمُوْهُ لِحِمَايَةِ الطَّبِيْعَةِ',
            'opsi_c' => 'رَجَعُوْا غَاضِبِيْنَ مِنَ العَمَلِ',
            'opsi_d' => 'رَجَعُوْا نَائِمِيْنَ فِي الشَّارِعِ',
            'opsi_e' => 'رَجَعُوْا خَاسِرِيْنَ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'رَجَعُوْا حَزِيْنِيْنَ وَخَائِفِيْنَ',
                'B' => 'رَجَعُوْا مَسْرُوْرِيْنَ بِمَا قَدَّمُوْهُ لِحِمَايَةِ الطَّبِيْعَةِ',
                'C' => 'رَجَعُوْا غَاضِبِيْنَ مِنَ العَمَلِ',
                'D' => 'رَجَعُوْا نَائِمِيْنَ فِي الشَّارِعِ',
                'E' => 'رَجَعُوْا خَاسِرِيْنَ'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا', 'Kelestarian Alam & Kaidah Al-Hal (الْحَالُ)', $ar4, $indo4, '2. PG KOMPLEKS (CENTANG)', 'Syarat-syarat gramatikal manakah yang <strong>harus dipenuhi oleh Al-Hal (الحَالُ)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Al-Hal harus berupa Isim Nakirah (tanpa alif lam) yang berstatus Manshub (fathah/ya).',
            'opsi_b' => 'Shahibul Hal (pemilik keadaan yang diterangkan) harus berupa Isim Ma\'rifat.',
            'opsi_c' => 'Menjawab pertanyaan "Bagaimana keadaannya (كَيْفَ)" saat pekerjaan tersebut berlangsung.',
            'opsi_d' => 'Al-Hal harus selalu berupa isim jamak taksir saja.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Al-Hal harus berupa Isim Nakirah (tanpa alif lam) yang berstatus Manshub (fathah/ya).', 'is_kunci' => true],
                    ['teks' => 'Shahibul Hal (pemilik keadaan yang diterangkan) harus berupa Isim Ma\'rifat.', 'is_kunci' => true],
                    ['teks' => 'Menjawab pertanyaan "Bagaimana keadaannya (كَيْفَ)" saat pekerjaan tersebut berlangsung.', 'is_kunci' => true],
                    ['teks' => 'Al-Hal harus selalu berupa isim jamak taksir saja.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا', 'Kelestarian Alam & Kaidah Al-Hal (الْحَالُ)', $ar4, $indo4, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah Al-Hal pada contoh berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pada kalimat "جَاءَ زَيْدٌ رَاكِبًا", kata "رَاكِبًا" berkedudukan sebagai Hal manshub.', 'kunci' => 'Benar'],
                    ['teks' => 'Shahibul Hal pada kalimat "جَاءَ زَيْدٌ رَاكِبًا" adalah kata "رَاكِبًا".', 'kunci' => 'Salah'],
                    ['teks' => 'Shahibul Hal pada kalimat tersebut adalah "زَيْدٌ" yang berstatus isim ma\'rifat.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا', 'Kelestarian Alam & Kaidah Al-Hal (الْحَالُ)', $ar4, $indo4, '4. MENJODOHKAN', 'Pasangkan kalimat ber-Hal dengan terjemahan keadaannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'شَرِبَ المَاءَ جَالِسًا', 'respon' => 'Dia meminum air dalam keadaan duduk'],
                ['premis' => 'أَدَّى الصَّلَاةَ خَاشِعًا', 'respon' => 'Dia menunaikan sholat dalam keadaan khusyuk'],
                ['premis' => 'دَخَلَ الفَصْلَ مُبْتَسِمًا', 'respon' => 'Dia memasuki kelas dalam keadaan tersenyum'],
                ['premis' => 'تَكَلَّمَ فَصِيْحًا', 'respon' => 'Dia berbicara dalam keadaan fasih']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'شَرِبَ المَاءَ جَالِسًا', 'respon' => 'Dia meminum air dalam keadaan duduk'],
                    ['premis' => 'أَدَّى الصَّلَاةَ خَاشِعًا', 'respon' => 'Dia menunaikan sholat dalam keadaan khusyuk'],
                    ['premis' => 'دَخَلَ الفَصْلَ مُبْتَسِمًا', 'respon' => 'Dia memasuki kelas dalam keadaan tersenyum'],
                    ['premis' => 'تَكَلَّمَ فَصِيْحًا', 'respon' => 'Dia berbicara dalam keadaan fasih']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'البِيْئَةُ وَالمُحَافَظَةُ عَلَيْهَا', 'Kelestarian Alam & Kaidah Al-Hal (الْحَالُ)', $ar4, $indo4, '5. ISIAN SINGKAT', 'Isim nakirah manshub yang menerangkan kondisi atau keadaan pelaku (Fa\'il) atau objek (Maf\'ul) saat terjadinya perbuatan dinamakan...'),
            'kunci_jawaban' => 'Hal',
            'konten_tambahan' => ['rubrik' => 'Hal (atau Al-Hal / الْحَالُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): لُغَةُ القُرْآنِ (Bahasa Al-Qur'an & Tamyiz)
        // ══════════════════════════════════════════════════════════════════
        $ar5 = 'اللُّغَةُ العَرَبِيَّةُ هِيَ لُغَةُ القُرْآنِ الكَرِيْمِ وَلُغَةُ أَهْلِ الجَنَّةِ، وَهِيَ أَوْسَعُ اللُّغَاتِ مُفْرَدَاتٍ وَأَدَقُّهَا تَعْبِيْرًا.<br>
قَالَ عُمَرُ بْنُ الخَطَّابِ رَضِيَ اللهُ عَنْهُ: "تَعَلَّمُوا العَرَبِيَّةَ فَإِنَّهَا تُثَبِّتُ العَقْلَ وَتَزِيْدُ فِي المُرُوْءَةِ".<br>
اشْتَرَى أَحْمَدُ <u>عِشْرِيْنَ كِتَابًا</u> فِي قَوَاعِدِ النَّحْوِ وَالبَلَاغَةِ، وَازْدَادَ الطَّالِبُ <u>عِلْمًا وَفَهْمًا</u> بِدِرَاسَتِهَا.<br>
إِنَّ مَنْ يُتْقِنُ اللُّغَةَ العَرَبِيَّةَ يَكُوْنُ <u>أَكْثَرَ قُدْرَةً</u> عَلَى فَهْمِ مَقَاصِدِ الشَّرِيْعَةِ وَتَذَوُّقِ إِعْجَازِ القُرْآنِ البَيَانِيِّ.';
        $indo5 = 'Teks mengenai keistimewaan bahasa Arab sebagai bahasa Al-Qur\'an, pesan sahabat Umar bin Khattab, serta kaidah At-Tamyiz (التَّمْيِيْزُ: penjelas kesamaran zat ukuran, timbangan, jarak, bilangan, atau penisbatan).';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'دِرَاسَةُ اللُّغَةِ العَرَبِيَّةِ لُغَةُ القُرْآنِ', 'Keagungan Bahasa Arab & At-Tamyiz (التَّمْيِيْزُ)', $ar5, $indo5, '1. PILIHAN GANDA', 'مَاذَا قَالَ عُمَرُ بْنُ الخَطَّابِ رَضِيَ اللهُ عَنْهُ عَنْ أَهَمِّيَّةِ تَعَلُّمِ اللُّغَةِ العَرَبِيَّةِ؟ (Apakah pesan Umar bin Khattab tentang belajar bahasa Arab?)'),
            'opsi_a' => 'أَنَّهَا تُضْعِفُ الذَّاكِرَةَ',
            'opsi_b' => 'أَنَّهَا تُثَبِّتُ العَقْلَ وَتَزِيْدُ فِي المُرُوْءَةِ',
            'opsi_c' => 'أَنَّهَا لَا حَاجَةَ إِلَيْهَا لِفَهْمِ الدِّيْنِ',
            'opsi_d' => 'أَنَّهَا صَعْبَةٌ لَا يُمْكِنُ تَعَلُّمُهَا',
            'opsi_e' => 'أَنَّهَا لِلتِّجَارَةِ فَقَطْ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَنَّهَا تُضْعِفُ الذَّاكِرَةَ',
                'B' => 'أَنَّهَا تُثَبِّتُ العَقْلَ وَتَزِيْدُ فِي المُرُوْءَةِ',
                'C' => 'أَنَّهَا لَا حَاجَةَ إِلَيْهَا لِفَهْمِ الدِّيْنِ',
                'D' => 'أَنَّهَا صَعْبَةٌ لَا يُمْكِنُ تَعَلُّمُهَا',
                'E' => 'أَنَّهَا لِلتِّجَارَةِ فَقَطْ'
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'دِرَاسَةُ اللُّغَةِ العَرَبِيَّةِ لُغَةُ القُرْآنِ', 'Keagungan Bahasa Arab & At-Tamyiz (التَّمْيِيْزُ)', $ar5, $indo5, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai kedudukan dan macam-macam <strong>At-Tamyiz (التَّمْيِيْزُ)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Tamyiz adalah isim nakirah manshub yang berfungsi menghilangkan kesamaran zat atau nisbah sebelumnya.',
            'opsi_b' => 'Tamyiz Dzāt menghilangkan kesamaran pada takaran (kayl), timbangan (wazn), jarak (misahah), atau bilangan hitungan (adad).',
            'opsi_c' => 'Isim mufrad manshub setelah hitungan angka 11 sampai 99 selalu berkedudukan sebagai Tamyiz (contoh: رَأَيْتُ أَحَدَ عَشَرَ كَوْكَبًا).',
            'opsi_d' => 'Tamyiz selalu berharakat marfu\' dhammah.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Tamyiz adalah isim nakirah manshub yang berfungsi menghilangkan kesamaran zat atau nisbah sebelumnya.', 'is_kunci' => true],
                    ['teks' => 'Tamyiz Dzāt menghilangkan kesamaran pada takaran (kayl), timbangan (wazn), jarak (misahah), atau bilangan hitungan (adad).', 'is_kunci' => true],
                    ['teks' => 'Isim mufrad manshub setelah hitungan angka 11 sampai 99 selalu berkedudukan sebagai Tamyiz (contoh: رَأَيْتُ أَحَدَ عَشَرَ كَوْكَبًا).', 'is_kunci' => true],
                    ['teks' => 'Tamyiz selalu berharakat marfu\' dhammah.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'دِرَاسَةُ اللُّغَةِ العَرَبِيَّةِ لُغَةُ القُرْآنِ', 'Keagungan Bahasa Arab & At-Tamyiz (التَّمْيِيْزُ)', $ar5, $indo5, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari analisis Tamyiz Nisbah pada kalimat berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pada kalimat "حَسُنَ الطَّالِبُ خُلُقًا", kata "خُلُقًا" berkedudukan sebagai Tamyiz penjelas aspek kebaikan.', 'kunci' => 'Benar'],
                    ['teks' => 'Isim yang terletak setelah Isim Tafdhil wazan af\'alu selalu berkedudukan sebagai Maf\'ul Bih.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada kalimat "أَنَا أَكْثَرُ مِنْكَ مَالًا", kata "مَالًا" adalah Tamyiz manshub yang menjelaskan keunggulan.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'دِرَاسَةُ اللُّغَةِ العَرَبِيَّةِ لُغَةُ القُرْآنِ', 'Keagungan Bahasa Arab & At-Tamyiz (التَّمْيِيْزُ)', $ar5, $indo5, '4. MENJODOHKAN', 'Pasangkan kalimat ber-Tamyiz dengan jenis kesamaran yang dihilangkannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'اشْتَرَيْتُ كِيْلُوغْرَامًا لَحْمًا', 'respon' => 'Tamyiz penjelas timbangan (Wazn)'],
                ['premis' => 'شَرِبْتُ لِتْرًا حَلِيْبًا', 'respon' => 'Tamyiz penjelas takaran volume (Kayl)'],
                ['premis' => 'فِي الفَصْلِ ثَلَاثُوْنَ طَالِبًا', 'respon' => 'Tamyiz penjelas bilangan hitungan (Adad)'],
                ['premis' => 'طَابَ زَيْدٌ نَفْسًا', 'respon' => 'Tamyiz penjelas penisbatan kalimat (Nisbah)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'اشْتَرَيْتُ كِيْلُوغْرَامًا لَحْمًا', 'respon' => 'Tamyiz penjelas timbangan (Wazn)'],
                    ['premis' => 'شَرِبْتُ لِتْرًا حَلِيْبًا', 'respon' => 'Tamyiz penjelas takaran volume (Kayl)'],
                    ['premis' => 'فِي الفَصْلِ ثَلَاثُوْنَ طَالِبًا', 'respon' => 'Tamyiz penjelas bilangan hitungan (Adad)'],
                    ['premis' => 'طَابَ زَيْدٌ نَفْسًا', 'respon' => 'Tamyiz penjelas penisbatan kalimat (Nisbah)']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'دِرَاسَةُ اللُّغَةِ العَرَبِيَّةِ لُغَةُ القُرْآنِ', 'Keagungan Bahasa Arab & At-Tamyiz (التَّمْيِيْزُ)', $ar5, $indo5, '5. ISIAN SINGKAT', 'Isim nakirah manshub yang berfungsi menghilangkan ketidakjelasan atau kesamaran makna dari kata atau kalimat sebelumnya disebut...'),
            'kunci_jawaban' => 'Tamyiz',
            'konten_tambahan' => ['rubrik' => 'Tamyiz (atau At-Tamyiz / التَّمْيِيْزُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): العَوْلَمَةُ وَالهُوِيَّةُ (Globalisasi & Istitsna')
        // ══════════════════════════════════════════════════════════════════
        $ar6 = 'تَفْرِضُ العَوْلَمَةُ الحَدِيْثَةُ تَحَدِّيَاتٍ فِكْرِيَّةً وَثَقَافِيَّةً كَبِيْرَةً عَلَى هُوِيَّةِ الشَّبَابِ الإِسْلَامِيِّ.<br>
انْجَرَفَ كَثِيْرٌ مِنَ النَّاسِ وَرَاءَ التَّقَالِيْدِ الغَرْبِيَّةِ، <u>فَنَجَا الشَّبَابُ إِلَّا المُتَمَسِّكِيْنَ بِدِيْنِهِمْ</u> وَأَخْلَاقِهِمْ السَّامِيَةِ.<br>
حَضَرَ الطُّلَّابُ النَّدْوَةَ الفِكْرِيَّةَ <u>كُلُّهُمْ إِلَّا طَالِبًا وَاحِدًا</u>، وَنَاقَشُوْا كَيْفِيَّةَ الِاسْتِفَادَةِ مِنَ التَّطَوُّرِ دُوْنَ التَّخَلِّي عَنِ القِيَمِ الإِسْلَامِيَّةِ.<br>
قَالَ المُنَاضِلُ: "كُلُّ شَيْءٍ يُمْكِنُ أَنْ يَتَغَيَّرَ فِي الحَيَاةِ <u>إِلَّا مَبَادِئَ العَقِيْدَةِ وَالأَخْلَاقِ</u>، فَهِيَ ثَابِتَةٌ كَالجِبَالِ الرَّاسِيَاتِ".';
        $indo6 = 'Teks mengenai tantangan globalisasi terhadap identitas muslim, menjaga integritas nilai Islam, serta penggunaan Al-Mustatsna bi Illa (المُسْتَثْنَى بِإِلَّا: pengecualian).';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَوْلَمَةُ وَالهُوِيَّةُ الإِسْلَامِيَّةُ', 'Tantangan Globalisasi & Istitsna\' (الِاسْتِثْنَاءُ بِإِلَّا)', $ar6, $indo6, '1. PILIHAN GANDA', 'مَا الشَّيْءُ الَّذِي لَا يُمْكِنُ أَنْ يَتَغَيَّرَ فِي الحَيَاةِ كَمَا وَرَدَ فِي خِتَامِ النَّصِّ؟ (Apakah perkara yang tidak boleh berubah dalam hidup menurut teks?)'),
            'opsi_a' => 'أَنْوَاعُ الأَطْعِمَةِ وَالمَلَابِسِ',
            'opsi_b' => 'مَبَادِئُ العَقِيْدَةِ وَالأَخْلَاقِ الثَّابِتَةُ',
            'opsi_c' => 'وَسَائِلُ النَّقْلِ وَالمُوَاصَلَاتِ',
            'opsi_d' => 'أَجْهِزَةُ الحَوَاسِيْبِ وَالهَوَاتِفِ',
            'opsi_e' => 'بِنَاءُ البُيُوْتِ وَالعِمَارَاتِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَنْوَاعُ الأَطْعِمَةِ وَالمَلَابِسِ',
                'B' => 'مَبَادِئُ العَقِيْدَةِ وَالأَخْلَاقِ الثَّابِتَةُ',
                'C' => 'وَسَائِلُ النَّقْلِ وَالمُوَاصَلَاتِ',
                'D' => 'أَجْهِزَةُ الحَوَاسِيْبِ وَالهَوَاتِفِ',
                'E' => 'بِنَاءُ البُيُوْتِ وَالعِمَارَاتِ'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَوْلَمَةُ وَالهُوِيَّةُ الإِسْلَامِيَّةُ', 'Tantangan Globalisasi & Istitsna\' (الِاسْتِثْنَاءُ بِإِلَّا)', $ar6, $indo6, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai rukun dan hukum <strong>Istitsna\' (Pengecualian)</strong> dalam bahasa Arab? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Rukun Istitsna\' terdiri atas Mustatsna Minhu, Adatul Istitsna\' (إِلَّا), dan Mustatsna.',
            'opsi_b' => 'Dalam kalimat Tam Mujab (lengkap dan positif/tidak ada nafi), Mustatsna bi Illa wajib berstatus Manshub (fathah).',
            'opsi_c' => 'Kata "غَيْرَ" dan "سِوَى" juga dapat berfungsi sebagai alat istitsna\' pengecualian.',
            'opsi_d' => 'Mustatsna bi Illa selalu berharakat majrur kasrah.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Rukun Istitsna\' terdiri atas Mustatsna Minhu, Adatul Istitsna\' (إِلَّا), dan Mustatsna.', 'is_kunci' => true],
                    ['teks' => 'Dalam kalimat Tam Mujab (lengkap dan positif/tidak ada nafi), Mustatsna bi Illa wajib berstatus Manshub (fathah).', 'is_kunci' => true],
                    ['teks' => 'Kata "غَيْرَ" dan "سِوَى" juga dapat berfungsi sebagai alat istitsna\' pengecualian.', 'is_kunci' => true],
                    ['teks' => 'Mustatsna bi Illa selalu berharakat majrur kasrah.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَوْلَمَةُ وَالهُوِيَّةُ الإِسْلَامِيَّةُ', 'Tantangan Globalisasi & Istitsna\' (الِاسْتِثْنَاءُ بِإِلَّا)', $ar6, $indo6, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari analisis kalimat Istitsna\' berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pada kalimat "حَضَرَ الطُّلَّابُ إِلَّا عَلِيًّا", kata "عَلِيًّا" berkedudukan sebagai Mustatsna manshub.', 'kunci' => 'Benar'],
                    ['teks' => 'Pada kalimat "مَا قَامَ إِلَّا زَيْدٌ", kata "زَيْدٌ" wajib dibaca manshub fathah.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada kalimat Mufarragh (negatif tanpa mustatsna minhu: مَا قَامَ إِلَّا زَيْدٌ), kata setelah illa dii\'rab sesuai tuntutan kedudukannya (Fa\'il marfu\').', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَوْلَمَةُ وَالهُوِيَّةُ الإِسْلَامِيَّةُ', 'Tantangan Globalisasi & Istitsna\' (الِاسْتِثْنَاءُ بِإِلَّا)', $ar6, $indo6, '4. MENJODOHKAN', 'Pasangkan kalimat pengecualian dengan terjemahannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'قَرَأْتُ الكِتَابَ إِلَّا صَفْحَةً', 'respon' => 'Saya membaca buku itu kecuali satu halaman'],
                ['premis' => 'نَجَحَ التَّلَامِيْذُ إِلَّا الكَسْلَانَ', 'respon' => 'Para siswa lulus kecuali siswa yang pemalas'],
                ['premis' => 'مَا فَعَلُوْهُ إِلَّا قَلِيْلٌ مِنْهُمْ', 'respon' => 'Tidaklah mereka mengerjakannya kecuali sedikit dari mereka'],
                ['premis' => 'لَا إِلَهَ إِلَّا اللهُ', 'respon' => 'Tiada Tuhan yang berhak disembah selain Allah']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'قَرَأْتُ الكِتَابَ إِلَّا صَفْحَةً', 'respon' => 'Saya membaca buku itu kecuali satu halaman'],
                    ['premis' => 'نَجَحَ التَّلَامِيْذُ إِلَّا الكَسْلَانَ', 'respon' => 'Para siswa lulus kecuali siswa yang pemalas'],
                    ['premis' => 'مَا فَعَلُوْهُ إِلَّا قَلِيْلٌ مِنْهُمْ', 'respon' => 'Tidaklah mereka mengerjakannya kecuali sedikit dari mereka'],
                    ['premis' => 'لَا إِلَهَ إِلَّا اللهُ', 'respon' => 'Tiada Tuhan yang berhak disembah selain Allah']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَوْلَمَةُ وَالهُوِيَّةُ الإِسْلَامِيَّةُ', 'Tantangan Globalisasi & Istitsna\' (الِاسْتِثْنَاءُ بِإِلَّا)', $ar6, $indo6, '5. ISIAN SINGKAT', 'Kata benda yang dikecualikan dari hukum kelompok sebelumnya dengan perantara kata إِلَّا dinamakan...'),
            'kunci_jawaban' => 'Mustatsna',
            'konten_tambahan' => ['rubrik' => 'Mustatsna (atau المُسْتَثْنَى)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): الدَّعْوَةُ الإِسْلَامِيَّةُ (Metode Dakwah & Munada)
        // ══════════════════════════════════════════════════════════════════
        $ar7 = 'قَالَ اللهُ تَعَالَى: {ادْعُ إِلَى سَبِيْلِ رَبِّكَ بِالحِكْمَةِ وَالمَوْعِظَةِ الحَسَنَةِ وَجَادِلْهُمْ بِالَّتِي هِيَ أَحْسَنُ}.<br>
الدَّعْوَةُ الإِسْلَامِيَّةُ رِسَالَةُ رَحْمَةٍ وَهِدَايَةٍ لِلْعَالَمِيْنَ، لَا إِكْرَاهَ فِيْهَا وَلَا عُنْفَ.<br>
خَاطَبَ الدَّاعِيَةُ الحَاضِرِيْنَ بِلِينٍ وَرِفْقٍ قَائِلًا: "<u>يَا عَبْدَ اللهِ</u>، اتَّقِ اللهَ حَيْثُمَا كُنْتَ، وَ<u>يَا طَالِبَ العِلْمِ</u>، أَخْلِصْ نِيَّتَكَ للهِ، وَ<u>يَا مُسْلِمُوْنَ</u>، كُوْنُوْا يَدًا وَاحِدَةً".<br>
نَشَرَ أَوْلِيَاءُ اللهِ التِّسْعَةُ (وَالِي سُوْنْغُو) الإِسْلَامَ فِي إِنْدُوْنِيْسِيَا بِطَرِيْقَةٍ سِلْمِيَّةٍ حَكِيْمَةٍ جَذَبَتْ قُلُوْبَ النَّاسِ.';
        $indo7 = 'Teks mengenai metode dakwah bil hikmah dan mau\'izhah hasanah, teladan dakwah damai para Wali Songo di Nusantara, serta kaidah Al-Munada (المُنَادَى: kata yang dipanggil dengan huruf nida\' يَا).';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'الدَّعْوَةُ الإِسْلَامِيَّةُ بِالحِكْمَةِ', 'Metode Dakwah & Kaidah Al-Munada (الْمُنَادَى)', $ar7, $indo7, '1. PILIHAN GANDA', 'كَيْفَ نَشَرَ أَوْلِيَاءُ اللهِ التِّسْعَةُ (وَالِي سُوْنْغُو) الإِسْلَامَ فِي إِنْدُوْنِيْسِيَا؟ (Bagaimanakah metode Wali Songo menyebarkan Islam?)'),
            'opsi_a' => 'بِالحَرْبِ وَالقَهْرِ وَالإِجْبَارِ',
            'opsi_b' => 'بِطَرِيْقَةٍ سِلْمِيَّةٍ حَكِيْمَةٍ جَذَبَتْ قُلُوْبَ النَّاسِ',
            'opsi_c' => 'بِفَرْضِ الضَّرَائِبِ البَاهِظَةِ',
            'opsi_d' => 'بِالِانْعِزَالِ عَنِ المُجْتَمَعِ',
            'opsi_e' => 'بِتَرْكِ الدَّعْوَةِ تَمَامًا',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'بِالحَرْبِ وَالقَهْرِ وَالإِجْبَارِ',
                'B' => 'بِطَرِيْقَةٍ سِلْمِيَّةٍ حَكِيْمَةٍ جَذَبَتْ قُلُوْبَ النَّاسِ',
                'C' => 'بِفَرْضِ الضَّرَائِبِ البَاهِظَةِ',
                'D' => 'بِالِانْعِزَالِ عَنِ المُجْتَمَعِ',
                'E' => 'بِتَرْكِ الدَّعْوَةِ تَمَامًا'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'الدَّعْوَةُ الإِسْلَامِيَّةُ بِالحِكْمَةِ', 'Metode Dakwah & Kaidah Al-Munada (الْمُنَادَى)', $ar7, $indo7, '2. PG KOMPLEKS (CENTANG)', 'Keadaan manakah di bawah ini di mana <strong>Munada (kata yang dipanggil) wajib dibaca Manshub (fathah)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Jika Munada berbentuk Mudhaf (contoh: يَا عَبْدَ اللهِ / يَا رَسُوْلَ اللهِ).',
            'opsi_b' => 'Jika Munada berbentuk Syabih bil Mudhaf (contoh: يَا طَالِعًا جَبَلًا).',
            'opsi_c' => 'Jika Munada berbentuk Nakirah Ghairu Maqshudah (contoh panggilan umum orang buta: يَا رَجُلًا خُذْ بِيَدِي).',
            'opsi_d' => 'Jika Munada berupa nama orang tunggal (Mufrad Alam).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Jika Munada berbentuk Mudhaf (contoh: يَا عَبْدَ اللهِ / يَا رَسُوْلَ اللهِ).', 'is_kunci' => true],
                    ['teks' => 'Jika Munada berbentuk Syabih bil Mudhaf (contoh: يَا طَالِعًا جَبَلًا).', 'is_kunci' => true],
                    ['teks' => 'Jika Munada berbentuk Nakirah Ghairu Maqshudah (contoh panggilan umum orang buta: يَا رَجُلًا خُذْ بِيَدِي).', 'is_kunci' => true],
                    ['teks' => 'Jika Munada berupa nama orang tunggal (Mufrad Alam).', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'الدَّعْوَةُ الإِسْلَامِيَّةُ بِالحِكْمَةِ', 'Metode Dakwah & Kaidah Al-Munada (الْمُنَادَى)', $ar7, $indo7, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah hukum Munada berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Munada Mufrad \'Alam (nama orang) mabni di atas harakat Dhammah tanpa tanwin (contoh: يَا مُحَمَّدُ / يَا زَيْدُ).', 'kunci' => 'Benar'],
                    ['teks' => 'Munada berbentuk mudhaf seperti "يَا عَبْدَ اللهِ" harus berharakat dhammah "يَا عَبْدُ اللهِ".', 'kunci' => 'Salah'],
                    ['teks' => 'Huruf nida\' yang paling umum digunakan untuk memanggil jarak dekat maupun jauh adalah huruf "يَا".', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'الدَّعْوَةُ الإِسْلَامِيَّةُ بِالحِكْمَةِ', 'Metode Dakwah & Kaidah Al-Munada (الْمُنَادَى)', $ar7, $indo7, '4. MENJODOHKAN', 'Pasangkan kalimat panggilan (Munada) dengan hukum I\'rabnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'يَا أَحْمَدُ', 'respon' => 'Mabni Dhammah (Mufrad \'Alam nama orang)'],
                ['premis' => 'يَا غُلَامُ', 'respon' => 'Mabni Dhammah (Nakirah Maqshudah tertentu)'],
                ['premis' => 'يَا طَالِبَ العِلْمِ', 'respon' => 'Manshub Fathah (Munada Mudhaf)'],
                ['premis' => 'يَا غَافِلًا عَنِ المَوْتِ', 'respon' => 'Manshub Fathah Bertanwin (Syabih bil Mudhaf)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'يَا أَحْمَدُ', 'respon' => 'Mabni Dhammah (Mufrad \'Alam nama orang)'],
                    ['premis' => 'يَا غُلَامُ', 'respon' => 'Mabni Dhammah (Nakirah Maqshudah tertentu)'],
                    ['premis' => 'يَا طَالِبَ العِلْمِ', 'respon' => 'Manshub Fathah (Munada Mudhaf)'],
                    ['premis' => 'يَا غَافِلًا عَنِ المَوْتِ', 'respon' => 'Manshub Fathah Bertanwin (Syabih bil Mudhaf)']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'الدَّعْوَةُ الإِسْلَامِيَّةُ بِالحِكْمَةِ', 'Metode Dakwah & Kaidah Al-Munada (الْمُنَادَى)', $ar7, $indo7, '5. ISIAN SINGKAT', 'Kata yang diseru atau dipanggil setelah huruf nida\' dinamakan...'),
            'kunci_jawaban' => 'Munada',
            'konten_tambahan' => ['rubrik' => 'Munada (atau Al-Munada / الْمُنَادَى)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): القِيَادَةُ العَادِلَةُ (Pemimpin Adil & Asma'ul Khamsah)
        // ══════════════════════════════════════════════════════════════════
        $ar8 = 'يُعَدُّ الخَلِيْفَةُ الأُمَوِيُّ عُمَرُ بْنُ عَبْدِ العَزِيْزِ خَامِسَ الخُلَفَاءِ الرَّاشِدِيْنَ لِعَدْلِهِ وَزُهْدِهِ وَأَمَانَتِهِ.<br>
<u>كَانَ أَبُوْهُ عَبْدُ العَزِيْزِ</u> أَمِيْرًا صَالِحًا، وَتَرَبَّى عُمَرُ عَلَى تَقْوَى اللهِ. عِنْدَمَا تَوَلَّى الخِلَافَةَ، رَفَضَ مَظَاهِرَ البَذَخِ، وَأَطْفَأَ شَمْعَةَ بَيْتِ المَالِ عِنْدَمَا تَحَدَّثَ فِي أَمْرِهِ الخَاصِّ.<br>
قَالَ لِأَهْلِهِ: "<u>إِنَّ أَبَاكُمْ</u> لَا يَمْلِكُ إِلَّا نَفْسَهُ، وَلَنْ يَأْخُذَ دِرْهَمًا حَرَامًا". فَاضَ المَالُ فِي عَهْدِهِ حَتَّى كَانَ الرَّجُلُ يَخْرُجُ بِزَكَاتِهِ <u>فَلَا يَجِدُ ذَا حَاجَةٍ</u> يَقْبَلُهَا مِنْهُ.<br>
إِنَّ القِيَادَةَ المَبْنِيَّةَ عَلَى خَوْفِ اللهِ تَجْلِبُ الرَّخَاءَ وَالبَرَكَةَ لِلْبِلَادِ وَالعِبَادِ.';
        $indo8 = 'Teks mengenai integritas kepemimpinan adil Khalifah Umar bin Abdul Aziz (memadamkan lilin negara untuk urusan pribadi), pemerataan kesejahteraan rakyat, serta kaidah Asma\'ul Khamsah (أَبُوْكَ، أَخُوْكَ، حَمُوْكَ، فُوْكَ، ذُوْ مَالٍ).';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'القِيَادَةُ العَادِلَةُ فِي التَّارِيْخِ', 'Keteladanan Pemimpin & Asma\'ul Khamsah (الأَسْمَاءُ الْخَمْسَةُ)', $ar8, $indo8, '1. PILIHAN GANDA', 'مَا الدَّلِيْلُ عَلَى كَثْرَةِ الخَيْرِ وَالرَّخَاءِ فِي عَهْدِ عُمَرَ بْنِ عَبْدِ العَزِيْزِ؟ (Apakah bukti kemakmuran di masa Umar bin Abdul Aziz?)'),
            'opsi_a' => 'أَنَّ النَّاسَ كَانُوْا يَبْنُوْنَ القُصُوْرَ العَالِيَةَ',
            'opsi_b' => 'أَنَّ الرَّجُلَ يَخْرُجُ بِزَكَاتِهِ فَلَا يَجِدُ فَقِيْرًا ذَا حَاجَةٍ يَقْبَلُهَا',
            'opsi_c' => 'أَنَّ الأَسْوَاقَ كَانَتْ مُغْلَقَةً',
            'opsi_d' => 'أَنَّ الحُرُوْبَ اشْتَعَلَتْ فِي كُلِّ مَكَانٍ',
            'opsi_e' => 'فَرَضَ ضَرَائِبَ جَدِيْدَةً',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَنَّ النَّاسَ كَانُوْا يَبْنُوْنَ القُصُوْرَ العَالِيَةَ',
                'B' => 'أَنَّ الرَّجُلَ يَخْرُجُ بِزَكَاتِهِ فَلَا يَجِدُ فَقِيْرًا ذَا حَاجَةٍ يَقْبَلُهَا',
                'C' => 'أَنَّ الأَسْوَاقَ كَانَتْ مُغْلَقَةً',
                'D' => 'أَنَّ الحُرُوْبَ اشْتَعَلَتْ فِي كُلِّ مَكَانٍ',
                'E' => 'فَرَضَ ضَرَائِبَ جَدِيْدَةً'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'القِيَادَةُ العَادِلَةُ فِي التَّارِيْخِ', 'Keteladanan Pemimpin & Asma\'ul Khamsah (الأَسْمَاءُ الْخَمْسَةُ)', $ar8, $indo8, '2. PG KOMPLEKS (CENTANG)', 'Isim-isim manakah yang tergolong dalam kelompok <strong>Asma\'ul Khamsah (Lima Isim Khusus)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'أَبٌ (Ayah) & أَخٌ (Saudara laki-laki)',
            'opsi_b' => 'حَمٌ (Mertua / kerabat suami)',
            'opsi_c' => 'فُوْ (Mulut) & ذُوْ (Pemilik)',
            'opsi_d' => 'ابْنٌ (Anak) & بِنْتٌ (Anak perempuan)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'أَبٌ (Ayah) & أَخٌ (Saudara laki-laki)', 'is_kunci' => true],
                    ['teks' => 'حَمٌ (Mertua / kerabat suami)', 'is_kunci' => true],
                    ['teks' => 'فُوْ (Mulut) & ذُوْ (Pemilik)', 'is_kunci' => true],
                    ['teks' => 'ابْنٌ (Anak) & بِنْتٌ (Anak perempuan)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'القِيَادَةُ العَادِلَةُ فِي التَّارِيْخِ', 'Keteladanan Pemimpin & Asma\'ul Khamsah (الأَسْمَاءُ الْخَمْسَةُ)', $ar8, $indo8, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari tanda-tanda I\'rab khusus Asma\'ul Khamsah berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Asma\'ul Khamsah marfu\' dengan huruf Wawu (أَبُوْكَ), manshub dengan Alif (أَبَاكَ), dan majrur dengan Ya (أَبِيْكَ).', 'kunci' => 'Benar'],
                    ['teks' => 'Jika Asma\'ul Khamsah disandarkan kepada Ya Mutakallim (أَبِيْ / ayahku), tetap dii\'rab dengan huruf wawu/alif/ya.', 'kunci' => 'Salah'],
                    ['teks' => 'Syarat dii\'rabnya Asma\'ul Khamsah dengan huruf adalah harus berstatus mufrad, mukabbar, dan di-idhafah-kan selain kepada ya mutakallim.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'القِيَادَةُ العَادِلَةُ فِي التَّارِيْخِ', 'Keteladanan Pemimpin & Asma\'ul Khamsah (الأَسْمَاءُ الْخَمْسَةُ)', $ar8, $indo8, '4. MENJODOHKAN', 'Pasangkan kalimat Asma\'ul Khamsah dengan status kedudukan I\'rabnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'جَاءَ أَبُوْكَ مُبْتَسِمًا', 'respon' => 'Fa\'il Marfu\' dengan tanda huruf Wawu'],
                ['premis' => 'رَأَيْتُ أَبَاكَ فِي المَسْجِدِ', 'respon' => 'Maf\'ul Bih Manshub dengan tanda huruf Alif'],
                ['premis' => 'سَلَّمْتُ عَلَى أَبِيْكَ', 'respon' => 'Isim Majrur dengan tanda huruf Ya'],
                ['premis' => 'إِنَّ أَبَاكَ رَجُلٌ صَالِحٌ', 'respon' => 'Isim Inna Manshub dengan tanda huruf Alif']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'جَاءَ أَبُوْكَ مُبْتَسِمًا', 'respon' => 'Fa\'il Marfu\' dengan tanda huruf Wawu'],
                    ['premis' => 'رَأَيْتُ أَبَاكَ فِي المَسْجِدِ', 'respon' => 'Maf\'ul Bih Manshub dengan tanda huruf Alif'],
                    ['premis' => 'سَلَّمْتُ عَلَى أَبِيْكَ', 'respon' => 'Isim Majrur dengan tanda huruf Ya'],
                    ['premis' => 'إِنَّ أَبَاكَ رَجُلٌ صَالِحٌ', 'respon' => 'Isim Inna Manshub dengan tanda huruf Alif']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'القِيَادَةُ العَادِلَةُ فِي التَّارِيْخِ', 'Keteladanan Pemimpin & Asma\'ul Khamsah (الأَسْمَاءُ الْخَمْسَةُ)', $ar8, $indo8, '5. ISIAN SINGKAT', 'Tanda I\'rab Rofa\' bagi kelompok kata Asma\'ul Khamsah (seperti pada kata أَبُوْكَ) adalah menggunakan huruf...'),
            'kunci_jawaban' => 'Wawu',
            'konten_tambahan' => ['rubrik' => 'Wawu (atau الْوَاوُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): التَّرْبِيَةُ الإِسْلَامِيَّةُ (Pendidikan Karakter & Mamnu')
        // ══════════════════════════════════════════════════════════════════
        $ar9 = 'كَتَبَ الإِمَامُ أَبُوْ حَامِدٍ الغَزَالِيُّ فِي رِسَالَتِهِ "أَيُّهَا الوَلَدُ" نَصَائِحَ ذَهَبِيَّةً فِي التَّرْبِيَةِ وَبِنَاءِ الأَخْلَاقِ.<br>
أَكَّدَ الإِمَامُ أَنَّ العِلْمَ بِلَا عَمَلٍ جُنُوْنٌ، وَالعَمَلَ بِغَيْرِ عِلْمٍ لَا يَكُوْنُ. وَدَعَا الطُّلَّابَ لِلصَّلَاةِ فِي <u>مَسَاجِدَ كَثِيْرَةٍ</u> وَحُضُوْرِ مَجَالِسِ <u>عُلَمَاءَ أَفَاضِلَ</u>.<br>
تَعَلَّمَ التَّلَامِيْذُ أَخْلَاقَ الصَّحَابَةِ، مِثْلَ: <u>عُمَرَ وَعُثْمَانَ وَفَاطِمَةَ وَعَائِشَةَ</u> رَضِيَ اللهُ عَنْهُمْ، حَيْثُ جَمَعُوْا بَيْنَ حُسْنِ الخُلُقِ وَسَعَةِ المَعْرِفَةِ.<br>
إِنَّ المَدْرَسَةَ الَّتِي تَبْنِي شَخْصِيَّةَ الإِنْسَانِ عَلَى الإِيْمَانِ وَالعِلْمِ هِيَ الَّتِي تُنِيْرُ طَرِيْقَ الحَيَاةِ.';
        $indo9 = 'Teks mengenai petuah pendidikan karakter Imam Al-Ghazali (Ayyuhal Walad), integritas ilmu dan amal, teladan sahabat nabi, serta kaidah Al-Mamnu\' minash Sharf (المَمْنُوْعُ مِنَ الصَّرْفِ: isim yang tidak menerima tanwin dan kasrah saat majrur).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'التَّرْبِيَةُ الإِسْلَامِيَّةُ وَبِنَاءُ الأَخْلَاقِ', 'Pendidikan Karakter & Al-Mamnu\' minash Sharf (الْمَمْنُوْعُ مِنَ الصَّرْفِ)', $ar9, $indo9, '1. PILIHAN GANDA', 'مَاذَا قَالَ الإِمَامُ الغَزَالِيُّ عَنِ العِلْمِ الَّذِي لَا يُصَاحِبُهُ عَمَلٌ صَالِحٌ؟ (Apakah perkataan Imam Al-Ghazali tentang ilmu tanpa amal?)'),
            'opsi_a' => 'أَنَّهُ شَرَفٌ عَظِيْمٌ لِصَاحِبِهِ',
            'opsi_b' => 'أَنَّ العِلْمَ بِلَا عَمَلٍ جُنُوْنٌ',
            'opsi_c' => 'أَنَّهُ يَكْفِي لِدُخُوْلِ الجَنَّةِ',
            'opsi_d' => 'أَنَّهُ يُورِثُ الغِنَى المَالِيَّ فَوْرًا',
            'opsi_e' => 'أَنَّهُ لَا عِلَاقَةَ بَيْنَهُمَا',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَنَّهُ شَرَفٌ عَظِيْمٌ لِصَاحِبِهِ',
                'B' => 'أَنَّ العِلْمَ بِلَا عَمَلٍ جُنُوْنٌ',
                'C' => 'أَنَّهُ يَكْفِي لِدُخُوْلِ الجَنَّةِ',
                'D' => 'أَنَّهُ يُورِثُ الغِنَى المَالِيَّ فَوْرًا',
                'E' => 'أَنَّهُ لَا عِلَاقَةَ بَيْنَهُمَا'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'التَّرْبِيَةُ الإِسْلَامِيَّةُ وَبِنَاءُ الأَخْلَاقِ', 'Pendidikan Karakter & Al-Mamnu\' minash Sharf (الْمَمْنُوْعُ مِنَ الصَّرْفِ)', $ar9, $indo9, '2. PG KOMPLEKS (CENTANG)', 'Isim-isim manakah di bawah ini yang tergolong <strong>Al-Mamnu\' minash Sharf (Tidak boleh bertanwin)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'عُثْمَانُ / رَمَضَانُ (Nama berakhiran alif dan nun tambahan)',
            'opsi_b' => 'مَسَاجِدُ / مَصَابِيْحُ (Shighat Muntahal Jumu\')',
            'opsi_c' => 'فَاطِمَةُ / عَائِشَةُ (Nama wanita Muannats)',
            'opsi_d' => 'مُحَمَّدٌ / زَيْدٌ (Nama laki-laki bertanwin biasa)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'عُثْمَانُ / رَمَضَانُ (Nama berakhiran alif dan nun tambahan)', 'is_kunci' => true],
                    ['teks' => 'مَسَاجِدُ / مَصَابِيْحُ (Shighat Muntahal Jumu\')', 'is_kunci' => true],
                    ['teks' => 'فَاطِمَةُ / عَائِشَةُ (Nama wanita Muannats)', 'is_kunci' => true],
                    ['teks' => 'مُحَمَّدٌ / زَيْدٌ (Nama laki-laki bertanwin biasa)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'التَّرْبِيَةُ الإِسْلَامِيَّةُ وَبِنَاءُ الأَخْلَاقِ', 'Pendidikan Karakter & Al-Mamnu\' minash Sharf (الْمَمْنُوْعُ مِنَ الصَّرْفِ)', $ar9, $indo9, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari tanda I\'rab Al-Mamnu\' minash Sharf berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Al-Mamnu\' minash Sharf ketika berkedudukan Majrur (didahului huruf jar) bertanda akhir Fathah pengganti Kasrah (contoh: صَلَّيْتُ فِي مَسَاجِدَ).', 'kunci' => 'Benar'],
                    ['teks' => 'Al-Mamnu\' minash Sharf boleh menerima tanwin apabila dibaca di akhir kalimat.', 'kunci' => 'Salah'],
                    ['teks' => 'Jika Al-Mamnu\' minash Sharf kemasukan Alif Lam (ال) atau berkedudukan sebagai Mudhaf, maka tanda majrurnya kembali menjadi Kasrah.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'التَّرْبِيَةُ الإِسْلَامِيَّةُ وَبِنَاءُ الأَخْلَاقِ', 'Pendidikan Karakter & Al-Mamnu\' minash Sharf (الْمَمْنُوْعُ مِنَ الصَّرْفِ)', $ar9, $indo9, '4. MENJODOHKAN', 'Pasangkan kata yang tercegah dari tanwin dengan illat penyebabnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'مَسَاجِدُ وَمَعَابِدُ', 'respon' => 'Shighat Muntahal Jumu\' (Wazan Mafā\'il)'],
                ['premis' => 'أَحْمَدُ وَيَزِيْدُ', 'respon' => 'Nama orang yang mengikuti wazan kata kerja (Fi\'il)'],
                ['premis' => 'عُثْمَانُ وَسَلْمَانُ', 'respon' => 'Nama orang berakhiran Alif dan Nun tambahan'],
                ['premis' => 'إِبْرَاهِيْمُ وَيُوْسُفُ', 'respon' => 'Nama orang non-Arab (\'Ajam) lebih dari 3 huruf']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'مَسَاجِدُ وَمَعَابِدُ', 'respon' => 'Shighat Muntahal Jumu\' (Wazan Mafā\'il)'],
                    ['premis' => 'أَحْمَدُ وَيَزِيْدُ', 'respon' => 'Nama orang yang mengikuti wazan kata kerja (Fi\'il)'],
                    ['premis' => 'عُثْمَانُ وَسَلْمَانُ', 'respon' => 'Nama orang berakhiran Alif dan Nun tambahan'],
                    ['premis' => 'إِبْرَاهِيْمُ وَيُوْسُفُ', 'respon' => 'Nama orang non-Arab (\'Ajam) lebih dari 3 huruf']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'التَّرْبِيَةُ الإِسْلَامِيَّةُ وَبِنَاءُ الأَخْلَاقِ', 'Pendidikan Karakter & Al-Mamnu\' minash Sharf (الْمَمْنُوْعُ مِنَ الصَّرْفِ)', $ar9, $indo9, '5. ISIAN SINGKAT', 'Tanda I\'rab Jar (Khafadh) pada isim yang tergolong Al-Mamnu\' minash Sharf ketika tidak memiliki alif lam dan tidak dimudhafkan adalah menggunakan harakat...'),
            'kunci_jawaban' => 'Fathah',
            'konten_tambahan' => ['rubrik' => 'Fathah (atau الفَتْحَةُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): الأَمَانَةُ المِهْنِيَّةُ (Integritas & Balaghah)
        // ══════════════════════════════════════════════════════════════════
        $ar10 = 'تُعْتَبَرُ الأَمَانَةُ وَالإِخْلَاصُ فِي العَمَلِ مِنْ أَهَمِّ القِيَمِ الإِسْلَامِيَّةِ الَّتِي تَقْضِي عَلَى الفَسَادِ وَالرِّشْوَةِ فِي الحَيَاةِ المُعَاصِرَةِ.<br>
قَالَ رَسُوْلُ اللهِ ﷺ: "لَعَنَ اللهُ الرَّاشِيَ وَالمُرْتَشِيَ". الرِّشْوَةُ جَرِيْمَةٌ تُفْسِدُ الذِّمَمَ وَتَهْدِمُ العَدَالَةَ فِي المُجْتَمَعِ.<br>
يَتَمَيَّزُ الأُسْلُوْبُ القُرْآنِيُّ بِالبَلَاغَةِ الرَّفِيْعَةِ، حَيْثُ يَسْتَخْدِمُ <u>التَّشْبِيْهَ وَالمَجَازَ وَالطِّبَاقَ</u>، مِثْلَ قَوْلِهِ تَعَالَى: {وَتَحْسَبُهُمْ أَيْقَاظًا وَهُمْ رُقُوْدٌ}، وَقَوْلِهِ: {قُلْ هَلْ يَسْتَوِي الأَعْمَى وَالبَصِيْرُ}.<br>
إِنَّ التَّمَسُّكَ بِالأَمَانَةِ المِهْنِيَّةِ يُنِيْرُ ضَمِيْرَ الإِنْسَانِ، وَيَجْعَلُهُ قُدْوَةً صَالِحَةً يُشَارُ إِلَيْهَا بِالبَنَانِ.';
        $indo10 = 'Teks mengenai nilai integritas profesi dan pencegahan suap/korupsi (Risywah), serta pengenalan dasar keindahan sastra Balaghah Al-Qur\'an (Tasybih, Majaz, dan Thibaq berlawanan kata).';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الأَمَانَةُ المِهْنِيَّةُ وَمُحَارَبَةُ الفَسَادِ', 'Integritas Moral & Seni Balaghah (البَلَاغَةُ وَالطِّبَاقُ)', $ar10, $indo10, '1. PILIHAN GANDA', 'مَا حُكْمُ الرِّشْوَةِ (السُّحْتِ) فِي الإِسْلَامِ كَمَا وَرَدَ فِي الحَدِيْثِ النَّبَوِيِّ الشَّرِيْفِ؟ (Apakah hukum suap menyuap menurut hadits Nabi SAW?)'),
            'opsi_a' => 'مُبَاحَةٌ فِي جَمِيْعِ الأَحْوَالِ',
            'opsi_b' => 'مَلْعُوْنٌ صَاحِبُهَا (الرَّاشِي وَالمُرْتَشِي) وَمُحَرَّمَةٌ شَرْعًا',
            'opsi_c' => 'مُسْتَحَبَّةٌ لِتَسْهِيلِ الأُمُوْرِ',
            'opsi_d' => 'مَكْرُوْهَةٌ كَرَاهَةً تَنْزِيْهِيَّةً فَقَطْ',
            'opsi_e' => 'جَائِزَةٌ عِنْدَ الضَّرُوْرَةِ المَالِيَّةِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'مُبَاحَةٌ فِي جَمِيْعِ الأَحْوَالِ',
                'B' => 'مَلْعُوْنٌ صَاحِبُهَا (الرَّاشِي وَالمُرْتَشِي) وَمُحَرَّمَةٌ شَرْعًا',
                'C' => 'مُسْتَحَبَّةٌ لِتَسْهِيلِ الأُمُوْرِ',
                'D' => 'مَكْرُوْهَةٌ كَرَاهَةً تَنْزِيْهِيَّةً فَقَطْ',
                'E' => 'جَائِزَةٌ عِنْدَ الضَّرُوْرَةِ المَالِيَّةِ'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الأَمَانَةُ المِهْنِيَّةُ وَمُحَارَبَةُ الفَسَادِ', 'Integritas Moral & Seni Balaghah (البَلَاغَةُ وَالطِّبَاقُ)', $ar10, $indo10, '2. PG KOMPLEKS (CENTANG)', 'Cabang keilmuan manakah di bawah ini yang dipelajari dalam <strong>Ilmu Balaghah (Sastra Bahasa Arab)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Ilmu Ma\'ani (mengkaji kesesuaian perkataan dengan situasi dan kondisi mukhatab).',
            'opsi_b' => 'Ilmu Bayan (mengkaji cara penyampaian makna melalui tasybih, majaz, dan kinayah).',
            'opsi_c' => 'Ilmu Badi\' (mengkaji keindahan seni kata seperti thibaq, jinas, dan saja\').',
            'opsi_d' => 'Ilmu Geologi Pertambangan.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Ilmu Ma\'ani (mengkaji kesesuaian perkataan dengan situasi dan kondisi mukhatab).', 'is_kunci' => true],
                    ['teks' => 'Ilmu Bayan (mengkaji cara penyampaian makna melalui tasybih, majaz, dan kinayah).', 'is_kunci' => true],
                    ['teks' => 'Ilmu Badi\' (mengkaji keindahan seni kata seperti thibaq, jinas, dan saja\').', 'is_kunci' => true],
                    ['teks' => 'Ilmu Geologi Pertambangan.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الأَمَانَةُ المِهْنِيَّةُ وَمُحَارَبَةُ الفَسَادِ', 'Integritas Moral & Seni Balaghah (البَلَاغَةُ وَالطِّبَاقُ)', $ar10, $indo10, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah ilmu Badi\' (Thibaq) berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Thibaq adalah berkumpulnya dua kata yang saling berlawanan makna (antonim) dalam satu kalimat.', 'kunci' => 'Benar'],
                    ['teks' => 'Ayat {وَتَحْسَبُهُمْ أَيْقَاظًا وَهُمْ رُقُوْدٌ} tidak mengandung unsur thibaq.', 'kunci' => 'Salah'],
                    ['teks' => 'Kata "أَيْقَاظًا" (terjaga) dan "رُقُوْدٌ" (tertidur) merupakan contoh keindahan uslub Thibaq Ijab.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الأَمَانَةُ المِهْنِيَّةُ وَمُحَارَبَةُ الفَسَادِ', 'Integritas Moral & Seni Balaghah (البَلَاغَةُ وَالطِّبَاقُ)', $ar10, $indo10, '4. MENJODOHKAN', 'Pasangkan pasangan kata berlawanan (Thibaq) dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'النُّوْرُ &times; الظُّلُمَاتُ', 'respon' => 'Cahaya Terang &times; Kegelapan'],
                ['premis' => 'الحَقُّ &times; البَاطِلُ', 'respon' => 'Kebenaran &times; Kebatilan'],
                ['premis' => 'الأَعْمَى &times; البَصِيْرُ', 'respon' => 'Orang Buta &times; Orang Melihat'],
                ['premis' => 'الحَيَاةُ &times; المَوْتُ', 'respon' => 'Kehidupan &times; Kematian']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'النُّوْرُ &times; الظُّلُمَاتُ', 'respon' => 'Cahaya Terang &times; Kegelapan'],
                    ['premis' => 'الحَقُّ &times; البَاطِلُ', 'respon' => 'Kebenaran &times; Kebatilan'],
                    ['premis' => 'الأَعْمَى &times; البَصِيْرُ', 'respon' => 'Orang Buta &times; Orang Melihat'],
                    ['premis' => 'الحَيَاةُ &times; المَوْتُ', 'respon' => 'Kehidupan &times; Kematian']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الأَمَانَةُ المِهْنِيَّةُ وَمُحَارَبَةُ الفَسَادِ', 'Integritas Moral & Seni Balaghah (البَلَاغَةُ وَالطِّبَاقُ)', $ar10, $indo10, '5. ISIAN SINGKAT', 'Keindahan seni sastra Arab (ilmu Badi\') berupa penyebutan dua kata yang saling berlawanan makna (antonim) dalam satu susunan kalimat dinamakan uslub...'),
            'kunci_jawaban' => 'Thibaq',
            'konten_tambahan' => ['rubrik' => 'Thibaq (atau الطِّبَاقُ / Ath-Thibāq)']
        ];

        return $list;
    }
}
