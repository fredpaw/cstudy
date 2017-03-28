<h2 class="nav-tab-wrapper">
	  
		<?php foreach($this->tabs as $key => $tab): ?>
	    		<a href="<?php echo admin_url('admin.php?page='.$key) ?>" class="nav-tab <?php if($_GET['page'] == $key){ echo 'nav-tab-active'; } ?>"><?php echo __($tab['title'],'links-auto-replacer'); ?></a>
		<?php endforeach; ?>
</h2>
