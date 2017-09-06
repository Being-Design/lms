<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$current_user = wp_get_current_user();
$pm_error = '';

if(isset($_POST['upload_image']))
{
	$filefield = $_FILES['user_image'];
	$allowed_ext ='jpg|jpeg|png|gif';
	if($_POST['user_id']==$current_user->ID)
	{
		$attachment_id = $pmrequests->make_upload_and_get_attached_id($filefield,$allowed_ext);
		update_user_meta($_POST['user_id'],$_POST['user_meta'],$attachment_id);	
	}
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page',site_url('/wp-login.php'));
	wp_redirect( $redirect_url );
	exit;
}

if(isset($_POST['remove_image']))
{
	if($_POST['user_id']==$current_user->ID)
	{
		update_user_meta($_POST['user_id'],$_POST['user_meta'],'');	
	}
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page',site_url('/wp-login.php'));
	wp_redirect( $redirect_url );
	exit;
}

if(isset($_POST['edit_profile']))
{
	$gid = $pmrequests->profile_magic_get_user_field_value($current_user->ID,'pm_group');
	$exclude = " and field_type not in('user_name','user_email','user_avatar','user_pass','confirm_pass','paragraph','heading')";
	$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',array('associate_group'=>$gid),'results',0,false, $sort_by = 'ordering',false,$exclude);
	$errors = $pmrequests->profile_magic_frontend_server_validation($_POST,$_FILES,$_SERVER,$fields,$textdomain,'edit_profile');	
	if(empty($errors))
	{
		$pmrequests->pm_update_user_custom_fields_data($_POST,$_FILES,$_SERVER,$gid,$fields,$current_user->ID);
	
                do_action('profile_magic_update_user_meta',$_POST,$_FILES,$_SERVER,$gid,$fields,$current_user->ID,$textdomain);
			
        }
        
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page',site_url('/wp-login.php'));
	wp_redirect( $redirect_url );
	exit;
}
if(isset($content['id']))$uid = $content['id'];else $uid = filter_input(INPUT_GET, 'uid');
if(isset($uid))
{
     $uid = $pmrequests->pm_get_uid_from_profile_slug($uid);
}
if(!isset($uid) && is_user_logged_in()){$uid = $current_user->ID;}
if(isset($uid))
{
    $user_info = get_userdata($uid);
    if(!empty($user_info)):
	$avatar = get_avatar($user_info->user_email, 274,'',false,array('class'=>'pm-user'));
	$userrole = $pmrequests->get_userrole_name($uid);
	
	$gid = $pmrequests->profile_magic_get_user_field_value($uid,'pm_group');
	if(isset($gid) && $gid!='') $groupinfo = $dbhandler->get_row('GROUPS',$gid);
        if(isset($groupinfo))
        {
            if($groupinfo->is_group_leader!=0){$group_leader = username_exists($groupinfo->leader_username);}else{$group_leader=0;}
	
            if($uid == $group_leader)$class = 'pm-group-leader-medium';else $class='';

            $sections =  $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid),'results',0,false,'ordering');
            $filter_user_id = $pmrequests->pm_get_profile_slug_by_id($current_user->ID);
            if(filter_input(INPUT_GET, 'user_id') && filter_input(INPUT_GET, 'user_id')==$filter_user_id)
            {
                $themepath = $this->profile_magic_get_pm_theme('edit-profile-tpl');
                include $themepath;
            }
            else
            {
                $themepath = $this->profile_magic_get_pm_theme('profile-tpl');
                include $themepath;
                echo '<input type="hidden" value="'.$uid.'" name="pm-uid" id="pm-uid" />';
            }
            
        }
        else
        {
            echo '<div class="pm_message">'.__('Your profile is not yet associated with any parent profile group. To make it visible please associate it with a group first.','profile-grid').'</div>';
        }
    else:
        echo '<div class="pm_message">'.__('This user does not exists.','profile-grid').'</div>';
    endif;   
	
}
else
{
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
	$redirect_url = add_query_arg( 'errors','loginrequired', $redirect_url );
	wp_redirect( $redirect_url );
	exit;	
}

?>