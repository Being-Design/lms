<?php

/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package Boss
 * @subpackage bp-legacy
 */

// var_dump( bp_get_group_id() );

?>

<style>
#primary #admins-list li .item-avatar,
#primary #mods-list li .item-avatar {
    width: 95px;
    min-width: 75px;
}
#primary #admins-list li .item,
#primary #mods-list li .item {
    width: 30%;
}
h2.section-header {
	padding-top: 30px;
}
</style>

<div class="group-members-list">

	<h2 class="section-header"><?php _e( 'Administrators', 'buddypress' ); ?></h2>

	<?php if ( bp_group_has_members( array( 'group_id' => bp_get_group_id(), 'per_page' => 15, 'group_role' => array( 'admin' ), 'page_arg' => 'mlpage-admin' ) ) ) : ?>

		<?php if ( bp_group_member_needs_pagination() ) : ?>
			<div class="pagination no-ajax">
				<div id="member-count" class="pag-count">
					<?php bp_group_member_pagination_count(); ?>
				</div>
				<div id="member-admin-pagination" class="pagination-links">
					<?php bp_group_member_admin_pagination(); ?>
				</div>
			</div>
		<?php endif; ?>

		<ul id="admins-list" class="item-list">
			<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
				<li>
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full&width=70&height=70'); ?></a>
					</div>

					<div class="item">
						<div class="item-title">
							<?php bp_group_member_link(); ?>
						</div>
					</div>

					<div class="action">&nbsp;</div>

				</li>
			<?php endwhile; ?>
		</ul>

		<?php if ( bp_group_member_needs_pagination() ) : ?>

			<div class="pagination no-ajax">
				<div id="member-count" class="pag-count">
					<?php bp_group_member_pagination_count(); ?>
				</div>
				<div id="member-admin-pagination" class="pagination-links">
					<?php bp_group_member_admin_pagination(); ?>
				</div>
			</div>

		<?php endif; ?>

	<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No group administrators were found.', 'buddypress' ); ?></p>
	</div>

	<?php endif; ?>
</div>

<br />

<div class="bp-widget group-members-list group-mods-list">
	<h2 class="section-header"><?php _e( 'Moderators', 'buddypress' ); ?></h2>

	<?php if ( bp_group_has_members( array( 'group_id' => bp_get_group_id(), 'per_page' => 15, 'group_role' => array( 'mod' ), 'page_arg' => 'mlpage-mod' ) ) ) : ?>

		<?php if ( bp_group_member_needs_pagination() ) : ?>
			<div class="pagination no-ajax">
				<div id="member-count" class="pag-count">
					<?php bp_group_member_pagination_count(); ?>
				</div>
				<div id="member-admin-pagination" class="pagination-links">
					<?php bp_group_member_admin_pagination(); ?>
				</div>
			</div>
		<?php endif; ?>

		<ul id="mods-list" class="item-list">

			<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
				<li>
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full&width=70&height=70'); ?></a>
					</div>

					<div class="item">
						<div class="item-title">
							<?php bp_group_member_link(); ?>
						</div>
					</div>

					<div class="action">&nbsp;</div>

				</li>
			<?php endwhile; ?>

		</ul>

		<?php if ( bp_group_member_needs_pagination() ) : ?>
			<div class="pagination no-ajax">
				<div id="member-count" class="pag-count">
					<?php bp_group_member_pagination_count(); ?>
				</div>
				<div id="member-admin-pagination" class="pagination-links">
					<?php bp_group_member_admin_pagination(); ?>
				</div>
			</div>
		<?php endif; ?>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'No group moderators were found.', 'buddypress' ); ?></p>
		</div>

	<?php endif; ?>
</div>

<br />

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( array( 'group_id' => bp_get_group_id(), 'group_role' => array( 'subscriber' ), 'exclude_admin_mods' => true ) ) ) : ?>
	<h2 class="section-header"><?php _e( 'Members', 'buddypress' ); ?></h2>

	<div id="pag-top" class="pagination">
		<div class="pag-count" id="member-dir-count-top">
			<?php bp_members_pagination_count(); ?>
		</div>
		<div class="pagination-links" id="member-dir-pag-top">
			<?php bp_members_pagination_links(); ?>
		</div>
	</div>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="item-list" role="main">
	<?php 
	$subtract = 0;
	while ( bp_members() ) : bp_the_member(); ?>

		<?php 
		$user = new WP_User( bp_get_member_user_id() ); 

		// echo '<pre>'; var_dump( $user ); echo '</pre>';
		// echo '<pre>'; var_dump( groups_is_user_member( $user->ID, bp_get_group_id() ) ); echo '</pre>';
		// var_dump ( groups_is_user_member( bp_displayed_user_id(), bp_get_group_id() ) );

		if ( groups_is_user_member( $user->ID, bp_get_group_id() ) ) :

			if ( !in_array( 'administrator', $user->roles) && !in_array( 'bbp_keymaster', $user->roles) ): ?>

			<li>
				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full&width=70&height=70'); ?></a>
				</div>

				<div class="item">
					<div class="item-title">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
					</div>

	                <?php
	                $showing = null;
	                //if bp-followers activated then show it.
	                if(function_exists("bp_follow_add_follow_button")) {
	                    $showing = "follows";
	                    $followers  = bp_follow_total_follow_counts(array("user_id"=>bp_displayed_user_id()));
	                } elseif (function_exists("bp_add_friend_button")) {
	                    $showing = "friends";
	                }

	                ?>

					<div class="item-meta">
						<div class="activity">
							<?php bp_member_last_active(); ?>
						</div>

						<?php if($showing == "friends"): ?>
	                    <span class="count"><?php echo friends_get_total_friend_count(bp_get_member_user_id()); ?></span>
	                    	<?php if ( friends_get_total_friend_count(bp_get_member_user_id()) > 1 ) { ?>
	                    		<span><?php _e("Friends","boss"); ?></span>
	                        <?php } else { ?>
	                        	<span><?php _e("Friend","boss"); ?></span>
	                        <?php } ?>
	                    <?php endif; ?>

	                    <?php if($showing == "follows"): ?>
	                    <span class="count"><?php $followers = bp_follow_total_follow_counts(array("user_id"=>bp_get_member_user_id())); echo $followers["followers"]; ?></span><span><?php _e("Followers","boss"); ?></span>
	                    <?php endif; ?>
					</div>

					<div class="item-desc">
						<p>
							<?php if ( bp_get_member_latest_update() ) : ?>
								<?php bp_member_latest_update( array( 'view_link' => true ) ); ?>
							<?php endif; ?>
						</p>
					</div>

					<?php do_action( 'bp_directory_members_item' ); ?>

					<?php
					 /***
					  * If you want to show specific profile fields here you can,
					  * but it'll add an extra query for each member in the loop
					  * (only one regardless of the number of fields you show):
					  *
					  * bp_member_profile_data( 'field=the field name' );
					  */
					?>
				</div>

				<div class="action">
	                <div class="action-wrap">
					    <?php do_action( 'bp_directory_members_actions' ); ?>
	                </div>
				</div>

				<div class="clear"></div>
			</li>

			<?php else: 
				$subtract++;
			endif; 

		endif;

	endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">
		<!--
		<div class="pag-count" id="member-dir-count-bottom">
			<?php bp_members_pagination_count(); ?>
		</div>
		-->
		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php bp_members_pagination_links(); ?>
		</div>
	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'boss' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
