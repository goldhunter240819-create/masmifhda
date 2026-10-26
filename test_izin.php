<?php
require 'apk/koneksi.php';

echo "=== CEK ONESIGNAL CONFIG ===\n";
$settings_file = __DIR__ . '/apk/apk_settings.json';
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
    echo "app_id: " . (!empty($settings['onesignal_app_id']) ? '✅ ADA ('.substr($settings['onesignal_app_id'],0,8).'...)' : '❌ KOSONG') . "\n";
    echo "rest_key: " . (!empty($settings['onesignal_rest_key']) ? '✅ ADA' : '❌ KOSONG') . "\n";
} else {
    echo "❌ File apk_settings.json tidak ditemukan!\n";
}

echo "\n=== CEK ADMIN IZIN PUNYA USER_ID ===\n";
try {
    $stmt = $core_db->query("
        SELECT g.id as guru_id, g.nama, g.user_id, a.app_code 
        FROM apk_akses_guru a 
        JOIN guru g ON a.guru_id = g.id 
        WHERE a.app_code = 'admin_izin'
    ");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($admins) > 0) {
        foreach ($admins as $adm) {
            $uid_status = !empty($adm['user_id']) ? "✅ user_id={$adm['user_id']}" : "❌ user_id NULL/KOSONG";
            echo "- {$adm['nama']}: $uid_status\n";
        }
    } else {
        echo "❌ Tidak ada guru dengan akses admin_izin!\n";
        echo "\nCek isi tabel apk_akses_guru:\n";
        $all = $core_db->query("SELECT * FROM apk_akses_guru LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        print_r($all);
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CEK FUNGSI sendOneSignalNotification ===\n";
echo function_exists('sendOneSignalNotification') ? "✅ Fungsi tersedia\n" : "❌ Fungsi tidak tersedia\n";
