<?php
// Master Koleksi Bukti Fisik Digital PKKM 2026 (7 Bukti Siap Pakai)
$daftarBuktiDigital = [
    [
        'id' => 'visi-misi',
        'nama' => 'Display Visi, Misi & Sasaran Mutu Madrasah',
        'indikator' => 'Indikator 1.2.1 & 2.1.2',
        'komponen' => 'Komponen 1 & 2',
        'icon' => 'award',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/visi-misi',
        'deskripsi' => 'Plakat plang kelembagaan zamrud-emas, Visi resmi, 5 butir Misi & 4 pilar mutu lulusan SKL.'
    ],
    [
        'id' => 'jadwal-kegiatan',
        'nama' => 'Matriks Kalender Akademik & Jadwal Kerja',
        'indikator' => 'Indikator 1.1.1, 1.4.2, 2.1.1',
        'komponen' => 'Komponen 1 & 2',
        'icon' => 'calendar',
        'color' => '#2563eb',
        'bg' => '#eff6ff',
        'url' => '/admin/pkkm/cetak-bukti/jadwal-kegiatan',
        'deskripsi' => 'Timeline 12 bulan (Juli-Juni) sinkron SIAKAD mengacu SK Dirjen Pendis No. 4860 & KMA 450.'
    ],
    [
        'id' => 'inventaris-tik',
        'nama' => 'Kartu Inventaris Sarana TIK & Lab Komputer',
        'indikator' => 'Indikator 1.4.3 & 2.5.1',
        'komponen' => 'Komponen 1 & 2',
        'icon' => 'monitor',
        'color' => '#0891b2',
        'bg' => '#ecfeff',
        'url' => '/admin/pkkm/cetak-bukti/inventaris-tik',
        'deskripsi' => 'Daftar 32 unit TIK siap pakai (Server, PC Lab ANBK, Laptop, Proyektor, Gigabit Switch & UPS).'
    ],
    [
        'id' => 'struktur-organisasi',
        'nama' => 'Bagan Struktur Organisasi & Tupoksi Madrasah',
        'indikator' => 'Indikator 1.1.1 & 1.1.2',
        'komponen' => 'Komponen 1',
        'icon' => 'network',
        'color' => '#7c3aed',
        'bg' => '#f5f3ff',
        'url' => '/admin/pkkm/cetak-bukti/struktur-organisasi',
        'deskripsi' => 'Diagram pohon organisasi visual murni CSS (Yayasan, Komite, Kamad, TU, 4 Waka, Guru) & Tupoksi.'
    ],
    [
        'id' => 'absensi-elektronik',
        'nama' => 'Rekap Presensi Digital Dewan Guru',
        'indikator' => 'Indikator 1.5.1',
        'komponen' => 'Komponen 1',
        'icon' => 'user-check',
        'color' => '#16a34a',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/absensi-elektronik',
        'deskripsi' => 'KPI tingkat kehadiran 98.4%, tabel absensi digital per guru & stempel server GH-SSS.'
    ],
    [
        'id' => 'website-email',
        'nama' => 'Website & Email Resmi Madrasah',
        'indikator' => 'Indikator 1.4.3 & 1.6.1',
        'komponen' => 'Komponen 1',
        'icon' => 'globe',
        'color' => '#ea580c',
        'bg' => '#fff7ed',
        'url' => '/admin/pkkm/cetak-bukti/website-email',
        'deskripsi' => 'Kop resmi + pernyataan + mockup browser website mismifhda.sch.id & webmail dinas resmi.'
    ],
    [
        'id' => 'perpustakaan',
        'nama' => 'Profil & Rekap Layanan Perpustakaan / Pojok Baca',
        'indikator' => 'Indikator 2.2.2 & 2.5.2',
        'komponen' => 'Komponen 2',
        'icon' => 'book',
        'color' => '#d97706',
        'bg' => '#fffbeb',
        'url' => '/admin/pkkm/cetak-bukti/perpustakaan',
        'deskripsi' => 'Statistik 1.565 eksemplar buku DDC, rasio buku, inovasi pojok baca kelas & tren kunjungan.'
    ],
    [
        'id' => 'lingkungan-sehat',
        'nama' => 'Portofolio Lingkungan Sehat, Asri & Ramah Anak',
        'indikator' => 'Indikator 2.3.2 & 2.5.1',
        'komponen' => 'Komponen 2',
        'icon' => 'leaf',
        'color' => '#16a34a',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/lingkungan-sehat',
        'deskripsi' => 'Galeri 6 kuadran: pagar keamanan, taman rindang, wastafel air mengalir, ventilasi kelas & checklist sanitasi.'
    ],
    [
        'id' => 'supervisi-guru',
        'nama' => 'Scorecard Supervisi Guru & Tindak Lanjut',
        'indikator' => 'Indikator 4.1, 4.2 & 4.3',
        'komponen' => 'Komponen 4',
        'icon' => 'clipboard-check',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/supervisi-guru',
        'deskripsi' => 'Matriks penilaian 10 guru (modul ajar, KBM, TIK, asesmen), rata-rata 92.6 (Amat Baik) & tindak lanjut.'
    ],
    [
        'id' => 'ekstrakurikuler',
        'nama' => 'Program & Rekapitulasi Ekstrakurikuler Minat Bakat',
        'indikator' => 'Indikator 2.6.2',
        'komponen' => 'Komponen 2',
        'icon' => 'activity',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/ekstrakurikuler',
        'deskripsi' => 'Matriks 6 cabang ekskul (Pramuka, Tahfidz, Hadroh, Silat, Futsal, UKS), jadwal rutin & 3 pilar pembinaan.'
    ],
    [
        'id' => 'prestasi-siswa',
        'nama' => 'Rekapitulasi Prestasi & Piagam Kejuaraan Siswa',
        'indikator' => 'Indikator 2.3.3',
        'komponen' => 'Komponen 2',
        'icon' => 'award',
        'color' => '#d97706',
        'bg' => '#fffbeb',
        'url' => '/admin/pkkm/cetak-bukti/prestasi-siswa',
        'deskripsi' => 'Rekapitulasi 14 raihan juara (KSM, MTQ, Hadroh, Silat, Pramuka), nomor piagam resmi & 4 highlight piala.'
    ],
    [
        'id' => 'kewirausahaan',
        'nama' => 'Unit Usaha Kewirausahaan & Kemitraan Lembaga',
        'indikator' => 'Indikator 3.1 & 3.3',
        'komponen' => 'Komponen 3',
        'icon' => 'shopping-bag',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/kewirausahaan',
        'deskripsi' => 'Profil 3 unit usaha (Koperasi, Kantin Sehat, Hidroponik), omzet Rp 14,8 Jt/bln & 4 MoU kemitraan.'
    ],
    [
        'id' => 'jaringan-internet',
        'nama' => 'Profil Jaringan & Speedtest Internet',
        'indikator' => 'Indikator 1.4.3 & 1.6.1',
        'komponen' => 'Komponen 1',
        'icon' => 'wifi',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/jaringan-internet',
        'deskripsi' => 'Gauge Speedtest 52.4 Mbps, data ISP Indihome Fiber, foto perangkat ONT/Switch & checklist.'
    ],
    [
        'id' => 'perangkat-guru',
        'nama' => 'Display Monitoring Perangkat Guru (RPP, Modul Ajar, Silabus)',
        'indikator' => 'Indikator 1.4.1 & 2.1.3 (Standar Proses)',
        'komponen' => 'Komponen 1 & 2',
        'icon' => 'book-open',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'url' => '/admin/pkkm/cetak-bukti/perangkat-guru',
        'deskripsi' => 'Portal audit & display digital kelengkapan 404 dokumen perangkat pembelajaran (RPP, Modul Ajar, Silabus/ATP, Prota, Promes, KKTP) seluruh dewan guru.'
    ],
];
?>


<!-- Modal Koleksi Bukti Fisik PKKM (Vanilla & Bootstrap Compatible) -->
<div id="modalKoleksiBuktiOverlay" class="koleksi-modal-overlay" style="display: none;">
    <div class="koleksi-modal-dialog">
        <div class="koleksi-modal-content">
            
            <!-- Modal Header -->
            <div class="koleksi-modal-header">
                <div>
                    <div class="koleksi-badge-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        KOLEKSI LINK BUKTI FISIK PKKM
                    </div>
                    <h3 class="koleksi-modal-title">Daftar Tautan Berkas Bukti Fisik (13 Dokumen)</h3>
                    <p class="koleksi-modal-subtitle">Salin tautan atau buka langsung bukti fisik digital 1 lembar A4 untuk dilampirkan ke <strong>Daftar Berkas Master</strong> atau <strong>Instrumen PKKM</strong>.</p>
                </div>
                <button type="button" class="koleksi-close-btn" onclick="closeKoleksiBuktiModal()" title="Tutup (Esc)">&times;</button>
            </div>

            <!-- Quick Search Input -->
            <div class="koleksi-search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="koleksiSearchInput" placeholder="Ketik kata kunci untuk mencari bukti fisik (misal: visi, supervisi, wirausaha, prestasi, ekskul)..." onkeyup="filterKoleksiBukti()">
            </div>

            <!-- Modal Body / Items List -->
            <div class="koleksi-modal-body" id="koleksiItemsList">
                <?php foreach($daftarBuktiDigital as $idx => $bk): ?>
                    <div class="koleksi-item-card" data-keywords="<?php echo strtolower($bk['nama'] . ' ' . $bk['indikator'] . ' ' . $bk['deskripsi']); ?>">
                        <div class="koleksi-item-left">
                            <div class="koleksi-item-icon" style="background: <?php echo $bk['bg']; ?>; color: <?php echo $bk['color']; ?>; border: 1px solid <?php echo $bk['color']; ?>33;">
                                <?php if($bk['icon'] === 'award'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                                <?php elseif($bk['icon'] === 'calendar'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <?php elseif($bk['icon'] === 'monitor'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="2" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                                <?php elseif($bk['icon'] === 'network'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="8" x="8" y="2" rx="1"/><rect width="8" height="8" x="2" y="14" rx="1"/><rect width="8" height="8" x="14" y="14" rx="1"/><path d="M12 10v4"/><path d="M6 14h12"/></svg>
                                <?php elseif($bk['icon'] === 'user-check'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                                <?php elseif($bk['icon'] === 'book'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 2v20"/></svg>
                                <?php elseif($bk['icon'] === 'leaf'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                                <?php elseif($bk['icon'] === 'clipboard-check'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                                <?php elseif($bk['icon'] === 'activity'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                <?php elseif($bk['icon'] === 'shopping-bag'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <?php elseif($bk['icon'] === 'globe'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" x2="12.01" y1="20" y2="20"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="koleksi-item-info">
                                <div class="koleksi-item-top">
                                    <span class="koleksi-tag-komponen"><?php echo $bk['komponen']; ?></span>
                                    <span class="koleksi-tag-ind"><?php echo $bk['indikator']; ?></span>
                                </div>
                                <h4 class="koleksi-item-name"><?php echo $bk['nama']; ?></h4>
                                <div class="koleksi-item-desc"><?php echo $bk['deskripsi']; ?></div>
                            </div>
                        </div>
                        <div class="koleksi-item-actions">
                            <a href="<?php echo $bk['url']; ?>" target="_blank" class="koleksi-btn-view" title="Buka berkas di tab baru">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                                Buka
                            </a>
                            <button type="button" class="koleksi-btn-copy" onclick="copyBuktiUrl('<?php echo $bk['url']; ?>', this)" title="Salin URL lengkap ke clipboard">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                Salin Link
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal Footer -->
            <div class="koleksi-modal-footer">
                <div class="koleksi-footer-tip">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <span>Klik <strong>Salin Link</strong> lalu tempel pada modal Upload (pilih Tipe: <em>Tautan Eksternal</em>).</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <?php 
                    $isGuruPortalModal = (strpos($_SERVER['REQUEST_URI'] ?? '', '/guru/') !== false);
                    $masterUrlModal = $isGuruPortalModal ? '/guru/pkkm/master-berkas' : '/admin/pkkm/master-berkas';
                    ?>
                    <a href="<?php echo $masterUrlModal; ?>" class="koleksi-btn-master" title="Ke Halaman Master Berkas">
                        📁 Buka Master Berkas
                    </a>
                    <button type="button" class="koleksi-btn-close" onclick="closeKoleksiBuktiModal()">Tutup</button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* === CSS STYLING KOLEKSI BUKTI MODAL (ISOLATED) === */
.koleksi-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    animation: koleksiFadeIn 0.2s ease-out;
}
@keyframes koleksiFadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}
.koleksi-modal-dialog {
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}
.koleksi-modal-content {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    border: 1px solid rgba(226, 232, 240, 0.9);
}
.koleksi-modal-header {
    background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0ea5e9 100%);
    color: #ffffff;
    padding: 18px 24px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.koleksi-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2.5px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.koleksi-modal-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 3px 0;
    line-height: 1.3;
}
.koleksi-modal-subtitle {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    line-height: 1.4;
}
.koleksi-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #ffffff;
    font-size: 1.5rem;
    line-height: 1;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.koleksi-close-btn:hover {
    background: rgba(255, 255, 255, 0.35);
}
.koleksi-search-bar {
    padding: 10px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.koleksi-search-bar input {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    transition: all 0.2s;
}
.koleksi-search-bar input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    background: #ffffff;
}
.koleksi-modal-body {
    padding: 14px 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #ffffff;
}
.koleksi-item-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    transition: all 0.2s;
}
.koleksi-item-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}
.koleksi-item-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}
.koleksi-item-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.koleksi-item-info {
    flex: 1;
    min-width: 0;
}
.koleksi-item-top {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
}
.koleksi-tag-komponen {
    font-size: 0.65rem;
    font-weight: 800;
    color: #0369a1;
    background: #e0f2fe;
    padding: 1px 6px;
    border-radius: 4px;
}
.koleksi-tag-ind {
    font-size: 0.65rem;
    font-weight: 700;
    color: #475569;
    background: #e2e8f0;
    padding: 1px 6px;
    border-radius: 4px;
}
.koleksi-item-name {
    font-size: 0.86rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.25;
}
.koleksi-item-desc {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.koleksi-item-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.koleksi-btn-view {
    background: #f1f5f9;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    padding: 6px 11px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}
.koleksi-btn-view:hover {
    background: #e2e8f0;
    color: #0369a1;
}
.koleksi-btn-copy {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    transition: all 0.2s;
}
.koleksi-btn-copy:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.35);
}
.koleksi-modal-footer {
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.koleksi-footer-tip {
    font-size: 0.74rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 220px;
}
.koleksi-btn-master {
    background: #f0f9ff;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}
.koleksi-btn-master:hover {
    background: #e0f2fe;
    color: #0369a1;
}
.koleksi-btn-close {
    background: #e2e8f0;
    color: #475569;
    border: none;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.koleksi-btn-close:hover {
    background: #cbd5e1;
    color: #0f172a;
}
</style>

<script>
function openKoleksiBuktiModal() {
    var overlay = document.getElementById('modalKoleksiBuktiOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
        var searchInp = document.getElementById('koleksiSearchInput');
        if (searchInp) {
            searchInp.value = '';
            filterKoleksiBukti();
            setTimeout(function() { searchInp.focus(); }, 100);
        }
    }
}

function closeKoleksiBuktiModal() {
    var overlay = document.getElementById('modalKoleksiBuktiOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Close on Escape or Click outside dialog
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeKoleksiBuktiModal();
});
document.addEventListener('click', function(e) {
    var overlay = document.getElementById('modalKoleksiBuktiOverlay');
    if (overlay && e.target === overlay) {
        closeKoleksiBuktiModal();
    }
});

function copyBuktiUrl(relativePath, btnEl) {
    var fullUrl = window.location.origin + relativePath;
    
    // Copy to clipboard
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(fullUrl).then(function() {
            showCopySuccess(btnEl);
        }).catch(function() {
            fallbackCopy(fullUrl, btnEl);
        });
    } else {
        fallbackCopy(fullUrl, btnEl);
    }
}

function fallbackCopy(text, btnEl) {
    var tempInp = document.createElement('textarea');
    tempInp.value = text;
    tempInp.style.position = 'fixed';
    tempInp.style.opacity = '0';
    document.body.appendChild(tempInp);
    tempInp.select();
    try {
        document.execCommand('copy');
        showCopySuccess(btnEl);
    } catch(err) {
        alert('Gagal menyalin link: ' + text);
    }
    document.body.removeChild(tempInp);
}

function showCopySuccess(btnEl) {
    var origHtml = btnEl.innerHTML;
    btnEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin! ✓';
    btnEl.style.background = '#0284c7';
    
    setTimeout(function() {
        btnEl.innerHTML = origHtml;
        btnEl.style.background = '';
    }, 2000);
}

function filterKoleksiBukti() {
    var q = (document.getElementById('koleksiSearchInput').value || '').toLowerCase().trim();
    var cards = document.querySelectorAll('#koleksiItemsList .koleksi-item-card');
    
    cards.forEach(function(c) {
        var kw = c.getAttribute('data-keywords') || '';
        if (!q || kw.indexOf(q) !== -1) {
            c.style.display = 'flex';
        } else {
            c.style.display = 'none';
        }
    });
}
</script>
