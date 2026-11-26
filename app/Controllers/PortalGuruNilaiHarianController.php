<?php
namespace App\Controllers;

use App\Core\Database;

class PortalGuruNilaiHarianController {

    private static function guard() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }
    }

    public static function getGuruId() {
        $db = Database::connect('core');
        $stmt = $db->prepare("SELECT id FROM guru WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $id = $stmt->fetchColumn();
        if (!$id) {
            echo "Akses ditolak. Anda bukan guru.";
            exit;
        }
        return (int)$id;
    }

    private static function render($view, $data = []) {
        extract($data);
        $title_page = $title ?? 'Nilai Harian';
        include __DIR__ . '/../../resources/views/' . $view . '.php';
    }

    /**
     * Resolve mapel_id => nama_mapel map from siakad_db or core_db safely.
     */
    private static function getMapelMap($db_core, $db_siakad) {
        $map = [];
        foreach ([$db_siakad, $db_core] as $conn) {
            foreach (['mapel', 'mata_pelajaran'] as $tbl) {
                try {
                    $rows = $conn->query("SELECT id, nama_mapel FROM {$tbl}")->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        if (!isset($map[$r['id']])) {
                            $map[$r['id']] = $r['nama_mapel'];
                        }
                    }
                } catch (\Exception $e) {}
            }
        }
        return $map;
    }

    /**
     * Resolve a single mapel name by ID across siakad_db and core_db.
     */
    private static function getMapelName($db_core, $db_siakad, $mapel_id) {
        $mapel_id = intval($mapel_id);
        foreach ([$db_siakad, $db_core] as $conn) {
            foreach (['mapel', 'mata_pelajaran'] as $tbl) {
                try {
                    $name = $conn->query("SELECT nama_mapel FROM {$tbl} WHERE id = {$mapel_id}")->fetchColumn();
                    if ($name) return $name;
                } catch (\Exception $e) {}
            }
        }
        return 'Mapel #' . $mapel_id;
    }

    /**
     * Fetch kelasList and mengajarData for the current teacher.
     */
    private static function getTeachingOptions($db_core, $db_siakad, $guru_id, $ta) {
        $kelasList = [];
        $mengajarData = [];

        $taId = (int)($ta['id'] ?? 0);
        $sem  = (int)($ta['semester'] ?? 1);

        // 1. Try siakad_db.mengajar
        foreach (['mapel', 'mata_pelajaran'] as $mapelTbl) {
            if (!empty($mengajarData)) break;
            try {
                $stmt_k = $db_siakad->prepare("
                    SELECT DISTINCT k.id, k.nama_kelas
                    FROM mengajar m
                    JOIN core_db.kelas k ON k.id = m.kelas_id
                    WHERE m.guru_id = ? AND (m.tahun_ajaran_id = ? OR m.tahun_ajaran_id IS NULL) AND (m.semester = ? OR m.semester IS NULL)
                    ORDER BY k.tingkat, k.nama_kelas
                ");
                $stmt_k->execute([$guru_id, $taId, $sem]);
                $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);

                $stmt_m = $db_siakad->prepare("
                    SELECT DISTINCT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
                    FROM mengajar m
                    JOIN {$mapelTbl} mp ON mp.id = m.mapel_id
                    WHERE m.guru_id = ? AND (m.tahun_ajaran_id = ? OR m.tahun_ajaran_id IS NULL) AND (m.semester = ? OR m.semester IS NULL)
                    ORDER BY mp.nama_mapel
                ");
                $stmt_m->execute([$guru_id, $taId, $sem]);
                $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        // 2. Fallback to jadwal_pelajaran (supports mapel_id or mata_pelajaran_id)
        if (empty($mengajarData)) {
            foreach (['mapel_id', 'mata_pelajaran_id'] as $col) {
                foreach (['mapel', 'mata_pelajaran'] as $mapelTbl) {
                    if (!empty($mengajarData)) break;
                    try {
                        $stmt_k = $db_siakad->prepare("
                            SELECT DISTINCT k.id, k.nama_kelas
                            FROM jadwal_pelajaran jp
                            JOIN core_db.kelas k ON k.id = jp.kelas_id
                            WHERE jp.guru_id = ?
                            ORDER BY k.tingkat, k.nama_kelas
                        ");
                        $stmt_k->execute([$guru_id]);
                        $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);

                        $stmt_m = $db_siakad->prepare("
                            SELECT DISTINCT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel
                            FROM jadwal_pelajaran jp
                            JOIN {$mapelTbl} mp ON mp.id = jp.{$col}
                            WHERE jp.guru_id = ?
                            ORDER BY mp.nama_mapel
                        ");
                        $stmt_m->execute([$guru_id]);
                        $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);
                    } catch (\Exception $e) {}
                }
            }
        }

        // 3. Fallback to all kelas & mapel if still empty
        if (empty($kelasList)) {
            try {
                $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        if (empty($mengajarData) && !empty($kelasList)) {
            $mapelMap = self::getMapelMap($db_core, $db_siakad);
            foreach ($kelasList as $k) {
                foreach ($mapelMap as $mId => $mName) {
                    $mengajarData[] = [
                        'kelas_id' => $k['id'],
                        'mapel_id' => $mId,
                        'nama_mapel' => $mName
                    ];
                }
            }
        }

        return [$kelasList, $mengajarData];
    }

    public static function index() {
        self::guard();
        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');

        $guru_id = self::getGuruId();
        $ta = \App\Core\AcademicYear::current();
        $taId = (int)($ta['id'] ?? 0);
        $sem  = (int)($ta['semester'] ?? 1);

        // Ambil riwayat nilai harian guru pada semester aktif (termasuk data lama tanpa tahun_ajaran_id jika ada)
        $stmt = $db->prepare("
            SELECT nh.*, k.nama_kelas,
                   (SELECT COUNT(*) FROM nilai_harian_detail WHERE nilai_harian_id = nh.id) as jumlah_siswa,
                   (SELECT AVG(nilai) FROM nilai_harian_detail WHERE nilai_harian_id = nh.id) as rata_rata
            FROM nilai_harian nh
            LEFT JOIN kelas k ON nh.kelas_id = k.id
            WHERE nh.guru_id = ?
              AND (nh.tahun_ajaran_id = ? OR nh.tahun_ajaran_id IS NULL OR nh.tahun_ajaran_id = 0)
              AND (nh.semester = ? OR nh.semester IS NULL OR nh.semester = 0)
            ORDER BY nh.tanggal DESC, nh.id DESC
        ");
        $stmt->execute([$guru_id, $taId, $sem]);
        $riwayat = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mapelMap = self::getMapelMap($db, $siakad_db);
        foreach ($riwayat as &$r) {
            $r['nama_kelas'] = $r['nama_kelas'] ?: ('Kelas #' . $r['kelas_id']);
            $r['nama_mapel'] = $mapelMap[$r['mapel_id']] ?? ('Mapel #' . $r['mapel_id']);
        }
        unset($r);

        [$kelasList, $mengajarData] = self::getTeachingOptions($db, $siakad_db, $guru_id, $ta);

        self::render('portal/guru/nilai_harian_list', [
            'riwayat' => $riwayat,
            'kelasList' => $kelasList,
            'mengajarData' => $mengajarData,
            'guru_id' => $guru_id,
            'title' => 'Rekap Nilai Harian'
        ]);
    }

    public static function detail($id) {
        self::guard();
        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');
        $id = intval($id);
        $guru_id = self::getGuruId();

        $stmt = $db->prepare("
            SELECT nh.*, k.nama_kelas
            FROM nilai_harian nh
            LEFT JOIN kelas k ON nh.kelas_id = k.id
            WHERE nh.id = ? AND nh.guru_id = ?
        ");
        $stmt->execute([$id, $guru_id]);
        $header = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$header) {
            die("Data tidak ditemukan atau Anda tidak memiliki akses.");
        }

        $header['nama_kelas'] = $header['nama_kelas'] ?: ('Kelas #' . $header['kelas_id']);
        $header['nama_mapel'] = self::getMapelName($db, $siakad_db, $header['mapel_id']);

        $stmt_d = $db->prepare("
            SELECT nd.*, s.nama, s.nis
            FROM nilai_harian_detail nd
            JOIN siswa s ON nd.siswa_id = s.id
            WHERE nd.nilai_harian_id = ?
            ORDER BY s.nama ASC
        ");
        $stmt_d->execute([$id]);
        $details = $stmt_d->fetchAll(\PDO::FETCH_ASSOC);

        self::render('portal/guru/nilai_harian_detail', [
            'header' => $header,
            'details' => $details,
            'title' => 'Edit Nilai Harian'
        ]);
    }

    public static function delete($id) {
        self::guard();
        $db = Database::connect('core');
        $id = intval($id);
        $guru_id = self::getGuruId();

        $stmt_chk = $db->prepare("SELECT id, kelas_id, mapel_id FROM nilai_harian WHERE id = ? AND guru_id = ?");
        $stmt_chk->execute([$id, $guru_id]);
        $check = $stmt_chk->fetch(\PDO::FETCH_ASSOC);

        if ($check) {
            $db->prepare("DELETE FROM nilai_harian_detail WHERE nilai_harian_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM nilai_harian WHERE id = ?")->execute([$id]);

            // Re-numbering PH
            $stmt2 = $db->prepare("
                SELECT id, judul_materi
                FROM nilai_harian
                WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ?
                  AND (judul_materi LIKE 'PH %' OR judul_materi LIKE 'Penilaian Harian (PH %')
                ORDER BY tanggal ASC, id ASC
            ");
            $stmt2->execute([$guru_id, $check['kelas_id'], $check['mapel_id']]);
            $ph_list = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

            $no = 1;
            $stmt_upd = $db->prepare("UPDATE nilai_harian SET judul_materi = ? WHERE id = ?");
            foreach ($ph_list as $ph) {
                $parts = explode(' - ', $ph['judul_materi'], 2);
                $new = 'PH ' . $no . (isset($parts[1]) ? ' - ' . $parts[1] : '');
                $stmt_upd->execute([$new, $ph['id']]);
                $no++;
            }
        }

        redirect('/guru/nilai_harian?msg=deleted');
        exit;
    }

    public static function storeNew() {
        self::guard();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/guru/nilai_harian');
            exit;
        }

        $db = Database::connect('core');
        $guru_id = self::getGuruId();

        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? 'PH';
        $materi_pokok = trim($_POST['materi_pokok'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

        if (!$kelas_id || !$mapel_id) {
            die("Data kelas dan mata pelajaran tidak lengkap.");
        }

        $ta = \App\Core\AcademicYear::current();
        $taId = (int)($ta['id'] ?? 1);
        $sem  = (int)($ta['semester'] ?? 1);

        if ($jenis_evaluasi === 'PH') {
            $stmt_cek = $db->prepare("
                SELECT COUNT(id) FROM nilai_harian
                WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ?
                  AND (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL)
                  AND (semester = ? OR semester IS NULL)
                  AND (judul_materi LIKE 'PH %' OR judul_materi LIKE 'Penilaian Harian (PH %')
            ");
            $stmt_cek->execute([$guru_id, $kelas_id, $mapel_id, $taId, $sem]);
            $next_no = ((int)$stmt_cek->fetchColumn()) + 1;
            $judul = "PH $next_no";
            $jenis_col = $judul;
        } elseif ($jenis_evaluasi === 'PTS') {
            $judul = "PTS";
            $jenis_col = "PTS";
        } elseif ($jenis_evaluasi === 'PAS') {
            $judul = "PAS";
            $jenis_col = "PAS";
        } else {
            $judul = "Evaluasi Lain";
            $jenis_col = "Tugas";
        }

        $judul_materi = $materi_pokok ? ($judul . ' - ' . $materi_pokok) : $judul;

        try {
            $stmt_hdr = $db->prepare("INSERT INTO nilai_harian (guru_id, kelas_id, mapel_id, jenis_evaluasi, judul_materi, tanggal, tahun_ajaran_id, semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_hdr->execute([$guru_id, $kelas_id, $mapel_id, $jenis_col, $judul_materi, $tanggal, $taId, $sem]);
        } catch (\PDOException $e) {
            // Fallback jika kolom jenis_evaluasi tidak ada di tabel nilai_harian
            $stmt_hdr = $db->prepare("INSERT INTO nilai_harian (guru_id, kelas_id, mapel_id, judul_materi, tanggal, tahun_ajaran_id, semester) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_hdr->execute([$guru_id, $kelas_id, $mapel_id, $judul_materi, $tanggal, $taId, $sem]);
        }

        $nh_id = $db->lastInsertId();

        $stmt_s = $db->prepare("SELECT id FROM siswa WHERE kelas_id = ? AND status = 'Aktif'");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_COLUMN);

        $stmt_dtl = $db->prepare("INSERT INTO nilai_harian_detail (nilai_harian_id, siswa_id, nilai) VALUES (?, ?, 0)");
        foreach ($siswa_list as $s_id) {
            $stmt_dtl->execute([$nh_id, $s_id]);
        }

        redirect('/guru/nilai_harian/detail/' . $nh_id . '?msg=created');
        exit;
    }

    public static function update() {
        self::guard();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/guru/nilai_harian');
            exit;
        }

        $db = Database::connect('core');
        $id = intval($_POST['id'] ?? 0);
        $guru_id = self::getGuruId();

        $stmt_chk = $db->prepare("SELECT id FROM nilai_harian WHERE id = ? AND guru_id = ?");
        $stmt_chk->execute([$id, $guru_id]);
        if (!$stmt_chk->fetch()) {
            die("Data tidak ditemukan atau Anda tidak memiliki akses.");
        }

        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $judul_materi = trim($_POST['judul_materi'] ?? '');
        $nilais = $_POST['nilai'] ?? [];

        $stmt = $db->prepare("UPDATE nilai_harian SET tanggal = ?, judul_materi = ? WHERE id = ?");
        $stmt->execute([$tanggal, $judul_materi, $id]);

        $stmt_detail = $db->prepare("UPDATE nilai_harian_detail SET nilai = ? WHERE nilai_harian_id = ? AND siswa_id = ?");
        foreach ($nilais as $siswa_id => $nilai) {
            $siswa_id = intval($siswa_id);
            $nilai = floatval($nilai);
            if ($nilai < 0) $nilai = 0;
            if ($nilai > 100) $nilai = 100;
            $stmt_detail->execute([$nilai, $id, $siswa_id]);
        }

        redirect('/guru/nilai_harian/detail/' . $id . '?msg=updated');
        exit;
    }

    /**
     * Shared data builder for rekapDetail and cetakRekap.
     */
    private static function buildRekapData($kelas_id, $mapel_id, $guru_id) {
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_id = $active_year ? (int)$active_year['id'] : 1;
        $active_semester = $active_year ? (int)$active_year['semester'] : 1;

        $stmt_k = $db_core->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $kelas_row = $stmt_k->fetch(\PDO::FETCH_ASSOC);
        $kelas = $kelas_row ? $kelas_row['nama_kelas'] : ('Kelas #' . $kelas_id);
        $tingkat = $kelas_row ? (int)($kelas_row['tingkat'] ?? 10) : 10;

        $mapel = self::getMapelName($db_core, $db_siakad, $mapel_id);

        $stmt_g = $db_core->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmt_g->execute([$guru_id]);
        $guru = $stmt_g->fetchColumn() ?: '-';

        $pengaturan = $db_siakad->query("SELECT tahun_ajaran_aktif, semester_aktif FROM pengaturan LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran = $pengaturan ? $pengaturan['tahun_ajaran_aktif'] : ($active_year['name'] ?? '-');
        $semester = $pengaturan ? $pengaturan['semester_aktif'] : ($active_semester == 2 ? 'Genap' : 'Ganjil');

        $stmt_wali = $db_core->prepare("
            SELECT g.nama
            FROM guru_tugas gt
            JOIN guru g ON gt.guru_id = g.id
            WHERE gt.tugas = 'Wali Kelas' AND (gt.keterangan = ? OR gt.keterangan = ?) AND gt.tahun_ajaran = ?
            LIMIT 1
        ");
        $stmt_wali->execute([$kelas, 'Kelas ' . $kelas, $tahun_ajaran]);
        $wali_kelas = $stmt_wali->fetchColumn() ?: '-';

        $stmt_ev = $db_core->prepare("
            SELECT * FROM nilai_harian
            WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ?
              AND (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL OR tahun_ajaran_id = 0)
              AND (semester = ? OR semester IS NULL OR semester = 0)
            ORDER BY tanggal ASC, id ASC
        ");
        $stmt_ev->execute([$guru_id, $kelas_id, $mapel_id, $active_year_id, $active_semester]);
        $evaluasi_list_raw = $stmt_ev->fetchAll(\PDO::FETCH_ASSOC);

        $evaluasi_list = [];
        $pts_eval_id = null;
        $pas_eval_id = null;

        foreach ($evaluasi_list_raw as $ev) {
            $j = strtoupper(trim($ev['judul_materi']));
            if (strpos($j, 'PTS') === 0) {
                $pts_eval_id = $ev['id'];
            } elseif (strpos($j, 'PAS') === 0) {
                $pas_eval_id = $ev['id'];
            } else {
                $evaluasi_list[] = $ev;
            }
        }

        $stmt_s = $db_core->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

        $kkm = 75;
        $bobot_rata = 50;
        $bobot_pas = 50;
        try {
            $stmt_kb = $db_siakad->prepare("SELECT kkm, bobot_rata, bobot_pas FROM kkm_bobot WHERE mapel_id = ? AND kelas_id = ?");
            $stmt_kb->execute([$mapel_id, $kelas_id]);
            $kkm_data = $stmt_kb->fetch(\PDO::FETCH_ASSOC);
            if ($kkm_data) {
                $kkm = (float)$kkm_data['kkm'];
                $bobot_rata = (float)$kkm_data['bobot_rata'];
                $bobot_pas = (float)$kkm_data['bobot_pas'];
            }
        } catch (\Exception $e) {
            try {
                $stmt_kkm = $db_core->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE mapel_id = ? AND tingkat = ? LIMIT 1");
                $stmt_kkm->execute([$mapel_id, $tingkat]);
                $kkm_val = $stmt_kkm->fetchColumn();
                if ($kkm_val !== false) $kkm = (float)$kkm_val;
            } catch (\Exception $e2) {}
        }

        $stmt_nd = $db_core->prepare("SELECT nilai FROM nilai_harian_detail WHERE nilai_harian_id = ? AND siswa_id = ?");
        $stmt_cbt = $db_core->prepare("SELECT nilai FROM ujian_nilai WHERE mapel = ? AND jenis = ? AND nis = ? LIMIT 1");

        foreach ($siswa_list as &$siswa) {
            $s_id = $siswa['id'];
            $siswa['nilai_harian'] = [];
            $sum_ph = 0;
            $count_ph = 0;
            foreach ($evaluasi_list as $ev) {
                $nh_id = $ev['id'];
                $stmt_nd->execute([$nh_id, $s_id]);
                $nilai_h = $stmt_nd->fetchColumn();
                $siswa['nilai_harian'][$nh_id] = $nilai_h !== false ? round((float)$nilai_h) : '-';
                if ($nilai_h !== false) {
                    $sum_ph += (float)$nilai_h;
                    $count_ph++;
                }
            }

            $n_pts_manual = false;
            if ($pts_eval_id) {
                $stmt_nd->execute([$pts_eval_id, $s_id]);
                $n_pts_manual = $stmt_nd->fetchColumn();
            }
            $n_pts_cbt = false;
            try {
                $stmt_cbt->execute([$mapel, 'uts', $siswa['nis']]);
                $n_pts_cbt = $stmt_cbt->fetchColumn();
            } catch (\Exception $e) {}
            $n_pts = $n_pts_manual !== false ? $n_pts_manual : $n_pts_cbt;

            $n_pas_manual = false;
            if ($pas_eval_id) {
                $stmt_nd->execute([$pas_eval_id, $s_id]);
                $n_pas_manual = $stmt_nd->fetchColumn();
            }
            $n_pas_cbt = false;
            try {
                $stmt_cbt->execute([$mapel, 'uas', $siswa['nis']]);
                $n_pas_cbt = $stmt_cbt->fetchColumn();
            } catch (\Exception $e) {}
            $n_pas = $n_pas_manual !== false ? $n_pas_manual : $n_pas_cbt;

            $siswa['nilai_pts'] = $n_pts !== false ? round((float)$n_pts) : '-';
            $siswa['nilai_pas'] = $n_pas !== false ? round((float)$n_pas) : '-';

            $pts_val = $n_pts !== false ? (float)$n_pts : 0;
            $pas_val = $n_pas !== false ? (float)$n_pas : 0;

            $total_ph_pts = $sum_ph + $pts_val;
            $pembagi = $count_ph + ($n_pts !== false ? 1 : 0);
            $nilai_gabungan = $pembagi > 0 ? ($total_ph_pts / $pembagi) : 0;

            $total_bobot = $bobot_rata + $bobot_pas;
            if ($total_bobot == 0) $total_bobot = 100;

            $siswa['nilai_rapor'] = round((($nilai_gabungan * $bobot_rata) + ($pas_val * $bobot_pas)) / $total_bobot);
        }
        unset($siswa);

        return compact('kelas', 'mapel', 'guru', 'wali_kelas', 'tahun_ajaran', 'semester', 'evaluasi_list', 'siswa_list', 'kkm', 'kelas_id', 'mapel_id', 'guru_id');
    }

    public static function rekapDetail() {
        self::guard();
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $mapel_id = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        $guru_id = self::getGuruId();

        if (!$kelas_id || !$mapel_id) {
            die("Parameter kelas_id dan mapel_id wajib diisi.");
        }

        $data = self::buildRekapData($kelas_id, $mapel_id, $guru_id);
        $data['title'] = 'Detail Rekap Nilai Harian';
        self::render('portal/guru/nilai_harian_rekap_detail', $data);
    }

    public static function cetakRekap() {
        self::guard();
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $mapel_id = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        $guru_id = self::getGuruId();

        if (!$kelas_id || !$mapel_id) {
            die("Parameter kelas_id dan mapel_id wajib diisi.");
        }

        $db_core = Database::connect('core');
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];
        $data = self::buildRekapData($kelas_id, $mapel_id, $guru_id);
        $data['inst'] = $inst;

        include __DIR__ . '/../../resources/views/portal/guru/nilai_harian_cetak.php';
    }

    public static function cetakBlanko() {
        self::guard();
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $mapel_id = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        $guru_id = self::getGuruId();
        $jumlah_ph = max(1, min(15, isset($_GET['jumlah_ph']) ? intval($_GET['jumlah_ph']) : 5));

        $stmt_k = $db_core->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $kelas = $stmt_k->fetchColumn();

        $mapel = self::getMapelName($db_core, $db_siakad, $mapel_id);

        $stmt_g = $db_core->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmt_g->execute([$guru_id]);
        $guru = $stmt_g->fetchColumn();

        if (empty($kelas) || empty($mapel) || empty($guru)) {
            die("Data Kelas, Mapel, atau Guru tidak ditemukan.");
        }

        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];
        $stmt_s = $db_core->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

        include __DIR__ . '/../../resources/views/portal/guru/nilai_harian_blanko.php';
    }
}
