<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
	<?php include LAR_DIR.'admin/partials/menu.php'; ?>
	<?php	
		$metabox_id = $this->tabs[$_GET['page']]['id'];
		cmb2_metabox_form($metabox_id, $this->key );
	?>
</div> 