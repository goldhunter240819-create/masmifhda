<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadInstitusiController {

    public function institusi() {
        $db = Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        $title = "Profil Institusi - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/institusi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function updateInstitusi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $db = Database::connect('core');
            
            $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
            $logo_name = $institusi['logo'];

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                if(!is_dir(__DIR__ . '/../../public/uploads/logo')) mkdir(__DIR__ . '/../../public/uploads/logo', 0777, true);
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $logo_name = "logo_" . time() . "." . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . "/../../public/uploads/logo/" . $logo_name);
            }

            $sql = "UPDATE institusi SET 
                    nama = ?, npsn = ?, nsm = ?, alamat = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kodepos = ?,
                    telepon = ?, email = ?, website = ?,
                    nama_kepala = ?, nip_kepala = ?, logo = ?, tahun_ajaran_id = ?, semester = ?
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['nama'], $data['npsn'], $data['nsm'], $data['alamat'],
                isset($data['desa']) ? $data['desa'] : '',
                $data['kecamatan'], $data['kota'], $data['provinsi'],
                isset($data['kodepos']) ? $data['kodepos'] : '',
                isset($data['telepon']) ? $data['telepon'] : '',
                isset($data['email']) ? $data['email'] : '',
                isset($data['website']) ? $data['website'] : '',
                $data['nama_kepala'], $data['nip_kepala'], $logo_name,
                $data['tahun_ajaran_id'], $data['semester'], $institusi['id']
            ]);

            // Sync with tahun_ajaran table if necessary (Optional, but good for consistency)
            if ($data['tahun_ajaran_id']) {
                $db->exec("UPDATE tahun_ajaran SET is_active = 0");
                $db->exec("UPDATE tahun_ajaran SET is_active = 1 WHERE id = " . $data['tahun_ajaran_id']);
                
                // ALSO sync with siakad.pengaturan globally!
                $thn = $db->query("SELECT name FROM tahun_ajaran WHERE id = " . intval($data['tahun_ajaran_id']))->fetch();
                if ($thn) {
                    $dbSiakad = Database::connect('siakad');
                    $dbSiakad->prepare("UPDATE pengaturan SET tahun_ajaran_aktif = ?, semester_aktif = ?")->execute([$thn['name'], $data['semester']]);
                }
            }
        }
        redirect('/siakad/institusi');
    }


    public function tahunAjaran() {
        $db = Database::connect('core');
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        $title = "Manajemen Tahun Ajaran - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/tahun_ajaran.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function addTahunAjaran() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $db = Database::connect('core');
            $stmt = $db->prepare("INSERT INTO tahun_ajaran (name) VALUES (?)");
            $stmt->execute([$name]);
        }
        redirect('/siakad/tahun-ajaran');
    }


    public function activateTahunAjaran($id) {
        $db = Database::connect('core');
        $db->exec("UPDATE tahun_ajaran SET is_active = 0");
        $stmt = $db->prepare("UPDATE tahun_ajaran SET is_active = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        // ALSO sync with siakad.pengaturan globally!
        $thn = $db->query("SELECT name FROM tahun_ajaran WHERE id = " . intval($id))->fetch();
        if ($thn) {
            $dbSiakad = Database::connect('siakad');
            $pengaturan = $dbSiakad->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch();
            $semester = $pengaturan ? $pengaturan['semester_aktif'] : 'Ganjil';
            $dbSiakad->prepare("UPDATE pengaturan SET tahun_ajaran_aktif = ?")->execute([$thn['name']]);
        }
        
        redirect('/siakad/institusi');
    }

}
