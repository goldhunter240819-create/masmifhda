<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 style="color: white; margin: 0; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="smartphone" style="color: #0ea5e9; width: 28px; height: 28px;"></i>
            APK Device Manager
        </h2>
        <p style="color: #94a3b8; margin: 5px 0 0 0; font-size: 0.95rem;">
            Pantau dan kelola seluruh perangkat siswa & guru yang terhubung ke aplikasi mobile.
        </p>
    </div>
    <div class="col-auto">
        <button id="btn-refresh" class="btn-primary" style="padding: 10px 20px; border-radius: 8px;">
            <i data-lucide="refresh-cw" id="icon-refresh"></i> Sync Data
        </button>
    </div>
</div>

<div class="card mb-4" style="padding: 15px 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; background: rgba(15, 23, 42, 0.4);">
    <div style="display: flex; align-items: center; gap: 10px;">
        <label style="color: #94a3b8; font-size: 0.85rem; font-weight: 500;">Filter Akun:</label>
        <select id="filter-role" class="form-control" style="width: 150px; border-radius: 8px;">
            <option value="Semua">Semua Pengguna</option>
            <option value="Siswa">Siswa Saja</option>
            <option value="Guru">Guru Saja</option>
        </select>
    </div>
    
    <div id="filter-kelas-container" style="display: flex; align-items: center; gap: 10px;">
        <label style="color: #94a3b8; font-size: 0.85rem; font-weight: 500;">Kelas:</label>
        <select id="filter-kelas" class="form-control" style="width: 150px; border-radius: 8px;">
            <option value="Semua">Semua Kelas</option>
            <?php if(isset($kelas_list)) { foreach($kelas_list as $k) { ?>
                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
            <?php }} ?>
        </select>
    </div>
</div>

<div class="card" style="padding: 25px; border-radius: 16px;">
    <div id="loader" style="text-align: center; padding: 40px; color: #64748b;">
        <i data-lucide="loader-2" class="dev-pulse-ring" style="width: 40px; height: 40px; color: #0ea5e9; animation: spin 1s linear infinite;"></i>
        <p style="margin-top: 15px;">Sinkronisasi data dengan OneSignal...</p>
    </div>

    <div id="error-container" style="display: none; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 20px; border-radius: 12px; text-align: center;">
        <i data-lucide="alert-circle" style="width: 32px; height: 32px; margin-bottom: 10px;"></i>
        <p id="error-message" style="margin: 0; font-weight: 500;"></p>
    </div>

    <div id="data-container" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;" id="device-grid">
            <!-- Data akan di-render di sini -->
        </div>
    </div>
</div>

<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    
    .device-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .device-card:hover {
        transform: translateY(-3px);
        border-color: rgba(14, 165, 233, 0.3);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .multiple-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.2);
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 15px;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        background: rgba(14, 165, 233, 0.1);
        color: #0ea5e9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    .device-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .device-item {
        background: rgba(255, 255, 255, 0.02);
        padding: 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    fetchData();

    document.getElementById('btn-refresh').addEventListener('click', function() {
        refreshData();
    });
    
    document.getElementById('filter-role').addEventListener('change', function() {
        const val = this.value;
        const kelasContainer = document.getElementById('filter-kelas-container');
        if (val === 'Guru') {
            kelasContainer.style.display = 'none';
        } else {
            kelasContainer.style.display = 'flex';
        }
        fetchData();
    });
    
    document.getElementById('filter-kelas').addEventListener('change', function() {
        fetchData();
    });
});

function refreshData() {
    const icon = document.getElementById('icon-refresh');
    icon.style.animation = 'spin 1s linear infinite';
    fetchData(() => {
        icon.style.animation = '';
    });
}

function formatTimestamp(ts) {
    if (!ts || ts === 0) return 'Belum pernah aktif';
    const d = new Date(ts * 1000);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + 
           d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function fetchData(callback = null) {
    document.getElementById('loader').style.display = 'block';
    document.getElementById('error-container').style.display = 'none';
    document.getElementById('data-container').style.display = 'none';
    
    const role = document.getElementById('filter-role').value;
    const kelas = document.getElementById('filter-kelas').value;
    
    fetch(`/dev-panel/apk-devices/api?role=${role}&kelas_id=${kelas}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loader').style.display = 'none';
            if (data.success) {
                renderGrid(data.data);
                document.getElementById('data-container').style.display = 'block';
            } else {
                showError(data.error || 'Terjadi kesalahan tidak diketahui.');
            }
            if (callback) callback();
        })
        .catch(err => {
            document.getElementById('loader').style.display = 'none';
            showError('Gagal menghubungi server.');
            console.error(err);
            if (callback) callback();
        });
}

function showError(msg) {
    document.getElementById('error-message').textContent = msg;
    document.getElementById('error-container').style.display = 'block';
}

function renderGrid(users) {
    const grid = document.getElementById('device-grid');
    grid.innerHTML = '';
    
    if (users.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">Belum ada perangkat yang terdeteksi.</div>';
        return;
    }
    
    users.forEach(u => {
        const initial = u.nama ? u.nama.charAt(0).toUpperCase() : '?';
        const numDevices = u.devices.length;
        const isMultiple = numDevices > 1;
        const isOffline = numDevices === 0;
        
        let devicesHTML = '';
        if (isOffline) {
            devicesHTML = `
                <div class="device-item" style="background: rgba(239, 68, 68, 0.05); border: 1px dashed rgba(239, 68, 68, 0.3);">
                    <i data-lucide="monitor-off" style="color: #ef4444; width: 20px; height: 20px;"></i>
                    <div style="flex: 1; min-width: 0;">
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 500;">
                            Belum Pernah Login
                        </div>
                        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 2px;">
                            Tidak ada perangkat APK yang ditautkan
                        </div>
                    </div>
                </div>
            `;
        } else {
            u.devices.forEach((d, idx) => {
                devicesHTML += `
                    <div class="device-item">
                        <i data-lucide="smartphone" style="color: #94a3b8; width: 20px; height: 20px;"></i>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: #e2e8f0; font-size: 0.85rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${d.device_model || 'Unknown Device'} ${d.os ? '('+d.os+')' : ''}
                            </div>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 2px;">
                                Aktif: ${formatTimestamp(d.last_active)}
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        
        const badgeHTML = isMultiple 
            ? `<div class="multiple-badge"><i data-lucide="alert-triangle" style="width:12px;height:12px;"></i> ${numDevices} Perangkat</div>` 
            : '';
            
        const roleColor = u.role === 'Guru' ? '#3b82f6' : '#0ea5e9';
        const cardStyle = isOffline ? 'opacity: 0.7; filter: grayscale(1);' : '';
        
        const cardHTML = `
            <div class="device-card" style="${cardStyle}">
                ${badgeHTML}
                <div class="user-info">
                    <div class="user-avatar" style="background: ${roleColor}20; color: ${roleColor};">
                        ${initial}
                    </div>
                    <div>
                        <h4 style="margin: 0; color: white; font-size: 1rem; font-weight: 600;">${u.nama}</h4>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 3px;">
                            <span style="color: ${roleColor}; font-weight: 500;">${u.role}</span> &bull; ID: ${u.user_id}
                        </div>
                    </div>
                </div>
                <div class="device-list">
                    ${devicesHTML}
                </div>
            </div>
        `;
        
        grid.insertAdjacentHTML('beforeend', cardHTML);
    });
    
    lucide.createIcons();
}
</script>

