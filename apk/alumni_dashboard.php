<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil data siswa
$stmt = $core_db->prepare("SELECT s.*, ta.name AS tahun_lulus_name FROM siswa s LEFT JOIN tahun_ajaran ta ON s.tahun_lulus_id = ta.id WHERE s.id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa || $siswa['status'] !== 'Alumni') {
    // Bukan alumni, kirim ke dashboard biasa
    header("Location: index.php");
    exit;
}

// Tagihan yang masih belum lunas
$stmt_t = $core_db->prepare("SELECT * FROM keuangan_tagihan WHERE siswa_id = :id AND status != 'Lunas'");
$stmt_t->execute(['id' => $siswa_id]);
$tagihans_aktif = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

$total_tunggakan = 0;
foreach ($tagihans_aktif as $t) {
    $total_tunggakan += ($t['jumlah_tagihan'] - $t['jumlah_terbayar']);
}

// Alumni seangkatan (lulus tahun yang sama)
$seangkatan = [];
if (!empty($siswa['tahun_lulus_id'])) {
    $stmt_s = $core_db->prepare("SELECT id, nama, foto FROM siswa WHERE tahun_lulus_id = :tid AND status = 'Alumni' AND id != :me ORDER BY nama ASC LIMIT 50");
    $stmt_s->execute(['tid' => $siswa['tahun_lulus_id'], 'me' => $siswa_id]);
    $seangkatan = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
}

// Nama kelas terakhir
$kelas_terakhir = '-';
if (!empty($siswa['kelas_id'])) {
    try {
        $stmt_k = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = :kid");
        $stmt_k->execute(['kid' => $siswa['kelas_id']]);
        $k = $stmt_k->fetch();
        if ($k) $kelas_terakhir = 'KELAS ' . $k['nama_kelas'];
    } catch(PDOException $e){}
}

$bulan_indo = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$tgl_lulus = '-';
if (!empty($siswa['tahun_lulus_name'])) {
    $tgl_lulus = $siswa['tahun_lulus_name'];
}

$page_title = 'Dashboard Alumni';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alumni - MA Miftahul Huda</title>
    <link rel="icon" type="image/png" href="/public/uploads/logo/logo_1781854511.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #f59e0b;
            --gold-light: #fbbf24;
            --gold-dark: #d97706;
            --navy: #1e3a5f;
            --navy-dark: #0f2744;
            --green: #10b981;
            --red: #ef4444;
            --bg: #f0f4ff;
            --card: #fff;
            --text: #0f172a;
            --muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: linear-gradient(160deg, #1e3a5f 0%, #0f2744 40%, #1a1a2e 100%);
            min-height: 100vh;
            color: white;
            display: flex;
            justify-content: center;
        }
        .wrapper {
            width: 100%;
            max-width: 480px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 80px;
        }

        /* ── HERO HEADER ── */
        .hero {
            position: relative;
            padding: 2rem 1.5rem 3.5rem;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2744 100%);
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.2) 0%, transparent 70%);
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, transparent 70%);
        }
        .hero-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }
        .alumni-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 12px rgba(245,158,11,0.4);
        }
        .hero-avatar {
            width: 70px; height: 70px;
            border-radius: 50%;
            border: 3px solid rgba(245,158,11,0.6);
            box-shadow: 0 0 20px rgba(245,158,11,0.3);
            overflow: hidden;
            background: #1e3a5f;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: rgba(255,255,255,0.5);
            flex-shrink: 0;
        }
        .hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .hero-info { flex: 1; min-width: 0; }
        .hero-info h1 {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.3;
            color: white;
            margin-bottom: 4px;
        }
        .hero-info .nis {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            font-family: monospace;
        }
        .hero-stats {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .hero-stat {
            flex: 1;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        .hero-stat .val {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--gold-light);
        }
        .hero-stat .lbl {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
        }

        /* ── MAIN CONTENT ── */
        .main { padding: 0 1rem; margin-top: -1.5rem; position: relative; z-index: 10; }

        /* ── SECTION CARD ── */
        .section-card {
            background: white;
            border-radius: 18px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .section-head {
            padding: 1rem 1.2rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .section-title {
            font-size: 0.9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .section-subtitle {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 1px;
        }
        .section-body { padding: 1rem 1.2rem; }

        /* ── DOCUMENT ITEMS ── */
        .doc-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 8px;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .doc-item:hover { background: #f0f9ff; border-color: #bae6fd; transform: translateY(-1px); }
        .doc-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .doc-info { flex: 1; min-width: 0; }
        .doc-name { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
        .doc-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 2px; }
        .doc-action {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .doc-available { color: #0284c7; background: #e0f2fe; }
        .doc-unavailable { color: #94a3b8; background: #f1f5f9; cursor: default; }

        /* ── TAGIHAN ── */
        .tagihan-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.82rem;
        }
        .tagihan-row:last-child { border-bottom: none; }
        .tagihan-nama { font-weight: 600; color: #0f172a; }
        .tagihan-jumlah { font-weight: 800; color: #ef4444; }

        /* ── SEANGKATAN GRID ── */
        .alumni-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .alumni-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            text-align: center;
        }
        .alumni-photo {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: #e2e8f0;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            color: #94a3b8;
            font-size: 1.1rem;
            border: 2px solid #e2e8f0;
        }
        .alumni-photo img { width: 100%; height: 100%; object-fit: cover; }
        .alumni-nama {
            font-size: 0.65rem;
            font-weight: 600;
            color: #334155;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: rgba(15, 39, 68, 0.96);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            padding: 8px 0 12px;
            z-index: 100;
        }
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            text-decoration: none;
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 600;
            padding: 5px 0;
            transition: all 0.2s;
        }
        .nav-item i { font-size: 1.15rem; }
        .nav-item.active { color: var(--gold-light); }
        .nav-item.active i { filter: drop-shadow(0 0 6px rgba(245,158,11,0.6)); }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 1.5rem 0;
        }
        .empty-state i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px; display: block; }
        .empty-state p { font-size: 0.8rem; color: #94a3b8; }

        /* ── TOTAL TUNGGAKAN BANNER ── */
        .tunggakan-banner {
            background: linear-gradient(135deg, #fef2f2, #fff1f2);
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .tunggakan-banner i { font-size: 1.5rem; color: #ef4444; }
        .tunggakan-banner .val { font-size: 1.1rem; font-weight: 900; color: #dc2626; }
        .tunggakan-banner .lbl { font-size: 0.72rem; color: #991b1b; margin-top: 1px; }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(e, url, message = 'Keluar Aplikasi?') {
            e.preventDefault();
            Swal.fire({
                title: message,
                text: "Anda yakin ingin keluar dari aplikasi?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    container: 'my-swal-container'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
</head>
<body>
<div class="wrapper">

    <!-- HERO -->
    <div class="hero">
        <div class="hero-top">
            <div class="hero-avatar">
                <?php if (!empty($siswa['foto'])): ?>
                    <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto" onerror="this.outerHTML='<i class=\'fa-solid fa-user-graduate\'></i>'">
                <?php else: ?>
                    <i class="fa-solid fa-user-graduate"></i>
                <?php endif; ?>
            </div>
            <div class="hero-info">
                <div class="alumni-badge" style="margin-bottom: 6px;">
                    <i class="fa-solid fa-graduation-cap"></i> Alumni
                </div>
                <h1><?= htmlspecialchars($siswa['nama']) ?></h1>
                <div class="nis">NIS: <?= htmlspecialchars($siswa['nis']) ?></div>
            </div>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="val"><i class="fa-solid fa-graduation-cap" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($kelas_terakhir) ?></div>
                <div class="lbl">Kelas Terakhir</div>
            </div>
            <div class="hero-stat">
                <div class="val"><?= htmlspecialchars($tgl_lulus) ?></div>
                <div class="lbl">Tahun Lulus</div>
            </div>
            <div class="hero-stat">
                <div class="val"><?= count($seangkatan) + 1 ?> orang</div>
                <div class="lbl">Seangkatan</div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TAGIHAN (hanya jika ada tunggakan) -->
        <?php if ($total_tunggakan > 0): ?>
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon" style="background: #fef2f2; color: #ef4444;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="section-title">Tagihan Belum Lunas</div>
                    <div class="section-subtitle">Harap segera dilunasi</div>
                </div>
            </div>
            <div class="section-body">
                <div class="tunggakan-banner">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <div class="val">Rp <?= number_format($total_tunggakan, 0, ',', '.') ?></div>
                        <div class="lbl">Total tagihan yang belum terlunasi</div>
                    </div>
                </div>
                <?php foreach ($tagihans_aktif as $t): ?>
                <div class="tagihan-row">
                    <div class="tagihan-nama"><?= htmlspecialchars($t['nama_tagihan'] ?? 'Tagihan') ?></div>
                    <div class="tagihan-jumlah">Rp <?= number_format($t['jumlah_tagihan'] - $t['jumlah_terbayar'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- DOKUMEN KELULUSAN -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon" style="background: #fffbeb; color: #f59e0b;">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <div class="section-title">Dokumen Kelulusan</div>
                    <div class="section-subtitle">Ijazah & Surat Keterangan Lulus</div>
                </div>
            </div>
            <div class="section-body">
                <!-- Ijazah -->
                <?php if (!empty($siswa['scan_ijazah'])): ?>
                <a href="/public/uploads/alumni_docs/<?= htmlspecialchars($siswa['scan_ijazah']) ?>" target="_blank" class="doc-item">
                    <div class="doc-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fa-solid fa-scroll"></i>
                    </div>
                    <div class="doc-info">
                        <div class="doc-name">Ijazah</div>
                        <div class="doc-meta">Tahun <?= htmlspecialchars($tgl_lulus) ?> · Tersedia</div>
                    </div>
                    <span class="doc-action doc-available"><i class="fa-solid fa-download"></i> Unduh</span>
                </a>
                <?php else: ?>
                <div class="doc-item" style="cursor: default;">
                    <div class="doc-icon" style="background: #f8fafc; color: #cbd5e1;">
                        <i class="fa-solid fa-scroll"></i>
                    </div>
                    <div class="doc-info">
                        <div class="doc-name">Ijazah</div>
                        <div class="doc-meta">Belum tersedia – hubungi pihak sekolah</div>
                    </div>
                    <span class="doc-action doc-unavailable">Belum Ada</span>
                </div>
                <?php endif; ?>

                <!-- SKL -->
                <?php if (!empty($siswa['scan_skl'])): ?>
                <a href="/public/uploads/alumni_docs/<?= htmlspecialchars($siswa['scan_skl']) ?>" target="_blank" class="doc-item">
                    <div class="doc-icon" style="background: #f0fdf4; color: #10b981;">
                        <i class="fa-solid fa-file-certificate"></i>
                    </div>
                    <div class="doc-info">
                        <div class="doc-name">Surat Keterangan Lulus (SKL)</div>
                        <div class="doc-meta">Tahun <?= htmlspecialchars($tgl_lulus) ?> · Tersedia</div>
                    </div>
                    <span class="doc-action doc-available" style="background: #dcfce7; color: #16a34a;"><i class="fa-solid fa-download"></i> Unduh</span>
                </a>
                <?php else: ?>
                <div class="doc-item" style="cursor: default;">
                    <div class="doc-icon" style="background: #f8fafc; color: #cbd5e1;">
                        <i class="fa-solid fa-file-certificate"></i>
                    </div>
                    <div class="doc-info">
                        <div class="doc-name">Surat Keterangan Lulus (SKL)</div>
                        <div class="doc-meta">Belum tersedia – hubungi pihak sekolah</div>
                    </div>
                    <span class="doc-action doc-unavailable">Belum Ada</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ALUMNI SEANGKATAN -->
        <?php if (!empty($seangkatan)): ?>
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon" style="background: #f0f9ff; color: #0284c7;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="section-title">Alumni Seangkatan</div>
                    <div class="section-subtitle">Teman lulus tahun <?= htmlspecialchars($tgl_lulus) ?> (<?= count($seangkatan) ?> orang)</div>
                </div>
            </div>
            <div class="section-body">
                <div class="alumni-grid">
                    <?php foreach ($seangkatan as $al): ?>
                    <div class="alumni-item">
                        <div class="alumni-photo">
                            <?php if (!empty($al['foto'])): ?>
                                <img src="/public/uploads/siswa/<?= htmlspecialchars($al['foto']) ?>" alt="<?= htmlspecialchars($al['nama']) ?>" onerror="this.outerHTML='<i class=\'fa-solid fa-user\'></i>'">
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div class="alumni-nama"><?= htmlspecialchars($al['nama']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon" style="background: #f0f9ff; color: #0284c7;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="section-title">Alumni Seangkatan</div>
                    <div class="section-subtitle">Teman lulus tahun <?= htmlspecialchars($tgl_lulus) ?></div>
                </div>
            </div>
            <div class="section-body">
                <div class="empty-state">
                    <i class="fa-solid fa-user-slash"></i>
                    <p>Belum ada data alumni seangkatan.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- PROFIL SINGKAT -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-icon" style="background: #f5f3ff; color: #7c3aed;">
                    <i class="fa-solid fa-address-card"></i>
                </div>
                <div>
                    <div class="section-title">Data Pribadi</div>
                    <div class="section-subtitle">Informasi pokok alumni</div>
                </div>
            </div>
            <div class="section-body">
                <?php
                $bulan_indo_full = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $tgl_lahir_format = '-';
                if (!empty($siswa['tanggal_lahir'])) {
                    $time = strtotime($siswa['tanggal_lahir']);
                    $tgl_lahir_format = date('d', $time) . ' ' . $bulan_indo_full[(int)date('m', $time)] . ' ' . date('Y', $time);
                }
                ?>
                <div class="tagihan-row">
                    <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">NISN</span>
                    <span style="font-size: 0.82rem; font-weight: 700; font-family: monospace;"><?= htmlspecialchars($siswa['nisn'] ?: '-') ?></span>
                </div>
                <div class="tagihan-row">
                    <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Tempat, Tgl Lahir</span>
                    <span style="font-size: 0.78rem; font-weight: 700; text-align: right; max-width: 60%;"><?= htmlspecialchars(($siswa['tempat_lahir'] ?: '-') . ', ' . $tgl_lahir_format) ?></span>
                </div>
                <div class="tagihan-row">
                    <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Nama Ayah</span>
                    <span style="font-size: 0.82rem; font-weight: 700;"><?= htmlspecialchars($siswa['nama_ayah'] ?: '-') ?></span>
                </div>
                <div class="tagihan-row">
                    <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Nama Ibu</span>
                    <span style="font-size: 0.82rem; font-weight: 700;"><?= htmlspecialchars($siswa['nama_ibu'] ?: '-') ?></span>
                </div>
            </div>
        </div>

    </div><!-- /.main -->

    <!-- BOTTOM NAV -->
    <div class="bottom-nav">
        <a href="alumni_dashboard.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'alumni_dashboard.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>
        <a href="tagihan.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'tagihan.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Tagihan</span>
        </a>
        <a href="profil.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-address-card"></i>
            <span>Profil</span>
        </a>
        <a href="?logout=1" class="nav-item" onclick="confirmLogout(event, this.href, 'Keluar dari akun ini?')">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('apk_remember_siswa', '', time() - 3600, "/");
    header("Location: login.php");
    exit;
}
?>
</body>
</html>
