<?php
$pageTitle = 'Ruang Ujian CBT: ' . htmlspecialchars($ujianInfo['nama_ujian']);
$durasiDetik = ($ujianInfo['durasi_menit'] ?? 90) * 60;
$logoUrl = \App\Core\Branding::getInstitusiLogo();

$cbtSettings = $cbtSettings ?? [];
$isAntiCheat = ($cbtSettings['anti_cheat_mode'] ?? '1') === '1';
$forceFullscreen = ($cbtSettings['force_fullscreen'] ?? '1') === '1';
$blockTab = ($cbtSettings['block_tab_switch'] ?? '1') === '1';
$maxViolations = (int)($cbtSettings['max_tab_violations'] ?? 3);
$actionViolation = $cbtSettings['action_on_violation'] ?? 'submit';
$disableRightClick = ($cbtSettings['disable_right_click'] ?? '1') === '1';
$disableCopyPaste = ($cbtSettings['disable_copy_paste'] ?? '1') === '1';
$soundAlarm = ($cbtSettings['sound_alarm_on_violation'] ?? '1') === '1';
$blockBackButton = ($cbtSettings['block_back_button'] ?? '1') === '1';
$watermark = ($cbtSettings['watermark_student_nis'] ?? '1') === '1';
$alarmSoundUrl = base_url('assets/sounds/alarm-danger.mp3');
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

        @keyframes pulseBorder {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 22px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        @keyframes shakeIcon {
            0%, 100% { transform: scale(1) rotate(0deg); }
            20% { transform: scale(1.15) rotate(-10deg); }
            40% { transform: scale(1.15) rotate(10deg); }
            60% { transform: scale(1.1) rotate(-6deg); }
            80% { transform: scale(1.1) rotate(6deg); }
        }
        .cbt-watermark-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 5;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-content: space-around;
            overflow: hidden;
            opacity: 0.045;
            user-select: none;
        }
        .cbt-watermark-overlay span {
            font-size: 1.15rem;
            font-weight: 800;
            transform: rotate(-25deg);
            padding: 28px;
            color: #000000;
            white-space: nowrap;
        }

        @media (max-width: 960px) {
            .cbt-workspace {
                grid-template-columns: 1fr;
            }
        }

        /* Interaktif Tarik Garis Menjodohkan (Curved Colored Bezier Lines) */
        .matching-wrapper {
            margin-top: 14px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05);
        }

        .matching-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .matching-hint {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #0369a1;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 6px 14px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .matching-board {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 80px;
            min-height: 260px;
            padding: 10px 0;
        }

        .matching-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
            overflow: visible;
        }

        .matching-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
            z-index: 3;
        }

        .matching-card {
            position: relative;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 52px;
        }

        .matching-card:hover {
            border-color: #94a3b8;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transform: translateY(-1px);
        }

        .matching-card.selected-source {
            border-color: #2563eb !important;
            background: #eff6ff !important;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.18) !important;
            transform: scale(1.02);
        }

        .connector-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #94a3b8;
            border: 2.5px solid #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.25);
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.25s ease;
        }

        .matching-left .connector-dot {
            right: -8px;
        }

        .matching-right .connector-dot {
            left: -8px;
        }

        @keyframes pulseDot {
            0%, 100% { transform: translateY(-50%) scale(1.1); box-shadow: 0 0 0 0 rgba(37,99,235,0.5); }
            50% { transform: translateY(-50%) scale(1.35); box-shadow: 0 0 0 8px rgba(37,99,235,0); }
        }

        .matching-card.selected-source .connector-dot {
            animation: pulseDot 1.4s infinite;
        }

        @media (max-width: 640px) {
            .matching-board {
                gap: 40px;
            }
            .matching-card {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

    <?php if ($watermark): ?>
    <!-- Watermark Identitas Siswa -->
    <div class="cbt-watermark-overlay">
        <?php for ($w = 0; $w < 24; $w++): ?>
            <span><?php echo htmlspecialchars(($pesertaInfo['nama_siswa'] ?? '') . ' • ' . ($pesertaInfo['nis'] ?? '')); ?></span>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <!-- Audio Element Danger Alarm Meme -->
    <audio id="cbtAlarmAudio" src="<?php echo $alarmSoundUrl; ?>" preload="auto" loop></audio>

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
            <div id="saveIndicator" style="font-size: 0.78rem; font-weight: 700; color: #16a34a; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 6px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 7px; transition: opacity 0.3s; opacity: 0;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                <span id="saveIndicatorText">Tersimpan Otomatis</span>
            </div>

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

    <!-- Modal Peringatan Pelanggaran Keluar Tab (Danger Alarm Meme) -->
    <div id="modalViolation" class="cbt-modal-backdrop" style="z-index: 99999; background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(8px);">
        <div class="cbt-modal-box" style="border: 3px solid #ef4444; box-shadow: 0 25px 60px rgba(239, 68, 68, 0.45); text-align: center; max-width: 500px; animation: pulseBorder 1.5s infinite;">
            
            <!-- Pulsing Hazard Icon -->
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; animation: shakeIcon 0.8s infinite; box-shadow: 0 8px 24px rgba(220,38,38,0.25);">
                <i data-lucide="alert-triangle" style="width: 46px; height: 46px;"></i>
            </div>

            <div style="display: inline-block; background: #dc2626; color: white; font-weight: 900; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; padding: 5px 16px; border-radius: 99px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(220,38,38,0.3);">
                🚨 PERINGATAN PELANGGARAN CBT!
            </div>

            <h3 id="violationTitle" style="font-size: 1.4rem; font-weight: 900; color: #991b1b; margin-bottom: 8px;">
                Terdeteksi Keluar dari Lembar Ujian!
            </h3>

            <p id="violationDesc" style="font-size: 0.92rem; color: #475569; line-height: 1.5; margin-bottom: 1.25rem;">
                Anda terdeteksi berpindah tab browser, membuka aplikasi lain, atau meninggalkan layar ujian. Tindakan ini telah dicatat oleh sistem pengawas!
            </p>

            <!-- Violation Badge Progress -->
            <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px; margin-bottom: 1.5rem;">
                <div style="font-size: 0.88rem; font-weight: 800; color: #b91c1c;">
                    Pelanggaran Ke: <strong id="lblViolationCount" style="font-size: 1.35rem; color: #dc2626;">1</strong> dari Maksimal <strong><?php echo $maxViolations; ?>x</strong>
                </div>
                <div style="font-size: 0.78rem; color: #7f1d1d; margin-top: 6px; font-weight: 600;">
                    Jika mencapai <?php echo $maxViolations; ?>x pelanggaran, sistem akan otomatis <strong><?php echo $actionViolation === 'submit' ? 'MENGUMPULKAN UJIAN ANDA' : 'MENGUNCI LAYAR UJIAN'; ?></strong>!
                </div>
            </div>

            <button type="button" id="btnAcknowledgeViolation" onclick="closeViolationModal()" class="btn-cbt" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; width: 100%; justify-content: center; padding: 12px; font-size: 0.95rem; font-weight: 800; border-radius: 12px; box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4); border: none; cursor: pointer;">
                <i data-lucide="volume-x" style="width: 18px; height: 18px;"></i> Matikan Alarm & Lanjutkan Ujian
            </button>
        </div>
    </div>

    <!-- Modal Sesi Terkunci Permanen (Must be unlocked by proctor) -->
    <div id="modalLocked" class="cbt-modal-backdrop" style="z-index: 999999; background: #0f172a;">
        <div class="cbt-modal-box" style="border: 2px solid #ef4444; background: #1e293b; color: white; text-align: center; max-width: 500px; padding: 2.5rem 2rem;">
            <div style="width: 84px; height: 84px; border-radius: 50%; background: #450a0a; color: #ef4444; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #ef4444;">
                <i data-lucide="lock" style="width: 46px; height: 46px;"></i>
            </div>
            <h2 style="font-size: 1.6rem; font-weight: 900; color: #f87171; margin-bottom: 10px;">SESI UJIAN TERKUNCI!</h2>
            <p style="font-size: 0.94rem; color: #cbd5e1; line-height: 1.6; margin-bottom: 1.5rem;">
                Anda telah melampaui batas toleransi pelanggaran keluar tab (<strong><?php echo $maxViolations; ?>x</strong>).<br>
                Sesi ujian Anda telah dibekukan secara otomatis. Harap segera melapor kepada <strong>Pengawas / Proktor Ruangan</strong> untuk membuka kunci pengerjaan Anda.
            </p>
            <div style="background: #0f172a; padding: 14px; border-radius: 12px; border: 1px solid #334155; font-size: 0.85rem; color: #94a3b8;">
                ID Peserta: <strong style="color: #38bdf8;"><?php echo htmlspecialchars($pesertaInfo['nis'] ?? '-'); ?></strong> — <strong style="color: #f1f5f9;"><?php echo htmlspecialchars($pesertaInfo['nama_siswa'] ?? ''); ?></strong>
            </div>
        </div>
    </div>

    <script>
        const soalList = <?php echo json_encode($soalList); ?>;
        const totalSoal = soalList.length;
        const ujianId = <?php echo (int)$ujianInfo['id']; ?>;
        const pesertaId = <?php echo (int)($pesertaInfo['id'] ?? 0); ?>;
        const serverSavedData = <?php echo json_encode($savedData ?? []); ?>;

        const storageKeyAns = `cbt_ans_${ujianId}_${pesertaId}`;
        const storageKeyRagu = `cbt_ragu_${ujianId}_${pesertaId}`;
        const storageKeyIdx = `cbt_idx_${ujianId}_${pesertaId}`;

        let currentIndex = 0;
        let userAnswers = {}; // { index: answerValue }
        let raguState = {};   // { index: true/false }
        let timeLeft = <?php echo (int)$durasiDetik; ?>;
        let autoSaveTimer = null;

        // Palet warna garis melengkung untuk setiap pasangan soal menjodohkan
        const MATCH_COLORS = [
            { border: '#3b82f6', bg: '#eff6ff', line: '#2563eb', text: '#1d4ed8', name: 'Biru' },
            { border: '#10b981', bg: '#ecfdf5', line: '#059669', text: '#047857', name: 'Hijau' },
            { border: '#8b5cf6', bg: '#f5f3ff', line: '#7c3aed', text: '#6d28d9', name: 'Ungu' },
            { border: '#f59e0b', bg: '#fffbeb', line: '#d97706', text: '#b45309', name: 'Oranye' },
            { border: '#ec4899', bg: '#fdf2f8', line: '#db2777', text: '#be185d', name: 'Pink' },
            { border: '#06b6d4', bg: '#ecfeff', line: '#0891b2', text: '#0e7490', name: 'Cyan' },
            { border: '#f43f5e', bg: '#fff1f2', line: '#e11d48', text: '#be123c', name: 'Merah' },
            { border: '#14b8a6', bg: '#f0fdfa', line: '#0d9488', text: '#0f766e', name: 'Teal' }
        ];

        function initCbt() {
            // 1. Pulihkan data tersimpan dari Cloud Server (jika ada)
            if (serverSavedData && typeof serverSavedData === 'object') {
                if (serverSavedData.answers_by_index) {
                    userAnswers = { ...serverSavedData.answers_by_index };
                } else if (serverSavedData.answers) {
                    // Map soal_id ke index urutan
                    soalList.forEach((s, idx) => {
                        if (serverSavedData.answers[s.id] !== undefined && serverSavedData.answers[s.id] !== null) {
                            userAnswers[idx] = serverSavedData.answers[s.id];
                        }
                    });
                }
                if (serverSavedData.ragu) {
                    raguState = { ...serverSavedData.ragu };
                }
                if (serverSavedData.last_index !== undefined) {
                    const lIdx = parseInt(serverSavedData.last_index);
                    if (!isNaN(lIdx) && lIdx >= 0 && lIdx < totalSoal) {
                        currentIndex = lIdx;
                    }
                }
            }

            // 2. Pulihkan dari LocalStorage (jika lebih baru atau server baru dibuka kembali)
            try {
                const localAns = localStorage.getItem(storageKeyAns);
                if (localAns) {
                    const parsed = JSON.parse(localAns);
                    if (parsed && typeof parsed === 'object') {
                        userAnswers = { ...userAnswers, ...parsed };
                    }
                }
                const localRagu = localStorage.getItem(storageKeyRagu);
                if (localRagu) {
                    const parsedR = JSON.parse(localRagu);
                    if (parsedR && typeof parsedR === 'object') {
                        raguState = { ...raguState, ...parsedR };
                    }
                }
                const localIdx = localStorage.getItem(storageKeyIdx);
                if (localIdx !== null) {
                    const pIdx = parseInt(localIdx);
                    if (!isNaN(pIdx) && pIdx >= 0 && pIdx < totalSoal) {
                        currentIndex = pIdx;
                    }
                }
            } catch (e) {}

            renderPalette();
            loadQuestion(currentIndex);
            startTimer();
            lucide.createIcons();
            showSaveIndicator('Sesi Siap', false);
        }

        // Simpan jawaban siswa secara otomatis (LocalStorage + Cloud Sync)
        function saveCurrentAnswers(syncCloud = true) {
            // 1. Simpan seketika di LocalStorage HP / Laptop siswa
            try {
                localStorage.setItem(storageKeyAns, JSON.stringify(userAnswers));
                localStorage.setItem(storageKeyRagu, JSON.stringify(raguState));
                localStorage.setItem(storageKeyIdx, String(currentIndex));
            } catch(e) {}

            // 2. Sinkronisasi background ke Database Server
            if (syncCloud && pesertaId > 0) {
                showSaveIndicator('Menyimpan...', true);
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    const payload = {
                        answers_by_index: userAnswers,
                        answers: {},
                        ragu: raguState,
                        last_index: currentIndex
                    };
                    for (let i = 0; i < totalSoal; i++) {
                        const sId = soalList[i].id;
                        payload.answers[sId] = userAnswers[i] !== undefined ? userAnswers[i] : '';
                    }

                    const formData = new FormData();
                    formData.append('ujian_id', ujianId);
                    formData.append('peserta_id', pesertaId);
                    formData.append('jawaban_payload', JSON.stringify(payload));

                    fetch('<?php echo base_url("cbt/kerjakan/simpan-jawaban"); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.status === 'success') {
                            showSaveIndicator('Tersimpan di Cloud ✓', false);
                        } else {
                            showSaveIndicator('Tersimpan Lokal', false);
                        }
                    })
                    .catch(() => {
                        showSaveIndicator('Tersimpan Lokal', false);
                    });
                }, 400);
            }
        }

        function showSaveIndicator(text, isSaving) {
            const el = document.getElementById('saveIndicator');
            const txtEl = document.getElementById('saveIndicatorText');
            if (el && txtEl) {
                txtEl.textContent = text;
                el.style.opacity = '1';
                if (isSaving) {
                    el.style.color = '#0284c7';
                    el.style.background = '#f0f9ff';
                    el.style.borderColor = '#bae6fd';
                } else {
                    el.style.color = '#16a34a';
                    el.style.background = '#f0fdf4';
                    el.style.borderColor = '#bbf7d0';
                }
            }
        }

        function loadQuestion(idx) {
            if (idx < 0 || idx >= totalSoal) return;
            currentIndex = idx;
            saveCurrentAnswers(false); // Simpan nomor soal terakhir yang dibuka

            const q = soalList[idx];
            document.getElementById('currentQBadge').textContent = `Soal Nomor ${idx + 1} dari ${totalSoal}`;
            
            // Render Teks Pertanyaan (Mendukung HTML Rich Text, Underline, dan Diagram SVG)
            const qEl = document.getElementById('questionText');
            if (q.pertanyaan.includes('<div') || q.pertanyaan.includes('<p') || q.pertanyaan.includes('<svg')) {
                qEl.innerHTML = q.pertanyaan;
            } else {
                qEl.innerHTML = q.pertanyaan.replace(/\n/g, '<br>');
            }

            const container = document.getElementById('optionsContainer');
            container.innerHTML = '';

            const qType = q.tipe_soal || 'sec_pg';
            
            if (qType === 'sec_pgk') {
                // MODEL 2: Pilihan Ganda Kompleks (Multi-Select Checkboxes)
                let curAns = [];
                if (Array.isArray(userAnswers[idx])) {
                    curAns = userAnswers[idx];
                } else if (typeof userAnswers[idx] === 'string' && userAnswers[idx]) {
                    curAns = userAnswers[idx].split(',').map(s => s.trim().toUpperCase());
                }

                let kData = {};
                try { kData = JSON.parse(q.konten_tambahan || '{}'); } catch(e) {}
                const pgkOptions = [];
                if (kData.opsi && Array.isArray(kData.opsi)) {
                    kData.opsi.forEach((o, i) => {
                        const letter = ['A','B','C','D','E','F'][i];
                        pgkOptions.push({ key: letter, text: o.teks });
                    });
                } else {
                    ['A','B','C','D','E'].forEach(k => {
                        if (q['opsi_' + k.toLowerCase()]) {
                            pgkOptions.push({ key: k, text: q['opsi_' + k.toLowerCase()] });
                        }
                    });
                }

                pgkOptions.forEach(opt => {
                    const isChecked = curAns.includes(opt.key);
                    const item = document.createElement('div');
                    item.className = 'opt-label' + (isChecked ? ' selected' : '');
                    item.style.cursor = 'pointer';
                    item.onclick = () => {
                        let updated = [...curAns];
                        if (updated.includes(opt.key)) {
                            updated = updated.filter(x => x !== opt.key);
                        } else {
                            updated.push(opt.key);
                        }
                        updated.sort();
                        userAnswers[idx] = updated.join(', ');
                        loadQuestion(idx);
                        renderPalette();
                        saveCurrentAnswers(true);
                    };

                    item.innerHTML = `
                        <div class="opt-key" style="border-radius:6px; background:${isChecked ? '#0284c7' : '#f1f5f9'}; color:${isChecked ? '#fff' : '#0f172a'};">${isChecked ? '✓' : opt.key}</div>
                        <div class="opt-text" style="flex:1;">${opt.text}</div>
                    `;
                    container.appendChild(item);
                });

            } else if (qType === 'sec_bs') {
                // MODEL 3: Tabel Pernyataan Benar / Salah
                let kData = {};
                try { kData = JSON.parse(q.konten_tambahan || '{}'); } catch(e) {}
                const butir = kData.butir || [];
                let curAns = {};
                try {
                    if (typeof userAnswers[idx] === 'object' && userAnswers[idx] !== null) {
                        curAns = userAnswers[idx];
                    } else if (typeof userAnswers[idx] === 'string' && userAnswers[idx]) {
                        curAns = JSON.parse(userAnswers[idx]);
                    }
                } catch(e) {}
                if (typeof curAns !== 'object' || curAns === null) curAns = {};

                const table = document.createElement('table');
                table.style.cssText = 'width:100%; border-collapse:collapse; margin-top:12px; font-size:0.92rem; background:#ffffff; border-radius:12px; overflow:hidden; border:1.5px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05);';
                table.innerHTML = `
                    <thead>
                        <tr style="background:#f8fafc; color:#334155; border-bottom:2px solid #cbd5e1;">
                            <th style="padding:12px 16px; text-align:left; font-weight:800;">Pernyataan Materi</th>
                            <th style="padding:12px 14px; text-align:center; width:95px; font-weight:800; border-left:1px solid #e2e8f0;">Benar</th>
                            <th style="padding:12px 14px; text-align:center; width:95px; font-weight:800; border-left:1px solid #e2e8f0;">Salah</th>
                        </tr>
                    </thead>
                    <tbody id="bsTbody_${idx}"></tbody>
                `;
                const tbody = table.querySelector(`#bsTbody_${idx}`);
                butir.forEach((b, bIdx) => {
                    const row = document.createElement('tr');
                    row.style.borderBottom = '1px solid #e2e8f0';
                    const isB = (curAns[bIdx] === 'Benar');
                    const isS = (curAns[bIdx] === 'Salah');
                    row.innerHTML = `
                        <td style="padding:12px 16px; color:#1e293b; font-weight:600; line-height:1.5;">${b.teks}</td>
                        <td style="padding:10px 14px; text-align:center; border-left:1px solid #e2e8f0;">
                            <button type="button" class="btn" style="padding:7px 16px; font-size:0.85rem; font-weight:800; border-radius:8px; cursor:pointer; transition:all 0.2s; background:${isB ? '#16a34a' : '#ffffff'}; color:${isB ? '#ffffff' : '#16a34a'}; border:1.5px solid #86efac; box-shadow:${isB ? '0 2px 4px rgba(22,163,74,0.3)' : 'none'};">
                                ${isB ? '✓ Benar' : 'Benar'}
                            </button>
                        </td>
                        <td style="padding:10px 14px; text-align:center; border-left:1px solid #e2e8f0;">
                            <button type="button" class="btn" style="padding:7px 16px; font-size:0.85rem; font-weight:800; border-radius:8px; cursor:pointer; transition:all 0.2s; background:${isS ? '#dc2626' : '#ffffff'}; color:${isS ? '#ffffff' : '#dc2626'}; border:1.5px solid #fca5a5; box-shadow:${isS ? '0 2px 4px rgba(220,38,38,0.3)' : 'none'};">
                                ${isS ? '✕ Salah' : 'Salah'}
                            </button>
                        </td>
                    `;
                    row.querySelectorAll('button')[0].onclick = () => {
                        curAns[bIdx] = 'Benar';
                        userAnswers[idx] = { ...curAns };
                        loadQuestion(idx);
                        renderPalette();
                        saveCurrentAnswers(true);
                    };
                    row.querySelectorAll('button')[1].onclick = () => {
                        curAns[bIdx] = 'Salah';
                        userAnswers[idx] = { ...curAns };
                        loadQuestion(idx);
                        renderPalette();
                        saveCurrentAnswers(true);
                    };
                    tbody.appendChild(row);
                });
                container.appendChild(table);

            } else if (qType === 'sec_jodoh') {
                // MODEL 4: Menjodohkan (Interaktif Tarik Garis Melengkung Berwarna)
                let kData = {};
                try { kData = JSON.parse(q.konten_tambahan || '{}'); } catch(e) {}
                const pasangan = kData.pasangan || [];
                let curAns = {};
                try {
                    if (typeof userAnswers[idx] === 'object' && userAnswers[idx] !== null) {
                        curAns = userAnswers[idx];
                    } else if (typeof userAnswers[idx] === 'string' && userAnswers[idx]) {
                        curAns = JSON.parse(userAnswers[idx]);
                    }
                } catch(e) {}
                if (typeof curAns !== 'object' || curAns === null) curAns = {};

                const allRespon = Array.from(new Set(pasangan.map(p => p.respon)));
                let selectedLeftIdx = null;

                const wrapper = document.createElement('div');
                wrapper.className = 'matching-wrapper';

                wrapper.innerHTML = `
                    <div class="matching-toolbar">
                        <div class="matching-hint" id="matchingHint_${idx}">
                            <i data-lucide="sparkles" style="width:16px;height:16px;color:#0284c7;"></i>
                            <span id="hintText_${idx}">Klik pernyataan di kolom kiri, lalu klik jawaban yang sesuai di kanan untuk membuat garis melengkung.</span>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="button" class="btn" id="btnResetLines_${idx}" style="font-size:0.75rem; font-weight:700; color:#dc2626; background:#fff1f2; border:1px solid #fecdd3; border-radius:12px; padding:6px 14px; cursor:pointer;">
                                <i data-lucide="rotate-ccw" style="width:13px;height:13px;vertical-align:middle;margin-right:3px;"></i> Reset Garis
                            </button>
                        </div>
                    </div>

                    <div class="matching-board" id="matchingBoard_${idx}">
                        <svg class="matching-svg" id="matchingSvg_${idx}"></svg>
                        
                        <!-- Kolom Kiri: Pernyataan (Premis) -->
                        <div class="matching-col matching-left" id="matchingLeft_${idx}">
                            <div style="font-size:0.72rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#2563eb;"></span> Kolom Pernyataan
                            </div>
                        </div>

                        <!-- Kolom Kanan: Pilihan Jawaban (Respon) -->
                        <div class="matching-col matching-right" id="matchingRight_${idx}">
                            <div style="font-size:0.72rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#059669;"></span> Pilihan Jawaban
                            </div>
                        </div>
                    </div>
                `;

                const board = wrapper.querySelector(`#matchingBoard_${idx}`);
                const svg = wrapper.querySelector(`#matchingSvg_${idx}`);
                const leftCol = wrapper.querySelector(`#matchingLeft_${idx}`);
                const rightCol = wrapper.querySelector(`#matchingRight_${idx}`);
                const hintEl = wrapper.querySelector(`#hintText_${idx}`);

                function updateHint(text) {
                    if (hintEl) hintEl.textContent = text;
                }

                // Render Kartu Kolom Kiri (Pernyataan)
                pasangan.forEach((p, pIdx) => {
                    const lc = document.createElement('div');
                    lc.className = 'matching-card';
                    lc.setAttribute('data-idx', pIdx);
                    const col = MATCH_COLORS[pIdx % MATCH_COLORS.length];

                    lc.innerHTML = `
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; background:${col.line}; color:#fff; font-size:0.78rem; font-weight:800; flex-shrink:0;">${pIdx + 1}</span>
                            <span style="font-size:0.92rem; font-weight:700; color:#1e293b;">${p.premis}</span>
                        </div>
                        <div class="connector-dot"></div>
                    `;

                    lc.onclick = () => {
                        if (selectedLeftIdx === pIdx) {
                            selectedLeftIdx = null;
                            lc.classList.remove('selected-source');
                            updateHint('Klik pernyataan di kiri, lalu klik jawaban yang sesuai di kanan.');
                        } else {
                            selectedLeftIdx = pIdx;
                            leftCol.querySelectorAll('.matching-card').forEach(c => c.classList.remove('selected-source'));
                            lc.classList.add('selected-source');
                            updateHint(`Pernyataan [${pIdx + 1}] aktif (Garis ${col.name}). Silakan klik pilihan jawaban di kolom kanan!`);
                        }
                    };
                    leftCol.appendChild(lc);
                });

                // Render Kartu Kolom Kanan (Pilihan Jawaban)
                allRespon.forEach(r => {
                    const rc = document.createElement('div');
                    rc.className = 'matching-card';
                    rc.setAttribute('data-val', r);

                    rc.innerHTML = `
                        <div class="connector-dot"></div>
                        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:8px;">
                            <span style="font-size:0.92rem; font-weight:700; color:#1e293b;">${r}</span>
                            <div class="match-badge-wrap" style="display:flex; gap:4px; flex-wrap:wrap;"></div>
                        </div>
                    `;

                    rc.onclick = () => {
                        if (selectedLeftIdx !== null) {
                            curAns[selectedLeftIdx] = r;
                            userAnswers[idx] = { ...curAns };

                            leftCol.querySelectorAll('.matching-card').forEach(c => c.classList.remove('selected-source'));
                            const connectedCol = MATCH_COLORS[selectedLeftIdx % MATCH_COLORS.length];
                            selectedLeftIdx = null;

                            drawLines();
                            renderPalette();
                            saveCurrentAnswers(true);
                            updateHint(`Garis lengkung berwarna ${connectedCol.name} berhasil terhubung!`);
                        } else {
                            const connectedLeft = Object.keys(curAns).filter(k => curAns[k] === r);
                            if (connectedLeft.length > 0) {
                                updateHint(`Jawaban '${r}' terhubung dengan Pernyataan ${connectedLeft.map(n => parseInt(n) + 1).join(', ')}.`);
                            } else {
                                updateHint('Silakan klik salah satu pernyataan di sebelah kiri terlebih dahulu.');
                            }
                        }
                    };
                    rightCol.appendChild(rc);
                });

                // Fungsi Menggambar Garis Melengkung (Cubic Bezier S-Curves)
                function drawLines() {
                    svg.innerHTML = '';
                    const boardRect = board.getBoundingClientRect();
                    svg.setAttribute('viewBox', `0 0 ${boardRect.width} ${boardRect.height}`);

                    const leftCards = leftCol.querySelectorAll('.matching-card');
                    const rightCards = rightCol.querySelectorAll('.matching-card');

                    // Reset styling kartu kanan
                    rightCards.forEach(rc => {
                        rc.style.borderColor = '#e2e8f0';
                        rc.style.background = '#f8fafc';
                        const badgeWrap = rc.querySelector('.match-badge-wrap');
                        if (badgeWrap) badgeWrap.innerHTML = '';
                        const dot = rc.querySelector('.connector-dot');
                        if (dot) {
                            dot.style.background = '#94a3b8';
                            dot.style.boxShadow = 'none';
                        }
                    });

                    leftCards.forEach((lc, pIdx) => {
                        const col = MATCH_COLORS[pIdx % MATCH_COLORS.length];
                        const dot = lc.querySelector('.connector-dot');
                        const targetVal = curAns[pIdx];

                        if (targetVal) {
                            lc.style.borderColor = col.border;
                            lc.style.background = col.bg;
                            if (dot) {
                                dot.style.background = col.line;
                                dot.style.boxShadow = `0 0 0 4px ${col.line}35`;
                            }

                            let targetRc = null;
                            rightCards.forEach(rc => {
                                if (rc.getAttribute('data-val') === targetVal) {
                                    targetRc = rc;
                                }
                            });

                            if (targetRc) {
                                targetRc.style.borderColor = col.border;
                                targetRc.style.background = col.bg;
                                const rDot = targetRc.querySelector('.connector-dot');
                                if (rDot) {
                                    rDot.style.background = col.line;
                                    rDot.style.boxShadow = `0 0 0 4px ${col.line}35`;
                                }

                                const badgeWrap = targetRc.querySelector('.match-badge-wrap');
                                if (badgeWrap) {
                                    const badge = document.createElement('span');
                                    badge.style.cssText = `background:${col.line}; color:#ffffff; font-size:0.75rem; font-weight:800; padding:2px 8px; border-radius:12px; display:inline-flex; align-items:center; gap:3px; box-shadow:0 1px 3px rgba(0,0,0,0.15);`;
                                    badge.innerHTML = `<span>[${pIdx + 1}]</span>`;
                                    badgeWrap.appendChild(badge);
                                }

                                const lDotRect = dot.getBoundingClientRect();
                                const rDotRect = rDot.getBoundingClientRect();

                                const x1 = lDotRect.left + lDotRect.width / 2 - boardRect.left;
                                const y1 = lDotRect.top + lDotRect.height / 2 - boardRect.top;
                                const x2 = rDotRect.left + rDotRect.width / 2 - boardRect.left;
                                const y2 = rDotRect.top + rDotRect.height / 2 - boardRect.top;

                                // Garis Bezier Melengkung Halus (S-Curve)
                                const dx = Math.max(35, (x2 - x1) * 0.48);
                                const pathD = `M ${x1} ${y1} C ${x1 + dx} ${y1}, ${x2 - dx} ${y2}, ${x2} ${y2}`;

                                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                                path.setAttribute('d', pathD);
                                path.setAttribute('stroke', col.line);
                                path.setAttribute('stroke-width', '3.5');
                                path.setAttribute('stroke-linecap', 'round');
                                path.setAttribute('fill', 'none');
                                path.style.filter = `drop-shadow(0 2px 6px ${col.line}50)`;
                                svg.appendChild(path);

                                const c1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                                c1.setAttribute('cx', x1); c1.setAttribute('cy', y1); c1.setAttribute('r', '4');
                                c1.setAttribute('fill', col.line);
                                svg.appendChild(c1);

                                const c2 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                                c2.setAttribute('cx', x2); c2.setAttribute('cy', y2); c2.setAttribute('r', '4');
                                c2.setAttribute('fill', col.line);
                                svg.appendChild(c2);
                            }
                        } else {
                            lc.style.borderColor = '#e2e8f0';
                            lc.style.background = '#f8fafc';
                            if (dot) {
                                dot.style.background = '#94a3b8';
                                dot.style.boxShadow = 'none';
                            }
                        }
                    });
                }

                // Tombol Reset Garis
                const btnReset = wrapper.querySelector(`#btnResetLines_${idx}`);
                btnReset.onclick = () => {
                    curAns = {};
                    userAnswers[idx] = {};
                    selectedLeftIdx = null;
                    leftCol.querySelectorAll('.matching-card').forEach(c => c.classList.remove('selected-source'));
                    drawLines();
                    renderPalette();
                    saveCurrentAnswers(true);
                    updateHint('Seluruh garis telah dilepas. Silakan hubungkan kembali.');
                };

                container.appendChild(wrapper);

                // Gambar garis awal setelah DOM dirender
                setTimeout(() => {
                    drawLines();
                    lucide.createIcons();
                }, 40);

                window.refreshMatching = drawLines;

            } else if (qType === 'sec_isian' || qType === 'sec_essay') {
                // MODEL 5: Isian Singkat / Uraian
                const curVal = userAnswers[idx] || '';
                const box = document.createElement('div');
                box.style.cssText = 'background:#ffffff; border:1.5px solid #cbd5e1; border-radius:12px; padding:16px; margin-top:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04);';
                box.innerHTML = `
                    <label style="font-size:0.88rem; font-weight:800; color:#334155; display:block; margin-bottom:10px;">
                        Tuliskan Jawaban Singkat Anda di Bawah Ini:
                    </label>
                    <input type="text" class="input-sm" id="isianInput_${idx}" value="${curVal}" placeholder="Ketik jawaban angka atau kata di sini..." style="width:100%; font-size:1.1rem; font-weight:800; color:#0f172a; padding:12px 16px; border:2px solid #0284c7; border-radius:10px; background:#f0f9ff; outline:none;">
                `;
                const inp = box.querySelector(`#isianInput_${idx}`);
                inp.oninput = () => {
                    userAnswers[idx] = inp.value;
                    renderPalette();
                    saveCurrentAnswers(true);
                };
                container.appendChild(box);

            } else {
                // MODEL 1: Pilihan Ganda Biasa (A, B, C, D, E)
                const opts = ['A', 'B', 'C', 'D', 'E'];
                opts.forEach(k => {
                    let text = q['opsi_' + k.toLowerCase()];
                    if (!text && q.konten_tambahan) {
                        try {
                            const kd = JSON.parse(q.konten_tambahan);
                            if (kd.opsi && kd.opsi[k]) text = kd.opsi[k];
                        } catch(e) {}
                    }
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
            }

            // Update Nav Buttons
            document.getElementById('btnPrev').style.visibility = (idx === 0) ? 'hidden' : 'visible';
            document.getElementById('btnNext').style.display = (idx === totalSoal - 1) ? 'none' : 'inline-flex';
            document.getElementById('btnSubmitModal').style.display = (idx === totalSoal - 1) ? 'inline-flex' : 'none';

            // Update Status Tombol Ragu-Ragu
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
            saveCurrentAnswers(true);
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
            saveCurrentAnswers(true);
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

                let ansTag = '';
                const ans = userAnswers[i];
                if (ans !== undefined && ans !== null && ans !== '') {
                    if (typeof ans === 'object') {
                        if (Object.keys(ans).length > 0) {
                            answeredCount++;
                            btn.classList.add('answered');
                            ansTag = '✓';
                        }
                    } else {
                        const str = String(ans).trim();
                        if (str.length > 0) {
                            answeredCount++;
                            btn.classList.add('answered');
                            ansTag = str.length > 3 ? '✓' : str;
                        }
                    }
                }

                if (raguState[i]) {
                    btn.classList.remove('answered');
                    btn.classList.add('doubt');
                }

                btn.onclick = () => loadQuestion(i);
                btn.innerHTML = `
                    <span>${i + 1}</span>
                    ${ansTag ? `<span class="pal-ans-tag">${ansTag}</span>` : ''}
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
            if (typeof window.refreshMatching === 'function') {
                setTimeout(window.refreshMatching, 50);
            }
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
            let answered = 0;
            for (let i = 0; i < totalSoal; i++) {
                const ans = userAnswers[i];
                if (ans !== undefined && ans !== null && ans !== '') {
                    if (typeof ans === 'object') {
                        if (Object.keys(ans).length > 0) answered++;
                    } else if (String(ans).trim().length > 0) {
                        answered++;
                    }
                }
            }
            let unanswered = totalSoal - answered;
            document.getElementById('modalSummaryText').innerHTML = `
                Anda telah menjawab <strong>${answered}</strong> dari <strong>${totalSoal}</strong> butir soal.<br>
                ${unanswered > 0 ? `<span style="color:#dc2626;font-weight:700;">Masih ada ${unanswered} butir soal yang belum dijawab!</span>` : '<span style="color:#16a34a;font-weight:700;">Seluruh soal telah selesai Anda jawab.</span>'}
            `;

            // Siapkan payload final berdasar ID soal
            const payload = {};
            for (let i = 0; i < totalSoal; i++) {
                const soalId = soalList[i].id;
                payload[soalId] = userAnswers[i] !== undefined ? userAnswers[i] : '';
            }
            document.getElementById('jawabanPayloadInput').value = JSON.stringify(payload);
            document.getElementById('modalConfirm').classList.add('open');
            lucide.createIcons();
        }

        // Hapus cache lokal setelah ujian resmi dikirim
        document.getElementById('formSubmitUjian').onsubmit = function() {
            stopDangerAlarm();
            isExamSubmitted = true;
            try {
                localStorage.removeItem(storageKeyAns);
                localStorage.removeItem(storageKeyRagu);
                localStorage.removeItem(storageKeyIdx);
                localStorage.removeItem(storageKeyViol);
            } catch(e) {}
        };

        function autoSubmit(reason = 'Waktu ujian telah habis!') {
            stopDangerAlarm();
            isExamSubmitted = true;
            alert(reason + ' Jawaban Anda akan otomatis dikumpulkan.');
            const payload = {};
            for (let i = 0; i < totalSoal; i++) {
                payload[soalList[i].id] = userAnswers[i] !== undefined ? userAnswers[i] : '';
            }
            document.getElementById('jawabanPayloadInput').value = JSON.stringify(payload);
            try {
                localStorage.removeItem(storageKeyAns);
                localStorage.removeItem(storageKeyRagu);
                localStorage.removeItem(storageKeyIdx);
                localStorage.removeItem(storageKeyViol);
            } catch(e) {}
            document.getElementById('formSubmitUjian').submit();
        }

        // ─── ANTI-CHEAT & DANGER ALARM SYSTEM (MEME AUDIO) ───────────────────
        const isAntiCheat = <?php echo $isAntiCheat ? 'true' : 'false'; ?>;
        const blockTab = <?php echo $blockTab ? 'true' : 'false'; ?>;
        const blockBackButton = <?php echo $blockBackButton ? 'true' : 'false'; ?>;
        const forceFullscreen = <?php echo $forceFullscreen ? 'true' : 'false'; ?>;
        const disableRightClick = <?php echo $disableRightClick ? 'true' : 'false'; ?>;
        const disableCopyPaste = <?php echo $disableCopyPaste ? 'true' : 'false'; ?>;
        const soundAlarm = <?php echo $soundAlarm ? 'true' : 'false'; ?>;
        const maxViolations = <?php echo $maxViolations; ?>;
        const actionViolation = '<?php echo $actionViolation; ?>';

        const storageKeyViol = `cbt_viol_${ujianId}_${pesertaId}`;
        let violationCount = parseInt(localStorage.getItem(storageKeyViol) || '0', 10);
        let isExamSubmitted = false;
        let isViolationModalOpen = false;
        let lastViolationTime = 0;

        const alarmAudio = document.getElementById('cbtAlarmAudio');
        let audioUnlocked = false;

        function unlockAudio() {
            if (!audioUnlocked && alarmAudio) {
                alarmAudio.play().then(() => {
                    alarmAudio.pause();
                    alarmAudio.currentTime = 0;
                    audioUnlocked = true;
                }).catch(() => {});
            }
        }
        document.addEventListener('click', unlockAudio, { once: true });
        document.addEventListener('keydown', unlockAudio, { once: true });
        document.addEventListener('touchstart', unlockAudio, { once: true });

        function playDangerAlarm() {
            if (!soundAlarm || !alarmAudio) return;
            try {
                alarmAudio.currentTime = 0;
                alarmAudio.play().catch(e => console.log('Audio autoplay note:', e));
            } catch(e) {}
        }

        function stopDangerAlarm() {
            if (!alarmAudio) return;
            try {
                alarmAudio.pause();
                alarmAudio.currentTime = 0;
            } catch(e) {}
        }

        function handleTabViolation(reason = 'Keluar Tab') {
            if (!isAntiCheat || isExamSubmitted) return;

            const now = Date.now();
            if (now - lastViolationTime < 1800) {
                // Debounce duplicate events within 1.8s
                return;
            }
            lastViolationTime = now;

            violationCount++;
            localStorage.setItem(storageKeyViol, violationCount);

            // Putar sound effect meme danger alarm!
            playDangerAlarm();

            // Tampilkan modal peringatan
            showViolationModal(reason);
        }

        function showViolationModal(reason = '') {
            if (isExamSubmitted) return;
            isViolationModalOpen = true;

            const modal = document.getElementById('modalViolation');
            const lblCount = document.getElementById('lblViolationCount');
            const titleEl = document.getElementById('violationTitle');
            const descEl = document.getElementById('violationDesc');

            if (lblCount) lblCount.textContent = violationCount;

            if (reason.includes('Kembali')) {
                if (titleEl) titleEl.textContent = 'Dilarang Menekan Tombol Kembali!';
                if (descEl) descEl.textContent = 'Anda terdeteksi menekan tombol kembali (Back) pada browser atau gawai. Selama ujian berlangsung, dilarang kembali ke menu atau aplikasi lain!';
            } else if (reason.includes('Fullscreen')) {
                if (titleEl) titleEl.textContent = 'Terdeteksi Keluar dari Layar Penuh!';
                if (descEl) descEl.textContent = 'Anda wajib mengerjakan ujian dalam mode layar penuh (Fullscreen) tanpa meminimalkan jendela.';
            } else {
                if (titleEl) titleEl.textContent = 'Terdeteksi Keluar dari Lembar Ujian!';
                if (descEl) descEl.textContent = 'Anda terdeteksi berpindah tab browser, membuka aplikasi lain, atau meninggalkan layar ujian. Tindakan ini telah dicatat oleh sistem pengawas!';
            }

            if (violationCount >= maxViolations) {
                stopDangerAlarm();
                if (actionViolation === 'submit') {
                    autoSubmit(`🚨 BATAS PELANGGARAN TERLAMPAUI (${violationCount}x)!`);
                    return;
                } else {
                    // Kunci sesi ujian
                    modal.classList.remove('open');
                    document.getElementById('modalLocked').classList.add('open');
                    fetch('<?php echo base_url("cbt/kerjakan/pelanggaran"); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `peserta_id=${pesertaId}&status=Terkunci`
                    });
                    if (window.lucide) lucide.createIcons();
                    return;
                }
            }

            modal.classList.add('open');
            if (window.lucide) lucide.createIcons();
        }

        function closeViolationModal() {
            stopDangerAlarm();
            isViolationModalOpen = false;
            document.getElementById('modalViolation').classList.remove('open');

            // Kunci kembali history gawai
            try {
                window.history.pushState({ cbt_session: 'active' }, document.title, window.location.href);
            } catch(e) {}

            if (forceFullscreen && !document.fullscreenElement) {
                requestFullscreenMode();
            }
        }

        function requestFullscreenMode() {
            const el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen().catch(() => {});
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            }
        }

        // ─── BLOKIR TOMBOL KEMBALI GAWAI / BROWSER (ANTI-BACK) ────────────────
        if (isAntiCheat && blockBackButton) {
            try {
                window.history.pushState({ cbt_session: 'active' }, document.title, window.location.href);
            } catch(e) {}

            window.addEventListener('popstate', function(event) {
                if (isExamSubmitted) return;

                // Segera tahan posisi history agar tidak kembali ke APK / halaman sebelumnya
                try {
                    window.history.pushState({ cbt_session: 'active' }, document.title, window.location.href);
                } catch(e) {}

                // Picu pelanggaran & alarm bahaya meme!
                handleTabViolation('Mencoba Menekan Tombol Kembali');
            });

            // Cegah penutupan / reload tab browser tanpa konfirmasi
            window.addEventListener('beforeunload', function(e) {
                if (!isExamSubmitted) {
                    const msg = 'Ujian sedang berlangsung! Anda tidak boleh meninggalkan halaman ini.';
                    e.preventDefault();
                    e.returnValue = msg;
                    return msg;
                }
            });
        }

        // Listener pindah tab & keluar layar
        if (isAntiCheat && blockTab) {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    handleTabViolation('Tab Berpindah');
                } else {
                    // Siswa kembali ke tab ujian
                    if (violationCount > 0 && !isExamSubmitted) {
                        playDangerAlarm();
                        showViolationModal();
                    }
                }
            });

            window.addEventListener('blur', function() {
                if (!isViolationModalOpen && !isExamSubmitted) {
                    handleTabViolation('Fokus Hilang');
                }
            });
        }

        // Blokir klik kanan & inspect element / shortcut
        if (isAntiCheat && disableRightClick) {
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            document.addEventListener('keydown', function(e) {
                // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U, Ctrl+S, Ctrl+P
                if (e.keyCode === 123 ||
                    (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) ||
                    (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode === 80))) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Blokir copy, cut, paste
        if (isAntiCheat && disableCopyPaste) {
            document.addEventListener('copy', function(e) { e.preventDefault(); return false; });
            document.addEventListener('cut', function(e) { e.preventDefault(); return false; });
            document.addEventListener('paste', function(e) { e.preventDefault(); return false; });
        }

        // Listener keluar fullscreen
        if (isAntiCheat && forceFullscreen) {
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement && !isExamSubmitted && !isViolationModalOpen) {
                    handleTabViolation('Keluar Fullscreen');
                }
            });
        }

        window.addEventListener('resize', () => {
            if (typeof window.refreshMatching === 'function') {
                window.refreshMatching();
            }
        });

        window.onload = function() {
            initCbt();
            if (isAntiCheat && violationCount >= maxViolations && actionViolation === 'lock') {
                document.getElementById('modalLocked').classList.add('open');
                if (window.lucide) lucide.createIcons();
            }
        };
    </script>
</body>
</html>

