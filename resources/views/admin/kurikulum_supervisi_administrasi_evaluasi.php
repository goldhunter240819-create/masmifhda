<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        .z-btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(14,165,233,0.3);
        }
        .z-btn-primary:hover { 
            filter: brightness(1.1); 
            box-shadow: 0 6px 8px -2px rgba(14,165,233,0.4);
            color: white;
            transform: translateY(-1px);
        }
        
        .z-btn-outline {
            background: rgba(255,255,255,0.15); 
            border: 1px solid rgba(255,255,255,0.3); 
            color: #fff;
            padding: 8px 16px; 
            border-radius: 20px; 
            backdrop-filter: blur(5px);
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            text-decoration: none;
            transition: all 0.2s;
        }
        .z-btn-outline:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }

        .z-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--z-border);
            background: var(--z-bg);
            color: var(--z-text);
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        .z-input:focus {
            border-color: #0ea5e9;
        }
        
        .radio-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            color: #475569;
        }
        .radio-label input {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Evaluasi Supervisi Administrasi</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Waka Kurikulum</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">
                
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.8);"></i> Instrumen Supervisi Administrasi
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Penilaian Kelengkapan Perangkat Pembelajaran Guru</p>
                    </div>
                    <a href="<?= base_url('admin/kurikulum/supervisi-administrasi') ?>" class="e-btn" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
                    </a>
                </div>

                <div class="z-card" style="padding: 2rem;">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="user" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 4px;">NAMA GURU</div>
                            <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;"><?= htmlspecialchars($guru['nama']) ?></div>
                            <?php if (!empty($guru['nip'])): ?>
                                <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">NIP: <?= htmlspecialchars($guru['nip']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form id="formSupervisi" onsubmit="event.preventDefault(); saveSupervisi();">
                        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">
                        
                        <div style="margin-bottom: 25px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Tanggal Supervisi</label>
                            <input type="date" name="tanggal_supervisi" class="z-input" style="max-width: 200px;" required value="<?= $supervisi ? $supervisi['tanggal_supervisi'] : date('Y-m-d') ?>">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Instrumen Administrasi Pembelajaran</label>
                            <table class="z-table" style="border: 1px solid var(--z-border);">
                                <thead>
                                    <tr style="background: #f8fafc;">
                                        <th style="padding: 12px 15px;">Nama Dokumen</th>
                                        <th style="width: 300px; text-align: left;">Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $instrumen_items = [
                                        'cp' => ['label' => 'Capaian Pembelajaran (CP)'],
                                        'atp' => ['label' => 'Alur Tujuan Pembelajaran (ATP)'],
                                        'modul' => ['label' => 'Modul Ajar / RPP'],
                                        'kktp' => ['label' => 'KKTP / KKM'],
                                        'prota' => ['label' => 'Program Tahunan (Prota)'],
                                        'promes' => ['label' => 'Program Semester (Promes)'],
                                        'absen' => ['label' => 'Buku Daftar Hadir Siswa'],
                                        'jurnal' => ['label' => 'Buku Jurnal Mengajar'],
                                        'daftar_nilai' => ['label' => 'Buku Daftar Nilai']
                                    ];
                                    foreach ($instrumen_items as $key => $item):
                                        $val = $supervisi['instrumen'][$key] ?? '';
                                        if (empty($val)) {
                                            // Fallback untuk data yang menggunakan format lama dari web
                                            if ($key == 'cp' || $key == 'atp') $val = $supervisi['instrumen']['silabus'] ?? '';
                                            elseif ($key == 'modul') $val = $supervisi['instrumen']['rpp'] ?? '';
                                            elseif ($key == 'kktp') $val = $supervisi['instrumen']['kkm'] ?? '';
                                            elseif ($key == 'daftar_nilai') $val = $supervisi['instrumen']['nilai'] ?? '';
                                        }
                                        
                                        // Reverse mapping untuk data dari web lama ("Ada", "Tidak Lengkap", "Tidak Ada")
                                        if ($val === 'Ada') $val = '4';
                                        elseif ($val === 'Tidak Lengkap') $val = '2';
                                        elseif ($val === 'Tidak Ada' || $val === '0') $val = '1';
                                        
                                        // Jika kosong, jadikan null agar tidak checked
                                        if ($val === '') $val = null;
                                    ?>
                                    <tr>
                                        <td style="padding: 12px 15px; font-weight: 600; color: #334155;">
                                            <?= $item['label'] ?>
                                        </td>
                                        <td style="padding: 12px 15px;">
                                            <div class="radio-group">
                                                <label class="radio-label">
                                                    <input type="radio" name="instrumen[<?= $key ?>]" value="4" <?= $val == '4' ? 'checked' : '' ?> required> Sangat Lengkap
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="instrumen[<?= $key ?>]" value="3" <?= $val == '3' ? 'checked' : '' ?> required> Cukup Lengkap
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="instrumen[<?= $key ?>]" value="2" <?= $val == '2' ? 'checked' : '' ?> required> Kurang Lengkap
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="instrumen[<?= $key ?>]" value="1" <?= $val == '1' ? 'checked' : '' ?> required> Tidak Ada
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Catatan Tambahan</label>
                            <textarea name="catatan" class="z-input" rows="3" placeholder="Masukkan catatan supervisi..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
                        </div>
                        
                        <div style="margin-bottom: 30px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" class="z-input" rows="3" placeholder="Masukkan saran tindak lanjut..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <button type="submit" class="z-btn-primary" id="btnSubmit">
                                <i data-lucide="save"></i> Simpan Hasil Supervisi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        lucide.createIcons();
        function zToggleSidebar() {
            document.querySelector('.z-sidebar').classList.toggle('open');
        }

        function saveSupervisi() {
            const form = document.getElementById('formSupervisi');
            const formData = new FormData(form);
            const btnSubmit = document.getElementById('btnSubmit');
            
            btnSubmit.innerHTML = '<i data-lucide="loader-2" class="spin"></i> Menyimpan...';
            lucide.createIcons();
            btnSubmit.disabled = true;

            fetch('<?= base_url("admin/kurikulum/supervisi-administrasi/save") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '<?= base_url("admin/kurikulum/supervisi-administrasi") ?>';
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    btnSubmit.innerHTML = '<i data-lucide="save"></i> Simpan Hasil Supervisi';
                    lucide.createIcons();
                    btnSubmit.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
                btnSubmit.innerHTML = '<i data-lucide="save"></i> Simpan Hasil Supervisi';
                lucide.createIcons();
                btnSubmit.disabled = false;
            });
        }
    </script>
    <style>
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</body>
</html>
