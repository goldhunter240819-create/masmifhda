<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <!-- Spacer for fixed header -->
        <div style="height: 20px;"></div>

        <div class="header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 30px 25px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
    <div class="header-content" style="color: white;">
        <h1 style="margin: 0; font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; display: flex; align-items: center; gap: 12px;">
            <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="globe" style="width: 24px; height: 24px; color: #38bdf8;"></i>
            </div>
            Jurnal Mengajar Global
        </h1>
        <p style="margin: 8px 0 0 0; color: #94a3b8; font-size: 0.95rem;">Pantau aktivitas mengajar seluruh guru secara real-time berdasarkan kelas dan tanggal.</p>
    </div>
</div>

<div class="z-panel" style="margin-bottom: 20px;">
    <div class="z-panel-body">
        <form method="GET" action="" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:5px;">Mulai Tanggal</label>
                <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($filter_tanggal_mulai) ?>" style="padding:10px 15px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; min-width:160px; background:#f8fafc;">
            </div>
            <div>
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:5px;">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="<?= htmlspecialchars($filter_tanggal_sampai) ?>" style="padding:10px 15px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; min-width:160px; background:#f8fafc;">
            </div>
            <div>
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:5px;">Filter Kelas</label>
                <select name="kelas_id" style="padding:10px 15px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; min-width:150px; background:#f8fafc;">
                    <option value="0">-- Semua Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($filter_kelas == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:5px;">Filter Guru</label>
                <select name="guru_id" style="padding:10px 15px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; min-width:150px; background:#f8fafc;">
                    <option value="0">-- Semua Guru --</option>
                    <?php foreach($guruList as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= ((isset($filter_guru) ? $filter_guru : 0) == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="z-btn z-btn-primary" style="padding: 10px 20px; font-size:0.95rem; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="filter" style="width:16px;"></i> Terapkan Filter
                </button>
            </div>
            <?php if($filter_kelas > 0 || (isset($filter_guru) && $filter_guru > 0) || isset($_GET['tanggal_mulai'])): ?>
            <div>
                <a href="<?php echo base_url('admin/siakad/jurnal-global'); ?>" class="z-btn" style="background:#f1f5f9; color:#475569; padding: 10px 20px; text-decoration:none;">Reset</a>
            </div>
            <?php endif; ?>
            <div>
                <a href="?tanggal_mulai=<?= urlencode($filter_tanggal_mulai) ?>&tanggal_sampai=<?= urlencode($filter_tanggal_sampai) ?>&kelas_id=<?= urlencode($filter_kelas) ?>&guru_id=<?= urlencode(isset($filter_guru) ? $filter_guru : 0) ?>&cetak=1" target="_blank" class="z-btn" style="background:#10b981; color:white; padding: 10px 20px; text-decoration:none; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="printer" style="width:16px;"></i> Cetak
                </a>
            </div>
        </form>
    </div>
</div>

<div class="z-panel">
    <div class="z-panel-body" style="padding: 0;">
        <div class="z-table-responsive">
            <table class="z-table" style="margin:0;">
                <thead>
                    <tr>
                        <th width="50" style="text-align:center;">No</th>
                        <th>Informasi Guru</th>
                        <th>Kelas & Mapel</th>
                        <th>Materi Pembahasan</th>
                        <th width="220" style="text-align:center;">Presensi Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jurnal_global)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px 20px;">
                            <div style="display:inline-block; padding:15px; background:#f8fafc; border-radius:50%; margin-bottom:10px;">
                                <i data-lucide="inbox" style="width:32px; height:32px; color:#cbd5e1;"></i>
                            </div>
                            <div style="color:#64748b; font-weight:600;">Tidak ada aktivitas mengajar ditemukan</div>
                            <div style="font-size:0.85rem; color:#94a3b8; margin-top:5px;">Coba ubah filter tanggal atau kelas di atas.</div>
                        </td>
                    </tr>
                    <?php else: $no=1; foreach($jurnal_global as $j): ?>
                    <tr <?php if(!empty($j['is_pulang_awal'])) echo 'style="background: linear-gradient(to right, #eff6ff, #dbeafe); box-shadow: inset 4px 0 0 0 #3b82f6;" title="Agenda Pulang Dipercepat / Rapat"'; else if(!empty($j['is_duplicate'])) echo 'style="background-color: #ffedd5;" title="Jurnal Ganda pada Mapel dan Tanggal yang sama"'; ?>>
                        <td style="text-align:center; font-weight:bold; color:#94a3b8;"><?= $no++ ?></td>
                        <?php if(!empty($j['is_libur'])): ?>
                            <td colspan="4" style="text-align:center; font-weight:bold; color:#ffffff; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); border:none; padding: 15px;">
                                <div style="display:inline-flex; align-items:center; gap:8px;">
                                    <i data-lucide="calendar-x" style="width:20px; height:20px;"></i>
                                    <span><?= htmlspecialchars($j['materi']) ?></span>
                                </div>
                            </td>
                        <?php else: ?>
                        <td>
                            <div style="font-weight:700; color:#1e293b; font-size:0.95rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="user" style="width:14px; color:#64748b;"></i> <?= htmlspecialchars($j['nama_guru']) ?>
                            </div>
                            <div style="font-size:0.8rem; color:#64748b; margin-top:3px;">
                                <i data-lucide="clock" style="width:12px; display:inline-block; vertical-align:-2px;"></i> Diinput pada <?= date('H:i', strtotime($j['created_at'])) ?> (<?= date('d/m/Y', strtotime($j['tanggal'])) ?>)
                            </div>
                        </td>
                        <td>
                            <div style="display:inline-block; background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:4px; margin-bottom:4px;">
                                <?= htmlspecialchars($j['nama_kelas']) ?>
                            </div>
                            <div style="font-weight:600; color:#334155; font-size:0.85rem;">
                                <?= htmlspecialchars($j['nama_mapel']) ?>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $materiText = $j['materi'];
                            $invalBanner = '';
                            if (preg_match('/^\(Inval oleh:\s*(.*?)\)\s*(.*)/s', $materiText, $matches)) {
                                $invalOleh = trim($matches[1]);
                                $materiText = trim($matches[2]);
                                
                                // Jika formatnya (Inval oleh: XXX)\n\nTugas Inval:\nYYY
                                // Hapus teks "Tugas Inval:" yang redundant
                                $materiText = preg_replace('/^Tugas Inval:\s*/i', '', $materiText);
                                
                                $invalBanner = '<div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #b45309; padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #fcd34d;"><i data-lucide="user-check" style="width: 14px;"></i> Inval oleh: ' . htmlspecialchars($invalOleh) . '</div><br>';
                            }
                            ?>
                            <?= $invalBanner ?>
                            <div style="font-weight:600; color:#1e293b; margin-bottom:4px; font-size:0.9rem;"><?= nl2br(htmlspecialchars($materiText)) ?></div>
                            <?php if(!empty($j['keterangan'])): ?>
                            <div style="font-size:0.8rem; color:#64748b; background:#f8fafc; padding:6px 10px; border-left:3px solid #cbd5e1; border-radius:4px;">
                                <i><?= htmlspecialchars($j['keterangan']) ?></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center; vertical-align:middle;">
                            <?php 
                            $ad = isset($j['absen_detail']) ? $j['absen_detail'] : ['hadir'=>[],'izin'=>[],'sakit'=>[],'alpa'=>[],'total'=>0];
                            $cntHadir = count($ad['hadir']);
                            $cntIzin = count($ad['izin']);
                            $cntSakit = count($ad['sakit']);
                            $cntAlpa = count($ad['alpa']);
                            $jsonDetail = htmlspecialchars(json_encode([
                                'hadir' => $ad['hadir'],
                                'izin' => $ad['izin'],
                                'sakit' => $ad['sakit'],
                                'alpa' => $ad['alpa'],
                                'total' => $ad['total']
                            ]), ENT_QUOTES, 'UTF-8');
                            ?>
                            <div onclick="openAbsenModal(<?= $jsonDetail ?>, '<?= htmlspecialchars($j['nama_kelas'], ENT_QUOTES) ?>', '<?= htmlspecialchars($j['nama_mapel'], ENT_QUOTES) ?>', '<?= date('d-m-Y', strtotime($j['tanggal'])) ?>', '<?= htmlspecialchars($j['nama_guru'], ENT_QUOTES) ?>')" 
                                 class="absen-card-btn" 
                                 title="Klik untuk melihat nama siswa" 
                                 style="cursor:pointer; display:inline-flex; flex-direction:column; gap:6px; align-items:center; background:#ffffff; padding:8px 12px; border-radius:12px; border:1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.04); transition:all 0.2s ease;">
                                
                                <div style="display:flex; gap:4px; flex-wrap:nowrap; justify-content:center; align-items:center;">
                                    <span style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:3px 7px; border-radius:8px; font-size:0.72rem; font-weight:800;" title="Hadir: <?= $cntHadir ?>">
                                        <?= $cntHadir ?> Hadir
                                    </span>
                                    <span style="background:<?= $cntIzin > 0 ? '#dbeafe' : '#f1f5f9' ?>; color:<?= $cntIzin > 0 ? '#1d4ed8' : '#94a3b8' ?>; border:1px solid <?= $cntIzin > 0 ? '#93c5fd' : '#e2e8f0' ?>; padding:3px 7px; border-radius:8px; font-size:0.72rem; font-weight:800;" title="Izin: <?= $cntIzin ?>">
                                        <?= $cntIzin ?> Izin
                                    </span>
                                    <span style="background:<?= $cntSakit > 0 ? '#fef3c7' : '#f1f5f9' ?>; color:<?= $cntSakit > 0 ? '#b45309' : '#94a3b8' ?>; border:1px solid <?= $cntSakit > 0 ? '#fde047' : '#e2e8f0' ?>; padding:3px 7px; border-radius:8px; font-size:0.72rem; font-weight:800;" title="Sakit: <?= $cntSakit ?>">
                                        <?= $cntSakit ?> Sakit
                                    </span>
                                    <span style="background:<?= $cntAlpa > 0 ? '#fee2e2' : '#f1f5f9' ?>; color:<?= $cntAlpa > 0 ? '#b91c1c' : '#94a3b8' ?>; border:1px solid <?= $cntAlpa > 0 ? '#fca5a5' : '#e2e8f0' ?>; padding:3px 7px; border-radius:8px; font-size:0.72rem; font-weight:800;" title="Alpa: <?= $cntAlpa ?>">
                                        <?= $cntAlpa ?> Alpa
                                    </span>
                                </div>
                                <div style="font-size:0.72rem; font-weight:700; color:#0ea5e9; display:flex; align-items:center; gap:4px; text-decoration:underline;">
                                    <i data-lucide="users" style="width:12px; height:12px;"></i> Lihat Nama Siswa
                                </div>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Modal Detail Presensi Siswa -->
<div id="modalDetailAbsen" class="z-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.65); backdrop-filter:blur(5px); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:20px; width:92%; max-width:680px; max-height:85vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #e2e8f0; animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Modal Header -->
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 id="modalTitle" style="margin:0; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="users" style="color:#38bdf8; width:20px; height:20px;"></i> Presensi Siswa Kelas
                </h3>
                <div id="modalSubtitle" style="font-size:0.83rem; color:#94a3b8; margin-top:4px;"></div>
            </div>
            <button onclick="closeAbsenModal()" style="background:rgba(255,255,255,0.12); border:none; color:white; border-radius:50%; width:32px; height:32px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.2s;" onhover="this.style.background='rgba(255,255,255,0.25)'">
                <i data-lucide="x" style="width:18px; height:18px;"></i>
            </button>
        </div>

        <!-- Modal Stats Bar (clickable to filter category) -->
        <div style="padding:14px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
            <div onclick="filterModalCategory('hadir')" id="statCardHadir" class="stat-card-btn" style="background:#dcfce7; border:1.5px solid #86efac; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#15803d; text-transform:uppercase;">Hadir</div>
                <div id="statCountHadir" style="font-size:1.25rem; font-weight:850; color:#166534; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('izin')" id="statCardIzin" class="stat-card-btn" style="background:#dbeafe; border:1.5px solid #93c5fd; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#1d4ed8; text-transform:uppercase;">Izin</div>
                <div id="statCountIzin" style="font-size:1.25rem; font-weight:850; color:#1e40af; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('sakit')" id="statCardSakit" class="stat-card-btn" style="background:#fef3c7; border:1.5px solid #fde047; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#b45309; text-transform:uppercase;">Sakit</div>
                <div id="statCountSakit" style="font-size:1.25rem; font-weight:850; color:#854d0e; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('alpa')" id="statCardAlpa" class="stat-card-btn" style="background:#fee2e2; border:1.5px solid #fca5a5; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#b91c1c; text-transform:uppercase;">Alpa</div>
                <div id="statCountAlpa" style="font-size:1.25rem; font-weight:850; color:#991b1b; margin-top:2px;">0</div>
            </div>
        </div>

        <!-- Filter Controls & Search -->
        <div style="padding:12px 24px; background:#fff; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;" id="filterTabButtons">
                <button onclick="filterModalCategory('all')" id="btnTabAll" class="modal-tab-btn active">Semua (<span id="countAll">0</span>)</button>
                <button onclick="filterModalCategory('hadir')" id="btnTabHadir" class="modal-tab-btn">Hadir (<span id="countHadir">0</span>)</button>
                <button onclick="filterModalCategory('izin')" id="btnTabIzin" class="modal-tab-btn">Izin (<span id="countIzin">0</span>)</button>
                <button onclick="filterModalCategory('sakit')" id="btnTabSakit" class="modal-tab-btn">Sakit (<span id="countSakit">0</span>)</button>
                <button onclick="filterModalCategory('alpa')" id="btnTabAlpa" class="modal-tab-btn">Alpa (<span id="countAlpa">0</span>)</button>
            </div>
            <div style="position:relative; width:180px;">
                <input type="text" id="inputSearchSiswa" onkeyup="renderSiswaList()" placeholder="Cari nama..." style="width:100%; padding:6px 10px 6px 28px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.8rem; outline:none;">
                <i data-lucide="search" style="position:absolute; left:8px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#94a3b8;"></i>
            </div>
        </div>

        <!-- Student List Container -->
        <div style="padding:16px 24px; overflow-y:auto; flex:1; background:#f8fafc;">
            <div id="modalSiswaList" style="display:flex; flex-direction:column; gap:8px;"></div>
        </div>
    </div>
</div>

<style>
    .z-main {
        margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
        flex: 1;
        background: var(--z-bg);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: var(--z-gap) var(--z-gap) var(--z-gap) 0;
    }
    .absen-card-btn:hover {
        border-color: #0ea5e9 !important;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15) !important;
        transform: translateY(-1px);
    }
    .stat-card-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .modal-tab-btn {
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .modal-tab-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .modal-tab-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    @keyframes modalPop {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @media (max-width: 768px) {
        .z-main { margin-left: 0; padding: 1.25rem; }
    }
</style>

<script>
let currentAbsenData = null;
let currentFilterCategory = 'all';

function openAbsenModal(detail, kelas, mapel, tanggal, guru) {
    currentAbsenData = detail;
    currentFilterCategory = 'all';
    
    document.getElementById('modalTitle').innerHTML = '<i data-lucide="users" style="color:#38bdf8; width:20px; height:20px;"></i> Detail Presensi - Kelas ' + kelas;
    document.getElementById('modalSubtitle').innerText = mapel + ' • ' + guru + ' • Tanggal: ' + tanggal;
    
    // Set counts
    const hCount = detail.hadir ? detail.hadir.length : 0;
    const iCount = detail.izin ? detail.izin.length : 0;
    const sCount = detail.sakit ? detail.sakit.length : 0;
    const aCount = detail.alpa ? detail.alpa.length : 0;
    const totalCount = detail.total || (hCount + iCount + sCount + aCount);

    document.getElementById('statCountHadir').innerText = hCount;
    document.getElementById('statCountIzin').innerText = iCount;
    document.getElementById('statCountSakit').innerText = sCount;
    document.getElementById('statCountAlpa').innerText = aCount;

    document.getElementById('countAll').innerText = totalCount;
    document.getElementById('countHadir').innerText = hCount;
    document.getElementById('countIzin').innerText = iCount;
    document.getElementById('countSakit').innerText = sCount;
    document.getElementById('countAlpa').innerText = aCount;

    document.getElementById('inputSearchSiswa').value = '';
    
    // If there are non-hadir students (e.g. izin or alpa), highlight tab if user prefers, but default to 'all'
    filterModalCategory('all');

    const modal = document.getElementById('modalDetailAbsen');
    modal.style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function closeAbsenModal() {
    document.getElementById('modalDetailAbsen').style.display = 'none';
}

function filterModalCategory(cat) {
    currentFilterCategory = cat;

    // Update active tab buttons
    const btnIds = {
        'all': 'btnTabAll',
        'hadir': 'btnTabHadir',
        'izin': 'btnTabIzin',
        'sakit': 'btnTabSakit',
        'alpa': 'btnTabAlpa'
    };

    Object.keys(btnIds).forEach(k => {
        const el = document.getElementById(btnIds[k]);
        if (el) {
            if (k === cat) el.classList.add('active');
            else el.classList.remove('active');
        }
    });

    renderSiswaList();
}

function renderSiswaList() {
    if (!currentAbsenData) return;
    const listContainer = document.getElementById('modalSiswaList');
    const searchQuery = document.getElementById('inputSearchSiswa').value.toLowerCase().trim();

    let items = [];

    if (currentFilterCategory === 'all') {
        if (currentAbsenData.hadir) items = items.concat(currentAbsenData.hadir.map(x => ({...x, category: 'hadir'})));
        if (currentAbsenData.izin) items = items.concat(currentAbsenData.izin.map(x => ({...x, category: 'izin'})));
        if (currentAbsenData.sakit) items = items.concat(currentAbsenData.sakit.map(x => ({...x, category: 'sakit'})));
        if (currentAbsenData.alpa) items = items.concat(currentAbsenData.alpa.map(x => ({...x, category: 'alpa'})));
    } else {
        const catArr = currentAbsenData[currentFilterCategory] || [];
        items = catArr.map(x => ({...x, category: currentFilterCategory}));
    }

    // Filter by search query
    if (searchQuery) {
        items = items.filter(i => i.nama.toLowerCase().includes(searchQuery));
    }

    if (items.length === 0) {
        listContainer.innerHTML = `
            <div style="text-align:center; padding:30px; background:white; border-radius:12px; border:1px dashed #cbd5e1; color:#94a3b8;">
                <i data-lucide="user-x" style="width:32px; height:32px; margin-bottom:6px; color:#cbd5e1;"></i>
                <div style="font-weight:700; color:#64748b;">Tidak ada data siswa</div>
                <div style="font-size:0.8rem; color:#94a3b8;">Tidak ada siswa pada kategori ini.</div>
            </div>
        `;
        if (window.lucide) lucide.createIcons();
        return;
    }

    const badgeConfig = {
        'hadir': { bg: '#dcfce7', color: '#15803d', border: '#86efac', label: 'Hadir', icon: 'check-circle' },
        'izin':  { bg: '#dbeafe', color: '#1d4ed8', border: '#93c5fd', label: 'Izin', icon: 'file-text' },
        'sakit': { bg: '#fef3c7', color: '#b45309', border: '#fde047', label: 'Sakit', icon: 'alert-circle' },
        'alpa':  { bg: '#fee2e2', color: '#b91c1c', border: '#fca5a5', label: 'Alpa', icon: 'x-circle' }
    };

    let html = '';
    items.forEach((item, index) => {
        const cfg = badgeConfig[item.category] || badgeConfig['alpa'];
        html += `
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:28px; height:28px; border-radius:50%; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.75rem;">
                        ${index + 1}
                    </div>
                    <div>
                        <div style="font-weight:700; color:#0f172a; font-size:0.9rem;">${escapeHtml(item.nama)}</div>
                    </div>
                </div>
                <span style="background:${cfg.bg}; color:${cfg.color}; border:1px solid ${cfg.border}; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
                    <i data-lucide="${cfg.icon}" style="width:12px; height:12px;"></i> ${cfg.label}
                </span>
            </div>
        `;
    });

    listContainer.innerHTML = html;
    if (window.lucide) lucide.createIcons();
}

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Close modal when clicking background overlay
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalDetailAbsen');
    if (e.target === modal) {
        closeAbsenModal();
    }
});
</script>


