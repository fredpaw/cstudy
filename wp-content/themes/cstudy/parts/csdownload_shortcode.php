<?php
//get the cat from download
$args = array(
    'post_type' => 'dlm_download',
    'tax_query' => array(
        array(
            'taxonomy' => 'dlm_download_category',
            'field'    => 'slug',
            'terms'    => $csatts['cat'],
        ),
    ),
);
$query = new WP_Query( $args );

//get all the filename, type, link from cat
if ( $query->have_posts() ) :
    ob_start();?>
    <ul class='csa-download-list'>
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <li><?php the_title();?> <img src="<?php echo get_stylesheet_directory_uri() ?>/images/download-<?php echo get_the_terms(get_the_id(),"dlm_download_tag")[0]->slug;?>.png"/> <span style="float:right;"><?php echo do_shortcode('[download id="'.get_the_id().'"]'); ?></span></li>
    <?php endwhile; ?>
    </ul>
<?php endif;
return ob_get_clean();