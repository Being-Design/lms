<?php
/**
 * The sidebar containing the widget area for WordPress blog posts and pages.
 *
 * @package WordPress
 * @subpackage Boss
 * @since Boss 1.0.0
 */
?>
	
<!-- default WordPress sidebar -->
<div id="secondary" class="widget-area" role="complementary">
    <?php the_widget( 'Boss_LearnDash_Course_Teacher_Widget', 'title=' ); ?>
    <?php //the_widget( 'Boss_LearnDash_Course_Participants_Widget', array( 'title' => 'Course Participants' , 'limit' => '5', 'size' => '50', 'display' => 'list' ) ); ?>
    <?php dynamic_sidebar( 'learndash-course' ); ?>
</div><!-- #secondary -->
