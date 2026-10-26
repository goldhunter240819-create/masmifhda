<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ini_set('display_errors', 0);
error_reporting(0);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['base64']) || empty($data['filename'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

$base64 = $data['base64'];
$filename = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $data['filename']);
$type = $data['type'] ?? 'image'; // 'image' atau 'pdf'

$dir = __DIR__ . '/../public/downloads/temp/';
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

// Cleanup old temp files (lebih dari 1 jam)
$files = glob($dir . '*');
$now = time();
foreach ($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) >= 3600) {
            @unlink($file);
        }
    }
}

if ($type === 'pdf') {
    // PDF dikirim sebagai base64 murni (tanpa prefix data:...)
    $decoded = base64_decode($base64);
    if ($decoded === false) {
        echo json_encode(['status' => 'error', 'message' => 'Decode base64 PDF gagal']);
        exit;
    }
    $filepath = $dir . $filename;
    file_put_contents($filepath, $decoded);
} else {
    // Image (PNG/JPG)
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $typeMatch)) {
        $base64 = substr($base64, strpos($base64, ',') + 1);
        $ext = strtolower($typeMatch[1]);
        if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tipe gambar tidak valid']);
            exit;
        }
        $decoded = base64_decode($base64);
        if ($decoded === false) {
            echo json_encode(['status' => 'error', 'message' => 'Decode base64 gagal']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Format base64 tidak dikenali']);
        exit;
    }
    $filepath = $dir . $filename;
    file_put_contents($filepath, $decoded);
}

echo json_encode(['status' => 'success', 'filename' => $filename]);
