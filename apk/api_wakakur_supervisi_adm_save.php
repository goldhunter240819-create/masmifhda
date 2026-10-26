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
    $catatan = $_POST['catatan'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    
    $instrumen = [
        'cp' => (int)($_POST['instrumen']['cp'] ?? 0),
        'atp' => (int)($_POST['instrumen']['atp'] ?? 0),
        'modul' => (int)($_POST['instrumen']['modul'] ?? 0),
        'kktp' => (int)($_POST['instrumen']['kktp'] ?? 0),
        'prota' => (int)($_POST['instrumen']['prota'] ?? 0),
        'promes' => (int)($_POST['instrumen']['promes'] ?? 0),
        'absen' => (int)($_POST['instrumen']['absen'] ?? 0),
        'jurnal' => (int)($_POST['instrumen']['jurnal'] ?? 0),
        'daftar_nilai' => (int)($_POST['instrumen']['daftar_nilai'] ?? 0)
    ];
    
    // Calculate final score
    $total_skor = array_sum($instrumen);
    $max_skor = count($instrumen) * 4;
    $nilai_akhir = $max_skor > 0 ? round(($total_skor / $max_skor) * 100) : 0;
    
    $instrumen_json = json_encode($instrumen);
    
    $penilai_id = $_SESSION['guru_id'];
    $stmt_penilai = $core_db->prepare("SELECT nama FROM guru WHERE id = ?");
    $stmt_penilai->execute([$penilai_id]);
    $penilai_nama = $stmt_penilai->fetchColumn() ?: 'Admin/Penilai';

    $stmt = $core_db->prepare("
        INSERT INTO kurikulum_supervisi_administrasi 
        (tahun_ajaran_id, guru_id, tanggal_supervisi, instrumen_json, nilai_akhir, catatan, tindak_lanjut, penilai_id, penilai_nama) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        tanggal_supervisi = VALUES(tanggal_supervisi),
        instrumen_json = VALUES(instrumen_json),
        nilai_akhir = VALUES(nilai_akhir),
        catatan = VALUES(catatan),
        tindak_lanjut = VALUES(tindak_lanjut),
        penilai_id = VALUES(penilai_id),
        penilai_nama = VALUES(penilai_nama)
    ");
    
    $stmt->execute([
        $tahun_ajaran_id, $guru_id, $tanggal_supervisi, $instrumen_json, 
        $nilai_akhir, $catatan, $tindak_lanjut, $penilai_id, $penilai_nama
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Supervisi administrasi berhasil disimpan!']);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
