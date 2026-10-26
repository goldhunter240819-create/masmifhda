<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil Semua Tagihan (dikelompokkan per TP)
$tagihan_per_tp = [];
$total_tunggakan = 0;
try {
    $stmt_ta = $core_db->prepare("
        SELECT t.*, ta.name as nama_tahun,
               (SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as jumlah_terbayar
        FROM keuangan_tagihan t 
        LEFT JOIN tahun_ajaran ta ON t.tahun_ajaran_id = ta.id 
        WHERE t.siswa_id = :siswa_id 
        ORDER BY ta.name DESC, t.jatuh_tempo ASC
    ");
    $stmt_ta->execute(['siswa_id' => $siswa_id]);
    $semua_tagihan = $stmt_ta->fetchAll(PDO::FETCH_ASSOC);

    foreach($semua_tagihan as $t) {
        $tp = $t['nama_tahun'] ?: 'Tagihan Lainnya';
        if (!isset($tagihan_per_tp[$tp])) {
            $tagihan_per_tp[$tp] = [];
        }
        $tagihan_per_tp[$tp][] = $t;
        
        if ($t['status'] != 'Lunas') {
            $total_tunggakan += ($t['jumlah_tagihan'] - $t['jumlah_terbayar']);
        }
    }
} catch (PDOException $e) {}

// Ambil Riwayat Pembayaran (maksimal 10 terakhir)
$riwayat_pembayaran = [];
try {
    $stmt_ri = $core_db->prepare("SELECT * FROM keuangan_komite_pembayaran WHERE siswa_id = :siswa_id ORDER BY tanggal_bayar DESC LIMIT 10");
    $stmt_ri->execute(['siswa_id' => $siswa_id]);
    $riwayat_pembayaran = $stmt_ri->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

$page_title = 'Keuangan';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <div class="greeting">
            <p>Rekapitulasi Keuangan</p>
            <h1>Data Pembayaran</h1>
        </div>
        <div class="avatar" style="color: var(--primary);">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div class="data-row">
            <span class="data-label">Total Tunggakan</span>
            <span class="data-value value-red" style="font-size: 1.2rem;">
                <?= formatRupiah($total_tunggakan) ?>
            </span>
        </div>

    </div>

    <?php if (empty($tagihan_per_tp)): ?>
        <div class="data-card" style="margin-top: 1.5rem;">
            <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">Belum ada data tagihan.</div>
        </div>
    <?php else: ?>
        <?php foreach ($tagihan_per_tp as $tp_name => $tagihans): ?>
            <h3 class="section-title" style="margin-top: 1.5rem;">Tahun Pelajaran: <?= htmlspecialchars($tp_name) ?></h3>
            <div class="data-card">
                <?php foreach ($tagihans as $t): 
                    $is_lunas = $t['status'] == 'Lunas';
                    $sisa = $t['jumlah_tagihan'] - $t['jumlah_terbayar'];
                ?>
                    <div class="data-row" style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; margin-bottom: 10px; align-items: flex-start;">
                        <div class="data-label" style="display: flex; flex-direction: column; gap: 4px;">
                            <span style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($t['nama_tagihan']) ?></span>
                            <span class="data-value <?= $is_lunas ? 'value-green' : 'value-orange' ?>" style="font-size: 1.05rem; text-align: left; margin: 0; padding: 0; display: block; width: 100%;">
                                <?= formatRupiah($is_lunas ? $t['jumlah_tagihan'] : $sisa) ?>
                            </span>
                            <small style="color: #94a3b8; font-size: 0.75rem;">Jatuh Tempo: <?= htmlspecialchars($t['jatuh_tempo']) ?></small>
                        </div>
                        <div style="text-align: right; margin-top: 2px;">
                            <?php if($is_lunas): ?>
                                <span class="badge" style="background:#dcfce7; color:#166534; font-size:0.75rem; padding:4px 8px; border-radius:6px; font-weight:600; white-space: nowrap;">Lunas</span>
                            <?php elseif($t['status'] == 'Menyicil' || ($t['jumlah_terbayar'] > 0 && $t['jumlah_terbayar'] < $t['jumlah_tagihan'])): ?>
                                <span class="badge" style="background:#fef3c7; color:#d97706; font-size:0.75rem; padding:4px 8px; border-radius:6px; font-weight:600; white-space: nowrap;">Menyicil</span>
                            <?php else: ?>
                                <span class="badge" style="background:#fee2e2; color:#b91c1c; font-size:0.75rem; padding:4px 8px; border-radius:6px; font-weight:600; white-space: nowrap;">Belum Bayar</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3 class="section-title" style="margin-top: 1.5rem;">10 Riwayat Pembayaran Terakhir</h3>
    <div class="data-card">
        <?php if (count($riwayat_pembayaran) > 0): ?>
            <?php foreach ($riwayat_pembayaran as $r): ?>
                <div class="data-row" style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; margin-bottom: 10px; align-items: flex-start; display: flex; justify-content: space-between;">
                    <div style="display: flex; gap: 12px; width: 100%;">
                        <div style="background: #ecfdf5; color: var(--primary); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; margin-top: 2px;"><i class="fa-solid fa-receipt"></i></div>
                        <div class="data-label" style="display: flex; flex-direction: column; gap: 4px; flex: 1;">
                            <span style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($r['jenis_pembayaran'] ?: 'Pembayaran') ?></span>
                            <span class="value-green" style="font-size: 1.05rem; text-align: left; margin: 0; padding: 0; display: block; width: 100%; font-weight: bold;">
                                <?= formatRupiah($r['jumlah']) ?>
                            </span>
                            <small style="color: #94a3b8; font-size: 0.75rem;">Tanggal: <?= htmlspecialchars($r['tanggal_bayar']) ?></small>
                        </div>
                        <div style="text-align: right; margin-top: 2px; display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                            <span class="badge" style="background:#dcfce7; color:#166534; font-size:0.75rem; padding:4px 8px; border-radius:6px; font-weight:600; white-space: nowrap;">Berhasil</span>
                            <a href="kwitansi.php?id=<?= $r['id'] ?>" style="font-size: 0.75rem; color: #2563eb; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-file-invoice"></i> Kwitansi
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem; padding: 15px;">Belum ada riwayat pembayaran.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Kwitansi -->
<div id="kwitansiModal" class="kwitansi-modal">
    <div class="kwitansi-modal-backdrop" onclick="closeKwitansiModal()"></div>
    <div class="kwitansi-modal-content">
        <div class="kwitansi-modal-header">
            <h3><i class="fa-solid fa-receipt"></i> Kwitansi</h3>
            <div class="kwitansi-modal-controls">
                <button id="btnRotate" onclick="rotateKwitansi()" class="control-btn" title="Putar 90°"><i class="fa-solid fa-rotate"></i></button>
            </div>
            <button class="kwitansi-modal-close" onclick="closeKwitansiModal()">&times;</button>
        </div>
        <div class="kwitansi-modal-body">
            <!-- Loading indicator -->
            <div id="kwitansiLoading" class="kwitansi-loading">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>Memuat Kwitansi...</span>
            </div>
            <iframe id="kwitansiIframe" src="about:blank" frameborder="0"></iframe>
        </div>
        <div class="kwitansi-modal-footer">
            <button id="btnDownloadKwitansi" onclick="downloadFromModal()" class="kwitansi-download-btn">
                <i class="fa-solid fa-download"></i> Download Kwitansi (PNG)
            </button>
        </div>
    </div>
</div>

<style>
/* Modal Kwitansi Styles */
.kwitansi-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
}

.kwitansi-modal.show {
    display: flex;
}

.kwitansi-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    z-index: 10001;
    animation: modalFadeIn 0.25s ease-out;
}

.kwitansi-modal-content {
    position: relative;
    background: #ffffff;
    width: 100%;
    max-width: 440px;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    z-index: 10002;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: modalScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalScaleUp {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.kwitansi-modal-header {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}

.kwitansi-modal-header h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.kwitansi-modal-header h3 i {
    color: #10b981;
}

.kwitansi-modal-close {
    background: #f1f5f9;
    border: none;
    font-size: 1.4rem;
    font-weight: bold;
    color: #64748b;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    line-height: 0;
    padding-bottom: 2px;
}

.kwitansi-modal-close:hover {
    background: #fee2e2;
    color: #ef4444;
}

.kwitansi-modal-body {
    position: relative;
    padding: 10px;
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 220px;
    background: #f8fafc;
}

.kwitansi-loading {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    z-index: 10;
}

.kwitansi-loading i {
    font-size: 1.8rem;
    color: #10b981;
}

.kwitansi-modal-body iframe {
    width: 100%;
    height: 220px;
    border: none;
    background: transparent;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 5;
}

.kwitansi-modal-body iframe.loaded {
    opacity: 1;
}

.kwitansi-modal-footer {
    padding: 12px 20px;
    background: #f0fdf4;
    border-top: 1px solid #dcfce7;
    text-align: center;
}

.kwitansi-modal-footer .tip-text {
    font-size: 0.75rem;
    color: #15803d;
    font-weight: 600;
    line-height: 1.4;
}

/* Modal Controls styling */
.kwitansi-modal-controls {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-right: 12px;
    margin-left: auto;
}

.control-btn {
    background: #f1f5f9;
    border: none;
    font-size: 0.85rem;
    color: #475569;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.control-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.control-btn:active {
    transform: scale(0.92);
}

/* Tombol Download di Footer Modal */
.kwitansi-download-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    cursor: pointer;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 700;
    width: 100%;
    justify-content: center;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    transition: all 0.2s ease;
}
.kwitansi-download-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}
.kwitansi-download-btn:active {
    transform: scale(0.97);
}
.kwitansi-download-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

</style>

<script src="/public/assets/js/html2canvas.min.js"></script>
<script>
let currentZoom = 1.0;
let currentRotation = 0;

function showKwitansiModal(url) {
    const modal = document.getElementById('kwitansiModal');
    const iframe = document.getElementById('kwitansiIframe');
    const loading = document.getElementById('kwitansiLoading');
    
    // Reset state variables
    currentZoom = 1.0;
    currentRotation = 0;
    
    iframe.classList.remove('loaded');
    iframe.style.height = '220px';
    loading.style.display = 'flex';
    iframe.src = url;
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeKwitansiModal() {
    const modal = document.getElementById('kwitansiModal');
    const iframe = document.getElementById('kwitansiIframe');
    
    modal.classList.remove('show');
    iframe.src = 'about:blank';
    document.body.style.overflow = ''; // Restore background scrolling
}

function rotateKwitansi() {
    currentRotation = (currentRotation + 90) % 360;
    applyZoomAndRotation();
}

function applyZoomAndRotation() {
    const iframe = document.getElementById('kwitansiIframe');
    if (!iframe || iframe.src === 'about:blank') return;
    
    try {
        if (iframe.contentWindow && typeof iframe.contentWindow.setZoomAndRotation === 'function') {
            iframe.contentWindow.setZoomAndRotation(currentZoom, currentRotation);
            // Adjust iframe container height in next frame after DOM adjustments
            setTimeout(updateIframeHeight, 50);
        }
    } catch (e) {
        console.error("Error applying zoom/rotate:", e);
    }
}

function updateIframeHeight() {
    const iframe = document.getElementById('kwitansiIframe');
    if (!iframe || iframe.src === 'about:blank') return;
    
    try {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        const container = doc.querySelector('.receipt-container');
        if (container) {
            iframe.style.height = container.offsetHeight + 'px';
        } else {
            iframe.style.height = doc.body.scrollHeight + 'px';
        }
    } catch (e) {
        console.error("Error updating height:", e);
    }
}

// Setup iframe load event to auto-scale height
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('kwitansiIframe');
    const loading = document.getElementById('kwitansiLoading');
    
    iframe.onload = function() {
        if (iframe.src === 'about:blank') return;
        
        try {
            // Wait slightly for child scaling JS to finalize height calculations
            setTimeout(() => {
                // Paksa rotasi 0° di iframe (override default APK rotation)
                try {
                    if (iframe.contentWindow && typeof iframe.contentWindow.setZoomAndRotation === 'function') {
                        iframe.contentWindow.setZoomAndRotation(1, 0);
                    }
                } catch(re) {}
                updateIframeHeight();
                loading.style.display = 'none';
                iframe.classList.add('loaded');
            }, 150);
        } catch (e) {
            console.error("Scale resize error:", e);
            iframe.style.height = '240px'; // Safe fallback
            loading.style.display = 'none';
            iframe.classList.add('loaded');
        }
    };
    
    // Intercept clicks on links pointing to kwitansi.php
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target && target.getAttribute('href') && target.getAttribute('href').startsWith('kwitansi.php')) {
            e.preventDefault();
            // Append modal=1 to prevent action buttons and apk tip box from showing inside the iframe
            const url = target.getAttribute('href') + '&modal=1';
            showKwitansiModal(url);
        }
    });
});

function downloadFromModal() {
    // Pastikan rotasi tetap 0° sebelum capture
    try {
        const iframeWin = document.getElementById('kwitansiIframe').contentWindow;
        if (typeof iframeWin.setZoomAndRotation === 'function') {
            iframeWin.setZoomAndRotation(1, 0);
        }
    } catch(e) {}

    _doDownload();
}

function _doDownload() {
    const iframe = document.getElementById('kwitansiIframe');
    const btn    = document.getElementById('btnDownloadKwitansi');
    if (!iframe || iframe.src === 'about:blank') {
        alert('Kwitansi belum dimuat.');
        return;
    }

    // Ambil payment ID dari URL iframe (kwitansi.php?id=XX&modal=1)
    const iframeSrc = iframe.src || '';
    const idMatch   = iframeSrc.match(/[?&]id=(\d+)/);
    const paymentId = idMatch ? idMatch[1] : '0';

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Sedang Mengunduh...';

    try {
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const target    = iframeDoc.querySelector('.receipt-paper-wrapper');

        if (!target) {
            alert('Gagal menemukan area kwitansi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';
            return;
        }

        // Sembunyikan gambar cross-origin (QR code, logo eksternal) sebelum capture
        // → Mencegah canvas "tainted" yang merusak toDataURL()
        const crossImgs = Array.from(iframeDoc.querySelectorAll('img')).filter(function(img) {
            try {
                const src = img.getAttribute('src') || '';
                return src.startsWith('http') && !src.includes(window.location.hostname);
            } catch(e) { return false; }
        });
        crossImgs.forEach(function(img) { img.style.visibility = 'hidden'; });

        html2canvas(target, {
            scale: 1.5,
            useCORS: true,          // Tanpa allowTaint — agar canvas tidak tainted
            backgroundColor: '#ffffff'
        }).then(function(canvas) {

            // Kembalikan gambar cross-origin setelah capture
            crossImgs.forEach(function(img) { img.style.visibility = ''; });

            const dataUrl = canvas.toDataURL('image/png');

            if (!dataUrl || dataUrl === 'data:,') {
                alert('Gagal membuat gambar kwitansi. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';
                return;
            }

            // POST gambar ke server, server simpan & return token download
            const formData = new FormData();
            formData.append('id',    paymentId);
            formData.append('image', dataUrl);

            fetch('/apk/save_kwitansi.php', {
                method: 'POST',
                body:   formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';

                if (data.status === 'success') {
                    // Content-Disposition: attachment → Android download manager simpan ke Downloads
                    window.location.href = '/apk/download_kwitansi.php?file='
                        + encodeURIComponent(data.filename)
                        + '&token=' + encodeURIComponent(data.token);
                } else {
                    alert('Gagal menyimpan kwitansi: ' + data.message);
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';
                console.error('AJAX Error:', err);
                alert('Terjadi kesalahan jaringan saat menyimpan kwitansi.');
            });

        }).catch(function(err) {
            // Pastikan gambar cross-origin dikembalikan meski gagal
            crossImgs.forEach(function(img) { img.style.visibility = ''; });
            console.error('html2canvas error:', err);
            alert('Gagal membuat gambar kwitansi: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';
        });

    } catch(e) {
        console.error('Error accessing iframe:', e);
        alert('Gagal mengakses kwitansi. Pastikan halaman dimuat dengan benar.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-download"></i> Download Kwitansi (PNG)';
    }
</script>

<?php include 'footer.php'; ?>
