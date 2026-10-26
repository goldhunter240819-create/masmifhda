<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$guru_id = $_SESSION['guru_id'];

// Ambil tahun ajaran aktif
$institusi = [];
try {
    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $institusi = $stmt_inst->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 1;
$semester = $institusi['semester'] == 'Genap' ? 2 : 1;

header('Content-Type: application/json');

if ($action === 'mapel') {
    $kelas_id = intval($_GET['kelas_id'] ?? 0);
    if ($kelas_id <= 0) {
        echo json_encode([]);
        exit;
    }
    
    try {
        $stmt = $siakad_db->prepare("
            SELECT DISTINCT m.id, m.nama_mapel 
            FROM jadwal_pelajaran j 
            JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
            WHERE j.guru_id = ? AND j.tahun_ajaran_id = ? AND j.kelas_id = ?
            ORDER BY m.nama_mapel ASC
        ");
        // Catatan: di jadwal_pelajaran, kolomnya mata_pelajaran_id (sesuai struktur umum masmifhda)
        $stmt->execute([$guru_id, $tahun_ajaran_id, $kelas_id]);
        $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Coba pakai mapel_id jika query di atas error/kosong karena nama kolom berbeda
        if (empty($mapel)) {
            $stmt2 = $siakad_db->prepare("
                SELECT DISTINCT m.id, m.nama_mapel 
                FROM jadwal_pelajaran j 
                JOIN mapel m ON j.mapel_id = m.id 
                WHERE j.guru_id = ? AND j.tahun_ajaran_id = ? AND j.kelas_id = ?
                ORDER BY m.nama_mapel ASC
            ");
            $stmt2->execute([$guru_id, $tahun_ajaran_id, $kelas_id]);
            $mapel = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($mapel);
    } catch (Exception $e) {
        echo json_encode([]);
    }
    exit;
}

if ($action === 'evaluasi') {
    $kelas_id = intval($_GET['kelas_id'] ?? 0);
    $mapel_id = intval($_GET['mapel_id'] ?? 0);
    
    if ($kelas_id <= 0 || $mapel_id <= 0) {
        echo json_encode([]);
        exit;
    }
    
    try {
        // Cek evaluasi apa saja yang sudah ada untuk PH
        $stmt = $core_db->prepare("
            SELECT DISTINCT jenis_evaluasi 
            FROM nilai_harian 
            WHERE guru_id = ? AND tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ?
        ");
        $stmt->execute([$guru_id, $tahun_ajaran_id, $kelas_id, $mapel_id]);
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $ph_count = 0;
        foreach ($existing as $ev) {
            if (preg_match('/^PH\s*(\d+)$/i', $ev, $matches)) {
                $num = intval($matches[1]);
                if ($num > $ph_count) $ph_count = $num;
            }
        }
        
        $next_ph = $ph_count + 1;
        
        $response = [
            ['value' => 'PH ' . $next_ph, 'label' => 'Penilaian Harian (Auto PH ' . $next_ph . ')'],
            ['value' => 'PTS', 'label' => 'Penilaian Tengah Semester (PTS)'],
            ['value' => 'PAS', 'label' => 'Penilaian Akhir Semester (PAS)'],
        ];
        
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            ['value' => 'PH 1', 'label' => 'Penilaian Harian (Auto PH 1)'],
            ['value' => 'PTS', 'label' => 'Penilaian Tengah Semester (PTS)'],
            ['value' => 'PAS', 'label' => 'Penilaian Akhir Semester (PAS)'],
        ]);
    }
    exit;
}

echo json_encode(['error' => 'Invalid action']);
