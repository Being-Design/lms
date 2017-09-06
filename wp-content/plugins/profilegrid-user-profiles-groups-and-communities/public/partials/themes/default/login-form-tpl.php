<?php $pmrequests = new PM_request;?>
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
	  <div class="pm-login-header pm-dbfl pm-bg pm-pad10 pm-border-bt">
		  <h3><?php _e( 'You have successfully logged in.','profile-grid' );?></h3>
		  <p><?php _e('PROCEED TO','profile-grid');?></p>
	  </div>
	   <div class="pm-login-header-buttons pm-dbfl pm-pad10">
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo $redirect_url;?>" class="pm_button"><?php _e('My Profile','profile-grid');?></a></div>
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo wp_logout_url( $pmrequests->profile_magic_get_frontend_url('pm_user_login_page','')); ?>" class="pm_button"><?php _e('Logout','profile-grid');?></a></div>
	   </div>
	 <?php
else:
?>
		
 
<!-----Form Starts----->
  <form class="pmagic-form pm-dbfl pm-bg-lt" method="post" action="" id="pm_login_form" name="pm_login_form">
  <?php wp_nonce_field('pm_login_form'); ?>
            <input type="text" name="<?php echo esc_attr('user_login');?>" id="<?php echo esc_attr('user_login');?>" placeholder="<?php _e('Email or Username','profile-grid');?>" required="required">
            <input type="password" name="<?php echo esc_attr('user_pass');?>" id="<?php echo esc_attr('user_pass');?>" placeholder="<?php _e('Password','profile-grid');?>" required="required">
    
            <div class="pm-login-box-bottom-container pm-dbfl pm-bg pm-border">
                <input type="submit" value="Login" name="login_form_submit" class="pm-difl">
                <div class="pm-login-links-box pm-difr pm-pad10">
                <a href="<?php echo $forget_password_url;?>"><?php _e('Forgot Password?','profile-grid');?></a>
                </div>
            </div>
   <?php
  /* $fields = array('username','password','remember_me');
   $html_creator->get_custom_login_form_html($fields,1);*/
   
  /*if($pmrequests->profile_magic_show_captcha('pm_enable_recaptcha_in_login'))
  $html_creator->pm_get_captcha_html();*/
  ?>
  </form>
  <?php endif;?>
   </div>
</div>
