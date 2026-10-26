<?php
/**
 * save_kwitansi.php
 * Menerima data gambar base64 dari html2canvas (POST),
 * menyimpannya sebagai file PNG di server,
 * dan mengembalikan URL-nya untuk dibuka di WebView.
 */

require_once 'koneksi.php';

// Hanya izinkan POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$is_siswa = isset($_SESSION['siswa_id']);
$is_guru = isset($_SESSION['guru_id']);

// Wajib login
if (!$is_siswa && !$is_guru) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Tidak terautentikasi.']);
    exit;
}

$id      = isset($_POST['id']) ? intval($_POST['id']) : 0;
$imageData = isset($_POST['image']) ? $_POST['image'] : '';

if ($id <= 0 || empty($imageData)) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
    exit;
}

// Validasi kepemilikan: pastikan pembayaran milik siswa yang login (jika siswa)
try {
    if ($is_siswa && !$is_guru) {
        $stmt = $core_db->prepare("SELECT id FROM keuangan_komite_pembayaran WHERE id = :id AND siswa_id = :siswa_id LIMIT 1");
        $stmt->execute(['id' => $id, 'siswa_id' => $_SESSION['siswa_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            exit;
        }
    } else {
        $stmt = $core_db->prepare("SELECT id FROM keuangan_komite_pembayaran WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
            exit;
        }
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan database.']);
    exit;
}

// Decode base64 image
$imageData = preg_replace('/^data:image\/png;base64,/', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);
$decoded   = base64_decode($imageData);

if ($decoded === false || strlen($decoded) < 100) {
    echo json_encode(['status' => 'error', 'message' => 'Data gambar tidak valid.']);
    exit;
}

// Simpan ke folder sementara yang bisa diakses publik
$saveDir = __DIR__ . '/../storage/kwitansi_tmp/';
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

$identifier = $is_siswa ? $_SESSION['siswa_id'] : 'admin_' . $_SESSION['guru_id'];

// Bersihkan file lama milik user ini
$pattern = $saveDir . 'kwitansi_' . $identifier . '_*.png';
foreach (glob($pattern) as $oldFile) {
    @unlink($oldFile);
}

// Ambil nama siswa untuk dipakai di nama file
$namaSiswa = 'Siswa';
try {
    $stmtNama = $core_db->prepare("SELECT s.nama FROM keuangan_komite_pembayaran p JOIN siswa s ON p.siswa_id = s.id WHERE p.id = :id LIMIT 1");
    $stmtNama->execute(['id' => $id]);
    $namaRow  = $stmtNama->fetch(PDO::FETCH_ASSOC);
    if ($namaRow) {
        $namaSiswa = preg_replace('/[^a-zA-Z0-9\s]/', '', $namaRow['nama']);
        $namaSiswa = trim(preg_replace('/\s+/', '_', $namaSiswa));
        if (empty($namaSiswa)) $namaSiswa = 'Siswa';
    }
} catch (PDOException $e) { /* biarkan default */ }

$filename = 'kwitansi_' . $identifier
          . '_KW' . str_pad($id, 5, '0', STR_PAD_LEFT)
          . '_' . $namaSiswa
          . '_' . time() . '.png';
$filePath = $saveDir . $filename;

if (file_put_contents($filePath, $decoded) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file di server.']);
    exit;
}

$publicUrl = '/storage/kwitansi_tmp/' . $filename;

// HMAC token agar download_kwitansi.php bisa diakses tanpa session
// (Android download manager tidak membawa cookie WebView)
$dlSecret = 'MismifhdaDL_S3cr3t_2026';
$dlToken  = substr(hash_hmac('sha256', $filename, $dlSecret), 0, 24);

echo json_encode([
    'status'   => 'success',
    'filename' => $filename,
    'token'    => $dlToken,
    'url'      => $publicUrl,
    'message'  => 'Kwitansi berhasil disimpan.'
]);
