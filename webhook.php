<?php
echo "Menerima request webhook MAS Mifhda...\n";

// 1. Bersihkan perubahan lokal tak sengaja di server agar pull tidak abort/konflik
shell_exec('git -c safe.directory="*" checkout -- . 2>&1');
shell_exec('git -c safe.directory="*" clean -fd 2>&1');

// 2. Tarik kode terbaru dari GitHub origin/main dan paksa sync ke HEAD origin/main
shell_exec('git -c safe.directory="*" fetch origin main 2>&1');
$output = shell_exec('git -c safe.directory="*" reset --hard origin/main 2>&1');
$headLog = shell_exec('git -c safe.directory="*" log -1 --oneline 2>&1');
$output .= "\nHEAD: " . trim($headLog);

// 3. Reset OPcache agar PHP langsung menyajikan kodingan terbaru tanpa restart Apache
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// 3.5 Bersihkan & Migrasikan Bank Soal ke Basis Tingkat
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
if (file_exists(__DIR__ . '/app/Core/AcademicYear.php')) {
    require_once __DIR__ . '/app/Core/AcademicYear.php';
}
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Controllers/BankSoalController.php';
try {
    $db = \App\Core\Database::connect('siakad') ?: \App\Core\Database::connect('core');
    \App\Controllers\BankSoalController::ensureTables($db);
    $activeTopikIds = $db->query("SELECT id FROM bs_topik WHERE status = 'Aktif'")->fetchAll(PDO::FETCH_COLUMN);
    $bsCtrl = new \App\Controllers\BankSoalController();
    foreach ($activeTopikIds as $actId) {
        $bsCtrl->syncFromPengampu((int)$actId, true);
    }
    $output .= "\nBank Soal synced to Tingkat successfully.\n";
} catch (\Exception $e) {
    $output .= "\nBank Soal migration: " . $e->getMessage() . "\n";
}

// 3.6 Update Brand Names & Tab Titles di Database
try {
    $dbCore = \App\Core\Database::connect('core');
    $dbCore->exec("UPDATE app_modules SET app_name = 'MA Miftahul Huda', tab_title = 'MA Miftahul Huda' WHERE app_code = 'portal' OR app_name LIKE '%Aula%' OR tab_title LIKE '%Aula%'");
    $output .= "\nApp modules branding updated to MA Miftahul Huda successfully.\n";
} catch (\Exception $e) {
    $output .= "\nBranding update migration error: " . $e->getMessage() . "\n";
}

// 3.7 Ensure kurikulum_supervisi_administrasi columns
try {
    $dbCore = \App\Core\Database::connect('core');
    $colsToAdd = [
        'keterangan' => 'TEXT NULL',
        'pegawai_id' => 'INT NULL',
        'guru_id' => 'INT NULL',
        'supervisor_id' => 'INT NULL',
        'penilai_id' => 'INT NULL',
        'tanggal_supervisi' => 'DATE NULL',
        'penilai_nama' => 'VARCHAR(150) NULL',
        'nilai_akhir' => 'FLOAT NULL'
    ];
    foreach ($colsToAdd as $c => $t) {
        try { $dbCore->exec("ALTER TABLE `kurikulum_supervisi_administrasi` ADD COLUMN `$c` $t"); } catch (\Throwable $e) {}
    }
    $output .= "\nEnsured all columns in kurikulum_supervisi_administrasi.\n";
} catch (\Exception $e) {}



// 4. Catat riwayat ke log
$logData = date('Y-m-d H:i:s') . "\nResult:\n" . $output . "\n-------------------------\n";
@file_put_contents(__DIR__ . '/webhook.log', $logData, FILE_APPEND);

echo "<pre>$output</pre>";
echo "Berhasil update otomatis MAS Mifhda!";
?>
