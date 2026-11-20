<?php
/**
 * CBT Madrasah Standalone Packager
 * Membuat bundle zip khusus server proktor madrasah yang HANYA berisi engine CBT
 * Bebas dari data SIAKAD, Keuangan/SPP, Kasir, Tabungan, PKKM, Rapor, PIP, dll.
 */

function build_cbt_standalone_package() {
    $baseDir = realpath(__DIR__ . '/..');
    $outDir = $baseDir . '/public/downloads';
    if (!is_dir($outDir)) {
        @mkdir($outDir, 0755, true);
    }
    $zipPath = $outDir . '/cbt_standalone_madrasah.zip';

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }

    // 1. Berkas APK khusus CBT
    $apkFiles = [
        'cbt.php', 'cbt_scan.php', 'hasil_ujian.php', 'login.php', 'logout.php',
        'koneksi.php', 'header.php', 'footer.php', 'profil_cbt.php',
        'apk_version_guard.php', 'ping_tracker.php', 'sw.js', 'manifest.json', 'cbt_sync_api.php'
    ];
    foreach ($apkFiles as $f) {
        $fullPath = $baseDir . '/apk/' . $f;
        if (file_exists($fullPath)) {
            $zip->addFile($fullPath, 'apk/' . $f);
        }
    }

    // Settings APK tanpa force update di server lokal
    $apkSettings = [
        "download_url" => "https://apk.masmifhda.sch.id/download.php",
        "onesignal_app_id" => "",
        "onesignal_rest_key" => "",
        "show_holiday_banner" => false,
        "maintenance_mode" => false,
        "elearning_enabled" => false,
        "min_apk_version" => "1.0.0",
        "min_apk_version_code" => 1,
        "force_update" => false
    ];
    $zip->addFromString('apk/apk_settings.json', json_encode($apkSettings, JSON_PRETTY_PRINT));

    if (file_exists($baseDir . '/apk/pwa/index.php')) {
        $zip->addFile($baseDir . '/apk/pwa/index.php', 'apk/pwa/index.php');
    }

    // 2. Berkas App (Controller, Core, Service)
    $zip->addFile($baseDir . '/app/Controllers/CbtController.php', 'app/Controllers/CbtController.php');
    $zip->addFile($baseDir . '/app/Core/Database.php', 'app/Core/Database.php');
    $zip->addFile($baseDir . '/app/Core/Router.php', 'app/Core/Router.php');
    if (file_exists($baseDir . '/app/Core/Branding.php')) {
        $zip->addFile($baseDir . '/app/Core/Branding.php', 'app/Core/Branding.php');
    }
    $zip->addFile($baseDir . '/app/Services/AuditLogger.php', 'app/Services/AuditLogger.php');

    // 3. Config Database Local Default
    $dbConfigContent = <<<'PHP'
<?php
/**
 * Database Configuration - CBT Madrasah Standalone
 * Konfigurasi koneksi MySQL lokal XAMPP pada laptop proktor madrasah.
 */
return [
    'core' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_db',
        'username' => 'root',
        'password' => '',
    ],
    'siakad' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_siakad_db',
        'username' => 'root',
        'password' => '',
    ],
    'absen' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_db',
        'username' => 'root',
        'password' => '',
    ],
    'absensi' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_db',
        'username' => 'root',
        'password' => '',
    ],
];
PHP;
    $zip->addFromString('config/database.php', $dbConfigContent);

    // 4. Resources Views CBT & Master Layout
    $cbtViews = glob($baseDir . '/resources/views/cbt/*.php');
    foreach ($cbtViews as $cv) {
        $zip->addFile($cv, 'resources/views/cbt/' . basename($cv));
    }
    if (file_exists($baseDir . '/resources/views/siakad/layout.php')) {
        $zip->addFile($baseDir . '/resources/views/siakad/layout.php', 'resources/views/siakad/layout.php');
    }

    // 5. Minimal Standalone public/index.php (Hanya rute CBT & auth proktor, bersih dari modul SIAKAD lain)
    $publicIndexContent = <<<'PHP'
<?php
date_default_timezone_set('Asia/Jakarta');
session_name("AULA_SYSTEM_PORTAL");

require_once __DIR__ . '/../vendor/autoload.php';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $base = str_replace('\\', '/', $base);
        $base = preg_replace('#/public.*$#', '', $base);
        if ($base === '/' || $base === '\\') $base = '';
        $path = ltrim($path, '/');
        return $base . '/' . $path;
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        header("Location: " . base_url($path));
        exit;
    }
}

$router = new \App\Core\Router();

// Root & Login redirection
$router->add('GET', '/', function() { redirect('/cbt/semi-online'); });
$router->add('GET', '/portal', function() { redirect('/cbt/semi-online'); });
$router->add('GET', '/login', function() { redirect('/apk/login.php'); });
$router->add('GET', '/logout', function() { redirect('/apk/logout.php'); });

// CBT Management Suite
$router->add('GET', '/cbt', [\App\Controllers\CbtController::class, 'dashboard']);
$router->add('GET', '/cbt/dashboard', [\App\Controllers\CbtController::class, 'dashboard']);

$router->add('GET', '/cbt/topik', [\App\Controllers\CbtController::class, 'topik']);
$router->add('POST', '/cbt/topik/save', [\App\Controllers\CbtController::class, 'saveTopik']);
$router->add('GET', '/cbt/topik/delete', [\App\Controllers\CbtController::class, 'deleteTopik']);
$router->add('POST', '/cbt/topik/delete', [\App\Controllers\CbtController::class, 'deleteTopik']);
$router->add('POST', '/cbt/topik/reset', [\App\Controllers\CbtController::class, 'resetTopik']);
$router->add('POST', '/cbt/topik/dates/save', [\App\Controllers\CbtController::class, 'saveTopikDates']);

$router->add('GET', '/cbt/jadwal', [\App\Controllers\CbtController::class, 'jadwal']);
$router->add('POST', '/cbt/jadwal/save', [\App\Controllers\CbtController::class, 'saveJadwal']);
$router->add('POST', '/cbt/jadwal/toggle-status', [\App\Controllers\CbtController::class, 'toggleStatusJadwal']);
$router->add('POST', '/cbt/jadwal/delete', [\App\Controllers\CbtController::class, 'deleteJadwal']);
$router->add('POST', '/cbt/jadwal/grid/save', [\App\Controllers\CbtController::class, 'saveGridJadwal']);
$router->add('POST', '/cbt/jadwal/grid/delete', [\App\Controllers\CbtController::class, 'deleteGridJadwal']);
$router->add('POST', '/cbt/jadwal/kelas/save-bulk', [\App\Controllers\CbtController::class, 'saveBulkKelasJadwal']);
$router->add('POST', '/cbt/jadwal/global/save', [\App\Controllers\CbtController::class, 'saveGlobalJadwal']);
$router->add('POST', '/cbt/jadwal/slot/save', [\App\Controllers\CbtController::class, 'saveJamSlot']);
$router->add('POST', '/cbt/jadwal/slot/delete', [\App\Controllers\CbtController::class, 'deleteJamSlot']);
$router->add('POST', '/cbt/jadwal/slot/reset', [\App\Controllers\CbtController::class, 'resetJamSlot']);
$router->add('POST', '/cbt/jadwal/topik/save', [\App\Controllers\CbtController::class, 'saveTopikJadwal']);

$router->add('GET', '/cbt/ujian', function() { redirect('/cbt/jadwal'); });
$router->add('POST', '/cbt/ujian/save', [\App\Controllers\CbtController::class, 'saveUjian']);
$router->add('POST', '/cbt/ujian/delete', [\App\Controllers\CbtController::class, 'deleteUjian']);
$router->add('POST', '/cbt/ujian/sync-peserta', [\App\Controllers\CbtController::class, 'syncPesertaAction']);

$router->add('GET', '/cbt/kerjakan', [\App\Controllers\CbtController::class, 'kerjakan']);
$router->add('POST', '/cbt/kerjakan/simpan-jawaban', [\App\Controllers\CbtController::class, 'ajaxSimpanJawaban']);
$router->add('POST', '/cbt/kerjakan/pelanggaran', [\App\Controllers\CbtController::class, 'ajaxCatatPelanggaran']);
$router->add('POST', '/cbt/kerjakan/submit', [\App\Controllers\CbtController::class, 'submitKerjakan']);
$router->add('GET', '/cbt/kerjakan/selesai', [\App\Controllers\CbtController::class, 'selesaiKerjakan']);

$router->add('GET', '/cbt/soal', [\App\Controllers\CbtController::class, 'soal']);
$router->add('POST', '/cbt/soal/save', [\App\Controllers\CbtController::class, 'saveSoal']);
$router->add('POST', '/cbt/soal/delete', [\App\Controllers\CbtController::class, 'deleteSoal']);

$router->add('GET', '/cbt/token', [\App\Controllers\CbtController::class, 'token']);
$router->add('POST', '/cbt/token/generate', [\App\Controllers\CbtController::class, 'generateToken']);
$router->add('POST', '/cbt/token/expire', [\App\Controllers\CbtController::class, 'expireToken']);

$router->add('GET', '/cbt/hasil', [\App\Controllers\CbtController::class, 'hasil']);
$router->add('GET', '/cbt/hasil/cetak', [\App\Controllers\CbtController::class, 'cetakHasil']);

$router->add('GET', '/cbt/ruang', [\App\Controllers\CbtController::class, 'ruang']);
$router->add('POST', '/cbt/ruang/save', [\App\Controllers\CbtController::class, 'saveRuang']);
$router->add('POST', '/cbt/ruang/delete', [\App\Controllers\CbtController::class, 'deleteRuang']);
$router->add('POST', '/cbt/ruang/pengawas/save', [\App\Controllers\CbtController::class, 'savePengawasRuang']);

$router->add('GET', '/cbt/denah', [\App\Controllers\CbtController::class, 'denah']);
$router->add('POST', '/cbt/denah/generate', [\App\Controllers\CbtController::class, 'generateDenah']);
$router->add('POST', '/cbt/denah/reset', [\App\Controllers\CbtController::class, 'resetDenah']);
$router->add('POST', '/cbt/denah/assign-manual', [\App\Controllers\CbtController::class, 'assignManualDenah']);
$router->add('POST', '/cbt/denah/remove-seat', [\App\Controllers\CbtController::class, 'removeSeatDenah']);
$router->add('GET', '/cbt/denah/cetak', [\App\Controllers\CbtController::class, 'cetakDenah']);
$router->add('GET', '/cbt/denah/cetak-label', [\App\Controllers\CbtController::class, 'cetakLabelMeja']);

$router->add('GET', '/cbt/scan', [\App\Controllers\CbtController::class, 'absensiScan']);
$router->add('POST', '/cbt/scan/process', [\App\Controllers\CbtController::class, 'processAbsensiScan']);
$router->add('GET', '/cbt/absensi', [\App\Controllers\CbtController::class, 'absensiRekap']);
$router->add('POST', '/cbt/absensi/delete', [\App\Controllers\CbtController::class, 'deleteAbsensi']);
$router->add('POST', '/cbt/absensi/reset-today', [\App\Controllers\CbtController::class, 'resetAbsensiToday']);

$router->add('POST', '/cbt/hasil/buka-ujian', [\App\Controllers\CbtController::class, 'bukaUjianKembali']);
$router->add('POST', '/cbt/peserta/buka-ujian', [\App\Controllers\CbtController::class, 'bukaUjianKembali']);
$router->add('GET', '/cbt/analisis', [\App\Controllers\CbtController::class, 'analisis']);
$router->add('GET', '/cbt/pengaturan', [\App\Controllers\CbtController::class, 'pengaturan']);
$router->add('POST', '/cbt/pengaturan/save', [\App\Controllers\CbtController::class, 'savePengaturan']);
$router->add('POST', '/cbt/pengaturan/reset-siswa', [\App\Controllers\CbtController::class, 'resetSiswaTerkunci']);
$router->add('POST', '/cbt/pengaturan/reset-default', [\App\Controllers\CbtController::class, 'resetDefaultPengaturan']);

$router->add('GET', '/cbt/peserta_khusus', [\App\Controllers\CbtController::class, 'pesertaKhusus']);
$router->add('POST', '/cbt/peserta_khusus/save', [\App\Controllers\CbtController::class, 'savePesertaKhusus']);
$router->add('POST', '/cbt/peserta_khusus/delete', [\App\Controllers\CbtController::class, 'deletePesertaKhusus']);

// Semi-Online Routes
$router->add('GET', '/cbt/semi-online', [\App\Controllers\CbtController::class, 'semiOnlineDashboard']);
$router->add('POST', '/cbt/semi-online/pull', [\App\Controllers\CbtController::class, 'ajaxPullDataOnline']);
$router->add('POST', '/cbt/semi-online/pull-absen', [\App\Controllers\CbtController::class, 'ajaxPullAbsensiOnline']);
$router->add('POST', '/cbt/semi-online/push', [\App\Controllers\CbtController::class, 'ajaxPushHasilOnline']);
$router->add('POST', '/cbt/semi-online/export-file', [\App\Controllers\CbtController::class, 'exportBackupFile']);
$router->add('POST', '/cbt/semi-online/import-file', [\App\Controllers\CbtController::class, 'importBackupFile']);
$router->add('GET', '/cbt/semi-online/api/export-paket', [\App\Controllers\CbtController::class, 'apiExportPaketOnline']);
$router->add('POST', '/cbt/semi-online/api/export-paket', [\App\Controllers\CbtController::class, 'apiExportPaketOnline']);
$router->add('POST', '/cbt/semi-online/api/import-hasil', [\App\Controllers\CbtController::class, 'apiImportHasilOnline']);

// Dispatch router
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = dirname($_SERVER['SCRIPT_NAME']);
$base = str_replace('\\', '/', $base);
$base = preg_replace('#/public.*$#', '', $base);
if ($base !== '/' && $base !== '') {
    $requestUri = preg_replace('#^' . preg_quote($base, '#') . '#', '', $requestUri);
}
if (empty($requestUri)) $requestUri = '/';

$router->dispatch($requestMethod, $requestUri);
PHP;
    $zip->addFromString('public/index.php', $publicIndexContent);
    if (file_exists($baseDir . '/public/.htaccess')) {
        $zip->addFile($baseDir . '/public/.htaccess', 'public/.htaccess');
    }
    if (file_exists($baseDir . '/public/logo_kemenag.png')) {
        $zip->addFile($baseDir . '/public/logo_kemenag.png', 'public/logo_kemenag.png');
    }

    // Assets css/js/fonts
    if (is_dir($baseDir . '/public/assets')) {
        $assetsIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir . '/public/assets', RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($assetsIterator as $item) {
            $subPath = str_replace('\\', '/', substr($item->getPathname(), strlen($baseDir . '/public/assets/')));
            if ($item->isFile()) {
                $zip->addFile($item->getPathname(), 'public/assets/' . $subPath);
            }
        }
    }

    // 6. Vendor Autoload & Composer
    $zip->addFile($baseDir . '/vendor/autoload.php', 'vendor/autoload.php');
    $composerFiles = glob($baseDir . '/vendor/composer/*');
    foreach ($composerFiles as $cf) {
        if (is_file($cf)) {
            $zip->addFile($cf, 'vendor/composer/' . basename($cf));
        }
    }

    // 7. Root index.php & .htaccess
    $rootIndexContent = <<<'PHP'
<?php
$baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($baseUri === '/' || $baseUri === '\\') $baseUri = '';
header('Location: ' . $baseUri . '/cbt/semi-online');
exit;
PHP;
    $zip->addFromString('index.php', $rootIndexContent);
    if (file_exists($baseDir . '/.htaccess')) {
        $zip->addFile($baseDir . '/.htaccess', '.htaccess');
    }

    // 8. Marker file .cbt_standalone
    $zip->addFromString('.cbt_standalone', 'CBT_MADRASAH_STANDALONE=1');

    // 9. Installer Web 1-Klik: setup_db.php
    $setupDbContent = <<<'PHP'
<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

$statusMsg = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    try {
        $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS aula_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS aula_siakad_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $db = new PDO("mysql:host=$host;dbname=aula_db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        require_once __DIR__ . '/vendor/autoload.php';
        \App\Controllers\CbtController::ensureTables($db);
        
        $isSuccess = true;
        $statusMsg = "Database aula_db & aula_siakad_db serta seluruh tabel CBT berhasil dibuat otomatis!";
    } catch (Exception $e) {
        $statusMsg = "Gagal setup database: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database Server CBT Madrasah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-3xl p-8 shadow-2xl text-center">
        <div class="w-20 h-20 bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-2xl flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fa-solid fa-server"></i>
        </div>
        <h1 class="text-2xl font-black mb-2 text-white">Server CBT Madrasah</h1>
        <p class="text-sm text-slate-400 mb-6">Inisialisasi Database Standalone Server Proktor</p>

        <?php if ($isSuccess): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 p-4 rounded-xl text-sm mb-6 flex items-center gap-3 text-left">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <div>
                    <div class="font-bold">Setup Berhasil!</div>
                    <div class="text-xs"><?= htmlspecialchars($statusMsg) ?></div>
                </div>
            </div>
            <a href="cbt/semi-online" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-xl block transition">
                <i class="fa-solid fa-cloud-arrow-down mr-2"></i> Buka Menu Tarik Data (Semi-Online)
            </a>
        <?php else: ?>
            <?php if (!empty($statusMsg)): ?>
                <div class="bg-rose-500/20 border border-rose-500/40 text-rose-300 p-4 rounded-xl text-sm mb-6 text-left">
                    <div class="font-bold">Terjadi Kesalahan:</div>
                    <div class="text-xs mt-1"><?= htmlspecialchars($statusMsg) ?></div>
                </div>
            <?php endif; ?>
            <form method="POST">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bolt"></i> Mulai Inisialisasi Database (1-Klik)
                </button>
            </form>
        <?php endif; ?>
        <div class="mt-6 text-xs text-slate-500">
            Login Admin Default: <b>proktor</b> / <b>proktor123</b>
        </div>
    </div>
</body>
</html>
PHP;
    $zip->addFromString('setup_db.php', $setupDbContent);

    // 10. Panduan Lengkap Proktor
    $panduanContent = <<<'TXT'
================================================================================
   PANDUAN LENGKAP SERVER CBT MADRASAH (SEMI-ONLINE STANDALONE)
   Madrasah Aliyah Miftahul Huda (MAS MIFHDA)
================================================================================

Paket ini dirancang KHUSUS untuk laptop server proktor di madrasah.
Paket ini HANYA berisi engine CBT, bank soal, jadwal, token, dan absensi ujian.
TIDAK ADA data sensitif sekolah (Keuangan, SPP, Kasir, Rapor, PKKM, dll).

LANGKAH 1: PERSIAPAN DI LAPTOP SERVER MADRASAH
1. Pasang aplikasi XAMPP (Download di https://www.apachefriends.org jika belum ada).
2. Jalankan XAMPP Control Panel, lalu klik "Start" pada:
   - Apache
   - MySQL
3. Ekstrak isi file zip ini ke folder:
   C:\xampp\htdocs\cbt
   (sehingga di dalam folder C:\xampp\htdocs\cbt terdapat folder apk, app, public, setup_db.php, dll)

LANGKAH 2: SETUP DATABASE 1-KLIK
1. Buka browser Google Chrome di laptop server, lalu ketik alamat:
   http://localhost/cbt/setup_db.php
2. Klik tombol "Mulai Inisialisasi Database (1-Klik)".
   Sistem akan otomatis membuat database `aula_db` dan seluruh tabel CBT dalam 1 detik!
3. Akun proktor default:
   - Username : proktor
   - Password : proktor123

LANGKAH 3: TARIK DATA DARI SERVER PUSAT
1. Di browser laptop proktor, buka menu:
   http://localhost/cbt/semi-online
2. Di bagian "Langkah 1 (Pra-Ujian): Tarik Data dari Server Pusat":
   - Pastikan URL Server Pusat: https://apk.masmifhda.sch.id
   - Klik tombol ungu: "Mulai Tarik Data dari Pusat"
3. Tunggu ~5 detik hingga muncul notifikasi sukses! Seluruh topik, bank soal,
   jadwal sesi, token ujian, denah meja, dan akun peserta ujian sudah tersinkron!

LANGKAH 4: HUBUNGKAN KE WI-FI MADRASAH & AKSES SISWA
1. Hubungkan laptop server proktor ke router Wi-Fi madrasah (menggunakan kabel LAN atau Wi-Fi).
2. Di halaman http://localhost/cbt/semi-online, lihat "Alamat IP Server Ini (Wi-Fi)",
   misalnya: 192.168.1.100.
3. Hubungkan HP / Laptop siswa ke nama Wi-Fi madrasah yang sama.
4. Siswa membuka Google Chrome di HP dan mengetik alamat:
   http://192.168.1.100/cbt/apk/cbt.php
   (Atau siswa bisa scan QR Code yang ada di halaman proktor).
5. Siswa login menggunakan NIS dan Password akun madrasah masing-masing.
6. Siswa mengerjakan ujian dengan sangat cepat, lancar, tanpa lag, dan 100% hemat kuota!

LANGKAH 5: ABSENSI SCAN 1 PINTU DI MADRASAH
- Petugas/pengawas madrasah dapat membuka scanner di:
  http://192.168.1.100/cbt/apk/cbt_scan.php
- Arahkan kamera ke barcode kartu ujian siswa untuk absensi otomatis per mapel/sesi!

LANGKAH 6: KIRIM HASIL KE SERVER PUSAT (SETELAH UJIAN SELESAI)
1. Setelah seluruh sesi ujian hari itu selesai, hubungkan laptop server ke internet (Hotspot HP / IndiHome).
2. Di halaman http://localhost/cbt/semi-online, lihat "Langkah 2: Kirim Hasil ke Server Pusat".
3. Klik tombol hijau: "Kirim Hasil Ujian ke Pusat".
4. Selesai! Semua nilai siswa, lembar jawaban, dan riwayat absensi lokal langsung
   tergabung kembali di Server Pusat bersama data siswa Kelas Jauh!
================================================================================
TXT;
    $zip->addFromString('PANDUAN_SETUP_PROKTOR_MADRASAH.txt', $panduanContent);

    $numFiles = $zip->numFiles;
    $zip->close();

    return [
        'path' => $zipPath,
        'size' => filesize($zipPath),
        'files' => $numFiles
    ];
}

// Jika dijalankan langsung via CLI
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    echo "Memulai pembuatan paket standalone CBT Madrasah...\n";
    $res = build_cbt_standalone_package();
    if ($res) {
        echo "BERHASIL! File: " . $res['path'] . "\n";
        echo "Ukuran: " . round($res['size'] / 1024, 2) . " KB\n";
        echo "Jumlah file di dalam zip: " . $res['files'] . "\n";
    } else {
        echo "GAGAL membuat paket zip.\n";
    }
}
