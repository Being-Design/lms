<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
// Clean up our options

// Non-multisite
delete_option( 'flizfbp_forcepaste' ); 
delete_option( 'flizfbp_extendtiny' );
 
// Multisite
delete_site_option( 'flizfbp_forcepaste' ); 
delete_site_option( 'flizfbp_extendtiny' );
?>
