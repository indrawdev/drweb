<html>
<head>
	<title>dokterpraktek.net - Aplikasi Dokter Praktek</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/ico" href="<?php echo $this->config->item('base_img');?>dr.png">
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_tema');?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_css');?>form.css?cb=<?php echo time()?>">
	<div class="center" id="loading">
		<img src="<?php echo $this->config->item('base_img');?>loading.gif" alt="Memuat"/>
	</div>
	<script type="text/javascript" src="<?php echo $this->config->item('base_ext');?>ext-all.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('base_lokal');?>ext-locale-id.js?cb=<?php echo time()?>"></script>
	<script type="text/javascript">
		var gBaseUX = '<?php echo $this->config->item('base_ux');?>';
		var gBaseIMG = '<?php echo $this->config->item('base_img');?>';
		var gBasePanel = '<?php echo $this->config->item('base_panel');?>';
	</script>
	<script type="text/javascript" src="<?php echo $this->config->item('base_js');?>setupiddokter.js?cb=<?php echo time()?>"></script>
</head>
</html>