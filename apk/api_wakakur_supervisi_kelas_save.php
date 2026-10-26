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
    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;
    
    $guru_id = $_POST['guru_id'] ?? null;
    $tanggal_supervisi = $_POST['tanggal_supervisi'] ?? date('Y-m-d');
    $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $jam_ke = $_POST['jam_ke'] ?? '';
    $materi_pokok = $_POST['materi_pokok'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    
    // Check if instrumen is provided
    $instrumen = [];
    if (isset($_POST['instrumen']) && is_array($_POST['instrumen'])) {
        foreach ($_POST['instrumen'] as $k => $v) {
            $instrumen[$k] = (int)$v;
        }
    }
    
    $instrumen_json = json_encode($instrumen);
    
    // Supervisor id is the current logged in Wakakur
    $supervisor_id = $_SESSION['guru_id'];
    
    $stmt = $core_db->prepare("
        INSERT INTO kurikulum_supervisi_kelas 
        (tahun_ajaran_id, guru_id, tanggal_supervisi, mata_pelajaran, kelas, jam_ke, materi_pokok, instrumen_json, catatan, tindak_lanjut, supervisor_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        tanggal_supervisi = VALUES(tanggal_supervisi),
        mata_pelajaran = VALUES(mata_pelajaran),
        kelas = VALUES(kelas),
        jam_ke = VALUES(jam_ke),
        materi_pokok = VALUES(materi_pokok),
        instrumen_json = VALUES(instrumen_json),
        catatan = VALUES(catatan),
        tindak_lanjut = VALUES(tindak_lanjut),
        supervisor_id = VALUES(supervisor_id)
    ");
    
    $stmt->execute([
        $tahun_ajaran_id, $guru_id, $tanggal_supervisi, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, 
        $instrumen_json, $catatan, $tindak_lanjut, $supervisor_id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Supervisi kelas berhasil disimpan!']);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
