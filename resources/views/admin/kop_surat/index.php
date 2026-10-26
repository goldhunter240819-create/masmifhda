<div class="siakad-container">
    <?php include __DIR__ . '/../../siakad/sidebar.php'; ?>
    
    <div class="z-main">
        <?php include __DIR__ . '/../../siakad/_siakad_header.php'; ?>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- Welcome Hero -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 2rem 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 2;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="file-text" style="color: rgba(255,255,255,0.8);"></i> Pengaturan Kop Surat
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Sesuaikan tampilan Kop Surat untuk cetak laporan sekolah.</p>
                </div>
                <!-- Abstract waves -->
                <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 400px; background: radial-gradient(circle at right, rgba(255,255,255,0.1) 0%, transparent 70%);"></div>
            </div>

            <div style="background:white; border-radius:16px; padding:2rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size:1.1rem; font-weight:800; color:#1e293b; margin:0;">Pengaturan Data Kop Surat</h2>
                    <button type="button" style="padding:8px 16px; background:#eff6ff; color:#3b82f6; border:1px solid #bfdbfe; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px; font-size:0.85rem;" onclick="toggleForm()">
                        <i data-lucide="eye-off" style="width:16px; height:16px;"></i> <span id="toggleText">Sembunyikan Form</span>
                    </button>
                </div>

                <div id="alertBox" style="display:none; padding:15px; margin-bottom:20px; border-radius:8px; font-weight:600; font-size:0.9rem;"></div>

                <form id="kopForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="current_logo" value="<?php echo htmlspecialchars($kop['logo'] ?? ''); ?>">

                    <!-- Logo Upload Row -->
                    <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 2rem;">
                        <div style="width: 100px; height: 100px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; background: #f8fafc; cursor:pointer;" onclick="document.getElementById('logoInput').click()">
                            <?php 
                                $logoSrc = ($kop && $kop['logo']) ? '/assets/images/' . $kop['logo'] : '/assets/images/logo.png';
                            ?>
                            <img id="logoPreview" src="<?php echo $logoSrc; ?>" style="max-width: 80px; max-height: 80px; object-fit: contain;" onerror="this.src='/logo.png'">
                            <div style="position: absolute; inset:0; background: rgba(0,0,0,0.4); color:white; display:flex; align-items:center; justify-content:center; opacity:0; transition:0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                <i data-lucide="upload" style="width:24px;height:24px;"></i>
                            </div>
                            <input type="file" id="logoInput" name="logo" style="display:none;" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this)">
                        </div>
                        <div>
                            <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem; margin-bottom: 4px;">Logo Madrasah</div>
                            <div style="font-size: 0.85rem; color: #64748b;">Klik pada gambar untuk mengganti logo.<br>Format yang didukung: PNG/JPG (Maks 2MB)</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 20px; margin-bottom: 1.2rem;">
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">NAMA YAYASAN</label>
                            <input type="text" name="nama_yayasan" value="<?php echo htmlspecialchars($kop['nama_yayasan'] ?? ''); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;" placeholder="Cth: YAYASAN PONDOK PESANTREN...">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">UKURAN FONT YAYASAN</label>
                            <input type="number" name="font_yayasan" value="<?php echo htmlspecialchars($kop['font_yayasan'] ?? '16'); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 20px; margin-bottom: 1.2rem;">
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">NAMA MADRASAH / INSTITUSI</label>
                            <input type="text" name="nama_madrasah" value="<?php echo htmlspecialchars($kop['nama_madrasah'] ?? ''); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;" placeholder="Cth: MADRASAH ALIYAH SUNAN AMPEL">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">UKURAN FONT MADRASAH</label>
                            <input type="number" name="font_madrasah" value="<?php echo htmlspecialchars($kop['font_madrasah'] ?? '24'); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.2rem;">
                        <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">ALAMAT JALAN</label>
                        <textarea name="alamat_jalan" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc; resize:vertical; min-height:80px;"><?php echo htmlspecialchars($kop['alamat_jalan'] ?? ''); ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.2rem;">
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">TELEPON</label>
                            <input type="text" name="telepon" value="<?php echo htmlspecialchars($kop['telepon'] ?? ''); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;" placeholder="Cth: 08123456789">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">EMAIL</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($kop['email'] ?? ''); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;" placeholder="Cth: admin@madrasah.com">
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:6px; letter-spacing:0.5px; text-transform:uppercase;">WEBSITE (Opsional)</label>
                        <input type="text" name="website" value="<?php echo htmlspecialchars($kop['website'] ?? ''); ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; color:#1e293b; box-sizing:border-box; background:#f8fafc;" placeholder="Cth: www.madrasah.sch.id">
                    </div>

                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" id="btnSubmit" style="padding:10px 24px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:800; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.2s;">
                            <i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            <!-- PREVIEW KOP SURAT -->
            <div style="background:white; border-radius:16px; padding:2rem; margin-top:2rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <h2 style="font-size:1.1rem; font-weight:800; color:#1e293b; margin:0 0 1.5rem 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">Preview Hasil Cetak Kop Surat</h2>
                
                <div style="padding: 20px; background: #fff; border: 1px solid #e2e8f0; max-width: 210mm; margin: 0 auto; box-sizing: border-box;">
                    <div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php 
                            $logoSrcPreview = ($kop && $kop['logo']) ? '/assets/images/' . $kop['logo'] : '/assets/images/logo.png';
                        ?>
                        <img id="liveLogo" src="<?php echo $logoSrcPreview; ?>" alt="Logo" style="width: 85px; height: auto; position: absolute; left: 0;" onerror="this.src='/logo.png'; this.onerror=null;">
                        <div style="flex-grow: 1; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3;">
                            <div id="liveYayasan" style="margin: 0; font-size: <?php echo htmlspecialchars($kop['font_yayasan'] ?? '16'); ?>pt !important; font-weight: bold;"><?php echo htmlspecialchars($kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA'); ?></div>
                            <div id="liveMadrasah" style="margin: 0; font-size: <?php echo htmlspecialchars($kop['font_madrasah'] ?? '24'); ?>pt !important; font-weight: bold;"><?php echo htmlspecialchars($kop['nama_madrasah'] ?? 'MADRASAH ALIYAH SUNAN AMPEL'); ?></div>
                            <div id="liveAlamat" style="margin: 0; font-size: 10pt;"><?php echo nl2br(htmlspecialchars($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu')); ?></div>
                            <div style="margin: 0; font-size: 10pt;">
                                Telp: <span id="liveTelp" style="font-size: 10pt !important;"><?php echo htmlspecialchars($kop['telepon'] ?? '081938240819'); ?></span> | 
                                Email: <span id="liveEmail" style="font-size: 10pt !important;"><?php echo htmlspecialchars($kop['email'] ?? 'masmiftahulhuda083@gmail.com'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<script>
    lucide.createIcons();
    
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }

    function toggleForm() {
        const form = document.getElementById('kopForm');
        const text = document.getElementById('toggleText');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            text.innerHTML = 'Sembunyikan Form';
        } else {
            form.style.display = 'none';
            text.innerHTML = 'Tampilkan Form';
        }
    }

    // Live Preview Scripts
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
                document.getElementById('liveLogo').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.querySelector('input[name="nama_yayasan"]').addEventListener('input', function(e) {
        document.getElementById('liveYayasan').innerText = e.target.value;
    });
    document.querySelector('input[name="font_yayasan"]').addEventListener('input', function(e) {
        document.getElementById('liveYayasan').style.setProperty('font-size', (e.target.value || 16) + 'pt', 'important');
    });
    document.querySelector('input[name="nama_madrasah"]').addEventListener('input', function(e) {
        document.getElementById('liveMadrasah').innerText = e.target.value;
    });
    document.querySelector('input[name="font_madrasah"]').addEventListener('input', function(e) {
        document.getElementById('liveMadrasah').style.setProperty('font-size', (e.target.value || 24) + 'pt', 'important');
    });
    document.querySelector('textarea[name="alamat_jalan"]').addEventListener('input', function(e) {
        document.getElementById('liveAlamat').innerHTML = e.target.value.replace(/\n/g, '<br>');
    });
    document.querySelector('input[name="telepon"]').addEventListener('input', function(e) {
        document.getElementById('liveTelp').innerText = e.target.value;
    });
    document.querySelector('input[name="email"]').addEventListener('input', function(e) {
        document.getElementById('liveEmail').innerText = e.target.value;
    });

    document.getElementById('kopForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let btn = document.getElementById('btnSubmit');
        let alertBox = document.getElementById('alertBox');
        
        btn.innerHTML = 'Menyimpan...';
        btn.disabled = true;
        
        fetch('<?php echo base_url("admin/siakad/kop-surat/update"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alertBox.style.display = 'block';
            if(data.success) {
                alertBox.style.background = '#dcfce7';
                alertBox.style.color = '#166534';
                alertBox.style.border = '1px solid #bbf7d0';
                alertBox.innerHTML = data.message;
            } else {
                alertBox.style.background = '#fee2e2';
                alertBox.style.color = '#991b1b';
                alertBox.style.border = '1px solid #fecaca';
                alertBox.innerHTML = data.message;
            }
            btn.innerHTML = '<i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Pengaturan';
            btn.disabled = false;
            lucide.createIcons();
            
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Pengaturan';
            btn.disabled = false;
            lucide.createIcons();
        });
    });
</script>
