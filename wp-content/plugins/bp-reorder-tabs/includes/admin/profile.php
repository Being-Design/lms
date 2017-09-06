<?php $my_profile_url = apply_filters( 'bp_r_t_my_profile_url', bp_loggedin_user_domain() );?>
<div class="profil_settings_preloader">
	<img src="<?php echo network_home_url( 'wp-includes/js/thickbox/loadingAnimation.gif' );?>" >

	<?php 
	/**
	 * loading user's page fires our action, which saves the info about what tabs to expect on profile page
	 */
	?>
	<iframe src="<?php echo $my_profile_url;?>" style="display: none" ></iframe>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($){
		var data = {
			action  : 'bp_r_t_a_profile_setting_html',
			nonce	: '<?php echo wp_create_nonce( 'bp_r_t_a_profile_setting_html' );?>'
		};

		setTimeout(function(){ 
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: data,
					success: function (response) {
						$(".profil_settings_preloader").replaceWith(response);
						bp_r_t_admin_bind();
					}
				});
			},
			3000
		);
	});
</script>
