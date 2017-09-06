<?php
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	$learndash_plus_paypal_settings = get_option( 'learndash_plus_paypal_settings' );
	$paypal_email =  isset($learndash_plus_paypal_settings['paypal_email'])? $learndash_plus_paypal_settings['paypal_email']:"";
	$paypal_currency =  isset($learndash_plus_paypal_settings['paypal_currency'])? $learndash_plus_paypal_settings['paypal_currency']:"USD";
	$paypal_country =  isset($learndash_plus_paypal_settings['paypal_country'])? $learndash_plus_paypal_settings['paypal_country']:"US";
	$paypal_cancel_url =  isset($learndash_plus_paypal_settings['paypal_cancel_url'])? $learndash_plus_paypal_settings['paypal_cancel_url']:get_bloginfo('wpurl');
	$paypal_return_url =  isset($learndash_plus_paypal_settings['paypal_return_url'])? $learndash_plus_paypal_settings['paypal_return_url']:get_bloginfo('wpurl');
	$paypal_notify_url =  get_bloginfo('wpurl')."/?ldp-paypal-ipn=1";
	$paypal_sandbox =  isset($learndash_plus_paypal_settings['paypal_sandbox'])? $learndash_plus_paypal_settings['paypal_sandbox']:"";
	
	if( isset($_POST[ "update_LearnDashPaypalSettings" ]) ) {
        // Read their posted value
		
        $paypal_email =  $_POST['learndash_plus_paypal_email'];
		$paypal_currency = $_POST['learndash_plus_paypal_currency'];
		$paypal_country = $_POST['learndash_plus_paypal_country'];
		$paypal_cancel_url = $_POST['learndash_plus_paypal_cancel_url'];
		$paypal_return_url = $_POST['learndash_plus_paypal_return_url'];
		$paypal_currency = $_POST['learndash_plus_paypal_currency'];
		$paypal_sandbox = !empty($_POST['learndash_plus_paypal_sandbox'])? 1:0;
		
		$learndash_plus_paypal_settings =  array(
										'paypal_email' => $paypal_email,
										'paypal_currency' => $paypal_currency,
										'paypal_country' => $paypal_country,
										'paypal_cancel_url' => $paypal_cancel_url,
										'paypal_return_url' => $paypal_return_url,
										'paypal_notify_url' => $paypal_notify_url,
										'paypal_sandbox' => $paypal_sandbox,
										);
		// Save the posted value in the database
        update_option( 'learndash_plus_paypal_settings', $learndash_plus_paypal_settings);
        // Put an settings updated message on the screen

	?>
	<div class="updated"><p><strong><?php _e('Settings saved.', 'learndash-plus' ); ?></strong></p></div>
	<?php
    }
	
?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2><?php _e('Paypal Settings', 'learndash-plus') ?></h2>
<h3><?php _e('PayPal Email:', 'learndash-plus') ?></h3>
<input name="learndash_plus_paypal_email" style="min-width:30%" value="<?php echo   apply_filters('format_to_edit',$paypal_email) ?>" />
<h3><?php _e('PayPal Currency', 'learndash-plus') ?><small class="description"> (<?php _e('Enter the currency code e.g. USD', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_paypal_currency" style="min-width:30%" value="<?php echo   apply_filters('format_to_edit',$paypal_currency) ?>" />
<h3><?php _e('PayPal Country', 'learndash-plus') ?><small class="description"> (<?php _e('Enter the country code e.g. US', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_paypal_country" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$paypal_country) ?>" />
<h3><?php _e('PayPal Cancel URL', 'learndash-plus') ?><small class="description"> (<?php _e('Enter the URL used for purchase cancellations.', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_paypal_cancel_url" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$paypal_cancel_url) ?>" />
<h3><?php _e('PayPal Return URL', 'learndash-plus') ?><small class="description"> (<?php _e('Enter the URL used for completed purchases, typically a thank you page.', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_paypal_return_url" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$paypal_return_url) ?>" />
<h3><?php _e('PayPal Notify URL', 'learndash-plus') ?><small class="description"> (<?php _e('If using Subscription options, set this url in your PayPal Admin settings for IPN.', 'learndash-plus') ?>)</small> :</h3>
<?php echo apply_filters('format_to_edit',$paypal_notify_url) ?>
<h3><?php _e('Use PayPal Sandbox', 'learndash-plus') ?><small class="description"> (<?php _e('Check to enable the PayPal sandbox', 'learndash-plus') ?>)</small> :</h3>
<input name="learndash_plus_paypal_sandbox" type="checkbox" <?php if($paypal_sandbox) echo "CHECKED='CHECKED'"; ?> />
<div class="submit">
<input type="submit" name="update_LearnDashPaypalSettings" value="<?php _e('Update Settings', 'learndash-plus') ?>" /></div>
</form>
	