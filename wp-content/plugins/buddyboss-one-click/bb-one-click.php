<?php
/**
 * Plugin Name: BuddyBoss One Click Installer
 * Plugin URI:  http://buddyboss.com/
 * Description: Quickly Setup BuddyBoss Demos on your server using One Click Installer.
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.0.5
 */

// Exit if accessed directly
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if (!defined( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_VERSION' ) ) {
  define( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_VERSION', '1.0.5' );
}

// Database version
if (!defined( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DB_VERSION' ) ) {
  define( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DB_VERSION', 1 );
}

// Directory
if (!defined( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DIR' ) ) {
  define( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if (!defined( 'BUDDYBOSS_ONECLICK_PLUGIN_BASENAME' ) ) {
  define( 'BUDDYBOSS_ONECLICK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

// Url
if (!defined( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_URL' ) ) {
  $plugin_url = plugin_dir_url( __FILE__ );

  // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
  if ( is_ssl() )
    $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

  define( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_URL', $plugin_url );
}

// File
if (!defined( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_FILE' ) ) {
  define( 'BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_FILE', __FILE__ );
}

global $bb_importor;

require_once(dirname(__FILE__)."/includes/admin-ui.php");

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

    function register_buddyboss_menu_page() {
        // Set position with odd number to avoid confict with other plugin/theme.
        add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_URL . 'assets/images/logo.svg', 64.99 );

        // To remove empty parent menu item.
        add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
        remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
    }

    add_action( 'admin_menu', 'register_buddyboss_menu_page' );
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once(dirname(__FILE__)."/includes/buddyboss-plugin-updater.php");
new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 326);

/* Oneclick Admin UI */

/**
 * 
 * @global \buddyboss_oneclick_installer $buddyboss_oneclick_installer
 * @return \buddyboss_oneclick_installer
 */
function buddyboss_oneclick_installer(){
    global $buddyboss_oneclick_installer;
    if( empty( $buddyboss_oneclick_installer ) ){
        $buddyboss_oneclick_installer = new buddyboss_oneclick_installer();
    }
    
    return $buddyboss_oneclick_installer;
}

buddyboss_oneclick_installer();