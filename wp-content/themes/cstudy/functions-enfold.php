<?php

if(!function_exists('avia_append_search_nav'))
{
  //first append search item to main menu
  add_filter( 'wp_nav_menu_items', 'avia_append_search_nav', 9997, 2 );
  add_filter( 'avf_fallback_menu_items', 'avia_append_search_nav', 9997, 2 );

  function avia_append_search_nav ( $items, $args )
  {
    if(avia_get_option('header_searchicon','header_searchicon') != "header_searchicon") return $items;
    if(avia_get_option('header_position',  'header_top') != "header_top") return $items;

    if ((is_object($args) && $args->theme_location == 'avia') || (is_string($args) && $args = "fallback_menu"))
    {
      global $avia_config;
      $form =  '<form id="main-menu-search" action="'.get_bloginfo('url').'/" method="get"><input id="s" class="" name="s" type="text" value=""><input id="menu-search-submit" type="submit" class="avia-font-entypo-fontello" value=""></form>' ;

      $items .= '<li id="menu-item-search" class="menu-item menu-item-search-dropdown menu-item-avia-special">
							'.$form.'
	        		   </li>';
    }
    return $items;
  }
}