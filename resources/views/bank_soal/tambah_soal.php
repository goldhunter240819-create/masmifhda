<?php
if (!isset($page_title)) $page_title = "Kelola Soal";
if (!isset($active_page)) $active_page = "kelola_soal";

$topikId = $topikId ?? ($topikInfo['id'] ?? 0);
$paketId = $paketId ?? ($paketInfo['id'] ?? 0);
$mapel_nama = $mapel_nama ?? ($mapelNama ?? ($paketInfo['mapel_nama'] ?? ($soalEdit['mapel_nama'] ?? '')));
$isEdit = !empty($soalEdit['id']);

$backUrl = !empty($paketId) 
    ? base_url('bank_soal/daftar_soal?paket_id=' . $paketId) 
    : (!empty($topikId) 
        ? base_url('bank_soal/daftar_soal?topik_id=' . $topikId . '&mapel=' . urlencode($mapel_nama))
        : base_url('bank_soal'));

$currentTipe = $soalEdit['tipe_soal'] ?? 'sec_pg';
$currentBobot = $soalEdit['skor_maksimal'] ?? 2;
$currentKesulitan = $soalEdit['tingkat_kesulitan'] ?? 'Sedang';
$currentPertanyaan = $soalEdit['pertanyaan'] ?? '';
$currentKunci = $soalEdit['kunci_jawaban'] ?? 'A';
$kontenDecoded = !empty($soalEdit['konten_tambahan']) ? json_decode($soalEdit['konten_tambahan'], true) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - MASMIFHDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <link href="<?php echo base_url('public/assets/css/zamrud.css'); ?>" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Trumbowyg Editor -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
    <style>
        .form-section { display: none; padding-top: 15px; border-top: 1px dashed #cbd5e1; margin-top: 15px; }
        .form-section.active { display: block; }
        
        .pg-option, .match-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .pg-option input[type="radio"], .pg-option input[type="checkbox"] { transform: scale(1.3); margin: 0 10px; }
        
        .type-selector { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; margin-bottom: 20px; }
        .type-btn { 
            border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 12px; 
            text-align: center; cursor: pointer; transition: 0.2s; background: #fff; color: #475569; font-weight: 700; font-size: 0.85rem;
        }
        .type-btn:hover { border-color: #0ea5e9; background: #f0f9ff; }
        .type-btn.selected { border-color: #0ea5e9; background: #0ea5e9; color: #fff; box-shadow: 0 4px 12px rgba(14,165,233,0.25); }
        
        .welcome-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.5;
        }
    </style>
</head>
<body class="z-bg">

<div class="z-wrap">
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="z-main">
        <?php 
        $page_badge = 'Bank Soal';
        include __DIR__ . '/../siakad/_siakad_header.php'; 
        ?>

        <div class="z-content">
            <div class="elite-page-header welcome-hero" style="background: linear-gradient(135deg, #0284c7, #0369a1); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px -5px rgba(2,132,199,0.3);">
                <div style="position: relative; z-index: 1;">
                    <a href="<?= $backUrl ?>" style="color: rgba(255,255,255,0.85); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.9rem; margin-bottom: 10px; font-weight: 600;">
                        <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali ke Daftar Soal
                    </a>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="<?= $isEdit ? 'file-edit' : 'plus-circle' ?>" style="color: rgba(255,255,255,0.9);"></i> <?= $isEdit ? 'Edit Butir Soal' : 'Tambah Soal Baru' ?>
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                        Topik: <strong><?= htmlspecialchars(($topikInfo['jenis'] ?? '') . ' - ' . ($topikInfo['nama_topik'] ?? '')) ?></strong> &bull; 
                        Mapel: <strong><?= htmlspecialchars($mapel_nama) ?></strong> 
                        <?php if (!empty($paketInfo['kelas_nama'])): ?> &bull; Kelas: <strong><?= htmlspecialchars($paketInfo['kelas_nama']) ?></strong><?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="z-card" style="padding: 2.2rem; border-radius: 16px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <form id="formSoal" action="<?= base_url('bank_soal/tambah_soal') ?>" method="POST">
                    <input type="hidden" name="topik_id" value="<?= htmlspecialchars($topikId) ?>">
                    <?php if ($paketId > 0): ?>
                    <input type="hidden" name="paket_id" value="<?= $paketId ?>">
                    <?php endif; ?>
                    <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)$soalEdit['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="mapel_nama" value="<?= htmlspecialchars($mapel_nama) ?>">
                    <input type="hidden" name="kelas_nama" value="<?= htmlspecialchars($paketInfo['kelas_nama'] ?? ($soalEdit['kelas_nama'] ?? '')) ?>">
                    
                    <!-- Metadata Soal -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <div class="z-form-group" style="margin:0;">
                            <label style="font-weight: 700; color: #475569; font-size: 0.85rem; margin-bottom: 6px; display: block;">Nomor Urut</label>
                            <input type="number" name="nomor_urut" class="z-input" value="<?= $soalEdit['nomor_urut'] ?? 1 ?>" min="1">
                        </div>
                        <div class="z-form-group" style="margin:0;">
                            <label style="font-weight: 700; color: #475569; font-size: 0.85rem; margin-bottom: 6px; display: block;">Tingkat Kesulitan</label>
                            <select name="tingkat_kesulitan" class="z-input">
                                <option value="Mudah" <?= ($currentKesulitan === 'Mudah') ? 'selected' : '' ?>>Mudah</option>
                                <option value="Sedang" <?= ($currentKesulitan === 'Sedang') ? 'selected' : '' ?>>Sedang</option>
                                <option value="Sulit" <?= ($currentKesulitan === 'Sulit') ? 'selected' : '' ?>>Sulit</option>
                            </select>
                        </div>
                        <div class="z-form-group" style="margin:0;">
                            <label style="font-weight: 700; color: #475569; font-size: 0.85rem; margin-bottom: 6px; display: block;">Skor Maksimal / Bobot <span style="color:red;">*</span></label>
                            <input type="number" step="0.5" name="skor_maksimal" class="z-input" value="<?= htmlspecialchars($currentBobot) ?>" required>
                        </div>
                    </div>

                    <!-- Pilihan Bentuk Soal -->
                    <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block; font-size: 0.95rem;">Bentuk Soal <span style="color:red;">*</span></label>
                    <div class="type-selector">
                        <div class="type-btn <?= ($currentTipe === 'sec_pg') ? 'selected' : '' ?>" data-target="sec_pg">1. Pilihan Ganda (PG)</div>
                        <div class="type-btn <?= ($currentTipe === 'sec_pgk') ? 'selected' : '' ?>" data-target="sec_pgk">2. PG Kompleks</div>
                        <div class="type-btn <?= ($currentTipe === 'sec_bs') ? 'selected' : '' ?>" data-target="sec_bs">3. Benar / Salah</div>
                        <div class="type-btn <?= ($currentTipe === 'sec_jodoh') ? 'selected' : '' ?>" data-target="sec_jodoh">4. Menjodohkan</div>
                        <div class="type-btn <?= ($currentTipe === 'sec_isian') ? 'selected' : '' ?>" data-target="sec_isian">5. Isian Singkat</div>
                        <div class="type-btn <?= ($currentTipe === 'sec_essay') ? 'selected' : '' ?>" data-target="sec_essay">6. Essay (Uraian)</div>
                    </div>
                    
                    <input type="hidden" name="bentuk_soal" id="bentuk_soal" value="<?= htmlspecialchars($currentTipe) ?>">

                    <!-- Konten Soal (WYSIWYG) -->
                    <div class="z-form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 8px; display: block;">Teks Pertanyaan / Soal <span style="color:red;">*</span></label>
                        <textarea class="editor" name="pertanyaan"><?= htmlspecialchars($currentPertanyaan) ?></textarea>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 1: PILIHAN GANDA (A-E) -->
                    <!-- ========================================== -->
                    <div id="sec_pg" class="form-section <?= ($currentTipe === 'sec_pg') ? 'active' : '' ?>">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 8px; display: block;">Opsi Jawaban & Kunci Benar</label>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: -4px; margin-bottom: 15px;">Pilih salah satu radio button di sebelah kiri sebagai kunci jawaban yang benar.</p>
                        
                        <?php 
                        $labels = ['A', 'B', 'C', 'D', 'E'];
                        $opsiValues = [
                            'A' => $soalEdit['opsi_a'] ?? '',
                            'B' => $soalEdit['opsi_b'] ?? '',
                            'C' => $soalEdit['opsi_c'] ?? '',
                            'D' => $soalEdit['opsi_d'] ?? '',
                            'E' => $soalEdit['opsi_e'] ?? '',
                        ];
                        ?>
                        <?php foreach($labels as $lbl): ?>
                        <div class="pg-option">
                            <input type="radio" name="kunci_pg" value="<?php echo $lbl; ?>" <?= (strtoupper(trim($currentKunci)) === $lbl) ? 'checked' : ($lbl === 'A' && empty($currentKunci) ? 'checked' : '') ?>>
                            <span style="font-weight:bold; width: 25px;"><?php echo $lbl; ?>.</span>
                            <input type="text" name="opsi_pg_<?php echo $lbl; ?>" class="z-input" placeholder="Teks jawaban <?php echo $lbl; ?>..." value="<?= htmlspecialchars($opsiValues[$lbl]) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 2: PILIHAN GANDA KOMPLEKS -->
                    <!-- ========================================== -->
                    <div id="sec_pgk" class="form-section">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block;">Opsi Jawaban & Kunci (Lebih dari 1)</label>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 15px;">Centang checkbox untuk jawaban-jawaban yang benar.</p>
                        
                        <div id="pgk-container">
                            <?php for($i=0; $i<4; $i++): ?>
                            <div class="pg-option">
                                <input type="checkbox" name="kunci_pgk[]" value="<?= $i ?>">
                                <span style="font-weight:bold; width: 25px;">•</span>
                                <input type="text" name="pgk_pernyataan[]" class="z-input" placeholder="Opsi jawaban...">
                                <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x" style="width:14px;height:14px;"></i></button>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-ghost" style="margin-top:10px; color:#0ea5e9;" onclick="addPgkRow()">
                            <i data-lucide="plus"></i> Tambah Opsi
                        </button>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 3: BENAR / SALAH -->
                    <!-- ========================================== -->
                    <div id="sec_bs" class="form-section">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block;">Tabel Pernyataan Benar/Salah</label>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 15px;">Tulis pernyataan dan pilih kunci jawabannya untuk setiap baris.</p>
                        
                        <div id="bs-container">
                            <?php for($i=0; $i<3; $i++): ?>
                            <div class="match-row" style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:10px;">
                                <input type="text" name="bs_pernyataan[]" class="z-input" placeholder="Tulis pernyataan di sini..." style="flex-grow:1;">
                                <select name="bs_jawaban[]" class="z-input" style="width: 120px;">
                                    <option value="Benar">Benar</option>
                                    <option value="Salah">Salah</option>
                                </select>
                                <button type="button" class="btn" style="width:38px;height:38px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x"></i></button>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-ghost" style="margin-top:5px; color:#0ea5e9;" onclick="addBsRow()">
                            <i data-lucide="plus"></i> Tambah Pernyataan
                        </button>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 4: MENJODOHKAN -->
                    <!-- ========================================== -->
                    <div id="sec_jodoh" class="form-section">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block;">Pasangan Menjodohkan</label>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 15px;">Tuliskan pasangan yang benar secara langsung. Sistem CBT akan mengacaknya nanti saat ujian.</p>
                        
                        <div id="jodoh-container">
                            <div class="match-row">
                                <input type="text" name="jodoh_premis[]" class="z-input" placeholder="Premis (Kiri)">
                                <i data-lucide="arrow-right" style="color:#cbd5e1;"></i>
                                <input type="text" name="jodoh_respon[]" class="z-input" placeholder="Respon / Jawaban (Kanan)">
                                <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x" style="width:14px;height:14px;"></i></button>
                            </div>
                            <div class="match-row">
                                <input type="text" name="jodoh_premis[]" class="z-input" placeholder="Premis (Kiri)">
                                <i data-lucide="arrow-right" style="color:#cbd5e1;"></i>
                                <input type="text" name="jodoh_respon[]" class="z-input" placeholder="Respon / Jawaban (Kanan)">
                                <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x" style="width:14px;height:14px;"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-ghost" style="margin-top:10px; color:#0ea5e9;" onclick="addMatchRow()">
                            <i data-lucide="plus"></i> Tambah Pasangan
                        </button>
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 5: ISIAN SINGKAT -->
                    <!-- ========================================== -->
                    <div id="sec_isian" class="form-section">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block;">Kunci Jawaban Singkat</label>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 15px;">Ketik jawaban pasti. Huruf besar/kecil biasanya akan diabaikan oleh sistem.</p>
                        <input type="text" name="kunci_isian" class="z-input" placeholder="Masukkan kunci jawaban pasti...">
                    </div>

                    <!-- ========================================== -->
                    <!-- SECTION 6: ESSAY (URAIAN) -->
                    <!-- ========================================== -->
                    <div id="sec_essay" class="form-section">
                        <label style="font-weight: 700; color: #334155; margin-bottom: 10px; display: block;">Pedoman Penskoran / Rubrik Jawaban</label>
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: -5px; margin-bottom: 15px;">Panduan bagi guru saat mengoreksi jawaban essay siswa secara manual.</p>
                        <textarea class="editor" name="rubrik"></textarea>
                    </div>


                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 2rem 0;">
                    
                    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="button" class="btn btn-outline" onclick="history.back()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i> Simpan Soal
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
    
    // Init WYSIWYG Editor
    $('.editor').trumbowyg({
        btns: [
            ['viewHTML'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat']
        ]
    });

    // Form Type Selector Logic
    $('.type-btn').click(function() {
        // Update UI buttons
        $('.type-btn').removeClass('selected');
        $(this).addClass('selected');
        
        // Hide all sections
        $('.form-section').removeClass('active');
        
        // Show target section
        let targetId = $(this).data('target');
        $('#' + targetId).addClass('active');
        
        // Update hidden input
        $('#bentuk_soal').val(targetId);
    });

    // Index tracker for dynamically added PGK inputs so checkboxes match array values
    let pgkCount = 4;
    function addPgkRow() {
        let html = `
        <div class="pg-option">
            <input type="checkbox" name="kunci_pgk[]" value="${pgkCount++}">
            <span style="font-weight:bold; width: 25px;">•</span>
            <input type="text" name="pgk_pernyataan[]" class="z-input" placeholder="Opsi jawaban...">
            <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x" style="width:14px;height:14px;"></i></button>
        </div>`;
        $('#pgk-container').append(html);
        lucide.createIcons();
    }

    function addBsRow() {
        let html = `
        <div class="match-row" style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:10px;">
            <input type="text" name="bs_pernyataan[]" class="z-input" placeholder="Tulis pernyataan di sini..." style="flex-grow:1;">
            <select name="bs_jawaban[]" class="z-input" style="width: 120px;">
                <option value="Benar">Benar</option>
                <option value="Salah">Salah</option>
            </select>
            <button type="button" class="btn" style="width:38px;height:38px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x"></i></button>
        </div>`;
        $('#bs-container').append(html);
        lucide.createIcons();
    }

    function addMatchRow() {
        let html = `
        <div class="match-row">
            <input type="text" name="jodoh_premis[]" class="z-input" placeholder="Premis (Kiri)">
            <i data-lucide="arrow-right" style="color:#cbd5e1;"></i>
            <input type="text" name="jodoh_respon[]" class="z-input" placeholder="Respon / Jawaban (Kanan)">
            <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;justify-content:center;align-items:center;background:#fee2e2;color:#ef4444;border-radius:6px;" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="x" style="width:14px;height:14px;"></i></button>
        </div>`;
        $('#jodoh-container').append(html);
        lucide.createIcons();
    }
</script>
</body>
</html>
