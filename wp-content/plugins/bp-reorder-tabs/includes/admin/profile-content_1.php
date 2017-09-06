<?php 
$bp = buddypress();
//Rename 'Activity' nav label if BuddyBoss wall is active and 'Wall' component is enabled
if ( function_exists( 'buddyboss_wall' ) && buddyboss_wall()->is_enabled() ) {
	$activity_label = buddyboss_wall()->option( 'MENU_NAME' );
}

$groups_nav = array();

$plugin_settings = get_option( 'b_r_t_plugin_options' );
if( isset( $plugin_settings['profile'] ) && isset( $plugin_settings['profile']['config_data'] ) ){
    $groups_nav = (array)$plugin_settings['profile']['config_data'];
}

?>

<form method="POST" id="frm_bp_r_t_a_screen" action="<?php echo admin_url('admin-ajax.php');?>">
	<input type="hidden" name="action" value="bp_r_t_a_profile" >
	<?php wp_nonce_field( 'bp_r_t_a_profile', 'nonce_bp_r_t_a_screen' );?>
	<input type="hidden" name="config_data" id="config_data" value="" >
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e( 'Default Profile Page', 'bp-reorder-tabs' );?></th>
				<td>
                    <?php 
                    $default_nav = defined( 'BP_DEFAULT_COMPONENT' ) ? BP_DEFAULT_COMPONENT : '';

                    /* 
                     * Some tabs are only visible to the logged in user, so don't make sense in this list.
                     * Therefore, lets exclude selected tabs
                     */
                    $private_navs = apply_filters( 'bp_r_t_profile_exclude_options', array( 'settings', 'notifications' ) );
                    ?>
                    
					<select name="def_profile_page" id="def_profile_page">
						<option value=''><?php _e( '&mdash; Select &mdash;', 'bp-reorder-tabs' );?></option>
						<?php 
						foreach( $bp->bp_nav as $nav=>$nav_props ){
							if( in_array( $nav_props['slug'], $private_navs ) )
								continue;
							
							$selected = $nav_props['slug']==$default_nav ? ' selected' : '';
							
							$nav_name = $nav_props['name'];
							$pos = strpos($nav_name, '<span');
							if( $pos ){
								$nav_name = substr($nav_name, 0, $pos);
							}
							
							echo "<option value='" . esc_attr( $nav_props['slug'] ) . "' {$selected}>{$nav_name}</option>";
						}
						?>
					</select>
				</td>
			</tr>
            <tr>
                <th scope="row"><?php _e( 'Adminbar nav', 'bp-reorder-tabs' );?></th>
                <td>
                    <?php

                    $rd_adminbar_nav = defined( 'BP_RD_ADMINBAR_NAV' ) ? BP_RD_ADMINBAR_NAV : '';

                    $checked = '';

                    if ( $rd_adminbar_nav )
                    {
                        $checked = ' checked="checked" ';
                    }

                    echo "<input ".$checked." id='enabled' name='rd_adminbar_nav' type='checkbox' value='yes'  />" . __( 'Enable adminbar nav links reorders.', 'bp-reorder-tabs' ) . "";
                    ?>
                </td>
            </tr>
		</tbody>
	</table>
	
    <ul class="nav-list sortable">
		<?php 
		$reordered_group_navs = array();
		foreach( $groups_nav as $nav=>$nav_props ){
			if( '' == $nav )
				continue;
			
			$nav_props = (array) $nav_props;
			$reordered_group_navs[$nav_props['position']] = $nav;
		}
		
		if( !empty( $reordered_group_navs ) ){
			ksort( $reordered_group_navs );
			foreach( $reordered_group_navs as $nav ){
				$nav_props = $groups_nav[$nav];
                //make sure to convert it into array
                if( is_object( $nav_props ) ){
                    $nav_props = get_object_vars( $nav_props );
                }
                
				$nav_name = $nav_props['name'];
				$pos = strpos($nav_name, '<span');
				if( $pos ){
					$nav_name = substr($nav_name, 0, $pos);
				}
                
                if ( ! empty( $nav_props['slug'] ) && $bp->activity->slug === $nav_props['slug'] &&  ! empty( $activity_label ) ) {
                    $nav_name = $activity_label;
                }

				echo "<li class='nav nav-top nav-type-profile nav-" . esc_attr( $nav ) ."' data-navid='" . esc_attr( $nav ) . "'>";
                    echo "<div class='drag-handle'>{$nav_name}</div>";

                    if( isset( $bp->bp_options_nav[$nav] ) ){
                        echo "<ul class='nav-list subnav-list sortable'>";

                        foreach( $bp->bp_options_nav[$nav] as $subnav_pos=>$subnav ){
                            $subnav_name = esc_html( $subnav['name'] );
                            $pos = strpos($subnav_name, '<span');
                            if( $pos ){
                                $subnav_name = substr($subnav_name, 0, $pos);
                            }

                            echo "<li class='nav nav-subnav nav-type-profile nav-" . esc_attr( $subnav['slug'] ) ."' data-navid='" . esc_attr( $subnav['slug'] ) . "'>";
                                echo "<div class='drag-handle'>{$subnav_name}</div>";
                            echo "</li>";
                        }

                        echo "</ul>";
                    }

                echo "</li>";
			}
		}
        
		?>
	</ul>
	<p class="submit">
		<button type="submit" class="button button-primary" ><?php _e( 'Save Changes', 'bp-reorder-tabs' );?></button>
	</p>
</form>