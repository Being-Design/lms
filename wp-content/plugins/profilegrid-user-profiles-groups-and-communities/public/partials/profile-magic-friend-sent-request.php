<?php
$pm_u_search = filter_input(INPUT_GET,'pm_u_search');
$requests = $pmfriends->profile_magic_my_friends_requests($uid,1);
if(!empty($requests))
{
	$users =  $dbhandler->pm_get_all_users($pm_u_search,$meta_query_array,'',$offset,$limit,'ASC','include',array(),$date_query,$requests);
	$total_users = count($dbhandler->pm_get_all_users($pm_u_search,$meta_query_array,'','','','ASC','include',array(),$date_query,$requests));
	$num_of_requests_pages = ceil( $total_users/$limit);
        $pagination = $dbhandler->pm_get_pagination($num_of_requests_pages,$pagenum);
	//print_r($users);die;
	//print_r($num_of_pages);die;
	?>	
        <div class="pm-friend-action-bar pm-dbfl">
            <button class="pm-difr pm-delete" onclick="pm_multiple_friends_request_cancel('<?php echo $u1;?>')"><?php _e('Cancel','profile-grid');?></button>
        </div>
<div id="pg-friends-requests-sent-container">
	<div class="pm-my-requests">
	<?php $pmfriends->profile_magic_friends_result_html($users,$uid,3);?>
	</div>
	<?php if($num_of_requests_pages>1):
            echo $pagination;
	endif;
        ?>
</div>
<?php
}
else
{
	echo '<div id="pg-friends-requests-sent-container"><p class="pm-no-result-found">'.__('No new sent requests',$this->profile_magic).'</p></div>';
	
}
?>