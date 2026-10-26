<?php
/**
 * API Endpoint: Status tunnel GH-SSS (Ngrok) + RDM (Cloudflare)
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$result = array(
    'ghsss'    => null,
    'rdm'      => null,
    'rdm_mode' => 'offline',
    'status'   => 'offline',
);

// ── 1. CEK NGROK (untuk GH-SSS) ──────────────────────────
if (function_exists('curl_init')) {
    $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $data = json_decode($response, true);
        if (isset($data['tunnels'])) {
            foreach ($data['tunnels'] as $tunnel) {
                if (isset($tunnel['proto']) && $tunnel['proto'] === 'https') {
                    $addr = isset($tunnel['config']['addr']) ? $tunnel['config']['addr'] : '';
                    $name = isset($tunnel['name']) ? $tunnel['name'] : '';
                    $url  = isset($tunnel['public_url']) ? $tunnel['public_url'] : '';
                    if ($name === 'ghsss' || (strpos($addr, '1203') === false && strpos($addr, ':80') !== false)) {
                        $result['ghsss'] = $url . '/';
                    }
                }
            }
        }
        $result['status'] = 'online';
    }
}

// ── 2. CEK CLOUDFLARE TUNNEL untuk RDM ───────────────────
$urlFile = __DIR__ . '/../../storage/rdm_tunnel_url.txt';

if (file_exists($urlFile)) {
    $rdmContent = file_get_contents($urlFile);
    // Remove BOM and other non-printable characters
    $rdmUrl = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $rdmContent));
    
    if (!empty($rdmUrl) && strpos($rdmUrl, 'https://') !== false) {
        $result['rdm']      = $rdmUrl;
        $result['rdm_mode'] = 'public'; // Bisa diakses dari mana saja!
        $result['status']   = 'online';
    }
}

// ── 3. FALLBACK: jika Cloudflare belum ada, cek port lokal ──
if ($result['rdm'] === null) {
    $sock = @fsockopen('127.0.0.1', 1203, $errno, $errstr, 1);
    if ($sock !== false) {
        fclose($sock);
        $result['rdm']      = 'http://localhost:1203';
        $result['rdm_mode'] = 'local';
        $result['status']   = 'online';
    }
}

$result['updated_at'] = date('H:i:s');
echo json_encode($result);
