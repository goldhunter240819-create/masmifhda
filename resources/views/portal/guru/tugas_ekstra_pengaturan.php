<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e293b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 12px;
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
        }
        .info-card {
            background: linear-gradient(to bottom right, #ffffff, #f8fafc);
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 1.75rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }
        .temp-btn {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .temp-btn:hover {
            background: #8b5cf6;
            border-color: #8b5cf6;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }
        .temp-btn:active {
            transform: translateY(0);
        }
        .btn-save {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }
        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
        }
        .btn-save:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap: wrap; gap: 15px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra'); ?>" class="z-btn-icon" style="background:white; border:1px solid #e2e8f0;"><i data-lucide="arrow-left"></i></a>
                    <div>
                        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;">Pengaturan Program</h2>
                        <p style="margin:0; font-size:0.85rem; color:#64748b;">Ekstrakurikuler: <?php echo htmlspecialchars($nama_ekstra); ?> (<?php echo htmlspecialchars($active_year_name); ?>)</p>
                    </div>
                </div>
                <div>
                    <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/cetak?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" target="_blank" class="z-btn" style="background:#0284c7; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 10px rgba(2,132,199,0.15); transition:0.2s;">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Program Kerja (A4)
                    </a>
                </div>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="background:#f0f9ff; border:1px solid #0ea5e9; color:#0284c7; padding:15px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                    <i data-lucide="check-circle"></i>
                    <div><strong>Berhasil!</strong> Pengaturan program kerja berhasil disimpan.</div>
                </div>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns: 2.2fr 1fr; gap:1.75rem; align-items:start; margin-bottom: 2rem;">
                
                <div class="form-card">
                    <form action="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/pengaturan/save'); ?>" method="POST">
                        <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($nama_ekstra); ?>">
                        
                        <div style="margin-bottom:2rem;">
                            <h3 class="section-title"><i data-lucide="calendar" style="width:20px;height:20px;color:#8b5cf6;"></i> Jadwal Latihan Rutin</h3>
                            <div style="display:flex; gap:20px; flex-wrap: wrap;">
                                <div style="flex:1; min-width:200px;">
                                    <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:8px;">Hari Latihan</label>
                                    <select name="jadwal_hari" class="z-field" style="width:100%; padding:12px; border-radius:10px; border:1px solid #cbd5e1; outline:none; background-color: #fff; font-size:0.9rem;">
                                        <option value="">Pilih Hari</option>
                                        <?php 
                                            $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                                            $selected_day = $program['jadwal_hari'] ?? '';
                                            foreach($days as $d): 
                                        ?>
                                            <option value="<?php echo $d; ?>" <?php echo $d == $selected_day ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="flex:1; min-width:200px;">
                                    <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:8px;">Waktu Latihan</label>
                                    <input type="text" name="jadwal_waktu" value="<?php echo htmlspecialchars($program['jadwal_waktu'] ?? ''); ?>" placeholder="Contoh: 14.00 - 16.00 WIB" class="z-field" style="width:100%; padding:12px; border-radius:10px; border:1px solid #cbd5e1; outline:none; font-size:0.9rem;">
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom:2rem;">
                            <h3 class="section-title"><i data-lucide="target" style="width:20px;height:20px;color:#8b5cf6;"></i> Program & Target Capaian</h3>
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:8px;">Deskripsi Program Kerja & Target Semester Ini</label>
                            <textarea name="target_pencapaian" id="target_pencapaian_field" rows="10" placeholder="Tuliskan program kerja, materi yang akan diajarkan, serta target kompetensi yang ingin dicapai..." style="width:100%; padding:12px; border-radius:10px; border:1px solid #cbd5e1; outline:none; font-family:inherit; resize:vertical; line-height:1.6; font-size:0.9rem; margin-bottom:12px;"><?php echo htmlspecialchars($program['target_pencapaian'] ?? ''); ?></textarea>
                            
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                <span style="font-size: 0.8rem; color: #475569; font-weight: 700; display:inline-flex; align-items:center; gap:5px;"><i data-lucide="layout" style="width:14px;height:14px;color:#64748b;"></i> Muat Rujukan Template:</span>
                                <div style="display:flex; gap:8px; flex-wrap: wrap;">
                                    <button type="button" onclick="loadEkstraTemplate('pramuka')" class="temp-btn"><i data-lucide="file-text" style="width:12px;height:12px;margin-right:4px;vertical-align:middle;"></i>Pramuka</button>
                                    <button type="button" onclick="loadEkstraTemplate('drumband')" class="temp-btn"><i data-lucide="music" style="width:12px;height:12px;margin-right:4px;vertical-align:middle;"></i>Drumband</button>
                                    <button type="button" onclick="loadEkstraTemplate('olahraga')" class="temp-btn"><i data-lucide="trophy" style="width:12px;height:12px;margin-right:4px;vertical-align:middle;"></i>Olahraga</button>
                                </div>
                            </div>
                        </div>

                        <div style="text-align:right; margin-top:2rem;">
                            <button type="submit" class="btn-save"><i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>

                <!-- Kolom Bantuan Info -->
                <div class="info-card">
                    <h4 style="margin:0 0 1rem 0; color:#0f172a; display:flex; align-items:center; gap:8px; font-weight:800; font-size:1.05rem;"><i data-lucide="info" style="color:#3b82f6;"></i> Petunjuk Pengaturan</h4>
                    <p style="font-size:0.85rem; color:#64748b; line-height:1.7; margin:0;">
                        Atur **Hari Latihan** dan **Waktu Latihan** rutin agar siswa dan wali murid dapat melihat jadwal kegiatan melalui portal masing-masing.
                        <br><br>
                        Kolom **Target Pencapaian** sangat penting sebagai acuan program kerja sekaligus tolok ukur evaluasi/nilai rapor akhir ekstrakurikuler siswa.
                        <br><br>
                        Anda dapat memanfaatkan tombol **Rujukan Template** di sebelah kiri untuk mempermudah pengisian cepat yang direkomendasikan.
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const ekstraTemplates = {
        pramuka: `I. PROGRAM KERJA UTAMA SEMESTER:
1. Pengenalan Pramuka & Sejarah Gerakan Pramuka.
2. Latihan Dasar SKU (Syarat Kecakapan Umum) Siaga/Penggalang.
3. Keterampilan Dasar: Tali Temali (Pioneering), Semaphore, Morse, dan Kompas.
4. Latihan Ketangkasan Fisik, Baris-Berbaris (PBB), dan Kedisiplinan.
5. Pertolongan Pertama Gawat Darurat (PPGD) dasar.
6. Kegiatan Outdoor (Penjelajahan/Wide Game) dan Ujian SKU Kenaikan Tingkat.
7. Persiapan dan Pelaksanaan Perkemahan Sabtu-Minggu (Persami).

II. TARGET PENCAPAIAN KOMPETENSI ANGGOTA:
1. Anggota hafal dan memahami nilai Dwi Darma & Dwi Satya (Siaga) / Trisatya & Dasa Darma (Penggalang).
2. Anggota mampu membuat minimal 4 jenis ikatan tali dasar (simpul mati, simpul hidup, simpul pangkal, simpul jangkar).
3. Anggota mampu mengirim dan menerima pesan sandi semaphore/morse sederhana.
4. Anggota memiliki sikap disiplin, gotong royong, mandiri, dan cinta alam.
5. Minimal 80% anggota aktif berhasil menyelesaikan minimal 5 poin SKU semester ini.`,
        drumband: `I. PROGRAM KERJA UTAMA SEMESTER:
1. Pengenalan Alat Musik Drumband (Snare, Bass, Trio/Quarto, Cymbal, Bellyra).
2. Latihan Teknik Ketukan (Rudiment) dan Tempo Dasar.
3. Pembacaan Partitur Sederhana / Pengingat Lagu.
4. Latihan Aransemen Lagu Wajib Nasional dan Lagu Populer Sekolah.
5. Latihan Formasi Baris-Berbaris & Display Drumband.
6. Persiapan Penampilan Upacara dan Event/Lomba.

II. TARGET PENCAPAIAN KOMPETENSI ANGGOTA:
1. Anggota menguasai teknik memegang stik dan memukul instrumen masing-masing dengan benar.
2. Anggota mampu memainkan minimal 3 lagu pilihan secara harmonis bersama tim.
3. Anggota disiplin menjaga tempo dan gerak baris-baris saat memainkan alat musik.
4. Terbentuknya tim drumband sekolah yang solid untuk persiapan parade/upacara besar.`,
        olahraga: `I. PROGRAM KERJA UTAMA SEMESTER:
1. Latihan Fisik: Endurance, Speed, Agility, dan Strength.
2. Latihan Teknik Dasar (Passing, Dribbling, Shooting, Servis, smash).
3. Pemahaman Taktik Bermain (Defending, Attacking, Formasi Permainan).
4. Sesi Uji Coba Internal (Internal Game) dan Evaluasi Kekurangan Tim.
5. Pemahaman Regulasi/Aturan Resmi Pertandingan.
6. Uji Coba Persahabatan dengan Sekolah Lain (Friendly Match).

II. TARGET PENCAPAIAN KOMPETENSI ANGGOTA:
1. Anggota mengalami peningkatan stamina fisik (daya tahan tubuh).
2. Anggota menguasai minimal 3 teknik dasar cabang olahraga terkait dengan baik.
3. Anggota memahami aturan permainan dan sportivitas/fair play.
4. Terpilihnya skuad utama untuk mewakili sekolah pada ajang turnamen/O2SN tingkat kecamatan/kabupaten.`
    };

    function loadEkstraTemplate(key) {
        if (confirm('Apakah Anda ingin memuat template ini? Semua teks program kerja yang sudah Anda ketik saat ini akan ditimpa.')) {
            document.getElementById('target_pencapaian_field').value = ekstraTemplates[key];
        }
    }

    lucide.createIcons();
</script>
</body>
</html>


