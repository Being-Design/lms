<?php
/**
 * Displays the course progress widget.
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */
?>

<div class="progress-wrap">
    <span class="course-completion-rate"><?php echo ($message) ? $message : __( '0 steps completed', 'boss-learndash' ); ?></span><span class="percent"><?php echo $percentage; ?>%</span>

    <dd class="course_progress" title="<?php echo sprintf( __( '%s out of %s steps completed', 'boss-learndash' ), $completed, $total ); ?>">
		<div class="course_progress_blue" style="width: <?php echo esc_attr( $percentage ); ?>%;"></div>
    </dd>
</div>