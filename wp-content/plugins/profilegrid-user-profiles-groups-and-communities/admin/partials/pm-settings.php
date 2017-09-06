<?php
global $wpdb;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
?>

<div class="uimagic">
  <div class="content pm_settings_option">
    <div class="uimheader">
      <?php _e( 'Global Settings','profile-grid' ); ?>
    </div>
    <div class="uimsubheader"> </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_general_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/general.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'General','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Form look, Default pages, Attachment settings etc.','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_security_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/security.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Security','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'reCAPTCHA placement, Google reCAPTCHA keys','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_user_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/usersettings.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'User Accounts','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Activation link, Manual Approvals etc.','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_email_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/autoresponder.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Email Notifications','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Admin Notifications, Multiple Email Notifications, From Email','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_tools">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/tools.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Tools','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Import/ Export Options','profile-grid' ); ?></span> 
      </div>
    </a>
    </div>
    <div class="uimrow"> 
          <a href="admin.php?page=pm_blog_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/userblogs.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'User Blogs','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Default post status, privacy settings etc.','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    <div class="uimrow"> 
          <a href="admin.php?page=pm_message_settings">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/privatemessaging.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Private Messaging','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Turn Private Messaging on/ off','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    <div class="uimrow"> 
        <a href="admin.php?page=pm_friend_settings">
            <div class="pm_setting_image"> 
                <img src="<?php echo $path;?>images/friends.png" class="options" alt="options"> 
            </div>
            <div class="pm-setting-heading"> 
                <span class="pm-setting-icon-title"><?php _e( 'Friends System',$textdomain ); ?></span> 
                <span class="pm-setting-description"><?php _e( 'Turn Friends System on or off and more',$textdomain); ?></span>
            </div>
         </a> 
    </div>
    
    <?php do_action('profile_magic_setting_option'); ?>
    
 
  </div>
</div>
