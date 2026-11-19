<?php

namespace App\Services\Datasets;

use App\Services\CbtBiologiArabGenerator;

class ArabKelasXI {

    public static function getQuestions() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): التَّسَوُّقُ فِي السُّوْقِ (Berbelanja di Pasar)
        // ══════════════════════════════════════════════════════════════════
        $ar1 = 'يَذْهَبُ النَّاسُ لِلتَّسَوُّقِ إِمَّا إِلَى <u>السُّوْقِ التَّقْلِيْدِيِّ</u> أَوْ إِلَى <u>المَرْكَزِ التِّجَارِيِّ الحَدِيْثِ (السُّوبَر مَارْكِت)</u>.<br>
فِي السُّوْقِ التَّقْلِيْدِيِّ، تَكُوْنُ البَضَائِعُ رَخِيْصَةَ الثَّمَنِ، وَيَسْتَطِيْعُ المُشْتَرِي أَنْ <u>يُمَاكِسَ (يُفَاوِضَ فِي السِّعْرِ)</u> مَعَ البَائِعِ حَتَّى يَصِلَا إِلَى اتِّفَاقٍ يُرْضِي الطَّرَفَيْنِ.<br>
أَمَّا فِي المَرْكَزِ التِّجَارِيِّ، فَتَكُوْنُ الأَسْعَارُ ثَابِتَةً لَا مُمَاكَسَةَ فِيْهَا، وَتَكُوْنُ الأَمَاكِنُ مُكَيَّفَةً وَمُنَظَّمَةً تَنْظِيْمًا جَيِّدًا، حَيْثُ يَخْتَارُ المُشْتَرِي حَاجَاتِهِ بِنَفْسِهِ ثُمَّ يَدْفَعُ الحِسَابَ عِنْدَ أَمِيْنِ الصُّنْدُوْقِ (الكِيرِ).<br>
يُفَضِّلُ كَثِيْرٌ مِنَ النَّاسِ شِرَاءَ الخَضْرَاوَاتِ وَالفَوَاكِهِ الطَّازَجَةِ مِنَ السُّوْقِ التَّقْلِيْدِيِّ لِدَعْمِ التُّجَّارِ الصِّغَارِ.';
        $indo1 = 'Teks perbandingan mengenai dinamika berbelanja di pasar tradisional (tawar menawar harga, produk segar, mendukung pedagang kecil) dan supermarket modern (harga pas, kenyamanan pendingin ruangan).';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّسَوُّقُ فِي السُّوْقِ التَّقْلِيْدِيِّ وَالمَرْكَزِ التِّجَارِيِّ', 'Budaya Berbelanja & Fi\'il Mudhari\' (الفِعْلُ الْمُضَارِعُ)', $ar1, $indo1, '1. PILIHAN GANDA', 'مَا المِيْزَةُ الرَّئِيْسِيَّةُ لِلتَّسَوُّقِ فِي السُّوْقِ التَّقْلِيْدِيِّ وِفْقًا لِلنَّصِّ؟ (Apakah keunggulan utama belanja di pasar tradisional menurut teks?)'),
            'opsi_a' => 'أَنَّ الأَسْعَارَ مُرْتَفِعَةٌ جِدًّا',
            'opsi_b' => 'إِمْكَانِيَّةُ المُمَاكَسَةِ (المُفَاوَضَةِ فِي السِّعْرِ) وَرُخْصُ البَضَائِعِ',
            'opsi_c' => 'وُجُوْدُ المُكَيِّفَاتِ فِي كُلِّ مَكَانٍ',
            'opsi_d' => 'الدَّفْعُ بِالبِطَاقَاتِ الإِلِكْتِرُونِيَّةِ فَقَطْ',
            'opsi_e' => 'عَدَمُ وُجُوْدِ البَائِعِيْنَ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَنَّ الأَسْعَارَ مُرْتَفِعَةٌ جِدًّا',
                'B' => 'إِمْكَانِيَّةُ المُمَاكَسَةِ (المُفَاوَضَةِ فِي السِّعْرِ) وَرُخْصُ البَضَائِعِ',
                'C' => 'وُجُوْدُ المُكَيِّفَاتِ فِي كُلِّ مَكَانٍ',
                'D' => 'الدَّفْعُ بِالبِطَاقَاتِ الإِلِكْتِرُونِيَّةِ فَقَطْ',
                'E' => 'عَدَمُ وُجُوْدِ البَائِعِيْنَ'
            ]]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّسَوُّقُ فِي السُّوْقِ التَّقْلِيْدِيِّ وَالمَرْكَزِ التِّجَارِيِّ', 'Budaya Berbelanja & Fi\'il Mudhari\' (الفِعْلُ الْمُضَارِعُ)', $ar1, $indo1, '2. PG KOMPLEKS (CENTANG)', 'Pilihlah huruf-huruf yang termasuk <strong>Huruf Mudhara\'ah (أَنَيْتُ)</strong> yang menandai permulaan Fi\'il Mudhari\'! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Huruf Hamzah (أ)',
            'opsi_b' => 'Huruf Nun (ن)',
            'opsi_c' => 'Huruf Ya (ي) dan Ta (ت)',
            'opsi_d' => 'Huruf Mim (م) dan Lam (ل)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Huruf Hamzah (أ)', 'is_kunci' => true],
                    ['teks' => 'Huruf Nun (ن)', 'is_kunci' => true],
                    ['teks' => 'Huruf Ya (ي) dan Ta (ت)', 'is_kunci' => true],
                    ['teks' => 'Huruf Mim (م) dan Lam (ل)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّسَوُّقُ فِي السُّوْقِ التَّقْلِيْدِيِّ وَالمَرْكَزِ التِّجَارِيِّ', 'Budaya Berbelanja & Fi\'il Mudhari\' (الفِعْلُ الْمُضَارِعُ)', $ar1, $indo1, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah tasrif Fi\'il Mudhari\' berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Fi\'il Mudhari\' "يَذْهَبُ" jika pelakunya dhomir "أَنْتُمْ" (kalian laki-laki) berubah menjadi "تَذْهَبُوْنَ".', 'kunci' => 'Benar'],
                    ['teks' => 'Fi\'il Mudhari\' untuk dhomir "هِيَ" (dia perempuan) menggunakan awalan huruf ya (ي).', 'kunci' => 'Salah'],
                    ['teks' => 'Fi\'il Mudhari\' untuk dhomir "أَنَا" diawali huruf hamzah, contohnya "أَشْتَرِي" (saya membeli).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّسَوُّقُ فِي السُّوْقِ التَّقْلِيْدِيِّ وَالمَرْكَزِ التِّجَارِيِّ', 'Budaya Berbelanja & Fi\'il Mudhari\' (الفِعْلُ الْمُضَارِعُ)', $ar1, $indo1, '4. MENJODOHKAN', 'Pasangkan istilah perniagaan bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'السِّعْرُ / الثَّمَنُ', 'respon' => 'Harga barang'],
                ['premis' => 'البَضَائِعُ', 'respon' => 'Barang-barang dagangan'],
                ['premis' => 'المُمَاكَسَةُ', 'respon' => 'Tawar-menawar harga'],
                ['premis' => 'أَمِيْنُ الصُّنْدُوْقِ', 'respon' => 'Kasir pembayaran']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'السِّعْرُ / الثَّمَنُ', 'respon' => 'Harga barang'],
                    ['premis' => 'البَضَائِعُ', 'respon' => 'Barang-barang dagangan'],
                    ['premis' => 'المُمَاكَسَةُ', 'respon' => 'Tawar-menawar harga'],
                    ['premis' => 'أَمِيْنُ الصُّنْدُوْقِ', 'respon' => 'Kasir pembayaran']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(1, '1 - 5', 'التَّسَوُّقُ فِي السُّوْقِ التَّقْلِيْدِيِّ وَالمَرْكَزِ التِّجَارِيِّ', 'Budaya Berbelanja & Fi\'il Mudhari\' (الفِعْلُ الْمُضَارِعُ)', $ar1, $indo1, '5. ISIAN SINGKAT', 'Kata kerja dalam bahasa Arab yang menunjukkan perbuatan yang sedang berlangsung saat ini atau masa yang akan datang disebut Fi\'il...'),
            'kunci_jawaban' => 'Mudhari\'',
            'konten_tambahan' => ['rubrik' => 'Mudhari\' (atau الفِعْلُ الْمُضَارِعُ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): الصِّحَّةُ وَالمُسْتَشْفَى (Kesehatan & RS)
        // ══════════════════════════════════════════════════════════════════
        $ar2 = 'الصِّحَّةُ نِعْمَةٌ عَظِيْمَةٌ مِنْ نِعَمِ اللهِ تَعَالَى عَلَى عِبَادِهِ. يَقُوْلُ المَثَلُ العَرَبِيُّ: "الصِّحَّةُ تَاجٌ عَلَى رُؤُوْسِ الأَصِحَّاءِ لَا يَرَاهُ إِلَّا المَرْضَى".<br>
عِنْدَمَا شَعَرَ طَارِقٌ بِأَلَمٍ شَدِيْدٍ فِي بَطْنِهِ، <u>ذَهَبَ طَارِقٌ إِلَى المُسْتَشْفَى</u>. قَابَلَ طَارِقٌ الطَّبِيْبَ فِي غُرْفَةِ الفَحْصِ.<br>
<u>فَحَصَ الطَّبِيْبُ المَرِيْضَ</u> بِالسَّمَّاعَةِ الطِّبِّيَّةِ وَقَاسَ ضَغْطَ دَمِهِ، ثُمَّ <u>كَتَبَ الطَّبِيْبُ الوَصْفَةَ الطِّبِّيَّةَ (الرُّوْشِيْتَةَ)</u>.<br>
اشْتَرَى طَارِقٌ الدَّوَاءَ مِنَ الصَّيْدَلِيَّةِ، وَنَصَحَهُ الطَّبِيْبُ أَنْ يَتَنَاوَلَ الطَّعَامَ الصِّحِّيَّ وَيَشْرَبَ المَاءَ النَّقِيَّ وَيَجْتَنِبَ المَأْكُوْلَاتِ الحَارَّةَ.';
        $indo2 = 'Teks mengenai urgensi menjaga kesehatan jasmani, pemeriksaan dokter menggunakan stetoskop di rumah sakit, resep obat di apotek, serta kaidah Fa\'il (Subjek Pelaku).';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الصِّحَّةُ وَالرِّعَايَةُ الطِّبِّيَّةُ', 'Pelayanan Medis & Kaidah Fa\'il (الْفَاعِلُ)', $ar2, $indo2, '1. PILIHAN GANDA', 'مَاذَا قَاسَ الطَّبِيْبُ لِطَارِقٍ أَثْنَاءَ الفَحْصِ الطِّبِّيِّ؟ (Apakah yang diukur oleh dokter saat pemeriksaan medis Thariq?)'),
            'opsi_a' => 'طُوْلَ قَامَتِهِ',
            'opsi_b' => 'ضَغْطَ دَمِهِ بِالسَّمَّاعَةِ الطِّبِّيَّةِ',
            'opsi_c' => 'وَزْنَ جِسْمِهِ فَقَطْ',
            'opsi_d' => 'نَظَرَ عَيْنَيْهِ',
            'opsi_e' => 'سُرْعَةَ مَشْيِهِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'طُوْلَ قَامَتِهِ',
                'B' => 'ضَغْطَ دَمِهِ بِالسَّمَّاعَةِ الطِّبِّيَّةِ',
                'C' => 'وَزْنَ جِسْمِهِ فَقَطْ',
                'D' => 'نَظَرَ عَيْنَيْهِ',
                'E' => 'سُرْعَةَ مَشْيِهِ'
            ]]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الصِّحَّةُ وَالرِّعَايَةُ الطِّبِّيَّةُ', 'Pelayanan Medis & Kaidah Fa\'il (الْفَاعِلُ)', $ar2, $indo2, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai kedudukan dan hukum <strong>Fa\'il (الْفَاعِلُ)</strong> dalam tata bahasa Arab? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Fa\'il adalah isim marfu\' yang terletak setelah kata kerja (Fi\'il Ma\'lum) dan menunjukkan pelaku perbuatan.',
            'opsi_b' => 'Tanda I\'rab rofa\' pada Fa\'il yang berupa Isim Mufrad adalah harakat Dhammah.',
            'opsi_c' => 'Tanda I\'rab rofa\' pada Fa\'il yang berupa Jamak Mudzakkar Salim adalah huruf Wawu (ـُوْنَ).',
            'opsi_d' => 'Fa\'il selalu berharakat akhir Fathah pada setiap keadaan kalimat.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Fa\'il adalah isim marfu\' yang terletak setelah kata kerja (Fi\'il Ma\'lum) dan menunjukkan pelaku perbuatan.', 'is_kunci' => true],
                    ['teks' => 'Tanda I\'rab rofa\' pada Fa\'il yang berupa Isim Mufrad adalah harakat Dhammah.', 'is_kunci' => true],
                    ['teks' => 'Tanda I\'rab rofa\' pada Fa\'il yang berupa Jamak Mudzakkar Salim adalah huruf Wawu (ـُوْنَ).', 'is_kunci' => true],
                    ['teks' => 'Fa\'il selalu berharakat akhir Fathah pada setiap keadaan kalimat.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الصِّحَّةُ وَالرِّعَايَةُ الطِّبِّيَّةُ', 'Pelayanan Medis & Kaidah Fa\'il (الْفَاعِلُ)', $ar2, $indo2, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah kesesuaian Fi\'il dan Fa\'il berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Jika Fa\'il berjenis Muannats (misal: فَاطِمَةُ), maka kata kerjanya harus menggunakan tanda ta ta\'nits (misal: كَتَبَتْ فَاطِمَةُ).', 'kunci' => 'Benar'],
                    ['teks' => 'Jika Fa\'il-nya berbentuk jamak isim zhahir di belakang kata kerja, maka fi\'il di depan harus ikut diubah menjadi jamak.', 'kunci' => 'Salah'],
                    ['teks' => 'Fi\'il di awal kalimat selalu berkedudukan tunggal (mufrad) meskipun Fa\'il isim zhahir di depannya berbentuk jamak.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الصِّحَّةُ وَالرِّعَايَةُ الطِّبِّيَّةُ', 'Pelayanan Medis & Kaidah Fa\'il (الْفَاعِلُ)', $ar2, $indo2, '4. MENJODOHKAN', 'Pasangkan istilah medis dalam bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'الوَصْفَةُ الطِّبِّيَّةُ', 'respon' => 'Resep Obat Dokter'],
                ['premis' => 'الصَّيْدَلِيَّةُ', 'respon' => 'Apotek Tempat Penjualan Obat'],
                ['premis' => 'السَّمَّاعَةُ الطِّبِّيَّةُ', 'respon' => 'Stetoskop Pemeriksaan'],
                ['premis' => 'ضَغْطُ الدَّمِ', 'respon' => 'Tekanan Darah']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'الوَصْفَةُ الطِّبِّيَّةُ', 'respon' => 'Resep Obat Dokter'],
                    ['premis' => 'الصَّيْدَلِيَّةُ', 'respon' => 'Apotek Tempat Penjualan Obat'],
                    ['premis' => 'السَّمَّاعَةُ الطِّبِّيَّةُ', 'respon' => 'Stetoskop Pemeriksaan'],
                    ['premis' => 'ضَغْطُ الدَّمِ', 'respon' => 'Tekanan Darah']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(2, '6 - 10', 'الصِّحَّةُ وَالرِّعَايَةُ الطِّبِّيَّةُ', 'Pelayanan Medis & Kaidah Fa\'il (الْفَاعِلُ)', $ar2, $indo2, '5. ISIAN SINGKAT', 'Tentukan kata yang berkedudukan sebagai Fa\'il pada kalimat berikut: "فَحَصَ الطَّبِيْبُ المَرِيْضَ" (Dokter itu telah memeriksa orang yang sakit). Kata yang menjadi Fa\'il adalah...'),
            'kunci_jawaban' => 'الطَّبِيْبُ',
            'konten_tambahan' => ['rubrik' => 'الطَّبِيْبُ (atau At-Thabibu / الطبيب)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 (SOAL 11 - 15): السَّفَرُ وَالسِّيَاحَةُ (Perjalanan Religi)
        // ══════════════════════════════════════════════════════════════════
        $ar3 = 'سَافَرَ الأُسْتَاذُ مَنْصُوْرٌ مَعَ أُسْرَتِهِ إِلَى المَمْلَكَةِ العَرَبِيَّةِ السُّعُوْدِيَّةِ لِأَدَاءِ مَنَاسِكِ العُمْرَةِ.<br>
رَكِبَ المُسَافِرُوْنَ <u>الطَّائِرَةَ</u> مِنْ مَطَارِ جَاكَرْتَا، وَوَصَلُوْا إِلَى مَطَارِ جِدَّةَ بَعْدَ تِسْعِ سَاعَاتٍ.<br>
فِي مَكَّةَ المُكَرَّمَةِ، <u>طَافَ المُعْتَمِرُوْنَ حَوْلَ الكَعْبَةِ المُشَرَّفَةِ</u> سَبْعَةَ أَشْوَاطٍ، ثُمَّ <u>سَعَوْا بَيْنَ الصَّفَا وَالمَرْوَةِ</u>، وَ<u>حَلَقُوْا رُؤُوْسَهُمْ</u> تَحَلُّلًا.<br>
بَعْدَ ذَلِكَ، زَارُوْا المَدِيْنَةَ المُنَوَّرَةَ وَصَلَّوْا فِي المَسْجِدِ النَّبَوِيِّ الشَّرِيْفِ وَسَلَّمُوْا عَلَى رَسُوْلِ اللهِ ﷺ فِي الرَّوْضَةِ الشَّرِيْفَةِ.';
        $indo3 = 'Teks mengenai perjalanan ibadah umrah ke Tanah Suci (Makkah dan Madinah), rukun tawaf, sa\'i, tahallul, ziarah ke Masjid Nabawi, serta penerapan kaidah Maf\'ul Bih (Objek Penderita).';

        // No 11: PG
        $list[] = [
            'nomor_urut' => 11,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'السَّفَرُ وَالسِّيَاحَةُ الدِّيْنِيَّةُ', 'Perjalanan Ibadah & Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)', $ar3, $indo3, '1. PILIHAN GANDA', 'كَمْ شَوْطًا طَافَ المُعْتَمِرُوْنَ حَوْلَ الكَعْبَةِ المُشَرَّفَةِ؟ (Berapa putarankah para jamaah thawaf mengelilingi Ka\'bah?)'),
            'opsi_a' => 'ثَلَاثَةَ أَشْوَاطٍ',
            'opsi_b' => 'خَمْسَةَ أَشْوَاطٍ',
            'opsi_c' => 'سَبْعَةَ أَشْوَاطٍ',
            'opsi_d' => 'تِسْعَةَ أَشْوَاطٍ',
            'opsi_e' => 'عَشَرَةَ أَشْوَاطٍ',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => [
                'A' => 'ثَلَاثَةَ أَشْوَاطٍ',
                'B' => 'خَمْسَةَ أَشْوَاطٍ',
                'C' => 'سَبْعَةَ أَشْوَاطٍ',
                'D' => 'تِسْعَةَ أَشْوَاطٍ',
                'E' => 'عَشَرَةَ أَشْوَاطٍ'
            ]]
        ];

        // No 12: PGK
        $list[] = [
            'nomor_urut' => 12,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'السَّفَرُ وَالسِّيَاحَةُ الدِّيْنِيَّةُ', 'Perjalanan Ibadah & Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)', $ar3, $indo3, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai kaidah <strong>Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Maf\'ul Bih adalah isim yang dikenai pekerjaan (objek penderita) oleh si pelaku (Fa\'il).',
            'opsi_b' => 'Hukum I\'rab asal dari Maf\'ul Bih selalu Manshub (berharakat fathah untuk isim mufrad).',
            'opsi_c' => 'Tanda nashob Maf\'ul Bih untuk Jamak Mudzakkar Salim dan Mutsanna adalah huruf Ya (ـيْنَ / ـَيْنِ).',
            'opsi_d' => 'Maf\'ul Bih selalu berharakat kasrah pada setiap jenis kata.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Maf\'ul Bih adalah isim yang dikenai pekerjaan (objek penderita) oleh si pelaku (Fa\'il).', 'is_kunci' => true],
                    ['teks' => 'Hukum I\'rab asal dari Maf\'ul Bih selalu Manshub (berharakat fathah untuk isim mufrad).', 'is_kunci' => true],
                    ['teks' => 'Tanda nashob Maf\'ul Bih untuk Jamak Mudzakkar Salim dan Mutsanna adalah huruf Ya (ـيْنَ / ـَيْنِ).', 'is_kunci' => true],
                    ['teks' => 'Maf\'ul Bih selalu berharakat kasrah pada setiap jenis kata.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 13: B/S
        $list[] = [
            'nomor_urut' => 13,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'السَّفَرُ وَالسِّيَاحَةُ الدِّيْنِيَّةُ', 'Perjalanan Ibadah & Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)', $ar3, $indo3, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari rukun ibadah umrah berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Sa\'i dalam ibadah umrah dilakukan dengan berlari-lari kecil antara bukit Shafa dan bukit Marwah sebanyak 7 kali.', 'kunci' => 'Benar'],
                    ['teks' => 'Wukuf di padang Arafah merupakan salah satu rukun wajib dalam ibadah umrah.', 'kunci' => 'Salah'],
                    ['teks' => 'Tahallul ditandai dengan mencukur atau memotong sebagian rambut kepala.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 14: Menjodohkan
        $list[] = [
            'nomor_urut' => 14,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'السَّفَرُ وَالسِّيَاحَةُ الدِّيْنِيَّةُ', 'Perjalanan Ibadah & Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)', $ar3, $indo3, '4. MENJODOHKAN', 'Pasangkan sarana transportasi perjalanan bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'الطَّائِرَةُ', 'respon' => 'Pesawat Terbang'],
                ['premis' => 'القِطَارُ السَّرِيْعُ', 'respon' => 'Kereta Api Cepat'],
                ['premis' => 'السَّفِيْنَةُ البَحْرِيَّةُ', 'respon' => 'Kapal Laut'],
                ['premis' => 'الحَافِلَةُ', 'respon' => 'Mobil Bus Penumpang']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'الطَّائِرَةُ', 'respon' => 'Pesawat Terbang'],
                    ['premis' => 'القِطَارُ السَّرِيْعُ', 'respon' => 'Kereta Api Cepat'],
                    ['premis' => 'السَّفِيْنَةُ البَحْرِيَّةُ', 'respon' => 'Kapal Laut'],
                    ['premis' => 'الحَافِلَةُ', 'respon' => 'Mobil Bus Penumpang']
                ]
            ]
        ];

        // No 15: Isian Singkat
        $list[] = [
            'nomor_urut' => 15,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(3, '11 - 15', 'السَّفَرُ وَالسِّيَاحَةُ الدِّيْنِيَّةُ', 'Perjalanan Ibadah & Maf\'ul Bih (الْمَفْعُوْلُ بِهِ)', $ar3, $indo3, '5. ISIAN SINGKAT', 'Tentukan kata yang menduduki posisi sebagai Maf\'ul Bih pada kalimat berikut: "رَكِبَ المُسَافِرُوْنَ الطَّائِرَةَ" (Para musafir itu menaiki pesawat terbang). Kata yang menjadi Maf\'ul Bih adalah...'),
            'kunci_jawaban' => 'الطَّائِرَةَ',
            'konten_tambahan' => ['rubrik' => 'الطَّائِرَةَ (atau At-Tho\'irota / الطائرة)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 4 (SOAL 16 - 20): التِّكْنُولُوجِيَا وَالاتِّصَالَاتُ (Teknologi)
        // ══════════════════════════════════════════════════════════════════
        $ar4 = 'أَصْبَحَتْ وَسَائِلُ الاتِّصَالِ الحَدِيْثَةُ وَشَبَكَةُ الإِنْتَرْنِت جُزْءًا لَا يَتَجَزَّأُ مِنْ حَيَاتِنَا اليَوْمِيَّةِ.<br>
يَسْتَخْدِمُ الطُّلَّابُ <u>الهَاتِفَ الذَّكِيَّ وَالحَاسُوْبَ</u> لِلبَحْثِ عَنِ المَعْلُوْمَاتِ النَّافِعَةِ وَمُتَابَعَةِ الدُّرُوْسِ عَبْرَ التَّعْلِيْمِ الإِلِكْتِرُونِيِّ (CBT).<br>
لِلتِّكْنُولُوجِيَا فَوَائِدُ كَثِيْرَةٌ: تُسَهِّلُ التَّوَاصُلَ بَيْنَ النَّاسِ <u>صَبَاحًا وَمَسَاءً</u>، وَتُقَرِّبُ البَعِيْدَ، وَتُوَفِّرُ الوَقْتَ وَالجُهْدَ.<br>
وَلَكِنْ إِذَا أَسَاءَ الإِنْسَانُ اسْتِخْدَامَهَا فَقَدْ تُضَيِّعُ الأَوْقَاتِ فِي الأَلْعَابِ غَيْرِ المُفِيْدَةِ. لِذَلِكَ، يَجِبُ عَلَى الطَّالِبِ أَنْ يَتَسَلَّحَ بِالوَعْيِ وَالحِكْمَةِ فِي اسْتِعْمَالِ التِّقْنِيَّةِ.';
        $indo4 = 'Teks mengenai dampak kemajuan teknologi komunikasi, internet pintar, pembelajaran online/CBT, efisiensi waktu, serta kaidah Zharaf Zaman dan Zharaf Makan (Maf\'ul Fih).';

        // No 16: PG
        $list[] = [
            'nomor_urut' => 16,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'التِّكْنُولُوجِيَا وَوَسَائِلُ الاتِّصَالِ', 'Teknologi Informasi & Maf\'ul Fih (الْمَفْعُوْلُ فِيْهِ / الظَّرْفُ)', $ar4, $indo4, '1. PILIHAN GANDA', 'كَيْفَ يَسْتَفِيْدُ الطُّلَّابُ مِنَ الهَاتِفِ الذَّكِيِّ وَالحَاسُوْبِ بِشَكْلٍ إِيْجَابِيٍّ؟ (Bagaimana siswa memanfaatkan ponsel pintar dan komputer secara positif?)'),
            'opsi_a' => 'لِإِضَاعَةِ الوَقْتِ فِي الأَلْعَابِ اللَّيْلِيَّةِ',
            'opsi_b' => 'لِلبَحْثِ عَنِ المَعْلُوْمَاتِ النَّافِعَةِ وَمُتَابَعَةِ الدُّرُوْسِ',
            'opsi_c' => 'لِإِهْمَالِ الوَاجِبَاتِ المَدْرَسِيَّةِ',
            'opsi_d' => 'لِلنَّوْمِ طَوَالَ النَّهَارِ',
            'opsi_e' => 'لِلتَّجَسُّسِ عَلَى الآخَرِيْنَ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'لِإِضَاعَةِ الوَقْتِ فِي الأَلْعَابِ اللَّيْلِيَّةِ',
                'B' => 'لِلبَحْثِ عَنِ المَعْلُوْمَاتِ النَّافِعَةِ وَمُتَابَعَةِ الدُّرُوْسِ',
                'C' => 'لِإِهْمَالِ الوَاجِبَاتِ المَدْرَسِيَّةِ',
                'D' => 'لِلنَّوْمِ طَوَالَ النَّهَارِ',
                'E' => 'لِلتَّجَسُّسِ عَلَى الآخَرِيْنَ'
            ]]
        ];

        // No 17: PGK
        $list[] = [
            'nomor_urut' => 17,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'التِّكْنُولُوجِيَا وَوَسَائِلُ الاتِّصَالِ', 'Teknologi Informasi & Maf\'ul Fih (الْمَفْعُوْلُ فِيْهِ / الظَّرْفُ)', $ar4, $indo4, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang tergolong <strong>Zharaf Zaman (Keterangan Waktu)</strong> yang berharakat manshub (fathah)? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'صَبَاحًا (Pada waktu pagi)',
            'opsi_b' => 'مَسَاءً (Pada waktu sore/malam)',
            'opsi_c' => 'لَيْلًا (Pada waktu malam)',
            'opsi_d' => 'فَوْقَ (Di atas)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'صَبَاحًا (Pada waktu pagi)', 'is_kunci' => true],
                    ['teks' => 'مَسَاءً (Pada waktu sore/malam)', 'is_kunci' => true],
                    ['teks' => 'لَيْلًا (Pada waktu malam)', 'is_kunci' => true],
                    ['teks' => 'فَوْقَ (Di atas)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 18: B/S
        $list[] = [
            'nomor_urut' => 18,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'التِّكْنُولُوجِيَا وَوَسَائِلُ الاتِّصَالِ', 'Teknologi Informasi & Maf\'ul Fih (الْمَفْعُوْلُ فِيْهِ / الظَّرْفُ)', $ar4, $indo4, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari kaidah Maf\'ul Fih berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Maf\'ul Fih adalah isim yang menerangkan tempat atau waktu terjadinya perbuatan dengan memuat makna "فِي" (di/pada).', 'kunci' => 'Benar'],
                    ['teks' => 'Zharaf Makan selalu berstatus I\'rab Rofa\' dengan tanda akhir Dhammah.', 'kunci' => 'Salah'],
                    ['teks' => 'Hukum I\'rab asal dari Zharaf Zaman dan Zharaf Makan adalah Manshub dengan fathah.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 19: Menjodohkan
        $list[] = [
            'nomor_urut' => 19,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'التِّكْنُولُوجِيَا وَوَسَائِلُ الاتِّصَالِ', 'Teknologi Informasi & Maf\'ul Fih (الْمَفْعُوْلُ فِيْهِ / الظَّرْفُ)', $ar4, $indo4, '4. MENJODOHKAN', 'Pasangkan istilah teknologi modern dalam bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'الهَاتِفُ الذَّكِيُّ', 'respon' => 'Ponsel Pintar (Smartphone)'],
                ['premis' => 'الحَاسُوْبُ المَحْمُوْلُ', 'respon' => 'Laptop / Komputer Jinjing'],
                ['premis' => 'شَبَكَةُ المَعْلُوْمَاتِ (الإِنْتَرْنِت)', 'respon' => 'Jaringan Internet'],
                ['premis' => 'البَرِيْدُ الإِلِكْتِرُونِيُّ', 'respon' => 'Surat Elektronik (Email)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'الهَاتِفُ الذَّكِيُّ', 'respon' => 'Ponsel Pintar (Smartphone)'],
                    ['premis' => 'الحَاسُوْبُ المَحْمُوْلُ', 'respon' => 'Laptop / Komputer Jinjing'],
                    ['premis' => 'شَبَكَةُ المَعْلُوْمَاتِ (الإِنْتَرْنِت)', 'respon' => 'Jaringan Internet'],
                    ['premis' => 'البَرِيْدُ الإِلِكْتِرُونِيُّ', 'respon' => 'Surat Elektronik (Email)']
                ]
            ]
        ];

        // No 20: Isian Singkat
        $list[] = [
            'nomor_urut' => 20,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(4, '16 - 20', 'التِّكْنُولُوجِيَا وَوَسَائِلُ الاتِّصَالِ', 'Teknologi Informasi & Maf\'ul Fih (الْمَفْعُوْلُ فِيْهِ / الظَّرْفُ)', $ar4, $indo4, '5. ISIAN SINGKAT', 'Isim manshub yang berfungsi menerangkan tempat atau waktu terjadinya perbuatan dinamakan Maf\'ul...'),
            'kunci_jawaban' => 'Fih',
            'konten_tambahan' => ['rubrik' => 'Fih (atau Maf\'ul Fih / الْمَفْعُوْلُ فِيْهِ / Zharaf)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 5 (SOAL 21 - 25): الأَدْيَانُ وَالتَّسَامُحُ (Tasamuh & Kaana)
        // ══════════════════════════════════════════════════════════════════
        $ar5 = 'يَدْعُو الإِسْلَامُ إِلَى التَّسَامُحِ وَالعَيْشِ المُشْتَرَكِ بَيْنَ أَبْنَاءِ الوَطَنِ الوَاحِدِ عَلَى اخْتِلَافِ أَدْيَانِهِمْ وَأَعْرَاقِهِمْ.<br>
<u>كَانَ النَّبِيُّ ﷺ قُدْوَةً حَسَنَةً</u> فِي مُعَامَلَةِ أَهْلِ الكِتَابِ، حَيْثُ كَتَبَ "وَثِيْقَةَ المَدِيْنَةِ" الَّتِي أَقَرَّتْ حُرِّيَّةَ العَقِيْدَةِ وَحِمَايَةَ الحُقُوْقِ.<br>
<u>أَصْبَحَ التَّسَامُحُ الدِّيْنِيُّ رَكِيْزَةً أَسَاسِيَّةً</u> فِي بُنْيَانِ الدَّوْلَةِ الإِنْدُوْنِيْسِيَّةِ المَبْنِيَّةِ عَلَى أُسُسِ "البَانْجَاسِيْلَا".<br>
إِنَّ الوَحْدَةَ الوَطَنِيَّةَ تَكُوْنُ قَوِيَّةً إِذَا احْتَرَمَ كُلُّ فَرْدٍ مُعْتَقَدَاتِ الآخَرِيْنَ وَتَعَاوَنُوْا عَلَى بِنَاءِ المُجْتَمَعِ.';
        $indo5 = 'Teks mengenai nilai moderasi beragama dan tasamuh (toleransi) dalam Islam, teladan Rasulullah dalam Piagam Madinah, serta kaidah Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا).';

        // No 21: PG
        $list[] = [
            'nomor_urut' => 21,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'التَّسَامُحُ فِي الإِسْلَامِ', 'Moderasi Beragama & Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)', $ar5, $indo5, '1. PILIHAN GANDA', 'مَا هِيَ الوَثِيْقَةُ التَّارِيْخِيَّةُ الَّتِي كَتَبَهَا النَّبِيُّ ﷺ لِتَنْظِيْمِ العَلَاقَاتِ فِي المَدِيْنَةِ؟ (Apakah piagam bersejarah yang disusun Rasulullah SAW?)'),
            'opsi_a' => 'وَثِيْقَةُ القُدْسِ',
            'opsi_b' => 'وَثِيْقَةُ المَدِيْنَةِ (Piagam Madinah)',
            'opsi_c' => 'صُلْحُ الحُدَيْبِيَةِ',
            'opsi_d' => 'حِلْفُ الفُضُوْلِ',
            'opsi_e' => 'خُطْبَةُ الوَدَاعِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'وَثِيْقَةُ القُدْسِ',
                'B' => 'وَثِيْقَةُ المَدِيْنَةِ (Piagam Madinah)',
                'C' => 'صُلْحُ الحُدَيْبِيَةِ',
                'D' => 'حِلْفُ الفُضُوْلِ',
                'E' => 'خُطْبَةُ الوَدَاعِ'
            ]]
        ];

        // No 22: PGK
        $list[] = [
            'nomor_urut' => 22,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'التَّسَامُحُ فِي الإِسْلَامِ', 'Moderasi Beragama & Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)', $ar5, $indo5, '2. PG KOMPLEKS (CENTANG)', 'Manakah kata-kata berikut yang tergolong dalam kelompok <strong>Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'كَانَ (Dahulu/adalah)',
            'opsi_b' => 'أَصْبَحَ (Menjadi pada waktu pagi)',
            'opsi_c' => 'لَيْسَ (Bukanlah/tidak)',
            'opsi_d' => 'إِنَّ (Sesungguhnya)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'كَانَ (Dahulu/adalah)', 'is_kunci' => true],
                    ['teks' => 'أَصْبَحَ (Menjadi pada waktu pagi)', 'is_kunci' => true],
                    ['teks' => 'لَيْسَ (Bukanlah/tidak)', 'is_kunci' => true],
                    ['teks' => 'إِنَّ (Sesungguhnya)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 23: B/S
        $list[] = [
            'nomor_urut' => 23,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'التَّسَامُحُ فِي الإِسْلَامِ', 'Moderasi Beragama & Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)', $ar5, $indo5, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari pengaruh fungsi Kaana terhadap I\'rab kalimat berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kaana wa akhwatuha masuk pada Jumlah Ismiyyah untuk merafa\'kan Isim Kaana dan menashobkan Khabar Kaana.', 'kunci' => 'Benar'],
                    ['teks' => 'Setelah kemasukan Kaana, Khabar Kaana tetap berstatus Marfu\' Dhammah.', 'kunci' => 'Salah'],
                    ['teks' => 'Contoh kalimat "كَانَ الجَوُّ بَارِدًا" benar karena Isim Kaana (الجَوُّ) marfu\' dan Khabar Kaana (بَارِدًا) manshub.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 24: Menjodohkan
        $list[] = [
            'nomor_urut' => 24,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'التَّسَامُحُ فِي الإِسْلَامِ', 'Moderasi Beragama & Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)', $ar5, $indo5, '4. MENJODOHKAN', 'Pasangkan saudara-saudara Kaana dengan makna gramatikanya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'أَصْبَحَ', 'respon' => 'Menunjukkan waktu pagi atau perubahan keadaan menjadi'],
                ['premis' => 'أَمْسَى', 'respon' => 'Menunjukkan waktu sore hari'],
                ['premis' => 'لَيْسَ', 'respon' => 'Menafikan (menyatakan bukan / tidak)'],
                ['premis' => 'مَا زَالَ', 'respon' => 'Menyatakan keadaan yang masih terus menerus berlangsung']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'أَصْبَحَ', 'respon' => 'Menunjukkan waktu pagi atau perubahan keadaan menjadi'],
                    ['premis' => 'أَمْسَى', 'respon' => 'Menunjukkan waktu sore hari'],
                    ['premis' => 'لَيْسَ', 'respon' => 'Menafikan (menyatakan bukan / tidak)'],
                    ['premis' => 'مَا زَالَ', 'respon' => 'Menyatakan keadaan yang masih terus menerus berlangsung']
                ]
            ]
        ];

        // No 25: Isian Singkat
        $list[] = [
            'nomor_urut' => 25,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(5, '21 - 25', 'التَّسَامُحُ فِي الإِسْلَامِ', 'Moderasi Beragama & Kaana wa Akhwatuha (كَانَ وَأَخَوَاتُهَا)', $ar5, $indo5, '5. ISIAN SINGKAT', 'Lengkapilah harakat kata bergaris bawah berikut: "كَانَ الأُسْتَاذُ ... " (pilihlah: حَاضِرٌ atau حَاضِرًا). Bentuk yang tepat untuk Khabar Kaana adalah...'),
            'kunci_jawaban' => 'حَاضِرًا',
            'konten_tambahan' => ['rubrik' => 'حَاضِرًا (atau Hadiran / حاضرا)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 6 (SOAL 26 - 30): العَمَلُ وَالإِتْقَانُ (Etos Kerja & Inna)
        // ══════════════════════════════════════════════════════════════════
        $ar6 = 'حَثَّ الإِسْلَامُ أَبْنَاءَهُ عَلَى العَمَلِ الشَّرِيْفِ وَالتَّكَسُّبِ الحَلَالِ، وَحَذَّرَ مِنَ الكَسَلِ وَالبَطَالَةِ.<br>
<u>إِنَّ اللهَ تَعَالَى يُحِبُّ إِذَا عَمِلَ أَحَدُكُمْ عَمَلًا أَنْ يُتْقِنَهُ</u>. الإِتْقَانُ هُوَ أَدَاءُ العَمَلِ عَلَى أَكْمَلِ وَجْهٍ بِدِقَّةٍ وَإِخْلَاصٍ.<br>
<u>إِنَّ العَمَلَ الصَّالِحَ مَصْدَرٌ لِلعِزَّةِ</u> وَالكَرَامَةِ، وَبِهِ تَرْتَقِي الأُمَمُ وَتَتَقَدَّمُ الحَضَارَاتُ.<br>
قَالَ عُمَرُ بْنُ الخَطَّابِ رَضِيَ اللهُ عَنْهُ: "لَا يَقْعُدَنَّ أَحَدُكُمْ عَنْ طَلَبِ الرِّزْقِ يَقُوْلُ: اللَّهُمَّ ارْزُقْنِي، وَقَدْ عَلِمَ أَنَّ السَّمَاءَ لَا تُمْطِرُ ذَهَبًا وَلَا فِضَّةً".';
        $indo6 = 'Teks mengenai etos kerja keras dalam Islam, profesionalisme (Itqan), kemuliaan mencari rezeki halal, serta kaidah Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا).';

        // No 26: PG
        $list[] = [
            'nomor_urut' => 26,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَمَلُ وَالإِتْقَانُ فِي الإِسْلَامِ', 'Etos Kerja & Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)', $ar6, $indo6, '1. PILIHAN GANDA', 'مَا المَقْصُوْدُ بِـ"الإِتْقَانِ" فِي العَمَلِ وِفْقًا لِمَا وَرَدَ فِي النَّصِّ؟ (Apakah yang dimaksud dengan Itqan dalam bekerja menurut teks?)'),
            'opsi_a' => 'أَدَاءُ العَمَلِ بِسُرْعَةٍ دُوْنَ اهْتِمَامٍ بِالجَوْدَةِ',
            'opsi_b' => 'أَدَاءُ العَمَلِ عَلَى أَكْمَلِ وَجْهٍ بِدِقَّةٍ وَإِخْلَاصٍ',
            'opsi_c' => 'تَرْكُ العَمَلِ وَانْتِظَارُ المُسَاعَدَةِ',
            'opsi_d' => 'الِاعْتِمَادُ عَلَى الآخَرِيْنَ فِي كُلِّ شَيْءٍ',
            'opsi_e' => 'العَمَلُ مِنْ أَجْلِ الشُّهْرَةِ فَقَطْ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَدَاءُ العَمَلِ بِسُرْعَةٍ دُوْنَ اهْتِمَامٍ بِالجَوْدَةِ',
                'B' => 'أَدَاءُ العَمَلِ عَلَى أَكْمَلِ وَجْهٍ بِدِقَّةٍ وَإِخْلَاصٍ',
                'C' => 'تَرْكُ العَمَلِ وَانْتِظَارُ المُسَاعَدَةِ',
                'D' => 'الِاعْتِمَادُ عَلَى الآخَرِيْنَ فِي كُلِّ شَيْءٍ',
                'E' => 'العَمَلُ مِنْ أَجْلِ الشُّهْرَةِ فَقَطْ'
            ]]
        ];

        // No 27: PGK
        $list[] = [
            'nomor_urut' => 27,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَمَلُ وَالإِتْقَانُ فِي الإِسْلَامِ', 'Etos Kerja & Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)', $ar6, $indo6, '2. PG KOMPLEKS (CENTANG)', 'Manakah huruf-huruf di bawah ini yang tergolong ke dalam <strong>Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'إِنَّ / أَنَّ (Sesungguhnya / Bahwa)',
            'opsi_b' => 'كَأَنَّ (Seakan-akan / Seperti)',
            'opsi_c' => 'لَكِنَّ (Akan tetapi)',
            'opsi_d' => 'أَصْبَحَ (Saudara Kaana)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'إِنَّ / أَنَّ (Sesungguhnya / Bahwa)', 'is_kunci' => true],
                    ['teks' => 'كَأَنَّ (Seakan-akan / Seperti)', 'is_kunci' => true],
                    ['teks' => 'لَكِنَّ (Akan tetapi)', 'is_kunci' => true],
                    ['teks' => 'أَصْبَحَ (Saudara Kaana)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 28: B/S
        $list[] = [
            'nomor_urut' => 28,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَمَلُ وَالإِتْقَانُ فِي الإِسْلَامِ', 'Etos Kerja & Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)', $ar6, $indo6, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari fungsi amal Inna wa Akhwatuha berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Inna wa akhwatuha menashobkan Isim Inna (fathah) dan merafa\'kan Khabar Inna (dhammah).', 'kunci' => 'Benar'],
                    ['teks' => 'Fungsi gramatika Inna wa akhwatuha persis sama dengan fungsi Kaana wa akhwatuha.', 'kunci' => 'Salah'],
                    ['teks' => 'Kalimat "إِنَّ العِلْمَ نُوْرٌ" benar kaidahnya karena العِلْمَ manshub dan نُوْرٌ marfu\'.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 29: Menjodohkan
        $list[] = [
            'nomor_urut' => 29,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَمَلُ وَالإِتْقَانُ فِي الإِسْلَامِ', 'Etos Kerja & Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)', $ar6, $indo6, '4. MENJODOHKAN', 'Pasangkan saudara Inna dengan makna harfiahnya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'إِنَّ / أَنَّ', 'respon' => 'Penegasan (Taukid / Sesungguhnya)'],
                ['premis' => 'كَأَنَّ', 'respon' => 'Penyerupaan (Tasybih / Seolah-olah)'],
                ['premis' => 'لَكِنَّ', 'respon' => 'Penyanggahan (Istidrak / Akan tetapi)'],
                ['premis' => 'لَيْتَ', 'respon' => 'Harapan yang mustahil (Tamanni / Andaikan)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'إِنَّ / أَنَّ', 'respon' => 'Penegasan (Taukid / Sesungguhnya)'],
                    ['premis' => 'كَأَنَّ', 'respon' => 'Penyerupaan (Tasybih / Seolah-olah)'],
                    ['premis' => 'لَكِنَّ', 'respon' => 'Penyanggahan (Istidrak / Akan tetapi)'],
                    ['premis' => 'لَيْتَ', 'respon' => 'Harapan yang mustahil (Tamanni / Andaikan)']
                ]
            ]
        ];

        // No 30: Isian Singkat
        $list[] = [
            'nomor_urut' => 30,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(6, '26 - 30', 'العَمَلُ وَالإِتْقَانُ فِي الإِسْلَامِ', 'Etos Kerja & Inna wa Akhwatuha (إِنَّ وَأَخَوَاتُهَا)', $ar6, $indo6, '5. ISIAN SINGKAT', 'Lengkapilah harakat Isim Inna pada kalimat berikut: "إِنَّ ... مَفْتُوْحٌ" (pilihlah: البَابُ atau البَابَ). Kata yang tepat adalah...'),
            'kunci_jawaban' => 'البَابَ',
            'konten_tambahan' => ['rubrik' => 'البَابَ (atau Al-Bāba / الباب)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 7 (SOAL 31 - 35): التَّعْلِيْمُ العَالِي (Pendidikan Tinggi & Amar)
        // ══════════════════════════════════════════════════════════════════
        $ar7 = 'يَتَطَلَّعُ طُلَّابُ المَرْحَلَةِ الثَّانَوِيَّةِ إِلَى مُوَاصَلَةِ دِرَاسَتِهِمْ فِي <u>الجَامِعَاتِ المَرْمُوْقَةِ</u> دَاخِلَ البِلَادِ وَخَارِجِهَا.<br>
نَصَحَ المُرْشِدُ الطُّلَّابِيُّ التَّلَامِيْذَ قَائِلًا: "يَا أَبْنَائِي، <u>اجْتَهِدُوْا</u> فِي دِرَاسَتِكُمْ، وَ<u>اخْتَارُوْا</u> التَّخَصُّصَ الَّذِي يُنَاسِبُ قُدُرَاتِكُمْ وَطُمُوْحَاتِكُمْ".<br>
مِنَ التَّخَصُّصَاتِ المَطْلُوْبَةِ: كُلِّيَّةُ الشَّرِيْعَةِ وَالدِّرَاسَاتِ الإِسْلَامِيَّةِ، وَكُلِّيَّةُ الطِّبِّ، وَكُلِّيَّةُ الهَنْدَسَةِ، وَكُلِّيَّةُ التَّرْبِيَةِ.<br>
قَالَ الأُسْتَاذُ: "<u>اقْرَأْ</u> كَثِيْرًا، وَ<u>تَعَلَّمْ</u> اللُّغَاتِ الأَجْنَبِيَّةَ، لِتَكُوْنَ قَادِرًا عَلَى مُوَاجَهَةِ تَحَدِّيَاتِ العَصْرِ".';
        $indo7 = 'Teks motivasi melanjutkan studi ke jenjang perguruan tinggi (universitas), memilih fakultas sesuai potensi minat, serta penggunaan Fi\'il Amar (Kata Kerja Perintah).';

        // No 31: PG
        $list[] = [
            'nomor_urut' => 31,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'مَرَاحِلُ التَّعْلِيْمِ العَالِي فِي الجَامِعَةِ', 'Pendidikan Tinggi & Fi\'il Amar (فِعْلُ الأَمْرِ)', $ar7, $indo7, '1. PILIHAN GANDA', 'بِمَاذَا نَصَحَ الأُسْتَاذُ طُلَّابَهُ لِمُوَاجَهَةِ تَحَدِّيَاتِ العَصْرِ؟ (Apakah nasihat ustadz untuk menghadapi tantangan zaman?)'),
            'opsi_a' => 'أَنْ يَقْرَؤُوْا كَثِيْرًا وَيَتَعَلَّمُوْا اللُّغَاتِ الأَجْنَبِيَّةَ',
            'opsi_b' => 'أَنْ يَتْرُكُوْا الدِّرَاسَةَ وَيَذْهَبُوْا إِلَى التِّجَارَةِ فَوْرًا',
            'opsi_c' => 'أَنْ يَقْضُوْا أَوْقَاتَهُمْ فِي السَّفَرِ وَالنُّزْهَةِ',
            'opsi_d' => 'أَنْ يَكْتَفُوْا بِالشَّهَادَةِ الِابْتِدَائِيَّةِ',
            'opsi_e' => 'أَنْ يَتَجَنَّبُوْا القِرَاءَةَ وَالبَحْثَ',
            'kunci_jawaban' => 'A',
            'konten_tambahan' => ['kunci' => 'A', 'opsi' => [
                'A' => 'أَنْ يَقْرَؤُوْا كَثِيْرًا وَيَتَعَلَّمُوْا اللُّغَاتِ الأَجْنَبِيَّةَ',
                'B' => 'أَنْ يَتْرُكُوْا الدِّرَاسَةَ وَيَذْهَبُوْا إِلَى التِّجَارَةِ فَوْرًا',
                'C' => 'أَنْ يَقْضُوْا أَوْقَاتَهُمْ فِي السَّفَرِ وَالنُّزْهَةِ',
                'D' => 'أَنْ يَكْتَفُوْا بِالشَّهَادَةِ الِابْتِدَائِيَّةِ',
                'E' => 'أَنْ يَتَجَنَّبُوْا القِرَاءَةَ وَالبَحْثَ'
            ]]
        ];

        // No 32: PGK
        $list[] = [
            'nomor_urut' => 32,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'مَرَاحِلُ التَّعْلِيْمِ العَالِي فِي الجَامِعَةِ', 'Pendidikan Tinggi & Fi\'il Amar (فِعْلُ الأَمْرِ)', $ar7, $indo7, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang merupakan <strong>Fi\'il Amar (Kata Kerja Perintah)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'اقْرَأْ (Bacalah!)',
            'opsi_b' => 'اجْتَهِدْ (Bersungguh-sungguhlah!)',
            'opsi_c' => 'اكْتُبْ (Tulislah!)',
            'opsi_d' => 'يَقْرَأُ (Dia sedang membaca)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'اقْرَأْ (Bacalah!)', 'is_kunci' => true],
                    ['teks' => 'اجْتَهِدْ (Bersungguh-sungguhlah!)', 'is_kunci' => true],
                    ['teks' => 'اكْتُبْ (Tulislah!)', 'is_kunci' => true],
                    ['teks' => 'يَقْرَأُ (Dia sedang membaca)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 33: B/S
        $list[] = [
            'nomor_urut' => 33,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'مَرَاحِلُ التَّعْلِيْمِ العَالِي فِي الجَامِعَةِ', 'Pendidikan Tinggi & Fi\'il Amar (فِعْلُ الأَمْرِ)', $ar7, $indo7, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran dari pembentukan Fi\'il Amar berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Fi\'il Amar dibentuk dari Fi\'il Mudhari\' dengan membuang huruf mudhara\'ah dan mematikan (sukun) huruf terakhirnya.', 'kunci' => 'Benar'],
                    ['teks' => 'Bentuk Fi\'il Amar untuk dhomir lawan bicara perempuan (أَنْتِ) sama persis tanpa tambahan huruf apapun.', 'kunci' => 'Salah'],
                    ['teks' => 'Bila ditujukan untuk dhomir "أَنْتِ", Fi\'il Amar mendapat tambahan ya mukhathabah di akhirnya (misal: اقْرَئِيْ).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 34: Menjodohkan
        $list[] = [
            'nomor_urut' => 34,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'مَرَاحِلُ التَّعْلِيْمِ العَالِي فِي الجَامِعَةِ', 'Pendidikan Tinggi & Fi\'il Amar (فِعْلُ الأَمْرِ)', $ar7, $indo7, '4. MENJODOHKAN', 'Pasangkan nama fakultas di universitas dalam bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'كُلِّيَّةُ الشَّرِيْعَةِ', 'respon' => 'Fakultas Syariah / Hukum Islam'],
                ['premis' => 'كُلِّيَّةُ الطِّبِّ', 'respon' => 'Fakultas Kedokteran'],
                ['premis' => 'كُلِّيَّةُ الهَنْدَسَةِ', 'respon' => 'Fakultas Teknik'],
                ['premis' => 'كُلِّيَّةُ التَّرْبِيَةِ', 'respon' => 'Fakultas Keguruan dan Ilmu Pendidikan']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'كُلِّيَّةُ الشَّرِيْعَةِ', 'respon' => 'Fakultas Syariah / Hukum Islam'],
                    ['premis' => 'كُلِّيَّةُ الطِّبِّ', 'respon' => 'Fakultas Kedokteran'],
                    ['premis' => 'كُلِّيَّةُ الهَنْدَسَةِ', 'respon' => 'Fakultas Teknik'],
                    ['premis' => 'كُلِّيَّةُ التَّرْبِيَةِ', 'respon' => 'Fakultas Keguruan dan Ilmu Pendidikan']
                ]
            ]
        ];

        // No 35: Isian Singkat
        $list[] = [
            'nomor_urut' => 35,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(7, '31 - 35', 'مَرَاحِلُ التَّعْلِيْمِ العَالِي فِي الجَامِعَةِ', 'Pendidikan Tinggi & Fi\'il Amar (فِعْلُ الأَمْرِ)', $ar7, $indo7, '5. ISIAN SINGKAT', 'Ubahlah kata kerja "كَتَبَ - يَكْتُبُ" menjadi bentuk Fi\'il Amar perintah untuk seorang laki-laki (أَنْتَ): " ... الرِّسَالَةَ يَا أَخِي!".'),
            'kunci_jawaban' => 'اكْتُبْ',
            'konten_tambahan' => ['rubrik' => 'اكْتُبْ (atau Uktub / اكتب)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 8 (SOAL 36 - 40): المَسْجِدُ مَرْكَزُ الحَضَارَةِ (Masjid & Majhul)
        // ══════════════════════════════════════════════════════════════════
        $ar8 = 'لَمْ يَكُنِ المَسْجِدُ فِي عَهْدِ النَّبِيِّ ﷺ مُجَرَّدَ مَكَانٍ لِأَدَاءِ الصَّلَوَاتِ الخَمْسِ فَحَسْبُ، بَلْ كَانَ مَرْكَزًا شَامِلًا لِلحَضَارَةِ الإِسْلَامِيَّةِ.<br>
فِي المَسْجِدِ، <u>عُلِّمَ القُرْآنُ الكَرِيْمُ</u> وَأَحْكَامُ الشَّرِيْعَةِ لِلصَّحَابَةِ، وَ<u>عُقِدَتِ الرَّايَاتُ</u> لِلجِهَادِ، وَ<u>قُضِيَ بَيْنَ النَّاسِ</u> بِالعَدْلِ.<br>
وَفِي صُفَّةِ المَسْجِدِ النَّبَوِيِّ، <u>أُنْشِئَتْ أَوَّلُ مَدْرَسَةٍ إِسْلَامِيَّةٍ</u> لِطُلَّابِ العِلْمِ الفُقَرَاءِ (أَهْلِ الصُّفَّةِ).<br>
لِذَلِكَ، يَنْبَغِي لِلْمَسَاجِدِ فِي هَذَا العَصْرِ أَنْ تَسْتَعِيْدَ دَوْرَهَا التَّرْبَوِيَّ وَالاجْتِمَاعِيَّ فِي نَشْرِ العِلْمِ وَخِدْمَةِ المُجْتَمَعِ.';
        $indo8 = 'Teks mengenai fungsi holistik masjid pada masa peradaban Islam klasik: tempat ibadah, madrasah ilmu bagi Ahlus Suffah, pusat musyawarah, serta kaidah Kalimat Pasif (Fi\'il Mabni Majhul & Naibul Fa\'il).';

        // No 36: PG
        $list[] = [
            'nomor_urut' => 36,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المَسْجِدُ مَرْكَزُ الحَضَارَةِ الإِسْلَامِيَّةِ', 'Pusat Peradaban & Naibul Fa\'il (نَائِبُ الْفَاعِلِ)', $ar8, $indo8, '1. PILIHAN GANDA', 'مَاذَا أُنْشِئَ فِي صُفَّةِ المَسْجِدِ النَّبَوِيِّ الشَّرِيْفِ كَمَا وَرَدَ فِي النَّصِّ؟ (Apakah yang didirikan di serambi/Suffah Masjid Nabawi?)'),
            'opsi_a' => 'أَكْبَرُ سُوْقٍ تِجَارِيٍّ فِي الجَزِيْرَةِ',
            'opsi_b' => 'أَوَّلُ مَدْرَسَةٍ إِسْلَامِيَّةٍ لِطُلَّابِ العِلْمِ (أَهْلِ الصُّفَّةِ)',
            'opsi_c' => 'مَصْنَعٌ لِلأَسْلِحَةِ الحَرْبِيَّةِ',
            'opsi_d' => 'فُنْدُقٌ لِلْمُسَافِرِيْنَ وَالتُّجَّارِ',
            'opsi_e' => 'مَسْبَحٌ لِلأَطْفَالِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'أَكْبَرُ سُوْقٍ تِجَارِيٍّ فِي الجَزِيْرَةِ',
                'B' => 'أَوَّلُ مَدْرَسَةٍ إِسْلَامِيَّةٍ لِطُلَّابِ العِلْمِ (أَهْلِ الصُّفَّةِ)',
                'C' => 'مَصْنَعٌ لِلأَسْلِحَةِ الحَرْبِيَّةِ',
                'D' => 'فُنْدُقٌ لِلْمُسَافِرِيْنَ وَالتُّجَّارِ',
                'E' => 'مَسْبَحٌ لِلأَطْفَالِ'
            ]]
        ];

        // No 37: PGK
        $list[] = [
            'nomor_urut' => 37,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المَسْجِدُ مَرْكَزُ الحَضَارَةِ الإِسْلَامِيَّةِ', 'Pusat Peradaban & Naibul Fa\'il (نَائِبُ الْفَاعِلِ)', $ar8, $indo8, '2. PG KOMPLEKS (CENTANG)', 'Pernyataan manakah yang <strong>BENAR</strong> mengenai pembentukan <strong>Fi\'il Mabni Majhul (Kata Kerja Pasif)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'Fi\'il Madhi pasif dibentuk dengan mendhammahkan huruf pertama dan mengkasrahkan huruf sebelum akhir (ضُمَّ أَوَّلُهُ وَكُسِرَ مَا قَبْلَ آخِرِهِ), contoh: كُتِبَ.',
            'opsi_b' => 'Fi\'il Mudhari\' pasif dibentuk dengan mendhammahkan huruf pertama dan memfathahkan huruf sebelum akhir (ضُمَّ أَوَّلُهُ وَفُتِحَ مَا قَبْلَ آخِرِهِ), contoh: يُكْتَبُ.',
            'opsi_c' => 'Setelah Fa\'il dihapus, kedudukannya digantikan oleh Naibul Fa\'il yang berstatus Marfu\' (dhammah).',
            'opsi_d' => 'Kata kerja pasif tidak memerlukan objek pengganti (Naibul Fa\'il).',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Fi\'il Madhi pasif dibentuk dengan mendhammahkan huruf pertama dan mengkasrahkan huruf sebelum akhir (ضُمَّ أَوَّلُهُ وَكُسِرَ مَا قَبْلَ آخِرِهِ), contoh: كُتِبَ.', 'is_kunci' => true],
                    ['teks' => 'Fi\'il Mudhari\' pasif dibentuk dengan mendhammahkan huruf pertama dan memfathahkan huruf sebelum akhir (ضُمَّ أَوَّلُهُ وَفُتِحَ مَا قَبْلَ آخِرِهِ), contoh: يُكْتَبُ.', 'is_kunci' => true],
                    ['teks' => 'Setelah Fa\'il dihapus, kedudukannya digantikan oleh Naibul Fa\'il yang berstatus Marfu\' (dhammah).', 'is_kunci' => true],
                    ['teks' => 'Kata kerja pasif tidak memerlukan objek pengganti (Naibul Fa\'il).', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 38: B/S
        $list[] = [
            'nomor_urut' => 38,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المَسْجِدُ مَرْكَزُ الحَضَارَةِ الإِسْلَامِيَّةِ', 'Pusat Peradaban & Naibul Fa\'il (نَائِبُ الْفَاعِلِ)', $ar8, $indo8, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari perubahan kalimat aktif ke pasif berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Kalimat aktif "فَتَحَ طَارِقٌ البَابَ" jika diubah ke bentuk pasif menjadi "فُتِحَ البَابُ".', 'kunci' => 'Benar'],
                    ['teks' => 'Pada kalimat "خُلِقَ الإِنْسَانُ ضَعِيْفًا", kata الإِنْسَانُ berkedudukan sebagai Fa\'il.', 'kunci' => 'Salah'],
                    ['teks' => 'Pada kalimat "خُلِقَ الإِنْسَانُ ضَعِيْفًا", kata الإِنْسَانُ berkedudukan sebagai Naibul Fa\'il marfu\'.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 39: Menjodohkan
        $list[] = [
            'nomor_urut' => 39,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(8, '36 - 40', 'المَسْجِدُ مَرْكَزُ الحَضَارَةِ الإِسْلَامِيَّةِ', 'Pusat Peradaban & Naibul Fa\'il (نَائِبُ الْفَاعِلِ)', $ar8, $indo8, '4. MENJODOHKAN', 'Pasangkan kata kerja aktif (Ma\'lum) dengan bentuk pasifnya (Majhul):'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'قَرَأَ (Membaca)', 'respon' => 'قُرِئَ (Dibaca)'],
                ['premis' => 'يَقْرَأُ (Sedang membaca)', 'respon' => 'يُقْرَأُ (Sedang dibaca)'],
                ['premis' => 'فَتَحَ (Membuka)', 'respon' => 'فُتِحَ (Dibuka)'],
                ['premis' => 'شَرِبَ (Meminum)', 'respon' => 'شُرِبَ (Diminum)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'قَرَأَ (Membaca)', 'respon' => 'قُرِئَ (Dibaca)'],
                    ['premis' => 'يَقْرَأُ (Sedang membaca)', 'respon' => 'يُقْرَأُ (Sedang dibaca)'],
                    ['premis' => 'فَتَحَ (Membuka)', 'respon' => 'فُتِحَ (Dibuka)'],
                    ['premis' => 'شَرِبَ (Meminum)', 'respon' => 'شُرِبَ (Diminum)']
                ]
            ]
        ];

        // No 40: Isian Singkat
        $list[] = [
            'nomor_urut' => 40,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(8, '36 - 40', 'المَسْجِدُ مَرْكَزُ الحَضَارَةِ الإِسْلَامِيَّةِ', 'Pusat Peradaban & Naibul Fa\'il (نَائِبُ الْفَاعِلِ)', $ar8, $indo8, '5. ISIAN SINGKAT', 'Isim marfu\' yang menggantikan posisi Fa\'il setelah kalimatnya diubah menjadi kalimat pasif (Mabni Majhul) dinamakan...'),
            'kunci_jawaban' => 'Naibul Fa\'il',
            'konten_tambahan' => ['rubrik' => 'Naibul Fa\'il (atau نَائِبُ الْفَاعِلِ)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 9 (SOAL 41 - 45): الكَوَارِثُ الطَّبِيْعِيَّةُ (Bencana & Isim Tafdhil)
        // ══════════════════════════════════════════════════════════════════
        $ar9 = 'تَتَعَرَّضُ بَعْضُ المَنَاطِقِ فِي العَالَمِ لِكَوَارِثَ طَبِيْعِيَّةٍ مُفَاجِئَةٍ، مِثْلُ: الفَيَضَانَاتِ، وَالزَّلَازِلِ، وَالبَرَاكِيْنِ.<br>
عِنْدَ وُقُوْعِ الزِّلْزَالِ الأَخِيْرِ، سَارَعَ طُلَّابُ مَدْرَسَةِ "مِفْتَاحُ الهُدَى" إِلَى جَمْعِ التَّبَرُّعَاتِ وَالمَعُوْنَاتِ لِمُسَاعَدَةِ المَنْكُوْبِيْنَ.<br>
قَالَ رَئِيْسُ المَدْرَسَةِ: "<u>مُسَاعَدَةُ المُحْتَاجِيْنَ أَوْلَى</u> مِنَ الإِنْفَاقِ فِي الكَمَالِيَّاتِ، وَ<u>اليَدُ العُلْيَا خَيْرٌ وَأَفْضَلُ مِنَ اليَدِ السُّفْلَى</u>".<br>
إِنَّ التَّضَامُنَ الإِنْسَانِيَّ فِي وَقْتِ الأَزَمَاتِ يُعَبِّرُ عَنْ صِدْقِ الإِيْمَانِ وَأُخُوَّةِ الإِسْلَامِ.';
        $indo9 = 'Teks mengenai solidaritas penanggulangan bencana alam (gempa bumi, banjir), menggalang donasi bantuan kemanusiaan, serta penggunaan Isim Tafdhil (Pola أَفْعَلُ مِنْ).';

        // No 41: PG
        $list[] = [
            'nomor_urut' => 41,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'الكَوَارِثُ الطَّبِيْعِيَّةُ وَالمُسَاعَدَاتُ', 'Solidaritas Bencana & Isim Tafdhil (اسْمُ التَّفْضِيْلِ)', $ar9, $indo9, '1. PILIHAN GANDA', 'مَاذَا فَعَلَ طُلَّابُ مَدْرَسَةِ مِفْتَاحِ الهُدَى عِنْدَ وُقُوْعِ الزِّلْزَالِ؟ (Apakah yang dilakukan siswa MAS Miftahul Huda saat gempa?)'),
            'opsi_a' => 'سَافَرُوْا إِلَى خَارِجِ البِلَادِ لِلسِّيَاحَةِ',
            'opsi_b' => 'سَارَعُوْا إِلَى جَمْعِ التَّبَرُّعَاتِ وَالمَعُوْنَاتِ لِلْمَنْكُوْبِيْنَ',
            'opsi_c' => 'أَغْلَقُوْا أَبْوَابَ المَدْرَسَةِ تَمَامًا',
            'opsi_d' => 'انْتَظَرُوْا المُسَاعَدَاتِ الخَارِجِيَّةَ دُوْنَ حَرَكَةٍ',
            'opsi_e' => 'تَجَاهَلُوْا أَخْبَارَ الكَارِثَةِ',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'سَافَرُوْا إِلَى خَارِجِ البِلَادِ لِلسِّيَاحَةِ',
                'B' => 'سَارَعُوْا إِلَى جَمْعِ التَّبَرُّعَاتِ وَالمَعُوْنَاتِ لِلْمَنْكُوْبِيْنَ',
                'C' => 'أَغْلَقُوْا أَبْوَابَ المَدْرَسَةِ تَمَامًا',
                'D' => 'انْتَظَرُوْا المُسَاعَدَاتِ الخَارِجِيَّةَ دُوْنَ حَرَكَةٍ',
                'E' => 'تَجَاهَلُوْا أَخْبَارَ الكَارِثَةِ'
            ]]
        ];

        // No 42: PGK
        $list[] = [
            'nomor_urut' => 42,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'الكَوَارِثُ الطَّبِيْعِيَّةُ وَالمُسَاعَدَاتُ', 'Solidaritas Bencana & Isim Tafdhil (اسْمُ التَّفْضِيْلِ)', $ar9, $indo9, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang berwazan <strong>أَفْعَلُ</strong> dan berfungsi sebagai <strong>Isim Tafdhil (Tingkat Perbandingan Lebih/Paling)</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'أَفْضَلُ (Lebih utama / paling utama)',
            'opsi_b' => 'أَكْبَرُ (Lebih besar / paling besar)',
            'opsi_c' => 'أَجْمَلُ (Lebih indah / paling indah)',
            'opsi_d' => 'يَفْعَلُ (Fi\'il Mudhari\')',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'أَفْضَلُ (Lebih utama / paling utama)', 'is_kunci' => true],
                    ['teks' => 'أَكْبَرُ (Lebih besar / paling besar)', 'is_kunci' => true],
                    ['teks' => 'أَجْمَلُ (Lebih indah / paling indah)', 'is_kunci' => true],
                    ['teks' => 'يَفْعَلُ (Fi\'il Mudhari\')', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 43: B/S
        $list[] = [
            'nomor_urut' => 43,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'الكَوَارِثُ الطَّبِيْعِيَّةُ وَالمُسَاعَدَاتُ', 'Solidaritas Bencana & Isim Tafdhil (اسْمُ التَّفْضِيْلِ)', $ar9, $indo9, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah Isim Tafdhil berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Pola Isim Tafdhil "أَفْعَلُ مِنْ" digunakan untuk menyatakan tingkatan perbandingan komparatif (lebih ... daripada).', 'kunci' => 'Benar'],
                    ['teks' => 'Bentuk Muannats dari wazan أَفْعَلُ adalah مَفْعُوْلَةٌ.', 'kunci' => 'Salah'],
                    ['teks' => 'Bentuk Muannats dari wazan أَفْعَلُ adalah فُعْلَى (seperti: أَكْبَرُ menjadi كُبْرَى).', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 44: Menjodohkan
        $list[] = [
            'nomor_urut' => 44,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(9, '41 - 45', 'الكَوَارِثُ الطَّبِيْعِيَّةُ وَالمُسَاعَدَاتُ', 'Solidaritas Bencana & Isim Tafdhil (اسْمُ التَّفْضِيْلِ)', $ar9, $indo9, '4. MENJODOHKAN', 'Pasangkan kalimat perbandingan bahasa Arab dengan artinya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'المَسْجِدُ أَكْبَرُ مِنَ الفَصْلِ', 'respon' => 'Masjid lebih besar daripada ruang kelas'],
                ['premis' => 'القِطَارُ أَسْرَعُ مِنَ السَّيَّارَةِ', 'respon' => 'Kereta api lebih cepat daripada mobil'],
                ['premis' => 'العِلْمُ أَنْفَعُ مِنَ المَالِ', 'respon' => 'Ilmu lebih bermanfaat daripada harta'],
                ['premis' => 'البُرْجُ أَعْلَى مِنَ البَيْتِ', 'respon' => 'Menara lebih tinggi daripada rumah']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'المَسْجِدُ أَكْبَرُ مِنَ الفَصْلِ', 'respon' => 'Masjid lebih besar daripada ruang kelas'],
                    ['premis' => 'القِطَارُ أَسْرَعُ مِنَ السَّيَّارَةِ', 'respon' => 'Kereta api lebih cepat daripada mobil'],
                    ['premis' => 'العِلْمُ أَنْفَعُ مِنَ المَالِ', 'respon' => 'Ilmu lebih bermanfaat daripada harta'],
                    ['premis' => 'البُرْجُ أَعْلَى مِنَ البَيْتِ', 'respon' => 'Menara lebih tinggi daripada rumah']
                ]
            ]
        ];

        // No 45: Isian Singkat
        $list[] = [
            'nomor_urut' => 45,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(9, '41 - 45', 'الكَوَارِثُ الطَّبِيْعِيَّةُ وَالمُسَاعَدَاتُ', 'Solidaritas Bencana & Isim Tafdhil (اسْمُ التَّفْضِيْلِ)', $ar9, $indo9, '5. ISIAN SINGKAT', 'Ubahlah kata sifat "كَبِيْرٌ" (besar) menjadi bentuk Isim Tafdhil bermakna "lebih besar": " ... مِنْ" .'),
            'kunci_jawaban' => 'أَكْبَرُ',
            'konten_tambahan' => ['rubrik' => 'أَكْبَرُ (atau Akbar / اكبر)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 10 (SOAL 46 - 50): الشَّخْصِيَّةُ الإِسْلَامِيَّةُ (Ibnu Sina & Maushul)
        // ══════════════════════════════════════════════════════════════════
        $ar10 = 'ابْنُ سِيْنَا هُوَ العَالِمُ الإِسْلَامِيُّ العَبْقَرِيُّ <u>الَّذِي لُقِّبَ بِـ"أَمِيْرِ الأَطِبَّاءِ"</u> فِي الحَضَارَةِ الإِسْلَامِيَّةِ وَالغَرْبِيَّةِ.<br>
أَلَّفَ ابْنُ سِيْنَا كِتَابَهُ الشَّهِيْرَ <u>"القَانُوْنَ فِي الطِّبِّ" الَّذِي كَانَ المَرْجِعَ الأَسَاسِيَّ</u> لِتَدْرِيْسِ الطِّبِّ فِي جَامِعَاتِ أُوْرُوبَّا لِعِدَّةِ قُرُوْنٍ.<br>
وَهُوَ أَيْضًا فَيْلَسُوْفٌ وَفَلَكِيٌّ وَرِيَاضِيٌّ <u>الَّذِي قَضَى حَيَاتَهُ فِي طَلَبِ العِلْمِ</u> وَالبَحْثِ وَالتَّأْلِيْفِ.<br>
إِنَّ الإِنْجَازَاتِ <u>الَّتِي قَدَّمَهَا عُلَمَاءُ الإِسْلَامِ</u> تُثْبِتُ أَنَّ الإِسْلَامَ دِيْنُ العِلْمِ وَالحَضَارَةِ وَالتَّنْوِيْرِ.';
        $indo10 = 'Teks biografi tokoh ilmuwan kedokteran muslim dunia, Ibnu Sina (Avicenna), mahakarya Al-Qanun fi Ath-Thibb, serta penerapan kaidah Isim Maushul (Kata Sambung).';

        // No 46: PG
        $list[] = [
            'nomor_urut' => 46,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الشَّخْصِيَّةُ الإِسْلَامِيَّةُ: ابْنُ سِيْنَا', 'Tokoh Muslim Dunia & Isim Maushul (الاِسْمُ الْمَوْصُوْلُ)', $ar10, $indo10, '1. PILIHAN GANDA', 'مَا اسْمُ الكِتَابِ الطِّبِّيِّ الشَّهِيْرِ الَّذِي أَلَّفَهُ ابْنُ سِيْنَا؟ (Apakah nama kitab medis monumental karya Ibnu Sina?)'),
            'opsi_a' => 'إِحْيَاءُ عُلُوْمِ الدِّيْنِ',
            'opsi_b' => 'القَانُوْنُ فِي الطِّبِّ',
            'opsi_c' => 'مُقَدِّمَةُ ابْنِ خَلْدُوْنَ',
            'opsi_d' => 'البِدَايَةُ وَالنِّهَايَةُ',
            'opsi_e' => 'فَتْحُ البَارِي',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => [
                'A' => 'إِحْيَاءُ عُلُوْمِ الدِّيْنِ',
                'B' => 'القَانُوْنُ فِي الطِّبِّ',
                'C' => 'مُقَدِّمَةُ ابْنِ خَلْدُوْنَ',
                'D' => 'البِدَايَةُ وَالنِّهَايَةُ',
                'E' => 'فَتْحُ البَارِي'
            ]]
        ];

        // No 47: PGK
        $list[] = [
            'nomor_urut' => 47,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الشَّخْصِيَّةُ الإِسْلَامِيَّةُ: ابْنُ سِيْنَا', 'Tokoh Muslim Dunia & Isim Maushul (الاِسْمُ الْمَوْصُوْلُ)', $ar10, $indo10, '2. PG KOMPLEKS (CENTANG)', 'Kata-kata manakah di bawah ini yang merupakan <strong>Isim Maushul (Kata Sambung yang bermakna "yang")</strong>? (Pilihan benar lebih dari satu)'),
            'opsi_a' => 'الَّذِي (Untuk isim mufrad mudzakkar)',
            'opsi_b' => 'الَّتِي (Untuk isim mufrad muannats & jamak taksir benda)',
            'opsi_c' => 'الَّذِيْنَ (Untuk jamak mudzakkar berakal)',
            'opsi_d' => 'هَذَا (Isim Isyarah)',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'الَّذِي (Untuk isim mufrad mudzakkar)', 'is_kunci' => true],
                    ['teks' => 'الَّتِي (Untuk isim mufrad muannats & jamak taksir benda)', 'is_kunci' => true],
                    ['teks' => 'الَّذِيْنَ (Untuk jamak mudzakkar berakal)', 'is_kunci' => true],
                    ['teks' => 'هَذَا (Isim Isyarah)', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 48: B/S
        $list[] = [
            'nomor_urut' => 48,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الشَّخْصِيَّةُ الإِسْلَامِيَّةُ: ابْنُ سِيْنَا', 'Tokoh Muslim Dunia & Isim Maushul (الاِسْمُ الْمَوْصُوْلُ)', $ar10, $indo10, '3. TABEL BENAR / SALAH', 'Tentukan nilai kebenaran dari kaidah Isim Maushul berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Untuk jamak taksir benda mati yang tidak berakal, kata sambung yang digunakan adalah "الَّتِي" (kaidah: كُلُّ جَمْعٍ مُؤَنَّثٌ).', 'kunci' => 'Benar'],
                    ['teks' => 'Kalimat "جَاءَ الطُّلَّابُ الَّتِي نَجَحُوْا" benar secara kaidah bahasa Arab.', 'kunci' => 'Salah'],
                    ['teks' => 'Kalimat yang benar adalah "جَاءَ الطُّلَّابُ الَّذِيْنَ نَجَحُوْا" karena jamak mudzakkar berakal.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 49: Menjodohkan
        $list[] = [
            'nomor_urut' => 49,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => CbtBiologiArabGenerator::wrapBioStimulus(10, '46 - 50', 'الشَّخْصِيَّةُ الإِسْلَامِيَّةُ: ابْنُ سِيْنَا', 'Tokoh Muslim Dunia & Isim Maushul (الاِسْمُ الْمَوْصُوْلُ)', $ar10, $indo10, '4. MENJODOHKAN', 'Pasangkan Isim Maushul dengan sasaran penggunaannya:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'الَّذِي', 'respon' => 'Tunggal laki-laki (Mufrad Mudzakkar)'],
                ['premis' => 'الَّتِي', 'respon' => 'Tunggal perempuan (Mufrad Muannats) & Jamak taksir tak berakal'],
                ['premis' => 'الَّذِيْنَ', 'respon' => 'Jamak laki-laki berakal (Jamak Mudzakkar)'],
                ['premis' => 'اللَّاتِي / اللَّائِي', 'respon' => 'Jamak perempuan (Jamak Muannats)']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'الَّذِي', 'respon' => 'Tunggal laki-laki (Mufrad Mudzakkar)'],
                    ['premis' => 'الَّتِي', 'respon' => 'Tunggal perempuan (Mufrad Muannats) & Jamak taksir tak berakal'],
                    ['premis' => 'الَّذِيْنَ', 'respon' => 'Jamak laki-laki berakal (Jamak Mudzakkar)'],
                    ['premis' => 'اللَّاتِي / اللَّائِي', 'respon' => 'Jamak perempuan (Jamak Muannats)']
                ]
            ]
        ];

        // No 50: Isian Singkat
        $list[] = [
            'nomor_urut' => 50,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => CbtBiologiArabGenerator::wrapArabStimulus(10, '46 - 50', 'الشَّخْصِيَّةُ الإِسْلَامِيَّةُ: ابْنُ سِيْنَا', 'Tokoh Muslim Dunia & Isim Maushul (الاِسْمُ الْمَوْصُوْلُ)', $ar10, $indo10, '5. ISIAN SINGKAT', 'Lengkapilah kalimat berikut dengan Isim Maushul yang tepat untuk seorang siswi: "هَذِهِ هِيَ الطَّالِبَةُ ... فَازَتْ فِي المُسَابَقَةِ" (Inilah siswi ... telah memenangkan perlombaan).'),
            'kunci_jawaban' => 'الَّتِي',
            'konten_tambahan' => ['rubrik' => 'الَّتِي (atau Allati / التي)']
        ];

        return $list;
    }
}
