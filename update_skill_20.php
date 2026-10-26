<?php
$file = 'c:\\xampp\\htdocs\\masmifhda\\.agents\\skills\\zamrud-ui\\SKILL.md';
$content = file_get_contents($file);

// Replace the previous Rule 20 text with the updated one including inputs
$search = "## 20. DataTables Styling (Anti Mepet)";
$pos = strpos($content, $search);
if ($pos !== false) {
    $content = substr($content, 0, $pos);
}

$new_rule = "## 20. DataTables Styling (Anti Mepet & Form Styling)\n**CRITICAL**: When using DataTables in Zamrud UI, the filter (Cari) and length (Tampilkan entri) controls will look cramped ('mepet') against the table, and the default inputs will look square and outdated if not styled properly. ALWAYS include the following CSS block in your view to space them out evenly and modernize the inputs:\n```css\n    /* DataTables Custom Spacing & Form Styling */\n    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 1.5rem 1.5rem 0 1.5rem; }\n    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 1.5rem; }\n    .dataTables_wrapper .dataTables_paginate .paginate_button { \n        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; \n        background: #fff !important; color: #475569 !important; font-weight: 700 !important; \n    }\n    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #0284c7 !important; color: #fff !important; border-color: #0284c7 !important; }\n    \n    .dataTables_wrapper select, .dataTables_wrapper input[type=\"search\"] {\n        border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 6px 12px;\n        font-family: inherit; font-size: 0.85rem; color: #334155; outline: none; transition: 0.2s;\n    }\n    .dataTables_wrapper select:focus, .dataTables_wrapper input[type=\"search\"]:focus {\n        border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);\n    }\n```\n";

$content .= $new_rule;
file_put_contents($file, $content);
echo "Updated Rule 20 with Input Styling";
