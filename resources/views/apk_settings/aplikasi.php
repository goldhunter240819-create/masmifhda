<?php
$page_title = 'Pengaturan Aplikasi';

$db = \App\Core\Database::connect('core');

// Ambil daftar guru
$gurus = $db->query("SELECT id, nama, nip, is_kamad, jabatan FROM guru ORDER BY nama ASC")->fetchAll();

// Ambil hak akses custom
$akses_raw = $db->query("SELECT guru_id, app_code FROM apk_akses_guru")->fetchAll();
$akses_map = [];
foreach ($akses_raw as $row) {
    $akses_map[$row['guru_id']][] = $row['app_code'];
}

// Definisi aplikasi yang bisa didelegasikan
$apk_apps = [
    'Keuangan & Tabungan' => [
        'kasir_tabungan' => ['icon' => 'wallet', 'name' => 'Kasir Tab.', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
        'kasir_pembayaran' => ['icon' => 'calculator', 'name' => 'Kasir Pembayaran', 'color' => '#8b5cf6', 'bg' => '#f3e8ff'],
        'bku_bendahara' => ['icon' => 'banknote', 'name' => 'BKU Bendahara', 'color' => '#10b981', 'bg' => '#ecfdf5'],
        'bendahara_bos' => ['icon' => 'landmark', 'name' => 'Bendahara BOS', 'color' => '#f43f5e', 'bg' => '#ffe4e6']
    ],
    'Absensi & Perizinan' => [
        'scan_siswa' => ['icon' => 'scan', 'name' => 'Scan Siswa', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
        'scan_guru' => ['icon' => 'scan-face', 'name' => 'Scan Guru', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
        'admin_izin' => ['icon' => 'clipboard-check', 'name' => 'Admin Izin', 'color' => '#f59e0b', 'bg' => '#fffbeb']
    ],
    'Kepala Madrasah' => [
        'mon_keuangan' => ['icon' => 'line-chart', 'name' => 'Mon. Keuangan', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
        'mon_bos' => ['icon' => 'pie-chart', 'name' => 'Monitor BOS', 'color' => '#6366f1', 'bg' => '#eef2ff'],
        'qr_absen_guru' => ['icon' => 'scan-line', 'name' => 'QR Absen Guru', 'color' => '#4f46e5', 'bg' => '#e0e7ff'],
        'kurikulum_pkg' => ['icon' => 'award', 'name' => 'PKG', 'color' => '#eab308', 'bg' => '#fefce8']
    ],
    'Waka Kurikulum' => [
        'wakakur_jadwal' => ['icon' => 'calendar-check', 'name' => 'Jadwal KBM', 'color' => '#d946ef', 'bg' => '#fdf4ff'],
        'mon_jurnal' => ['icon' => 'book-open', 'name' => 'Monitor Jurnal', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
        'val_ekinerja' => ['icon' => 'clipboard-check', 'name' => 'Supervisi Adm.', 'color' => '#d946ef', 'bg' => '#fdf4ff'],
        'supervisi_kunjungan' => ['icon' => 'presentation', 'name' => 'Kunjungan Kelas', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
        'supervisi_kelas' => ['icon' => 'presentation', 'name' => 'Supervisi Kelas', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'mon_penilaian' => ['icon' => 'clipboard-list', 'name' => 'Supervisi Penilaian', 'color' => '#c026d3', 'bg' => '#fdf4ff'],
        'mon_elearning' => ['icon' => 'laptop', 'name' => 'Mon. E-Learning', 'color' => '#10b981', 'bg' => '#ecfdf5']
    ],
    'Waka Kesiswaan' => [
        'wakasis_poin' => ['icon' => 'clipboard-list', 'name' => 'Poin Log', 'color' => '#ef4444', 'bg' => '#fef2f2'],
        'wakasis_kehadiran' => ['icon' => 'pie-chart', 'name' => 'Kehadiran', 'color' => '#4338ca', 'bg' => '#e0e7ff'],
        'absen_qr' => ['icon' => 'scan-line', 'name' => 'QR Absen Siswa', 'color' => '#9333ea', 'bg' => '#f3e8ff'],
        'absen_mapel' => ['icon' => 'book-check', 'name' => 'Absen Mapel', 'color' => '#10b981', 'bg' => '#ecfdf5']
    ],
    'Bimbingan Konseling' => [
        'bk_jurnal' => ['icon' => 'book-user', 'name' => 'Jurnal Konseling', 'color' => '#6366f1', 'bg' => '#eef2ff'],
        'bk_poin' => ['icon' => 'alert-triangle', 'name' => 'Poin Kedisiplinan', 'color' => '#ef4444', 'bg' => '#fef2f2']
    ],
    'Wali Kelas' => [
        'wali_siswa' => ['icon' => 'users', 'name' => 'Data Siswa', 'color' => '#6366f1', 'bg' => '#eef2ff'],
        'wali_jurnal' => ['icon' => 'clipboard-list', 'name' => 'Jurnal Kelas', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
        'wali_absen' => ['icon' => 'user-check', 'name' => 'Rekap Absen', 'color' => '#10b981', 'bg' => '#ecfdf5'],
        'wali_buku' => ['icon' => 'list-todo', 'name' => 'Buku Kerja', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
        'wali_poin' => ['icon' => 'alert-circle', 'name' => 'Log Kedisiplinan', 'color' => '#ef4444', 'bg' => '#fef2f2']
    ],
    'Pembina Ekstra' => [
        'ekstra_daftar' => ['icon' => 'list-todo', 'name' => 'Daftar Ekstra', 'color' => '#10b981', 'bg' => '#ecfdf5']
    ],
    'Guru Wali (Mentor)' => [
        'wali_asuh_siswa' => ['icon' => 'users', 'name' => 'Daftar Anak Asuh', 'color' => '#ec4899', 'bg' => '#fdf2f8'],
        'wali_asuh_pantau' => ['icon' => 'activity', 'name' => 'Pantau Akademik', 'color' => '#06b6d4', 'bg' => '#ecfeff'],
        'wali_asuh_jurnal' => ['icon' => 'heart-handshake', 'name' => 'Catatan Bimbingan', 'color' => '#f59e0b', 'bg' => '#fffbeb']
    ]
];

ob_start();
?>
<div class="elite-page-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #d946ef 100%); box-shadow: 0 15px 40px -10px rgba(139, 92, 246, 0.4);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div class="header-text-premium">
            <h1><i data-lucide="settings"></i> Pengaturan Aplikasi</h1>
            <p>Konfigurasi tema, warna utama, dan pendelegasian akses kustom fitur Aplikasi Mobile kepada masing-masing guru.</p>
        </div>
    </div>
</div>

<div class="z-card" style="margin-bottom: 2rem;">
    <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Delegasi Hak Akses Aplikasi</h3>
        <input type="text" id="searchGuru" placeholder="Cari nama guru..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 250px;">
    </div>
    
    <div style="padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; color: #64748b;">
        <i data-lucide="info" style="width:14px; height:14px; vertical-align:middle;"></i> Pilih aplikasi yang ingin ditampilkan pada menu <b>"Aplikasi Saya"</b> di akun APK masing-masing guru.
    </div>

    <div style="max-height: 600px; overflow-y: auto;">
        <?php foreach ($gurus as $g): ?>
            <?php 
                $akses = $akses_map[$g['id']] ?? []; 
                $hasAny = count($akses) > 0;
            ?>
            <div class="guru-row" style="border-bottom: 1px solid #f1f5f9; padding: 15px 20px;" data-nama="<?= strtolower(htmlspecialchars($g['nama'])) ?>">
                <div style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleAccordion(<?= $g['id'] ?>)">
                    <div style="display:flex; align-items:center; gap: 15px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, #f3e8ff, #fdf4ff); color:#d946ef; display:flex; align-items:center; justify-content:center; font-weight:700;">
                            <?= substr(htmlspecialchars($g['nama']), 0, 2) ?>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #334155; font-size: 1.05rem;"><?= htmlspecialchars($g['nama']) ?></div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top:2px;">
                                NIP: <?= empty($g['nip']) ? '-' : htmlspecialchars($g['nip']) ?> &bull; 
                                <span style="color:<?= $g['is_kamad'] ? '#b91c1c' : '#d946ef' ?>; font-weight:600;"><?= $g['is_kamad'] ? 'Kepala Madrasah' : 'Guru' ?></span>
                                &bull; <?= htmlspecialchars($g['jabatan']) ?>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:15px;">
                        <?php if($hasAny): ?>
                            <span class="pill pill-green" id="badge-<?= $g['id'] ?>"><?= count($akses) ?> Akses Aktif</span>
                        <?php else: ?>
                            <span class="pill" id="badge-<?= $g['id'] ?>" style="background:#f1f5f9; color:#94a3b8;">Default</span>
                        <?php endif; ?>
                        <i data-lucide="chevron-down" id="icon-<?= $g['id'] ?>" style="color:#94a3b8; transition:0.3s;"></i>
                    </div>
                </div>

                <div id="acc-<?= $g['id'] ?>" class="acc-body" data-akses='<?= json_encode($akses) ?>' style="display:none; margin-top:20px; padding:20px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                    <!-- Content will be loaded lazily -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Template for Apps Grid (rendered once, cloned via JS) -->
<div id="apps-template" style="display:none;">
    <?php foreach ($apk_apps as $catName => $apps): ?>
        <div style="margin-bottom: 20px;">
            <div style="font-size: 0.85rem; font-weight: 800; color: #475569; margin-bottom: 12px; text-transform:uppercase; letter-spacing:0.5px;">
                <?= $catName ?>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:15px;">
                <?php foreach ($apps as $code => $app): ?>
                    <label class="app-toggle-card" data-code="<?= $code ?>" data-color="<?= $app['color'] ?>" style="display:flex; align-items:center; gap:12px; padding:12px; background:#fff; border:1px solid #cbd5e1; border-radius:10px; cursor:pointer; transition:0.2s; box-shadow: none;">
                        <div style="width:36px; height:36px; border-radius:8px; background:<?= $app['bg'] ?>; color:<?= $app['color'] ?>; display:flex; align-items:center; justify-content:center;">
                            <i data-lucide="<?= $app['icon'] ?>" style="width:18px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= $app['name'] ?></div>
                        </div>
                        <div>
                            <input type="checkbox" class="z-switch app-checkbox" data-code="<?= $code ?>">
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function toggleAccordion(id) {
    const acc = document.getElementById('acc-' + id);
    const icon = document.getElementById('icon-' + id);
    if (acc.style.display === 'none') {
        if (!acc.hasAttribute('data-loaded')) {
            const tmpl = document.getElementById('apps-template').cloneNode(true);
            tmpl.style.display = 'block';
            tmpl.id = ''; // clear id to avoid duplicates
            
            const aksesStr = acc.getAttribute('data-akses');
            let akses = [];
            if(aksesStr) {
                try { akses = JSON.parse(aksesStr); } catch(e) {}
            }
            
            // Set up checkboxes and styles
            tmpl.querySelectorAll('.app-toggle-card').forEach(card => {
                const code = card.getAttribute('data-code');
                const color = card.getAttribute('data-color');
                const checkbox = card.querySelector('.app-checkbox');
                
                if (akses.includes(code)) {
                    checkbox.checked = true;
                    card.classList.add('active');
                    card.style.borderColor = color;
                    card.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
                }
                
                checkbox.addEventListener('change', function() {
                    toggleAkses(id, code, this.checked, card, color, acc);
                });
            });
            
            acc.appendChild(tmpl);
            acc.setAttribute('data-loaded', 'true');
        }
        acc.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        acc.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

function toggleAkses(guruId, appCode, isActive, cardElem, activeColor, accWrapper) {
    // Update styling instantly
    if(isActive) {
        cardElem.style.borderColor = activeColor;
        cardElem.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
    } else {
        cardElem.style.borderColor = '#cbd5e1';
        cardElem.style.boxShadow = 'none';
    }

    // Send AJAX request
    fetch('<?= base_url("admin/apk-settings/aplikasi/save-akses") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `guru_id=${guruId}&app_code=${appCode}&is_active=${isActive ? 1 : 0}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            alert('Gagal menyimpan perubahan: ' + (data.message || 'Unknown error'));
        } else {
            // Update badge count
            let activeCount = accWrapper.querySelectorAll('.app-checkbox:checked').length;
            const badge = document.getElementById('badge-' + guruId);
            if (activeCount > 0) {
                badge.className = 'pill pill-green';
                badge.style = '';
                badge.innerHTML = activeCount + ' Akses Aktif';
            } else {
                badge.className = 'pill';
                badge.style = 'background:#f1f5f9; color:#94a3b8;';
                badge.innerHTML = 'Default';
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan jaringan.');
    });
}

// Simple search filter
document.getElementById('searchGuru').addEventListener('input', function(e) {
    const val = e.target.value.toLowerCase();
    document.querySelectorAll('.guru-row').forEach(row => {
        const nama = row.getAttribute('data-nama');
        if(nama.includes(val)) {
            row.style.display = 'block';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
