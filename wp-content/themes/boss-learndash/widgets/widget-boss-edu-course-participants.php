<?php
/**
 * Course Participants Widget
 *
 *
 * @package WordPress
 * @subpackage Boss for LearnDash
 * @category Widgets
 * @author BuddyBoss
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Boss_LearnDash_Course_Participants_Widget extends WP_Widget {
	protected $boss_edu_widget_cssclass;
	protected $boss_edu_widget_description;
	protected $boss_edu_widget_idbase;
	protected $boss_edu_widget_title;

	/**
	 * Constructor function.
	 * @since  1.1.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->boss_edu_widget_cssclass = 'widget_learndash_course_participants';
		$this->boss_edu_widget_description = sprintf( __( 'Displays a list of learners taking the current %s, with links to their profiles (if public).', 'boss-learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) );
		$this->boss_edu_widget_idbase = 'widget_learndash_course_participants';
		$this->boss_edu_widget_title = sprintf( __( '(BuddyBoss) - %s Participants', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->boss_edu_widget_cssclass, 'description' => $this->boss_edu_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->boss_edu_widget_idbase );

		/* Create the widget. */
		parent::__construct( $this->boss_edu_widget_idbase, $this->boss_edu_widget_title, $widget_ops, $control_ops );
	}

    /**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		global $current_user, $wpdb;
        $bp = buddypress();
		extract( $args );

		//Array of course participants user ids
		$learners = array();

		if ( ( is_singular( array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic') ) ) ) {
			if ( get_post_type() == 'sfwd-courses' ) {
				$course_id = get_the_ID();
			}

			if ( get_post_type() == 'sfwd-lessons' ) {
				$course_id = get_post_meta(get_the_ID(),'course_id',true);
			}

			if ( get_post_type() == 'sfwd-topic' ) {
				$lesson_id = get_post_meta(get_the_ID(),'lesson_id',true);
				$course_id = get_post_meta($lesson_id,'course_id',true);
			}

			//Select group id attached with course
			$sql        = "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE
						   meta_key = 'bp_course_attached' AND meta_value = {$course_id}";

			$group_id   = $wpdb->get_var( $sql );

		} else {
			$course_id = groups_get_groupmeta( bp_get_group_id(), 'bp_course_attached', true );
			$group_id = bp_get_group_id();
		}

		/* Select all users from course access list and add them to course participants list */
		$meta = get_post_meta( $course_id, '_sfwd-courses', true );
		if ( ! empty( $meta['sfwd-courses_course_access_list'] ) ) {
			$learners = explode( ',', $meta['sfwd-courses_course_access_list'] );
		}

        //group id will be empty if the course is not associated with any group
        if( $group_id ){
            /* Select all members from group attached with course and add them to course participants list */
            $sql            = "SELECT user_id FROM {$bp->groups->table_name_members} WHERE is_banned = 0 AND is_confirmed = 1 AND
                               group_id = {$group_id}";
            $group_members  = $wpdb->get_col( $sql );
        }

		if ( ! empty( $group_members ) ) {
			$learners = array_merge( $learners, $group_members );
		}

		if ( isset( $instance['title'] ) ) {
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
		}
		if ( isset( $instance['limit'] ) && ( 0 < count( $instance['limit'] ) ) ) {
			$limit = intval( $instance['limit'] );
		}

		// Frontend Output
		echo $before_widget;

		/* Display the widget title if one was input */
		if ( $title ) { echo $before_title . $title . $after_title; }

		// Add actions for plugins/themes to hook onto.
		do_action( $this->boss_edu_widget_cssclass . '_top' );

		$html = '';
		if( empty( $learners ) ) {
			$html .= '<p>' . __( 'There are no other learners currently taking this course. Be the first!', 'boss-learndash' ) . '</p>';
		} else {

			$list_class = 'list';
			$html .= '<ul class="learndash-course-participants-list' . ' ' . $list_class . '">';

			// Begin templating logic.
			$tpl = '<li class="learndash-course-participant fix %%CLASS%%">%%IMAGE%%%%TITLE%%</li>';
			$tpl = apply_filters( 'learndash_course_participants_template', $tpl );

			$i = 0;
			
            $args = array(
                'include'   => $learners,
                //'max'       => intval( $instance['limit'] ),
            );
            if( bp_has_members( $args ) ){
                while ( bp_members() ){
                    bp_the_member();
                    
                    $i++;
                    $class = $i <= $limit ? 'show' : 'hide';
                
                    $link = '<a class="debug" href="' . bp_get_member_permalink() . '" title="' . __( 'View public learner profile', 'boss-learndash' ) . '">';
                    
                    $image = '<figure itemprop="image">' . bp_get_member_avatar( array( 'width' => 50, 'height' => 50 ) ) . '</figure>' . "\n";
                    $image = $link . $image . '</a>';
                    
                    $learner_name = '<h3 itemprop="name" class="learner-name">' . bp_get_member_name() . '</h3>' . "\n";
                    $learner_name = $link . $learner_name . '</a>';
                    
                    $template = $tpl;
                    $template = str_replace( '%%CLASS%%', $class, $template );
                    $template = str_replace( '%%IMAGE%%', $image, $template );
                    $template = str_replace( '%%TITLE%%', $learner_name, $template );

                    $html .= $template;
                }
                
                $html .= '</ul>';
                // Display a view all link if not all learners are displayed.
                if( $limit < count( $learners ) ) {
                    $html .= '<div class="learndash-view-all-participants"><a href="#">' . __( 'View all', 'boss-learndash' ) . '</a></div>';
                }

            } else {
                $html .= '<p>' . __( 'There are no other learners currently taking this course. Be the first!', 'boss-learndash' ) . '</p>';
            }

		}

		echo $html;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->boss_edu_widget_cssclass . '_bottom' );

		echo $after_widget;
	} // End widget()
    
	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and limit to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['size'] = intval( $new_instance['size'] );

		/* The select box is returning a text value, so we escape it. */
		$instance['display'] = esc_attr( $new_instance['display'] );

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
						'title' => '',
						'limit' => 5,
						'size' => 50,
						'display' => 'list'
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title (optional):', 'boss-learndash' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"  value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" />
		</p>
		<!-- Widget Limit: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Number of Learners (optional):', 'boss-learndash' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"  value="<?php echo esc_attr( $instance['limit'] ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" />
		</p>

<?php
	} // End form()


	/**
	 * Get an array of the available display options.
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_display_options () {
		return array(
					'list' 			=> __( 'List', 'boss-learndash' ),
					'grid' 			=> __( 'Grid', 'boss-learndash' )
					);
	} // End get_display_options()
}