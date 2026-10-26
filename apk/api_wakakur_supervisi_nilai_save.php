<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) { 
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
    $inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
    $tahun_ajaran_id = $inst['tahun_ajaran_id'] ?? 1;
    $semester = $inst['semester'] ?? 'Ganjil';
    
    $guru_id = $_POST['guru_id'] ?? 0;
    $kelas_id = $_POST['kelas_id'] ?? 0;
    $mapel_id = $_POST['mapel_id'] ?? 0;
    $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
    
    $ada_kisi = isset($_POST['ada_kisi']) ? 1 : 0;
    $ada_analisis = isset($_POST['ada_analisis']) ? 1 : 0;
    $ada_remedial = isset($_POST['ada_remedial']) ? 1 : 0;
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    
    // Supervisor id is the current logged in Wakakur
    $disupervisi_oleh = $_SESSION['guru_id'];
    
    $stmt = $core_db->prepare("
        INSERT INTO kurikulum_supervisi_nilai 
        (tahun_ajaran_id, semester, guru_id, kelas_id, mapel_id, jenis_evaluasi, ada_kisi, ada_analisis, ada_remedial, catatan, tindak_lanjut, disupervisi_oleh) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        ada_kisi = VALUES(ada_kisi),
        ada_analisis = VALUES(ada_analisis),
        ada_remedial = VALUES(ada_remedial),
        catatan = VALUES(catatan),
        tindak_lanjut = VALUES(tindak_lanjut),
        disupervisi_oleh = VALUES(disupervisi_oleh)
    ");
    
    $stmt->execute([
        $tahun_ajaran_id, $semester, $guru_id, $kelas_id, $mapel_id, $jenis_evaluasi, 
        $ada_kisi, $ada_analisis, $ada_remedial, $catatan, $tindak_lanjut, $disupervisi_oleh
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Supervisi penilaian berhasil disimpan!']);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
