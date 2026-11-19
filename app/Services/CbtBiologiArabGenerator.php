<?php

namespace App\Services;

use App\Core\Database;
use App\Services\Datasets\BiologiKelasX;
use App\Services\Datasets\BiologiKelasXI;
use App\Services\Datasets\BiologiKelasXII;
use App\Services\Datasets\ArabKelasX;
use App\Services\Datasets\ArabKelasXI;
use App\Services\Datasets\ArabKelasXII;
use PDO;

class CbtBiologiArabGenerator {

    /**
     * Helper untuk membuat kotak Stimulus Biologi ber-CSS elegan dengan Prompt Gambar AI & Inline SVG
     */
    public static function wrapBioStimulus($stimulusNo, $soalRange, $title, $aiPrompt, $svgDiagram, $storyHtml, $badgeTipe, $questionPrompt) {
        $html = '<div style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:14px; padding:14px 16px; margin-bottom:14px; box-shadow:0 3px 8px rgba(0,0,0,0.03); font-family:sans-serif;">';
        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px 12px; margin-bottom:8px; border-bottom:1.5px solid #e2e8f0; padding-bottom:8px;">';
        $html .= '<span style="font-size:0.75rem; font-weight:800; background:#059669; color:#ffffff; padding:4px 10px; border-radius:20px; letter-spacing:0.3px;">STIMULUS ' . $stimulusNo . ' (SOAL NO. ' . $soalRange . ')</span>';
        $html .= '<span style="font-size:0.75rem; font-weight:700; color:#64748b;">Simulasi Asesmen Biologi AKM</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:1rem; font-weight:800; color:#0f172a; margin-bottom:10px;">' . $title . '</div>';

        // Prompt Gambar AI Box
        if (!empty($aiPrompt)) {
            $html .= '<div style="background:#eff6ff; border:1.5px dashed #3b82f6; border-radius:10px; padding:10px 14px; margin:10px 0; font-size:0.84rem; color:#1e40af;">';
            $html .= '<div style="font-weight:800; display:flex; align-items:center; gap:6px; margin-bottom:4px; color:#1d4ed8;">';
            $html .= '<span>🎨</span> <span>[PROMPT GAMBAR AI / DESKRIPSI VISUAL]:</span>';
            $html .= '</div>';
            $html .= '<div style="font-style:italic; line-height:1.5; color:#1e3a8a;">"' . htmlspecialchars($aiPrompt) . '"</div>';
            $html .= '</div>';
        }

        // Inline SVG Diagram jika ada
        if (!empty($svgDiagram)) {
            $html .= '<div style="text-align:center; margin:10px 0 14px 0; background:#ffffff; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">' . $svgDiagram . '</div>';
        }

        if (!empty($storyHtml)) {
            $html .= '<div style="font-size:0.88rem; color:#334155; line-height:1.65; margin-bottom:4px;">' . $storyHtml . '</div>';
        }
        $html .= '</div>';

        // Badge Tipe Soal & Pertanyaan
        $html .= '<div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">';
        $html .= '<span style="font-size:0.72rem; font-weight:800; background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; border:1px solid #cbd5e1;">' . $badgeTipe . '</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:0.95rem; font-weight:700; color:#0f172a; line-height:1.5;">' . $questionPrompt . '</div>';

        return $html;
    }

    /**
     * Helper untuk membuat kotak Stimulus Bahasa Arab dengan Teks Berharakat Indah
     */
    public static function wrapArabStimulus($stimulusNo, $soalRange, $titleArabic, $titleIndo, $arabicText, $indoContext, $badgeTipe, $questionPrompt) {
        $html = '<div style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:14px; padding:14px 16px; margin-bottom:14px; box-shadow:0 3px 8px rgba(0,0,0,0.03); font-family:sans-serif;">';
        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px 12px; margin-bottom:8px; border-bottom:1.5px solid #e2e8f0; padding-bottom:8px;">';
        $html .= '<span style="font-size:0.75rem; font-weight:800; background:#0284c7; color:#ffffff; padding:4px 10px; border-radius:20px; letter-spacing:0.3px;">STIMULUS ' . $stimulusNo . ' (SOAL NO. ' . $soalRange . ')</span>';
        $html .= '<span style="font-size:0.75rem; font-weight:700; color:#64748b;">Simulasi Asesmen Bahasa Arab AKM</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:1.05rem; font-weight:800; color:#0f172a; margin-bottom:10px;">' . $titleArabic . ' &bull; <span style="font-size:0.9rem; color:#475569; font-weight:600;">' . $titleIndo . '</span></div>';

        // Kotak Teks Arab Berharakat
        if (!empty($arabicText)) {
            $html .= '<div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:14px 18px; margin:10px 0; direction:rtl; text-align:right; font-family:\'Amiri\', \'Traditional Arabic\', \'Scheherazade New\', serif; font-size:1.25rem; line-height:2.2; color:#14532d;">';
            $html .= $arabicText;
            $html .= '</div>';
        }

        if (!empty($indoContext)) {
            $html .= '<div style="font-size:0.84rem; color:#475569; line-height:1.6; margin-top:8px; background:#ffffff; padding:8px 12px; border-radius:8px; border:1px solid #e2e8f0;">';
            $html .= '<strong>Konteks Bacaan:</strong> ' . $indoContext;
            $html .= '</div>';
        }
        $html .= '</div>';

        // Badge Tipe Soal & Pertanyaan
        $html .= '<div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">';
        $html .= '<span style="font-size:0.72rem; font-weight:800; background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; border:1px solid #cbd5e1;">' . $badgeTipe . '</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:0.95rem; font-weight:700; color:#0f172a; line-height:1.5;">' . $questionPrompt . '</div>';

        return $html;
    }

    /**
     * Generate 50 Soal Biologi & 50 Soal Bahasa Arab per Tingkat (Kelas X, XI, XII)
     */
    public static function generateAll($topikId = null) {
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');

        // Pastikan tabel CBT tersedia
        \App\Controllers\CbtController::ensureTables($db_core);
        \App\Controllers\BankSoalController::ensureTables($db_siakad);
        if ($db_core !== $db_siakad) {
            \App\Controllers\BankSoalController::ensureTables($db_core);
        }

        // Tentukan daftar topik yang akan digenerate
        $targetTopiks = [];
        if ($topikId) {
            $targetTopiks[] = (int)$topikId;
        } else {
            // Ambil semua topik aktif dari bs_topik siakad dan core
            try {
                $t1 = $db_siakad->query("SELECT id FROM bs_topik WHERE status = 'Aktif' OR nama_topik LIKE '%SIMULASI%' ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($t1 as $id) {
                    $targetTopiks[] = (int)$id;
                }
            } catch (\Exception $e) {}

            try {
                $t2 = $db_core->query("SELECT id FROM bs_topik WHERE status = 'Aktif' OR nama_topik LIKE '%SIMULASI%' ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($t2 as $id) {
                    $targetTopiks[] = (int)$id;
                }
            } catch (\Exception $e) {}

            // Sertakan juga Topik ID 1 dan ID 7 jika ada
            $targetTopiks[] = 1;
            $targetTopiks[] = 7;
            $targetTopiks = array_unique(array_filter($targetTopiks));
        }

        if (empty($targetTopiks)) {
            $targetTopiks = [1];
        }

        $totalInserted = 0;

        foreach ($targetTopiks as $tId) {
            // Cek apakah topik ada di database
            $cekT = (int)$db_siakad->query("SELECT COUNT(*) FROM bs_topik WHERE id = {$tId}")->fetchColumn();
            if ($cekT === 0) {
                // Buat topik jika belum ada di siakad
                try {
                    $namaTopik = ($tId === 7) ? 'SIMULASI CBT NEW' : 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027';
                    $stmtInsT = $db_siakad->prepare("INSERT INTO bs_topik (id, jenis, nama_topik, tahun_ajaran, semester, status) VALUES (?, 'SAS', ?, '2026/2027', 'Ganjil', 'Aktif')");
                    $stmtInsT->execute([$tId, $namaTopik]);
                } catch (\Exception $e) {}
            }

            // Bersihkan butir soal lama Biologi & Bahasa Arab untuk topik ini agar fresh & tidak double
            try {
                $db_siakad->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')")->execute([$tId]);
            } catch (\Exception $e) {}
            try {
                if ($db_core !== $db_siakad) {
                    $db_core->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')")->execute([$tId]);
                }
            } catch (\Exception $e) {}

            // Bersihkan cbt_soal untuk ujian biologi & arab topik ini
            try {
                $uIds = $db_core->query("SELECT id FROM cbt_ujian WHERE (topik_id = {$tId} OR topik_id IS NULL) AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')")->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($uIds)) {
                    $inIds = implode(',', array_map('intval', $uIds));
                    $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inIds})");
                }
            } catch (\Exception $e) {}

            // ══════════════════════════════════════════════════════════════
            // 1. GENERATE SOAL BIOLOGI (KELAS X, XI, XII)
            // ══════════════════════════════════════════════════════════════
            $bioLevels = [
                [
                    'tingkat' => 'Kelas X',
                    'rombel'  => 'X E.1',
                    'kode'    => 'UJN-BIO-X',
                    'guru'    => 'INDAH YUNITASARI, S.Pd',
                    'dataset' => BiologiKelasX::getQuestions()
                ],
                [
                    'tingkat' => 'Kelas XI',
                    'rombel'  => 'XI F.1',
                    'kode'    => 'UJN-BIO-XI',
                    'guru'    => 'ERWANSAH, S.Pd',
                    'dataset' => BiologiKelasXI::getQuestions()
                ],
                [
                    'tingkat' => 'Kelas XII',
                    'rombel'  => 'XII F.1, XII F.2',
                    'kode'    => 'UJN-BIO-XII',
                    'guru'    => 'ERWANSAH, S.Pd',
                    'dataset' => BiologiKelasXII::getQuestions()
                ]
            ];

            foreach ($bioLevels as $lvl) {
                $inserted = self::insertSubjectPackage(
                    $db_siakad, $db_core, $tId, 'Biologi',
                    $lvl['tingkat'], $lvl['rombel'], $lvl['kode'], $lvl['guru'],
                    'Simulasi Biologi - ' . $lvl['tingkat'], $lvl['dataset']
                );
                $totalInserted += $inserted;
            }

            // ══════════════════════════════════════════════════════════════
            // 2. GENERATE SOAL BAHASA ARAB (KELAS X, XI, XII)
            // Catatan: Cek apakah di bs_paket menggunakan nama "Bahasa Arab Peminatan" atau "Bahasa Arab"
            // Kita dukung kedua varian agar selalu match dengan tampilan Bank Soal user!
            // ══════════════════════════════════════════════════════════════
            $arabMapelNames = ['Bahasa Arab', 'Bahasa Arab Peminatan'];

            $arabLevels = [
                [
                    'tingkat' => 'Kelas X',
                    'rombel'  => 'X E.1',
                    'kode'    => 'UJN-ARB-X',
                    'guru'    => 'DIDI HARTONO, S.Pd.I',
                    'dataset' => ArabKelasX::getQuestions()
                ],
                [
                    'tingkat' => 'Kelas XI',
                    'rombel'  => 'XI F.1',
                    'kode'    => 'UJN-ARB-XI',
                    'guru'    => 'DIDI HARTONO, S.Pd.I',
                    'dataset' => ArabKelasXI::getQuestions()
                ],
                [
                    'tingkat' => 'Kelas XII',
                    'rombel'  => 'XII F.1, XII F.2',
                    'kode'    => 'UJN-ARB-XII',
                    'guru'    => 'DIDI HARTONO, S.Pd.I',
                    'dataset' => ArabKelasXII::getQuestions()
                ]
            ];

            foreach ($arabLevels as $lvl) {
                // Cek apakah paket Bahasa Arab Peminatan ada di bs_paket untuk tingkat ini
                $stmtCekArab = $db_siakad->prepare("SELECT mapel_nama FROM bs_paket WHERE topik_id = ? AND mapel_nama LIKE '%Arab%' AND kelas_nama = ? LIMIT 1");
                $stmtCekArab->execute([$tId, $lvl['tingkat']]);
                $existingArabMapel = $stmtCekArab->fetchColumn();

                $chosenMapel = $existingArabMapel ?: 'Bahasa Arab Peminatan';

                $inserted = self::insertSubjectPackage(
                    $db_siakad, $db_core, $tId, $chosenMapel,
                    $lvl['tingkat'], $lvl['rombel'], $lvl['kode'], $lvl['guru'],
                    'Simulasi Bahasa Arab - ' . $lvl['tingkat'], $lvl['dataset']
                );
                $totalInserted += $inserted;

                // Jika nama paket aslinya Bahasa Arab dan ada juga Bahasa Arab Peminatan, kita sync dua-duanya
                if ($chosenMapel === 'Bahasa Arab Peminatan') {
                    // Juga pastikan jika ada paket "Bahasa Arab" biasa terisi soalnya
                    $stmtCek2 = $db_siakad->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = 'Bahasa Arab' AND kelas_nama = ? LIMIT 1");
                    $stmtCek2->execute([$tId, $lvl['tingkat']]);
                    if ($stmtCek2->fetchColumn()) {
                        self::insertSubjectPackage(
                            $db_siakad, $db_core, $tId, 'Bahasa Arab',
                            $lvl['tingkat'], $lvl['rombel'], $lvl['kode'] . '-REG', $lvl['guru'],
                            'Simulasi Bahasa Arab Reguler - ' . $lvl['tingkat'], $lvl['dataset']
                        );
                    }
                }
            }
        }

        // Pastikan Token Ujian Tersedia & Aktif
        try {
            $tokAktif = $db_core->query("SELECT * FROM cbt_token WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tokAktif) {
                $db_core->exec("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES ('MAS26A', 240, 'Aktif', NOW())");
            }
        } catch (\Exception $e) {}

        return $totalInserted;
    }

    /**
     * Memasukkan Paket Mapel ke bs_paket, butir soal ke bs_soal, ujian ke cbt_ujian, dan butir ke cbt_soal
     */
    private static function insertSubjectPackage($db_siakad, $db_core, $topikId, $mapelNama, $tingkat, $rombel, $kodeUjian, $guru, $namaUjian, $dataset) {
        // 1. Pastikan Paket di bs_paket ada
        $stmtP = $db_siakad->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ?) LIMIT 1");
        $stmtP->execute([$topikId, $mapelNama, $tingkat, $rombel]);
        $paketId = (int)$stmtP->fetchColumn();

        if (!$paketId) {
            $stmtInsP = $db_siakad->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, ?, 75, 90, 0)");
            $stmtInsP->execute([$topikId, $mapelNama, $tingkat, $rombel, $guru]);
            $paketId = (int)$db_siakad->lastInsertId();
        } else {
            $db_siakad->prepare("UPDATE bs_paket SET guru_nama = ?, kelas_sasaran = ?, kkm = 75, durasi_menit = 90, is_locked = 0 WHERE id = ?")->execute([$guru, $rombel, $paketId]);
        }

        // 2. Pastikan cbt_ujian ada dan Aktif
        $stmtU = $db_core->prepare("SELECT id FROM cbt_ujian WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama = 'Semua Kelas') LIMIT 1");
        $stmtU->execute([$topikId, $mapelNama, $tingkat, $rombel]);
        $ujianId = (int)$stmtU->fetchColumn();

        if (!$ujianId) {
            $stmtInsU = $db_core->prepare("INSERT INTO cbt_ujian (topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, status) VALUES (?, ?, ?, ?, ?, 90, 75, 1, 1, 'Aktif')");
            $stmtInsU->execute([$topikId, $kodeUjian, $namaUjian, $mapelNama, $tingkat]);
            $ujianId = (int)$db_core->lastInsertId();
        } else {
            $db_core->prepare("UPDATE cbt_ujian SET status = 'Aktif', durasi_menit = 90, kkm = 75, nama_ujian = ? WHERE id = ?")->execute([$namaUjian, $ujianId]);
        }

        // Hubungkan ke jadwal_grid jika ada slot kelas ini
        try {
            $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ?)")->execute([$ujianId, $topikId, $mapelNama, $rombel, $tingkat]);
        } catch (\Exception $e) {}

        // 3. Masukkan 50 Butir Soal ke bs_soal dan cbt_soal
        $stmtBs = $db_siakad->prepare("
            INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtCbt = $db_core->prepare("
            INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $count = 0;
        foreach ($dataset as $soal) {
            $no = (int)$soal['nomor_urut'];
            $tipe = $soal['tipe_soal'];
            $tkesulitan = $soal['tingkat_kesulitan'] ?? 'Sedang';
            $skor = (float)($soal['skor_maksimal'] ?? 2.00);
            $tanya = $soal['pertanyaan'];
            $opA = $soal['opsi_a'] ?? '';
            $opB = $soal['opsi_b'] ?? '';
            $opC = $soal['opsi_c'] ?? '';
            $opD = $soal['opsi_d'] ?? '';
            $opE = $soal['opsi_e'] ?? '';
            $kunci = $soal['kunci_jawaban'] ?? '';
            $konten = is_array($soal['konten_tambahan']) ? json_encode($soal['konten_tambahan']) : $soal['konten_tambahan'];

            // Insert ke bs_soal
            $stmtBs->execute([
                $topikId, $paketId, $mapelNama, $tingkat, $no, $tipe, $tkesulitan, $skor,
                $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $konten
            ]);

            // Insert ke cbt_soal
            $stmtCbt->execute([
                $ujianId, $no, $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $skor, $tipe, $konten
            ]);

            $count++;
        }

        return $count;
    }
}
