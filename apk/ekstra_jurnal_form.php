<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_ekstra = $_GET['ekstra'] ?? '';
$tahun_ajaran = $_GET['ta'] ?? '';
$id = $_GET['id'] ?? null;

if (empty($nama_ekstra) || empty($tahun_ajaran)) {
    echo "<script>alert('Data tidak valid.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

// Cek hak akses pembina
$stmt = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = :ekstra AND tahun_ajaran = :ta");
$stmt->execute(['gid' => $guru_id, 'ekstra' => $nama_ekstra, 'ta' => $tahun_ajaran]);
if (!$stmt->fetch()) {
    echo "<script>alert('Akses ditolak.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

$db_siakad = $siakad_db;

// Save Handle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $materi = $_POST['materi'] ?? '';
    $absensi = $_POST['absensi'] ?? []; 
    
    $dokumentasi = null;
    if ($id) {
        $jurnal_old = $db_siakad->query("SELECT dokumentasi FROM ekstra_jurnal WHERE id = " . intval($id))->fetch();
        $dokumentasi = $jurnal_old['dokumentasi'] ?? null;
    }

    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../public/uploads/ekstra_jurnal/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('jurnal_') . '.' . $ext;
        if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $uploadDir . $filename)) {
            if ($dokumentasi && file_exists($uploadDir . $dokumentasi)) {
                unlink($uploadDir . $dokumentasi);
            }
            $dokumentasi = $filename;
        }
    }

    $db_siakad->beginTransaction();
    try {
        if ($id) {
            $upd = $db_siakad->prepare("UPDATE ekstra_jurnal SET tanggal = ?, materi = ?, dokumentasi = ? WHERE id = ?");
            $upd->execute([$tanggal, $materi, $dokumentasi, $id]);
            
            $db_siakad->query("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($id));
            $jurnal_id = $id;
        } else {
            $ins = $db_siakad->prepare("INSERT INTO ekstra_jurnal (nama_ekstra, guru_id, tahun_ajaran, tanggal, materi, dokumentasi) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$nama_ekstra, $guru_id, $tahun_ajaran, $tanggal, $materi, $dokumentasi]);
            $jurnal_id = $db_siakad->lastInsertId();
        }

        if (!empty($absensi)) {
            $stmt_absen = $db_siakad->prepare("INSERT INTO ekstra_absen (jurnal_id, siswa_id, status_kehadiran) VALUES (?, ?, ?)");
            foreach ($absensi as $s_id => $status) {
                $stmt_absen->execute([$jurnal_id, $s_id, $status]);
            }
        }
        
        $db_siakad->commit();
        header("Location: ekstra_jurnal.php?ekstra=" . urlencode($nama_ekstra) . "&ta=" . urlencode($tahun_ajaran) . "&success=1");
        exit;
    } catch (\Exception $e) {
        $db_siakad->rollBack();
        $msg = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Data Load (Edit Mode)
$jurnal = null;
$absen_map = [];
if ($id) {
    $stmt = $db_siakad->prepare("SELECT * FROM ekstra_jurnal WHERE id = ? AND nama_ekstra = ?");
    $stmt->execute([$id, $nama_ekstra]);
    $jurnal = $stmt->fetch();
    if (!$jurnal) { echo "Jurnal tidak ditemukan."; exit; }
    
    $absenList = $db_siakad->query("SELECT siswa_id, status_kehadiran FROM ekstra_absen WHERE jurnal_id = " . intval($id))->fetchAll();
    foreach($absenList as $ab) {
        $absen_map[$ab['siswa_id']] = $ab['status_kehadiran'];
    }
}

// Ambil anggota
$anggotaList = $db_siakad->query("SELECT ea.siswa_id, s.nama, k.nama_kelas 
                                FROM ekstra_anggota ea 
                                JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                WHERE ea.nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                AND ea.tahun_ajaran = ".$db_siakad->quote($tahun_ajaran)."
                                ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = ($id ? 'Edit Jurnal' : 'Tambah Jurnal');
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p><?= $id ? 'Edit Jurnal' : 'Tambah Jurnal' ?></p>
            <h1><?= htmlspecialchars($nama_ekstra) ?></h1>
        </div>
        <a href="ekstra_jurnal.php?ekstra=<?= urlencode($nama_ekstra) ?>&ta=<?= urlencode($tahun_ajaran) ?>" style="color: white; font-size: 1.5rem; text-decoration: none;">
            <i class="fa-solid fa-circle-xmark"></i>
        </a>
    </div>
</div>

<div class="main-content">
    <?php if (isset($msg)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #fecaca; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="data-card" style="margin-top: <?= isset($msg) ? '0' : '-2.5rem' ?>; z-index: 10;">
            <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-pen-to-square" style="color: #10b981; margin-right: 5px;"></i> Data Kegiatan</h3>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" required value="<?= $jurnal ? $jurnal['tanggal'] : date('Y-m-d') ?>" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Materi / Topik Pembinaan</label>
                <textarea name="materi" required rows="3" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem; font-family: inherit; resize: vertical;" placeholder="Tuliskan materi atau kegiatan hari ini..."><?= $jurnal ? htmlspecialchars($jurnal['materi']) : '' ?></textarea>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Dokumentasi Foto</label>
                <input type="file" name="dokumentasi" accept="image/*" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: #f8fafc;">
                <?php if ($jurnal && $jurnal['dokumentasi']): ?>
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;"><i class="fa-solid fa-image" style="color: #10b981;"></i> Foto sudah diupload. Pilih file baru untuk mengganti.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="data-card" style="margin-top: 15px; margin-bottom: 30px;">
            <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-list-check" style="color: #10b981; margin-right: 5px;"></i> Presensi Anggota</h3>
            
            <?php if (empty($anggotaList)): ?>
                <div style="text-align: center; padding: 20px; color: #64748b; font-size: 0.85rem;">
                    Belum ada anggota. Silakan tambah anggota terlebih dahulu dari menu Anggota.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($anggotaList as $idx => $anggota): 
                        $s_id = $anggota['siswa_id'];
                        $status_now = $absen_map[$s_id] ?? 'Hadir';
                    ?>
                        <div style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px;">
                            <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;"><?= $idx + 1 ?>. <?= htmlspecialchars($anggota['nama']) ?></div>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px; margin-bottom: 8px;"><i class="fa-solid fa-door-open" style="margin-right: 3px;"></i> Kelas <?= htmlspecialchars($anggota['nama_kelas']) ?></div>
                            
                            <div class="flex" style="display: flex; gap: 10px; background: #f8fafc; padding: 5px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <label style="flex: 1; text-align: center; font-size: 0.8rem; padding: 6px; border-radius: 6px; cursor: pointer; <?= $status_now == 'Hadir' ? 'background: #dcfce7; color: #16a34a; font-weight: 700; border: 1px solid #bbf7d0;' : 'color: #64748b; border: 1px solid transparent;' ?>">
                                    <input type="radio" name="absensi[<?= $s_id ?>]" value="Hadir" <?= $status_now == 'Hadir' ? 'checked' : '' ?> onchange="updateRadioStyle(this)" style="display: none;"> Hadir
                                </label>
                                <label style="flex: 1; text-align: center; font-size: 0.8rem; padding: 6px; border-radius: 6px; cursor: pointer; <?= $status_now == 'Sakit' ? 'background: #fef3c7; color: #d97706; font-weight: 700; border: 1px solid #fde68a;' : 'color: #64748b; border: 1px solid transparent;' ?>">
                                    <input type="radio" name="absensi[<?= $s_id ?>]" value="Sakit" <?= $status_now == 'Sakit' ? 'checked' : '' ?> onchange="updateRadioStyle(this)" style="display: none;"> Sakit
                                </label>
                                <label style="flex: 1; text-align: center; font-size: 0.8rem; padding: 6px; border-radius: 6px; cursor: pointer; <?= $status_now == 'Izin' ? 'background: #dbeafe; color: #1d4ed8; font-weight: 700; border: 1px solid #bfdbfe;' : 'color: #64748b; border: 1px solid transparent;' ?>">
                                    <input type="radio" name="absensi[<?= $s_id ?>]" value="Izin" <?= $status_now == 'Izin' ? 'checked' : '' ?> onchange="updateRadioStyle(this)" style="display: none;"> Izin
                                </label>
                                <label style="flex: 1; text-align: center; font-size: 0.8rem; padding: 6px; border-radius: 6px; cursor: pointer; <?= $status_now == 'Alpa' ? 'background: #fee2e2; color: #dc2626; font-weight: 700; border: 1px solid #fecaca;' : 'color: #64748b; border: 1px solid transparent;' ?>">
                                    <input type="radio" name="absensi[<?= $s_id ?>]" value="Alpa" <?= $status_now == 'Alpa' ? 'checked' : '' ?> onchange="updateRadioStyle(this)" style="display: none;"> Alpa
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="ekstra_jurnal.php?ekstra=<?= urlencode($nama_ekstra) ?>&ta=<?= urlencode($tahun_ajaran) ?>" style="flex: 1; text-align: center; padding: 12px; background: #f1f5f9; color: #475569; border-radius: 8px; font-weight: 700; text-decoration: none;">Batal</a>
            <button type="submit" style="flex: 2; border: none; background: #10b981; color: white; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
function updateRadioStyle(radio) {
    const labels = radio.closest('.flex').querySelectorAll('label');
    labels.forEach(l => {
        l.style.background = 'transparent';
        l.style.color = '#64748b';
        l.style.fontWeight = '400';
        l.style.borderColor = 'transparent';
    });
    
    const label = radio.closest('label');
    label.style.fontWeight = '700';
    if (radio.value == 'Hadir') {
        label.style.background = '#dcfce7';
        label.style.color = '#16a34a';
        label.style.borderColor = '#bbf7d0';
    } else if (radio.value == 'Sakit') {
        label.style.background = '#fef3c7';
        label.style.color = '#d97706';
        label.style.borderColor = '#fde68a';
    } else if (radio.value == 'Izin') {
        label.style.background = '#dbeafe';
        label.style.color = '#1d4ed8';
        label.style.borderColor = '#bfdbfe';
    } else if (radio.value == 'Alpa') {
        label.style.background = '#fee2e2';
        label.style.color = '#dc2626';
        label.style.borderColor = '#fecaca';
    }
}
</script>

<!-- CSS fix for flex querySelector in JS -->
<style>
.flex { display: flex; }
</style>

<?php include 'footer_guru.php'; ?>
