<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PengawasPkkmController {

    private static function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Pengawas (role 5) atau Admin/Dev (role 1, 99) untuk testing/pratinjau
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [5, 1, 99])) {
            header('Location: /login');
            exit;
        }
    }

    public static function index() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        // Identifikasi Pengawas aktif
        $userId = $_SESSION['user_id'] ?? 0;
        $pengawas = null;
        try {
            $pengawas = $db->query("SELECT * FROM pkkm_pengawas WHERE user_id = $userId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Fallback jika login sebagai admin untuk tes tampilan
        if (!$pengawas) {
            try {
                $pengawas = $db->query("SELECT * FROM pkkm_pengawas ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}

            if (!$pengawas) {
                $pengawas = [
                    'id' => 1,
                    'nama' => 'H. AHMAD SYARIF, M.Pd.I',
                    'nip' => '197405122002121004',
                    'jabatan' => 'Pengawas Madrasah Ahli Madya',
                    'instansi' => 'Kantor Kementerian Agama Kab. Lampung Barat',
                    'peran' => 'penilai_1'
                ];
            }
        }

        $pengawasId = $pengawas['id'];

        // Ambil Data Institusi & Kamad
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil Komponen, Sub, dan Indikator
        $komponens = $db->query("SELECT * FROM pkkm_komponen ORDER BY CAST(urutan AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($komponens as &$k) {
            if (!isset($k['nama'])) {
                $k['nama'] = $k['nama_komponen'] ?? ('Komponen ' . ($k['urutan'] ?? ''));
            }
        }
        unset($k);

        $sub_komponens = $db->query("SELECT * FROM pkkm_sub_komponen ORDER BY CAST(SUBSTRING_INDEX(urutan, '.', 1) AS UNSIGNED), CAST(SUBSTRING_INDEX(urutan, '.', -1) AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil Skor dari Pengawas ini
        $skor_map = [];
        $catatan_map = [];
        try {
            $skors = $db->query("SELECT indikator_id, skor, catatan FROM pkkm_penilai_skor WHERE pengawas_id = $pengawasId")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($skors as $sk) {
                $skor_map[$sk['indikator_id']] = (int)$sk['skor'];
                $catatan_map[$sk['indikator_id']] = $sk['catatan'];
            }
        } catch (\Exception $e) {}

        // Fallback jika belum ada skor pengawas khusus, baca dari pkkm_nilai umum
        $fallback_skor = [];
        try {
            $fsk = $db->query("SELECT indikator_id, skor FROM pkkm_nilai")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fsk as $row) {
                $fallback_skor[$row['indikator_id']] = (int)$row['skor'];
            }
        } catch (\Exception $e) {}

        // Ambil Indikator
        $indikators = $db->query("SELECT * FROM pkkm_indikator ORDER BY komponen_id ASC, sub_komponen_id ASC, CAST(urutan AS UNSIGNED) ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Pastikan bukti_fisik selalu terisi dari dataset resmi jika di DB kosong
        $datasetMaster = null;
        foreach ($indikators as &$ind) {
            if (empty(trim($ind['bukti_fisik'] ?? ''))) {
                if ($datasetMaster === null) {
                    try {
                        $ds = \App\Services\PkkmSyncService::getDataset();
                        $datasetMaster = $ds['indikator'] ?? [];
                    } catch (\Exception $e) {
                        $datasetMaster = [];
                    }
                }
                foreach ($datasetMaster as $d) {
                    if (trim($d['indikator']) === trim($ind['indikator']) || 
                       (($ind['komponen_id'] ?? $ind['komp_id'] ?? 0) == $d['komp_id'] && ($ind['urutan'] ?? 0) == $d['urutan'])) {
                        $ind['bukti_fisik'] = $d['bukti_fisik'];
                        break;
                    }
                }
            }
        }
        unset($ind);

        $sub_komponen_map = [];
        foreach ($sub_komponens as $sk) {
            $sub_komponen_map[$sk['id']] = $sk;
        }

        // Ambil Berkas yang diunggah madrasah
        $berkas = [];
        $master_berkas = [];
        $master_files = [];
        try {
            $berkas = $db->query("SELECT * FROM pkkm_berkas")->fetchAll(PDO::FETCH_ASSOC);
            $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
            $master_files = $db->query("SELECT * FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $mfiles_map = [];
        foreach ($master_files as $mf) {
            $mfiles_map[$mf['master_berkas_id']][] = $mf;
        }

        $berkas_map = [];
        foreach ($berkas as $b) {
            $berkas_map[$b['indikator_id']][] = $b;
        }

        // Hitung Skor & Ketercapaian
        $totalIndikator = count($indikators);
        $totalDinilai = 0;
        $totalSkor = 0;

        foreach ($indikators as &$ind) {
            $iid = $ind['id'];
            if (!isset($ind['komp_id'])) {
                $ind['komp_id'] = $ind['komponen_id'] ?? 1;
            }
            $currentSkor = isset($skor_map[$iid]) ? $skor_map[$iid] : (isset($fallback_skor[$iid]) ? $fallback_skor[$iid] : 0);
            $ind['skor_penilai'] = $currentSkor;
            $ind['catatan_penilai'] = $catatan_map[$iid] ?? '';
            
            // Berkas langsung dari pkkm_berkas
            $ind['berkas_list'] = $berkas_map[$iid] ?? [];

            // Sinkronkan berkas dari Daftar Berkas PKKM (pkkm_master_berkas_file)
            // Persis 100% sama dengan AdminPkkmController agar tidak ada berkas nyasar/dobel
            $buktiFisikInd = $ind['bukti_fisik'] ?? '';
            foreach ($master_berkas as $mb) {
                $mbNama = trim($mb['nama_berkas'] ?? '');
                if ($mbNama === '') continue;

                if (stripos($buktiFisikInd, $mbNama) !== false) {
                    if (isset($mfiles_map[$mb['id']])) {
                        foreach ($mfiles_map[$mb['id']] as $mf) {
                            $ind['berkas_list'][] = [
                                'id' => 'master_' . $mf['id'],
                                'indikator_id' => $iid,
                                'tipe' => $mf['tipe'] ?? 'file',
                                'url' => $mf['url'],
                                'file_path' => $mf['url'],
                                'nama_file' => $mf['nama_file_asli'] ?: $mbNama,
                                'is_master' => true
                            ];
                        }
                    }
                }
            }

            // Hapus duplikasi file (berdasarkan URL/file_path dan nama_file)
            $uniqueList = [];
            $seen = [];
            foreach ($ind['berkas_list'] as $bk) {
                $uKey = trim($bk['url'] ?? $bk['file_path'] ?? '') . '::' . trim($bk['nama_file'] ?? '');
                if (!isset($seen[$uKey])) {
                    $seen[$uKey] = true;
                    $uniqueList[] = $bk;
                }
            }
            $ind['berkas_list'] = $uniqueList;

            // Kode Juknis Hierarkis (misal 1.1.1, 1.1.2)
            $subKomp = $sub_komponen_map[$ind['sub_komponen_id'] ?? 0] ?? null;
            $subUrutan = $subKomp['urutan'] ?? '';
            $indUrutan = $ind['urutan'] ?? '1';
            $indKode = !empty($subUrutan) ? ($subUrutan . '.' . $indUrutan) : ($ind['komp_id'] . '.' . $indUrutan);
            $ind['kode_lengkap'] = $indKode;
            $ind['sub_urutan'] = $subUrutan;
            $ind['sub_nama'] = $subKomp['nama_sub'] ?? ($subKomp['sub_komponen'] ?? '');

            if ($currentSkor > 0) {
                $totalDinilai++;
                $totalSkor += $currentSkor;
            }
        }
        unset($ind);

        $maxSkor = $totalIndikator * 4;
        $persentase = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 1) : 0;
        
        $predikat = 'Kurang';
        $badgeClass = 'bg-danger';
        if ($persentase >= 91) {
            $predikat = 'Amat Baik (A)';
            $badgeClass = 'bg-success';
        } elseif ($persentase >= 76) {
            $predikat = 'Baik (B)';
            $badgeClass = 'bg-primary';
        } elseif ($persentase >= 61) {
            $predikat = 'Cukup (C)';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($persentase >= 51) {
            $predikat = 'Sedang (D)';
            $badgeClass = 'bg-secondary';
        }

        // Statistik per komponen untuk Dashboard
        $komponen_stats = [];
        foreach ($komponens as $k) {
            $kid = $k['id'];
            $kIndikators = array_filter($indikators, function($ind) use ($kid) {
                return ($ind['komp_id'] ?? $ind['komponen_id'] ?? 0) == $kid;
            });
            $kTotal = count($kIndikators);
            $kDinilai = 0;
            $kSkor = 0;
            foreach ($kIndikators as $kind) {
                if (($kind['skor_penilai'] ?? 0) > 0) {
                    $kDinilai++;
                    $kSkor += $kind['skor_penilai'];
                }
            }
            $kMax = $kTotal * 4;
            $kPersen = $kMax > 0 ? round(($kSkor / $kMax) * 100, 1) : 0;
            $komponen_stats[$kid] = [
                'id' => $kid,
                'nama' => $k['nama'],
                'urutan' => $k['urutan'] ?? $kid,
                'total' => $kTotal,
                'dinilai' => $kDinilai,
                'skor' => $kSkor,
                'max' => $kMax,
                'persen' => $kPersen
            ];
        }

        // Tim Pengawas (Penilai 1 & 2)
        $tim_penilai = [];
        try {
            $tim_penilai = $db->query("SELECT * FROM pkkm_pengawas ORDER BY peran ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['dashboard', 'penilaian', 'ttd']) ? $_GET['tab'] : 'dashboard';

        include __DIR__ . '/../../resources/views/pengawas/pkkm_portal.php';
    }

    public static function saveNilai() {
        self::checkAuth();
        $db = Database::connect('core');

        $userId = $_SESSION['user_id'] ?? 0;
        $pengawasId = 1;
        try {
            $p = $db->query("SELECT id FROM pkkm_pengawas WHERE user_id = $userId LIMIT 1")->fetch();
            if ($p) $pengawasId = (int)$p['id'];
        } catch (\Exception $e) {}

        $indikator_id = isset($_POST['indikator_id']) ? (int)$_POST['indikator_id'] : 0;
        $skor = isset($_POST['skor']) ? (int)$_POST['skor'] : 0;
        $catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : null;

        if ($indikator_id > 0 && $skor >= 1 && $skor <= 4) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO pkkm_penilai_skor (pengawas_id, indikator_id, skor, catatan)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE skor = VALUES(skor), catatan = VALUES(catatan)
                ");
                $stmt->execute([$pengawasId, $indikator_id, $skor, $catatan]);

                // Update juga nilai pkkm_nilai umum agar sinkron
                $stmtNilai = $db->prepare("
                    INSERT INTO pkkm_nilai (indikator_id, skor)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE skor = VALUES(skor)
                ");
                $stmtNilai->execute([$indikator_id, $skor]);

                // Hitung kembali total dinilai & total skor
                $stats = $db->query("
                    SELECT COUNT(*) as dinilai, SUM(skor) as total 
                    FROM pkkm_penilai_skor 
                    WHERE pengawas_id = $pengawasId AND skor > 0
                ")->fetch(PDO::FETCH_ASSOC);

                $totalDinilai = (int)($stats['dinilai'] ?? 0);
                $totalSkor = (int)($stats['total'] ?? 0);
                $persentase = round(($totalSkor / (42 * 4)) * 100, 1);

                echo json_encode([
                    'status' => 1,
                    'pesan' => 'Skor ' . $skor . ' berhasil disimpan',
                    'total_dinilai' => $totalDinilai,
                    'total_skor' => $totalSkor,
                    'persentase' => $persentase
                ]);
                exit;
            } catch (\Exception $e) {
                echo json_encode(['status' => 0, 'pesan' => $e->getMessage()]);
                exit;
            }
        }

        echo json_encode(['status' => 0, 'pesan' => 'Data penilaian tidak valid']);
        exit;
    }

    public static function resetNilai() {
        self::checkAuth();
        $db = Database::connect('core');

        $userId = $_SESSION['user_id'] ?? 0;
        $pengawasId = 1;
        try {
            $p = $db->query("SELECT id FROM pkkm_pengawas WHERE user_id = $userId LIMIT 1")->fetch();
            if ($p) $pengawasId = (int)$p['id'];
        } catch (\Exception $e) {}

        try {
            // Kosongkan penilaian dari pengawas ini dan tabel nilai umum
            $resetAll = isset($_POST['reset_all']) && $_POST['reset_all'] == '1';
            if ($resetAll) {
                $db->exec("DELETE FROM pkkm_penilai_skor");
            } else {
                $db->exec("DELETE FROM pkkm_penilai_skor WHERE pengawas_id = $pengawasId");
            }
            $db->exec("DELETE FROM pkkm_nilai");

            echo json_encode([
                'status' => 1,
                'pesan' => 'Semua skor penilaian berhasil direset kembali ke 0.'
            ]);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'pesan' => $e->getMessage()]);
            exit;
        }
    }

    public static function saveTtd() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        $userId = $_SESSION['user_id'] ?? 0;
        $pengawas = null;
        try {
            $pengawas = $db->query("SELECT * FROM pkkm_pengawas WHERE user_id = $userId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (!$pengawas) {
            try {
                $pengawas = $db->query("SELECT * FROM pkkm_pengawas ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        if (!$pengawas) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1')) {
                echo json_encode(['status' => 0, 'pesan' => 'Data pengawas tidak ditemukan']);
                exit;
            }
            header('Location: /pengawas/pkkm?tab=ttd&msg=not_found');
            exit;
        }

        $pengawasId = (int)$pengawas['id'];
        $nama = trim($_POST['nama'] ?? $pengawas['nama']);
        $nip = trim($_POST['nip'] ?? $pengawas['nip']);
        $jabatan = trim($_POST['jabatan'] ?? $pengawas['jabatan']);
        $ttd_url = $pengawas['ttd_digital'] ?? null;

        $uploadDir = __DIR__ . '/../../public/uploads/pkkm/ttd';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        // 1. Cek Data Canvas Tanda Tangan (Base64)
        if (!empty($_POST['signature_data'])) {
            $dataUrl = $_POST['signature_data'];
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $filename = 'ttd_' . $pengawasId . '_' . time() . '.png';
                    $filepath = $uploadDir . '/' . $filename;
                    if (file_put_contents($filepath, $decoded)) {
                        $ttd_url = '/uploads/pkkm/ttd/' . $filename;
                    }
                }
            }
        }

        // 2. Cek Upload File Gambar (PNG / JPG)
        if (isset($_FILES['file_ttd']) && $_FILES['file_ttd']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['file_ttd']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                $filename = 'ttd_upload_' . $pengawasId . '_' . time() . '.' . $ext;
                $filepath = $uploadDir . '/' . $filename;
                if (move_uploaded_file($_FILES['file_ttd']['tmp_name'], $filepath)) {
                    $ttd_url = '/uploads/pkkm/ttd/' . $filename;
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE pkkm_pengawas SET nama = ?, nip = ?, jabatan = ?, ttd_digital = ? WHERE id = ?");
            $stmt->execute([$nama, $nip, $jabatan, $ttd_url, $pengawasId]);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1')) {
                echo json_encode([
                    'status' => 1,
                    'pesan' => 'Tanda tangan digital dan identitas pengawas berhasil disimpan!',
                    'ttd_url' => $ttd_url
                ]);
                exit;
            }

            header('Location: /pengawas/pkkm?tab=ttd&msg=saved');
            exit;
        } catch (\Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1')) {
                echo json_encode(['status' => 0, 'pesan' => $e->getMessage()]);
                exit;
            }
            header('Location: /pengawas/pkkm?tab=ttd&msg=error');
            exit;
        }
    }

    public static function cetakRekap() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        $userId = $_SESSION['user_id'] ?? 0;
        $pengawasAktif = null;
        try {
            $pengawasAktif = $db->query("SELECT * FROM pkkm_pengawas WHERE user_id = $userId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $p1 = null;
        $p2 = null;
        try {
            $p1 = $db->query("SELECT * FROM pkkm_pengawas WHERE peran = 'penilai_1' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $p2 = $db->query("SELECT * FROM pkkm_pengawas WHERE peran = 'penilai_2' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (!$p1) {
            $p1 = $pengawasAktif ?: [
                'nama' => 'H. AHMAD SYARIF, M.Pd.I',
                'nip' => '197405122002121004',
                'jabatan' => 'Pengawas Madrasah Ahli Madya',
                'peran' => 'penilai_1',
                'ttd_digital' => null
            ];
        }

        if (!$p2) {
            $p2 = [
                'nama' => 'DRA. HJ. SITI NURHAYATI, M.Pd',
                'nip' => '197803152005012003',
                'jabatan' => 'Pengawas Madrasah Ahli Muda',
                'peran' => 'penilai_2',
                'ttd_digital' => null
            ];
        }

        $pengawas = $p1;
        $pengawas2 = $p2;

        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $pengawasId = (int)($pengawas['id'] ?? 1);
        // Hitung nilai per komponen
        $komponens = $db->query("SELECT * FROM pkkm_komponen ORDER BY CAST(urutan AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($komponens as &$k) {
            $kid = $k['id'];
            if (!isset($k['nama'])) {
                $k['nama'] = $k['nama_komponen'] ?? ('Komponen ' . ($k['urutan'] ?? ''));
            }
            $res = $db->query("
                SELECT COUNT(i.id) as total_ind, 
                       SUM(COALESCE(s.skor, COALESCE(n.skor, 0))) as skor_perolehan
                FROM pkkm_indikator i
                LEFT JOIN pkkm_penilai_skor s ON s.indikator_id = i.id AND s.pengawas_id = $pengawasId
                LEFT JOIN pkkm_nilai n ON n.indikator_id = i.id
                WHERE i.komponen_id = $kid
            ")->fetch(PDO::FETCH_ASSOC);

            $k['total_ind'] = (int)($res['total_ind'] ?? 0);
            $k['skor_perolehan'] = (int)($res['skor_perolehan'] ?? 0);
            $k['skor_maks'] = $k['total_ind'] * 4;
            $k['nilai_komponen'] = $k['skor_maks'] > 0 ? round(($k['skor_perolehan'] / $k['skor_maks']) * 100, 2) : 0;
        }
        unset($k);

        include __DIR__ . '/../../resources/views/pengawas/pkkm_rekap_nilai_pdf.php';
    }

    private static function ensurePengawasTables($db) {
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE
            )");
            $db->exec("INSERT IGNORE INTO roles (id, name) VALUES (5, 'pengawas')");

            $db->exec("CREATE TABLE IF NOT EXISTS pkkm_pengawas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                nama VARCHAR(150) NOT NULL,
                nip VARCHAR(50) DEFAULT '-',
                jabatan VARCHAR(100) DEFAULT 'Pengawas Madrasah',
                instansi VARCHAR(150) DEFAULT 'Kantor Kementerian Agama Kab. Lampung Barat',
                peran ENUM('penilai_1', 'penilai_2') DEFAULT 'penilai_1',
                no_hp VARCHAR(30) NULL,
                ttd_digital TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Pastikan kolom ttd_digital ada jika tabel sudah pernah dibuat sebelumnya
            try {
                $db->exec("ALTER TABLE pkkm_pengawas ADD COLUMN ttd_digital TEXT NULL");
            } catch (\Exception $exCol) {}

            $db->exec("CREATE TABLE IF NOT EXISTS pkkm_penilai_skor (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pengawas_id INT NOT NULL,
                indikator_id INT NOT NULL,
                skor INT DEFAULT 0,
                catatan TEXT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_pengawas_indikator (pengawas_id, indikator_id)
            )");

            $db->exec("CREATE TABLE IF NOT EXISTS pkkm_nilai (
                indikator_id INT PRIMARY KEY,
                skor INT DEFAULT 0
            )");
        } catch (\Exception $e) {}
    }
}
