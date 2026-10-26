<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Allowed paths without login (Landing Page is now PUBLIC)
        $allowed = ['/login', '/activate', '/setup', '/dev-panel', '/updates', '/changelog', '/rekap_soal', '/spmb', '/portal', '/kwitansi/verifikasi', '/verify-sk', '/pip'];
        // Root / and /portal handle auth internally (redirect to /login if guest)
        if ($path == '/' || $path == '/index.php') {
            return true;
        }
        $isAllowed = false;
        foreach ($allowed as $a) {
            if (strpos($path, $a) !== false) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed && !isset($_SESSION['user_id'])) {
            \App\Services\AuditLogger::securityAlert("Akses tanpa login diblokir — path: {$path}");
            
            $baseUri = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
            if ($baseUri === '/' || $baseUri === '\\') $baseUri = '';
            // If they are inside public folder directly or not
            if (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false) {
                $baseUri = str_replace('/public', '', $baseUri);
            }
            // Simple robust base URI
            $baseUri = rtrim(str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']), '/');

            header('Location: ' . $baseUri . '/login');
            exit;
        }

        return true;
    }
}
