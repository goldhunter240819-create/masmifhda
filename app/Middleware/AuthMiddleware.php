<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Allowed paths without login (Landing Page & Sync APIs are PUBLIC)
        $allowed = [
            '/login', '/activate', '/setup', '/dev-panel', '/updates', '/changelog', 
            '/rekap_soal', '/spmb', '/portal', '/kwitansi/verifikasi', '/verify-sk', 
            '/pip', '/sinkron_langsung.php', '/cbt/semi-online/api/', '/apk/download_standalone.php'
        ];
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

        // Security Guard: Jika Siswa (role_id = 3 / role_id = 7 / akun CBT siswa) iseng membuka portal proktor/admin/bank soal
        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
        $isAdminOrDev = ($roleId === 1 || $roleId === 99 || !empty($_SESSION['is_kamad']) || (isset($_SESSION['role']) && in_array(strtolower($_SESSION['role']), ['admin', 'developer', 'kamad', 'proktor'])));

        if ($isAdminOrDev) {
            unset($_SESSION['cbt_student_id'], $_SESSION['no_peserta']);
        } elseif (($roleId === 3 || $roleId === 7 || !empty($_SESSION['cbt_student_id']) || !empty($_SESSION['no_peserta'])) && isset($_SESSION['user_id'])) {
            $isManagementPath = (
                strpos($path, '/cbt') !== false 
                && strpos($path, '/cbt/kerjakan') === false 
                && strpos($path, '/kerjakan') === false 
                && strpos($path, '/cbt/tata-tertib') === false
                && strpos($path, '/tata-tertib') === false
                && strpos($path, '/cbt/konfirmasi-token') === false
                && strpos($path, '/konfirmasi-token') === false
                && strpos($path, '/cbt/api/status-peserta') === false
                && strpos($path, '/cbt/api/lock-back-spam') === false
                && strpos($path, '/apk/') === false
            ) || strpos($path, '/bank_soal') !== false 
              || strpos($path, '/admin') !== false 
              || strpos($path, '/kurikulum') !== false;

            if ($isManagementPath) {
                $cbtUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');
                header("Location: {$cbtUrl}");
                exit;
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
