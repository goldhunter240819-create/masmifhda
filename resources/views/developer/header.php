<nav class="navbar navbar-home" style="margin-bottom: 2rem; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius:18px; padding:0.8rem 1.5rem; display:flex; align-items:center; justify-content:space-between; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.05); box-sizing: border-box;">
    <a href="<?php echo base_url('dev-panel'); ?>" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
        <div class="dev-pulse-ring" style="width: 35px; height: 35px; background: rgba(16, 185, 129, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="terminal" style="color: #10b981; width: 18px; height: 18px;"></i>
        </div>
        <span style="font-size:1.3rem; font-weight:800; color:#f8fafc; letter-spacing:-0.5px; text-shadow: 0 0 10px rgba(16,185,129,0.3);">GOD MODE</span>
    </a>

    <!-- User Actions -->
    <div style="display:flex; align-items:center; gap:0.75rem;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Tombol Git Pull Cepat -->
            <button type="button" onclick="triggerGitPull()" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; border: 1px solid rgba(56,189,248,0.3); padding: 7px 15px; border-radius: 100px; display: inline-flex; align-items: center; gap: 7px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(2,132,199,0.35);" onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 0 15px rgba(56,189,248,0.5)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(2,132,199,0.35)';" title="Tarik Update Kode Terbaru dari GitHub (Git Pull)">
                <i data-lucide="git-pull-request" style="width: 15px; height: 15px;"></i>
                <span>PULL GIT</span>
            </button>

            <div style="background:rgba(255,255,255,0.05); padding:6px 16px 6px 6px; border-radius:100px; display:flex; align-items:center; gap:10px; color:#cbd5e1; font-weight:600; font-size:0.85rem; border: 1px solid rgba(255,255,255,0.1);">
                <div style="width:30px; height:30px; background:linear-gradient(135deg, #10b981, #059669); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 10px rgba(16,185,129,0.5);">
                    <i data-lucide="user" style="width:16px; color: white;"></i>
                </div>
                <span>SUPER DEV</span>
            </div>
            
            <a href="<?php echo base_url('logout'); ?>" style="background:rgba(239, 68, 68, 0.1); color:#f87171; border: 1px solid rgba(239, 68, 68, 0.2); width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:0.3s; box-shadow: 0 0 10px rgba(239,68,68,0.1);" onmouseover="this.style.background='#ef4444'; this.style.color='#fff'; this.style.boxShadow='0 0 15px rgba(239,68,68,0.5)';" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#f87171'; this.style.boxShadow='0 0 10px rgba(239,68,68,0.1)';" title="Logout dari God Mode">
                <i data-lucide="power" style="width:16px;"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>

<script>
function triggerGitPull() {
    function runPull() {
        Swal.fire({
            title: 'Tarik Update (Git Pull)?',
            html: `
                <div style="text-align:left; font-size:0.9rem; line-height:1.6; color:#cbd5e1;">
                    Sistem akan mengeksekusi <b>git pull origin main</b> secara aman di server untuk mengambil file & fitur terbaru.<br><br>
                    <div style="background:rgba(14,165,233,0.1); border:1px solid rgba(14,165,233,0.3); border-radius:8px; padding:10px 12px; color:#38bdf8; font-size:0.82rem;">
                        <i data-lucide="shield-check" style="width:14px;height:14px;vertical-align:middle;display:inline-block;margin-right:4px;"></i>
                        Otomatis menjalankan <b>git stash</b> terlebih dahulu agar file lokal tidak tertimpa konflik.
                    </div>
                </div>
            `,
            icon: 'question',
            background: '#0f172a',
            color: '#f8fafc',
            showCancelButton: true,
            confirmButtonText: '🚀 Ya, Jalankan Git Pull!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#475569',
            reverseButtons: true,
            didOpen: () => { if (typeof lucide !== 'undefined') lucide.createIcons(); }
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Menjalankan Git Pull...',
                html: `
                    <div style="font-size:0.9rem; color:#94a3b8; margin-bottom:15px;">
                        Menghubungi repositori GitHub dan menyinkronkan file sistem...
                    </div>
                    <div style="display:flex; justify-content:center; margin:15px 0;">
                        <div style="width:40px; height:40px; border:3px solid rgba(14,165,233,0.2); border-top-color:#0ea5e9; border-radius:50%; animation:spin 1s linear infinite;"></div>
                    </div>
                    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
                `,
                background: '#0f172a',
                color: '#f8fafc',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });

            fetch('<?php echo base_url("dev-panel/git-pull"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                const isSuccess = data.success;
                const logContent = (data.output || '').trim();
                const stashContent = (data.stash || '').trim();
                const commitInfo = (data.commit || '').trim();

                const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

                let terminalHtml = `
                    <div style="text-align:left; background:#020617; border:1px solid #1e293b; border-radius:8px; padding:12px; font-family:Consolas, Monaco, monospace; font-size:0.75rem; color:#e2e8f0; max-height:220px; overflow-y:auto; line-height:1.5; white-space:pre-wrap; margin-top:12px;">
<span style="color:#38bdf8; font-weight:bold;">$ git stash</span>
${stashContent ? esc(stashContent) : '(tidak ada perubahan lokal)'}

<span style="color:#38bdf8; font-weight:bold;">$ git pull origin main</span>
<span style="color:${isSuccess ? '#4ade80' : '#f87171'}; font-weight:600;">${esc(logContent || '(tidak ada output)')}</span>
${commitInfo ? `\n<span style="color:#eab308; font-weight:bold;">$ git log -1</span>\n` + esc(commitInfo) : ''}
                    </div>
                `;

                Swal.fire({
                    title: isSuccess ? 'Git Pull Selesai!' : 'Catatan Git Pull',
                    html: `
                        <div style="text-align:left; font-size:0.88rem; color:#cbd5e1;">
                            ${esc(data.message || (isSuccess ? 'Pembaruan kode berhasil ditarik ke server.' : 'Cek catatan output terminal di bawah.'))}
                        </div>
                        ${terminalHtml}
                    `,
                    icon: isSuccess ? 'success' : 'info',
                    background: '#0f172a',
                    color: '#f8fafc',
                    confirmButtonText: isSuccess ? '🔄 Muat Ulang Halaman' : 'Tutup',
                    confirmButtonColor: isSuccess ? '#10b981' : '#475569'
                }).then(() => {
                    if (isSuccess) {
                        window.location.reload();
                    }
                });
            })
            .catch(err => {
                Swal.fire({
                    title: 'Koneksi Gagal!',
                    text: 'Gagal menghubungi endpoint /dev-panel/git-pull: ' + err.message,
                    icon: 'error',
                    background: '#0f172a',
                    color: '#f8fafc',
                    confirmButtonColor: '#ef4444'
                });
            });
        });
    }

    // Pastikan SweetAlert2 tersedia
    if (typeof Swal === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        script.onload = runPull;
        document.head.appendChild(script);
    } else {
        runPull();
    }
}
</script>
