<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PortalGuruPkkmController {

    private static function getGuruInfo() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect('core');
        $guru = $db->query("SELECT id, user_id, nama, nip, foto, jabatan, status_kepegawaian FROM guru WHERE user_id = " . intval($_SESSION['user_id']))->fetch(PDO::FETCH_ASSOC);

        // Jika user adalah admin yang membuka portal guru untuk simulasi
        if (!$guru && isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])) {
            $guru = [
                'id' => 0,
                'user_id' => $_SESSION['user_id'],
                'nama' => $_SESSION['nama'] ?? 'Administrator',
                'nip' => '198001012010011001',
                'foto' => null,
                'jabatan' => 'Koordinator Tim PKKM',
                'status_kepegawaian' => 'PNS'
            ];
        }

        if (!$guru) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru/Tendik.";
            exit;
        }

        return $guru;
    }

    private static function ensureTimTables($db) {
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

    private static function compressAndUploadImage($tmp, $dest, $ext) {
        $allowedImage = ['jpg', 'jpeg', 'png'];
        if (filesize($tmp) < 512000) {
            return move_uploaded_file($tmp, $dest);
        }

        if (in_array(strtolower($ext), $allowedImage)) {
            $info = @getimagesize($tmp);
            if ($info && $info['mime'] == 'image/jpeg') {
                $image = @imagecreatefromjpeg($tmp);
                if ($image) {
                    imagejpeg($image, $dest, 60);
                    imagedestroy($image);
                    return true;
                }
            }
        }
        return move_uploaded_file($tmp, $dest);
    }

    private static function processUploadedFiles($filesArray, $tipe, $idOrIndikator, $isMaster, $nama_file_asli) {
        $db = Database::connect('core');
        $success = false;

        $imagesToMerge = [];
        $otherFiles = [];

        foreach ($filesArray['name'] as $i => $name) {
            if ($filesArray['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $tmp = $filesArray['tmp_name'][$i];
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $imagesToMerge[] = ['tmp' => $tmp, 'ext' => $ext, 'name' => $name];
                } else {
                    $otherFiles[] = ['tmp' => $tmp, 'ext' => $ext, 'name' => $name];
                }
            }
        }

        if (count($imagesToMerge) > 1) {
            require_once __DIR__ . '/../../app/Libraries/fpdf/fpdf.php';
            $pdf = new \FPDF('P', 'mm', 'A4');
            $albumFilename = 'pkkm_album_' . time() . '_' . rand(100,999) . '.pdf';
            $dest = __DIR__ . '/../../public/uploads/pkkm/' . $albumFilename;
            if (!is_dir(__DIR__ . '/../../public/uploads/pkkm')) mkdir(__DIR__ . '/../../public/uploads/pkkm', 0777, true);

            foreach ($imagesToMerge as $img) {
                $tmp = $img['tmp'];
                $ext = strtoupper($img['ext']);
                if ($ext === 'JPG') $ext = 'JPEG';
                $info = @getimagesize($tmp);
                if($info) {
                    $pdf->AddPage();
                    $w_px = $info[0]; $h_px = $info[1];
                    $ratio = min(190 / $w_px, 277 / $h_px);
                    $pdf->Image($tmp, (210 - ($w_px * $ratio)) / 2, (297 - ($h_px * $ratio)) / 2, $w_px * $ratio, $h_px * $ratio, $ext);
                }
            }
            $pdf->Output('F', $dest);

            $url = '/uploads/pkkm/' . $albumFilename;
            $nfa = !empty($nama_file_asli) ? $nama_file_asli : 'Album Foto Dokumentasi PKKM';

            if ($isMaster) {
                $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
            } else {
                $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, ?, ?, ?)")->execute([$idOrIndikator, $tipe, $url, $nfa]);
            }
            $success = true;
        } elseif (count($imagesToMerge) == 1) {
            $otherFiles[] = $imagesToMerge[0];
        }

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

    public static function instrumen() {
        $guru = self::getGuruInfo();
        $db = Database::connect('core');
        self::ensureTimTables($db);

        // Ambil penugasan guru di PKKM
        $myAssignments = [];
        $myKomponenIds = [];
        if ($guru['id'] > 0) {
            $stmtMy = $db->prepare("
                SELECT t.*, k.nama_komponen, k.urutan as komponen_urutan
                FROM pkkm_tim t
                JOIN pkkm_komponen k ON t.komponen_id = k.id
                WHERE t.guru_id = ?
                ORDER BY CAST(k.urutan AS UNSIGNED) ASC
            ");
            $stmtMy->execute([$guru['id']]);
            $myAssignments = $stmtMy->fetchAll(PDO::FETCH_ASSOC);
            foreach($myAssignments as $a) {
                $myKomponenIds[] = (int)$a['komponen_id'];
            }
        }

        $komponens = $db->query("SELECT * FROM pkkm_komponen ORDER BY CAST(urutan AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        $sub_komponens = $db->query("SELECT * FROM pkkm_sub_komponen ORDER BY CAST(SUBSTRING_INDEX(urutan, '.', 1) AS UNSIGNED), CAST(SUBSTRING_INDEX(urutan, '.', -1) AS UNSIGNED) ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        $indikators = $db->query("
            SELECT i.*, COALESCE(n.skor, 0) as skor 
            FROM pkkm_indikator i
            LEFT JOIN pkkm_nilai n ON n.indikator_id = i.id
            ORDER BY i.urutan ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $isSupervisor = !empty($_SESSION['is_kamad']) || (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99]));

        // ISOLASI KOMPONEN: Jika guru biasa, HANYA muat komponen yang ditugaskan!
        if (!$isSupervisor) {
            if (!empty($myKomponenIds)) {
                $komponens = array_values(array_filter($komponens, function($k) use ($myKomponenIds) {
                    return in_array((int)$k['id'], $myKomponenIds);
                }));
                $sub_komponens = array_values(array_filter($sub_komponens, function($sk) use ($myKomponenIds) {
                    return in_array((int)$sk['komponen_id'], $myKomponenIds);
                }));
                $indikators = array_values(array_filter($indikators, function($ind) use ($myKomponenIds) {
                    return in_array((int)$ind['komponen_id'], $myKomponenIds);
                }));
            } else {
                $komponens = [];
                $sub_komponens = [];
                $indikators = [];
            }
        }

        $berkas = $db->query("SELECT * FROM pkkm_berkas")->fetchAll(PDO::FETCH_ASSOC);
        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT * FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);
        
        $mfiles_map = [];
        foreach($master_files as $mf) {
            $mfiles_map[$mf['master_berkas_id']][] = $mf;
        }

        $data = [];
        foreach ($komponens as $k) {
            $k['sub_komponens'] = [];
            $k['indikators'] = [];
            $data[$k['id']] = $k;
        }

        $sub_map = [];
        foreach ($sub_komponens as $sub) {
            $sub['indikators'] = [];
            $sub_map[$sub['id']] = $sub;
        }

        $totalBerkasCount = 0;
        foreach ($indikators as &$ind) {
            $ind['berkas'] = [];
            foreach ($berkas as $b) {
                if ($b['indikator_id'] == $ind['id']) {
                    $b['is_master'] = false;
                    $ind['berkas'][] = $b;
                }
            }

            // Tautkan berkas dari Master Berkas
            foreach ($master_berkas as $mb) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    if (isset($mfiles_map[$mb['id']])) {
                        foreach ($mfiles_map[$mb['id']] as $mf) {
                            $ind['berkas'][] = [
                                'id' => $mf['id'],
                                'indikator_id' => $ind['id'],
                                'tipe' => $mf['tipe'],
                                'url' => $mf['url'],
                                'nama_file' => $mf['nama_file_asli'] . ' (Master)',
                                'created_at' => $mf['created_at'],
                                'is_master' => true,
                                'master_berkas_id' => $mb['id']
                            ];
                        }
                    }
                }
            }

            $totalBerkasCount += count($ind['berkas']);

            if (!empty($ind['sub_komponen_id']) && isset($sub_map[$ind['sub_komponen_id']])) {
                $sub_map[$ind['sub_komponen_id']]['indikators'][] = $ind;
            } elseif (isset($data[$ind['komponen_id']])) {
                $data[$ind['komponen_id']]['indikators'][] = $ind;
            }
        }
        unset($ind);

        foreach ($sub_map as $sub) {
            if (isset($data[$sub['komponen_id']])) {
                $data[$sub['komponen_id']]['sub_komponens'][] = $sub;
            }
        }

        $totalKomponen = count($komponens);
        $totalIndikator = count($indikators);
        $totalBerkas = $totalBerkasCount;

        $title_page = "Instrumen PKKM";
        include __DIR__ . '/../../resources/views/portal/guru/pkkm_instrumen.php';
    }

    public static function masterBerkas() {
        $guru = self::getGuruInfo();
        $db = Database::connect('core');
        self::ensureTimTables($db);

        // Ambil penugasan guru di PKKM
        $myAssignments = [];
        $myKomponenIds = [];
        if ($guru['id'] > 0) {
            $stmtMy = $db->prepare("
                SELECT t.*, k.nama_komponen, k.urutan as komponen_urutan
                FROM pkkm_tim t
                JOIN pkkm_komponen k ON t.komponen_id = k.id
                WHERE t.guru_id = ?
                ORDER BY CAST(k.urutan AS UNSIGNED) ASC
            ");
            $stmtMy->execute([$guru['id']]);
            $myAssignments = $stmtMy->fetchAll(PDO::FETCH_ASSOC);
            foreach($myAssignments as $a) {
                $myKomponenIds[] = (int)$a['komponen_id'];
            }
        }

        $master_berkas = $db->query("SELECT * FROM pkkm_master_berkas ORDER BY nama_berkas ASC")->fetchAll(PDO::FETCH_ASSOC);
        $master_files = $db->query("SELECT * FROM pkkm_master_berkas_file")->fetchAll(PDO::FETCH_ASSOC);

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

        $isSupervisor = !empty($_SESSION['is_kamad']) || (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99]));

        $filteredMasterBerkas = [];
        foreach($master_berkas as $mb) {
            $mb['files'] = isset($files_map[$mb['id']]) ? $files_map[$mb['id']] : [];
            
            $matchedInds = [];
            $relatedKomponenIds = [];
            foreach ($allIndikators as $ind) {
                if (stripos($ind['bukti_fisik'], $mb['nama_berkas']) !== false) {
                    $kode = "Indikator " . ($ind['sub_urutan'] ? $ind['sub_urutan'] . '.' . $ind['urutan'] : ($ind['komp_urutan'] ? $ind['komp_urutan'] . '.' . $ind['urutan'] : $ind['urutan']));
                    $matchedInds[] = [
                        'id' => $ind['id'],
                        'kode' => $kode,
                        'komponen' => $ind['nama_komponen'] ?? ('Komponen ' . $ind['komponen_id']),
                        'komponen_id' => (int)$ind['komponen_id']
                    ];
                    $relatedKomponenIds[] = (int)$ind['komponen_id'];
                }
            }
            $mb['matched_indicators'] = $matchedInds;
            $mb['indikators_terkait'] = $matchedInds;
            $mb['related_komponen_ids'] = array_unique($relatedKomponenIds);

            // Cek apakah berkas ini relevan dengan penugasan guru yang sedang login
            $isMyTask = false;
            if (!empty($myKomponenIds)) {
                foreach($mb['related_komponen_ids'] as $rkId) {
                    if (in_array($rkId, $myKomponenIds)) {
                        $isMyTask = true;
                        break;
                    }
                }
            }
            $mb['is_my_task'] = $isMyTask;

            // ISOLASI MASTER BERKAS: Jika guru biasa, sembunyikan berkas yang BUKAN tugas komponennya!
            if (!$isSupervisor) {
                if (!empty($myKomponenIds) && !$isMyTask) {
                    continue; // Skip dokumen komponen lain!
                } elseif (empty($myKomponenIds)) {
                    continue; // Guru belum ditugaskan
                }
            }

            // Dapatkan metadata judul kontekstual, deskripsi konkret, dan shortcut digital
            $meta = \App\Services\PkkmPanduanService::getMasterBerkasMetadata($mb['nama_berkas'], $matchedInds);
            $mb['judul_tampilan'] = $meta['judul_tampilan'];
            $mb['deskripsi'] = $meta['deskripsi'];
            $mb['kategori'] = $meta['kategori'];
            $mb['kategori_label'] = $meta['kategori_label'];
            $mb['kategori_badge'] = $meta['kategori_badge'];
            $mb['kategori_icon'] = $meta['kategori_icon'];
            $mb['bukti_digital'] = $meta['bukti_digital'];

            $filteredMasterBerkas[] = $mb;
        }

        $master_berkas = $filteredMasterBerkas;

        $totalUploaded = 0;
        $totalPending = 0;
        $totalDigitalReady = 0;
        $totalFileCount = 0;

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

        foreach($master_berkas as $mb) {
            if (!empty($mb['files'])) {
                $totalUploaded++;
                $totalFileCount += count($mb['files']);
            } else {
                $totalPending++;
            }

            if (!empty($mb['bukti_digital'])) {
                $totalDigitalReady++;
            }

            $catKey = $mb['kategori'] ?? 'umum';
            if (isset($categoryCounts[$catKey])) {
                $categoryCounts[$catKey]++;
            } else {
                $categoryCounts['umum']++;
            }
        }

        $title_page = "Daftar Berkas PKKM";
        include __DIR__ . '/../../resources/views/portal/guru/pkkm_master_berkas.php';
    }

    public static function saveBerkas() {
        self::getGuruInfo();
        $db = Database::connect('core');
        $indikator_id = (int)$_POST['indikator_id'];
        $tipe = $_POST['tipe'];

        if ($tipe === 'link') {
            $url = $_POST['url'];
            $nama_file = $_POST['nama_file'] ?? 'Tautan Bukti Fisik';
            $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, ?, ?, ?)")
               ->execute([$indikator_id, $tipe, $url, $nama_file]);
        } else {
            if (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
                $nama_file = $_POST['nama_file'] ?? null;
                self::processUploadedFiles($_FILES['file_upload'], $tipe, $indikator_id, false, $nama_file);
            }
        }
        header("Location: /guru/pkkm/instrumen?msg=saved");
        exit;
    }

    public static function deleteBerkas() {
        self::getGuruInfo();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];

        $b = $db->prepare("SELECT url, tipe FROM pkkm_berkas WHERE id = ?");
        $b->execute([$id]);
        $berkas = $b->fetch(PDO::FETCH_ASSOC);

        if ($berkas && $berkas['tipe'] == 'file' && $berkas['url']) {
            $filepath = __DIR__ . '/../../public' . $berkas['url'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
        }

        $db->prepare("DELETE FROM pkkm_berkas WHERE id = ?")->execute([$id]);
        header("Location: /guru/pkkm/instrumen?msg=deleted");
        exit;
    }

    public static function uploadMasterBerkas() {
        self::getGuruInfo();
        $db = Database::connect('core');
        $master_berkas_id = (int)($_POST['master_berkas_id'] ?? $_POST['id'] ?? 0);
        $tipe = $_POST['tipe'] ?? 'file';
        $nama_file = !empty($_POST['nama_file']) ? trim($_POST['nama_file']) : null;

        if ($tipe === 'link' && !empty($_POST['url'])) {
            $url = trim($_POST['url']);
            $nama_file = $nama_file ?: 'Tautan Eksternal';
            $db->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())")
               ->execute([$master_berkas_id, $url, $nama_file]);
        } elseif (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
            self::processUploadedFiles($_FILES['file_upload'], 'file', $master_berkas_id, true, $nama_file);
        }

        header("Location: /guru/pkkm/master-berkas?msg=uploaded");
        exit;
    }

    public static function deleteMasterBerkas() {
        self::getGuruInfo();
        $db = Database::connect('core');
        $id = (int)$_POST['id'];

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
        header("Location: /guru/pkkm/master-berkas?msg=deleted");
        exit;
    }

    public static function attachDigitalToMaster() {
        self::getGuruInfo();
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
            header("Location: /guru/pkkm/master-berkas?msg=digital_attached");
        } else {
            header("Location: /guru/pkkm/master-berkas?msg=error");
        }
        exit;
    }

    public static function attachWebsiteLink() {
        self::getGuruInfo();
        $db = Database::connect('core');
        $indikator_id = (int)($_POST['indikator_id'] ?? 0);
        $url = trim($_POST['url'] ?? 'https://masmifhda.sch.id');
        $nama_file = trim($_POST['nama_file'] ?? 'Portal Resmi Madrasah (masmifhda.sch.id)');

        if ($indikator_id > 0) {
            $db->prepare("INSERT INTO pkkm_berkas (indikator_id, tipe, url, nama_file) VALUES (?, 'link', ?, ?)")
               ->execute([$indikator_id, $url, $nama_file]);
        }
        header("Location: /guru/pkkm/instrumen?msg=website_attached");
        exit;
    }
}
