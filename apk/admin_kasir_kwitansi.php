<?php
require_once 'koneksi.php';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return '../' . ltrim($path, '/');
    }
}

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Akses Ditolak: Parameter tidak valid.");
}

// Cek hak akses
$guru_id = $_SESSION['guru_id'];
$is_keuangan = false;
$user_id = $_SESSION['user_id'] ?? 0;
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'keuangan' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
if ($stmt_app->fetch()) {
    $is_keuangan = true;
} else {
    $cek_bendahara = $core_db->prepare("SELECT id FROM keuangan_akses WHERE guru_id = ?");
    $cek_bendahara->execute([$guru_id]);
    if ($cek_bendahara->fetch()) {
        $is_keuangan = true;
    }
}
if (!$is_keuangan) {
    die("Akses Ditolak: Anda tidak memiliki akses bendahara.");
}

try {
    // Ambil data pembayaran
    $stmt = $core_db->prepare("
        SELECT p.*, s.nama, s.nis, c.nama_kelas as kelas, t.status as status_tagihan
        FROM keuangan_komite_pembayaran p
        JOIN siswa s ON p.siswa_id = s.id
        LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
        LEFT JOIN kelas c ON rks.kelas_id = c.id
        LEFT JOIN keuangan_tagihan t ON (p.siswa_id = t.siswa_id AND TRIM(p.jenis_pembayaran) = TRIM(t.nama_tagihan))
        WHERE p.id = :id
    ");
    $stmt->execute(['id' => $id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        die("Akses Ditolak: Data tidak ditemukan.");
    }

    // Ambil data institusi
    $institusi = $core_db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    // Tanda tangan nama guru
    $adminNama = $_SESSION['nama'] ?? 'Bendahara';
    $is_apk = true;

    // Sertakan template kwitansi
    include __DIR__ . '/../resources/views/admin/finance_kwitansi.php';

} catch (PDOException $e) {
    die("Terjadi kesalahan sistem: " . $e->getMessage());
}
?>
