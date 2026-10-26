<style>
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #f1f5f9;
    }
    
    .komparasi-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .header-section {
        text-align: center;
        margin-bottom: 40px;
    }

    .header-section h1 {
        font-size: 32px;
        font-weight: 800;
        background: linear-gradient(to right, #38bdf8, #818cf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .header-section p {
        color: #94a3b8;
        font-size: 16px;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover, .stat-card.active {
        transform: translateY(-5px);
    }

    .stat-card.dapat-lagi { box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.2); }
    .stat-card.dapat-lagi:hover, .stat-card.dapat-lagi.active { border-color: #10b981; }
    
    .stat-card.tidak-dapat { box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.2); }
    .stat-card.tidak-dapat:hover, .stat-card.tidak-dapat.active { border-color: #ef4444; }
    
    .stat-card.baru-dapat { box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.2); }
    .stat-card.baru-dapat:hover, .stat-card.baru-dapat.active { border-color: #3b82f6; }

    .stat-title {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .stat-card.dapat-lagi .stat-title { color: #34d399; }
    .stat-card.tidak-dapat .stat-title { color: #f87171; }
    .stat-card.baru-dapat .stat-title { color: #60a5fa; }

    .stat-number {
        font-size: 48px;
        font-weight: 800;
        color: #fff;
    }

    .table-section {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        overflow: hidden;
        display: none;
    }

    .table-section.active {
        display: block;
        animation: fadeIn 0.4s ease forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .table-header h2 {
        margin: 0;
        font-size: 20px;
        color: #fff;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th {
        background: rgba(0, 0, 0, 0.2);
        padding: 16px 24px;
        font-size: 13px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 16px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 15px;
        color: #e2e8f0;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    .empty-state {
        padding: 40px;
        text-align: center;
        color: #94a3b8;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(255,255,255,0.1);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 30px;
        transition: 0.2s;
    }
    .btn-back:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .komparasi-container { margin: 20px auto; }
        .header-section h1 { font-size: 24px; }
        .stats-container { grid-template-columns: 1fr; gap: 15px; }
        .stat-number { font-size: 36px; }
        th, td { padding: 12px 15px; font-size: 13px; }
        .table-header h2 { font-size: 16px; }
    }
</style>

<div class="komparasi-container">
    <a href="/pip" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>

    <div class="header-section">
        <h1>Komparasi Data PIP</h1>
        <p>Analisis perbandingan penerima Program Indonesia Pintar Tahun 2025 vs 2026</p>
    </div>

    <div class="stats-container">
        <div class="stat-card dapat-lagi active" onclick="showTab('dapatLagi')">
            <div class="stat-title">Dapat Lagi</div>
            <div class="stat-number"><?php echo count($dapatLagi); ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Penerima Berlanjut</div>
        </div>
        <div class="stat-card tidak-dapat" onclick="showTab('tidakDapat')">
            <div class="stat-title">Terputus</div>
            <div class="stat-number"><?php echo count($tidakDapat); ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Tidak Dapat Tahun Ini</div>
        </div>
        <div class="stat-card baru-dapat" onclick="showTab('baruDapat')">
            <div class="stat-title">Baru Dapat</div>
            <div class="stat-number"><?php echo count($baruDapat); ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Penerima Baru 2026</div>
        </div>
    </div>

    <!-- Dapat Lagi Tab -->
    <div id="tab-dapatLagi" class="table-section active">
        <div class="table-header">
            <h2>Daftar Siswa Yang Kembali Menerima PIP</h2>
        </div>
        <div class="table-container">
            <?php if (count($dapatLagi) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="30%">NISN</th>
                        <th width="60%">Nama Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dapatLagi as $index => $s): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['NISN']); ?></td>
                        <td><strong><?php echo htmlspecialchars($s['NAMA_SISWA'] ?? $s['NAMA'] ?? '-'); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">Tidak ada data siswa dalam kategori ini.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tidak Dapat Tab -->
    <div id="tab-tidakDapat" class="table-section">
        <div class="table-header">
            <h2>Daftar Siswa Yang Terputus / Lulus</h2>
        </div>
        <div class="table-container">
            <?php if (count($tidakDapat) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="30%">NISN</th>
                        <th width="60%">Nama Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tidakDapat as $index => $s): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['NISN']); ?></td>
                        <td><strong><?php echo htmlspecialchars($s['NAMA_SISWA'] ?? $s['NAMA'] ?? '-'); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">Tidak ada data siswa dalam kategori ini.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Baru Dapat Tab -->
    <div id="tab-baruDapat" class="table-section">
        <div class="table-header">
            <h2>Daftar Siswa Penerima Baru</h2>
        </div>
        <div class="table-container">
            <?php if (count($baruDapat) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="30%">NISN</th>
                        <th width="60%">Nama Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($baruDapat as $index => $s): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['NISN']); ?></td>
                        <td><strong><?php echo htmlspecialchars($s['NAMA_SISWA'] ?? $s['NAMA'] ?? '-'); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">Tidak ada data siswa dalam kategori ini.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function showTab(tabId) {
        // Hide all tables
        document.querySelectorAll('.table-section').forEach(el => el.classList.remove('active'));
        // Show selected table
        document.getElementById('tab-' + tabId).classList.add('active');
        
        // Remove active class from all cards
        document.querySelectorAll('.stat-card').forEach(el => el.classList.remove('active'));
        
        // Add active class to clicked card
        if(tabId === 'dapatLagi') {
            document.querySelector('.stat-card.dapat-lagi').classList.add('active');
        } else if(tabId === 'tidakDapat') {
            document.querySelector('.stat-card.tidak-dapat').classList.add('active');
        } else if(tabId === 'baruDapat') {
            document.querySelector('.stat-card.baru-dapat').classList.add('active');
        }
    }
</script>
