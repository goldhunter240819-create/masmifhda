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

$message = '';
$status = '';

if (!isset($_GET['id'])) {
    header("Location: rekap_jurnal.php");
    exit;
}
$jurnal_id = intval($_GET['id']);

// Ambil data jurnal
$stmt_j = $siakad_db->prepare("SELECT * FROM jurnal_guru WHERE id = ?");
$stmt_j->execute([$jurnal_id]);
$jurnal = $stmt_j->fetch(PDO::FETCH_ASSOC);

if (!$jurnal) {
    header("Location: rekap_jurnal.php");
    exit;
}

// Ambil absensi lama
$stmt_a = $siakad_db->prepare("SELECT siswa_id, status FROM absensi_permapel WHERE jurnal_id = ?");
$stmt_a->execute([$jurnal_id]);
$absen_lama = [];
while($row = $stmt_a->fetch(PDO::FETCH_ASSOC)) {
    $absen_lama[$row['siswa_id']] = $row['status'];
}

// Proses POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kelas & Mapel tidak diubah, ambil dari database jurnal lama
    $kelas_id = $jurnal['kelas_id'];
    $mapel_id = $jurnal['mapel_id'];
    $tanggal = $_POST['tanggal'];
    $materi = $_POST['materi'];
    $keterangan = $_POST['keterangan'];
    
    // Validasi input
    if ($kelas_id > 0 && $mapel_id > 0 && !empty($tanggal) && !empty($materi)) {
        // Ambil tahun ajaran aktif
        $activeTahunId = 1;
        $activeSemester = 1;
        try {
            $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            if ($thn = $stmtTahun->fetch()) {
                $activeTahunId = $thn['id'];
                $activeSemester = $thn['semester'];
            }
        } catch (Exception $e) {}

        // Cek apakah guru boleh mengajar kelas+mapel ini (berdasarkan jadwal pelajaran ATAU mengajar)
        $cek_jadwal = $siakad_db->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND kelas_id = ? AND mata_pelajaran_id = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        $cek_jadwal->execute([$guru_id, $kelas_id, $mapel_id, $activeTahunId, $activeSemester]);
        
        $cek_mengajar = $siakad_db->prepare("SELECT id FROM mengajar WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        $cek_mengajar->execute([$guru_id, $kelas_id, $mapel_id, $activeTahunId, $activeSemester]);
        
        if (!$cek_jadwal->fetch() && !$cek_mengajar->fetch()) {
            $message = 'Akses ditolak: Anda tidak memiliki jadwal atau tugas untuk kelas/mapel tersebut di SIAKAD pada semester aktif.';
            $status = 'error';
        } else {
        try {
            $siakad_db->beginTransaction();
            
            // Simpan data siswa lama untuk sinkronisasi ulang nanti
            $stmt_old_siswa = $siakad_db->prepare("SELECT DISTINCT siswa_id FROM absensi_permapel WHERE jurnal_id = ?");
            $stmt_old_siswa->execute([$jurnal_id]);
            $old_siswa_ids = $stmt_old_siswa->fetchAll(PDO::FETCH_COLUMN);
            $old_tanggal = $jurnal['tanggal'];
            
            // 1. Update Jurnal
            $stmt_jurnal = $siakad_db->prepare("UPDATE jurnal_guru SET tanggal = ?, materi = ?, keterangan = ? WHERE id = ?");
            $stmt_jurnal->execute([$tanggal, $materi, $keterangan, $jurnal_id]);
            
            // 2. Hapus absen lama agar tidak ganda
            $siakad_db->prepare("DELETE FROM absensi_permapel WHERE jurnal_id = ?")->execute([$jurnal_id]);
            
            // 3. Insert Absensi Siswa Baru
            if (isset($_POST['absensi']) && is_array($_POST['absensi'])) {
                $stmt_absen = $siakad_db->prepare("
                    INSERT INTO absensi_permapel 
                        (jurnal_id, guru_id, siswa_id, kelas_id, mapel_id, tanggal, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = VALUES(status)
                ");
                
                // Persiapan Integrasi Absen Pusat & Notifikasi
                $stmt_cek_pusat = $siakad_db->prepare("SELECT id, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmt_insert_pusat = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
                $stmt_update_pusat = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                
                $stmt_semua_absen = $siakad_db->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ?");
                
                $stmt_mapel_name = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
                $stmt_mapel_name->execute([$mapel_id]);
                $mapel_info = $stmt_mapel_name->fetch();
                $nama_mapel = $mapel_info ? $mapel_info['nama_mapel'] : 'Mata Pelajaran';
                
                $stmt_siswa_info = $core_db->prepare("SELECT nama, user_id FROM siswa WHERE id = ?");
                
                require_once __DIR__ . '/../vendor/autoload.php';
                
                foreach ($_POST['absensi'] as $siswa_id_absen => $status_absen) {
                    $s_id = intval($siswa_id_absen);
                    
                    // ATURAN MUTLAK: Cek pusat, jika Izin/Sakit/Alpa maka override status guru
                    $stmt_cek_pusat->execute([$s_id, $tanggal]);
                    $pusat_row = $stmt_cek_pusat->fetch();
                    if ($pusat_row && in_array($pusat_row['status'], ['Izin', 'Sakit', 'Alpa', 'Alpha', 'A'])) {
                        $status_absen = $pusat_row['status'];
                    }
                    
                    // Simpan absen mapel
                    $stmt_absen->execute([$jurnal_id, $guru_id, $s_id, $kelas_id, $mapel_id, $tanggal, $status_absen]);
                    
                    // Cek semua absen mapel hari ini untuk siswa ini
                    $stmt_semua_absen->execute([$s_id, $tanggal]);
                    $semua_status = $stmt_semua_absen->fetchAll(PDO::FETCH_COLUMN);
                    
                    $ada_hadir = in_array('Hadir', $semua_status);
                    $ada_alpa = in_array('Alpa', $semua_status);
                    
                    // Gunakan fungsi sinkronisasi agar seragam dengan gate dan aturan mutlak
                    sinkronisasi_status_pusat($s_id, $tanggal, $core_db, $siakad_db);
                    
                    // Kirim Notifikasi OneSignal jika absen = Sakit/Izin/Alpa (hanya untuk status yang berubah jika perlu, tapi kita skip notif di edit untuk menghindari spam)
                }
            }
            
            // Sinkronisasi ulang siswa lama yang tidak masuk di POST (misal karena ganti tanggal)
            if ($old_tanggal != $tanggal) {
                foreach ($old_siswa_ids as $s_id) {
                    $stmt_semua_absen->execute([$s_id, $old_tanggal]);
                    $semua_status = $stmt_semua_absen->fetchAll(PDO::FETCH_COLUMN);
                    if (empty($semua_status)) {
                        $siakad_db->prepare("DELETE FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?")->execute([$s_id, $old_tanggal]);
                    } else {
                        sinkronisasi_status_pusat($s_id, $old_tanggal, $core_db, $siakad_db);
                    }
                }
            }
            
            $siakad_db->commit();
            
            // Update data $jurnal di memori agar UI terupdate
            $jurnal['tanggal'] = $tanggal;
            $jurnal['materi'] = $materi;
            $jurnal['keterangan'] = $keterangan;
            $absen_lama = $_POST['absensi'] ?? [];
            
            $message = 'Jurnal dan absensi berhasil diperbarui!';
            $status = 'success';
        } catch (PDOException $e) {
            $siakad_db->rollBack();
            $message = 'Gagal menyimpan data: ' . $e->getMessage();
            $status = 'error';
        }
        } // end else (guru berhak)
    } else {
        $message = 'Harap isi semua kolom wajib!';
        $status = 'warning';
    }
}

// Ambil tahun ajaran aktif
$activeTahunId = 1;
$activeSemester = 1;
try {
    $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
        $activeSemester = $thn['semester'];
    }
} catch (Exception $e) {}

// Ambil kelas & mapel yang diampu guru ini (Gabungan dari tabel mengajar & jadwal_pelajaran)
$kelasList = [];
$mapelList = [];
try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas, k.tingkat
        FROM (
            SELECT kelas_id FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT kelas_id FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN aula_db.kelas k ON k.id = combined.kelas_id
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("
        SELECT DISTINCT mp.id, mp.nama_mapel
        FROM (
            SELECT mapel_id as mapel FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN mata_pelajaran mp ON mp.id = combined.mapel
        ORDER BY mp.nama_mapel
    ");
    $stmt_m->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $mapelList = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Edit Jurnal & Absensi';
include 'header.php';

// Ambil daftar siswa kelas ini
$stmt_siswa = $core_db->prepare("SELECT id, nama FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
$stmt_siswa->execute([$jurnal['kelas_id']]);
$siswaList = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div class="header-top">
        <a href="rekap_jurnal.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="text-align: center; flex: 1;">
            <h1 style="margin: 0; font-size: 1.2rem;">Edit Jurnal & Absensi</h1>
        </div>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
        <div style="background: <?= $status=='success'?'#ecfdf5':'#fee2e2' ?>; border-left: 4px solid <?= $status=='success'?'#10b981':'#ef4444' ?>; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: <?= $status=='success'?'#065f46':'#b91c1c' ?>; font-size: 0.9rem; font-weight: 600;"><?= $message ?></p>
        </div>
    <?php endif; ?>

    <div class="data-card" <?= empty($message) ? 'style="margin-top: -2.5rem; z-index: 10;"' : '' ?>>
        <form action="" method="POST" id="formJurnal">
            
            <h3 class="section-title" style="margin-top: 0;">Informasi Jurnal</h3>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Kelas</label>
                <select disabled style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #e2e8f0; color: #64748b;">
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id']==$jurnal['kelas_id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="kelas_id" value="<?= $jurnal['kelas_id'] ?>">
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Mata Pelajaran</label>
                <select disabled style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #e2e8f0; color: #64748b;">
                    <?php foreach($mapelList as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $m['id']==$jurnal['mapel_id']?'selected':'' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="mapel_id" value="<?= $jurnal['mapel_id'] ?>">
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Tanggal <span style="color:red">*</span></label>
                <input type="date" name="tanggal" required value="<?= htmlspecialchars($jurnal['tanggal']) ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc;">
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Topik / Materi Pembelajaran <span style="color:red">*</span></label>
                <textarea name="materi" required rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; font-family: inherit;"><?= htmlspecialchars($jurnal['materi']) ?></textarea>
            </div>
            
            <div class="input-group" style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Catatan Tambahan</label>
                <textarea name="keterangan" rows="2" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; font-family: inherit;"><?= htmlspecialchars($jurnal['keterangan']) ?></textarea>
            </div>
            
            <div id="wrapAbsensi">
                <h3 class="section-title">Edit Absensi Siswa</h3>
                <div style="background: #eff6ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 0.8rem; color: #1e40af; border-left: 3px solid #3b82f6;">
                    <i class="fa-solid fa-circle-info"></i> Ubah status kehadiran siswa jika terdapat kesalahan data sebelumnya.
                </div>
                
                <div id="listSiswa" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px;">
                    <?php 
                    $no = 1;
                    foreach($siswaList as $s): 
                        $status_absen = isset($absen_lama[$s['id']]) ? $absen_lama[$s['id']] : 'Hadir';
                        if ($status_absen == 'Alpha') $status_absen = 'Alpa';
                    ?>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= $no++ ?>. <?= htmlspecialchars($s['nama']) ?></div>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                                <input type="radio" name="absensi[<?= $s['id'] ?>]" value="Hadir" <?= $status_absen=='Hadir'?'checked':'' ?>> <span style="font-size:0.8rem; font-weight:600; color:#10b981;">Hadir</span>
                            </label>
                            <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                                <input type="radio" name="absensi[<?= $s['id'] ?>]" value="Sakit" <?= $status_absen=='Sakit'?'checked':'' ?>> <span style="font-size:0.8rem; font-weight:600; color:#eab308;">Sakit</span>
                            </label>
                            <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                                <input type="radio" name="absensi[<?= $s['id'] ?>]" value="Izin" <?= $status_absen=='Izin'?'checked':'' ?>> <span style="font-size:0.8rem; font-weight:600; color:#3b82f6;">Izin</span>
                            </label>
                            <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                                <input type="radio" name="absensi[<?= $s['id'] ?>]" value="Alpa" <?= $status_absen=='Alpa'?'checked':'' ?>> <span style="font-size:0.8rem; font-weight:600; color:#ef4444;">Alpa</span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($siswaList) == 0): ?>
                        <div style="text-align:center; color:#94a3b8; font-size:0.85rem; padding: 20px;">Tidak ada siswa di kelas ini.</div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; padding: 14px; background: #f59e0b; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; margin-bottom: 15px; cursor: pointer;">
                    <i class="fa-solid fa-save"></i> Perbarui Jurnal & Absensi
                </button>
            </div>
        </form>
    </div>
</div>



<?php include 'footer_guru.php'; ?>
