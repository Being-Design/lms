<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$permalink = get_permalink();
$identifier = 'BLOG';
$themepath = $this->profile_magic_get_pm_theme('add-blog-tpl');
$tiny = ($dbhandler->get_global_option_value('pm_blog_editor','0')==0?false:true);
$poststatus = $dbhandler->get_global_option_value('pm_blog_status','pending');
$settings = array('wpautop' => true,'media_buttons' => false,'textarea_name' => 'blog_description',
    'textarea_rows' => 10,'tabindex' => '','tabfocus_elements' => ':prev,:next','editor_css' => '',
    'editor_class' => '','teeny' => false,'dfw' => false,'tinymce' => $tiny,'quicktags' => false
);
$current_user = wp_get_current_user();
$uid = $current_user->ID;

$gid = $pmrequests->profile_magic_get_user_field_value($uid,'pm_group');
if(isset($_POST['pg_blog_submit']))
{
    $retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
    if (!wp_verify_nonce($retrieved_nonce, 'pg_blog_post' ) ) die( __('Failed security check','profile-grid') );
    $exclude = array("_wpnonce","_wp_http_referer","pg_blog_submit");
    $post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
    if(!isset($post['blog_tags']))$post['blog_tags']='';
    $allowed_ext = 'jpg|jpeg|png|gif';
    $arg = array('post_type' => 'profilegrid_blogs',
        'post_title' =>$post['blog_title'],
        'post_status' => $poststatus,
        'tags_input'	=> $post['blog_tags'],
        'post_content' => $post['blog_description']
        );
    $postid = wp_insert_post($arg);
    if(isset($_FILES['blog_image']))
    {
        $attchment_id = $pmrequests->make_upload_and_get_attached_id($_FILES['blog_image'],$allowed_ext,array(),$postid);
        set_post_thumbnail($postid, $attchment_id );
    }
    update_post_meta($postid,'pm_enable_custom_access','1');
    if(isset($post['pm_content_access'])):
        if($post['pm_content_access']==5)
        {
            update_post_meta($postid,'pm_content_access','2');
            update_post_meta($postid,'pm_content_access_group',$gid);
        }
        else
        {
            if($post['pm_content_access']==2)
            {
                update_post_meta($postid,'pm_content_access_group','all');
            }
            
            update_post_meta($postid,'pm_content_access',$post['pm_content_access']);
        }
    else:
            update_post_meta($postid,'pm_content_access','1');
    endif;
    	
    $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page',site_url('/wp-login.php'));
    wp_redirect( $redirect_url );
    exit;
}
if(is_user_logged_in())
{
    include $themepath;
}
else
{
    $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
    $redirect_url = add_query_arg( 'errors','loginrequired', $redirect_url );
    wp_redirect( $redirect_url );
    exit;	
}
?>