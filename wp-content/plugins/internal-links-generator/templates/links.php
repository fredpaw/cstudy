<div class="container links">
    <h4><?php _e('Target URLs', 'ilgen')?></h4>
    <?php $rows = $this->ilgen_get_ordered_targets();?>
    <?php if($rows['int']):?>
        <div class="box">
            <h4 class="toggle closed" data="box_internal_links"><?php _e('Internal Links')?><span></span></h4>
            <div class="box-inner" id="box_internal_links">
                <?php foreach($rows['int'] as $k => $row):?>
                    <div class="box">
                        <h4 class="toggle closed" data="box_<?= $k?>"><?= $row['target']?><span></span></h4>
                        <?php if(!empty($row['keywords'])):?>
                            <div class="box-inner" id="box_<?= $k?>">
                                <form action="" method="post">
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
                                    </div>
                                    <table>
                                        <thead><tr>
                                            <th><input type="checkbox" class="check_all"></th>
                                            <th><?php _e('Keyword', 'ilgen')?></th>
                                            <th><?php _e('Links Limit', 'ilgen')?></th>
                                            <th><?php _e('Found on Site', 'ilgen')?></th>
                                            <th><?php _e('Linked', 'ilgen')?></th>
                                            <th><?php _e('Delete', 'ilgen')?></th>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach($row['keywords'] as $key):?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="ids[]" value="<?= $key->id?>">
                                                        <input type="hidden" name="targets[<?= $key->id?>]" value="<?= $key->target?>">
                                                    </td>
                                                    <td><?= html_entity_decode($key->keyword)?></td>
                                                    <td><input type="text" name="limits[<?= $key->id?>]" value="<?= $key->limit?>" size="3" class="ilgen-watch-input"></td>
                                                    <td><?= $key->count?></td>
                                                    <td><?= $key->linked?></td>
                                                    <td><button class="ilgen-keywords-del button button-small ilgen-button-delete" data-id="<?= $key->id?>"><?php _e('Del', 'ilgen')?></button></td>
                                                </tr>
                                            <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        <?php endif;?>
                    </div>
                <?php endforeach;?>
            </div>
        </div> 
    <?php endif;?>
    
    <?php if($rows['ext']):?>
        <div class="box">
            <h4 class="toggle closed" data="box_external_links"><?php _e('External Links')?><span></span></h4>
            <div class="box-inner" id="box_external_links">
                <?php foreach($rows['ext'] as $k => $row):?>
                    <div class="box">
                        <h4 class="toggle closed" data="box_<?= $k?>"><?= $row['target']?><span></span></h4>
                        <?php if(!empty($row['keywords'])):?>
                            <div class="box-inner" id="box_<?= $k?>">
                                <table><tbody>
                                    <?php foreach($row['keywords'] as $key):
                                        if(!$key->keyword) continue;?> 
                                        <tr>
                                            <td><ul>
                                                <?php if($posts = unserialize($key->posts)):
                                                    foreach($posts as $post): 
                                                        if(!$post) continue;
                                                        $permalink = get_the_permalink($post);
                                                        $editlink = get_edit_post_link($post);?>
                                                        <li>
                                                            <a href="<?= $permalink?>"><?= $permalink?></a>
                                                            <a href="<?= $editlink?>"><span class="ilgen-edit-post"></span></a>
                                                        </li> 
                                                    <?php endforeach;
                                                endif;?>
                                            </ul></td>
                                            <td><?= html_entity_decode($key->keyword)?></td>
                                            <td><button class="ilgen-keywords-del button button-small ilgen-button-delete" data-id="<?= $key->id?>"><?php _e('Del', 'ilgen')?></button></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody></table>
                            </div>
                        <?php endif;?>  
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif;?>
    
    <div class="box">
        <h4  class="toggle closed" data="box_add-url"><?php _e('Add URLs', 'ilgen')?><span class="plus"></span></h4>
        <div class="box-inner" id="box_add-url">
            <form method="post" action="">
                <?php wp_nonce_field( 'internal_link_generator-simple_import' );?>
                <input type="hidden" name="action" value="simple_import">
                <input type="hidden" name="param" value="target">
                <div class="ilgen-container">
                    <h4><?php _e('Simple URL import', 'ilgen')?></h4>
                    <p class="ilgen-notification">
                        <?php _e('Put each url on a separate line or separate them by commas.', 'ilgen')?>
                    </p>
                    <textarea rows="5" name="import_string"></textarea>
                    <p>
                        <input type="submit" name="ilgen_simple_import" value="<?php _e('Import', 'ilgen')?>" class="button button-primary">
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <div class="box">
        <h4  class="toggle closed" data="box_edit-url"><?php _e('Edit URLs', 'ilgen')?><span class="plus"></span></h4>
        <div class="box-inner" id="box_edit-url">
            <form method="post" action="">
                <?php wp_nonce_field( 'internal_link_generator-targets_edit' );?>
                <input type="hidden" name="action" value="targets_edit">
                <input type="hidden" name="param" value="target">
                <div class="ilgen-container">
                    <h4><?php _e('Simpe URL Edit', 'ilgen')?></h4>
                    <table><tr>
                        <td><select name="target_old">
                            <option></option>
                            <?php if($rows = $this->ilgen_get_targets()){
                                foreach($rows as $k => $tgt){
                                    if($tgt->target) echo "<option>{$tgt->target}</option>";
                                }
                            }?>
                        </select></td>
                        <td class="td-arrow"><button id="ilgenArrowTo">&rarr;</button></td>
                        <td><input type="text" name="target_new"></td>
                        <td><input type="submit" name="ilgen_targets_edit" value="<?php _e('Edit', 'ilgen')?>" class="button button-primary"></td>
                    </tr></table>
                </div>
            </form>
        </div>
    </div>

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
            $('#ilgenArrowTo').on('click', function(e){
                e.preventDefault();
                $('input[name=target_new]').val($('select[name=target_old]').val());
            });
        });
    </script>
</div>
