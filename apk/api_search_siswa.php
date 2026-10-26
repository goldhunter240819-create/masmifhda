<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($keyword) || strlen($keyword) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $module = isset($_GET['module']) ? $_GET['module'] : '';
    $user_id = $_SESSION['user_id'] ?? 0;
    $role_id = $_SESSION['role_id'] ?? 3;

    $query = "
        SELECT s.id, s.nama, s.nis, s.foto, COALESCE(k.nama_kelas, '-') as nama_kelas 
        FROM siswa s 
        LEFT JOIN kelas k ON k.id = s.kelas_id
        WHERE (s.nama LIKE :kw OR s.nis LIKE :kw) 
        ORDER BY COALESCE(k.nama_kelas, '-') ASC, s.nama ASC 
        LIMIT 30
    ";
    
    $stmt = $core_db->prepare($query);
    $stmt->execute([
        ':kw' => '%' . $keyword . '%'
    ]);
    
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($siswa);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
