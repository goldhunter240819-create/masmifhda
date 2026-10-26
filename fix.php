<?php
require_once __DIR__ . '/app/Core/Database.php';
$config = require __DIR__ . '/config/database.php';
$pdo = \App\Core\Database::connect('siakad');

// Fix pertanyaan
$pdo->query("UPDATE bs_soal SET pertanyaan = REPLACE(pertanyaan, '^2', '²')");
$pdo->query("UPDATE bs_soal SET pertanyaan = REPLACE(pertanyaan, '^3', '³')");

// Fix konten_tambahan
$pdo->query("UPDATE bs_soal SET konten_tambahan = REPLACE(konten_tambahan, '^2', '²')");
$pdo->query("UPDATE bs_soal SET konten_tambahan = REPLACE(konten_tambahan, '^3', '³')");

echo "Fixed pangkat!";
