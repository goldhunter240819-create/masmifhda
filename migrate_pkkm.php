<?php
// mismifhda/migrate_pkkm.php
// Script Migrasi Database Otomatis untuk PKKM & Akun Pengawas Penilai

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use App\Core\Database;

header('Content-Type: text/html; charset=utf-8');

$logs = [];
$success = true;

try {
    $db = Database::connect('core');
    $logs[] = ["status" => "ok", "msg" => "Berhasil terhubung ke database core MySQL."];

    // 1. Pastikan tabel roles ada dan role 5 'pengawas' terdaftar
    $db->exec("CREATE TABLE IF NOT EXISTS `roles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL UNIQUE,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $db->exec("INSERT IGNORE INTO `roles` (`id`, `name`) VALUES (5, 'pengawas');");
    $logs[] = ["status" => "ok", "msg" => "Role 5 ('pengawas') tervalidasi di tabel roles."];

    // 2. Buat tabel pkkm_pengawas jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_pengawas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `nama` varchar(150) NOT NULL,
        `nip` varchar(50) DEFAULT '-',
        `jabatan` varchar(100) DEFAULT 'Pengawas Madrasah Ahli Madya',
        `instansi` varchar(150) DEFAULT 'Kantor Kementerian Agama Kab. Lampung Barat',
        `peran` enum('penilai_1','penilai_2') DEFAULT 'penilai_1',
        `no_hp` varchar(30) DEFAULT NULL,
        `ttd_digital` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    try {
        $db->exec("ALTER TABLE `pkkm_pengawas` ADD COLUMN `ttd_digital` TEXT NULL");
    } catch (\Exception $e) {}

    $logs[] = ["status" => "ok", "msg" => "Tabel 'pkkm_pengawas' siap digunakan (didukung tanda tangan digital)."];

    // 3. Buat tabel pkkm_penilai_skor jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_penilai_skor` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pengawas_id` int(11) NOT NULL,
        `indikator_id` int(11) NOT NULL,
        `skor` int(11) DEFAULT 0,
        `catatan` text DEFAULT NULL,
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_pengawas_indikator` (`pengawas_id`, `indikator_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $logs[] = ["status" => "ok", "msg" => "Tabel 'pkkm_penilai_skor' siap digunakan."];

    // 4. Pastikan tabel pkkm_nilai dasar ada
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_nilai` (
        `indikator_id` int(11) NOT NULL,
        `skor` int(11) DEFAULT 0,
        PRIMARY KEY (`indikator_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $logs[] = ["status" => "ok", "msg" => "Tabel 'pkkm_nilai' tervalidasi."];

    // 4b. Buat tabel pkkm_tim jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_tim` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `guru_id` int(11) NOT NULL,
        `komponen_id` int(11) NOT NULL,
        `peran` varchar(50) DEFAULT 'anggota',
        `tugas_keterangan` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $logs[] = ["status" => "ok", "msg" => "Tabel 'pkkm_tim' tervalidasi."];

    // 4c. Buat tabel pkkm_master_berkas jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_master_berkas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nama_berkas` varchar(255) NOT NULL,
        `kategori` varchar(50) DEFAULT 'program',
        `deskripsi` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_master_berkas_file` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `master_berkas_id` int(11) NOT NULL,
        `tipe` varchar(20) DEFAULT 'file',
        `url` text NOT NULL,
        `nama_file_asli` varchar(255) DEFAULT NULL,
        `uploaded_by` int(11) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $logs[] = ["status" => "ok", "msg" => "Tabel 'pkkm_master_berkas' & 'pkkm_master_berkas_file' tervalidasi."];

    // 5. Inisialisasi Akun Standar Penilai 1 (jika belum ada)
    $check1 = $db->query("SELECT p.id, u.username FROM pkkm_pengawas p LEFT JOIN users u ON u.id = p.user_id WHERE p.peran = 'penilai_1' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$check1) {
        $hash1 = password_hash('penilai123', PASSWORD_BCRYPT);
        
        // Cek apakah user 'penilai1' sudah ada di tabel users
        $u1 = $db->query("SELECT id FROM users WHERE username = 'penilai1'")->fetch();
        if ($u1) {
            $uid1 = $u1['id'];
            $db->prepare("UPDATE users SET password_hash = ?, role_id = 5, status = 'active' WHERE id = ?")->execute([$hash1, $uid1]);
        } else {
            $stmt1 = $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES ('penilai1', ?, 5, 'active')");
            $stmt1->execute([$hash1]);
            $uid1 = $db->lastInsertId();
        }

        $stmtP1 = $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtP1->execute([
            $uid1,
            'H. AHMAD SYARIF, M.Pd.I',
            '197405122002121004',
            'Pengawas Madrasah Ahli Madya',
            'Kantor Kementerian Agama Kab. Lampung Barat',
            'penilai_1',
            '081234567890'
        ]);
        $logs[] = ["status" => "ok", "msg" => "Akun Penilai 1 berhasil dibuat (User: penilai1 / Pass: penilai123)."];
    } else {
        $logs[] = ["status" => "info", "msg" => "Akun Penilai 1 sudah ada (User: " . ($check1['username'] ?? 'penilai1') . ")."];
    }

    // 6. Inisialisasi Akun Standar Penilai 2 (jika belum ada)
    $check2 = $db->query("SELECT p.id, u.username FROM pkkm_pengawas p LEFT JOIN users u ON u.id = p.user_id WHERE p.peran = 'penilai_2' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$check2) {
        $hash2 = password_hash('penilai223', PASSWORD_BCRYPT);
        
        // Cek apakah user 'penilai2' sudah ada di tabel users
        $u2 = $db->query("SELECT id FROM users WHERE username = 'penilai2'")->fetch();
        if ($u2) {
            $uid2 = $u2['id'];
            $db->prepare("UPDATE users SET password_hash = ?, role_id = 5, status = 'active' WHERE id = ?")->execute([$hash2, $uid2]);
        } else {
            $stmt2 = $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES ('penilai2', ?, 5, 'active')");
            $stmt2->execute([$hash2]);
            $uid2 = $db->lastInsertId();
        }

        $stmtP2 = $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtP2->execute([
            $uid2,
            'DRA. HJ. SITI NURHAYATI, M.Pd',
            '197803152005012003',
            'Pengawas Madrasah Ahli Muda',
            'Kantor Kementerian Agama Kab. Lampung Barat',
            'penilai_2',
            '081298765432'
        ]);
        $logs[] = ["status" => "ok", "msg" => "Akun Penilai 2 berhasil dibuat (User: penilai2 / Pass: penilai223)."];
    } else {
        $logs[] = ["status" => "info", "msg" => "Akun Penilai 2 sudah ada (User: " . ($check2['username'] ?? 'penilai2') . ")."];
    }

} catch (\Exception $e) {
    $success = false;
    $logs[] = ["status" => "error", "msg" => "Terjadi kesalahan: " . $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrasi Database PKKM & Akun Pengawas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        body {
            background: #0f172a;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .mig-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            max-width: 620px;
            width: 100%;
            overflow: hidden;
        }
        .mig-header {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            padding: 24px;
            text-align: center;
        }
        .mig-body {
            padding: 24px;
        }
        .log-item {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 8px;
            font-size: 0.86rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cred-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px dashed #10b981;
            border-radius: 12px;
            padding: 14px 18px;
            margin-top: 16px;
        }
    </style>
</head>
<body>

<div class="mig-card">
    <div class="mig-header">
        <h4 class="fw-bold text-white mb-1">⚡ Auto-Migration Database PKKM</h4>
        <p class="text-white-50 mb-0" style="font-size: 0.85rem;">
            Sinkronisasi Struktur Tabel & Pembuatan Akun Asesor Pengawas Live Server
        </p>
    </div>

    <div class="mig-body">
        <h6 class="fw-bold mb-3" style="color: #94a3b8; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.8px;">
            Log Eksekusi Database:
        </h6>

        <?php foreach ($logs as $log): ?>
            <div class="log-item">
                <?php if ($log['status'] === 'ok'): ?>
                    <span class="badge bg-success" style="font-size: 0.7rem;">OK</span>
                    <span style="color: #34d399;"><?php echo htmlspecialchars($log['msg']); ?></span>
                <?php elseif ($log['status'] === 'info'): ?>
                    <span class="badge bg-primary" style="font-size: 0.7rem;">INFO</span>
                    <span style="color: #93c5fd;"><?php echo htmlspecialchars($log['msg']); ?></span>
                <?php else: ?>
                    <span class="badge bg-danger" style="font-size: 0.7rem;">GAGAL</span>
                    <span style="color: #f87171;"><?php echo htmlspecialchars($log['msg']); ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <div class="cred-box">
                <div class="fw-bold text-success mb-2" style="font-size: 0.88rem;">
                    🔑 Akun Pengawas Penilai yang Siap Digunakan:
                </div>
                <div style="font-size: 0.82rem; line-height: 1.6; color: #e2e8f0;">
                    • <strong>Penilai 1:</strong> User <code>penilai1</code> | Pass: <code>penilai123</code> (H. Ahmad Syarif, M.Pd.I)<br>
                    • <strong>Penilai 2:</strong> User <code>penilai2</code> | Pass: <code>penilai223</code> (Dra. Hj. Siti Nurhayati, M.Pd)
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="/admin/pkkm/pengawas" class="btn btn-success fw-bold flex-fill py-2" style="border-radius: 10px; background: #059669; border: none;">
                    Kelola Akun Pengawas &rarr;
                </a>
                <a href="/pengawas/pkkm" class="btn btn-outline-light fw-bold flex-fill py-2" style="border-radius: 10px;">
                    Buka Portal Penilai &rarr;
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger mt-3 mb-0 rounded-3">
                Silakan periksa konfigurasi koneksi database Anda.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
