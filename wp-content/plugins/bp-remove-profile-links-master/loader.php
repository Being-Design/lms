<?php 
/*
Plugin Name: BP Remove Profile Links
Plugin URI: https://github.com/bphelp/bp-remove-profile-links
Description: Removes the Auto Created Links Form BP Profiles. 
No settings just activate it to remove the links.
Version: 1.2
Requires at least: WordPress 3.2 / BuddyPress 1.5.
Tested up to: WordPress 3.6 beta2 / BuddyPress 1.8 beta1
License: GNU/GPL 2
Author: @bphelp
Author URI: http://bphelpblog.wordpress.com/
*/

function bp_remove_profile_links_init() {
	require( dirname( __FILE__ ) . '/remove-profile-links.php' );
}
add_action( 'bp_include', 'bp_remove_profile_links_init' );
?>