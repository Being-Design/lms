<?php

/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;


function bp_auto_group_join_all_members( $group_id, $member_types = array() ){
    if( !isset($group_id) || empty($group_id) ) return;

        if( isset($member_types) && !empty($member_types) ){
            $all_users = bp_auto_group_join_get_members($member_types);
        }else{
            $all_users = bp_auto_group_join_get_members();
        }

        if( isset($all_users) && !empty($all_users) ){
            foreach($all_users as $single){
                $user_id = $single;
                // check if already member
                $membership = new BP_Groups_Member( $user_id, $group_id );
                if( !isset($membership->ID) ){
                    // add as member
                    groups_accept_invite($user_id, $group_id);
                }
            }
        }
}

function bp_auto_group_join_get_members($member_types = array() ){
    $member_ids = array();
    $member_args = array(
        'object' => 'members',
        'type' => 'alphabetical',
        'per_page' => 0,
    );
    if( isset($member_types) && !empty($member_types) ){
        $member_args['member_type'] = $member_types;
    }

    if ( bp_has_members( $member_args ) ) :
        while ( bp_members() ) : bp_the_member();
            $member_ids[] = bp_get_member_user_id();
        endwhile;
    endif;

    return $member_ids;
}