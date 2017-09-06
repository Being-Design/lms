<?php
/**
 * @package WordPress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'BuddyPress_Reorder_Tabs_Admin' ) ):
	
/**
 *
 * BuddyPress Reorder Tabs Admin
 * ********************
 *
 *
 */
class BuddyPress_Reorder_Tabs_Admin{
	/**
	 * Settings page's hook_suffix, generate by add_options_page
	 * 
	 * @var string
	 */
	private $my_screen = '';
	
	/**
	 * Plugins slug, used in setting screen's url etc.
	 * @var string 
	 */
	private $network_activated = false,
			$plugin_slug = 'bp-reorder-tabs',
			$menu_hook = 'admin_menu',
			$settings_page = 'buddyboss-settings',
            $capability = 'manage_options';
	
	/**
	 * Plugin options
	 *
	 * @var array
	 */
	public $options = array();
	
	/**
	 * Empty constructor function to ensure a single instance
	 */
	public function __construct(){
		// ... leave empty, see Singleton below
	}


	/* Singleton
	 * ===================================================================
	 */

	/**
	 * Admin singleton
	 *
	 * @param  array  $options [description]
	 *
	 * @return object Admin class
	 */
	public static function instance(){
		static $instance = null;

		if ( null === $instance )
		{
			$instance = new BuddyPress_Reorder_Tabs_Admin();
			$instance->setup();
		}

		return $instance;
	}
	
	/**
	 * Get option
	 *
	 * @param  string $key Option key
	 *
	 * @return mixed      Option value
	 */
	public function option( $key ){
		$value = buddypress_redorder_tabs()->option( $key );
		return $value;
	}
	
	/**
	 * Setup admin class
	 */
	public function setup(){
		if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ){
			return;
		}

		$actions = array(
			'admin_menu',
			'network_admin_menu',
			'admin_enqueue_scripts',
			'wp_ajax_bp_r_t_a_profile',
            'wp_ajax_bp_r_t_a_profile_setting_html',
			'wp_ajax_bp_r_t_a_groups',
			'wp_ajax_bp_r_t_a_groups_setting_html',
		);

		foreach( $actions as $action ){
			add_action( $action, array( $this, $action ) );
		}
	}
	
	/**
	 * Add plugin settings page
	 */
	public function admin_menu(){
        $this->my_screen = add_submenu_page(
            $this->settings_page, __('BuddyPress Reorder Tabs', 'TEXTDOMAIN'), __('Reorder Tabs', 'TEXTDOMAIN'), $this->capability, $this->plugin_slug, array( $this, 'options_page' )
        );
	}
	
	/**
	 * Add plugin settings page
	 */
	public function network_admin_menu(){
		return $this->admin_menu();
	}
	
	/**
	* Register admin scripts
	*
	* @since BuddyPress Reorder Tabs (1.0.0)
	*
	* @uses wp_enqueue_script() Enqueue admin script
	* @uses wp_enqueue_style() Enqueue admin style
	* @uses buddyboss_dashboards()->assets_url Get plugin URL
	*/
	public function admin_enqueue_scripts( $hook ) {
	   if( $hook==$this->my_screen ){
		   //wp_enqueue_script('admin-widgets');

		   wp_enqueue_script( 'jquery-form' );
		   wp_enqueue_script( 'jquery-ui-draggable' );
		   wp_enqueue_script( 'jquery-ui-droppable' );
		   wp_enqueue_script( 'jquery-ui-sortable' );
		   if ( wp_is_mobile() )
			   wp_enqueue_script( 'jquery-touch-punch' );

		   $js = trailingslashit( BUDDYBOSS_REORDER_TABS_PLUGIN_URL ) . 'includes/admin/assets/js/';
		   $css = trailingslashit( BUDDYBOSS_REORDER_TABS_PLUGIN_URL ) . 'includes/admin/assets/css/';

		   wp_enqueue_style( 'bp-reorder-tabs-admin-main', $css.'bp-reorder-tabs.min.css', array(), BUDDYBOSS_REORDER_TABS_PLUGIN_VERSION );
//		   wp_enqueue_style( 'bp-reorder-tabs-admin-main', $css.'bp-reorder-tabs.css', array(), '1.0.0' );
		   
		   wp_enqueue_script( 'bp-reorder-tabs-admin-main', $js.'bp-reorder-tabs.min.js',	array( 'jquery' ), BUDDYBOSS_REORDER_TABS_PLUGIN_VERSION );
//		   wp_enqueue_script( 'bp-reorder-tabs-admin-main', $js.'bp-reorder-tabs.js',	array( 'jquery' ), '1.0.0' );

		   $script_data = apply_filters( 'bp_reorder_tabs_admin_script_data', array() );
		   wp_localize_script( 'bp-reorder-tabs-admin-main', 'BP_REORDER_TABS_ADMIN_', $script_data );
	   }
	}
	
	/**
	 * Render settings page
	 */
	public function options_page(){
		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			
			<?php $this->update_settings_content(); ?>

			<br />
			
			<div class='bp_reorder_tabs_admin'>
				<h2 class="nav-tab-wrapper">
					<?php $this->print_plugin_tabs();?>
				</h2>
				<div class='tabs-panel'>
					<?php $this->print_screen_content();?>
				</div>
			</div><!-- .bp_reorder_tabs_admin -->
		</div>
		<?php
	}
	
	public function update_settings_content(){
			
	}

	private function get_screens(){
		$screens = array(
			'profile'	=> __( 'Profile Tabs', 'bp-reorder-tabs' ),
		);
		
		if( bp_is_active( 'groups' ) ){
			$screens['groups']	= __( 'Group Tabs', 'bp-reorder-tabs' );
		}
		return $screens;
	}

	public function get_active_screen(){
		return isset( $_GET['screen'] ) ? $_GET['screen'] : 'profile';
	}

	/**
	* Print the tabs in admin screen
	* 
	* @since    1.0.0
	*/
	public function print_screen_tabs() {
        $active_screen = $this->get_active_screen();

        $cntr = 1;
        $total_count = $this->get_screens();
        foreach( $this->get_screens() as $screen => $label ){
            $active = $active_screen == $screen ? 'current' : '';
            echo '<li><a class="' . $active . '" href="?page=' . $this->plugin_slug . '&screen=' . $screen . '">' . $label . '</a>';
           
            if( count( $total_count ) > $cntr ){
                echo " | ";
            }
            $cntr++;
            
            echo '</li>';
        }
	}
    
	public function print_plugin_tabs() {
        $active_screen = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

        $screens = array( 
            'general' => __( 'General', 'TEXTDOMAIN' ),
            'support' => __( 'Support', 'TEXTDOMAIN' ),
        );
        
        $cntr = 0;
        foreach( $screens as $screen => $label ){
            $active = $active_screen == $screen ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_slug . '&tab=' . $screen . '">' . $label . '</a>';
        }
	}
    
	/**
	 * 
	 */
	public function print_screen_content(){
        $active_screen = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        if( 'general'==$active_screen ){
            echo "<ul class='subsubsub'>";
            $this->print_screen_tabs();
            echo "</ul>";

            $active_screen = $this->get_active_screen();
            include_once( BUDDYBOSS_REORDER_TABS_PLUGIN_DIR . 'includes/admin/'. $active_screen .'.php' );
        }
        
        if( 'support'==$active_screen ){
            include_once( BUDDYBOSS_REORDER_TABS_PLUGIN_DIR . 'includes/admin/'. $active_screen .'.php' );
        }
	}
	
	public function wp_ajax_bp_r_t_a_profile(){
		check_ajax_referer( 'bp_r_t_a_profile', 'nonce_bp_r_t_a_screen' );
		if( !current_user_can( 'level_10' ) )
			die( 'Access Denied!' );
		
		$saved_options = get_option( 'b_r_t_plugin_options', array() );
		
		$setting = array();
		$setting['default'] = isset( $_REQUEST['def_profile_page'] ) ? $_REQUEST['def_profile_page'] : '';
		$setting['rd_adminbar_nav'] = isset( $_REQUEST['rd_adminbar_nav'] ) ? $_REQUEST['rd_adminbar_nav'] : 'no';

		$config_data = isset( $_REQUEST['config_data'] ) && !empty( $_REQUEST['config_data'] ) ? json_decode( stripslashes( $_REQUEST['config_data'] ) ) : '';
		$setting['config_data'] = $config_data;
		
		$saved_options['profile'] = $setting;
		update_option( 'b_r_t_plugin_options', $saved_options );
		
		$retval = array(
			'status'	=> true,
			'message'	=> __( 'Settings saved.', 'bp-reorder-tabs' ),
		);
		die( json_encode( $retval ) );
	}
    
    public function wp_ajax_bp_r_t_a_profile_setting_html(){
		check_ajax_referer( 'bp_r_t_a_profile_setting_html', 'nonce' );
		if( !current_user_can( 'level_10' ) )
			die( 'Access Denied!' );
		
		ob_start();
		include_once( BUDDYBOSS_REORDER_TABS_PLUGIN_DIR . 'includes/admin/profile-content.php' );
		$html = ob_get_clean();
		die( $html );
	}
	
	public function wp_ajax_bp_r_t_a_groups(){
		check_ajax_referer( 'bp_r_t_a_groups', 'nonce_bp_r_t_a_screen' );
		if( !current_user_can( 'level_10' ) )
			die( 'Access Denied!' );
		
		$saved_options = get_option( 'b_r_t_plugin_options', array() );
		
		$setting = array();
		$setting['default'] = isset( $_REQUEST['def_profile_page'] ) ? $_REQUEST['def_profile_page'] : '';
		
		$config_data = isset( $_REQUEST['config_data'] ) && !empty( $_REQUEST['config_data'] ) ? json_decode( stripslashes( $_REQUEST['config_data'] ) ) : '';
		$setting['config_data'] = $config_data;
		
		$saved_options['groups'] = $setting;
		update_option( 'b_r_t_plugin_options', $saved_options );
		
		$retval = array(
			'status'	=> true,
			'message'	=> __( 'Settings saved.', 'bp-reorder-tabs' ),
		);
		die( json_encode( $retval ) );
	}
	
	public function wp_ajax_bp_r_t_a_groups_setting_html(){
		check_ajax_referer( 'bp_r_t_a_groups_setting_html', 'nonce' );
		if( !current_user_can( 'level_10' ) )
			die( 'Access Denied!' );
		
		if( !bp_r_t_my_group_url() )
			die( __( 'You must be a member (preferably admin) of at least one group. Please join/create a group and reload this screen again.', 'bp-reorder-tabs' ) );
		
		ob_start();
		include_once( BUDDYBOSS_REORDER_TABS_PLUGIN_DIR . 'includes/admin/groups-content.php' );
		$html = ob_get_clean();
		die( $html );
	}
	
}

endif;

/**
 * Returns the url of any one group
 *	 - which i am an admin of
 *   - which i am a member of
 * in the given order of preferance.
 * 
 * @todo: give UI to choose which group to check.
 *		case:	group 1 (i am admin) has tabs - home, members, documents, manage
 *				group 2 (i am admin) has tabs - home, members, documents, pages, extras, manage
 *			if this function returns group 1, then admin won't be able to reposition 'pages' and 'extras' tabs.
 * 
 * @param int $user_id DEFAULT current user
 * @return string url of group, false if user is not a member of any group
 */
function bp_r_t_my_group_url( $user_id=false ){
	$group_url = '';
	if( !$user_id )
		$user_id = bp_loggedin_user_id ();
	
	if( !bp_is_active('groups') )
		return false;
	
	if( bp_has_groups( array( 'user_id'=>$user_id, 'show_hidden'=>true ) ) ){
		while ( bp_groups() ){ 
			bp_the_group();
			$group_url = bp_get_group_permalink();
			
			if( bp_group_is_admin() )
				break;//no need to continue;
		}
	}
	
	return apply_filters( 'bp_r_t_my_group_url', $group_url );
}