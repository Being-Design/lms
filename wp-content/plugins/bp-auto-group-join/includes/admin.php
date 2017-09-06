<?php
/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BP_Auto_Group_Join_Admin_Page' ) ):

	/**
	 *
	 * BP_Auto_Group_Join_Admin_Page
	 * ********************
	 *
	 *
	 */
	class BP_Auto_Group_Join_Admin_Page {
		/* Options/Load
		 * ===================================================================
		 */

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public $options = array();
		private $plugin_settings_tabs = array();

		/**
		 * Empty constructor function to ensure a single instance
		 */
		public function __construct() {
			// ... leave empty, see Singleton below
		}

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Admin singleton
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @param  array  $options [description]
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::setup() Init admin class
		 *
		 * @return object Admin class
		 */
		public static function instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new BP_Auto_Group_Join_Admin_Page;
				$instance->setup();
			}

			return $instance;
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Get option
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @param  string $key Option key
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::option() Get option
		 *
		 * @return mixed      Option value
		 */
		public function option( $key ) {
			$value = bp_auto_group_join()->option( $key );
			return $value;
		}

		/* Actions/Init
		 * ===================================================================
		 */

		/**
		 * Setup admin class
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses bp_auto_group_join() Get options from main BP_Auto_Group_Join_Admin_Page class
		 * @uses is_admin() Ensures we're in the admin area
		 * @uses curent_user_can() Checks for permissions
		 * @uses add_action() Add hooks
		 */
		public function setup() {
			if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$actions = array(
				'admin_init',
				'admin_menu',
				'network_admin_menu'
			);

			foreach ( $actions as $action ) {
				add_action( $action, array( $this, $action ) );
			}
			add_action( 'admin_init', array( $this, 'register_support_settings' ) );

            // add setting link
			$buddyboss = BP_Auto_Group_Join_Plugin::instance();
			$plugin = $buddyboss->basename;

			add_filter( "plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ) );
		}

		/**
		 * Register admin settings
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses register_setting() Register plugin options
		 * @uses add_settings_section() Add settings page option sections
		 * @uses add_settings_field() Add settings page option
		 */
		public function admin_init() {
            $is_network_activated = bp_auto_group_join()->is_network_activated();
            if( $is_network_activated ){
                add_action('network_admin_edit_bp_auto_group_join', array($this, 'save_network_settings_page'), 10, 0);
            }

            $this->plugin_settings_tabs[ 'bp_auto_group_join_plugin_options' ] = 'General';

			register_setting( 'bp_auto_group_join_plugin_options', 'bp_auto_group_join_plugin_options' );
			add_settings_section( 'general_section', __( 'General Settings', 'bp-auto-group-join' ), array( $this, 'section_general' ), __FILE__ );

			add_settings_field( 'ajg_bmt_info', 'Auto-Join Users to Groups', array( $this, 'ajg_bmt_info_option' ), __FILE__, 'general_section' );
			add_settings_field( 'ajg_bmt_support', 'BuddyPress Member Types', array( $this, 'ajg_bmt_support_option' ), __FILE__, 'general_section' );
		}

		function register_support_settings() {
			$this->plugin_settings_tabs[ 'bp_auto_group_join_support_options' ] = 'Support';

			register_setting( 'bp_auto_group_join_support_options', 'bp_auto_group_join_support_options' );
			add_settings_section( 'section_support', ' ', array( &$this, 'section_support_desc' ), 'bp_auto_group_join_support_options' );
		}

		function section_support_desc() {
			if ( file_exists( dirname( __FILE__ ) . '/help-support.php' ) ) {
				require_once( dirname( __FILE__ ) . '/help-support.php' );
			}
		}

        function save_network_settings_page(){
            if( isset($_POST['bp_auto_group_join_plugin_options']) && !empty($_POST['bp_auto_group_join_plugin_options']) ){
                $prepare_data = $_POST['bp_auto_group_join_plugin_options'];
                $prepare_data = serialize($prepare_data);
                global $wpdb;
                $table_name = $wpdb->base_prefix.'options';
                $row_exists = $wpdb->get_results("SELECT * from {$table_name} WHERE option_name = 'bp_auto_group_join_plugin_options' ");
                if( isset($row_exists[0]) && !empty($row_exists[0]) ){
                    $wpdb->update(
                        $table_name,
                        array(
                            'option_value' => $prepare_data
                        ),
                        array( 'option_name' => 'bp_auto_group_join_plugin_options' ),
                        array(
                            '%s'
                        ),
                        array( '%s' )
                    );
                }else{
                    $wpdb->insert(
                        $table_name,
                        array(
                            'option_name' => 'bp_auto_group_join_plugin_options',
                            'option_value' => $prepare_data
                        ),
                        array(
                            '%s',
                            '%s'
                        )
                    );
                }

                $plugin_page = network_admin_url( "admin.php?page=bp-auto-group-join/includes/admin.php" );
                wp_redirect($plugin_page);
                exit();
            }
        }

		/**
		 * Add plugin settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses add_options_page() Add plugin settings page
		 */
		public function admin_menu() {
			add_submenu_page( 'buddyboss-settings', 'BP Auto Group Join', 'Auto Group Join', 'manage_options', __FILE__, array( $this, 'options_page' ) );
		}

		/**
		 * Add plugin settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::admin_menu() Add settings page option sections
		 */
		public function network_admin_menu() {
			return $this->admin_menu();
		}

		// Add settings link on plugin page
		function plugin_settings_link( $links ) {
            $links[] = '<a href="' . admin_url( "admin.php?page=" . __FILE__ ) . '">'.__("Settings","bp-auto-group-join").'</a>';
            return $links;
		}

		/* Settings Page + Sections
		 * ===================================================================
		 */

		/**
		 * Render settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses do_settings_sections() Render settings sections
		 * @uses settings_fields() Render settings fields
		 * @uses esc_attr_e() Escape and localize text
		 */
		public function options_page() {
			$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : __FILE__;

			if ( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] == 'true' ) {
				?>
				<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
					<p><strong>Settings saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div><?php
			}

            $is_network_activated = bp_auto_group_join()->is_network_activated();
            $submit_url = $is_network_activated ? 'edit.php?action=bp_auto_group_join' : 'options.php';
            ?>
			<div class="wrap">
				<h2><?php _e( "BP Auto Group Join", "bp-auto-group-join" ); ?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form action="<?php echo $submit_url;?>" method="post" class="bb-inbox-settings-form">
					<?php
					if ( 'bp_auto_group_join_plugin_options' == $tab || empty( $_GET[ 'tab' ] ) ) {
						settings_fields( 'bp_auto_group_join_plugin_options' );
						do_settings_sections( __FILE__ );
						?>
						<p class="submit">
							<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( __( "Save Changes", "bp-auto-group-join" ) ); ?>" />
						</p><?php
					} else {
						settings_fields( $tab );
						do_settings_sections( $tab );
					}
					?>

				</form>
			</div>

			<?php
		}

		function plugin_options_tabs() {
			$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bp_auto_group_join_plugin_options';

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . __FILE__ . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		public function ajg_bmt_info_option() {
            $is_network_activated = bp_auto_group_join()->is_network_activated();
            $get_admin_url = $is_network_activated ? network_admin_url() : get_admin_url();
			echo '<p class="description">' . __( 'To automatically join users to a specific group, go to the <a href="'.$get_admin_url.'admin.php?page=bp-groups">Groups edit page</a> and select the group to edit.', 'bp-auto-group-join' ) . '</p>';
		}
		
		public function ajg_bmt_support_option() {
			
			$profile_field_visibility = $this->option( 'ajg_bmt_support' );
			if ( ! $profile_field_visibility ) {
				$profile_field_visibility = 'off';
			}

			$options = array(
				'on' => __( 'On', 'bp-auto-group-join' ),
				'off' => __( 'Off', 'bp-auto-group-join' )
			);
			foreach ( $options as $option => $label ) {
				$checked = $profile_field_visibility == $option ? ' checked' : '';
				echo '<label><input type="radio" name="bp_auto_group_join_plugin_options[ajg_bmt_support]" value="' . $option . '" ' . $checked . '>' . $label . '</label>&nbsp;&nbsp;';
			}

			echo '<p class="description">' . __( 'Add support for joining users to groups based on their member type. Requires <a target="_blank" href="https://www.buddyboss.com/product/buddypress-member-types/">BuddyPress Member Types</a>.', 'bp-auto-group-join' ) . '</p>';
		}

		/**
		 * General settings section
		 *
		 * @since BuddyBoss Wall (1.0.0)
		 */
		public function section_general() {
			
		}

	}

	

// End class BP_Auto_Group_Join_Admin_Page
endif;