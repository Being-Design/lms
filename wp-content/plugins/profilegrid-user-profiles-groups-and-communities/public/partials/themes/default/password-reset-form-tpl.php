<?php
global $wpdb;
$path =  plugin_dir_url(__FILE__);
$pmrequests = new PM_request;

?>
<div class="pmagic">
  <div class="pm-login-box pm-dbfl pm-border">
  <?php if(isset($pm_error) && $pm_error!='' && !is_user_logged_in()):?>
    <div class="pm-login-box-error"><?php echo $pm_error;?></div>
    <?php endif;?>
    <!-----Form Starts----->
    
    <?php 
if ( is_user_logged_in()) : ?>
	<?php
			$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
	?> 
	  <div class="pm-login-header pm-dbfl pm-bg pm-border-bt">
		  <h4><?php _e( 'You have already logged in.','profile-grid' );?></h4>
		  <p><?php _e('PROCEED TO','profile-grid');?></p>
	  </div>
	   <div class="pm-login-header-buttons pm-dbfl pm-pad10">
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo $redirect_url;?>" class="pm_button"><?php _e('My Profile','profile-grid');?></a></div>
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo wp_logout_url( $pmrequests->profile_magic_get_frontend_url('pm_user_login_page','')); ?>" class="pm_button"><?php _e('Logout','profile-grid');?></a></div>
	   </div>
	 <?php
else:
?>

    <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
      <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
      <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
      <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" placeholder="<?php _e('New password','profile-grid');?>" required="required" />
      <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" placeholder="<?php _e('Repeat new password','profile-grid');?>" required="required" />
      <div class="pm-login-box-bottom-container">
        <input type="submit" name="submit" id="resetpass-button" class="button" value="<?php _e( 'Reset Password','profile-grid' ); ?>" />
      </div>
    </form>
    
    <?php endif;?>
  </div>
</div>
