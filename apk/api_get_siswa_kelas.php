<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;

if ($kelas_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    // Cari tahun ajaran aktif
    $stmt_ta = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    $ta = $stmt_ta->fetch();
    $ta_id = $ta ? $ta['id'] : 0;

    $query = "
        SELECT s.id, s.nama, s.nis, s.foto 
        FROM riwayat_kelas_siswa r 
        JOIN siswa s ON r.siswa_id = s.id 
        WHERE r.kelas_id = :kelas_id 
    ";
    
    $params = [':kelas_id' => $kelas_id];

    if ($ta_id > 0) {
        $query .= " AND r.tahun_ajaran_id = :ta_id ";
        $params[':ta_id'] = $ta_id;
    }

    $query .= " ORDER BY s.nama ASC";

    $stmt = $core_db->prepare($query);
    $stmt->execute($params);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($siswa);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
