<?php
$dbhandler = new PM_DBhandler;
$textdomain = $this->profile_magic;
$pmrequests = new PM_request;
$path =  plugin_dir_url(__FILE__);
$identifier = 'SETTINGS';
if(filter_input(INPUT_POST,'submit_settings'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_message_settings' ) ) die( 'Failed security check' );
	$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
	if(!isset($_POST['pm_enable_private_messaging'])) $_POST['pm_enable_private_messaging'] = 0;
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
  <form name="pm_message_settings" id="pm_message_settings" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <div class="uimheader">
        <?php _e( 'Messaging Settings','profile-grid' ); ?>
      </div>
     
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
    
        <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Enable Private Messaging','profile-grid' ); ?>
        </div>
        <div class="uiminput">
           <input name="pm_enable_private_messaging" id="pm_enable_private_messaging" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_enable_private_messaging','1'),'1'); ?> class="pm_toggle" value="1" style="display:none;" />
          <label for="pm_enable_private_messaging"></label>
        </div>
        <div class="uimnote"><?php _e("Turn on private messaging system for your site users. Registered users can start conversations with each other.",'profile-grid');?></div>
      </div>
        
   
      <div class="buttonarea"> 
          <a href="admin.php?page=pm_settings">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <?php wp_nonce_field('save_message_settings'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_settings" id="submit_settings" />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
   
  </form>
</div>