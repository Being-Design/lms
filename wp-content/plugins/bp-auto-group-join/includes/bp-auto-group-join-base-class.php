<?php

/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BP_Auto_Group_Join_Base' ) ):

	/**
	 *
	 * BP Auto Group Join Base
	 * ***********************************
	 */
    class BP_Auto_Group_Join_Base {

        public function __construct() {
            $this->hooks();
            $this->setup_actions();
        }

        public function hooks() {
            add_action( 'bp_core_activated_user', array($this, 'wpmu_auto_group_join_new_user'), 999, 3 );
            add_action( 'wpmu_activate_user', array($this, 'wpmu_auto_group_join_new_user'), 999, 3 );
            add_action( 'xprofile_updated_profile', array($this, 'auto_group_join_new_user'), 999, 1 );
            add_action( 'user_register', array($this, 'auto_group_join_new_user'), 999, 1 );
            add_filter( 'wpmu_signup_user_notification_email', array($this, 'wpmu_signup_user_notification_email'), 999, 5 );
            
            add_action( 'wp_footer', array( $this, 'maybe_auto_join_current_group' ) );
            add_action( 'admin_notices', array( $this, 'member_types_plugin_check' ) );
        }

        function wpmu_signup_user_notification_email($message, $user, $user_email, $key, $meta){
            $activation_link = site_url( "activate/$key" );
            $message = str_replace('%s', $activation_link, $message);
            return $message;
        }

        function wpmu_auto_group_join_new_user($user_id, $key, $user){
            if( !isset($user_id) || empty($user_id) ) return;
            $groups_args = array(
                'object' => 'groups',
                'per_page' => 0,
            );
            if ( bp_has_groups( $groups_args ) ) :
                while ( bp_groups() ) : bp_the_group();
                    $group_id = bp_get_group_id();
                    $get_settings = groups_get_groupmeta($group_id, 'aj_new_registrations', true);
                    if($get_settings == 'all_members'){
                        // add as member
                        groups_accept_invite($user_id, $group_id);
                    }else{
                        $get_mt = groups_get_groupmeta($group_id, 'aj_new_registrations_mt', true);
                        $user_member_type = bp_get_member_type( $user_id );
                        if( ! empty( $get_mt ) & in_array($user_member_type, (array)$get_mt) ){
                            // add as member
                            groups_accept_invite($user_id, $group_id);
                        }
                    }
                endwhile;
            endif;
        }

        function auto_group_join_new_user($user_id){
            if( !isset($user_id) || empty($user_id) ) return;
            $groups_args = array(
                'object' => 'groups',
                'per_page' => 0,
            );
            if ( bp_has_groups( $groups_args ) ) :
                while ( bp_groups() ) : bp_the_group();
                    $group_id = bp_get_group_id();
                    $get_settings = groups_get_groupmeta($group_id, 'aj_new_registrations', true);
                    if($get_settings == 'all_members'){
                        // add as member
                        groups_accept_invite($user_id, $group_id);
                    }else{
                        $get_mt = groups_get_groupmeta($group_id, 'aj_new_registrations_mt', true);
                        $user_member_type = bp_get_member_type( $user_id );
                        if( ! empty( $get_mt ) && in_array($user_member_type, (array)$get_mt) ){
                            // add as member
                            groups_accept_invite($user_id, $group_id);
                        }
                    }
                endwhile;
            endif;
        }
        
        /**
         * In multisite, when the plugin is activated on a site and not network-wide:
         * If user is added/activated from network admin screen, he/she is not auto-added to groups, obviously.
         * 
         * Lets make that work, by performing a check when the user visits a group for the first time.
         * 
         * @return void
         */
        public function maybe_auto_join_current_group(){
            if( !is_user_logged_in() || !is_multisite() )
                return;
            
            if( !bp_is_active( 'groups' ) || !bp_is_group() )
                return;
            
            $group_id = bp_get_group_id();
            $user_id = bp_loggedin_user_id();
            
            /**
             * We'll maintain a record of all the groups user has already visited.
             * So, we know if the user is visiting this group for the first time.
             * This way, we dont add old users to groups when the plugin is moved from network-wide-activated status to site-wide-activated.
             */
            $groups_visited = get_user_meta( $user_id, '_bpagj_groups_visited', true );
            $blog_id = get_current_blog_id();
            $groups_visited[$blog_id] = isset( $groups_visited[$blog_id] ) ? $groups_visited[$blog_id] : array();
            if( !in_array( $group_id, $groups_visited[$blog_id] ) ){
                $groups_visited[$blog_id][] = $group_id;
                update_user_meta( $user_id, '_bpagj_groups_visited', $groups_visited );
            } else {
                //user has already visited this group in past, so we needn't worry about anything.
                return;
            }
            
            if( bp_auto_group_join()->is_network_activated() )
                return;

            if( !groups_is_user_member( $user_id, $group_id ) ){
                $do_redirect = false;
                $get_settings = groups_get_groupmeta($group_id, 'aj_new_registrations', true);
                if($get_settings == 'all_members'){
                    // add as member
                    groups_accept_invite($user_id, $group_id);
                    $do_redirect = true;
                }else{
                    $get_mt = groups_get_groupmeta($group_id, 'aj_new_registrations_mt', true);
                    $user_member_type = bp_get_member_type( $user_id );
                    if( ! empty( $get_mt ) && in_array($user_member_type, (array)$get_mt) ){
                        // add as member
                        groups_accept_invite($user_id, $group_id);
                        $do_redirect = true;
                    }
                }
                
                if( $do_redirect ){
                    ?><script type='text/javascript'>window.location.href = window.location.href;</script><?php 
                }
            }
            
        }

        /**
         * Convenince method for getting main plugin options.
         *
         * @since BP Auto Group Join (1.0.0)
         */
        public function option( $key ) {
            return bp_auto_group_join()->option( $key );
        }

        /**
         * SETUP BUDDYPRESS GLOBAL OPTIONS
         *
         * @since	BP Auto Group Join (1.0.0)
         */
        public function setup_globals( $args = array() ) {

        }

        /**
         * SETUP ACTIONS
         *
         * @since  BP Auto Group Join (1.0.0)
         */
        public function setup_actions() {
            // Add body class
            //add_filter( 'body_class', array( $this, 'body_class' ) );

            // Front End Assets
            if ( ! is_admin() && ! is_network_admin() ) {
                add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
            }

            // Back End Assets
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        }

        /**
         * Add active BP Auto Group Join class
         *
         * @since BP Auto Group Join (1.0.0)
         */
        public function body_class( $classes ) {
            $classes[] = apply_filters( 'bp-auto-group-join_class', 'bp-auto-group-join' );
            return $classes;
        }

        /**
         * Load CSS/JS
         * @return void
         */
        public function assets() {

            // Scripts
            wp_enqueue_script( 'bp-auto-group-join-main', bp_auto_group_join()->assets_url . '/js/bp-auto-group-join.js', array( 'jquery' ), BP_AUTO_GROUP_JOIN_PLUGIN_VERSION, true );
        }

        /**
         * Load Admin Script
         * @return void
         */
        public function admin_assets() {

            $screen       = get_current_screen();
            $screen_id    = $screen ? $screen->id : '';

            if ( in_array( $screen_id, array( 'toplevel_page_bp-groups' ) ) ) {
                // CSS
                wp_enqueue_style( 'bp-auto-group-join-main-admin', bp_auto_group_join()->assets_url . '/css/bp-auto-group-join-admin.css', array(), BP_AUTO_GROUP_JOIN_PLUGIN_VERSION, 'all' );

                // Scripts
                wp_enqueue_script( 'bp-auto-group-join-main-admin', bp_auto_group_join()->assets_url . '/js/bp-auto-group-join-admin.js', array( 'jquery' ), BP_AUTO_GROUP_JOIN_PLUGIN_VERSION, true );
            }
        }

        /**
         * Show admin notice when members type plugin is not active and ajg_bmt_support is on
         */
        public function member_types_plugin_check() {

            $member_type_enabled = bp_auto_group_join()->option('ajg_bmt_support');

            if ( 'on' == $member_type_enabled && ! is_plugin_active( 'buddypress-member-types/buddyboss-bmt.php' ) ) {
                $notice = sprintf( __( "<strong>BP Auto Group Join:</strong> support for joining users to groups based on their member type requires 
                <a href='%s'>BuddyPress Member Types</a> to be active!", 'bp-auto-group-join' ),  'https://www.buddyboss.com/product/buddypress-member-types/' );

                echo '<div class="error"><p>' . $notice . '</p></div>';
            }
        }

    }
	 //End of class BP_Auto_Group_Join_Hooks
	

endif;

