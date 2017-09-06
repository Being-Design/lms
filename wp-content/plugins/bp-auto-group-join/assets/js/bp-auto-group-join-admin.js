/**
 * BP Auto Group Join Admin
 *
 *
 * This file should load in the footer
 *
 * @author      BuddyBoss
 * @since       BP Auto Group Join (1.0.0)
 * @package     BP Auto Group Join
 *
 * ====================================================================
 *
 * 1. Main BPAGJ Functionality
 */


/**
 * 1. Main BPAGJ Functionality
 * ====================================================================
 */
var jq = $ = jQuery;

jq(document).ready(function(){

    var aj_reg_selection = jq('.bpaj_group_meta_box input:radio[name="aj_new_registrations"], .bpaj_group_meta_box input:radio[name="aj_existing_users"]');

    // check the selection & show hide member types options
    aj_reg_selection.on('change', function(){

        var this_selector = jq(this);
        var selected_val = this_selector.val();

        if( selected_val == 'bp_member_types' ) {
            this_selector.parents('.bpaj_group_meta_box').find('.bpaj_member_types_wrapper').show();
        }else{
            this_selector.parents('.bpaj_group_meta_box').find('.bpaj_member_types_wrapper input').attr('checked', false);
            this_selector.parents('.bpaj_group_meta_box').find('.bpaj_member_types_wrapper').hide();
        }

    });

});
