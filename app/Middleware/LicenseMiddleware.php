<?php

namespace App\Middleware;

use App\Core\Database;

class LicenseMiddleware {
    public static function handle() {
        // Skip license check for dev-panel, activation, and CBT standalone mode
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
        $current_domain = strtolower(trim($_SERVER['HTTP_HOST'] ?? ''));

        if (
            file_exists(__DIR__ . '/../../.cbt_standalone') ||
            $current_domain === 'localhost' ||
            strpos($current_domain, 'localhost:') === 0 ||
            $current_domain === '127.0.0.1' ||
            strpos($current_domain, '127.0.0.1:') === 0 ||
            strpos($current_domain, '10.') === 0 ||
            strpos($current_domain, '192.168.') === 0 ||
            strpos($current_domain, '172.') === 0 ||
            strpos($path, '/cbt/') !== false ||
            strpos($path, '/apk/') !== false ||
            strpos($path, '/dev-panel') !== false ||
            strpos($path, '/activate') !== false ||
            strpos($path, '/setup') !== false
        ) {
            return true;
        }

        $db = Database::connect('core');
        $current_domain = strtolower(trim($_SERVER['HTTP_HOST']));

        // Cek lisensi spesifik untuk domain ini (multi-domain support)
        $stmt = $db->prepare("SELECT status, domain FROM licenses WHERE domain = ? AND status = 'aktif' LIMIT 1");
        $stmt->execute([$current_domain]);
        $license = $stmt->fetch();

        if (!$license) {
            redirect('/activate');
            exit;
        }

        return true;
    }
}
