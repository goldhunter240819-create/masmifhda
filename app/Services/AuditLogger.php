<?php

namespace App\Services;

/**
 * AuditLogger — Pencatat aktivitas sistem GH-SSS
 * Semua event ditulis ke storage/logs/audit.log
 */
class AuditLogger
{
    private static string $logFile = __DIR__ . '/../../storage/logs/audit.log';
    private static int    $maxLines = 500; // batas baris sebelum rotasi

    // ── Kategori event ────────────────────────────────────────────────────
    const AUTH    = 'AUTH';
    const ACCESS  = 'ACCESS';
    const DATA    = 'DATA';
    const SYSTEM  = 'SYSTEM';
    const TUNNEL  = 'TUNNEL';
    const SECURITY = 'SECURITY';

    // ── Level ─────────────────────────────────────────────────────────────
    const INFO  = 'INFO';
    const WARN  = 'WARN';
    const ERROR = 'ERROR';
    const OK    = 'OK';

    /**
     * Tulis satu baris log.
     *
     * @param string $category  AUTH | ACCESS | DATA | SYSTEM | TUNNEL | SECURITY
     * @param string $action    Deskripsi singkat tindakan
     * @param string $level     INFO | WARN | ERROR | OK
     * @param array  $extra     Data tambahan opsional (key => value)
     */
    public static function log(
        string $category,
        string $action,
        string $level = self::INFO,
        array  $extra = []
    ): void {
        // Pastikan direktori ada
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Metadata dari sesi
        $user    = self::getCurrentUser();
        $ip      = self::getClientIp();
        $ua      = self::getBrowser();
        $ts      = date('Y-m-d H:i:s');
        $uri     = $_SERVER['REQUEST_URI'] ?? '-';

        // Bangun pesan
        $extraStr = '';
        if (!empty($extra)) {
            $parts = [];
            foreach ($extra as $k => $v) {
                $parts[] = strtoupper($k) . '=' . $v;
            }
            $extraStr = ' | ' . implode(', ', $parts);
        }

        $line = sprintf(
            "[%s] [%s] [%s] %s | USER=%s IP=%s BROWSER=%s URI=%s%s\n",
            $ts,
            str_pad($level,    5),
            str_pad($category, 8),
            $action,
            $user,
            $ip,
            $ua,
            $uri,
            $extraStr
        );

        // Rotasi log jika terlalu besar
        self::rotate();

        // Tulis ke file (append)
        @file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
    }

    // ── Shorthand methods ─────────────────────────────────────────────────

    public static function loginSuccess(string $username, string $role): void
    {
        self::log(self::AUTH, "LOGIN BERHASIL — @{$username}", self::OK, ['role' => $role]);
    }

    public static function loginFailed(string $username): void
    {
        self::log(self::AUTH, "LOGIN GAGAL — percobaan username '@{$username}'", self::WARN);
    }

    public static function loginQr(string $identifier, bool $success): void
    {
        if ($success) {
            self::log(self::AUTH, "LOGIN QR BERHASIL — ID: {$identifier}", self::OK);
        } else {
            self::log(self::AUTH, "LOGIN QR GAGAL — data: {$identifier}", self::WARN);
        }
    }

    public static function logout(string $username): void
    {
        self::log(self::AUTH, "LOGOUT — @{$username}", self::INFO);
    }

    public static function accessPage(string $page): void
    {
        $user = self::getCurrentUser();
        self::log(self::ACCESS, "Akses halaman [{$page}]", self::INFO, ['user' => $user]);
    }

    public static function dataChange(string $entity, string $action, string $detail = ''): void
    {
        $msg = "DATA [{$entity}] {$action}";
        if ($detail) $msg .= " — {$detail}";
        self::log(self::DATA, $msg, self::INFO);
    }

    public static function systemAction(string $action, string $detail = ''): void
    {
        $msg = $action;
        if ($detail) $msg .= " — {$detail}";
        self::log(self::SYSTEM, $msg, self::INFO);
    }

    public static function tunnelAction(string $type, string $action): void
    {
        self::log(self::TUNNEL, "TUNNEL [{$type}] {$action}", self::INFO);
    }

    public static function securityAlert(string $detail): void
    {
        self::log(self::SECURITY, "⚠ SECURITY ALERT — {$detail}", self::WARN);
    }

    // ── Helper methods ────────────────────────────────────────────────────

    private static function getCurrentUser(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $username = $_SESSION['username'] ?? 'guest';
        $role_id  = $_SESSION['role_id']  ?? '?';
        $roleMap  = [1 => 'Admin', 2 => 'Guru', 3 => 'Siswa', 99 => 'Dev'];
        $roleName = $roleMap[$role_id] ?? "role:{$role_id}";
        return "@{$username}[{$roleName}]";
    }

    private static function getClientIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }

    private static function getBrowser(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Edge'))    return 'Edge';
        if (str_contains($ua, 'curl'))    return 'cURL';
        return 'Other';
    }

    private static function rotate(): void
    {
        if (!file_exists(self::$logFile)) return;
        $lines = @file(self::$logFile);
        if ($lines && count($lines) >= self::$maxLines) {
            // Simpan 200 baris terakhir saja
            $keep = array_slice($lines, -200);
            $rotated = dirname(self::$logFile) . '/audit_' . date('Ymd_His') . '.log';
            @file_put_contents($rotated, implode('', $lines));
            @file_put_contents(self::$logFile, implode('', $keep));
        }
    }

    /**
     * Ambil N baris terakhir dari log untuk ditampilkan di UI.
     * Mengembalikan array baris (string), dari terlama ke terbaru.
     */
    public static function tail(int $n = 50, int $offset = 0): array
    {
        if (!file_exists(self::$logFile)) return [];
        $lines = @file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return [];
        if ($offset > 0) {
            $lines = array_slice($lines, 0, -$offset);
        }
        return array_slice($lines, -$n);
    }
    
    public static function getTotalCount(): int
    {
        if (!file_exists(self::$logFile)) return 0;
        $lines = @file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $lines ? count($lines) : 0;
    }

    /**
     * Ambil semua log dan kembalikan sebagai JSON-friendly array.
     */
    public static function getAll(int $limit = 100, int $offset = 0): array
    {
        $lines = self::tail($limit, $offset);
        $result = [];
        foreach (array_reverse($lines) as $line) {
            // Parse format: [2026-05-29 18:00:00] [INFO ] [AUTH    ] ...
            if (preg_match('/^\[(.+?)\] \[(\w+)\s*\] \[(\w+)\s*\] (.+)$/', $line, $m)) {
                $result[] = [
                    'ts'       => $m[1],
                    'level'    => trim($m[2]),
                    'category' => trim($m[3]),
                    'message'  => $m[4],
                    'raw'      => $line,
                ];
            } else {
                $result[] = [
                    'ts'       => '',
                    'level'    => 'INFO',
                    'category' => 'SYSTEM',
                    'message'  => $line,
                    'raw'      => $line,
                ];
            }
        }
        return $result;
    }
}
