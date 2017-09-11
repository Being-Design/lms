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
    	submit_button();
    ?>
    </form>
</div>
