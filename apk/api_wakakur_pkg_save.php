<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$pkg_id = $_POST['pkg_id'] ?? 0;
$kompetensi_ke = $_POST['kompetensi_ke'] ?? 1;
$is_last = $_POST['is_last'] ?? 0;
$instrumen = $_POST['instrumen'] ?? [];

if (!$pkg_id) die("PKG ID tidak valid.");

// Hitung total skor
$total_skor = 0;
foreach ($instrumen as $val) {
    $total_skor += (int)$val;
}

// Maksimum skor per kompetensi = jumlah indikator * 2
// Untuk persentase, total skor / (jumlah_indikator * 2) * 100
$jumlah_indikator = count($instrumen);
$skor_max = $jumlah_indikator * 2;
$persentase = ($skor_max > 0) ? ($total_skor / $skor_max) * 100 : 0;

// Konversi 1 - 4
$nilai_konversi = 0;
if ($persentase > 0 && $persentase <= 25) $nilai_konversi = 1;
elseif ($persentase > 25 && $persentase <= 50) $nilai_konversi = 2;
elseif ($persentase > 50 && $persentase <= 75) $nilai_konversi = 3;
elseif ($persentase > 75 && $persentase <= 100) $nilai_konversi = 4;

$skor_json = json_encode($instrumen);

try {
    // Insert or Update nilai per kompetensi
    $stmt = $core_db->prepare("
        INSERT INTO kurikulum_pkg_nilai (pkg_id, kompetensi_ke, skor_json, total_skor, nilai_konversi) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE skor_json = VALUES(skor_json), total_skor = VALUES(total_skor), nilai_konversi = VALUES(nilai_konversi)
    ");
    $stmt->execute([
        $pkg_id, $kompetensi_ke, $skor_json, $total_skor, $nilai_konversi
    ]);
    
    if ($is_last) {
        // Kalkulasi Total Skor ke tabel master
        $stmt_total = $core_db->prepare("SELECT sum(nilai_konversi) as tot FROM kurikulum_pkg_nilai WHERE pkg_id = ?");
        $stmt_total->execute([$pkg_id]);
        $semua_nilai = $stmt_total->fetch(PDO::FETCH_ASSOC);
        $total_konversi = $semua_nilai['tot'] ?? 0;
        
        // Asumsi sederhana: PKG Max 56 (14 x 4). Nilai 100 = (total_konversi / 56) * 100.
        // Konversi persis sesuai juknis mungkin berbeda, tapi kita buat standar dulu.
        $nilai_akhir_pkg = ($total_konversi / 56) * 100;
        
        $sebutan = 'Kurang';
        if ($nilai_akhir_pkg >= 91) $sebutan = 'Amat Baik';
        elseif ($nilai_akhir_pkg >= 76) $sebutan = 'Baik';
        elseif ($nilai_akhir_pkg >= 61) $sebutan = 'Cukup';
        elseif ($nilai_akhir_pkg >= 51) $sebutan = 'Sedang';
        
        $stmt_update = $core_db->prepare("UPDATE kurikulum_pkg_guru SET status_selesai = 'Selesai', total_skor_14_kompetensi = ?, nilai_akhir_pkg = ?, sebutan = ? WHERE id = ?");
        $stmt_update->execute([
            $total_konversi, $nilai_akhir_pkg, $sebutan, $pkg_id
        ]);
        
        header("Location: wakakur_pkg.php");
        exit;
    } else {
        $next = $kompetensi_ke + 1;
        header("Location: wakakur_pkg_form.php?pkg_id=$pkg_id&kompetensi=$next");
        exit;
    }
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
