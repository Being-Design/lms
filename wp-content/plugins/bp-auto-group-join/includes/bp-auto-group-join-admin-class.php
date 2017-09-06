<?php

/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BP_Auto_Group_Join_Admin' ) ):

	/**
	 *
	 * BP Auto Group Join Admin
	 * ***********************************
	 */
    class BP_Auto_Group_Join_Admin {

        public function __construct() {
            $this->hooks();
        }

        public function hooks() {
            add_action( 'bp_groups_admin_load', array($this, 'bp_groups_admin_load') );
            add_action( 'bp_groups_admin_meta_boxes', array($this, 'bp_groups_admin_meta_boxes') );
        }

        public function bp_groups_admin_load(){
            // Check if our nonce is set.
            if ( ! isset( $_POST['bpagj_group_mb_nonce'] ) ) {
                return;
            }
            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST['bpagj_group_mb_nonce'], 'bpagj_save_group_mb_data' ) ) {
                return;
            }
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            // if there is no gid, then bail out
            if( !isset($_GET['gid']) && empty($_GET['gid']) ) {
                return;
            }

            $group_id = $_GET['gid'];

            // new users
            if( isset($_POST['aj_new_registrations']) && !empty($_POST['aj_new_registrations']) ){
                groups_update_groupmeta( $group_id, 'aj_new_registrations', $_POST['aj_new_registrations']);
                // new users member type
                if( $_POST['aj_new_registrations'] == 'bp_member_types' && isset($_POST['aj_new_registrations_mt']) && !empty($_POST['aj_new_registrations_mt']) ){
                    groups_update_groupmeta( $group_id, 'aj_new_registrations_mt', $_POST['aj_new_registrations_mt']);
                }else{
                    groups_delete_groupmeta( $group_id, 'aj_new_registrations_mt');
                }
            }

            // existing users
            if( isset($_POST['aj_existing_users']) && !empty($_POST['aj_existing_users']) ){
                groups_update_groupmeta( $group_id, 'aj_existing_users', $_POST['aj_existing_users']);
                if($_POST['aj_existing_users'] == 'all_members') {
                    bp_auto_group_join_all_members($group_id);
                }
                // existing users member type
                if( $_POST['aj_existing_users'] == 'bp_member_types' && isset($_POST['aj_existing_users_mt']) && !empty($_POST['aj_existing_users_mt']) ){
                    groups_update_groupmeta( $group_id, 'aj_existing_users_mt', $_POST['aj_existing_users_mt']);
                    bp_auto_group_join_all_members($group_id, $_POST['aj_existing_users_mt']);
                }else{
                    groups_delete_groupmeta( $group_id, 'aj_existing_users_mt');
                }
            }
        }

        public function bp_groups_admin_meta_boxes(){
            add_meta_box( 'bp_group_auto_join_member', _x( 'Auto-Join Users to this Group', 'group admin edit screen', 'bp-auto-group-join' ), array($this, 'bp_groups_admin_edit_auto_join_member'), get_current_screen()->id, 'normal', 'high' );
        }

        public function bp_groups_admin_edit_auto_join_member() {
            $member_type_enabled = bp_auto_group_join()->option('ajg_bmt_support');
            $group_id = $_GET['gid'];
            $new_registrations = groups_get_groupmeta( $group_id, 'aj_new_registrations', true);
            $new_registrations_mt = groups_get_groupmeta( $group_id, 'aj_new_registrations_mt', true);
            $existing_users = groups_get_groupmeta( $group_id, 'aj_existing_users', true);
            $existing_users_mt = groups_get_groupmeta( $group_id, 'aj_existing_users_mt', true);

            // show/hide member type selection
            $new_registrations_mt_css = $new_registrations == 'bp_member_types' ? '' : 'style="display:none"';
            $existing_users_mt_css = $existing_users == 'bp_member_types' ? '' : 'style="display:none"';

            // display message based on member type plugin activate/deactivate
            $mt_activated_msg   = sprintf(__('<em><a href="%s" target="_blank">Configure Member Types</a></em>', 'bp-auto-group-join'), get_admin_url().'edit.php?post_type=bmt-member-type');
            $mt_deactivated_msg = sprintf(__('<em>Requires the <a href="%s" target="_blank">Buddypress Member Types plugin</a></em>', 'bp-auto-group-join'), 'https://www.buddyboss.com/product/buddypress-member-types/');
            $mt_message_display = function_exists('BUDDYBOSS_BMT_init') ? $mt_activated_msg : $mt_deactivated_msg;

            wp_nonce_field( 'bpagj_save_group_mb_data', 'bpagj_group_mb_nonce' );
            ?>

            <div class="bpaj_group_meta_box_wrapper">
                <fieldset class="bpaj_group_meta_box">
                    <legend><h4><?php _e('Join New Registrations', 'bp-auto-group-join');?></h4></legend>
                    <ul>
                        <li>
                            <input <?php echo $new_registrations == 'all_members' ? 'checked' : '';?> name="aj_new_registrations" id="aj_new_registrations_all" type="radio" value="all_members">
                            <label for="aj_new_registrations_all"> <?php _e('All new registrations', 'bp-auto-group-join');?> </label>
                        </li>
                        <?php if(($member_type_enabled == 'on' )): ?>
                        <li>
                            <input <?php echo $new_registrations == 'bp_member_types' ? 'checked' : '';?> name="aj_new_registrations" id="aj_new_registrations_mt" type="radio" value="bp_member_types">
                            <label for="aj_new_registrations_mt"> <?php _e('Select by member type', 'bp-auto-group-join');?> </label>

                            <div class="bpaj_member_types_wrapper" <?php echo $new_registrations_mt_css;?>>
                                <?php
                                $member_types = bp_get_member_types();
                                if( isset($member_types) &!empty($member_types) ):
                                    ?>
                                    <ul class="bpaj_member_types">
                                        <?php foreach($member_types as $key => $val): ?>
                                            <li>
                                                <input <?php echo ( ! empty( $new_registrations_mt ) && is_array( $new_registrations_mt ) && in_array($key, $new_registrations_mt) ) ? 'checked' : '';?> name="aj_new_registrations_mt[]" id="aj_new_registrations_mt_<?php echo $key;?>" type="checkbox" value="<?php echo $key;?>">
                                                <label for="aj_new_registrations_mt_<?php echo $key;?>"> <?php echo $val;?> </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="bpaj_mt_msg"><?php echo $mt_message_display;?></div>
                            </div>

                        </li>
                        <?php endif; ?>
                        <li>
                            <input <?php echo $new_registrations == 'none' || empty($new_registrations) ? 'checked' : '';?> name="aj_new_registrations" id="aj_new_registrations_all" type="radio" value="none">
                            <label for="aj_new_registrations_none"> <?php _e('None', 'bp-auto-group-join');?> </label>
                        </li>
                    </ul>
                </fieldset>

                <fieldset class="bpaj_group_meta_box">
                    <legend><h4><?php _e('Join Existing Members', 'bp-auto-group-join');?></h4></legend>
                    <ul>
                        <li>
                            <input <?php echo $existing_users == 'all_members' ? 'checked' : '';?> name="aj_existing_users" id="aj_existing_users_all" type="radio" value="all_members">
                            <label for="aj_existing_users_all"> <?php _e('All existing members', 'bp-auto-group-join');?> </label>
                        </li>
                        <?php if(($member_type_enabled == 'on' )): ?>
                        <li>
                            <input <?php echo $existing_users == 'bp_member_types' ? 'checked' : '';?> name="aj_existing_users" id="aj_existing_users_mt" type="radio" value="bp_member_types">
                            <label for="aj_existing_users_mt"> <?php _e('Select by member type', 'bp-auto-group-join');?> </label>

                            <div class="bpaj_member_types_wrapper" <?php echo $existing_users_mt_css;?>>
                                <?php
                                $member_types = bp_get_member_types();
                                if( isset($member_types) &!empty($member_types) ):
                                ?>
                                    <ul class="bpaj_member_types">
                                        <?php foreach($member_types as $key => $val): ?>
                                        <li>
                                            <input <?php echo ( ! empty( $existing_users_mt ) && in_array($key, $existing_users_mt) ) ? 'checked' : '';?> name="aj_existing_users_mt[]" id="aj_existing_users_mt_<?php echo $key;?>" type="checkbox" value="<?php echo $key;?>">
                                            <label for="aj_existing_users_mt_<?php echo $key;?>"> <?php echo $val;?> </label>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="bpaj_mt_msg"><?php echo $mt_message_display;?></div>
                            </div>

                        </li>
                        <?php endif; ?>
                        <li>
                            <input <?php echo $existing_users == 'none' || empty($existing_users) ? 'checked' : '';?> name="aj_existing_users" id="aj_existing_users_all" type="radio" value="none">
                            <label for="aj_existing_users_none"> <?php _e('None', 'bp-auto-group-join');?> </label>
                        </li>
                    </ul>
                </fieldset>
            </div>
        <?php
        }

    }
	 //End of class BP_Auto_Group_Join_Admin
	new BP_Auto_Group_Join_Admin();

endif;

