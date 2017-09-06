<?php 
$gids = implode(',',$_POST['groups']);
$dbhandler = new PM_DBhandler;
$additional = "associate_group in($gids) and field_type not in('file','heading','paragraph','term_checkbox','user_avatar','user_pass','confirm_pass')";
$fields = $dbhandler->get_all_result('FIELDS','*',1,'results',0,false,null,false,$additional);
//print_r(count($fields));
?>
<div class="uimrow">
    <div class="uimfield">
      <?php _e( 'Select Field(s):','profile-grid' ); ?>
    </div>
    <div class="uiminput pm_select_required">
      <select name="pm_fields[]" id="pm_fields" multiple>
        <?php
        foreach($fields as $field)
        {?>
            <option value="<?php echo $field->field_id;?>"><?php echo $field->field_name; ?></option>
        <?php }
        ?>
      </select>
      <div class="errortext"></div>
    </div>
    <div class="uimnote"><?php _e('Step 2: Now select the fields you wish to export for each user in above selected Group(s). If you want to export everything, click on a field and press Ctrl+A or ⌘+A to select all of them.','profile-grid');?></div>
</div>