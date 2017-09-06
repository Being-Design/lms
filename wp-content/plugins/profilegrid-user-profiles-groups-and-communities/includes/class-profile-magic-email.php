<?php
class PM_Emails
{
	public function pm_send_group_based_notification($gid,$userid,$event='')
	{
                $dbhandler = new PM_DBhandler;
		$row = $dbhandler->get_row('GROUPS',$gid);
		$tmpl_id = '';
		if(!empty($row))$group_options = maybe_unserialize($row->group_options);
		if(isset($group_options['enable_notification']) && $group_options['enable_notification']==1 && $group_options[$event]!='')
		{
			$tmpl_id = $group_options[$event] ;
		}
		
		if($tmpl_id!='')
		{
			$this->pm_send_user_notification($tmpl_id,$userid);	
		}
			
	}
	
	public function pm_send_admin_notification($subject,$message)
	{
                $pmrequests = new PM_request;
		$from_email_address = $pmrequests->profile_magic_get_from_email();
		$admin_email_address = $pmrequests->profile_magic_get_admin_email();				
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers .= 'From:'.$from_email_address. "\r\n"; 
		wp_mail( $admin_email_address, $subject, $message,$headers );			
	}
	
	public function pm_send_user_notification($id,$userid)
	{
                $pmrequests = new PM_request;
		$from_email_address = $pmrequests->profile_magic_get_from_email();
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers .= 'From:'.$from_email_address. "\r\n"; 
                $user_info = get_userdata($userid);
                $user_email = $user_info->user_email;
		$subject = $this->pm_get_email_subject($id);
		$message = $this->pm_get_email_content($id,$userid);
		if(isset($user_email))
		{
		  wp_mail( $user_email, $subject, $message, $headers );//Sends email to user on successful registration
		}	
	}
	
	public function getInbetweenStrings($start, $end, $str)
	{
		$matches = array();
		$regex = "/$start([a-zA-Z0-9_]*)$end/";
		preg_match_all($regex, $str, $matches);
		return $matches;
	}
	
	public function pm_get_email_content($id,$userid)
	{
                        $dbhandler = new PM_DBhandler;
                        $pmrequests = new PM_request;
			$row = $dbhandler->get_row('EMAIL_TMPL',$id);
			if(!empty($row))$message =$row->email_body;else $message = '';
			$matches = $this->getInbetweenStrings('{{','}}',$message);
			$result = $matches[1];
			foreach($result as $field)
			{
				$search = '{{'.$field.'}}';
				$value = $pmrequests->profile_magic_get_user_field_value($userid,$field);
                                if($field=='pm_activation_code')
                                {
                                    $value = $pmrequests->pm_create_user_activation_link($userid,$value);
                                }
                                
				$message = str_replace($search,$value,$message);
			}
			return $message;	
	}
	
	public function pm_get_email_subject($id)
	{
                        $dbhandler = new PM_DBhandler;
			$subject = $dbhandler->get_value('EMAIL_TMPL','email_subject',$id,'id');
			return $subject;	
	}
	
       
        
        public function pm_send_activation_link($userid,$textdomain='profile-grid')
        {
            $pmrequests = new PM_request;
            $dbhandler = new PM_DBhandler;
            $from_email_address = $pmrequests->profile_magic_get_from_email();
            $tmpl_id = $dbhandler->get_global_option_value('pm_user_activation_email_tmpl',0);
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= 'From:'.$from_email_address. "\r\n"; 
            $user_email = get_user_meta($userid,'user_email',true);
            $subject = $this->pm_get_email_subject($tmpl_id);
            $message = $this->pm_get_email_content($tmpl_id,$userid);
            
            if($subject=='' || $subject==NULL)
            {
                $subject = __('Your Registration is Pending Approval','profile-grid');
            }
            
            if($message=='' || $message==NULL)
            {
                $message  = sprintf(__( 'You are now registered at %s.','profile-grid'),get_bloginfo( 'name' )) . "<br />\r\n\r\n";
		$message .= __( 'Before you can login, you need to activate your account by visiting this link:','profile-grid') . "<br />\r\n\r\n";
		$message .= $pmrequests->pm_create_user_activation_link($userid,get_user_meta($userid,'pm_activation_code',true)) . "<br />\r\n\r\n";
		$message .= __( 'Thanks!','profile-grid' ) . "<br />\r\n";
            }
            
            if(isset($user_email))
            {
              wp_mail( $user_email, $subject, $message, $headers );//Sends email to user on successful registration
            }	
        }
}
?>