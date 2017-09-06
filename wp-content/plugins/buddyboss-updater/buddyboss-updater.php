<?php
/**
 * Plugin Name: BuddyBoss Products Updater
 * Plugin URI:  http://buddyboss.com/product/buddyboss-wall/
 * Description: Manages license and update process for BuddyBoss products.
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.0.2
 * Network:     true
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* ++++++++++++++++++++++++++++++
 * CONSTANTS
 +++++++++++++++++++++++++++++ */
// Directory
if ( ! defined( 'BUDDYBOSS_UPDATER_DIR' ) ) {
	define( 'BUDDYBOSS_UPDATER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if ( ! defined( 'BUDDYBOSS_UPDATER_URL' ) ) {
	$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );

	// If we're using https, update the protocol.
	if ( is_ssl() )
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

	define( 'BUDDYBOSS_UPDATER_URL', $plugin_url );
}

/* ______________________________ */

function buddyboss_updater_init() {
	require( BUDDYBOSS_UPDATER_DIR . 'includes/main-class.php' );
	require( BUDDYBOSS_UPDATER_DIR . 'includes/functions.php' );
	require( BUDDYBOSS_UPDATER_DIR . 'includes/vendor/license.php' );
	require( BUDDYBOSS_UPDATER_DIR . 'includes/vendor/updater.php' );
    BuddyBoss_Updater__Plugin::get_instance();//instantiate
}
add_action( 'plugins_loaded', 'buddyboss_updater_init' );

function buddyboss_updater_plugin(){
	return BuddyBoss_Updater__Plugin::get_instance();
}

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

	function register_buddyboss_menu_page() {
		// Set position with odd number to avoid confict with other plugin/theme.
		add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', BUDDYBOSS_UPDATER_URL . 'assets/images/logo.svg', 61.000129 );

		// To remove empty parent menu item.
		add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
		remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
	}

	add_action( 'admin_menu', 'register_buddyboss_menu_page' );
	add_action( 'network_admin_menu', 'register_buddyboss_menu_page' );
}

//Setting up a cron job, to run in background for performing tasks like notifying admin of license expiry etc.
add_filter( 'cron_schedules', 'buddyboss_updater_cron_schedules' );
function buddyboss_updater_cron_schedules( $schedules ){
    if( !isset( $schedules["four_hours"] ) ){
        $schedules["four_hours"] = array(
            'interval' => 4*60*60,
            'display' => __('Once every 4 hours')
        );
    }
    
    return $schedules;
}

register_activation_hook( __FILE__, 'buddyboss_updater_create_schedule_4hours' );
function buddyboss_updater_create_schedule_4hours(){
    $timestamp = wp_next_scheduled( 'buddyboss_updater_schedule_4hours' );

    if( $timestamp == false ){
        wp_schedule_event( time(), 'four_hours', 'buddyboss_updater_schedule_4hours' );
    }
}

register_deactivation_hook( __FILE__, 'buddyboss_updater_remove_schedule_4hours' );
function buddyboss_updater_remove_schedule_4hours(){
    wp_clear_scheduled_hook( 'buddyboss_updater_schedule_4hours' );
}

add_filter( 'bboss_updatable_products', 'bbupdater_register_self_update' );
function bbupdater_register_self_update( $products ){
    //key should be exactly the same as product key above
    $products['BBOSS_UPDATER'] = array(
        //'path'  => plugin_basename(__FILE__),
        //@todo: this needs to be updated for all products
        'path'      => 'buddyboss-updater/buddyboss-updater.php',
        'id'        => 590,
        'software_ids'  => array(),
    );
    return $products;
}