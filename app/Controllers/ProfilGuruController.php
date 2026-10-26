<?php

namespace App\Controllers;

use App\Core\Database;

class ProfilGuruController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/');
            exit;
        }

        $db = Database::connect('core');
        $user_id = $_SESSION['user_id'];
        
        $guru = $db->query("SELECT g.*, u.username, u.plain_password FROM guru g JOIN users u ON g.user_id = u.id WHERE u.id = $user_id")->fetch();

        // If for some reason guru profile doesn't exist, create a dummy array
        if (!$guru) {
            $guru = [
                'nama' => isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : ''),
                'nip' => '-',
                'nik' => '-',
                'tempat_lahir' => '',
                'tanggal_lahir' => '',
                'jenis_kelamin' => 'L',
                'alamat' => '',
                'no_hp' => '',
                'email' => '',
                'jabatan' => 'Guru',
                'foto' => '',
                'status_kepegawaian' => 'Honorer',
                'rt' => '', 'rw' => '', 'desa' => '', 'kecamatan' => '', 'kota' => '', 'provinsi' => '', 'kode_pos' => '',
                'npk' => '', 'npwp' => '', 'no_rekening' => '', 'nrg' => '', 'no_peserta_sertifikasi' => '', 'no_sertifikat' => '', 
                'tgl_sertifikat' => '', 'jenjang_sertifikat' => '', 'mapel_sertifikat' => '',
                'username' => $_SESSION['username'],
                'plain_password' => '********'
            ];
        }

        $dbS = Database::connect('siakad');
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $daftarMengajar = [];
        if ($guru) {
            $daftarMengajar = $dbS->query("SELECT ma.nama_mapel, ma.kode_mapel, k.nama_kelas, ta.name as tahun_pelajaran
                                          FROM mengajar m 
                                          JOIN mapel ma ON m.mapel_id = ma.id 
                                          JOIN aula_db.kelas k ON m.kelas_id = k.id 
                                          JOIN aula_db.tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                                          WHERE m.guru_id = " . $guru['id'] . " AND ta.name = '$active_year_name'
                                          GROUP BY ma.id, k.id
                                          ORDER BY k.nama_kelas ASC, ma.nama_mapel ASC")->fetchAll();
        }

        $title = "Profil Saya - GH-SSS";
        ob_start();
        include __DIR__ . '/../../resources/views/portal/profil_guru.php';
        echo ob_get_clean();
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
                redirect('/');
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $db = Database::connect('core');
            $guru = $db->query("SELECT * FROM guru WHERE user_id = $user_id")->fetch();
            
            $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
            $nip = isset($_POST['nip']) ? $_POST['nip'] : '';
            $nik = isset($_POST['nik']) ? $_POST['nik'] : '';
            $tempat_lahir = isset($_POST['tempat_lahir']) ? $_POST['tempat_lahir'] : '';
            $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
            $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
            $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
            $no_hp = isset($_POST['no_hp']) ? $_POST['no_hp'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            
            $rt = isset($_POST['rt']) ? $_POST['rt'] : '';
            $rw = isset($_POST['rw']) ? $_POST['rw'] : '';
            $desa = isset($_POST['desa']) ? $_POST['desa'] : '';
            $kecamatan = isset($_POST['kecamatan']) ? $_POST['kecamatan'] : '';
            $kota = isset($_POST['kota']) ? $_POST['kota'] : '';
            $provinsi = isset($_POST['provinsi']) ? $_POST['provinsi'] : '';
            $kode_pos = isset($_POST['kode_pos']) ? $_POST['kode_pos'] : '';
            
            $npk = isset($_POST['npk']) ? $_POST['npk'] : '';
            $npwp = isset($_POST['npwp']) ? $_POST['npwp'] : '';
            $no_rekening = isset($_POST['no_rekening']) ? $_POST['no_rekening'] : '';
            $nrg = isset($_POST['nrg']) ? $_POST['nrg'] : '';
            $no_peserta_sertifikasi = isset($_POST['no_peserta_sertifikasi']) ? $_POST['no_peserta_sertifikasi'] : '';
            $no_sertifikat = isset($_POST['no_sertifikat']) ? $_POST['no_sertifikat'] : '';
            $tgl_sertifikat = !empty($_POST['tgl_sertifikat']) ? $_POST['tgl_sertifikat'] : null;
            $jenjang_sertifikat = isset($_POST['jenjang_sertifikat']) ? $_POST['jenjang_sertifikat'] : '';
            $mapel_sertifikat = isset($_POST['mapel_sertifikat']) ? $_POST['mapel_sertifikat'] : '';
            
            // Handle Foto Upload
            $foto_name = $guru ? $guru['foto'] : '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "guru_" . time() . "." . $ext;
                if(!is_dir(__DIR__ . "/../../public/uploads/guru")) {
                    mkdir(__DIR__ . "/../../public/uploads/guru", 0777, true);
                }
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/guru/" . $foto_name);
            }

            if ($guru) {
                $stmt = $db->prepare("UPDATE guru SET nama = ?, nip = ?, nik = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, email = ?, foto = ?, rt = ?, rw = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kode_pos = ?, npk = ?, npwp = ?, no_rekening = ?, nrg = ?, no_peserta_sertifikasi = ?, no_sertifikat = ?, tgl_sertifikat = ?, jenjang_sertifikat = ?, mapel_sertifikat = ? WHERE user_id = ?");
                $stmt->execute([$nama, $nip, $nik, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $email, $foto_name, $rt, $rw, $desa, $kecamatan, $kota, $provinsi, $kode_pos, $npk, $npwp, $no_rekening, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat, $user_id]);
            } else {
                // If it doesn't exist, insert it (unlikely but safe fallback)
                $stmt = $db->prepare("INSERT INTO guru (user_id, nama, nip, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_hp, email, foto, rt, rw, desa, kecamatan, kota, provinsi, kode_pos, npk, npwp, no_rekening, nrg, no_peserta_sertifikasi, no_sertifikat, tgl_sertifikat, jenjang_sertifikat, mapel_sertifikat, jabatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Guru')");
                $stmt->execute([$user_id, $nama, $nip, $nik, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $email, $foto_name, $rt, $rw, $desa, $kecamatan, $kota, $provinsi, $kode_pos, $npk, $npwp, $no_rekening, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat]);
            }

            // Update session name if changed
            $_SESSION['nama'] = $nama;

            redirect('/guru/profil-guru');
            exit;
        }
    }
}
