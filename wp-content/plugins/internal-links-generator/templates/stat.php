<div class="container stat">
    <h4><?php _e('Statistics', 'ilgen')?></h4>
    <div class="tablenav top">
        <div class="alignleft">
            <span><?php _e('URIs')?></span>&nbsp;
            <a href="" onclick="insertParam('order', 'targetByASC'); return false;">&uarr;</a>&nbsp;
            <a href="" onclick="insertParam('order', 'targetByDESC'); return false;">&darr;</a>&nbsp;
        </div>
        <div class="alignleft">
            <div class="search-box">
                <input type="search" id="filterInput" value="<?= $_GET['filter']?>">
                <input type="button" class="button" onclick="insertParam('filter', document.getElementById('filterInput').value);" value="<?php _e('Filter', 'ilgen')?>">               
            </div>
        </div>
        <div class="alignright">
            <span><?php _e('Int.Links')?></span>&nbsp;
            <a href="" onclick="insertParam('order', 'countByASC'); return false;">&uarr;</a>&nbsp;
            <a href="" onclick="insertParam('order', 'countByDESC'); return false;">&darr;</a>&nbsp;
        </div>
    </div>
    <?php $rows = $this->ilgen_get_ordered_targets($_GET['order'], $_GET['filter']);
    if($rows['int']): foreach($rows['int'] as $k => $row):?>
        <div class="box">
            <h4 class="toggle closed" data="box_<?= $k?>"><?= $row['target']?>
                <i class="ilgen-linked-count">[<?= intval($row['count'])?>]</i>
                <span></span>
            </h4>
            <?php if($row['keywords']):?>
                <div class="box-inner" id="box_<?= $k?>">
                    <?php foreach($row['keywords'] as $j => $kword):?>
                        <div class="box">
                            <h4 class="toggle closed" data="box__<?= $j?>"><?= $kword->keyword?>
                                <i class="ilgen-linked-count">[<?= intval($kword->linked)?>]</i>
                                <span></span>
                            </h4>
                            <div class="box-inner" id="box__<?= $j?>">
                                <?php if($posts = unserialize($kword->posts)):?>
                                    <ul>
                                        <?php foreach($posts as $post): 
                                            if(!$post) continue;
                                            $permalink = get_the_permalink($post);
                                            $editlink = ($link = get_edit_post_link($post)) ? $link : $permalink;?>
                                            <li>
                                                <a href="<?= $permalink?>"><?= $permalink?></a>
                                                <a href="<?= $editlink?>"><span class="ilgen-edit-post"></span></a>
                                            </li> 
                                        <?php endforeach;?>
                                    </ul>
                                <?php endif;?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
        </div>
    <?php endforeach; else:?>
        <p class="ilgen-notification">
            <?php _e('Target URIs not found!', 'ilgen')?>
        </p>
    <?php endif;?>
</div>