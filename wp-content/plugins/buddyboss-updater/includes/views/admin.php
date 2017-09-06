<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    
    <p style="margin: 21px 0px 31px 0px;">
        <?php _e( 'Use this page to activate your BuddyBoss license keys.', 'TEXTDOMAIN' );?><br>
        
        <?php printf( __( 'Connect your %s account to automatically add all subscriptions keys to your site.', 'TEXTDOMAIN' ), '<a href="http://buddyboss.com/" target="_blank" rel="noopener">BuddyBoss.com</a>' );?><br>
        
        <?php printf( __( 'If you decide to move your keys to another site, you can disconnect the account or a license key from your site, at any time.', 'TEXTDOMAIN' ) );?><br>
        
        <?php 
        $link = sprintf(
            "<a href='https://www.buddyboss.com/tutorials/buddyboss-updater-plugin/' target='_blank' rel='noopener'>%s</a>",
            __( 'BuddyBoss Updater tutorial page', 'TEXTDOMAIN' )
        );
        
        printf( 
            __( 'You can also add your keys manually by following the instructions on the %s.', 'TEXTDOMAIN' ),
            $link
        );
        ?>
    </p>
    
    
	<?php 
	$controller = BuddyBoss_Updater_Admin::instance();
	$controller->show_form_post_response();
    
    $controller->bb_connect_ui();
	?>
	<div class='bbossupdater-settings clearfix'>
		<div class="setting-tabs-wrapper">
            <ul>
                <?php $controller->print_settings_tabs();?>
            </ul>
		</div>
        <div>
            <div class='tabs-panel'>
                <?php $controller->print_settings_content();?>
            </div>
        </div>
	</div><!-- .bbossupdater-settings -->

</div>
