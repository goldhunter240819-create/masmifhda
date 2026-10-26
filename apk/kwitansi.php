<?php
require_once 'koneksi.php';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return '../' . ltrim($path, '/');
    }
}

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Akses Ditolak: Parameter tidak valid.");
}

try {
    // Ambil data pembayaran spesifik siswa yang login
    $stmt = $core_db->prepare("
        SELECT p.*, s.nama, s.nis, c.nama_kelas as kelas, t.status as status_tagihan
        FROM keuangan_komite_pembayaran p
        JOIN siswa s ON p.siswa_id = s.id
        LEFT JOIN kelas c ON s.kelas_id = c.id
        LEFT JOIN keuangan_tagihan t ON (p.siswa_id = t.siswa_id AND TRIM(p.jenis_pembayaran) = TRIM(t.nama_tagihan))
        WHERE p.id = :id AND p.siswa_id = :siswa_id
    ");
    $stmt->execute(['id' => $id, 'siswa_id' => $_SESSION['siswa_id']]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        die("Akses Ditolak: Data tidak ditemukan atau Anda tidak memiliki akses ke kwitansi ini.");
    }

    // Ambil data institusi
    $institusi = $core_db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    // Tanda tangan nama default bendahara sekolah untuk tampilan siswa
    $adminNama = 'Bendahara Sekolah';
    $is_apk = true;

    // Sertakan template kwitansi yang sama untuk konsistensi visual 100%
    include __DIR__ . '/../resources/views/admin/finance_kwitansi.php';

} catch (PDOException $e) {
    die("Terjadi kesalahan sistem: " . $e->getMessage());
}
?>
