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

add_action( 'widgets_init', function(){
  register_widget( 'Footer_Widget' );
});