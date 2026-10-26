<?php
require 'app/Core/Database.php';
$db_siakad = App\Core\Database::connect('siakad');
$stmt = $db_siakad->query("SELECT * FROM jadwal_pelajaran WHERE kelas_id=1 AND hari='Jumat'");
$jadwals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$temp = [];
foreach($jadwals as $j) {
    $k = $j['hari'].'_'.$j['kelas_id'].'_'.$j['jam_mulai'];
    if(!isset($temp[$k]) || $j['id'] > $temp[$k]['id']) {
        $temp[$k] = $j;
    }
}
print_r($temp);
?>
