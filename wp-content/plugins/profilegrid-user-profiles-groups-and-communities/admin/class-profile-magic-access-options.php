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
class Profile_Magic_access_options {
//put your code here

	
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

	}

	public function profile_magic_access_meta_box()
	{
		add_meta_box( 'profile-magic-access-metabox', __( 'ProfileGrid','profile-grid'),array( $this, 'pm_display_meta_box' ), 'page');
		add_meta_box( 'profile-magic-access-metabox', __( 'ProfileGrid','profile-grid'),array( $this, 'pm_display_meta_box' ), 'post');
                add_meta_box( 'profile-magic-access-metabox', __( 'ProfileGrid','profile-grid'),array( $this, 'pm_display_meta_box' ), 'profilegrid_blogs');
                add_meta_box( 'profile-magic-access-metabox', __( 'ProfileGrid','profile-grid'),array( $this, 'pm_display_meta_box' ), 'pg_groupwall');
	}
	
	public function pm_display_meta_box($post)
	{
		include 'partials/access-meta-box.php';
	}
	
	public function profile_magic_save_access_meta($post_id)
	{
		if(isset($post_id))
		{
			if(isset($_POST['pm_enable_custom_access']))
			{
				update_post_meta($post_id,'pm_enable_custom_access',$_POST['pm_enable_custom_access']);	
			}
			else
			{
				update_post_meta($post_id,'pm_enable_custom_access',0);
			}
			
			if(isset($_POST['pm_content_access']))
			{
				update_post_meta($post_id,'pm_content_access',$_POST['pm_content_access']);
			}
			
			if(isset($_POST['pm_content_access_group']))
			{
				update_post_meta($post_id,'pm_content_access_group',$_POST['pm_content_access_group']);
			}
		}
		
	}
	
	public function profile_magic_check_content_access($content)
	{
		$id = get_the_ID();
                $author_id =  get_the_author_meta('ID');
                $pmfriends = new PM_Friends_Functions;
                $pmrequests = new PM_request;
		if(get_post_meta($id,'pm_enable_custom_access',true)==1)
		{
			if(get_post_meta($id,'pm_content_access',true)==2)
			{
				  if ( is_user_logged_in() ) 
				  {
                                        $uid = get_current_user_id();
					if(get_post_meta($id,'pm_content_access_group',true)!='all')
					{
						$user_group = get_user_meta($uid,'pm_group',true);
						if(get_post_meta($id,'pm_content_access_group',true)!=$user_group)
						{
							 $error = $pmrequests->profile_magic_get_error_message('not_permitted','profile-grid');
					  		 $content = $this->profile_magic_content_access_message($error);
						}
					}
				  }
				  else
				  {
					  $error = $pmrequests->profile_magic_get_error_message('loginrequired','profile-grid');
					  $content = $this->profile_magic_content_access_message($error);
				  }
			}
                        
                        if(get_post_meta($id,'pm_content_access',true)==3)
                        {
                            if ( is_user_logged_in() ) 
				  {
                                        $author_friends = $pmfriends->profile_magic_my_friends($author_id);
                                        $uid = get_current_user_id();
                                        if($uid!=$author_id)
                                        {
                                            if(!in_array($uid,$author_friends))
                                            {
                                                $error = $pmrequests->profile_magic_get_error_message('not_permitted','profile-grid');
                                                $content = $this->profile_magic_content_access_message($error);
                                            }
                                        }
				  }
				  else
				  {
					  $error = $pmrequests->profile_magic_get_error_message('loginrequired','profile-grid');
					  $content = $this->profile_magic_content_access_message($error);
				  }
                        }
                        
                        if(get_post_meta($id,'pm_content_access',true)==4)
                        {
                            if ( is_user_logged_in() ) 
				  {
                                        $uid = get_current_user_id();
					if($uid!=$author_id)
					{
                                            $error = $pmrequests->profile_magic_get_error_message('not_permitted','profile-grid');
                                            $content = $this->profile_magic_content_access_message($error);
					}
				  }
				  else
				  {
					  $error = $pmrequests->profile_magic_get_error_message('loginrequired','profile-grid');
					  $content = $this->profile_magic_content_access_message($error);
				  }
                        }
                        
                        
		}
		return $content;	
	}
	
	public function profile_magic_content_access_message($error)
	{
		$content = '<div class="pm-login-box-error"><span>';
		$content .= $error;
		$content .= '</span>
		</div>';	
		return $content;
	}
	

	
	
	
// class end
}
