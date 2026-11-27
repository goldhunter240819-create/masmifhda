<?php
$pageTitle = 'Pengaturan CBT & Keamanan';
$active_menu = 'pengaturan';

$settings = $settings ?? [];
$isAntiCheatActive = ($settings['anti_cheat_mode'] ?? '1') === '1';
$forceFullscreen = ($settings['force_fullscreen'] ?? '1') === '1';
$blockTab = ($settings['block_tab_switch'] ?? '1') === '1';
$maxViolations = (int)($settings['max_tab_violations'] ?? 3);
$actionViolation = $settings['action_on_violation'] ?? 'submit';
$disableRightClick = ($settings['disable_right_click'] ?? '1') === '1';
$disableCopyPaste = ($settings['disable_copy_paste'] ?? '1') === '1';
$lockDevice = ($settings['lock_single_device'] ?? '1') === '1';
$watermark = ($settings['watermark_student_nis'] ?? '1') === '1';
$soundAlarm = ($settings['sound_alarm_on_violation'] ?? '1') === '1';
$blockBackButton = ($settings['block_back_button'] ?? '1') === '1';
$backSpamLockHours = (int)($settings['back_spam_lock_hours'] ?? 10);

$durasiMenit = (int)($settings['default_durasi_menit'] ?? 90);
$defaultKkm = (int)($settings['default_kkm'] ?? 75);
$lockBySchedule = ($settings['lock_by_schedule_time'] ?? '1') === '1';
$acakSoal = ($settings['acak_soal_default'] ?? '1') === '1';
$acakOpsi = ($settings['acak_opsi_default'] ?? '1') === '1';
$showScore = ($settings['show_score_to_student'] ?? '1') === '1';
$showDiscussion = ($settings['show_discussion_to_student'] ?? '0') === '1';
$allowReview = ($settings['allow_student_review'] ?? '1') === '1';
$requireAllAnswered = ($settings['require_all_answered_no_doubt'] ?? '1') === '1';
$minSubmitPercent = (int)($settings['min_submit_percent'] ?? 80);
$navDelaySeconds = (int)($settings['nav_delay_seconds'] ?? 10);
$requireAttendance = ($settings['require_cbt_attendance'] ?? '0') === '1';
$tokenMode = $settings['token_mode'] ?? 'manual';
$tokenInterval = (int)($settings['token_refresh_interval'] ?? 120);

$totalPeserta = $totalPeserta ?? 0;
$pesertaMengerjakan = $pesertaMengerjakan ?? 0;
$pesertaSelesai = $pesertaSelesai ?? 0;
$pesertaTerkunci = $pesertaTerkunci ?? 0;
$listTerkunci = $listTerkunci ?? [];
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Alerts -->
            <?php if (isset($_GET['saved'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(34,197,94,0.1);">
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #22c55e; flex-shrink: 0;"></i>
                <div>
                    <div style="font-size: 0.95rem;">Pengaturan CBT Berhasil Disimpan!</div>
                    <div style="font-size: 0.82rem; font-weight: 500; color: #15803d; margin-top: 2px;">Seluruh parameter keamanan anti-cheat dan aturan pelaksanaan ujian telah diperbarui secara terpusat.</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['unlocked'])): ?>
            <div style="background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(59,130,246,0.1);">
                <i data-lucide="unlock" style="width: 22px; height: 22px; color: #3b82f6; flex-shrink: 0;"></i>
                <div>
                    <div style="font-size: 0.95rem;">Status Siswa Berhasil Dipulihkan!</div>
                    <div style="font-size: 0.82rem; font-weight: 500; color: #2563eb; margin-top: 2px;">Siswa yang sebelumnya terkunci kini dapat melanjutkan ujian kembali.</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['finalized_all'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(34,197,94,0.1);">
                <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #16a34a; flex-shrink: 0;"></i>
                <div>
                    <div style="font-size: 0.95rem;">Berhasil Menyelesaikan <?php echo (int)$_GET['finalized_all']; ?> Sesi Ujian Menggantung!</div>
                    <div style="font-size: 0.82rem; font-weight: 500; color: #15803d; margin-top: 2px;">Seluruh siswa yang masih berstatus Mengerjakan, Terkunci, atau Pelanggaran s/d hari ini telah dinilai dan diubah menjadi Selesai.</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="refresh-cw" style="width: 22px; height: 22px; color: #ef4444; flex-shrink: 0;"></i>
                <div>
                    <div style="font-size: 0.95rem;">Pengaturan Dikembalikan ke Default Pabrik!</div>
                    <div style="font-size: 0.82rem; font-weight: 500; color: #b91c1c; margin-top: 2px;">Parameter standar keamanan dan evaluasi telah direset ke rekomendasi madrasah.</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Elite Page Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">SECURITY PROTOCOL & ENGINE SETTINGS</div>
                        <h1><i data-lucide="shield-check"></i> Pengaturan & Keamanan CBT Online</h1>
                        <p>Pusat kendali proteksi anti-cheat, integritas pelaksanaan ujian, kunci layar penuh, pembatasan gawai, dan kebijakan pengerjaan siswa.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="document.getElementById('formPengaturan').submit()" class="e-btn e-btn-primary" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; padding: 10px 22px; box-shadow: 0 4px 14px rgba(14,165,233,0.35);">
                            <i data-lucide="save"></i> Simpan Semua Pengaturan
                        </button>
                        <button type="button" onclick="switchTab('darurat')" class="e-btn e-btn-glass">
                            <i data-lucide="unlock"></i> Buka Kunci Siswa (<?php echo $pesertaTerkunci; ?>)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Status Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
                <!-- Anti-Cheat Status -->
                <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; background: #ffffff;">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: <?php echo $isAntiCheatActive ? '#f0fdf4' : '#fef2f2'; ?>; color: <?php echo $isAntiCheatActive ? '#16a34a' : '#ef4444'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="<?php echo $isAntiCheatActive ? 'shield-check' : 'shield-alert'; ?>" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">Anti-Cheat</span>
                            <span style="font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 99px; background: <?php echo $isAntiCheatActive ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $isAntiCheatActive ? '#15803d' : '#991b1b'; ?>;">
                                <?php echo $isAntiCheatActive ? 'AKTIF' : 'NONAKTIF'; ?>
                            </span>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 4px;">Proteksi berlapis terpusat</div>
                    </div>
                </div>

                <!-- Tab Switch Violation -->
                <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; background: #ffffff;">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="monitor-x" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #0f172a;"><?php echo $maxViolations; ?>x Peringatan</div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 4px;">Toleransi keluar tab / aplikasi</div>
                    </div>
                </div>

                <!-- Mode Sanksi -->
                <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; background: #ffffff;">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #fdf4ff; color: #c026d3; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="alert-octagon" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; text-transform: capitalize;">
                            <?php echo $actionViolation === 'submit' ? 'Auto-Submit' : 'Kunci Pengerjaan'; ?>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 4px;">Sanksi jika melanggar batas</div>
                    </div>
                </div>

                <!-- Peserta Terkunci -->
                <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; background: #ffffff;">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: <?php echo $pesertaTerkunci > 0 ? '#fff1f2' : '#f0fdf4'; ?>; color: <?php echo $pesertaTerkunci > 0 ? '#e11d48' : '#16a34a'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="<?php echo $pesertaTerkunci > 0 ? 'lock' : 'users'; ?>" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                            <?php echo $pesertaTerkunci; ?> <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Siswa Terkunci</span>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 4px;">
                            <?php echo $pesertaMengerjakan; ?> Sedang Ujian
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Tabs Navigation -->
            <div style="display: flex; gap: 10px; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.75rem; overflow-x: auto; padding-bottom: 2px;">
                <button type="button" class="cbt-tab-btn active" id="tabBtn-keamanan" onclick="switchTab('keamanan')">
                    <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    <span>Keamanan & Anti-Cheat</span>
                </button>
                <button type="button" class="cbt-tab-btn" id="tabBtn-aturan" onclick="switchTab('aturan')">
                    <i data-lucide="sliders" style="width: 18px; height: 18px;"></i>
                    <span>Aturan & Pelaksanaan Ujian</span>
                </button>
                <button type="button" class="cbt-tab-btn" id="tabBtn-darurat" onclick="switchTab('darurat')">
                    <i data-lucide="unlock" style="width: 18px; height: 18px;"></i>
                    <span>Buka Kunci Siswa & Pemulihan</span>
                    <?php if ($pesertaTerkunci > 0): ?>
                        <span style="background: #ef4444; color: white; border-radius: 99px; padding: 2px 7px; font-size: 0.7rem; font-weight: 800;"><?php echo $pesertaTerkunci; ?></span>
                    <?php endif; ?>
                </button>
            </div>

            <!-- Settings Form -->
            <form id="formPengaturan" method="POST" action="<?php echo base_url('cbt/pengaturan/save'); ?>">

                <!-- TAB 1: KEAMANAN & ANTI-CHEAT -->
                <div id="tabContent-keamanan" class="cbt-tab-pane">
                    
                    <div style="margin-bottom: 1.25rem;">
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="shield" style="width: 20px; height: 20px; color: #0ea5e9;"></i> Modul Perlindungan Anti-Cheat
                        </h2>
                        <p style="font-size: 0.84rem; color: #64748b; margin: 0;">Konfigurasi proteksi integritas ujian untuk mencegah kecurangan, contek-mencontek, dan kebocoran soal.</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                        
                        <!-- 1. Master Anti-Cheat Switch -->
                        <div class="card setting-card" style="border-left: 4px solid #0ea5e9;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-blue"><i data-lucide="shield-check"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Mode Anti-Cheat Terpusat</div>
                                    </div>
                                    <p class="setting-desc">
                                        Saklar utama sistem proteksi CBT. Mengaktifkan seluruh lapisan keamanan di browser siswa secara menyeluruh.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[anti_cheat_mode]" value="0">
                                    <input type="checkbox" name="settings[anti_cheat_mode]" value="1" <?php echo $isAntiCheatActive ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 2. Force Fullscreen -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-indigo"><i data-lucide="maximize-2"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kunci Layar Penuh (Force Fullscreen)</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mewajibkan siswa masuk mode layar penuh saat mulai ujian. Jika keluar fullscreen, layar ujian diblokir oleh dialog peringatan seketika.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[force_fullscreen]" value="0">
                                    <input type="checkbox" name="settings[force_fullscreen]" value="1" <?php echo $forceFullscreen ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. Block Tab Switching -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-amber"><i data-lucide="monitor-x"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Deteksi Pindah Tab & Minimize</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mendeteksi jika siswa berpindah jendela browser, membuka tab baru, atau meminimalkan aplikasi untuk mencari jawaban.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[block_tab_switch]" value="0">
                                    <input type="checkbox" name="settings[block_tab_switch]" value="1" <?php echo $blockTab ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 4. Limit & Penalty Configuration -->
                        <div class="card setting-card" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <div class="setting-icon bg-red"><i data-lucide="alert-octagon"></i></div>
                                <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Toleransi & Tindakan Pelanggaran</div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 12px;">
                                <div>
                                    <label class="label-sm" style="font-weight: 700; color: #475569; margin-bottom: 4px; display: block; font-size: 0.78rem;">
                                        Batas Peringatan (Kali)
                                    </label>
                                    <input type="number" name="settings[max_tab_violations]" value="<?php echo $maxViolations; ?>" min="1" max="10" class="input-sm" style="font-size: 0.95rem; font-weight: 800; text-align: center;">
                                </div>
                                <div>
                                    <label class="label-sm" style="font-weight: 700; color: #475569; margin-bottom: 4px; display: block; font-size: 0.78rem;">
                                        Tindakan Saat Melewati Batas
                                    </label>
                                    <select name="settings[action_on_violation]" class="input-sm" style="font-size: 0.85rem; font-weight: 700;">
                                        <option value="submit" <?php echo $actionViolation === 'submit' ? 'selected' : ''; ?>>Submit Otomatis (Selesai)</option>
                                        <option value="lock" <?php echo $actionViolation === 'lock' ? 'selected' : ''; ?>>Kunci Layar (Butuh Pengawas)</option>
                                    </select>
                                </div>
                            </div>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 8px;">
                                Siswa diberi peringatan bertahap 1x, 2x. Pada pelanggaran ke-<?php echo $maxViolations; ?>, sistem langsung mengeksekusi tindakan terpilih.
                            </p>
                        </div>

                        <!-- 5. Disable Right Click & DevTools -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-emerald"><i data-lucide="code-2"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Blokir Klik Kanan & Inspect Element</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mematikan context menu (klik kanan), shortcut F12, Ctrl+Shift+I, dan Ctrl+U untuk mencegah siswa mengintip jawaban atau source code.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[disable_right_click]" value="0">
                                    <input type="checkbox" name="settings[disable_right_click]" value="1" <?php echo $disableRightClick ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 6. Disable Copy-Paste & Text Select -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-purple"><i data-lucide="clipboard-x"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Blokir Copy, Cut, & Paste Soal</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mencegah siswa menyalin teks soal untuk dilempar ke AI (ChatGPT/Gemini) atau mesin pencari Google.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[disable_copy_paste]" value="0">
                                    <input type="checkbox" name="settings[disable_copy_paste]" value="1" <?php echo $disableCopyPaste ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 7. Single Device Lock -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-cyan"><i data-lucide="smartphone"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kunci 1 Akun 1 Perangkat</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mencegah akun siswa yang sama dibuka di lebih dari 1 HP / Komputer secara bersamaan. Sesi lain otomatis ditolak.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[lock_single_device]" value="0">
                                    <input type="checkbox" name="settings[lock_single_device]" value="1" <?php echo $lockDevice ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 8. Watermark Identity -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-teal"><i data-lucide="fingerprint"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Watermark Identitas Siswa</div>
                                    </div>
                                    <p class="setting-desc">
                                        Menampilkan watermark transparan Nama & NIS siswa berulang di latar belakang lembar soal untuk mencegah soal difoto dan dibagikan ke media sosial/grup WA.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[watermark_student_nis]" value="0">
                                    <input type="checkbox" name="settings[watermark_student_nis]" value="1" <?php echo $watermark ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 9. Danger Alarm Sound Effect Meme -->
                        <div class="card setting-card" style="border-left: 4px solid #ef4444;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-red"><i data-lucide="volume-2"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Suara Alarm Bahaya Keluar Tab (Danger Meme)</div>
                                    </div>
                                    <p class="setting-desc">
                                        Memutar efek suara alarm bahaya (*Danger Alarm Meme*) saat siswa terdeteksi keluar dari tab ujian atau berpindah aplikasi untuk efek kejut peringatan.
                                    </p>
                                    <div style="margin-top: 12px; display: flex; align-items: center; gap: 10px;">
                                        <button type="button" onclick="testAlarmSound()" id="btnTestAlarm" class="e-btn" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 6px 14px; font-size: 0.8rem; font-weight: 800; border-radius: 8px;">
                                            <i data-lucide="volume-2" style="width: 14px; height: 14px;"></i> <span id="lblTestAlarm">Tes Putar Suara Alarm</span>
                                        </button>
                                        <audio id="audioPreviewDanger" src="<?php echo base_url('assets/sounds/alarm-danger.mp3'); ?>" preload="none"></audio>
                                    </div>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[sound_alarm_on_violation]" value="0">
                                    <input type="checkbox" name="settings[sound_alarm_on_violation]" value="1" <?php echo $soundAlarm ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 10. Anti Back Button Lock -->
                        <div class="card setting-card" style="border-left: 4px solid #f59e0b;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-amber"><i data-lucide="arrow-left-circle"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kunci & Blokir Tombol Kembali (Anti-Back)</div>
                                    </div>
                                    <p class="setting-desc">
                                        Mengunci tombol kembali (*browser / hardware back button*) pada gawai dan browser siswa. Jika ditekan, siswa dicegat tidak bisa keluar dan sistem langsung memicu peringatan/alarm.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[block_back_button]" value="0">
                                    <input type="checkbox" name="settings[block_back_button]" value="1" <?php echo $blockBackButton ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 11. Durasi Kunci Akun Spam Tombol Kembali -->
                        <div class="card setting-card" style="border-left: 4px solid #ef4444;">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-rose"><i data-lucide="shield-alert"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kunci Akun Jika Spam Tombol Kembali (Jam)</div>
                                    </div>
                                    <p class="setting-desc">
                                        Jika siswa menekan tombol <b>Kembali (Back)</b> berulang kali secara cepat (≥ 3x dalam 6 detik) di Beranda CBT maupun Ruang Ujian, siswa langsung di-logout dan akunnya dikunci selama durasi ini (Default: <code>10 jam</code>). Isi <code>0</code> untuk mematikan.
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <input type="number" name="settings[back_spam_lock_hours]" min="0" max="72" value="<?php echo $backSpamLockHours; ?>" style="width: 72px; padding: 8px 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 800; text-align: center; font-size: 0.95rem;">
                                    <span style="font-weight: 700; color: #475569; font-size: 0.85rem;">Jam</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- TAB 2: ATURAN & PELAKSANAAN UJIAN -->
                <div id="tabContent-aturan" class="cbt-tab-pane" style="display: none;">
                    
                    <div style="margin-bottom: 1.25rem;">
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="sliders" style="width: 20px; height: 20px; color: #0ea5e9;"></i> Parameter Baku Pelaksanaan Ujian
                        </h2>
                        <p style="font-size: 0.84rem; color: #64748b; margin: 0;">Nilai default untuk paket soal baru, aturan pengacakan, kebijakan nilai, dan mekanisme token ujian.</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                        
                        <!-- 1. Durasi Ujian Default -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-blue"><i data-lucide="clock"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Durasi Ujian Default</div>
                                    </div>
                                    <p class="setting-desc">Waktu pengerjaan standar (dalam menit) saat membuat paket ujian baru.</p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; min-width: 140px; justify-content: flex-end; flex-shrink: 0;">
                                    <input type="number" name="settings[default_durasi_menit]" value="<?php echo $durasiMenit; ?>" min="15" max="300" class="input-sm" style="width: 85px; text-align: center;">
                                    <span style="font-size: 0.85rem; font-weight: 800; color: #475569; white-space: nowrap;">Mnt</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. KKM Default -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-emerald"><i data-lucide="award"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Standar KKM Default</div>
                                    </div>
                                    <p class="setting-desc">Batas nilai KKM acuan kelulusan dan penentuan status Tuntas / Remedial.</p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; min-width: 140px; justify-content: flex-end; flex-shrink: 0;">
                                    <input type="number" name="settings[default_kkm]" value="<?php echo $defaultKkm; ?>" min="0" max="100" class="input-sm" style="width: 85px; text-align: center;">
                                    <span style="font-size: 0.85rem; font-weight: 800; color: #475569; white-space: nowrap;">Poin</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2b. Kunci Sesuai Jadwal (Mode Normal vs Susulan) -->
                        <div class="card setting-card" style="border-left: 4px solid #0ea5e9;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-cyan"><i data-lucide="calendar-clock"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kunci Pengerjaan Sesuai Jadwal</div>
                                    </div>
                                    <p class="setting-desc">
                                        <b>Jika AKTIF (Mode Normal):</b> Siswa hanya dapat membuka lembar ujian pada tanggal & jam slot sesi ujian yang telah ditentukan di Jadwal Ujian. Tombol dan Token otomatis terkunci sebelum waktu mulai dan sesudah sesi berakhir.<br>
                                        <b>Jika NONAKTIF (Mode Susulan):</b> Siswa yang izin/sakit dapat mengerjakan ujian di luar jadwal resmi. <i>(Catatan: Siswa yang sudah menyelesaikan ujian tetap tidak dapat mengulang).</i>
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[lock_by_schedule_time]" value="0">
                                    <input type="checkbox" name="settings[lock_by_schedule_time]" value="1" <?php echo $lockBySchedule ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 2b. Wajibkan Absensi Sebelum Ujian (Scan 1 Pintu) -->
                        <div class="card setting-card" style="border-left: 4px solid #8b5cf6; background: #faf8ff;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-purple"><i data-lucide="qr-code"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Wajibkan Absensi Sebelum Ujian (Scan 1 Pintu)</div>
                                    </div>
                                    <p class="setting-desc">
                                        <b>Jika AKTIF:</b> Siswa <b>WAJIB</b> di-scan barcode/QR kartunya oleh pengawas di pintu masuk/ruang ujian sebelum bisa mengerjakan ujian. Jika siswa belum tercatat hadir pada mapel/sesi hari ini, tombol pengerjaan di aplikasi siswa akan <b>abu-abu (disabled)</b> dengan keterangan <i>"Silakan Absensi Terlebih Dahulu"</i>.<br>
                                        <b>Jika NONAKTIF:</b> Siswa dapat langsung mulai ujian tanpa verifikasi scan absensi terlebih dahulu.
                                    </p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[require_cbt_attendance]" value="0">
                                    <input type="checkbox" name="settings[require_cbt_attendance]" value="1" <?php echo $requireAttendance ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. Acak Soal Default -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-indigo"><i data-lucide="shuffle"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Acak Nomor Urut Soal</div>
                                    </div>
                                    <p class="setting-desc">Nomor soal diacak secara dinamis per peserta agar urutan soal tidak sama antar siswa.</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[acak_soal_default]" value="0">
                                    <input type="checkbox" name="settings[acak_soal_default]" value="1" <?php echo $acakSoal ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 4. Acak Opsi Jawaban Default -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-cyan"><i data-lucide="list-ordered"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Acak Opsi Pilihan Jawaban</div>
                                    </div>
                                    <p class="setting-desc">Urutan pilihan A, B, C, D, E diacak secara otomatis sehingga posisi kunci jawaban berbeda.</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[acak_opsi_default]" value="0">
                                    <input type="checkbox" name="settings[acak_opsi_default]" value="1" <?php echo $acakOpsi ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 5. Tampilkan Skor Akhir -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-teal"><i data-lucide="eye"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Tampilkan Skor ke Siswa</div>
                                    </div>
                                    <p class="setting-desc">Siswa langsung dapat melihat perolehan skor dan status KKM begitu mengklik tombol Selesai Ujian.</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[show_score_to_student]" value="0">
                                    <input type="checkbox" name="settings[show_score_to_student]" value="1" <?php echo $showScore ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 6. Tampilkan Kunci & Pembahasan -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-amber"><i data-lucide="check-square"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Tampilkan Kunci & Pembahasan</div>
                                    </div>
                                    <p class="setting-desc">Izinkan siswa melihat kunci jawaban setelah ujian dikumpulkan. (Direkomendasikan NONAKTIF saat ujian resmi).</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[show_discussion_to_student]" value="0">
                                    <input type="checkbox" name="settings[show_discussion_to_student]" value="1" <?php echo $showDiscussion ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 7. Izinkan Siswa Cek Ulang Jawaban -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-purple"><i data-lucide="check-circle-2"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Izinkan Review & Ragu-Ragu</div>
                                    </div>
                                    <p class="setting-desc">Siswa bebas bernavigasi bolak-balik memeriksa dan mengubah jawaban sebelum menekan konfirmasi Selesai Ujian.</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[allow_student_review]" value="0">
                                    <input type="checkbox" name="settings[allow_student_review]" value="1" <?php echo $allowReview ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 8. Wajib Jawab Semua Soal & Bebas Ragu-Ragu Sebelum Selesai -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-emerald" style="background: #ecfdf5; color: #059669;"><i data-lucide="list-checks"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Wajib Jawab Semua Soal & Bebas Ragu-Ragu</div>
                                    </div>
                                    <p class="setting-desc">Jika AKTIF: Siswa <b>TIDAK DAPAT</b> mengumpulkan/menyelesaikan ujian jika masih ada soal yang belum dijawab atau masih berstatus "Ragu-Ragu". Tombol Selesai otomatis terkunci.</p>
                                </div>
                                <label class="cbt-switch">
                                    <input type="hidden" name="settings[require_all_answered_no_doubt]" value="0">
                                    <input type="checkbox" name="settings[require_all_answered_no_doubt]" value="1" <?php echo $requireAllAnswered ? 'checked' : ''; ?>>
                                    <span class="cbt-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- 9. Minimal Waktu Pengerjaan Sebelum Submit (%) -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-indigo"><i data-lucide="hourglass"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Minimal Waktu Sebelum Submit (%)</div>
                                    </div>
                                    <p class="setting-desc">Siswa belum dapat mengklik tombol <b>Selesai Ujian</b> sebelum waktu berjalan mencapai persentase ini dari durasi ujian (Default: <code>80%</code>, misal ujian 60 menit = 48 menit; 50 menit = 40 menit). Isi <code>0</code> agar bebas.</p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <input type="number" name="settings[min_submit_percent]" min="0" max="95" value="<?php echo $minSubmitPercent; ?>" style="width: 72px; padding: 8px 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 800; text-align: center; font-size: 0.95rem;">
                                    <span style="font-weight: 700; color: #475569; font-size: 0.85rem;">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- 10. Jeda Navigasi Antar Soal (Detik) -->
                        <div class="card setting-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="setting-icon bg-amber"><i data-lucide="timer"></i></div>
                                        <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Jeda Navigasi Antar Soal (Detik)</div>
                                    </div>
                                    <p class="setting-desc">Setiap pindah soal, tombol <b>Selanjutnya</b>, <b>Sebelumnya</b>, dan <b>Daftar Nomor Soal</b> terkunci sementara sesuai jeda detik ini agar siswa membaca soal dengan teliti (Default: <code>10 detik</code>). Isi <code>0</code> untuk mematikan.</p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <input type="number" name="settings[nav_delay_seconds]" min="0" max="60" value="<?php echo $navDelaySeconds; ?>" style="width: 72px; padding: 8px 10px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 800; text-align: center; font-size: 0.95rem;">
                                    <span style="font-weight: 700; color: #475569; font-size: 0.85rem;">Detik</span>
                                </div>
                            </div>
                        </div>

                        <!-- 8. Mode & Interval Token -->
                        <div class="card setting-card" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <div class="setting-icon bg-blue"><i data-lucide="key-round"></i></div>
                                <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Kebijakan Token Ujian</div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 12px;">
                                <div>
                                    <label class="label-sm" style="font-weight: 700; color: #475569; margin-bottom: 4px; display: block; font-size: 0.78rem;">
                                        Mode Perilisan Token
                                    </label>
                                    <select name="settings[token_mode]" class="input-sm" style="font-size: 0.85rem; font-weight: 700;">
                                        <option value="manual" <?php echo $tokenMode === 'manual' ? 'selected' : ''; ?>>Manual (Oleh Proktor)</option>
                                        <option value="auto" <?php echo $tokenMode === 'auto' ? 'selected' : ''; ?>>Otomatis (Sesuai Jam Ujian)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label-sm" style="font-weight: 700; color: #475569; margin-bottom: 4px; display: block; font-size: 0.78rem;">
                                        Masa Berlaku (Menit)
                                    </label>
                                    <input type="number" name="settings[token_refresh_interval]" value="<?php echo $tokenInterval; ?>" min="5" max="480" class="input-sm" style="font-size: 0.95rem; font-weight: 800; text-align: center;">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SAVE BUTTON BAR -->
                <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="check-check" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; font-weight: 800; color: #0f172a;">Simpan Konfigurasi CBT</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Perubahan akan langsung diterapkan ke sistem CBT dan pengerjaan siswa.</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="e-btn e-btn-primary" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; padding: 10px 24px;">
                            <i data-lucide="save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>

            </form>

            <!-- TAB 3: BUKA KUNCI SISWA & STATUS DARURAT -->
            <div id="tabContent-darurat" class="cbt-tab-pane" style="display: none; margin-top: 1.75rem;">
                
                <div style="margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="unlock" style="width: 20px; height: 20px; color: #ef4444;"></i> Pemulihan Sesi & Buka Kunci Siswa
                    </h2>
                    <p style="font-size: 0.84rem; color: #64748b; margin: 0;">Tangani siswa yang terblokir akibat batas toleransi keluar tab terlampaui, listrik padam, atau kendala jaringan.</p>
                </div>

                <div class="card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 2px 0;">Daftar Siswa Terkunci / Pelanggaran</h3>
                            <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Siswa yang pengerjaannya dibekukan oleh sistem anti-cheat.</p>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <form method="POST" action="<?php echo base_url('cbt/pengaturan/selesaikan-semua'); ?>" onsubmit="return confirm('Selesaikan & nilai semua siswa yang masih berstatus Mengerjakan, Terkunci, atau Pelanggaran sampai hari ini?')">
                                <button type="submit" class="e-btn e-btn-primary" style="background: #0284c7; border: none; padding: 8px 16px;">
                                    <i data-lucide="check-check"></i> Selesaikan Semua S/D Hari Ini
                                </button>
                            </form>
                            <?php if (!empty($listTerkunci)): ?>
                            <form method="POST" action="<?php echo base_url('cbt/pengaturan/reset-siswa'); ?>" onsubmit="return confirm('Buka kunci untuk SEMUA siswa yang terkunci?')">
                                <input type="hidden" name="peserta_id" value="0">
                                <button type="submit" class="e-btn e-btn-primary" style="background: #22c55e; border: none; padding: 8px 18px;">
                                    <i data-lucide="unlock"></i> Buka Kunci Semua Siswa
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($listTerkunci)): ?>
                        <div class="table-responsive">
                            <table class="table-custom" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">NO</th>
                                        <th>NIS & NAMA SISWA</th>
                                        <th>KELAS</th>
                                        <th>PAKET UJIAN</th>
                                        <th>STATUS</th>
                                        <th style="text-align: right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($listTerkunci as $lk): ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;"><?php echo $no++; ?></td>
                                        <td>
                                            <div style="font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($lk['nama_siswa']); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;">NIS: <?php echo htmlspecialchars($lk['nis']); ?></div>
                                        </td>
                                        <td><span class="badge badge-subtle"><?php echo htmlspecialchars($lk['kelas_nama'] ?: '-'); ?></span></td>
                                        <td>
                                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;"><?php echo htmlspecialchars($lk['nama_ujian'] ?: 'Ujian'); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($lk['mapel_nama'] ?: '-'); ?></div>
                                        </td>
                                        <td>
                                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                                <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Terkunci
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="<?php echo base_url('cbt/pengaturan/reset-siswa'); ?>" style="display: inline-block;">
                                                <input type="hidden" name="peserta_id" value="<?php echo $lk['id']; ?>">
                                                <button type="submit" class="e-btn e-btn-primary" style="background: #0ea5e9; padding: 6px 14px; font-size: 0.78rem;">
                                                    <i data-lucide="unlock" style="width: 14px; height: 14px;"></i> Buka Kunci
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2.5rem; text-align: center; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                                <i data-lucide="check" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">Tidak Ada Siswa yang Terkunci</div>
                            <div style="font-size: 0.82rem; color: #64748b; margin-top: 4px;">Seluruh peserta ujian saat ini berstatus normal atau belum memulai sesi.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Daftar Akun Siswa Terkunci Login (Spam Tombol Kembali) -->
                <div class="card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #fed7aa; margin-bottom: 1.5rem; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #9a3412; margin: 0 0 2px 0; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="user-x" style="width: 18px; height: 18px; color: #ea580c;"></i> Akun Terkunci Login (Spam Tombol Kembali)
                            </h3>
                            <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Siswa yang ter-logout & terkunci otomatis karena menekan tombol Kembali berkali-kali secara cepat.</p>
                        </div>
                        <?php if (!empty($listLoginLock)): ?>
                        <form method="POST" action="<?php echo base_url('cbt/pengaturan/unlock-login'); ?>" onsubmit="return confirm('Buka kunci login untuk SEMUA siswa ini?')">
                            <input type="hidden" name="lock_id" value="0">
                            <button type="submit" class="e-btn e-btn-primary" style="background: #ea580c; border: none; padding: 8px 16px;">
                                <i data-lucide="unlock"></i> Buka Semua Kunci Login
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($listLoginLock)): ?>
                        <div class="table-responsive">
                            <table class="table-custom" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NAMA SISWA</th>
                                        <th>KELAS</th>
                                        <th>ALASAN</th>
                                        <th>TERKUNCI HINGGA</th>
                                        <th style="text-align: right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $noL = 1; foreach ($listLoginLock as $ll): ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;"><?php echo $noL++; ?></td>
                                        <td style="font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($ll['nama_siswa'] ?: 'Siswa'); ?></td>
                                        <td><span class="badge badge-subtle"><?php echo htmlspecialchars($ll['kelas_nama'] ?: '-'); ?></span></td>
                                        <td style="font-size: 0.8rem; color: #9a3412; font-weight: 700;"><?php echo htmlspecialchars($ll['reason'] ?: 'Spam Tombol Kembali'); ?></td>
                                        <td style="font-size: 0.8rem; color: #dc2626; font-weight: 800;"><?php echo !empty($ll['locked_until']) ? date('d/m H:i', strtotime($ll['locked_until'])) . ' WIB' : '-'; ?></td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="<?php echo base_url('cbt/pengaturan/unlock-login'); ?>" style="display: inline-block;">
                                                <input type="hidden" name="lock_id" value="<?php echo (int)$ll['id']; ?>">
                                                <button type="submit" class="e-btn e-btn-primary" style="background: #0ea5e9; padding: 6px 14px; font-size: 0.78rem;">
                                                    <i data-lucide="unlock" style="width: 14px; height: 14px;"></i> Buka Kunci Login
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 1.5rem; text-align: center; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b; font-size: 0.84rem; font-weight: 600;">
                            Tidak ada akun siswa yang sedang terkunci login.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Log Pelanggaran Anti-Cheat Real-Time -->
                <div class="card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #fecaca; margin-bottom: 1.5rem; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #991b1b; margin: 0 0 2px 0; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="alert-triangle" style="width: 18px; height: 18px; color: #dc2626;"></i> Log Pelanggaran Siswa (Real-Time)
                            </h3>
                            <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Mencatat setiap siswa yang terdeteksi keluar tab, menekan tombol kembali, atau keluar fullscreen (pelanggaran ke-1, ke-2, dst).</p>
                        </div>
                        <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 800;">
                            <?php echo count($listPelanggaranLog ?? []); ?> Catatan
                        </span>
                    </div>

                    <?php if (!empty($listPelanggaranLog)): ?>
                        <div class="table-responsive">
                            <table class="table-custom" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>WAKTU</th>
                                        <th>NAMA SISWA</th>
                                        <th>KELAS</th>
                                        <th>MAPEL / UJIAN</th>
                                        <th>JENIS PELANGGARAN</th>
                                        <th style="text-align: center;">KE-</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $noLog = 1; foreach ($listPelanggaranLog as $pl): ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;"><?php echo $noLog++; ?></td>
                                        <td style="font-size: 0.78rem; color: #475569; white-space: nowrap;">
                                            <?php echo !empty($pl['created_at']) ? date('d/m H:i:s', strtotime($pl['created_at'])) : '-'; ?>
                                        </td>
                                        <td style="font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($pl['nama_siswa'] ?: 'Siswa'); ?></td>
                                        <td><span class="badge badge-subtle"><?php echo htmlspecialchars($pl['kelas_nama'] ?: '-'); ?></span></td>
                                        <td style="font-size: 0.82rem; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($pl['nama_ujian'] ?: '-'); ?></td>
                                        <td>
                                            <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 3px 9px; border-radius: 8px; font-size: 0.74rem; font-weight: 700;">
                                                <?php echo htmlspecialchars($pl['reason'] ?: 'Keluar Tab'); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="background: #fee2e2; color: #b91c1c; padding: 3px 10px; border-radius: 99px; font-size: 0.76rem; font-weight: 900;">
                                                <?php echo (int)($pl['violation_count'] ?? 1); ?>x
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b; font-size: 0.84rem; font-weight: 600;">
                            Belum ada catatan pelanggaran siswa yang masuk.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Daftar Siswa yang Telah Selesai / Auto-Submit -->
                <div class="card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 2px 0;">Buka Ujian Kembali (Siswa Selesai / Ter-Auto-Submit)</h3>
                            <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Izinkan siswa yang sudah selesai atau ter-submit otomatis karena alarm pelanggaran untuk melanjutkan kembali ujiannya.</p>
                        </div>
                        <button type="button" onclick="bukaModalReopenSemuaPengaturan()" class="e-btn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border: none; padding: 9px 18px; font-weight: 800; font-size: 0.82rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 14px rgba(2,132,199,0.3); cursor: pointer;">
                            <i data-lucide="unlock" style="width: 15px; height: 15px;"></i> Buka Semua Ujian Siswa
                        </button>
                    </div>

                    <?php if (!empty($listSelesai)): ?>
                        <div class="table-responsive">
                            <table class="table-custom" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NIS & NAMA SISWA</th>
                                        <th>KELAS</th>
                                        <th>PAKET UJIAN</th>
                                        <th>SKOR SAAT INI</th>
                                        <th style="text-align: right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($listSelesai as $ls): ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #64748b;"><?php echo $no++; ?></td>
                                        <td>
                                            <div style="font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($ls['nama_siswa']); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;">NIS: <?php echo htmlspecialchars($ls['nis']); ?></div>
                                        </td>
                                        <td><span class="badge badge-subtle"><?php echo htmlspecialchars($ls['kelas_nama'] ?: '-'); ?></span></td>
                                        <td>
                                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;"><?php echo htmlspecialchars($ls['nama_ujian'] ?: 'Ujian'); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($ls['mapel_nama'] ?: '-'); ?></div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 900; font-size: 0.95rem; color: <?php echo (float)$ls['nilai_akhir'] >= 75 ? '#16a34a' : '#dc2626'; ?>;">
                                                <?php echo number_format((float)$ls['nilai_akhir'], 1); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <button type="button" class="e-btn e-btn-primary" style="background: #0ea5e9; padding: 6px 14px; font-size: 0.78rem; cursor: pointer;" onclick="bukaModalReopenPengaturan(<?php echo $ls['id']; ?>, '<?php echo htmlspecialchars(addslashes($ls['nama_siswa'])); ?>', '<?php echo htmlspecialchars(addslashes($ls['nama_ujian'])); ?>')">
                                                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Buka Ujian
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.85rem;">
                            Belum ada siswa dengan status selesai untuk dibuka kembali.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Emergency Factory Reset Card -->
                <div class="card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #fecaca; background: #fff5f5;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="alert-triangle" style="width: 20px; height: 20px; color: #dc2626;"></i>
                                <span style="font-weight: 800; font-size: 1rem; color: #991b1b;">Reset Pengaturan ke Standar Awal Pabrik</span>
                            </div>
                            <p style="font-size: 0.8rem; color: #7f1d1d; margin: 4px 0 0 0;">
                                Tindakan ini akan mengembalikan seluruh nilai parameter keamanan dan ujian ke konfigurasi rekomendasi madrasah awal.
                            </p>
                        </div>
                        <form method="POST" action="<?php echo base_url('cbt/pengaturan/reset-default'); ?>" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mereset seluruh pengaturan CBT ke pengaturan awal rekomendasi madrasah?')">
                            <button type="submit" class="e-btn" style="background: #dc2626; color: white; border: none; padding: 10px 20px; font-weight: 800;">
                                <i data-lucide="refresh-cw"></i> Reset ke Pengaturan Default
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
/* CBT Toggle Switch */
.cbt-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
}
.cbt-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.cbt-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .25s ease-in-out;
    border-radius: 34px;
}
.cbt-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .25s ease-in-out;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.cbt-switch input:checked + .cbt-slider {
    background-color: #0ea5e9;
}
.cbt-switch input:checked + .cbt-slider:before {
    transform: translateX(22px);
}

/* Card & Tabs */
.setting-card {
    padding: 1.25rem 1.5rem;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    transition: transform 0.2s, box-shadow 0.2s;
}
.setting-card:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.04);
}
.setting-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.setting-icon svg {
    width: 18px;
    height: 18px;
}
.setting-desc {
    font-size: 0.82rem;
    color: #64748b;
    margin: 8px 0 0 0;
    line-height: 1.45;
}

/* Icon Backgrounds */
.bg-blue { background: #e0f2fe; color: #0284c7; }
.bg-indigo { background: #e0e7ff; color: #4338ca; }
.bg-emerald { background: #dcfce7; color: #15803d; }
.bg-amber { background: #fef3c7; color: #b45309; }
.bg-purple { background: #f3e8ff; color: #7e22ce; }
.bg-cyan { background: #cffafe; color: #0e7490; }
.bg-teal { background: #ccfbf1; color: #0f766e; }
.bg-red { background: #fee2e2; color: #b91c1c; }

/* Tab Buttons */
.cbt-tab-btn {
    background: none;
    border: none;
    padding: 10px 18px;
    font-size: 0.9rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 3px solid transparent;
    transition: 0.2s;
    white-space: nowrap;
}
.cbt-tab-btn:hover {
    color: #0ea5e9;
}
.cbt-tab-btn.active {
    color: #0ea5e9;
    border-bottom-color: #0ea5e9;
}

/* Modern Input Controls */
.input-sm {
    width: 100%;
    min-width: 75px;
    padding: 8px 12px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
    background: #ffffff;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    line-height: 1.4;
}
.input-sm:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
}
</style>

<script>
function switchTab(tabId) {
    const tabs = ['keamanan', 'aturan', 'darurat'];
    tabs.forEach(t => {
        const btn = document.getElementById('tabBtn-' + t);
        const pane = document.getElementById('tabContent-' + t);
        if (btn) btn.classList.remove('active');
        if (pane) pane.style.display = 'none';
    });

    const activeBtn = document.getElementById('tabBtn-' + tabId);
    const activePane = document.getElementById('tabContent-' + tabId);
    if (activeBtn) activeBtn.classList.add('active');
    if (activePane) activePane.style.display = 'block';

    if (window.lucide) {
        lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }
});

let isTestingSound = false;
function testAlarmSound() {
    const audio = document.getElementById('audioPreviewDanger');
    const lbl = document.getElementById('lblTestAlarm');
    if (!audio) return;
    if (!isTestingSound) {
        audio.currentTime = 0;
        audio.play().then(() => {
            isTestingSound = true;
            lbl.textContent = 'Hentikan Alarm';
        }).catch(err => {
            console.log(err);
        });
        audio.onended = () => {
            isTestingSound = false;
            lbl.textContent = 'Tes Putar Suara Alarm';
        };
    } else {
        audio.pause();
        audio.currentTime = 0;
        isTestingSound = false;
        lbl.textContent = 'Tes Putar Suara Alarm';
    }
}

function bukaModalReopenPengaturan(pesertaId, namaSiswa, namaUjian) {
    document.getElementById('reopenPengaturanPesertaId').value = pesertaId;
    document.getElementById('reopenPengaturanNamaSiswa').textContent = namaSiswa;
    document.getElementById('reopenPengaturanNamaUjian').textContent = namaUjian || 'Ujian CBT';
    document.getElementById('modalReopenPengaturan').style.display = 'flex';
}

function tutupModalReopenPengaturan() {
    document.getElementById('modalReopenPengaturan').style.display = 'none';
}

function bukaModalReopenSemuaPengaturan() {
    document.getElementById('modalReopenSemuaPengaturan').style.display = 'flex';
}

function tutupModalReopenSemuaPengaturan() {
    document.getElementById('modalReopenSemuaPengaturan').style.display = 'none';
}
</script>

<!-- Modal Buka Ujian Modern untuk Tab Pengaturan -->
<div id="modalReopenPengaturan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; justify-content: center; align-items: center; padding: 18px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 440px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <form method="POST" action="<?php echo base_url('cbt/hasil/buka-ujian'); ?>" style="margin: 0;">
            <input type="hidden" name="peserta_id" id="reopenPengaturanPesertaId" value="0">
            
            <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 22px 24px; color: #ffffff; display: flex; align-items: center; gap: 14px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    🔓
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">Buka Ujian Kembali</h3>
                    <p style="margin: 3px 0 0; font-size: 0.78rem; color: #bae6fd;">Izinkan siswa melanjutkan atau mengulang sesi ujian</p>
                </div>
            </div>

            <div style="padding: 20px 24px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Nama Siswa:</div>
                    <div id="reopenPengaturanNamaSiswa" style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 2px;">-</div>
                    <div id="reopenPengaturanNamaUjian" style="font-size: 0.78rem; color: #0284c7; font-weight: 600; margin-top: 4px;">-</div>
                </div>

                <div style="font-size: 0.8rem; font-weight: 800; color: #334155; margin-bottom: 10px;">Pilih Metode Pembukaan Ujian:</div>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px; border: 2px solid #0ea5e9; background: #f0f9ff; border-radius: 12px; margin-bottom: 10px; cursor: pointer;">
                    <input type="radio" name="mode" value="lanjutkan" checked style="margin-top: 3px; accent-color: #0ea5e9;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0369a1;">Lanjutkan Pengerjaan (Direkomendasikan)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px; line-height: 1.4;">Jawaban yang sudah dijawab siswa tetap tersimpan. Siswa dapat melanjutkan pengerjaan soal.</div>
                    </div>
                </label>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px; border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 12px; cursor: pointer;">
                    <input type="radio" name="mode" value="ulang" style="margin-top: 3px; accent-color: #ef4444;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #dc2626;">Ulang dari Awal (Reset Jawaban)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px; line-height: 1.4;">Seluruh jawaban sebelumnya akan dihapus dan waktu pengerjaan diulang dari awal.</div>
                    </div>
                </label>
            </div>

            <div style="padding: 0 24px 24px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="tutupModalReopenPengaturan()" style="padding: 10px 18px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 10px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);">
                    Buka Akses Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Buka SEMUA Ujian Siswa Serentak -->
<div id="modalReopenSemuaPengaturan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; justify-content: center; align-items: center; padding: 18px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <form method="POST" action="<?php echo base_url('cbt/hasil/buka-ujian'); ?>" style="margin: 0;">
            <input type="hidden" name="buka_semua" value="1">
            <input type="hidden" name="peserta_id" value="0">
            <input type="hidden" name="tanggal" value="all">
            
            <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 22px 24px; color: #ffffff; display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    🔓
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">Buka Semua Ujian Siswa</h3>
                    <p style="margin: 3px 0 0; font-size: 0.78rem; color: #bae6fd;">Aktifkan kembali ujian untuk seluruh siswa yang selesai / terkunci</p>
                </div>
            </div>

            <div style="padding: 20px 24px;">
                <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 14px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                    <i data-lucide="info" style="width: 20px; height: 20px; color: #2563eb; flex-shrink: 0; margin-top: 2px;"></i>
                    <div style="font-size: 0.8rem; color: #1e40af; line-height: 1.45;">
                        Tindakan ini akan mengaktifkan kembali status pengerjaan seluruh siswa yang saat ini berstatus <b>Selesai</b>, <b>Terkunci</b>, atau <b>Pelanggaran</b> agar mereka dapat langsung login dan melanjutkan pengerjaan soal ujian secara serentak.
                    </div>
                </div>

                <div style="font-size: 0.82rem; font-weight: 800; color: #334155; margin-bottom: 10px;">Pilih Mode Pembukaan:</div>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 14px; border: 2px solid #0ea5e9; background: #f0f9ff; border-radius: 12px; margin-bottom: 10px; cursor: pointer;">
                    <input type="radio" name="mode" value="lanjutkan" checked style="margin-top: 3px; accent-color: #0ea5e9;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0369a1;">Lanjutkan Pengerjaan (Sangat Direkomendasikan)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px; line-height: 1.4;">Seluruh jawaban yang telah dijawab sebelumnya tetap aman dan tidak terhapus. Siswa tinggal melanjutkan soal yang belum selesai.</div>
                    </div>
                </label>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 14px; border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 12px; cursor: pointer;">
                    <input type="radio" name="mode" value="ulang" style="margin-top: 3px; accent-color: #ef4444;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #dc2626;">Ulang dari Awal (Reset Total Seluruh Siswa)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px; line-height: 1.4;">Jawaban seluruh siswa akan dikosongkan kembali dari awal. Gunakan hanya jika ada keputusan ujian diulang total.</div>
                    </div>
                </label>
            </div>

            <div style="padding: 0 24px 24px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="tutupModalReopenSemuaPengaturan()" style="padding: 10px 18px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 10px 22px; border-radius: 10px; border: none; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                    Buka Semua Ujian Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
