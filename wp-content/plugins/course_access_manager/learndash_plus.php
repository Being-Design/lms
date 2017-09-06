<?php
/**
 * @package LearnDash 
 * @version 1.0.2
 */
/*
Plugin Name: Course Access Manager
Plugin URI: http://www.learndash.com
Description: Makes it easy to protect and sell online course content
Version: 1.0
Author: LearnDash
Author URI: http://www.learndash.com
*/

include(dirname(__FILE__)."/enhanced-paypal-shortcodes.php");

add_action('admin_menu', 'learndash_plus_menu', 0);
function learndash_plus_menu() {
    add_menu_page(__("Course Access", 'learndash-plus') , __("Course Access", 'learndash-plus'), "manage_options", "learndash-plus-settings", null, null, null);
    add_submenu_page("learndash-plus-settings", __("Settings", 'learndash-plus') , __("Settings", 'learndash-plus'),'manage_options','learndash-plus-settings', 'learndash_plus_settings_page');
    add_submenu_page("learndash-plus-settings", __("Access Levels", 'learndash-plus') , __("Access Levels", 'learndash-plus'),'manage_options','learndash-plus-levels', 'learndash_plus_levels_page');	
    add_submenu_page("learndash-plus-settings", __("Paypal Settings", 'learndash-plus') , __("Paypal Settings", 'learndash-plus'),'manage_options','learndash-plus-paypal-settings', 'learndash_plus_paypal_settings_page');	
    		
}

function learndash_plus_settings_page() {
	include(dirname(__FILE__)."/settings_page.php"); 
}

function learndash_plus_levels_page() {
	include(dirname(__FILE__)."/levels_page.php"); 
}

function learndash_plus_paypal_settings_page() {
	include(dirname(__FILE__)."/paypal_settings_page.php"); 
}

/* Adds a box to the main column on the Post and Page edit screens */
include(dirname(__FILE__)."/page_meta.php");

/**** Additional Profile Field - Start ****/
include(dirname(__FILE__)."/profile_fields.php");
	

/* Restrict Access */
function learndash_plus_restrict_access() {
	global $post;
	
	if(!is_singular()) //Exit without sending page view for Category and Tag pages.
	return;
	
	if(learndash_plus_is_redirect_url($post))
	return;
	
	$user_id = get_current_user_id();
	$accessable = false;
	
	$accessable = learndash_plus_check_access($user_id, $post->ID);
	
	if(!$accessable)
	{
		$learndash_plus_settings = get_option( 'learndash_plus_settings' );
		$redirect_url =  isset($learndash_plus_settings['redirect_url'])? $learndash_plus_settings['redirect_url']:"";
		
		if(empty($redirect_url))
		$redirect_url = get_bloginfo('wpurl');
		wp_redirect( $redirect_url );
		exit;
	}
}
function learndash_plus_check_access($user_id, $post_id) {
	$data = get_post_meta( $post_id, $key = '_learndash_plus', $single = true );
	
	if(current_user_can('manage_options')) //Allow for Admin
		return true;
		
	if(empty($data['protect'])) //Protection not enabled on this post
		return true;
	
	if(empty($user_id))
		return false;
	
	$allowedlevels = !empty($data['selectedlevels'])? $data['selectedlevels']:array();
	$userlevel =  intVal(get_user_meta($user_id, 'learndash_plus_level', true ));
	return in_array($userlevel, $allowedlevels);
}

function learndash_plus_is_redirect_url($post) {
	$learndash_plus_settings = get_option( 'learndash_plus_settings' );
	$redirect_url =  isset($learndash_plus_settings['redirect_url'])? $learndash_plus_settings['redirect_url']:"";
	$redirect_post_id = url_to_postid( $redirect_url );
	if(!empty($post->ID) && $redirect_post_id == $post->ID)
	return true;
}
add_action('wp', 'learndash_plus_restrict_access');
/* Restrict Access */

/* Login Redirect */
function learndash_plus_login($username) {
	$user = get_user_by('login', $username);
	$user_id = $user->ID;
	$level_upgrade 	=  get_user_meta($user_id, 'learndash_plus_level_upgrade', true );
	$now = time();
	if(!empty($level_upgrade) && $now >= $level_upgrade['upgrade_timestamp'])
	{
		$payment = array('amount' => '0.00',
						'currency' => __('AUTO UPGRADE', 'learndash-plus') ,
						'timestamp' => $now
						);
		if(!empty($level_upgrade['nextlevel_id']))
		learndash_plus_update_user_level($user_id, $level_upgrade['nextlevel_id'], $payment);
	}
	learndash_plus_loginredirect($user);
}
function learndash_plus_loginredirect($user) {
		$user_id = $user->ID;
		$current_level = get_user_meta($user_id, 'learndash_plus_level', true );
		$levels = learndash_plus_get_levels();
		if(!empty($levels[$current_level]) && !empty($levels[$current_level]['redirect_url']))
		{
			$login_redirect_url =  $levels[$current_level]['redirect_url'];
		}
		else
		{
			$learndash_plus_settings = get_option( 'learndash_plus_settings' );
			$login_redirect_url =  isset($learndash_plus_settings['login_redirect_url'])? $learndash_plus_settings['login_redirect_url']:"";
		}
		if(!empty($login_redirect_url)){
			wp_redirect( $login_redirect_url );
			exit;
		}
}
add_action('wp_login', 'learndash_plus_login');
/* Login Redirect */


/* LDP Transactions */
function ldp_transactions_post_content() {
	load_plugin_textdomain( 'learndash-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 	
	$labels = array(
		'name'               => _x( 'Transactions', 'post type general name', 'learndash-plus' ),
		'singular_name'      => _x( 'Transaction', 'post type singular name', 'learndash-plus' ),
		'add_new'            => _x( 'Add New', 'book' , 'learndash-plus'),
		'add_new_item'       => __( 'Add New Transaction' , 'learndash-plus'),
		'edit_item'          => __( 'Edit Transaction' , 'learndash-plus'),
		'new_item'           => __( 'New Transaction', 'learndash-plus' ),
		'all_items'          => __( 'Transactions', 'learndash-plus' ),
		'view_item'          => __( 'View Transaction', 'learndash-plus' ),
		'search_items'       => __( 'Search Transaction', 'learndash-plus' ),
		'not_found'          => __( 'No Transaction found', 'learndash-plus' ),
		'not_found_in_trash' => __( 'No Transaction found in the Trash', 'learndash-plus' ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Transactions', 'learndash-plus')
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __('Holds Course Transactions', 'learndash-plus'),
		'public'        => true,
		'menu_position' => 5,
		'show_in_menu'	=> 'learndash-plus-settings',
		'supports'      => array( 'title', 'custom-fields'),
		'show_in_nav_menus' => false ,
		'has_archive'   => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
	);
	register_post_type( 'ldp-transactions', $args );	
}
add_action( 'init', 'ldp_transactions_post_content' );
/* LDP Transactions */

function learndash_plus_paypal_ipn() {
	if(!empty($_GET['ldp-paypal-ipn']))
	{
		include(dirname(__FILE__)."/ipn.php");
		exit;
	}
}
add_action( 'wp', 'learndash_plus_paypal_ipn' );


/* Role Managmenet */
    function learndash_plus_update_role($user_id, $levelid)
	{
		if(user_can( $user_id, 'manage_options' ))
		return;
		
		$levels = learndash_plus_get_levels();
		
		if(isset($levels[$levelid]) && isset($levels[$levelid]['role']))
		{
			global $wpdb;
			$newrole = $levels[$levelid]['role'];
			$user = new WP_User($user_id);
			$role = $wpdb->prefix . 'capabilities';
			$user->role = array_keys($user->$role);
			$role = $user->role[0];
			$user->remove_role($role);
			$user->add_role($newrole);
		}
	}
	function learndash_plus_update_user_level($user_id, $levelid, $payment) 
	{
		
		$newlevel = learndash_plus_get_levels($levelid);		

		if(empty($newlevel))
		{
			update_user_meta( $user_id, 'learndash_plus_level_upgrade', null );
			return false;
		}
		else
		{
			$now = time();	
			$new_level_upgrade['join_timestamp'] = $now;
			$new_level_upgrade['join_id'] = $newlevel['id'];
			$new_level_upgrade['upgrade_timestamp'] = $new_level_upgrade['join_timestamp'] + $newlevel['autoupgradeafter'] * 24 * 60 * 60 ;
			$new_level_upgrade['nextlevel_id'] = $newlevel['nextlevel'];
			$new_level_upgrade['autoupgradeafter'] = $newlevel['autoupgradeafter'];
			
			if(!empty($newlevel['nextlevel']))
			update_user_meta( $user_id, 'learndash_plus_level_upgrade', $new_level_upgrade );
			else
			update_user_meta( $user_id, 'learndash_plus_level_upgrade', null);
			
			
			//Update Level
			update_user_meta($user_id, 'learndash_plus_level', $levelid );
			
			//Send Email
			learndash_plus_send_email($user_id, $newlevel['name']);
			
			//Update Role
			learndash_plus_update_role($user_id, $levelid);
			
			//Update Status
			update_user_meta( $user_id, 'learndash_plus_membership_status', '1' );
			
			//Update Payment
			if(!empty($payment)) {
			$paid = get_user_meta( $user_id, 'learndash_plus_paid', true );
			$paid[$now] = array(	'amount' 	=> $payment['amount'],
									'currency'	=> $payment['currency'],
									'timestamp'		=> $payment['timestamp'],
									'membership_level' => $newlevel['name'],
									'membership_id' =>  $levelid
								);
			
			update_user_meta( $user_id, 'learndash_plus_paid', $paid );
			}
			return true;
		}
	}
/* Role Managmenet */	

/* Level Functions */
	function learndash_plus_get_new_level_id($learndash_plus_levels_options = null)
	{
		if(empty($learndash_plus_levels_options) || empty($learndash_plus_levels_options['levels']))
			return 1;
		else
		{
			$id = 1;
			foreach($learndash_plus_levels_options['levels'] as $level)
			{
				if($id < $level['id'])
				$id = $level['id'];
			}
			return $id + 1;
		}
	}
	function learndash_plus_get_levels($id = null)
	{
		$learndash_plus_levels_options = get_option('learndash_plus_levels_options');
		$levels = isset($learndash_plus_levels_options['levels'])? $learndash_plus_levels_options['levels']:array();
		if(is_null($id))
		return $levels;
		else
		{
			if(!empty($levels[$id]))
			return $levels[$id];
			else if($id == 0) 
			{
				return array(
							'id' => 0,
							'name' => 'None',
							'role' => '',
							'nextlevel' => '',
							'autoupgradeafter' => 0,
							'price' => '0.00',
							'price_type' => '',
							'p3' => 0,
							't3' => '',
							'redirect_url' => ''
							);
			}
			else
			return array();
		}
	}
/* Level Functions */


/* Email Functions */
function learndash_plus_email_template_default() {
	$learndash_plus_email_template_default = __("Dear [name],
As of today, you now have access to all content for [access-level]!  Log into your account to access the content.

Thanks!", 'learndash-plus');
	return $learndash_plus_email_template_default;
}
function learndash_plus_email_subject_default() {
	return __("Congratulations! Your have got new Access Level", 'learndash-plus');
}
function learndash_plus_email_subject() {
	$learndash_plus_email_subject_default = learndash_plus_email_subject_default();
	$learndash_plus_settings = get_option( 'learndash_plus_settings' );
	$learndash_plus_email_subject =  isset($learndash_plus_settings['learndash_plus_email_subject'])? $learndash_plus_settings['learndash_plus_email_subject']:$learndash_plus_email_subject_default;
	
	return $learndash_plus_email_subject;
}

function learndash_plus_email_template() {

	$learndash_plus_email_template_default = learndash_plus_email_template_default();
	$learndash_plus_settings = get_option( 'learndash_plus_settings' );
	$learndash_plus_email_template =  isset($learndash_plus_settings['learndash_plus_email_template'])? $learndash_plus_settings['learndash_plus_email_template']:$learndash_plus_email_template_default;
	
	return $learndash_plus_email_template;
}

function learndash_plus_send_email($user_id, $levelname) {
	$learndash_plus_email_template  = learndash_plus_email_template();
	$user = get_user_by('id', $user_id);
	$admin_email = get_bloginfo( 'admin_email' );
	$blog_name = get_bloginfo();
	$to = $user->user_email;
	$name = $user->user_nicename;
	
	$subject = learndash_plus_email_subject();
	$subject = str_replace("[name]", $name, $subject);
	$subject = str_replace("[access-level]", $levelname, $subject);
	
	$message = learndash_plus_email_template();
	$message = str_replace("[name]", $name, $message);
	$message = str_replace("[access-level]", $levelname, $message);
	
	$headers = 'From: '.$blog_name.' <'.$admin_email.'>' . "\r\n";
	   
	if(!function_exists('wp_email'))
	include_once(ABSPATH . 'wp-includes/pluggable.php');
	
	wp_mail($to, $subject, $message, $headers);
	
}
/* Email Functions */


/* CRON FUNCTION */
function learndash_email_cron() {
	if(isset($_GET['learndash_email_cron']))
	{
		$users = get_users();
		foreach($users as $user) {
			$user_id = $user->ID;
			$level_upgrade 	=  get_user_meta($user_id, 'learndash_plus_level_upgrade', true );
			$now = time();
			if(!empty($level_upgrade) && $now >= $level_upgrade['upgrade_timestamp'])
			{
				$payment = array('amount' => '0.00',
								'currency' => __('AUTO UPGRADE', 'learndash-plus'),
								'timestamp' => $now
								);
				if(!empty($level_upgrade['nextlevel_id']))
				learndash_plus_update_user_level($user_id, $level_upgrade['nextlevel_id'], $payment);
			}	
		}
		
		exit;
	}
}
add_action('wp', 'learndash_email_cron');
/* CRON FUNCTION */
