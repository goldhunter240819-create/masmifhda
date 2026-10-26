<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Services/PkkmSyncService.php';

use App\Services\PkkmSyncService;

echo "========================================================\n";
echo "  SINKRONISASI INSTRUMEN PKKM 2026 (42 INDIKATOR PDF)   \n";
echo "========================================================\n";

$dataset = PkkmSyncService::getDataset();
echo "Data siap:\n";
echo "- Komponen: " . count($dataset['komponen']) . "\n";
echo "- Sub-Komponen: " . count($dataset['sub_komponen']) . "\n";
echo "- Indikator: " . count($dataset['indikator']) . "\n";

$res = PkkmSyncService::executeSync();
print_r($res);
