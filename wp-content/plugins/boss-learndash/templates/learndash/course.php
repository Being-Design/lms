<?php
/**
 * Displays a course
 *
 * Available Variables:
 * $course_id 		: (int) ID of the course
 * $course 		: (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id 		: Current User ID
 * $logged_in 		: User is logged in
 * $current_user 	: (object) Currently logged in user object
 *
 * $course_status 	: Course Status
 * $has_access 	: User has access to course or is enrolled.
 * $materials 		: Course Materials
 * $has_course_content		: Course has course content
 * $lessons 		: Lessons Array
 * $quizzes 		: Quizzes Array
 * $lesson_progression_enabled 	: (true/false)
 * $has_topics		: (true/false)
 * $lesson_topics	: (array) lessons topics
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */
?>

<!-- Container added by boss -->
<div class="lms-post-content">
	<?php if ( $logged_in ) : ?>
		<?php
			/**
			 * Filter to add custom content after the Course Status section of the Course template output.
			 * @since 2.3
			 * See https://bitbucket.org/snippets/learndash/7oe9K for example use of this filter.
			 */
			echo apply_filters('ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id );
		?>

		<?php  if ( ! empty( $course_certficate_link ) ) : ?>
			<div id="learndash_course_certificate">
				<a href='<?php echo esc_attr( $course_certficate_link ); ?>' class="btn-blue" target="_blank"><?php echo apply_filters('ld_certificate_link_label', __( 'PRINT YOUR CERTIFICATE', 'boss-learndash' ), $user_id, $post->ID ); ?></a>
			</div>
			<br />
		<?php endif; ?>
	<?php endif; ?>

	<?php echo $content; ?>
</div>

<?php
/**
 * Payment Buttons
 */
if ( !$has_access ) :
	echo boss_edu_payment_buttons( $post );
endif;

/**
 * Course Content
 */
if ( $has_course_content ) {

	$show_course_content = true;

	if ( !$has_access ) :
		if ( $course_meta[ 'sfwd-courses_course_disable_content_table' ] == 'on' ) :
			$show_course_content = false;
		endif;
	endif;

	if ( $show_course_content ) :

		/**
		 * Course Materials
		 */
		if ( isset( $materials ) ) :
			?>
			<div id="learndash_course_materials">
				<h4><?php printf( _x( '%s Materials', 'Course Materials Label', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h4>
				<div class="materials-content"><?php echo $materials; ?></div>
			</div><?php
		endif;
		?>

		<div id='learndash_course_content'>

			<?php
			/* Show Lesson List */
			if ( !empty( $lessons ) ) {
				?>

				<div id="learndash_lessons">

					<div id="lesson_heading">
						<span><?php echo LearnDash_Custom_Label::get_label( 'lessons' ) ?></span>

						<?php if ( $has_topics ) : ?>
							<div class="expand_collapse">
								<a href="#" onClick='jQuery( "#learndash_post_<?php echo $course_id; ?> .learndash_topic_dots" ).slideDown();
				                        return false;'><?php _e( 'Expand All', 'boss-learndash' ); ?></a> |
								<a href="#" onClick='jQuery( "#learndash_post_<?php echo esc_attr( $course_id ); ?> .learndash_topic_dots" ).slideUp();
				                        return false;'><?php _e( 'Collapse All', 'boss-learndash' ); ?></a>
							</div>
						<?php endif; ?>
					</div>

					<div id="lessons_list">
						<?php foreach ( $lessons as $lesson ) { ?>
							<?php
							/* Lesson Topis */
							$topics	 = @$lesson_topics[ $lesson[ 'post' ]->ID ];
							$meta	 = get_post_meta( $lesson[ "post" ]->ID, '_' . $lesson[ "post" ]->post_type );

							/* Lesson meta info */
							$lesson_meta_data = array();

							if ( is_array( $topics ) && 0 < count( $topics ) ) {
								$lesson_meta_data[] = sprintf( _n( '%s topic', '%s topics', count( $topics ), 'boss-learndash' ), count( $topics ) );
							}

							if ( !empty( $meta[ 0 ][ $lesson[ "post" ]->post_type . '_forced_lesson_time' ] ) ) {
								$lesson_meta_data[] = $meta[ 0 ][ $lesson[ "post" ]->post_type . '_forced_lesson_time' ];
							}
							?>

							<div class="lesson post-<?php echo $lesson[ "post" ]->ID; ?> <?php echo $lesson[ "sample" ]; ?> <?php echo (empty( $topics )) ? 'no-topics' : 'has-topics' ?>">
								<h4>
									<a class="<?php echo $lesson[ "status" ]; ?>" href="<?php echo $lesson[ "permalink" ] ?>"><?php echo $lesson[ "post" ]->post_title; ?></a>
									<p class="lesson-meta">
										<span class="lesson-meta-data"><?php echo implode( ', ', $lesson_meta_data ); ?></span>
										<span class="drop-list fa fa-chevron-down"></span>
									</p>
								</h4>

								<?php
								/* Not available message for drip feeding lessons */
								if ( !empty( $lesson[ "lesson_access_from" ] ) ) {
									?>
									<small class='notavailable_message'>
										<?php echo sprintf( __( ' Available on: %s ', "boss-learndash" ), date( "d-M-Y", $lesson[ "lesson_access_from" ] ) ); ?>
									</small>
									<?php
								}

								if ( !empty( $topics ) ) {
									?>
									<div id="learndash_topic_dots-<?php echo esc_attr( $lesson[ 'post' ]->ID ); ?>" class="learndash_topic_dots type-list">
										<ul>
											<?php
											$odd_class = '';

											foreach ( $topics as $key => $topic ) {
												$odd_class		 = empty( $odd_class ) ? 'nth-of-type-odd' : '';
												$completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed';
												?>
												<li class="<?php echo $odd_class; ?>">
													<span class="topic_item">
														<a class="<?php echo $completed_class; ?>" href="<?php echo get_permalink( $topic->ID ); ?>" title="<?php echo $topic->post_title; ?>">
															<?php echo $topic->post_title; ?>
														</a>
													</span>
												</li><?php
											}
											?>
										</ul>
									</div><?php
								}
								?>

							</div><?php
						}
						?>
					</div>

				</div><?php
			}

			/* Show Quiz List */
			if ( !empty( $quizzes ) ) {
				?>
				<div id='learndash_quizzes'>
					<div id="quiz_heading"><span><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ) ?></span></div>
					<div id="quiz_list">
						<?php foreach ( $quizzes as $quiz ) { ?>
							<div id="post-<?php echo $quiz[ "post" ]->ID; ?>" class="<?php echo $quiz[ "sample" ]; ?>">
								<h4>
									<a class="<?php echo $quiz[ "status" ]; ?>" href="<?php echo $quiz[ "permalink" ] ?>"><?php echo $quiz[ "post" ]->post_title; ?></a>
								</h4>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php
			}
			?>

		</div><?php
	endif;
}
