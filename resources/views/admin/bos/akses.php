<?php
$page_title = 'Hak Akses Bendahara BOS';
include __DIR__ . '/_layout_top.php';
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(56,189,248,0.2); color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                OTORISASI PETUGAS
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="shield-check" style="color:#38bdf8;"></i> Pendelegasian Hak Akses Bendahara BOS</h1>
                <p style="color: rgba(255,255,255,0.75);">Tentukan guru atau staf yang diberi wewenang khusus mengelola dana BOS (Akses Web & Mobile APK)</p>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: minmax(300px, 380px) 1fr; gap: 24px; align-items: start;" id="aksesGrid">
    <!-- Form Tambah Akses -->
    <div class="z-panel" style="border-radius: 24px; margin-bottom: 0;">
        <div class="z-panel-head">
            <div class="z-panel-title">
                <i data-lucide="user-plus"></i> Tambah Bendahara
            </div>
        </div>
        <div class="z-panel-body">
            <p style="color: var(--z-muted); font-size: 0.85rem; margin-bottom: 20px; line-height: 1.5;">Pilih guru / staf dari daftar di bawah ini untuk memberikan akses ke Modul RKAS, BKU, dan Pencetakan Laporan.</p>
            
            <form method="POST" action="<?= base_url('admin/bos/akses/save') ?>">
                <input type="hidden" name="action" value="add_akses">
                <div class="z-form-group">
                    <label class="z-label">Pilih Guru / Staf <span style="color:#ef4444;">*</span></label>
                    <select class="z-field" name="user_id" required>
                        <option value="">-- Cari / Pilih Guru --</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?> (<?= htmlspecialchars($u['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                    <i data-lucide="plus"></i> Berikan Hak Akses
                </button>
            </form>
        </div>
    </div>
    
    <!-- Daftar Admin -->
    <div class="z-panel" style="border-radius: 24px; margin-bottom: 0;">
        <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="z-panel-title">
                <i data-lucide="users"></i> Daftar Bendahara Aktif
            </div>
            <span class="pill pill-green"><?= count($admins) ?> Bendahara</span>
        </div>
        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th style="text-align: right;" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($admins)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            <div style="color: var(--z-muted); margin-bottom: 8px;"><i data-lucide="shield-off" style="width:36px;height:36px;opacity:0.3;"></i></div>
                            <div style="font-size: 0.85rem; color: var(--z-muted);">Belum ada bendahara tambahan yang ditunjuk.</div>
                        </td>
                    </tr>
                    <?php else: $no=1; foreach($admins as $a): ?>
                    <tr>
                        <td style="color: var(--z-muted); font-size: 0.85rem;"><?= $no++ ?></td>
                        <td style="font-weight: 800; color: var(--z-text);"><?= htmlspecialchars($a['nama']) ?></td>
                        <td style="color: var(--z-muted);">@<?= htmlspecialchars($a['username']) ?></td>
                        <td style="text-align: right;">
                            <form method="POST" action="<?= base_url('admin/bos/akses/save') ?>" onsubmit="return confirm('Apakah Anda yakin ingin mencabut hak akses bendahara BOS untuk <?= htmlspecialchars($a['nama'], ENT_QUOTES) ?>?');" style="display: inline;">
                                <input type="hidden" name="action" value="del_akses">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $a['user_id'] ?? 0 ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="Cabut Akses">
                                    <i data-lucide="trash-2"></i> Cabut
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    #aksesGrid { grid-template-columns: 1fr !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
