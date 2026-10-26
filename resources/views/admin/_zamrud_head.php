<?php
/* _zamrud_head.php â€” include di <head> setiap admin page */
$brand = \App\Core\Branding::get();
$pt = isset($page_title) ? $page_title . ' - ' . $brand['name'] : $brand['name'];
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pt; ?></title>
<link rel="icon" type="image/png" href="<?php echo $brand['favicon']; ?>">
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
<script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
