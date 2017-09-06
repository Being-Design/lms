<?php

/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BP_Auto_Group_Join_Plugin' ) ):

	/**
	 *
	 * BP Auto Group Join Main Plugin Controller
	 * *************************************
	 *
	 *
	 */
	class BP_Auto_Group_Join_Plugin {
		/* Includes
		 * ===================================================================
		 */

		/**
		 * Most WordPress/BuddyPress plugin have the includes in the function
		 * method that loads them, we like to keep them up here for easier
		 * access.
		 * @var array
		 */
		private $main_includes = array(
			'bp-auto-group-join-base-class',
            'bp-auto-group-join-admin-class',
            'bp-auto-group-join-functions',
		);

		/**
		 * Admin includes
		 * @var array
		 */
		private $admin_includes = array(
            'admin',
		);

		/* Plugin Options
		 * ===================================================================
		 */

		/**
		 * Default options for the plugin, the strings are
		 * run through localization functions during instantiation,
		 * and after the user saves options the first time they
		 * are loaded from the DB.
		 *
		 * @var array
		 */
		private $default_options = array(
			'enabled' => true,
		);

		/**
		 * This options array is setup during class instantiation, holds
		 * default and saved options for the plugin.
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Whether the plugin is activated network wide.
		 * 
		 * @var boolean 
		 */
		public $network_activated = false;

		/**
		 * Is BuddyPress installed and activated?
		 * @var boolean
		 */
		public $bp_enabled = false;

		/* Version
		 * ===================================================================
		 */

		/**
		 * Plugin codebase version
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * Plugin database version
		 * @var string
		 */
		public $db_version = '0.0.0';

		/* Paths
		 * ===================================================================
		 */
		public $file = '';
		public $basename = '';
		public $plugin_dir = '';
		public $plugin_url = '';
		public $includes_dir = '';
		public $includes_url = '';
		public $lang_dir = '';
		public $assets_dir = '';
		public $assets_url = '';

		/* Component State
		 * ===================================================================
		 */
		public $current_type = '';
		public $current_item = '';
		public $current_action = '';
		public $is_single_item = false;

		/* Magic
		 * ===================================================================
		 */

		/**
		 * BP Auto Group Join uses many variables, most of which can be filtered to
		 * customize the way that it works. To prevent unauthorized access,
		 * these variables are stored in a private array that is magically
		 * updated using PHP 5.2+ methods. This is to prevent third party
		 * plugins from tampering with essential information indirectly, which
		 * would cause issues later.
		 *
		 * @see BP_Auto_Group_Join_Plugin::setup_globals()
		 * @var array
		 */
		private $data;

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Main BP Auto Group Join Instance.
		 *
		 * BP Auto Group Join is great
		 * Please load it only one time
		 * For this, we thank you
		 *
		 * Insures that only one instance of BP Auto Group Join exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @static object $instance
		 * @uses BP_Auto_Group_Join_Plugin::setup_globals() Setup the globals needed.
		 * @uses BP_Auto_Group_Join_Plugin::setup_actions() Setup the hooks and actions.
		 * @uses BP_Auto_Group_Join_Plugin::setup_textdomain() Setup the plugin's language file.
		 * @see bp_auto_group_join()
		 *
		 * @return BP Auto Group Join The one true BuddyBoss.
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication
			static $instance = null;

			// Only run these methods if they haven't been run previously
			if ( null === $instance ) {
				$instance = new BP_Auto_Group_Join_Plugin();
				$instance->setup_globals();
				$instance->setup_actions();
				$instance->setup_textdomain();
			}

			// Always return the instance
			return $instance;
		}

		/* Magic Methods
		 * ===================================================================
		 */

		/**
		 * A dummy constructor to prevent BP Auto Group Join from being loaded more than once.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 * @see BP_Auto_Group_Join_Plugin::instance()
		 * @see buddypress()
		 */
		private function __construct() { /* nothing here */
		}

		/**
		 * A dummy magic method to prevent BP Auto Group Join from being cloned.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-auto-group-join' ), '1.0.0' );
		}

		/**
		 * A dummy magic method to prevent BP Auto Group Join from being unserialized.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-auto-group-join' ), '1.0.0' );
		}

		/**
		 * Magic method for checking the existence of a certain custom field.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __isset( $key ) {
			return isset( $this->data[ $key ] );
		}

		/**
		 * Magic method for getting BP Auto Group Join varibles.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __get( $key ) {
			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}

		/**
		 * Magic method for setting BP Auto Group Join varibles.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __set( $key, $value ) {
			$this->data[ $key ] = $value;
		}

		/**
		 * Magic method for unsetting BP Auto Group Join variables.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __unset( $key ) {
			if ( isset( $this->data[ $key ] ) )
				unset( $this->data[ $key ] );
		}

		/**
		 * Magic method to prevent notices and errors from invalid method calls.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 */
		public function __call( $name = '', $args = array() ) {
			unset( $name, $args );
			return null;
		}

		/* Plugin Specific, Setup Globals, Actions, Includes
		 * ===================================================================
		 */

		/**
		 * Setup BP Auto Group Join plugin global variables.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 * @access private
		 *
		 * @uses plugin_dir_path() To generate BP Auto Group Join plugin path.
		 * @uses plugin_dir_url() To generate BP Auto Group Join plugin url.
		 * @uses apply_filters() Calls various filters.
		 */
		private function setup_globals( $args = array() ) {
			$this->network_activated = $this->is_network_activated();

			global $BP_AUTO_GROUP_JOIN, $wpdb;

			//$saved_options = $this->network_activated ? get_site_option( 'bp_auto_group_join_plugin_options' ) : get_option( 'bp_auto_group_join_plugin_options' );
            if($this->network_activated){
                $base_option_table = $wpdb->base_prefix.'options';
                $saved_options = $wpdb->get_col("SELECT option_value from {$base_option_table} WHERE option_name='bp_auto_group_join_plugin_options' ");
                if( isset($saved_options[0]) && !empty($saved_options[0]) ){
                    $saved_options = unserialize($saved_options[0]);
                }
            }else{
                $saved_options = get_option( 'bp_auto_group_join_plugin_options' );
            }
			$saved_options = maybe_unserialize( $saved_options );

			$this->options = wp_parse_args( $saved_options, $this->default_options );

			// Normalize legacy uppercase keys
			foreach ( $this->options as $key => $option ) {
				// Delete old entry
				unset( $this->options[ $key ] );

				// Override w/ lowercase key
				$this->options[ strtolower( $key ) ] = $option;
			}

			/** Versions ************************************************* */
			$this->version = BP_AUTO_GROUP_JOIN_PLUGIN_VERSION;
			$this->db_version = BP_AUTO_GROUP_JOIN_PLUGIN_DB_VERSION;

			/** Paths ***************************************************** */
			// BP Auto Group Join root directory
			$this->file = BP_AUTO_GROUP_JOIN_PLUGIN_FILE;
			$this->basename = plugin_basename( $this->file );
			$this->plugin_dir = BP_AUTO_GROUP_JOIN_PLUGIN_DIR;
			$this->plugin_url = BP_AUTO_GROUP_JOIN_PLUGIN_URL;

			// Languages
			$this->lang_dir = dirname( $this->basename ) . '/languages/';

			// Includes
			$this->includes_dir = $this->plugin_dir . 'includes';
			$this->includes_url = $this->plugin_url . 'includes';

			// Templates
			$this->templates_dir = $this->plugin_dir . 'templates';
			$this->templates_url = $this->plugin_url . 'templates';

			// Assets
			$this->assets_dir = $this->plugin_dir . 'assets';
			$this->assets_url = $this->plugin_url . 'assets';
		}

		/**
		 * Check if the plugin is activated network wide(in multisite)
		 * 
		 * @since 1.0.0
		 * @access private
		 * 
		 * @return boolean
		 */
		public function is_network_activated() {
			$network_activated = false;
			if ( is_multisite() ) {
				if ( ! function_exists( 'is_plugin_active_for_network' ) )
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

				if ( is_plugin_active_for_network( basename( constant( 'BP_AUTO_GROUP_JOIN_PLUGIN_DIR' ) ) . '/bp-auto-group-join.php' ) ) {
					$network_activated = true;
				}
			}
			return $network_activated;
		}

		/**
		 * Setup BP Auto Group Join main actions
		 *
		 * @since  BP Auto Group Join 1.0
		 */
		private function setup_actions() {
			// Admin
			add_action( 'init', array( $this, 'setup_admin_settings' ) );

			if ( ! $this->is_enabled() )
				return;

			// Hook into BuddyPress init
			add_action( 'bp_init', array( $this, 'bp_loaded' ) );
		}

		public function setup_admin_settings() {
			if ( ( is_admin() || is_network_admin() ) && current_user_can( 'manage_options' ) ) {
				$this->load_admin();
			}
		}

		/**
		 * Load plugin text domain
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses sprintf() Format .mo file
		 * @uses get_locale() Get language
		 * @uses file_exists() Check for language file(filename)
		 * @uses load_textdomain() Load language file
		 */
		public function setup_textdomain() {
			$domain = 'bp-auto-group-join';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			//first try to load from wp-content/languages/plugins/ directory
			load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );

			//if not found, then load from bp-auto-group-join/languages/ directory
			load_plugin_textdomain( 'bp-auto-group-join', false, $this->lang_dir );
		}

		/**
		 * We require BuddyPress to run the main components, so we attach
		 * to the 'bp_loaded' action which BuddyPress calls after it's started
		 * up. This ensures any BuddyPress related code is only loaded
		 * when BuddyPress is active.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 * @access public
		 *
		 * @return void
		 */
		public function bp_loaded() {
			global $bp;

			$this->bp_enabled = true;
			$this->load_main();
		}

		/* Load
		 * ===================================================================
		 */

		/**
		 * Include required admin files.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 * @access private
		 *
		 * @uses $this->do_includes() Loads array of files in the include folder
		 */
		public function load_admin() {
			$this->do_includes( $this->admin_includes );

			$this->admin = BP_Auto_Group_Join_Admin_Page::instance();
		}

		/**
		 * Include required files.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 * @access private
		 *
		 * @uses BP_Auto_Group_Join_Plugin::do_includes() Loads array of files in the include folder
		 */
		private function load_main() {
			$this->do_includes( $this->main_includes );

			new BP_Auto_Group_Join_Base();
		}

		/* Activate/Deactivation/Uninstall callbacks
		 * ===================================================================
		 */

		/**
		 * Fires when plugin is activated
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function activate() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';

			check_admin_referer( "activate-plugin_{$plugin}" );
		}

		/**
		 * Fires when plugin is de-activated
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function deactivate() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';

			check_admin_referer( "deactivate-plugin_{$plugin}" );
		}

		/**
		 * Fires when plugin is uninstalled
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function uninstall() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			check_admin_referer( 'bulk-plugins' );

			// Important: Check if the file is the one
			// that was registered during the uninstall hook.
			if ( $this->file != WP_UNINSTALL_PLUGIN ) {
				return;
			}
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Include required array of files in the includes directory
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses require_once() Loads include file
		 */
		public function do_includes( $includes = array() ) {
			foreach ( ( array ) $includes as $include ) {
				require_once( $this->includes_dir . '/' . $include . '.php' );
			}
		}

		/**
		 * Check if the plugin is active and enabled in the plugin's admin options.
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses BuddyBoss_Media_Plugin::option() Get plugin option
		 *
		 * @return boolean True when the plugin is active
		 */
		public function is_enabled() {
			$is_enabled = $this->option( 'enabled' ) === true || $this->option( 'enabled' ) === 'on';

			return $is_enabled;
		}

		/**
		 * Convenience function to access plugin options, returns false by default
		 *
		 * @since  BP Auto Group Join (1.0.0)
		 *
		 * @param  string $key Option key

		 * @uses apply_filters() Filters option values with 'bp_auto_group_join_option' &
		 *                       'bp_auto_group_join_option_{$option_name}'
		 * @uses sprintf() Sanitizes option specific filter
		 *
		 * @return mixed Option value (false if none/default)
		 *
		 */
		public function option( $key ) {
			$key = strtolower( $key );
			$option = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;

			// Apply filters on options as they're called for maximum
			// flexibility. Options are are also run through a filter on
			// class instatiation/load.
			// ------------------------
			// This filter is run for every option
			$option = apply_filters( 'bp_auto_group_join_option', $option );

			// Option specific filter name is converted to lowercase
			$filter_name = sprintf( 'bp_auto_group_join_option_%s', strtolower( $key ) );
			$option = apply_filters( $filter_name, $option );

			return $option;
		}

	} // End class BP_Auto_Group_Join_Plugin

endif;