<?php

if (!class_exists('Learndash_Admin_Course_Edit')) {
	class Learndash_Admin_Course_Edit {
		
		private $courses_post_type = 'sfwd-courses';
	    
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-post.php', 			array( $this, 'on_load') );
			add_action( 'load-post-new.php', 		array( $this, 'on_load') );
		}
		
		function on_load() {
			global $learndash_assets_loaded, $typenow;	// Contains the same as $_GET['post_type]
			
			if ( (empty( $typenow ) ) || ( $typenow != $this->courses_post_type ) )  return;

			wp_enqueue_script( 
				'learndash-admin-binary-selector-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ),
				LEARNDASH_VERSION,
				true
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-script'] = __FUNCTION__;

			wp_enqueue_style( 
				'learndash-admin-binary-selector-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array( ),
				LEARNDASH_VERSION
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-style'] = __FUNCTION__;
		
			// Add Metabox and hook for saving post metabox
			add_action( 'add_meta_boxes', 			array( $this, 'add_metaboxes' ) );
			add_action( 'save_post', 				array( $this, 'save_metaboxes'), 20, 3 );

		}
		
		/**
		 * Register Groups meta box for admin
		 *
		 * Managed enrolled groups, users and group leaders
		 * 
		 * @since 2.1.2
		 */
		function add_metaboxes() {
			
			if ( ( isset( $_GET['post'] ) ) && ( !empty( $_GET['post'] ) ) ) {

				if ( ( defined( 'LEARNDASH_COURSE_BUILDER' ) ) && ( LEARNDASH_COURSE_BUILDER === true ) ) {
					$course_id = intval( $_GET['post'] );
					$course_price_type = learndash_get_setting( $course_id, 'course_price_type' );

					if ( $course_price_type !== 'open' ) {

						add_meta_box(
							'learndash_couse_users',
							sprintf( _x( 'LearnDash %s Users', 'LearnDash Course Users', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
							array( $this, 'course_users_page_box' ),
							$this->courses_post_type
						);
					}
				}
				
				/** 
				 * @since 2.3.1
				 * Check if we have defined groups before showing the meta box
				 */
				$group_query_args = array( 
					'post_type' 		=> 	'groups', 
					'post_status' 		=> 	'publish',  
					'posts_per_page' 	=> 	1,
				);
		
				$group_query = new WP_Query( $group_query_args );
				if ( ( $group_query instanceof WP_Query) && ( !empty( $group_query->posts ) ) ) {
		
					add_meta_box(
						'learndash_couse_groups',
						sprintf( _x( 'LearnDash %s Group', 'LearnDash Course Group', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						array( $this, 'course_groups_page_box' ),
						$this->courses_post_type
					);
				}
			}
		}


		/**
		 * Prints content for Groups meta box for admin
		 *
		 * @since 2.1.2
		 * 
		 * @param  object $post WP_Post
		 * @return string 		meta box HTML output
		 */
		function course_groups_page_box( $post ) {
			$course_id = $post->ID;

			// Use nonce for verification
			wp_nonce_field( 'learndash_course_groups_nonce_'. $course_id, 'learndash_course_groups_nonce' );
			
			?>
			<div id="learndash_course_groups_page_box" class="learndash_course_groups_page_box">
			<?php
				$ld_binary_selector_course_groups = new Learndash_Binary_Selector_Course_Groups(
					array(
						'course_id'		=>	$course_id,
						'selected_ids'	=>	learndash_get_course_groups( $course_id, true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_course_groups->show();
			?>
			</div>
			<?php 
		}

		/**
		 * Prints content for Groups meta box for admin
		 *
		 * @since 2.1.2
		 * 
		 * @param  object $post WP_Post
		 * @return string 		meta box HTML output
		 */
		function course_users_page_box( $post ) {
			$course_id = $post->ID;

			$user_ids = array();
			
			$course_users_query = learndash_get_users_for_course( $course_id );
			if ( $course_users_query instanceof WP_User_Query ) {	
				$user_ids = $course_users_query->get_results();
			}
			


			// Use nonce for verification
			wp_nonce_field( 'learndash_course_users_nonce_'. $course_id, 'learndash_course_users_nonce' );
			
			?>
			<div id="learndash_course_users_page_box" class="learndash_course_users_page_box">
			<?php
				$ld_binary_selector_course_users = new Learndash_Binary_Selector_Course_Users(
					array(
						'course_id'		=>	$course_id,
						'selected_ids'	=>	$user_ids,
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_course_users->show();
			?>
			</div>
			<?php 
		}


		function save_metaboxes( $post_id, $post, $update ) {

			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// If this is just a revision, don't send the email.
			if ( wp_is_post_revision( $post_id ) )
				return;
			
			$post_type = get_post_type( $post_id );	
			if (( empty( $post_type ) ) || ( $this->courses_post_type != $post_type )) {
				return;
			}
			
			// Check permissions
			if ( ! current_user_can( 'edit_courses', $post_id ) ) {
				return;
			}
			
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if ( ( isset( $_POST['learndash_course_groups_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_groups_nonce'], 'learndash_course_groups_nonce_'. $post_id ) ) ) {

				if ( ( isset( $_POST['learndash_course_groups'] ) ) && ( isset( $_POST['learndash_course_groups'][$post_id] ) ) && ( !empty( $_POST['learndash_course_groups'][$post_id] ) ) ) {
					$course_groups = (array)json_decode( stripslashes( $_POST['learndash_course_groups'][$post_id] ) );
					learndash_set_course_groups( $post_id, $course_groups );
				}
			}

			/*
			if ( ( isset( $_POST['learndash_course_users_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_users_nonce'], 'learndash_course_users_nonce_'. $post_id ) ) ) {

				if ( ( isset( $_POST['learndash_course_users'] ) ) && ( isset( $_POST['learndash_course_users'][$post_id] ) ) && ( !empty( $_POST['learndash_course_users'][$post_id] ) ) ) {
					$course_users = (array)json_decode( stripslashes( $_POST['learndash_course_users'][$post_id] ) );
					//update_post_meta( $post_id, 'learndash_course_users_groups', $course_users_groups );
				}
			}
			*/

		}
		// End of functions
	}
}
