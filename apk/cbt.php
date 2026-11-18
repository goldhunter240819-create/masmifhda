<?php
require_once 'koneksi.php';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = (int)$_SESSION['siswa_id'];
$is_cbt_only = !empty($_SESSION['is_cbt_only']);

if ($is_cbt_only) {
    $stmt = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = :id AND status = 'Aktif' LIMIT 1");
    $stmt->execute(['id' => $siswa_id]);
    $cbtStudent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cbtStudent) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    $siswa = [
        'id' => $cbtStudent['id'],
        'nama' => $cbtStudent['nama_siswa'],
        'nis' => $cbtStudent['no_peserta'],
        'nik' => $cbtStudent['nis'] ?? '',
        'nama_kelas' => $cbtStudent['kelas_nama'],
        'tingkat' => $cbtStudent['tingkat'],
        'status' => 'Kelas Jauh',
        'foto' => ''
    ];

    $tingkat_kelas = $cbtStudent['tingkat'];
    if (stripos($tingkat_kelas, 'XII') !== false || stripos($tingkat_kelas, '12') !== false) {
        $tingkat_kelas = 'Kelas XII';
        $short_tingkat = 'XII';
    } elseif (stripos($tingkat_kelas, 'XI') !== false || stripos($tingkat_kelas, '11') !== false) {
        $tingkat_kelas = 'Kelas XI';
        $short_tingkat = 'XI';
    } else {
        $tingkat_kelas = 'Kelas X';
        $short_tingkat = 'X';
    }
} else {
    // Ambil data siswa
    $stmt = $core_db->prepare("SELECT s.*, COALESCE(k.nama_kelas, 'Umum') as nama_kelas 
                               FROM siswa s 
                               LEFT JOIN kelas k ON s.kelas_id = k.id 
                               WHERE s.id = :id LIMIT 1");
    $stmt->execute(['id' => $siswa_id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$siswa) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Tentukan tingkat kelas (misal: 'Kelas X', 'Kelas XI', 'Kelas XII')
    $tingkat_kelas = 'Kelas X';
    $short_tingkat = 'X';
    if (stripos($siswa['nama_kelas'], 'XII') !== false || stripos($siswa['nama_kelas'], '12') !== false) {
        $tingkat_kelas = 'Kelas XII';
        $short_tingkat = 'XII';
    } elseif (stripos($siswa['nama_kelas'], 'XI') !== false || stripos($siswa['nama_kelas'], '11') !== false) {
        $tingkat_kelas = 'Kelas XI';
        $short_tingkat = 'XI';
    }
}

// 1. Jalankan Auto-Sync Matriks Jadwal ke CBT Ujian
try {
    if (class_exists('\App\Controllers\CbtController')) {
        \App\Controllers\CbtController::syncJadwalWithUjian($core_db, $siakad_db);
    }
} catch (\Exception $e) {}

// 2. Token Ujian Aktif (Jamin selalu ada token aktif untuk kemudahan siswa)
$tokenAktif = null;
try {
    $stmtTok = $core_db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
    if ($stmtTok) {
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC);
    }
    if (!$tokenAktif) {
        // Auto-aktifkan token jika belum ada
        $tokCode = 'MAS' . rand(100, 999);
        $core_db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, 240, 'Aktif', NOW())")->execute([$tokCode]);
        $tokenAktif = ['token' => $tokCode, 'status' => 'Aktif'];
    }
} catch (\Exception $e) {}

// 3. Ambil Info Topik Aktif
$topikAktif = null;
try {
    $stmtTop = $siakad_db->query("SELECT * FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
    if ($stmtTop) {
        $topikAktif = $stmtTop->fetch(PDO::FETCH_ASSOC);
    }
    if (!$topikAktif) {
        $stmtTopC = $core_db->query("SELECT * FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
        if ($stmtTopC) {
            $topikAktif = $stmtTopC->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (\Exception $e) {}

// 4. Daftar Ujian CBT yang Aktif untuk Siswa
$ujianList = [];
try {
    $allowedKelas = ['Semua Kelas', '', $siswa['nama_kelas'], $tingkat_kelas, $short_tingkat];
    if ($short_tingkat === 'XII') {
        $allowedKelas[] = 'Kelas 12';
        $allowedKelas[] = '12';
    } elseif ($short_tingkat === 'XI') {
        $allowedKelas[] = 'Kelas 11';
        $allowedKelas[] = '11';
    } else {
        $allowedKelas[] = 'Kelas 10';
        $allowedKelas[] = '10';
    }

    // Tambahkan seluruh rombel kelas di madrasah yang setingkat agar siswa kelas jauh otomatis dapat mengakses soal ujian paralel
    try {
        $numTingkat = ($short_tingkat === 'XII') ? 12 : (($short_tingkat === 'XI') ? 11 : 10);
        $stmtK = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE tingkat = ?");
        $stmtK->execute([$numTingkat]);
        $rombelNames = $stmtK->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($rombelNames)) {
            $allowedKelas = array_merge($allowedKelas, $rombelNames);
        }
    } catch (\Exception $e) {}

    $allowedKelas = array_values(array_unique(array_filter($allowedKelas)));
    $inPlaceholders = implode(',', array_fill(0, count($allowedKelas), '?'));

    $sqlUjian = "
        SELECT u.*,
               COALESCE((SELECT COUNT(*) FROM cbt_soal s WHERE s.ujian_id = u.id), 0) as total_soal,
               p.id as peserta_id,
               p.status_pengerjaan,
               p.nilai_akhir,
               p.waktu_mulai,
               p.waktu_selesai
        FROM cbt_ujian u
        LEFT JOIN cbt_peserta p ON p.ujian_id = u.id AND (p.siswa_id = ? OR (p.nis = ? AND ? != ''))
        WHERE u.status = 'Aktif'
          AND (u.kelas_nama IS NULL OR u.kelas_nama = '' OR u.kelas_nama IN ($inPlaceholders))
        ORDER BY u.id DESC
    ";
    $params = array_merge([$siswa_id, (string)($siswa['nis'] ?? ''), (string)($siswa['nis'] ?? '')], $allowedKelas);
    $stmtUjian = $core_db->prepare($sqlUjian);
    $stmtUjian->execute($params);
    $ujianList = $stmtUjian->fetchAll(PDO::FETCH_ASSOC);

    // Auto-heal: Jika belum ada di cbt_ujian tapi ada di cbt_jadwal_grid, buatkan langsung!
    if (empty($ujianList)) {
        $stmtJadwal = $core_db->prepare("
            SELECT * FROM cbt_jadwal_grid 
            WHERE mapel_nama IS NOT NULL AND mapel_nama != '' 
              AND (kelas_nama IS NULL OR kelas_nama = '' OR kelas_nama IN ($inPlaceholders))
        ");
        $stmtJadwal->execute($allowedKelas);
        $jadwalRows = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($jadwalRows) && class_exists('\App\Controllers\CbtController')) {
            \App\Controllers\CbtController::syncJadwalWithUjian($core_db, $siakad_db);
            // Query ulang
            $stmtUjian = $core_db->prepare($sqlUjian);
            $stmtUjian->execute($params);
            $ujianList = $stmtUjian->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (\Exception $e) {}

// Riwayat Ujian yang Sudah Selesai
$riwayatUjian = [];
try {
    $stmtRiw = $core_db->prepare("
        SELECT p.*, u.nama_ujian, u.mapel_nama, u.kkm, u.durasi_menit
        FROM cbt_peserta p
        JOIN cbt_ujian u ON p.ujian_id = u.id
        WHERE (p.siswa_id = :sid OR (p.nis = :nis AND :nis != ''))
          AND p.status_pengerjaan = 'Selesai'
        ORDER BY p.waktu_selesai DESC
    ");
    $stmtRiw->execute([
        'sid' => $siswa_id,
        'nis' => (string)($siswa['nis'] ?? '')
    ]);
    $riwayatUjian = $stmtRiw->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

$page_title = 'CBT Ujian Online';
include 'header.php';
?>

<style>
    .cbt-banner {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #4338ca 100%);
        border-radius: 20px;
        padding: 22px 20px;
        color: white;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.35);
    }
    .cbt-banner::after {
        content: '\f108';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -15px;
        bottom: -25px;
        font-size: 130px;
        opacity: 0.12;
        pointer-events: none;
    }
    .cbt-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.72rem;
        font-weight: 700;
        backdrop-filter: blur(4px);
        margin-bottom: 8px;
    }
    .cbt-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cbt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    }
    .token-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 6px 15px rgba(15, 23, 42, 0.15);
    }
    .token-display {
        font-family: 'JetBrains Mono', monospace, monospace;
        font-size: 1.35rem;
        font-weight: 900;
        letter-spacing: 3px;
        color: #38bdf8;
        background: rgba(255, 255, 255, 0.08);
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px dashed rgba(56, 189, 248, 0.4);
    }
    .btn-ujian {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s;
        border: none;
    }
    .btn-ujian-start {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .btn-ujian-start:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
    .btn-ujian-continue {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    .btn-ujian-done {
        background: #f1f5f9;
        color: #059669;
        border: 1px solid #a7f3d0;
        cursor: default;
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
    <div class="header-top">
        <div class="greeting">
            <p style="margin-bottom: 2px;">Evaluasi Akademik Online</p>
            <h1 style="margin: 0; font-size: 1.3rem;">CBT Ujian Siswa</h1>
        </div>
        <div class="avatar" style="color: #2563eb; background: white;">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 85px;">

    <!-- Card Identitas Siswa -->
    <div style="background: #ffffff; border-radius: 16px; padding: 14px 16px; margin-top: -2.2rem; margin-bottom: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; position: relative; z-index: 10;">
        <div style="width: 48px; height: 48px; border-radius: 12px; overflow: hidden; background: #e0e7ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #4338ca; font-size: 1.3rem;">
            <?php if (!empty($siswa['foto'])): ?>
                <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user-graduate\'></i>';">
            <?php else: ?>
                <i class="fa-solid fa-user-graduate"></i>
            <?php endif; ?>
        </div>
        <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 6px;">
                <span><?= htmlspecialchars($siswa['nama']) ?></span>
                <?php if ($is_cbt_only): ?>
                    <span style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.65rem; padding: 2px 7px; border-radius: 6px; font-weight: 800;">Kelas Jauh</span>
                <?php endif; ?>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 2px; font-size: 0.72rem; color: #64748b;">
                <span><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($siswa['nama_kelas']) ?></span>
                <span>•</span>
                <span>No. Peserta: <b><?= htmlspecialchars($siswa['nis'] ?: '-') ?></b></span>
                <?php if (!empty($siswa['nik'])): ?>
                <span>•</span>
                <span>NIK: <b><?= htmlspecialchars($siswa['nik']) ?></b></span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($is_cbt_only): ?>
            <a href="logout.php" onclick="return confirm('Keluar dari ruang ujian CBT?')" style="background: #fee2e2; color: #dc2626; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0;" title="Keluar Akun">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        <?php endif; ?>
    </div>

    <!-- Banner CBT Info -->
    <div class="cbt-banner">
        <div class="cbt-badge-live">
            <i class="fa-solid fa-circle" style="color: #4ade80; font-size: 0.55rem; animation: pulse 1.5s infinite;"></i> CBT Engine v2.5 Online
        </div>
        <h2 style="margin: 0 0 6px 0; font-size: 1.15rem; font-weight: 800; line-height: 1.3;">
            <?= htmlspecialchars($topikAktif['nama_topik'] ?? 'Ujian Berbasis Komputer Madrasah') ?>
        </h2>
        <p style="margin: 0; font-size: 0.8rem; opacity: 0.9; line-height: 1.4;">
            Pastikan Anda memiliki jaringan internet yang stabil sebelum menekan tombol Mulai Ujian.
        </p>
    </div>

    <!-- Info Token Ujian -->
    <div class="token-card">
        <div>
            <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Token Ujian Hari Ini</div>
            <div style="font-size: 0.82rem; font-weight: 600; margin-top: 2px;">
                <?= $tokenAktif ? 'Status: <span style="color: #4ade80;">Aktif</span>' : 'Status: <span style="color: #94a3b8;">Menunggu Rilis</span>' ?>
            </div>
        </div>
        <div>
            <div class="token-display">
                <?= htmlspecialchars($tokenAktif['token'] ?? '------') ?>
            </div>
        </div>
    </div>

    <!-- Section Daftar Ujian Aktif -->
    <h3 class="section-title" style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
        <span><i class="fa-solid fa-clipboard-list" style="color: #2563eb; margin-right: 6px;"></i> Paket Ujian Tersedia</span>
        <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;"><?= count($ujianList) ?> Paket</span>
    </h3>

    <?php if (empty($ujianList)): ?>
        <div style="background: white; border-radius: 16px; padding: 25px 20px; text-align: center; border: 1px dashed #cbd5e1; margin-bottom: 20px;">
            <div style="width: 55px; height: 55px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px;">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <h4 style="margin: 0 0 4px 0; font-size: 0.95rem; color: #1e293b;">Belum Ada Ujian Aktif</h4>
            <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4;">
                Saat ini belum ada paket ujian yang dirilis untuk kelas <b><?= htmlspecialchars($siswa['nama_kelas']) ?></b>. Jadwal ujian berikutnya akan diumumkan oleh guru atau panitia CBT.
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($ujianList as $u): ?>
            <?php
            $isSelesai = ($u['status_pengerjaan'] === 'Selesai');
            $isMengerjakan = in_array($u['status_pengerjaan'], ['Mengerjakan', 'Sedang Mengerjakan']);
            $durasi = (int)($u['durasi_menit'] ?? 90);
            $totalSoal = (int)($u['total_soal'] ?? 0);
            $kkm = (int)($u['kkm'] ?? 75);
            ?>
            <div class="cbt-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-book-bookmark"></i> <?= htmlspecialchars($u['kode_ujian']) ?>
                        </div>
                        <h4 style="margin: 3px 0 0; font-size: 1rem; color: #0f172a; font-weight: 800;">
                            <?= htmlspecialchars($u['nama_ujian']) ?>
                        </h4>
                    </div>
                    <?php if ($isSelesai): ?>
                        <span style="background: #ecfdf5; color: #059669; font-size: 0.7rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; border: 1px solid #a7f3d0;">
                            ✓ Selesai
                        </span>
                    <?php elseif ($isMengerjakan): ?>
                        <span style="background: #fffbeb; color: #d97706; font-size: 0.7rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; border: 1px solid #fde68a;">
                            ⏳ Mengerjakan
                        </span>
                    <?php else: ?>
                        <span style="background: #eff6ff; color: #2563eb; font-size: 0.7rem; font-weight: 800; padding: 4px 8px; border-radius: 20px; border: 1px solid #bfdbfe;">
                            Siap
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Info Meta Ujian -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; background: #f8fafc; border-radius: 10px; padding: 10px; margin-bottom: 14px; text-align: center; border: 1px solid #f1f5f9;">
                    <div>
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 600;">Mata Pelajaran</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($u['mapel_nama']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 600;">Waktu</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #1e293b;"><?= $durasi ?> Menit</div>
                    </div>
                    <div>
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 600;">Total Soal</div>
                        <div style="font-size: 0.8rem; font-weight: 800; color: #1e293b;"><?= $totalSoal > 0 ? $totalSoal . ' Butir' : 'Tersusun' ?></div>
                    </div>
                </div>

                <!-- Action Button -->
                <?php if ($isSelesai): ?>
                    <div style="display: flex; gap: 8px;">
                        <div class="btn-ujian btn-ujian-done" style="flex: 1;">
                            <i class="fa-solid fa-check-circle"></i> Skor Anda: <b><?= number_format((float)$u['nilai_akhir'], 1) ?></b>
                        </div>
                        <a href="/cbt/kerjakan/selesai?peserta_id=<?= (int)$u['peserta_id'] ?>&ujian_id=<?= (int)$u['id'] ?>" class="btn-ujian btn-ujian-start" style="width: auto; padding: 10px 14px; font-size: 0.8rem;">
                            <i class="fa-solid fa-file-lines"></i> Detail
                        </a>
                    </div>
                <?php elseif ($isMengerjakan): ?>
                    <a href="/cbt/kerjakan?ujian_id=<?= (int)$u['id'] ?>" class="btn-ujian btn-ujian-continue">
                        <i class="fa-solid fa-play"></i> Lanjutkan Pengerjaan Ujian
                    </a>
                <?php else: ?>
                    <button type="button" class="btn-ujian btn-ujian-start" style="width: 100%; border: none; cursor: pointer;" onclick="konfirmasiMulaiUjian(<?= (int)$u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nama_ujian'])) ?>', '<?= htmlspecialchars(addslashes($u['mapel_nama'])) ?>', <?= $durasi ?>, <?= $totalSoal ?>)">
                        <i class="fa-solid fa-arrow-right"></i> Mulai Kerjakan Ujian Sekarang
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Riwayat Ujian Siswa -->
    <?php if (!empty($riwayatUjian)): ?>
        <h3 class="section-title" style="margin-top: 20px; margin-bottom: 12px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #10b981; margin-right: 6px;"></i> Riwayat Hasil Ujian CBT
        </h3>
        <div style="margin-bottom: 20px;">
            <?php foreach ($riwayatUjian as $r): ?>
                <?php
                $isTuntas = ((float)$r['nilai_akhir'] >= (float)($r['kkm'] ?? 75));
                ?>
                <div style="background: white; border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; border-left: 4px solid <?= $isTuntas ? '#10b981' : '#ef4444' ?>; box-shadow: 0 2px 6px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #1e293b;"><?= htmlspecialchars($r['nama_ujian']) ?></div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px;">
                            <span><?= htmlspecialchars($r['mapel_nama']) ?></span> • 
                            <span><?= date('d M Y, H:i', strtotime($r['waktu_selesai'])) ?></span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.1rem; font-weight: 900; color: <?= $isTuntas ? '#10b981' : '#ef4444' ?>;">
                            <?= number_format((float)$r['nilai_akhir'], 1) ?>
                        </div>
                        <span style="font-size: 0.65rem; font-weight: 700; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                            <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Panduan Ujian -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px 16px; margin-top: 15px;">
        <h4 style="margin: 0 0 6px 0; font-size: 0.85rem; color: #1e293b; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-shield-halved" style="color: #3b82f6;"></i> Tata Tertib Ujian CBT
        </h4>
        <ul style="margin: 0; padding-left: 18px; font-size: 0.75rem; color: #64748b; line-height: 1.6;">
            <li>Gunakan koneksi internet yang stabil selama ujian berlangsung.</li>
            <li>Dilarang keluar aplikasi atau berpindah tab saat waktu ujian berjalan.</li>
            <li>Jawaban Anda tersimpan otomatis setiap kali memilih opsi.</li>
            <li>Jika ada kendala soal atau token, segera hubungi Pengawas Ruang / Proktor.</li>
        </ul>
    </div>

    <!-- Modal Konfirmasi Mulai Ujian Modern -->
    <div id="modalStartExam" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; justify-content: center; align-items: center; padding: 18px;">
        <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 390px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
            <!-- Modal Header -->
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 22px 20px 18px; text-align: center; color: #ffffff;">
                <div style="width: 56px; height: 56px; margin: 0 auto 12px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; border: 2px solid rgba(255, 255, 255, 0.3);">
                    🚀
                </div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.3px;">Mulai Pengerjaan Ujian?</h3>
                <p style="margin: 4px 0 0; font-size: 0.78rem; color: #bfdbfe;">Waktu pengerjaan akan segera dihitung mundur</p>
            </div>

            <!-- Modal Body -->
            <div style="padding: 18px 20px 14px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;">
                    <div id="modalExamTitle" style="font-size: 0.92rem; font-weight: 800; color: #0f172a; line-height: 1.4; margin-bottom: 6px;">-</div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; font-size: 0.75rem; color: #475569; font-weight: 600;">
                        <span id="modalExamMapel" style="background: #eff6ff; color: #2563eb; padding: 3px 8px; border-radius: 6px; border: 1px solid #dbeafe;">-</span>
                        <span id="modalExamDurasi" style="background: #f0fdf4; color: #16a34a; padding: 3px 8px; border-radius: 6px; border: 1px solid #dcfce7;">-</span>
                        <span id="modalExamSoal" style="background: #faf5ff; color: #7c3aed; padding: 3px 8px; border-radius: 6px; border: 1px solid #f3e8ff;">-</span>
                    </div>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 12px; font-size: 0.74rem; color: #92400e; line-height: 1.5; display: flex; gap: 8px; align-items: flex-start;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.95rem; margin-top: 2px; flex-shrink: 0; color: #d97706;"></i>
                    <div>
                        <b>Perhatian Proktor:</b> Jangan menutup aplikasi, berpindah tab, atau keluar dari ruang ujian. Pelanggaran sistem dapat mengunci lembar jawaban Anda.
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div style="padding: 0 20px 20px; display: flex; gap: 10px;">
                <button type="button" onclick="tutupModalMulai()" style="flex: 1; padding: 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">
                    Batal
                </button>
                <a id="modalStartLink" href="#" style="flex: 1.4; padding: 12px; border-radius: 10px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); transition: all 0.2s;">
                    <span>Mulai Sekarang</span> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
    @keyframes popInModal {
        0% { transform: scale(0.92); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    </style>

    <script>
    function konfirmasiMulaiUjian(ujianId, namaUjian, mapelNama, durasi, totalSoal) {
        document.getElementById('modalExamTitle').textContent = namaUjian;
        document.getElementById('modalExamMapel').textContent = mapelNama || 'CBT Online';
        document.getElementById('modalExamDurasi').textContent = durasi + ' Menit';
        document.getElementById('modalExamSoal').textContent = (totalSoal > 0 ? totalSoal + ' Soal' : '50 Soal');
        document.getElementById('modalStartLink').href = '/cbt/kerjakan?ujian_id=' + ujianId;
        
        const modal = document.getElementById('modalStartExam');
        modal.style.display = 'flex';
    }

    function tutupModalMulai() {
        const modal = document.getElementById('modalStartExam');
        modal.style.display = 'none';
    }
    </script>
</div>

<?php include 'footer.php'; ?>
