<?php
$dbhandler = new PM_DBhandler;
$pm_activator = new Profile_Magic_Activator;
$pmrequests = new PM_request;
$basicfunctions = new Profile_Magic_Basic_Functions($this->profile_magic, $this->version);
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$identifier = 'GROUPS';
$group_options = array();

$email_template =  $dbhandler->get_all_result('EMAIL_TMPL','id,tmpl_name');
$id = filter_input(INPUT_GET, 'id');
if($id==false || $id==NULL)
{
    $id=0;
}
else
{
    $row = $dbhandler->get_row($identifier,$id);
	if($row->group_options!="")$group_options = maybe_unserialize($row->group_options);
	if(!empty($row) && $row->leader_rights!="")
	{
		$leader_rights = maybe_unserialize($row->leader_rights);
	}
}
$args = array('meta_key'=> 'pm_group','meta_value'=> $id,'meta_compare' => '='); 
$all_users = $basicfunctions->get_all_users_for_combo_box($args);
$usrarray = str_replace('"','',$all_users);
if(filter_input(INPUT_POST,'submit_group'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_pm_add_group' ) ) die( 'Failed security check' );
	$groupid = filter_input(INPUT_POST,'group_id');
	$exclude = array("_wpnonce","_wp_http_referer","submit_group","group_id");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if($post!=false)
	{
		if(!isset($post['is_group_limit'])) $post['is_group_limit'] = 0;
		if(!isset($post['is_group_leader'])) $post['is_group_leader'] = 0;
		if(!isset($post['show_success_message'])) $post['show_success_message'] = 0;
		
		foreach($post as $key=>$value)
		{
		  $data[$key] = $value;
		  $arg[] = $pm_activator->get_db_table_field_type($identifier,$key);
		}
	}
	if($groupid==0)
	{
	    $gid = $dbhandler->insert_row($identifier, $data,$arg);
            $pmrequests->profile_magic_set_group_leader($gid);
            $section_data = array('gid'=>$gid,'section_name'=>'default','ordering'=>$gid);
            $section_arg = array('%d','%s','%d');
            $sid = $dbhandler->insert_row('SECTION', $section_data,$section_arg);
            $lastrow = $dbhandler->pm_count('FIELDS');
            $lastrow = $dbhandler->get_all_result('FIELDS','field_id',1,'var',0,1,'field_id','DESC');
            $ordering = $lastrow + 1;
            $field_option = 'a:15:{s:17:"place_holder_text";s:0:"";s:19:"css_class_attribute";s:0:"";s:14:"maximum_length";s:0:"";s:13:"default_value";s:0:"";s:12:"first_option";s:0:"";s:21:"dropdown_option_value";s:0:"";s:18:"radio_option_value";a:1:{i:0;s:0:"";}s:14:"paragraph_text";s:0:"";s:7:"columns";s:0:"";s:4:"rows";s:0:"";s:18:"term_and_condition";s:0:"";s:18:"allowed_file_types";s:0:"";s:12:"heading_text";s:0:"";s:11:"heading_tag";s:2:"h1";s:5:"price";s:0:"";}';
            $field_data = array('field_name'=>'User Email','field_type'=>'user_email','field_options'=>$field_option,'field_icon'=>0,'associate_group'=>$gid,'associate_section'=>$sid,'show_in_signup_form'=>1,'is_required'=>1,'ordering'=>$ordering,'field_key'=>'user_email');
            $field_arg = array('%s','%s','%s','%d','%d','%d','%d','%d','%d','%s');
            $newgid = $dbhandler->insert_row('FIELDS', $field_data,$field_arg);
            do_action('ProfileGrid_after_create_group',$gid);
            
	}
	else
	{
		$dbhandler->update_row($identifier,'id',$groupid,$data,$arg,'%d');	
		$pmrequests->profile_magic_set_group_leader($groupid);
	}
	
	wp_redirect('admin.php?page=pm_manage_groups');exit;
}
if(filter_input(INPUT_POST,'delete'))
{
	$selected = filter_input(INPUT_POST, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	foreach($selected as $gid)
	{
		$dbhandler->remove_row($identifier,'id',$gid,'%d');
	}
	
	wp_redirect('admin.php?page=pm_manage_groups');exit;
}

if(filter_input(INPUT_POST,'duplicate'))
{
	$selected = filter_input(INPUT_POST, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	foreach($selected as $gid)
	{
		$data =$dbhandler->get_row($identifier,$gid,'id','ARRAY_A');
                $oldgid = $data['id'];
		unset($data['id']);
		$newgid = $dbhandler->insert_row($identifier, $data);
                unset($data);
                $sections =  $dbhandler->get_all_result('SECTION','*',array('gid'=>$oldgid),'results',0,false,null,false,'','ARRAY_A');
                foreach($sections as $section)
                {
                    $oldsectionid = $section['id'];
                    unset($section['id']);
                    $section['gid']=$newgid;
                    $newsection_id = $dbhandler->insert_row('SECTION', $section);
                    unset($section);
                    $fields =  $dbhandler->get_all_result('FIELDS','*',array('associate_group'=>$oldgid,'associate_section'=>$oldsectionid),'results',0,false,null,false,'','ARRAY_A');
                    foreach($fields as $field)
                    {
                        unset($field['field_id']);
                        $lastrow = $dbhandler->get_all_result('FIELDS','field_id',1,'var',0,1,'field_id','DESC');
                        $ordering = $lastrow + 1;
                        $field['ordering'] = $ordering;
                        $field['field_key'] = $pmrequests->get_field_key($field['field_type'],$ordering);
                        $field['associate_group'] = $newgid;
                        $field['associate_section']=$newsection_id;
                        $dbhandler->insert_row('FIELDS',$field);
                        unset($field);
                    }
                    
                }
                
	}
	wp_redirect('admin.php?page=pm_manage_groups');exit;
}

?>

<div class="uimagic">
  <form name="pm_add_group" id="pm_add_group" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <?php if($id==0): ?>
      <div class="uimheader">
        <?php _e( 'New Group','profile-grid' ); ?>
      </div>
      <?php else: ?>
      <div class="uimheader">
        <?php _e( 'Edit Group','profile-grid' ); ?>
      </div>
      <?php endif; ?>
      <div class="uimsubheader">
        <?php
        $basicfunctions->null_field_notice();
		//Show subheadings or message or notice
		?>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e('Group Name','profile-grid');?>
          <sup>*</sup></div>
        <div class="uiminput pm_required">
          <input type="text" name="group_name" id="group_name" value="<?php if(!empty($row)) echo esc_attr($row->group_name); ?>" />
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Name of this Group. The name will appear on Single and All Groups page and Member Profiles.','profile-grid');?></div>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Group Description:','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <textarea name="group_desc" id="group_desc"><?php if(!empty($row))echo esc_attr($row->group_desc); ?>
</textarea>
        </div>
        <div class="uimnote"> <?php _e('Description or details of the group. It will appear on the individual Group page and as intro text on All Groups page.','profile-grid');?></div>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Group Icon/ Badge','profile-grid' ); ?>
        </div>
        <div class="uiminput" id="icon_html">
          <input id="group_icon" type="hidden" name="group_icon" class="icon_id" value="<?php if(!empty($row)) echo esc_attr($row->group_icon); ?>" />
          <input id="group_icon_button" class="button group_icon_button" type="button" value="<?php _e('Upload Icon','profile-grid');?>" />
          <?php if(!empty($row) && $row->group_icon!=0)
		  {
			echo wp_get_attachment_link($row->group_icon,array(50,50),false,true,false); 
		  }
		?>
          <img src="" width="50px" id="group_icon_img" style="display:none;" />
           <?php if(!empty($row) && $row->group_icon!=0)
		  {
			echo '<input type="button" name="remove_group_icon" id="remove_group_icon" class="remove_icon" value="Remove Icon" />'; 
		  }
		?>
          
          <div class="errortext" id="icon_error"></div>
        </div>
        <div class="uimnote"><?php _e('Group badge, icon or image. This will appear with group description and on member user profiles belonging to this group.','profile-grid');?> </div>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Hide Group Card','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="group_options[is_hide_group_card]" id="is_hide_group_card" type="checkbox"  class="pm_toggle" value="1" <?php if(!empty($group_options) && isset($group_options['is_hide_group_card']) && $group_options['is_hide_group_card']==1){ echo "checked";}?> style="display:none;" />
          <label for="is_hide_group_card"></label>
        </div>
          <div class="uimnote"><?php _e('Turn on to hide group card on its group page.','profile-grid');?></div>
      </div>
         <div class="uimrow" id="grouplimit">
        <div class="uimfield">
          <?php _e( 'Membership Limit','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="is_group_limit" id="is_group_limit" type="checkbox"  class="pm_toggle" value="1" <?php if(!empty($row) && $row->is_group_limit==1){ echo "checked";}?> style="display:none;"  onClick="pm_show_hide(this,'grouplimit_html')" />
          <label for="is_group_limit"></label>
        </div>
          <div class="uimnote"><?php _e('Limit the number of membership slots for this group. Turn off for unlimited members.','profile-grid');?></div>
      </div>
      <div class="childfieldsrow" id="grouplimit_html" style=" <?php if(!empty($row) && $row->is_group_limit==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
        <div class="uimrow">
          <div class="uimfield">
            <?php _e( 'Max no. of Members','profile-grid' ); ?>
          </div>
          <div class="uiminput <?php if(!empty($row) && $row->is_group_limit==1){echo 'pm_required';}?>">
            <input type="number" name="group_limit" min="0" id="group_limit" value="<?php if(!empty($row)) echo esc_attr($row->group_limit); ?>" />
            <div class="errortext"></div>
          </div>
          <div class="uimnote"><?php _e('Define the maximum number of members allowed for this group.','profile-grid');?></div>
        </div>
        <div class="uimrow" id="group_limit_message_html">
          <div class="uimfield">
            <?php _e( 'Limit Reached Message','profile-grid' ); ?>
          </div>
          <div class="uiminput">
            <?php if(isset($row))$group_limit_message = $row->group_limit_message; else $group_limit_message = ''; wp_editor( $group_limit_message, 'group_limit_message'/*,array('textarea_name' => 'wpuf_post_content', 'editor_class' => 'requiredField', 'teeny' => true, 'textarea_rows' => 8) */);?>
             <div class="errortext"></div>
          </div>
          <div class="uimnote"><?php _e('Users trying to register for this group will see this message once all membership slots are filled.','profile-grid');?></div>
        </div>
      </div>
      
      
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Associated WP Role','profile-grid' ); ?><sup>*</sup>
        </div>
        <div class="uiminput pm_select_required">
          <select name="associate_role" id="associate_role">
            <option value=""><?php _e('Select User Role','profile-grid');?></option>
            <?php
			  $roles = get_editable_roles();
			  foreach($roles as $key=>$role)
			  {?>
            <option value="<?php echo $key;?>" <?php if(!empty($row) && $row->associate_role==$key) echo 'selected'; ?>><?php echo $role['name']; ?></option>
            <?php }
			  ?>
          </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Inherit the access rights for the members of this group based on WP User Role.','profile-grid');?></div>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Group Leader','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="is_group_leader" id="is_group_leader" type="checkbox"  class="pm_toggle" value="1" <?php if(!empty($row) && $row->is_group_leader==1){ echo "checked";}?> style="display:none;"  onClick="pm_show_hide(this,'groupleaderhtml')" />
          <label for="is_group_leader"></label>
        </div>
          <div class="uimnote"><?php _e('A group leader is mentioned on the Group page prominently. The leader also has special Privileges to moderate the group.','profile-grid');?></div>
      </div>
      <div class="childfieldsrow" id="groupleaderhtml" style=" <?php  if(!empty($row) && $row->is_group_leader==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
        <div class="uimrow">
          <div class="uimfield">
            <?php _e( 'Select Leader:','profile-grid' ); ?>
          </div>
          <div class="uiminput <?php if(!empty($row) && $row->is_group_leader==1){echo 'pm_required pm_group_leader_name';}?>">
            <input name="leader_username" id="leader_username" type="text"  value="<?php if(!empty($row))echo esc_attr($row->leader_username); ?>" onkeyup="check_group_leader_name('<?php echo $usrarray;?>')" onblur="check_group_leader_name('<?php echo $usrarray;?>')" />
            <div class="errortext"></div>
            <div class="user_name_error"></div>
          </div>
            <div class="uimnote"><?php _e('Select from existing users. Start typing for real-time search.','profile-grid');?></div>
        </div>
        <?php /*?><div class="uimrow">
          <div class="uimfield">
            <?php _e( 'Leader Rights:','profile-grid' ); ?>
          </div>
          <div class="uiminput <?php if(!empty($row) && $row->is_group_leader==1){echo 'pm_checkbox_required';}?>">
            <ul class="uimradio">
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="invite_users"  <?php if(!empty($leader_rights) && in_array('invite_users',$leader_rights)) echo 'checked';?>>
                <?php _e('Invite Users','profile-grid' ); ?>
              </li>
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="edit_user"  <?php if(!empty($leader_rights) && in_array('edit_user',$leader_rights)) echo 'checked';?>>
                <?php _e('Edit User','profile-grid' ); ?>
              </li>
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="disable_user"  <?php if(!empty($leader_rights) && in_array('disable_user',$leader_rights)) echo 'checked';?>>
                <?php _e('Disable User','profile-grid' ); ?>
              </li>
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="email_group_users"  <?php if(!empty($leader_rights) && in_array('email_group_users',$leader_rights)) echo 'checked';?>>
                <?php _e('Email Users in Group','profile-grid' ); ?>
              </li>
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="add_group_annoucement"  <?php if(!empty($leader_rights) && in_array('add_group_annoucement',$leader_rights)) echo 'checked';?>>
                <?php _e('Add Group Annoucement','profile-grid' ); ?>
              </li>
              <li>
                <input type="checkbox" name="leader_rights[]" id="leader_rights[]" value="edit_group_badge"  <?php if(!empty($leader_rights) && in_array('edit_group_badge',$leader_rights)) echo 'checked';?>>
                <?php _e('Upload/ Edit Group Badge','profile-grid' ); ?>
              </li>
            </ul>
            <div class="errortext"></div>
          </div>
        </div><?php */?>
      </div>
        
      <?php do_action('profile_magic_group_option',$id,$group_options);?>
       
       <div class="uimrow" id="redirection">
        <div class="uimfield">
          <?php _e( 'After Registration, Redirect to','profile-grid' ); ?>
        </div>
        <div class="uiminput pm_checkbox_required">
         <ul class="uimradio">
              <li>
                <input type="radio" name="group_options[redirect]" id="redirect" value="none" <?php 
                if(!empty($group_options)){if(isset($group_options['redirect']) && $group_options['redirect']=='none'){ echo "checked";}}else {echo "checked";}
                        ?> onClick="pm_show_hide(this,'','redirect_page_html','redirect_url_html')">
                <?php _e('None','profile-grid' ); ?>
              </li>
               <li>
                <input type="radio" name="group_options[redirect]" id="redirect" value="page" <?php if(!empty($group_options) && isset($group_options['redirect']) && $group_options['redirect']=='page'){ echo "checked";}?> onClick="pm_show_hide(this,'redirect_page_html','redirect_url_html')">
                <?php _e('Page','profile-grid' ); ?>
              </li>
               <li>
                <input type="radio" name="group_options[redirect]" id="redirect" value="url" <?php if(!empty($group_options) && isset($group_options['redirect']) && $group_options['redirect']=='url'){ echo "checked";}?> onClick="pm_show_hide(this,'redirect_url_html','redirect_page_html')">
                <?php _e('URL','profile-grid' ); ?>
              </li>
          </ul>
        </div>
           <div class="uimnote"><?php _e('Redirect users to a page or URL after they successfully submit the group registration form.','profile-grid');?></div>
      </div>
      <div class="childfieldsrow" id="redirect_page_html" style=" <?php if(!empty($group_options) && isset($group_options['redirect']) && $group_options['redirect']=='page'){echo 'display:block;';} else { echo 'display:none;';} ?>">
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Page','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <?php 
		if(!empty($group_options['redirect_page_id']))
		{
			$selected = $group_options['redirect_page_id'];
		}
		else
		{
			$selected = 0;
		}
		$args = array(
			'depth'            => 0,
			'child_of'         => 0,
			'selected'         => $selected,
			'echo'             => 1,
			'name'             => 'group_options[redirect_page_id]'); ?>
        <?php wp_dropdown_pages($args); ?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Select the page where user will be redirected after registration. Usually this page will have relevant information related to the group of registration process.','profile-grid');?></div>
      </div>
      
      
      </div>
      
      <div class="childfieldsrow" id="redirect_url_html" style=" <?php if(!empty($group_options) && isset($group_options['redirect']) && $group_options['redirect']=='url'){echo 'display:block;';} else { echo 'display:none;';} ?>">
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'URL','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input type="url" name="group_options[redirect_url]" id="group_options[redirect_url]" value="<?php if(!empty($group_options) && isset($group_options['redirect_url'])) echo $group_options['redirect_url'];?>">
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Enter the URL of the page where the user will be redirected.','profile-grid');?> </div>
      </div>
      
      </div>
      
      
      <div class="uimrow" id="sucess_message">
        <div class="uimfield">
          <?php _e( 'Display a Message after Registration','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="show_success_message" id="show_success_message" type="checkbox"  class="pm_toggle" value="1" style="display:none;"  onClick="pm_show_hide(this,'success_message_html')" <?php if(!empty($row) && $row->show_success_message==1){ echo "checked";}?>/>
          <label for="show_success_message"></label>
        </div>
          <div class="uimnote"><?php _e('This message will appear to the user after the form is submitted. If redirection is turned on, it will appear for a few seconds before redirection is triggered.','profile-grid');?></div>
      </div>
      
       <div class="childfieldsrow" id="success_message_html" style=" <?php if(!empty($row) && $row->show_success_message==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
       <div class="uimrow" id="notification">
        <div class="uimfield">
          <?php _e( 'Success Message','profile-grid' ); ?>
        </div>
        <div class="uiminput">
        <?php if(isset($row))$success_message = $row->success_message; else $success_message = ''; wp_editor( $success_message, 'success_message');?>
        </div>
           <div class="uimnote"><?php _e('The contents of the success message. Rich text is supported.','profile-grid');?></div>
      </div>
       </div>
      
      
      <div class="uimrow" id="notification">
        <div class="uimfield">
          <?php _e( 'Member Email Notifications','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <input name="group_options[enable_notification]" id="enable_notification" type="checkbox"  class="pm_toggle" value="1" style="display:none;"  onClick="pm_show_hide(this,'notification_html')" <?php if(!empty($group_options) && isset($group_options['enable_notification']) && $group_options['enable_notification']==1){ echo "checked";}?>/>
          <label for="enable_notification"></label>
        </div>
          <div class="uimnote"><?php _e('Send relevant custom email notifications to the user on important registration events. If you have not created any custom email templates, you can save the group now, and come back later to assign them.','profile-grid');?></div>
      </div>
      
      <div class="childfieldsrow" id="notification_html" style=" <?php if(!empty($group_options) && isset($group_options['enable_notification']) && $group_options['enable_notification']==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
       
       <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'On Registration','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <select name="group_options[on_registration]" id="on_registration">
            <option value=""><?php _e('Select Email Template','profile-grid');?></option>
            <?php
			  foreach($email_template as $tmpl)
			  {?>
            <option value="<?php echo $tmpl->id;?>" <?php if(!empty($group_options) && isset($group_options['on_registration']) && $group_options['on_registration']==$tmpl->id) echo 'selected'; ?>><?php echo $tmpl->tmpl_name; ?></option>
            <?php }
			  ?>
          </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Sends this message to the user on successful registration form submission.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'On User Activate','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <select name="group_options[on_user_activate]" id="on_user_activate">
            <option value=""><?php _e('Select Email Template','profile-grid');?></option>
            <?php
			  foreach($email_template as $tmpl)
			  {?>
            <option value="<?php echo $tmpl->id;?>" <?php if(!empty($group_options) && isset($group_options['on_user_activate']) && $group_options['on_user_activate']==$tmpl->id) echo 'selected'; ?>><?php echo $tmpl->tmpl_name; ?></option>
            <?php }
			  ?>
          </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Sends this message to the user on his/ her user account activation in WP dashboard.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'On User Deactivate','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <select name="group_options[on_user_deactivate]" id="on_user_deactivate">
            <option value=""><?php _e('Select Email Template','profile-grid');?></option>
            <?php
			  foreach($email_template as $tmpl)
			  {?>
            <option value="<?php echo $tmpl->id;?>" <?php if(!empty($group_options) && isset($group_options['on_user_deactivate']) && $group_options['on_user_deactivate']==$tmpl->id) echo 'selected'; ?>><?php echo $tmpl->tmpl_name; ?></option>
            <?php }
			  ?>
          </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Sends this message to the user when his/ her user account is deactivate in WP dashboard.','profile-grid');?></div>
      </div>
       
         
        <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'On Password Change','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <select name="group_options[on_password_change]" id="on_password_change">
            <option value=""><?php _e('Select Email Template','profile-grid');?></option>
            <?php
			  foreach($email_template as $tmpl)
			  {?>
            <option value="<?php echo $tmpl->id;?>" <?php if(!empty($group_options) && isset($group_options['on_password_change']) && $group_options['on_password_change']==$tmpl->id) echo 'selected'; ?>><?php echo $tmpl->tmpl_name; ?></option>
            <?php }
			  ?>
          </select>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Sends this message to the user when his/ her password is changed.','profile-grid');?></div>
      </div>
       </div>
       
      
      <div class="buttonarea"> <a href="admin.php?page=pm_manage_groups">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <input type="hidden" name="group_id" id="group_id" value="<?php echo $id;?>" />
        <?php wp_nonce_field('save_pm_add_group'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_group" id="submit_group" onClick="return add_group_validation()"  />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>
<style>
  .ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 100px;
  }
  </style>
<script type="text/javascript">
	jQuery(function(){   
    jQuery("#leader_username").autocomplete({
        // code...
		 source: <?php echo '['.$all_users.']';?>
   });
});
</script>
