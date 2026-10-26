<?php
// perpus_digital.php
require_once 'koneksi.php';

$page_title = 'Perpustakaan Digital';
include 'header.php';

// Ambil kategori
$kategori_stmt = $core_db->query("SELECT * FROM perpus_kategori ORDER BY nama_kategori ASC");
$kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter kategori
$filter_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$search_query = isset($_GET['q']) ? $_GET['q'] : '';

// Query Buku
$sql = "SELECT e.*, k.nama_kategori 
        FROM perpus_ebook_v2 e
        LEFT JOIN perpus_kategori k ON e.kategori_id = k.id
        WHERE 1=1";
$params = [];

if ($filter_kategori > 0) {
    $sql .= " AND e.kategori_id = :kat";
    $params['kat'] = $filter_kategori;
}

if (!empty($search_query)) {
    $sql .= " AND e.judul LIKE :sq";
    $params['sq'] = "%$search_query%";
}

$sql .= " ORDER BY e.id DESC";

$stmt = $core_db->prepare($sql);
$stmt->execute($params);
$buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .perpus-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 4rem 1.5rem 3rem;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-top: -80px; /* Overlap with default header space */
    }
    
    .perpus-header h1 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .perpus-header p {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .search-box {
        background: white;
        border-radius: 16px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .search-box input {
        border: none;
        outline: none;
        flex-grow: 1;
        font-size: 0.95rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-main);
    }

    .search-box i {
        color: var(--text-muted);
    }

    .kategori-scroll {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding: 5px 2px;
        margin: 20px 0;
        scrollbar-width: none;
    }
    .kategori-scroll::-webkit-scrollbar {
        display: none;
    }

    .kat-badge {
        background: white;
        color: var(--text-muted);
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
        text-decoration: none;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .kat-badge.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .buku-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }

    .buku-card {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s;
    }
    
    .buku-card:active {
        transform: scale(0.98);
    }

    .buku-cover {
        width: 100%;
        aspect-ratio: 3/4;
        background-color: #f1f5f9;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .buku-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .buku-cover .no-cover {
        font-size: 3rem;
        color: #cbd5e1;
    }

    .buku-info {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .buku-judul {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
        color: var(--text-main);
    }

    .buku-kategori {
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .buku-stats {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    
    /* Sembunyikan bottom-nav bawaan header/footer apk jika publik */
    .bottom-nav {
        display: none !important;
    }
</style>

<div class="perpus-header">
    <h1>E-Library Publik</h1>
    <p>Akses ribuan buku dan modul belajar secara gratis.</p>
    
    <form method="GET" action="perpus_digital.php">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" placeholder="Cari judul buku..." value="<?= htmlspecialchars($search_query) ?>">
            <?php if($filter_kategori > 0): ?>
                <input type="hidden" name="kategori" value="<?= $filter_kategori ?>">
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="main-content" style="padding-top: 10px;">
    
    <div class="kategori-scroll">
        <a href="perpus_digital.php" class="kat-badge <?= ($filter_kategori == 0) ? 'active' : '' ?>">Semua</a>
        <?php foreach($kategori_list as $kat): ?>
            <a href="perpus_digital.php?kategori=<?= $kat['id'] ?>" class="kat-badge <?= ($filter_kategori == $kat['id']) ? 'active' : '' ?>">
                <?= htmlspecialchars($kat['nama_kategori']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php if(!empty($search_query)): ?>
        <p style="margin-bottom: 15px; font-size: 0.9rem; color: var(--text-muted);">
            Hasil pencarian untuk: <b>"<?= htmlspecialchars($search_query) ?>"</b>
        </p>
    <?php endif; ?>

    <div class="buku-grid">
        <?php if(count($buku_list) > 0): ?>
            <?php foreach($buku_list as $buku): 
                // Asumsi cover tersimpan di folder uploads/ebook_covers/ atau serupa. Kita gunakan fallback.
                $cover_url = '';
                if (!empty($buku['cover'])) {
                    $cover_url = '/public/uploads/buku/' . $buku['cover']; // Sesuaikan dengan struktur aktual
                }
            ?>
            <a href="baca_buku.php?id=<?= $buku['id'] ?>" class="buku-card">
                <div class="buku-cover">
                    <?php if(!empty($cover_url)): ?>
                        <img src="<?= $cover_url ?>" alt="Cover" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fa-solid fa-book no-cover\'></i>';">
                    <?php else: ?>
                        <i class="fa-solid fa-book no-cover"></i>
                    <?php endif; ?>
                </div>
                <div class="buku-info">
                    <div class="buku-judul"><?= htmlspecialchars($buku['judul']) ?></div>
                    <div class="buku-kategori"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum') ?></div>
                    <div class="buku-stats">
                        <i class="fa-solid fa-eye"></i> <?= number_format($buku['unduhan'] ?? 0) ?>x dibaca
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px dashed var(--border-color);">
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: var(--text-muted); font-weight: 500;">Tidak ada buku yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Footer Kosong / Minimalist karena publik -->
<div style="padding-bottom: 40px; background: var(--bg-color); text-align: center; font-size: 0.8rem; color: var(--text-muted);">
    &copy; <?= date('Y') ?> MAS Mifhda
</div>

<?php 
// Sertakan script penutup tanpa navigasi bawah bawaan (jika terpaksa kita tutup tag manual)
?>
</body>
</html>
