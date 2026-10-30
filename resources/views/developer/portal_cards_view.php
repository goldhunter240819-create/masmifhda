<?php
/**
 * Developer View: Portal Card Manager
 * Kelola visibilitas & akses setiap card pada halaman portal utama.
 */

$roleLabels = [1 => 'Admin', 2 => 'Guru/Staff', 3 => 'Siswa', 99 => 'Developer'];
$roleColors  = [1 => '#f59e0b', 2 => '#3b82f6', 3 => '#0ea5e9', 99 => '#8b5cf6'];

$cardIcons = [
    'siakad'         => 'graduation-cap',
    'ghcbt'          => 'monitor-play',
    'website'        => 'globe',
    'ai_generator'   => 'bot',
    'spmb'           => 'user-plus',
    'keuangan'       => 'credit-card',
    'bos'            => 'landmark',
    'absensi'        => 'qr-code',
    'persuratan'     => 'mail',
    'inventaris'     => 'package',
    'perpustakaan'   => 'library',
    'elearning'      => 'monitor',
    'piagam'         => 'award',
    'ujian_madrasah' => 'scroll-text',
    'rekap_soal'     => 'database',
    'rekap_jadwal'   => 'calendar',
    'rdm'            => 'book-open-check',
    'manajemen_berkas' => 'folder-check',
];

$cardThemes = [
    'siakad'         => ['bg'=>'#f0f9ff','color'=>'#0284c7'],
    'ghcbt'          => ['bg'=>'#ede9fe','color'=>'#7c3aed'],
    'website'        => ['bg'=>'#eff6ff','color'=>'#2563eb'],
    'ai_generator'   => ['bg'=>'#faf5ff','color'=>'#9333ea'],
    'spmb'           => ['bg'=>'#ede9fe','color'=>'#7c3aed'],
    'keuangan'       => ['bg'=>'#fffbeb','color'=>'#d97706'],
    'bos'            => ['bg'=>'#f0fdfa','color'=>'#0d9488'],
    'absensi'        => ['bg'=>'#f0fdfa','color'=>'#0d9488'],
    'persuratan'     => ['bg'=>'#eff6ff','color'=>'#2563eb'],
    'inventaris'     => ['bg'=>'#faf5ff','color'=>'#9333ea'],
    'perpustakaan'   => ['bg'=>'#f0fdfa','color'=>'#0d9488'],
    'elearning'      => ['bg'=>'#fff1f2','color'=>'#e11d48'],
    'piagam'         => ['bg'=>'#fffbeb','color'=>'#d97706'],
    'ujian_madrasah' => ['bg'=>'#f0f9ff','color'=>'#0284c7'],
    'rekap_soal'     => ['bg'=>'#eff6ff','color'=>'#2563eb'],
    'rekap_jadwal'   => ['bg'=>'#faf5ff','color'=>'#9333ea'],
    'rdm'            => ['bg'=>'#0f2e5c','color'=>'#38bdf8'],
    'manajemen_berkas' => ['bg'=>'#ede9fe','color'=>'#7c3aed'],
];
?>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <?php if(isset($_GET['saved'])): ?>
        <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#34d399;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Pengaturan card portal berhasil disimpan!
        </div>
        <?php endif; ?>

        <div class="card" style="padding:1.5rem;margin-bottom:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                <div>
                    <h2 style="margin:0 0 6px;color:#0ea5e9;display:flex;align-items:center;gap:10px;">
                        <i data-lucide="layout-dashboard" style="width:22px;height:22px;"></i>
                        Portal Card Manager
                    </h2>
                    <p style="margin:0;color:#64748b;font-size:0.875rem;">
                        Aktifkan/nonaktifkan card yang tampil di halaman Portal, dan atur role mana yang bisa melihat setiap card.
                    </p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button type="button" onclick="toggleAll(true)" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#0ea5e9;padding:7px 14px;border-radius:8px;cursor:pointer;font-size:0.8rem;font-weight:700;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="toggle-right" style="width:14px;height:14px;"></i> Aktifkan Semua
                    </button>
                    <button type="button" onclick="toggleAll(false)" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:7px 14px;border-radius:8px;cursor:pointer;font-size:0.8rem;font-weight:700;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="toggle-left" style="width:14px;height:14px;"></i> Nonaktifkan Semua
                    </button>
                </div>
            </div>
        </div>

        <form action="<?php echo base_url('dev-panel/portal-cards/save'); ?>" method="POST" id="cardForm">

        <!-- Legend Roles -->
        <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap;">
            <span style="font-size:0.75rem;color:#64748b;font-weight:700;align-self:center;">Role:</span>
            <?php foreach($roleLabels as $rid => $rlabel): ?>
            <span style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);padding:4px 10px;border-radius:100px;font-size:0.72rem;font-weight:700;color:<?php echo $roleColors[$rid]; ?>;">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:<?php echo $roleColors[$rid]; ?>;margin-right:4px;"></span>
                <?php echo $rid ?> â€” <?php echo $rlabel ?>
            </span>
            <?php endforeach; ?>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
            <?php foreach($cards as $key => $card):
                $icon  = $cardIcons[$key]  ?? 'layout-grid';
                $theme = $cardThemes[$key] ?? ['bg'=>'#f8fafc','color'=>'#64748b'];
                $enabled = $card['enabled'];
                $cardRoles = $card['roles'];
            ?>
            <div class="card-block" id="block-<?php echo $key; ?>" style="
                background:rgba(15,23,42,0.6);
                border:1px solid <?php echo $enabled ? 'rgba(16,185,129,0.2)' : 'rgba(255,255,255,0.05)'; ?>;
                border-radius:16px;
                padding:1.25rem;
                transition:all 0.3s ease;
                opacity:<?php echo $enabled ? '1' : '0.55'; ?>;
            ">
                <!-- Header -->
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem;">
                    <div style="width:44px;height:44px;border-radius:12px;background:<?php echo $theme['bg']; ?>;color:<?php echo $theme['color']; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="<?php echo $icon; ?>" style="width:20px;height:20px;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="color:#e2e8f0;font-weight:800;font-size:0.9rem;"><?php echo htmlspecialchars($card['label']); ?></div>
                        <div style="color:#475569;font-size:0.72rem;font-weight:600;font-family:monospace;"><?php echo $key; ?></div>
                    </div>
                    <!-- Toggle Switch -->
                    <label class="big-toggle" style="cursor:pointer;flex-shrink:0;" title="Aktifkan/nonaktifkan card ini">
                        <input type="hidden" name="cards[<?php echo $key; ?>][enabled]" value="0">
                        <input type="checkbox" 
                            name="cards[<?php echo $key; ?>][enabled]" 
                            value="1"
                            onchange="onToggle('<?php echo $key; ?>', this.checked)"
                            <?php echo $enabled ? 'checked' : ''; ?>>
                        <span class="big-slider"></span>
                    </label>
                </div>

                <!-- Role Checkboxes -->
                <div style="margin-bottom:0.5rem;">
                    <div style="font-size:0.72rem;color:#475569;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Tampilkan untuk role:</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        <?php foreach($roleLabels as $rid => $rlabel): ?>
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:5px;
                            background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                            padding:4px 10px;border-radius:100px;transition:all 0.2s;
                            font-size:0.72rem;font-weight:700;color:<?php echo in_array($rid, $cardRoles) ? $roleColors[$rid] : '#475569'; ?>;"
                            class="role-pill" 
                            data-checked="<?php echo in_array($rid, $cardRoles) ? '1' : '0'; ?>"
                            style="border-color:<?php echo in_array($rid, $cardRoles) ? $roleColors[$rid].'44' : 'rgba(255,255,255,0.07)'; ?>">
                            <input type="checkbox" 
                                name="cards[<?php echo $key; ?>][roles][]" 
                                value="<?php echo $rid; ?>"
                                style="display:none;"
                                <?php echo in_array($rid, $cardRoles) ? 'checked' : ''; ?>>
                            <span style="width:6px;height:6px;border-radius:50%;background:<?php echo in_array($rid, $cardRoles) ? $roleColors[$rid] : '#334155'; ?>;display:inline-block;"></span>
                            <?php echo $rid ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="position:sticky;bottom:0;background:rgba(9,14,23,0.95);backdrop-filter:blur(12px);padding:1rem 0;margin-top:1.5rem;border-top:1px solid rgba(255,255,255,0.05);z-index:10;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#475569;font-size:0.8rem;">
                    <i data-lucide="info" style="width:14px;height:14px;vertical-align:-2px;"></i>
                    Perubahan langsung berlaku setelah disimpan.
                </span>
                <button type="submit" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);border:none;color:white;padding:10px 28px;border-radius:12px;cursor:pointer;font-weight:800;font-size:0.9rem;display:flex;align-items:center;gap:8px;box-shadow:0 4px 15px rgba(16,185,129,0.3);transition:all 0.3s;">
                    <i data-lucide="save" style="width:16px;height:16px;"></i> Simpan Pengaturan
                </button>
            </div>
        </div>

        </form>

        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<style>
/* Big Toggle */
.big-toggle { position:relative; display:inline-block; width:46px; height:26px; }
.big-toggle input { opacity:0; width:0; height:0; }
.big-slider {
    position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
    background:rgba(255,255,255,0.1); border-radius:34px; transition:.3s;
    box-shadow:inset 0 2px 4px rgba(0,0,0,0.3);
}
.big-slider:before {
    position:absolute; content:""; height:18px; width:18px;
    left:4px; bottom:4px; background:#64748b; border-radius:50%;
    transition:.3s; box-shadow:0 2px 5px rgba(0,0,0,0.2);
}
.big-toggle input:checked + .big-slider { background:rgba(16,185,129,0.2); border:1px solid rgba(16,185,129,0.5); }
.big-toggle input:checked + .big-slider:before { transform:translateX(20px); background:#0ea5e9; box-shadow:0 0 10px rgba(16,185,129,0.8); }

/* Role pill hover */
.role-pill:hover { border-color:rgba(255,255,255,0.15) !important; background:rgba(255,255,255,0.05) !important; }
.role-pill input:checked ~ span { /* handled via JS */ }

/* Card block transition */
.card-block { transition: opacity 0.3s ease, border-color 0.3s ease; }
</style>

<script>
function onToggle(key, enabled) {
    const block = document.getElementById('block-' + key);
    if (enabled) {
        block.style.opacity = '1';
        block.style.borderColor = 'rgba(16,185,129,0.2)';
    } else {
        block.style.opacity = '0.55';
        block.style.borderColor = 'rgba(255,255,255,0.05)';
    }
}

function toggleAll(state) {
    document.querySelectorAll('input[type=checkbox][name*="[enabled]"]').forEach(cb => {
        if(cb.value === '1') {
            cb.checked = state;
            const key = cb.name.match(/cards\[(\w+)\]/)[1];
            onToggle(key, state);
        }
    });
}

// Style role pills on click
document.querySelectorAll('.role-pill').forEach(pill => {
    const cb = pill.querySelector('input');
    const dot = pill.querySelector('span');
    const colorMap = { 1: '#f59e0b', 2: '#3b82f6', 3: '#0ea5e9', 99: '#8b5cf6' };
    const rid = parseInt(cb.value);
    const color = colorMap[rid] || '#64748b';

    function syncStyle() {
        if (cb.checked) {
            pill.style.color = color;
            pill.style.borderColor = color + '55';
            dot.style.background = color;
        } else {
            pill.style.color = '#475569';
            pill.style.borderColor = 'rgba(255,255,255,0.07)';
            dot.style.background = '#334155';
        }
    }
    syncStyle();
    pill.addEventListener('click', () => { setTimeout(syncStyle, 10); });
});

lucide.createIcons();
</script>

