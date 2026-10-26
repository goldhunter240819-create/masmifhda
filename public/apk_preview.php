<?php
session_name("AULA_SYSTEM_PORTAL");
session_start();

// Ensure only Developer can access this
// --- BYPASS LOGIN (SECURITY RISK - FOR DEV ONLY) ---
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
    $_SESSION['role_id'] = 99;
    $_SESSION['user_id'] = 1;
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
    die("Access Denied: Only Developer can preview APK.");
}

$role = isset($_GET['role']) ? $_GET['role'] : '';

// Close the portal session so we can inject into the APK session
session_write_close();

// Open the APK session (default PHPSESSID)
session_name("PHPSESSID");
session_start();

// Connect to database to fetch dummy data
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=aula_db;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Clear previous apk roles
    unset($_SESSION['siswa_id']);
    unset($_SESSION['siswa_nama']);
    unset($_SESSION['siswa_nis']);
    unset($_SESSION['siswa_status']);
    unset($_SESSION['guru_id']);
    unset($_SESSION['guru_nama']);
    unset($_SESSION['guru_nip']);

    if ($role === 'siswa') {
        $stmt = $db->query("SELECT * FROM siswa LIMIT 1");
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($siswa) {
            $_SESSION['siswa_id'] = $siswa['id'];
            $_SESSION['siswa_nama'] = $siswa['nama'];
            $_SESSION['siswa_nis'] = $siswa['nis'];
            $_SESSION['siswa_status'] = $siswa['status'];
            $_SESSION['role_id'] = 3; 
            
            header("Location: /apk/index.php");
            exit;
        } else {
            die("Siswa dummy tidak ditemukan.");
        }
    } elseif ($role === 'guru') {
        // Fallback to any guru if the specific NIP doesn't exist
        $stmt = $db->query("SELECT * FROM guru LIMIT 1");
        $guru = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($guru) {
            $_SESSION['guru_id'] = $guru['id'];
            $_SESSION['guru_nama'] = $guru['nama'];
            $_SESSION['guru_nip'] = $guru['nip'] ?? '-';
            $_SESSION['role_id'] = 2;
            
            header("Location: /apk/guru_dashboard.php");
            exit;
        } else {
            die("Guru dummy tidak ditemukan.");
        }
    } else {
        die("Invalid role specified.");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

