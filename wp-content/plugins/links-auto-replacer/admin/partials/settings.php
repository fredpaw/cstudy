<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
	<?php include plugin_dir_path(__FILE__).'menu.php'; ?>
	<?php	

		$metabox_id =(($_GET['page']!='lar_settings'))?$this->tabs[$_GET['page']]['id']:'lar_settings';
		cmb2_metabox_form($metabox_id, $this->key );
	?>
</div> 