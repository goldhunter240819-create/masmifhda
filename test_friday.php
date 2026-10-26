<?php
require 'app/Core/Database.php';

$db_core = Database::connect('core');
$db_siakad = Database::connect('siakad');

$jadwal_query = "SELECT * FROM jadwal_pelajaran WHERE kelas_id = 1 AND hari = 'Jumat'";
$stmt_j = $db_siakad->prepare($jadwal_query);
$stmt_j->execute();
$jadwals = $stmt_j->fetchAll(PDO::FETCH_ASSOC);

print_r($jadwals);
?>
