<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_ghtech_key = "4a0fef55431a38a8ee6317c512178502";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) {
        $vid = uniqid("loc_");
        setcookie("ghtech_vid", $vid, time() + 86400, "/");
        $_ghtech_ip = $vid;
    } else {
        $_ghtech_ip = $_COOKIE["ghtech_vid"];
    }
}

$_ghtech_user = $_SESSION["siswa_nama"] ?? ($_SESSION["guru_nama"] ?? ($_SESSION["nama"] ?? ""));

if (!empty($_ghtech_user)) {
    $url = "http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_ghtech_user);
    $ctx = stream_context_create(['http' => ['timeout' => 1]]);
    @file_get_contents($url, false, $ctx);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'user' => $_ghtech_user]);
