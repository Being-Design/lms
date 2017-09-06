<?php
/**
 * This file contains the code that displays the course list.
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */
?>

<?php
global $post, $bp;
$post_id			 = $post->ID;
$post_title			 = $post->post_title;
$user_info			 = get_userdata( absint( $post->post_author ) );
$author_link		 = !$bp ? get_author_posts_url( absint( $post->post_author ) ) : bp_core_get_user_domain( absint( $post->post_author ) );
$author_avatar		 = get_avatar( $post->post_author, 75 );
$author_display_name = $user_info->display_name;
$author_id			 = $post->post_author;
$course_lessons_list = learndash_get_course_lessons_list( $post_id );
$total_lessons		 = ( is_array( $course_lessons_list ) ) ? count( $course_lessons_list ) : 0;
$options			 = get_option( 'sfwd_cpt_options' );
$currency			 = null;

if ( !is_null( $options ) ) {
	if ( isset( $options[ 'modules' ] ) && isset( $options[ 'modules' ][ 'sfwd-courses_options' ] ) && isset( $options[ 'modules' ][ 'sfwd-courses_options' ][ 'sfwd-courses_paypal_currency' ] ) )
		$currency = $options[ 'modules' ][ 'sfwd-courses_options' ][ 'sfwd-courses_paypal_currency' ];
}

$enable_video = get_post_meta( $post_id, '_learndash_course_grid_enable_video_preview', true );
$embed_code   = get_post_meta( $post_id, '_learndash_course_grid_video_embed_code', true );
$button_text  = get_post_meta( $post_id, '_learndash_course_grid_custom_button_text', true );

$button_text = isset( $button_text ) && ! empty( $button_text ) ? $button_text : __( 'See more...', 'boss-learndash' );
$button_text = apply_filters( 'learndash_course_grid_custom_button_text', $button_text, $post_id );

$options = get_option('sfwd_cpt_options');
$currency = null;

if ( ! is_null( $options ) ) {
	if ( isset($options['modules'] ) && isset( $options['modules']['sfwd-courses_options'] ) && isset( $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'] ) )
		$currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];
}

if ( is_null( $currency ) ) {
	$currency = 'USD';
}

$course_options 	= get_post_meta($post_id, "_sfwd-courses", true);
$price 				= $course_options && isset($course_options['sfwd-courses_course_price']) ? $course_options['sfwd-courses_course_price'] : __( 'Free', 'boss-learndash' );
$short_description 	= @$course_options['sfwd-courses_course_short_description'];

$has_access   = sfwd_lms_has_access( $post_id, get_current_user_id() );
$is_completed = learndash_course_completed( get_current_user_id(), $post_id );

if( $price == '' )
	$price .= __( 'Free', 'boss-learndash' );

if ( is_numeric( $price ) ) {
	if ( $currency == "USD" )
		$price = '$' . $price;
	else
		$price .= ' ' . $currency;
}

$class       = '';
$ribbon_text = '';

if ( $has_access && ! $is_completed ) {
	$class = 'ld_course_grid_price ribbon-enrolled';
	$ribbon_text = __( 'Enrolled', 'boss-learndash' );
} elseif ( $has_access && $is_completed ) {
	$class = 'ld_course_grid_price';
	$ribbon_text = __( 'Completed', 'boss-learndash' );
} else {
	$class = ! empty( $course_options['sfwd-courses_course_price'] ) ? 'ld_course_grid_price price_' . $currency : 'ld_course_grid_price free';
	$ribbon_text = $price;
}
?>

<div class="<?php echo esc_attr( join( ' ', get_post_class( array( 'course', 'post' ), $post_id ) ) ); ?>">

    <div class="course-inner">

		<?php if ( get_post_type( $post_id ) == 'sfwd-courses' && !isset( $course_options[ 'sfwd-courses_boss_hide_price_tag' ] ) ): ?>
			<div class="price <?php echo esc_attr( $class ); ?>">
				<?php echo esc_attr( $ribbon_text ); ?>
			</div>
		<?php endif; ?>

        <div class="course-image">
			<div class="course-overlay">
                <a href="<?php echo get_permalink( $post_id ); ?>" title="<?php echo esc_attr( $post_title ); ?>" class="bb-course-link">
                    <span class="play"><i class="fa fa-play"></i></span>
                </a>
				<a href="<?php echo $author_link; ?>" title="<?php echo esc_attr( $author_display_name ); ?>">
					<?php echo $author_avatar; ?>
                </a>
            </div>

			<?php
//			if ( has_post_thumbnail( $post_id ) ) {
//				// Get Featured Image
//				$img = get_the_post_thumbnail( $post_id, 'course-archive-thumb', array( 'class' => 'woo-image thumbnail alignleft' ) );
//			} else {
//				$img = '<img src="http://placehold.it/360x250&text=' . LearnDash_Custom_Label::get_label( 'course' ) . '" alt="' . esc_attr( $post_title ) . '" />';
//			}
//
//			if ( !$img ) {
//				$img = '<img src="http://placehold.it/360x250&text=' . LearnDash_Custom_Label::get_label( 'course' ) . '" alt="' . esc_attr( $post_title ) . '" />';
//			}
//
//			echo '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( $post_title ) . '" class="course-cover-image">' . $img . '</a>';
			?>

			<?php if ( 1 == $enable_video && ! empty( $embed_code ) ) : ?>
				<div class="ld_course_grid_video_embed">
					<?php echo $embed_code; ?>
				</div>
			<?php elseif( has_post_thumbnail( $post_id ) ) :?>
				<a href="<?php the_permalink( $post_id ); ?>" class="course-cover-image">
					<?php echo get_the_post_thumbnail( $post_id, 'course-archive-thumb', array( 'class' => 'woo-image thumbnail alignleft' ) ); ?>
				</a>
			<?php else :?>
				<a href="<?php the_permalink(); ?>" class="course-cover-image">
					<img alt="" src="http://placehold.it/360x250&text=<?php echo LearnDash_Custom_Label::get_label( 'course' ) ?>"/>
				</a>
			<?php endif;?>
        </div>



        <section class="entry">
            <div class="course-flexible-area">
                <header>
                    <h2><a href="<?php echo get_permalink( $post_id ); ?>" title="<?php echo esc_attr( $post_title ); ?>"><?php echo mb_strimwidth( $post_title, 0, 80, '...' ); ?></a></h2>
                </header>

                <p class="sensei-course-meta">
					<span class="course-author"><?php _e( 'by ', 'boss-learndash' ); ?><a href="<?php echo $author_link; ?>" title="<?php echo esc_attr( $author_display_name ); ?>"><?php echo esc_html( $author_display_name ); ?></a></span>
                </p>
            </div>

			<div class="caption">
				<?php if(!empty($short_description)) { ?>
					<p class="entry-content"><?php echo htmlspecialchars_decode( do_shortcode( $short_description ) ); ?></p>
				<?php  } ?>
			</div>

            <div class="sensei-course-meta">
				<p class="ld_course_grid_button"><a class="button" role="button" href="<?php the_permalink( $post_id ); ?>" rel="bookmark"><?php echo esc_attr( $button_text ); ?></a></p>
				<span class="course-lesson-count"><?php echo $total_lessons . '&nbsp;' . apply_filters( 'learndash_lessons_text', LearnDash_Custom_Label::get_label( 'lessons' ) ); ?></span>
            </div>

			<?php if ( isset( $shortcode_atts['progress_bar'] ) && $shortcode_atts['progress_bar'] == 'true' ) : ?>
				<p><?php echo do_shortcode( '[learndash_course_progress course_id="' . $post_id . '" user_id="' . get_current_user_id() . '"]' ); ?></p>
			<?php endif; ?>

        </section>



    </div>

</div>