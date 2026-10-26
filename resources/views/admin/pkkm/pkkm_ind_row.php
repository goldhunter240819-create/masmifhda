<?php
$indCode = (!empty($sub['urutan']) ? $sub['urutan'] : ($komp['urutan'] ?? '1')) . '.' . $ind['urutan'];

// Pisahkan Penggalian Data dan butir dokumen bukti fisik
$bf_lines = explode("\n", $ind['bukti_fisik'] ?? '');
$penggalian_data = null;
$doc_lines = [];

foreach($bf_lines as $l) {
    $lt = trim($l);
    if(empty($lt)) continue;

    if(stripos($lt, 'Penggalian Data:') === 0) {
        $penggalian_data = trim(substr($lt, strlen('Penggalian Data:')));
        if (empty($penggalian_data)) $penggalian_data = $lt;
    } else {
        $doc_lines[] = $lt;
    }
}
?>
<tr class="indicator-header-row">
    <td colspan="4">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="badge" style="background: #0284c7; color: #ffffff; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 7px; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(2,132,199,0.2);">
                        <?php echo htmlspecialchars($indCode); ?>
                    </span>
                    <span style="color: #0f172a; font-size: 0.92rem; font-weight: 700; line-height: 1.45;">
                        <?php echo htmlspecialchars($ind['indikator']); ?>
                    </span>
                </div>
                <?php if(!empty($penggalian_data)): ?>
                    <div>
                        <span class="indicator-meta-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <span><strong>Metode:</strong> <?php echo htmlspecialchars($penggalian_data); ?></span>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="indicator-header-actions">
                <button type="button" class="btn-banner-upload" onclick="openModalBerkas(<?php echo $ind['id']; ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                    <span>Upload Berkas</span>
                </button>
                <button type="button" class="btn-banner-edit" onclick="editIndikator(<?php echo $ind['id']; ?>, <?php echo $ind['komponen_id']; ?>, <?php echo $ind['sub_komponen_id'] ?: 'null'; ?>, '<?php echo htmlspecialchars(addslashes($ind['indikator'])); ?>', '<?php echo htmlspecialchars(addslashes($ind['kriteria'])); ?>', '<?php echo htmlspecialchars(addslashes(str_replace(["\r\n", "\r", "\n"], "\\n", $ind['bukti_fisik']))); ?>', <?php echo $ind['urutan']; ?>)" title="Edit Indikator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    <span>Edit</span>
                </button>
                <form action="/admin/pkkm/indikator/delete" method="POST" style="margin:0;" onsubmit="return confirm('Hapus indikator ini?')">
                    <input type="hidden" name="id" value="<?php echo $ind['id']; ?>">
                    <button type="submit" class="btn-banner-del" title="Hapus Indikator">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </td>
</tr>
<tr class="indicator-data-row">
    <!-- Kolom 1: Kriteria Penilaian -->
    <td style="vertical-align: top; padding: 14px 16px; background: #ffffff;">
        <div style="font-size: 0.85rem; color: #1e293b; line-height: 1.55; font-weight: 500;">
            <?php echo nl2br(htmlspecialchars($ind['kriteria'])); ?>
        </div>
    </td>

    <!-- Kolom 2: Bukti Fisik Dokumen (Murni Daftar Kartu) -->
    <td style="vertical-align: top; padding: 14px 16px; background: #ffffff;">
        <div class="evidence-card-list">
            <?php 
            foreach($doc_lines as $line):
                if(preg_match('/^-\s*([a-zA-Z])[\.\)]\s*(.*)$/', $line, $m)):
            ?>
                <div class="evidence-card" onclick="showPanduanBukti('<?php echo htmlspecialchars(addslashes($m[2])); ?>', <?php echo $ind['id']; ?>)" role="button" title="Klik untuk lihat petunjuk & contoh konkret bukti fisik">
                    <div class="evidence-card-main">
                        <span class="evidence-card-letter"><?php echo strtolower($m[1]); ?></span>
                        <span class="evidence-card-text"><?php echo htmlspecialchars($m[2]); ?></span>
                    </div>
                    <div class="evidence-card-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                        <span>Contoh</span>
                    </div>
                </div>
            <?php elseif(preg_match('/^-\s*(.*)$/', $line, $m)): ?>
                <div class="evidence-card" onclick="showPanduanBukti('<?php echo htmlspecialchars(addslashes($m[1])); ?>', <?php echo $ind['id']; ?>)" role="button" title="Klik untuk lihat petunjuk & contoh konkret bukti fisik">
                    <div class="evidence-card-main">
                        <span class="evidence-card-bullet">•</span>
                        <span class="evidence-card-text"><?php echo htmlspecialchars($m[1]); ?></span>
                    </div>
                    <div class="evidence-card-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                        <span>Contoh</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="evidence-card-plain">
                    <?php echo htmlspecialchars($line); ?>
                </div>
            <?php endif; endforeach; ?>
        </div>
    </td>

    <!-- Kolom 3: File Terlampir (Dropzone Elegan bila kosong / List rapi) -->
    <td style="vertical-align: top; padding: 14px 16px; background: #ffffff;">
        <?php if(empty($ind['berkas'])): ?>
            <div class="empty-file-dropzone" onclick="openModalBerkas(<?php echo $ind['id']; ?>)" role="button" title="Klik untuk mengunggah file bukti">
                <div class="empty-file-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                </div>
                <div class="empty-file-title">Belum ada file</div>
                <div class="empty-file-hint">+ Unggah Berkas</div>
            </div>
        <?php else: ?>
            <div class="file-list-group">
                <?php foreach($ind['berkas'] as $b): ?>
                    <div class="berkas-item-modern">
                        <a href="<?php echo htmlspecialchars($b['url']); ?>" target="_blank" class="berkas-item-link" title="<?php echo htmlspecialchars($b['nama_file']); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                            <span><?php echo htmlspecialchars($b['nama_file']); ?></span>
                        </a>
                        <?php if(!isset($b['is_master']) || !$b['is_master']): ?>
                            <form action="/admin/pkkm/berkas/delete" method="POST" style="margin:0;" onsubmit="return confirm('Hapus berkas ini?')">
                                <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                <button type="submit" class="berkas-del-btn" title="Hapus Berkas"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </form>
                        <?php else: ?>
                            <span class="badge-auto">Auto</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="btn-add-more-file" onclick="openModalBerkas(<?php echo $ind['id']; ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    <span>+ Tambah Berkas</span>
                </button>
            </div>
        <?php endif; ?>
    </td>

    <!-- Kolom 4: Skor Penilaian -->
    <td class="text-center align-middle" style="vertical-align: middle; padding: 14px 16px; background: #ffffff;">
        <select class="select-skor" onchange="saveSkor(<?php echo $ind['id']; ?>, this.value)" style="padding: 6px 12px; font-size: 0.9rem;">
            <option value="0" <?php echo $ind['skor'] == 0 ? 'selected':''; ?>>-</option>
            <option value="1" <?php echo $ind['skor'] == 1 ? 'selected':''; ?>>1</option>
            <option value="2" <?php echo $ind['skor'] == 2 ? 'selected':''; ?>>2</option>
            <option value="3" <?php echo $ind['skor'] == 3 ? 'selected':''; ?>>3</option>
            <option value="4" <?php echo $ind['skor'] == 4 ? 'selected':''; ?>>4</option>
        </select>
    </td>
</tr>
