<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuditLogger;

class DeveloperController {
    public static function index() {
        self::checkDev();
        $active_menu = 'dashboard';
        
        $health = array('core' => false, 'siakad' => false, 'cbt' => false);
        try { $db = Database::connect('core'); $health['core'] = true; } catch (\Exception $e) {}
        try { $db = Database::connect('siakad'); $health['siakad'] = true; } catch (\Exception $e) {}
        try { $db = Database::connect('cbt'); $health['cbt'] = true; } catch (\Exception $e) {}
        
        $diskFree = disk_free_space("C:");
        $diskTotal = disk_total_space("C:");
        $diskUsage = round((($diskTotal - $diskFree) / $diskTotal) * 100, 2);

        $activeSessions = 0;
        $visitorToday = 0;
        $visitorTotal = 0;
        try {
            $db_core = Database::connect('core');
            $activeSessions = $db_core->query("SELECT COUNT(*) FROM users WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 5")->fetchColumn();
            $visitorToday = $db_core->query("SELECT COUNT(*) FROM visitor_logs WHERE visit_date = CURDATE()")->fetchColumn();
            $visitorTotal = $db_core->query("SELECT COUNT(*) FROM visitor_logs")->fetchColumn();
        } catch (\Exception $e) {}

        $title = "Developer Dashboard - GH-SSS";
        ob_start();
        ?>
        <div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/developer/sidebar.php'; ?>
            <div class="siakad-main">
                <?php include __DIR__ . '/../../resources/views/developer/header.php'; ?>

                <div class="card" style="margin-bottom: 2rem; background: #0f172a; color: white; border: none;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="layout-dashboard" style="color: #0ea5e9;"></i> Developer Dashboard
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px;">Ringkasan Sistem & Status Server Utama</p>
                </div>

                <style>
                    @keyframes pulse { 0% { transform: scale(0.95); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(0.95); opacity: 0.5; } }
                </style>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Active Sessions Card -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(16,185,129,0.2); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(16,185,129,0.15); border-radius: 50%; filter: blur(15px);"></div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; position: relative; z-index: 2;">
                            <div style="color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Klien Aktif</div>
                            <div style="background: rgba(16,185,129,0.15); color: #0ea5e9; padding: 8px; border-radius: 10px;"><i data-lucide="users" style="width: 20px; height: 20px;"></i></div>
                        </div>
                        <div id="dash-active-sessions" style="font-size: 2.5rem; font-weight: 800; color: white; line-height: 1; position: relative; z-index: 2;"><?php echo $activeSessions; ?></div>
                        <div style="margin-top: 10px; font-size: 0.75rem; color: #0ea5e9; display: flex; align-items: center; gap: 6px; position: relative; z-index: 2;"><div style="width: 6px; height: 6px; background: #0ea5e9; border-radius: 50%; animation: pulse 2s infinite;"></div> Terhubung Saat Ini</div>
                    </div>

                    <!-- Visitor Today Card -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(59,130,246,0.2); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(59,130,246,0.15); border-radius: 50%; filter: blur(15px);"></div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; position: relative; z-index: 2;">
                            <div style="color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pengunjung Hari Ini</div>
                            <div style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 8px; border-radius: 10px;"><i data-lucide="globe" style="width: 20px; height: 20px;"></i></div>
                        </div>
                        <div id="dash-visitor-today" style="font-size: 2.5rem; font-weight: 800; color: white; line-height: 1; position: relative; z-index: 2;"><?php echo $visitorToday; ?></div>
                        <div style="margin-top: 10px; font-size: 0.75rem; color: #94a3b8; position: relative; z-index: 2;">IP Unik Terdeteksi</div>
                    </div>

                    <!-- Total Visitor Card -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(139,92,246,0.2); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(139,92,246,0.15); border-radius: 50%; filter: blur(15px);"></div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; position: relative; z-index: 2;">
                            <div style="color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Pengunjung</div>
                            <div style="background: rgba(139,92,246,0.15); color: #8b5cf6; padding: 8px; border-radius: 10px;"><i data-lucide="bar-chart-3" style="width: 20px; height: 20px;"></i></div>
                        </div>
                        <div id="dash-visitor-total" style="font-size: 2.5rem; font-weight: 800; color: white; line-height: 1; position: relative; z-index: 2;"><?php echo $visitorTotal; ?></div>
                        <div style="margin-top: 10px; font-size: 0.75rem; color: #94a3b8; position: relative; z-index: 2;">Sepanjang Waktu</div>
                    </div>
                </div>

                <!-- SERVER HEALTH & DISK SECTION -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                    
                    <!-- Databases Status -->
                    <div style="background: #0f172a; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);">
                        <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.01);">
                            <h3 style="margin: 0; color: white; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;"><i data-lucide="server" style="color: #3b82f6;"></i> Database Servers Status</h3>
                            <div style="padding: 4px 10px; background: rgba(16,185,129,0.1); color: #0ea5e9; border-radius: 20px; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.05em; border: 1px solid rgba(16,185,129,0.2);">SYSTEMS NOMINAL</div>
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                            <!-- CORE DB -->
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 15px; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="background: rgba(0,0,0,0.3); width: 45px; height: 45px; border-radius: 10px; display: flex; justify-content: center; align-items: center; border: 1px solid rgba(255,255,255,0.05);"><i data-lucide="database" style="color: #cbd5e1;"></i></div>
                                    <div>
                                        <div style="color: white; font-weight: 600; font-size: 0.95rem;">Core Database</div>
                                        <div style="color: #64748b; font-size: 0.8rem; margin-top: 2px;">core_db (Main Configuration)</div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px;">
                                    <?php if($health['core']): ?>
                                        <span style="color: #0ea5e9; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">ONLINE</span>
                                        <div style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; box-shadow: 0 0 10px #0ea5e9;"></div>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">OFFLINE</span>
                                        <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 10px #ef4444;"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- SIAKAD DB -->
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 15px; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="background: rgba(0,0,0,0.3); width: 45px; height: 45px; border-radius: 10px; display: flex; justify-content: center; align-items: center; border: 1px solid rgba(255,255,255,0.05);"><i data-lucide="database" style="color: #cbd5e1;"></i></div>
                                    <div>
                                        <div style="color: white; font-weight: 600; font-size: 0.95rem;">SIAKAD Database</div>
                                        <div style="color: #64748b; font-size: 0.8rem; margin-top: 2px;">siakad_db (Academic Data)</div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px;">
                                    <?php if($health['siakad']): ?>
                                        <span style="color: #0ea5e9; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">ONLINE</span>
                                        <div style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; box-shadow: 0 0 10px #0ea5e9;"></div>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">OFFLINE</span>
                                        <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 10px #ef4444;"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- CBT DB -->
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.03); border-radius: 12px; padding: 15px; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="background: rgba(0,0,0,0.3); width: 45px; height: 45px; border-radius: 10px; display: flex; justify-content: center; align-items: center; border: 1px solid rgba(255,255,255,0.05);"><i data-lucide="database" style="color: #cbd5e1;"></i></div>
                                    <div>
                                        <div style="color: white; font-weight: 600; font-size: 0.95rem;">CBT Database</div>
                                        <div style="color: #64748b; font-size: 0.8rem; margin-top: 2px;">cbt_db (Examinations)</div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px;">
                                    <?php if($health['cbt']): ?>
                                        <span style="color: #0ea5e9; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">ONLINE</span>
                                        <div style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; box-shadow: 0 0 10px #0ea5e9;"></div>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em;">OFFLINE</span>
                                        <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 10px #ef4444;"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Disk Usage -->
                    <div style="background: linear-gradient(180deg, #0f172a 0%, rgba(15,23,42,0.8) 100%); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);">
                        <h3 style="margin: 0 0 1.5rem 0; color: white; font-size: 1.1rem; width: 100%; text-align: left; display: flex; align-items: center; gap: 10px;"><i data-lucide="hard-drive" style="color: #f59e0b;"></i> Storage Status (C:)</h3>
                        
                        <div style="position: relative; width: 140px; height: 140px; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.4); border-radius: 50%; box-shadow: inset 0 0 20px rgba(0,0,0,0.5), 0 0 0 10px rgba(255,255,255,0.02);">
                            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="2.5" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="<?php echo $diskUsage > 90 ? '#ef4444' : '#0ea5e9'; ?>" stroke-width="2.5" stroke-dasharray="<?php echo $diskUsage; ?>, 100" style="transition: stroke-dasharray 1s ease-in-out;" />
                            </svg>
                            <div style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div style="font-size: 2rem; font-weight: 800; color: white; line-height: 1;"><?php echo $diskUsage; ?>%</div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px; font-weight: 600; letter-spacing: 0.05em;">USED</div>
                            </div>
                        </div>

                        <div style="margin-top: 2rem; width: 100%; padding: 0 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85rem; color: #94a3b8; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px;">
                                <span>Used: <span style="color: white; font-weight: bold;"><?php echo round(($diskTotal - $diskFree)/1073741824, 1); ?> GB</span></span>
                                <span>Free: <span style="color: #0ea5e9; font-weight: bold;"><?php echo round($diskFree/1073741824, 1); ?> GB</span></span>
                            </div>
                            <div style="color: #64748b; font-size: 0.75rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <i data-lucide="server" style="width:12px;height:12px;"></i> PHP Engine: <?php echo phpversion(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- APK PREVIEW SECTION -->
                <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                    <!-- Preview Siswa -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(16,185,129,0.2); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); text-align: center;">
                        <div style="width: 60px; height: 60px; background: rgba(16,185,129,0.15); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 15px;">
                            <i data-lucide="smartphone"></i>
                        </div>
                        <h3 style="color: white; margin: 0 0 5px;">Tampilan APK Siswa</h3>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 20px;">Masuk sebagai Siswa Dummy tanpa password</p>
                        <a href="/apk_preview.php?role=siswa" target="_blank" style="display: inline-block; background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: background 0.3s;"><i data-lucide="external-link" style="width: 16px; height: 16px; display: inline-block; vertical-align: text-bottom; margin-right: 5px;"></i> Buka Preview Siswa</a>
                    </div>
                    
                    <!-- Preview Guru -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(14,165,233,0.2); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); text-align: center;">
                        <div style="width: 60px; height: 60px; background: rgba(14,165,233,0.15); color: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 15px;">
                            <i data-lucide="smartphone"></i>
                        </div>
                        <h3 style="color: white; margin: 0 0 5px;">Tampilan APK Guru</h3>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 20px;">Masuk sebagai Guru Dummy tanpa password</p>
                        <a href="/apk_preview.php?role=guru" target="_blank" style="display: inline-block; background: #0ea5e9; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: background 0.3s;"><i data-lucide="external-link" style="width: 16px; height: 16px; display: inline-block; vertical-align: text-bottom; margin-right: 5px;"></i> Buka Preview Guru</a>
                    </div>
                </div>

            </div>
        </div>
        
        <script>
            // Real-time polling untuk Dashboard Stats (setiap 5 detik)
            function pollDashboardStats() {
                fetch('/dev-panel/stats')
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('dash-active-sessions').innerText = data.activeSessions;
                        document.getElementById('dash-visitor-today').innerText = data.visitorToday;
                        document.getElementById('dash-visitor-total').innerText = data.visitorTotal;
                    })
                    .catch(e => console.error("Error polling stats:", e));
            }
            setInterval(pollDashboardStats, 5000);
        </script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function mou() {
        self::checkDev();
        $active_menu = 'mou';
        $title = "MOU Kerjasama - GH-SSS";
        
        $file = __DIR__ . '/../../storage/draft_mou.md';
        $contentMou = file_exists($file) ? file_get_contents($file) : "File draft belum tersedia.";

        ob_start();
        ?>
        <div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/developer/sidebar.php'; ?>
            <div class="siakad-main">
                <?php include __DIR__ . '/../../resources/views/developer/header.php'; ?>

                <div class="card" style="margin-bottom: 2rem; background: #0f172a; color: white; border: none;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="file-edit" style="color: #0ea5e9;"></i> Editor MOU Kerjasama
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px;">Draft Perjanjian Kerja Sama. Kamu bisa mengedit teks di bawah ini.</p>
                </div>
                
                <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column;">
                    <form action="/dev-panel/mou-save" method="POST">
                        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8; font-size: 0.9rem;">Editor Teks Draf MOU</span>
                            <div style="display: flex; gap: 10px;">
                                <a href="/dev-panel/mou-print" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 8px 20px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Preview & Cetak
                                </a>
                                <button type="submit" style="display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; border: none; padding: 8px 20px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                    <i data-lucide="save" style="width: 14px; height: 14px;"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['mou_msg'])): ?>
                            <div style="background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">
                                <i data-lucide="check-circle" style="width: 16px; height: 16px; vertical-align: text-bottom;"></i> <?php echo $_SESSION['mou_msg']; unset($_SESSION['mou_msg']); ?>
                            </div>
                        <?php endif; ?>
                        <textarea name="mou_content" style="width: 100%; height: 500px; background: #050a14; color: #e2e8f0; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 15px; font-family: monospace; font-size: 0.9rem; resize: vertical; line-height: 1.5;"><?php echo htmlspecialchars($contentMou); ?></textarea>
                    </form>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function mouSave() {
        self::checkDev();
        $file = __DIR__ . '/../../storage/draft_mou.md';
        if (isset($_POST['mou_content'])) {
            file_put_contents($file, $_POST['mou_content']);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['mou_msg'] = 'Draf MOU berhasil disimpan!';
        }
        header("Location: /dev-panel/mou");
        exit;
    }

    public static function mouPrint() {
        self::checkDev();
        $file = __DIR__ . '/../../storage/draft_mou.md';
        $contentMou = file_exists($file) ? file_get_contents($file) : "File draft belum tersedia.";
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak MOU Kerjasama</title>
            <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
            <style>
                body { 
                    font-family: 'Times New Roman', Times, serif; 
                    line-height: 1.5; 
                    padding: 40px 60px; 
                    max-width: 850px; 
                    margin: 0 auto; 
                    color: black; 
                    background: white;
                }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
                h1, h2, h3 { text-align: center; }
                hr { border: none; border-top: 1px solid black; margin: 20px 0; }
                ul, ol { margin-top: 0; }
                p { text-align: justify; }
            </style>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 20px; text-align: center; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <button onclick="window.print()" style="background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold;">🖨️ Cetak Dokumen</button>
                <p style="margin: 10px 0 0; font-size: 13px; color: #64748b; font-family: sans-serif;">Gunakan ukuran kertas A4. Sesuaikan margin jika diperlukan saat dialog print muncul.</p>
            </div>
            <div id="content"></div>
            <script>
                // Decode base64 to handle all special characters safely
                const rawMarkdown = atob("<?php echo base64_encode($contentMou); ?>");
                document.getElementById('content').innerHTML = marked.parse(rawMarkdown);
            </script>
        </body>
        </html>
        <?php
        exit;
    }

    public static function dashboardStats() {
        self::checkDev();
        
        $activeSessions = 0;
        $visitorToday = 0;
        $visitorTotal = 0;
        try {
            $db_core = Database::connect('core');
            $activeSessions = $db_core->query("SELECT COUNT(*) FROM users WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 5")->fetchColumn();
            $visitorToday = $db_core->query("SELECT COUNT(*) FROM visitor_logs WHERE visit_date = CURDATE()")->fetchColumn();
            $visitorTotal = $db_core->query("SELECT COUNT(*) FROM visitor_logs")->fetchColumn();
        } catch (\Exception $e) {}

        header('Content-Type: application/json');
        echo json_encode([
            'activeSessions' => $activeSessions,
            'visitorToday' => $visitorToday,
            'visitorTotal' => $visitorTotal
        ]);
        exit;
    }

    public static function monitor() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Only developer (99) can access dev-panel.
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            die("Unauthorized access.");
        }

        $active_menu = 'monitor';
        $title = "Live System Monitor - GH-SSS";
        ob_start();
        ?>
        <div class="siakad-container">
            <!-- Developer Sidebar -->
            <?php include __DIR__ . '/../../resources/views/developer/sidebar.php'; ?>

            <div class="siakad-main">
                <!-- Developer Header -->
                <?php include __DIR__ . '/../../resources/views/developer/header.php'; ?>

                <div class="card" style="margin-bottom: 2rem; background: #0f172a; color: white; border: none;">
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="activity" style="color: #3b82f6;"></i> Live System Monitor
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px;">Pantau aktivitas jaringan dan log keamanan secara real-time</p>
                </div>
                
                <!-- NEW ROW: MOBILE APP CONNECTED DEVICES -->
                <div class="card" style="padding: 1.5rem; margin-top: 2rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h3 style="margin:0; color: #0ea5e9; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="smartphone"></i> Mobile App Connected Devices
                        </h3>
                        <span style="background: rgba(16,185,129,0.1); color: #0ea5e9; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: bold; border: 1px solid rgba(16,185,129,0.2);">ONESIGNAL SYNC</span>
                    </div>
                    <div style="background: rgba(0,0,0,0.15); border-radius: 8px; padding: 0 15px;">
                        <div id="onesignal-devices-body" style="min-height: 100px; padding: 20px; text-align: center; color: #64748b; font-size: 0.9rem;">
                            <i data-lucide="loader" style="animation:spin 1s linear infinite; margin-bottom: 10px; width: 30px; height: 30px;"></i><br>
                            Memuat daftar perangkat dari satelit OneSignal...
                        </div>
                    </div>
                </div>

                <!-- NEW ROW: LIVE USER MONITOR & LOGS -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                    
                    <!-- LIVE USER MONITOR -->
                    <div class="card" style="padding: 1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3 style="margin:0; color: #3b82f6; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="radio" style="animation: pulse 2s infinite;"></i> Live User Monitor
                            </h3>
                            <span style="background: rgba(59,130,246,0.1); color: #3b82f6; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: bold; border: 1px solid rgba(59,130,246,0.2);">REAL-TIME</span>
                        </div>
                        <style>
                            @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
                            .user-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
                            .user-row:last-child { border-bottom: none; }
                            .u-avatar { width: 32px; height: 32px; border-radius: 50%; background: #1e293b; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold; color: #94a3b8; }
                            .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
                            .online { background: #0ea5e9; box-shadow: 0 0 8px #0ea5e9; }
                            .offline { background: #64748b; }
                        </style>
                        <div id="live-user-body" style="background: rgba(0,0,0,0.15); border-radius: 8px; padding: 0 15px; min-height: 200px;">
                            <div style="padding: 20px; text-align: center; color: #64748b; font-size: 0.9rem;">
                                <i data-lucide="loader" style="animation:spin 1s linear infinite; margin-bottom: 10px; width: 30px; height: 30px;"></i><br>
                                Memuat aktivitas pengguna...
                            </div>
                        </div>
                    </div>


                    <!-- SYSTEM AUDIT LOGS â€” INTERACTIVE -->
                    <div class="card" style="padding: 1.5rem; display:flex; flex-direction:column;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:10px;">
                            <h3 style="margin:0; color: #f59e0b; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="scroll-text"></i> System Audit Logs
                            </h3>
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                <!-- Live indicator -->
                                <span id="audit-live-dot" style="display:inline-flex;align-items:center;gap:5px;font-size:0.7rem;font-weight:700;color:#0ea5e9;background:rgba(16,185,129,0.1);padding:3px 10px;border-radius:100px;border:1px solid rgba(16,185,129,0.2);">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#0ea5e9;display:inline-block;animation:pulse 2s infinite;"></span> LIVE
                                </span>
                                <!-- Counter -->
                                <span id="audit-count" style="font-size:0.7rem;color:#64748b;background:rgba(255,255,255,0.05);padding:3px 10px;border-radius:100px;border:1px solid rgba(255,255,255,0.05);">0 entries</span>
                            </div>
                        </div>

                        <!-- Controls Bar -->
                        <div style="display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; align-items:center;">
                            <!-- Category filter pills -->
                            <div id="audit-filters" style="display:flex;gap:4px;flex-wrap:wrap;">
                                <button class="audit-filter-btn active" data-filter="ALL" onclick="setAuditFilter('ALL',this)">Semua</button>
                                <button class="audit-filter-btn" data-filter="AUTH" onclick="setAuditFilter('AUTH',this)">ðŸ” Auth</button>
                                <button class="audit-filter-btn" data-filter="DATA" onclick="setAuditFilter('DATA',this)">ðŸ“ Data</button>
                                <button class="audit-filter-btn" data-filter="ACCESS" onclick="setAuditFilter('ACCESS',this)">ðŸ‘ Akses</button>
                                <button class="audit-filter-btn" data-filter="SYSTEM" onclick="setAuditFilter('SYSTEM',this)">âš™ Sistem</button>
                                <button class="audit-filter-btn" data-filter="TUNNEL" onclick="setAuditFilter('TUNNEL',this)">ðŸŒ Tunnel</button>
                                <button class="audit-filter-btn" data-filter="SECURITY" onclick="setAuditFilter('SECURITY',this)">ðŸ›¡ Security</button>
                            </div>
                            <div style="flex:1;"></div>
                            <!-- Search -->
                            <div style="position:relative;">
                                <input type="text" id="audit-search" placeholder="Cari log..." 
                                    style="background:#0a0f1a;border:1px solid #1e293b;color:#e2e8f0;padding:6px 12px 6px 30px;border-radius:8px;font-size:0.75rem;width:180px;outline:none;"
                                    oninput="debouncedAuditSearch()">
                                <i data-lucide="search" style="position:absolute;left:8px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#475569;pointer-events:none;"></i>
                            </div>
                            <!-- Clear btn -->
                            <button onclick="clearAuditLog()" title="Bersihkan log" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;padding:6px 10px;border-radius:8px;cursor:pointer;font-size:0.75rem;display:flex;align-items:center;gap:4px;">
                                <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Clear
                            </button>
                        </div>

                        <style>
                            .audit-filter-btn {
                                background: rgba(255,255,255,0.03);
                                border: 1px solid rgba(255,255,255,0.06);
                                color: #64748b;
                                padding: 4px 10px;
                                border-radius: 100px;
                                font-size: 0.68rem;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s;
                            }
                            .audit-filter-btn:hover { background: rgba(255,255,255,0.06); color: #94a3b8; }
                            .audit-filter-btn.active { background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.3); color: #f59e0b; }

                            .audit-line { padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 0.76rem; line-height: 1.5; animation: auditFadeIn 0.3s ease; }
                            .audit-line:last-child { border-bottom: none; }
                            @keyframes auditFadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

                            .audit-ts { color: #475569; }
                            .audit-level-ok { color: #0ea5e9; font-weight: 700; }
                            .audit-level-info { color: #3b82f6; font-weight: 700; }
                            .audit-level-warn { color: #f59e0b; font-weight: 700; }
                            .audit-level-error { color: #ef4444; font-weight: 700; }
                            .audit-cat { color: #8b5cf6; font-weight: 600; background: rgba(139,92,246,0.08); padding: 1px 6px; border-radius: 4px; margin: 0 4px; font-size: 0.65rem; }
                            .audit-msg { color: #cbd5e1; }
                            .audit-meta { color: #334155; font-size: 0.68rem; }

                            .audit-empty { color: #334155; text-align: center; padding: 3rem 1rem; font-size: 0.85rem; }
                            .audit-empty i { display: block; margin: 0 auto 10px; width: 40px; height: 40px; color: #1e293b; }
                        </style>

                        <!-- Log body -->
                        <div id="audit-body" style="flex:1; background: #050a14; border-radius: 10px; border: 1px solid #0f1a2e; padding: 12px 15px; font-family: 'JetBrains Mono', 'Fira Code', monospace; overflow-y: auto; max-height: 400px; min-height: 200px;">
                            <div class="audit-empty">
                                <i data-lucide="loader" style="animation:spin 1s linear infinite;"></i>
                                Memuat audit log...
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Developer Footer -->
                <?php include __DIR__ . '/../../resources/views/developer/footer.php'; ?>
            </div>
        </div>

        <script>
        // â”€â”€ Audit Log Real-Time Engine â”€â”€
        let _auditFilter = 'ALL';
        let _auditSearch = '';
        let _auditTimer = null;
        let _auditPrevCount = -1;
        let auditOffset = 0;
        let auditLimit = 15;

        function fetchAuditLogs() {
            let url = `/dev-panel/audit-log?limit=${auditLimit}&offset=${auditOffset}`;
            if (_auditFilter && _auditFilter !== 'ALL') url += '&filter=' + encodeURIComponent(_auditFilter);
            if (_auditSearch) url += '&search=' + encodeURIComponent(_auditSearch);

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const body = document.getElementById('audit-body');
                    const countEl = document.getElementById('audit-count');
                    countEl.textContent = data.count + ' entries';

                    if (data.count === 0) {
                        body.innerHTML = '<div class="audit-empty"><i data-lucide="inbox"></i>Tidak ada log yang cocok.</div>';
                        lucide.createIcons();
                        return;
                    }

                    // Only re-render if count changed
                    if (data.count === _auditPrevCount) return;
                    _auditPrevCount = data.count;

                    let html = '';
                    data.entries.forEach(entry => {
                        const levelClass = 'audit-level-' + entry.level.toLowerCase();
                        // Parse message to highlight key parts
                        let msg = escAudit(entry.message);
                        // Highlight USER=, IP=, etc
                        msg = msg.replace(/\b(USER|IP|BROWSER|URI|ROLE)=([^\s,|]+)/g, '<span class="audit-meta">$1=</span><span style="color:#94a3b8;">$2</span>');
                        // Highlight usernames
                        msg = msg.replace(/@(\w+)/g, '<span style="color:#38bdf8;font-weight:600;">@$1</span>');

                        html += '<div class="audit-line">';
                        html += '<span class="audit-ts">[' + escAudit(entry.ts) + ']</span> ';
                        html += '<span class="' + levelClass + '">[' + entry.level + ']</span> ';
                        html += '<span class="audit-cat">' + entry.category + '</span> ';
                        html += '<span class="audit-msg">' + msg + '</span>';
                        html += '</div>';
                    });
                    
                    // Pagination Controls for Audit Logs
                    let prevDisabled = auditOffset === 0 ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.05);';
                    let nextDisabled = (auditOffset + auditLimit) >= data.totalCount ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.05);';
                    
                    html += `
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px;">
                        <button onclick="changeAuditPage(-1)" style="padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; transition: all 0.2s; ${prevDisabled}" ${auditOffset === 0 ? 'disabled' : ''}>
                            <i data-lucide="chevron-left" style="width:12px;height:12px;vertical-align:-2px;"></i> Prev
                        </button>
                        <button onclick="changeAuditPage(1)" style="padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; transition: all 0.2s; ${nextDisabled}" ${(auditOffset + auditLimit) >= data.totalCount ? 'disabled' : ''}>
                            Next <i data-lucide="chevron-right" style="width:12px;height:12px;vertical-align:-2px;"></i>
                        </button>
                    </div>`;

                    body.innerHTML = html;
                    lucide.createIcons();
                })
                .catch(() => {});
        }
        
        function changeAuditPage(direction) {
            if (direction === -1 && auditOffset >= auditLimit) {
                auditOffset -= auditLimit;
            } else if (direction === 1) {
                auditOffset += auditLimit;
            }
            _auditPrevCount = -1; // force re-render
            fetchAuditLogs();
        }

        function escAudit(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function setAuditFilter(filter, btn) {
            _auditFilter = filter;
            _auditPrevCount = -1;
            document.querySelectorAll('.audit-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            fetchAuditLogs();
        }

        let _searchDebounce;
        function debouncedAuditSearch() {
            clearTimeout(_searchDebounce);
            _searchDebounce = setTimeout(() => {
                _auditSearch = document.getElementById('audit-search').value.trim();
                _auditPrevCount = -1;
                fetchAuditLogs();
            }, 300);
        }

        function clearAuditLog() {
            if (!confirm('Bersihkan semua audit log?')) return;
            fetch('/dev-panel/audit-log/clear', { method: 'POST' })
                .then(r => r.json())
                .then(() => {
                    _auditPrevCount = -1;
                    fetchAuditLogs();
                });
        }

        // Initial load + auto-refresh every 5s
        fetchAuditLogs();
        _auditTimer = setInterval(fetchAuditLogs, 5000);

        let devicesOffset = 0;
        let devicesLimit = 9;

        function fetchDevices() {
            fetch(`/dev-panel/devices?limit=${devicesLimit}&offset=${devicesOffset}`)
                .then(r => r.json())
                .then(data => {
                    const body = document.getElementById('onesignal-devices-body');
                    if (!data || !data.players || (data.players.length === 0 && devicesOffset === 0)) {
                        body.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b; font-size: 0.9rem;">Belum ada perangkat terhubung.</div>';
                        return;
                    }
                    
                    let html = '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; padding: 10px 0;">';
                    data.players.forEach(p => {
                        const lastActive = new Date(p.last_active * 1000).toLocaleString('id-ID');
                        const isOnline = (Date.now() / 1000 - p.last_active) < 300;
                        const statusDot = isOnline ? '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#0ea5e9; box-shadow:0 0 8px #0ea5e9; margin-right:5px;"></span>' : '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#64748b; margin-right:5px;"></span>';
                        
                        html += `
                        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; text-align: left;">
                            <div style="font-weight: bold; color: #e2e8f0; font-size: 0.9rem; margin-bottom: 5px; display: flex; justify-content: space-between;">
                                <span><i data-lucide="smartphone" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 4px; color: #38bdf8;"></i> ${p.device_model || 'Unknown Device'}</span>
                                ${statusDot}
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 3px;">
                                <i data-lucide="user" style="width: 12px; height: 12px; vertical-align: -2px;"></i> User: <span style="color: #f59e0b; font-family: monospace;">${p.nama_siswa ? p.nama_siswa + ' (ID: ' + p.external_user_id + ')' : (p.external_user_id || 'Belum Login')}</span>
                            </div>
                            <div style="font-size: 0.7rem; color: #64748b;">
                                <i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: -2px;"></i> Active: ${lastActive}
                            </div>
                        </div>`;
                    });
                    html += '</div>';

                    // Pagination Controls
                    let prevDisabled = devicesOffset === 0 ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.1);';
                    let nextDisabled = data.players.length < devicesLimit ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.1);';
                    
                    html += `
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; padding-bottom: 10px;">
                        <button onclick="changeDevicePage(-1)" style="padding: 6px 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; font-size: 0.8rem; background: transparent; transition: all 0.2s; ${prevDisabled}" ${devicesOffset === 0 ? 'disabled' : ''}>
                            <i data-lucide="chevron-left" style="width:14px;height:14px;vertical-align:-2px;"></i> Prev
                        </button>
                        <button onclick="changeDevicePage(1)" style="padding: 6px 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; font-size: 0.8rem; background: transparent; transition: all 0.2s; ${nextDisabled}" ${data.players.length < devicesLimit ? 'disabled' : ''}>
                            Next <i data-lucide="chevron-right" style="width:14px;height:14px;vertical-align:-2px;"></i>
                        </button>
                    </div>`;

                    body.innerHTML = html;
                    lucide.createIcons();
                })
                .catch(() => {
                    document.getElementById('onesignal-devices-body').innerHTML = '<div style="color: #ef4444; font-size: 0.85rem; padding: 10px;">Gagal terhubung ke API OneSignal.</div>';
                });
        }
        
        function changeDevicePage(direction) {
            if (direction === -1 && devicesOffset >= devicesLimit) {
                devicesOffset -= devicesLimit;
            } else if (direction === 1) {
                devicesOffset += devicesLimit;
            }
            fetchDevices();
        }

        fetchDevices();
        setInterval(fetchDevices, 10000); // refresh every 10 seconds

        lucide.createIcons();
        </script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
        ?>
        <script>
        // â”€â”€ Live User Monitor Engine â”€â”€
        let liveOffset = 0;
        let liveLimit = 6;
        let _liveTimer = null;

        function fetchLiveUsers() {
            fetch(`/dev-panel/live-monitor?limit=${liveLimit}&offset=${liveOffset}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const body = document.getElementById('live-user-body');
                    
                    if (data.users.length === 0 && liveOffset === 0) {
                        body.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b; font-size: 0.9rem;">Belum ada aktivitas terekam.</div>';
                        return;
                    }
                    
                    let html = '';
                    data.users.forEach(u => {
                        let isOnline = u.mins_ago <= 5;
                        let initial = (u.nama || u.username).substring(0, 2).toUpperCase();
                        let roleName = u.role_id == 1 ? 'Admin' : (u.role_id == 99 ? 'Dev' : (u.role_id == 2 ? 'Guru' : 'Siswa'));
                        
                        html += `
                        <div class="user-row">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="u-avatar">${initial}</div>
                                <div>
                                    <div style="color:#e2e8f0; font-weight:600; font-size:0.9rem;">${u.nama || u.username}</div>
                                    <div style="color:#64748b; font-size:0.75rem;">${roleName} &bull; @${u.username}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">`;
                        
                        if (isOnline) {
                            html += `<div style="color:#0ea5e9; font-size:0.8rem; font-weight:bold;"><span class="status-dot online"></span> ONLINE</div>
                                     <div style="color:#64748b; font-size:0.7rem;">Sekarang</div>`;
                        } else {
                            html += `<div style="color:#94a3b8; font-size:0.8rem; font-weight:bold;"><span class="status-dot offline"></span> OFFLINE</div>
                                     <div style="color:#64748b; font-size:0.7rem;">${u.mins_ago < 60 ? u.mins_ago + ' m lalu' : Math.floor(u.mins_ago/60) + ' j lalu'}</div>`;
                        }
                        
                        html += `</div></div>`;
                    });
                    
                    // Pagination Controls for Live Monitor
                    let prevDisabled = liveOffset === 0 ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.05);';
                    let nextDisabled = (liveOffset + liveLimit) >= data.totalCount ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;background:rgba(255,255,255,0.05);';
                    
                    html += `
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); padding: 15px 0 10px;">
                        <button onclick="changeLivePage(-1)" style="padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; transition: all 0.2s; ${prevDisabled}" ${liveOffset === 0 ? 'disabled' : ''}>
                            <i data-lucide="chevron-left" style="width:12px;height:12px;vertical-align:-2px;"></i> Prev
                        </button>
                        <button onclick="changeLivePage(1)" style="padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; transition: all 0.2s; ${nextDisabled}" ${(liveOffset + liveLimit) >= data.totalCount ? 'disabled' : ''}>
                            Next <i data-lucide="chevron-right" style="width:12px;height:12px;vertical-align:-2px;"></i>
                        </button>
                    </div>`;

                    body.innerHTML = html;
                    lucide.createIcons();
                })
                .catch(() => {});
        }
        
        function changeLivePage(direction) {
            if (direction === -1 && liveOffset >= liveLimit) {
                liveOffset -= liveLimit;
            } else if (direction === 1) {
                liveOffset += liveLimit;
            }
            fetchLiveUsers();
        }

        fetchLiveUsers();
        _liveTimer = setInterval(fetchLiveUsers, 10000); // 10s refresh
        </script>
    <?php
    }

    public static function setup() {
        echo "Setup is handled via database seeds.";
    }

    private static function checkDev() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // --- BYPASS LOGIN (SECURITY RISK - FOR DEV ONLY) ---
        // Automatically set session as Developer if not set
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            $_SESSION['role_id'] = 99;
            $_SESSION['user_id'] = 1; // Assuming dev user ID is 1
        }
    }

    // --- GLOBAL IDENTITY HUB (BRANDING) ---
    public static function branding() {
        self::checkDev();
        $db = Database::connect('core');
        $apps = $db->query("SELECT * FROM app_modules ORDER BY app_code ASC")->fetchAll();
        $active_menu = 'branding';
        
        ob_start();
        include __DIR__ . '/../../resources/views/developer/branding_view.php';
        $content = ob_get_clean();
        
        $title = "Global App Branding - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function saveBranding() {
        self::checkDev();
        $db = Database::connect('core');
        $app_code = $_POST['app_code'];
        $app_name = $_POST['app_name'];
        $app_version = $_POST['app_version'];
        $theme_color = $_POST['theme_color'];
        
        // Handle uploads
        $logo_path = null;
        $favicon_path = null;
        $target_dir = __DIR__ . '/../../public/images/';
        
        if (isset($_FILES["logo_file"]) && $_FILES["logo_file"]["error"] == 0) {
            $logo_path = 'logo_' . $app_code . '_' . time() . '.' . pathinfo($_FILES["logo_file"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["logo_file"]["tmp_name"], $target_dir . $logo_path);
        }
        if (isset($_FILES["favicon_file"]) && $_FILES["favicon_file"]["error"] == 0) {
            $favicon_path = 'favicon_' . $app_code . '_' . time() . '.' . pathinfo($_FILES["favicon_file"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["favicon_file"]["tmp_name"], $target_dir . $favicon_path);
        }

        // Update DB
        $sql = "UPDATE app_modules SET app_name = :name, app_version = :ver, theme_color = :theme";
        $params = [':name' => $app_name, ':ver' => $app_version, ':theme' => $theme_color, ':code' => $app_code];
        
        if ($logo_path) { $sql .= ", logo_path = :logo"; $params[':logo'] = $logo_path; }
        if ($favicon_path) { $sql .= ", favicon_path = :fav"; $params[':fav'] = $favicon_path; }
        
        $sql .= " WHERE app_code = :code";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // â”€â”€ Audit Log â”€â”€
        AuditLogger::dataChange('Branding', 'UPDATE', "app={$app_code}, name={$app_name}, ver={$app_version}");

        redirect("/dev-panel/branding?success=1");
        exit;
    }

    // --- MASTER ADMIN CONTROL ---
    public static function users() {
        self::checkDev();
        $active_menu = 'users';
        $db = Database::connect('core');
        $users = $db->query("
            SELECT u.*, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.role_id != 3
            ORDER BY u.role_id ASC
        ")->fetchAll();
        $roles = $db->query("SELECT * FROM roles WHERE id != 3 ORDER BY id ASC")->fetchAll();
        
        $active_menu = 'users';
        ob_start();
        include __DIR__ . '/../../resources/views/developer/users_view.php';
        $content = ob_get_clean();
        
        $title = "Master Admin Control - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function saveUser() {
        self::checkDev();
        $db = Database::connect('core');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $username = $_POST['username'];
        $role_id = $_POST['role_id'];
        
        if ($id) {
            $sql = "UPDATE users SET username = :username, role_id = :role WHERE id = :id";
            $params = [':username' => $username, ':role' => $role_id, ':id' => $id];
            
            if (!empty($_POST['password'])) {
                $sql = "UPDATE users SET username = :username, role_id = :role, password_hash = :pass WHERE id = :id";
                $params[':pass'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            $db->prepare($sql)->execute($params);
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::dataChange('User', 'UPDATE', "id={$id}, username={$username}, role_id={$role_id}");
        } else {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO users (username, password_hash, role_id) VALUES (:u, :p, :r)")
               ->execute([':u' => $username, ':p' => $pass, ':r' => $role_id]);
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::dataChange('User', 'CREATE', "username={$username}, role_id={$role_id}");
        }
        redirect("/dev-panel/users?success=1");
        exit;
    }

    // --- APP DELEGATION ---
    public static function appDelegation() {
        self::checkDev();
        $db = Database::connect('core');
        $delegations = $db->query("
            SELECT a.id, a.app_code, u.username, u.id as user_id, g.nama as guru_nama
            FROM app_admins a 
            JOIN users u ON a.user_id = u.id
            LEFT JOIN guru g ON u.id = g.user_id
            ORDER BY a.app_code ASC, g.nama ASC, u.username ASC
        ")->fetchAll();
        
        $gurus = $db->query("
            SELECT u.id, u.username, g.nama 
            FROM users u 
            LEFT JOIN guru g ON u.id = g.user_id 
            WHERE u.role_id = 2 
            ORDER BY g.nama ASC, u.username ASC
        ")->fetchAll();
        $apps = [
            'cbt' => 'GHCBT System',
            'siakad' => 'SIAKAD',
            'keuangan' => 'Keuangan',
            'tabungan' => 'Tabungan',
            'absensi' => 'Absensi',
            'eraport' => 'E-Raport',
            'elearning' => 'E-Learning',
            'persuratan' => 'Persuratan',
            'inventaris' => 'Inventaris',
            'perpustakaan' => 'Perpustakaan'
        ];
        
        $active_menu = 'delegation';
        ob_start();
        include __DIR__ . '/../../resources/views/developer/delegation_view.php';
        $content = ob_get_clean();
        
        $title = "App Delegation - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function saveDelegation() {
        self::checkDev();
        $db = Database::connect('core');
        $user_id = $_POST['user_id'];
        $app_code = $_POST['app_code'];
        
        try {
            $db->prepare("INSERT IGNORE INTO app_admins (user_id, app_code) VALUES (:u, :a)")
               ->execute([':u' => $user_id, ':a' => $app_code]);
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::dataChange('Delegation', 'CREATE', "user_id={$user_id}, app={$app_code}");
        } catch (\PDOException $e) {}
        
        redirect("/dev-panel/delegation?success=1");
        exit;
    }

    public static function deleteDelegation($id) {
        self::checkDev();
        $db = Database::connect('core');
        $db->prepare("DELETE FROM app_admins WHERE id = :id")->execute([':id' => $id]);
        // â”€â”€ Audit Log â”€â”€
        AuditLogger::dataChange('Delegation', 'DELETE', "id={$id}");
        redirect("/dev-panel/delegation?deleted=1");
        exit;
    }

    // --- ACCESS MATRIX ---
    public static function access() {
        self::checkDev();
        $configFile = __DIR__ . '/../../config/menu_permissions.json';
        $permissions = json_decode(file_get_contents($configFile), true);
        
        $active_menu = 'access';
        ob_start();
        include __DIR__ . '/../../resources/views/developer/access_view.php';
        $content = ob_get_clean();
        
        $title = "Matrix Hak Akses - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function saveAccess() {
        self::checkDev();
        $configFile = __DIR__ . '/../../config/menu_permissions.json';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPermissions = array(
                'siakad' => array(),
                'cbt' => array()
            );
            
            if (isset($_POST['perms'])) {
                foreach ($_POST['perms'] as $module => $menus) {
                    foreach ($menus as $menu => $roles) {
                        $newPermissions[$module][$menu] = array_map('intval', $roles);
                    }
                }
            }
            
            file_put_contents($configFile, json_encode($newPermissions, JSON_PRETTY_PRINT));
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::systemAction('Hak Akses diperbarui', 'menu_permissions.json updated');
        }

        redirect("/dev-panel/access?success=1");
        exit;
    }

    // --- KEYMASTER PRO (LICENSES) ---
    public static function licenses() {
        self::checkDev();
        $db = Database::connect('core');
        $licenses = $db->query("SELECT * FROM licenses ORDER BY created_at DESC")->fetchAll();
        
        $active_menu = 'licenses';
        ob_start();
        include __DIR__ . '/../../resources/views/developer/licenses_view.php';
        $content = ob_get_clean();
        
        $title = "Keymaster Pro - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function saveLicense() {
        self::checkDev();
        $db = Database::connect('core');
        
        if (isset($_POST['generate'])) {
            $domain = strtolower(trim($_POST['domain']));
            $expired = $_POST['expired_at'];
            
            // Algorithmic Key Generation
            $salt = "GH-SSS-SULTAN-2026";
            $hash = md5($domain . $salt);
            $key = 'GHSSS-' . strtoupper(substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4));
            
            $db->prepare("INSERT INTO licenses (activation_code, domain, status, expired_at) VALUES (?, ?, 'aktif', ?)
                          ON DUPLICATE KEY UPDATE domain = VALUES(domain), status = 'aktif', expired_at = VALUES(expired_at)")
               ->execute([$key, $domain, $expired]);
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::dataChange('License', 'GENERATE', "domain={$domain}, key={$key}, expired={$expired}");
        }
        
        if (isset($_POST['revoke'])) {
            $id = $_POST['id'];
            $db->prepare("UPDATE licenses SET status = 'nonaktif' WHERE id = ?")->execute([$id]);
            // â”€â”€ Audit Log â”€â”€
            AuditLogger::dataChange('License', 'REVOKE', "id={$id}");
        }

        redirect("/dev-panel/licenses?success=1");
        exit;
    }

    // --- PORTAL CARD MANAGER ---
    public static function portalCards() {
        self::checkDev();
        $configFile = __DIR__ . '/../../config/portal_cards.json';
        $config = json_decode(file_get_contents($configFile), true);
        $cards = $config['cards'] ?? [];

        $active_menu = 'portal_cards';
        ob_start();
        include __DIR__ . '/../../resources/views/developer/portal_cards_view.php';
        $content = ob_get_clean();

        $title = "Portal Card Manager - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function savePortalCards() {
        self::checkDev();
        $configFile = __DIR__ . '/../../config/portal_cards.json';
        $existing = json_decode(file_get_contents($configFile), true);
        $allCardKeys = array_keys($existing['cards']);

        $posted = isset($_POST['cards']) ? $_POST['cards'] : [];
        $newCards = [];

        foreach ($allCardKeys as $key) {
            $enabled = isset($posted[$key]['enabled']) && intval($posted[$key]['enabled']) === 1;
            $roles   = [];
            if (isset($posted[$key]['roles'])) {
                $roles = array_map('intval', $posted[$key]['roles']);
            }
            $newCards[$key] = [
                'label'   => $existing['cards'][$key]['label'],
                'enabled' => $enabled,
                'roles'   => $roles,
            ];
        }

        $newConfig = ['cards' => $newCards];
        file_put_contents($configFile, json_encode($newConfig, JSON_PRETTY_PRINT));

        AuditLogger::systemAction('Portal Cards', 'Pengaturan card portal diperbarui');
        redirect('/dev-panel/portal-cards?saved=1');
        exit;
    }

    // --- TUNNEL MANAGER ---

    // Helper: load all tunnels from config
    private static function loadTunnelConfig(): array {
        $path = __DIR__ . '/../../config/cloudflare_config.json';
        if (!file_exists($path)) return [];
        $data = json_decode(file_get_contents($path), true);
        return (is_array($data) && isset($data['tunnels'])) ? $data['tunnels'] : [];
    }

    // Helper: save all tunnels to config
    private static function saveTunnelConfig(array $tunnels): void {
        $path = __DIR__ . '/../../config/cloudflare_config.json';
        file_put_contents($path, json_encode(['tunnels' => array_values($tunnels)], JSON_PRETTY_PRINT));
    }

    // Helper: rebuild Apache VirtualHosts
    private static function rebuildApacheVirtualHosts(array $tunnels): bool {
        $vhostContent = "# Auto-generated by GH-SSS Developer Panel\n";
        $vhostContent .= "# Do not edit manually, changes will be overwritten!\n\n";
        
        $hasDomain = false;
        foreach ($tunnels as $t) {
            if (!empty($t['domain']) && !empty($t['target_folder'])) {
                $hasDomain = true;
                $domain = trim($t['domain']);
                $folder = trim($t['target_folder'], '/\\');
                $folder = str_replace(['..', ':'], '', $folder); // security sanitize
                
                $vhostContent .= "<VirtualHost *:80>\n";
                $vhostContent .= "    DocumentRoot \"C:/xampp/htdocs/{$folder}\"\n";
                $vhostContent .= "    ServerName {$domain}\n";
                $vhostContent .= "    <Directory \"C:/xampp/htdocs/{$folder}\">\n";
                $vhostContent .= "        Options Indexes FollowSymLinks Includes ExecCGI\n";
                $vhostContent .= "        AllowOverride All\n";
                $vhostContent .= "        Require all granted\n";
                $vhostContent .= "    </Directory>\n";
                $vhostContent .= "</VirtualHost>\n\n";
            }
        }
        
        $path = 'C:/xampp/apache/conf/extra/httpd-vhosts-tunnels.conf';
        if ($hasDomain) {
            @file_put_contents($path, $vhostContent);
            return true;
        } else {
            @file_put_contents($path, "# No tunnels configured with domains.\n");
            return true; // Still true because we updated it
        }
    }

    // Helper: load PID map { tunnel_id => pid }
    private static function loadPidMap(): array {
        $path = __DIR__ . '/../../storage/cloudflare_pids.json';
        if (!file_exists($path)) return [];
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }

    // Helper: save PID map
    private static function savePidMap(array $pids): void {
        $path = __DIR__ . '/../../storage/cloudflare_pids.json';
        file_put_contents($path, json_encode($pids, JSON_PRETTY_PRINT));
    }

    // Helper: check if a PID is still running
    private static function isPidRunning(int $pid): bool {
        $out = [];
        exec('tasklist /FI "PID eq ' . $pid . '" /NH 2>NUL', $out);
        foreach ($out as $line) {
            if (strpos($line, (string)$pid) !== false) return true;
        }
        return false;
    }

    // Helper: build running status map { tunnel_id => bool }
    private static function buildStatusMap(array $tunnels): array {
        $pidMap = self::loadPidMap();
        $status = [];
        foreach ($tunnels as $t) {
            $id  = $t['id'];
            $pid = isset($pidMap[$id]) ? (int)$pidMap[$id] : 0;
            $status[$id] = ($pid > 0) ? self::isPidRunning($pid) : false;
        }
        return $status;
    }

    public static function tunnels() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        $tunnels   = self::loadTunnelConfig();
        $statusMap = self::buildStatusMap($tunnels);

        $active_menu = 'tunnels';
        $title = "Cloudflare Tunnels - Developer Panel";

        ob_start();
        include __DIR__ . '/../../resources/views/developer/tunnels.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function tunnelControl() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        header('Content-Type: application/json');

        $action   = isset($_POST['action'])    ? trim($_POST['action'])    : '';
        $tunnelId = isset($_POST['tunnel_id']) ? trim($_POST['tunnel_id']) : '';

        if (empty($tunnelId)) {
            echo json_encode(['success' => false, 'message' => 'tunnel_id tidak ditemukan.']);
            exit;
        }

        $tunnels = self::loadTunnelConfig();
        $tunnel  = null;
        foreach ($tunnels as $t) {
            if ($t['id'] === $tunnelId) { $tunnel = $t; break; }
        }

        if (!$tunnel) {
            echo json_encode(['success' => false, 'message' => 'Tunnel tidak ditemukan.']);
            exit;
        }

        $pidMap = self::loadPidMap();

        try {
            if ($action === 'start') {
                $token = trim($tunnel['token'] ?? '');
                if (empty($token)) {
                    echo json_encode(['success' => false, 'message' => 'Token tunnel belum diisi.']);
                    exit;
                }

                // Cari full path cloudflared.exe
                $cfPaths = [
                    'c:\\xampp\\htdocs\\mismifhda\\cloudflared.exe',
                    'c:\\xampp\\htdocs\\cloudflared.exe',
                    'c:\\cloudflared\\cloudflared.exe',
                    'c:\\Program Files\\cloudflared\\cloudflared.exe',
                    'c:\\Windows\\System32\\cloudflared.exe',
                    'cloudflared.exe', // fallback PATH
                ];
                $cfExe = 'cloudflared.exe';
                foreach ($cfPaths as $p) {
                    if ($p === 'cloudflared.exe') { $cfExe = $p; break; }
                    if (file_exists($p)) { $cfExe = $p; break; }
                }

                $logFile = 'c:\\xampp\\htdocs\\cloudflared_' . $tunnelId . '.log';
                @unlink($logFile);

                // Tulis PS1 ke temp dir
                $scriptPath = sys_get_temp_dir() . '\\cf_start_' . $tunnelId . '.ps1';
                $psScript  = '$p = Start-Process -FilePath "' . str_replace('\\', '\\\\', $cfExe) . '"';
                $psScript .= ' -ArgumentList @("tunnel", "--no-autoupdate", "run", "--token", "' . addslashes($token) . '")';
                $psScript .= ' -PassThru -WindowStyle Hidden;' . "\r\n";
                $psScript .= 'if ($p) { Write-Output $p.Id } else { Write-Output 0 }';
                file_put_contents($scriptPath, $psScript);

                $pidOut = [];
                exec('powershell -NoProfile -ExecutionPolicy Bypass -File "' . $scriptPath . '" 2>NUL', $pidOut);
                @unlink($scriptPath);

                $pid = intval(trim(implode('', $pidOut)));

                // Fallback: jika PID 0, cari via wmic dengan token substring
                if ($pid <= 0) {
                    sleep(2);
                    $tokenSnippet = addslashes(substr($token, 0, 20));
                    $wmicOut = [];
                    exec('wmic process where "name=\'cloudflared.exe\'" get processid /value 2>NUL', $wmicOut);
                    // Ambil PID terbesar (proses paling baru)
                    $pids = [];
                    foreach ($wmicOut as $line) {
                        if (preg_match('/ProcessId=(\d+)/', $line, $m) && intval($m[1]) > 0) {
                            $pids[] = intval($m[1]);
                        }
                    }
                    // Kecualikan PID yang sudah dipakai tunnel lain
                    $usedPids = array_values($pidMap);
                    foreach (array_reverse($pids) as $candidate) {
                        if (!in_array($candidate, $usedPids)) {
                            $pid = $candidate;
                            break;
                        }
                    }
                }

                if ($pid > 0) {
                    $pidMap[$tunnelId] = $pid;
                    self::savePidMap($pidMap);
                }

                \App\Services\AuditLogger::tunnelAction('cloudflare:' . ($tunnel['domain'] ?? $tunnelId), 'START');
                echo json_encode(['success' => true, 'pid' => $pid]);

            } else if ($action === 'stop') {
                $pid = isset($pidMap[$tunnelId]) ? (int)$pidMap[$tunnelId] : 0;
                if ($pid > 0) {
                    exec('taskkill /F /PID ' . $pid . ' 2>NUL');
                }
                unset($pidMap[$tunnelId]);
                self::savePidMap($pidMap);

                \App\Services\AuditLogger::tunnelAction('cloudflare:' . ($tunnel['domain'] ?? $tunnelId), 'STOP');
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Action tidak valid.']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public static function tunnelSave() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        header('Content-Type: application/json');

        $id          = isset($_POST['id'])           ? trim($_POST['id'])           : '';
        $name        = isset($_POST['name'])         ? trim($_POST['name'])         : '';
        $token       = isset($_POST['token'])        ? trim($_POST['token'])        : '';
        $domain      = isset($_POST['domain'])       ? trim($_POST['domain'])       : '';
        $localTarget = isset($_POST['local_target']) ? trim($_POST['local_target']) : 'http://localhost';
        $targetFolder= isset($_POST['target_folder'])? trim($_POST['target_folder']) : 'mismifhda';

        // Auto-strip prefix jika user paste seluruh command dari Cloudflare Dashboard
        // e.g. "cloudflared.exe service install eyJh..." -> "eyJh..."
        if (preg_match('/(?:service\s+install|tunnel\s+run\s+--token)\s+(\S+)/i', $token, $m)) {
            $token = $m[1];
        }

        if (empty($token)) {
            echo json_encode(['success' => false, 'message' => 'Token tidak boleh kosong.']);
            exit;
        }

        // Normalize domain - strip http/https prefix
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        if (empty($name)) {
            $name = $domain ?: 'Tunnel ' . date('d/m H:i');
        }

        $tunnels = self::loadTunnelConfig();

        if (!empty($id)) {
            // Update existing
            $found = false;
            foreach ($tunnels as &$t) {
                if ($t['id'] === $id) {
                    $t['name']         = $name;
                    $t['token']        = $token;
                    $t['domain']       = $domain;
                    $t['local_target'] = $localTarget;
                    $t['target_folder']= $targetFolder;
                    $found = true;
                    break;
                }
            }
            unset($t);
            if (!$found) {
                echo json_encode(['success' => false, 'message' => 'Tunnel tidak ditemukan.']);
                exit;
            }
        } else {
            // Create new with unique ID
            $id = 'cf_' . uniqid();
            $tunnels[] = [
                'id'           => $id,
                'name'         => $name,
                'token'        => $token,
                'domain'       => $domain,
                'local_target' => $localTarget,
                'target_folder'=> $targetFolder,
                'auto_start'   => false,
                'created_at'   => date('Y-m-d H:i:s'),
            ];
        }

        try {
            self::saveTunnelConfig($tunnels);
            $apacheRebuilt = self::rebuildApacheVirtualHosts($tunnels);
            \App\Services\AuditLogger::tunnelAction('cloudflare_config', 'UPDATE');
            echo json_encode(['success' => true, 'id' => $id, 'apache_rebuilt' => $apacheRebuilt]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        exit;
    }

    public static function tunnelDelete() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? trim($_POST['id']) : '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan.']);
            exit;
        }

        // Stop if running
        $pidMap = self::loadPidMap();
        if (isset($pidMap[$id])) {
            $pid = (int)$pidMap[$id];
            if ($pid > 0) exec('taskkill /F /PID ' . $pid . ' 2>NUL');
            unset($pidMap[$id]);
            self::savePidMap($pidMap);
        }

        // Remove from config
        $tunnels = self::loadTunnelConfig();
        $tunnels = array_filter($tunnels, fn($t) => $t['id'] !== $id);

        try {
            self::saveTunnelConfig($tunnels);
            $apacheRebuilt = self::rebuildApacheVirtualHosts($tunnels);
            \App\Services\AuditLogger::tunnelAction('cloudflare_config', 'DELETE');
            echo json_encode(['success' => true, 'apache_rebuilt' => $apacheRebuilt]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
        exit;
    }

    public static function tunnelToggleAutoStart() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        header('Content-Type: application/json');

        $id    = isset($_POST['id'])    ? trim($_POST['id'])    : '';
        $value = isset($_POST['value']) ? ($_POST['value'] === 'true' || $_POST['value'] === '1') : false;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan.']);
            exit;
        }

        $tunnels = self::loadTunnelConfig();
        $found   = false;
        foreach ($tunnels as &$t) {
            if ($t['id'] === $id) {
                $t['auto_start'] = $value;
                $found = true;
                break;
            }
        }
        unset($t);

        if (!$found) {
            echo json_encode(['success' => false, 'message' => 'Tunnel tidak ditemukan.']);
            exit;
        }

        self::saveTunnelConfig($tunnels);
        echo json_encode(['success' => true, 'auto_start' => $value]);
        exit;
    }

    public static function tunnelSetupAutoStart() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die("Unauthorized access.");

        header('Content-Type: application/json');

        $ps1Path = __DIR__ . '/../../storage/cf_autostart.ps1';
        $batPath = __DIR__ . '/../../storage/setup_autostart.bat';

        if (!file_exists($ps1Path)) {
            echo json_encode(['success' => false, 'message' => 'Script autostart tidak ditemukan.']);
            exit;
        }

        // Coba daftarkan Task Scheduler via PowerShell
        $psCmd = 'powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "';
        $psCmd .= '$a = New-ScheduledTaskAction -Execute powershell.exe -Argument \'\'-NoProfile -WindowStyle Hidden -ExecutionPolicy Bypass -File \"' . str_replace('\\', '\\\\', $ps1Path) . '\"\'\'\'; ';
        $psCmd .= '$t = New-ScheduledTaskTrigger -AtStartup; ';
        $psCmd .= '$s = New-ScheduledTaskSettingsSet -ExecutionTimeLimit (New-TimeSpan -Minutes 10) -StartWhenAvailable $true; ';
        $psCmd .= '$p = New-ScheduledTaskPrincipal -UserId SYSTEM -LogonType ServiceAccount -RunLevel Highest; ';
        $psCmd .= 'Register-ScheduledTask -TaskName \'MismiHDA - Cloudflare Tunnels\' -Action $a -Trigger $t -Settings $s -Principal $p -Force; ';
        $psCmd .= '"';

        $output = [];
        $code   = 0;
        exec($psCmd . ' 2>&1', $output, $code);

        if ($code === 0) {
            \App\Services\AuditLogger::tunnelAction('cloudflare_autostart', 'SETUP');
            echo json_encode(['success' => true, 'message' => 'Task Scheduler berhasil didaftarkan! Semua tunnel auto_start akan nyala saat Windows boot.']);
        } else {
            // Gagal otomatis, kembalikan path .bat untuk dijalankan manual
            echo json_encode([
                'success'    => false,
                'need_admin' => true,
                'bat_path'   => str_replace('/', '\\', $batPath),
                'message'    => 'Perlu dijalankan sebagai Administrator. Jalankan file .bat berikut sebagai admin.'
            ]);
        }
        exit;
    }
    public static function devicesApi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        require_once __DIR__ . '/../Services/OneSignalService.php';
        $app_id = \App\Services\OneSignalService::$APP_ID;
        $rest_key = \App\Services\OneSignalService::$REST_API_KEY;
        
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 8;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . $app_id . "&limit=" . $limit . "&offset=" . $offset);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $rest_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['players'])) {
                try {
                    $db = \App\Core\Database::connect('core');
                    foreach ($data['players'] as &$p) {
                        $p['nama_siswa'] = '';
                        if (!empty($p['external_user_id'])) {
                            $ext_id = $p['external_user_id'];
                            if (strpos($ext_id, 'GURU_') === 0) {
                                // Ini adalah Guru
                                $guruId = str_replace('GURU_', '', $ext_id);
                                $stmt = $db->prepare("SELECT nama FROM guru WHERE id = ? LIMIT 1");
                                $stmt->execute([$guruId]);
                                $s = $stmt->fetch();
                                if ($s) {
                                    $p['nama_siswa'] = $s['nama'] . ' (Guru)';
                                }
                            } else {
                                // Ini adalah Siswa
                                $stmt = $db->prepare("SELECT nama FROM siswa WHERE id = ? LIMIT 1");
                                $stmt->execute([$ext_id]);
                                $s = $stmt->fetch();
                                if ($s) {
                                    $p['nama_siswa'] = $s['nama'];
                                }
                            }
                        }
                    }
                    unset($p);
                    usort($data['players'], function($a, $b) {
                        $la = isset($a['last_active']) ? $a['last_active'] : 0;
                        $lb = isset($b['last_active']) ? $b['last_active'] : 0;
                        return $lb <=> $la;
                    });
                } catch (\Exception $e) {}
                echo json_encode($data);
            } else {
                echo $response;
            }
        } else {
            echo json_encode(['success' => false, 'players' => []]);
        }
        exit;
    }

    // â”€â”€ AUDIT LOG API â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public static function auditLogApi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');

        $limit  = min((int)($_GET['limit'] ?? 60), 200);
        $offset = max((int)($_GET['offset'] ?? 0), 0);
        $filter = strtoupper(trim($_GET['filter'] ?? ''));
        $search = strtolower(trim($_GET['search'] ?? ''));

        $entries = \App\Services\AuditLogger::getAll($limit, $offset);
        $totalCount = \App\Services\AuditLogger::getTotalCount();

        // Filter by category
        if ($filter && $filter !== 'ALL') {
            $entries = array_values(array_filter($entries, function($e) use ($filter) {
                return $e['category'] === $filter;
            }));
        }

        // Search by keyword
        if ($search) {
            $entries = array_values(array_filter($entries, function($e) use ($search) {
                return strpos(strtolower($e['message']), $search) !== false ||
                       strpos(strtolower($e['raw']), $search) !== false;
            }));
        }

        echo json_encode([
            'success' => true,
            'count' => count($entries),
            'totalCount' => $totalCount,
            'entries' => $entries
        ]);
        exit;
    }

    // â”€â”€ AUDIT LOG CLEAR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public static function auditLogClear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) die('Unauthorized');

        header('Content-Type: application/json');
        $logFile = __DIR__ . '/../../storage/logs/audit.log';
        @file_put_contents($logFile, '');
        AuditLogger::systemAction('Audit log dibersihkan oleh Developer');
        echo json_encode(['success' => true]);
        exit;
    }
    // â”€â”€ LIVE MONITOR API â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public static function liveMonitorApi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');

        $limit  = min((int)($_GET['limit'] ?? 6), 50);
        $offset = max((int)($_GET['offset'] ?? 0), 0);

        try {
            $db_core = \App\Core\Database::connect('core');
            $recentUsers = $db_core->query("
                SELECT u.id, u.username, u.role_id, u.last_activity,
                       TIMESTAMPDIFF(MINUTE, u.last_activity, NOW()) as mins_ago,
                       CASE WHEN u.role_id = 3 THEN (SELECT nama FROM siswa WHERE user_id = u.id LIMIT 1)
                             WHEN u.role_id = 2 THEN (SELECT nama FROM guru WHERE user_id = u.id LIMIT 1)
                            ELSE u.username END as nama
                FROM users u
                WHERE u.last_activity IS NOT NULL
                ORDER BY u.last_activity DESC
                LIMIT $limit OFFSET $offset
            ")->fetchAll();
            $totalCount = $db_core->query("SELECT COUNT(*) FROM users WHERE last_activity IS NOT NULL")->fetchColumn();
            
            echo json_encode(['success' => true, 'users' => $recentUsers, 'totalCount' => $totalCount]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // â”€â”€ APK DEVICES MANAGER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public static function apkDevices() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            die("Unauthorized access.");
        }

        $db = \App\Core\Database::connect('core');
        $kelas_list = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY id ASC")->fetchAll();

        $active_menu = 'apk_devices';
        $title = "APK Device Manager - GH-SSS";
        ob_start();
        ?>
        <div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/developer/sidebar.php'; ?>
            <div class="siakad-main">
                <?php include __DIR__ . '/../../resources/views/developer/header.php'; ?>
                <?php include __DIR__ . '/../../resources/views/developer/apk_devices.php'; ?>
                <?php include __DIR__ . '/../../resources/views/developer/footer.php'; ?>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        // Luluskan kelas_list ke view
        $GLOBALS['kelas_list'] = $kelas_list;
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function apkDevicesApi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        $role_filter = isset($_GET['role']) ? $_GET['role'] : 'Semua';
        $kelas_filter = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : 'Semua';

        require_once __DIR__ . '/../Services/OneSignalService.php';
        $app_id = \App\Services\OneSignalService::$APP_ID;
        $rest_key = \App\Services\OneSignalService::$REST_API_KEY;
        
        $limit = 300;
        $offset = 0;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . $app_id . "&limit=" . $limit . "&offset=" . $offset);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $rest_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $api_devices = [];
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['players'])) {
                foreach ($data['players'] as $p) {
                    if (empty($p['external_user_id'])) continue;
                    $ext_id = $p['external_user_id'];
                    if (!isset($api_devices[$ext_id])) {
                        $api_devices[$ext_id] = [];
                    }
                    $api_devices[$ext_id][] = [
                        'device_model' => $p['device_model'] ? $p['device_model'] : 'Unknown Device',
                        'os' => $p['device_os'] ? $p['device_os'] : '',
                        'last_active' => isset($p['last_active']) ? $p['last_active'] : 0
                    ];
                }
            }
        }

        $final_list = [];
        try {
            $db = \App\Core\Database::connect('core');
            
            // 1. Fetch Guru
            if ($role_filter === 'Semua' || $role_filter === 'Guru') {
                $stmt = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC");
                while ($g = $stmt->fetch()) {
                    $ext_id = 'GURU_' . $g['id'];
                    $user_devices = isset($api_devices[$ext_id]) ? $api_devices[$ext_id] : [];
                    $final_list[] = [
                        'user_id' => $ext_id,
                        'nama' => $g['nama'],
                        'role' => 'Guru',
                        'devices' => $user_devices
                    ];
                }
            }
            
            // 2. Fetch Siswa
            if ($role_filter === 'Semua' || $role_filter === 'Siswa') {
                $sql = "SELECT id, nama, nis FROM siswa WHERE status = 'Aktif'";
                $params = [];
                if ($kelas_filter !== 'Semua') {
                    $sql .= " AND kelas_id = ?";
                    $params[] = $kelas_filter;
                }
                $sql .= " ORDER BY nama ASC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                while ($s = $stmt->fetch()) {
                    $ext_id = $s['id'];
                    $user_devices = isset($api_devices[$ext_id]) ? $api_devices[$ext_id] : [];
                    $final_list[] = [
                        'user_id' => $ext_id,
                        'nama' => $s['nama'],
                        'role' => 'Siswa',
                        'devices' => $user_devices
                    ];
                }
            }
            
            // Sort combined list alphabetically by nama
            usort($final_list, function($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            echo json_encode(['success' => true, 'data' => $final_list]);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // --- APK PHONE EMULATOR ---
    public static function apkPreview() {
        self::checkDev();
        $active_menu = 'apk_preview';
        
        ob_start();
        include __DIR__ . '/../../resources/views/developer/apk_preview_view.php';
        $content = ob_get_clean();
        
        $title = "APK Phone Emulator - Developer Panel";
        include __DIR__ . '/../../resources/views/layout.php';
    }
}
