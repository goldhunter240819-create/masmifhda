<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil data guru
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Cek is_kamad dan is_wakakur
$is_kamad = (isset($guru['is_kamad']) && $guru['is_kamad'] == 1);
$is_wakakur = false;
try {
    $stmt_w = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
    $stmt_w->execute([$guru_id]);
    if ($stmt_w->fetch()) {
        $is_wakakur = true;
    }
} catch(Exception $e) {}

$kategori_list = [
    'Capaian Pembelajaran (CP)',
    'Alur Tujuan Pembelajaran (ATP)',
    'Modul Ajar',
    'KKTP',
    'Program Tahunan (Prota)',
    'Program Semester (Promes)'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // ==========================================
    // BACKEND PERANGKAT MENGAJAR
    // ==========================================
    if ($_POST['action'] === 'upload_perangkat') {
        $nama_berkas = trim($_POST['nama_berkas'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $mapel_kelas = trim($_POST['mapel_kelas'] ?? 'Umum|Umum');
        
        $mapel_parts = explode('|', $mapel_kelas);
        $mapel_nama = $mapel_parts[0] ?? 'Umum';
        $kelas_nama = $mapel_parts[1] ?? 'Umum';
        
        if (!empty($nama_berkas) && !empty($kategori) && isset($_FILES['berkas_file']) && $_FILES['berkas_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/guru_perangkat/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $ext = strtolower(pathinfo($_FILES['berkas_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'perangkat_' . $guru_id . '_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['berkas_file']['tmp_name'], $uploadDir . $filename)) {
                    $stmt = $core_db->prepare("INSERT INTO guru_perangkat (guru_id, kategori, mapel_nama, kelas_nama, nama_berkas, file_path, status_review) VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')");
                    $stmt->execute([$guru_id, $kategori, $mapel_nama, $kelas_nama, $nama_berkas, $filename]);
                    
                    header("Location: berkas_guru.php?tab=perangkat&success=upload_perangkat");
                    exit;
                }
            } else {
                header("Location: berkas_guru.php?tab=perangkat&error=format");
                exit;
            }
        } else {
            header("Location: berkas_guru.php?tab=perangkat&error=empty");
            exit;
        }
    } elseif ($_POST['action'] === 'delete_perangkat') {
        $berkas_id = intval($_POST['berkas_id'] ?? 0);
        $stmt = $core_db->prepare("SELECT file_path, status_review FROM guru_perangkat WHERE id = ? AND guru_id = ?");
        $stmt->execute([$berkas_id, $guru_id]);
        $berkas = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($berkas) {
            if ($berkas['status_review'] !== 'Sempurna') {
                $uploadDir = __DIR__ . '/../public/uploads/guru_perangkat/';
                if (file_exists($uploadDir . $berkas['file_path'])) unlink($uploadDir . $berkas['file_path']);
                $del = $core_db->prepare("DELETE FROM guru_perangkat WHERE id = ?");
                $del->execute([$berkas_id]);
                
                header("Location: berkas_guru.php?tab=perangkat&success=delete_perangkat");
                exit;
            } else {
                header("Location: berkas_guru.php?tab=perangkat&error=locked");
                exit;
            }
        }
    } elseif ($_POST['action'] === 'edit_perangkat') {
        $berkas_id = intval($_POST['berkas_id'] ?? 0);
        $nama_berkas = trim($_POST['nama_berkas'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');

        $stmt = $core_db->prepare("SELECT file_path, status_review FROM guru_perangkat WHERE id = ? AND guru_id = ?");
        $stmt->execute([$berkas_id, $guru_id]);
        $berkas = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($berkas && $berkas['status_review'] !== 'Sempurna' && !empty($nama_berkas) && !empty($kategori)) {
            if (isset($_FILES['berkas_file']) && $_FILES['berkas_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/guru_perangkat/';
                $ext = strtolower(pathinfo($_FILES['berkas_file']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['pdf'])) {
                    if (file_exists($uploadDir . $berkas['file_path'])) {
                        unlink($uploadDir . $berkas['file_path']);
                    }
                    $filename = 'perangkat_' . $guru_id . '_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['berkas_file']['tmp_name'], $uploadDir . $filename);
                    
                    $upd = $core_db->prepare("UPDATE guru_perangkat SET nama_berkas = ?, kategori = ?, file_path = ?, status_review = 'Menunggu' WHERE id = ?");
                    $upd->execute([$nama_berkas, $kategori, $filename, $berkas_id]);
                } else {
                    header("Location: berkas_guru.php?tab=perangkat&error=format");
                    exit;
                }
            } else {
                $upd = $core_db->prepare("UPDATE guru_perangkat SET nama_berkas = ?, kategori = ? WHERE id = ?");
                $upd->execute([$nama_berkas, $kategori, $berkas_id]);
            }
            header("Location: berkas_guru.php?tab=perangkat&success=edit_perangkat");
            exit;
        } else {
            header("Location: berkas_guru.php?tab=perangkat&error=edit_locked");
            exit;
        }
    }

    // ==========================================
    // BACKEND DOKUMEN PRIBADI
    // ==========================================
    if ($_POST['action'] === 'upload_pribadi') {
        $nama_berkas = trim($_POST['nama_berkas'] ?? '');
        if (!empty($nama_berkas) && isset($_FILES['berkas_file']) && $_FILES['berkas_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/guru_berkas/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['berkas_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf'];
            if (in_array($ext, $allowed)) {
                $filename = 'berkas_' . $guru_id . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['berkas_file']['tmp_name'], $uploadDir . $filename)) {
                    $stmt = $core_db->prepare("INSERT INTO guru_berkas (guru_id, nama_berkas, file_path) VALUES (?, ?, ?)");
                    $stmt->execute([$guru_id, $nama_berkas, $filename]);
                    
                    require_once __DIR__ . '/../vendor/autoload.php';
                    if (class_exists('\App\Services\AuditLogger')) {
                        \App\Services\AuditLogger::dataChange('Guru_Berkas', 'UPLOAD', "guru_id={$guru_id}, nama_berkas={$nama_berkas} (APK)");
                    }

                    header("Location: berkas_guru.php?tab=pribadi&success=upload_pribadi");
                    exit;
                }
            } else {
                header("Location: berkas_guru.php?tab=pribadi&error=format");
                exit;
            }
        } else {
            header("Location: berkas_guru.php?tab=pribadi&error=empty");
            exit;
        }
    } elseif ($_POST['action'] === 'delete_pribadi') {
        $berkas_id = intval($_POST['berkas_id'] ?? 0);
        $stmt = $core_db->prepare("SELECT file_path, nama_berkas FROM guru_berkas WHERE id = ? AND guru_id = ?");
        $stmt->execute([$berkas_id, $guru_id]);
        $berkas = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($berkas) {
            $uploadDir = __DIR__ . '/../public/uploads/guru_berkas/';
            if (file_exists($uploadDir . $berkas['file_path'])) unlink($uploadDir . $berkas['file_path']);
            $del = $core_db->prepare("DELETE FROM guru_berkas WHERE id = ?");
            $del->execute([$berkas_id]);
            
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists('\App\Services\AuditLogger')) {
                \App\Services\AuditLogger::dataChange('Guru_Berkas', 'DELETE', "id={$berkas_id}, nama_berkas={$berkas['nama_berkas']} (APK)");
            }

            header("Location: berkas_guru.php?tab=pribadi&success=delete_pribadi");
            exit;
        }
    }
}

// Messages
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'upload_perangkat') $success_msg = "Perangkat pembelajaran berhasil diunggah! ❤️";
    if ($_GET['success'] === 'delete_perangkat') $success_msg = "Berkas perangkat berhasil dihapus.";
    if ($_GET['success'] === 'edit_perangkat') $success_msg = "Berkas perangkat berhasil diperbarui!";
    if ($_GET['success'] === 'upload_pribadi') $success_msg = "Dokumen pribadi berhasil diupload!";
    if ($_GET['success'] === 'delete_pribadi') $success_msg = "Dokumen pribadi berhasil dihapus!";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'format') $error_msg = "Format file tidak diizinkan.";
    if ($_GET['error'] === 'empty') $error_msg = "Gagal upload. Pastikan semua form terisi.";
    if ($_GET['error'] === 'locked') $error_msg = "Berkas yang direview 'Sempurna' tidak bisa dihapus.";
    if ($_GET['error'] === 'edit_locked') $error_msg = "Berkas tidak dapat diedit.";
}

// ==========================================
// DATA FETCH: PERANGKAT MENGAJAR
// ==========================================
$ta_id = 1;
$sem_num = 1;
try {
    $stmt_peng = $siakad_db->query("SELECT tahun_ajaran_aktif, semester_aktif FROM pengaturan LIMIT 1");
    $peng = $stmt_peng->fetch(PDO::FETCH_ASSOC);
    if ($peng) {
        $ta_name = $peng['tahun_ajaran_aktif'];
        $sem_name = strtolower(trim($peng['semester_aktif']));
        $sem_num = ($sem_name === 'genap' || $sem_name === '2') ? 2 : 1;
        $stmt_ta = $core_db->prepare("SELECT id FROM tahun_ajaran WHERE name = ? AND semester = ? LIMIT 1");
        $stmt_ta->execute([$ta_name, $sem_num]);
        $ta_row = $stmt_ta->fetch(PDO::FETCH_ASSOC);
        if ($ta_row) $ta_id = $ta_row['id'];
    }
} catch (Exception $e) {}

$mapel_kelas_list = [];
try {
    $stmt_jadwal_guru = $siakad_db->prepare("
        SELECT DISTINCT m.nama_mapel, k.nama_kelas 
        FROM (
            SELECT kelas_id, mapel_id as mapel FROM mengajar WHERE guru_id = :guru_id AND tahun_ajaran_id = :ta_id AND semester = :semester
            UNION
            SELECT kelas_id, mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = :guru_id AND tahun_ajaran_id = :ta_id AND semester = :semester
        ) jp
        JOIN mata_pelajaran m ON jp.mapel = m.id
        JOIN aula_db.kelas k ON jp.kelas_id = k.id
        ORDER BY k.nama_kelas ASC, m.nama_mapel ASC
    ");
    $stmt_jadwal_guru->execute(['guru_id' => $guru_id, 'ta_id' => $ta_id, 'semester' => (string)$sem_num]);
    $mapel_kelas_list = $stmt_jadwal_guru->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$stmt_perangkat = $core_db->prepare("SELECT * FROM guru_perangkat WHERE guru_id = ? ORDER BY kelas_nama ASC, mapel_nama ASC, kategori ASC, created_at DESC");
$stmt_perangkat->execute([$guru_id]);
$all_perangkat = $stmt_perangkat->fetchAll(PDO::FETCH_ASSOC);

$grouped_perangkat = [];
foreach ($all_perangkat as $p) {
    $kelas = isset($p['kelas_nama']) ? $p['kelas_nama'] : 'Umum';
    $map = $p['mapel_nama'];
    $group_key = $map;
    if ($kelas !== 'Umum') $group_key .= " - Kelas " . $kelas;
    $kat = $p['kategori'];
    if (!isset($grouped_perangkat[$group_key])) $grouped_perangkat[$group_key] = [];
    if (!isset($grouped_perangkat[$group_key][$kat])) $grouped_perangkat[$group_key][$kat] = [];
    $grouped_perangkat[$group_key][$kat][] = $p;
}

// ==========================================
// DATA FETCH: DOKUMEN PRIBADI
// ==========================================
$stmtBerkas = $core_db->prepare("SELECT * FROM guru_berkas WHERE guru_id = ? ORDER BY created_at DESC");
$stmtBerkas->execute([$guru_id]);
$list_pribadi = $stmtBerkas->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Pusat Dokumen';
include 'header.php';
$active_tab = $_GET['tab'] ?? 'perangkat';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .tab-btn {
        flex: 1; padding: 12px; border: none; border-radius: 12px; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;
    }
    .tab-btn.active {
        background: #fff; color: #0ea5e9; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .tab-btn.inactive {
        background: transparent; color: #64748b;
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    @keyframes slideUpModal {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- HEADER LENGKUNG MTSRS STYLE -->
<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding-bottom: 20px;">
    <div class="header-top">
        <a href="profil_guru.php" style="color: white; font-size: 1.5rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 24px;">
            <p style="margin: 0; font-size: 0.8rem; opacity: 0.8; color: #e0f2fe;">Manajemen Dokumen</p>
            <h1 style="margin: 0; font-size: 1.2rem; font-weight: 800;">Pusat Dokumen</h1>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 80px;">
    
    <div class="data-card" style="margin-top: -40px; background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); position: relative; z-index: 10; padding: 15px;">
        
        <!-- Tab Navigasi -->
        <div style="display: flex; gap: 5px; margin-bottom: 20px; padding: 5px; background: #f1f5f9; border-radius: 15px;">
            <button onclick="switchTab('perangkat')" id="btn_perangkat" class="tab-btn <?= $active_tab == 'perangkat' ? 'active' : 'inactive' ?>">
                Perangkat Mengajar
            </button>
            <button onclick="switchTab('pribadi')" id="btn_pribadi" class="tab-btn <?= $active_tab == 'pribadi' ? 'active' : 'inactive' ?>">
                Berkas Pribadi
            </button>
        </div>

        <?php if (isset($success_msg)): ?>
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; border-left: 4px solid #22c55e;">
                <i class="fa-solid fa-circle-check"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; border-left: 4px solid #ef4444;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <!-- ================= TAB PERANGKAT ================= -->
        <div id="content_perangkat" class="tab-content <?= $active_tab == 'perangkat' ? 'active' : '' ?>">

            <!-- MODAL FORM UPLOAD -->
            <div id="modal-form-perangkat" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
                <div style="background: white; width: 100%; max-width: 400px; border-radius: 20px; padding: 25px 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); position: relative; animation: slideUpModal 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <h3 style="margin-top: 0; color: #0f172a; font-size: 1.1rem; margin-bottom: 20px;"><i class="fa-solid fa-cloud-arrow-up" style="color: #0ea5e9;"></i> Upload Dokumen</h3>
                    <button type="button" onclick="document.getElementById('modal-form-perangkat').style.display='none'" style="position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="fa-solid fa-xmark"></i></button>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_perangkat">
                        <input type="hidden" id="input_mapel_kelas" name="mapel_kelas" required>
                        <input type="hidden" id="input_kategori" name="kategori" required>
                        
                        <div style="margin-bottom: 15px; background: #f0f9ff; padding: 12px; border-radius: 12px; border: 1px dashed #bae6fd;">
                            <div style="font-size: 0.75rem; color: #0284c7; margin-bottom: 4px; font-weight: 700;"><i class="fa-solid fa-tags"></i> Kategori:</div>
                            <div id="display_kategori" style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">-</div>
                            <div id="display_mapel_kelas" style="font-size: 0.75rem; color: #64748b; margin-top: 4px; font-weight: 600;">-</div>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">Judul / Nama Dokumen <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nama_berkas" placeholder="mis: Modul Ajar Bab 1" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; background: #f8fafc; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#0ea5e9'" onblur="this.style.borderColor='#cbd5e1'">
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">File (Wajib PDF) <span style="color:#ef4444">*</span></label>
                            <input type="file" name="berkas_file" required accept="application/pdf" style="width: 100%; padding: 10px; background: #f8fafc; border: 1px dashed #94a3b8; border-radius: 10px; font-size: 0.85rem;">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" onclick="document.getElementById('modal-form-perangkat').style.display='none'" style="flex: 1; background: white; color: #64748b; border: 1px solid #cbd5e1; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s;">Batal</button>
                            <button type="submit" style="flex: 1; background: #0ea5e9; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(14,165,233,0.3); transition: 0.2s;"><i class="fa-solid fa-upload"></i> Upload</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL FORM EDIT PERANGKAT -->
            <div id="modal-edit-perangkat" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
                <div style="background: white; width: 100%; max-width: 400px; border-radius: 20px; padding: 25px 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); position: relative; animation: slideUpModal 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                    <h3 style="margin-top: 0; color: #0f172a; font-size: 1.1rem; margin-bottom: 20px;"><i class="fa-solid fa-pen-to-square" style="color: #0ea5e9;"></i> Edit Dokumen</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-perangkat').style.display='none'" style="position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="fa-solid fa-xmark"></i></button>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit_perangkat">
                        <input type="hidden" id="edit_berkas_id" name="berkas_id" required>
                        
                        <div style="margin-bottom: 15px; background: #f0f9ff; padding: 12px; border-radius: 12px; border: 1px dashed #bae6fd;">
                            <div style="font-size: 0.75rem; color: #0284c7; margin-bottom: 4px; font-weight: 700;"><i class="fa-solid fa-tags"></i> Kelas & Mapel:</div>
                            <div id="edit_display_mapel_kelas" style="font-size: 0.85rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">Kategori Dokumen <span style="color:#ef4444">*</span></label>
                            <select name="kategori" id="edit_input_kategori" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; background: #f8fafc; outline: none; transition: 0.2s;">
                                <?php foreach($kategori_list as $dw): ?>
                                    <option value="<?= htmlspecialchars($dw) ?>"><?= htmlspecialchars($dw) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">Judul / Nama Dokumen <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nama_berkas" id="edit_input_nama_berkas" placeholder="mis: Modul Ajar Bab 1" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; background: #f8fafc; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#0ea5e9'" onblur="this.style.borderColor='#cbd5e1'">
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">File Baru (Opsional, PDF)</label>
                            <input type="file" name="berkas_file" accept="application/pdf" style="width: 100%; padding: 10px; background: #f8fafc; border: 1px dashed #94a3b8; border-radius: 10px; font-size: 0.85rem;">
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 5px;">Biarkan kosong jika tidak ingin mengubah file PDF.</div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" onclick="document.getElementById('modal-edit-perangkat').style.display='none'" style="flex: 1; background: white; color: #64748b; border: 1px solid #cbd5e1; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s;">Batal</button>
                            <button type="submit" style="flex: 1; background: #0ea5e9; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(14,165,233,0.3); transition: 0.2s;"><i class="fa-solid fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div style="margin: 0 0 15px 0;">
                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 15px;">
                    <h3 class="section-title" style="margin: 0;">Koleksi Perangkat Saya</h3>
                    
                    <?php 
                    $dokumen_wajib = [
                        'Capaian Pembelajaran (CP)',
                        'Alur Tujuan Pembelajaran (ATP)',
                        'Modul Ajar',
                        'KKTP',
                        'Program Tahunan (Prota)',
                        'Program Semester (Promes)'
                    ];
                    
                    // Hitung berkas yang belum diupload
                    $missing_docs_list = [];
                    if(!empty($mapel_kelas_list)) {
                        foreach($mapel_kelas_list as $mk) {
                            $m_key = $mk['nama_mapel'] . ' - Kelas ' . $mk['nama_kelas'];
                            foreach($dokumen_wajib as $dw) {
                                $found = false;
                                foreach($all_perangkat as $p) {
                                    $p_kelas = isset($p['kelas_nama']) ? $p['kelas_nama'] : 'Umum';
                                    $p_key = $p['mapel_nama'];
                                    if ($p_kelas !== 'Umum') $p_key .= " - Kelas " . $p_kelas;
                                    
                                    if ($p_key === $m_key && $p['kategori'] === $dw) {
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) {
                                    $missing_docs_list[] = ['mapel' => $m_key, 'kategori' => $dw];
                                }
                            }
                        }
                    }
                    ?>
                    
                    <?php if (!empty($missing_docs_list)): ?>
                    <div onclick="document.getElementById('modal-missing-docs').style.display='flex'" style="margin-top: 8px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.1); transition: 0.2s;">
                        <i class="fa-solid fa-bell fa-shake"></i> <?= count($missing_docs_list) ?> berkas wajib bolong! (Lihat)
                    </div>
                </div>
                
                <!-- MODAL MISSING DOCS -->
                <div id="modal-missing-docs" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
                    <div style="background: white; width: 100%; max-width: 400px; border-radius: 20px; padding: 25px 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); position: relative; animation: slideUpModal 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 80vh; display: flex; flex-direction: column;">
                        <h3 style="margin-top: 0; color: #b91c1c; font-size: 1.1rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-triangle-exclamation"></i> Berkas Belum Lengkap</h3>
                        <button type="button" onclick="document.getElementById('modal-missing-docs').style.display='none'" style="position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="fa-solid fa-xmark"></i></button>
                        
                        <div style="overflow-y: auto; flex: 1; padding-right: 5px; display: flex; flex-direction: column; gap: 8px;">
                            <?php foreach($missing_docs_list as $md): ?>
                                <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px 12px; border-radius: 10px;">
                                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 2px;"><?= htmlspecialchars($md['mapel']) ?></div>
                                    <div style="font-size: 0.85rem; font-weight: 800; color: #ef4444;"><i class="fa-solid fa-file-circle-xmark"></i> <?= htmlspecialchars($md['kategori']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="button" onclick="document.getElementById('modal-missing-docs').style.display='none'" style="width: 100%; background: #0f172a; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; transition: 0.2s;">Tutup</button>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(!empty($mapel_kelas_list) && count($mapel_kelas_list) > 1): ?>
                <div class="mapel-slider" style="display: flex; align-items: center; justify-content: space-between; background: white; padding: 10px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px;">
                    <button id="btn-prev-mapel" onclick="changeMapel(-1)" style="background: #f8fafc; border: 1px solid #e2e8f0; width: 45px; height: 45px; border-radius: 12px; color: #475569; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.2s; outline: none; flex-shrink: 0;"><i class="fa-solid fa-chevron-left"></i></button>
                    <div style="flex: 1; text-align: center; padding: 0 10px; min-width: 0;">
                         <select id="filter-mapel" onchange="filterMapel()" style="width: 100%; border: none; background: transparent; font-size: 0.95rem; font-weight: 800; color: #0f172a; text-align: center; text-align-last: center; outline: none; appearance: none; cursor: pointer; white-space: normal; text-overflow: ellipsis; padding: 0;">
                            <?php foreach($mapel_kelas_list as $mk): 
                                $val = htmlspecialchars($mk['nama_mapel'] . ' - Kelas ' . $mk['nama_kelas']);
                            ?>
                                <option value="<?= $val ?>"><?= $val ?></option>
                            <?php endforeach; ?>
                         </select>
                         <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px; font-weight: 600;"><i class="fa-solid fa-hand-pointer"></i> Ketuk atau geser</div>
                    </div>
                    <button id="btn-next-mapel" onclick="changeMapel(1)" style="background: #f8fafc; border: 1px solid #e2e8f0; width: 45px; height: 45px; border-radius: 12px; color: #475569; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.2s; outline: none; flex-shrink: 0;"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <?php endif; ?>
            </div>
            
            <?php 
            if(empty($mapel_kelas_list)): ?>
                <div style="text-align: center; padding: 30px; border: 1px dashed #cbd5e1; border-radius: 12px;">
                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Belum ada jadwal mengajar yang ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach($mapel_kelas_list as $mk): 
                    $mapel_key = $mk['nama_mapel'] . ' - Kelas ' . $mk['nama_kelas'];
                    $mapel_val = $mk['nama_mapel'] . '|' . $mk['nama_kelas'];
                    
                    // Kumpulkan berkas untuk mapel ini
                    $docs_for_this_mapel = [];
                    foreach($all_perangkat as $p) {
                        $p_kelas = isset($p['kelas_nama']) ? $p['kelas_nama'] : 'Umum';
                        $p_key = $p['mapel_nama'];
                        if ($p_kelas !== 'Umum') $p_key .= " - Kelas " . $p_kelas;
                        
                        if ($p_key === $mapel_key) {
                            $docs_for_this_mapel[] = $p;
                        }
                    }
                ?>
                    <div class="mapel-container" data-mapel="<?= htmlspecialchars($mapel_key) ?>" style="background: #f1f5f9; border-radius: 12px; margin-bottom: 20px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);">
                        <div style="background: #e2e8f0; padding: 12px 15px; border-bottom: 1px solid #cbd5e1;">
                            <h3 style="margin: 0; font-size: 0.95rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-book-open" style="color: #0284c7;"></i> <?= htmlspecialchars($mapel_key) ?>
                            </h3>
                        </div>
                        <div style="padding: 15px; display: flex; flex-direction: column; gap: 10px;">
                            
                            <?php foreach($dokumen_wajib as $dok_wajib): 
                                $matched_berkas = [];
                                foreach($docs_for_this_mapel as $b) {
                                    if ($b['kategori'] === $dok_wajib) {
                                        $matched_berkas[] = $b;
                                    }
                                }
                                $color_map = [
                                    'Capaian Pembelajaran (CP)' => ['bg' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#0284c7'],
                                    'Alur Tujuan Pembelajaran (ATP)' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#16a34a'],
                                    'Modul Ajar' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'text' => '#d97706'],
                                    'KKTP' => ['bg' => '#fdf4ff', 'border' => '#f5d0fe', 'text' => '#c026d3'],
                                    'Program Tahunan (Prota)' => ['bg' => '#ecfeff', 'border' => '#a5f3fc', 'text' => '#0891b2'],
                                    'Program Semester (Promes)' => ['bg' => '#eef2ff', 'border' => '#c7d2fe', 'text' => '#4f46e5'] // Ubah ke Indigo agar tidak sama dengan merah alert
                                ];
                                $c = $color_map[$dok_wajib] ?? ['bg' => '#f8fafc', 'border' => '#e2e8f0', 'text' => '#475569'];
                                $card_body_bg = 'white';
                                
                                // Jika belum diupload, timpa dengan warna merah merona agar sangat mencolok
                                if (empty($matched_berkas)) {
                                    $c = ['bg' => '#ef4444', 'border' => '#ef4444', 'text' => '#ffffff']; // Header Full Merah, teks putih
                                    $card_body_bg = '#fff5f5'; // Body tetap sedikit merah muda
                                }
                                
                                $btn_text_color = empty($matched_berkas) ? '#ef4444' : $c['text'];
                            ?>
                                <div style="margin-bottom: 12px; background: <?= $card_body_bg ?>; border: 2px solid <?= $c['border'] ?>; border-radius: 8px; overflow: hidden; box-shadow: <?= empty($matched_berkas) ? '0 4px 12px rgba(239, 68, 68, 0.15)' : 'none' ?>;">
                                    <div style="background: <?= $c['bg'] ?>; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid <?= $c['border'] ?>;">
                                        <div style="font-size: 0.85rem; font-weight: 800; color: <?= $c['text'] ?>;"><i class="fa-solid <?= empty($matched_berkas) ? 'fa-triangle-exclamation fa-beat-fade' : 'fa-check' ?>" style="color: <?= $c['text'] ?>; margin-right: 6px;"></i> <?= htmlspecialchars($dok_wajib) ?></div>
                                        <button onclick="bukaModalUpload('<?= $dok_wajib ?>', '<?= $mapel_val ?>', '<?= htmlspecialchars($mapel_key) ?>')" style="background: white; border: 1px solid <?= $c['border'] ?>; color: <?= $btn_text_color ?>; padding: 4px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: 0.2s;">
                                            <i class="fa-solid <?= empty($matched_berkas) ? 'fa-upload' : 'fa-plus' ?>"></i> <?= empty($matched_berkas) ? 'Upload' : 'Tambah' ?>
                                        </button>
                                    </div>
                                    <div style="padding: 8px; display: flex; flex-direction: column; gap: 5px;">
                                        <?php if(empty($matched_berkas)): ?>
                                            <div style="font-size: 0.7rem; color: #ef4444; background: #fef2f2; padding: 6px 10px; border-radius: 6px; border: 1px solid #fecaca; display: inline-block; align-self: flex-start;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Berkas belum diupload
                                            </div>
                                        <?php else: ?>
                                            <?php foreach($matched_berkas as $b): 
                                                $status_color = '#94a3b8'; $status_bg = '#f1f5f9'; $status_icon = 'fa-clock';
                                                if ($b['status_review'] == 'Sempurna') { $status_color = '#10b981'; $status_bg = '#dcfce7'; $status_icon = 'fa-check-circle'; }
                                                elseif ($b['status_review'] == 'Perlu Disempurnakan') { $status_color = '#eab308'; $status_bg = '#fef9c3'; $status_icon = 'fa-pen-to-square'; }
                                            ?>
                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #f8fafc; border-left: 3px solid <?= $status_color ?>; border-radius: 6px; margin-bottom: 2px;">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($b['nama_berkas']) ?></div>
                                                        <div style="font-size: 0.65rem; color: #94a3b8;">
                                                            <?= date('d M Y', strtotime($b['created_at'])) ?> &bull; 
                                                            <span style="color: <?= $status_color ?>; font-weight: 700;"><i class="fa-solid <?= $status_icon ?>"></i> <?= htmlspecialchars($b['status_review']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; gap: 6px; margin-left: 8px; flex-shrink: 0;">
                                                        <a href="pdf_viewer.php?type=perangkat&id=<?= $b['id'] ?>" style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; text-decoration: none;"><i class="fa-solid fa-eye"></i></a>
                                                        <?php if ($b['status_review'] !== 'Sempurna'): ?>
                                                            <button type="button" onclick="bukaModalEditPerangkat(<?= $b['id'] ?>, '<?= addslashes($b['nama_berkas']) ?>', '<?= addslashes($b['kategori']) ?>', '<?= htmlspecialchars($mapel_key) ?>')" style="background: #fef3c7; color: #d97706; border: none; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;"><i class="fa-solid fa-pen"></i></button>
                                                            <form method="POST" style="margin: 0;" id="form-delp-<?= $b['id'] ?>">
                                                                <input type="hidden" name="action" value="delete_perangkat">
                                                                <input type="hidden" name="berkas_id" value="<?= $b['id'] ?>">
                                                                <button type="button" onclick="confirmDelete(<?= $b['id'] ?>, '<?= addslashes($b['nama_berkas']) ?>', 'form-delp-')" style="background: #fee2e2; color: #dc2626; border: none; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;"><i class="fa-solid fa-trash-can"></i></button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Dokumen Lainnya -->
                            <?php 
                                $adaLainnya = false;
                                foreach($docs_for_this_mapel as $b) {
                                    if (!in_array($b['kategori'], $dokumen_wajib)) {
                                        if(!$adaLainnya) {
                                            echo '<div style="margin-top: 10px; font-size: 0.8rem; font-weight: 800; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;">Kategori Tambahan:</div>';
                                            $adaLainnya = true;
                                        }
                                        
                                        $status_color = '#94a3b8'; $status_bg = '#f1f5f9'; $status_icon = 'fa-clock';
                                        if ($b['status_review'] == 'Sempurna') { $status_color = '#10b981'; $status_bg = '#dcfce7'; $status_icon = 'fa-check-circle'; }
                                        elseif ($b['status_review'] == 'Perlu Disempurnakan') { $status_color = '#eab308'; $status_bg = '#fef9c3'; $status_icon = 'fa-pen-to-square'; }
                                        ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #f8fafc; border-left: 3px solid <?= $status_color ?>; border-radius: 6px; margin-bottom: 6px;">
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-size: 0.7rem; color: #64748b; font-weight: 700; margin-bottom: 2px;"><?= htmlspecialchars($b['kategori']) ?></div>
                                                <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($b['nama_berkas']) ?></div>
                                                <div style="font-size: 0.65rem; color: #94a3b8;">
                                                    <?= date('d M Y', strtotime($b['created_at'])) ?> &bull; 
                                                    <span style="color: <?= $status_color ?>; font-weight: 700;"><i class="fa-solid <?= $status_icon ?>"></i> <?= htmlspecialchars($b['status_review']) ?></span>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 6px; margin-left: 8px; flex-shrink: 0;">
                                                <a href="pdf_viewer.php?type=perangkat&id=<?= $b['id'] ?>" style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; text-decoration: none;"><i class="fa-solid fa-eye"></i></a>
                                                <?php if ($b['status_review'] !== 'Sempurna'): ?>
                                                    <button type="button" onclick="bukaModalEditPerangkat(<?= $b['id'] ?>, '<?= addslashes($b['nama_berkas']) ?>', '<?= addslashes($b['kategori']) ?>', '<?= htmlspecialchars($mapel_key) ?>')" style="background: #fef3c7; color: #d97706; border: none; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;"><i class="fa-solid fa-pen"></i></button>
                                                    <form method="POST" style="margin: 0;" id="form-delp-<?= $b['id'] ?>">
                                                        <input type="hidden" name="action" value="delete_perangkat">
                                                        <input type="hidden" name="berkas_id" value="<?= $b['id'] ?>">
                                                        <button type="button" onclick="confirmDelete(<?= $b['id'] ?>, '<?= addslashes($b['nama_berkas']) ?>', 'form-delp-')" style="background: #fee2e2; color: #dc2626; border: none; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;"><i class="fa-solid fa-trash-can"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- ================= TAB PRIBADI ================= -->
        <div id="content_pribadi" class="tab-content <?= $active_tab == 'pribadi' ? 'active' : '' ?>">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h3 style="font-size: 1rem; color: #4c1d95; margin: 0;"><i class="fa-solid fa-file-invoice"></i> Upload Arsip</h3>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 3px 0 0 0;">Ijazah, Sertifikat, KTP dll</p>
                </div>
                <button onclick="document.getElementById('form-pribadi').style.display='block'" style="background: #8b5cf6; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(139,92,246,0.3);">
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </div>

            <form id="form-pribadi" method="POST" enctype="multipart/form-data" style="display: none; background: #faf5ff; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px dashed #e9d5ff;">
                <input type="hidden" name="action" value="upload_pribadi">
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #4c1d95; display: block; margin-bottom: 5px;">Nama Dokumen</label>
                    <input type="text" name="nama_berkas" placeholder="mis: Ijazah S1" required style="width: 100%; padding: 10px; border: 1px solid #e9d5ff; border-radius: 8px;">
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #581c87; display: block; margin-bottom: 5px;">File (Wajib PDF)</label>
                    <input type="file" name="berkas_file" required accept="application/pdf" style="width: 100%; padding: 8px; background: white; border: 1px solid #e9d5ff; border-radius: 8px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                    <button type="button" onclick="document.getElementById('form-pribadi').style.display='none'" style="background: white; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 15px; border-radius: 8px; font-weight: 600;">Batal</button>
                    <button type="submit" style="background: #8b5cf6; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 600;"><i class="fa-solid fa-upload"></i> Upload</button>
                </div>
            </form>

            <h3 class="section-title" style="margin: 20px 0 15px 0;">Arsip Pribadi Saya</h3>
            
            <?php if(empty($list_pribadi)): ?>
                <div style="text-align: center; padding: 30px; border: 1px dashed #cbd5e1; border-radius: 12px;">
                    <i class="fa-solid fa-folder-closed" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Belum ada arsip pribadi.</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach($list_pribadi as $bp): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: white; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                                <div style="background: #f5f3ff; color: #8b5cf6; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fa-solid fa-file-contract" style="font-size: 1.2rem;"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <div style="font-size: 0.9rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;"><?= htmlspecialchars($bp['nama_berkas']) ?></div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;"><i class="fa-regular fa-clock"></i> <?= date('d M Y', strtotime($bp['created_at'])) ?></div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <a href="/public/uploads/guru_berkas/<?= $bp['file_path'] ?>" target="_blank" style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-eye"></i></a>
                                <form method="POST" style="margin: 0;" id="form-delb-<?= $bp['id'] ?>">
                                    <input type="hidden" name="action" value="delete_pribadi">
                                    <input type="hidden" name="berkas_id" value="<?= $bp['id'] ?>">
                                    <button type="button" onclick="confirmDelete(<?= $bp['id'] ?>, '<?= addslashes($bp['nama_berkas']) ?>', 'form-delb-')" style="background: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function switchTab(tabId) {
    // Reset buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('inactive');
    });
    // Reset contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Set active
    let activeBtn = document.getElementById('btn_' + tabId);
    let activeContent = document.getElementById('content_' + tabId);
    
    if (activeBtn) {
        activeBtn.classList.remove('inactive');
        activeBtn.classList.add('active');
    }
    if (activeContent) {
        activeContent.classList.add('active');
    }
    
    // Update URL param without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabId);
    window.history.pushState({}, '', url);
}

function confirmDelete(id, nama, formPrefix) {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: "Anda yakin ingin menghapus " + nama + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formPrefix + id).submit();
        }
    });
}

// Add simple spinner for forms
document.querySelectorAll('form').forEach(f => {
    f.addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        if(btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
        }
    });
});

function changeMapel(direction) {
    let select = document.getElementById('filter-mapel');
    if(!select) return;
    let newIndex = select.selectedIndex + direction;
    if (newIndex < 0) newIndex = select.options.length - 1;
    if (newIndex >= select.options.length) newIndex = 0;
    select.selectedIndex = newIndex;
    filterMapel();
}

function filterMapel() {
    let select = document.getElementById('filter-mapel');
    if(!select) return;
    let filterVal = select.value;
    localStorage.setItem('selectedMapel', filterVal);
    let containers = document.querySelectorAll('.mapel-container');
    containers.forEach(container => {
        if(container.getAttribute('data-mapel') === filterVal) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    let select = document.getElementById('filter-mapel');
    if(select) {
        let savedMapel = localStorage.getItem('selectedMapel');
        if (savedMapel) {
            let options = Array.from(select.options);
            let exists = options.some(opt => opt.value === savedMapel);
            if (exists) {
                select.value = savedMapel;
            }
        }
        filterMapel(); // Inisialisasi mapel pertama saat dimuat agar sisanya tersembunyi
    }
});

function bukaModalUpload(kategori, mapelVal, mapelKey) {
    document.getElementById('input_kategori').value = kategori;
    document.getElementById('input_mapel_kelas').value = mapelVal;
    document.getElementById('display_kategori').innerText = kategori;
    document.getElementById('display_mapel_kelas').innerText = mapelKey;
    document.getElementById('modal-form-perangkat').style.display = 'flex';
}

function bukaModalEditPerangkat(id, namaBerkas, kategori, mapelKey) {
    document.getElementById('edit_berkas_id').value = id;
    document.getElementById('edit_input_nama_berkas').value = namaBerkas;
    document.getElementById('edit_input_kategori').value = kategori;
    document.getElementById('edit_display_mapel_kelas').innerText = mapelKey;
    document.getElementById('modal-edit-perangkat').style.display = 'flex';
}
</script>

<?php include 'footer_guru.php'; ?>