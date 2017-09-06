<?php $pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
$pmrequests = new PM_request;
$pagenum = filter_input(INPUT_GET, 'pagenum');
$rid = filter_input(INPUT_GET,'rid');
$pagenum = isset($pagenum) ? absint($pagenum) : 1;
?>
<div class="pmagic"> 
  <!-----Operationsbar Starts----->
  <div class="pm-group-view pm-dbfl">
    <div class="pm-header-section pm-dbfl pm-bg pm-border pm-radius5"> 
      <!-- cover page -->
      <div class="pm-cover-image pm-dbfl" <?php if($uid != $current_user->ID) echo 'id="pm-show-cover-image"';?> > 
          <?php 
          echo $pmrequests->profile_magic_get_cover_image($user_info->ID,'pm-cover-image');
          //echo wp_get_attachment_image($pmrequests->profile_magic_get_user_field_value($user_info->ID,''),'full',false,array('class'=>'pm-cover-image'));?>
        <?php if($uid == $current_user->ID):?>
        <ul class="pm-bg-dk dbfl">
          <li id="pm-change-cover-image">
              <i class="fa fa-camera-retro" aria-hidden="true"></i>
              <?php _e('Update Image','profile-grid');?></li>
        </ul>
        <?php endif;?>
      </div>
      <!-- header section -->
      <div class="pm-profile-title-header pm-dbfl">
        <div  <?php if($uid != $current_user->ID) echo 'id="pm-show-profile-image"';?> class="pm-profile-image pm-difl pm-pad10 <?php echo $class;?>"> <?php echo get_avatar($user_info->user_email,150,'',false,array('class'=>'pm-user'));?>
          <?php if($uid == $current_user->ID):?>
          <ul class="pm-bg-dk">
            <li id="pm-change-image">
                <i class="fa fa-camera-retro" aria-hidden="true"></i>
                <?php _e('Update Image','profile-grid');?></li>
          </ul>
          <?php endif;?>
        </div>
        <div class="pm-profile-title pm-difl pm-pad10">
          <div class="pm-user-name pm-dbfl pm-clip"><?php echo $pmrequests->pm_get_display_name($uid);?></div>
          <div class="pm-user-group-name pm-dbfl pm-clip">
              <i class="fa fa-users" aria-hidden="true"></i>
              <?php echo $groupinfo->group_name;?></div>
        </div>
        
        <div class="pm-group-icon pm-difr pm-pad10">
              <?php echo $pmrequests->profile_magic_get_group_icon($groupinfo,'pm-group-badge'); ?>
        </div>
      </div>
    </div>
    <div class="pm-profile-tabs pm-dbfl" id="pg-profile-tabs">
     <div class="pm-section-nav-horizental pm-dbfl">
        <ul class="pm-difl pm-profile-tab-wrap pm-border-bt">	
            <?php if($dbhandler->get_global_option_value('pm_enable_private_messaging','1')==1 || $dbhandler->get_global_option_value('pm_enable_blog','1')==1):?>
            <li class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg-about"><?php _e('About','profile-grid');?></a></li>
            <?php if($dbhandler->get_global_option_value('pm_enable_blog','1')==1):?>
            <li class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg-blog"><?php _e('Blog','profile-grid');?></a></li>
            <?php endif;?>
             <audio id="msg_tone" src="<?php echo $path ?>/images/sounds/msg_tone.mp3"></audio>
        
                <?php if($uid == $current_user->ID && $dbhandler->get_global_option_value('pm_enable_private_messaging','1')==1):?>
            <li class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg-messages" onClick="refresh_messenger();"><?php _e('Messages','profile-grid');?><b id="unread_thread_count" class=""></b></a></li>
            
               <?php endif;
            if($uid == $current_user->ID)
            {
                $pm_notification = new Profile_Magic_Notification();
                $unread_notification = $pm_notification->pm_get_user_unread_notification_count($current_user->ID);
                if($unread_notification==0){
                $unread_notification='';
                $unread_notification_class='';
                }else{
                    $unread_notification_class = "thread-count-show";
                }
            
            
            ?>            
            <li id="notification_tab" onclick="read_notification();" class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg-notifications"><?php _e('Notifications','profile-grid');?><b id="unread_notification_count" class="<?php echo $unread_notification_class; ?>"><?php echo $unread_notification;?></b></a></li>
            <?php
            } ?>
                <?php do_action( 'profile_magic_profile_tab',$uid,$gid);?>
         <?php 
         ?>
         </ul>
         <?php endif; ?>
      </div>
        
        <div id="pg-about" class="pm-difl">
        
            <div class="pm-section pm-dbfl" id="sections">
      <?php if($uid == $current_user->ID):
                $filter_uid = $pmrequests->pm_get_profile_slug_by_id($uid);
		$redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page',site_url('/wp-login.php'));
		$redirect_url = add_query_arg( 'user_id',$filter_uid,$redirect_url );
	?>
      <div class="pm-dbfl">    
      <div class="pm-edit-user pm-difl pm-pad10"> <a href="<?php echo $redirect_url;?>" class="pm_button pm-dbfl">
          <i class="fa fa-pencil" aria-hidden="true"></i>
          <?php _e('Edit','profile-grid');?></a> </div>
      <div class="pm-edit-user pm-difl pm-pad10"><a href="" class="pm_button pm-dbfl" id="pm-change-password" onclick="return false;">
          <i class="fa fa-key" aria-hidden="true"></i>
          <?php _e('Change Password','profile-grid');?></a></div></div>
      <?php endif; ?>
        <?php if(count($sections)>1):?>
      <div class="pm-section-nav-vertical pm-difl pm-border pm-radius5 pm-bg">
        <ul class="dbfl">
          <?php 
		  do_action( 'profile_magic_before_profile_section_tab',$uid,$gid);
		foreach($sections as $section):
			echo '<li class="pm-dbfl pm-border-bt pm-pad10"><a class="pm-dbfl" href="#'.sanitize_key($section->section_name).'">'.$section->section_name.'</a></li>';
		endforeach;
		do_action( 'profile_magic_after_profile_section_tab',$uid,$gid);
		
		?>
        </ul>
      </div>
        <?php endif;?>
      <?php 
	  do_action( 'profile_magic_before_profile_section_content',$uid,$gid);
	foreach($sections as $section):?>
      <div id="<?php echo sanitize_key($section->section_name);?>" class="pm-section-content pm-difl <?php if(count($sections)==1)echo 'pm_full_width_profile'; ?>">
        <?php 
		$fields = $pmrequests->pm_get_frontend_user_meta($uid,$gid,$group_leader,'',$section->id,'"user_avatar","user_pass","user_name","heading","paragraph","confirm_pass"');
		$pmhtmlcreator->get_user_meta_fields_html($fields,$uid);
		?>
      </div>
      <?php endforeach; 
	  do_action( 'profile_magic_after_profile_section_content',$uid,$gid);
	  ?>
    </div>
            
        </div>
        
        <?php if($dbhandler->get_global_option_value('pm_enable_blog','1')==1):?>
        <div id="pg-blog" class="pm-difl">
            <div id="pg-blog-container">
            <?php
            $pmhtmlcreator->pm_get_user_blog_posts($uid);
            ?>
            </div>
        </div>
        <?php endif;?>
   
       <?php if($uid == $current_user->ID && $dbhandler->get_global_option_value('pm_enable_private_messaging','1')==1): ?>
        <div id="pg-messages" class="pm-dbfl">
        <?php
        if(!isset($rid))
            $rid='';
            $pmhtmlcreator->pm_get_user_messenger($rid);  
        ?>
            </div>
        <?php endif;?>
         <?php if($uid == $current_user->ID):?>
        <div id="pg-notifications" class="pm-difl">
        <?php $pmhtmlcreator->pm_get_notification_html($uid); ?>
         </div>
        <?php endif;?>
        <?php do_action( 'profile_magic_profile_tab_content',$uid,$gid);?>

    </div>
      
      
  </div>
  
  <?php if($uid == $current_user->ID):?>
  <div class="pm-popup-mask"></div>
    <div id="pm-change-image-dialog">
    <div class="pm-popup-container pm-update-image-container pm-radius5">
      <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
          <i class="fa fa-camera-retro" aria-hidden="true"></i>
        <?php _e('Change Profile Image','profile-grid');?>
          <div class="pm-popup-close pm-difr">
              <img src="<?php echo $path;?>images/popup-close.png" height="24px" width="24px">
          </div>
      </div>
      <div class="pm-popup-image pm-dbfl pm-bg pm-pad10"> 
          <?php echo get_avatar($user_info->user_email,150,'',false,array('class'=>'pm-user','id'=>'avatar-edit-img'));?>
        <div class="pm-popup-action">
          <a type="button" class="btn btn-primary" id="change-pic">Change Image</a>
	  <div id="changePic" class="" style="display:none">
            <form id="cropimage" method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                <div class="pm-dbfl">
	           <label>Upload your image</label>
                <input type="file" name="photoimg" id="photoimg" />
                    </div>
            <input type="hidden" name="action" value="pm_upload_image" id="action" />
            <input type="hidden" name="status" value="" id="status" />
            <input type="hidden" name="filepath" value="<?php echo $path;?>" />
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_info->ID); ?>" />
            <input type="hidden" name="user_meta" id="user_meta" value="<?php echo esc_attr('pm_user_avatar'); ?>" />
            <input type="hidden" id="x" name="x" />
            <input type="hidden" id="y" name="y" />
            <input type="hidden" id="w" name="w" />
            <input type="hidden" id="h" name="h" />
            <div id="preview-avatar-profile"></div>
	    <div id="thumbs" style="padding:5px; width:600px"></div>	
            </form>
            <div class="modal-footer">
                <button type="button" id="btn-cancel" class="btn btn-default">Cancel</button>
                <button type="button" id="btn-crop" class="btn btn-primary">Crop & Save</button>
            </div>
          </div>
          <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_info->ID); ?>" />
            <input type="hidden" name="user_meta" value="<?php echo esc_attr('pm_user_avatar'); ?>" />
            <input type="submit" value="Remove" name="remove_image" />
          </form>
        </div>
        <p class="pm-popup-info pm-dbfl pm-pad10">
          <?php _e('For best visibility choose square image with minimum size of 200 x 200 pixels','profile-grid');?>
        </p>
      </div>
    </div>
  </div>
<div class="pm-popup-mask"></div>
  <div id="pm-change-cover-image-dialog">
    <div class="pm-popup-container pm-update-image-container pm-radius5">
      <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
        <?php _e('Change Cover Image','profile-grid');?>
          <div class="pm-popup-close pm-difr">
              <img src="<?php echo $path;?>images/popup-close.png" height="24px" width="24px">
          </div>
      </div>
      <div class="pm-popup-image pm-dbfl pm-pad10 pm-bg"> 
          <?php echo wp_get_attachment_image($pmrequests->profile_magic_get_user_field_value($user_info->ID,'pm_cover_image'),array(85,85),true,array('class'=>'pm-cover-image','id'=>'cover-edit-img'));?>
        <div class="pm-popup-action pm-dbfl pm-pad10">
          <a type="button" class="btn btn-primary" id="change-cover-pic"><?php _e('Change Cover Image','profile-grid');?></a>
	  <div id="changeCoverPic" class="" style="display:none">
            <form id="cropcoverimage" method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin-ajax.php' );?>">
	    <label><?php _e('Upload Your Cover Image','profile-grid');?></label>
            <input type="file" name="coverimg" id="coverimg"  />
            <input type="hidden" name="action" value="pm_upload_cover_image" id="action" />
            <input type="hidden" name="cover_status" value="" id="cover_status" />
            <input type="hidden" name="cover_filepath" value="<?php echo $path;?>" />
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_info->ID); ?>" />
            <input type="hidden" id="cx" name="cx" />
            <input type="hidden" id="cy" name="cy" />
            <input type="hidden" id="cw" name="cw" />
            <input type="hidden" id="ch" name="ch" />
            <input type="hidden" id="cover_minwidth" name="cover_minwidth" value="" />
           
            <div id="preview-cover-image"></div>
	    <div id="thumbs" style="padding:5px; width:600px"></div>	
            </form>
            <div class="modal-footer">
                <button type="button" id="btn-cover-cancel" class="btn btn-default">Cancel</button>
                <button type="button" id="btn-cover-crop" class="btn btn-primary">Crop & Save</button>
            </div>
          </div>
            
            
          <form method="post" action="" enctype="multipart/form-data">     
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_info->ID); ?>" />
            <input type="hidden" name="user_meta" value="<?php echo esc_attr('pm_cover_image'); ?>" />
            <input type="submit" value="<?php _e('Remove','profile-grid');?>" name="remove_image" />
          </form>
        </div>
        <p class="pm-popup-info pm-dbfl pm-pad10">
          <?php _e('For best visibility choose a landscape aspect ratio image with size of <span id="pm-cover-image-width">1200</span> x 300 pixels','profile-grid');?>
        </p>
      </div>
    </div>
  </div>
<div class="pm-popup-mask"></div>    
  <div id="pm-change-password-dialog">
    <div class="pm-popup-container pm-radius5">
      <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
          <i class="fa fa-key" aria-hidden="true"></i>
        <?php _e('Change Password','profile-grid');?>
          <div class="pm-popup-close pm-difr"><img src="<?php echo $path;?>images/popup-close.png" height="24px" width="24px">
                                                   </div>
      </div>
      <div class="pm-popup-image">
        <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
          <div class="pm-login-box">
    <div class="pm-login-box-error pm-pad10" style="display:none;" id="pm_reset_passerror"></div>
    <!-----Form Starts----->
    <form name="resetpassform" id="resetpassform" method="post" autocomplete="off" onsubmit="return pm_frontend_change_password(this)">
      <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user_info->ID); ?>" />
      <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" placeholder="<?php _e('New password','profile-grid');?>" required="required"/>
      <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" placeholder="<?php _e('Repeat new password','profile-grid');?>" required="required"/>
      <div class="pm-login-box-bottom-container pm-dbfl">
        <input type="submit" name="change_password" id="resetpass-button" class="button pm-dbfl" value="<?php _e( 'Change Password','profile-grid' ); ?>" />
      </div>
    </form>
  </div>
        </div>
      </div>
    </div>
  </div>
    
  <?php else: ?>
<div class="pm-popup-mask"></div>    

<div id="pm-show-profile-image-dialog"><div class="pm-popup-container">
    <?php echo get_avatar($user_info->user_email,512);?></div>
</div>

<div class="pm-popup-mask"></div>    
<div id="pm-show-cover-image-dialog"><div class="pm-popup-container">
    <?php  echo $pmrequests->profile_magic_get_cover_image($user_info->ID,'pm-cover-image');?></div>
</div>
<?php endif;?>
</div>
