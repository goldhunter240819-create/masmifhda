<?php
$zip = new ZipArchive();
if ($zip->open('public/downloads/cbt_standalone_madrasah.zip') === true) {
    $content = $zip->getFromName('app/Controllers/CbtController.php');
    $lines = explode("\n", $content);
    echo "Total lines: " . count($lines) . "\n";
    echo "--- Lines 650 to 660 ---\n";
    for ($i = 649; $i <= 660; $i++) {
        if (isset($lines[$i])) {
            echo ($i + 1) . ": " . $lines[$i] . "\n";
        }
    }
    echo "--- Lines 845 to 860 ---\n";
    for ($i = 844; $i <= 860; $i++) {
        if (isset($lines[$i])) {
            echo ($i + 1) . ": " . $lines[$i] . "\n";
        }
    }
    $zip->close();
} else {
    echo "Failed to open zip\n";
}
