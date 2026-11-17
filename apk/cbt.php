<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = (int)$_SESSION['siswa_id'];

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
if (stripos($siswa['nama_kelas'], 'XII') !== false || stripos($siswa['nama_kelas'], '12') !== false) {
    $tingkat_kelas = 'Kelas XII';
} elseif (stripos($siswa['nama_kelas'], 'XI') !== false || stripos($siswa['nama_kelas'], '11') !== false) {
    $tingkat_kelas = 'Kelas XI';
}

// Token Ujian Aktif
$tokenAktif = null;
try {
    $stmtTok = $core_db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
    if ($stmtTok) {
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC);
    }
} catch (\Exception $e) {}

// Daftar Ujian CBT yang Aktif untuk Siswa
$ujianList = [];
try {
    $stmtUjian = $core_db->prepare("
        SELECT u.*,
               COALESCE((SELECT COUNT(*) FROM cbt_soal s WHERE s.ujian_id = u.id), 0) as total_soal,
               p.id as peserta_id,
               p.status_pengerjaan,
               p.nilai_akhir,
               p.waktu_mulai,
               p.waktu_selesai
        FROM cbt_ujian u
        LEFT JOIN cbt_peserta p ON p.ujian_id = u.id AND (p.siswa_id = :sid OR (p.nis = :nis AND :nis != ''))
        WHERE u.status = 'Aktif'
          AND (u.kelas_nama IS NULL OR u.kelas_nama = '' OR u.kelas_nama = 'Semua Kelas' OR u.kelas_nama = :k_nama OR u.kelas_nama LIKE :k_tingkat)
        ORDER BY u.id DESC
    ");
    $stmtUjian->execute([
        'sid' => $siswa_id,
        'nis' => (string)($siswa['nis'] ?? ''),
        'k_nama' => $siswa['nama_kelas'],
        'k_tingkat' => '%' . $tingkat_kelas . '%'
    ]);
    $ujianList = $stmtUjian->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

// Jika belum ada di cbt_ujian, cek juga jadwal dari cbt_jadwal_grid / bs_topik aktif
$topikAktif = null;
try {
    $stmtTop = $core_db->query("SELECT * FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
    if ($stmtTop) {
        $topikAktif = $stmtTop->fetch(PDO::FETCH_ASSOC);
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
            <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= htmlspecialchars($siswa['nama']) ?>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 2px; font-size: 0.72rem; color: #64748b;">
                <span><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($siswa['nama_kelas']) ?></span>
                <span>•</span>
                <span>NIS: <b><?= htmlspecialchars($siswa['nis'] ?: '-') ?></b></span>
                <?php if (!empty($siswa['nik'])): ?>
                <span>•</span>
                <span>NIK: <b><?= htmlspecialchars($siswa['nik']) ?></b></span>
                <?php endif; ?>
            </div>
        </div>
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
            $isMengerjakan = ($u['status_pengerjaan'] === 'Mengerjakan');
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
                    <a href="/cbt/kerjakan?ujian_id=<?= (int)$u['id'] ?>" class="btn-ujian btn-ujian-start" onclick="return confirm('Mulai mengerjakan ujian <?= htmlspecialchars(addslashes($u['nama_ujian'])) ?>? Waktu pengerjaan akan segera berjalan.');">
                        <i class="fa-solid fa-arrow-right"></i> Mulai Kerjakan Ujian Sekarang
                    </a>
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

</div>

<?php include 'footer.php'; ?>
