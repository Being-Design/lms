<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = 'profile-grid';
$path =  plugin_dir_url(__FILE__);
$identifier = 'SETTINGS';
if(filter_input(INPUT_POST,'submit_settings'))
{
	$retrieved_nonce = filter_input(INPUT_POST,'_wpnonce');
	if (!wp_verify_nonce($retrieved_nonce, 'save_payment_settings' ) ) die( 'Failed security check' );
	$exclude = array("_wpnonce","_wp_http_referer","submit_settings");
	$post = $pmrequests->sanitize_request($_POST,$identifier,$exclude);
	if($post!=false)
	{
		if(!isset($post['pm_paypal_test_mode'])) $post['pm_paypal_test_mode'] = 0;
		foreach($post as $key=>$value)
		{
			$dbhandler->update_global_option_value($key,$value);
		}
	}
	
	wp_redirect('admin.php?page=pm_settings');exit;
}
?>

<div class="uimagic">
  <form name="pm_user_settings" id="pm_user_settings" method="post">
    <!-----Dialogue Box Starts----->
    <div class="content">
      <div class="uimheader">
        <?php _e( 'Payments', 'profile-grid' ); ?>
      </div>
     
      <div class="uimsubheader">
        <?php
		//Show subheadings or message or notice
		?>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Payment Processor:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
           <select name="pm_payment_method" id="pm_payment_method">
           <option value="paypal" selected><?php _e('PayPal','profile-grid');?></option>
           </select>
        </div>
        <div class="uimnote"> <?php _e('Select the payment system(s) you want to use for accepting payments. Make sure you configure them right.','profile-grid');?> </div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Test Mode:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
           <input name="pm_paypal_test_mode" id="pm_paypal_test_mode" type="checkbox" <?php checked($dbhandler->get_global_option_value('pm_paypal_test_mode'),'1'); ?> class="pm_toggle" value="1" style="display:none;" />
          <label for="pm_paypal_test_mode"></label>
        </div>
        <div class="uimnote"> <?php _e('This will put ProfileGrid payments on test mode. Useful for testing payment system.','profile-grid');?> </div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'PayPal Email:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
         <input name="pm_paypal_email" id="pm_paypal_email" type="text" value="<?php echo $dbhandler->get_global_option_value('pm_paypal_email'); ?>" />
        </div>
        <div class="uimnote"> <?php _e('Your PayPal account email, to which you will accept the payments.','profile-grid');?> </div>
      </div>
      
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Currency:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
         <select name="pm_paypal_currency" id="pm_paypal_currency">
          <option value="USD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'USD');?>><?php _e('US Dollars','profile-grid');?> ($)</option>
          <option value="EUR" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'EUR');?>><?php _e('Euros','profile-grid');?> (&euro;)</option>
          <option value="GBP" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'GBP');?>><?php _e('Pounds Sterling','profile-grid');?> (&pound;)</option>
          <option value="AUD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'AUD');?>><?php _e('Australian Dollars','profile-grid');?> ($)</option>
          <option value="BRL" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'BRL');?>><?php _e('Brazilian Real','profile-grid');?> (R$)</option>
          <option value="CAD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'CAD');?>><?php _e('Canadian Dollars','profile-grid');?> ($)</option>
          <option value="CZK" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'CZK');?>><?php _e('Czech Koruna','profile-grid');?></option>
          <option value="DKK" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'DKK');?>><?php _e('Danish Krone','profile-grid');?></option>
          <option value="HKD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'HKD');?>><?php _e('Hong Kong Dollar','profile-grid');?> ($)</option>
          <option value="HUF" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'HUF');?>><?php _e('Hungarian Forint','profile-grid');?></option>
          <option value="ILS" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'ILS');?>><?php _e('Israeli Shekel','profile-grid');?> (&#x20aa;)</option>
          <option value="JPY" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'JPY');?>><?php _e('Japanese Yen','profile-grid');?> (&yen;)</option>
          <option value="MYR" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'MYR');?>><?php _e('Malaysian Ringgits','profile-grid');?></option>
          <option value="MXN" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'MXN');?>><?php _e('Mexican Peso','profile-grid');?> ($)</option>
          <option value="NZD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'NZD');?>><?php _e('New Zealand Dollar','profile-grid');?> ($)</option>
          <option value="NOK" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'NOK');?>><?php _e('Norwegian Krone','profile-grid');?></option>
          <option value="PHP" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'PHP');?>><?php _e('Philippine Pesos','profile-grid');?></option>
          <option value="PLN" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'PLN');?>><?php _e('Polish Zloty','profile-grid');?></option>
          <option value="SGD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'SGD');?>><?php _e('Singapore Dollar','profile-grid');?> ($)</option>
          <option value="SEK" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'SEK');?>><?php _e('Swedish Krona','profile-grid');?></option>
          <option value="CHF" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'CHF');?>><?php _e('Swiss Franc','profile-grid');?></option>
          <option value="TWD" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'TWD');?>><?php _e('Taiwan New Dollars','profile-grid');?></option>
          <option value="THB" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'THB');?>><?php _e('Thai Baht','profile-grid');?> (&#3647;)</option>
          <option value="INR" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'INR');?>><?php _e('Indian Rupee','profile-grid');?> (&#x20B9;)</option>
          <option value="TRY" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'TRY');?>><?php _e('Turkish Lira','profile-grid');?> (&#8378;)</option>
          <option value="RIAL" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'RIAL');?>><?php _e('Iranian Rial','profile-grid');?></option>
          <option value="RUB" <?php selected( $dbhandler->get_global_option_value('pm_paypal_currency'),'RUB');?>><?php _e('Russian Rubles','profile-grid');?></option>
        </select>
        </div>
        <div class="uimnote"> <?php _e('Default Currency for accepting payments. Usually, this will be default currency in your PayPal account.','profile-grid');?> </div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'PayPal Page Style:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
         <input name="pm_paypal_page_style" id="pm_paypal_page_style" type="text" value="<?php echo $dbhandler->get_global_option_value('pm_paypal_page_style'); ?>" />
        </div>
        <div class="uimnote"><?php _e('If you have created checkout pages in your PayPal account and want to show a specific page, you can enter it’s name here.','profile-grid');?></div>
      </div>
      
      <div class="uimrow">
        <div class="uimfield">
          <?php _e( 'Currency Symbol Position:', 'profile-grid' ); ?>
        </div>
        <div class="uiminput">
         <select id="pm_currency_position" name="pm_currency_position">
         <option value="before" <?php selected($dbhandler->get_global_option_value('pm_currency_position'),'before');?>><?php _e('Before - $10','profile-grid');?></option>
         <option value="after" <?php selected($dbhandler->get_global_option_value('pm_currency_position'),'after');?>><?php _e('After - 10$','profile-grid');?></option>
         </select>
        </div>
        <div class="uimnote"><?php _e('Choose the location of the currency sign.','profile-grid');?></div>
      </div>
      
    

      <div class="buttonarea"> <a href="admin.php?page=pm_settings">
        <div class="cancel">&#8592; &nbsp;
          <?php _e('Cancel','profile-grid');?>
        </div>
        </a>
        <?php wp_nonce_field('save_payment_settings'); ?>
        <input type="submit" value="<?php _e('Save','profile-grid');?>" name="submit_settings" id="submit_settings" />
        <div class="all_error_text" style="display:none;"></div>
      </div>
    </div>
  </form>
</div>