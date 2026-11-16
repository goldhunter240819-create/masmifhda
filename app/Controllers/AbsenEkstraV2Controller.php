<?php

namespace App\Controllers;

use App\Core\Database;

class AbsenEkstraV2Controller {

    private static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }
    }

    private static function getActiveYearAndSemester() {
        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran = $active_year ? $active_year['name'] : '2026/2027';
        $semester = (date('n') >= 7 && date('n') <= 12) ? 'Ganjil' : 'Genap';
        return [$tahun_ajaran, $semester];
    }

    private static function ensureSchema() {
        try {
            $db_siakad = Database::connect('siakad');
            
            // Cek & tambah kolom ekstra_absen
            $colsAbsen = $db_siakad->query("SHOW COLUMNS FROM ekstra_absen")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('status_kehadiran', $colsAbsen)) {
                $db_siakad->exec("ALTER TABLE ekstra_absen ADD COLUMN status_kehadiran VARCHAR(20) DEFAULT 'Hadir'");
            }
            if (!in_array('catatan', $colsAbsen)) {
                $db_siakad->exec("ALTER TABLE ekstra_absen ADD COLUMN catatan VARCHAR(255) NULL");
            }

            // Cek & tambah kolom ekstra_jurnal
            $colsJurnal = $db_siakad->query("SHOW COLUMNS FROM ekstra_jurnal")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('guru_id', $colsJurnal)) {
                $db_siakad->exec("ALTER TABLE ekstra_jurnal ADD COLUMN guru_id INT(11) NULL");
            }
            if (!in_array('pertemuan_ke', $colsJurnal)) {
                $db_siakad->exec("ALTER TABLE ekstra_jurnal ADD COLUMN pertemuan_ke INT(11) NULL DEFAULT 1");
            }
            if (!in_array('jam_mulai', $colsJurnal)) {
                $db_siakad->exec("ALTER TABLE ekstra_jurnal ADD COLUMN jam_mulai VARCHAR(10) NULL");
            }
            if (!in_array('jam_selesai', $colsJurnal)) {
                $db_siakad->exec("ALTER TABLE ekstra_jurnal ADD COLUMN jam_selesai VARCHAR(10) NULL");
            }

            // Cek & tambah kolom ekstra_program
            $colsProg = $db_siakad->query("SHOW COLUMNS FROM ekstra_program")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('deskripsi', $colsProg)) {
                $db_siakad->exec("ALTER TABLE ekstra_program ADD COLUMN deskripsi TEXT NULL");
            }
            if (!in_array('status', $colsProg)) {
                $db_siakad->exec("ALTER TABLE ekstra_program ADD COLUMN status VARCHAR(20) DEFAULT 'Aktif'");
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Halaman Input / Edit Presensi & Jurnal Kegiatan Ekstrakurikuler
     */
    public static function presensi() {
        self::checkAuth();
        self::ensureSchema();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        // 1. Ambil semua cabang ekstrakurikuler yang terdaftar
        $ekstraList = $db_siakad->query("SELECT ep.*, g.nama as nama_pembina 
                                        FROM ekstra_program ep 
                                        LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                        ORDER BY ep.nama_ekstra ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // Jika belum ada cabang ekstra sama sekali, buatkan default
        if (empty($ekstraList)) {
            $defaultEkstras = [
                ['Pramuka Penegak', 'Sabtu', '14.00 - 16.00 WIB'],
                ['Bola Voli', 'Sabtu', '15.30 - 17.00 WIB'],
                ['Futsal', 'Jumat', '15.30 - 17.00 WIB'],
                ['Tenis Meja', 'Sabtu', '14.00 - 16.00 WIB'],
                ['Muhadoroh / Da\'wah', 'Kamis', '13.30 - 15.00 WIB'],
                ['PMR (Palang Merah Remaja)', 'Sabtu', '14.00 - 15.30 WIB'],
                ['Hadroh / Seni Religi', 'Rabu', '15.00 - 16.30 WIB']
            ];
            foreach ($defaultEkstras as $de) {
                $stmtIns = $db_siakad->prepare("INSERT INTO ekstra_program (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, status) VALUES (?, 0, ?, ?, ?, ?, 'Aktif')");
                $stmtIns->execute([$de[0], $active_year_name, $active_semester, $de[1], $de[2]]);
            }
            $ekstraList = $db_siakad->query("SELECT ep.*, g.nama as nama_pembina 
                                            FROM ekstra_program ep 
                                            LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                            ORDER BY ep.nama_ekstra ASC")->fetchAll(\PDO::FETCH_ASSOC);
        }

        // 2. Tentukan cabang ekstra terpilih
        $selected_ekstra = $_GET['ekstra'] ?? ($ekstraList[0]['nama_ekstra'] ?? '');
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $jurnal_id = isset($_GET['jurnal_id']) ? intval($_GET['jurnal_id']) : null;

        // Ambil info cabang ekstra terpilih
        $stmtInfo = $db_siakad->prepare("SELECT ep.*, g.nama as nama_pembina, g.nip as nip_pembina 
                                         FROM ekstra_program ep 
                                         LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                         WHERE ep.nama_ekstra = ? LIMIT 1");
        $stmtInfo->execute([$selected_ekstra]);
        $ekstraDetail = $stmtInfo->fetch(\PDO::FETCH_ASSOC);

        // 3. Ambil data jurnal (jika ada pada tanggal tersebut atau berdasarkan jurnal_id)
        $currentJurnal = null;
        if ($jurnal_id) {
            $stmtJ = $db_siakad->prepare("SELECT * FROM ekstra_jurnal WHERE id = ?");
            $stmtJ->execute([$jurnal_id]);
            $currentJurnal = $stmtJ->fetch(\PDO::FETCH_ASSOC);
            if ($currentJurnal) {
                $tanggal = $currentJurnal['tanggal'];
                $selected_ekstra = $currentJurnal['nama_ekstra'];
            }
        } else {
            $stmtJ = $db_siakad->prepare("SELECT * FROM ekstra_jurnal WHERE nama_ekstra = ? AND tanggal = ? ORDER BY id DESC LIMIT 1");
            $stmtJ->execute([$selected_ekstra, $tanggal]);
            $currentJurnal = $stmtJ->fetch(\PDO::FETCH_ASSOC);
        }

        // Tentukan pertemuan_ke otomatis jika belum ada jurnal
        $pertemuan_ke = 1;
        if ($currentJurnal) {
            $pertemuan_ke = $currentJurnal['pertemuan_ke'] ?? 1;
        } else {
            $cntJurnal = $db_siakad->prepare("SELECT COUNT(*) FROM ekstra_jurnal WHERE nama_ekstra = ? AND tahun_ajaran = ?");
            $cntJurnal->execute([$selected_ekstra, $active_year_name]);
            $pertemuan_ke = intval($cntJurnal->fetchColumn()) + 1;
        }

        // 4. Ambil daftar anggota siswa untuk ekstra ini
        // Kita join dengan core_db siswa & kelas
        $anggotaList = $db_siakad->query("SELECT ea.*, s.nama, s.nis, s.nisn, s.jenis_kelamin, 
                                                COALESCE(k_hist.nama_kelas, k.nama_kelas, '-') as nama_kelas 
                                         FROM ekstra_anggota ea 
                                         JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                         LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                             AND rks.tahun_ajaran_id IN (SELECT id FROM aula_db.tahun_ajaran WHERE name = '$active_year_name')
                                         LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                         LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                                         WHERE ea.nama_ekstra = ".$db_siakad->quote($selected_ekstra)." 
                                         ORDER BY nama_kelas ASC, s.nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // 5. Ambil data absensi jika jurnal sudah ada
        $absenMap = [];
        $catatanMap = [];
        if ($currentJurnal) {
            $absenRows = $db_siakad->query("SELECT siswa_id, status, status_kehadiran, catatan 
                                           FROM ekstra_absen 
                                           WHERE jurnal_id = " . intval($currentJurnal['id']))->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($absenRows as $ar) {
                // Konversi format status
                $st = strtoupper($ar['status'] ?? 'H');
                if (empty($st) || !in_array($st, ['H', 'S', 'I', 'A'])) {
                    if (stripos($ar['status_kehadiran'], 'sakit') !== false) $st = 'S';
                    elseif (stripos($ar['status_kehadiran'], 'izin') !== false) $st = 'I';
                    elseif (stripos($ar['status_kehadiran'], 'alpa') !== false) $st = 'A';
                    else $st = 'H';
                }
                $absenMap[$ar['siswa_id']] = $st;
                $catatanMap[$ar['siswa_id']] = $ar['catatan'] ?? '';
            }
        }

        // 6. Ambil riwayat pertemuan sebelumnya untuk cabang ekstra ini
        $riwayatJurnal = $db_siakad->query("SELECT ej.*, 
                                                COUNT(ea.id) as total_absen,
                                                SUM(CASE WHEN ea.status = 'H' OR ea.status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as tot_hadir,
                                                SUM(CASE WHEN ea.status = 'S' OR ea.status_kehadiran = 'Sakit' THEN 1 ELSE 0 END) as tot_sakit,
                                                SUM(CASE WHEN ea.status = 'I' OR ea.status_kehadiran = 'Izin' THEN 1 ELSE 0 END) as tot_izin,
                                                SUM(CASE WHEN ea.status = 'A' OR ea.status_kehadiran = 'Alpa' THEN 1 ELSE 0 END) as tot_alpa
                                            FROM ekstra_jurnal ej 
                                            LEFT JOIN ekstra_absen ea ON ej.id = ea.jurnal_id 
                                            WHERE ej.nama_ekstra = ".$db_siakad->quote($selected_ekstra)." 
                                            GROUP BY ej.id 
                                            ORDER BY ej.tanggal DESC, ej.id DESC 
                                            LIMIT 20")->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Presensi Ekstrakurikuler - Absen V2";
        $initial = strtoupper(substr($_SESSION['nama'] ?? 'Admin', 0, 1));
        $nama = $_SESSION['nama'] ?? 'Administrator';

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/ekstra/presensi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan Presensi & Jurnal Kegiatan Ekstrakurikuler
     */
    public static function savePresensi() {
        self::checkAuth();
        self::ensureSchema();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra');
            exit;
        }

        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? date('Y-m-d'));
        $pertemuan_ke = intval($_POST['pertemuan_ke'] ?? 1);
        $jam_mulai = trim($_POST['jam_mulai'] ?? '14:00');
        $jam_selesai = trim($_POST['jam_selesai'] ?? '16:00');
        $materi = trim($_POST['materi'] ?? 'Latihan Rutin Ekstrakurikuler');
        $jurnal_id = !empty($_POST['jurnal_id']) ? intval($_POST['jurnal_id']) : null;
        $absensi = $_POST['absensi'] ?? []; // [siswa_id => 'H'|'S'|'I'|'A']
        $catatan_siswa = $_POST['catatan'] ?? [];

        if (empty($nama_ekstra)) {
            redirect('/admin/absen_v2/ekstra?msg=error_no_ekstra');
            exit;
        }

        // Ambil guru_id pembina dari ekstra_program
        $stmtProg = $db_siakad->prepare("SELECT guru_id FROM ekstra_program WHERE nama_ekstra = ? LIMIT 1");
        $stmtProg->execute([$nama_ekstra]);
        $guru_id = intval($stmtProg->fetchColumn() ?: 0);

        // Upload dokumentasi jika ada
        $dokumentasi = null;
        if ($jurnal_id) {
            $stmtOldDoc = $db_siakad->prepare("SELECT dokumentasi FROM ekstra_jurnal WHERE id = ?");
            $stmtOldDoc->execute([$jurnal_id]);
            $dokumentasi = $stmtOldDoc->fetchColumn();
        }

        if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/ekstra_jurnal/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                $filename = 'ekstra_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $uploadDir . $filename)) {
                    if ($dokumentasi && file_exists($uploadDir . $dokumentasi)) {
                        @unlink($uploadDir . $dokumentasi);
                    }
                    $dokumentasi = $filename;
                }
            }
        }

        $db_siakad->beginTransaction();
        try {
            // Cek apakah jurnal sudah ada pada tanggal ini untuk ekstra ini jika bukan mode edit
            if (!$jurnal_id) {
                $stmtCek = $db_siakad->prepare("SELECT id FROM ekstra_jurnal WHERE nama_ekstra = ? AND tanggal = ? LIMIT 1");
                $stmtCek->execute([$nama_ekstra, $tanggal]);
                $ada = $stmtCek->fetchColumn();
                if ($ada) {
                    $jurnal_id = intval($ada);
                }
            }

            if ($jurnal_id) {
                $stmtUpd = $db_siakad->prepare("UPDATE ekstra_jurnal 
                                                SET tanggal = ?, pertemuan_ke = ?, jam_mulai = ?, jam_selesai = ?, materi = ?, dokumentasi = ?, guru_id = ?, tahun_ajaran = ?, semester = ? 
                                                WHERE id = ?");
                $stmtUpd->execute([$tanggal, $pertemuan_ke, $jam_mulai, $jam_selesai, $materi, $dokumentasi, $guru_id, $active_year_name, $active_semester, $jurnal_id]);
            } else {
                $stmtIns = $db_siakad->prepare("INSERT INTO ekstra_jurnal 
                                                (nama_ekstra, tahun_ajaran, semester, tanggal, materi, dokumentasi, guru_id, pertemuan_ke, jam_mulai, jam_selesai) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtIns->execute([$nama_ekstra, $active_year_name, $active_semester, $tanggal, $materi, $dokumentasi, $guru_id, $pertemuan_ke, $jam_mulai, $jam_selesai]);
                $jurnal_id = $db_siakad->lastInsertId();
            }

            // Hapus presensi lama pada jurnal ini
            $db_siakad->exec("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($jurnal_id));

            // Insert presensi baru
            $statusTextMap = [
                'H' => 'Hadir',
                'S' => 'Sakit',
                'I' => 'Izin',
                'A' => 'Alpa'
            ];

            if (!empty($absensi) && is_array($absensi)) {
                $stmtAbs = $db_siakad->prepare("INSERT INTO ekstra_absen (jurnal_id, siswa_id, status, status_kehadiran, catatan) VALUES (?, ?, ?, ?, ?)");
                foreach ($absensi as $s_id => $st) {
                    $s_id = intval($s_id);
                    $st = strtoupper(trim($st));
                    if (!in_array($st, ['H', 'S', 'I', 'A'])) {
                        $st = 'H';
                    }
                    $stText = $statusTextMap[$st] ?? 'Hadir';
                    $cat = trim($catatan_siswa[$s_id] ?? '');
                    $stmtAbs->execute([$jurnal_id, $s_id, $st, $stText, $cat]);
                }
            }

            $db_siakad->commit();
            redirect("/admin/absen_v2/ekstra?ekstra=" . urlencode($nama_ekstra) . "&tanggal=" . urlencode($tanggal) . "&msg=saved");
            exit;
        } catch (\Throwable $e) {
            $db_siakad->rollBack();
            redirect("/admin/absen_v2/ekstra?ekstra=" . urlencode($nama_ekstra) . "&tanggal=" . urlencode($tanggal) . "&msg=error&err=" . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Hapus Presensi / Jurnal Pertemuan Ekstrakurikuler
     */
    public static function deletePresensi() {
        self::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra');
            exit;
        }

        $id = intval($_POST['jurnal_id'] ?? 0);
        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');

        if ($id > 0) {
            $db_siakad = Database::connect('siakad');
            
            // Hapus foto jika ada
            $stmtOld = $db_siakad->prepare("SELECT dokumentasi FROM ekstra_jurnal WHERE id = ?");
            $stmtOld->execute([$id]);
            $doc = $stmtOld->fetchColumn();
            if ($doc) {
                $file = __DIR__ . '/../../public/uploads/ekstra_jurnal/' . $doc;
                if (file_exists($file)) @unlink($file);
            }

            $db_siakad->prepare("DELETE FROM ekstra_absen WHERE jurnal_id = ?")->execute([$id]);
            $db_siakad->prepare("DELETE FROM ekstra_jurnal WHERE id = ?")->execute([$id]);
        }

        redirect("/admin/absen_v2/ekstra?ekstra=" . urlencode($nama_ekstra) . "&msg=deleted");
        exit;
    }

    /**
     * Rekapitulasi Presensi Ekstrakurikuler (Matriks Anggota x Pertemuan)
     */
    public static function rekap() {
        self::checkAuth();
        self::ensureSchema();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        // 1. Ambil daftar cabang ekstra
        $ekstraList = $db_siakad->query("SELECT nama_ekstra FROM ekstra_program ORDER BY nama_ekstra ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $selected_ekstra = $_GET['ekstra'] ?? ($ekstraList[0] ?? '');
        $semester = $_GET['semester'] ?? $active_semester;
        $tahun_ajaran = $_GET['tahun_ajaran'] ?? $active_year_name;
        $bulan = $_GET['bulan'] ?? ''; // jika kosong, ambil 1 semester penuh

        // 2. Info cabang & pembina
        $stmtInfo = $db_siakad->prepare("SELECT ep.*, g.nama as nama_pembina, g.nip as nip_pembina 
                                         FROM ekstra_program ep 
                                         LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                         WHERE ep.nama_ekstra = ? LIMIT 1");
        $stmtInfo->execute([$selected_ekstra]);
        $ekstraDetail = $stmtInfo->fetch(\PDO::FETCH_ASSOC);

        // 3. Ambil seluruh pertemuan/jurnal pada periode terpilih
        $sqlJurnal = "SELECT id, tanggal, pertemuan_ke, materi 
                      FROM ekstra_jurnal 
                      WHERE nama_ekstra = :ekstra AND tahun_ajaran = :ta";
        $paramsJurnal = [':ekstra' => $selected_ekstra, ':ta' => $tahun_ajaran];

        if (!empty($semester) && $semester !== 'Semua') {
            $sqlJurnal .= " AND (semester = :sem OR semester IS NULL OR semester = '')";
            $paramsJurnal[':sem'] = $semester;
        }
        if (!empty($bulan)) {
            $sqlJurnal .= " AND MONTH(tanggal) = :bulan";
            $paramsJurnal[':bulan'] = intval($bulan);
        }

        $sqlJurnal .= " ORDER BY tanggal ASC, pertemuan_ke ASC, id ASC";
        $stmtJ = $db_siakad->prepare($sqlJurnal);
        $stmtJ->execute($paramsJurnal);
        $daftarPertemuan = $stmtJ->fetchAll(\PDO::FETCH_ASSOC);

        // 4. Ambil daftar anggota siswa
        $anggotaList = $db_siakad->query("SELECT ea.siswa_id, s.nama, s.nis, s.nisn, s.jenis_kelamin, 
                                                COALESCE(k_hist.nama_kelas, k.nama_kelas, '-') as nama_kelas 
                                         FROM ekstra_anggota ea 
                                         JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                         LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                             AND rks.tahun_ajaran_id IN (SELECT id FROM aula_db.tahun_ajaran WHERE name = '$tahun_ajaran')
                                         LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                         LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                                         WHERE ea.nama_ekstra = ".$db_siakad->quote($selected_ekstra)." 
                                         ORDER BY nama_kelas ASC, s.nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // 5. Ambil seluruh record absen untuk pertemuan-pertemuan tersebut
        $jurnalIds = array_column($daftarPertemuan, 'id');
        $matrixAbsen = []; // [siswa_id][jurnal_id] => 'H'|'S'|'I'|'A'
        if (!empty($jurnalIds)) {
            $inJurnal = implode(',', array_map('intval', $jurnalIds));
            $rowsAbs = $db_siakad->query("SELECT jurnal_id, siswa_id, status, status_kehadiran 
                                         FROM ekstra_absen 
                                         WHERE jurnal_id IN ($inJurnal)")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rowsAbs as $ra) {
                $st = strtoupper($ra['status'] ?? '');
                if (empty($st) || !in_array($st, ['H', 'S', 'I', 'A'])) {
                    if (stripos($ra['status_kehadiran'], 'sakit') !== false) $st = 'S';
                    elseif (stripos($ra['status_kehadiran'], 'izin') !== false) $st = 'I';
                    elseif (stripos($ra['status_kehadiran'], 'alpa') !== false) $st = 'A';
                    else $st = 'H';
                }
                $matrixAbsen[$ra['siswa_id']][$ra['jurnal_id']] = $st;
            }
        }

        // 6. Hitung rekap per siswa
        $totalPertemuan = count($daftarPertemuan);
        $rekapSiswa = [];
        foreach ($anggotaList as $ag) {
            $sid = $ag['siswa_id'];
            $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;
            $harian = [];
            foreach ($daftarPertemuan as $p) {
                $jid = $p['id'];
                $st = $matrixAbsen[$sid][$jid] ?? '-';
                $harian[$jid] = $st;
                if ($st === 'H') $hadir++;
                elseif ($st === 'S') $sakit++;
                elseif ($st === 'I') $izin++;
                elseif ($st === 'A') $alpa++;
            }
            $persen = $totalPertemuan > 0 ? round(($hadir / $totalPertemuan) * 100) : 0;
            $rekapSiswa[] = [
                'siswa_id' => $sid,
                'nama' => $ag['nama'],
                'nis' => $ag['nis'],
                'nisn' => $ag['nisn'],
                'nama_kelas' => $ag['nama_kelas'],
                'jenis_kelamin' => $ag['jenis_kelamin'],
                'harian' => $harian,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'total_pertemuan' => $totalPertemuan,
                'persentase' => $persen
            ];
        }

        $title = "Rekap Presensi Ekstrakurikuler - Absen V2";
        $initial = strtoupper(substr($_SESSION['nama'] ?? 'Admin', 0, 1));
        $nama = $_SESSION['nama'] ?? 'Administrator';

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/ekstra/rekap.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Cetak Laporan Rekapitulasi Presensi Ekstrakurikuler (A4 Landscape)
     */
    public static function cetakRekap() {
        self::checkAuth();
        self::ensureSchema();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        $selected_ekstra = $_GET['ekstra'] ?? '';
        $semester = $_GET['semester'] ?? $active_semester;
        $tahun_ajaran = $_GET['tahun_ajaran'] ?? $active_year_name;
        $bulan = $_GET['bulan'] ?? '';

        // Info cabang & pembina
        $stmtInfo = $db_siakad->prepare("SELECT ep.*, g.nama as nama_pembina, g.nip as nip_pembina 
                                         FROM ekstra_program ep 
                                         LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                         WHERE ep.nama_ekstra = ? LIMIT 1");
        $stmtInfo->execute([$selected_ekstra]);
        $ekstraDetail = $stmtInfo->fetch(\PDO::FETCH_ASSOC);

        // Pertemuan
        $sqlJurnal = "SELECT id, tanggal, pertemuan_ke, materi 
                      FROM ekstra_jurnal 
                      WHERE nama_ekstra = :ekstra AND tahun_ajaran = :ta";
        $paramsJurnal = [':ekstra' => $selected_ekstra, ':ta' => $tahun_ajaran];

        if (!empty($semester) && $semester !== 'Semua') {
            $sqlJurnal .= " AND (semester = :sem OR semester IS NULL OR semester = '')";
            $paramsJurnal[':sem'] = $semester;
        }
        if (!empty($bulan)) {
            $sqlJurnal .= " AND MONTH(tanggal) = :bulan";
            $paramsJurnal[':bulan'] = intval($bulan);
        }

        $sqlJurnal .= " ORDER BY tanggal ASC, pertemuan_ke ASC, id ASC";
        $stmtJ = $db_siakad->prepare($sqlJurnal);
        $stmtJ->execute($paramsJurnal);
        $daftarPertemuan = $stmtJ->fetchAll(\PDO::FETCH_ASSOC);

        // Anggota
        $anggotaList = $db_siakad->query("SELECT ea.siswa_id, s.nama, s.nis, s.nisn, s.jenis_kelamin, 
                                                COALESCE(k_hist.nama_kelas, k.nama_kelas, '-') as nama_kelas 
                                         FROM ekstra_anggota ea 
                                         JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                         LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                             AND rks.tahun_ajaran_id IN (SELECT id FROM aula_db.tahun_ajaran WHERE name = '$tahun_ajaran')
                                         LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                         LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                                         WHERE ea.nama_ekstra = ".$db_siakad->quote($selected_ekstra)." 
                                         ORDER BY nama_kelas ASC, s.nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // Matrix
        $jurnalIds = array_column($daftarPertemuan, 'id');
        $matrixAbsen = [];
        if (!empty($jurnalIds)) {
            $inJurnal = implode(',', array_map('intval', $jurnalIds));
            $rowsAbs = $db_siakad->query("SELECT jurnal_id, siswa_id, status, status_kehadiran 
                                         FROM ekstra_absen 
                                         WHERE jurnal_id IN ($inJurnal)")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rowsAbs as $ra) {
                $st = strtoupper($ra['status'] ?? '');
                if (empty($st) || !in_array($st, ['H', 'S', 'I', 'A'])) {
                    if (stripos($ra['status_kehadiran'], 'sakit') !== false) $st = 'S';
                    elseif (stripos($ra['status_kehadiran'], 'izin') !== false) $st = 'I';
                    elseif (stripos($ra['status_kehadiran'], 'alpa') !== false) $st = 'A';
                    else $st = 'H';
                }
                $matrixAbsen[$ra['siswa_id']][$ra['jurnal_id']] = $st;
            }
        }

        $totalPertemuan = count($daftarPertemuan);
        $rekapSiswa = [];
        foreach ($anggotaList as $ag) {
            $sid = $ag['siswa_id'];
            $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;
            $harian = [];
            foreach ($daftarPertemuan as $p) {
                $jid = $p['id'];
                $st = $matrixAbsen[$sid][$jid] ?? '-';
                $harian[$jid] = $st;
                if ($st === 'H') $hadir++;
                elseif ($st === 'S') $sakit++;
                elseif ($st === 'I') $izin++;
                elseif ($st === 'A') $alpa++;
            }
            $persen = $totalPertemuan > 0 ? round(($hadir / $totalPertemuan) * 100) : 0;
            $rekapSiswa[] = [
                'siswa_id' => $sid,
                'nama' => $ag['nama'],
                'nis' => $ag['nis'],
                'nisn' => $ag['nisn'],
                'nama_kelas' => $ag['nama_kelas'],
                'jenis_kelamin' => $ag['jenis_kelamin'],
                'harian' => $harian,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'total_pertemuan' => $totalPertemuan,
                'persentase' => $persen
            ];
        }

        // Info Kamad
        $kamadNama = "AHMAD RIFA'I, M.Pd";
        $kamadNip = "-";
        try {
            $km = $db_core->query("SELECT nama, nip FROM guru WHERE jabatan LIKE '%Kepala%' OR nama LIKE '%RIFA%' LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if ($km) {
                $kamadNama = $km['nama'];
                if (!empty($km['nip']) && $km['nip'] !== '-') $kamadNip = $km['nip'];
            }
        } catch (\Throwable $e) {}

        include __DIR__ . '/../../resources/views/admin/absen_v2/ekstra/cetak_rekap.php';
    }

    /**
     * Manajemen Cabang & Anggota Ekstrakurikuler
     */
    public static function anggota() {
        self::checkAuth();
        self::ensureSchema();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        // 1. Ambil semua cabang ekstra dengan total anggota
        $cabangList = $db_siakad->query("SELECT ep.*, g.nama as nama_pembina, 
                                                (SELECT COUNT(*) FROM ekstra_anggota ea WHERE ea.nama_ekstra = ep.nama_ekstra) as total_anggota,
                                                (SELECT COUNT(*) FROM ekstra_jurnal ej WHERE ej.nama_ekstra = ep.nama_ekstra) as total_pertemuan 
                                         FROM ekstra_program ep 
                                         LEFT JOIN aula_db.guru g ON ep.guru_id = g.id 
                                         ORDER BY ep.nama_ekstra ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $selected_ekstra = $_GET['ekstra'] ?? ($cabangList[0]['nama_ekstra'] ?? '');

        // 2. Daftar anggota dari cabang terpilih
        $anggotaList = $db_siakad->query("SELECT ea.*, s.nama, s.nis, s.nisn, s.jenis_kelamin, 
                                                COALESCE(k_hist.nama_kelas, k.nama_kelas, '-') as nama_kelas 
                                         FROM ekstra_anggota ea 
                                         JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                         LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                             AND rks.tahun_ajaran_id IN (SELECT id FROM aula_db.tahun_ajaran WHERE name = '$active_year_name')
                                         LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                         LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                                         WHERE ea.nama_ekstra = ".$db_siakad->quote($selected_ekstra)." 
                                         ORDER BY nama_kelas ASC, s.nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Daftar semua guru untuk pilihan pembina
        $guruList = $db_core->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // 4. Daftar kelas untuk modal tambah anggota
        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Data Anggota & Cabang Ekstra - Absen V2";
        $initial = strtoupper(substr($_SESSION['nama'] ?? 'Admin', 0, 1));
        $nama = $_SESSION['nama'] ?? 'Administrator';

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/ekstra/anggota.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Edit Cabang Ekstrakurikuler
     */
    public static function saveCabang() {
        self::checkAuth();
        self::ensureSchema();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra/anggota');
            exit;
        }

        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');
        $guru_id = intval($_POST['guru_id'] ?? 0);
        $jadwal_hari = trim($_POST['jadwal_hari'] ?? 'Sabtu');
        $jadwal_waktu = trim($_POST['jadwal_waktu'] ?? '14.00 - 16.00 WIB');
        $target_pencapaian = trim($_POST['target_pencapaian'] ?? '');

        if (empty($nama_ekstra)) {
            redirect('/admin/absen_v2/ekstra/anggota?msg=error_name');
            exit;
        }

        if ($id) {
            // Edit cabang lama
            $stmtOld = $db_siakad->prepare("SELECT nama_ekstra FROM ekstra_program WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldName = $stmtOld->fetchColumn();

            $stmtUpd = $db_siakad->prepare("UPDATE ekstra_program 
                                            SET nama_ekstra = ?, guru_id = ?, jadwal_hari = ?, jadwal_waktu = ?, target_pencapaian = ? 
                                            WHERE id = ?");
            $stmtUpd->execute([$nama_ekstra, $guru_id, $jadwal_hari, $jadwal_waktu, $target_pencapaian, $id]);

            // Jika nama berubah, update di tabel terkait
            if ($oldName && $oldName !== $nama_ekstra) {
                $db_siakad->prepare("UPDATE ekstra_anggota SET nama_ekstra = ? WHERE nama_ekstra = ?")->execute([$nama_ekstra, $oldName]);
                $db_siakad->prepare("UPDATE ekstra_jurnal SET nama_ekstra = ? WHERE nama_ekstra = ?")->execute([$nama_ekstra, $oldName]);
            }
        } else {
            // Tambah baru
            $stmtIns = $db_siakad->prepare("INSERT INTO ekstra_program 
                                            (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, target_pencapaian, status) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, 'Aktif')");
            $stmtIns->execute([$nama_ekstra, $guru_id, $active_year_name, $active_semester, $jadwal_hari, $jadwal_waktu, $target_pencapaian]);
        }

        // Sinkronisasi dengan guru_tugas di core_db
        if ($guru_id > 0) {
            try {
                $stmtCek = $db_core->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = ? AND tahun_ajaran = ?");
                $stmtCek->execute([$guru_id, $nama_ekstra, $active_year_name]);
                if (!$stmtCek->fetch()) {
                    $stmtGT = $db_core->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Pembina Ekstrakurikuler', ?, ?)");
                    $stmtGT->execute([$guru_id, $nama_ekstra, $active_year_name]);
                }
            } catch (\Throwable $e) {}
        }

        redirect("/admin/absen_v2/ekstra/anggota?ekstra=" . urlencode($nama_ekstra) . "&msg=cabang_saved");
        exit;
    }

    /**
     * Hapus Cabang Ekstrakurikuler
     */
    public static function deleteCabang() {
        self::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra/anggota');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $db_siakad = Database::connect('siakad');
            $stmtOld = $db_siakad->prepare("SELECT nama_ekstra FROM ekstra_program WHERE id = ?");
            $stmtOld->execute([$id]);
            $nama_ekstra = $stmtOld->fetchColumn();

            if ($nama_ekstra) {
                // Hapus jurnal & absensi terkait
                $jurnals = $db_siakad->query("SELECT id FROM ekstra_jurnal WHERE nama_ekstra = " . $db_siakad->quote($nama_ekstra))->fetchAll(\PDO::FETCH_COLUMN);
                if (!empty($jurnals)) {
                    $inJ = implode(',', array_map('intval', $jurnals));
                    $db_siakad->exec("DELETE FROM ekstra_absen WHERE jurnal_id IN ($inJ)");
                    $db_siakad->exec("DELETE FROM ekstra_jurnal WHERE id IN ($inJ)");
                }
                // Hapus anggota
                $db_siakad->prepare("DELETE FROM ekstra_anggota WHERE nama_ekstra = ?")->execute([$nama_ekstra]);
                // Hapus program
                $db_siakad->prepare("DELETE FROM ekstra_program WHERE id = ?")->execute([$id]);
            }
        }

        redirect("/admin/absen_v2/ekstra/anggota?msg=cabang_deleted");
        exit;
    }

    /**
     * Tambah Anggota Siswa ke Cabang Ekstra (Multi-select)
     */
    public static function addAnggota() {
        self::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra/anggota');
            exit;
        }

        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');
        $siswa_ids = $_POST['siswa_ids'] ?? [];

        if (!empty($nama_ekstra) && !empty($siswa_ids) && is_array($siswa_ids)) {
            $stmtIns = $db_siakad->prepare("INSERT INTO ekstra_anggota (nama_ekstra, siswa_id, tahun_ajaran, status) VALUES (?, ?, ?, 'Aktif')");
            foreach ($siswa_ids as $sid) {
                $sid = intval($sid);
                // Cek apakah sudah terdaftar
                $stmtCek = $db_siakad->prepare("SELECT id FROM ekstra_anggota WHERE nama_ekstra = ? AND siswa_id = ?");
                $stmtCek->execute([$nama_ekstra, $sid]);
                if (!$stmtCek->fetch()) {
                    $stmtIns->execute([$nama_ekstra, $sid, $active_year_name]);
                }
            }
        }

        redirect("/admin/absen_v2/ekstra/anggota?ekstra=" . urlencode($nama_ekstra) . "&msg=anggota_added");
        exit;
    }

    /**
     * Hapus Anggota Siswa dari Cabang Ekstra
     */
    public static function deleteAnggota() {
        self::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/absen_v2/ekstra/anggota');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');

        if ($id > 0) {
            $db_siakad = Database::connect('siakad');
            $db_siakad->prepare("DELETE FROM ekstra_anggota WHERE id = ?")->execute([$id]);
        }

        redirect("/admin/absen_v2/ekstra/anggota?ekstra=" . urlencode($nama_ekstra) . "&msg=anggota_deleted");
        exit;
    }

    /**
     * API JSON: Mengambil daftar siswa berdasarkan Kelas yang belum bergabung di ekstra ini
     */
    public static function apiGetSiswaByKelas() {
        self::checkAuth();
        header('Content-Type: application/json');

        $kelas_id = intval($_GET['kelas_id'] ?? 0);
        $nama_ekstra = trim($_GET['ekstra'] ?? '');

        if ($kelas_id <= 0 || empty($nama_ekstra)) {
            echo json_encode(['success' => false, 'data' => []]);
            exit;
        }

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        list($active_year_name, $active_semester) = self::getActiveYearAndSemester();

        // Ambil siswa_id yang sudah bergabung di ekstra ini
        $stmtJoined = $db_siakad->prepare("SELECT siswa_id FROM ekstra_anggota WHERE nama_ekstra = ?");
        $stmtJoined->execute([$nama_ekstra]);
        $joinedIds = $stmtJoined->fetchAll(\PDO::FETCH_COLUMN);

        $notIn = "";
        if (!empty($joinedIds)) {
            $notIn = " AND s.id NOT IN (" . implode(',', array_map('intval', $joinedIds)) . ")";
        }

        $sql = "SELECT s.id, s.nama, s.nis, s.nisn, s.jenis_kelamin 
                FROM siswa s 
                LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name') 
                WHERE s.status = 'Aktif' 
                  AND COALESCE(rks.kelas_id, s.kelas_id) = :kelas_id 
                  $notIn 
                ORDER BY s.nama ASC";

        $stmt = $db_core->prepare($sql);
        $stmt->execute([':kelas_id' => $kelas_id]);
        $siswaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $siswaList]);
        exit;
    }
}
