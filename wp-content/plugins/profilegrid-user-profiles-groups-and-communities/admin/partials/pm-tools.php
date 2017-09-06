<?php
global $wpdb;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
?>

<div class="uimagic">
  <div class="content pm_settings_option">
    <div class="uimheader">
      <?php _e( 'Tools','profile-grid' ); ?>
    </div>
    <div class="uimsubheader"> </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_export_users">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/export-users.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Export Users','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Exporting made super simple!','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
    <div class="uimrow"> 
    <a href="admin.php?page=pm_import_users">
      <div class="pm_setting_image"> 
      	<img src="<?php echo $path;?>images/import-users.png" class="options" alt="options"> 
      </div>
      <div class="pm-setting-heading"> 
          <span class="pm-setting-icon-title"><?php _e( 'Import Users','profile-grid' ); ?></span> 
          <span class="pm-setting-description"><?php _e( 'Different options to add users to your site from CSV file','profile-grid' ); ?></span> 
      </div>
    </a> 
    </div>
    
 
  </div>
</div>