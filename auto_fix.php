<?php
require_once 'apk/koneksi.php';

$json = file_get_contents('mistakes.json');
$mistakes = json_decode($json, true);

$count = 0;
foreach($mistakes as $m) {
    if (isset($m['fix_to'])) {
        $stmt = $siakad_db->prepare("UPDATE jurnal_guru SET mapel_id = ? WHERE id = ?");
        $stmt->execute([$m['fix_to'], $m['jurnal_id']]);
        $count++;
    }
}

echo "Berhasil memperbaiki $count kesalahan secara otomatis.\n";
?>
