<?php
/**
 * Gateway Auto-Login for GH-SSS Portal
 */
require_once __DIR__ . '/../../app/Core/Database.php';
use App\Core\Database;

// Start session to access GH-SSS portal session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verify if user is logged in to GH-SSS and is Admin/Developer
// Role 1 = Admin, Role 99 = Developer
if (isset($_SESSION['user_id']) && in_array($_SESSION['role_id'], [1, 99])) {
    
    // 2. Set Absen System Admin Session
    // We use 'admin' as the default session value as expected by absen system
    $_SESSION['admin'] = 'admin';
    
    // 3. Redirect to Absen Dashboard
    header("Location: pages/dashboard.php");
    exit;
} else {
    // If not admin, redirect to the public root of absen (login/info page)
    header("Location: index.php");
    exit;
}
