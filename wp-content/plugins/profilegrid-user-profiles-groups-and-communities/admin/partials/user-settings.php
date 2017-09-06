<?php
$dbhandler = new PM_DBhandler;
$textdomain = $this->profile_magic;
$pmrequests = new PM_request;
$path =  plugin_dir_url(__FILE__);
$identifier = 'SETTINGS';
if(filter_input(INPUT_POST,'submit_settings'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_user_settings' ) ) die( 'Failed security check' );
	$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
	if(!isset($_POST['pm_auto_approval'])) $_POST['pm_auto_approval'] = 0;
        if(!isset($_POST['pm_send_user_activation_link'])) $_POST['pm_send_user_activation_link'] = 0;
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if($post!=false)
	{
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
        <?php _e( 'User Accounts','profile-grid' ); ?>
      </div>
     
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
      
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'WP Registration Auto Approval:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
           <input name="pm_auto_approval" id="pm_auto_approval" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_auto_approval'),'1'); ?> class="pm_toggle" value="1" style="display:none;" onClick="pm_show_hide(this,'enable_auto_approval_html')" />
          <label for="pm_auto_approval"></label>
        </div>
        <div class="uimnote"><?php _e("Automatically activate user accounts after registration form is submitted. Keep this setting off if you want to manually approve each registering user.",'profile-grid');?></div>
      </div>
        
      <div class="childfieldsrow" id="enable_auto_approval_html" style=" <?php if($dbhandler->get_global_option_value('pm_auto_approval',0)==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
             <div class="uimrow">
                <div class="uimfield">
                  <?php _e( 'Send user Activation link in email:','profile-grid' ); ?>
                </div>
                <div class="uiminput">
                   <input name="pm_send_user_activation_link" id="pm_send_user_activation_link" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_send_user_activation_link'),'1'); ?> class="pm_toggle" value="1" style="display:none;" />
                  <label for="pm_send_user_activation_link"></label>
                </div>
                <div class="uimnote"><?php _e('Send an activation link to user in an email. Keep this setting "off", if you want to automatically approve each registered user.','profile-grid');?></div>
             </div>
      </div>

      <div class="buttonarea"> <a href="admin.php?page=pm_settings">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <?php wp_nonce_field('save_user_settings'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_settings" id="submit_settings" />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>