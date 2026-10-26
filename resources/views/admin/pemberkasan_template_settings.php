<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__.'/_zamrud_head.php'; ?>
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .ts-wrap { display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap; }
        .ts-sidebar { width: 250px; flex-shrink: 0; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; position: sticky; top: 20px; }
        .ts-content { flex: 1; min-width: 400px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; }
        
        .ts-tab { display: block; padding: 12px 16px; text-decoration: none; color: #475569; font-weight: 600; font-size: 0.95rem; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
        .ts-tab:hover { background: #f8fafc; color: #0f172a; }
        .ts-tab.active { background: #eff6ff; color: #2563eb; border-left: 4px solid #2563eb; }

        .ts-alert { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display:flex; align-items:center; gap:8px; }

        .var-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin-bottom: 20px; font-size: 0.85rem; color: #475569; line-height: 1.6; }
        .var-code { background: #e2e8f0; color: #0f172a; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-weight: 700; user-select: all; cursor: pointer; }
        .var-code:hover { background: #cbd5e1; }
    </style>
</head>
<body>
<div class="z-wrap">
    <?php include __DIR__.'/_zamrud_sidebar.php'; ?>
    <main class="z-main">
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                <span class="z-header-title">Template Surat Pemberkasan</span>
                <span class="z-badge">Admin</span>
            </div>
            <div class="z-header-user">
                <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
                <div class="z-avatar"><?php echo $adminInitial; ?></div>
            </div>
        </header>

        <div class="z-scroll">
            <div class="z-page-head-row">
                <div>
                    <h1>Template Surat / SK Otomatis</h1>
                    <p>Desain isi surat bebas menggunakan Editor, gunakan Variabel Ajaib agar isi otomatis berubah sesuai data guru.</p>
                </div>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="ts-alert"><i data-lucide="check-circle-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <div class="ts-wrap">
                <div class="ts-sidebar">
                    <?php
                    $types = [
                        'surat_rekomendasi' => 'Surat Rekomendasi',
                        'sk_pembagian_tugas' => 'SK Pembagian Tugas'
                    ];
                    foreach($types as $key => $label) {
                        $active = ($activeJenis === $key) ? 'active' : '';
                        echo '<button type="button" onclick="window.location.href=\base_url('admin/persuratan/pemberkasan_template?jenis=').$key.'\'" class="ts-tab tab-btn '.$active.'" style="width:100%;text-align:left;cursor:pointer;border:none;background:transparent;border-bottom:1px solid #f1f5f9;">'.$label.'</button>';
                    }
                    ?>
                </div>

                <div class="ts-content">
                    <h2 style="font-size:1.25rem; font-weight:800; margin-bottom:15px; color:#0f172a; border-bottom:2px solid #f1f5f9; padding-bottom:10px;">
                        Edit Template: <?php echo htmlspecialchars($types[$activeJenis] ?? $activeJenis); ?>
                    </h2>

                    <div class="var-box">
                        <strong><i data-lucide="wand-2" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;color:#f59e0b;"></i> Variabel Ajaib (Mail Merge)</strong><br>
                        Klik variabel di bawah ini untuk menyalin (*copy*), lalu *paste* ke dalam Editor. Saat dicetak, kode akan otomatis berubah menjadi data asli guru tersebut.<br><br>
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:10px;">
                            <div><span class="var-code" onclick="copyVar(this)">[NAMA_GURU]</span> : Nama Lengkap Guru</div>
                            <div><span class="var-code" onclick="copyVar(this)">[TTL_GURU]</span> : Tempat, Tgl Lahir</div>
                            <div><span class="var-code" onclick="copyVar(this)">[JABATAN]</span> : Jabatan (cth: Guru Kelas)</div>
                            <div><span class="var-code" onclick="copyVar(this)">[MAPEL]</span> : Mata Pelajaran Guru</div>
                            <div><span class="var-code" onclick="copyVar(this)">[NAMA_KAMAD]</span> : Nama Kepala Madrasah</div>
                            <div><span class="var-code" onclick="copyVar(this)">[NAMA_SEKOLAH]</span> : Nama Madrasah</div>
                            <div><span class="var-code" onclick="copyVar(this)">[ALAMAT_SEKOLAH]</span> : Alamat Madrasah</div>
                            <div><span class="var-code" onclick="copyVar(this)">[TANGGAL_SURAT]</span> : Tanggal Hari Ini</div>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo base_url('admin/persuratan/pemberkasan_template/save'); ?>">
                        <input type="hidden" name="jenis" value="<?php echo htmlspecialchars($activeJenis); ?>">
                        
                        <div style="margin-bottom:20px;">
                            <textarea id="myEditor" name="konten_html" style="width:100%; height:500px;"><?php 
                                echo htmlspecialchars($templates[$activeJenis]['konten_html'] ?? '<p>Ketik surat di sini...</p>'); 
                            ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="font-weight:700; padding:12px 24px;">
                            <i data-lucide="save" style="width:18px;height:18px;vertical-align:-3px;margin-right:6px;"></i> Simpan Template
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
lucide.createIcons();

function copyVar(el) {
    navigator.clipboard.writeText(el.innerText);
    var oldText = el.innerText;
    el.innerText = 'Tersalin!';
    el.style.background = '#bbf7d0';
    setTimeout(() => {
        el.innerText = oldText;
        el.style.background = '#e2e8f0';
    }, 1000);
}

tinymce.init({
    selector: '#myEditor',
    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table directionality emoticons template',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 600,
    menubar: 'file edit view insert format tools table',
    content_style: "body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; text-align: justify; padding: 20px; }"
});
</script>
</body>
</html>
