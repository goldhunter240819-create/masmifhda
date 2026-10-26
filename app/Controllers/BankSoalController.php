<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class BankSoalController {
    
    private $db;

    public function __construct() {
        $this->db = Database::connect('siakad');
    }

    // --- MANAJEMEN TOPIK ---

    public function indexTopik() {
        $stmt = $this->db->query("
            SELECT t.*, (SELECT COUNT(id) FROM bs_soal WHERE topik_id = t.id) as jml_soal 
            FROM bs_topik t 
            ORDER BY t.created_at DESC
        ");
        $topiks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $page_title = "Kelola Topik Soal";
        $active_page = "topik";
        include __DIR__ . '/../../resources/views/bank_soal/topik.php';
    }

    public function simpanTopik() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $jenis = $_POST['jenis'] ?? '';
            $nama_topik = $_POST['nama_topik'] ?? '';
            $keterangan = $_POST['keterangan'] ?? '';
            $tahun_ajaran = $_SESSION['tahun_ajaran'] ?? '2026/2027';
            
            if ($id) {
                $stmt = $this->db->prepare("UPDATE bs_topik SET jenis=?, nama_topik=?, keterangan=? WHERE id=?");
                $stmt->execute([$jenis, $nama_topik, $keterangan, $id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO bs_topik (jenis, nama_topik, keterangan, tahun_ajaran) VALUES (?, ?, ?, ?)");
                $stmt->execute([$jenis, $nama_topik, $keterangan, $tahun_ajaran]);
            }
            redirect('/bank_soal/topik?success=1');
        }
    }

    public function hapusTopik() {
        if (isset($_GET['id'])) {
            $stmt = $this->db->prepare("DELETE FROM bs_topik WHERE id = ?");
            $stmt->execute([$_GET['id']]);
        }
        redirect('/bank_soal/topik?deleted=1');
    }

    // --- MANAJEMEN SOAL (MAPEL) ---

    public function kelolaSoal() {
        $stmt = $this->db->query("SELECT * FROM bs_topik ORDER BY created_at DESC");
        $topiks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapels = [];
        $selected_topik = $_GET['topik_id'] ?? null;
        
        if ($selected_topik) {
            // Ambil semua mapel dari tabel SIAKAD
            $stmtM = $this->db->query("SELECT nama_mapel FROM mata_pelajaran ORDER BY nama_mapel ASC");
            $all_mapel = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            // Hitung soal yang ada di topik ini
            $stmt2 = $this->db->prepare("
                SELECT mapel_nama, COUNT(id) as jml_soal 
                FROM bs_soal 
                WHERE topik_id = ? 
                GROUP BY mapel_nama
            ");
            $stmt2->execute([$selected_topik]);
            $counts = [];
            foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $counts[$row['mapel_nama']] = $row['jml_soal'];
            }

            foreach($all_mapel as $m) {
                $mapels[] = [
                    'mapel_nama' => $m['nama_mapel'],
                    'jml_soal' => $counts[$m['nama_mapel']] ?? 0
                ];
            }
        }

        $page_title = "Pilih Mapel - Bank Soal";
        $active_page = "kelola_soal";
        include __DIR__ . '/../../resources/views/bank_soal/kelola_soal.php';
    }

    // --- MANAJEMEN SOAL (DAFTAR & FORM) ---

    public function daftarSoal() {
        $topik_id = $_GET['topik_id'] ?? null;
        $mapel_nama = $_GET['mapel'] ?? null;

        if (!$topik_id || !$mapel_nama) {
            redirect('/bank_soal/kelola_soal');
        }

        $stmtT = $this->db->prepare("SELECT * FROM bs_topik WHERE id = ?");
        $stmtT->execute([$topik_id]);
        $topikInfo = $stmtT->fetch(PDO::FETCH_ASSOC);

        $stmtS = $this->db->prepare("SELECT * FROM bs_soal WHERE topik_id = ? AND mapel_nama = ? ORDER BY id ASC");
        $stmtS->execute([$topik_id, $mapel_nama]);
        $soals = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        $page_title = "Daftar Soal - Bank Soal";
        $active_page = "kelola_soal";
        include __DIR__ . '/../../resources/views/bank_soal/daftar_soal.php';
    }

    public function tambahSoal() {
        $topik_id = $_GET['topik_id'] ?? null;
        $mapel_nama = $_GET['mapel'] ?? null;

        if (!$topik_id || !$mapel_nama) {
            redirect('/bank_soal/kelola_soal');
        }

        $stmtT = $this->db->prepare("SELECT * FROM bs_topik WHERE id = ?");
        $stmtT->execute([$topik_id]);
        $topikInfo = $stmtT->fetch(PDO::FETCH_ASSOC);

        $page_title = "Tambah Soal - Bank Soal";
        $active_page = "kelola_soal";
        include __DIR__ . '/../../resources/views/bank_soal/tambah_soal.php';
    }

    public function simpanSoal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topik_id = $_POST['topik_id'];
            $mapel_nama = $_POST['mapel_nama'];
            $tipe_soal = $_POST['bentuk_soal'];
            $tingkat_kesulitan = $_POST['tingkat_kesulitan'];
            $skor_maksimal = $_POST['skor_maksimal'];
            $pertanyaan = $_POST['pertanyaan'];

            $konten = [];
            if ($tipe_soal === 'sec_pg') {
                $konten['kunci'] = $_POST['kunci_pg'] ?? '';
                $konten['opsi'] = [
                    'A' => $_POST['opsi_pg_A'] ?? '',
                    'B' => $_POST['opsi_pg_B'] ?? '',
                    'C' => $_POST['opsi_pg_C'] ?? '',
                    'D' => $_POST['opsi_pg_D'] ?? '',
                    'E' => $_POST['opsi_pg_E'] ?? ''
                ];
            } elseif ($tipe_soal === 'sec_pgk') {
                $pernyataan = $_POST['pgk_pernyataan'] ?? [];
                $kunci = $_POST['kunci_pgk'] ?? [];
                $opsi = [];
                foreach ($pernyataan as $idx => $teks) {
                    if (!empty(trim($teks))) {
                        $opsi[] = [
                            'teks' => $teks,
                            'is_kunci' => in_array($idx, $kunci) ? true : false
                        ];
                    }
                }
                $konten['opsi'] = $opsi;
            } elseif ($tipe_soal === 'sec_bs') {
                $pernyataan = $_POST['bs_pernyataan'] ?? [];
                $jawaban = $_POST['bs_jawaban'] ?? [];
                $butir = [];
                foreach ($pernyataan as $idx => $teks) {
                    if (!empty(trim($teks))) {
                        $butir[] = [
                            'teks' => $teks,
                            'kunci' => $jawaban[$idx] ?? 'Benar'
                        ];
                    }
                }
                $konten['butir'] = $butir;
            } elseif ($tipe_soal === 'sec_jodoh') {
                $premis = $_POST['jodoh_premis'] ?? [];
                $respon = $_POST['jodoh_respon'] ?? [];
                $pasangan = [];
                for ($i=0; $i<count($premis); $i++) {
                    if (!empty($premis[$i]) && !empty($respon[$i])) {
                        $pasangan[] = ['premis' => $premis[$i], 'respon' => $respon[$i]];
                    }
                }
                $konten['pasangan'] = $pasangan;
            } elseif ($tipe_soal === 'sec_isian') {
                $konten['kunci'] = $_POST['kunci_isian'] ?? '';
            } elseif ($tipe_soal === 'sec_essay') {
                $konten['rubrik'] = $_POST['rubrik'] ?? '';
            }

            $konten_json = json_encode($konten);

            $stmt = $this->db->prepare("
                INSERT INTO bs_soal 
                (topik_id, mapel_nama, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, konten_tambahan) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $topik_id, $mapel_nama, $tipe_soal, $tingkat_kesulitan, $skor_maksimal, $pertanyaan, $konten_json
            ]);

            redirect("/bank_soal/daftar_soal?topik_id={$topik_id}&mapel=" . urlencode($mapel_nama) . "&success=1");
        }
    }

    public function hapusSoal() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->db->prepare("SELECT topik_id, mapel_nama FROM bs_soal WHERE id = ?");
            $stmt->execute([$id]);
            $soal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($soal) {
                $del = $this->db->prepare("DELETE FROM bs_soal WHERE id = ?");
                $del->execute([$id]);
                redirect("/bank_soal/daftar_soal?topik_id={$soal['topik_id']}&mapel=" . urlencode($soal['mapel_nama']) . "&deleted=1");
            }
        }
        redirect('/bank_soal/kelola_soal');
    }
}
