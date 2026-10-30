<?php
$pageTitle = 'Ruang Ujian CBT: ' . htmlspecialchars($ujianInfo['nama_ujian']);
$durasiDetik = ($ujianInfo['durasi_menit'] ?? 90) * 60;
$logoUrl = \App\Core\Branding::getInstitusiLogo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .cbt-topbar {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .cbt-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cbt-brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 8px;
        }

        .cbt-exam-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .cbt-exam-sub {
            font-size: 0.76rem;
            color: #64748b;
            font-weight: 600;
        }

        .cbt-timer-box {
            background: #1e1b4b;
            color: #ffffff;
            padding: 8px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.25);
        }

        .cbt-timer-val {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #38bdf8;
        }

        .cbt-timer-label {
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Main Workspace Layout */
        .cbt-workspace {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
            padding: 1.5rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Question Area */
        .cbt-main-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(0,0,0,0.02);
            min-height: 520px;
        }

        .q-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 1.5rem;
        }

        .q-badge-num {
            background: #e0e7ff;
            color: #4338ca;
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .font-sizer {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .font-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .font-btn.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .q-text {
            font-size: 1.08rem;
            font-weight: 600;
            line-height: 1.7;
            color: #1e293b;
            margin-bottom: 2rem;
            user-select: text;
        }

        /* Options */
        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 2.5rem;
        }

        .opt-label {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .opt-label:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .opt-label.selected {
            border-color: #4f46e5;
            background: #eef2ff;
        }

        .opt-key {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.92rem;
            flex-shrink: 0;
            transition: 0.15s;
        }

        .opt-label.selected .opt-key {
            background: #4f46e5;
            color: white;
        }

        .opt-text {
            font-size: 0.96rem;
            font-weight: 500;
            color: #1e293b;
            line-height: 1.5;
            margin-top: 3px;
        }

        /* Nav Footer */
        .q-nav-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 1.5rem;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-cbt {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.88rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-prev {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-prev:hover { background: #e2e8f0; }

        .btn-ragu {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .btn-ragu.active {
            background: #f59e0b;
            color: white;
            border-color: #d97706;
        }

        .btn-next {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-next:hover { background: #4338ca; }

        .btn-finish {
            background: #16a34a;
            color: #ffffff;
        }
        .btn-finish:hover { background: #15803d; }

        /* Right Palette Sidebar */
        .cbt-side-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .side-profile-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
            border-radius: 12px;
        }

        .p-name { font-weight: 800; font-size: 0.92rem; color: #0f172a; }
        .p-nis { font-size: 0.74rem; color: #64748b; font-family: 'JetBrains Mono', monospace; }

        .palette-title {
            font-weight: 800;
            font-size: 0.9rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .palette-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            max-height: 380px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .pal-btn {
            aspect-ratio: 1;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            font-weight: 800;
            font-size: 0.9rem;
            color: #475569;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: 0.15s;
            position: relative;
        }

        .pal-btn.current {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .pal-btn.answered {
            background: #16a34a;
            color: white;
            border-color: #16a34a;
        }

        .pal-btn.doubt {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }

        .pal-ans-tag {
            font-size: 0.65rem;
            font-weight: 900;
            line-height: 1;
            margin-top: 2px;
        }

        /* Modal Confirmation */
        .cbt-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .cbt-modal-backdrop.open { display: flex; }

        .cbt-modal-box {
            background: #ffffff;
            border-radius: 20px;
            width: 90%;
            max-width: 480px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            text-align: center;
        }

        @media (max-width: 960px) {
            .cbt-workspace {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Header Topbar -->
    <header class="cbt-topbar">
        <div class="cbt-brand">
            <img src="<?php echo $logoUrl; ?>" alt="Logo">
            <div>
                <div class="cbt-exam-title"><?php echo htmlspecialchars($ujianInfo['nama_ujian']); ?></div>
                <div class="cbt-exam-sub"><?php echo htmlspecialchars($ujianInfo['mapel_nama']); ?> &bull; KKM: <?php echo $ujianInfo['kkm']; ?></div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px;">
            <div class="cbt-timer-box">
                <div>
                    <div class="cbt-timer-label">Sisa Waktu</div>
                    <div class="cbt-timer-val" id="timerDisplay">--:--:--</div>
                </div>
                <i data-lucide="clock" style="width: 22px; height: 22px; color: #38bdf8;"></i>
            </div>
        </div>
    </header>

    <!-- Main Exam Workspace -->
    <main class="cbt-workspace">
        
        <!-- Left: Question & Answers -->
        <div class="cbt-main-card">
            <div>
                <div class="q-header">
                    <div class="q-badge-num">
                        <i data-lucide="file-question" style="width: 16px; height: 16px;"></i>
                        <span id="currentQBadge">Soal Nomor 1</span>
                    </div>

                    <div class="font-sizer">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-right: 4px;">Ukuran Font:</span>
                        <button type="button" class="font-btn" onclick="setFontSize('sm')">A-</button>
                        <button type="button" class="font-btn active" id="fontMid" onclick="setFontSize('md')">A</button>
                        <button type="button" class="font-btn" onclick="setFontSize('lg')">A+</button>
                    </div>
                </div>

                <!-- Question Text -->
                <div class="q-text" id="questionText">
                    Memuat butir pertanyaan...
                </div>

                <!-- Choices A - E -->
                <div class="options-grid" id="optionsContainer">
                    <!-- Populated via Javascript -->
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="q-nav-footer">
                <button type="button" class="btn-cbt btn-prev" id="btnPrev" onclick="navigateQuestion(-1)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Sebelumnya
                </button>

                <button type="button" class="btn-cbt btn-ragu" id="btnRagu" onclick="toggleRagu()">
                    <i data-lucide="help-circle" style="width: 16px; height: 16px;"></i> Ragu-Ragu
                </button>

                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-cbt btn-next" id="btnNext" onclick="navigateQuestion(1)">
                        Selanjutnya <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                    </button>

                    <button type="button" class="btn-cbt btn-finish" id="btnSubmitModal" onclick="openConfirmModal()">
                        <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Selesai Ujian
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Palette & Identity -->
        <aside class="cbt-side-card">
            <div class="side-profile-box">
                <div style="font-size: 0.7rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Peserta Ujian</div>
                <div class="p-name"><?php echo htmlspecialchars($pesertaInfo['nama_siswa'] ?? 'Siswa Uji Coba'); ?></div>
                <div class="p-nis">NIS: <?php echo htmlspecialchars($pesertaInfo['nis'] ?? '-'); ?> &bull; Kelas: <?php echo htmlspecialchars($pesertaInfo['kelas_nama'] ?? 'Semua'); ?></div>
            </div>

            <div>
                <div class="palette-title">
                    <span>Nomor Soal</span>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;" id="answeredCountLabel">0 / <?php echo count($soalList); ?> Terjawab</span>
                </div>

                <div class="palette-grid" id="paletteGrid">
                    <!-- Numbers 1 to N -->
                </div>
            </div>

            <div style="background: #f8fafc; border-radius: 12px; padding: 12px; font-size: 0.72rem; color: #64748b; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background: #16a34a; display: inline-block;"></span>
                    <span>Sudah Dijawab</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background: #f59e0b; display: inline-block;"></span>
                    <span>Ragu-Ragu</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; border: 1.5px solid #cbd5e1; background: #fff; display: inline-block;"></span>
                    <span>Belum Dijawab</span>
                </div>
            </div>
        </aside>

    </main>

    <!-- Modal Konfirmasi Selesai Ujian -->
    <div id="modalConfirm" class="cbt-modal-backdrop">
        <div class="cbt-modal-box">
            <div style="width: 60px; height: 60px; border-radius: 16px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <i data-lucide="check-check" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Kumpulkan Jawaban Ujian?</h3>
            <p style="font-size: 0.9rem; color: #64748b; line-height: 1.5; margin-bottom: 1.5rem;" id="modalSummaryText">
                Pastikan Anda telah memeriksa seluruh jawaban sebelum mengakhiri sesi ujian ini.
            </p>

            <form method="POST" action="<?php echo base_url('cbt/kerjakan/submit'); ?>" id="formSubmitUjian">
                <input type="hidden" name="ujian_id" value="<?php echo $ujianInfo['id']; ?>">
                <input type="hidden" name="peserta_id" value="<?php echo $pesertaInfo['id'] ?? 0; ?>">
                <input type="hidden" name="jawaban_payload" id="jawabanPayloadInput" value="">

                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" onclick="document.getElementById('modalConfirm').classList.remove('open')" class="btn-cbt btn-prev">
                        Periksa Lagi
                    </button>
                    <button type="submit" class="btn-cbt btn-finish" style="background: #4f46e5;">
                        Ya, Selesai & Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const soalList = <?php echo json_encode($soalList); ?>;
        const totalSoal = soalList.length;
        let currentIndex = 0;
        let userAnswers = {}; // { index: 'A' }
        let raguState = {};   // { index: true }
        let timeLeft = <?php echo $durasiDetik; ?>;

        function initCbt() {
            renderPalette();
            loadQuestion(0);
            startTimer();
            lucide.createIcons();
        }

        function loadQuestion(idx) {
            if (idx < 0 || idx >= totalSoal) return;
            currentIndex = idx;

            const q = soalList[idx];
            document.getElementById('currentQBadge').textContent = `Soal Nomor ${idx + 1} dari ${totalSoal}`;
            document.getElementById('questionText').innerHTML = q.pertanyaan.replace(/\n/g, '<br>');

            const container = document.getElementById('optionsContainer');
            container.innerHTML = '';

            const opts = ['A', 'B', 'C', 'D', 'E'];
            opts.forEach(k => {
                const text = q['opsi_' + k.toLowerCase()];
                if (!text) return;

                const isSelected = (userAnswers[idx] === k);
                const item = document.createElement('div');
                item.className = 'opt-label' + (isSelected ? ' selected' : '');
                item.onclick = () => selectOption(idx, k);

                item.innerHTML = `
                    <div class="opt-key">${k}</div>
                    <div class="opt-text">${text}</div>
                `;
                container.appendChild(item);
            });

            // Update Nav Buttons
            document.getElementById('btnPrev').style.visibility = (idx === 0) ? 'hidden' : 'visible';
            document.getElementById('btnNext').style.display = (idx === totalSoal - 1) ? 'none' : 'inline-flex';
            document.getElementById('btnSubmitModal').style.display = (idx === totalSoal - 1) ? 'inline-flex' : 'none';

            // Update Ragu button state
            const btnRagu = document.getElementById('btnRagu');
            if (raguState[idx]) {
                btnRagu.classList.add('active');
            } else {
                btnRagu.classList.remove('active');
            }

            updatePaletteCurrent();
        }

        function selectOption(qIdx, key) {
            userAnswers[qIdx] = key;
            loadQuestion(qIdx);
            renderPalette();
        }

        function toggleRagu() {
            raguState[currentIndex] = !raguState[currentIndex];
            const btnRagu = document.getElementById('btnRagu');
            if (raguState[currentIndex]) {
                btnRagu.classList.add('active');
            } else {
                btnRagu.classList.remove('active');
            }
            renderPalette();
        }

        function navigateQuestion(step) {
            const nextIdx = currentIndex + step;
            if (nextIdx >= 0 && nextIdx < totalSoal) {
                loadQuestion(nextIdx);
            }
        }

        function renderPalette() {
            const grid = document.getElementById('paletteGrid');
            grid.innerHTML = '';

            let answeredCount = 0;

            for (let i = 0; i < totalSoal; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'pal-btn';
                if (i === currentIndex) btn.classList.add('current');

                if (userAnswers[i]) {
                    answeredCount++;
                    btn.classList.add('answered');
                }
                if (raguState[i]) {
                    btn.classList.remove('answered');
                    btn.classList.add('doubt');
                }

                btn.onclick = () => loadQuestion(i);
                btn.innerHTML = `
                    <span>${i + 1}</span>
                    ${userAnswers[i] ? `<span class="pal-ans-tag">${userAnswers[i]}</span>` : ''}
                `;
                grid.appendChild(btn);
            }

            document.getElementById('answeredCountLabel').textContent = `${answeredCount} / ${totalSoal} Terjawab`;
        }

        function updatePaletteCurrent() {
            const btns = document.querySelectorAll('.pal-btn');
            btns.forEach((b, i) => {
                if (i === currentIndex) {
                    b.classList.add('current');
                } else {
                    b.classList.remove('current');
                }
            });
        }

        function setFontSize(size) {
            const textEl = document.getElementById('questionText');
            document.querySelectorAll('.font-btn').forEach(b => b.classList.remove('active'));
            if (size === 'sm') textEl.style.fontSize = '0.92rem';
            if (size === 'md') textEl.style.fontSize = '1.08rem';
            if (size === 'lg') textEl.style.fontSize = '1.25rem';
        }

        function startTimer() {
            const display = document.getElementById('timerDisplay');
            const interval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    autoSubmit();
                    return;
                }
                timeLeft--;

                const hours = Math.floor(timeLeft / 3600);
                const minutes = Math.floor((timeLeft % 3600) / 60);
                const seconds = timeLeft % 60;

                display.textContent = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');
            }, 1000);
        }

        function openConfirmModal() {
            let answered = Object.keys(userAnswers).length;
            let unanswered = totalSoal - answered;
            document.getElementById('modalSummaryText').innerHTML = `
                Anda telah menjawab <strong>${answered}</strong> dari <strong>${totalSoal}</strong> butir soal.<br>
                ${unanswered > 0 ? `<span style="color:#dc2626;font-weight:700;">Masih ada ${unanswered} butir soal yang belum dijawab!</span>` : '<span style="color:#16a34a;font-weight:700;">Seluruh soal telah selesai Anda jawab.</span>'}
            `;

            // Prepare payload
            const payload = {};
            for (let i = 0; i < totalSoal; i++) {
                const soalId = soalList[i].id;
                payload[soalId] = userAnswers[i] || '';
            }
            document.getElementById('jawabanPayloadInput').value = JSON.stringify(payload);
            document.getElementById('modalConfirm').classList.add('open');
            lucide.createIcons();
        }

        function autoSubmit() {
            alert('Waktu ujian telah habis! Jawaban Anda akan otomatis dikumpulkan.');
            const payload = {};
            for (let i = 0; i < totalSoal; i++) {
                payload[soalList[i].id] = userAnswers[i] || '';
            }
            document.getElementById('jawabanPayloadInput').value = JSON.stringify(payload);
            document.getElementById('formSubmitUjian').submit();
        }

        window.onload = initCbt;
    </script>
</body>
</html>

