<?php
/* _zamrud_head.php — include di <head> setiap admin page */
$brand = \App\Core\Branding::get();
$brand_name = (!empty($brand['name']) && stripos($brand['name'], 'Aula') === false) ? $brand['name'] : 'MA Miftahul Huda';
$pt = isset($page_title) ? $page_title . ' - ' . $brand_name : $brand_name;
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pt; ?></title>
<link rel="icon" type="image/png" href="<?php echo $brand['favicon']; ?>">
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
<script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
