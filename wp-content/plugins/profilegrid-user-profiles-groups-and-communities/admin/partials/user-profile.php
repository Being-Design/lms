<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$pmemails = new PM_Emails;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__); 
$id = filter_input(INPUT_GET, 'id');
$user_info = get_userdata($id);
$current_user = wp_get_current_user();
$avatar = get_avatar($user_info->user_email, 300 );
$userrole = $pmrequests->get_userrole_name($id);
$customfields = $pmrequests->get_user_custom_fields_data($id);
$gid = $pmrequests->profile_magic_get_user_field_value($id,'pm_group');
$groupinfo = $dbhandler->get_row('GROUPS',$gid);
if(isset($groupinfo))
{
    if($groupinfo->is_group_leader!=0){$group_leader = username_exists($groupinfo->leader_username);}else{$group_leader=0;}
    $sections =  $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid),'results',0,false,'ordering');
}

if(filter_input(INPUT_POST,'deactivate'))
{
	$uid = filter_input(INPUT_POST, 'uid');
	if($uid != $current_user->ID)
	{
		update_user_meta( $uid,'rm_user_status','1');
		$pmemails->pm_send_group_based_notification($gid,$uid,'on_user_deactivate');
	}
}

if(filter_input(INPUT_POST,'activate'))
{
	$uid = filter_input(INPUT_POST, 'uid');
	if($uid != $current_user->ID)
	{
		update_user_meta( $uid,'rm_user_status','0');
		$pmemails->pm_send_group_based_notification($gid,$uid,'on_user_activate');
	}
}

if(filter_input(INPUT_POST,'delete'))
{
	$uid = filter_input(INPUT_POST, 'uid');
	if($uid != $current_user->ID)
	wp_delete_user( $uid );
	wp_redirect('admin.php?page=pm_user_manager');exit;
}

//print_r($customfields);die;
if ($id == $current_user->ID )
{
	$class="rm_current_user";				
}
else
{
	$class="";
} 
?>
<div class="pmagic"> 
  
  <!-----Operationsbar Starts----->
  
  <div class="operationsbar">
    <div class="pmtitle"><?php echo $user_info->user_login;?></div>
    <div class="icons"> </div>
    <form name="pm_single_user" id="pm_single_user" method="post">
    <input type="hidden" value="<?php echo $id;?>" name="uid" />
    <div class="nav">
      <ul>
        <li><a href="user-edit.php?user_id=<?php echo $id;?>"><?php _e("Edit",'profile-grid');?></a></li>
        <?php if ($id != $current_user->ID ){ ?>
        <li><input type="submit" name="delete" value="Delete" onclick="return pg_confirm('<?php _e('Are you sure you want to delete this user permanently? You cannot undo this action.','profile-grid');?>')" /></li>
        <?php if($pmrequests->profile_magic_get_user_field_value($id,'rm_user_status')==1): ?>
        <li><input type="submit" name="activate" value="<?php _e("Activate",'profile-grid');?>" /></li>
        <?php else : ?>
        <li><input type="submit" name="deactivate" value="<?php _e("Deactivate",'profile-grid');?>" /></li>
        <?php endif; }?>
        
      </ul>
    </div>
   </form>
    
  </div>
  <!--------Operationsbar Ends-----> 
  
  <!----User Area Starts---->
  
  <div class="pm-user-area">
    <div class="pm-user-info">
      <div class="pm-profile-image"><?php echo $avatar;?> </div>
      <div class="pm-profile-fields">
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php _e('First Name','profile-grid');?></div>
          <div class="pm-field-value"><?php echo $user_info->first_name;?></div>
        </div>
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php _e('Last Name','profile-grid');?></div>
          <div class="pm-field-value"><?php echo $user_info->last_name;?></div>
        </div>
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php _e('Email','profile-grid');?></div>
          <div class="pm-field-value"><?php echo $user_info->user_email;?></div>
        </div>
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php _e('Role','profile-grid');?></div>
          <div class="pm-field-value"><?php echo $userrole;?></div>
        </div>
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php _e('Bio','profile-grid');?></div>
          <div class="pm-field-value"><?php echo $user_info->description;?></div>
        </div>
      </div>
    </div>
    <div id="tabs">
    <?php 
    if(isset($groupinfo)):?>
    <ul class="pm-profile-nav">
     <?php 
		foreach($sections as $section):
			echo '<li class="pm-profile-nav-item"><a href="#'.sanitize_key($section->section_name).'">'.$section->section_name.'</a></li>';
		endforeach;
        ?>
    </ul>
    
    
    <?php 
	foreach($sections as $section):
	?>
      <div class="pm-user-content" id="<?php echo sanitize_key($section->section_name);?>">
      <div class="pm-profile-fields">
        <?php $fields = $pmrequests->pm_get_backend_user_meta($id,$gid,$group_leader,'',$section->id,'"first_name","last_name","description","user_avatar","user_pass","user_name","user_email","heading","paragraph","confirm_pass"');
		
            if(!empty($fields)):
                foreach($fields as $field):?>
        <?php $field_value =  $pmrequests->profile_magic_get_user_field_value($id,$field->field_key,$field->field_type);
                $field_value = maybe_unserialize($field_value);
                $value = '';
                
                if(is_array($field_value)):
                    if($field->field_type=='address')
                    {
                        $options = maybe_unserialize($field->field_options);
                        foreach($field_value as $key=>$fv)
                        {
                            if(!isset($options[$key]))
                            {
                                unset($field_value[$key]);
                            }
                        }

                    }
                    foreach($field_value as $val)
                    {
                            if($val!='')
                            $value.= '<div class="rm-field-multiple-value">'.$val.'</div>';	
                    }
                else:
                        $value = $field_value; 
                endif;?>

        <?php if($value!=''):?>
        <div class="pm-profile-field-row">
          <div class="pm-field-label">
            <div class="pm-user-field-icon">
             <?php if(isset($field) && $field->field_icon!=0):
                echo wp_get_attachment_image($field->field_icon,array(16,16),true,false);
               endif; ?>
            </div>
            <?php echo $field->field_name; ?>:</div>
          <div class="pm-field-value"><?php echo $value;?></div>
        </div>
        <?php
                endif;
                endforeach;
                
            else:
                ?>
          <div class="pmnotice"><?php _e('No User Profile Fields in this section.','profile-grid');?></div>
          <?php
            endif;
	?>
      </div>
      </div>
    <?php
	endforeach; 
	?>
    <?php 
    else:
        echo '<div class="pm_message">'.__('This profile is not yet associated with any parent profile group. To make it visible please associate it with a group first.','profile-grid').'</div>';
    endif;
    ?>
    
    
    
    <?php /*?><div class="pm-user-content" id="tabs-1">
      <div class="pm-profile-fields">
      <?php foreach($customfields as $field => $value): ?>
        <div class="pm-profile-field-row">
          <div class="pm-field-label"><?php echo $field;?></div>
          <div class="pm-field-value"><?php echo $value;?></div>
        </div>
        <?php endforeach;?>
      </div>
      </div><?php */?>
      
       
    </div>
  </div>
</div>
<script type="text/javascript">
jQuery(function($) {
    $( "#tabs" ).tabs();  });
</script>