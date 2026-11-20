<?php
/**
 * CBT Madrasah Semi-Online Synchronization Endpoint
 * Direct API for pulling exam packages & pushing results.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/koneksi.php';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

$syncSecret = 'MIFHDA_CBT_SYNC_2026';
$providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
if ($providedToken !== $syncSecret) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Token sinkronisasi tidak valid.']);
    exit;
}

$action = $_REQUEST['action'] ?? 'export';

if ($action === 'export') {
    \App\Controllers\CbtController::apiExportPaketOnline();
} elseif ($action === 'import') {
    \App\Controllers\CbtController::apiImportHasilOnline();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal.']);
}
