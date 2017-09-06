<div class="pmagic">
<!-----Form Starts----->
  <form class="pmagic-form pm-dbfl" method="post" action="" id="pm_add_blog_post" name="pm_add_blog_post" onsubmit="return profile_magic_blogpost_validation()" enctype="multipart/form-data">
   
       <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
            <div class="pm-field-lable">
                    <label for="blog_title"><?php _e('Title','profile-grid');?><sup class="pm_estric">*</sup></label>
              </div>
              <div class="pm-field-input pm_required">
                    <input title="Enter your title" type="text" class="" maxlength="" value="" id="blog_title" name="blog_title" placeholder="">
                    <div class="errortext" style="display:none;"></div>
              </div>
            </div>
            
        </div>
      
      <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
            <div class="pm-field-lable">
                    <label for="blog_description"><?php _e('Description','profile-grid');?></label>
              </div>
              <div class="pm-field-input">
                  <?php wp_editor('', 'blog_description',$settings);?>                
                    <div class="errortext" style="display:none;"></div>
              </div>
            </div>
        </div>
       <?php if($dbhandler->get_global_option_value('pm_blog_tags','0')==1):?>
      <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
            <div class="pm-field-lable">
                    <label for="blog_tags"><?php _e('Tags','profile-grid');?></label>
              </div>
              <div class="pm-field-input">
                   <input type="text" value="" tabindex="5" size="16" name="blog_tags" id="blog_tags" />
                    <div class="errortext" style="display:none;"></div>
              </div>
            </div>
            
        </div>
      <?php endif;?>
      <?php if($dbhandler->get_global_option_value('pm_blog_feature_image','0')==1):?>
       <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
            <div class="pm-field-lable">
                <label for="blog_image"><?php _e('Image','profile-grid');?></label>
            </div>
            <div class="pm-field-input pm_fileinput">
               <div class="pm_repeat">
                 <input title="" type="file" class="" id="" name="blog_image" data-filter-placeholder="" />
                  <div class="errortext" style="display:none;"></div>
               </div>
            </div>             
            </div>
        </div>
      <?php endif;?>
      
       <?php if($dbhandler->get_global_option_value('pm_blog_privacy_level','0')==1):?>
       <div class="pmrow">        
        <div class="pm-col">
        <div class="pm-form-field-icon"></div>
            <div class="pm-field-lable">
                <label for="blog_image"><?php _e('Content Privacy','profile-grid');?></label>
            </div>
            <div class="pm-field-input">
               <div class="pmradio">
                   <div class="pm-radio-option">
                    <input type="radio" name="pm_content_access" id="pm_content_access" value="1" checked/>
                <?php _e('Content accessible to Everyone','profile-grid' ); ?>
                   </div>
                    <div class="pm-radio-option">
                    <input type="radio" name="pm_content_access" id="pm_content_access" value="2" />
                    <?php _e('Content accessible to Logged In Users','profile-grid' ); ?>
                   </div>
                   <div class="pm-radio-option">
                    <input type="radio" name="pm_content_access" id="pm_content_access" value="3" />
                <?php _e('Content accessible to My Friends','profile-grid' ); ?>
                   </div>
                   <div class="pm-radio-option">
                    <input type="radio" name="pm_content_access" id="pm_content_access" value="5" />
                    <?php _e('Content accessible to my Group Member','profile-grid' ); ?>
                   </div>
                    <div class="pm-radio-option">
                    <input type="radio" name="pm_content_access" id="pm_content_access" value="4" />
                <?php _e('Content accessible to Only Me','profile-grid' ); ?>
                   </div>
                </div>
            </div>             
            </div>
        </div>
      <?php endif;?>
      
      <div class="all_errors" style="display:none;"></div>
        <div class="buttonarea pm-full-width-container">
           <input type="submit" value="Submit" name="pg_blog_submit">
		<?php wp_nonce_field( 'pg_blog_post' ); ?>
    </div>
  </form>
</div>