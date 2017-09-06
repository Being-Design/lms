<?php $my_group_url = bp_r_t_my_group_url();?>

<?php if( $my_group_url ):?>

	<div class="groups_settings_preloader">
		<img src="<?php echo network_home_url( 'wp-includes/js/thickbox/loadingAnimation.gif' );?>" >

		<?php 
		/**
		 * loading a group(which current user is a member of) fires our action, which saves the info about what tabs to expect on group page
		 */
		?>
		<iframe src="<?php echo $my_group_url;?>" style="display: none" ></iframe>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function($){
		var data = {
			action  : 'bp_r_t_a_groups_setting_html',
			nonce	: '<?php echo wp_create_nonce( 'bp_r_t_a_groups_setting_html' );?>'
		};

		setTimeout(function(){ 
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: data,
					success: function (response) {
						$(".groups_settings_preloader").replaceWith(response);
						bp_r_t_admin_bind();
					}
				});
			},
			3000
		);
	});
	</script>

<?php else: ?>
	<?php 
	/**
	 * Apparantely, current user is not a member of any group,
	 * so we can't fetch which tabs would be dispalyed on group page.
	 */
	?>
	<div class="error fade">
		<p><?php _e( 'You must be a member (preferably admin) of at least one group. Please join/create a group and reload this screen again.', 'bp-reorder-tabs' );?></p>
	</div>
<?php endif; ?>