<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek akses: Kamad atau Wakakur
$is_supervisi = false;

$stmt_guru = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt_guru->execute(['id' => $guru_id]);
$guru_login = $stmt_guru->fetch(PDO::FETCH_ASSOC);

if ($guru_login && $guru_login['is_kamad'] == 1) {
    $is_supervisi = true;
} else {
    // Cek apakah waka kurikulum di tabel guru_tugas
    try {
        $stmt_waka = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
        $stmt_waka->execute([$guru_id]);
        if ($stmt_waka->fetch()) {
            $is_supervisi = true;
        }
    } catch(Exception $e) {}
}

if (!$is_supervisi) {
    header("Location: akses_ditolak.php");
    exit;
}

$kategori_list = [
    'Modul Ajar (RPP Plus)',
    'CP, TP & ATP',
    'Modul Projek (P5)',
    'Asesmen (Formatif/Sumatif)',
    'Prota & Promes',
    'Bahan Ajar Tambahan',
    'Lainnya'
];

// Proses pemberian review/catatan cinta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review_berkas') {
    $berkas_id = intval($_POST['berkas_id']);
    $status_review = $_POST['status_review'];
    $catatan_kamad = trim($_POST['catatan_kamad'] ?? '');
    $guru_target_id = intval($_POST['guru_target_id']);
    
    $stmt_update = $core_db->prepare("UPDATE guru_perangkat SET status_review = ?, catatan_kamad = ? WHERE id = ?");
    $stmt_update->execute([$status_review, $catatan_kamad, $berkas_id]);
    
    header("Location: monitoring_perangkat.php?guru_id=" . $guru_target_id . "&success=review");
    exit;
}

if (isset($_GET['success']) && $_GET['success'] == 'review') {
    $success_msg = "Catatan cinta dan evaluasi berhasil disimpan! ❤️";
}

$page_title = 'Supervisi Perangkat';
include 'header.php';
?>
<style>
    .teacher-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: inherit; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .teacher-card:hover { border-color: #ec4899; box-shadow: 0 4px 6px rgba(236,72,153,0.1); }
</style>

<div class="header" style="background: linear-gradient(135deg, #ec4899 0%, #e11d48 100%); padding-bottom: 70px;">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.5rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 24px;">
            <p style="margin: 0; font-size: 0.8rem; opacity: 0.9; color: #fce7f3;">Kurikulum Merdeka</p>
            <h1 style="margin: 0; font-size: 1.2rem; color: white;">Monitoring Perangkat</h1>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if (isset($success_msg)): ?>
        <div style="background: #fdf2f8; color: #be185d; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; border-left: 4px solid #ec4899; margin-top: -30px; position:relative; z-index:10;">
            <i class="fa-solid fa-heart" style="color: #ec4899;"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($_GET['guru_id'])): ?>
        <!-- DAFTAR GURU -->
        <div class="data-card" style="margin-top: -40px; z-index: 10;">
            <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px; color: #be185d;"><i class="fa-solid fa-users"></i> Daftar Guru</h3>
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;">Pilih guru untuk melihat dan mensupervisi perangkat pembelajarannya.</p>
            
            <?php
            // Ambil semua guru beserta rekap perangkatnya
            $sql_rekap = "
                SELECT g.id, g.nama, g.foto,
                    (SELECT COUNT(*) FROM guru_perangkat p WHERE p.guru_id = g.id) as total_berkas,
                    (SELECT COUNT(*) FROM guru_perangkat p WHERE p.guru_id = g.id AND p.status_review = 'Menunggu') as total_menunggu
                FROM guru g
                WHERE g.id != 1
                ORDER BY total_menunggu DESC, g.nama ASC
            ";
            $stmt_rekap = $core_db->query($sql_rekap);
            $gurus = $stmt_rekap->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div style="display: flex; flex-direction: column;">
                <?php foreach($gurus as $g): 
                    $foto = !empty($g['foto']) ? '/public/uploads/guru/' . $g['foto'] : '/public/assets/img/default-avatar.png';
                ?>
                    <a href="?guru_id=<?= $g['id'] ?>" class="teacher-card">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="<?= htmlspecialchars($foto) ?>" alt="Foto" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #fbcfe8;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b;"><?= htmlspecialchars($g['nama']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                                    <i class="fa-solid fa-folder-open"></i> <?= $g['total_berkas'] ?> Dokumen
                                </div>
                            </div>
                        </div>
                        <?php if($g['total_menunggu'] > 0): ?>
                            <div style="background: #e11d48; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; flex-shrink: 0;">
                                <?= $g['total_menunggu'] ?>
                            </div>
                        <?php else: ?>
                            <i class="fa-solid fa-chevron-right" style="color: #cbd5e1;"></i>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: 
        $target_guru_id = intval($_GET['guru_id']);
        
        $stmt_tg = $core_db->prepare("SELECT nama, foto FROM guru WHERE id = ?");
        $stmt_tg->execute([$target_guru_id]);
        $target_guru = $stmt_tg->fetch(PDO::FETCH_ASSOC);
        
        if (!$target_guru) {
            echo "Guru tidak ditemukan.";
            exit;
        }

        // Fetch perangkat guru target
        $stmt_perangkat = $core_db->prepare("SELECT * FROM guru_perangkat WHERE guru_id = ? ORDER BY kelas_nama ASC, mapel_nama ASC, kategori ASC, created_at DESC");
        $stmt_perangkat->execute([$target_guru_id]);
        $all_perangkat = $stmt_perangkat->fetchAll(PDO::FETCH_ASSOC);

        $grouped_perangkat = [];
        foreach ($all_perangkat as $p) {
            $kelas = isset($p['kelas_nama']) ? $p['kelas_nama'] : 'Umum';
            $map = $p['mapel_nama'];
            
            $group_key = $map;
            if ($kelas !== 'Umum') {
                $group_key .= " - Kelas " . $kelas;
            }

            $kat = $p['kategori'];
            if (!isset($grouped_perangkat[$group_key])) $grouped_perangkat[$group_key] = [];
            if (!isset($grouped_perangkat[$group_key][$kat])) $grouped_perangkat[$group_key][$kat] = [];
            $grouped_perangkat[$group_key][$kat][] = $p;
        }
        
        $foto_target = !empty($target_guru['foto']) ? '/public/uploads/guru/' . $target_guru['foto'] : '/public/assets/img/default-avatar.png';
    ?>
        <div class="data-card" style="margin-top: -40px; z-index: 10; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="<?= htmlspecialchars($foto_target) ?>" alt="Foto" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ec4899;">
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;"><?= htmlspecialchars($target_guru['nama']) ?></h3>
                    <p style="margin: 3px 0 0 0; font-size: 0.8rem; color: #64748b;">Supervisi Perangkat Pembelajaran</p>
                </div>
            </div>
            
            <a href="monitoring_perangkat.php" style="display: inline-block; margin-top: 15px; background: #f1f5f9; color: #475569; padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; text-decoration: none; font-weight: bold;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Guru</a>
        </div>

        <?php 
        $has_any_docs = false;
        foreach($grouped_perangkat as $mapel => $kategori_docs): 
            $has_any_docs = true;
        ?>
            <div style="background: white; border-radius: 12px; margin-bottom: 20px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div style="background: linear-gradient(to right, #fdf2f8, #ffffff); padding: 15px; border-bottom: 2px solid #fbcfe8;">
                    <h3 style="margin: 0; font-size: 1.1rem; color: #be185d; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-book-bookmark"></i> Mata Pelajaran: <?= htmlspecialchars($mapel) ?>
                    </h3>
                </div>
                
                <div style="padding: 15px;">
                    <?php foreach($kategori_docs as $kategori => $docs): 
                        if(empty($docs)) continue;
                    ?>
                        <div style="margin-bottom: 15px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 0.95rem; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-folder-open" style="color: #ec4899; margin-right: 5px;"></i> <?= htmlspecialchars($kategori) ?></span>
                                <span style="background: #e2e8f0; color: #475569; font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; font-weight: bold;"><?= count($docs) ?> file</span>
                            </h4>
                            
                            <?php foreach($docs as $b): 
                                $status_color = '#94a3b8'; // Menunggu
                                $status_bg = '#f1f5f9';
                                $status_icon = 'fa-clock';
                                if ($b['status_review'] == 'Sempurna') {
                                    $status_color = '#10b981';
                                    $status_bg = '#dcfce7';
                                    $status_icon = 'fa-check-circle';
                                } elseif ($b['status_review'] == 'Perlu Disempurnakan') {
                                    $status_color = '#eab308';
                                    $status_bg = '#fef9c3';
                                    $status_icon = 'fa-pen-to-square';
                                }
                            ?>
                                <div style="padding: 15px 15px; border-bottom: 1px dashed #e2e8f0; background: #f8fafc; border-radius: 8px; margin-bottom: 8px;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <div>
                                            <div style="font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 3px;"><?= htmlspecialchars($b['nama_berkas']) ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 8px;">
                                                Diunggah: <?= date('d M Y, H:i', strtotime($b['created_at'])) ?>
                                            </div>
                                            <div style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 12px; background: <?= $status_bg ?>; color: <?= $status_color ?>; font-size: 0.75rem; font-weight: bold;">
                                                <i class="fa-solid <?= $status_icon ?>"></i> <?= htmlspecialchars($b['status_review']) ?>
                                            </div>
                                        </div>
                                        <a href="pdf_viewer.php?type=perangkat&id=<?= $b['id'] ?>" style="background: white; border: 1px solid #0284c7; color: #0284c7; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 5px;"><i class="fa-solid fa-eye"></i> Lihat File</a>
                                    </div>

                                    <!-- Form Review -->
                                    <form method="POST" style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #cbd5e1; margin: 0;">
                                        <input type="hidden" name="action" value="review_berkas">
                                        <input type="hidden" name="berkas_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="guru_target_id" value="<?= $target_guru_id ?>">
                                        
                                        <div style="margin-bottom: 12px;">
                                            <label style="font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 5px; display: block;">Status Supervisi</label>
                                            <div style="display: flex; gap: 15px;">
                                                <label style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem; color: #10b981; cursor: pointer;">
                                                    <input type="radio" name="status_review" value="Sempurna" <?= $b['status_review'] == 'Sempurna' ? 'checked' : '' ?> required> Sempurna (ACC)
                                                </label>
                                                <label style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem; color: #eab308; cursor: pointer;">
                                                    <input type="radio" name="status_review" value="Perlu Disempurnakan" <?= $b['status_review'] == 'Perlu Disempurnakan' ? 'checked' : '' ?> required> Perlu Disempurnakan
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div style="margin-bottom: 12px;">
                                            <label style="font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 5px; display: block;">Catatan Penuh Cinta (Opsional)</label>
                                            <textarea name="catatan_kamad" rows="2" placeholder="Berikan apresiasi atau saran membangun..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'Outfit', sans-serif; resize: vertical;"><?= htmlspecialchars($b['catatan_kamad'] ?? '') ?></textarea>
                                        </div>
                                        
                                        <div style="text-align: right;">
                                            <button type="submit" style="background: #ec4899; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: bold; cursor: pointer;"><i class="fa-solid fa-paper-plane"></i> Simpan Review</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(!$has_any_docs): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                <p style="font-size: 0.95rem; font-weight: bold; color: #64748b; margin: 0;">Belum Ada Dokumen</p>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">Guru ini belum mengunggah perangkat pembelajaran apa pun.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'footer_guru.php'; ?>
