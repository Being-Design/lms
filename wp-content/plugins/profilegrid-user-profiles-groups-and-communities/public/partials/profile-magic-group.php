<?php
$dbhandler = new PM_DBhandler;
$pm_activator = new Profile_Magic_Activator;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$gid = filter_input(INPUT_GET, 'gid');
$identifier = 'GROUPS';
if(!isset($gid))
$gid = $content['id'];
$current_user = wp_get_current_user();
$row = $dbhandler->get_row('GROUPS',$gid);

if(isset($_POST['remove_image']))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_pm_edit_group' ) ) die( __('Failed security check','profile-grid') );
	$groupid = filter_input(INPUT_POST,'group_id');
	
	if($groupid!=0)
	{
		$data = array('group_icon'=>'');
		$arg = array('%d');
	    $dbhandler->update_row($identifier,'id',$groupid,$data,$arg,'%d');
	}
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_group_page','');
	$redirect_url = add_query_arg('gid',$groupid,$redirect_url);
	wp_redirect( $redirect_url );
	exit;
	
}

if(isset($_POST['cancel']))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_pm_edit_group' ) ) die( __('Failed security check','profile-grid') );
	$groupid = filter_input(INPUT_POST,'group_id');
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_group_page','');
	$redirect_url = add_query_arg('gid',$groupid,$redirect_url);
	wp_redirect( $redirect_url );
	exit;
}

if(isset($_POST['edit_group']))
{
	
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_pm_edit_group' ) ) die( 'Failed security check' );
	$groupid = filter_input(INPUT_POST,'group_id');
	$exclude = array("_wpnonce","_wp_http_referer","edit_group","group_id");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	$filefield = $_FILES['group_icon'];
	$allowed_ext ='jpg|jpeg|png|gif';
	if(isset($filefield) && !empty($filefield))
	{
		$attachment_id = $pmrequests->make_upload_and_get_attached_id($filefield,$allowed_ext);
		$post['group_icon'] = $attachment_id;
	}
	
	if($post!=false)
	{
		foreach($post as $key=>$value)
		{
		  $data[$key] = $value;
		  $arg[] = $pm_activator->get_db_table_field_type($identifier,$key);
		}
	}
	if($groupid!=0)
	{
	    $dbhandler->update_row($identifier,'id',$groupid,$data,$arg,'%d');
	}
	$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_group_page','');
	$redirect_url = add_query_arg('gid',$groupid,$redirect_url);
	wp_redirect( $redirect_url );
	exit;	
}



if(!empty($row))
{
	$pagenum = filter_input(INPUT_GET, 'pagenum');
	
	$pagenum = isset($pagenum) ? absint($pagenum) : 1;
	$limit = 10; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	$meta_query = array(
						'relation' => 'AND',
						array(
							'key'     => 'pm_group',
							'value'   => $gid,
							'compare' => '='
						),
						array(
							'key'     => 'rm_user_status',
							'value'   => '0',
							'compare' => '='
						)
					);
	if($row->is_group_leader!=0)
	$group_leader = username_exists($row->leader_username);
	if(isset($group_leader))$exclude = array($group_leader);else{ $exclude = array(); $group_leader = 0;}
	$users =  $dbhandler->pm_get_all_users('',$meta_query,'',$offset,$limit,'ASC','ID');
	$total_users = count($dbhandler->pm_get_all_users('',$meta_query,'','','','ASC','ID'));
	$num_of_pages = ceil( $total_users/$limit);
	$pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum);
	if(filter_input(INPUT_GET, 'edit') && $group_leader==$current_user->ID && is_user_logged_in())
	{
                 $themepath = $this->profile_magic_get_pm_theme('edit-group-tpl');
                 include $themepath;
	}
	else
	{
                 $themepath = $this->profile_magic_get_pm_theme('group-tpl');
                 include $themepath;	
	}
	
}
else
{
	_e( 'Sorry, this group is currently not accessible. Either it was deleted or its ID does not matches.','profile-grid' ); 
}
?>