<?php
/**
 *  Widgets for footer
 */

class Footer_Widget extends WP_Widget {
  public function __construct() {
    $widget_ops = array(
      'classname' => 'footer_widget',
      'description' => 'This Widget is for footer content',
    );
    parent::__construct( 'footer_widget', 'Footer Widget', $widget_ops );
  }

  public function widget( $args, $instance ) {
    include_once ("parts/footer_widget.php");
  }
}

/**
 *  Widgets for sidebar
 */

// Study Plan Widgets
class Study_Plan_Widget extends WP_Widget {
  public function __construct() {
    $widget_ops = array(
      'classname' => 'study_plan_widget',
      'description' => 'This Widget is for Sidebar Study Plan block',
    );
    parent::__construct( 'study_plan_widget', 'Study Plan Widget', $widget_ops );
  }

  public function widget( $args, $instance ) {
    include_once ("parts/study_plan_widget.php");
  }
}

// Ad Share Widgets
class Ad_Share_Widget extends WP_Widget {
  public function __construct() {
    $widget_ops = array(
      'classname' => 'ad_share_widget',
      'description' => 'This Widget is for Sidebar Ad Share block',
    );
    parent::__construct( 'ad_share_widget', 'Ad Share Widget', $widget_ops );
  }

  public function widget( $args, $instance ) {
    include_once ("parts/ad_share_widget.php");
  }
}

// Ad Ranking Widgets
class Ad_Ranking_Widget extends WP_Widget {
  public function __construct() {
    $widget_ops = array(
      'classname' => 'ad_ranking_widget',
      'description' => 'This Widget is for Sidebar Ad Ranking block',
    );
    parent::__construct( 'ad_ranking_widget', 'Ad Ranking Widget', $widget_ops );
  }

  public function widget( $args, $instance ) {
    include_once ("parts/ad_ranking_widget.php");
  }
}

/**
 * Add all the widgets
 */
add_action( 'widgets_init', function(){
  register_widget( 'Footer_Widget' );
  register_widget( 'Study_Plan_Widget' );
  register_widget( 'Ad_Share_Widget' );
  register_widget( 'Ad_Ranking_Widget' );
});


/**
 * All shortcode functions
 */

// shortcode [csdownload]
function csdownload_func($atts) {
    $csatts = shortcode_atts( array(
        'cat' => '',
    ), $atts );
    if ($csatts['cat'] == '') {
        return ;
    }

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
                <li><?php the_title();?>
                    <?php if(get_the_terms(get_the_id(),"dlm_download_tag")[0]->slug): ?>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/images/download-<?php echo get_the_terms(get_the_id(),"dlm_download_tag")[0]->slug;?>.png"/>
                    <?php endif; ?>
                    <span style="float:right;"><?php echo do_shortcode('[download id="'.get_the_id().'"]'); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif;
    return ob_get_clean();
}

/**
 * Add all the shortcode
 */
add_shortcode( 'csdownload', 'csdownload_func' );


/**
 * Register all the scripts
 */
function kefu_enqueue_script() {
    wp_enqueue_script( 'kefu-js', get_bloginfo('stylesheet_directory').'/kefu/js/kefu.js', 'jquery', '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'kefu_enqueue_script' );
