<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.1
 *
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 */

/**
 * The LD course plugin class.
 *
 * @since      1.0.1
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
namespace LdccGroup;

class LdccGroup
{

    protected static $group_id=0;
    
    protected static $new_group_id=0;

    /**
     *
     * @since    1.0.1
     */

    public function __construct()
    {
    }

    public function addGroupRowActions($actions, $post_data)
    {
        if (get_post_type($post_data->ID) === 'groups') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_group' => '<a href="#" title="Clone this Group" class="ldcc-clone-group" data-group-id="' . $post_data->ID . '" data-group="' . wp_create_nonce('dup_group_' . $post_data->ID) . '">' . __('Clone Group') . '</a>'
                        )
            );
        }
        return $actions;
    }
    public static function createDuplicateGroup()
    {
        $group_id = filter_input(INPUT_POST, 'group_id', FILTER_VALIDATE_INT);
        $course_nonce = filter_input(INPUT_POST, 'group');
        $nonce_check = wp_verify_nonce($course_nonce, 'dup_group_' . $group_id);

        if ($nonce_check === false) {
            echo json_encode(array( "error" => __("Security check failed.", "ld-content-cloner") ));
            die();
        }

        if ((!isset($group_id)) || !(get_post_type($group_id) == 'groups')) {
            echo json_encode(array( "error" => __("The current post is not a Group and hence could not be cloned.", "ld-content-cloner") ));
            die();
        }

        $group_post = get_post($group_id, ARRAY_A);

        $group_post = self::stripPostData($group_post);

        $new_group_id = wp_insert_post($group_post, true);

        if (! is_wp_error($new_course_id)) {
            $group_leaders=learndash_get_groups_administrator_ids($group_id);

            learndash_set_groups_administrators($new_group_id, $group_leaders);

            $group_users=learndash_get_groups_user_ids($group_id);
            learndash_set_groups_users($new_group_id, $group_users);

            $group_enroll_course=learndash_group_enrolled_courses($group_id);
            if (!empty($group_enroll_course)) {
                foreach ($group_enroll_course as $course_id) {
                    update_post_meta($course_id, 'learndash_group_enrolled_' . $new_group_id, time());
                }
            }
            $c_data = array( 'lesson' => array(), 'quiz' => array() );

            $send_result = array( "success" => array( "new_group_id" => $new_group_id, "c_data" => $c_data, ) );
            echo json_encode($send_result);
        } else {
            echo json_encode(array( "error" => __("Some error occurred. The Group could not be cloned.", "ld-content-cloner") ));
        }

        die();
    }

    public static function stripPostData($post_array)
    {
        $exclude_remove = array( 'post_content', 'post_title', 'post_status', 'post_type', 'tags_input' );
        foreach ($post_array as $key => $value) {
            if (!in_array($key, $exclude_remove)) {
                unset($post_array[ $key ]);
            }
        }
        $post_array['post_status'] = "draft";
        $post_array['post_title'] .= " Copy";
        return $post_array;
    }

    public function addModalStructure()
    {
        global $current_screen;

        if (isset($current_screen) && in_array($current_screen->post_type, array( 'groups' )) && !isset($_GET['post'])) {
            ?>
			<div id="ldcc-group-dialog" title="<?php _e("Group Cloning", "ld-content-cloner");
            ?>">

				<div class="ldcc-success">
					<div>
						<?php echo sprintf(__("Click %s to edit the cloned Group", "ld-content-cloner"), "<a class='ldcc-group-link' href='#'>".__("here", "ld-content-cloner") . "</a>");
            ?>
					</div>
				</div>

				<div class="ldcc-notice"><?php _e("Note: Remember to change the Title of the Group.", "ld-content-cloner");
            ?></div>

			</div>
		<?php

        }
    }
}
