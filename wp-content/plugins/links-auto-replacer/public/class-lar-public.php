<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://waseem-senjer.com/product/links-auto-replacer-pro/
 * @since      2.0.0
 *
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/public
 * @author     Waseem Senjer <waseem.senjer@gmail.com>
 */
class Links_Auto_Replacer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $Links_Auto_Replacer    The ID of this plugin.
	 */
	private $Links_Auto_Replacer;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $Links_Auto_Replacer       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Links_Auto_Replacer, $version ) {

		$this->Links_Auto_Replacer = $Links_Auto_Replacer;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Links_Auto_Replacer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Links_Auto_Replacer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->Links_Auto_Replacer, plugin_dir_url( __FILE__ ) . 'css/lar-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Links_Auto_Replacer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Links_Auto_Replacer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	
		wp_enqueue_script( $this->Links_Auto_Replacer, plugin_dir_url( __FILE__ ) . 'js/lar-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * This is the main function that converts the keywords to links.
	 * It's used as (the_content, the_exerpt) filters callback.
	 * @param	 string the original content
	 * @return 	 string	the converted content.
	 * @since    1.0.0
	 */
	public function lar_auto_replace_links( $content ){
		
		$lar_global_enabled = lar()->get_option(LAR_LITE_PLUGIN_PREFIX.'enable');
		if($lar_global_enabled === 'off') return $content;
		
		global $wpdb; 
		global $post;

		$is_disabled =  get_post_meta( $post->ID, 'lar_disabled'  , true );
		
		if($is_disabled == 'on') return $content;


		$links = get_posts('post_type=lar_link&post_status=publish&posts_per_page=-1');

		foreach ($links as $link) {
			$link_meta = get_post_meta($link->ID);
			
			$dofollow = '';
			$link_dofollow = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'do_follow'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'do_follow'][0]:'';
			if($link_dofollow != 1){
				$dofollow = 'rel="nofollow"';
			}
			$link_type = (isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'link_type'][0]))?$link_meta[LAR_LITE_PLUGIN_PREFIX.'link_type'][0]:'';
			if($link_type == 'external' OR $link_type ==''){
				if ( get_option('permalink_structure') != '' ) {
					$url = (isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0]) && $link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0]!= '')? site_url().'/go/'.$link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0] : $link_meta[LAR_LITE_PLUGIN_PREFIX.'url'][0];
				
				}else{
					$url = (isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0]) && $link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0] != '')? site_url().'/index.php?go='.$link_meta[LAR_LITE_PLUGIN_PREFIX.'slug'][0] : $link_meta[LAR_LITE_PLUGIN_PREFIX.'url'][0];
				
				}
			}elseif($link_type == 'internal'){ // if internal link
					$url = get_permalink($link_meta[LAR_LITE_PLUGIN_PREFIX.'internal_url'][0]);
			}elseif($link_type == 'popup'){ // if internal link
					$url = '#lar_popup_'. $link->ID;
			}elseif($link_type == 'popup_image'){
					$url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_image'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_image'][0]:'';
			}elseif($link_type == 'popup_gallery'){
					$url = 'javascript:void(0)';
			}elseif($link_type == 'popup_video'){
					$url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_videourl'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_videourl'][0]:'';
			}elseif($link_type == 'popup_map'){
					$url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_mapurl'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_mapurl'][0]:'';
			}elseif($link_type == 'sharing_tip'){
					$url = isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'sharing_tip'][0])?$link_meta[LAR_LITE_PLUGIN_PREFIX.'sharing_tip'][0]:'javascript:void(0)';
			}

			




			
			
			$keywords = unserialize( $link_meta[LAR_LITE_PLUGIN_PREFIX.'keywords'][0] );
			
			// if the keywords not really inserted
			if($keywords === false) continue;

			$doc = new DOMDocument();
			
			@$doc->loadHTML('<?xml encoding="UTF-8">'.$content);
			$doc->encoding = 'UTF-8';
			foreach($keywords as $keyword){
				$keyword = html_entity_decode(stripslashes(wptexturize($keyword)));

				$extra_attrs = apply_filters('lar_add_extra_atts',$link->ID, $post->ID);

				$final_url = ' <a href="'.$url.'" '.$extra_attrs.' '.$dofollow.' target="'.$link_meta[LAR_LITE_PLUGIN_PREFIX.'open_in'][0].'">${1}</a>';
				$post_content = html_entity_decode(($content));

				// sensitivity modifier
				if(!isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'is_sensitive'][0])){
					$i = 'i';
				}elseif(isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'is_sensitive'][0]) && $link_meta[LAR_LITE_PLUGIN_PREFIX.'is_sensitive'][0] !== 'on'){
					$i = '';
				}else{
					$i = '';
				}
				$changed =  $this->showDOMNode($doc,$keyword,$final_url,$i);


				$mock = new DOMDocument;
			    $body = $changed->getElementsByTagName('body')->item(0);
				if(isset($body->childNodes)){
					foreach ($body->childNodes as $child){
			    		$mock->appendChild($mock->importNode($child, true));
					}
				}
				

				$content = htmlspecialchars_decode($mock->SaveHTML());

				// @since 2.1 adding the popup div
				if($link_type == 'popup'){
					$content .= '<div id="lar_popup_'. $link->ID.'" class="white-popup mfp-hide">';
					$content .= (isset($link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_content'][0]))?$link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_content'][0]:'';
					$content .= '</div>';
				}elseif($link_type == 'popup_gallery'){
					$content .= '<div class="lar-gallery" id="lar_gallery_'.$link->ID.'">';
					$images  = unserialize($link_meta[LAR_LITE_PLUGIN_PREFIX.'popup_gallery'][0]);
					foreach($images as $image){

						$content .= '<a href="'. $image .'"></a>';
					}
					$content .= '</div>';
				}elseif($link_type == 'sharing_tip'){
					$content .= '<div id="grabMe" style="display:none;">';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/facebook.png" /> ';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/twitter.png" /> ';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/linkedin.png" /> ';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/gplus.png" /> ';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/tumblr.png" /> ';
					$content .= ' <img class="lar_social_share_icon" src="'.LARPRO_URL.'public/images/share/style01/reddit.png" /> ';
					$content .= '</div>';
				}
			}
			
			
		}
		
		// Replace Content Filter
		$content = apply_filters('lar_replace_content', $content);
		
		return $content;
	}

	/**
	 * Helper function that is used to get text nodes from the DomNode and replace the keywords
	 * It's used in $this -> lar_auto_replace_links()
	 * @param	 DOMNode current node
	 * @param	 string The keyword that should be replaced.
	 * @param	 string The replacement string, it's the url in our context.
	 * @return 	 DOMNode The replaced Node.
	 * @since    1.5.0
	 */
	public function showDOMNode(DOMNode $domNode,$word,$replacement,$case_sensitive) {
	    foreach ($domNode->childNodes as $node)
	    {
	        if($node->nodeName == '#text'){
	        	$node->nodeValue =  preg_replace('/('.($word).')/'.$case_sensitive.'u', $replacement, $node->nodeValue);
	        }
	        if($node->hasChildNodes()) {
	            $this->showDOMNode($node,$word,$replacement,$case_sensitive);
	        }
	    } 
	    return $domNode;    
	}

	/**
	 * This method adds a new rewrite rules for external links with slugs.
	 * @param	 array  The original rules.
	 * @return	 array The new rules.
	 * @since    1.0.0
	 */
	public function lar_setup_rewrite_rules($rules)
	{
	    $newrules['^go/([^/]*)/?$'] = 'index.php?go=$matches[1]';
	    $newrules['^index.php?go=([^/]*)?$'] = 'index.php?go=$matches[1]';
	    return $newrules + $rules;
	}





	/**
	 * This method adds a new query var [go] 
	 * @TODO Should be enhanced by removing the [go] prefix from urls with slugs.
	 *
	 * @param	 array  The original vars.
	 * @return	 array The new vars with [go] added.
	 * @since    1.0.0
	 */
	public function add_go_variable($vars)
	{
	    array_push($vars, 'go');
	    return $vars;
	}


	/**
	* This method is responsible for redirecting slugged links to its original urls.
	* Also, it, it will save a new stats record.
	*
	* @since    1.0.0
	*/
	function lar_redirect(){
		global $wp_query;
		
		if(isset($wp_query->query_vars['go'])){
			global $wpdb;
			$link = get_posts('post_type=lar_link&meta_key='.LAR_LITE_PLUGIN_PREFIX.'slug&meta_value='.$wp_query->query_vars['go']);
			$link_url = get_post_meta($link[0]->ID, LAR_LITE_PLUGIN_PREFIX.'url',true);

			if(!is_null($link_url)){
				do_action('lar_link_redirected', $link[0]->ID);
				wp_redirect($link_url);
				exit;
			}
			
		}
		
	}

}
