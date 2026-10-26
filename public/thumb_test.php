<?php
require_once __DIR__ . '/../app/Config/Database.php';

$db = Database::connect('core');
$siswas = $db->query("SELECT id, nama, foto FROM siswa WHERE foto IS NOT NULL AND foto != '' LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Kualitas Thumbnail</title>
    <style>
        body { font-family: sans-serif; background: #f1f5f9; padding: 20px; }
        .row { display: flex; gap: 20px; margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .col { flex: 1; text-align: center; }
        .col img { border: 1px solid #ccc; background: #f8fafc; }
        h3 { margin-top: 0; }
        .info { font-size: 12px; color: #666; margin-top: 5px; }
        .card-size { width: 22.5mm; height: 30mm; object-fit: cover; border-radius: 1mm; }
    </style>
</head>
<body>
    <h2>Perbandingan Kualitas Gambar</h2>
    <p>Halaman ini membandingkan ukuran file dan kualitas antara Foto Original vs Thumbnail (300x300) untuk keperluan cetak kartu (22.5mm x 30mm).</p>
    
    <?php foreach($siswas as $s): ?>
        <?php
            $path = __DIR__ . '/uploads/siswa/' . $s['foto'];
            if (!file_exists($path)) continue;
            
            $size_ori = round(filesize($path) / 1024, 2) . ' KB';
            
            $url_ori = '/uploads/siswa/' . $s['foto'];
            $url_thumb = '/thumb.php?src=uploads/siswa/' . $s['foto'] . '&w=300&h=300';
            
            // To get thumb size, we need to generate it or find it
            $thumb_path = __DIR__ . '/uploads/thumb/' . md5('uploads/siswa/' . $s['foto'] . '300300') . '.' . strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $size_thumb = 'Belum di-generate';
            if (file_exists($thumb_path)) {
                $size_thumb = round(filesize($thumb_path) / 1024, 2) . ' KB';
            }
        ?>
        <div class="row">
            <div class="col" style="flex: 0.5;">
                <h3>Nama Siswa</h3>
                <p><strong><?php echo htmlspecialchars($s['nama']); ?></strong></p>
                <div style="font-size: 12px; color: #888;">
                    Ini adalah ukuran sebenarnya (22.5mm x 30mm) ketika dicetak di kartu. Coba perhatikan apakah thumbnail terlihat pecah.
                </div>
            </div>
            <div class="col">
                <h3>Original (Ukuran Cetak)</h3>
                <img src="<?php echo $url_ori; ?>" class="card-size">
                <div class="info">Size Asli: <?php echo $size_ori; ?></div>
            </div>
            <div class="col">
                <h3>Thumbnail (Ukuran Cetak)</h3>
                <img src="<?php echo $url_thumb; ?>" class="card-size">
                <div class="info">Size Thumb: <?php echo $size_thumb; ?></div>
                <div class="info" style="font-size: 10px;">(Refresh jika size blm muncul)</div>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
