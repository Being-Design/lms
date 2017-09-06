<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/admin
 * @author     Your Name <email@example.com>
 */
class Profile_Magic_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $profile_magic    The ID of this plugin.
	 */
	private $profile_magic;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $profile_magic       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $profile_magic, $version ) {

		$this->profile_magic = $profile_magic;
		$this->version = $version;
		$this->pm_theme_path = plugin_dir_path( __FILE__ ) . '../public/partials/themes/';
                $theme_path = get_template_directory();
                $override_template = $theme_path . "/profilegrid-user-profiles-groups-and-communities/themes/";
                $this->pm_theme_path_in_wptheme = $override_template;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
        
        public function activate_sitewide_plugins($blog_id)
        {
            // Switch to new website
            $dbhandler = new PM_DBhandler;
            switch_to_blog( $blog_id );
            // Activate
            foreach( array_keys( get_site_option( 'active_sitewide_plugins' ) ) as $plugin ) {
                do_action( 'activate_'  . $plugin, false );
                do_action( 'activate'   . '_plugin', $plugin, false );
            }
            // Restore current website 
            restore_current_blog();
        }
        
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Profile_Magic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Profile_Magic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 global $wp_scripts;
		// tell WordPress to load jQuery UI tabs
		wp_enqueue_script('jquery-ui-tabs');
		// get registered script object for jquery-ui
		$ui = $wp_scripts->query('jquery-ui-core');
		// tell WordPress to load the Smoothness theme from Google CDN
		$protocol = is_ssl() ? 'https' : 'http';
		$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
		$screen = get_current_screen();
		//print_r($screen->base);die;
		if(isset($screen) && $screen->base!='admin_page_pm_profile_fields' && $screen->base!='admin_page_pm_profile_view')
		wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
		wp_enqueue_style( $this->profile_magic, plugin_dir_url( __FILE__ ) . 'css/profile-magic-admin.css', array(), $this->version, 'all' );
		//wp_enqueue_style( 'pm-joyride', plugin_dir_url( __FILE__ ) . 'css/joyride.css', array(), $this->version, 'all' );
                wp_enqueue_style( 'pm-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all' );
		//wp_enqueue_style( 'pm-user-manager', plugin_dir_url( __FILE__ ) . 'css/pm-user-manager.css', array(), $this->version, 'all' );
		wp_enqueue_style('thickbox');
		wp_register_style('pm_googleFonts', 'https://fonts.googleapis.com/css?family=Titillium+Web:400,600');
                wp_enqueue_style( 'pm_googleFonts');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Profile_Magic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Profile_Magic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                $dbhandler = new PM_DBhandler;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_Script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('media-upload');
                wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_media();
		wp_enqueue_script( $this->profile_magic, plugin_dir_url( __FILE__ ) . 'js/profile-magic-admin.js', array( 'jquery' ), $this->version, false );
                //wp_enqueue_script( 'pm-joyride', plugin_dir_url( __FILE__ ) . 'js/joyride.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->profile_magic, 'pm_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php')) );
                $error = array();
		$error['valid_email'] = __('Please enter a valid e-mail address.','profile-grid');
		$error['valid_number'] = __('Please enter a valid number.','profile-grid');
		$error['valid_date'] = __('Please enter a valid date(yyyy-mm-dd format).','profile-grid');
		$error['required_field'] = __('This is a required field.','profile-grid');
		$error['file_type'] = __('This file type is not allowed.','profile-grid');
		$error['short_password'] = __('Your password should be at least 7 characters long.','profile-grid');
		$error['pass_not_match'] = __('Password and confirm password do not match.','profile-grid');
		$error['user_exist'] = __('Sorry, username already exist.','profile-grid');
		$error['email_exist'] = __('Sorry, email already exist.','profile-grid');
                $error['change_group'] = __('You are changing the group of this user. All data associated with profile fields of old group will be hidden and the user will have to edit and fill profile fields associated with the new group. Do you wish to continue?','profile-grid');
		$error['allow_file_ext'] = $dbhandler->get_global_option_value('pm_allow_file_types','jpg|jpeg|png|gif');	
		wp_localize_script( $this->profile_magic, 'pm_error_object',$error);

	}
	
	
	public function profile_magic_admin_menu()
	{
		add_menu_page("ProfileGrid","ProfileGrid","manage_options","pm_manage_groups",array( $this, 'pm_manage_groups' ),'dashicons-groups',28.123);
		add_submenu_page("","New Group","New Group","manage_options","pm_add_group",array( $this, 'pm_add_group' ));
		add_submenu_page("","Profile Fields","Profile Fields","manage_options","pm_profile_fields",array( $this, 'pm_profile_fields' ));
		add_submenu_page("","New Field","New Field","manage_options","pm_add_field",array( $this, 'pm_add_field' ));
		add_submenu_page("","New Section","New Section","manage_options","pm_add_section",array( $this, 'pm_add_section' ));
		add_submenu_page("pm_manage_groups","Users Profiles","Users Profiles","manage_options","pm_user_manager",array( $this, 'pm_user_manager' ));
		add_submenu_page("","Profile View","Profile View","manage_options","pm_profile_view",array( $this, 'pm_profile_view' ));
		add_submenu_page("","Edit User","Edit User","manage_options","pm_user_edit",array( $this, 'pm_user_edit' ));
		add_submenu_page("pm_manage_groups","Email Templates","Email Templates","manage_options","pm_email_templates",array( $this, 'pm_email_templates' ));
		add_submenu_page("pm_manage_groups","Add Email Template","Add Email Template","manage_options","pm_add_email_template",array( $this, 'pm_add_email_template' ));
		add_submenu_page("","Email Preview","Email Preview","manage_options","pm_email_preview",array($this,'pm_email_preview'));
		add_submenu_page("","Analytics","Analytics","manage_options","pm_analytics",array( $this, 'pm_analytics' ));
		add_submenu_page("","Membership","Membership","manage_options","pm_membership",array( $this, 'pm_membership' ));
		add_submenu_page("pm_manage_groups","Shortcodes","Shortcodes","manage_options","pm_shortcodes",array( $this, 'pm_shortcodes' ));
                add_submenu_page("pm_manage_groups","Global Settings","Global Settings","manage_options","pm_settings",array( $this, 'pm_settings' ));
		add_submenu_page("pm_manage_groups","Extensions","Extensions","manage_options","pm_extensions",array( $this, 'pm_extensions' ));
                add_submenu_page("","General Settings","General Settings","manage_options","pm_general_settings",array( $this, 'pm_general_settings' ));
		add_submenu_page("","Anti Spam Settings","Anti Spam Settings","manage_options","pm_security_settings",array( $this, 'pm_security_settings' ));
		add_submenu_page("","User Accounts Settings","User Accounts Settings","manage_options","pm_user_settings",array( $this, 'pm_user_settings' ));
		add_submenu_page("","User Accounts Settings","User Accounts Settings","manage_options","pm_email_settings",array( $this, 'pm_email_settings' ));
		add_submenu_page("","Third Party Integrations","Third Party Integrations","manage_options","pm_third_party_settings",array( $this, 'pm_third_party_settings' ));
                add_submenu_page("","Payments Settings","Payments Settings","manage_options","pm_payment_settings",array( $this, 'pm_payment_settings' ));
                add_submenu_page("","Tools","Tools","manage_options","pm_tools",array( $this, 'pm_tools' ));
                add_submenu_page("","Export Users","Export Users","manage_options","pm_export_users",array( $this, 'pm_export_users' ));
                add_submenu_page("","Import Users","Import Users","manage_options","pm_import_users",array( $this, 'pm_import_users' ));
                add_submenu_page("","Blog Settings","Blog Settings","manage_options","pm_blog_settings",array( $this, 'pm_blog_settings' ));
                add_submenu_page("","Message Settings","Message Settings","manage_options","pm_message_settings",array( $this, 'pm_message_settings' ));
                add_submenu_page("","Friends Settings","Friends Settings","manage_options","pm_friend_settings",array( $this, 'pm_friend_settings' ));
        }
	
        public function pm_message_settings()
        {
            include 'partials/message-settings.php';
        }
        
        public function pm_extensions()
        {
            include 'partials/pm_extensions.php';
        }
        
        public function pm_friend_settings()
	{
            include 'partials/friends-settings.php';	
	}
        
        public function pm_tools()
        {
                include 'partials/pm-tools.php';
        }
        
        public function pm_payment_settings()
	{
		include 'partials/payment-settings.php';	
	}
        
        public function pm_blog_settings()
        {
            include 'partials/blog-settings.php';
        }
        
        public function pm_export_users()
        {
                include 'partials/pm-export-users.php';
        }
        
        public function pm_import_users()
        {
                include 'partials/pm-import-users.php';
        }
        
        public function pm_profile_magic_add_group_option($gid,$group_options)
	{
		include 'partials/profile-magic-group-option.php';	
	}
        
        public function pm_profile_magic_add_option_setting_page()
	{
		include 'partials/profile-magic-paypal-admin-display.php';	
	}
        
	public function pm_add_email_template()
	{
		include 'partials/email-template.php';
                $this->pg_get_footer_banner();
	}
	
        public function pm_shortcodes()
        {
            include 'partials/shortcode.php';
            $this->pg_get_footer_banner();
        }
	public function pm_email_templates()
	{
		include 'partials/email-templates-list.php';
                $this->pg_get_footer_banner();
	}
	
	public function pm_email_preview()
	{
		include 'partials/email-preview.php';
	}
	
	public function pm_template_preview_button()
	{	
		echo '<a href="admin.php?page=pm_email_preview&TB_iframe=false&width=600&height=550inlineId=wpbody" class="thickbox" onClick="return preview()">Preview</a>';
		
	}
	
	public function pm_manage_groups()
	{
		include 'partials/manage-groups.php';
	}
	
	public function pm_add_group()
	{
		include 'partials/add-group.php';
	}
	
	public function pm_add_field()
	{
		include 'partials/add-field.php';	
	}
	
	public function pm_add_section()
	{
		include 'partials/add-section.php';	
	}
	
	public function pm_profile_fields()
	{
		include 'partials/manage-fields.php';	
	}
	
	public function pm_user_manager()
	{
		include 'partials/user-manager.php';	
                $this->pg_get_footer_banner();
	}
	
	public function pm_profile_view()
	{
		include 'partials/user-profile.php';	
	}
	
	public function pm_third_party_settings()
	{
		include 'partials/thirdparty-settings.php';	
	}
	
	public function pm_email_settings()
	{
		include 'partials/email-settings.php'; 
	}
	
	public function pm_user_settings()
	{
		include 'partials/user-settings.php';	
	}
	
	public function pm_general_settings()
	{
		include 'partials/general-settings.php';
		
	}
	
	public function pm_security_settings()
	{
		include 'partials/security-settings.php';
	}
	
	public function pm_settings()
	{
		include 'partials/pm-settings.php';
                $this->pg_get_footer_banner();
	}
	
	
	
	
	public function profile_magic_set_field_order()
	{
		include 'partials/set-fields-order.php';
		die;
	}
	
	public function profile_magic_set_section_order()
	{
                $dbhandler = new PM_DBhandler;
		$textdomain = $this->profile_magic;
		$path =  plugin_dir_url(__FILE__); 
		$identifier = 'SECTION';
		$list_order = filter_input(INPUT_POST, 'list_order');
		if(isset($list_order))
		{
			$list = explode(',' , $list_order);
			$i = 1 ;
			foreach($list as $id) {
				$dbhandler->update_row($identifier,'id',$id,array('ordering' => $i),array('%d'),'%d');	
				
				$i++;
			}
		}	
		die;
	}
	
	public function profile_magic_section_dropdown()
	{
		$textdomain = $this->profile_magic;
		$path =  plugin_dir_url(__FILE__);
		$gid = filter_input(INPUT_POST, 'gid');
                $dbhandler = new PM_DBhandler;
		$sections = $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid));
		foreach($sections as $section)
	    {?>
            <option value="<?php echo $section->id;?>" <?php if(!empty($row))selected($row->associate_section,$section->id);?>><?php echo $section->section_name; ?></option>
         <?php 
		}
		die;	
	}
	
	public function profile_magic_template_preview()
	{
		$path =  plugin_dir_path( __FILE__ );
		$filepath = $path.'partials/email-preview.php';
		file_put_contents($filepath, stripslashes($_POST['html']));
		$url = admin_url( 'admin.php?page=pm_email_preview');
		die;
	}
	
	public function profile_magic_check_smtp_connection()
	{
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
		$identifier = 'SETTINGS';
		$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
		$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
		if($post!=false)
		{
			if(isset($post['pm_smtp_password']) && $post['pm_smtp_password']!='') 
			{
				//$post['pm_smtp_password'] = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$post['pm_smtp_password']);
                                $post['pm_smtp_password'] = $post['pm_smtp_password'];
			}
			else
			{
				unset($post['pm_smtp_password']);
			}
			foreach($post as $key=>$value)
			{
				$dbhandler->update_global_option_value($key,$value);
			}
		}
		$dbhandler->update_global_option_value('pm_enable_smtp',1);
		$to = $dbhandler->get_global_option_value('pm_smtp_test_email_address');
                //echo $to;die;
		echo wp_mail( $to,'Test SMTP Connection','Test');die;
                
	}
	
	public function profile_magic_check_user_exist()
	{
                $pmrequests = new PM_request;
		switch($_POST['type'])
		{
			case 'validateUserName': 
				 if($pmrequests->profile_magic_check_username_exist($_POST['userdata']))
                                 {
                                     echo 'true';
                                 }
                                 else
                                 {
                                     echo 'false';
                                 }
                                     
				break;
			case 'validateUserEmail': 
				if($pmrequests->profile_magic_check_user_email_exist($_POST['userdata']))
                                {
                                    echo 'true';
                                }
                                else
                                {
                                    echo 'false';
                                }
				break;
		}		
     	die;
	}
	
	public function pm_fields_list_for_email()
	{
                $dbhandler = new PM_DBhandler;
		$exclude = "and field_type not in('file','user_avatar','heading','paragraph','confirm_pass','user_pass','divider','spacing','birth_date')";
		$groups =  $dbhandler->get_all_result('GROUPS');
		echo '<select name="pm_field_list" class="pm_field_list" onchange="pm_insert_field_in_email(this.value)">';
		echo '<option>'.__('Select A Field','profile-grid').'</option>';
		echo '<optgroup label="'.__('Comman Fields','profile-grid').'" >';
		echo '<option value="{{user_login}}">'.__('User Name','profile-grid').'</option>';
		echo '<option value="{{user_pass}}">'.__('User Password','profile-grid').'</option>';
               // echo '<option value="{{pm_activation_code}}">'.__('User Activation Link','profile-grid').'</option>';
		echo '</optgroup>';
		if(isset($groups)):
		foreach($groups as $group)
		{
			$fields =  $dbhandler->get_all_result('FIELDS','*',array('associate_group'=>$group->id),'results',0,false,'ordering',false,$exclude);
			echo '<optgroup label="'.$group->group_name.'" >';
			if(isset($fields)):
			foreach($fields as $field)
			{
				echo '<option value="{{'.$field->field_key.'}}">'.$field->field_name.'</option>';
			}
			endif;
			
			echo '</optgroup>';
		}
		echo '</select>';
		endif;
			
	}
	
	public function profile_magic_show_user_fields($user)
	{
		//print_r($user);
                $dbhandler = new PM_DBhandler;
                $pm_customfields = new PM_Custom_Fields;
                $pmrequests = new PM_request;
		$exclude = "and field_type not in('first_name','last_name','user_name','user_email','user_url','user_pass','confirm_pass','description','file','user_avatar','heading','paragraph')";
		if(is_object($user))
		{
			$gid = $pmrequests->profile_magic_get_user_field_value($user->ID,'pm_group');
		}
		else
		{
			$gid = 0;	
		}
		$fields =  $dbhandler->get_all_result('FIELDS','*',array('associate_group'=>$gid),'results',0,false,'ordering',false,$exclude);
		
		$col = $dbhandler->get_global_option_value('pm_reg_form_cols',1);
		
		$profile_pic = (is_object($user)) ? get_user_meta($user->ID, 'pm_user_avatar', true): false;
		$groups =  $dbhandler->get_all_result('GROUPS','id,group_name');
   ?>
        <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
			<div class="pm-field-lable">
				<label for="pm_field_37"><?php _e('Profile Picture','profile-grid');?></label>
			  </div>
			  <div class="pm-field-input">              
             <input id="pm_user_avatar" type="hidden" name="pm_user_avatar" class="icon_id" value="<?php if(isset($profile_pic)) echo $profile_pic; ?>" />
          <input id="field_icon_button" name="field_icon_button" class="button group_icon_button" type="button" value="<?php _e('Upload Icon','profile-grid');?>" />
				<div class="errortext" style="display:none;"></div>
			  </div>
			</div>
        </div>
        
        <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
			<div class="pm-field-lable">
				<label for="pm_field_37"><?php _e('User Group','profile-grid');?></label>
			  </div>
			  <div class="pm-field-input">              
            <select name="pm_group" id="pm_group" onchange="pm_change_group_notice(this,<?php if(!empty($gid))echo $gid;else 0;?>)">
            <?php
			  foreach($groups as $group)
			  {?>
            <option value="<?php echo $group->id;?>" <?php if(!empty($gid))selected($gid,$group->id);?>><?php echo $group->group_name; ?></option>
            <?php }?>
          </select>
				<div class="errortext" style="display:none;"></div>
			  </div>
			</div>
        </div>
        
        <?php
		
		if($lastRec=count($fields) && $gid!=0)
		{
			echo '<div class="pmrow">';
			$i=0;
			foreach($fields as $field) 
			{
				$value = $pmrequests->profile_magic_get_user_field_value($user->ID,$field->field_key);
				//echo $value;die;
				if( $i!=0 && ($i % $col == 0) && ($i<$lastRec) ) echo '</div><div class="pmrow">';
				$pm_customfields->pm_get_custom_form_fields($field,$value,$this->profile_magic);
				$i++;
			}
			echo '</div>';
		}
                echo '<div class="all_errors" style="display:none;"></div>';
				
	}
	
	public function profile_magic_update_user_fields($user_id )
	{
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
		if ( !current_user_can( 'edit_user', $user_id ))
		return FALSE;
		if(!isset($_POST['reg_form_submit']))
		{
			update_user_meta($user_id, 'pm_user_avatar', $_POST['pm_user_avatar']);
			$exclude = "and field_type not in('first_name','last_name','user_name','user_email','user_url','user_pass','confirm_pass','description','file','user_avatar','heading','paragraph')";
			$gid = $pmrequests->profile_magic_get_user_field_value($user_id,'pm_group');
			$fields =  $dbhandler->get_all_result('FIELDS','*',array('associate_group'=>$gid),'results',0,false,'ordering',false,$exclude);
			$pmrequests->pm_update_user_custom_fields_data($_POST,$_FILES,$_SERVER,$gid,$fields,$user_id);
			update_user_meta($user_id, 'pm_group', $_POST['pm_group']);
		}
	}
        
        public function profile_magic_activate_user_by_email()
        {
            $req = $_GET['user'];
            $pmrequests = new PM_request;
            $req_deco = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$req);
            $user_data = json_decode($req_deco);
            $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));

        if ($user_data->activation_code == get_user_meta($user_data->user_id, 'pm_activation_code', true)) 
        {
            update_user_meta($user_data->user_id,'rm_user_status',0);
            if (!delete_user_meta($user_data->user_id, 'pm_activation_code')) {
                 $redirect_url = add_query_arg( 'errors','ajx_failed_del', $redirect_url );
            }
            else
            {
               $message = __('You have successfully activated the user.','profile-grid');
                $redirect_url = add_query_arg( 'activated','success', $redirect_url );
            }
        }
        else 
        {
             $message = __('Failed to upadte user information.Can not activate user','profile-grid');
             $redirect_url = add_query_arg( 'errors','invalid_code', $redirect_url );
        }
       wp_redirect( $redirect_url );exit;
        die;
        }
        
        public function pm_load_export_fields_dropdown()
        {
            include 'partials/export-fields.php';
            die;
        }
        
        public function pm_upload_csv()
        {
            include 'partials/pm-import-ajax.php';
            die;
        }
        
        public function profile_grid_myme_types($mime_types)
        {
            $mime_types['csv'] = 'text/csv';
            return $mime_types;
        }
        
        public function profile_magic_show_feedback_form()
        {
            $path =  plugin_dir_url(__FILE__);
           ?>
            <div class="pmagic uimagic">
            <div id="pg-deactivate-feedback-dialog-wrapper" class="pg-modal-view" style="display: none">
                <div class="pg-modal-overlay" style="display: none"></div>
                
               <div class="pg-modal-wrap pg-deactivate-feedback"> 
                   <div class="pg-modal-titlebar">
                               <div class="pg-modal-title">ProfileGrid Feedback </div>
                               <div class="pg-modal-close">&times;</div>
                           </div>
                   
                   <form id="pg-deactivate-feedback-dialog-form" method="post">
                       <input type="hidden" name="action" value="pg_deactivate_feedback" />
                   <div class="pg-modal-container">
                <div class="uimrow">
                <div id="pg-deactivate-feedback-dialog-form-caption">If you have a moment, please share why you are deactivating ProfileGrid:</div>
                <div id="pg-deactivate-feedback-dialog-form-body">
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-feature_not_available" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="feature_not_available">
                        <label for="pg-deactivate-feedback-feature_not_available" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f61e;</span>Doesn't have the feature I need</label>
                        <div class="pginput" id="pg_reason_feature_not_available" style="display:none"><input class="pg-feedback-text" type="text" name="pg_reason_feature_not_available" placeholder="Please let us know the missing feature..."></div>
                    </div>
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-feature_not_working" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="feature_not_working" >
                        <label for="pg-deactivate-feedback-feature_not_working" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f615;</span>One of the features didn't worked</label>
                        <div class="pginput" id="pg_reason_feature_not_working" style="display:none"><input class="pg-feedback-text" type="text" name="pg_reason_feature_not_working" placeholder="Please let us know the feature, like 'emails notifications'"></div>
                    </div>
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-found_a_better_plugin" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="found_a_better_plugin" >
                        <label for="pg-deactivate-feedback-found_a_better_plugin" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f60a;</span>Moved to a different plugin</label>
                        <div class="pginput" id="pg_reason_found_a_better_plugin" style="display:none"><input class="pg-feedback-text" type="text" name="pg_reason_found_a_better_plugin" placeholder="Could you please share the plugin's name"></div>
                    </div>
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-plugin_broke_site" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="plugin_broke_site">
                        <label for="pg-deactivate-feedback-plugin_broke_site" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f621;</span>The plugin broke my site</label>
                    </div>
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-plugin_stopped_working" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="plugin_stopped_working">
                        <label for="pg-deactivate-feedback-plugin_stopped_working" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f620;</span>The plugin suddenly stopped working</label>
                    </div>
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-temporary_deactivation" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="temporary_deactivation">
                        <label for="pg-deactivate-feedback-temporary_deactivation" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f60a;</span>It's a temporary deactivation</label>
                    </div>
                    
                    <div class="pg-deactivate-feedback-dialog-input-wrapper">
                        <input id="pg-deactivate-feedback-other" class="pg-deactivate-feedback-dialog-input" type="radio" name="pg_feedback_key" value="other">
                        <label for="pg-deactivate-feedback-other" class="pg-deactivate-feedback-dialog-label"><span class="pg-feedback-emoji">&#x1f610;</span>Other</label>
                        <div class="pginput" id="pg_reason_other"  style="display:none"><input class="pg-feedback-text" type="text" name="pg_reason_other" placeholder="Please share the reason"></div>
                    </div>
                </div>

            </div>
                   </div>
                       
                          <div class="pg-ajax-loader" style="display:none">
                              <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                                <span class="">Loading...</span>
                                </div>
                       
                       <div class="pg-modal-footer uimrow">
                                <input type="button" id="pg-feedback-btn" value="Submit & Deactivate"/>
                                <input type="button" id="pg-feedback-cancel-btn" value="Cancel"/>
                                </div>
                       
                   </form>
               </div>
   
        
</div>
            </div>
            <?php
        }
        
        public function pg_post_feedback(){
        $msg= $_POST['msg'];
        $feedback= $_POST['feedback'];
        $body= '';
        switch($feedback)
        {
            case 'feature_not_available': $body='Feature not available: '; break;
            case 'feature_not_working': $body='Feature not working: '; break;
            case 'found_a_better_plugin': $body='Found a better plugin: '; break;
            case 'plugin_broke_site': $body='Plugin broke my site.'; break;
            case 'plugin_stopped_working': $body='Plugin stopped working'; break;
            case 'temporary_deactivation': return;
            case 'upgrade':  $body='Upgrading to premium '; break;   
            case 'other': $body='Other: '; break;
            default: return;
        }
        $body .= $msg;
        wp_mail('feedback@profilegrid.co','ProfileGrid Uninstallation Feedback',$body);
        die;
    }
    
    public function pg_frontend_group_short_code()
    {
        $pg_function = new Profile_Magic_Basic_Functions($this->profile_magic,$this->version);
        $link = $pg_function->pg_get_extension_shortcode('FRONTEND_GROUP');
        $path =  plugin_dir_url(__FILE__);
        $html = '
            <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle">'.__("Group Creation Form","profile-grid").'</div>
            <div class="pg-scblock"><span class="pg-code">'.$link.'</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="'.$path.'partials/images/sc-12.png"></div>
            <div class="pg-scblock pg-scdesc">'.__("Allow registered users to create new Groups on front end. These Groups behave and work just like regular ProfileGrid groups.","profile-grid").'</div>
            </div>';
        $html = apply_filters( 'pg_filter_frontend_group_shortcode',$html);
        echo $html; 
    }
    
    public function pg_geolocation_short_code()
    {
        $pg_function = new Profile_Magic_Basic_Functions($this->profile_magic,$this->version);
        $link = $pg_function->pg_get_extension_shortcode('GEOLOCATION');
        $path =  plugin_dir_url(__FILE__);
        $html = '
            <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle">'.__("Generate User Map","profile-grid").'</div>
            <div class="pg-scblock"><span class="pg-code">'.$link.'</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="'.$path.'partials/images/sc-11.png"></div>
            <div class="pg-scblock pg-scdesc">'.__("Generate maps showing locations of all users or specific groups using simple shortcodes. Get location data from registration form.","profile-grid").'</div>
            </div>';
        $html = apply_filters( 'pg_filter_geolocation_shortcode',$html);
        echo $html; 
    }
    
    public function pg_get_footer_banner()
    {
         $path =  plugin_dir_url(__FILE__);
         
        ?>
            <div class="pg-footer-banner"><a href="admin.php?page=pm_extensions"><img src="<?php echo $path;?>partials/images/extension_banner.png" /></a></div>
        <?php
    }
}
