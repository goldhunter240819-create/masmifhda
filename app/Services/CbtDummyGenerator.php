<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class CbtDummyGenerator {

    /**
     * Generate 50 Soal Simulasi Matematika per Tingkat (Kelas X, XI, XII)
     * Menggunakan 5 Model Soal CBT AKM (PG, PGK, Benar/Salah, Menjodohkan, Isian)
     * Disertai Teks Stimulus Kontekstual, Kata Bergaris Bawah (<u>), Kata Tebal (<strong>), dan Diagram SVG.
     */
    public static function generateAll($topikId = null) {
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');

        // Pastikan tabel CBT dan kolom tipe_soal sudah tersedia
        \App\Controllers\CbtController::ensureTables($db_core);

        if (!$topikId) {
            $tp = $db_siakad->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tp) {
                $tp = $db_core->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            }
            $topikId = $tp ? (int)$tp['id'] : 1;
        }

        // Ambil nama topik asli agar nama ujian seragam dan konsisten
        $topikNama = 'SIMULASI CBT';
        try {
            $tpRow = $db_siakad->query("SELECT nama_topik FROM bs_topik WHERE id = {$topikId}")->fetch(PDO::FETCH_ASSOC);
            if (!$tpRow && $db_core !== $db_siakad) {
                $tpRow = $db_core->query("SELECT nama_topik FROM bs_topik WHERE id = {$topikId}")->fetch(PDO::FETCH_ASSOC);
            }
            if ($tpRow && !empty($tpRow['nama_topik'])) {
                $topikNama = trim($tpRow['nama_topik']);
            }
        } catch (\Exception $e) {}

        // 1. Bersihkan butir soal lama Matematika pada topik ini (agar tidak ganda)
        try {
            $db_siakad->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND mapel_nama = 'Matematika'")->execute([$topikId]);
        } catch (\Exception $e) {}
        try {
            if ($db_core !== $db_siakad) {
                $db_core->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND mapel_nama = 'Matematika'")->execute([$topikId]);
            }
        } catch (\Exception $e) {}

        // Bersihkan cbt_soal untuk ujian matematika topik ini
        try {
            $uIds = $db_core->query("SELECT id FROM cbt_ujian WHERE (topik_id = {$topikId} OR topik_id IS NULL) AND mapel_nama = 'Matematika'")->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($uIds)) {
                $inIds = implode(',', array_map('intval', $uIds));
                $db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inIds})");
            }
        } catch (\Exception $e) {}

        $levels = [
            [
                'tingkat' => 'Kelas X',
                'rombel'  => 'X E.1',
                'kode'    => 'UJN-MAT-X',
                'guru'    => 'SAWALUDIN, S.Pd',
                'topik_label' => 'Fase E (Eksponen, SPLDV, Barisan & Geometri Dasar)',
                'dataset' => self::getDataKelasX()
            ],
            [
                'tingkat' => 'Kelas XI',
                'rombel'  => 'XI F.1',
                'kode'    => 'UJN-MAT-XI',
                'guru'    => 'AHMAD NABHAN, S.Pd.I',
                'topik_label' => 'Fase F (Matriks, Program Linear, Lingkaran & Deret)',
                'dataset' => self::getDataKelasXI()
            ],
            [
                'tingkat' => 'Kelas XII',
                'rombel'  => 'XII F.2',
                'kode'    => 'UJN-MAT-XII',
                'guru'    => 'AHMAD NABHAN, S.Pd.I',
                'topik_label' => 'Fase F+ (Dimensi Tiga, Statistika Lanjut, Peluang & Kalkulus)',
                'dataset' => self::getDataKelasXII()
            ]
        ];

        $totalInserted = 0;

        foreach ($levels as $lvl) {
            $tingkat = $lvl['tingkat'];
            $rombel = $lvl['rombel'];
            $kode = $lvl['kode'];
            $guru = $lvl['guru'];
            $dataset = $lvl['dataset'];

            // 1. Pastikan Paket di bs_paket ada
            $stmtP = $db_siakad->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama LIKE '%Matematika%' AND (kelas_nama = ? OR kelas_nama = ?) LIMIT 1");
            $stmtP->execute([$topikId, $tingkat, $rombel]);
            $paketId = (int)$stmtP->fetchColumn();

            if (!$paketId) {
                $stmtInsP = $db_siakad->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, 'Matematika', ?, ?, 75, 90, 0)");
                $stmtInsP->execute([$topikId, $tingkat, $guru]);
                $paketId = (int)$db_siakad->lastInsertId();
            } else {
                $db_siakad->prepare("UPDATE bs_paket SET guru_nama = ?, kkm = 75, durasi_menit = 90, is_locked = 0 WHERE id = ?")->execute([$guru, $paketId]);
            }

            // Sinkronkan juga bs_paket ke db_core jika terpisah
            if ($db_core !== $db_siakad) {
                try {
                    $stmtPCore = $db_core->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama LIKE '%Matematika%' AND (kelas_nama = ? OR kelas_nama = ?) LIMIT 1");
                    $stmtPCore->execute([$topikId, $tingkat, $rombel]);
                    $pIdCore = (int)$stmtPCore->fetchColumn();
                    if (!$pIdCore) {
                        $db_core->prepare("INSERT INTO bs_paket (id, topik_id, mapel_nama, kelas_nama, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, 'Matematika', ?, ?, 75, 90, 0)")
                                ->execute([$paketId, $topikId, $tingkat, $guru]);
                    } else {
                        $db_core->prepare("UPDATE bs_paket SET guru_nama = ?, kkm = 75, durasi_menit = 90, is_locked = 0 WHERE id = ?")->execute([$guru, $pIdCore]);
                    }
                } catch (\Exception $e) {}
            }

            // 2. Pastikan cbt_ujian ada dan Aktif
            $stmtU = $db_core->prepare("SELECT id FROM cbt_ujian WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama LIKE '%Matematika%' AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama = 'Semua Kelas') LIMIT 1");
            $stmtU->execute([$topikId, $tingkat, $rombel]);
            $ujianId = (int)$stmtU->fetchColumn();

            $namaUjian = "{$topikNama} - Matematika ({$tingkat})";
            if (!$ujianId) {
                $stmtInsU = $db_core->prepare("INSERT INTO cbt_ujian (topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, status) VALUES (?, ?, ?, 'Matematika', ?, 90, 75, 1, 1, 'Aktif')");
                $stmtInsU->execute([$topikId, $kode, $namaUjian, $tingkat]);
                $ujianId = (int)$db_core->lastInsertId();
            } else {
                $db_core->prepare("UPDATE cbt_ujian SET status = 'Aktif', durasi_menit = 90, kkm = 75, nama_ujian = ? WHERE id = ?")->execute([$namaUjian, $ujianId]);
            }

            // Hubungkan juga ke jadwal_grid jika ada slot kelas ini
            try {
                $db_core->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama LIKE '%Matematika%' AND (kelas_nama = ? OR kelas_nama = ?)")->execute([$ujianId, $topikId, $rombel, $tingkat]);
            } catch (\Exception $e) {}

            // Bersihkan butir soal lama di paket ini agar tidak double
            try { $db_siakad->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]); } catch (\Exception $e) {}
            if ($db_core !== $db_siakad) {
                try { $db_core->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]); } catch (\Exception $e) {}
            }
            try { $db_core->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$ujianId]); } catch (\Exception $e) {}

            // 3. Masukkan 50 Butir Soal ke bs_soal dan cbt_soal
            $stmtBs = $db_siakad->prepare("
                INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
                VALUES (?, ?, 'Matematika', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmtCbt = $db_core->prepare("
                INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmtBsCore = ($db_core !== $db_siakad) ? $db_core->prepare("
                INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
                VALUES (?, ?, 'Matematika', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ") : null;

            foreach ($dataset as $soal) {
                $no = (int)$soal['nomor_urut'];
                $tipe = $soal['tipe_soal'];
                $tkesulitan = $soal['tingkat_kesulitan'] ?? 'Mudah';
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
                    $topikId, $paketId, $tingkat, $no, $tipe, $tkesulitan, $skor,
                    $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $konten
                ]);

                if ($stmtBsCore) {
                    try {
                        $stmtBsCore->execute([
                            $topikId, $paketId, $tingkat, $no, $tipe, $tkesulitan, $skor,
                            $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $konten
                        ]);
                    } catch (\Exception $e) {}
                }

                // Insert ke cbt_soal
                $stmtCbt->execute([
                    $ujianId, $no, $tanya, $opA, $opB, $opC, $opD, $opE, $kunci, $skor, $tipe, $konten
                ]);

                $totalInserted++;
            }
        }

        // 4. Pastikan ada Token Ujian Aktif di cbt_token
        try {
            $tokAktif = $db_core->query("SELECT * FROM cbt_token WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tokAktif) {
                $db_core->exec("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES ('MAS26A', 240, 'Aktif', NOW())");
            }
        } catch (\Exception $e) {}

        return $totalInserted;
    }

    /**
     * Helper untuk membuat kotak Stimulus ber-CSS indah dengan Inline SVG
     */
    private static function wrapStimulus($stimulusNo, $soalRange, $title, $svgDiagram, $storyHtml, $badgeTipe, $questionPrompt) {
        $html = '<div style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:14px; padding:14px 16px; margin-bottom:14px; box-shadow:0 3px 8px rgba(0,0,0,0.03); font-family:sans-serif;">';
        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px 12px; margin-bottom:8px; border-bottom:1.5px solid #e2e8f0; padding-bottom:8px;">';
        $html .= '<span style="font-size:0.75rem; font-weight:800; background:#0284c7; color:#ffffff; padding:4px 10px; border-radius:20px; letter-spacing:0.3px;">STIMULUS ' . $stimulusNo . ' (UNTUK SOAL NO. ' . $soalRange . ')</span>';
        $html .= '<span style="font-size:0.75rem; font-weight:700; color:#64748b;">Simulasi Asesmen AKM</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:1rem; font-weight:800; color:#0f172a; margin-bottom:10px;">' . $title . '</div>';
        if (!empty($svgDiagram)) {
            $html .= '<div style="text-align:center; margin:10px 0 14px 0; background:#ffffff; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">' . $svgDiagram . '</div>';
        }
        $html .= '<div style="font-size:0.88rem; color:#334155; line-height:1.65; margin-bottom:4px;">' . $storyHtml . '</div>';
        $html .= '</div>';

        // Badge Tipe Soal & Pertanyaan
        $html .= '<div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">';
        $html .= '<span style="font-size:0.72rem; font-weight:800; background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; border:1px solid #cbd5e1;">' . $badgeTipe . '</span>';
        $html .= '</div>';
        $html .= '<div style="font-size:0.95rem; font-weight:700; color:#0f172a; line-height:1.5;">' . $questionPrompt . '</div>';

        return $html;
    }

    /**
     * KELAS X DATASET (50 Butir Soal = 10 Stimulus × 5 Model Soal)
     */
    private static function getDataKelasX() {
        $list = [];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 1 (SOAL 1 - 5): Belanja Buku & Diskon di Koperasi Siswa
        // ══════════════════════════════════════════════════════════════════
        $svg1 = '<svg width="280" height="90" viewBox="0 0 280 90" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="10" width="120" height="70" rx="8" fill="#e0f2fe" stroke="#0284c7" stroke-width="2"/>
            <text x="70" y="32" font-size="11" font-weight="bold" fill="#0369a1" text-anchor="middle">PAKET A (BUKU TULIS)</text>
            <text x="70" y="52" font-size="14" font-weight="bold" fill="#0f172a" text-anchor="middle">Rp 50.000</text>
            <text x="70" y="70" font-size="10" fill="#16a34a" text-anchor="middle">Diskon 10%</text>
            <rect x="150" y="10" width="120" height="70" rx="8" fill="#fef3c7" stroke="#d97706" stroke-width="2"/>
            <text x="210" y="32" font-size="11" font-weight="bold" fill="#b45309" text-anchor="middle">PAKET B (ALAT TULIS)</text>
            <text x="210" y="52" font-size="14" font-weight="bold" fill="#0f172a" text-anchor="middle">Rp 80.000</text>
            <text x="210" y="70" font-size="10" fill="#16a34a" text-anchor="middle">Diskon 25%</text>
        </svg>';
        $story1 = 'Dalam rangka pekan milad madrasah, koperasi sekolah memberikan <u>potongan harga (diskon)</u> khusus untuk dua paket perlengkapan belajar. Paket A seharga <strong>Rp 50.000</strong> mendapat <u>diskon 10%</u>, sedangkan Paket B seharga <strong>Rp 80.000</strong> mendapat <u>diskon 25%</u>. Setiap siswa yang berbelanja dapat menghitung penghematan yang didapatkan.';

        // No 1: PG
        $list[] = [
            'nomor_urut' => 1,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => self::wrapStimulus(1, '1 - 5', 'Diskon Perlengkapan Sekolah di Koperasi Siswa', $svg1, $story1, '1. PILIHAN GANDA', 'Berdasarkan teks stimulus di atas, berapakah harga yang harus dibayar siswa untuk membeli <u>Paket A setelah dipotong diskon</u>?'),
            'opsi_a' => 'Rp 40.000', 'opsi_b' => 'Rp 45.000', 'opsi_c' => 'Rp 48.000', 'opsi_d' => 'Rp 42.500', 'opsi_e' => 'Rp 47.000',
            'kunci_jawaban' => 'B',
            'konten_tambahan' => ['kunci' => 'B', 'opsi' => ['A' => 'Rp 40.000', 'B' => 'Rp 45.000', 'C' => 'Rp 48.000', 'D' => 'Rp 42.500', 'E' => 'Rp 47.000']]
        ];

        // No 2: PGK
        $list[] = [
            'nomor_urut' => 2,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => self::wrapStimulus(1, '1 - 5', 'Diskon Perlengkapan Sekolah di Koperasi Siswa', '', $story1, '2. PG KOMPLEKS (CENTANG)', 'Berilah tanda centang (✓) pada setiap pernyataan yang <strong>BENAR</strong> mengenai perhitungan harga belanja di koperasi! (Jawaban benar lebih dari satu)'),
            'opsi_a' => 'Besar potongan harga untuk Paket A adalah Rp 5.000.',
            'opsi_b' => 'Besar potongan harga untuk Paket B adalah Rp 20.000.',
            'opsi_c' => 'Harga Paket B setelah diskon menjadi Rp 60.000.',
            'opsi_d' => 'Jika membeli Paket A dan Paket B sekaligus, total diskon yang didapat adalah Rp 35.000.',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => 'Besar potongan harga untuk Paket A adalah Rp 5.000.', 'is_kunci' => true],
                    ['teks' => 'Besar potongan harga untuk Paket B adalah Rp 20.000.', 'is_kunci' => true],
                    ['teks' => 'Harga Paket B setelah diskon menjadi Rp 60.000.', 'is_kunci' => true],
                    ['teks' => 'Jika membeli Paket A dan Paket B sekaligus, total diskon yang didapat adalah Rp 35.000.', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 3: B/S
        $list[] = [
            'nomor_urut' => 3,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => self::wrapStimulus(1, '1 - 5', 'Diskon Perlengkapan Sekolah di Koperasi Siswa', '', $story1, '3. TABEL BENAR / SALAH', 'Tentukan status kebenaran (Benar atau Salah) dari masing-masing pernyataan perbandingan diskon berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Benar', 'Salah']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Persentase diskon Paket B lebih besar daripada persentase diskon Paket A.', 'kunci' => 'Benar'],
                    ['teks' => 'Total yang dibayar siswa jika membeli kedua paket adalah Rp 105.000.', 'kunci' => 'Benar'],
                    ['teks' => 'Uang Rp 100.000 cukup untuk membeli Paket A dan Paket B secara bersamaan.', 'kunci' => 'Salah']
                ]
            ]
        ];

        // No 4: Menjodohkan
        $list[] = [
            'nomor_urut' => 4,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => self::wrapStimulus(1, '1 - 5', 'Diskon Perlengkapan Sekolah di Koperasi Siswa', '', $story1, '4. MENJODOHKAN', 'Pasangkan rincian transaksi belanja pada kolom kiri dengan nominal uang yang sesuai pada kolom kanan:'),
            'kunci_jawaban' => json_encode([
                ['premis' => 'Potongan harga Paket A', 'respon' => 'Rp 5.000'],
                ['premis' => 'Potongan harga Paket B', 'respon' => 'Rp 20.000'],
                ['premis' => 'Harga akhir Paket B', 'respon' => 'Rp 60.000'],
                ['premis' => 'Total hemat 2 paket', 'respon' => 'Rp 25.000']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => 'Potongan harga Paket A', 'respon' => 'Rp 5.000'],
                    ['premis' => 'Potongan harga Paket B', 'respon' => 'Rp 20.000'],
                    ['premis' => 'Harga akhir Paket B', 'respon' => 'Rp 60.000'],
                    ['premis' => 'Total hemat 2 paket', 'respon' => 'Rp 25.000']
                ]
            ]
        ];

        // No 5: Isian Singkat
        $list[] = [
            'nomor_urut' => 5,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => self::wrapStimulus(1, '1 - 5', 'Diskon Perlengkapan Sekolah di Koperasi Siswa', '', $story1, '5. ISIAN SINGKAT', 'Seorang siswa membeli 1 Paket A (setelah diskon) dan membayar dengan selembar uang pecahan <u>Rp 50.000</u>. Berapakah <u>rupiah kembalian</u> yang diterima siswa tersebut? (Tuliskan nominal angkanya saja)'),
            'kunci_jawaban' => '5000',
            'konten_tambahan' => ['rubrik' => '5000 (atau Rp 5.000)']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 2 (SOAL 6 - 10): Pertumbuhan Eksponen Bakteri
        // ══════════════════════════════════════════════════════════════════
        $svg2 = '<svg width="280" height="90" viewBox="0 0 280 90" xmlns="http://www.w3.org/2000/svg">
            <circle cx="30" cy="45" r="16" fill="#fbcfe8" stroke="#db2777" stroke-width="2"/>
            <text x="30" y="50" font-size="10" font-weight="bold" fill="#9d174d" text-anchor="middle">1 Sel</text>
            <path d="M 55 45 L 90 45" stroke="#94a3b8" stroke-width="2" marker-end="url(#arrow)"/>
            <circle cx="115" cy="28" r="13" fill="#fbcfe8" stroke="#db2777" stroke-width="1.5"/>
            <circle cx="115" cy="62" r="13" fill="#fbcfe8" stroke="#db2777" stroke-width="1.5"/>
            <text x="115" y="48" font-size="9" font-weight="bold" fill="#9d174d" text-anchor="middle">2 Sel</text>
            <path d="M 135 45 L 170 45" stroke="#94a3b8" stroke-width="2"/>
            <circle cx="195" cy="18" r="10" fill="#fbcfe8" stroke="#db2777" stroke-width="1.2"/>
            <circle cx="195" cy="38" r="10" fill="#fbcfe8" stroke="#db2777" stroke-width="1.2"/>
            <circle cx="195" cy="58" r="10" fill="#fbcfe8" stroke="#db2777" stroke-width="1.2"/>
            <circle cx="195" cy="78" r="10" fill="#fbcfe8" stroke="#db2777" stroke-width="1.2"/>
            <text x="240" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">4 Sel (2²)</text>
        </svg>';
        $story2 = 'Di laboratorium IPA madrasah, diamati pertumbuhan suatu koloni bakteri. Setiap <strong>1 jam</strong>, satu bakteri akan <u>membelah diri menjadi 2 bakteri baru</u> secara eksponensial. Rumus jumlah bakteri setelah <em>t</em> jam dinyatakan dengan fungsi eksponen: <strong>N(t) = N₀ × 2ᵗ</strong>, dengan <u>N₀ sebagai jumlah mula-mula</u>.';

        // No 6: PG
        $list[] = [
            'nomor_urut' => 6,
            'tipe_soal' => 'sec_pg',
            'pertanyaan' => self::wrapStimulus(2, '6 - 10', 'Konsep Pertumbuhan Eksponen Bakteri', $svg2, $story2, '1. PILIHAN GANDA', 'Jika pada awal pengamatan mula-mula terdapat <strong>5 bakteri</strong> (N₀ = 5), berapakah <u>jumlah bakteri setelah 3 jam</u>?'),
            'opsi_a' => '20 bakteri', 'opsi_b' => '30 bakteri', 'opsi_c' => '40 bakteri', 'opsi_d' => '50 bakteri', 'opsi_e' => '60 bakteri',
            'kunci_jawaban' => 'C',
            'konten_tambahan' => ['kunci' => 'C', 'opsi' => ['A' => '20 bakteri', 'B' => '30 bakteri', 'C' => '40 bakteri', 'D' => '50 bakteri', 'E' => '60 bakteri']]
        ];

        // No 7: PGK
        $list[] = [
            'nomor_urut' => 7,
            'tipe_soal' => 'sec_pgk',
            'pertanyaan' => self::wrapStimulus(2, '6 - 10', 'Konsep Pertumbuhan Eksponen Bakteri', '', $story2, '2. PG KOMPLEKS (CENTANG)', 'Pilihlah seluruh <u>sifat-sifat eksponen</u> berikut yang bernilai <strong>BENAR</strong>! (Jawaban benar lebih dari satu)'),
            'opsi_a' => '2³ × 2² = 2^(3+2) = 2⁵ = 32',
            'opsi_b' => '2⁴ : 2² = 2^(4-2) = 2² = 4',
            'opsi_c' => '(2²)³ = 2^(2×3) = 2⁶ = 64',
            'opsi_d' => '2⁰ = 0',
            'opsi_e' => '',
            'kunci_jawaban' => 'A, B, C',
            'konten_tambahan' => [
                'opsi' => [
                    ['teks' => '2³ × 2² = 2^(3+2) = 2⁵ = 32', 'is_kunci' => true],
                    ['teks' => '2⁴ : 2² = 2^(4-2) = 2² = 4', 'is_kunci' => true],
                    ['teks' => '(2²)³ = 2^(2×3) = 2⁶ = 64', 'is_kunci' => true],
                    ['teks' => '2⁰ = 0', 'is_kunci' => false]
                ],
                'kunci_array' => ['A', 'B', 'C']
            ]
        ];

        // No 8: B/S
        $list[] = [
            'nomor_urut' => 8,
            'tipe_soal' => 'sec_bs',
            'pertanyaan' => self::wrapStimulus(2, '6 - 10', 'Konsep Pertumbuhan Eksponen Bakteri', '', $story2, '3. TABEL BENAR / SALAH', 'Tentukan kebenaran dari pernyataan mengenai sifat eksponen pembelahan bakteri berikut:'),
            'kunci_jawaban' => json_encode(['Benar', 'Salah', 'Benar']),
            'konten_tambahan' => [
                'butir' => [
                    ['teks' => 'Nilai dari 2⁴ adalah 16.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai dari 2⁻¹ adalah bilangan bernilai negatif.', 'kunci' => 'Salah'],
                    ['teks' => 'Pertumbuhan bakteri berlipat ganda setiap jam merupakan contoh fungsi eksponen bertumbuh.', 'kunci' => 'Benar']
                ]
            ]
        ];

        // No 9: Menjodohkan
        $list[] = [
            'nomor_urut' => 9,
            'tipe_soal' => 'sec_jodoh',
            'pertanyaan' => self::wrapStimulus(2, '6 - 10', 'Konsep Pertumbuhan Eksponen Bakteri', '', $story2, '4. MENJODOHKAN', 'Pasangkan bentuk pangkat (eksponen) di sebelah kiri dengan nilai numeriknya di sebelah kanan:'),
            'kunci_jawaban' => json_encode([
                ['premis' => '2³', 'respon' => '8'],
                ['premis' => '3²', 'respon' => '9'],
                ['premis' => '5²', 'respon' => '25'],
                ['premis' => '10²', 'respon' => '100']
            ]),
            'konten_tambahan' => [
                'pasangan' => [
                    ['premis' => '2³', 'respon' => '8'],
                    ['premis' => '3²', 'respon' => '9'],
                    ['premis' => '5²', 'respon' => '25'],
                    ['premis' => '10²', 'respon' => '100']
                ]
            ]
        ];

        // No 10: Isian Singkat
        $list[] = [
            'nomor_urut' => 10,
            'tipe_soal' => 'sec_isian',
            'pertanyaan' => self::wrapStimulus(2, '6 - 10', 'Konsep Pertumbuhan Eksponen Bakteri', '', $story2, '5. ISIAN SINGKAT', 'Berapakah hasil dari perpangkatan <u>2⁵</u>? (Tuliskan hasil akhir berupa angka saja)'),
            'kunci_jawaban' => '32',
            'konten_tambahan' => ['rubrik' => '32']
        ];

        // ══════════════════════════════════════════════════════════════════
        // STIMULUS 3 s/d 10 (Melengkapi total 50 Butir Soal Kelas X)
        // ══════════════════════════════════════════════════════════════════
        $stimulusMetaX = [
            [
                'no' => 3, 'range' => '11 - 15', 'judul' => 'SPLDV: Tiket Masuk Taman Edukasi Madrasah',
                'svg' => '<svg width="240" height="70" viewBox="0 0 240 70"><rect x="10" y="10" width="100" height="50" rx="6" fill="#dcfce7" stroke="#16a34a" stroke-width="1.5"/><text x="60" y="32" font-size="11" font-weight="bold" fill="#15803d" text-anchor="middle">Dewasa (x)</text><text x="60" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Rp 15.000</text><rect x="130" y="10" width="100" height="50" rx="6" fill="#fef9c3" stroke="#ca8a04" stroke-width="1.5"/><text x="180" y="32" font-size="11" font-weight="bold" fill="#854d0e" text-anchor="middle">Anak-Anak (y)</text><text x="180" y="48" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Rp 10.000</text></svg>',
                'cerita' => 'Pak Ahmad membeli <u>2 tiket dewasa (x)</u> dan <u>3 tiket anak-anak (y)</u> dengan total harga <strong>Rp 60.000</strong>. Di loket lain, Bu Siti membeli <u>1 tiket dewasa (x)</u> dan <u>1 tiket anak-anak (y)</u> dengan total harga <strong>Rp 25.000</strong>.',
                'q_pg' => 'Berdasarkan sistem persamaan di atas, berapakah harga <u>1 tiket dewasa (x)</u>?',
                'pg_ops' => ['Rp 15.000', 'Rp 10.000', 'Rp 12.000', 'Rp 18.000', 'Rp 20.000'], 'pg_k' => 'A',
                'q_pgk' => 'Pernyataan mana saja yang tepat terkait sistem persamaan tiket tersebut?',
                'pgk_ops' => [
                    ['teks' => 'Model matematikanya adalah 2x + 3y = 60.000 dan x + y = 25.000', 'is_kunci' => true],
                    ['teks' => 'Harga 1 tiket anak-anak (y) adalah Rp 10.000', 'is_kunci' => true],
                    ['teks' => 'Harga tiket dewasa lebih murah daripada tiket anak-anak', 'is_kunci' => false],
                    ['teks' => 'Membeli 2 tiket dewasa membutuhkan uang Rp 30.000', 'is_kunci' => true]
                ], 'pgk_k' => 'A, B, D',
                'bs_ops' => [
                    ['teks' => 'Sistem persamaan di atas memiliki tepat satu penyelesaian.', 'kunci' => 'Benar'],
                    ['teks' => 'Metode eliminasi dan substitusi dapat digunakan mencari harga tiket.', 'kunci' => 'Benar'],
                    ['teks' => 'Harga total 1 dewasa dan 2 anak-anak adalah Rp 45.000.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Tiket Dewasa (x)', 'respon' => 'Rp 15.000'],
                    ['premis' => 'Tiket Anak (y)', 'respon' => 'Rp 10.000'],
                    ['premis' => 'Total 1 Dewasa + 1 Anak', 'respon' => 'Rp 25.000'],
                    ['premis' => 'Selisih harga tiket', 'respon' => 'Rp 5.000']
                ],
                'q_isian' => 'Berapakah harga <u>1 tiket anak-anak (y)</u> dalam nominal angka?', 'isian_k' => '10000'
            ],
            [
                'no' => 4, 'range' => '16 - 20', 'judul' => 'Barisan Aritmatika: Formasi Kursi Upacara',
                'svg' => '<svg width="240" height="70" viewBox="0 0 240 70"><text x="30" y="25" font-size="11" font-weight="bold" fill="#0284c7">Baris 1: 10 Kursi</text><text x="30" y="45" font-size="11" font-weight="bold" fill="#0284c7">Baris 2: 14 Kursi (+4)</text><text x="30" y="65" font-size="11" font-weight="bold" fill="#0284c7">Baris 3: 18 Kursi (+4)</text></svg>',
                'cerita' => 'Dalam gedung pertemuan madrasah, penataan kursi upacara mengikuti pola <u>barisan aritmatika</u>. Baris pertama (a) berisi <strong>10 kursi</strong>, baris kedua berisi <strong>14 kursi</strong>, dan baris ketiga berisi <strong>18 kursi</strong>. <u>Beda penambahan kursi (b)</u> bernilai tetap.',
                'q_pg' => 'Berapakah <u>jumlah kursi pada baris ke-5</u>?',
                'pg_ops' => ['22 kursi', '24 kursi', '26 kursi', '28 kursi', '30 kursi'], 'pg_k' => 'C',
                'q_pgk' => 'Manakah sifat barisan aritmatika berikut yang <strong>BENAR</strong>?',
                'pgk_ops' => [
                    ['teks' => 'Suku pertama (a) adalah 10', 'is_kunci' => true],
                    ['teks' => 'Beda antar baris (b) adalah 4', 'is_kunci' => true],
                    ['teks' => 'Rumus suku ke-n adalah Un = 10 + (n-1) × 4', 'is_kunci' => true],
                    ['teks' => 'Baris ke-4 berjumlah 20 kursi', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Barisan kursi di atas adalah barisan aritmatika bertambah (naik).', 'kunci' => 'Benar'],
                    ['teks' => 'Baris ke-4 berisi 22 kursi.', 'kunci' => 'Benar'],
                    ['teks' => 'Beda barisan kursi bernilai negatif.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Baris ke-1 (a)', 'respon' => '10 kursi'],
                    ['premis' => 'Beda baris (b)', 'respon' => '4 kursi'],
                    ['premis' => 'Baris ke-2 (U2)', 'respon' => '14 kursi'],
                    ['premis' => 'Baris ke-3 (U3)', 'respon' => '18 kursi']
                ],
                'q_isian' => 'Berapakah <u>beda penambahan kursi (b)</u> pada setiap baris?', 'isian_k' => '4'
            ],
            [
                'no' => 5, 'range' => '21 - 25', 'judul' => 'Fungsi Kuadrat: Lintasan Lemparan Bola',
                'svg' => '<svg width="240" height="70" viewBox="0 0 240 70"><path d="M 20 60 Q 120 10 220 60" fill="none" stroke="#6366f1" stroke-width="2.5"/><circle cx="120" cy="35" r="5" fill="#ef4444"/><text x="120" y="25" font-size="10" font-weight="bold" fill="#4338ca" text-anchor="middle">Puncak Max</text></svg>',
                'cerita' => 'Tinggi lemparan bola vertikal dinyatakan dengan fungsi kuadrat <strong>h(t) = -t² + 4t</strong>, dengan <em>t</em> dalam detik dan <em>h</em> dalam meter. <u>Titik puncak parabola</u> menunjukkan tinggi maksimum yang dicapai bola.',
                'q_pg' => 'Pada detik ke berapa bola mencapai <u>ketinggian maksimum</u>?',
                'pg_ops' => ['1 detik', '2 detik', '3 detik', '4 detik', '5 detik'], 'pg_k' => 'B',
                'q_pgk' => 'Karakteristik fungsi kuadrat h(t) = -t² + 4t yang benar adalah:',
                'pgk_ops' => [
                    ['teks' => 'Kurva parabola terbuka ke bawah karena koefisien a bernilai negatif', 'is_kunci' => true],
                    ['teks' => 'Memiliki nilai maksimum pada titik puncaknya', 'is_kunci' => true],
                    ['teks' => 'Tinggi maksimum yang dicapai bola adalah 4 meter', 'is_kunci' => true],
                    ['teks' => 'Bola kembali ke tanah pada detik ke-10', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Pada t = 0 (awal), tinggi bola adalah 0 meter.', 'kunci' => 'Benar'],
                    ['teks' => 'Tinggi bola pada detik ke-2 adalah 4 meter.', 'kunci' => 'Benar'],
                    ['teks' => 'Grafik fungsi kuadrat di atas berbentuk garis lurus linear.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Waktu puncak (t)', 'respon' => '2 detik'],
                    ['premis' => 'Tinggi max (h)', 'respon' => '4 meter'],
                    ['premis' => 'Tinggi saat t = 1', 'respon' => '3 meter'],
                    ['premis' => 'Tinggi saat t = 4', 'respon' => '0 meter']
                ],
                'q_isian' => 'Berapakah <u>tinggi maksimum (dalam meter)</u> yang dapat dicapai bola?', 'isian_k' => '4'
            ],
            [
                'no' => 6, 'range' => '26 - 30', 'judul' => 'Teorema Pythagoras: Menara dan Tali Pengaman',
                'svg' => '<svg width="220" height="70" viewBox="0 0 220 70"><polygon points="30,60 150,60 150,15" fill="#f1f5f9" stroke="#0f172a" stroke-width="2"/><text x="90" y="55" font-size="10" font-weight="bold">Alas: 4 m</text><text x="155" y="40" font-size="10" font-weight="bold">Tinggi: 3 m</text><text x="75" y="30" font-size="10" font-weight="bold" fill="#dc2626">Miring: 5 m</text></svg>',
                'cerita' => 'Sebuah tiang bendera madrasah setinggi <strong>3 meter</strong> dipasangi tali pengaman dari puncak tiang ke pasak tanah yang berjarak <strong>4 meter</strong> dari pangkal tiang. Hubungan sisi segitiga siku-siku ini memenuhi <u>teorema Pythagoras</u>: <strong>a² + b² = c²</strong>.',
                'q_pg' => 'Berapakah <u>panjang tali pengaman (sisi miring c)</u> yang dibutuhkan?',
                'pg_ops' => ['5 meter', '6 meter', '7 meter', '8 meter', '9 meter'], 'pg_k' => 'A',
                'q_pgk' => 'Kelompok bilangan berikut yang merupakan <u>tripel Pythagoras</u> adalah:',
                'pgk_ops' => [
                    ['teks' => '3, 4, 5 (karena 3² + 4² = 5²)', 'is_kunci' => true],
                    ['teks' => '6, 8, 10 (kelipatan dari 3, 4, 5)', 'is_kunci' => true],
                    ['teks' => '5, 12, 13 (karena 25 + 144 = 169)', 'is_kunci' => true],
                    ['teks' => '2, 3, 4 (karena 4 + 9 = 16)', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Sisi terpanjang pada segitiga siku-siku disebut hipotenusa.', 'kunci' => 'Benar'],
                    ['teks' => 'Teorema Pythagoras hanya berlaku untuk segitiga siku-siku.', 'kunci' => 'Benar'],
                    ['teks' => 'Panjang sisi miring selalu lebih pendek dari sisi tegaknya.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Alas segitiga (a)', 'respon' => '4 meter'],
                    ['premis' => 'Tinggi tiang (b)', 'respon' => '3 meter'],
                    ['premis' => 'Hipotenusa (c)', 'respon' => '5 meter'],
                    ['premis' => 'Nilai 3² + 4²', 'respon' => '25']
                ],
                'q_isian' => 'Berapakah <u>panjang hipotenusa</u> segitiga siku-siku jika alas 3 m dan tinggi 4 m?', 'isian_k' => '5'
            ],
            [
                'no' => 7, 'range' => '31 - 35', 'judul' => 'Trigonometri: Sudut Elevasi Mengamati Menara',
                'svg' => '<svg width="220" height="70" viewBox="0 0 220 70"><polygon points="20,60 160,60 160,20" fill="#f8fafc" stroke="#2563eb" stroke-width="2"/><text x="40" y="55" font-size="10" fill="#2563eb">Sudut 30°</text></svg>',
                'cerita' => 'Seorang siswa mengamati puncak menara madrasah dengan <u>sudut elevasi 30°</u>. Dalam trigonometri segitiga siku-siku, nilai <u>sinus sudut</u> didefinisikan sebagai perbandingan sisi depan terhadap sisi miring.',
                'q_pg' => 'Berapakah nilai perbandingan trigonometri dari <u>sin 30°</u>?',
                'pg_ops' => ['1/2', '1/3', '1/4', '√2 / 2', '√3 / 2'], 'pg_k' => 'A',
                'q_pgk' => 'Manakah nilai perbandingan sudut istimewa berikut yang <strong>BENAR</strong>?',
                'pgk_ops' => [
                    ['teks' => 'sin 30° = 1/2', 'is_kunci' => true],
                    ['teks' => 'cos 60° = 1/2', 'is_kunci' => true],
                    ['teks' => 'tan 45° = 1', 'is_kunci' => true],
                    ['teks' => 'sin 90° = 0', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai tan 45° adalah sama dengan 1.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai sin 30° sama besarnya dengan cos 60°.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai sinus selalu bernilai negatif pada Kuadran I.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'sin 30°', 'respon' => '1/2'],
                    ['premis' => 'tan 45°', 'respon' => '1'],
                    ['premis' => 'cos 0°', 'respon' => '1'],
                    ['premis' => 'sin 90°', 'respon' => '1']
                ],
                'q_isian' => 'Berapakah nilai dari <u>tan 45°</u>?', 'isian_k' => '1'
            ],
            [
                'no' => 8, 'range' => '36 - 40', 'judul' => 'Statistika: Nilai Rata-Rata Asesmen Matematika',
                'svg' => '<svg width="240" height="70" viewBox="0 0 240 70"><rect x="20" y="30" width="30" height="30" fill="#38bdf8"/><rect x="60" y="20" width="30" height="40" fill="#38bdf8"/><rect x="100" y="10" width="30" height="50" fill="#0284c7"/><rect x="140" y="25" width="30" height="35" fill="#38bdf8"/><rect x="180" y="35" width="30" height="25" fill="#38bdf8"/></svg>',
                'cerita' => 'Sebanyak 5 siswa memperoleh nilai harian matematika sebagai berikut: <strong>70, 75, 80, 85, 90</strong>. Nilai <u>rata-rata (mean)</u> dihitung dengan menjumlahkan seluruh nilai kemudian dibagi banyaknya siswa.',
                'q_pg' => 'Berapakah <u>nilai rata-rata (mean)</u> dari kelima siswa tersebut?',
                'pg_ops' => ['75', '78', '80', '82', '85'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan ukuran pemusatan data yang tepat adalah:',
                'pgk_ops' => [
                    ['teks' => 'Nilai rata-rata (mean) data tersebut adalah 80', 'is_kunci' => true],
                    ['teks' => 'Nilai tengah (median) data tersebut adalah 80', 'is_kunci' => true],
                    ['teks' => 'Jangkauan data (skor terbesar dikurang terkecil) adalah 20', 'is_kunci' => true],
                    ['teks' => 'Nilai rata-rata lebih kecil dari 70', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai median adalah nilai yang berada di posisi tengah data terurut.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai maksimum pada data tersebut adalah 90.', 'kunci' => 'Benar'],
                    ['teks' => 'Rata-rata data tersebut bernilai 100.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Nilai Minimum', 'respon' => '70'],
                    ['premis' => 'Nilai Rata-rata', 'respon' => '80'],
                    ['premis' => 'Nilai Maksimum', 'respon' => '90'],
                    ['premis' => 'Median Data', 'respon' => '80']
                ],
                'q_isian' => 'Berapakah <u>nilai median</u> dari data 70, 75, 80, 85, 90?', 'isian_k' => '80'
            ],
            [
                'no' => 9, 'range' => '41 - 45', 'judul' => 'Peluang: Pengambilan Bola Warna dalam Kotak',
                'svg' => '<svg width="220" height="70" viewBox="0 0 220 70"><circle cx="40" cy="35" r="14" fill="#ef4444"/><circle cx="75" cy="35" r="14" fill="#ef4444"/><circle cx="110" cy="35" r="14" fill="#ef4444"/><circle cx="145" cy="35" r="14" fill="#3b82f6"/><circle cx="180" cy="35" r="14" fill="#3b82f6"/></svg>',
                'cerita' => 'Dalam sebuah kotak terdapat <strong>3 bola merah</strong> dan <strong>2 bola biru</strong>. Total bola dalam kotak adalah <strong>5 bola</strong>. Sebuah bola diambil secara acak. <u>Peluang kejadian P(A) = n(A) / n(S)</u>.',
                'q_pg' => 'Berapakah <u>peluang terambilnya 1 bola merah</u>?',
                'pg_ops' => ['1/5', '2/5', '3/5', '4/5', '1'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan tentang peluang bola berikut yang bernilai <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Peluang terambil bola merah adalah 3/5', 'is_kunci' => true],
                    ['teks' => 'Peluang terambil bola biru adalah 2/5', 'is_kunci' => true],
                    ['teks' => 'Jumlah peluang seluruh kejadian ruang sampel sama dengan 1', 'is_kunci' => true],
                    ['teks' => 'Peluang terambil bola kuning adalah 1/5', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai peluang suatu kejadian selalu berada di antara 0 dan 1.', 'kunci' => 'Benar'],
                    ['teks' => 'Peluang terambil bola biru adalah 2/5 (atau 0,4).', 'kunci' => 'Benar'],
                    ['teks' => 'Peluang terambil bola merah lebih kecil dari bola biru.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Banyak bola merah', 'respon' => '3 bola'],
                    ['premis' => 'Banyak bola biru', 'respon' => '2 bola'],
                    ['premis' => 'Total bola (ruang sampel)', 'respon' => '5 bola'],
                    ['premis' => 'Peluang bola merah', 'respon' => '3/5']
                ],
                'q_isian' => 'Berapakah <u>total seluruh bola</u> yang berada di dalam kotak?', 'isian_k' => '5'
            ],
            [
                'no' => 10, 'range' => '46 - 50', 'judul' => 'Logaritma: Hubungan Invers dengan Eksponen',
                'svg' => '<svg width="240" height="70" viewBox="0 0 240 70"><text x="120" y="32" font-size="13" font-weight="bold" fill="#0369a1" text-anchor="middle">2³ = 8 ⟺ ²log 8 = 3</text><text x="120" y="52" font-size="11" fill="#475569" text-anchor="middle">Basis 2, Numerus 8, Hasil 3</text></svg>',
                'cerita' => 'Operasi <u>logaritma</u> merupakan kebalikan (invers) dari perpangkatan. Jika <strong>bᶜ = a</strong>, maka bentuk logaritmanya adalah <strong>ᵇlog a = c</strong>, di mana <em>b</em> adalah basis dan <em>a</em> adalah <u>numerus logaritma</u>.',
                'q_pg' => 'Berapakah nilai dari <u>²log 8</u>?',
                'pg_ops' => ['1', '2', '3', '4', '8'], 'pg_k' => 'C',
                'q_pgk' => 'Sifat dasar logaritma yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'ᵃlog a = 1 (untuk a > 0 dan a ≠ 1)', 'is_kunci' => true],
                    ['teks' => 'ᵃlog 1 = 0', 'is_kunci' => true],
                    ['teks' => '²log 4 = 2', 'is_kunci' => true],
                    ['teks' => '²log 2 = 0', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai dari ¹⁰log 100 adalah 2 karena 10² = 100.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai dari ᵃlog 1 selalu sama dengan 0.', 'kunci' => 'Benar'],
                    ['teks' => 'Operasi logaritma tidak memiliki hubungan dengan eksponen.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => '²log 4', 'respon' => '2'],
                    ['premis' => '²log 8', 'respon' => '3'],
                    ['premis' => '³log 9', 'respon' => '2'],
                    ['premis' => '¹⁰log 10', 'respon' => '1']
                ],
                'q_isian' => 'Berapakah nilai dari <u>²log 4</u>?', 'isian_k' => '2'
            ]
        ];

        foreach ($stimulusMetaX as $meta) {
            $baseNo = ($meta['no'] - 1) * 5 + 1;
            $sNo = $meta['no'];
            $range = $meta['range'];
            $judul = $meta['judul'];
            $svg = $meta['svg'];
            $cerita = $meta['cerita'];

            // 1. PG
            $list[] = [
                'nomor_urut' => $baseNo,
                'tipe_soal' => 'sec_pg',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, $svg, $cerita, '1. PILIHAN GANDA', $meta['q_pg']),
                'opsi_a' => $meta['pg_ops'][0], 'opsi_b' => $meta['pg_ops'][1], 'opsi_c' => $meta['pg_ops'][2], 'opsi_d' => $meta['pg_ops'][3], 'opsi_e' => $meta['pg_ops'][4],
                'kunci_jawaban' => $meta['pg_k'],
                'konten_tambahan' => ['kunci' => $meta['pg_k'], 'opsi' => ['A' => $meta['pg_ops'][0], 'B' => $meta['pg_ops'][1], 'C' => $meta['pg_ops'][2], 'D' => $meta['pg_ops'][3], 'E' => $meta['pg_ops'][4]]]
            ];

            // 2. PGK
            $list[] = [
                'nomor_urut' => $baseNo + 1,
                'tipe_soal' => 'sec_pgk',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '2. PG KOMPLEKS (CENTANG)', $meta['q_pgk']),
                'opsi_a' => $meta['pgk_ops'][0]['teks'], 'opsi_b' => $meta['pgk_ops'][1]['teks'], 'opsi_c' => $meta['pgk_ops'][2]['teks'], 'opsi_d' => $meta['pgk_ops'][3]['teks'] ?? '', 'opsi_e' => '',
                'kunci_jawaban' => $meta['pgk_k'],
                'konten_tambahan' => ['opsi' => $meta['pgk_ops'], 'kunci_array' => explode(', ', $meta['pgk_k'])]
            ];

            // 3. B/S
            $bsKeys = array_map(function($x){ return $x['kunci']; }, $meta['bs_ops']);
            $list[] = [
                'nomor_urut' => $baseNo + 2,
                'tipe_soal' => 'sec_bs',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '3. TABEL BENAR / SALAH', 'Tentukan kebenaran dari masing-masing pernyataan berikut:'),
                'kunci_jawaban' => json_encode($bsKeys),
                'konten_tambahan' => ['butir' => $meta['bs_ops']]
            ];

            // 4. Jodoh
            $list[] = [
                'nomor_urut' => $baseNo + 3,
                'tipe_soal' => 'sec_jodoh',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '4. MENJODOHKAN', 'Pasangkan pernyataan pada kolom kiri dengan jawaban yang sesuai pada kolom kanan:'),
                'kunci_jawaban' => json_encode($meta['jodoh_ops']),
                'konten_tambahan' => ['pasangan' => $meta['jodoh_ops']]
            ];

            // 5. Isian
            $list[] = [
                'nomor_urut' => $baseNo + 4,
                'tipe_soal' => 'sec_isian',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '5. ISIAN SINGKAT', $meta['q_isian']),
                'kunci_jawaban' => $meta['isian_k'],
                'konten_tambahan' => ['rubrik' => $meta['isian_k']]
            ];
        }

        return $list;
    }

    /**
     * KELAS XI DATASET (50 Butir Soal = 10 Stimulus × 5 Model Soal)
     */
    private static function getDataKelasXI() {
        $metaXI = [
            [
                'no' => 1, 'range' => '1 - 5', 'judul' => 'Operasi Matriks: Penjualan Dua Cabang Toko Buku',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="50" y="35" font-size="12" font-weight="bold" fill="#0369a1">[ 2  4 ]</text><text x="110" y="35" font-size="12" font-weight="bold" fill="#64748b">+</text><text x="160" y="35" font-size="12" font-weight="bold" fill="#15803d">[ 3  1 ]</text></svg>',
                'cerita' => 'Toko buku madrasah mencatat persediaan barang dalam bentuk <u>matriks ordo 2x2</u>. Matriks A = [[2, 4], [1, 3]] dan Matriks B = [[3, 1], [2, 5]]. Penjumlahan dua matriks dilakukan dengan menjumlahkan elemen-elemen yang <u>seletak</u>.',
                'q_pg' => 'Berapakah elemen baris pertama kolom pertama dari hasil <u>penjumlahan matriks (A + B)</u>?',
                'pg_ops' => ['4', '5', '6', '7', '8'], 'pg_k' => 'B',
                'q_pgk' => 'Sifat operasi matriks berikut yang bernilai <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Penjumlahan matriks bersifat komutatif: A + B = B + A', 'is_kunci' => true],
                    ['teks' => 'Dua matriks dapat dijumlahkan jika memiliki ordo yang sama', 'is_kunci' => true],
                    ['teks' => 'Elemen baris 1 kolom 2 dari A + B adalah 4 + 1 = 5', 'is_kunci' => true],
                    ['teks' => 'Matriks A dan B memiliki ordo yang berbeda', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Ordo matriks A adalah 2 x 2 (2 baris dan 2 kolom).', 'kunci' => 'Benar'],
                    ['teks' => 'Elemen seletak pada matriks dijumlahkan secara langsung.', 'kunci' => 'Benar'],
                    ['teks' => 'Perkalian matriks selalu bersifat komutatif (A × B = B × A).', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Elemen (1,1) A + B', 'respon' => '5 (2+3)'],
                    ['premis' => 'Elemen (1,2) A + B', 'respon' => '5 (4+1)'],
                    ['premis' => 'Elemen (2,1) A + B', 'respon' => '3 (1+2)'],
                    ['premis' => 'Elemen (2,2) A + B', 'respon' => '8 (3+5)']
                ],
                'q_isian' => 'Berapakah nilai elemen baris 1 kolom 1 dari A + B (2 + 3)?', 'isian_k' => '5'
            ],
            [
                'no' => 2, 'range' => '6 - 10', 'judul' => 'Determinan Matriks Ordo 2x2',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="12" font-weight="bold" fill="#4338ca" text-anchor="middle">det(A) = (a × d) - (b × c)</text></svg>',
                'cerita' => 'Diberikan matriks A = [[4, 2], [1, 3]]. Nilai <u>determinan matriks ordo 2x2</u> dihitung dengan rumus: <strong>det(A) = ad - bc</strong>. Determinan digunakan untuk menentukan apakah matriks memiliki invers atau tidak.',
                'q_pg' => 'Berapakah nilai <u>determinan dari matriks A</u> di atas?',
                'pg_ops' => ['8', '10', '12', '14', '16'], 'pg_k' => 'B',
                'q_pgk' => 'Pernyataan yang tepat tentang determinan matriks adalah:',
                'pgk_ops' => [
                    ['teks' => 'det(A) = (4 × 3) - (2 × 1) = 12 - 2 = 10', 'is_kunci' => true],
                    ['teks' => 'Jika det(A) ≠ 0, maka matriks A memiliki invers', 'is_kunci' => true],
                    ['teks' => 'Matriks dengan determinan 0 disebut matriks singular', 'is_kunci' => true],
                    ['teks' => 'Nilai determinan di atas bernilai negatif', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai determinan matriks A adalah 10.', 'kunci' => 'Benar'],
                    ['teks' => 'Matriks A bukan matriks singular karena determinannya tidak nol.', 'kunci' => 'Benar'],
                    ['teks' => 'Rumus determinan ordo 2x2 adalah ad + bc.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Perkalian diagonal utama (ad)', 'respon' => '12 (4×3)'],
                    ['premis' => 'Perkalian diagonal samping (bc)', 'respon' => '2 (2×1)'],
                    ['premis' => 'Nilai Determinan', 'respon' => '10 (12-2)'],
                    ['premis' => 'Status Invers', 'respon' => 'Memiliki Invers']
                ],
                'q_isian' => 'Berapakah <u>nilai determinan</u> dari matriks [[4, 2], [1, 3]]?', 'isian_k' => '10'
            ],
            [
                'no' => 3, 'range' => '11 - 15', 'judul' => 'Deret Geometri: Pembelahan Sel Terpadu',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">U1 = 3,  U2 = 6,  U3 = 12 (Rasio r = 2)</text></svg>',
                'cerita' => 'Suatu kultur sel berkembang biak menurut <u>barisan geometri</u>. Suku pertama (a) = <strong>3</strong>, suku kedua = <strong>6</strong>, dan suku ketiga = <strong>12</strong>. <u>Rasio perbandingan (r)</u> bernilai tetap.',
                'q_pg' => 'Berapakah <u>suku ke-4 (U4)</u> dari barisan geometri tersebut?',
                'pg_ops' => ['18', '20', '24', '28', '32'], 'pg_k' => 'C',
                'q_pgk' => 'Karakteristik barisan geometri di atas yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Suku pertama (a) adalah 3', 'is_kunci' => true],
                    ['teks' => 'Rasio barisan (r) adalah 6 / 3 = 2', 'is_kunci' => true],
                    ['teks' => 'Rumus suku ke-n adalah Un = a × r^(n-1)', 'is_kunci' => true],
                    ['teks' => 'Barisan tersebut merupakan barisan aritmatika', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Rasio barisan geometri tersebut adalah 2.', 'kunci' => 'Benar'],
                    ['teks' => 'Suku ke-4 adalah 24.', 'kunci' => 'Benar'],
                    ['teks' => 'Suku ke-5 adalah 100.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Suku pertama (U1)', 'respon' => '3'],
                    ['premis' => 'Rasio (r)', 'respon' => '2'],
                    ['premis' => 'Suku ke-2 (U2)', 'respon' => '6'],
                    ['premis' => 'Suku ke-3 (U3)', 'respon' => '12']
                ],
                'q_isian' => 'Berapakah nilai <u>rasio (r)</u> dari barisan geometri 3, 6, 12, ...?', 'isian_k' => '2'
            ],
            [
                'no' => 4, 'range' => '16 - 20', 'judul' => 'Persamaan Lingkaran Berpusat di Titik Asal (0,0)',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><circle cx="110" cy="35" r="25" fill="#f0fdf4" stroke="#16a34a" stroke-width="2"/><text x="110" y="38" font-size="10" font-weight="bold" fill="#15803d" text-anchor="middle">r = 5</text></svg>',
                'cerita' => 'Sebuah lingkaran yang berpusat di titik <strong>(0,0)</strong> memiliki jari-jari <strong>r = 5</strong>. Bentuk umum persamaan lingkaran berpusat di (0,0) adalah: <strong>x² + y² = r²</strong>.',
                'q_pg' => 'Manakah <u>persamaan lingkaran</u> yang berpusat di (0,0) dengan jari-jari r = 5?',
                'pg_ops' => ['x² + y² = 5', 'x² + y² = 10', 'x² + y² = 25', 'x² - y² = 25', 'x + y = 25'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan yang tepat tentang lingkaran x² + y² = 25 adalah:',
                'pgk_ops' => [
                    ['teks' => 'Pusat lingkaran berada pada koordinat (0, 0)', 'is_kunci' => true],
                    ['teks' => 'Panjang jari-jari lingkaran adalah 5 satuan', 'is_kunci' => true],
                    ['teks' => 'Titik (3, 4) terletak tepat pada lingkaran (karena 3² + 4² = 25)', 'is_kunci' => true],
                    ['teks' => 'Titik (0, 0) terletak pada garis lingkaran', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Diameter lingkaran tersebut adalah 10 satuan (2 × r).', 'kunci' => 'Benar'],
                    ['teks' => 'Titik (5, 0) dilalui oleh lingkaran.', 'kunci' => 'Benar'],
                    ['teks' => 'Persamaan lingkaran selalu berbentuk linear berderajat 1.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Pusat Lingkaran', 'respon' => '(0, 0)'],
                    ['premis' => 'Jari-jari (r)', 'respon' => '5'],
                    ['premis' => 'Diameter (2r)', 'respon' => '10'],
                    ['premis' => 'Persamaan Lingkaran', 'respon' => 'x² + y² = 25']
                ],
                'q_isian' => 'Berapakah <u>panjang jari-jari (r)</u> dari lingkaran x² + y² = 25?', 'isian_k' => '5'
            ],
            [
                'no' => 5, 'range' => '21 - 25', 'judul' => 'Transformasi Geometri: Translasi (Pergeseran Titik)',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">A(2, 3) + T(3, 2) ➜ A\'(5, 5)</text></svg>',
                'cerita' => 'Sebuah titik A(2, 3) <u>ditranslasikan (digeser)</u> oleh vektor translasi T = (3, 2). Koordinat bayangan A\'(x\', y\') diperoleh dengan menjumlahkan: <strong>x\' = x + a</strong> dan <strong>y\' = y + b</strong>.',
                'q_pg' => 'Berapakah koordinat <u>bayangan titik A\'</u> hasil translasi tersebut?',
                'pg_ops' => ['(5, 5)', '(6, 5)', '(5, 6)', '(6, 6)', '(4, 4)'], 'pg_k' => 'A',
                'q_pgk' => 'Sifat translasi (pergeseran) berikut yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Translasi tidak mengubah bentuk dan ukuran bangun objek', 'is_kunci' => true],
                    ['teks' => 'Hanya mengubah posisi koordinat objek pada bidang datar', 'is_kunci' => true],
                    ['teks' => 'Koordinat bayangan x\' = 2 + 3 = 5', 'is_kunci' => true],
                    ['teks' => 'Translasi memutar arah bangun sebesar 90 derajat', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Bayangan titik A(2, 3) oleh T(3, 2) adalah (5, 5).', 'kunci' => 'Benar'],
                    ['teks' => 'Translasi mempertahankan orientasi dan ukuran bangun.', 'kunci' => 'Benar'],
                    ['teks' => 'Vektor translasi selalu bernilai 0.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Titik Awal (x, y)', 'respon' => '(2, 3)'],
                    ['premis' => 'Vektor Translasi T', 'respon' => '(3, 2)'],
                    ['premis' => 'Absis Bayangan x\'', 'respon' => '5 (2+3)'],
                    ['premis' => 'Ordinat Bayangan y\'', 'respon' => '5 (3+2)']
                ],
                'q_isian' => 'Berapakah nilai <u>absis (koordinat x)</u> bayangan dari titik A(2, 3) oleh T(3, 2)?', 'isian_k' => '5'
            ],
            [
                'no' => 6, 'range' => '26 - 30', 'judul' => 'Turunan Fungsi Aljabar (Kalkulus Dasar)',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="12" font-weight="bold" fill="#0f172a" text-anchor="middle">f(x) = x² + 3x ➜ f\'(x) = 2x + 3</text></svg>',
                'cerita' => 'Diberikan fungsi aljabar <strong>f(x) = x² + 3x</strong>. Rumus dasar turunan fungsi adalah: jika f(x) = axⁿ, maka <u>turunan pertamanya f\'(x) = n × axⁿ⁻¹</u>.',
                'q_pg' => 'Berapakah <u>turunan pertama f\'(x)</u> dari fungsi f(x) = x² + 3x?',
                'pg_ops' => ['2x + 3', '2x + 1', 'x + 3', '3x + 2', '2x² + 3'], 'pg_k' => 'A',
                'q_pgk' => 'Pernyataan tentang turunan fungsi f(x) = x² + 3x yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Turunan dari x² adalah 2x', 'is_kunci' => true],
                    ['teks' => 'Turunan dari 3x adalah 3', 'is_kunci' => true],
                    ['teks' => 'Nilai f\'(1) = 2(1) + 3 = 5', 'is_kunci' => true],
                    ['teks' => 'Turunan dari konstanta bernilai 1', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Turunan pertama f\'(x) adalah 2x + 3.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai f\'(0) adalah 3.', 'kunci' => 'Benar'],
                    ['teks' => 'Turunan dari fungsi kuadrat menghasilkan fungsi derajat 3.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Turunan dari x²', 'respon' => '2x'],
                    ['premis' => 'Turunan dari 3x', 'respon' => '3'],
                    ['premis' => 'f\'(x) lengkap', 'respon' => '2x + 3'],
                    ['premis' => 'Nilai f\'(1)', 'respon' => '5']
                ],
                'q_isian' => 'Berapakah nilai <u>f\'(1)</u> dari f\'(x) = 2x + 3?', 'isian_k' => '5'
            ],
            [
                'no' => 7, 'range' => '31 - 35', 'judul' => 'Integral Aljabar Tak Tentu',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="12" font-weight="bold" fill="#0369a1" text-anchor="middle">∫ 2x dx = x² + C</text></svg>',
                'cerita' => 'Operasi <u>integral</u> merupakan antiturunan (kebalikan dari turunan). Rumus dasar integral tak tentu untuk fungsi f(x) = axⁿ adalah: <strong>∫ axⁿ dx = [a / (n+1)] xⁿ⁺¹ + C</strong>.',
                'q_pg' => 'Berapakah hasil dari <u>integral tak tentu ∫ 2x dx</u>?',
                'pg_ops' => ['x² + C', '2x² + C', 'x + C', '3x² + C', 'x³ + C'], 'pg_k' => 'A',
                'q_pgk' => 'Sifat integral tak tentu yang tepat adalah:',
                'pgk_ops' => [
                    ['teks' => '∫ 2x dx = x² + C', 'is_kunci' => true],
                    ['teks' => 'C melambangkan konstanta integrasi', 'is_kunci' => true],
                    ['teks' => 'Integral merupakan operasi kebalikan dari diferensial (turunan)', 'is_kunci' => true],
                    ['teks' => 'Hasil integral selalu bernilai 0', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Hasil antiturunan dari 2x adalah x² + C.', 'kunci' => 'Benar'],
                    ['teks' => 'Jika x² diturunkan, hasilnya kembali menjadi 2x.', 'kunci' => 'Benar'],
                    ['teks' => 'Konstanta C tidak perlu ditulis pada integral tentu berbatas.', 'kunci' => 'Benar']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Antiturunan 2x', 'respon' => 'x² + C'],
                    ['premis' => 'Antiturunan 3x²', 'respon' => 'x³ + C'],
                    ['premis' => 'Antiturunan 1 (dx)', 'respon' => 'x + C'],
                    ['premis' => 'Arti simbol C', 'respon' => 'Konstanta']
                ],
                'q_isian' => 'Apakah suku variabel dari hasil ∫ 2x dx? (Tuliskan bentuk x^2)', 'isian_k' => 'x^2'
            ],
            [
                'no' => 8, 'range' => '36 - 40', 'judul' => 'Program Linear: Daerah Bersih Penyelesaian',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">x + y ≤ 10,  x ≥ 0,  y ≥ 0</text></svg>',
                'cerita' => 'Sebuah masalah <u>program linear</u> memiliki sistem pertidaksamaan: <strong>x + y ≤ 10</strong>, dengan syarat non-negatif <strong>x ≥ 0</strong> dan <strong>y ≥ 0</strong>.',
                'q_pg' => 'Apakah titik (3, 4) berada di dalam <u>daerah penyelesaian (DHP)</u> pertidaksamaan x + y ≤ 10?',
                'pg_ops' => ['Ya, karena 3 + 4 = 7 ≤ 10', 'Tidak, karena 7 > 10', 'Tepat pada garis batas', 'Di luar kuadran I', 'Tidak dapat ditentukan'], 'pg_k' => 'A',
                'q_pgk' => 'Titik-titik uji berikut yang memenuhi pertidaksamaan x + y ≤ 10 adalah:',
                'pgk_ops' => [
                    ['teks' => 'Titik (2, 3) karena 2 + 3 = 5 ≤ 10', 'is_kunci' => true],
                    ['teks' => 'Titik (5, 5) karena 5 + 5 = 10 ≤ 10', 'is_kunci' => true],
                    ['teks' => 'Titik (0, 0) karena 0 + 0 = 0 ≤ 10', 'is_kunci' => true],
                    ['teks' => 'Titik (8, 5) karena 8 + 5 = 13 ≤ 10', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Titik (0, 0) berada di dalam daerah himpunan penyelesaian.', 'kunci' => 'Benar'],
                    ['teks' => 'Garis batas x + y = 10 memotong sumbu X di titik (10, 0).', 'kunci' => 'Benar'],
                    ['teks' => 'Titik (10, 10) memenuhi pertidaksamaan x + y ≤ 10.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Titik potong sumbu X', 'respon' => '(10, 0)'],
                    ['premis' => 'Titik potong sumbu Y', 'respon' => '(0, 10)'],
                    ['premis' => 'Nilai x + y pada (5, 5)', 'respon' => '10'],
                    ['premis' => 'Nilai x + y pada (2, 3)', 'respon' => '5']
                ],
                'q_isian' => 'Berapakah nilai <u>x + y</u> pada titik uji (4, 6)?', 'isian_k' => '10'
            ],
            [
                'no' => 9, 'range' => '41 - 45', 'judul' => 'Vektor pada Bidang Datar (Dimensi Dua)',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#4338ca" text-anchor="middle">u = (3, 4) ➜ |u| = √(3² + 4²) = 5</text></svg>',
                'cerita' => 'Sebuah <u>vektor posisi</u> dinyatakan dengan <strong>u = (3, 4)</strong>. <u>Panjang vektor (modulus)</u> dihitung dengan rumus Pythagoras: <strong>|u| = √(x² + y²)</strong>.',
                'q_pg' => 'Berapakah <u>panjang (panjang modulus |u|)</u> dari vektor u = (3, 4)?',
                'pg_ops' => ['3 satuan', '4 satuan', '5 satuan', '7 satuan', '25 satuan'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan tentang vektor u = (3, 4) yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Komponen horizontal x adalah 3', 'is_kunci' => true],
                    ['teks' => 'Komponen vertikal y adalah 4', 'is_kunci' => true],
                    ['teks' => 'Panjang vektor |u| = √(9 + 16) = √25 = 5', 'is_kunci' => true],
                    ['teks' => 'Vektor memiliki arah dan besar nilai', 'is_kunci' => true]
                ], 'pgk_k' => 'A, B, C, D',
                'bs_ops' => [
                    ['teks' => 'Panjang vektor selalu bernilai positif atau nol.', 'kunci' => 'Benar'],
                    ['teks' => 'Vektor u = (3, 4) memiliki panjang 5 satuan.', 'kunci' => 'Benar'],
                    ['teks' => 'Vektor nol memiliki panjang 1 satuan.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Komponen x', 'respon' => '3'],
                    ['premis' => 'Komponen y', 'respon' => '4'],
                    ['premis' => 'Nilai x² + y²', 'respon' => '25 (9+16)'],
                    ['premis' => 'Panjang vektor |u|', 'respon' => '5']
                ],
                'q_isian' => 'Berapakah <u>panjang vektor |u|</u> untuk u = (3, 4)?', 'isian_k' => '5'
            ],
            [
                'no' => 10, 'range' => '46 - 50', 'judul' => 'Invers Matriks Ordo 2x2',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">A × A⁻¹ = I (Matriks Identitas)</text></svg>',
                'cerita' => 'Diberikan matriks A. Suatu matriks persegi A memiliki <u>matriks invers A⁻¹</u> jika dan hanya jika <u>determinan det(A) ≠ 0</u>. Perkalian matriks dengan inversnya menghasilkan <u>matriks identitas I = [[1, 0], [0, 1]]</u>.',
                'q_pg' => 'Apakah hasil dari perkalian matriks <u>A × A⁻¹</u>?',
                'pg_ops' => ['Matriks Identitas (I)', 'Matriks Nol (O)', 'Matriks Skalar', 'Matriks Diagonal', 'Matriks Transpose'], 'pg_k' => 'A',
                'q_pgk' => 'Sifat invers matriks berikut yang bernilai <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'A × A⁻¹ = I', 'is_kunci' => true],
                    ['teks' => 'A⁻¹ × A = I', 'is_kunci' => true],
                    ['teks' => 'Matriks singular tidak memiliki invers', 'is_kunci' => true],
                    ['teks' => 'Setiap matriks pasti memiliki invers', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Matriks identitas I memiliki diagonal utama bernilai 1.', 'kunci' => 'Benar'],
                    ['teks' => 'Jika det(A) = 0 maka inversnya tidak terdefinisi.', 'kunci' => 'Benar'],
                    ['teks' => 'Matriks bukan persegi memiliki matriks invers dua sisi.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Hasil A × A⁻¹', 'respon' => 'Matriks Identitas (I)'],
                    ['premis' => 'Syarat ada invers', 'respon' => 'det(A) ≠ 0'],
                    ['premis' => 'Diagonal Matriks I', 'respon' => '1, 1'],
                    ['premis' => 'Matriks tanpa invers', 'respon' => 'Matriks Singular']
                ],
                'q_isian' => 'Berapakah nilai determinan dari matriks identitas I = [[1,0],[0,1]]?', 'isian_k' => '1'
            ]
        ];

        return self::buildFromMeta($metaXI);
    }

    /**
     * KELAS XII DATASET (50 Butir Soal = 10 Stimulus × 5 Model Soal)
     */
    private static function getDataKelasXII() {
        $metaXII = [
            [
                'no' => 1, 'range' => '1 - 5', 'judul' => 'Dimensi Tiga: Panjang Diagonal Sisi dan Ruang Kubus',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><rect x="30" y="15" width="40" height="40" fill="#e0e7ff" stroke="#4338ca" stroke-width="1.5"/><rect x="50" y="5" width="40" height="40" fill="none" stroke="#4338ca" stroke-width="1.5"/><text x="140" y="38" font-size="11" font-weight="bold" fill="#312e81">Rusuk s = 6 cm</text></svg>',
                'cerita' => 'Sebuah kubus ABCD.EFGH memiliki <u>panjang rusuk s = 6 cm</u>. <u>Diagonal sisi</u> kubus dihitung dengan rumus <strong>d_s = s√2</strong>, sedangkan <u>diagonal ruang</u> kubus dihitung dengan rumus <strong>d_r = s√3</strong>.',
                'q_pg' => 'Berapakah <u>panjang diagonal ruang</u> kubus tersebut?',
                'pg_ops' => ['6√2 cm', '6√3 cm', '12 cm', '18 cm', '36 cm'], 'pg_k' => 'B',
                'q_pgk' => 'Pernyataan ukuran dimensi kubus dengan rusuk 6 cm yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Panjang diagonal sisi adalah 6√2 cm', 'is_kunci' => true],
                    ['teks' => 'Panjang diagonal ruang adalah 6√3 cm', 'is_kunci' => true],
                    ['teks' => 'Kubus memiliki 6 sisi berbentuk persegi yang kongruen', 'is_kunci' => true],
                    ['teks' => 'Volume kubus adalah 36 cm³', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Kubus memiliki 12 rusuk sama panjang.', 'kunci' => 'Benar'],
                    ['teks' => 'Panjang diagonal sisi adalah 6√2 cm.', 'kunci' => 'Benar'],
                    ['teks' => 'Diagonal ruang lebih pendek dari diagonal sisi.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Panjang rusuk (s)', 'respon' => '6 cm'],
                    ['premis' => 'Diagonal sisi (s√2)', 'respon' => '6√2 cm'],
                    ['premis' => 'Diagonal ruang (s√3)', 'respon' => '6√3 cm'],
                    ['premis' => 'Luas 1 sisi persegi', 'respon' => '36 cm²']
                ],
                'q_isian' => 'Berapakah <u>luas satu permukaan sisi kubus</u> (s²) dengan rusuk 6 cm?', 'isian_k' => '36'
            ],
            [
                'no' => 2, 'range' => '6 - 10', 'judul' => 'Statistika: Kuartil dan Median Data Nilai Ujian',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">Data: 60, 70, 80, 90, 100 ➜ Q2 = 80</text></svg>',
                'cerita' => 'Hasil asesmen 5 siswa madrasah tercatat sebagai berikut: <strong>60, 70, 80, 90, 100</strong>. Nilai <u>kuartil kedua (Q2)</u> membagi data terurut menjadi dua bagian yang sama besar dan identik dengan <u>median</u>.',
                'q_pg' => 'Berapakah nilai <u>Kuartil Tengah (Q2 atau Median)</u> dari data tersebut?',
                'pg_ops' => ['70', '75', '80', '85', '90'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan statistika data 60, 70, 80, 90, 100 yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Nilai median (Q2) adalah 80', 'is_kunci' => true],
                    ['teks' => 'Nilai rata-rata (mean) adalah (60+70+80+90+100)/5 = 80', 'is_kunci' => true],
                    ['teks' => 'Jangkauan data adalah 100 - 60 = 40', 'is_kunci' => true],
                    ['teks' => 'Data tersebut memiliki 3 modus', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai kuartil kedua (Q2) selalu sama dengan nilai median.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai mean (rata-rata) data tersebut adalah 80.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai terendah data tersebut adalah 80.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Nilai Terendah (Min)', 'respon' => '60'],
                    ['premis' => 'Nilai Tertinggi (Max)', 'respon' => '100'],
                    ['premis' => 'Median / Kuartil Q2', 'respon' => '80'],
                    ['premis' => 'Jangkauan (Range)', 'respon' => '40']
                ],
                'q_isian' => 'Berapakah <u>jangkauan (range)</u> data dari 100 - 60?', 'isian_k' => '40'
            ],
            [
                'no' => 3, 'range' => '11 - 15', 'judul' => 'Kaidah Pencacahan: Aturan Perkalian Memilih Pakaian',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0369a1" text-anchor="middle">3 Baju × 2 Celana = 6 Pasang</text></svg>',
                'cerita' => 'Seorang santri memiliki <strong>3 kemeja baju</strong> (Putih, Biru, Hijau) dan <strong>2 celana bahan</strong> (Hitam, Abu-abu). Menurut <u>aturan perkalian kaidah pencacahan</u>, banyaknya pasangan pakaian yang dapat dibentuk adalah: <strong>n = 3 × 2</strong>.',
                'q_pg' => 'Berapakah <u>banyaknya variasi pasangan kemeja dan celana</u> yang dapat dipakai?',
                'pg_ops' => ['4 pasang', '5 pasang', '6 pasang', '7 pasang', '8 pasang'], 'pg_k' => 'C',
                'q_pgk' => 'Pernyataan tentang kaidah perkalian berikut yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Total variasi dihitung dengan mengalikan: 3 × 2 = 6', 'is_kunci' => true],
                    ['teks' => 'Jika ditambah 1 peci, kombinasinya menjadi 3 × 2 × 1 = 6', 'is_kunci' => true],
                    ['teks' => 'Setiap kemeja dapat dipasangkan dengan 2 jenis celana', 'is_kunci' => true],
                    ['teks' => 'Total variasi dihitung dengan menjumlahkan: 3 + 2 = 5', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Aturan perkalian berlaku untuk pemilihan kejadian berurutan.', 'kunci' => 'Benar'],
                    ['teks' => 'Total pasangan pakaian adalah 6 pasang.', 'kunci' => 'Benar'],
                    ['teks' => 'Variasi pakaian bernilai tak terhingga.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Jumlah Kemeja', 'respon' => '3'],
                    ['premis' => 'Jumlah Celana', 'respon' => '2'],
                    ['premis' => 'Total Kombinasi', 'respon' => '6 (3×2)'],
                    ['premis' => 'Metode Perhitungan', 'respon' => 'Aturan Perkalian']
                ],
                'q_isian' => 'Berapakah hasil kali dari <u>3 × 2</u> pasangan pakaian?', 'isian_k' => '6'
            ],
            [
                'no' => 4, 'range' => '16 - 20', 'judul' => 'Permutasi: Memilih Pengurus Inti Organisasi OSIM',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">P(4, 2) = 4! / (4-2)! = 4 × 3 = 12</text></svg>',
                'cerita' => 'Dari <strong>4 calon pengurus OSIM</strong> madrasah, akan dipilih <u>Ketua</u> dan <u>Sekretaris</u>. Karena jabatan memiliki urutan dan tingkatan, digunakan rumus <u>permutasi</u>: <strong>P(n, r) = n! / (n - r)!</strong>.',
                'q_pg' => 'Berapakah <u>banyaknya susunan pengurus (Ketua & Sekretaris)</u> yang dapat terbentuk dari 4 calon?',
                'pg_ops' => ['6 susunan', '8 susunan', '10 susunan', '12 susunan', '16 susunan'], 'pg_k' => 'D',
                'q_pgk' => 'Karakteristik permutasi dalam pemilihan pengurus yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Permutasi memperhatikan urutan posisi/jabatan', 'is_kunci' => true],
                    ['teks' => 'P(4, 2) = 4 × 3 = 12 susunan', 'is_kunci' => true],
                    ['teks' => 'Posisi Ketua dan Sekretaris tidak dapat ditukar sembarangan', 'is_kunci' => true],
                    ['teks' => 'Permutasi sama persis dengan kombinasi tanpa urutan', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Permutasi memperhatikan urutan jabatan (Ketua ≠ Sekretaris).', 'kunci' => 'Benar'],
                    ['teks' => 'Banyak cara memilih adalah 12 susunan.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai dari 4! adalah 10.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Jumlah Calon (n)', 'respon' => '4'],
                    ['premis' => 'Jumlah Posisi (r)', 'respon' => '2'],
                    ['premis' => 'Rumus P(4, 2)', 'respon' => '4 × 3'],
                    ['premis' => 'Hasil Permutasi', 'respon' => '12 susunan']
                ],
                'q_isian' => 'Berapakah hasil perhitungan <u>P(4, 2) = 4 × 3</u>?', 'isian_k' => '12'
            ],
            [
                'no' => 5, 'range' => '21 - 25', 'judul' => 'Kombinasi: Membentuk Tim Cerdas Cermat',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">C(4, 2) = 4! / (2! × 2!) = 6</text></svg>',
                'cerita' => 'Dari <strong>4 siswa berprestasi</strong>, akan dipilih <strong>2 siswa</strong> sebagai perwakilan tim cerdas cermat madrasah. Karena dalam tim posisi siswa setara dan <u>tidak memperhatikan urutan</u>, digunakan rumus <u>kombinasi</u>: <strong>C(n, r) = n! / (r! × (n - r)!)</strong>.',
                'q_pg' => 'Berapakah <u>banyaknya kombinasi tim</u> yang dapat dibentuk?',
                'pg_ops' => ['4 cara', '6 cara', '8 cara', '10 cara', '12 cara'], 'pg_k' => 'B',
                'q_pgk' => 'Pernyataan tentang kombinasi pemilihan tim yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Kombinasi tidak memperhatikan urutan anggota', 'is_kunci' => true],
                    ['teks' => 'C(4, 2) = (4 × 3) / (2 × 1) = 6', 'is_kunci' => true],
                    ['teks' => 'Memilih siswa A dan B sama dengan memilih B dan A', 'is_kunci' => true],
                    ['teks' => 'Nilai kombinasi selalu lebih besar daripada permutasi', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Kombinasi digunakan saat urutan pemilihan diabaikan.', 'kunci' => 'Benar'],
                    ['teks' => 'Banyak cara memilih tim perwakilan adalah 6 cara.', 'kunci' => 'Benar'],
                    ['teks' => 'C(4, 2) bernilai sama dengan 24.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Jumlah Siswa (n)', 'respon' => '4'],
                    ['premis' => 'Dipilih (r)', 'respon' => '2'],
                    ['premis' => 'Rumus C(4, 2)', 'respon' => '(4×3)/(2×1)'],
                    ['premis' => 'Hasil Kombinasi', 'respon' => '6 cara']
                ],
                'q_isian' => 'Berapakah nilai dari <u>C(4, 2) = (4 × 3) / 2</u>?', 'isian_k' => '6'
            ],
            [
                'no' => 6, 'range' => '26 - 30', 'judul' => 'Integral Tentu: Menghitung Luas Bidang Datar Sederhana',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="12" font-weight="bold" fill="#0369a1" text-anchor="middle">∫₀² 2x dx = [x²]₀² = 2² - 0² = 4</text></svg>',
                'cerita' => 'Luas daerah di bawah kurva y = 2x dari batas <strong>x = 0</strong> sampai <strong>x = 2</strong> dihitung menggunakan <u>integral tentu</u>: <strong>∫₀² 2x dx = [x²]₀²</strong>. Nilai dihitung dengan mensubstitusi batas atas dikurangi batas bawah.',
                'q_pg' => 'Berapakah <u>nilai integral tentu</u> ∫₀² 2x dx?',
                'pg_ops' => ['2', '3', '4', '5', '6'], 'pg_k' => 'C',
                'q_pgk' => 'Sifat integral tentu berikut yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Hasil antiturunan dari 2x adalah x²', 'is_kunci' => true],
                    ['teks' => 'Substitusi batas atas: 2² = 4', 'is_kunci' => true],
                    ['teks' => 'Substitusi batas bawah: 0² = 0', 'is_kunci' => true],
                    ['teks' => 'Integral tentu selalu menghasilkan konstanta C', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai integral tentu di atas adalah 4 satuan luas.', 'kunci' => 'Benar'],
                    ['teks' => 'Integral tentu menghasilkan nilai berupa bilangan riil pasti.', 'kunci' => 'Benar'],
                    ['teks' => 'Batas bawah integral di atas adalah 2.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Fungsi integran', 'respon' => '2x'],
                    ['premis' => 'Batas Bawah', 'respon' => '0'],
                    ['premis' => 'Batas Atas', 'respon' => '2'],
                    ['premis' => 'Hasil Integral Tentu', 'respon' => '4']
                ],
                'q_isian' => 'Berapakah nilai hasil dari <u>[x²]₀² = 2² - 0</u>?', 'isian_k' => '4'
            ],
            [
                'no' => 7, 'range' => '31 - 35', 'judul' => 'Turunan Kedua dan Kecekungan Kurva',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">f(x) = x³ ➜ f\'(x) = 3x² ➜ f\'\'(x) = 6x</text></svg>',
                'cerita' => 'Diberikan fungsi aljabar <strong>f(x) = x³</strong>. <u>Turunan pertama f\'(x) = 3x²</u>, dan <u>turunan kedua f\'\'(x) = 6x</u>. Turunan kedua digunakan untuk menguji kecekungan kurva dan titik belok.',
                'q_pg' => 'Berapakah <u>turunan kedua f\'\'(x)</u> dari fungsi f(x) = x³?',
                'pg_ops' => ['3x', '6x', '6x²', '3x²', '6'], 'pg_k' => 'B',
                'q_pgk' => 'Pernyataan turunan fungsi f(x) = x³ yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Turunan pertama f\'(x) = 3x²', 'is_kunci' => true],
                    ['teks' => 'Turunan kedua f\'\'(x) = 6x', 'is_kunci' => true],
                    ['teks' => 'Nilai f\'\'(1) = 6(1) = 6', 'is_kunci' => true],
                    ['teks' => 'Turunan kedua bernilai konstan tetap', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Turunan kedua diperoleh dengan menurunkan kembali turunan pertama.', 'kunci' => 'Benar'],
                    ['teks' => 'Nilai f\'\'(2) adalah 12 (6 × 2).', 'kunci' => 'Benar'],
                    ['teks' => 'Turunan pertama dari x³ adalah 6x.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Fungsi Awal f(x)', 'respon' => 'x³'],
                    ['premis' => 'Turunan Pertama f\'(x)', 'respon' => '3x²'],
                    ['premis' => 'Turunan Kedua f\'\'(x)', 'respon' => '6x'],
                    ['premis' => 'Nilai f\'\'(1)', 'respon' => '6']
                ],
                'q_isian' => 'Berapakah nilai <u>f\'\'(1)</u> dari f\'\'(x) = 6x?', 'isian_k' => '6'
            ],
            [
                'no' => 8, 'range' => '36 - 40', 'judul' => 'Distribusi Peluang Pelemparan Dua Keping Uang Logam',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0284c7" text-anchor="middle">Ruang Sampel S = {AA, AG, GA, GG}</text></svg>',
                'cerita' => 'Dua keping uang logam dilempar bersamaan satu kali. Setiap koin memiliki sisi <u>Angka (A)</u> dan <u>Gambar (G)</u>. Ruang sampel memiliki <strong>n(S) = 4 kemungkinan</strong>: {AA, AG, GA, GG}.',
                'q_pg' => 'Berapakah <u>peluang munculnya dua sisi angka (AA)</u>?',
                'pg_ops' => ['1/4', '2/4', '3/4', '4/4', '0'], 'pg_k' => 'A',
                'q_pgk' => 'Pernyataan tentang pelemparan dua keping uang logam yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Banyaknya anggota ruang sampel n(S) adalah 4', 'is_kunci' => true],
                    ['teks' => 'Peluang muncul 1 Angka dan 1 Gambar (AG, GA) adalah 2/4 = 1/2', 'is_kunci' => true],
                    ['teks' => 'Peluang muncul dua sisi gambar (GG) adalah 1/4', 'is_kunci' => true],
                    ['teks' => 'Peluang muncul 3 sisi angka adalah 1/2', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Banyaknya titik sampel adalah 4.', 'kunci' => 'Benar'],
                    ['teks' => 'Peluang muncul minimal satu sisi angka adalah 3/4.', 'kunci' => 'Benar'],
                    ['teks' => 'Peluang munculnya sisi angka bernilai lebih dari 1.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Titik Sampel (AA)', 'respon' => '1 kejadian'],
                    ['premis' => 'Titik Sampel (AG, GA)', 'respon' => '2 kejadian'],
                    ['premis' => 'Total Ruang Sampel', 'respon' => '4 kejadian'],
                    ['premis' => 'Peluang Kejadian AA', 'respon' => '1/4']
                ],
                'q_isian' => 'Berapakah <u>banyaknya titik sampel n(S)</u> pada pelemparan 2 keping koin?', 'isian_k' => '4'
            ],
            [
                'no' => 9, 'range' => '41 - 45', 'judul' => 'Statistika: Simpangan Rata-Rata Data Tunggal',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0f172a" text-anchor="middle">Data: 4, 6, 8 ➜ Mean = 6 ➜ SR = (|4-6|+|6-6|+|8-6|)/3</text></svg>',
                'cerita' => 'Diberikan data tunggal sederhana: <strong>4, 6, 8</strong>. Nilai rata-rata data adalah <strong>x̄ = 6</strong>. <u>Simpangan rata-rata (SR)</u> mengukur sejauh mana data menyimpang dari rata-ratanya: <strong>SR = Σ|xᵢ - x̄| / n</strong>.',
                'q_pg' => 'Berapakah nilai <u>simpangan rata-rata (SR)</u> dari data 4, 6, 8?',
                'pg_ops' => ['4/3', '2/3', '1', '2', '3'], 'pg_k' => 'A',
                'q_pgk' => 'Langkah menghitung simpangan rata-rata yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Nilai rata-rata data adalah (4 + 6 + 8)/3 = 6', 'is_kunci' => true],
                    ['teks' => 'Selisih mutlak |4 - 6| = 2 dan |8 - 6| = 2', 'is_kunci' => true],
                    ['teks' => 'Jumlah selisih mutlak adalah 2 + 0 + 2 = 4', 'is_kunci' => true],
                    ['teks' => 'Selisih mutlak dapat bernilai negatif', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Nilai rata-rata data tersebut adalah 6.', 'kunci' => 'Benar'],
                    ['teks' => 'Tanda nilai mutlak selalu menghasilkan nilai non-negatif.', 'kunci' => 'Benar'],
                    ['teks' => 'Simpangan rata-rata selalu bernilai negatif.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Rata-rata (Mean)', 'respon' => '6'],
                    ['premis' => 'Nilai mutlak |4 - 6|', 'respon' => '2'],
                    ['premis' => 'Jumlah selisih mutlak', 'respon' => '4'],
                    ['premis' => 'Banyak data (n)', 'respon' => '3']
                ],
                'q_isian' => 'Berapakah <u>nilai rata-rata (mean)</u> dari data 4, 6, 8?', 'isian_k' => '6'
            ],
            [
                'no' => 10, 'range' => '46 - 50', 'judul' => 'Jarak Titik ke Titik pada Balok',
                'svg' => '<svg width="220" height="65" viewBox="0 0 220 65"><text x="110" y="38" font-size="11" font-weight="bold" fill="#0369a1" text-anchor="middle">Balok p = 4, l = 3, t = 12 ➜ d = √(4²+3²+12²) = 13</text></svg>',
                'cerita' => 'Sebuah balok memiliki panjang <strong>p = 4 cm</strong>, lebar <strong>l = 3 cm</strong>, dan tinggi <strong>t = 12 cm</strong>. <u>Jarak terjauh antara dua titik sudut berlawanan (diagonal ruang)</u> dihitung dengan rumus: <strong>d = √(p² + l² + t²)</strong>.',
                'q_pg' => 'Berapakah <u>panjang diagonal ruang balok</u> tersebut?',
                'pg_ops' => ['10 cm', '11 cm', '12 cm', '13 cm', '15 cm'], 'pg_k' => 'D',
                'q_pgk' => 'Pernyataan tentang dimensi balok yang <strong>BENAR</strong> adalah:',
                'pgk_ops' => [
                    ['teks' => 'Nilai p² + l² = 4² + 3² = 16 + 9 = 25', 'is_kunci' => true],
                    ['teks' => 'Nilai p² + l² + t² = 25 + 144 = 169', 'is_kunci' => true],
                    ['teks' => 'Panjang diagonal ruang adalah √169 = 13 cm', 'is_kunci' => true],
                    ['teks' => 'Balok memiliki 6 rusuk yang sama panjang', 'is_kunci' => false]
                ], 'pgk_k' => 'A, B, C',
                'bs_ops' => [
                    ['teks' => 'Panjang diagonal ruang balok di atas adalah 13 cm.', 'kunci' => 'Benar'],
                    ['teks' => 'Diagonal sisi alas balok adalah 5 cm (√(4²+3²)).', 'kunci' => 'Benar'],
                    ['teks' => 'Semua sisi balok selalu berbentuk persegi sama sisi.', 'kunci' => 'Salah']
                ],
                'jodoh_ops' => [
                    ['premis' => 'Nilai p² + l²', 'respon' => '25 (16+9)'],
                    ['premis' => 'Nilai t²', 'respon' => '144 (12²)'],
                    ['premis' => 'Jumlah kuadrat p²+l²+t²', 'respon' => '169'],
                    ['premis' => 'Panjang Diagonal Ruang', 'respon' => '13 cm']
                ],
                'q_isian' => 'Berapakah <u>panjang diagonal ruang (dalam cm)</u> dari √169?', 'isian_k' => '13'
            ]
        ];

        return self::buildFromMeta($metaXII);
    }

    /**
     * Helper untuk mengonversi array konfigurasi metadata menjadi daftar 50 butir soal
     */
    private static function buildFromMeta($metaList) {
        $list = [];
        foreach ($metaList as $meta) {
            $baseNo = ($meta['no'] - 1) * 5 + 1;
            $sNo = $meta['no'];
            $range = $meta['range'];
            $judul = $meta['judul'];
            $svg = $meta['svg'];
            $cerita = $meta['cerita'];

            // 1. PG
            $list[] = [
                'nomor_urut' => $baseNo,
                'tipe_soal' => 'sec_pg',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, $svg, $cerita, '1. PILIHAN GANDA', $meta['q_pg']),
                'opsi_a' => $meta['pg_ops'][0], 'opsi_b' => $meta['pg_ops'][1], 'opsi_c' => $meta['pg_ops'][2], 'opsi_d' => $meta['pg_ops'][3], 'opsi_e' => $meta['pg_ops'][4],
                'kunci_jawaban' => $meta['pg_k'],
                'konten_tambahan' => ['kunci' => $meta['pg_k'], 'opsi' => ['A' => $meta['pg_ops'][0], 'B' => $meta['pg_ops'][1], 'C' => $meta['pg_ops'][2], 'D' => $meta['pg_ops'][3], 'E' => $meta['pg_ops'][4]]]
            ];

            // 2. PGK
            $list[] = [
                'nomor_urut' => $baseNo + 1,
                'tipe_soal' => 'sec_pgk',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '2. PG KOMPLEKS (CENTANG)', $meta['q_pgk']),
                'opsi_a' => $meta['pgk_ops'][0]['teks'], 'opsi_b' => $meta['pgk_ops'][1]['teks'], 'opsi_c' => $meta['pgk_ops'][2]['teks'], 'opsi_d' => $meta['pgk_ops'][3]['teks'] ?? '', 'opsi_e' => '',
                'kunci_jawaban' => $meta['pgk_k'],
                'konten_tambahan' => ['opsi' => $meta['pgk_ops'], 'kunci_array' => explode(', ', $meta['pgk_k'])]
            ];

            // 3. B/S
            $bsKeys = array_map(function($x){ return $x['kunci']; }, $meta['bs_ops']);
            $list[] = [
                'nomor_urut' => $baseNo + 2,
                'tipe_soal' => 'sec_bs',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '3. TABEL BENAR / SALAH', 'Tentukan kebenaran dari masing-masing pernyataan berikut:'),
                'kunci_jawaban' => json_encode($bsKeys),
                'konten_tambahan' => ['butir' => $meta['bs_ops']]
            ];

            // 4. Jodoh
            $list[] = [
                'nomor_urut' => $baseNo + 3,
                'tipe_soal' => 'sec_jodoh',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '4. MENJODOHKAN', 'Pasangkan pernyataan pada kolom kiri dengan jawaban yang sesuai pada kolom kanan:'),
                'kunci_jawaban' => json_encode($meta['jodoh_ops']),
                'konten_tambahan' => ['pasangan' => $meta['jodoh_ops']]
            ];

            // 5. Isian
            $list[] = [
                'nomor_urut' => $baseNo + 4,
                'tipe_soal' => 'sec_isian',
                'pertanyaan' => self::wrapStimulus($sNo, $range, $judul, '', $cerita, '5. ISIAN SINGKAT', $meta['q_isian']),
                'kunci_jawaban' => $meta['isian_k'],
                'konten_tambahan' => ['rubrik' => $meta['isian_k']]
            ];
        }
        return $list;
    }
}
