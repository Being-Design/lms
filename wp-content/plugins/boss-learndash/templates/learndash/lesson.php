<?php
/**
 * Displays a lesson.
 *
 * Available Variables:
 *
 * $course_id 		: (int) ID of the course
 * $course 		: (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status 	: Course Status
 * $has_access 	: User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id 		: (object) Current User ID
 * $logged_in 		: (true/false) User is logged in
 * $current_user 	: (object) Currently logged in user object
 *
 * $quizzes 		: (array) Quizzes Array
 * $post 			: (object) The lesson post object
 * $topics 		: (array) Array of Topics in the current lesson
 * $all_quizzes_completed : (true/false) User has completed all quizzes on the lesson Or, there are no quizzes.
 * $lesson_progression_enabled 	: (true/false)
 * $show_content	: (true/false) true if lesson progression is disabled or if previous lesson is completed.
 * $previous_lesson_completed 	: (true/false) true if previous lesson is completed
 * $lesson_settings : Settings specific to the current lesson.
 *
 * @since 2.1.0
 *
 * @package LearnDash\Lesson
 */
?>

<div id="post-entries">
	<div class="nav-prev">
		<?php echo learndash_previous_post_link(); ?>
	</div>
	<div class="nav-next">
		<?php echo learndash_next_post_link(); ?>
	</div>
</div>

<?php if ( @$lesson_progression_enabled && ! @$previous_lesson_completed ) : ?>
	<span id="learndash_complete_prev_lesson">
	<?php
		$previous_item = learndash_get_previous( $post );
		if ( ( !empty( $previous_item ) ) && ( $previous_item instanceof WP_Post ) ) {
			if ( $previous_item->post_type == 'sfwd-quiz') {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: quiz URL, quiz label', 'boss-learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('quiz') );

			} else if ( $previous_item->post_type == 'sfwd-topic') {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: topic URL, topic label', 'boss-learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('topic') );
			} else {
				echo sprintf( _x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%s">%s</a>.', 'placeholders: lesson URL, lesson label', 'boss-learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower('lesson') );
			}

		} else {
			echo sprintf( _x( 'Please go back and complete the previous %s.', 'placeholder lesson', 'boss-learndash' ), LearnDash_Custom_Label::label_to_lower('lesson') );
		}
	?>
	</span>
	<?php add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 ); ?>
<?php endif; ?>

<?php
if ( $show_content ) {
	$course_video_embed = get_post_meta( $post->ID, '_boss_edu_post_video', true );

	if ( 'http' == substr( $course_video_embed, 0, 4 ) ) {
		// V2 - make width and height a setting for video embed /* , array( 'width' => 100 , 'height' => 100) */
		$course_video_embed = wp_oembed_get( esc_url( $course_video_embed ) );
	}

	if ( '' != $course_video_embed ) {
		?><div class="lesson-video"><?php echo html_entity_decode( $course_video_embed ); ?></div><?php
	}
}
?>

<header class="entry-header">
	<span><?php echo LearnDash_Custom_Label::get_label( 'lesson' ); ?></span>
	<h1 class="entry-title"><?php the_title(); ?></h1>
</header>

<?php if ( $show_content ) { ?>

	<div class="lms-post-content">
		<?php echo $content; ?>
	</div>

	<?php
	/**
	 * Lesson Topics
	 */
	if ( !empty( $topics ) ) {
		?>
		<div id="learndash_lesson_topics_list">
			<div id="learndash_topic_dots-<?php echo $post->ID; ?>" class="learndash_topic_dots type-list">
				<h2><?php echo LearnDash_Custom_Label::get_label( 'lesson' ) . ' ' . LearnDash_Custom_Label::get_label( 'topics' ) ?></h2>
				<ul>
					<?php
					$odd_class = '';
					foreach ( $topics as $key => $topic ) {
						$odd_class		 = empty( $odd_class ) ? 'nth-of-type-odd' : '';
						$completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed';
						?>
						<li class="<?php echo esc_attr( $odd_class ); ?>">
							<span class="topic_item">
								<a class="<?php echo esc_attr( $completed_class ); ?>" href="<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>" title="<?php echo esc_attr( $topic->post_title ); ?>">
									<?php echo $topic->post_title; ?>
								</a>
							</span>
						</li><?php
					}
					?>
				</ul>
			</div>
		</div><?php
	}

	/**
	 * Show Quiz List
	 */
	if ( !empty( $quizzes ) ) :
		?>
		<div id="learndash_quizzes">
			<div id="quiz_heading"><span><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ); ?></span><span class="right">&nbsp;<?php _e( 'Status', 'boss-learndash' ); ?> </span></div>
			<div id="quiz_list">

				<?php foreach ( $quizzes as $quiz ) : ?>
					<div id="post-<?php echo esc_attr( $quiz[ 'post' ]->ID ); ?>" class="<?php echo esc_attr( $quiz[ 'sample' ] ); ?>">
						<h4>
							<a class="<?php echo esc_attr( $quiz[ 'status' ] ); ?>" href="<?php echo esc_attr( $quiz[ 'permalink' ] ); ?>"><?php echo $quiz[ 'post' ]->post_title; ?></a>
						</h4>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
		<?php
	endif;

	/**
	 * Display Lesson Assignments
	 */
	if ( lesson_hasassignments( $post ) ) :
		?>
		<?php $assignments = learndash_get_user_assignments( $post->ID, $user_id ); ?>

		<div id="learndash_uploaded_assignments">
			<h2><?php _e( 'Files you have uploaded', 'boss-learndash' ); ?></h2>
			<table>
				<?php if ( !empty( $assignments ) ) : ?>
					<?php foreach ( $assignments as $assignment ) : ?>
						<tr>
							<td>
								<a href='<?php echo esc_attr( get_post_meta( $assignment->ID, 'file_link', true ) ); ?>' target="_blank"><?php echo __( 'Download', 'boss-learndash' ) . ' ' . get_post_meta( $assignment->ID, 'file_name', true ); ?></a>
								<br />
								<span class="learndash_uploaded_assignment_points"><?php echo learndash_assignment_points_awarded( $assignment->ID ); ?></span>
							</td>
							<td><a href='<?php echo esc_attr( get_permalink( $assignment->ID ) ); ?>'><?php _e( 'Comments', 'boss-learndash' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</table>
		</div>
		<?php
	endif;

	/**
	 * Display Mark Complete Button
	 */
	if ( $all_quizzes_completed && $logged_in ) :
		echo learndash_mark_complete( $post );
	endif;
}
