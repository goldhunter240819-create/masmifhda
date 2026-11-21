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

$results = [
    'user394' => $user394,
    'core_matches' => [],
    'siakad_matches' => []
];

// Search core
$coreTables = ['siswa', 'guru', 'cbt_peserta_khusus'];
foreach ($coreTables as $t) {
    try {
        $rows = $core->query("SELECT * FROM `$t` WHERE id = 394 OR user_id = 394 OR nis = '46278530' OR nisn = '46278530' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $results['core_matches'][$t] = $rows;
        }
    } catch (\Exception $e) {
        $results['core_matches'][$t . '_err'] = $e->getMessage();
    }
}

// Search siakad
$siakadTables = ['siswa', 'guru'];
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

// Check who is logged in or user info
$results['user_columns'] = $core->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);

// Let's also check if username '46278530' matches any NIK or NISN or no_kk or similar in core.siswa!
try {
    $cs = $core->query("SELECT id, user_id, nis, nisn, nik, nama, kelas_id FROM siswa WHERE nik = '46278530' OR no_kk = '46278530' OR nis = '46278530' OR nisn = '46278530' OR nama LIKE '%Faiz%' OR nama LIKE '%46278530%'")->fetchAll(PDO::FETCH_ASSOC);
    $results['siswa_by_various'] = $cs;
} catch (\Exception $e) {
    $results['siswa_by_various_err'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT);
