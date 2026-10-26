<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <?php include __DIR__ . '/header.php'; ?>

        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding: 0 0.5rem; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #f59e0b; text-shadow: 0 0 20px rgba(245,158,11,0.35); font-size: 1.5rem;">
                    <i data-lucide="cloud-cog"></i> Cloudflare Tunnel Manager
                </h2>
                <p style="opacity: 0.55; margin: 5px 0 0; color: #94a3b8; font-size: 0.85rem;">
                    Kelola banyak domain sekaligus. Setiap tunnel berjalan sebagai proses terpisah di Windows.
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button onclick="setupAutoStart()" id="btnSetupAutoStart" style="background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.3); padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: flex; align-items: center; gap: 7px; transition: 0.3s;">
                    <i data-lucide="zap"></i> Setup Auto-Start
                </button>
                <button onclick="openModal()" id="btnAddTunnel" style="background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.15)); color: #fbbf24; border: 1px solid rgba(245,158,11,0.35); padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 0 20px rgba(245,158,11,0.1);">
                    <i data-lucide="plus-circle"></i> Tambah Tunnel
                </button>
            </div>
        </div>

        <?php if (empty($tunnels)): ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: 4rem 2rem; background: rgba(245,158,11,0.03); border: 2px dashed rgba(245,158,11,0.15); border-radius: 20px;">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.4;">â˜ï¸</div>
            <h3 style="color: #94a3b8; margin: 0 0 8px; font-size: 1.1rem;">Belum ada tunnel yang dikonfigurasi</h3>
            <p style="color: #475569; font-size: 0.85rem; margin: 0 0 1.5rem;">Klik tombol <strong style="color:#fbbf24;">Tambah Tunnel</strong> untuk menambahkan domain pertama Anda.</p>
            <button onclick="openModal()" style="background: rgba(245,158,11,0.1); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); padding: 10px 22px; border-radius: 10px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="plus"></i> Tambah Tunnel Pertama
            </button>
        </div>
        <?php else: ?>

        <!-- Tunnel Cards Grid -->
        <div id="tunnelGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.25rem;">
            <?php foreach ($tunnels as $tunnel):
                $tid       = $tunnel['id'];
                $isAlive   = isset($statusMap[$tid]) && $statusMap[$tid];
                $domain    = $tunnel['domain']       ?? '';
                $target    = $tunnel['local_target'] ?? 'http://localhost';
                $name      = $tunnel['name']         ?? $domain;
                $token     = $tunnel['token']        ?? '';
                $autoStart = !empty($tunnel['auto_start']);
                $tokenMask = !empty($token) ? substr($token, 0, 8) . '...' . substr($token, -8) : '(kosong)';
            ?>
            <div class="tunnel-card" id="card-<?php echo htmlspecialchars($tid); ?>" style="background: rgba(15,23,42,0.9); border: 1px solid <?php echo $isAlive ? 'rgba(16,185,129,0.3)' : 'rgba(245,158,11,0.15)'; ?>; border-radius: 16px; overflow: hidden; transition: box-shadow 0.3s; box-shadow: 0 8px 25px rgba(0,0,0,0.25), <?php echo $isAlive ? '0 0 20px rgba(16,185,129,0.08)' : '0 0 0 transparent'; ?>;">
                <!-- Card Header -->
                <div style="background: <?php echo $isAlive ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.07)'; ?>; padding: 1rem 1.25rem; border-bottom: 1px solid <?php echo $isAlive ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.1)'; ?>; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: <?php echo $isAlive ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.12)'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="cloud" style="width: 18px; color: <?php echo $isAlive ? '#34d399' : '#fbbf24'; ?>;"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($name); ?></div>
                            <?php if (!empty($domain)): ?>
                            <a href="https://<?php echo htmlspecialchars($domain); ?>" target="_blank" style="font-size: 0.75rem; color: <?php echo $isAlive ? '#34d399' : '#94a3b8'; ?>; text-decoration: none; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                <i data-lucide="external-link" style="width: 11px;"></i><?php echo htmlspecialchars($domain); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                        <?php if ($isAlive): ?>
                            <span style="background: rgba(16,185,129,0.15); color: #34d399; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; border: 1px solid rgba(16,185,129,0.3); display: inline-flex; align-items: center; gap: 5px;">
                                <div style="width:6px;height:6px;border-radius:50%;background:#34d399;animation:cfpulse 2s infinite;"></div> ON
                            </span>
                        <?php else: ?>
                            <span style="background: rgba(100,116,139,0.15); color: #64748b; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; border: 1px solid rgba(100,116,139,0.2); display: inline-flex; align-items: center; gap: 5px;">
                                <div style="width:6px;height:6px;border-radius:50%;background:#64748b;"></div> OFF
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Body -->
                <div style="padding: 1.1rem 1.25rem;">
                    <!-- Info rows -->
                    <div style="display: flex; flex-direction: column; gap: 7px; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem;">
                            <i data-lucide="arrow-right-circle" style="width: 13px; color: #64748b; flex-shrink:0;"></i>
                            <span style="color: #64748b;">Target:</span>
                            <code style="color: #a3e635; font-size: 0.78rem; background: rgba(163,230,53,0.08); padding: 2px 6px; border-radius: 5px;"><?php echo htmlspecialchars($target); ?></code>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem;">
                            <i data-lucide="folder" style="width: 13px; color: #64748b; flex-shrink:0;"></i>
                            <span style="color: #64748b;">Folder:</span>
                            <code style="color: #fbbf24; font-size: 0.78rem; background: rgba(245,158,11,0.08); padding: 2px 6px; border-radius: 5px;"><?php echo htmlspecialchars($tunnel['target_folder'] ?? 'mismifhda'); ?></code>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem;">
                            <i data-lucide="key" style="width: 13px; color: #64748b; flex-shrink:0;"></i>
                            <span style="color: #64748b;">Token:</span>
                            <code style="color: #94a3b8; font-size: 0.75rem; font-family: monospace;"><?php echo htmlspecialchars($tokenMask); ?></code>
                        </div>
                        <!-- Auto-Start Toggle -->
                        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,<?php echo $autoStart ? '0.25' : '0.1'; ?>); border-radius: 8px; padding: 7px 10px; margin-top: 2px;">
                            <span style="font-size: 0.78rem; color: <?php echo $autoStart ? '#a78bfa' : '#64748b'; ?>; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="zap" style="width: 12px;"></i> Auto-Start saat boot
                            </span>
                            <label class="cf-toggle" title="Toggle auto-start">
                                <input type="checkbox" id="as-<?php echo htmlspecialchars($tid); ?>" <?php echo $autoStart ? 'checked' : ''; ?> onchange="toggleAutoStart('<?php echo htmlspecialchars($tid); ?>', this.checked)">
                                <span class="cf-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php if ($isAlive): ?>
                            <button onclick="tunnelAction('<?php echo htmlspecialchars($tid); ?>', 'stop', this)" style="flex: 1; min-width: 100px; background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 9px 12px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: 0.3s;">
                                <i data-lucide="square" style="width: 12px; fill: currentColor;"></i> STOP
                            </button>
                        <?php else: ?>
                            <button onclick="tunnelAction('<?php echo htmlspecialchars($tid); ?>', 'start', this)" style="flex: 1; min-width: 100px; background: rgba(245,158,11,0.1); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); padding: 9px 12px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: 0.3s;">
                                <i data-lucide="play" style="width: 12px; fill: currentColor;"></i> START
                            </button>
                        <?php endif; ?>

                        <button onclick="openModal(<?php echo htmlspecialchars(json_encode($tunnel)); ?>)" style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.08); padding: 9px 12px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.3s;">
                            <i data-lucide="edit-2" style="width: 13px;"></i> Edit
                        </button>
                        <button onclick="deleteTunnel('<?php echo htmlspecialchars($tid); ?>', '<?php echo htmlspecialchars($name); ?>')" style="background: rgba(239,68,68,0.05); color: #f87171; border: 1px solid rgba(239,68,68,0.15); padding: 9px 10px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: 0.3s;">
                            <i data-lucide="trash-2" style="width: 13px;"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Info bar -->
        <div style="margin-top: 1.5rem; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.04); border-radius: 12px; padding: 12px 16px; display: flex; gap: 12px; align-items: flex-start; font-size: 0.8rem; color: #64748b;">
            <i data-lucide="info" style="width: 15px; color: #475569; flex-shrink: 0; margin-top: 1px;"></i>
            <div>
                Setiap tunnel berjalan sebagai <strong style="color:#94a3b8;">proses Windows terpisah</strong> dengan PID unik â€” bisa jalan banyak domain bersamaan.
                Routing domain ke local target dikelola manual di <strong style="color:#f59e0b;">Cloudflare Zero Trust Dashboard</strong>.
                Token didapat dari <strong style="color:#94a3b8;">Zero Trust â†’ Networks â†’ Tunnels â†’ [nama tunnel] â†’ Configure â†’ Install connector</strong>.
            </div>
        </div>
        <?php endif; ?>

        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<!-- Add / Edit Tunnel Modal -->
<div id="tunnelModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; display: none;">
    <div style="background: #0f172a; border: 1px solid rgba(245,158,11,0.25); border-radius: 20px; width: 100%; max-width: 520px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
        <!-- Modal Header -->
        <div style="background: rgba(245,158,11,0.1); padding: 1.1rem 1.5rem; border-bottom: 1px solid rgba(245,158,11,0.15); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin: 0; color: #fbbf24; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle"></i> Tambah Tunnel Baru
            </h3>
            <button onclick="closeModal()" style="background: none; border: none; color: #64748b; cursor: pointer; padding: 4px; border-radius: 6px; display: flex; align-items: center; transition: 0.2s;">
                <i data-lucide="x" style="width: 18px;"></i>
            </button>
        </div>
        <!-- Modal Body -->
        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <input type="hidden" id="modalTunnelId" value="">

            <div>
                <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 700;">Nama / Label Tunnel</label>
                <input type="text" id="modalName" placeholder="e.g. MI Miftahul Huda, Portal Sekolah..." style="width: 100%; padding: 10px 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.3); color: #f8fafc; font-size: 0.85rem; outline: none; box-sizing: border-box; transition: border-color 0.3s;" onfocus="this.style.borderColor='rgba(245,158,11,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 700;">
                    <span style="display: flex; align-items: center; gap: 5px;"><i data-lucide="key" style="width: 12px; color: #f59e0b;"></i> Cloudflare Tunnel Token <span style="color:#ef4444;">*</span></span>
                </label>
                <textarea id="modalToken" rows="3" placeholder="Paste token dari Cloudflare Zero Trust Dashboard..." style="width: 100%; padding: 10px 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.3); color: #f8fafc; font-size: 0.8rem; outline: none; resize: none; box-sizing: border-box; font-family: monospace; line-height: 1.5; transition: border-color 0.3s;" onfocus="this.style.borderColor='rgba(245,158,11,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'"></textarea>
                <small style="color: #475569; font-size: 0.73rem;">Dari: Zero Trust â†’ Networks â†’ Tunnels â†’ [Tunnel] â†’ Configure â†’ Install connector</small>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 700;">
                    <span style="display: flex; align-items: center; gap: 5px;"><i data-lucide="globe" style="width: 12px; color: #f59e0b;"></i> Domain Publik</span>
                </label>
                <input type="text" id="modalDomain" placeholder="e.g. mismifhda.my.id atau sekolah.my.id" style="width: 100%; padding: 10px 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.3); color: #f8fafc; font-size: 0.85rem; outline: none; box-sizing: border-box; transition: border-color 0.3s;" onfocus="this.style.borderColor='rgba(245,158,11,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                <small style="color: #475569; font-size: 0.73rem;">Hanya untuk label & link referensi. Routing dikelola di Cloudflare Dashboard.</small>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 700;">
                    <span style="display: flex; align-items: center; gap: 5px;"><i data-lucide="arrow-right-circle" style="width: 12px; color: #f59e0b;"></i> Arahkan ke (Local Target)</span>
                </label>
                <input type="text" id="modalLocalTarget" value="http://localhost" placeholder="e.g. http://localhost atau http://localhost:8080" style="width: 100%; padding: 10px 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.3); color: #f8fafc; font-size: 0.85rem; outline: none; box-sizing: border-box; transition: border-color 0.3s;" onfocus="this.style.borderColor='rgba(245,158,11,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                <small style="color: #475569; font-size: 0.73rem;">Alamat lokal tujuan. Tidak perlu path, cukup <code style="color:#a3e635;">http://localhost</code>.</small>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 700;">
                    <span style="display: flex; align-items: center; gap: 5px;"><i data-lucide="folder" style="width: 12px; color: #f59e0b;"></i> Target Folder (XAMPP htdocs)</span>
                </label>
                <input type="text" id="modalTargetFolder" value="mismifhda" placeholder="e.g. mismifhda atau mtsalikhlas" style="width: 100%; padding: 10px 13px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.3); color: #f8fafc; font-size: 0.85rem; outline: none; box-sizing: border-box; transition: border-color 0.3s;" onfocus="this.style.borderColor='rgba(245,158,11,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                <small style="color: #475569; font-size: 0.73rem;">Folder aplikasi di dalam <code>C:\xampp\htdocs\</code>. Apache akan diatur otomatis.</small>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 0.5rem;">
                <button onclick="closeModal()" style="flex: 1; background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.08); padding: 11px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;">
                    Batal
                </button>
                <button onclick="saveTunnel()" id="btnSaveTunnel" style="flex: 2; background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.15)); color: #fbbf24; border: 1px solid rgba(245,158,11,0.4); padding: 11px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s;">
                    <i data-lucide="save"></i> Simpan Tunnel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- PowerShell Modal -->
<div id="psModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); z-index: 10000; display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #0f172a; border: 1px solid rgba(139,92,246,0.3); border-radius: 20px; width: 100%; max-width: 600px; padding: 1.5rem; text-align: left; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
        <h3 style="margin: 0 0 10px; color: #a78bfa; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="terminal"></i> Selesaikan Instalasi Auto-Start
        </h3>
        <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.5;">
            Tunnel berhasil dibuat! Agar tunnel berjalan otomatis (sebagai Windows Service), silakan <b>COPY</b> perintah di bawah ini dan paste di <b>PowerShell Administrator</b>, lalu tekan Enter.
        </p>
        <textarea id="psCmdBox" readonly rows="4" style="width: 100%; padding: 12px; border-radius: 10px; background: #000; border: 1px solid #333; color: #a3e635; font-family: monospace; font-size: 0.8rem; resize: none; margin-bottom: 1rem; outline: none;"></textarea>
        <div style="display: flex; gap: 10px;">
            <button onclick="copyPsCmd()" style="flex: 1; background: #a78bfa; color: #fff; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;">
                <i data-lucide="copy"></i> Copy PowerShell Script
            </button>
            <button onclick="closePsModalAndReload()" style="background: rgba(255,255,255,0.1); color: #fff; padding: 12px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; border: none; transition: 0.2s;">
                Selesai / Tutup
            </button>
        </div>
    </div>
</div>

<!-- Custom Confirm Modal -->
<div id="zConfirmModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); z-index: 10001; display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #0f172a; border: 1px solid rgba(245,158,11,0.3); border-radius: 20px; width: 100%; max-width: 400px; padding: 1.5rem; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #fbbf24; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
            <i data-lucide="help-circle" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 id="zConfirmTitle" style="margin: 0 0 10px; color: #f8fafc; font-size: 1.1rem;">Konfirmasi</h3>
        <p id="zConfirmMessage" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;"></p>
        <div style="display: flex; gap: 10px;">
            <button onclick="zConfirmCancel()" style="flex: 1; background: rgba(255,255,255,0.05); color: #94a3b8; padding: 10px; border-radius: 10px; font-weight: bold; cursor: pointer; border: 1px solid rgba(255,255,255,0.1); transition: 0.2s;">
                Batal
            </button>
            <button id="zConfirmOkBtn" style="flex: 1; background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.15)); color: #fbbf24; padding: 10px; border-radius: 10px; font-weight: bold; cursor: pointer; border: 1px solid rgba(245,158,11,0.4); transition: 0.2s;">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="zAlertModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); z-index: 10001; display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #0f172a; border: 1px solid rgba(139,92,246,0.3); border-radius: 20px; width: 100%; max-width: 400px; padding: 1.5rem; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
        <div id="zAlertIconWrapper" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(139,92,246,0.1); color: #a78bfa; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
            <i data-lucide="info" id="zAlertIcon" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 id="zAlertTitle" style="margin: 0 0 10px; color: #f8fafc; font-size: 1.1rem;">Info</h3>
        <p id="zAlertMessage" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5; white-space: pre-wrap;"></p>
        <button onclick="zAlertClose()" style="width: 100%; background: rgba(139,92,246,0.2); color: #a78bfa; padding: 10px; border-radius: 10px; font-weight: bold; cursor: pointer; border: 1px solid rgba(139,92,246,0.4); transition: 0.2s;">
            Tutup
        </button>
    </div>
</div>

<style>
@keyframes cfpulse {
    0%   { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.5); }
    70%  { box-shadow: 0 0 0 5px rgba(52, 211, 153, 0); }
    100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
}
.tunnel-card:hover { box-shadow: 0 12px 35px rgba(0,0,0,0.4) !important; }
button:hover { filter: brightness(1.15); }

/* Toggle switch */
.cf-toggle { position: relative; display: inline-block; width: 36px; height: 20px; flex-shrink: 0; }
.cf-toggle input { opacity: 0; width: 0; height: 0; }
.cf-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 20px; transition: 0.3s; border: 1px solid rgba(255,255,255,0.1); }
.cf-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background: #64748b; border-radius: 50%; transition: 0.3s; }
.cf-toggle input:checked + .cf-slider { background: rgba(139,92,246,0.3); border-color: rgba(139,92,246,0.5); }
.cf-toggle input:checked + .cf-slider:before { transform: translateX(16px); background: #a78bfa; }
</style>

<script>
lucide.createIcons();

// â”€â”€ Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function openModal(data) {
    const modal = document.getElementById('tunnelModal');
    const title = document.getElementById('modalTitle');

    if (data && data.id) {
        title.innerHTML = '<i data-lucide="edit-2"></i> Edit Tunnel';
        document.getElementById('modalTunnelId').value    = data.id;
        document.getElementById('modalName').value        = data.name         || '';
        document.getElementById('modalToken').value       = data.token        || '';
        document.getElementById('modalDomain').value      = data.domain       || '';
        document.getElementById('modalLocalTarget').value = data.local_target || 'http://localhost';
        document.getElementById('modalTargetFolder').value = data.target_folder || 'mismifhda';
    } else {
        title.innerHTML = '<i data-lucide="plus-circle"></i> Tambah Tunnel Baru';
        document.getElementById('modalTunnelId').value    = '';
        document.getElementById('modalName').value        = '';
        document.getElementById('modalToken').value       = '';
        document.getElementById('modalDomain').value      = '';
        document.getElementById('modalLocalTarget').value = 'http://localhost';
        document.getElementById('modalTargetFolder').value = 'mismifhda';
    }

    modal.style.display = 'flex';
    lucide.createIcons();
}

function closeModal() {
    document.getElementById('tunnelModal').style.display = 'none';
}

document.getElementById('tunnelModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// â”€â”€ Save tunnel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function saveTunnel() {
    const id          = document.getElementById('modalTunnelId').value.trim();
    const name        = document.getElementById('modalName').value.trim();
    const token       = document.getElementById('modalToken').value.trim();
    const domain      = document.getElementById('modalDomain').value.trim();
    const localTarget = document.getElementById('modalLocalTarget').value.trim();
    const targetFolder= document.getElementById('modalTargetFolder').value.trim();

    if (!token) {
        if (window.ZNS) ZNS.showError('Token tidak boleh kosong!');
        else zAlert('Token tidak boleh kosong!', 'Gagal', true);
        return;
    }

    const btn = document.getElementById('btnSaveTunnel');
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="spin" style="width:16px;"></i> Menyimpan...';
    btn.disabled = true;
    lucide.createIcons();

    fetch(base_url('dev-panel/tunnels/save'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id, name, token, domain, local_target: localTarget, target_folder: targetFolder })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeModal();
            let msg = 'Tunnel berhasil disimpan di sistem XAMPP!';
            if (res.apache_rebuilt) {
                msg += '\n\nâš ï¸ PENTING: WAJIB Restart Apache dari XAMPP Control Panel sekarang agar domain baru bisa mengarah ke folder yang tepat!';
            }
            if (window.ZNS) ZNS.showSuccess('Tersimpan! Silakan copy script instalasi.');
            zAlert(msg, 'Berhasil', false, () => openPsModal(name, token));
        } else {
            if (window.ZNS) ZNS.showError(res.message);
            else zAlert(res.message, 'Gagal', true);
        }
        btn.innerHTML = oldHtml;
        btn.disabled = false;
        lucide.createIcons();
    })
    .catch(() => {
        if (window.ZNS) ZNS.showError('Kesalahan jaringan.');
        else zAlert('Kesalahan jaringan.', 'Gagal', true);
        btn.innerHTML = oldHtml;
        btn.disabled = false;
        lucide.createIcons();
    });
}

// â”€â”€ Start / Stop â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function tunnelAction(tunnelId, action, btn) {
    if (action === 'stop') {
        zConfirm('Yakin ingin menghentikan tunnel ini?', function() {
            _executeTunnelAction(tunnelId, action, btn);
        });
    } else {
        _executeTunnelAction(tunnelId, action, btn);
    }
}

function _executeTunnelAction(tunnelId, action, btn) {
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="spin" style="width:12px;"></i> ...';
    btn.disabled = true;
    lucide.createIcons();

    fetch(base_url('dev-panel/tunnels/control'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ tunnel_id: tunnelId, action: action })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const label = action === 'start' ? 'Tunnel dimulai!' : 'Tunnel dihentikan!';
            if (window.ZNS) {
                ZNS.showSuccess(label);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                zAlert(label, 'Berhasil', false, () => window.location.reload());
            }
        } else {
            if (window.ZNS) ZNS.showError(res.message || 'Gagal.');
            else zAlert(res.message || 'Unknown', 'Gagal', true);
            btn.innerHTML = oldHtml;
            btn.disabled = false;
            lucide.createIcons();
        }
    })
    .catch(() => {
        if (window.ZNS) ZNS.showError('Kesalahan jaringan.');
        else zAlert('Kesalahan jaringan.', 'Gagal', true);
        btn.innerHTML = oldHtml;
        btn.disabled = false;
        lucide.createIcons();
    });
}

// â”€â”€ Delete â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function deleteTunnel(tunnelId, name) {
    zConfirm('Hapus tunnel "' + name + '"? Jika sedang berjalan akan otomatis dihentikan.', function() {
        _executeDeleteTunnel(tunnelId, name);
    });
}

function _executeDeleteTunnel(tunnelId, name) {
    fetch(base_url('dev-panel/tunnels/delete'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id: tunnelId })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            let msg = 'Tunnel dihapus!';
            if (res.apache_rebuilt) {
                msg += '\n\nâš ï¸ Konfigurasi Apache telah diupdate.\nSilakan Restart Apache dari XAMPP Control Panel.';
            }
            if (window.ZNS) ZNS.showSuccess('Tunnel dihapus!');
            zAlert(msg, 'Berhasil', false, () => window.location.reload());
        } else {
            if (window.ZNS) ZNS.showError(res.message);
            else zAlert(res.message, 'Gagal', true);
        }
    })
    .catch(() => {
        if (window.ZNS) ZNS.showError('Kesalahan jaringan.');
        else zAlert('Kesalahan jaringan.', 'Gagal', true);
    });
}

// â”€â”€ Toggle Auto-Start â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function toggleAutoStart(tunnelId, value) {
    fetch(base_url('dev-panel/tunnels/toggle-autostart'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id: tunnelId, value: value ? 'true' : 'false' })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const label = value ? 'Auto-start diaktifkan âœ…' : 'Auto-start dinonaktifkan';
            if (window.ZNS) ZNS.showSuccess(label);
            else zAlert(label, 'Berhasil', false);
        } else {
            if (window.ZNS) ZNS.showError(res.message);
            else zAlert(res.message, 'Gagal', true);
            // Kembalikan state checkbox
            document.getElementById('as-' + tunnelId).checked = !value;
        }
    })
    .catch(() => {
        document.getElementById('as-' + tunnelId).checked = !value;
        if (window.ZNS) ZNS.showError('Kesalahan jaringan.');
        else zAlert('Kesalahan jaringan.', 'Gagal', true);
    });
}

// â”€â”€ Setup Auto-Start (Task Scheduler) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function setupAutoStart() {
    const btn = document.getElementById('btnSetupAutoStart');
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="spin" style="width:14px;"></i> Mendaftarkan...';
    btn.disabled = true;
    lucide.createIcons();

    fetch(base_url('dev-panel/tunnels/setup-autostart'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(r => r.json())
    .then(res => {
        btn.innerHTML = oldHtml;
        btn.disabled = false;
        lucide.createIcons();

        if (res.success) {
            zAlert(res.message, 'Berhasil', false);
        } else if (res.need_admin) {
            // Perlu admin â€” tampilkan instruksi
            const msg = 'Jalankan file berikut sebagai Administrator:\n\n' +
                res.bat_path + '\n\n' +
                'Caranya:\n' +
                '1. Buka File Explorer\n' +
                '2. Navigasi ke file tersebut\n' +
                '3. Klik kanan â†’ "Run as administrator"\n' +
                '4. Tekan Enter / klik OK\n\n' +
                'Cukup dilakukan SEKALI. Setelah itu semua tunnel auto_start akan nyala otomatis saat Windows boot.';
            zAlert(msg, 'Perlu akses Administrator!', true);
        } else {
            zAlert(res.message, 'Gagal', true);
        }
    })
    .catch(() => {
        btn.innerHTML = oldHtml;
        btn.disabled = false;
        lucide.createIcons();
        if (window.ZNS) ZNS.showError('Kesalahan jaringan.');
        else zAlert('Kesalahan jaringan.', 'Gagal', true);
    });
}

// â”€â”€ PowerShell Auto-Start Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function openPsModal(name, token) {
    const safeName = name.replace(/[^a-zA-Z0-9]/g, '');
    const cmd = `sc.exe create Cloudflared_${safeName} binPath= "C:\\xampp\\htdocs\\mismifhda\\cloudflared.exe tunnel run --token ${token}" start= auto DisplayName= "Cloudflare Tunnel - ${name}"\nStart-Service Cloudflared_${safeName}`;
    document.getElementById('psCmdBox').value = cmd;
    document.getElementById('psModal').style.display = 'flex';
    lucide.createIcons();
}

function copyPsCmd() {
    const box = document.getElementById('psCmdBox');
    box.select();
    document.execCommand('copy');
    const btn = document.querySelector('#psModal button[onclick="copyPsCmd()"]');
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check"></i> Tercopy!';
    btn.style.background = '#34d399';
    lucide.createIcons();
    setTimeout(() => { btn.innerHTML = oldHtml; btn.style.background = '#a78bfa'; lucide.createIcons(); }, 2000);
}

function closePsModalAndReload() {
    document.getElementById('psModal').style.display = 'none';
    window.location.reload();
}

// â”€â”€ Custom Modals Logic â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
let _zConfirmCallback = null;
let _zAlertCallback = null;
function zConfirm(message, callback) {
    document.getElementById('zConfirmMessage').innerText = message;
    _zConfirmCallback = callback;
    document.getElementById('zConfirmModal').style.display = 'flex';
    lucide.createIcons();
}
function zConfirmCancel() {
    document.getElementById('zConfirmModal').style.display = 'none';
    _zConfirmCallback = null;
}
document.getElementById('zConfirmOkBtn').addEventListener('click', function() {
    document.getElementById('zConfirmModal').style.display = 'none';
    if (_zConfirmCallback) _zConfirmCallback();
});

function zAlert(message, title = 'Info', isError = false, callback = null) {
    _zAlertCallback = callback;
    document.getElementById('zAlertTitle').innerText = title;
    document.getElementById('zAlertMessage').innerText = message;
    
    const iconWrap = document.getElementById('zAlertIconWrapper');
    const icon = document.getElementById('zAlertIcon');
    const btn = document.querySelector('#zAlertModal button');
    
    if (isError) {
        iconWrap.style.background = 'rgba(239,68,68,0.1)';
        iconWrap.style.color = '#f87171';
        icon.setAttribute('data-lucide', 'alert-triangle');
        btn.style.background = 'rgba(239,68,68,0.2)';
        btn.style.color = '#f87171';
        btn.style.borderColor = 'rgba(239,68,68,0.4)';
    } else {
        iconWrap.style.background = 'rgba(139,92,246,0.1)';
        iconWrap.style.color = '#a78bfa';
        icon.setAttribute('data-lucide', 'info');
        btn.style.background = 'rgba(139,92,246,0.2)';
        btn.style.color = '#a78bfa';
        btn.style.borderColor = 'rgba(139,92,246,0.4)';
    }
    
    document.getElementById('zAlertModal').style.display = 'flex';
    lucide.createIcons();
}
function zAlertClose() {
    document.getElementById('zAlertModal').style.display = 'none';
    if (_zAlertCallback) _zAlertCallback();
}
</script>
