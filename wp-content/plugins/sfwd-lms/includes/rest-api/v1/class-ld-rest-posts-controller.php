<?php
if ( !class_exists('LD_REST_Posts_Controller' ) ) {
	abstract class LD_REST_Posts_Controller extends WP_REST_Posts_Controller {

		protected $version = 'v1';

		public function __construct( $post_type = '' ) {
			parent::__construct( $post_type );
		}		

		public function get_item( $request ) {
			//error_log('in '. __FUNCTION__ );
			//error_log('request[id] ['. $request['id'] .']');
			
			$post = $this->get_post( $request['id'] );
			//error_log('post<pre>'. print_r($post, true) .'</pre>');
			
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$data     = $this->prepare_item_for_response( $post, $request );
			$response = rest_ensure_response( $data );

			if ( is_post_type_viewable( get_post_type_object( $post->post_type ) ) ) {
				$response->link_header( 'alternate',  get_permalink( $post->ID ), array( 'type' => 'text/html' ) );
			}

			return $response;
		}
		

		public function get_item_permissions_check( $request ) {
			//error_log('in '. __FUNCTION__ );
			
			$post = $this->get_post( $request['id'] );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
				return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit this post.' ), array( 'status' => rest_authorization_required_code() ) );
			}

			if ( $post && ! empty( $request['password'] ) ) {
				// Check post password, and return error if invalid.
				if ( ! hash_equals( $post->post_password, $request['password'] ) ) {
					return new WP_Error( 'rest_post_incorrect_password', __( 'Incorrect post password.' ), array( 'status' => 403 ) );
				}
			}

			// Allow access to all password protected posts if the context is edit.
			if ( 'edit' === $request['context'] ) {
				add_filter( 'post_password_required', '__return_false' );
			}

			//if ( $post ) {
			//	return $this->check_read_permission( $post );
			//}

			return true;
		}

		public function get_course_items( $request ) {
			global $learndash_post_types;
			
			error_log('in '. basename(__FILE__) .':'. __FUNCTION__ );
			
			//error_log('_GET<pre>'. print_r($_GET, true) .'</pre>');
			//error_log('post_type['. $this->post_type .']');
			//error_log('request<pre>'. print_r($request, true) .'</pre>');
			
			$args = array();
			
			if ( is_user_logged_in() )
				$current_user_id = get_current_user_id();
			else
				$current_user_id = 0;

			global $learndash_post_types;

			if ( in_array( $this->post_type, $learndash_post_types ) === false ) {	
				return new WP_Error( 'rest_post_invalid_id_2', __( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
			}
			
			error_log('post_type['. $this->post_type.']');
			
			switch( $this->post_type ) {
				case 'sfwd-courses':
					break;
				
				default:
					if ( empty( $current_user_id ) ) {
						return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.', 'learndash' ), array( 'status' => 401 ) );
					}
				
					$args['course_id'] = $request['course_id'];
					error_log('course_id['. $args['course_id'] .']');
					if ( empty( $args['course_id'] ) ) {
						return new WP_Error( 'rest_post_invalid_id_3', __( 'Invalid Course ID.' ), array( 'status' => 404 ) );
					}
					$course = get_post( $args['course_id'] );
					if ( is_wp_error( $course ) ) {
						return $course;
					}
					
					if ( $course->post_type !== 'sfwd-courses' ) {
						return new WP_Error( 'rest_post_invalid_id_3', __( 'Invalid Course ID.' ), array( 'status' => 404 ) );
					}
					
					$course_price_type = get_course_meta_setting( $args['course_id'], 'course_price_type' );
					
					$has_access = sfwd_lms_has_access( $args['course_id'], $current_user_id );
					if ( ( ! $has_access ) && ( $course_price_type != 'open' ) ) {
						return new WP_Error( 'rest_cannot_view', __( 'Sorry, you are not allowed view items.' ), array( 'status' => rest_authorization_required_code() ) );
					}

					if ( $this->post_type === 'sfwd-lessons' ) {
						
					} else if ( $this->post_type === 'sfwd-topic' ) {
						$args['lesson_id'] = $request['lesson_id'];
						error_log('lesson_id['. $lesson_id .']');
					} else if ( $this->post_type === 'sfwd-quiz' ) {
						$args['lesson_id'] = $request['lesson_id'];
						error_log('lesson_id['. $lesson_id .']');

						$args['topic_id'] = $request['topic_id'];
						error_log('topic_id['. $topic_id .']');
					}
					
					break;
			}
			
			$args['offset']               = $request['offset'];
			$args['order']                = $request['order'];
			$args['orderby']              = $request['orderby'];
			$args['paged']                = $request['page'];
			$args['post__in']             = $request['include'];
			$args['post__not_in']         = $request['exclude'];
			$args['posts_per_page']       = $request['per_page'];
			$args['name']                 = $request['slug'];
			$args['post_parent__in']      = $request['parent'];
			$args['post_parent__not_in']  = $request['parent_exclude'];
			$args['s']                    = $request['search'];

//			$request_params = $request->get_url_params();			
//			if ( ( isset( $request_params['course_id'] ) ) && ( !empty( $request_params['course_id'] ) ) )
//				$args['course_id'] = intval( $request_params['course_id'] );
//			else if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) )
//				$args['course_id'] = intval( $_GET['course_id'] );
//			
//			if ( ( isset( $request_params['lesson_id'] ) ) && ( !empty( $request_params['lesson_id'] ) ) )
//				$args['lesson_id'] = intval( $request_params['lesson_id'] );
//			else if ( ( isset( $_GET['lesson_id'] ) ) && ( !empty( $_GET['lesson_id'] ) ) )
//				$args['lesson_id'] = intval( $_GET['lesson_id'] );
//
//			if ( ( isset( $request_params['topic_id'] ) ) && ( !empty( $request_params['topic_id'] ) ) )
//				$args['topic_id'] = intval( $request_params['topic_id'] );
//			else if ( ( isset( $_GET['topic_id'] ) ) && ( !empty( $_GET['topic_id'] ) ) )
//				$args['topic_id'] = intval( $_GET['topic_id'] );

			if ( ( !isset( $_GET['order'] ) ) || ( !isset( $_GET['orderby'] ) ) ) {
				if ( ( ( $this->post_type == 'sfwd-lessons' ) || ( $this->post_type == 'sfwd-topic' ) ) && ( !empty( $args['course_id'] ) ) ) {
					$course_lesson_orderby = learndash_get_setting( $args['course_id'], 'course_lesson_orderby' );
					$course_lesson_order   = learndash_get_setting( $args['course_id'], 'course_lesson_order' );
					
					$lessons               = sfwd_lms_get_post_options( 'sfwd-lessons' );
					
					$orderby               = ( empty( $course_lesson_orderby)) ? $lessons['orderby'] : $course_lesson_orderby;
					$order                 = ( empty( $course_lesson_order)) ? $lessons['order'] : $course_lesson_order;
					
					if ( !isset( $_GET['order'] ) ) 
						$args['order'] = $order;
					
					if ( !isset( $_GET['orderby'] ) ) 
						$args['orderby'] = $orderby;
					
				}
			}

			if ( !isset( $_GET['posts_per_page'] ) ) {
				$lessons = sfwd_lms_get_post_options( 'sfwd-lessons' );
				$args['posts_per_page'] = $lessons['posts_per_page'];
			}

			$args['date_query'] = array();
			// Set before into date query. Date query must be specified as an array of an array.
			if ( isset( $request['before'] ) ) {
				$args['date_query'][0]['before'] = $request['before'];
			}

			// Set after into date query. Date query must be specified as an array of an array.
			if ( isset( $request['after'] ) ) {
				$args['date_query'][0]['after'] = $request['after'];
			}

			// Force the post_type argument, since it's not a user input variable.
			$args['post_type'] = $this->post_type;

			/**
			 * Filter the query arguments for a request.
			 *
			 * Enables adding extra arguments or setting defaults for a post
			 * collection request.
			 *
			 * @param array           $args    Key value array of query var to query value.
			 * @param WP_REST_Request $request The request used.
			 */
			$args = apply_filters( "learndash_rest_{$this->post_type}_query", $args, $request );
			error_log('args<pre>'. print_r($args, true) .'</pre>');
			
			$query_args = $this->prepare_items_query( $args, $request );
			
			if ( ( isset($args['course_id'] ) ) && ( intval( $args['course_id'] ) ) ) {
				if ( !isset( $query_args['meta_query'] ) )
					$query_args['meta_query'] = array();
				
				$query_args['meta_query'][] = array(
					'key'     => 'course_id',
					'value'   => intval( $args['course_id'] ),
					'compare' => '=',
				);
			}

			if ( ( isset($args['lesson_id'] ) ) && ( intval( $args['lesson_id'] ) ) ) {
				if ( !isset( $query_args['meta_query'] ) )
					$query_args['meta_query'] = array();
				
				$query_args['meta_query'][] = array(
					'key'     => 'lesson_id',
					'value'   => intval( $args['lesson_id'] ),
					'compare' => '=',
				);
			}

			$posts_query = new WP_Query();
			$query_result = $posts_query->query( $query_args );

			$posts = array();
			foreach ( $query_result as $post ) {
				//if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', $post->ID ) ) {
				//	continue;
				//}

				$data = $this->prepare_item_for_response( $post, $request );
				$posts[] = $this->prepare_response_for_collection( $data );
			}

			$page = (int) $query_args['paged'];
			$total_posts = $posts_query->found_posts;

			if ( $total_posts < 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count
				unset( $query_args['paged'] );
				$count_query = new WP_Query();
				$count_query->query( $query_args );
				$total_posts = $count_query->found_posts;
			}

			$max_pages = ceil( $total_posts / (int) $query_args['posts_per_page'] );

			$response = rest_ensure_response( $posts );
			$response->header( 'X-WP-Total', (int) $total_posts );
			$response->header( 'X-WP-TotalPages', (int) $max_pages );

			$request_params = $request->get_query_params();
			if ( ! empty( $request_params['filter'] ) ) {
				// Normalize the pagination params.
				unset( $request_params['filter']['posts_per_page'] );
				unset( $request_params['filter']['paged'] );
			}
			$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

			if ( $page > 1 ) {
				$prev_page = $page - 1;
				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}
				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}
			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );
				$response->link_header( 'next', $next_link );
			}

			return $response;
		}
		
		public function get_course_items_permissions_check( $request ) {
			global $learndash_post_types;

			error_log('in '. basename(__FILE__) .': '. __FUNCTION__ );

			$course_id = $request['course_id'];
			error_log('course_id['. $course_id .']');
			
			$course = get_post( $request['course_id'] );
			if ( ( !$course ) || ( $course->post_type !== 'sfwd-courses' ) ) {
				return new WP_Error( 'rest_post_invalid_id_1', __( 'Invalid Course ID.' ), array( 'status' => 404 ) );
			}

			if ( in_array( $this->post_type, $learndash_post_types ) === false ) {	
				return new WP_Error( 'rest_post_invalid_id_2', __( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
			}
			error_log('post_type['. $this->post_type .']');

			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) ) {
				return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.', 'learndash' ), array( 'status' => 401 ) );
			}
			error_log('current_user_id['. $current_user_id .']');
			
			$current_user = wp_get_current_user();

			$course_price_type = get_course_meta_setting( $course->ID, 'course_price_type' );
			//if ( $course_price_type == 'closed' ) {
			//	return new WP_Error( 'rest_course_enroll_closed', __( 'Course Enroll Closed.', 'learndash' ), array( 'status' => 403 ) );
			//}
			
			// At this point we have a valid course and valid user
			// So we call a filter to see if anything wants to prevent us from enrolling this user. 
			$has_access = sfwd_lms_has_access( $course->ID, $current_user->ID );
			if ( ( ! $has_access ) && ( $course_price_type != 'open' ) ) {
				return new WP_Error( 'rest_cannot_view', __( 'Sorry, you are not allowed view items.' ), array( 'status' => rest_authorization_required_code() ) );
			}
			
			return true;
		}
		
		
		
		function register_fields() {
			//error_log('in '. __FUNCTION__ );
			
			global $sfwd_lms;
			$post_args_fields = $sfwd_lms->get_post_args_section( $this->post_type, 'fields' );
			//error_log('post_args_fields<pre>'. print_r($post_args_fields, true) .'</pre>');
			
			if ( !empty( $post_args_fields ) ) {
				foreach( $post_args_fields as $field_key => $field_set ) {
					
					if ( ( isset( $field_set['show_in_rest'] ) ) && ( $field_set['show_in_rest'] === true ) ) {	
						//error_log('field_key['. $field_key .']<pre>'. print_r($field_set, true) .'</pre>');
						
						if ( ( isset( $field_set['rest_args'] ) ) && ( is_array( $field_set['rest_args'] ) ) )
							$field_args = $field_set['rest_args'];
						else
							$field_args = array();
						
						if ( ( !isset( $field_args['get_callback'] ) ) || ( empty( $field_args['get_callback'] ) ) ) {
							$field_args['get_callback'] = array( $this, 'ld_get_field_value' );
						}

						if ( ( !isset( $rest_field_args['update_callback'] ) ) || ( empty( $rest_field_args['update_callback'] ) ) ) {
							$field_args['update_callback'] = array( $this, 'ld_update_field_value' );
						}
						
						if ( ( !isset( $field_args['schema'] ) ) || ( empty( $field_args['schema'] ) ) ) {
							$field_args['schema'] = array();
						}
						
						if ( ( !isset( $field_args['schema']['name'] ) ) || ( empty( $field_args['schema']['name'] ) ) ) {
							if ( isset( $field_set['name'] ) )
								$field_args['schema']['description'] 	= $field_set['name'];
						}
						
						if ( ( !isset( $field_args['schema']['type'] ) ) || ( empty( $field_args['schema']['type'] ) ) ) {
							if ( isset( $field_set['type'] ) ) {
								switch( $field_set['type'] ) {
									case 'select':
									case 'multiselect':
										$field_args['schema']['type'] = 'string';
										break;
										
									case 'checkbox':
										$field_args['schema']['type'] = 'boolean';
										break;
									
									default:
										$field_args['schema']['type'] = $field_set['type'];	
										break;
								}
							}
						}
						
						if ( ( !isset( $field_args['schema']['required'] ) ) || ( empty( $field_args['schema']['required'] ) ) ) {
							$field_args['schema']['required'] = false;
						}
						
						if ( ( !isset( $field_args['schema']['sanitize_callback'] ) ) || ( empty( $field_args['schema']['sanitize_callback'] ) ) ) {
							$field_args['schema']['sanitize_callback']     = 'sanitize_key';
						}
						
						if ( ( !isset( $field_args['schema']['validate_callback'] ) ) || ( empty( $field_args['schema']['validate_callback'] ) ) ) {
							$field_args['schema']['validate_callback']     = 'rest_validate_request_arg';
						}
						
						if ( ( !isset( $field_args['schema']['default'] ) ) || ( empty( $field_args['schema']['default'] ) ) ) {	
							if ( isset( $field_set['default'] ) )
								$field_args['schema']['default']			= $field_set['default'];
						}
						
						if ( ( !isset( $field_args['schema']['initial_options'] ) ) || ( empty( $field_args['schema']['initial_options'] ) ) ) {	
							if ( ( isset( $field_set['initial_options'] ) ) && ( !empty( $field_set['initial_options'] ) ) )
								$field_args['schema']['enum'] = $field_set['initial_options'];
							
							//$field_args['context'] = array( 'view', 'edit' );
							
						}

						//error_log('field_key['. $field_key .']<pre>'. print_r($field_args, true) .'</pre>');

						register_rest_field( 
							$this->post_type, 
							$field_key, 
							$field_args
						);
						
					}
				}
			}
		}
		
		function ld_get_field_value( array $postdata, $field_name, WP_REST_Request $request, $post_type ) {
			//error_log('in '. __FUNCTION__ );
			//error_log('postdata<pre>'. print_r($postdata, true) .'</pre>');
			//error_log('field_name<pre>'. print_r($field_name, true) .'</pre>');
			//error_log('request<pre>'. print_r($request, true) .'</pre>');
			//error_log('post_type<pre>'. print_r($post_type, true) .'</pre>');
			
			if ( ( isset( $postdata['id'] ) ) && ( !empty( $postdata['id'] ) ) ) {
				$ld_post = get_post( $postdata['id'] );
				if ( ( is_a( $ld_post, 'WP_Post' ) ) && ( $ld_post->post_type == $this->post_type ) ) {
					return learndash_get_setting( $ld_post, $field_name );
				}
			}
		}

		function ld_update_field_value( $value, WP_Post $post, $field_name, WP_REST_Request $request, $post_type ) {
			return true;
		}
		
	}
}
