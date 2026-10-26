<?php

namespace App\Controllers;

use App\Core\Database;

class ProfilSiswaController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
            redirect('/');
            exit;
        }

        $db = Database::connect('core');
        $user_id = $_SESSION['user_id'];
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $siswa = $db->query("SELECT s.*, u.username, u.plain_password, k.nama_kelas 
                             FROM siswa s 
                             JOIN users u ON s.user_id = u.id 
                             LEFT JOIN kelas k ON s.kelas_id = k.id 
                             WHERE u.id = $user_id AND s.tahun_ajaran = '$active_year_name'")->fetch();

        if (!$siswa) {
            redirect('/');
            exit;
        }

        $title = "Profil Saya - GH-SSS";
        ob_start();
        include __DIR__ . '/../../resources/views/portal/profil_siswa.php';
        echo ob_get_clean();
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
                redirect('/');
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $db = Database::connect('core');
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            $siswa = $db->query("SELECT * FROM siswa WHERE user_id = $user_id AND tahun_ajaran = '$active_year_name'")->fetch();
            
            if (!$siswa) exit;

            $data = $_POST;
            
            // Handle Foto Upload
            $foto_name = $siswa['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "siswa_" . time() . "." . $ext;
                if(!is_dir(__DIR__ . "/../../public/uploads/siswa")) {
                    mkdir(__DIR__ . "/../../public/uploads/siswa", 0777, true);
                }
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/siswa/" . $foto_name);
            }

            $sql = "UPDATE siswa SET 
                    nama = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, 
                    alamat = ?, no_hp_ortu = ?, email = ?, foto = ?,
                    nik = ?, no_kk = ?, agama = ?, gol_darah = ?, anak_ke = ?,
                    nama_ayah = ?, pekerjaan_ayah = ?, nama_ibu = ?, pekerjaan_ibu = ?,
                    penghasilan_ortu = ?, nama_wali = ?, pekerjaan_wali = ?, no_hp_wali = ?,
                    rt = ?, rw = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kode_pos = ?,
                    sekolah_asal = ?, pendidikan_terakhir = ?, no_ijazah = ?
                    WHERE user_id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['nama'], $data['tempat_lahir'], $data['tanggal_lahir'], 
                $data['jenis_kelamin'], $data['alamat'], $data['no_hp_ortu'], 
                isset($data['email']) ? $data['email'] : '', $foto_name,
                $data['nik'] ?? '', $data['no_kk'] ?? '', $data['agama'] ?? '', $data['gol_darah'] ?? '', $data['anak_ke'] ?? '',
                $data['nama_ayah'] ?? '', $data['pekerjaan_ayah'] ?? '', $data['nama_ibu'] ?? '', $data['pekerjaan_ibu'] ?? '',
                $data['penghasilan_ortu'] ?? '', $data['nama_wali'] ?? '', $data['pekerjaan_wali'] ?? '', $data['no_hp_wali'] ?? '',
                $data['rt'] ?? '', $data['rw'] ?? '', $data['desa'] ?? '', $data['kecamatan'] ?? '', $data['kota'] ?? '', $data['provinsi'] ?? '', $data['kode_pos'] ?? '',
                $data['sekolah_asal'] ?? '', $data['pendidikan_terakhir'] ?? '', $data['no_ijazah'] ?? '',
                $user_id
            ]);

            $_SESSION['nama'] = $data['nama'];

            redirect('/siswa/profil-siswa');
            exit;
        }
    }
}
