<?php
use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) ? $institusi['nama'] : 'MAS MIFTAHUL HUDA';
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/' . $institusi['logo'] : Branding::getInstitusiLogo();
$tahun_ajaran = $tahun_ajaran ?? '2026/2027';
$semester = $semester ?? 'Ganjil';
$kamad_nama = $kamad['nama'] ?? 'Drs. Hamim Hasyim';
$kamad_nip = $kamad['nip'] ?? '-';
$waka_nama = $ketua_panitia['nama'] ?? 'M AZIZ SULAIMAN, S.Pd';
$waka_nip = $ketua_panitia['nip'] ?? '1804072704000002';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php 
    $page_title = '3 Program Unggulan'; 
    include __DIR__ . '/../admin/_zamrud_head.php'; 
    ?>
    <style>
        .prog-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: all 0.25s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .prog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .btn-act {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }
        .btn-primary-emerald {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5,150,105,0.25);
        }
        .btn-primary-emerald:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .btn-white {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
        }
        .btn-white:hover {
            background: #f1f5f9;
        }
        .subprog-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            transition: background 0.15s;
        }
        .subprog-item:hover {
            background: #f1f5f9;
        }
        .timeline-table th, .timeline-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.86rem;
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div style="padding: 24px;">

            <!-- TOP NAVIGATION & ACTION BAR -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:0.86rem; color:#64748b; font-weight:600;">
                    <a href="/kurikulum" style="color:#059669; text-decoration:none;">Kurikulum</a>
                    <span>/</span>
                    <span style="color:#0f172a; font-weight:700;">3 Program Unggulan</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <a href="/kurikulum/program-unggulan/cetak" target="_blank" class="btn-act btn-primary-emerald">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Naskah Lengkap (PDF Siap Cetak A4)
                    </a>
                    <a href="/kurikulum" class="btn-act btn-white">
                        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- GRAND HERO BANNER -->
            <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 55%, #059669 100%); color:#fff; border-radius:18px; padding:28px 32px; margin-bottom:28px; box-shadow:0 10px 30px -5px rgba(6,78,59,0.3); border:1px solid rgba(255,255,255,0.15); position:relative; overflow:hidden;">
                <div style="position:absolute; right:-30px; bottom:-30px; opacity:0.08; pointer-events:none;">
                    <i data-lucide="award" style="width:260px; height:260px;"></i>
                </div>
                <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,0.18); padding:5px 14px; border-radius:999px; font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:12px;">
                    <i data-lucide="sparkles" style="width:14px;height:14px;color:#fef08a;"></i> Blueprint Inovasi & Kemandirian Madrasah &bull; TP <?= htmlspecialchars($tahun_ajaran) ?>
                </div>
                <h1 style="font-size:1.85rem; font-weight:900; margin:0 0 10px 0; letter-spacing:-0.5px; line-height:1.25;">
                    Tiga Program Unggulan MAS Miftahul Huda
                </h1>
                <p style="margin:0 0 20px 0; font-size:0.95rem; color:rgba(255,255,255,0.92); max-width:850px; line-height:1.6;">
                    Strategi akselerasi mutu madrasah melalui penguatan <strong>Madrasah Berbasis Pesantren</strong> (Penguasaan Imtaq, Tahfidz & Turats Klasik), <strong>Madrasah Berbasis Life Skill</strong> (Vokasi, Wirausaha & Agribisnis Mandiri), serta <strong>Madrasah Berbasis Digital</strong> (Transformasi Sistem 5.0, Koding & Smart School).
                </p>

                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                    <div style="background:rgba(255,255,255,0.12); padding:8px 16px; border-radius:10px; display:flex; align-items:center; gap:10px;">
                        <i data-lucide="shield-check" style="width:18px;height:18px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.7rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Legalitas Dokumen</div>
                            <div style="font-size:0.85rem; font-weight:800;">SK Kamad No. 421.3/088/2026</div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,0.12); padding:8px 16px; border-radius:10px; display:flex; align-items:center; gap:10px;">
                        <i data-lucide="users" style="width:18px;height:18px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.7rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Penanggung Jawab</div>
                            <div style="font-size:0.85rem; font-weight:800;"><?= htmlspecialchars($kamad_nama) ?></div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,0.12); padding:8px 16px; border-radius:10px; display:flex; align-items:center; gap:10px;">
                        <i data-lucide="book-open" style="width:18px;height:18px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.7rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Ketua Kurikulum</div>
                            <div style="font-size:0.85rem; font-weight:800;"><?= htmlspecialchars($waka_nama) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID 3 PILAR PROGRAM UTAMA -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:22px; margin-bottom:30px;">

                <!-- 1. MADRASAH PESANTREN -->
                <div class="prog-card" style="border-top:5px solid #059669;">
                    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                                🕌
                            </div>
                            <span class="badge-pill" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;">
                                Program 1 &bull; Imtaq & Turats
                            </span>
                        </div>
                        <h2 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Pesantren
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Penguatan fondasi religius, penguasaan kitab kuning, tahfidz Al-Qur'an, dan pembiasaan amaliah Ahlussunnah wal Jama'ah.
                        </p>
                    </div>

                    <div style="padding:20px 24px; flex:1;">
                        <div style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                            Pilar Pembelajaran Unggulan:
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="book-open" style="width:16px;height:16px;color:#059669;"></i>
                                Tahfidz & Tahsin Al-Qur'an Terpadu
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Target mutqin minimal 3 Juz (Juz 30, 1, 2) bagi reguler; kelas peminatan tahfidz akselerasi 10-30 juz dengan munaqosyah resmi.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="scroll" style="width:16px;height:16px;color:#059669;"></i>
                                Kajian Turats / Kitab Kuning
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Silabus kitab klasik: <em>Al-Ajurumiyyah</em>, <em>Amtsilah Tashrifiyyah</em>, <em>Safinatun Naja</em>, <em>Fathul Qorib</em>, dan <em>Ta'limul Muta'allim</em>.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="message-square" style="width:16px;height:16px;color:#059669;"></i>
                                Muhadatsah 3 Bahasa & Retorika Dakwah
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Penguasaan Bahasa Arab & Inggris aktif, pembinaan khutbah, kultum santri, dan debat keislaman.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="heart" style="width:16px;height:16px;color:#059669;"></i>
                                Pembiasaan Ubudiyah & Akhlakul Karimah
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Qiyamul lail, sholat dhuha harian, pembacaan ratib, dzikir pagi-petang, serta pendampingan adab santri.
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:#64748b;">
                        <span>Kordinator: <strong>Saipudin Aziz, S.Pd</strong></span>
                        <span style="font-weight:700; color:#059669;">Target: 100% Tuntas</span>
                    </div>
                </div>

                <!-- 2. MADRASAH LIFE SKILL -->
                <div class="prog-card" style="border-top:5px solid #d97706;">
                    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                                🛠️
                            </div>
                            <span class="badge-pill" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a;">
                                Program 2 &bull; Vokasi & Mandiri
                            </span>
                        </div>
                        <h2 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Life Skill
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Pemberdayaan keterampilan vokasional, wirausaha kreatif, dan keahlian teknis agar lulusan mandiri finansial dan siap kerja.
                        </p>
                    </div>

                    <div style="padding:20px 24px; flex:1;">
                        <div style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                            5 Peminatan Keahlian Terapan:
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="sprout" style="width:16px;height:16px;color:#d97706;"></i>
                                Agribisnis Terpadu & Greenhouse
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Budidaya sayuran hidroponik, perikanan air tawar (bioflok nila/lele), dan produksi pupuk organik ramah lingkungan.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="palette" style="width:16px;height:16px;color:#d97706;"></i>
                                Desain Grafis & Digital Marketing UMKM
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Pembuatan desain kemasan, poster promosi, fotografi produk, copywriting, dan pengelolaan etalase marketplace online.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="utensils" style="width:16px;height:16px;color:#d97706;"></i>
                                Tata Boga & Olahan Pangan Halal Santri
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Produksi bakery/roti madrasah, kuliner nusantara, pengemasan kedap udara, dan edukasi sertifikasi halal LPPOM MUI.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="cpu" style="width:16px;height:16px;color:#d97706;"></i>
                                Teknisi Komputer & Elektronika Terapan
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Perakitan PC, perbaikan hardware/software ringan, instalasi jaringan internet LAN/Wi-Fi, dan instalasi listrik dasar.
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:#64748b;">
                        <span>Kordinator: <strong>Indah Yunitasari, S.Pd</strong></span>
                        <span style="font-weight:700; color:#d97706;">Expo Produk Santri</span>
                    </div>
                </div>

                <!-- 3. MADRASAH DIGITAL -->
                <div class="prog-card" style="border-top:5px solid #2563eb;">
                    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                                💻
                            </div>
                            <span class="badge-pill" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                                Program 3 &bull; Smart Tech & AI 5.0
                            </span>
                        </div>
                        <h2 style="font-size:1.25rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Digital
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Implementasi ekosistem pendidikan modern cerdas 5.0, paperless administration, literasi koding & pemanfaatan AI secara beretika.
                        </p>
                    </div>

                    <div style="padding:20px 24px; flex:1;">
                        <div style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                            Inovasi Transformasi Digital:
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="layers" style="width:16px;height:16px;color:#2563eb;"></i>
                                MASMIFHDA SYSTEM Terpadu
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Integrasi Portal Guru, Portal Siswa, Presensi Scanner QR/Face, Jurnal Mengajar Real-time, E-Kinerja, dan E-Rapor Digital.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="code-2" style="width:16px;height:16px;color:#2563eb;"></i>
                                Kurikulum Koding & AI Literacy Siswa
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Mapel Koding (Computational Thinking, HTML/CSS, Python dasar) dan pengenalan Artificial Intelligence secara bijak.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="check-circle-2" style="width:16px;height:16px;color:#2563eb;"></i>
                                CBT & Assessment 100% Paperless
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Pelaksanaan STS, PAS, dan PAT digital tanpa kertas, acak butir soal otomatis, sistem anti-curang, dan koreksi instan.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="library" style="width:16px;height:16px;color:#2563eb;"></i>
                                E-Library & Repositori Turats Digital
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Perpustakaan digital terintegrasi e-book resmi madrasah, kitab kuning digital, dan modul ajar interaktif multi-perangkat.
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:#64748b;">
                        <span>Kordinator: <strong>Syaiful Bahri, S.Pd</strong></span>
                        <span style="font-weight:700; color:#2563eb;">100% Paperless</span>
                    </div>
                </div>

            </div>

            <!-- ROADMAP TIMELINE & JADWAL PELAKSANAAN -->
            <div style="background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:24px 28px; margin-bottom:30px; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                    <div>
                        <h3 style="margin:0 0 4px 0; font-size:1.1rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="calendar" style="width:20px;height:20px;color:#059669;"></i>
                            Matriks Timeline & Rencana Aksi Pelaksanaan (1 Tahun Ajaran)
                        </h3>
                        <p style="margin:0; font-size:0.82rem; color:#64748b;">Jadwal tahapan implementasi, monitoring berkala, dan evaluasi hasil ketiga program unggulan.</p>
                    </div>
                    <span class="badge-pill" style="background:#f1f5f9; color:#475569;">TP 2026/2027</span>
                </div>

                <div style="overflow-x:auto;">
                    <table class="timeline-table" style="width:100%; border-collapse:collapse; text-align:left;">
                        <thead>
                            <tr style="background:#f8fafc; color:#475569; font-weight:700;">
                                <th style="width:50px; text-align:center;">No</th>
                                <th>Tahapan Kegiatan</th>
                                <th>Fokus Program</th>
                                <th>Waktu Pelaksanaan</th>
                                <th>Target Capaian & Output</th>
                                <th>Penanggung Jawab</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">1</td>
                                <td><strong>Sosialisasi & Pemetaan Minat Bakat</strong></td>
                                <td><span class="badge-pill" style="background:#ecfdf5; color:#065f46;">Pesantren & Vokasi</span></td>
                                <td>Juli 2026 (Awal Semester)</td>
                                <td>Data pemetaan hafalan awal santri & pilihan kejuruan life skill siswa kelas X-XII</td>
                                <td>Waka Kesiswaan & Wali Kelas</td>
                            </tr>
                            <tr>
                                <td align="center">2</td>
                                <td><strong>Implementasi Full Digital Smart KBM</strong></td>
                                <td><span class="badge-pill" style="background:#eff6ff; color:#1d4ed8;">Digital Madrasah</span></td>
                                <td>Agustus 2026 s.d. Seterusnya</td>
                                <td>100% presensi scanner, jurnal KBM digital guru, dan modul daring aktif di MASMIFHDA SYSTEM</td>
                                <td>Tim IT & Waka Kurikulum</td>
                            </tr>
                            <tr>
                                <td align="center">3</td>
                                <td><strong>Praktikum Terjadwal & Workshop Vokasi</strong></td>
                                <td><span class="badge-pill" style="background:#fffbeb; color:#92400e;">Life Skill</span></td>
                                <td>Setiap Hari Sabtu</td>
                                <td>Bibit hidroponik tertanam, produk roti batch pertama, portofolio desain promosi siswa</td>
                                <td>Kordinator Divisi Vokasi</td>
                            </tr>
                            <tr>
                                <td align="center">4</td>
                                <td><strong>Munaqosyah Tahfidz Gelombang 1</strong></td>
                                <td><span class="badge-pill" style="background:#ecfdf5; color:#065f46;">Pesantren</span></td>
                                <td>Oktober 2026</td>
                                <td>Uji publik hafalan Juz 30 dan juz berjalan, penerbitan syahadah tahfidz tahap 1</td>
                                <td>Dewan Asatidz Tahfidz</td>
                            </tr>
                            <tr>
                                <td align="center">5</td>
                                <td><strong>Pelaksanaan PAS Paperless CBT Terintegrasi</strong></td>
                                <td><span class="badge-pill" style="background:#eff6ff; color:#1d4ed8;">Digital Madrasah</span></td>
                                <td>Desember 2026</td>
                                <td>Asesmen Sumatif Ganjil 100% digital tanpa kertas, nilai otomatis tersinkron ke E-Rapor</td>
                                <td>Panitia PAS & Tim IT</td>
                            </tr>
                            <tr>
                                <td align="center">6</td>
                                <td><strong>Pameran Karya & Expo Entrepreneur Santri</strong></td>
                                <td><span class="badge-pill" style="background:#fffbeb; color:#92400e;">Life Skill & Pesantren</span></td>
                                <td>Januari 2027</td>
                                <td>Gelar produk panen hidroponik, pameran kriya, bazar kuliner halal, dan pentas pidato 3 bahasa</td>
                                <td>Seluruh Sivitas & OSIM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TIM PENGELOLA & PENGESAHAN DOKUMEN -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:24px 28px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div>
                    <h4 style="margin:0 0 6px 0; font-size:1rem; font-weight:800; color:#0f172a;">
                        Pusat Kendali Mutu & Dewan Pengembang 3 Program Unggulan
                    </h4>
                    <p style="margin:0; font-size:0.83rem; color:#64748b; max-width:650px;">
                        Disusun oleh Tim Pengembang Kurikulum, disahkan oleh Kepala Madrasah, dan didukung penuh oleh Komite Madrasah serta Pengawas Kemenag Kabupaten Lampung Barat.
                    </p>
                </div>
                <div style="display:flex; gap:12px; align-items:center;">
                    <a href="/kurikulum/program-unggulan/cetak" target="_blank" class="btn-act btn-primary-emerald">
                        <i data-lucide="file-text" style="width:16px;height:16px;"></i> Buka Naskah Lengkap PDF
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>