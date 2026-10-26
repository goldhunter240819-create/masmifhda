<?php
$src = isset($_GET['src']) ? $_GET['src'] : '';
$w = isset($_GET['w']) ? (int)$_GET['w'] : 100;
$h = isset($_GET['h']) ? (int)$_GET['h'] : 100;

if (empty($src) || !file_exists(__DIR__ . '/' . $src)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$cache_dir = __DIR__ . '/uploads/thumb';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}

$file_info = pathinfo($src);
$ext = strtolower(isset($file_info['extension']) ? $file_info['extension'] : '');
$cache_file = $cache_dir . '/' . md5($src . $w . $h) . '.' . $ext;

if (file_exists($cache_file)) {
    $mime = mime_content_type($cache_file);
    header('Content-Type: ' . $mime);
    header('Cache-Control: max-age=2592000, public');
    readfile($cache_file);
    exit;
}

$source_path = __DIR__ . '/' . $src;
$mime = mime_content_type($source_path);

switch ($mime) {
    case 'image/jpeg':
    case 'image/jpg':
        $img = @imagecreatefromjpeg($source_path);
        break;
    case 'image/png':
        $img = @imagecreatefrompng($source_path);
        break;
    case 'image/webp':
        $img = @imagecreatefromwebp($source_path);
        break;
    case 'image/gif':
        $img = @imagecreatefromgif($source_path);
        break;
    default:
        header('Content-Type: ' . $mime);
        readfile($source_path);
        exit;
}

if (!$img) {
    header('Content-Type: ' . $mime);
    readfile($source_path);
    exit;
}

$orig_w = imagesx($img);
$orig_h = imagesy($img);

$ratio = max($w / $orig_w, $h / $orig_h);
$new_w = $orig_w * $ratio;
$new_h = $orig_h * $ratio;

$x = ($orig_w - $w / $ratio) / 2;
$y = ($orig_h - $h / $ratio) / 2;

$thumb = imagecreatetruecolor($w, $h);

if ($mime == 'image/png' || $mime == 'image/webp' || $mime == 'image/gif') {
    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);
    $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
    imagefilledrectangle($thumb, 0, 0, $w, $h, $transparent);
}

imagecopyresampled($thumb, $img, 0, 0, $x, $y, $w, $h, $w / $ratio, $h / $ratio);

switch ($mime) {
    case 'image/jpeg':
    case 'image/jpg':
        imagejpeg($thumb, $cache_file, 85);
        break;
    case 'image/png':
        imagepng($thumb, $cache_file, 8);
        break;
    case 'image/webp':
        imagewebp($thumb, $cache_file, 85);
        break;
    case 'image/gif':
        imagegif($thumb, $cache_file);
        break;
}

imagedestroy($img);
imagedestroy($thumb);

header('Content-Type: ' . $mime);
header('Cache-Control: max-age=2592000, public');
readfile($cache_file);
