<?php
class PM_sanitizer
{
	public function get_sanitized_fields($identifier,$field,$value)
	{
		switch($identifier)
		{
			case 'GROUPS':
				$sanitized_value =  $this->get_sanitized_groups_field($field,$value);
				break;
			case 'FIELDS':
				$sanitized_value =  $this->get_sanitized_fields_field($field,$value);
				break;
			case 'SECTION':
				$sanitized_value =  $this->get_sanitized_section_field($field,$value);
				break;
			case 'EMAIL_TMPL':
				$sanitized_value =  $this->get_sanitized_emailtemplate_field($field,$value);
				break;
                        case 'BLOG':
				$sanitized_value =  $this->get_sanitized_blog_field($field,$value);
				break;
                        case 'MSG_THREADS':
				$sanitized_value =  $this->get_sanitized_msg_thread_field($field,$value);
				break;
                        case 'MSG_CONVERSATION':
				$sanitized_value =  $this->get_sanitized_msg_conversation_field($field,$value);
				break;
                        case 'NOTIFICATION':
                                $sanitized_value = $this->get_sanitized_notification_field($field,$value);
                        
			default:
				$classname = "PM_Helper_$identifier";
				if (class_exists($classname)) 
				{
                                        $externalclass = new $classname;
   					$sanitized_value = $externalclass->get_sanitized_fields($identifier,$field,$value);
				}
				else
				{
					$sanitized_value = $value;
				}
		}
		return $sanitized_value;
	}
	
	public function get_sanitized_section_field($field,$value)
	{
	    switch($field)
            {
                case 'id':
                    $value = sanitize_text_field($value);
                    break;
				case 'gid':
                    $value = sanitize_text_field($value);
                    break;
				case 'section_name':
                    $value = sanitize_text_field($value);
                    break;
				case 'section_options':
                    $value = sanitize_text_field($value);
                    break;
				 default:
					$value = sanitize_text_field($value);
						
			}
            return $value;
	}
        
        public function get_sanitized_notification_field($field,$value)
        {
            switch($field)
            {
                case 'id':
                    $value = sanitize_text_field($value);
                    break;
                case 'gid':
                    $value = sanitize_text_field($value);
                    break;
                case 'section_name':
                    $value = sanitize_text_field($value);
                    break;
                case 'section_options':
                    $value = sanitize_text_field($value);
                    break;
                default:
                    $value = sanitize_text_field($value);

           }
           return $value;
        }
	
	public function get_sanitized_emailtemplate_field($field,$value)
	{
		switch($field)
		{
			case 'id':
				$value = sanitize_text_field($value);
				break;
			case 'tmpl_name':
				$value = sanitize_text_field($value);
				break;
			case 'email_subject':
				$value = sanitize_text_field($value);
				break;
			case 'email_body':
				$value = wp_kses_post($value);
				break;
			 default:
				$value = sanitize_text_field($value);
					
		}
		return $value;	
	}
        
        public function get_sanitized_blog_field($field,$value)
        {
            switch($field)
		{
			case 'blog_title':
				$value = sanitize_text_field($value);
				break;
			case 'blog_description':
				$value = wp_kses_post($value);
				break;
			case 'blog_tags':
				$value = sanitize_text_field($value);
				break;
			 default:
				$value = sanitize_text_field($value);
					
		}
		return $value;	
        }
	
	public function get_sanitized_groups_field($field,$value)
	{
		switch($field)
            {
                case 'id':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_name':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_desc':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_icon':
                    $value = sanitize_file_name($value);
                    break;
                case 'is_group_limit':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_limit':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_limit_message':
                    $value = wp_kses_post( $value );
                    break;
				case 'associate_role':
                    $value = sanitize_text_field($value);
                    break;
				case 'is_group_leader':
                    $value = sanitize_text_field($value);
                    break;
				case 'leader_username':
                    $value = sanitize_text_field($value);
                    break;
				case 'leader_rights':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_slug':
                    $value = sanitize_text_field($value);
                    break;
				case 'group_options':
                    $value = sanitize_text_field($value);
                    break;
				case 'success_message':
					$value = wp_kses_post( $value );
					break;
                default:
					$value = sanitize_text_field($value);
                    
            }
            return $value;
	}
	
	public function get_sanitized_fields_field($field,$value)
	{
		switch($field)
            {
                case 'field_id':
                    $value = sanitize_text_field($value);
                    break;
				case 'field_name':
                    $value = sanitize_text_field($value);
                    break;
				case 'field_desc':
                    $value = sanitize_text_field($value);
                    break;
				case 'field_type':
                    $value = sanitize_text_field($value);
                    break;
                case 'field_options':
                    $value = $value;
                    break;
				case 'field_icon':
                    $value = sanitize_text_field($value);
                    break;
				case 'associate_group':
                    $value = sanitize_text_field($value);
                    break;
				case 'show_in_signup_form':
                    $value = sanitize_text_field($value);
                    break;
				case 'is_required':
                    $value = sanitize_text_field($value);
                    break;
				case 'is_editable':
                    $value = sanitize_text_field($value);
                    break;
				case 'display_on_profile':
                    $value = sanitize_text_field($value);
                    break;
				case 'display_on_group':
                    $value = sanitize_text_field($value);
                    break;
				case 'visibility':
                    $value = sanitize_text_field($value);
                    break;
				case 'ordering':
					$value = sanitize_text_field($value);
                    break;
                default:
					$value = sanitize_text_field($value);
                    
            }
            return $value;
	}
	
	public function get_sanitized_frontend_field($type,$value)
	{
		switch ($type) {
            case 'user_name':
                $value = sanitize_user($value, true);
                break;
            case 'user_login':
                $value = sanitize_user($value, true);
                break;
            case 'user_email':
                $value = sanitize_email($value);
                break;
            case 'email':
                $value = sanitize_email($value);
                break;
            case 'first_name':
                $value = sanitize_text_field($value);
                break;
            case 'last_name':
                $value = sanitize_text_field($value);
                break;
            case 'radio':
                $value = sanitize_text_field($value);
                break;
            case 'description':
                $value = sanitize_text_field($value);
                break;
            case 'text':
                $value = sanitize_text_field($value);
                break;
            case 'select':
                $value = sanitize_text_field($value);
                break;
            case 'heading':
                $value = sanitize_text_field($value);
                break;
            case 'paragraph':
                $value = sanitize_text_field($value);
                break;
            case 'number':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'country':
                $value = sanitize_text_field($value);
                break;
            case 'timezone':
                $value = sanitize_text_field($value);
                break;
            case 'term_checkbox':
                $value = sanitize_text_field($value);
                break;
            case 'user_url':
                $value = esc_url($value);
                break;
            case 'textarea':
                $value = sanitize_text_field($value);
                break;
            case 'DatePicker':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'mobile_number':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'phone_number':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'gender':
                $value = sanitize_text_field($value);
                break;
            case 'language':
                $value = sanitize_text_field($value);
                break;
            case 'birth_date':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'multi_dropdown':
                $value = sanitize_text_field($value);
                break;
            case 'facebook':
                $value = esc_url($value);
                break;
            case 'twitter':
                $value = esc_url($value);
                break;
            case 'google':
                $value = esc_url($value);
                break;
            case 'linked_in':
                $value = esc_url($value);
                break;
            case 'youtube':
                $value = esc_url($value);
                break;
            case 'instagram':
                $value = esc_url($value);
                break;
            case 'address':
                $value = esc_url($value);
                break;

            default:
                $value = $value;
        }
        return $value;
    }
    
    
     public function get_sanitized_msg_thread_field($field,$value)
        {
            switch($field)
		{
			 default:
				$value = sanitize_text_field($value);
					
		}
		return $value;	
        }

        
         public function get_sanitized_msg_conversation_field($field,$value)
        {
            switch($field)
		{
			case 'content':
				$value = wp_kses_post($value);
                                $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
                                $value = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $value);
                              
				break;
			 default:
				$value = sanitize_text_field($value);
					
		}
		return $value;	
        }

}
	
