<?php
require_once 'koneksi.php';

// Cek logout
if (isset($_GET['logout'])) {
    if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])) {
        unset($_SESSION['siswa_id'], $_SESSION['siswa_nama'], $_SESSION['siswa_nis'], $_SESSION['siswa_status']);
        unset($_SESSION['guru_id'], $_SESSION['guru_nama'], $_SESSION['guru_nip']);
    } else {
        session_destroy();
    }
    setcookie('apk_remember_siswa', '', time() - 3600, "/");
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Ambil data siswa lengkap
$stmt = $core_db->prepare("SELECT * FROM siswa WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

// Nama Kelas
$nama_kelas = "-";
if (!empty($siswa['kelas_id'])) {
    try {
        $stmt_k = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = :kid");
        $stmt_k->execute(['kid' => $siswa['kelas_id']]);
        $k = $stmt_k->fetch();
        if ($k) $nama_kelas = $k['nama_kelas'];
    } catch(PDOException $e){}
}

$page_title = 'Profil Siswa';
include 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="header">
    <div class="header-top">
        <div class="greeting">
            <p>Informasi Pokok</p>
            <h1>Data Peserta Didik</h1>
        </div>
        <div class="avatar" style="color: var(--primary);">
            <i class="fa-solid fa-address-card"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">
        <!-- Edit Profil Button -->
        <a href="edit_profil.php" style="position: absolute; top: 15px; right: 15px; background: #e0f2fe; color: var(--primary); font-size: 0.75rem; font-weight: 700; padding: 6px 12px; border-radius: 20px; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-pen-to-square"></i> Edit
        </a>
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--text-muted); margin: 0 auto 15px; overflow: hidden; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <?php if(!empty($siswa['foto'])): ?>
                    <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif; ?>
            </div>
            <h2 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 5px;"><?= htmlspecialchars($siswa['nama']) ?></h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">NIS: <?= htmlspecialchars($siswa['nis']) ?> &bull; NISN: <?= htmlspecialchars($siswa['nisn'] ?: '-') ?></p>
        </div>

        <div style="height: 1px; background: #e2e8f0; margin: 15px 0;"></div>

        <div class="data-row">
            <span class="data-label">Kelas</span>
            <span class="data-value"><?= htmlspecialchars($nama_kelas) ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tempat Lahir</span>
            <span class="data-value"><?= htmlspecialchars($siswa['tempat_lahir'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tanggal Lahir</span>
            <span class="data-value">
                <?php
                if (!empty($siswa['tanggal_lahir'])) {
                    $bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $time = strtotime($siswa['tanggal_lahir']);
                    echo htmlspecialchars(date('d', $time) . ' ' . $bulan_indo[(int)date('m', $time)] . ' ' . date('Y', $time));
                } else {
                    echo '-';
                }
                ?>
            </span>
        </div>
        <div class="data-row">
            <span class="data-label">Nama Ayah</span>
            <span class="data-value"><?= htmlspecialchars($siswa['nama_ayah'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Nama Ibu</span>
            <span class="data-value"><?= htmlspecialchars($siswa['nama_ibu'] ?: '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Kontak Ortu</span>
            <span class="data-value"><?= htmlspecialchars($siswa['no_hp_ortu'] ?: '-') ?></span>
        </div>
        
    </div>

    <!-- MENU LAINNYA -->
    <div style="margin-top: 20px;">
        <h4 style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; padding-left: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Lainnya</h4>
        
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            
            <a href="ubah_password.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #fee2e2; color: #ef4444; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Ubah Password</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Ganti kata sandi akun Anda</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>
            
            <a href="kalender.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #e0f2fe; color: #0ea5e9; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Kalender Akademik</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Agenda, jadwal ujian, & hari libur</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>

            <a href="buku_saku.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Buku Saku Siswa</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Tata tertib & panduan madrasah</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>

            <a href="saran.php" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #e2e8f0; text-decoration: none; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #fffbeb; color: #f59e0b; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Kotak Saran / Pengaduan</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Sampaikan masukan Anda ke sekolah</div>
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
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Kirim laporan masalah ke tim IT</div>
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
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Versi 26.2.2 (Terbaru)</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
            </a>
        </div>
    </div>

    <a href="profil.php?logout=1" class="btn-logout" style="margin-top: 15px;" onclick="confirmLogout(event, this.href, 'Keluar dari Aplikasi?');">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar Aplikasi
    </a>
    
    <div style="text-align: center; margin-top: 2rem; padding-bottom: 80px;">
        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%); padding: 10px 20px; border-radius: 30px; border: 1px solid rgba(14, 165, 233, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 15px;">
            <div style="width: 32px; height: 32px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <i class="fa-solid fa-shield-halved" style="color: #0ea5e9; font-size: 1rem;"></i>
            </div>
            <div style="text-align: left;">
                <p style="font-size: 0.8rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.5px;">Sistem Informasi Terpadu</p>
                <p style="font-size: 0.65rem; color: #64748b; margin: 0; font-weight: 600; letter-spacing: 0.5px; margin-top: 2px;">AULA SYSTEM &bull; v26.2.2</p>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <span style="font-size: 0.6rem; color: #94a3b8; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Powered By</span>
            <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                <img src="<?= htmlspecialchars($favicon_url) ?>" style="height: 24px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
                <i class="fa-solid fa-xmark" style="font-size: 0.7rem; color: #cbd5e1;"></i>
                <img src="/public/uploads/logo/ghtechlogokecil.png" style="height: 24px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
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
                <h4 style="font-size:0.9rem; color:#10b981; margin-bottom:5px;"><i class="fa-solid fa-star"></i> Fitur Aplikasi untuk Siswa & Wali</h4>
                <ul style="font-size:0.8rem; color:#475569; padding-left:20px; margin-bottom:15px; line-height:1.5;">
                    <li><b>Akademik:</b> Pantau Nilai Harian, Nilai Ujian, dan e-Raport</li>
                    <li><b>Kehadiran & Sikap:</b> Riwayat Kehadiran Kelas, Catatan Jurnal Wali Kelas</li>
                    <li><b>Keuangan:</b> Pantau Saldo Tabungan, Riwayat Tagihan SPP & Komite</li>
                    <li><b>Komunikasi:</b> Notifikasi Realtime dari Sekolah, Kotak Pengaduan</li>
                    <li><b>Administrasi:</b> Permohonan Surat Aktif Belajar, Buku Saku Digital</li>
                </ul>
                <h4 style="font-size:0.9rem; color:#3b82f6; margin-bottom:5px;"><i class="fa-solid fa-clock-rotate-left"></i> Yang Baru di v26.2.2</h4>
                <ul style="font-size:0.8rem; color:#475569; padding-left:20px; line-height:1.5;">
                    <li>🔥 Integrasi penuh <b>Sistem Audit Logs (CCTV)</b> untuk keamanan akun Anda.</li>
                    <li>⚡ Peningkatan performa loading aplikasi dengan fitur lazyload.</li>
                    <li>🔐 Peningkatan proteksi keamanan sistem secara menyeluruh.</li>
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

<?php include 'footer.php'; ?>
