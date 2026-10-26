<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="margin-bottom: 2rem; background: transparent; padding: 1.5rem; border: none; box-shadow: none;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #0ea5e9; text-shadow: 0 0 15px rgba(16,185,129,0.5);">
                        <i data-lucide="network"></i> App Delegation
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px; color: #94a3b8;">Delegasikan wewenang administrator modul kepada guru/tendik.</p>
                </div>
                <button onclick="document.getElementById('add-delegation-modal').style.display='flex'" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
                    <i data-lucide="plus-circle"></i> DELEGASI BARU
                </button>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(14, 165, 233, 0.3); box-shadow: 0 0 20px rgba(16,185,129,0.1);">
                <i data-lucide="check-circle" style="vertical-align: -5px; width: 20px; height: 20px;"></i> Pendelegasian admin berhasil disimpan!
            </div>
        <?php endif; ?>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Guru (User)</th>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Modul Aplikasi</th>
                        <th style="padding: 15px 20px; text-align: right; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($delegations as $d): 
                        $displayName = !empty($d['guru_nama']) ? $d['guru_nama'] . ' <span style="font-size:0.8rem; color:#64748b; font-weight:normal; margin-left:8px;">(@'.$d['username'].')</span>' : $d['username'];
                    ?>
                    <tr style="transition: all 0.3s ease; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px 20px; font-weight: bold; color: #f8fafc; font-size: 1.05rem;">
                            <i data-lucide="user" style="width:16px;height:16px;color:#94a3b8;margin-right:8px;vertical-align:-2px;"></i>
                            <?php echo $displayName; ?>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span style="background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                <i data-lucide="box" style="width:12px;height:12px;vertical-align:-2px;"></i> <?php echo isset($apps[$d['app_code']]) ? $apps[$d['app_code']] : $d['app_code']; ?>
                            </span>
                        </td>
                        <td style="padding: 15px 20px; text-align: right;">
                            <a href="<?php echo base_url('dev-panel/delegation/delete/' . $d['id']); ?>" onclick="return confirm('Hapus delegasi ini?')" class="btn" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); padding: 8px 15px; font-size: 0.85rem; border-radius: 8px; transition: 0.3s; text-decoration:none;">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Cabut
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($delegations)): ?>
                    <tr>
                        <td colspan="3" style="padding: 30px; text-align: center; color: #64748b; font-style: italic;">Belum ada delegasi admin aplikasi.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<!-- Modal Form -->
<div id="add-delegation-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 450px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #0ea5e9; font-size: 1.3rem;">Tambah Delegasi Admin</h3>
            <button onclick="document.getElementById('add-delegation-modal').style.display='none'" style="background:rgba(255,255,255,0.1); color:white; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.3s;"><i data-lucide="x" style="width:16px;height:16px;"></i></button>
        </div>
        <form action="<?php echo base_url('dev-panel/delegation/save'); ?>" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1; font-weight:bold;">Pilih Guru</label>
                <select name="user_id" required class="form-control" style="width:100%; padding:12px; border-radius:8px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #f8fafc; outline: none;">
                    <option value="" style="background:#0f172a; color:#ffffff;">-- Pilih Guru Terlebih Dahulu --</option>
                    <?php foreach($gurus as $g): 
                        $optionName = !empty($g['nama']) ? $g['nama'] . ' (' . $g['username'] . ')' : $g['username'];
                    ?>
                        <option value="<?php echo $g['id']; ?>" style="background:#0f172a; color:#ffffff;"><?php echo $optionName; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1; font-weight:bold;">Pilih Modul Aplikasi</label>
                <select name="app_code" required class="form-control" style="width:100%; padding:12px; border-radius:8px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #f8fafc; outline: none;">
                    <option value="" style="background:#0f172a; color:#ffffff;">-- Pilih Aplikasi --</option>
                    <?php foreach($apps as $code => $name): ?>
                        <option value="<?php echo $code; ?>" style="background:#0f172a; color:#ffffff;"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding: 12px; font-weight:bold; letter-spacing:1px;"><i data-lucide="check-circle"></i> SIMPAN DELEGASI</button>
        </form>
    </div>
</div>

<script>
lucide.createIcons();
</script>

