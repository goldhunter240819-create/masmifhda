<?php
require_once 'koneksi.php';

// Cek Auth Login Guru / Admin
if (!isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_SESSION['nama'] ?? ($_SESSION['guru_nama'] ?? 'Pengawas Kelas Jauh');
$nip_petugas = $_SESSION['guru_nip'] ?? ($_SESSION['nip'] ?? '-');

// Handle Action AJAX: Buka Kunci Peserta Kelas Jauh
if (isset($_POST['action']) && $_POST['action'] === 'unlock_peserta') {
    header('Content-Type: application/json');
    $peserta_id = (int)($_POST['peserta_id'] ?? 0);
    if ($peserta_id > 0) {
        try {
            $stmtUn = $core_db->prepare("UPDATE cbt_peserta SET status_pengerjaan = 'Mengerjakan', status_kunci = 'Unlocked', pelanggaran_count = 0, is_locked = 0 WHERE id = ?");
            $stmtUn->execute([$peserta_id]);
            echo json_encode(['success' => true, 'message' => 'Status ujian siswa berhasil dibuka kuncinya! Siswa dapat login kembali.']);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'ID peserta tidak valid']);
    exit;
}

// 1. Ambil Data Siswa Kelas Jauh & Rombel
$totalSiswaKJ = 0;
$rombelKJ = [];
try {
    $stmtTot = $core_db->query("SELECT COUNT(*) FROM cbt_peserta_khusus");
    $totalSiswaKJ = (int)$stmtTot->fetchColumn();

    $stmtRomb = $core_db->query("SELECT kelas_nama, COUNT(*) as jml FROM cbt_peserta_khusus GROUP BY kelas_nama ORDER BY kelas_nama ASC");
    $rombelKJ = $stmtRomb->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

// 2. Ambil Rekap Absensi Siswa Kelas Jauh Hari Ini
$todayDate = date('Y-m-d');
$hadirHariIniKJ = 0;
$listAbsenKJ = [];
try {
    $stmtAbsKJ = $core_db->prepare("
        SELECT a.*, k.nama_siswa as nama_lengkap, k.no_peserta 
        FROM cbt_absensi a
        LEFT JOIN cbt_peserta_khusus k ON (a.nis = k.no_peserta OR a.nis = k.username)
        WHERE (a.tipe_peserta = 'khusus' OR a.kelas_nama LIKE '%Jauh%' OR a.ruang_nama LIKE '%Jauh%')
          AND a.tanggal = ?
        ORDER BY a.waktu_scan DESC, a.id DESC
    ");
    $stmtAbsKJ->execute([$todayDate]);
    $listAbsenKJ = $stmtAbsKJ->fetchAll(PDO::FETCH_ASSOC);
    $hadirHariIniKJ = count($listAbsenKJ);
} catch (\Exception $e) {}

// 3. Ambil Data Topik Ujian (Hanya yang berstatus 'Aktif', deduplikasi nama kembar)
$topikList = [];
try {
    $rawTopik = $core_db->query("SELECT id, nama_topik FROM bs_topik WHERE status = 'Aktif' ORDER BY (id = 3) DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rawTopik)) {
        $rawTopik = $core_db->query("SELECT id, nama_topik FROM bs_topik WHERE status != 'Arsip' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    $seenNames = [];
    foreach ($rawTopik as $rt) {
        $normName = trim($rt['nama_topik']);
        if (!isset($seenNames[$normName])) {
            $seenNames[$normName] = true;
            $topikList[] = $rt;
        }
    }
} catch (\Exception $e) {}

// Ambil Filter Parameter: Topik -> Mapel -> Kelas -> Urutan
$selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
if ($selectedTopikId == 5) {
    $selectedTopikId = 3;
}
$selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
$selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
$selectedSort = isset($_GET['sort']) ? trim($_GET['sort']) : 'nilai_desc';
$selectedLimit = isset($_GET['limit']) ? ($_GET['limit'] === 'all' ? 0 : (int)$_GET['limit']) : 30;
if ($selectedLimit < 0) $selectedLimit = 30;

// 4. Ambil Daftar Mata Pelajaran berdasarkan Topik (atau semua jika topik belum dipilih)
$mapelList = [];
try {
    if ($selectedTopikId > 0) {
        $tpName = '';
        foreach ($topikList as $tp) {
            if ((int)$tp['id'] === $selectedTopikId) {
                $tpName = trim($tp['nama_topik']);
                break;
            }
        }
        $stmtM = $core_db->prepare("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE (topik_id = ? OR (? != '' AND nama_ujian LIKE ?)) AND mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC");
        $stmtM->execute([$selectedTopikId, $tpName, "%{$tpName}%"]);
        $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
    }
    if (empty($mapelList)) {
        $mapelList = $core_db->query("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (\Exception $e) {}

// 5. Ambil Daftar Kelas Jauh
$kelasListKJ = [];
try {
    $kelasListKJ = $core_db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != '' ORDER BY kelas_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($kelasListKJ)) {
        $kelasListKJ = ['X - Kelas Jauh', 'XI - Kelas Jauh', 'XII - Kelas Jauh'];
    }
} catch (\Exception $e) {}

// Hitung total paket soal untuk metrik card
$totalPaketSoal = 0;
try {
    $stmtTotUjn = $core_db->query("SELECT COUNT(*) FROM cbt_ujian WHERE status = 'Aktif' OR id IN (SELECT DISTINCT ujian_id FROM cbt_peserta)");
    $totalPaketSoal = (int)$stmtTotUjn->fetchColumn();
} catch (\Exception $e) {}

// Hitung total peserta KJ yang sudah selesai ujian
$totalSelesaiKJ = 0;
try {
    $stmtSel = $core_db->query("
        SELECT COUNT(*) 
        FROM cbt_peserta p 
        INNER JOIN cbt_peserta_khusus k ON (p.nis = k.no_peserta OR p.nis = k.username OR p.nama_siswa = k.nama_siswa)
        WHERE p.status_pengerjaan = 'Selesai'
    ");
    $totalSelesaiKJ = (int)$stmtSel->fetchColumn();
} catch (\Exception $e) {}

// 6. Ambil Hasil Ujian Siswa Kelas Jauh dengan Filter Topik, Mapel, Kelas
$hasilListKJ = [];
$statRata2 = 0;
$statTertinggi = 0;
$statTuntasCount = 0;

try {
    $sql = "SELECT p.*, k.nama_siswa as nama_lengkap, k.no_peserta, k.kelas_nama as rombel_kj, 
                   u.nama_ujian, u.mapel_nama, u.kkm, u.durasi_menit, u.topik_id,
                   (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
            FROM cbt_peserta p
            INNER JOIN cbt_peserta_khusus k ON (p.nis = k.no_peserta OR p.nis = k.username OR p.nama_siswa = k.nama_siswa)
            LEFT JOIN cbt_ujian u ON u.id = p.ujian_id
            WHERE 1=1";
    $params = [];

    if ($selectedTopikId > 0) {
        $tpName = '';
        foreach ($topikList as $tp) {
            if ((int)$tp['id'] === $selectedTopikId) {
                $tpName = trim($tp['nama_topik']);
                break;
            }
        }
        if (!empty($tpName)) {
            $sql .= " AND (u.topik_id = :topik_id OR u.nama_ujian LIKE :topik_like)";
            $params[':topik_id'] = $selectedTopikId;
            $params[':topik_like'] = "%{$tpName}%";
        } else {
            $sql .= " AND (u.topik_id = :topik_id)";
            $params[':topik_id'] = $selectedTopikId;
        }
    }

    if (!empty($selectedMapel)) {
        $sql .= " AND (u.mapel_nama = :mapel OR u.mapel_nama LIKE :mapel_like OR u.nama_ujian LIKE :mapel_like2)";
        $params[':mapel'] = $selectedMapel;
        $params[':mapel_like'] = "%{$selectedMapel}%";
        $params[':mapel_like2'] = "%{$selectedMapel}%";
    }

    if (!empty($selectedKelas)) {
        $sql .= " AND (k.kelas_nama = :kelas OR p.kelas_nama = :kelas OR k.kelas_nama LIKE :kelas_like OR p.kelas_nama LIKE :kelas_like)";
        $params[':kelas'] = $selectedKelas;
        $params[':kelas_like'] = "%{$selectedKelas}%";
    }

    // Custom Sort Siswa (Nilai Tertinggi, Terendah, Alphabet Nama A-Z, Z-A)
    switch ($selectedSort) {
        case 'nilai_asc':
            $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir ASC, p.nama_siswa ASC";
            break;
        case 'nama_asc':
            $sql .= " ORDER BY p.nama_siswa ASC, p.nilai_akhir DESC";
            break;
        case 'nama_desc':
            $sql .= " ORDER BY p.nama_siswa DESC, p.nilai_akhir DESC";
            break;
        case 'nilai_desc':
        default:
            $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
            break;
    }

    $sql .= " LIMIT 200";

    $stmtHsl = $core_db->prepare($sql);
    $stmtHsl->execute($params);
    $hasilListKJ = $stmtHsl->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($hasilListKJ)) {
        $totalNilai = 0;
        foreach ($hasilListKJ as $h) {
            $nilai = (float)($h['nilai_akhir'] ?? 0);
            $kkm = (float)($h['kkm'] ?? 75);
            $totalNilai += $nilai;
            if ($nilai > $statTertinggi) $statTertinggi = $nilai;
            if ($nilai >= $kkm) $statTuntasCount++;
        }
        $statRata2 = round($totalNilai / count($hasilListKJ), 1);
    }
} catch (\Exception $e) {}

$activeTab = isset($_GET['tab']) ? trim($_GET['tab']) : 'beranda';
if (!in_array($activeTab, ['beranda', 'scan', 'nilai'])) {
    $activeTab = 'beranda';
}

if (!function_exists('renderHasilCardKJ')) {
    function renderHasilCardKJ($h, $idx) {
        $nilai = (float)($h['nilai_akhir'] ?? 0);
        $kkmNilai = (float)($h['kkm'] ?? 75);
        $isTuntas = ($nilai >= $kkmNilai);
        $isLocked = (!empty($h['is_locked']) || $h['status_pengerjaan'] === 'Terkunci');

        $benar = (int)($h['jumlah_benar'] ?? 0);
        $salah = (int)($h['jumlah_salah'] ?? 0);
        $tidakJawab = (int)($h['jumlah_tidak_jawab'] ?? 0);

        $totalSoal = (int)($h['total_soal_ujian'] ?? 0);

        $ansCount = 0;
        if (!empty($h['jawaban_json'])) {
            $rawAns = json_decode($h['jawaban_json'], true);
            $arrAns = $rawAns['answers'] ?? ($rawAns['answers_by_index'] ?? $rawAns);
            if (is_array($arrAns)) {
                foreach ($arrAns as $aVal) {
                    if ($aVal !== '' && $aVal !== null && $aVal !== []) {
                        $ansCount++;
                    }
                }
            }
        }

        if ($totalSoal <= 0) {
            $totalSoal = max(20, $ansCount > 0 ? $ansCount : ($benar + $salah));
        }

        if ($ansCount > 0 && $ansCount < $totalSoal) {
            $tidakJawab = $totalSoal - $ansCount;
            $salah = max(0, $ansCount - $benar);
        } elseif ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
            $tidakJawab = $totalSoal - ($benar + $salah);
        }

        ob_start();
        ?>
        <div class="hasil-card-kj" data-idx="<?= $idx ?>" style="background: white; border-radius: 14px; padding: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800;">
                        <?= $idx + 1 ?>
                    </span>
                    <div>
                        <h4 style="margin: 0; font-size: 0.92rem; font-weight: 800; color: #0f172a;">
                            <?= htmlspecialchars($h['nama_siswa']) ?>
                        </h4>
                        <div style="font-size: 0.72rem; color: #64748b;">
                            No: <b><?= htmlspecialchars($h['nis']) ?></b> • <?= htmlspecialchars($h['rombel_kj'] ?: 'Kelas Jauh') ?>
                        </div>
                        <?php if (!empty($h['nama_ujian'])): ?>
                            <div style="font-size: 0.68rem; color: #0284c7; font-weight: 700; margin-top: 2px;">
                                <i class="fa-solid fa-file-lines"></i> <?= htmlspecialchars($h['nama_ujian']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.25rem; font-weight: 900; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                        <?= $nilai ?>
                    </div>
                    <span style="font-size: 0.65rem; font-weight: 800; padding: 2px 7px; border-radius: 10px; background: <?= $isTuntas ? '#ecfdf5' : '#fef2f2' ?>; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                        <?= $isTuntas ? 'Tuntas' : 'Remedial' ?>
                    </span>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; justify-content: space-between; border-top: 1px dashed #f1f5f9; padding-top: 8px; margin-top: 8px; font-size: 0.72rem;">
                <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                    <span style="color: #15803d; font-weight: 800; background: #ecfdf5; padding: 2px 7px; border-radius: 6px; border: 1px solid #bbf7d0;">
                        <i class="fa-solid fa-check"></i> <?= $benar ?> Benar
                    </span>
                    <span style="color: #b91c1c; font-weight: 800; background: #fef2f2; padding: 2px 7px; border-radius: 6px; border: 1px solid #fecaca;">
                        <i class="fa-solid fa-xmark"></i> <?= $salah ?> Salah
                    </span>
                    <span style="color: #64748b; font-weight: 800; background: #f8fafc; padding: 2px 7px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <i class="fa-solid fa-circle-minus"></i> <?= $tidakJawab ?> Kosong
                    </span>
                    <?php if ($isLocked): ?>
                        <span style="color: #dc2626; font-weight: 800; background: #fff1f2; padding: 2px 7px; border-radius: 6px; border: 1px solid #fda4af;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Kena Pelanggaran
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($isLocked || in_array($h['status_pengerjaan'], ['Terkunci', 'Mengerjakan'])): ?>
                    <button type="button" onclick="bukaKunciPeserta(<?= (int)$h['id'] ?>, '<?= htmlspecialchars(addslashes($h['nama_siswa'])) ?>')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
                        <i class="fa-solid fa-unlock"></i> Buka Kunci
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Handler AJAX Hasil Nilai Kelas Jauh
$isAjaxKJ = (!empty($_GET['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
if ($isAjaxKJ && ($activeTab === 'nilai' || isset($_GET['topik_id']) || isset($_GET['mapel']) || isset($_GET['kelas']))) {
    header('Content-Type: application/json; charset=utf-8');
    $limitKJ = isset($_GET['limit']) ? ($_GET['limit'] === 'all' ? 0 : (int)$_GET['limit']) : 30;
    if ($limitKJ < 0) $limitKJ = 30;

    $displayListKJ = ($limitKJ > 0 && count($hasilListKJ) > $limitKJ) ? array_slice($hasilListKJ, 0, $limitKJ) : $hasilListKJ;

    ob_start();
    if (empty($hasilListKJ)) {
        ?>
        <div style="text-align: center; padding: 30px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.82rem;">
            <i class="fa-solid fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
            Tidak ada data nilai siswa yang sesuai dengan filter Topik, Mapel, atau Kelas.<br>Silakan ganti pilihan filter di atas.
        </div>
        <?php
    } else {
        foreach ($displayListKJ as $idx => $h) {
            echo renderHasilCardKJ($h, $idx);
        }
    }
    $cardsHtml = ob_get_clean();

    echo json_encode([
        'success' => true,
        'total' => count($hasilListKJ),
        'display_count' => count($displayListKJ),
        'limit' => $limitKJ,
        'cards_html' => $cardsHtml,
        'stat_rata2' => $statRata2,
        'stat_tertinggi' => $statTertinggi,
        'stat_tuntas' => "{$statTuntasCount}/" . count($hasilListKJ),
        'mapel_list' => array_values(array_unique($mapelList)),
        'cetak_url' => "cetak_hasil.php?tab=nilai&topik_id={$selectedTopikId}&mapel=" . urlencode($selectedMapel) . "&kelas=" . urlencode($selectedKelas) . "&sort=" . urlencode($selectedSort)
    ]);
    exit;
}

$page_title = 'Portal Pengawas - Kelas Jauh CBT';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #059669 0%, #0d9488 100%);">
    <div class="header-top">
        <div class="greeting">
            <p><i class="fa-solid fa-satellite-dish"></i> Portal Mandiri Kelas Jauh</p>
            <h1>Pengawas CBT Kelas Jauh</h1>
        </div>
        <div class="avatar" style="color: #ef4444; background: white; cursor: pointer;" onclick="confirmLogoutPengawasKJ(event)" title="Keluar">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 90px;">
    <!-- Welcome Info Card -->
    <div style="background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.98rem; color: #0f172a;"><?= htmlspecialchars($nama_petugas) ?></div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 600;">NIP/ID: <?= htmlspecialchars($nip_petugas) ?> • Pengawas Sentral</div>
                </div>
            </div>
            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;">
                CBT KELAS JAUH
            </span>
        </div>

        <!-- Quick Summary 3 Kolom -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; text-align: center;">
            <div style="background: #f8fafc; border-radius: 10px; padding: 10px 6px; border: 1px solid #f1f5f9;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Peserta</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #0f172a;"><?= $totalSiswaKJ ?></div>
            </div>
            <div style="background: #f0fdf4; border-radius: 10px; padding: 10px 6px; border: 1px solid #dcfce7;">
                <div style="font-size: 0.65rem; color: #166534; font-weight: 700; text-transform: uppercase;">Absen Hari Ini</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #15803d;"><?= $hadirHariIniKJ ?></div>
            </div>
            <div style="background: #eff6ff; border-radius: 10px; padding: 10px 6px; border: 1px solid #dbeafe;">
                <div style="font-size: 0.65rem; color: #1e40af; font-weight: 700; text-transform: uppercase;">Paket Soal</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #2563eb;"><?= $totalPaketSoal > 0 ? $totalPaketSoal : count($mapelList) ?></div>
            </div>
        </div>
    </div>

    <!-- Switch Navigation Pills: 3 TAB JELAS & BERBEDA -->
    <div style="display: flex; gap: 6px; margin-bottom: 16px; background: #f1f5f9; padding: 4px; border-radius: 14px; border: 1px solid #e2e8f0;">
        <button type="button" id="tabBtn_beranda" onclick="switchKJTab('beranda')" style="flex: 1; padding: 10px 6px; border: none; border-radius: 10px; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: 0.2s; background: <?= ($activeTab === 'beranda') ? '#059669' : 'transparent' ?>; color: <?= ($activeTab === 'beranda') ? '#ffffff' : '#475569' ?>;">
            <i class="fa-solid fa-house"></i> Beranda
        </button>
        <button type="button" id="tabBtn_scan" onclick="switchKJTab('scan')" style="flex: 1; padding: 10px 6px; border: none; border-radius: 10px; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: 0.2s; background: <?= ($activeTab === 'scan') ? '#059669' : 'transparent' ?>; color: <?= ($activeTab === 'scan') ? '#ffffff' : '#475569' ?>;">
            <i class="fa-solid fa-qrcode"></i> Scan Absen
        </button>
        <button type="button" id="tabBtn_nilai" onclick="switchKJTab('nilai')" style="flex: 1; padding: 10px 6px; border: none; border-radius: 10px; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: 0.2s; background: <?= ($activeTab === 'nilai') ? '#059669' : 'transparent' ?>; color: <?= ($activeTab === 'nilai') ? '#ffffff' : '#475569' ?>;">
            <i class="fa-solid fa-chart-column"></i> Hasil Nilai
        </button>
    </div>

    <!-- ================= TAB 1: BERANDA DASHBOARD ================= -->
    <div id="tabContent_beranda" style="display: <?= ($activeTab === 'beranda') ? 'block' : 'none' ?>;">
        <!-- Card Menu Aksi Cepat Pengawas -->
        <div style="background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-bolt" style="color: #059669;"></i> Menu Aksi Cepat Pengawas
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div onclick="switchKJTab('scan')" style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 6px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div style="font-weight: 800; font-size: 0.86rem; color: #065f46;">Scan Absen</div>
                    <div style="font-size: 0.7rem; color: #15803d;">Buka kamera scanner & input nomor peserta</div>
                </div>

                <div onclick="switchKJTab('nilai')" style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 6px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-square-poll-vertical"></i>
                    </div>
                    <div style="font-weight: 800; font-size: 0.86rem; color: #1e40af;">Hasil Nilai</div>
                    <div style="font-size: 0.7rem; color: #2563eb;">Rekap skor ujian & buka kunci siswa</div>
                </div>
            </div>
        </div>

        <!-- Rombongan Belajar Siswa Kelas Jauh -->
        <div style="background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-users" style="color: #059669;"></i> Rombel Siswa Kelas Jauh
                </div>
                <span style="font-size: 0.7rem; font-weight: 800; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 20px;">
                    <?= $totalSiswaKJ ?> Siswa Total
                </span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php if (empty($rombelKJ)): ?>
                    <div style="text-align: center; color: #94a3b8; font-size: 0.8rem; padding: 12px;">Data rombel belum tersedia.</div>
                <?php else: ?>
                    <?php foreach ($rombelKJ as $r): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 10px;">
                            <div style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">
                                <i class="fa-solid fa-graduation-cap" style="color: #059669; margin-right: 6px;"></i>
                                <?= htmlspecialchars($r['kelas_nama']) ?>
                            </div>
                            <span style="font-weight: 800; font-size: 0.78rem; color: #0f172a; background: white; border: 1px solid #e2e8f0; padding: 2px 10px; border-radius: 8px;">
                                <?= (int)$r['jml'] ?> Siswa
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panduan & Pengingat Pengawas -->
        <div style="background: #f0fdf4; border-radius: 16px; padding: 16px; border: 1.5px dashed #86efac;">
            <div style="font-weight: 800; font-size: 0.85rem; color: #166534; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-info"></i> Petunjuk Pengawas CBT Kelas Jauh
            </div>
            <ul style="margin: 0; padding-left: 18px; font-size: 0.75rem; color: #15803d; line-height: 1.6;">
                <li>Siswa wajib terabsen terlebih dahulu di menu <b>Scan Absen</b> sebelum mengerjakan soal ujian.</li>
                <li>Jika siswa mengalami kendala koneksi atau akun terkunci, buka menu <b>Hasil Nilai</b> lalu klik tombol <b>Buka Kunci</b>.</li>
                <li>Data absensi dan nilai siswa tersinkronisasi otomatis dengan server CBT Madrasah.</li>
            </ul>
        </div>
    </div>

    <!-- ================= TAB 2: SCAN ABSENSI KELAS JAUH ================= -->
    <div id="tabContent_scan" style="display: <?= ($activeTab === 'scan') ? 'block' : 'none' ?>;">
        <!-- Shortcut Masuk Scanner Mobile Full Camera -->
        <a href="cbt_scan.php" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 14px 18px; border-radius: 14px; text-decoration: none; font-weight: 800; font-size: 0.95rem; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-camera" style="font-size: 1.3rem;"></i>
                <div>
                    <div>Buka Kamera Scanner 1 Pintu</div>
                    <div style="font-size: 0.72rem; opacity: 0.85; font-weight: 500;">Scan QR / Barcode kartu ujian otomatis</div>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right"></i>
        </a>

        <!-- Absen Cepat Manual NIS / Username -->
        <div style="background: white; border-radius: 14px; padding: 14px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">
                <i class="fa-solid fa-keyboard" style="color: #059669;"></i> Input Cepat No. Peserta / Username Kelas Jauh:
            </label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="manualCodeKJ" placeholder="Contoh: KJ-1001 atau username..." style="flex: 1; padding: 11px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.9rem; font-weight: 700; outline: none;" onkeydown="if(event.key==='Enter') submitScanKJ()">
                <button type="button" onclick="submitScanKJ()" style="background: #059669; color: white; border: none; padding: 11px 18px; border-radius: 10px; font-weight: 800; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">
                    <i class="fa-solid fa-check"></i> Absenkan
                </button>
            </div>
            <div id="quickScanMsg" style="margin-top: 8px; font-size: 0.78rem; font-weight: 700; display: none;"></div>
        </div>

        <!-- Live Feed Absensi Hari Ini -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 0.92rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #059669;"></i> Riwayat Kehadiran Hari Ini
            </h3>
            <span style="font-size: 0.7rem; color: #059669; font-weight: 800; background: #ecfdf5; padding: 2px 8px; border-radius: 20px;">
                <?= count($listAbsenKJ) ?> Siswa Hadir
            </span>
        </div>

        <?php if (empty($listAbsenKJ)): ?>
            <div style="text-align: center; padding: 30px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.82rem;">
                <i class="fa-solid fa-user-clock" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
                Belum ada siswa kelas jauh yang terabsen pada hari ini.<br>Gunakan scanner kamera atau input cepat nomor peserta di atas.
            </div>
        <?php else: ?>
            <!-- Menampilkan 10 data pertama secara default, sisanya disembunyikan dan dibuka bertahap (+10) -->
            <div id="absenKJListContainer" style="display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($listAbsenKJ as $idx => $a): ?>
                    <div class="absen-kj-item" style="background: white; border-radius: 12px; padding: 12px 14px; border: 1px solid #e2e8f0; display: <?= ($idx < 10) ? 'flex' : 'none' ?>; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.05rem;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($a['nama_siswa']) ?></div>
                                <div style="font-size: 0.72rem; color: #64748b;">
                                    No: <b><?= htmlspecialchars($a['nis']) ?></b> • Mapel: <b><?= htmlspecialchars($a['mapel_nama'] ?: 'Umum') ?></b>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: #ecfdf5; color: #059669; font-weight: 800; font-size: 0.68rem; padding: 3px 8px; border-radius: 20px;">
                                <?= date('H:i', strtotime($a['waktu_scan'])) ?> WIB
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($listAbsenKJ) > 10): ?>
                <div id="wrapLoadMoreAbsen" style="margin-top: 12px; text-align: center;">
                    <button type="button" id="btnLoadMoreAbsen" onclick="loadMoreAbsenKJ()" style="width: 100%; padding: 12px 16px; background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; border-radius: 12px; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;">
                        <i class="fa-solid fa-chevron-down"></i>
                        <span>Lihat Selengkapnya (+10 Siswa, Sisa <?= count($listAbsenKJ) - 10 ?> Lagi)</span>
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ================= TAB 3: REKAP HASIL & NILAI KELAS JAUH ================= -->
    <div id="tabContent_nilai" style="display: <?= ($activeTab === 'nilai') ? 'block' : 'none' ?>;">
        <!-- Filter Bertingkat: 1. Topik Ujian -> 2. Mapel -> 3. Kelas (AJAX Zero Reload) -->
        <form id="formFilterKJ" onsubmit="event.preventDefault(); applyFilterKJ(false);" style="background: white; border-radius: 16px; padding: 14px; margin-bottom: 14px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <input type="hidden" name="tab" value="nilai">

            <!-- 1. Topik Ujian -->
            <div>
                <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                    <i class="fa-solid fa-layer-group" style="color: #059669;"></i> 1. Topik Ujian (Aktif):
                </label>
                <select name="topik_id" id="selTopikKJ" onchange="applyFilterKJ(true)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <option value="0">-- Semua Topik Ujian --</option>
                    <?php foreach ($topikList as $tp): ?>
                        <option value="<?= (int)$tp['id'] ?>" <?= ($selectedTopikId == $tp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tp['nama_topik']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Mata Pelajaran -->
            <div>
                <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                    <i class="fa-solid fa-book-open" style="color: #059669;"></i> 2. Mata Pelajaran:
                </label>
                <select name="mapel" id="selMapelKJ" onchange="applyFilterKJ(false)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    <?php foreach ($mapelList as $mp): ?>
                        <option value="<?= htmlspecialchars($mp) ?>" <?= ($selectedMapel === $mp) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mp) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 3. Filter Kelas -->
            <div>
                <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                    <i class="fa-solid fa-graduation-cap" style="color: #059669;"></i> 3. Filter Kelas:
                </label>
                <select name="kelas" id="selKelasKJ" onchange="applyFilterKJ(false)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <option value="">-- Semua Kelas Jauh --</option>
                    <?php foreach ($kelasListKJ as $kls): ?>
                        <option value="<?= htmlspecialchars($kls) ?>" <?= ($selectedKelas === $kls) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kls) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Urutan Data Siswa (Nilai / Alphabet) -->
            <div>
                <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
                    <i class="fa-solid fa-arrow-down-wide-short" style="color: #059669;"></i> 4. Urutan Data Siswa:
                </label>
                <div style="display: flex; gap: 8px;">
                    <select name="sort" id="selSortKJ" onchange="applyFilterKJ(false)" style="flex: 1; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.86rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                        <option value="nilai_desc" <?= ($selectedSort === 'nilai_desc') ? 'selected' : '' ?>>Nilai Tertinggi (Rank 1 ➔ Terendah)</option>
                        <option value="nilai_asc" <?= ($selectedSort === 'nilai_asc') ? 'selected' : '' ?>>Nilai Terendah ➔ Tertinggi</option>
                        <option value="nama_asc" <?= ($selectedSort === 'nama_asc') ? 'selected' : '' ?>>Alphabet Nama Siswa (A ➔ Z)</option>
                        <option value="nama_desc" <?= ($selectedSort === 'nama_desc') ? 'selected' : '' ?>>Alphabet Nama Siswa (Z ➔ A)</option>
                    </select>
                    <button type="button" id="btnResetFilterKJ" onclick="resetFilterKJ()" style="padding: 10px 14px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 10px; display: <?= ($selectedTopikId > 0 || !empty($selectedMapel) || !empty($selectedKelas) || $selectedSort !== 'nilai_desc') ? 'flex' : 'none' ?>; align-items: center; justify-content: center; cursor: pointer;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tombol Download PDF & Print (Terisolasi Sesuai Filter) -->
        <div style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 8px; margin-bottom: 14px;">
            <a id="btnCetakPdfKJ" href="cetak_hasil.php?tab=nilai&download=1&topik_id=<?= $selectedTopikId ?>&mapel=<?= urlencode($selectedMapel) ?>&kelas=<?= urlencode($selectedKelas) ?>&sort=<?= urlencode($selectedSort) ?>" target="_blank" style="box-sizing: border-box; padding: 12px 14px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
                <i class="fa-solid fa-file-arrow-down" style="font-size: 1.05rem;"></i> Download PDF (A4)
            </a>
            <a id="btnPreviewPdfKJ" href="cetak_hasil.php?tab=nilai&autoprint=1&topik_id=<?= $selectedTopikId ?>&mapel=<?= urlencode($selectedMapel) ?>&kelas=<?= urlencode($selectedKelas) ?>&sort=<?= urlencode($selectedSort) ?>" target="_blank" style="box-sizing: border-box; padding: 12px 12px; background: #ffffff; color: #334155; border: 1.5px solid #cbd5e1; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.82rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-print" style="color: #64748b;"></i> Cetak / Print
            </a>
        </div>

        <!-- Metric Card Hasil Ujian -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 14px;">
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">RATA-RATA</div>
                <div id="statRata2KJ" style="font-size: 1.3rem; font-weight: 900; color: #2563eb;"><?= $statRata2 ?></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">TERTINGGI</div>
                <div id="statTertinggiKJ" style="font-size: 1.3rem; font-weight: 900; color: #10b981;"><?= $statTertinggi ?></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">TUNTAS KKM</div>
                <div id="statTuntasKJ" style="font-size: 1.3rem; font-weight: 900; color: #059669;"><?= $statTuntasCount ?>/<?= count($hasilListKJ) ?></div>
            </div>
        </div>

        <!-- Header Daftar Nilai Siswa with Limit Selector -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 0.92rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-list-ol" style="color: #059669;"></i> <span id="lblTitleNilaiKJ">Daftar Nilai Siswa (<?= count($hasilListKJ) ?>)</span>
            </h3>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 0.7rem; color: #64748b; font-weight: 700;">Tampil:</span>
                <select id="selLimitKJ" onchange="applyFilterKJ(false)" style="padding: 4px 8px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.75rem; font-weight: 700; color: #0f172a; background: white; cursor: pointer;">
                    <option value="30" <?= ($selectedLimit == 30) ? 'selected' : '' ?>>30 Siswa</option>
                    <option value="50" <?= ($selectedLimit == 50) ? 'selected' : '' ?>>50 Siswa</option>
                    <option value="100" <?= ($selectedLimit == 100) ? 'selected' : '' ?>>100 Siswa</option>
                    <option value="all" <?= ($selectedLimit === 0 || $selectedLimit === 'all') ? 'selected' : '' ?>>Semua</option>
                </select>
            </div>
        </div>

        <?php
        $displayListKJ = ($selectedLimit > 0 && count($hasilListKJ) > $selectedLimit) ? array_slice($hasilListKJ, 0, $selectedLimit) : $hasilListKJ;
        ?>
        <div id="daftarNilaiKJContainer" style="display: flex; flex-direction: column; gap: 10px; transition: opacity 0.15s ease;">
            <?php if (empty($hasilListKJ)): ?>
                <div style="text-align: center; padding: 30px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.82rem;">
                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
                    Tidak ada data nilai siswa yang sesuai dengan filter Topik, Mapel, atau Kelas.<br>Silakan ganti pilihan filter di atas.
                </div>
            <?php else: ?>
                <?php foreach ($displayListKJ as $idx => $h): ?>
                    <?= renderHasilCardKJ($h, $idx) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="wrapLoadMoreNilaiKJ" style="margin-top: 12px; text-align: center; display: <?= (count($hasilListKJ) > count($displayListKJ)) ? 'block' : 'none' ?>;">
            <button type="button" id="btnLoadMoreNilaiKJ" onclick="loadMoreNilaiKJ()" style="width: 100%; padding: 12px 16px; background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; border-radius: 12px; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;">
                <i class="fa-solid fa-chevron-down"></i>
                <span id="lblLoadMoreNilaiKJ">Lihat Selengkapnya (+30 Siswa, Sisa <?= count($hasilListKJ) - count($displayListKJ) ?> Lagi)</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Tab Switch Controller (Beranda, Scan, Nilai)
    function switchKJTab(tab) {
        const tabs = ['beranda', 'scan', 'nilai'];
        tabs.forEach(t => {
            const btn = document.getElementById('tabBtn_' + t);
            const content = document.getElementById('tabContent_' + t);
            const nav = document.getElementById('navItem_' + t);

            if (t === tab) {
                if (btn) {
                    btn.style.background = '#059669';
                    btn.style.color = '#ffffff';
                }
                if (content) {
                    content.style.display = 'block';
                }
                if (nav) {
                    nav.style.color = '#059669';
                }
            } else {
                if (btn) {
                    btn.style.background = 'transparent';
                    btn.style.color = '#475569';
                }
                if (content) {
                    content.style.display = 'none';
                }
                if (nav) {
                    nav.style.color = '#64748b';
                }
            }
        });

        // Update URL tanpa reload
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    }

    // Pagination Riwayat Kehadiran (Tampilkan 10, klik lagi tambah 10 seterusnya)
    let currentAbsenVisible = 10;
    function loadMoreAbsenKJ() {
        const items = document.querySelectorAll('.absen-kj-item');
        const total = items.length;
        currentAbsenVisible += 10;

        items.forEach((item, idx) => {
            if (idx < currentAbsenVisible) {
                item.style.display = 'flex';
            }
        });

        const btn = document.getElementById('btnLoadMoreAbsen');
        const wrap = document.getElementById('wrapLoadMoreAbsen');
        if (currentAbsenVisible >= total) {
            if (btn) btn.style.display = 'none';
            if (wrap) {
                wrap.innerHTML = '<span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700;"><i class="fa-solid fa-check-circle" style="color: #059669;"></i> Seluruh riwayat kehadiran (' + total + ' siswa) telah ditampilkan</span>';
            }
        } else {
            const sisa = total - currentAbsenVisible;
            const nextBatch = Math.min(10, sisa);
            if (btn) {
                btn.innerHTML = `<i class="fa-solid fa-chevron-down"></i> <span>Lihat Selengkapnya (+${nextBatch} Siswa, Sisa ${sisa} Lagi)</span>`;
            }
        }
    }

    function submitScanKJ() {
        const inp = document.getElementById('manualCodeKJ');
        const code = inp.value.trim();
        const msgEl = document.getElementById('quickScanMsg');

        if (!code) {
            inp.focus();
            return;
        }

        msgEl.style.display = 'block';
        msgEl.style.color = '#2563eb';
        msgEl.innerText = 'Memproses absensi...';

        fetch('/cbt/scan/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                code: code,
                ruang_nama: 'Sentral Kelas Jauh'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                if (res.already) {
                    msgEl.style.color = '#dc2626';
                    msgEl.innerText = '⚠️ Siswa sudah scan sebelumnya: ' + (res.siswa ? res.siswa.nama : code) + ' pada jam ' + (res.waktu || '') + ' WIB. Belum waktunya ganti sesi!';
                    alert('⚠️ SISWA SUDAH SCAN SEBELUMNYA!\n\nSiswa ' + (res.siswa ? res.siswa.nama : code) + ' telah terabsen pada jam ' + (res.waktu || '') + ' WIB.\nBelum waktunya ganti sesi/jam ujian!');
                } else {
                    msgEl.style.color = '#059669';
                    msgEl.innerText = '✓ Berhasil: ' + (res.siswa ? res.siswa.nama : code) + ' terabsen pada mapel ' + (res.mapel_nama || '-');
                    inp.value = '';
                    setTimeout(() => { window.location.reload(); }, 1200);
                }
            } else {
                msgEl.style.color = '#dc2626';
                msgEl.innerText = '✗ ' + (res.message || 'Gagal memproses kode');
            }
        })
        .catch(() => {
            msgEl.style.color = '#dc2626';
            msgEl.innerText = 'Terjadi kesalahan jaringan atau server.';
        });
    }

    function bukaKunciPeserta(pesertaId, namaSiswa) {
        if (!confirm('Buka kunci ujian untuk ' + namaSiswa + ' agar dapat melanjutkan ujian?')) return;

        fetch('guru_kelas_jauh.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'unlock_peserta',
                peserta_id: pesertaId
            })
        })
        .then(res => res.json())
        .then(res => {
            alert(res.message);
            if (res.success) {
                applyFilterKJ(false);
            }
        })
        .catch(() => {
            alert('Gagal menghubungi server.');
        });
    }

    // AJAX Filter & Pagination Rekap Nilai Siswa Kelas Jauh
    let kjCurrentLimit = <?= (int)($selectedLimit ?: 30) ?>;
    let kjAbortController = null;

    function applyFilterKJ(isTopikChange) {
        if (isTopikChange) {
            const selM = document.getElementById('selMapelKJ');
            if (selM) selM.value = '';
        }

        const topikId = document.getElementById('selTopikKJ') ? document.getElementById('selTopikKJ').value : '0';
        const mapel = document.getElementById('selMapelKJ') ? document.getElementById('selMapelKJ').value : '';
        const kelas = document.getElementById('selKelasKJ') ? document.getElementById('selKelasKJ').value : '';
        const sort = document.getElementById('selSortKJ') ? document.getElementById('selSortKJ').value : 'nilai_desc';
        const limit = document.getElementById('selLimitKJ') ? document.getElementById('selLimitKJ').value : '30';
        kjCurrentLimit = (limit === 'all') ? 9999 : parseInt(limit || 30);

        const container = document.getElementById('daftarNilaiKJContainer');
        if (container) {
            container.style.opacity = '0.35';
            container.style.pointerEvents = 'none';
        }

        const btnReset = document.getElementById('btnResetFilterKJ');
        if (btnReset) {
            btnReset.style.display = (topikId !== '0' || mapel !== '' || kelas !== '' || sort !== 'nilai_desc') ? 'flex' : 'none';
        }

        if (kjAbortController) {
            kjAbortController.abort();
        }
        kjAbortController = new AbortController();

        const queryParams = new URLSearchParams({
            tab: 'nilai',
            ajax: '1',
            topik_id: topikId,
            mapel: mapel,
            kelas: kelas,
            sort: sort,
            limit: limit
        });

        // Update URL tanpa reload
        try {
            const cleanParams = new URLSearchParams(queryParams);
            cleanParams.delete('ajax');
            const newUrl = 'guru_kelas_jauh.php?' + cleanParams.toString();
            window.history.replaceState({}, '', newUrl);
        } catch (e) {}

        fetch('guru_kelas_jauh.php?' + queryParams.toString(), {
            signal: kjAbortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            // Update stats
            const elRata = document.getElementById('statRata2KJ');
            if (elRata) elRata.textContent = data.stat_rata2;
            const elMax = document.getElementById('statTertinggiKJ');
            if (elMax) elMax.textContent = data.stat_tertinggi;
            const elTuntas = document.getElementById('statTuntasKJ');
            if (elTuntas) elTuntas.textContent = data.stat_tuntas;

            // Update Title
            const elTitle = document.getElementById('lblTitleNilaiKJ');
            if (elTitle) elTitle.textContent = 'Daftar Nilai Siswa (' + data.total + ')';

            // Update PDF Links
            const btnPdf = document.getElementById('btnCetakPdfKJ');
            if (btnPdf && data.cetak_url) {
                btnPdf.href = data.cetak_url + '&download=1';
            }
            const btnPrev = document.getElementById('btnPreviewPdfKJ');
            if (btnPrev && data.cetak_url) {
                btnPrev.href = data.cetak_url + '&autoprint=1';
            }

            // Update Mapel options if Topik changed
            if (isTopikChange && data.mapel_list) {
                const selMapel = document.getElementById('selMapelKJ');
                if (selMapel) {
                    const prevMapel = selMapel.value;
                    selMapel.innerHTML = '<option value="">-- Semua Mata Pelajaran --</option>';
                    data.mapel_list.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m;
                        opt.textContent = m;
                        if (m === prevMapel) opt.selected = true;
                        selMapel.appendChild(opt);
                    });
                }
            }

            // Update Cards Container
            if (container) {
                container.innerHTML = data.cards_html;
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }

            // Update Load More button
            const wrapMore = document.getElementById('wrapLoadMoreNilaiKJ');
            if (wrapMore) {
                if (data.total > data.display_count) {
                    wrapMore.style.display = 'block';
                    const lblMore = document.getElementById('lblLoadMoreNilaiKJ');
                    if (lblMore) {
                        const sisa = data.total - data.display_count;
                        lblMore.textContent = 'Lihat Selengkapnya (+30 Siswa, Sisa ' + sisa + ' Lagi)';
                    }
                } else {
                    wrapMore.style.display = 'none';
                }
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return;
            if (container) {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }
        });
    }

    function loadMoreNilaiKJ() {
        kjCurrentLimit += 30;
        const topikId = document.getElementById('selTopikKJ') ? document.getElementById('selTopikKJ').value : '0';
        const mapel = document.getElementById('selMapelKJ') ? document.getElementById('selMapelKJ').value : '';
        const kelas = document.getElementById('selKelasKJ') ? document.getElementById('selKelasKJ').value : '';
        const sort = document.getElementById('selSortKJ') ? document.getElementById('selSortKJ').value : 'nilai_desc';

        const btnMore = document.getElementById('btnLoadMoreNilaiKJ');
        if (btnMore) {
            btnMore.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memuat data...</span>';
        }

        const queryParams = new URLSearchParams({
            tab: 'nilai',
            ajax: '1',
            topik_id: topikId,
            mapel: mapel,
            kelas: kelas,
            sort: sort,
            limit: kjCurrentLimit
        });

        fetch('guru_kelas_jauh.php?' + queryParams.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            const container = document.getElementById('daftarNilaiKJContainer');
            if (container) {
                container.innerHTML = data.cards_html;
            }
            const wrapMore = document.getElementById('wrapLoadMoreNilaiKJ');
            if (wrapMore) {
                if (data.total > data.display_count) {
                    wrapMore.style.display = 'block';
                    if (btnMore) {
                        const sisa = data.total - data.display_count;
                        btnMore.innerHTML = '<i class="fa-solid fa-chevron-down"></i> <span>Lihat Selengkapnya (+30 Siswa, Sisa ' + sisa + ' Lagi)</span>';
                    }
                } else {
                    wrapMore.style.display = 'none';
                }
            }
        })
        .catch(() => {
            if (btnMore) {
                btnMore.innerHTML = '<i class="fa-solid fa-chevron-down"></i> <span>Lihat Selengkapnya (+30 Siswa Lagi)</span>';
            }
        });
    }

    function resetFilterKJ() {
        const selT = document.getElementById('selTopikKJ');
        if (selT) selT.value = '0';
        const selM = document.getElementById('selMapelKJ');
        if (selM) selM.value = '';
        const selK = document.getElementById('selKelasKJ');
        if (selK) selK.value = '';
        const selS = document.getElementById('selSortKJ');
        if (selS) selS.value = 'nilai_desc';
        const selL = document.getElementById('selLimitKJ');
        if (selL) selL.value = '30';
        applyFilterKJ(true);
    }

    function confirmLogoutPengawasKJ(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<div style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin-top: 4px;">Keluar Akun Pengawas?</div>',
                html: `
                    <div style="font-size: 0.88rem; color: #64748b; line-height: 1.5; margin: 8px 0 14px;">
                        Bapak/Ibu <b><?= htmlspecialchars($nama_petugas) ?></b>, Anda akan mengakhiri sesi pada <b>Portal Pengawas Kelas Jauh</b>.
                    </div>
                    <div style="background: #fef2f2; border: 1.5px dashed #fca5a5; border-radius: 12px; padding: 10px 14px; font-size: 0.78rem; color: #b91c1c; display: flex; align-items: center; gap: 10px; text-align: left;">
                        <i class="fa-solid fa-shield-halved" style="font-size: 1.2rem; flex-shrink: 0; color: #ef4444;"></i>
                        <span>Pastikan Anda telah selesai memantau ujian & seluruh absensi siswa cabang telah tersimpan.</span>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 6px;"></i> Ya, Keluar Sekarang',
                cancelButtonText: '<i class="fa-solid fa-xmark" style="margin-right: 6px;"></i> Batal',
                reverseButtons: true,
                background: '#ffffff',
                padding: '24px 20px',
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        } else {
            if (confirm('Keluar dari akun pengawas?')) {
                window.location.href = 'logout.php';
            }
        }
    }
</script>

<!-- BOTTOM NAVIGATION FOOTER PENGAWAS CBT KELAS JAUH -->
<div class="bottom-nav" style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; border-top: 1px solid #e2e8f0; box-shadow: 0 -4px 20px rgba(0,0,0,0.06); display: flex; justify-content: space-around; align-items: center; min-height: 68px; padding-top: 6px; padding-bottom: calc(14px + env(safe-area-inset-bottom)); z-index: 9999; background: #ffffff;">
    <a href="javascript:void(0)" onclick="switchKJTab('beranda'); window.scrollTo({top: 0, behavior: 'smooth'});" id="navItem_beranda" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($activeTab === 'beranda') ? '#059669' : '#64748b' ?>;">
        <i class="fa-solid fa-house" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Beranda</span>
    </a>
    <a href="javascript:void(0)" onclick="switchKJTab('scan'); window.scrollTo({top: 0, behavior: 'smooth'});" id="navItem_scan" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($activeTab === 'scan') ? '#059669' : '#64748b' ?>;">
        <i class="fa-solid fa-qrcode" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Scan Absen</span>
    </a>
    <a href="javascript:void(0)" onclick="switchKJTab('nilai'); window.scrollTo({top: 0, behavior: 'smooth'});" id="navItem_nilai" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($activeTab === 'nilai') ? '#059669' : '#64748b' ?>;">
        <i class="fa-solid fa-chart-column" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Hasil Nilai</span>
    </a>
    <a href="javascript:void(0)" onclick="confirmLogoutPengawasKJ(event)" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #ef4444;">
        <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Keluar</span>
    </a>
</div>

</div> <!-- .mobile-wrapper -->
</body>
</html>
