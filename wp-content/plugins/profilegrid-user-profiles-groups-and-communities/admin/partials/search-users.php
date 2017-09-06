<?php
$blogusers = get_users( array( 'search' => $_POST['name'] ) );
// Array of WP_User objects.
foreach ( $blogusers as $user ) {
	echo '<span>' . esc_html( $user->user_email ) . '</span>';
}
?>