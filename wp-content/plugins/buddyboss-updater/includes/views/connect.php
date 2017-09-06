<table class="table table-form bb_connect">
    <tr>
        <td>
            <button id="btn_bb_connect" class="button button-primary button-hero">
                <span class="dashicons dashicons-admin-plugins"></span> Connect your BuddyBoss.com account
            </button>
        </td>
        <td>
            <span class="connecting" style="display:none;">Connecting</span>
        </td>
    </tr>
    
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
</table>

<div id="bb_connector_overlay_wrapper" style="display: none;">
    <div id="bb_connector_overlay">
        <img src="<?php echo home_url( 'wp-includes/images/spinner-2x.gif' );?>" >
    </div>
</div>