<?php
/**
 * Displays a user's profile.
 *
 * Available Variables:
 *
 * $user_id 		: Current User ID
 * $current_user 	: (object) Currently logged in user object
 * $user_courses 	: Array of course ID's of the current user
 * $quiz_attempts 	: Array of quiz attempts of the current user
 *
 * @since 2.1.0
 *
 * @package LearnDash\User
 */
?>
<?php
	$filepath = locate_template(
		array(
			'learndash/learndash_template_script.min.js',
			'learndash/learndash_template_script.js',
			'learndash_template_script.min.js',
			'learndash_template_script.js'
		)
	);

	if ( !empty( $filepath ) ) {
		wp_enqueue_script( 'learndash_template_script_js', str_replace( ABSPATH, '/', $filepath ), array( 'jquery' ), LEARNDASH_VERSION, true );
		$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;
	} else if ( file_exists( LEARNDASH_LMS_PLUGIN_DIR .'/templates/learndash_template_script'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js' ) ) {
		wp_enqueue_script( 'learndash_template_script_js', LEARNDASH_LMS_PLUGIN_URL . 'templates/learndash_template_script'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', array( 'jquery' ), LEARNDASH_VERSION, true );
		$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;

		$data = array();
		$data['ajaxurl'] = admin_url('admin-ajax.php');
		$data = array( 'json' => json_encode( $data ) );
		wp_localize_script( 'learndash_template_script_js', 'sfwd_data', $data );

	}
    
	//LD_QuizPro::showModalWindow();
    add_action( 'wp_footer', array( 'LD_QuizPro', 'showModalWindow' ), 20 );
?>
<style type="text/css">
    #wpProQuiz_user_content table{
        display: table;
        width: 100%;
    }
    #wpProQuiz_user_content table th,
    #wpProQuiz_user_content table td{
        padding: 5px;
        box-sizing: border-box;
    }
</style>
<div id="learndash_profile">

    <div class="expand_collapse">
		<a href="#" onClick="return flip_expand_all( '#course_list' );"><?php _e( 'Expand All', 'boss-learndash' ); ?></a>
		<span class="sep"><?php _e( '/', 'boss-learndash' ); ?></span>
		<a href="#" onClick="return flip_collapse_all( '#course_list' );"><?php _e( 'Collapse All', 'boss-learndash' ); ?></a>
	</div>

	<div class="learndash_profile_heading">
        <span class="title"><?php _e( 'Profile', 'boss-learndash' ); ?></span>
		<?php echo '<div class="profile_edit_profile"><a href="' . get_edit_user_link() . '">' . __( 'Edit profile', 'boss-learndash' ) . '</a></div>'; ?>
	</div>

	<div class="profile_info clear_both">
		<div class="profile_avatar">
			<?php echo get_avatar( $current_user->user_email, 180 ); ?>
		</div>

		<div class="learndash_profile_details">
			<?php if ( (!empty( $current_user->user_lastname )) || (!empty( $current_user->user_firstname )) ) { ?>
				<div><b><?php _e( 'Name', 'boss-learndash' ); ?>:</b> <?php echo $current_user->user_firstname . ' ' . $current_user->user_lastname; ?></div>
				<?php
			}
			?>
			<div><b><?php _e( 'Username', 'boss-learndash' ); ?>:</b> <?php echo $current_user->user_login; ?></div>
			<div><b><?php _e( 'Email', 'boss-learndash' ); ?>:</b> <?php echo $current_user->user_email; ?></div>

			<?php if ( ( isset( $shortcode_atts['course_points_user'] ) ) && ( $shortcode_atts['course_points_user'] == true ) ) { ?>
				<?php echo do_shortcode('[ld_user_course_points user_id="'. $current_user->ID .'" context="ld_profile"]'); ?>
			<?php } ?>
		</div>
	</div>

	<div class="learndash_profile_heading no_radius clear_both">
		<span class="title"><?php printf( __( 'Registered %s', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ); ?></span>
		<span class="ld_profile_status"><?php _e( 'Status', 'boss-learndash' ); ?></span>
	</div>

	<div id="course_list-wrap">

        <div id="course_list">

			<?php if ( !empty( $user_courses ) ) : ?>

				<?php foreach ( $user_courses as $course_id ) { ?>

					<?php
					$course		 = get_post( $course_id );
					$course_link = get_permalink( $course_id );
					$progress	 = learndash_course_progress( array(
						'user_id'	 => $user_id,
						'course_id'	 => $course_id,
						'array'		 => true
					) );

					$status = ( $progress[ 'percentage' ] == 100 ) ? 'completed' : 'notcompleted';
					?>

					<div id='course-<?php echo esc_attr( $user_id ) . '-' . esc_attr( $course->ID ); ?>'>

						<div class="list_arrow collapse flippable" onClick='return flip_expand_collapse( "#course-<?php echo esc_attr( $user_id ); ?>", <?php echo esc_attr( $course->ID ); ?> );'></div>

						<?php
						/**
						 * @todo Remove h4 container.
						 */
						?>
					<h4>
						<a class="<?php echo $status; ?>" href="<?php echo $course_link; ?>"><?php echo $course->post_title; ?></a>

						<div class="learndash-course-certificate"><?php
							$certificateLink = learndash_get_course_certificate_link( $course->ID, $user_id );
							if ( !empty( $certificateLink ) ) {
								?><a target="_blank" href="<?php echo esc_attr( $certificateLink ); ?>"><div class="<?php echo ( wp_is_mobile() ) ? 'certificate_icon_small' : 'certificate_icon_large' ?>"></div></a><?php
							}
							?></div>

						<div class="flip" style="display:none;">
							<div class="learndash_profile_heading course_overview_heading"><?php printf( __("%s Progress Overview", "boss-learndash"), LearnDash_Custom_Label::get_label( 'course' ) ); ?></div>
							<div class="overview table">
								<div class="table-cell">
									<dd class="course_progress" title="<?php echo sprintf(__("%s out of %s steps completed", 'boss-learndash'),$progress["completed"], $progress["total"]); ?>">
										<div class="course_progress_blue" style="width: <?php echo $progress["percentage"]; ?>%;">
									</dd>
								</div>
								<div class="table-cell">
									<div class="right">
										<?php echo sprintf(__("%s%% Complete", 'boss-learndash'), $progress["percentage"]); ?>
									</div>
								</div>
							</div>
							<?php if(!empty($quiz_attempts[$course_id])) { ?>
								<div class="learndash_profile_quizzes clear_both">
									<div class="learndash_profile_quiz_heading">
										<div class="quiz_title"><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ) ?></div>
										<div class="certificate"><?php _e("Certificate", "boss-learndash"); ?></div>
										<div class="scores"><?php _e("Score", "boss-learndash"); ?></div>
										<div class="statistics"><?php _e( 'Statistics', 'boss-learndash' ); ?></div>
										<div class="quiz_date"><?php _e("Date", "boss-learndash"); ?></div>
									</div>
									<?php
									foreach( $quiz_attempts[$course_id] as $k => $quiz_attempt ) {
										$certificateLink = null;

										if ( (isset( $quiz_attempt['has_graded'] ) ) && ( true === $quiz_attempt['has_graded'] ) && (true === LD_QuizPro::quiz_attempt_has_ungraded_question( $quiz_attempt )) ) {
											$status = 'pending';
										} else {
											$certificateLink = @$quiz_attempt['certificate']['certificateLink'];
											$status = empty( $quiz_attempt['pass'] ) ? 'failed' : 'passed';
										}

										$quiz_title = !empty($quiz_attempt["post"]->post_title)? $quiz_attempt["post"]->post_title:@$quiz_attempt['quiz_title'];
										$quiz_link = !empty($quiz_attempt["post"]->ID)? get_permalink($quiz_attempt["post"]->ID):"#";
										if(!empty($quiz_title)) {
											?>
											<div  class="<?php echo $status; ?>">
												<div class="quiz_title"><span class="<?php echo $status; ?>_icon"></span><a href="<?php echo $quiz_link; ?>"><?php echo $quiz_title; ?></a></div>
												<div class="certificate"><?php if(!empty($certificateLink)) {?> <a href="<?php echo $certificateLink; ?>&time=<?php echo $quiz_attempt['time'] ?>" target="_blank"><div class="certificate_icon_small"></div></a><?php } else{ echo '-';	}?></div>

												<div class="scores">
													<?php if ( (isset( $quiz_attempt['has_graded'] ) ) && (true === $quiz_attempt['has_graded']) && (true === LD_QuizPro::quiz_attempt_has_ungraded_question( $quiz_attempt )) ) : ?>
														<?php echo _x('Pending', 'Pending Certificate Status Label', 'boss-learndash'); ?>
													<?php else : ?>
														<?php echo round( $quiz_attempt['percentage'], 2 ); ?>%
													<?php endif; ?>
												</div>
												<div class="statistics">
													<?php
													if ( get_post_meta($quiz_attempt['post']->ID, '_viewProfileStatistics', true) && ( isset( $quiz_attempt['statistic_ref_id'] ) ) && ( !empty( $quiz_attempt['statistic_ref_id'] ) ) ) {
														?><a class="user_statistic" data-statistic_nonce="<?php echo wp_create_nonce( 'statistic_nonce_'. $quiz_attempt['statistic_ref_id'] .'_'. get_current_user_id() . '_'. $user_id ); ?>" data-user_id="<?php echo $user_id ?>" data-quiz_id="<?php echo $quiz_attempt['pro_quizid'] ?>" data-ref_id="<?php echo intval( $quiz_attempt['statistic_ref_id'] ) ?>" href="#"><div class="statistic_icon"></div></a><?php
													}
													?>
												</div>
												<div class="quiz_date"><?php echo date_i18n( "d-M-Y", $quiz_attempt['time'] ) ?></div>
											</div>
										<?php }
									} ?>
								</div>
							<?php } ?>
						</div>
					</h4>

					</div><?php
				}

			endif;
			?>

		</div>

	</div>

</div>