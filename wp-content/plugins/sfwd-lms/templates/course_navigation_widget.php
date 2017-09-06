<?php
/**
 * Displays the course navigation widget.
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\Course
 */
?>
<?php
/**
 * Filter to allow override of widget instance arguments. 
 * @since 2.3.3
 */
if ( !isset( $widget_instance ) ) $widget_instance = array();
$widget_instance = apply_filters( 'learndash_course_navigation_widget_args', $widget_instance, $course_id );
?>
<div id="course_navigation">
    <?php
    /**
     * @todo fix typo in navigation - consider reverse compatibility
     */
    ?>
	<div class="learndash_navigation_lesson_topics_list">

		<?php 
		global $post;

		if ( $post->post_type == 'sfwd-topic' || $post->post_type == 'sfwd-quiz')
			$lesson_id = learndash_get_setting( $post, 'lesson');
		else
			$lesson_id = $post->ID;

		if ( ! empty( $lessons) ) {
			foreach( $lessons as $course_lesson) {

				$current_topic_ids = '';
				$topics =  learndash_topic_dots( $course_lesson['post']->ID, false, 'array' );
				
				if ( ( isset( $widget_instance['show_lesson_quizzes'] ) ) && ( $widget_instance['show_lesson_quizzes'] == true ) ) {
					$lesson_quiz_list = learndash_get_lesson_quiz_list( $course_lesson['post']->ID, get_current_user_id() ); 
				} else {
					$lesson_quiz_list = array();
				}
				
				
				$is_current_lesson = ( $lesson_id == $course_lesson['post']->ID );
				$lesson_list_class = ( $is_current_lesson ) ? 'active' : 'inactive';
				$lesson_lesson_completed = ( $course_lesson['status'] == 'completed' ) ? 'lesson_completed' : 'lesson_incomplete';
				$list_arrow_class = (( $is_current_lesson ) && (( !empty( $topics ) ) || ( !empty($lesson_quiz_list ) ))) ? 'expand' : 'collapse';

				if ($lesson_id == $course_lesson['post']->ID)
					$lesson_list_class .= ' learndash-current-menu-ancestor '; 
			
				if ( ( ! empty( $topics ) ) || ( !empty( $lesson_quiz_list ) ) )
					$list_arrow_class .= ' flippable';
				
				$lesson_topic_child_item_active = false;
				?>
				<div class='<?php echo esc_attr( $lesson_list_class ); ?>' id='lesson_list-<?php echo esc_attr( $course_lesson['post']->ID ); ?>'>

                    <div class='list_arrow <?php echo esc_attr( $list_arrow_class ); ?> <?php echo esc_attr( $lesson_lesson_completed ); ?>' onClick='return flip_expand_collapse("#lesson_list", <?php echo esc_attr( $course_lesson['post']->ID ); ?>);' ></div>

					<?php 
						$current_lesson_class = '';
						if ($course_lesson['post']->ID == $post->ID)
							$current_lesson_class .= ' learndash-current-menu-item '; 
					?>

                    <div class="list_lessons">
						<div class="lesson <?php echo $current_lesson_class ?>" >
							<a href='<?php echo esc_attr( get_permalink( $course_lesson['post']->ID ) ); ?>'><?php echo $course_lesson['post']->post_title; ?></a>
						</div> 

						<?php if ( ! empty( $topics ) ) { ?>
							<div id='learndash_topic_dots-<?php echo esc_attr( $course_lesson['post']->ID ); ?>' class="flip learndash_topic_widget_list"  style='<?php echo ( strpos( $list_arrow_class, 'collapse' ) !== false ) ? 'display:none' : '' ?>'>
								<ul>								
									<?php 
										$odd_class = '';

										foreach ( $topics as $key => $topic ) {
											$odd_class = empty( $odd_class ) ? 'nth-of-type-odd' : '';
											$completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed';
											$current_topic_class = ($topic->ID == $post->ID) ? 'learndash-current-menu-item' : '';
											?>
											<li class="<?php echo $current_topic_class ?>">
												<span class="topic_item"><a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>' title='<?php echo esc_attr( $topic->post_title ); ?>'><span><?php echo $topic->post_title; ?></span></a></span>
											<?php 
												if ( ( isset( $widget_instance['show_topic_quizzes'] ) ) && ( $widget_instance['show_topic_quizzes'] == true ) ) {
											
													$topic_quiz_list = learndash_get_lesson_quiz_list( $topic->ID, get_current_user_id() ); 
													if ( !empty( $topic_quiz_list ) ) {
														?><ul id="learndash-lesson-quiz-list-<?php echo $topic->ID ?>" class="learndash-topic-quiz-list"><?php
														foreach ( $topic_quiz_list as $quiz ) { 
															$quiz_completed = learndash_is_quiz_complete( get_current_user_id(), $quiz['post']->ID );
															$completed_class = empty( $quiz_completed ) ? 'topic-notcompleted' : 'topic-completed';
															$current_topic_class = ($quiz['post']->ID == $post->ID) ? 'learndash-current-menu-item' : '';
														
															if ( !empty( $current_topic_class ) ) {
																$lesson_topic_child_item_active = true;
															}
														
															?>
															<li class="quiz-item <?php echo $current_topic_class ?>">
																<span class="topic_item"><a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $quiz['post']->ID ) ); ?>' title='<?php echo esc_attr( $quiz['post']->post_title ); ?>'><span><?php echo $quiz['post']->post_title; ?></span></a></span>
															</li>
															<?php 
														} 
														?></ul><?php
													}
												}
											?>
											</li>
											<?php
										}
										
										if ( ( isset( $widget_instance['show_lesson_quizzes'] ) ) && ( $widget_instance['show_lesson_quizzes'] == true ) ) {
										
											$lesson_quiz_list = learndash_get_lesson_quiz_list( $course_lesson['post']->ID, get_current_user_id() ); 
											if ( !empty( $lesson_quiz_list ) ) {
												if ( !empty( $lesson_quiz_list ) ) {
													foreach ( $lesson_quiz_list as $quiz ) { 
														$quiz_completed = learndash_is_quiz_complete( get_current_user_id(), $quiz['post']->ID );
														$completed_class = empty( $quiz_completed ) ? 'topic-notcompleted' : 'topic-completed';
														$current_topic_class = ($quiz['post']->ID == $post->ID) ? 'learndash-current-menu-item' : '';
														if ( !empty( $current_topic_class ) ) {
															$lesson_topic_child_item_active = true;
														}
														?>
														<li class="quiz-item <?php echo $current_topic_class ?>">
															<span class="topic_item"><a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $quiz['post']->ID ) ); ?>' title='<?php echo esc_attr( $quiz['post']->post_title ); ?>'><span><?php echo $quiz['post']->post_title; ?></span></a></span>
														</li>
														<?php 
													} 
												}
											}
										}
									?>
								</ul>
							</div>
						<?php } else {
							if ( !empty( $lesson_quiz_list ) ) {
								?>
								<div id='learndash_topic_dots-<?php echo esc_attr( $course_lesson['post']->ID ); ?>' class="flip learndash_topic_widget_list"  style='<?php echo ( strpos( $list_arrow_class, 'collapse' ) !== false ) ? 'display:none' : '' ?>'>
									<ul>								
									<?php
										if ( !empty( $lesson_quiz_list ) ) {
											foreach ( $lesson_quiz_list as $quiz ) { 
												$quiz_completed = learndash_is_quiz_complete( get_current_user_id(), $quiz['post']->ID );
												$completed_class = empty( $quiz_completed ) ? 'topic-notcompleted' : 'topic-completed';
												$current_topic_class = ($quiz['post']->ID == $post->ID) ? 'learndash-current-menu-item' : '';

												if ( !empty( $current_topic_class ) ) {
													$lesson_topic_child_item_active = true;
												}
												?>
												<li class="quiz-item <?php echo $current_topic_class ?>">
													<span class="topic_item"><a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $quiz['post']->ID ) ); ?>' title='<?php echo esc_attr( $quiz['post']->post_title ); ?>'><span><?php echo $quiz['post']->post_title; ?></span></a></span>
												</li>
												<?php 
											} 
										}
									?>
									</ul>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<?php
					if ($lesson_topic_child_item_active) {
						?>
						<script type="text/javascript">
							jQuery(document).ready(function() {

								if ( jQuery('#course_navigation #lesson_list-<?php echo $course_lesson['post']->ID ?>').hasClass('inactive') ) {
									jQuery('#course_navigation #lesson_list-<?php echo $course_lesson['post']->ID ?>').removeClass('inactive')
									jQuery('#course_navigation #lesson_list-<?php echo $course_lesson['post']->ID ?>').addClass('active');
									jQuery('#course_navigation #lesson_list-<?php echo $course_lesson['post']->ID ?>').addClass('learndash-current-menu-ancestor');
									flip_expand_collapse("#lesson_list", <?php echo $course_lesson['post']->ID ?>);
								}
							});
						</script>
						<?php
					}
				?>


			<?php } ?>

		<?php } ?>
	</div> <!-- Closing <div class='learndash_navigation_lesson_topics_list'> -->
		<?php
			if ( ( isset( $widget_instance['show_course_quizzes'] ) ) && ( $widget_instance['show_course_quizzes'] == true ) ) {
		
				$course_quiz_list = learndash_get_course_quiz_list( $course_id, get_current_user_id() ); 
				if ( !empty( $course_quiz_list ) ) {
					?><ul id="learndash-course-quiz-list-<?php echo $course_id ?>" class="learndash-course-quiz-list learndash_navigation_lesson_topics_list"><?php
					foreach ( $course_quiz_list as $quiz ) { 
						$quiz_completed = learndash_is_quiz_complete( get_current_user_id(), $quiz['post']->ID );
						$completed_class = empty( $quiz_completed ) ? 'topic-notcompleted' : 'topic-completed';
						$current_topic_class = ($quiz['post']->ID == $post->ID) ? 'learndash-current-menu-item' : '';
						?>
						<li class="quiz-item <?php echo $current_topic_class ?>">
							<span class="topic_item"><a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $quiz['post']->ID ) ); ?>' title='<?php echo esc_attr( $quiz['post']->post_title ); ?>'><span><?php echo $quiz['post']->post_title; ?></span></a></span>
						</li>
						<?php 
					} 
					?></ul><?php
				}
			}
		?>

	<?php if ( $post->ID != $course->ID ) { ?> 
		<div class="widget_course_return">
			<?php _e( 'Return to', 'learndash' ); ?>
            <a href='<?php echo esc_attr( get_permalink( $course_id ) ); ?>'><?php echo $course->post_title; ?></a>
		</div>
	<?php } ?>

</div> <!-- Closing <div id='course_navigation'> -->
