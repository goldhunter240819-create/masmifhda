<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
if (!$guru_id) {
    header("Location: wakakur_supervisi_kelas.php");
    exit;
}

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
$tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

// Get guru info
$stmtGuru = $core_db->prepare("SELECT id, nama, nip, foto FROM guru WHERE id = ?");
$stmtGuru->execute([$guru_id]);
$guru = $stmtGuru->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    header("Location: wakakur_supervisi_kelas.php");
    exit;
}

// Get existing supervisi
$stmtSup = $core_db->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
$stmtSup->execute([$tahun_ajaran_id, $guru_id]);
$supervisi = $stmtSup->fetch(PDO::FETCH_ASSOC);

$instrumen = [];
if ($supervisi && !empty($supervisi['instrumen_json'])) {
    $instrumen = json_decode($supervisi['instrumen_json'], true);
}

$page_title = 'Form Evaluasi Kelas';
include 'header.php';

$foto_url = !empty($guru['foto']) ? (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $guru['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($guru['nama']) . '&background=f1f5f9&color=475569';

$instrumen_items = [
    'A. KEGIATAN PENDAHULUAN' => [
        'p1' => 'Guru menyiapkan peserta didik secara psikis dan fisik',
        'p2' => 'Guru memberi motivasi belajar peserta didik',
        'p3' => 'Mengaitkan pengetahuan sebelumnya dengan materi',
        'p4' => 'Menjelaskan tujuan pembelajaran / kompetensi dasar',
        'p5' => 'Menyampaikan cakupan materi & uraian kegiatan'
    ],
    'B. KEGIATAN INTI' => [
        'i1' => 'Penguasaan materi pembelajaran',
        'i2' => 'Mengaitkan materi dengan pengetahuan relevan',
        'i3' => 'Menyajikan pembahasan materi dengan tepat',
        'i4' => 'Pembelajaran sesuai alokasi waktu',
        'i5' => 'Penerapan strategi pembelajaran aktif',
        'i6' => 'Pemanfaatan media/sumber belajar',
        'i7' => 'Melibatkan siswa dalam pemanfaatan media',
        'i8' => 'Menumbuhkan partisipasi aktif siswa',
        'i9' => 'Merespon positif partisipasi siswa'
    ],
    'C. PENILAIAN PEMBELAJARAN' => [
        'n1' => 'Memantau kemajuan belajar (penilaian proses)',
        'n2' => 'Melakukan penilaian akhir sesuai tujuan'
    ],
    'D. KEGIATAN PENUTUP' => [
        't1' => 'Melakukan refleksi / rangkuman bersama siswa',
        't2' => 'Memberikan tes lisan / tulisan',
        't3' => 'Mengumpulkan hasil kerja (portofolio)',
        't4' => 'Tindak lanjut (arahan & tugas pengayaan)'
    ]
];

?>
<style>
.form-group { margin-bottom: 15px; }
.form-label { display: block; margin-bottom: 6px; font-weight: 700; color: #1e293b; font-size: 0.85rem; }
.form-input { 
    width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; 
    font-size: 0.9rem; font-family: inherit; background: #f8fafc; color: #1e293b;
    outline: none;
}
.form-input:focus { border-color: #d97706; background: #fff; }
.card-item {
    background: #fff; padding: 15px; border-radius: 12px; margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
}
.score-radio {
    display: flex; gap: 8px; margin-top: 8px;
}
.score-radio label {
    flex: 1; text-align: center; background: #f1f5f9; padding: 8px 0; border-radius: 6px;
    font-weight: 700; font-size: 0.8rem; color: #64748b; cursor: pointer; border: 1px solid transparent;
}
.score-radio input[type="radio"] { display: none; }
.score-radio input[type="radio"]:checked + label {
    background: #fef3c7; border-color: #d97706; color: #d97706;
}
</style>

<div class="header" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Form Evaluasi</p>
            <h1 style="font-size: 1.1rem;">Supervisi Kelas</h1>
        </div>
        <div class="avatar" style="color: #d97706; background: white;" onclick="window.location.href='wakakur_supervisi_kelas.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; margin-bottom: 20px;">
        <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        <div>
            <div style="font-weight: 800; color: #1e293b;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size: 0.8rem; color: #64748b;">NIP: <?= empty($guru['nip']) ? '-' : $guru['nip'] ?></div>
        </div>
    </div>

    <form id="formSupervisi">
        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
        
        <div class="card-item">
            <div class="form-group">
                <label class="form-label">Tanggal Supervisi</label>
                <input type="date" name="tanggal_supervisi" class="form-input" value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="form-input" placeholder="Mis. Bahasa Indonesia" value="<?= htmlspecialchars($supervisi['mata_pelajaran'] ?? '') ?>">
            </div>
            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-input" placeholder="Mis. VII A" value="<?= htmlspecialchars($supervisi['kelas'] ?? '') ?>">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Jam Ke-</label>
                    <input type="text" name="jam_ke" class="form-input" placeholder="Mis. 1-2" value="<?= htmlspecialchars($supervisi['jam_ke'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Materi Pokok</label>
                <input type="text" name="materi_pokok" class="form-input" placeholder="Materi yang diajarkan" value="<?= htmlspecialchars($supervisi['materi_pokok'] ?? '') ?>">
            </div>
        </div>

        <h3 style="font-size: 0.95rem; font-weight: 800; margin: 20px 0 10px 5px; color: #334155;">Instrumen Penilaian (1-4)</h3>
        
        <?php foreach ($instrumen_items as $kategori => $items): ?>
            <div style="font-size: 0.8rem; font-weight: 800; color: #b45309; margin: 15px 0 10px 5px; text-transform: uppercase;"><?= $kategori ?></div>
            
            <?php foreach($items as $key => $label): 
                $val = $instrumen[$key] ?? '';
            ?>
            <div class="card-item">
                <div style="font-weight: 700; font-size: 0.8rem; color: #334155; line-height: 1.4;"><?= $label ?></div>
                <div class="score-radio">
                    <?php for($i=4; $i>=1; $i--): ?>
                        <input type="radio" name="instrumen[<?= $key ?>]" id="i_<?= $key ?>_<?= $i ?>" value="<?= $i ?>" <?= $val == $i ? 'checked' : '' ?>>
                        <label for="i_<?= $key ?>_<?= $i ?>"><?= $i ?></label>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <div class="card-item" style="margin-top: 20px;">
            <div class="form-group">
                <label class="form-label">Catatan Supervisi (Kelebihan / Kekurangan)</label>
                <textarea name="catatan" class="form-input" rows="4" style="resize:none;" placeholder="Berikan catatan..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Saran & Tindak Lanjut</label>
                <textarea name="tindak_lanjut" class="form-input" rows="4" style="resize:none;" placeholder="Berikan saran perbaikan..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
            </div>
        </div>

        <div style="padding-bottom: 30px;">
            <button type="submit" id="btnSubmit" style="width:100%; padding:15px; border-radius:12px; background: #d97706; color:white; font-weight:800; font-size:1rem; border:none; margin-top: 10px;">
                <i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('formSupervisi').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin" style="margin-right: 8px;"></i> Menyimpan...';
    
    const formData = new FormData(this);
    
    fetch('api_wakakur_supervisi_kelas_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'wakakur_supervisi_kelas.php';
            });
        } else {
            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi';
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Gagal menghubungi server', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi';
    });
});
</script>

<?php include 'footer_guru.php'; ?>
