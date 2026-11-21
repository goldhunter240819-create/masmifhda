<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$user394 = $core->query("SELECT * FROM users WHERE id = 394")->fetch(PDO::FETCH_ASSOC);

// Cari di semua tabel core dan siakad
$results = [
    'user394' => $user394,
    'core_matches' => [],
    'siakad_matches' => []
];

// Search core
$coreTables = ['siswa', 'guru', 'users', 'cbt_peserta_khusus', 'cbt_absensi', 'presensi'];
foreach ($coreTables as $t) {
    try {
        $rows = $core->query("SELECT * FROM `$t` WHERE id = 394 OR user_id = 394 OR nis = '46278530' OR nisn = '46278530' OR username = '46278530' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $results['core_matches'][$t] = $rows;
        }
    } catch (\Exception $e) {
        $results['core_matches'][$t . '_err'] = $e->getMessage();
    }
}

// Search siakad
$siakadTables = ['siswa', 'guru', 'users', 'presensi'];
foreach ($siakadTables as $t) {
    try {
        $rows = $siakad->query("SELECT * FROM `$t` WHERE id = 394 OR nis = '46278530' OR nisn = '46278530' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $results['siakad_matches'][$t] = $rows;
        }
    } catch (\Exception $e) {
        $results['siakad_matches'][$t . '_err'] = $e->getMessage();
    }
}

// Also let's check who the last presensi was in presensi where user_id = 394!
$pres = $core->query("SELECT p.*, u.username, u.nama as u_nama, u.role_id FROM presensi p LEFT JOIN users u ON p.user_id = u.id WHERE p.user_id = 394 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$results['presensi_detail'] = $pres;

// Also check ALL columns of users for 394
echo json_encode($results, JSON_PRETTY_PRINT);
