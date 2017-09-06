<?php

if (!class_exists('Learndash_Admin_User_Profile_Edit')) {
	class Learndash_Admin_User_Profile_Edit {
		
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-profile.php', 		array( $this, 'on_load_user_profile') );
			add_action( 'load-user-edit.php', 		array( $this, 'on_load_user_profile') );

			add_action( 'show_user_profile', 		array( $this, 'show_user_profile') );
			add_action( 'edit_user_profile', 		array( $this, 'show_user_profile') );

			add_action( 'personal_options_update',  array( $this, 'save_user_profile' ), 1 );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ), 1 );

		}
		
		function on_load_user_profile() {
			global $learndash_assets_loaded;
			
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


			wp_enqueue_script( 
				'learndash-admin-binary-selector-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ),
				LEARNDASH_VERSION,
				true
			);
			$learndash_assets_loaded['scripts']['learndash-admin-binary-selector-script'] = __FUNCTION__;

			wp_enqueue_script( 
				'sfwd-module-script', 
				LEARNDASH_LMS_PLUGIN_URL . '/assets/js/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_VERSION,
				true 
			);
			$learndash_assets_loaded['scripts']['sfwd-module-script'] = __FUNCTION__;	

			$data = array();

			if ( ! empty( $this->script_data ) ) {
				$data = $this->script_data;
			}
			if ( !isset( $data['ajaxurl'] ) )
				$data['ajaxurl'] = admin_url('admin-ajax.php');

			$data = array( 'json' => json_encode( $data ) );
			wp_localize_script( 'sfwd-module-script', 'sfwd_data', $data );


			wp_enqueue_style( 
				'learndash-admin-binary-selector-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array( ),
				LEARNDASH_VERSION
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-style'] = __FUNCTION__;
			
			// We need to load the wpProQuiz_admin.js in order to show the quiz statistics on the profile. 
			//$WpProQuiz_Controller_Admin = new WpProQuiz_Controller_Admin();
			//$WpProQuiz_Controller_Admin->enqueueScript();
		}
		
		function show_user_profile( $user ) {
			$this->show_user_courses( $user );
			$this->show_user_groups( $user );
			$this->show_leader_groups( $user );
			
			//$user_couses = get_user_meta( $user->ID, '_sfwd-course_progress', true );
			//error_log( 'user_couses<pre>'. print_r( $user_couses, true ) .'</pre>' );

			//$user_quizzes = get_user_meta( $user->ID, '_sfwd-quizzes', true );
			//error_log( 'user_quizzes<pre>'. print_r( $user_quizzes, true ) .'</pre>' );
			
			//LD_QuizPro::showModalWindow();
		}
				
		function save_user_profile( $user_id ) {
			//if ( ! learndash_is_admin_user( ) ) {
			if ( !current_user_can( 'edit_users' ) ) {
				return;
			}
			
			if ( ( isset( $_POST['learndash_user_courses'] ) ) && ( isset( $_POST['learndash_user_courses'][$user_id] ) ) && ( !empty( $_POST['learndash_user_courses'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_courses-'. $user_id .'-nonce'], 'learndash_user_courses-'.$user_id )) {
						$user_courses = (array)json_decode( stripslashes( $_POST['learndash_user_courses'][$user_id] ) );
						learndash_user_set_enrolled_courses( $user_id, $user_courses );
					}
				}
			}

			if ( ( isset( $_POST['learndash_user_groups'] ) ) && ( isset( $_POST['learndash_user_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_user_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_groups-'. $user_id .'-nonce'], 'learndash_user_groups-'.$user_id )) {

						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_user_groups'][$user_id] ) );
						learndash_set_users_group_ids( $user_id, $user_groups );
					}
				}
			}

			if ( ( isset( $_POST['learndash_leader_groups'] ) ) && ( isset( $_POST['learndash_leader_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_leader_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_leader_groups-'. $user_id .'-nonce'], 'learndash_leader_groups-'.$user_id )) {
						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_leader_groups'][$user_id] ) );
						learndash_set_administrators_group_ids( $user_id, $user_groups );
					}
				}
			}
			
			if ( ( isset( $_POST['learndash_delete_quiz_user_lock_data'] ) ) && ( !empty( $_POST['learndash_delete_quiz_user_lock_data'] ) ) ) {
				if ( ( isset( $_POST['learndash_delete_quiz_user_lock_data-nonce'] ) ) && ( !empty( $_POST['learndash_delete_quiz_user_lock_data-nonce'] ) ) ) {
					if ( wp_verify_nonce( $_POST['learndash_delete_quiz_user_lock_data-nonce'], 'learndash_delete_quiz_user_lock_data-'. $user_id ) ) {
						learndash_remove_user_quiz_locks( $user_id, $_POST['learndash_delete_quiz_user_lock_data'] );
					} 
				}
			}

			if ( isset( $_POST['learndash_course_points'] ) ) {
				update_user_meta($user_id, 'course_points', learndash_format_course_points( $_POST['learndash_course_points'] ) );
			}

			learndash_save_user_course_complete( $user_id );
		}
		
		function show_user_courses( $user ) {
			// First check is the user viewing the screen is admin...
			//if ( learndash_is_admin_user( ) ) {
			if ( current_user_can( 'edit_users' ) ) {
				// Then is the user profile being viewed is not admin
				if ( learndash_is_admin_user( $user->ID ) ) {
					
					/**
					 * See example if 'learndash_override_course_auto_enroll' filter 
					 * https://bitbucket.org/snippets/learndash/kon6y
					 *
					 * @since 2.3
					 */
					$course_autoenroll_admin = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
					if ( $course_autoenroll_admin == 'yes' ) $course_autoenroll_admin = true;
					else $course_autoenroll_admin = false;
					$course_autoenroll_admin_filtered = apply_filters('learndash_override_course_auto_enroll', $course_autoenroll_admin, $user->ID );
					
					if ( $course_autoenroll_admin_filtered ) {
						?>
						<h3><?php echo sprintf( _x('User Enrolled %s', 'User Enrolled Courses', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) )  ?></h3>
						<p><?php _e('Administrators are automatically enrolled in all Courses.', 'learndash') ?></p>
						<?php
						return;
					} 
				}
				
				$ld_binary_selector_user_courses = new Learndash_Binary_Selector_User_Courses(
					array(
						'user_id'				=>	$user->ID,
						'selected_ids'			=>	learndash_user_get_enrolled_courses( $user->ID, array(), true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_user_courses->show();
			}
		}

		function show_user_groups( $user ) {
			//if ( learndash_is_admin_user( ) ) {
			if ( current_user_can( 'edit_users' ) ) {
				
				$ld_binary_selector_user_groups = new Learndash_Binary_Selector_User_Groups(
					array(
						'user_id'				=>	$user->ID,
						'selected_ids'			=>	learndash_get_users_group_ids( $user->ID, true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_user_groups->show();
			}
		}

		function show_leader_groups( $user ) {
			//if ( learndash_is_admin_user() ) {
			if ( current_user_can( 'edit_users' ) ) {
				if ( learndash_is_group_leader_user( $user->ID ) ) {
					$ld_binary_selector_leader_groups = new Learndash_Binary_Selector_Leader_Groups(
						array(
							'user_id'				=>	$user->ID,
							'selected_ids'			=>	learndash_get_administrators_group_ids( $user->ID, true ),
							'search_posts_per_page' => 100
						)
					);
					$ld_binary_selector_leader_groups->show();
				}
			}
		}

		// End of functions
	}
}
