<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 */

/**
 * The LD course plugin class.
 *
 * @since      1.0.0
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
namespace LdccCourse;

class LdccCourse
{

    protected static $course_id=0;
    protected static $new_course_id=0;

    /**
     *
     * @since    1.0.0
     */

    public function __construct()
    {
    }

    public static function createDuplicateCourse()
    {
        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
        $course_nonce = filter_input(INPUT_POST, 'course');
        $nonce_check = wp_verify_nonce($course_nonce, 'dup_course_' . $course_id);

        if ($nonce_check === false) {
            echo json_encode(array( "error" => __("Security check failed.", "ld-content-cloner") ));
            die();
        }

        if ((!isset($course_id)) || !(get_post_type($course_id) == 'sfwd-courses')) {
            echo json_encode(array( "error" => __("The current post is not a Course and hence could not be cloned.", "ld-content-cloner") ));
            die();
        }

        $course_post = get_post($course_id, ARRAY_A);


        $course_post = self::stripPostData($course_post);

        $new_course_id = wp_insert_post($course_post, true);

        if (! is_wp_error($new_course_id)) {
            self::setMeta("course", $course_id, $new_course_id);
            $lessons = learndash_get_course_lessons_list($course_id);
            $quizzes = learndash_get_global_quiz_list($course_id);

            $c_data = array( 'lesson' => array(), 'quiz' => array() );
            
            if (!empty($lessons)) {
                foreach ($lessons as $key => $lesson) {
                    $c_data['lesson'][] = array( $lesson['post']->ID, $lesson['post']->post_title );
                }
            }

            if (!empty($quizzes)) {
                foreach ($quizzes as $key => $quiz) {
                    $c_data['quiz'][] = array( $quiz->ID, $quiz->post_title );
                }
            }

            $send_result = array( "success" => array( "new_course_id" => $new_course_id, "c_data" => $c_data, ) );
            echo json_encode($send_result);
        } else {
            echo json_encode(array( "error" => __("Some error occurred. The Course could not be cloned.", "ld-content-cloner") ));
        }

        die();
    }

    public static function createDuplicateLesson()
    {
        $lesson_id = filter_input(INPUT_POST, 'lesson_id', FILTER_VALIDATE_INT);
        if ((!isset($lesson_id)) || !(get_post_type($lesson_id) == 'sfwd-lessons')) {
            echo json_encode(array( "error" => __("The current post is not a Lesson and hence could not be cloned.", "ld-content-cloner") ));
            die();
        }

        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
        if ((!isset($course_id)) || !(get_post_type($course_id) == 'sfwd-courses')) {
            echo json_encode(array( "error" => __("The course ID provided with is incorrect for the lesson.", "ld-content-cloner") ));
            die();
        }
        $lesson_post = get_post($lesson_id, ARRAY_A);

        $lesson_post = self::stripPostData($lesson_post);

        $new_lesson_id = wp_insert_post($lesson_post, true);

        if (! is_wp_error($new_lesson_id)) {
            $meta_result = self::setMeta(
                'lesson',
                $lesson_id,
                $new_lesson_id,
                array(
                                        "course_id" => $course_id
                                    )
            );
            $topics = learndash_get_topic_list($lesson_id);
            foreach ($topics as $sin_topic_obj) {
                self::duplicateUnit($sin_topic_obj->ID, $new_lesson_id, $course_id);
            }
            
            $quizzes = learndash_get_lesson_quiz_list($lesson_id);
            foreach ($quizzes as $quiz) {
                self::duplicateQuiz($quiz['post']->ID, $new_lesson_id, $course_id);
            }

            $send_result = array( "success" => array( ) );
        } else {
            $send_result = array( "error" => __("Some error occurred. The Lesson was not fully cloned.", "ld-content-cloner") );
        }
        echo json_encode($send_result);
        unset($meta_result);
        die();
    }

    public static function duplicateUnit($unit_id, $lesson_id, $course_id)
    {
        $unit_post = get_post($unit_id, ARRAY_A);

        $unit_post = self::stripPostData($unit_post);

        $new_unit_id = wp_insert_post($unit_post, true);
        if (! is_wp_error($new_unit_id)) {
            $meta_result = self::setMeta(
                'unit',
                $unit_id,
                $new_unit_id,
                array(
                                        "lesson_id" => $lesson_id,
                                        "course_id" => $course_id
                                    )
            );
            $quizzes = learndash_get_lesson_quiz_list($unit_id);
            foreach ($quizzes as $quiz) {
                self::duplicateQuiz($quiz['post']->ID, $new_unit_id, $course_id);
            }
        }
        unset($meta_result);
    }

    public static function duplicateQuiz($quiz_id = 0, $lesson_id = 0, $course_id = 0)
    {
        // duplicate quiz post
        $send_response = false;
        if ($quiz_id == 0) {
            $quiz_id = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $send_response = true;
        }
        $quiz_post = get_post($quiz_id, ARRAY_A);

        $quiz_post = self::stripPostData($quiz_post);

        $new_quiz_id = wp_insert_post($quiz_post, true);
        if (! is_wp_error($new_quiz_id)) {
            $meta_result = self::setMeta(
                'quiz',
                $quiz_id,
                $new_quiz_id,
                array(
                                        "lesson_id" => $lesson_id,
                                        "course_id" => $course_id
                                    )
            );
            $ld_quiz_data = get_post_meta($new_quiz_id, '_sfwd-quiz', true);
            $pro_quiz_id = $ld_quiz_data['sfwd-quiz_quiz_pro'];
            global $wpdb;
            $_prefix = $wpdb->prefix.'wp_pro_quiz_';
            
            $_tableQuestion = $_prefix.'question';
            $_tableMaster = $_prefix.'master';
            $_tablePrerequisite = $_prefix.'prerequisite';
            $_tableForm = $_prefix.'form';

            // fetch and create in top quiz master table ( wp_pro_quiz_master )
            $pq_query = "SELECT * FROM $_tableMaster WHERE id = %d;";

            
            
            $pro_quiz = $wpdb->get_row($wpdb->prepare($pq_query, $pro_quiz_id), ARRAY_A);

            unset($pro_quiz['id']);
            $pro_quiz['name'] .= " Copy";

            $format = array( '%s','%s','%s','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%s','%d','%d','%d','%d','%d','%d','%d','%d','%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d' );

            $ins_result = $wpdb->insert($_tableMaster, $pro_quiz, $format);

            $wp_pro_quiz_id = 0;

            if ($ins_result !== false) {
                $wp_pro_quiz_id = $wpdb->insert_id;
                $ld_quiz_data['sfwd-quiz_quiz_pro'] = $wp_pro_quiz_id;
                update_post_meta($new_quiz_id, '_sfwd-quiz', $ld_quiz_data);
                // fetch and create in pre-requisites table ( wp_pro_quiz_prerequisite )
                $pqr_query = "SELECT * FROM $_tablePrerequisite WHERE prerequisite_quiz_id = %d;";
                $pror_quizzes = $wpdb->get_results($wpdb->prepare($pqr_query, $pro_quiz_id), ARRAY_A);
                if (!empty($pror_quizzes)) {
                    foreach ($pror_quizzes as $pror_quiz) {
                        $pror_quiz['prerequisite_quiz_id'] = $wp_pro_quiz_id;
                        $ins_result = $wpdb->insert($_tablePrerequisite, $pror_quiz, array( '%s', '%s', ));
                    }
                }
                // copy pro quiz questions ( wp_pro_quiz_question )
                $questionArr = self::getQuestions($pro_quiz_id);
                if (!empty($questionArr)) {
                    self::copyQuestions($wp_pro_quiz_id, $questionArr);
                }
                //copy custom fields in quiz
                $frm_query = "SELECT * FROM $_tableForm WHERE quiz_id = %d;";
                $frm_quizzes = $wpdb->get_results($wpdb->prepare($frm_query, $pro_quiz_id), ARRAY_A);
                if (!empty($frm_quizzes)) {
                    foreach ($frm_quizzes as $frm_quiz) {
                        unset($frm_quiz['form_id']);
                        $frm_quiz['quiz_id'] = $wp_pro_quiz_id;
                        $frm_ins_result = $wpdb->insert($_tableForm, $frm_quiz, array( '%d', '%s', '%d', '%d', '%d', '%s'));
                    }
                }
            }
            $send_result = array( "success" => array( ) );
        } else {
            $send_result = array( "error" => __("Some error occurred. The Quiz was not fully cloned.", "ld-content-cloner") );
        }

        if ($send_response) {
            echo json_encode($send_result);
            die();
        }
        unset($meta_result);
        unset($_tableQuestion);
        unset($frm_ins_result);
    }

    public static function getQuestions($quizId)
    {
        $quizMapper = new \WpProQuiz_Model_QuizMapper();
        $questionMapper = new \WpProQuiz_Model_QuestionMapper();
        $data = array();
        $quiz = $quizMapper->fetch($quizId);
        $questions = $questionMapper->fetchAll($quiz->getId());
        $questionArray = array();
        
        foreach ($questions as $qu) {
            $questionArray[] = $qu->getId();
        }

        return $questionArray;
        unset($data);
    }

    public static function copyQuestions($quizId, $questionArray)
    {
        $questionMapper = new \WpProQuiz_Model_QuestionMapper();

        $questions = $questionMapper->fetchById($questionArray);

        foreach ($questions as $question) {
            $question->setId(0);
            $question->setQuizId($quizId);
            $questionMapper->save($question);
        }
    }

    public static function stripPostData($post_array)
    {
        $exclude_remove = array( 'post_content', 'post_title', 'post_status', 'post_type', 'comment_status', 'ping_status' );
        foreach ($post_array as $key => $value) {
            if (!in_array($key, $exclude_remove)) {
                unset($post_array[ $key ]);
            }
        }
        
        $post_array['post_status'] = "draft";
        $post_array['post_title'] .= " Copy";
        return $post_array;
        unset($value);
    }

    public static function setMeta($post_type, $old_post_id, $new_post_id, $other_data = array())
    {
        global $wpdb;
        if (!empty($old_post_id) && !empty($new_post_id)) {
            if ($post_type == 'course') {
                $ld_data = get_post_meta($old_post_id, '_sfwd-courses', true);
                if (!empty($ld_data)) {
                    $ld_data=self::getDetaultValues($ld_data);
                    if (!empty($ld_data['sfwd-courses_course_price_type'])) {
                        if ($ld_data['sfwd-courses_course_price_type'] == 'subscribe') {
                            $billing_cycle_time=get_post_meta($old_post_id, 'course_price_billing_t3', true);
                            update_post_meta($new_post_id, 'course_price_billing_t3', $billing_cycle_time);
                            $billing_cycle_day=get_post_meta($old_post_id, 'course_price_billing_p3', true);
                            update_post_meta($new_post_id, 'course_price_billing_p3', $billing_cycle_day);
                        }
                    }
                }
                $thumbnail = get_post_meta($old_post_id, '_thumbnail_id', true);
                $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships where object_id=".$old_post_id);
                if (!empty($term_taxonomy_ids)) {
                    foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                        $wpdb->insert(
                            'wp_term_relationships',
                            array(
                                'object_id' => $new_post_id,
                                'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                                'term_order' => 0
                            ),
                            array(
                                '%d',
                                '%d',
                                '%d'
                            )
                        );
                    }
                }
                update_post_meta($new_post_id, '_sfwd-courses', $ld_data);
                update_post_meta($new_post_id, '_thumbnail_id', $thumbnail);
            } elseif ($post_type == 'lesson') {
                $sent_c_id = $other_data['course_id'];
                $ld_data = get_post_meta($old_post_id, '_sfwd-lessons', true);
                $lesson_course_id = $sent_c_id;
                
                $ld_data['sfwd-lessons_course'] = $lesson_course_id;

                $thumbnail = get_post_meta($old_post_id, '_thumbnail_id', true);
                $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships where object_id=".$old_post_id);
                if (!empty($term_taxonomy_ids)) {
                    foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                        $wpdb->insert(
                            'wp_term_relationships',
                            array(
                                'object_id' => $new_post_id,
                                'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                                'term_order' => 0
                            ),
                            array(
                                '%d',
                                '%d',
                                '%d'
                            )
                        );
                    }
                }
                $old_lesson = get_post($old_post_id);
                $menu_order = $old_lesson->menu_order;
                $new_lesson_order = array(
                    'ID'           => $new_post_id,
                    'menu_order'   => $menu_order,
                );
                wp_update_post($new_lesson_order);
                update_post_meta($new_post_id, '_sfwd-lessons', $ld_data);
                update_post_meta($new_post_id, 'course_id', $lesson_course_id);
                update_post_meta($new_post_id, '_thumbnail_id', $thumbnail);
                update_post_meta($new_post_id, 'course_'.$lesson_course_id.'_lessons_list', learndash_get_lesson_list($lesson_course_id));
            } elseif ($post_type == 'unit') {
                $unit_course_id = $other_data['course_id'];
                $unit_lesson_id = $other_data['lesson_id'];
                $ld_data = get_post_meta($old_post_id, '_sfwd-topic', true);
                
                $ld_data['sfwd-topic_course'] = $unit_course_id;
                $ld_data['sfwd-topic_lesson'] = $unit_lesson_id;

                $thumbnail = get_post_meta($old_post_id, '_thumbnail_id', true);
                $old_topic = get_post($old_post_id);
                $menu_order = $old_topic->menu_order;
                $new_topic_order = array(
                    'ID'           => $new_post_id,
                    'menu_order'   => $menu_order,
                );
                $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships where object_id=".$old_post_id);
                if (!empty($term_taxonomy_ids)) {
                    foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                        $wpdb->insert(
                            'wp_term_relationships',
                            array(
                                'object_id' => $new_post_id,
                                'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                                'term_order' => 0
                            ),
                            array(
                                '%d',
                                '%d',
                                '%d'
                            )
                        );
                    }
                }
                wp_update_post($new_topic_order);
                update_post_meta($new_post_id, '_sfwd-topic', $ld_data);
                update_post_meta($new_post_id, 'course_id', $unit_course_id);
                update_post_meta($new_post_id, 'lesson_id', $unit_lesson_id);
                update_post_meta($new_post_id, '_thumbnail_id', $thumbnail);
                update_post_meta($new_post_id, 'lesson_'.$new_post_id.'_topic_list', learndash_get_topic_list($new_post_id));
            } elseif ($post_type == 'quiz') {
                $unit_course_id = $other_data['course_id'];
                $unit_lesson_id = $other_data['lesson_id'];
                $ld_data = get_post_meta($old_post_id, '_sfwd-quiz', true);

                $ld_data['sfwd-quiz_course'] = $unit_course_id;
                $ld_data['sfwd-quiz_lesson'] = $unit_lesson_id;

                $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships where object_id=".$old_post_id);
                if (!empty($term_taxonomy_ids)) {
                    foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                        $wpdb->insert(
                            'wp_term_relationships',
                            array(
                                'object_id' => $new_post_id,
                                'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                                'term_order' => 0
                            ),
                            array(
                                '%d',
                                '%d',
                                '%d'
                            )
                        );
                    }
                }
                $old_quiz = get_post($old_post_id);
                $menu_order = $old_quiz->menu_order;
                $new_quiz_order = array(
                    'ID'           => $new_post_id,
                    'menu_order'   => $menu_order,
                );
                wp_update_post($new_quiz_order);
                $thumbnail = get_post_meta($old_post_id, '_thumbnail_id', true);
                $viewProfileStatistics = get_post_meta($old_post_id, '_viewProfileStatistics', true);
                $_timeLimitCookie=get_post_meta($old_post_id, '_timeLimitCookie', true);
                update_post_meta($new_post_id, '_timeLimitCookie', $_timeLimitCookie);
                update_post_meta($new_post_id, '_sfwd-quiz', $ld_data);
                update_post_meta($new_post_id, 'course_id', $unit_course_id);
                update_post_meta($new_post_id, 'lesson_id', $unit_lesson_id);
                update_post_meta($new_post_id, '_thumbnail_id', $thumbnail);
                update_post_meta($new_post_id, '_viewProfileStatistics', $viewProfileStatistics);
            }
            return true;
        }
        return false;
    }
    public static function getDetaultValues($ld_data)
    {
        if (empty($ld_data['sfwd-courses_course_lesson_orderby'])) {
            $ld_data['sfwd-courses_course_lesson_orderby'] = 'menu_order';
        }
        if (empty($ld_data['sfwd-courses_course_lesson_order'])) {
            $ld_data['sfwd-courses_course_lesson_order'] = 'ASC';
        }
        if (!empty($ld_data['sfwd-courses_course_access_list'])) {
            $ld_data['sfwd-courses_course_access_list'] = '';
        }
        return $ld_data;
    }
    public function addCourseRowActions($actions, $post_data)
    {
        if (get_post_type($post_data->ID) === 'sfwd-courses') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_course' => '<a href="#" title="Clone this course" class="ldcc-clone-course" data-course-id="' . $post_data->ID . '" data-course="' . wp_create_nonce('dup_course_' . $post_data->ID) . '">' . __('Clone Course') . '</a>'
                        )
            );
        }
        return $actions;
    }

    public function addLessonRowActions($actions, $post_data)
    {
        if (get_post_type($post_data->ID) === 'sfwd-lessons') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_lesson' => '<a href="#" title="Clone this lesson" class="ldcc-clone-lesson" data-lesson-id="' . $post_data->ID . '" >' . __('Clone Lesson') . '</a>'
                        )
            );
        } elseif (get_post_type($post_data->ID) === 'sfwd-quiz') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_quiz' => '<a href="#" title="Clone quiz" class="ldcc-clone-quiz" data-quiz-id="' . $post_data->ID . '" data-course-id="'.get_post_meta($post_data->ID, 'course_id', true).'">' . __('Clone Quiz') . '</a>'
                        )
            );
        }
        return $actions;
    }

    public function addModalStructure()
    {
        global $current_screen;
        
        if (isset($current_screen) && in_array($current_screen->post_type, array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-quiz' ))) {
            ?>
            <div id="ldcc-dialog" title="<?php _e("Course Cloning", "ld-content-cloner");
            ?>">
                
                <div class="ldcc-success">
                    <div>
                        <?php echo sprintf(__("Click %s to edit the cloned Course", "ld-content-cloner"), "<a class='ldcc-course-link' href='#'>".__("here", "ld-content-cloner") . "</a>");
            ?>
                    </div>
                    <div>
                        <?php echo sprintf(__("Click %s to rename the cloned Course content", "ld-content-cloner"), "<a class='ldcc-course-rename-link' href='#'>".__("here", "ld-content-cloner") . "</a>");
            ?>
                    </div>
                </div>

                <div class="ldcc-notice"><?php _e("Note: Remember to change the Title and Slugs for all the cloned Posts.", "ld-content-cloner");
            ?></div>

            </div>
            <?php
        }
    }
}
