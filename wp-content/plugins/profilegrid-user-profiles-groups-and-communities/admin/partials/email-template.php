<?php
$dbhandler = new PM_DBhandler;
$pm_activator = new Profile_Magic_Activator;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$identifier = 'EMAIL_TMPL';
$id = filter_input(INPUT_GET, 'id');
if($id==false || $id==NULL)
{
    $id=0;
}
else
{
    $row = $dbhandler->get_row($identifier,$id);
}
if(filter_input(INPUT_POST,'submit_tmpl'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_pm_add_email_tmpl' ) ) die( 'Failed security check' );
	$tmpl_id = filter_input(INPUT_POST,'tmpl_id');
	$exclude = array("_wpnonce","_wp_http_referer","submit_tmpl","tmpl_id","pm_field_list");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if($post!=false)
	{
		foreach($post as $key=>$value)
		{
		  $data[$key] = $value;
		  $arg[] = $pm_activator->get_db_table_field_type($identifier,$key);
		}
	}
	if($tmpl_id==0)
	{
	    $dbhandler->insert_row($identifier, $data,$arg);
	}
	else
	{
		$dbhandler->update_row($identifier,'id',$tmpl_id,$data,$arg,'%d');	
	}
	
	wp_redirect('admin.php?page=pm_email_templates');exit;
}
?>
<div class="uimagic">
  <form name="pm_add_email_template" id="pm_add_email_template" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <?php if($id==0): ?>
      <div class="uimheader">
        <?php _e( 'New Template','profile-grid' ); ?>
      </div>
      <?php else: ?>
      <div class="uimheader">
        <?php _e( 'Edit Template','profile-grid' ); ?>
      </div>
      <?php endif; ?>
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
      <div class="uimrow">
        <div class="uimfield">
          <?php _e('Title','profile-grid');?>
          <sup>*</sup></div>
        <div class="uiminput pm_required">
          <input type="text" name="tmpl_name" id="tmpl_name" value="<?php if(!empty($row)) echo esc_attr($row->tmpl_name); ?>" />
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Define a name for this template. Front-end user never sees this. Only for your reference.','profile-grid');?></div>
      </div>      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Email Subject','profile-grid' ); ?>
        </div>
        <div class="uiminput">
         <input type="text" name="email_subject" id="email_subject" value="<?php if(!empty($row)) echo esc_attr($row->email_subject); ?>" />
          <div class="errortext" id="icon_error"></div>
        </div>
        <div class="uimnote"><?php _e('Subject of the email sent to the user.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Email Template','profile-grid' ); ?>
        </div>
        <div class="uiminput">
          <?php
	   if(!empty($row))
	   {
	  	 $email_body =  $row->email_body;
	   }
	   else {$email_body ='';}
	   $settings = array(
    'wpautop' => true,
    'media_buttons' => true,
    'textarea_name' => 'email_body',
    'textarea_rows' => 20,
    'tabindex' => '',
    'tabfocus_elements' => ':prev,:next', 
    'editor_css' => '', 
    'editor_class' => '',
    'teeny' => false,
    'dfw' => false,
    'tinymce' => true, // <-----
    'quicktags' => true
);
	   //add_action('media_buttons', array($this,'pm_template_preview_button'));
	   add_action('media_buttons', array($this,'pm_fields_list_for_email'));
	   
	    wp_editor( $email_body, 'email_body',$settings);
		?>
          <div class="errortext"></div>
        </div>
        <div class="uimnote"><?php _e('Content of the email sent to the user. You can add profile field values using <i>Select A Field</i> dropdown. <hr/><strong>Important</strong> - if you do not have Password field in your Group sign up form, add Password field to the email template you plan to assign to user activation event. This will make sure that new users receive an auto-generated password inside their account activation email.','profile-grid');?></div>
      </div>
      
      
      
     
      <div class="buttonarea"> <a href="admin.php?page=pm_email_templates">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <input type="hidden" name="tmpl_id" id="tmpl_id" value="<?php echo $id;?>" />
        <?php wp_nonce_field('save_pm_add_email_tmpl'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_tmpl" id="submit_tmpl" onClick="return add_group_validation()"  />
        <div class="all_error_text" style="display:none;"></div>
        <div class="user_name_error" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>
<script>
function preview()
{
	html = jQuery("#email_body").val();
	var data = {
					'action': 'pm_template_preview',
					'html': html
				};
// We can also pass the url value separately from ajaxurl for front end AJAX implementations
	jQuery.post(pm_ajax_object.ajax_url, data, function(response) {
		
	});		
	return false;
}	
</script>