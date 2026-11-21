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
     * Membersihkan topik duplikat tak terpakai & butir soal yang nyasar di topik non-simulasi
     */
    public static function cleanupUnwantedTopics($db_siakad, $db_core) {
        // 1. Deduplikasi Topik: Hapus baris duplikat yang memiliki nama sama (misal SAS kosong, duplikat SAS baru)
        try {
            $all = $db_siakad->query("
                SELECT t.id, t.nama_topik, t.tgl_mulai, 
                       (SELECT COUNT(*) FROM bs_paket WHERE topik_id = t.id) as cnt_paket,
                       (SELECT COUNT(*) FROM bs_soal WHERE topik_id = t.id) as cnt_soal
                FROM bs_topik t
                ORDER BY t.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);

            $grouped = [];
            foreach ($all as $item) {
                $name = trim($item['nama_topik']);
                $grouped[$name][] = $item;
            }

            foreach ($grouped as $name => $items) {
                if (count($items) > 1) {
                    // Urutkan: utamakan yang memiliki tgl_mulai, lalu paket terbanyak, lalu id terkecil
                    usort($items, function($a, $b) {
                        $hasDateA = !empty($a['tgl_mulai']) ? 1 : 0;
                        $hasDateB = !empty($b['tgl_mulai']) ? 1 : 0;
                        if ($hasDateA !== $hasDateB) return $hasDateB - $hasDateA;
                        if ((int)$a['cnt_paket'] !== (int)$b['cnt_paket']) return (int)$b['cnt_paket'] - (int)$a['cnt_paket'];
                        return (int)$a['id'] - (int)$b['id'];
                    });

                    // Item pertama dipertahankan, sisanya dihapus
                    array_shift($items);
                    foreach ($items as $del) {
                        $dId = (int)$del['id'];
                        $db_siakad->exec("DELETE FROM bs_soal WHERE topik_id = {$dId}");
                        $db_siakad->exec("DELETE FROM bs_paket WHERE topik_id = {$dId}");
                        $db_siakad->exec("DELETE FROM bs_topik WHERE id = {$dId}");
                        try { $db_core->exec("DELETE FROM bs_soal WHERE topik_id = {$dId}"); } catch (\Exception $e) {}
                        try { $db_core->exec("DELETE FROM bs_paket WHERE topik_id = {$dId}"); } catch (\Exception $e) {}
                        try { $db_core->exec("DELETE FROM bs_topik WHERE id = {$dId}"); } catch (\Exception $e) {}
                        try { $db_core->exec("DELETE FROM cbt_jadwal_grid WHERE topik_id = {$dId}"); } catch (\Exception $e) {}
                    }
                }
            }
        } catch (\Exception $e) {}

        // 2. Hapus juga topik yang periodenya kosong dan bukan topik SIMULASI
        try {
            $emptyDateDups = $db_siakad->query("
                SELECT id FROM bs_topik 
                WHERE (tgl_mulai IS NULL OR tgl_mulai = '') 
                  AND nama_topik NOT LIKE '%SIMULASI%'
            ")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($emptyDateDups as $dId) {
                $dId = (int)$dId;
                $db_siakad->exec("DELETE FROM bs_soal WHERE topik_id = {$dId}");
                $db_siakad->exec("DELETE FROM bs_paket WHERE topik_id = {$dId}");
                $db_siakad->exec("DELETE FROM bs_topik WHERE id = {$dId}");
                try { $db_core->exec("DELETE FROM bs_soal WHERE topik_id = {$dId}"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM bs_paket WHERE topik_id = {$dId}"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM bs_topik WHERE id = {$dId}"); } catch (\Exception $e) {}
            }
        } catch (\Exception $e) {}

        // 3. Bersihkan butir soal Biologi & Arab yang nyasar di topik NON-SIMULASI (seperti STS, UM, SAS resmi)
        try {
            $nonSimTopiks = $db_siakad->query("SELECT id FROM bs_topik WHERE nama_topik NOT LIKE '%SIMULASI%'")->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($nonSimTopiks)) {
                $inNonSim = implode(',', array_map('intval', $nonSimTopiks));
                $db_siakad->exec("DELETE FROM bs_soal WHERE topik_id IN ({$inNonSim}) AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')");
                if ($db_core !== $db_siakad) {
                    $db_core->exec("DELETE FROM bs_soal WHERE topik_id IN ({$inNonSim}) AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')");
                }
                $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN (SELECT id FROM cbt_ujian WHERE topik_id IN ({$inNonSim}) AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%'))");
                $db_core->exec("DELETE FROM cbt_ujian WHERE topik_id IN ({$inNonSim}) AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')");
            }
        } catch (\Exception $e) {}
    }

    /**
     * Generate 50 Soal Biologi & 50 Soal Bahasa Arab per Tingkat (Kelas X, XI, XII)
     * KHUSUS & HANYA UNTUK TOPIK SIMULASI (SIMULASI CBT NEW)!
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

        // 1. Bersihkan topik duplikat jika tidak ada topik spesifik yang di-request
        if (!$topikId) {
            self::cleanupUnwantedTopics($db_siakad, $db_core);
        }

        // 2. Tentukan target topik
        $targetTopiks = [];
        if ($topikId && (int)$topikId > 0) {
            $targetTopiks[] = (int)$topikId;
        } else {
            // Cari topik yang namanya memuat kata 'SIMULASI'
            try {
                $simId = $db_siakad->query("SELECT id FROM bs_topik WHERE nama_topik LIKE '%SIMULASI%' ORDER BY id DESC LIMIT 1")->fetchColumn();
                if ($simId) {
                    $targetTopiks[] = (int)$simId;
                }
            } catch (\Exception $e) {}

            if (empty($targetTopiks)) {
                try {
                    $simId2 = $db_core->query("SELECT id FROM bs_topik WHERE nama_topik LIKE '%SIMULASI%' ORDER BY id DESC LIMIT 1")->fetchColumn();
                    if ($simId2) {
                        $targetTopiks[] = (int)$simId2;
                    }
                } catch (\Exception $e) {}
            }

            if (empty($targetTopiks)) {
                $targetTopiks = [7]; // Default ID SIMULASI CBT NEW
            }
        }

        $targetTopiks = array_unique(array_filter($targetTopiks));

        $totalInserted = 0;

        foreach ($targetTopiks as $tId) {
            // Cek apakah topik ada di database (siakad atau core)
            $cekT = (int)$db_siakad->query("SELECT COUNT(*) FROM bs_topik WHERE id = {$tId}")->fetchColumn();
            if ($cekT === 0) {
                try { $cekT = (int)$db_core->query("SELECT COUNT(*) FROM bs_topik WHERE id = {$tId}")->fetchColumn(); } catch (\Exception $e) {}
            }
            if ($cekT === 0) {
                continue;
            }

            // Ambil nama topik asli agar nama ujian seragam dan konsisten
            $topikNama = 'SIMULASI CBT';
            try {
                $tpRow = $db_siakad->query("SELECT nama_topik FROM bs_topik WHERE id = {$tId}")->fetch(PDO::FETCH_ASSOC);
                if (!$tpRow && $db_core !== $db_siakad) {
                    $tpRow = $db_core->query("SELECT nama_topik FROM bs_topik WHERE id = {$tId}")->fetch(PDO::FETCH_ASSOC);
                }
                if ($tpRow && !empty($tpRow['nama_topik'])) {
                    $topikNama = trim($tpRow['nama_topik']);
                }
            } catch (\Exception $e) {}

            // Bersihkan butir soal lama Biologi & Bahasa Arab untuk topik ini agar fresh & tidak double
            try {
                $db_siakad->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')")->execute([$tId]);
                $db_siakad->prepare("DELETE FROM bs_soal WHERE mapel_nama LIKE '%Peminatan%'")->execute();
                $db_siakad->prepare("DELETE FROM bs_paket WHERE mapel_nama LIKE '%Peminatan%'")->execute();
            } catch (\Exception $e) {}
            try {
                if ($db_core !== $db_siakad) {
                    $db_core->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND (mapel_nama LIKE '%Biologi%' OR mapel_nama LIKE '%Arab%')")->execute([$tId]);
                }
                $pemUjianIds = $db_core->query("SELECT id FROM cbt_ujian WHERE mapel_nama LIKE '%Peminatan%'")->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($pemUjianIds)) {
                    $inPem = implode(',', array_map('intval', $pemUjianIds));
                    $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inPem})");
                    $db_core->exec("DELETE FROM cbt_ujian WHERE id IN ({$inPem})");
                }
                $db_core->exec("DELETE FROM cbt_jadwal_grid WHERE mapel_nama LIKE '%Peminatan%'");
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
                $bioMapelList = [];
                try {
                    $stmtCekBio = $db_siakad->prepare("SELECT DISTINCT mapel_nama FROM bs_paket WHERE topik_id = ? AND mapel_nama LIKE '%Biologi%' AND (kelas_nama = ? OR kelas_nama LIKE ?)");
                    $stmtCekBio->execute([$tId, $lvl['tingkat'], '%' . $lvl['tingkat'] . '%']);
                    $bioMapelList = $stmtCekBio->fetchAll(PDO::FETCH_COLUMN);
                } catch (\Exception $e) {}

                if (empty($bioMapelList)) {
                    $bioMapelList = ['Biologi'];
                }

                foreach ($bioMapelList as $bMapel) {
                    $inserted = self::insertSubjectPackage(
                        $db_siakad, $db_core, $tId, $bMapel,
                        $lvl['tingkat'], $lvl['rombel'], $lvl['kode'], $lvl['guru'],
                        $topikNama . ' - ' . $bMapel . ' (' . $lvl['tingkat'] . ')', $lvl['dataset']
                    );
                    $totalInserted += $inserted;
                }
            }

            // ══════════════════════════════════════════════════════════════
            // 2. GENERATE SOAL BAHASA ARAB (KELAS X, XI, XII)
            // Catatan: Cek apakah di bs_paket menggunakan nama "Bahasa Arab Peminatan" atau "Bahasa Arab"
            // Kita dukung kedua varian agar selalu match dengan tampilan Bank Soal user!
            // ══════════════════════════════════════════════════════════════
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
                // Ambil semua varian nama Bahasa Arab di bs_paket untuk tingkat ini (kecuali Peminatan)
                $arabMapelList = [];
                try {
                    $stmtCekArab = $db_siakad->prepare("SELECT DISTINCT mapel_nama FROM bs_paket WHERE topik_id = ? AND mapel_nama LIKE '%Arab%' AND mapel_nama NOT LIKE '%Peminatan%' AND (kelas_nama = ? OR kelas_nama LIKE ?)");
                    $stmtCekArab->execute([$tId, $lvl['tingkat'], '%' . $lvl['tingkat'] . '%']);
                    $arabMapelList = $stmtCekArab->fetchAll(PDO::FETCH_COLUMN);
                } catch (\Exception $e) {}

                if (empty($arabMapelList)) {
                    $arabMapelList = ['Bahasa Arab'];
                }

                foreach ($arabMapelList as $aMapel) {
                    if (stripos($aMapel, 'peminatan') !== false) continue;
                    $inserted = self::insertSubjectPackage(
                        $db_siakad, $db_core, $tId, $aMapel,
                        $lvl['tingkat'], $lvl['rombel'], $lvl['kode'], $lvl['guru'],
                        $topikNama . ' - ' . $aMapel . ' (' . $lvl['tingkat'] . ')', $lvl['dataset']
                    );
                    $totalInserted += $inserted;
                }
            }
        }

        // Deduplikasi cbt_ujian agar bersih tanpa ada ujian ganda
        self::deduplicateCbtUjian($db_core, $db_siakad);

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
        // Batasi tepat 10 butir soal untuk simulasi sesuai instruksi user
        $dataset = array_slice($dataset, 0, 10);
        foreach ($dataset as $idx => &$item) {
            $item['nomor_urut'] = $idx + 1;
            $item['skor_maksimal'] = 10.00;
        }
        unset($item);

        // 1. Pastikan Paket di bs_paket ada
        $stmtP = $db_siakad->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama = ?) LIMIT 1");
        $stmtP->execute([$topikId, $mapelNama, $tingkat, '%' . $tingkat . '%', $rombel]);
        $paketId = (int)$stmtP->fetchColumn();

        if (!$paketId) {
            $stmtInsP = $db_siakad->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, ?, 75, 60, 0)");
            $stmtInsP->execute([$topikId, $mapelNama, $tingkat, $rombel, $guru]);
            $paketId = (int)$db_siakad->lastInsertId();
        } else {
            $db_siakad->prepare("UPDATE bs_paket SET guru_nama = ?, kelas_sasaran = ?, kkm = 75, durasi_menit = 60, is_locked = 0 WHERE id = ?")->execute([$guru, $rombel, $paketId]);
        }

        // Sinkronkan juga bs_paket ke db_core jika terpisah
        if ($db_core !== $db_siakad) {
            try {
                $stmtPCore = $db_core->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama = ?) LIMIT 1");
                $stmtPCore->execute([$topikId, $mapelNama, $tingkat, '%' . $tingkat . '%', $rombel]);
                $pIdCore = (int)$stmtPCore->fetchColumn();
                if (!$pIdCore) {
                    $db_core->prepare("INSERT INTO bs_paket (id, topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, ?, ?, 75, 60, 0)")
                            ->execute([$paketId, $topikId, $mapelNama, $tingkat, $rombel, $guru]);
                } else {
                    $db_core->prepare("UPDATE bs_paket SET guru_nama = ?, kelas_sasaran = ?, kkm = 75, durasi_menit = 60, is_locked = 0 WHERE id = ?")->execute([$guru, $rombel, $pIdCore]);
                }
            } catch (\Exception $e) {}
        }

        // 2. Pastikan cbt_ujian ada dan Aktif
        $stmtU = $db_core->prepare("SELECT id FROM cbt_ujian WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama = 'Semua Kelas') ORDER BY id ASC LIMIT 1");
        $stmtU->execute([$topikId, $mapelNama, $tingkat, '%' . $tingkat . '%']);
        $ujianId = (int)$stmtU->fetchColumn();

        if (!$ujianId) {
            $stmtInsU = $db_core->prepare("INSERT INTO cbt_ujian (topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, status) VALUES (?, ?, ?, ?, ?, 60, 75, 1, 1, 'Aktif')");
            $stmtInsU->execute([$topikId, $kodeUjian, $namaUjian, $mapelNama, $tingkat]);
            $ujianId = (int)$db_core->lastInsertId();
        } else {
            $db_core->prepare("UPDATE cbt_ujian SET status = 'Aktif', durasi_menit = 60, kkm = 75, nama_ujian = ?, kelas_nama = ? WHERE id = ?")->execute([$namaUjian, $tingkat, $ujianId]);
        }

        // Hubungkan ke jadwal_grid agar semua rombel kelas ini memakai ujian ini
        try {
            if ($tingkat === 'Kelas XII') {
                $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = 'XII F.1' OR kelas_nama = 'XII F.2' OR kelas_nama = 'Kelas XII')")->execute([$ujianId, $topikId, $mapelNama]);
            } elseif ($tingkat === 'Kelas XI') {
                $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = 'XI F.1' OR kelas_nama = 'Kelas XI')")->execute([$ujianId, $topikId, $mapelNama]);
            } else {
                $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = 'X E.1' OR kelas_nama = 'Kelas X')")->execute([$ujianId, $topikId, $mapelNama]);
            }
        } catch (\Exception $e) {}

        // Bersihkan butir soal lama di paket dan ujian ini agar tepat 10 butir (tidak double)
        try { $db_siakad->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]); } catch (\Exception $e) {}
        if ($db_core !== $db_siakad) {
            try { $db_core->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]); } catch (\Exception $e) {}
        }
        try { $db_core->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$ujianId]); } catch (\Exception $e) {}

        // Reset riwayat peserta pada ujian ini agar bisa tes simulasi fresh
        try {
            $db_core->prepare("DELETE FROM cbt_peserta_jawaban WHERE ujian_id = ?")->execute([$ujianId]);
            $db_core->prepare("DELETE FROM cbt_jawaban WHERE ujian_id = ?")->execute([$ujianId]);
            $db_core->prepare("DELETE FROM cbt_peserta WHERE ujian_id = ?")->execute([$ujianId]);
        } catch (\Exception $e) {}

        // 3. Masukkan 10 Butir Soal ke bs_soal dan cbt_soal
        $stmtBs = $db_siakad->prepare("
            INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtCbt = $db_core->prepare("
            INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtBsCore = ($db_core !== $db_siakad) ? $db_core->prepare("
            INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ") : null;

        $count = 0;
        foreach ($dataset as $soal) {
            $no = (int)$soal['nomor_urut'];
            $tipe = $soal['tipe_soal'];
            $tkesulitan = $soal['tingkat_kesulitan'] ?? 'Sedang';
            $skor = (float)($soal['skor_maksimal'] ?? 10.00);
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

            if ($stmtBsCore) {
                try {
                    $stmtBsCore->execute([
                        $topikId, $paketId, $mapelNama, $tingkat, $no, $tipe, $tkesulitan, $skor,
                        $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $konten
                    ]);
                } catch (\Exception $e) {}
            }

            // Insert ke cbt_soal
            $stmtCbt->execute([
                $ujianId, $no, $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $skor, $tipe, $konten
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Membersihkan dan mendeduplikasi cbt_ujian agar setiap Mapel & Tingkat Kelas hanya memiliki tepat 1 Ujian Aktif
     */
    public static function deduplicateCbtUjian($db_core, $db_siakad = null) {
        if (!$db_siakad) {
            $db_siakad = Database::connect('siakad');
        }

        // 1. Dapatkan daftar ID topik yang sah di bs_topik
        $validTopikIds = [];
        try {
            $validTopikIds = $db_siakad->query("SELECT id FROM bs_topik")->fetchAll(PDO::FETCH_COLUMN);
            $validTopikIds = array_map('intval', $validTopikIds);
        } catch (\Exception $e) {}

        // Cari ID topik SIMULASI
        $simTopikId = 7;
        try {
            $sId = $db_siakad->query("SELECT id FROM bs_topik WHERE nama_topik LIKE '%SIMULASI%' ORDER BY id DESC LIMIT 1")->fetchColumn();
            if ($sId) $simTopikId = (int)$sId;
        } catch (\Exception $e) {}

        // 2. Ambil semua ujian dari cbt_ujian
        try {
            $allUjian = $db_core->query("
                SELECT u.id, u.topik_id, u.kode_ujian, u.nama_ujian, u.mapel_nama, u.kelas_nama, u.status,
                       (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as cnt_soal
                FROM cbt_ujian u
                ORDER BY u.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);

            // Grouping berdasarkan Normalisasi Kelas + Mapel
            $grouped = [];
            foreach ($allUjian as $u) {
                // Normalisasi Kelas
                $k = strtolower(trim($u['kelas_nama'] ?? ''));
                if (strpos($k, 'xii') !== false || strpos($k, '12') !== false) {
                    $normKelas = 'XII';
                } elseif (strpos($k, 'xi') !== false || strpos($k, '11') !== false) {
                    $normKelas = 'XI';
                } elseif (strpos($k, 'x') !== false || strpos($k, '10') !== false) {
                    $normKelas = 'X';
                } else {
                    $normKelas = 'Semua';
                }

                // Normalisasi Mapel
                $m = strtolower(trim($u['mapel_nama'] ?? ''));
                if (strpos($m, 'arab') !== false) {
                    $normMapel = 'arab';
                } elseif (strpos($m, 'biologi') !== false) {
                    $normMapel = 'biologi';
                } elseif (strpos($m, 'matematika') !== false) {
                    $normMapel = 'matematika';
                } else {
                    $normMapel = $m;
                }

                $key = $normKelas . '_' . $normMapel;
                $grouped[$key][] = $u;
            }

            $toDeleteUjianIds = [];
            $toKeepUjianIds = [];

            foreach ($grouped as $key => $items) {
                if (count($items) === 1) {
                    $keepItem = $items[0];
                    $toKeepUjianIds[] = (int)$keepItem['id'];
                } else {
                    // Sorting item terbaik yang akan dipertahankan:
                    // 1. Topik SIMULASI (topik_id == $simTopikId atau nama_ujian memuat SIMULASI)
                    // 2. Jumlah butir soal terbanyak
                    // 3. ID topik valid di bs_topik
                    // 4. ID ujian terbesar
                    usort($items, function($a, $b) use ($simTopikId, $validTopikIds) {
                        $isSimA = ((int)$a['topik_id'] === $simTopikId || stripos($a['nama_ujian'], 'SIMULASI') !== false) ? 1 : 0;
                        $isSimB = ((int)$b['topik_id'] === $simTopikId || stripos($b['nama_ujian'], 'SIMULASI') !== false) ? 1 : 0;
                        if ($isSimA !== $isSimB) return $isSimB - $isSimA;

                        $soalA = (int)$a['cnt_soal'];
                        $soalB = (int)$b['cnt_soal'];
                        if ($soalA !== $soalB) return $soalB - $soalA;

                        $validA = in_array((int)$a['topik_id'], $validTopikIds) ? 1 : 0;
                        $validB = in_array((int)$b['topik_id'], $validTopikIds) ? 1 : 0;
                        if ($validA !== $validB) return $validB - $validA;

                        return (int)$b['id'] - (int)$a['id'];
                    });

                    // Item pertama dipertahankan
                    $winner = array_shift($items);
                    $toKeepUjianIds[] = (int)$winner['id'];

                    // Sisanya dihapus dan arahkan referensi jadwal grid ke pemenang
                    foreach ($items as $loser) {
                        $toDeleteUjianIds[] = (int)$loser['id'];
                        try {
                            $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE ujian_id = ?")->execute([(int)$winner['id'], (int)$loser['id']]);
                        } catch (\Exception $e) {}
                    }
                }
            }

            // Hapus duplikat cbt_ujian dan relasinya
            if (!empty($toDeleteUjianIds)) {
                $delStr = implode(',', $toDeleteUjianIds);
                try { $db_core->exec("DELETE FROM cbt_peserta_jawaban WHERE ujian_id IN ({$delStr})"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM cbt_jawaban WHERE ujian_id IN ({$delStr})"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM cbt_peserta WHERE ujian_id IN ({$delStr})"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$delStr})"); } catch (\Exception $e) {}
                try { $db_core->exec("DELETE FROM cbt_ujian WHERE id IN ({$delStr})"); } catch (\Exception $e) {}
            }

            // Untuk ujian yang dipertahankan dan bertipe Simulasi, pastikan topik_id diset ke $simTopikId
            if (!empty($toKeepUjianIds) && $simTopikId > 0) {
                $keepStr = implode(',', $toKeepUjianIds);
                try {
                    $db_core->exec("UPDATE cbt_ujian SET topik_id = {$simTopikId}, status = 'Aktif' WHERE id IN ({$keepStr}) AND nama_ujian LIKE '%Simulasi%'");
                } catch (\Exception $e) {}
            }

            return count($toDeleteUjianIds);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
