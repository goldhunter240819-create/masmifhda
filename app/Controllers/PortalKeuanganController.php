<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PortalKeuanganController {

    public static function index() {
        $self = new self();
        $db = Database::connect('core');
        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];
        
        $data_keuangan = [];
        
        if ($role_id == 2) {
            // Guru: Sementara masih dummy karena tabel penggajian lengkap belum dibuat
            $data_keuangan = [
                'total_gaji' => 4500000,
                'insentif' => 500000,
                'potongan' => 150000,
                'history' => [
                    ['bulan' => 'Mei 2026', 'total' => 4850000, 'status' => 'Proses'],
                    ['bulan' => 'April 2026', 'total' => 4850000, 'status' => 'Dibayar'],
                ]
            ];
        } elseif ($role_id == 3) {
            // Siswa: Membaca tagihan asli dari database
            $siswa = $db->query("SELECT id FROM siswa WHERE user_id = $user_id")->fetch();
            $siswa_id = $siswa ? $siswa['id'] : 0;

            // Hitung total tagihan
            $tagihan = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = $siswa_id ORDER BY jatuh_tempo DESC")->fetchAll();
            $total_tagihan = 0;
            $terbayar = 0;
            $history = [];

            foreach ($tagihan as $t) {
                $total_tagihan += $t['jumlah_tagihan'];
                $terbayar += $t['jumlah_terbayar'];
                if ($t['status'] === 'Lunas') {
                    $terbayar += $t['jumlah_tagihan']; // safety
                }
            }

            // Ambil history transaksi pembayaran
            $transaksi = $db->query("SELECT * FROM keuangan_transaksi WHERE siswa_id = $siswa_id ORDER BY tanggal_bayar DESC LIMIT 10")->fetchAll();
            foreach ($transaksi as $tr) {
                $history[] = [
                    'tgl' => $tr['tanggal_bayar'],
                    'ket' => $tr['keterangan'],
                    'jml' => $tr['jumlah'],
                    'status' => 'Lunas'
                ];
            }

            // Jika belum ada history, fallback tampilkan dari tagihan saja
            if (empty($history)) {
                foreach ($tagihan as $t) {
                    $history[] = [
                        'tgl' => $t['jatuh_tempo'],
                        'ket' => $t['nama_tagihan'],
                        'jml' => $t['jumlah_tagihan'],
                        'status' => $t['status']
                    ];
                }
            }

            $data_keuangan = [
                'total_tagihan' => $total_tagihan,
                'terbayar' => $terbayar,
                'sisa' => max(0, $total_tagihan - $terbayar),
                'history' => $history
            ];
        }

        $self->render('Keuangan Saya', ['finance' => $data_keuangan]);
    }

    private function render($title_page, $extra_data = []) {
        $db = Database::connect('core');
        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];
        
        $foto = '';
        if ($role_id == 2) {
            $data = $db->query("SELECT foto FROM guru WHERE user_id = $user_id")->fetch();
            $foto = isset($data['foto']) ? $data['foto'] : '';
        } elseif ($role_id == 3) {
            $data = $db->query("SELECT foto FROM siswa WHERE user_id = $user_id")->fetch();
            $foto = isset($data['foto']) ? $data['foto'] : '';
        }

        $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
        $roleName = ($role_id == 2) ? "Guru / Tendik" : "Siswa";
        $profileUrl = ($role_id == 2) ? '/profil-guru' : '/profil-siswa';
        
        extract($extra_data);
        $title = "$title_page - GH-SSS";
        
        include __DIR__ . '/../../resources/views/portal/keuangan.php';
    }
}
