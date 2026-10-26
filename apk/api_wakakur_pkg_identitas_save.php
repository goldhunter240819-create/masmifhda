<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = $_POST['guru_id'] ?? 0;
$pkg_id = $_POST['pkg_id'] ?? 0;
$penilai_id = $_POST['penilai_id'] ?? 0;
$periode_penilaian = $_POST['periode_penilaian'] ?? '';
$tanggal_penilaian = $_POST['tanggal_penilaian'] ?? '';
$tahun = $_POST['tahun'] ?? '';

if (!$guru_id || !$penilai_id) die("Data tidak lengkap.");

try {
    if ($pkg_id == 0) {
        // Insert new
        $stmt = $core_db->prepare("INSERT INTO kurikulum_pkg_guru (guru_id, periode_penilaian, tahun, penilai_id, tanggal_penilaian, status_selesai) VALUES (?, ?, ?, ?, ?, 'Proses')");
        $stmt->execute([
            $guru_id, $periode_penilaian, $tahun, $penilai_id, $tanggal_penilaian
        ]);
        $pkg_id = $core_db->lastInsertId();
    } else {
        // Update existing
        $stmt = $core_db->prepare("UPDATE kurikulum_pkg_guru SET periode_penilaian=?, tahun=?, penilai_id=?, tanggal_penilaian=?, status_selesai=IF(status_selesai='Belum', 'Proses', status_selesai) WHERE id=?");
        $stmt->execute([
            $periode_penilaian, $tahun, $penilai_id, $tanggal_penilaian, $pkg_id
        ]);
    }
    
    header("Location: wakakur_pkg_form.php?pkg_id=$pkg_id&kompetensi=1");
    exit;
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
