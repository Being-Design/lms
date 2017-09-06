<form method="POST" id="frm_bp_r_t_a_screen" action="<?php echo admin_url('admin-ajax.php');?>">
	<input type="hidden" name="action" value="bp_r_t_a_groups" >
	<?php wp_nonce_field( 'bp_r_t_a_groups', 'nonce_bp_r_t_a_screen' );?>
	<input type="hidden" name="config_data" id="config_data" value="" >
	
	<?php 
	$groups_nav = array();
	
	$plugin_settings = get_option( 'b_r_t_plugin_options' );
	if( isset( $plugin_settings['groups'] ) && isset( $plugin_settings['groups']['config_data'] ) ){
		$groups_nav = (array)$plugin_settings['groups']['config_data'];
	}
	?>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e( 'Default Group Page', 'bp-reorder-tabs' );?></th>
				<td>
					<select name="def_profile_page" id="def_profile_page">
						<option value=''><?php _e( '&mdash; Select &mdash;', 'bp-reorder-tabs' );?></option>
						<?php 
						if( !empty( $groups_nav ) ){
							/* 
                            * Some tabs are only visible to the logged in user, so don't make sense in this list.
                            * Therefore, lets exclude selected tabs
                            */
							$private_navs = apply_filters( 'bp_r_t_group_exclude_options', array( 'admin' ) );

							$default_nav = isset( $plugin_settings['groups'] ) && isset( $plugin_settings['groups']['default'] ) ? $plugin_settings['groups']['default'] : '';
							foreach( $groups_nav as $nav=>$nav_props ){


								$nav_props = (array) $nav_props;
								
								if( in_array( $nav, $private_navs ) )
									continue;
								
								$selected = $nav==$default_nav ? ' selected' : '';

								$nav_name = $nav_props['name'];
								$pos = strpos($nav_name, '<span');
								if( $pos ){
									$nav_name = substr($nav_name, 0, $pos);
								}

								echo "<option value='" . esc_attr( $nav ) . "' {$selected}>{$nav_name}</option>";
							}
						}
						?>
					</select>
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

				$nav_props = (array) $groups_nav[$nav];

                if ( empty( $nav_props ) ) continue;

				$nav_name = $nav_props['name'];
				$pos = strpos($nav_name, '<span');

				if( $pos ){
					$nav_name = substr($nav_name, 0, $pos);
				}

				echo "<li class='nav nav-top nav-type-groups nav-" . esc_attr( $nav ) ."' data-navid='" . esc_attr( $nav ) . "'>";
					echo "<div class='drag-handle'>{$nav_name}</div>";
				echo "</li>";
			}
		}
		?>
	</ul>
	<p class="submit">
		<button type="submit" class="button button-primary" ><?php _e( 'Save Changes', 'bp-reorder-tabs' );?></button>
	</p>
</form>