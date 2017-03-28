<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://waseem-senjer.com/product/links-auto-replacer-pro/
 * @since      2.0.0
 *
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Links_Auto_Replacer
 * @subpackage Links_Auto_Replacer/includes
 * @author     Waseem Senjer <waseem.senjer@gmail.com>
 */
class Links_Auto_Replacer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Links_Auto_Replacer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $Links_Auto_Replacer    The string used to uniquely identify this plugin.
	 */
	protected $Links_Auto_Replacer;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	public static $lar_name;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->Links_Auto_Replacer = 'links-auto-replacer';
		$this->version = '2.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Links_Auto_Replacer_Loader. Orchestrates the hooks of the plugin.
	 * - Links_Auto_Replacer_i18n. Defines internationalization functionality.
	 * - Links_Auto_Replacer_Admin. Defines all hooks for the admin area.
	 * - Links_Auto_Replacer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		
		/**
		*
		* Base62 Class
		**/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class.base62.php';

		/**
		* Link Class
		*
		**/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class.link.php';

		/**
		 * The Metabox Library
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cmb/init.php';
		
		/**
		 *
		 * The Helper Class
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-settings.php';
		
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lar-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-lar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lar-public.php';

		$this->loader = new Links_Auto_Replacer_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Links_Auto_Replacer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Links_Auto_Replacer_i18n();
		$plugin_i18n->set_domain( $this->get_Links_Auto_Replacer() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );


	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Links_Auto_Replacer_Admin( $this->get_Links_Auto_Replacer(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_admin, 'register_links_post_type' );
		
		
		$this->loader->add_action('cmb2_init',$plugin_admin, 'lar_links_register_metabox');
		$this->loader->add_action( 'wp_ajax_my_pre_submit_validation', $plugin_admin, 'pre_submit_link_validation' );
		

		if(isset($_GET['post_type']) OR isset($_GET['post'])){
			if( (isset($_GET['post_type']) && $_GET['post_type'] == 'lar_link') OR (isset($_GET['post']) && get_post_type($_GET['post'])=='lar_link')){
				$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'insert_validation_nonce' );
			}
		}
		

		// disabled for individual posts
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'disable_for_single_post' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'lar_meta_save' );
		

		// edit the admin columns
		$this->loader->add_filter('manage_lar_link_posts_columns', $plugin_admin, 'lar_columns_head');
		$this->loader->add_filter('manage_lar_link_posts_custom_column', $plugin_admin, 'lar_columns_content',10,2);
		
		// @since 2.0.1
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_upgrade_to_pro_box' );
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_row_actions', 10, 2 );
	
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Links_Auto_Replacer_Public( $this->get_Links_Auto_Replacer(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		
		$this->loader->add_filter( 'the_content', $plugin_public, 'lar_auto_replace_links' );
		$this->loader->add_filter( 'the_excerpt', $plugin_public, 'lar_auto_replace_links' );
		
		
		$this->loader->add_filter( 'rewrite_rules_array', $plugin_public, 'lar_setup_rewrite_rules' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_go_variable' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'lar_redirect' );



		
	}




	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_Links_Auto_Replacer() {
		return $this->Links_Auto_Replacer;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Links_Auto_Replacer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
