<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Profile_Magic
 * @subpackage Profile_Magic/includes
 * @author     Your Name <email@example.com>
 */
class Profile_Magic_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
    
        
        public function activate()
        {
               global $wpdb;
               if ( is_multisite()) {
                   // Get all blogs in the network and activate plugin on each one
                   $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                   foreach ( $blog_ids as $blog_id ) {
                       switch_to_blog( $blog_id );
                       $this->create_table();
                       restore_current_blog();
                   }
               } else {
                  $this->create_table();
               }
        }
         
	public function create_table() 
	{
            global $wpdb;
            require_once( ABSPATH . 'wp-includes/wp-db.php');
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->get_db_table_name('GROUPS');
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `group_name` varchar(255) NOT NULL,
		  `group_desc` longtext DEFAULT NULL,
		  `group_icon` int(11) DEFAULT NULL,
		  `is_group_limit` int(11) NOT NULL DEFAULT '0',
		  `group_limit` int(11) NOT NULL DEFAULT '0',
		  `group_limit_message` longtext DEFAULT NULL,
		  `associate_role` varchar(255) NOT NULL,
		  `is_group_leader` int(11) NOT NULL DEFAULT '0',
		  `leader_username` varchar(255) NOT NULL,
		  `leader_rights` longtext,
		  `group_slug` varchar(255),
		  `show_success_message` int(11) NOT NULL DEFAULT '0',
		  `success_message` longtext DEFAULT NULL,
		  `group_options` longtext,
		  PRIMARY KEY (`id`)
		)$charset_collate;";
          dbDelta($sql);
			
			$table_name = $this->get_db_table_name('FIELDS');
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		  `field_id` int(11) NOT NULL AUTO_INCREMENT,
		  `field_name` varchar(255) NOT NULL,
		  `field_desc` longtext DEFAULT NULL,
		  `field_type` varchar(255) NOT NULL,
		  `field_options` longtext DEFAULT NULL,
		  `field_icon` int(11) DEFAULT NULL,
		  `associate_group` int(11) NOT NULL DEFAULT '0',
		  `associate_section` int(11) NOT NULL DEFAULT '0',
		  `show_in_signup_form` int(11) NOT NULL DEFAULT '0',
		  `is_required` int(11) NOT NULL DEFAULT '0',
		  `is_editable` int(11) NOT NULL DEFAULT '0',
		  `display_on_profile` int(11) NOT NULL DEFAULT '0',
		  `display_on_group` int(11) NOT NULL DEFAULT '0',
		  `visibility` int(11) NOT NULL DEFAULT '0',
		  `ordering` int(11) NOT NULL,
		  `field_key` varchar(255) NOT NULL,
		  PRIMARY KEY (`field_id`)
		)$charset_collate;";
            dbDelta($sql);
			
			$table_name = $this->get_db_table_name('PAYPAL_LOG');
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `txn_id` varchar(600) NOT NULL,
			  `log` longtext NOT NULL,
			  `posted_date` datetime NOT NULL,
                          `gid` int(11) NOT NULL,
                          `status` varchar(255) NOT NULL,
                          `invoice` varchar(255) NOT NULL,
                          `amount` int(11) NOT NULL,
                          `currency` varchar(255) NOT NULL,
                          `pay_processor` varchar(255) NOT NULL,
                          `pay_type` varchar(255) NOT NULL,
                          `uid` int(11) NOT NULL,
			  PRIMARY KEY (`id`))$charset_collate;";
			dbDelta( $sql );
			
			$table_name = $this->get_db_table_name('SECTION');
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gid` int(11) NOT NULL,
			  `section_name` varchar(600) NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `section_options` longtext DEFAULT NULL,
			  PRIMARY KEY (`id`))$charset_collate;";
			dbDelta( $sql );
			
			$table_name = $this->get_db_table_name('EMAIL_TMPL');
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tmpl_name` varchar(600) NOT NULL,
			  `email_subject` varchar(255) NOT NULL,
			  `email_body` longtext DEFAULT NULL,
			  PRIMARY KEY (`id`))$charset_collate;";
			dbDelta( $sql );
                        
                        $table_name = $this->get_db_table_name('FRIENDS');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `user1` int(11) NOT NULL,
                            `user2` int(11) NOT NULL,
                            `created_date` datetime NOT NULL,
                            `action_date` datetime NOT NULL,
                            `status` int(11) NOT NULL,
                            PRIMARY KEY (`id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        $table_name = $this->get_db_table_name('MSG_THREADS');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `t_id` int(11) NOT NULL AUTO_INCREMENT,
                                `s_id` int(11) NOT NULL,
                                `r_id` int(11) NOT NULL,
                                `timestamp` datetime NOT NULL ,
                                `title` varchar(255),
                                `status` int(11),
                                `thread_desc` longtext DEFAULT NULL,
                                PRIMARY KEY (`t_id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        
                        $table_name = $this->get_db_table_name('MSG_CONVERSATION');
                        $foreign_key_table = $this->get_db_table_name('MSG_THREADS');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `m_id` int(11) NOT NULL AUTO_INCREMENT,
                                `s_id` int(11) NOT NULL,
                                `t_id` int(11) NOT NULL,
                                `content` longtext DEFAULT NULL,
                                `timestamp` datetime NOT NULL ,
                                `subject` varchar(255),
                                `status` int(11),
                                `msg_desc` longtext DEFAULT NULL,
                                 FOREIGN KEY (`t_id`) REFERENCES $foreign_key_table(`t_id`),
                                PRIMARY KEY (`m_id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        $table_name = $this->get_db_table_name('NOTIFICATION');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `type` varchar(255) NOT NULL,
                            `sid` int(11) NOT NULL,
                            `rid` int(11) NOT NULL,
                            `timestamp` datetime NOT NULL,
                            `description` longtext DEFAULT NULL,
                            `status` int(11) NOT NULL,
                            `meta` longtext DEFAULT NULL,
                            PRIMARY KEY (`id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        
		$this->create_pages();
			
	}
        
        public function upgrade_db()
        {
            global $wpdb;
            require_once( ABSPATH . 'wp-includes/wp-db.php');
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $dbhandler = new PM_DBhandler;
           
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->get_db_table_name('FRIENDS');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `user1` int(11) NOT NULL,
                            `user2` int(11) NOT NULL,
                            `created_date` datetime NOT NULL ,
                            `action_date` datetime NOT NULL,
                            `status` int(11) NOT NULL,
                            PRIMARY KEY (`id`))$charset_collate;";
                        dbDelta( $sql );
                        
            $table_name = $this->get_db_table_name('MSG_THREADS');
                         $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `t_id` int(11) NOT NULL AUTO_INCREMENT,
                                `s_id` int(11) NOT NULL,
                                `r_id` int(11) NOT NULL,
                                `timestamp` datetime NOT NULL ,
                                `title` varchar(255),
                                `status` int(11),
                                `thread_desc` longtext DEFAULT NULL,
                                PRIMARY KEY (`t_id`))$charset_collate;";
                        dbDelta( $sql );
        
            $table_name = $this->get_db_table_name('MSG_CONVERSATION');
            $foreign_key_table = $this->get_db_table_name('MSG_THREADS');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `m_id` int(11) NOT NULL AUTO_INCREMENT,
                                `s_id` int(11) NOT NULL,
                                `t_id` int(11) NOT NULL,
                                `content` longtext DEFAULT NULL,
                                `timestamp` datetime NOT NULL ,
                                `subject` varchar(255),
                                `status` int(11),
                                `msg_desc` longtext DEFAULT NULL,
                                FOREIGN KEY (`t_id`) REFERENCES $foreign_key_table(`t_id`),
                                PRIMARY KEY (`m_id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        $table_name = $this->get_db_table_name('NOTIFICATION');
                        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `type` varchar(255) NOT NULL,
                            `sid` int(11) NOT NULL,
                            `rid` int(11) NOT NULL,
                            `timestamp` datetime NOT NULL,
                            `description` longtext DEFAULT NULL,
                            `status` int(11) NOT NULL,
                            `meta` longtext DEFAULT NULL,
                            PRIMARY KEY (`id`))$charset_collate;";
                        dbDelta( $sql );
                        
                        $paypallog = $dbhandler->get_row('PAYPAL_LOG', '1');
                        //print_r($paypallog);die;
                        $table_name = $this->get_db_table_name('PAYPAL_LOG');
                        //Add column if not present.
                        if(!isset($paypallog->gid))
                        {
           
                            $wpdb->query("ALTER TABLE $table_name 
                                ADD COLUMN `gid` int(11) NOT NULL AFTER `posted_date`,
                                ADD COLUMN `status` VARCHAR(255) NOT NULL AFTER `gid`,
                                ADD COLUMN `invoice` VARCHAR(255) NOT NULL AFTER `status`,
                                ADD COLUMN `amount` int(11) NOT NULL AFTER `invoice`,
                                ADD COLUMN `currency` VARCHAR(255) NOT NULL AFTER `amount`,
                                ADD COLUMN `pay_processor` VARCHAR(255) NOT NULL AFTER `currency`,
                                ADD COLUMN `pay_type` VARCHAR(255) NOT NULL AFTER `pay_processor`,
                                ADD COLUMN `uid` VARCHAR(255) NOT NULL AFTER `pay_type`
                            ");
                            
                            $this->upgrade_paypal_tbl();
                                    
                        }
                        
                        update_option( "progrid_db_version",PROGRID_DB_VERSION );
        }
        
        public function upgrade_paypal_tbl()
        {
             $dbhandler = new PM_DBhandler;
             
             $results = $dbhandler->get_all_result('PAYPAL_LOG');
             if(!empty($results)):
                foreach($results as $result)
                {
                    $log = maybe_unserialize($result->log);
                    
                    $data = array();
                    $gid = get_user_meta($log['custom'],'pm_group',true);
                    $data['gid'] = $gid;
                    $data['status'] = $log['payment_status'];
                    $data['invoice'] = $log['invoice'];
                    $data['amount'] = $log['mc_gross'];
                    $data['currency'] = $log['mc_currency'];
                    $data['pay_processor'] = 'paypal';
                    $data['pay_type'] = 'one_time';
                    $data['uid'] = $log['custom'];
                    $dbhandler->update_row('PAYPAL_LOG','id',$result->id,$data);
                    unset($data);
                    
                }
                 
             endif;
            
        }
        
	public function get_db_table_name($identifier)
	{
		global $wpdb;
		$plugin_prefix = $wpdb->prefix.'promag_';
		
		switch ($identifier)
		{
			case 'GROUPS':
				$table_name= $plugin_prefix."groups";
				break;
			
			case 'FIELDS':
				$table_name= $plugin_prefix."fields";
				break;
			case 'PAYPAL_LOG':
				$table_name = $plugin_prefix."paypal_log";
				break;
			case 'SECTION':
				$table_name = $plugin_prefix."sections";
				break;
			case 'EMAIL_TMPL':
				$table_name = $plugin_prefix."email_templates";
				break;
                        case 'FRIENDS':
                                $table_name = $plugin_prefix."friends";
                                break;
                        case 'MSG_THREADS':
                                $table_name = $plugin_prefix."msg_threads";
                                break;
                        case 'MSG_CONVERSATION':
                                $table_name = $plugin_prefix."msg_conversation";
                                break;
                        case 'NOTIFICATION':
                                $table_name = $plugin_prefix."notification";
                                break;
                            
			default:
				$classname = "PM_Helper_$identifier";
				if (class_exists($classname)) 
				{
                                        $externalclass = new $classname;
   					$table_name = $externalclass->get_db_table_name($identifier);
				}
				else
				{
					return false;
				}
		}
		return $table_name;
	}

	public function create_pages()
	{
		$dbhandler = new PM_DBhandler;
		$pages['PM_Registration'] = array('post_type' => 'page','post_title' => 'Registration','post_status' => 'publish','post_name' => 'pm_registration','post_content' => '[PM_Registration id="1"]');
		$pages['PM_Group'] = array('post_type' => 'page','post_title' => 'Group','post_status' => 'publish','post_name' => 'pm_group','post_content' => '[PM_Group id="1"]');
		$pages['PM_Groups'] = array('post_type' => 'page','post_title' => 'All Groups','post_status' => 'publish','post_name' => 'pm_groups','post_content' => '[PM_Groups]');
		$pages['PM_Login'] = array('post_type' => 'page','post_title' => 'Login','post_status' => 'publish','post_name' => 'pm_login','post_content' => '[PM_Login]');
		$pages['PM_Profile'] = array('post_type' => 'page','post_title' => 'My Profile','post_status' => 'publish','post_name' => 'pm_profile','post_content' => '[PM_Profile]');
		$pages['PM_Forget_Password'] = array('post_type' => 'page','post_title' => 'Forgot Password','post_status' => 'publish','post_name' => 'pm_forgot_password','post_content' => '[PM_Forget_Password]');
		$pages['PM_Password_Reset_Form'] = array('post_type' => 'page','post_title' => 'Reset Password Form','post_status' => 'publish','post_name' => 'pm_reset_password_form','post_content' => '[PM_Password_Reset_Form]');
		// The Query
		foreach($pages as $key=>$page)
		{
			$string = '['.$key;
			$my_query = new WP_Query( array('post_type' => 'any','post_status' => 'publish','s' => $string,'fields'=>'ids'));
			if(empty($my_query->posts))
			{
				$page_id[$key] = wp_insert_post($page);
			}
			else
			{
				$page_id[$key] = $my_query->posts[0];	
			}
		}
		//print_r($page_id);die;
		foreach($page_id as $key=>$id)
		{
			if($key=='PM_Registration')$field = 'pm_registration_page';
			if($key=='PM_Group')$field = 'pm_group_page';
			if($key=='PM_Groups')$field = 'pm_groups_page';
			if($key=='PM_Login')$field = 'pm_user_login_page';
			if($key=='PM_Profile')$field = 'pm_user_profile_page';
			if($key=='PM_Forget_Password')$field = 'pm_forget_password_page';
			if($key=='PM_Password_Reset_Form')$field = 'pm_password_reset_page';
		//print_r($field). ' '.print_r($id);die;
			$dbhandler->update_global_option_value($field,$id);	
		}
		
		if($dbhandler->pm_count('GROUPS')==0)
		{
			$data = array('group_name'=>'Default Group','associate_role'=>'subscriber');
			$arg = array('%s','%s');
			$gid = $dbhandler->insert_row('GROUPS', $data,$arg);
			$section_data = array('gid'=>$gid,'section_name'=>'default','ordering'=>$gid);
			$section_arg = array('%d','%s','%d');
			$sid = $dbhandler->insert_row('SECTION', $section_data,$section_arg);
                        $lastrow = $dbhandler->pm_count('FIELDS');
                        $lastrow = $dbhandler->get_all_result('FIELDS','field_id',1,'var',0,1,'field_id','DESC');
                        $ordering = $lastrow + 1;
                        $field_option = 'a:15:{s:17:"place_holder_text";s:0:"";s:19:"css_class_attribute";s:0:"";s:14:"maximum_length";s:0:"";s:13:"default_value";s:0:"";s:12:"first_option";s:0:"";s:21:"dropdown_option_value";s:0:"";s:18:"radio_option_value";a:1:{i:0;s:0:"";}s:14:"paragraph_text";s:0:"";s:7:"columns";s:0:"";s:4:"rows";s:0:"";s:18:"term_and_condition";s:0:"";s:18:"allowed_file_types";s:0:"";s:12:"heading_text";s:0:"";s:11:"heading_tag";s:2:"h1";s:5:"price";s:0:"";}';
                        $field_data = array('field_name'=>'User Email','field_type'=>'user_email','field_options'=>$field_option,'field_icon'=>0,'associate_group'=>$gid,'associate_section'=>$sid,'show_in_signup_form'=>1,'is_required'=>1,'ordering'=>$ordering,'field_key'=>'user_email');
                        $field_arg = array('%s','%s','%s','%d','%d','%d','%d','%d','%d','%s');
                        $dbhandler->insert_row('FIELDS', $field_data,$field_arg);
		}
		
		$users =  get_users( array( 'meta_key' => 'pm_group', 'meta_value' => '', 'meta_compare' => 'NOT EXISTS' ) );
		if(!empty($users))
		{
			if(!isset($gid))
			{
				$groups =  $dbhandler->get_all_result('GROUPS','id',1,'results','0',1,'id');
				foreach($groups as $group)
				{
					$gid = $group->id;
				}
			}
			foreach($users as $user)
			{
				add_user_meta($user->ID, 'pm_group',$gid,true);
				add_user_meta($user->ID, 'rm_user_status',0,true);
			}
		}
		
	}
	
	public function get_db_table_unique_field_name($identifier)
	{
	   
		switch ($identifier)
		{
			case 'GROUPS':
				$unique_field_name = 'id';
				break;
			
			case 'FIELDS':
				$unique_field_name = 'field_id';
				break;
			case 'PAYPAL_LOG':
				$unique_field_name = 'id';
				break;
			case 'EMAIL_TMPL':
				$unique_field_name = 'id';
				break;
			case 'SECTION':
				$unique_field_name = 'id';
				break;
                        case 'NOTIFICATION':
                                $unique_field_name = 'id';
				break;
			default:
				$classname = "PM_Helper_$identifier";
				if (class_exists($classname)) 
				{
                                        $externalclass = new $classname;
   					$unique_field_name = $externalclass->get_db_table_unique_field_name($identifier);
				}
				else
				{
					return false;
				}
		}
		return $unique_field_name;
		
	}

	public function get_db_table_field_type($identifier,$field)
	{
	   
		$functionname = 'get_field_format_type_'.$identifier;
		if (method_exists('Profile_Magic_Activator',$functionname))
		{
                        $format = $this->$functionname($field);
		}
		else
		{
			$classname = "PM_Helper_$identifier";
			if (class_exists($classname)) 
			{
                                $externalclass = new $classname;
				$format = $externalclass->get_db_table_field_type($identifier,$field);
			}
			else
			{
				return false;
			}
		}
		
		return $format;
	}
	
	public function get_field_format_type_SECTION($field)
	{
		switch ($field)
		{
			case 'id':
				$format = '%d';
				break;
			 case 'gid':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}
		return $format;
	}
	
	public function get_field_format_type_EMAIL_TMPL($field)
	{
		switch ($field)
		{
			case 'id':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}
		return $format;
	}
	
	 public function get_field_format_type_PAYPAL_LOG($field)
	 {
		switch ($field)
		{
			case 'id':
				$format = '%d';
				break;
                        case 'gid':
                                $format = '%d';
				break;
                        case 'amount':
                                $format = '%d';
				break;
                        case 'uid':
                                $format = '%d';
				break;
			default:
				$format = '%s';
		}
		return $format;
	}
        
        public function get_field_format_type_NOTIFICATION($field)
        {
            switch ($field)
		{
			case 'id':
                            $format = '%d';
                            break;
			default:
                            $format = '%s';
		}
		return $format;
        }


        public function get_field_format_type_GROUPS($field)
	{
		switch ($field)
		{
			case 'id':
				$format = '%d';
				break;
			case 'is_group_limit':
				$format = '%d';
				break;
			case 'is_group_leader':
				$format = '%d';
				break;
			case 'group_limit':
				$format = '%d';
				break;
			case 'group_icon':
				$format = '%d';
				break;
			case 'show_success_message':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}
		return $format;
	}
	
	public function get_field_format_type_FIELDS($field)
	{
		switch ($field)
		{
			case 'field_id':
				$format = '%d';
				break;
			case 'field_icon':
				$format = '%d';
				break;
			case 'associate_group':
				$format = '%d';
				break;
			case 'show_in_signup_form':
				$format = '%d';
				break;
			case 'is_required':
				$format = '%d';
				break;
			case 'is_editable':
				$format = '%d';
				break;
			case 'display_on_profile':
				$format = '%d';
				break;
			case 'display_on_group':
				$format = '%d';
				break;
			case 'visibility':
				$format = '%d';
				break;
			case 'ordering':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}
		return $format;
	}
}
