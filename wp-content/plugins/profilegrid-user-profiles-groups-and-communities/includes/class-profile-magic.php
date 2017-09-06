<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/includes
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
 * @since      1.0.0
 * @package    Profile_Magic
 * @subpackage Profile_Magic/includes
 * @author     Your Name <email@example.com>
 */
class Profile_Magic {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Profile_Magic_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $profile_magic    The string used to uniquely identify this plugin.
	 */
	protected $profile_magic;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->profile_magic = 'profile-grid';
		$this->version = '1.0.0';
		$this->load_dependencies();
		$this->set_locale();
                $this->define_global_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_access_hooks();
                $this->define_notification_hooks();
		$this->define_smtp_connection();
                ob_start();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Profile_Magic_Loader. Orchestrates the hooks of the plugin.
	 * - Profile_Magic_i18n. Defines internationalization functionality.
	 * - Profile_Magic_Admin. Defines all hooks for the admin area.
	 * - Profile_Magic_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
                require_once plugin_dir_path( dirname( __FILE__ )) . 'includes/class-profile-magic-activator.php';
                require_once plugin_dir_path(dirname( __FILE__ ) ) . 'includes/class-profile-magic-deactivator.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-dbhandler.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-request.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-sanitized.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-custom-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-smtp.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-html-generator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-email.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-profile-magic-access-options.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-paypal-integration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-paypal-functions.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-export-import.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-messenger.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-friends-helper.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profile-magic-friends-integration.php';
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-profile-magic-notifications.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-profile-magic-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-profile-magic-public.php';
		
		

		$this->loader = new Profile_Magic_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Profile_Magic_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Profile_Magic_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
        
        private function define_global_hooks(){
            
              $this->loader->add_filter('plugins_loaded', $this, 'pg_on_plugins_loaded');
        }
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_smtp_connection()
	{
                $dbhandler = new PM_DBhandler;
		$plugin_smtp = new Profile_Magic_SMTP( $this->get_profile_magic(), $this->get_version() );
		if($dbhandler->get_global_option_value('pm_enable_smtp',0)==1)
		{
			$this->loader->add_action( 'phpmailer_init', $plugin_smtp, 'pm_smtp_connection' );
		}
			
	}
	
	private function define_admin_hooks() {

		$plugin_admin = new Profile_Magic_Admin( $this->get_profile_magic(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'profile_magic_admin_menu' );
		$this->loader->add_action( 'wp_ajax_pm_set_field_order', $plugin_admin, 'profile_magic_set_field_order' );
		$this->loader->add_action( 'wp_ajax_pm_set_section_order', $plugin_admin, 'profile_magic_set_section_order' );
		$this->loader->add_action( 'wp_ajax_pm_test_smtp', $plugin_admin, 'profile_magic_check_smtp_connection' );
		$this->loader->add_action( 'wp_ajax_nopriv_pm_check_user_exist', $plugin_admin, 'profile_magic_check_user_exist' );
		$this->loader->add_action( 'wp_ajax_pm_check_user_exist', $plugin_admin, 'profile_magic_check_user_exist' );
		$this->loader->add_action( 'wp_ajax_pm_template_preview', $plugin_admin, 'profile_magic_template_preview' );
		$this->loader->add_action( 'wp_ajax_pm_section_dropdown', $plugin_admin, 'profile_magic_section_dropdown' );
		$this->loader->add_action( 'wp_ajax_nopriv_pm_activate_user_by_email', $plugin_admin, 'profile_magic_activate_user_by_email' );
		$this->loader->add_action( 'wp_ajax_pm_activate_user_by_email', $plugin_admin, 'profile_magic_activate_user_by_email' );
                
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'profile_magic_show_user_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'profile_magic_show_user_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'profile_magic_update_user_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'profile_magic_update_user_fields' );
		$this->loader->add_action('admin_footer',$plugin_admin,'profile_magic_show_feedback_form');
                $this->loader->add_action( 'user_new_form', $plugin_admin, 'profile_magic_show_user_fields' );
		$this->loader->add_action( 'user_register', $plugin_admin, 'profile_magic_update_user_fields' );
                $this->loader->add_action('wpmu_new_blog', $plugin_admin, 'activate_sitewide_plugins');
                //paypal integration for dashboard options
                $this->loader->add_action( 'profile_magic_setting_option', $plugin_admin, 'pm_profile_magic_add_option_setting_page' );
		$this->loader->add_action( 'profile_magic_group_option', $plugin_admin, 'pm_profile_magic_add_group_option',10,2);
		$this->loader->add_action( 'wp_ajax_pm_load_export_fields_dropdown', $plugin_admin, 'pm_load_export_fields_dropdown' );
                $this->loader->add_action( 'wp_ajax_pm_upload_csv', $plugin_admin, 'pm_upload_csv' );
                $this->loader->add_filter('upload_mimes',$plugin_admin, 'profile_grid_myme_types');
                $this->loader->add_action('wp_ajax_pg_post_feedback',$plugin_admin,'pg_post_feedback');
                $this->loader->add_action('profilegrid_shortcode_desc',$plugin_admin,'pg_geolocation_short_code');
                $this->loader->add_action('profilegrid_shortcode_desc',$plugin_admin,'pg_frontend_group_short_code');
        }
	
	private function define_access_hooks()
	{
		$plugin_access = new Profile_Magic_access_options( $this->get_profile_magic(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $plugin_access, 'profile_magic_access_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_access, 'profile_magic_save_access_meta' );	
		$this->loader->add_filter('the_content',$plugin_access, 'profile_magic_check_content_access');	
	}
        
        private function define_notification_hooks()
        {
            $plugin_notification = new Profile_Magic_Notification( $this->get_profile_magic(), $this->get_version() );
            $this->loader->add_action('comment_post',$plugin_notification,'pm_add_comment_notification',10,2);
            $this->loader->add_filter('heartbeat_received',$plugin_notification, 'pm_notification_heartbeat_received', 10, 2); //for login user response
            $this->loader->add_action( 'added_post_meta',$plugin_notification, 'pm_blog_post_published', 10, 4 );
            //$this->loader->add_filter('heartbeat_nopriv_received', $plugin_notification, 'pm_notification_heartbeat_received', 10, 2); //for visiters
	
        }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() 
	{
		$plugin_public = new Profile_Magic_Public( $this->get_profile_magic(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'wp_ajax_nopriv_pm_captcha_verification', $plugin_public, 'profile_magic_captcha_verification' );
		$this->loader->add_action('wp_login',$plugin_public, 'profile_magic_check_login_status',10, 2);
                $this->loader->add_action('wp_logout',$plugin_public, 'profile_magic_update_logout_status');
		$this->loader->add_action('login_form_lostpassword',$plugin_public, 'profile_magic_do_password_lost');
		$this->loader->add_action('login_form_lostpassword',$plugin_public, 'profile_magic_lost_password_form');
		$this->loader->add_action('password_reset',$plugin_public, 'profile_magic_send_email_after_password_reset',10,2);
		$this->loader->add_action('login_form_rp',$plugin_public, 'profile_magic_do_password_reset');
		$this->loader->add_action('login_form_resetpass',$plugin_public, 'profile_magic_do_password_reset');
		
		$this->loader->add_action('login_form_rp',$plugin_public, 'profile_magic_redirect_to_password_reset');
		$this->loader->add_action('login_form_resetpass',$plugin_public, 'profile_magic_redirect_to_password_reset');		
                $this->loader->add_filter('login_message',$plugin_public, 'profile_magic_login_notice');
		$this->loader->add_filter('register_url',$plugin_public, 'profile_magic_default_registration_url');
		$this->loader->add_filter('login_redirect',$plugin_public, 'profile_magic_redirect_after_login',10,3);
		$this->loader->add_filter('get_avatar',$plugin_public, 'profile_magic_get_avatar',100000000,6);
		$this->loader->add_filter('retrieve_password_message',$plugin_public, 'profile_magice_retrieve_password_message',10,4);
		$this->loader->add_action( 'wp_ajax_pm_update_profile_image', $plugin_public, 'pm_update_user_profile_image' );
		$this->loader->add_action('wp_ajax_pm_change_frontend_user_pass',$plugin_public,'pm_change_frontend_user_pass');
		$this->loader->add_action('profile_magic_registration_process',$plugin_public, 'pm_submit_user_registration',1,7);
                $this->loader->add_action('profile_magic_show_captcha',$plugin_public, 'profile_magic_recapcha_field',1,1);
                $this->loader->add_action('profile_magic_registration_process',$plugin_public, 'pm_submit_user_registration_paypal',10,7);
		$this->loader->add_action('profile_magic_before_registration_form',$plugin_public,'pm_payment_process',10,4);
                $this->loader->add_action( 'wp_ajax_pm_upload_image', $plugin_public, 'pm_upload_image' );
                $this->loader->add_action( 'wp_ajax_pm_upload_cover_image', $plugin_public, 'pm_upload_cover_image' );
                $this->loader->add_action('wp_ajax_pm_send_change_pass_email',$plugin_public,'pm_send_change_pass_email');
                $this->loader->add_action('wp_ajax_nopriv_pm_send_change_pass_email',$plugin_public,'pm_send_change_pass_email');
                $this->loader->add_action('wp_ajax_pm_advance_user_search',$plugin_public,'pm_advance_user_search');
                $this->loader->add_action('wp_ajax_nopriv_pm_advance_user_search',$plugin_public,'pm_advance_user_search');
                $this->loader->add_action('wp_ajax_pm_advance_search_get_search_fields_by_gid',$plugin_public,'pm_advance_search_get_search_fields_by_gid');
                $this->loader->add_action('wp_ajax_nopriv_pm_advance_search_get_search_fields_by_gid',$plugin_public,'pm_advance_search_get_search_fields_by_gid'); 
                $this->loader->add_action('wp_ajax_pm_messenger_send_new_message',$plugin_public,'pm_messenger_send_new_message');
                $this->loader->add_action('wp_ajax_pm_messenger_show_threads',$plugin_public,'pm_messenger_show_threads');
                $this->loader->add_action('wp_ajax_pm_messenger_show_messages',$plugin_public,'pm_messenger_show_messages');
                $this->loader->add_action('wp_ajax_pm_messenger_show_thread_user',$plugin_public,'pm_messenger_show_thread_user');
                $this->loader->add_action('wp_ajax_pm_get_messenger_notification',$plugin_public,'pm_get_messenger_notification');
                $this->loader->add_action('wp_ajax_pm_autocomplete_user_search',$plugin_public,'pm_autocomplete_user_search');
                $this->loader->add_action('wp_ajax_pm_messenger_delete_threads',$plugin_public,'pm_messenger_delete_threads');
                $this->loader->add_action('wp_ajax_pm_messenger_notification_extra_data',$plugin_public,'pm_messenger_notification_extra_data');
                $this->loader->add_action( 'init', $plugin_public, 'pg_create_post_type' );
                $this->loader->add_action('wp_ajax_pm_load_pg_blogs',$plugin_public,'pm_load_pg_blogs');
                $this->loader->add_action('wp_ajax_nopriv_pm_load_pg_blogs',$plugin_public,'pm_load_pg_blogs');
                $this->loader->add_action('wp_ajax_pm_get_rid_by_uname',$plugin_public,'pm_get_rid_by_uname');
                
                $this->loader->add_action('profile_magic_profile_tab',$plugin_public, 'pm_show_friends_tab',10,2);
                $this->loader->add_action('profile_magic_profile_tab',$plugin_public, 'pm_right_side_options',10,2);
		$this->loader->add_action('profile_magic_profile_tab_content',$plugin_public, 'pm_show_friends_content',10,2);
		$this->loader->add_action('wp_ajax_pm_fetch_my_friends',$plugin_public,'pm_fetch_my_friends');
		$this->loader->add_action('wp_ajax_pm_fetch_my_suggestion',$plugin_public,'pm_fetch_my_suggestion');
		$this->loader->add_action('wp_ajax_pm_add_friend_request',$plugin_public,'pm_add_friend_request');
		$this->loader->add_action('wp_ajax_pm_confirm_friend_request',$plugin_public,'pm_confirm_friend_request');
		$this->loader->add_action('wp_ajax_pm_unfriend_friend',$plugin_public,'pm_unfriend_friend');
		$this->loader->add_action('wp_ajax_pm_block_friend',$plugin_public,'pm_block_friend');
		$this->loader->add_action('wp_ajax_pm_reject_friend_request',$plugin_public,'pm_reject_friend_request');
		$this->loader->add_action('wp_ajax_pm_remove_friend_suggestion',$plugin_public,'pm_remove_friend_suggestion');
		$this->loader->add_action('wp_ajax_pm_get_friends_notification',$plugin_public,'pm_get_friends_notification');
                $this->loader->add_action('wp_ajax_pm_delete_notification',$plugin_public,'pm_delete_notification');
                $this->loader->add_action('wp_ajax_pm_load_more_notification',$plugin_public,'pm_load_more_notification');
                $this->loader->add_action('wp_ajax_pm_read_all_notification',$plugin_public,'pm_read_all_notification');
                $this->loader->add_action('wp_ajax_pm_fetch_friend_list_counter',$plugin_public,'pm_fetch_friend_list_counter');
                $this->loader->add_action('wp_ajax_pm_refresh_notification',$plugin_public,'pm_refresh_notification');
                $this->loader->add_action('profile_magic_custom_fields_html',$plugin_public,'profile_magic_custom_payment_fields',10,1);
                $this->loader->add_filter('profile_magic_check_payment_config',$plugin_public, 'profile_magic_check_paypal_config',10,1);
                
        }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_profile_magic() {
		return $this->profile_magic;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Profile_Magic_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
        
        public function pg_on_plugins_loaded()
        {
            add_option('progrid_db_version',PROGRID_DB_VERSION);
            $existing_pg_db_version = floatval(get_option('progrid_db_version','1.0'));
            if($existing_pg_db_version < PROGRID_DB_VERSION)
            {
                $pm_activator = new Profile_Magic_Activator;
                $pm_activator->upgrade_db();
            }
        }

}
