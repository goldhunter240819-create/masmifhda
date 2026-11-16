<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = (int)$_SESSION['guru_id'];
$user_id = (int)($_SESSION['user_id'] ?? 0);

// Ambil data nama guru
$guru_nama = $_SESSION['guru_nama'] ?? '';
if (empty($guru_nama)) {
    try {
        $stmtG = $core_db->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmtG->execute([$guru_id]);
        $guru_nama = $stmtG->fetchColumn() ?: '';
    } catch (\Exception $e) {}
}

$page_title = 'Bank Soal Saya';
include 'header.php';

$db = $siakad_db ?: $core_db;

// 1. Ambil seluruh Topik Ujian
$stmtT = $db->query("SELECT * FROM bs_topik ORDER BY status = 'Aktif' DESC, id DESC");
$topikList = $stmtT ? $stmtT->fetchAll(PDO::FETCH_ASSOC) : [];

// 2. Ambil penugasan mengajar riil guru ini jika paket belum ada
$teachingMap = [];
try {
    $stmtM = $siakad_db->prepare("
        SELECT DISTINCT mp.nama_mapel, k.nama_kelas 
        FROM mengajar m
        JOIN mapel mp ON m.mapel_id = mp.id
        JOIN kelas k ON m.kelas_id = k.id
        WHERE m.guru_id = ?
        ORDER BY mp.nama_mapel ASC, k.nama_kelas ASC
    ");
    $stmtM->execute([$guru_id]);
    $teachingMap = $stmtM->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

// Siapkan data per topik
$topikData = [];
$totalMyPaket = 0;
$totalMySoal = 0;
$totalMyLocked = 0;

foreach ($topikList as $tp) {
    $tId = (int)$tp['id'];

    // Ambil paket yang diampu guru ini (berdasarkan guru_id ATAU kecocokan nama)
    $stmtP = $db->prepare("
        SELECT * FROM bs_paket 
        WHERE topik_id = ? 
          AND (guru_id = ? OR guru_nama = ? OR guru_nama LIKE ?)
        ORDER BY kelas_nama ASC, mapel_nama ASC
    ");
    $stmtP->execute([$tId, $guru_id, $guru_nama, '%' . $guru_nama . '%']);
    $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

    // Auto-create jika topik aktif dan paket belum terdaftar untuk guru ini
    if (empty($paketList) && !empty($teachingMap) && $tp['status'] === 'Aktif') {
        $stmtIns = $db->prepare("
            INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_id, guru_nama, kkm, durasi_menit, is_locked)
            VALUES (?, ?, ?, ?, ?, 75, 90, 0)
        ");
        foreach ($teachingMap as $tm) {
            $stmtIns->execute([$tId, $tm['nama_mapel'], $tm['nama_kelas'], $guru_id, $guru_nama]);
        }
        $stmtP->execute([$tId, $guru_id, $guru_nama, '%' . $guru_nama . '%']);
        $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hitung soal & breakdown untuk masing-masing paket
    foreach ($paketList as &$pk) {
        $pId = (int)$pk['id'];
        $stmtB = $db->prepare("SELECT tipe_soal, COUNT(*) as jml FROM bs_soal WHERE paket_id = ? GROUP BY tipe_soal");
        $stmtB->execute([$pId]);
        $bRows = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        $breakdown = [];
        $tot = 0;
        foreach ($bRows as $br) {
            $breakdown[$br['tipe_soal']] = (int)$br['jml'];
            $tot += (int)$br['jml'];
        }
        $pk['total_soal'] = $tot;
        $pk['breakdown'] = $breakdown;

        $totalMyPaket++;
        $totalMySoal += $tot;
        if (!empty($pk['is_locked'])) {
            $totalMyLocked++;
        }
    }
    unset($pk);

    $tp['my_paket'] = $paketList;
    $topikData[] = $tp;
}
?>

<style>
    .card-topik-bs {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 14px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.03);
    }
    .head-topik-bs {
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }
    .head-topik-bs:active {
        background: #f1f5f9;
    }
    .badge-topik-tag {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .badge-active-tag {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-closed-tag {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
    .badge-kelas-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .pill-jenis-mini {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .pill-pg { background: #e0f2fe; color: #0284c7; }
    .pill-pgk { background: #f3e8ff; color: #7e22ce; }
    .pill-essay { background: #ecfdf5; color: #047857; }
</style>

<!-- Header Mobile -->
<div class="header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="margin-left: 14px;">
            <p>Ruang Kerja Guru</p>
            <h1>Bank Soal Saya</h1>
        </div>
        <div class="avatar" style="color: #0284c7; background: white;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">

        <!-- Info Card Guru -->
        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px;">Guru Pengampu</div>
                <div style="font-size: 1rem; font-weight: 900; color: #0f172a; margin-top: 2px;"><?= htmlspecialchars($guru_nama ?: 'Guru MAS Miftahul Huda') ?></div>
                <div style="font-size: 0.75rem; color: #475569; margin-top: 3px;">
                    Kelola soal ujian dan kunci jawaban untuk kelas yang Anda ampu.
                </div>
            </div>
            <div style="text-align: right; flex-shrink: 0; padding-left: 10px;">
                <span style="font-size: 1.25rem; font-weight: 900; color: #0284c7; display: block; line-height: 1;"><?= $totalMyPaket ?></span>
                <span style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Paket Mapel</span>
            </div>
        </div>

        <?php if (empty($topikData)): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 14px; border: 1.5px dashed #cbd5e1; color: #64748b;">
                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; color: #94a3b8; margin-bottom: 10px;"></i>
                <h4 style="margin: 0 0 5px; color: #1e293b; font-size: 1rem; font-weight: 800;">Belum Ada Topik Ujian</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Topik ujian (SAS, STS, UM) belum dibuat oleh Admin/Kurikulum.</p>
            </div>
        <?php else: ?>
            <?php foreach ($topikData as $idx => $tp): 
                $isOpen = false;
                $myPkts = $tp['my_paket'] ?? [];
            ?>
                <div class="card-topik-bs">
                    <div class="head-topik-bs" onclick="toggleTopik('tpk_<?= $tp['id'] ?>')">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                                <i class="fa-solid fa-folder-closed" id="icon_folder_tpk_<?= $tp['id'] ?>"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                                    <span class="badge-topik-tag <?= $tp['status'] === 'Aktif' ? 'badge-active-tag' : 'badge-closed-tag' ?>">
                                        <?= htmlspecialchars($tp['jenis'] ?: 'SAS') ?> &bull; <?= htmlspecialchars($tp['status'] ?: 'Terjadwal') ?>
                                    </span>
                                </div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; line-height: 1.25;">
                                    <?= htmlspecialchars($tp['nama_topik']) ?>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 0.72rem; font-weight: 800; background: #f1f5f9; color: #334155; padding: 2px 7px; border-radius: 12px;">
                                <?= count($myPkts) ?> Mapel
                            </span>
                            <i class="fa-solid fa-chevron-down" id="arrow_tpk_<?= $tp['id'] ?>" style="color: #94a3b8; font-size: 0.8rem; transition: transform 0.25s; <?= $isOpen ? 'transform: rotate(180deg);' : '' ?>"></i>
                        </div>
                    </div>

                    <div id="body_tpk_<?= $tp['id'] ?>" style="display: <?= $isOpen ? 'block' : 'none' ?>; padding: 12px 14px; background: #ffffff;">
                        <?php if (empty($myPkts)): ?>
                            <div style="text-align: center; padding: 20px 10px; font-size: 0.8rem; color: #94a3b8; font-style: italic;">
                                Tidak ada mata pelajaran yang diampu untuk topik ini.
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php foreach ($myPkts as $pk): 
                                    $isLocked = !empty($pk['is_locked']);
                                    $kls = trim($pk['kelas_nama'] ?: 'X E.1');
                                    $totSoal = $pk['total_soal'] ?? 0;
                                    $bd = $pk['breakdown'] ?? [];

                                    // Warna Badge Kelas
                                    $klsBg = '#eff6ff'; $klsCol = '#1d4ed8'; $klsBorder = '#bfdbfe';
                                    if (strpos($kls, 'XI F') !== false) {
                                        $klsBg = '#ecfdf5'; $klsCol = '#047857'; $klsBorder = '#a7f3d0';
                                    } elseif (strpos($kls, 'XII F.1') !== false) {
                                        $klsBg = '#f5f3ff'; $klsCol = '#6d28d9'; $klsBorder = '#ddd6fe';
                                    } elseif (strpos($kls, 'XII F.2') !== false) {
                                        $klsBg = '#fffbeb'; $klsCol = '#b45309'; $klsBorder = '#fde68a';
                                    }
                                ?>
                                <div style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; background: #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                                    <!-- Header Paket: Mapel & Kelas -->
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                                        <div style="font-weight: 900; color: #0f172a; font-size: 0.95rem;">
                                            <?= htmlspecialchars($pk['mapel_nama']) ?>
                                        </div>
                                        <span class="badge-kelas-pill" style="background: <?= $klsBg ?>; color: <?= $klsCol ?>; border: 1px solid <?= $klsBorder ?>;">
                                            <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($kls) ?>
                                        </span>
                                    </div>

                                    <!-- Status Kunci & Jumlah Soal -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.78rem;">
                                        <div style="color: #64748b; font-weight: 700;">
                                            Total: <strong style="color: #0f172a; font-size: 0.9rem;"><?= $totSoal ?></strong> Soal
                                            <?php if ($totSoal > 0): ?>
                                                <span style="margin-left: 4px; display: inline-flex; gap: 3px;">
                                                    <?php if (!empty($bd['sec_pg'])): ?>
                                                        <span class="pill-jenis-mini pill-pg"><?= $bd['sec_pg'] ?> PG</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($bd['sec_pgk'])): ?>
                                                        <span class="pill-jenis-mini pill-pgk"><?= $bd['sec_pgk'] ?> PGK</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($bd['sec_essay'])): ?>
                                                        <span class="pill-jenis-mini pill-essay"><?= $bd['sec_essay'] ?> Esai</span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <?php if ($isLocked): ?>
                                                <span style="font-size: 0.68rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 12px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fa-solid fa-lock" style="font-size: 0.65rem;"></i> Terkunci
                                                </span>
                                            <?php else: ?>
                                                <span style="font-size: 0.68rem; font-weight: 800; background: #ecfdf5; color: #047857; padding: 3px 8px; border-radius: 12px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fa-solid fa-lock-open" style="font-size: 0.65rem;"></i> Terbuka
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Tombol Aksi -->
                                    <div style="display: flex; gap: 8px;">
                                        <?php if ($isLocked): ?>
                                            <a href="bank_soal_detail.php?id=<?= $pk['id'] ?>" style="flex: 1; text-align: center; background: #f8fafc; color: #475569; padding: 9px 0; border-radius: 8px; font-size: 0.82rem; font-weight: 800; text-decoration: none; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <i class="fa-solid fa-eye"></i> Lihat Soal (Terkunci)
                                            </a>
                                        <?php else: ?>
                                            <a href="bank_soal_detail.php?id=<?= $pk['id'] ?>" style="flex: 1; text-align: center; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 9px 0; border-radius: 8px; font-size: 0.82rem; font-weight: 800; text-decoration: none; border: none; box-shadow: 0 3px 8px rgba(14, 165, 233, 0.25); display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <i class="fa-solid fa-pen-to-square"></i> Kelola Butir Soal
                                            </a>
                                        <?php endif; ?>

                                        <!-- Shortcut Web Version -->
                                        <a href="/bank_soal/daftar_soal?paket_id=<?= $pk['id'] ?>" target="_blank" style="width: 38px; height: 38px; background: #f1f5f9; color: #475569; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.85rem; border: 1px solid #e2e8f0;" title="Buka di Versi Web Lengkap">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script>
function toggleTopik(id) {
    var body = document.getElementById('body_' + id);
    var arrow = document.getElementById('arrow_' + id);
    var iconFolder = document.getElementById('icon_folder_' + id);
    if (body.style.display === 'none' || body.style.display === '') {
        body.style.display = 'block';
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        if (iconFolder) {
            iconFolder.classList.remove('fa-folder-closed');
            iconFolder.classList.add('fa-folder-open');
        }
    } else {
        body.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
        if (iconFolder) {
            iconFolder.classList.remove('fa-folder-open');
            iconFolder.classList.add('fa-folder-closed');
        }
    }
}
</script>

<div style="height: 80px;"></div>

<?php include 'footer_guru.php'; ?>
