<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$basicfunctions = new Profile_Magic_Basic_Functions($this->profile_magic,$this->version);
$path =  plugin_dir_url(__FILE__);
$html_creator = new PM_HTML_Creator($this->profile_magic,$this->version);
$errors = $basicfunctions->get_error_frontend_message();
$paymentpage = $pmrequests->profile_magic_check_paid_group($gid);
$sections =  $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid),'results',0,false,'ordering');
	
?>
<?php 
if ( is_user_logged_in()) : ?>
	<?php
			$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
	?> 
    <div class="pmagic"> 
    <div class="pm-login-box pm-dbfl pm-radius5 pm-border"> 
	  <div class="pm-login-header pm-dbfl pm-bg pm-border-bt">
		  <h4><?php _e( 'You have successfully logged in.','profile-grid' );?></h4>
		  <p><?php _e('PROCEED TO','profile-grid');?></p>
	  </div>
	   <div class="pm-login-header-buttons pm-dbfl pm-pad10">
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo $redirect_url;?>" class="pm_button"><?php _e('My Profile','profile-grid');?></a></div>
		   <div class="pm-center-button pm-difl pm-pad10"><a href="<?php echo wp_logout_url( $pmrequests->profile_magic_get_frontend_url('pm_user_login_page','')); ?>" class="pm_button"><?php _e('Logout','profile-grid');?></a></div>
	   </div>
       </div>
       </div>
	 <?php
else:
?>

<div class="pmagic">   
<!-----Form Starts----->
  <form class="pmagic-form pm-dbfl" method="post" action="" id="multipage" name="pm_regform_<?php echo $gid; ?>" onsubmit="return profile_magic_frontend_validation(this)" enctype="multipart/form-data">

   <?php
  $html_creator->get_custom_fields_html_singlepage($gid,$fields);
 // if($pmrequests->profile_magic_show_captcha('pm_enable_recaptcha_in_reg'))
 // $html_creator->pm_get_captcha_html();
  ?>
    <div class="pm-full-width-container pm-dbfl">
    <input type="hidden" name="reg_form_submit" value="Submit" />
    <input type="submit" name="reg_form_submit" class="submit action-button" value="<?php _e('Submit','profile-grid');?>" />
    <div class="all_errors" style="display:none;"></div>
     <?php if($paymentpage>0):?>
    <input type="hidden" name="action" value="process" />
    <input type="hidden" name="cmd" value="_cart" /> 
    <input type="hidden" name="invoice" value="<?php echo date("His").rand(1234, 9632); ?>" />
    <?php endif; ?>
    
    </div>
   
  </form>
</div>
<?php endif;?>