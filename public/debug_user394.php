<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$hits = [];

// Search in all tables of core
$tables = $core->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $tbl) {
    try {
        $cols = $core->query("DESCRIBE `$tbl`")->fetchAll(PDO::FETCH_ASSOC);
        $whereOr = [];
        foreach ($cols as $col) {
            $colName = $col['Field'];
            $whereOr[] = "`$colName` = '394'";
            $whereOr[] = "`$colName` = '46278530'";
        }
        $sql = "SELECT * FROM `$tbl` WHERE " . implode(' OR ', $whereOr) . " LIMIT 5";
        $rows = $core->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $hits["core.$tbl"] = $rows;
        }
    } catch (\Throwable $e) {}
}

// Search in all tables of siakad
$tablesSiakad = $siakad->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tablesSiakad as $tbl) {
    try {
        $cols = $siakad->query("DESCRIBE `$tbl`")->fetchAll(PDO::FETCH_ASSOC);
        $whereOr = [];
        foreach ($cols as $col) {
            $colName = $col['Field'];
            $whereOr[] = "`$colName` = '394'";
            $whereOr[] = "`$colName` = '46278530'";
        }
        $sql = "SELECT * FROM `$tbl` WHERE " . implode(' OR ', $whereOr) . " LIMIT 5";
        $rows = $siakad->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $hits["siakad.$tbl"] = $rows;
        }
    } catch (\Throwable $e) {}
}

echo json_encode($hits, JSON_PRETTY_PRINT);
