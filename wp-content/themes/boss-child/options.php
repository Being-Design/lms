<?php 
/*
 * Settings page for custom Being Design theme options.
 */ 
?>
<div class="wrap">
	<h1>Being Design Theme Options</h1>
    <form method="post" action="options.php">
    	<?php
    	settings_fields( 'bd-options-group' );
    	do_settings_sections( 'bd-options-group' );
    	?>

    	<table class="form-table">
            <tr valign="top">
            <th scope="row">Hide Left Sidebar</th>
            <td><input type="checkbox" name="hide_left_bar" <?php if ( get_option('hide_left_bar') ) echo 'checked'; ?> value="true" /></td>
            </tr>
        </table>

    	<?php submit_button(); ?>

    </form>
</div>
