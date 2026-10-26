<?php
require_once 'koneksi.php';

$is_logged_in = isset($_SESSION['siswa_id']) || isset($_SESSION['guru_id']) || isset($_SESSION['role_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'buku';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$doc_url = "";
$absolute_doc_url = "";
$page_title = "";
$doc_ext = "";

if ($type === 'perangkat') {
    $stmt = $core_db->prepare("SELECT nama_berkas, file_path FROM guru_perangkat WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $berkas = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$berkas) {
        echo "<script>alert('Perangkat tidak ditemukan!'); window.history.back();</script>";
        exit;
    }
    $doc_url = "../public/uploads/guru_perangkat/" . $berkas['file_path'];
    $absolute_doc_url = "http://" . $_SERVER['HTTP_HOST'] . "/public/uploads/guru_perangkat/" . $berkas['file_path'];
    $page_title = $berkas['nama_berkas'];
    $doc_ext = strtolower(pathinfo($berkas['file_path'], PATHINFO_EXTENSION));
} else {
    $stmt = $core_db->prepare("SELECT judul, file_pdf FROM buku_bacaan WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$buku) {
        echo "<script>alert('Buku tidak ditemukan!'); window.location.href='buku_bacaan.php';</script>";
        exit;
    }
    $doc_url = "../public/uploads/buku/" . $buku['file_pdf'];
    $absolute_doc_url = "http://" . $_SERVER['HTTP_HOST'] . "/public/uploads/buku/" . $buku['file_pdf'];
    $page_title = $buku['judul'];
    $doc_ext = "pdf";
}

include 'header.php';
?>
<style>
    /* Sembunyikan global app bar bawaan header.php karena nabrak header putih PDF Viewer */
    .mobile-wrapper > div[style*="position: absolute"][style*="z-index: 100"] { display: none !important; }
</style>

<div class="header" style="background: white; border-bottom: 1px solid #e2e8f0; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
    <div style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
        <a href="javascript:window.history.back();" style="color: #475569; font-size: 1.2rem; padding: 5px; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <img src="<?= htmlspecialchars($favicon_url ?? '/public/uploads/logo/logo_1781854511.png') ?>" style="height: 28px; width: 28px; border-radius: 50%; object-fit: contain; border: 1px solid #e2e8f0; padding: 2px;">
    </div>
    
    <h1 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b; flex: 1; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 15px;">
        <?= htmlspecialchars($page_title) ?>
    </h1>
    
    <?php if ($doc_ext === 'pdf'): ?>
    <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px 10px; border-radius: 8px; display: flex; align-items: center; gap: 5px; flex-shrink: 0;">
        <span id="page-num" style="background: white; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 4px; color: #0f172a;">0</span> / <span id="page-count">0</span>
    </div>
    <?php else: ?>
    <div style="width: 60px;"></div>
    <?php endif; ?>
</div>

<?php if ($doc_ext === 'pdf'): ?>
    <!-- PDF Viewer Container -->
    <div id="pdf-container" style="background: #f1f5f9; min-height: calc(100vh - 65px); display: flex; flex-direction: column; align-items: center; padding: 20px 10px 90px 10px; overflow-x: hidden;">
        
        <div id="loading-indicator" style="text-align: center; padding: 40px; color: #475569;">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; margin-bottom: 10px; color: #3b82f6;"></i>
            <p style="margin: 0; font-weight: 600;">Memuat Dokumen...</p>
        </div>

        <!-- Flipbook Wrapper -->
        <div id="pages-container" style="width: 100%; max-width: 800px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div id="flipbook" style="display: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);"></div>
        </div>
        
        <!-- Navigation Bar (Floating Bottom) -->
        <div id="pdf-nav" style="position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%); background: white; padding: 10px 30px; border-radius: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: none; align-items: center; gap: 20px; z-index: 50; border: 1px solid #f1f5f9;">
            <button id="prev-page" style="background: white; color: #334155; border: 2px solid #cbd5e1; width: 45px; height: 45px; border-radius: 50%; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.2s; outline: none;">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button id="next-page" style="background: white; color: #334155; border: 2px solid #cbd5e1; width: 45px; height: 45px; border-radius: 50%; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.2s; outline: none;">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <style>
        .page { background-color: white; display: flex; justify-content: center; align-items: center; overflow: hidden; border: 1px solid #e2e8f0; }
        .page-content { width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background: #fff; }
        .page-content canvas { max-width: 100%; max-height: 100%; object-fit: contain; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
        const url = '<?= $doc_url ?>';
        const container = document.getElementById('pages-container');
        const flipbookEl = document.getElementById('flipbook');
        const loading = document.getElementById('loading-indicator');
        const navBar = document.getElementById('pdf-nav');
        
        let pdfDoc = null;
        let pageFlip = null;
        let totalPages = 0;
        const renderedPages = new Set();

        const pageNumSpan = document.getElementById('page-num');
        const pageCountSpan = document.getElementById('page-count');
        const btnPrev = document.getElementById('prev-page');
        const btnNext = document.getElementById('next-page');

        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            totalPages = pdfDoc.numPages;
            if(pageCountSpan) pageCountSpan.textContent = totalPages;
            
            // Build DOM for pages
            for(let i = 1; i <= totalPages; i++) {
                const pageDiv = document.createElement('div');
                pageDiv.className = 'page';
                pageDiv.innerHTML = `<div class="page-content"><canvas id="canvas-page-${i}"></canvas></div>`;
                flipbookEl.appendChild(pageDiv);
            }

            loading.style.display = 'none';
            flipbookEl.style.display = 'block';
            navBar.style.display = 'flex';

            // Init StPageFlip
            const cw = container.clientWidth || window.innerWidth;
            const w = Math.min(cw - 20, 450);
            const h = w * 1.414; // A4 ratio

            pageFlip = new St.PageFlip(flipbookEl, {
                width: w, 
                height: h,
                size: "stretch",
                minWidth: 300,
                maxWidth: 600,
                minHeight: 424,
                maxHeight: 850,
                showCover: true,
                maxShadowOpacity: 0.25,
                mobileScrollSupport: false
            });

            pageFlip.loadFromHTML(document.querySelectorAll('.page'));

            // Initial render
            renderPage(1);
            if(totalPages > 1) renderPage(2);
            if(totalPages > 2) renderPage(3);
            
            if(pageNumSpan) pageNumSpan.textContent = 1;
            updateButtons(0);

            pageFlip.on('flip', (e) => {
                const currentPageIndex = e.data; // 0-indexed
                const actualPage = currentPageIndex + 1;
                
                if(pageNumSpan) pageNumSpan.textContent = actualPage;
                updateButtons(currentPageIndex);

                // Render current and adjacent pages lazily
                renderPage(actualPage);
                renderPage(actualPage - 1);
                renderPage(actualPage + 1);
                renderPage(actualPage + 2);
            });
            
        }).catch(function(error) {
            loading.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="font-size: 2.5rem; color: #ef4444; margin-bottom: 10px;"></i><p>Gagal memuat file PDF.</p>';
            console.error('Error loading PDF:', error);
        });

        function renderPage(pageNum) {
            if (pageNum < 1 || pageNum > totalPages || renderedPages.has(pageNum)) return;
            renderedPages.add(pageNum);
            
            pdfDoc.getPage(pageNum).then(function(page) {
                const canvas = document.getElementById(`canvas-page-${pageNum}`);
                if(!canvas) return;
                const ctx = canvas.getContext('2d');
                
                const outputScale = Math.max(window.devicePixelRatio || 1, 2);
                const viewport = page.getViewport({scale: 1.5 * outputScale});
                
                canvas.width = Math.floor(viewport.width);
                canvas.height = Math.floor(viewport.height);

                page.render({ canvasContext: ctx, viewport: viewport });
            });
        }

        btnPrev.addEventListener('click', () => { if(pageFlip) pageFlip.flipPrev(); });
        btnNext.addEventListener('click', () => { if(pageFlip) pageFlip.flipNext(); });

        function updateButtons(currentPageIndex) {
            btnPrev.style.opacity = currentPageIndex <= 0 ? '0.3' : '1';
            btnPrev.style.pointerEvents = currentPageIndex <= 0 ? 'none' : 'auto';
            btnPrev.style.borderColor = currentPageIndex <= 0 ? '#e2e8f0' : '#334155';
            
            // In double page mode, last page might be totalPages - 2 or -1
            const isLast = (currentPageIndex >= totalPages - 1);
            btnNext.style.opacity = isLast ? '0.3' : '1';
            btnNext.style.pointerEvents = isLast ? 'none' : 'auto';
            btnNext.style.borderColor = isLast ? '#e2e8f0' : '#334155';
        }
    </script>
<?php elseif (in_array($doc_ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
    <!-- Google Docs / Office Viewer -->
    <div id="pdf-container" style="background: #cbd5e1; height: calc(100vh - 5.5rem); display: flex; flex-direction: column; align-items: center; overflow: hidden; padding: 0;">
        <iframe src="https://docs.google.com/gview?url=<?= urlencode($absolute_doc_url) ?>&embedded=true" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
<?php elseif (in_array($doc_ext, ['jpg', 'jpeg', 'png'])): ?>
    <!-- Image Viewer -->
    <div id="pdf-container" style="background: #cbd5e1; min-height: calc(100vh - 5.5rem); display: flex; justify-content: center; align-items: flex-start; overflow-y: auto; padding: 20px;">
        <img src="<?= $doc_url ?>" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    </div>
<?php else: ?>
    <div style="padding: 40px; text-align: center; background: #cbd5e1; min-height: calc(100vh - 5.5rem);">
        <i class="fa-solid fa-file-arrow-down" style="font-size: 3rem; color: #64748b; margin-bottom: 15px;"></i>
        <p style="font-weight: bold; color: #334155;">Format dokumen tidak dapat dipratinjau.</p>
        <a href="<?= $doc_url ?>" target="_blank" style="display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; font-weight: bold; text-decoration: none; margin-top: 10px;">Unduh File</a>
    </div>
<?php endif; ?>

<?php // No footer for reading experience ?>
