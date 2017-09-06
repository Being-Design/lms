<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-profile-magic-request
 *
 * @author Vikas Arora
 */
class PM_request {
//put your code here
	public function sanitize_request($post,$identifier,$exclude=array()) 
	{
                $pmsanitizer = new PM_sanitizer;
		foreach($post as $key=>$value)
		{
		  if(is_array($value))
		  {
			$value = maybe_serialize($value); 
		  }
		  if(!in_array($key,$exclude))
		  {
			 $data[$key] = $pmsanitizer->get_sanitized_fields($identifier,$key,$value);
		  }
		}
	
		return $data;
	}
	
	
	
	public function get_field_key($type,$id)
	{
		switch($type)
		{
			case 'first_name':
				$key = $type;
				break;
			case 'last_name':
				$key = $type;
				break;
			case 'description':
				$key = $type;
				break;
			case 'user_name':
				$key = 'user_login';
				break;
			case 'user_email':
				$key = $type;
				break;
			case 'user_pass':
				$key = $type;
				break;
			case 'confirm_pass':
				$key = $type;
				break;
			case 'user_url':
				$key = $type;
				break;
			case 'user_avatar':
				$key = 'pm_user_avatar';
				break;
			default:
				$key = 'pm_field_'.$id;
		}
		return sanitize_key($key);
	}





	public function get_default_key_type($type)
	{
		switch($type)
		{
			case 'first_name':
			case 'last_name':
			case 'description':
			case 'user_name':
			case 'user_email':
			case 'user_pass':
			case 'confirm_pass':
			case 'user_url':
			case 'user_avatar':
				$value = TRUE;
				break;
			default:
				$value = FALSE;
		}
		return $value;
	}
	
	public function get_userrole_name($userid) 
	{
		global $wp_roles;
		$user_info = get_userdata($userid);
		$roles = $user_info->roles;
		$role = array_shift($roles);
		return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
	}
	
	public function get_user_custom_fields_data($id,$visibility=false)
	{
                $dbhandler = new PM_DBhandler;
		$gid = get_user_meta($id,'pm_group',true);
		$data=array();
		if($gid!='' || $gid!=false)
		{
			if($visibility==false)
			{
				$where = array('associate_group'=>$gid);
			}
			else
			{
				$where = array('associate_group'=>$gid,'visibility'=>$visibility);
			}
			$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',$where,'results',0,false, $sort_by = 'ordering');
			
			if(!empty($fields))
			{
				foreach($fields as $field)
				{
					$data[$field->field_name] = get_user_meta($id,$field->field_key,true);	
				}
			}
			
		}
		return $data;
	}
	
	public function make_upload_and_get_attached_id($filefield,$allowed_ext,$require_imagesize=array(),$parent_post_id=0)
	{
		$allowfieldstypes = strtolower(trim($allowed_ext));
		$attach_id = '';	
		if(is_array($filefield))
		{
			$file = array('name' => $filefield['name'],
			'type' => $filefield['type'],
			'tmp_name' => $filefield['tmp_name'],
			'error' => $filefield['error'],
			'size' => $filefield['size']);
                        
                        if(!empty($require_imagesize))
                        {
                            $imagesize = getimagesize($file['tmp_name']);
                             $image_width = $imagesize[0];
                             $image_height = $imagesize[1];
                             if ( $image_width < $require_imagesize['0'] || $image_height < $require_imagesize['1'] ) 
                             {
                                 $too_small = sprintf( __( 'Image dimensions are too small. Minimum size is %d by %d pixels.','profile-grid' ), $require_imagesize['0'],$require_imagesize['1'] );
                             }
                             else
                             {
                                 $too_small = false;
                             }
                        }
                        else
                        {
                            $too_small = false;
                        }
                        
			if ($filefield['error']=== 0)
			{			
			  if ( ! function_exists( 'wp_handle_upload' ) )
			  {
				  require_once( ABSPATH . 'wp-admin/includes/file.php' );
				  require_once( ABSPATH . 'wp-admin/includes/image.php' );
			  }
			  $upload_overrides = array( 'test_form' => false );
			  $movefile = wp_handle_upload( $file, $upload_overrides );
			  if ( $movefile )
			  {
				  // $filename should be the path to a file in the upload directory.
				  $filename = $movefile['file'];
				  // The ID of the post this attachment is for.
				  
				  // Check the type of tile. We'll use this as the 'post_mime_type'.
				  $filetype = wp_check_filetype( basename( $filename ), null );
				  $current_file_type = strtolower($filetype['ext']);
				  if(strpos($allowfieldstypes,$current_file_type)!==false && $too_small==false)
				  {
                                          
					  // Get the path to the upload directory.
					  $wp_upload_dir = wp_upload_dir();
					  // Prepare an array of post data for the attachment.
					  $attachment = array(
						  'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
						  'post_mime_type' => $filetype['type'],
						  'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						  'post_content'   => '',
						  'post_status'    => 'inherit'
					  );
					  // Insert the attachment.
					  include_once( ABSPATH . 'wp-admin/includes/image.php' );
					  $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );  
					  $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
					  wp_update_attachment_metadata( $attach_id, $attach_data );

				  }
				  else
				  {
                                        if(strpos($allowfieldstypes,$current_file_type)===false)
                                        {
                                            return __("This file type is not allowed.",'profile-grid');
                                        }
                                        else
                                        {
					    return $too_small;
                                        }
				  }
			  }  
			}
		}
		return $attach_id;
							
	}
	
	public function pm_check_field_exist($gid,$type,$signup=false)
	{
                $dbhandler = new PM_DBhandler;
		if($gid!='' && $type!='')
		{
			$where = array('associate_group'=>$gid,'field_type'=>$type);
			if($signup=true)
			{
				$where['show_in_signup_form']=1;	
			}
			$result = $dbhandler->get_all_result('FIELDS', $column = '*',$where,'results',0,false, $sort_by = 'ordering');
			if($result==null)
			{
				return false;	
			}
			else
			{
				return true;	
			}
		}
		else
		{
			return false;	
		}
			
	}
	
	public function pm_get_currency_symbol()
	{
                $dbhandler = new PM_DBhandler;
		$currency = $dbhandler->get_global_option_value('pm_paypal_currency','USD');
		switch ($currency)
		{
			case 'USD':
				$sign = '&#36;';
				break;
			case 'EUR':
				$sign = '&#0128;';
				break;
			case 'GBP':
				$sign = '&#163;';
				break;
			case 'AUD':
				$sign = '&#36;';
				break;
			case 'BRL':
				$sign = 'R&#36;';
				break;
			case 'CAD':
				$sign = '&#36;';
				break;
			case 'HKD':
				$sign = '&#36;';
				break;
			case 'ILS':
				$sign = '&#8362;';
				break;
			case 'JPY':
				$sign = '&#165;';
				break;
			case 'MXN':
				$sign = '&#36;';
				break;
			case 'NZD':
				$sign = '&#36;';
				break;
			case 'SGD':
				$sign = '&#36;';
				break;
			case 'THB':
				$sign = '&#3647;';
				break;
			case 'INR':
				$sign = '&#8377;';
				break;
			case 'TRY':
				$sign = '&#8378;';
				break;
			default:
				$sign = $currency;
		}
		return $sign;
		
	}
	
	public function pm_encrypt_decrypt_pass($action,$string) 
	{
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'This is my secret key';
		$secret_iv = 'This is my secret iv';
		// hash
		$key = hash('sha256', $secret_key);
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if( $action == 'encrypt' ) {
                    if (function_exists('openssl_encrypt')) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
                    }
                    else
                    {
			$output = base64_encode($string);
                    }
			
		}
		else if( $action == 'decrypt' ){
                    
                    if (function_exists('openssl_decrypt')) {
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }
                    else
                    {
			$output = base64_decode($string);
                    }
                    
			
		}
		return $output;
	}
	
	public function profile_magic_captcha_verification($response,$remote_ip) {
                $dbhandler = new PM_DBhandler;
		$secret_key = $dbhandler->get_global_option_value('pm_recaptcha_secret_key');
		// make a GET request to the Google reCAPTCHA Server
		$request = wp_remote_get(
			'https://www.google.com/recaptcha/api/siteverify?secret=' .$secret_key . '&response=' . $response . '&remoteip=' . $remote_ip
		);
		// get the request response body
		$response_body = wp_remote_retrieve_body( $request );
		$result = json_decode( $response_body, true );
		return $result['success'];
	}
	
	public function profile_magic_check_username_exist($username)
	{
		if(username_exists($username))
		{
			 if(is_multisite() && is_user_member_of_blog(username_exists($username))==false)
			 { 
				return false;		
			 }
			 else
			 {
				return true;
			 }
		}
		else
		{
			return false;
		}
	}
	
	public function profile_magic_check_user_email_exist($email)
	{
		if(email_exists($email))
		{
			 if(is_multisite() && is_user_member_of_blog(email_exists($email))==false)
			 { 
				return false;		
			 }
			 else
			 {
				return true;
			 }
		}
		else
		{
			return false;
		}
	}
	
	public function profile_magic_frontend_server_validation($post,$files,$server,$fields,$textdomain,$type='')
	{
                $dbhandler = new PM_DBhandler;
		$error = array();
		foreach($fields as $field) 
		{
			$field_key = $field->field_key;
			if($field->field_type=='user_email')
			{
				
				if(!isset($post[$field_key]) || $post[$field_key]=='')
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid');	
				}
				else
				{
					$useremail = sanitize_email($post[$field_key]);
					if(is_email($useremail)==false)
					{
						$error[] =  __("Please enter a valid e-mail address",'profile-grid');	 
					}
					
					if($this->profile_magic_check_user_email_exist($useremail))
					{
						$error[] =  __("This user is already registered. Please try with different email.",'profile-grid');
					}
				}
			}
			if($field->field_type=='user_name')
			{
				
				if(!isset($post[$field_key]) || $post[$field_key]=='')
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid');	
				}
				else
				{
					$username = sanitize_user($post[$field_key]);
					if($this->profile_magic_check_username_exist($username))
					{
						$error[] =  __("Sorry, username already exist.",'profile-grid');
					}
				}
			}
			if($field->field_type=='user_pass')
			{
				if(!isset($post[$field_key]) || $post[$field_key]=='')
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid').'<br />';	
				}
				else
				{
					if(strlen($post[$field_key])<7)
					{
						$error[] .=  __("Password is too short. At least 7 characters please!",'profile-grid').'<br />';
					}	
				}
			}
			if($field->field_type=='confirm_pass')
			{
				if(!isset($post[$field_key]) || $post[$field_key]=='')
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid').'<br />';	
				}
				else
				{
					if($post[$field_key]!==$post['user_pass'])
					{
						$error[] .=  __("Password and confirm password do not match.",'profile-grid').'<br />';
					}	
				}
			}
			if($field->is_required == 1 && $field->field_type!='file' && $field->field_type!='user_avatar' && $field->field_type!='user_name' && $field->field_type!='user_email' && $field->field_type!='user_pass' && $field->field_type!='confirm_pass')
			{
				if(!isset($post[$field_key]) || $post[$field_key]=='')
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid').'<br />';	
				}
				else
				{
					if(is_array($post[$field_key]))
					{
						$value = implode(',',$post[$field_key]);
						if(!isset($value) || $value=='') $error[] =  $field->field_name. __(" is a required field",'profile-grid').'<br />';
					}
				}
			}
			if($field->is_required == 1 && ($field->field_type=='file' || $field->field_type=='user_avatar') && $type!='edit_profile')
			{
				$filefield = $files[$field_key];			
				if(is_array($filefield) && empty($filefield['name'][0]))
				{
					$error[] =  $field->field_name. __(" is a required field",'profile-grid').'<br />';	
				}
			}
			
			if(($field->field_type=='file' || $field->field_type=='user_avatar')  && isset($files[$field_key]) && !empty($files[$field_key]['name'][0]))
			{
				$field_options = maybe_unserialize($field->field_options);
				$allowed_ext = (($field_options['allowed_file_types']!='')?$field_options['allowed_file_types']:$dbhandler->get_global_option_value('pm_allow_file_types','jpg|jpeg|png|gif'));
				//$current_file_type = '';
				if($field->field_type=='user_avatar')
				{
					$allowed_ext = 'jpg|jpeg|png|gif';
				}
				$allowfieldstypes = strtolower(trim($allowed_ext));
				$filefield = $files[$field_key];
				if(is_array($filefield))
				{
					
					for( $i =0; $i<count($filefield['name']); $i++ ) 
					{
						$file = array(
									  'name'     => $filefield['name'][$i],
									  'type'     => $filefield['type'][$i],
									  'tmp_name' => $filefield['tmp_name'][$i],
									  'error'    => $filefield['error'][$i],
									  'size'     => $filefield['size'][$i]
									);
						 $filetype = wp_check_filetype( basename($file['name'] ), null );
						 $current_file_type = strtolower($filetype['ext']);
						 if(empty($current_file_type) || $current_file_type=='')
						 {
							 $error[] = __("This file type is not allowed.",'profile-grid');
						 }
						 elseif(strpos($allowfieldstypes,$current_file_type)===false)			
							$error[] = __("This file type is not allowed.",'profile-grid');
					}
				}
			}
			
			if($field->field_type=='email' && isset($post[$field_key]) && $post[$field_key]!="")
			{
				 if(is_email($post[$field_key])==false)
				 {
					$error[] =  __("Please enter a valid e-mail address",'profile-grid').'<br />';	 
				 }
			}
			
			if($field->field_type=='number' && isset($post[$field_key]) && $post[$field_key]!="")
			{
				 if(is_numeric($post[$field_key])==false)
				 {
					$error[] =  __("Please enter a valid number",'profile-grid').'<br />';	 
				 }
			}
			if($field->field_type=='pricing' && isset($post[$field_key]) && $post[$field_key]!="")
			{
				 if(is_numeric($post[$field_key])==false)
				 {
					$error[] =  __("Please enter a valid amount",'profile-grid').'<br />';	 
				 }
			}		
			if($field->field_type=='DatePicker' && isset($post[$field_key]) && $post[$field_key]!="")
			{
				 if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$post[$field_key]))
				 {
					$error[] =  __("Please enter a valid date (yyyy-mm-dd format)",'profile-grid').'<br />';	 
				 }
			}
		}
		return $error;
	}
	
	public function profile_magic_show_captcha($option)
	{
                $dbhandler = new PM_DBhandler;
		$enable_recaptcha = $dbhandler->get_global_option_value('pm_enable_recaptcha');
		$enable_recaptcha_form = $dbhandler->get_global_option_value($option);
		if($enable_recaptcha ==1 && $enable_recaptcha_form==1)
		{
			return true;	
		}
		else
		{
			return false;
		}
	
	}
	
	public function profile_magic_generate_password()
	{
		$password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		return $password;
	}
	
	public function profile_magic_check_paid_group($gid)
	{
                $dbhandler = new PM_DBhandler;
		$options = maybe_unserialize($dbhandler->get_value('GROUPS','group_options',$gid,'id'));
		
		if(!empty($options) && isset($options['is_paid_group']) && $options['is_paid_group']==1)
		{
			$price = $options['group_price'];
		}
		else{$price = 0;}
		return $price;
	}
	
	public function profile_magic_get_frontend_url($page,$default)
	{
                $dbhandler = new PM_DBhandler;
		 $profile_magic_url = $dbhandler->get_global_option_value($page,'0');
		 if($profile_magic_url==0)
		 {
			$url = $default;
		 }
		 else
		 {
			$url = get_permalink($profile_magic_url);
		 }
		 return $url;
	}
	
	public function profile_magic_get_error_message($error_code,$textdomain)
	{
		switch ( $error_code ) 
		{
			case 'empty_username':
				$message = __( 'You do have an email address, right?','profile-grid' );
				break;	 
			case 'empty_password':
				$message = __( 'You need to enter a password to login.','profile-grid' );
				break;
			case 'invalid_username':
				$message = __("We don't have any users with that email address. Maybe you used a different one when signing up?",'profile-grid');
				break;
			case 'incorrect_password':
				$err = __("You entered incorrect password. Please try again or click on <a href='%s'>“Forgot Password”</a>",'profile-grid');
                                $forget_password_url = $this->profile_magic_get_frontend_url('pm_forget_password_page',site_url('/wp-login.php?action=lostpassword'));
				$message =  sprintf( $err, $forget_password_url );
                                break;
			case 'empty_username':
				$message = __( 'You need to enter your email address to continue.','profile-grid');
				break;
			case 'account_disabled':
				$message = __('Account disabled','profile-grid');
				break;
			case 'payment_pending':
				$url = __("Your account has been deactivated due to a pending payment. <a href='%s'>Do you wish to pay now?</a>",'profile-grid');
				$message =  sprintf( $url,$this->pm_get_repayment_url($_REQUEST['id']));
				break;
			 
			case 'invalid_email':
				$message = __( 'There are no users registered with this email address.','profile-grid' );
				break;
			case 'invalidcombo':
				$message = __( 'There are no users registered with this email address.','profile-grid' );
				break;
			case 'expiredkey':
				$message = __( 'The password reset link you used is not valid anymore.','profile-grid' );
				break;
			case 'invalidkey':
				$message = __( 'The password reset link you used is not valid anymore.','profile-grid' );
				break;
			 
			case 'password_reset_mismatch':
				$message = __( "The two passwords you entered don't match.",'profile-grid' );
				break;
				 
			case 'password_reset_empty':
				$message = __( "Sorry, we don't accept empty passwords.",'profile-grid' );
				break;
			case 'password_too_short':
				$message = __( "Password is too short. At least 7 characters please!",'profile-grid' );
				break;
				
			case 'loginrequired':
                                $err = __("Login required to view this page. Please <a href='%s'>Login</a>.", 'profile-grid');
                                $login_page_url = $this->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
				$message =  sprintf( $err, $login_page_url );
				break;
			case 'not_permitted':
				$message = __("You have not permitted to view this page.",'profile-grid');
				break;
			case 'ajx_failed_del':
				$message = __("Failed to upadte user information.Can not activate user.",'profile-grid');
				break;
                        case 'invalid_code':
				$message = __("Invalid Activation code.",'profile-grid');
				break;
			default:
				$message = __( 'An unknown error occurred. Please try again later.','profile-grid' );
				break;
    	}
     	return $message;
	}
	
	public function profile_magic_get_user_field_value($userid,$field,$field_type='')
	{
		$user_info = get_userdata($userid);
		
		switch($field)
		{
			
			case 'user_login':
				$value =  $user_info->user_login;
				break;
			case 'user_pass':
				$pass =  get_user_meta($userid,'user_pass',true);
				$value = $this->pm_encrypt_decrypt_pass('decrypt',$pass);
				break;
			case 'user_nicename':
				$value =  $user_info->user_nicename;
				break;
			case 'user_email':
				$value =  $user_info->user_email;
				break;
			case 'user_url':
				$value =  $user_info->user_url;
				break;
			case 'user_registered':
				$value =  $user_info->user_registered;
				break;
			case 'display_name':
				$value =  $user_info->display_name;
				break;
			case 'first_name':
				$value =  $user_info->first_name;
				break;
			case 'last_name':
				$value =  $user_info->last_name;
				break;
			case 'nickname':
				$value =  $user_info->nickname;
				break;
			case 'description':
				$value =  $user_info->description;
				break;
			case 'wp_capabilities':
				$value =  $user_info->wp_capabilities;
				break;
			case 'admin_color':
				$value =  $user_info->admin_color;
				break;
			case 'closedpostboxes_page':
				$value =  $user_info->closedpostboxes_page;
				break;
			case 'primary_blog':
				$value =  $user_info->primary_blog;
				break;
			case 'rich_editing':
				$value =  $user_info->rich_editing;
				break;
			case 'source_domain':
				$value =  $user_info->source_domain;
				break;
			default:
				$value = get_user_meta($userid,$field,true);
				if($field_type!='' && $field_type=='file' && $value!='')
				{
					$value = $this->profile_magic_get_user_attachment($value);
                                        
				}
				break;
		}
                
		
		return $value;		
	}
	
	public function profile_magic_get_user_attachment($value)
	{
		
		$values = explode(',',$value);
		$attachment_html ='<span class="pm_frontend_attachment">';	
		foreach($values as $fileid)
		{
			$attachment_html .= '<span class="attachment_icon">'.wp_get_attachment_link($fileid,'full',false,true,false).'</span>';
			$attachment_html .='<span class="pm-attachment-title pm-dbfl"><a href="'.wp_get_attachment_url( $fileid ).'">'.get_the_title( $fileid ).'</a></span>';
		}
		$attachment_html .='</span>';
		unset($values);
		return $attachment_html;
	}
	public function profile_magic_get_from_email()
	{
                $dbhandler = new PM_DBhandler;
		if($dbhandler->get_global_option_value('pm_enable_smtp')==1)
		{
			$email_address = $dbhandler->get_global_option_value('pm_smtp_from_email_address',get_option('admin_email'));
		}
		else
		{
			$from_name = $dbhandler->get_global_option_value('pm_from_email_name',get_bloginfo( 'name' ));
			$email_address = $from_name.' <'.$dbhandler->get_global_option_value('pm_from_email_address',get_option('admin_email')).'>';
		}
		return $email_address;
	}
	
	public function profile_magic_get_admin_email()
	{
                $dbhandler = new PM_DBhandler;
		$email_address = implode(',',maybe_unserialize($dbhandler->get_global_option_value('pm_admin_email')));
		if($email_address=='' || empty($email_address)) $email_address = get_option('admin_email');
		return $email_address;
	}
	
	public function profile_magic_frontend_registration_request($post,$files,$server,$gid,$fields)
	{
                $dbhandler = new PM_DBhandler;
                $pmsanitizer = new PM_sanitizer;
		$user_email = $pmsanitizer->get_sanitized_frontend_field('user_email',$post['user_email']);
		$user_role = $dbhandler->get_value('GROUPS','associate_role',$gid,'id');
		$password = (isset($post['user_pass'])?$post['user_pass']:$this->profile_magic_generate_password());	
		
		if(isset($post['user_login']))
		{
			$user_name = $pmsanitizer->get_sanitized_frontend_field('user_login',$post['user_login']);
		}
		else
		{
			$user_name = $pmsanitizer->get_sanitized_frontend_field('user_login',$post['user_email']);
		}
			
		$user_id = $dbhandler->pm_add_user($user_name,$password,$user_email,$user_role);
		update_user_meta( $user_id,'pm_group',$gid);
		$newpass = $this->pm_encrypt_decrypt_pass('encrypt',$password);
		update_user_meta( $user_id,'user_pass',$newpass);
		update_user_meta( $user_id,'rm_user_status','1');
		$this->pm_update_user_custom_fields_data($post,$files,$server,$gid,$fields,$user_id);		
		return $user_id;
	}
	
	public function pm_admin_notification_message_html($post,$gid,$fields,$exclude=array())
	{
		$html = '';
		if(!empty($fields))
		{
			$html .= '<table>';
			foreach($fields as $field)
			{
				if(in_array($field->field_type,$exclude)) continue;
                                if(isset($post[$field->field_key]))
                                {
                                    if(is_array($post[$field->field_key]))
                                    {
                                            $value = implode(',',$post[$field->field_key]);	
                                    }
                                    else
                                    {
                                            $value = $post[$field->field_key];	
                                    }
                                    $html .='<tr><td>'.$field->field_name.'</td><td>'.$value.'</td></tr>';
                                }
                                
                        }
			$html .= '</table>';
		}
		return $html;
	}
	
	public function pm_update_user_custom_fields_data($post,$files,$server,$gid,$fields,$user_id)
	{
                $dbhandler = new PM_DBhandler;
                $pmsanitizer = new PM_sanitizer;
		if(isset($fields) && !empty($fields)):
		foreach($fields as $field)
		{
			if($field->field_type=='user_pass' || $field->field_type=='confirm_pass') continue;
			$value = '';
			$field_key = $field->field_key;
			$field_options = maybe_unserialize($field->field_options);
			if($field->field_type=='file' || $field->field_type=='user_avatar')
			{
				$allowed_ext = (($field_options['allowed_file_types']!='')?$field_options['allowed_file_types']:$dbhandler->get_global_option_value('pm_allow_file_types','jpg|jpeg|png|gif'));
				$filefield = $files[$field_key];
				if(is_array($filefield))
				{
					
					for( $i =0; $i<count($filefield['name']); $i++ ) 
					{
						$file = array(
									  'name'     => $filefield['name'][$i],
									  'type'     => $filefield['type'][$i],
									  'tmp_name' => $filefield['tmp_name'][$i],
									  'error'    => $filefield['error'][$i],
									  'size'     => $filefield['size'][$i]
									);			
						$attchment_id[] = $this->make_upload_and_get_attached_id($file,$allowed_ext);
					}
					
					$value = implode(',',$attchment_id);
					unset($attchment_id);
				}
				
				
			}
			else
			{
				if(isset($post[$field_key]))
				{
					if(is_array($post[$field_key]))
					{
						//$value = sanitize_text_field(rtrim(implode(',',$post[$field_key]),','));
                                                $value = maybe_serialize($post[$field_key]);
					}
					else
					{
						$value = $pmsanitizer->get_sanitized_frontend_field($field->field_type,$post[$field_key]);	
					}
					
				}
			}
			if($field->field_type=='user_url')
			{
				wp_update_user( array( 'ID' => $user_id, 'user_url' => $value ) );
			}
			else
			{
				if($field->field_type=='file' || $field->field_type=='user_avatar')
				{
					if($value!='')
					{
						update_user_meta( $user_id,$field_key,$value);
					}
				}
				else
				{
					update_user_meta( $user_id,$field_key,$value);	
				}	
			}
		}
		endif;
	}
	public function pm_get_user_avatar($userid)
	{
		$avatar = get_avatar($userid, 274,'',false,array('class'=>'pm-user'));
		//$avatar = get_avatar($user_info->user_email, 300 );
		return $avatar;
	}
	
	public function pm_get_repayment_url($uid)
	{
		$gid = get_user_meta( $uid, 'pm_group', true );
		$registration_url  = $this->profile_magic_get_frontend_url('pm_registration_page','');
		$registration_url = add_query_arg( 'gid',$gid,$registration_url );
		$registration_url = add_query_arg( 'uid',$uid,$registration_url );
		$registration_url = add_query_arg( 'action','re_process',$registration_url );
		return $registration_url;

	}
	
	public function pm_get_user_redirect($gid)
	{
                $dbhandler = new PM_DBhandler;
		$options = maybe_unserialize($dbhandler->get_value('GROUPS','group_options',$gid));
		$url = '';
		if(!empty($options) && isset($options['redirect']))
		{
			switch($options['redirect'])
			{
				case 'url':
					$url = $options['redirect_url'];
					break;
				case 'page':
					$url = get_permalink($options['redirect_page_id']);
					break;
				default:
					$url = '';
					break;	
			}
		}
		return $url;
	}
	
	public function pm_get_user_date_query($get)
	{
		$date_query = array();
		if(isset($get['time']) && $get['time']!='' && $get['time']!='all' )
		{
			switch($get['time'])
			{
				case 'today':
					$today = getdate();
					$date_query[] = array(
						'year'  => $today['year'],
						'month' => $today['mon'],
						'day'   => $today['mday'],
					);
					break;	
				case 'yesterday':
					$yesterday = getdate(strtotime("-1 day"));
					$date_query[] = array(
						'year'  => $yesterday['year'],
						'month' => $yesterday['mon'],
						'day'   => $yesterday['mday'],
					);
					break;
				case 'this_week':
					$date_query[] = array(
						'year' => date( 'Y' ),
						'week' => date( 'W' ),
					);
					break;
				case 'last_week':
					$date_query[] = array(
						'year' => date( 'Y' ),
						'week' => date( 'W',strtotime("-1 week") ),
					);
					break;
				case 'this_month':
					$date_query[] = array(
						'year'  => date( 'Y' ),
						'month' =>  date( 'm' ),
					);
					break;
				case 'this_year':
					$date_query[] = array(
						'year'  => date( 'Y' ),
					);
					break;
				case 'specific':
					if(isset($get['start_date']) && $get['end_date'])
					{
						$end_date = strtotime($get['end_date']);
						$start_date = strtotime($get['start_date']);
						$date_query[] = array(
							'after'     => array(
								'year'  => date('Y',$start_date),
								'month' => date('m',$start_date),
								'day'   => date('d',$start_date),
							),
							'before'    => array(
								'year'  => date('Y',$end_date),
								'month' => date('m',$end_date),
								'day'   => date('d',$end_date),
							),
							'inclusive' => true,
						);
					}
					break;
			}
		}
		return $date_query;
	}
	public function pm_get_user_meta_query($get)
	{
		$meta_query_array = array();
		if(isset($get['gid']) && $get['gid']!='')
		{
			$meta_query_array['relation'] = 'AND';
			$meta_query_array[] =array(
									'key'     => 'pm_group',
									'value'   => $get['gid'],
									'compare' => '='
								); 
		}
                else
                {
                    $meta_query_array['relation'] = 'AND';
                    $meta_query_array[] =array('key'     => 'pm_group');    
                }
		
		if(isset($get['status']) && $get['status']!='')
		{
			if($get['status']!='all')
			{
			       
                                    $meta_query_array[] = array(
                                    'key'     => 'rm_user_status',
                                    'value'   => $get['status'],
                                    'compare' => '=');				
                            
			}
		}
		
		if(isset($get['match_field']) && $get['match_field']!='' && isset($get['field_value']) &&  $get['field_value']!='' )
		{
			$meta_query_array[] =array(
                                                    'key'     => $get['match_field'],
                                                    'value'   => $get['field_value'],
                                                    'compare' => 'LIKE'
                                            ); 
			
		}
		
		return $meta_query_array;
	}
        
    public function pm_get_user_advance_search_meta_query($get)
    {
        $meta_query_array = array();
        $search_string = esc_attr(trim($get['pm_search']));
        //MATCH GID FOR SEARCH
            if (isset($get['gid']) && $get['gid'] != '')
            {
                    $meta_query_array['relation'] = 'AND';
                    $meta_query_array[] = array
                                            (
                                                'key' => 'pm_group',
                                                'value' => $get['gid'],
                                                'compare' => '='
                                             );
            }
            else
            {
                    $meta_query_array['relation'] = 'AND';
                    $meta_query_array[] = array('key' => 'pm_group');
            }
            
		
		if(isset($get['status']) && $get['status']!='')
		{
			if($get['status']!='all')
			{
			       
                                    $meta_query_array[] = array(
                                    'key'     => 'rm_user_status',
                                    'value'   => $get['status'],
                                    'compare' => '=');				
                            
			}
		}
            
            
            if (isset($get['match_fields']) && isset($get['pm_search']) && $get['pm_search'] != '') 
            {
                if (is_array($get['match_fields'])) 
                    {
                       $match_field_array['relation'] = 'OR';
                       foreach ($get['match_fields'] as $value)
                       {
                            $match_field_array[] = array
                            (
                                'key' => $value,
                                'value' => $search_string,
                                'compare' => 'LIKE'
                            );
                        }
                   
                        $meta_query_array[]=$match_field_array;
                       }
                   else
                    {
                        $meta_query_array[] = array
                        (
                            'key' => $get['match_fields'],
                            'value' => $search_string,
                            'compare' => 'LIKE'
                        );
                   }
            }
                
        return $meta_query_array;
    }
        
        
        
	
	public function pm_get_frontend_user_meta($uid,$gid,$group_leader,$view='',$section='',$exclude='')
	{
                $dbhandler = new PM_DBhandler;
		$data = array();
		$where = array('associate_group'=>$gid,'display_on_profile'=>1);
		if($view=='group')
		{
			$where['display_on_group']=1;		
		}
		
		if($section!='')
		{
			$where['associate_section']=$section;		
		}
		
		if(is_user_logged_in())
		{
			$current_user = wp_get_current_user();
			if($current_user->ID==$group_leader || $current_user->ID == $uid)
			{
				$additional = 'AND visibility in(1,2,3)';
			}
			else
			{
				$additional = 'AND visibility in(1,2)';
			}
		}
		else
		{
			$additional = 'AND visibility = 1';
		} 
		if($exclude!='')
		{
			$additional .= ' AND field_type not in('.$exclude.')';	
		}
		$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',$where,'results',0,false, $sort_by = 'ordering',false,$additional);
		return $fields;
	}
	
	public function pm_get_backend_user_meta($uid,$gid,$group_leader,$view='',$section='',$exclude='')
	{
                $dbhandler = new PM_DBhandler;
		$data = array();
		$where = array('associate_group'=>$gid);
		if($section!='')
		{
			$where['associate_section']=$section;		
		}
		
		if($exclude!='')
		{
			$additional = ' AND field_type not in('.$exclude.')';	
		}
		$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',$where,'results',0,false, $sort_by = 'ordering',false,$additional);
		return $fields;
	}
	
	public function profile_magic_check_is_group_leader($userid,$gid=false)
	{
                $dbhandler = new PM_DBhandler;
		$group_leader_id = false;
                if($gid==false)
                {
		$gid = get_user_meta($userid,'pm_group',true);
                }
		$is_group_leader = $dbhandler->get_value('GROUPS','is_group_leader',$gid,'id');
		$group_leader_name = $dbhandler->get_value('GROUPS','leader_username',$gid,'id');
		if(isset($group_leader_name) && $group_leader_name!='' && $is_group_leader!=0)
		{
			$group_leader_id = username_exists($group_leader_name);	
		}
		return $group_leader_id;	
	}
	
	public function profile_magic_set_group_leader($gid)
	{
                $dbhandler = new PM_DBhandler;
		$is_group_leader = $dbhandler->get_value('GROUPS','is_group_leader',$gid,'id');
		$group_leader_name = $dbhandler->get_value('GROUPS','leader_username',$gid,'id');
		if(isset($group_leader_name) && $group_leader_name!='' && $is_group_leader!=0)
		{
			$userid = username_exists($group_leader_name);	
			update_user_meta( $userid,'pm_group',$gid);
		}		
	}	
        
        public function profile_magic_get_group_icon($group=NULL,$class='')
        {
            $path =  plugins_url( '../public/partials/images/default-group.jpg', __FILE__ );
            if(isset($group) && $group->group_icon!=0):
                $image = wp_get_attachment_image($group->group_icon,'full',true,array('class'=>$class));
            else :
                $image = '<img src="'.$path.'" class="'.$class.'" />';
            endif;
            return $image;
        }
	
        public function profile_magic_get_cover_image($uid,$class='')
        {
            $imageid = $this->profile_magic_get_user_field_value($uid,'pm_cover_image');
            $path =  plugins_url( '../public/partials/images/default-cover.jpg', __FILE__ );
            if(isset($imageid) && !empty($imageid)):
                $image = wp_get_attachment_image($imageid,'full',false,array('class'=>$class));
             else:
                $image = '<img src="'.$path.'" class="'.$class.'" />';
            endif;
            return $image;
        }
        
        public function profile_magic_get_pm_theme_name()
        {
            $dirname = array();
            $pm_theme_path = plugin_dir_path( __FILE__ ) . '../public/partials/themes/';
            $wp_theme_dir = get_template_directory();
            $override_pm_theme_path = $wp_theme_dir . "/profilegrid-user-profiles-groups-and-communities/themes/";
            if(file_exists($pm_theme_path))
            {
                foreach(glob($pm_theme_path.'*', GLOB_ONLYDIR) as $dir) 
                {
                      $dirname[] =  basename($dir) ;   
                }  
            }
            
            if(file_exists($override_pm_theme_path))
            {
                foreach(glob($override_pm_theme_path.'*', GLOB_ONLYDIR) as $dir2) 
                {
                      $dirname[] =  basename($dir2) ;   
                }  
            }
            return array_unique($dirname);
        }
        
        public function pm_update_user_activation_code($user_id)
        {
            if ((int) $user_id)
            {
                $pass = wp_generate_password(10, false);
                $activation_code = md5($pass);
                update_user_meta($user_id, 'pm_activation_code', $activation_code);
            }
        }
        
        public function pm_create_user_activation_link($user_id,$activation_code)
        {
            $user_data_obj = new stdClass();
            $user_data_obj->user_id = $user_id;
            $user_data_obj->activation_code = $activation_code;
            $user_data_json = json_encode($user_data_obj);
            $user_data_enc = urlencode($this->pm_encrypt_decrypt_pass('encrypt',$user_data_json));
            $user_activation_link = admin_url('admin-ajax.php') . '?action=pm_activate_user_by_email&user=' . $user_data_enc;
            return $user_activation_link;
        }
        
         public function pm_get_display_name($uid)
         {
             
           $user = get_userdata($uid);
           if(isset($user))
           {
               $firstname = $user->first_name;
               $lastname = $user->last_name;
               $username = $user->user_login;
               $displayname = $username;
               if(isset($firstname)&&($firstname!=''))
               {
                       if(isset($lastname) && $lastname!='')
                       {
                           $displayname = $firstname.' '.$lastname;
                       }
                       else
                       {
                           $displayname = $firstname;
                       }
               }
               
               $displayname = apply_filters( 'profile_magic_filter_display_name',$displayname,$uid );
                return $displayname;              
           }
         }
         
         
     
         
         public function pm_five_star_review_banner()
         {?>
<div class="pm_five_star_Banner">
    <p align="center"><?php _e("Do you like ProfileGrid? Help us  make it better…Please rate it ", 'profile-grid'); ?>
        <span class="pm-star">
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
        </span><?php _e(" Stars on ", 'profile-grid'); ?><a target="_blank" href="https://wordpress.org/support/plugin/profilegrid-user-profiles-groups-and-communities/reviews/?rate=5#new-post">WordPress.org</a></p>
</div>

         <?php    
         }
         
         
         public function pm_get_user_all_threads($uid) {
        if ($uid) {
            $dbhandler = new PM_DBhandler;
            $identifier = 'MSG_THREADS';
            $where = 1;
            $additional = ' s_id = '.$uid.' OR r_id = '.$uid;
            $threads = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', 0, false, $sort_by = 'timestamp', true,$additional);
            return $threads;
        }
    }

    public function pm_create_message($sid, $rid, $content) {
        $inserted=='false';
        if($sid!=''&& $rid!=''){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_CONVERSATION';
        $status = 2;
        $content = wp_kses($content);
        $tid = $this->fetch_or_create_thread($sid, $rid);
        $data = array('s_id' => $sid,'t_id'=>  $tid, 'content' => $content, 'status' => $status,'timestamp'=> current_time('mysql',true));
        $data = $this->sanitize_request($data,$identifier);
        $inserted = $dbhandler->insert_row($identifier, $data);
        }
       if($inserted=='false'){
           return false;
       }else{
       $this->pm_update_thread_time($tid); 
       $this->pm_update_thread_status($tid,$rid); //RID is sent for status of thread
       return true;
       }
          
    }
    
    public function pm_update_thread_time($tid,$status=''){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $data=array('timestamp'=> current_time('mysql',true));
        if($status!='')$data['status']=$status;
        $data = $this->sanitize_request($data,$identifier);
        $updated=$dbhandler->update_row($identifier, 't_id',$tid,$data);
     
    }
    
    public function pm_update_thread_status($tid,$status){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $data=array('status'=> $status);
        $data = $this->sanitize_request($data,$identifier);
        $updated=$dbhandler->update_row($identifier, 't_id',$tid,$data);
     
    }
    
    

    public function fetch_or_create_thread($sid, $rid) {
        $dbhandler =new PM_DBhandler;
         $identifier = 'MSG_THREADS';
        if ($this->is_thread_exsist($sid, $rid)) {
        
            $tid = $this->get_thread_id($sid, $rid);
        } else {
               
            $thread_desc = array();
            $thread_desc["$sid"]['typing_timestamp']=0;
            $thread_desc["$sid"]['delete_mid']=0;
            $thread_desc["$sid"]['typing_status']='nottyping';
            $thread_desc["$rid"]['typing_timestamp']=0;
            $thread_desc["$rid"]['delete_mid']=0;
            $thread_desc["$rid"]['typing_status']='nottyping';
            $value= maybe_serialize($thread_desc);
            $data = array('s_id' => $sid, 'r_id' => $rid,'thread_desc'=>$value,'timestamp'=> current_time('mysql',true));     
            $data = $this->sanitize_request($data,$identifier);
            $dbhandler->insert_row($identifier, $data);
            $tid = $this->get_thread_id($sid, $rid);
        }
        return $tid;
    }

    public function is_thread_exsist($sid, $rid) {
        if($sid!='' && $rid!='' ){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $where = 1;
        $additional = " s_id in ($sid,$rid) AND r_id in ($sid,$rid)";
        $thread = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', 0, false, $sort_by = 'timestamp', true, $additional);
              //  echo '<pre>'.print_r($thread).'</pre>';die;
        if ($thread>1) {
            return true;
        } else {
            return false;
        }
        }else{
            return false;
        }
    }

    public function get_thread_id($sid, $rid) {
        if($sid!=''&& $rid!=''){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $where = 1;
        $additional = " s_id in ($sid,$rid) AND r_id in ($sid,$rid)";
        $thread = $dbhandler->get_all_result($identifier, $column = 't_id', $where, 'results', 0, false, $sort_by = 'timestamp', true, $additional);
        
        if (sizeof($thread)>0) {
            $tid = $thread[0]->t_id;
        return $tid;     
        }else{
            return false;
        }
        }else{
            return false;
        }
       
    }
    public function get_unread_msg_count($tid){
        $dbhandler = new PM_DBhandler;
        $identifier='MSG_CONVERSATION';
        $uid = wp_get_current_user()->ID;
        $where = 1;
        $status = 2;
        $additional = " t_id = $tid AND s_id NOT IN ($uid) AND status =$status ";
        $message = $dbhandler->get_all_result($identifier, $column = 'm_id', $where, 'results', 0, FALSE, $sort_by = 'timestamp', true,$additional);
         
        $count= sizeof($message);
        return $count;
        
    }


    public function get_message_of_thread($tid,$limit=false,$offset=0,$descending=true){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_CONVERSATION';
        $where = 1;
        $additional = " t_id = $tid";
        $message = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', $offset, $limit, $sort_by = 'timestamp', $descending,$additional);
       
        if(sizeof($message)>0){
           return $message;
       }
    }
    
    public function update_message_status_to_read($tid){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_CONVERSATION';
        $uid = wp_get_current_user()->ID;
        $where = 1;
        $status = 2;
        $additional = " t_id = $tid AND s_id NOT IN ($uid) AND status =$status ";
        $messages = $dbhandler->get_all_result($identifier, $column = 'm_id', $where, 'results', 0, $limit=false, $sort_by = 'timestamp', true,$additional);
        $data=array('status'=> '1');
        $data = $this->sanitize_request($data,$identifier);
        if(sizeof($messages)>0){
            foreach($messages as $message){
              $updated=$dbhandler->update_row($identifier, 'm_id',$message->m_id,$data);
           }
       }
         
      
    }
    
    public function delete_thread($tid){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $uid = wp_get_current_user()->ID;
        $message =$this->get_message_of_thread($tid, '1');
        $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
        $thread_desc=maybe_unserialize( $thread->thread_desc);
        $thread_desc["$uid"]['delete_mid']=$message[0]->m_id;
        $value= maybe_serialize($thread_desc);
        $data = array('thread_desc'=>$value);
        $return= $dbhandler->update_row('MSG_THREADS','t_id',$tid, $data);

    return $return;
    }
    
    public function update_typing_timestamp($tid,$activity){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $uid = $this->get_other_uid_of_thread($tid);
        $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
        $thread_desc=maybe_unserialize( $thread->thread_desc);
        $thread_desc["$uid"]['typing_timestamp']=current_time('mysql',true);
        $thread_desc["$uid"]['typing_status']=$activity;
        $value= maybe_serialize($thread_desc);
        $data = array('thread_desc'=>$value);
        $return= $dbhandler->update_row('MSG_THREADS','t_id',$tid, $data);

    return $return;
    }
    
    public function get_typing_timestamp($tid)
    {
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $uid = wp_get_current_user()->ID;
        $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
        $thread_desc=maybe_unserialize( $thread->thread_desc);
        return $thread_desc["$uid"]['typing_timestamp'];
    }
    
        public function get_typing_status($tid)
    {
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $uid = wp_get_current_user()->ID;
        $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
        $thread_desc=maybe_unserialize( $thread->thread_desc);
        return $thread_desc["$uid"]['typing_status'];
    }

    public function get_other_uid_of_thread($tid)
    {
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $uid = wp_get_current_user()->ID;
        $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
        if($thread->s_id==$uid){
            $other_uid = $thread->r_id;
        }else{
            $other_uid = $thread->s_id;
        }
        return $other_uid;
    }

    public function pm_filter_deleted_threads($threads){
        $filtered_threads=array();
        $uid= wp_get_current_user()->ID;
        foreach($threads as $thread){ 
        $thread_desc=maybe_unserialize( $thread->thread_desc);
        $delete_mid =(isset($thread_desc["$uid"]['delete_mid']))?$thread_desc["$uid"]['delete_mid']:0; 
        $message =$this->get_message_of_thread($thread->t_id, '1');
        $last_mid = $message[0]->m_id;
      
        if($delete_mid < $last_mid){
              $filtered_threads[]=$thread;
          }
    }
        return $filtered_threads;
        }
        
        public function pm_filter_deleted_message($messages,$tid){
            $dbhandler = new PM_DBhandler;
            $uid = wp_get_current_user()->ID;
            $filtered_messages=array();
            
           $thread = $dbhandler->get_row('MSG_THREADS',$tid,'t_id');
       

            $thread_desc=unserialize( $thread->thread_desc);
             if(isset($thread_desc) && isset($thread_desc["$uid"]['delete_mid']))
             {
                 $delete_mid = $thread_desc["$uid"]['delete_mid'];
             }else{
                    $delete_mid=0; 
             }   
         foreach($messages as $message){
              
                $message_mid = $message->m_id;
                if($message_mid>$delete_mid){
                    $filtered_messages[]=$message;
                }
            }
            return $filtered_messages;
        }
        
        public function get_data_of_thread($tid){
        $dbhandler = new PM_DBhandler;
        $identifier = 'MSG_THREADS';
        $where = 1;
        $additional  = " t_id in ($tid) ";
        $thread = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', 0, false, $sort_by = 'timestamp', true,$additional);
        return $thread;
        }

        public function pm_get_user_online_status($uid)
        {
            return get_user_meta( $uid, 'pm_login_status', true );
        }
        
        public function pm_get_profile_slug_by_id($uid)
        {
            $slug = $uid;
            $slug = apply_filters('profile_magic_get_filter_slug_by_id',$slug,$uid);  
            return $slug;
        }
        
        public function pm_get_uid_from_profile_slug($slug)
        {
            $uid = $slug;
            $uid = apply_filters('profile_magic_get_filter_uid_by_slug',$uid,$slug);  
            return $uid;
        }
        
        public function pm_get_user_profile_url($uid)
        {
            $slug = $this->pm_get_profile_slug_by_id($uid);
            $profile_url = $this->profile_magic_get_frontend_url('pm_user_profile_page', '');
            $profile_url = add_query_arg('uid',$slug, $profile_url); 
            return $profile_url;
        }


// class end
}
