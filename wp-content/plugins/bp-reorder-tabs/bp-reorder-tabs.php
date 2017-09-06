<?php
/**
 * Plugin Name: BuddyPress Reorder Tabs
 * Plugin URI:  http://buddyboss.com/product/buddypress-reorder-tabs/
 * Description: Easily reorder BuddyPress Profile and Group tabs from the WordPress admin
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.0.9
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if (!defined( 'BUDDYBOSS_REORDER_TABS_PLUGIN_VERSION' ) ) {
  define( 'BUDDYBOSS_REORDER_TABS_PLUGIN_VERSION', '1.0.9' );
}

// Database version
if (!defined( 'BUDDYBOSS_REORDER_TABS_PLUGIN_DB_VERSION' ) ) {
  define( 'BUDDYBOSS_REORDER_TABS_PLUGIN_DB_VERSION', 1 );
}

// Directory
if (!defined( 'BUDDYBOSS_REORDER_TABS_PLUGIN_DIR' ) ) {
  define( 'BUDDYBOSS_REORDER_TABS_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if (!defined( 'BUDDYBOSS_REORDER_TABS_PLUGIN_URL' ) ) {
  $plugin_url = plugin_dir_url( __FILE__ );

  // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
  if ( is_ssl() )
    $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

  define( 'BUDDYBOSS_REORDER_TABS_PLUGIN_URL', $plugin_url );
}

// File
if (!defined( 'BUDDYBOSS_REORDER_TABS_PLUGIN_FILE' ) ) {
  define( 'BUDDYBOSS_REORDER_TABS_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Main
 *
 * @return void
 */
function buddypress_reorder_tabs_init()
{
  global $bp, $BUDDYBOSS_REORDER_TABS;

  $main_include  = BUDDYBOSS_REORDER_TABS_PLUGIN_DIR  . 'includes/main-class.php';

  try
  {
    if ( file_exists( $main_include ) )
    {
      require( $main_include );
    }
    else{
      $msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'bp-reorder-tabs' ), $main_include );
      throw new Exception( $msg, 404 );
    }
  }
  catch( Exception $e )
  {
    $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-reorder-tabs' ), $e->getMessage() );
    echo $msg;
  }

  $BUDDYBOSS_REORDER_TABS = BuddyPress_Reorder_Tabs_Plugin::instance();
  
}
add_action( 'plugins_loaded', 'buddypress_reorder_tabs_init' );

/**
 * Must be called after hook 'plugins_loaded'
 * @return BuddyPress Reorder Tabs Plugin main controller object
 */
function buddypress_reorder_tabs(){
  global $BUDDYBOSS_REORDER_TABS;

  return $BUDDYBOSS_REORDER_TABS;
}

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

	function register_buddyboss_menu_page() {
		// Set position with odd number to avoid confict with other plugin/theme.
		add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', BUDDYBOSS_REORDER_TABS_PLUGIN_URL . 'assets/images/logo.svg', 64.99 );

		// To remove empty parent menu item.
		add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
		remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
	}

	add_action( 'admin_menu', 'register_buddyboss_menu_page' );
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 2);

?>