<?php
$dbs = ['aula_db', 'core_db', 'db_mts_rs', 'db_mts_mifhda', 'db_masamuda'];
foreach ($dbs as $db) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=$db", 'root', '');
        $res = $pdo->query("SELECT id, nama FROM guru WHERE nama LIKE '%Sulis%'")->fetchAll(PDO::FETCH_ASSOC);
        echo "DB: $db\n";
        print_r($res);
        $res2 = $pdo->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        echo "TOTAL GURU in $db: $res2\n\n";
    } catch (Exception $e) {
        echo "DB $db err: " . $e->getMessage() . "\n";
    }
}
