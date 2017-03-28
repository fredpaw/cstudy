<?php



class Lar_Link{


	/**
	* Get the final url that will be replaced in the frontend.
	* @param	 integer link ID
	* @return	 string the final url.
	* @since    2.0.0
	**/
	public static function get_final_url( $link_id ){
		$link_meta = get_post_meta( $link_id );
		$link_type = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'link_type'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'link_type'][0]:'';
		$link_slug = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0]:'';
		$link_url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'url'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'url'][0]:'';
		$link_internal_url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'internal_url'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'internal_url'][0]:'';
		
		if( $link_type == 'external' OR $link_type == ''){
				if ( get_option('permalink_structure') != '' ) {
					$url = ($link_slug!= '')? site_url().'/go/'.$link_slug : $link_url;
					return '<input disabled type="text" value="'. $url .'" />';
				}else{
					$url =  ($link_slug != '')? site_url().'/index.php?go='.$link_slug : $link_url;
					return '<input disabled type="text" value="'. $url .'" />';
				}
		}elseif($link_type == 'internal'){ // if internal link
				return '<input disabled type="text" value="'. get_permalink($link_internal_url) .'" />';
		}else{
			return '-';
		}
	}
}