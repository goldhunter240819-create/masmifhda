<?php
// DEBUG TOOL - DELETE AFTER USE
require_once 'koneksi.php';
header('Content-Type: application/json');

// Test 1: Cek apakah koneksi OneSignal bisa dihubungi
$settings_file = __DIR__ . '/apk_settings.json';
$settings = json_decode(file_get_contents($settings_file), true);
$app_id = $settings['onesignal_app_id'] ?? '';
$api_key = $settings['onesignal_rest_key'] ?? '';

// Test 2: Ambil daftar subscriber yang terdaftar di OneSignal
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id={$app_id}&limit=50");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json; charset=utf-8',
    'Authorization: Basic ' . $api_key
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$players_data = json_decode($response, true);
$external_ids = [];
if (!empty($players_data['players'])) {
    foreach ($players_data['players'] as $player) {
        if (!empty($player['external_user_id'])) {
            $external_ids[] = [
                'external_user_id' => $player['external_user_id'],
                'device_type' => $player['device_type'], // 1=iOS, 5=Android
                'last_active' => date('Y-m-d H:i:s', $player['last_active'])
            ];
        }
    }
}

// Test 3: Coba kirim notifikasi test ke user yang ada
$test_target = isset($_GET['target']) ? $_GET['target'] : null;
$test_result = null;
if ($test_target) {
    $test_result = sendOneSignalNotification($test_target, "Test Notifikasi", "Halo ini test notifikasi dari server!");
    $test_result = json_decode($test_result, true);
}

// Test 4: Cek API Key valid
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, "https://onesignal.com/api/v1/apps/{$app_id}");
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $api_key
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
$app_response = curl_exec($ch2);
$app_http_code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
$app_data = json_decode($app_response, true);

echo json_encode([
    'app_id' => $app_id,
    'api_key_valid' => $app_http_code === 200,
    'app_name' => $app_data['name'] ?? 'Error',
    'app_players_total' => $app_data['players'] ?? 0,
    'players_with_external_id' => $external_ids,
    'total_found' => count($external_ids),
    'players_http_code' => $http_code,
    'test_target' => $test_target,
    'test_result' => $test_result,
], JSON_PRETTY_PRINT);
