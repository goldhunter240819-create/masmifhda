<?php
include '../includes/koneksi.php';

// Matikan error reporting agar tidak merusak JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if (isset($_POST['qr_code']) || isset($_POST['user_id'])) {
    $qr_code_raw = isset($_POST['qr_code']) ? $_POST['qr_code'] : null;
    $user_id_param = isset($_POST['user_id']) ? mysqli_real_escape_string($koneksi, $_POST['user_id']) : null;

    // Parser QR Siakad (Support multiline data) - DO BEFORE ESCAPING
    if ($qr_code_raw && preg_match('/(IDENTITAS GURU|NIS\/NISN|NIP:)/i', $qr_code_raw)) {
        if (preg_match('/NIP:\s*(.*)/i', $qr_code_raw, $matches)) {
            // Format Guru: NIP: [NIP]
            $qr_code_raw = trim($matches[1]);
        } elseif (preg_match('/NIS\/NISN:\s*(.*)/i', $qr_code_raw, $matches)) {
            // Format Siswa: NIS/NISN: [NISN]/[NIS]
            $parts = explode("/", $matches[1]);
            $qr_code_raw = trim(end($parts)); // Ambil bagian terakhir (NIS)
        }
    }
    
    $qr_code = mysqli_real_escape_string($koneksi, $qr_code_raw);
    
    // Ambil Pengaturan Jam
    $peng_res = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
    if(mysqli_num_rows($peng_res) > 0) {
        $peng = mysqli_fetch_assoc($peng_res);
    } else {
        $peng = ['mulai_masuk'=>'00:00:00','akhir_masuk'=>'13:00:00','mulai_keluar'=>'14:00:00','akhir_keluar'=>'23:59:00'];
    }

    $waktu_sekarang_str = date('H:i:s');
    $waktu_sekarang_ts = strtotime($waktu_sekarang_str);
    
    $m_m = strtotime($peng['mulai_masuk']);
    $a_m = strtotime($peng['akhir_masuk']);
    $m_k = strtotime($peng['mulai_keluar']);
    $a_k = strtotime($peng['akhir_keluar']);

    $mode = "";
    $msg_error_time = "";

    if ($waktu_sekarang_ts >= $m_m && $waktu_sekarang_ts <= $a_m) {
        $mode = "masuk";
    } elseif ($waktu_sekarang_ts >= $m_k && $waktu_sekarang_ts <= $a_k) {
        $mode = "keluar";
    } elseif ($waktu_sekarang_ts > $a_m && $waktu_sekarang_ts < $m_k) {
        $mode = "jeda";
        $msg_error_time = "Waktu yang ditentukan tidak sesuai.";
    } elseif ($waktu_sekarang_ts > $a_k) {
        $mode = "tutup";
        $msg_error_time = "Absen telah ditutup.";
    } else {
        $mode = "belum_buka";
        $msg_error_time = "Waktu yang ditentukan tidak sesuai.";
    }

    // Cari identifier user
    if ($user_id_param) {
        $query_user = "SELECT * FROM users WHERE id = '$user_id_param'";
    } else {
        $query_user = "SELECT * FROM users WHERE qr_code = '$qr_code'";
    }
    $result_user = mysqli_query($koneksi, $query_user);
    
    if (mysqli_num_rows($result_user) === 1) {
        $user = mysqli_fetch_assoc($result_user);
        $user_id = $user['id'];
        $tipe = $user['tipe'];
        $nama = $user['nama'];
        $tanggal = date('Y-m-d');
        
        $hari_ini_ind = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $nama_hari = $hari_ini_ind[date('N')];
        $status_absen = "Hadir";

        if ($tipe == 'guru') {
            $cek_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE user_id = '$user_id' AND hari = '$nama_hari'");
            if (mysqli_num_rows($cek_jadwal) == 0) {
                $status_absen = "Tidak Ada Jam";
            }
        }

        if ($mode == "jeda" || $mode == "tutup" || $mode == "belum_buka") {
            echo json_encode(['status' => 'error_time', 'message' => $msg_error_time]);
            exit;
        }
        
        $query_cek = "SELECT * FROM absensi WHERE user_id = '$user_id' AND tanggal = '$tanggal'";
        $result_cek = mysqli_query($koneksi, $query_cek);
        $absen_hrini = mysqli_fetch_assoc($result_cek);
        
        if ($mode == "masuk") {
            if (!$absen_hrini) {
                $q_in = "INSERT INTO absensi (user_id, tipe, tanggal, jam_masuk, status) VALUES ('$user_id', '$tipe', '$tanggal', '$waktu_sekarang_str', '$status_absen')";
                if (mysqli_query($koneksi, $q_in)) {
                    echo json_encode(['status' => 'success_masuk', 'message' => 'Presensi masuk dicatat.', 'nama' => $nama, 'status_absen' => $status_absen]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal mencatat masuk.']);
                }
            } else {
                echo json_encode(['status' => 'info_masuk', 'message' => 'Sudah absen masuk.', 'nama' => $nama]);
            }
        } elseif ($mode == "keluar") {
            if (!$absen_hrini) {
                 $q_in = "INSERT INTO absensi (user_id, tipe, tanggal, jam_keluar, status) VALUES ('$user_id', '$tipe', '$tanggal', '$waktu_sekarang_str', '$status_absen')";
                 mysqli_query($koneksi, $q_in);
                 echo json_encode(['status' => 'success_keluar', 'message' => 'Presensi pulang dicatat. (Abaikan Masuk)', 'nama' => $nama, 'status_absen' => $status_absen]);
            } else {
                 if (empty($absen_hrini['jam_keluar']) || $absen_hrini['jam_keluar'] == "00:00:00" || $absen_hrini['jam_keluar'] == null) {
                     $id_absen = $absen_hrini['id'];
                     mysqli_query($koneksi, "UPDATE absensi SET jam_keluar='$waktu_sekarang_str' WHERE id='$id_absen'");
                     echo json_encode(['status' => 'success_keluar', 'message' => 'Presensi pulang dicatat.', 'nama' => $nama, 'status_absen' => $status_absen]);
                 } else {
                     echo json_encode(['status' => 'info_keluar', 'message' => 'Sudah absen pulang.', 'nama' => $nama]);
                 }
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Identitas tidak ditemukan: ' . $qr_code]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payload kosong.']);
}
?>
