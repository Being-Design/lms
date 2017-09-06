<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Boss
 * @since Boss 1.0.0
 */
get_header();
?>

<div class="page-right-sidebar">

	<div id="primary" class="site-content">

		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<section class="course-header">

						<div class="table top">
							<?php $img			 = get_the_post_thumbnail( $post->ID, 'course-single-thumb', array( 'class' => 'thumbnail alignleft' ) ); ?>
							<div class="table-cell <?php echo (esc_html( $img )) ? 'image' : ''; ?>">
								<?php echo $img; ?>
							</div>

							<div class="table-cell content">
								<span><?php echo LearnDash_Custom_Label::get_label( 'course' ) ?></span>
								<header><h1><?php the_title(); ?></h1></header>
								<?php echo '<p class="course-excerpt">' . $post->post_excerpt . '</p>'; ?>
							</div>
						</div>

						<div class="table bottom">
							<div class="table-cell categories">
								<?php
								// Get Course Categories
								$category_list	 = get_the_category( $post->ID );

								if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {
									$course_category_list = wp_get_post_terms( $post->ID, 'ld_course_category' );
								}

								if ( !empty( $category_list ) || ! empty( $course_category_list ) ) {
									?>
									<ul class="post-categories"><?php
										foreach ( $category_list as $category ) {
											if ( trim( $category->name ) != "Uncategorized" ) {
												?>
												<li><a rel="category tag" href="<?php echo home_url() . '/category/' . $category->slug . '/?post_type=sfwd-courses'; ?>"><?php echo $category->name; ?></a></li><?php
											}
										}

										foreach ( $course_category_list as $category ) {
											if ( trim( $category->name ) != "Uncategorized" ) {
												?>
												<li><a rel="category tag" href="<?php echo get_term_link( $category ); ?>"><?php echo $category->name; ?></a></li><?php
											}
										}
										?>
									</ul><?php }
									?>
							</div>

							<div class="table-cell progress">
								<?php echo do_shortcode( '[learndash_course_progress]' ); ?>
							</div>
						</div>

					</section>

					<div id="course-video">
						<a href="#" id="hide-video" class="button"><i class="fa fa-close"></i></a>
						<?php
						/**
						 * sensei_course_meta_video hook
						 *
						 * @hooked sensei_course_meta_video - 10 (outputs the video for course)
						 */
						$course_video_embed = get_post_meta( $post->ID, '_boss_edu_post_video', true );

						if ( 'http' == substr( $course_video_embed, 0, 4 ) ) {
							// V2 - make width and height a setting for video embed
							$course_video_embed = wp_oembed_get( esc_url( $course_video_embed )/* , array( 'width' => 100 , 'height' => 100) */ );
						}

						if ( '' != $course_video_embed ) {
							?><div class="course-video"><?php echo html_entity_decode( $course_video_embed ); ?></div><?php
							}
							?>
					</div>

					<section id="course-details">
						<span class="course-statistic">
							<?php
							$course_id		 = $post->ID;
							$total_lessons	 = learndash_get_course_lessons_list( $course_id );
							$total_lessons	 = is_array( $total_lessons ) ? count( $total_lessons ) : 0;
							printf( _n( '%s ' . LearnDash_Custom_Label::get_label( 'lesson' ), '%s ' . LearnDash_Custom_Label::get_label( 'lessons' ), $total_lessons, 'boss-learndash' ), $total_lessons );
							?>
						</span>
						<div class="course-buttons">
							<?php
							if ( $course_video_embed ) {
								?>
								<a href="#" id="show-video" class="button"><i class="fa fa-play"></i><?php apply_filters( 'boss_edu_show_video_text', _e( 'Watch Introduction', 'boss-learndash' ) ) ?></a>
								<?php
							}

							$user_id = get_current_user_id();

							if ( function_exists( 'learndash_get_course_certificate_link' ) ):
								$course_certficate_link = learndash_get_course_certificate_link( $course_id, $user_id );

								if ( !empty( $course_certficate_link ) ) :
									?>
									<a href='<?php echo esc_attr( $course_certficate_link ); ?>' id="learndash_course_certificate" class="btn-blue" target="_blank"><?php _e( 'PRINT YOUR CERTIFICATE!', 'boss-learndash' ); ?></a><?php
								endif;

							endif;

							$logged_in = !empty( $user_id );

							if ( $logged_in ) {
								?>
								<span id='learndash_course_status'>
									<?php
									$course_status = learndash_course_status( $course_id, null );

									if ( trim( $course_status ) != 'Not Started' && trim( $course_status ) != 'Completed' ) {
										echo '<i class="fa fa-spinner"></i>';
									}

									echo $course_status;
									?>
								</span>
								<?php
							}

							$has_access = sfwd_lms_has_access( $course_id, $user_id );

							if ( !$has_access ) {
								echo boss_edu_payment_buttons( $post );
							}
							?>
						</div>

					</section>

					<?php
					$group_attached = get_post_meta( $course_id, 'bp_course_group', true );

					if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
						$group_data = groups_get_group( array( 'group_id' => $group_attached ) );
					}

					if ( !empty( $group_attached ) && $group_attached != '-1' && !empty( $group_data->id ) ) {

						$group_slug = trailingslashit( home_url() ) . bp_get_root_slug( 'groups' ) . '/' . $group_data->slug;

						$group_query = array(
							'count_total'		 => '', // Prevents total count
							'populate_extras'	 => false,
							'type'				 => 'alphabetical',
							'group_id'			 => absint( $group_attached ),
							'group_role'		 => array( 'admin', 'member', 'mod' )
						);

						$group_users = new BP_Group_Member_Query( $group_query );
						$forum_id	 = groups_get_groupmeta( $group_attached, 'forum_id' );

						//Send invite tab slug
						if ( defined( 'BP_INVITE_ANYONE_SLUG' ) ) {
							$send_invite_slug = BP_INVITE_ANYONE_SLUG;
						} else {
							$send_invite_slug = 'send-invites';
						}
						?>
						<div id="buddypress">
							<div id="item-nav" class="course-group-nav">
								<div role="navigation" id="object-nav" class="item-list-tabs no-ajax">
									<ul>
										<li id="home-groups-li"><a href="<?php echo $group_slug . '/'; ?>" id="home"><?php _e( 'Home', 'boss-learndash' ); ?></a></li>
										<?php if ( function_exists( 'bbpress' ) && !empty( $group_data->enable_forum ) ): ?>
											<li id="nav-forum-groups-li"><a href="<?php echo $group_slug . '/forum/'; ?>" id="nav-forum"><?php _e( 'Forum', 'boss-learndash' ); ?></a></li>
										<?php endif; ?>
										<li class="current selected" id="home-groups-li"><a href="" id="home"><?php echo LearnDash_Custom_Label::get_label( 'course' ) ?></a></li>
										<li id="members-groups-li"><a href="<?php echo $group_slug . '/members/'; ?>" id="members"><?php _e( 'Members', 'boss-learndash' ); ?><span><?php echo $group_users->total_users; ?></span></a></li>
										<li id="invite-groups-li"><a href="<?php echo $group_slug . '/' . $send_invite_slug . '/'; ?>" id="invite"><?php _e( 'Send Invites', 'boss-learndash' ); ?></a></li>
										<?php if ( groups_is_user_admin( get_current_user_id(), absint( $group_attached ) ) ): ?>
											<li id="admin-groups-li"><a href="<?php echo $group_slug . '/admin/edit-details/'; ?>" id="admin"><?php _e( 'Manage', 'boss-learndash' ); ?></a></li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</div><?php
					}
					?>

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'boss-learndash' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'boss-learndash' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->

				</article><!-- #post -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop.     ?>

		</div><!-- #content -->

	</div><!-- #primary -->

	<?php
	global $boss_learndash;
	$boss_learndash->boss_edu_load_template( 'sidebar-learndash-course' );
	?>

</div>

<?php get_footer(); ?>