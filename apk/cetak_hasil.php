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
if ($selectedTopikId == 5) {
    $selectedTopikId = 3;
}
$selectedMapel = trim($_GET['mapel'] ?? '');
$selectedKelas = trim($_GET['kelas'] ?? '');
$search = trim($_GET['q'] ?? '');
$sort = trim($_GET['sort'] ?? 'nilai_desc');
$isKJTab = (isset($_GET['tab']) && $_GET['tab'] === 'nilai') || (stripos($selectedKelas, 'jauh') !== false);

$currentUjian = null;
$paket = null;
$topik = null;
$pesertaList = [];

if ($selectedTopikId > 0) {
    try {
        $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
        $stmtT->execute([$selectedTopikId]);
        $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

if ($isKJTab) {
    // Mode Khusus Kelas Jauh: Filter terisolasi 100% sama dengan di layar portal pengawas
    try {
        $sql = "SELECT p.*, k.nama_siswa as nama_lengkap, k.no_peserta, k.kelas_nama as rombel_kj, 
                       u.nama_ujian, u.kode_ujian, u.mapel_nama as ujian_mapel, u.kkm as kkm_ujian, u.durasi_menit,
                       (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
                FROM cbt_peserta p
                INNER JOIN cbt_peserta_khusus k ON (p.nis = k.no_peserta OR p.nis = k.username OR p.nama_siswa = k.nama_siswa)
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id
                WHERE 1=1";
        $params = [];

        if ($selectedTopikId > 0) {
            $tpName = $topik['nama_topik'] ?? '';
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

        if (!empty($search)) {
            $sql .= " AND (p.nama_siswa LIKE :q OR p.nis LIKE :q OR k.nama_siswa LIKE :q2 OR k.no_peserta LIKE :q3)";
            $params[':q'] = "%{$search}%";
            $params[':q2'] = "%{$search}%";
            $params[':q3'] = "%{$search}%";
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

        if (!empty($pesertaList)) {
            $firstP = $pesertaList[0];
            $currentUjian = [
                'nama_ujian' => $firstP['nama_ujian'] ?? ($topik['nama_topik'] ?? 'Asesmen CBT Siswa'),
                'mapel_nama' => !empty($selectedMapel) ? $selectedMapel : ($firstP['ujian_mapel'] ?? 'Semua Mapel'),
                'topik_nama' => $topik['nama_topik'] ?? 'Asesmen CBT',
                'kelas_nama' => !empty($selectedKelas) ? $selectedKelas : 'Semua Kelas Jauh',
                'kkm' => (int)($firstP['kkm_ujian'] ?? 75),
                'durasi_menit' => (int)($firstP['durasi_menit'] ?? 90)
            ];
        } else {
            $currentUjian = [
                'nama_ujian' => $topik['nama_topik'] ?? 'Asesmen CBT Siswa',
                'mapel_nama' => !empty($selectedMapel) ? $selectedMapel : 'Semua Mapel',
                'topik_nama' => $topik['nama_topik'] ?? 'Asesmen CBT',
                'kelas_nama' => !empty($selectedKelas) ? $selectedKelas : 'Semua Kelas Jauh',
                'kkm' => 75,
                'durasi_menit' => 90
            ];
        }
    } catch (\Exception $e) {}

} else {
    // Mode Reguler CBT Admin
    $matchedUjianIds = [];
    if ($selectedTopikId > 0 || !empty($selectedMapel)) {
        try {
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

    if ($ujian_id > 0) {
        $matchedUjianIds[] = $ujian_id;
        try {
            $stmtU = $core_db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
            $stmtU->execute([$ujian_id]);
            $currentUjian = $stmtU->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
    }

    $matchedUjianIds = array_unique(array_filter($matchedUjianIds));

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
}

$kkmUjian = (int)($currentUjian['kkm'] ?? 75);

// Hitung statistik nilai
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

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $mapelClean = preg_replace('/[^A-Za-z0-9_-]/', '_', $currentUjian['mapel_nama'] ?? 'Mapel');
    $kelasClean = preg_replace('/[^A-Za-z0-9_-]/', '_', $currentUjian['kelas_nama'] ?? 'Kelas');
    $filename = "Rekap_Nilai_{$mapelClean}_{$kelasClean}.xls";
    
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'>";
    echo "<style>table{border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:11pt;} th,td{border:1px solid #000;padding:6px;text-align:left;} th{background:#e2e8f0;font-weight:bold;text-align:center;} .center{text-align:center;} .num{mso-number-format:'\@';}</style>";
    echo "</head><body>";
    echo "<h3>REKAPITULASI HASIL UJIAN CBT</h3>";
    echo "<p><b>Mata Pelajaran:</b> " . htmlspecialchars($currentUjian['mapel_nama'] ?? '-') . "<br>";
    echo "<b>Topik / Ujian:</b> " . htmlspecialchars($currentUjian['nama_ujian'] ?? '-') . "<br>";
    echo "<b>Kelas:</b> " . htmlspecialchars($currentUjian['kelas_nama'] ?? '-') . "<br>";
    echo "<b>KKM:</b> " . $kkmUjian . " | <b>Rata-rata:</b> " . number_format($rataRata, 1) . " | <b>Tertinggi:</b> " . number_format($maxNilai, 1) . "</p>";
    
    echo "<table>";
    echo "<thead><tr>";
    echo "<th>No</th><th>No Peserta / NIS</th><th>Nama Lengkap Siswa</th><th>Kelas</th><th>Benar</th><th>Salah</th><th>Tidak Dijawab</th><th>Nilai Akhir</th><th>Ketuntasan</th>";
    echo "</tr></thead><tbody>";
    
    foreach ($pesertaList as $idx => $p) {
        $benar = (int)($p['jumlah_benar'] ?? 0);
        $salah = (int)($p['jumlah_salah'] ?? 0);
        $tidakJawab = (int)($p['jumlah_tidak_jawab'] ?? 0);
        $totalSoal = (int)($p['total_soal_ujian'] ?? 0);
        if ($totalSoal <= 0) $totalSoal = max(20, $benar + $salah);
        if ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
            $tidakJawab = $totalSoal - ($benar + $salah);
        }
        $nilai = (float)($p['nilai_akhir'] ?? 0);
        $status = ($p['status_pengerjaan'] === 'Selesai') ? ($nilai >= $kkmUjian ? 'TUNTAS' : 'REMEDIAL') : ($p['status_pengerjaan'] ?? 'Belum Ujian');
        
        echo "<tr>";
        echo "<td class='center'>" . ($idx + 1) . "</td>";
        echo "<td class='num center'>" . htmlspecialchars($p['nis'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($p['nama_siswa'] ?? '') . "</td>";
        echo "<td class='center'>" . htmlspecialchars($p['kelas_nama'] ?? ($p['rombel_kj'] ?? '')) . "</td>";
        echo "<td class='center'>" . $benar . "</td>";
        echo "<td class='center'>" . $salah . "</td>";
        echo "<td class='center'>" . $tidakJawab . "</td>";
        echo "<td class='center'><b>" . number_format($nilai, 1) . "</b></td>";
        echo "<td class='center'>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></body></html>";
    exit;
}

include __DIR__ . '/../resources/views/cbt/cetak_hasil.php';
