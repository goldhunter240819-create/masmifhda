<?php
use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) ? $institusi['nama'] : 'MAS MIFTAHUL HUDA';
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/' . $institusi['logo'] : Branding::getInstitusiLogo();

$cfg = $cfgProg ?? [];
$nomor_sk = $cfg['nomor_sk'] ?? '421.3/088/MAS-MH/SK-PROG/VII/2026';
$tanggal_sk = $cfg['tanggal_sk'] ?? '2026-07-15';
$tahun_ajaran = $cfg['tahun_ajaran'] ?? '2026/2027';
$semester = $cfg['semester'] ?? 'Ganjil';

$kamad_id = $cfg['kamad_id'] ?? null;
$kamad_nama = $cfg['kamad_nama'] ?? 'Drs. Hamim Hasyim';
$kamad_nip = $cfg['kamad_nip'] ?? '-';

$waka_id = $cfg['waka_id'] ?? null;
$waka_nama = $cfg['waka_nama'] ?? 'M AZIZ SULAIMAN, S.Pd';
$waka_nip = $cfg['waka_nip'] ?? '1804072704000002';

$kord_pesantren_id = $cfg['kord_pesantren_id'] ?? null;
$kord_pesantren_nama = $cfg['kord_pesantren_nama'] ?? 'SAIPUDIN AZIZ, S.Pd';

$pembina_rohis_id = $cfg['pembina_rohis_id'] ?? null;
$pembina_rohis_nama = $cfg['pembina_rohis_nama'] ?? 'DIDI HARTONO, S.Pd.I';

$kord_lifeskill_id = $cfg['kord_lifeskill_id'] ?? null;
$kord_lifeskill_nama = $cfg['kord_lifeskill_nama'] ?? 'INDAH YUNITASARI, S.Pd';

$kord_digital_id = $cfg['kord_digital_id'] ?? null;
$kord_digital_nama = $cfg['kord_digital_nama'] ?? 'SYAIFUL BAHRI, S.Pd';

$komite_nama = $cfg['komite_nama'] ?? 'H. AHMAD ROFIQ, S.Ag';
$pengawas_nama = $cfg['pengawas_nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I';
$pengawas_nip = $cfg['pengawas_nip'] ?? '196805141994031002';
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
        .form-control-custom {
            width: 100%;
            padding: 9px 13px;
            font-size: 0.88rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.65);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
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

            <?php if(isset($_SESSION['flash_msg'])): ?>
            <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-size:0.88rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <i data-lucide="check-circle" style="width:18px;height:18px;color:#059669;"></i>
                <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            </div>
            <?php unset($_SESSION['flash_msg']); endif; ?>

            <?php if(isset($_SESSION['flash_err'])): ?>
            <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-size:0.88rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <i data-lucide="alert-circle" style="width:18px;height:18px;color:#ef4444;"></i>
                <?= htmlspecialchars($_SESSION['flash_err']) ?>
            </div>
            <?php unset($_SESSION['flash_err']); endif; ?>

            <!-- GRAND HERO BANNER ZAMRUD (MENYATU ELEGAN) -->
            <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 55%, #065f46 100%); color:#ffffff; border-radius:18px; padding:26px 30px; margin-bottom:26px; box-shadow:0 10px 30px -5px rgba(6,78,59,0.3); border:1px solid rgba(255,255,255,0.15); position:relative; overflow:hidden;">
                
                <div style="position:absolute; right:-25px; bottom:-25px; opacity:0.07; pointer-events:none;">
                    <i data-lucide="award" style="width:240px; height:240px;"></i>
                </div>

                <!-- TOP BAR INSIDE BANNER -->
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px; position:relative; z-index:2;">
                    <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,0.18); padding:5px 14px; border-radius:999px; font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.8px;">
                        <i data-lucide="sparkles" style="width:14px;height:14px;color:#fef08a;"></i> Blueprint Inovasi & Kemandirian Madrasah &bull; TP <?= htmlspecialchars($tahun_ajaran) ?>
                    </div>

                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        <button type="button" onclick="openConfigModal()" class="btn-act" style="background:rgba(255,255,255,0.18); color:#ffffff; border:1px solid rgba(255,255,255,0.3); padding:8px 14px; font-size:0.82rem;">
                            <i data-lucide="settings" style="width:15px;height:15px;color:#fef08a;"></i> Atur Tim & Pengaturan SK
                        </button>
                        <a href="/kurikulum/program-unggulan/cetak" target="_blank" class="btn-act" style="background:#ffffff; color:#065f46; border:none; padding:8px 16px; font-size:0.82rem; box-shadow:0 4px 10px rgba(0,0,0,0.15);">
                            <i data-lucide="printer" style="width:15px;height:15px;color:#059669;"></i> Cetak Naskah PDF Siap Cetak A4
                        </a>
                        <a href="/kurikulum" class="btn-act" style="background:rgba(255,255,255,0.1); color:#ffffff; border:1px solid rgba(255,255,255,0.2); padding:8px 12px; font-size:0.82rem;">
                            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Dashboard
                        </a>
                    </div>
                </div>

                <h1 style="font-size:1.75rem; font-weight:900; margin:0 0 8px 0; letter-spacing:-0.4px; line-height:1.25; position:relative; z-index:2;">
                    Tiga Program Unggulan MAS Miftahul Huda
                </h1>
                <p style="margin:0 0 18px 0; font-size:0.92rem; color:rgba(255,255,255,0.92); max-width:850px; line-height:1.55; position:relative; z-index:2;">
                    Harmonisasi Pendidikan Karakter Qur'ani & Pembinaan Keislaman (<strong>Madrasah Pesantren & ROHIS</strong>), Kemandirian Vokasi Kreatif (<strong>Madrasah Life Skill</strong>), dan Transformasi Cerdas Menuju Era AI 5.0 (<strong>Madrasah Digital</strong>).
                </p>

                <!-- DYNAMIC INFO CHIPS -->
                <div style="display:flex; gap:12px; flex-wrap:wrap; position:relative; z-index:2;">
                    <div style="background:rgba(255,255,255,0.14); padding:6px 14px; border-radius:10px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="shield-check" style="width:16px;height:16px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.68rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Legalitas SK Penetapan</div>
                            <div style="font-size:0.82rem; font-weight:800;"><?= htmlspecialchars($nomor_sk) ?></div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,0.14); padding:6px 14px; border-radius:10px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="user-check" style="width:16px;height:16px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.68rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Penanggung Jawab (Kamad)</div>
                            <div style="font-size:0.82rem; font-weight:800;"><?= htmlspecialchars($kamad_nama) ?></div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,0.14); padding:6px 14px; border-radius:10px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="book-open" style="width:16px;height:16px;color:#a7f3d0;"></i>
                        <div>
                            <div style="font-size:0.68rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Ketua Pelaksana (Waka Kurikulum)</div>
                            <div style="font-size:0.82rem; font-weight:800;"><?= htmlspecialchars($waka_nama) ?></div>
                        </div>
                    </div>
                    <div style="background:rgba(255,255,255,0.14); padding:6px 14px; border-radius:10px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="heart-handshake" style="width:16px;height:16px;color:#fef08a;"></i>
                        <div>
                            <div style="font-size:0.68rem; color:rgba(255,255,255,0.75); text-transform:uppercase; font-weight:700;">Pembina ROHIS Santri</div>
                            <div style="font-size:0.82rem; font-weight:800;"><?= htmlspecialchars($pembina_rohis_nama) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID 3 PILAR PROGRAM UTAMA (RINGKAS & TEPAT SASARAN) -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:22px; margin-bottom:30px;">

                <!-- 1. MADRASAH PESANTREN & ROHIS -->
                <div class="prog-card" style="border-top:5px solid #059669;">
                    <div style="padding:22px 24px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                            <div style="width:48px; height:48px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                                🕌
                            </div>
                            <span class="badge-pill" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;">
                                Program 1 &bull; Imtaq & Rohis
                            </span>
                        </div>
                        <h2 style="font-size:1.22rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Pesantren
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Penguatan fondasi religius, hafalan Al-Qur'an, pembinaan Rohani Islam (ROHIS), dan pembiasaan amaliah Ahlussunnah wal Jama'ah.
                        </p>
                    </div>

                    <div style="padding:20px 24px; flex:1;">
                        <div style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                            Pilar Utama:
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
                                <i data-lucide="sparkles" style="width:16px;height:16px;color:#059669;"></i>
                                Pembinaan & Aktivitas ROHIS (Kerohanian Islam)
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Kajian keislaman berkala santri, mentoring adab, keputrian & keagamaan, peringatan hari besar Islam (PHBI), tadarus bersama, serta kaderisasi dakwah santri teladan.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="heart" style="width:16px;height:16px;color:#059669;"></i>
                                Pembiasaan Ubudiyah & Akhlakul Karimah
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Sholat fardhu berjamaah tepat waktu, sholat dhuha harian, qiyamul lail, pembacaan ratib, dzikir pagi-petang, serta pendampingan adab santri.
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; font-size:0.78rem; color:#64748b; line-height:1.5;">
                        <div>Kordinator: <strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong></div>
                        <div>Pembina ROHIS: <strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong></div>
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
                        <h2 style="font-size:1.22rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Life Skill
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Pemberdayaan keterampilan vokasional, wirausaha kreatif, dan keahlian teknis agar lulusan mandiri finansial dan siap kerja.
                        </p>
                    </div>

                    <div style="padding:20px 24px; flex:1;">
                        <div style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                            Bidang Keahlian Terapan:
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="scissors" style="width:16px;height:16px;color:#d97706;"></i>
                                Menjahit
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Pelatihan teknik dasar memotong kain, pembuatan pola sederhana, serta menjahit pakaian santri, jilbab, gamis, dan seragam madrasah secara mandiri.
                            </div>
                        </div>

                        <div class="subprog-item">
                            <div style="font-weight:700; color:#0f172a; font-size:0.88rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="utensils" style="width:16px;height:16px;color:#d97706;"></i>
                                Tata Boga & Olahan Pangan Halal Santri
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Produksi bakery/roti madrasah, kuliner nusantara, pengemasan kedap udara higienis, dan edukasi sertifikasi halal LPPOM MUI.
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

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; font-size:0.78rem; color:#64748b; line-height:1.5;">
                        <div>Kordinator: <strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong></div>
                        <div style="color:#d97706; font-weight:700;">Gelar Expo Produk & Karya Siswa</div>
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
                        <h2 style="font-size:1.22rem; font-weight:800; color:#0f172a; margin:0 0 6px 0;">
                            Madrasah Berbasis Digital
                        </h2>
                        <p style="font-size:0.84rem; color:#64748b; margin:0; line-height:1.5;">
                            Implementasi ekosistem pendidikan cerdas 5.0, paperless administration, literasi koding & pemanfaatan AI secara beretika.
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
                                E-Library & Repositori Digital
                            </div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:4px;">
                                Perpustakaan digital terintegrasi e-book resmi madrasah, modul ajar interaktif, dan referensi belajar digital.
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; font-size:0.78rem; color:#64748b; line-height:1.5;">
                        <div>Kordinator: <strong><?= htmlspecialchars($kord_digital_nama) ?></strong></div>
                        <div style="color:#2563eb; font-weight:700;">100% Paperless Smart School</div>
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
                    <span class="badge-pill" style="background:#f1f5f9; color:#475569;">TP <?= htmlspecialchars($tahun_ajaran) ?></span>
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
                                <td>Waka Kesiswaan & Pembina ROHIS</td>
                            </tr>
                            <tr>
                                <td align="center">2</td>
                                <td><strong>Implementasi Full Digital Smart KBM</strong></td>
                                <td><span class="badge-pill" style="background:#eff6ff; color:#1d4ed8;">Digital Madrasah</span></td>
                                <td>Agustus 2026 s.d. Seterusnya</td>
                                <td>100% presensi scanner, jurnal KBM digital guru, dan modul daring aktif di MASMIFHDA SYSTEM</td>
                                <td>Kord. Digital & Waka Kurikulum</td>
                            </tr>
                            <tr>
                                <td align="center">3</td>
                                <td><strong>Praktikum Terjadwal & Workshop Vokasi</strong></td>
                                <td><span class="badge-pill" style="background:#fffbeb; color:#92400e;">Life Skill</span></td>
                                <td>Setiap Hari Sabtu</td>
                                <td>Produk roti batch pertama, keterampilan menjahit busana santri, dan instalasi PC lab siswa</td>
                                <td>Kordinator Divisi Vokasi</td>
                            </tr>
                            <tr>
                                <td align="center">4</td>
                                <td><strong>Munaqosyah Tahfidz & Kajian Akbar ROHIS</strong></td>
                                <td><span class="badge-pill" style="background:#ecfdf5; color:#065f46;">Pesantren & ROHIS</span></td>
                                <td>Oktober 2026</td>
                                <td>Uji publik hafalan Juz 30 dan juz berjalan, penerbitan syahadah tahfidz tahap 1 & peringatan PHBI</td>
                                <td>Kord. Pesantren & Pembina ROHIS</td>
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
                                <td><span class="badge-pill" style="background:#fffbeb; color:#92400e;">Life Skill & ROHIS</span></td>
                                <td>Januari 2027</td>
                                <td>Gelar produk karya siswa, busana jahitan santri, bazar kuliner halal, dan pentas dakwah santri</td>
                                <td>Seluruh Sivitas & OSIM/ROHIS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL CONFIG / SETTINGS PROGRAM UNGGULAN -->
<div id="modalConfigProg" class="modal-backdrop">
    <div style="background:#fff; border-radius:16px; max-width:680px; width:92%; padding:26px; box-shadow:0 20px 50px rgba(0,0,0,0.3); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
            <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <i data-lucide="settings" style="width:20px;height:20px;color:#059669;"></i>
                Pengaturan Personel, Pembina & Legalitas SK
            </h3>
            <button onclick="closeConfigModal()" style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:#94a3b8;">&times;</button>
        </div>

        <form action="/kurikulum/program-unggulan/update-config" method="POST">
            
            <!-- SECTION 1: LEGALITAS SK -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:18px;">
                <h4 style="margin:0 0 10px 0; font-size:0.86rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="file-text" style="width:16px;height:16px;color:#2563eb;"></i> Legalitas Surat Keputusan (SK)
                </h4>
                <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:12px; margin-bottom:10px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Nomor SK Penetapan</label>
                        <input type="text" name="nomor_sk" class="form-control-custom" value="<?= htmlspecialchars($nomor_sk) ?>" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Tanggal Penetapan</label>
                        <input type="date" name="tanggal_sk" class="form-control-custom" value="<?= htmlspecialchars($tanggal_sk) ?>" required>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" class="form-control-custom" value="<?= htmlspecialchars($tahun_ajaran) ?>" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Semester</label>
                        <select name="semester" class="form-control-custom">
                            <option value="Ganjil" <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= $semester === 'Genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PIMPINAN & KOORDINATOR -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:18px;">
                <h4 style="margin:0 0 10px 0; font-size:0.86rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="users" style="width:16px;height:16px;color:#059669;"></i> Struktur Pimpinan, Koordinator & Pembina
                </h4>

                <!-- KAMAD & WAKA -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Penanggung Jawab (Kepala Madrasah)</label>
                        <select name="kamad_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'kamad')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($kamad_id && $kamad_id == $dg['id']) || (!$kamad_id && (stripos($dg['nama'], 'hamim') !== false || !empty($dg['is_kamad'])));
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" data-nip="<?= htmlspecialchars($dg['nip'] ?: '-') ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="kamad_nama" id="prog_kamad_nama" value="<?= htmlspecialchars($kamad_nama) ?>">
                        <input type="hidden" name="kamad_nip" id="prog_kamad_nip" value="<?= htmlspecialchars($kamad_nip) ?>">
                    </div>

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Ketua Pelaksana (Waka Kurikulum)</label>
                        <select name="waka_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'waka')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($waka_id && $waka_id == $dg['id']) || (!$waka_id && (stripos($dg['nama'], 'aziz') !== false && stripos($dg['nama'], 'sulaiman') !== false));
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" data-nip="<?= htmlspecialchars($dg['nip'] ?: '-') ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="waka_nama" id="prog_waka_nama" value="<?= htmlspecialchars($waka_nama) ?>">
                        <input type="hidden" name="waka_nip" id="prog_waka_nip" value="<?= htmlspecialchars($waka_nip) ?>">
                    </div>
                </div>

                <!-- KORD PESANTREN & PEMBINA ROHIS -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Kordinator Madrasah Pesantren</label>
                        <select name="kord_pesantren_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'kord_pesantren')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($kord_pesantren_id && $kord_pesantren_id == $dg['id']) || (!$kord_pesantren_id && (stripos($dg['nama'], 'saipudin') !== false || stripos($dg['nama'], 'pesantren') !== false));
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="kord_pesantren_nama" id="prog_kord_pesantren_nama" value="<?= htmlspecialchars($kord_pesantren_nama) ?>">
                    </div>

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Pembina ROHIS Santri</label>
                        <select name="pembina_rohis_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'pembina_rohis')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($pembina_rohis_id && $pembina_rohis_id == $dg['id']) || (!$pembina_rohis_id && stripos($dg['nama'], 'didi') !== false);
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="pembina_rohis_nama" id="prog_pembina_rohis_nama" value="<?= htmlspecialchars($pembina_rohis_nama) ?>">
                    </div>
                </div>

                <!-- KORD LIFE SKILL & KORD DIGITAL -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Kordinator Madrasah Life Skill</label>
                        <select name="kord_lifeskill_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'kord_lifeskill')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($kord_lifeskill_id && $kord_lifeskill_id == $dg['id']) || (!$kord_lifeskill_id && stripos($dg['nama'], 'indah') !== false);
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="kord_lifeskill_nama" id="prog_kord_lifeskill_nama" value="<?= htmlspecialchars($kord_lifeskill_nama) ?>">
                    </div>

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Kordinator Madrasah Digital & IT</label>
                        <select name="kord_digital_id" class="form-control-custom" onchange="autoFillGuruProg(this, 'kord_digital')">
                            <?php foreach($daftarGuru as $dg): 
                                $sel = ($kord_digital_id && $kord_digital_id == $dg['id']) || (!$kord_digital_id && stripos($dg['nama'], 'syaiful') !== false);
                            ?>
                            <option value="<?= $dg['id'] ?>" data-nama="<?= htmlspecialchars($dg['nama']) ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dg['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="kord_digital_nama" id="prog_kord_digital_nama" value="<?= htmlspecialchars($kord_digital_nama) ?>">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: PENGESAH EKSTERNAL -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px;">
                <h4 style="margin:0 0 10px 0; font-size:0.86rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="check-square" style="width:16px;height:16px;color:#d97706;"></i> Lembaga Pengesah / Eksternal
                </h4>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Nama Ketua Komite Madrasah</label>
                        <input type="text" name="komite_nama" class="form-control-custom" value="<?= htmlspecialchars($komite_nama) ?>" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:4px;">Pengawas Pembina Kemenag & NIP</label>
                        <input type="text" name="pengawas_nama" class="form-control-custom" value="<?= htmlspecialchars($pengawas_nama) ?>" placeholder="Nama Pengawas" required style="margin-bottom:6px;">
                        <input type="text" name="pengawas_nip" class="form-control-custom" value="<?= htmlspecialchars($pengawas_nip) ?>" placeholder="NIP Pengawas">
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeConfigModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="background:#059669; color:#fff; border:none; padding:10px 22px; border-radius:8px; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(5,150,105,0.25);">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openConfigModal() {
        document.getElementById('modalConfigProg').style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    function closeConfigModal() {
        document.getElementById('modalConfigProg').style.display = 'none';
    }
    function autoFillGuruProg(sel, key) {
        var opt = sel.options[sel.selectedIndex];
        if (opt) {
            var nama = opt.getAttribute('data-nama') || opt.text;
            var nip = opt.getAttribute('data-nip') || '-';
            var elNama = document.getElementById('prog_' + key + '_nama');
            if (elNama) elNama.value = nama.trim();
            var elNip = document.getElementById('prog_' + key + '_nip');
            if (elNip) elNip.value = nip.trim();
        }
    }
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>