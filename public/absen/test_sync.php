<?php
include 'public/absen/includes/koneksi.php';
include 'public/absen/includes/sync_handler.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting Sync...\n";
syncSiakad($koneksi, 'all');
echo "Sync Finished.\n";
?>
