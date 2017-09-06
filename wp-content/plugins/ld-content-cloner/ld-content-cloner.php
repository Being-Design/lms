<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wisdmlabs.com
 * @since             1.0.0
 * @package           Ld_Content_Cloner
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash Content Cloner
 * Plugin URI:        https://wisdmlabs.com
 * Description:       This plugin clones LearnDash course content - the course along with the associated lessons and topics - for easy content creation.
 * Version:           1.0.2
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ld-content-cloner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

add_action('admin_init', 'wdm_migration_activation_dependency_check');
function wdm_migration_activation_dependency_check()
{

    // check if woocommerce active
    $is_ld_active = is_plugin_active('sfwd-lms/sfwd_lms.php');

    // check dependency activation
    if (!$is_ld_active) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET['activate']);
        add_action('admin_notices', 'wdm_migration_activation_dependency_check_notices');
    }
}

add_action('plugins_loaded','loadPlugin');
function loadPlugin()
{
    run_ld_content_cloner();
}
function wdm_migration_activation_dependency_check_notices()
{
    echo "<div class='error'>
			<p>LearnDash LMS plugin is not active. In order to make <strong>LearnDash Content Cloner</strong> plugin work, you need to install and activate LearnDash LMS first.</p>
		</div>";
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ld-content-cloner-activator.php
 */
function activate_ld_content_cloner()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner-activator.php';
    \LdContentClonerActivator\LdContentClonerActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ld-content-cloner-deactivator.php
 */
function deactivate_ld_content_cloner()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner-deactivator.php';
    LdContentClonerDeactivator\LdContentClonerDeactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ld_content_cloner');
register_deactivation_hook(__FILE__, 'deactivate_ld_content_cloner');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ld_content_cloner()
{
    $plugin = new LdContentCloner\LdContentCloner();
    $plugin->run();
}
//run_ld_content_cloner();
