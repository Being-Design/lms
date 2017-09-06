<?php
	/**
	 *  PHP-PayPal-IPN Handler
	 */
 
	/*NOTE: the IPN call is asynchronous and can arrive later than the browser is redirected to the success url by paypal
	You cannot rely on setting up some details here and then using them in your success page.
	*/

	ini_set('log_errors', true);
	ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

	error_log("GET:\n".print_r($_GET,true));
	error_log("POST:\n".print_r($_POST,true));
	global $processing_id;
	$processing_id	= time();
	if(!function_exists('ld_debug')){
		function ld_debug($msg) {
			global $processing_id;
			if(isset($_GET['debug']))
			error_log("[$processing_id] ".$msg); //Comment This line to stop logging debug messages.
		}
	}
	ld_debug(print_r($_REQUEST, true));
	
	ld_debug('IPN Listener Loading...');	
	include('ipnlistener.php');
	$listener = new IpnListener();
	ld_debug('IPN Listener Loaded');

	/*While testing your IPN script you should be using a PayPal "Sandbox" (get an account at: https://developer.paypal.com )
	When you are ready to go live change use_sandbox to false.*/

	$learndash_plus_paypal_settings = get_option( 'learndash_plus_paypal_settings' );
	$paypal_email =  isset($learndash_plus_paypal_settings['paypal_email'])? $learndash_plus_paypal_settings['paypal_email']:"";
	$paypal_currency =  isset($learndash_plus_paypal_settings['paypal_currency'])? $learndash_plus_paypal_settings['paypal_currency']:"USD";
	$paypal_country =  isset($learndash_plus_paypal_settings['paypal_country'])? $learndash_plus_paypal_settings['paypal_country']:"US";
	$paypal_cancel_url =  isset($learndash_plus_paypal_settings['paypal_cancel_url'])? $learndash_plus_paypal_settings['paypal_cancel_url']:get_bloginfo('wpurl');
	$paypal_return_url =  isset($learndash_plus_paypal_settings['paypal_return_url'])? $learndash_plus_paypal_settings['paypal_return_url']:get_bloginfo('wpurl');
	$paypal_sandbox =  isset($learndash_plus_paypal_settings['paypal_sandbox'])? $learndash_plus_paypal_settings['paypal_sandbox']:0;
	
	$listener->use_sandbox = false;
	
	if ( !empty( $paypal_sandbox ) )
		$listener->use_sandbox = true;
		ld_debug('Sandbox Enabled.');

	try {
		ld_debug('Checking Post Method.');
		$listener->requirePostMethod();
		$verified = $listener->processIpn();
		ld_debug('Post method check completed.');
		
	} catch (Exception $e) {
		error_log($e->getMessage());
		exit(0);
	}

	$YOUR_NOTIFICATION_EMAIL_ADDRESS = get_option('admin_email');
	$seller_email = $paypal_email;
	
	$notify_on_valid_ipn = 1;
	
	/*The processIpn() method returned true if the IPN was "VERIFIED" and false if it was "INVALID".*/

	if ($verified) {

		/*	Once you have a verified IPN you need to do a few more checks on the POST
		fields--typically against data you stored in your database during when the
		end user made a purchase (such as in the "success" page on a web payments
		standard button). The fields PayPal recommends checking are:	
			1. Check the $_POST['payment_status'] is "Completed"
			2. Check that $_POST['txn_id'] has not been previously processed 
			3. Check that $_POST['receiver_email'] is get_option('EVI_Paypal_Seller_email')
			4. Check that $_POST['payment_amount'] and $_POST['payment_currency'] 
				are correct
		*/

		//note: This is just notification for us. Paypal has already made up its mind and the payment has been processed 
		//  (you can't cancel that here)
		if ( $_POST['receiver_email'] != $seller_email ) {
			$error_msg = __('Warning: IPN with invalid receiver email!', 'learndash-plus') . $listener->getTextReport();
			if ($YOUR_NOTIFICATION_EMAIL_ADDRESS !='') {	
				mail($YOUR_NOTIFICATION_EMAIL_ADDRESS, $error_msg);	
			} else {
				error_log('notification email not set: ERROR MSG: '.$error_msg);
			}
		}

		if ( $_POST['payment_status'] == "Completed") {
			//a customer has purchased from this website
			//add him to database for customer support
			
			// get / add user
			
			$email = $_REQUEST['payer_email'];
			ld_debug('Payment Email: '.$email);
			
			
			if (!empty($_REQUEST['custom']) && is_numeric($_REQUEST['custom'])) {
				$user = get_user_by("id", $_REQUEST['custom']);
				ld_debug('User ID ['.$_REQUEST['custom'].'] passed back by Paypal. Checking if user exists. User Found: '.(!empty($user->ID)? "Yes":"No"));
			}
			
			if(!empty($user->ID)) {
				$user_id = $user->ID;
				ld_debug('User found. Passed back by Paypal. User ID: '. $user_id);
			} else if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$user_id = $user->ID;
			} else {
				if ( $user_id = email_exists( $email ) ) {
					$user = get_user_by( 'id', $user_id );
				} else {
					$username = $email;
					if ( username_exists( $email ) ) {
						$count = 1;
						do {
							$new_username = $count . "_" . $email;
							$count++;
						} while ( username_exists( $new_username ) );
						$username = $new_username;
					}
					$random_password = wp_generate_password( 12, false );
					$user_id = wp_create_user( $username, $random_password, $email );
					$user = get_user_by( 'id', $user_id );
					wp_new_user_notification( $user_id, $random_password );
				}
			}
			
			// Update User Level
			$membership_id = $_REQUEST['item_number'];
			$membership_level = $_REQUEST['item_name'];
			
			$now = time();
			$payment = array('amount' => $_POST['payment_gross'],
						'currency' => $_POST['mc_currency'],
						'timestamp' => $now
						);
			learndash_plus_update_user_level($user_id, $membership_id, $payment);
		
			// log transaction
			$transaction = $_REQUEST;
			$transaction['user_id'] = $user_id;
			$transaction['membership_id'] = $membership_id;
			$transaction['membership_level'] = $membership_level;
			
			$post_id = wp_insert_post( Array( 'post_title' => "Access Id: ".$membership_id." [".$membership_level."] Purchased By {$email}", 'post_type' => 'ldp-transactions', 'post_status' => 'publish', 'post_author' => $user_id ) );
			foreach( $transaction as $k => $v )
				update_post_meta( $post_id, $k, $v );
		}

		$notifyOnValid = $notify_on_valid_ipn != '' ? $notify_on_valid_ipn : '0';
		if($notifyOnValid == '1') {
			mail($YOUR_NOTIFICATION_EMAIL_ADDRESS, 'Verified IPN', $listener->getTextReport());
		}

	} else {
		/*An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's a good idea to have a developer or sys admin 
		manually investigate any invalid IPN.*/

		mail($YOUR_NOTIFICATION_EMAIL_ADDRESS, 'Invalid IPN', $listener->getTextReport());
	}

	//we're done here
	