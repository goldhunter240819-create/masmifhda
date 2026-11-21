<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class CbtAllSubjects20Generator {

    /**
     * Generate 20 Soal Mudah untuk SEMUA Mapel di Topik SIMULASI NEW CBT
     * Total 26 Mapel x Paket Kelas = 74 Paket Mapel (Masing-masing tepat 20 Butir Soal)
     */
    public static function generateAll($topikId = 10) {
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');
        $dbs = (!empty($db_siakad) && $db_siakad !== $db_core) ? [$db_siakad, $db_core] : [$db_core];

        // 1. Pastikan tabel CBT tersedia
        \App\Controllers\CbtController::ensureTables($db_core);

        // Ambil ID topik jika tidak dispesifikasikan
        if (!$topikId) {
            $tp = $db_siakad->query("SELECT id FROM bs_topik WHERE nama_topik LIKE '%SIMULASI%' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $topikId = $tp ? (int)$tp['id'] : 10;
        }

        // 2. Bersihkan seluruh butir soal lama di bs_soal dan cbt_soal untuk topik ini
        foreach ($dbs as $db) {
            try {
                $db->prepare("DELETE FROM bs_soal WHERE topik_id = ?")->execute([$topikId]);
            } catch (\Exception $e) {}
        }

        try {
            $uIds = $db_core->query("SELECT id FROM cbt_ujian WHERE topik_id = {$topikId}")->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($uIds)) {
                $inIds = implode(',', array_map('intval', $uIds));
                $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inIds})");
                $db_core->exec("DELETE FROM cbt_peserta_jawaban WHERE ujian_id IN ({$inIds})");
                $db_core->exec("DELETE FROM cbt_jawaban WHERE ujian_id IN ({$inIds})");
            }
        } catch (\Exception $e) {}

        // 3. Ambil seluruh paket di topik ini dari bs_paket
        $stmtP = $db_siakad->prepare("SELECT id, mapel_nama, kelas_nama, kelas_sasaran FROM bs_paket WHERE topik_id = ? ORDER BY id ASC");
        $stmtP->execute([$topikId]);
        $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        if (empty($paketList) && $db_core !== $db_siakad) {
            $stmtPCore = $db_core->prepare("SELECT id, mapel_nama, kelas_nama, kelas_sasaran FROM bs_paket WHERE topik_id = ? ORDER BY id ASC");
            $stmtPCore->execute([$topikId]);
            $paketList = $stmtPCore->fetchAll(PDO::FETCH_ASSOC);
        }

        $allQuestions = self::getMasterBankSoal();
        $totalInserted = 0;

        $stmtInsBs = $db_siakad->prepare("
            INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban)
            VALUES (?, ?, ?, ?, ?, 'sec_pg', 'Mudah', 5.00, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtInsBsCore = ($db_core !== $db_siakad) ? $db_core->prepare("
            INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban)
            VALUES (?, ?, ?, ?, ?, 'sec_pg', 'Mudah', 5.00, ?, ?, ?, ?, ?, ?, ?)
        ") : null;

        $stmtInsCbt = $db_core->prepare("
            INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 5.00, 'sec_pg')
        ");

        // Helper cari ujian_id
        $getLevel = function($str) {
            $s = strtoupper((string)$str);
            if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
            if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
            if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
            return 0;
        };

        foreach ($paketList as $pkt) {
            $pId = (int)$pkt['id'];
            $mapel = trim($pkt['mapel_nama']);
            $kelas = trim($pkt['kelas_nama']);
            $pLvl = $getLevel($kelas);

            // Ambil 20 butir soal untuk mapel ini
            $questions = self::getQuestionsForMapel($allQuestions, $mapel, $pLvl);

            // Cari ujian_id yang bersesuaian di cbt_ujian
            $ujianId = 0;
            try {
                $allUjian = $db_core->query("SELECT id, mapel_nama, kelas_nama FROM cbt_ujian WHERE topik_id = {$topikId}")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($allUjian as $uj) {
                    if (trim($uj['mapel_nama']) === $mapel && $getLevel($uj['kelas_nama']) === $pLvl) {
                        $ujianId = (int)$uj['id'];
                        break;
                    }
                }
            } catch (\Exception $e) {}

            $no = 1;
            foreach ($questions as $q) {
                $qText = $q['pertanyaan'];
                $a = $q['opsi_a'];
                $b = $q['opsi_b'];
                $c = $q['opsi_c'];
                $d = $q['opsi_d'];
                $e = $q['opsi_e'];
                $kunci = $q['kunci'];

                // Insert ke bs_soal siakad
                try {
                    $stmtInsBs->execute([$topikId, $pId, $mapel, $kelas, $no, $qText, $a, $b, $c, $d, $e, $kunci]);
                    if ($stmtInsBsCore) {
                        $stmtInsBsCore->execute([$topikId, $pId, $mapel, $kelas, $no, $qText, $a, $b, $c, $d, $e, $kunci]);
                    }
                } catch (\Exception $e) {}

                // Insert ke cbt_soal jika ujian_id ditemukan
                if ($ujianId > 0) {
                    try {
                        $stmtInsCbt->execute([$ujianId, $no, $qText, $a, $b, $c, $d, $e, $kunci]);
                    } catch (\Exception $e) {}
                }

                $no++;
                $totalInserted++;
            }
        }

        return $totalInserted;
    }

    /**
     * Ambil 20 pertanyaan untuk mapel tertentu
     */
    private static function getQuestionsForMapel($bank, $mapel, $level) {
        // Cari exact match atau sub-match
        $matchedKey = null;
        foreach (array_keys($bank) as $k) {
            if (strcasecmp($k, $mapel) === 0 || stripos($mapel, $k) !== false || stripos($k, $mapel) !== false) {
                $matchedKey = $k;
                break;
            }
        }

        if ($matchedKey && isset($bank[$matchedKey])) {
            $pool = $bank[$matchedKey];
        } else {
            // Fallback: soal pengetahuan umum madrasah
            $pool = self::getDefaultQuestions($mapel);
        }

        // Pastikan pas 20 butir
        if (count($pool) < 20) {
            $filler = self::getDefaultQuestions($mapel);
            while (count($pool) < 20) {
                $item = array_shift($filler);
                $pool[] = $item;
            }
        }

        return array_slice($pool, 0, 20);
    }

    /**
     * Database 20 Butir Soal Mudah untuk Seluruh Mata Pelajaran
     */
    public static function getMasterBankSoal() {
        return [
            'Akidah Akhlak' => [
                ['pertanyaan' => 'Rukun Iman yang pertama yang wajib diyakini setiap muslim adalah iman kepada...', 'opsi_a' => 'Malaikat', 'opsi_b' => 'Allah SWT', 'opsi_c' => 'Kitab-kitab Allah', 'opsi_d' => 'Rasul-rasul Allah', 'opsi_e' => 'Hari Akhir', 'kunci' => 'B'],
                ['pertanyaan' => 'Sifat wajib bagi Allah yang memiliki arti "Maha Mengetahui" adalah...', 'opsi_a' => 'Al-Alim', 'opsi_b' => 'Al-Bashir', 'opsi_c' => 'As-Sami\'', 'opsi_d' => 'Al-Hayyu', 'opsi_e' => 'Al-Qudrah', 'kunci' => 'A'],
                ['pertanyaan' => 'Sikap berserah diri sepenuhnya kepada Allah SWT setelah berikhtiar dengan sungguh-sungguh dinamakan...', 'opsi_a' => 'Qana\'ah', 'opsi_b' => 'Tawakal', 'opsi_c' => 'Ikhlas', 'opsi_d' => 'Sabar', 'opsi_e' => 'Syukur', 'kunci' => 'B'],
                ['pertanyaan' => 'Jumlah Asmaul Husna yang masyhur bagi Allah SWT berjumlah...', 'opsi_a' => '25', 'opsi_b' => '50', 'opsi_c' => '99', 'opsi_d' => '100', 'opsi_e' => '114', 'kunci' => 'C'],
                ['pertanyaan' => 'Perilaku memperlihatkan amal ibadah kepada orang lain agar mendapat pujian dinamakan...', 'opsi_a' => 'Riya\'', 'opsi_b' => 'Sum\'ah', 'opsi_c' => 'Ujub', 'opsi_d' => 'Takabur', 'opsi_e' => 'Hasad', 'kunci' => 'A'],
                ['pertanyaan' => 'Sifat rasul yang berarti dapat dipercaya dan memegang amanah adalah...', 'opsi_a' => 'Siddiq', 'opsi_b' => 'Amanah', 'opsi_c' => 'Tabligh', 'opsi_d' => 'Fathanah', 'opsi_e' => 'Adil', 'kunci' => 'B'],
                ['pertanyaan' => 'Malaikat yang bertugas meniup sangkakala pada hari kiamat adalah Malaikat...', 'opsi_a' => 'Jibril', 'opsi_b' => 'Mikail', 'opsi_c' => 'Israfil', 'opsi_d' => 'Izrail', 'opsi_e' => 'Ridwan', 'kunci' => 'C'],
                ['pertanyaan' => 'Memohon ampunan kepada Allah SWT atas segala dosa yang telah dilakukan disebut...', 'opsi_a' => 'Tasbih', 'opsi_b' => 'Tahmid', 'opsi_c' => 'Istighfar', 'opsi_d' => 'Tahlil', 'opsi_e' => 'Takbir', 'kunci' => 'C'],
                ['pertanyaan' => 'Sikap rela menerima ketentuan Allah dan tidak iri atas nikmat yang diperoleh orang lain disebut...', 'opsi_a' => 'Qana\'ah', 'opsi_b' => 'Tawadhu\'', 'opsi_c' => 'Ta\'awun', 'opsi_d' => 'Tasamuh', 'opsi_e' => 'Husnudzan', 'kunci' => 'A'],
                ['pertanyaan' => 'Sikap rendah hati dan tidak menyombongkan diri di hadapan manusia disebut...', 'opsi_a' => 'Tawadhu\'', 'opsi_b' => 'Takabur', 'opsi_c' => 'Ghibah', 'opsi_d' => 'Namimah', 'opsi_e' => 'Riya\'', 'kunci' => 'A'],
                ['pertanyaan' => 'Salah satu tanda orang beriman ketika mendengar asma Allah adalah...', 'opsi_a' => 'Hatinya bergetar', 'opsi_b' => 'Merasa takut berlebihan', 'opsi_c' => 'Meninggalkan pekerjaan', 'opsi_d' => 'Menjadi sombong', 'opsi_e' => 'Berdiam diri tanpa bergerak', 'kunci' => 'A'],
                ['pertanyaan' => 'Lawan dari sifat jujur (siddiq) adalah...', 'opsi_a' => 'Kizib (dusta)', 'opsi_b' => 'Khianat', 'opsi_c' => 'Kitman', 'opsi_d' => 'Baladah', 'opsi_e' => 'Fasiq', 'kunci' => 'A'],
                ['pertanyaan' => 'Sikap berbaik sangka kepada Allah dan kepada sesama manusia dinamakan...', 'opsi_a' => 'Su\'udzan', 'opsi_b' => 'Husnudzan', 'opsi_c' => 'Ghibah', 'opsi_d' => 'Fitnah', 'opsi_e' => 'Hasad', 'kunci' => 'B'],
                ['pertanyaan' => 'Sikap toleransi dan menghargai perbedaan keyakinan dalam batasan yang dibenarkan syariat disebut...', 'opsi_a' => 'Tasamuh', 'opsi_b' => 'Ta\'awun', 'opsi_c' => 'Tafahum', 'opsi_d' => 'Takaful', 'opsi_e' => 'Tadabbur', 'kunci' => 'A'],
                ['pertanyaan' => 'Malaikat yang bertugas mencatat amal perbuatan baik manusia adalah Malaikat...', 'opsi_a' => 'Raqib', 'opsi_b' => 'Atid', 'opsi_c' => 'Malik', 'opsi_d' => 'Ridwan', 'opsi_e' => 'Munkar', 'kunci' => 'A'],
                ['pertanyaan' => 'Hari dibangkitkannya seluruh manusia dari alam kubur setelah kiamat disebut...', 'opsi_a' => 'Yaumul Ba\'ats', 'opsi_b' => 'Yaumul Mahsyar', 'opsi_c' => 'Yaumul Hisab', 'opsi_d' => 'Yaumul Mizan', 'opsi_e' => 'Yaumul Jaza\'', 'kunci' => 'A'],
                ['pertanyaan' => 'Menahan diri dari amarah dan hawa nafsu ketika ditimpa musibah merupakan cerminan dari sikap...', 'opsi_a' => 'Sabar', 'opsi_b' => 'Syukur', 'opsi_c' => 'Ikhlas', 'opsi_d' => 'Zuhud', 'opsi_e' => 'Wara\'', 'kunci' => 'A'],
                ['pertanyaan' => 'Berbakti kepada kedua orang tua dalam istilah Islam dikenal dengan...', 'opsi_a' => 'Birrul Walidain', 'opsi_b' => 'Uququl Walidain', 'opsi_c' => 'Silaturahim', 'opsi_d' => 'Ukhuwah Islamiyah', 'opsi_e' => 'Muamalah', 'kunci' => 'A'],
                ['pertanyaan' => 'Dosa besar yang tidak akan diampuni oleh Allah jika pelakunya mati sebelum bertaubat adalah...', 'opsi_a' => 'Syirik', 'opsi_b' => 'Mencuri', 'opsi_c' => 'Berbohong', 'opsi_d' => 'Durhaka', 'opsi_e' => 'Ghibah', 'kunci' => 'A'],
                ['pertanyaan' => 'Kalimat thoyyibah yang diucapkan saat kagum terhadap keindahan ciptaan Allah adalah...', 'opsi_a' => 'Subhanallah', 'opsi_b' => 'Alhamdulillah', 'opsi_c' => 'Allahu Akbar', 'opsi_d' => 'Astaghfirullah', 'opsi_e' => 'La ilaha illallah', 'kunci' => 'A'],
            ],
            'Al-Qur\'an Hadits' => [
                ['pertanyaan' => 'Wahyu Al-Qur\'an yang pertama kali diturunkan kepada Nabi Muhammad SAW adalah Surah...', 'opsi_a' => 'Al-Fatihah ayat 1-7', 'opsi_b' => 'Al-Alaq ayat 1-5', 'opsi_c' => 'Al-Muddatsir ayat 1-7', 'opsi_d' => 'Al-Baqarah ayat 1-5', 'opsi_e' => 'Al-Ikhlas ayat 1-4', 'kunci' => 'B'],
                ['pertanyaan' => 'Al-Qur\'an terdiri dari berapa juz dan berapa surah...', 'opsi_a' => '30 Juz dan 114 Surah', 'opsi_b' => '30 Juz dan 112 Surah', 'opsi_c' => '25 Juz dan 114 Surah', 'opsi_d' => '30 Juz dan 120 Surah', 'opsi_e' => '28 Juz dan 110 Surah', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum membaca nun sukun atau tanwin yang bertemu dengan huruf Ba (ب) adalah...', 'opsi_a' => 'Iqlab', 'opsi_b' => 'Idgham Bighunnah', 'opsi_c' => 'Idzhar Halqi', 'opsi_d' => 'Ikhfa Haqiqi', 'opsi_e' => 'Ghunnah', 'kunci' => 'A'],
                ['pertanyaan' => 'Secara bahasa, kata "Hadits" memiliki arti...', 'opsi_a' => 'Baru atau perkataan', 'opsi_b' => 'Lama atau kuno', 'opsi_c' => 'Wahyu langsung', 'opsi_d' => 'Perjanjian', 'opsi_e' => 'Peraturan', 'kunci' => 'A'],
                ['pertanyaan' => 'Unsur hadits yang berupa rangkaian orang-orang yang meriwayatkan hadits disebut...', 'opsi_a' => 'Sanad', 'opsi_b' => 'Matan', 'opsi_c' => 'Rawi', 'opsi_d' => 'Mukharrij', 'opsi_e' => 'Rijal', 'kunci' => 'A'],
                ['pertanyaan' => 'Isi materi atau redaksi perkataan Nabi Muhammad SAW dalam hadits disebut...', 'opsi_a' => 'Matan', 'opsi_b' => 'Sanad', 'opsi_c' => 'Musnad', 'opsi_d' => 'Isnad', 'opsi_e' => 'Tarikh', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum nun sukun atau tanwin dibaca jelas tanpa mendengung jika bertemu huruf halqi disebut...', 'opsi_a' => 'Idzhar Halqi', 'opsi_b' => 'Idgham Bilaghunnah', 'opsi_c' => 'Ikhfa', 'opsi_d' => 'Iqlab', 'opsi_e' => 'Qalqalah', 'kunci' => 'A'],
                ['pertanyaan' => 'Berikut ini yang termasuk huruf Qalqalah adalah...', 'opsi_a' => 'ق - ط - ب - ج - د', 'opsi_b' => 'ا - ب - ت - ث - ج', 'opsi_c' => 'ي - ن - م - و', 'opsi_d' => 'ء - هـ - ع - غ - ح - خ', 'opsi_e' => 'ل - ر', 'kunci' => 'A'],
                ['pertanyaan' => 'Surah dalam Al-Qur\'an yang dijuluki sebagai "Ummul Kitab" (Induk Al-Qur\'an) adalah Surah...', 'opsi_a' => 'Al-Fatihah', 'opsi_b' => 'Yasin', 'opsi_c' => 'Al-Baqarah', 'opsi_d' => 'Al-Ikhlas', 'opsi_e' => 'Ar-Rahman', 'kunci' => 'A'],
                ['pertanyaan' => 'Hadits yang diriwayatkan oleh orang banyak pada setiap generasinya sehingga mustahil berbohong disebut hadits...', 'opsi_a' => 'Mutawatir', 'opsi_b' => 'Ahad', 'opsi_c' => 'Shahih', 'opsi_d' => 'Hasan', 'opsi_e' => 'Dhaif', 'kunci' => 'A'],
                ['pertanyaan' => 'Surah terpanjang di dalam Al-Qur\'an adalah Surah...', 'opsi_a' => 'Al-Baqarah', 'opsi_b' => 'Ali \'Imran', 'opsi_c' => 'An-Nisa\'', 'opsi_d' => 'Al-Ma\'idah', 'opsi_e' => 'Al-An\'am', 'kunci' => 'A'],
                ['pertanyaan' => 'Tempat turunnya wahyu Al-Qur\'an pertama kali adalah di...', 'opsi_a' => 'Gua Hira', 'opsi_b' => 'Gua Tsur', 'opsi_c' => 'Jabal Uhud', 'opsi_d' => 'Masjidil Haram', 'opsi_e' => 'Padang Arafah', 'kunci' => 'A'],
                ['pertanyaan' => 'Kitab kumpulan hadits shahih yang disusun oleh Imam Bukhari dinamakan...', 'opsi_a' => 'Shahih Bukhari', 'opsi_b' => 'Sunan Abu Dawud', 'opsi_c' => 'Muwatta\'', 'opsi_d' => 'Riyadhus Shalihin', 'opsi_e' => 'Al-Musnad', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum bacaan mim sukun bertemu huruf selain mim dan ba adalah...', 'opsi_a' => 'Idzhar Syafawi', 'opsi_b' => 'Ikhfa Syafawi', 'opsi_c' => 'Idgham Mimi', 'opsi_d' => 'Iqlab', 'opsi_e' => 'Idzhar Halqi', 'kunci' => 'A'],
                ['pertanyaan' => 'Malaikat yang bertugas menyampaikan wahyu Al-Qur\'an kepada Rasulullah SAW adalah...', 'opsi_a' => 'Jibril', 'opsi_b' => 'Mikail', 'opsi_c' => 'Israfil', 'opsi_d' => 'Izrail', 'opsi_e' => 'Malik', 'kunci' => 'A'],
                ['pertanyaan' => 'Surah-surah Al-Qur\'an yang diturunkan sebelum Nabi Muhammad SAW hijrah ke Madinah disebut...', 'opsi_a' => 'Makkiyah', 'opsi_b' => 'Madaniyah', 'opsi_c' => 'Qudsiyyah', 'opsi_d' => 'Nabawiyyah', 'opsi_e' => 'Misriyyah', 'kunci' => 'A'],
                ['pertanyaan' => 'Arti dari lafaz "Iqra\'" pada wahyu pertama adalah...', 'opsi_a' => 'Bacalah', 'opsi_b' => 'Tulislah', 'opsi_c' => 'Dengarkanlah', 'opsi_d' => 'Bangkitlah', 'opsi_e' => 'Sampaikanlah', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum memanjangkan bacaan huruf dalam ilmu tajwid disebut dengan hukum...', 'opsi_a' => 'Mad', 'opsi_b' => 'Ghunnah', 'opsi_c' => 'Waqaf', 'opsi_d' => 'Washal', 'opsi_e' => 'Saktah', 'kunci' => 'A'],
                ['pertanyaan' => 'Tanda berhenti membaca dalam Al-Qur\'an dinamakan tanda...', 'opsi_a' => 'Waqaf', 'opsi_b' => 'Washal', 'opsi_c' => 'Saktah', 'opsi_d' => 'Tanwin', 'opsi_e' => 'Sukun', 'kunci' => 'A'],
                ['pertanyaan' => 'Tujuan utama Al-Qur\'an diturunkan kepada seluruh umat manusia adalah sebagai...', 'opsi_a' => 'Petunjuk hidup (Hudan)', 'opsi_b' => 'Penghias ruangan', 'opsi_c' => 'Cerita masa lalu belaka', 'opsi_d' => 'Bahan perdebatan', 'opsi_e' => 'Hafalan tanpa dipahami', 'kunci' => 'A'],
            ],
            'Bahasa Arab' => [
                ['pertanyaan' => 'Arti dari kata "فَصْلٌ" (Fashlun) dalam bahasa Indonesia adalah...', 'opsi_a' => 'Kelas', 'opsi_b' => 'Meja', 'opsi_c' => 'Buku', 'opsi_d' => 'Pena', 'opsi_e' => 'Sekolah', 'kunci' => 'A'],
                ['pertanyaan' => 'Jawaban yang tepat untuk sapaan "صَبَاحَ الْخَيْرِ" (Shobaahal khair) adalah...', 'opsi_a' => 'صَبَاحَ النُّوْرِ (Shobaahan nuur)', 'opsi_b' => 'مَسَاءَ الْخَيْرِ', 'opsi_c' => 'أَهْلًا وَسَهْلًا', 'opsi_d' => 'مَعَ السَّلَامَةِ', 'opsi_e' => 'شُكْرًا', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata ganti orang pertama tunggal "Saya" dalam bahasa Arab adalah...', 'opsi_a' => 'أَنَا (Ana)', 'opsi_b' => 'أَنْتَ (Anta)', 'opsi_c' => 'هُوَ (Huwa)', 'opsi_d' => 'هِيَ (Hiya)', 'opsi_e' => 'نَحْنُ (Nahnu)', 'kunci' => 'A'],
                ['pertanyaan' => 'Bahasa Arab dari benda "Buku tulis" adalah...', 'opsi_a' => 'دَفْتَرٌ (Daftarun)', 'opsi_b' => 'قَلَمٌ (Qolamun)', 'opsi_c' => 'كِتَابٌ (Kitaabun)', 'opsi_d' => 'مِسْطَرَةٌ (Mistorotun)', 'opsi_e' => 'حَقِيْبَةٌ (Haqiibatun)', 'kunci' => 'A'],
                ['pertanyaan' => 'Arti dari kalimat tanya "مَا هَذَا؟" (Maa haadzaa?) adalah...', 'opsi_a' => 'Apa ini?', 'opsi_b' => 'Siapa kamu?', 'opsi_c' => 'Di mana kamu?', 'opsi_d' => 'Kapan datang?', 'opsi_e' => 'Bagaimana kabarmu?', 'kunci' => 'A'],
                ['pertanyaan' => 'Angka "خَمْسَةٌ" (Khamsatun) dalam bahasa Indonesia berarti angka...', 'opsi_a' => '5', 'opsi_b' => '3', 'opsi_c' => '7', 'opsi_d' => '9', 'opsi_e' => '10', 'kunci' => 'A'],
                ['pertanyaan' => 'Bahasa Arab untuk ruangan "Perpustakaan" di sekolah adalah...', 'opsi_a' => 'مَكْتَبَةٌ (Maktabatun)', 'opsi_b' => 'مَسْجِدٌ (Masjidun)', 'opsi_c' => 'مَيْدَانٌ (Maidaanun)', 'opsi_d' => 'مَقْصَفٌ (Maqshofun)', 'opsi_e' => 'حَمَّامٌ (Hammaamun)', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata "طَالِبٌ" (Thoolibun) memiliki arti...', 'opsi_a' => 'Siswa/Murid', 'opsi_b' => 'Guru', 'opsi_c' => 'Kepala Madrasah', 'opsi_d' => 'Satpam', 'opsi_e' => 'Penjaga', 'kunci' => 'A'],
                ['pertanyaan' => 'Ucapan terima kasih dalam bahasa Arab adalah...', 'opsi_a' => 'شُكْرًا (Syukran)', 'opsi_b' => 'عَفْوًا (Afwan)', 'opsi_c' => 'أَهْلًا (Ahlan)', 'opsi_d' => 'مَرْحَبًا (Marhaban)', 'opsi_e' => 'إِلَى اللِّقَاءِ', 'kunci' => 'A'],
                ['pertanyaan' => 'Lawan kata dari "كَبِيْرٌ" (Kabiirun / Besar) adalah...', 'opsi_a' => 'صَغِيْرٌ (Shoghiirun)', 'opsi_b' => 'طَوِيْلٌ (Thowiilun)', 'opsi_c' => 'قَصِيْرٌ (Qoshiirun)', 'opsi_d' => 'جَدِيْدٌ (Jadiidun)', 'opsi_e' => 'قَدِيْمٌ (Qodiimun)', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata tunjuk untuk benda dekat jenis perempuan (Muannats) adalah...', 'opsi_a' => 'هَذِهِ (Haadzihi)', 'opsi_b' => 'هَذَا (Haadzaa)', 'opsi_c' => 'ذَلِكَ (Dzaalika)', 'opsi_d' => 'تِلْكَ (Tilka)', 'opsi_e' => 'هُنَا (Hunaa)', 'kunci' => 'A'],
                ['pertanyaan' => 'Arti dari kata "مَدْرَسَةٌ" (Madrasatun) adalah...', 'opsi_a' => 'Sekolah / Madrasah', 'opsi_b' => 'Rumah', 'opsi_c' => 'Masjid', 'opsi_d' => 'Pasar', 'opsi_e' => 'Kantor', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata ganti "Dia laki-laki" dalam bahasa Arab adalah...', 'opsi_a' => 'هُوَ (Huwa)', 'opsi_b' => 'هِيَ (Hiya)', 'opsi_c' => 'أَنْتَ (Anta)', 'opsi_d' => 'أَنْتِ (Anti)', 'opsi_e' => 'هُمْ (Hum)', 'kunci' => 'A'],
                ['pertanyaan' => 'Kalimat "كَيْفَ حَالُكَ؟" (Kaifa haaluka?) digunakan untuk menanyakan...', 'opsi_a' => 'Kabar kesehatan', 'opsi_b' => 'Nama seseorang', 'opsi_c' => 'Alamat rumah', 'opsi_d' => 'Waktu / jam', 'opsi_e' => 'Harga barang', 'kunci' => 'A'],
                ['pertanyaan' => 'Benda yang digunakan untuk menulis di papan tulis adalah...', 'opsi_a' => 'طَبَاشِيْرُ (Thobaasyiir)', 'opsi_b' => 'مِمْسَحَةٌ (Mimsahatun)', 'opsi_c' => 'مِسْطَرَةٌ (Mistorotun)', 'opsi_d' => 'مِقْلَمَةٌ (Miqlamatun)', 'opsi_e' => 'كُرْسِيٌّ (Kursiyyun)', 'kunci' => 'A'],
                ['pertanyaan' => 'Warna "أَبْيَضُ" (Abyadh) dalam bahasa Indonesia berarti warna...', 'opsi_a' => 'Putih', 'opsi_b' => 'Hitam', 'opsi_c' => 'Merah', 'opsi_d' => 'Hijau', 'opsi_e' => 'Biru', 'kunci' => 'A'],
                ['pertanyaan' => 'Hari "الأَحَد" (Al-Ahad) dalam bahasa Indonesia adalah hari...', 'opsi_a' => 'Minggu', 'opsi_b' => 'Senin', 'opsi_c' => 'Selasa', 'opsi_d' => 'Rabu', 'opsi_e' => 'Kamis', 'kunci' => 'A'],
                ['pertanyaan' => 'Bahasa Arab dari kata "Pintu" adalah...', 'opsi_a' => 'بَابٌ (Baabun)', 'opsi_b' => 'نَافِذَةٌ (Naafidzatun)', 'opsi_c' => 'جِدَارٌ (Jidaarun)', 'opsi_d' => 'سَقْفٌ (Saqfun)', 'opsi_e' => 'بِلَاطٌ (Bilaathun)', 'kunci' => 'A'],
                ['pertanyaan' => 'Angka "عَشَرَةٌ" (Asyaratun) melambangkan bilangan...', 'opsi_a' => '10', 'opsi_b' => '1', 'opsi_c' => '100', 'opsi_d' => '20', 'opsi_e' => '5', 'kunci' => 'A'],
                ['pertanyaan' => 'Arti dari kata "أُسْتَاذٌ" (Ustadzun) adalah...', 'opsi_a' => 'Guru / Pendidik', 'opsi_b' => 'Dokter', 'opsi_c' => 'Polisi', 'opsi_d' => 'Pedagang', 'opsi_e' => 'Petani', 'kunci' => 'A'],
            ],
            'Bahasa Indonesia' => [
                ['pertanyaan' => 'Gagasan utama yang mendasari sebuah paragraf disebut dengan...', 'opsi_a' => 'Ide Pokok / Gagasan Utama', 'opsi_b' => 'Kalimat Penjelas', 'opsi_c' => 'Kesimpulan Akhir', 'opsi_d' => 'Latar Cerita', 'opsi_e' => 'Amanat', 'kunci' => 'A'],
                ['pertanyaan' => 'Lawan kata (antonim) dari kata "optimis" adalah...', 'opsi_a' => 'Pesimis', 'opsi_b' => 'Realis', 'opsi_c' => 'Dinamis', 'opsi_d' => 'Kritis', 'opsi_e' => 'Skeptis', 'kunci' => 'A'],
                ['pertanyaan' => 'Penulisan kata baku yang benar menurut KBBI di bawah ini adalah...', 'opsi_a' => 'Apotek', 'opsi_b' => 'Apotik', 'opsi_c' => 'Apotike', 'opsi_d' => 'Apotheek', 'opsi_e' => 'Apotikh', 'kunci' => 'A'],
                ['pertanyaan' => 'Kalimat yang disusun sesuai dengan kaidah tata bahasa yang berlaku dan mudah dipahami disebut kalimat...', 'opsi_a' => 'Efektif', 'opsi_b' => 'Majemuk', 'opsi_c' => 'Tanya', 'opsi_d' => 'Perintah', 'opsi_e' => 'Tak langsung', 'kunci' => 'A'],
                ['pertanyaan' => 'Tanda baca yang digunakan untuk mengakhiri kalimat berita adalah tanda...', 'opsi_a' => 'Titik (.)', 'opsi_b' => 'Koma (,)', 'opsi_c' => 'Tanya (?)', 'opsi_d' => 'Seru (!)', 'opsi_e' => 'Titik dua (:)', 'kunci' => 'A'],
                ['pertanyaan' => 'Teks yang berisi pemaparan hasil pengamatan secara sistematis dan objektif disebut teks...', 'opsi_a' => 'Laporan Hasil Observasi', 'opsi_b' => 'Narasi Fiksi', 'opsi_c' => 'Puisi', 'opsi_d' => 'Drama', 'opsi_e' => 'Cerpen', 'kunci' => 'A'],
                ['pertanyaan' => 'Gaya bahasa yang membandingkan benda mati seolah-olah memiliki sifat seperti manusia disebut majas...', 'opsi_a' => 'Personifikasi', 'opsi_b' => 'Metafora', 'opsi_c' => 'Hiperbola', 'opsi_d' => 'Litotes', 'opsi_e' => 'Ironi', 'kunci' => 'A'],
                ['pertanyaan' => 'Persamaan kata (sinonim) dari kata "sukses" adalah...', 'opsi_a' => 'Berhasil', 'opsi_b' => 'Gagal', 'opsi_c' => 'Runtuh', 'opsi_d' => 'Mundur', 'opsi_e' => 'Kandas', 'kunci' => 'A'],
                ['pertanyaan' => 'Pesan moral yang ingin disampaikan pengarang kepada pembaca dalam sebuah karya sastra dinamakan...', 'opsi_a' => 'Amanat', 'opsi_b' => 'Alur', 'opsi_c' => 'Tema', 'opsi_d' => 'Sudut Pandang', 'opsi_e' => 'Perwatakan', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata penghubung yang menyatakan hubungan sebab-akibat adalah...', 'opsi_a' => 'Karena / Sehingga', 'opsi_b' => 'Dan', 'opsi_c' => 'Atau', 'opsi_d' => 'Tetapi', 'opsi_e' => 'Meskipun', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata serapan yang benar dalam bahasa Indonesia untuk kata "activity" adalah...', 'opsi_a' => 'Aktivitas', 'opsi_b' => 'Aktifitas', 'opsi_c' => 'Aktivitasi', 'opsi_d' => 'Aktipitas', 'opsi_e' => 'Aktifity', 'kunci' => 'A'],
                ['pertanyaan' => 'Teks yang bertujuan meyakinkan pembaca melalui argumen dan fakta disebut teks...', 'opsi_a' => 'Eksposisi / Argumentasi', 'opsi_b' => 'Deskripsi', 'opsi_c' => 'Narasi', 'opsi_d' => 'Prosedur', 'opsi_e' => 'Anekdot', 'kunci' => 'A'],
                ['pertanyaan' => 'Bagian awal dari sebuah cerita pendek yang mengenalkan tokoh dan latar dinamakan...', 'opsi_a' => 'Orientasi', 'opsi_b' => 'Komplikasi', 'opsi_c' => 'Resolusi', 'opsi_d' => 'Koda', 'opsi_e' => 'Evaluasi', 'kunci' => 'A'],
                ['pertanyaan' => 'Berikut ini kalimat yang memuat keterangan waktu adalah...', 'opsi_a' => 'Siswa masuk ke kelas pukul 07.00 WIB.', 'opsi_b' => 'Siswa belajar dengan sangat tekun.', 'opsi_c' => 'Guru menulis di papan tulis putih.', 'opsi_d' => 'Buku itu bersampul warna biru.', 'opsi_e' => 'Mereka berjalan di lapangan hijau.', 'kunci' => 'A'],
                ['pertanyaan' => 'Langkah-langkah membuat sesuatu secara berurutan dan jelas tertuang dalam jenis teks...', 'opsi_a' => 'Prosedur', 'opsi_b' => 'Laporan', 'opsi_c' => 'Ekplanasi', 'opsi_d' => 'Biografi', 'opsi_e' => 'Puisi', 'kunci' => 'A'],
                ['pertanyaan' => 'Penulisan huruf kapital yang benar di bawah ini adalah...', 'opsi_a' => 'Presiden Joko Widodo mengunjungi Kota Solo.', 'opsi_b' => 'presiden joko widodo mengunjungi kota solo.', 'opsi_c' => 'Presiden joko widodo mengunjungi kota Solo.', 'opsi_d' => 'presiden Joko Widodo mengunjungi Kota solo.', 'opsi_e' => 'Presiden Joko Widodo mengunjungi kota Solo.', 'kunci' => 'A'],
                ['pertanyaan' => 'Kata dasar dari kata berimbuhan "memperjuangkan" adalah...', 'opsi_a' => 'Juang', 'opsi_b' => 'Pejuang', 'opsi_c' => 'Perjuang', 'opsi_d' => 'Berjuang', 'opsi_e' => 'Juangan', 'kunci' => 'A'],
                ['pertanyaan' => 'Kalimat yang mengandung ungkapan melebih-lebihkan kenyataan disebut bermajas...', 'opsi_a' => 'Hiperbola', 'opsi_b' => 'Litotes', 'opsi_c' => 'Personifikasi', 'opsi_d' => 'Metafora', 'opsi_e' => 'Simile', 'kunci' => 'A'],
                ['pertanyaan' => 'Daftar buku yang dijadikan rujukan pada akhir sebuah karya tulis ilmiah disebut...', 'opsi_a' => 'Daftar Pustaka', 'opsi_b' => 'Daftar Isi', 'opsi_c' => 'Kata Pengantar', 'opsi_d' => 'Lampiran', 'opsi_e' => 'Glosarium', 'kunci' => 'A'],
                ['pertanyaan' => 'Inti sari dari keseluruhan isi teks yang dibuat secara singkat dan padat disebut...', 'opsi_a' => 'Ringkasan / Rangkuman', 'opsi_b' => 'Kutipan', 'opsi_c' => 'Paragraf', 'opsi_d' => 'Subjudul', 'opsi_e' => 'Catatan Kaki', 'kunci' => 'A'],
            ],
            'Bahasa Inggris' => [
                ['pertanyaan' => 'What is the appropriate response to the greeting "Good morning"?', 'opsi_a' => 'Good morning', 'opsi_b' => 'Good night', 'opsi_c' => 'Good bye', 'opsi_d' => 'You are welcome', 'opsi_e' => 'Thank you', 'kunci' => 'A'],
                ['pertanyaan' => 'Complete the sentence: "She ... to school by bicycle every day."', 'opsi_a' => 'goes', 'opsi_b' => 'go', 'opsi_c' => 'gone', 'opsi_d' => 'going', 'opsi_e' => 'is go', 'kunci' => 'A'],
                ['pertanyaan' => 'What is the opposite meaning (antonym) of the word "clean"?', 'opsi_a' => 'Dirty', 'opsi_b' => 'Tidy', 'opsi_c' => 'Pure', 'opsi_d' => 'Bright', 'opsi_e' => 'Fresh', 'kunci' => 'A'],
                ['pertanyaan' => 'Which of the following is a classroom object used for writing?', 'opsi_a' => 'Pen', 'opsi_b' => 'Chair', 'opsi_c' => 'Desk', 'opsi_d' => 'Door', 'opsi_e' => 'Window', 'kunci' => 'A'],
                ['pertanyaan' => 'What day comes after Tuesday?', 'opsi_a' => 'Wednesday', 'opsi_b' => 'Monday', 'opsi_c' => 'Thursday', 'opsi_d' => 'Friday', 'opsi_e' => 'Saturday', 'kunci' => 'A'],
                ['pertanyaan' => 'Complete the sentence: "I ... very happy today because of the test result."', 'opsi_a' => 'am', 'opsi_b' => 'is', 'opsi_c' => 'are', 'opsi_d' => 'were', 'opsi_e' => 'be', 'kunci' => 'A'],
                ['pertanyaan' => 'What is the English word for "Perpustakaan"?', 'opsi_a' => 'Library', 'opsi_b' => 'Laboratory', 'opsi_c' => 'Canteen', 'opsi_d' => 'Field', 'opsi_e' => 'Office', 'kunci' => 'A'],
                ['pertanyaan' => 'Choose the correct plural form of "book":', 'opsi_a' => 'Books', 'opsi_b' => 'Bookes', 'opsi_c' => 'Bookies', 'opsi_d' => 'Booken', 'opsi_e' => 'Book', 'kunci' => 'A'],
                ['pertanyaan' => 'What is the question word used to ask about a person?', 'opsi_a' => 'Who', 'opsi_b' => 'Where', 'opsi_c' => 'When', 'opsi_d' => 'Why', 'opsi_e' => 'What', 'kunci' => 'A'],
                ['pertanyaan' => 'Complete the dialog: "Thank you for your help." - "..."', 'opsi_a' => 'You are welcome', 'opsi_b' => 'Never mind', 'opsi_c' => 'I am fine', 'opsi_d' => 'Good luck', 'opsi_e' => 'See you', 'kunci' => 'A'],
                ['pertanyaan' => 'What is the past tense (V2) of the verb "eat"?', 'opsi_a' => 'Ate', 'opsi_b' => 'Eaten', 'opsi_c' => 'Eats', 'opsi_d' => 'Eating', 'opsi_e' => 'Eated', 'kunci' => 'A'],
                ['pertanyaan' => 'How many months are there in one year?', 'opsi_a' => 'Twelve (12)', 'opsi_b' => 'Ten (10)', 'opsi_c' => 'Eleven (11)', 'opsi_d' => 'Thirteen (13)', 'opsi_e' => 'Seven (7)', 'kunci' => 'A'],
                ['pertanyaan' => 'Which animal is known as the "King of the Jungle"?', 'opsi_a' => 'Lion', 'opsi_b' => 'Cat', 'opsi_c' => 'Elephant', 'opsi_d' => 'Rabbit', 'opsi_e' => 'Cow', 'kunci' => 'A'],
                ['pertanyaan' => '"My mother is a teacher. She works in a ..."', 'opsi_a' => 'School', 'opsi_b' => 'Hospital', 'opsi_c' => 'Bank', 'opsi_d' => 'Airport', 'opsi_e' => 'Restaurant', 'kunci' => 'A'],
                ['pertanyaan' => 'What is the opposite of "hot"?', 'opsi_a' => 'Cold', 'opsi_b' => 'Warm', 'opsi_c' => 'Sunny', 'opsi_d' => 'Boiling', 'opsi_e' => 'Dry', 'kunci' => 'A'],
                ['pertanyaan' => 'Complete the sentence: "They ... playing football in the school yard right now."', 'opsi_a' => 'are', 'opsi_b' => 'is', 'opsi_c' => 'am', 'opsi_d' => 'was', 'opsi_e' => 'be', 'kunci' => 'A'],
                ['pertanyaan' => 'What color do you get when you mix blue and yellow?', 'opsi_a' => 'Green', 'opsi_b' => 'Purple', 'opsi_c' => 'Orange', 'opsi_d' => 'Black', 'opsi_e' => 'Brown', 'kunci' => 'A'],
                ['pertanyaan' => 'Choose the synonym of the word "smart":', 'opsi_a' => 'Clever', 'opsi_b' => 'Lazy', 'opsi_c' => 'Slow', 'opsi_d' => 'Angry', 'opsi_e' => 'Sad', 'kunci' => 'A'],
                ['pertanyaan' => 'Complete the sentence: "I want to drink because I am ..."', 'opsi_a' => 'Thirsty', 'opsi_b' => 'Hungry', 'opsi_c' => 'Sleepy', 'opsi_d' => 'Tired', 'opsi_e' => 'Bored', 'kunci' => 'A'],
                ['pertanyaan' => 'Which word is a polite expression to ask for permission?', 'opsi_a' => 'May I...?', 'opsi_b' => 'Give me!', 'opsi_c' => 'I want!', 'opsi_d' => 'Hurry up!', 'opsi_e' => 'Stop that!', 'kunci' => 'A'],
            ],
            'Biologi' => [
                ['pertanyaan' => 'Organel sel yang berfungsi sebagai tempat penghasil energi (respirasi sel) adalah...', 'opsi_a' => 'Mitokondria', 'opsi_b' => 'Ribosom', 'opsi_c' => 'Badan Golgi', 'opsi_d' => 'Lisosom', 'opsi_e' => 'Vakuola', 'kunci' => 'A'],
                ['pertanyaan' => 'Zat hijau daun yang berperan penting dalam proses fotosintesis adalah...', 'opsi_a' => 'Klorofil', 'opsi_b' => 'Karotenoid', 'opsi_c' => 'Antosianin', 'opsi_d' => 'Xantofil', 'opsi_e' => 'Melanin', 'kunci' => 'A'],
                ['pertanyaan' => 'Organ utama pada sistem pernapasan manusia tempat pertukaran oksigen dan karbondioksida adalah...', 'opsi_a' => 'Paru-paru (Alveolus)', 'opsi_b' => 'Jantung', 'opsi_c' => 'Hati', 'opsi_d' => 'Ginjal', 'opsi_e' => 'Lambung', 'kunci' => 'A'],
                ['pertanyaan' => 'Dalam rantai makanan, tumbuhan hijau menempati posisi sebagai...', 'opsi_a' => 'Produsen', 'opsi_b' => 'Konsumen I', 'opsi_c' => 'Konsumen II', 'opsi_d' => 'Pengurai (Dekomposer)', 'opsi_e' => 'Karnivora', 'kunci' => 'A'],
                ['pertanyaan' => 'Hewan yang memakan tumbuhan sebagai makanan utamanya dikelompokkan ke dalam hewan...', 'opsi_a' => 'Herbivora', 'opsi_b' => 'Karnivora', 'opsi_c' => 'Omnivora', 'opsi_d' => 'Insektivora', 'opsi_e' => 'Parasit', 'kunci' => 'A'],
                ['pertanyaan' => 'Pembuluh darah yang bertugas membawa darah bersih kaya oksigen dari jantung ke seluruh tubuh adalah...', 'opsi_a' => 'Arteri (Nadi)', 'opsi_b' => 'Vena (Balik)', 'opsi_c' => 'Kapiler', 'opsi_d' => 'Venula', 'opsi_e' => 'Limfa', 'kunci' => 'A'],
                ['pertanyaan' => 'Bagian tumbuhan yang berfungsi menyerap air dan unsur hara dari dalam tanah adalah...', 'opsi_a' => 'Akar', 'opsi_b' => 'Batang', 'opsi_c' => 'Daun', 'opsi_d' => 'Bunga', 'opsi_e' => 'Buah', 'kunci' => 'A'],
                ['pertanyaan' => 'Ciri makhluk hidup yang bertujuan mempertahankan kelestarian jenisnya agar tidak punah adalah...', 'opsi_a' => 'Berkembang biak (Reproduksi)', 'opsi_b' => 'Bernapas', 'opsi_c' => 'Tumbuh dan berkembang', 'opsi_d' => 'Peka terhadap rangsang', 'opsi_e' => 'Bergerak', 'kunci' => 'A'],
                ['pertanyaan' => 'Organ ekskresi pada manusia yang menghasilkan cairan empedu dan menetralkan racun adalah...', 'opsi_a' => 'Hati', 'opsi_b' => 'Ginjal', 'opsi_c' => 'Kulit', 'opsi_d' => 'Paru-paru', 'opsi_e' => 'Pankreas', 'kunci' => 'A'],
                ['pertanyaan' => 'Satuan terkecil penyusun makhluk hidup disebut...', 'opsi_a' => 'Sel', 'opsi_b' => 'Jaringan', 'opsi_c' => 'Organ', 'opsi_d' => 'Sistem Organ', 'opsi_e' => 'Individu', 'kunci' => 'A'],
                ['pertanyaan' => 'Proses fotosintesis pada tumbuhan hijau menghasilkan zat berupa...', 'opsi_a' => 'Glukosa dan Oksigen', 'opsi_b' => 'Karbondioksida dan Air', 'opsi_c' => 'Nitrogen dan Energi', 'opsi_d' => 'Karbon monoksida', 'opsi_e' => 'Metana', 'kunci' => 'A'],
                ['pertanyaan' => 'Enzim yang terdapat di dalam lambung dan berfungsi mencerna protein adalah enzim...', 'opsi_a' => 'Pepsin', 'opsi_b' => 'Amilase', 'opsi_c' => 'Lipase', 'opsi_d' => 'Ptialin', 'opsi_e' => 'Laktase', 'kunci' => 'A'],
                ['pertanyaan' => 'Hubungan timbal balik antara makhluk hidup dengan lingkungannya dipelajari dalam cabang biologi...', 'opsi_a' => 'Ekologi', 'opsi_b' => 'Genetika', 'opsi_c' => 'Anatomi', 'opsi_d' => 'Morfologi', 'opsi_e' => 'Fisiologi', 'kunci' => 'A'],
                ['pertanyaan' => 'Gas yang dihirup manusia saat bernapas normal untuk metabolisme tubuh adalah...', 'opsi_a' => 'Oksigen (O2)', 'opsi_b' => 'Karbondioksida (CO2)', 'opsi_c' => 'Nitrogen (N2)', 'opsi_d' => 'Helium', 'opsi_e' => 'Ozon (O3)', 'kunci' => 'A'],
                ['pertanyaan' => 'Bagian sel tumbuhan yang tidak dimiliki oleh sel hewan dan memberi bentuk kaku adalah...', 'opsi_a' => 'Dinding Sel', 'opsi_b' => 'Membran Plasma', 'opsi_c' => 'Sitoplasma', 'opsi_d' => 'Inti Sel', 'opsi_e' => 'Ribosom', 'kunci' => 'A'],
                ['pertanyaan' => 'Proses perubahan ulat menjadi kepompong lalu menjadi kupu-kupu dinamakan...', 'opsi_a' => 'Metamorfosis Sempurna', 'opsi_b' => 'Metamorfosis Tidak Sempurna', 'opsi_c' => 'Regenerasi', 'opsi_d' => 'Fertilisasi', 'opsi_e' => 'Mutasi', 'kunci' => 'A'],
                ['pertanyaan' => 'Sel darah merah (eritrosit) berwarna merah karena mengandung protein bernama...', 'opsi_a' => 'Hemoglobin', 'opsi_b' => 'Fibrinogen', 'opsi_c' => 'Albumin', 'opsi_d' => 'Globulin', 'opsi_e' => 'Trombosit', 'kunci' => 'A'],
                ['pertanyaan' => 'Bakteri pengurai dan jamur dalam ekosistem memiliki peran penting sebagai...', 'opsi_a' => 'Dekomposer', 'opsi_b' => 'Produsen', 'opsi_c' => 'Konsumen primer', 'opsi_d' => 'Predator', 'opsi_e' => 'Inang', 'kunci' => 'A'],
                ['pertanyaan' => 'Contoh simbiosis mutualisme (saling menguntungkan) di alam adalah...', 'opsi_a' => 'Kupu-kupu dengan bunga', 'opsi_b' => 'Benalu dengan pohon inang', 'opsi_c' => 'Kutu dengan rambut manusia', 'opsi_d' => 'Cacing pita dengan usus', 'opsi_e' => 'Singa dengan rusa', 'kunci' => 'A'],
                ['pertanyaan' => 'Alat indra pada manusia yang berfungsi untuk mendeteksi rangsang cahaya adalah...', 'opsi_a' => 'Mata', 'opsi_b' => 'Telinga', 'opsi_c' => 'Hidung', 'opsi_d' => 'Lidah', 'opsi_e' => 'Kulit', 'kunci' => 'A'],
            ],
            'Matematika' => [
                ['pertanyaan' => 'Hasil dari operasi hitung 25 + 15 x 2 adalah...', 'opsi_a' => '55', 'opsi_b' => '80', 'opsi_c' => '50', 'opsi_d' => '65', 'opsi_e' => '70', 'kunci' => 'A'],
                ['pertanyaan' => 'Berapakah luas persegi yang memiliki panjang sisi 8 cm?', 'opsi_a' => '64 cm²', 'opsi_b' => '32 cm²', 'opsi_c' => '16 cm²', 'opsi_d' => '48 cm²', 'opsi_e' => '24 cm²', 'kunci' => 'A'],
                ['pertanyaan' => 'Bila x + 7 = 15, maka nilai dari x adalah...', 'opsi_a' => '8', 'opsi_b' => '7', 'opsi_c' => '9', 'opsi_d' => '22', 'opsi_e' => '10', 'kunci' => 'A'],
                ['pertanyaan' => 'Berapakah keliling lingkaran yang memiliki diameter 14 cm? (π = 22/7)', 'opsi_a' => '44 cm', 'opsi_b' => '88 cm', 'opsi_c' => '154 cm', 'opsi_d' => '28 cm', 'opsi_e' => '22 cm', 'kunci' => 'A'],
                ['pertanyaan' => 'Nilai dari 2³ + 3² adalah...', 'opsi_a' => '17', 'opsi_b' => '15', 'opsi_c' => '12', 'opsi_d' => '25', 'opsi_e' => '18', 'kunci' => 'A'],
                ['pertanyaan' => 'Segitiga siku-siku memiliki panjang sisi alas 6 cm dan tinggi 8 cm. Berapakah panjang sisi miringnya?', 'opsi_a' => '10 cm', 'opsi_b' => '12 cm', 'opsi_c' => '14 cm', 'opsi_d' => '16 cm', 'opsi_e' => '9 cm', 'kunci' => 'A'],
                ['pertanyaan' => 'Berapakah nilai rata-rata (mean) dari data nilai: 7, 8, 9, 6, 10?', 'opsi_a' => '8', 'opsi_b' => '7.5', 'opsi_c' => '8.5', 'opsi_d' => '9', 'opsi_e' => '7', 'kunci' => 'A'],
                ['pertanyaan' => 'Bentuk sederhana dari pecahan 12/16 adalah...', 'opsi_a' => '3/4', 'opsi_b' => '2/3', 'opsi_c' => '1/2', 'opsi_d' => '4/5', 'opsi_e' => '3/5', 'kunci' => 'A'],
                ['pertanyaan' => 'Sebuah kubus memiliki panjang rusuk 5 cm. Berapakah volume kubus tersebut?', 'opsi_a' => '125 cm³', 'opsi_b' => '25 cm³', 'opsi_c' => '150 cm³', 'opsi_d' => '100 cm³', 'opsi_e' => '75 cm³', 'kunci' => 'A'],
                ['pertanyaan' => 'Nilai dari (-5) x (-4) adalah...', 'opsi_a' => '20', 'opsi_b' => '-20', 'opsi_c' => '9', 'opsi_d' => '-9', 'opsi_e' => '1', 'kunci' => 'A'],
                ['pertanyaan' => 'Hasil dari 15% dari 200 adalah...', 'opsi_a' => '30', 'opsi_b' => '25', 'opsi_c' => '20', 'opsi_d' => '35', 'opsi_e' => '15', 'kunci' => 'A'],
                ['pertanyaan' => 'Jumlah sudut dalam sebuah bangun datar segitiga selalu sama dengan...', 'opsi_a' => '180°', 'opsi_b' => '90°', 'opsi_c' => '360°', 'opsi_d' => '270°', 'opsi_e' => '120°', 'kunci' => 'A'],
                ['pertanyaan' => 'Nilai akar kuadrat dari 144 (√144) adalah...', 'opsi_a' => '12', 'opsi_b' => '14', 'opsi_c' => '11', 'opsi_d' => '16', 'opsi_e' => '13', 'kunci' => 'A'],
                ['pertanyaan' => 'Faktor Persekutuan Terbesar (FPB) dari 12 dan 18 adalah...', 'opsi_a' => '6', 'opsi_b' => '3', 'opsi_c' => '2', 'opsi_d' => '36', 'opsi_e' => '12', 'kunci' => 'A'],
                ['pertanyaan' => 'Kelipatan Persekutuan Terkecil (KPK) dari 4 dan 6 adalah...', 'opsi_a' => '12', 'opsi_b' => '24', 'opsi_c' => '8', 'opsi_d' => '18', 'opsi_e' => '6', 'kunci' => 'A'],
                ['pertanyaan' => 'Jika 3y = 21, maka nilai y adalah...', 'opsi_a' => '7', 'opsi_b' => '6', 'opsi_c' => '8', 'opsi_d' => '9', 'opsi_e' => '5', 'kunci' => 'A'],
                ['pertanyaan' => 'Berapakah luas persegi panjang dengan panjang 10 cm dan lebar 4 cm?', 'opsi_a' => '40 cm²', 'opsi_b' => '28 cm²', 'opsi_c' => '14 cm²', 'opsi_d' => '50 cm²', 'opsi_e' => '20 cm²', 'kunci' => 'A'],
                ['pertanyaan' => 'Nilai dari 1/2 + 1/4 adalah...', 'opsi_a' => '3/4', 'opsi_b' => '2/6', 'opsi_c' => '1/8', 'opsi_d' => '2/4', 'opsi_e' => '1/6', 'kunci' => 'A'],
                ['pertanyaan' => 'Sebuah mobil melaju dengan kecepatan 60 km/jam selama 2 jam. Jarak yang ditempuh mobil adalah...', 'opsi_a' => '120 km', 'opsi_b' => '30 km', 'opsi_c' => '80 km', 'opsi_d' => '100 km', 'opsi_e' => '140 km', 'kunci' => 'A'],
                ['pertanyaan' => 'Nilai mutlak dari |-15| adalah...', 'opsi_a' => '15', 'opsi_b' => '-15', 'opsi_c' => '0', 'opsi_d' => '1', 'opsi_e' => '30', 'kunci' => 'A'],
            ],
            'Fiqih' => [
                ['pertanyaan' => 'Rukun Islam yang kedua yang wajib didirikan lima waktu sehari semalam adalah...', 'opsi_a' => 'Shalat', 'opsi_b' => 'Syahadat', 'opsi_c' => 'Zakat', 'opsi_d' => 'Puasa', 'opsi_e' => 'Haji', 'kunci' => 'A'],
                ['pertanyaan' => 'Bersuci dari hadats kecil dilakukan dengan cara berwudhu atau jika tidak ada air diganti dengan...', 'opsi_a' => 'Tayamum', 'opsi_b' => 'Mandi wajib', 'opsi_c' => 'Istinja\'', 'opsi_d' => 'Membasuh kaki', 'opsi_e' => 'Mengusap pakaian', 'kunci' => 'A'],
                ['pertanyaan' => 'Benda suci yang digunakan untuk bertayamum adalah...', 'opsi_a' => 'Debu yang suci', 'opsi_b' => 'Pasir basah', 'opsi_c' => 'Kerikil tajam', 'opsi_d' => 'Kain kering', 'opsi_e' => 'Daun kering', 'kunci' => 'A'],
                ['pertanyaan' => 'Jumlah rakaat shalat fardhu Zhuhur adalah sebanyak...', 'opsi_a' => '4 Rakaat', 'opsi_b' => '3 Rakaat', 'opsi_c' => '2 Rakaat', 'opsi_d' => '1 Rakaat', 'opsi_e' => '5 Rakaat', 'kunci' => 'A'],
                ['pertanyaan' => 'Gerakan shalat yang dilakukan dengan membungkukkan badan dan meletakkan tangan di lutut disebut...', 'opsi_a' => 'Ruku\'', 'opsi_b' => 'Sujud', 'opsi_c' => 'I\'tidal', 'opsi_d' => 'Tasyahud', 'opsi_e' => 'Salam', 'kunci' => 'A'],
                ['pertanyaan' => 'Zakat yang wajib dikeluarkan setiap muslim pada bulan Ramadhan menjelang Idul Fitri dinamakan...', 'opsi_a' => 'Zakat Fitrah', 'opsi_b' => 'Zakat Mal', 'opsi_c' => 'Zakat Emas', 'opsi_d' => 'Infaq', 'opsi_e' => 'Sedekah', 'kunci' => 'A'],
                ['pertanyaan' => 'Menahan diri dari makan, minum, dan hal-hal yang membatalkan dari terbit fajar hingga terbenam matahari disebut...', 'opsi_a' => 'Puasa (Shaum)', 'opsi_b' => 'I\'tikaf', 'opsi_c' => 'Tahajud', 'opsi_d' => 'Tawaf', 'opsi_e' => 'Sa\'i', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum shalat berjamaah bagi laki-laki menurut sebagian besar ulama adalah...', 'opsi_a' => 'Sunnah Muakkad', 'opsi_b' => 'Fardhu \'Ain', 'opsi_c' => 'Mubah', 'opsi_d' => 'Makruh', 'opsi_e' => 'Haram', 'kunci' => 'A'],
                ['pertanyaan' => 'Orang yang memimpin shalat berjamaah disebut...', 'opsi_a' => 'Imam', 'opsi_b' => 'Makmum', 'opsi_c' => 'Muadzin', 'opsi_d' => 'Khatib', 'opsi_e' => 'Bilal', 'kunci' => 'A'],
                ['pertanyaan' => 'Batas minimal jumlah harta yang wajib dikeluarkan zakatnya disebut...', 'opsi_a' => 'Nishab', 'opsi_b' => 'Haul', 'opsi_c' => 'Kadar', 'opsi_d' => 'Kifarat', 'opsi_e' => 'Fidyah', 'kunci' => 'A'],
                ['pertanyaan' => 'Mengelilingi Ka\'bah sebanyak 7 kali putaran berlawanan arah jarum jam dalam ibadah haji disebut...', 'opsi_a' => 'Tawaf', 'opsi_b' => 'Sa\'i', 'opsi_c' => 'Wukuf', 'opsi_d' => 'Tahallul', 'opsi_e' => 'Mabit', 'kunci' => 'A'],
                ['pertanyaan' => 'Puncak pelaksanaan ibadah haji di mana seluruh jamaah berkumpul di padang Arafah disebut...', 'opsi_a' => 'Wukuf', 'opsi_b' => 'Tawaf Ifadhah', 'opsi_c' => 'Sa\'i', 'opsi_d' => 'Lempar Jumrah', 'opsi_e' => 'Tahallul', 'kunci' => 'A'],
                ['pertanyaan' => 'Shalat sunnah yang dilakukan khusus pada malam hari di bulan Ramadhan dinamakan...', 'opsi_a' => 'Shalat Tarawih', 'opsi_b' => 'Shalat Witir', 'opsi_c' => 'Shalat Dhuha', 'opsi_d' => 'Shalat Istikharah', 'opsi_e' => 'Shalat Hajat', 'kunci' => 'A'],
                ['pertanyaan' => 'Hewan yang sah untuk dijadikan kurban pada hari raya Idul Adha antara lain adalah...', 'opsi_a' => 'Kambing, Sapi, Unta', 'opsi_b' => 'Ayam, Bebek, Angsa', 'opsi_c' => 'Kuda, Keledai, Rusa', 'opsi_d' => 'Ikan, Burung, Kelinci', 'opsi_e' => 'Kucing, Anjing', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum memakan bangkai ikan dan belalang dalam ajaran Islam adalah...', 'opsi_a' => 'Halal', 'opsi_b' => 'Haram', 'opsi_c' => 'Makruh', 'opsi_d' => 'Subhat', 'opsi_e' => 'Mubah jika terpaksa', 'kunci' => 'A'],
                ['pertanyaan' => 'Salah satu syarat sah shalat adalah menutup aurat. Batas aurat laki-laki dalam shalat adalah antara...', 'opsi_a' => 'Pusar hingga lutut', 'opsi_b' => 'Dada hingga kaki', 'opsi_c' => 'Leher hingga mata kaki', 'opsi_d' => 'Seluruh tubuh', 'opsi_e' => 'Wajah dan telapak tangan', 'kunci' => 'A'],
                ['pertanyaan' => 'Shalat yang dapat dijamak (digabungkan) dan diqashar (diringkas) bagi seorang musafir adalah shalat...', 'opsi_a' => 'Zhuhur dengan Ashar', 'opsi_b' => 'Shubuh dengan Zhuhur', 'opsi_c' => 'Ashar dengan Maghrib', 'opsi_d' => 'Isya dengan Shubuh', 'opsi_e' => 'Maghrib dengan Shubuh', 'kunci' => 'A'],
                ['pertanyaan' => 'Bagi orang yang sakit parah dan tidak mampu berpuasa Ramadhan serta tidak ada harapan sembuh, kewajibannya diganti dengan...', 'opsi_a' => 'Membayar Fidyah', 'opsi_b' => 'Qadha puasa nanti', 'opsi_c' => 'Membayar Kifarat', 'opsi_d' => 'Sedekah biasa', 'opsi_e' => 'Tidak perlu melakukan apa-apa', 'kunci' => 'A'],
                ['pertanyaan' => 'Hukum melakukan sujud sahwi ketika lupa rakaat atau rukun tertentu dalam shalat adalah...', 'opsi_a' => 'Sunnah', 'opsi_b' => 'Wajib', 'opsi_c' => 'Fardhu \'ain', 'opsi_d' => 'Makruh', 'opsi_e' => 'Mubah', 'kunci' => 'A'],
                ['pertanyaan' => 'Air suci yang dapat mensucikan untuk wudhu dan mandi wajib adalah air mutlak, contohnya adalah...', 'opsi_a' => 'Air hujan, air sumur, air sungai', 'opsi_b' => 'Air teh manis', 'opsi_c' => 'Air kopi hitam', 'opsi_d' => 'Air kelapa muda', 'opsi_e' => 'Air sirup', 'kunci' => 'A'],
            ],
            'Informatika' => [
                ['pertanyaan' => 'Bagian fisik komputer yang dapat dilihat dan disentuh secara langsung disebut...', 'opsi_a' => 'Hardware (Perangkat Keras)', 'opsi_b' => 'Software (Perangkat Lunak)', 'opsi_c' => 'Brainware (Pengguna)', 'opsi_d' => 'Malware', 'opsi_e' => 'Firmware', 'kunci' => 'A'],
                ['pertanyaan' => 'Otak dari sebuah komputer yang memproses semua instruksi dan perhitungan adalah...', 'opsi_a' => 'CPU (Processor)', 'opsi_b' => 'Harddisk', 'opsi_c' => 'RAM', 'opsi_d' => 'Power Supply', 'opsi_e' => 'Monitor', 'kunci' => 'A'],
                ['pertanyaan' => 'Perangkat masukan (input device) yang digunakan untuk mengetik teks dan angka adalah...', 'opsi_a' => 'Keyboard', 'opsi_b' => 'Monitor', 'opsi_c' => 'Speaker', 'opsi_d' => 'Printer', 'opsi_e' => 'Proyektor', 'kunci' => 'A'],
                ['pertanyaan' => 'Jaringan komputer global yang menghubungkan miliaran komputer di seluruh dunia dinamakan...', 'opsi_a' => 'Internet', 'opsi_b' => 'Intranet', 'opsi_c' => 'LAN', 'opsi_d' => 'Bluetooth', 'opsi_e' => 'Hotspot', 'kunci' => 'A'],
                ['pertanyaan' => 'Aplikasi yang digunakan untuk menjelajahi halaman web di internet (misalnya Chrome, Edge, Firefox) disebut...', 'opsi_a' => 'Web Browser', 'opsi_b' => 'Search Engine', 'opsi_c' => 'Antivirus', 'opsi_d' => 'Word Processor', 'opsi_e' => 'Spreadsheet', 'kunci' => 'A'],
                ['pertanyaan' => 'Pintasan keyboard (shortcut) yang umum digunakan untuk menyalin (Copy) teks adalah...', 'opsi_a' => 'Ctrl + C', 'opsi_b' => 'Ctrl + V', 'opsi_c' => 'Ctrl + X', 'opsi_d' => 'Ctrl + Z', 'opsi_e' => 'Ctrl + P', 'kunci' => 'A'],
                ['pertanyaan' => 'Pintasan keyboard untuk menempelkan teks (Paste) yang telah disalin adalah...', 'opsi_a' => 'Ctrl + V', 'opsi_b' => 'Ctrl + C', 'opsi_c' => 'Ctrl + S', 'opsi_d' => 'Ctrl + A', 'opsi_e' => 'Ctrl + F', 'kunci' => 'A'],
                ['pertanyaan' => 'Langkah-langkah terstruktur dan logis yang disusun untuk menyelesaikan suatu masalah disebut...', 'opsi_a' => 'Algoritma', 'opsi_b' => 'Program', 'opsi_c' => 'Bahasa Pemrograman', 'opsi_d' => 'Sistem Operasi', 'opsi_e' => 'Basis Data', 'kunci' => 'A'],
                ['pertanyaan' => 'Simbol bangun belah ketupat dalam diagram alir (flowchart) digunakan untuk melambangkan...', 'opsi_a' => 'Keputusan (Decision)', 'opsi_b' => 'Mulai / Berhenti (Terminator)', 'opsi_c' => 'Proses perhitungan', 'opsi_d' => 'Input / Output data', 'opsi_e' => 'Penghubung halaman', 'kunci' => 'A'],
                ['pertanyaan' => 'Sistem operasi komputer yang bersifat open-source dan gratis adalah...', 'opsi_a' => 'Linux', 'opsi_b' => 'Microsoft Windows', 'opsi_c' => 'macOS', 'opsi_d' => 'iOS', 'opsi_e' => 'MS-DOS', 'kunci' => 'A'],
                ['pertanyaan' => 'Perangkat penyimpanan portabel yang menggunakan port USB untuk menyimpan data adalah...', 'opsi_a' => 'Flashdisk', 'opsi_b' => 'RAM', 'opsi_c' => 'ROM', 'opsi_d' => 'CD-ROM', 'opsi_e' => 'Sound Card', 'kunci' => 'A'],
                ['pertanyaan' => 'Tindakan kejahatan penipuan digital untuk mencuri kata sandi dengan situs palsu dinamakan...', 'opsi_a' => 'Phishing', 'opsi_b' => 'Browsing', 'opsi_c' => 'Streaming', 'opsi_d' => 'Chatting', 'opsi_e' => 'Coding', 'kunci' => 'A'],
                ['pertanyaan' => 'Satuan kapasitas memori komputer yang setara dengan 1024 Kilobyte (KB) adalah...', 'opsi_a' => '1 Megabyte (MB)', 'opsi_b' => '1 Gigabyte (GB)', 'opsi_c' => '1 Terabyte (TB)', 'opsi_d' => '1 Byte', 'opsi_e' => '1 Bit', 'kunci' => 'A'],
                ['pertanyaan' => 'Program jahat yang dirancang untuk merusak sistem atau mencuri data komputer disebut...', 'opsi_a' => 'Virus / Malware', 'opsi_b' => 'Firewall', 'opsi_c' => 'Driver', 'opsi_d' => 'Browser', 'opsi_e' => 'Cache', 'kunci' => 'A'],
                ['pertanyaan' => 'Layanan pengiriman surat elektronik melalui internet dikenal dengan sebutan...', 'opsi_a' => 'Email', 'opsi_b' => 'SMS', 'opsi_c' => 'Fax', 'opsi_d' => 'Surat Kilat', 'opsi_e' => 'Telegram konvensional', 'kunci' => 'A'],
                ['pertanyaan' => 'Kombinasi kata sandi yang aman dan kuat sebaiknya terdiri dari...', 'opsi_a' => 'Huruf besar, huruf kecil, angka, dan simbol', 'opsi_b' => 'Hanya tanggal lahir', 'opsi_c' => 'Nama lengkap saja', 'opsi_d' => 'Angka urut 123456', 'opsi_e' => 'Satu kata pendek', 'kunci' => 'A'],
                ['pertanyaan' => 'Perangkat keluaran (output device) yang menghasilkan salinan cetak di atas kertas adalah...', 'opsi_a' => 'Printer', 'opsi_b' => 'Scanner', 'opsi_c' => 'Keyboard', 'opsi_d' => 'Mouse', 'opsi_e' => 'Microphone', 'kunci' => 'A'],
                ['pertanyaan' => 'Teknologi nirkabel jarak dekat yang umum digunakan untuk menghubungkan headset ke ponsel adalah...', 'opsi_a' => 'Bluetooth', 'opsi_b' => 'Kabel LAN', 'opsi_c' => 'Satelit', 'opsi_d' => 'Fiber Optic', 'opsi_e' => 'Dial-up', 'kunci' => 'A'],
                ['pertanyaan' => 'Penyimpanan data secara daring di internet sehingga bisa diakses dari mana saja disebut...', 'opsi_a' => 'Cloud Storage', 'opsi_b' => 'Harddisk Internal', 'opsi_c' => 'Disket', 'opsi_d' => 'Pita Magnetik', 'opsi_e' => 'ROM', 'kunci' => 'A'],
                ['pertanyaan' => 'Empat pilar dalam Berpikir Komputasional (Computational Thinking) adalah Dekomposisi, Pengenalan Pola, Abstraksi, dan...', 'opsi_a' => 'Perancangan Algoritma', 'opsi_b' => 'Instalasi Program', 'opsi_c' => 'Perakitan Komputer', 'opsi_d' => 'Pembelian Lisensi', 'opsi_e' => 'Pencetakan Dokumen', 'kunci' => 'A'],
            ],
        ];
    }

    /**
     * Soal default pengetahuan umum untuk mata pelajaran lainnya
     */
    public static function getDefaultQuestions($mapel) {
        $clean = htmlspecialchars($mapel);
        $questions = [];
        $templates = [
            ["Konsep dasar dan prinsip utama dalam pembelajaran {$clean} bertujuan untuk...", "Mengembangkan pemahaman dan keterampilan siswa", "Menghafal materi tanpa memahami maksudnya", "Menyulitkan proses belajar mengajar", "Membuat siswa pasif di dalam kelas", "Mengabaikan etika dan nilai moral", "A"],
            ["Dalam mempelajari mata pelajaran {$clean}, sikap ilmiah yang harus ditunjukkan siswa adalah...", "Kritis, objektif, dan jujur", "Mudah percaya tanpa bukti", "Menolak pendapat orang lain secara emosional", "Pasif dan tidak ingin tahu", "Menyontek karya teman", "A"],
            ["Manfaat utama dari mempelajari mata pelajaran {$clean} bagi kehidupan sehari-hari adalah...", "Menerapkan ilmu untuk memecahkan masalah nyata", "Hanya untuk memperoleh nilai di rapor", "Menghabiskan waktu luang di sekolah", "Menghindari pelajaran lainnya", "Supaya dipuji oleh orang lain", "A"],
            ["Salah satu metode pembelajaran efektif dalam {$clean} adalah melalui...", "Diskusi kelompok dan eksplorasi aktif", "Hanya mencatat apa yang diucapkan guru", "Membaca sekilas tanpa memahami konsep", "Belajar hanya saat menjelang ujian", "Mengabaikan tugas yang diberikan", "A"],
            ["Sumber referensi yang kredibel dan dapat dipertanggungjawabkan dalam {$clean} adalah...", "Buku teks resmi dan jurnal ilmiah", "Informasi hoax di media sosial", "Kabar burung yang belum diverifikasi", "Opini pribadi tanpa dasar data", "Cerita mitos yang tidak terbukti", "A"],
            ["Etika penting yang harus dijunjung tinggi saat mengkaji materi {$clean} adalah...", "Integritas dan menghargai orisinalitas karya", "Melakukan plagiarisme tanpa izin", "Meremehkan kontribusi ilmuwan terdahulu", "Menyebarkan informasi yang salah", "Mengubah data demi keuntungan pribadi", "A"],
            ["Langkah awal yang tepat ketika menghadapi soal evaluasi {$clean} yang sulit adalah...", "Membaca soal dengan teliti dan memahami maksud pertanyaan", "Langsung menebak jawaban tanpa membaca", "Menyerah dan mengosongkan jawaban", "Menanyakan jawaban kepada teman di samping", "Mengeluh kepada pengawas ujian", "A"],
            ["Karakteristik penting dalam pembelajaran {$clean} di era digital adalah...", "Pemanfaatan teknologi untuk memperluas wawasan", "Menolak penggunaan komputer dan internet", "Hanya mengandalkan buku cetak lama", "Membatasi diri dari perkembangan ilmu baru", "Menolak kolaborasi secara digital", "A"],
            ["Peran guru pengampu mata pelajaran {$clean} di madrasah adalah sebagai...", "Fasilitator dan pembimbing proses belajar", "Hakim yang mencari kesalahan siswa", "Pemberi hukuman tanpa alasan", "Satu-satunya sumber kebenaran di kelas", "Pengamat yang tidak peduli pada siswa", "A"],
            ["Tujuan asesmen dan evaluasi berkala pada mata pelajaran {$clean} adalah...", "Mengukur pencapaian kompetensi dan pemahaman materi", "Memberikan rasa takut kepada siswa", "Membandingkan kelemahan antarsiswa", "Membuat siswa tertekan secara psikis", "Mengurangi waktu istirahat siswa", "A"],
            ["Sikap saling menghormati pendapat dalam diskusi materi {$clean} mencerminkan nilai...", "Toleransi dan keterbukaan berpikir", "Ketidaktegasan dalam bersikap", "Kelemahan argumen yang dimiliki", "Ketidaktahuan terhadap materi", "Sikap pasrah tanpa pendirian", "A"],
            ["Keterkaitan antara mata pelajaran {$clean} dengan nilai-nilai keislaman di madrasah adalah...", "Memperkuat keimanan dan akhlak mulia", "Memisahkan ilmu pengetahuan dari agama", "Mengurangi minat siswa dalam beribadah", "Menghilangkan identitas kemadrasahan", "Menimbulkan keraguan terhadap ajaran agama", "A"],
            ["Kreativitas dalam menyelesaikan tugas proyek pada mata pelajaran {$clean} dapat diasah melalui...", "Inovasi dan pencarian solusi baru yang bermanfaat", "Menjiplak karya orang lain secara utuh", "Mengerjakan tugas seadanya asal selesai", "Mengandalkan kerja teman satu kelompok", "Menghindari tugas yang memerlukan ide baru", "A"],
            ["Penerapan konsep ramah lingkungan dan keberlanjutan dalam kajian {$clean} berguna untuk...", "Menjaga kelestarian alam dan generasi masa depan", "Mengeksploitasi sumber daya tanpa batas", "Menimbulkan kerusakan ekosistem lokal", "Mementingkan keuntungan ekonomi sesaat", "Mengabaikan dampak polusi lingkungan", "A"],
            ["Kemampuan berkomunikasi dan mempresentasikan hasil kerja {$clean} secara santun berguna untuk...", "Menyampaikan ide dengan jelas dan meyakinkan", "Menjatuhkan martabat kelompok lawan", "Memperlihatkan kehebatan diri sendiri", "Menutupi kekurangan pemahaman materi", "Menghindari tanya jawab dari peserta lain", "A"],
            ["Ketelitian dan ketuntasan dalam mencatat materi pembelajaran {$clean} bermanfaat untuk...", "Bahan belajar mandiri yang runtut dan sistematis", "Sekadar memenuhi kewajiban pemeriksaan buku", "Membuat buku catatan terlihat tebal", "Menghindari teguran guru di kelas", "Bahan pameran kepada teman sebangku", "A"],
            ["Sikap pantang menyerah saat mempelajari topik yang menantang dalam {$clean} disebut...", "Kegigihan (Perseverance)", "Keras kepala", "Keterpaksaan", "Kepasrahan", "Keengganan", "A"],
            ["Pemanfaatan perpustakaan madrasah dalam mendukung pelajaran {$clean} sangat baik untuk...", "Mencari literatur tambahan dan memperluas wawasan", "Tempat tidur siang saat jam kosong", "Mengobrol dan membuat gaduh", "Menyembunyikan buku dari siswa lain", "Menghindari kegiatan pembelajaran di kelas", "A"],
            ["Keterampilan literasi dan numerasi yang dilatih melalui pelajaran {$clean} bertujuan agar...", "Siswa mampu bernalar kritis dalam menganalisis informasi", "Siswa mampu menghafal rumus tanpa mengerti", "Siswa hanya bisa berhitung cepat tanpa makna", "Siswa tidak membutuhkan bantuan teknologi", "Siswa mengabaikan teks bacaan panjang", "A"],
            ["Hasil belajar yang optimal dalam mata pelajaran {$clean} akan tercapai jika disertai dengan...", "Doa, kesungguhan belajar, dan akhlak yang mulia", "Bermalas-malasan dan mengandalkan keberuntungan", "Menunda-nunda tugas hingga menit terakhir", "Bergantung sepenuhnya pada bantuan orang lain", "Belajar hanya jika ada imbalan hadiah", "A"]
        ];

        foreach ($templates as $idx => $t) {
            $questions[] = [
                'nomor_urut' => $idx + 1,
                'pertanyaan' => $t[0],
                'opsi_a' => $t[1],
                'opsi_b' => $t[2],
                'opsi_c' => $t[3],
                'opsi_d' => $t[4],
                'opsi_e' => $t[5],
                'kunci' => $t[6],
                'skor_maksimal' => 5.00
            ];
        }

        return $questions;
    }
}
