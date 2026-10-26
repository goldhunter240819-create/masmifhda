<?php
$content = file_get_contents('c:/xampp/htdocs/masmifhda/apk/absen_guru.php');

// Remove detail per mapel block
$permapel_start = strpos($content, '// Detail absensi per mapel');
$permapel_end = strpos($content, '// Rekap Bulanan');
$content = substr_replace($content, '', $permapel_start, $permapel_end - $permapel_start);

// Replace rendering of detail mapel with waktu_masuk and waktu_keluar
$render_start = strpos($content, '<div id="detail-<?= $index ?>" class="detail-mapel"');
$render_end = strpos($content, '</div>', strpos($content, '<?php endif; ?>', $render_start)) + 6;

$new_render = <<<'HTML'
                    <div id="detail-<?= $index ?>" class="detail-mapel" style="display: none; width: 100%; padding-top: 10px; margin-top: 10px; border-top: 1px dashed var(--border-color); font-size: 0.85rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span><i class="fa-solid fa-arrow-right-to-bracket" style="color: var(--primary); margin-right: 5px;"></i> Jam Masuk</span>
                            <span style="font-weight: 600;"><?= htmlspecialchars($a['waktu_masuk'] ?: '-') ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span><i class="fa-solid fa-arrow-right-from-bracket" style="color: var(--danger); margin-right: 5px;"></i> Jam Keluar</span>
                            <span style="font-weight: 600;"><?= htmlspecialchars($a['waktu_keluar'] ?: '-') ?></span>
                        </div>
                        <?php if (!empty($a['keterangan'])): ?>
                        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dotted #e2e8f0; color: var(--text-muted);">
                            <span style="font-weight: 600;">Keterangan:</span> <?= htmlspecialchars($a['keterangan']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
HTML;

$content = substr_replace($content, $new_render, $render_start, $render_end - $render_start);

file_put_contents('c:/xampp/htdocs/masmifhda/apk/absen_guru.php', $content);
