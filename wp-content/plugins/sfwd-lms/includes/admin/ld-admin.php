<?php
/**
 * Functions for wp-admin
 *
 * @since 2.1.0
 *
 * @package LearnDash\Admin
 */


/**
 * Output for admin head
 *
 * Adds learndash icon next to the LearnDash LMS menu item
 *
 * @since 2.1.0
 */
function learndash_admin_head() {
	?>
		<style> #adminmenu #toplevel_page_learndash-lms div.wp-menu-image:before { content: "\f472"; } </style>
	<?php
}

add_action( 'admin_head', 'learndash_admin_head' );



/**
 * Hide top level menu when there are no submenus
 *
 * @since 2.1.0
 */
function learndash_hide_menu_when_not_required() {
	?>
		<script>
		jQuery(window).ready(function() {
		if(jQuery(".toplevel_page_learndash-lms").length && jQuery(".toplevel_page_learndash-lms").find("li").length <= 1)
			jQuery(".toplevel_page_learndash-lms").hide();
		});
		</script>
	<?php
}

add_filter( 'admin_footer', 'learndash_hide_menu_when_not_required', 99 );



/**
 * Scripts/styles for admin
 *
 * @since 2.1.0
 */
function learndash_load_admin_resources() {
	global $pagenow, $post, $typenow;
	global $learndash_post_types, $learndash_pages;;
	global $learndash_assets_loaded;

	if ( in_array( @$_GET['page'], $learndash_pages ) || in_array( @$_GET['post_type'], $learndash_post_types ) || $pagenow == 'post.php' && in_array( $post->post_type, $learndash_post_types ) ) {
		wp_enqueue_style( 
			'learndash_style', 
			LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(), 
			LEARNDASH_VERSION 
		);
		$learndash_assets_loaded['styles']['learndash_style'] = __FUNCTION__;

		wp_enqueue_style( 
			'sfwd-module-style', 
			LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(), 
			LEARNDASH_VERSION 
		);
		$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;	
		
		if ( ( $pagenow == 'edit.php' ) && ( ( $typenow == 'sfwd-essays' ) || ( $typenow == 'sfwd-assignment' ) ) ) {
			wp_enqueue_script( 
				'sfwd-module-script', 
				LEARNDASH_LMS_PLUGIN_URL . '/assets/js/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_VERSION,
				true 
			);
			$learndash_assets_loaded['scripts']['sfwd-module-script'] = __FUNCTION__;	
			wp_localize_script( 'sfwd-module-script', 'sfwd_data', array() );
		}
	}

	if ( $pagenow == 'post.php' && $post->post_type == 'sfwd-quiz' || $pagenow == 'post-new.php' && @$_GET['post_type'] == 'sfwd-quiz' ) {
		wp_enqueue_script( 
			'wpProQuiz_admin_javascript', 
			plugins_url('js/wpProQuiz_admin'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', WPPROQUIZ_FILE),
			array( 'jquery' ),
			LEARNDASH_VERSION,
			true
		);
		$learndash_assets_loaded['scripts']['wpProQuiz_admin_javascript'] = __FUNCTION__;
	}

	if ( $pagenow == 'post-new.php' && @$_GET['post_type'] == 'sfwd-lessons' || $pagenow == 'post.php' && @get_post( @$_GET['post'] )->post_type == 'sfwd-lessons' ) {
		wp_enqueue_style( 
			'ld-datepicker-ui-css', 
			LEARNDASH_LMS_PLUGIN_URL . 'assets/css/jquery-ui'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(), 
			LEARNDASH_VERSION 
		);
		$learndash_assets_loaded['styles']['ld-datepicker-ui-css'] = __FUNCTION__;
	}
	
	if ( ($pagenow == 'admin.php') && (@$_GET['page'] == 'ldAdvQuiz') && (@$_GET['module'] == 'statistics') )  {
		wp_enqueue_style( 
			'ld-datepicker-ui-css', 
			LEARNDASH_LMS_PLUGIN_URL . 'assets/css/jquery-ui'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
			array(), 
			LEARNDASH_VERSION 
		);
		$learndash_assets_loaded['styles']['ld-datepicker-ui-css'] = __FUNCTION__;
	}
}

add_action( 'admin_enqueue_scripts', 'learndash_load_admin_resources' );




/**
 * Change label in admin bar on single topic to 'Edit Topic'
 *
 * @todo  consider for deprecation, action is commented
 *
 * @since 2.1.0
 */
function learndash_admin_bar_link() {
	global $wp_admin_bar;
	global $post;

	if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
		return;
	}

	if ( is_single() && $post->post_type == 'sfwd-topic' ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'edit_fixed',
			'parent' => false,
			'title' => sprintf( _x( 'Edit %s', 'Edit Topic Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
			'href' => get_edit_post_link( $post->id )
		) );
	}
}



/**
 * Output Reports Page
 *
 * @since 2.1.0
 */
function learndash_lms_reports_page() {
	?>
		<div  id="learndash-reports"  class="wrap">
			<h1><?php _e( 'User Reports', 'learndash' ); ?></h1>
			<br>
			<div class="sfwd_settings_left">
				<div class=" " id="sfwd-learndash-reports_metabox">
					<div class="inside">
						<a class="button-primary" href="<?php echo admin_url( 'admin.php?page=learndash-lms-reports&action=sfp_update_module&nonce-sfwd='.wp_create_nonce( 'sfwd-nonce' ).'&page_options=sfp_home_description&courses_export_submit=Export' ); ?>"><?php printf( _x( 'Export User %s Data', 'Export User Course Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></a>
						<a class="button-primary" href="<?php echo admin_url( 'admin.php?page=learndash-lms-reports&action=sfp_update_module&nonce-sfwd='.wp_create_nonce( 'sfwd-nonce' ).'&page_options=sfp_home_description&quiz_export_submit=Export' ); ?>"><?php printf( _x( 'Export %s Data', 'Export Quiz Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></a>
						<?php
							/**
							 * Run actions after report page buttons print
							 *
							 * @since 2.1.0
							 */
							do_action( 'learndash_report_page_buttons' );
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
}



/**
 * Add Javascript to admin footer
 *
 * @since 2.1.0
 * @TODO We need to get rid of this JS logic and replace with filter to set the $parent_file
 * See:
 * https://developer.wordpress.org/reference/hooks/parent_file/
 * https://developer.wordpress.org/reference/hooks/submenu_file/
 */
function learndash_select_menu() {
	global $learndash_current_page_link;
	//error_log('learndash_current_page_link['. $learndash_current_page_link .']');
	
	global $parent_file, $submenu_file;
	//error_log('parent_file['. $parent_file .']');
	//error_log('submenu_file['. $submenu_file .']');
	?>
		<script type="text/javascript">
		jQuery(window).load( function( $) {
			jQuery("body").removeClass("sticky-menu");
			jQuery("#toplevel_page_learndash-lms, #toplevel_page_learndash-lms > a").removeClass('wp-not-current-submenu' );
			jQuery("#toplevel_page_learndash-lms").addClass('current wp-has-current-submenu wp-menu-open' );
			<?php if ( ! empty( $learndash_current_page_link ) ) : ?>
				jQuery("#toplevel_page_learndash-lms a[href='<?php echo $learndash_current_page_link;?>']").parent().addClass("current");
			<?php endif; ?>
		});
		</script>
	<?php
};



/**
 * Shortcode columns in admin for Quizes
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function add_shortcode_data_columns( $cols ) {
	return array_merge(
		array_slice( $cols, 0, 3 ),
		array( 'shortcode' => __( 'Shortcode', 'learndash' ) ),
		array_slice( $cols, 3 )
	);
}



/**
 * Assigned Course columns in admin for Lessons and Quizes
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function add_course_data_columns( $cols ) {
	return array_merge(
		array_slice( $cols, 0, 3 ),
		array( 
			'course' => sprintf( _x( 'Assigned %s', 'Assigned Course Label', 'learndash' ) , LearnDash_Custom_Label::get_label( 'course' ) ) 
		),
		array_slice( $cols, 3 )
	);
}



/**
 * Assigned Lesson & Assigned Course columns in admin for Topics and Assignments
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function add_lesson_data_columns( $cols ) {
	return array_merge(
		array_slice( $cols, 0, 3 ),
		array(
			'lesson' => sprintf( _x( 'Assigned %s', 'Assigned Lesson Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
			'course' => sprintf( _x( 'Assigned %s', 'Assigned Course Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
		),
		array_slice( $cols, 3 )
	);
}



/**
 * Status columns in admin for Assignments
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function add_assignment_data_columns( $cols ) {
	return array_merge(
		array_slice( $cols, 0, 3 ),
		array(
			'approval_status' => __( 'Status', 'learndash' ),
			'approval_points' => __( 'Points', 'learndash' ),
		),
		array_slice( $cols, 3 )
	);
}


/**
 * Remove tags column for quizzes
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function remove_tags_column( $cols ){
	unset( $cols['tags'] );
	return $cols;
}



/**
 * Remove categories column for quizzes
 *
 * @since 2.1.0
 *
 * @param array 	$cols 	admin columns for post type
 * @return array 	$cols 	admin columns for post type
 */
function remove_categories_column( $cols ){
	unset( $cols['categories'] );
	return $cols;
}



/**
 * Output approval status for assignment in admin column
 *
 * @since 2.1.0
 *
 * @param  string 	$column_name
 * @param  int 		$id
 */
function manage_asigned_assignment_columns( $column_name, $assignment_id ) {
	switch ( $column_name ) {
		case 'approval_status':
			
		//$current_screen = get_current_screen();
		//error_log('current_screen<pre>'. print_r($current_screen, true) .'</pre>');
			
			$approval_status_flag = learndash_is_assignment_approved_by_meta( $assignment_id );
			if ($approval_status_flag == 1) {
				$approval_status_label = __( 'Approved', 'learndash' );
			} else {
				$approval_status_label = __( 'Not Approved', 'learndash' );
			}
			$approval_status_url = admin_url( 'edit.php?post_type='.@$_GET['post_type'].'&approval_status='. $approval_status_flag );
			
			echo '<a " href="'. $approval_status_url .'">'. $approval_status_label .'</a>';
			
			break;
			
		case 'approval_points':
			if ( learndash_assignment_is_points_enabled( $assignment_id ) ) {
				$max_points = 0;
			
				$assignment_settings_id = intval( get_post_meta( $assignment_id, 'lesson_id', true ) );
				if (!empty( $assignment_settings_id ) ) {
					$max_points = learndash_get_setting( $assignment_settings_id, 'lesson_assignment_points_amount' );
				} 
			
				$current_points = get_post_meta( $assignment_id, 'points', true );
				if ( ( $current_points == 'Pending' ) || ( $current_points == '' ) ) {
					$approval_status_flag = learndash_is_assignment_approved_by_meta( $assignment_id );
					if ( $approval_status_flag != 1 ) {
						$current_points = '<input id="assignment_points_'. $assignment_id .'" class="small-text" type="number" value="0" max="'. $max_points .'" min="0" step="1" name="assignment_points['. $assignment_id .']" />';
					} else {
						$current_points = 0;
					}
				}
				echo sprintf( _x('%s / %s', 'placeholders: current points / maximum point for assignment', 'learndash'), $current_points, $max_points);
				
				$approval_status_flag = learndash_is_assignment_approved_by_meta( $assignment_id );
				if ($approval_status_flag != 1) {
					?> <button id="assignment_approve_<?php echo $assignment_id ?>" class="small assignment_approve_single"><?php _e('approve', 'learndash'); ?></button><?php
				}
			
			} else {
				_e('Not Enabled', 'learndash');
			}		
			break;
			
		default:	
			break;
	}
}



/**
 * Output values for Assigned Courses in admin columns
 * for lessons, quizzes, topics, assignments
 *
 * @since 2.1.0
 *
 * @param  string 	$column_name
 * @param  int 		$id
 */
function manage_asigned_course_columns( $column_name, $id ){
	switch ( $column_name ) {
		case 'shortcode':
		echo '<strong>[ld_quiz quiz_id="'. $id . '"]</strong>';
			$quiz_pro = learndash_get_setting( $id, 'quiz_pro', true );
			if ( ! empty( $quiz_pro) ) {
				echo '<br />or [LDAdvQuiz '.$quiz_pro.']';
			} 
			break;
		case 'course':
			$url = admin_url( 'edit.php?post_type='.@$_GET['post_type'].'&course_id='.learndash_get_course_id( $id ) );
			if ( learndash_get_course_id( $id ) ){
				echo '<a href="'.$url .'">'.get_the_title( learndash_get_course_id( $id ) ).'</a>';
			} else {
				echo '&#8212;';
			}
			break;

		case 'lesson':
			$parent_id = learndash_get_setting( $id, 'lesson' );
			if ( ! empty( $parent_id ) ) {
				$url = admin_url( 'edit.php?post_type='.@$_GET['post_type'].'&lesson_id='.$parent_id );
				echo '<a href="'.$url.'">'.get_the_title( $parent_id ).'</a>';
			} else {
				echo  '&#8212;';
			}
			break;
		default:
			break;
	}
}



/**
 * Output select dropdown before the filter button to filter post listing
 * by course
 *
 * @since 2.1.0
 */
function restrict_listings_by_course( $post_type, $location = '') {
	global $pagenow;

	$ld_post_types = array( 
		'sfwd-courses',
		'sfwd-lessons', 
		'sfwd-topic', 
		'sfwd-quiz', 
		'sfwd-certificates',
		'groups', 
		'sfwd-assignment',
		'sfwd-transactions',
		'sfwd-essays',
	);

	if ( !is_admin() ) return;
	if ( $pagenow != 'edit.php' ) return;
 	if ( ( isset( $_GET['post_status'] ) ) && ( $_GET['post_status'] == 'trash') ) return;
	if ( ( !isset( $post_type ) ) || ( !in_array( $post_type, $ld_post_types ) ) ) return;

	// Firsr we display the object taxonomies 
	$object_taxonomies = get_object_taxonomies( $post_type );
	if ( ( !empty( $object_taxonomies ) ) && ( is_array( $object_taxonomies ) ) ) {
		// We remove 'category' from the object taxonomies because by now WP has already output it. 
		// Maybe at some point we can move the filter earlier
		$object_taxonomies = array_diff( $object_taxonomies, array('category') );
	}
	$object_taxonomies = apply_filters('learndash-admin-taxonomy-filters-display', $object_taxonomies, $post_type );
	
	if ( ( !empty( $object_taxonomies ) ) && ( is_array( $object_taxonomies ) ) ) {
		foreach( $object_taxonomies as $taxonomy_slug ) {
			if ( isset( $_GET[$taxonomy_slug] ) ) {
				$selected = esc_attr( $_GET[$taxonomy_slug] );
			} else {
				$selected = false;
			}
			//error_log('taxonomy_slug['. $taxonomy_slug .']selected['. $selected .']');
			
			//if ( $taxonomy_slug == 'post_tag' )
			//	$taxonomy_slug_name = 'tag';
			//else
				$taxonomy_slug_name = $taxonomy_slug;
			
			$dropdown_options = array(
				'taxonomy' => $taxonomy_slug,
				'name' => $taxonomy_slug_name,
				'show_option_all' => get_taxonomy( $taxonomy_slug )->labels->all_items,
				'show_option_none' => get_taxonomy( $taxonomy_slug )->labels->all_items,
				'option_none_value' => '',
				'hide_empty' => 0,
				'hierarchical' => get_taxonomy( $taxonomy_slug )->hierarchical,
				'show_count' => 0,
				'orderby' => 'name',
				'value_field' => 'slug',
				'selected' => $selected
			);
			
			echo '<label class="screen-reader-text" for="'. $taxonomy_slug .'">' . sprintf( __( 'Filter by %s', 'learndash' ), get_taxonomy( $taxonomy_slug )->labels->singular_name ) . '</label>';
			wp_dropdown_categories( $dropdown_options );
		}
	}

	$cpt_filters_shown['sfwd-courses'] = array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-assignment', 'sfwd-quiz', 'sfwd-essays', /* 'sfwd-certificates', */ 'groups', 'sfwd-transactions' );
	$cpt_filters_shown['sfwd-lessons'] = array( 'sfwd-topic', 'sfwd-assignment', 'sfwd-quiz', 'sfwd-essays' );
	$cpt_filters_shown['sfwd-topic'] = array();
	$cpt_filters_shown['sfwd-quiz'] = array( 'sfwd-essays', /* 'sfwd-certificates' */ );
		
	$cpt_filters_shown = apply_filters( 'learndash-admin-cpt-filters-display', $cpt_filters_shown );

	$course_ids = array();
	$lesson_ids = array();
	$group_ids = array();

	// Courses filter
	if ( in_array( $_GET['post_type'], $cpt_filters_shown['sfwd-courses'] ) ) {
		$query_options_course = array( 
			'post_type' 		=> 	'sfwd-courses', 
			'post_status' 		=> 	'any',  
			'posts_per_page' 	=> 	-1,
			'orderby'			=>	'title',
			'order'				=>	'ASC'	
		);
		
		if ( learndash_is_group_leader_user( get_current_user_id() ) ) {
			$group_ids = learndash_get_administrators_group_ids( get_current_user_id() );
			if ( ! empty( $group_ids ) && is_array( $group_ids ) ) {
				foreach( $group_ids as $group_id ) {
					$group_course_ids = learndash_group_enrolled_courses( $group_id );
					if ( ! empty( $group_course_ids ) && is_array( $group_course_ids ) ) {
						$course_ids = array_merge( $course_ids, $group_course_ids );
					}
				}
			}
		
			if ( ! empty( $course_ids ) && count( $course_ids ) ) {
				$query_options_course['post__in'] = $course_ids;
			}
		}

		$lazy_load = apply_filters('learndash_element_lazy_load_admin', true);
		if ( $lazy_load == true ) {
			$lazy_load = apply_filters('learndash_element_lazy_load_admin_'. $_GET['post_type'] .'_filters', true);
			if ( $lazy_load == true ) {
				$query_options_course['paged'] 			= 	1;
				$query_options_course['posts_per_page'] = 	apply_filters('learndash_element_lazy_load_per_page', LEARNDASH_LMS_DEFAULT_LAZY_LOAD_PER_PAGE, $_GET['post_type'] );
			}
		}

		$query_options_course = apply_filters( 'learndash_course_post_options_filter', $query_options_course, $_GET['post_type'] );
		
		$query_posts_course = new WP_Query( $query_options_course );
		
		if ( ! empty( $query_posts_course->posts ) ) {
			if ( count( $query_posts_course->posts ) >= $query_posts_course->found_posts ) {
				// If the number of returned posts is equal or greater then found_posts then no need to run lazy load
				$lazy_load = false;
			}

			if ($lazy_load == true) {
				$lazy_load_data = array();
				$lazy_load_data['query_vars'] 	= 	$query_options_course;
				$lazy_load_data['query_type']	= 	'WP_Query';
			
				if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) )
					$lazy_load_data['value']	=	intval( $_GET['course_id'] );
				else
					$lazy_load_data['value']	=	0;
			
				$lazy_load_data = ' learndash_lazy_load_data="'. htmlspecialchars( json_encode( $lazy_load_data ) ) .'" ';
			} else {
				$lazy_load_data = '';
			}

			echo "<select ". $lazy_load_data ." name='course_id' id='course_id' class='postform'>";
			echo "<option value=''>". sprintf( _x( 'Show All %s', 'Show All Courses Option Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ).'</option>';

			foreach ( $query_posts_course->posts as $p ) {
				echo '<option value='. $p->ID, ( @$_GET['course_id'] == $p->ID ? ' selected="selected"' : '').'>' . $p->post_title .'</option>';
			}
			echo '</select>';
		
			$lazy_load_spinner = '<span style="display:none;" class="learndash_lazy_loading"><img class="learndash_lazy_load_spinner" alt="'. __('loading', 'learndash') .'" src="'. admin_url('/images/wpspin_light.gif') .'" /> </span>';
			echo $lazy_load_spinner;
		
		} 
	}
	
	// Lessons filter
	if ( in_array( $_GET['post_type'], $cpt_filters_shown['sfwd-lessons'] ) ) {
			
		$query_options_lesson = array( 
			'post_type' 		=> 	'sfwd-lessons', 
			'post_status' 		=> 	'any',  
			'posts_per_page' 	=> 	-1,
			'orderby'			=>	'title',
			'order'				=>	'ASC'	
		);

		$LOAD_LESSONS = true;
		if ( learndash_is_group_leader_user( get_current_user_id() ) ) {
			//error_log('in GROUP LEADER');
			//error_log('course_ids<pre>'. print_r($course_ids, true) .'</pre>');

			if ( !empty( $course_ids ) ) {

				if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
					if ( in_array( $_GET['course_id'], $course_ids ) ) {

						if (!isset( $query_options_lesson['meta_query'] ) )
							$query_options_lesson['meta_query'] = array();
			
						$query_options_lesson['meta_query'][] = array(
							'key'     => 'course_id',
							'value'   => array( intval( $_GET['course_id'] ) ),
							'compare' => 'IN',
						);
					} else {
						$LOAD_LESSONS = false;
					}
				} else {
					if (!isset( $query_options_lesson['meta_query'] ) )
						$query_options_lesson['meta_query'] = array();
				
					$query_options_lesson['meta_query'][] = array(
						'key'     => 'course_id',
						'value'   => $course_ids,
						'compare' => 'IN',
					);
				}
			} else {
				$LOAD_LESSONS = false;
			}
		} else {
			
			// If the course_id is selected we limit the lesson selector to only those related to course_id
			// @since 2.3
			if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {

				if (!isset( $query_options_lesson['meta_query'] ) )
					$query_options_lesson['meta_query'] = array();
			
				$query_options_lesson['meta_query'][] = array(
					'key'     => 'course_id',
					'value'   => array( intval( $_GET['course_id'] ) ),
					'compare' => 'IN',
				);
			
			} else {
				if ( ! empty( $course_ids ) && count( $course_ids ) ) {
				
					if (!isset( $query_options_lesson['meta_query'] ) )
						$query_options_lesson['meta_query'] = array();
				
					$query_options_lesson['meta_query'][] = array(
						'key'     => 'course_id',
						'value'   => $course_ids,
						'compare' => 'IN',
					);
				}
			}
		}
				
		
		if ( $LOAD_LESSONS ) {
			$lazy_load = apply_filters('learndash_element_lazy_load_admin', true);
			if ( $lazy_load == true ) {
				$lazy_load = apply_filters('learndash_element_lazy_load_admin_'. $_GET['post_type'] .'_filters', true);
				if ( $lazy_load == true ) {
					$query_options_lesson['paged'] 			= 	1;
					$query_options_lesson['posts_per_page'] = 	apply_filters('learndash_element_lazy_load_per_page', LEARNDASH_LMS_DEFAULT_LAZY_LOAD_PER_PAGE, $_GET['post_type']);
				}
			}
		
			$query_options_lesson = apply_filters( 'learndash_lesson_post_options_filter', $query_options_lesson, $_GET['post_type'] );		
			$query_posts_lesson = new WP_Query( $query_options_lesson );
			if ( ! empty( $query_posts_lesson->posts ) ) {
				if ( count( $query_posts_lesson->posts ) >= $query_posts_lesson->found_posts ) {
					// If the number of returned posts is equal or greater then found_posts then no need to run lazy load
					$lazy_load = false;
				}

				if ($lazy_load == true) {
					$lazy_load_data = array();
					$lazy_load_data['query_vars'] 	= 	$query_options_lesson;
					$lazy_load_data['query_type']	= 	'WP_Query';
			
					if (isset( $_GET['lesson_id'] ) )
						$lazy_load_data['value']	=	intval( $_GET['lesson_id'] );
					else
						$lazy_load_data['value']	=	0;
			
					$lazy_load_data = ' learndash_lazy_load_data="'. htmlspecialchars( json_encode( $lazy_load_data ) ) .'" ';
				} else {
					$lazy_load_data = '';
				}

				echo "<select ". $lazy_load_data ." name='lesson_id' id='lesson_id' class='postform'>";
				echo "<option value=''>".sprintf( _x( 'Show All %s', 'Show All Lessons Option Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'lessons' ) ).'</option>';

				foreach ( $query_posts_lesson->posts as $p ) {
					echo '<option value='. $p->ID, ( @$_GET['lesson_id'] == $p->ID ? ' selected="selected"' : '').'>' . $p->post_title .'</option>';
				
					if ( ( $_GET['post_type'] == 'sfwd-essays' ) || ( $_GET['post_type'] == 'sfwd-quiz' ) || ( $_GET['post_type'] == 'sfwd-assignment' ) ) {
						$query_options_topic = array( 
							'post_type' 		=> 	'sfwd-topic', 
							'post_status' 		=> 	'any',
							'posts_per_page' 	=> 	-1,
							'orderby'			=>	'title',
							'order'				=>	'ASC',
							'meta_query'		=>	array(
								array(
									'key'     => 'lesson_id',
									'value'   => $p->ID,
									'compare' => '=',
								)
							)		
						);
				
						$query_posts_topic = new WP_Query( $query_options_topic );
						if ( ! empty( $query_posts_topic->posts ) ) {
							foreach ( $query_posts_topic->posts as $topic ) {
								echo '<option value='. $topic->ID, ( @$_GET['lesson_id'] == $topic->ID ? ' selected="selected"' : '').'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $topic->post_title .'</option>';
							}
						}
					}
				}
				echo '</select>';
			}
		}
	}

	// Topicss filter
	if ( in_array( $_GET['post_type'], $cpt_filters_shown['sfwd-topic'] ) ) {
			
		$query_options_topic = array( 
			'post_type' 		=> 	'sfwd-topic', 
			'post_status' 		=> 	'any',  
			'posts_per_page' 	=> 	-1,
			'orderby'			=>	'title',
			'order'				=>	'ASC'	
		);

		// If the course_id is selected we limit the lesson selector to only those related to course_id
		// @since 2.3
		if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) ) {
			$query_options_topic['meta_key'] = 'lesson_id';
			$query_options_topic['meta_value'] = intval( $_GET['lesson_id'] );
			$query_options_topic['meta_compare'] = '=';
		} else {
			if ( ! empty( $lesson_ids ) && count( $lesson_ids ) ) {
				if (!isset( $query_options_topic['meta_query'] ) )
					$query_options_topic['meta_query'] = array();
				
				$query_options_topic['meta_query'][] = array(
					'key'     => 'lesson_id',
					'value'   => $lesson_ids,
					'compare' => 'IN',
				);
			}
		}

		$lazy_load = apply_filters('learndash_element_lazy_load_admin', true);
		if ( $lazy_load == true ) {
			$lazy_load = apply_filters('learndash_element_lazy_load_admin_'. $_GET['post_type'] .'_filters', true);
			if ( $lazy_load == true ) {
				$query_options_topic['paged'] 			= 	1;
				$query_options_topic['posts_per_page'] 	= 	apply_filters('learndash_element_lazy_load_per_page', LEARNDASH_LMS_DEFAULT_LAZY_LOAD_PER_PAGE, $_GET['post_type']);
			}
		}
		
		$query_options_topic = apply_filters( 'learndash_lesson_post_options_filter', $query_options_topic, $_GET['post_type'] );		
		
		$query_posts_topic = new WP_Query( $query_options_topic );
		
		if ( ! empty( $query_posts_topic->posts ) ) {
			if ( count( $query_posts_topic->posts ) >= $query_posts_topic->found_posts ) {
				// If the number of returned posts is equal or greater then found_posts then no need to run lazy load
				$lazy_load = false;
			}

			if ($lazy_load == true) {
				$lazy_load_data = array();
				$lazy_load_data['query_vars'] 	= 	$query_options_topic;
				$lazy_load_data['query_type']	= 	'WP_Query';
			
				if (isset( $_GET['topic_id'] ) )
					$lazy_load_data['value']	=	intval( $_GET['topic_id'] );
				else
					$lazy_load_data['value']	=	0;
			
				$lazy_load_data = ' learndash_lazy_load_data="'. htmlspecialchars( json_encode( $lazy_load_data ) ) .'" ';
			} else {
				$lazy_load_data = '';
			}

			echo "<select ". $lazy_load_data ." name='topic_id' id='topic_id' class='postform'>";
			echo "<option value=''>".sprintf( _x( 'Show All %s', 'Show All Topics Option Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ).'</option>';
			foreach ( $query_posts_topic->posts as $p ) {
				echo '<option value='. $p->ID, ( @$_GET['topic_id'] == $p->ID ? ' selected="selected"' : '').'>' . $p->post_title .'</option>';
			}
			echo '</select>';
		}
	}


	// Quiz Filters
	if ( in_array( $_GET['post_type'], $cpt_filters_shown['sfwd-quiz'] ) ) {	
		
		//$quiz    = new WpProQuiz_Model_QuizMapper();
		//$quizzes = $quiz->fetchAll();
		//echo "<select name='quiz_id' id='quiz_id' class='postform'>";
		//echo "<option value=''>". sprintf( _x( 'Show All %s', 'Show All Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ) .'</option>';
		//foreach ( $quizzes as $quiz ) {
		//	echo '<option value='. $quiz->getId(), ( @$_GET['quiz_id'] == $quiz->getId() ? ' selected="selected"' : '').'>' . $quiz->getName() .'</option>';
		//}
		//echo '</select>';
		
		$query_options_quiz = array( 
			'post_type' 		=> 	'sfwd-quiz', 
			'post_status' 		=> 	'any',  
			'posts_per_page' 	=> 	-1,
			'orderby'			=>	'title',
			'order'				=>	'ASC'	
		);

		// If the course_id is selected we limit the lesson selector to only those related to course_id
		// @since 2.3
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			$query_options_quiz['meta_key'] = 'course_id';
			$query_options_quiz['meta_value'] = intval( $_GET['course_id'] );
		} else {
			if ( ! empty( $course_ids ) && count( $course_ids ) ) {
				
				if (!isset( $query_options_quiz['meta_query'] ) )
					$query_options_quiz['meta_query'] = array();
				
				$query_options_quiz['meta_query'][] = array(
					'key'     => 'course_id',
					'value'   => $course_ids,
					'compare' => 'IN',
				);
			}
		}
		$query_options_quiz = apply_filters( 'learndash_lesson_post_options_filter', $query_options_quiz, $_GET['post_type'] );
		$query_posts_quiz = new WP_Query( $query_options_quiz );

		if ( ! empty( $query_posts_quiz->posts ) ) {
			if ( count( $query_posts_quiz->posts ) >= $query_posts_quiz->found_posts ) {
				// If the number of returned posts is equal or greater then found_posts then no need to run lazy load
				$lazy_load = false;
			}

			if ($lazy_load == true) {
				$lazy_load_data = array();
				$lazy_load_data['query_vars'] 	= 	$query_options_quiz;
				$lazy_load_data['query_type']	= 	'WP_Query';
			
				if (isset( $_GET['quiz_id'] ) )
					$lazy_load_data['value']	=	intval( $_GET['quiz_id'] );
				else
					$lazy_load_data['value']	=	0;
			
				$lazy_load_data = ' learndash_lazy_load_data="'. htmlspecialchars( json_encode( $lazy_load_data ) ) .'" ';
			} else {
				$lazy_load_data = '';
			}

			echo "<select ". $lazy_load_data ." name='quiz_id' id='lesson_id' class='postform'>";
			echo "<option value=''>".sprintf( _x( 'Show All %s', 'Show All Quizzes Option Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ).'</option>';
			foreach ( $query_posts_quiz->posts as $p ) {
				$quiz_pro_id = get_post_meta( $p->ID, 'quiz_pro_id', true );
				if ( !empty( $quiz_pro_id ) ) {
					echo '<option value="'. $quiz_pro_id .'" '. selected( @$_GET['quiz_id'], $quiz_pro_id, false) .'>' . $p->post_title .'</option>';
				}
			}
			echo '</select>';
		}
	}

	if ( $_GET['post_type'] == 'sfwd-assignment' ) {
		$selected_1 = '';
		$selected_0 = '';

		if ( isset( $_GET['approval_status'] ) ) {
			if ( $_GET['approval_status'] == 1 ) {
				$selected_1 = 'selected="selected"';
				$selected_0 = '';
			} if ( $_GET['approval_status'] == 0 ) {
				$selected_0 = 'selected="selected"';
				$selected_1 = '';
			}
		} else if ((isset($_GET['approval_status'])) && ( $_GET['approval_status'] == 0 )) {
			$selected_0 = 'selected="selected"';
			$selected_1 = '';
		} else if ( !isset( $_GET['approval_status'] ) ) {
			$selected_0 = '';
			$selected_1 = '';
		}
		?>
			<select name='approval_status' id='approval_status' class='postform'>
				<option value='-1'><?php _e( 'Approval Status', 'learndash' ); ?></option>
				<option value='1' <?php echo $selected_1; ?>><?php _e( 'Approved', 'learndash' ); ?></option>
				<option value='0' <?php echo $selected_0; ?>><?php _e( 'Not Approved', 'learndash' ); ?></option>
			</select>
		<?php
	}
}



/**
 * Filter queries in admin post listing by what user selects
 *
 * @since 2.1.0
 *
 * @param  object $query 	WP_Query object
 * @return object $q_vars    WP_Query object
 */
function course_table_filter( $query ) {
	global $pagenow, $typenow;
	$q_vars = &$query->query_vars;

	//error_log('in '. __FUNCTION__ );
	//error_log('_GET<pre>'. print_r($_GET, true) .'</pre>');
	//error_log('q_vars<pre>'. print_r($q_vars, true) .'</pre>');

	if ( !is_admin() ) return;
	if ( $pagenow != 'edit.php' ) return;
	if ( !$query->is_main_query() ) return;
	if ( empty( $typenow ) ) return;
	
	/*
	if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) 
	  && ( $typenow == 'sfwd-lessons' || $typenow == 'sfwd-topic' || $typenow == 'sfwd-quiz' || $typenow == 'sfwd-assignment' || $typenow == 'sfwd-essays' ) ) {
			$q_vars['meta_query'][] = array(
				'key' => 'course_id',
				'value'	=> $_GET['course_id'],
			);
		}

		if ( ( isset($_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) && ( $typenow == 'sfwd-topic' || $typenow == 'sfwd-assignment' || $typenow == 'sfwd-essays' ) ) {
			$q_vars['meta_query'][] = array(
				'key' => 'lesson_id',
				'value'	=> $_GET['lesson_id'],
			);
		}

		if ( ( isset( $_GET['quiz_id'] ) )  && ( !empty( $_GET['quiz_id'] ) ) && ( $typenow == 'sfwd-essays' ) ) {
			$q_vars['meta_query'][] = array(
				'key' 	=>	'quiz_id',
				'value'	=> 	intval( $_GET['quiz_id'] ),
			);
		}

		// set custom post status anytime we are looking at essays with no particular post status
		if ( ( isset( $_GET['post_status'] ) ) && ( !isset( $_GET['post_status'] ) ) && ( $typenow == 'sfwd-essays' ) ) {
			$q_vars['post_status'] = array( 'graded', 'not_graded' );
		}

		if ( ( isset( $_GET['approval_status'] ) ) && ( $typenow == 'sfwd-topic' || $typenow == 'sfwd-assignment' ) ) {
			if ( $_GET['approval_status'] == 1 ) {
				$q_vars['meta_query'][] = array(
					'key' 	=> 	'approval_status',
					'value'	=> 	1,
				);
			} else if ( $_GET['approval_status'] == 0 ) {
				$q_vars['meta_query'][] = array(
					'key' 		=> 	'approval_status',
					'compare' 	=> 	'NOT EXISTS',
				);
			}
		}
	}
	*/

	
	if ( $typenow == 'sfwd-courses' ) {
		if ( ( isset( $_GET['post_tag'] ) ) && ( !empty( $_GET['post_tag'] ) ) ) {
			$post_tag = esc_attr( $_GET['post_tag'] );
			if ( $post_tag != '0' ) {
				$post_tag_term = get_term_by( 'slug', $post_tag, 'post_tag' );
				if ( ( !empty( $post_tag_term ) ) && ( $post_tag_term instanceof WP_Term ) ) {
					$q_vars['tag_id'] = $post_tag_term->term_id;
				}
			}
		}
		
	} else if ( $typenow == 'sfwd-lessons' ) {
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'course_id',
				'value'		=> 	intval( $_GET['course_id'] ),
			);
		}
		if ( ( isset( $_GET['post_tag'] ) ) && ( !empty( $_GET['post_tag'] ) ) ) {
			$post_tag = esc_attr( $_GET['post_tag'] );
			if ( $post_tag != '0' ) {
				$post_tag_term = get_term_by( 'slug', $post_tag, 'post_tag' );
				if ( ( !empty( $post_tag_term ) ) && ( $post_tag_term instanceof WP_Term ) ) {
					$q_vars['tag_id'] = $post_tag_term->term_id;
				}
			}
		}
			
	} else if ( $typenow == 'sfwd-topic' ) {
		if ( ( isset( $_GET['post_tag'] ) ) && ( !empty( $_GET['post_tag'] ) ) ) {
			$post_tag = esc_attr( $_GET['post_tag'] );
			if ( $post_tag != '0' ) {
				$post_tag_term = get_term_by( 'slug', $post_tag, 'post_tag' );
				if ( ( !empty( $post_tag_term ) ) && ( $post_tag_term instanceof WP_Term ) ) {
					$q_vars['tag_id'] = $post_tag_term->term_id;
				}
			}
		}
		
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'course_id',
				'value'		=> 	intval($_GET['course_id']),
			);
		}
		
		if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'lesson_id',
				'value'		=> 	intval( $_GET['lesson_id'] ),
			);
		}
		
		$q_vars['relation'] = 'AND';
		
	} else if ( $typenow == 'sfwd-quiz' ) {
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();
			
			$q_vars['meta_query'][] = array(
				'key' => 'course_id',
				'value'	=> intval($_GET['course_id']),
			);
		}

		if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'lesson_id',
				'value'		=> 	intval( $_GET['lesson_id'] ),
			);
		}
	} else if ( $typenow == 'sfwd-assignment' ) {
		
		if ( ( isset( $_GET['approval_status'] ) ) && ( $_GET['approval_status'] == 1 ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();
			
			$q_vars['meta_query'][] = array(
				'key' 	=> 	'approval_status',
				'value'	=> 	1,
			);
		} else if ( ( isset( $_GET['approval_status'] ) ) && ( $_GET['approval_status'] == 0 ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();
			
			$q_vars['meta_query'][] = array(
				'key' 		=> 	'approval_status',
				'compare' 	=> 	'NOT EXISTS',
			);
		} else if ( !isset( $_GET['approval_status'] ) ) {
			///
		}
		
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'course_id',
				'value'		=> 	intval( $_GET['course_id'] ),
			);
		}

		if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'lesson_id',
				'value'		=> 	intval( $_GET['lesson_id'] ),
			);
		}
	} else if ( $typenow == 'groups' ) {
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			$groups = learndash_get_course_groups( intval($_GET['course_id']), true );
			if ( !empty( $groups ) ) 
				$q_vars['post__in'] = $groups;
			else 
				$q_vars['post__in'] = array(-1);
		}
	} else if ( $typenow == 'sfwd-essays' ) {
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'course_id',
				'value'		=> 	intval( $_GET['course_id'] ),
			);
		}

		if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'lesson_id',
				'value'		=> 	intval( $_GET['lesson_id'] ),
			);
		}
	} else if ( $typenow == 'sfwd-transactions' ) {
		if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
			if ( !isset( $q_vars['meta_query'] ) ) $q_vars['meta_query'] = array();

			$q_vars['meta_query'][] = array(
				'key' 		=> 	'course_id',
				'value'		=> 	intval( $_GET['course_id'] ),
			);
		}
		
	}

//	if ( isset( $q_vars['meta_query'] ) ) {
//		error_log('meta_query<pre>'. print_r( $q_vars['meta_query'], true ) .'</pre>');
//	} else {
//		error_log('meta_query not set');
//	}	
}



/**
 * Generate lesson id's and course id's once for all existing lessons, quizzes and topics
 *
 * @since 2.1.0
 */
function learndash_generate_patent_course_and_lesson_id_onetime() {

	if ( isset( $_GET['learndash_generate_patent_course_and_lesson_ids_onetime'] ) || get_option( 'learndash_generate_patent_course_and_lesson_ids_onetime', 'yes' ) == 'yes' ) {
		$quizzes = get_posts( 'post_type=sfwd-quiz&posts_per_page=-1' );

		if ( ! empty( $quizzes ) ) {
			foreach ( $quizzes as $quiz ) {
				update_post_meta( $quiz->ID, 'course_id', learndash_get_course_id( $quiz->ID ) );
				$meta = get_post_meta( $quiz->ID, '_sfwd-quiz', true );
				if ( ! empty( $meta['sfwd-quiz_lesson'] ) ) {
					update_post_meta( $quiz->ID, 'lesson_id', $meta['sfwd-quiz_lesson'] );
				}
			}//exit;
		}

		$topics = get_posts( 'post_type=sfwd-topic&posts_per_page=-1' );

		if ( ! empty( $topics) ) {
			foreach ( $topics as $topic ) {
				update_post_meta( $topic->ID, 'course_id', learndash_get_course_id( $topic->ID ) );
				$meta = get_post_meta( $topic->ID, '_sfwd-topic', true );
				if ( ! empty( $meta['sfwd-topic_lesson'] ) ) {
					update_post_meta( $topic->ID, 'lesson_id', $meta['sfwd-topic_lesson'] );
				}
			}
		}

		$lessons = get_posts( 'post_type=sfwd-lessons&posts_per_page=-1' );

		if ( ! empty( $lessons) ) {
			foreach ( $lessons as $lesson ) {
				update_post_meta( $lesson->ID, 'course_id', learndash_get_course_id( $lesson->ID ) );
			}
		}

		update_option( 'learndash_generate_patent_course_and_lesson_ids_onetime', 'no' );

	}
}

add_action( 'admin_init', 'learndash_generate_patent_course_and_lesson_id_onetime' );



/**
 * On post save, update post id's that maintain relationships between
 * courses, lessons, topics, and quizzes
 *
 * @since 2.1.0
 *
 * @param  int $post_id
 */
function learndash_patent_course_and_lesson_id_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( empty( $post_id ) || empty( $_POST['post_type'] ) ) {
		return '';
	}

	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( 'sfwd-lessons' == $_POST['post_type'] || 'sfwd-quiz' == $_POST['post_type'] || 'sfwd-topic' == $_POST['post_type'] ) {
		if ( isset( $_POST[ $_POST['post_type'].'_course'] ) ) {
			update_post_meta( $post_id, 'course_id', @$_POST[ $_POST['post_type'].'_course'] );
		}
	}

	if ( 'sfwd-topic' == $_POST['post_type'] || 'sfwd-quiz' == $_POST['post_type'] ) {
		if ( isset( $_POST[ $_POST['post_type'].'_lesson'] ) ) {
			update_post_meta( $post_id, 'lesson_id', @$_POST[ $_POST['post_type'].'_lesson'] );
		}
	}

	if ( 'sfwd-lessons' == $_POST['post_type'] || 'sfwd-topic' == $_POST['post_type'] ) {
		global $wpdb;

		if ( isset( $_POST[ $_POST['post_type'].'_course'] ) ) {
			$course_id = get_post_meta( $post_id, 'course_id', true );
		}

		if ( ! empty( $course_id ) ) {
			$posts_with_lesson = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'lesson_id' AND meta_value = '%d'", $post_id ) );

			if ( ! empty( $posts_with_lesson) && ! empty( $posts_with_lesson[0] ) ) {
				foreach ( $posts_with_lesson as $post_with_lesson ) {
					$post_course_id = learndash_get_setting( $post_with_lesson, 'course' );

					if ( $post_course_id != $course_id ) {
						learndash_update_setting( $post_with_lesson, 'course', $course_id );

						$quizzes_under_lesson_topic = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'lesson_id' AND meta_value = '%d'", $posts_with_lesson ) );
						if ( ! empty( $quizzes_under_lesson_topic) && ! empty( $quizzes_under_lesson_topic[0] ) ) {
							foreach ( $quizzes_under_lesson_topic as $quiz_post_id ) {
								$quiz_course_id = learndash_get_setting( $quiz_post_id, 'course' );
								if ( $course_id != $quiz_course_id ) {
									learndash_update_setting( $quiz_course_id, 'course', $course_id );
								}
							}
						}
					}
				}
			}

		}

	}
}

add_action( 'save_post', 'learndash_patent_course_and_lesson_id_save' );


/**
 * Add action links to quizzes post listing on post hover
 * Questions, Statistics, Leaderboard
 *
 * @since 2.1.0
 *
 * @param array   $actions An array of row action links
 * @param WP_Post $post    The post object.
 * @return array  $actions An array of row action links
 */
function learndash_quizzes_inline_actions( $actions, $post ) {
	if ( $post->post_type == 'sfwd-quiz' ) {
		$pro_quiz_id = learndash_get_setting( $post, 'quiz_pro', true );

		if ( empty( $pro_quiz_id ) ) {
			return $actions;
		}

		$statistics_link = admin_url( 'admin.php?page=ldAdvQuiz&module=statistics&id='.$pro_quiz_id.'&post_id='.$post->ID );
		$questions_link = admin_url( 'admin.php?page=ldAdvQuiz&module=question&quiz_id='.$pro_quiz_id.'&post_id='.$post->ID );
		$leaderboard_link = admin_url( 'admin.php?page=ldAdvQuiz&module=toplist&id='.$pro_quiz_id.'&post_id='.$post->ID );

		$actions['questions'] = "<a href='".$questions_link."'>".__( 'Questions', 'learndash' ).'</a>';
		$actions['statistics'] = "<a href='".$statistics_link."'>".__( 'Statistics', 'learndash' ).'</a>';
		$actions['leaderboard'] = "<a href='".$leaderboard_link."'>".__( 'Leaderboard', 'learndash' ).'</a>';
	}

	return $actions;
}

add_filter( 'post_row_actions', 'learndash_quizzes_inline_actions', 10, 2 );



function learndash_element_lazy_loader() {
		
	$reply_data = array();

	if ((isset($_POST['query_data'])) && (!empty($_POST['query_data']))) {

		if ( ( isset( $_POST['query_data']['query_vars'] ) ) && ( !empty( $_POST['query_data']['query_vars'] ) ) ) {
			$reply_data['query_data'] = $_POST['query_data'];
			
			if ( isset( $_POST['query_data']['query_type'] ) ) {
				switch( $_POST['query_data']['query_type'] ) {
					case 'WP_Query':
						$query = new WP_Query( $_POST['query_data']['query_vars'] );
						if ( $query instanceof WP_Query ) {
							if ( ! empty( $query->posts ) ) {
								$reply_data['html_options'] = '';
								foreach ( $query->posts as $p ) {
									if ( intval($p->ID) == intval($_POST['query_data']['value'])) 
										$selected = ' selected="selected" ';
									else
										$selected = '';
									$reply_data['html_options'] .= '<option '. $selected .' value="'. $p->ID .'">'. $p->post_title .'</option>';
								}
							}
						} 
						break;

					case 'WP_User_Query':
						$query = new WP_User_Query( $_POST['query_data']['query_vars'] );
						break;

					default:
						break;
				}
			}
		}
	}
	
	echo json_encode($reply_data);
	
	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_learndash_element_lazy_loader', 'learndash_element_lazy_loader' );


add_filter('views_edit-sfwd-essays', 'learndash_edit_list_table_views', 10, 1 );
add_filter('views_edit-sfwd-assignment', 'learndash_edit_list_table_views', 10, 1 );
function learndash_edit_list_table_views( $views = array() ) {
	if ( ! learndash_is_admin_user() ) { 
		$views = array();
	}

	return $views;
}

add_filter( 'plugin_row_meta', 'learndash_plugin_row_meta', 10, 4 );
function learndash_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( $plugin_file == LEARNDASH_LMS_PLUGIN_KEY ) {
		if ( !isset( $plugin_meta['changelog'] ) ) {
			$plugin_meta['changelog'] = '<a target="_blank" href="https://www.learndash.com/changelog">'. __('Changelog', 'learndash' ) .'</a>';
		}
	}
	
	return $plugin_meta;
}


// Override the post_tag edit-tags.php 'Count' column to show only the related count for the LD post_type
function learndash_manage_edit_post_tag_columns( $columns = array() ) {
	if ( ( isset( $_GET['post_type'] ) ) && ( !empty( $_GET['post_type'] ) ) ) {
		if ( in_array( $_GET['post_type'], array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic' ) ) ) {
			if ( isset( $columns['posts'] ) ) unset( $columns['posts'] );
			$columns['ld_posts'] = __( 'Count', 'learndash' );
		}
	}

    return $columns;
}
add_filter('manage_edit-post_tag_columns', 'learndash_manage_edit_post_tag_columns' );
//function learndash_manage_edit_category_columns( $columns = array() ) {
//	//error_log('columns<pre>'. print_r($columns, true) .'</pre>');
//	if ( ( isset( $_GET['post_type'] ) ) && ( !empty( $_GET['post_type'] ) ) ) {
//		if ( isset( $columns['posts'] ) ) unset( $columns['posts'] );
//		
//		$columns['ld_posts'] = __( 'Count', 'learndash' );
//	}
//
//    return $columns;
//}
add_filter('manage_edit-category_columns', 'learndash_manage_edit_post_tag_columns' );

function learndash_manage_post_tag_custom_column( $column_content = '', $column_name, $term_id ) {
	if ($column_name == 'ld_posts' ) {
		if ( ( isset( $_GET['post_type'] ) ) && ( !empty( $_GET['post_type'] ) ) ) {
			if ( in_array( $_GET['post_type'], array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic' ) ) ) {
				$query_args = array(
					'post_type'		=>	esc_attr( $_GET['post_type'] ),
					'post_status' 	=> 	'publish',
					'tag_id' 		=> 	$term_id,
					'fields' 		=> 	'ids',
					'nopaging'		=>	true
				);
			
				$query_results = new WP_Query( $query_args );
				if ( !is_wp_error( $query_results ) ) {
					$count = count( $query_results->posts );
					if ( $count > 0 ) {
						$term = get_term_by('id', $term_id, 'category');
						$column_content = "<a href='" . esc_url ( add_query_arg( array(
							'post_type' => 	esc_attr( $_GET['post_type'] ),
							'taxonomy'	=>	'post_tag',
							'post_tag'	=>	$term->slug
						), 'edit.php' ) ) . "'>". count( $query_results->posts ) ."</a>";
					} else {
						$column_content = 0;
					}
				}
			}
		}
	}
	return $column_content;
}
add_filter( "manage_post_tag_custom_column", 'learndash_manage_post_tag_custom_column', 10, 3 );

// Override the category edit-tags.php 'Count' column to show only the related count for the LD post_type
function learndash_manage_category_custom_column( $column_content = '', $column_name, $term_id ) {
	if ($column_name == 'ld_posts' ) {
		if ( ( isset( $_GET['post_type'] ) ) && ( !empty( $_GET['post_type'] ) ) ) {
			if ( in_array( $_GET['post_type'], array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic' ) ) ) {
				$query_args = array(
					'post_type'		=>	esc_attr( $_GET['post_type'] ),
					'post_status' 	=> 	'publish',
					'cat' 			=> 	$term_id,
					'fields' 		=> 	'ids',
					'nopaging'		=>	true
				);
			
				$query_results = new WP_Query( $query_args );
				if ( !is_wp_error( $query_results ) ) {
					$count = count( $query_results->posts );
					if ( $count > 0 ) {
						//$term = get_term_by('id', $term_id, 'category');						
						$column_content = "<a href='" . esc_url ( add_query_arg( array(
							'post_type' => 	esc_attr( $_GET['post_type'] ),
							'taxonomy'	=>	'category',
							'cat'		=>	$term_id
						), 'edit.php' ) ) . "'>". count( $query_results->posts ) ."</a>";
					} else {
						$column_content = 0;
					}
				}
			}
		}
	}
	return $column_content;
}
add_filter( "manage_category_custom_column", 'learndash_manage_category_custom_column', 10, 3 );
