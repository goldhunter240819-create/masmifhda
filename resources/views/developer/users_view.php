<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="margin-bottom: 2rem; background: transparent; padding: 1.5rem; border: none; box-shadow: none;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #0ea5e9; text-shadow: 0 0 15px rgba(16,185,129,0.5);">
                        <i data-lucide="users"></i> Master Admin Control
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px; color: #94a3b8;">Kelola akun administrator untuk semua modul tingkat atas.</p>
                </div>
                <button onclick="document.getElementById('add-user-modal').style.display='flex'" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
                    <i data-lucide="user-plus"></i> ADMIN BARU
                </button>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(14, 165, 233, 0.3); box-shadow: 0 0 20px rgba(16,185,129,0.1);">
                <i data-lucide="check-circle" style="vertical-align: -5px; width: 20px; height: 20px;"></i> Data admin berhasil disimpan!
            </div>
        <?php endif; ?>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Username</th>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Role Akses</th>
                        <th style="padding: 15px 20px; text-align: right; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr style="transition: all 0.3s ease;">
                        <td style="padding: 15px 20px; font-weight: bold; color: #f8fafc; font-size: 1.1rem;"><?php echo $u['username']; ?></td>
                        <td style="padding: 15px 20px;">
                            <?php if($u['role_id'] == 99): ?>
                                <span style="background: rgba(236,72,153,0.1); color: #f472b6; border: 1px solid rgba(236,72,153,0.3); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-shadow: 0 0 10px rgba(244,114,182,0.5); box-shadow: 0 0 10px rgba(236,72,153,0.2);">
                                    <i data-lucide="zap" style="width:12px;height:12px;vertical-align:-2px;"></i> GOD MODE
                                </span>
                            <?php elseif($u['role_id'] == 1): ?>
                                <span style="background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">
                                    <i data-lucide="shield" style="width:12px;height:12px;vertical-align:-2px;"></i> Admin Induk
                                </span>
                            <?php else: ?>
                                <span style="background: rgba(255,255,255,0.05); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase;"><?php echo $u['role_name']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px 20px; text-align: right;">
                            <button onclick='editUser(<?php echo json_encode($u); ?>)' class="btn" style="background: rgba(255,255,255,0.05); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 8px 15px; font-size: 0.85rem; border-radius: 8px; transition: 0.3s;">
                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<!-- Modal Form -->
<div id="add-user-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 450px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #0ea5e9; font-size: 1.3rem;">Form Administrator</h3>
            <button onclick="document.getElementById('add-user-modal').style.display='none'" style="background:rgba(255,255,255,0.1); color:white; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.3s;"><i data-lucide="x" style="width:16px;height:16px;"></i></button>
        </div>
        <form action="<?php echo base_url('dev-panel/users/save'); ?>" method="POST">
            <input type="hidden" name="id" id="form-id">
            
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1;">Username</label>
                <input type="text" name="username" id="form-username" required class="form-control" style="width:100%; padding:10px 12px; border-radius:8px;">
            </div>


            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1;">Password <span style="opacity:0.5; font-size:0.75rem;">(Kosongkan jika tak diubah)</span></label>
                <input type="password" name="password" id="form-password" class="form-control" style="width:100%; padding:10px 12px; border-radius:8px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1;">Role Akses</label>
                <select name="role_id" id="form-role" class="form-control" style="width:100%; padding:10px 12px; border-radius:8px;">
                    <?php foreach($roles as $r): ?>
                        <option value="<?php echo $r['id']; ?>" style="background:#0f172a;"><?php echo $r['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding: 12px; font-weight:bold; letter-spacing:1px;"><i data-lucide="save"></i> SIMPAN DATA ADMIN</button>
        </form>
    </div>
</div>

<script>
lucide.createIcons();
function editUser(u) {
    document.getElementById('form-id').value = u.id;
    document.getElementById('form-username').value = u.username;
    document.getElementById('form-role').value = u.role_id;
    document.getElementById('add-user-modal').style.display = 'flex';
}
</script>

