<?php

namespace App\Controllers;

use App\Core\Database;

class KopSuratController {
    public function index() {
        // Authenticate
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $db = Database::connect('core');
        $stmt = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
        $kop = $stmt->fetch();

        // Siapkan variabel untuk view
        $title = "Pengaturan Kop Surat - SIAKAD";
        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 2));

        ob_start();
        include __DIR__ . '/../../resources/views/admin/kop_surat/index.php';
        $content = ob_get_clean();
        
        // Include layout utama SIAKAD
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function update() {
        // Authenticate
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = Database::connect('core');

        $nama_yayasan = $_POST['nama_yayasan'] ?? '';
        $font_yayasan = $_POST['font_yayasan'] ?? 16;
        $nama_madrasah = $_POST['nama_madrasah'] ?? '';
        $font_madrasah = $_POST['font_madrasah'] ?? 24;
        $alamat_jalan = $_POST['alamat_jalan'] ?? '';
        $telepon = $_POST['telepon'] ?? '';
        $email = $_POST['email'] ?? '';
        $website = $_POST['website'] ?? '';

        // Handle file upload
        $logoName = $_POST['current_logo'] ?? 'logo.png';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($fileInfo, $_FILES['logo']['tmp_name']);
            finfo_close($fileInfo);

            if (in_array($mime, $allowedTypes) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $newName = 'kop_logo_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $newName)) {
                    $logoName = $newName;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'File tidak valid. Pastikan format PNG/JPG dan ukuran max 2MB.']);
                exit;
            }
        }

        try {
            $stmt = $db->query("SELECT id FROM pengaturan_kop_surat LIMIT 1");
            $exists = $stmt->fetch();

            if ($exists) {
                $update = $db->prepare("UPDATE pengaturan_kop_surat SET 
                    nama_yayasan = ?, font_yayasan = ?, 
                    nama_madrasah = ?, font_madrasah = ?, 
                    alamat_jalan = ?, telepon = ?, 
                    email = ?, website = ?, logo = ? 
                    WHERE id = ?");
                $update->execute([
                    $nama_yayasan, $font_yayasan, 
                    $nama_madrasah, $font_madrasah, 
                    $alamat_jalan, $telepon, 
                    $email, $website, $logoName,
                    $exists['id']
                ]);
            } else {
                $insert = $db->prepare("INSERT INTO pengaturan_kop_surat 
                    (nama_yayasan, font_yayasan, nama_madrasah, font_madrasah, alamat_jalan, telepon, email, website, logo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert->execute([
                    $nama_yayasan, $font_yayasan, 
                    $nama_madrasah, $font_madrasah, 
                    $alamat_jalan, $telepon, 
                    $email, $website, $logoName
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Pengaturan Kop Surat berhasil disimpan.']);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
