<?php
/**
 * Runner Seed Data Dummy Ekstrakurikuler ROHIS di Server Live
 */
header('Content-Type: text/plain');

require_once __DIR__ . '/vendor/autoload.php';

echo "=== MENJALANKAN SEEDER EKSTRAKURIKULER ROHIS DI SERVER ===\n";

try {
    require_once __DIR__ . '/scripts/seed_ekstra_dummy.php';
    echo "\nSUKSES! Data dummy ROHIS berhasil dimasukkan ke database server live!\n";
} catch (\Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
