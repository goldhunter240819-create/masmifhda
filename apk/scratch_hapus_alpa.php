<?php
require 'c:\xampp\htdocs\masmifhda\apk\koneksi.php';

// Ambil semua libur dari kalender_akademik
$stmt = $siakad_db->query("SELECT tanggal_mulai, tanggal_selesai FROM kalender_akademik WHERE tipe = 'libur'");
$liburs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$libur_dates = [];
foreach($liburs as $l) {
    $t = strtotime($l['tanggal_mulai']);
    $e = strtotime($l['tanggal_selesai']);
    if (!$e) $e = $t;
    while($t <= $e) {
        $libur_dates[] = date('Y-m-d', $t);
        $t = strtotime('+1 day', $t);
    }
}

// Hapus Alpa pada tanggal merah
foreach($libur_dates as $d) {
    $del = $siakad_db->prepare("DELETE FROM absensi_siswa WHERE tanggal = ? AND status IN ('Alpa', 'Alfa', 'Bolos')");
    $del->execute([$d]);
    echo "Deleted Alpa for $d : " . $del->rowCount() . " rows\n";
}

