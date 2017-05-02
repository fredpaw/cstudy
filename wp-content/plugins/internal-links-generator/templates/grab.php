<div class="container grabb">
    <h4><?php _e('Grab & Import existing links', 'ilgen')?></h4>
    <p><i><?php _e('Each time you open this tab, plugin will scan your website for internal links you created manually across your website.', 'ilgen')?></i></p>
    <?php if($rows = $this->ilgen_grabb_links()):?>
        <form action="" method="post">
            <?php wp_nonce_field( 'internal_link_generator-grabb' );?>
            <input type="hidden" name="action" value="grabb">
            <div class="grabb-inner">
                <table>
                    <thead><tr>
                        <th><input type="checkbox" class="check_all"></th>
                        <th><?php _e('Anchor Text', 'ilgen')?></th>
                        <th><?php _e('Target URL', 'ilgen')?></th>
                        <th><?php _e('Post', 'ilgen')?></th>
                    <tr></thead>
                    <tbody>
                        <?php foreach($rows as $k => $row):?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= $k?>"></td>
                                <td><?= $row[3]?></td>
                                <td><?= $row[2]?></td>
                                <td><a href="<?= get_the_permalink($row[0])?>" target="_blank"><?= get_the_title($row[0])?></a></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <p><input type="submit" name="ilgen_grabb" value="<?php _e('Import', 'ilgen')?>" class="button button-primary"></p>
        </form>
    <?php else:?>
        <p class="ilgen-notification"><?php _e('Links not found!', 'ilgen');?></p>
    <?php endif;?>
</div>