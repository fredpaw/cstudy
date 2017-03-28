<?php 


class Lar_Lite_Settings{


	protected $tabs = array();
	protected $key = 'lar_settings';



	/**
	* Define the pages in the constructor.
	* @since     2.0.0
	**/
	public function __construct(){

		$this->tabs = apply_filters('lar_settings_tabs',array(
			'lar_settings' => array(
						'id' => 'lar_settings',
						'title' => __('Settings','links-auto-replacer-pro'),
						'metabox_callback' => false,
						'callback' => array($this , 'admin_page_display')
					),
				'lar_upgrade_settings' => array(
						'id' => 'lar_upgrade_settings',
						'title' => __('Upgrade','links-auto-replacer'),
						'metabox_callback' => false,
						'callback' => array($this , 'display_upgrade_page')
					),
				'lar_about_settings' => array(
						'id' => 'lar_about_settings',
						'title' => __('About','links-auto-replacer'),
						'metabox_callback' => false,
						'callback' => array($this , 'display_about_page')
					),
				

		));
		

		$this->hooks();

	}



	/**
	* Init the settings hooks.
	* @since     2.0.0
	**/
	public function hooks(){
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action('cmb2_init',array($this,'add_main_settings_page_metabox'));
		foreach($this->tabs as $tab){
			if($tab['metabox_callback'] !== false){
				add_action( 'cmb2_init',  $tab['metabox_callback']);
			}
		}
		
	}

	/**
	* register the plugin's settings.
	* @since     2.0.0
	**/
	public function init() {
		register_setting( $this->key, $this->key );
	}


	/**
	* Register the plugin settings page.
	* @since     2.0.0
	**/
	public function add_options_page() {
		global $lar_name;

		//$this->settings_page =   add_menu_page($lar_name,$lar_name,'manage_options',$this->key,array($this, 'admin_page_display'),'',55.05);

	    foreach ($this->tabs as $key => $tab){
			if(isset($tab['callback']))
				add_submenu_page( 'edit.php?post_type=lar_link', $tab['title'], $tab['title'], 'manage_options', $key , $tab['callback']);
			else
				add_submenu_page( 'edit.php?post_type=lar_link', $tab['title'], $tab['title'], 'manage_options', $key , array($this, 'admin_page_display'));

		}
			 
	
		do_action('lar_add_custom_pages');
		
	}

	/**
	* Include main settings page template
	* @since     2.0.0
	**/
	public function admin_page_display(){
		// include partial

		require_once LAR_DIR . 'admin/partials/settings.php';
		
	}



	/**
	* Include about page template
	* @since     2.0.0
	**/
	public function display_about_page(){
		require_once LAR_DIR . 'admin/partials/about.php';

	}



	/**
	* Include upgrade page template
	* @since     2.0.0
	**/
	public function display_upgrade_page(){
		require_once LAR_DIR . 'admin/partials/upgrade.php';

	}






	/**
	* The meta box fields of the upgrade settings page
	* @since     2.0.0
	**/
	public function add_upgrade_settings_page_metabox(){

		$cmb = new_cmb2_box( array(
			'id'      => 'lar_upgrade_settings',
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		


	}



	/**
	* The meta box fields of the main settings page
	* @since     2.0.0
	**/
	public function add_main_settings_page_metabox(){



		

		$cmb = new_cmb2_box( array(
			'id'      => 'lar_settings',
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields
		$cmb->add_field( array(
			'name'    => __( 'Enable Auto Replcement', 'links-auto-replacer' ),
			'id'      => LAR_LITE_PLUGIN_PREFIX . 'enable',
			'type'    => 'checkbox',
			'default' => $this->get_if_enabled( true )
		) );

		$cmb->add_field( array(
			'name'    => __( '<h2>"Add Auto Link" Defaults</h2>', 'links-auto-replacer' ),
			'id'	  => 'title',
			'type'    => 'title',
			//'default' => 'yes',
		) );

		$cmb->add_field( array(
			'name' => __( 'Dofollow?', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'do_follow',
			'type' => 'checkbox',
			'description' => __('if you checked this option, you will allow search engines to follow this link and use it in ranking.','links-auto-replacer'),
		) );

		$cmb->add_field( array(
			'name' => __( 'Open in:', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'open_in',
			'type' => 'select',
			'default' => '_self',
			'options' => array(
					  '_self' => __('Same Window','links-auto-replacer'),
					  '_blank' => __('New Window','links-auto-replacer'),
				),
			
		) );


		$cmb->add_field( array(
			'name' => __( 'Shrink?', 'links-auto-replacer' ),
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'shrink',
			'type' => 'checkbox',
			'description' => __('The link will be shortened (e.g example.com/go/amazon)','links-auto-replacer'),
		) );



		$cmb->add_field( array(
			'name' => __( 'Case Sensitive?', 'links-auto-replacer' ),
			
			'id'   => LAR_LITE_PLUGIN_PREFIX . 'is_sensitive',
			'type' => 'checkbox',
			'description' => __('If you checked this option, the plugin will replace the keywords exactly according to the letters case.','links-auto-replacer').' <span id="lar_slug_example"></span>',
		));
		
		

		


		
	}
	




	/**
	* Validate the license key.
	* @param	 string the option's key.
	* @return	 string the value of the key.
	* @since     2.0.0
	**/
	public function get_option( $key = '' ) {
		return cmb2_get_option( $this->key, $key );
	}

	private function get_if_enabled( $default ){
		return  ($this->get_option(LAR_LITE_PLUGIN_PREFIX . 'enable'))  ? '' : ( $default ? (string) $default : '' );
	}

	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'tabs', 'key' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}










}




//$GLOBALS['lar_settings'] = new lar_Settings();