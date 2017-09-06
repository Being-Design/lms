<?php
	$id = intVal($_REQUEST['id']);

	$learndash_plus_levels_options = get_option('learndash_plus_levels_options');
	$levels = learndash_plus_get_levels();
    global $wp_roles;
    $roles = $wp_roles->roles;

	//print_r($levels);
	if($id != 0 && !isset($levels[$id]))
	{
		echo _e("Invalid Access Level", "learndash-plus");
	}
	else
	{
		if($id == 0)
		{
			$level = array(
						'id' => learndash_plus_get_new_level_id($learndash_plus_levels_options),
						'name' => '',
						'role' =>  '',
						'nextlevel' => '',
						'autoupgradeafter' => 0,
						'price' => 0,
						'price_type' => 1,
						'p3' => '',
						't3' => '',
						'redirect_url' => '',				
					);
		}
		else
		$level = $levels[$id];
	
	if( isset($_POST[ "update_LearnDashMembershipLevels" ]) ) {
        // Read their posted value
		
        $id   =  $_POST['learndash_plus_level_id'];
		$name = $_POST['learndash_plus_level_name'];
		$role = $_POST['learndash_plus_level_role'];
		$nextlevel = $_POST['learndash_plus_level_nextlevel'];
		$autoupgradeafter = intVal($_POST['learndash_plus_level_autoupgradeafter']);
		$price = intVal($_POST['learndash_plus_level_price']);
		$price_type = intVal($_POST['learndash_plus_level_price_type']);
		$p3 = intVal($_POST['learndash_plus_level_p3']);
		$t3 = $_POST['learndash_plus_level_t3'];
		$redirect_url = $_POST['learndash_plus_level_redirect_url'];
		
		$level = $levels[$id] = array(
										'id' => $id,
										'name' => $name,
										'role' => $role,
										'nextlevel' => $nextlevel,
										'autoupgradeafter' => $autoupgradeafter,
										'price' => $price,
										'price_type' => $price_type,
										'p3' => $p3,
										't3' => $t3,
										'redirect_url' => $redirect_url
										);
		
		$learndash_plus_levels_options['levels'] = $levels;
		
		// Save the posted value in the database
        update_option( 'learndash_plus_levels_options', $learndash_plus_levels_options);
        // Put an settings updated message on the screen
		$url = get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id);
	?>
	<div class="updated"><p><strong><?php _e('Settings Saved.', 'learndash-plus' ); ?></strong></p></div>
	<meta http-equiv="refresh" content="0; url=<?php echo $url; ?>"> 

	<?php
    }
	
	?>
	<script>
	function learndash_plus_billing_cycle(val){
		if(val == 2)
		document.getElementById('learndash_plus_billing_cycle').style.display = "block";
		else
		document.getElementById('learndash_plus_billing_cycle').style.display = "none";
		
	}
	</script>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	
	
	<input name="learndash_plus_level_id" type='hidden' value="<?php echo apply_filters('format_to_edit',$level['id']) ?>" />
	
	
	<h3><?php _e('Access Level Name', 'learndash-plus') ?>: </h3>
	
	<input name="learndash_plus_level_name" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$level['name']) ?>" />
	
	<h3><?php _e('Associated Role', 'learndash-plus') ?>: </h3>
	<select name="learndash_plus_level_role" style="min-width:30%">
		<?php 
				$levelrole = !empty($level['role'])? $level['role']:'subscriber';
				foreach($roles as $roleid => $role) { 
					$name = $role['name'];
					$selected = ($levelrole == $roleid)? 'SELECTED="SELECTED"':"";	
		?>
				<option value="<?php echo $roleid;?>" <?php echo $selected; ?>><?php _e($name) ?></option>
		<?php } ?>
	</select>
	
	<h3><?php _e('Next Level', 'learndash-plus') ?>: </h3>
	
	<select name="learndash_plus_level_nextlevel" style="min-width:30%">
				<option value="0"><?php _e('None', 'learndash-plus') ?></option>
		<?php foreach($levels as $l) { 
					$levelid = $l['id'];
					$name = $l['name'];
					$selected = ($level['nextlevel'] == $levelid) ? 'SELECTED="SELECTED"':"";	

		?>
				<option value="<?php echo $levelid;?>" <?php echo $selected; ?>><?php echo $name;?></option>
		<?php } ?>
	</select>
	
	<h3><?php _e('Auto Upgrade After (days)', 'learndash-plus') ?>: </h3>
	<input name="learndash_plus_level_autoupgradeafter" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$level['autoupgradeafter']) ?>" />

	<h3><?php _e('Price', 'learndash-plus') ?>: </h3>
	<input name="learndash_plus_level_price" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$level['price']) ?>" />
	
	<h3><?php _e('Price Type', 'learndash-plus') ?>: </h3>
	<select name="learndash_plus_level_price_type" style="min-width:30%" onClick='learndash_plus_billing_cycle(this.value);'>
				<option value="1" <?php if($level['price_type'] == '1') echo 'SELECTED'; ?>><?php _e('Buy Now', 'learndash-plus') ?></option>
				<option value="2" <?php if($level['price_type'] == '2') echo 'SELECTED'; ?>><?php _e('Recurring', 'learndash-plus') ?></option>
	</select>
	<div id='learndash_plus_billing_cycle' style="<?php if($level['price_type'] != 2) echo 'display:none'; ?>">
	<h3><?php _e('Billing Cycle', 'learndash-plus') ?>: </h3>
	<input name="learndash_plus_level_p3" size='3' value="<?php echo apply_filters('format_to_edit',$level['p3']) ?>" />
	<select name="learndash_plus_level_t3" style="100px">
				<option value="D" <?php if($level['t3'] == 'D') echo 'SELECTED'; ?>><?php _e('day(s)', 'learndash-plus') ?></option>
				<option value="W" <?php if($level['t3'] == 'W') echo 'SELECTED'; ?>><?php _e('week(s)', 'learndash-plus') ?></option>
				<option value="M" <?php if($level['t3'] == 'M') echo 'SELECTED'; ?>><?php _e('month(s)', 'learndash-plus') ?></option>
				<option value="Y" <?php if($level['t3'] == 'Y') echo 'SELECTED'; ?>><?php _e('year(s)', 'learndash-plus') ?></option>
	</select>
	
	</div>
	
	<h3><?php _e('Login Redirect Url', 'learndash-plus') ?>: <small>(<?php _e('Leave blank to use the default global', 'learndash-plus') ?>)</small></h3>
	<input name="learndash_plus_level_redirect_url" style="min-width:30%" value="<?php echo apply_filters('format_to_edit',$level['redirect_url']) ?>" />
	
	<div class="submit">
		<input type="submit" name="update_LearnDashMembershipLevels" value="<?php _e('Update Settings', 'learndash-plus') ?>" />	
	</div>

	<?php if(!empty($id)) { //Shortcode
			$learndash_plus_paypal_settings = get_option( 'learndash_plus_paypal_settings' );
			$paypal_email =  isset($learndash_plus_paypal_settings['paypal_email'])? $learndash_plus_paypal_settings['paypal_email']:"";
			$paypal_currency =  isset($learndash_plus_paypal_settings['paypal_currency'])? $learndash_plus_paypal_settings['paypal_currency']:"USD";
			$paypal_country =  isset($learndash_plus_paypal_settings['paypal_country'])? $learndash_plus_paypal_settings['paypal_country']:"US";
			$paypal_cancel_url =  isset($learndash_plus_paypal_settings['paypal_cancel_url'])? $learndash_plus_paypal_settings['paypal_cancel_url']:get_bloginfo('wpurl');
			$paypal_return_url =  isset($learndash_plus_paypal_settings['paypal_return_url'])? $learndash_plus_paypal_settings['paypal_return_url']:get_bloginfo('wpurl');
			$paypal_notify_url =  isset($learndash_plus_paypal_settings['paypal_notify_url'])? $learndash_plus_paypal_settings['paypal_notify_url']:get_bloginfo('wpurl')."/?ldp-paypal-ipn=1";
			$paypal_sandbox =  isset($learndash_plus_paypal_settings['paypal_sandbox'])? $learndash_plus_paypal_settings['paypal_sandbox']:0;
			
			if($level['price_type'] == '1') //Pay Now/Buy Now
			//$paypal_shortcode = "[paypal type='paynow' amount='".number_format($level['price'],2)."' sandbox='{$paypal_sandbox}' email='{$paypal_email}' itemno='".$level['id']."' name='".$level['name']."' noshipping='1' nonote='1' qty='1' currencycode='{$paypal_currency}' rm='2' notifyurl='{$paypal_notify_url}' returnurl='{$paypal_return_url}' scriptcode='scriptcode' imagewidth='100px' pagestyle='paypal' lc='{$paypal_country}' cbt='Complete Your Purchase']";
			$paypal_shortcode = "[ldp_paypal type='paynow' amount='".number_format($level['price'],2)."'  itemno='".$level['id']."' name='".$level['name']."']";
			else
			$paypal_shortcode = "[ldp_paypal type='subscribe' a3='".number_format($level['price'],2)."'    p3='".$level['p3']."' t3='".$level['t3']."' itemno='".$level['id']."' name='".$level['name']."']";
			
	?>
	<div><h3><?php _e('Paypal ShortCode', 'learndash-plus') ?>:</h3>
		<div>
			<?php echo $paypal_shortcode ?>
		</div>
	</div>
	<?php } ?>
<?php 
include(dirname(__FILE__).'/levels_edit_userslist.php');
} //Valid Level ?>

