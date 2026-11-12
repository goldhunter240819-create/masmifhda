<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Dashboard Supervisi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
    <style>
        .supervisi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .sv-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            text-decoration: none !important;
            color: inherit;
        }
        .sv-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            border-color: #cbd5e1;
        }
        .sv-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .sv-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .sv-desc {
            font-size: 0.85rem;
            color: #64748b;
            flex: 1;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }
        .sv-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Variants */
        .v-primary .sv-icon { background: #eff6ff; color: #3b82f6; }
        .v-primary .sv-action { color: #3b82f6; }
        
        .v-success .sv-icon { background: #ecfdf5; color: #10b981; }
        .v-success .sv-action { color: #10b981; }
        
        .v-warning .sv-icon { background: #fffbeb; color: #f59e0b; }
        .v-warning .sv-action { color: #f59e0b; }
        
        .v-purple .sv-icon { background: #f3e8ff; color: #a855f7; }
        .v-purple .sv-action { color: #a855f7; }

        .v-pink .sv-icon { background: #fdf2f8; color: #ec4899; }
        .v-pink .sv-action { color: #ec4899; }
        
        .v-teal .sv-icon { background: #f0fdfa; color: #14b8a6; }
        .v-teal .sv-action { color: #14b8a6; }
        
        .v-indigo .sv-icon { background: #e0e7ff; color: #6366f1; }
        .v-indigo .sv-action { color: #6366f1; }
        
        .v-rose .sv-icon { background: #ffe4e6; color: #f43f5e; }
        .v-rose .sv-action { color: #f43f5e; }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI KOMPREHENSIF</div>
                    <h1><i data-lucide="layout-dashboard"></i> Dashboard Supervisi</h1>
                    <p>Pusat kendali dan manajemen seluruh instrumen supervisi pendidikan di madrasah.</p>
                </div>
                <div class="header-actions-premium">
                    <a href="/kurikulum/supervisi/laporan-hasil" class="e-btn e-btn-outline" style="background:#fff; color:#059669; border-color:#a7f3d0; text-decoration:none; font-weight:700;">
                        <i data-lucide="file-bar-chart-2"></i> Laporan Supervisi
                    </a>
                    <a href="/kurikulum/supervisi/laporan-tindak-lanjut" class="e-btn e-btn-outline" style="background:#fff; color:#2563eb; border-color:#bfdbfe; text-decoration:none; font-weight:700;">
                        <i data-lucide="file-check"></i> Laporan Tindak Lanjut
                    </a>
                    <a href="/kurikulum/supervisi/buku-program" class="e-btn e-btn-outline" style="background:#fff; color:#475569; border-color:#cbd5e1; text-decoration:none;" target="_blank">
                        <i data-lucide="book-open"></i> Cetak Buku Program
                    </a>
                    <a href="/kurikulum/supervisi/rekapitulasi" class="e-btn e-btn-primary" style="background:#8b5cf6; border-color:#8b5cf6; color:#fff; text-decoration:none;">
                        <i data-lucide="pie-chart"></i> Laporan Analytics
                    </a>
                </div>
            </div>
        </div>
        
        <!-- 2 PILAR UTAMA SUPERVISI (PRIORITAS UTAMA) -->
        <div style="margin-top: 1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.85rem;">
                <h3 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin:0; display:flex; align-items:center; gap:8px;">
                    <span style="background:#059669; color:#fff; width:26px; height:26px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem;">⭐</span>
                    2 Pilar Fokus Utama Supervisi Madrasah
                </h3>
                <span style="font-size:0.8rem; color:#64748b; font-weight:600;">Fokus Implementasi Kurikulum Merdeka & PKKM</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(360px, 1fr)); gap:1.25rem;">
                <!-- PILAR 1 -->
                <div style="background:linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%); border-radius:14px; border:2px solid #a7f3d0; padding:1.5rem; box-shadow:0 10px 15px -3px rgba(16,185,129,0.08); display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-15px; right:-15px; width:90px; height:90px; background:#10b981; opacity:0.07; border-radius:50%;"></div>
                    <div>
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; box-shadow:0 4px 10px rgba(16,185,129,0.3);">
                                <i data-lucide="eye"></i>
                            </div>
                            <div>
                                <span style="font-size:0.75rem; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.5px;">PILAR 1 : AKADEMIK</span>
                                <h2 style="font-size:1.2rem; font-weight:800; color:#0f172a; margin:0;">Supervisi Kelas (Observasi Pembelajaran)</h2>
                            </div>
                        </div>
                        <p style="font-size:0.85rem; color:#475569; line-height:1.5; margin-bottom:14px;">
                            Observasi langsung Kegiatan Belajar Mengajar (KBM) guru di ruang kelas dengan <strong>4 Aspek Standar</strong>: Persiapan Mengajar, Kegiatan Pendahuluan, Kegiatan Inti, dan Kegiatan Penutup.
                        </p>
                        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:18px;">
                            <span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">6 Ind. Persiapan</span>
                            <span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">5 Ind. Pendahuluan</span>
                            <span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">11 Ind. Inti KBM</span>
                            <span style="background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">3 Ind. Penutup</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="/kurikulum/supervisi/kelas" class="e-btn e-btn-primary" style="flex:1; justify-content:center; background:#059669; border-color:#059669; color:#fff; text-decoration:none; padding:10px; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="play-circle" style="width:16px;"></i> Mulai / Jadwal Supervisi Kelas
                        </a>
                        <a href="/admin/pkkm/cetak-bukti/supervisi-guru" target="_blank" class="e-btn e-btn-outline" style="background:#fff; color:#059669; border-color:#a7f3d0; text-decoration:none; padding:10px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" title="Buka Rekapitulasi & Bukti PKKM">
                            <i data-lucide="printer" style="width:16px;"></i> Rekap PKKM
                        </a>
                    </div>
                </div>

                <!-- PILAR 2 -->
                <div style="background:linear-gradient(145deg, #ffffff 0%, #eff6ff 100%); border-radius:14px; border:2px solid #bfdbfe; padding:1.5rem; box-shadow:0 10px 15px -3px rgba(59,130,246,0.08); display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-15px; right:-15px; width:90px; height:90px; background:#3b82f6; opacity:0.07; border-radius:50%;"></div>
                    <div>
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; box-shadow:0 4px 10px rgba(37,99,235,0.3);">
                                <i data-lucide="file-check-2"></i>
                            </div>
                            <div>
                                <span style="font-size:0.75rem; font-weight:800; color:#1d4ed8; text-transform:uppercase; letter-spacing:0.5px;">PILAR 2 : TENDIK & ADMINISTRASI</span>
                                <h2 style="font-size:1.2rem; font-weight:800; color:#0f172a; margin:0;">Supervisi Tenaga Pendidik & Tendik</h2>
                            </div>
                        </div>
                        <p style="font-size:0.85rem; color:#475569; line-height:1.5; margin-bottom:14px;">
                            Pemeriksaan kelayakan 13+ dokumen perangkat ajar kurikulum guru (CP, ATP, Modul Ajar, Prota, Promes, KKTP) serta pengawasan layanan administrasi Tata Usaha, Perpustakaan, Sarpras, dan Operator.
                        </p>
                        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:18px;">
                            <span style="background:#dbeafe; color:#1e40af; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">13+ Perangkat Guru</span>
                            <span style="background:#dbeafe; color:#1e40af; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">Tata Usaha / Persuratan</span>
                            <span style="background:#dbeafe; color:#1e40af; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">Perpustakaan</span>
                            <span style="background:#dbeafe; color:#1e40af; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:6px;">Sarpras & Aset</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="/kurikulum/supervisi/administrasi" class="e-btn e-btn-primary" style="flex:1; justify-content:center; background:#2563eb; border-color:#2563eb; color:#fff; text-decoration:none; padding:10px; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="check-square" style="width:16px;"></i> Supervisi Administrasi Pendidik
                        </a>
                        <a href="/kurikulum/supervisi/administrasi" class="e-btn e-btn-outline" style="background:#fff; color:#2563eb; border-color:#bfdbfe; text-decoration:none; padding:10px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="folder-cog" style="width:16px;"></i> Tendik
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- MANAJEMEN PROGRAM & TINDAK LANJUT -->
        <div style="margin-top: 2rem;">
            <h3 style="font-size:1.05rem; font-weight:700; color:#334155; margin-bottom: 0.85rem; display:flex; align-items:center; gap:8px;">
                <i data-lucide="layers" style="width:18px; color:#6366f1;"></i> Perencanaan Program & Tindak Lanjut
            </h3>
            <div class="supervisi-grid" style="margin-top:0;">
                <a href="/kurikulum/supervisi/program" class="sv-card v-primary">
                    <div class="sv-icon"><i data-lucide="calendar-check"></i></div>
                    <div class="sv-title">Program Supervisi Tahunan</div>
                    <div class="sv-desc">Dokumen induk perencanaan jadwal, tujuan, sasaran, dan 2 pilar ruang lingkup supervisi.</div>
                    <div class="sv-action">Buka Program <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/kurikulum/supervisi/tim" class="sv-card v-teal">
                    <div class="sv-icon" style="background:#f0fdf4; color:#047857;"><i data-lucide="user-check"></i></div>
                    <div class="sv-title">Tim Supervisi & SK</div>
                    <div class="sv-desc">Atur susunan dewan observer, nomor SK penetapan, pejabat pengawas, dan cetak naskah SK resmi.</div>
                    <div class="sv-action">Kelola Tim & SK <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/kurikulum/supervisi/tindak-lanjut" class="sv-card v-success">
                    <div class="sv-icon"><i data-lucide="arrow-right-circle"></i></div>
                    <div class="sv-title">Rencana Tindak Lanjut (RTL)</div>
                    <div class="sv-desc">Program pembinaan pasca-supervisi, supervisi klinis, pelatihan KKG, dan monitoring realisasi.</div>
                    <div class="sv-action">Kelola RTL <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/kurikulum/supervisi/laporan-hasil" class="sv-card v-teal">
                    <div class="sv-icon" style="background:#ecfdf5; color:#059669;"><i data-lucide="file-bar-chart-2"></i></div>
                    <div class="sv-title">Laporan Hasil Supervisi</div>
                    <div class="sv-desc">Dokumen laporan resmi: Cover, Pengesahan, Bab I s.d Bab IV, Rekapitulasi Nilai & QR Validasi.</div>
                    <div class="sv-action">Buka Dokumen Laporan <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/kurikulum/supervisi/laporan-tindak-lanjut" class="sv-card v-indigo">
                    <div class="sv-icon" style="background:#eff6ff; color:#2563eb;"><i data-lucide="file-check"></i></div>
                    <div class="sv-title">Laporan Hasil Tindak Lanjut</div>
                    <div class="sv-desc">Dokumen laporan resmi realisasi pembinaan guru, diseminasi Kombel, MOOC PINTAR, dan bukti fisik PKKM.</div>
                    <div class="sv-action">Buka Dokumen RTL <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/kurikulum/supervisi/instrumen" class="sv-card v-purple">
                    <div class="sv-icon"><i data-lucide="clipboard-list"></i></div>
                    <div class="sv-title">Master Instrumen Penilaian</div>
                    <div class="sv-desc">Kelola butir-butir indikator pada 4 kategori standar observasi kelas dan administrasi.</div>
                    <div class="sv-action">Kelola Instrumen <i data-lucide="arrow-right" style="width:14px;"></i></div>
                </a>

                <a href="/admin/pkkm/cetak-bukti/supervisi-guru" target="_blank" class="sv-card v-teal">
                    <div class="sv-icon"><i data-lucide="printer"></i></div>
                    <div class="sv-title">Bukti Fisik PKKM 4.2 & 4.3</div>
                    <div class="sv-desc">Rekapitulasi resmi, lembar kisi-kisi, matriks rekomendasi, dan cetak dokumen RTL untuk pengawas.</div>
                    <div class="sv-action">Buka Lembar PKKM <i data-lucide="external-link" style="width:14px;"></i></div>
                </a>
            </div>
        </div>

        <!-- MODUL TAMBAHAN / PENGEMBANGAN LANJUTAN (OPSIONAL) -->
        <div style="margin-top: 2rem; margin-bottom: 2rem;">
            <details style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 18px; cursor:pointer;">
                <summary style="font-weight:700; color:#64748b; font-size:0.9rem; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="sliders" style="width:16px;"></i> Modul Pengembangan Tambahan / Instrumen Khusus (Opsional)
                    <span style="font-size:0.75rem; background:#e2e8f0; color:#475569; padding:2px 8px; border-radius:10px; margin-left:auto;">Klik untuk menampilkan</span>
                </summary>
                <div class="supervisi-grid" style="margin-top:1.2rem;">
                    <a href="/kurikulum/supervisi/universal/asesmen" class="sv-card v-indigo">
                        <div class="sv-icon"><i data-lucide="check-square"></i></div>
                        <div class="sv-title">Supervisi Asesmen</div>
                        <div class="sv-desc">Pemeriksaan kualitas butir soal, kisi-kisi, dan rubrik asesmen formatif/sumatif.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>

                    <a href="/kurikulum/supervisi/universal/pengelolaan-kelas" class="sv-card v-teal">
                        <div class="sv-icon"><i data-lucide="layout-grid"></i></div>
                        <div class="sv-title">Supervisi Pengelolaan Kelas</div>
                        <div class="sv-desc">Penilaian iklim belajar, kerapian, tata letak, dan dinamika ruang kelas.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>

                    <a href="/kurikulum/supervisi/universal/literasi" class="sv-card v-pink">
                        <div class="sv-icon"><i data-lucide="book-open"></i></div>
                        <div class="sv-title">Supervisi Literasi</div>
                        <div class="sv-desc">Evaluasi pembiasaan membaca dan ketersediaan sudut baca kelas.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>

                    <a href="/kurikulum/supervisi/universal/numerasi" class="sv-card v-primary">
                        <div class="sv-icon"><i data-lucide="calculator"></i></div>
                        <div class="sv-title">Supervisi Numerasi</div>
                        <div class="sv-desc">Evaluasi integrasi logika matematika dan pemecahan masalah di kelas.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>
                    
                    <a href="/kurikulum/supervisi/universal/karakter-dan-kbc" class="sv-card v-rose">
                        <div class="sv-icon"><i data-lucide="heart-handshake"></i></div>
                        <div class="sv-title">Supervisi Karakter & KBC</div>
                        <div class="sv-desc">Monitoring Pendidikan Karakter dan pendekatan Kurikulum Berbasis Cinta.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>
                    
                    <a href="/kurikulum/supervisi/universal/deep-learning" class="sv-card v-purple">
                        <div class="sv-icon"><i data-lucide="brain-circuit"></i></div>
                        <div class="sv-title">Supervisi Deep Learning</div>
                        <div class="sv-desc">Penilaian penerapan metode pembelajaran bermakna dan pertanyaan pemantik.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>

                    <a href="/kurikulum/supervisi/universal/teknologi" class="sv-card v-success">
                        <div class="sv-icon"><i data-lucide="monitor-smartphone"></i></div>
                        <div class="sv-title">Supervisi Teknologi & Digital</div>
                        <div class="sv-desc">Evaluasi pemanfaatan media digital dan proyektor/perangkat TIK di kelas.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>

                    <a href="/kurikulum/supervisi/universal/ekstrakurikuler" class="sv-card v-warning">
                        <div class="sv-icon"><i data-lucide="award"></i></div>
                        <div class="sv-title">Supervisi Ekstrakurikuler</div>
                        <div class="sv-desc">Pemantauan ketercapaian target bakat, minat, dan prestasi ekstrakurikuler.</div>
                        <div class="sv-action">Buka Modul <i data-lucide="arrow-right" style="width:14px;"></i></div>
                    </a>
                </div>
            </details>
        </div>

    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
