<?php
// update_brand_color.php - Update database theme colors to MAS MIFHDA Blue (#0ea5e9)

require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::connect('core');
    
    // 1. Update app_modules
    $stmt = $db->query("SELECT app_code, app_name, theme_color FROM app_modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Updating app_modules theme colors to MAS MIFHDA Ocean Blue:</h3><ul>";
    foreach ($modules as $m) {
        $code = $m['app_code'];
        $oldColor = $m['theme_color'];
        
        // If green or empty, change to 0ea5e9
        if (empty($oldColor) || in_array(strtolower($oldColor), ['10b981', '#10b981', '059669', '#059669', '047857', '#047857'])) {
            $up = $db->prepare("UPDATE app_modules SET theme_color = '0ea5e9' WHERE app_code = ?");
            $up->execute([$code]);
            echo "<li>Module <b>{$code}</b>: {$oldColor} &rarr; <span style='color:#0ea5e9;font-weight:bold;'>0ea5e9</span></li>";
        } else {
            echo "<li>Module <b>{$code}</b>: {$oldColor} (kept)</li>";
        }
    }
    echo "</ul>";

    // 2. Check institusi or apk_settings if exists
    try {
        $db->query("UPDATE institusi SET theme_color = '0ea5e9' WHERE theme_color IN ('10b981', '#10b981', '059669', '#059669')");
        echo "<p>Table `institusi` updated.</p>";
    } catch (\Throwable $e) {}

    try {
        $db->query("UPDATE apk_settings SET setting_value = '0ea5e9' WHERE setting_key = 'theme_color' AND setting_value IN ('10b981', '#10b981')");
        echo "<p>Table `apk_settings` updated.</p>";
    } catch (\Throwable $e) {}

    echo "<h4 style='color:#0284c7;'>✓ Database branding successfully aligned with MAS MIFHDA Blue (#0ea5e9)!</h4>";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
