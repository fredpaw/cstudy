<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://waseem-senjer.com/product/links-auto-replacer-lite/
 * @since      2.0.0
 *
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/admin
 * @author     Waseem Senjer <waseem.senjer@gmail.com>
 */
class Links_Auto_Replacer_Admin {

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
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $last_link_id;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $Links_Auto_Replacer       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Links_Auto_Replacer, $version ) {

		$this->Links_Auto_Replacer = $Links_Auto_Replacer;
		$this->version = $version;
		$this->last_link_id = base62::encode(get_option('last_lar_link_id') + 100);
		
		

	}

	/**
	 * Register the stylesheets for the admin area.
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
		
		wp_enqueue_style( $this->Links_Auto_Replacer, plugin_dir_url( __FILE__ ) . 'css/lar-links-admin.css', array(), $this->version, 'all' );
		
		


	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->Links_Auto_Replacer, plugin_dir_url( __FILE__ ) . 'js/lar-links-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * The plugin use this method to register the main custom post type of the links.
	 * 
	 * @since    2.0.0
	 */
	public function register_links_post_type(){
		$labels = array(
			'name'               => _x( 'Auto Links', 'post type general name', 'links-auto-replacer' ),
			'singular_name'      => _x( 'Auto Link', 'post type singular name', 'links-auto-replacer' ),
			'menu_name'          => _x( 'Auto Links', 'admin menu', 'links-auto-replacer' ),
			'name_admin_bar'     => _x( 'Auto Link', 'add new on admin bar', 'links-auto-replacer' ),
			'add_new'            => _x( 'Add New Auto Link', 'book', 'links-auto-replacer' ),
			'add_new_item'       => __( 'Add New Auto Link', 'links-auto-replacer' ),
			'new_item'           => __( 'New Auto Link', 'links-auto-replacer' ),
			'edit_item'          => __( 'Edit Auto Link', 'links-auto-replacer' ),
			'view_item'          => __( 'View Auto Link', 'links-auto-replacer' ),
			'all_items'          => __( 'All Auto Links', 'links-auto-replacer' ),
			'search_items'       => __( 'Search Auto Links', 'links-auto-replacer' ),
			'parent_item_colon'  => __( 'Parent Auto Links:', 'links-auto-replacer' ),
			'not_found'          => __( 'No links found.', 'links-auto-replacer' ),
			'not_found_in_trash' => __( 'No links found in Trash.', 'links-auto-replacer' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'lar_link' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(  'author' ),
			'menu_icon'			 => 'dashicons-editor-unlink',
			
		);

		register_post_type( 'lar_link', $args );
	}


	


	/**
	 *	Using CMB2 library The method will build a custom meta box for the user to fill the link information.
	 * 
	 *  @since    2.0.0
	 */
	public function lar_links_register_metabox() {
		

		$add_links_box = new_cmb2_box( array(
			'id'            => LAR_LITE_PLUGIN_PREFIX . 'metabox',
			'title'         => __( 'Add new Link', 'links-auto-replacer' ),
			'object_types'  => array( 'lar_link', ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );


		$add_links_box->add_field( array(
			'name' => __( 'Keyword/s', 'links-auto-replacer' ),
			
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'keywords',
			'type' => 'text_medium',
			'repeatable' => true,
		) );

		///////////////

		
		//may add new fields
		do_action('lar_add_link_custom_field', $add_links_box);


		$add_links_box->add_field( array(
			'name' => __( 'Link Type', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'link_type',
			'type' => 'select',
			'options' => array(
					  'external' => __('External','links-auto-replacer'),
					  'internal_pro' => __('Internal (PRO)','links-auto-replacer'),
					  'popup_pro' => __('Popup (PRO)','links-auto-replacer'),
					  'popup_image_pro' => __('Popup Image (PRO)','links-auto-replacer'),
					  'popup_gallery_pro' => __('Popup Gallery (PRO)','links-auto-replacer'),
					  'popup_video_pro' => __('Popup Video (PRO)','links-auto-replacer'),
					  'popup_map_pro' => __('Google Map (PRO)','links-auto-replacer'),
					  'url_share_pro' => __('Shared URL (PRO)','links-auto-replacer'),
				),
			'description' => __('','links-auto-replacer'),
		) );

		$add_links_box->add_field( array(
			'name' => __( 'URL (Link)', 'links-auto-replacer' ),
			'default' => 'http://',
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'url',
			'type' => 'text_url',
		) );

		////////////////
		

		$add_links_box->add_field( array(
			'name' => __( 'Dofollow?', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'do_follow',
			'type' => 'checkbox',
			'default'=> lar()->get_option(LAR_LITE_PLUGIN_PREFIX . 'do_follow'),
			'description' => __('if you checked this option, you will allow search engines to follow this link and use it in ranking.','links-auto-replacer'),
		) );

		$add_links_box->add_field( array(
			'name' => __( 'Open in:', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'open_in',
			'type' => 'select',
			'default'=> lar()->get_option(LAR_LITE_PLUGIN_PREFIX . 'open_in'),
			'options' => array(
					  '_self' => __('Same Window','links-auto-replacer'),
					  '_blank' => __('New Window','links-auto-replacer'),
				),
			'description' => __('When the visitor click on the link, it\'s either open in a new window or the same window.','links-auto-replacer'),
		) );


		$add_links_box->add_field( array(
			'name' => __( 'Shrink?', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'shrink',
			'type' => 'checkbox',
			'default'=> lar()->get_option(LAR_LITE_PLUGIN_PREFIX . 'shrink'),
			'description' => __('The link will be shortened (e.g example.com/go/amazon)','links-auto-replacer'),
		) );


		

		$add_links_box->add_field( array(
			'name' => __( 'Slug', 'links-auto-replacer' ),
			'default' => $this->last_link_id,
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'slug',
			'type' => 'text_small',
			'description' => __('The slug for the shortened link','links-auto-replacer').' <span id="lar_slug_example"></span>',
		));



		$add_links_box->add_field( array(
			'name' => __( 'Case Sensitive?', 'links-auto-replacer' ),
			
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'is_sensitive',
			'type' => 'checkbox',
			//'default'=> lar()->get_option(LAR_LITE_PLUGIN_PREFIX . 'is_sensitive'),
			'description' => __('If you checked this option, the plugin will replace the keywords exactly according to the letters case.','links-auto-replacer').' <span id="lar_slug_example"></span>',
		));

		
	
	}

	/**
	 * Ajax called action to validate the link data of the meta box.	
	 * @since    2.0.0
	 */
	public function pre_submit_link_validation(){
		$errors = array();
	    //simple Security check
	    check_ajax_referer( 'my_pre_submit_validation', 'security' );

	   
	    parse_str($_POST['form_data'], $link);
	    $keywords = array_filter($link[LAR_LITE_PLUGIN_PREFIX.'keywords']);
	    $keywords = array_map('trim', $keywords);
	    if( count(array_unique($keywords)) < count($keywords) ){
	    	$errors['keywords'] = __('Please, remove the repetition from the keywords','links-auto-replacer');
	    }
	    

	    
	    if(empty($keywords))
	    {
	    	$errors['keywords'] = __('Please provide keyword/s','links-auto-replacer');
	    }
	    $link_type = isset($link[LAR_LITE_PLUGIN_PREFIX . 'link_type'])?$link[LAR_LITE_PLUGIN_PREFIX . 'link_type']:'';
	    $link_url = isset($link[LAR_LITE_PLUGIN_PREFIX . 'url'])?$link[LAR_LITE_PLUGIN_PREFIX . 'url']:'';
	    if($link_type == 'external' OR $link_type ==''){
			if($link_url == '' OR filter_var($link_url, FILTER_VALIDATE_URL) === false)
			{
			    $errors['url'] = __('Please provide a valid url','links-auto-replacer');
			}
		}
		


	    // we don't want to touch the DB unless the user fill all the data right.
	    if(empty($errors)){

		    $keywords = $this->get_meta_values(LAR_LITE_PLUGIN_PREFIX . 'keywords', 'lar_link','publish',$link['post_ID']);
		    foreach ($keywords as $key => $value) {
		   		$intersect = array_intersect($link[LAR_LITE_PLUGIN_PREFIX.'keywords'], unserialize($value));
		   		if(!empty($intersect)){
		   			$errors['keywords'] = 'keyword\s ( '. implode(',', $intersect) .' ) is already exist';
		   			break;
		   		}
		    }

		
		    $urls = $this->get_meta_values(LAR_LITE_PLUGIN_PREFIX . 'url', 'lar_link','publish',$link['post_ID']);
		    if($link[LAR_LITE_PLUGIN_PREFIX . 'link_type'] == 'external'){
			    if(in_array($link[LAR_LITE_PLUGIN_PREFIX . 'url'], $urls)){
			    	$errors['url'] = __('URL is already exist','links-auto-replacer');
			    }
			}
		    
			$link_slug = (isset($link[LAR_LITE_PLUGIN_PREFIX . 'slug']))?$link[LAR_LITE_PLUGIN_PREFIX . 'slug']:'';
			if(trim($link_slug)!=''){    
			    $slugs = $this->get_meta_values(LAR_LITE_PLUGIN_PREFIX . 'slug', 'lar_link','publish',$link['post_ID']);
			    if(in_array($link_slug, $slugs)){
			    	$errors['slugs'] = sprintf(__( 'Slug (%s) is already exist','links-auto-replacer'),$link_slug);
			    }
			}
		} //empty($errors)
	    	
		// extending the validation filter
		if(empty($errors)){
			$errors = apply_filters('lar_post_validate_link',$errors,$link); 
		}


	    if(!empty($errors)){
	    	echo 'Please correct the following errors: <ul id="lar_errors"><li>';
	    	echo implode('</li><li>', $errors);
	    	echo '</li></ul>';
	    }else{
	    	add_option('last_lar_link_id',$link['post_ID']) or update_option('last_lar_link_id',$link['post_ID']);
	    	echo '1';
	    }

	    die();
	}



	/**
	 * A helper method to get all the values sored in wp_postmeta table that has one key.
	 * @param	 string the meta key.
	 * @param	 string the post type
	 * @param	 string the post status
	 * @param	 number the post ID to exlude from the query
	 * @return 	 stdObject	All the rows with the specified key.
	 * @since    2.0.0
	 */
	public function get_meta_values( $key = '', $type = 'post', $status = 'publish', $post_ID = 0 ) {
		
	    global $wpdb;

	    if( empty( $key ) )
	        return;

	    $r = $wpdb->get_col( $wpdb->prepare( "
	        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	        WHERE pm.meta_key = '%s' 
	        AND p.post_status = '%s' 
	        AND p.post_type = '%s'
	        AND p.ID <> ".$post_ID
	        , $key, $status, $type ) );
	    
	    return $r;
	}


	/**
	 * This one is used to enqueue the scripts into our link post type page.
	 * @since    2.0.0
	 */
	public function insert_validation_nonce(){
		if(isset($_GET['post'])){
	 		$link_slug = get_post_meta($_GET['post'], LAR_LITE_PLUGIN_PREFIX.'slug',true);
		}
		
	 	?>
		 	<script type="text/javascript">
		 		<?php do_action('lar_add_js_variables'); ?>
		 		var validation_nonce = '<?php echo wp_create_nonce( 'my_pre_submit_validation' ); ?>'; 
		 		var plugin_prefix = '<?php echo LAR_LITE_PLUGIN_PREFIX; ?>';
		 		<?php if(isset($link_slug)): ?>
		 			var last_link_id = '<?php echo $link_slug; ?>'; 
		 		<?php else: ?>
		 			var last_link_id = '<?php echo $this->last_link_id; ?>'; 
		 		<?php endif; ?>
		 		var home_url = '<?php echo home_url(); ?>'; 
		 	</script>
	 	<?php 
	 	wp_enqueue_script( $this->Links_Auto_Replacer.'-validation', plugin_dir_url( __FILE__ ) . 'js/lar-links-validation.js', array( 'jquery' ), $this->version, false );	 	


	}



	/**
	 * Adding a new meta box to disable the auto-replacement for a specific post,page.
	 *
	 * @since    2.0.0
	 */
	public function disable_for_single_post(){
		global $lar_name;
		$screens = apply_filters('lar_disable_box_screens',array( 'post', 'page' ));

	    foreach ( $screens as $screen ) {
	        add_meta_box( 'lar_meta', __( 'Disable '. $lar_name .' for this post', 'links-auto-replacer' ), array($this,'lar_meta_callback'), $screen );
	    }
	}

	/**
	 * The callback of the disable meta box.
	 * @param	 StdObject the post object
	 * @since    2.0.0
	 */
	public function lar_meta_callback( $post ) {
	    wp_nonce_field( basename( __FILE__ ), 'lar_nonce' );

	    $lar_disabled_meta = get_post_meta( $post->ID );
	    
	    ?>
	 
	    <p>
	        
	        <input type="checkbox" name="lar_disabled" id="lar_disabled" <?php if (   $lar_disabled_meta['lar_disabled'][0] == 'on' ) echo 'checked="checked"'; ?> />
	   		<label for="lar_disabled" class="lar-row-title"><?php _e( 'Disable', 'lar-links-auto-replacer' )?></label>
	    </p>
	 
	    <?php  
	}

	/**
	 * Saving the `disable` option for each post,page.
	 * @param	 integer the post ID.
	 * @since    1.5.0
	 */
	function lar_meta_save( $post_id ) {
 		// If post_type is not post or page, just do nothing.
		$screens = apply_filters('lar_disable_box_screens',array( 'post', 'page' ));

 		if(!in_array(get_post_type($post_id), $screens)) return;
	    // Checks save status
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ 'lar_nonce' ] ) && wp_verify_nonce( $_POST[ 'lar_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
	 
	    // Exits script depending on save status
	    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
	        return;
	    }
	 
	    // Checks for input and sanitizes/saves if needed
	    if( isset( $_POST[ 'lar_disabled' ] ) ) {

	        update_post_meta( $post_id, 'lar_disabled',  'on'  );
	    }else{
	        update_post_meta( $post_id, 'lar_disabled',  'off'  );

	    }
	}


	/**
	* Change the default Wordpress colums heads for links post types.
	* @param	 array default admin colums heads.
	* @return	 array altered colums heads.
	* @since    2.0.0
	*/
	public function lar_columns_head( $defaults ) {
			unset($defaults['title']);
			unset($defaults['author']);
		    $new = array();
			foreach($defaults as $key => $title) {
			    if ($key==apply_filters('lar_put_colums_before','date')){ // Put the Thumbnail column before the Author column
			      		
			      		$new['keywords'] = __('Keyword/s','links-auto-replacer');	    		
		    			$new['link'] = __('Link','links-auto-replacer');
		    			$new['lite_total_clicks'] = __('Total Clicks','links-auto-replacer');
		    			$new = apply_filters('lar_links_colums_heads',$new);
		    			
			  		}
			    	$new[$key] = $title;
			}
			
			return $new;

		    
	}
		 
	/**
	* Change the default Wordpress colums for links post types.
	* @param	 array default admin colums.
	* @return	 array altered colums.
	* @since    2.0.0
	**/
	public function lar_columns_content($column_name, $post_ID) {

		    if($column_name == 'link'){
		    	echo Lar_Link::get_final_url($post_ID);
		    }
		    if($column_name == 'lite_total_clicks'){
		    	echo '<a href="'.admin_url('admin.php?page=lar_upgrade_settings').'">(PRO Feature)</a>';
		    }

		    if($column_name == 'keywords'){
		    	$keywords = get_post_meta($post_ID, LAR_LITE_PLUGIN_PREFIX.'keywords',true);
		    	$stats = get_post_meta($post_ID, LAR_LITE_PLUGIN_PREFIX.'stats');

		    	if(!empty($keywords)){
		    		?>
		    		<strong><a class="row-title" href="<?php echo get_edit_post_link($post_ID);  ?>" title="Edit"><?php echo implode(' | ',$keywords); ?></a></strong>
			    		<div class="row-actions">
				    		<span class="edit"><a href="<?php echo get_edit_post_link($post_ID);  ?>" title="Edit this item">Edit</a> | </span>	
				    		<span class="trash"><a class="submitdelete" title="Move this item to the Trash" href="<?php echo get_delete_post_link($post_ID); ?>">Trash</a>  </span>
				    		<span class="quick_stats" class="edit"> | <a href="<?php echo admin_url('admin.php?page=lar_upgrade_settings'); ?>"><?php _e('Stats (PRO)','links-auto-replacer'); ?></a> </span>	
				    		<?php do_action('lar_add_quick_links', $post_ID); ?>

			    		</div>

		    		<?php
		    		
		    	}else{
		    		echo '-';
		    	}
		    	
		    }
	}

	/**
	 * Adding a new meta box for promoting the pro version
	 *
	 * @since    2.0.1
	 */
	public function add_upgrade_to_pro_box(){
		global $lar_name;
	    add_meta_box( 'lar_pro_metabox', __( 'Upgrade to PRO', 'links-auto-replacer' ), array($this,'meta_pro_callback'), 'lar_link', 'side' );
	}

	public function meta_pro_callback($post){
		echo '<a href="https://wpruby.com/plugin/affiliate-butler-pro/" target="_blank"><img style="width:100%;" src="'.LAR_URL.'admin/images/upgrade_to_pro.png" /></a>';
	}

	public function remove_row_actions( $actions, $post )
	{
	  global $current_screen;
		if( $current_screen->post_type != 'lar_link' ) return $actions;
		unset( $actions['edit'] );
		unset( $actions['view'] );
		unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		//$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );

		return $actions;
	}
}
