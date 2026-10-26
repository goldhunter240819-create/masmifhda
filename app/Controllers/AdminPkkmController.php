<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class AdminPkkmController {
    
    // Mencegah akses selain admin/kepsek (role 1 atau 99)
    private static function checkAuth() {
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /');
            exit;
        }
    }

    // Khusus lembar bukti fisik cetak PKKM: izinkan Guru (role 2), Pengawas (role 5), dan Admin (role 1, 99)
    private static function checkAuthBukti() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2, 5, 99])) {
            header('Location: /login');
            exit;
        }
    }

    private static function compressAndUploadImage($tmp, $dest, $ext) {
        $allowedImage = ['jpg', 'jpeg', 'png'];
        
        // Skip GD processing if file is small (< 500KB)
        if (filesize($tmp) < 512000) {
            return move_uploaded_file($tmp, $dest);
        }

        if (in_array(strtolower($ext), $allowedImage)) {
            $info = @getimagesize($tmp);
            if ($info) {
                // Hanya kompresi JPEG. PNG sangat berat diproses GD dan rawan tambah besar ukurannya
                if ($info['mime'] == 'image/jpeg') {
                    $image = @imagecreatefromjpeg($tmp);
                    if ($image) {
                        // Simpan dengan kualitas 60%
                        imagejpeg($image, $dest, 60);
                        imagedestroy($image);
                        return true;
                    }
                }
            }
        }
        
        // Fallback untuk PNG atau jika getimagesize / create image gagal
        return move_uploaded_file($tmp, $dest);
    }

    private static function processUploadedFiles($filesArray, $tipe, $idOrIndikator, $isMaster, $nama_file_asli) {
        $db = Database::connect('core');
        $success = false;
        
        $fileCount = count($filesArray['name']);
        $imagesToMerge = [];
        $otherFiles = [];
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($filesArray['error'][$i] == UPLOAD_ERR_OK) {
                $tmp = $filesArray['tmp_name'][$i];
                $name = $filesArray['name'][$i];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $imagesToMerge[] = ['tmp' => $tmp, 'name' => $name, 'ext' => $ext];
                } elseif (in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx'])) {
                    $otherFiles[] = ['tmp' => $tmp, 'name' => $name, 'ext' => $ext];
                }
            }
        }
        
        // Handle images: Merge into PDF if > 1
        if (count($imagesToMerge) > 1) {
            $albumFilename = 'pkkm_' . time() . '_' . rand(100,999) . '_Album_Foto.pdf';
            $dest = __DIR__ . '/../../public/uploads/pkkm/' . $albumFilename;
            if (!is_dir(__DIR__ . '/../../public/uploads/pkkm')) mkdir(__DIR__ . '/../../public/uploads/pkkm', 0777, true);
            
            if (!class_exists('FPDF')) require_once (file_exists(__DIR__ . '/../../vendor/fpdf/fpdf.php') ? __DIR__ . '/../../vendor/fpdf/fpdf.php' : __DIR__ . '/../../app/Libraries/fpdf/fpdf.php');
            
            $pdf = new \FPDF();
            foreach ($imagesToMerge as $img) {
                $tmp = $img['tmp'];
                $ext = strtoupper($img['ext']);
                if ($ext === 'JPG') $ext = 'JPEG';
                
                $info = @getimagesize($tmp);
                if($info) {
                    $pdf->AddPage();
                    $w_px = $info[0]; $h_px = $info[1];
                    $ratio = min(190 / $w_px, 277 / $h_px);
                    
                    // Directly use the uploaded tmp file, bypassing the buggy temp rename
                    // We must pass the correct image type (JPEG or PNG) to FPDF
                    $pdf->Image($tmp, (210 - ($w_px * $ratio)) / 2, (297 - ($h_px * $ratio)) / 2, $w_px * $ratio, $h_px * $ratio, $ext);
                }
            }
            $pdf->Output('F', $dest);
            
            $url = '/uploads/pkkm/' . $albumFilename;
            $nfa = !empty($nama_file_asli) ? $nama_file_asli : 'Album Foto Dokumentasi';
            
            if ($isMaster) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
            } else {
                $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
            }
            $success = true;
        } elseif (count($imagesToMerge) == 1) {
            // Process as single image
            $otherFiles[] = $imagesToMerge[0]; 
        }
        
        // Handle other files (and single image)
        foreach ($otherFiles as $index => $file) {
            $filename = 'pkkm_' . time() . '_' . rand(100,999) . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file['name']);
            $dest = __DIR__ . '/../../public/uploads/pkkm/' . $filename;
            if (!is_dir(__DIR__ . '/../../public/uploads/pkkm')) mkdir(__DIR__ . '/../../public/uploads/pkkm', 0777, true);
            
            $isImg = in_array($file['ext'], ['jpg', 'jpeg', 'png']);
            $uploaded = false;
            
            if ($isImg) {
                $uploaded = self::compressAndUploadImage($file['tmp'], $dest, $file['ext']);
            } else {
                $uploaded = move_uploaded_file($file['tmp'], $dest);
            }
            
            if ($uploaded) {
                $url = '/uploads/pkkm/' . $filename;
                $nfa = !empty($nama_file_asli) ? $nama_file_asli : $file['name'];
                if (!empty($nama_file_asli) && count($otherFiles) > 1) {
                    $nfa .= " (" . ($index + 1) . ")";
                }
                
                if ($isMaster) {
                    $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
                } else {
                    $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
                }
                $success = true;
            }
        }
        
        return $success;
    }

    public static function dashboard() {
        self::checkAuth();
        $db = Database::connect('core');

        // Kalkulasi Statistik
        $totalKomponen = $db->query("SELECT COUNT(*) FROM pkkm_komponen")->fetchColumn();
        $totalIndikator = $db->query("SELECT COUNT(*) FROM pkkm_indikator")->fetchColumn();
        
        $totalManual = $db->query("SELECT COUNT(*) FROM pkkm_berkas")->fetchColumn();
        $totalMaster = $db->query("SELECT COUNT(*) FROM pkkm_master_berkas_file")->fetchColumn();
        $totalBerkas = $totalManual + $totalMaster;
        
        $skorTotal = $db->query("SELECT SUM(skor) FROM pkkm_nilai")->fetchColumn() ?: 0;
        $skorMax = $totalIndikator * 4;
        
        $persentase = $skorMax > 0 ? round(($skorTotal / $skorMax) * 100, 2) : 0;
        
        $kategori = 'Kurang';
        if ($persentase >= 91) $kategori = 'Amat Baik';
        else if ($persentase >= 76) $kategori = 'Baik';
        else if ($persentase >= 61) $kategori = 'Cukup';

        // Kelengkapan per komponen (Sinkronisasi dengan Master Berkas)
        $komponenStats = $db->query("
            SELECT k.id, k.nama_komponen, 
                   COUNT(i.id) as jml_indikator
            FROM pkkm_komponen k
            LEFT JOIN pkkm_indikator i ON i.komponen_id = k.id
            GROUP BY k.id
            ORDER BY k.urutan ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $indikators = $db->query("SELECT id, komponen_id, bukti_fisik FROM pkkm_indikator")->fetchAll(PDO::FETCH_ASSOC);
        $berkas = $db->query("SELECT indikator_id FROM pkkm_berkas")->fetchAll(PDO::FETCH_ASSOC);
        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT master_berkas_id FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);

        $mfiles_map = [];
        foreach($master_files as $mf) {
            $mfiles_map[$mf['master_berkas_id']][] = 1;
        }

        $berkasCountPerKomponen = [];
        foreach ($indikators as $ind) {
            $kid = $ind['komponen_id'];
            if(!isset($berkasCountPerKomponen[$kid])) $berkasCountPerKomponen[$kid] = 0;
            
            // Hitung berkas manual
            foreach ($berkas as $b) {
                if ($b['indikator_id'] == $ind['id']) {
                    $berkasCountPerKomponen[$kid]++;
                }
            }
            
            // Hitung berkas master yang tertaut
            foreach ($master_berkas as $mb) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    if (isset($mfiles_map[$mb['id']])) {
                        $berkasCountPerKomponen[$kid] += count($mfiles_map[$mb['id']]);
                    }
                }
            }
        }
        
        foreach($komponenStats as &$k) {
            $k['jml_berkas'] = $berkasCountPerKomponen[$k['id']] ?? 0;
        }

        self::render('pkkm_dashboard.php', 'Dashboard PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'overview',
            'totalKomponen' => $totalKomponen,
            'totalIndikator' => $totalIndikator,
            'totalBerkas' => $totalBerkas,
            'skorTotal' => $skorTotal,
            'skorMax' => $skorMax,
            'persentase' => $persentase,
            'kategori' => $kategori,
            'komponenStats' => $komponenStats
        ]);
    }

    public static function instrumen() {
        self::checkAuth();
        $db = Database::connect('core');

        $komponens = $db->query("SELECT * FROM pkkm_komponen ORDER BY CAST(urutan AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        $sub_komponens = $db->query("SELECT * FROM pkkm_sub_komponen ORDER BY CAST(SUBSTRING_INDEX(urutan, '.', 1) AS UNSIGNED), CAST(SUBSTRING_INDEX(urutan, '.', -1) AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Ambil semua indikator
        $indikators = $db->query("
            SELECT i.*, COALESCE(n.skor, 0) as skor 
            FROM pkkm_indikator i
            LEFT JOIN pkkm_nilai n ON n.indikator_id = i.id
            ORDER BY i.urutan ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil semua berkas
        $berkas = $db->query("SELECT * FROM pkkm_berkas")->fetchAll(PDO::FETCH_ASSOC);
        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT * FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);
        
        $mfiles_map = [];
        foreach($master_files as $mf) {
            $mfiles_map[$mf['master_berkas_id']][] = $mf;
        }

        // Grouping
        $data = [];
        foreach ($komponens as $k) {
            $k['sub_komponens'] = [];
            $k['indikators'] = []; // Untuk indikator yang tidak masuk sub
            $data[$k['id']] = $k;
        }

        $sub_map = [];
        foreach ($sub_komponens as $sub) {
            $sub['indikators'] = [];
            $sub_map[$sub['id']] = $sub;
        }

        foreach ($indikators as $ind) {
            $ind['berkas'] = [];
            foreach ($berkas as $b) {
                if ($b['indikator_id'] == $ind['id']) {
                    $b['is_master'] = false;
                    $ind['berkas'][] = $b;
                }
            }
            
            // Inject master berkas
            foreach ($master_berkas as $mb) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    if (isset($mfiles_map[$mb['id']])) {
                        foreach($mfiles_map[$mb['id']] as $mf) {
                            $ind['berkas'][] = [
                                'id' => 'master_' . $mf['id'],
                                'indikator_id' => $ind['id'],
                                'tipe' => $mf['tipe'],
                                'url' => $mf['url'],
                                'nama_file' => '[Auto] ' . ($mf['nama_file_asli'] ?: $mb['nama_berkas']),
                                'is_master' => true
                            ];
                        }
                    }
                }
            }
            
            if (!empty($ind['sub_komponen_id']) && isset($sub_map[$ind['sub_komponen_id']])) {
                $sub_map[$ind['sub_komponen_id']]['indikators'][] = $ind;
            } elseif (isset($data[$ind['komponen_id']])) {
                $data[$ind['komponen_id']]['indikators'][] = $ind;
            }
        }

        foreach ($sub_map as $sub) {
            if (isset($data[$sub['komponen_id']])) {
                $data[$sub['komponen_id']]['sub_komponens'][] = $sub;
            }
        }

        $totalKomponen = count($komponens);
        $totalIndikator = count($indikators);
        $totalBerkasManual = count($berkas);
        $totalBerkasMaster = count($master_files);
        $totalBerkas = $totalBerkasManual + $totalBerkasMaster;
        
        $skorTotal = $db->query("SELECT SUM(skor) FROM pkkm_nilai")->fetchColumn() ?: 0;
        $skorMax = $totalIndikator * 4;
        $persentase = $skorMax > 0 ? round(($skorTotal / $skorMax) * 100, 1) : 0;

        self::render('pkkm_instrumen.php', 'Instrumen Penilaian', [
            'active_module' => 'pkkm',
            'active_page' => 'instrumen',
            'data' => $data,
            'totalKomponen' => $totalKomponen,
            'totalIndikator' => $totalIndikator,
            'totalBerkas' => $totalBerkas,
            'skorTotal' => $skorTotal,
            'skorMax' => $skorMax,
            'persentase' => $persentase
        ]);
    }

    public static function saveKomponen() {
        self::checkAuth();
        $db = Database::connect('core');
        $nama = $_POST['nama_komponen'];
        $urutan = (int)$_POST['urutan'];
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $db->prepare("UPDATE pkkm_komponen SET nama_komponen = ?, urutan = ? WHERE id = ?")->execute([$nama, $urutan, $id]);
        } else {
            $db->prepare("INSERT INTO pkkm_komponen (nama_komponen, urutan) VALUES (?, ?)")->execute([$nama, $urutan]);
        }
        header("Location: /admin/pkkm/instrumen?msg=success");
    }

    public static function deleteKomponen() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        $db->prepare("DELETE FROM pkkm_komponen WHERE id = ?")->execute([$id]);
        header("Location: /admin/pkkm/instrumen?msg=deleted");
    }

    public static function saveSubKomponen() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $komponen_id = (int)$_POST['komponen_id'];
        $nama_sub = $_POST['nama_sub'];
        $urutan = $_POST['urutan'];

        if ($id > 0) {
            $db->prepare("UPDATE pkkm_sub_komponen SET komponen_id = ?, nama_sub = ?, urutan = ? WHERE id = ?")->execute([$komponen_id, $nama_sub, $urutan, $id]);
        } else {
            $db->prepare("INSERT INTO pkkm_sub_komponen (komponen_id, nama_sub, urutan) VALUES (?, ?, ?)")->execute([$komponen_id, $nama_sub, $urutan]);
        }
        header("Location: /admin/pkkm/instrumen?msg=success");
    }

    public static function deleteSubKomponen() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        $db->prepare("DELETE FROM pkkm_sub_komponen WHERE id = ?")->execute([$id]);
        header("Location: /admin/pkkm/instrumen?msg=deleted");
    }

    public static function saveIndikator() {
        self::checkAuth();
        $db = Database::connect('core');
        $komponen_id = (int)$_POST['komponen_id'];
        $sub_komponen_id = isset($_POST['sub_komponen_id']) && $_POST['sub_komponen_id'] !== '' ? (int)$_POST['sub_komponen_id'] : null;
        $indikator = $_POST['indikator'];
        $kriteria = $_POST['kriteria'];
        $bukti_fisik = $_POST['bukti_fisik'];
        $urutan = (int)$_POST['urutan'];
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $db->prepare("UPDATE pkkm_indikator SET komponen_id=?, sub_komponen_id=?, indikator=?, kriteria=?, bukti_fisik=?, urutan=? WHERE id=?")
               ->execute([$komponen_id, $sub_komponen_id, $indikator, $kriteria, $bukti_fisik, $urutan, $id]);
        } else {
            $db->prepare("INSERT INTO pkkm_indikator (komponen_id, sub_komponen_id, indikator, kriteria, bukti_fisik, urutan) VALUES (?, ?, ?, ?, ?, ?)")
               ->execute([$komponen_id, $sub_komponen_id, $indikator, $kriteria, $bukti_fisik, $urutan]);
        }
        
        // Auto Sync ke Master Berkas
        $lines = explode("\n", $bukti_fisik);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '-') === 0) {
                $nama_berkas = trim(substr($line, 1));
                if ($nama_berkas !== '') {
                    $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas WHERE nama_berkas = ?");
                    $stmt->execute([$nama_berkas]);
                    if (!$stmt->fetch()) {
                        $db->prepare("INSERT INTO pkkm_master_berkas (nama_berkas) VALUES (?)")->execute([$nama_berkas]);
                    }
                }
            }
        }
        header("Location: /admin/pkkm/instrumen?msg=success");
    }

    public static function deleteIndikator() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        $db->prepare("DELETE FROM pkkm_indikator WHERE id = ?")->execute([$id]);
        header("Location: /admin/pkkm/instrumen?msg=deleted");
    }

    public static function daftarBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        
        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT * FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);
        
        // Ambil indikator lengkap beserta komponen dan sub-komponennya
        $allIndikators = $db->query("
            SELECT i.id, i.komponen_id, i.sub_komponen_id, i.urutan, i.indikator, i.bukti_fisik,
                   k.nama_komponen, k.urutan AS komp_urutan,
                   sk.nama_sub, sk.urutan AS sub_urutan
            FROM pkkm_indikator i
            LEFT JOIN pkkm_komponen k ON i.komponen_id = k.id
            LEFT JOIN pkkm_sub_komponen sk ON i.sub_komponen_id = sk.id
            ORDER BY i.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $files_map = [];
        foreach($master_files as $f) {
            $files_map[$f['master_berkas_id']][] = $f;
        }

        $totalUploaded = 0;
        $totalPending = 0;
        $totalDigitalReady = 0;
        $totalFileCount = count($master_files);

        $categoryCounts = [
            'all' => count($master_berkas),
            'sk' => 0,
            'notulen' => 0,
            'program' => 0,
            'laporan' => 0,
            'digital' => 0,
            'sarpras' => 0,
            'umum' => 0
        ];

        foreach($master_berkas as &$mb) {
            $mb['files'] = isset($files_map[$mb['id']]) ? $files_map[$mb['id']] : [];
            
            // Cari seluruh indikator spesifik yang memerlukan berkas ini
            $matchedInds = [];
            foreach ($allIndikators as $ind) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    $kode = "Indikator " . ($ind['sub_urutan'] ? $ind['sub_urutan'] . '.' . $ind['urutan'] : ($ind['komp_urutan'] ? $ind['komp_urutan'] . '.' . $ind['urutan'] : $ind['urutan']));
                    $matchedInds[] = [
                        'id' => $ind['id'],
                        'kode' => $kode,
                        'komponen' => $ind['nama_komponen'] ?? ('Komponen ' . $ind['komponen_id']),
                        'sub' => $ind['nama_sub'] ?? '',
                        'nama' => $ind['indikator']
                    ];
                }
            }
            $mb['matched_indicators'] = $matchedInds;
            $mb['matched_count'] = count($matchedInds);

            // Metadata judul kontekstual, deskripsi konkret, kategori & shortcut bukti digital
            $meta = \App\Services\PkkmPanduanService::getMasterBerkasMetadata($mb['nama_berkas'], $matchedInds);
            $mb['judul_tampilan'] = $meta['judul_tampilan'];
            $mb['deskripsi'] = $meta['deskripsi'];
            $mb['kategori'] = $meta['kategori'];
            $mb['kategori_label'] = $meta['kategori_label'];
            $mb['kategori_badge'] = $meta['kategori_badge'];
            $mb['kategori_icon'] = $meta['kategori_icon'];
            $mb['bukti_digital'] = $meta['bukti_digital'];

            if (!empty($mb['bukti_digital'])) {
                $totalDigitalReady++;
            }

            if (isset($categoryCounts[$mb['kategori']])) {
                $categoryCounts[$mb['kategori']]++;
            } else {
                $categoryCounts['umum']++;
            }

            if (!empty($mb['files'])) {
                $totalUploaded++;
            } else {
                $totalPending++;
            }
        }
        unset($mb);
        
        $totalMaster = count($master_berkas);
        $totalIndikator = count($allIndikators);
        $percentUploaded = $totalMaster > 0 ? round(($totalUploaded / $totalMaster) * 100) : 0;
        
        self::render('pkkm_master_berkas.php', 'Daftar Berkas PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'master_berkas',
            'master_berkas' => $master_berkas,
            'totalMaster' => $totalMaster,
            'totalUploaded' => $totalUploaded,
            'totalPending' => $totalPending,
            'totalDigitalReady' => $totalDigitalReady,
            'totalFileCount' => $totalFileCount,
            'totalIndikator' => $totalIndikator,
            'percentUploaded' => $percentUploaded,
            'categoryCounts' => $categoryCounts
        ]);
    }

    public static function syncMasterBerkas() {
        self::checkAuth();
        $res = \App\Services\PkkmSyncService::syncMasterBerkas();
        if ($res['status']) {
            $ins = (int)($res['inserted'] ?? 0);
            $del = (int)($res['deleted'] ?? 0);
            header("Location: /admin/pkkm/master-berkas?msg=synced&ins=$ins&del=$del");
        } else {
            header("Location: /admin/pkkm/master-berkas?msg=error");
        }
        exit;
    }

    public static function createMasterBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        $nama_berkas = isset($_POST['nama_berkas']) ? trim($_POST['nama_berkas']) : '';
        if ($nama_berkas !== '') {
            $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas WHERE LOWER(nama_berkas) = LOWER(?)");
            $stmt->execute([$nama_berkas]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO pkkm_master_berkas (nama_berkas, created_at) VALUES (?, NOW())")->execute([$nama_berkas]);
            }
            header("Location: /admin/pkkm/master-berkas?msg=created");
        } else {
            header("Location: /admin/pkkm/master-berkas?msg=error");
        }
        exit;
    }

    public static function uploadMasterBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        $tipe = $_POST['tipe'];
        
        $url = null;
        $nama_file_asli = !empty($_POST['nama_file']) ? trim($_POST['nama_file']) : null;
        
        if ($tipe == 'link') {
            $url = $_POST['url'];
            $nama_file_asli = $nama_file_asli ?: 'Tautan Eksternal';
            $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli) VALUES (?, ?, ?, ?)")->execute([$id, $tipe, $url, $nama_file_asli]);
            $success = true;
        } else {
            if (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
                $success = self::processUploadedFiles($_FILES['file_upload'], $tipe, $id, true, $nama_file_asli);
            }
        }
        
        if (isset($success) && $success) {
            header("Location: /admin/pkkm/master-berkas?msg=success");
        } else {
            header("Location: /admin/pkkm/master-berkas?msg=error");
        }
    }

    public static function deleteMasterBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        
        // Hapus file fisik jika ada
        $b = $db->prepare("SELECT url, tipe FROM pkkm_master_berkas_file WHERE id = ?");
        $b->execute([$id]);
        $berkas = $b->fetch(PDO::FETCH_ASSOC);
        
        if ($berkas && $berkas['tipe'] == 'file' && $berkas['url']) {
            $filepath = __DIR__ . '/../../public' . $berkas['url'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
        }
        
        $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE id = ?")->execute([$id]);
        header("Location: /admin/pkkm/master-berkas?msg=deleted");
    }

    public static function attachDigitalToMaster() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)($_POST['id'] ?? 0);
        $url = trim($_POST['url'] ?? '');
        $nama_file = trim($_POST['nama_file'] ?? 'Bukti Fisik Resmi (Cetak PDF)');
        
        if ($id > 0 && $url !== '') {
            $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas_file WHERE master_berkas_id = ? AND url = ?");
            $stmt->execute([$id, $url]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$id, $url, $nama_file]);
            }
            header("Location: /admin/pkkm/master-berkas?msg=digital_attached");
        } else {
            header("Location: /admin/pkkm/master-berkas?msg=error");
        }
        exit;
    }


    public static function saveBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        $indikator_id = (int)$_POST['indikator_id'];
        $tipe = $_POST['tipe']; // 'file' atau 'link'
        
        if ($tipe === 'link') {
            $url = $_POST['url'];
            $nama_file = $_POST['nama_file'] ?? 'Tautan Eksternal';
            $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, ?, ?, ?)")
               ->execute([$indikator_id, $tipe, $url, $nama_file]);
        } else {
            // Handle multiple file upload & merge
            if (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
                $nama_file = $_POST['nama_file'] ?? null;
                self::processUploadedFiles($_FILES['file_upload'], 'file', $indikator_id, false, $nama_file);
            }
        }
        header("Location: /admin/pkkm/instrumen?msg=berkas_saved");
    }

    public static function deleteBerkas() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];
        
        $berkas = $db->query("SELECT * FROM pkkm_berkas WHERE id = $id")->fetch();
        if ($berkas) {
            if ($berkas['tipe'] === 'file') {
                $filePath = __DIR__ . '/../..' . $berkas['url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $db->prepare("DELETE FROM pkkm_berkas WHERE id = ?")->execute([$id]);
        }
        header("Location: /admin/pkkm/instrumen?msg=deleted");
    }

    public static function saveNilai() {
        self::checkAuth();
        $db = Database::connect('core');
        $indikator_id = (int)$_POST['indikator_id'];
        $skor = (int)$_POST['skor'];
        
        $exists = $db->query("SELECT * FROM pkkm_nilai WHERE indikator_id = $indikator_id")->fetch();
        if ($exists) {
            $db->prepare("UPDATE pkkm_nilai SET skor = ? WHERE indikator_id = ?")->execute([$skor, $indikator_id]);
        } else {
            $db->prepare("INSERT INTO pkkm_nilai (indikator_id, skor) VALUES (?, ?)")->execute([$indikator_id, $skor]);
        }
        
        echo json_encode(['status' => 1, 'pesan' => 'Tersimpan']);
        exit;
    }

    public static function syncLatestInstrumen() {
        self::checkAuth();
        $res = \App\Services\PkkmSyncService::executeSync();
        if ($res['status']) {
            header("Location: /admin/pkkm/instrumen?msg=synced");
        } else {
            header("Location: /admin/pkkm/instrumen?msg=error_sync");
        }
        exit;
    }

    public static function cetakBuktiWebsiteEmail() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_website_email_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_website_email_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_website_email_pdf.php';
        include $bukti_file;
    }

    public static function uploadWebsiteScreenshot() {
        self::checkAuthBukti();
        if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['screenshot']['tmp_name'];
            $destDir = __DIR__ . '/../../public/uploads/website';
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                $dest = $destDir . '/screenshot_landing.png';
                move_uploaded_file($tmp, $dest);
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '/pengawas/pkkm/cetak-bukti/website-email';
        header("Location: $referer");
        exit;
    }

    public static function attachWebsiteLink() {
        self::checkAuth();
        $db = Database::connect('core');
        $indikator_id = isset($_POST['indikator_id']) ? (int)$_POST['indikator_id'] : 0;
        
        if ($indikator_id > 0) {
            $url = "https://masmifhda.sch.id";
            $nama_file = "Website Resmi MAS Miftahul Huda (https://masmifhda.sch.id)";
            $stmt = $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, file_path, nama_file) VALUES (?, 'link', ?, ?)");
            $stmt->execute([$indikator_id, $url, $nama_file]);
        }
        header("Location: /admin/pkkm/instrumen?msg=link_attached");
        exit;
    }

    public static function cetakBuktiJaringanInternet() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_jaringan_internet_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_jaringan_internet_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_jaringan_internet_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiAbsensiElektronik() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $gurus = [];
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $gurus = $db->query("SELECT nama, nip, jabatan FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_absensi_elektronik_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_absensi_elektronik_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_absensi_elektronik_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiStrukturOrganisasi() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_struktur_organisasi_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_struktur_organisasi_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_struktur_organisasi_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiInventarisTik() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_inventaris_tik_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_inventaris_tik_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_inventaris_tik_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiJadwalKegiatan() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $kalender_events = [];
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        try {
            $dbSiakad = Database::connect('siakad');
            $kalender_events = $dbSiakad->query("SELECT * FROM kalender_pendidikan ORDER BY tanggal_mulai ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_jadwal_kegiatan_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_jadwal_kegiatan_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_jadwal_kegiatan_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiVisiMisi() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_visi_misi_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_visi_misi_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_visi_misi_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiPerpustakaan() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_perpustakaan_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_perpustakaan_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_perpustakaan_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiLingkunganSehat() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_lingkungan_sehat_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_lingkungan_sehat_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_lingkungan_sehat_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiSupervisiGuru() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $gurus = [];
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $gurus = $db->query("SELECT * FROM guru WHERE is_kamad = 0 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_supervisi_guru_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_supervisi_guru_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_supervisi_guru_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiEkstrakurikuler() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_ekstrakurikuler_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_ekstrakurikuler_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_ekstrakurikuler_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiPrestasiSiswa() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_prestasi_siswa_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_prestasi_siswa_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_prestasi_siswa_pdf.php';
        include $bukti_file;
    }

    public static function cetakBuktiKewirausahaan() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $bukti_file = file_exists(__DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_kewirausahaan_pdf.php') ? __DIR__ . '/../../resources/views/admin/pkkm/pkkm_bukti_kewirausahaan_pdf.php' : __DIR__ . '/../../resources/views/admin/pkkm_bukti_kewirausahaan_pdf.php';
        include $bukti_file;
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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $db->exec("CREATE TABLE IF NOT EXISTS pkkm_penilai_skor (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pengawas_id INT NOT NULL,
                indikator_id INT NOT NULL,
                skor INT DEFAULT 0,
                catatan TEXT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_pengawas_indikator (pengawas_id, indikator_id)
            )");
        } catch (\Exception $e) {}
    }

    public static function daftarPengawas() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        $pengawas_list = [];
        try {
            $pengawas_list = $db->query("
                SELECT p.*, u.username, u.status as user_status,
                (SELECT COUNT(*) FROM pkkm_penilai_skor s WHERE s.pengawas_id = p.id AND s.skor > 0) as total_dinilai,
                (SELECT SUM(skor) FROM pkkm_penilai_skor s WHERE s.pengawas_id = p.id) as skor_total
                FROM pkkm_pengawas p
                LEFT JOIN users u ON u.id = p.user_id
                ORDER BY p.peran ASC, p.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $total_indikator = 42;

        self::render('pkkm_pengawas.php', 'Kelola Akun Pengawas / Penilai PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'pengawas',
            'pengawas_list' => $pengawas_list,
            'total_indikator' => $total_indikator
        ]);
    }

    public static function savePengawas() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nama = trim($_POST['nama'] ?? '');
        $nip = trim($_POST['nip'] ?? '-');
        $jabatan = trim($_POST['jabatan'] ?? 'Pengawas Madrasah');
        $instansi = trim($_POST['instansi'] ?? 'Kantor Kementerian Agama Kab. Lampung Barat');
        $peran = in_array($_POST['peran'] ?? '', ['penilai_1', 'penilai_2']) ? $_POST['peran'] : 'penilai_1';
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $no_hp = trim($_POST['no_hp'] ?? '');

        if (empty($nama) || empty($username)) {
            header("Location: /admin/pkkm/pengawas?error=empty_fields");
            exit;
        }

        try {
            if ($id > 0) {
                $p = $db->query("SELECT * FROM pkkm_pengawas WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
                if ($p) {
                    $userId = $p['user_id'];
                    if ($userId > 0) {
                        if (!empty($password)) {
                            $hash = password_hash($password, PASSWORD_BCRYPT);
                            $db->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ?")
                               ->execute([$username, $hash, $userId]);
                        } else {
                            $db->prepare("UPDATE users SET username = ? WHERE id = ?")
                               ->execute([$username, $userId]);
                        }
                    }
                    $stmt = $db->prepare("UPDATE pkkm_pengawas SET nama=?, nip=?, jabatan=?, instansi=?, peran=?, no_hp=? WHERE id=?");
                    $stmt->execute([$nama, $nip, $jabatan, $instansi, $peran, $no_hp, $id]);
                }
            } else {
                $existingUser = $db->query("SELECT id FROM users WHERE username = " . $db->quote($username))->fetch();
                $userId = null;
                if ($existingUser) {
                    $userId = $existingUser['id'];
                    $db->prepare("UPDATE users SET role_id = 5 WHERE id = ?")->execute([$userId]);
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);
                    }
                } else {
                    $pass = !empty($password) ? $password : 'pengawas123';
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES (?, ?, 5, 'active')");
                    $stmt->execute([$username, $hash]);
                    $userId = $db->lastInsertId();
                }

                $stmt = $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $nama, $nip, $jabatan, $instansi, $peran, $no_hp]);
            }
            header("Location: /admin/pkkm/pengawas?msg=saved");
        } catch (\Exception $e) {
            header("Location: /admin/pkkm/pengawas?error=" . urlencode($e->getMessage()));
        }
        exit;
    }

    public static function deletePengawas() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            try {
                $p = $db->query("SELECT user_id FROM pkkm_pengawas WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
                if ($p && $p['user_id'] > 0) {
                    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$p['user_id']]);
                }
                $db->prepare("DELETE FROM pkkm_penilai_skor WHERE pengawas_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM pkkm_pengawas WHERE id = ?")->execute([$id]);
            } catch (\Exception $e) {}
        }
        header("Location: /admin/pkkm/pengawas?msg=deleted");
        exit;
    }

    public static function generateDefaultPengawas() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensurePengawasTables($db);

        try {
            $check1 = $db->query("SELECT id FROM pkkm_pengawas WHERE peran = 'penilai_1' LIMIT 1")->fetch();
            if (!$check1) {
                $hash1 = password_hash('penilai123', PASSWORD_BCRYPT);
                $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES (?, ?, 5, 'active')")
                   ->execute(['penilai1', $hash1]);
                $uid1 = $db->lastInsertId();

                $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)")
                   ->execute([$uid1, 'H. AHMAD SYARIF, M.Pd.I', '197405122002121004', 'Pengawas Madrasah Ahli Madya', 'Kantor Kemenag Kab. Lampung Barat', 'penilai_1', '081234567890']);
            }

            $check2 = $db->query("SELECT id FROM pkkm_pengawas WHERE peran = 'penilai_2' LIMIT 1")->fetch();
            if (!$check2) {
                $hash2 = password_hash('penilai223', PASSWORD_BCRYPT);
                $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES (?, ?, 5, 'active')")
                   ->execute(['penilai2', $hash2]);
                $uid2 = $db->lastInsertId();

                $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)")
                   ->execute([$uid2, 'DRA. HJ. SITI NURHAYATI, M.Pd', '197803152005012003', 'Pengawas Madrasah Ahli Muda', 'Kantor Kemenag Kab. Lampung Barat', 'penilai_2', '081298765432']);
            }
        } catch (\Exception $e) {}

        header("Location: /admin/pkkm/pengawas?msg=generated");
        exit;
    }

    public static function ensureTimTables($db) {
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS pkkm_tim (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guru_id INT NOT NULL,
                komponen_id INT NOT NULL,
                peran VARCHAR(100) DEFAULT 'Anggota Tim',
                tugas_keterangan VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_guru (guru_id),
                KEY idx_komponen (komponen_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {}
    }

    public static function tim() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensureTimTables($db);

        $komponens = $db->query("SELECT * FROM pkkm_komponen ORDER BY CAST(urutan AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        $guru_list = $db->query("SELECT id, nama, nip, jabatan, foto FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        $tim_list = [];
        try {
            $tim_list = $db->query("
                SELECT t.*, g.nama as nama_guru, g.nip, g.foto, g.jabatan,
                       k.nama_komponen, k.urutan as komponen_urutan
                FROM pkkm_tim t
                JOIN guru g ON t.guru_id = g.id
                JOIN pkkm_komponen k ON t.komponen_id = k.id
                ORDER BY CAST(k.urutan AS UNSIGNED) ASC, 
                         CASE WHEN t.peran LIKE '%Koordinator%' THEN 1 
                              WHEN t.peran LIKE '%Penanggung Jawab%' THEN 2 
                              ELSE 3 END ASC,
                         g.nama ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $tim_by_komponen = [];
        foreach($komponens as $k) {
            $tim_by_komponen[$k['id']] = [];
        }
        foreach($tim_list as $t) {
            $tim_by_komponen[$t['komponen_id']][] = $t;
        }

        self::render('pkkm_tim.php', 'Tim Penanggung Jawab Komponen PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'tim_pkkm',
            'komponens' => $komponens,
            'guru_list' => $guru_list,
            'tim_list' => $tim_list,
            'tim_by_komponen' => $tim_by_komponen
        ]);
    }

    public static function saveTim() {
        self::checkAuth();
        $db = Database::connect('core');
        self::ensureTimTables($db);

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $guru_id = (int)($_POST['guru_id'] ?? 0);
        $komponen_id = (int)($_POST['komponen_id'] ?? 0);
        $peran = trim($_POST['peran'] ?? 'Anggota Tim');
        $tugas_keterangan = trim($_POST['tugas_keterangan'] ?? '');

        if ($guru_id <= 0 || $komponen_id <= 0) {
            header("Location: /admin/pkkm/tim?error=missing_data");
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE pkkm_tim SET guru_id = ?, komponen_id = ?, peran = ?, tugas_keterangan = ? WHERE id = ?");
                $stmt->execute([$guru_id, $komponen_id, $peran, $tugas_keterangan, $id]);
            } else {
                $stmtCek = $db->prepare("SELECT id FROM pkkm_tim WHERE guru_id = ? AND komponen_id = ?");
                $stmtCek->execute([$guru_id, $komponen_id]);
                if ($stmtCek->fetch()) {
                    $stmt = $db->prepare("UPDATE pkkm_tim SET peran = ?, tugas_keterangan = ? WHERE guru_id = ? AND komponen_id = ?");
                    $stmt->execute([$peran, $tugas_keterangan, $guru_id, $komponen_id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO pkkm_tim (guru_id, komponen_id, peran, tugas_keterangan) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$guru_id, $komponen_id, $peran, $tugas_keterangan]);
                }
            }
            header("Location: /admin/pkkm/tim?msg=saved");
        } catch (\Exception $e) {
            header("Location: /admin/pkkm/tim?error=" . urlencode($e->getMessage()));
        }
        exit;
    }

    public static function deleteTim() {
        self::checkAuth();
        $db = Database::connect('core');
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM pkkm_tim WHERE id = ?")->execute([$id]);
        }
        header("Location: /admin/pkkm/tim?msg=deleted");
        exit;
    }

        public static function render($view_file, $title_page, $extra_data = []) {
        $adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['guru_nama']) ? $_SESSION['guru_nama'] : 'Admin'));
        $adminInitial = strtoupper(substr($adminNama, 0, 2));

        extract($extra_data);
        ob_start();
        if (file_exists(__DIR__ . '/../../resources/views/admin/pkkm/' . $view_file)) {
            include __DIR__ . '/../../resources/views/admin/pkkm/' . $view_file;
        } elseif (file_exists(__DIR__ . '/../../resources/views/admin/' . $view_file)) {
            include __DIR__ . '/../../resources/views/admin/' . $view_file;
        } else {
            echo "View PKKM tidak ditemukan: " . htmlspecialchars($view_file);
        }
        echo ob_get_clean();
    }

    // --- LEGACY / HELPER COMPATIBILITY METHODS FROM MASMIFHDA ---

    public function index() {
        return $this->dashboard();
    }

    public function saveIdentitas() {
        $db = $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('admin/pkkm/instrumen'));
            exit;
        }

        $nama_kamad = trim($_POST['nama_kamad'] ?? '');
        $nip_kamad = trim($_POST['nip_kamad'] ?? '');
        $nama_madrasah = trim($_POST['nama_madrasah'] ?? '');
        $alamat_madrasah = trim($_POST['alamat_madrasah'] ?? '');
        $nama_penilai = trim($_POST['nama_penilai'] ?? '');
        $nip_penilai = trim($_POST['nip_penilai'] ?? '');
        $jabatan_penilai = trim($_POST['jabatan_penilai'] ?? '');
        $tgl_penilaian = !empty($_POST['tgl_penilaian']) ? $_POST['tgl_penilaian'] : date('Y-m-d');
        $tempat_penilaian = trim($_POST['tempat_penilaian'] ?? 'Lampung Barat');
        $tahun_penilaian = trim($_POST['tahun_penilaian'] ?? date('Y'));

        try {
            $cnt = $db->query("SELECT COUNT(*) FROM pkkm_identitas")->fetchColumn();
            if ($cnt == 0) {
                $stmt = $db->prepare("INSERT INTO pkkm_identitas (nama_kamad, nip_kamad, nama_madrasah, alamat_madrasah, nama_penilai, nip_penilai, jabatan_penilai, tgl_penilaian, tempat_penilaian, tahun_penilaian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama_kamad, $nip_kamad, $nama_madrasah, $alamat_madrasah, $nama_penilai, $nip_penilai, $jabatan_penilai, $tgl_penilaian, $tempat_penilaian, $tahun_penilaian]);
            } else {
                $stmt = $db->prepare("UPDATE pkkm_identitas SET nama_kamad=?, nip_kamad=?, nama_madrasah=?, alamat_madrasah=?, nama_penilai=?, nip_penilai=?, jabatan_penilai=?, tgl_penilaian=?, tempat_penilaian=?, tahun_penilaian=? WHERE id = 1");
                $stmt->execute([$nama_kamad, $nip_kamad, $nama_madrasah, $alamat_madrasah, $nama_penilai, $nip_penilai, $jabatan_penilai, $tgl_penilaian, $tempat_penilaian, $tahun_penilaian]);
            }
            $_SESSION['flash_success'] = 'Identitas instrumen penilaian berhasil diperbarui!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Gagal menyimpan identitas: ' . $e->getMessage();
        }

        header('Location: ' . base_url('admin/pkkm/instrumen'));
        exit;
    }

    /**
     * Simpan Berkas Langsung ke Indikator (pkkm_berkas)
     */

    private function getInstitusiAndKamad($db) {
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        try {
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (!$kamad) {
            try {
                $ident = $db->query("SELECT nama_kamad as nama, nip_kamad as nip FROM pkkm_identitas LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                if ($ident) $kamad = $ident;
            } catch (\Exception $e) {}
        }
        return ['inst' => $inst, 'kamad' => $kamad];
    }

    public function cetakInstrumen() {
        $db = $this->checkAccess();

        $identitas = $db->query("SELECT * FROM pkkm_identitas LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if (!$identitas) {
            $identitas = [
                'nama_kamad' => "AHMAD RIFA'I, M.Pd",
                'nip_kamad' => "-",
                'nama_madrasah' => "MA MIFTAHUL HUDA MATHLA'UL ANWAR",
                'alamat_madrasah' => "Jl. Tirtomoyo, No. 083, Margorahayu, Tambak Jaya, Way Tenong, Lampung Barat",
                'nama_penilai' => "Drs. H. M. Pengawas, M.Pd.I",
                'nip_penilai' => "196805121995031002",
                'jabatan_penilai' => "Pengawas Madrasah Ahli Madya",
                'tgl_penilaian' => date('Y-m-d'),
                'tempat_penilaian' => "Lampung Barat",
                'tahun_penilaian' => "2026"
            ];
        }

        $sql = "
            SELECT 
                i.id as indikator_id,
                i.komponen_id,
                i.sub_komponen_id,
                i.kode,
                i.indikator,
                i.kriteria,
                i.bukti_fisik,
                i.penggalian_data,
                i.urutan as indikator_urutan,
                s.nama_sub,
                s.urutan as sub_urutan,
                k.nama_komponen,
                k.urutan as komp_urutan,
                n.skor
            FROM pkkm_indikator i
            JOIN pkkm_sub_komponen s ON i.sub_komponen_id = s.id
            JOIN pkkm_komponen k ON i.komponen_id = k.id
            LEFT JOIN pkkm_nilai n ON i.id = n.indikator_id
            ORDER BY k.urutan ASC, CAST(s.urutan AS UNSIGNED) ASC, i.urutan ASC
        ";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $groupedKomponen = [];
        $totalSkor = 0;
        $totalTerisi = 0;
        foreach ($rows as $row) {
            $kId = $row['komponen_id'];
            $subId = $row['sub_komponen_id'];

            if (!isset($groupedKomponen[$kId])) {
                $groupedKomponen[$kId] = [
                    'nama_komponen' => $row['nama_komponen'],
                    'urutan' => $row['komp_urutan'],
                    'sub' => []
                ];
            }

            if (!isset($groupedKomponen[$kId]['sub'][$subId])) {
                $groupedKomponen[$kId]['sub'][$subId] = [
                    'nama_sub' => $row['nama_sub'],
                    'sub_urutan' => $row['sub_urutan'],
                    'items' => []
                ];
            }

            if (!empty($row['skor'])) {
                $totalSkor += (int)$row['skor'];
                $totalTerisi++;
            }

            $groupedKomponen[$kId]['sub'][$subId]['items'][] = $row;
        }

        include __DIR__ . '/../../resources/views/admin/pkkm/cetak_instrumen.php';
    }

    /**
     * AJAX Simpan Nilai Skor Instrumen
     */

    public function dokumen() {
        $db = $this->checkAccess();

        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : 'all';

        $sql = "
            SELECT 
                mb.id,
                mb.nama_berkas,
                mb.created_at,
                f.id as file_id,
                f.tipe,
                f.url,
                f.nama_file_asli,
                f.created_at as file_uploaded_at
            FROM pkkm_master_berkas mb
            LEFT JOIN pkkm_master_berkas_file f ON mb.id = f.master_berkas_id
        ";

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "mb.nama_berkas LIKE ?";
            $params[] = '%' . $search . '%';
        }

        if ($filterStatus === 'uploaded') {
            $conditions[] = "f.id IS NOT NULL";
        } elseif ($filterStatus === 'empty') {
            $conditions[] = "f.id IS NULL";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY mb.nama_berkas ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $berkasList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung total berkas & terupload
        $totalBerkas = (int)$db->query("SELECT COUNT(*) FROM pkkm_master_berkas")->fetchColumn();
        $totalUploaded = (int)$db->query("SELECT COUNT(DISTINCT master_berkas_id) FROM pkkm_master_berkas_file")->fetchColumn();

        $active_module = 'pkkm';
        $active_page   = 'dokumen';
        $page_title    = 'Dokumen & Bukti Fisik PKKM';

        include __DIR__ . '/../../resources/views/admin/pkkm/dokumen.php';
    }

    /**
     * Upload File Dokumen atau Simpan Link
     */

    public function uploadDokumen() {
        $db = $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('admin/pkkm/dokumen'));
            exit;
        }

        $master_berkas_id = isset($_POST['master_berkas_id']) ? (int)$_POST['master_berkas_id'] : 0;
        $tipe = isset($_POST['tipe']) && $_POST['tipe'] === 'link' ? 'link' : 'file';

        if ($master_berkas_id <= 0) {
            $_SESSION['flash_error'] = 'Pilih dokumen yang valid.';
            header('Location: ' . base_url('admin/pkkm/dokumen'));
            exit;
        }

        if ($tipe === 'link') {
            $linkUrl = isset($_POST['url']) ? trim($_POST['url']) : '';
            if (empty($linkUrl) || !filter_var($linkUrl, FILTER_VALIDATE_URL)) {
                $_SESSION['flash_error'] = 'URL Link tidak valid.';
                header('Location: ' . base_url('admin/pkkm/dokumen'));
                exit;
            }

            try {
                // Hapus berkas lama jika ada
                $this->removeOldMasterFile($db, $master_berkas_id);

                $stmt = $db->prepare("
                    INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at)
                    VALUES (?, 'link', ?, 'Tautan Eksternal', NOW())
                ");
                $stmt->execute([$master_berkas_id, $linkUrl]);
                $_SESSION['flash_success'] = 'Tautan dokumen berhasil disimpan!';
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Gagal menyimpan: ' . $e->getMessage();
            }
        } else {
            // Upload file
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash_error'] = 'Gagal mengunggah file. Silakan periksa ukuran file.';
                header('Location: ' . base_url('admin/pkkm/dokumen'));
                exit;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/pkkm/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $originalName = $_FILES['file']['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'rar'];

            if (!in_array($ext, $allowedExt)) {
                $_SESSION['flash_error'] = 'Format file tidak diizinkan. Gunakan PDF, Office, Gambar, atau ZIP.';
                header('Location: ' . base_url('admin/pkkm/dokumen'));
                exit;
            }

            // Max 25MB
            if ($_FILES['file']['size'] > 26214400) {
                $_SESSION['flash_error'] = 'Ukuran file melebihi batas 25MB.';
                header('Location: ' . base_url('admin/pkkm/dokumen'));
                exit;
            }

            $newFileName = 'PKKM_' . $master_berkas_id . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                $relativeUrl = 'public/uploads/pkkm/' . $newFileName;
                
                try {
                    $this->removeOldMasterFile($db, $master_berkas_id);

                    $stmt = $db->prepare("
                        INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at)
                        VALUES (?, 'file', ?, ?, NOW())
                    ");
                    $stmt->execute([$master_berkas_id, $relativeUrl, $originalName]);
                    $_SESSION['flash_success'] = 'File bukti fisik berhasil diunggah!';
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = 'Gagal menyimpan data berkas: ' . $e->getMessage();
                }
            } else {
                $_SESSION['flash_error'] = 'Gagal memindahkan file yang diunggah.';
            }
        }

        header('Location: ' . base_url('admin/pkkm/dokumen'));
        exit;
    }

    /**
     * Hapus berkas lama dari disk dan database untuk master_berkas_id tertentu
     */

    private function removeOldMasterFile($db, $master_berkas_id) {
        $stmt = $db->prepare("SELECT id, tipe, url FROM pkkm_master_berkas_file WHERE master_berkas_id = ?");
        $stmt->execute([$master_berkas_id]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($files as $f) {
            if ($f['tipe'] === 'file' && !empty($f['url'])) {
                $filePath = __DIR__ . '/../../' . ltrim($f['url'], '/');
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        $del = $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE master_berkas_id = ?");
        $del->execute([$master_berkas_id]);
    }

    /**
     * Hapus Dokumen Bukti Fisik
     */

    public function deleteDokumen() {
        $db = $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('admin/pkkm/dokumen'));
            exit;
        }

        $master_berkas_id = isset($_POST['master_berkas_id']) ? (int)$_POST['master_berkas_id'] : 0;
        if ($master_berkas_id > 0) {
            $this->removeOldMasterFile($db, $master_berkas_id);
            $_SESSION['flash_success'] = 'Dokumen bukti fisik berhasil dihapus.';
        }

        header('Location: ' . base_url('admin/pkkm/dokumen'));
        exit;
    }

    /**
     * Auto-sync bukti fisik dari modul-modul sistem MASMIFHDA yang sudah aktif
     */

    public function syncSistem() {
        $db = $this->checkAccess();

        $syncItems = [
            ['id' => 123, 'url' => 'admin/absen-v2/rekap', 'title' => 'Sistem Presensi QR V2 Realtime'],
            ['id' => 192, 'url' => 'guru/kamad/program/cetak', 'title' => 'Buku Program Kerja Kamad 2026/2027'],
            ['id' => 191, 'url' => 'guru/kamad/program', 'title' => 'Program Kerja Madrasah & Target SNP'],
            ['id' => 54,  'url' => 'guru/kamad/program', 'title' => 'Dokumen EDM Terintegrasi'],
            ['id' => 55,  'url' => 'admin/bos/rkas', 'title' => 'RKT & Anggaran BOS Berbasis EDM'],
            ['id' => 15,  'url' => 'admin/perpus/fisik', 'title' => 'Katalog Buku Induk Perpustakaan'],
            ['id' => 27,  'url' => 'admin/perpus/fisik', 'title' => 'Master Buku & Rak Perpustakaan'],
            ['id' => 21,  'url' => 'admin/perpus/sirkulasi', 'title' => 'Statistik Layanan & Sirkulasi Buku'],
            ['id' => 43,  'url' => 'admin/perpus/sirkulasi', 'title' => 'Log Pengunjung & Peminjaman Perpustakaan'],
            ['id' => 58,  'url' => 'admin/perpus', 'title' => 'Dashboard E-Library & Aplikasi Perpus'],
            ['id' => 104, 'url' => 'admin/kurikulum/monitoring-jadwal', 'title' => 'Jadwal Mengajar KBM Guru Terpadu'],
            ['id' => 237, 'url' => 'admin/kurikulum/monitoring-jadwal', 'title' => 'Jadwal Pelajaran Kelas KBM'],
            ['id' => 102, 'url' => 'admin/kurikulum/supervisi-administrasi', 'title' => 'Jadwal Pelaksanaan Supervisi Guru'],
            ['id' => 76,  'url' => 'admin/kurikulum/supervisi-administrasi', 'title' => 'Dokumen Instrumen Supervisi Administrasi GTK'],
            ['id' => 127, 'url' => 'admin/kurikulum/supervisi-kelas', 'title' => 'Laporan Evaluasi Supervisi Kelas GTK'],
            ['id' => 213, 'url' => 'admin/kurikulum/supervisi-kelas', 'title' => 'Catatan Rekomendasi & Tindak Lanjut Supervisi'],
            ['id' => 137, 'url' => 'admin/bos/laporan', 'title' => 'Laporan Pertanggungjawaban BOS & Keuangan Komite'],
            ['id' => 17,  'url' => 'admin/bos/bku', 'title' => 'Buku Kas Umum (BKU) BOS Realtime'],
            ['id' => 36,  'url' => 'admin/siakad/siswa/alumni', 'title' => 'Database Alumni Lulusan Madrasah'],
            ['id' => 10,  'url' => 'admin/siakad/guru', 'title' => 'Struktur Personalia Guru & Tenaga Kependidikan'],
            ['id' => 214, 'url' => 'admin/siakad/guru/tugas', 'title' => 'Rincian Tugas & Beban Kerja Guru/Tendik'],
            ['id' => 185, 'url' => 'apk/ekstra_jurnal.php', 'title' => 'Program & Jurnal Pembina Ekstrakurikuler'],
            ['id' => 233, 'url' => 'portal', 'title' => 'Ekosistem Portal & Website Madrasah'],
            ['id' => 165, 'url' => 'admin/elearning', 'title' => 'Sistem Pembelajaran Digital E-Learning'],
            ['id' => 169, 'url' => 'admin/siakad/nilai', 'title' => 'Sistem E-Raport & Penilaian Akademik'],
            ['id' => 16,  'url' => 'admin/inventaris', 'title' => 'Inventaris Sarana & Prasarana Madrasah'],
            ['id' => 216, 'url' => 'admin/persuratan/sk', 'title' => 'SK Kamad Penetapan Komite Madrasah']
        ];

        $syncedCount = 0;
        foreach ($syncItems as $item) {
            $mbId = (int)$item['id'];
            $exists = $db->query("SELECT id FROM pkkm_master_berkas WHERE id = $mbId")->fetch();
            if ($exists) {
                $hasFile = $db->query("SELECT id, tipe FROM pkkm_master_berkas_file WHERE master_berkas_id = $mbId AND tipe = 'file'")->fetch();
                if (!$hasFile) {
                    $this->removeOldMasterFile($db, $mbId);
                    $stmt = $db->prepare("
                        INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at)
                        VALUES (?, 'link', ?, ?, NOW())
                    ");
                    $stmt->execute([$mbId, $item['url'], $item['title']]);
                    $syncedCount++;
                }
            }
        }

        // Auto-assign score 4 to matching indicators if score is not set
        $autoScoreKode = ['1.2', '2.1', '2.2', '4.2', '4.3', '5.1', '5.2', '6.1'];
        $stmtInd = $db->prepare("SELECT id FROM pkkm_indikator WHERE kode = ?");
        $stmtInsScore = $db->prepare("INSERT INTO pkkm_nilai (indikator_id, skor) VALUES (?, 4) ON DUPLICATE KEY UPDATE skor = IF(skor = 0, 4, skor)");
        foreach ($autoScoreKode as $kCode) {
            $stmtInd->execute([$kCode]);
            $iId = $stmtInd->fetchColumn();
            if ($iId) {
                $stmtInsScore->execute([$iId]);
            }
        }

        $_SESSION['flash_success'] = "Berhasil mensinkronkan $syncedCount berkas bukti fisik langsung dari modul sistem!";
        header('Location: ' . base_url('admin/pkkm/dokumen'));
        exit;
    }

    /**
     * Pemetaan Link Modul Sistem per Kode Indikator PKKM
     */
    public static function getSystemSyncMapping() {
        return [
            // Komponen A
            '1.2' => [
                'label' => 'SIAKAD: Data & Tugas Guru',
                'url' => 'admin/siakad/guru',
                'desc' => 'SK Pembagian Tugas & Uraian Tugas Guru/Staf'
            ],
            '2.1' => [
                'label' => 'SIAKAD: Data Alumni',
                'url' => 'admin/siakad/siswa/alumni',
                'desc' => 'Database Alumni & Profil Lulusan'
            ],
            '2.2' => [
                'label' => 'APK: Ekstrakurikuler',
                'url' => 'apk/ekstra_jurnal.php',
                'desc' => 'Program & Jurnal Ekstrakurikuler Siswa'
            ],
            '4.2' => [
                'label' => 'Waka Kurikulum: Jadwal KBM',
                'url' => 'admin/kurikulum/monitoring-jadwal',
                'desc' => 'Jadwal Mengajar, Perangkat KBM & Penilaian'
            ],
            '4.3' => [
                'label' => 'Portal HUB MASMIFHDA',
                'url' => 'portal',
                'desc' => 'Pemanfaatan TIK, CBT & Website Madrasah'
            ],
            '5.1' => [
                'label' => 'Absensi QR V2: Laporan Realtime',
                'url' => 'admin/absen-v2/rekap',
                'desc' => 'Laporan Absensi Elektronik Guru & Siswa'
            ],
            '5.2' => [
                'label' => 'E-Learning & Bank Soal',
                'url' => 'admin/elearning',
                'desc' => 'Model Pembelajaran Berbasis TIK'
            ],
            '6.1' => [
                'label' => 'Sistem Informasi Portal',
                'url' => 'portal',
                'desc' => 'Manajemen Berbasis SIM & Website Madrasah'
            ],
            // Komponen B (Unsur & Kode)
            '1.1' => [
                'label' => 'Buku Program Kerja Kamad',
                'url' => 'guru/kamad/program/cetak',
                'desc' => 'EDM & RKTM Berbasis 8 Standar'
            ],
            '2.1' => [
                'label' => 'SIAKAD: Notulen & Rapat',
                'url' => 'admin/siakad/lain-lain/rapat',
                'desc' => 'Daftar Hadir & Notulen Rapat Madrasah'
            ],
            '5.1' => [
                'label' => 'Manajemen Aset & Inventaris',
                'url' => 'admin/inventaris',
                'desc' => 'Buku Inventaris Sarana Prasarana'
            ],
            '5.2' => [
                'label' => 'Perpustakaan: Katalog & Sirkulasi',
                'url' => 'admin/perpus',
                'desc' => 'Buku Induk & Grafik Layanan E-Library'
            ],
            '6.1' => [
                'label' => 'SIAKAD: Data Induk Siswa',
                'url' => 'admin/siakad/siswa',
                'desc' => 'Buku Penerimaan & Biodata Siswa'
            ],
            '8.1' => [
                'label' => 'BOS: RKAS Anggaran',
                'url' => 'admin/bos/rkas',
                'desc' => 'Rencana Anggaran Tahunan 8 Standar'
            ],
            '8.2' => [
                'label' => 'BOS: Buku Kas Umum (BKU)',
                'url' => 'admin/bos/bku',
                'desc' => 'Laporan Pertanggungjawaban Keuangan Realtime'
            ],
            '9.1' => [
                'label' => 'Persuratan: Agenda Surat',
                'url' => 'admin/persuratan/masuk',
                'desc' => 'Buku Agenda Surat Masuk & Keluar'
            ],
            '9.2' => [
                'label' => 'SIAKAD Terpadu',
                'url' => 'admin/siakad',
                'desc' => 'Administrasi Akademik & Kesiswaan'
            ],
            // Komponen D
            '1.1_D' => [
                'label' => 'Supervisi: SK & Perencanaan GTK',
                'url' => 'admin/kurikulum/supervisi-administrasi',
                'desc' => 'Program Supervisi & SK Tim Supervisor'
            ],
            '2.1_D' => [
                'label' => 'Supervisi: Laporan & Rekomendasi',
                'url' => 'admin/kurikulum/supervisi-kelas',
                'desc' => 'Instrumen & Hasil Supervisi KBM Guru'
            ],
            '3.1_D' => [
                'label' => 'Supervisi: Tindak Lanjut Guru',
                'url' => 'admin/kurikulum/supervisi-administrasi',
                'desc' => 'Laporan Realisasi Tindak Lanjut Supervisi'
            ]
        ];
    }
}

