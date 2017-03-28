<div class="container settings">
    <h4><?php _e('Plugin settings', 'ilgen')?></h4>
    <form action="" method="post">
        <?php wp_nonce_field( 'internal_link_generator-settings' );?>
        <input type="hidden" name="action" value="settings">
        <div class="settings-inner">
            <div class="ilgen-notification">
                <h5><?php _e('Post Types', 'ilgen');?></h5>
                <small><?php _e('Allow this page/post types to put internal links in.', 'ilgen');?></small>
            </div>
            <ul>
                <?php foreach(get_post_types(array('public' => true ), 'objects') as $pt):
                    $checked = ( in_array($pt->name, $template_data['options']['allowed_pt'])) ? 'checked' : ''?>
                    <li><input type="checkbox" name="allowed_pt[]" value="<?= $pt->name?>" <?= $checked?>>&nbsp;<?= $pt->labels->name?></li>
                <?php endforeach;?>
            </ul>
            <div class="ilgen-notification">
                <h5><?php _e('Number of Links', 'ilgen');?></h5>
                <small><?php _e('Maximum number of internal links from one page.', 'ilgen');?></small>
            </div>
            <input type="text" name="numlinks" value="<?= $template_data['options']['numlinks']?>">
            <p><input type="submit" name="ilgen_settings" class="button button-primary" value="<?php _e('Update Settings', 'ilgen')?>"></p>
        </div>
    </form>
</div>