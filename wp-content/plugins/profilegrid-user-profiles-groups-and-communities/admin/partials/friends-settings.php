<?php
$textdomain = $this->profile_magic;
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$path =  plugin_dir_url(__FILE__);
$identifier = 'SETTINGS';
if(filter_input(INPUT_POST,'submit_settings'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_friends_settings' ) ) die( 'Failed security check' );
	$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if(!isset($post['pm_friends_panel'])) $post['pm_friends_panel'] = 0;
	if($post!=false)
	{
		if(!isset($post['pm_friends_panel'])) $post['pm_friends_panel'] = 0;
		if(!isset($post['pm_send_message_to_anyusers'])) $post['pm_send_message_to_anyusers'] = 0;
		if(!isset($post['pm_show_friend_suggestion'])) $post['pm_show_friend_suggestion'] = 0;
		if(!isset($post['pm_allow_sending_request_to_rejected_person'])) $post['pm_allow_sending_request_to_rejected_person'] = 0;
	
		foreach($post as $key=>$value)
		{
			$dbhandler->update_global_option_value($key,$value);
		}
	}
	
	wp_redirect('admin.php?page=pm_settings');exit;
}
?>

<div class="uimagic">
  <form name="pm_user_settings" id="pm_user_settings" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <div class="uimheader">
        <?php _e( 'Friends Integration', $textdomain ); ?>
      </div>
     
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Allow/ Disallow Making Friends:', $textdomain ); ?>
        </div>
        <div class="uiminput">
            <input name="pm_friends_panel" id="pm_friends_panel" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_friends_panel'),'1'); ?> class="pm_toggle" value="1" style="display:none;" onClick="pm_show_hide(this,'friendshtml')"/>
          <label for="pm_friends_panel"></label>
        </div>
        <div class="uimnote"> Toggle Friends functionality on/ off.</div>
      </div> 
    
      <div class="childfieldsrow" id="friendshtml" style=" <?php if($dbhandler->get_global_option_value('pm_friends_panel','0')==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
      
     
      
     
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Allow sending request to person who rejected earlier request:', $textdomain ); ?>
        </div>
        <div class="uiminput">
        <input name="pm_allow_sending_request_to_rejected_person" id="pm_allow_sending_request_to_rejected_person" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_allow_sending_request_to_rejected_person'),'1'); ?> class="pm_toggle" value="1" style="display:none;" onClick="pm_show_hide(this,'rejectedhtml')" />
          <label for="pm_allow_sending_request_to_rejected_person"></label>
          
        </div>
        <div class="uimnote">Allow resending of friend requests to users who have earlier rejected the request.</div>
      </div>
       <div class="childfieldsrow" id="rejectedhtml" style=" <?php if($dbhandler->get_global_option_value('pm_allow_sending_request_to_rejected_person','0')==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'After how many days?', $textdomain ); ?>
        </div>
        <div class="uiminput">
         <input name="pm_send_request_to_rejected_person_after_days" id="pm_send_request_to_rejected_person_after_days" type="number" min="0" value="<?php echo $dbhandler->get_global_option_value('pm_send_request_to_rejected_person_after_days','0');?>" />
       
          
        </div>
        <div class="uimnote">Define number of days after which a user can resend friend request to another user who has earlier deleted his/ her request.</div>
      </div>
      </div>
     
      
      </div>
      
      <div class="buttonarea"> <a href="admin.php?page=pm_settings">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel',$textdomain);?>
        </div>
        </a>
        <?php wp_nonce_field('save_friends_settings'); ?>
        <input type="submit" value="<?php _e('Save',$textdomain);?>" name="submit_settings" id="submit_settings" />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>