<?php
/**
 * Runner Seed Data Dummy Ekstrakurikuler di Server Live
 */
header('Content-Type: text/plain');

require_once __DIR__ . '/vendor/autoload.php';

echo "=== MENJALANKAN SEEDER SELURUH EKSTRAKURIKULER DI SERVER LIVE ===\n\n";

try {
    // 1. Eksekusi ROHIS jika ada
    if (file_exists(__DIR__ . '/scripts/seed_ekstra_dummy.php')) {
        require_once __DIR__ . '/scripts/seed_ekstra_dummy.php';
    }

    // 2. Eksekusi seluruh cabang ekstrakurikuler lainnya
    if (file_exists(__DIR__ . '/scripts/seed_all_ekstra_dummy.php')) {
        require_once __DIR__ . '/scripts/seed_all_ekstra_dummy.php';
    }

    echo "\nSUKSES! Seluruh data dummy ekstrakurikuler berhasil dimasukkan ke database server live!\n";
} catch (\Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
