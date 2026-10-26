<?php
require 'apk/koneksi.php';
$gurus = $core_db->query("SELECT id FROM guru")->fetchAll(PDO::FETCH_ASSOC);

$stmt_check = $core_db->prepare("SELECT COUNT(*) FROM apk_akses_guru WHERE guru_id = ? AND app_code = 'absen_qr'");
$stmt_insert = $core_db->prepare("INSERT IGNORE INTO apk_akses_guru (guru_id, app_code) VALUES (?, 'absen_qr')");

$count = 0;
foreach ($gurus as $g) {
    $stmt_check->execute([$g['id']]);
    if ($stmt_check->fetchColumn() == 0) {
        $stmt_insert->execute([$g['id']]);
        $count++;
    }
}
echo "Added absen_qr to $count gurus.";
