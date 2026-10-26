<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AcademicYear;

class AdminSiakadJadwalController {

    public static function index() {
        $db = Database::connect('siakad');

        // Ambil semua kelas
        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::current();
        $pengaturan = $db->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
        $semester_raw = $pengaturan['semester_aktif'] ?? 'Ganjil';
        $semester = (strtolower(trim($semester_raw)) === 'genap' || $semester_raw === '2') ? '2' : '1';

        // Ambil semua jadwal
        $stmt_jadwal = $db->prepare("
            SELECT jp.*, m.nama_mapel, m.kode_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
            JOIN aula_db.guru g ON jp.guru_id = g.id
            WHERE jp.tahun_ajaran_id = ? AND jp.semester = ?
            ORDER BY jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$activeYear['id'], $semester]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        // Susun jadwal: $jadwal_master[hari][jam_mulai][kelas_id] = [data...]
        $jadwal_master = [];
        $jam_list = [];
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        foreach ($jadwal_raw as $j) {
            $h = $j['hari'];
            if ($h == "Jum'at" || $h == 'Jum`at') $h = 'Jumat'; // Normalize
            $jam = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
            $jadwal_master[$h][$jam][$j['kelas_id']] = $j;
            
            if (!in_array($jam, $jam_list)) {
                $jam_list[] = $jam;
            }
        }
        
        // Sort jam_list
        sort($jam_list);

        ob_start();
        require_once __DIR__ . '/../../resources/views/siakad/admin/jadwal/index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function edit($id) {
        $db = Database::connect('siakad');

        $activeYear = AcademicYear::current();
        $pengaturan = $db->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
        $semester_raw = $pengaturan['semester_aktif'] ?? 'Ganjil';
        $semester = (strtolower(trim($semester_raw)) === 'genap' || $semester_raw === '2') ? '2' : '1';

        $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$id]);
        $kelas = $stmt->fetch();

        if (!$kelas) {
            redirect('/siakad/jadwal');
            exit;
        }

        $stmt_jadwal = $db->prepare("
            SELECT jp.*, m.nama_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
            JOIN aula_db.guru g ON jp.guru_id = g.id
            WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
            ORDER BY jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$id, $activeYear['id'], $semester]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        // Prepare $jadwal with time slot as key
        $jadwal = [];
        foreach ($jadwal_raw as $j) {
            $jam_mulai = substr($j['jam_mulai'], 0, 5);
            $jadwal[$j['hari']][$jam_mulai] = $j;
        }

        $mapel = $db->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel ASC")->fetchAll();
        $guru = $db->query("SELECT id, nama FROM aula_db.guru ORDER BY nama ASC")->fetchAll();
        
        // Fetch Mengajar rules for this class
        $stmt_mengajar = $db->prepare("
            SELECT m.mapel_id, m.guru_id, g.nama as nama_guru 
            FROM mengajar m
            JOIN aula_db.guru g ON m.guru_id = g.id
            WHERE m.kelas_id = ? AND m.tahun_ajaran_id = ? AND m.semester = ?
        ");
        $stmt_mengajar->execute([$id, $activeYear['id'], $semester]);
        $mengajar_raw = $stmt_mengajar->fetchAll();
        $mengajar_map = [];
        foreach($mengajar_raw as $m) {
            $mengajar_map[$m['mapel_id']] = [
                'guru_id' => $m['guru_id'],
                'nama_guru' => $m['nama_guru']
            ];
        }

        ob_start();
        require_once __DIR__ . '/../../resources/views/siakad/admin/jadwal/edit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function save($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hari = $_POST['hari'];
            $jam_mulai = $_POST['jam_mulai'];
            $jam_selesai = $_POST['jam_selesai'];
            $mapel_id = $_POST['mata_pelajaran_id'];
            $guru_id = $_POST['guru_id'];

            $db = Database::connect('siakad');
            
            $activeYear = AcademicYear::current();
            $pengaturan = $db->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
            $semester_raw = $pengaturan['semester_aktif'] ?? 'Ganjil';
            $semester_jadwal = $semester_raw;
            $semester_mengajar = (strtolower(trim($semester_raw)) === 'genap' || $semester_raw === '2') ? '2' : '1';

            $stmt = $db->prepare("INSERT INTO jadwal_pelajaran (tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai, mata_pelajaran_id, guru_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$activeYear['id'], $semester_jadwal, $id, $hari, $jam_mulai, $jam_selesai, $mapel_id, $guru_id]);

            $sp = strpos($_SERVER['REQUEST_URI'], '/admin/siakad') !== false ? '/admin/siakad' : '/siakad';
            header('Location: ' . $sp . '/jadwal/edit/' . $id . '?msg=saved');
            exit;
        }
    }

    public static function delete($id) {
        $db = Database::connect('siakad');

        // Ambil kelas_id untuk redirect kembali
        $stmt = $db->prepare("SELECT kelas_id FROM jadwal_pelajaran WHERE id = ?");
        $stmt->execute([$id]);
        $jadwal = $stmt->fetch();

        $sp = strpos($_SERVER['REQUEST_URI'], '/admin/siakad') !== false ? '/admin/siakad' : '/siakad';

        if ($jadwal) {
            $del = $db->prepare("DELETE FROM jadwal_pelajaran WHERE id = ?");
            $del->execute([$id]);
            header('Location: ' . $sp . '/jadwal/edit/' . $jadwal['kelas_id'] . '?msg=deleted');
            exit;
        }

        header('Location: ' . $sp . '/jadwal');
        exit;
    }

    public static function import($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            
            $db = \App\Core\Database::connect('siakad');
            $activeYear = \App\Core\AcademicYear::current();
            $pengaturan = $db->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
            $semester_raw = $pengaturan['semester_aktif'] ?? 'Ganjil';
            $semester_jadwal = $semester_raw;
            $semester_mengajar = (strtolower(trim($semester_raw)) === 'genap' || $semester_raw === '2') ? '2' : '1';

            // Pre-load mapel & guru to match text from excel
            $allMapel = $db->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            $mapelMap = [];
            foreach ($allMapel as $m) {
                $mapelMap[strtolower(trim($m['nama_mapel']))] = $m['id'];
            }

            $allGuru = $db->query("SELECT id, nama FROM aula_db.guru")->fetchAll();
            $guruMap = [];
            foreach ($allGuru as $g) {
                $guruMap[strtolower(trim($g['nama']))] = $g['id'];
            }

            $success = 0;
            foreach ($lines as $line) {
                $cols = explode("\t", trim($line));
                if (count($cols) < 5) continue; // Format: Hari, Jam Mulai, Jam Selesai, Mapel, Guru
                
                $hari = trim($cols[0]);
                $jam_mulai = trim($cols[1]);
                $jam_selesai = trim($cols[2]);
                $mapel_str = strtolower(trim($cols[3]));
                $guru_str = strtolower(trim($cols[4]));
                
                if (empty($hari) || empty($mapel_str)) continue;

                // Fuzzy match Mapel
                $mapel_id = null;
                foreach($mapelMap as $name => $m_id) {
                    if (strpos($mapel_str, $name) !== false || strpos($name, $mapel_str) !== false) {
                        $mapel_id = $m_id; break;
                    }
                }

                // Fuzzy match Guru
                $guru_id = null;
                foreach($guruMap as $name => $g_id) {
                    // Extract first names for better match if full name not matched
                    $name_clean = preg_replace('/\,.*/', '', $name); // remove title e.g. S.Pd
                    $guru_clean = preg_replace('/\,.*/', '', $guru_str);
                    if (strpos($guru_clean, $name_clean) !== false || strpos($name_clean, $guru_clean) !== false) {
                        $guru_id = $g_id; break;
                    }
                }

                // Only insert if both matched to avoid errors
                if ($mapel_id && $guru_id) {
                    // Format time just in case it's not strictly H:i
                    if(strpos($jam_mulai, ':') === false && strlen($jam_mulai) == 4) $jam_mulai = substr($jam_mulai,0,2).':'.substr($jam_mulai,2,2);
                    if(strpos($jam_selesai, ':') === false && strlen($jam_selesai) == 4) $jam_selesai = substr($jam_selesai,0,2).':'.substr($jam_selesai,2,2);
                    
                    $stmt = $db->prepare("INSERT INTO jadwal_pelajaran (tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai, mata_pelajaran_id, guru_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$activeYear['id'], $semester_jadwal, $id, $hari, $jam_mulai, $jam_selesai, $mapel_id, $guru_id]);
                    $success++;
                }
            }
            
            $sp = strpos($_SERVER['REQUEST_URI'], '/admin/siakad') !== false ? '/admin/siakad' : '/siakad';
            header('Location: ' . $sp . '/jadwal/edit/' . $id . '?msg=imported&count=' . $success);
            exit;
        }
    }

    public static function saveInline($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            $hari = $_POST['hari'] ?? '';
            $jam_mulai = $_POST['jam_mulai'] ?? '';
            $jam_selesai = $_POST['jam_selesai'] ?? '';
            $mapel_id = $_POST['mapel_id'] ?? '';
            $guru_id = $_POST['guru_id'] ?? '';
            $jadwal_id = $_POST['jadwal_id'] ?? ''; // if exists

            if (!$hari || !$jam_mulai || !$jam_selesai) {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
                exit;
            }

            $db = Database::connect('siakad');
            
            if (empty($mapel_id) || empty($guru_id)) {
                // If mapel is empty, it means they want to delete this slot
                if ($jadwal_id) {
                    $stmt = $db->prepare("DELETE FROM jadwal_pelajaran WHERE id = ?");
                    $stmt->execute([$jadwal_id]);
                    echo json_encode(['status' => 'success', 'message' => 'Jadwal dikosongkan', 'jadwal_id' => null]);
                } else {
                    echo json_encode(['status' => 'success', 'message' => 'Tidak ada perubahan', 'jadwal_id' => null]);
                }
                exit;
            }

            $activeYear = AcademicYear::current();
            $pengaturan = $db->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
            $semester_raw = $pengaturan['semester_aktif'] ?? 'Ganjil';
            $semester = (strtolower(trim($semester_raw)) === 'genap' || $semester_raw === '2') ? '2' : '1';

            if ($jadwal_id) {
                if (empty($mapel_id)) {
                    $stmt = $db->prepare("DELETE FROM jadwal_pelajaran WHERE id = ?");
                    $stmt->execute([$jadwal_id]);
                    echo json_encode(['status' => 'success', 'message' => 'Jadwal dikosongkan', 'jadwal_id' => null]);
                    exit;
                }
                $stmt = $db->prepare("UPDATE jadwal_pelajaran SET mata_pelajaran_id = ?, guru_id = ? WHERE id = ?");
                $stmt->execute([$mapel_id, $guru_id, $jadwal_id]);
                echo json_encode(['status' => 'success', 'message' => 'Jadwal diperbarui', 'jadwal_id' => $jadwal_id]);
            } else {
                if (empty($mapel_id)) {
                     echo json_encode(['status' => 'success', 'message' => 'Tidak ada perubahan', 'jadwal_id' => null]);
                     exit;
                }
                $stmt = $db->prepare("INSERT INTO jadwal_pelajaran (tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai, mata_pelajaran_id, guru_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$activeYear['id'], $semester, $id, $hari, $jam_mulai, $jam_selesai, $mapel_id, $guru_id]);
                echo json_encode(['status' => 'success', 'message' => 'Jadwal disimpan', 'jadwal_id' => $db->lastInsertId()]);
            }
            exit;
        }
    }
}
