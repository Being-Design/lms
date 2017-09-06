<?php
/**
 * Course Functions
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\Course
 */



/**
 * Get course ID for resource.
 * 
 * Determine type of ID is being passed in.  Should be the ID of
 * anything that belongs to a course (Lesson, Topic, Quiz, etc)
 *
 * @since 2.1.0
 * 
 * @param  obj|int 	$id 	id of resource
 * @return string    		id of course
 */
function learndash_get_course_id( $id = null ) {
	global $post;

	if ( is_object( $id ) && $id->ID ) {
		$p = $id;
		$id = $p->ID;
	} else if ( is_numeric( $id ) ) {
		$p = get_post( $id );
	}

	if ( empty( $id ) ) {
		if ( ! is_single() || is_home() ) {
			return false;
		}

		$id = $post->ID;
		$p = $post;
	}

	if ( empty( $p->ID ) ) {
		return 0;
	}

	if ( $p->post_type == 'sfwd-courses' ) {
		return $p->ID;
	}

	return get_post_meta( $id, 'course_id', true );
}



/**
 * Get course ID for resource (legacy users)
 * 
 * Determine type of ID is being passed in.  Should be the ID of
 * anything that belongs to a course (Lesson, Topic, Quiz, etc)
 * 
 * @since 2.1.0
 * 
 * @param  obj|int 	$id 	id of resource
 * @return string    		id of course
 */
function learndash_get_legacy_course_id( $id = null ){
	global $post;

	if ( empty( $id ) ) {
		if ( ! is_single() || is_home() ) {
			return false;
		}

		$id = $post->ID;
	}

	$terms = wp_get_post_terms( $id, 'courses' );

	if ( empty( $terms) || empty( $terms[0] ) || empty( $terms[0]->slug) ) {
		return 0;
	}

	$courseslug = $terms[0]->slug;

	global $wpdb;

	$term_taxonomy_id = $wpdb->get_var(
		$wpdb->prepare(
			"
		 SELECT `term_taxonomy_id` FROM $wpdb->term_taxonomy tt, $wpdb->terms t 
		 WHERE slug = %s 
		 AND t.term_id = tt.term_id
		 AND tt.taxonomy = 'courses'
		",
			$courseslug
		)
	);

	$course_id = $wpdb->get_var(
		$wpdb->prepare(
			"
		 SELECT `ID` FROM $wpdb->term_relationships, $wpdb->posts 
		 WHERE `ID` = `object_id`
		 AND `term_taxonomy_id` = %d
		 AND `post_type` = 'sfwd-courses'
		 AND `post_status` = 'publish' 
		",
			$term_taxonomy_id
		)
	);

	return $course_id;
}



/**
 * Get lesson id of resource
 *
 * @since 2.1.0
 * 
 * @param  int 		$id  post id of resource
 * @return string     	 lesson id
 */
function learndash_get_lesson_id( $id = null ) {
	global $post;

	if ( empty( $id ) ) {
		if ( ! is_single() || is_home() ) {
			return false;
		}

		$id = $post->ID;
	}

	return get_post_meta( $id, 'lesson_id', true );
}


/**
 * Get array of courses that user has access to
 *
 * @since 2.1.0
 * 
 * @param  int 		$user_id
 * @param array    	array attributes ('order', 'orderby')
 * @return array    array of courses that user has access to
 */
function ld_get_mycourses( $user_id = null, $atts = array() ) {


	if ((defined('LEARNDASH_GROUPS_LEGACY_v220') && (LEARNDASH_GROUPS_LEGACY_v220 === true))) {

		$filter = array(
			'post_type' => 'sfwd-courses', 
			'posts_per_page' => - 1, 
			'post_status' => 'publish'
		);
	
		$loop = new WP_Query( $filter );
		$mycourses = array();

		while ( $loop->have_posts() ) {
			$loop->the_post();

			if ( sfwd_lms_has_access( get_the_ID(), $user_id ) ) {
				$mycourses[] = get_the_ID();
			}
		}

		wp_reset_query();
		return $mycourses;
	} else {
		
		$defaults = array(
			'order' 	=> 'DESC', 
			'orderby' 	=> 'ID', 
		);
		$atts = wp_parse_args( $atts, $defaults );
		
		return learndash_user_get_enrolled_courses( 
			$user_id, 
			$atts, 
			true
 	   );
	}
}


/**
 * Does user have access to course (houses filter)
 * 
 * @since 2.1.0
 * 
 * @param  int 	$post_id 	id of resource
 * @param  int 	$user_id
 * @return bool       
 */
function sfwd_lms_has_access( $post_id, $user_id = null ) {

	 /**
	 * Filter if user has access to course
	 *
	 * Calls sfwd_lms_has_access_fn() to determine if user has access to course
	 * 
	 * @since 2.1.0
	 * 
	 * @param  bool
	 */
	return apply_filters( 'sfwd_lms_has_access', sfwd_lms_has_access_fn( $post_id, $user_id ), $post_id, $user_id );
}



/**
 * Does user have access to course
 * 
 * Check's if user has access to course when they try to access a resource that
 * belong to that course (Lesson, Topic, Quiz, etc.)
 *
 * @since 2.1.0
 * 
 * @param  int 	$post_id 	id of resource
 * @param  int 	$user_id
 * @return bool  
 */
function sfwd_lms_has_access_fn( $post_id, $user_id = null ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( learndash_is_admin_user( $user_id ) ) {
		/**
		 * See example if 'learndash_override_course_auto_enroll' filter 
		 * https://bitbucket.org/snippets/learndash/kon6y
		 *
		 * @since 2.3
		 */
		
		$course_autoenroll_admin = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
		if ( $course_autoenroll_admin == 'yes' ) $course_autoenroll_admin = true;
		else $course_autoenroll_admin = false;
		
		if ( apply_filters('learndash_override_course_auto_enroll', $course_autoenroll_admin, $user_id ) ) {
			return true;
		}
	}

	$course_id = learndash_get_course_id( $post_id );

	if ( empty( $course_id ) ) {
		return true;
	}

	if ( ! empty( $post_id ) && learndash_is_sample( $post_id ) ) {
		return true;
	}

	$meta = get_post_meta( $course_id, '_sfwd-courses', true );
	
	if ( @$meta['sfwd-courses_course_price_type'] == 'open' || @$meta['sfwd-courses_course_price_type'] == 'paynow' && empty( $meta['sfwd-courses_course_join'] ) && empty( $meta['sfwd-courses_course_price'] ) ) {
		return true;
	}

	if ( empty( $user_id ) ) {
		return false;
	}

	if ( ! empty( $meta['sfwd-courses_course_access_list'] ) ) {
		$course_access_list = explode( ',', $meta['sfwd-courses_course_access_list'] );
	} else {
		$course_access_list = array();
	}
	
	//if ( in_array( $user_id, $course_access_list ) ) {
	//	return true;
	//}
	
	//$user_has_group_access = learndash_user_group_enrolled_to_course( $user_id, $course_id );
	//if ( $user_has_group_access ) {
	//	$expired = ld_course_access_expired( $course_id, $user_id );
	//	return ! $expired; //True if not expired.
	//} 
	
	//return false;
	
	if ( in_array( $user_id, $course_access_list ) || learndash_user_group_enrolled_to_course( $user_id, $course_id ) ) {
		$expired = ld_course_access_expired( $course_id, $user_id );
		return ! $expired; //True if not expired.
	} else {
		return false;
	}
	
}



/**
 * Redirect user to course
 *
 * @since 2.1.0
 * 
 * @param  int 	$post_id  id of resource that belongs to a course
 */
function sfwd_lms_access_redirect( $post_id ) {
	$access = sfwd_lms_has_access( $post_id );
	if ( $access === true ) {
		return true;
	}

	$link = get_permalink( learndash_get_course_id( $post_id ) );
	$link = apply_filters( 'learndash_access_redirect' , $link, $post_id );
	wp_redirect( $link );
	exit();
}



/**
 * Is users access to course expired
 *
 * @since 2.1.0
 * 
 * @param  int 	$course_id
 * @param  int 	$user_id  
 * @return bool           
 */
function ld_course_access_expired( $course_id, $user_id ) {
	$course_access_upto = ld_course_access_expires_on( $course_id, $user_id );
	
	if ( empty( $course_access_upto ) ) {
		return false;
	} else {

		if ( time() >= $course_access_upto ) {
			/**
			 * As of LearnDash 2.3.0.3 we store the GMT timestamp as the meta value. In prior versions we stored 1
			*/
			update_user_meta( $user_id, 'learndash_course_expired_'.$course_id, time() );
			ld_update_course_access( $user_id, $course_id, $remove = true );
			$delete_course_progress = learndash_get_setting( $course_id, 'expire_access_delete_progress' );
			if ( ! empty( $delete_course_progress) ) {
				learndash_delete_course_progress( $course_id, $user_id );
			}
			return true;
		} else {
			return false;
		}

	}	 
}



/**
 * Generate alert in wp_head that users access to course is expired
 *
 * @since 2.1.0
 */
function ld_course_access_expired_alert() {
	global $post;

	if ( ! is_singular() || empty( $post->ID ) || $post->post_type != 'sfwd-courses' ) {
		return;
	}

	$user_id = get_current_user_id();

	if ( empty( $user_id ) ) {
		return;
	}
	
	$expired = get_user_meta( $user_id, 'learndash_course_expired_'.$post->ID, true );
	
	if ( empty( $expired) ) {
		return;
	}

	$has_access = sfwd_lms_has_access( $post->ID, $user_id );

	if ( $has_access ) {
		delete_user_meta( $user_id, 'learndash_course_expired_'.$post->ID );
		return;
	} else	{
		?>
		<script>
			setTimeout(function() {
				alert("<?php echo sprintf( _x( 'Your access to this %s has expired.', 'Your access to this course has expired.', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' )); ?>")
			}, 2000);
		</script>
		<?php
	}
}

add_action( 'wp_head', 'ld_course_access_expired_alert', 1 );



/**
 * Get amount of time until users course access expires for user
 *
 * @since 2.1.0
 * 
 * @param  int 	$course_id
 * @param  int 	$user_id  
 * @return int  
 */
function ld_course_access_expires_on( $course_id, $user_id ) {
	// Set a default return var. 
	$course_access_upto = 0;
	
	// Check access to course_id + user_id
	$courses_access_from = ld_course_access_from( $course_id, $user_id );

	// If the course_id + user_id is not set we check the group courses.
	if ( empty( $courses_access_from ) ) {
		$courses_access_from = learndash_user_group_enrolled_to_course_from( $user_id, $course_id );
	}
	
	// If we have a non-empty access from...
	if (  abs( intval( $courses_access_from ) ) ) {
		
		// Check the course is using expire access
		$expire_access = learndash_get_setting( $course_id, 'expire_access' );
		// The value stored in the post meta for 'expire_access' is 'on' not true/false 1 or 0. The string 'on'.
		if ( !empty( $expire_access) ) {
			$expire_access_days = learndash_get_setting( $course_id, 'expire_access_days' );
			if ( abs( intval( $expire_access_days ) )  > 0 ) {
				$course_access_upto = abs( intval( $courses_access_from ) ) + ( abs( intval( $expire_access_days ) ) * DAY_IN_SECONDS );
			}
		}
	}
	return $course_access_upto;
}



/**
 * Get amount of time when lesson becomes available to user
 *
 * @since 2.1.0
 * 
 * @param  int $course_id
 * @param  int $user_id  
 * @return string
 */
function ld_course_access_from( $course_id, $user_id ) {
	return get_user_meta( $user_id, 'course_'.$course_id.'_access_from', true );
}



/**
 * Update list of courses users has access to
 *
 * @since 2.1.0
 * 
 * @param  int 		$user_id   
 * @param  int 	 	$course_id 
 * @param  bool 	$remove    
 * @return array   list of courses users has access to
 */
function ld_update_course_access( $user_id, $course_id, $remove = false ) {
	if ( empty( $user_id ) || empty( $course_id ) ) {
		return;
	}

	$meta = get_post_meta( $course_id, '_sfwd-courses', true );
	$access_list = $meta['sfwd-courses_course_access_list'];

	if ( empty( $remove ) ) {

		if ( empty( $access_list ) ) {
			$access_list = $user_id;
		} else {
			$access_list_arr = explode( ',', $access_list );
			$access_list_arr = array_map( 'intVal', $access_list_arr );
			$access_list_arr[] = $user_id;
			$access_list_arr = array_unique( $access_list_arr );
			$access_list = implode( ',', $access_list_arr );
		}

		$user_course_access_time = get_user_meta( $user_id, "course_".$course_id."_access_from", true );
		if ( empty( $user_course_access_time ) ) {
			$user_course_access_time = time();
			update_user_meta( $user_id, "course_".$course_id."_access_from", $user_course_access_time );
		}

		learndash_update_user_activity(
			array(
				'post_id'			=>	$course_id,
				'activity_type'		=>	'access',
				'user_id'			=>	$user_id,
				'activity_started'	=>	$user_course_access_time,
			)
		); 
	} else if ( ! empty( $access_list ) ) {

		$access_list = explode( ',', $access_list );
		$new_access_list = array();
		foreach ( $access_list as $c ) {
			if ( trim( $c ) != $user_id ) {
				$new_access_list[] = trim( $c );
			}
		}
		$access_list = implode( ',', $new_access_list );
		delete_user_meta( $user_id, 'course_'.$course_id.'_access_from' );

	}

	$meta['sfwd-courses_course_access_list'] = $access_list;
	update_post_meta( $course_id, '_sfwd-courses', $meta );
	
	/**
	 * Run actions after a users list of courses is updated
	 * 
	 * @since 2.1.0
	 * 
	 * @param  int  	$user_id 		
	 * @param  int  	$course_id
	 * @param  array  	$access_list
	 * @param  bool  	$remove
	 */
	do_action( 'learndash_update_course_access', $user_id, $course_id, $access_list, $remove );

	return $meta;	
}



/**
 * Get timestamp of when user has access to lesson
 *
 * @since 2.1.0
 * 
 * @param  int 	$lesson_id
 * @param  int 	$user_id  
 * @return int  timestamp
 */
function ld_lesson_access_from( $lesson_id, $user_id ) {
	$return = null;

	$course_id = learndash_get_course_id( $lesson_id );
	$courses_access_from = ld_course_access_from( $course_id, $user_id );
	if ( empty( $courses_access_from ) ) {
		$courses_access_from = learndash_user_group_enrolled_to_course_from( $user_id, $course_id );
	}

	$visible_after = learndash_get_setting( $lesson_id, 'visible_after' );
	if ( $visible_after > 0 ) {
		
		// Adjust the Course acces from by the number of days. Use abs() to ensure no negative days.
		$lesson_access_from = $courses_access_from + abs($visible_after) * 24 * 60 * 60;
		$lesson_access_from = apply_filters( 'ld_lesson_access_from__visible_after', $lesson_access_from, $lesson_id, $user_id );

		$current_timestamp = time();
		if ( $current_timestamp < $lesson_access_from ) {
			$return = $lesson_access_from;
		}		

	} else {
		$visible_after_specific_date = learndash_get_setting( $lesson_id, 'visible_after_specific_date' );
		if ( !empty( $visible_after_specific_date ) ) {
			if ( !is_numeric( $visible_after_specific_date ) ) {
				// If we a non-numberic value like a date stamp Y-m-d hh:mm:ss we want to convert it to a GMT timestamp
				$visible_after_specific_date = learndash_get_timestamp_from_date_string( $visible_after_specific_date, true );
			} 

			$current_time = time();
			
			if ( $current_time < $visible_after_specific_date ) {
				$return = apply_filters( 'ld_lesson_access_from__visible_after_specific_date', $visible_after_specific_date, $lesson_id, $user_id );
			}
		}
	}

	return apply_filters( 'ld_lesson_access_from', $return, $lesson_id, $user_id );
}



/**
 * Display when lesson will be available
 *
 * @since 2.1.0
 * 
 * @param  string $content content of lesson
 * @param  object $post    WP_Post object
 * @return string          when lesson will be available
 */
function lesson_visible_after( $content, $post ) {	
	if ( empty( $post->post_type ) ) {
		return $content; 
	}

	if ( $post->post_type == 'sfwd-lessons' ) {
		$lesson_id = $post->ID; 
	} else {
		if ( $post->post_type == 'sfwd-topic' || $post->post_type == 'sfwd-quiz' ) {
			$lesson_id = learndash_get_setting( $post, 'lesson' );	
			if ( empty( $lesson_id ) ) {
				return $content; 
			}
		} else {
			return $content; 
		}
	}

	$lesson_access_from = ld_lesson_access_from( $lesson_id, get_current_user_id() );
	if ( empty( $lesson_access_from) ) {
		return $content; 
	} else {
		$content = SFWD_LMS::get_template( 
			'learndash_course_lesson_not_available', 
			array(
				'user_id'					=>	get_current_user_id(),
				'course_id'					=>	learndash_get_course_id( $lesson_id ),
				'lesson_id'					=>	$lesson_id,
				'lesson_access_from_int'	=>	$lesson_access_from,
				'lesson_access_from_date'	=>	learndash_adjust_date_time_display( $lesson_access_from ),
				'context'					=>	'lesson'
			), false
		);
		return $content;
	}

	return $content;
}

add_filter( 'learndash_content', 'lesson_visible_after', 1, 2 );



/**
 * Is users course prerequisites completed for a given course
 *
 * @since 2.1.0
 * 
 * @param  int  	$id  course id
 * @return boolean 
 */
function is_course_prerequities_completed( $post_id = 0 ) {
	$course_pre_complete = true;
	
	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( ( !empty( $course_id ) ) && ( learndash_get_course_prerequisite_enabled( $course_id ) ) ) {
		
			$course_pre = learndash_get_course_prerequisites( $course_id );
			if ( ! empty( $course_pre ) ) {
				$course_pre_compare = learndash_get_course_prerequisite_compare( $course_id );
				if ( $course_pre_compare == 'ANY' ) {
					$s_pre = array_search( true, $course_pre );
					if ( $s_pre !== false ) 
						$course_pre_complete = true;
					else
						$course_pre_complete = false;
				
				} else if ( $course_pre_compare == 'ALL' ) {
					$s_pre = array_search( false, $course_pre );
					if ( array_search( false, $course_pre ) === false ) 
						$course_pre_complete = true;
					else
						$course_pre_complete = false;
				}
			}
		}
	}

	return $course_pre_complete;
}

/**
 * Given a course ID will return an array of the prereq item and the status 
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return array 
 */
function learndash_get_course_prerequisites( $post_id = 0 ) {
	$courses_status_array = array();

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( ( !empty( $course_id ) ) && ( learndash_get_course_prerequisite_enabled( $course_id ) ) ) {
		
			$course_pre = learndash_get_course_prerequisite( $course_id );
			if ( ! empty( $course_pre ) ) {
				$course_pre_compare = learndash_get_course_prerequisite_compare( $course_id );
			
				if ( is_string( $course_pre ) ) $course_pre = array( $course_pre );
			
				foreach( $course_pre as $c_id ) {
					//Now check if the prerequities course is completed by user or not
					$course_status = learndash_course_status( $c_id, null );
					if ( $course_status == __( 'Completed','learndash' ) ) { 
						$courses_status_array[$c_id] = true;
					} else {
						$courses_status_array[$c_id] = false;
					}
				}
			}
		}
	}
	return $courses_status_array;
}

/**
 * Get list of course prerequisites for a given course
 *
 * @since 2.1.0
 * 
 * @param  int 	 $id  course id
 * @return array      list of courses
 */
function learndash_get_course_prerequisite( $course_id = 0 ) {
	//$id = learndash_get_course_id( $id );
	//$post_options = get_post_meta( $id, '_sfwd-courses', true );
	//$course_pre = isset( $post_options['sfwd-courses_course_prerequisite'] ) ? $post_options['sfwd-courses_course_prerequisite'] : array();
	$course_pre = learndash_get_setting( $course_id, 'course_prerequisite' );
	if ( empty( $course_pre ) ) $course_pre = array();
	
	return $course_pre;
}

/**
 * Given a course ID will return true or false if prereq is enabled
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return bool 	true is prereq is enabled false if not 
 */
function learndash_get_course_prerequisite_enabled( $course_id ) {
	$course_pre_enabled = false;
	
	$course_id = learndash_get_course_id( $course_id );
	if (!empty( $course_id ) ) {
		$post_options = get_post_meta( $course_id, '_sfwd-courses', true );

		if ( ( isset( $post_options['sfwd-courses_course_prerequisite_enabled'] ) ) && ( $post_options['sfwd-courses_course_prerequisite_enabled'] == 'on' ) ) {
			$course_pre_enabled = true;
		} else if ( !isset( $post_options['sfwd-courses_course_prerequisite_enabled'] ) ) {
			// If the 'course_prerequisite_enabled' setting is not found we check the 'sfwd-courses_course_prerequisite'
			if ( ( isset( $post_options['sfwd-courses_course_prerequisite'] ) ) && ( !empty( $post_options['sfwd-courses_course_prerequisite'] ) ) ) {
				$course_pre_enabled = true;
				$post_options['sfwd-courses_course_prerequisite_enabled'] = 'on';
			} else {
				$post_options['sfwd-courses_course_prerequisite_enabled'] = '';
			}
			update_post_meta( $course_id, '_sfwd-courses', $post_options );
		}
	}
	
	return $course_pre_enabled;
}

/**
 * Given a course ID will return the compare value 'ALL' or 'ANY' (default)
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return string 	'ALL' or 'ANY' default
 */
function learndash_get_course_prerequisite_compare( $post_id ) {

	$course_pre_compare = 'ANY';

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( !empty( $course_id ) ) {
			$course_prerequisite_compare = learndash_get_setting( $course_id, 'course_prerequisite_compare' );
			if ( ( $course_prerequisite_compare == 'ANY') || ( $course_prerequisite_compare == 'ALL' ) )  {
				$course_pre_compare = $course_prerequisite_compare;
			}
		}
	}
	return $course_pre_compare;
}

/**
 * Given a course ID will return true or false if course points  enabled
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return bool 	true is prereq is enabled false if not 
 */
function learndash_get_course_points_enabled( $post_id = 0 ) {
	$course_points_enabled = false;

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( !empty( $course_id ) ) {
			$course_points_enabled = learndash_get_setting( $course_id, 'course_points_enabled' );
			if ( $course_points_enabled == 'on' )
				$course_points_enabled = true;
		}
	}
	
	return $course_points_enabled;
}

/**
 * Given a course ID will return the course points
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return bool 	false - course points not enabled, int 0 or greater course points
 */
function learndash_get_course_points( $post_id = 0 ) {
	$course_points = false;

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( !empty( $course_id ) ) {
			if ( learndash_get_course_points_enabled( $course_id ) ) {
				$course_points = 0;
		
				$course_points = learndash_get_setting( $course_id, 'course_points' );
			}
		}
	}
	
	return intval( $course_points );
}

/**
 * Given a course ID will return the course points for access
 *
 * @since 2.4.0
 * 
 * @param  int  	$id  course id
 * @return bool 	false - course point not enabled, int 0 or greater access points
 */
function learndash_get_course_points_access( $post_id = 0 ) {
	$course_points_access = false;

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( !empty( $course_id ) ) {
			if ( learndash_get_course_points_enabled( $course_id ) ) {
				$course_points_access = 0;

				$course_points_access = learndash_format_course_points( learndash_get_setting( $course_id, 'course_points_access' ) );
			}
		}
	}
	
	return $course_points_access;
}

function learndash_check_user_course_points_access( $post_id, $user_id = 0 ) {
	$user_can_access = true;

	if ( empty( $user_id ) ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			return false;
		}
	}

	if ( !empty( $post_id ) ) {
		$course_id = learndash_get_course_id( $post_id );
		if ( ( !empty( $course_id ) ) && ( !empty( $user_id ) ) ) {
			if ( learndash_get_course_points_enabled( $course_id ) ) {
				$course_access_points = learndash_get_course_points_access( $course_id );

				if ( !empty( $course_access_points ) ) {
					$user_course_points = learndash_get_user_course_points( $user_id );
					
					if ( floatval( $user_course_points ) >= floatval( $course_access_points ) ) 
						return true;
					else
					    return false;
				}
			}
		}
	}
	
	return true;
}

/**
 * Handles actions to be made when user joins a course
 *
 * Redirects user to login url, adds course access to user
 * 
 * @since 2.1.0
 */
function learndash_process_course_join(){
	if ( ( ! isset( $_POST['course_join'] ) ) || ( ! isset( $_POST['course_id'] ) ) )  {
		return;
	}

	$user_id = get_current_user_id();
	$course_id = intval( $_POST['course_id'] );

	if ( empty( $user_id ) ) {
		$redirect_url = get_permalink( $course_id );
		//$redirect_url = add_query_arg('course_join', $_POST['course_join'], $redirect_url );
		//$redirect_url = add_query_arg('course_id', $course_id, $redirect_url );
		$login_url = wp_login_url( $redirect_url );
		
		 /**
		 * Filter URL of where user should be redirected to
		 * 
		 * @since 2.1.0
		 * 
		 * @param  login_url  $login_url
		 */
		$login_url = apply_filters( 'learndash_course_join_redirect', $login_url, $course_id );
		wp_redirect( $login_url );
		exit;
	}
	
	/**
	 * Verify the form is valid
	 * @since 2.2.1.2
	 */
	if ( !wp_verify_nonce( $_POST['course_join'], 'course_join_'. $user_id .'_'. $course_id ) ) {
		return;
	}
	
	$meta = get_post_meta( $course_id, '_sfwd-courses', true );

	if ( @$meta['sfwd-courses_course_price_type'] == 'free' || @$meta['sfwd-courses_course_price_type'] == 'paynow' && empty( $meta['sfwd-courses_course_price'] ) && ! empty( $meta['sfwd-courses_course_join'] ) || sfwd_lms_has_access( $course_id, $user_id ) ) {
		ld_update_course_access( $user_id, $course_id );
	}
}

add_action( 'wp', 'learndash_process_course_join' );

/*
global $learndash_after_login;
//$learndash_after_login = false;
function learndash_wp_login_process_course_join( $user_login = '', $user = '' ) {
	if ( !empty( $user_login ) ) {
		if ( !( $user instanceof WP_User ) ) {
			$user = get_user_by('login', $user_login );
		}

		if ( $user instanceof WP_User ) {
			global $learndash_after_login;
            $learndash_after_login = true;
		}
	}
}
add_action('wp_login', 'learndash_wp_login_process_course_join', 99, 2);
*/
/*
function learndash_course_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	global $learndash_after_login;

	if ( $learndash_after_login ) {
		if ( ( isset( $redirect_to ) ) && ( !empty( $redirect_to ) ) ) {
			$url = parse_url( $redirect_to );
			if ( ( isset( $url['query'] ) ) && ( !empty( $url['query'] ) ) ) {
				parse_str( $url['query'], $url_elements );

				if ( ( isset( $url_elements['course_id'] ) ) && ( !empty( $url_elements['course_id'] ) ) && ( isset( $url_elements['course_join'] ) ) && ( !empty( $url_elements['course_join'] ) ) ) {

					// sort of a hack. If we are here then the user clicked on a Course 'Take This Course' form button. At the time the user was not known to WP which means
					// the nonce used in the form will be different than a nonce for an authentcated user. So we need to reseed the nonce so when we get to the form processing
					// in learndash_process_course_join() it will verify. 
	
					$redirect_to = add_query_arg( 'course_join', wp_create_nonce( 'course_join_'. $user->ID .'_'. $url_elements['course_id'] ), $redirect_to );
				}
			}
		} 
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'learndash_course_login_redirect', 10, 3 );
*/

/**
 * Shortcode to output course content
 *
 * @since 2.1.0
 * 
 * @param  array 	$atts 	shortcode attributes
 * @return string       	output of shortcode
 */
function learndash_course_content_shortcode( $atts ) {
	
	global $learndash_shortcode_used;
	
	if ( empty( $atts['course_id'] ) ) {
		return '';
	}

	$course_id = $atts['course_id'];

	$course = $post = get_post( $course_id );

	if ( ! is_singular() || $post->post_type != 'sfwd-courses' ) {
		return '';
	}

	$current_user = wp_get_current_user();

	$user_id = $current_user->ID;
	$logged_in = ! empty( $user_id );
	$lesson_progression_enabled = false;

	$course_settings = learndash_get_setting( $course );
	$lesson_progression_enabled  = learndash_lesson_progression_enabled();
	$courses_options = learndash_get_option( 'sfwd-courses' );
	$lessons_options = learndash_get_option( 'sfwd-lessons' );
	$quizzes_options = learndash_get_option( 'sfwd-quiz' );
	$course_status = learndash_course_status( $course_id, null );
	$has_access = sfwd_lms_has_access( $course_id, $user_id );

	$lessons = learndash_get_course_lessons_list( $course );
	$quizzes = learndash_get_course_quiz_list( $course );
	$has_course_content = ( ! empty( $lessons ) || ! empty( $quizzes ) );

	$has_topics = false;

	if ( ! empty( $lessons) ) {
		foreach ( $lessons as $lesson ) {
			$lesson_topics[ $lesson['post']->ID ] = learndash_topic_dots( $lesson['post']->ID, false, 'array' );
			if ( ! empty( $lesson_topics[ $lesson['post']->ID ] ) ) {
				$has_topics = true;
			}
		}
	}

	$level = ob_get_level();
	ob_start();
	include( SFWD_LMS::get_template( 'course_content_shortcode', null, null, true ) );
	$content = learndash_ob_get_clean( $level );
	$content = str_replace( array("\n", "\r"), ' ', $content );
	$user_has_access = $has_access? 'user_has_access':'user_has_no_access';

	$learndash_shortcode_used = true;
	
	/**
	 * Filter course content shortcode
	 * 
	 * @since 2.1.0
	 */
	return '<div class="learndash '.$user_has_access.'" id="learndash_post_'.$course_id.'">'.apply_filters( 'learndash_content', $content, $post ).'</div>';
}

add_shortcode( 'course_content', 'learndash_course_content_shortcode' );


function learndash_update_user_activity( $args = array() ) {

	//error_log(__FUNCTION__ .': args<pre>'. print_r($args, true) .'</pre>');
	
	global $wpdb;

	$default_args = array(
		// Can be passed in if we are updating a specific existing activity row.
		'activity_id'						=>	0,
		
		// Required. This is the ID of the Course, Lesson, Topic, Quiz item. Unique key part 1/3
		'post_id'							=>	0,
		
		// Optional. Will use get_current_user_id() if left 0. Unique key part 2/3
		'user_id'							=>	0,

		// Will be the token stats that described the status_times array (next argument) Can be most anything. 
		// From 'course', 'lesson', 'topic', 'access' or 'expired'. Unique key part 3/3.
		'activity_type'						=>	'',

		// true if the lesson, topic, course, quiz is complete. False if not complete. null if not started
		'activity_status'					=>	'',

		// Should be the timstamp when the 'status' started
		'activity_started'					=>	'',

		// Should be the timstamp when the 'status' completed
		'activity_completed'				=>	'',

		// Should be the timstamp when the activity record was last updated. Used as a sort column for ProPanel and other queries
		'activity_updated'					=>	'',
		
		// Flag to indicate what we are 'update', 'insert', 'delete'. The default action 'update' will cause this function
		// to check for an existing record to update (if found) 
		'activity_action'					=>	'update',	
		
		'activity_meta'						=>	''
	);
	
	$args = wp_parse_args( $args, $default_args );
	if ( empty( $args['activity_id'] ) ) {
		if ( ( empty( $args['post_id'] ) ) || ( empty( $args['activity_type'] ) ) ) {
			//error_log('ABORT #1');
			return;
		}
	}
	
	if ( empty( $args['user_id'] ) ) {
		// If we don't have a user_id passed via args
		if ( !is_user_logged_in() ) 
			return; // If not logged in, abort
		 
		// Else use the logged in user ID as the args user_id 
		$args['user_id'] = get_current_user_id();
	} 
	
	// End of args processing. Finally after we have applied all the logic we go out for filters. 
	$args = apply_filters('learndash_update_user_activity_args', $args);
	if ( empty( $args ) ) return;
	
	$values_array = array(
		'user_id' 			=> 	$args['user_id'], 
		'post_id' 			=> 	$args['post_id'],
		'activity_type'		=>	$args['activity_type'],
	);
	
	$types_array = array(
		'%d', // user_id
		'%d', // post_id
		'%s', // activity_type
	);

	if ( ( $args['activity_status'] === true ) || ( $args['activity_status'] === false ) ) {
		$values_array['activity_status'] = $args['activity_status'];
		$types_array[] = '%d';
	}
	
	//if ( ( $args['activity_status'] == true ) && ( !empty( $args['activity_completed'] ) ) ) {
	if ( $args['activity_completed'] !== '' ) {
		$values_array['activity_completed'] = $args['activity_completed'];
		$types_array[] = '%d';
	}

	if ( $args['activity_started'] !== '' ) {
		$values_array['activity_started'] = $args['activity_started'];
		$types_array[] = '%d';
	}

	if ( $args['activity_updated'] !== '' ) {
		$values_array['activity_updated'] = $args['activity_updated'];
		$types_array[] = '%d';
	} else {
		if ( ( empty( $args['activity_started'] ) ) && ( empty( $args['activity_completed'] ) ) ) {
			$values_array['activity_updated'] = time();
			$types_array[] = '%d';
		} else if ( $args['activity_started'] == $args['activity_completed'] ) {
			$values_array['activity_updated'] = $args['activity_completed'];
			$types_array[] = '%d';
		} else {
			if ( $args['activity_started'] > $args['activity_completed'] ) {
				$values_array['activity_updated'] = $args['activity_started'];
				$types_array[] = '%d';
			} else if ( $args['activity_completed'] > $args['activity_started'] ) {
				$values_array['activity_updated'] = $args['activity_completed'];
				$types_array[] = '%d';
			}
		}
	}
		
	$update_ret = false;
	
	if ( $args['activity_action'] == 'update' ) {

		if ( empty( $args['activity_id'] ) ) {
			$activity = learndash_get_user_activity( $args );
			if ( null !== $activity ) {
			
				$args['activity_id'] = $activity->activity_id;
			}
		}
		
		if ( !empty( $args['activity_id'] ) ) {
			
			$update_values_array = $values_array;
			$update_types_array = $types_array;

			$update_ret = $wpdb->update( 
				$wpdb->prefix. 'learndash_user_activity', 
				$update_values_array,
				array(
					'activity_id' => $args['activity_id']
				),
				$update_types_array,
				array( 
					'%d' // activity_id
				)
			);
			
		} else {
			$args['activity_action'] = 'insert';
		}
	}
	
	if ( $args['activity_action'] == 'insert' ) {
				
		$insert_ret = $wpdb->insert( 
			$wpdb->prefix. 'learndash_user_activity', 
			$values_array,
			$types_array
		);
		
		if ( $insert_ret !== false) {
			$args['activity_id'] = $wpdb->insert_id;
		}
	}

	// Finally for the course we update the activity meta
	if ( ( !empty( $args['activity_id'] ) ) && ( !empty( $args['activity_meta'] ) ) )  {
		foreach( $args['activity_meta'] as $meta_key => $meta_value ) {
			learndash_update_user_activity_meta( $args['activity_id'], $meta_key, $meta_value);
		}
	}

	return $args['activity_id'];
}

function learndash_get_user_activity( $args = array() ) {
	global $wpdb;
	
	if ( $args['activity_type'] == 'quiz' ) {
		$sql_str = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."learndash_user_activity WHERE user_id=%d AND post_id=%d AND activity_type=%s AND activity_started=%s LIMIT 1", $args['user_id'], $args['post_id'], $args['activity_type'], $args['activity_started'] );
	} else {
		$sql_str = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."learndash_user_activity WHERE user_id=%d AND post_id=%d AND activity_type=%s LIMIT 1", $args['user_id'], $args['post_id'], $args['activity_type'] );
	}
	//error_log('sql_str['. $sql_str .']');
	$activity = $wpdb->get_row( $sql_str );		
	if ( $activity ) {
		//error_log('activity<pre>'. print_r($activity, true) .'</pre>');
		if ( property_exists( $activity, 'activity_status' ) ) {
			if ( $activity->activity_status == true )
				$activity->activity_status = true;
			else if ( $activity->activity_status == false )
				$activity->activity_status = false;
		}
	}
	return $activity;	
}

function learndash_get_user_activity_meta( $activity_id = 0, $activity_meta_key = '', $return_activity_meta_value_only = true ) {

	global $wpdb;

	if ( empty( $activity_id ) )
		return;
	
	if ( !empty( $activity_meta_key ) ) {
	
		$meta_sql_str = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."learndash_user_activity_meta WHERE activity_id=%d AND activity_meta_key=%s", $activity_id, $activity_meta_key);
		$activity_meta = $wpdb->get_row( $meta_sql_str );
		if ( !empty($activity_meta ) ) {
			if ( $return_activity_meta_value_only == true ) {
				if ( property_exists( $activity_meta, 'activity_meta_value' ) ) {
					return $activity_meta->activity_meta_value;
				}
			} 
		}
		return $activity_meta;
	} else {
		// Here we return ALL meta for the given activity_id
		$meta_sql_str = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."learndash_user_activity_meta WHERE activity_id=%d", $activity_id);
		$activity_meta = $wpdb->get_results( $meta_sql_str );
	}
}

function learndash_update_user_activity_meta( $activity_id = 0, $meta_key = '', $meta_value = null) {
	global $wpdb;

	if ( ( empty( $activity_id ) ) || ( empty( $meta_key ) ) || ( $meta_value === null ) )
		return;
	
	$activity = learndash_get_user_activity_meta( $activity_id, $meta_key, false);	
	if ( null !== $activity ) {
		$wpdb->update( 
			$wpdb->prefix ."learndash_user_activity_meta",
			array(
				'activity_id'			=>	$activity_id,
				'activity_meta_key'		=>	$meta_key,
				'activity_meta_value'	=>	maybe_serialize( $meta_value )
			),
			array(
				'activity_meta_id'			=>	$activity->activity_meta_id
			),
			array(
				'%d',	// activity_id
				'%s',	// meta_key
				'%s'	// meta_value	
			),
			array(
				'%d'	// activity_meta_id
			)
		);
		
	} else {
		$wpdb->insert( 
			$wpdb->prefix ."learndash_user_activity_meta",
			array(
				'activity_id'			=>	$activity_id,
				'activity_meta_key'		=>	$meta_key,
				'activity_meta_value'	=>	maybe_serialize( $meta_value )
			),
			array(
				'%d',	// activity_id
				'%s',	// meta_key
				'%s'	// meta_value	
			)
		);
	}
}


/**
 * Utility function to return all the courses that are price_type: open 
 * Logic for this query was taken from the sfwd_lms_has_access_fn() function 
 * @since 2.3
 * 
 * @param  bool 	$bypass_transient 	Set to true to bypass transient cache. 
 * @return array    array of post_ids (course ids) found
 */
function learndash_get_open_courses( $bypass_transient = false ) {
	global $wpdb;
	
	$transient_key = "learndash_open_courses";

	if (!$bypass_transient) {
		$courses_ids_transient = learndash_get_valid_transient( $transient_key );
	} else {
		$courses_ids_transient = false;
	}
	
	if ( $courses_ids_transient === false ) {
	
		$sql_str = "SELECT postmeta.post_id as post_id FROM ". $wpdb->prefix ."postmeta as postmeta INNER JOIN ". $wpdb->prefix ."posts as posts ON posts.ID = postmeta.post_id WHERE posts.post_status='publish' AND postmeta.meta_key='_sfwd-courses' AND ( postmeta.meta_value REGEXP '\"sfwd-courses_course_price_type\";s:4:\"open\";' )";
		$course_ids = $wpdb->get_col( $sql_str );
	
		set_transient( $transient_key, $course_ids, MINUTE_IN_SECONDS );
	
	} else {
		$course_ids = $courses_ids_transient;
	}
	return $course_ids;
}

/**
 * Utility function to return all the courses that are price_type: paynow with empty price
 * Logic for this query was taken from the sfwd_lms_has_access_fn() function 
 * @since 2.3
 * 
 * @param  bool 	$bypass_transient 	Set to true to bypass transient cache. 
 * @return array    array of post_ids (course ids) found
 */
function learndash_get_paynow_courses( $bypass_transient = false ) {
	global $wpdb;
	
	$transient_key = "learndash_paynow_courses";

	if (!$bypass_transient) {
		$courses_ids_transient = learndash_get_valid_transient( $transient_key );
	} else {
		$courses_ids_transient = false;
	}
	
	if ( $courses_ids_transient === false ) {
	
		$sql_str = "SELECT postmeta.post_id FROM ". $wpdb->prefix ."postmeta as postmeta INNER JOIN ". $wpdb->prefix ."posts as posts ON posts.ID = postmeta.post_id WHERE posts.post_status='publish' AND postmeta.meta_key='_sfwd-courses' AND (( postmeta.meta_value REGEXP 's:30:\"sfwd-courses_course_price_type\";s:6:\"paynow\";' ) AND ( postmeta.meta_value REGEXP 's:25:\"sfwd-courses_course_price\";s:0:\"\";' ))";
		//error_log('sql_str['. $sql_str .']');
		$course_ids = $wpdb->get_col( $sql_str );
		set_transient( $transient_key, $course_ids, MINUTE_IN_SECONDS );
	
	} else {
		$course_ids = $courses_ids_transient;
	}
	return $course_ids;
}

// Gets ALL users that have access to given course_id.
// Optional bool flag to exclude admin roles
function learndash_get_users_for_course( $course_id = 0, $query_args = array(), $exclude_admin = true ) {
	$course_user_ids = array();
	
	if ( empty( $course_id ) ) return $course_user_ids;

	$defaults = array(
		// By default WP_User_Query will return ALL users. Strange.
		'fields'		=>	'ID',
	);
	
	$query_args = wp_parse_args( $query_args, $defaults );
	
	if ( $exclude_admin == true ) {
		$query_args['role__not_in'] = array('administrator');
	}
	
	$course_price_type = get_course_meta_setting( $course_id, 'course_price_type' );
	
	if ($course_price_type == 'open') {
		
		$user_query = new WP_User_Query( $query_args );
		return $user_query;
		
	} else {
	
		$course_access_list = get_course_meta_setting( $course_id, 'course_access_list');
		$course_user_ids = array_merge( $course_user_ids, $course_access_list );

		$course_access_users = get_course_users_access_from_meta( $course_id );
		$course_user_ids = array_merge( $course_user_ids, $course_access_users );
		
		$course_groups_users = get_course_groups_users_access( $course_id );
		$course_user_ids = array_merge( $course_user_ids, $course_groups_users );

		if ( !empty( $course_user_ids ) )
			$course_user_ids = array_unique( $course_user_ids );

		$course_expired_access_users = get_course_expired_access_from_meta( $course_id );
		if ( !empty( $course_expired_access_users ) )
			$course_user_ids = array_diff( $course_access_list, $course_expired_access_users );

		if ( !empty( $course_user_ids ) ) {
			$query_args['include'] = $course_user_ids;
			
			$user_query = new WP_User_Query( $query_args );
			
			//$course_user_ids = $user_query->get_results();
			return $user_query;
		}
	}
	
	/*
	if ( !empty( $course_user_ids ) ) {
		
		// Finally we spin through this list of user_ids and check for expired access. 
		$course_expire_access = get_course_meta_setting( $course_id, 'expire_access' );
		if ( !empty( $course_expire_access ) ) {
		
			$expired_user_ids = array();
			foreach( $course_user_ids as $user_id ) {
				if ( ld_course_access_expired( $course_id, $user_id ) )
					$expired_user_ids[] = $user_id;
				
			}
			
			if ( !empty( $expired_user_ids ) ) {
				$course_user_ids = array_diff( $course_user_ids, $expired_user_ids );
			}
		}
	}
	*/
		
	return $course_user_ids;
}

// Get all users with explicit 'course_XX_access_from' access
function get_course_users_access_from_meta( $course_id = 0 ) {
	global $wpdb;
	
	$course_user_ids = array();
	
	if ( !empty( $course_id ) ) {
		// We have to do it this was because WP_User_Query cannot handle on meta EXISTS and another 'NOT EXISTS' in the same query. 
		$sql_str = $wpdb->prepare( "SELECT user_id FROM ". $wpdb->usermeta ." as usermeta WHERE meta_key = %s", 'course_'. $course_id .'_access_from');
	
		$course_user_ids = $wpdb->get_col( $sql_str );
	}
	return $course_user_ids;
}

// Get all the users for a given course_id that have 'learndash_course_expired_XX' user meta records. 
function get_course_expired_access_from_meta( $couese_id = 0 ) {
	global $wpdb;
	
	$expired_user_ids = array();
	
	if ( !empty( $course_id ) ) {
		$sql_str = $wpdb->prepare( "SELECT user_id FROM ". $wpdb->usermeta ." as usermeta WHERE meta_key = %s", 'learndash_course_expired_'. $course_id);
	
		$expired_user_ids = $wpdb->get_col( $sql_str );
	}
	
	return $expired_user_ids;
}


// Utility function to att the course settings in meta. Better than having this over inline over and over again. 
// @TODO Need to convert all references to get_post_meta for '_sfwd-courses' to use this function.
function get_course_meta_setting( $course_id = 0, $setting_key = '' ) {
	$course_settings = array();
	
	if ( empty( $course_id ) ) return $course_settings;
	
	$meta = get_post_meta( $course_id, '_sfwd-courses', true );
	
	// we only want/need to reformat the access list of we are returning ALL setting or just the access list
	if ( ( empty( $setting_key ) ) || ( $setting_key == 'course_access_list' ) ) {
		if ( !isset( $meta['sfwd-courses_course_access_list'] ) ) {
			$meta['sfwd-courses_course_access_list'] = '';
		}
		$meta['sfwd-courses_course_access_list'] = array_map( 'intVal', explode( ',', $meta['sfwd-courses_course_access_list'] ) );
				
		// Need to remove the empty '0' items
		$meta['sfwd-courses_course_access_list'] = array_diff($meta['sfwd-courses_course_access_list'], array(0, ''));
	}

	if ( empty( $setting_key ) ) {
		return $meta;
	} else if ( isset( $meta['sfwd-courses_'. $setting_key] ) ) {
		return $meta['sfwd-courses_'. $setting_key];
	}
}

function learndash_get_course_steps_ORG( $course_id = 0, $include_post_types = array( 'sfwd-lessons', 'sfwd-topic' ) ) {
	$steps = array();
	
	if ( ( !empty( $course_id ) ) && ( !empty( $include_post_types) ) ) {
	
		$steps_query_args = array(
			'post_type' 		=> $include_post_types, 
			'posts_per_page' 	=> 	-1, 
			'post_status' 		=> 	'publish',
			'fields'			=>	'ids',
			'meta_query' 		=> 	array(
										array(
											'key'     	=> 'course_id',
											'value'   	=> intval($course_id),
											'compare' 	=> '=',
											'type'		=>	'NUMERIC'
										)
									)
		);

		//error_log('steps_query_args<pre>'. print_r($steps_query_args, true) .'</pre>');
		
		$steps_query = new WP_Query( $steps_query_args );
		if ($steps_query->have_posts())
			$steps = $steps_query->posts;
	}
	
	return $steps;
}

// Get the total number of Lessons + Topics for a given course_id. For now excludes quizzes at lesson and topic level. 
function learndash_get_course_steps( $course_id = 0, $include_post_types = array( 'sfwd-lessons', 'sfwd-topic' ) ) {

	// The steps array will hold all the individual step counts for each post_type.
	$steps = array();
	
	// This will hold the combined steps post ids once we have run all queries. 
	$steps_all = array();
	
	if ( !empty( $course_id ) ) {
		// Just a loop to initialize each post_type set
		foreach( $include_post_types as $post_type ) {
			$steps[$post_type] = array();
		}

		if ( ( in_array( 'sfwd-lessons', $include_post_types ) ) || ( in_array( 'sfwd-topic', $include_post_types ) ) ) {
			$lesson_steps_query_args = array(
				'post_type' 		=> 'sfwd-lessons',
				'posts_per_page' 	=> 	-1,
				'post_status' 		=> 	'publish',
				'fields'			=>	'ids',
				'meta_query' 		=> 	array(
					array(
						'key'     	=> 'course_id',
						'value'   	=> intval($course_id),
						'compare' 	=> '=',
						'type'		=>	'NUMERIC'
					)
				)
			);

			$lesson_steps_query = new WP_Query( $lesson_steps_query_args );
			if ($lesson_steps_query->have_posts()) {
				$steps['sfwd-lessons'] = $lesson_steps_query->posts;
			}
		} 

		// For Topics we still require the parent lessons items
		if ( ( in_array( 'sfwd-topic', $include_post_types ) ) && ( !empty( $steps['sfwd-lessons'] ) ) ) {
			$topic_steps_query_args = array(
				'post_type' 		=> 'sfwd-topic',
				'posts_per_page' 	=> 	-1,
				'post_status' 		=> 	'publish',
				'fields'			=>	'ids',
				'meta_query' 		=> 	array(
					array(
						'key'     	=> 'course_id',
						'value'   	=> intval($course_id),
						'compare' 	=> '=',
						'type'		=>	'NUMERIC'
					)
				)
			);

			if ( ( isset( $steps['sfwd-lessons'] ) ) && ( !empty( $steps['sfwd-lessons'] ) ) ) {
				$topic_steps_query_args['meta_query'][] = array(
					'key'     	=> 'lesson_id',
					'value'   	=> $steps['sfwd-lessons'],
					'compare' 	=> 'IN',
					'type'		=>	'NUMERIC'
				);
			}

			//error_log( 'topic_steps_query_args<pre>'. print_r($topic_steps_query_args, true) .'</pre>' );
			$topic_steps_query = new WP_Query( $topic_steps_query_args );
			if ($topic_steps_query->have_posts()) {
				$steps['sfwd-topic'] = $topic_steps_query->posts;
			}
		} 
	}
	
	foreach( $include_post_types as $post_type ) {
		if ( ( isset( $steps[$post_type] ) ) && ( !empty( $steps[$post_type] ) ) ) {
			$steps_all = array_merge( $steps_all, $steps[$post_type] );
		}
	}
	
	return $steps_all;
}

function learndash_get_course_steps_count( $course_id = 0 ) {

	$course_steps_count = 0;
	$course_steps = learndash_get_course_steps( $course_id );
	if ( !empty( $course_steps ) )
		$course_steps_count = count( $course_steps );

	if ( has_global_quizzes( $course_id ) )
		$course_steps_count += 1;	
		
	return $course_steps_count;
}

// Get total completed steps for a given course_progress array structure. 
function learndash_course_get_completed_steps( $user_id = 0, $course_id = 0, $course_progress = array() ) {
	$steps_completed_count = 0;

	if ( ( !empty( $user_id ) ) && ( !empty( $course_id ) ) ) {
		
		if ( empty( $course_progress ) ) {
			$course_progress_all = get_user_meta( $user_id, '_sfwd-course_progress', true );
			if ( isset( $course_progress_all[$course_id] ) ) $course_progress = $course_progress_all[$course_id];
		}

		if ( isset( $course_progress['lessons'] ) ) {
			foreach( $course_progress['lessons'] as $lesson_id => $lesson_completed ) {

				$steps_completed_count += intval($lesson_completed);
			}
		}
		
		if ( isset( $course_progress['topics'] ) ) {
			foreach( $course_progress['topics'] as $lesson_id => $lesson_topics ) {
				if ( ( is_array( $lesson_topics ) ) && ( !empty( $lesson_topics ) ) ) {
					foreach( $lesson_topics as $topic_id => $topic_completed ) {
						$steps_completed_count += intval($topic_completed);
					}
				}
			}
		}

		if ( has_global_quizzes( $course_id ) ) {
			if ( is_all_global_quizzes_complete( $user_id, $course_id ) ) {
				$steps_completed_count += 1;
			} 
		}
	}
	
	return $steps_completed_count;
}

add_filter('sfwd-courses_display_options', function( $options, $location ) {
	if ( ( !isset( $options[$location.'_course_prerequisite_enabled'] ) ) || ( empty( $options[$location.'_course_prerequisite_enabled'] ) )) {
		global $post;
		if ( $post instanceof WP_Post ) {
			$settings = get_post_meta( $post->ID, '_sfwd-courses', true);
			
			if ( ( isset( $settings[$location .'_course_prerequisite'] ) ) && ( !empty( $settings[$location .'_course_prerequisite'] ) ) ) {
				$options[$location.'_course_prerequisite_enabled'] = 'on';
				$settings[$location.'_course_prerequisite_enabled'] = 'on';
				update_post_meta( $post->ID, '_sfwd-courses', $settings);
			}
		}
	}
	
	return $options;
}, 1, 2);

function learndash_update_course_users_groups( $user_id, $course_id, $access_list, $remove ) {
	if ( ( !empty( $user_id ) ) && ( !empty( $course_id ) ) ) {
		
		$course_groups = learndash_get_course_groups( $course_id, true );
		if ( !empty( $course_groups ) ) {
			foreach( $course_groups as $course_group_id ) {
				$ld_auto_enroll_group_courses = get_post_meta( $course_group_id, 'ld_auto_enroll_group_courses', true );
				if ( $ld_auto_enroll_group_courses == 'yes' ) {
					ld_update_group_access( $user_id, $course_group_id );
				}
			}
		}
	}
}
add_action( 'learndash_update_course_access', 'learndash_update_course_users_groups', 50, 4 );


