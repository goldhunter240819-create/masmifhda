<?php
// GHTech Analytics Tracking
$app_key = "f7254c5e179f82b9edb4dfd2941ee199";
$action_baca = isset($_GET['stream']) ? "Membaca Buku (Stream)" : "Membaca Buku";
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $app_key . "&ip=" . $_SERVER['REMOTE_ADDR'] . "&action=" . urlencode($action_baca));

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
        
        if (strpos($_SERVER['HTTP_HOST'], 'e-library') !== false) {
            $host = str_replace('e-library', 'apk', $_SERVER['HTTP_HOST']);
            $base = 'https://' . $host; // Force HTTPS untuk WebView APK
        } else {
            $host = $_SERVER['HTTP_HOST'];
            $base = $protocol . '://' . $host . '/masmifhda';
        }
        return $base . '/' . ltrim($path, '/');
    }
}

$db = \App\Core\Database::connect('core');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $db->prepare("SELECT * FROM buku_bacaan WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku || empty($buku['file_pdf'])) {
    die("Buku tidak ditemukan atau file rusak.");
}

// Stream PDF untuk menghindari masalah CORS (Cross-Origin Resource Sharing)
if (isset($_GET['stream']) && $_GET['stream'] == '1') {
    $file_path = __DIR__ . '/../public/uploads/buku/' . $buku['file_pdf'];
    if (file_exists($file_path)) {
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($file_path));
        // Matikan output buffering jika ada
        if (ob_get_level()) ob_end_clean();
        readfile($file_path);
        exit;
    } else {
        http_response_code(404);
        die("File PDF fisik tidak ditemukan di server.");
    }
}

$favicon_sekolah = \App\Core\Branding::getInstitusiFavicon();

// Tambahkan +1 pada data "dilihat" setiap kali halaman pembaca dimuat
$db->prepare("UPDATE buku_bacaan SET dilihat = dilihat + 1 WHERE id = ?")->execute([$id]);

$pdf_url = 'baca?id=' . $id . '&stream=1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Membaca: <?= htmlspecialchars($buku['judul']) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_sekolah) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], }, colors: { brand: { 500: '#14b8a6', 600: '#0d9488' } } } } }
    </script>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <!-- StPageFlip -->
    <script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.js"></script>

    <style>
        body { background-color: #f8fafc; color: #334155; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; margin: 0; padding: 0; }
        
        /* Hide arrows on number input */
        .hide-arrows::-webkit-outer-spin-button,
        .hide-arrows::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .hide-arrows[type=number] { -moz-appearance: textfield; }

        .flip-book { display: none; margin: 0 auto; }
        
        .st-page {
            /* Bulletproof fix untuk kebocoran clip-path di Safari/Chrome */
            -webkit-transform: translateZ(0) !important;
            transform: translateZ(0) !important;
            -webkit-backface-visibility: hidden !important;
            backface-visibility: hidden !important;
            overflow: hidden !important;
        }

        .page { 
            background-color: white; 
            box-shadow: 0 0 15px rgba(0,0,0,0.05), inset 0 0 5px rgba(0,0,0,0.02); 
            display: flex; align-items: center; justify-content: center; 
            position: relative; overflow: hidden; 
            /* Fix for content leaking during flip */
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        /* Bingkai overlay untuk menutupi sisi pinggir canvas yang mungkin bocor/anti-aliasing */
        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid #f1f5f9; 
            border-radius: inherit;
            z-index: 5;
            pointer-events: none;
            mix-blend-mode: multiply;
        }
        
        .page canvas { 
            width: 100%; height: 100%; 
            object-fit: contain; 
            /* Fix for canvas layer ignoring parent clip-path */
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            pointer-events: none;
        }
        
        .page-loading { 
            position: absolute; inset: 0; 
            display: flex; align-items: center; justify-content: center; 
            background: #ffffff; color: #94a3b8; 
            z-index: 10;
        }
        
        .page.--left { border-right: 1px solid rgba(0,0,0,0.05); border-radius: 8px 0 0 8px; }
        .page.--right { border-left: 1px solid rgba(0,0,0,0.05); border-radius: 0 8px 8px 0; }
        .page.--left .page-shadow-inner { position: absolute; right: 0; top: 0; bottom: 0; width: 40px; background: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.08) 100%); pointer-events: none; z-index: 20; }
        .page.--right .page-shadow-inner { position: absolute; left: 0; top: 0; bottom: 0; width: 40px; background: linear-gradient(to left, rgba(0,0,0,0) 0%, rgba(0,0,0,0.08) 100%); pointer-events: none; z-index: 20; }
    </style>
</head>
<body class="antialiased flex flex-col h-screen">

    <!-- Topbar -->
    <div class="h-14 md:h-16 bg-white border-b border-slate-200 flex flex-col justify-center px-4 z-50 shrink-0 shadow-sm relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1 md:gap-2">
                <a href="./" class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors shadow-sm text-slate-500 hover:text-slate-800 border border-slate-200" title="Kembali">
                    <i class="fa-solid fa-arrow-left text-sm md:text-base"></i>
                </a>
                <button id="top-btn-prev" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors border border-transparent hover:border-brand-200">
                    <i class="fa-solid fa-chevron-left"></i> Sebelumnya
                </button>
            </div>
            
            <div class="text-center truncate px-2 flex-1">
                <h1 class="font-bold text-sm md:text-base truncate text-slate-800"><?= htmlspecialchars($buku['judul']) ?></h1>
                <p class="text-[9px] md:text-[10px] text-brand-600 font-bold tracking-widest uppercase hidden md:block">Mode Baca 3D</p>
            </div>
            
            <div class="flex items-center gap-1 md:gap-2">
                <button id="top-btn-next" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors border border-transparent hover:border-brand-200">
                    Selanjutnya <i class="fa-solid fa-chevron-right"></i>
                </button>
                <div class="flex items-center bg-slate-50 rounded-md shadow-inner border border-slate-200 overflow-hidden focus-within:border-brand-400 focus-within:ring-1 focus-within:ring-brand-400 transition-all" title="Lompat ke halaman (Ketik & Enter)">
                    <input type="number" id="page-input" value="1" min="1" class="w-10 md:w-12 text-center text-[11px] md:text-xs font-bold text-slate-700 bg-transparent outline-none py-1.5 hide-arrows">
                    <div class="px-1.5 md:px-2 py-1.5 text-[11px] md:text-xs font-bold text-slate-500 border-l border-slate-200 bg-slate-100">
                        / <span id="total-pages">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Reader Area -->
    <div class="flex-1 relative bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-100 to-slate-300 flex flex-col items-center justify-center p-4 overflow-hidden" id="reader-container">
        
        <!-- Loading State -->
        <div id="loading-state" class="absolute inset-0 flex flex-col items-center justify-center z-40 bg-slate-50/80 backdrop-blur-sm">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-brand-500 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-brand-600 font-bold animate-pulse tracking-wide">Mempersiapkan Lembaran...</p>
        </div>

        <div class="flip-book" id="flip-book">
            <!-- Pages injected here -->
        </div>
    </div>

    <!-- Mobile Controls -->
    <div class="h-16 bg-white border-t border-slate-200 flex items-center justify-center gap-12 px-4 z-50 shrink-0 sm:hidden">
        <button id="btn-prev" class="w-12 h-12 rounded-full flex items-center justify-center bg-slate-50 hover:bg-brand-50 active:scale-95 transition-all text-xl shadow-sm border border-slate-200 hover:border-brand-500 text-slate-600 hover:text-brand-600"><i class="fa-solid fa-chevron-left"></i></button>
        <button id="btn-next" class="w-12 h-12 rounded-full flex items-center justify-center bg-slate-50 hover:bg-brand-50 active:scale-95 transition-all text-xl shadow-sm border border-slate-200 hover:border-brand-500 text-slate-600 hover:text-brand-600"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        const pdfUrl = '<?= $pdf_url ?>';
        let pdfDoc = null;
        let pageFlip = null;
        const renderedPages = new Set();
        const renderScale = window.devicePixelRatio > 1 ? 1.5 : 1.2; // Crisp on retina displays

        async function loadPDF() {
            try {
                pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                document.getElementById('total-pages').textContent = pdfDoc.numPages;
                
                const flipBook = document.getElementById('flip-book');
                
                // 1. Dapatkan dimensi halaman pertama untuk proporsi
                const firstPage = await pdfDoc.getPage(1);
                const viewport = firstPage.getViewport({ scale: 1 });
                const isMobile = window.innerWidth < 768;
                
                // 2. Generate elemen DOM untuk seluruh halaman (kosong, canvas belum di-render)
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'page';
                    
                    const canvas = document.createElement('canvas');
                    canvas.id = `canvas-${i}`;
                    
                    const shadowInner = document.createElement('div');
                    shadowInner.className = 'page-shadow-inner';
                    
                    const loader = document.createElement('div');
                    loader.className = 'page-loading';
                    loader.id = `loader-${i}`;
                    loader.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-2xl"></i>`;
                    
                    pageDiv.appendChild(loader);
                    pageDiv.appendChild(canvas);
                    pageDiv.appendChild(shadowInner);
                    flipBook.appendChild(pageDiv);
                }

                // 3. Inisialisasi StPageFlip
                pageFlip = new St.PageFlip(document.getElementById('flip-book'), {
                    width: viewport.width,
                    height: viewport.height,
                    size: "stretch",
                    minWidth: 250,
                    maxWidth: 1000,
                    minHeight: 350,
                    maxHeight: 1200,
                    maxShadowOpacity: 0.6,
                    showCover: true, // Halaman 1 sebagai cover keras
                    mobileScrollSupport: false, // Nonaktifkan scroll biasa
                    usePortrait: true // Memungkinkan mode 1 halaman di layar kecil
                });

                // Load halaman ke engine 3D
                pageFlip.loadFromHTML(document.querySelectorAll('.page'));

                document.getElementById('loading-state').style.display = 'none';
                document.getElementById('flip-book').style.display = 'block';
                document.getElementById('page-input').value = "1";

                // 4. Lazy Render Logic (Hanya render halaman yg dilihat)
                renderPage(1); // Render Cover
                if (!isMobile) renderPage(2); // Render halaman sebelah cover jika di PC

                pageFlip.on('flip', (e) => {
                    // e.data adalah array halaman yang sedang aktif (bisa 1 atau 2 elemen)
                    // Wait, in page-flip, e.data is the current page index (0-based)
                    const currentPageIndex = e.data;
                    document.getElementById('page-input').value = currentPageIndex + 1;
                    
                    // Render halaman saat ini, sebelumnya, dan selanjutnya agar mulus
                    renderPage(currentPageIndex + 1);
                    renderPage(currentPageIndex + 2);
                    if (currentPageIndex > 0) renderPage(currentPageIndex);
                    if (!isMobile && currentPageIndex + 3 <= pdfDoc.numPages) renderPage(currentPageIndex + 3);
                });

                // Controls
                document.getElementById('btn-prev').addEventListener('click', () => pageFlip.flipPrev());
                document.getElementById('btn-next').addEventListener('click', () => pageFlip.flipNext());
                document.getElementById('top-btn-prev').addEventListener('click', () => pageFlip.flipPrev());
                document.getElementById('top-btn-next').addEventListener('click', () => pageFlip.flipNext());
                
                // Page Input Jump
                const pageInput = document.getElementById('page-input');
                pageInput.addEventListener('change', (e) => {
                    let page = parseInt(e.target.value);
                    const total = pdfDoc.numPages;
                    if (isNaN(page)) page = 1;
                    if (page < 1) page = 1;
                    if (page > total) page = total;
                    e.target.value = page;
                    pageFlip.turnToPage(page - 1);
                });
                
                // Keyboard Support
                document.addEventListener('keydown', (e) => {
                    // Prevent flipping when typing in input
                    if (e.target.tagName.toLowerCase() === 'input') return;
                    
                    if (e.key === 'ArrowLeft') pageFlip.flipPrev();
                    if (e.key === 'ArrowRight') pageFlip.flipNext();
                });

            } catch (error) {
                console.error(error);
                document.getElementById('loading-state').innerHTML = '<p class="text-red-500 font-bold">Gagal memuat dokumen PDF. Pastikan file tidak rusak.</p>';
            }
        }

        async function renderPage(pageNum) {
            if (pageNum > pdfDoc.numPages || pageNum < 1 || renderedPages.has(pageNum)) return;
            
            try {
                // Tandai sedang/sudah di-render agar tidak dobel
                renderedPages.add(pageNum); 
                
                const page = await pdfDoc.getPage(pageNum);
                const canvas = document.getElementById(`canvas-${pageNum}`);
                const ctx = canvas.getContext('2d');
                const loader = document.getElementById(`loader-${pageNum}`);
                
                // Skala resolusi tinggi untuk retina display
                const viewport = page.getViewport({ scale: renderScale });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                await page.render(renderContext).promise;
                
                if(loader) loader.style.display = 'none';
            } catch (e) {
                console.error('Gagal render halaman ' + pageNum, e);
            }
        }

        // Mulai memproses
        loadPDF();
    </script>
</body>
</html>
