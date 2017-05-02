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


