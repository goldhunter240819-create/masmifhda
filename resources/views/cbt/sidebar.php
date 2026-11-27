<?php
$active_menu = $active_menu ?? 'dashboard';

$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
$initial = strtoupper(substr($nama, 0, 2));

$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$roleLabel = 'Guru / Pengawas';
if ($roleId === 1 || $roleId === 99) {
    $roleLabel = 'Administrator';
} elseif (!empty($_SESSION['is_kamad'])) {
    $roleLabel = 'Kepala Madrasah';
}

$logoUrl = \App\Core\Branding::getInstitusiLogo();

// Deteksi status open masing-masing dropdown accordion
$isUjianOpen = in_array($active_menu, ['topik', 'jadwal', 'bank_soal', 'token']);
$isRuangOpen = in_array($active_menu, ['ruang', 'denah', 'berita_acara_ruang']);
$isPengawasOpen = in_array($active_menu, ['pengawas_jadwal', 'pengawas_scan', 'pengawas_rekap', 'berita_acara_ruang', 'daftar_hadir_kelas']);
$isAbsenOpen = in_array($active_menu, ['absensi_cbt', 'absensi_rekap']);
$isHasilOpen = in_array($active_menu, ['hasil', 'analisis', 'daftar_hadir_kelas']);
?>
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text">MASMIFHDA</div>
            <div class="logo-sub" style="color:#0ea5e9;">CBT Online</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;">
            <i data-lucide="x"></i>
        </button>
    </div>

    <!-- Nav List -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav">
            <div style="padding: 0.75rem 1rem 0 1rem;">
                <?php 
                $widgetPath = __DIR__ . '/../partials/academic_year_widget.php';
                if (file_exists($widgetPath)) {
                    include $widgetPath;
                }
                ?>
            </div>

            <div class="z-nav-label">Menu Ujian</div>

            <!-- 1. Dashboard -->
            <a href="<?php echo base_url('cbt/dashboard'); ?>" class="z-nav-item <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <!-- 1b. Pratinjau Dashboard CBT Siswa -->
            <a href="#" onclick="event.preventDefault(); openModalPreviewSiswa();" class="z-nav-item">
                <i data-lucide="smartphone"></i>
                <span>Pratinjau CBT Siswa</span>
            </a>

            <!-- 2. Dropdown: Manajemen Ujian -->
            <div class="z-nav-dropdown <?php echo $isUjianOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isUjianOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="layers"></i>
                        <span>Manajemen Ujian</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/topik'); ?>" class="z-nav-item <?php echo $active_menu === 'topik' ? 'active' : ''; ?>">
                        <i data-lucide="bookmark"></i>
                        <span>Topik Ujian</span>
                    </a>
                    <a href="<?php echo base_url('cbt/jadwal'); ?>" class="z-nav-item <?php echo $active_menu === 'jadwal' ? 'active' : ''; ?>">
                        <i data-lucide="calendar"></i>
                        <span>Jadwal Ujian</span>
                    </a>
                    <a href="<?php echo base_url('bank_soal'); ?>" class="z-nav-item <?php echo $active_menu === 'bank_soal' ? 'active' : ''; ?>">
                        <i data-lucide="database"></i>
                        <span>Bank Soal</span>
                    </a>
                    <a href="<?php echo base_url('cbt/token'); ?>" class="z-nav-item <?php echo $active_menu === 'token' ? 'active' : ''; ?>">
                        <i data-lucide="key-round"></i>
                        <span>Token Ujian</span>
                    </a>
                </div>
            </div>

            <!-- 3. Dropdown: Ruang & Denah Meja -->
            <div class="z-nav-dropdown <?php echo $isRuangOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isRuangOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="door-open"></i>
                        <span>Ruang & Tempat Duduk</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/ruang'); ?>" class="z-nav-item <?php echo $active_menu === 'ruang' ? 'active' : ''; ?>">
                        <i data-lucide="layout-grid"></i>
                        <span>Kelola Ruang</span>
                    </a>
                    <a href="<?php echo base_url('cbt/denah'); ?>" class="z-nav-item <?php echo $active_menu === 'denah' ? 'active' : ''; ?>">
                        <i data-lucide="grid"></i>
                        <span>Denah Tempat Duduk</span>
                    </a>
                    <a href="<?php echo base_url('cbt/ruang/berita-acara'); ?>" class="z-nav-item <?php echo $active_menu === 'berita_acara_ruang' ? 'active' : ''; ?>">
                        <i data-lucide="file-check-2"></i>
                        <span>Berita Acara Ruang</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown: Pengawas Ruang -->
            <div class="z-nav-dropdown <?php echo $isPengawasOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isPengawasOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="user-check"></i>
                        <span>Pengawas Ruang</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/pengawas/jadwal'); ?>" class="z-nav-item <?php echo $active_menu === 'pengawas_jadwal' ? 'active' : ''; ?>">
                        <i data-lucide="calendar"></i>
                        <span>Jadwal Pengawas</span>
                    </a>
                    <a href="<?php echo base_url('cbt/pengawas/scan'); ?>" class="z-nav-item <?php echo $active_menu === 'pengawas_scan' ? 'active' : ''; ?>">
                        <i data-lucide="scan-face"></i>
                        <span>Scan Presensi Pengawas</span>
                    </a>
                    <a href="<?php echo base_url('cbt/pengawas/rekap'); ?>" class="z-nav-item <?php echo $active_menu === 'pengawas_rekap' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-check"></i>
                        <span>Rekap Presensi Pengawas</span>
                    </a>
                </div>
            </div>

            <!-- 4. Dropdown: Absensi & Kehadiran CBT -->
            <div class="z-nav-dropdown <?php echo $isAbsenOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isAbsenOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="qr-code"></i>
                        <span>Absensi CBT</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/scan'); ?>" class="z-nav-item <?php echo $active_menu === 'absensi_cbt' ? 'active' : ''; ?>">
                        <i data-lucide="scan"></i>
                        <span>Scan 1 Pintu</span>
                    </a>
                    <a href="<?php echo base_url('cbt/absensi'); ?>" class="z-nav-item <?php echo $active_menu === 'absensi_rekap' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-check"></i>
                        <span>Rekap Absensi</span>
                    </a>
                </div>
            </div>

            <!-- 5. Peserta Kelas Jauh -->
            <a href="<?php echo base_url('cbt/peserta_khusus'); ?>" class="z-nav-item <?php echo $active_menu === 'peserta_khusus' ? 'active' : ''; ?>">
                <i data-lucide="users-2"></i>
                <span>Peserta Kelas Jauh</span>
            </a>

            <?php if (\App\Controllers\CbtController::isStandalone()): ?>
            <!-- 5b. Peserta Siswa SIAKAD (Hanya Tampil di Server CBT Offline) -->
            <a href="<?php echo base_url('cbt/peserta_siakad'); ?>" class="z-nav-item <?php echo $active_menu === 'peserta_siakad' ? 'active' : ''; ?>">
                <i data-lucide="graduation-cap"></i>
                <span>Peserta SIAKAD</span>
            </a>
            <?php endif; ?>

            <!-- 6. Dropdown: Hasil & Evaluasi -->
            <div class="z-nav-dropdown <?php echo $isHasilOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isHasilOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="award"></i>
                        <span>Hasil & Evaluasi</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/hasil'); ?>" class="z-nav-item <?php echo $active_menu === 'hasil' ? 'active' : ''; ?>">
                        <i data-lucide="file-spreadsheet"></i>
                        <span>Rekap Hasil Ujian</span>
                    </a>
                    <a href="<?php echo base_url('cbt/analisis'); ?>" class="z-nav-item <?php echo $active_menu === 'analisis' ? 'active' : ''; ?>">
                        <i data-lucide="bar-chart-2"></i>
                        <span>Analisis Butir Soal</span>
                    </a>
                    <a href="<?php echo base_url('cbt/hasil/daftar-hadir-kelas'); ?>" class="z-nav-item <?php echo $active_menu === 'daftar_hadir_kelas' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-list"></i>
                        <span>Daftar Hadir Per-Kelas</span>
                    </a>
                </div>
            </div>

            <!-- 7. Pengaturan CBT & Keamanan -->
            <a href="<?php echo base_url('cbt/pengaturan'); ?>" class="z-nav-item <?php echo $active_menu === 'pengaturan' ? 'active' : ''; ?>">
                <i data-lucide="shield-check"></i>
                <span>Pengaturan CBT</span>
            </a>

            <!-- 8. Sinkronisasi CBT Semi-Online -->
            <a href="<?php echo base_url('cbt/semi-online'); ?>" class="z-nav-item <?php echo $active_menu === 'semi_online' ? 'active' : ''; ?>">
                <i data-lucide="refresh-cw"></i>
                <span>Sinkron Semi-Online</span>
            </a>

            <!-- Toggle Suara AI Pengawas di Admin -->
            <button type="button" id="btnToggleAdminAiVoice" onclick="toggleAdminAiVoice()" class="z-nav-item" style="width: calc(100% - 1rem); background: rgba(14, 165, 233, 0.12); border: 1px solid rgba(56, 189, 248, 0.25); cursor: pointer; text-align: left; margin-top: 6px;">
                <i data-lucide="volume-2" id="iconAdminAiVoice" style="color: #38bdf8;"></i>
                <span id="lblAdminAiVoice" style="color: #e0f2fe; font-size: 0.78rem;">Suara AI Pengawas: ON</span>
            </button>

            <div class="z-nav-label" style="margin-top: 1rem;">Integrasi</div>

            <!-- Portal Utama -->
            <a href="<?php echo base_url('portal'); ?>" class="z-nav-item">
                <i data-lucide="home"></i>
                <span>Portal Utama</span>
            </a>
        </nav>
    </div>
</aside>

<?php
// Ambil daftar kelas untuk Modal Pratinjau Dashboard CBT Siswa
$previewKelasListReg = [];
$previewKelasListJauh = [];
try {
    $dbPrev = \App\Core\Database::connect('core');
    if ($dbPrev) {
        $previewKelasListReg = $dbPrev->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != '' ORDER BY nama_kelas ASC")->fetchAll(\PDO::FETCH_COLUMN);
        $previewKelasListJauh = $dbPrev->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != '' ORDER BY kelas_nama ASC")->fetchAll(\PDO::FETCH_COLUMN);
    }
} catch (\Throwable $e) {}
if (empty($previewKelasListReg)) {
    $previewKelasListReg = ['X E.1', 'XI F.1', 'XII F.1', 'XII F.2'];
}
?>

<!-- Modal Pilih Kelas untuk Pratinjau Dashboard CBT Siswa -->
<div id="modalPreviewCbtSiswa" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white; padding: 18px 22px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i data-lucide="smartphone" style="width: 22px; height: 22px;"></i>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Pratinjau Dashboard CBT Siswa</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">Cek mapel terjadwal & uji butir soal per kelas (Bypass Jam & Absen)</div>
                </div>
            </div>
            <button type="button" onclick="closeModalPreviewSiswa()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 10px; cursor: pointer; font-weight: 800; font-size: 1rem;">✕</button>
        </div>

        <div style="padding: 20px 22px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">1. Pilih Kelas yang Ingin Dicek:</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;" id="previewKelasGrid">
                    <?php foreach ($previewKelasListReg as $idx => $pkName): ?>
                        <button type="button" onclick="selectPreviewKelas(this, '<?php echo htmlspecialchars(addslashes($pkName)); ?>')" class="btn-prev-kelas <?php echo $idx === 0 ? 'selected' : ''; ?>" style="padding: 10px 12px; border-radius: 10px; border: 2px solid <?php echo $idx === 0 ? '#2563eb' : '#e2e8f0'; ?>; background: <?php echo $idx === 0 ? '#eff6ff' : '#f8fafc'; ?>; color: <?php echo $idx === 0 ? '#1d4ed8' : '#334155'; ?>; font-weight: 800; font-size: 0.85rem; cursor: pointer; text-align: left; display: flex; align-items: center; justify-content: space-between; transition: 0.15s;">
                            <span>🏛️ <?php echo htmlspecialchars($pkName); ?></span>
                            <span class="prev-check" style="display: <?php echo $idx === 0 ? 'inline' : 'none'; ?>;">✓</span>
                        </button>
                    <?php endforeach; ?>
                    <?php foreach ($previewKelasListJauh as $pkJauh): ?>
                        <button type="button" onclick="selectPreviewKelas(this, '<?php echo htmlspecialchars(addslashes($pkJauh)); ?>')" class="btn-prev-kelas" style="padding: 10px 12px; border-radius: 10px; border: 2px solid #e2e8f0; background: #fffbeb; color: #92400e; font-weight: 800; font-size: 0.82rem; cursor: pointer; text-align: left; display: flex; align-items: center; justify-content: space-between; transition: 0.15s;">
                            <span>📡 <?php echo htmlspecialchars($pkJauh); ?></span>
                            <span class="prev-check" style="display: none;">✓</span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="previewSiswaKelasInput" value="<?php echo htmlspecialchars($previewKelasListReg[0] ?? 'X E.1'); ?>">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">2. Tanggal Jadwal Ujian:</label>
                <input type="date" id="previewSiswaDateInput" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.88rem; font-weight: 700; color: #0f172a;">
                <div style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">Default hari ini. Bisa diganti tanggal besok untuk mengecek jadwal ujian besok.</div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 14px; font-size: 0.76rem; color: #166534; font-weight: 600; line-height: 1.45; margin-bottom: 18px;">
                <strong>✓ Mode Bebas Kunci (Simulasi Admin):</strong> Anda bisa langsung membuka & menguji soal pada seluruh mapel terjadwal hari tersebut meskipun jam sesi belum mulai, wajib absen aktif, atau token dikunci. Tidak akan mengotori data nilai siswa!
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModalPreviewSiswa()" style="padding: 10px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Batal</button>
                <button type="button" onclick="launchPreviewSiswa()" style="padding: 10px 18px; border-radius: 10px; border: none; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                    <i data-lucide="external-link" style="width: 15px; height: 15px;"></i> Buka Dashboard Siswa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCbtDropdown(toggleEl) {
    const parent = toggleEl.parentElement;
    const wasOpen = parent.classList.contains('open');
    document.querySelectorAll('.z-nav-dropdown.open').forEach(el => {
        el.classList.remove('open');
    });
    if (!wasOpen) {
        parent.classList.add('open');
    }
}

function openModalPreviewSiswa() {
    const modal = document.getElementById('modalPreviewCbtSiswa');
    if (modal) {
        modal.style.display = 'flex';
        if (window.lucide) lucide.createIcons();
    }
}

function closeModalPreviewSiswa() {
    const modal = document.getElementById('modalPreviewCbtSiswa');
    if (modal) modal.style.display = 'none';
}

function selectPreviewKelas(btn, kelasName) {
    document.getElementById('previewSiswaKelasInput').value = kelasName;
    document.querySelectorAll('#previewKelasGrid .btn-prev-kelas').forEach(b => {
        b.style.borderColor = '#e2e8f0';
        b.style.background = b.textContent.includes('Kelas Jauh') ? '#fffbeb' : '#f8fafc';
        b.style.color = b.textContent.includes('Kelas Jauh') ? '#92400e' : '#334155';
        const chk = b.querySelector('.prev-check');
        if (chk) chk.style.display = 'none';
    });
    btn.style.borderColor = '#2563eb';
    btn.style.background = '#eff6ff';
    btn.style.color = '#1d4ed8';
    const chk = btn.querySelector('.prev-check');
    if (chk) chk.style.display = 'inline';
}

function launchPreviewSiswa() {
    const kelas = document.getElementById('previewSiswaKelasInput').value || 'X E.1';
    const tgl = document.getElementById('previewSiswaDateInput').value || '<?php echo date('Y-m-d'); ?>';
    const url = '<?php echo base_url("apk/cbt.php"); ?>?admin_preview=1&preview_kelas=' + encodeURIComponent(kelas) + '&preview_date=' + encodeURIComponent(tgl);
    window.open(url, '_blank');
    closeModalPreviewSiswa();
}

// ============================================================
// SUARA AI PENGAWAS CBT DI ADMIN (Real-time Violation Monitor)
// ============================================================
let adminAiVoiceEnabled = (localStorage.getItem('cbt_admin_ai_voice') !== '0');
let lastViolationLogId = 0;
let adminViolationInitDone = false;

function updateAdminAiVoiceUI() {
    const lbl = document.getElementById('lblAdminAiVoice');
    const icon = document.getElementById('iconAdminAiVoice');
    const btn = document.getElementById('btnToggleAdminAiVoice');
    if (lbl) lbl.textContent = adminAiVoiceEnabled ? 'Suara AI Pengawas: ON' : 'Suara AI Pengawas: OFF';
    if (btn) btn.style.opacity = adminAiVoiceEnabled ? '1' : '0.6';
    if (icon) {
        icon.setAttribute('data-lucide', adminAiVoiceEnabled ? 'volume-2' : 'volume-x');
        if (window.lucide) try { lucide.createIcons(); } catch(e) {}
    }
}

function toggleAdminAiVoice() {
    adminAiVoiceEnabled = !adminAiVoiceEnabled;
    localStorage.setItem('cbt_admin_ai_voice', adminAiVoiceEnabled ? '1' : '0');
    updateAdminAiVoiceUI();
    if (adminAiVoiceEnabled) {
        speakAdminAiText('Suara AI pengawas ujian diaktifkan.');
    } else if ('speechSynthesis' in window) {
        try { window.speechSynthesis.cancel(); } catch(e) {}
    }
}

function speakAdminAiText(text) {
    if (!adminAiVoiceEnabled) return;
    if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;
    try {
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 1.0;
        utter.pitch = 1.05;
        utter.volume = 1.0;
        const voices = window.speechSynthesis.getVoices ? window.speechSynthesis.getVoices() : [];
        const idVoice = voices.find(v => (v.lang || '').toLowerCase().startsWith('id'));
        if (idVoice) utter.voice = idVoice;
        window.speechSynthesis.speak(utter);
    } catch(e) {}
}

function showAdminViolationToast(item) {
    let container = document.getElementById('adminViolationToastBox');
    if (!container) {
        container = document.createElement('div');
        container.id = 'adminViolationToastBox';
        container.style.cssText = 'position:fixed; top:18px; right:18px; z-index:999999; display:flex; flex-direction:column; gap:10px; max-width:360px;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = 'background:#7f1d1d; color:#fff; padding:12px 16px; border-radius:14px; border:2px solid #ef4444; box-shadow:0 12px 28px rgba(0,0,0,0.28); font-size:0.82rem; line-height:1.45;';
    toast.innerHTML = `<div style="font-weight:900; color:#fecaca; margin-bottom:2px;">🚨 PELANGGARAN CBT (${item.violation_count}x)</div>
        <div><b>${item.nama_siswa || 'Siswa'}</b> (${item.kelas_nama || '-'})</div>
        <div style="font-size:0.75rem; color:#fde68a; margin-top:2px;">${item.reason || 'Keluar Tab'} • ${item.nama_ujian || ''}</div>`;
    container.appendChild(toast);
    setTimeout(() => { try { toast.remove(); } catch(e) {} }, 7000);
}

function pollAdminViolations() {
    if (!adminAiVoiceEnabled || document.hidden) return;
    fetch('<?php echo base_url("cbt/api/cek-pelanggaran"); ?>?after_id=' + lastViolationLogId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (!res || !res.success) return;
        if (!adminViolationInitDone) {
            lastViolationLogId = parseInt(res.latest_id || 0, 10);
            adminViolationInitDone = true;
            return;
        }
        if (typeof res.latest_id !== 'undefined' && parseInt(res.latest_id, 10) > lastViolationLogId) {
            lastViolationLogId = parseInt(res.latest_id, 10);
        }
        if (Array.isArray(res.items) && res.items.length > 0) {
            res.items.forEach(item => {
                showAdminViolationToast(item);
                let aksi = 'keluar dari layar ujian';
                const rsn = String(item.reason || '');
                if (rsn.includes('Kembali')) aksi = 'menekan tombol kembali';
                else if (rsn.includes('Fullscreen')) aksi = 'keluar dari layar penuh';
                const msg = `Perhatian! Siswa ${item.nama_siswa || ''}, kelas ${item.kelas_nama || ''}, terdeteksi ${aksi}, pelanggaran ke ${item.violation_count || 1}.`;
                speakAdminAiText(msg);
            });
        }
    })
    .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
    updateAdminAiVoiceUI();
    pollAdminViolations();
    setInterval(pollAdminViolations, 4000);
});
</script>

<style>
/* Styling Tambahan untuk Dropdown Sidebar CBT */
.z-nav-dropdown {
    display: flex;
    flex-direction: column;
    margin-bottom: 2px;
}
.z-nav-dropdown-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 10px;
    margin: 1px 0.5rem;
}
.z-nav-dropdown-toggle:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
}
.z-nav-dropdown-toggle.active {
    background: rgba(14, 165, 233, 0.15);
    color: #38bdf8;
}
.z-nav-dropdown-toggle .dt-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.z-nav-dropdown-toggle .dt-left i,
.z-nav-dropdown-toggle .dt-left svg {
    width: 17px !important;
    height: 17px !important;
    opacity: 0.85;
}
.z-nav-dropdown-toggle .dt-icon {
    width: 14px !important;
    height: 14px !important;
    transition: transform 0.25s ease;
    opacity: 0.6;
}
.z-nav-dropdown.open .dt-icon {
    transform: rotate(180deg);
    opacity: 1;
}
.z-nav-dropdown-menu {
    display: none;
    flex-direction: column;
    padding-left: 1.25rem;
    margin: 2px 0 6px 0;
    border-left: 2px solid rgba(255, 255, 255, 0.1);
    margin-left: 1.25rem;
}
.z-nav-dropdown.open .z-nav-dropdown-menu {
    display: flex;
}
.z-nav-dropdown-menu .z-nav-item {
    padding: 0.45rem 0.85rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.65);
    margin: 1px 0;
}
.z-nav-dropdown-menu .z-nav-item:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}
.z-nav-dropdown-menu .z-nav-item.active {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-weight: 700;
}
</style>
