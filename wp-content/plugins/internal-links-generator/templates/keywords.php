<div class="container keywords">
    <h4><?php _e('Keywords list', 'ilgen')?></h4>
    <?php if(!empty($template_data['keywords'])):?>
        <form name="" action="" method="post">
            <?php wp_nonce_field( 'internal_link_generator-bulk' );?>
            <input type="hidden" name="action" value="bulk">
            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="bulk_action">
                        <option><?php _e('Bulk Actions', 'ilgen')?></option>
                        <option value="update"><?php _e('Update', 'ilgen')?></option>
                        <option value="recount"><?php _e('ReCount', 'ilgen')?></option>
                        <option value="linking"><?php _e('Link all', 'ilgen')?></option>
                        <option value="unlinking"><?php _e('Unlink all', 'ilgen')?></option>
                        <option value="delete"><?php _e('Delete', 'ilgen')?></option>
                    </select>
                    <input type="submit" class="button button-primary" name="ilgen_bulk" value="<?php _e('Apply', 'ilgen')?>">
                    <span class="ilgen-watch-notification"><?php _e('Click "Apply" to save changes!')?></span>
                </div>
                <div class="alignright actions">
                    <input type="search" id="ilgenSearchInput" value="<?= $_GET['filter']?>">
                    <input type="button" class="button" value="<?php _e('Filter')?>" onclick="insertParam('filter', document.getElementById('ilgenSearchInput').value); return false;">
                </div>
            </div>
            <div class="keywords-inner">
                <table>
                    <thead><tr>
                        <th><input type="checkbox" class="check_all"></th>
                        <th><?php _e('Keyword', 'ilgen')?></th>
                        <th><?php _e('Target URL', 'ilgen')?></th>
                        <th><?php _e('Links Limit', 'ilgen')?></th>
                        <th><?php _e('Found on Site', 'ilgen')?></th>
                        <th><?php _e('Linked', 'ilgen')?></th>
                        <th><?php _e('Outer Tag', 'ilgen')?></th>
                        <th><?php _e('Delete', 'ilgen')?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach($template_data['keywords'] as $key):
                            if($_GET['filter'] && !stristr($key->keyword, $_GET['filter'])) continue;?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= $key->id?>"></td>
                                <td><?= html_entity_decode($key->keyword)?></td>
                                <td><input type="text" name="targets[<?= $key->id?>]" value="<?= $key->target?>" size="7" class="ilgen-watch-input"></td>
                                <td><input type="text" name="limits[<?= $key->id?>]" value="<?= $key->limit?>" size="3" class="ilgen-watch-input"></td>
                                <td><?= $key->count?></td>
                                <td><?= $key->linked?></td>
                                <td><select name="tags[<?= $key->id?>]" class="ilgen-watch-input">
                                    <option></option>
                                    <?php foreach(array('strong', 'b', 'i', 'u') as $tag){
                                        $sel = ($key->tag == $tag) ? 'selected' : '';
                                        printf('<option %s>%s</option>', $sel, $tag);     
                                    }?>
                                </select></td>
                                <td><button class="ilgen-keywords-del button button-small ilgen-button-delete" data-id="<?= $key->id?>"><?php _e('Del', 'ilgen')?></button></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </form>
        <div class="box">
            <h4  class="toggle closed" data="box_0"><?php _e('Add Keywords', 'ilgen')?><span class="plus"></span></h4>
            <div class="box-inner" id="box_0">
                <form method="post" action="">
                    <?php wp_nonce_field( 'internal_link_generator-simple_import' );?>
                    <input type="hidden" name="action" value="simple_import">
                    <div class="ilgen-container">
                        <h4><?php _e('Simple keywords import', 'ilgen')?></h4>
                        <p class="ilgen-notification">
                            <?php _e('Put each keyword on a separate line or separate them by commas.', 'ilgen')?>
                        </p>
                        <textarea rows="5" name="import_string"></textarea>
                        <p>
                            <input type="submit" name="ilgen_simple_import" value="<?php _e('Import', 'ilgen')?>" class="button button-primary">
                        </p>
                    </div>
                </form>
            </div>
        </div>
    <?php else:?>
        <p class="ilgen-notification"><?php printf('In order to add keywords, use %s tab.', '<a href="options-general.php?page=internal_links_generator&tab=impex">' . __('Import/Export', 'ilgen') . '</a>');?></p>
    <?php endif;?>
    <script>
        jQuery(document).ready(function($){
            $('.ilgen-keywords-del').click(function(e){
                e.preventDefault();
                obj = $(this);
                jQuery.ajax({
                    url : '<?php menu_page_url('internal_links_generator');?>',
                    type : 'post',
                    data : {
                        action   : 'ajax',
                        _wpnonce : '<?php echo wp_create_nonce('internal_link_generator-ajax');?>',
                        type     : 'keywords_del',
                        id       : obj.attr('data-id')
                    },
                    success : function( response ) {
                        obj.attr('disabled', true);
                        obj.closest('tr').css('display','none');
                    }
                });
            });
        });
    </script>
</div>