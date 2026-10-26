<?php
require 'config/database.php';
$dbConfig = include('config/database.php');
$c = $dbConfig['core'];
try {
    $pdo = new PDO('mysql:host='.$c['host'].';dbname='.$c['db_name'], $c['username'], $c['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Reset Siswa
    $stmtSiswa = $pdo->query("SELECT id, user_id, nik, nis, nama FROM siswa WHERE user_id IS NOT NULL");
    $siswas = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);
    $siswaCount = 0;
    foreach ($siswas as $s) {
        $username = trim($s['nik']);
        if (empty($username)) {
            $username = trim($s['nis']);
        }
        if (empty($username)) {
            $username = "S" . $s['id'] . rand(10,99);
        }
        
        $password = $username;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $upd = $pdo->prepare("UPDATE users SET username = ?, password_hash = ?, plain_password = ? WHERE id = ?");
        try {
            $upd->execute([$username, $hash, $password, $s['user_id']]);
            $siswaCount++;
        } catch (Exception $e) {
            echo "Failed to update Siswa ID {$s['id']} (User ID {$s['user_id']}) - " . $e->getMessage() . "\n";
        }
    }
    echo "Successfully updated $siswaCount Siswa accounts.\n";
    
    // 2. Reset Guru
    $stmtGuru = $pdo->query("SELECT id, user_id, nik, nip, nama FROM guru WHERE user_id IS NOT NULL");
    $gurus = $stmtGuru->fetchAll(PDO::FETCH_ASSOC);
    $guruCount = 0;
    foreach ($gurus as $g) {
        $username = trim($g['nik']);
        if (empty($username)) {
            $username = trim($g['nip']);
        }
        if (empty($username)) {
            $username = "G" . $g['id'] . rand(10,99);
        }
        
        $password = $username;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $upd = $pdo->prepare("UPDATE users SET username = ?, password_hash = ?, plain_password = ? WHERE id = ?");
        try {
            $upd->execute([$username, $hash, $password, $g['user_id']]);
            $guruCount++;
        } catch (Exception $e) {
            echo "Failed to update Guru ID {$g['id']} (User ID {$g['user_id']}) - " . $e->getMessage() . "\n";
        }
    }
    echo "Successfully updated $guruCount Guru accounts.\n";
    
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
