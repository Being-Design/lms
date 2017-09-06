<?php 
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$pm_customfields = new PM_Custom_Fields;
?>
<div class="pmagic"> 
  <!-----Operationsbar Starts----->
  <div class="pm-group-view pm-dbfl pm-bg-lt">
    <form class="pmagic-form pm-dbfl" method="post" action="" id="pm_edit_form" name="pm_edit_form" onsubmit="return profile_magic_frontend_validation(this)" enctype="multipart/form-data">
      <div class="pm-edit-heading">
        <h1>
          <?php _e('Edit Profile','profile-grid');?>
        </h1>
        <span class="pm-edit-action">
        <input type="submit" name="edit_profile" 
   value="<?php _e('Save','profile-grid');?>" />
        <input type="button" name="expand" id="expand" value="<?php _e('Expand All','profile-grid');?>" onclick="pm_expand_all_conent()" />
        <input type="button" id="collapse" name="collapse" value="<?php _e('Collapse All','profile-grid');?>" onclick="pm_collapse_all_conent()" />
        </span></div>
      <div id="pm-accordion" class="pm-dbfl">
        <?php 
foreach($sections as $section):
$exclude = " and field_type not in('user_name','user_email','user_avatar','user_pass','confirm_pass','paragraph','heading')";
$fields =  $dbhandler->get_all_result('FIELDS', $column = '*',array('associate_group'=>$gid,'associate_section'=>$section->id),'results',0,false, $sort_by = 'ordering',false,$exclude);

echo '<h3 class="pm-accordian-title pm-dbfl pm-border pm-bg pm-pad10">'.$section->section_name.'</h3>';
	?>
        <div id="<?php echo sanitize_key($section->section_name);?>" class="pm-accordian-content pm-dbfl pm-pad10">
          <?php 
		 	 if(isset($fields) && !empty($fields))
			 {
				 foreach($fields as $field)
				 {
					echo '<div class="pmrow">';
					$value = $pmrequests->profile_magic_get_user_field_value($current_user->ID,$field->field_key);
					$pm_customfields->pm_get_custom_form_fields($field,$value,$this->profile_magic);
					echo '</div>';	 
				 }
				 echo '<div class="all_errors" style="display:none;"></div>';
				 
			 }

	?>
        </div>
        <?php	
endforeach;
?>
      </div>
    </form>
  </div>
</div>
