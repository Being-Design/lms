<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'BuddyPress_Reorder_Tabs_Plugin' ) ):
/**
 *
 * BuddyBoss Reorder Tabs Plugin Main Controller
 * **************************************
 *
 *
 */
class BuddyPress_Reorder_Tabs_Plugin {
	/**
	 * Default options for the plugin, the strings are
	 * run through localization functions during instantiation,
	 * and after the user saves options the first time they
	 * are loaded from the DB.
	 *
	 * @var array
	 */
	private $default_options = array(
		'profile'	=> array(
			'default'		=> '',
			'config_data'	=> '',
		),
		'groups'	=> array(
			'default'		=> '',
			'config_data'	=> '',
		)
	);
	
	/**
	 * This options array is setup during class instantiation, holds
	 * default and saved options for the plugin.
	 *
	 * @var array
	 */
	public $options = array();
	
	/**
	 * Just a random string.
	 * This is appended to a single groups url while querying to save group nav info into db.
	 * The operation is only performed if this key is detected in url.
	 * 
	 * @var string 
	 */
	private $secret = 'yuYmn_erin2356UY';
	
    /**
     * Whether plugin bp group extras has redorderd group navs
     * @since 1.0.8 
     * @var boolean 
     */
    private $flag_bp_group_extras = false;
    
	/**
	 * Main BuddyPress Reorder Tabs Instance.
	 *
	 * Insures that only one instance of this class exists in memory at any
	 * one time. Also prevents needing to define globals all over the place.
	 *
	 * @since BuddyPress Reorder Tabs (1.0.0)
	 *
	 * @static object $instance
	 * @uses BuddyBoss_Edit_Activity::setup_actions() Setup the hooks and actions.
	 * @uses BuddyBoss_Edit_Activity::setup_textdomain() Setup the plugin's language file.
	 * @see buddyboss_edit_activity()
	 *
	 * @return object BuddyBoss_Edit_Activity
	 */
	public static function instance(){
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance )
		{
			$instance = new BuddyPress_Reorder_Tabs_Plugin();
			
            /**
	         * Code now uses buddypress new nagivation api nad hence will not support buddypress versions lower than 
	         * In those cases, show an admin notice and abort
	         */
	        if ( version_compare( BP_VERSION, '2.6', '<' ) ){
	            add_action( 'admin_notices', array( $instance, 'admin_notice_min_bp_version' ) );
	        } else {
				$instance->setup_globals();
				$instance->setup_actions();
				$instance->setup_textdomain();
			}
		}

		// Always return the instance
		return $instance;
	}
	
	/* Magic Methods
	 * ===================================================================
	 */
	private function __construct() { /* Do nothing here */ }

	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-reorder-tabs' ), '1.0.0' ); }

	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-reorder-tabs' ), '1.0.0' ); }

	public function __isset( $key ) { return isset( $this->data[$key] ); }

	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	public function __set( $key, $value ) {
		
		if ( !empty ($this->data[$key]) ) {
			$this->data[$key] = $value;
		}
		
	}

	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }
	
	/**
	 * Setup BuddyPress Reorder Tabs plugin global variables.
	 *
	 * @since BuddyPress Reorder Tabs (1.0.0)
	 * @access private
	 */
	private function setup_globals(){
		// DEFAULT CONFIGURATION OPTIONS
		$default_options = $this->default_options;

		$saved_options = get_option( 'b_r_t_plugin_options' );
		$saved_options = maybe_unserialize( $saved_options );

		$this->options = wp_parse_args( $saved_options, $default_options );
		
		$group_settings = $this->option( 'groups' );
		if( isset( $group_settings['default'] ) && !empty( $group_settings['default'] ) ){
			if( !defined( 'BP_GROUPS_DEFAULT_EXTENSION' ) ){
				define( 'BP_GROUPS_DEFAULT_EXTENSION', $group_settings['default'] );
			}
		}
		
		$profile_settings = $this->option( 'profile' );
		if( isset( $profile_settings['default'] ) && !empty( $profile_settings['default'] ) ){
			if( !defined( 'BP_DEFAULT_COMPONENT' ) ){
				define( 'BP_DEFAULT_COMPONENT', $profile_settings['default'] );
			}
		}

        //Reorder admin nav items
        if( ! isset( $profile_settings['rd_adminbar_nav'] ) || 'yes' == $profile_settings['rd_adminbar_nav'] ) {
			if( !defined( 'BP_RD_ADMINBAR_NAV' ) ){
				define( 'BP_RD_ADMINBAR_NAV', 'true' );
			}
		}
	}
	
	private function setup_actions(){
		
		// Hook into BuddyPress init
		add_action( 'bp_loaded', array( $this, 'bp_loaded' ) );

		//Setup admin setting page
		add_action( 'init', array( $this, 'setup_admin_settings' ) );
	}

	/**
	 * Setup plugin options settings admin page
	 */
	public function setup_admin_settings() {

		if ( ( is_admin() || is_network_admin() ) && current_user_can( 'manage_options' ) ) {
			$this->load_admin();
		}
	}


	/**
	 * Include required admin files.
	 *
	 * @since BuddyPress Reorder Tabs (1.0.0)
	 * @access private
	 */
	private function load_admin(){
		require_once( BUDDYBOSS_REORDER_TABS_PLUGIN_DIR . 'includes/admin.php' );

		$this->admin = BuddyPress_Reorder_Tabs_Admin::instance();
	}
	
	/**
	 * Load plugin text domain
	 *
	 * @since BuddyPress Reorder Tabs (1.0.0)
	 *
	 * @uses sprintf() Format .mo file
	 * @uses get_locale() Get language
	 * @uses file_exists() Check for language file
	 * @uses load_textdomain() Load language file
	 */
	public function setup_textdomain(){
		$domain = 'bp-reorder-tabs';
		$locale = apply_filters('plugin_locale', get_locale(), $domain);
		
		//first try to load from wp-contents/languages/plugins/ directory
		load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo');
		
		//if not found, then load from bp-reorder-tabs/languages/ directory
		load_plugin_textdomain( $domain, false, 'bp-reorder-tabs/languages' );
	}
	
	/**
	 * We require BuddyPress to run the main components, so we attach
	 * to the 'bp_loaded' action which BuddyPress calls after it's started
	 * up. This ensures any BuddyPress related code is only loaded
	 * when BuddyPress is active.
	 *
	 * @since BuddyPress Reorder Tabs (1.0.0)
	 *
	 * @return void
	 */
	public function bp_loaded(){
		add_action( 'bp_setup_nav',				array( $this, 'change_profile_tab_order' ), 999 );
		/**
		 * Actions bp_setup_nav or groups_setup_nav don't work for group navs.
		 * Let's hook late.
         * 
         * Compatibility with bp group extras: 
         *  - lets hook it AFTER bp group extras,
         *  - if bp group extras has already ordered the navs, leave it alone
		 */
        add_action( 'bpge_get_nav_order',       array( $this, 'set_flag_bp_group_extras' ) );
		add_action( 'bp_screens',                  array( $this, 'change_groups_tab_order' ), 101 );
		add_action( 'bp_init',                  array( $this, 'change_subnavs' ), 999 );
		
		add_action( 'bp_group_options_nav',		array( $this, 'save_group_navs_info' ) );
        add_action( 'bp_member_options_nav',	array( $this, 'save_profile_navs_info' ) );
		
		add_filter( 'bp_r_t_my_group_url',		array( $this, 'url_add_secret' ) );
        add_filter( 'bp_r_t_my_profile_url',	array( $this, 'url_add_secret' ) );
        
        add_action( 'wp_before_admin_bar_render', array($this, 'reorder_wp_menus'), 9999 );
        
        add_action( 'wp_footer', array( $this, 'maybe_reset_default_component' ) );
	}
	
	public function change_profile_tab_order(){
		$bp = buddypress();
		
		$profile_settings = $this->option( 'profile' );
		
		// those, whose position has been specified.
		$navs_defined = array();
		$last_nav_position = 0;
		
		if( isset( $profile_settings['config_data'] ) && !empty( $profile_settings['config_data'] ) ){
			$config = (array)$profile_settings['config_data'];
			foreach( $config as $nav => $nav_settings ){
				$nav_settings = (array)$nav_settings;
				/**
				 * set position starting from 10.
				 * so first nav has position 10, second has 11 and so on
				 */
				if( $bp->members->nav->__isset( $nav ) ){
                    $nav_obj = $bp->members->nav->__get( $nav );
                    $nav_obj->position = 10 + (int)$nav_settings['position'];
                    $bp->members->nav->__set( $nav, $nav_obj );

                    $navs_defined[] = $nav;
                    $last_nav_position = (int)$nav_settings['position'];
				}
			}
		}
		
		$last_nav_position += 10;
		//now put all the remaining at the end
        $primary_nav_items = $bp->members->nav->get_primary();
        if( !empty( $primary_nav_items ) ){
            foreach( $primary_nav_items as $nav_obj ){
                if( !in_array( $nav_obj->slug, $navs_defined ) ){
                    $last_nav_position++;
                    $nav_obj->position = $last_nav_position;
                    $bp->members->nav->__set( $nav_obj->slug, $nav_obj );
                }
            }
        }
		
		//change subnav order
		/*if( bp_displayed_user_id() ){
			if( isset( $bp->bp_options_nav[bp_current_action()] ) ){
				
			}
		}*/
	}
	
    /**
     * Set bp_group_extras flag to true.
     * 
     * @since 1.0.8
     * @return void
     */
    public function set_flag_bp_group_extras(){
        $this->flag_bp_group_extras = true;
    }
    
	public function change_groups_tab_order(){
        if( $this->flag_bp_group_extras )
            return;//plugin 'bp group extras' has reordered the nav items, so lets abort.
        
		$bp = buddypress();
		
		if( 'groups' !=  bp_current_component() || ! bp_is_single_item() )
			return;
		
		if( bp_displayed_user_id() )
			return;
		
		$the_index = bp_current_item();
		if( !$the_index )
			return;
		
		$profile_settings = $this->option( 'groups' );
		
		// those, whose position has been specified.
		$navs_defined = array();
		$last_nav_position = 0;
        
        $bp_options_nav = $bp->groups->nav->get_secondary( array( 'parent_slug' => $the_index ) );
        $bp_options_nav_reordered = array();
        
		if( !empty( $bp_options_nav ) && isset( $profile_settings['config_data'] ) && !empty( $profile_settings['config_data'] ) ){
			$group_navs = (array)$profile_settings['config_data'];
			$reordered_group_navs = array();
			
			foreach( $group_navs as $nav=>$nav_props ){
				if( '' == $nav )
					continue;

				$nav_props = (array) $nav_props;
				$reordered_group_navs[$nav_props['position']] = $nav;
			}
            
			if( !empty( $reordered_group_navs ) ){
				ksort( $reordered_group_navs );
				foreach( $reordered_group_navs as $nav ){
					$nav_settings = (array) $group_navs[$nav];
                    
                    //find the corresponding nav item
                    foreach( $bp_options_nav as $pos => $nav_obj ){
                        if( $nav_obj->slug==$nav ){
                            /**
                             * set position starting from 10.
                             * so first nav has position 10, second has 11 and so on
                            */
                            $position = 10 + (int)$nav_settings['position'];
                            $nav_obj->position = $position;
                            $bp_options_nav_reordered[$position] = $nav_obj;
                            $last_nav_position = (int)$nav_settings['position'];
                            $navs_defined[] = $nav;
                            break;
                        }
                    }
				}
			}
		}
		
		$last_nav_position += 10;
		//now put all the remaining at the end
		foreach( $bp_options_nav as $post => $nav_obj ){
            $slug = $nav_obj->slug;
			if( !in_array( $slug, $navs_defined ) ){
				$last_nav_position++;
                $bp_options_nav_reordered[$last_nav_position] = $nav_obj;
			}
        }
        
        $bp->groups->nav->__set( $the_index, $bp_options_nav_reordered );
	}
	
	public function change_subnavs(){
        $bp = buddypress();

		$profile_settings = $this->option( 'profile' );
        
		if( isset( $profile_settings['config_data'] ) && !empty( $profile_settings['config_data'] ) ){
			$topnavs = (array)$profile_settings['config_data'];
			foreach( $topnavs as $topnav=>$topnav_settings ){
                $bp_options_nav = $bp->members->nav->get_secondary( array( 'parent_slug' => $topnav ) );
                
				if( isset( $topnav_settings->subnavs ) && !empty( $topnav_settings->subnavs ) ){
					$subnavs = (array) $topnav_settings->subnavs;
				
					foreach( $subnavs as $subnav=>$subnav_props ){
						
						if( !empty( $bp_options_nav ) ){
							foreach( $bp_options_nav as $index => $opt_nav ){
								if( $opt_nav->slug == $subnav ){
                                    $opt_nav->position = 10 + $subnav_props->position;
                                    
                                    $nav_args = array();
                                    foreach( $opt_nav as $k=>$v ){
                                        $nav_args[$k] = $v;
                                    }
                                    $bp->members->nav->edit_nav( $nav_args, $opt_nav->slug, $topnav );
								}
							}
						}
					}
				}
			}
		}
	}
	
	function url_add_secret( $url ){
		if( $url ){
			$url = esc_url(add_query_arg( 'secret', $this->secret, $url ));
		}
		return $url;
	}
	
	/**
	 * Apparantely, there is no direct way of determining what all nav items will be displayed on a group page.
	 * 
	 * So we'll hook into this action and save the nav items in db for later use.
	 * 
	 * @since 1.0.0
	 */
	public function save_group_navs_info(){
		if( !isset( $_GET['secret'] ) || $_GET['secret'] != $this->secret )
			return;
		
		$bp = buddypress();
		
		if ( ! bp_is_single_item() ) 
			return;
        
        $the_index = bp_current_item();
        
		/**
		 * get all nav items for a single group
		 */
		$group_navs = array();
        $bp_options_nav = $bp->groups->nav->get_secondary( array( 'parent_slug' => $the_index ) );
		if ( empty( $bp_options_nav ) ) {
			return false;
		}

		// Loop through each navigation item
        foreach( $bp_options_nav as $pos => $subnav_item ){
            if ( empty( $subnav_item->name ) ) continue;

			$item = array(
				'name'		=> $subnav_item->name,
				'position'	=> $subnav_item->position,
			);
			
			$group_navs[$subnav_item->slug] = $item;
		}
		
		//override positions with settings saved previously
		$saved_options = $this->options;
		$config_data = $saved_options['groups']['config_data'];
		if( !empty( $config_data ) ){
			foreach( $config_data as $s_nav=>$s_nav_props ){
				$s_nav_props = (array)$s_nav_props;
				if( isset( $s_nav_props['position'] ) ){
					if( isset( $group_navs[$s_nav] ) )
						$group_navs[$s_nav]['position'] = $s_nav_props['position'];
				}
			}
		}
		
		$saved_options['groups']['config_data'] = $group_navs;
		
		update_option( 'b_r_t_plugin_options', $saved_options );
	}
    
    /**
	 * Apparantely, there is no direct way of determining what all nav items will be displayed on user profile page.
	 * 
	 * So we'll hook into this action and save the nav items in db for later use.
	 * 
	 * @since 1.0.6
	 */
	public function save_profile_navs_info(){
		if( !isset( $_GET['secret'] ) || $_GET['secret'] != $this->secret )
			return;
		
		$bp = buddypress();
		
		if ( !bp_is_user() ) 
			return;
		
		/**
		 * get all nav items for user profile
		 */
		$profile_navs = array();
        
		// Loop through each navigation item
		foreach( $bp->members->nav->get_primary() as $nav_outer ){
			$item_outer = array(
				'name'		=> $nav_outer->name,
				'position'	=> $nav_outer->position,
				'subnavs'	=> array(),
			);
			
			$profile_navs[$nav_outer->slug] = $item_outer;
			
			if( ( $subnavs = $bp->members->nav->get_secondary( array( 'parent_slug' => $nav_outer->slug ) ) ) != '' ){
				foreach( $subnavs as $nav_inner ){
					$item_inner = array(
						'name'		=> $nav_inner->name,
						'position'	=> $nav_inner->position,
					);
				}
			}
		}
		
		//override positions with settings saved previously
		$saved_options = $this->options;
		$config_data = $saved_options['profile']['config_data'];
		if( !empty( $config_data ) ){
			foreach( $config_data as $s_nav=>$s_nav_props ){
				$s_nav_props = (array)$s_nav_props;
				if( isset( $s_nav_props['position'] ) ){
					if( isset( $profile_navs[$s_nav] ) )
						$profile_navs[$s_nav]['position'] = $s_nav_props['position'];
				}
			}
		}
		
		$saved_options['profile']['config_data'] = $profile_navs;
		
		update_option( 'b_r_t_plugin_options', $saved_options );
	}
	
	/**
	 * Convenience function to access plugin options, returns false by default
	 */
	public function option( $key ){
		$key    = strtolower( $key );
		$option = isset( $this->options[$key] )
		        ? $this->options[$key]
		        : array();

		// Apply filters on options as they're called for maximum
		// flexibility. Options are are also run through a filter on
		// class instatiation/load.
		// ------------------------

		// This filter is run for every option
		$option = apply_filters( 'b_r_t_plugin_option', $option );

		// Option specific filter name is converted to lowercase
		$filter_name = sprintf( 'b_r_t_plugin_option_%s', strtolower( $key  ) );
		$option = apply_filters( $filter_name,  $option );

		return $option;
	}
	
	/**
	 * REORDER WORDPRESS ADMIN-BAR ITEMS
	 *
	 * @since BuddyBoss Reorder tabs 1.0.0
	 */
	public function reorder_wp_menus() {
		
		global $wp_admin_bar, $bp;

        //Bail if admin nav reorder is turn off
        if( ! defined('BP_RD_ADMINBAR_NAV') ) return;

		$profile_settings = $this->option( 'profile' );
		$nav_data_arr = array();
        $admin_items_arr = array();
        
        $bp_navs = $bp->members->nav->get_primary();

		if ( isset( $profile_settings[ 'config_data' ] ) && ! empty( $profile_settings[ 'config_data' ] ) ) {
			$config = ( array ) $profile_settings[ 'config_data' ];

            /**
             * An array contain wp admin menu href and menu id
             */
            foreach ( $bp->active_components as $key => $value ) {

                if ( empty( $bp->{$key}->admin_menu[0] ) ) continue;

                $admin_nav_item = $bp->{$key}->admin_menu[0];

                $admin_items_arr[] = array(
                        'href'  => $admin_nav_item['href'],
                        'id'    => $admin_nav_item['id']
                );

            }

			//Loop for removing all the nodes
			foreach ( $config as $nav => $nav_settings ) {

                //Profile nav has 'x' prefix so we MUST check hunt out 'xprofile'
				if ( 'profile' == $nav ) {
					$nav_data_arr[ $nav ] = $wp_admin_bar->get_node( 'my-account-x' . $nav );
					$wp_admin_bar->remove_node( 'my-account-x' . $nav );

                    /**
                     * Attempt to find admin nav item from bp nav item slug like
                     * e.g we have bp nav item 'messages' then it will rummage though wp nav item for
                     * the 'my-account-messages'
                     */
				} else {

                    $nav_data = $wp_admin_bar->get_node( 'my-account-' . $nav );

                    if ( ! empty( $nav_data ) ) {

                        $nav_data_arr[ $nav ] = $nav_data;
                        $wp_admin_bar->remove_node( 'my-account-' . $nav );

                        /**
                         * At last take a whack at $admin_items_arr for wp_admin nav item
                         * $admin_items_arr would have 2 elements 1. nav id and 2. nav href
                         * e.g for messages component $admin_items_arr would like
                         * $admin_items_arr[$index] = array(
                         *  'href' => 'http://projects.local.com/members/admin/messages/',
                         *  'id'  => 'my-account-messages'
                         * )
                         */
                    } else {
                        foreach ( $bp_navs as $nav_item )  {
                            if ( $nav_item->slug == $nav ) {
                                $bp_nav_item_link = $nav_item->link;
                                break;
                            }
                        }

                        if( !empty( $bp_nav_item_link )){
                            $wp_admin_nav_item =  array_search( $bp_nav_item_link, wp_list_pluck( $admin_items_arr, 'href', 'id' ) );
                            $nav_data_arr[ $nav ] = $wp_admin_bar->get_node( $wp_admin_nav_item );
                            $wp_admin_bar->remove_node( $wp_admin_nav_item );
                        }
                    }
                }
			}

			//Loop for adding the reordered node
			foreach ( $config as $nav => $nav_settings ) {

				//Continue if nav node is not available
				if ( empty ( $nav_data_arr[$nav] ) ) {
					continue;
				}

				$wp_admin_bar->add_menu( array(
					'parent' => $nav_data_arr[$nav]->parent,
					'id' => $nav_data_arr[$nav]->id,
					'title' => $nav_data_arr[$nav]->title,
					'href' => $nav_data_arr[$nav]->href
				) );
				
			}
			
		}
		
	}
	
    public function maybe_reset_default_component(){
        if( is_404() ){
            if( bp_is_user() ){
                $profile_settings = $this->option( 'profile' );
                if( isset( $profile_settings['default'] ) && bp_current_component() == $profile_settings['default'] ) {
                    /**
                     * we've set BP_DEFAULT_COMPONENT acc to our settings.
                     * But, apparently that component is no longer active.
                     * Reasons for that could be:
                     *      - component is now disabled by admin.
                     *      - in case of custom component, its plugin is now deactivated.
                     *      - etc..
                     * 
                     * Let's fix that
                    */
                    unset( $profile_settings['default'] );
                    $settings = $this->options;
                    $settings['profile'] = $profile_settings;
                    update_option( 'b_r_t_plugin_options', $settings );

                    //reload the page
                    ?>
                    <script type="text/javascript">window.location.href = window.location.href;</script>
                    <?php 
                }
            } else if( bp_is_active( 'groups' ) && bp_is_group() ) {
                $group_settings = $this->option( 'groups' );
                if( isset( $group_settings['default'] ) && bp_current_action() == $group_settings['default'] ) {
                    unset( $group_settings['default'] );
                    $settings = $this->options;
                    $settings['groups'] = $group_settings;
                    update_option( 'b_r_t_plugin_options', $settings );

                    //reload the page
                    ?>
                    <script type="text/javascript">window.location.href = window.location.href;</script>
                    <?php 
                }
            }
        }
    }
    
    /**
     * Show admin notice if buddypress version is unsupported.
     * @since 1.0.9
     */
    public function admin_notice_min_bp_version(){
        if ( current_user_can( 'manage_options' ) ){
			$notice = sprintf( __( "Hey! BuddyPress Reorder Tabs requires BuddyPress version 2.6 or higher. You seem to have an older version and therefore BuddyPress Reorder Tabs is not working on your site. Please update BuddyPress.", 'bp-reorder-tabs' ), $bp_settings_link );

			echo "<div class='error'><p>{$notice}</p></div>";
		}
    }
}

endif;
