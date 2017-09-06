<?php
$dbhandler = new PM_DBhandler;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$pmrequests = new PM_request;
$html_creator = new PM_HTML_Creator($this->profile_magic,$this->version);
$forget_password_url = $pmrequests->profile_magic_get_frontend_url('pm_forget_password_page',site_url('/wp-login.php?action=lostpassword'));
// Retrieve possible errors from request parameters
$attributes['errors'] = array();
if ( isset( $_REQUEST['errors'] ) ) {
    $error_codes = explode( ',', $_REQUEST['errors'] );
    foreach ( $error_codes as $error_code ) {
        $attributes['errors'][]=  $pmrequests->profile_magic_get_error_message($error_code,$textdomain);
    }
}
// Check if the user just requested a new password 
$pm_error = '';
$attributes['password_updated'] = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';
$attributes['user_activated'] = isset( $_REQUEST['activated'] ) && $_REQUEST['activated'] == 'success';
$attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
if ( $attributes['lost_password_sent'] ) $pm_error .= '<span class="pm_info">'.__( 'Check your email for a link to reset your password.','profile-grid' ).'</span>';
if ( $attributes['password_updated'] ) $pm_error .= '<span class="pm_info">'.__( 'Your password has been changed. You can sign in now.','profile-grid' ).'</span>';
if ( $attributes['user_activated'] ) $pm_error .= '<span class="pm_info">'.__( 'Your account has been successfully activated.','profile-grid' ).'</span>';

if ( count( $attributes['errors'] ) > 0 )
{
	foreach ( $attributes['errors'] as $error )
	{
		$pm_error .= '<span>'.$error.'</span>';
	}
}

if(isset($_POST['login_form_submit']))
{
	if($pmrequests->profile_magic_show_captcha('pm_enable_recaptcha_in_login'))
	{
		$response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';
		$remote_ip = $_SERVER["REMOTE_ADDR"];
		$check_captcha = $pmrequests->profile_magic_captcha_verification($response,$remote_ip);
	}
	else
	{
		$check_captcha=true;
	}
	
	if($check_captcha==true)
	{
		$retrieved_nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'pm_login_form' ) ) die( __('Failed security check','profile-grid') );
		$username = $_POST['user_login'];
   		$password = $_POST['user_pass'];
		$secure_cookie = is_ssl();
    
		if (filter_var($username, FILTER_VALIDATE_EMAIL)) 
		{ //Invalid Email
			$user = get_user_by('email', $username);
		} 
		else 
		{
			$user = get_user_by('login', $username);
		}
    
		if ($user) 
		{
			if(wp_check_password( $password, $user->data->user_pass, $user->ID))
			{
				$creds = array('user_login' => $user->data->user_login, 'user_password' => $password);
				$user = wp_signon( $creds, $secure_cookie );
				//wp_redirect('/members/'.$user->data->user_login.'/courses');
				$pm_redirect_after_login = $dbhandler->get_global_option_value('pm_redirect_after_login','0');
				if($pm_redirect_after_login==0)
				{
					$url = home_url('wp-admin');
				}
				else
				{
					$url = get_permalink($pm_redirect_after_login);	
				}
				
				wp_redirect($url);exit;
			}
			else
			{
				$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
				$redirect_url = add_query_arg( 'errors','incorrect_password', $redirect_url );
				wp_redirect( $redirect_url );exit;
			}
		}
		else
		{
			$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
			$redirect_url = add_query_arg( 'errors','invalid_username', $redirect_url );
			wp_redirect( $redirect_url );exit;
		}
	}
	else
	{
		$pm_error .= '<p class="pm_error">'.__( 'Captcha Failed','profile-grid' ).'</p>';	
	}
}
$themepath = $this->profile_magic_get_pm_theme('login-form-tpl');
include $themepath;
?>
