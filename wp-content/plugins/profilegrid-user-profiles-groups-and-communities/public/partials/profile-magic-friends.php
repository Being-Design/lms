<?php
$pmrequests = new PM_request;
$dbhandler = new PM_DBhandler;
$pmfriends = new PM_Friends_Functions;
$pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
$identifier = 'FRIENDS';
$profilemagic_profile_url = $profile_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
$profilemagic_my_friend_url = add_query_arg( 'pm_tab',1,$profilemagic_profile_url );
$profilemagic_my_suggestion_url = add_query_arg( 'pm_tab',2,$profilemagic_profile_url );
$profilemagic_my_request_url = add_query_arg( 'pm_tab',3,$profilemagic_profile_url );
$current_user = wp_get_current_user();
//if(isset($content['id']))$uid = $content['id'];else $uid = filter_input(INPUT_GET, 'uid');
if(!isset($uid) && is_user_logged_in()){$uid = $current_user->ID;}
$path =  plugin_dir_url(__FILE__);
$pagenum = filter_input(INPUT_GET, 'pagenum');
$activefriendtab = filter_input(INPUT_GET, 'pm_tab');
$activefriendtab = isset($activefriendtab) ? absint($activefriendtab) : 1;
$pm_f_search = filter_input(INPUT_GET,'pm_f_search');
$pagenum = isset($pagenum) ? absint($pagenum) : 1;
$limit = 20; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$meta_query_array = $pmrequests->pm_get_user_meta_query($_GET);
$date_query = $pmrequests->pm_get_user_date_query($_GET);
$total_myfriends = $pmfriends->pm_count_my_friends($uid);
$total_myfriends_requests = $pmfriends->pm_count_my_friend_requests($uid);
$total_send_requests = $pmfriends->pm_count_my_friend_requests($uid,1);
if($uid==$current_user->ID):
 $u1 = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$uid);
?>
<div class="pm-group-view pm-border-bt">
<div class="pm-section pm-dbfl" id="pg-friends-container">
 <div class="pm-section-nav-vertical pm-difl">
    <ul class="dbfl">
        <li class="pm-dbfl pm-border-bt pm-pad10"><a class="pm-dbfl" href="#pg-myfriends"><?php _e('My Friends','profile-grid');?><span class="pm-difr notification-count"><?php echo $total_myfriends;?></span></a></li>
        <li class="pm-dbfl pm-border-bt pm-pad10"><a class="pm-dbfl" href="#pg-friend-requests"><?php _e('Friend Requests','profile-grid');?><span class="pm-difr notification-count"><?php echo $total_myfriends_requests;?></span></a></li>
        <li class="pm-dbfl pm-border-bt pm-pad10"><a class="pm-dbfl" href="#pg-requests-sent"><?php _e('Requests Sent','profile-grid');?><span class="pm-difr notification-count"><?php echo $total_send_requests;?></span></a></li>
    </ul>
</div>

<div id="pg-myfriends" class="pm-blog-desc-wrap pm-difl pm-section-content">
   
   <?php //include 'profile-magic-my-friends.php';
   $pmhtmlcreator->pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,1);
   ?>
</div>
<div id="pg-friend-requests" class="pm-blog-desc-wrap pm-difl pm-section-content">
   
   <?php $pmhtmlcreator->pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,2); //include 'profile-magic-friend-requested.php';?>
</div>
<div id="pg-requests-sent" class="pm-blog-desc-wrap pm-difl pm-section-content">
    
   <?php $pmhtmlcreator->pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,3); //include 'profile-magic-friend-sent-request.php';?>
</div>
</div>
</div>
<?php else:?>
<div id="pg-myfriends" class="pm-blog-desc-wrap pm-difl pm-section-content pm-thirdparty-friends">
   
   <?php //include 'profile-magic-my-friends.php';
   $pmhtmlcreator->pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,1);
   ?>
</div>
<?php endif; ?>


