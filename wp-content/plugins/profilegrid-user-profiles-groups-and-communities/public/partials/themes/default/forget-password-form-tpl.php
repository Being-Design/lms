<?php
global $wpdb;
$path =  plugin_dir_url(__FILE__);
$pmrequests = new PM_request;

?>
<div class="pmagic">  
 <div class="pm-login-box pm-dbfl pm-border pm-radius5"> 
 <?php if(isset($pm_error) && $pm_error!='' && !is_user_logged_in()):?>
 <div class="pm-login-box-error pm-dbfl pm-pad10 pm-border-bt"><?php echo $pm_error;?></div>
 <?php endif;?>
 <?php 
if ( is_user_logged_in()) : ?>
	<?php
			$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
	?> 
	  <div class="pm-login-header pm-dbfl pm-bg pm-border-bt pm-pad10">
		  <h4><?php _e( 'You are already logged in.','profile-grid' );?></h4>
		  <p><?php _e('PROCEED TO','profile-grid');?></p>
	  </div>
	   <div class="pm-login-header-buttons pm-dbfl pm-pad10">
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo $redirect_url;?>" class="pm_button"><?php _e('My Profile','profile-grid');?></a></div>
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo wp_logout_url( $pmrequests->profile_magic_get_frontend_url('pm_user_login_page','')); ?>" class="pm_button"><?php _e('Logout','profile-grid');?></a></div>
	   </div>
	 <?php
else:
?>
 
 
 
 <form id="lostpasswordform" class="pmagic-form pm-dbfl pm-bg-lt" action="<?php echo site_url('/wp-login.php?action=lostpassword'); ?>" method="post">
 	<input type="text" name="<?php echo esc_attr('user_login');?>" id="user_login" placeholder="<?php _e('Email or Username','profile-grid');?>">
         
    
            <div class="pm-login-box-bottom-container pm-dbfl pm-bg pm-border">
                <input type="submit" name="submit" class="lostpassword-button" value="<?php _e( 'Reset Password','profile-grid' ); ?>"/>
            </div>
  </form>
 <?php endif;?>
 </div>
 </div>
