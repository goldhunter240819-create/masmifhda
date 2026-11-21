<?php
/**
 * ⚡ SINKRONISASI KILAT & BERSIH SERVER CBT MADRASAH
 * Menyelaraskan seluruh data topik, bank soal, jadwal, dan akun dari server online pusat.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);
ini_set('memory_limit', '512M');
date_default_timezone_set('Asia/Jakarta');

$baseDir = __DIR__;
if (file_exists($baseDir . '/vendor/autoload.php')) {
    require_once $baseDir . '/vendor/autoload.php';
}

$dbConfig = file_exists($baseDir . '/config/database.php') ? require $baseDir . '/config/database.php' : [];
$host = $dbConfig['core']['host'] ?? '127.0.0.1';
$user = $dbConfig['core']['username'] ?? 'root';
$pass = $dbConfig['core']['password'] ?? '';
$coreDbName = $dbConfig['core']['db_name'] ?? 'aula_db';
$siakadDbName = $dbConfig['siakad']['db_name'] ?? 'aula_siakad_db';

$status = [];
$errorMsg = '';
$isDone = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['mulai'])) {
    try {
        $pdoCore = new PDO("mysql:host=$host;dbname=$coreDbName;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $pdoSiakad = new PDO("mysql:host=$host;dbname=$siakadDbName;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        if (class_exists('\App\Controllers\CbtController')) {
            \App\Controllers\CbtController::ensureTables($pdoCore);
        }
        if (class_exists('\App\Controllers\BankSoalController')) {
            \App\Controllers\BankSoalController::ensureTables($pdoSiakad);
            \App\Controllers\BankSoalController::ensureTables($pdoCore);
        }

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $apiUrl = $serverUrl . '/apk/cbt_sync_api.php?action=export&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || empty($response)) {
            $errorMsg = "Gagal menghubungi server pusat ($apiUrl). HTTP: $httpCode. Error: $curlErr";
        } else {
            $data = json_decode($response, true);
            if (!$data || ($data['status'] ?? '') !== 'success') {
                $errorMsg = "Format respon dari server pusat tidak valid.";
            } else {
                // 1. Bersihkan dummy topik dan sinkronkan 4 topik resmi
                if (!empty($data['topik'])) {
                    $validTopikIds = array_filter(array_map('intval', array_column($data['topik'], 'id')));
                    if (!empty($validTopikIds)) {
                        $inClauseT = implode(',', $validTopikIds);
                        $pdoCore->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)");
                        $pdoSiakad->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)");
                    }

                    $stmtT = $pdoCore->prepare("INSERT INTO bs_topik (id, tahun_ajaran, semester, nama_topik, jenis, tgl_mulai, tgl_selesai, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_topik = VALUES(nama_topik), jenis = VALUES(jenis), status = VALUES(status), tgl_mulai = VALUES(tgl_mulai), tgl_selesai = VALUES(tgl_selesai), tahun_ajaran = VALUES(tahun_ajaran), semester = VALUES(semester), keterangan = VALUES(keterangan)");
                    $stmtTS = $pdoSiakad->prepare("INSERT INTO bs_topik (id, tahun_ajaran, semester, nama_topik, jenis, tgl_mulai, tgl_selesai, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_topik = VALUES(nama_topik), jenis = VALUES(jenis), status = VALUES(status), tgl_mulai = VALUES(tgl_mulai), tgl_selesai = VALUES(tgl_selesai), tahun_ajaran = VALUES(tahun_ajaran), semester = VALUES(semester), keterangan = VALUES(keterangan)");

                    $cntT = 0;
                    foreach ($data['topik'] as $t) {
                        $vals = [$t['id'], $t['tahun_ajaran'] ?? '2026/2027', $t['semester'] ?? 'Ganjil', $t['nama_topik'], $t['jenis'] ?? ($t['jenis_topik'] ?? 'SAS'), $t['tgl_mulai'] ?? null, $t['tgl_selesai'] ?? null, $t['keterangan'] ?? null, $t['status'] ?? 'Terjadwal'];
                        $stmtT->execute($vals);
                        $stmtTS->execute($vals);
                        $cntT++;
                    }
                    $status['topik'] = $cntT;
                }

                // 2. Paket Bank Soal (bs_paket)
                if (!empty($data['bs_paket'])) {
                    $validPaketIds = array_filter(array_map('intval', array_column($data['bs_paket'], 'id')));
                    if (!empty($validPaketIds)) {
                        $inClauseP = implode(',', $validPaketIds);
                        $pdoCore->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)");
                        $pdoSiakad->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)");
                    }

                    $sqlP = "INSERT INTO bs_paket (id, topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_id, guru_nama, kkm, durasi_menit, is_locked, locked_at, locked_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), kelas_sasaran = VALUES(kelas_sasaran), guru_id = VALUES(guru_id), guru_nama = VALUES(guru_nama), kkm = VALUES(kkm), durasi_menit = VALUES(durasi_menit), is_locked = VALUES(is_locked)";
                    $stmtP1 = $pdoCore->prepare($sqlP);
                    $stmtP2 = $pdoSiakad->prepare($sqlP);

                    $cntP = 0;
                    foreach ($data['bs_paket'] as $p) {
                        $vals = [$p['id'], $p['topik_id'], $p['mapel_nama'], $p['kelas_nama'] ?? 'Kelas X', $p['kelas_sasaran'] ?? null, $p['guru_id'] ?? null, $p['guru_nama'] ?? null, $p['kkm'] ?? 75, $p['durasi_menit'] ?? 90, $p['is_locked'] ?? 0, $p['locked_at'] ?? null, $p['locked_by'] ?? null];
                        $stmtP1->execute($vals);
                        $stmtP2->execute($vals);
                        $cntP++;
                    }
                    $status['bs_paket'] = $cntP;
                }

                // 3. Butir Soal Bank Soal (bs_soal)
                if (!empty($data['bs_soal'])) {
                    $sqlBS = "INSERT INTO bs_soal (id, topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), paket_id = VALUES(paket_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan), opsi_a = VALUES(opsi_a), opsi_b = VALUES(opsi_b), opsi_c = VALUES(opsi_c), opsi_d = VALUES(opsi_d), opsi_e = VALUES(opsi_e)";
                    $stmtBS1 = $pdoCore->prepare($sqlBS);
                    $stmtBS2 = $pdoSiakad->prepare($sqlBS);

                    $cntBS = 0;
                    foreach ($data['bs_soal'] as $bs) {
                        $vals = [$bs['id'], $bs['topik_id'], $bs['paket_id'] ?? null, $bs['mapel_nama'], $bs['kelas_nama'] ?? null, $bs['nomor_urut'] ?? 1, $bs['tipe_soal'] ?? 'sec_pg', $bs['tingkat_kesulitan'] ?? 'Sedang', $bs['skor_maksimal'] ?? 2.00, $bs['pertanyaan'], $bs['opsi_a'] ?? null, $bs['opsi_b'] ?? null, $bs['opsi_c'] ?? null, $bs['opsi_d'] ?? null, $bs['opsi_e'] ?? null, $bs['kunci_jawaban'] ?? null, $bs['konten_tambahan'] ?? null];
                        $stmtBS1->execute($vals);
                        $stmtBS2->execute($vals);
                        $cntBS++;
                    }
                    $status['bs_soal'] = $cntBS;
                }

                // 4. Jadwal Grid (cbt_jadwal_grid)
                if (!empty($data['jadwal_grid'])) {
                    $stmtG = $pdoCore->prepare("INSERT INTO cbt_jadwal_grid (id, topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE mapel_nama = VALUES(mapel_nama), ujian_id = VALUES(ujian_id), tanggal = VALUES(tanggal), hari = VALUES(hari), jam_slot = VALUES(jam_slot), topik_id = VALUES(topik_id)");
                    $cntG = 0;
                    foreach ($data['jadwal_grid'] as $g) {
                        $stmtG->execute([$g['id'], $g['topik_id'] ?? null, $g['ujian_id'] ?? 0, $g['mapel_nama'] ?? '', $g['hari'], $g['jam_slot'], $g['kelas_id'] ?? null, $g['kelas_nama'], $g['pengawas'] ?? null, $g['ruang'] ?? null, $g['tanggal'] ?? null]);
                        $cntG++;
                    }
                    $status['jadwal'] = $cntG;
                }

                // 5. Ujian CBT & Butir Soal Ujian
                if (!empty($data['ujian'])) {
                    $stmtU = $pdoCore->prepare("INSERT INTO cbt_ujian (id, topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_ujian = VALUES(nama_ujian), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), status = VALUES(status), durasi_menit = VALUES(durasi_menit), kkm = VALUES(kkm)");
                    $cntU = 0;
                    foreach ($data['ujian'] as $u) {
                        $stmtU->execute([$u['id'], $u['topik_id'] ?? null, $u['kode_ujian'], $u['nama_ujian'], $u['mapel_nama'], $u['kelas_nama'], $u['durasi_menit'] ?? 60, $u['kkm'] ?? 75, $u['acak_soal'] ?? 1, $u['acak_opsi'] ?? 1, $u['token'] ?? '', $u['status'] ?? 'Aktif']);
                        $cntU++;
                    }
                    $status['ujian'] = $cntU;
                }

                if (!empty($data['soal'])) {
                    $stmtSoal = $pdoCore->prepare("INSERT INTO cbt_soal (id, ujian_id, nomor_urut, tipe_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan)");
                    $cntS = 0;
                    foreach ($data['soal'] as $so) {
                        $stmtSoal->execute([$so['id'], $so['ujian_id'], $so['nomor_urut'], $so['tipe_soal'] ?? 'sec_pg', $so['pertanyaan'], $so['opsi_a'] ?? null, $so['opsi_b'] ?? null, $so['opsi_c'] ?? null, $so['opsi_d'] ?? null, $so['opsi_e'] ?? null, $so['kunci_jawaban'] ?? null, $so['bobot'] ?? 1.00, $so['konten_tambahan'] ?? null]);
                        $cntS++;
                    }
                    $status['soal_cbt'] = $cntS;
                }

                // 6. Siswa & Users
                if (!empty($data['siswa'])) {
                    $stmtSsw1 = $pdoCore->prepare("INSERT INTO siswa (id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nis = VALUES(nis), nik = VALUES(nik), status = VALUES(status), kelas_id = VALUES(kelas_id)");
                    $stmtSsw2 = $pdoSiakad->prepare("INSERT INTO siswa (id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nis = VALUES(nis), nik = VALUES(nik), status = VALUES(status), kelas_id = VALUES(kelas_id)");
                    $cntSw = 0;
                    foreach ($data['siswa'] as $sw) {
                        $vals = [$sw['id'], $sw['user_id'] ?? null, $sw['nis'], $sw['nik'] ?? null, $sw['nama'] ?? ($sw['nama_lengkap'] ?? ''), $sw['status'] ?? 'Aktif', $sw['kelas_id'] ?? null, $sw['jenis_kelamin'] ?? 'L'];
                        $stmtSsw1->execute($vals);
                        $stmtSsw2->execute($vals);
                        $cntSw++;
                    }
                    $status['siswa'] = $cntSw;
                }

                if (!empty($data['users'])) {
                    $stmtU = $pdoCore->prepare("INSERT INTO users (id, username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), plain_password = VALUES(plain_password), role_id = VALUES(role_id), status = VALUES(status)");
                    $cntUsr = 0;
                    foreach ($data['users'] as $u) {
                        $stmtU->execute([$u['id'], $u['username'], $u['password_hash'], $u['plain_password'] ?? null, $u['role_id'], $u['status'] ?? 'active']);
                        $cntUsr++;
                    }
                    $status['users'] = $cntUsr;
                }

                $isDone = true;
            }
        }
    } catch (Exception $e) {
        $errorMsg = "Terjadi kegagalan: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi Kilat Server CBT Madrasah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-slate-800 border border-slate-700 rounded-3xl p-8 shadow-2xl">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-700">
            <div class="w-14 h-14 bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white">Sinkronisasi Kilat Server CBT</h1>
                <p class="text-xs text-slate-400">Penyelarasan Bersih ID Topik, Bank Soal, Jadwal & Siswa MAS MIFHDA</p>
            </div>
        </div>

        <?php if ($isDone): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 p-5 rounded-2xl mb-6">
                <div class="flex items-center gap-3 font-bold text-base mb-2">
                    <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
                    <span>ALHAMDULILLAH! SINKRONISASI BERHASIL 100%</span>
                </div>
                <p class="text-xs text-emerald-200 mb-4">Seluruh data lama/dummy berhasil dibersihkan dan digantikan dengan data resmi dari server pusat.</p>
                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-900/60 p-4 rounded-xl border border-emerald-500/30">
                    <div>• Topik Ujian: <b class="text-white"><?= $status['topik'] ?? 0 ?> Topik</b></div>
                    <div>• Paket Bank Soal: <b class="text-white"><?= $status['bs_paket'] ?? 0 ?> Paket</b></div>
                    <div>• Butir Bank Soal: <b class="text-white"><?= $status['bs_soal'] ?? 0 ?> Butir</b></div>
                    <div>• Sesi Jadwal Grid: <b class="text-white"><?= $status['jadwal'] ?? 0 ?> Sesi</b></div>
                    <div>• Paket Ujian CBT: <b class="text-white"><?= $status['ujian'] ?? 0 ?> Ujian</b></div>
                    <div>• Butir Soal CBT: <b class="text-white"><?= $status['soal_cbt'] ?? 0 ?> Butir</b></div>
                    <div>• Siswa Madrasah: <b class="text-white"><?= $status['siswa'] ?? 0 ?> Siswa</b></div>
                    <div>• Akun Login: <b class="text-white"><?= $status['users'] ?? 0 ?> Pengguna</b></div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <a href="bank_soal" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl text-center transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30">
                    <i class="fa-solid fa-layer-group"></i> 👉 Buka Bank Soal Madrasah Sekarang
                </a>
                <a href="topik" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold py-3 px-6 rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-calendar-check"></i> Cek Master Topik Ujian
                </a>
                <a href="semi-online" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold py-2.5 px-6 rounded-xl text-center transition text-xs">
                    Kembali ke Menu Semi-Online
                </a>
            </div>
        <?php else: ?>
            <?php if (!empty($errorMsg)): ?>
                <div class="bg-rose-500/20 border border-rose-500/40 text-rose-300 p-4 rounded-xl text-sm mb-6 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-lg mt-0.5 text-rose-400"></i>
                    <div>
                        <div class="font-bold">Gagal Sinkronisasi:</div>
                        <div class="text-xs mt-1 text-rose-200"><?= htmlspecialchars($errorMsg) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-xs text-slate-300 bg-slate-700/50 p-4 rounded-xl mb-6 leading-relaxed border border-slate-600">
                <b class="text-indigo-400 font-bold"><i class="fa-solid fa-info-circle mr-1"></i> Apa yang dilakukan alat ini?</b><br>
                1. Menghapus 5 topik dummy bawaan awal yang ID-nya salah / tidak cocok.<br>
                2. Mengimpor 4 Topik Resmi (SAS 89 Sesi, UM, STS, Simulasi).<br>
                3. Mengimpor 154 Paket Bank Soal dan 1.980 Butir Soal.<br>
                4. Mengimpor 125 Jadwal Grid, 90 Siswa SIAKAD, dan 207 Akun Login.
            </div>

            <form method="POST">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-300 mb-2">URL Server Online Pusat:</label>
                    <input type="text" name="server_url" value="https://apk.masmifhda.sch.id" required class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-bolt"></i> Mulai Sinkronisasi Kilat (1-Klik Bersih)
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
