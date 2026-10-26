<?php

namespace App\Controllers;

class PipController {
    public function index() {
        $title = "Cek Penerima PIP - MAS Miftahul Huda";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/index.php';
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function surat() {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            redirect('/pip');
            exit;
        }

        // Decrypt the token
        $id = openssl_decrypt(base64_decode($token), 'AES-128-ECB', 'pip_secret_2026');
        if (!$id) {
            redirect('/pip');
            exit;
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        if (file_exists($dataFile)) {
            $data = json_decode(file_get_contents($dataFile), true);
            $student = null;
            foreach ($data as $item) {
                if ($item['NIK'] === $id || $item['NISN'] === $id) {
                    $student = $item;
                    break;
                }
            }
        }

        if (!$student) {
            redirect('/pip');
            exit;
        }

        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';
        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
            if (isset($logs[$student['NIK']])) {
                $student['confirmed_at'] = $logs[$student['NIK']]['confirmed_at'];
            }
        }

        $db = \App\Core\Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $title = "Surat Keterangan Penerima PIP - " . $student['NAMA_SISWA'];
        ob_start();
        include __DIR__ . '/../../resources/views/pip/surat.php';
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function rapor() {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            redirect('/pip');
            exit;
        }

        // Decrypt the token
        $id = openssl_decrypt(base64_decode($token), 'AES-128-ECB', 'pip_secret_2026');
        if (!$id) {
            redirect('/pip');
            exit;
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        if (file_exists($dataFile)) {
            $data = json_decode(file_get_contents($dataFile), true);
            $student = null;
            foreach ($data as $item) {
                if ($item['NIK'] === $id || $item['NISN'] === $id) {
                    $student = $item;
                    break;
                }
            }
        }

        if (!$student) {
            redirect('/pip');
            exit;
        }

        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';
        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
            if (isset($logs[$student['NIK']])) {
                $student['confirmed_at'] = $logs[$student['NIK']]['confirmed_at'];
            }
        }

        $db = \App\Core\Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $nik = $student['NIK'] ?? '';
        $nisn = $student['NISN'] ?? '';
        if ($nik || $nisn) {
            $stmt = $db->prepare("SELECT s.*, k.nama_kelas as nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.nik = ? OR s.nisn = ? LIMIT 1");
            $stmt->execute([$nik, $nisn]);
            $siswaDb = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($siswaDb) {
                $student['SIAKAD'] = $siswaDb;
            }
        }

        $title = "Profil Rapor PIP - " . $student['NAMA_SISWA'];
        ob_start();
        include __DIR__ . '/../../resources/views/pip/rapor.php';
        $content = ob_get_clean();
        $hide_header_footer = true;
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function komparasi() {
        $lastYearFile = __DIR__ . '/../../storage/pip_data_2025.json';
        $thisYearFile = __DIR__ . '/../../storage/pip_data.json';

        $lastYearData = file_exists($lastYearFile) ? json_decode(file_get_contents($lastYearFile), true) : [];
        $thisYearData = file_exists($thisYearFile) ? json_decode(file_get_contents($thisYearFile), true) : [];

        $thisYearMap = [];
        foreach ($thisYearData as $student) {
            $nisn = str_pad($student['NISN'], 10, '0', STR_PAD_LEFT);
            $thisYearMap[$nisn] = $student;
        }

        $lastYearMap = [];
        foreach ($lastYearData as $student) {
            $lastYearMap[$student['NISN']] = $student;
        }

        $dapatLagi = [];
        $tidakDapat = [];
        $baruDapat = [];

        foreach ($lastYearData as $student) {
            if (isset($thisYearMap[$student['NISN']])) {
                $dapatLagi[] = $student;
            } else {
                $tidakDapat[] = $student;
            }
        }

        foreach ($thisYearData as $student) {
            $nisn = str_pad($student['NISN'], 10, '0', STR_PAD_LEFT);
            if (!isset($lastYearMap[$nisn])) {
                $baruDapat[] = $student;
            }
        }

        $title = "Komparasi Data PIP - MAS Miftahul Huda";
        ob_start();
        include __DIR__ . '/../../resources/views/pip/komparasi.php';
        $content = ob_get_clean();
        $hide_header_footer = true; // We use the same layout styling
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function apiCheck() {
        $query = $_POST['query'] ?? '';
        $query = trim($query);

        if (!$query) {
            echo json_encode(['status' => 'error', 'message' => 'Silahkan masukkan NIK atau NISN.']);
            exit;
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        if (!file_exists($dataFile)) {
            echo json_encode(['status' => 'error', 'message' => 'Data PIP belum tersedia.']);
            exit;
        }

        $data = json_decode(file_get_contents($dataFile), true);
        $found = null;

        foreach ($data as $item) {
            if (trim($item['NIK']) === $query || trim($item['NISN']) === $query) {
                $found = $item;
                break;
            }
        }

        if ($found) {
            $token = base64_encode(openssl_encrypt($found['NIK'], 'AES-128-ECB', 'pip_secret_2026'));
            $found['TOKEN'] = $token;
            echo json_encode(['status' => 'success', 'data' => $found]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Mohon maaf, data tidak ditemukan.']);
        }
        exit;
    }

    public function confirm() {
        $token = $_POST['token'] ?? '';
        if (!$token) {
            echo json_encode(['status' => 'error']);
            exit;
        }

        $id = openssl_decrypt(base64_decode($token), 'AES-128-ECB', 'pip_secret_2026');
        if (!$id) {
            echo json_encode(['status' => 'error']);
            exit;
        }

        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';
        $logs = [];
        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
            if (!is_array($logs)) $logs = [];
        }

        if (!isset($logs[$id])) {
            $logs[$id] = [
                'confirmed_at' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ];
            file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
        }

        echo json_encode(['status' => 'success']);
        exit;
    }

    public function verifyDoc() {
        $id = $_GET['id'] ?? '';
        if (!$id) {
            die("ID Validasi Tidak Ditemukan.");
        }

        $dataFile = __DIR__ . '/../../storage/pip_data.json';
        $logFile = __DIR__ . '/../../storage/pip_confirmations.json';

        $pipData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
        $logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

        $student = null;
        foreach ($pipData as $item) {
            if ($item['NIK'] === $id) {
                $student = $item;
                break;
            }
        }

        // Must exist in data AND must have confirmed
        $isValid = false;
        if ($student && isset($logs[$id])) {
            $student['confirmed_at'] = $logs[$id]['confirmed_at'];
            $isValid = true;
        }

        include __DIR__ . '/../../resources/views/pip/verifikasi.php';
    }
}
