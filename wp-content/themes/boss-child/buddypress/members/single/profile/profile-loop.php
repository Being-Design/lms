<?php do_action( 'bp_before_profile_loop_content' ); ?>

<?php if ( bp_has_profile() ) : ?>

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<?php if ( bp_profile_group_has_fields() ) : ?>

			<?php do_action( 'bp_before_profile_field_content' ); ?>

			<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">

				<h4><?php /* bp_the_profile_group_name(); */ ?>Profile</h4>

				<table class="profile-fields">

					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						<?php if ( bp_field_has_data() ) : ?>

							<tr<?php bp_field_css_class(); ?>>

								<td class="label"><?php bp_the_profile_field_name(); ?></td>

								<td class="data"><?php /* bp_the_profile_field_value();*/ echo strip_tags( bp_get_the_profile_field_value(), '<p>'); ?></td>

							</tr>

						<?php endif; ?>

						<?php do_action( 'bp_profile_field_item' ); ?>

					<?php endwhile; ?>

					<tr class="field_type_textbox">
						<td class="label"><?php _e('Role(s)'); ?></td>
						<td class="data">
							<?php
								$user = new WP_User( bp_displayed_user_id() );
								if(!empty($user->roles)) {
									global $wp_roles;
									$all_roles = $wp_roles->roles;
									$counter = 0;
									foreach( $user->roles as $user_role_key ) {
										if ( $counter != 0 ) {
											echo ', ';
										}
										foreach ( $all_roles as $role_key => $role_details ) {
											if ( $role_key == $user_role_key ) {
												echo $role_details['name'];
												break;
											}
										}
										$counter++;
									}
								}
							?>
						</td>
					</tr>

				</table>
			</div>

			<?php do_action( 'bp_after_profile_field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_profile_loop_content' ); ?>


<!-- copied & modified from buddypress/groups/groups-loop.php -->
<div class="groups mygroups">

	<h4>Groups</h4>

	<?php do_action( 'bp_before_groups_loop' ); ?>

	<?php if ( bp_get_current_group_directory_type() ) : ?>
		<?php bp_current_group_directory_type_message() ?>
	<?php endif; ?>

	<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

		<div id="pag-top" class="pagination">
			<div class="pag-count" id="group-dir-count-top">
				<?php bp_groups_pagination_count(); ?>
			</div>
			<div class="pagination-links" id="group-dir-pag-top">
				<?php bp_groups_pagination_links(); ?>
			</div>
		</div>

		<?php do_action( 'bp_before_directory_groups_list' ); ?>

		<ul id="groups-list" class="item-list" role="main">
			<?php while ( bp_groups() ) : bp_the_group(); ?>
				<li <?php bp_group_class(); ?>>
					<div class="item-avatar">
						<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=full&width=70&height=70' ); ?></a>
					</div>
					<div class="item">
						<div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
						<div class="item-meta"><div class="mobile"><?php bp_group_type(); ?></div><span class="activity"><?php printf( __( 'active %s', 'boss' ), bp_get_group_last_active() ); ?></span></div>
						<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>
		                <div class="item-meta">
		                    <?php
		                    global $groups_template;
		                    if ( isset( $groups_template->group->total_member_count ) ) {
		                         $count = (int) $groups_template->group->total_member_count;
		                    } else {
		                         $count = 0;
		                    }
		                    $html = sprintf( _n( '<span class="meta-wrap"><span class="count">%1s</span> <span>member</span></span>', '<span class="meta-wrap"><span class="count">%1s</span> <span>members</span></span>', $count, 'boss' ), $count );
		                    ?>
		                    <span class="desktop"><?php bp_group_type(); ?>&nbsp; / </span><?php  echo $html; ?>
		                </div>
						<?php do_action( 'bp_directory_groups_item' ); ?>
					</div>
					<div class="action">&nbsp;</div>
					<div class="clear"></div>
				</li>
			<?php endwhile; ?>
		</ul>

		<?php do_action( 'bp_after_directory_groups_list' ); ?>

		<div id="pag-bottom" class="pagination">
			<div class="pag-count" id="group-dir-count-bottom">
				<?php bp_groups_pagination_count(); ?>
			</div>
			<div class="pagination-links" id="group-dir-pag-bottom">
				<?php bp_groups_pagination_links(); ?>
			</div>
		</div>

	<?php else: ?>
		<div id="message" class="info">
			<p><?php _e( 'There were no groups found.', 'boss' ); ?></p>
		</div>
	<?php endif; ?>

	<?php do_action( 'bp_after_groups_loop' ); ?>

</div>
