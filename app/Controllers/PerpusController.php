<?php

namespace App\Controllers;

use App\Core\Database;

class PerpusController
{
    private $db_core;
    private $db_aula;

    public function __construct()
    {
        // Pastikan hanya admin yang bisa akses
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/portal');
        }
        $this->db_core = Database::connect('core');
        
        // Coba koneksi ke aula_db
        try {
            $host = 'localhost';
            $db   = 'aula_db';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';
            
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->db_aula = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            die("Koneksi ke aula_db gagal: " . $e->getMessage());
        }
    }

    public function dashboardUtama()
    {
        $stats = [
            'total_ebook' => $this->db_aula->query('SELECT COUNT(*) FROM buku_bacaan')->fetchColumn(),
            'total_fisik' => $this->db_core->query('SELECT SUM(stok) FROM perpus_buku_v2')->fetchColumn() ?: 0,
            'dipinjam' => $this->db_core->query("SELECT COUNT(*) FROM perpus_transaksi_detail WHERE status = 'Pinjam'")->fetchColumn(),
            'kunjungan_hari_ini' => $this->db_core->query("SELECT COUNT(*) FROM perpus_pengunjung WHERE DATE(waktu_masuk) = CURDATE()")->fetchColumn()
        ];
        include __DIR__ . '/../../resources/views/perpus/dashboard_utama.php';
    }

    public function dashboardEbook()
    {
        $stmt = $this->db_aula->query("
            SELECT b.*, k.nama_kategori 
            FROM buku_bacaan b 
            LEFT JOIN perpus_kategori k ON b.kategori_id = k.id 
            ORDER BY b.id DESC
        ");
        $buku = $stmt->fetchAll();

        // Load the view
        include __DIR__ . '/../../resources/views/perpus/ebook.php';
    }

    public function tambah()
    {
        $stmt = $this->db_aula->query("SELECT * FROM perpus_kategori ORDER BY nama_kategori ASC");
        $kategoriList = $stmt->fetchAll();
        
        include __DIR__ . '/../../resources/views/perpus/tambah.php';
    }

    public function simpanBuku()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul'] ?? '');
            $kategori_id = $_POST['kategori_id'] ?? null;
            if (empty($kategori_id)) $kategori_id = null;
            $deskripsi = $_POST['deskripsi'] ?? '';
            
            // Check for duplicates
            $stmtCheck = $this->db_aula->prepare("SELECT id FROM buku_bacaan WHERE judul = ?");
            $stmtCheck->execute([$judul]);
            if ($stmtCheck->rowCount() > 0) {
                $_SESSION['error'] = "Buku dengan judul yang sama sudah ada!";
                redirect('/admin/perpus/tambah');
            }
            
            $file_pdf = '';
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION));
                if ($ext == 'pdf') {
                    $file_pdf = time() . '_' . rand(1000, 9999) . '.pdf';
                    move_uploaded_file($_FILES['file_pdf']['tmp_name'], __DIR__ . '/../../public/uploads/buku/' . $file_pdf);
                } else {
                    $_SESSION['error'] = "File harus berformat PDF!";
                    redirect('/admin/perpus/tambah');
                }
            } else {
                $_SESSION['error'] = "File PDF wajib diupload!";
                redirect('/admin/perpus/tambah');
            }
            
            $cover_image = '';
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $cover_image = 'cover_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $upload_path = __DIR__ . '/../../public/uploads/buku/' . $cover_image;
                    move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path);
                    $this->createThumbnail($upload_path, $cover_image, $ext);
                }
            }

            $stmt = $this->db_aula->prepare("INSERT INTO buku_bacaan (judul, kategori_id, deskripsi, cover_image, file_pdf) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $kategori_id, $deskripsi, $cover_image, $file_pdf]);
            
            $_SESSION['success'] = "Buku berhasil ditambahkan!";
            redirect('/admin/perpus/ebook');
        }
    }

    public function editBuku($id)
    {
        $adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $adminInitial = strtoupper(substr($adminNama, 0, 2));
        
        $stmt = $this->db_aula->prepare("SELECT * FROM buku_bacaan WHERE id = ?");
        $stmt->execute([$id]);
        $buku = $stmt->fetch();
        
        if (!$buku) {
            $_SESSION['error'] = "Buku tidak ditemukan!";
            redirect('/admin/perpus/ebook');
        }

        $stmtKat = $this->db_aula->query("SELECT * FROM perpus_kategori ORDER BY nama_kategori ASC");
        $kategoriList = $stmtKat->fetchAll();

        ob_start();
        include __DIR__ . '/../../resources/views/perpus/edit.php';
        $content = ob_get_clean();
        
        $page_title = "Edit Buku Digital";
        $active_page = "perpus_ebook";
        include __DIR__ . '/../../resources/views/admin/_zamrud_head.php';
        echo $content;
    }

    public function updateBuku($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $judul = trim($_POST['judul'] ?? '');
            $kategori_id = $_POST['kategori_id'] ?? null;
            if (empty($kategori_id)) $kategori_id = null;
            $deskripsi = $_POST['deskripsi'] ?? '';
            
            // Check for duplicates (excluding current ID)
            $stmtCheck = $this->db_aula->prepare("SELECT id FROM buku_bacaan WHERE judul = ? AND id != ?");
            $stmtCheck->execute([$judul, $id]);
            if ($stmtCheck->rowCount() > 0) {
                $_SESSION['error'] = "Buku dengan judul yang sama sudah ada!";
                redirect('/admin/perpus/edit/' . $id);
            }

            // Get current file names
            $stmt = $this->db_aula->prepare("SELECT cover_image, file_pdf FROM buku_bacaan WHERE id = ?");
            $stmt->execute([$id]);
            $currentBuku = $stmt->fetch();

            $file_pdf = $currentBuku['file_pdf'];
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['file_pdf']['name'], PATHINFO_EXTENSION));
                if ($ext == 'pdf') {
                    $file_pdf = time() . '_' . rand(1000, 9999) . '.pdf';
                    move_uploaded_file($_FILES['file_pdf']['tmp_name'], __DIR__ . '/../../public/uploads/buku/' . $file_pdf);
                } else {
                    $_SESSION['error'] = "File harus berformat PDF!";
                    redirect('/admin/perpus/edit/' . $id);
                }
            }
            
            $cover_image = $currentBuku['cover_image'];
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $cover_image = 'cover_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $upload_path = __DIR__ . '/../../public/uploads/buku/' . $cover_image;
                    move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path);
                    $this->createThumbnail($upload_path, $cover_image, $ext);
                }
            }

            $stmt = $this->db_aula->prepare("UPDATE buku_bacaan SET judul = ?, kategori_id = ?, deskripsi = ?, cover_image = ?, file_pdf = ? WHERE id = ?");
            $stmt->execute([$judul, $kategori_id, $deskripsi, $cover_image, $file_pdf, $id]);
            
            $_SESSION['success'] = "Buku berhasil diupdate!";
            redirect('/admin/perpus/ebook');
        }
    }

    public function hapusBuku($id)
    {
        $stmt = $this->db_aula->prepare("SELECT file_pdf, cover_image FROM buku_bacaan WHERE id = ?");
        $stmt->execute([$id]);
        $buku = $stmt->fetch();
        
        if ($buku) {
            $dir = __DIR__ . '/../../public/uploads/buku/';
            if ($buku['file_pdf'] && file_exists($dir . $buku['file_pdf'])) {
                unlink($dir . $buku['file_pdf']);
            }
            if ($buku['cover_image'] && file_exists($dir . $buku['cover_image'])) {
                unlink($dir . $buku['cover_image']);
            }
            
            $del = $this->db_aula->prepare("DELETE FROM buku_bacaan WHERE id = ?");
            $del->execute([$id]);
            
            $_SESSION['success'] = "Buku berhasil dihapus!";
        }
        
        redirect('/admin/perpus');
    }

    public function bukuFisik()
    {
        $db = Database::connect('core');
        $buku = $db->query("
            SELECT b.*, k.nama_kategori, r.nama_rak, p.nama_penulis, pb.nama_penerbit 
            FROM perpus_buku_v2 b
            LEFT JOIN perpus_kategori k ON b.kategori_id = k.id
            LEFT JOIN perpus_rak r ON b.rak_id = r.id
            LEFT JOIN perpus_penulis p ON b.penulis_id = p.id
            LEFT JOIN perpus_penerbit pb ON b.penerbit_id = pb.id
            ORDER BY b.id DESC
        ")->fetchAll();
        include __DIR__ . '/../../resources/views/perpus/buku_fisik.php';
    }

    public function rak()
    {
        $db = Database::connect('core');
        $rak = $db->query("SELECT * FROM perpus_rak ORDER BY nama_rak ASC")->fetchAll();
        include __DIR__ . '/../../resources/views/perpus/master_rak.php';
    }

    public function penulisPenerbit()
    {
        $db = Database::connect('core');
        $penulis = $db->query("SELECT * FROM perpus_penulis ORDER BY nama_penulis ASC")->fetchAll();
        $penerbit = $db->query("SELECT * FROM perpus_penerbit ORDER BY nama_penerbit ASC")->fetchAll();
        include __DIR__ . '/../../resources/views/perpus/master_penulis_penerbit.php';
    }

    public function sirkulasi()
    {
        $db = Database::connect('core');
        $transaksi = $db->query("
            SELECT t.*, d.buku_id, d.status as status_buku, b.judul, b.kode_buku 
            FROM perpus_transaksi t
            LEFT JOIN perpus_transaksi_detail d ON t.id = d.transaksi_id
            LEFT JOIN perpus_buku_v2 b ON d.buku_id = b.id
            ORDER BY t.id DESC
        ")->fetchAll();
        include __DIR__ . '/../../resources/views/perpus/sirkulasi.php';
    }

    public function sirkulasiPinjam()
    {
        $db = Database::connect('core');
        // Fetch books that are available
        $buku = $db->query("SELECT * FROM perpus_buku_v2 WHERE stok > 0 ORDER BY judul ASC")->fetchAll();
        // Fetch members (Siswa/Guru). For now, let's fetch Siswa from siakad db.
        $db_siakad = Database::connect('siakad');
        $siswa = $db_siakad->query("SELECT id, nis, nama_lengkap as nama, kelas_id FROM siswa WHERE status = 'aktif' ORDER BY nama ASC")->fetchAll();
        $guru = $db->query("SELECT id, nip, nama FROM guru ORDER BY nama ASC")->fetchAll();
        
        include __DIR__ . '/../../resources/views/perpus/sirkulasi_pinjam.php';
    }

    public function kunjungan()
    {
        $db = Database::connect('core');
        $tamu = $db->query("SELECT * FROM perpus_pengunjung ORDER BY waktu_masuk DESC")->fetchAll();
        include __DIR__ . '/../../resources/views/perpus/kunjungan.php';
    }

    public function __construct()
    {
        $this->db_aula = \App\Core\Database::connect('core');
    }

    private function createThumbnail($upload_path, $cover_image, $ext) {
        if (!function_exists('imagecreatefromjpeg')) return;
        $thumb_path = __DIR__ . '/../../public/uploads/buku/thumb_' . $cover_image;
        $img = null;
        if (in_array($ext, ['jpg', 'jpeg'])) { $img = @imagecreatefromjpeg($upload_path); } 
        elseif ($ext == 'png') { $img = @imagecreatefrompng($upload_path); }
        
        if ($img !== false && $img !== null) {
            $width = imagesx($img);
            $height = imagesy($img);
            $new_width = 400;
            if ($width > $new_width) {
                $new_height = floor($height * ($new_width / $width));
                $tmp_img = imagecreatetruecolor($new_width, $new_height);
                if ($ext == 'png') {
                    imagealphablending($tmp_img, false); imagesavealpha($tmp_img, true);
                    $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
                    imagefilledrectangle($tmp_img, 0, 0, $new_width, $new_height, $transparent);
                }
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                if (in_array($ext, ['jpg', 'jpeg'])) { imagejpeg($tmp_img, $thumb_path, 80); } 
                elseif ($ext == 'png') { imagepng($tmp_img, $thumb_path, 8); }
                imagedestroy($tmp_img);
            } else {
                @copy($upload_path, $thumb_path);
            }
            imagedestroy($img);
        }
    }

    public function kunjunganScan()
    {
        $db = \App\Core\Database::connect('core');
        $inst = $db->query("SELECT nama, logo FROM institusi LIMIT 1")->fetch();
        $nama_sekolah = $inst['nama'] ?? 'Perpustakaan Digital';
        $logo_sekolah = \App\Core\Branding::getInstitusiLogo();
        $page_title = "Scan Kunjungan Perpustakaan";
        include __DIR__ . '/../../resources/views/perpus/kunjungan_scan.php';
    }

    // --- FITUR KATEGORI E-BOOK ---
    public function kategori()
    {
        $adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $adminInitial = strtoupper(substr($adminNama, 0, 2));

        $stmt = $this->db_aula->query("SELECT * FROM perpus_kategori ORDER BY created_at DESC");
        $kategoriList = $stmt->fetchAll();

        $active_page = "perpus_kategori";
        include __DIR__ . '/../../resources/views/perpus/master_kategori.php';
    }

    public function simpanKategori()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $kode_kategori = trim($_POST['kode_kategori'] ?? '');
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            $deskripsi = $_POST['deskripsi'] ?? '';

            if (empty($id)) {
                // Tambah baru
                $stmtCheck = $this->db_aula->prepare("SELECT id FROM perpus_kategori WHERE kode_kategori = ? OR nama_kategori = ?");
                $stmtCheck->execute([$kode_kategori, $nama_kategori]);
                if ($stmtCheck->rowCount() > 0) {
                    $_SESSION['error'] = "Kode atau Nama Kategori sudah ada!";
                    redirect('/admin/perpus/kategori');
                }

                $stmt = $this->db_aula->prepare("INSERT INTO perpus_kategori (kode_kategori, nama_kategori, deskripsi) VALUES (?, ?, ?)");
                $stmt->execute([$kode_kategori, $nama_kategori, $deskripsi]);
                $_SESSION['success'] = "Kategori berhasil ditambahkan!";
            } else {
                // Edit
                $stmtCheck = $this->db_aula->prepare("SELECT id FROM perpus_kategori WHERE (kode_kategori = ? OR nama_kategori = ?) AND id != ?");
                $stmtCheck->execute([$kode_kategori, $nama_kategori, $id]);
                if ($stmtCheck->rowCount() > 0) {
                    $_SESSION['error'] = "Kode atau Nama Kategori sudah dipakai!";
                    redirect('/admin/perpus/kategori');
                }

                $stmt = $this->db_aula->prepare("UPDATE perpus_kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ? WHERE id = ?");
                $stmt->execute([$kode_kategori, $nama_kategori, $deskripsi, $id]);
                $_SESSION['success'] = "Kategori berhasil diupdate!";
            }
            redirect('/admin/perpus/kategori');
        }
    }

    public function hapusKategori($id)
    {
        $stmtCheck = $this->db_aula->prepare("SELECT id FROM buku_bacaan WHERE kategori_id = ?");
        $stmtCheck->execute([$id]);
        if ($stmtCheck->rowCount() > 0) {
            $_SESSION['error'] = "Kategori tidak bisa dihapus karena masih digunakan oleh E-Book!";
            redirect('/admin/perpus/kategori');
        }

        $stmt = $this->db_aula->prepare("DELETE FROM perpus_kategori WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "Kategori berhasil dihapus!";
        redirect('/admin/perpus/kategori');
    }
}
