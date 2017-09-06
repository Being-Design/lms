<form method="POST">
    <?php wp_nonce_field( "bboss_licensing" );?>
    <table class="wp-list-table widefat plugins">
        <tfoot>
            <tr>
                <td colspan="100%">
                    <button type="submit" name="btn_submit" class="button button-primary">
                        <span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Connect Subscription', 'buddyboss-updater' );?>
                    </button>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <tr>
                <td><?php _e( 'Products', 'buddyboss-updater' );?></td>
                <td>
                    <?php 
                    $product_names = array();
                    foreach( $package['products'] as $product ){
                        $product_names[] = '<em>' . $product['name'] . '</em>';
                    }
                    $product_names = implode( _x( ' and ', 'Conjuction joining different product names', 'buddyboss-updater' ), $product_names ) . '.';
                    printf( __( 'This license gives you access to updates and support for %s', 'buddyboss-updater' ), $product_names );
                    
                    $controller = BuddyBoss_Updater_Admin::instance();
                    $controller->show_partial_activations( $package );
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php _e( 'License Key', 'buddyboss-updater' );?>
                    <span class='tooltip-persistent-container'>
                        <span class='help-tip'></span>
                        <span class="tooltip-persistent">
                            You can find the license key for your product by going to the <a href="https://buddyboss.com/my-account/?part=mysubscriptions" target="_blank" rel="noopener" >My Subscriptions</a> page in your account area.
                        </span>
                    </span>
                </td>
                <td>
                    <input type="text" name="license_key" value="<?php echo esc_attr( isset( $license['license_key'] ) ? $license['license_key'] : '' );?>" class="regular-text">
                    <?php 
                    if( $license['is_active'] ){
                        //echo "<span class='status status-active'>" . __( 'License active', 'buddyboss-updater' ) . "</span>";
                    } else {
                        //echo "<span class='status status-inactive'>" . __( 'Please activate', 'buddyboss-updater' ) . "</span>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php _e( 'Activation Email ID', 'buddyboss-updater' );?>
                    <span class='tooltip-persistent-container'>
                        <span class='help-tip'></span>
                        <span class="tooltip-persistent">
                            This is your account email you use to login to your BuddyBoss account.
                        </span>
                    </span>
                </td>
                
                <td>
                    <input type="email" name="activation_email" value="<?php echo esc_attr( isset( $license['activation_email'] ) ? $license['activation_email'] : '' );?>" class="regular-text">
                </td>
            </tr>
        </tbody>
    </table>
</form>