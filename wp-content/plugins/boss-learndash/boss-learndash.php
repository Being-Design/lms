<?php
/**
 * Plugin Name: Boss for LearnDash
 * Plugin URI:  https://www.buddyboss.com/product/social-learner-learndash/
 * Description: Makes LearnDash look beautiful with Boss theme
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.2.5
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
if (!defined( 'BOSS_LEARNDASH_PLUGIN_VERSION' ) ) {
  define( 'BOSS_LEARNDASH_PLUGIN_VERSION', '1.2.5' );
}

// Database version
if (!defined( 'BOSS_LEARNDASH_PLUGIN_DB_VERSION' ) ) {
  define( 'BOSS_LEARNDASH_PLUGIN_DB_VERSION', 1 );
}

// Directory
if (!defined( 'BOSS_LEARNDASH_PLUGIN_DIR' ) ) {
  define( 'BOSS_LEARNDASH_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if (!defined( 'BOSS_LEARNDASH_PLUGIN_URL' ) ) {
  $plugin_url = plugin_dir_url( __FILE__ );

  // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
  if ( is_ssl() )
    $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

  define( 'BOSS_LEARNDASH_PLUGIN_URL', $plugin_url );
}

// File
if (!defined( 'BOSS_LEARNDASH_PLUGIN_FILE' ) ) {
  define( 'BOSS_LEARNDASH_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Check whether
 * it meets all requirements
 * @return void
 */
function buddypress_creative_portfolio_pro_requirements()
{

    global $Plugin_Requirements_Check;

    $requirements_Check_include  = BOSS_LEARNDASH_PLUGIN_DIR  . 'includes/requirements-class.php';

    try
    {
        if ( file_exists( $requirements_Check_include ) )
        {
            require( $requirements_Check_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load BPCP_Plugin_Check class at:<br/>%s", 'boss-learndash' ), $requirements_Check_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'boss-learndash' ), $e->getMessage() );
        echo $msg;
    }

    $Plugin_Requirements_Check = new Plugin_Requirements_Check();
    $Plugin_Requirements_Check->activation_check();

}
register_activation_hook( __FILE__, 'buddypress_creative_portfolio_pro_requirements' );

/**
 * Main
 *
 * @return void
 */
function boss_learndash_init()
{
  global $bp, $boss_learndash;

    //Check LearnDash plugin and Boss theme are installed and active
    if (  ! class_exists( 'SFWD_LMS' ) ) {
        add_action( 'admin_notices', 'boss_learndash_install_plugin_theme_notice' );
        return;
    }

    if ( version_compare( LEARNDASH_VERSION , '2.1.8', '<' ) ) {
        add_action( 'admin_notices', 'boss_learndash_upgrade_notice' );
        return;
    }

  $main_include  = BOSS_LEARNDASH_PLUGIN_DIR  . 'includes/main-class.php';

  try
  {
    if ( file_exists( $main_include ) )
    {
      require( $main_include );
      require_once BOSS_LEARNDASH_PLUGIN_DIR  . 'includes/boss-learndash-functions.php';
    }
    else{
      $msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'boss-learndash' ), $main_include );
      throw new Exception( $msg, 404 );
    }
  }
  catch( Exception $e )
  {
    $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'boss-learndash' ), $e->getMessage() );
    echo $msg;
  }

  $boss_learndash = Boss_Learndash_Plugin::instance();

}
add_action( 'plugins_loaded', 'boss_learndash_init' );

/**
 * Must be called after hook 'plugins_loaded'
 * @return Boss for LearnDash Plugin main controller object
 */
function boss_education()
{
  global $boss_learndash;

  return $boss_learndash;
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
//new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 40);


/**
 * Show the admin notice to install/activate BuddyPress first
 */
function boss_learndash_install_plugin_theme_notice() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Boss for LearnDash</strong> requires the LearnDash plugin to work. Please <a href="http://www.learndash.com/">install LearnDash</a> first.', 'boss-learndash');
    echo '</p></div>';
}

/**
 * Show the admin notice to update learndash first
 */
function boss_learndash_upgrade_notice() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Boss for LearnDash</strong> requires LearnDash v2.1.8 or higher to work. Please <a href="http://www.learndash.com/">update LearnDash</a> first.', 'boss-learndash');
    echo '</p></div>';
}
