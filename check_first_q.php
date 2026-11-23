<?php
$lines = file('app/Controllers/CbtController.php');
echo "Checking lines before 660 for '?':\n";
for ($i = 0; $i < 660; $i++) {
    if (strpos($lines[$i], '?') !== false) {
        echo ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
