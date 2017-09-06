<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$gid = filter_input(INPUT_GET, 'gid');
if(!isset($gid))$gid = $content['id'];
//print_r($content);die;
if(isset($content['type']) && $content['type']=='multipage')
{
    wp_enqueue_script( 'profile-magic-multistep-form', plugin_dir_url( __FILE__ ) . '../js/profile-magic-multistep-form.js');
    $type = 'reg-form-multipage-tpl';
}
else 
{
    $type = 'reg-form-tpl';
}
$check = $pmrequests->pm_check_field_exist($gid,'user_email',true);
$meta_query_array = $pmrequests->pm_get_user_meta_query(array('gid'=>$gid));
$total_users_in_group = count($dbhandler->pm_get_all_users('',$meta_query_array));
$limit = $dbhandler->get_value('GROUPS','group_limit',$gid);
$is_group_limit = $dbhandler->get_value('GROUPS','is_group_limit',$gid);
if(isset($_REQUEST["action"]) && $_REQUEST["action"]!='process')
{
	do_action('profile_magic_before_registration_form',$_POST,$_REQUEST,$gid,$textdomain);
	return false;
}

$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',array('associate_group'=>$gid,'show_in_signup_form'=>1),'results',0,false, $sort_by = 'ordering');
//print_r($_POST);die;
if(isset($_POST['reg_form_submit']) || isset($_POST['pm_payment_method']))
{
	$errors = '';
	if($pmrequests->profile_magic_show_captcha('pm_enable_recaptcha_in_reg'))
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
		$errors = $pmrequests->profile_magic_frontend_server_validation($_POST,$_FILES,$_SERVER,$fields,$textdomain);
		if(empty($errors))
		{
			$user_id = $pmrequests->profile_magic_frontend_registration_request($_POST,$_FILES,$_SERVER,$gid,$fields);
			
			do_action('profile_magic_registration_process',$_POST,$_FILES,$_SERVER,$gid,$fields,$user_id,$textdomain);
			
                        if(!isset($_POST['action']) && $dbhandler->get_value('GROUPS','show_success_message',$gid)==1)
                        {
                            echo $dbhandler->get_value('GROUPS','success_message',$gid);
                        }
                
			if($pmrequests->pm_get_user_redirect($gid)!='')
			{
				header('refresh: 5; url='.$pmrequests->pm_get_user_redirect($gid));	
			}
			
		}
		else
		{
			foreach($errors as $error)
			{
				echo $error.'<br />';	
			}
		}
	}
	else
	{
		$errors = __('captcha failed','profile-grid');	
	}
}
else
{
	
	if($check==false)
	{
		$message =  __('Require Useremail field','profile-grid');
	}
	elseif($is_group_limit==1)
	{
		if($limit > $total_users_in_group)
		{
			$message ='';	
		}
		else
		{
			$message  = $dbhandler->get_value('GROUPS','group_limit_message',$gid);
		}
	}elseif($pmrequests->profile_magic_check_paid_group($gid)>0)
        {
            $message = apply_filters( 'profile_magic_check_payment_config','');
            if($message == 'disabled')
            {
                $message = __('Payment system is not configured to accept payments. Please configure at least one payment processor for this to work.','profile-grid');
            }
        }
	else
	{
		$message = '';
	}
	
	if($message!='')
	{
		echo $message;	
	}
	else
	{
		$this->profile_magic_get_pm_theme_tmpl($type,$gid,$fields);
	}
}
?>
