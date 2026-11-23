<?php
require_once 'koneksi.php';

// Auth check: Guru, Admin, atau Staf
if (!isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = $siakad_db ?: $core_db;

$paket_id = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;
$ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
$selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$selectedMapel = trim($_GET['mapel'] ?? '');
$selectedKelas = trim($_GET['kelas'] ?? '');
$search = trim($_GET['q'] ?? '');
$sort = trim($_GET['sort'] ?? 'nilai_desc');
$isKJTab = (isset($_GET['tab']) && $_GET['tab'] === 'nilai') || (stripos($selectedKelas, 'jauh') !== false);

$currentUjian = null;
$paket = null;
$topik = null;
$matchedUjianIds = [];

// Jika ada parameter topik_id atau mapel
if ($selectedTopikId > 0 || !empty($selectedMapel)) {
    try {
        if ($selectedTopikId > 0) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([$selectedTopikId]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }

        $sqlFind = "SELECT * FROM cbt_ujian WHERE (topik_id = :topik OR :topik = 0)";
        $paramsFind = [':topik' => $selectedTopikId];
        if (!empty($selectedMapel)) {
            $sqlFind .= " AND (mapel_nama = :mapel OR mapel_nama LIKE CONCAT('%', :mapel2, '%') OR :mapel3 LIKE CONCAT('%', mapel_nama, '%'))";
            $paramsFind[':mapel'] = $selectedMapel;
            $paramsFind[':mapel2'] = $selectedMapel;
            $paramsFind[':mapel3'] = $selectedMapel;
        }
        $stmtFind = $core_db->prepare($sqlFind . " ORDER BY id DESC");
        $stmtFind->execute($paramsFind);
        $uList = $stmtFind->fetchAll(PDO::FETCH_ASSOC);
        foreach ($uList as $u) {
            $matchedUjianIds[] = (int)$u['id'];
            if (!$currentUjian) {
                $currentUjian = $u;
            }
        }
    } catch (\Exception $e) {}
}

// Jika ada paket_id
if ($paket_id > 0) {
    try {
        $stmtP = $db->prepare("SELECT * FROM bs_paket WHERE id = ?");
        $stmtP->execute([$paket_id]);
        $paket = $stmtP->fetch(PDO::FETCH_ASSOC);

        if ($paket) {
            if (!empty($paket['topik_id'])) {
                $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
                $stmtT->execute([(int)$paket['topik_id']]);
                $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
            }

            // Cari ujian yang cocok
            $stmtFind = $core_db->prepare("
                SELECT * FROM cbt_ujian 
                WHERE (topik_id = :topik OR :topik = 0)
                  AND (
                      mapel_nama = :mapel 
                      OR mapel_nama LIKE CONCAT('%', :mapel2, '%') 
                      OR :mapel3 LIKE CONCAT('%', mapel_nama, '%')
                  )
                ORDER BY id DESC
            ");
            $stmtFind->execute([
                ':topik' => (int)($paket['topik_id'] ?? 0),
                ':mapel' => $paket['mapel_nama'],
                ':mapel2' => $paket['mapel_nama'],
                ':mapel3' => $paket['mapel_nama']
            ]);
            $uList = $stmtFind->fetchAll(PDO::FETCH_ASSOC);
            foreach ($uList as $u) {
                $matchedUjianIds[] = (int)$u['id'];
                if (!$currentUjian) {
                    $currentUjian = $u;
                }
            }
        }
    } catch (\Exception $e) {}
}

if ($ujian_id > 0) {
    $matchedUjianIds[] = $ujian_id;
    try {
        $stmtU = $core_db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
        $stmtU->execute([$ujian_id]);
        $currentUjian = $stmtU->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

if (empty($matchedUjianIds) && $isKJTab) {
    try {
        $uList = $core_db->query("SELECT DISTINCT ujian_id FROM cbt_peserta WHERE kelas_nama LIKE '%Jauh%' OR nis IN (SELECT no_peserta FROM cbt_peserta_khusus)")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($uList as $uid) {
            $matchedUjianIds[] = (int)$uid;
        }
    } catch (\Exception $e) {}
}

if (!$currentUjian) {
    $currentUjian = [
        'nama_ujian' => !empty($topik['nama_topik']) ? $topik['nama_topik'] : 'Rekap Nilai Siswa CBT',
        'mapel_nama' => !empty($selectedMapel) ? $selectedMapel : 'Semua Mapel',
        'topik_nama' => !empty($topik['nama_topik']) ? $topik['nama_topik'] : 'Asesmen CBT',
        'kelas_nama' => !empty($selectedKelas) ? $selectedKelas : ($isKJTab ? 'Semua Kelas Jauh' : 'Semua Kelas'),
        'kkm' => 75,
        'durasi_menit' => 90
    ];
}

$matchedUjianIds = array_unique(array_filter($matchedUjianIds));

$pesertaList = [];
$kkmUjian = (int)($currentUjian['kkm'] ?? ($paket['kkm'] ?? 75));

if (!empty($matchedUjianIds)) {
    try {
        $inIds = implode(',', $matchedUjianIds);
        $sql = "
            SELECT p.*, u.nama_ujian, u.kode_ujian, u.mapel_nama as ujian_mapel, u.kkm as kkm_ujian,
                   (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
            FROM cbt_peserta p
            JOIN cbt_ujian u ON p.ujian_id = u.id
            WHERE p.ujian_id IN ($inIds)
        ";
        $params = [];

        if ($isKJTab) {
            $sql .= " AND (p.kelas_nama LIKE '%Jauh%' OR p.nis IN (SELECT no_peserta FROM cbt_peserta_khusus) OR p.nis IN (SELECT username FROM cbt_peserta_khusus))";
        }

        if (!empty($selectedKelas)) {
            $sql .= " AND p.kelas_nama = :kelas";
            $params[':kelas'] = $selectedKelas;
        }

        if (!empty($search)) {
            $sql .= " AND (p.nama_siswa LIKE :q OR p.nis LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        // Custom Sort
        switch ($sort) {
            case 'nilai_asc':
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir ASC, p.nama_siswa ASC";
                break;
            case 'nama_asc':
                $sql .= " ORDER BY p.nama_siswa ASC, p.nilai_akhir DESC";
                break;
            case 'nama_desc':
                $sql .= " ORDER BY p.nama_siswa DESC, p.nilai_akhir DESC";
                break;
            case 'nis_asc':
                $sql .= " ORDER BY p.nis ASC, p.nama_siswa ASC";
                break;
            case 'waktu_desc':
                $sql .= " ORDER BY (p.waktu_selesai IS NOT NULL) DESC, p.waktu_selesai DESC, p.nilai_akhir DESC";
                break;
            case 'nilai_desc':
            default:
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
                break;
        }

        $stmtP = $core_db->prepare($sql);
        $stmtP->execute($params);
        $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

// Hitung statistik
$totalSelesai = 0;
$totalNilai = 0;
$tuntasCount = 0;
$maxScore = 0;
$minScore = 100;

foreach ($pesertaList as $p) {
    if ($p['status_pengerjaan'] === 'Selesai') {
        $totalSelesai++;
        $nilai = (float)$p['nilai_akhir'];
        $totalNilai += $nilai;
        if ($nilai > $maxScore) $maxScore = $nilai;
        if ($nilai < $minScore) $minScore = $nilai;
        if ($nilai >= $kkmUjian) {
            $tuntasCount++;
        }
    }
}
if ($totalSelesai === 0) {
    $minScore = 0;
}
$rataRata = $totalSelesai > 0 ? ($totalNilai / $totalSelesai) : 0;
$persenTuntas = $totalSelesai > 0 ? round(($tuntasCount / $totalSelesai) * 100) : 0;
$maxNilai = $maxScore;
$minNilai = $minScore;

include __DIR__ . '/../resources/views/cbt/cetak_hasil.php';
