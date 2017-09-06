<?php
class PM_paypal_request {
	
    public function profile_magic_repayment_process($user_id,$gid)
	{	
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
		$invoice = date("His").rand(1234, 9632);
		$amount = $pmrequests->profile_magic_check_paid_group($gid);
		$p = new profile_magic_paypal_class(); // paypal class
		$p->admin_mail = $pmrequests->profile_magic_get_admin_email(); // set notification email
		$p->sendbox = $dbhandler->get_global_option_value('pm_paypal_test_mode');
		$p->from_mail = $pmrequests->profile_magic_get_from_email();
		$group_name = $dbhandler->get_value('GROUPS','group_name',$gid,'id');
		update_user_meta( $user_id,'pm_user_payment_status','pending');
		update_user_meta( $user_id,'pm_invoice',$invoice);
		$this_script = get_permalink();
		$sign = strpos($this_script,'?')?'&':'?';
		$p->add_field('business', $dbhandler->get_global_option_value('pm_paypal_email')); // Call the facilitator eaccount
		$p->add_field('cmd','_cart'); // cmd should be _cart for cart checkout
		$p->add_field('upload', '1');
		$p->add_field('return', $this_script.$sign.'action=success&uid='.$user_id); // return URL after the transaction got over
		$p->add_field('cancel_return', $this_script.$sign.'action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
		$p->add_field('notify_url', $this_script.$sign.'action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
		$p->add_field('currency_code',$dbhandler->get_global_option_value('pm_paypal_currency'));
		$p->add_field('invoice', $invoice);
		$p->add_field('custom',$user_id);
		$p->add_field('page_style',$dbhandler->get_global_option_value('pm_paypal_page_style'));
		$p->add_field('item_name_1',$group_name);
		$p->add_field('amount_1',$amount);
		$p->submit_paypal_post(); // POST it to paypal	
	}
	
	public function profile_magic_payment_process($post,$action,$gid,$user_id,$textdomain)
	{
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
                $pmemails = new PM_Emails;
		$amount = $pmrequests->profile_magic_check_paid_group($gid);
		$p = new profile_magic_paypal_class(); // paypal class
		$p->admin_mail 	= $pmrequests->profile_magic_get_admin_email(); // set notification email
		$p->sendbox = $dbhandler->get_global_option_value('pm_paypal_test_mode');
		$p->from_mail = $pmrequests->profile_magic_get_from_email();
		$group_name = $dbhandler->get_value('GROUPS','group_name',$gid,'id');
		
		switch($action){
			case "process":
				update_user_meta( $user_id,'rm_user_status','1');
				update_user_meta( $user_id,'pm_user_payment_status','pending');
				update_user_meta( $user_id,'pm_invoice',$post["invoice"]);
				$this_script =get_permalink();
				$sign = strpos($this_script,'?')?'&':'?';
				$p->add_field('business', $dbhandler->get_global_option_value('pm_paypal_email')); // Call the facilitator eaccount
				$p->add_field('cmd', $post["cmd"]); // cmd should be _cart for cart checkout
				$p->add_field('upload', '1');
				$p->add_field('return', $this_script.$sign.'action=success&uid='.$user_id); // return URL after the transaction got over
				$p->add_field('cancel_return', $this_script.$sign.'action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
				$p->add_field('notify_url', $this_script.$sign.'action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
				$p->add_field('currency_code',$dbhandler->get_global_option_value('pm_paypal_currency'));
				$p->add_field('invoice', $post["invoice"]);
				$p->add_field('custom',$user_id);
				$p->add_field('page_style',$dbhandler->get_global_option_value('pm_paypal_page_style'));
				$p->add_field('item_name_1',$group_name);
				$p->add_field('amount_1',$amount);
				$p->submit_paypal_post(); // POST it to paypal
				//$p->dump_fields(); die;// Show the posted values for a reference, comment this line before app goes live
				break;
			case "success": // success case to show the user payment got success
                                
				echo '<div>';
				echo "<div class='info-text'>".__('Payment Transaction Done Successfully','profile-grid')."</br>";
				echo '</div></div>';
                                if(isset($_GET['uid'])):
                                    $gid = $pmrequests->profile_magic_get_user_field_value($_GET['uid'],'pm_group');
                                endif;
				
				//update_user_meta( $user_id,'pm_user_status','1');
                                $pmemails->pm_send_group_based_notification($gid,$user_id,'on_registration');
				if($dbhandler->get_value('GROUPS','show_success_message',$gid)==1)
				{
					echo $dbhandler->get_value('GROUPS','success_message',$gid);
				}
				if($pmrequests->pm_get_user_redirect($gid)!='')
				{
					header('refresh: 5; url='.$pmrequests->pm_get_user_redirect($gid));	
				}
				
			break;
			case "cancel": // case cancel to show user the transaction was cancelled
			echo '<div id="crf-form">';
				echo "<div class='info-text'>".__('Transaction Cancelled','profile-grid')."</br>";
				echo '</div></div>';
			break;
			case "ipn": // IPN case to receive payment information. this case will not displayed in browser. This is server to server communication. PayPal will send the transactions each and every details to this case in secured POST menthod by server to server. 
				$trasaction_id  = $post["txn_id"];
				$payment_status = strtolower($post["payment_status"]);
				$invoice = $post["invoice"];
				$user_id = $post["custom"];
				$log_array		= maybe_serialize($post);
				$log_check 		= $dbhandler->pm_count('PAYPAL_LOG',array('txn_id'=>$trasaction_id),'*');
				$amount = $post['mc_gross'];
                                $gid = get_user_meta($user_id,'pm_group',true);
                                $currency = $post['mc_currency'];
                                if($log_check<= 0){
                                    $dbhandler->insert_row('PAYPAL_LOG',array('txn_id'=>$trasaction_id,'log'=>$log_array,'posted_date'=>"NOW()",'gid'=>$gid,'status'=>$payment_status,'invoice'=>$invoice,'amount'=>$amount,'currency'=>$currency,'pay_processor'=>'paypal','pay_type'=>'one_time','uid'=>$user_id),array('%s','%s','%s','%d','%s','%s','%d','%s','%s','%s','%d'));	
				}else{
					$dbhandler->update_row('PAYPAL_LOG','txn_id',$trasaction_id,array('log'=>$log_array),array('%s'),'%s');	
				} // Save and update the logs array
				$paypal_log_id = $dbhandler->get_value('PAYPAL_LOG','id',$trasaction_id,'txn_id');
				if ($p->validate_ipn()){ // validate the IPN, do the others stuffs here as per your app logic
					update_user_meta( $user_id,'pm_txn_id',$trasaction_id);
					update_user_meta( $user_id,'pm_paypal_log_id',$paypal_log_id);
					update_user_meta( $user_id,'pm_user_payment_status',$payment_status);
					update_user_meta( $user_id,'pm_invoice',$invoice);
					$subject = __('Instant Payment Notification - Recieved Payment','profile-grid');
					//$p->send_report($subject); // Send the notification about the transaction
					if($payment_status=='completed')
					{
                                            $autoapproval = $dbhandler->get_global_option_value('pm_auto_approval',0);
                                            $send_user_activation_link = $dbhandler->get_global_option_value('pm_send_user_activation_link',0);
                                            if($autoapproval=='1')
                                            {
                                                if($send_user_activation_link=='1')
                                                {
                                                  $userstatus = '1';   
                                                  $pmrequests->pm_update_user_activation_code($user_id);
                                                  $pmemails->pm_send_activation_link($user_id,$this->profile_magic);
                                                }
                                                else
                                                {
                                                    $userstatus = '0';
                                                }
                                            }
                                            else
                                            {
                                                $userstatus = '1';
                                            }
						update_user_meta( $user_id,'rm_user_status',$userstatus);
					}
				}else{
					$subject = __('Instant Payment Notification - Payment Fail','profile-grid');
					//$p->send_report($subject); // failed notification
				}
			break;
		}	
	}
	
}