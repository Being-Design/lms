<?php
if ( ( !class_exists( 'LD_REST_Posts_Courses_Controller' ) ) && ( class_exists( 'LD_REST_Posts_Controller' ) ) ) {
	class LD_REST_Posts_Courses_Controller extends LD_REST_Posts_Controller {
		
		public function __construct( $post_type = '' ) {
			$this->post_type = 'sfwd-courses';
			
			parent::__construct( $this->post_type );
			$this->namespace = LEARNDASH_REST_API_NAMESPACE .'/'. $this->version;
			$this->rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' );
		}
		
	    public function register_routes() {
			
			$this->register_fields();
			
			//error_log('in '. __FUNCTION__ );
			//error_log('namespace['. $this->namespace .']');
			//error_log('rest_base['. $this->rest_base .']');

			$collection_params = $this->get_collection_params();

			$schema = $this->get_item_schema();
			//error_log('schema<pre>'. print_r($schema, true) .'</pre>');
			
			$get_item_args = array(
				'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
			);
			if ( isset( $schema['properties']['password'] ) ) {
				$get_item_args['password'] = array(
					'description' => __( 'The password for the post if it is password protected.' ),
					'type'        => 'string',
				);
			}
			//error_log('get_item_args<pre>'. print_r($get_item_args, true) .'</pre>');
			

			register_rest_route( 
	  			$this->namespace, 
				'/' . $this->rest_base, 
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_item' ),
						'permission_callback' => array( $this, 'create_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				) 
			);

			register_rest_route( 
				$this->namespace, 
				'/' . $this->rest_base . '/(?P<id>[\d]+)', 
				array(
					'args' => array(
						'id' => array(
							'description' 	=> __( 'Unique identifier for the object.' ),
							'required'		=> true,
							'type'        	=> 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'delete_item_permissions_check' ),
						'args'                => array(
							'force' => array(
								'type'        => 'boolean',
								'default'     => false,
								'description' => __( 'Whether to bypass trash and force deletion.' ),
							),
						),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				) 
			);

			register_rest_route( 
				$this->namespace, 
				'/' . $this->rest_base . '/(?P<id>[\d]+)/enroll', 
				array(
					'args' => array(
						'id' => array(
							'description' => __( 'Course ID to enroll user into.' ),
							'required' => true,
							'type' => 'integer',
						),
					),
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'course_enroll_user' ),
					),
				) 
			);
	    }
		
		function get_items_permissions_check( $request ) {
			if ( ( 'edit' === $request['context'] ) || ( 'view' === $request['context'] ) ) {
				return true;
			}
		}
		
		function course_enroll_user( $request ) {
			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) ) {
				return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.', 'learndash' ), array( 'status' => 401 ) );
			}
			$current_user = wp_get_current_user();

			$course = $this->get_post( $request['id'] );
			if ( is_wp_error( $course ) ) {
				return $course;
			}

//			if ( $course->post_type != $this->post_type ) {
//				return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
//			}
//			
//			$course_price_type = get_course_meta_setting( $course->ID, 'course_price_type' );
//			if ( $course_price_type == 'closed' ) {
//				return new WP_Error( 'rest_course_enroll_closed', __( 'Course Enroll Closed.', 'learndash' ), array( 'status' => 403 ) );
//			}
			
			// At this point we have a valid course and valid user
			// So we call a filter to see if anything wants to prevent us from enrolling this user. 
			$has_access = sfwd_lms_has_access( $course->ID, $current_user->ID );
			if ( ! $has_access ) {
				$user_enrolled = ld_update_course_access( $current_user->ID, $course->ID );
			}

			$data = array( 'ld_course_enrolled_date_gmt' => $this->prepare_date_response( current_time( 'mysql' ) ) );

			// Create the response object
			$response = rest_ensure_response( $data );

			// Add a custom status code
			$response->set_status( 200 );

			return $response;
		}
		// End of functions
	}
}
