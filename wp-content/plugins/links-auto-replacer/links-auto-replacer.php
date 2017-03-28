<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://waseem-senjer.com/product/affiliate-butler-pro/
 * @since             1.0
 * @package           Links_Auto_Replacer
 *
 * @wordpress-plugin
 * Plugin Name:       Keywords to Links Converter
 * Plugin URI:        https://wpruby.com/plugin/affiliate-butler-pro/
 * Description:       Auto replace your affiliate links and track them.
 * Version:           2.1.3
 * Author:            Waseem Senjer
 * Author URI:        https://waseem-senjer.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       links-auto-replacer
 * Domain Path:       /languages
 */

define('LAR_VERSION','2.0.0');
define('LAR_URL',plugin_dir_url(__FILE__));
define('LAR_DIR',plugin_dir_path(__FILE__));

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('LAR_LITE_PLUGIN_PREFIX','_lar_links_');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lar-activator.php
 */
function activate_Links_Auto_Replacer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lar-activator.php';
	Links_Auto_Replacer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lar-deactivator.php
 */
function deactivate_Links_Auto_Replacer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lar-deactivator.php';
	Links_Auto_Replacer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Links_Auto_Replacer' );
register_deactivation_hook( __FILE__, 'deactivate_Links_Auto_Replacer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_Links_Auto_Replacer() {

	$plugin = new Links_Auto_Replacer();
	$plugin->run();

}
run_Links_Auto_Replacer();

function lar(){
	static $object = null;
	if ( is_null( $object ) ) {

		$object = new Lar_Lite_Settings();
	}

	return $object;
}

add_action('init',function(){
	lar();
});

$GLOBALS['lar_name'] = 'Links Auto Replacer Lite';