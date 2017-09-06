<?php
/*** Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	} else {
		add_action( 'admin_notices', 'remove_profile_links_install_buddypress_notice' );
		return;
	}
}


function remove_profile_links_install_buddypress_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BP Remove Profile Links</strong></a> requires the BuddyPress plugin to work. Please <a href="http://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BP Remove Profile Links</a>.');
	echo '</p></div>';
}

class bphelp_remove_profile_links {
	function remove_xprofile_links() {
		remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
	}
}
add_action( 'init', array( 'bphelp_remove_profile_links', 'remove_xprofile_links' ));
?>