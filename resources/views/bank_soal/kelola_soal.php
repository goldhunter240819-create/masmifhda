<?php
if (!isset($page_title)) $page_title = "Pilih Mapel - Bank Soal";
if (!isset($active_page)) $active_page = "kelola_soal";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .welcome-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.5;
        }
        .mapel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .mapel-card { 
            background: linear-gradient(145deg, #ffffff, #f8fafc); 
            border-radius: 12px; padding: 1.5rem; text-align: center;
            border: 1px solid #e2e8f0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; color: inherit; display: block;
            position: relative; overflow: hidden;
        }
        /* Motif Background Bintik-bintik halus (Pattern) */
        .mapel-card::after {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(#94a3b8 1px, transparent 1px);
            background-size: 16px 16px; opacity: 0.1; pointer-events: none;
            z-index: 0;
        }
        /* Efek Glow di Pojok Kanan Atas */
        .mapel-card::before {
            content: ''; position: absolute; top: 0; right: 0; width: 120px; height: 120px;
            background: radial-gradient(circle at top right, rgba(14,165,233,0.15), transparent 70%);
            border-radius: 0 12px 0 100px; z-index: 0; transition: 0.3s;
        }
        .mapel-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.15), 0 8px 10px -6px rgba(14, 165, 233, 0.1);
            border-color: #38bdf8;
        }
        .mapel-card:hover::before {
            background: radial-gradient(circle at top right, rgba(14,165,233,0.25), transparent 70%);
            transform: scale(1.2);
        }
        .mapel-icon, .mapel-title, .mapel-meta { position: relative; z-index: 1; }
        .mapel-icon {
            width: 64px; height: 64px; border-radius: 16px; background: #f0f9ff; color: #0ea5e9;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;
            transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.1);
        }
        .mapel-card:hover .mapel-icon { 
            background: linear-gradient(135deg, #0ea5e9, #0284c7); 
            color: #ffffff; transform: scale(1.1) rotate(5deg); 
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
        }
        .mapel-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }
        .mapel-meta {
            font-size: 0.85rem;
            color: #64748b;
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
            <!-- Hero Banner -->
            <div class="elite-page-header welcome-hero" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="book-copy" style="color: rgba(255,255,255,0.8);"></i> Pilih Mata Pelajaran
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Silakan pilih Topik Ujian terlebih dahulu, lalu klik kartu mata pelajaran untuk mengelola soal di dalamnya.</p>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="z-card" style="padding: 1.5rem; margin-bottom: 2rem; border-left: 4px solid #0ea5e9;">
                <form action="" method="GET" style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
                    <div class="z-form-group" style="margin: 0; flex-grow: 1; max-width: 400px;">
                        <label>Pilih Topik Ujian</label>
                        <select name="topik_id" class="z-input" onchange="this.form.submit()">
                            <option value="">-- Silakan Pilih Topik Ujian --</option>
                            <?php foreach($topiks as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($selected_topik == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['jenis'] . ' - ' . $t['nama_topik']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if($selected_topik): ?>
                    <a href="<?= base_url('bank_soal/kelola_soal') ?>" class="btn btn-outline" style="height: 42px;"><i data-lucide="refresh-cw"></i> Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if(!$selected_topik): ?>
            <!-- Empty State / Placeholder before selection -->
            <div class="z-card" id="emptyState" style="text-align: center; padding: 5rem 1rem; color: #64748b; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px;">
                <i data-lucide="mouse-pointer-2" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 1.5rem; transform: rotate(-15deg);"></i>
                <h3 style="margin: 0 0 0.5rem 0; color: #334155; font-size: 1.25rem;">Silakan pilih topik ujian terlebih dahulu</h3>
                <p style="margin: 0; font-size: 0.95rem; color: #94a3b8;">Daftar mata pelajaran akan muncul setelah topik dipilih.</p>
            </div>
            <?php else: ?>
            
            <div class="z-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                    <h3 style="margin: 0;">Mata Pelajaran (SIAKAD)</h3>
                    <span class="z-badge" style="background:#e0f2fe; color:#0284c7;">Total: <?= count($mapels) ?> Mapel</span>
                </div>

                <!-- Mapel Grid -->
                <div class="mapel-grid">
                    <?php foreach($mapels as $m): ?>
                    <a href="<?= base_url('bank_soal/daftar_soal?topik_id=' . $selected_topik . '&mapel=' . urlencode($m['mapel_nama'])) ?>" class="mapel-card">
                        <div class="mapel-icon"><i data-lucide="book-open" style="width: 32px; height: 32px;"></i></div>
                        <h3 class="mapel-title"><?= htmlspecialchars($m['mapel_nama']) ?></h3>
                        <div class="mapel-meta"><?= $m['jml_soal'] ?> Soal Tersedia</div>
                    </a>
                    <?php endforeach; ?>

                    <?php if(empty($mapels)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                        <p style="color: #64748b; margin-bottom: 1rem;">Mata pelajaran di database SIAKAD masih kosong.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php endif; ?>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.querySelector('.z-sidebar').classList.toggle('open');
    }
    
    // Tombol tambah mapel manual dihapus karena mapel langsung ditarik dari SIAKAD
</script>
</body>
</html>
