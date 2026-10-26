<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadMapelController {

    public function mapel() {
        $db = Database::connect('siakad');
        $mapel = $db->query("SELECT id, nama_mapel, kode_mapel, 'Umum' as kategori FROM mata_pelajaran ORDER BY nama_mapel ASC")->fetchAll();

        $title = "Manajemen Mata Pelajaran - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/mapel.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function addMapel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['nama_mapel'];
            $code = $_POST['kode_mapel'];
            $kategori = $_POST['kategori'] ?? 'Umum';
            $db = Database::connect('siakad');
            $stmt = $db->prepare("INSERT INTO mata_pelajaran (nama_mapel, kode_mapel, kelompok) VALUES (?, ?, 'A')");
            $stmt->execute([$name, $code]);
        }
        redirect('/siakad/mapel');
    }


    public function updateMapel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['nama_mapel'];
            $code = $_POST['kode_mapel'];
            $kategori = $_POST['kategori'] ?? 'Umum';
            $db = Database::connect('siakad');
            $stmt = $db->prepare("UPDATE mata_pelajaran SET nama_mapel = ?, kode_mapel = ? WHERE id = ?");
            $stmt->execute([$name, $code, $id]);
        }
        redirect('/siakad/mapel');
    }


    public function deleteMapel($id) {
        $db = Database::connect('siakad');
        $db->exec("DELETE FROM mata_pelajaran WHERE id = $id");
        redirect('/siakad/mapel');
    }

}
