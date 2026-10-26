<?php
$file = 'c:\\xampp\\htdocs\\masmifhda\\.agents\\skills\\zamrud-ui\\SKILL.md';
$content = file_get_contents($file);

// Replace Rule 16
$search = "## 16. Modal Styling (Z-Modal)";
$pos = strpos($content, $search);
if ($pos !== false) {
    // Find next rule to extract just rule 16
    $next_pos = strpos($content, "## 17", $pos);
    if($next_pos === false) $next_pos = strpos($content, "## 20", $pos);
    
    if($next_pos !== false) {
        $part1 = substr($content, 0, $pos);
        $part2 = substr($content, $next_pos);
        $content = $part1 . "## 16. Modal Styling (Z-Modal)\n**CRITICAL**: When creating modals/popups in Zamrud UI, use the following exact structure (`z-modal` for the background overlay, and `z-modal-content` for the white box):\n```html\n<div id=\"modalId\" class=\"z-modal\" style=\"display:none;\">\n    <div class=\"z-modal-content\">\n        <div class=\"z-modal-header\">\n            <h3 id=\"modalTitle\"><i data-lucide=\"ICON\"></i> Title Here</h3>\n            <button onclick=\"document.getElementById('modalId').style.display='none'\" class=\"close-btn\"><i data-lucide=\"x\"></i></button>\n        </div>\n        <form action=\"...\" method=\"POST\">\n            <div class=\"z-modal-body\">\n                <!-- form inputs -->\n            </div>\n            <div class=\"z-modal-footer\">\n                <button type=\"button\" class=\"z-btn z-btn-ghost\" onclick=\"document.getElementById('modalId').style.display='none'\">Batal</button>\n                <button type=\"submit\" class=\"z-btn z-btn-primary\">Simpan</button>\n            </div>\n        </form>\n    </div>\n</div>\n```\nTo open the modal, use `document.getElementById('modalId').style.display='flex'`. If your modal has a Select2, initialize it AFTER opening the modal and specify `dropdownParent: $('#modalId')`.\n\n" . $part2;
    }
}

file_put_contents($file, $content);
echo "Updated Rule 16";
