<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_guru = $_SESSION['nama'] ?? 'Guru';
$initial = strtoupper(substr($nama_guru, 0, 1));
$page_title = 'Ajukan Izin';

// Ambil Riwayat Izin
$riwayat_izin = [];
try {
    $stmt_r = $siakad_db->prepare("SELECT * FROM ajuan_izin_guru WHERE guru_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt_r->execute([$guru_id]);
    $riwayat_izin = $stmt_r->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$edit_id = $_GET['edit_id'] ?? 0;
$edit_data = null;
$existing_tugas = [];
if ($edit_id > 0) {
    try {
        $stmt_e = $siakad_db->prepare("SELECT * FROM ajuan_izin_guru WHERE id = ? AND guru_id = ? AND status = 'Pending' LIMIT 1");
        $stmt_e->execute([$edit_id, $guru_id]);
        if ($edit_data = $stmt_e->fetch(PDO::FETCH_ASSOC)) {
            $t_arr = json_decode($edit_data['tugas_mapel'], true);
            if (is_array($t_arr)) {
                foreach ($t_arr as $t) {
                    $existing_tugas[$t['jadwal_id']] = $t['tugas'];
                }
            }
        }
    } catch (Exception $e) {}
}

$def_tgl = $edit_data ? $edit_data['tanggal_izin'] : date('Y-m-d');
$def_ket = $edit_data ? $edit_data['keterangan'] : '';
$def_kategori = '';
$def_ket_lain = '';
if (!empty($def_ket)) {
    $parts = explode(' - ', $def_ket, 2);
    $def_kategori = $parts[0];
    if (count($parts) > 1) {
        $def_ket_lain = $parts[1];
    } else {
        if ($def_kategori !== 'Izin' && $def_kategori !== 'Sakit') {
            $def_ket_lain = $def_kategori;
            $def_kategori = 'Izin';
        }
    }
}

include 'header.php';
?>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: 2px solid var(--border-color, #e2e8f0);
        background: var(--card-bg, #ffffff);
        font-size: 0.95rem;
        color: var(--text-main, #0f172a);
        outline: none;
        transition: all 0.2s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .mapel-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    
    .mapel-title {
        font-weight: 700;
        color: var(--primary-dark, #0284c7);
        margin-bottom: 5px;
        font-size: 0.95rem;
        display: flex;
        justify-content: space-between;
    }
    
    .mapel-info {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 14px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 30px;
    }

    .empty-jadwal {
        text-align: center;
        padding: 20px;
        background: #f1f5f9;
        border-radius: 12px;
        color: var(--text-muted);
        font-size: 0.85rem;
        border: 1px dashed #cbd5e1;
    }

    /* Loading */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }
    
    .loading-text {
        text-align: center;
        color: var(--text-muted);
        font-size: 0.85rem;
        padding: 10px;
    }

    @keyframes spin {
    .data-card {
        background: var(--card-bg, #ffffff);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        position: relative;
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding-bottom: 2.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p><?= htmlspecialchars($nama_guru) ?></p>
            <h1>Ajukan Izin</h1>
        </div>
        <a href="absen_guru.php" class="avatar" style="color: #0ea5e9; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="tabs-container" style="display: flex; background: white; border-radius: 12px; padding: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: -2rem; position: relative; z-index: 11; margin-bottom: 20px;">
        <div class="tab-btn active" onclick="switchTab('pengajuan')" id="btn-pengajuan" style="flex: 1; text-align: center; padding: 10px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; background: #0ea5e9; color: white;">Pengajuan</div>
        <div class="tab-btn" onclick="switchTab('riwayat')" id="btn-riwayat" style="flex: 1; text-align: center; padding: 10px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; color: var(--text-muted); background: transparent;">Riwayat Izin</div>
    </div>

    <!-- TAB PENGAJUAN -->
    <div id="tab-pengajuan" class="tab-content" style="display: block;">
        <div class="data-card" style="margin-top: 0; z-index: 10;">
        <form id="form-izin">
            <input type="hidden" name="id" value="<?= $edit_data ? $edit_data['id'] : '' ?>">
            
            <div class="form-group">
                <label class="form-label">Pilih Tanggal Izin</label>
                <input type="date" class="form-control" name="tanggal_izin" id="tanggal_izin" required value="<?= $def_tgl ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select class="form-control" name="kategori" id="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Izin" <?= $def_kategori == 'Izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= $def_kategori == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                </select>
            </div>
            
            <div class="form-group" id="keterangan_lain_group">
                <label class="form-label">Keterangan / Alasan (Opsional)</label>
                <input type="text" class="form-control" name="keterangan_lain" id="keterangan_lain" placeholder="Contoh: Dinas Luar, Acara Keluarga, dll..." value="<?= htmlspecialchars($def_ket_lain) ?>">
            </div>

            <div style="margin: 25px 0 15px 0; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-bottom: 5px;">Tugas & Materi</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Silakan berikan tugas atau materi untuk kelas yang akan Anda tinggalkan hari ini. Kosongkan jika tidak ada.</p>
                
                <div id="jadwal-container">
                    <div class="empty-jadwal">Pilih tanggal terlebih dahulu untuk memuat jadwal.</div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
            </button>
        </form>
        </div>
    </div>

    <!-- TAB RIWAYAT -->
    <div id="tab-riwayat" class="tab-content" style="display: none;">
        <?php if (count($riwayat_izin) > 0): ?>
            <?php foreach ($riwayat_izin as $r): 
                $badge_bg = '#fef3c7'; $badge_color = '#d97706';
                if ($r['status'] == 'Disetujui') { $badge_bg = '#d1fae5'; $badge_color = '#059669'; }
                if ($r['status'] == 'Ditolak') { $badge_bg = '#fee2e2'; $badge_color = '#ef4444'; }
            ?>
            <div class="data-card" style="margin-bottom: 15px; padding: 15px; display: block; border-left: 4px solid <?= $badge_color ?>;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div>
                        <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);"><i class="fa-regular fa-calendar" style="margin-right: 5px; color: #0ea5e9;"></i> <?= date('d M Y', strtotime($r['tanggal_izin'])) ?></div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">
                            <strong>Alasan:</strong> <?= htmlspecialchars($r['keterangan']) ?>
                        </div>
                    </div>
                    <div style="background: <?= $badge_bg ?>; color: <?= $badge_color ?>; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                        <?= $r['status'] ?>
                    </div>
                </div>
                <?php if ($r['status'] == 'Pending'): ?>
                <div style="margin-top: 10px; display: flex; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                    <a href="izin_guru.php?edit_id=<?= $r['id'] ?>" style="background: #e0f2fe; color: #0ea5e9; padding: 6px 15px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 700; flex: 1; text-align: center;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    <button type="button" onclick="batalkanIzin(<?= $r['id'] ?>)" style="background: #fee2e2; color: #ef4444; padding: 6px 15px; border-radius: 8px; border: none; font-size: 0.8rem; font-weight: 700; cursor: pointer; flex: 1; text-align: center;"><i class="fa-solid fa-trash-can"></i> Batalkan</button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="data-card" style="text-align: center; padding: 30px 15px;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                <div style="font-weight: 600; color: var(--text-muted);">Belum ada riwayat pengajuan izin.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alert Modal -->
    <div id="alert-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding:20px;">
        <div style="background:white; border-radius:16px; padding:25px; width:100%; max-width:320px; text-align:center;">
            <div id="alert-icon" style="font-size:3rem; margin-bottom:15px; color:var(--primary);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 id="alert-title" style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">Berhasil!</h3>
            <p id="alert-message" style="font-size:0.9rem; color:var(--text-muted); margin-bottom:20px;">Pengajuan izin berhasil dikirim.</p>
            <button onclick="closeAlert()" style="background:var(--primary); color:white; border:none; padding:12px 20px; border-radius:10px; font-weight:700; width:100%;">Tutup</button>
        </div>
    </div>

    </div> <!-- end .main-content -->
    <script>
        const existingTugas = <?= json_encode($existing_tugas) ?>;
        const isEditMode = <?= $edit_data ? 'true' : 'false' ?>;
        const existingId = <?= $edit_id ?>;
        const tanggalInput = document.getElementById('tanggal_izin');
        const keteranganLainInput = document.getElementById('keterangan_lain');
        const jadwalContainer = document.getElementById('jadwal-container');
        const formIzin = document.getElementById('form-izin');
        const btnSubmit = document.getElementById('btn-submit');
        
        function validateTugas() {
            const textareas = jadwalContainer.querySelectorAll('textarea[name^="tugas"]');
            let allFilled = true;
            textareas.forEach(ta => {
                if (ta.value.trim() === '') {
                    allFilled = false;
                }
            });
            
            if (textareas.length > 0) {
                btnSubmit.disabled = !allFilled;
                if (!allFilled) {
                    btnSubmit.style.opacity = '0.5';
                    btnSubmit.style.cursor = 'not-allowed';
                } else {
                    btnSubmit.style.opacity = '1';
                    btnSubmit.style.cursor = 'pointer';
                }
            } else {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            }
        }
        
        jadwalContainer.addEventListener('input', validateTugas);

        function fetchJadwal() {
            const tanggal = tanggalInput.value;
            if (!tanggal) return;

            btnSubmit.style.display = 'flex';
            document.getElementById('kategori').disabled = false;
            document.getElementById('keterangan_lain').disabled = false;
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.5';

            jadwalContainer.innerHTML = '<div class="loading-text"><div class="loading-spinner" style="border-color: rgba(14,165,233,0.3); border-top-color: var(--primary);"></div><br>Memuat jadwal kelas...</div>';

            fetch(`izin_guru_ajax.php?tanggal=${tanggal}&edit_id=${isEditMode ? existingId : 0}`)
                .then(response => response.json())
                .then(data => {
                    if (data.already_applied) {
                        jadwalContainer.innerHTML = `
                            <div class="empty-jadwal" style="color:var(--danger); border-color:var(--danger); background: #fef2f2; padding: 25px;">
                                <i class="fa-solid fa-lock" style="font-size:2.5rem; margin-bottom:15px; color:var(--danger);"></i><br>
                                <strong style="font-size: 1rem;">Anda sudah mengajukan izin di tanggal ini.</strong><br><br>
                                Silakan cek tab <strong>Riwayat Izin</strong> untuk mengedit atau membatalkan pengajuan.
                            </div>`;
                        btnSubmit.style.display = 'none';
                        document.getElementById('kategori').disabled = true;
                        document.getElementById('keterangan_lain').disabled = true;
                        return;
                    }

                    jadwalContainer.innerHTML = '';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(j => {
                            const mapelId = j.id;
                            const html = `
                                <div class="mapel-card">
                                    <div class="mapel-title">
                                        <span>${j.kelas}</span>
                                        <span style="color:var(--text-muted); font-size:0.8rem;"><i class="fa-regular fa-clock"></i> ${j.jam_mulai} - ${j.jam_selesai}</span>
                                    </div>
                                    <div class="mapel-info">${j.mapel}</div>
                                    <input type="hidden" name="jadwal_id[]" value="${mapelId}">
                                    <input type="hidden" name="kelas_nama[${mapelId}]" value="${j.kelas}">
                                    <input type="hidden" name="mapel_nama[${mapelId}]" value="${j.mapel}">
                                    <textarea class="form-control" name="tugas[${mapelId}]" placeholder="Tulis tugas atau materi untuk kelas ini..." required>${existingTugas[mapelId] || ''}</textarea>
                                </div>
                            `;
                            jadwalContainer.innerHTML += html;
                        });
                        validateTugas();
                    } else {
                        jadwalContainer.innerHTML = '<div class="empty-jadwal">Tidak ada jadwal kelas pada tanggal ini.</div>';
                        validateTugas();
                    }
                })
                .catch(error => {
                    jadwalContainer.innerHTML = '<div class="empty-jadwal" style="color:var(--danger); border-color:var(--danger);">Gagal memuat jadwal. Periksa koneksi internet.</div>';
                    btnSubmit.disabled = false;
                    btnSubmit.style.opacity = '1';
                });
        }

        tanggalInput.addEventListener('change', fetchJadwal);
        
        // Fetch initial
        if(tanggalInput.value) fetchJadwal();

        formIzin.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Adjust keterangan
            let kategori = formData.get('kategori');
            let keteranganLain = formData.get('keterangan_lain');
            let finalKeterangan = kategori;
            if (keteranganLain && keteranganLain.trim() !== '') {
                finalKeterangan = kategori + ' - ' + keteranganLain.trim();
            }
            formData.set('keterangan', finalKeterangan);
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<div class="loading-spinner"></div> Mengirim...';
            
            fetch('izin_guru_submit.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan';
                
                if (data.success) {
                    showAlert('Berhasil!', data.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'absen_guru.php';
                    }, 2000);
                } else {
                    showAlert('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan';
                showAlert('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
            });
        });

        function showAlert(title, message, type) {
            document.getElementById('alert-title').innerText = title;
            document.getElementById('alert-message').innerText = message;
            const icon = document.getElementById('alert-icon');
            if (type === 'success') {
                icon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                icon.style.color = '#10b981';
            } else {
                icon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
                icon.style.color = '#ef4444';
            }
            document.getElementById('alert-modal').style.display = 'flex';
        }

        function closeAlert() {
            document.getElementById('alert-modal').style.display = 'none';
        }
        
        function batalkanIzin(id) {
            if(confirm('Yakin ingin membatalkan pengajuan izin ini?')) {
                const fd = new FormData();
                fd.append('id', id);
                fetch('izin_guru_delete.php', {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Berhasil', data.message, 'success');
                        setTimeout(() => window.location.href='izin_guru.php', 1500);
                    } else {
                        showAlert('Gagal', data.message, 'error');
                    }
                })
                .catch(err => showAlert('Gagal', 'Terjadi kesalahan sistem', 'error'));
            }
        }
        
        function switchTab(tabId) {
            document.getElementById('tab-pengajuan').style.display = 'none';
            document.getElementById('tab-riwayat').style.display = 'none';
            
            document.getElementById('btn-pengajuan').style.background = 'transparent';
            document.getElementById('btn-pengajuan').style.color = 'var(--text-muted)';
            
            document.getElementById('btn-riwayat').style.background = 'transparent';
            document.getElementById('btn-riwayat').style.color = 'var(--text-muted)';
            
            document.getElementById('tab-' + tabId).style.display = 'block';
            document.getElementById('btn-' + tabId).style.background = '#0ea5e9';
            document.getElementById('btn-' + tabId).style.color = 'white';
        }
    </script>
</div> <!-- End main-content -->
<?php include 'footer_guru.php'; ?>
