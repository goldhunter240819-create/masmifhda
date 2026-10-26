<?php
// GHTech Analytics Tracking
$app_key = "f7254c5e179f82b9edb4dfd2941ee199";
$action = urlencode("Mengunjungi E-Library");
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $app_key . "&ip=" . $_SERVER['REMOTE_ADDR'] . "&action=" . $action);

// Perpustakaan Umum - Standalone
require_once __DIR__ . '/../vendor/autoload.php';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        $protocol = $isSecure ? 'https' : 'http';
        
        // Deteksi jika diakses via subdomain e-library
        if (strpos($_SERVER['HTTP_HOST'], 'e-library') !== false) {
            // Gunakan domain utama untuk mengambil aset public
            $host = str_replace('e-library', 'apk', $_SERVER['HTTP_HOST']);
            $base = 'https://' . $host; // Force HTTPS untuk WebView APK agar tidak kena Mixed Content
        } else {
            $host = $_SERVER['HTTP_HOST'];
            $base = $protocol . '://' . $host . '/masmifhda';
        }
        
        return $base . '/' . ltrim($path, '/');
    }
}

$db = \App\Core\Database::connect('core');

// Helper for Lazy Thumbnail Generation
if (!function_exists('get_cover_thumbnail')) {
    function get_cover_thumbnail($cover_filename) {
        if (empty($cover_filename)) return '';
        
        $upload_dir = __DIR__ . '/../public/uploads/buku/';
        $original_path = $upload_dir . $cover_filename;
        $thumb_filename = 'thumb_' . $cover_filename;
        $thumb_path = $upload_dir . $thumb_filename;
        
        // Jika thumbnail sudah ada, langsung return
        if (file_exists($thumb_path)) {
            return $thumb_filename;
        }
        
        // Jika aslinya nggak ada atau GD library nggak aktif, return aslinya
        if (!file_exists($original_path) || !function_exists('imagecreatefromjpeg')) {
            return $cover_filename;
        }
        
        // Proses pembuatan thumbnail
        $ext = strtolower(pathinfo($original_path, PATHINFO_EXTENSION));
        $img = null;
        
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $img = @imagecreatefromjpeg($original_path);
        } elseif ($ext == 'png') {
            $img = @imagecreatefrompng($original_path);
        }
        
        if ($img !== false && $img !== null) {
            $width = imagesx($img);
            $height = imagesy($img);
            $new_width = 400; // Ukuran optimal untuk HP & Laptop (Grid)
            
            // Kalau gambar aslinya udah kecil, gausah di-resize, langsung copy aja
            if ($width <= $new_width) {
                @copy($original_path, $thumb_path);
                return file_exists($thumb_path) ? $thumb_filename : $cover_filename;
            }
            
            $new_height = floor($height * ($new_width / $width));
            $tmp_img = imagecreatetruecolor($new_width, $new_height);
            
            if ($ext == 'png') {
                imagealphablending($tmp_img, false);
                imagesavealpha($tmp_img, true);
                $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
                imagefilledrectangle($tmp_img, 0, 0, $new_width, $new_height, $transparent);
            }
            
            imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
            if (in_array($ext, ['jpg', 'jpeg'])) {
                imagejpeg($tmp_img, $thumb_path, 80); // Kompresi 80%
            } elseif ($ext == 'png') {
                imagepng($tmp_img, $thumb_path, 8); // Kompresi PNG medium
            }
            
            imagedestroy($img);
            imagedestroy($tmp_img);
            
            if (file_exists($thumb_path)) {
                return $thumb_filename;
            }
        }
        return $cover_filename;
    }
}

// Get categories
$kat_stmt = $db->query("SELECT * FROM perpus_kategori ORDER BY nama_kategori ASC");
$kategori_list = $kat_stmt->fetchAll();

// Get Institution Branding
$inst_stmt = $db->query("SELECT * FROM institusi LIMIT 1");
$institusi = $inst_stmt->fetch();
$nama_sekolah = $institusi['nama'] ?? $institusi['nama_institusi'] ?? 'Perpustakaan Sekolah';
// Hapus Mathla'ul Anwar
$nama_sekolah = trim(str_ireplace([" MATHLA'UL ANWAR", " MATHLAUL ANWAR", " mathla'ul anwar", " mathlaul anwar"], "", $nama_sekolah));
$logo_sekolah = \App\Core\Branding::getInstitusiLogo();
$favicon_sekolah = \App\Core\Branding::getInstitusiFavicon();

// Get filters
$kat_id = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$search = isset($_GET['q']) ? $_GET['q'] : '';
$show_all = isset($_GET['all']) ? true : false;

// Query books
$sql = "SELECT e.*, k.nama_kategori 
        FROM buku_bacaan e
        LEFT JOIN perpus_kategori k ON e.kategori_id = k.id
        WHERE 1=1";
$params = [];

if ($kat_id > 0) {
    $sql .= " AND e.kategori_id = ?";
    $params[] = $kat_id;
}
if (!empty($search)) {
    $sql .= " AND e.judul LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY e.id DESC";

// Limit to latest 5 if no filters applied and not requesting all
$is_filtering = !empty($search) || $kat_id > 0;
if (!$is_filtering && !$show_all) {
    $sql .= " LIMIT 5";
}
$stmt = $db->prepare($sql);
$stmt->execute($params);
$buku_list = $stmt->fetchAll();

// Get popular books for homepage
$popular_books = [];
if (!$is_filtering && !$show_all) {
    $pop_sql = "SELECT e.*, k.nama_kategori 
                FROM buku_bacaan e
                LEFT JOIN perpus_kategori k ON e.kategori_id = k.id
                WHERE e.dilihat > 0
                ORDER BY e.dilihat DESC, e.id DESC LIMIT 5";
    $popular_books = $db->query($pop_sql)->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library | MA Miftahul Huda</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_sekolah) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'blob': 'blob 10s infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '100%': { transform: 'translate(30px, -50px) scale(1.1)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { background-color: #f8fafc; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .glass-header {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -10px rgba(20, 184, 166, 0.15);
            border-color: rgba(20, 184, 166, 0.2);
        }

        .cat-badge {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.3s ease;
        }
        
        .cat-badge:hover {
            background: #f1f5f9;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .cat-badge.active {
            background: linear-gradient(135deg, #14b8a6, #0ea5e9);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
        }

        .search-input {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }
        
        .search-input:focus {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
        }

        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            overflow: hidden;
            background: #f8fafc;
        }
        
        .book-cover-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            background: #f1f5f9;
        }
        
        .book-cover-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0, 0.5) 0%, rgba(0,0,0, 0) 60%);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .glass-card:hover .book-cover-wrapper::after {
            opacity: 0.4;
        }
        
        .btn-read {
            background: #f1f5f9;
            color: #475569;
            transition: all 0.3s ease;
        }
        
        .glass-card:hover .btn-read {
            background: #14b8a6;
            color: white;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }
    </style>
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white">

    <!-- Animated Background (Light Mode) -->
    <div class="mesh-bg">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-cyan-200/40 rounded-full mix-blend-multiply filter blur-[80px] animate-blob"></div>
        <div class="absolute top-[20%] right-[-5%] w-[400px] h-[400px] bg-blue-200/40 rounded-full mix-blend-multiply filter blur-[80px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-teal-200/40 rounded-full mix-blend-multiply filter blur-[80px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <!-- Header -->
    <header class="glass-header sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between">
                <a href="./" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm border border-slate-100 group-hover:scale-105 transition-transform duration-300 overflow-hidden p-1.5">
                        <img src="<?= htmlspecialchars($logo_sekolah) ?>" alt="Logo Sekolah" class="w-full h-full object-contain">
                    </div>
                    <div class="overflow-hidden">
                        <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight truncate max-w-[200px] sm:max-w-none group-hover:text-brand-600 transition-colors"><?= htmlspecialchars($nama_sekolah) ?></h1>
                        <p class="text-[10px] md:text-[11px] text-brand-600 font-semibold tracking-widest uppercase flex items-center gap-1">
                            <i class="fa-solid fa-book-open-reader"></i> E-Library Public
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 md:pt-10 pb-6 md:pb-8 text-center z-10">
        
        <!-- Search Form (Moved to Top) -->
        <form method="GET" action="" class="max-w-2xl mx-auto relative group shadow-xl shadow-brand-500/5 rounded-full mb-10 md:mb-14">
            <input type="hidden" name="kategori" value="<?= $kat_id ?>">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-brand-500 transition-colors text-sm md:text-base"></i>
            </div>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari buku, topik..." 
                   class="search-input w-full rounded-full py-3.5 md:py-4 pl-12 pr-16 md:pr-32 text-sm md:text-lg outline-none placeholder-slate-400 bg-white/90 backdrop-blur-sm border-white/50">
            <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 md:right-2 md:top-2 md:bottom-2 bg-gradient-to-r from-brand-500 to-blue-500 hover:from-brand-600 hover:to-blue-600 text-white w-10 md:w-auto md:px-8 rounded-full font-bold shadow-md shadow-brand-500/20 transition-transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2 text-sm md:text-base">
                <span class="hidden md:inline">Cari</span>
                <i class="fa-solid fa-arrow-right md:hidden"></i>
            </button>
        </form>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50/80 backdrop-blur-sm text-brand-600 text-[11px] md:text-xs font-bold mb-4 md:mb-6 border border-brand-200/50 shadow-sm animate-float">
            <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-brand-500 animate-pulse"></span>
            Ribuan Koleksi Digital
        </div>
        <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-800 mb-4 md:mb-6 tracking-tight leading-[1.15]">
            Jelajahi Dunia <br class="sm:hidden"><span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-500 to-blue-600">Pengetahuan</span>
        </h2>
        <p class="text-slate-500 text-sm sm:text-base md:text-xl max-w-2xl mx-auto mb-8 font-medium leading-relaxed px-2 md:px-0">
            Akses tak terbatas ke berbagai macam literatur digital. Baca, pelajari, dan kembangkan dirimu kapan saja, di mana saja.
        </p>

    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 z-10 relative">
        
        <!-- Categories Filter (Horizontal Scroll on Mobile) -->
        <div class="flex overflow-x-auto no-scrollbar items-center md:justify-center gap-3 mb-8 pb-2 px-1 -mx-4 sm:mx-0 sm:px-0 snap-x">
            <div class="w-2 sm:hidden shrink-0"></div> <!-- Spacer for mobile edge -->
            <a href="?all=1" class="snap-start shrink-0 cat-badge px-5 py-2 md:px-6 md:py-2.5 rounded-full text-xs md:text-sm font-semibold shadow-sm <?= ($kat_id == 0 && empty($search)) ? 'active' : '' ?>">
                Semua Koleksi
            </a>
            <?php foreach($kategori_list as $kat): ?>
                <a href="?kategori=<?= $kat['id'] ?>&q=<?= urlencode($search) ?>" class="snap-start shrink-0 cat-badge px-5 py-2 md:px-6 md:py-2.5 rounded-full text-xs md:text-sm font-semibold shadow-sm <?= ($kat_id == $kat['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </a>
            <?php endforeach; ?>
            <div class="w-2 sm:hidden shrink-0"></div> <!-- Spacer for mobile edge -->
        </div>

        <!-- Popular Books Section -->
        <?php if (!$is_filtering && !$show_all && count($popular_books) > 0): ?>
            <div class="flex items-center justify-between mb-4 md:mb-6 mt-2">
                <h2 class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                        <i class="fa-solid fa-fire text-sm"></i>
                    </span>
                    Paling Populer
                </h2>
            </div>
            
            <div class="flex flex-nowrap md:flex-wrap overflow-x-auto no-scrollbar md:justify-center gap-4 md:gap-6 mb-12 pb-4 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 scroll-pl-4 after:content-[''] after:w-px after:shrink-0 sm:after:hidden">
                <?php foreach($popular_books as $buku): ?>
                    <a href="baca?id=<?= $buku['id'] ?>" class="snap-start w-[140px] shrink-0 sm:w-[calc(33.333%-0.667rem)] md:w-[calc(25%-1.125rem)] lg:w-[calc(20%-1.2rem)] block relative rounded-2xl overflow-hidden group aspect-[2/3] shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-slate-100/50 bg-slate-100">
                        
                        <!-- Book Cover Background -->
                        <?php if(!empty($buku['cover_image'])): 
                            $cover_src = get_cover_thumbnail($buku['cover_image']);
                        ?>
                            <img src="<?= base_url('public/uploads/buku/' . htmlspecialchars($cover_src)) ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Cover">
                        <?php else: ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-200 text-slate-400">
                                <i class="fa-solid fa-book text-4xl mb-2 opacity-50"></i>
                            </div>
                        <?php endif; ?>

                        <!-- View Count Badge (Top Left) -->
                        <div class="absolute top-3 left-3 z-20 bg-slate-900/80 border border-white/10 text-white text-[9px] sm:text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-eye text-brand-400"></i> <?= isset($buku['dilihat']) ? number_format($buku['dilihat']) : 0 ?>x
                        </div>

                        <!-- Category Tag (Top Right) -->
                        <div class="absolute top-3 right-3 z-20 bg-black/40 border border-white/20 text-white text-[9px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full">
                            <?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum') ?>
                        </div>

                        <!-- Bottom Dark Overlay for Text Legibility -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/60 to-transparent opacity-90 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-3 flex flex-col justify-end translate-y-3 group-hover:translate-y-0 transition-transform duration-300 z-10">
                            <h3 class="font-extrabold text-white text-xs sm:text-sm leading-snug line-clamp-2 mb-1">
                                <?= htmlspecialchars($buku['judul']) ?>
                            </h3>
                            <p class="text-[9px] sm:text-[10px] font-medium text-slate-300 line-clamp-1 mb-1">
                                <i class="fa-solid fa-align-left text-[8px] mr-1 opacity-70"></i> <?= htmlspecialchars($buku['deskripsi'] ?? 'Tanpa deskripsi') ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Section Title -->
        <?php if (!$is_filtering && !$show_all && count($buku_list) > 0): ?>
            <div class="flex items-center justify-between mb-4 md:mb-6 mt-4">
                <h2 class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center text-brand-600">
                        <i class="fa-solid fa-star text-sm"></i>
                    </span>
                    Rilisan Terbaru
                </h2>
                <a href="?all=1" class="text-[11px] md:text-xs font-bold text-brand-600 hover:text-brand-700 bg-brand-50 px-3 py-1.5 rounded-full transition-colors">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
        <?php elseif (!empty($search)): ?>
            <div class="mb-4 mt-2 px-1">
                <h3 class="text-lg md:text-xl font-bold text-slate-800">Hasil Pencarian: <span class="text-brand-600">"<?= htmlspecialchars($search) ?>"</span></h3>
            </div>
        <?php endif; ?>

        <!-- Books Grid / Scroll -->
        <?php if (count($buku_list) > 0): ?>
            <?php 
                $is_grid_mode = ($show_all || $is_filtering);
                $grid_classes = $is_grid_mode
                    ? "flex flex-wrap justify-center gap-4 md:gap-6 pb-4"
                    : "flex flex-nowrap md:flex-wrap overflow-x-auto no-scrollbar md:justify-center gap-4 md:gap-6 pb-4 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 scroll-pl-4 after:content-[''] after:w-px after:shrink-0 sm:after:hidden";
                
                $card_classes = $is_grid_mode
                    ? "w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.667rem)] md:w-[calc(25%-1.125rem)] lg:w-[calc(20%-1.2rem)] block relative rounded-2xl overflow-hidden group aspect-[2/3] shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-slate-100/50 bg-slate-100 shrink-0"
                    : "snap-start w-[140px] shrink-0 sm:w-[calc(33.333%-0.667rem)] md:w-[calc(25%-1.125rem)] lg:w-[calc(20%-1.2rem)] block relative rounded-2xl overflow-hidden group aspect-[2/3] shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-slate-100/50 bg-slate-100";
            ?>
            <div class="<?= $grid_classes ?>">
                <?php foreach($buku_list as $buku): ?>
                    <a href="baca?id=<?= $buku['id'] ?>" class="<?= $card_classes ?>">
                        
                        <!-- Book Cover Background -->
                        <?php if(!empty($buku['cover_image'])): 
                            $cover_src = get_cover_thumbnail($buku['cover_image']);
                        ?>
                            <img src="<?= base_url('public/uploads/buku/' . htmlspecialchars($cover_src)) ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Cover">
                        <?php else: ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-200 text-slate-400">
                                <i class="fa-solid fa-book text-4xl mb-2 opacity-50"></i>
                            </div>
                        <?php endif; ?>

                        <!-- View Count Badge (Top Left) -->
                        <div class="absolute top-3 left-3 z-20 bg-slate-900/80 border border-white/10 text-white text-[9px] sm:text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-eye text-brand-400"></i> <?= isset($buku['dilihat']) ? number_format($buku['dilihat']) : 0 ?>x
                        </div>

                        <!-- Category Tag (Top Right) -->
                        <div class="absolute top-3 right-3 z-20 bg-black/40 border border-white/20 text-white text-[9px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full">
                            <?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum') ?>
                        </div>

                        <!-- Bottom Dark Overlay for Text Legibility -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/60 to-transparent opacity-90 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-3 flex flex-col justify-end translate-y-3 group-hover:translate-y-0 transition-transform duration-300 z-10">
                            <h3 class="font-extrabold text-white text-xs sm:text-sm leading-snug line-clamp-2 mb-1">
                                <?= htmlspecialchars($buku['judul']) ?>
                            </h3>
                            <p class="text-[9px] sm:text-[10px] font-medium text-slate-300 line-clamp-1 mb-1">
                                <i class="fa-solid fa-align-left text-[8px] mr-1 opacity-70"></i> <?= htmlspecialchars($buku['deskripsi'] ?? 'Tanpa deskripsi') ?>
                            </p>
                            
                            <!-- Read Button (Appears on Hover) -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-full">
                                <div class="w-full bg-brand-500/90 hover:bg-brand-500 text-white text-[10px] sm:text-xs font-bold py-1.5 sm:py-2 rounded-lg text-center shadow-lg backdrop-blur-sm border border-brand-400/50 flex items-center justify-center gap-1.5">
                                    <i class="fa-brands fa-readme"></i> Baca
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-24 px-4 bg-white rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-book-open-reader text-4xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Buku Tidak Ditemukan</h3>
                <p class="text-slate-500 text-center max-w-md font-medium">Koleksi yang kamu cari tidak ada. Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                <a href="?" class="mt-6 px-6 py-2.5 rounded-full bg-brand-50 hover:bg-brand-100 text-brand-600 font-bold transition-colors">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-200 py-8 text-center text-slate-500 text-sm font-medium">
        <p>&copy; <?= date('Y') ?> E-Library Digital. All rights reserved.</p>
        <p class="mt-1 text-xs text-slate-400 font-semibold tracking-wide">Powered by <span class="text-brand-600">GH-Tech</span></p>
    </footer>

</body>
</html>
