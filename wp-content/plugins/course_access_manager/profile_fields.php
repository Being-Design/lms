<?php
    function learndash_plus_add_custom_user_profile_fields( $user ) {
    ?>
        <h3><?php _e('Access Information', 'learndash-plus'); ?></h3>
        
        <table class="form-table">
            <tr>
                <th>
                    <label for="learndash_plus_level"><?php _e('Access Level', 'learndash-plus'); ?>
                </label></th>
                <td>
					<?php 
					$levels = learndash_plus_get_levels();
					$currentlevel =  intVal(get_user_meta($user->ID, 'learndash_plus_level', true ));
					$currentlevelname = isset($levels[$currentlevel])? $levels[$currentlevel]['name']:"None";

					$lastpaid_timestamp = get_user_meta( $user->ID, 'learndash_plus_lastpaid', true );
					$paid = get_user_meta( $user->ID, 'learndash_plus_paid', true );
					
					if (current_user_can('manage_options')) {
					?>
						<select name="learndash_plus_level" style="min-width:30%">
							<option value="0"><?php _e('None', 'learndash-plus') ?></option>
							<?php 
								foreach($levels as $l) { 
										$levelid = $l['id'];
										$name = $l['name'];
										$selected = ($currentlevel == $levelid)? 'SELECTED="SELECTED"':"";	
							?>
							<option value="<?php echo $levelid;?>"  <?php echo $selected; ?> ><?php echo $name;?></option>
							<?php } ?>
						</select>
					<?php } else { 
						echo $currentlevelname;
					} ?>
					<br />
                </td>

            </tr>

			<?php if(!empty($paid )) { ?>
			<tr>
				<th><label for="learndash_plus_level"><?php _e('Payments', 'learndash-plus'); ?>
				</th>
				<td>
					<?php 	$i = 1;
							foreach($paid as $payment) { 
								echo $i.". <b>" . __('Date', 'learndash-plus') . ":</b> ".date("m-d-Y H:i:s", intVal($payment['timestamp']));
								echo " <b>" . __('Amount', 'learndash-plus') . ":</b> ".$payment['amount']. " " . $payment['currency'];
								echo " <b>" . __('For Access Level', 'learndash-plus') . ":</b> ". $payment['membership_level'];
								echo "<br>";

								$i++;
							} 
					?>
				</td>
			</tr>
			<?php } ?>
			
        </table>
    <?php }
     
    function learndash_plus_save_custom_user_profile_fields( $user_id ) {
	
        if ( !current_user_can('manage_options'))
            return FALSE;
		$currentlevel = get_user_meta( $user_id, 'learndash_plus_level', true);
		
		if($currentlevel != $_POST['learndash_plus_level'])
		{
			$now = time();
			$payment = array('amount' => "0.00",
						'currency' => __("By ADMIN", 'learndash-plus'),
						'timestamp' => $now
						);
			learndash_plus_update_user_level($user_id, $_POST['learndash_plus_level'], $payment);
		}
    }
	


    add_action( 'show_user_profile', 'learndash_plus_add_custom_user_profile_fields' );
    add_action( 'edit_user_profile', 'learndash_plus_add_custom_user_profile_fields' );
     
    add_action( 'personal_options_update', 'learndash_plus_save_custom_user_profile_fields' );
    add_action( 'edit_user_profile_update', 'learndash_plus_save_custom_user_profile_fields' );