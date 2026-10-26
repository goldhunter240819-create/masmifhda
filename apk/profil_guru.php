<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Ambil data guru lengkap
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['user_id']) && isset($_SESSION['guru_id'])) {
    try {
        $stmt_g = $core_db->prepare("SELECT user_id FROM guru WHERE id = ?");
        $stmt_g->execute([$_SESSION['guru_id']]);
        $uid_rec = $stmt_g->fetchColumn();
        if ($uid_rec) $_SESSION['user_id'] = $uid_rec;
    } catch(Exception $e) {}
}

$show_aplikasi_menu = false;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_cek = $core_db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code IN ('tabungan', 'keuangan', 'absensi')");
        $stmt_cek->execute([$_SESSION['user_id']]);
        if ($stmt_cek->fetch()) $show_aplikasi_menu = true;
    } catch(Exception $e) {}

    if (!$show_aplikasi_menu) {
        try {
            $cek_kta = $core_db->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = ?");
            $cek_kta->execute([$_SESSION['user_id']]);
            if ($cek_kta->fetch()) $show_aplikasi_menu = true;
        } catch(Exception $e) {}
    }

    if (!$show_aplikasi_menu) {
        try {
            $cek_bendahara = $core_db->prepare("SELECT ka.id FROM keuangan_akses ka JOIN guru g ON ka.guru_id = g.id WHERE g.user_id = ? OR ka.guru_id = ?");
            $cek_bendahara->execute([$_SESSION['user_id'], $_SESSION['guru_id'] ?? 0]);
            if ($cek_bendahara->fetch()) $show_aplikasi_menu = true;
        } catch(Exception $e) {}
    }
}

$is_wali_kelas = false;
try {
    $stmt_wk = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
    $stmt_wk->execute(['gid' => $guru_id]);
    if ($stmt_wk->fetch()) {
        $is_wali_kelas = true;
    }
} catch(Exception $e) {}

$is_pembina_ekstra = false;
try {
    $stmt_pe = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Pembina Ekstrakurikuler' LIMIT 1");
    $stmt_pe->execute(['gid' => $guru_id]);
    if ($stmt_pe->fetch()) {
        $is_pembina_ekstra = true;
    }
} catch(Exception $e) {}

$page_title = 'Profil Guru';
include 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Informasi Pokok</p>
            <h1>Data Kepegawaian</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9;">
            <i class="fa-solid fa-address-card"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">
        <a href="lihat_profil_guru.php" style="position: absolute; top: 15px; right: 15px; background: #0ea5e9; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 8px rgba(14, 165, 233, 0.4);">
            <i class="fa-solid fa-eye" style="font-size: 0.9rem;"></i>
        </a>
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 80px; height: 80px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #0ea5e9; margin: 0 auto 15px; overflow: hidden; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <?php if(!empty($guru['foto'])): ?>
                    <img src="/public/uploads/guru/<?= htmlspecialchars($guru['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-chalkboard-user\'></i>';">
                <?php else: ?>
                    <i class="fa-solid fa-chalkboard-user"></i>
                <?php endif; ?>
            </div>
            <h2 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 5px;"><?= htmlspecialchars($guru['nama']) ?></h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= htmlspecialchars($guru['jabatan']) ?></div>
        </div>



    <!-- PENGATURAN & MENU LAINNYA -->
    <div style="margin-top: 20px;">
        <h4 style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; padding-left: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Lainnya</h4>
        
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            
            <?php if ($is_wali_kelas): ?>
            <a href="chat_list_guru.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <i class="fa-solid fa-comment-dots"></i>
                        <div class="fab-badge chat-badge-indicator" style="display: none; position: absolute; top: -5px; right: -5px; font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; border: 1px solid white;">0</div>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Pesan Masuk</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>
            <?php endif; ?>



            <a href="kalender.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #e0f2fe; color: #0ea5e9; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Kalender Akademik</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>

            <a href="buku_saku.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #fdf4ff; color: #d946ef; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Buku Saku Guru</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>

            <a href="it_support.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Bantuan & IT Support</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>

            <a href="#" onclick="openTentang(); return false;" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #fef3c7; color: #f59e0b; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Tentang Aplikasi</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>
            
        </div>
    </div>

    <!-- Tombol Logout Khusus -->
    <a href="logout.php" class="btn-logout" style="margin-top: 15px;" onclick="confirmLogout(event, this.href, 'Keluar Aplikasi?');">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar Aplikasi
    </a>
    
    <div style="text-align: center; margin-top: 2rem; padding-bottom: 80px;">
        <div style="display: inline-flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%); padding: 10px 25px; border-radius: 20px; border: 1px solid rgba(14, 165, 233, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 20px;">
            <p style="font-size: 0.8rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">Sistem Informasi Terpadu</p>
            <p style="font-size: 0.65rem; color: #64748b; margin: 0; font-weight: 600; letter-spacing: 0.5px; margin-top: 3px; text-align: center;">AULA SYSTEM &bull; v26.2.2</p>
        </div>
        
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
            <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Powered By</span>
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                <img src="<?= htmlspecialchars($favicon_url) ?>" style="height: 32px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
                <i class="fa-solid fa-xmark" style="font-size: 0.8rem; color: #cbd5e1;"></i>
                <img src="/public/uploads/logo/ghtechlogokecil.png" style="height: 32px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
            </div>
        </div>
    </div>
</div>

<script>
function openBantuan() {
    Swal.fire({
        title: 'Pusat Bantuan',
        html: `
            <p style="font-size: 0.95rem; color: #475569; margin-bottom: 20px;">Jika Anda mengalami kendala atau menemukan bug pada aplikasi, silakan hubungi tim IT Support / Admin Sekolah kami via WhatsApp.</p>
            <a href="intent://send/6281938240819#Intent;scheme=smsto;package=com.whatsapp;action=android.intent.action.SENDTO;end" 
               style="display: inline-block; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; font-weight: bold; text-decoration: none; width: 100%; box-sizing: border-box; text-align:center;">
               <i class="fa-brands fa-whatsapp" style="font-size: 1.2rem; margin-right: 8px; vertical-align: middle;"></i> Buka Aplikasi WhatsApp
            </a>
            <div style="margin-top: 15px;">
                <button type="button" class="swal2-cancel swal2-styled" onclick="Swal.close()" style="background-color: #94a3b8; width: 100%; margin: 0;">Tutup</button>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: false,
        backdrop: `rgba(0,0,123,0.4)`
    });
}

function openTentang() {
    Swal.fire({
        title: 'AULA SYSTEM',
        html: `
            <div style="text-align:left; padding-top:10px; max-height: 400px; overflow-y: auto;">
                <div style="text-align:center;">
                    <img src="/public/uploads/logo/logo_1781854511.png" style="width:80px; height:80px; margin-bottom:15px; border-radius:20px; box-shadow:0 10px 20px rgba(14,165,233,0.15); border: 2px solid rgba(14,165,233,0.1);">
                    <p style="font-size:1.15rem; font-weight:800; margin:0; color:#0f172a; text-transform:uppercase;">Sistem Informasi Terpadu</p>
                    <p style="font-size:0.85rem; color:#0ea5e9; margin-top:5px; font-weight:600; letter-spacing:0.5px;">Versi 26.2.2 (Build 2026)</p>
                </div>
                <hr style="border:0; border-top:1px dashed #cbd5e1; margin:20px 0;">
                <h4 style="font-size:0.9rem; color:#10b981; margin-bottom:5px;"><i class="fa-solid fa-star"></i> Fitur Aplikasi untuk Guru</h4>
                <ul style="font-size:0.8rem; color:#475569; padding-left:20px; margin-bottom:15px; line-height:1.5;">
                    <li><b>Akademik:</b> Input Nilai Harian & Ujian, e-Raport, Jurnal Mengajar</li>
                    <li><b>Kehadiran:</b> Rekap Absensi Siswa per Mapel, Absen Keliling (Piket)</li>
                    <li><b>Wali Kelas:</b> Chat dengan Wali Murid, Pantau Sikap Siswa</li>
                    <li><b>Administrasi:</b> Arsip Digital (Upload Sertifikat/Ijazah), Kalender Akademik, Buku Saku</li>
                    <li><b>Delegasi:</b> Kasir Tabungan, Penerimaan Pembayaran Tagihan</li>
                </ul>
                <h4 style="font-size:0.9rem; color:#3b82f6; margin-bottom:5px;"><i class="fa-solid fa-clock-rotate-left"></i> Yang Baru di v26.2.2</h4>
                <ul style="font-size:0.8rem; color:#475569; padding-left:20px; line-height:1.5;">
                    <li>🔥 Integrasi penuh <b>Sistem Audit Logs (CCTV)</b> untuk keamanan aplikasi.</li>
                    <li>⚡ Peningkatan performa loading gambar dengan fitur lazyload.</li>
                    <li>🔐 Peningkatan proteksi keamanan halaman admin & anti spam.</li>
                </ul>
                <div style="text-align:center; margin-top:20px; font-size:0.75rem; color:#94a3b8; font-weight:600;">
                    &copy; 2026 Aula System. All rights reserved.
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonColor: '#0ea5e9',
        confirmButtonText: 'Tutup',
        backdrop: `rgba(0,0,123,0.4)`
    });
}
</script>

<?php include 'footer_guru.php'; ?>
