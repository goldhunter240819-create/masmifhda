<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=core_db', 'root', '');
$gurus = $pdo->query("SELECT g.*, u.username, u.plain_password FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($gurus as $g) {
    if (strpos($g['nama'], 'SULIS') !== false) {
        echo $g['nama'] . "\n";
    }
}
echo "TOTAL GURU: " . count($gurus) . "\n";
