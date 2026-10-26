<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses absensi
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'absensi' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
if (!$stmt_app->fetch()) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Absensi Piket Global';
include 'header.php';

$message = '';
$status = '';

// Proses POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas_id = intval($_POST['kelas_id']);
    $tanggal = $_POST['tanggal'];
    
    if ($kelas_id > 0 && !empty($tanggal)) {
        try {
            $siakad_db->beginTransaction();
            
            // Hapus data absen kelas ini di tanggal ini agar tidak double
            $stmt_del = $siakad_db->prepare("DELETE FROM absensi_siswa WHERE kelas_id = ? AND tanggal = ?");
            $stmt_del->execute([$kelas_id, $tanggal]);
            
            // Insert ulang
            if (isset($_POST['absensi']) && is_array($_POST['absensi'])) {
                $stmt_absen = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
                foreach ($_POST['absensi'] as $siswa_id => $status_absen) {
                    $stmt_absen->execute([$siswa_id, $kelas_id, $tanggal, $status_absen]);
                }
            }
            $siakad_db->commit();
            
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists('\App\Services\AuditLogger')) {
                \App\Services\AuditLogger::dataChange('Absensi', 'UPDATE_GLOBAL', "kelas_id={$kelas_id}, tanggal={$tanggal} (APK)");
            }
            
            // --- KIRIM PUSH NOTIFICATION ---
            try {
                $tgl_indo = date('d-m-Y', strtotime($tanggal));
                
                // Kelompokkan siswa berdasarkan status absennya agar pengiriman lebih cepat (bulk)
                $grup_absen = [];
                foreach ($_POST['absensi'] as $siswa_id => $status_absen) {
                    $grup_absen[$status_absen][] = 'siswa_' . $siswa_id;
                }
                
                $notif_title = "Pembaruan Absensi";
                
                // Kirim notifikasi secara massal untuk setiap grup status (Hadir, Sakit, dll)
                foreach ($grup_absen as $status => $array_siswa) {
                    $notif_msg = "Kehadiran tanggal $tgl_indo tercatat: $status.";
                    sendOneSignalNotification($array_siswa, $notif_title, $notif_msg);
                }
            } catch (Exception $e) {}

            $message = 'Absensi global berhasil disimpan dan diperbarui!';
            $status = 'success';
        } catch (PDOException $e) {
            $siakad_db->rollBack();
            $message = 'Gagal menyimpan data: ' . $e->getMessage();
            $status = 'error';
        }
    } else {
        $message = 'Harap pilih kelas terlebih dahulu!';
        $status = 'warning';
    }
}

// Ambil data kelas dari core_db
$kelasList = [];
try {
    $stmt_k = $core_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas");
    $kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

?>

<div class="header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Piket</p>
            <h1>Absensi Keliling</h1>
        </div>
        <div class="avatar" style="color: #3b82f6; background: white;">
            <i class="fa-solid fa-user-check"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
        <div style="background: <?= $status=='success'?'#ecfdf5':'#fee2e2' ?>; border-left: 4px solid <?= $status=='success'?'#10b981':'#ef4444' ?>; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: <?= $status=='success'?'#065f46':'#b91c1c' ?>; font-size: 0.9rem; font-weight: 600;"><?= $message ?></p>
        </div>
    <?php endif; ?>

    <div class="data-card" <?= empty($message) ? 'style="margin-top: -2.5rem; z-index: 10;"' : '' ?>>
        <form action="" method="POST" id="formAbsen">
            
            <h3 class="section-title" style="margin-top: 0;">Pilih Target Absensi</h3>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Tanggal <span style="color:red">*</span></label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc;">
            </div>
            
            <div class="input-group" style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;">Kelas <span style="color:red">*</span></label>
                <select name="kelas_id" id="kelas_id" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc;" onchange="loadSiswa()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="wrapAbsensi" style="display: none;">
                <h3 class="section-title">Pendataan Siswa</h3>
                <div style="background: #eff6ff; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 0.8rem; color: #1e40af; border-left: 3px solid #3b82f6;">
                    <i class="fa-solid fa-circle-info"></i> Menandai absen lewat fitur ini akan <b>mengesampingkan dan menimpa (overwrite)</b> absensi sebelumnya pada tanggal dan kelas yang sama.
                </div>
                
                <div id="loadingSiswa" style="text-align: center; padding: 20px; display: none;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6;"></i>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 10px;">Memuat daftar siswa...</p>
                </div>
                
                <div id="listSiswa" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px;">
                    <!-- Diisi lewat JS -->
                </div>
                
                <button type="submit" class="btn" style="width: 100%; padding: 14px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; margin-bottom: 15px; cursor: pointer;">
                    <i class="fa-solid fa-save"></i> Simpan Data Absensi Global
                </button>
            </div>
        </form>
    </div>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<script>
function loadSiswa() {
    var kelas_id = document.getElementById('kelas_id').value;
    var wrap = document.getElementById('wrapAbsensi');
    var loading = document.getElementById('loadingSiswa');
    var list = document.getElementById('listSiswa');
    
    if (!kelas_id) {
        wrap.style.display = 'none';
        return;
    }
    
    wrap.style.display = 'block';
    list.innerHTML = '';
    loading.style.display = 'block';
    
    fetch('api_get_siswa_kelas.php?kelas_id=' + kelas_id)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            if(data.error) {
                list.innerHTML = '<div style="color:red; font-size:0.85rem;">Error: '+data.error+'</div>';
                return;
            }
            if(data.length === 0) {
                list.innerHTML = '<div style="text-align:center; color:#94a3b8; font-size:0.85rem; padding: 20px;">Tidak ada siswa di kelas ini.</div>';
                return;
            }
            
            data.forEach(function(s, index) {
                var no = index + 1;
                var html = `
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                    <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;">${no}. ${s.nama}</div>
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Hadir" checked> <span style="font-size:0.8rem; font-weight:600; color:#10b981;">Hadir</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Sakit"> <span style="font-size:0.8rem; font-weight:600; color:#eab308;">Sakit</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Izin"> <span style="font-size:0.8rem; font-weight:600; color:#3b82f6;">Izin</span>
                        </label>
                        <label style="flex:1; text-align:center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 5px; cursor: pointer;">
                            <input type="radio" name="absensi[${s.id}]" value="Alpa"> <span style="font-size:0.8rem; font-weight:600; color:#ef4444;">Alpa</span>
                        </label>
                    </div>
                </div>
                `;
                list.innerHTML += html;
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            list.innerHTML = '<div style="color:red; font-size:0.85rem;">Error koneksi jaringan.</div>';
        });
}
</script>

<?php include 'footer_guru.php'; ?>
