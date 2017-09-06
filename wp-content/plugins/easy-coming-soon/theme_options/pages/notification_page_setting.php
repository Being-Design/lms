<?php

$notification_default_settings = array(

	// notification page settings

	'mailto'=>'no',
	'email_address'=>'',
	'message'=>'Thanks for subscribing to this page',
	'sb_btn'=>'',
	'placeholder_text'=>'',
	'user_message'=>'Hey Some one with email id => [email] has subscribed to the Coming Soon page'
	);
?>
<div class="block ui-tabs-panel active" id="option-ui-id-3" >
<?php $current_options = wp_parse_args( get_option( 'soon_page_notification_settings', array() ), $notification_default_settings );
	
		if(isset($_POST['easy-coming-soon_lite_settings_save_3'])) 
		{
				

				$current_options['mailto']=$_POST['mailto'];
				$current_options['email_address']=$_POST['email_address'];	
				$current_options['message']=$_POST['message'];
				$current_options['sb_btn']=$_POST['sb_btn'];
				$current_options['placeholder_text']=$_POST['placeholder_text'];
				$current_options['user_message']=$_POST['user_message'];
				
				update_option('soon_page_notification_settings',$current_options);

		}
			
		if(isset($_POST['easy-coming-soon_lite_settings_reset_3'])) 
		{
			$current_options['mailto']='no';
			$current_options['email_address']='';
			$current_options['message']='Thanks for subscribing this page';
			$current_options['sb_btn']='';
			$current_options['placeholder_text']='';
			$current_options['user_message']='Hey Some one with email id => [email] has subscribed to the Coming Soon page';

			update_option('soon_page_notification_settings',$current_options);
		}
	?>
	<form method="post" action="#section_notification">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Notification Forms Settings','easy-coming-soon');?></h2></td>
				<td style="width:30%;">
					<div class="easy-coming-soon_lite_settings_loding" id="easy-coming-soon_loding_3_image"></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_3_success" ><?php _e('Options data successfully Saved','easy-coming-soon');?></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_3_reset" ><?php _e('Options data successfully reset','easy-coming-soon');?></div>
				</td>
				<td style="text-align:right;">					
					<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_3" value="Restore Defaults">&nbsp;
					<input class="button button-primary button-large" type="submit" name="easy-coming-soon_lite_settings_save_3" value="Save Options">
				</td>
				</tr>
			</table>			
		</div>	
		<?php wp_nonce_field('easy-coming-soon_lite_customization_nonce_gernalsetting','easy-coming-soon_lite_gernalsetting_nonce_customization'); ?>
		
		<div class="section">
            <div class="element">
            	<h3><?php _e('Mail Option','easy-coming-soon') ?></h3>
                <select name="mailto" id="mailto" onchange="mail_field_display()">
                    <option onclick="hideAll()" value="no" <?php if($current_options['mailto'] == 'no'){ echo "selected"; } ?>><?php _e('Do not display signup field','easy-coming-soon') ?></option>
                    <option onclick="show_mail_field()" value="yes" <?php if($current_options['mailto'] == 'yes'){ echo "selected"; } ?>><?php _e('Mail to the owner','easy-coming-soon') ?></option>
                </select>
            </div>
        </div>

        <div class="section email_address_section">
            <div class="element">
            	<h3><?php _e('Owner Email Address','easy-coming-soon') ?></h3>
                <input onchange="return email_check();" type="text" id="email_address" name="email_address" placeholder="example@gmail.com" value="<?php if(!empty($current_options['email_address'])){ echo $current_options['email_address']; } ?>">
            </div>
        </div>

        <div class="section message_section">
            <div class="element">
            	<h3><?php _e('Display Message After User Subscribing','easy-coming-soon') ?></h3>
                <input type="text" id="message" name="message" placeholder="Thanks you for subscribing" value="<?php if(!empty($current_options['message'])){ echo $current_options['message']; } ?>">
            </div>
        </div>

        <div class="section sb_btn_section">
            <div class="element">
            	<h3><?php _e('Subscribe Button','easy-coming-soon') ?></h3>
                <input type="text" id="sb_btn" name="sb_btn" placeholder="Notify Me!" value="<?php if(!empty($current_options['sb_btn'])){ echo $current_options['sb_btn']; } ?>">
            </div>
        </div>

        <div class="section placeholder_text_section">
            <div class="element">
            	<h3><?php _e('Email Textbox Placeholder','easy-coming-soon') ?></h3>
                <input type="text" id="placeholder_text" name="placeholder_text" placeholder="Enter your email to notify" value="<?php if(!empty($current_options['placeholder_text'])){ echo $current_options['placeholder_text']; } ?>">
            </div>
        </div>
		
		<div class="section placeholder_text_section">
            <div class="element">
            	<h3><?php _e('Custom Email Message to Owner','easy-coming-soon') ?></h3>
                <textarea rows="5" type="text" id="user_message" name="user_message" placeholder="Custom Email message to owner"><?php if(!empty($current_options['user_message'])){ echo $current_options['user_message']; } ?></textarea>
				
				<p><?php echo sprintf (__("Use Shortcode <strong style='font-size:15px;'>[email]</strong> to include the email address entered by the Subscriber","easy-coming-soon")); ?></p>
			</div>
        </div>


		<div id="button_section">
			<input class="button button-primary button-large" type="submit" name="easy-coming-soon_lite_settings_save_3" value="Save Options">&nbsp;
			<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_3" value="Restore Defaults">
		</div>
	</form>
</div>
<script>

<?php if($current_options['mailto'] == 'no'){ echo "hideAll();"; } ?>

function mail_field_display()
{
	var mailfield = jQuery("#mailto").val();
	//alert(mailfield)
	if(mailfield=="no"){ hideAll();	}
	if(mailfield=="yes"){ show_mail_field(); }
	
}

function hideAll()
{
	jQuery('.placeholder_text_section').hide();
	jQuery('.sb_btn_section').hide();
	jQuery('.message_section').hide();
	jQuery('.email_address_section').hide();
	
}

function show_mail_field()
{
	jQuery('.placeholder_text_section').show();
	jQuery('.sb_btn_section').show();
	jQuery('.message_section').show();
	jQuery('.email_address_section').show();
	
}

function email_check()
{
    
    var email_address = jQuery('#email_address').val();

	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

	if(email_address == '')
	{
		alert("Please enter your email");
		return false;
	}
			
	if(!regex.test(email_address))
	{
		alert("Please Enter a valid Email Address");
		return false;
	}	
}

</script>