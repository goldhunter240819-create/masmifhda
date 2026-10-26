<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_guru = $_SESSION['nama'] ?? 'Guru';

// Get active semester
$activeTahunId = 1;
try {
    $stmtTahun = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
    }
} catch (Exception $e) {}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt_del = $core_db->prepare("DELETE FROM bk_jurnal WHERE id = ? AND guru_id = ? AND tanggal = CURDATE()");
    $stmt_del->execute([$del_id, $guru_id]);
    header("Location: bk_jurnal.php?msg=del_success");
    exit;
}

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_jurnal') {
    $siswa_id = intval($_POST['siswa_id']);
    $jenis_bimbingan = $_POST['jenis_bimbingan'] ?? '';
    $topik = $_POST['topik'] ?? '';
    $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
    $status_penanganan = $_POST['status_penanganan'] ?? 'Selesai';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    
    if ($siswa_id > 0 && !empty($jenis_bimbingan) && !empty($topik)) {
        try {
            $stmt_ins = $core_db->prepare("INSERT INTO bk_jurnal (tanggal, siswa_id, jenis_bimbingan, topik, tindak_lanjut, status_penanganan, guru_id, tahun_ajaran_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_ins->execute([$tanggal, $siswa_id, $jenis_bimbingan, $topik, $tindak_lanjut, $status_penanganan, $guru_id, $activeTahunId]);
            header("Location: bk_jurnal.php?msg=success");
            exit;
        } catch (Exception $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    } else {
        $error = "Data wajib tidak lengkap!";
    }
}

// Ajax Get Siswa
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_siswa' && isset($_GET['kelas_id'])) {
    $kelas_id = intval($_GET['kelas_id']);
    $stmt_s = $core_db->prepare("SELECT s.id, s.nama FROM riwayat_kelas_siswa r JOIN siswa s ON r.siswa_id = s.id WHERE r.kelas_id = ? ORDER BY s.nama ASC");
    $stmt_s->execute([$kelas_id]);
    $siswaList = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($siswaList);
    exit;
}

// Data List Kelas
$stmt_k = $core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas");
$kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

// Data Riwayat Hari Ini
$stmt_riwayat = $core_db->prepare("
    SELECT bj.*, s.nama as nama_siswa, k.nama_kelas
    FROM bk_jurnal bj
    JOIN siswa s ON bj.siswa_id = s.id
    LEFT JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id
    LEFT JOIN kelas k ON r.kelas_id = k.id
    WHERE bj.guru_id = ? AND bj.tanggal = CURDATE()
    ORDER BY bj.created_at DESC
");
$stmt_riwayat->execute([$guru_id]);
$riwayatList = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Jurnal Bimbingan Konseling';
include 'header.php';
?>
<style>
    .data-card {
        background: var(--card-bg, #ffffff);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .input-group label {
        display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;
    }
    .form-control {
        width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; font-family: inherit;
    }
    .btn-submit {
        background: #6366f1; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: bold; width: 100%; font-size: 1rem; cursor: pointer; transition: 0.2s; margin-top: 10px;
    }
    .btn-submit:hover { background: #4f46e5; }
    
    .riwayat-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        background: #fff;
        border-left: 4px solid #6366f1;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.75rem;
    }
    .badge-status.selesai { background: #d1fae5; color: #10b981; }
    .badge-status.pantauan { background: #fef08a; color: #ca8a04; }
    
    .btn-del {
        color: #ef4444;
        background: #fee2e2;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); padding-bottom: 2.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Bimbingan Konseling</p>
            <h1>Jurnal BK (Konseling)</h1>
        </div>
        <a href="aplikasi_saya.php" class="avatar" style="color: #6366f1; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Jurnal BK berhasil disimpan.', confirmButtonColor: '#6366f1' });
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'del_success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Jurnal telah dibatalkan.', confirmButtonColor: '#6366f1' });
            });
        </script>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="background: #fee2e2; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: bold;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">
        <form method="POST" action="">
            <input type="hidden" name="action" value="save_jurnal">
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Tanggal <span style="color:red">*</span></label>
                <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <label>Kelas <span style="color:red">*</span></label>
                <select name="kelas_id" id="kelas_id" class="form-control" required onchange="loadSiswa()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Nama Siswa <span style="color:red">*</span></label>
                <select name="siswa_id" id="siswa_id" class="form-control" required disabled>
                    <option value="">Pilih kelas terlebih dahulu...</option>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Jenis Bimbingan <span style="color:red">*</span></label>
                <select name="jenis_bimbingan" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Bimbingan Pribadi">Bimbingan Pribadi</option>
                    <option value="Bimbingan Sosial">Bimbingan Sosial</option>
                    <option value="Bimbingan Belajar">Bimbingan Belajar</option>
                    <option value="Bimbingan Karir">Bimbingan Karir</option>
                    <option value="Mediasi / Resolusi Konflik">Mediasi / Resolusi Konflik</option>
                    <option value="Pemanggilan Orang Tua">Pemanggilan Orang Tua / Wali</option>
                    <option value="Lainnya">Lainnya...</option>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Topik / Masalah <span style="color:red">*</span></label>
                <textarea name="topik" rows="2" class="form-control" required placeholder="Contoh: Kesulitan mengatur waktu belajar dan sering tertidur di kelas"></textarea>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Tindak Lanjut / Solusi (Opsional)</label>
                <textarea name="tindak_lanjut" rows="3" class="form-control" placeholder="Contoh: Membuat jadwal harian bersama siswa, memberikan teguran ringan"></textarea>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Status Penanganan <span style="color:red">*</span></label>
                <select name="status_penanganan" class="form-control" required>
                    <option value="Selesai">Selesai (Tuntas)</option>
                    <option value="Dalam Pantauan">Masih Dalam Pantauan</option>
                </select>
            </div>
            
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save"></i> Simpan Jurnal BK</button>
        </form>
    </div>
    
    <!-- Riwayat Hari Ini -->
    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 15px; margin-top: 30px;">Catatan Jurnal Anda Hari Ini</h3>
    
    <?php if(count($riwayatList) > 0): ?>
        <?php foreach($riwayatList as $r): 
            $badge_class = ($r['status_penanganan'] == 'Selesai') ? 'selesai' : 'pantauan';
        ?>
        <div class="riwayat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 1rem;"><?= htmlspecialchars($r['nama_siswa']) ?></div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">Kelas <?= htmlspecialchars($r['nama_kelas']) ?> | <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($r['jenis_bimbingan']) ?></div>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                    <span class="badge-status <?= $badge_class ?>"><?= htmlspecialchars($r['status_penanganan']) ?></span>
                    <a href="?delete_id=<?= $r['id'] ?>" class="btn-del" onclick="return confirm('Hapus jurnal ini?')"><i class="fa-solid fa-trash"></i></a>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 10px; border-radius: 8px; font-size: 0.85rem; color: #475569; border: 1px solid #e2e8f0;">
                <strong>Topik:</strong> <?= nl2br(htmlspecialchars($r['topik'])) ?>
                <?php if(!empty($r['tindak_lanjut'])): ?>
                <hr style="margin: 8px 0; border: 0; border-top: 1px dashed #cbd5e1;">
                <strong>Tindak Lanjut:</strong> <?= nl2br(htmlspecialchars($r['tindak_lanjut'])) ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            Belum ada jurnal yang dicatat hari ini.
        </div>
    <?php endif; ?>
    
    <div style="height: 30px;"></div>
</div>

<script>
function loadSiswa() {
    const kelasId = document.getElementById('kelas_id').value;
    const siswaSelect = document.getElementById('siswa_id');
    
    if (!kelasId) {
        siswaSelect.innerHTML = '<option value="">Pilih kelas terlebih dahulu...</option>';
        siswaSelect.disabled = true;
        return;
    }
    
    siswaSelect.innerHTML = '<option value="">Sedang memuat...</option>';
    siswaSelect.disabled = true;
    
    fetch('bk_jurnal.php?ajax=get_siswa&kelas_id=' + kelasId)
        .then(response => response.json())
        .then(data => {
            if(data.length === 0) {
                siswaSelect.innerHTML = '<option value="">Tidak ada siswa aktif</option>';
            } else {
                let options = '<option value="">-- Pilih Siswa --</option>';
                data.forEach(s => {
                    options += `<option value="${s.id}">${s.nama}</option>`;
                });
                siswaSelect.innerHTML = options;
                siswaSelect.disabled = false;
            }
        });
}
</script>

<?php include 'footer_guru.php'; ?>