<?php
$path =  plugin_dir_url(__FILE__);
?>
<div class="uimrow"> <a href="admin.php?page=pm_payment_settings">
  <div class="pm_setting_image"> <img src="<?php echo $path;?>images/pg_payments.png" class="options" alt="options"> </div>
  <div class="pm-setting-heading"> <span class="pm-setting-icon-title">
    <?php _e( 'Payments','profile-grid' ); ?>
    </span> <span class="pm-setting-description">
    <?php _e( 'Currency, Symbol Position, Checkout Page etc.', 'profile-grid' ); ?>
    </span> </div>
  </a> </div>