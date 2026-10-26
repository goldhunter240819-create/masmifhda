<?php
$pdo_mts = new PDO('mysql:host=127.0.0.1;dbname=db_mts_rs;charset=utf8', 'root', '');
$pdo_aula = new PDO('mysql:host=127.0.0.1;dbname=aula_db;charset=utf8', 'root', '');

$tables = ['bk_kategori', 'bk_pelanggaran', 'bk_poin'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo_mts->query("SHOW CREATE TABLE $table");
        $createTableStmt = $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'];
        
        $pdo_aula->exec($createTableStmt);
        echo "Table $table created in aula_db.\n";
        
        // Also copy data just in case it's needed (like categories)
        if ($table === 'bk_kategori') {
            $data = $pdo_mts->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $row) {
                $cols = array_keys($row);
                $colsStr = implode(', ', $cols);
                $placeholders = implode(', ', array_fill(0, count($cols), '?'));
                $insertStmt = $pdo_aula->prepare("INSERT IGNORE INTO $table ($colsStr) VALUES ($placeholders)");
                $insertStmt->execute(array_values($row));
            }
            echo "Copied data for $table.\n";
        }
    } catch (Exception $e) {
        echo "Error on $table: " . $e->getMessage() . "\n";
    }
}
