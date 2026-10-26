<?php

namespace App\Middleware;

use App\Core\Database;

class LicenseMiddleware {
    public static function handle() {
        // Skip license check for dev-panel and activation pages
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (strpos($path, '/dev-panel') !== false || strpos($path, '/activate') !== false || strpos($path, '/setup') !== false) {
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
