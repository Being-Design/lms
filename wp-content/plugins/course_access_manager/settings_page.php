<?php
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	$learndash_plus_email_template_default = learndash_plus_email_template_default();
	$learndash_plus_email_subject_default = learndash_plus_email_subject_default();
	
	$learndash_plus_settings = get_option( 'learndash_plus_settings' );
	$redirect_url =  isset($learndash_plus_settings['redirect_url'])? $learndash_plus_settings['redirect_url']:"";
	$login_redirect_url =  isset($learndash_plus_settings['login_redirect_url'])? $learndash_plus_settings['login_redirect_url']:"";
	$learndash_plus_email_template =  isset($learndash_plus_settings['learndash_plus_email_template'])? $learndash_plus_settings['learndash_plus_email_template']:$learndash_plus_email_template_default;
	$learndash_plus_email_subject =  isset($learndash_plus_settings['learndash_plus_email_subject'])? $learndash_plus_settings['learndash_plus_email_subject']:$learndash_plus_email_subject_default;
		
	if( isset($_POST[ "update_LearnDashMembershipSettings" ]) ) {
        // Read their posted value
		
        $redirect_url =  $_POST['learndash_plus_redirect_url'];
		$login_redirect_url = $_POST['learndash_plus_login_redirect_url'];
		$learndash_plus_email_template = $_POST['learndash_plus_email_template'];
		$learndash_plus_email_subject = $_POST['learndash_plus_email_subject'];
		
		$learndash_plus_settings =  array(
										'redirect_url' => $redirect_url,
										'login_redirect_url' => $login_redirect_url,
										'learndash_plus_email_template' => $learndash_plus_email_template,
										'learndash_plus_email_subject' => $learndash_plus_email_subject
										);
		// Save the posted value in the database
        update_option( 'learndash_plus_settings', $learndash_plus_settings);
        // Put an settings updated message on the screen

	?>
	<div class="updated"><p><strong><?php _e('Settings saved.', 'learndash-plus' ); ?></strong></p></div>
	<?php
    }
	
	
?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2><?php _e('Course Access Settings', 'learndash-plus') ?></h2>
<h3><?php _e('Access Denied Redirect Url', 'learndash-plus') ?><small class="description"> (<?php _e('URL to redirect to when the content is restricted to the user', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_redirect_url" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$redirect_url) ?>" />
<h3><?php _e('Login Redirect Url', 'learndash-plus') ?><small class="description"> (<?php _e('URL to redirect to on login', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_login_redirect_url" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$login_redirect_url) ?>" />
<h3><?php _e('Email Subject', 'learndash-plus') ?><small class="description"></small> :</h3>
<input name="learndash_plus_email_subject" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$learndash_plus_email_subject) ?>" />
<h3><?php _e('Email Template', 'learndash-plus') ?><small class="description"> (<?php _e('This email is sent when a users access level changes', 'learndash-plus') ?>)</small> :</h3>
<textarea name="learndash_plus_email_template" style="min-width:35%;height:100px" ><?php echo apply_filters('format_to_edit',$learndash_plus_email_template) ?></textarea><br>
<small><?php _e('Tags', 'learndash-plus') ?>:<br>
<i>[name]</i>: <?php _e("User's Name", 'learndash-plus') ?><br>
<i>[access-level]</i>: <?php _e('Access Level', 'learndash-plus') ?><br>
</small>
<h3><?php _e('Cron Url', 'learndash-plus') ?>:<small class="description"> (<?php _e('Setup this url as a cron job to perform periodic auto upgrades. Without it auto upgrades will happen on user login.', 'learndash-plus') ?>)</small> :</h3>
<?php echo get_bloginfo('wpurl')."/?learndash_email_cron=1"; ?>
<div class="submit">
<input type="submit" name="update_LearnDashMembershipSettings" value="<?php _e('Update Settings', 'learndash-plus') ?>" /></div>
</form>
