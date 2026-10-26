<?php
require_once 'koneksi.php';

// Debug: tampilkan raw QR data yang diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['qr_data'])) {
    echo "<pre>RAW QR DATA:\n";
    echo htmlspecialchars($_POST['qr_data']);
    echo "\n\n";
    
    $qr_data = trim($_POST['qr_data']);
    $identifier = $qr_data;
    
    // Coba regex pertama (NIS/NISN angka)
    preg_match('/NIS\/NISN:\s*([\d\/]+)/i', $qr_data, $matches);
    echo "Match 1 (NIS/NISN angka): "; print_r($matches);
    
    if (!$matches) {
        preg_match('/(\d{5,20})/i', $qr_data, $matches);
        echo "Match 2 (angka 5-20 digit): "; print_r($matches);
    }
    
    // Coba regex baru (NIS/NISN alfanumerik)
    preg_match('/NIS\/NISN:\s*([A-Z0-9\/]+)/i', $qr_data, $matches_new);
    echo "Match baru (NIS/NISN alfanum): "; print_r($matches_new);
    
    if ($matches) {
        $raw_nis = $matches[1];
        $parts = array_filter(explode('/', $raw_nis));
        $identifier = end($parts);
        $identifier = trim($identifier);
    }
    
    echo "IDENTIFIER FINAL: " . $identifier . "\n\n";
    
    // Coba cari di DB
    $stmt = $core_db->prepare("SELECT id, nama, nis, nisn, status FROM siswa WHERE nis = :qr OR nisn = :qr LIMIT 1");
    $stmt->execute(['qr' => $identifier]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "HASIL DB: "; print_r($siswa);
    echo "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>QR Debug</title></head>
<body style="background:#111; color:#fff; padding:20px; font-family: monospace;">
<h2>Debug QR Login</h2>
<form method="POST">
    <textarea name="qr_data" style="width:100%; height:100px; background:#222; color:#0f0; border: 1px solid #0f0; padding:10px;" placeholder="Paste QR data atau scan di sini..."></textarea><br><br>
    <button type="submit" style="background:#0f0; color:#000; padding:10px 20px; border:none; cursor:pointer; font-weight:bold;">Test QR</button>
</form>
</body>
</html>
