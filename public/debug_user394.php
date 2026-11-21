<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$siakadCols = $siakad->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);
$coreCols = $core->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);

$user394 = $core->query("SELECT * FROM users WHERE id = 394")->fetch(PDO::FETCH_ASSOC);

// Cari di semua tabel core dan siakad yang memiliki kata 46278530 atau 394
$coreTables = $core->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$foundIn = [];
foreach ($coreTables as $t) {
    try {
        $cnt = $core->query("SELECT COUNT(*) FROM `$t` WHERE id = 394")->fetchColumn();
        if ($cnt > 0) $foundIn["core.$t.id"] = $cnt;
    } catch (\Exception $e) {}
    try {
        $cols = $core->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cols as $c) {
            if ($c === 'user_id' || $c === 'siswa_id' || $c === 'username' || $c === 'nis') {
                $c1 = $core->query("SELECT COUNT(*) FROM `$t` WHERE `$c` = '394' OR `$c` = '46278530'")->fetchColumn();
                if ($c1 > 0) $foundIn["core.$t.$c"] = $c1;
            }
        }
    } catch (\Exception $e2) {}
}

$siakadTables = $siakad->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($siakadTables as $t) {
    try {
        $cnt = $siakad->query("SELECT COUNT(*) FROM `$t` WHERE id = 394")->fetchColumn();
        if ($cnt > 0) $foundIn["siakad.$t.id"] = $cnt;
    } catch (\Exception $e) {}
    try {
        $cols = $siakad->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cols as $c) {
            if ($c === 'user_id' || $c === 'siswa_id' || $c === 'username' || $c === 'nis' || $c === 'nisn') {
                $c1 = $siakad->query("SELECT COUNT(*) FROM `$t` WHERE `$c` = '394' OR `$c` = '46278530'")->fetchColumn();
                if ($c1 > 0) $foundIn["siakad.$t.$c"] = $c1;
            }
        }
    } catch (\Exception $e2) {}
}

$presensi = $core->query("SELECT * FROM presensi WHERE user_id = 394 ORDER BY id DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'presensi' => $presensi
]);


