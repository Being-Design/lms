<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$identifier = 'SETTINGS';
if(filter_input(INPUT_POST,'submit_settings'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_general_settings' ) ) die( 'Failed security check' );
	$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if($post!=false)
	{
		if(!isset($post['pm_allow_multiple_attachments'])) $post['pm_allow_multiple_attachments'] = 0;
		if(!isset($post['pm_save_ip_browser_info'])) $post['pm_save_ip_browser_info'] = 0;
		foreach($post as $key=>$value)
		{
			$dbhandler->update_global_option_value($key,$value);
		}
	}
	
	wp_redirect('admin.php?page=pm_settings');exit;
}
?>

<div class="uimagic">
  <form name="pm_general_settings" id="pm_general_settings" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <div class="uimheader">
        <?php _e( 'General','profile-grid' ); ?>
      </div>
     
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e('Template','profile-grid');?>
        </div>
        <div class="uiminput">
          <select name="pm_style" id="pm_style">
          <?php
          $themename = $pmrequests->profile_magic_get_pm_theme_name();
            foreach($themename as $dirname) {
                ?>
            <option value="<?php echo $dirname;?>" <?php selected($dbhandler->get_global_option_value('pm_style'),$dirname); ?>><?php echo $dirname;?></option>
            <?php } ?>        
  	</select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('You can create new templates by copying and renaming "default" folder (&#128194;) inside "[plugin root]/public/partials/themes" to "[your current theme directory]/profilegrid-user-profiles-groups-and-communities/themes".','profile-grid');?></div>
      </div>
        
        <div class="uimrow">
        <div class="uimfield">
          <?php _e('Theme','profile-grid');?>
        </div>
        <div class="uiminput">
          <select name="pm_theme_type" id="pm_theme_type">
            <option value="light" <?php selected($dbhandler->get_global_option_value('pm_theme_type','light'),'light'); ?>><?php _e('Light','profile-grid');?></option>
            <option value="dark" <?php selected($dbhandler->get_global_option_value('pm_theme_type','light'),'dark'); ?>><?php _e('Dark','profile-grid');?></option>
        </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('While Light will fit most of the themes, choose Dark if your WordPress theme has black or dark background.','profile-grid');?></div>
      </div>
     
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Allow Multiple Attachments','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="pm_allow_multiple_attachments" id="pm_allow_multiple_attachments" type="checkbox" class="pm_toggle" value="1" style="display:none;" <?php checked($dbhandler->get_global_option_value('pm_allow_multiple_attachments'),'1'); ?>   />
          <label for="pm_allow_multiple_attachments"></label>
        </div>
          <div class="uimnote"><?php _e('Allow users to attach more than one file to file upload fields.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Default WP Registration Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   $default_registration_url = $dbhandler->get_global_option_value('pm_default_regisration_page','0');
			$args = array(
				'depth'            => 0,
				'child_of'         => 0,
				'selected'         => $default_registration_url,
				'echo'             => 1,
				'show_option_none'      => __('Select Page','profile-grid'),
    			'option_none_value'     => 0, 
				'name'             => 'pm_default_regisration_page'); 
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Redirect all registration links to this page on your site. This helps in hiding the default WP registration form.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'After Login Redirect User to:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   $pm_redirect_after_login = $dbhandler->get_global_option_value('pm_redirect_after_login','0');
			$args = array(
				'depth'            => 0,
				'child_of'         => 0,
				'selected'         => $pm_redirect_after_login,
				'echo'             => 1,
				'show_option_none'      => __('Select Page','profile-grid'),
    			'option_none_value'     => 0, 
				'name'             => 'pm_redirect_after_login'); 
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('When the user logs in, he/ she will be redirected to this page. This is usually a member specific area, like user profile page.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'All Groups Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   $pm_groups_page = $dbhandler->get_global_option_value('pm_groups_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_groups_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_groups_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('All Groups page displays all the groups on your site beautifully on a single page. A great way to allow visitors to decide and sign up for relevant group.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Registration Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   $pm_registration_page = $dbhandler->get_global_option_value('pm_registration_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_registration_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_registration_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('This will add Username and Password fields to this form.','profile-grid');?> </div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Profile Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   $pm_user_profile_page = $dbhandler->get_global_option_value('pm_user_profile_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_user_profile_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_user_profile_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Default member profile page. Make sure it has profile shortcode pasted inside it.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Login Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   		$pm_user_login_page = $dbhandler->get_global_option_value('pm_user_login_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_user_login_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_user_login_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('The page where users can log in. It should have the login shortcode pasted inside.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Password Recovery Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   		$pm_forget_password_page = $dbhandler->get_global_option_value('pm_forget_password_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_forget_password_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_forget_password_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('This page will allows users to start password recovery process.','profile-grid');?></div>
      </div>
      
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Password Reset Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   		$pm_password_reset_page = $dbhandler->get_global_option_value('pm_password_reset_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_password_reset_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_password_reset_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('After successfully starting password recovery process, this page is the final step to reset lost passwords.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Group Page:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php 
	   		$pm_group_page = $dbhandler->get_global_option_value('pm_group_page','0');
			$args = array('depth'=>0,'child_of'=>0,'selected'=> $pm_group_page,'echo'=>1,'show_option_none'=>__('Select Page','profile-grid'),'option_none_value'=>0,
			'name'=>'pm_group_page');
			wp_dropdown_pages($args); 
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Single group page where users can see group details and other members of the group.','profile-grid');?></div>
      </div>
     
      <div class="buttonarea"> <a href="admin.php?page=pm_settings">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <?php wp_nonce_field('save_general_settings'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_settings" id="submit_settings" />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>