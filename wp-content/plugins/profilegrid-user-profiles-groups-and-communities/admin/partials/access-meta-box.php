<div class="uimrow">
        <div class="uimfield">
          <?php _e('Apply custom access settings?','profile-grid'); ?>
        </div>
        <div class="uiminput">
          <input name="pm_enable_custom_access" id="pm_enable_custom_access" type="checkbox"  class="pm_toggle" value="1" style="display:none;" onClick="pm_show_hide(this,'pm_access_html')" <?php checked(get_post_meta ($post->ID,'pm_enable_custom_access',true),'1'); ?> />
          <label for="pm_enable_custom_access"></label>
        </div>
      </div>
      <?php //echo get_post_meta($post->ID,'pm_enable_custom_access',true);die; ?>
 <div class="childfieldsrow" id="pm_access_html" style=" <?php if(get_post_meta($post->ID,'pm_enable_custom_access',true)!=1)echo 'display:none';else'display:block';?>" >
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Content Availability','profile-grid'); ?>
        </div>
        <div class="uiminput">
           <ul class="uimradio">
              <li>
                <input type="radio" name="pm_content_access" id="pm_content_access" value="1" onClick="pm_show_hide(this,'','pm_group_html')"  <?php checked(get_post_meta ($post->ID,'pm_content_access',true),'1'); ?>>
                <?php _e('Content accessible to Everyone','profile-grid' ); ?>
              </li>
              <li>
                <input type="radio" name="pm_content_access" id="pm_content_access" value="2" onClick="pm_show_hide(this,'pm_group_html')" <?php checked(get_post_meta($post->ID,'pm_content_access',true),'2'); ?>>
                <?php _e('Content accessible to Logged In Users','profile-grid' ); ?>
              </li>
             <li>
                <input type="radio" name="pm_content_access" id="pm_content_access" value="3" onClick="pm_show_hide(this,'','pm_group_html')" <?php checked(get_post_meta($post->ID,'pm_content_access',true),'3'); ?>>
                <?php _e('Content accessible to My Friends','profile-grid' ); ?>
              </li>
              <li>
                <input type="radio" name="pm_content_access" id="pm_content_access" value="4" onClick="pm_show_hide(this,'','pm_group_html')" <?php checked(get_post_meta($post->ID,'pm_content_access',true),'4'); ?>>
                <?php _e('Content accessible to Only Me','profile-grid' ); ?>
              </li>
            </ul>
        </div>
      </div>

  	<?php //echo get_post_meta($post->ID,'pm_content_access',true);die;?>
   <div class="childfieldsrow" id="pm_group_html" style=" <?php if(get_post_meta($post->ID,'pm_content_access',true)!=2)echo 'display:none';else'display:block';?>">
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Select the User Group that can see this content?','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <?php
                  $dbhandler = new PM_DBhandler;
		  $groups =  $dbhandler->get_all_result('GROUPS');
		  ?>
          <select name="pm_content_access_group" id="pm_content_access_group">
          <option value="all" <?php selected(get_post_meta ($post->ID,'pm_content_access_group',true),'all');?>><?php _e('All Registered Users','profile-grid');?></option>
          <?php foreach($groups as $group):?>
          <option value="<?php echo $group->id;?>" <?php selected(get_post_meta ($post->ID,'pm_content_access_group',true),$group->id);?>><?php echo $group->group_name;?></option>
          <?php endforeach;?>
          </select>
        </div>
      </div>
  </div>
    </div>
        
