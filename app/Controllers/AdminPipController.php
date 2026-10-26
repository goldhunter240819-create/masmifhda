<?php

namespace App\Controllers;

use App\Core\Database;

class AdminPipController {
    
    public function dashboard() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }

        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';

        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
        $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

        $totalSiswa = count($pipData);
        $totalConfirmed = count($logs);
        
        // Merge logs with student data
        $confirmedList = [];
        foreach ($pipData as $student) {
            $id = $student['NIK'];
            if (isset($logs[$id])) {
                $student['confirmed_at'] = $logs[$id]['confirmed_at'];
                $student['ip_address'] = $logs[$id]['ip_address'] ?? '-';
                $confirmedList[] = $student;
            }
        }

        // Sort by confirmed_at DESC
        usort($confirmedList, function($a, $b) {
            return strtotime($b['confirmed_at']) - strtotime($a['confirmed_at']);
        });

        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Dashboard PIP - Admin";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/admin_dashboard.php';
        $content = ob_get_clean();
        $hide_header_footer = true; // Use generic layout without public header/footer
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function konfirmasi() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }

        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';

        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
        $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
        
        // Merge logs with student data
        $confirmedList = [];
        foreach ($pipData as $student) {
            $id = $student['NIK'];
            if (isset($logs[$id])) {
                $student['confirmed_at'] = $logs[$id]['confirmed_at'];
                $student['ip_address'] = $logs[$id]['ip_address'] ?? '-';
                $confirmedList[] = $student;
            }
        }

        // Sort by confirmed_at DESC
        usort($confirmedList, function($a, $b) {
            return strtotime($b['confirmed_at']) - strtotime($a['confirmed_at']);
        });

        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Data Konfirmasi PIP - Admin";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/admin_konfirmasi.php';
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetakPernyataan() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }

        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin.");
        }

        $id = $_GET['id'] ?? '';
        if (!$id) {
            die("ID Siswa tidak valid.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';

        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
        $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

        if (!isset($logs[$id])) {
            die("Siswa belum mengkonfirmasi persetujuan PIP.");
        }

        $student = null;
        foreach ($pipData as $item) {
            if ($item['NIK'] === $id) {
                $student = $item;
                break;
            }
        }

        if (!$student) {
            die("Data siswa tidak ditemukan.");
        }

        $student['confirmed_at'] = $logs[$id]['confirmed_at'];
        
        $institusi = [];
        try {
            $db = Database::connect('core');
            $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {}

        // Generate the letter view
        include __DIR__ . '/../../resources/views/pip/surat_pernyataan.php';
    }

    public function kehilangan() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = \App\Core\Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }
        
        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin PIP.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Surat Kehilangan PIP - Admin";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/admin_surat_kehilangan.php';
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetakKehilangan() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        $id = $_GET['id'] ?? '';
        if (!$id) die("ID (NIK) tidak valid.");

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

        $student = null;
        foreach ($pipData as $item) {
            if ($item['NIK'] === $id) {
                $student = $item;
                break;
            }
        }

        if (!$student) {
            die("Data siswa tidak ditemukan di database PIP.");
        }
        
        $institusi = [];
        $kepsek = null;
        try {
            $db = \App\Core\Database::connect('core');
            $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            
            // Ambil data Kepsek dari tabel guru
            $kepsek = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 OR jabatan LIKE '%Kepala%' LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if (!$kepsek) {
                $kepsek = ['nama' => 'Nama Kepala Madrasah', 'nip' => '-'];
            }

            // Ambil data lengkap siswa dari tabel siswa berdasarkan NIK atau NISN
            $stmt = $db->prepare("SELECT * FROM siswa WHERE nik = ? OR nisn = ? LIMIT 1");
            $stmt->execute([$student['NIK'] ?? '', $student['NISN'] ?? '']);
            $dbStudent = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($dbStudent) {
                // Gabungkan data DB ke $student, menutupi yang kosong
                $student['NIS'] = $dbStudent['nis'] ?? '-';
                $student['TEMPAT_LAHIR'] = $dbStudent['tempat_lahir'] ?? '-';
                $student['TANGGAL_LAHIR'] = $dbStudent['tanggal_lahir'] ?? '-';
                
                // Konversi L/P ke Laki-laki / Perempuan
                if (($dbStudent['jenis_kelamin'] ?? '') === 'L') {
                    $student['JENIS_KELAMIN'] = 'Laki-Laki';
                } elseif (($dbStudent['jenis_kelamin'] ?? '') === 'P') {
                    $student['JENIS_KELAMIN'] = 'Perempuan';
                }

                $student['NAMA_AYAH'] = !empty($dbStudent['nama_ayah']) ? $dbStudent['nama_ayah'] : (!empty($dbStudent['nama_ibu']) ? $dbStudent['nama_ibu'] : '-');
                $student['NAMA_IBU_KANDUNG'] = $dbStudent['nama_ibu'] ?? '-';
                
                // Format alamat yang lebih baik
                $alamatLengkap = $dbStudent['alamat'] ?? '';
                if (!empty($dbStudent['desa'])) $alamatLengkap .= ', Desa ' . $dbStudent['desa'];
                if (!empty($dbStudent['kecamatan'])) $alamatLengkap .= ', Kec. ' . $dbStudent['kecamatan'];
                if (!empty($dbStudent['kota'])) $alamatLengkap .= ', ' . $dbStudent['kota'];
                $student['ALAMAT'] = $alamatLengkap ?: '-';
            }
        } catch (\PDOException $e) {
            if (!$kepsek) $kepsek = ['nama' => 'Nama Kepala Madrasah', 'nip' => '-'];
        }

        include __DIR__ . '/../../resources/views/pip/cetak_surat_kehilangan.php';
    }

    public function kuasa() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = \App\Core\Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }
        
        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin PIP.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Surat Kuasa PIP - Admin";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/admin_surat_kuasa.php';
        $content = ob_get_clean();

        // Menggunakan layout siakad agar CSS zamrud ter-load
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetakKuasa() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = \App\Core\Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }
        
        if (!$is_admin) {
            die("Akses ditolak. Anda bukan admin PIP.");
        }

        $id = $_GET['id'] ?? '';
        if (!$id) die("ID (NIK) tidak valid.");

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

        $student = null;
        foreach ($pipData as $p) {
            if ($p['NIK'] == $id) {
                $student = $p;
                break;
            }
        }

        if (!$student) die("Data siswa tidak ditemukan.");

        $institusi = [];
        $kepsek = null;
        try {
            $db = \App\Core\Database::connect('core');
            $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            
            // Ambil data Kepsek dari tabel guru
            $kepsek = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 OR jabatan LIKE '%Kepala%' LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if (!$kepsek) {
                $kepsek = ['nama' => 'Nama Kepala Madrasah', 'nip' => '-'];
            }

            // Ambil data lengkap siswa dari tabel siswa berdasarkan NIK atau NISN
            $stmt = $db->prepare("SELECT * FROM siswa WHERE nik = ? OR nisn = ? LIMIT 1");
            $stmt->execute([$student['NIK'] ?? '', $student['NISN'] ?? '']);
            $dbStudent = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($dbStudent) {
                // Gabungkan data DB ke $student, menutupi yang kosong
                $student['NIS'] = $dbStudent['nis'] ?? '-';
                $student['TEMPAT_LAHIR'] = $dbStudent['tempat_lahir'] ?? '-';
                $student['TANGGAL_LAHIR'] = $dbStudent['tanggal_lahir'] ?? '-';
                
                // Konversi L/P ke Laki-laki / Perempuan
                if (($dbStudent['jenis_kelamin'] ?? '') === 'L') {
                    $student['JENIS_KELAMIN'] = 'Laki-Laki';
                } elseif (($dbStudent['jenis_kelamin'] ?? '') === 'P') {
                    $student['JENIS_KELAMIN'] = 'Perempuan';
                }

                $student['NAMA_AYAH'] = !empty($dbStudent['nama_ayah']) ? $dbStudent['nama_ayah'] : (!empty($dbStudent['nama_ibu']) ? $dbStudent['nama_ibu'] : '-');
                $student['NAMA_IBU_KANDUNG'] = $dbStudent['nama_ibu'] ?? '-';
                
                // Format alamat yang lebih baik
                $alamatLengkap = $dbStudent['alamat'] ?? '';
                if (!empty($dbStudent['desa'])) $alamatLengkap .= ', Desa ' . $dbStudent['desa'];
                if (!empty($dbStudent['kecamatan'])) $alamatLengkap .= ', Kec. ' . $dbStudent['kecamatan'];
                if (!empty($dbStudent['kota'])) $alamatLengkap .= ', ' . $dbStudent['kota'];
                $student['ALAMAT'] = $alamatLengkap ?: '-';
            }
        } catch (\PDOException $e) {
            if (!$kepsek) $kepsek = ['nama' => 'Nama Kepala Madrasah', 'nip' => '-'];
        }

        // Tangkap parameter penerima kuasa dari request GET
        $kuasa_nama = $_GET['kuasa_nama'] ?? '';
        $kuasa_nik = $_GET['kuasa_nik'] ?? '';
        $kuasa_alamat = $_GET['kuasa_alamat'] ?? '';

        include __DIR__ . '/../../resources/views/pip/cetak_surat_kuasa.php';
    }
}
