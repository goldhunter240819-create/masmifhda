<?php

namespace App\Services;

class OneSignalService {

    // APP ID OneSignal Komandan
    public static $APP_ID = "219da542-40ee-4007-8d38-18a99bf11c62";
    
    // PERHATIAN: REST API KEY WAJIB DIISI!
    public static $REST_API_KEY = "YOUR_ONESIGNAL_REST_KEY";

    /**
     * Mengirim Push Notification ke semua aplikasi Android yang terinstal
     * 
     * @param string $title Judul notifikasi
     * @param string $message Isi pesan notifikasi
     * @param string|null $url (Opsional) URL spesifik yang akan dibuka saat notif diklik
     */
    public static function sendNotification($judul, $pesan, $url = null, $targetUserId = null) {
        $content = array(
            "en" => $pesan
        );
        $headings = array(
            "en" => $judul
        );

        $fields = array(
            'app_id' => self::$APP_ID,
            'headings' => $headings,
            'contents' => $content,
            'android_sound' => 'notif_khusus' // Nama file audio tanpa ekstensi (.mp3/.wav)
        );

        // Jika ada target ID spesifik (Notifikasi Personal / Massal)
        if ($targetUserId !== null) {
            $aliases = is_array($targetUserId) ? array_map('strval', $targetUserId) : array(strval($targetUserId));
            $fields['include_aliases'] = array('external_id' => $aliases);
            $fields['target_channel'] = 'push';
        } else {
            // Jika tidak ada target, kirim ke semua (Broadcast)
            $fields['included_segments'] = array('All');
        }

        if ($url) {
            $fields['url'] = $url;
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . self::$REST_API_KEY
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
