<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_guru = $_SESSION['nama'] ?? 'Guru';

$tanggal_izin = $_POST['tanggal_izin'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$jadwal_ids = $_POST['jadwal_id'] ?? [];
$kelas_namas = $_POST['kelas_nama'] ?? [];
$mapel_namas = $_POST['mapel_nama'] ?? [];
$tugas = $_POST['tugas'] ?? [];
$id = $_POST['id'] ?? 0;

if (empty($tanggal_izin) || empty($keterangan)) {
    echo json_encode(['success' => false, 'message' => 'Tanggal dan keterangan wajib diisi']);
    exit;
}

// Susun array tugas
$tugas_arr = [];
if (is_array($jadwal_ids) && count($jadwal_ids) > 0) {
    foreach ($jadwal_ids as $jid) {
        $tugas_arr[] = [
            'jadwal_id' => $jid,
            'kelas' => $kelas_namas[$jid] ?? '',
            'mapel' => $mapel_namas[$jid] ?? '',
            'tugas' => $tugas[$jid] ?? ''
        ];
    }
}
$tugas_json = json_encode($tugas_arr);

try {
    if ($id > 0) {
        // Update mode
        $stmt = $siakad_db->prepare("UPDATE ajuan_izin_guru SET tanggal_izin = :tgl, keterangan = :ket, tugas_mapel = :tugas WHERE id = :id AND guru_id = :guru_id AND status = 'Pending'");
        $stmt->execute([
            'id' => $id,
            'guru_id' => $guru_id,
            'tgl' => $tanggal_izin,
            'ket' => $keterangan,
            'tugas' => $tugas_json
        ]);
        echo json_encode(['success' => true, 'message' => 'Pengajuan berhasil diperbarui']);
    } else {
        // Insert mode
        $stmt = $siakad_db->prepare("INSERT INTO ajuan_izin_guru (guru_id, tanggal_izin, keterangan, tugas_mapel, status, created_at) VALUES (:guru_id, :tgl, :ket, :tugas, 'Pending', NOW())");
        $stmt->execute([
            'guru_id' => $guru_id,
            'tgl' => $tanggal_izin,
            'ket' => $keterangan,
            'tugas' => $tugas_json
        ]);

        // Kirim notifikasi OneSignal ke Admin Izin
        $stmt_admin = $core_db->query("SELECT g.user_id FROM apk_akses_guru a JOIN guru g ON a.guru_id = g.id WHERE a.app_code = 'admin_izin' AND g.user_id IS NOT NULL");
        $admins = $stmt_admin->fetchAll(PDO::FETCH_COLUMN);

        if (count($admins) > 0) {
            $target_user_ids = [];
            foreach ($admins as $ad) {
                if (!empty($ad)) $target_user_ids[] = (string)$ad;
            }
            
            if (count($target_user_ids) > 0) {
                $tgl_indo = date('d-m-Y', strtotime($tanggal_izin));
                $title = "Pengajuan Izin Guru";
                $message = "Guru {$nama_guru} mengajukan izin untuk tanggal {$tgl_indo} ({$keterangan}).";
                if (function_exists('sendOneSignalNotification')) {
                    sendOneSignalNotification($target_user_ids, $title, $message);
                }
            }
        }
        echo json_encode(['success' => true, 'message' => 'Pengajuan berhasil dikirim']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
