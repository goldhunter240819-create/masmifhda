<?php
namespace App\Core;

class AuditLog {
    public static function write($action, $details = '') {
        $db = Database::connect('core');
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip]);
    }
}
