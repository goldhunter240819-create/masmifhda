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
    // Pastikan hanya bisa hapus poin yang diinput oleh guru ini
    $stmt_del = $core_db->prepare("DELETE FROM bk_poin WHERE id = ? AND guru_id = ? AND tanggal = CURDATE()");
    $stmt_del->execute([$del_id, $guru_id]);
    header("Location: bk_poin.php?msg=del_success");
    exit;
}

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_poin') {
    $siswa_id = intval($_POST['siswa_id']);
    $kategori_id = intval($_POST['kategori_id']);
    $poin = intval($_POST['poin']);
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    
    if ($siswa_id > 0 && $kategori_id > 0 && $poin > 0) {
        try {
            $stmt_ins = $core_db->prepare("INSERT INTO bk_poin (siswa_id, kategori_id, poin, tahun_ajaran_id, guru_id, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_ins->execute([$siswa_id, $kategori_id, $poin, $activeTahunId, $guru_id, $tanggal, $keterangan]);
            header("Location: bk_poin.php?msg=success");
            exit;
        } catch (Exception $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    } else {
        $error = "Data tidak lengkap!";
    }
}

// Ajax Get Siswa
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_siswa' && isset($_GET['kelas_id'])) {
    $kelas_id = intval($_GET['kelas_id']);
    // Ambil siswa aktif dari tabel siswa_aktif
    $stmt_s = $core_db->prepare("SELECT s.id, s.nama FROM siswa s JOIN riwayat_kelas_siswa sa ON s.id = sa.siswa_id WHERE sa.kelas_id = ? AND sa.tahun_ajaran_id = ? ORDER BY s.nama ASC");
    $stmt_s->execute([$kelas_id, $activeTahunId]);
    $siswaList = $stmt_s->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($siswaList);
    exit;
}

// Data List Kelas
$stmt_k = $core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas");
$kelasList = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

// Data List Kategori
$stmt_kat = $core_db->query("SELECT * FROM bk_kategori ORDER BY tipe, tingkat, nama_kategori");
$kategoriList = $stmt_kat->fetchAll(PDO::FETCH_ASSOC);

// Data Riwayat Hari Ini (Input by this teacher today)
$stmt_riwayat = $core_db->prepare("
    SELECT bp.*, s.nama as nama_siswa, bk.nama_kategori, bk.tipe, k.nama_kelas
    FROM bk_poin bp
    JOIN siswa s ON bp.siswa_id = s.id
    JOIN bk_kategori bk ON bp.kategori_id = bk.id
    LEFT JOIN riwayat_kelas_siswa sa ON s.id = sa.siswa_id AND sa.tahun_ajaran_id = bp.tahun_ajaran_id
    LEFT JOIN kelas k ON sa.kelas_id = k.id
    WHERE bp.guru_id = ? AND bp.tanggal = CURDATE()
    ORDER BY bp.created_at DESC
");
$stmt_riwayat->execute([$guru_id]);
$riwayatList = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Poin Disiplin & Prestasi';
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
        background: #ef4444; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: bold; width: 100%; font-size: 1rem; cursor: pointer; transition: 0.2s; margin-top: 10px;
    }
    .btn-submit:hover { background: #dc2626; }
    
    .riwayat-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }
    .riwayat-card.tipe-pelanggaran { border-left: 4px solid #ef4444; }
    .riwayat-card.tipe-prestasi { border-left: 4px solid #10b981; }
    
    .badge-poin {
        padding: 4px 10px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.85rem;
    }
    .badge-poin.pelanggaran { background: #fee2e2; color: #ef4444; }
    .badge-poin.prestasi { background: #d1fae5; color: #10b981; }
    
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

<div class="header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding-bottom: 2.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Bimbingan Konseling</p>
            <h1>Input Poin Disiplin</h1>
        </div>
        <a href="aplikasi_saya.php" class="avatar" style="color: #ef4444; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Poin berhasil dicatat.', confirmButtonColor: '#ef4444' });
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'del_success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Catatan poin telah dibatalkan.', confirmButtonColor: '#ef4444' });
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
            <input type="hidden" name="action" value="save_poin">
            
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
                <label>Jenis Catatan <span style="color:red">*</span></label>
                <div style="display: flex; gap: 10px;">
                    <label style="flex: 1; background: #fee2e2; border: 2px solid #fca5a5; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer;">
                        <input type="radio" name="tipe" value="pelanggaran" checked onchange="filterKategori()" style="margin-right: 5px;">
                        <span style="color: #ef4444;">Pelanggaran</span>
                    </label>
                    <label style="flex: 1; background: #d1fae5; border: 2px solid #6ee7b7; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer;">
                        <input type="radio" name="tipe" value="prestasi" onchange="filterKategori()" style="margin-right: 5px;">
                        <span style="color: #10b981;">Prestasi</span>
                    </label>
                </div>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Kategori <span style="color:red">*</span></label>
                <select name="kategori_id" id="kategori_id" class="form-control" required onchange="updatePoinDefault()">
                    <option value="">-- Pilih Kategori --</option>
                </select>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Poin (Dapat Diubah) <span style="color:red">*</span></label>
                <input type="number" name="poin" id="poin" class="form-control" required min="1">
                <small style="color: #94a3b8; display: block; margin-top: 5px;"><i class="fa-solid fa-circle-info"></i> Nilai diambil otomatis dari kategori, namun Anda bisa mengubahnya jika diperlukan.</small>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Tanggal Kejadian <span style="color:red">*</span></label>
                <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label>Catatan / Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="2" class="form-control" placeholder="Contoh: Datang terlambat 30 menit"></textarea>
            </div>
            
            <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> Simpan Catatan</button>
        </form>
    </div>
    
    <!-- Riwayat Hari Ini -->
    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 15px; margin-top: 30px;">Input Anda Hari Ini</h3>
    
    <?php if(count($riwayatList) > 0): ?>
        <?php foreach($riwayatList as $r): ?>
        <div class="riwayat-card tipe-<?= $r['tipe'] ?>">
            <div style="flex: 1;">
                <div style="font-weight: 800; color: #1e293b; font-size: 1rem; margin-bottom: 3px;"><?= htmlspecialchars($r['nama_siswa']) ?></div>
                <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">Kelas <?= htmlspecialchars($r['nama_kelas']) ?> | <i class="fa-regular fa-clock"></i> <?= date('H:i', strtotime($r['created_at'])) ?></div>
                <div style="font-size: 0.85rem; color: #475569;"><?= htmlspecialchars($r['nama_kategori']) ?></div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <span class="badge-poin <?= $r['tipe'] ?>"><?= ($r['tipe'] == 'pelanggaran' ? '-' : '+') . $r['poin'] ?> Poin</span>
                <a href="?delete_id=<?= $r['id'] ?>" class="btn-del" onclick="return confirm('Batalkan input poin ini?')"><i class="fa-solid fa-trash"></i></a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            Belum ada catatan yang Anda masukkan hari ini.
        </div>
    <?php endif; ?>
    
    <div style="height: 30px;"></div>
</div>

<script>
// Data Kategori dari PHP ke JS
const kategoriData = <?= json_encode($kategoriList) ?>;

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
    
    fetch('bk_poin.php?ajax=get_siswa&kelas_id=' + kelasId)
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

function filterKategori() {
    const tipe = document.querySelector('input[name="tipe"]:checked').value;
    const katSelect = document.getElementById('kategori_id');
    
    let options = '<option value="">-- Pilih Kategori --</option>';
    kategoriData.filter(k => k.tipe === tipe).forEach(k => {
        // Tampilkan nama dan poin referensi
        options += `<option value="${k.id}" data-poin="${k.poin}">[${k.tingkat ? k.tingkat.toUpperCase() : '-'}] ${k.nama_kategori} (${k.poin} Poin)</option>`;
    });
    
    katSelect.innerHTML = options;
    document.getElementById('poin').value = '';
}

function updatePoinDefault() {
    const katSelect = document.getElementById('kategori_id');
    const selectedOption = katSelect.options[katSelect.selectedIndex];
    
    if(selectedOption && selectedOption.dataset.poin) {
        document.getElementById('poin').value = selectedOption.dataset.poin;
    } else {
        document.getElementById('poin').value = '';
    }
}

// Inisialisasi awal
document.addEventListener('DOMContentLoaded', function() {
    filterKategori();
});
</script>

<?php include 'footer_guru.php'; ?>