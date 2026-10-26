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
            
            if (!class_exists('FPDF')) require_once __DIR__ . '/../../vendor/fpdf/fpdf.php';
            
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

        // Kalkulasi Statistik Dasar
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

        // Kelengkapan per komponen (Akurat berdasarkan pemenuhan tiap butir indikator)
        $komponenStats = $db->query("
            SELECT k.id, k.urutan, k.nama_komponen, 
                   COUNT(i.id) as jml_indikator
            FROM pkkm_komponen k
            LEFT JOIN pkkm_indikator i ON i.komponen_id = k.id
            GROUP BY k.id
            ORDER BY CAST(k.urutan AS UNSIGNED) ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $indikators = $db->query("SELECT id, komponen_id, bukti_fisik FROM pkkm_indikator")->fetchAll(PDO::FETCH_ASSOC);
        $berkas = $db->query("SELECT indikator_id FROM pkkm_berkas")->fetchAll(PDO::FETCH_ASSOC);
        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT master_berkas_id FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);

        $mfiles_map = [];
        foreach($master_files as $mf) {
            $mfiles_map[$mf['master_berkas_id']][] = 1;
        }

        $berkasPerIndikator = [];
        foreach ($berkas as $b) {
            $berkasPerIndikator[$b['indikator_id']][] = 1;
        }

        $berkasCountPerKomponen = [];
        $indikatorTerpenuhiPerKomponen = [];
        $totalIndikatorTerpenuhiSemua = 0;

        foreach ($indikators as $ind) {
            $kid = $ind['komponen_id'];
            if(!isset($berkasCountPerKomponen[$kid])) $berkasCountPerKomponen[$kid] = 0;
            if(!isset($indikatorTerpenuhiPerKomponen[$kid])) $indikatorTerpenuhiPerKomponen[$kid] = 0;
            
            $fileCountForThisInd = 0;

            // 1. Hitung berkas manual untuk indikator ini
            if (isset($berkasPerIndikator[$ind['id']])) {
                $fileCountForThisInd += count($berkasPerIndikator[$ind['id']]);
            }
            
            // 2. Hitung berkas master yang tertaut untuk indikator ini
            foreach ($master_berkas as $mb) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    if (isset($mfiles_map[$mb['id']])) {
                        $fileCountForThisInd += count($mfiles_map[$mb['id']]);
                    }
                }
            }

            $berkasCountPerKomponen[$kid] += $fileCountForThisInd;

            // Indikator dianggap terpenuhi HANYA JIKA memiliki minimal 1 berkas (baik manual maupun master)
            if ($fileCountForThisInd > 0) {
                $indikatorTerpenuhiPerKomponen[$kid]++;
                $totalIndikatorTerpenuhiSemua++;
            }
        }
        
        foreach($komponenStats as &$k) {
            $kid = $k['id'];
            $k['jml_berkas'] = $berkasCountPerKomponen[$kid] ?? 0;
            $k['indikator_terpenuhi'] = $indikatorTerpenuhiPerKomponen[$kid] ?? 0;
            $k['indikator_sisa'] = max(0, $k['jml_indikator'] - $k['indikator_terpenuhi']);
            $k['persen_kelengkapan'] = $k['jml_indikator'] > 0 ? round(($k['indikator_terpenuhi'] / $k['jml_indikator']) * 100, 1) : 0;
            
            // Status Kelengkapan yang Benar & Adil
            if ($k['jml_indikator'] > 0 && $k['indikator_terpenuhi'] >= $k['jml_indikator']) {
                $k['status_label'] = 'Lengkap';
                $k['status_class'] = 'lengkap';
            } elseif ($k['indikator_terpenuhi'] > 0) {
                $k['status_label'] = 'Proses (' . $k['indikator_terpenuhi'] . '/' . $k['jml_indikator'] . ' Terisi)';
                $k['status_class'] = 'proses';
            } else {
                $k['status_label'] = 'Belum Ada';
                $k['status_class'] = 'kosong';
            }
        }
        unset($k);

        $totalIndikatorBelum = max(0, $totalIndikator - $totalIndikatorTerpenuhiSemua);
        $persenKelengkapanGlobal = $totalIndikator > 0 ? round(($totalIndikatorTerpenuhiSemua / $totalIndikator) * 100, 1) : 0;

        self::render('pkkm_dashboard.php', 'Dashboard PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'overview',
            'totalKomponen' => $totalKomponen,
            'totalIndikator' => $totalIndikator,
            'totalIndikatorTerpenuhi' => $totalIndikatorTerpenuhiSemua,
            'totalIndikatorBelum' => $totalIndikatorBelum,
            'persenKelengkapanGlobal' => $persenKelengkapanGlobal,
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
        
        $totalPerangkatSynced = (int)$db->query("SELECT COUNT(*) FROM pkkm_master_berkas_file WHERE url LIKE '/public/uploads/perangkat/%'")->fetchColumn();

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
            'categoryCounts' => $categoryCounts,
            'totalPerangkatSynced' => $totalPerangkatSynced
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

    public static function unsyncPerangkat() {
        self::checkAuth();
        try {
            $db = Database::connect('core');
            $stmt = $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE url LIKE '/public/uploads/perangkat/%'");
            $stmt->execute();
            $del = $stmt->rowCount();
            header("Location: /admin/pkkm/master-berkas?msg=unsynced_perangkat&del=" . $del);
            exit;
        } catch (\Exception $e) {
            header("Location: /admin/pkkm/master-berkas?msg=error");
            exit;
        }
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

    public static function buktiDigital() {
        self::checkAuth();
        $db = Database::connect('core');

        // Master koleksi 14 Bukti Digital PKKM
        $buktiList = [
            [
                'id' => 'perangkat-guru',
                'nama' => 'Display Monitoring Perangkat Pembelajaran Guru (RPP, Modul Ajar, Silabus)',
                'kategori' => 'Kurikulum & Pembelajaran',
                'indikator' => 'Indikator 1.4.1 & 2.1.3 (Standar Proses & Kurikulum)',
                'komponen' => 'Komponen 1 & 2',
                'icon' => 'book-open',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'url' => '/admin/pkkm/cetak-bukti/perangkat-guru',
                'deskripsi' => 'Portal audit & display digital kelengkapan 404 dokumen perangkat pembelajaran (RPP, Modul Ajar, Silabus/ATP, Prota, Promes, KKTP) dewan guru dengan tombol buka PDF asli.',
                'target_keywords' => ['rpp', 'perangkat pembelajaran', 'pengembangan silabus', 'silabus', 'modul ajar', 'program tahunan', 'program semester', 'promes', 'prota', 'kktp']
            ],
            [
                'id' => 'website-email',
                'nama' => 'Dokumen Domain & Email Dinas Madrasah (masmifhda.sch.id)',
                'kategori' => 'Sarana TIK & Publikasi',
                'indikator' => 'Indikator 1.4.3 & 1.6.1',
                'komponen' => 'Komponen 1',
                'icon' => 'globe',
                'color' => '#ea580c',
                'bg' => '#fff7ed',
                'url' => '/admin/pkkm/cetak-bukti/website-email',
                'deskripsi' => 'Lembar pengesahan web resmi madrasah, panel domain sch.id, dan akun email dinas lembaga @masmifhda.sch.id.',
                'target_keywords' => ['website', 'email dinas', 'domain', 'email', 'web madrasah', 'portal']
            ],
            [
                'id' => 'jaringan-internet',
                'nama' => 'Profil Jaringan & Speedtest Internet Madrasah',
                'kategori' => 'Sarana TIK & Publikasi',
                'indikator' => 'Indikator 1.4.3 & 1.6.1',
                'komponen' => 'Komponen 1',
                'icon' => 'wifi',
                'color' => '#0284c7',
                'bg' => '#f0f9ff',
                'url' => '/admin/pkkm/cetak-bukti/jaringan-internet',
                'deskripsi' => 'Gauge Speedtest 52.4 Mbps, data ISP Indihome Fiber, foto perangkat ONT/Switch & checklist sarana.',
                'target_keywords' => ['langganan internet', 'jaringan wifi', 'speedtest', 'internet', 'wifi']
            ],
            [
                'id' => 'absensi-elektronik',
                'nama' => 'Rekap Presensi Digital Dewan Guru & Tendik',
                'kategori' => 'Ketenagaan & Disiplin',
                'indikator' => 'Indikator 1.5.1',
                'komponen' => 'Komponen 1',
                'icon' => 'user-check',
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
                'url' => '/admin/pkkm/cetak-bukti/absensi-elektronik',
                'deskripsi' => 'KPI tingkat kehadiran 98.4%, tabel absensi digital per guru & stempel server GH-SSS.',
                'target_keywords' => ['daftar hadir guru', 'presensi harian', 'presensi', 'kehadiran guru', 'absen']
            ],
            [
                'id' => 'struktur-organisasi',
                'nama' => 'Bagan Struktur Organisasi & Tupoksi Madrasah',
                'kategori' => 'Kelembagaan & Regulasi',
                'indikator' => 'Indikator 1.1.1 & 1.1.2',
                'komponen' => 'Komponen 1',
                'icon' => 'network',
                'color' => '#7c3aed',
                'bg' => '#f5f3ff',
                'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi',
                'deskripsi' => 'Diagram pohon organisasi visual murni CSS (Yayasan, Komite, Kamad, TU, 4 Waka, Guru) & Tupoksi.',
                'target_keywords' => ['struktur organisasi', 'tupoksi', 'organisasi madrasah', 'uraian tugas']
            ],
            [
                'id' => 'inventaris-tik',
                'nama' => 'Kartu Inventaris Sarana TIK & Lab Komputer',
                'kategori' => 'Sarana & Prasarana',
                'indikator' => 'Indikator 1.4.3 & 2.5.1',
                'komponen' => 'Komponen 1 & 2',
                'icon' => 'monitor',
                'color' => '#0891b2',
                'bg' => '#ecfeff',
                'url' => '/admin/pkkm/cetak-bukti/inventaris-tik',
                'deskripsi' => 'Daftar 32 unit TIK siap pakai (Server, PC Lab ANBK, Laptop, Proyektor, Gigabit Switch & UPS).',
                'target_keywords' => ['inventaris barang', 'inventaris tik', 'sarana prasarana', 'inventaris', 'laboratorium komputer', 'sarana']
            ],
            [
                'id' => 'jadwal-kegiatan',
                'nama' => 'Matriks Kalender Akademik & Jadwal Kerja',
                'kategori' => 'Kurikulum & Pembelajaran',
                'indikator' => 'Indikator 1.1.1, 1.4.2, 2.1.1',
                'komponen' => 'Komponen 1 & 2',
                'icon' => 'calendar',
                'color' => '#2563eb',
                'bg' => '#eff6ff',
                'url' => '/admin/pkkm/cetak-bukti/jadwal-kegiatan',
                'deskripsi' => 'Timeline 12 bulan (Juli-Juni) sinkron SIAKAD mengacu SK Dirjen Pendis No. 4860 & KMA 450.',
                'target_keywords' => ['kalender pendidikan', 'jadwal pembelajaran', 'jadwal kegiatan', 'jadwal pelajaran', 'pembagian jam mengajar']
            ],
            [
                'id' => 'visi-misi',
                'nama' => 'Display Visi, Misi & Sasaran Mutu Madrasah',
                'kategori' => 'Kelembagaan & Regulasi',
                'indikator' => 'Indikator 1.2.1 & 2.1.2',
                'komponen' => 'Komponen 1 & 2',
                'icon' => 'award',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'url' => '/admin/pkkm/cetak-bukti/visi-misi',
                'deskripsi' => 'Plakat plang kelembagaan zamrud-emas, Visi resmi, 5 butir Misi & 4 pilar mutu lulusan SKL.',
                'target_keywords' => ['visi misi', 'sosialisasi visi misi', 'visi', 'misi', 'tujuan madrasah']
            ],
            [
                'id' => 'perpustakaan',
                'nama' => 'Profil & Rekap Layanan Perpustakaan / Pojok Baca',
                'kategori' => 'Kurikulum & Pembelajaran',
                'indikator' => 'Indikator 2.2.2 & 2.5.2',
                'komponen' => 'Komponen 2',
                'icon' => 'book',
                'color' => '#d97706',
                'bg' => '#fffbeb',
                'url' => '/admin/pkkm/cetak-bukti/perpustakaan',
                'deskripsi' => 'Statistik 1.565 eksemplar buku DDC, rasio buku, inovasi pojok baca kelas & tren kunjungan.',
                'target_keywords' => ['perpustakaan', 'pojok baca', 'buku teks', 'buku pegangan']
            ],
            [
                'id' => 'lingkungan-sehat',
                'nama' => 'Portofolio Lingkungan Sehat, Asri & Ramah Anak',
                'kategori' => 'Sarana & Lingkungan',
                'indikator' => 'Indikator 2.3.2 & 2.5.1',
                'komponen' => 'Komponen 2',
                'icon' => 'leaf',
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
                'url' => '/admin/pkkm/cetak-bukti/lingkungan-sehat',
                'deskripsi' => 'Galeri 6 kuadran: pagar keamanan, taman rindang, wastafel air mengalir, ventilasi kelas & checklist sanitasi.',
                'target_keywords' => ['lingkungan madrasah', 'bersih dan asri', 'sanitasi', 'lingkungan', 'uks', 'kebersihan']
            ],
            [
                'id' => 'supervisi-guru',
                'nama' => 'Scorecard Supervisi Guru & Tindak Lanjut',
                'kategori' => 'Supervisi & Tendik',
                'indikator' => 'Indikator 4.1, 4.2 & 4.3',
                'komponen' => 'Komponen 4',
                'icon' => 'clipboard-check',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'url' => '/admin/pkkm/cetak-bukti/supervisi-guru',
                'deskripsi' => 'Matriks penilaian guru (modul ajar, KBM, TIK, asesmen), skor terverifikasi & tindak lanjut dari sistem supervisi kurikulum.',
                'target_keywords' => ['supervisi', 'supervisor', 'supervise', 'tindak lanjut', 'rekomendasi hasil supervisi']
            ],
            [
                'id' => 'ekstrakurikuler',
                'nama' => 'Program & Rekapitulasi Ekstrakurikuler Minat Bakat',
                'kategori' => 'Kesiswaan & Prestasi',
                'indikator' => 'Indikator 2.6.2',
                'komponen' => 'Komponen 2',
                'icon' => 'activity',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'url' => '/admin/pkkm/cetak-bukti/ekstrakurikuler',
                'deskripsi' => 'Matriks 6 cabang ekskul (Pramuka, Tahfidz, Hadroh, Silat, Futsal, UKS), jadwal rutin & 3 pilar pembinaan.',
                'target_keywords' => ['ekstrakurikuler', 'ekskul', 'pramuka', 'minat bakat']
            ],
            [
                'id' => 'prestasi-siswa',
                'nama' => 'Rekapitulasi Prestasi & Piagam Kejuaraan Siswa',
                'kategori' => 'Kesiswaan & Prestasi',
                'indikator' => 'Indikator 2.3.3',
                'komponen' => 'Komponen 2',
                'icon' => 'award',
                'color' => '#d97706',
                'bg' => '#fffbeb',
                'url' => '/admin/pkkm/cetak-bukti/prestasi-siswa',
                'deskripsi' => 'Rekapitulasi 14 raihan juara (KSM, MTQ, Hadroh, Silat, Pramuka), nomor piagam resmi & 4 highlight piala.',
                'target_keywords' => ['prestasi siswa', 'prestasi akademik', 'kejuaraan', 'prestasi', 'lomba']
            ],
            [
                'id' => 'kewirausahaan',
                'nama' => 'Unit Usaha Kewirausahaan & Kemitraan Lembaga',
                'kategori' => 'Kewirausahaan & MoU',
                'indikator' => 'Indikator 3.1 & 3.3',
                'komponen' => 'Komponen 3',
                'icon' => 'shopping-bag',
                'color' => '#059669',
                'bg' => '#ecfdf5',
                'url' => '/admin/pkkm/cetak-bukti/kewirausahaan',
                'deskripsi' => 'Profil 3 unit usaha (Koperasi, Kantin Sehat, Hidroponik), omzet Rp 14,8 Jt/bln & 4 MoU kemitraan.',
                'target_keywords' => ['kewirausahaan', 'koperasi', 'kantin', 'mou kemitraan', 'wirausaha', 'kemitraan']
            ]
        ];

        // Ambil daftar master berkas untuk opsi penautan
        $allMaster = $db->query("SELECT id, nama_berkas FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil file-file link yang saat ini tertaut di pkkm_master_berkas_file
        $attachedFiles = $db->query("SELECT id, master_berkas_id, url, nama_file_asli FROM pkkm_master_berkas_file WHERE tipe = 'link'")->fetchAll(PDO::FETCH_ASSOC);

        $attachedMap = [];
        $masterNameMap = [];
        foreach ($allMaster as $m) {
            $masterNameMap[$m['id']] = $m['nama_berkas'];
        }

        foreach ($attachedFiles as $af) {
            $af['nama_master'] = $masterNameMap[$af['master_berkas_id']] ?? 'Master Berkas #' . $af['master_berkas_id'];
            $attachedMap[$af['url']][] = $af;
        }

        // Hitung total terpasang
        $totalAttached = 0;
        foreach ($buktiList as &$b) {
            $b['attached_to'] = $attachedMap[$b['url']] ?? [];
            $b['is_attached'] = !empty($b['attached_to']);
            if ($b['is_attached']) {
                $totalAttached++;
            }
            
            // Rekomendasi master berkas berdasarkan keywords
            $matchedMaster = [];
            foreach ($allMaster as $m) {
                foreach ($b['target_keywords'] as $kw) {
                    if (stripos($m['nama_berkas'], $kw) !== false) {
                        $matchedMaster[] = $m;
                        break;
                    }
                }
            }
            $b['recommended_masters'] = $matchedMaster;
        }
        unset($b);

        self::render('pkkm_bukti_digital.php', 'Bukti Digital & TIK PKKM', [
            'active_module' => 'pkkm',
            'active_page' => 'bukti_digital',
            'buktiList' => $buktiList,
            'allMaster' => $allMaster,
            'totalAttached' => $totalAttached
        ]);
    }

    public static function attachBuktiDigital() {
        self::checkAuth();
        $db = Database::connect('core');
        $master_id = (int)($_POST['master_berkas_id'] ?? 0);
        $url = trim($_POST['url'] ?? '');
        $nama_file = trim($_POST['nama_file'] ?? 'Bukti Digital PKKM');

        if ($master_id > 0 && $url !== '') {
            $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas_file WHERE master_berkas_id = ? AND url = ?");
            $stmt->execute([$master_id, $url]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$master_id, $url, $nama_file]);
            }
            header("Location: /admin/pkkm/bukti-digital?msg=attached");
        } else {
            header("Location: /admin/pkkm/bukti-digital?msg=error");
        }
        exit;
    }

    public static function detachBuktiDigital() {
        self::checkAuth();
        $db = Database::connect('core');
        $file_id = (int)($_POST['file_id'] ?? 0);
        $url = trim($_POST['url'] ?? '');

        if ($file_id > 0) {
            $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE id = ?")->execute([$file_id]);
            header("Location: /admin/pkkm/bukti-digital?msg=detached");
        } elseif ($url !== '') {
            $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE url = ?")->execute([$url]);
            header("Location: /admin/pkkm/bukti-digital?msg=detached");
        } else {
            header("Location: /admin/pkkm/bukti-digital?msg=error");
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_website_email_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_jaringan_internet_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_absensi_elektronik_pdf.php';
    }

    public static function cetakBuktiStrukturOrganisasi() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $bagan = [];
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $rows = $db->query("SELECT * FROM struktur_organisasi ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $bagan[$r['posisi_key']] = $r;
            }
        } catch (\Exception $e) {}

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_struktur_organisasi_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_inventaris_tik_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_jadwal_kegiatan_pdf.php';
    }

    public static function cetakBuktiVisiMisi() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $pengawas_list = [];
        $is_pkkm_linked = false;
        $linked_count = 0;
        $activeTab = $_GET['tab'] ?? 'dokumen';
        $savedNotes = null;

        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            
            // Cek keterkaitan dengan instrumen PKKM
            $url = '/admin/pkkm/cetak-bukti/visi-misi';
            $inBerkas = (int)$db->query("SELECT COUNT(*) FROM pkkm_berkas WHERE url = '$url'")->fetchColumn();
            $inMaster = (int)$db->query("SELECT COUNT(*) FROM pkkm_master_berkas_file WHERE url = '$url'")->fetchColumn();
            $linked_count = $inBerkas + $inMaster;
            $is_pkkm_linked = $linked_count > 0;

            // Daftar pengawas aktif
            $pengawas_list = $db->query("SELECT * FROM pkkm_pengawas ORDER BY peran ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

            // Ambil catatan verifikasi pengawas yang tersimpan
            $notesFile = __DIR__ . '/../../storage/pkkm_visi_misi_notes.json';
            if (file_exists($notesFile)) {
                $savedNotes = json_decode(file_get_contents($notesFile), true);
            }
        } catch (\Exception $e) {}

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_visi_misi_pdf.php';
    }

    public static function attachVisiMisiToPkkm() {
        self::checkAuth();
        $db = Database::connect('core');
        $url = '/admin/pkkm/cetak-bukti/visi-misi';
        $nama_file = 'Portal Monitoring & Bukti Visi Misi Madrasah (Plang, SK Kamad & Rekomendasi Pengawas)';

        $count = 0;

        // 1. Tautkan langsung ke pkkm_berkas untuk Indikator 1.2.1 (ID 4) dan 2.1.2 (ID 27) jika ada
        $indTargets = [4, 27];
        foreach ($indTargets as $indId) {
            $chk = $db->prepare("SELECT id FROM pkkm_berkas WHERE indikator_id = ? AND url = ?");
            $chk->execute([$indId, $url]);
            if (!$chk->fetch()) {
                $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$indId, $url, $nama_file]);
                $count++;
            }
        }

        // 2. Tautkan juga ke Master Berkas terkait visi misi (ID 11, 231, 232 atau pencarian kata kunci)
        $masters = $db->query("
            SELECT id FROM pkkm_master_berkas 
            WHERE nama_berkas LIKE '%visi%misi%' 
               OR nama_berkas LIKE '%visi dan misi%' 
               OR id IN (11, 231, 232)
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($masters as $m) {
            $mid = $m['id'];
            $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas_file WHERE master_berkas_id = ? AND url = ?");
            $stmt->execute([$mid, $url]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$mid, $url, $nama_file]);
                $count++;
            }
        }

        header("Location: /admin/pkkm/cetak-bukti/visi-misi?msg=attached&count=" . $count);
        exit;
    }

    public static function detachVisiMisiFromPkkm() {
        self::checkAuth();
        $db = Database::connect('core');
        $url = '/admin/pkkm/cetak-bukti/visi-misi';
        
        $db->prepare("DELETE FROM pkkm_berkas WHERE url = ?")->execute([$url]);
        $db->prepare("DELETE FROM pkkm_master_berkas_file WHERE url = ?")->execute([$url]);

        header("Location: /admin/pkkm/cetak-bukti/visi-misi?msg=detached");
        exit;
    }

    public static function saveCatatanPengawasVisiMisi() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $storageDir = __DIR__ . '/../../storage';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }

        $data = [
            'updated_at' => date('Y-m-d H:i:s'),
            'nama_penilai' => $_POST['nama_penilai'] ?? 'Pengawas Penilai',
            'status_verifikasi' => $_POST['status_verifikasi'] ?? 'Sangat Baik',
            'skor_rekomendasi' => $_POST['skor_rekomendasi'] ?? '4',
            'catatan' => $_POST['catatan'] ?? '',
            'checklist' => $_POST['chk'] ?? []
        ];

        file_put_contents($storageDir . '/pkkm_visi_misi_notes.json', json_encode($data, JSON_PRETTY_PRINT));

        // Simpan juga skor rekomendasi jika dipilih ke pkkm_nilai untuk indikator 4 (1.2.1)
        if (isset($_POST['simpan_skor_pkkm']) && $_POST['simpan_skor_pkkm'] == '1') {
            try {
                $db = Database::connect('core');
                $skor = (int)($_POST['skor_rekomendasi'] ?? 4);
                $db->prepare("INSERT INTO pkkm_nilai (indikator_id, skor) VALUES (4, ?) ON DUPLICATE KEY UPDATE skor = ?")
                   ->execute([$skor, $skor]);
            } catch (\Exception $e) {}
        }

        $isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
        $redir = $isPengawas ? '/pengawas/pkkm/cetak-bukti/visi-misi?tab=pengawas&msg=saved' : '/admin/pkkm/cetak-bukti/visi-misi?tab=pengawas&msg=saved';
        header("Location: " . $redir);
        exit;
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_perpustakaan_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_lingkungan_sehat_pdf.php';
    }

    public static function cetakBuktiSupervisiGuru() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        
        $inst = null;
        $kamad = null;
        $gurus = [];
        $supervisiData = [];
        $pengawas_list = [];
        $savedNotes = null;
        $is_pkkm_linked = false;
        $linked_count = 0;
        $activeTab = $_GET['tab'] ?? 'instrumen';

        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $gurus = $db->query("SELECT * FROM guru WHERE is_kamad = 0 ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            // Pengawas aktif
            try {
                $pengawas_list = $db->query("SELECT * FROM pkkm_pengawas ORDER BY peran ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}

            // Cek keterkaitan dengan instrumen PKKM
            $inBerkas = (int)$db->query("SELECT COUNT(*) FROM pkkm_berkas WHERE url IN ('/admin/pkkm/monitoring-supervisi', '/admin/pkkm/cetak-bukti/supervisi-guru')")->fetchColumn();
            $inMaster = (int)$db->query("SELECT COUNT(*) FROM pkkm_master_berkas_file WHERE url IN ('/admin/pkkm/monitoring-supervisi', '/admin/pkkm/cetak-bukti/supervisi-guru')")->fetchColumn();
            $linked_count = $inBerkas + $inMaster;
            $is_pkkm_linked = $linked_count > 0;

            // Ambil catatan verifikasi pengawas yang tersimpan
            $notesFile = __DIR__ . '/../../storage/pkkm_supervisi_notes.json';
            if (file_exists($notesFile)) {
                $savedNotes = json_decode(file_get_contents($notesFile), true);
            }

            // Ambil data evaluasi supervisi kelas riil dari kurikulum
            $stmt_jadwal = $db->query("
                SELECT sk.*, g.nama as nama_guru, g.nip as guru_nip, sp.nama as nama_supervisor 
                FROM kurikulum_supervisi_kelas sk
                JOIN guru g ON sk.guru_id = g.id
                JOIN guru sp ON sk.supervisor_id = sp.id
                ORDER BY sk.tanggal DESC
            ");
            $supervisiData = $stmt_jadwal ? $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC) : [];

            try {
                $siakad_db = Database::connect('siakad');
                $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(PDO::FETCH_KEY_PAIR);
                $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(PDO::FETCH_KEY_PAIR);
                foreach ($supervisiData as &$j) {
                    $j['nama_mapel'] = $mapels[$j['mapel_id']] ?? 'Mata Pelajaran';
                    $j['nama_kelas'] = $kelasList[$j['kelas_id']] ?? '-';
                }
                unset($j);
            } catch (\Exception $e) {}

            // Ambil rincian skor per kategori riil dari kurikulum_supervisi_hasil
            $hasilKategori = [];
            try {
                $stmt_hasil = $db->query("
                    SELECT h.supervisi_id, i.kategori, SUM(h.skor) as total_skor, SUM(i.bobot_maksimal) as total_bobot
                    FROM kurikulum_supervisi_hasil h
                    JOIN kurikulum_instrumen_supervisi i ON h.instrumen_id = i.id
                    WHERE h.tipe_supervisi = 'Kelas'
                    GROUP BY h.supervisi_id, i.kategori
                ");
                if ($stmt_hasil) {
                    while ($hk = $stmt_hasil->fetch(PDO::FETCH_ASSOC)) {
                        $sid = (int)$hk['supervisi_id'];
                        $kat = strtoupper(trim($hk['kategori']));
                        $pct = ($hk['total_bobot'] > 0) ? round(($hk['total_skor'] / $hk['total_bobot']) * 100, 1) : 0;
                        
                        $key = 'lainnya';
                        if (stripos($kat, 'PERSIAPAN') !== false || stripos($kat, 'PERENCANAAN') !== false) {
                            $key = 'persiapan';
                        } elseif (stripos($kat, 'PENDAHULUAN') !== false || stripos($kat, 'AWAL') !== false) {
                            $key = 'pendahuluan';
                        } elseif (stripos($kat, 'INTI') !== false || stripos($kat, 'KBM') !== false) {
                            $key = 'inti';
                        } elseif (stripos($kat, 'PENUTUP') !== false || stripos($kat, 'AKHIR') !== false) {
                            $key = 'penutup';
                        }
                        $hasilKategori[$sid][$key] = $pct;
                    }
                }
            } catch (\Exception $e) {}

            $instrumenList = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_supervisi_guru_pdf.php';
    }

    public static function attachSupervisiToPkkm() {
        self::checkAuth();
        $db = Database::connect('core');
        $url = '/admin/pkkm/monitoring-supervisi';
        $nama_file = 'Portal Monitoring & Audit Hasil Supervisi GTK (Instrumen, Laporan & RTL)';

        $count = 0;

        // 1. Tautkan langsung ke pkkm_berkas untuk seluruh butir indikator Komponen 4
        $komp4Inds = $db->query("SELECT id FROM pkkm_indikator WHERE komponen_id = 4 OR id IN (40, 41, 42, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97)")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($komp4Inds as $indId) {
            $chk = $db->prepare("SELECT id FROM pkkm_berkas WHERE indikator_id = ? AND (url = ? OR url = '/admin/pkkm/cetak-bukti/supervisi-guru')");
            $chk->execute([$indId, $url]);
            if (!$chk->fetch()) {
                $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$indId, $url, $nama_file]);
                $count++;
            }
        }

        // 2. Tautkan ke master berkas PKKM yang relevan dengan supervisi GTK
        $targets = $db->query("
            SELECT id, nama_berkas FROM pkkm_master_berkas 
            WHERE nama_berkas LIKE '%supervisi%' 
               OR nama_berkas LIKE '%supervisor%' 
               OR nama_berkas LIKE '%tindak lanjut%'
               OR nama_berkas LIKE '%instrumen%'
               OR id IN (23, 24, 30, 50, 76, 99, 102, 106, 127, 199, 213)
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($targets as $t) {
            $mid = $t['id'];
            $stmt = $db->prepare("SELECT id FROM pkkm_master_berkas_file WHERE master_berkas_id = ? AND (url = ? OR url = '/admin/pkkm/cetak-bukti/supervisi-guru')");
            $stmt->execute([$mid, $url]);
            if (!$stmt->fetch()) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())")
                   ->execute([$mid, $url, $nama_file]);
                $count++;
            }
        }

        header("Location: /admin/pkkm/monitoring-supervisi?msg=attached&count=" . $count);
        exit;
    }

    public static function detachSupervisiFromPkkm() {
        self::checkAuth();
        $db = Database::connect('core');
        $db->query("DELETE FROM pkkm_berkas WHERE url IN ('/admin/pkkm/monitoring-supervisi', '/admin/pkkm/cetak-bukti/supervisi-guru')");
        $db->query("DELETE FROM pkkm_master_berkas_file WHERE url IN ('/admin/pkkm/monitoring-supervisi', '/admin/pkkm/cetak-bukti/supervisi-guru')");
        header("Location: /admin/pkkm/monitoring-supervisi?msg=detached");
        exit;
    }

    public static function saveCatatanPengawasSupervisi() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $storageDir = __DIR__ . '/../../storage';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }

        $data = [
            'updated_at' => date('Y-m-d H:i:s'),
            'nama_penilai' => $_POST['nama_penilai'] ?? 'Pengawas Pembina PKKM',
            'status_verifikasi' => $_POST['status_verifikasi'] ?? 'Sangat Memadai',
            'skor_rekomendasi' => $_POST['skor_rekomendasi'] ?? '4',
            'catatan' => $_POST['catatan'] ?? '',
            'checklist' => $_POST['chk'] ?? []
        ];

        file_put_contents($storageDir . '/pkkm_supervisi_notes.json', json_encode($data, JSON_PRETTY_PRINT));

        // Simpan juga skor rekomendasi jika dipilih ke pkkm_nilai untuk indikator Komponen 4
        if (isset($_POST['simpan_skor_pkkm']) && $_POST['simpan_skor_pkkm'] == '1') {
            try {
                $db = Database::connect('core');
                $skor = (int)($_POST['skor_rekomendasi'] ?? 4);
                $indTargetIds = [87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 40, 41, 42];
                foreach ($indTargetIds as $targetIndId) {
                    $chk = $db->query("SELECT id FROM pkkm_indikator WHERE id = $targetIndId")->fetch();
                    if ($chk) {
                        $db->prepare("INSERT INTO pkkm_nilai (indikator_id, skor) VALUES (?, ?) ON DUPLICATE KEY UPDATE skor = ?")
                           ->execute([$targetIndId, $skor, $skor]);
                    }
                }
            } catch (\Exception $e) {}
        }

        $isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
        $redir = $isPengawas ? '/pengawas/pkkm/monitoring-supervisi?tab=pengawas&msg=saved' : '/admin/pkkm/monitoring-supervisi?tab=pengawas&msg=saved';
        header("Location: " . $redir);
        exit;
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_ekstrakurikuler_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_prestasi_siswa_pdf.php';
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

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_kewirausahaan_pdf.php';
    }

    public static function cetakBuktiPerangkatGuru() {
        self::checkAuthBukti();
        $db = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        
        $inst = null;
        $kamad = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            $kamad = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Global stats
        $stats = [
            'total'      => 0,
            'modul'      => 0,
            'atp'        => 0,
            'prota'      => 0,
            'kktp'       => 0,
            'guru_count' => 0
        ];

        try {
            $stats['total']      = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran")->fetchColumn();
            $stats['modul']      = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%Modul%' OR jenis_perangkat LIKE '%RPP%'")->fetchColumn();
            $stats['atp']        = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%ATP%' OR jenis_perangkat LIKE '%Silabus%'")->fetchColumn();
            $stats['prota']      = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%Prota%' OR jenis_perangkat LIKE '%Promes%'")->fetchColumn();
            $stats['kktp']       = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%KKTP%' OR jenis_perangkat LIKE '%Capaian%'")->fetchColumn();
            $stats['guru_count'] = (int)$db_siakad->query("SELECT COUNT(DISTINCT guru_id) FROM perangkat_pembelajaran")->fetchColumn();
        } catch (\Exception $e) {}

        // Matriks guru
        $gurus = $db->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $counts = $db_siakad->query("
            SELECT guru_id, 
                   COUNT(*) as total,
                   SUM(CASE WHEN jenis_perangkat LIKE '%Modul%' OR jenis_perangkat LIKE '%RPP%' THEN 1 ELSE 0 END) as modul_cnt,
                   SUM(CASE WHEN jenis_perangkat LIKE '%ATP%' OR jenis_perangkat LIKE '%Silabus%' THEN 1 ELSE 0 END) as atp_cnt,
                   SUM(CASE WHEN jenis_perangkat LIKE '%Prota%' OR jenis_perangkat LIKE '%Promes%' THEN 1 ELSE 0 END) as prota_cnt,
                   SUM(CASE WHEN jenis_perangkat LIKE '%KKTP%' OR jenis_perangkat LIKE '%Capaian%' THEN 1 ELSE 0 END) as kktp_cnt
            FROM perangkat_pembelajaran
            GROUP BY guru_id
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $guruCountMap = [];
        foreach ($counts as $c) {
            $guruCountMap[$c['guru_id']] = $c;
        }

        $assigns = $db_siakad->query("
            SELECT m.guru_id, m.kelas_id, m.mapel_id, k.nama_kelas, mp.nama_mapel
            FROM mengajar m
            JOIN core_db.kelas k ON k.id = m.kelas_id
            JOIN mapel mp ON mp.id = m.mapel_id
            UNION
            SELECT jp.guru_id, jp.kelas_id, jp.mata_pelajaran_id as mapel_id, k.nama_kelas, mp.nama_mapel
            FROM jadwal_pelajaran jp
            JOIN core_db.kelas k ON k.id = jp.kelas_id
            JOIN mapel mp ON mp.id = jp.mata_pelajaran_id
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $guruAssignMap = [];
        foreach ($assigns as $a) {
            $gid = $a['guru_id'];
            if (!isset($guruAssignMap[$gid])) {
                $guruAssignMap[$gid] = ['mapel' => [], 'kelas' => []];
            }
            if (!in_array($a['nama_mapel'], $guruAssignMap[$gid]['mapel'])) $guruAssignMap[$gid]['mapel'][] = $a['nama_mapel'];
            if (!in_array($a['nama_kelas'], $guruAssignMap[$gid]['kelas'])) $guruAssignMap[$gid]['kelas'][] = $a['nama_kelas'];
        }

        $matriksGuru = [];
        foreach ($gurus as $g) {
            $gid = $g['id'];
            $cnt = $guruCountMap[$gid] ?? ['total' => 0, 'modul_cnt' => 0, 'atp_cnt' => 0, 'prota_cnt' => 0, 'kktp_cnt' => 0];
            $assign = $guruAssignMap[$gid] ?? ['mapel' => ['-'], 'kelas' => ['-']];
            if ($cnt['total'] > 0) {
                $matriksGuru[] = [
                    'id'        => $gid,
                    'nama'      => $g['nama'],
                    'nip'       => $g['nip'] ?: '-',
                    'mapel'     => implode(', ', $assign['mapel']),
                    'kelas'     => implode(', ', $assign['kelas']),
                    'modul_cnt' => $cnt['modul_cnt'],
                    'atp_cnt'   => $cnt['atp_cnt'],
                    'prota_cnt' => $cnt['prota_cnt'],
                    'kktp_cnt'  => $cnt['kktp_cnt'],
                    'total'     => $cnt['total'],
                    'status'    => 'Lengkap (Terverifikasi)'
                ];
            }
        }

        // Filter katalog perangkat untuk penelaahan pengawas
        $filter_guru  = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_mapel = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        $filter_jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';
        $q            = isset($_GET['q']) ? trim($_GET['q']) : '';
        $activeTab    = isset($_GET['tab']) ? trim($_GET['tab']) : 'hierarki';

        // Ambil daftar mapel unik yang memiliki perangkat
        $daftarMapel = [];
        try {
            $daftarMapel = $db_siakad->query("
                SELECT DISTINCT m.id, m.nama_mapel 
                FROM perangkat_pembelajaran p 
                JOIN mapel m ON m.id = p.mapel_id 
                ORDER BY m.nama_mapel ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $where = ["1=1"];
        $params = [];
        if ($filter_guru > 0) {
            $where[] = "p.guru_id = :guru_id";
            $params[':guru_id'] = $filter_guru;
        }
        if ($filter_mapel > 0) {
            $where[] = "p.mapel_id = :mapel_id";
            $params[':mapel_id'] = $filter_mapel;
        }
        if (!empty($filter_jenis)) {
            $where[] = "p.jenis_perangkat = :jenis";
            $params[':jenis'] = $filter_jenis;
        }
        if (!empty($q)) {
            $where[] = "(p.judul LIKE :q OR p.nama_file LIKE :q OR g.nama LIKE :q OR m.nama_mapel LIKE :q)";
            $params[':q'] = "%$q%";
        }

        // Ambil semua berkas perangkat tanpa limit sewenang-wenang (agar penilai dapat mengaudit seluruh 404+ berkas)
        $allDocsStmt = $db_siakad->prepare("
            SELECT p.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, m.nama_mapel, k.nama_kelas
            FROM perangkat_pembelajaran p
            LEFT JOIN core_db.guru g ON g.id = p.guru_id
            LEFT JOIN mapel m ON m.id = p.mapel_id
            LEFT JOIN core_db.kelas k ON k.id = p.kelas_id
            WHERE " . implode(" AND ", $where) . "
            ORDER BY g.nama ASC, m.nama_mapel ASC, k.nama_kelas ASC, p.id DESC
        ");
        $allDocsStmt->execute($params);
        $katalogPerangkat = $allDocsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Struktur hierarki: Guru -> Mapel & Kelas -> Berkas Perangkat
        $groupedGurus = [];
        foreach ($katalogPerangkat as $doc) {
            $gid = $doc['guru_id'];
            if (!isset($groupedGurus[$gid])) {
                $groupedGurus[$gid] = [
                    'id'         => $gid,
                    'nama'       => $doc['nama_guru'] ?? 'Guru',
                    'nip'        => $doc['nip_guru'] ?: '-',
                    'foto'       => $doc['foto_guru'] ?? null,
                    'total_docs' => 0,
                    'mapel_list' => []
                ];
            }
            $groupedGurus[$gid]['total_docs']++;

            $mapelKey = ($doc['nama_mapel'] ?: 'Mata Pelajaran Umum') . ' — Kelas ' . ($doc['nama_kelas'] ?: '-');
            if (!isset($groupedGurus[$gid]['mapel_list'][$mapelKey])) {
                $groupedGurus[$gid]['mapel_list'][$mapelKey] = [
                    'nama_mapel' => $doc['nama_mapel'] ?: 'Mata Pelajaran Umum',
                    'nama_kelas' => $doc['nama_kelas'] ?: '-',
                    'docs'       => []
                ];
            }
            $groupedGurus[$gid]['mapel_list'][$mapelKey]['docs'][] = $doc;
        }

        include __DIR__ . '/../../resources/views/admin/pkkm_bukti_perangkat_guru_pdf.php';
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

    private static function render($view_file, $title_page, $extra_data = []) {
        $adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');
        $adminInitial = strtoupper(substr($adminNama, 0, 2));

        extract($extra_data);
        ob_start();
        include __DIR__ . '/../../resources/views/admin/' . $view_file;
        echo ob_get_clean();
    }
}
