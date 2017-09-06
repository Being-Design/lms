<?php
$textdomain = 'profile-grid';
$path =  plugin_dir_url(__FILE__);
?>
<div class="uimrow" id="paidgroup">
  <div class="uimfield">
    <?php _e( 'Is Paid Group', 'profile-grid' ); ?>
  </div>
  <div class="uiminput">
    <input name="group_options[is_paid_group]" id="is_paid_group" type="checkbox"  class="pm_toggle" value="1" style="display:none;"  onClick="pm_show_hide(this,'paidgrouphtml')" <?php if(!empty($group_options) && isset($group_options['is_paid_group']) && $group_options['is_paid_group']==1){ echo "checked";}?>/>
    <label for="is_paid_group"></label>
  </div>
</div>
<div class="childfieldsrow" id="paidgrouphtml" style=" <?php if(!empty($group_options) && isset($group_options['is_paid_group']) && $group_options['is_paid_group']==1){echo 'display:block;';} else { echo 'display:none;';} ?>">
  <div class="uimrow">
    <div class="uimfield">
      <?php _e( 'Group Membership Price', 'profile-grid' ); ?>
    </div>
    <div class="uiminput <?php if(!empty($group_options) && isset($group_options['is_paid_group']) && $group_options['is_paid_group']==1){echo 'pm_required';}?>">
      <input type="number" name="group_options[group_price]" id="group_price" value="<?php if(!empty($group_options) && isset($group_options['group_price']))echo $group_options['group_price'];?>" />
      <div class="errortext"></div>
    </div>
    <div class="uimnote"><?php _e('While signing up, users will be charged this amount for successful registration. User account will be automatically activated after successful payment. Make sure you have properly configured payment settings for this to work.','profile-grid');?> </div>
  </div>
</div>
