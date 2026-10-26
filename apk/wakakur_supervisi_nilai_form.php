<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$mapel_id = isset($_GET['mapel_id']) ? (int)$_GET['mapel_id'] : 0;
$jenis_evaluasi = isset($_GET['jenis']) ? $_GET['jenis'] : '';

if (!$guru_id || !$kelas_id || !$mapel_id || empty($jenis_evaluasi)) {
    header("Location: wakakur_supervisi_nilai_detail.php?guru_id=$guru_id");
    exit;
}

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
$inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
$tahun_ajaran_id = $inst['tahun_ajaran_id'] ?? 1;
$semester = $inst['semester'] ?? 'Ganjil';

// Get Mapel & Kelas Name
$stmtMK = $core_db->prepare("SELECT m.nama_mapel, k.nama_kelas FROM mapel m, kelas k WHERE m.id = ? AND k.id = ?");
$stmtMK->execute([$mapel_id, $kelas_id]);
$mk = $stmtMK->fetch(PDO::FETCH_ASSOC);
$nama_mapel = $mk['nama_mapel'] ?? '';
$nama_kelas = $mk['nama_kelas'] ?? '';

// Get existing supervision status
$stmtSup = $core_db->prepare("SELECT * FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ? AND guru_id = ? AND kelas_id = ? AND mapel_id = ? AND jenis_evaluasi = ?");
$stmtSup->execute([$tahun_ajaran_id, $semester, $guru_id, $kelas_id, $mapel_id, $jenis_evaluasi]);
$supervisi = $stmtSup->fetch(PDO::FETCH_ASSOC);

$page_title = 'Form Supervisi Nilai';
include 'header.php';
?>
<style>
.form-group { margin-bottom: 15px; }
.form-label { display: block; margin-bottom: 6px; font-weight: 700; color: #1e293b; font-size: 0.85rem; }
.form-input { 
    width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; 
    font-size: 0.9rem; font-family: inherit; background: #f8fafc; color: #1e293b;
    outline: none;
}
.form-input:focus { border-color: #c026d3; background: #fff; }
.card-item {
    background: #fff; padding: 15px; border-radius: 12px; margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
}
.checkbox-wrap {
    display: flex; align-items: center; gap: 10px; margin-bottom: 12px; cursor: pointer; 
    padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; transition: 0.2s; background: #f8fafc;
}
.checkbox-wrap input[type="checkbox"] {
    width: 18px; height: 18px; accent-color: #c026d3;
}
.checkbox-wrap span {
    font-size: 0.9rem; color: #334155; font-weight: 600;
}
</style>

<div class="header" style="background: linear-gradient(135deg, #c026d3 0%, #86198f 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Form Evaluasi</p>
            <h1 style="font-size: 1.1rem;">Supervisi Nilai</h1>
        </div>
        <div class="avatar" style="color: #c026d3; background: white;" onclick="window.location.href='wakakur_supervisi_nilai_detail.php?guru_id=<?= $guru_id ?>'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 15px; background: white; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: #c026d3; text-transform: uppercase; margin-bottom: 5px;">EVALUASI YANG DISUPERVISI</div>
        <div style="font-weight: 800; font-size: 1.1rem; color: #1e293b;"><?= htmlspecialchars($jenis_evaluasi) ?></div>
        <div style="font-size: 0.85rem; color: #64748b; margin-top: 5px;">
            <?= htmlspecialchars($nama_mapel) ?> &bull; Kelas <?= htmlspecialchars($nama_kelas) ?>
        </div>
    </div>

    <form id="formSupervisi">
        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
        <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
        <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
        <input type="hidden" name="jenis_evaluasi" value="<?= htmlspecialchars($jenis_evaluasi) ?>">
        
        <h3 style="font-size: 0.95rem; font-weight: 800; margin: 20px 0 10px 5px; color: #334155;">Kelengkapan Administrasi</h3>
        
        <div class="card-item">
            <label class="checkbox-wrap">
                <input type="checkbox" name="ada_kisi" value="1" <?= ($supervisi['ada_kisi'] ?? 0) == 1 ? 'checked' : '' ?>>
                <span>1. Kisi-kisi Soal Tersedia</span>
            </label>
            <label class="checkbox-wrap">
                <input type="checkbox" name="ada_analisis" value="1" <?= ($supervisi['ada_analisis'] ?? 0) == 1 ? 'checked' : '' ?>>
                <span>2. Analisis Butir Soal Dikerjakan</span>
            </label>
            <label class="checkbox-wrap" style="margin-bottom: 0;">
                <input type="checkbox" name="ada_remedial" value="1" <?= ($supervisi['ada_remedial'] ?? 0) == 1 ? 'checked' : '' ?>>
                <span>3. Program Remedial / Pengayaan Tersedia</span>
            </label>
        </div>

        <div class="card-item" style="margin-top: 20px;">
            <div class="form-group">
                <label class="form-label">Catatan Supervisi (Opsional)</label>
                <textarea name="catatan" class="form-input" rows="4" style="resize:none;" placeholder="Berikan catatan perbaikan atau masukan untuk guru..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Rencana Tindak Lanjut</label>
                <select name="tindak_lanjut" class="form-input">
                    <option value="">-- Pilih Tindak Lanjut --</option>
                    <?php 
                    $tls = ['Apresiasi / Dipertahankan', 'Pembinaan Internal (Kepsek/Waka)', 'Teguran Lisan / Tertulis', 'Diikutsertakan KKG / Diklat'];
                    $ct = $supervisi['tindak_lanjut'] ?? '';
                    foreach($tls as $t): ?>
                        <option value="<?= $t ?>" <?= $ct === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="padding-bottom: 30px;">
            <button type="submit" id="btnSubmit" style="width:100%; padding:15px; border-radius:12px; background: #c026d3; color:white; font-weight:800; font-size:1rem; border:none; margin-top: 10px;">
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
    
    fetch('api_wakakur_supervisi_nilai_save.php', {
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
                window.location.href = 'wakakur_supervisi_nilai_detail.php?guru_id=<?= $guru_id ?>';
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
