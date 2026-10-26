<?php
$page_title = 'Cetak Laporan BOS';
include __DIR__ . '/_layout_top.php';
?>

<div class="z-panel" style="border-radius: 20px; max-width: 800px; margin: 0 auto;">
    <div class="z-panel-head" style="text-align: center; border-bottom: none; padding-bottom: 0;">
        <i data-lucide="printer" style="width: 48px; height: 48px; color: #0ea5e9; margin-bottom: 15px;"></i>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">Cetak Laporan BOS Nasional</h2>
        <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Pilih jenis format cetak laporan yang akan digunakan untuk LPJ Keuangan Madrasah</p>
    </div>
    
    <div class="z-panel-body" style="padding: 40px;">
        <div style="display: grid; gap: 20px;">
            
            <!-- Format K7 -->
            <a href="#" onclick="alert('Fitur Cetak PDF Format K7 (Buku Kas Umum) sedang disiapkan oleh developer!'); return false;" class="e-btn" style="background: #fff; border: 2px solid #e2e8f0; color: #0f172a; display: flex; align-items: flex-start; text-align: left; padding: 20px; height: auto; justify-content: flex-start;">
                <div style="background: #e0f2fe; color: #0284c7; padding: 15px; border-radius: 12px; margin-right: 20px;">
                    <i data-lucide="file-text" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <div style="font-size: 1.15rem; font-weight: 800; margin-bottom: 5px;">Format K-7 (Buku Kas Umum)</div>
                    <div style="font-size: 0.9rem; color: #64748b; white-space: normal; line-height: 1.4;">Laporan realisasi BKU bulanan/tahunan yang diakui resmi oleh Kemdikbud/Kemenag.</div>
                </div>
            </a>

            <!-- Format K7a -->
            <a href="#" onclick="alert('Fitur Cetak PDF Format K7a (Realisasi RKAS) sedang disiapkan oleh developer!'); return false;" class="e-btn" style="background: #fff; border: 2px solid #e2e8f0; color: #0f172a; display: flex; align-items: flex-start; text-align: left; padding: 20px; height: auto; justify-content: flex-start;">
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-right: 20px;">
                    <i data-lucide="pie-chart" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <div style="font-size: 1.15rem; font-weight: 800; margin-bottom: 5px;">Format K-7a (Realisasi RKAS)</div>
                    <div style="font-size: 0.9rem; color: #64748b; white-space: normal; line-height: 1.4;">Rekapitulasi realisasi penggunaan dana BOS untuk tiap 8 Standar Nasional Pendidikan.</div>
                </div>
            </a>
            
            <!-- Format Buku Pajak -->
            <a href="#" onclick="alert('Fitur Cetak PDF Buku Pembantu Pajak sedang disiapkan oleh developer!'); return false;" class="e-btn" style="background: #fff; border: 2px solid #e2e8f0; color: #0f172a; display: flex; align-items: flex-start; text-align: left; padding: 20px; height: auto; justify-content: flex-start;">
                <div style="background: #fef3c7; color: #b45309; padding: 15px; border-radius: 12px; margin-right: 20px;">
                    <i data-lucide="percent" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <div style="font-size: 1.15rem; font-weight: 800; margin-bottom: 5px;">Buku Pembantu Pajak</div>
                    <div style="font-size: 0.9rem; color: #64748b; white-space: normal; line-height: 1.4;">Buku khusus yang merangkum keseluruhan pemungutan dan penyetoran pajak.</div>
                </div>
            </a>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
